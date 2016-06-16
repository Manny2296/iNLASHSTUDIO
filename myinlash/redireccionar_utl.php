<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlash";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/programacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$v_id_programacion = $_REQUEST['p_id_programacion'];
$v_requiere = req_toma_medidas ($conn, $v_id_programacion);
if ($v_requiere == "S") {
	$v_id_usuario = get_id_usuario_prog ($conn, $v_id_programacion);
	$v_url = "cliente_ficha_frm.php?p_id_programacion=".$v_id_programacion."&p_id_usuario=".$v_id_usuario;	
} else {
	$v_url = "programacion_asistencia_frm.php?p_id_programacion=".$v_id_programacion;
}
?>
<html>
<head>
  <title>Redireccionando...</title>
  <script type="text/javascript" language="javascript">
     function redireccion(){
		 //alert ("<?php echo($v_url); ?>");
		 location.replace("<?php echo($v_url); ?>");
	 }
  </script>
</head>
<body onLoad="redireccion();">
</body>
</html>