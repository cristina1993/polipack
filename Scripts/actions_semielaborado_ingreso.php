<?php

//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_semielaborado_ingreso.php'; // cambiar clsClase_productos
$Set = new Clase_semielaborado_ingreso();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields]; //Datos para auditoria
$x = $_REQUEST[x];
$s = $_REQUEST[s];
$emp = $_REQUEST[emp];
switch ($op) {

    case 15:
        $rst = pg_fetch_array($Set->lista_secuencial_transferencia());
        $sec = (substr($rst[sec_transferencias], -5) + 1);
        if ($sec >= 0 && $sec < 10) {
            $txt = '000000000';
        } else if ($sec >= 10 && $sec < 100) {
            $txt = '00000000';
        } else if ($sec >= 100 && $sec < 1000) {
            $txt = '0000000';
        } else if ($sec >= 1000 && $sec < 10000) {
            $txt = '000000';
        } else if ($sec >= 10000 && $sec < 100000) {
            $txt = '00000';
        } else if ($sec >= 100000 && $sec < 1000000) {
            $txt = '0000';
        } else if ($sec >= 1000000 && $sec < 10000000) {
            $txt = '000';
        } else if ($sec >= 10000000 && $sec < 100000000) {
            $txt = '00';
        } else if ($sec >= 100000000 && $sec < 1000000000) {
            $txt = '0';
        } else if ($sec >= 1000000000 && $sec < 10000000000) {
            $txt = '';
        }
        $retorno = $txt . $sec;
        echo $retorno;
        break;

    case 16:
        $sms = 0;
        $sec = $_REQUEST[sec];
        if ($Set->insert_sec_transferencia($sec) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 12:
        $sms = 0;
        $data = $_REQUEST[data];
        $n = 0;
        while ($n < count($data)) {
            $dat = explode('&', $data[$n]);
            if (!$Set->insert_transferencia($dat)) {
                $sms = pg_last_error();
            }
            $n++;
        }
        if ($sms == 0) {
            $j = 0;
            while ($j < count($fields)) {
                $f = $f . strtoupper($fields[$j] . '&');
                $j++;
            }

            $modulo = 'INGRESO PRODUCTO SEMIELABORADO';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $dat[4]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 64:
        $rst = pg_fetch_array($Set->lista_un_producto($id));
        if ($rst[pro_id] != '') {
            echo $rst[pro_id] . '&' . $rst[pro_codigo] . '&' . $rst[pro_descripcion] . '&' . $rst[pro_uni];
        }
        break;
}
?>

