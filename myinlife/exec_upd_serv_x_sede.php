<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/sedes_dml.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	
	if ( validar_permisos ($conn, 'exec_upd_productos.php') ) {
		$v_id_sede = $_POST['p_id_sede'];
		$v_id_servicio = $_POST['p_id_servicio'];
		$v_sesiones_simultaneas = $_POST['p_sesiones_simultaneas'];
		$creada=false;
		$actualizada=false;
		if (agregar_servicio($conn, $v_id_sede,$v_id_servicio,$v_sesiones_simultaneas)) {
			$creada=true;
			
		} else if(upd_servicio_sede($conn, $v_id_sede,$v_id_servicio,$v_sesiones_simultaneas)){

			$actualizada = true; 
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
    if($actualizada) {
  		mensaje_form(1, 'el servicio de la sede fue actualizado correctamente', 'sede_servicios_lst.php', 'p_id_sede', $v_id_sede); 
	} else {
		if($creada){
			mensaje_form(1, 'el servicio de la sede fue creado correctamente', 'sede_servicios_lst.php', 'p_id_sede', $v_id_sede); 
		}else{

			mensaje_form(2, 'el servicio de la sede no fue creado o actualizado correctamente', 'sede_servicios_lst.php', 'p_id_sede', $v_id_sede); 
			
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