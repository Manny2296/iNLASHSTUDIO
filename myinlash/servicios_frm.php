<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/servicios_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
  if ( validar_permisos ($conn, 'servicios_frm.php') ) {
    $v_id_servicio = null;
    if (isset($_REQUEST['p_id_servicio'])) {
      $v_id_servicio = $_REQUEST['p_id_servicio'];
      $r_servicio = detalle_servicio ($conn, $v_id_servicio);
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Creaci&oacute;n / Modificaci&oacute;n de servicios</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="titulo">CONFIGURACI&Oacute;N DE SERVICIOS</div>
     <div class="capa_form_sf">
        <form id="forma" name="forma" method="post" action="exec_upd_servicios.php">
        <?php if (!is_null($v_id_servicio)) { ?>
        <input type="hidden" name="p_id_servicio" id="p_id_servicio" value="<?php echo($v_id_servicio); ?>" />
        <?php } ?>
        <table width="80%" border="0" cellpadding="0" cellspacing="0">
          <tr>
      <th>Nombre:</th>
            <td><input type="text" name="p_nombre" id="p_nombre" size="30" maxlength="50" value="<?php if(!is_null($v_id_servicio)){echo($r_servicio['nombre']);} ?>" /></td>
          </tr>
          <tr>
      <th>Descripci&oacute;n:</th>
            <td><textarea name="p_descripcion" id="p_descripcion" rows="4" cols="40"><?php if(!is_null($v_id_servicio)){echo($r_servicio['descripcion']);} ?></textarea></td>
          </tr>
          <tr>
      <th>Precio Base:</th>
            <td>$ <input type="text" name="p_precio_base" id="p_precio_base" size="8" maxlength="10" value="<?php if(!is_null($v_id_servicio)){echo($r_servicio['precio_base']);} ?>" /></td>
          </tr>
          <tr>
      <th>Impuestos:</th>
            <td><input type="text" name="p_impuesto" id="p_impuesto" size="5" maxlength="5" value="<?php if(!is_null($v_id_servicio)){echo($r_servicio['impuesto']);} ?>" /></td>
          </tr>
        </table>
        <div class="sub_tit">CARACTER&Iacute;STICAS DEL SERVICIO</div>
        <table width="80%" border="0" cellpadding="0" cellspacing="0">
          <tr>
      <th>Requiere Prepago:</th>
            <td><select name="p_prepagado" id="p_prepagado">
              <option value=""></option>
              <option value="S" <?php if (!is_null($v_id_servicio) && $r_servicio['prepagado'] == "S") {echo("Selected"); } ?>>S</option>
              <option value="N" <?php if (!is_null($v_id_servicio) && $r_servicio['prepagado'] == "N") {echo("Selected"); } ?>>N</option>
              </select></td>
          </tr>
          <!-- Modulo agendamiento por clien
               se deja depreciado a peticion del cliente
          <tr>
      <th>Puede ser agendado directamente por el cliente:</th>
            <td><select name="p_programable" id="p_programable">
              <option value=""></option>
              <option value="S" <?php //if (!is_null($v_id_servicio) && $r_servicio['programable'] == "S") {echo("Selected"); } ?>>S</option>
              <option value="N" <?php //if (!is_null($v_id_servicio) && $r_servicio['programable'] == "N") {echo("Selected"); } ?>>N</option>
              </select></td>
          </tr>-->
          <!-- Modulo se deja depreciado a peticion del cliente. 
          <tr>
      <th>Requiere Ficha Antropom&eacute;trica:</th>
            <td><select name="p_ficha_antrop" id="p_ficha_antrop">
              <option value=""></option>
              <option value="S" <?php// if (!is_null($v_id_servicio) && $r_servicio['ficha_antrop'] == "S") {echo("Selected"); } ?>>S</option>
              <option value="N" <?php// if (!is_null($v_id_servicio) && $r_servicio['ficha_antrop'] == "N") {echo("Selected"); } ?>>N</option>
              </select></td>
          </tr>-->
        <tr>
      <th>Duraci&oacute;n m&iacute;nima de una sesi&oacute;n (minutos):</th>
            <td><input type="text" name="p_sesion_minima" id="p_sesion_minima" size="4" maxlength="4" value="<?php if(!is_null($v_id_servicio)){echo($r_servicio['sesion_minima']);} ?>" /></td>
          </tr>
          <!--
          <tr>
      <th>Asociado al m&oacute;dulo de pesta&ntilde;as:</th>
            <td><select name="p_pestanas" id="p_pestanas">
              <option value=""></option>
              <option value="S" <?php//if (!is_null($v_id_servicio) && $r_servicio['modulo_pestanas'] // "S") {echo("Selected"); } ?>>S</option>
              <option value="N" <?php// if (!is_null($v_id_servicio) && $r_servicio['modulo_pestanas'] == "N") {echo("Selected"); } ?>>N</option>
              </select></td>
          </tr>-->
          <!-- Modulo se deja depreciado a peticion del cliente. 
          <tr>
      <th>D&iacute;as antes del vencimiento para notificar al cliente:</th>
            <td><input type="text" name="p_dias_venc" id="p_dias_venc" size="4" maxlength="4" value="<?php//f(!is_null($v_id_servicio)){echo($r_servicio['dias_vencimiento']);} ?>" /></td>
          </tr>-->
          <tr>
      <th>D&iacute;as requeridos para agendar un mantenimiento:</th>
            <td><input type="text" name="p_dias_mant" id="p_dias_mant" size="4" maxlength="4" value="<?php if(!is_null($v_id_servicio)){echo($r_servicio['dias_mantenimiento']);} ?>" /></td>
          </tr>
          <tr>
              <td colspan="2" align="center"><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Guardar" onclick="document.forma.submit();" />
              &nbsp; <input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Regresar" onclick="javascript:top.GB_hide();" /> </td>
          </tr>
        </table>
        </form>
     </div>
  <!-- InstanceEndEditable -->
  </div>
</body>
<!-- InstanceEnd --></html>
<?php 
  }
  else {
    mensaje(2, 'Usted no tiene permisos para acceder esta opci&oacute;n', 'javascript:history.go(-1);', '_self');
  }      
} else {
  mensaje(2, 'Su sesi&oacute;n no est&aacute; activa.<br>Por favor ingrese al sistema nuevamente', $url_login, '_parent');
}
dbdisconn ($conn);
?>