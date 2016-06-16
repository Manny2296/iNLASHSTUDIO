<?php
/* 
 Libreria de utilidades para la presentación de gráficos usando FlashChart
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca01@gmail.com
  Desarrollo: Dev Manuel Felipe S.R 
 			manuel.sanchez-r@mail.escuelaing.edu.co
 Fecha     : 03/12/2010 11:40 a.m.
 Version   : 1.0
*/
function random_hex_color(){
	$hex_arr = array ( 0 => '0',   1 => '1',  2 => '2',  3 => '3',  4 => '4',
                       5 => '5',   6 => '6',  7 => '7',  8 => '8',  9 => '9',
					  10 => 'A', 11 => 'B', 12 => 'C', 13 => 'D', 14 => 'E',
					  15 => 'F' );
   $v_color = "#";
   for ($x=0; $x<6; $x++) {
      $keyval = rand(0, 15);
	  $v_color .= $hex_arr[$keyval];
   }
   return ($v_color);
}
function get_mes($mes_num, $idioma) {
	$t_mes[1][1] = "Enero";
	$t_mes[2][1] = "Febrero";
	$t_mes[3][1] = "Marzo";
	$t_mes[4][1] = "Abril";
	$t_mes[5][1] = "Mayo";
	$t_mes[6][1] = "Junio";
	$t_mes[7][1] = "Julio";
	$t_mes[8][1] = "Agosto";
	$t_mes[9][1] = "Septiembre";
	$t_mes[10][1] = "Octubre";
	$t_mes[11][1] = "Noviembre";
	$t_mes[12][1] = "Diciembre";
	//en ingles
	$t_mes[1][2] = "January";
	$t_mes[2][2] = "February";
	$t_mes[3][2] = "March";
	$t_mes[4][2] = "April";
	$t_mes[5][2] = "May";
	$t_mes[6][2] = "June";
	$t_mes[7][2] = "July";
	$t_mes[8][2] = "August";
	$t_mes[9][2] = "September";
	$t_mes[10][2] = "October";
	$t_mes[11][2] = "November";
	$t_mes[12][2] = "December";
	
	return ($t_mes[$mes_num][$idioma]);
}
?>