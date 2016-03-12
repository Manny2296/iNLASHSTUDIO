<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/servicios_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	
	if ( validar_permisos ($conn, 'exec_upd_servicios.php') ) {
		$v_nombre = $_POST['p_nombre'];
		$v_descripcion = $_POST['p_descripcion'];
		$v_precio_base = $_POST['p_precio_base'];
		$v_impuesto = $_POST['p_impuesto'];
		$v_prepagado = $_POST['p_prepagado'];
		$v_programable = $_POST['p_programable'];
		$v_ficha_antrop = $_POST['p_ficha_antrop'];
		$v_sesion_minima = $_POST['p_sesion_minima'];
		$v_sesiones_simultaneas = $_POST['p_sesiones_simultaneas'];
		$v_pestanas = $_POST['p_pestanas'];
		$v_dias_venc = $_POST['p_dias_venc'];
		$v_dias_mant = $_POST['p_dias_mant'];
		
		if (isset($_POST['p_id_servicio'])) {
			$v_id_servicio = $_POST['p_id_servicio'];
			upd_servicio ($conn,           $v_id_servicio,   $v_nombre,    $v_descripcion, 
					      $v_precio_base,  $v_impuesto,      $v_prepagado, $v_programable,
					      $v_ficha_antrop, $v_sesion_minima, $v_sesiones_simultaneas,
						  $v_pestanas,	   $v_dias_venc,     $v_dias_mant);
		} else {
			crea_servicio ($conn,              $v_nombre,       $v_descripcion, 
						   $v_precio_base, 	   $v_impuesto,     $v_prepagado,
						   $v_programable, 	   $v_ficha_antrop, $v_sesion_minima,
						   $v_sesiones_simultaneas, $v_pestanas, $v_dias_venc,
						   $v_dias_mant);
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Actualizaci&oacute;n de servicios</title>
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
    if(isset($_POST['p_id_servicio'])) {
  		mensaje(1, 'El servicio fue actualizado correctamente', 'javascript:top.refrescar(); top.GB_hide();', '_self'); 
	} else {
		mensaje(1, 'El servicio fue creado correctamente', 'javascript:top.refrescar(); top.GB_hide();', '_self'); 
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