<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/usuarios_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'clientes_lst.php') ) {
		$v_tipo = $_POST['p_tipo'];
		$v_id_sede = $_POST['p_id_sede'];
		if (isset($_POST['p_param'])) {
			$v_param = $_POST['p_param'];
		} else {
			$v_param = null;
		}
		$t_usuarios = lista_clientes ($conn, $v_tipo, $v_param,$v_id_sede);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/tabla_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.:: MY INLIFE STUDIO - Administraci&oacute;n de usuarios ::.</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" language="JavaScript">
          var GB_ROOT_DIR = "<?php echo ($site_domain."/".$instdir); ?>/lib/greybox/greybox/";
</script>
<link href="<?php echo ("/".$instdir); ?>/lib/greybox/greybox/gb_styles.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript" src="<?php echo ("/".$instdir); ?>/lib/greybox/greybox/AJS.js"></script>
<script type="text/javascript" language="JavaScript" src="<?php echo ("/".$instdir); ?>/lib/greybox/greybox/AJS_fx.js"></script>
<script type="text/javascript" language="JavaScript" src="<?php echo ("/".$instdir); ?>/lib/greybox/greybox/gb_scripts.js"></script>
<script type="text/javascript" language="javascript">
	function refrescar() {
		myForm = document.forma;
		myForm.action = "clientes_lst.php";
		myForm.submit();
	}
	function agregar(){
		myForm = document.forma;
		var p_id_sede = myForm.p_id_sede.value;
		var url = "<?php echo ("/".$instdir); ?>/clientes_frm.php?p_id_sede="+p_id_sede;
		GB_showCenter("Crear clientes", url, 430, 720);	  
	}
	function editar() {
		myForm = document.forma;
		if (!myForm.p_id_perf_unico) {
			return;
		} else if (myForm.p_id_perf_unico.length == undefined) {
			var p_id_perf_unico = myForm.p_id_perf_unico.value;
		} else {
			for (var x=0; x<myForm.p_id_perf_unico.length; x++) {
				if (myForm.p_id_perf_unico[x].checked) {
					var p_id_perf_unico = myForm.p_id_perf_unico[x].value;
					break;
				}
			}
		}
		var url = "<?php echo ("/".$instdir); ?>/clientes_frm.php?p_id_perf_unico="+p_id_perf_unico;
	    GB_showCenter('Modificar cliente', url, 430, 720);	  
	}
	function ver(id_cliente) {
		var url = "<?php echo ("/".$instdir); ?>/clientes_rep.php?p_id_perf_unico="+id_cliente;
	    GB_showCenter('Datos del cliente', url, 430, 720);	  
	}
	function eliminar(){
		myForm = document.forma;
		if (confirm ("Se dispone a eliminar al cliente seleccionado.\n\nDesea Continuar?")) {
			myForm.action = "exec_del_cliente.php";
			myForm.submit();
		}
		return;
	}
	function servicios(id_cliente,id_perf_unico){
		var url = "<?php echo ("/".$instdir); ?>/cliente_servicios_lst.php?p_id_usuario="+id_cliente+"&p_id_perf_unico="+id_perf_unico;
		GB_showCenter("Servicios en prepago definidos para el cliente", url, 430, 800);	  
	}
	function ver_ficha(id_cliente){
		var url = "<?php echo ("/".$instdir); ?>/cliente_ficha_frm.php?p_id_usuario="+id_cliente;
		GB_showCenter("Ficha antropomética del cliente", url, 430, 720);	  
	}
	function ver_pestanas(id_cliente){
		var url = "<?php echo ("/".$instdir); ?>/cliente_pestanas_frm.php?p_id_usuario="+id_cliente;
		GB_showCenter("Pestañas del cliente", url, 500, 720);	  
	}
	function facturar(id_cliente,id_sede){
		var url = "<?php echo ("/".$instdir); ?>/factura_frm.php?p_id_usuario="+id_cliente+"&p_id_sede="+id_sede;
		GB_showCenter("Factura", url, 500, 780);	  
	}
	function ver_notifs(id_cliente){
		var url = "<?php echo ("/".$instdir); ?>/cliente_segemails_rep.php?p_id_usuario="+id_cliente;
		GB_showCenter("Seguimiento de notificaciones para el cliente", url, 430, 800);	  
	}
	function reset_pwd(id_cliente){
		var url = "<?php echo ("/".$instdir); ?>/exec_reset_pwd.php?p_id_usuario="+id_cliente;
		GB_showCenter("Restablecimiento de contraseña", url, 260, 600);	  
	}
</script>
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contendor">
     <?php include ($path."/layout_header.php"); ?>
     <?php include ($path."/layout_menu_lateral.php"); ?>
     <div id="contenido">
	 <!-- InstanceBeginEditable name="contenido" -->
	 <div id="contiene_tabla">
        <form action="#" name="forma" id="forma" method="post">
          <input type="hidden" name="p_tipo" id="p_tipo" value="<?php echo($v_tipo); ?>" />
          <input type="hidden" name="p_param" id="p_param" value="<?php echo($v_param); ?>" />
          <input type="hidden" name="p_id_sede" id="p_id_sede" value="<?php echo($v_id_sede); ?>" />
          <table border="0" cellpadding="0" cellspacing="0" width="80%">
            <tr>
              <td colspan="4"><div id="barra_botones">
                <a href="javascript:agregar();" class="button"><span>Agregar</span></a>&nbsp;
                <a href="javascript:editar();" class="button"><span>Modificar</span></a>&nbsp;
                <a href="javascript:eliminar();" class="button"><span>Eliminar</span></a>&nbsp;
                <a href="clientes_consulta_frm.php" class="button"><span>Regresar</span></a>
              </div></td>
            </tr>
            <tr class="t_header">
              <td>&nbsp;</td>
              <td>Documento de Identidad</td>
              <td>Apellidos y Nombres</td>
              <td>Sede</td>
               <td>Telefono</td>
              <td>Opciones</td>

            </tr>
            <?php
			$v_cont =0;
			if (is_array($t_usuarios)) {
				foreach ($t_usuarios as $dato) {
					$v_pestanas = mostrar_pestanas ($conn, $dato['id_usuario']);
					if ($v_tipo != "perfil") {
						$v_id_perfil_usua = $dato['id_perfil'];
					}
					
			?>
            <tr class="t_texto">
              <td><input type="radio" id="p_id_perf_unico" name="p_id_perf_unico" value="<?php echo($dato['id_perf_unico']); ?>" <?php if ($v_cont == 0) { echo("Checked"); } ?> /></td>
              <td><?php echo($dato['abreviatura']." ".$dato['numero_id']); ?></td>
              <td><a href="javascript:ver(<?php echo($dato['id_perf_unico']); ?>);"><?php echo($dato['apellidos']." ".$dato['nombres']); ?></a></td>
              <td><?php echo($dato['nomsede']); ?></td>
                <td><?php echo($dato['celular']); ?></td>
              <td><a href="javascript:reset_pwd(<?php echo($dato['id_usuario']); ?>);" class="button"><span><img src="skins/<?php echo($skin); ?>/icon_pwd.png" alt="Restablecer contrase&ntilde;a" title="Restablecer contrase&ntilde;a" border="0" /></a>&nbsp;<a href="javascript:ver_notifs(<?php echo($dato['id_usuario']); ?>);" class="button"><span><img src="skins/<?php echo($skin); ?>/icon_email.png" alt="Seguimiento de notificaciones del cliente" title="Seguimiento de notificaciones del cliente" border="0" /></a>&nbsp;<a href="javascript:facturar(<?php echo($dato['id_usuario']); ?>,<?php echo($dato['id_sede']); ?>);" class="button"><span><img src="skins/<?php echo($skin); ?>/icon_factura.png" alt="Crear una factura" title="Crear una factura" border="0" /></a></span>&nbsp;<a href="javascript:servicios(<?php echo($dato['id_usuario']);?>, <?php echo($dato['id_perf_unico']); ?>);" class="button"><span><img src="skins/<?php echo($skin); ?>/icon_service.png" alt="Asignar servicios prepagados al cliente" title="Asignar servicios prepagados al cliente" border="0" /></a></span>&nbsp;<a href="javascript:ver_ficha(<?php echo($dato['id_usuario']); ?>);" class="button"><span><img src="skins/<?php echo($skin); ?>/icon_ficha.png" alt="Consultar ficha antropom&eacute;trica" title="Consultar ficha antropom&eacute;trica" border="0" /></a><?php  { ?>&nbsp;<a href="javascript:ver_pestanas(<?php echo($dato['id_usuario']); ?>);" class="button"><span><img src="skins/<?php echo($skin); ?>/icon_eye.png" alt="Consultar pesta&ntilde;as del cliente" title="Consultar pesta&ntilde;as del cliente" border="0" /></a><?php }  ?></td>

            </tr>
            <?php
					$v_cont++;
				}
			}
			if ($v_cont == 0) {
			?>
            <tr class="t_texto" height="40">
              <td colspan="4"><div align="center">No hay clientes definidos para los criterios de consulta establecidos</div></td>
            </tr>
            <?php
			}
			?>
            <tr>
              <td colspan="4"><div id="barra_botones">
                <a href="javascript:agregar();" class="button"><span>Agregar</span></a>&nbsp;
                <a href="javascript:editar();" class="button"><span>Modificar</span></a>&nbsp;
                <a href="javascript:eliminar();" class="button"><span>Eliminar</span></a>&nbsp;
                <a href="clientes_consulta_frm.php" class="button"><span>Regresar</span></a>
              </div></td>
            </tr>
          </table>
        </form>
     </div>
	 <!-- InstanceEndEditable -->
     </div>
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