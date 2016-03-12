<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/usuarios_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'clientes_frm.php') ) {
		$v_id_perf_unico = $_REQUEST['p_id_perf_unico'];
		$r_usuario = detalle_usuario ($conn, $v_id_perf_unico);
		$v_genero = $r_usuario['genero'];
		if (!is_null($r_usuario['fecha_nacimiento']) && $r_usuario['fecha_nacimiento'] != ""){
			$fecha = DateTime::createFromFormat('Y-m-d', $r_usuario['fecha_nacimiento']);
			$v_fecha_nacimiento = $fecha->format('d-m-Y');
		} else {
			$v_fecha_nacimiento = null;
		}
		if (!is_null($r_usuario['fecha_ingreso']) && $r_usuario['fecha_ingreso'] != ""){
			$fecha = DateTime::createFromFormat('Y-m-d', $r_usuario['fecha_ingreso']);
			$v_fecha_ingreso = $fecha->format('d-m-Y');
		} else {
			$v_fecha_ingreso = null;
		}
?>
<html><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Consulta de usuarios del sistema</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="titulo">CLIENTES INLIFE</div>
     <div class="capa_form_sf">
        <form name="forma" id="forma" action="#">
        <table width="80%" border="0" cellpadding="0" cellspacing="0">
          <tr>
			<th>Tipo de documento de identidad:</th>
            <td><?php echo($r_usuario['abreviatura']); ?></td>
          </tr>
          <tr>
			<th>N&uacute;mero de documento:</th>
            <td><?php echo($r_usuario['numero_id']); ?></td>
          </tr>
          <tr>
			<th>Nombres:</th>
            <td><?php echo($r_usuario['nombres']); ?></td>
          </tr>
          <tr>
			<th>Apellidos:</th>
            <td><?php echo($r_usuario['apellidos']); ?></td>
          </tr>
          <tr>
			<th>Tel&eacute;fono fijo:</th>
            <td><?php echo($r_usuario['telefono']); ?></td>
          </tr>
          <tr>
			<th>M&oacute;vil:</th>
            <td><?php echo($r_usuario['celular']); ?></td>
          </tr>
          <tr>
			<th>Email:</th>
            <td><a href="mailto:<?php echo($r_usuario['email']); ?>"><?php echo($r_usuario['email']); ?></a></td>
          </tr>
          <tr>
			<th>Genero:</th>
            <td><?php echo($r_usuario['genero']); ?></td>
          </tr>
          <tr>
			<th>Fecha de nacimiento:</th>
            <td><?php echo($v_fecha_nacimiento);?></td>
          </tr>
          <tr>
			<th>Eps:</th>
            <td><?php echo($r_usuario['nomeps']); ?></td>
           <tr>
			<th>Prepagada:</th>
            <td><?php echo($r_usuario['nomprepagada']); ?></td>
          </tr>
          <tr>
			<th>Login:</th>
            <td><?php echo($r_usuario['login']); ?></td>
          </tr>
          <tr>
			<th>Fecha de inscripci&oacute;n:</th>
            <td><?php echo($v_fecha_ingreso); ?></td>
          </tr>
          <tr>
			<th>Anotaciones personales:</th>
            <td><?php echo($r_usuario['descripcion']); ?></td>
          </tr>
          <tr>
              <td colspan="2" align="center"><input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Regresar" onClick="javascript:top.GB_hide();" /> </td>
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