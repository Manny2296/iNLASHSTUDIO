<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/antropometria_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'ajax_upd_objetivo.php') ) {
		if(isset($_POST['p_id_usuario'])) {
			$v_id_usuario = $_POST['p_id_usuario'];
		} else {
			$v_id_usuario = $_SESSION['id_usuario'];
		}
		$v_fecha = $_POST['p_fecha'];
		$v_id_medida = $_POST['p_id_medida'];
		$v_objetivo = $_POST['p_objetivo'];
		
		upd_objetivo($conn, $v_id_usuario, $v_id_medida, $v_objetivo, $v_fecha);
	}
}
echo('ok');
dbdisconn($conn);
?>