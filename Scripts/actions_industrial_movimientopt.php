<?php

//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_industrial_movimientopt.php'; // cambiar clsClase_productos
$Clase_industrial_movimientopt = new Clase_industrial_movimientopt();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields]; //Datos para auditoria
$x = $_REQUEST[x];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {

            $n = 0;

            while ($n < count($data)) {
                $dt = explode('&', $data[$n]);
                $dat = Array($dt[0],
                    $dt[1],
                    $dt[2],
                    $dt[3],
                    strtoupper($dt[4]),
                    $dt[5],
                    $dt[6], $dt[7]
                );
                if ($dt[0] != 'undefined') {
                    if ($Clase_industrial_movimientopt->insert_industrial_ingresopt($dat) == false) {
                        $sms = pg_last_error();
                    } else {
                        $fields = str_replace("&", ",", $fields[0]);
                        $modulo = 'Movimiento';
                        $accion = 'Insert';
                        if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                            $sms = "Auditoria" . pg_last_error();
                        }
                    }
                }
                $n++;
            }
        } else {
            $n = 0;
            while ($n < count($data)) {
                $dt = explode('&', $data[$n]);
                $dat = Array($dt[0],
                    $dt[1],
                    $dt[2],
                    $dt[3],
                    strtoupper($dt[4]),
                    $dt[5],
                    $dt[6],
                    $dt[7],
                    $dt[8],
                );
                if ($dt[0] != 'undefined') {
                    if ($Clase_industrial_movimientopt->upd_industrial_ingreso($dat) == FALSE) {
                        $sms = pg_last_error();
                    }
                }
                $n++;
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_industrial_movimientopt->delete_industrial_ingreso($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        $rst = pg_fetch_array($Clase_industrial_movimientopt->lista_un_producto($id));
        $id = $rst[pro_id];
        $codigo = $rst[pro_codigo];
        $desc = $rst[pro_descripcion];
        $uni = $rst[pro_uni];
        echo $id . '&' . $codigo . '&' . $desc . '&' . $uni;

        break;
    case 3:
        $cns = $Clase_industrial_movimientopt->lista_producto($id);
        while ($rst = pg_fetch_array($cns)) {
            $producto.= '<option value="' . $rst[pro_codigo] . '" >' . $rst[pro_codigo] . '  ' . $rst[pro_descripcion] . '</option>';
        }
        echo $producto;
        break;
    case 4:
        $rst = pg_fetch_array($Clase_industrial_movimientopt->lista_secuencial());
        $sec = (substr($rst[mov_documento], -5) + 1);
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

        $rst1 = pg_fetch_array($Clase_industrial_movimientopt->lista_siglas($id));
        $retorno = $txt . $sec;
        echo $retorno;
        break;

    case 5:
        $rst_cli = pg_fetch_array($Clase_industrial_movimientopt->lista_un_proveedor($id));
        $retorno = $rst_cli[cli_id] . '&' . $rst_cli[nombres];
        echo $retorno;
        break;
    case 6:
        $rst_tra = pg_fetch_array($Clase_industrial_movimientopt->lista_transaccion($id));
        $retorno = $rst_tra[trs_descripcion];
        echo $retorno;
        break;
    case 7:
        if (!empty($_REQUEST[lt])) {
            $rst1 = pg_fetch_array($Clase_industrial_movimientopt->total_ingreso_egreso_lote($_REQUEST[lt]));
            $rst = pg_fetch_array($Clase_industrial_movimientopt->lista_movimiento_lote($_REQUEST[lt]));
            $lotes = "";
        } else {
            $rst = pg_fetch_array($Clase_industrial_movimientopt->lista_un_producto($id));
            if ($rst[pro_id] != '') {
                $rst1 = pg_fetch_array($Clase_industrial_movimientopt->total_ingreso_egreso_fac($rst[pro_id], $_REQUEST[lt]));
            }
            $cns_lote = $Clase_industrial_movimientopt->lista_lotes_movimiento($rst[pro_id]);
            $lotes = "";
            while ($rst_lt = pg_fetch_array($cns_lote)) {
                $lotes.="<option value='$rst_lt[mov_pago]'>$rst_lt[mov_pago]</option>";
            }
        }
        if (strlen($_REQUEST[lt]) == 11) {
            $l = substr($_REQUEST[lt], 0, 7);
            $rst_ord = pg_fetch_array($Clase_industrial_movimientopt->lista_una_orden_extrusion($l, $rst[pro_id]));
        } else {
            $l = substr($_REQUEST[lt], 0, 6);
            $rst_ord = pg_fetch_array($Clase_industrial_movimientopt->lista_una_orden_corte($l));
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
