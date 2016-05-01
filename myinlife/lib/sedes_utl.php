<?php
function lista_sedes ($connid, $v_activa) {
	$query = "Select sede.*
	            From conf_sedes sede
	            
			   Order By sede.nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function sede_admin ($connid, $v_id_sede) {
	$query = "Select sede.*
	            From conf_sedes sede
	            where id_sede = ".$v_id_sede."
			   Order By sede.nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function detalle_sede ($connid, $id_sede){
	$query = "Select sede.* 
			  from conf_sedes sede
	           Where sede.id_sede = ".$id_sede;

	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}
function verificar_sede ($connid, $nombre) {
	$query = "Select nombre, id_sede
	            From conf_sedes sede
			   Where sede.nombre like '%".$nombre."%'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (!is_array($rset)) {
		return (null);
	} else {
		$resultado[0] = 'U';
		$resultado[1] = $rset[0]['id_sede'];
		
		$query = "Select count(9) conteo
		            From conf_sedes sede
		           Where sede.id_sede = ".$resultado[1]."
				     And sede.Activa  = 'S' ";
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		if ($rset[0]['conteo'] > 0) {
			$resultado[0] = 'P';
		}
		return ($resultado);
	}
}
?>