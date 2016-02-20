<?php

//include_once '../Clases/clsClase_guia_remision.php';

require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
//$Set = new Clase_guia_remision();
//$id= $_GET[id];
////$cns = $Set->lista_una_guia($id);
//$rst = pg_fetch_array($Set->lista_una_guia($id));
////echo 'pg_fetch_array($Set->lista_una_guia('.$id.'))';
//$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));

class PDF extends FPDF {

    function guia_remision($rst, $emisor) {
        $x = 0;
        $y = 0;
        $this->SetFont('helvetica', 'B', 14);
        $this->Text($x + 22, $y + 20, $emisor[nombre_comercial]);
        $this->Text($x + 100, $y + 20, "COMPROBANTE DE RETENCION " . $emisor[identificacion]);
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 25, $y + 23, "CONTRIBUYENTE ESPECIAL");
        $this->SetFont('helvetica', '', 14);
        $this->Text($x + 100, $y + 28, "RUC: 1790007871001");
        $this->SetFont('helvetica', '', 8);
        $this->Text($x + 21, $y + 26, "RESOLUCION No. 636 del 29/12/2014");
        $this->Text($x + 150, $y + 28, "SERIE 001 - 001");
        $this->Text($x + 36, $y + 29, "DIRECCION");
        $this->Text($x + 15, $y + 32, $emisor[dir_establecimiento_matriz]);
        $this->Text($x + 15, $y + 35, "TELEFONO:(593-2) 2445-145 FAX: (593-2) 2459-890");
        $this->SetFont('helvetica', '', 14);
        $this->Text($x + 100, $y + 38, "NO." . $rst[num_comprobante]);
        $this->SetFont('helvetica', '', 12);
        $this->Text($x + 100, $y + 48, "AUT.SRI: 1115893636");
        $this->SetFont('helvetica', '', 8);
        $this->Text($x + 100, $y + 53, "FECHA DE AUTORIZACION:12-DICIEMBRE-2015 ");
        $this->Text($x + 15, $y + 58, "_____________________________________________________________________________________________________________");
        $this->Text($x + 15, $y + 63, "FECHA INICIO TRASLADO: " . $rst[fecha_inicio_transporte]);
        $this->Text($x + 100, $y + 63, "FECHA TERMINACION TRASLADO: " . $rst[fecha_fin_transporte]);
        $this->Text($x + 15, $y + 68, "_____________________________________________________________________________________________________________");
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 15, $y + 73, "DATOS COMPROBANTE DE VENTA: " . $rst[fecha_fin_transporte]);
        $this->SetFont('helvetica', '', 8);
        $this->Text($x + 15, $y + 78, "TIPO: " . $rst[tipo_comprobante]);
        $this->Text($x + 15, $y + 83, "NO. AUTORIZACION: " . $rst[num_autorizacion]);
        $this->Text($x + 100, $y + 83, "NO. DEL COMPROBANTE: " . $rst[num_comprobante_venta]);
        $this->Text($x + 15, $y + 68, "_____________________________________________________________________________________________________________");
        $this->Text($x + 15, $y + 93, "NO. DECLARACION ADUANERA: " . $rst[documento_aduanero]);
        $this->Text($x + 15, $y + 98, "MOTIVO TRASLADO: " . $rst[motivo_traslado]);
        $this->Text($x + 15, $y + 103, "PUNTO DE PARTIDA: " . $rst[punto_partida]);
        $this->Text($x + 100, $y + 103, "DESTINO(PUNTO DE LLEGADA): " . $rst[destino]);
        $this->Text($x + 15, $y + 108, "_____________________________________________________________________________________________________________");
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 27, $y + 113, "IDENTIFICACION DEL DESTINATARIO");
        $this->Text($x + 117, $y + 113, "IDENTIFICACION DEL TRANSPORTISTA");
        $this->SetFont('helvetica', '', 8);
        $this->Text($x + 15, $y + 118, "R.U.C/C.I: " . $rst[identificacion_destinario]);
        $this->Text($x + 100, $y + 118, "R.U.C/C.I: " . $rst[identificacion]);
        $this->Text($x + 15, $y + 123, "RAZON SOCIAL: " . $rst[nombre_destinario]);
        $this->Text($x + 100, $y + 123, "RAZON SOCIAL: " . $rst[razon_social]);
        $this->Text($x + 100, $y + 128, "PLACA: " . $rst[razon_social]);
        $this->Text($x + 15, $y + 133, "IDENTIFICACION DEL REMITENTE: " . $rst[identificacion_remitente]);
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->guia_remision($rst, $emisor);
$pdf->SetDisplayMode(100);
$pdf->Output();



