<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/sedes_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$v_nombre = $_POST['p_nombre'];

$t_existe = verificar_sede ($conn, $v_nombre);
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
   $v_id_sede = $t_existe[1];
   $r_sede = detalle_sede($conn, $v_id_sede);
   $v_nombre = $r_sede['nombre'];
   
?>
<form action="#" method="post" name="frmfake" id="frmfake">
  <input name="p_existe" type="hidden" id="p_existe" value="U">
  <input name="p_nombre" type="hidden" id="p_nombre" value="<?php echo ($v_nombre); ?>">
  <input name="p_id_sede" type="hidden" id="p_id_sede" value="<?php echo ($v_id_sede); ?>">

</form>
<?php }
dbdisconn($conn);
?>