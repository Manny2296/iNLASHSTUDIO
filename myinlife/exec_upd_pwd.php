<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);
$v_login = $_POST['p_login'];
$v_pwd = $_POST['p_new_pwd'];
cambiar_pwd ($conn, $v_login, $v_pwd);
mensaje(1, 'La contrase&ntilde;a fue actualizada correctamente' , 'logout.php', '_self');
dbdisconn ($conn);
?>