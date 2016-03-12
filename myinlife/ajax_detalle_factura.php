<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/facturacion_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);
if (isset($_SESSION['id_perfil'])) {
	$v_id_factura = $_POST['p_id_factura'];
	$v_tipo = $_POST['p_tipo'];
	$t_detalle = detalle_factura($conn, $v_id_factura);
	$v_subtotal = 0;
	$v_total = 0;
	$v_descuento = 0; 
	$t_impuestos = impuestos_factura($conn, $v_id_factura);
	
}
dbdisconn($conn);
?>
          <table width="80%" border="0" cellpadding="0" cellspacing="0">
            <?php if ($v_tipo == "editar"){ ?>
            <tr>
              <td colspan="6" align="right"><input type="button" name="btn_nuevo" id="btn_nuevo" class="button white" value="Agregar Item" onclick="addItem();" /></td>
            </tr>
            <?php } ?>
            
            <tr>
              <?php if ($v_tipo == "editar"){ ?><th width="5%" nowrap="nowrap">&nbsp;</th><?php } ?>
              <th width="40%"><div align="center">Art&iacute;culo</div></th>
              <th><div align="center">Cantidad</div></th>
              <th><div align="center">V. Unitario</div></th>
              <th><div align="center">% Descuento</div></th>
              <th><div align="center">Valor</div></th>
            </tr>
            <?php 
			if(is_array($t_detalle)) { 
				foreach($t_detalle as $dato){  
			?>
            <tr>
              <?php if($v_tipo == "editar"){?><td><a href="javascript:delItem(<?php echo($dato['id_detalle']); ?>);"><img src="skins/<?php echo($skin); ?>/opt_eliminar.png" alt="Eliminar detalle de la factura" title="Eliminar detalle de la factura" border="0" /></a></td><?php } ?>
              <td nowrap="nowrap"><?php if(!is_null($dato['nomservicio'])){ echo(utf8_encode($dato['nomservicio'])); } else { echo(utf8_encode($dato['nomproducto'])); } ?></td>
              <td align="center" nowrap="nowrap"><?php echo($dato['cantidad']); ?></td>
              <td align="right" nowrap="nowrap"><?php echo("$ ".number_format($dato['valor_unitario'], 2, ".", ",")); ?></td>
              <td align="center" nowrap="nowrap"><?php echo(number_format($dato['descuento'], 2, ".", ","). "%"); ?></td>
              <td align="right" nowrap="nowrap"><?php echo("$ ".number_format($dato['total'], 2, ".", ",")); ?></td>
            </tr>
            <?php
					$v_subtotal += $dato['total'] + $dato['valor_descuento'] - $dato['iva'];
					$v_descuento += $dato['valor_descuento']; 
				}
				if ($v_tipo == "editar"){ ?>
            <tr>
              <td nowrap="nowrap"><input type="radio" id="p_tipo" name="p_tipo" value="servicio" onclick="getListaItem(this);"/>Servicio<br />
                  <input type="radio" id="p_tipo" name="p_tipo" value="producto" onclick="getListaItem(this);"/>Producto</td>
              <td><div id="productosdiv"></div></td>
              <td align="center" nowrap="nowrap"><input type="text" name="p_cantidad" id="p_cantidad" value="1" size="3" maxlength="4" /></td>
              <td align="right" nowrap="nowrap">$ <input type="text" name="p_valor_unitario" id="p_valor_unitario" size="7" maxlength="7" />
              <td align="center" nowrap="nowrap"><input type="text" id="p_pordto" name="p_pordto" value="0" size="3" maxlength="5" />%</td>
            </tr>
            <?php }
			} else {?>
            <?php if ($v_tipo == "editar"){ ?>
            <tr>
              <td nowrap="nowrap"><input type="radio" id="p_tipo" name="p_tipo" value="servicio" onclick="getListaItem(this);"/>Servicio<br />
                  <input type="radio" id="p_tipo" name="p_tipo" value="producto" onclick="getListaItem(this);"/>Producto</td>
              <td><div id="productosdiv"></div></td>
              <td align="center" nowrap="nowrap"><input type="text" name="p_cantidad" id="p_cantidad" value="1" size="3" maxlength="4" /></td>
              <td align="right" nowrap="nowrap">$ <input type="text" name="p_valor_unitario" id="p_valor_unitario" size="7" maxlength="7" />
              <td align="center" nowrap="nowrap"><input type="text" id="p_pordto" name="p_pordto" value="0" size="3" maxlength="5" />%</td>
            </tr>
            <?php } else { ?>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <?php } } ?>
            <tr>
              <td colspan="<?php if($v_tipo == "editar") { echo("5"); } else {echo("4"); } ?>">Subtotal</td>
              <td align="right" nowrap="nowrap">$ <?php echo(number_format($v_subtotal, 2, ".", ",")); ?></td>
            </tr>
            <tr>
              <td colspan="<?php if($v_tipo == "editar") { echo("5"); } else {echo("4"); } ?>">Descuento</td>
              <td align="right" nowrap="nowrap">$ <?php echo(number_format($v_descuento, 2, ".", ",")); ?></td>
            </tr>
            <?php foreach($t_impuestos as $dato){ ?>
            <tr>
              <td colspan="<?php if($v_tipo == "editar") { echo("5"); } else {echo("4"); } ?>">Iva (<?php echo($dato['impuesto']); ?>%)</td>
              <td align="right" nowrap="nowrap">$ <?php echo(number_format($dato['valor'], 2, '.', ',')); ?></td>
            </tr>
            <?php 	$v_total += $dato['valor']; 
				  } ?>
            <tr>
              <td colspan="<?php if($v_tipo == "editar") { echo("5"); } else {echo("4"); } ?>">Total</td>
              <td align="right" nowrap="nowrap">$ <?php echo(number_format(($v_subtotal + $v_total - $v_descuento), 2, '.', ',')); ?></td>
            </tr>
            <?php if ($v_tipo == "editar"){ ?>
            <tr>
              <td colspan="6" align="right"><input type="button" name="btn_nuevo" id="btn_nuevo" class="button white" value="Agregar Item" onclick="addItem();" /></td>
            </tr>
            <?php } ?>
          </table>
