<?php 
/* 
 Libreria de utilidades de seguridad y control de acceso
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca01@gmail.com Desarrollo: Dev Manuel Felipe S.R 
      manuel.sanchez-r@mail.escuelaing.edu.co
 Fecha     : 03/11/2010 08:00
 Version   : 1.0
*/
function validar_usr($login, $pwd, $connid) {
   $setpwd = md5 ($pwd);
   $query = "Select count(*) conteo 
               from segu_usuarios
              Where login='".$login."' And pwd='".$setpwd."'";
   $result = dbquery($query, $connid);
   $cont = dbresult ($result);
   if ( $cont[0]['conteo'] == 0 ) {
      return false;
   }
   else {
      return true;
   }
}
function cantidad_perfil ($connid, $login) {
   $query = "Select count(*) cantidad
               From segu_perfil_x_usuario uspf,
	                segu_usuarios         usua
              Where usua.id_usuario = uspf.id_usuario
		        And usua.login  = '".$login."'
	            And uspf.estado = 'A'";	
   $result = dbquery($query, $connid);
   $rset = dbresult($result);
   return ($rset[0]['cantidad']);
}
function obtener_perfil ($connid, $login) {
   $query = "Select tppf.nombre nomperfil, uspf.id_perfil,  uspf.id_usuario,
   					uspf.id_perf_unico,    usua.nombres,	usua.apellidos, uspf.id_sede
               From segu_perfil_x_usuario uspf,
                    segu_usuarios         usua,
                    conf_tipo_perfil      tppf 
              Where uspf.id_usuario   = usua.id_usuario
                And uspf.id_perfil    = tppf.id_perfil
				And uspf.estado       = 'A'
                And usua.login        = '".$login."'";
   $result = dbquery($query, $connid);
   $rset = dbresult($result);
   return ($rset);
}
function requiere_cambio_pwd ($connid, $login) {
   $query = " Select count(9) conteo
                From segu_usuarios usua
               Where usua.pwd   = md5(usua.numero_id)
			     And usua.login = '".$login."'";
   $result = dbquery($query, $connid);
   $rset = dbresult($result);
   if ($rset[0]['conteo'] > 0) {
	   return (true);
   } else {
	   return (false);
   }
}
function cambiar_pwd ($connid, $login, $new_pwd) {
   $query = "Update segu_usuarios usua
                set usua.pwd = md5('".$new_pwd."')
			  Where usua.pwd   = md5(usua.numero_id)
			    And usua.login = '".$login."'";
   $result = dbquery($query, $connid);
   return (true);
}
function reset_pwd ($connid, $id_usuario){
	$query = "Update segu_usuarios usua
                 set usua.pwd = md5(usua.numero_id)
	 		   Where usua.id_usuario = ".$id_usuario;
    $result = dbquery($query, $connid);
    $query = "Select concat(usua.nombres,' ',usua.apellidos) nombres, usua.login
                From segu_usuarios usua
			   Where usua.id_usuario = ".$id_usuario;
	$result = dbquery($query, $connid);
    $rset = dbresult($result); 
    return ($rset[0]);
}
function validar_permisos ($connid, $nombre_prog) {
   if (isset ( $_SESSION['id_perfil'] ) ) {
       $v_id_perfil = $_SESSION['id_perfil'];
	   $query = "Select count(9) conteo
	               From segu_programas_x_perfil pfpr,
				        segu_programas          prog
				  Where pfpr.id_programa = prog.id_programa
				    And pfpr.id_perfil   = ".$v_id_perfil."
				    And prog.archivo     = '".$nombre_prog."'";
	 $result = dbquery($query, $connid);
     $rset = dbresult($result);
     if ($rset[0]['conteo'] > 0) {
        return (true);
     }
     else {
        return (false);
     }
   } else {
      return (false);
   }
}
?>
