<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/programacion_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'myinlife.php') ) {
		$v_id_programacion = $_POST['p_id_programacion'];
		del_programacion ($conn, $v_id_programacion);
	}
}
dbdisconn($conn);
?>