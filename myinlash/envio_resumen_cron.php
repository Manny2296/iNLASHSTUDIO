<?php
session_start();
include ("/home/inlifes1/public_html/myinlash/lib/inlife_inc.php");
include ("/home/inlifes1/public_html/myinlash/lib/".$db_engine_lib);
include ("/home/inlifes1/public_html/myinlash/lib/swiftemailer/lib/swift_required.php");
include ("/home/inlifes1/public_html/myinlash/lib/notificaciones_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
notificar_citas($conn);
echo("citas: OK");
notificar_vencimiento($conn);
echo("vencimiento: OK");
notificar_descongelar($conn);
echo("descongelar: OK");
notificar_inasistencias($conn);
echo("inasistencias: OK");
notificar_cumpleanos($conn);
echo("cumpleanos: OK");
notificar_mantenimientos($conn);
echo("mantenimientos: OK");
eliminar_resultados($conn);
dbdisconn ($conn);
?>