<?php

include_once '../Reports/fpdf/fpdf.php';
include_once '../Clases/clsMail.php';
include_once '../Clases/Conn.php';
include_once './nusoap.php';

class Clase_factura {
    var $con;
    function recupera_datos($clave, $amb) {
        if ($amb == 2) { //Produccion
            $wsdl = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl', 'wsdl');
        } else {      //Pruebas
            $wsdl = new nusoap_client('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl', 'wsdl');
        }

        $res = $wsdl->call('autorizacionComprobante', array("claveAccesoComprobante" => $clave));
        $req = $res[RespuestaAutorizacionComprobante][autorizaciones][autorizacion];
        $xml = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>
      <ns2:RespuestaAutorizacion xsi:type='ns2:autorizacion' xmlns:ns2='http://ec.gob.sri.ws.autorizacion' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'>
      <estado>" . $req[estado] . "</estado>
      <numeroAutorizacion>" . $req[numeroAutorizacion] . "</numeroAutorizacion>
      <fechaAutorizacion>" . $req[fechaAutorizacion] . "</fechaAutorizacion>
      <ambiente>" . $amb . "</ambiente>
      <comprobante><![CDATA[" . $req[comprobante] . "]]></comprobante>
      <mensajes/>
      </ns2:RespuestaAutorizacion>";

        return $respuesta = array($req[estado], $req[numeroAutorizacion], $req[fechaAutorizacion], $req[ambiente], $xml, $req[mensajes][mensaje][mensaje]);
    }

    function Clase_factura() {
        $this->con = new Conn();
    }

    function lista_no_enviados() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion where (ret_estado_correo<>'ENVIADO' or ret_estado_correo is null) and char_length(ret_autorizacion)=37  and ret_nombre<>'CONSUMIDOR FINAL' order by ret_id desc limit 1");
        }
    }

    function lista_una_retencion_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion r, erp_i_cliente c, erp_vendedores v where r.cli_id=c.cli_id and r.vnd_id=v.vnd_id and r.ret_id=$id");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where cod_punto_emision='$id'");
        }
    }

    function lista_detalle_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_retencion WHERE ret_id=$id");
        }
    }
    
    function update_estado_correo($sts, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_retencion SET ret_estado_correo='$sts' where ret_id=$id ");
        }
    }
    

}

class PDF extends FPDF {

    function Code39($x, $y, $code, $ext = true, $cks = false, $w = 0.11, $h = 14, $wide = false) {
        $this->SetFont('Arial', 'B', 7);
        $this->Text($x + 5, $y + 17, $code);
        if ($ext) {
            $code = $this->encode_code39_ext($code);
        } else {
            $code = strtoupper($code);
            if (!preg_match('|^[0-9A-Z. $/+%-]*$|', $code))
                $this->Error('Invalid barcode value: ' . $code);
        }
        if ($cks)
            $code .= $this->checksum_code39($code);
        $code = '*' . $code . '*';
        $narrow_encoding = array(
            '0' => '101001101101', '1' => '110100101011', '2' => '101100101011',
            '3' => '110110010101', '4' => '101001101011', '5' => '110100110101',
            '6' => '101100110101', '7' => '101001011011', '8' => '110100101101',
            '9' => '101100101101', 'A' => '110101001011', 'B' => '101101001011',
            'C' => '110110100101', 'D' => '101011001011', 'E' => '110101100101',
            'F' => '101101100101', 'G' => '101010011011', 'H' => '110101001101',
            'I' => '101101001101', 'J' => '101011001101', 'K' => '110101010011',
            'L' => '101101010011', 'M' => '110110101001', 'N' => '101011010011',
            'O' => '110101101001', 'P' => '101101101001', 'Q' => '101010110011',
            'R' => '110101011001', 'S' => '101101011001', 'T' => '101011011001',
            'U' => '110010101011', 'V' => '100110101011', 'W' => '110011010101',
            'X' => '100101101011', 'Y' => '110010110101', 'Z' => '100110110101',
            '-' => '100101011011', '.' => '110010101101', ' ' => '100110101101',
            '*' => '100101101101', '$' => '100100100101', '/' => '100100101001',
            '+' => '100101001001', '%' => '101001001001');

        $wide_encoding = array(
            '0' => '101000111011101', '1' => '111010001010111', '2' => '101110001010111',
            '3' => '111011100010101', '4' => '101000111010111', '5' => '111010001110101',
            '6' => '101110001110101', '7' => '101000101110111', '8' => '111010001011101',
            '9' => '101110001011101', 'A' => '111010100010111', 'B' => '101110100010111',
            'C' => '111011101000101', 'D' => '101011100010111', 'E' => '111010111000101',
            'F' => '101110111000101', 'G' => '101010001110111', 'H' => '111010100011101',
            'I' => '101110100011101', 'J' => '101011100011101', 'K' => '111010101000111',
            'L' => '101110101000111', 'M' => '111011101010001', 'N' => '101011101000111',
            'O' => '111010111010001', 'P' => '101110111010001', 'Q' => '101010111000111',
            'R' => '111010101110001', 'S' => '101110101110001', 'T' => '101011101110001',
            'U' => '111000101010111', 'V' => '100011101010111', 'W' => '111000111010101',
            'X' => '100010111010111', 'Y' => '111000101110101', 'Z' => '100011101110101',
            '-' => '100010101110111', '.' => '111000101011101', ' ' => '100011101011101',
            '*' => '100010111011101', '$' => '100010001000101', '/' => '100010001010001',
            '+' => '100010100010001', '%' => '101000100010001');

        $encoding = $wide ? $wide_encoding : $narrow_encoding;

        //Inter-character spacing
        $gap = ($w > 0.29) ? '00' : '0';

        //Convert to bars
        $encode = '';
        for ($i = 0; $i < strlen($code); $i++)
            $encode .= $encoding[$code[$i]] . $gap;

        //Draw bars
        $this->draw_code39($encode, $x, $y, $w, $h);
    }

    function checksum_code39($code) {
        $chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
            'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
            'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%');
        $sum = 0;
        for ($i = 0; $i < strlen($code); $i++) {
            $a = array_keys($chars, $code[$i]);
            $sum += $a[0];
        }
        $r = $sum % 43;
        return $chars[$r];
    }

    function encode_code39_ext($code) {
        $encode = array(
            chr(0) => '%U', chr(1) => '$A', chr(2) => '$B', chr(3) => '$C',
            chr(4) => '$D', chr(5) => '$E', chr(6) => '$F', chr(7) => '$G',
            chr(8) => '$H', chr(9) => '$I', chr(10) => '$J', chr(11) => '?K',
            chr(12) => '$L', chr(13) => '$M', chr(14) => '$N', chr(15) => '$O',
            chr(16) => '$P', chr(17) => '$Q', chr(18) => '$R', chr(19) => '$S',
            chr(20) => '$T', chr(21) => '$U', chr(22) => '$V', chr(23) => '$W',
            chr(24) => '$X', chr(25) => '$Y', chr(26) => '$Z', chr(27) => '%A',
            chr(28) => '%B', chr(29) => '%C', chr(30) => '%D', chr(31) => '%E',
            chr(32) => ' ', chr(33) => '/A', chr(34) => '/B', chr(35) => '/C',
            chr(36) => '/D', chr(37) => '/E', chr(38) => '/F', chr(39) => '/G',
            chr(40) => '/H', chr(41) => '/I', chr(42) => '/J', chr(43) => '/K',
            chr(44) => '/L', chr(45) => '-', chr(46) => '.', chr(47) => '/O',
            chr(48) => '0', chr(49) => '1', chr(50) => '2', chr(51) => '3',
            chr(52) => '4', chr(53) => '5', chr(54) => '6', chr(55) => '7',
            chr(56) => '8', chr(57) => '9', chr(58) => '/Z', chr(59) => '%F',
            chr(60) => '%G', chr(61) => '%H', chr(62) => '%I', chr(63) => '%J',
            chr(64) => '%V', chr(65) => 'A', chr(66) => 'B', chr(67) => 'C',
            chr(68) => 'D', chr(69) => 'E', chr(70) => 'F', chr(71) => 'G',
            chr(72) => 'H', chr(73) => 'I', chr(74) => 'J', chr(75) => 'K',
            chr(76) => 'L', chr(77) => 'M', chr(78) => 'N', chr(79) => 'O',
            chr(80) => 'P', chr(81) => 'Q', chr(82) => 'R', chr(83) => 'S',
            chr(84) => 'T', chr(85) => 'U', chr(86) => 'V', chr(87) => 'W',
            chr(88) => 'X', chr(89) => 'Y', chr(90) => 'Z', chr(91) => '%K',
            chr(92) => '%L', chr(93) => '%M', chr(94) => '%N', chr(95) => '%O',
            chr(96) => '%W', chr(97) => '+A', chr(98) => '+B', chr(99) => '+C',
            chr(100) => '+D', chr(101) => '+E', chr(102) => '+F', chr(103) => '+G',
            chr(104) => '+H', chr(105) => '+I', chr(106) => '+J', chr(107) => '+K',
            chr(108) => '+L', chr(109) => '+M', chr(110) => '+N', chr(111) => '+O',
            chr(112) => '+P', chr(113) => '+Q', chr(114) => '+R', chr(115) => '+S',
            chr(116) => '+T', chr(117) => '+U', chr(118) => '+V', chr(119) => '+W',
            chr(120) => '+X', chr(121) => '+Y', chr(122) => '+Z', chr(123) => '%P',
            chr(124) => '%Q', chr(125) => '%R', chr(126) => '%S', chr(127) => '%T');

        $code_ext = '';
        for ($i = 0; $i < strlen($code); $i++) {
            if (ord($code[$i]) > 127)
                $this->Error('Invalid character: ' . $code[$i]);
            $code_ext .= $encode[$code[$i]];
        }
        return $code_ext;
    }

    function draw_code39($code, $x, $y, $w, $h) {
        for ($i = 0; $i < strlen($code); $i++) {
            if ($code[$i] == '1')
                $this->Rect($x + $i * $w, $y, $w, $h, 'F');
        }
    }

    function factura($rst, $cns, $emisor) {
        // ///////////////////////////////// ENCABEZADO IZQUIERDO ///////////////////////////////////////////////////////
       $this->Ln($x + 2, $y + 90);
        $this->Image('../img/logo_noperti.jpg', 1, 1, 100);
        $this->SetFont('helvetica', 'B', 12);
        $this->SetXY(5, 34);
        $this->Cell(100, 15, "NOPERTI CIA LTDA ", 'LRT', 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(5, 45);
        $this->Cell(100, 9, "Dir Matriz :", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(5, 50);
        $this->Cell(100, 9, utf8_decode($emisor[dir_establecimiento_matriz]), 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(5, 55);
        $this->Cell(100, 9, "Dir Sucursal : ", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(5, 60);
        $this->Cell(100, 9, utf8_decode($emisor[dir_establecimiento_emisor]), 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(5, 67);
        $this->Cell(100, 5, "Contribuyente Especial Nro :   636  ", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(5, 72);
        $this->Cell(100, 5, "OBLIGAO A LLEVAR CONTABILIDAD      SI ", 'LRB', 0, 'L');
        $this->Ln();
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////// ENCABEZADO DERECHO ////////////////////////////////////////////////////////        
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(115, 9);
        $this->Cell(85, 7, "RUC: " . $emisor[identificacion], 'LRT', 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 12);
        $this->SetXY(115, 14);
        $this->Cell(85, 5, "COMPROBANTE DE RETENCION", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(115, 19);
        $this->Cell(85, 5, "  No.   " . $rst[ret_numero], 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(115, 24);
        $this->Cell(85, 5, "NUMERO DE AUTORIZACION", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(115, 29);
        $this->Cell(85, 5, $rst[ret_autorizacion], 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(115, 34);
        $this->Cell(85, 5, "FECHA Y HORA DE  ", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(115, 39);
        $this->Cell(85, 5, "AUTORIZACION : " . $rst[ret_fec_hora_aut], 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(115, 44);
        $this->Cell(85, 5, "AMBIENTE :  PRODUCCION", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(115, 49);
        $this->Cell(85, 5, "EMISION :  NORMAL", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(115, 54);
        $this->Cell(85, 5, "CLAVE DE ACCESO", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(115, 59);
        $this->Code39($x + 115, $y + 59, $rst[ret_clave_acceso]);
        $this->Cell(85, 19, "", 'LRB', 0, 'L');
        $this->Ln();
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////// ENCABEZADO CENTRAL ////////////////////////////////////////////////////////        
        $this->Ln($x + 4, $y + 1);
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(140, 5, "Razon Social / Nombres y Apellidos : " . trim($rst[ret_nombre]), 'LT', 0, 'L');
        $this->Cell(55, 5, "Identificacion : " . $rst[ret_identificacion], 'RT', 'R');
        $this->Ln();
        $this->Cell(195, 5, "Fecha de Emision : " . $rst[ret_fecha_emision], 'LRB', 0, 'L');

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////// CUERPO ////////////////////////////////////////////////////////                        

        $this->Ln();
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(6, 100);
        $this->Cell(27, 5, "Comprobante", 'LT', 0, 'C');
        $this->Cell(30, 5, "Numero", 'LT', 0, 'C');
        $this->Cell(26, 5, "Fecha Emision", 'LT', 0, 'C');
        $this->Cell(19, 5, "Ejercicio", 'LT', 0, 'C');
        $this->Cell(36, 5, "Base imponible para", 'LT', 0, 'C');
        $this->Cell(20, 5, "IMPUESTO", 'LT', 0, 'C');
        $this->Cell(22, 5, "Porcentaje ", 'LT', 0, 'C');
        $this->Cell(17, 5, "Valor", 'LTR', 0, 'C');
        $this->Ln();
        $this->SetXY(6, 105);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(27, 5, "", 'LBR', 0, 'C');
        $this->Cell(30, 5, "", 'BR', 0, 'C');
        $this->Cell(26, 5, "", 'LBR', 0, 'C');
        $this->Cell(19, 5, "FISCAL", 'BR', 0, 'C');
        $this->Cell(36, 5, "la Retencion", 'LBR', 0, 'C');
        $this->Cell(20, 5, "", 'BR', 0, 'L');
        $this->Cell(22, 5, "Retencion", 'LBR', 0, 'C');
        $this->Cell(17, 5, "Retenido", 'BR', 0, 'C');
        $this->Ln();

        $y1 = 0;
        $n = 0;
        while ($rst2 = pg_fetch_array($cns)) {
            $n++;
            if ($rst[ret_denominacion_comp] == 1) {
                $comp = 'FACTURA';
            } else {
                $comp = 'NOTA CREDITO';
            }
            if ($rst2[dtr_tipo_impuesto] == "IV") {
                $tp = 'IVA';
            } else if ($rst2[dtr_tipo_impuesto] == "IR") {
                $tp = 'RENTA';
            } else if ($rst2[dtr_tipo_impuesto] == "ID") {
                $tp = 'SALIDA DE DIVISAS';
            }

            $this->SetXY(6, 110 + $y);
            $this->SetFont('helvetica', '', 8);
            $this->Cell(27, 5, $comp, 1, 0, 'C');
            $this->Cell(30, 5, $rst[ret_num_comp_retiene], 1, 0, 'C');
            $this->Cell(26, 5, $rst[ret_fecha_emision], 1, 0, 'R');
            $this->Cell(19, 5, $rst2[dtr_ejercicio_fiscal], 1, 0, 'C');
            $this->Cell(36, 5, $rst2[dtr_base_imponible], 1, 0, 'C');
            $this->Cell(20, 5, $tp, 1, 0, 'C');
            $this->Cell(22, 5, $rst2[dtr_procentaje_retencion], 1, 0, 'C');
            $this->Cell(17, 5, $rst2[dtr_valor], 1, 0, 'C');
            $this->Ln();
            $y = $y + 5;
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////       
        ///////////////////////////////// ENCABEZADO INFERIOR IZQUIERDO ////////////////////////////////////////////////
        $this->Ln();
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(85, 10, "Informacion Adicional ", 'LRT', 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(85, 9, "Direccion : " . $rst[ret_direccion], 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(85, 9, "Telefono : " . $rst[ret_telefono], 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(85, 9, "Email :  " . strtolower($rst[ret_email]), 'LRB', 0, 'L');
        $this->Ln();
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    }

}

set_time_limit(0);
date_default_timezone_set('America/Guayaquil');
$Set = new Clase_factura();
$Obj = new Mail();
$cns_docs = $Set->lista_no_enviados();
$PDF = new PDF();
$amb = 2;
while ($rst = pg_fetch_array($cns_docs)) {
    $id = $rst[ret_id];
    $em=$rst[emi_id];
    if ($em < 10) {
        $emi = '00' . $em;
    } else {
        $emi = '0' . $em;
    }
    $rstret = pg_fetch_array($Set->lista_una_retencion_id($id)); //lista nota
    $emisor = pg_fetch_array($Set->lista_emisor($emi));
    $cns = $Set->lista_detalle_retencion($id);
    $PDF->AddPage();
    $PDF->factura($rstret, $cns, $emisor);
    $PDF->Output('../mail/' . $id . '.pdf', 'F');
    $doc = $Set->recupera_datos($rst[ret_clave_acceso], $amb);
    $fch = fopen("../xml_docs/" . $rst[ret_clave_acceso] . ".xml", "w+o");
    fwrite($fch, $doc[4]);
    fclose($fch);
    $files = array('../mail/' . $id . '.pdf', '../xml_docs/' . $rst[ret_clave_acceso] . '.xml');
    $resp = $Obj->envia_correo($rst[ret_email], $rst[ret_nombre], $files, $rst[ret_numero], 7);
    if ($resp == 0) {
        if (!$Set->update_estado_correo('ENVIADO', $id)) {
            echo "Estado " . pg_last_error();
        }
    }
}


