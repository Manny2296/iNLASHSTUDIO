<?php
function crea_programacion ($connid,   $id_usuario,      $id_servicio,
							$fecha,    $hora_ini,        $hora_fin,
							$maquina,  $sesion_especial, $login_mod,
							$cortesia, $comentarios,     $maquinas_bloqueo) {
	if (is_null($sesion_especial)) {
		$sesion_especial = 'N';
	}
	$v_db_hini = "str_to_date(concat(date_format(fecha, '%d-%m-%Y'),' ',hora_ini), '%d-%m-%Y %H:%i')";
	$v_db_hfin = "str_to_date(concat(date_format(fecha, '%d-%m-%Y'),' ',hora_fin), '%d-%m-%Y %H:%i')";
	$v_hini = "str_to_date(concat(date_format(fecha, '%d-%m-%Y'),' ".$hora_ini."'), '%d-%m-%Y %H:%i')";
	$v_hfin = "str_to_date(concat(date_format(fecha, '%d-%m-%Y'),' ".$hora_fin."'), '%d-%m-%Y %H:%i')";
	//inicializar resultado de la transacción
	$t_result[0] = true;
	$t_result[1] = null;
	$query = "Select prepagado
	            From conf_servicios
			   Where id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_prepago = $rset[0]['prepagado'];
	//validar que la maquina está disponible
	$query = "Select count(9) conteo
	            From spa_programacion
			   Where id_servicio    = ".$id_servicio."
			     And fecha          = str_to_date('".$fecha."', '%d-%m-%Y')
				 And (( ".$v_hini." >= ".$v_db_hini." And
				        ".$v_hini."  < ".$v_db_hfin." ) Or
					  ( ".$v_hfin." > ".$v_db_hini." And
				        ".$v_hfin." <= ".$v_db_hfin." ))
				 And maquina        = ".$maquina;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		$t_result[0] = false;
		$t_result[1] = "La estaci&oacute;n solicitada se encuentra ocupada";
		return ($t_result);
	}
	//validar que el servicio esté disponible
	$v_fecha = DateTime::createFromFormat('d-m-Y', $fecha);
	$v_db_hini = "str_to_date(concat('01-01-2001',' ',hora_inicio), '%d-%m-%Y %H:%i')";
	$v_db_hfin = "str_to_date(concat('01-01-2001',' ',hora_final), '%d-%m-%Y %H:%i')";
	$v_hini = "str_to_date('01-01-2001 ".$hora_ini."', '%d-%m-%Y %H:%i')";
	$v_hfin = "str_to_date('01-01-2001 ".$hora_fin."', '%d-%m-%Y %H:%i')";
	
	$query = "Select count(9) conteo
				From spa_resthoraria
			   Where id_servicio = ".$id_servicio."
			     And dia         = ".$v_fecha->format('N')."
				 And (( ".$v_hini." >= ".$v_db_hini." And
				        ".$v_hini."  < ".$v_db_hfin." ) Or
					  ( ".$v_hfin." > ".$v_db_hini." And
				        ".$v_hfin." <= ".$v_db_hfin." ))";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		$t_result[0] = false;
		$t_result[1] = "El servicio no est&aacute; disponible en este horario";
		return ($t_result);
	}
	//validar que el usuario no tenga nada programado en la hora establecida
	$query = "Select count(9) conteo
	            From spa_programacion
			   Where id_usuario = ".$id_usuario."
			     And fecha      = str_to_date('".$fecha."', '%d-%m-%Y')
				 And hora_ini   = '".$hora_ini."'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		$t_result[0] = false;
		$t_result[1] = "El cliente tiene programado otro servicio en este horario";
		return ($t_result);
	}
	//validar que el usuario tenga disponibilidad contratada
	$query = "Select count(9) disp
	            From spa_servicios_x_usuario svus
			   Where svus.id_servicio = ".$id_servicio."
			     And svus.id_usuario  = ".$id_usuario."
				 And sesiones_disp (svus.id_servicio, svus.id_usuario, svus.fecha) > 0";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['disp'] < 1 && $cortesia == "N" && $v_prepago == "S") {
		$t_result[0] = false;
		$t_result[1] = "El cliente no tiene sesiones contratadas para este servicio";
		return ($t_result);
	}
	$query = "Insert Into spa_programacion
	             (id_servicio, id_usuario, fecha,
				  hora_ini,    hora_fin,   maquina,
				  login_mod,   asistencia, sesion_especial, 
				  cortesia,    comentarios)
				 Values
				 (".$id_servicio.", ".$id_usuario.", str_to_date('".$fecha."', '%d-%m-%Y'),
				  '".$hora_ini."',  '".$hora_fin."', ".$maquina.",
				  '".$login_mod."', Null, '".$sesion_especial."',
				  '".$cortesia."', '".$comentarios."')";
	$result = dbquery ($query, $connid);
	$query = "select Last_Insert_Id() id
	            From spa_programacion";
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	$v_id_programacion = $rset[0]['id']; 
	$t_result[1] = $v_id_programacion;
	$query = "Insert Into spa_bloqueo_estacion (id_programacion, maquina)
				  Values (".$v_id_programacion.", ".$maquina.")";
	$result = dbquery ($query, $connid);
	if (is_array($maquinas_bloqueo)) {
		foreach($maquinas_bloqueo as $dato) {
			$query = "Insert Into spa_bloqueo_estacion (id_programacion, maquina)
			              Values (".$v_id_programacion.", ".$dato.")";
			$result = dbquery ($query, $connid);
		}
	}
	return ($t_result);
}
function del_programacion ($connid, $id_programacion) {
	//validar que la clase no se encuentra asistida y que es presente o futura
	$query = "Select count(9) conteo
	            From spa_programacion prog
			   Where prog.id_programacion = ".$id_programacion."
			     And prog.asistencia    = 'S'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return (false);
	}
	$query = "Delete From spa_bloqueo_estacion
	           Where id_programacion = ".$id_programacion;
	$result = dbquery ($query, $connid);
	
	$query = "Delete From spa_programacion
	           Where id_programacion = ".$id_programacion;
	$result = dbquery ($query, $connid);
	return (true);
}
function upd_estado_prog ($connid, $id_programacion, $asistencia) {
	$query = "Update spa_programacion
	             Set asistencia = '".$asistencia."'
			   Where id_programacion = ".$id_programacion;
	$result = dbquery ($query, $connid);
	return (true);
}
function asistencias_pasadas ($connid) {
	$query = "Update spa_programacion
	             Set asistencia  = 'S'
			   Where asistencia Is Null
			     And fecha       < Curdate()";
	$result = dbquery ($query, $connid);
	return (true);
}
function agregar_mantenimiento ($connid, $id_usuario, $fecha_mantenimiento) {
	//obtener el número de mantenimientos configurados
	$query = "Select valor
	            From conf_parametros
			   Where codigo = 'MNPE'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_cantidad = $rset[0]['valor'];
	//verificar si hay mantenimiento de pestañas para el cliente y obtener fecha de postura
	$query = "Select fecha_postura
	            From spa_pestanas
			   Where id_usuario     = ".$id_usuario."
			     And mantenimientos < ".$v_cantidad;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (is_array($rset) && !is_null($rset)) {
		$query = "Update spa_pestanas
		             Set fecha_ult_mantenimiento = str_to_date('".$fecha_mantenimiento."', '%d-%m-%Y'),
					     mantenimientos = mantenimientos + 1
				   Where id_usuario = ".$id_usuario."
				     And fecha_postura = '".$rset[0]['fecha_postura']."'";
		$result = dbquery ($query, $connid);
		return(true);
	} else {
		return(false);
	}
}
function completar_mantenimientos($connid, $id_usuario, $fecha_mantenimiento) {
	//obtener el número de mantenimientos configurados
	$query = "Select valor
	            From conf_parametros
			   Where codigo = 'MNPE'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_cantidad = $rset[0]['valor'];
	
	$query = "Update spa_pestanas
		         Set fecha_ult_mantenimiento = str_to_date('".$fecha_mantenimiento."', '%d-%m-%Y'),
				     mantenimientos = ".$v_cantidad."
			   Where id_usuario     = ".$id_usuario."
				 And mantenimientos < ".$v_cantidad;
	$result = dbquery ($query, $connid);
	return(true);	 
}
?>