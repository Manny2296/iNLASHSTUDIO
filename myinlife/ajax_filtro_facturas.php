<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/facturacion_utl.php");
include ($path."/lib/sedes_utl.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
if ($_SESSION['id_perfil']==1){
      $t_sede = lista_sedes ($conn,'S');
    }else{
     $v_id_sede = $_SESSION['id_sede'];
     $t_sede = detalle_sede ($conn,$v_id_sede);
    }
if(isset($_POST['p_tipo'])) {
	$v_tipo = $_POST['p_tipo'];
} else {
	$v_tipo = "cliente";
}
if(isset($_POST['p_id_sede'])) {
  $v_id_sede = $_POST['p_id_sede'];
} else if(is_array($t_sede)){
  $v_id_sede = $t_sede[0]['id_sede'];

}else{
  $v_id_sede=null;
}
if($v_tipo=="cliente") {
   if(is_array($t_sede))
          {
            
            $t_clientes = lista_clientes($conn,$v_id_sede);
          }else
          {
            $v_id_sede = null;
            $t_clientes = null;
          }
	
} elseif($v_tipo == "producto") {
	$t_productos = lista_productos($conn);
} elseif ($v_tipo == "servicio") {
  if(is_array($t_sede))
          {
            

            $t_servicios = lista_servicios ($conn,$v_id_sede);
          }else
          {
            $v_id_sede = null;
            $t_servicios = null;
          }
}

$v_fecha_ini = get_fecha_factura($conn, "inicial");
$v_fecha_fin = get_fecha_factura($conn, "final");

dbdisconn($conn);
?>
      <form id="forma" name="forma" method="post" action="#">
         <table width="90%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <th>Sede:</th>
            <td>
            <?php if($_SESSION['id_perfil']==1) { ?>
              <select name="p_id_sede" id="p_id_sede" onchange="setTimeout('getParams();',0);">
         <?php if(!is_array($t_sede)){echo ("<option value='No hay Sedes Registradas'>No hay sedes Registradas</option>");}else{echo ("");}?>
          <?php foreach($t_sede as $dato) { ?>
            <option value="<?php echo($dato['id_sede']); ?>" <?php if($dato['id_sede'] == $v_id_sede) { echo("Selected"); } ?>><?php echo($dato['nombre']); ?></option>
          <?php } ?>
          </select>
            <?php } else {?>
            <?php echo($t_sede['nombre']); ?><input type="hidden" name="p_id_sede" id="p_id_sede" value="<?php echo($t_sede['id_sede']); ?>" />
            <?php }?>
            </td>
            <td></td>
            <td></td>
          </tr>
           <tr>
             <th>Tipo de consulta:</th>

             <td><select name="p_tipo" id="p_tipo" onchange="setTimeout('getParams();',0);">
                     <option value="cliente" <?php if($v_tipo=="cliente") {echo("selected");} ?>>Por Cliente</option>
                     <option value="estado" <?php if($v_tipo=="estado") {echo("selected");} ?>>Por Estado</option>
                     <option value="factura" <?php if($v_tipo=="factura") {echo("selected");} ?>>Por N&uacute;mero de Factura</option>
                     <option value="producto" <?php if($v_tipo=="producto") {echo("selected");} ?>>Por Producto</option>
                     <option value="servicio" <?php if($v_tipo=="servicio") {echo("selected");} ?>>Por Servicio</option>
                   </select></td>
           <?php
		   if($v_tipo == "cliente") {
		   ?>
             <th>Cliente:</th>
             <td><select name="p_param" id="p_param">
                   <option value="">Todos</option>
                 <?php
				 if(is_array($t_clientes)){
					 foreach($t_clientes as $dato) {
				?>
                  <option value="<?php echo($dato['id_usuario']); ?>"><?php echo(utf8_encode($dato['nomcliente'])); ?></option>
                <?php
					 }
				 }?>
               </select></td>
           <?php
		   } elseif ($v_tipo == "factura") {
		   ?>
             <th>Factura No.:</th>
             <td><input type="text" name="p_param" id="p_param" size="20" /></td>
           <?php
		   } elseif ($v_tipo == "producto") {
		   ?>
             <th>Producto:</th>
             <td><select name="p_param" id="p_param">
                   <option value="">Todos</option>
                 <?php
				 if(is_array($t_productos)){
					 foreach($t_productos as $dato) {
				?>
                  <option value="<?php echo($dato['id_producto']); ?>"><?php echo(utf8_encode($dato['nombre'])); ?></option>
                <?php
					 }
				 }?>
               </select></td>
           <?php 
		   } elseif ($v_tipo == "servicio") {
		   ?>
             <th>Servicio:</th>
             <td><select name="p_param" id="p_param">
                   <option value="">Todos</option>
                 <?php
				 if(is_array($t_servicios)){
					 foreach($t_servicios as $dato) {
				?>
                  <option value="<?php echo($dato['id_servicio']); ?>"><?php echo(utf8_encode($dato['nombre'])); ?></option>
                <?php
					 }
				 }?>
               </select></td>
           <?php
		   }elseif ($v_tipo == "estado") {
		   ?>
             <th>Estado de la factura:</th>
             <td><select name="p_param" id="p_param">
                   <option value="">Todos</option>
                   <option value="FAC">Facturadas</option>
                   <option value="ANL">Anuladas</option>
               </select></td>
           <?php
		   }
		   ?>
           </tr>
           <?php
		   if ($v_tipo != "factura") {
		   ?>
           <tr>
             <th>Fecha Inicial:</th>
             <td><input type="p_fecha_ini" id="p_fecha_ini" size="12" maxlength="12" value="<?php echo($v_fecha_ini); ?>" onClick="popUpCalendar(this, forma.p_fecha_ini);" readonly/></td>
             <th>Fecha Final:</th>
             <td><input type="p_fecha_fin" id="p_fecha_fin" size="12" maxlength="12" value="<?php echo($v_fecha_fin); ?>" onClick="popUpCalendar(this, forma.p_fecha_fin);" readonly/></td>
           </tr>
           <?php
		   }
		   ?> 
		   <tr>
       <?php if (is_array($t_sede)){ ?>
             <td colspan="4" align="right"><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Consultar" onClick="getResults();" /></td>
       <?php } ?>
           </tr>
         </table>
       </form>