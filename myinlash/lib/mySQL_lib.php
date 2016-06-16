<?php 
/* 
 Libreria de conectividad y procesamiento de datos para sistemas MySQL.
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca@cibercolegios.com
   Desarrollo: Dev Manuel Felipe S.R 
 			manuel.sanchez-r@mail.escuelaing.edu.co
 Fecha     : 02/07/2004 23:00
 Version   : 1.0
*/

/* 
 Funcion : dbconn
 IN      : $p_server --> Nombre del servidor (default localhost)
           $p_dbname --> Nombre de la base de datos.
		   $p_dbuser --> Nombre de usuario de acceso al servidor MySQL.
		   $p_dbpwd  --> Contraseña de usuario
 OUT     : Identificador de conexión en exito o falso en fracaso 
 */
   function dbconn ($p_server, $p_dbname, $p_dbuser, $p_dbpwd) {
      $v_connid = mysql_connect ($p_server, $p_dbuser, $p_dbpwd, true);
	  if ($v_connid != false) {
	     if (mysql_select_db ($p_dbname, $v_connid) ){
			mysql_set_charset ('latin1', $v_connid);
			mysql_query('set collation_connection=latin1_spanish_ci', $v_connid);
		    return $v_connid;
		 }
		 else {
		    echo ("La base de datos ". $p_dbname . " no está disponible o no existe");
		 }
	  }
	  else {
	     echo ("Error de conexión: Servidor, Nombre de Usuario o Password incorrectos!");
		 return false;
	  }
   }
 /* 
  Funcion : dbdisconn 
  IN      : $p_idconn --> Identificador de conexion a base de datos
  OUT     : Verdadero en exito o falso de lo contrario.
  */
   function dbdisconn ($p_idconn) {
      if (mysql_close ($p_idconn)) {
	     return true;
	  }
	  else {
	     echo ("Conexión no cerrada: ". mysql_error($p_idconn));
	     return false;
	  }
   }
/* 
 Funcion  : dbquery
 IN       : $p_query --> Sentencia SQL a Ejecutar (DML)
            $p_idconn --> Identificador de conexión con el servidor MySQL.
 OUT      : Verdadero o id de resultado si exitoso, falso de lo contrario. El sistema tiene en cuenta el
            control transaccinal del proceso por lo cual las tablas MySQL deben ser de tipo InnoDB.
 */

   function dbquery ($p_query, $p_idconn) {
      $v_result = mysql_query ($p_query, $p_idconn);
	  if ($v_result == false) {
       echo ("Query no ejecutada:<br> ". $p_query . "<br><br>Error: " .mysql_error());
       mysql_query ("ROLLBACK", $p_idconn);
		 return false;
	  }
	  else {
	     mysql_query ("COMMIT", $p_idconn);
	     return $v_result;
	  }
   }
   
/* 
 Funcion : dbresult
 IN      : $p_result --> identificador de resultado de una consulta SQL
 OUT     : Arreglo multidimensional con los resultados de la consulta indexados por fila 
           a partir de 0 y hasta el número máximo de registros recuperados.
 */
   function dbresult ($p_result) {
      if (mysql_num_rows($p_result) == 0) {
	     return null;
	  }
      for ( $i = 0; $i < mysql_num_rows($p_result); $i++) {
	     $v_resset = mysql_fetch_array ($p_result, MYSQL_NUM);
	     for ($x = 0; $x < mysql_num_fields($p_result); $x++) {
	        $res_array[$i][mysql_field_name ($p_result, $x)] = $v_resset[$x];
	     }
	  }
	  mysql_free_result ($p_result);
	  return $res_array;
   }
/* 
 Funcion : db_massive_upload
 IN      : $p_connid --> identificador de conexión MySQL
           $p_query  --> Query para cargue masivo
 OUT     : Número de filas afectadas en la ejecución del comando DML
 */
   function db_massive_upload ($p_idconn, $p_query) {
      $cont = 0;
      $v_result = mysql_query ($p_query, $p_idconn);
	  if ($v_result == false) {
         echo ("Cargue no ejecutado:<br> ". $p_query . "<br><br>Error: " .mysql_error());
         mysql_query ("ROLLBACK", $p_idconn);
	  }
	  else {
	     $cont = mysql_affected_rows ($p_idconn);
	     mysql_query ("COMMIT", $p_idconn);
	  }
	  return ($cont);
   }
   function db_change ($db_name, $connid) {
      if (mysql_select_db ($db_name, $connid) ){
		 return true;
      }
	  else {
		 echo ("La base de datos ".$db_name. " no está disponible o no existe");
	  }
   }
?>