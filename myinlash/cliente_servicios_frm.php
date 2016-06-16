<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/servicios_utl.php");
include ($path."/lib/usuarios_utl.php");
include ($path."/lib/parametros_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'cliente_servicios_frm.php') ) {
		$v_id_usuario = $_POST["p_id_usuario"];
		$v_id_perf_unico = $_POST["p_id_perf_unico"];
		$r_usuario = nombres_usua($conn, $v_id_usuario);
		
		$r_datos_usuario = detalle_usuario ($conn, $v_id_perf_unico);
		$v_nombre = $r_usuario['nombres']." ".$r_usuario['apellidos'];
		$t_servicios =  lista_servicios_prep ($conn, $v_id_perf_unico,$r_datos_usuario['id_sede']);
		$v_accion = $_POST['p_accion'];
		if ($v_accion == "editar") {
			$v_codigo = explode("|", $_POST['p_id_servicio']);
			$r_servicio = detalle_servicio_cliente ($conn, $v_id_usuario, $v_codigo[0], $v_codigo[1]);
		} else {
			$r_servicio = null;
			$v_intervalo = obtener_valor_param ($conn, 'VIPA');
			$v_fecha_ini = new DateTime();
			$v_fecha_cad = new DateTime();
			$v_fecha_cad->add(new DateInterval('P'.$v_intervalo.'D'));
		}
?>
<html><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Definici&oacute;n de servicios para el cliente</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" language="javascript" src="lib/popcalendar.js"></script>
<script type="text/javascript" language="javascript">
	function validar() {
		myForm = document.forma;
		try{
			if (myForm.p_id_servicio.selectedIndex == 0) {
				alert ("Por favor seleccione un servicio de la lista");
				return;
			}
		}catch(e){
			null;
		}
		if ( myForm.p_caducidad.value != "" ) {
			var ftext = myForm.p_caducidad.value.split("-");
			var fcad = new Date(ftext[2], ftext[1], ftext[0]);
			ftext = myForm.p_fecha.value.split("-");
			fini = new Date(ftext[2], ftext[1], ftext[0]);
			hoy = new Date();
			
			if (fini >= fcad || fcad <= hoy) {
				alert("La fecha de caducidad del paquete debe ser como mínimo mañana");
				return;
			}
		}
		if (myForm.p_fecha.value == ""){
			alert("Por favor seleccione una fecha para el registro de la compra del servicio");
			return;
		}
		if (myForm.p_cantidad.value == ""){
			alert("Por favor ingrese la cantidad contratada");
			return;
		}
		if (myForm.p_continuidad.value == ""){
			alert("Por favor ingrese el número de sesiones semanales ideales para el usuario según su progreso");
			return;
		}
		myForm.submit();
	}
</script>
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="sub_tit">DEFINICI&Oacute;N DE SERVICIOS PARA EL CLIENTE</div>
     <div class="capa_form_sf">
        <form id="forma" name="forma" method="post" action="exec_upd_servcliente.php">
        <input type="hidden" name="p_id_usuario" id="p_id_usuario" value="<?php echo($v_id_usuario); ?>" />
        <input type="hidden" name="p_id_perf_unico" id="p_id_perf_unico" value="<?php echo($v_id_perf_unico); ?>" />
        <table width="90%" border="0" cellpadding="0" cellspacing="0">
          <tr>
			<th>Cliente:</th>
            <td><?php echo($v_nombre); ?></td>
          </tr>
          <tr>
			<th>Fecha:</th>
            <td><input type="text" name="p_fecha" id="p_fecha" maxlength="12" size="12" <?php if (is_null($r_servicio)) { ?> onClick="popUpCalendar(this, forma.p_fecha);" <?php } ?> readonly value="<?php if(!is_null($r_servicio)){ echo($r_servicio['fecha']); } else { echo($v_fecha_ini->format('d-m-Y')); } ?>"/></td>
          </tr>
          <tr>
			<th>Servicio:</th>
            <?php if (is_null($r_servicio)){ ?>
            <td><select name="p_id_servicio" id="p_id_servicio">
            <option value=""></option>
            <?php foreach($t_servicios as $dato) { ?>
            <option value="<?php echo($dato['id_servicio']); ?>"><?php echo($dato['nombre']); ?></option>
            <?php } ?>
            </select></td>
            <?php } else { ?>
            <td><?php echo($r_servicio['nombre']); ?> <input type="hidden" name="p_id_servicio" id="p_id_servicio" value="<?php echo($v_codigo[0]); ?>"></td>
            <?php } ?>
          </tr>
          <tr>
			<th>Cantidad adquirida:</th>
            <td><input type="text" name="p_cantidad" id="p_cantidad" size="4" maxlength="4" value="<?php if(!is_null($r_servicio)){ echo($r_servicio['cantidad']); } ?>" /></td>
          </tr>
          <tr>
			<th>Número de sesiones semanales sugeridas:</th>
            <td><input type="text" name="p_continuidad" id="p_continuidad" size="4" maxlength="4" value="<?php if(!is_null($r_servicio)){ echo($r_servicio['continuidad']); } ?>"/></td>
          </tr>
          <tr>
			<th>Fecha de vencimiento del paquete:</th>
            <td><input type="text" name="p_caducidad" id="p_caducidad" maxlength="12" size="12" onClick="popUpCalendar(this, forma.p_caducidad);" readonly value="<?php if(!is_null($r_servicio)){ echo($r_servicio['caducidad']); } else { echo($v_fecha_cad->format('d-m-Y')); } ?>"/></td>
          </tr>
          <tr>
            <th>Congelar paquete:</th>
            <td><input type="checkbox" name="p_congelar" id="p_congelar" value="S" <?php if($r_servicio['congelar'] == "S") { echo("Checked"); }?>></td>
          <tr>
              <td colspan="2" align="center"><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Guardar" onClick="validar();" />
              &nbsp; <input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Regresar" onClick="javascript:document.frmback.submit();" /> </td>
          </tr>
        </form>
      </div>
      <form id="frmback" name="frmback" action="cliente_servicios_lst.php" method="post">
      <input type="hidden" name="p_id_usuario" id="p_id_usuario" value="<?php echo($v_id_usuario); ?>" />
      <input type="hidden" name="p_id_perf_unico" id="p_id_perf_unico" value="<?php echo($v_id_perf_unico); ?>" />
      </form>
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