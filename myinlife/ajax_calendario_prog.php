<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/misc_utl.php");

if (isset($_POST['p_mes'])) {
	$v_mes = $_POST['p_mes'];
	$v_ano = $_POST['p_ano'];
	$v_text_mes = get_mes((int)$v_mes);
} else {
	$v_hoy = new DateTime();
	$v_mes = $v_hoy->format('m');
	$v_ano = $v_hoy->format('Y');
	$v_text_mes = get_mes((int)$v_mes);
}
$v_fecha_ini = DateTime::createFromFormat('d-m-Y', '01-'.$v_mes.'-'.$v_ano);
$v_dia = $v_fecha_ini->format('N');
//calcular el comienzo del calendario primer día domingo
$v_interval = new DateInterval('P1D');
if ($v_dia != 7) {
	$v_fecha_ini->sub(new DateInterval('P'.$v_dia.'D'));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td><a href="javascript:previous();">&lt;</a> <a href="javascript:next();">&gt;</a></td>
    <td align="center"><?php echo(strtoupper($v_text_mes).' '.$v_ano);?></td>
  </tr>
</table>
<table width="100%" border="1" cellspacing="0" cellpadding="2">
  <tr>
    <td align="center" valign="middle">Domingo</td>
    <td align="center" valign="middle">Lunes</td>
    <td align="center" valign="middle">Martes</td>
    <td align="center" valign="middle">Mi&eacute;rcoles</td>
    <td align="center" valign="middle">Jueves</td>
    <td align="center" valign="middle">Viernes</td>
    <td align="center" valign="middle">S&aacute;bado</td>
  </tr>
<?php
$v_col = 0;
while ($v_fecha_ini->format('m') <= $v_mes) {
	if ($v_col == 0) {
?>
  <tr>
<?php
	}
	if ($v_fecha_ini->format('m') != $v_mes) {
?>
    <td align="center" valign="middle"><?php echo($v_fecha_ini->format('d'));?></td>
<?php
	} else {
?>
    <td align="center" valign="middle"><a href="javascript:go('<?php echo($v_fecha_ini->format('d-m-Y'));?>');"><?php echo($v_fecha_ini->format('d'));?></a></td>
<?php
	}
    if ($v_col == 6) {
		$v_col = 0;
?>
  </tr>
<?php
	} else {
		$v_col++;
	}
	$v_fecha_ini->add($v_interval);
}
if ($v_col < 6) {
	while ($v_col < 7) {
?>
    <td align="center" valign="middle"><?php echo($v_fecha_ini->format('d'));?></td>
<?php
		$v_fecha_ini->add($v_interval);
		$v_col++;
	}
?>
  </tr>
<?php
}
?>
</table>
</body>
</html>