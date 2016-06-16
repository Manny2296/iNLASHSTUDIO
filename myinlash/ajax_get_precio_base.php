<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/facturacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
if(isset($_POST['p_id_servicio'])){
	$v_id_servicio = $_POST['p_id_servicio'];
	$v_precio = getPrecioBase($conn, "servicio", $v_id_servicio);
} else {
	$v_id_producto = $_POST['p_id_producto'];
	$v_precio = getPrecioBase($conn, "producto", $v_id_producto);
}
dbdisconn($conn);
echo($v_precio);
?>