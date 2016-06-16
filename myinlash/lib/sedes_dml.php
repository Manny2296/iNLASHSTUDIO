<?php 
	function crea_sede($connid, $v_nombre ,$v_pais ,$v_ciudad ,$v_direccion,$v_telefono ,$v_domicilio ,$v_num_factura ,$v_pref_factura ,$v_activa){
		$query = "Select count(9) conteo
					From conf_sedes sede
				   Where sede.nombre like '%".$v_nombre."%'";
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
    	if($rset[0]['conteo']> 0){
    		return (false);
    	}
    	$query = "Insert INTO conf_sedes
					(
						nombre ,pais   ,ciudad ,
						direccion ,telefono  ,domicilio  ,
						Num_factura ,Pref_factura ,Activa 
 					)Values
 					(
 						'".$v_nombre."','".$v_pais."','".$v_ciudad."',
						'".$v_direccion."',".$v_telefono." ,'".$v_domicilio."' ,
						".$v_num_factura.",'".$v_pref_factura."','".$v_activa."'
 					)";
 		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
    	return (true);
	}

	function upd_sede($connid, $v_id_sede, $v_nombre ,$v_pais ,$v_ciudad ,$v_direccion,$v_telefono ,$v_domicilio ,$v_num_factura ,$v_pref_factura ,$v_activa ){
		$query ="update conf_sedes
	             set nombre = '".$v_nombre."',pais = '".$v_pais."' ,ciudad = '".$v_ciudad."',
						direccion = '".$v_direccion."',telefono = ".$v_telefono." ,domicilio = '".$v_domicilio."' ,
						Num_factura = ".$v_num_factura." ,Pref_factura = '".$v_pref_factura."',Activa = '".$v_activa."'
						Where id_sede = ".$v_id_sede;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
	}

	function del_sede($connid, $v_id_sede){
		$query = "Select count(9) conteo
					From segu_perfil_x_usuario spxu
				   Where spxu.id_sede = ".$v_id_sede;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
    	if($rset[0]['conteo']> 0){
    		$query = "update conf_sedes
	             set Activa = 'N'
	             Where id_sede =".$v_id_sede;
	    	$result = dbquery ($query, $connid);
    		$rset = dbresult($result);
    		return (false);
    	}		   
		$query = "Select count(9) conteo
					From fact_facturacion fact
				   Where fact.id_sede = ".$v_id_sede;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
    	if($rset[0]['conteo']> 0){
    		$query = "update conf_sedes
	             set Activa = 'N'
	             Where id_sede =".$v_id_sede;
	    	$result = dbquery ($query, $connid);
    		$rset = dbresult($result);
    		return (false);
    	}
    	$query = "Delete From conf_sedes
				   Where id_sede = ".$v_id_sede;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
    	return(true);
	}
	function act_sede($connid, $v_id_sede){
		$query = "update conf_sedes
	             set Activa = 'S'
	             Where id_sede =".$v_id_sede;
	    $result = dbquery ($query, $connid);
    	$rset = dbresult($result);
	}
	function agregar_servicio($connid, $v_id_sede,$v_id_servicio,$v_sesiones_simultaneas){
		$query = "Select count(9) conteo
					From conf_servicios_x_sede csps
				   Where csps.id_sede = ".$v_id_sede."
				   And   csps.id_servicio = ".$v_id_servicio;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
    	if($rset[0]['conteo']> 0){
    		return (false);
    	}
    	$query = "Insert into conf_servicios_x_sede 
				  (
				  	id_sede, id_servicio, sesiones_simultaneas
				  )values
				  (
				  	".$v_id_sede.",".$v_id_servicio.",".$v_sesiones_simultaneas."
				  )
				  ";
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
    	return (true);
	}
	function upd_servicio_sede($connid, $v_id_sede,$v_id_servicio,$v_sesiones_simultaneas){
		$query = "update conf_servicios_x_sede csps
	             set sesiones_simultaneas = ".$v_sesiones_simultaneas."
	             Where csps.id_sede = ".$v_id_sede."
				 And   csps.id_servicio = ".$v_id_servicio;
	    $result = dbquery ($query, $connid);
    	$rset = dbresult($result);
    	return (true);
	}

?>