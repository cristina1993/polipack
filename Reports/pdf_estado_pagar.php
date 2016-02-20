<?php

include_once '../Clases/clsClase_cuentasxpagar.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new CuentasPagar();
$desde = $_GET[d];
$hasta = $_GET[h];
$cli = $_GET[cli];
$fec1 = str_replace('-', '', $desde);
$fec2 = str_replace('-', '', $hasta);
$estado = $_GET[e];

$txt = "WHERE c.reg_ruc_cliente =cl.cli_ced_ruc and c.reg_ruc_cliente='$cli' and exists (select * from erp_pagos_documentos p where p.reg_id=c.reg_id) ";

//$txt = "WHERE tipo_comprobante=1 and (cod_punto_emision=1 or cod_punto_emision=10) and identificacion ='$cli' and exists (select * from erp_pagos_factura p where p.com_id=c.num_documento)";
$cns = $Set->lista_estado_cuenta_cliente($cli, $desde, $hasta);
$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));
$cliente = pg_fetch_array($Set->lista_cliente_ced($cli));
$res = pg_fetch_array($Set->saldo_anterior($cli, $desde));
$ant = $res[debito] - $res[credito];

class PDF extends FPDF {

    function encabezado($emisor, $desde, $hasta, $cl, $doc) {
        $Set = new CuentasPagar();
        $cod = '';
        $rst_cta = pg_fetch_array($Set->listar_una_ctapagar_comid($doc));
        if ($rst_cta[pln_id] != '') {
            $rst_pln = pg_fetch_array($Set->listar_una_cuenta_id($rst_cta[pln_id]));
            $cod = $rst_pln[pln_codigo];
        }
        $this->Image('../img/logo_noperti.jpg', 1, 5, 50);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(270, 5, "CUENTAS POR PAGAR", 0, 0, 'C');
        $this->Ln();
        $this->Cell(270, 5, "ESTADO DE CUENTAS PROVEEDORES", 0, 0, 'C');
        $this->Ln();
        $this->Cell(270, 5, "NOPERTI CIA. LTDA.", 0, 0, 'C');
        $this->Ln();
        $this->Cell(270, 5, "RUC: " . $emisor[identificacion], 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(270, 5, "PERIODO  DE: " . $desde . " AL " . $hasta, 0, 0, 'C');
        $this->Ln();
        $this->Ln();
        $this->Cell(150, 5, "CODIGO PROVEEDOR: " . $cl[cli_codigo], 0, 0, 'L');
        $this->Cell(150, 5, "RUC: " . $cl[cli_ced_ruc], 0, 0, 'L');
        $this->Ln();
        $this->Cell(150, 5, "CLIENTE: " . $cl[cli_raz_social], 0, 0, 'L');
        $this->Cell(150, 5, "DIRECCION: " . $cl[cli_calle_prin], 0, 0, 'L');
        $this->Ln();
        $this->Cell(150, 5, "CODIGO CONTABLE: " . $cod, 0, 0, 'L');
        $this->Cell(150, 5, "TELEFONO: " . $cl[cli_telefono], 0, 0, 'L');
        $this->Ln();
    }

    function encabezado_tab() {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, "FECHA", 'TB', 0, 'L');
        $this->Cell(30, 5, "DOCUMENTO", 'TB', 0, 'C');
        $this->Cell(40, 5, "CONCEPTO", 'TB', 0, 'C');
        $this->Cell(30, 5, "F.PAGO", 'TB', 0, 'C');
        $this->Cell(33, 5, "DEBE", 'TB', 0, 'C');
        $this->Cell(33, 5, "HABER", 'TB', 0, 'C');
        $this->Cell(33, 5, "SALDO", 'TB', 0, 'C');
        $this->Cell(35, 5, "SALDO VENCIDO", 'TB', 0, 'C');
    }

    function body($cns, $ant) {
        $Set = new CuentasPagar();
        $rst_dec = pg_fetch_array($Set->lista_configuraciones_gen('6'));
        $dec=$rst_dec[con_ambiente];
        $this->SetFont('Arial', '', 8);
        $this->Cell(25, 5, '', 0, 0, 'L');
        $this->Cell(30, 5, $rst_doc[reg_num_documento], 0, 0, 'L');
        $this->Cell(40, 5, substr('SALDO ANTERIOR', 0, 30), 0, 0, 'L');
        $this->Cell(30, 5, substr('', 0, 30), 0, 0, 'L');
        $this->Cell(30, 5, number_format(0, $dec), 0, 0, 'R');
        $this->Cell(30, 5, number_format(0, $dec), 0, 0, 'R');
        $this->Cell(30, 5, number_format($ant, $dec), 0, 0, 'R');
        $this->Cell(35, 5, number_format(0, $dec), 0, 0, 'R');
        $this->Ln();
        $saldo1 = 0;
        $n = 0;
        while ($rst = pg_fetch_array($cns)) {
            $n++;
            $ast = '';

            $debito1 = $rst[debe];
            $credito1 = $rst[reg_total];
            if ($n == 1) {
                $saldo1 = $ant + $debito1 - $credito1;
            } else {
                $saldo1 = $saldo1 + $debito1 - $credito1;
            }
            if ($rst[concepto] == 'FACTURA COMPRA') {
                $Set = new CuentasPagar();
                $res = pg_fetch_array($Set->suma_pagos1($rst[reg_id])); //////cambio
                $mto = $res[monto]; // suma pagos cta
                $pgo = $res[pago] + $res[debito];
                $sal = $pgo - $mto;
                $cns_pag1 = $Set->lista_pagos_regfac($rst[reg_id]);
                while ($rst_pag1 = pg_fetch_array($cns_pag1)) {
                    $fvencimiento = $rst_pag1[pag_fecha_v];
                }
                if ($fvencimiento < date('Y-m-d')) {
                    $vencido1 = '-' . $sal;
                } else {
                    $vencido1 = 0;
                }
            } else {
                $vencido1 = 0;
            }
            $credito1 = $rst[reg_total];
            $emi = $rst[reg_femision];
            $this->SetFont('Arial', '', 8);
            $this->Cell(25, 5, $emi, 0, 0, 'L');
            $this->Cell(30, 5, $rst[reg_num_documento], 0, 0, 'L');
            $this->SetFont('Arial', '', 7);
            $this->Cell(40, 5, substr($rst[concepto], 0, 25), 0, 0, 'L');
            $this->SetFont('Arial', '', 8);
            $this->Cell(30, 5, substr($rst[forma], 0, 30), 0, 0, 'L');
            $this->Cell(30, 5, number_format($debito1, $dec), 0, 0, 'R');
            $this->Cell(30, 5, number_format($credito1, $dec), 0, 0, 'R');
            $this->Cell(30, 5, number_format($saldo1, $dec), 0, 0, 'R');
            $this->Cell(35, 5, number_format($vencido1, $dec), 0, 0, 'R');
            $this->Ln();
            $tdeb+=round($debito1,$dec);
            $tcre+=round($credito1,$dec);
            $tvc+=round($vencido1,$dec);
        }
//        $tcre = $tcre + $ant;
        $tsal = $tdeb - $tcre + $ant;

        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(90, 5, '', '', 0, 'R');
        $this->Cell(35, 5, 'TOTAL ', '', 0, 'R');
        $this->Cell(30, 5, number_format($tdeb, $dec), 'T', 0, 'R');
        $this->Cell(30, 5, number_format($tcre, $dec), 'T', 0, 'R');
        $this->Cell(30, 5, number_format($tsal, $dec), 'T', 0, 'R');
        $this->Cell(35, 5, number_format($tvc, $dec), 'T', 0, 'R');
        $this->Ln();
    }

}

$pdf = new PDF($orientation = 'L', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$doc = pg_fetch_array($Set->lista_documentos_buscador($txt));
$com = $doc[reg_id];
$pdf->encabezado($emisor, $desde, $hasta, $cliente, $com);
$pdf->encabezado_tab();
$pdf->Ln();
$grup = '';
$pdf->body($cns, $ant);
$pdf->Output();



