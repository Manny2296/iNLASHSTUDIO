<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/facturacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$v_tipo = $_POST['p_tipo'];
if (!is_null($_POST['p_param']) && $_POST['p_param'] != "") {
	$v_param = $_POST['p_param'];
} else {
	$v_param = null;
}
$v_fecha_ini = $_POST['p_fecha_ini'];
$v_fecha_fin = $_POST['p_fecha_fin'];
$v_total = 0;

$t_consulta = listar_facturas($conn, $v_tipo, $v_param, $v_fecha_ini, $v_fecha_fin);
dbdisconn($conn);
?>
<table width="75%" border="0" cellpadding="0" cellspacing="0">
  <tr class="t_texto">
    <td colspan="4"><div id="barra_botones"><a href="javascript:facturar();" class="button"><span>Crear Factura</span></a></div></td>
  </tr>
  <tr class="t_header">
    <th>Factura No.</th>
    <th>Cliente</th>
    <th>Valor</th>
    <th>Estado</th>
  </tr>
  <?php 
  if(is_array($t_consulta)) {
	  foreach($t_consulta as $dato){
		  switch($dato['estado']){
			  case "FAC":
			  	$v_estado = "Facturada";
				break;
			  case "PRC":
			  	$v_estado = "En Proceso";
				break;
			  case "ANL":
			  	$v_estado = "Anulada";
				break;
			  case "PPA":
			  	$v_estado = "Cancelada Parcialmente";
				break;
			  case "OK":
			  	$v_estado = "Cancelada";
				break;
		  }
		  $v_total += $dato['total'];
  ?>
  <tr class="t_texto">
    <td align="center"><a href="javascript:detalle(<?php echo($dato['id_factura']); ?>);"><?php echo($dato['num_factura']); ?></a></td>
    <td><?php echo(utf8_encode($dato['nomcliente'])); ?></td>
    <td align="right"><?php echo("$ ".number_format($dato['total'], 2, '.', ',')); ?></td>
    <td align="center"><?php echo($v_estado); ?></td>
  </tr>
  <?php
	  } ?>
  <tr class="t_header">
    <th colspan="2">Total Facturado:</th>
    <td align="right"><div align="right"><?php echo("$ ".number_format($v_total, 2, '.', ',')); ?></div> </td>
    <td>&nbsp;</td>
  </tr>
  <?php
  } else { ?>
  <tr class="t_texto">
    <td height="40" align="center" colspan="4">No se encontraron facturas con los criterios seleccionados</td>
  </tr>
  <?php  } ?>
</table>
