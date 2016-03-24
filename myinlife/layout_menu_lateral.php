<?php
if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'layout_menu_lateral.php') ) {
		$v_id_perfil = $_SESSION['id_perfil'];
		//armar tabla de opciones de menu según perfil
		switch ($v_id_perfil) {
			case 1:
				$t_opciones[0]['texto'] = 'Parametrizaci&oacute;n';
				$t_opciones[0]['url'] = 'sysconfig_frm.php';
				$t_opciones[1]['texto'] = 'Servicios';
				$t_opciones[1]['url'] = 'servicios_lst.php';
				$t_opciones[2]['texto'] = 'Productos';
				$t_opciones[2]['url'] = 'productos_lst.php';
				$t_opciones[3]['texto'] = 'Usuarios del sistema';
				$t_opciones[3]['url'] = 'usuarios_consulta_frm.php';
				$t_opciones[4]['texto'] = 'Clientes';
				$t_opciones[4]['url'] = 'clientes_consulta_frm.php';
				$t_opciones[5]['texto'] = 'Programaci&oacute;n';
				$t_opciones[5]['url'] = 'programacion_lst.php';
				$t_opciones[6]['texto'] = 'Facturaci&oacute;n';
				$t_opciones[6]['url'] = 'facturacion_lst.php';
				$t_opciones[7]['texto'] = 'Sedes';
				$t_opciones[7]['url'] = 'sedes_lst.php';
				break;	
		}
		//dibujar menu lateral
?>
	<div id="menu_lateral">
    
	  <div id="botones">
<?php
		foreach($t_opciones as $menu) {
?>
    	  <input class="button white" type="button" value="<?php echo($menu['texto']); ?>" onClick="location.replace('<?php echo($menu['url']); ?>')" /><p>&nbsp;<p>
<?php
		}
?>
    	</div>
    </div>
<?php	
	}
	else {
		include ($path."/lib/mensaje_utl.php");
		mensaje(2, 'Usted no tiene permisos para acceder esta opci&oacute;n', 'javascript:history.go(-1);', '_self');
	}      
} else {
	include ($path."/lib/mensaje_utl.php");
	mensaje(2, 'Su sesi&oacute;n no est&aacute; activa.<br>Por favor ingrese al sistema nuevamente', $url_login, '_parent');
}
?>
