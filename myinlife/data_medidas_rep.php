<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/graph_utl.php");
include ($path."/lib/flash_chart/php-ofc-library/open-flash-chart.php");
include ($path."/lib/antropometria_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$v_id_usuario = $_SESSION['id_usuario'];
$t_medidas = lista_medidas($conn, $v_id_usuario);
if (isset($_REQUEST['p_id_medida'])) {
	$v_id_medida  = $_REQUEST['p_id_medida'];
} else {
	$v_id_medida = $t_medidas[0]['id_medida'];
}
$t_reporte = reporte_medida_usuario ($conn, $v_id_usuario, $v_id_medida);
dbdisconn ($conn);

//crear menu de opciones
$v_menu = new ofc_menu("#999999", "#707070");
$v_cont = 0;
if(is_array($t_medidas)) {
	foreach($t_medidas as $dato) {
		$t_item[$v_cont] = new ofc_menu_item ("Ver ".$dato['nombre'], "ver_medida_".$dato['id_medida']);
		$v_cont++;
	}
} else {
	$t_item = null;
}
$v_menu->values($t_item);
// establecer rango y
$v_min = 0;
$v_max = 0;
$v_cont =0;
if(is_array($t_reporte)) {
	foreach($t_reporte as $dato) {
		if ($v_min == 0 || $v_min > $dato['valor']) {
			$v_min = $dato['valor'];
		}
		if ($v_max == 0 || $v_max < $dato['valor']) {
			$v_max = $dato['valor'];
		}
		$t_labels_x[$v_cont] = $dato['fecha'];
		$t_valores[$v_cont] = (float)$dato['valor'];
		$v_cont++;
	}
	$v_min-=0.3;
	$v_max+=0.3;
	if ($v_min < 0) {
		$v_min = 0;
	}
} else {
	$t_labels_x[$v_cont] = '01-01-2012';
	$t_valores[$v_cont] = 0;
}
$v_steps = round(($v_max - $v_min)/5, 2);
if ($v_steps == 0) {
	$v_max++;
	$v_steps = 0.2;
}
$v_titulo = new Title(utf8_encode("Medidas registradas de ".$t_reporte[0]['nombre']));
$v_titulo->set_style("font-size:11px;text-align=center;font-family:Verdana, Geneva, sans-serif;font-weight:bold;");
$v_eje_x = new x_axis();
$v_eje_x->colour("#909090");
$v_eje_x->set_grid_colour("#000000");
$v_eje_x->set_labels_from_array($t_labels_x);
$v_eje_x-> set_3d(2);

$v_eje_y = new y_axis();
$v_eje_y->set_grid_colour("#000000");
$v_eje_y->set_colour("#909090");
$v_eje_y->set_range($v_min, $v_max, $v_steps);
$v_leyenda_y = new y_legend(utf8_encode($t_reporte[0]['unidad']));
$v_leyenda_y->set_style( '{font-size: 11px; color: #000000}' );

$v_chart = new open_flash_chart();
$v_chart->set_title( $v_titulo );
$v_chart->set_bg_colour( '#FFFFFF' );
$v_chart->set_menu( $v_menu );


//grafico de medidas
$v_dot = new dot();
$v_dot->size = 1;
$v_dot->tooltip( '#x_label#:#val# '.$t_reporte[0]['unidad'] );
$v_color = random_hex_color();
$v_dot->colour($v_color);

$v_line = new line();
$v_line->set_default_dot_style($v_dot);
$v_line->set_width( 2 );
$v_line->set_colour( $v_color );
$v_line->set_values ($t_valores);
$v_line->set_key($t_reporte[0]['nombre'], 12);
$v_chart->add_element ($v_line);
$v_chart->set_x_axis( $v_eje_x );
$v_chart->set_y_axis( $v_eje_y );
$v_chart->set_y_legend($v_leyenda_y);

echo ($v_chart->toPrettyString());
?>