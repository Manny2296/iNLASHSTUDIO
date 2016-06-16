<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/sedes_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'servicios_lst.php') ) {
    if($_SESSION['id_perfil']== 4){
      $t_sedes = sede_admin ($conn, $_SESSION['id_sede']) ;
    }else if($_SESSION['id_perfil']== 1){
      $t_sedes = lista_sedes($conn,'S');
    }else
    {
      $t_sedes = null;
    }
		
		$v_cont = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="file:///C|/Dropbox/Proyectos/mundus/site/Templates/main_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.:: iNLASH & Co - Administraci&oacute;n de Sedes ::.</title>
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
   		location.replace("sedes_lst.php");	
	}
	function agregar(){
		var url = "<?php echo ("/".$instdir); ?>/sedes_frm.php";
		GB_showCenter("Crear producto", url, 430, 720);	  
	}
  function agregar_serv(){
    myForm = document.forma;
    if (!myForm.p_id_sede) {
      return;
    } else if (myForm.p_id_sede.length == undefined) {
      var id_sede = myForm.p_id_sede.value;
    } else {
      for (var x=0; x<myForm.p_id_sede.length; x++) {
        if (myForm.p_id_sede[x].checked) {
          var id_sede = myForm.p_id_sede[x].value;
          break;
        }
      }
    }
    
    var url = "<?php echo ("/".$instdir); ?>/sede_servicios_lst.php?p_id_sede="+id_sede;
    GB_showCenter("Agregar Servicio", url, 430, 720);   
  }
	function editar() {
		myForm = document.forma;
		if (!myForm.p_id_sede) {
			return;
		} else if (myForm.p_id_sede.length == undefined) {
			var id_sede = myForm.p_id_sede.value;
		} else {
			for (var x=0; x<myForm.p_id_sede.length; x++) {
				if (myForm.p_id_sede[x].checked) {
					var id_sede = myForm.p_id_sede[x].value;
					break;
				}
			}
		}
		var url = "<?php echo ("/".$instdir); ?>/sedes_frm.php?p_id_sede="+id_sede;
	    GB_showCenter('Modificar Sede', url, 430, 720);	  
	}
	function eliminar(){
		myForm = document.forma;
		if (confirm ("Se dispone a eliminar la sede seleccionada.\n\nDesea Continuar?")) {
			myForm.action = "exec_del_sede.php";
			myForm.submit();
		}
		return;
	}
  function ver(id_sede) {
    var url = "<?php echo ("/".$instdir); ?>/sedes_rep.php?p_id_sede="+id_sede;
      GB_showCenter('Datos del cliente', url, 430, 720);    
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
     <div class="titulo">SEDES iNLASH</div>
     <div id="contiene_tabla">
        <form action="#" name="forma" id="forma" method="post">
          <table border="0" cellpadding="0" cellspacing="0" width="80%">
            <tr>
              <td colspan="3"><div id="barra_botones">
                <?php if($_SESSION['id_perfil']== 1) { ?>
                <a href="javascript:agregar();" class="button"><span>Agregar Sede</span></a>&nbsp;
                <?php } ?>
                <a href="javascript:agregar_serv();" class="button"><span>Agregar Servicio</span></a>&nbsp;
                <a href="javascript:editar();" class="button"><span>Modificar</span></a>&nbsp;
                 <?php if($_SESSION['id_perfil']== 1) { ?>
                <a href="javascript:eliminar();" class="button"><span>Eliminar</span></a>&nbsp;
                <?php } ?>
                <a href="mainsite.php" class="button"><span>Regresar</span></a>
              </div></td>
            
            <tr class="t_header">
              <td>&nbsp;</td>
              <td>Sede</td>
              <td>Direccion</td>
            </tr>
            <?php
			if (is_array($t_sedes)) {
				foreach ($t_sedes as $dato) {
			?>
            <tr class="t_texto">
              <td><input type="radio" id="p_id_sede" name="p_id_sede" value="<?php echo($dato['id_sede']); ?>" <?php if ($v_cont == 0) { echo("Checked"); } ?> /></td>
              <td><a href="javascript:ver(<?php echo($dato['id_sede']); ?>);"><?php echo($dato['nombre']); ?></a></td>
              <td><?php echo($dato['direccion']); ?></td>
            </tr>
            <?php
					$v_cont++;
				}
			}
			if ($v_cont == 0) {
			?>
            <tr class="t_texto" height="40">
              <td colspan="3"><div align="center">No hay Sedes definidas</div></td>
            </tr>
            <?php
			}
			?>
            <tr>
              <td colspan="3"><div id="barra_botones">
                 <?php if($_SESSION['id_perfil']== 1) { ?>
                <a href="javascript:agregar();" class="button"><span>Agregar Sede</span></a>&nbsp;
                <?php } ?>
                <a href="javascript:agregar_serv();" class="button"><span>Agregar Servicio</span></a>&nbsp;
                <a href="javascript:editar();" class="button"><span>Modificar</span></a>&nbsp;
                 <?php if($_SESSION['id_perfil']== 1) { ?>
                <a href="javascript:eliminar();" class="button"><span>Eliminar</span></a>&nbsp;
                <?php } ?>
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