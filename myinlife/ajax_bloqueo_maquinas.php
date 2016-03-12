<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/programacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$v_id_servicio = $_POST['p_id_servicio'];
$v_fecha = $_POST['p_fecha'];
$v_maquina = $_POST['p_maquina'];
$v_hora_ini = $_POST['p_hora_ini'];
$v_hora_fin = $_POST['p_hora_fin'];
$t_maquinas = list_estacion_sin_bloqueo($conn, $v_id_servicio, $v_fecha, $v_hora_ini, $v_hora_fin); 
dbdisconn($conn);

if (is_array($t_maquinas) && count($t_maquinas) > 1) {
	?>
    <table width="90%" border="0" cellpadding="0" cellspacing="0">
    <tr>
		<th width="45%" valign="top">Bloquear las siguientes estaciones:</th>
        <td><select name="p_maquinas[]" id="p_maquinas" size="4" multiple>
     <?php
	foreach($t_maquinas as $dato){
		if($dato != $v_maquina) {
	?>
	          <option value="<?php echo($dato); ?>"><?php echo("Estaci&oacute;n ".$dato); ?></option>
    <?php
		}
	}
	?>
	       </select></td>
   </tr>
   </table>
<?php
}
?>