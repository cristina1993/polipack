<?php

include_once '../Clases/clsClase_asientos.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_asientos();
$desde = $_GET[desde];
$hasta = $_GET[hasta];
$cta = $_GET[cuenta];
$dc=2;
if (!empty($cta)) {
    $txt1 = " and con_concepto_debe='$cta'";
    $txt2 = " and con_concepto_haber='$cta'";
} else {
    $txt1 = "";
    $txt2 = "";
}
$cns = $Set->lista_cuentas_fecha($desde, $hasta, $txt1, $txt2);

$cuentas0 = pg_fetch_all_columns($cns);
$cuentas = array_unique($cuentas0);
$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));

class PDF extends FPDF {

    function encabezado($emisor, $desde, $hasta) {
        $this->Image('../img/logo_noperti.jpg', 1, 5, 50);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(200, 5, "LIBRO MAYOR - GENERAL", 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(200, 5, "NOPERTI CIA. LTDA.", 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(200, 5, $emisor[identificacion], 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(200, 5, "PERIODO " . $desde . '  AL ' . $hasta, 0, 0, 'C');
        $this->Ln();
    }

    function encabezado_tab($cuenta) {
        $Set = new Clase_asientos();
        $rst_cuenta = pg_fetch_array($Set->lista_un_plan_cuenta($cuenta));
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(85, 5, "CODIGO: " . $cuenta, 0, 0, 'L');
        $this->Ln();
        $this->Cell(85, 5, "CUENTA: " . strtoupper($rst_cuenta[pln_descripcion]), 0, 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 5, "F. EMISION", 'TB', 0, 'C');
        $this->Cell(25, 5, "ASIENTO No", 'TB', 0, 'C');
        $this->Cell(40, 5, "TIPO", 'TB', 0, 'C');
        $this->Cell(50, 5, "CONCEPTO", 'TB', 0, 'C');
        $this->Cell(20, 5, "DEBE", 'TB', 0, 'R');
        $this->Cell(20, 5, "HABER", 'TB', 0, 'R');
        $this->Cell(20, 5, "SALDO", 'TB', 0, 'R');
        $this->Ln();
    }

    function asientos($emisor, $cuenta, $desde, $hasta,$dc) {
        $Set = new Clase_asientos();
        $cns_as = $Set->lista_asientos_cuenta_fecha($cuenta, $desde, $hasta);
        $this->encabezado_tab($cuenta, $desde, $hasta);
        while ($rst_as = pg_fetch_array($cns_as)) {
            $rst_cuenta = pg_fetch_array($Set->lista_un_plan_cuenta($cuenta));

            if ($rst_as[tipo] == 'Debe') {
                $debe = $rst_as[con_valor_debe];
                $haber = '';
            } else {
                $debe = '';
                $haber = $rst_as[con_valor_haber];
            }
            $this->SetFont('helvetica', '', 8);
            $this->Cell(18, 5, $rst_as[con_fecha_emision], '', 0, 'C');
            $this->Cell(25, 5, $rst_as[con_asiento], '', 0, 'C');
            $this->SetFont('Arial', '', 7);
            $this->Cell(40, 5, substr($rst_as[con_tipo], 0, 30), '', 0, 'L');
            $this->Cell(50, 5, substr($rst_as[con_concepto], 0, 30), '', 0, 'L');
            $this->SetFont('Arial', '', 8);
            $this->Cell(20, 5, number_format($debe, $dc), '', 0, 'R');
            $valor_d = $debe;
            $total_d = $total_d + $valor_d;
            $this->Cell(20, 5, number_format($haber, $dc), '', 0, 'R');
            $valor_h = $haber;
            $total_h = $total_h + $valor_h;
            $total_v = $total_d - $valor_h;

            //----------------------------
            if ($rst_as[tipo] == 'Debe') {
                $total_vv = $total_d - $valor_h;
            }
//            else {
//                $total_vv = ($total_v - $valor_h) + $valor_d;
//            }
            if ($rst_as[tipo] == 'Haber') {
                $total_vv = $valor_d - $valor_h;
            }
            $this->Cell(20, 5, number_format($total_vv, $dc), '', 0, 'R');
            $this->Ln();
        }
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(83, 5, "", 'T', 'C');
        $this->Cell(50, 5, "TOTAL", 'T', 0, 'L');
        $tot_saldo = $total_d - $total_h;
        $this->SetFont('helvetica', '', 8);
        $this->Cell(20, 5, number_format($total_d, $dc), 'T', 0, 'R');
        $this->Cell(20, 5, number_format($total_h, $dc), 'T', 0, 'R');
        $this->Cell(20, 5, number_format($tot_saldo, $dc), 'T', 0, 'R');
        $this->Ln();
    }

    function pie_pagina() {
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Cell(20, 5, '', '');
        $this->Cell(40, 5, 'PREPARADO', 'T', 0, 'C');
        $this->Cell(20, 5, '', '');
        $this->Cell(40, 5, 'REVISADO', 'T', 0, 'C');
        $this->Cell(20, 5, '', '');
        $this->Cell(40, 5, 'AUTORIZADO', 'T', 0, 'C');
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->encabezado($emisor, $desde, $hasta);

$n = 0;
while ($n < count($cuentas)) {
    $pdf->asientos($emisor, $cuentas[$n], $desde, $hasta,$dc);
    $n++;
}
$pdf->pie_pagina();
$pdf->Output();



