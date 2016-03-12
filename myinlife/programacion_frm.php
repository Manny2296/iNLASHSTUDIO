<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/programacion_utl.php");
include ($path."/lib/servicios_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'programacion_frm.php') ) {
		$v_id_servicio = $_REQUEST['p_id_servicio'];
		$v_hora = DateTime::createFromFormat('d-m-Y H:i', '01-01-2001 '.$_REQUEST['p_hora']);
		$v_fecha = $_REQUEST['p_fecha'];
		$v_maquina = $_REQUEST['p_maquina'];
		$r_servicio = detalle_servicio ($conn, $v_id_servicio);
		$t_horas = horas_fin_servicio($conn, $v_id_servicio, $v_maquina, $v_fecha, $_REQUEST['p_hora']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Programaci&oacute;n de sesiones</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" src="lib/autosuggest/js/bsn.AutoSuggest_c_2.0.js"></script>
<script type="text/javascript" src="lib/zxml.js" language="javascript"></script>
<script type="text/javascript" language="javascript">
window.onload = function(){
	get_bloqueo();
}
function validar(){
	myForm = document.forma;
	if (myForm.p_id_usuario.value==""){
		alert("Por favor ingrese un cliente para realizar la programación");
		myForm.p_nombre.focus();
		return;
	} 
	myForm.submit();
}
function verificar_ficha(id_usuario){
	myForm = document.forma;
	var fecha = myForm.p_fecha.value;
	var servicio = myForm.p_id_servicio.value;
	
	var rUrl = "ajax_verificar_ficha.php";
	var rBody = "p_id_usuario="+id_usuario+"&p_id_servicio="+servicio+"&p_fecha="+fecha;
	oDiv = document.getElementById ("respdiv");
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
function get_bloqueo(){
	myForm = document.forma;
	var fecha = myForm.p_fecha.value;
	var maquina = myForm.p_maquina.value;
	var servicio = myForm.p_id_servicio.value;
	var hora_ini = myForm.p_hora.value;
	var hora_fin = myForm.p_hora_fin.value;
	
	var rUrl = "ajax_bloqueo_maquinas.php";
	var rBody = "p_id_servicio="+servicio+"&p_fecha="+fecha+"&p_hora_ini="+hora_ini+"&p_hora_fin="+hora_fin+"&p_maquina="+maquina;
	oDiv = document.getElementById ("maquinasdiv");
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
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="sub_tit">PROGRAMACI&Oacute;N DE SESIONES</div>
     <div class="capa_form_sf">
        <form id="forma" name="forma" method="post" action="exec_upd_program_adm.php">
        <input type="hidden" name="p_id_usuario" id="p_id_usuario" />
        <input type="hidden" name="p_fecha" id="p_fecha" value="<?php echo($v_fecha); ?>" />
        <input type="hidden" name="p_hora" id="p_hora" value="<?php echo($v_hora->format('H:i')); ?>" />
        <input type="hidden" name="p_id_servicio" id="p_id_servicio" value="<?php echo($v_id_servicio); ?>" />
        <input type="hidden" name="p_maquina" id="p_maquina" value="<?php echo($v_maquina); ?>" />
        <table width="90%" border="0" cellpadding="0" cellspacing="0">
          <tr>
			<th width="45%">Cliente:</th>
            <td><input type="text" size="30" maxlength="200" name="p_usuario" id="p_usuario" /></td>
          </tr>
          <tr>
			<th width="45%">Servicio a programar:</th>
            <td><?php echo($r_servicio['nombre']); ?></td>
          </tr>
          <tr>
			<th width="45%">Fecha:</th>
            <td><?php echo($v_fecha); ?></td>
          </tr>
          <tr>
			<th width="45%">Hora de Inicio:</th>
            <td><?php echo($v_hora->format('h:i a')); ?></td>
          </tr>
          <tr>
			<th width="45%">Hora de Finalizaci&oacute;n:</th>
            <td><select name="p_hora_fin" id="p_hora_fin" onchange="setTimeout('get_bloqueo();', 0);">
                   <?php foreach($t_horas as $dato) {
                   			$v_hora = DateTime::createFromFormat('d-m-Y h:i a', '01-01-2001 '.$dato);
					?>
                   <option value="<?php echo($v_hora->format('H:i')); ?>"><?php echo($v_hora->format('h:i a')); ?></option>
                   <?php } ?>
                 </select></td> 	
            </td>
          </tr>
          <tr>
			<th width="45%">Es una sesión de cortes&iacute;a:</th>
            <td><input type="checkbox" name="p_cortesia" id="p_cortesia" value="S" /></td>
          </tr>
          <tr>
			<th width="45%">Comentarios:</th>
            <td><textarea name="p_comentarios" id="p_comentarios" cols="30" rows="5"></textarea></td>
          </tr>
        </table>
        <div id="maquinasdiv"></div>
        <div id="respdiv"></div>
        <table width="90%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="2" align="center"><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Programar" onClick="validar();" />
              &nbsp; <input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Cerrar" onClick="javascript:top.GB_hide();" /> </td>
          </tr>
         </table>
        </form>
      </div>
  <!-- InstanceEndEditable -->
  </div>
<script type="text/javascript">
	        //implementacion de autosuggest
			var options = {
				script:"ajax_lista_clientes.php?",
				varname:"p_letras",
				json:true,
				callback: function (obj) { document.getElementById('p_id_usuario').value = obj.id; verificar_ficha(obj.id); }
			};
			var as_json = new AutoSuggest('p_usuario', options);
		</script>
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