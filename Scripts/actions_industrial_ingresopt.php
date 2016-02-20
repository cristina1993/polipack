<?php

//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_industrial_ingresopt.php'; // cambiar clsClase_productos
$Clase_industrial_ingresopt = new Clase_industrial_ingresopt();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields]; //Datos para auditoria
$x = $_REQUEST[x];
$s = $_REQUEST[s];
$emp = $_REQUEST[emp];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {

            $n = 0;

            while ($n < count($data)) {
                $dat = explode('&', $data[$n]);
                if ($dt[0] != 'undefined') {
                    if ($Clase_industrial_ingresopt->insert_industrial_ingresopt($dat) == false) {
                        $sms = pg_last_error();
                    }
//                    else {
//                        $fields = str_replace("&", ",", $fields[0]);
//                        $modulo = 'BodegaIndus';
//                        $accion = 'Insert';
//                        if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
//                            $sms = "Auditoria" . pg_last_error();
//                        }
//                    }
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
                    if ($Clase_industrial_ingresopt->upd_industrial_ingreso($dat) == FALSE) {
                        $sms = pg_last_error();
                    }
                }
                $n++;
            }
        }
        echo $sms . '&' . $x;
        break;
    case 1:
        if ($Clase_industrial_ingresopt->delete_industrial_ingreso($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        if ($_REQUEST[ems] == 1) {
            $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_producto_noperti_cod($id));
            $id = $rst[id];
            $codigo = $rst[pro_a];
            $desc = $rst[pro_b];
            $uni = $rst[pro_uni];
            echo $id . '&' . $codigo . '&' . $desc . '&' . $uni;
        } else {
            if ($emp == 1 or $emp == 2) {
                $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_producto_noperti_cod($id));
                $id = $rst[id];
                $codigo = $rst[pro_a];
                $desc = $rst[pro_b];
                $uni = $rst[pro_uni];
                echo $id . '&' . $codigo . '&' . $desc . '&' . $uni;
            } else {
                $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_producto_cod($id));
                $id = $rst[pro_id];
                $codigo = $rst[pro_codigo];
                $desc = $rst[pro_descripcion];
                $uni = $rst[pro_uni];
                //$uni2 = $rst[pro_uni2];
            }
            echo $id . '&' . $codigo . '&' . $desc . '&' . $uni;
        }



        break;
    case 3:
        if ($_REQUEST[ems] == 1) {
            $cns = $Clase_industrial_ingresopt->lista_productos_comercial();
            while ($rst = pg_fetch_array($cns)) {
                $tp = explode('&', $rst[pro_tipo]);
                $producto.= "<option value='$rst[id]' >$rst[pro_a] $rst[pro_ac] $rst[pro_b]</option>";
            }
        } else {
            $cns = $Clase_industrial_ingresopt->lista_producto($id);
            while ($rst = pg_fetch_array($cns)) {
                $producto.= "<option value='$rst[pro_id]' >$rst[pro_codigo] $rst[pro_descripcion]</option>";
            }
        }
        echo $producto;
        break;
    case 4:
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_secuencial());
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

        $rst1 = pg_fetch_array($Clase_industrial_ingresopt->lista_siglas($id));
        $retorno = $txt . $sec;
        echo $retorno;
        break;

    case 5:
        $rst_cli = pg_fetch_array($Clase_industrial_ingresopt->lista_un_proveedor($id));
        $retorno = $rst_cli[cli_id] . '&' . $rst_cli[nombres];
        echo $retorno;
        break;
    case 6:
        $rst_tra = pg_fetch_array($Clase_industrial_ingresopt->lista_transaccion($id));
        $retorno = $rst_tra[trs_descripcion];
        echo $retorno;
        break;
    case 7:
        $cns1 = $Clase_industrial_ingresopt->lista_prod_noperti();
        while ($rst = pg_fetch_array($cns1)) {
            $tp = explode('&', $rst[pro_tipo]);
//            $producto.= "<option value='$rst[pro_a]' >$rst[pro_a] - $rst[pro_ac] $tp[9] $rst[pro_b]</option>";
            $producto.= "<option value='$rst[id]' >$rst[pro_a] - $rst[pro_ac] $rst[pro_b]</option>";
        }
        echo $producto;
        break;
    case 8:
        $cns1 = $Clase_industrial_ingresopt->lista_prod_comercial();
        while ($rst = pg_fetch_array($cns1)) {
            $tp = explode('&', $rst[pro_tipo]);
            $producto.= "<option value='$rst[id]' >$rst[pro_a] - $rst[pro_ac] $rst[pro_b]</option>";
        }
        echo $producto;
        break;
///Transferencias        
    case 9:
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_local($id));
        echo $rst[cod_punto_emision] . '&' . $rst[cod_cli] . '&' . $rst[nombre_comercial];
        break;
    case 10:
        $tbl = substr($id, 0, 1);
        $id2 = substr($id, 1, (strlen($id) - 1));
        if ($tbl == 1) {
            $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_producto_noperti_cod($id2));
            $id = $rst[id];
            $codigo = $rst[pro_a];
            $lote = $rst[pro_ac];
            $desc = $rst[pro_b];
            $rst1 = pg_fetch_array($Clase_industrial_ingresopt->total_ingreso_egreso_fac($rst[id], $s, '1'));
            $inv = $rst1[ingreso] - $rst1[egreso];
            echo $id . '&' . $codigo . '&' . $lote . '&' . $desc . '&' . $uni . '&' . $tbl . '&' . $inv . '&' . $rst[pro_ad];
        } else {
            $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_producto_cod($id2));
            $id = $rst[pro_id];
            $codigo = $rst[pro_codigo];
            $lote = '';
            $desc = $rst[pro_descripcion];
            $rst1 = pg_fetch_array($Clase_industrial_ingresopt->total_ingreso_egreso_fac($rst[pro_id], $s, '0'));
            $inv = $rst1[ingreso] - $rst1[egreso];
            echo $id . '&' . $codigo . '&' . $lote . '&' . $desc . '&' . $uni . '&' . $tbl . '&' . $inv . '& ';
        }
        break;
    case 11:
        $sms = 0;
        $data1 = $_REQUEST[data1];
        $data2 = $_REQUEST[data2];
        $n = 0;
        while ($n < count($data1)) {
            $dat1 = explode('&', $data1[$n]);
            $dat2 = explode('&', $data2[$n]);
            if (!$Clase_industrial_ingresopt->insert_transferencia($dat1)) {
                $sms = pg_last_error();
                break;
            } else {
                if (!$Clase_industrial_ingresopt->insert_transferencia($dat2)) {
                    $sms = pg_last_error();
                    break;
                }
            }

            $n++;
            $doc = $dat1[4];
        }
        if ($sms == 0) {
            $j = 0;
            while ($j < count($fields)) {
                $f = $f . strtoupper($fields[$j] . '&');
                $j++;
            }
            $modulo = 'TRANSFERENCIA PRODUCTO TERMINADO';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $doc) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }

        echo $sms;
        break;
    case 12:
        $sms = 0;
        $data = $_REQUEST[data];
        $n = 0;
        while ($n < count($data)) {
            $dat = explode('&', $data[$n]);
            if (!$Clase_industrial_ingresopt->insert_transferencia($dat)) {
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
            if ($x == 0) {
                $modulo = 'INGRESO PRODUCTO TERMINADO';
            } else if ($x == 1) {
                $modulo = 'MOVIMIENTO PRODUCTO TERMINADO';
            } else {
                $modulo = 'MOVIMIENTO PRODUCTO';
            }
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $dat[4]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 13:
        $cns_pro = $Clase_industrial_ingresopt->lista_productos_total($s);
        while ($rst_pro = pg_fetch_array($cns_pro)) {
            echo "<option value='$rst_pro[tbl]$rst_pro[id]' >$rst_pro[lote] $rst_pro[codigo]  $rst_pro[descripcion]</option>";
        }
//        echo $sms;
        break;
    case 14:
        $sms = 0;
        $data1 = $_REQUEST[data1];
        $data2 = $_REQUEST[data2];
        $n = 0;
        while ($n < count($data1)) {
            $dat1 = explode('&', $data1[$n]);
            $dat2 = explode('&', $data2[$n]);
            if (!$Clase_industrial_ingresopt->insert_transferencia($dat1)) {
                $sms = pg_last_error();
                break;
            } else {
                if (!$Clase_industrial_ingresopt->insert_transferencia($dat2)) {
                    $sms = pg_last_error();
                    break;
                }
            }
            $n++;
            $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_id_pedido($dat2[5]));
            $rst1 = pg_fetch_array($Clase_industrial_ingresopt->lista_det_pedido($dat2[5], $rst[ped_id]));
            if ($rst1[transferencia] == $rst1[pedido]) {
                $std = 8;
            } else {
                $std = 7;
            }
            if (!$Clase_industrial_ingresopt->upd_pedido($rst[ped_id], $std)) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 15:
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_secuencial_transferencia());
        $sec = (substr($rst[sec_transferencias], -5) + 1);
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

        $rst1 = pg_fetch_array($Clase_industrial_ingresopt->lista_siglas($id));
        $retorno = $txt . $sec;
        echo $retorno;
        break;

    case 16:
        $sms = 0;
        $sec = $_REQUEST[sec];
        if ($Clase_industrial_ingresopt->insert_sec_transferencia($sec) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
}
?>

