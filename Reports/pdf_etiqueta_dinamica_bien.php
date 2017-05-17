<?php

include_once '../Clases/Conn.php';

class Clase_etiqueta {

    var $con;

    function Clase_etiqueta() {
        $this->con = new Conn();
    }

    function lista_una_etiqueta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_etiquetas  where eti_id=$id");
        }
    }

}

date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_etiqueta();
$dt = $_GET[datos];
$data = explode(',', $dt);
$rst = pg_fetch_array($Set->lista_una_etiqueta($data[7]));
switch ($rst[eti_tamano]) {
    case '1':
        $tam = 'etiqueta_reg';
        $o = 'p';
        break;
    case '3':
        $tam = 'etiqueta_grande1';
        $o = 'l';
        break;
//    case '2':
//        $tam = 'etiqueta_op';
//        break;
    default:
        $tam = 'etiqueta_reg';
        $o = 'p';
        break;
}



require('pdf_js.php');

class PDF_AutoPrint extends PDF_JavaScript {

    function AutoPrint($dialog = false) {
        //Open the print dialog or start printing immediately on the standard printer
        $param = ($dialog ? 'true' : 'false');
        $script = "print($param);";
        $this->IncludeJS($script);
    }

    function AutoPrintToPrinter($server, $printer, $dialog = false) {
        //Print on a shared printer (requires at least Acrobat 6)
        $script = "var pp = getPrintParams();";
        if ($dialog)
            $script .= "pp.interactive = pp.constants.interactionLevel.full;";
        else
            $script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
        $script .= "pp.printerName = '\\\\\\\\" . $server . "\\\\" . $printer . "';";
        $script .= "print(pp);";
        $this->IncludeJS($script);
    }

    function Code39($x, $y, $code, $tl, $w, $h, $ext = true, $cks = false, $wide = false) {
        $this->SetFont('Arial', '', $tl);
        $this->Text($x + 13, $y + 10, $code);
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

    function etq($data, $rst) {
        switch ($rst[eti_tamano]) {
            case '1':
                $x = 1;
                $y = 1;
                $w = 1;
                $h = 1;
                $tl = 7;
                break;
//            case '2':
//                $x = 2;
//                $y = 2;
//                $w=1.5;
//                $h=1.5;
//                $tl=10;
//                break;
            default:
                $x = 1;
                $y = 1;
                $w = 1;
                $h = 1;
                $tl = 7;
                break;
        }

        $dt = explode('&', $rst[eti_elementos]);
        switch ($data[6]) {
            case 0:
                $estado = 'CONFORME';
                break;
            case 3:
                $estado = 'INCONFORME';
                break;
        }
        $this->SetFont('helvetica', '', $tl);
        if ($dt[7] == 1) {
            $this->Text(15 * $x, 4 * $y, utf8_decode("Polipack Cía. Ltda."));
        }
        if ($dt[0] == 1) {
            $this->Text(5 * $x, 7 * $y, "OT: " . $data[0]);
        }
        if ($dt[1] == 1) {
            $this->Text(5 * $x, 10 * $y, "Ancho: " . $data[1]);
        }
        if ($dt[2] == 1) {
            $this->Text(25 * $x, 10 * $y, "Peso: " . $data[2] . " kg");
        }
        if ($dt[3] == 1) {
            $this->Text(5 * $x, 13 * $y, "Espesor: " . $data[3]);
        }
        if ($dt[4] == 1) {
            $this->Text(25 * $x, 13 * $y, "Largo: " . $data[4] . " m");
        }
        if ($dt[5] == 1) {
            $this->Code39(5 * $x, 14 * $y, $data[5], $tl, 0.25, 8);
        }
        if ($dt[6] == 1) {
            if ($data[6] == 3) {
                $this->Text(18 * $x, 27 * $y, $estado);
            }
        }
    }

    function etq_grande($data, $rst) {

        $dt = explode('&', $rst[eti_elementos]);
        $this->SetXY(4, 5);
        $this->SetFont('helvetica', 'b', 7);
        $this->Cell(61, 80, '', '1', 0, 'C');
        if ($dt[0] == 1) {
            $this->SetXY(4, 5);
            $this->Cell(58, 5, utf8_decode("POLIPACK CÍA. LTDA."), '0', 0, 'C');
        } else {
            $this->Cell(58, 5, '', '0', 0, 'C');
        }
        $this->Ln();
        $this->SetXY(7, 10);
        $this->SetFont('helvetica', 'b', 7);
        if ($dt[1] == 1) {
            $this->Cell(17, 5, 'FECHA:', '0', 0, 'L');
            $this->SetFont('helvetica', '', 7);
            $this->Cell(14, 5, $data[9], '0', 0, 'C');
        }
//        if ($dt[2] == 1) {
        $this->Cell(12, 5, '', '0', 0, 'L'); //espacio1
//        }
//        if ($dt[3] == 1) {
        $this->Cell(12, 5, '', '0', 0, 'L'); //espacio2
//        }
        $this->Ln();
        $this->SetXY(7, 15);
        if ($dt[4] == 1) {
            $this->SetFont('helvetica', 'b', 7);
            $this->Cell(17, 5, 'CLIENTE:', 'LTB', 0, 'L');
            $this->SetFont('helvetica', '', 7);
            $this->Cell(38, 5, substr($data[10], 0, 33), 'RTB', 0, 'L');
        } else {
            $this->Cell(17, 5, '', 'LTB', 0, 'L');
            $this->Cell(38, 5, '', 'RTB', 0, 'L');
        }
        $this->Ln();

        $this->SetXY(7, 20);
        if ($dt[5] == 1) {
            $this->SetFont('helvetica', 'b', 7);
            $this->Cell(17, 5, 'O/T:', 'LTB', 0, 'L');
            $this->SetFont('helvetica', '', 7);
            $this->Cell(14, 5, $data[0], 'RTB', 0, 'L');
        } else {
            $this->Cell(17, 5, '', 'LTB', 0, 'L');
            $this->Cell(14, 5, '', 'RTB', 0, 'L');
        }
//        if ($dt[6] == 1) {
        $this->Cell(12, 5, '', 'LTB', 0, 'L'); //espacio3
//        }
//        if ($dt[7] == 1) {
        $this->Cell(12, 5, '', 'RTB', 0, 'L'); //espacio4
//        }
        $this->Ln();
        $this->SetXY(7, 25);
//        if ($dt[8] == 1) {
        $this->Cell(17, 5, '', 'LTB', 0, 'L'); //espacio5
//        }
//        if ($dt[9] == 1) {
        $this->Cell(14, 5, '', 'RTB', 0, 'L'); //espacio6
//        }
        if ($dt[10] == 1) {
            $this->SetFont('helvetica', 'b', 7);
            $this->Cell(12, 5, 'TARA:', 'LTB', 0, 'L');
            $this->SetFont('helvetica', '', 7);
            $this->Cell(12, 5, $data[11], 'RTB', 0, 'L');
        } else {
            $this->Cell(12, 5, '', 'LTB', 0, 'L');
            $this->Cell(12, 5, '', 'RTB', 0, 'L');
        }
        $this->Ln();
        $this->SetXY(7, 30);
        if ($dt[11] == 1) {
            $this->SetFont('helvetica', 'b', 7);
            $this->Cell(17, 5, 'P.BRUTO:', 'LTB', 0, 'L');
            $this->SetFont('helvetica', '', 7);
            $this->Cell(14, 5, $data[2], 'RTB', 0, 'L');
        } else {
            $this->Cell(17, 5, '', 'LTB', 0, 'L');
            $this->Cell(14, 5, '', 'RTB', 0, 'L');
        }
        if ($dt[12] == 1) {
            $this->SetFont('helvetica', 'b', 7);
            $this->Cell(12, 5, 'P.NETO:', 'LTB', 0, 'L');
            $this->SetFont('helvetica', '', 7);
            $this->Cell(12, 5, $data[12], 'RTB', 0, 'L');
        } else {
            $this->Cell(12, 5, '', 'LTB', 0, 'L');
            $this->Cell(32, 5, '', 'RTB', 0, 'L');
        }
        $this->Ln();
        $this->SetXY(7, 35);
        if ($dt[13] == 1) {
            $this->SetFont('helvetica', 'b', 7);
            $this->Cell(17, 5, 'ESPESOR:', 'LTB', 0, 'L');
            $this->SetFont('helvetica', '', 7);
            $this->Cell(14, 5, $data[3], 'RTB', 0, 'L');
        } else {
            $this->Cell(17, 5, '', 'LTB', 0, 'L');
            $this->Cell(14, 5, '', 'RTB', 0, 'L');
        }
        if ($dt[14] == 1) {
            $this->SetFont('helvetica', 'b', 7);
            $this->Cell(12, 5, 'ANCHO:', 'LTB', 0, 'L');
            $this->SetFont('helvetica', '', 7);
            $this->Cell(12, 5, $data[1], 'RTB', 0, 'L');
        } else {
            $this->Cell(12, 5, '', 'LTB', 0, 'L');
            $this->Cell(12, 5, '', 'RTB', 0, 'L');
        }
        $this->Ln();
        $this->SetXY(7, 40);
//        if ($dt[15] == 1) {
        $this->Cell(17, 5, '', 'LTB', 0, 'L'); //espacio7
//        }
//        if ($dt[16] == 1) {
        $this->Cell(14, 5, '', 'RTB', 0, 'L'); //espacio8
//        }
//        if ($dt[17] == 1) {
        $this->Cell(12, 5, '', 'LTB', 0, 'L'); //espacio9
//        }
//        if ($dt[18] == 1) {
        $this->Cell(12, 5, '', 'RTB', 0, 'L'); //espacio10
//        }
        $this->Ln();
        $this->SetXY(7, 45);
        if ($dt[19] == 1) {
            $this->SetFont('helvetica', 'b', 7);
            $this->Cell(17, 5, 'OPERADOR', 'LTB', 0, 'L');
            $this->SetFont('helvetica', '', 7);
            $this->Cell(14, 5, '', 'RTB', 0, 'L');
        } else {
            $this->Cell(17, 5, '', 'LTB', 0, 'L');
            $this->Cell(14, 5, '', 'RTB', 0, 'L');
        }
//        if ($dt[20] == 1) {
        $this->Cell(12, 5, '', 'LTB', 0, 'L'); //espacio11
//        }
//        if ($dt[21] == 1) {
        $this->Cell(12, 5, '', 'RTB', 0, 'L'); //espacio12
//        }
        $this->Ln();
        $this->SetXY(7, 50);
//        if ($dt[22] == 1) {
        $this->Cell(31, 5, '', '1', 0, 'L'); //espacio13
//        }
//        if ($dt[23] == 1) {
        $this->Cell(24, 5, '', '1', 0, 'L'); //espacio14
//        }
        $this->Ln();
        $this->SetXY(7, 55);
        if ($dt[24] == 1) {
            $this->Code39(15, 60, $data[5], 7, 0.25, 8);
            $this->Cell(55, 18, '', '1', 0, 'L');
        } else {
            $this->Cell(55, 18, '', 'LTB', 0, 'L');
        }
        $this->Ln();
        $this->SetXY(26, 69);
        if ($dt[25] == 1) {
            if ($data[6] == 3) {
                $this->Cell(24, 5, 'INCONFORME', '0', 0, 'L'); //espacio14
            }
        }


//////////////////////copia
//        $this->SetXY(68, 5);
//        $this->SetFont('helvetica', 'b', 7);
//        $this->Cell(61, 80, '', '1', 0, 'C');
//        if ($dt[0] == 1) {
//            $this->SetXY(71, 5);
//            $this->Cell(58, 5, utf8_decode("POLIPACK CÍA. LTDA."), '0', 0, 'C');
//        } else {
//            $this->Cell(58, 5, '', '0', 0, 'C');
//        }
//        $this->Ln();
//        $this->SetXY(71, 10);
//        $this->SetFont('helvetica', 'b', 7);
//        if ($dt[1] == 1) {
//            $this->Cell(17, 5, 'FECHA:', '0', 0, 'L');
//            $this->SetFont('helvetica', '', 7);
//            $this->Cell(14, 5, $data[9], '0', 0, 'C');
//        }
//        $this->Cell(12, 5, '', '0', 0, 'L'); //espacio1
//        $this->Cell(12, 5, '', '0', 0, 'L'); //espacio1
//        $this->Ln();
//        $this->SetXY(71, 15);
//        if ($dt[4] == 1) {
//            $this->SetFont('helvetica', 'b', 7);
//            $this->Cell(17, 5, 'CLIENTE:', 'LTB', 0, 'L');
//            $this->SetFont('helvetica', '', 7);
//            $this->Cell(38, 5, substr($data[10], 0, 33), 'RTB', 0, 'L');
//        } else {
//            $this->Cell(17, 5, '', 'LTB', 0, 'L');
//            $this->Cell(38, 5, '', 'RTB', 0, 'L');
//        }
//        $this->Ln();
//        $this->SetXY(71, 20);
//        if ($dt[5] == 1) {
//            $this->SetFont('helvetica', 'b', 7);
//            $this->Cell(17, 5, 'O/T:', 'LTB', 0, 'L');
//            $this->SetFont('helvetica', '', 7);
//            $this->Cell(14, 5, $data[0], 'RTB', 0, 'L');
//        } else {
//            $this->Cell(17, 5, '', 'LTB', 0, 'L');
//            $this->Cell(14, 5, '', 'RTB', 0, 'L');
//        }
//        $this->Cell(12, 5, '', 'LTB', 0, 'L'); //espacio3
//        $this->Cell(12, 5, '', 'RTB', 0, 'L'); //espacio4
//        $this->Ln();
//        $this->SetXY(71, 25);
//        $this->Cell(17, 5, '', 'LTB', 0, 'L'); //espacio5
//        $this->Cell(14, 5, '', 'RTB', 0, 'L'); //espacio6
//        if ($dt[10] == 1) {
//            $this->SetFont('helvetica', 'b', 7);
//            $this->Cell(12, 5, 'TARA:', 'LTB', 0, 'L');
//            $this->SetFont('helvetica', '', 7);
//            $this->Cell(12, 5, $data[11], 'RTB', 0, 'L');
//        } else {
//            $this->Cell(12, 5, '', 'LTB', 0, 'L');
//            $this->Cell(12, 5, '', 'RTB', 0, 'L');
//        }
//        $this->Ln();
//        $this->SetXY(71, 30);
//        if ($dt[11] == 1) {
//            $this->SetFont('helvetica', 'b', 7);
//            $this->Cell(17, 5, 'P.BRUTO:', 'LTB', 0, 'L');
//            $this->SetFont('helvetica', '', 7);
//            $this->Cell(14, 5, $data[2], 'RTB', 0, 'L');
//        } else {
//            $this->Cell(17, 5, '', 'LTB', 0, 'L');
//            $this->Cell(14, 5, '', 'RTB', 0, 'L');
//        }
//        if ($dt[12] == 1) {
//            $this->SetFont('helvetica', 'b', 7);
//            $this->Cell(12, 5, 'P.NETO:', 'LTB', 0, 'L');
//            $this->SetFont('helvetica', '', 7);
//            $this->Cell(12, 5, $data[12], 'RTB', 0, 'L');
//        } else {
//            $this->Cell(12, 5, '', 'LTB', 0, 'L');
//            $this->Cell(32, 5, '', 'RTB', 0, 'L');
//        }
//        $this->Ln();
//        $this->SetXY(71, 35);
//        if ($dt[13] == 1) {
//            $this->SetFont('helvetica', 'b', 7);
//            $this->Cell(17, 5, 'ESPESOR:', 'LTB', 0, 'L');
//            $this->SetFont('helvetica', '', 7);
//            $this->Cell(14, 5, $data[3], 'RTB', 0, 'L');
//        } else {
//            $this->Cell(17, 5, '', 'LTB', 0, 'L');
//            $this->Cell(14, 5, '', 'RTB', 0, 'L');
//        }
//        if ($dt[14] == 1) {
//            $this->SetFont('helvetica', 'b', 7);
//            $this->Cell(12, 5, 'ANCHO:', 'LTB', 0, 'L');
//            $this->SetFont('helvetica', '', 7);
//            $this->Cell(12, 5, $data[1], 'RTB', 0, 'L');
//        } else {
//            $this->Cell(12, 5, '', 'LTB', 0, 'L');
//            $this->Cell(12, 5, '', 'RTB', 0, 'L');
//        }
//        $this->Ln();
//        $this->SetXY(71, 40);
//        $this->Cell(17, 5, '', 'LTB', 0, 'L'); //espacio7
//        $this->Cell(14, 5, '', 'RTB', 0, 'L'); //espacio8
//        $this->Cell(12, 5, '', 'LTB', 0, 'L'); //espacio9
//        $this->Cell(12, 5, '', 'RTB', 0, 'L'); //espacio10
//        $this->Ln();
//        $this->SetXY(71, 45);
//        if ($dt[19] == 1) {
//            $this->SetFont('helvetica', 'b', 7);
//            $this->Cell(17, 5, 'OPERADOR', 'LTB', 0, 'L');
//            $this->SetFont('helvetica', '', 7);
//            $this->Cell(14, 5, '', 'RTB', 0, 'L');
//        } else {
//            $this->Cell(17, 5, '', 'LTB', 0, 'L');
//            $this->Cell(14, 5, '', 'RTB', 0, 'L');
//        }
//        $this->Cell(12, 5, '', 'LTB', 0, 'L'); //espacio11
//        $this->Cell(12, 5, '', 'RTB', 0, 'L'); //espacio12
//        $this->Ln();
//        $this->SetXY(71, 50);
//        $this->Cell(31, 5, '', '1', 0, 'L'); //espacio13
//        $this->Cell(24, 5, '', '1', 0, 'L'); //espacio14
//        $this->Ln();
//        $this->SetXY(71, 55);
//        if ($dt[24] == 1) {
//            $this->Code39(78, 60, $data[5], 7, 0.25, 8);
//            $this->Cell(55, 18, '', '1', 0, 'L');
//        } else {
//            $this->Cell(55, 18, '', 'LTB', 0, 'L');
//        }
//        $this->Ln();
//        $this->SetXY(89, 69);
//        if ($dt[25] == 1) {
//            if ($data[6] == 3) {
//                $this->Cell(24, 5, 'INCONFORME', '0', 0, 'L'); //espacio14
//            }
//        }
    }

}

$pdf = new PDF_AutoPrint($orientation = $o, $unit = 'mm', $size = $tam);
$m = 0;
$j = 1;
if (!empty($data[13])) {
    $j = 2;
}
while ($m < $j) {
    $n = 0;
    if (!empty($data[13]) && $m == 1) {
        $data = array(
            $data[0],
            $data[1],
            $data[13],
            $data[3],
            $data[4],
            $data[15],
            $data[16],
            $data[7],
            $data[8],
            $data[9],
            $data[10],
            $data[11],
            $data[12]
        );
    }
    while ($n < $data[8]) {
        $pdf->AddPage();
        if ($rst[eti_tamano] != 3) {
            $pdf->etq($data, $rst);
        } else {
            $pdf->etq_grande($data, $rst);
        }
        $n++;
    }
    $m++;
}
$pdf->AutoPrint(true);
$pdf->Output();
?>