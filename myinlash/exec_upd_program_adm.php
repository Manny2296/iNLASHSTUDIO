<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/notificaciones_utl.php");
include ($path."/lib/programacion_utl.php");
include ($path."/lib/programacion_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'exec_upd_program_adm.php') ) {
		$v_id_usuario = $_POST['p_id_usuario'];
		$v_id_servicio = $_POST['p_id_servicio'];
		$v_fecha = $_POST['p_fecha'];
		$v_hora = $_POST['p_hora'];
		$v_maquina = $_POST['p_maquina'];
		$v_hora_fin = $_POST['p_hora_fin'];
		$v_login_mod = $_SESSION['login'];
		$v_id_sede = $_POST['p_id_sede'];
		if (isset($_POST['p_cortesia']) && $_POST['p_cortesia'] == "S"){
			$v_cortesia = "S";
		} else {
			$v_cortesia = "N";
		}
		$v_comentarios = $_POST['p_comentarios'];
		$t_maquinas = null;
		if (isset($_POST['p_maquinas']) && is_array($_POST['p_maquinas'])) {
			$t_maquinas = $_POST['p_maquinas'];
		}

		$hoy = new DateTime();
		$hoy->setTime(0,0);
		$v_manana = clone $hoy;
		$v_manana->add(new DateInterval('P1D'));
		$v_fecha_cita = DateTime::createFromFormat('d-m-Y', $v_fecha);

		if (isset($_POST['p_sesion_especial']) && $_POST['p_sesion_especial'] == "S") {
			$v_sesion_especial = "S";
		} else {
			$v_sesion_especial = "N";
		}
		$v_mantenimientos = req_mantenimiento_usua($conn, $v_id_servicio, $v_id_usuario);
		if ($v_mantenimientos == 'S' && $v_sesion_especial == "S") {
			$v_fecha_mant = get_ult_mantenimiento($conn, $v_id_usuario);
			completar_mantenimientos($conn, $v_id_usuario, $v_fecha_mant);
		}
		$t_result = crea_programacion ($conn,       $v_id_usuario,      $v_id_servicio,
							   		   $v_fecha,    $v_hora,		    $v_hora_fin,
									   $v_maquina,  $v_sesion_especial, $v_login_mod,
									   $v_cortesia, $v_comentarios,     $t_maquinas, $v_id_sede);

		$v_diff = $hoy->diff($v_fecha_cita);
		if ($t_result[0] && ($v_diff->format('%a') == 0 || $v_diff->format('%R%a') == 1 )){
			$v_subject = "iNlash & Co - Cita Programada";
			if ($v_diff->format('%a') == 0) {
				$v_msg = "<p>iNlash & Co te recuerda la cita que tienes programada para el d&iacute;a de hoy: </p>";
			} else {
				$v_msg = "<p>iNlash & Co te recuerda la cita que tienes programada para el d&iacute;a de ma&ntilde;ana: </p>";
			}
			$r_cita = detalle_programacion($conn, $t_result[1]);
			$v_hora_ini = DateTime::createFromFormat('d-m-Y H:i', $r_cita['fecha'].' '.$r_cita['hora_ini']);
			$v_hora_fin = DateTime::createFromFormat('d-m-Y H:i', $r_cita['fecha'].' '.$r_cita['hora_fin']);
			$v_msg .= "<p>En caso de no poder asistir por favor cancelar su cita con 3 horas de anticiación</p>";
			$v_msg .= '<p><table border="0"><tr><td>Servicio:</td><td>'.$r_cita['nombre'].'</td></tr>';
			$v_msg .= '<tr><td>Hora del servicio:</td><td>'.$v_hora_ini->format('h:i a').'</td></tr>';
			$v_msg .= '<tr><td>Hora de finalizaci&oacute;n:</td><td>'.$v_hora_fin->format('h:i a').'</td></tr></table></p>';
			$v_msg .= "<p>Te esperamos!</p>";
			$v_msg .= ' <p>Horario de atenci�n: lunes a sábado de 7:00 a.m. a 7:00 p.m.&nbsp;&nbsp;-&nbsp;&nbsp;S�bado de 8:00am a 2:00p.m.</p>';
			$v_msg .= '<br> iNlash & Co';
			$v_msg.= '<br>Sede Principal Cr 17A # 122 - 45, Bogotá D.C.
									 <br>Sede Contador Cll 136 # 19 - 47, Bogotá D.C.
									 <br>Sede Santa Ana Cr 11D # 118A - 95, Bogotá D.C.
									 <br>Tels: 4785349 - 313 400 7364 - 3004553566<br>';
			$v_msg .= '<br><a href="www.inlash.com.co">www.inlash.com.co</a>';
			$v_msg .= '<br>email: <a href="mailto:contacto@inlash.com.co">contacto@inlash.com.co</a></span></p>';
			$v_msg.= ' S&iacute;ganos a trav&eacute;s de <br> <a href="https://www.facebook.com/InLash-Extensiones-de-Pesta%C3%B1as-977194908981840/?ref=ts&fref=ts">Facebook: InLash-Extensiones-de-Pestañas</a>, ';
			$v_msg .= '<br><a href="http://instagram.com/inlashpestanas">Instagram: inlashpestanas</a> ';

			notificar_email_usuario ($conn, $v_id_usuario, $v_subject, $v_msg, 'N');
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
