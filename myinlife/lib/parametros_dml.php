<?php 
/* 
 Libreria de servicios DML para la actualización de parámetros del sistema
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca01@gmail.com
 Fecha     : 04/11/2010 04:40 p.m.
 Version   : 1.0
*/
function upd_parametro($connid, $id_parametro, $valor){
	if (!is_null($valor) || strlen($valor) > 0) {
		$query = "Update conf_parametros para
		             Set para.valor = '".$valor."'
				   Where para.id_parametro = ".$id_parametro;
	} else {
		$query = "Update conf_parametros para
		             Set para.valor = null
				   Where para.id_parametro = ".$id_parametro;
	}
	$result = dbquery ($query, $connid);
	return (true);
}
?>