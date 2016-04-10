<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/facturacion_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'ajax_upd_factura.php') ) {
		$v_ahora = new DateTime();
		if(isset($_POST['p_estado'])) {
			$v_estado = $_POST['p_estado'];
			$v_id_sede = $_POST['p_id_sede'];
			$v_id_factura = $_POST['p_id_factura'];
			$v_tipo_pago = $_POST['p_tipo_pago'];
			$v_fecha = $_POST['p_fecha'].' '.$v_ahora->format('H:i');
			upd_estado_factura ($conn, $v_id_factura, $v_estado, $v_tipo_pago, $v_fecha,$v_id_sede);
		}
		elseif(isset($_POST['p_id_usuario'])){
			$v_id_usuario = $_POST['p_id_usuario'];
			$v_id_sede = $_POST['p_id_sede'];
			$v_fecha = $_POST['p_fecha'].' '.$v_ahora->format('H:i');
			$v_cajero = $_SESSION['id_usuario'];
			crea_factura($conn, $v_id_usuario, $v_fecha, $v_cajero,$v_id_sede);
		} else {
			$v_id_factura = $_POST['p_id_factura'];
			del_factura($conn, $v_id_factura);
		}
	}
}
dbdisconn($conn);
?>