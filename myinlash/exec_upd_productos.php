<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/facturacion_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	
	if ( validar_permisos ($conn, 'exec_upd_productos.php') ) {
		$v_nombre = $_POST['p_nombre'];
		$v_referencia = $_POST['p_referencia'];
		$v_valor = $_POST['p_valor'];
		$v_iva = $_POST['p_iva'];
		
		if (isset($_POST['p_id_producto'])) {
			$v_id_producto = $_POST['p_id_producto'];
			upd_producto ($conn, $v_id_producto, $v_referencia, $v_nombre, $v_valor, $v_iva);
		} else {
			crea_producto ($conn, $v_referencia, $v_nombre, $v_valor, $v_iva);
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Actualizaci&oacute;n de productos</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <?php 
    if(isset($_POST['p_id_producto'])) {
  		mensaje(1, 'El producto fue actualizado correctamente', 'javascript:top.refrescar(); top.GB_hide();', '_self'); 
	} else {
		mensaje(1, 'El producto fue creado correctamente', 'javascript:top.refrescar(); top.GB_hide();', '_self'); 
	}
	?>
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