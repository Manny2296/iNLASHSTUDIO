<?php 
session_start();
session_unset();
session_destroy();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Secure Logout</title>
<script language="javascript" type="text/javascript">
function redirect() {
	location.replace ("<?php echo($site_domain); ?>");
}
</script>
</head>

<body onload="redirect();">
</body>
</html>