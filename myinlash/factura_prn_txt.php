<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/facturacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

$v_id_factura = $_REQUEST['p_id_factura'];
$r_factura = datos_factura($conn, $v_id_factura);
$t_detalle = detalle_factura($conn, $v_id_factura);
$t_impuestos = impuestos_factura($conn, $v_id_factura);
$v_subtotal = 0;
$v_total = 0;
$v_descuento = 0; 
dbdisconn($conn);

header('Content-Type: plain/text; charset=iso-8859-2');
header('Content-Disposition: attachment; filename=factura_'.$r_factura['num_factura'].'.txt');
?>
Inlash & Co Sede: <?php echo($r_factura['nomsede']);  ?><?php echo("\r\n"); ?>
Nit. 526205738<?php echo("\r\n"); ?>
Dir. <?php echo($r_factura['direccion']);  ?><?php echo("\r\n"); ?>
Tel. <?php echo($r_factura['telefono']);  ?><?php echo("\r\n"); ?>
---------------------------------------<?php echo("\r\n"); ?>
<?php echo("\r\n"); ?>
<?php echo("\r\n"); ?>
---------------------------------------<?php echo("\r\n"); ?>
Convenio de Prestación de Servicios <?php echo("\r\n"); ?>
           Profesionales<?php echo("\r\n"); ?><?php echo("\r\n"); ?>
Factura No. <?php echo($r_factura['num_factura']."\r\n"); ?>
Fecha:                  <?php echo($r_factura['fecha']."\r\n"); ?>
Hora:                   <?php echo($r_factura['hora']."\r\n"); ?>
---------------------------------------<?php echo("\r\n"); ?>
Cliente: <?php echo(substr($r_factura['nomcliente'],0,30)."\r\n"); ?>
---------------------------------------<?php echo("\r\n"); ?>
    Artículo      Cant       Valor<?php echo("\r\n"); ?>
---------------------------------------<?php echo("\r\n"); ?>
<?php 
if(is_array($t_detalle)) { 
	foreach($t_detalle as $dato){  
		$v_subtotal += $dato['total'] + $dato['valor_descuento'] - $dato['iva'];
		$v_descuento += $dato['valor_descuento'];
		 if(!is_null($dato['nomservicio'])){ 
   	  		$v_detalle = substr($dato['nomservicio'],0,15); 
	  	 } else { 
		 	$v_detalle = substr($dato['nomproducto'],0,15); 
	  	 } 
		 while(strlen($v_detalle) < 19) {
			 $v_detalle .= " ";
		 }
		 $v_detalle .= $dato['cantidad'];
		 while(strlen($v_detalle) < 26) {
			 $v_detalle .= " ";
		 }
		 $v_valor = "$ ".number_format(($dato['cantidad']*$dato['valor_unitario']), 2, '.', ',');
		 $v_cant_espacios = 39 - strlen($v_detalle) - strlen($v_valor);
		 if ($v_cant_espacios > 0) {
			 for($x=0;$x<$v_cant_espacios;$x++){
				$v_detalle .= " ";
		 	}
		 }
		 $v_detalle .= $v_valor;
		 echo($v_detalle."\r\n");
	}
}
?>
---------------------------------------<?php echo("\r\n"); ?>
<?php
//subtotal
$v_detalle = "Subtotal";
$v_cant_espacios = 39 - strlen($v_detalle);
$v_valor = "$ ".number_format($v_subtotal, 2, ".", ",");
$v_cant_espacios -= strlen($v_valor);
for($x=0;$x<$v_cant_espacios;$x++){
	$v_detalle .= " ";
}
$v_detalle .= $v_valor;
echo($v_detalle."\r\n");
//descuento
$v_detalle = "Descuento";
$v_cant_espacios = 39 - strlen($v_detalle);
$v_valor = "$ ".number_format($v_descuento, 2, ".", ",");
$v_cant_espacios -= strlen($v_valor);
for($x=0;$x<$v_cant_espacios;$x++){
	$v_detalle .= " ";
}
$v_detalle .= $v_valor;
echo($v_detalle."\r\n");
//impuestos
/*
foreach($t_impuestos as $dato){
	$v_detalle = "Iva (".$dato['impuesto']."%)";
	$v_cant_espacios = 39 - strlen($v_detalle);
	$v_valor = "$ ".number_format($dato['valor'], 2, ".", ",");
	$v_cant_espacios -= strlen($v_valor);
	for($x=0;$x<$v_cant_espacios;$x++){
		$v_detalle .= " ";
	}
	$v_detalle .= $v_valor;
	echo($v_detalle."\r\n");
	$v_total += $dato['valor'];
}
*/
//total
$v_detalle = "Total";
$v_cant_espacios = 39 - strlen($v_detalle);
$v_valor = "$ ".number_format(($v_subtotal + $v_total - $v_descuento), 2, ".", ",");
$v_cant_espacios -= strlen($v_valor);
for($x=0;$x<$v_cant_espacios;$x++){
	$v_detalle .= " ";
}
$v_detalle .= $v_valor;
echo($v_detalle."\r\n");
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
?>
---------------------------------------<?php echo("\r\n"); ?>
Medio de Pago: <?php echo($v_medio."\r\n"); ?>
---------------------------------------<?php echo("\r\n"); ?>
Cajero: <?php echo(substr(strtoupper($r_factura['cajero']),0,30)."\r\n"); ?>
GRACIAS POR UTILIZAR NUESTROS SERVICIOS<?php echo("\r\n"); ?>
---------------------------------------<?php echo("\r\n"); ?>
<?php echo("\r\n"); ?>
<?php echo("\r\n"); ?>