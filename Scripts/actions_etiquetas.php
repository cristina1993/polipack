<?php

include_once '../Clases/clsClase_etiquetas.php';
include_once("../Clases/clsAuditoria.php");
$Set = new Clase_etiquetas();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            if ($Set->insert_etiquetas($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'ETIQUETAS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($Set->upd_etiquetas($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'ETIQUETAS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Set->delete_etiqueta($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $id;
            $modulo = 'ETIQUETAS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 2:
        $rst = pg_fetch_array($Set->lista_una_etiqueta($id));
        $dt = explode('&', $rst[eti_elementos]);
        $elementos = $dt[0] + $dt[1] + $dt[2] + $dt[3] + $dt[4] + $dt[5] + $dt[6];
        echo $rst[eti_tamano] . '&&' . $elementos;
        break;
    case 3:
        $sms = 0;
        if (empty($id)) {
            if ($Set->insert_asignacion($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'ASIGNACION ETIQUETAS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($Set->upd_asignacion($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'ASIGNACION ETIQUETAS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms.'&'.$data[1];
        break;
}
?>
