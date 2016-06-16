<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'prog.php') ) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- TemplateBeginEditable name="doctitle" -->
<title>Untitled Document</title>
<!-- TemplateEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- TemplateBeginEditable name="head" -->
<!-- TemplateEndEditable -->
</head>

<body>
  <div id="contendor">
     <?php include ($path."/layout_header.php"); ?>
     <?php include ($path."/layout_menu_lateral.php"); ?>
     <div id="contenido">
	 <!-- TemplateBeginEditable name="contenido" -->
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
			foreach ($t_ as $dato) {
			?>
            <tr class="t_texto">
              <td><input type="radio" id="p_id_servicio" name="p_id_servicio" value="<?php echo($dato['id_servicio']); ?>" <?php if ($v_cont == 0) { echo("Checked"); } ?> /></td>
              <td><?php echo($dato['nombre']); ?></td>
              <td><?php echo($dato['descripcion']); ?></td>
            </tr>
            <?php
				$v_cont++;
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
	 <!-- TemplateEndEditable -->
     </div>
  </div>
</body>
</html>
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