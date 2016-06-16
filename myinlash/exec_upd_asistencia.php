<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/programacion_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {	
	if ( validar_permisos ($conn, 'exec_upd_asistencia.php') ) {
		$v_id_programacion = $_POST['p_id_programacion'];
		$v_es_mantenimiento = $_POST['p_es_mantenimiento'];
		$v_id_usuario = $_POST['p_id_usuario'];
		$v_fecha = $_POST['p_fecha'];
		
		if(isset($_POST['p_asistencia']) && $_POST['p_asistencia'] == 'N'){
			$v_asistencia = 'N';
		} else {
			$v_asistencia = 'S';
		}
		upd_estado_prog ($conn, $v_id_programacion, $v_asistencia);
		if ($v_es_mantenimiento == "S") {
			if(isset($_POST['p_completar_mant']) && $_POST['p_completar_mant'] == "S") {
				completar_mantenimientos($conn, $v_id_usuario, $v_fecha);
			} else {
				agregar_mantenimiento ($conn, $v_id_usuario, $v_fecha);
			}
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.:: iNLASH & Co - Programaci&oacute;n de sesiones ::.</title>
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
    mensaje(1, 'La sesi&oacute;n fue actualizada', 'javascript:top.refrescar();', '_self'); 
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