<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/servicios_utl.php");
include ($path."/lib/usuarios_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'cliente_servicios_lst.php') ) {
		$v_id_usuario = $_REQUEST["p_id_usuario"];
    $v_id_perf_unico = $_REQUEST["p_id_perf_unico"];
		$t_servicios = lista_servicios_cliente($conn, $v_id_usuario);
		$v_cont = 0;
		$r_usuario = nombres_usua($conn, $v_id_usuario);
		$v_nombre = $r_usuario['nombres']." ".$r_usuario['apellidos'];
		$v_hoy = new DateTime();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Lista de servicios del cliente</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" language="javascript">
    function agregar() {
		myForm = document.forma;
		myForm.p_accion.value = "crear";
		myForm.action = "cliente_servicios_frm.php";
		myForm.submit();
	}
	function editar() {
		myForm = document.forma;
		myForm.p_accion.value = "editar";
		myForm.action = "cliente_servicios_frm.php";
		myForm.submit();
	}
	function eliminar(){
		myForm = document.forma;
		if (confirm ("Se dispone a eliminar el servicio seleccionado para el cliente.\n\nDesea Continuar?")) {
			myForm.action = "exec_del_servcliente.php";
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
  <div class="sub_tit">SERVICIOS PREPAGADOS POR <?php echo(strtoupper($v_nombre)); ?></div>
     <div id="contiene_tabla">
        <form action="#" name="forma" id="forma" method="post">
        <input type="hidden" name="p_id_usuario" id="p_id_usuario" value="<?php echo($v_id_usuario); ?>" />
        <input type="hidden" name="p_id_perf_unico" id="p_id_perf_unico" value="<?php echo($v_id_perf_unico); ?>" />
        <input type="hidden" name="p_accion" id="p_accion" /> 
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td colspan="8"><div id="barra_botones">
                <a href="javascript:agregar();" class="button"><span>Agregar</span></a>&nbsp;
                <a href="javascript:editar();" class="button"><span>Modificar</span></a>&nbsp;
                <a href="javascript:eliminar();" class="button"><span>Eliminar</span></a>&nbsp;
                <a href="javascript:top.GB_hide();" class="button"><span>Cerrar</span></a>
              </div></td>
            </tr>
            <tr class="t_header">
              <td>&nbsp;</td>
              <td>Servicio</td>
              <td>Cantidad Contratada</td>
              <td>Fecha de Adquisici&oacute;n</td> 
              <td>Fecha de caducidad</td>
              <td>Sesiones Disponibles</td>
              <td>Sesiones Semanales</td>
              <td>Estado</td>
            </tr>
            <?php
			if (is_array($t_servicios)) {
				foreach ($t_servicios as $dato) {
					$v_fecha = DateTime::createFromFormat('Y-m-d', $dato['fecha']);
					if(is_null($dato['caducidad']) || $dato['caducidad'] == "") {
						$v_caducidad = null;
					} else {
						$v_caducidad = DateTime::createFromFormat('Y-m-d', $dato['caducidad']);
					}
					if (!is_null($v_caducidad) && $v_caducidad < $v_hoy) {
						$v_estado = "Caducado";
					} elseif ($dato['congelar'] == "S") {
						$v_estado = "Congelado";
					} else {
						$v_estado = "Vigente";
					}
			?>
            <tr class="t_texto">
              <td><input type="radio" id="p_id_servicio" name="p_id_servicio" value="<?php echo($dato['id_servicio']."|".$v_fecha->format("d-m-Y")); ?>" <?php if ($v_cont == 0) { echo("Checked"); } ?> /></td>
              <td><?php echo($dato['nombre']); ?></td>
              <td><div align="center"><?php echo($dato['cantidad']); ?></div></td>
              <td><div align="center"><?php echo($v_fecha->format('d-m-Y')); ?></div></td>
              <td><div align="center"><?php echo($v_caducidad->format('d-m-Y')); ?></div></td>
              <td><div align="center"><?php echo($dato['restantes']); ?></div></td>
              <td><div align="center"><?php echo($dato['continuidad']); ?></div></td>
              <td><div align="center"><?php echo($v_estado); ?></div></td>
            </tr>
            <?php
					$v_cont++;
				}
			}
			if ($v_cont == 0) {
			?>
            <tr class="t_texto" height="40">
              <td colspan="8"><div align="center">No hay servicios en prepago definidos para el usuario</div></td>
            </tr>
            <?php
			}
			?>
            <tr>
              <td colspan="8"><div id="barra_botones">
                <a href="javascript:agregar();" class="button"><span>Agregar</span></a>&nbsp;
                <a href="javascript:editar();" class="button"><span>Modificar</span></a>&nbsp;
                <a href="javascript:eliminar();" class="button"><span>Eliminar</span></a>&nbsp;
                <a href="javascript:top.GB_hide();" class="button"><span>Cerrar</span></a>
              </div></td>
            </tr>
          </table>
        </form>
     </div>
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