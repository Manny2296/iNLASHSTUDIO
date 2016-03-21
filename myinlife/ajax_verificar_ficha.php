<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/programacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$v_id_usuario = $_POST['p_id_usuario'];
$v_fecha = $_POST['p_fecha'];
$v_id_servicio = $_POST['p_id_servicio'];

$v_medidas = req_toma_medidas_usua ($conn, $v_id_usuario, $v_id_servicio, $v_fecha);
if ($v_medidas == "S") {
?>
<table width="90%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <th width="45%">Esta sesi&oacute;n incluye una toma de medidas, desea programarla?</th>
    <td><input type="checkbox" name="p_sesion_especial" id="p_sesion_especial" value="S" /></td>
  </tr>
</table>
<?php
}
$v_mantenimientos = req_mantenimiento_usua($conn, $v_id_servicio, $v_id_usuario);
if ($v_mantenimientos == "S") {
?>
<table width="90%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <th width="45%">Esta sesi&oacute;n es un mantenimiento de pesta&ntilde;as. Desea convertirla en un tratamiento nuevo?</th>
    <td><input type="checkbox" name="p_sesion_especial" id="p_sesion_especial" value="S" /></td>
  </tr>
</table>
<?php
}

dbdisconn($conn);
?>