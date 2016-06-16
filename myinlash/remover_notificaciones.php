<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/notificaciones_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);
$v_email = $_REQUEST['email'];
$v_result = desactivar_notificaciones($conn, $v_email);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: iNLASH & Co - Bloqueo de Notificaciones ::.</title>
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
</head>

<body>
     <div id="contenido">
	 <?php 
    if($v_result) {
  		mensaje(1, '<p>No se enviar&aacute;n mensajes adicionales de notificaci&oacute;n para la cuenta de usuario asociada a la direcci&oacute;n de correo electr&oacute;nico <span class="negrita">'.$v_email.'</span>.</p><p>Si desea restablecer este servicio comun&iacute;quese con Inlife Studio.</p>', $site_domain, '_self'); 
	} else {
		mensaje(2, 'No fue posible modificar sus preferencias de mensajes de notificaci&oacute;n por correo electr&oacute;nico.<p>Por favor comun&iacute;quese con Inlife Studio.</p>', $site_domain, '_self'); 
	}
	?>
     </div>
  </div>
</body>
</html>
<?php	
dbdisconn ($conn);
?>