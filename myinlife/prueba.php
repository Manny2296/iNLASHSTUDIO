<?php
session_start();
include ("/home/inlifes1/public_html/myinlife/lib/inlife_inc.php");
include ("/home/inlifes1/public_html/myinlife/lib/".$db_engine_lib);
include ("/home/inlifes1/public_html/myinlife/lib/swiftemailer/lib/swift_required.php");
include ("/home/inlifes1/public_html/myinlife/lib/notificaciones_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
notificar_citas($conn);
eliminar_resultados($conn);
dbdisconn ($conn);
?>