<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/sedes_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'clientes_frm.php') ) {
		$v_id_sede = $_REQUEST['p_id_sede'];
		$r_sede = detalle_sede ($conn, $v_id_sede);
		
?>
<html><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Consulta de sedes del sistema</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="titulo">SEDES INLIFE</div>
     <div class="capa_form_sf">
        <form name="forma" id="forma" action="#">
        <table width="80%" border="0" cellpadding="0" cellspacing="0">
          <tr>
			<th>Nombre:</th>
            <td><?php echo($r_sede['nombre']); ?></td>
          </tr>
          <tr>
			<th>Pais:</th>
            <td><?php echo($r_sede['pais']); ?></td>
          </tr>
          <tr>
			<th>Ciudad:</th>
            <td><?php echo($r_sede['ciudad']); ?></td>
          </tr>
          <tr>
			<th>Direcci&oacute;n:</th>
            <td><?php echo($r_sede['direccion']); ?></td>
          </tr>
          <tr>
			<th>Tel&eacute;fono :</th>
            <td><?php echo($r_sede['telefono']); ?></td>
          </tr>
          <tr>
			<th>Domicilio:</th>
          <?php if ($r_sede['domicilio']=='S'){?>
            <td>Si</td>
          <?php }else{ ?>
            <td>No</td> 
            <?php } ?>
          </tr>
          <tr>
			<th>Numero de factura:</th>
            <td><?php echo($r_sede['Num_factura']); ?></td>
          </tr>
          <tr>
			<th>Pref. factura:</th>
            <td><?php echo($r_sede['Pref_factura']); ?></td>
          </tr>
          <tr>
      <th>Activa:</th>
            <?php if ($r_sede['Activa']=='S'){?>
            <td>Si</td>
          <?php }else{ ?>
            <td>No</td> 
            <?php } ?>
          </tr>
          <tr>
              <td colspan="2" align="center"><input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Regresar" onClick="javascript:top.GB_hide();" /> </td>
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