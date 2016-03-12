<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/parametros_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'sysconfig_frm.php') ) {
		$t_params = obtener_parametros($conn);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/main_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.:: MY INLIFE STUDIO - Configuraci&oacute;n del sistema ::.</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contendor">
     <?php include ($path."/layout_header.php"); ?>
     <?php include ($path."/layout_menu_lateral.php"); ?>
     <div id="contenido">
	 <!-- InstanceBeginEditable name="contenido" -->
	 <div class="titulo">CONFIGURACI&Oacute;N DEL SISTEMA</div>
     <div class="capa_form">
        <form id="forma" name="forma" method="post" action="exec_upd_config.php">
        <table width="80%" border="0" cellpadding="0" cellspacing="0">
          <?php
			foreach ($t_params as $dato) {
			?>
				<tr>
				  <th><?php echo ($dato['descripcion'].'<br><span class="texto_peq">'.$dato['codigo'].'</span>'); ?></th>
				  <?php 
				  switch ($dato['tipo']){
					  case 5:
				  ?>
				  <td><?php lista_sn ($dato['id_parametro'], $dato['valor']);?></td>
				  <?php 
					  break;
					  case 1:
				  ?>
				  <td><input type="text" id="para_<?php echo ($dato['id_parametro']); ?>" name="para_<?php echo ($dato['id_parametro']); ?>" maxlength="200" size="50" value="<?php echo ($dato['valor']); ?>" /></td>
				  <?php 
					  break;
					  case 2:
				  ?>
				  <td><input type="text" id="para_<?php echo ($dato['id_parametro']); ?>" name="para_<?php echo ($dato['id_parametro']); ?>" maxlength="10" size="10" value="<?php echo ($dato['valor']); ?>" /></td>
				  <?php 
					  break;
					  case 4:
				  ?>
				  <td><?php lista_items_params ($conn, $dato['id_parametro'], $dato['valor']);?></td>
				  <?php 
					  break;
				  }?>
			  </tr>
			 <?php }?>
			  <tr>
				  <td colspan="2" align="center"><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Guardar" onclick="document.forma.submit();" />
				  &nbsp; <input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Regresar" onclick="location.replace('mainsite.php');" /> </td>
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