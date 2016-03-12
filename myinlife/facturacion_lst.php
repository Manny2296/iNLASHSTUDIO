<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/facturacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'facturacion_lst.php') ) {
?>
<html><!-- InstanceBegin template="/Templates/main_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.:: MY INLIFE STUDIO - Reporte de Facturaci&oacute;n ::.</title>
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
<script type="text/javascript" language="javascript" src="lib/zxml.js"></script>
<script type="text/javascript" language="javascript" src="lib/popcalendar.js"></script>
<script type="text/javascript" language="javascript">
window.onload = function(){
	getParams();
}
function getParams(){
	try{
	  myForm = document.forma;
	  var tipo = myForm.p_tipo.options[myForm.p_tipo.selectedIndex].value;
	}catch(e){
	  var tipo = "cliente";
	}
	var rUrl = "ajax_filtro_facturas.php";
	var rBody = "p_tipo="+tipo;
	oDiv = document.getElementById ("filtrodiv");
	oDiv.innerHTML = "<img src=\"skins/<?php echo($skin); ?>/loader.gif\"><b>Consultando...</b>";
	var oXmlHttp = zXmlHttp.createRequest();
	oXmlHttp.open("post", rUrl, true);
	oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
			 if (oXmlHttp.status == 200) {
				oDiv.innerHTML = oXmlHttp.responseText;
				document.getElementById ("contiene_tabla").innerHTML = "";
			 } else {
				oDiv.innerHTML ="<img src=\"skins/<?php echo($skin); ?>/opt_eliminar.png\"><b>AJAX: Problema al traer datos</b>";
			 }
		  }
	   };
	   oXmlHttp.send(rBody); 
	   return;
}
function getResults(){
	myForm = document.forma;
	var tipo = myForm.p_tipo.options[myForm.p_tipo.selectedIndex].value;
	try{
		if (myForm.p_param.type == "text") { 
		    var params = myForm.p_param.value;
		} else {
			var params = myForm.p_param.options[myForm.p_param.selectedIndex].value;
		}
	}catch(e){
		var params = "all";
	}
	try {
		var fecha_ini = myForm.p_fecha_ini.value;
		var fecha_fin = myForm.p_fecha_fin.value;
	}catch(e){
		var fecha_ini = null;
		var fecha_fin = null;
	}
	var rUrl = "ajax_resultado_facturas.php";
	var rBody = "p_tipo="+tipo+"&p_param="+params+"&p_fecha_ini="+fecha_ini+"&p_fecha_fin="+fecha_fin;
	oDiv = document.getElementById ("contiene_tabla");
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
	function detalle(id_factura){
		var url = "<?php echo ("/".$instdir); ?>/factura_frm.php?p_id_factura="+id_factura;
		GB_showCenter("Detalle de la Factura", url, 500, 720);	  
	}
	function facturar(){
		try {
		   myForm = document.forma;
		   var tipo = myForm.p_tipo.options[myForm.p_tipo.selectedIndex].value;
		   if (tipo == "cliente" && myForm.p_param.selectedIndex != 0) {
			   var id_usuario = myForm.p_param.options[myForm.p_param.selectedIndex].value;
			   var url = "<?php echo ("/".$instdir); ?>/factura_frm.php?p_id_usuario="+id_usuario;
		   } else {
			   var url = "<?php echo ("/".$instdir); ?>/factura_frm.php";
		   }
		}catch(e){
			var url = "<?php echo ("/".$instdir); ?>/factura_frm.php";
		}
		GB_showCenter("Factura", url, 500, 780);	  
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
	 <div class="titulo">REPORTE DE FACTURACI&Oacute;N</div>
     <div class="capa_form">
       <div id="filtrodiv"></div>
     </div>
     <p>&nbsp;</p>
     <div id="contiene_tabla" align="center">
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