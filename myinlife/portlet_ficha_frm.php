<?php
if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'portlet_ficha_frm.php') ) {
		$v_fecha = get_ultima_fecha_medidas ($conn, $v_id_usuario);
		$v_permitir_obj = obtener_valor_param ($conn, 'PEOB');
		if (!is_null($v_fecha)){
			$v_fecha_cta = DateTime::createFromFormat('Y-m-d', $v_fecha);
			$t_medidas_usua = lista_medidas_usuario ($conn, $v_id_usuario, $v_fecha_cta->format('d-m-Y'));
			setlocale (LC_TIME, 'esp', 'es_ES', 'es_ES.UTF-8', 'Spanish_Spain.1252');
			$v_fecha_txt = strtoupper(strftime ("%d de %B de %Y", strtotime($v_fecha_cta->format('m/d/Y'))));
		} else {
			$t_medidas_usua = null;
		}
		$t_medidas = lista_medidas($conn, $v_id_usuario);
		$t_comentarios = anotaciones_fian ($conn, $v_id_usuario);
?>
    <div id="contiene_tabla">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr class="t_header">
            <th>MEDIDAS</th>
            <?php if (!is_null($v_fecha)) { ?>
            <th><?php echo($v_fecha_txt); ?></th>
            <th>OBJETIVO</th>
            <th>RESULTADO</th>
            <?php } ?>
          </tr>
           <?php
		   if (is_array($t_medidas)){
			   $v_nomtipo = null;
			   $v_id_medida = null;
			   $v_pos=0;
			   foreach ($t_medidas as $dato) {
				   if (is_null($v_nomtipo) || $v_nomtipo != $dato['nomtipo']) {
					   $v_nomtipo = $dato['nomtipo'];
				   ?>
            <tr class="t_header">
              <td><?php echo($v_nomtipo); ?></td>
            </tr>
            <?php
				   }
				   $v_id_medida = $dato['id_medida'];
				   $v_objetivo = $t_medidas_usua[$v_pos]['objetivo'];
				   if (is_null($v_objetivo)) {
					   $v_objetivo_txt = null;
				   } elseif ($v_objetivo == 'S') {
					   $v_objetivo_txt = 'Aumentar';
				   } else {
					   $v_objetivo_txt = 'Disminuir';
				   }
				   $v_estado = $t_medidas_usua[$v_pos]['estado'];
				   if (is_null($v_objetivo)) {
					   $v_estado_txt = '&nbsp;';
				   } elseif ($v_objetivo == 'S') {
					   $v_estado_txt = 'Aument&oacute;';
				   } elseif ($v_estado == "I") {
					   $v_estado_txt = 'Sin Cambios';
				   } else {
					   $v_estado_txt = 'Disminuy&oacute;';
				   }
		    ?>
            <tr class="t_texto">
              <td><?php echo($dato['nombre']); ?></td>
              <?php
			  		if (is_array($t_medidas_usua)) {
			  ?>
              <td><div align="center"><?php echo($t_medidas_usua[$v_pos]['valor']." ".$t_medidas_usua[$v_pos]['unidad']); ?></div></td>
              <td><div align="center"><?php if (!is_null($v_objetivo)) { echo($v_objetivo_txt.'<br>'); }?><?php if($v_permitir_obj=="S") { ?><a href="javascript:cambiar_obj(<?php echo($v_id_medida.", 'subir'"); ?>);"><img src="skins/<?php echo($skin); ?>/icon_subir.png" border="<?php if ($v_objetivo == 'S') { echo("1"); } else { echo("0"); } ?>" alt="Aumentar Medida" title="Aumentar Medida" /></a>&nbsp;<a href="javascript:cambiar_obj(<?php echo($v_id_medida.", 'igual'"); ?>);"><img src="skins/<?php echo($skin); ?>/icon_igual.png" border="<?php if (is_null($v_objetivo)) { echo("1"); } else { echo("0"); } ?>" alt="Sin objetivo" title="Sin Objetivo"/></a>&nbsp;<a href="javascript:cambiar_obj(<?php echo($v_id_medida.", 'bajar'"); ?>);"><img src="skins/<?php echo($skin); ?>/icon_bajar.png" border="<?php if ($v_objetivo == 'B') { echo("1"); } else { echo("0"); } ?>" alt="Disminuir Medida" title="Disminuir Medida" /></a><?php } ?></div></td>
              <td><div align="center"><?php echo($v_estado_txt) ?></div></td>
              <?php
			  			$v_pos++;
					}
			  ?>
            </tr>
           <?php
			   }
		   }
		   ?>
        </table>
        <div class="sub_tit">OBSERVACIONES</div>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr class="t_header">
            <th>Fecha</th>
            <th>Observacion</th>
          </tr>
          <?php
			if (is_array($t_comentarios)){
				foreach($t_comentarios as $dato) {
					$v_new_fecha = DateTime::createFromFormat('Y-m-d', $dato['fecha']);
		  ?>
          <tr class="t_texto">
            <td><?php echo($v_new_fecha->format('d-m-Y')); ?></td>
            <td><?php echo($dato['texto']); ?></td>
          </tr>
          <?php
				}
			}
		  ?>
        </table>
      </form>
    </div>
    <div id="respdiv"></div>
    <div id="fichadiv"></div>
<?php	
	}
	else {
		mensaje(2, 'Usted no tiene permisos para acceder esta opci&oacute;n', 'javascript:history.go(-1);', '_self');
	}      
} else {
	mensaje(2, 'Su sesi&oacute;n no est&aacute; activa.<br>Por favor ingrese al sistema nuevamente', $url_login, '_parent');
}
?>