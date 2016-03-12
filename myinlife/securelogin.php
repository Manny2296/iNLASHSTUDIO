<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset ($_SESSION['login'])){
	$v_login = $_SESSION['login'];
	$v_pwd = null;
}else{
	//obtiene variables de formulario
	if (isset($_POST['p_login'])) {
		$v_login = $_POST['p_login'];
		$v_pwd = $_POST['p_pwd'];
	} else {
		//ajuste en caso de que el formulario esté hecho incorrectamente en flash
		$postdata = file_get_contents("php://input");
		$postdata = str_replace("%5F", "_", $postdata);
		parse_str($postdata, $datos);
		$v_login = $datos['p_login'];
		$v_pwd= $datos['p_pwd'];
	}
}	
$cant_perfiles = cantidad_perfil ($conn, $v_login);
// validar seguridad
if (isset($_SESSION['id_usuario']) && isset($_POST['p_id_perfil'])) {
	$_SESSION['login'] = $v_login;
	$_SESSION['id_perfil'] = $_POST['p_id_perfil'];
	$_SESSION['id_perf_unico'] = $_POST['p_id_perf_unico'];
	if($_POST['p_id_perfil'] == 3){
		$v_url = "myinlife.php";
	} else {
		$v_url = "mainsite.php";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"  />
   <title>Ingreso al Sistema</title>
   <link href="skins/<?php echo($skin); ?>/estilo.css" rel="stylesheet" type="text/css">
   <script language="javascript" type="text/javascript">
      function redirect() {
         location.replace ("<?php echo($v_url); ?>");
      }
   </script>
</head>
<body onLoad="redirect();">
</body>
</html>
<?php
} elseif (!isset($_SESSION['login']) && (!validar_usr($v_login, $v_pwd, $conn) || $cant_perfiles == 0)) {
	include ($path."/lib/mensaje_utl.php");
	mensaje(2, 'El login y/o contrase&ntilde;a ingresados no son v&aacute;lidos.<br>Por favor int&eacute;ntelo de nuevo.', $url_login, '_parent');
} elseif (requiere_cambio_pwd ($conn, $v_login) ) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nosecurity.dwt.php" codeOutsideHTMLIsLocked="false" --><head>
 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Validando credenciales de acceso...</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" language="javascript">
function validar() {
	myForm = document.forma;
	if (myForm.p_new_pwd.value != myForm.p_new_pwd_conf.value) {
		alert ("La nueva contrasena y su confirmación no coinciden");
		myForm.p_new_pwd.value = "";
		myForm.p_new_pwd_conf.value = "";
		return;
	}
	if (myForm.p_new_pwd.value == "") {
		alert ("Por favor ingrese una nueva contrasena.\nRecurde que su nueva contrasena es sensible a mayúsculas y minusculas y no puede ser igual al documento de identidad.");
		return;
	}
	myForm.submit();
}		
</script>
<!-- InstanceEndEditable -->
</head>

<body>
<!-- InstanceBeginEditable name="Regcontenido" -->
<form action="exec_upd_pwd.php" name="forma" id="forma" method="post">
<input type="hidden" name="p_login" id="p_login" value="<?php echo($v_login); ?>" />
<table width="600" border="0" align="center" cellpadding="2" cellspacing="2" class="bordertable">
  <tr>
    <th colspan="2"><h1>Cambio de contrase&ntilde;a</h1></th>
  </tr>
  <tr>
    <td colspan="2"><div align="center">Ingrese una nueva contrase&ntilde;a de acceso al sistema. Tenga en cuenta que su nueva contrase&ntilde;a debe ser <strong>diferente de su n&uacute;mero de documento de identidad</strong> y es sensible a may&uacute;sculas y min&uacute;sculas.</div></td>
  </tr>
  <tr>
    <th><div align="left">Nueva contrase&ntilde;a: </div></th>
    <td><input type="password" name="p_new_pwd" id="p_new_pwd" size="20" maxlength="20" /></td>
  </tr>
  <tr>
    <th><div align="left">Confirme su nueva contrase&ntilde;a: </div></th>
    <td><input type="password" name="p_new_pwd_conf" id="p_new_pwd_conf" size="20" maxlength="20" /></td>
  </tr>
  <tr>
    <td colspan="2"><div align="center"><input type="button" name="btn_enviar" id="btn_enviar" value="Actualizar" onClick="validar();" />
      &nbsp;<input type="button" name="boton2" id="boton2" value="Regresar" onClick="location.replace('logout.php');" /></div> </td>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>  
<?php	
} elseif ($cant_perfiles == 1) {
	$v_usuario = obtener_perfil ($conn, $v_login);
	$_SESSION['id_usuario'] = $v_usuario[0]['id_usuario'];
	$_SESSION['id_perfil'] = $v_usuario[0]['id_perfil'];
	$_SESSION['id_perf_unico'] = $v_usuario[0]['id_perf_unico'];
	$_SESSION['nombre'] = $v_usuario[0]['nombres'].' '.$v_usuario[0]['apellidos'];
	$_SESSION['login'] = $v_login;
	if($v_usuario[0]['id_perfil'] == 3){
		$v_url = "myinlife.php";
	} else {
		$v_url = "mainsite.php";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
   <title>Ingreso al Sistema</title>
   <link href="skins/<?php echo($skin); ?>/estilo.css" rel="stylesheet" type="text/css">
   <script language="javascript" type="text/javascript">
      function redirect() {
		 location.replace ("<?php echo($v_url) ?>");
      }
   </script>
</head>
<body onLoad="redirect();">
</body>
</html>
<?php	
} else {
	$t_usuario = obtener_perfil ($conn, $v_login);
	$_SESSION['id_usuario'] = $t_usuario[0]['id_usuario'];
	$_SESSION['nombre'] = $t_usuario[0]['nombres'].' '.$t_usuario[0]['apellidos'];
	$_SESSION['login'] = $v_login;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nosecurity.dwt.php" codeOutsideHTMLIsLocked="false" --><head>
 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Validando credenciales de acceso...</title>
<script language="javascript" type="text/javascript">
function selperfil(perfuk, perfil) {
	myForm = document.getElementById("forma");
	myForm.p_id_perf_unico.value = perfuk;
	myForm.p_id_perfil.value = perfil;
	myForm.submit();
}
</script>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->

<!-- InstanceEndEditable -->
</head>

<body>
<!-- InstanceBeginEditable name="Regcontenido" -->
<table width="600" border="0" align="center" cellpadding="2" cellspacing="2" class="bordertable">
  <tr>
    <th colspan="2"><h1>Selecci&oacute;n de Perfil</h1></th>
  </tr>
  <tr>
    <td colspan="2"><div align="center">Seleccione el perfil de usuario con el cual desea acceder al portal</div></td>
  </tr>
<?php
if (is_array($t_usuario)) {
	foreach ($t_usuario as $dato) {
?>
  <tr>
    <td width="40">
       <span class="botonop"><a href="javascript:selperfil(<?php echo ("'".$dato['id_perf_unico']. "', '".$dato['id_perfil']."'"); ?>);">
          <img src="skins/<?php echo ($skin); ?>/boton_opcion.png" alt="Opci&oacute;n" title="Opci&oacute;n" border="0" /></a></span></td>
    <td><h2><div align="left"><?php echo ($dato['nomperfil']);?></div></h2></td>
  </tr>
<?php
	}
}
?>
  <tr>
    <th colspan="2"><img src="skins/<?php echo ($skin); ?>/spacer.gif" width="30" height="30" /></th>
  </tr>
</table>
<form id="forma" name="forma" method="post" action="securelogin.php">
  <input id="p_id_perfil" name="p_id_perfil" type="hidden" />
  <input id="p_id_perf_unico" name="p_id_perf_unico" type="hidden" />
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
<?php
}
dbdisconn ($conn);
?>