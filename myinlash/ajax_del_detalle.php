<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/facturacion_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);
if (isset($_SESSION['id_perfil'])) {
	$v_id_detalle = $_POST['p_id_detalle'];
	$v_id_factura = $_POST['p_id_factura'];
	
	del_detalle($conn, $v_id_detalle);
	calcular_totales($conn, $v_id_factura);
}
dbdisconn($conn);
?>
<select id="<?php echo($v_id_campo); ?>" name="<?php echo($v_id_campo); ?>">
<?php if($v_tipo == "producto") {
	if(is_array($t_lista)) {
		foreach($t_lista as $dato){
?>
	<option value="<?php echo($dato['id_producto']); ?>"><?php echo($dato['nombre']); ?></option>
<?php	} } 
	} else { 
	if(is_array($t_lista)) {
		foreach($t_lista as $dato){
?>
	<option value="<?php echo($dato['id_servicio']); ?>"><?php echo($dato['nombre']); ?></option>
<?php } } } ?>
</select>