<?php
function nombre_cliente($connid, $p_id_usuario) {
	$query = "Select concat(usua.nombres,' ',usua.apellidos) nomcliente
	            From segu_usuarios usua
			   Where id_usuario = ".$p_id_usuario;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return($rset[0]['nomcliente']);
}
function lista_clientes($connid,$v_id_sede) {
	$query = "Select Distinct usua.id_usuario, concat(usua.apellidos,' ',usua.nombres) nomcliente
	            From segu_usuarios usua,
				     fact_facturacion fact
			   Where usua.id_usuario = fact.id_usuario
			   And   fact.id_sede = ".$v_id_sede."
			   Order By usua.apellidos, usua.nombres";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return($rset);
}  
function lista_productos($connid){
	$query = "Select id_producto, nombre, referencia, valor, iva
	            From fact_productos
			   Order By nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return($rset);
}
function detalle_producto($connid, $p_id_producto){
	$query = "Select referencia, nombre, valor, iva
	            From fact_productos
			   Where id_producto = ".$p_id_producto;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return($rset);
}
function lista_servicios ($connid,$v_id_sede){
	$query = "Select serv.id_servicio, serv.nombre, serv.descripcion
	            From conf_servicios serv,
	            	 conf_servicios_x_sede csps
	            Where serv.id_servicio = csps.id_servicio
	            And   csps.id_sede = ".$v_id_sede."
			   Order By nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function detalle_servicio ($connid, $id_servicio) {
	$query = "Select * from conf_servicios serv
	           Where serv.id_servicio = ".$id_servicio;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}
function obtener_numfactura($connid,$v_id_sede){
	$query = "Select num_factura
	            From conf_sedes
			   Where id_sede =".$v_id_sede;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]['valor']);
}
function listar_facturas($connid, $tipo, $param, $fecha_ini, $fecha_fin,$v_id_sede) {
	$v_where = null;
	if($tipo == "all") {
		$query = "Select fact.id_factura, fact.num_factura, Concat(usua.nombres,' ', usua.apellidos) nomcliente,
		                 fact.total,      fact.pagado,      date_format(fact.fecha, '%d-%m-%Y') fecha,      
						 fact.estado
				    From fact_facturacion fact,
					     segu_usuarios    usua
				   Where fact.id_usuario = usua.id_usuario
				   	 And fact.id_sede = ".$v_id_sede."
				     And fact.fecha      Between str_to_date('".$fecha_ini." 00:00', '%d-%m-%Y %H:%i') And str_to_date('".$fecha_fin." 23:59', '%d-%m-%Y %H:%i')
				   Order By fact.fecha Desc";
	} elseif ($tipo == "estado") {
		if (!is_null($param)) {
			$v_where = "And fact.estado     = '".$param."'";	
		}
		$query = "Select fact.id_factura, fact.num_factura, Concat(usua.nombres,' ', usua.apellidos) nomcliente,
		                 fact.total,      fact.pagado,      date_format(fact.fecha, '%d-%m-%Y') fecha,    
						 fact.estado
				    From fact_facturacion fact,
					     segu_usuarios    usua
				   Where fact.id_usuario = usua.id_usuario
				   And fact.id_sede = ".$v_id_sede."
				     ".$v_where."
					 And fact.fecha      Between str_to_date('".$fecha_ini." 00:00', '%d-%m-%Y %H:%i') And str_to_date('".$fecha_fin." 23:59', '%d-%m-%Y %H:%i')
				   Order By fact.fecha Desc";
	} elseif ($tipo == "factura") {
		if (!is_null($param)) {
			$v_where = "And fact.num_factura like '%".$param."%'";	
		}
		$query = "Select fact.id_factura, fact.num_factura, Concat(usua.nombres,' ', usua.apellidos) nomcliente,
		                 fact.total,      fact.pagado,      date_format(fact.fecha, '%d-%m-%Y') fecha,      
						 fact.estado
				    From fact_facturacion fact,
					     segu_usuarios    usua
				   Where fact.id_usuario  = usua.id_usuario
				   And fact.id_sede = ".$v_id_sede."
				     ".$v_where."
				   Order By fact.fecha Desc";
	} elseif ($tipo == "cartera") {
		$query = "Select fact.id_factura, fact.num_factura, Concat(usua.nombres,' ', usua.apellidos) nomcliente,
		                 fact.total,      fact.pagado,      date_format(fact.fecha, '%d-%m-%Y') fecha,      
						 fact.estado
				    From fact_facturacion fact,
					     segu_usuarios    usua
				   Where fact.id_usuario = usua.id_usuario
				     And fact.id_sede = ".$v_id_sede."
				     And fact.estado     = 'PPA'
				   Order By fact.fecha Desc";
	} elseif ($tipo == "cliente") {
		if (!is_null($param)) {
			$v_where = "And fact.id_usuario = ".$param;	
		}
		$query = "Select fact.id_factura, fact.num_factura, Concat(usua.nombres,' ', usua.apellidos) nomcliente,
		                 fact.total,      fact.pagado,      date_format(fact.fecha, '%d-%m-%Y') fecha,      
						 fact.estado
				    From fact_facturacion fact,
					     segu_usuarios    usua
				   Where fact.id_usuario = usua.id_usuario
				   And fact.id_sede = ".$v_id_sede."
				     ".$v_where."
					 And fact.fecha      Between str_to_date('".$fecha_ini." 00:00', '%d-%m-%Y %H:%i') And str_to_date('".$fecha_fin." 23:59', '%d-%m-%Y %H:%i')
				   Order By fact.fecha desc";
	} elseif ($tipo == "producto") {
		if (!is_null($param)) {
			$v_where = "And deta.id_producto = ".$param;	
		}
		$query = "Select Distinct fact.id_factura, fact.num_factura, Concat(usua.nombres,' ', usua.apellidos) nomcliente,
		                 fact.total,      fact.pagado,      date_format(fact.fecha, '%d-%m-%Y') fecha,      
						 fact.estado
				    From fact_facturacion fact,
					     segu_usuarios    usua,
						 fact_detalle     deta
				   Where fact.id_usuario  = usua.id_usuario
				     And fact.id_factura  = deta.id_factura
				     And fact.id_sede = ".$v_id_sede."
					 And fact.estado      Not In ('PRC', 'ANL')
				     ".$v_where."
					 And fact.fecha      Between str_to_date('".$fecha_ini." 00:00', '%d-%m-%Y %H:%i') And str_to_date('".$fecha_fin." 23:59', '%d-%m-%Y %H:%i')
				   Order By fact.fecha desc";
	} elseif ($tipo == "servicio") {
		if (!is_null($param)) {
			$v_where = "And deta.id_servicio = ".$param;	
		}
		$query = "Select Distinct fact.id_factura, fact.num_factura, Concat(usua.nombres,' ', usua.apellidos) nomcliente,
		                 fact.total,      fact.pagado,      date_format(fact.fecha, '%d-%m-%Y') fecha,      
						 fact.estado
				    From fact_facturacion fact,
					     segu_usuarios    usua,
						 fact_detalle     deta
				   Where fact.id_usuario  = usua.id_usuario
				     And fact.id_factura  = deta.id_factura
				     And fact.id_sede = ".$v_id_sede."
					 And fact.estado      Not In ('PRC', 'ANL')
				     ".$v_where."
					 And fact.fecha      Between str_to_date('".$fecha_ini." 00:00', '%d-%m-%Y %H:%i') And str_to_date('".$fecha_fin." 23:59', '%d-%m-%Y %H:%i')
				   Order By fact.fecha desc";
	} 
	//echo($query);
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function datos_factura($connid, $id_factura) {
	$query = "Select fact.id_factura, fact.num_factura, Concat(usua.nombres,' ',usua.apellidos) nomcliente,
				     fact.id_usuario, fact.descuento,   date_format(fact.fecha, '%d-%m-%Y') fecha,
					 fact.total,      fact.pagado,      date_format(fact.fecha_ult_pago, '%d-%m-%Y') ult_pago,
					 fact.estado,     date_format(fact.fecha, '%h:%i %p') hora, Concat(usua1.nombres,' ',usua1.apellidos) cajero,
					 fact.tipo_pago
				From fact_facturacion fact,
				     segu_usuarios    usua,
					 segu_usuarios    usua1
			   Where fact.id_usuario = usua.id_usuario
			     And fact.cajero     = usua1.id_usuario
			     And fact.id_factura = ".$id_factura;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}
function impuestos_factura($connid, $id_factura) {
	$query = "Select t1.impuesto, IfNull(sum(t1.valor), 0) valor
	            From ( Select prod.iva impuesto, IfNull(sum(deta.iva), 0) valor
	            		 From fact_productos prod,
				     		  fact_detalle deta
					    Where deta.id_producto = prod.id_producto
						  And deta.id_factura  = ".$id_factura."
					    Group By prod.iva
					    Union All
					   Select serv.impuesto impuesto, IfNull(sum(deta.iva), 0) valor
						 From conf_servicios serv,
						 	  fact_detalle deta
					    Where deta.id_servicio = serv.id_servicio
						  And deta.id_factura  = ".$id_factura."
					    Group By serv.impuesto )t1
					    Order By impuesto";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (is_array($rset) && !is_null($rset[0]['impuesto'])) {
		return ($rset);
	} else {
		$rset[0]['impuesto'] = 16;
		$rset[0][ 'valor'] = 0;
		return ($rset);
	}
}
function detalle_factura($connid, $id_factura) {
	$query = "Select deta.id_detalle, deta.id_servicio, deta.id_producto,
	                 deta.cantidad,   deta.valor_unitario, deta.iva,
					 deta.descuento,  deta.valor_descuento, deta.total,
					 serv.nombre nomservicio, prod.nombre nomproducto
				From fact_detalle deta Left Outer Join 
				     conf_servicios serv On (deta.id_servicio = serv.id_servicio) Left Outer Join
					 fact_productos prod On (deta.id_producto = prod.id_producto)
			   Where deta.id_factura = ".$id_factura."
			   Order By deta.id_detalle";
	$result = dbquery ($query, $connid);
    $t_result = dbresult($result);
	return ($t_result);
}
function get_detalle($connid, $id_detalle) {
	$query = "Select deta.id_detalle, deta.id_servicio, deta.id_producto,
	                 deta.cantidad,   deta.valor_unitario, deta.iva,
					 deta.descuento,  deta.valor_descuento, deta.total,
					 serv.nombre nomservicio, prod.nombre nomproducto
				From fact_detalle deta Left Outer Join 
				     conf_servicios serv On (deta.id_servicio = serv.id_servicio) Left Outer Join
					 fact_productos prod On (deta.id_producto = prod.id_producto)
			   Where deta.id_detalle = ".$id_detalle;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}
function get_factura_proc($connid,$v_id_sede){
	$query = "Select id_factura
	            From fact_facturacion
			   Where estado = 'PRC'
			   And id_sede = ".$v_id_sede;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (is_array($rset) && !is_null($rset[0]['id_factura'])) {
		return ($rset[0]['id_factura']);
	} else {
		return (null);
	}
}
function get_fecha_factura($connid, $tipo){
	$v_hoy = new DateTime();
	if($tipo=="inicial") {
		$query = "Select date_format(ifNull(Min(fecha), Curdate()), '%d-%m-%Y') fecha
		            From fact_facturacion
				   Where date_format(fecha, '%Y') = '".$v_hoy->format('Y')."'";
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		return($rset[0]['fecha']);
	}else {
		return($v_hoy->format('d-m-Y'));
	}
}
function getPrecioBase($connid, $tipo, $id){
	if($tipo == "servicio") {
		$query = "Select precio_base precio
		            From conf_servicios
				   Where id_servicio = ".$id;
	} else {
		$query = "Select valor precio
		            From fact_productos
				   Where id_producto = ".$id;
	}
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if(is_array($rset) && !is_null($rset[0]['precio'])){
		return($rset[0]['precio']);
	} else {
		return(0);
	}
}
?>