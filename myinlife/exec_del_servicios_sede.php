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
	if ( validar_permisos ($conn, 'exec_del_servicios.php') ) {
		$v_id_servicio = $_POST['p_id_servicio'];
		$v_id_sede =  $_POST['p_id_sede'];
		$v_result = del_servicio_sede ($conn, $v_id_servicio, $v_id_sede);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/main_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.:: MY INLIFE STUDIO - Eliminaci&oacute;n de servicios ::.</title>
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
    if($v_result) {
  		mensaje_form(1, 'el servicio de la sede fue eliminado correctamente', 'sede_servicios_lst.php', 'p_id_sede', $v_id_sede);  
	} else {
		
		mensaje_form(2, 'El servicio no pudo ser eliminado pues alg&uacute;n usuario lo tiene asignado', 'sede_servicios_lst.php', 'p_id_sede', $v_id_sede); 
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