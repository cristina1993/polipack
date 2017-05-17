<?php

include_once '../Clases/clsClase_resultados_operaciones.php';
include_once("../Clases/clsAuditoria.php");
$Set = new Clase_resultados_operaciones();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            if ($Set->insert_resultados($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $i = 0;
                while ($i < count($data2)) {
                    $rst_resultado = pg_fetch_array($Set->lista_unresultado_operacion($data[27]));
                    $dt = explode('&', $data2[$i]);
                    $detalle = Array(
                        $rst_resultado[rop_id],
                        $dt[0],
                        $dt[1],
                        $dt[2]
                    );
                    if ($Set->insert_detalle($detalle) == false) {
                        $sms = 'insert detalle' . pg_last_error();
                    }
                    $i++;
                }
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'RESULTADOS DE OPERACION';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($Set->update_resultados($id, $data) == FALSE) {
                $sms = pg_last_error();
            } else {
                if ($Set->delete_detalle($id) == FALSE) {
                    $sms = pg_last_error();
                } else {
                    $i = 0;
                    while ($i < count($data2)) {
                        $rst_resultado = pg_fetch_array($Set->lista_unresultado_operacion($data[27]));
                        $dt = explode('&', $data2[$i]);
                        $detalle = Array(
                            $rst_resultado[rop_id],
                            $dt[0],
                            $dt[1],
                            $dt[2]
                        );
                        if ($Set->insert_detalle($detalle) == false) {
                            $sms = 'insert detalle' . pg_last_error();
                        }
                        $i++;
                    }

                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'RESULTADOS DE OPERACION';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Set->delete_detalle($id) == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_resultados($id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                $f = $id;
                $modulo = 'RESULTADOS DE OPERACION';
                $accion = 'ELIMINAR';
                if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
}
?>
