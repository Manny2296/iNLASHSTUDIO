<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/facturacion_utl.php");
include ($path."/lib/sedes_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) { 
	if ( validar_permisos ($conn, 'factura_frm.php') ) {
		

		if (isset($_REQUEST['p_id_sede'])) {
			$v_id_sede = $_REQUEST['p_id_sede'];
			
		}else{
			$v_id_sede = null;
		}
		$v_editar = 'S';
		$v_estado = null;
		$v_numero_fact = null;
		$r_factura = null;
		$v_medio = 'Efectivo';
		if (isset($_REQUEST['p_id_factura'])) {
			$v_id_factura = $_REQUEST['p_id_factura'];
		} else {
			$v_id_factura = get_factura_proc($conn,$v_id_sede);
		}
		$v_nomusuario = null;
		$v_hoy = new DateTime();
	    $v_fecha = $v_hoy->format('d-m-Y');
		if (isset($_REQUEST['p_id_usuario'])) {
			$v_id_usuario = $_REQUEST['p_id_usuario'];
		} else {
			$v_id_usuario = null;
		}
		if (!is_null($v_id_factura)) {
			$r_factura = datos_factura($conn, $v_id_factura);
			$v_numero_fact = $r_factura['num_factura'];
			$v_id_usuario = $r_factura['id_usuario'];
			$v_nomusuario = $r_factura['nomcliente'];
			$v_estado = $r_factura['estado'];
            $v_fecha = $r_factura['fecha'];
			
			if ($v_estado != 'PRC') {
				$v_editar = 'N';
			}
			switch($r_factura['tipo_pago']){
				case 'EF':
					$v_medio = 'Efectivo';
					break;
				case 'TD':
					$v_medio = 'Tarjeta Débito';
					break;
				case 'TC':
					$v_medio = 'Tarjeta Crédito';
					break;
				default:
					$v_medio = 'Cheque';
					break;
			}
		}
		if (is_null($v_numero_fact)) {
			$v_numero_fact = obtener_numfactura($conn,$v_id_sede);
		}	
?>
<html><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Factura</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" language="javascript" src="<?php echo ("/".$instdir); ?>/lib/popcalendar.js"></script>
<script type="text/javascript" src="lib/autosuggest/js/bsn.AutoSuggest_c_2.0.js"></script>
<script type="text/javascript" language="javascript" src="lib/zxml.js"></script>

<script type="text/javascript" language="javascript">
var tipoItem = null;
<?php if(is_null($v_id_factura) && !is_null($v_id_usuario)) { ?>
window.onload = function() {
	setTimeout("autoguardar();", 0);	
}
<?php } elseif(!is_null($v_id_factura)) {?>
window.onload = function() {
	setTimeout("getDetalle();", 0);	
}
<?php } ?>
function refrescar() {
	document.frmrefresh.submit();
}
function getDetalle(){
	myForm = document.frmfactura;
	var rUrl = "ajax_detalle_factura.php";
	var tipo = "<?php if($v_editar == 'S') { echo ("editar"); } else { echo("consultar"); } ?>";
	var rBody = "p_tipo="+tipo+"&p_id_factura="+myForm.p_id_factura.value+"&p_id_sede="+myForm.p_id_sede.value;
	oDiv = document.getElementById ("detallediv");
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
function autoguardar(){
	myForm = document.frmfactura;
	var rUrl = "ajax_upd_factura.php";
	var id_usuario = myForm.p_id_usuario.value;
	var id_sede = myForm.p_id_sede.value;
	
	var fecha = myForm.p_fecha.value;
	var rBody = "p_fecha="+fecha+"&p_id_usuario="+id_usuario+"&p_id_sede="+id_sede;
	var oXmlHttp = zXmlHttp.createRequest();
	
	oXmlHttp.open("post", rUrl, true);
	oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
			 if (oXmlHttp.status == 200) {
				refrescar();
			 } else {
				alert("La factura no pudo ser creada");
			 }
		  }
	   };
	   oXmlHttp.send(rBody); 
	   return;
}
function delFactura() {
	myForm = document.frmfactura;
	var rUrl = "ajax_upd_factura.php";
	var id_factura = myForm.p_id_factura.value;
	var rBody = "p_id_factura="+id_factura;
	var oXmlHttp = zXmlHttp.createRequest();
	
	oXmlHttp.open("post", rUrl, true);
	oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
			 if (oXmlHttp.status == 200) {
				top.GB_hide();
				top.refrescar();
			 } else {
				alert("La factura no pudo ser eliminada");
			 }
		  }
	   };
	   oXmlHttp.send(rBody); 
	   return;
}
function getListaItem(objChk) {
	if(objChk.checked){
		var valor = objChk.value;
	} else {
		return;
	}
	myForm = document.frmfactura;
	var id_sede = myForm.p_id_sede.value;
	var rUrl = "ajax_lista_productos.php";
	var rBody = "p_tipo="+valor+"&p_id_sede="+id_sede;
	var oXmlHttp = zXmlHttp.createRequest();
	oDiv = document.getElementById ("productosdiv");
	oDiv.innerHTML = "<img src=\"skins/<?php echo($skin); ?>/loader.gif\"><b>Consultando...</b>";
	
	oXmlHttp.open("post", rUrl, true);
	oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
			 if (oXmlHttp.status == 200) {
				tipoItem = valor;
				oDiv.innerHTML = oXmlHttp.responseText;
				setTimeout("getPrecio();", 10);
			 } else {
				oDiv.innerHTML ="<img src=\"skins/<?php echo($skin); ?>/opt_eliminar.png\"><b>AJAX: Problema al traer datos</b>";
			 }
		  }
	   };
	   oXmlHttp.send(rBody); 
	   return;
}
function addItem(){
	myForm = document.frmdetalle;
	
	if(tipoItem == "servicio") {
		var valor = myForm.p_id_servicio[myForm.p_id_servicio.selectedIndex].value;
		var id_sede = myForm.p_id_sede.value;
	} else {
		var valor = myForm.p_id_producto[myForm.p_id_producto.selectedIndex].value;
		var id_sede = null;
	}
	var cantidad = myForm.p_cantidad.value;
	var pordto   = myForm.p_pordto.value;
	var valor_unitario = myForm.p_valor_unitario.value;
	var id_factura = myForm.p_id_factura.value;
	
	var rUrl = "ajax_upd_detalle.php";
	var rBody = "p_tipo="+tipoItem+"&p_valor="+valor+"&p_pordto="+pordto+"&p_cantidad="+cantidad+"&p_id_factura="+id_factura+"&p_valor_unitario="+valor_unitario+"&p_id_sede="+id_sede;
	var oXmlHttp = zXmlHttp.createRequest();
	oDiv = document.getElementById ("detallediv");
	oDiv.innerHTML = "<img src=\"skins/<?php echo($skin); ?>/loader.gif\"><b>Consultando...</b>";
	
	oXmlHttp.open("post", rUrl, true);
	oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
			 if (oXmlHttp.status == 200) {
				setTimeout("getDetalle();", 0);
			 } else {
				oDiv.innerHTML ="<img src=\"skins/<?php echo($skin); ?>/opt_eliminar.png\"><b>AJAX: Problema al traer datos</b>";
			 }
		  }
	   };
	   oXmlHttp.send(rBody); 
	   return;	
}
function delItem(itemID){
	var rUrl = "ajax_del_detalle.php";
	var rBody = "p_id_detalle="+itemID;
	var oXmlHttp = zXmlHttp.createRequest();
	oDiv = document.getElementById ("detallediv");
	oDiv.innerHTML = "<img src=\"skins/<?php echo($skin); ?>/loader.gif\"><b>Consultando...</b>";
	
	oXmlHttp.open("post", rUrl, true);
	oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
			 if (oXmlHttp.status == 200) {
				setTimeout("getDetalle();", 0);
			 } else {
				oDiv.innerHTML ="<img src=\"skins/<?php echo($skin); ?>/opt_eliminar.png\"><b>AJAX: Problema al traer datos</b>";
			 }
		  }
	   };
	   oXmlHttp.send(rBody); 
	   return;
}
function getPrecio(){
	myForm = document.frmdetalle;
	
	if(tipoItem == "servicio") {
		var valor = myForm.p_id_servicio[myForm.p_id_servicio.selectedIndex].value;
		var rBody = "p_id_servicio="+valor;
	} else {
		var valor = myForm.p_id_producto[myForm.p_id_producto.selectedIndex].value;
		var rBody = "p_id_producto="+valor;
	}
	var rUrl = "ajax_get_precio_base.php";
	var oXmlHttp = zXmlHttp.createRequest();
	
	oXmlHttp.open("post", rUrl, true);
	oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
			 if (oXmlHttp.status == 200) {
				myForm.p_valor_unitario.value = oXmlHttp.responseText;
			 } else {
				myForm.p_valor_unitario.value = 0;
			 }
		  }
	   };
	   oXmlHttp.send(rBody); 
	   return;
}
function liquidar(){
	myForm = document.frmfactura;
	var rUrl = "ajax_upd_factura.php";
	var id_factura = myForm.p_id_factura.value;
	var id_sede = myForm.p_id_sede.value;

	var estado = "FAC";
	var fecha = myForm.p_fecha.value;
	var medio = myForm.p_tipo_pago.options[myForm.p_tipo_pago.selectedIndex].value;
	var rBody = "p_id_factura="+id_factura+"&p_estado="+estado+"&p_tipo_pago="+medio+"&p_fecha="+fecha+"&p_id_sede="+id_sede;

	var oXmlHttp = zXmlHttp.createRequest();
	
	oXmlHttp.open("post", rUrl, true);
	oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
			 if (oXmlHttp.status == 200) {
				window.open("factura_prn_txt.php?p_id_factura="+id_factura, "factprnwin", "width=400,height=400,scrollbars=auto");
				return;
			 } else {
				alert("La factura no pudo ser 'liquidada");
			 }
		  }
	   };
	   oXmlHttp.send(rBody); 
	   return;
}
function imprimir() {
	var id_factura = document.frmfactura.p_id_factura.value;
	window.open("factura_prn_txt.php?p_id_factura="+id_factura, "factprnwin", "width=400,height=400,scrollbars=auto");
}
</script>
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="titulo">Factura de Venta No. <?php echo($v_numero_fact); ?></div>
     <div class="capa_form_sf">
        <form id="frmfactura" name="frmfactura" method="post" action="#">
        <input type="hidden" name="p_id_usuario" id="p_id_usuario" value="<?php echo($v_id_usuario); ?>" />
        <?php if (!is_null($v_id_factura)) { ?>
        <input type="hidden" name="p_id_factura" id="p_id_factura" value="<?php echo($v_id_factura); ?>" />
        <?php } ?>
        <input type="hidden" name="p_id_sede" id="p_id_sede" value="<?php echo($v_id_sede); ?>" />
        <table width="80%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <tr>
			<th width="20%">Fecha:</th>
            <td><input type="text" name="p_fecha" id="p_fecha" size="12" maxlength="12" readonly value="<?php echo($v_fecha); ?>" onClick="popUpCalendar(this, frmfactura.p_fecha);" /></td>
          </tr>
		  <?php if(is_null($v_id_usuario)) { ?>
          <tr>
            <tr>
			<th width="20%">Cliente:</th>
            <td><input type="text" size="30" maxlength="200" name="p_usuario" id="p_usuario" /></td>
          </tr>
          <?php } else { 
		  			if (is_null($v_nomusuario)) {
						$v_nomusuario = nombre_cliente($conn, $v_id_usuario);
					}
		  ?>
          <tr>
            <tr>
			<th width="45%">Cliente:</th>
            <td><?php echo($v_nomusuario); ?></td>
          </tr>
          <?php } ?>
          <tr>
            <tr>
			<th width="45%">Medio de pago:</th>
            <?php if ($v_editar == 'S') { ?>
            <td><select name="p_tipo_pago" id="p_tipo_pago">
               <option value="CH" <?php if(!is_null($r_factura) && $r_factura['tipo_pago'] == "CH") { echo("selected"); } ?>>Cheque</option>
               <option value="EF" <?php if(!is_null($r_factura) && $r_factura['tipo_pago'] == "EF") { echo("selected"); } ?>>Efectivo</option>
               <option value="TC" <?php if(!is_null($r_factura) && $r_factura['tipo_pago'] == "TC") { echo("selected"); } ?>>Tarjeta de Cr&eacute;dito</option>
               <option value="TD" <?php if(!is_null($r_factura) && $r_factura['tipo_pago'] == "TD") { echo("selected"); } ?>>Tarjeta D&eacute;bito</option>
               </select></td>
          <?php } else { ?>
              <td><?php echo($v_medio); ?></td>
          <?php } ?>
          </tr>
          <tr>
              <td colspan="2" align="center"><?php if($v_estado=="PRC"){?><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Liquidar e Imprimir" onClick="liquidar();" />&nbsp;<input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Eliminar" onClick="delFactura();" /><?php } elseif($v_estado != "ANL") {?><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Anular" onClick="delFactura();" /><?php } ?>&nbsp;<input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Imprimir" onClick="imprimir();" />&nbsp;<input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Regresar" onClick="javascript:top.GB_hide();javascript:top.refrescar();" /> </td>
          </tr>
        </table>
        </form>
        <form name="frmdetalle" id="frmdetalle" action="#" method="post">
        <?php if (!is_null($v_id_factura)) { ?>
        <input type="hidden" name="p_id_factura" id="p_id_factura" value="<?php echo($v_id_factura); ?>" />
        <?php } ?>
        <input  type="hidden" name="p_id_sede" id="p_id_sede" value="<?php echo($v_id_sede); ?>" />
        <div id="detallediv"></div>
        </form>
        <form action="factura_frm.php" name="frmrefresh" id="frmrefresh" method="post">
        <input  type="hidden" name="p_id_sede" id="p_id_sede" value="<?php echo($v_id_sede); ?>" />
        <input type="hidden" name="p_id_usuario" id="p_id_usuario" value="<?php echo($v_id_usuario); ?>" />
        <?php if (!is_null($v_id_factura)) { ?>
        <input  type="hidden" name="p_id_factura" id="p_id_factura" value="<?php echo($v_id_factura); ?>" />
        <?php } ?>
        </form>
     </div>
     <script type="text/javascript">
	        //implementacion de autosuggest
			var options = {
				script:"ajax_lista_clientes.php?p_id_sede=<?php echo($v_id_sede); ?>&" ,
				varname:"p_letras",
				json:true,
				callback: function (obj) { document.getElementById('p_id_usuario').value = obj.id; autoguardar(); }
			};
			var as_json = new AutoSuggest('p_usuario', options);
		</script>
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