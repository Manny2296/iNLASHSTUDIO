<?php
/* 
 Libreria de utilidades para la presentaciσn de informaciσn relacionada con servicios ofrecidos por la compaρνa
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca01@gmail.com
 Fecha     : 05/11/2010 11:40 a.m.
 Version   : 1.0
*/
function lista_servicios ($connid){
	$query = "Select id_servicio, nombre, descripcion
	            From conf_servicios serv
			   Order By nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function lista_servicios_prep ($connid, $id_usuario){
	$query = "Select id_servicio, nombre, descripcion
	            From conf_servicios serv
			   Where serv.prepagado = 'S'
			     And sesiones_disp(serv.id_servicio, ".$id_usuario.", curdate()) >= 0
			   Order By nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function detalle_servicio ($connid, $id_servicio) {
	$query = "Select * from conf_servicios serv
	           Where serv.id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}
function rest_horaria ($connid, $id_servicio) {
	$query = "Select reho.dia, reho.hora_inicio, reho.hora_final
	            From spa_resthoraria reho
			   Where reho.id_servicio  = ".$id_servicio."
			   Order By reho.hora_inicio, reho.dia";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function lista_servicios_cliente ($connid, $id_usuario) {
	$query = "Select svus.id_servicio, serv.nombre, svus.fecha,
	                 svus.cantidad,    svus.continuidad, 
					 sesiones_disp(svus.id_servicio, svus.id_usuario, svus.fecha) restantes,
					 svus.congelar,    svus.caducidad
				From spa_servicios_x_usuario svus,
				     conf_servicios          serv
			   Where svus.id_servicio = serv.id_servicio
			     And svus.id_usuario  = ".$id_usuario."
			   Order by svus.fecha, serv.nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function detalle_servicio_cliente ($connid, $id_usuario, $id_servicio, $fecha) {
	$query = "Select serv.nombre,   date_format(svus.fecha, '%d-%m-%Y') fecha,
	                 svus.cantidad, svus.continuidad, 
					 sesiones_disp(svus.id_servicio, svus.id_usuario, svus.fecha) restantes,
					 svus.congelar, date_format(svus.caducidad, '%d-%m-%Y') caducidad
				From spa_servicios_x_usuario svus,
				     conf_servicios          serv
			   Where svus.id_servicio = serv.id_servicio
			     And svus.id_usuario  = ".$id_usuario."
				 And svus.id_servicio = ".$id_servicio."
				 And svus.fecha       = str_to_date('".$fecha."', '%d-%m-%Y')";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}
?>