<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/graph_utl.php");
include ($path."/lib/flash_chart/php-ofc-library/open-flash-chart.php");
include ($path."/lib/programacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$v_fecha_fin = new DateTime();
$v_fecha_ini = DateTime::createFromFormat('d-m-Y', '01-'.$v_fecha_fin->format('m-Y'));
$v_fecha_ini->sub(new DateInterval('P1M'));
$v_id_usuario = $_SESSION['id_usuario'];
$v_id_servicio = $_REQUEST['p_id_servicio'];
$t_servicios = lista_servicios_cont ($conn, $v_id_usuario);
$t_reporte = reporte_asistencia ($conn, $v_fecha_ini->format('d-m-Y'), $v_fecha_fin->format('d-m-Y'), $v_id_usuario, $v_id_servicio);
$t_base = reporte_asistencia_base ($conn, $v_fecha_ini->format('d-m-Y'), $v_fecha_fin->format('d-m-Y'), $v_id_usuario, $v_id_servicio);
$v_fecha_rango_ini = DateTime::createFromFormat('Y-m-d', get_primera_fecha($conn, $v_id_usuario, $v_id_servicio, $v_fecha_ini->format('d-m-Y')));

dbdisconn ($conn);

//crear menu de opciones
$v_menu = new ofc_menu("#999999", "#707070");
$v_cont = 0;
foreach($t_servicios as $dato) {
	if($dato['id_servicio']==$v_id_servicio) {
		$v_nom_servicio = $dato['nombre'];
	}
	$t_item[$v_cont] = new ofc_menu_item ("Ver asistencia para ".$dato['nombre'], "ver_servicio_".$dato['id_servicio']);
	$v_cont++;	
}
$v_menu->values($t_item);
$v_titulo = new Title(utf8_encode($v_nom_servicio));
$v_titulo->set_style("font-size:11px;text-align=center;font-family:Verdana, Geneva, sans-serif;font-weight:bold;");

// establecer rango y
$v_min = 0;
$v_max = 0;
if (is_array($t_reporte)) {
	foreach($t_reporte as $dato) {
		if ($v_min == 0 || $v_min > $dato['cant']) {
			$v_min = $dato['cant'];
		}
		if ($v_max == 0 || $v_max < $dato['cant']) {
			$v_max = $dato['cant'];
		}
	}
}
foreach($t_base as $dato) {
	if ($v_min == 0 || $v_min > $dato['continuidad']) {
		$v_min = $dato['continuidad'];
	}
	if ($v_max == 0 || $v_max < $dato['continuidad']) {
		$v_max = $dato['continuidad'];
	}
}
$v_max++;
$v_eje_x = new x_axis();
$v_eje_x->colour("#909090");
$v_eje_x->set_grid_colour("#000000");

$v_eje_y = new y_axis();
$v_eje_y->set_grid_colour("#000000");
$v_eje_y->set_colour("#909090");
$v_eje_y->set_range($v_min, $v_max, 1);

$v_chart = new open_flash_chart();
$v_chart->set_bg_colour( '#FFFFFF' );
$v_chart->set_menu( $v_menu );
$v_chart->set_title( $v_titulo );

$v_cont = 0;
$v_pos_base = 0;
$v_pos_rep = 0;
$v_semana_ant = null;
$v_intervalo = new DateInterval('P1D');
while($v_fecha_rango_ini <= $v_fecha_fin) {
	$v_semana = $v_fecha_rango_ini->format('W');
	if (is_null($v_semana_ant) || $v_semana_ant != $v_semana) {
		$t_semanas[$v_cont] = 'Semana '.$v_semana.' ('.$v_fecha_rango_ini->format('m').'-'.$v_fecha_rango_ini->format('Y').')';
		$v_semana_ant = $v_semana;
		//verificar la base y establecerla
		if ( $v_semana == $t_base[$v_pos_base]['semana'] && $v_fecha_rango_ini->format('Y') == $t_base[$v_pos_base]['ano'] ) {
			$t_base_valor[$v_cont] = (float)$t_base[$v_pos_base]['continuidad'];
			if(count($t_base) > $v_pos_base+1) {
				$v_pos_base++;
			}
		} else {
			$t_base_valor[$v_cont] = (float)$t_base[$v_pos_base]['continuidad'];
		}
		//verifica el valor de asistencia de la semana
		if ($t_reporte[$v_pos_rep]['semana'] = $v_semana && $v_fecha_rango_ini->format('Y') == $t_reporte[$v_pos_rep]['ano'] ) {
			$t_reporte_valor[$v_cont] = (float)$t_reporte[$v_pos_rep]['cant'];
			if(count($t_reporte) > $v_pos_rep+1) {
				$v_pos_rep++;
			}
		} else {
			$t_reporte_valor[$v_cont] = 0;
		}
		$v_cont++;
	}
	$v_fecha_rango_ini->add($v_intervalo);
}
$v_eje_x->set_labels_from_array($t_semanas);
$v_eje_x-> set_3d(2);

$v_cont = 0;
$v_estd = null;
$v_pos = -1;
//continuidad esperada
$v_dot = new dot();
$v_dot->size = 1;
$v_dot->tooltip( '#x_label#:#val#' );
$v_color = random_hex_color();
$v_dot->colour($v_color);

$v_line = new line();
$v_line->set_default_dot_style($v_dot);
$v_line->set_width( 2 );
$v_line->set_colour( $v_color );
$v_line->set_values ($t_base_valor);
$v_line->set_key('Continuidad Esperada', 12);
$v_chart->add_element ($v_line);
//asistencia del usuario
$v_color_1 = random_hex_color();
$v_dot_1 = new dot();
$v_dot_1->size = 1;
$v_dot_1->tooltip( '#x_label#:#val#' );
$v_dot_1->colour($v_color_1);

$v_line_1 = new line();
$v_line_1->set_default_dot_style($v_dot_1);
$v_line_1->set_width( 2 );
$v_line_1->set_colour( $v_color_1 );
$v_line_1->set_values ($t_reporte_valor);
$v_line_1->set_key('Tu Asistencia', 12);
$v_chart->add_element ($v_line_1);

$v_chart->set_x_axis( $v_eje_x );
$v_chart->set_y_axis( $v_eje_y );

echo ($v_chart->toPrettyString());
?>