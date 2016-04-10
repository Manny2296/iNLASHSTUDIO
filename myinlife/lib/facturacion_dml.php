<?php
function crea_producto ($connid, $referencia, $nombre, $valor, $iva) {
	$query = "Select count(9) conteo
	            From fact_productos
			   Where referencia = '".$referencia."'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return(false);
	}
	$query = "Insert Into fact_productos (referencia, nombre, valor, iva)
	             Values ('".$referencia."', '".$nombre."', ".$valor.", ".$iva.")";
	$result = dbquery ($query, $connid);
	return(true);
}
function upd_producto ($connid, $id_producto, $referencia, $nombre, $valor, $iva) {
	$query = "Select count(9) conteo
	            From fact_productos
			   Where referencia = '".$referencia."'
			     And id_producto != ".$id_producto;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return(false);
	}
	$query = "Update fact_productos
	             Set referencia = '".$referencia."', nombre = '".$nombre."',
				     valor = ".$valor.", iva = ".$iva."
			   Where id_producto = ".$id_producto;
	$result = dbquery ($query, $connid);
    return(true);
}
function del_producto ($connid, $id_producto) {
	$query = "Select count(9) conteo
	            From fact_detalle
			   Where id_producto = ".$id_producto;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return(false);
	}
	$query = "Delete From fact_productos Where id_producto = ".$id_producto;
	$result = dbquery ($query, $connid);
    return(true);
}
function crea_factura($connid, $id_usuario, $fecha, $cajero,$v_id_sede) {
	$query = "Select count(9) conteo
	            From fact_facturacion fact
			   Where fact.estado = 'PRC'
			   And fact.id_sede =".$v_id_sede;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return(false);
	}
	$query = "Insert Into fact_facturacion 
	             (num_factura, id_usuario, fecha,
				  descuento, total, pagado,
				  fecha_ult_pago, estado, cajero, id_sede)
				 Values
				 (null, ".$id_usuario.", str_to_date('".$fecha."', '%d-%m-%Y %H:%i'),
				  0, 0, 0,
				  null, 'PRC', ".$cajero.",".$v_id_sede.")";
	$result = dbquery ($query, $connid);
	return(true);
}
function upd_estado_factura ($connid, $id_factura, $estado, $medio_pago, $fecha,$v_id_sede){
	$query = "Select count(9) conteo
	            From fact_facturacion fact
			   Where fact.id_factura = ".$id_factura."
			     And fact.id_sede=".$v_id_sede."
			     And fact.estado     = 'PRC'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if($rset[0]['conteo'] == 0) {
		return(false);
	}
	$query = "Update fact_facturacion
	             Set estado = '".$estado."'
			   Where id_factura = ".$id_factura."
			     And id_sede=".$v_id_sede; 
	$result = dbquery ($query, $connid);
	if ($estado == 'FAC') {
		$query = "Select num_factura, pref_factura
	            From conf_sedes
			   Where id_sede =".$v_id_sede;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		$v_num_factura = $rset[0]['num_factura'];
		$query = "Update fact_facturacion
		             Set num_factura = '".$rset[0]['pref_factura'].$v_num_factura."', tipo_pago='".$medio_pago."',
					     fecha = str_to_date('".$fecha."', '%d-%m-%Y %H:%i')
				   Where id_factura = ".$id_factura."
				   And id_sede=".$v_id_sede; 
		$result = dbquery ($query, $connid);
		$v_num_factura++;
		$query = "Update conf_sedes
		             Set num_factura = '".$v_num_factura."'
				   Where id_sede =".$v_id_sede;
		$result = dbquery ($query, $connid);
	}
	return(true);
}
function del_factura($connid, $id_factura) {
	$query = "Select estado
	            From fact_facturacion
			   Where id_factura = ".$id_factura;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_estado = $rset[0]['estado'];
	if ($v_estado == 'PRC') {
		$query = "Delete From fact_detalle
		           Where id_factura = ".$id_factura;    
		$result = dbquery ($query, $connid);
		$query = "Delete From fact_facturacion
		           Where id_factura = ".$id_factura;    
		$result = dbquery ($query, $connid);
	} elseif($v_estado != 'ANL') {
		$query = "Update fact_facturacion
		             Set estado = 'ANL'
			       Where id_factura = ".$id_factura;
		$result = dbquery ($query, $connid);
	}
	return(true);
}
function crea_detalle($connid, $id_factura, $id_servicio, $id_producto, $cantidad, $pordto, $valor_unitario,$v_id_sede) {
	$v_valor_unitario = $valor_unitario;
	if(!is_null($id_producto)) {
		$v_id_producto = $id_producto;
		$v_id_servicio = 'null';
		$query = "Select count(9) conteo
		            From fact_detalle
				   Where id_factura = ".$id_factura."
					 And id_producto = ".$v_id_producto;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		if($rset[0]['conteo'] == 0) {
			$v_accion = "insert";
		} else {
			$v_accion = "update";
		}
		$query = "Select iva 
		            From fact_productos prod
				   Where prod.id_producto = ".$v_id_producto;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		$v_iva = $rset[0]['iva'];
		$v_valor_descuento = ($cantidad*$v_valor_unitario) * ($pordto / 100);
		$v_impuestos = ($cantidad*$v_valor_unitario) * ($v_iva/100);
		$v_total =  ($cantidad*$v_valor_unitario) + $v_impuestos - $v_valor_descuento;
	} elseif(!is_null($id_servicio)) {
		$v_id_producto = 'null';
		$v_id_servicio = $id_servicio;
		$query = "Select count(9) conteo
		            From fact_detalle
			       Where id_factura = ".$id_factura."
					 And id_servicio = ".$v_id_servicio;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		if($rset[0]['conteo'] == 0) {
			$v_accion = "insert";
		} else {
			$v_accion = "update";
		}
		$query = "Select impuesto 
		            From conf_servicios serv
				   Where serv.id_servicio = ".$v_id_servicio;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		$v_iva = $rset[0]['impuesto'];
		$v_valor_descuento = ($cantidad*$v_valor_unitario) * ($pordto / 100);
		$v_impuestos = ($cantidad*$v_valor_unitario) * ($v_iva/100);
		$v_total =  ($cantidad*$v_valor_unitario) + $v_impuestos - $v_valor_descuento;
	}
	if ($v_accion == "insert") {
		$query = "Insert Into fact_detalle 
		             (id_factura, id_producto, id_servicio,
					  cantidad,   valor_unitario, iva,
					  descuento,  valor_descuento, total)
					 Values
					 (".$id_factura.", ".$v_id_producto.", ".$v_id_servicio.",
					  ".$cantidad.", ".$v_valor_unitario.", ".$v_impuestos.",
					  ".$pordto.", ".$v_valor_descuento.", ".$v_total.")";
	} else {
		if(!is_null($id_producto)) {
			$query = "Update fact_detalle
			             Set cantidad = ".$cantidad.", valor_unitario = ".$v_valor_unitario.",
						     iva = ".$v_iva.", descuento = ".$pordto.", 
							 valor_descuento = ".$v_valor_descuento.", total = ".$v_total."
					   Where id_factura = ".$id_factura."
					     And id_producto = ".$v_id_producto;
		} else {
			$query = "Update fact_detalle
			             Set cantidad = ".$cantidad.", valor_unitario = ".$v_valor_unitario.",
						     iva = ".$v_impuestos.", descuento = ".$pordto.", 
							 valor_descuento = ".$v_valor_descuento.", total = ".$v_total."
					   Where id_factura = ".$id_factura."
					     And id_servicio = ".$v_id_servicio;
		}
	}
	$result = dbquery ($query, $connid);
    return(true);
}
function del_detalle($connid, $id_detalle) {
	$query = "Delete from fact_detalle Where id_detalle = ".$id_detalle;
	$result = dbquery ($query, $connid);
    return(true);
}
function calcular_totales($connid, $id_factura) {
	$query = "Select IfNull(Sum(cantidad*valor_unitario), 0) subtotal, IfNull(Sum(valor_descuento), 0) descuento, 
	                 IfNull(Sum(total), 0) total
				From fact_detalle
			   Where id_factura = ".$id_factura;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$t_result['subtotal'] = $rset[0]['subtotal'];
	$t_result['descuento'] = $rset[0]['descuento'];
	$t_result['impuestos'] = $rset[0]['total']+$rset[0]['descuento']-$rset[0]['subtotal'];
	$t_result['total'] = $rset[0]['total'];
	$query = "Update fact_facturacion
	             Set descuento = ".$t_result['descuento'].", total = ".$t_result['total']."
			   Where id_factura = ".$id_factura;
	$result = dbquery ($query, $connid);
	return(true);
}
?>