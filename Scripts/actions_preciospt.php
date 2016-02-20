<?php

//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_preciospt.php';
include_once("../Clases/clsAuditoria.php");
$Adt = new Auditoria();
$Clase_preciospt = new Clase_preciospt();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
$tab = $_REQUEST[tab];
switch ($op) {
    case 0:
        if (!empty($id)) {
            $sms = 0;
            if ($Clase_preciospt->upd_precios($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'PRECIOS PT';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $fields[4]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms . '&' . $data[0] . '&' . $data[1] . '&' . $data[2];
        break;

    case 1:

        if (!empty($id)) {

            $sms = 0;
            $pre_id = pg_fetch_array($Clase_preciospt->ultimo_precios());
            $pre = $pre_id[pre_id] + 1;
            if ($Clase_preciospt->insert_precios($id, $pre, $tab) == false) {
                $sms = pg_last_error();
            }
//            else {
//                $fields = str_replace("&", ",", $fields[0]);
//                $modulo = 'PRECIOS PT';
//                $accion = 'UPDATE';
//                if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
//                    $sms = "Auditoria" . pg_last_error();
//                }
//            }
        }
        echo $sms;
        break;

    case 2:
//        echo $id;
        $sms = 0;
        if (strlen($id) != 0) {
            if ($Clase_preciospt->upd_precios_todos($id) == false) {
                $sms = pg_last_error();
            }
//            else {
//                $fields = str_replace("&", ",", $fields[0]);
//                $modulo = 'PRECIOS PT';
//                $accion = 'UPDATE';
//                if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
//                    $sms = "Auditoria" . pg_last_error();
//                }
//            }
        }
        echo $sms;
        break;


    case 3:
        $sms = 0;
        if ($tab == 1) {
            $cns_com = pg_fetch_array($Clase_preciospt->ultimo_producto_comercial());
            $id = $cns_com[id];
        } else {
            $cns_ind = pg_fetch_array($Clase_preciospt->ultimo_producto_industrial());
            $id = $cns_ind[pro_id];
        }

        $pre_id = pg_fetch_array($Clase_preciospt->ultimo_precios());
        $pre = $pre_id[pre_id] + 1;
        if ($Clase_preciospt->insert_precios($id, $pre, $tab) == false) {
            $sms = pg_last_error();
        }
//            else {
//                $fields = str_replace("&", ",", $fields[0]);
//                $modulo = 'PRECIOS PT';
//                $accion = 'UPDATE';
//                if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
//                    $sms = "Auditoria" . pg_last_error();
//                }
//            }
        echo $sms;
        break;
    case 4:
        $sms = 0;
        if (!empty($id)) {
            if ($Clase_preciospt->upd_precios2($id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'PRECIOS PT';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $fields[2]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 5:
        $sms = 0;
        if ($Clase_preciospt->upd_vpre1_pre2($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 6:
        $sms = 0;
        $cns_Pro = $Clase_preciospt->lista_table_pro_set($id);
        while ($rst = pg_fetch_array($cns_Pro)) {
            if ($Clase_preciospt->upd_vpre2_pre1($rst[id]) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 7:
        $sms = 0;
        if ($Clase_preciospt->upd_vald_pre2($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 8:
        $sms = 0;
        $cns_Pro = $Clase_preciospt->lista_table_pro_set($id);
        while ($rst = pg_fetch_array($cns_Pro)) {
            if ($Clase_preciospt->upd_vald_pre1($rst[id]) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 9:
        $sms = 0;
        if (!empty($id)) {
            if ($Clase_preciospt->upd_precios_precios2($id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;

    case 10:
        $sms = 0;
        if (!empty($id)) {
            if ($Clase_preciospt->upd_precios_costos($id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'COSTOS PT';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $fields[2]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;

    case 11:
        if (!empty($id)) {
            $sms = 0;
            if ($Clase_preciospt->upd_costos($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'COSTOS PT';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $fields[4]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms . '&' . $data[0] . '&' . $data[1];
        break;
}
?>
