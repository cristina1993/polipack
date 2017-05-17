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
        $rst = pg_fetch_array($Set->lista_un_producto($id));
        if ($rst[pro_id] != '') {
            $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fac($rst[pro_id], $_REQUEST[lt]));
            $inv = $rst1[ingreso] - $rst1[egreso];
            echo $rst[pro_id] . '&' . $rst[pro_codigo] . '&' . $rst[pro_descripcion] . '&' . $rst[pro_uni] . '&' . $inv;
        }
        break;
}
?>

