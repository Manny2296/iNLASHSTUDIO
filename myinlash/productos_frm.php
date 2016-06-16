<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/facturacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'productos_frm.php') ) {
		$v_id_producto = null;
		if (isset($_REQUEST['p_id_producto'])) {
			$v_id_producto = $_REQUEST['p_id_producto'];
			$r_producto = detalle_producto ($conn, $v_id_producto);
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Creaci&oacute;n / Modificaci&oacute;n de productos</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="titulo">CONFIGURACI&Oacute;N DE PRODUCTOS</div>
     <div class="capa_form_sf">
        <form id="forma" name="forma" method="post" action="exec_upd_productos.php">
        <?php if (!is_null($v_id_producto)) { ?>
        <input type="hidden" name="p_id_producto" id="p_id_producto" value="<?php echo($v_id_producto); ?>" />
        <?php } ?>
        <table width="80%" border="0" cellpadding="0" cellspacing="0">
          <tr>
			<th>Referencia:</th>
            <td><input type="text" name="p_referencia" id="p_referencia" size="30" maxlength="50" value="<?php if(!is_null($v_id_producto)){echo($r_producto['referencia']);} ?>" /></td>
          </tr>
          <tr>
			<th>Nombre:</th>
            <td><input type="text" name="p_nombre" id="p_nombre" size="30" maxlength="50" value="<?php if(!is_null($v_id_producto)){echo($r_producto['nombre']);} ?>" /></td>
          </tr>
          <tr>
			<th>Precio Base:</th>
            <td>$ <input type="text" name="p_valor" id="p_valor" size="8" maxlength="10" value="<?php if(!is_null($v_id_producto)){echo($r_producto['valor']);} ?>" /></td>
          </tr>
          <tr>
			<th>Impuestos:</th>
            <td><input type="text" name="p_iva" id="p_iva" size="5" maxlength="5" value="<?php if(!is_null($v_id_producto)){echo($r_producto['iva']);} ?>" /></td>
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