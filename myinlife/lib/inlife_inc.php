<?php
/*
-- Archivo de configuración
-- Inlife Studio
--
*/
date_default_timezone_set ('America/Bogota');
$default_pais = 169;							// pais por defecto
$default_ciudad = 11;							// ciudad por defecto
$site_domain = "http://www.inlifestudio.com"; 				// dominio de la aplicacion.
$url_login = "http://www.inlifestudio.com/login.php";                   // url de acceso al sistemas.
$db_engine_lib = "mySQL_lib.php";                                       // Motor de base de datos
$db_name = "inlifes1_myinlife";  					// Base de datos de la apliación.
$db_host = "localhost";     					        // Host de la base de datos
$db_user = "inlifes1";     					        // usuario de la base de datos - Privilegios DML solamente 
$db_pwd  = "inlife@2012";            					// Contraseña de acceso a la base de datos
$theme = "default";                 					// Tema (skin) de la aplicación
$instdir = "myinlife";              					// Directorio de instalación de la herramienta (raiz desde htdocs)
//opciones de correo electrónico
$email_from ="notificaciones@inlifestudio.com";				// Correo para envio de notificaciones
$email_name ="InLife Studio";						// Nombre asociado al correo de notificaciones
$transport_method = "MAIL";						// Tipo de transporte para envío de mensajes
$smtp_server = "smtp.gmail.com";		                        // Servidor SMTP para envio de correo por este método
$smtp_port = 465;							// Puerto SMTP
$smtp_encrypt = "ssl";							// Tipo de encripción SMTP
$email_pwd = "inlife2012";						// Contraseña SMTP
$sendmail_path = "/usr/sbin/sendmail -bs";				// Path de SendMail del servidor
$msg_min_limit = 100;							// limite de mensajes por minuto enviados por el servidor