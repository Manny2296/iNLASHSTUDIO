<?php
function lista_sedes ($connid) {
	$query = "Select sede.id_sede, sede.nombre
	            From conf_sedes sede
			   Order By sede.nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
?>