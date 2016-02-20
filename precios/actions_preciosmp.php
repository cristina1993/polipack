<?php

//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_preciosmp.php';
$Clase_preciosmp = new Clase_preciosmp();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
$tab = $_REQUEST[tab];
switch ($op) {
    case 0:
        if (!empty($id)) {
            $sms = 0;
            if ($Clase_preciosmp->upd_precios($data, $id) == false) {
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
        echo $sms . '&' . $data[0] . '&' . $data[1] . '&' . $data[2] . '&' . $data[3];
        break;

    case 1:
        $sms = 0;
        if (!empty($id)) {
            if ($Clase_preciosmp->upd_precios2($id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;

    case 2:
        $sms = 0;
        if (strlen($id) != 0) {
            if ($Clase_preciosmp->upd_precios_todos($tab, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;


    case 3:
        $sms = 0;
        $rst_prod = pg_fetch_array($Clase_preciosmp->lista_productos_codigo($_REQUEST[cod]));
        $rst_precio = pg_fetch_array($Clase_preciosmp->lista_precios_proid($rst_prod[pro_id]));
        if (empty($rst_precio)) {
            if ($Clase_preciosmp->insert_precios($rst_prod[pro_id]) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
//    case 4:
//        $sms = 0;
//        if (strlen($id) != 0) {
//            if ($Clase_preciosmp->upd_precios_precios2($id) == false) {
//                $sms = pg_last_error();
//            } else {
//                if ($Clase_preciosmp->upd_precios_precios2($id) == false) {
//                    $sms = pg_last_error();
//                } el if ($Clase_preciosmp->upd_precios2($id) == false) {
//                    $sms = pg_last_error();
//                }
//            }
//        }
//        echo $sms;
//        break;
    case 5:
        $sms = 0;
        if (strlen($id) != 0) {
            if ($Clase_preciosmp->upd_vpre1_pre2($id) == false) {
                $sms = pg_last_error();
            } else {
                if ($Clase_preciosmp->upd_vald_pre1($id) == false) {
                    $sms = pg_last_error();
                }
            }
        }
        echo $sms;
        break;

    case 6:
        $sms = 0;
        if (strlen($id) != 0) {
            if ($Clase_preciosmp->upd_vpre2_pre1($id) == false) {
                $sms = pg_last_error();
            } else {
                if ($Clase_preciosmp->upd_vald_pre2($id) == false) {
                    $sms = pg_last_error();
                }
            }
        }
        echo $sms;
        break;
}
?>
