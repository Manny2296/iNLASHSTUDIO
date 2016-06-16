<?php
/* 
 Libreria de funciones utilitarias del sistema
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca01@gmail.com
  Desarrollo: Dev Manuel Felipe S.R 
 			manuel.sanchez-r@mail.escuelaing.edu.co
 Fecha     : 11/02/2010 11:30 a.m.
 Version   : 1.0
*/
function lista_sn ($nombre, $valor) {
	?>
    <select id="<?php echo ($nombre); ?>" name="<?php echo ($nombre); ?>">
       <option value=""></option>
       <option value="S" <?php if($valor == "S") {echo("Selected");}?>>Si</option>
       <option value="N" <?php if($valor == "N") {echo("Selected");}?>>No</option>
    </select>
    <?php
	return (null);
}
function get_mes($mes) {
	$t_mes[1] = 'enero';
	$t_mes[2] = 'febrero';
	$t_mes[3] = 'marzo';
	$t_mes[4] = 'abril';
	$t_mes[5] = 'mayo';
	$t_mes[6] = 'junio';
	$t_mes[7] = 'julio';
	$t_mes[8] = 'agosto';
	$t_mes[9] = 'septiembre';
	$t_mes[10] = 'octubre';
	$t_mes[11] = 'noviembre';
	$t_mes[12] = 'diciembre';
	
	return($t_mes[$mes]);
}
?>