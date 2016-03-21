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
function lista_servicios_x_sede ($connid, $id_sede)
{
	$query = "Select serv.id_servicio, serv.nombre, serv.descripcion, csps.id_sede,
					 csps.id_servicio, csps.sesiones_simultaneas, sede.id_sede sidsede, sede.nombre nomsede
	            From conf_servicios serv, 
	            	 conf_servicios_x_sede csps,
	            	 conf_sedes sede
	            Where serv.id_servicio = csps.id_servicio
	            And   sede.id_sede = csps.id_sede
	            And   sede.id_sede = ".$id_sede."
			   Order By nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function lista_servicios_prep ($connid, $id_usuario, $id_sede){
	$query = "Select serv.id_servicio, serv.nombre, serv.descripcion,
				csps.id_sede, csps.id_servicio, sede.nombre nomsede,spxu.id_perf_unico,spxu.id_sede
	            From conf_servicios serv,
	            	 conf_servicios_x_sede csps,
	            	 conf_sedes sede,
	            	 segu_perfil_x_usuario spxu
			   Where serv.prepagado = 'S'
			   	 And serv.id_servicio = csps.id_servicio
	             And sede.id_sede = csps.id_sede
	             And spxu.id_perf_unico = ".$id_usuario."
	             And sede.id_sede = spxu.id_sede
	             And sede.id_sede = ".$id_sede."	 
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
function detalle_servicio_sede ($connid, $id_servicio, $id_sede) {
	$query = "Select serv.*, csps.sesiones_simultaneas sesiones
			   from conf_servicios serv, 
			   		conf_servicios_x_sede csps
	           Where serv.id_servicio = ".$id_servicio."
	           And   csps.id_servicio = serv.id_servicio
	           And   csps.id_sede     = ".$id_sede;
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