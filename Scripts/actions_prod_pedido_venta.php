<?php

include_once '../Clases/clsClase_prod_pedido_venta.php';
include_once("../Clases/clsAuditoria.php");
$Adt = new Auditoria();
$Reg = new Clase_prod_pedido_venta();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$detalle = $_REQUEST[detalle];
$pagos = $_REQUEST[pagos];
$id = $_REQUEST[id];
$s = $_REQUEST[s];
$sts = $_REQUEST[sts];
$emisor = $_REQUEST[emi];
$fields = $_REQUEST[fields];
switch ($op) {

    case 0:
        $sms = 0;

        if ($Reg->lista_cambia_status_det($data, $sts) == false) {
            $sms = pg_last_error();
        } else {
            $ped = pg_fetch_array($Reg->lista_detalle($id));
            if (empty($ped)) {
                if ($Reg->lista_cambia_status($id, $sts) == false) {
                    $sms = pg_last_error();
                }
            }
        }
        echo $sms;
        break;

//    case 100:
//        if ($Reg->insert_asiento_mp($_REQUEST[sbt_mp]) == false) {
//            $sms = 'Asiento ' . pg_last_error();
//        }
//        //echo $sms;       
//        break;
}
?>
