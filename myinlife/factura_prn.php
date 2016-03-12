<?php
session_start();
$path =  getenv("DOCUMENT_ROOT")."/myinlife";
include ($path."/lib/inlife_inc.php");
include ($path."/lib/".$db_engine_lib);
include ($path."/lib/securityutl_lib.php");
include ($path."/lib/layoututl_lib.php");
include ($path."/lib/facturacion_utl.php");
require ($path."/lib/fpdf.php");

$conn  = dbconn ($db_host, $db_name, $db_user, $db_pwd);
$skin  = obtener_skin ($conn);

$v_id_factura = $_REQUEST['p_id_factura'];
$r_factura = datos_factura($conn, $v_id_factura);
$t_detalle = detalle_factura($conn, $v_id_factura);
$t_impuestos = impuestos_factura($conn, $v_id_factura);
dbdisconn($conn);

$font_name   = "courier";
$rfont_file  = "courier.php";
$bfont_file  = "courierb.php";
$ifont_file  = "courieri.php";
$font_size      = "8";
$font_color     = "0";
//Clase PDF
class EPDF extends FPDF {
	function Header(){
		global $font_name;
		global $font_size;
		$this -> SetFont ($font_name, '', $font_size);
		$this -> Cell(0,5,'Inlife Studio S.A.S',0,1,'L');
		$this -> Cell(0,5,'Nit. 900805347-0',0,1,'L');
		$this -> Cell(0,5,'Dir. Cra 17A#122-45',0,1,'L');
		$this -> Cell(0,5,'Tel. 3108001209',0,1,'L');
		$this -> Cell(0,5,'----------------------------------------',0,1,'L');
		$this -> Cell(0,5,'RES 320001223737',0,1,'L');
		$this -> Cell(0,5,'----------------------------------------',0,1,'L');
	}
	function DatosFactura(){
		global $font_name;
		global $font_size;
		global $r_factura;
		
		$this -> SetFont ($font_name, '', $font_size);
		$this -> Cell(0,5,'Convenio de Prestación de Servicios',0,1,'C');
		$this -> Cell(0,5,'Profesionales',0,1,'C');
		$this -> Cell(33,5,'Factura No. ',0);
		$this -> Cell(0,5,$r_factura['num_factura'],0,1);
		$this -> Cell(33,5,'Fecha:',0);
		$this -> Cell(0,5,$r_factura['fecha'],0,1);
		$this -> Cell(33,5,'Hora:',0);
		$this -> Cell(0,5,$r_factura['hora'],0,1);
		$this -> Cell(0,5,'----------------------------------------',0,1,'L');
		$this -> Cell(15,5,'Cliente:',0);
		$this -> Cell(0,5,substr($r_factura['nomcliente'],0,30),0,1);
	}
	function Cuerpo(){
    	global $font_name;
        global $font_size;
		global $r_factura;
		global $t_detalle;
		global $t_impuestos;

		$v_subtotal = 0;
		$v_total = 0;
		$v_descuento = 0; 
		
		$this -> SetFont ($font_name, '', $font_size);
		$this -> Cell(0,5,'----------------------------------------',0,1,'L');
		$this -> Cell(32,5,'Artículo',0,0,'C');
		$this -> Cell(8,5,'Cant',0,0,'C');
		$this -> Cell(0,5,'Valor',0,1,'C');
		$this -> Cell(0,5,'----------------------------------------',0,1,'L');
		if(is_array($t_detalle)) { 
			foreach($t_detalle as $dato){  
				$v_subtotal += $dato['total'] + $dato['valor_descuento'] - $dato['iva'];
				$v_descuento += $dato['valor_descuento'];
				if(!is_null($dato['nomservicio'])){ 
          			$this -> Cell(32,5,substr($dato['nomservicio'],0,15));
				} else { 
					$this -> Cell(32,5,substr($dato['nomproducto'],0,15));
				}
				$this -> Cell(8,5,$dato['cantidad'],0,0,'C');
				$this -> Cell(0,5,'$ '.number_format(($dato['cantidad']*$dato['valor_unitario']), 2, '.', ','),0, 1, 'R');
			}
		}
		$this -> Cell(0,5,'----------------------------------------',0,1,'L');
		//totales
		$this -> Cell(32,5,'Subtotal ',0);
		$this -> Cell(0,5,'$ '.number_format($v_subtotal, 2, ".", ","),0,1,'R');
		$this -> Cell(32,5,'Descuento ',0);
		$this -> Cell(0,5,'$ '.number_format($v_descuento, 2, ".", ","),0,1,'R');
		foreach($t_impuestos as $dato){
			$this -> Cell(32,5,'Iva ('.$dato['impuesto'].'%)',0);
			$this -> Cell(0,5,'$ '.number_format($dato['valor'], 2, ".", ","),0,1,'R');
			$v_total += $dato['valor'];
		}
		$this -> Cell(32,5,'Total ',0);
		$this -> Cell(0,5,'$ '.number_format(($v_subtotal + $v_total - $v_descuento), 2, ".", ","),0,1,'R');
		$this -> Cell(0,5,'----------------------------------------',0,1,'L');
		$this -> Cell(15,5,'Cajero:',0);
		$this -> Cell(0,5,substr(strtoupper($r_factura['cajero']),0,30),0,1);
		$this -> Cell(0,5,'----------------------------------------',0,1,'L');
		$this -> Cell(0,5,'GRACIAS POR UTILIZAR NUESTROS SERVICIOS',0,1,'C');
	}
}	
// Definir PDF
$pdf = new EPDF('P','mm','pos');
$pdf -> SetMargins(5,5);
$pdf -> AddFont($font_name, '', $rfont_file);
$pdf -> AddFont($font_name, 'B', $bfont_file);
$pdf -> AddFont($font_name, 'I', $ifont_file);
$pdf -> SetTextColor($font_color);
$pdf -> AddPage();
$pdf -> SetFont($font_name, '', $font_size);
$pdf -> DatosFactura();
$pdf -> Cuerpo();
$filename = "factura_".$v_id_factura.".pdf";
$pdf -> Output($filename, 'I');	
?>