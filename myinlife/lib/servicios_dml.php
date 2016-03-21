<?php 
/* 
 Libreria de servicios DML para la administraciσn de servicios de la Compaρνa
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca01@gmail.com
 Fecha     : 10/02/2010 08:40 a.m.
 Version   : 1.1
*/
function crea_servicio ($connid,               $nombre,       $descripcion, 
						$precio_base, 	       $impuesto,     $prepagado,
						$programable, 		   $ficha_antrop, $sesion_minima,
						$pestanas,     $dias_venc,
						$dias_mant) {
	if (is_null($sesion_minima) || $sesion_minima =="" ) {
		$sesion_minima = 'Null';
	}
	
	if (is_null($dias_venc) || $dias_venc == "" ) {
		$dias_venc = 'Null';
	}
	if (is_null($dias_mant) || $dias_mant == "" ) {
		$dias_mant = 'Null';
	}
	
	$query = "Insert Into conf_servicios 
	             (nombre,       descripcion,   precio_base,
				  impuesto,     prepagado,     programable,
				  ficha_antrop, sesion_minima,
				  modulo_pestanas, dias_vencimiento, dias_mantenimiento)
				 Values
				 ('".$nombre."', '".$descripcion."', ".$precio_base.",
				  ".$impuesto.", '".$prepagado."', '".$programable."',
				  '".$ficha_antrop."', ".$sesion_minima.",
				  '".$pestanas."', ".$dias_venc.", ".$dias_mant.")";
	$result = dbquery ($query, $connid);
	return (true);
}
function upd_servicio ($connid,       $id_servicio,   $nombre,    $descripcion, 
					   $precio_base,  $impuesto,      $prepagado, $programable,
					   $ficha_antrop, $sesion_minima, 
					   $pestanas,     $dias_venc,	  $dias_mant) {
	if (is_null($sesion_minima) || $sesion_minima =="" ) {
		$sesion_minima = 'Null';
	}
	
	if (is_null($dias_venc) || $dias_venc == "" ) {
		$dias_venc = 'Null';
	}
	if (is_null($dias_mant) || $dias_mant == "" ) {
		$dias_mant = 'Null';
	}
	
	$query = "update conf_servicios serv
	             set serv.nombre = '".$nombre."',             serv.descripcion = '".$descripcion."', 
				     serv.precio_base = ".$precio_base.",     serv.impuesto = ".$impuesto.", 
					 serv.prepagado = '".$prepagado."',       serv.programable = '".$programable."',
					 serv.ficha_antrop = '".$ficha_antrop."', serv.sesion_minima = ".$sesion_minima.", 
					 serv.modulo_pestanas = '".$pestanas."',
					 serv.dias_vencimiento = ".$dias_venc.",  serv.dias_mantenimiento = ".$dias_mant."
			   Where serv.id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
	return (true);
}
function del_servicio ($connid, $id_servicio) {
	$query = "Select count(9) conteo
	            From spa_servicios_x_usuario svus
			   Where svus.id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return (false);
	} 
	
	$query = "Select count(9) conteo
	            From spa_programacion
			   Where id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return (false);
	} 
	$query = "Select count(9) conteo
	            From conf_servicios_x_sede 
			   Where id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return (false);
	} 
	
	$query = "Delete from conf_servicios
		           Where id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
	return (true);
}
function del_servicio_sede ($connid, $id_servicio, $id_sede) {
	
	
	$query = "Select count(9) conteo
	            From spa_programacion
			   Where id_servicio = ".$id_servicio."
		       And id_sede = ".$id_sede;
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return (false);
	} 
	
	
	$query = "Delete from conf_servicios_x_sede  
		           Where id_servicio = ".$id_servicio."
		           And id_sede = ".$id_sede;
	$result = dbquery ($query, $connid);
	return (true);
}
function upd_servicio_cliente ($connid, $id_servicio, $id_usuario, $fecha, $cantidad, $continuidad, $caducidad, $congelar) {
	if (is_null($cantidad) || $cantidad == "" ) {
		$cantidad = 'Null';
	}
	if (is_null($continuidad) || $continuidad == "" ) {
		$continuidad = 'Null';
	}
	if (is_null($caducidad) || $caducidad == "" ) {
		$caducidad = null;
	} else {
		$caducidad = "str_to_date('".$caducidad."', '%d-%m-%Y')";
	}
	if (is_null($congelar) || $congelar == "" ) {
		$congelar = 'N';
	}
	$query = "Select count(9) conteo
	            From spa_servicios_x_usuario
			   Where id_servicio = ".$id_servicio."
			     And id_usuario  = ".$id_usuario."
				 And fecha		 = str_to_date('".$fecha."', '%d-%m-%Y')";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	
	if ($rset[0]['conteo'] == 0 ) {
		$query = "Insert into spa_servicios_x_usuario
	                (id_servicio, id_usuario, 
			   	     fecha,		  cantidad,
					 continuidad, caducidad,
					 congelar,    fecha_cambio )
				    values 
				    (".$id_servicio.", ".$id_usuario.", 
				     str_to_date('".$fecha."', '%d-%m-%Y'), ".$cantidad.",
					 ".$continuidad.", ".$caducidad.",
					 '".$congelar."', curdate())";
	} else {
		$query = "Update spa_servicios_x_usuario
		             Set cantidad = ".$cantidad.", continuidad = ".$continuidad.",
					     caducidad = ".$caducidad.", congelar = '".$congelar."',
						 fecha_cambio = Curdate()
				   Where id_servicio = ".$id_servicio."
			         And id_usuario  = ".$id_usuario."
					 And fecha		 = str_to_date('".$fecha."', '%d-%m-%Y')";
	}
	$result = dbquery ($query, $connid);
	return(true);
}
function del_servicio_cliente ($connid, $id_servicio, $id_usuario, $fecha) {
	$query = "Select Count(9) conteo
	            From spa_programacion prog
			   Where prog.id_servicio = ".$id_servicio."
			     And prog.id_usuario  = ".$id_usuario."
				 And prog.fecha      >= str_to_date('".$fecha."', '%d-%m-%Y')";
				 
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	
	if ($rset[0]['conteo'] == 0 ) {
		$query = "Delete From spa_servicios_x_usuario
				   Where id_servicio = ".$id_servicio."
					 And id_usuario  = ".$id_usuario."
					 And fecha		 = str_to_date('".$fecha."', '%d-%m-%Y')";
		$result = dbquery ($query, $connid);
		return(true);
	} else {
		return(false);
	}
}
function upd_restriccion_horaria ($connid, $id_servicio, $dia, $hora_inicio) {
	$query = "Select IfNull(sesion_minima, 0) minima
	            From conf_servicios serv
			   Where serv.id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	
	if (!is_array($rset) || $rset[0]['minima'] == 0) {
		return (false);
	} else {
		$v_intervalo = new DateInterval ('PT'.$rset[0]['minima'].'M');
	}
	$v_hora_fin = DateTime::createFromFormat ('d-m-Y H:i', '01-01-2001 '.$hora_inicio);
	$v_hora_fin->add($v_intervalo);
	
	$query = "Select count(9) conteo
	            From spa_resthoraria reho
			   Where reho.id_servicio  = ".$id_servicio."
			     And reho.dia          = ".$dia."
				 And reho.hora_inicio  = '".$hora_inicio."'
				 And reho.hora_final   = '".$v_hora_fin->format('H:i')."'";
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return (true);
	} else {
		$query = "Insert into spa_resthoraria (id_servicio, dia, hora_inicio, hora_final)
		            Values (".$id_servicio.", ".$dia.", '".$hora_inicio."', '".$v_hora_fin->format('H:i')."')";
		$result = dbquery ($query, $connid);
		return (true);
	}
}
function del_restriccion_horaria ($connid, $id_servicio, $dia) {
	$query = "Delete from spa_resthoraria
	           Where id_servicio = ".$id_servicio."
			     And dia         = ".$dia;
	$result = dbquery ($query, $connid);
	return (true);
}
?>