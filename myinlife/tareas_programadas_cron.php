<?php
session_start();
include ("/home/inlifes1/public_html/myinlife/lib/inlife_inc.php");
include ("/home/inlifes1/public_html/myinlife/lib/".$db_engine_lib);
include ("/home/inlifes1/public_html/myinlife/lib/programacion_dml.php");
// actualizar asistencias del d�a
$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
asistencias_pasadas ($conn);
echo("asistencias actualizadas OK");
dbdisconn ($conn);
?>