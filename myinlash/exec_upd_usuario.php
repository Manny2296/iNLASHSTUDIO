<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/usuarios_dml.php");
include ($path."/lib/notificaciones_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	
	if ( validar_permisos ($conn, 'exec_upd_usuario.php') ) {
		$v_id_tipoid = $_POST['p_id_tipoid'];
		$v_numero_id = $_POST['p_numero_id'];
		$v_nombres = $_POST['p_nombres'];
		$v_apellidos = $_POST['p_apellidos'];
		$v_id_perfil = $_POST['p_id_perfil'];
		$v_telefono = $_POST['p_telefono'];
		$v_celular = $_POST['p_celular'];
		if($_POST['p_email'] == "" || is_null($_POST['p_email'])){
			$v_email = null;
		} else {
			$v_email = $_POST['p_email'];
		}
		$v_genero = $_POST['p_genero'];
		$v_fecha_nacimiento = $_POST['p_fecha_nacimiento'];
		$v_fecha_ingreso = $_POST['p_fecha_ingreso'];
		$v_id_eps = $_POST['p_id_eps'];
		$v_eps = $_POST['p_eps'];
		$v_id_prepagada = $_POST['p_id_prepagada'];
		$v_prepagada = $_POST['p_prepagada'];
		$v_descripcion = $_POST['p_descripcion'];
		$v_login_mod = $_SESSION['login'];
		$v_existe = $_POST['p_existe'];
		$v_id_usuario = null;
		$id_sede=$_POST['p_id_sedes_reg'];
		$multi_sede =$_POST['p_multi_sede'];
		if(isset($_POST['p_notificar'])) {
			$v_notificar = $_POST['p_notificar'];
		} else {
			$v_notificar = null;
		}
		
		if ($v_existe == "N" && (!isset($_POST['p_id_usuario']) || $_POST['p_id_usuario'] == "" )) {
			$v_id_usuario = crea_usuario ($conn,               $v_id_tipoid,    $v_numero_id,        $v_nombres,
										  $v_apellidos,        $v_telefono,     $v_celular,          $v_email,         
										  $v_genero,		   $v_id_eps,		$v_eps,              $v_id_prepagada, 
										  $v_prepagada,        $v_descripcion,	$v_fecha_nacimiento, $v_fecha_ingreso
										  , $multi_sede);
			$v_login = obtener_login($conn, $v_id_usuario);
			$v_titulo = "Bienvenido(a) a iNlash & Co";
			$v_mensaje = "<p>".$v_nombres.": <p>IiNLAsh & CO te da la bienvenida  a nuestra sede. Por este medio te mantendremos informada de servicios agendados y noticias importantes sobre los mismos.

				Recibe un cordial saludo,";
			$v_mensaje .= "<p>iNlash & Co
							Sede Principal Cr 17A # 122 - 45, Bogotá D.C.
							Sede Contador Cll 136 # 19 - 47, Bogotá D.C.
							Sede Santa Ana Cr 11D # 118A - 95, Bogotá D.C.
							Tels: 4785349 - 313 400 7364 - 3004553566

							www.inlash.com.co email: contacto@inlash.com.co
							Instagram: inlashpestanas
							Facebook: InLash-Extensiones-de-Pestañas";
			//$v_mensaje .= "<p>Tus credenciales de acceso son:<p>Login: ".$v_login."<br>Contrase&ntilde;a: ".$v_numero_id;
				
			notificar_email_usuario ($conn, $v_id_usuario, $v_titulo, $v_mensaje, "N");
		} elseif ($v_existe != "U" && isset($_POST['p_id_usuario']) && $_POST['p_id_usuario'] != "" ) {
			$v_id_usuario = $_POST['p_id_usuario'];
			upd_usuario ($conn,               $v_id_usuario,    $v_id_tipoid,        $v_numero_id,    $v_nombres,
					     $v_apellidos,        $v_telefono,      $v_celular,          $v_email,         
					     $v_genero,		      $v_id_eps,		$v_eps,              $v_id_prepagada, 
						 $v_prepagada,        $v_descripcion,	$v_fecha_nacimiento, $v_fecha_ingreso, 
						 $v_notificar, $multi_sede);
			crea_perfil ($conn, $v_id_usuario, $v_id_perfil, $v_login_mod,$id_sede);
		} 
		
		if ($v_existe == "N" || $v_existe == "U") {
			if (isset($_POST['p_id_usuario']) && is_null($v_id_usuario)) {
				$v_id_usuario = $_POST['p_id_usuario'];
			} 
			crea_perfil ($conn, $v_id_usuario, $v_id_perfil, $v_login_mod,$id_sede);
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Actualizaci&oacute;n de cuentas de usuario</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <?php 
    if(isset($_POST['p_id_usuario'])) {
  		mensaje(1, 'El usuario fue actualizado correctamente', 'javascript:top.refrescar(); top.GB_hide();', '_self'); 
	} else {
		mensaje(1, 'El usuario fue creado correctamente', 'javascript:top.refrescar(); top.GB_hide();', '_self'); 
	}
	?>
  <!-- InstanceEndEditable -->
  </div>
</body>
<!-- InstanceEnd --></html>
<?php	
	}
	else {
		mensaje(2, 'Usted no tiene permisos para acceder esta opci&oacute;n', 'javascript:history.go(-1);', '_self');
	}      
} else {
	mensaje(2, 'Su sesi&oacute;n no est&aacute; activa.<br>Por favor ingrese al sistema nuevamente', $url_login, '_parent');
}
dbdisconn ($conn);
?>