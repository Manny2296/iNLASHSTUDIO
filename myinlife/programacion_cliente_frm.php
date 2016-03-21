<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/programacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'programacion_cliente_frm.php') ) {
		$v_id_usuario = $_SESSION['id_usuario'];
		$v_nombre = $_SESSION['nombre'];
		$v_fecha = $_REQUEST['p_fecha'];
		if (isset($_POST['p_id_servicio'])) {
			$v_id_servicio = $_POST['p_id_servicio'];
			$t_horas = lista_horas ($conn, $v_id_servicio);
			$v_cant_maquinas = numero_maquinas ($conn, $v_id_servicio);
			$v_maquina_act = 1;
		} else {
			$v_id_servicio = null;
		}
		$t_servicios = lista_servicios_prog ($conn, 'cliente', $v_id_usuario);
		
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
<script type="text/javascript" language="javascript">
function refrescar(objSel){
   if (objSel.selectedIndex==0){
	   alert("Por favor seleccione un servicio");
	   objSel.focus();
	   return;
   }
   myForm = document.forma;
   myForm.action = "programacion_cliente_frm.php";
   myForm.submit();
}
function validar(){
	myForm = document.forma;
	var ok = false;
	if (myForm.p_id_servicio.selectedIndex==0){
	   alert("Por favor seleccione un servicio");
	   objSel.focus();
	   return;
   }
   try{
	   for(var x in myForm.p_reserva){
		   if(myForm.p_reserva[x].checked){
			   codigo = myForm.p_reserva[x].value.split("|");
			   //alert("reserva: "+myForm.p_reserva[x].value);
			   myForm.p_maquina.value = codigo[0];
			   myForm.p_hora.value = codigo[1];
			   ok = true;
		   }
	   }
   }catch(e){
	   try{
		   if(myForm.p_reserva.checked){
			   codigo = myForm.p_reserva.value.split("|");
			   myForm.p_maquina.value = codigo[0];
			   myForm.p_hora.value = codigo[1];
			   ok = true;
		   }
	   }catch(e){
		   alert("No hay una hora y máquina seleccionada para la programación de la sesión");
		   return;
	   }
   }
   myForm.submit();
}
</script>
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="sub_tit">PROGRAMACI&Oacute;N DE SESIONES</div>
     <div class="capa_form_sf">
        <form id="forma" name="forma" method="post" action="exec_upd_program_clie.php">
        <input type="hidden" name="p_fecha" id="p_fecha" value="<?php echo($v_fecha); ?>" />
        <input type="hidden" name="p_hora" id="p_hora" />
        <input type="hidden" name="p_maquina" id="p_maquina" />
        <table width="90%" border="0" cellpadding="0" cellspacing="0">
          <tr>
			<th>Cliente:</th>
            <td><?php echo($v_nombre); ?></td>
          </tr>
          <tr>
			<th>Servicio a programar:</th>
            <td><select name="p_id_servicio" id="p_id_servicio" onchange="refrescar(this);">
            <option value=""></option>
            <?php foreach($t_servicios as $dato) { ?>
            <option value="<?php echo($dato['id_servicio']); ?>" <?php if ($dato['id_servicio'] == $v_id_servicio) { echo("Selected"); } ?>><?php echo($dato['nombre']); ?></option>
            <?php } ?>
            </select></td>
          </tr>
          <tr>
			<th>Fecha:</th>
            <td><?php echo($v_fecha); ?></td>
          </tr>
          </table>
          <?php if (!is_null($v_id_servicio)) {
	 				while ($v_maquina_act <= $v_cant_maquinas) {
		 				$t_programacion = lista_horas_prog($conn, 'cliente', $v_id_servicio, $v_maquina_act, $v_fecha, $v_id_usuario);
	 ?>
     <div class="ventana_maquina">
       <div class="titulo_ventana">M&Aacute;QUINA <?php echo($v_maquina_act); ?></div>
	   <div class="contenido_ventana">
         <table width=100% border="0" align="center" cellpadding="0" cellspacing="0">
         <?php
		 $v_pos=0;
		 $v_hora_ini = null;
		 $v_hora_fin = null;
		 foreach($t_horas as $dato) {
			 $v_hora_act = DateTime::createFromFormat('d-m-Y H:i', '01-01-2001 '.$dato);
			 if (!is_null($v_hora_fin) && $v_hora_fin == $v_hora_act) {
				 $v_pos++;
			 }
			 if (count($t_programacion) > $v_pos){
				 $v_hora_ini = DateTime::createFromFormat('d-m-Y H:i', '01-01-2001 '.$t_programacion[$v_pos]['hora_ini']);
				 $v_hora_fin = DateTime::createFromFormat('d-m-Y H:i', '01-01-2001 '.$t_programacion[$v_pos]['hora_fin']);
				 $v_maquina = $t_programacion[$v_pos]['maquina'];
			 }
			 if (!is_null($v_hora_ini) && $v_hora_act >= $v_hora_ini && $v_hora_act < $v_hora_fin && $v_maquina == 0) {
		 ?>
          <tr class="t_header_maquina">
            <td class="t_hora_nodisp">
                <div id="hora"><?php echo($v_hora_act->format('H:i')); ?></div>
            </td>
          </tr>
		 <?php
			 } elseif (!is_null($v_hora_ini) && $v_hora_act >= $v_hora_ini && $v_hora_act < $v_hora_fin && $v_maquina == $v_maquina_act) {
		 ?> 
          <tr class="t_header_maquina">
            <td class="t_hora_nodisp">
                <div id="hora"><?php echo($v_hora_act->format('H:i')); ?></div>
            </td>
          </tr>
          <?php
			 } else {
		  ?>
          <tr class="t_header_maquina">
            <td class="t_hora_disp">
              <div id="hora"><?php echo($v_hora_act->format('H:i')); ?></div>
              <div id="disponible"><input type="radio" name="p_reserva" id="p_reserva" value="<?php echo($v_maquina_act."|".$v_hora_act->format("H:i")); ?>" /></div>
            </td>
          </tr>
          <?php
			 }
		 }
		  ?>
         </table>
       </div>
     </div>
     <?php
	    $v_maquina_act++;
	 }
	 ?>
        <table width="90%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="2" align="center"><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Programar" onClick="validar();" />
              &nbsp; <input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Cerrar" onClick="javascript:top.GB_hide();" /> </td>
          </tr>
        </table>
        <?php } ?>
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