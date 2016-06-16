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
  if ( validar_permisos ($conn, 'servicios_lst.php') ) {
    $t_servicios = lista_servicios ($conn);
    $v_cont = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="file:///C|/Dropbox/Proyectos/mundus/site/Templates/main_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.:: iNLASH & Co - Administraci&oacute;n de servicios ::.</title>
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
      location.replace("servicios_lst.php");  
  }
  function agregar(){
    var url = "<?php echo ("/".$instdir); ?>/servicios_frm.php";
    GB_showCenter("Crear Servicio", url, 430, 720);   
  }

  function editar() {
    myForm = document.forma;
    if (!myForm.p_id_servicio) {
      return;
    } else if (myForm.p_id_servicio.length == undefined) {
      var id_serv = myForm.p_id_servicio.value;
    } else {
      for (var x=0; x<myForm.p_id_servicio.length; x++) {
        if (myForm.p_id_servicio[x].checked) {
          var id_serv = myForm.p_id_servicio[x].value;
          break;
        }
      }
    }
    var url = "<?php echo ("/".$instdir); ?>/servicios_frm.php?p_id_servicio="+id_serv;
      GB_showCenter('Modificar Servicio', url, 430, 720);   
  }
  function eliminar(){
    myForm = document.forma;
    if (confirm ("Se dispone a eliminar el servicio seleccionado.\n\nDesea Continuar?")) {
      myForm.action = "exec_del_servicios.php";
      myForm.submit();
    }
    return;
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
     <div class="titulo">SERVICIOS iNLASH</div>
     <div id="contiene_tabla">
        <form action="#" name="forma" id="forma" method="post">
          <table border="0" cellpadding="0" cellspacing="0" width="80%">
            <tr>
              <td colspan="3"><div id="barra_botones">
                <a href="javascript:agregar();" class="button"><span>Agregar</span></a>&nbsp;
                <a href="javascript:editar();" class="button"><span>Modificar</span></a>&nbsp;
                <a href="javascript:eliminar();" class="button"><span>Eliminar</span></a>&nbsp;
                <a href="mainsite.php" class="button"><span>Regresar</span></a>
              </div></td>
            </tr>
            <tr class="t_header">
              <td>&nbsp;</td>
              <td>Servicio</td>
              <td>Descripci&oacute;n</td>
            </tr>
            <?php
      if (is_array($t_servicios)) {
        foreach ($t_servicios as $dato) {
      ?>
            <tr class="t_texto">
              <td><input type="radio" id="p_id_servicio" name="p_id_servicio" value="<?php echo($dato['id_servicio']); ?>" <?php if ($v_cont == 0) { echo("Checked"); } ?> /></td>
              <td><?php echo($dato['nombre']); ?></td>
              <td><?php echo($dato['descripcion']); ?></td>
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
                <a href="mainsite.php" class="button"><span>Regresar</span></a>
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