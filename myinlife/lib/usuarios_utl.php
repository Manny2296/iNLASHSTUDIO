<?php
/*
libreria de utilitarios para clientes y usuarios
*/
function lista_perfil ($connid) {
   $query = "Select id_perfil, nombre
               From conf_tipo_perfil tppf
			  Where tppf.id_perfil != 3
			  Order By nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function lista_usuarios ($connid, $tipo, $parametro,$id_sede,$filtro) {
	if ($tipo == "nombre") {
		$query = "Select Distinct pfus.id_perf_unico, usua.nombres, usua.apellidos,
						 tpid.abreviatura, usua.numero_id, usua.id_usuario, 
						 pfus.id_perfil,   tppf.nombre nomperfil, sede.nombre nomsede
					From segu_usuarios usua,
						 conf_tipo_id  tpid,
						 segu_perfil_x_usuario pfus,
						 conf_tipo_perfil tppf,
						 conf_sedes sede
				   Where usua.id_tipoid  = tpid.id_tipoid
				     And usua.id_usuario = pfus.id_usuario
					 And tppf.id_perfil  = pfus.id_perfil
					 And pfus.estado     = 'A'
					 And pfus.id_perfil != 3 
					 And ( usua.nombres   Like '%".$parametro."%' Or
						   usua.apellidos Like '%".$parametro."%' )
					 And sede.id_sede = pfus.id_sede
					  And pfus.id_sede = ".$id_sede." "
					  .$filtro."
				   Order By usua.apellidos, usua.nombres";
				   
	}elseif ($tipo == "id") {

		$query = "Select Distinct pfus.id_perf_unico, usua.nombres, usua.apellidos,
						 tpid.abreviatura, usua.numero_id, usua.id_usuario, 
						 pfus.id_perfil,   tppf.nombre nomperfil, sede.nombre nomsede
					From segu_usuarios usua,
						 conf_tipo_id  tpid,
						 segu_perfil_x_usuario pfus,
						 conf_tipo_perfil tppf,
						 conf_sedes sede
				   Where usua.id_tipoid  = tpid.id_tipoid
				     And usua.id_usuario = pfus.id_usuario
					 And tppf.id_perfil  = pfus.id_perfil
					 And pfus.estado     = 'A'
					 And pfus.id_perfil != 3
					 And usua.numero_id Like '%".$parametro."%'
					 And sede.id_sede = pfus.id_sede
					  And pfus.id_sede = ".$id_sede." "
					  .$filtro."
				   Order By usua.apellidos, usua.nombres";
	}elseif ($tipo == "perfil") {
		if($parametro ==0){
			$query = "Select Distinct pfus.id_perf_unico, usua.nombres, usua.apellidos,
						 tpid.abreviatura, usua.numero_id, usua.id_usuario, 
						 pfus.id_perfil,   tppf.nombre nomperfil, sede.nombre nomsede
					From segu_usuarios usua,
						 conf_tipo_id  tpid,
						 segu_perfil_x_usuario pfus,
						 conf_tipo_perfil tppf,
						 conf_sedes sede
				   Where usua.id_tipoid  = tpid.id_tipoid
				     And usua.id_usuario = pfus.id_usuario
					 And tppf.id_perfil  = pfus.id_perfil
					 
					 And pfus.estado     = 'A'
					 And pfus.id_perfil != 3
					 And sede.id_sede = pfus.id_sede
					  And pfus.id_sede = ".$id_sede." "
					  .$filtro."
				   Order By usua.apellidos, usua.nombres";

		}
		else

		$query = "Select Distinct pfus.id_perf_unico, usua.nombres, usua.apellidos,
						 tpid.abreviatura, usua.numero_id, usua.id_usuario, 
						 pfus.id_perfil,   tppf.nombre nomperfil, sede.nombre nomsede,
					From segu_usuarios usua,
						 conf_tipo_id  tpid,
						 segu_perfil_x_usuario pfus,
						 conf_tipo_perfil tppf,
						 conf_sedes sede
				   Where usua.id_tipoid  = tpid.id_tipoid
				     And usua.id_usuario = pfus.id_usuario
					 And tppf.id_perfil  = pfus.id_perfil
					 And pfus.id_perfil  = ".$parametro."
					 And pfus.estado     = 'A'
					 And pfus.id_perfil != 3
					 And sede.id_sede = pfus.id_sede
					  And pfus.id_sede = ".$id_sede." "
					  .$filtro."
				   Order By usua.apellidos, usua.nombres";
	}

	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function lista_clientes ($connid, $tipo, $parametro,$id_sede) {
	if(is_null($id_sede)){
		if ($tipo == "nombre" ) {
		$query = "Select Distinct pfus.id_perf_unico, usua.nombres, usua.apellidos,
						 tpid.abreviatura, usua.numero_id, usua.id_usuario, 
						 pfus.id_perfil,   tppf.nombre nomperfil, sede.nombre nomsede,
						 sede.id_sede,usua.celular
					From segu_usuarios usua,
						 conf_tipo_id  tpid,
						 segu_perfil_x_usuario pfus,
						 conf_tipo_perfil tppf,
						 conf_sedes sede
				   Where usua.id_tipoid  = tpid.id_tipoid
				     And usua.id_usuario = pfus.id_usuario
					 And tppf.id_perfil  = pfus.id_perfil
					 And pfus.estado     = 'A'
					 And pfus.id_perfil  = 3
					 And ( usua.nombres   Like '%".$parametro."%' Or
						   usua.apellidos Like '%".$parametro."%' )
					 And sede.id_sede = pfus.id_sede
				   Order By usua.apellidos, usua.nombres";
				   
		}elseif ($tipo == "id") {
			$query = "Select Distinct pfus.id_perf_unico, usua.nombres, usua.apellidos,
							 tpid.abreviatura, usua.numero_id, usua.id_usuario, 
							 pfus.id_perfil,   tppf.nombre nomperfil, sede.nombre nomsede,
							 sede.id_sede,usua.celular
						From segu_usuarios usua,
							 conf_tipo_id  tpid,
							 segu_perfil_x_usuario pfus,
							 conf_tipo_perfil tppf,
							 conf_sedes sede
					   Where usua.id_tipoid  = tpid.id_tipoid
					     And usua.id_usuario = pfus.id_usuario
						 And tppf.id_perfil  = pfus.id_perfil
						 And pfus.estado     = 'A'
						 And pfus.id_perfil  = 3
						 And usua.numero_id Like '%".$parametro."%'
						 And sede.id_sede = pfus.id_sede
					   Order By usua.apellidos, usua.nombres";
		}elseif ($tipo == "perfil") {
			$query = "Select Distinct pfus.id_perf_unico, usua.nombres, usua.apellidos,
							 tpid.abreviatura, usua.numero_id, usua.id_usuario, 
							 pfus.id_perfil,   tppf.nombre nomperfil, sede.nombre nomsede,
							 sede.id_sede,usua.celular
						From segu_usuarios usua,
							 conf_tipo_id  tpid,
							 segu_perfil_x_usuario pfus,
							 conf_tipo_perfil tppf,
							 conf_sedes sede
					   Where usua.id_tipoid  = tpid.id_tipoid
					     And usua.id_usuario = pfus.id_usuario
						 And tppf.id_perfil  = pfus.id_perfil
						 And 
						 And pfus.estado     = 'A'
						 And pfus.id_perfil  = 3
						 And sede.id_sede = pfus.id_sede
					   Order By usua.apellidos, usua.nombres";
		}
	}else{
		if ($tipo == "nombre" ) {
		$query = "Select Distinct pfus.id_perf_unico, usua.nombres, usua.apellidos,
						 tpid.abreviatura, usua.numero_id, usua.id_usuario, 
						 pfus.id_perfil,   tppf.nombre nomperfil, sede.nombre nomsede,
						 sede.id_sede,usua.celular
					From segu_usuarios usua,
						 conf_tipo_id  tpid,
						 segu_perfil_x_usuario pfus,
						 conf_tipo_perfil tppf,
						 conf_sedes sede
				   Where usua.id_tipoid  = tpid.id_tipoid
				     And usua.id_usuario = pfus.id_usuario
					 And tppf.id_perfil  = pfus.id_perfil
					 And pfus.estado     = 'A'
					 And pfus.id_perfil  = 3
					 And ( usua.nombres   Like '%".$parametro."%' Or
						   usua.apellidos Like '%".$parametro."%' )
					 And sede.id_sede = pfus.id_sede
					 And pfus.id_sede = ".$id_sede."
				   Order By usua.apellidos, usua.nombres";
				   
		}elseif ($tipo == "id") {
			$query = "Select Distinct pfus.id_perf_unico, usua.nombres, usua.apellidos,
							 tpid.abreviatura, usua.numero_id, usua.id_usuario, 
							 pfus.id_perfil,   tppf.nombre nomperfil, sede.nombre nomsede,
							 sede.id_sede,usua.celular
						From segu_usuarios usua,
							 conf_tipo_id  tpid,
							 segu_perfil_x_usuario pfus,
							 conf_tipo_perfil tppf,
							 conf_sedes sede
					   Where usua.id_tipoid  = tpid.id_tipoid
					     And usua.id_usuario = pfus.id_usuario
						 And tppf.id_perfil  = pfus.id_perfil
						 And pfus.estado     = 'A'
						 And pfus.id_perfil  = 3
						 And usua.numero_id Like '%".$parametro."%'
						 And sede.id_sede = pfus.id_sede
						 And pfus.id_sede = ".$id_sede."
					   Order By usua.apellidos, usua.nombres";
		}elseif ($tipo == "perfil") {
			$query = "Select Distinct pfus.id_perf_unico, usua.nombres, usua.apellidos,
							 tpid.abreviatura, usua.numero_id, usua.id_usuario, 
							 pfus.id_perfil,   tppf.nombre nomperfil, sede.nombre nomsede,
							 sede.id_sede,usua.celular
						From segu_usuarios usua,
							 conf_tipo_id  tpid,
							 segu_perfil_x_usuario pfus,
							 conf_tipo_perfil tppf,
							 conf_sedes sede
					   Where usua.id_tipoid  = tpid.id_tipoid
					     And usua.id_usuario = pfus.id_usuario
						 And tppf.id_perfil  = pfus.id_perfil
						 And pfus.estado     = 'A'
						 And pfus.id_perfil  = 3
						 And sede.id_sede = pfus.id_sede
						 And pfus.id_sede = ".$id_sede."
					   Order By usua.apellidos, usua.nombres";
		}
	}
	
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function detalle_usuario ($connid, $id_perf_unico) {
	$query = "Select usua.*, pfus.id_perfil,
	                 tpid.abreviatura, tppf.nombre nomperfil, sede.nombre nomsede
	                 , pfus.id_sede
	            from segu_usuarios          usua,
				     conf_tipo_id           tpid,
					 segu_perfil_x_usuario  pfus,
					 conf_tipo_perfil       tppf,
					 conf_sedes sede
	           Where usua.id_usuario    = pfus.id_usuario
			     And usua.id_tipoid     = tpid.id_tipoid
				 And tppf.id_perfil     = pfus.id_perfil
				 And sede.id_sede = pfus.id_sede
			     And pfus.id_perf_unico = ".$id_perf_unico;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$resultado = $rset;
	$v_id_prepagada = $resultado[0]['id_prepagada']; 
	$v_id_eps = $resultado[0]['id_eps'];
	if (!is_null($v_id_prepagada) && $v_id_prepagada != "") {
		$query = "Select nombre
		            From conf_prepagadas prep
 				   Where prep.id_prepagada = ".$v_id_prepagada;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		$resultado[0]['nomprepagada'] = $rset[0]['nombre'];
	} else {
		$resultado[0]['nomprepagada'] = '&nbsp;';
	}
	if (!is_null($v_id_eps) && $v_id_eps != "") {
		$query = "Select nombre
		            From conf_eps eps
 				   Where eps.id_eps = ".$v_id_eps;
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		$resultado[0]['nomeps'] = $rset[0]['nombre'];
	} else {
		$resultado[0]['nomeps'] = '&nbsp;';
	}
	return ($resultado[0]);
}
function nombres_usua ($connid, $id_usuario) {
	$query = "Select usua.nombres, usua.apellidos
	            from segu_usuarios          usua
	           Where usua.id_usuario  = ".$id_usuario;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}
function lista_tipo_id ($connid) {
	$query = "Select tpid.id_tipoid, tpid.nombre, tpid.abreviatura
	            From conf_tipo_id tpid
			   Order By tpid.nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}
function lista_genero ($genero) {
	if (isset($genero) && !is_null($genero) && $genero != "") {
		$v_genero = $genero;
	} else {
		$v_genero = null;
	}
?>
<select name="p_genero" id="p_genero" size="1">
   <option value="" <?php if (is_null($v_genero)) { echo ("Selected"); } ?>></option>
   <option value="F" <?php if ($v_genero == "F") { echo ("Selected"); } ?>>Femenino</option>
   <option value="M" <?php if ($v_genero == "M") { echo ("Selected"); } ?>>Masculino</option>
</select>
<?php
}
function detalle_prepagada ($connid, $id_prepagada) {
	if(is_null($id_prepagada)) {
		return null;
	}
	$query = "Select id_prepagada, nombre
	            From conf_prepagadas
		       Where id_prepagada = ".$id_prepagada;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}
function detalle_eps ($connid, $id_eps) {
	if(is_null($id_eps)) {
		return null;
	}
	$query = "Select id_eps, nombre
	            From conf_eps
		       Where id_eps = ".$id_eps;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]);
}

function buscar_eps ($connid, $valor) {
	$query = "Select id_eps, nombre
	            From conf_eps
				Where nombre like '%".$valor."%'
		       Order By nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}

function buscar_prepagada ($connid, $valor) {
	$query = "Select id_prepagada, nombre
	            From conf_prepagadas
			   Where nombre like '%".$valor."%'
		       Order By nombre";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset);
}

function verificar_usuario ($connid, $id_tipoid, $numero_id, $id_perfil) {
	$query = "Select id_usuario
	            From segu_usuarios usua
			   Where usua.id_tipoid = ".$id_tipoid."
			     And usua.numero_id = '".$numero_id."'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if (!is_array($rset)) {
		return (null);
	} else {
		$resultado[0] = 'U';
		$resultado[1] = $rset[0]['id_usuario'];
		
		$query = "Select count(9) conteo
		            From segu_perfil_x_usuario pfus
		           Where pfus.id_usuario = ".$resultado[1]."
				     And pfus.id_perfil  = ".$id_perfil."
					 And pfus.estado     = 'A'";
		$result = dbquery ($query, $connid);
    	$rset = dbresult($result);
		if ($rset[0]['conteo'] > 0) {
			$resultado[0] = 'P';
		}
		return ($resultado);
	}
}
function mostrar_pestanas($connid, $id_usuario) {
	$query = "Select count(9) conteo
	            From spa_programacion prog,
				     conf_servicios   serv
			   Where serv.id_servicio     = prog.id_servicio
			     And serv.modulo_pestanas = 'S'
				 And prog.id_usuario      = ".$id_usuario;
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		return (true);
	} else {
		return (false);
	}
}
function next_ilid($connid) {
	$query = "Select ifNull(max(numero_id), 'ILID001') numid
	            From segu_usuarios
			   Where id_tipoid = 0";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_next = (int)$rset[0]['numid'] + 1;
	$v_len = strlen($v_next);
	while($v_len < 4){
		$v_next = "0".$v_next;
		$v_len++;
	}
	return($v_next);
}
?>