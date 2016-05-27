<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/programacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'programacion_asistencia_frm.php') ) {
		$v_id_programacion = $_REQUEST['p_id_programacion'];
		$r_prog = detalle_programacion($conn, $v_id_programacion);
		$v_mantenimiento = req_mantenimiento($conn, $v_id_programacion);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Programaci&oacute;n de sesiones</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="sub_tit">SESI&Oacute;N PROGRAMADA</div>
     <div class="capa_form_sf">
        <form id="forma" name="forma" method="post" action="exec_upd_asistencia.php">
        <input type="hidden" name="p_id_usuario" id="p_id_usuario" value="<?php echo($r_prog['id_usuario']); ?>" />
        <input type="hidden" name="p_id_programacion" id="p_id_programacion" value="<?php echo($v_id_programacion); ?>" />
        <input type="hidden" name="p_fecha" id="p_fecha" value="<?php echo($r_prog['fecha']); ?>" />
        <?php if ($v_mantenimiento) { ?>
		<input type="hidden" name="p_es_mantenimiento" id="p_es_mantenimiento" value="S" />
        <?php } else { ?>
        <input type="hidden" name="p_es_mantenimiento" id="p_es_mantenimiento" value="N" />
        <?php } ?>
        <table width="90%" border="0" cellpadding="0" cellspacing="0">
          <tr>
			<th>Cliente:</th>
            <td><?php echo($r_prog['nomcliente']); ?></td>
          </tr>
               <tr>
      <th>Telefono:</th>
            <td><?php echo($r_prog['celular']); ?></td>
          </tr>
           <tr>
      <th>Sede:</th>
            <td><?php echo($r_prog['snom']); ?></td>
          </tr>
          <tr>
			<th>Servicio programado:</th>
            <td><?php echo($r_prog['nombre']); ?></td>
          </tr>
          <tr>
			<th>Fecha:</th>
            <td><?php echo($r_prog['fecha']); ?></td>
          </tr>
          <tr>
			<th>Hora de inicio:</th>
            <td><?php echo($r_prog['hora_ini']); ?></td>
          </tr>
          <tr>
			<th>Hora de finalizaci&oacute;n:</th>
            <td><?php echo($r_prog['hora_fin']); ?></td>
          </tr>
          <tr>
			<th>Sesi&oacute;n de cortes&iacute;a:</th>
            <td><?php if($r_prog['cortesia'] == "S") { echo('Si'); } else { echo('No'); } ?></td>
          </tr>
          <tr>
			<th>Comentarios:</th>
            <td><?php echo($r_prog['comentarios']); ?></td>
          </tr>
          <?php if ($v_mantenimiento) { ?>
          <tr>
			<th>Completar Mantenimientos:</th>
            <td><input type="checkbox" name="p_completar_mant" id="p_completar_mant" value="S" /></td>
          </tr>
          <?php } ?>
          <tr>
			<th>Fall&oacute; a la Cita:</th>
            <td><input type="checkbox" name="p_asistencia" id="p_asistencia" value="N" <?php if($r_prog['asistencia'] == 'N') {echo("Checked"); } ?> /></td>
          </tr>
          <tr>
            <td colspan="2" align="center"><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Actualizar" onClick="document.forma.submit();" />
              &nbsp; <input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Cerrar" onClick="javascript:top.GB_hide();" /> </td>
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