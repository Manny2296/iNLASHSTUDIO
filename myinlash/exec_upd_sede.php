<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/sedes_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	
	if ( validar_permisos ($conn, 'exec_upd_servicios.php') ) {
		$v_nombre = $_POST['p_nombre'];
		$v_pais = $_POST['p_pais'];
		$v_ciudad = $_POST['p_ciudad'];
		$v_direccion = $_POST['p_direccion'];
		$v_telefono = $_POST['p_telefono'];
		$v_domicilio = $_POST['p_domicilio'];
		$v_num_factura = $_POST['p_num_factura'];
		$v_pref_factura = $_POST['p_pref_factura'];
		$v_activa = $_POST['p_activa'];
		$v_existe = $_POST['p_existe'];
		if (isset($_POST['p_id_sede'])) {
			$v_id_sede = $_POST['p_id_sede'];
			if($v_existe == "U"){
				act_sede($conn, $v_id_sede);
				
			}else{
				upd_sede ($conn, $v_id_sede, $v_nombre ,$v_pais ,$v_ciudad ,$v_direccion,$v_telefono ,$v_domicilio ,$v_num_factura ,$v_pref_factura ,$v_activa );
				
			}
			
		} else {

			$creada=crea_sede ($conn,$v_nombre ,$v_pais ,$v_ciudad ,$v_direccion,$v_telefono ,$v_domicilio ,$v_num_factura ,$v_pref_factura ,$v_activa);
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Actualizaci&oacute;n de sedes</title>
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
    if(isset($_POST['p_id_sede'])) {
  		mensaje(1, 'la sede fue actualizada correctamente', 'javascript:top.refrescar(); top.GB_hide();', '_self'); 
	} else {
		if($creada){
			mensaje(1, 'la sede fue creada correctamente', 'javascript:top.refrescar(); top.GB_hide();', '_self'); 
		}else{

			mensaje(2, 'la sede no fue creada correctamente, el nombre ya esta siendo usado', 'javascript:top.refrescar(); top.GB_hide();', '_self'); 
			
		}
		
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