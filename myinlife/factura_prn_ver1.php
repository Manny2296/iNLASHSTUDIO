<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
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
//$t_impuestos = impuestos_factura($conn, $v_id_factura);
$t_impuestos = null;
$v_subtotal = 0;
$v_total = 0;
$v_descuento = 0; 
dbdisconn($conn);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="skins/<?php echo($skin); ?>/facturas.css" rel="stylesheet" type="text/css" />
<title>Factura de venta</title>
<script language="javascript" type="text/javascript">
window.onload = function() {
	window.print();
	window.close();
	return;
}
</script>
</head>

<body>
<table>
<tr>
  <td colspan="3">Inlash & Co</td>
</tr>
<tr>
  <td colspan="3">Nit. 526205738</td>
</tr>
<tr>
  <td colspan="3">Dir. Cr 17A # 122 - 45</td>
</tr>
<tr>
  <td class="separador" colspan="3">Tel.  313 400 7364</td>
</tr>

<tr>
  <td colspan="3">Convenio de Prestación de Servicios</td>
</tr>
<tr>
  <td colspan="3"><div align="center">Profesionales</div></td>
</tr>
<tr>
  <td colspan="2">Factura No.</td>
  <td><?php echo($r_factura['num_factura']); ?></td>
</tr>
<tr>
  <td colspan="2">Fecha:</td>
  <td><?php echo($r_factura['fecha']); ?></td>
</tr>
<tr>
  <td colspan="2" class="separador">Hora:</td>
  <td class="separador"><?php echo($r_factura['hora']); ?></td>
</tr>
<tr>
  <td class="separador">Cliente:</td>
  <td colspan="2" class="separador"><?php echo($r_factura['nomcliente']); ?></td>
</tr>
<tr>
  <th>Art&iacute;culo</th>
  <th>Cant</th>
  <th>Valor</th>
</tr>
<?php 
if(is_array($t_detalle)) { 
	foreach($t_detalle as $dato){  
		$v_subtotal += $dato['total'] + $dato['valor_descuento'] - $dato['iva'];
		$v_descuento += $dato['valor_descuento'];
?>
<tr>              
  <td><?php if(!is_null($dato['nomservicio'])){ 
          		echo(substr($dato['nomservicio'],0,15)); 
			} else { 
			    echo(substr($dato['nomproducto'],0,15)); 
			} ?></td>
  <td align="center"><?php echo($dato['cantidad']); ?></td>
  <td align="right">$ <?php echo(number_format(($dato['cantidad']*$dato['valor_unitario']), 2, '.', ',')); ?></td>
</tr>
<?php } }?>
<tr>
  <td colspan="2" class="separador_up">Subtotal</td>
  <td align="right" nowrap="nowrap" class="separador_up">$ <?php echo(number_format($v_subtotal, 2, ".", ",")); ?></td>
</tr>
<tr>
  <td colspan="2">Descuento</td>
  <td align="right" nowrap="nowrap">$ <?php echo(number_format($v_descuento, 2, ".", ",")); ?></td>
</tr>
<?php if(is_array($t_impuestos)) {
foreach($t_impuestos as $dato){ ?>
<tr>
  <td colspan="2">Iva (<?php echo($dato['impuesto']); ?>%)</td>
  <td align="right" nowrap="nowrap">$ <?php echo(number_format($dato['valor'], 2, '.', ',')); ?></td>
</tr>
<?php 	$v_total += $dato['valor']; 
      } }?>
<tr>
  <td colspan="2" class="separador">Total</td>
  <td align="right" nowrap="nowrap" class="separador">$ <?php echo(number_format(($v_subtotal + $v_total - $v_descuento), 2, '.', ',')); ?></td>
</tr>
<tr>
  <td>Cajero:</td>
  <td colspan="2"><?php echo(strtoupper($r_factura['cajero'])); ?></td>
</tr>
<tr>
  <td colspan="3" align="center" nowrap="nowrap">GRACIAS POR UTILIZAR NUESTROS SERVICIOS</td>
</tr>
</table>
</body>
</html>