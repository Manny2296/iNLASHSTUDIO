<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/facturacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);
if (isset($_SESSION['id_perfil'])) {
	$v_tipo = $_POST['p_tipo'];
	if ($v_tipo == "servicio") {
		$v_id_campo = "p_id_servicio";
		$t_lista = lista_servicios($conn);
	} else {
		$v_id_campo = "p_id_producto";
		$t_lista = lista_productos($conn);
	}	
}
dbdisconn($conn);
?>
<select id="<?php echo($v_id_campo); ?>" name="<?php echo($v_id_campo); ?>" onchange="setTimeout('getPrecio();', 0);">
<?php if($v_tipo == "producto") {
	if(is_array($t_lista)) {
		foreach($t_lista as $dato){
?>
	<option value="<?php echo($dato['id_producto']); ?>"><?php echo(utf8_encode($dato['nombre'])); ?></option>
<?php	} } 
	} else { 
	if(is_array($t_lista)) {
		foreach($t_lista as $dato){
?>
	<option value="<?php echo($dato['id_servicio']); ?>"><?php echo(utf8_encode($dato['nombre'])); ?></option>
<?php } } } ?>
</select>