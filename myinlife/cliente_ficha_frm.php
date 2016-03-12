<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/usuarios_utl.php");
include ($path."/lib/antropometria_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'cliente_ficha_frm.php') ) {
		$v_id_usuario = $_REQUEST['p_id_usuario'];
		$v_id_perfil = $_SESSION['id_perfil'];
		$v_activar_fecha = false;
		if ($v_id_perfil != 3) {
			$v_editar = true;
		} else {
			$v_editar = false;
		}
		if (isset($_POST['p_accion'])) {
			$v_accion = $_POST['p_accion'];
			if($v_accion == "crear") {
				$v_hoy = new DateTime();
				$v_req_medidas = true;
				$v_activar_fecha = true;
			} else {
			    $v_hoy = DateTime::createFromFormat('d-m-Y', $_POST['p_fecha']);
				$v_req_medidas = false;
		    }
		} else {
			$v_hoy = new DateTime();
			$v_accion = 'crear';
			$v_req_medidas = require_toma_medidas($conn, $v_id_usuario);
		}
		
		if ($v_editar && $v_req_medidas) {
			$v_activar_fecha = true;
		}
		$r_usuario = nombres_usua($conn, $v_id_usuario);
		$v_nombre = $r_usuario['nombres']." ".$r_usuario['apellidos'];
		$t_medidas = lista_medidas($conn, $v_id_usuario);
		$t_medidas_usua = lista_medidas_usuario ($conn, $v_id_usuario, null);
		$t_fechas = fechas_medidas($conn, $v_id_usuario);
		$t_comentarios = anotaciones_fian ($conn, $v_id_usuario);
		$v_colspan = count($t_fechas);
		$v_ult_medidas = get_ultima_fecha_medidas ($conn, $v_id_usuario);
		$v_fecha_objetivo = DateTime::createFromFormat('Y-m-d', $v_ult_medidas);
		
		if ($v_editar && $v_req_medidas) {
			$v_colspan++;
		}
?>
<html>
<!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Ficha Antropom&eacute;tica</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" language="javascript" src="lib/zxml.js"></script>
<script type="text/javascript" language="javascript" src="lib/popcalendar.js"></script>
<script type="text/javascript" language="javascript">
function refrescar(){
	document.frmrefresh.submit();
	return;
}
function cambiar_obj(id_medida, objetivo){
	var id_usuario = document.forma.p_id_usuario.value;
	<?php if (!is_null($v_ult_medidas)) { ?>
	var fecha = "<?php echo($v_fecha_objetivo->format('d-m-Y')); ?>";
	<?php } else {?>
	var fecha = "";
	<?php } ?>
	if (objetivo == "subir"){
	   objetivo = 'S';
	} else if (objetivo == "bajar"){
	   objetivo = 'B';
	} else {
	   objetivo = 'I';
	}
	var rUrl = "ajax_upd_objetivo.php";
	var rBody = "p_id_usuario="+id_usuario+"&p_fecha="+fecha+"&p_id_medida="+id_medida+"&p_objetivo="+objetivo;
	oDiv = document.getElementById ("respdiv");
	oDiv.innerHTML = "<img src=\"skins/<?php echo($skin); ?>/loader.gif\"><b>Consultando...</b>";
	var oXmlHttp = zXmlHttp.createRequest();
	oXmlHttp.open("post", rUrl, true);
	oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
			 if (oXmlHttp.status == 200) {
				oDiv.innerHTML = oXmlHttp.responseText;
				refrescar();
			 } else {
				oDiv.innerHTML ="<img src=\"skins/<?php echo($skin); ?>/opt_eliminar.png\"><b>AJAX: Problema al traer datos</b>";
			 }
		  }
	   };
	   oXmlHttp.send(rBody); 
	   return;
}
function crear(){
	myForm = document.forma;
	myForm.p_accion.value="crear";
	myForm.action = "cliente_ficha_frm.php";
	myForm.submit();
	return;
}
function editar(fecha){
	myForm = document.forma;
	myForm.p_accion.value="editar";
	myForm.p_fecha.value = fecha;
	myForm.action = "cliente_ficha_frm.php";
	myForm.submit();
	return;
}
function eliminar(fecha){
	if(confirm("Se dispone a eliminar la toma de medidas del día "+fecha+".\n\nDesea Continuar?")){
		myForm = document.forma;
		myForm.p_fecha.value = fecha;
		myForm.action = "exec_del_ficha.php";
		myForm.submit();
	}
	return;
}
function delComment(fecha){
	if(confirm("Se dispone a eliminar los comentarios publicados el día "+fecha+".\n\nDesea Continuar?")){
		myForm = document.forma;
		myForm.p_fecha.value = fecha;
		myForm.action = "exec_del_comentario.php";
		myForm.submit();
	}
	return;
}
</script>
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="sub_tit">FICHA ANTROPOM&Eacute;TRICA DE <?php echo(strtoupper($v_nombre)); ?></div>
    <div id="contiene_tabla">
      <form action="exec_upd_fichacliente.php" method="post" name="forma" id="forma" class="formato">
        <input type="hidden" name="p_id_usuario" id="p_id_usuario" value="<?php echo($v_id_usuario); ?>" />
        <?php if (!$v_activar_fecha) { ?>
        <input type="hidden" name="p_fecha" id="p_fecha" value="<?php echo($v_hoy->format("d-m-Y")); ?>" />
        <?php } ?>
        <input type="hidden" name="p_accion" id="p_accion" />
		<?php 
		if (isset($_REQUEST['p_id_programacion'])) {
		?>
        <input type="hidden" name="p_id_programacion" id="p_id_programacion" value="<?php echo($_REQUEST['p_id_programacion']); ?>" />
        <?php } ?>
        <?php if ($v_editar && !$v_req_medidas) { ?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr align="center">
            <td height="40"><input type="button" name="btn_crear" id="btn_crear" class="button white" value="Generar nuevo set de medidas" onclick="crear();" /></td>
          </tr>
        </table>
        <?php } ?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr class="t_header">
        <?php if ($v_colspan > 0) { ?>
            <th rowspan="2">MEDIDAS</th>
            <th rowspan="2">OBJETIVO</th>
            <th colspan="<?php echo($v_colspan); ?>">FECHA</th>
          </tr>
          <tr class="t_header">
            <?php if (is_array($t_fechas)) {
				  	foreach($t_fechas as $dato) {
						$v_fecha = DateTime::createFromFormat('Y-m-d', $dato['fecha']);
			?>
            <th><?php echo($v_fecha->format('d-m-Y')); if($v_editar && ($v_accion == "crear" || ($v_accion == "editar" && $v_fecha != $v_hoy))) { ?><br />
              <input type="button" name="btn_editar" id="btn_editar" class="button white" value="Modificar" onclick="editar('<?php echo($v_fecha->format('d-m-Y')); ?>');" />&nbsp;<input type="button" name="btn_eliminar" id="btn_eliminar" class="button white" value="Eliminar" onclick="eliminar('<?php echo($v_fecha->format('d-m-Y')); ?>');" />
			  <?php } ?>
            </th>
            <?php
					}
			      }
				  if ($v_editar && $v_req_medidas) {
		   ?>
           <th><?php if(!$v_activar_fecha) { echo($v_hoy->format('d-m-Y')); } else { ?>
             <input type="text" name="p_fecha" id="p_fecha" size="10" readonly="readonly" onClick="popUpCalendar(this, forma.p_fecha);" />
               <?php  } ?>
           </th>
           <?php  } 
		      } else {
		   ?>
           <th>MEDIDAS</th>
           <th>OBJETIVO</th>
           <?php  } ?>
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
              <td colspan="<?php echo($v_colspan + 1); ?>"><?php echo($v_nomtipo); ?></td>
            </tr>
            <?php
				   }
				   $v_id_medida = $dato['id_medida'];
				   $v_objetivo = get_objetivo_medida($conn, $v_id_usuario, $v_id_medida, $v_ult_medidas);
				   if (is_null($v_objetivo)) {
					   $v_objetivo_txt = null;
				   } elseif ($v_objetivo == 'S') {
					   $v_objetivo_txt = 'Aumentar';
				   } else {
					   $v_objetivo_txt = 'Disminuir';
				   }
		    ?>
            <tr class="t_texto">
              <td><?php echo($dato['nombre']); ?></td>
              <?php if (is_array($t_medidas_usua)) { ?>
              <td><div align="center"><?php if (!is_null($v_objetivo)) { echo($v_objetivo_txt.'<br>'); }?><?php if($v_editar) { ?><a href="javascript:cambiar_obj(<?php echo($v_id_medida.", 'subir'"); ?>);"><img src="skins/<?php echo($skin); ?>/icon_subir.png" border="<?php if ($v_objetivo == 'S') { echo("1"); } else { echo("0"); } ?>" alt="Aumentar Medida" title="Aumentar Medida" /></a>&nbsp;<a href="javascript:cambiar_obj(<?php echo($v_id_medida.", 'igual'"); ?>);"><img src="skins/<?php echo($skin); ?>/icon_igual.png" border="<?php if (is_null($v_objetivo)) { echo("1"); } else { echo("0"); } ?>" alt="Sin objetivo" title="Sin Objetivo"/></a>&nbsp;<a href="javascript:cambiar_obj(<?php echo($v_id_medida.", 'bajar'"); ?>);"><img src="skins/<?php echo($skin); ?>/icon_bajar.png" border="<?php if ($v_objetivo == 'B') { echo("1"); } else { echo("0"); } ?>" alt="Disminuir Medida" title="Disminuir Medida" /></a><?php } ?></div></td>
              <?php } else { ?>
              <td>&nbsp;</td>
              <?php
			        }
			  		if (is_array($t_medidas_usua) && count($t_medidas_usua) > $v_pos) {
						while(count($t_medidas_usua) > $v_pos && $t_medidas_usua[$v_pos]['id_medida'] == $v_id_medida) {
							$v_fecha_actual = DateTime::createFromFormat('Y-m-d', $t_medidas_usua[$v_pos]['fecha']);
							if ($v_editar && $v_accion == "editar" && $v_fecha_actual == $v_hoy) {
								if ($dato['calculable'] == 'N') {
			  ?>
              <td><div align="center"><input type="hidden" name="p_id_medida[]" id="p_id_medida" value="<?php echo($v_id_medida); ?>" />
                <input type="text" name="p_valor[]" id="p_valor" size="6" maxlength="6" value="<?php echo($t_medidas_usua[$v_pos]['valor']); ?>" /> <?php echo($dato['unidad']); ?></div></td>
              <?php
								} else {
			  ?>
              <td><div align="center"><input type="hidden" name="p_calculable[]" id="p_calculable" value="<?php echo($v_id_medida); ?>" />Calculable</div></td>
              <?php
								}
							} else {
			  ?>
              <td><div align="center"><?php echo($t_medidas_usua[$v_pos]['valor']." ".$t_medidas_usua[$v_pos]['unidad']); ?></div></td>
              <?php
						    }
						    $v_pos++;
						}
					}
					if ($v_editar && $v_req_medidas) {
						if ($dato['calculable'] == 'N') {
			  ?>
              <td><div align="center"><input type="hidden" name="p_id_medida[]" id="p_id_medida" value="<?php echo($v_id_medida); ?>" />
                <input type="text" name="p_valor[]" id="p_valor" size="6" maxlength="6" /> <?php echo($dato['unidad']); ?></div></td>
              <?php
						} else {
			  ?>
              <td><div align="center"><input type="hidden" name="p_calculable[]" id="p_calculable" value="<?php echo($v_id_medida); ?>" />Calculable</div></td>
              <?php
						}
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
		 	if ($v_editar) { 
		  		$v_new_fecha = new DateTime();	
		  ?>
          <tr class="t_texto">
            <td><?php echo($v_new_fecha->format('d-m-Y')); ?></td>
            <td><textarea name="p_observacion" id="p_observacion" rows="4" cols="50"></textarea></td>
          </tr>
          <?php
			}
			if (is_array($t_comentarios)){
				foreach($t_comentarios as $dato) {
					$v_new_fecha = DateTime::createFromFormat('Y-m-d', $dato['fecha']);
		  ?>
          <tr class="t_texto">
            <td><?php if($v_editar) { ?><a href="javascript:delComment('<?php echo($v_new_fecha->format('d-m-Y')); ?>');"><img src="skins/<?php echo($skin); ?>/opt_eliminar.png" align="baseline" alt="Eliminar Comentario" title="Eliminar Comentario" border="0"></a><?php } ?> <?php echo($v_new_fecha->format('d-m-Y')); ?></td>
            <td><?php echo($dato['texto']); ?></td>
          </tr>
          <?php
				}
			}
		  ?>
          <tr>
              <td colspan="2" align="center"><?php if($v_id_perfil != 3) { ?><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Guardar" onClick="document.forma.submit();" />
              &nbsp;<?php } ?> <input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Cerrar" onClick="javascript:top.GB_hide();" /> </td>
          </tr>
        </table>
      </form>
      <form action="cliente_ficha_frm.php" method="post" name="frmrefresh" id="frmrefresh">
      <input type="hidden" name="p_id_usuario" id="p_id_usuario" value="<?php echo($v_id_usuario); ?>" />
      </form>
    </div>
    <div id="respdiv"></div>
  <!-- InstanceEndEditable -->
  </div>
</body>
<!-- InstanceEnd --></html>
<?php	
	}
	else {
		mensaje(2, 'Usted no tiene permisos para acceder esta opci&oacute;n', 'javascript:history.go(-1);', '_self');
	}      
} else {
	mensaje(2, 'Su sesi&oacute;n no est&aacute; activa.<br>Por favor ingrese al sistema nuevamente', $url_login, '_parent');
}
dbdisconn ($conn);
?>