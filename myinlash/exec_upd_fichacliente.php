<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/antropometria_utl.php");
include ($path."/lib/antropometria_dml.php");
include ($path."/lib/programacion_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'exec_upd_fichacliente.php') ) {
		$v_id_usuario = $_POST['p_id_usuario'];
		$v_fecha = $_POST['p_fecha'];
		$v_genero = get_genero($conn, $v_id_usuario);
		$v_login_mod = $_SESSION['login'];
		
		if (isset($_POST['p_id_medida'])) {
			$t_medidas = $_POST['p_id_medida'];
			$t_valores = $_POST['p_valor'];
			$v_pos = 0;
			foreach($t_medidas as $dato) {
				upd_ficha_antrop($conn, $v_id_usuario, $dato, $v_fecha, $t_valores[$v_pos], null);
				$v_pos++;
			}
		}
		//actualizar calculables
		if (isset($_POST['p_calculable'])){
			$t_calculable = $_POST['p_calculable'];
			foreach($t_calculable as $dato) {
				$r_medida = medida_detalle($conn, $dato);
				$v_formula = $r_medida['formula'];
				$v_valor = calcula_medida ($conn, $v_formula, $v_id_usuario, $v_fecha);
				upd_ficha_antrop($conn, $v_id_usuario, $dato, $v_fecha, $v_valor, null);
			}
		}
		//agrega comentarios
		if(isset($_POST['p_observacion'])){
			$v_observacion = $_POST['p_observacion'];
			crea_observacion ($conn, $v_id_usuario, $v_fecha, $v_observacion, $v_login_mod);
		}
		//actualiza asistencia del usuario a la sesion
		if (isset($_POST['p_id_programacion'])){
			$v_id_programacion = $_POST['p_id_programacion'];
			upd_estado_prog ($conn, $v_id_programacion, 'S');
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Actualizaci&oacute;n de ficha del usuario</title>
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
    mensaje(1, 'La ficha antropom&eacute;trica del cliente fue actualizada correctamente', 'cliente_ficha_frm.php?p_id_usuario='.$v_id_usuario, '_self'); 
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