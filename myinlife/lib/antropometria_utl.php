<?php
/* 
 Libreria de servicios utilitarios para la administración de la ficha antrometrica del usuario
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca01@gmail.com
  Desarrollo: Dev Manuel Felipe S.R 
 			manuel.sanchez-r@mail.escuelaing.edu.co
 Fecha     : 11/02/2010 11:30 a.m.
 Version   : 1.0
*/
function lista_medidas($connid, $id_usuario) {
	$query = "Select genero
	            From segu_usuarios
			   Where id_usuario = ".$id_usuario;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_genero = $rset[0]['genero'];
	 
	$query = "Select medi.id_medida,  tpme.nombre nomtipo,
	                 medi.nombre,     medi.unidad,
					 medi.calculable, medi.orden
				From conf_medidas medi,
				     conf_tipo_medidas tpme
			   Where medi.id_tpmedida = tpme.id_tpmedida
			     And (medi.genero     = 'T' Or
				      medi.genero     = '".$v_genero."')
			   Order By medi.id_tpmedida, medi.orden";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function medida_detalle($connid, $id_medida) {
	$query = "Select * 
	            From conf_medidas medi
			   Where medi.id_medida = ".$id_medida;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}
function lista_tipos_medida ($connid) {
	$query = "Select tpme.id_tpmedida, tpme.nombre
	            From conf_tipo_medidas tpme
			   Order By tmpe.nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function lista_orden_medida ($connid, $id_tpmedida, $orden) {
	$query = "Select count(9) conteo
	            From conf_medidas medi
			   Where medi.id_tpmedida = ".$id_tpmedida;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_tam = $set[0]['conteo'];
?>
<select name="p_orden[]" id="p_orden">
<?php 
   for ($x=1; $x<=$tam; $x++) {
?>
   <option value="<?php echo ($x); ?>" <?php if ($x == $orden) {echo("Selected");} ?>><?php echo ($x); ?></option>
<?php 
   }
?>
</select>
<?php
}
function lista_tabla_medidas ($connid, $id_medida) {
	$query = "Select id_det_medida,  rango_min, rango_max, 
	                 interpretacion, anotaciones
			    From conf_tabla_medidas tame
			   Where id_medida = ".$id_medida."
			   Order By rango_min";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function detalle_tabla_medidas ($connid, $id_det_medida) {
	$query = "Select rango_min,      rango_max, 
	                 interpretacion, anotaciones
			    From conf_tabla_medidas tame
			   Where id_det_medida = ".$id_det_medida;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}
function anotacion_medida($connid, $id_medida, $valor) {
	$query = "Select interpretacion, anotaciones
			    From conf_tabla_medidas tame
			   Where id_medida        = ".$id_medida."
			     And ".$valor." Between rango_min And rango_max";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (is_array($rset)) {
	    return ($rset[0]);
	} else {
	    return (null);
	}
}
function calcula_medida ($connid, $formula, $id_usuario, $fecha) {
	//los separadores de cada parametro son []
	$v_eol = strlen($formula);
	$resultado = 0;
	$v_pos = strpos($formula, "[");
	if ($v_pos === false) {
		eval("$resultado = ".$formula.";");
	} else {
		while ($v_pos >= 0 && $v_pos !== false && $v_pos < $v_eol) {
			$v_pos_end = strpos($formula, "]", $v_pos);
			if ($v_pos_end === false) {
				return (0);
			}
			$v_length = $v_pos_end-$v_pos-1;
			if ($v_length > 0) {
				$v_id_medida = (int)substr($formula, $v_pos+1, $v_length);
				$query = "Select valor
				            From spa_ficha_antro fian
						   Where fian.id_usuario = ".$id_usuario."
						     And fian.id_medida  = ".$v_id_medida."
							 And fian.fecha      = str_to_date('".$fecha."', '%d-%m-%Y')";
				$result = dbquery ($query, $connid);
    			$rset = dbresult($result);
				if (!is_array($rset)) {
					return (0);
				}
				
				$formula = str_replace ("[".$v_id_medida."]", $rset[0]['valor'], $formula);
			} else {
				return(0);
			}
			$v_pos = strpos($formula, "[");
		}
		//echo("formula: ".$formula);
		eval("\$resultado = ".$formula.";");
	}
	return (round($resultado,2));
}
function lista_medidas_usuario ($connid, $id_usuario, $fecha) {
	if (!is_null($fecha)) {
		$v_fecha_txt = "str_to_date('".$fecha."', '%d-%m-%Y')";
	} else {
		$v_fecha_txt = "fian.fecha";
	}
	$query = "Select fian.id_medida, fian.fecha, 
	                 fian.valor,     fian.objetivo,
	                 medi.nombre,    medi.unidad,
					 medi.orden,     tpme.nombre nomtipo, 
					 estado_medida (fian.id_medida, fian.id_usuario, fian.fecha) estado,
					 tiene_tabla_medidas (fian.id_medida) tiene_tabla
				From conf_medidas medi,
				     spa_ficha_antro fian,
					 conf_tipo_medidas tpme
			   Where fian.id_medida   = medi.id_medida
			     And tpme.id_tpmedida = medi.id_tpmedida
			     And fian.id_usuario  = ".$id_usuario."
				 And fian.fecha       = ".$v_fecha_txt."
			   Order By tpme.id_tpmedida, medi.orden, fian.fecha";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function reporte_medida_usuario ($connid, $id_usuario, $id_medida) {
	$query = "Select fian.id_medida, Date_Format(fian.fecha, '%d-%m-%Y') fecha,
	                 fian.valor,     medi.nombre,    
					 medi.unidad
				From conf_medidas medi,
				     spa_ficha_antro fian
			   Where fian.id_medida   = medi.id_medida
			     And fian.id_usuario  = ".$id_usuario."
				 And fian.id_medida   = ".$id_medida."
			   Order By fian.fecha";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function fechas_medidas($connid, $id_usuario) {
	$query = "Select Distinct fian.fecha 
	            From spa_ficha_antro fian
			   Where fian.id_usuario = ".$id_usuario."
			   Order By fian.fecha";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function anotaciones_fian ($connid, $id_usuario) {
	$query = "Select anot.fecha, anot.texto
	            From spa_anotaciones anot
			   Where anot.id_usuario = ".$id_usuario."
			   Order By anot.fecha";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function require_toma_medidas($connid, $id_usuario) {
	//determinar si el usuario tiene servicios que requieren medidas
	$query = "Select count(9) conteo
	            From conf_servicios serv,
				     (Select Distinct id_servicio
					    From spa_programacion prog
					   Where id_usuario = ".$id_usuario."
					  Union 
					  Select Distinct id_servicio
					    From spa_servicios_x_usuario
					   Where id_usuario  = ".$id_usuario.") t1
			   Where serv.id_servicio  = t1.id_servicio
			     And serv.ficha_antrop = 'S'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] == 0) {
		return(false);
	}			  
	//determinar última fecha de toma de medidas
	$query = "Select Date_Format(Max(fecha), '%d-%m-%Y') fecha
	            From spa_ficha_antro
			   Where id_usuario = ".$id_usuario;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if(is_null($rset) || is_null($rset[0]['fecha'])) {
		return(true);
	} 
	$v_fecha = $rset[0]['fecha'];
	$query = "Select count(Distinct fecha) conteo
	            From spa_programacion prog,
				     conf_servicios   serv
			   Where serv.id_servicio  = prog.id_servicio
			     And serv.ficha_antrop = 'S'
				 And ( prog.asistencia   = 'S' Or
				       prog.asistencia  Is Null)
				 And prog.id_usuario   = ".$id_usuario."
				 And prog.fecha        > str_to_date('".$v_fecha."', '%d-%m-%Y')";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_sesiones = (int)$rset[0]['conteo'];
	if ($v_sesiones == 0) {
		return(false);
	}
	$query = "Select valor
	            From conf_parametros
			   Where codigo = 'FAFA'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_frec = (int)$rset[0]['valor'];
	if ( $v_sesiones >= $v_frec ) {
		return (true);
	} else {
		return (false);
	}
}
function get_genero($connid, $id_usuario) {
   $query = "Select genero
               From segu_usuarios usua
			  Where usua.id_usuario = ".$id_usuario;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]['genero']);
}
function get_ultima_fecha_medidas ($connid, $id_usuario){
	$query = "Select Max(fian.fecha) fecha 
	            From spa_ficha_antro fian
			   Where fian.id_usuario = ".$id_usuario;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if(is_array($rset)) {
	   return ($rset[0]['fecha']);
	} else {
	   return (null);
	}
}
function get_objetivo_medida($connid, $id_usuario, $id_medida, $fecha) {
	$query = "Select objetivo
	            From spa_ficha_antro fian
			   Where fian.id_usuario = ".$id_usuario."
			     And fian.id_medida  = ".$id_medida."
				 And fian.fecha      = '".$fecha."'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]['objetivo']);
}
function lista_tipos_pestana($connid){
	$query = "Select id_tipo_pestana, referencia
	            From conf_tipo_pestana
			   Order By referencia";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return($rset);
}
function detalle_pestanas_cliente($connid, $id_usuario) {
	$query = "Select valor
	            From conf_parametros
			   Where codigo = 'MNPE'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_cantidad = $rset[0]['valor'];
	
	$query = "Select id_pestana_1, id_pestana_2, id_pestana_3,
	                 date_format(fecha_postura, '%d-%m-%Y') postura,
					 date_format(fecha_ult_mantenimiento, '%d-%m-%Y') ult_mantenimiento,
					 IfNull(mantenimientos, 0) cantidad
			    From spa_pestanas
			   Where id_usuario = ".$id_usuario."
			     And ( mantenimientos is Null Or 
				       mantenimientos < ".$v_cantidad." )
			     And fecha_postura = ( Select Max(fecha_postura)
				                         From spa_pestanas
										Where id_usuario = ".$id_usuario." )";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (!is_array($rset)) {
		return(null);
	}
	return($rset[0]);
}  
?>