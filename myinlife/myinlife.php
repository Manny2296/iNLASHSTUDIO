<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/usuarios_utl.php");
include ($path."/lib/antropometria_utl.php");
include ($path."/lib/programacion_utl.php");
include ($path."/lib/parametros_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'myinlife.php') ) {
		$v_id_usuario = $_SESSION['id_usuario'];
		$r_nombre = nombres_usua ($conn, $v_id_usuario);
		$v_hoy = new DateTime();
		if (isset($_POST['p_fecha'])) {				
			$v_fecha_ini = DateTime::createFromFormat('d-m-Y', $_POST['p_fecha']);
		} else {
			$v_fecha_ini = new DateTime();
		}
		$v_dia = $v_fecha_ini->format('w');
		if($v_dia != 1) {
			if ($v_dia == 0) {
				$v_fecha_ini->sub(new DateInterval('P6D'));
			} else {
				$v_fecha_ini->sub(new DateInterval('P'.($v_dia-1).'D'));
			}
		}
		$v_fecha_fin = clone $v_fecha_ini;
		$v_fecha_fin->add(new DateInterval('P5D'));
		$v_interval = new DateInterval('P1D');
		$v_ayer = clone $v_fecha_ini;
		$v_ayer->sub($v_interval);
		$v_manana = clone $v_fecha_fin;
		$v_manana->add(new DateInterval('P2D'));
		
	    setlocale (LC_TIME, 'esp', 'es_ES', 'es_ES.UTF-8', 'Spanish_Spain.1252');
		$v_titulo_semana = strftime ("%d de %B", strtotime($v_fecha_ini->format('m/d/Y'))).' al '.strftime ("%d de %B", strtotime($v_fecha_fin->format('m/d/Y'))) ;
		$t_medidas = lista_medidas($conn, $v_id_usuario);
		$t_servicios = lista_servicios_cont ($conn, $v_id_usuario);
		$v_ult_fecha_med = get_ultima_fecha_medidas ($conn, $v_id_usuario);
		if (!is_null($v_ult_fecha_med)) {
			$v_fecha_upd = DateTime::createFromFormat('Y-m-d', $v_ult_fecha_med);
		} else {
			$v_fecha_upd = null;
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/client_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.::Inlife Studio - <?php echo($r_nombre['nombres'].' '.$r_nombre['apellidos']); ?> ::.</title>
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
<script type="text/javascript" src="lib/flash_chart/js/swfobject.js"></script>
<script type="text/javascript" language="javascript">
var p_id_medida = <?php echo($t_medidas[0]['id_medida']); ?>;
var p_id_servicio = <?php echo($t_servicios[0]['id_servicio']); ?>;
window.onload = function(){
	pintar_asistencia();
	pintar_graph_ficha();
}
function refrescar() {
	document.frmenvio.submit();
}
function cancelar(id_prog){
	if (confirm("Te dispones a cancelar una sesión programada.\n\nDeseas continuar?")){
		var rUrl = "ajax_cancelar_reserva.php";
		var rBody = "p_id_programacion="+id_prog;
		oDiv = document.getElementById ("respdiv");
		oDiv.innerHTML = "<img src=\"skins/<?php echo($skin); ?>/loader.gif\"><b>Consultando...</b>";
		var oXmlHttp = zXmlHttp.createRequest();
		oXmlHttp.open("post", rUrl, true);
		oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		oXmlHttp.onreadystatechange = function () {
			if (oXmlHttp.readyState == 4) {
				 if (oXmlHttp.status == 200) {
					oDiv.innerHTML = oXmlHttp.responseText;
					refrescar();
				 } else {
					oDiv.innerHTML ="<img src=\"skins/<?php echo($skin); ?>/opt_eliminar.png\"><b>AJAX: Problema al traer datos</b>";
				 }
			  }
		   };
		   oXmlHttp.send(rBody);
	}
	return;
}
function reserva(fecha){
	var url = "<?php echo ("/".$instdir); ?>/programacion_cliente_frm.php?p_fecha="+fecha;
	GB_showCenter("Programación de sesiones", url, 430, 720);	  
}
function cambiar_obj(id_medida, objetivo){
	<?php if (!is_null($v_fecha_upd)) { ?>
	var fecha = "<?php echo($v_fecha_upd->format('d-m-Y')); ?>";
	<?php } else {?>
	var fecha = "";
	<?php } ?>
	if (objetivo == "subir"){
	   objetivo = 'S';
	} else if (objetivo == "bajar"){
	   objetivo = 'B';
	} else {
	   objetivo = 'I';
	}
	var rUrl = "ajax_upd_objetivo.php";
	var rBody = "p_fecha="+fecha+"&p_id_medida="+id_medida+"&p_objetivo="+objetivo;
	oDiv = document.getElementById ("respdiv");
	oDiv.innerHTML = "<img src=\"skins/<?php echo($skin); ?>/loader.gif\"><b>Consultando...</b>";
	var oXmlHttp = zXmlHttp.createRequest();
	oXmlHttp.open("post", rUrl, true);
	oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
			 if (oXmlHttp.status == 200) {
				oDiv.innerHTML = oXmlHttp.responseText;
				refrescar();
			 } else {
				oDiv.innerHTML ="<img src=\"skins/<?php echo($skin); ?>/opt_eliminar.png\"><b>AJAX: Problema al traer datos</b>";
			 }
		  }
	   };
	   oXmlHttp.send(rBody); 
	   return;
}
function ir_fecha(fecha){
	myForm = document.frmenvio;
	myForm.p_fecha.value = fecha;
	myForm.submit();
}
function pintar_asistencia() {
	var p_data = { "data-file":"data_asistencia_rep.php?p_id_servicio="+p_id_servicio };
	swfobject.embedSWF("lib/flash_chart/open-flash-chart.swf", "asistenciadiv", "100%", "150", "9.0.0", "expressInstall.swf", p_data );
}
function ver_ficha(id_cliente){
	var url = "<?php echo ("/".$instdir); ?>/cliente_ficha_frm.php?p_id_usuario=<?php echo($v_id_usuario); ?>";
	GB_showCenter("Tu Ficha antropomética", url, 430, 720);	  
}
function pintar_graph_ficha(){
	var p_data = { "data-file":"data_medidas_rep.php?p_id_medida="+p_id_medida };
	swfobject.embedSWF("lib/flash_chart/open-flash-chart.swf", "fichadiv", "100%", "500", "9.0.0", "expressInstall.swf", p_data );
}
<?php foreach($t_medidas as $dato){ ?>
function ver_medida_<?php echo($dato['id_medida']); ?>(){
	p_id_medida = <?php echo($dato['id_medida']); ?>;
	pintar_graph_ficha();
}
<?php } ?>
<?php foreach($t_servicios as $dato){ ?>
function ver_servicio_<?php echo($dato['id_servicio']); ?>(){
	p_id_servicio = <?php echo($dato['id_servicio']); ?>;
	pintar_asistencia();
}
<?php } ?>
</script>
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contendor">
     <?php include ($path."/layout_header.php"); ?>
     <div id="contenido">
	 <!-- InstanceBeginEditable name="contenido" -->
	 <div class="ventana_larga">
       <div class="titulo_ventana">Mi Perseverancia</div>
       <div class="contenido_ventana">
         <div id="asistenciadiv"></div>
       </div>
     </div>
     <div class="ventana">
       <div class="titulo_ventana">Mis Medidas<br />
       <span class="texto_peq"><a href="javascript:ver_ficha();">Ver todas mis tomas de medidas</a></span></div>
       <div class="contenido_ventana">
       <?php include($path."/portlet_ficha_frm.php"); ?>
       </div>
     </div>
     <div class="ventana">
       <div class="titulo_ventana">
       <a href="javascript:ir_fecha('<?php echo($v_ayer->format('d-m-Y')); ?>');"><img src="skins/<?php echo($skin); ?>/atras.png" alt="Una fecha atr&aacute;s" border="0" /></a> Mi programaci&oacute;n del <?php echo($v_titulo_semana); ?> <a href="javascript:ir_fecha('<?php echo($v_manana->format('d-m-Y')); ?>');"><img src="skins/<?php echo($skin); ?>/adelante.png" alt="Una fecha adelante" border="0" /></a></div>
       <div class="contenido_ventana">
       <?php include($path."/portlet_programacion_frm.php"); ?>
       </div>
     </div>
     <form name="frmenvio" id="frmenvio" action="myinlife.php" method="post">
     <input type="hidden" name="p_fecha" id="p_fecha" value="<?php echo($v_fecha_ini->format('d-m-Y')); ?>" />
     </form>
     <!-- Google Analytics Code -->
     <script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-12716966-1']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	 </script>
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