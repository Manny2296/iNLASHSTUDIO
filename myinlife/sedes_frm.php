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
	if ( validar_permisos ($conn, 'productos_frm.php') ) {
		$v_id_sede = null;
		if (isset($_REQUEST['p_id_sede'])) {
			$v_id_sede = $_REQUEST['p_id_sede'];
			$r_sede = detalle_sede ($conn, $v_id_sede);
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Creaci&oacute;n / Modificaci&oacute;n de sedes</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="lib/zxml.js"></script>

<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<script type="text/javascript" language="javascript">
   function validar_sede() {
     myForm = document.forma;
    
     var p_nombre = myForm.p_nombre.value;
      
     var rUrl = "ajax_verificar_sede.php";
     var rBody = "p_nombre="+p_nombre;
     oDiv = document.getElementById ("fakefrmdiv");
     oDiv.innerHTML = "<img src=\"skins/<?php echo($skin); ?>/loader.gif\"><b>Consultando...</b>";
     var oXmlHttp = zXmlHttp.createRequest();
     oXmlHttp.open("post", rUrl, true);
     oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

     oXmlHttp.onreadystatechange = function () {
      if (oXmlHttp.readyState == 4) {
       if (oXmlHttp.status == 200) {
        oDiv.innerHTML = oXmlHttp.responseText;
        //validaciones del usuario
        myFake = document.frmfake;
        document.forma.p_existe.value =  myFake.p_existe.value;
        
        if ( myFake.p_existe.value == "P" ) {
           alert ("Ya existe una sede con ese nombre");
           
           myForm.p_nombre.value = "";
        }
        else if ( myFake.p_existe.value == "U" ) {
           var nombre = myFake.p_nombre.value ;
           
           
           if (confirm ("la sede "+nombre+" se encuentra inactiva.\n\nDesea aactivarla ?") ){
            myForm.p_id_sede.value = myFake.p_id_sede.value;
            
            myForm.p_existe.value = "U";
            myForm.submit();
           }
        }
        else {
           myForm.p_existe.value = "N";
           myForm.p_pais.focus();
        }
       } else {
        oDiv.innerHTML ="<img src=\"skins/<?php echo($skin); ?>/opt_eliminar.png\"><b>AJAX: Problema al traer datos</b>";
       }
      }
     };
     oXmlHttp.send(rBody); 
     return;
  }
  </script>
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="titulo">CONFIGURACI&Oacute;N DE SEDES</div>
     <div class="capa_form_sf">
        <form id="forma" name="forma" method="post" action="exec_upd_sede.php">
        <input type="hidden" name="p_existe" id="p_existe" value="<?php echo($v_id_sede); ?>"/>
       <?php if (!is_null($v_id_sede)){?>
        <input type="hidden" name="p_id_sede" id="p_id_sede" value="<?php echo($v_id_sede); ?>" />
        <?php } ?>
        <table width="80%" border="0" cellpadding="0" cellspacing="0">
          <tr>
			<th>Nombre:</th>
            <td><input type="text" name="p_nombre" id="p_nombre" size="30" maxlength="50" value="<?php if(!is_null($v_id_sede)){echo($r_sede['nombre']);} ?>" onChange="setTimeout('validar_sede();', 0);"/></td>
          </tr>
          <tr>
			<th>Pais:</th>
            <td><input type="text" name="p_pais" id="p_pais" size="30" maxlength="50" value="<?php if(!is_null($v_id_sede)){echo($r_sede['direccion']);} ?>" /></td>
          </tr>
          <tr>
			<th>Ciudad:</th>
            <td> <input type="text" name="p_ciudad" id="p_ciudad" size="8" maxlength="10" value="<?php if(!is_null($v_id_sede)){echo($r_sede['ciudad']);} ?>" /></td>
          </tr>
          <tr>
			<th>Direcci&oacute;n:</th>
            <td><input type="text" name="p_direccion" id="p_direccion" size="5" maxlength="5" value="<?php if(!is_null($v_id_sede)){echo($r_sede['direccion']);} ?>" /></td>
          </tr>
           <tr>
      <th>Tel&eacute;fono:</th>
            <td><input type="text" name="p_telefono" id="p_telefono" size="5" maxlength="5" value="<?php if(!is_null($v_id_sede)){echo($r_sede['telefono']);} ?>" /></td>
          </tr>
          <tr>
      <th>Domicilio :</th>
            <td><select name="p_domicilio" id="p_domicilio">
              <option value=""></option>
              <option value="S" <?php if (!is_null($v_id_sede) && $r_sede['domicilio'] == "S") {echo("Selected"); } ?>>S</option>
              <option value="N" <?php if (!is_null($v_id_sede) && $r_sede['domicilio'] == "N") {echo("Selected"); } ?>>N</option>
              </select></td>
          </tr>
           <tr>
      <th>N&uacute;mero de factura:</th>
            <td><input type="text" name="p_num_factura" id="p_num_factura" size="5" maxlength="5" value="<?php if(!is_null($v_id_sede)){echo($r_sede['Num_factura']);} ?>" /></td>
          </tr>
          <tr>
      <th>Prefijo de factura:</th>
            <td><input type="text" name="p_pref_factura" id="p_pref_factura" size="5" maxlength="5" value="<?php if(!is_null($v_id_sede)){echo($r_sede['Pref_factura']);} ?>" /></td>
          </tr>
          <tr>
      <th>Activa:</th>
            <td><select name="p_activa" id="p_activa">
              <option value=""></option>
              <option value="S" <?php if (!is_null($v_id_sede) && $r_sede['Activa'] == "S") {echo("Selected"); } ?>>S</option>
              <option value="N" <?php if (!is_null($v_id_sede) && $r_sede['Activa'] == "N") {echo("Selected"); } ?>>N</option>
              </select></td>
          </tr>
          <tr>
              <td colspan="2" align="center"><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Guardar" onclick="document.forma.submit();" />
              &nbsp; <input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Regresar" onclick="javascript:top.GB_hide();" /> </td>
          </tr>
        </table>
        </form>
     </div>
  <!-- InstanceEndEditable -->
  <div id="fakefrmdiv"></div>
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