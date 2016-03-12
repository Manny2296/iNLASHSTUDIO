<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/usuarios_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$v_id_perfil = $_POST['p_id_perfil'];
$v_id_tipoid = $_POST['p_id_tipoid'];
$v_numero_id = $_POST['p_numero_id'];

$t_existe = verificar_usuario ($conn, $v_id_tipoid, $v_numero_id, $v_id_perfil);
if ( is_null($t_existe) ){
?>
<form action="#" method="post" name="frmfake" id="frmfake">
  <input name="p_existe" type="hidden" id="p_existe" value="N">
</form>
<?php }
elseif ( $t_existe[0] == "P" ) { ?>
<form action="#" method="post" name="frmfake" id="frmfake">
  <input name="p_existe" type="hidden" id="p_existe" value="P">
</form>
<?php } 
else { 
   $v_id_usuario = $t_existe[1];
   $r_usuario = nombres_usua($conn, $v_id_usuario);
   $v_nombres = $r_usuario['nombres'];
   $v_apellidos = $r_usuario['apellidos'];
?>
<form action="#" method="post" name="frmfake" id="frmfake">
  <input name="p_existe" type="hidden" id="p_existe" value="U">
  <input name="p_nombres" type="hidden" id="p_nombres" value="<?php echo ($v_nombres); ?>">
  <input name="p_apellidos" type="hidden" id="p_apellidos" value="<?php echo ($v_apellidos); ?>">
  <input name="p_id_usuario" type="hidden" id="p_id_usuario" value="<?php echo ($v_id_usuario); ?>">
</form>
<?php }
dbdisconn($conn);
?>