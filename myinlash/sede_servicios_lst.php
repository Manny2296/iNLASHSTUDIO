<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/servicios_utl.php");
include ($path."/lib/sedes_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'servicios_lst.php') ) {
		$v_id_sede = $_REQUEST['p_id_sede'];
		$r_sede = detalle_sede ($conn, $v_id_sede);
		$t_servicios = lista_servicios_x_sede ($conn,$v_id_sede);
		$v_cont = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="file:///C|/Dropbox/Proyectos/mundus/site/Templates/main_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.Agregar servicio a una sede.</title>
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
   		location.replace("sede_servicios_lst.php");	
	}
	function agregar(){
		myForm = document.forma;
		myForm.action = "sede_servicios_frm.php";
		myForm.submit();  
	}
	function editar() {
		myForm = document.forma;
		myForm.action = "sede_servicios_frm.php";
		myForm.p_editar.value='S';
		myForm.submit();   
	}
	function eliminar(){
		myForm = document.forma;
		if (confirm ("Se dispone a eliminar el servicio seleccionado.\n\nDesea Continuar?")) {
			myForm.action = "exec_del_servicios_sede.php";
			myForm.submit();
		}
		return;
	}
</script>
<!-- InstanceEndEditable -->
</head>

<body>

     <div id="contenido">
	 <!-- InstanceBeginEditable name="contenido" -->
     <div class="titulo">SERVICIOS INLIFE PARA LA SEDE: <?php echo($r_sede['nombre']); ?>  </div>
     <div id="contiene_tabla">
        <form action="#" name="forma" id="forma" method="post">
        <input type="hidden" name="p_id_sede" id="p_id_sede" value="<?php echo($v_id_sede); ?>" />
        <input type="hidden" name="p_editar" id="p_editar" value="N" />
          <table border="0" cellpadding="0" cellspacing="0" width="80%">
            <tr>
              <td colspan="3"><div id="barra_botones">
                <a href="javascript:agregar();" class="button"><span>Agregar</span></a>&nbsp;
                <a href="javascript:editar();" class="button"><span>Modificar</span></a>&nbsp;
                <a href="javascript:eliminar();" class="button"><span>Eliminar</span></a>&nbsp;
                <a href="javascript:top.GB_hide();" class="button"><span>Cerrar</span></a>
              </div></td>
            </tr>
            <tr class="t_header">
              <td>&nbsp;</td>
              <td>Servicio</td>
              <td>Descripci&oacute;n</td>
              <td>Sesiones Simultaneas</td>
            </tr>
            <?php
			if (is_array($t_servicios)) {
				foreach ($t_servicios as $dato) {
			?>
            <tr class="t_texto">
              <td><input type="radio" id="p_id_servicio" name="p_id_servicio" value="<?php echo($dato['id_servicio']); ?>" <?php if ($v_cont == 0) { echo("Checked"); } ?> /></td>
              <td><?php echo($dato['nombre']); ?></td>
              <td><?php echo($dato['descripcion']); ?></td>
              <td><?php echo($dato['sesiones_simultaneas']); ?></td>
            </tr>
            <?php
					$v_cont++;
				}
			}
			if ($v_cont == 0) {
			?>
            <tr class="t_texto" height="40">
              <td colspan="3"><div align="center">No hay servicios definidos</div></td>
            </tr>
            <?php
			}
			?>
            <tr>
              <td colspan="3"><div id="barra_botones">
                <a href="javascript:agregar();" class="button"><span>Agregar</span></a>&nbsp;
                <a href="javascript:editar();" class="button"><span>Modificar</span></a>&nbsp;
                <a href="javascript:eliminar();" class="button"><span>Eliminar</span></a>&nbsp;
                <a href="javascript:top.GB_hide();" class="button"><span>Cerrar</span></a>
              </div></td>
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