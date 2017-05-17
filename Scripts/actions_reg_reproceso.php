<?php

//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_reg_reproceso.php'; // cambiar clsClase_productos
$Set = new Clase_reg_reproceso();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields]; //Datos para auditoria
$bod = $_REQUEST[bod];
$lt = $_REQUEST[lt];
$x = $_REQUEST[x];
$s = $_REQUEST[s];
$emp = $_REQUEST[emp];
switch ($op) {
    case 1:
        $cns = $Set->lista_productos_bod($id);
        $lista = "";
        while ($rst = pg_fetch_array($cns)) {
            if ($rst[inv] != 0) {
                $lista.="<option value='$rst[pro_id]_$rst[mov_pago]'>$rst[mov_pago] / $rst[pro_codigo] / $rst[pro_descripcion]";
            }
        }
        $retorno = $lista;
        echo $retorno;
        break;
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
            if (!$Set->insert_reproceso($dat)) {
                $sms = pg_last_error();
            }
            $n++;
        }
        ////insertar movimientos en inventario
        if ($sms == 0) {
            $dat = explode('&', $data[0]);
            if ($dat[1] == 1) {
                $rst = pg_fetch_array($Set->lista_secuencial_transferencia());
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
                $retorno = '001-' . $txt . $sec;
                if ($Set->insert_sec_transferencia($retorno) == FALSE) {
                    $sms = 'insert_sec_elaborado' . pg_last_error();
                }
                $n = 0;
                while ($n < count($data)) {
                    $dat = explode('&', $data[$n]);
                    $dt_mov = Array(
                        $dat[0], //pro_id,
                        '27', ///trs_id,
                        $dat[2], //cli_id,
                        '1', //bod_id,
                        $retorno, //mov_documento,
                        $dat[5], // mov_guia_transporte,
                        $dat[6], //mov_fecha_trans,
                        $dat[7], //mov_cantidad,
                        '0', //mov_tabla,
                        $dat[9],//mov_pago
                        $dat[12]//mov_flete(estado)
                    );

                    if (!$Set->insert_transferencia($dt_mov)) {
                        $sms = 'insert_mov_elaborado' . pg_last_error();
                    }
                    $n++;
                }
            } else {
                $rst = pg_fetch_array($Set->lista_secuencial_transferencia_terminado());
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
                $retorno = '001-' . $txt . $sec;
                if ($Set->insert_sec_transferencia_terminado($retorno) == FALSE) {
                    $sms = 'insert_sec_terminado' . pg_last_error();
                }
                $n = 0;
                while ($n < count($data)) {
                    $dat = explode('&', $data[$n]);
                    $dt_mov = Array(
                        $dat[0], //pro_id,
                        '27', ///trs_id,
                        $dat[2], //cli_id,
                        '1', //bod_id,
                        $retorno, //mov_documento,
                        $dat[5], // mov_guia_transporte,
                        $dat[6], //mov_fecha_trans,
                        $dat[7], //mov_cantidad,
                        '0', //mov_tabla,
                        $dat[9],//mov_pago,
                        $dat[12]//mov_flete(estado)
                    );

                    if (!$Set->insert_transferencia_terminado($dt_mov)) {
                        $sms = 'insert_mov_terminado' . pg_last_error();
                    }
                    $n++;
                }
            }
        }
        ///insertar movimiento en bodega MP
        if ($sms == 0) {
            $rst_sec = pg_fetch_array($Set->lista_secuencia_transaccion('0'));
            $sec0 = explode('-', $rst_sec[mov_num_trans]);
            $sec = ($sec0[1] + 1);
            if ($sec >= 0 && $sec < 10) {
                $tx_trs = "000000000";
            } elseif ($sec >= 10 && $sec < 100) {
                $tx_trs = "00000000";
            } elseif ($sec >= 100 && $sec < 1000) {
                $tx_trs = "0000000";
            } elseif ($sec >= 1000 && $sec < 10000) {
                $tx_trs = "000000";
            } elseif ($sec >= 10000 && $sec < 100000) {
                $tx_trs = "00000";
            } elseif ($sec >= 100000 && $sec < 1000000) {
                $tx_trs = "0000";
            } elseif ($sec >= 1000000 && $sec < 10000000) {
                $tx_trs = "000";
            } elseif ($sec >= 10000000 && $sec < 100000000) {
                $tx_trs = "00";
            } elseif ($sec >= 100000000 && $sec < 1000000000) {
                $tx_trs = "0";
            } elseif ($sec >= 1000000000 && $sec < 10000000000) {
                $tx_trs = "";
            }

            $no_trs = '000-' . $tx_trs . $sec;
            $n = 0;
            while ($n < count($data)) {
                $dt = explode('&', $data[$n]);
                $dat_mp = array(
                    '28',
                    $dt[11], //MP_ID
                    $dt[4],
                    $no_trs,
                    $dt[6],
                    $dt[7], //cnt
                    '',
                    '0',
                    '1', //proveedor
                    '0', //vunit
                    '', //transpo
                    '',
                    $dt[5]
                );
                if (!$Set->insert_inv_mp($dat_mp)) {
                    $sms = 'insert_mov_mp' . pg_last_error();
                }
                $n++;
            }
        }
        ///inserta egreso en bodega MP si va a extrusion
        $dt1 = explode('&', $data[0]);
        if ($dt1[10] == 2) {
            if ($sms == 0) {
                $rst_sec = pg_fetch_array($Set->lista_secuencia_transaccion('1'));
                $sec0 = explode('-', $rst_sec[mov_num_trans]);
                $sec = ($sec0[1] + 1);
                if ($sec >= 0 && $sec < 10) {
                    $tx_trs = "000000000";
                } elseif ($sec >= 10 && $sec < 100) {
                    $tx_trs = "00000000";
                } elseif ($sec >= 100 && $sec < 1000) {
                    $tx_trs = "0000000";
                } elseif ($sec >= 1000 && $sec < 10000) {
                    $tx_trs = "000000";
                } elseif ($sec >= 10000 && $sec < 100000) {
                    $tx_trs = "00000";
                } elseif ($sec >= 100000 && $sec < 1000000) {
                    $tx_trs = "0000";
                } elseif ($sec >= 1000000 && $sec < 10000000) {
                    $tx_trs = "000";
                } elseif ($sec >= 10000000 && $sec < 100000000) {
                    $tx_trs = "00";
                } elseif ($sec >= 100000000 && $sec < 1000000000) {
                    $tx_trs = "0";
                } elseif ($sec >= 1000000000 && $sec < 10000000000) {
                    $tx_trs = "";
                }

                $no_trs = '001-' . $tx_trs . $sec;
                $n = 0;
                while ($n < count($data)) {
                    $dt = explode('&', $data[$n]);
                    $dat_mp = array(
                        '1',
                        $dt[11], //MP_ID
                        $dt[4],
                        $no_trs,
                        $dt[6],
                        $dt[7], //cnt
                        '',
                        '0',
                        '1', //proveedor
                        '0', //vunit
                        '', //transpo
                        '',
                        $dt[5]
                    );
                    if (!$Set->insert_inv_mp($dat_mp)) {
                        $sms = 'insert_mov_mp' . pg_last_error();
                    }
                    $n++;
                }
            }
        }
        if ($sms == 0) {
            $j = 0;
            while ($j < count($fields)) {
                $f = $f . strtoupper($fields[$j] . '&');
                $j++;
            }

            $modulo = 'REGISTRO REPROCESO';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $dat[4]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 15:
        $rst = pg_fetch_array($Set->lista_secuencial_transferencia());
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
        $retorno = $txt . $sec;
        echo $retorno;
        break;
    case 16:
        $sms = 0;
        $sec = $_REQUEST[sec];
        if ($Set->insert_sec_transferencia($sec) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 64:

        if ($lt != '') {
            $rst = pg_fetch_array($Set->lista_un_producto_lote($lt, $bod));
        } else {
            $rst = pg_fetch_array($Set->lista_un_producto($id, $lt, $bod));
        }
        if ($rst[pro_id] != '') {
            echo $rst[pro_id] . '&' . $rst[pro_codigo] . '&' . $rst[pro_descripcion] . '&' . $rst[pro_uni] . '&' . $rst[inv] . '&' . $rst[mov_pago]. '&' . $rst[ mov_flete];
        }
        break;
}
?>

