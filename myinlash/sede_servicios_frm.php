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
		$v_id_sede = $_REQUEST['p_id_sede'];
		$t_servicios = lista_servicios ($conn);
		$v_editar = $_REQUEST['p_editar'];
		if ($v_editar=='S') {
			$v_id_servicio = $_REQUEST['p_id_servicio'];
			$r_sesiones= detalle_servicio_sede ($conn, $v_id_servicio, $v_id_sede);
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
  <div class="titulo">CONFIGURACI&Oacute;N DE SERVICIOS POR SEDE</div>
     <div class="capa_form_sf">
        <form id="forma" name="forma" method="post" action="exec_upd_serv_x_sede.php">
        <?php if (!is_null($v_id_sede)) { ?>
        <input type="hidden" name="p_id_sede" id="p_id_sede" value="<?php echo($v_id_sede); ?>" />
        <?php } ?>
        <table width="80%" border="0" cellpadding="0" cellspacing="0">
          <tr>
			<th>Servicio :</th>
            <td> <select name="p_id_servicio" id="p_id_servicio" onChange="refrescar();">
          <option value=""></option>
          <?php foreach($t_servicios as $dato) { ?>
            <option value="<?php echo($dato['id_servicio']); ?>" <?php if($dato['id_servicio'] == $v_id_servicio) { echo("Selected"); } ?>><?php echo($dato['nombre']); ?></option>
          <?php } ?>
          </select></td>
          </tr>
          <tr>
			<th>Sesiones Simultaneas:</th>
            <td><input type="text" name="p_sesiones_simultaneas" id="p_sesiones_simultaneas" size="30" maxlength="50" value="<?php if(!is_null($v_id_servicio)){echo($r_sesiones['sesiones']);} ?>" /></td>
          </tr>
          
          <tr>
              <td colspan="2" align="center"><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Guardar" onclick="document.forma.submit();" />
              &nbsp; <input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Regresar" onClick="javascript:document.frmback.submit();" /> </td>
          </tr>
        </table>
        </form>
     </div>
  <!-- InstanceEndEditable -->
  <form id="frmback" name="frmback" action="sede_servicios_lst.php" method="post">
      <input type="hidden" name="p_id_sede" id="p_id_sede" value="<?php echo($v_id_sede); ?>" />
      </form>
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