<?php

//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_industrial_egresopt.php'; // cambiar clsClase_productos
$Set = new Clase_industrial_egresopt();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields]; //Datos para auditoria
$x = $_REQUEST[x];
switch ($op) {
    case 0:
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
            $modulo = 'EGRESO PRODUCTO TERMINADO';

            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $dat[4]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 1:
        if (!empty($_REQUEST[lt])) {
            $rst1 = pg_fetch_array($Set->total_ingreso_egreso_lote($_REQUEST[lt]));
            $rst = pg_fetch_array($Set->lista_movimiento_lote($_REQUEST[lt]));
            $lotes = "";
        } else {
            $rst = pg_fetch_array($Set->lista_un_producto($id));
            if ($rst[pro_id] != '') {
                $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fac($rst[pro_id], $_REQUEST[lt]));
            }
            $cns_lote = $Set->lista_lotes_movimiento($rst[pro_id]);
            $lotes = "";
            while ($rst_lt = pg_fetch_array($cns_lote)) {
                $lotes.="<option value='$rst_lt[mov_pago]'>$rst_lt[mov_pago]</option>";
            }
        }
        if (strlen($_REQUEST[lt]) == 11) {
            $l = substr($_REQUEST[lt], 0, 7);
            $rst_ord = pg_fetch_array($Set->lista_una_orden_extrusion($l, $rst[pro_id]));
        } else {
            $l = substr($_REQUEST[lt], 0, 6);
            $rst_ord = pg_fetch_array($Set->lista_una_orden_corte($l));
        }

        $inv = $rst1[ingreso] - $rst1[egreso];
        $inv_cnt = $rst1[cnt_ingreso] - $rst1[cnt_egreso];
        $inv_caja = $inv_cnt / $rst_ord[opp_velocidad];
        $dt = explode('.', $inv_caja);
        if (empty($rst_ord[opp_velocidad])) {
            $cnt_cj = 0;
        } else {
            if (!empty($dt[1])) {
                $cnt_cj = round($dt[0]);
            } else {
                $cnt_cj = $inv_caja;
            }
        }
        echo $rst[pro_codigo] . '&' . $rst[pro_descripcion] . '&' . $rst[pro_uni] . '&' . $rst[pro_id] . '&' . $lotes . '&' . $inv . '&' . $inv_cnt . '&' . $cnt_cj . '&' . round($rst_ord[opp_velocidad]) . '&' . $rst1[estado];
        break;
}
?>
