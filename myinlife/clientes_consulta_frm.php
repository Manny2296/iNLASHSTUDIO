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
	if ( validar_permisos ($conn, 'clientes_lst.php') ) {
		$t_tipo[0]['texto'] = "Número de documento de identidad";
		$t_tipo[0]['valor'] = "id";
		$t_tipo[1]['texto'] = "Apellidos o nombres";
		$t_tipo[1]['valor'] = "nombre";
    $inf_sede=null;
		if (!isset($_SESSION['id_sede'])){
      		$t_sede = lista_sedes ($conn,'S');
    	}else{
          $inf_sede = detalle_sede ($conn, $_SESSION['id_sede']);
      		$t_sede = $inf_sede['nombre'];
    	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/main_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.:: MY INLIFE STUDIO - Consulta de clientes del sistema ::.</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" language="JavaScript" src="lib/zxml.js"></script>
<script type="text/javascript" language="javascript">
	window.onload = function(){
		getParams();
	}
	function getParams(){
		myForm = document.forma;
		var v_valor = myForm.p_tipo[myForm.p_tipo.selectedIndex].value;
		var rUrl = "ajax_opciones_consulta_usua.php";
		var rBody = "p_tipo="+v_valor;
		oDiv = document.getElementById ("pardiv");
		oDiv.innerHTML = "<img src=\"skins/<?php echo($skin); ?>/loader.gif\"><b>Consultando...</b>";
		var oXmlHttp = zXmlHttp.createRequest();
		oXmlHttp.open("post", rUrl, true);
		oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
			if (oXmlHttp.status == 200) {
				oDiv.innerHTML = oXmlHttp.responseText;
			} else {
				oDiv.innerHTML ="<img src=\"skins/<?php echo($skin); ?>/opt_eliminar.png\"><b>AJAX: Problema al traer datos</b>";
			}
		}
		};
		oXmlHttp.send(rBody); 
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
	 <div class="titulo">CONSULTA DE CLIENTES INLIFE</div>
     <div class="capa_form">
        <form id="forma" name="forma" method="post" action="clientes_lst.php">
        <table width="80%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <th>Sede:</th>
            <td>
            <?php if(!isset($_SESSION['id_sede'])) {?>
              <select name="p_id_sede" id="p_id_sede" >
         <?php if(!is_array($t_sede)){echo ("<option value='No hay Sedes Registradas'>No hay sedes Registradas</option>");}else{echo ("");}?>
          <?php foreach($t_sede as $dato) { ?>
            <option value="<?php echo($dato['id_sede']); ?>" ><?php echo($dato['nombre']); ?></option>
          <?php } ?>
          </select>
            <?php } else {?>
            <?php echo($t_sede); ?><input type="hidden" name="p_id_sede" id="p_id_sede" value="<?php echo($inf_sede['id_sede']); ?>" />
            <?php }?>
            </td>
            <td></td>
            <td></td>
          </tr>
          <tr>
			<th>Tipo de consulta:</th>
            <td><select name="p_tipo" id="p_tipo" onchange="setTimeout('getParams()', 0);">
            <?php foreach($t_tipo as $dato){ ?>
              <option value="<?php echo($dato['valor']);?>"><?php echo($dato['texto']); ?></option>
            <?php } ?>
              </select></td>
          </tr>
          <tr>
            <th>Par&aacute;metro de Consulta:</th>
            <td><div id="pardiv"></div></td>
          </tr>
          <tr>
            <td colspan="2" align="center">
            <?php if(is_array($t_sede)|| !is_null($inf_sede)){ ?>
            <input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Consultar" onclick="document.forma.submit();" />
            <?php } ?>
              &nbsp;<input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Regresar" onclick="location.replace('mainsite.php');" /></td>
          </tr>
        </table>
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