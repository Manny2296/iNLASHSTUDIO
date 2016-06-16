<?php
function list_estacion_sin_bloqueo($connid, $id_servicio, $fecha, $hora_ini, $hora_fin,$id_sede){
	$query = "Select sesiones_simultaneas maquinas
	            From conf_servicios_x_sede serv
			   Where serv.id_servicio = ".$id_servicio."
			   And   serv.id_sede =".$id_sede;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_maquinas = $rset[0]['maquinas'];
	$v_fecha_inidb = "str_to_date(concat(date_format(prog.fecha, '%d-%m-%Y'),' ',hora_ini), '%d-%m-%Y %H:%i')";
	$v_fecha_findb = "str_to_date(concat(date_format(prog.fecha, '%d-%m-%Y'),' ',hora_fin), '%d-%m-%Y %H:%i')";
	$v_fecha_ini = "str_to_date('".$fecha." ".$hora_ini."', '%d-%m-%Y %H:%i')";
	$v_fecha_fin = "str_to_date('".$fecha." ".$hora_fin."', '%d-%m-%Y %H:%i')";
	$v_pos = 0;
	$t_result = null;
	for($x=1;$x<=$v_maquinas;$x++){
		$query = "Select count(9) conteo
		            From spa_bloqueo_estacion bles,
					     spa_programacion     prog
				   Where bles.id_programacion = prog.id_programacion
				     And bles.maquina         = ".$x."
					 And ((".$v_fecha_inidb." >= ".$v_fecha_ini." And
					       ".$v_fecha_inidb." < ".$v_fecha_fin." ) Or
						   (".$v_fecha_findb." > ".$v_fecha_ini." And
					       ".$v_fecha_findb." <= ".$v_fecha_fin." ))";
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		if ($rset[0]['conteo'] == "0") {
			$t_result[$v_pos]=$x;
			$v_pos++;
		}
	}
	return($t_result); 
}
function maquina_bloqueada($connid, $id_servicio, $maquina, $fecha, $hora) {
	$v_fecha_inidb = "str_to_date(concat(date_format(prog.fecha, '%d-%m-%Y'),' ',hora_ini), '%d-%m-%Y %H:%i')";
	$v_fecha_findb = "str_to_date(concat(date_format(prog.fecha, '%d-%m-%Y'),' ',hora_fin), '%d-%m-%Y %H:%i')";
	$v_fecha = "str_to_date('".$fecha." ".$hora."', '%d-%m-%Y %H:%i')";
	$query = "Select count(9) conteo
				From spa_bloqueo_estacion bles,
					 spa_programacion     prog
			   Where bles.id_programacion = prog.id_programacion
				 And bles.maquina         = ".$maquina."
				 And ".$v_fecha."        >= ".$v_fecha_inidb."
				 And ".$v_fecha."         < ".$v_fecha_findb." 
				 And prog.id_servicio     = ".$id_servicio;
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	if($rset[0]['conteo'] == 0) {
		return(false);
	} else {
		return(true);
	}
}
function horas_fin_servicio($connid, $id_servicio, $maquina, $fecha, $hora_ini) {
	$t_result = null;
	$query = "Select valor
	            From conf_parametros para
			   Where para.codigo = 'HFIN'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_ifnull = "str_to_date('".$fecha." ".$rset[0]['valor']."', '%d-%m-%Y %H:%i')";
	$query = "Select Date_Format(IfNull(Min(str_to_date(Concat('".$fecha." ',prog.hora_ini), '%d-%m-%Y %H:%i')), ".$v_ifnull."), '%d-%m-%Y %H:%i') fecha_min
	            From spa_programacion prog
			   Where prog.fecha       = str_to_date('".$fecha."', '%d-%m-%Y')
			     And str_to_date(Concat('".$fecha." ',prog.hora_ini), '%d-%m-%Y %H:%i') > str_to_date('".$fecha." ".$hora_ini."', '%d-%m-%Y %H:%i')
				 And prog.id_servicio = ".$id_servicio."
				 And prog.maquina     = ".$maquina;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_max_hora = DateTime::createFromFormat('d-m-Y H:i', $rset[0]['fecha_min']);
	$query = "Select sesion_minima
	            From conf_servicios serv
			   Where serv.id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_intervalo = new DateInterval('PT'.$rset[0]['sesion_minima'].'M');
	$v_min_hora = DateTime::createFromFormat('d-m-Y H:i', $fecha.' '.$hora_ini);
	$v_min_hora->add($v_intervalo);
	$v_pos = 0;
	while($v_min_hora <= $v_max_hora) {
		$t_result[$v_pos] = $v_min_hora->format('h:i a');
		$v_pos++;
		$v_min_hora->add($v_intervalo);
	}
	return ($t_result);
}
function req_toma_medidas ($connid, $id_programacion) {
	$query = "Select prog.sesion_especial, prog.id_usuario, date_format(prog.fecha, '%d-%m-%Y') fecha
	            From spa_programacion prog
		 	   Where prog.id_programacion = ".$id_programacion;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$activar = $rset[0]['sesion_especial'];
	$id_usuario = $rset[0]['id_usuario'];
	$v_fecha = DateTime::createFromFormat('d-m-Y', $rset[0]['fecha']);
	
	$query = "Select max(fecha) ultima_toma
			    From spa_ficha_antro
			   Where id_usuario = ".$id_usuario;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (is_array($rset) && !is_null($rset[0]['ultima_toma'])) {
		$v_ultima_toma = DateTime::createFromFormat('d-m-Y', $rset[0]['ultima_toma']);
	} else {
		$v_ultima_toma = clone $v_fecha;
	}
	if ( $activar == "S" && $v_fecha >= $v_ultima_toma) {
		return ('S');
	} else {
		return ('N');
	}
}
function req_toma_medidas_usua ($connid, $id_usuario, $id_servicio, $fecha) {
	// verificar si el servicio requiere toma de medidas
	$query = "Select valor
	            From conf_parametros para
			   Where para.codigo = 'FAFA'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (!is_array($rset) || is_null($rset[0]['valor']) || $rset[0]['valor'] <= 0) {
		return ('N');
	} else {
		$v_frecuencia = $rset[0]['valor'];
		$query = "Select Count(9) conteo
		            From conf_servicios   serv
				   Where serv.id_servicio  = ".$id_servicio."
				     And serv.ficha_antrop = 'S'";
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		if ($rset[0]['conteo'] == 0) {
			return ('N');
		} else {
			$query = "Select max(fecha) ultima_toma
			            From spa_ficha_antro
					   Where id_usuario = ".$id_usuario;
			$result = dbquery ($query, $connid);
    		$rset = dbresult($result);
			
			if (!is_array($rset) || is_null($rset[0]['ultima_toma'])) {
				return('S');	
			} else {
				$v_ultima_toma = $rset[0]['ultima_toma'];
			}
			$query = "Select count(9) conteo
			            From spa_programacion prog,
				             conf_servicios   serv
			           Where serv.id_servicio  = prog.id_servicio
			             And serv.ficha_antrop = 'S'
					     And prog.id_usuario   = ".$id_usuario."
						 And prog.fecha        between '".$v_ultima_toma."' And str_to_date('".$fecha."', '%d-%m-%Y')
						 And prog.asistencia   = 'S'";
			$result = dbquery ($query, $connid);
    		$rset = dbresult($result);
			if ($rset[0]['conteo'] >= $v_frecuencia) {
				return('S');
			} else {
				return('N');				
			}
		}
	}
}
function req_pestanas ($connid, $id_programacion) {
	//verificar que el servicio programado es de pentañas
	$query = "Select count(9) conteo
	            From spa_programacion prog,
				     conf_servicios   serv
			   Where prog.id_servicio     = serv.id_servicio
			     And serv.modulo_pestanas = 'S'
				 And prog.id_programacion = ".$id_programacion;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] == "0") {
		return (false);
	}
	//obtener el número de mantenimientos configurados
	$query = "Select valor
	            From conf_parametros
			   Where codigo = 'MNPE'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_cantidad = $rset[0]['valor'];
	//verificar si el usuario tiene manteniemientos pendientes
	$query = "Select count(9) conteo
	            From spa_pestanas pest,
				     spa_programacion prog
		       Where pest.id_usuario      = prog.id_usuario
			     And prog.id_programacion = ".$id_programacion."
				 And pest.mantenimientos  < ".$v_cantidad;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] == 0) {
		return (true);
	} else {
		return (false);
	}
}
function req_mantenimiento($connid, $id_programacion) {
	//verificar que el servicio programado es de pentañas
	$query = "Select count(9) conteo
	            From spa_programacion prog,
				     conf_servicios   serv
			   Where prog.id_servicio     = serv.id_servicio
			     And serv.modulo_pestanas = 'S'
				 And prog.id_programacion = ".$id_programacion;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] == "0") {
		return (false);
	}
	//obtener el número de mantenimientos configurados
	$query = "Select valor
	            From conf_parametros
			   Where codigo = 'MNPE'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_cantidad = $rset[0]['valor'];
	//verificar si el usuario tiene manteniemientos pendientes
	$query = "Select count(9) conteo
	            From spa_pestanas pest,
				     spa_programacion prog
		       Where pest.id_usuario      = prog.id_usuario
			     And prog.id_programacion = ".$id_programacion."
				 And pest.mantenimientos  < ".$v_cantidad;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] == 0) {
		return (false);
	} else {
		return (true);
	}
}
function req_mantenimiento_usua($connid, $id_servicio, $id_usuario) {
	$query = "Select count(9) conteo
	            From conf_servicios   serv
			   Where serv.modulo_pestanas = 'S'
				 And serv.id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] == "0") {
		return ('N');
	}
	//obtener el número de mantenimientos configurados
	$query = "Select valor
	            From conf_parametros
			   Where codigo = 'MNPE'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_cantidad = $rset[0]['valor'];
	//verificar si el usuario tiene mantenimientos pendientes
	$query = "Select count(9) conteo
	            From spa_pestanas pest
		       Where pest.id_usuario      = ".$id_usuario."
			     And pest.mantenimientos  < ".$v_cantidad;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] == 0) {
		return ('N');
	} else {
		return ('S');
	}
}
function get_ult_mantenimiento($connid, $id_usuario) {
	$query = "Select date_format(IfNull(Max(fecha_ult_mantenimiento), Curdate()), '%d-%m-%Y') fecha
	            From spa_pestanas
			   Where id_usuario = ".$id_usuario;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return($rset[0]['fecha']);
}
function lista_horas ($connid, $id_servicio) {
	$query = "Select valor
	            From conf_parametros para
			   Where para.codigo = 'HINI'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_hora_ini = DateTime::createFromFormat ('d-m-Y H:i', '01-01-2001 '.$rset[0]['valor']);
	$query = "Select valor
	            From conf_parametros para
			   Where para.codigo = 'HFIN'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_hora_fin = DateTime::createFromFormat ('d-m-Y H:i', '01-01-2001 '.$rset[0]['valor']);
	$query = "Select sesion_minima
	            From conf_servicios serv
			   Where id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_intervalo = new DateInterval ('PT'.$rset[0]['sesion_minima'].'M');
	$v_cont = 0;
	$rset = null;
	while ($v_hora_ini <= $v_hora_fin) {
		$rset[$v_cont] = $v_hora_ini->format('H:i');
		$v_hora_ini->add($v_intervalo);
		$v_cont++;
	}
	return ($rset);
}
function lista_servicios_prog ($connid, $tipo, $id_usuario, $id_sede) {
	if ($tipo == "all" && is_null($id_sede)) {
		$query = "Select serv.id_servicio, serv.nombre, csps.id_sede, csps.id_servicio, csps.sesiones_simultaneas
		            From conf_servicios serv,
		            	 conf_servicios_x_sede csps 
				   Where serv.sesion_minima is not null
				     And serv.sesion_minima > 0
				     And csps.id_servicio = serv.id_servicio
				   Order By nombre";
	} else if($tipo == "all" ){
		$query = "Select serv.id_servicio, serv.nombre, csps.id_sede, csps.id_servicio, csps.sesiones_simultaneas
		            From conf_servicios serv,
		            	 conf_servicios_x_sede csps 
				   Where serv.sesion_minima is not null
				     And serv.sesion_minima > 0
				     And csps.id_servicio = serv.id_servicio
				     And csps.id_sede =".$id_sede."
				   Order By nombre";
	}else{
		$query = "Select serv.id_servicio, serv.nombre
		            From conf_servicios serv
				   Where serv.programable   = 'S'
				     And sesion_minima     Is Not Null
				     And sesion_minima      > 0
					 And sesiones_disp(id_servicio, ".$id_usuario.", curdate()) > 0
				   Order By nombre";
	}
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function lista_servicios_cont ($connid, $id_usuario) {
	$query = "Select Distinct serv.id_servicio, serv.nombre
		        From conf_servicios serv,
				 	 spa_servicios_x_usuario svcl
			   Where svcl.id_servicio = serv.id_servicio
			     And svcl.id_usuario  = ".$id_usuario."
				 And svcl.continuidad > 0
			    Order By serv.nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function horario_usuario ($connid, $id_usuario, $fecha) {
	$query = "Select prog.id_programacion, serv.nombre, prog.hora_ini, prog.hora_fin,
                     prog.maquina, prog.asistencia
	 		    From spa_programacion prog,
				     conf_servicios   serv
			   Where prog.id_servicio = serv.id_servicio
			     And prog.fecha       = str_to_date('".$fecha."', '%d-%m-%Y')
			     And prog.id_usuario = ".$id_usuario."
			   Order By prog.hora_ini";
    $result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function lista_horas_prog($connid, $tipo, $id_servicio, $maquina, $fecha, $id_usuario, $id_sede) {
	$v_fecha = DateTime::createFromFormat('d-m-Y', $fecha);
	if ($tipo == "servicio") {
		$query = "Select prog.id_programacion, prog.id_usuario, prog.hora_ini, prog.hora_fin,
						 prog.maquina,         serv.nombre,     usua.nombres,  usua.apellidos

					
					From spa_programacion prog,
					     conf_servicios   serv,
						 segu_usuarios    usua,
						 conf_servicios_x_sede csps
				   Where prog.id_servicio = serv.id_servicio
				     And prog.id_usuario  = usua.id_usuario
				     And prog.fecha       = str_to_date('".$fecha."', '%d-%m-%Y')
					 And prog.id_servicio = ".$id_servicio."
					 And prog.maquina      = ".$maquina."
					 And csps.id_sede	   = ".$id_sede."
					 And prog.id_servicio = csps.id_servicio
					 And prog.id_sede = csps.id_sede
				  Union All
				  Select Null id_programacion, Null id_usuario, reho.hora_inicio hora_ini, reho.hora_final hora_fin,
						 0 maquina,            Null nombre,     null nombres,              null apellidos
					From spa_resthoraria reho
				   Where reho.id_servicio = ".$id_servicio."
					 And reho.dia         = ".$v_fecha->format('N')."
				  Union All
				  Select prog.id_programacion, prog.id_usuario, prog.hora_ini, prog.hora_fin,
						 bles.maquina,                serv.nombre,     'Estación' nombres,  'Bloqueda' apellidos
					From spa_programacion      prog,
					     conf_servicios        serv,
						 segu_usuarios         usua,
						 spa_bloqueo_estacion  bles,
						 conf_servicios_x_sede csps
				   Where prog.id_servicio     = serv.id_servicio
				     And prog.id_usuario      = usua.id_usuario
					 And prog.id_programacion = bles.id_programacion
				     And prog.fecha           = str_to_date('".$fecha."', '%d-%m-%Y')
					 And prog.id_servicio     = ".$id_servicio."
					 And bles.maquina         = ".$maquina."
					 And csps.id_sede	   = ".$id_sede."
					 And prog.id_servicio = csps.id_servicio
					 And prog.id_sede = csps.id_sede
					 And bles.id_programacion Not In (Select id_programacion
					                                    From spa_programacion
													   Where id_servicio = ".$id_servicio."
													     And fecha   = str_to_date('".$fecha."', '%d-%m-%Y')
														 And maquina = ".$maquina."
														 And id_sede	   = ".$id_sede.")
			       Order By hora_ini, maquina";
	} else {
		$query = "Select prog.id_programacion, prog.id_usuario, prog.hora_ini, prog.hora_fin,
						 prog.maquina,         serv.nombre
					From spa_programacion prog,
					     conf_servicios   serv
				   Where prog.id_servicio = serv.id_servicio
				     And prog.fecha       = str_to_date('".$fecha."', '%d-%m-%Y')
					 And prog.id_servicio = ".$id_servicio."
					 And prog.maquina     = ".$maquina."
				  Union
				  Select Null id_programacion, Null id_usuario, reho.hora_inicio hora_ini, reho.hora_final hora_fin,
						 0 maquina,            Null nombre
					From spa_resthoraria reho
				   Where reho.id_servicio = ".$id_servicio."
					 And reho.dia         = ".$v_fecha->format('N')."
				  Union
				  Select prog.id_programacion, prog.id_usuario, prog.hora_ini, prog.hora_fin,
						 0 maquina,            serv.nombre
					From spa_programacion prog,
					     conf_servicios   serv
				   Where prog.id_servicio  = serv.id_servicio
				     And prog.fecha        = str_to_date('".$fecha."', '%d-%m-%Y')
					 And prog.id_servicio != ".$id_servicio."
					 And prog.id_usuario   = ".$id_usuario."
				  Union 
				  Select prog.id_programacion, Null, prog.hora_ini, prog.hora_fin,
						 bles.maquina,         serv.nombre
					From spa_programacion     prog,
					     conf_servicios       serv,
						 spa_bloqueo_estacion bles
				   Where prog.id_servicio     = serv.id_servicio
				     And prog.id_programacion = bles.id_programacion
				     And prog.fecha           = str_to_date('".$fecha."', '%d-%m-%Y')
					 And prog.id_servicio     = ".$id_servicio."
					 And bles.maquina         = ".$maquina."
					 And bles.id_programacion Not In (Select id_programacion
					                                    From spa_programacion
													   Where id_servicio = ".$id_servicio."
													     And fecha   = str_to_date('".$fecha."', '%d-%m-%Y')
														 And maquina = ".$maquina.")
				   Order By hora_ini, maquina";
	}
    $result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function numero_maquinas ($connid, $id_servicio, $id_sede) {
	$query = "Select sesiones_simultaneas cant
	            From conf_servicios_x_sede
			   Where id_servicio = ".$id_servicio."
			   And   id_sede = ".$id_sede;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]['cant']);
}
function get_id_usuario_prog ($connid, $id_programacion) {
	$query = "Select id_usuario
				From spa_programacion prog
			   Where prog.id_programacion =".$id_programacion;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]['id_usuario']);
}
function detalle_programacion($connid, $id_programacion) {
	$query = "Select date_format(prog.fecha, '%d-%m-%Y') fecha, serv.nombre, 
	                 usua.id_usuario, Concat(usua.nombres,' ',usua.apellidos) nomcliente, 
					 prog.hora_ini, prog.hora_fin,
					 ifNull(prog.asistencia, 'S') asistencia,
					 prog.comentarios, prog.cortesia,sede.nombre snom, sede.direccion,
					 sede.pais, sede.ciudad, sede.telefono,usua.celular
	            From spa_programacion prog,
				     segu_usuarios    usua,
					 conf_servicios   serv,
					 conf_sedes       sede,
					 segu_perfil_x_usuario cpfu
			   Where prog.id_servicio = serv.id_servicio
			     And prog.id_usuario  = usua.id_usuario
				 And prog.id_programacion = ".$id_programacion."
				 And usua.id_usuario = cpfu.id_usuario
				 And sede.id_sede = cpfu.id_sede";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}
function reporte_asistencia ($connid, $fecha_ini, $fecha_fin, $id_usuario, $id_servicio){
 	$query = "Select Date_Format(prog.fecha, '%u')+1 semana, Date_Format(prog.fecha, '%Y') ano,
	                 Date_Format(prog.fecha, '%m') mes,    Count(prog.id_programacion) cant
				From spa_programacion prog
			   Where prog.id_usuario  = ".$id_usuario."
			     And prog.fecha       Between str_to_date('".$fecha_ini."', '%d-%m-%Y') And str_to_date('".$fecha_fin."', '%d-%m-%Y')
			     And prog.asistencia  = 'S'
				 And prog.id_servicio = ".$id_servicio."
			   Group By Date_Format(prog.fecha, '%u'), Date_Format(prog.fecha, '%Y'), Date_Format(prog.fecha, '%m')
			   Order By Date_Format(prog.fecha, '%m'), Date_Format(prog.fecha, '%Y'), Date_Format(prog.fecha, '%u')";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function reporte_asistencia_base ($connid, $fecha_ini, $fecha_fin, $id_usuario, $id_servicio){
	
	$query = "Select Distinct Date_Format(svus.fecha, '%u')+1 semana, Date_Format(svus.fecha, '%Y') ano,
	                 Date_Format(svus.fecha, '%m') mes,             svus.continuidad
				From spa_servicios_x_usuario svus
			   Where svus.id_usuario  = ".$id_usuario."
			     And svus.id_servicio = ".$id_servicio."
				 And sesiones_disp (svus.id_servicio, svus.id_usuario, svus.fecha) > 0";
	//echo($query);
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function get_primera_fecha($connid, $id_usuario, $id_servicio, $fecha) {
	$query = "Select fecha
	            From spa_programacion prog
			   Where prog.id_usuario   = ".$id_usuario."
			     And prog.id_servicio  = ".$id_servicio."
				 And prog.fecha       >= str_to_date('".$fecha."', '%d-%m-%Y')
			   Limit 0,1";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]['fecha']);
}
?>