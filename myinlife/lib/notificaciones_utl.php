<?php
/* 
 Libreria de utilidades para el envío de notificaciones por email y SMS
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca01@gmail.com
 Fecha     : 10/12/2010 11:40 a.m.
 Version   : 1.0
*/
function almacenar_resultado($connid, $id_usuario, $tipo, $resultado){
	$query = "Select email
	            From segu_usuarios
			   Where id_usuario = ".$id_usuario;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_email = $rset[0]['email'];
	$query = "Insert Into noti_envio_mensajes (id_usuario, email, tipo, resultado, fecha)
	            Values (".$id_usuario.", '".$v_email."', '".$tipo."', '".$resultado."', now())";
	$result = dbquery ($query, $connid);
    return(true);
}
function eliminar_resultados($connid) {
	$query = " Delete From noti_envio_mensajes
	            Where fecha <= Curdate() - 8";
	$result = dbquery ($query, $connid);
    return(true);
}
function lista_resultados($connid, $id_usuario) {
	$query = "Select ntms.email, ntms.tipo, ntms.resultado, date_format(ntms.fecha, '%d-%m-%Y %h:%i %p') fecha
	            From noti_envio_mensajes ntms
			   Where id_usuario = ".$id_usuario."
			   Order By ntms.fecha Desc";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return($rset);
}
function enviar_emails($connid, $tipo, $subject, $message, $t_sendto) {
	//La presente función requiere la inclusión de la libreria de SwiftEmailer
	// variable globales
	global $transport_method;
	global $email_from;
	global $email_name;
	global $smtp_server;
	global $smtp_port;
	global $sendmail_path;
	global $msg_min_limit;
	global $smtp_encrypt;
	global $email_pwd;
	// validación de email de salida y return-path
	if (is_null($email_from) || $email_from == "" || !filter_var($email_from, FILTER_VALIDATE_EMAIL) || 
		!strpos($email_from, "@") ){
		return(false);
	}
	if (function_exists('mb_internal_encoding') && ((int) ini_get('mbstring.func_overload')) & 2)
	{
	  $mbEncoding = mb_internal_encoding();
	  mb_internal_encoding('ASCII');
	}
	//imlementación de SwiftEmailer
	if (is_array($t_sendto)) {
		$o_message = Swift_Message::newInstance();
		$o_message->setCharset('iso-8859-1');
		$o_message->setReturnPath($email_from);
		$o_message->setSubject($subject);
		$o_message->setFrom(array($email_from => $email_name));
		$o_message->setMaxLineLength(78);
		$o_message->setPriority(2);
		//$o_message->getHeaders()->addTextHeader('Precedence', 'bulk');
		//establecer transporte
		if($transport_method == "SMTP") {
			$o_transport = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port);
			if (!is_null($smtp_encrypt)) {
				$o_transport -> setEncryption($smtp_encrypt);
			}
			if (!is_null($email_pwd)){
				$o_transport -> setUsername($email_from);
				$o_transport -> setPassword($email_pwd);
			}
		} elseif ($transport_method == "SENDMAIL") {
			$o_transport = Swift_SendmailTransport::newInstance($sendmail_path);
		} else {
			$o_transport = Swift_MailTransport::newInstance();	
		}
		$o_mailer = Swift_Mailer::newInstance($o_transport);
		$o_mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(100, 30));
		$o_mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin($msg_min_limit, Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE));
		//armar el cuerpo del mensaje usando combinación de correspondencia
		foreach($t_sendto as $dato) {
			if (!is_null($dato['emailto']) && $dato['emailto'] != "" && filter_var($dato['emailto'], FILTER_VALIDATE_EMAIL) &&
				strpos($dato['emailto'], "@") > 0 ){
	
				$v_emailto = array($dato['emailto'] => $dato['nameto']);
				
				$arrkeys = array_keys($dato);
				$v_message = $message;
				foreach($arrkeys as $llave) {
					$v_message = str_replace("|".$llave."|", $dato[$llave], $v_message);
				}
				$o_message->setBody($v_message, 'text/html');
				$o_message->setTo($v_emailto);
				if($o_mailer->send($o_message)) {
					almacenar_resultado($connid, $dato['id_usuario'], $tipo, 'OK');
				} else {
					almacenar_resultado($connid, $dato['id_usuario'], $tipo, 'ERR');
				}
			} else {
				almacenar_resultado($connid, $dato['id_usuario'], $tipo, 'ERR');
			}
		}
	}
	//
	if (isset($mbEncoding))
	{
	  mb_internal_encoding($mbEncoding);
	}
	return(true);
}
function notificar_email_usuario ($connid, $id_usuario_to, $titulo, $mensaje, $copia){
	global $email_from;
	global $email_name;
	if (is_null($email_from) || $email_from == "" || !filter_var($email_from, FILTER_VALIDATE_EMAIL) || 
		!strpos($email_from, "@") ){
		return(false);
	}
	$email_from = $email_name.'<'.$email_from.'>';
	$query = "Select usua.email, Concat(usua.nombres, ' ', usua.apellidos) nombre
	            from segu_usuarios usua
			   Where usua.id_usuario = ".$id_usuario_to;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (is_null($rset[0]['email']) || $rset[0]['email'] == "" || !filter_var($rset[0]['email'], FILTER_VALIDATE_EMAIL) || 
		!strpos($rset[0]['email'], "@") ){
		return(false);
	}
	$v_to = $rset[0]['nombre'].'<'.$rset[0]['email'].'>';
	$v_mail_to = $rset[0]['email'];
	$mensaje = str_replace("|emailto|", $v_mail_to, $mensaje);
	$mensaje = wordwrap($mensaje, 70);
	
	//Encabezados del mail
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	if (isset($copia) && $copia == 'S') {
		$headers .= 'Cc: '.$email_from. "\r\n";
	}	
	$headers .= 'From: '.$email_from. "\r\n";
	
	return(mail($v_mail_to, $titulo, $mensaje, $headers));
}
function notificar_email_perf ($connid, $id_perf_unico_to, $titulo, $mensaje, $copia){
	global $email_from;
	global $email_name;
	if (is_null($email_from) || $email_from == "" || !filter_var($email_from, FILTER_VALIDATE_EMAIL) || 
		!strpos($email_from, "@") ){
		return(false);
	}
	$email_from = $email_name.'<'.$email_from.'>';
	$query = "Select usua.email, Concat(usua.nombres, ' ', usua.apellidos) nombre
	            from segu_usuarios usua,
					 segu_perfil_x_usuario pfus
			   Where pfus.id_usuario    = usua.id_usuario
			     And pfus.id_perf_unico = ".$id_perf_unico_to;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (is_null($rset[0]['email']) || $rset[0]['email'] == "" || !filter_var($rset[0]['email'], FILTER_VALIDATE_EMAIL) || 
		!strpos($rset[0]['email'], "@") ){
		return(false);
	}
	$v_to = $rset[0]['nombre'].'<'.$rset[0]['email'].'>';
	$v_mail_to = $rset[0]['email'];
	$mensaje = str_replace("|emailto|", $v_mail_to, $mensaje);
	$mensaje = wordwrap($mensaje, 70);
	
	//Encabezados del mail
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	if (isset($copia) && $copia == 'S') {
		$headers .= 'Cc: '.$email_from. "\r\n";
	}
	$headers .= 'From: '.$email_from. "\r\n";
	
	return(mail($v_mail_to, $titulo, $mensaje, $headers));
}
function firma(){
	$v_firma = '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:14px;">Si desea dejar de recibir mensajes de notificación por parte de Inlife Studio haga clic <a href="http://www.inlifestudio.com/myinlife/remover_notificaciones.php?email=|emailto|">Aquí</a> o copie el siguiente vínculo en su navegador de Internet favorito http://www.inlifestudio.com/myinlife/remover_notificaciones.php?email=|emailto|</p>';	
	$v_firma = '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:14px;">Horario de atención: lunes a viernes de 6:00 a.m. a 8:30 p.m.&nbsp;&nbsp;-&nbsp;&nbsp;Sábado de 8:00am a 2:00p.m.</p>';
	$v_firma .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:14px;">In Life Studio<br>Carrera 17A # 122-45, Bogotá';
	$v_firma .= '<br>Tels: 4785349 - 3004553566<br><a href="www.inlifestudio.com">www.inlifestudio.com</a><br>';
	$v_firma .= 'email: <a href="mailto:contacto@inlifestudio.com">contacto@inlifestudio.com</a></span></p>';
	$v_firma .= 'S&iacute;ganos a trav&eacute;s de <a href="https://www.facebook.com/PowerPlateBog">Facebook</a>, ';
	$v_firma .= '<a href="http://instagram.com/inlifestudio">Instagram</a> y ';
	$v_firma .= '<a href="https://twitter.com/inlifestudio">Twitter</a>';
	return($v_firma);
}
function notificar_citas($connid) {
	$query = "Select usua.nombres, usua.apellidos, usua.id_usuario,
	                 prog.hora_ini, prog.hora_fin, date_format(prog.fecha, '%d-%m-%Y') fecha,
					 serv.nombre, usua.email
			    From segu_usuarios usua,
				     spa_programacion prog,
					 conf_servicios serv
			   Where prog.id_usuario  = usua.id_usuario
			     And prog.id_servicio = serv.id_servicio
			     And prog.fecha       = Curdate()+1
				 And usua.notificar   = 'S'
			   Order By usua.id_usuario, prog.hora_ini";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_titulo = "Tus citas en Inlife";
	//mensaje
	$v_msg = '<h3>Apreciado(a) |nameto|:</h3>';
	$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">Inlife Studio le recuerda las siguientes citas programadas para mañana:</p>';
	$v_msg .= '|tabla| <p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">En caso de que sesión sea de Power Plate y no pueda asistir por favor cancélela con <b>6 horas de anticipación</b> para que su clase no sea descontada.</span></p>';
	$v_msg .= firma();
	//
	$v_old_id = null;
	$v_pos = 0;
	if (is_array($rset)) {
		foreach($rset as $dato) {
			if (!is_null($v_old_id) && $v_old_id != $dato['id_usuario']) {
				$v_pos++;
			}
			if (is_null($v_old_id) || $v_old_id != $dato['id_usuario']) {
				$t_emailto[$v_pos]['emailto'] = $dato['email'];
				$t_emailto[$v_pos]['nameto'] = $dato['nombres']." ".$dato['apellidos'];
				$t_emailto[$v_pos]['id_usuario'] = $dato['id_usuario'];
				$t_emailto[$v_pos]['tabla'] = null;
			}
			$v_hora_ini = DateTime::createFromFormat('d-m-Y H:i', $dato['fecha'].' '.$dato['hora_ini']);
			$v_hora_fin = DateTime::createFromFormat('d-m-Y H:i', $dato['fecha'].' '.$dato['hora_fin']);
			$t_emailto[$v_pos]['tabla'] .= '<p><table border="0"><tr><td>Servicio:</td><td>'.$dato['nombre'].'</td></tr>';
			$t_emailto[$v_pos]['tabla'] .= '<tr><td>Hora del servicio:</td><td>'.$v_hora_ini->format('h:i a').'</td></tr>';
			$t_emailto[$v_pos]['tabla'] .= '<tr><td>Hora de finalizaci&oacute;n:</td><td>'.$v_hora_fin->format('h:i a');
			$t_emailto[$v_pos]['tabla'] .= '</td></tr></table></p>';
			$v_old_id = $dato['id_usuario'];
		}
		enviar_emails($connid, 'Recordatorio de Cita', $v_titulo, $v_msg, $t_emailto);
	}
	return(true);
}
function notificar_vencimiento($connid) {
	$query = "Select usua.nombres, usua.apellidos, usua.id_usuario,
	                 serv.nombre, usua.genero,date_format(svus.caducidad, '%d-%m-%Y') fecha
				From segu_usuarios usua,
				     conf_servicios serv,
					 spa_servicios_x_usuario svus
			   Where svus.id_usuario       = usua.id_usuario
			     And svus.id_servicio      = serv.id_servicio
				 And svus.congelar         = 'N'
				 And sesiones_disp(svus.id_servicio, svus.id_usuario, svus.fecha) > 0
				 And svus.caducidad   = Curdate() + serv.dias_vencimiento
				 And usua.notificar   = 'S'
				Order By usua.apellidos, usua.id_usuario"; 
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_titulo = "Vencimiento de servicios Inlife";
	$v_old_id = null;
	$v_pos = 0;
	//mensaje
	$v_msg = '<h3>Apreciado(a) |nameto|:</h3>';
	$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">Teniendo en cuenta el tiempo estipulado de sus paquetes más los 15 días de gracia, los siguientes servicios están próximos a vencer:</span></p>';
	$v_msg .= '|tabla| <p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">Comuníquese con nosotros para contarle cual es el estado de sus paquetes y de esta forma continúe disfrutando de los beneficios que In life Studio le ofrece.</span></p>';
	$v_msg .= firma();
	//
	if (is_array($rset)) {
		foreach($rset as $dato) {
			if (!is_null($v_old_id) && $v_old_id != $dato['id_usuario']) {
				$v_pos++;
			}
			if (is_null($v_old_id) || $v_old_id != $dato['id_usuario']) {
				$t_emailto[$v_pos]['emailto'] = $dato['email'];
				$t_emailto[$v_pos]['nameto'] = $dato['nombres']." ".$dato['apellidos'];
				$t_emailto[$v_pos]['id_usuario'] = $dato['id_usuario'];
				$t_emailto[$v_pos]['tabla'] = null;
			}
			$t_emailto[$v_pos]['tabla'] .= '<p><table border="0"><tr><td>Servicio a vencer:</td><td>'.$dato['nombre'].'</td></tr>';
			$t_emailto[$v_pos]['tabla'] .= '<tr><td>Fecha de vencimiento:</td><td>'.$dato['fecha'].'</td></tr></table></p>';
			$v_old_id = $dato['id_usuario'];
		}
		enviar_emails($connid, 'Vencimiento', $v_titulo, $v_msg, $t_emailto);
	}
	return(true);
}
function notificar_descongelar($connid) {
	$query = "Select valor
	            From conf_parametros
			   Where codigo = 'TMCG'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_periodo = $rset[0]['valor'];
	$v_tiempo = $v_periodo;
	$v_periodo = $v_periodo - 2;
	if($v_periodo < 0) {
		$v_periodo = 0;
	}
	$query = "Select usua.nombres, usua.apellidos, 
					 usua.id_usuario, serv.nombre nomservicio, 
					 usua.email, date_format(svus.fecha_cambio, '%d-%m-%Y') fecha_cong
				From segu_usuarios usua,
				     spa_servicios_x_usuario svus,
					 conf_servicios          serv
			   Where usua.id_usuario   = svus.id_usuario
			     And serv.id_servicio  = svus.id_servicio
				 And svus.congelar     = 'S'
				 And svus.fecha_cambio = curdate() - ".$v_periodo."
				 And usua.notificar    = 'S'
			   Order By usua.id_usuario";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_titulo = "Descongelamiento de servicios Inlife";
	$v_pos = 0;
	//mensaje
	$v_msg = '<h3>Apreciado(a) |nameto|:</h3>';
	$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">De acuerdo con las políticas de In Life Studio los paquetes se podrán congelar por un tiempo no mayor de 3 meses y una vez por paquete; teniendo en cuenta que la congelación de su paquete se realizó el |fecha|';
	$v_msg .= ", deberá reactivar su paquete en los próximos dos(2) días hábiles que será la fecha límite de congelación.</span></p>";
	$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">Gracias por la atención prestada y esperamos una pronta respuesta.</span></p>';
	$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">¡Reactive nuevamente sus sesiones y disfrute de los beneficios de entrenar en In Life Studio con Power Plate!</span></p>';
	$v_msg .= firma();
	//
	if (is_array($rset)) {
		foreach($rset as $dato) {
			$t_emailto[$v_pos]['emailto'] = $dato['email'];
			$t_emailto[$v_pos]['nameto'] = $dato['nombres']." ".$dato['apellidos'];
			$t_emailto[$v_pos]['id_usuario'] = $dato['id_usuario'];
			$t_emailto[$v_pos]['fecha'] = $dato['fecha_cong'];
			$v_pos++;
		}
		enviar_emails($connid, 'Descongelamiento', $v_titulo, $v_msg, $t_emailto);
	}
	return(true);
}
function notificar_inasistencias($connid) {
	$query = "Select usua.nombres, usua.apellidos, usua.id_usuario,
	                 prog.hora_ini, prog.hora_fin, prog.fecha,
					 serv.nombre, usua.email
			    From segu_usuarios usua,
				     spa_programacion prog,
					 conf_servicios serv
			   Where prog.id_usuario  = usua.id_usuario
			     And prog.id_servicio = serv.id_servicio
			     And prog.fecha       = Curdate()-1
				 And prog.asistencia  = 'N'
				 And usua.notificar   = 'S'
			   Order By usua.id_usuario, prog.hora_ini";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_titulo = "Falló a su cita en In Life";
	$v_pos = 0;
	$v_old_id = null;
	//mensaje
	$v_msg = '<h3>Apreciado(a) |nameto|:</h3>';
	$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">In Life Studio le recuerda que faltó a la sesión que tenía programada el día de ayer, a continuación relacionamos las sesiones programadas:</span></p>';
	$v_msg .= '|tabla| <p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">Recuerde que sus resultados dependen de su constancia y persistencia.</span></p>';
	$v_msg .= firma();
	//
	if (is_array($rset)) {
		foreach($rset as $dato) {
			if (!is_null($v_old_id) && $v_old_id != $dato['id_usuario']) {
				$v_pos++;
			}
			if (is_null($v_old_id) || $v_old_id != $dato['id_usuario']) {
				$t_emailto[$v_pos]['emailto'] = $dato['email'];
				$t_emailto[$v_pos]['nameto'] = $dato['nombres']." ".$dato['apellidos'];
				$t_emailto[$v_pos]['id_usuario'] = $dato['id_usuario'];
				$t_emailto[$v_pos]['tabla'] = null;
			}
			$v_hora_ini = DateTime::createFromFormat('d-m-Y H:i', $dato['fecha'].' '.$dato['hora_ini']);
			$v_hora_fin = DateTime::createFromFormat('d-m-Y H:i', $dato['fecha'].' '.$dato['hora_fin']);
			$t_emailto[$v_pos]['tabla'] .= '<p><table border="0"><tr><td>Servicio:</td><td>'.$dato['nombre'].'</td></tr>';
			$t_emailto[$v_pos]['tabla'] .= '<tr><td>Hora del servicio:</td><td>'.$v_hora_ini->format('h:i a').'</td></tr>';
			$t_emailto[$v_pos]['tabla'] .= '<tr><td>Hora de finalización:</td><td>'.$v_hora_fin->format('h:i a').'</td></tr></table></p>';
			$v_old_id = $dato['id_usuario'];
		}
		enviar_emails($connid, 'Inasistencia', $v_titulo, $v_msg, $t_emailto);
	}
	return(true);
}
function notificar_cumpleanos($connid){
	$query = "Select usua.nombres, usua.apellidos, usua.id_usuario, usua.email
	            From segu_usuarios usua
			   Where date_format(fecha_nacimiento, '%d-%m') = date_format(curdate(), '%d-%m')
			     And usua.notificar   = 'S'
			   Order By usua.apellidos";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_titulo = "Inlife Studio te recuerda en tu día";
	$v_pos = 0;
	//mensaje
	$v_msg = '<h3>Apreciado(a) |nameto|:</h3>';
	$v_msg .= '<P><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">In Life Studio quiere celebrar contigo este día tan especial, ven a disfrutar de una sesión de Power Plate Balance, porque sabemos que te mereces lo mejor, regálate lo mejor.</span></P>';
	$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">Comunícate con nosotros y programa tus sesiones.</span></p>';
	$v_msg .= firma();
	//
	if (is_array($rset)) {
		foreach($rset as $dato) {
			$t_emailto[$v_pos]['emailto'] = $dato['email'];
			$t_emailto[$v_pos]['nameto'] = $dato['nombres']." ".$dato['apellidos'];
			$t_emailto[$v_pos]['id_usuario'] = $dato['id_usuario'];
			$v_pos++;
		}
		enviar_emails($connid, 'Cumpleaños', $v_titulo, $v_msg, $t_emailto);
	}
	return(true);
}
function notificar_mantenimientos($connid){
	$query = "Select valor 
	            From conf_parametros para
		       Where codigo = 'FRMN'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_frecuencia = $rset[0]['valor'];
	
	$query = "Select valor 
	            From conf_parametros para
		       Where codigo = 'MNPE'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_mantenimientos = $rset[0]['valor'];
	
	$v_fecha = new DateTime;
	$v_fecha->add(new DateInterval('P2D'));
	$query = "Select usua.nombres, usua.apellidos, usua.id_usuario, usua.email
	            From segu_usuarios usua,
				     spa_pestanas  pest
			   Where pest.id_usuario = usua.id_usuario
			     And pest.mantenimientos between 1 and ".$v_mantenimientos."
				 And pest.fecha_ult_mantenimiento + ".$v_frecuencia." = curdate() + 2
				 And usua.notificar   = 'S'
			  Union
			  Select usua.nombres, usua.apellidos, usua.id_usuario, usua.email
	            From segu_usuarios usua,
				     spa_pestanas  pest
			   Where pest.id_usuario = usua.id_usuario
			     And pest.fecha_ult_mantenimiento is null
				 And pest.fecha_postura + ".$v_frecuencia." = curdate() + 2
				 And usua.notificar   = 'S'
			  Order By apellidos, nombres";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_titulo = "Mantenimiento de pestañas";
	$v_pos = 0;
	//mensaje
	$v_msg = '<h3>Apreciado(a) |nameto|:</h3>';
	$v_msg .='<P><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">In Life Studio Lash le recuerda programar su mantenimiento de extensiones de pesta&ntilde;as el pr&oacute;ximo '.$v_fecha->format('d-m-Y')."</span></P>";
			$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">Recuerde que debe tener como m&iacute;nimo el 70% de sus extensiones para ser v&aacute;lido como mantenimiento.</span></p>';
	$v_msg .= firma();
	//
	if (is_array($rset)) {
		foreach($rset as $dato) {
			$t_emailto[$v_pos]['emailto'] = $dato['email'];
			$t_emailto[$v_pos]['nameto'] = $dato['nombres']." ".$dato['apellidos'];
			$t_emailto[$v_pos]['id_usuario'] = $dato['id_usuario'];
			$v_pos++;
		}
		enviar_emails($connid, 'Mantenimiento', $v_titulo, $v_msg, $t_emailto);
	}
	return(true);
}
function notificar_logins_msv($connid) {
	$query = "Select * From temp_notif_login
	           Order by login
			   Limit 0, 25";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_titulo = "Bienvenido(a) a MyInlife Studio";
	$v_pos = 0;
	$v_msg = '<h3>Apreciado(a) |nameto|:</h3>';
	$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">In Life Studio le da la bienvenida a su portal en Internet, a través del cual podrá mantenerse informado(a) sobre su progreso y tendrá la posibilidad de programar sus sesiones.</span></p>';
	$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">Para ingresar al sistema ingrese a la página de In Life Studio haciendo clic <a href="http://www.inlifestudio.com">Aqu&iacute;</a> y luego seleccione la opci&oacute;n My In Life Login.</span></p>';
	$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">Sus credenciales de acceso son:<p>Login: |login|<br>Contraseña: |numero_id| </span></p>';
	$v_msg .= '<p><span style="font-family:lucida sans unicode,lucida grande,sans-serif;font-size:11px;">El sistema le solicitará que cambie su contraseña por primera vez. Tenga en cuenta que su nueva contraseña no podrá corresponder a su número de documento de identidad y que esta es sensible a mayúsculas y minúsculas</span></p>';
	$v_msg .= firma();
	//
	if (is_array($rset)) {
		foreach($rset as $dato) {
			$t_emailto[$v_pos]['emailto'] = $dato['email'];
			$t_emailto[$v_pos]['nameto'] = $dato['nombres'];
			$t_emailto[$v_pos]['id_usuario'] = $dato['id_usuario'];
			$t_emailto[$v_pos]['login'] = $dato['login'];
			$t_emailto[$v_pos]['numero_id'] = $dato['numero_id'];
			$query = "Delete from temp_notif_login where id_usuario = ".$dato['id_usuario'];
			$result = dbquery ($query, $connid);
			$v_pos++;
		}
		enviar_emails($connid, 'Envío de Login', $v_titulo, $v_msg, $t_emailto);
	}
	return(true);
}
function desactivar_notificaciones($connid, $email){
	$query = "Update segu_usuarios
	             set notificar = 'N'
			   Where email = '".$email."'";
	$result = dbquery ($query, $connid);
	return(true);
}
?>