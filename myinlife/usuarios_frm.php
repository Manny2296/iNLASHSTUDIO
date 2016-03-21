<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/mensaje_utl.php");
include ($path."/lib/usuarios_utl.php");
include ($path."/lib/sedes_utl.php");
$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);
if (isset($_SESSION['id_perfil'])) {
	if ( validar_permisos ($conn, 'usuarios_frm.php') ) {
		$v_id_perf_unico = null;
		if (isset($_REQUEST['p_id_perf_unico'])) {
			$v_id_perf_unico = $_REQUEST['p_id_perf_unico'];
			$r_usuario = detalle_usuario ($conn, $v_id_perf_unico);
			$v_id_perfil = $r_usuario['id_perfil'];
			$v_genero = $r_usuario['genero'];
			$fecha = DateTime::createFromFormat('Y-m-d', $r_usuario['fecha_nacimiento']);
			$v_fecha_nacimiento = $fecha->format('d-m-Y');
			$fecha = DateTime::createFromFormat('Y-m-d', $r_usuario['fecha_ingreso']);
			$v_fecha_ingreso = $fecha->format('d-m-Y');
			$v_existe = 'P';
		} else {
			$t_tipo_perfil = lista_perfil ($conn);
			$v_genero = 'F';
			$v_existe = 'N';
		}
		$t_tipo_id = lista_tipo_id ($conn);
		$t_sedes_reg = lista_sedes ($conn,'S');
		$v_next_ilid = next_ilid($conn);
?>
<html><!-- InstanceBegin template="/Templates/nomenu_layout.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Creaci&oacute;n / Modificaci&oacute;n de usuarios del sistema</title>
<!-- InstanceEndEditable -->
<link href="skins/<?php echo($skin); ?>/estilo.css"rel="stylesheet" type="text/css" />
<link href="skins/<?php echo($skin); ?>/reset.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript" src="lib/autosuggest/js/bsn.AutoSuggest_c_2.0.js"></script>
<script type="text/javascript" language="javascript" src="lib/zxml.js"></script>
<script type="text/javascript" language="javascript" src="lib/popcalendar.js"></script>
<script type="text/javascript" language="javascript">
   function validar_usr() {
	   myForm = document.forma;
	   <?php if (is_null($v_id_perf_unico)) { ?>
	   var p_perfil = myForm.p_id_perfil[myForm.p_id_perfil.selectedIndex].value;
	   <?php } else { ?>
	   var p_perfil = <?php echo ($v_id_perfil); ?>;
	   <?php } ?>
	   var p_id_tipoid = myForm.p_id_tipoid.options[myForm.p_id_tipoid.selectedIndex].value;
	   var p_numero_id = myForm.p_numero_id.value;
	   var p_id_sedes_reg = myForm.p_id_sedes_reg.options[myForm.p_id_sedes_reg.selectedIndex].value;
	   var p_multi_sede = myForm.p_multi_sede.options[myForm.p_multi_sede.selectedIndex].value;
	   var rUrl = "ajax_verificar_usuario.php";
	   var rBody = "p_id_perfil="+p_perfil+"&p_id_tipoid="+p_id_tipoid+"&p_numero_id="+p_numero_id+"&p_id_sedes_reg="+p_id_sedes_reg+"&p_multi_sede="+p_multi_sede;
	   oDiv = document.getElementById ("fakefrmdiv");
	   oDiv.innerHTML = "<img src=\"skins/<?php echo($skin); ?>/loader.gif\"><b>Consultando...</b>";
	   var oXmlHttp = zXmlHttp.createRequest();
	   oXmlHttp.open("post", rUrl, true);
	   oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	   oXmlHttp.onreadystatechange = function () {
		  if (oXmlHttp.readyState == 4) {
			 if (oXmlHttp.status == 200) {
				oDiv.innerHTML = oXmlHttp.responseText;
				//validaciones del usuario
				myFake = document.frmfake;
				if ( myFake.p_existe.value == "P" ) {
				   alert ("El usuario que intenta crear ya existe con este perfil en el cliente");
				   myForm.p_id_tipoid.selectedIndex = 0;
				   myForm.p_numero_id.value = "";
				}
				else if ( myFake.p_existe.value == "U" ) {
				   nombre = myFake.p_nombres.value +" "+ myFake.p_apellidos.value;
				   docid = myForm.p_numero_id.value;
				   
				   if (confirm ("El usuario "+nombre+" identificado con documento de identidad No. "+docid+" ya existe.\n\nDesea agregarle el este perfil ?") ){
					  myForm.p_id_usuario.value = myFake.p_id_usuario.value;
					  myForm.p_existe.value = "U";
					  myForm.p_id_sedes_reg.value=myFake.p_id_sedes_reg.value;
					  myForm.p_multi_sede.value=myFake.p_multi_sede.value;
					  myForm.submit();
				   }
				}
				else {
				   myForm.p_nombres.focus();
				   myForm.p_existe.value = "N";
				}
			 } else {
				oDiv.innerHTML ="<img src=\"skins/<?php echo($skin); ?>/opt_eliminar.png\"><b>AJAX: Problema al traer datos</b>";
			 }
		  }
	   };
	   oXmlHttp.send(rBody); 
	   return;
	}
	function validar() {
		myForm = document.forma;
		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (myForm.p_numero_id.value == "") {
			alert("Por favor ingrese el número de documento de identidad");
			myForm.p_numero_id.focus();
			return;
		}
		if (myForm.p_nombres.value == "") {
			alert("Por favor ingrese el nombre del usuario");
			myForm.p_nombres.focus();
			return;
		}
		if (myForm.p_apellidos.value == "") {
			alert("Por favor ingrese el apellido del usuario");
			myForm.p_apellidos.focus();
			return;
		}
		if (myForm.p_email.value == "" || !filter.test(myForm.p_email.value)) {
			alert("Por favor ingrese un email válido para el usuario");
			myForm.p_email.focus();
			return;
		}
		if (myForm.p_telefono.value == "") {
			alert("Por favor ingrese un teléfono fijo para el usuario");
			myForm.p_telefono.focus();
			return;
		}
		if (myForm.p_fecha_nacimiento.value == "") {
			alert("Por favor ingrese la fecha de nacimiento del usuario");
			myForm.p_fecha_nacimiento.focus();
			return;
		}
		if (myForm.p_fecha_ingreso.value == "") {
			alert("Por favor ingrese la fecha de inscripción del usuario a InLife Studio");
			myForm.p_fecha_ingreso.focus();
			return;
		}
		myForm.submit();
	}
	function next_id() {
		myForm = document.forma;
		var tipoid = myForm.p_id_tipoid.options[myForm.p_id_tipoid.selectedIndex].value;
		if (tipoid == "0"){
			myForm.p_numero_id.value = "<?php echo($v_next_ilid); ?>";
		}
		return;
	}
</script>
<!-- InstanceEndEditable -->
</head>

<body>
  <div id="contenido">
  <!-- InstanceBeginEditable name="contenido" -->
  <div class="titulo">DEFINICI&Oacute;N DE USUARIOS</div>
     <div class="capa_form_sf">
        <form id="forma" name="forma" method="post" action="exec_upd_usuario.php">
        <?php if (!is_null($v_id_perf_unico)) { ?>
        <input type="hidden" name="p_id_perfil" id="p_id_perfil" value="<?php echo($r_usuario['id_perfil']); ?>" />
        <?php } ?>
        <input type="hidden" name="p_existe" id="p_existe" value="<?php echo($v_existe); ?>" />
        <input type="hidden" name="p_id_usuario" id="p_id_usuario" value="<?php if (!is_null($v_id_perf_unico)) { echo($r_usuario['id_usuario']); } ?>" />
        <table width="80%" border="0" cellpadding="0" cellspacing="0">
          <tr>
			<th>Perfil del usuario:</th>
            <?php if (is_null($v_id_perf_unico)) { ?>
            <td><select name="p_id_perfil" id="p_id_perfil">
            <?php foreach($t_tipo_perfil as $dato) { ?>
            <option value="<?php echo($dato['id_perfil']); ?>"><?php echo($dato['nombre']); ?></option>
            <?php } ?>
            </select></td>
            <?php } else { ?>
            <td><?php echo($r_usuario['nomperfil']); ?></td>
            <?php } ?>
          </tr>
          <tr>
          <tr>
			<th>Sede:</th>
            <td><select name="p_id_sedes_reg" id="p_id_sedes_reg" onChange="next_id();">
            <?php foreach($t_sedes_reg as $dato) { ?>
            <option value="<?php echo($dato['id_sede']); ?>" <?php if (!is_null($v_id_perf_unico) && $r_usuario['id_sede']== $dato['id_sede']) { echo("Selected"); } ?>><?php echo($dato['nombre']); ?></option>
            <?php } ?>
            </select></td>
          <tr>
          	<th>Permitir varias sedes?:</th>
          	<td>
          		<select name="p_multi_sede" id="p_multi_sede">

          		<option value="S" <?php if (!is_null($v_id_perf_unico) && $r_usuario['multisede']== 'S') { echo("Selected"); } ?>
          		>Si</option>
          		<option value="N" <?php if (!is_null($v_id_perf_unico) && $r_usuario['multisede']== 'N') { echo("Selected"); } ?>>No</option>
          		</select>
          	</td>
          </tr>  
          </tr>
			<th>Tipo de documento de identidad:</th>
            <td><select name="p_id_tipoid" id="p_id_tipoid" onChange="document.forma.p_numero_id.value='';next_id();">
            <option value=""></option>
            <?php foreach($t_tipo_id as $dato) { ?>
            <option value="<?php echo($dato['id_tipoid']); ?>" <?php if (!is_null($v_id_perf_unico) && $r_usuario['id_tipoid']== $dato['id_tipoid']) { echo("Selected"); } ?>><?php echo($dato['nombre']); ?></option>
            <?php } ?>
            </select></td>
          </tr>
          <tr>
			<th>N&uacute;mero de documento:</th>
            <td><input type="text" name="p_numero_id" id="p_nombre" size="30" maxlength="45" value="<?php if(!is_null($v_id_perf_unico)){echo($r_usuario['numero_id']);} ?>" onChange="setTimeout('validar_usr();', 0);"/></td>
          </tr>
          <tr>
			<th>Nombres:</th>
            <td><input type="text" name="p_nombres" id="p_nombres" size="50" maxlength="100" value="<?php if(!is_null($v_id_perf_unico)){echo($r_usuario['nombres']);} ?>" /></td>
          </tr>
          <tr>
			<th>Apellidos:</th>
            <td><input type="text" name="p_apellidos" id="p_apellidos" size="50" maxlength="100" value="<?php if(!is_null($v_id_perf_unico)){echo($r_usuario['apellidos']);} ?>" /></td>
          </tr>
          <tr>
			<th>Tel&eacute;fono fijo:</th>
            <td><input type="text" name="p_telefono" id="p_telefono" size="30" maxlength="30" value="<?php if(!is_null($v_id_perf_unico)){echo($r_usuario['telefono']);} ?>" /></td>
          </tr>
          <tr>
			<th>M&oacute;vil:</th>
            <td><input type="text" name="p_celular" id="p_celular" size="30" maxlength="30" value="<?php if(!is_null($v_id_perf_unico)){echo($r_usuario['celular']);} ?>" /></td>
          </tr>
          <tr>
			<th>Email:</th>
            <td><input type="text" name="p_email" id="p_email" size="50" maxlength="200" value="<?php if(!is_null($v_id_perf_unico)){echo($r_usuario['email']);} ?>" /></td>
          </tr>
          <tr>
			<th>Genero:</th>
            <td><?php lista_genero($v_genero); ?></td>
          </tr>
          <tr>
			<th>Fecha de nacimiento:</th>
            <td><input type="text" name="p_fecha_nacimiento" id="p_fecha_nacimiento" maxlength="12" size="12" value="<?php if(!is_null($v_id_perf_unico)) {echo($v_fecha_nacimiento);}?>" onClick="popUpCalendar(this, forma.p_fecha_nacimiento);" readonly/></td>
          </tr>
          <tr>
			<th>Eps:</th>
            <td><input type="text" name="p_eps" id="p_eps" size="50" maxlength="200" value="<?php if(!is_null($v_id_perf_unico)){ echo($r_eps['nombre']);}?>" />
            <input type="hidden" name="p_id_eps" id="p_id_eps" value="<?php if(!is_null($v_id_perf_unico)){echo($r_eps['id_eps']);}?>"/></td>
          </tr>
          <tr>
			<th>Prepagada:</th>
            <td><input type="text" name="p_prepagada" id="p_prepagada" size="50" maxlength="200" value="<?php if(!is_null($v_id_perf_unico)){echo($r_prepagada['nombre']);}?>" />
            <input type="hidden" name="p_id_prepagada" id="p_id_prepagada" value="<?php if(!is_null($v_id_perf_unico)){ echo($r_prepagada['id_prepagada']);}?>"/></td>
          </tr>
          <tr>
			<th>Fecha de inscripci&oacute;n:</th>
            <td><input type="text" name="p_fecha_ingreso" id="p_fecha_ingreso" maxlength="12" size="12" value="<?php if(!is_null($v_id_perf_unico)) {echo($v_fecha_ingreso);}?>" onClick="popUpCalendar(this, forma.p_fecha_ingreso);" readonly/></td>
          </tr>
          <tr>
			<th>Anotaciones personales:</th>
            <td><textarea name="p_descripcion" id="p_descripcion" rows="4" cols="40"><?php if(!is_null($v_id_perf_unico)){echo($r_usuario['descripcion']);} ?></textarea></td>
          </tr>
          
          
          <tr>
              <td colspan="2" align="center"><input type="button" name="btn_enviar" id="btn_enviar" class="button white" value="Guardar" onClick="validar();" />
              &nbsp; <input type="button" name="btn_regresar" id="btn_regresar" class="button white" value="Regresar" onClick="javascript:top.GB_hide();" /> </td>
          </tr>
        </table>
        </form>
     </div>
     <div id="fakefrmdiv"></div>
     <script type="text/javascript">
		//implementacion de autosuggest
		var options_eps = {
			script:"ajax_buscar_eps.php?",
			varname:"p_letras",
			json:true,
			callback: function (obj) { document.getElementById('p_id_eps').value = obj.id; }
		};
		var as_json_eps = new AutoSuggest('p_eps', options_eps);
		
		var options_prepa = {
			script:"ajax_buscar_prepagada.php?",
			varname:"p_letras",
			json:true,
			callback: function (obj) { document.getElementById('p_id_prepagada').value = obj.id; }
		};
		var as_json_prepa = new AutoSuggest('p_prepagada', options_prepa);
	</script>
  <!-- InstanceEndEditable -->
  </div>
</body>
<!-- InstanceEnd --></html>
<?php	
	}
	else {
		mensaje(2, 'Usted no tiene permisos para acceder esta opci&oacute;n', 'javascript:history.go(-1);', '_self');
	}      
} else {
	mensaje(2, 'Su sesi&oacute;n no est&aacute; activa.<br>Por favor ingrese al sistema nuevamente', $url_login, '_parent');
}
dbdisconn ($conn);
?>