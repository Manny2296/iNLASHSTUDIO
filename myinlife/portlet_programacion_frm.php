<?php
if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'portlet_programacion_frm.php') ) {
		$v_permitir_prog = obtener_valor_param ($conn, 'PEPR');
		$v_permitir_canc = obtener_valor_param ($conn, 'PECA');
		$v_horas_canc = obtener_valor_param ($conn, 'TICA');
?>
    <div id="contiene_tabla">
<?php 
		while ($v_fecha_ini <= $v_fecha_fin) {
			$t_eventos = horario_usuario ($conn, $v_id_usuario, $v_fecha_ini->format('d-m-Y'));	
			$v_fecha_txt = strftime ("%A, %d de %B de %Y", strtotime($v_fecha_ini->format('m/d/Y')));
?>
	  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
	    <tr class="t_header">
          <td colspan="2" nowrap>
            <?php if ($v_fecha_ini >= $v_hoy && $v_permitir_prog == 'S') { ?>
              <div id="espacio_botones"><a href="javascript:reserva('<?php echo($v_fecha_ini->format('d-m-Y')); ?>');" class="button white small">Hacer reserva</a></div><br />
            <?php } ?>
              <div id="disponible"><?php echo($v_fecha_txt); ?></div>
          </td>
        </tr>
<?php
			if(is_array($t_eventos)){
				foreach($t_eventos as $dato) {
					$v_fecha_evento = DateTime::createFromFormat('d-m-Y H:i', $v_fecha_ini->format('d-m-Y')." ".$dato['hora_ini']);
					$v_fecha_evento->sub(new DateInterval('PT'.$v_horas_canc.'H'));
					if ($v_fecha_evento >= $v_hoy && $v_permitir_canc == "S") {
						$v_cancelar = true;
					} else {
						$v_cancelar = false;
					}
?>
		<tr class="t_texto">
          <td nowrap width="20"><?php echo($dato['hora_ini'].'-'.$dato['hora_fin']); ?></td>
          <td><?php if ($v_cancelar) { ?><div id="espacio_botones"><a href="javascript:cancelar(<?php echo($dato['id_programacion']); ?>);" class="button white small">Cancelar Reserva</a></div><br/><?php } ?>
		  <div align="center"><?php echo($dato['nombre']); ?></div></td>
        </tr>
<?php
				}
			} else {
?>
		<tr class="t_texto">
          <td colspan="2" height="40" nowrap><div align="center">No hay sesiones programadas</div></td>
        </tr>
<?php
			}
			$v_fecha_ini->add($v_interval);
?>
	 </table><p>&nbsp;</p>
<?php	
		}
	}
	else {
		mensaje(2, 'Usted no tiene permisos para acceder esta opci&oacute;n', 'javascript:history.go(-1);', '_self');
	}      
} else {
	mensaje(2, 'Su sesi&oacute;n no est&aacute; activa.<br>Por favor ingrese al sistema nuevamente', $url_login, '_parent');
}
?>