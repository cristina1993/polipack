<?php

include_once '../Clases/clsClase_asientos.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_asientos();

$id = $_GET[id];
$cns = $Set->listar_asiento_agrupado($id);
$rst1 = pg_fetch_array($Set->listar_asiento_numero($id));
$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));

class PDF extends FPDF {

    function encabezado($emisor, $id) {
        $Set = new Clase_asientos();
        $rst1 = pg_fetch_array($Set->listar_asiento_numero($id));
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(200, 15, "REPORTE LIBRO DIARIO", 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(85, 5, "PERIODO: " . $rst1[con_fecha_emision], 0, 0, 'L');
        $this->Ln();
        $this->Cell(85, 5, "RUC: " . $emisor[identificacion], 0, 0, 'L');
        $this->Ln();
        $this->Cell(85, 5, "RAZON SOCIAL: NOPERTI CIA LTDA", 0, 0, 'L');
        $this->Ln();
        $this->Cell(85, 5, "MONEDA: DOLAR", 0, 0, 'L');
        $this->Ln();
    }

    function encabezado_tab($as) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(85, 5, "ASIENTO No.: " . $as, 0, 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(10, 5, "No", 'TB', 0, 'C');
        $this->Cell(18, 5, "F. EMISION", 'TB', 0, 'C');
        $this->Cell(25, 5, "CODIGO", 'TB', 0, 'C');
        $this->Cell(45, 5, "CUENTA", 'TB', 0, 'C');
        $this->Cell(30, 5, "DOCUMENTO", 'TB', 0, 'C');
        $this->Cell(40, 5, "CONCEPTO", 'TB', 0, 'C');
        $this->Cell(17, 5, "DEBE", 'TB', 0, 'C');
        $this->Cell(17, 5, "HABER", 'TB', 0, 'C');
        $this->Ln();
    }

    function asientos($as, $emisor, $desde, $hasta) {
        $Set = new Clase_asientos();
        $this->SetFont('helvetica', '', 8);
        $cns_cuentas = $Set->lista_cuentas_asientos($as);
        $cuentas = Array();
        while ($rst_cuentas = pg_fetch_array($cns_cuentas)) {
            if (!empty($rst_cuentas[con_concepto_debe])) {
                array_push($cuentas, $rst_cuentas[con_concepto_debe] . '&' . $rst_cuentas[con_id]);
            }

            if (!empty($rst_cuentas[con_concepto_haber])) {
                array_push($cuentas, $rst_cuentas[con_concepto_haber] . '&' . $rst_cuentas[con_id]);
            }
        }

        //Eliminar Duplicados del Array
        $n = 0;
        $j = 1;
        while ($n < count($cuentas)) {
            $cta = explode('&', $cuentas[$n]);
            $rst_cuentas1 = pg_fetch_array($Set->listar_descripcion_asiento($cta[0]));

            $rst_v = pg_fetch_array($Set->listar_debe_haber_asiento_cuenta1($as, $cta[0],$cta[1]));

            $this->Cell(10, 5, $j, 0, 0, 'L');
            $this->Cell(18, 5, $rst_v[fecha], 0, 0, 'L');
            $this->Cell(25, 5, $rst_cuentas1[pln_codigo], 0, 0, 'L');
            $this->Cell(45, 5, substr(strtoupper($rst_cuentas1[pln_descripcion]), 0, 24), 0, 0, 'L');
            $this->Cell(30, 5, $rst_v[documento], 0, 0, 'L');
            $this->Cell(40, 5, substr($rst_v[concepto], 0, 24), 0, 0, 'L');
            $this->Cell(17, 5, number_format($rst_v[debe], 2), 0, 0, 'R');
            $this->Cell(17, 5, number_format($rst_v[haber], 2), 0, 0, 'R');
            $this->Ln();
            $n++;
            $j++;
        }
    }

    function totales($as) {
        $Set = new Clase_asientos();
        $tot = pg_fetch_array($Set->suma_totales($as));
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(160, 5, 'TOTAL ', '', 0, 'R');
        $this->Cell(25, 5, number_format($tot[debe], 2), '', 0, 'R');
        $this->Cell(18, 5, number_format($tot[haber], 2), '', 0, 'R');
        $this->Ln();
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->encabezado($emisor, $id);
$pdf->Ln();

while ($rst = pg_fetch_array($cns)) {
    $pdf->encabezado_tab($rst[con_asiento]);
    $pdf->asientos($rst[con_asiento], $emisor, $desde, $hasta);
    $pdf->Cell(202, 7, '', 'T', 0, 'L');
    $pdf->Ln();
    $pdf->totales($rst[con_asiento]);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Ln();
}
$pdf->Output();
