<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/usuarios_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);

if(isset($_REQUEST['p_letras'])){
	$v_letras = $_REQUEST['p_letras'];
	$v_letras = preg_replace("/[^a-z0-9 ]/si","",$v_letras);
	$t_eps = buscar_eps ($conn, $v_letras);
	dbdisconn ($conn);
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header ("Pragma: no-cache"); // HTTP/1.0
	header ("Content-Type: application/json");
	echo ("{\"results\": [");
	$arr = array();
	if (is_array($t_eps)) {
		foreach($t_eps as $dato) {
			$arr[] = "{\"id\": \"".$dato['id_eps']."\", \"value\": \"".utf8_encode($dato['nombre'])."\", \"info\": \"\"}";
			//$arr[] = "{\"id\": \"Hola\", \"value\": \"Hola\", \"info\": \"\"}";
		}
		echo implode(", ", $arr);
	}
	echo ("]}");
}
?>