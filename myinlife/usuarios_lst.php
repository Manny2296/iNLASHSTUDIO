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
	if ( validar_permisos ($conn, 'usuarios_lst.php') ) {
		$v_tipo = $_POST['p_tipo'];
		if (isset($_POST['p_param'])) {
			$v_param = $_POST['p_param'];
		} else {
			$v_param = null;
		}
		$t_usuarios = lista_usuarios ($conn, $v_tipo, $v_param);
		if ($v_tipo == "perfil") {
			$v_id_perfil_usua = $v_param;
		}
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
		myForm.action = "usuarios_lst.php";
		myForm.submit();
	}
	function agregar(){
		var url = "<?php echo ("/".$instdir); ?>/usuarios_frm.php";
		GB_showCenter("Crear usuarios", url, 430, 720);	  
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
		var url = "<?php echo ("/".$instdir); ?>/usuarios_frm.php?p_id_perf_unico="+p_id_perf_unico;
	    GB_showCenter('Modificar usuario', url, 430, 720);	  
	}
	function eliminar(){
		myForm = document.forma;
		if (confirm ("Se dispone a eliminar el perfil del usuario seleccionado.\n\nDesea Continuar?")) {
			myForm.action = "exec_del_usuario.php";
			myForm.submit();
		}
		return;
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
          <table border="0" cellpadding="0" cellspacing="0" width="80%">
            <tr>
              <td colspan="5"><div id="barra_botones">
                <a href="javascript:agregar();" class="button"><span>Agregar</span></a>&nbsp;
                <a href="javascript:editar();" class="button"><span>Modificar</span></a>&nbsp;
                <a href="javascript:eliminar();" class="button"><span>Eliminar</span></a>&nbsp;
                <a href="usuarios_consulta_frm.php" class="button"><span>Regresar</span></a>
              </div></td>
            </tr>
            <tr class="t_header">
              <td>&nbsp;</td>
              <td>Documento de Identidad</td>
              <td>Apellidos y Nombres</td>
              <td>Tipo de usuario</td>
              <td>Opciones</td>
            </tr>
            <?php
			$v_cont =0;
			if (is_array($t_usuarios)) {
				foreach ($t_usuarios as $dato) {
					if ($v_tipo != "perfil") {
						$v_id_perfil_usua = $dato['id_perfil'];
					}
					
			?>
            <tr class="t_texto">
              <td><input type="radio" id="p_id_perf_unico" name="p_id_perf_unico" value="<?php echo($dato['id_perf_unico']); ?>" <?php if ($v_cont == 0) { echo("Checked"); } ?> /></td>
              <td><?php echo($dato['abreviatura']." ".$dato['numero_id']); ?></td>
              <td><?php echo($dato['apellidos']." ".$dato['nombres']); ?></td>
              <td><?php echo($dato['nomperfil']); ?></td>
              <?php  
			    if ($v_id_perfil_usua == 3) { ?>
               <td><a href="javascript:reset_pwd(<?php echo($dato['id_usuario']); ?>);" class="button"><span><img src="skins/<?php echo($skin); ?>/icon_pwd.png" alt="Restablecer contrase&ntilde;a" title="Restablecer contrase&ntilde;a" border="0" /></a>&nbsp;<a href="javascript:servicios(<?php echo($dato['id_usuario']); ?>);" class="button"><span><img src="skins/<?php echo($skin); ?>/icon_service.png" alt="Asignar servicios prepagados al cliente" title="Asignar servicios prepagados al cliente" border="0" /></a></span></td>
               <?php } else { ?>
               <td><a href="javascript:reset_pwd(<?php echo($dato['id_usuario']); ?>);" class="button"><span><img src="skins/<?php echo($skin); ?>/icon_pwd.png" alt="Restablecer contrase&ntilde;a" title="Restablecer contrase&ntilde;a" border="0" /></a></td>
               <?php } ?>
            </tr>
            <?php
					$v_cont++;
				}
			}
			if ($v_cont == 0) {
			?>
            <tr class="t_texto" height="40">
              <td colspan="5"><div align="center">No hay usuarios definidos para los criterios de consulta establecidos</div></td>
            </tr>
            <?php
			}
			?>
            <tr>
              <td colspan="5"><div id="barra_botones">
                <a href="javascript:agregar();" class="button"><span>Agregar</span></a>&nbsp;
                <a href="javascript:editar();" class="button"><span>Modificar</span></a>&nbsp;
                <a href="javascript:eliminar();" class="button"><span>Eliminar</span></a>&nbsp;
                <a href="usuarios_consulta_frm.php" class="button"><span>Regresar</span></a>
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