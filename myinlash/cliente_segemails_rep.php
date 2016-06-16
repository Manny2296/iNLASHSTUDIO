<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/usuarios_utl.php");
include ($path."/lib/notificaciones_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'clientes_frm.php') ) {
		$v_id_usuario = $_REQUEST['p_id_usuario'];
		$r_usuario = nombres_usua($conn, $v_id_usuario);
		$v_nombre = $r_usuario['nombres']." ".$r_usuario['apellidos'];
		$t_emails = lista_resultados($conn, $v_id_usuario);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Seguimiento de notificaciones</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="sub_tit">&Uacute;LTIMAS NOTIFICACIONES PARA <?php echo(strtoupper($v_nombre)); ?></div>
    <div id="contiene_tabla">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr class="t_header">
            <th>FECHA</th>
            <th>EMAIL</th>
            <th>TIPO</th>
            <th>RESULTADO</th>
          </tr>
          <?php 
		  if (is_array($t_emails)) {
			  foreach($t_emails as $dato) {
		  ?>
          <tr class="t_texto">
            <td><?php echo($dato['fecha']); ?></td>
            <td><?php echo($dato['email']); ?></td>
            <td><?php echo($dato['tipo']); ?></td>
            <td><?php echo($dato['resultado']); ?></td>
          </tr>
          <?php
			  }
		  } else {
		  ?>
          <tr class="t_texto">
            <td height="40" colspan="4"><div align="center">No han sido enviados mensajes de notificaci&oacute;n</div></td>
          </tr>
          <?php
		  }
		  ?>
          </table>
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