<?php

//include_once '../Clases/clsClase_factura.php';
include_once '../Clases/clsClase_nota.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_nota_Credito();
$id = $_GET[id];
$rst = pg_fetch_array($Set->lista_una_nota_credito_id($id)); //lista nota
$emisor = pg_fetch_array($Set->lista_emisor($rst[emi_id]));
$cns = $Set->lista_detalle_nota_credito($id);

class PDF extends FPDF {

    function factura($rst, $cns, $emisor) {
        $x = -10;
        $y = 0;
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(1, 2);
        $this->Cell(75, 5, utf8_decode($emisor[nombre_comercial]), '', 0, 'C');
        $this->SetFont('helvetica', 'B', 7);
        $this->SetXY(1, 7);
        $this->Cell(75, 5, utf8_decode(substr($emisor[dir_establecimiento_emisor], 0, 48)), '', 0, 'C');
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 41, $y + 16, "MATRIZ");
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 33, $y + 19, "NOPERTI CIA. LTDA.");
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 19, $y + 22, utf8_decode($emisor[dir_establecimiento_matriz]));
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 34, $y + 25, "QUITO - ECUADOR");
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 33, $y + 28, "RUC:" . $emisor[identificacion]);

        $this->SetFont('Arial', 'B', 7);
        $this->SetXY(1, 30);
        $this->Cell(44, 5, "DESCRIPCION", 'TB', 0, 'C');
        $this->Cell(9, 5, "CANT.", 'TB', 0, 'C');
        $this->Cell(9, 5, "P.UNI", 'TB', 0, 'C');
        $this->Cell(12, 5, "P.TOT", 'TB', 0, 'C');

        $this->Ln();
        $n = 0;
        while ($rst1 = pg_fetch_array($cns)) {
            $this->SetXY(1, 35 + $y1);
            $n++;

            $this->SetFont('helvetica', '', 6);
            $this->Cell(42, 5, utf8_decode(substr($rst1[dnc_descripcion], 0, 35)), '', 0, 'L');
            $this->Cell(10, 5, number_format($rst1[dnc_cantidad], 2), '', 0, 'R');
            $this->Cell(10, 5, number_format($rst1[dnc_precio_unit], 2), '', 0, 'R');
            $this->Cell(10, 5, number_format($rst1[dnc_precio_total], 2), '', 0, 'R');
            $this->Ln();
            $y1 += 3;
        }
        $this->SetFont('helvetica', '', 7);
        $this->SetXY(1, 36 + $y1);
        $this->Cell(54, 5, "SUBTOTAL 12%", 'T', 0, 'L');
        $this->Cell(18, 5, number_format($rst[ncr_subtotal12], 2), 'T', 0, 'R');
        $this->SetXY(1, 40 + $y1);
        $this->Cell(54, 5, "SUBTOTAL 0%", '', 0, 'L');
        $this->Cell(18, 5, number_format($rst[ncr_subtotal0], 2), '', 0, 'R');
        $this->Ln();
        $this->SetXY(1, 44 + $y1);
        $this->Cell(54, 5, "SUBTOTAL Exento de IVA", '', 0, 'L');
        $this->Cell(18, 5, number_format($rst[ncr_subtotal_ex_iva], 2), '', 0, 'R');
        $this->Ln();
        $this->SetXY(1, 48 + $y1);
        $this->Cell(54, 5, "SUBTOTAL No objeto de IVA", '', 0, 'L');
        $this->Cell(18, 5, number_format($rst[ncr_subtotal_no_iva], 2), '', 0, 'R');
        $this->Ln();
        $this->SetXY(1, 52 + $y1);
        $this->Cell(54, 5, "TOTAL DESCUENTO", '', 0, 'L');
        $this->Cell(18, 5, number_format($rst[ncr_total_descuento], 2), '', 0, 'R');
        $this->Ln();
        $this->SetXY(1, 56 + $y1);
        $this->Cell(54, 5, "IVA", '', 0, 'L');
        $this->Cell(18, 5, number_format($rst[ncr_total_iva], 2), '', 0, 'R');
        $this->Ln();
        $this->SetXY(1, 60 + $y1);
        $this->SetFont('helvetica', 'B', 7);
        $this->Cell(54, 5, "VALOR TOTAL", 'TB', 0, 'L');
        $this->Cell(18, 5, number_format($rst[nrc_total_valor], 2), 'TB', 0, 'R');
        $this->Ln();
        $this->SetFont('helvetica', '', 7);
        $this->SetXY(1, 68 + $y1);
        $this->Cell(14, 5, "CLIENTE:" . utf8_decode($rst[ncr_nombre]), 0, 'L');
        $this->Ln();
        $this->SetXY(1, 72 + $y1);
        $this->Cell(14, 5, "CED/RUC:" . $rst[ncr_identificacion], 0, 'L');
        $this->Ln();
        $this->SetXY(1, 76 + $y1);
        $this->Cell(14, 5, "EMAIL:" . strtolower($rst[ncr_email]), 0, 'L');
        $this->Ln();
        $this->SetXY(1, 80 + $y1);
        $this->Cell(14, 5, "FACTURA No.:" . $rst[ncr_num_comp_modifica], 0, 'L');
        $this->Ln();
        $this->SetXY(1, 84 + $y1);
        $this->Cell(14, 5, "NOTA CREDITO No.:" . $rst[ncr_numero], 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->SetXY(1, 90 + $y1);
        $this->Cell(14, 5, "GRACIAS POR PREFERIRNOS", '', 0, 'L');
        $this->Ln();
        $this->SetXY(1, 94 + $y1);
        $this->Cell(14, 5, "Comprobante sin validez tributaria ", '', 0, 'L');
        $this->Ln();
        $this->SetXY(1, 98 + $y1);
        $this->Cell(14, 5, "Revise su comprobante en su mail o ingrese a:", '', 0, 'L');
        $this->Ln();
        $this->SetXY(1, 102 + $y1);
        $this->Cell(14, 5, "www.gruponoperti.com", '', 0, 'L');
        $this->Ln();
        $this->SetXY(1, 106 + $y1);
        $this->Cell(14, 5, "Usuario: " . $rst[ncr_identificacion], 0, 'L');
        $this->Ln();
        if ($rst[emi_id] < 10) {
            $pto_em = '0' . $rst[emi_id];
        } else {
            $pto_em = $rst[emi_id];
        }
        $g = explode('-', $rst[ncr_numero]);
        $sec = intval($g[2]);
        $this->SetXY(1, 110 + $y1);
        $this->Cell(14, 5, "Clave: 04" . $pto_em . $sec, 0, 'L');
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'talonario');
$pdf->AddPage();
$pdf->factura($rst, $cns, $emisor);
$pdf->SetDisplayMode(100);
$pdf->Output();



