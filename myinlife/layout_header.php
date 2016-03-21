<?php
if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'layout_header.php') ) {
		$v_nombre = $_SESSION['nombre'];
		$v_perfiles = cantidad_perfil ($conn, $_SESSION['login']);
?>
    <div id="header">
    	<div id="logo"><div align="left"><img src="skins/<?php echo($skin); ?>/header.png" align="absmiddle" alt="MyInlife" title="MyInlife" /></div>
        </div>
        <div id="centro_header"><span class="texto_peq">Bienvenido(a)  <span class="negrita"><?php echo($v_nombre); ?></span></span>
        </div>
        <div id="salir"><?php if ($v_perfiles > 1) {?>
        <a href="securelogin.php"><img src="skins/<?php echo($skin); ?>/icon_perfil.png" alt="Seleccionar Perfil" title="Seleccionar Perfil" /></a>&nbsp;&nbsp;
        <?php } ?>
        <a href="logout.php"><img src="skins/<?php echo($skin); ?>/salir_icono.png" alt="Cerrar Sesi&oacute;n" title="Cerrar Sesi&oacute;n" /></a>
        </div>
    </div>
<?php
		
	}
	else {
		include ($path."/lib/mensaje_utl.php");
		mensaje(2, 'Usted no tiene permisos para acceder esta opci&oacute;n', 'javascript:history.go(-1);', '_self');
	}      
} else {
	include ($path."/lib/mensaje_utl.php");
	mensaje(2, 'Su sesi&oacute;n no est&aacute; activa.<br>Por favor ingrese al sistema nuevamente', $url_login, '_parent');
}
?>
