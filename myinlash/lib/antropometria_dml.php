<?php
/* 
 Libreria de servicios DML para la administración de la ficha antrometrica del usuario
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca01@gmail.com
 Desarrollo: Dev Manuel Felipe S.R 
 			manuel.sanchez-r@mail.escuelaing.edu.co
 Fecha     : 10/02/2010 08:40 a.m.
 Version   : 1.0
*/
function crea_medida ($connid,  $id_tpmedida, $nombre, $calculable, 
					  $formula, $unidad){
	$query = "Select ifNull(max(orden), 0)+1 orden
	            From conf_medidas";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_orden = $rset[0]['orden'];
	
	$query = "Insert into conf_medidas
	             (id_tpmedida, nombre, calculable,
				  formula,     unidad, orden)
				 Values
				 (".$id_tpmedida.", '".$nombre."', '".$calculable."',
				  '".$formula."',   '".$unidad."', ".$orden.")";
	$result = dbquery ($query, $connid);
	return (true);
}
function upd_medida ($connid, $id_medida,  $id_tpmedida, 
					 $nombre, $calculable, $formula,
					 $unidad){
	$query = "Update conf_medidas medi
	             Set medi.id_tpmedida = ".$id_tpmedida.", medi.nombre = '".$nombre."',
				     medi.calculable = '".$calculable."', medi.formula = '".$formula."',
					 medi.unidad = '".$unidad."'
			   Where medi.id_medida = ".$id_medida;
	$result = dbquery ($query, $connid);
	return (true);
}
function del_medida ($connid, $id_medida) {
	$query = "Delete from tabla_medidas
	           Where id_medida = ".$id_medida;
	$result = dbquery ($query, $connid);
	
	$query = "Delete from spa_ficha_antro
	           Where id_medida = ".$id_medida;
	$result = dbquery ($query, $connid);
	
	$query = "Delete from conf_medidas
	           Where id_medida = ".$id_medida;
	$result = dbquery ($query, $connid);
	return (true);
}
function crea_tabla_medida ($connid,    $id_medida,      $rango_min, 
							$rango_max, $interpretacion, $anotaciones ) {
	$query = "Select count(9) conteo
	            From conf_tabla_medidas tame
			   Where tame.id_medida = ".$id_medida."
			     And ( ".$rango_min." Between tame.rango_min And tame.rango_max Or
					   ".$rango_max." Between tame.rango_min And tame.rango_max )";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return (false);
	}
	$query = "Insert Into conf_tabla_medidas
	             (id_medida,      rango_min, rango_max,
				  interpretacion, anotaciones)
				 Values
				 (".$id_medida.",        ".$rango_min.", ".$rango_max.",
				  '".$interpretacion."', '".anotaciones."')";
	$result = dbquery ($query, $connid);
	return (true);
}
function upd_orden_medida ($connid, $id_medida, $orden){
	$query = "Update conf_medidas medi
	             Set medi.orden = ".$orden."
			   Where medi.id_medida = ".$id_medida;
	$result = dbquery ($query, $connid);
	return (true);
}
function upd_tabla_medidas ($connid,    $id_det_medida, $id_medida,
							$rango_min, $rango_max,     $interpretacion,
							$anotaciones ) {
	$query = "Select count(9) conteo
	            From conf_tabla_medidas tame
			   Where tame.id_medida      = ".$id_medida."
			     And tame.id_det_medida != ".$id_det_medida."
			     And ( ".$rango_min." Between tame.rango_min And tame.rango_max Or
					   ".$rango_max." Between tame.rango_min And tame.rango_max )";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return (false);
	}
	$query = "Update conf_tabla_medidas tame
	             Set tame.rango_min = ".$rango_min.", tame.rango_max = ".$rango_max.",
				     tame.interpretacion = '".$interpretacion."', tame.anotaciones = '".anotaciones."'
			   Where tame.id_det_medida = ".$id_det_medida;
	$result = dbquery ($query, $connid);
	return (true);
}
function del_tabla_medidas ($connid, $id_det_medida) {
	$query = "Delete From conf_tabla_medidas
	           Where id_det_medida = ".$id_det_medida;
	$result = dbquery ($query, $connid);
	return (true);
}
function upd_ficha_antrop($connid, $id_usuario, $id_medida, $fecha, $valor, $objetivo) {
	if ($objetivo == "" || is_null($objetivo)) {
		$objetivo = 'Null';
	} else {
		$objetivo = "'".$objetivo."'";
	}
	$query = "Select count(9) conteo
	            From spa_ficha_antro fian
			  Where fian.id_usuario = ".$id_usuario."
			    And fian.id_medida  = ".$id_medida."
				And fian.fecha 		= str_to_date('".$fecha."', '%d-%m-%Y')";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	
	if ($rset[0]['conteo'] == 0 ) {
		$query = "Insert Into spa_ficha_antro 
		             (id_usuario, id_medida, fecha,
					  valor,	  objetivo)
					 Values
					 (".$id_usuario.", ".$id_medida.", str_to_date ('".$fecha."', '%d-%m-%Y'),
					  ".$valor.", ".$objetivo.")";
	} else {
		$query = "Update spa_ficha_antro fian
		             Set fian.valor = ".$valor.", fian.objetivo = ".$objetivo."
				   Where fian.id_usuario = ".$id_usuario."
					 And fian.id_medida  = ".$id_medida."
					 And fian.fecha      = str_to_date('".$fecha."', '%d-%m-%Y')";
	}
	$result = dbquery ($query, $connid);
	return (true);
}

function del_ficha_antro($connid, $id_usuario, $fecha) {
	$query = "Delete From spa_ficha_antro 
	           Where id_usuario = ".$id_usuario."
			     And fecha = str_to_date('".$fecha."', '%d-%m-%Y')";
	$result = dbquery ($query, $connid);
	return (true);
}
function crea_observacion ($connid, $id_usuario, $fecha, $texto, $login_mod){
	if (!is_null($texto) && strlen($texto) > 2) {
		$query = "Insert Into spa_anotaciones (id_usuario, fecha, texto, login_mod)
		             Values(".$id_usuario.", str_to_date('". $fecha."', '%d-%m-%Y'), '".$texto."', '".$login_mod."')";
		$result = dbquery ($query, $connid);
	}
	return(true);
}

function del_observacion ($connid, $id_usuario, $fecha) {
	$query = "Delete From spa_anotaciones
               Where id_usuario = ".$id_usuario."
			     And fecha = str_to_date('".$fecha."', '%d-%m-%Y')";
	$result = dbquery ($query, $connid);
	return (true);
}

function upd_objetivo($connid, $id_usuario, $id_medida, $objetivo, $fecha) {
	if ($objetivo == "I") {
	    $query = "Update spa_ficha_antro
	                 Set objetivo = null
		 		   Where id_usuario = ".$id_usuario."
				     And id_medida  = ".$id_medida."
					 And fecha      = str_to_date('".$fecha."', '%d-%m-%Y')";
	} else {
		$query = "Update spa_ficha_antro
	                 Set objetivo = '".$objetivo."'
				   Where id_usuario = ".$id_usuario."
				     And id_medida  = ".$id_medida."
					 And fecha      = str_to_date('".$fecha."', '%d-%m-%Y')";
	}
	$result = dbquery ($query, $connid);
	return(true);
}
function upd_pestanas ($connid, $id_usuario, $id_pestana_1, 
					   $id_pestana_2, $id_pestana_3, $fecha_postura) {
	//obtener el número de mantenimientos configurados
	$query = "Select valor
	            From conf_parametros
			   Where codigo = 'MNPE'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_cantidad = $rset[0]['valor'];
	//verificar si hay mantenimiento de pestañas para el cliente y obtener fecha de postura
	$query = "Select Max(fecha_postura) fecha_postura
	            From spa_pestanas
			   Where id_usuario     = ".$id_usuario."
			     And mantenimientos < ".$v_cantidad;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (is_array($rset) && !is_null($rset[0]['fecha_postura'])) {
		$query = "Update spa_pestanas
		             Set id_pestana_1 = ".$id_pestana_1.", id_pestana_2 = ".$id_pestana_2.",
					     id_pestana_3 = ".$id_pestana_3.", fecha_postura = str_to_date('".$fecha_postura."', '%d-%m-%Y')
				   Where id_usuario = ".$id_usuario."
				     And fecha_postura = '".$rset[0]['fecha_postura']."'";
	} else {
		$query = "Select count(9) conteo
		            From spa_pestanas
				   Where id_usuario = ".$id_usuario."
				     And fecha_postura = str_to_date('".$fecha_postura."', '%d-%m-%Y')";
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		if($rset[0]['conteo'] > 0) {
			return(false);
		}
		$query = "Insert Into spa_pestanas 
		             (id_pestana_1, id_pestana_2, id_pestana_3,
					  fecha_postura, id_usuario)
					 Values
					 (".$id_pestana_1.", ".$id_pestana_2.", ".$id_pestana_3.",
					  str_to_date('".$fecha_postura."', '%d-%m-%Y'), ".$id_usuario.")";
	}
	$result = dbquery ($query, $connid);
    return(true);
}
?>