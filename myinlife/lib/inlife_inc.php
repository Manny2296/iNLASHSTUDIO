<?php
/*
-- Archivo de configuraci�n
-- Inlife Studio
--
*/
date_default_timezone_set ('America/Bogota');
$default_pais = 169;							// pais por defecto
$default_ciudad = 11;							// ciudad por defecto
$site_domain = "http://www.inlifestudio.com"; 				// dominio de la aplicacion.
$url_login = "http://www.inlifestudio.com/login.php";                   // url de acceso al sistemas.
$db_engine_lib = "mySQL_lib.php";                                       // Motor de base de datos
$db_name = "inlifes1_myinlife";  					// Base de datos de la apliaci�n.
$db_host = "localhost";     					        // Host de la base de datos
$db_user = "inlifes1";     					        // usuario de la base de datos - Privilegios DML solamente 
$db_pwd  = "inlife@2012";            					// Contrase�a de acceso a la base de datos
$theme = "default";                 					// Tema (skin) de la aplicaci�n
$instdir = "myinlife";              					// Directorio de instalaci�n de la herramienta (raiz desde htdocs)
//opciones de correo electr�nico
$email_from ="notificaciones@inlifestudio.com";				// Correo para envio de notificaciones
$email_name ="InLife Studio";						// Nombre asociado al correo de notificaciones
$transport_method = "MAIL";						// Tipo de transporte para env�o de mensajes
$smtp_server = "smtp.gmail.com";		                        // Servidor SMTP para envio de correo por este m�todo
$smtp_port = 465;							// Puerto SMTP
$smtp_encrypt = "ssl";							// Tipo de encripci�n SMTP
$email_pwd = "inlife2012";						// Contrase�a SMTP
$sendmail_path = "/usr/sbin/sendmail -bs";				// Path de SendMail del servidor
$msg_min_limit = 100;							// limite de mensajes por minuto enviados por el servidor