<?php
/* 
 Libreria de utilidades para la obtención de datos de parametrizción del sistema
 Desarrollo: Ing. Carlos Augusto Abarca
             email: cabarca01@gmail.com
  Desarrollo: Dev Manuel Felipe S.R 
 			manuel.sanchez-r@mail.escuelaing.edu.co
 Fecha     : 04/11/2010 04:30 p.m.
 Version   : 1.0
*/
function obtener_parametros($connid){
	$query = "Select para.*
	            From conf_parametros para
			   Order By para.id_parametro";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	$v_cont = 0;
	foreach ($rset as $dato){
		$data_rec = null;
		$data_rec['id_parametro'] = $dato['id_parametro'];
		$data_rec['codigo'] = $dato['codigo'];
		$data_rec['descripcion'] = $dato['descripcion'];
		$data_rec['valor'] = $dato['valor'];
		$data_rec['tipo'] = $dato['id_tpparametro'];
		
		$res[$v_cont] = $data_rec;
		$v_cont++;
	}
	return ($res);
}
function obtener_valor_param ($connid, $codigo) {
	$query = "Select para.valor
	            From conf_parametros para
			   Where para.codigo = '".$codigo."'";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	return ($rset[0]['valor']);
}
function lista_items_params ($connid, $id_parametro, $valor) {
	$query = "Select * From conf_parametros_lista lipa
	           Where lipa.id_parametro = ".$id_parametro."
			   Order By lipa.texto";
	$result = dbquery ($query, $connid);
    $rset = dbresult($result);
	?>
	<select id="para_<?php echo ($id_parametro); ?>" name="para_<?php echo ($id_parametro); ?>">
    <option value=""></option>
    <?php 
	foreach ($rset as $dato) {
	?>
    <option value="<?php echo ($dato['valor']); ?>" <?php if($valor == $dato['valor']) {echo("Selected");}?>><?php echo ($dato['texto']); ?></option>
    <?php 
	}?>
    </select>
    <?php
	return (null);
}
function lista_sn ($id_parametro, $valor) {
	?>
    <select id="para_<?php echo ($id_parametro); ?>" name="para_<?php echo ($id_parametro); ?>">
    <option value=""></option>
    <option value="S" <?php if($valor == "S") {echo("Selected");}?>>Si</option>
    <option value="N" <?php if($valor == "N") {echo("Selected");}?>>No</option>
    </select>
    <?php
	return (null);
}
?>