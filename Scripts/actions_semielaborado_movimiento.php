<?php

//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_semielaborado_movimiento.php'; // cambiar clsClase_productos
$Set = new Clase_semielaborado_movimiento();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields]; //Datos para auditoria
$x = $_REQUEST[x];
$s = $_REQUEST[s];
$emp = $_REQUEST[emp];
switch ($op) {
    case 5:
        $rst_cli = pg_fetch_array($Set->lista_un_proveedor($id));
        $retorno = $rst_cli[cli_id] . '&' . $rst_cli[nombres];
        echo $retorno;
        break;
    case 6:
        $rst_tra = pg_fetch_array($Set->lista_transaccion($id));
        $retorno = $rst_tra[trs_descripcion];
        echo $retorno;
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
        $inv = $rst1[ingreso] - $rst1[egreso];
        echo $rst[pro_id] . '&' . $rst[pro_codigo] . '&' . $rst[pro_descripcion] . '&' . $rst[pro_uni] . '&' . $inv . '&' . $lotes. '&' . $rst1[estado];
        break;
}
?>

