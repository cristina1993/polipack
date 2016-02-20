<?php

include_once '../Clases/clsClase_rep_ventas.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_rep_ventas();

$desde = $_GET[desde];
$hasta = $_GET[hasta];

$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));

class PDF extends FPDF {

    function encabezado($emisor, $desde, $hasta) {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(295, 15, "REPORTE DE VENTAS", 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(85, 5, "PERIODO  DESDE: " . $desde . '  HASTA: ' . $hasta, 0, 0, 'L');
        $this->Ln();
        $this->Cell(85, 5, "RUC: " . $emisor[identificacion], 0, 0, 'L');
        $this->Ln();
        $this->Cell(85, 5, "RAZON SOCIAL: NOPERTI CIA LTDA", 0, 0, 'L');
        $this->Ln();
//        $this->Cell(85, 5, "MONEDA: DOLAR", 0, 0, 'L');
//        $this->Ln();
    }

    function encabezado_tab() {
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(28, 5, "", 'LRT', 0, 'C');
        $this->Cell(17, 5, "NOPERTI", 'LRT', 0, 'C');
        $this->Cell(17, 5, "INDUSTRIAL", 'LRT', 0, 'C');
        $this->Cell(17, 5, "ATAHUALPA", 'LRT', 0, 'C');
        $this->Cell(17, 5, "CCNU", 'LRT', 0, 'C');
        $this->Cell(17, 5, "EL CONDADO", 'LRT', 0, 'C');
        $this->Cell(17, 5, "QUICENTRO", 'LRT', 0, 'C');
        $this->Cell(17, 5, "QUICENTRO", 'LRT', 0, 'C');
        $this->Cell(17, 5, "RECREO", 'LRT', 0, 'C');
        $this->Cell(17, 5, "TOP TENIS", 'LRT', 0, 'C');
        $this->Cell(17, 5, "CITY MALL", 'LRT', 0, 'C');
        $this->Cell(17, 5, "MALL DEL", 'LRT', 0, 'C');
        $this->Cell(17, 5, "RIOCENTRO", 'LRT', 0, 'C');
        $this->Cell(17, 5, "SAN MARINO", 'LRT', 0, 'C');
        $this->Cell(17, 5, "SHOPPING", 'LRT', 0, 'C');
        $this->Cell(17, 5, "TOTALES", 'LRT', 0, 'C');

        $this->Ln();
        $this->Cell(28, 5, "", 'LRB', 0, 'C');
        $this->Cell(17, 5, "", 'LRB', 0, 'C');
        $this->Cell(17, 5, "", 'LRB', 0, 'C');
        $this->Cell(17, 5, "", 'LRB', 0, 'C');
        $this->Cell(17, 5, "", 'LRB', 0, 'C');
        $this->Cell(17, 5, "", 'LRB', 0, 'C');
        $this->Cell(17, 5, "SHOPPING", 'LRB', 0, 'C');
        $this->Cell(17, 5, "SUR", 'LRB', 0, 'C');
        $this->Cell(17, 5, "", 'LRB', 0, 'C');
        $this->Cell(17, 5, "", 'LRB', 0, 'C');
        $this->Cell(17, 5, "", 'LRB', 0, 'C');
        $this->Cell(17, 5, "SOL", 'LRB', 0, 'C');
        $this->Cell(17, 5, "", 'LRB', 0, 'C');
        $this->Cell(17, 5, "SHOPPING", 'LRB', 0, 'C');
        $this->Cell(17, 5, "MACHALA", 'LRB', 0, 'C');

        $this->Cell(17, 5, "", 'LRB', 0, 'C');

        $this->Ln();
    }

    function valores($desde, $hasta) {
        $Set = new Clase_rep_ventas();
        $this->SetFont('helvetica', 'b', 7);
        $this->Cell(28, 5, "REPORTE VENTAS", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "TOTAL VTAS. BRUTAS", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "DESCUENTO", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "SUBTOTAL CON IVA", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "SUBTOTAL SIN IVA", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "SUBTOTAL NETO", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "ICE", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "IVA", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "TOTAL", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "DEVOLUCIONES", 1, 0, 'l');
        $this->Ln();
//        $this->Cell(28, 5, "DESCUENTO", 1, 0, 'l');
//        $this->Ln();
        $this->Cell(28, 5, "SUBTOTAL CON IVA", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "SUBTOTAL SIN IVA", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "IVA", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "TOTAL", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "VENTA NETA", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "TOTAL PERIODO", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "IVA", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "TOTAL", 1, 0, 'l');
        $this->Ln();
        $this->SetFont('helvetica', '', 7);
        $n = 1;
        $j = 1;


        $x = 35;
        $tvb = 0;
        while ($n < 15) {
            $rst_emi = pg_fetch_array($Set->orden_emisor($n));
            $rst = pg_fetch_array($Set->lista_total_venta($rst_emi[cod_punto_emision], $desde, $hasta));
            $vb = $rst[sub12] + $rst[sub0] + $rst[subex] + $rst[subno] + $rst[des];
            $des = $rst[des];
            $sb12 = $rst[sub12];
            $sb0 = $rst[sub0] + $rst[subex] + $rst[subno];
            $sbn = $rst[sub12] + $sb0;
            $ice = $rst[ice];
            $iva = $rst[iva];
            $total = $rst[total];
            $this->SetFont('helvetica', '', 7);
            $this->SetXY($x, 47);
            $this->Cell(17, 5, '', 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 52);
            $this->Cell(17, 5, number_format($vb, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 57);
            $this->Cell(17, 5, number_format($des, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 62);
            $this->Cell(17, 5, number_format($sb12, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 67);
            $this->Cell(17, 5, number_format($sb0, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 72);
            $this->Cell(17, 5, number_format($sbn, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 77);
            $this->Cell(17, 5, number_format($ice, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 82);
            $this->Cell(17, 5, number_format($iva, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetFont('helvetica', 'b', 7);
            $this->SetXY($x, 87);
            $this->Cell(17, 5, number_format($total, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetFont('helvetica', '', 7);
            $this->SetXY($x, 92);
            $this->Cell(17, 5, '', 1, 0, 'R');
            $this->Ln();

            $rst_not = pg_fetch_array($Set->lista_total_devoluciones($rst_emi[cod_punto_emision], $desde, $hasta));
//            $nsbdes = $rst_not[ndes];
            $nsb12 = $rst_not[nsub12];
            $nsb0 = $rst_not[nsub0] + $rst_not[nsubex] + $rst_not[nsubno];
            $niva = $rst_not[niva];
            $ntot = $rst_not[ntotal];

            $periodo = $sbn - $nsb12 - $nsb0;
            $pi = $iva - $niva;
            $t = $periodo + $pi;
//            $this->SetXY($x, 97);
//            $this->Cell(17, 5, number_format($nsbdes, 4), 1, 0, 'R');
//            $this->Ln();
            $this->SetXY($x, 97);
            $this->Cell(17, 5, number_format($nsb12, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 102);
            $this->Cell(17, 5, number_format($nsb0, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 107);
            $this->Cell(17, 5, number_format($niva, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetFont('helvetica', 'b', 7);
            $this->SetXY($x, 112);
            $this->Cell(17, 5, number_format($ntot, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetFont('helvetica', '', 7);
            $this->SetXY($x, 117);
            $this->Cell(17, 5, '', 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 122);
            $this->Cell(17, 5, number_format($periodo, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 127);
            $this->Cell(17, 5, number_format($pi, 4), 1, 0, 'R');
            $this->Ln();
            $this->SetFont('helvetica', 'b', 7);
            $this->SetXY($x, 132);
            $this->Cell(17, 5, number_format($t, 4), 1, 0, 'R');
            $this->Ln();
            $n++;
            $x = $x + 17;

            $tvb = $tvb + $vb;
            $tde = $tde + $des;
            $ti12 = $ti12 + $sb12;
            $ti0 = $ti0 + $sb0;
            $tne = $tne + $sbn;
            $tic = $tic + $ice;
            $tiv = $tiv + $iva;
            $tt = $tt + $total;
//            $tnd = $tnd + $nsbdes;
            $td12 = $td12 + $nsb12;
            $td0 = $td0 + $nsb0;
            $tdi = $tdi + $niva;
            $td = $td + $ntot;
            $tpe = $tpe + $periodo;
            $tpi = $tpi + $pi;
            $tto = $tto + $t;
        }
        $this->SetFont('helvetica', 'b', 7);
        $this->SetXY(273, 47);
        $this->Cell(17, 5, "", 1, 0, 'l');
        $this->Ln();
        $this->SetXY(273, 52);
        $this->Cell(17, 5, number_format($tvb, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 57);
        $this->Cell(17, 5, number_format($tde, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 62);
        $this->Cell(17, 5, number_format($ti12, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 67);
        $this->Cell(17, 5, number_format($ti0, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 72);
        $this->Cell(17, 5, number_format($tne, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 77);
        $this->Cell(17, 5, number_format($tic, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 82);
        $this->Cell(17, 5, number_format($tiv, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 87);
        $this->Cell(17, 5, number_format($tt, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 92);
        $this->Cell(17, 5, '', 1, 0, 'R');
        $this->Ln();
//        $this->SetXY(273, 97);
//        $this->Cell(17, 5, number_format($tnd, 4), 1, 0, 'R');
//        $this->Ln();
        $this->SetXY(273, 97);
        $this->Cell(17, 5, number_format($td12, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 102);
        $this->Cell(17, 5, number_format($td0, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 107);
        $this->Cell(17, 5, number_format($tdi, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 112);
        $this->Cell(17, 5, number_format($td, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 117);
        $this->Cell(17, 5, '', 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 122);
        $this->Cell(17, 5, number_format($tpe, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 127);
        $this->Cell(17, 5, number_format($tpi, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 132);
        $this->Cell(17, 5, number_format($tto, 4), 1, 0, 'R');
        $this->Ln();
    }

    function cierre($desde, $hasta) {
        $Set = new Clase_rep_ventas();
//        $tot = pg_fetch_array($Set->suma_totales($as));
//        $this->Ln();
        $this->SetFont('helvetica', 'B', 7);
        $this->Ln();
        $this->Cell(17, 5, 'CIERRE DE CAJA ', '', 0, 'L');
        $this->Ln();
        $this->Cell(28, 5, "TARJETAS CREDITO", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "TARJETAS DEBITO", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "EFECTIVO", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "CHEQUE", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "NOTA CREDITO", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "RETENCIONES", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "BONOS", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "CERTIFICADOS", 1, 0, 'l');
        $this->Ln();
        $this->Cell(28, 5, "TOTAL", 1, 0, 'l');
        $this->Ln();

        $n = 1;
        $x = 35;
        while ($n < 15) {
            $rst_emi = pg_fetch_array($Set->orden_emisor($n));
            $rst_cierre = pg_fetch_array($Set->lista_total_cierre($rst_emi[cod_punto_emision], $desde, $hasta));
            if ($rst_emi[cod_punto_emision] == 1 || $rst_emi[cod_punto_emision] == 10) {
//                $rst_cierre[efectivo] = $rst_cierre[valor];
                $rst_cierre[credito] = '';
                $rst_cierre[debito] = '';
                $rst_cierre[efectivo] = '';
                $rst_cierre[cheque] = '';
                $rst_cierre[nota] = '';
                $rst_cierre[retencion] = '';
                $rst_cierre[bonos] = '';
                $rst_cierre[certificados] = '';
            }
            $this->SetFont('helvetica', '', 7);
            $this->SetXY($x, 147);
            $this->Cell(17, 5, number_format($rst_cierre[credito], 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 152);
            $this->Cell(17, 5, number_format($rst_cierre[debito], 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 157);
            $this->Cell(17, 5, number_format($rst_cierre[efectivo], 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 162);
            $this->Cell(17, 5, number_format($rst_cierre[cheque], 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 167);
            $this->Cell(17, 5, number_format($rst_cierre[nota], 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 172);
            $this->Cell(17, 5, number_format($rst_cierre[retencion], 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 177);
            $this->Cell(17, 5, number_format($rst_cierre[bonos], 4), 1, 0, 'R');
            $this->Ln();
            $this->SetXY($x, 182);
            $this->Cell(17, 5, number_format($rst_cierre[certificados], 4), 1, 0, 'R');
            $this->Ln();
            $this->SetFont('helvetica', 'b', 7);
            $tc = $rst_cierre[credito] + $rst_cierre[debito] + $rst_cierre[efectivo] + $rst_cierre[cheque] + $rst_cierre[nota] + $rst_cierre[retencion] + $rst_cierre[bonos] + $rst_cierre[certificados];
            $this->SetXY($x, 187);
            if ($rst_emi[cod_punto_emision] == 1 || $rst_emi[cod_punto_emision] == 10) {
                $tc = '';
            }
            $this->Cell(17, 5, number_format($tc, 4), 1, 0, 'R');
            $this->Ln();
            $n++;
            $x = $x + 17;

            $ttc = $ttc + $rst_cierre[credito];
            $ttd = $ttd + $rst_cierre[debito];
            $tef = $tef + $rst_cierre[efectivo];
            $tch = $tch + $rst_cierre[cheque];
            $tnc = $tnc + $rst_cierre[nota];
            $tre = $tre + $rst_cierre[retencion];
            $tbo = $tbo + $rst_cierre[bonos];
            $tce = $tce + $rst_cierre[certificados];
            $tci = $tci + $tc;
        }


        $this->SetFont('helvetica', 'b', 7);
        $this->SetXY(273, 147);
        $this->Cell(17, 5, number_format($ttc, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 152);
        $this->Cell(17, 5, number_format($ttd, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 157);
        $this->Cell(17, 5, number_format($tef, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 162);
        $this->Cell(17, 5, number_format($tch, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 167);
        $this->Cell(17, 5, number_format($tnc, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 172);
        $this->Cell(17, 5, number_format($tre, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 177);
        $this->Cell(17, 5, number_format($tbo, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 182);
        $this->Cell(17, 5, number_format($tce, 4), 1, 0, 'R');
        $this->Ln();
        $this->SetXY(273, 187);
        $this->Cell(17, 5, number_format($tci, 4), 1, 0, 'R');
        $this->Ln();
    }

}

$pdf = new PDF($orientation = 'L', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->encabezado($emisor, $desde, $hasta, $f1, $f2);
//$pdf->Ln();
$pdf->encabezado_tab();
$pdf->valores($desde, $hasta);
$pdf->cierre($desde, $hasta);
//}
$pdf->Output();
