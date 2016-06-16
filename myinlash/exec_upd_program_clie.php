<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/programacion_dml.php");
include ($path."/lib/servicios_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {	
	if ( validar_permisos ($conn, 'exec_upd_program_clie.php') ) {
		$v_id_usuario = $_SESSION['id_usuario'];
		$v_id_servicio = $_POST['p_id_servicio'];
		$v_fecha = $_POST['p_fecha'];
		$v_reserva = explode('|', $_POST['p_reserva']);
		$v_maquina = $v_reserva[0];
		$v_hora_ini = DateTime::createFromFormat('d-m-Y H:i', $v_fecha.' '.$v_reserva[1]);
		$r_servicio = detalle_servicio ($conn, $v_id_servicio);
		$v_hora_fin = clone $v_hora_ini; 
		$v_hora_fin->add(new DateInterval('PT'.$r_servicio['sesion_minima'].'M'));
		
		$v_login_mod = $_SESSION['login'];
		
		$t_result = crea_programacion ($conn,      $v_id_usuario,              $v_id_servicio,
									   $v_fecha,   $v_hora_ini->format('H:i'), $v_hora_fin->format('H:i'),
									   $v_maquina, null,                       $v_login_mod,  
									   null,       null,                       null);
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
    if($t_result[0]) {
  		mensaje(1, 'La sesi&oacute;n fue programada correctamente', 'javascript:top.refrescar();', '_self'); 
	} else {
		mensaje(2, $t_result[1], 'javascript:top.GB_hide();', '_self'); 
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