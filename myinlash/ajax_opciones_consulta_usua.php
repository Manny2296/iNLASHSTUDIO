<?php
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/usuarios_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$v_tipo = $_POST['p_tipo'];
if ($v_tipo == "nombre" || $v_tipo == "id"){
?>
<input type="text" size="50" name="p_param" id="p_param" />
<?php
} else {
	$t_tipo_perfil = lista_perfil ($conn);
?>
<select name="p_param" id="p_param">
  <option value=""></option>
  <?php foreach($t_tipo_perfil as $dato) { ?>
  <option value="<?php echo($dato['id_perfil']); ?>"><?php echo($dato['nombre']); ?></option>
  <?php } ?>
</select>
<?php
}
dbdisconn ($conn);
?>
