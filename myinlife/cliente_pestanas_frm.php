<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/usuarios_utl.php");
include ($path."/lib/antropometria_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'cliente_pestanas_frm.php') ) {
		$v_id_usuario = $_REQUEST['p_id_usuario'];
		$r_usuario = nombres_usua($conn, $v_id_usuario);
		$v_nombre = $r_usuario['nombres']." ".$r_usuario['apellidos'];
		$t_tipos_pestana = lista_tipos_pestana($conn);
		$r_pestanas_cliente = detalle_pestanas_cliente($conn, $v_id_usuario);
		$v_editar = true;
		if (!is_null($r_pestanas_cliente)) {
			$v_pestana_1 = $r_pestanas_cliente['id_pestana_1'];
			$v_pestana_2 = $r_pestanas_cliente['id_pestana_2'];
			$v_pestana_3 = $r_pestanas_cliente['id_pestana_3'];
		} else {
			$v_pestana_1 = null;
			$v_pestana_2 = null;
			$v_pestana_3 = null;
		}
?>
<html><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Colocaci&oacute;n de pesta&ntilde;as</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" language="javascript" src="lib/popcalendar.js"></script>
<script type="text/javascript" language="javascript">
function refrescar(){
	document.frmrefresh.submit();
	return;
}
</script>
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="sub_tit">TRATAMIENTO DE PESTA&Ntilde;AS PARA <?php echo(strtoupper($v_nombre)); ?></div>
    <div id="contiene_tabla">
      <form action="exec_upd_pestanas.php" method="post" name="forma" id="forma" class="formato">
        <input type="hidden" name="p_id_usuario" id="p_id_usuario" value="<?php echo($v_id_usuario); ?>" />
        <div align="center">
        <table border="0" cellpadding="2" cellspacing="10" align="center">
          <tr>
            <td id="ojo_img">
            <div id="ojo_izq"><select name="p_id_pestana_1" id="p_id_pestana_1">
                  <option value=""></option>
                  <?php foreach($t_tipos_pestana as $dato){ ?>
                  <option value="<?php echo($dato['id_tipo_pestana']); ?>" <?php if(!is_null($v_pestana_1) && $v_pestana_1 == $dato['id_tipo_pestana']) { echo("Selected"); } ?>><?php echo($dato['referencia']); ?></option>
                  <?php } ?>
                </select></div>
              <div id="ojo_centro"><select name="p_id_pestana_2" id="p_id_pestana_2">
                  <option value=""></option>
                  <?php foreach($t_tipos_pestana as $dato){ ?>
                  <option value="<?php echo($dato['id_tipo_pestana']); ?>" <?php if(!is_null($v_pestana_2) && $v_pestana_2 == $dato['id_tipo_pestana']) { echo("Selected"); } ?>><?php echo($dato['referencia']); ?></option>
                  <?php } ?>
                </select></div>
              <div id="ojo_der"><select name="p_id_pestana_3" id="p_id_pestana_3">
                  <option value=""></option>
                  <?php foreach($t_tipos_pestana as $dato){ ?>
                  <option value="<?php echo($dato['id_tipo_pestana']); ?>" <?php if(!is_null($v_pestana_3) && $v_pestana_3 == $dato['id_tipo_pestana']) { echo("Selected"); } ?>><?php echo($dato['referencia']); ?></option>
                  <?php } ?>
                </select></div>
            </td>
          </tr>
          </table>
          <table border="1" width="500" cellpadding="5" cellspacing="5" class="t_header">
          <tr>
            <th>Fecha de postura:</th>
            <td><?php if($v_editar) { ?><input type="text" id="p_fecha_postura" name="p_fecha_postura" maxlength="12" size="12" readonly="readonly" onClick="popUpCalendar(this, forma.p_fecha_postura);" value="<?php echo($r_pestanas_cliente['postura']); ?>" /> <?php } else { echo($r_pestanas_cliente['postura']); } ?></td>
          </tr> 
          <tr>
            <th class="t_header">Mantenimientos realizados:</th>
            <td class="t_texto"><?php echo($r_pestanas_cliente['cantidad']); ?></td>
          </tr>
          <tr>
            <th class="t_header">Fecha de &uacute;ltimo mantenimiento:</th>
            <td class="t_texto"><?php echo($r_pestanas_cliente['ult_mantenimiento']); ?></td>
          </tr>
          <tr>
              <td colspan="2" align="center"><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Guardar" onClick="document.forma.submit();" />
              &nbsp; <input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Cerrar" onClick="javascript:top.GB_hide();" /> </td>
          </tr>
        </table>
      </form>
      <form action="cliente_ficha_frm.php" method="post" name="frmrefresh" id="frmrefresh">
      <input type="hidden" name="p_id_usuario" id="p_id_usuario" value="<?php echo($v_id_usuario); ?>" />
      </form>
    </div></div>
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