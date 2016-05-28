<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/antropometria_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {	
	if ( validar_permisos ($conn, 'exec_upd_pestanas.php') ) {
		$v_id_usuario = $_POST['p_id_usuario'];
		$v_id_pestana_1 = $_POST['p_id_pestana_1'];
		$v_id_pestana_2 = $_POST['p_id_pestana_2'];
		$v_id_pestana_3 = $_POST['p_id_pestana_3'];
		$v_fecha_postura = $_POST['p_fecha_postura'];
		
		$v_result = upd_pestanas ($conn, $v_id_usuario, $v_id_pestana_1, 
					              $v_id_pestana_2, $v_id_pestana_3, $v_fecha_postura);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.:: iNLASH & Co - Actualizaci&oacute;n de servicio de pesta&ntilde;as ::.</title>
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
    	mensaje(1, 'La informaci&oacute;n del tratamiento de pesta&ntilde;as fue actualizada', 'javascript:top.GB_hide();', '_self'); 
	} else {
	    mensaje(2, 'Existe otro tratamiento para el cliente con la misma fecha de postura', 'javascript:top.GB_hide();', '_self'); 
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