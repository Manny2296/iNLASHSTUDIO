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
	if ( validar_permisos ($conn, 'exec_upd_servcliente.php') ) {
		$v_id_usuario = $_POST['p_id_usuario'];
		$v_id_servicio = $_POST['p_id_servicio'];
		$v_cantidad = $_POST['p_cantidad'];
		$v_continuidad = $_POST['p_continuidad'];
		$v_fecha = $_POST['p_fecha'];
		$v_caducidad = $_POST['p_caducidad'];
		if (isset($_POST['p_congelar'])) {
			$v_congelar = $_POST['p_congelar'];
		} else {
			$v_congelar = 'N';
		}
		
		$v_result = upd_servicio_cliente ($conn, $v_id_servicio, $v_id_usuario, $v_fecha, $v_cantidad, $v_continuidad, $v_caducidad, $v_congelar);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.:: MY INLIFE STUDIO - Actualizaci&oacute;n de servicios de clientes ::.</title>
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
  		mensaje_form(1, 'El servicio fue asignado al usuario', 'cliente_servicios_lst.php', 'p_id_usuario', $v_id_usuario); 
	} else {
		mensaje_form(2, 'El servicio no pudo ser asignado al usuario', 'cliente_servicios_lst.php', 'p_id_usuario', $v_id_usuario); 
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