<?php

//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_preciospt.php';
include_once '../Clases/clsClase_descuentos.php';
include_once("../Clases/clsAuditoria.php");

$Adt = new Auditoria();
$Clase_preciospt = new Clase_preciospt();
$Clase_preciospt = new Clase_preciospt();
$Desc = new Descuentos();
$op = $_REQUEST[op];
$x = $_REQUEST[x];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
$tab = $_REQUEST[tab];
switch ($op) {
    case 0:
        if (empty($x)) {
            $sms = 0;
            if ($Clase_preciospt->insert_descuento($data, $id) == false) {
                $sms = pg_last_error();
            }
        } else {
            $sms = 0;
            if ($Clase_preciospt->update_descuentos($data, $id, $x) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms . '&' . $data[2];
        break;

    case 1:
        $sms = 0;
        $rst = pg_fetch_array($Clase_preciospt->lista_un_descuento_fecha($id, $tab, $x));
        if (!empty($rst)) {
            if ($rst[pro_tabla == 0]) {
                $rst1 = pg_fetch_array($Clase_preciospt->lista_i_productos($rst[pro_id]));
                $pro = $rst1[pro_descripcion];
            } else {
                $rst1 = pg_fetch_array($Clase_preciospt->lista_productos($rst[pro_id]));
                $pro = $rst1[pro_b];
            }
            echo $pro . '&' . $rst[des_fec_inicio] . '&' . $rst[des_fec_fin] . '&' . $rst[des_estado];
        } else {
            echo $sms;
        }
        break;
    case 2:
        $sms = 0;
        if ($Clase_preciospt->delete_descuentos($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;

        break;
    case 3:
        $sms = 0;
        $n = 0;
        while ($n < count($data)) {
            $dat0 = explode(',', $data[$n]);
            $n0 = 0;
            while ($n0 < count($dat0)) {
                $d = 0;
                $dat1 = explode('&', $dat0[$n0]);
                $rst_pre = pg_fetch_array($Desc->lista_precio_prod_tbl($dat1[3], $dat1[4]));
                if (empty($rst_pre[pre_id])) {
                    $pre_id = pg_fetch_array($Desc->ultimo_precios());
                    $pre = $pre_id[pre_id] + 1;
                    if ($Desc->insert_precios($pre, $dat1[3], $dat1[4]) == false) {
                        $sms = pg_last_error();
                    } else {
                        $d = 1;
                    }
                } else {
                    $d = 1;
                }
                if ($d == 1) {
                    $rst_pre1 = pg_fetch_array($Desc->lista_precio_prod_tbl($dat1[3], $dat1[4]));
                    $dat1[0] = $rst_pre1[pre_id];
                    $rst_dsc = pg_fetch_array($Desc->lista_desc_prid_ems($dat1[0], $dat1[1]));
                    if (empty($rst_dsc)) {
                        if ($Desc->insert_descuentos($dat1) == false) {
                            $sms = pg_last_error();
                        }
                    } else {
                        if ($Desc->upd_descuentos($dat1[2], $rst_dsc[dsc_id]) == false) {
                            $sms = pg_last_error();
                        }
                    }
                }
                $n0++;
            }
            $n++;
        }

//        if ($sms == 0) {
//            $j = 0;
//            while ($j < count($fields)) {
//                $f = $f . strtoupper($fields[$j] . '&');
//                $j++;
//            }
//            $modulo = 'DESCUENTOS PT';
//            $accion = 'MODIFICAR';
//            if ($Adt->insert_audit_general($modulo, $accion, $f, '') == false) {
//                $sms = "Auditoria" . pg_last_error() . 'ok2';
//            }
//        }

        echo $sms;
        break;
}
?>
