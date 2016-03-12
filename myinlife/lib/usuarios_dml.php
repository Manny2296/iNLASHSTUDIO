<?php
/*
Libreria de funciones DML para la administración de usuarios
*/
function crea_eps ($connid, $nombre) {
	$nombre = ltrim(rtrim($nombre));
	$query = "Select count(9) cant
	            From conf_eps
			   Where nombre = '".$nombre."'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['cant'] == 0) {
		$query = "Insert conf_eps(nombre)
		          Values ('".$nombre."')";
		$result = dbquery ($query, $connid);
	}
	$query = "Select id_eps
				From conf_eps
			   Where nombre = '".$nombre."'";
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	return($rset[0]['id_eps']);	
}

function crea_prepagada ($connid, $nombre) {
	$nombre = ltrim(rtrim($nombre));
	$query = "Select count(9) cant
	            From conf_prepagadas
			   Where nombre = '".$nombre."'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['cant'] == 0) {
		$query = "Insert conf_prepagadas(nombre)
		          Values ('".$nombre."')";
		$result = dbquery ($query, $connid);
	}
	$query = "Select id_prepagada
				From conf_prepagadas
			   Where nombre = '".$nombre."'";
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	return($rset[0]['id_prepagada']);	
}

function crea_usuario ($connid,           $id_tipoid,    $numero_id,        $nombres,
					   $apellidos,        $telefono,     $celular,          $email,         
					   $genero,		      $id_eps,		 $eps,              $id_prepagada, 
					   $prepagada,	      $descripcion,	 $fecha_nacimiento, $fecha_ingreso){
	$query = "Select count(9) conteo
	            From segu_usuarios usua
			   Where usua.id_tipoid = ".$id_tipoid."
			     And usua.numero_id = '".$numero_id."'";
				 
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	if ($rset[0]['conteo'] == 0) {
		$needle = array("ñ", "Ñ", "á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú");
		$haystack = array("n", "n", "a", "e", "i", "o", "u", "a", "e", "i", "o", "u");
		$nombres = ltrim(rtrim($nombres));
		$apellidos = ltrim(rtrim($apellidos));
		
		if (strpos($apellidos," ") === false) {
			$v_login = str_replace($needle, $haystack, strtolower(ltrim(rtrim(substr(substr($nombres,0,1).$apellidos, 0, 20)))));
		} else{
			$v_login = str_replace($needle, $haystack, strtolower(ltrim(rtrim(substr(substr($nombres,0,1).substr($apellidos,0,strpos($apellidos," ")), 0, 20)))));
		}
		$query = "Select count(9) conteo
					From segu_usuarios usua
				   Where usua.login like '".$v_login."%'";
		$result = dbquery ($query, $connid);
		$rset = dbresult($result);
		if ($rset[0]['conteo'] > 0) {
			$v_login = substr($v_login, 0, (20 - strlen($rset[0]['conteo']))).$rset[0]['conteo'];
		}
		$v_pwd = md5(ltrim(rtrim(str_replace(".", "", str_replace(",", "", $numero_id)))));
		if (is_null($fecha_nacimiento) || $fecha_nacimiento == "") {
			$str_fecha_nacimiento = "Null";
		} else {
			$str_fecha_nacimiento = "str_to_date('".$fecha_nacimiento."', '%d-%m-%Y')";
		}
		if (is_null($fecha_ingreso) || $fecha_ingreso == "") {
			$str_fecha_ingreso = "Null";
		} else {
			$str_fecha_ingreso = "str_to_date('".$fecha_ingreso."', '%d-%m-%Y')";
		}
		if (is_null($id_eps) || $id_eps == "") {
			if(strlen($eps) > 1) {
				$str_eps = crea_eps($connid, $eps);
			} else {
				$str_eps = "Null";
			}
		} else {
			$str_eps = $id_eps;
		}
		if (is_null($id_prepagada) || $id_prepagada == "") {
			if(strlen($prepagada) > 1) {
				$str_prepagada = crea_prepagada($connid, $prepagada);
			} else {
				$str_prepagada = "Null";
			}
		} else {
			$str_prepagada = $id_prepagada;
		}
		if (is_null($email) || $email == "") {
			$str_email = "Null";
		} else {
			$str_email = "'".$email."'";
		}
		$query = "Insert into segu_usuarios 
					(id_tipoid,        numero_id,     nombres,      apellidos,
					 telefono,		   celular,		  genero,		email,       
					 fecha_nacimiento, id_eps,		  id_prepagada, descripcion,
					 fecha_ingreso,    login,         pwd)
					Values
					(".$id_tipoid.",            '".$numero_id."', '".$nombres."',     '".$apellidos."',
					 '".$telefono."',	        '".$celular."',	  '".$genero."',      ".$str_email.",
					 ".$str_fecha_nacimiento.", ".$str_eps.",     ".$str_prepagada.", '".$descripcion."', 
					 ".$str_fecha_ingreso.",    '".$v_login."',   '".$v_pwd."')";
	   $query = str_replace ("''", "Null", $query);
	   $result = dbquery ($query, $connid);
	}
    $query = "Select id_usuario
                From segu_usuarios usua
	  		   Where usua.id_tipoid = ".$id_tipoid."
			     And usua.numero_id = '".$numero_id."'";
    $result = dbquery ($query, $connid);
    $rset = dbresult($result);
    return ($rset[0]['id_usuario']);
}
function upd_usuario ($connid,           $id_usuario,    $id_tipoid,        $numero_id,    $nombres,
					  $apellidos,        $telefono,      $celular,          $email,         
					  $genero,		     $id_eps,		 $eps,              $id_prepagada, 
					  $prepagada,        $descripcion,   $fecha_nacimiento, $fecha_ingreso, 
					  $notificar){
	if (is_null($fecha_nacimiento) || $fecha_nacimiento == "") {
		$str_fecha_nacimiento = "Null";
	} else {
		$str_fecha_nacimiento = "str_to_date('".$fecha_nacimiento."', '%d-%m-%Y')";
	}
	if (is_null($fecha_ingreso) || $fecha_ingreso == "") {
		$str_fecha_ingreso = "Null";
	} else {
		$str_fecha_ingreso = "str_to_date('".$fecha_ingreso."', '%d-%m-%Y')";
	}
	if (is_null($id_eps) || $id_eps == "") {
		if(strlen($eps) > 1) {
			$str_eps = crea_eps($connid, $eps);
		} else {
			$str_eps = "Null";
		}
	} else {
		if(strlen($eps) > 1) {
			$str_eps = crea_eps($connid, $eps);
		} else {
			$str_eps = "Null";
		}
	}
	if (is_null($id_prepagada) || $id_prepagada == "") {
		if(strlen($prepagada) > 1) {
			$str_prepagada = crea_prepagada($connid, $prepagada);
		} else {
			$str_prepagada = "Null";
		}
	} else {
		if(strlen($prepagada) > 1) {
			$str_prepagada = crea_prepagada($connid, $prepagada);
		} else {
			$str_prepagada = "Null";
		}
	}
	if (is_null($email) || $email == "") {
		$str_email = "Null";
	} else {
		$str_email = "'".$email."'";
	}
	if (is_null($notificar) || $notificar != "S") {
		$notificar = 'N';
	} else {
		$notificar = 'S';
	}
	
	$query = "Update segu_usuarios
	             Set id_tipoid = ".$id_tipoid.",             numero_id ='".$numero_id."',     
				     nombres = '".$nombres."',               apellidos = '".$apellidos."',
				 	 telefono = '".$telefono."',	 		 celular = '".$celular."',
				     email = ".$str_email.",       			 id_eps = ".$str_eps.",
					 id_prepagada = ".$str_prepagada.",      descripcion = '".$descripcion."',
				     genero = '".$genero."',				 fecha_nacimiento = ".$str_fecha_nacimiento.", 
					 fecha_ingreso = ".$str_fecha_ingreso.", notificar = '".$notificar."'
			   Where id_usuario = ".$id_usuario;
   $query = str_replace ("''", "Null", $query);
   $result = dbquery ($query, $connid);
   return (true);
}
function crea_perfil ($connid, $id_usuario, $id_perfil, $login_mod){
	$query = "Select count(9) conteo
	            From segu_perfil_x_usuario pfus
			   Where pfus.id_usuario = ".$id_usuario."
			     And pfus.id_perfil  = ".$id_perfil;
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	if ($rset[0]['conteo'] > 0) {
		$query = "Update segu_perfil_x_usuario pfus
		             Set pfus.fecha = Curdate(), pfus.estado = 'A',
					     pfus.login_mod = '".$login_mod."'
				   Where pfus.id_usuario = ".$id_usuario."
			         And pfus.id_perfil  = ".$id_perfil;
	    $result = dbquery ($query, $connid);
	} else {
		$query = "Insert Into segu_perfil_x_usuario 
		             (id_usuario, id_perfil,
					  estado,     fecha, 
					  login_mod)
					 Values
					 (".$id_usuario.", ".$id_perfil.",
					  'A',             Curdate(),   
					  '".$login_mod."')";
		$result = dbquery ($query, $connid);
	}
	$query = "Select id_perf_unico
	            From segu_perfil_x_usuario pfus
			   Where pfus.id_usuario = ".$id_usuario."
			     And pfus.id_perfil  = ".$id_perfil;
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	return ($rset[0]['id_perf_unico']);
}
function upd_perfil ($connid, $id_perf_unico, $estado, $login_mod){
	$query = "Update segu_perfil_x_usuario pfus
		         Set pfus.fecha = Curdate(), pfus.estado = '".$estado."',
				     pfus.login_mod = '".$login_mod."'
			   Where pfus.id_perf_unico = ".$id_perf_unico;
	$result = dbquery ($query, $connid);
	return (true);
}
function del_usuario ($connid, $id_perf_unico, $login_mod) {
	upd_perfil ($connid, $id_perf_unico, 'I', $login_mod);
	return(true);
}
function obtener_login($connid, $id_usuario) {
	$query = "Select login
	            From segu_usuarios usua
			   Where usua.id_usuario = ".$id_usuario;
	$result = dbquery ($query, $connid);
	$rset = dbresult($result);
	return ($rset[0]['login']);
}
?>