<?php

//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_preciosmp.php';
include_once("../Clases/clsAuditoria.php");
$Adt = new Auditoria();
$Clase_preciosmp = new Clase_preciosmp();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$tab = $_REQUEST[tab];
$fields = $_REQUEST[fields];
$mod = $_REQUEST[mod];
switch ($op) {
    case 0:
        if (!empty($id)) {
            $sms = 0;
            $rst = pg_fetch_array($Clase_preciosmp->buscar_un_costo($id, $data[1]));
            if (!empty($rst)) {
                if ($Clase_preciosmp->upd_costos($data, $id) == false) {
                    $sms = pg_last_error();
                }
            } else {
                if ($Clase_preciosmp->insert_costos($data, $id) == false) {
                    $sms = pg_last_error();
                }
            }
            if ($sms == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                if ($mod == 0) {
                    $modulo = 'COSTOS';
                } else if ($mod == 1) {
                    $modulo = 'COSTOS EXTRUSION';
                } else if ($mod == 2) {
                    $modulo = 'COSTOS BOBINADO';
                }
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $fields[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms . '&' . $data[0] . '&' . $data[1];
        break;

    case 1:
        $sms = 0;
        if (!empty($id)) {
            if ($Clase_preciosmp->upd_precios2($id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'PRECIOS';
                $accion = 'MODIFICAR';

                if ($Adt->insert_audit_general($modulo, $accion, $f, $fields[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms . '&' . $id;
        break;

    case 2:
        $sms = 0;
        if (strlen($id) != 0) {
            if ($Clase_preciosmp->upd_precios_todos($tab, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'PRECIOS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $fields[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;


    case 3:
        if (!empty($id)) {
            $sms = 0;
            if ($Clase_preciosmp->upd_costos($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'COSTOS';
                $accion = 'MODIFICAR';

                if ($Adt->insert_audit_general($modulo, $accion, $f, $fields[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms . '&' . $data[0] . '&' . $data[1];
        break;
    case 4:
        $sms = 0;
        if (!empty($id)) {
            if ($Clase_preciosmp->upd_costos2($id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'COSTOS';
                $accion = 'MODIFICAR';

                if ($Adt->insert_audit_general($modulo, $accion, $f, $fields[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms . '&' . $id;
        break;


    case 7:
        $sms = 0;
        $imp = pg_fetch_array($Clase_preciosmp->lista_un_impuesto($id));
        $cnt = $imp[por_porcentage];
//        $cnt = $imp[imp_porcentage] / 100;
        echo $imp[por_id] . '&' . $imp[por_codigo] . '&' . $cnt;
        break;

    case 8:
        if (!empty($id)) {
            $sms = 0;
            if ($Clase_preciosmp->upd_costos_importe($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'COSTOS';
                $accion = 'MODIFICAR';

                if ($Adt->insert_audit_general($modulo, $accion, $f, $fields[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms . '&' . $data[0] . '&' . $data[1];
        break;
}
?>
