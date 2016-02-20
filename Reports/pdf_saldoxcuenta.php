<?php

include_once '../Clases/clsClase_cuentasxcobrar.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new CuentasCobrar();
$fec1 = $_GET[d];
$fec2 = $_GET[h];

$nm = trim(strtoupper($_GET[txt]));
$estado = $_GET[e];

$txt = "and (f.fac_numero LIKE '%$nm%' or f.fac_nombre like '%$nm%' or f.fac_identificacion like '%$nm%') and f.fac_fecha_emision between '$fec1' and '$fec2' and exists (select * from erp_pagos_factura p where p.com_id=cast(f.fac_id as varchar))";
$cns = $Set->lista_documentos_ctas($txt);
$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));

class PDF extends FPDF {

    function encabezado($emisor, $fec1, $fec2) {
        $this->Image('../img/logo_noperti.jpg', 1, 5, 50);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(200, 5, "CUENTAS POR COBRAR", 0, 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, "SALDO POR CUENTAS", 0, 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, "NOPERTI CIA. LTDA.", 0, 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, "RUC: " . $emisor[identificacion], 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(200, 5, "PERIODO  DE: " . $fec1 . '  AL ' . $fec2, 0, 0, 'C');
        $this->Ln();
        $this->Ln();
    }

    function encabezado_tab() {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, "COD. CONTABLE", 'TB', 0, 'C');
        $this->Cell(70, 5, "CLIENTE", 'TB', 0, 'L');
        $this->Cell(25, 5, "DEBE", 'TB', 0, 'C');
        $this->Cell(25, 5, "HABER", 'TB', 0, 'C');
        $this->Cell(25, 5, "S. DEUDOR", 'TB', 0, 'C');
        $this->Cell(25, 5, "S. ACREEDOR", 'TB', 0, 'C');
        $this->Ln();
    }

    function doc($cns) {
        $Set = new CuentasCobrar();
        $debito = 0;
        $credito = 0;
        $deudor = 0;
        $acreedor = 0;
        $n = 0;
        while ($rst_doc = pg_fetch_array($cns)) {
            if (!empty($rst_doc[pln_id])) {
                $rst_pln = pg_fetch_array($Set->listar_una_cuenta_id($rst_doc[pln_id]));
                $cod = $rst_pln[pln_codigo];
            }
            if ($grup != $rst_doc[fac_identificacion]) {
                $res = pg_fetch_array($Set->suma_documentos_cliente($rst_doc[fac_identificacion]));
                $haber = $res[ctas_credito]; // suma pagos cta
                $debe = $res[val_factura] + $res[val_fact_ctas]+$res[ctas_debito];
                if ($debe > $haber) {
                    $deudor = $debe - $haber;
                } else {
                    $acreedor = $debe - $haber;
                }
                $this->SetFont('Arial', '', 8);
                $this->Cell(30, 5, $cod, 0, 0, 'L');
                $this->Cell(70, 5, utf8_decode(substr($rst_doc[fac_nombre], 0, 45)), 0, 0, 'L');
                $this->Cell(25, 5, number_format($debe, 4), 0, 0, 'R');
                $this->Cell(25, 5, number_format($haber, 4), 0, 0, 'R');
                $this->Cell(25, 5, number_format($deudor, 4), 0, 0, 'R');
                $this->Cell(25, 5, number_format($acreedor, 4), 0, 0, 'R');
                $this->Ln();
                $tdeb+=$debe;
                $thab+=$haber;
                $tdeu+=$deudor;
                $tacr+=$acreedor;
                $debe = 0;
                $haber = 0;
                $deudor = 0;
                $acreedor = 0;
                $grup = $rst_doc[fac_identificacion];
                $grup2 = $cod;
            }
            $cod = '';
            $rst_doc[pln_id] = '';
        }
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(60, 5, '', '', 0, 'R');
        $this->Cell(40, 5, 'TOTAL ', '', 0, 'R');
        $this->Cell(25, 5, number_format($tdeb, 4), 'T', 0, 'R');
        $this->Cell(25, 5, number_format($thab, 4), 'T', 0, 'R');
        $this->Cell(25, 5, number_format($tdeu, 4), 'T', 0, 'R');
        $this->Cell(25, 5, number_format($tacr, 4), 'T', 0, 'R');
        $this->Ln();
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->encabezado($emisor, $fec1, $fec2);
$pdf->encabezado_tab();
$pdf->doc($cns);
$pdf->Ln();
$pdf->Output();



