<?php
/* 
 Libreria de utilidades de control visual y layout
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca01@gmail.com
  Desarrollo: Dev Manuel Felipe S.R 
 			manuel.sanchez-r@mail.escuelaing.edu.co
 Fecha     : 03/11/2010 08:00
 Version   : 1.0
*/
function obtener_skin ($connid) {
   $query = "Select valor
	           From conf_parametros para
		      Where para.codigo = 'SKIN'";
   $result = dbquery ($query, $connid);
   $rset = dbresult($result);
   return ($rset[0]['valor']);
}
?>