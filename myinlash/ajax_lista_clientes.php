<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/usuarios_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
if(isset($_REQUEST['p_letras'])){
	$v_letras = $_REQUEST['p_letras'];
	$v_id_sede = $_REQUEST['p_id_sede'];
	$v_letras = preg_replace("/[^a-z0-9 ]/si","",$v_letras);
	$t_usuarios = lista_clientes ($conn, 'nombre', $v_letras,$v_id_sede);
	//$telefono = cliente_telefono ($conn, 'nombre',)
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header ("Pragma: no-cache"); // HTTP/1.0
	header ("Content-Type: application/json");
	
	echo ("{\"results\": [");
	$arr = array();
	if (is_array($t_usuarios)) {
		foreach($t_usuarios as $dato) {
			$arr[] = "{\"id\": \"".$dato['id_usuario']."\", \"value\": \"".utf8_encode($dato['nombres']." ".$dato['apellidos'])."\", \"info\": \"".$dato['celular']."\"}";
		     
		}
		echo implode(", ", $arr);
	}
	echo ("]}");
}
?>