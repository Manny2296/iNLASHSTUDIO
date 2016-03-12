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
	if ( validar_permisos ($conn, 'programacion_lst.php') ) {
		$t_servicios = lista_servicios_prog ($conn, 'all', null);
		if (isset($_POST['p_id_servicio'])) {
			$v_id_servicio = $_POST['p_id_servicio'];
		} else {
			$v_id_servicio = $t_servicios[0]['id_servicio'];
		}
		if (isset($_POST['p_fecha'])) {
			$v_fecha = DateTime::createFromFormat('d-m-Y', $_POST['p_fecha']);
		} else {
			$v_fecha = new DateTime();
		}
		$t_horas = lista_horas ($conn, $v_id_servicio);
		$v_cant_maquinas = numero_maquinas ($conn, $v_id_servicio);
		$v_maquina_act = 1;
		$v_interval = new DateInterval('P1D');
		$v_ayer = clone $v_fecha;
		$v_ayer->sub($v_interval);
		$v_manana = clone $v_fecha;
		$v_manana->add($v_interval);
		setlocale (LC_TIME, 'esp', 'es_ES', 'es_ES.UTF-8', 'Spanish_Spain.1252');
		$v_fecha_txt = strftime ("%A, %d de %B de %Y", strtotime($v_fecha->format('m/d/Y')));
		
		$v_hoy = new DateTime();
		/*
		if ($v_fecha < $v_hoy) {
			$v_editar = false;
		} else {
			$v_editar = true;
		}
		*/
		$v_editar = true;
?>
<html><!-- InstanceBegin template="/Templates/main_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>.:: MY INLIFE STUDIO - Programaci&oacute;n de clases por servicio ::.</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" language="JavaScript">
          var GB_ROOT_DIR = "<?php echo ($site_domain."/".$instdir); ?>/lib/greybox/greybox/";
		  pc_callback_fn = function(){ir_fecha(document.frmfecha.p_fecha.value);}
</script>
<link href="<?php echo ("/".$instdir); ?>/lib/greybox/greybox/gb_styles.css" rel="stylesheet" type="text/css">

<script type="text/javascript" language="javascript" src="<?php echo ("/".$instdir); ?>/lib/popcalendar.js"></script>
<script type="text/javascript" language="JavaScript" src="<?php echo ("/".$instdir); ?>/lib/greybox/greybox/AJS.js"></script>
<script type="text/javascript" language="JavaScript" src="<?php echo ("/".$instdir); ?>/lib/greybox/greybox/AJS_fx.js"></script>
<script type="text/javascript" language="JavaScript" src="<?php echo ("/".$instdir); ?>/lib/greybox/greybox/gb_scripts.js"></script>
<script type="text/javascript" language="javascript">
	function refrescar() {
		myForm = document.forma;
		myForm.action = "programacion_lst.php";
		myForm.submit();
	}
	function ir_fecha(p_fecha){
		document.forma.p_fecha.value = p_fecha;
		refrescar();
	}
	function reserva(p_maquina, p_hora){
		myForm = document.forma;
		var v_id_servicio = <?php echo($v_id_servicio); ?>;
		var p_fecha = myForm.p_fecha.value;
		var url = "<?php echo ("/".$instdir); ?>/programacion_frm.php?p_id_servicio="+v_id_servicio+"&p_fecha="+p_fecha+"&p_hora="+p_hora+"&p_maquina="+p_maquina;
		GB_showCenter("Programar sesi�n", url, 350, 600);
	}
	function cancelar(p_id_prog) {
		myForm = document.forma;
		myForm.p_id_programacion.value = p_id_prog;
		if (confirm("Se dispone a cancelar la reserva seleccionada.\n\nDesea Continuar?")){
			myForm.action = "exec_del_programacion_adm.php";
			myForm.submit();
		}
	}
	function detalle(p_id_prog) {
		var url = "<?php echo ("/".$instdir); ?>/programacion_asistencia_frm.php?p_id_programacion="+p_id_prog;
		GB_showCenter("Seguimiento de sesi�n", url, 500, 700);
	}
	function ver_ficha(id_cliente){
		var url = "<?php echo ("/".$instdir); ?>/cliente_ficha_frm.php?p_id_usuario="+id_cliente;
		GB_showCenter("Ficha antropom�tica del cliente", url, 430, 720);	  
	}
	function ver_pestanas(id_cliente){
		var url = "<?php echo ("/".$instdir); ?>/cliente_pestanas_frm.php?p_id_usuario="+id_cliente;
		GB_showCenter("Pesta�as del cliente", url, 500, 720);	  
	}
	function facturar(){
		var url = "<?php echo ("/".$instdir); ?>/factura_frm.php";
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
     <div id="contiene_barra">
        <div id="buscafecha"><a href="#" onClick="popUpCalendar(this, frmfecha.p_fecha);"><img src="skins/<?php echo($skin); ?>/calendar_search.png" alt="Buscar Fecha" title="Buscar Fecha" border="0" align="absmiddle" /></a>&nbsp;<a href="javascript:facturar();"><img src="skins/<?php echo($skin); ?>/icon_factura.png" alt="Crear una factura" title="Crear una factura" border="0" /></a></div>
      	<a href="javascript:ir_fecha('<?php echo($v_ayer->format('d-m-Y')); ?>');"><img src="skins/<?php echo($skin); ?>/atras.png" alt="Una fecha atr&aacute;s" border="0" align="absmiddle" /></a> <?php echo($v_fecha_txt); ?> <a href="javascript:ir_fecha('<?php echo($v_manana->format('d-m-Y')); ?>');"><img src="skins/<?php echo($skin); ?>/adelante.png" alt="Una fecha adelante" border="0" align="absmiddle" /></a></div><br />
        <form name="frmfecha" id="frmfecha" action="programacion_lst.php" method="post">
           <input type="hidden" name="p_fecha" id="p_fecha" />
        </form>  
        <form name="forma" id="forma" action="#" method="post">
          Servicio a programar: <select name="p_id_servicio" id="p_id_servicio" onChange="refrescar();">
          <?php foreach($t_servicios as $dato) { ?>
            <option value="<?php echo($dato['id_servicio']); ?>" <?php if($dato['id_servicio'] == $v_id_servicio) { echo("Selected"); } ?>><?php echo($dato['nombre']); ?></option>
          <?php } ?>
          </select>
          <input type="hidden" name="p_fecha" id="p_fecha" value="<?php echo($v_fecha->format('d-m-Y')); ?>" />
          <input type="hidden" name="p_id_programacion" id="p_id_programacion" />
        </form>
	 <?php 
	 
	 while ($v_maquina_act <= $v_cant_maquinas) {
		 $t_programacion = lista_horas_prog($conn, 'servicio', $v_id_servicio, $v_maquina_act, $v_fecha->format('d-m-Y'), null);
	 ?>
     <div class="ventana_maquina">
       <div class="titulo_ventana">ESTACI&Oacute;N <?php echo($v_maquina_act); ?></div>
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
                <div id="hora"><?php echo($v_hora_act->format('h:i a')); ?></div>
            </td>
          </tr>
		 <?php
			 } elseif (!is_null($v_hora_ini) && $v_hora_act >= $v_hora_ini && $v_hora_act < $v_hora_fin && $v_maquina == $v_maquina_act) {
		 ?> 
          <tr class="t_header_maquina">
            <td class="t_hora_reservado">
               <?php if ($v_editar) { ?>
               <div id="espacio_botones"><a href="javascript:cancelar(<?php echo($t_programacion[$v_pos]['id_programacion']); ?>);" class="button white small">Cancelar</a></div><br/>
               <?php } 
			   		 $v_id_programacion = $t_programacion[$v_pos]['id_programacion'];
					 $v_id_usuario = $t_programacion[$v_pos]['id_usuario'];
					 $v_ficha_antro = req_toma_medidas ($conn, $v_id_programacion );
					 $v_pestanas = req_pestanas ($conn, $v_id_programacion);
			   ?>
               <div id="hora"><?php echo($v_hora_act->format('h:i a')); ?></div>
               <div id="disponible"><a href="javascript:detalle(<?php echo($t_programacion[$v_pos]['id_programacion']); ?>);"><?php echo($t_programacion[$v_pos]['nombres'].' '.$t_programacion[$v_pos]['apellidos']); ?></a><div id="espacio_botones"><?php if ($v_ficha_antro == "S") { ?>
                  <a href="javascript:ver_ficha('<?php echo($v_id_usuario); ?>');" class="button white nopad"><img src="skins/<?php echo($skin); ?>/icon_ficha.png" alt="Actualizar Ficha Antropom&eacute;trica" title="Actualizar Ficha Antropom&eacute;trica" border="0" /></a><?php } if ($v_pestanas) {?><a href="javascript:ver_pestanas('<?php echo($v_id_usuario); ?>');" class="button white nopad"><img src="skins/<?php echo($skin); ?>/icon_eye.png" alt="Diligenciar Informaci&oacute;n de pesta&ntilde;as" title="Diligenciar Informaci&oacute;n de pesta&ntilde;as" border="0" /></a><?php } ?></div></div>
            </td>
          </tr>
          <?php
			 } else {
		  ?>
          <tr class="t_header_maquina">
            <td class="t_hora_disp">
            <?php if ($v_editar) { ?>
              <div id="espacio_botones"><a href="javascript:reserva(<?php echo($v_maquina_act.", '".$v_hora_act->format("H:i")."'"); ?>);" class="button white small">Reservar</a></div><br />
            <?php } ?>
              <div id="hora"><?php echo($v_hora_act->format('h:i a')); ?></div>
              <div id="disponible">&nbsp;</div>
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