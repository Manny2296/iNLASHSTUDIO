<?php 
   function mensaje($tipo, $texto, $returl, $frame) {
      global $instdir, $skin, $conn;
	  $uri_skin = $instdir."/skins/".$skin;
?>
<div class="capa_mensaje" align="center">
    <table border=0 align="center">
      <tr align="center">
         <th colspan="2"><h1>MENSAJE DE LA APLICACI&Oacute;N</h1></th>
      </tr>
      <tr valign="middle">
         <td><img src="/<?php echo($uri_skin);?>/<?php if ($tipo == 1){echo("ok.png");} else{echo("mal.png");} ?>"></td>
         <td><?php echo($texto); ?></td>
      </tr>
      <tr>
         <td colspan="2"><div align="center"><a href="<?php echo($returl); ?>" target="<?php echo($frame); ?>">Regresar</a></div></td>
      </tr>
      <tr align="center">
         <th colspan="2">&nbsp;</th>
      </tr>
      </table>
</div>
   <?php
   }
   function mensaje_form($tipo, $texto, $accion, $campo, $codigo) {
      global $instdir, $skin, $conn;
	  $uri_skin = $instdir."/skins/".$skin;
   ?>
<div class="capa_mensaje" align="center">
    <table border=0 align="center">
      <tr align="center">
         <th colspan="2"><h1>MENSAJE DE LA APLICACI&Oacute;N</h1></th>
      </tr>
      <tr valign="middle">
         <td><img src="/<?php echo($uri_skin);?>/<?php if ($tipo == 1){echo("ok.png");} else{echo("mal.png");} ?>"></td>
         <td><?php echo($texto); ?></td>
      </tr>
      <tr>
         <td colspan="2"><div align="center"><a href="javascript:document.forma.submit();">Regresar</a></div></td>
      </tr>
      <tr align="center">
         <th colspan="2">&nbsp;</th>
      </tr>
      </table>
</div>

<form action="<?php echo($accion); ?>" method="post" name="forma">
<?php
if (is_array($campo)) {
   for ($x=0; $x < count($campo); $x++) {
?>
<input type="hidden" name="<?php echo ($campo[$x]); ?>" value= "<?php echo($codigo[$x]); ?>">
<?php 
   }
} else {
?>
<input type="hidden" name="<?php echo ($campo); ?>" value= "<?php echo($codigo); ?>">
<?php } ?>
</form>
   <?php
   }
?>
