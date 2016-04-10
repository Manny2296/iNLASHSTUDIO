<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/facturacion_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);
if (isset($_SESSION['id_perfil'])) {
	$v_id_factura = $_POST['p_id_factura'];
	$v_id_sede = $_POST['p_id_sede'];
	$v_cantidad = $_POST['p_cantidad'];
	$v_pordto = $_POST['p_pordto'];
	$v_tipo = $_POST['p_tipo'];
	$v_valor_unitario = $_POST['p_valor_unitario'];
	
	if ($v_tipo == "servicio"){
		$v_id_servicio = $_POST['p_valor'];
		$v_id_producto = null;
	} else {
		$v_id_producto = $_POST['p_valor'];
		$v_id_servicio = null;
	}
	
	crea_detalle($conn, $v_id_factura, $v_id_servicio, $v_id_producto, $v_cantidad, $v_pordto, $v_valor_unitario);
	calcular_totales($conn, $v_id_factura);
}
dbdisconn($conn);
