<?php

//$_SESSION[User]='PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_ordenes_padding.php';
$Clase = new Clase_Orden_Padding();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$id = $_REQUEST[id];
$nom = $_REQUEST[nom];
$fields = $_REQUEST[fields]; //Datos para auditoria
$ei = $_REQUEST[ei]; //Datos para auditoria
$ef = $_REQUEST[ef]; //Datos para auditoria
$Adt = new Auditoria();
switch ($op) {
    case 0:
        if (empty($id)) {
            if ($Clase->insert($data) == true) {
                $sms = 0;
                $i = 0;
                while ($i < count($data2)) {
                    $rst_or = pg_fetch_array($Clase->lista_una_orden_numero($data[0]));
                    $dt = explode('&', $data2[$i]);
                    $dtorden = Array(
                        $rst_or[opp_id],
                        $dt[0],
                        $dt[1],
                        $dt[2],
                        $dt[3]
                    );
                    if ($Clase->insert_detalle_orden($dtorden) == false) {
                        $sms = 'insert det orden' . pg_last_error();
                    }
//                    if ($Clase->update_estado_mov($dt[0], $dt[1], '2', $data[0]) == false) {
//                        $sms = 'update estado movtotal' . pg_last_error();
//                    }
                    $i++;
                }

/////insert pedido mp
                $rst_sec = pg_fetch_array($Clase->lista_ped_sec());
                $sec = ($rst_sec[ped_orden] + 1);
                if ($sec >= 0 && $sec < 10) {
                    $tx_trs = "00000";
                } elseif ($sec >= 10 && $sec < 100) {
                    $tx_trs = "0000";
                } elseif ($sec >= 100 && $sec < 1000) {
                    $tx_trs = "000";
                } elseif ($sec >= 1000 && $sec < 10000) {
                    $tx_trs = "00";
                } elseif ($sec >= 10000 && $sec < 100000) {
                    $tx_trs = "0";
                } elseif ($sec >= 100000 && $sec < 1000000) {
                    $tx_trs = "";
                }
                $code = $tx_trs . $sec;
                $n = 0;
                $j = 11;
                $i = 21;
                while ($n < 6) {
                    $n++;
                    $j++;
                    $i++;
                    $ord_mp = array(
                        $code,
                        $data[5],
                        '5',
                        $data[$j],
                        $data[$i],
                        $data[$i],
                        $data[0]
                    );
                    if ($data[$j] != 0) {
                        if ($Clase->insert_pmp($ord_mp) == false) {
                            $sms = 'insert pedido mp' . pg_last_error();
                        }
                    }
                }
                if (!empty($data[32])) {
                    if ($Clase->lista_cambia_status_det($data[32], '9') == false) {
                        $sms = 'Cambio_estado_det_ped' . pg_last_error();
                    } else {
                        $rst_ped = pg_fetch_array($Clase->lista_pedido($data[32]));
                        if ($Clase->lista_cambia_status_pedido($rst_ped[ped_id], 9) == false) {///
                            $sms = 'Cambio_estado_ped' . pg_last_error();
                        }
                    }
                }

                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'ORDEN PADDING';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            } else {
                $sms = pg_last_error();
            }
        } else {
            if ($Clase->upd($data, $id) == true) {
                $sms = 0;
                $n = 0;
                $cns_det = $Clase->lista_detalle_orden($id);
                while ($rst_det = pg_fetch_array($cns_det)) {
                    if ($Clase->update_estado_mov($rst_det[pro_id], $rst_det[pro_lote], '0', '') == false) {
                        $sms = 'update estado movtotal' . pg_last_error();
                    }
                }
                if ($Clase->delete_det_orden($id) == false) {
                    $sms = 'del_det_orden' . pg_last_error();
                } else {
                    $i = 0;
                    while ($i < count($data2)) {
                        $rst_or = pg_fetch_array($Clase->lista_una_orden_numero($data[0]));
                        $dt = explode('&', $data2[$i]);
                        $dtorden = Array(
                            $rst_or[opp_id],
                            $dt[0],
                            $dt[1],
                            $dt[2],
                            $dt[3]
                        );
                        if ($Clase->insert_detalle_orden($dtorden) == false) {
                            $sms = 'insert det orden' . pg_last_error();
                        }
                        if ($Clase->update_estado_mov($dt[0], $dt[1], '2', $data[0]) == false) {
                            $sms = 'update estado movtotal2' . pg_last_error();
                        }
                        $i++;
                    }
                    if ($Clase->del_pmp_orden($data[0]) == false) {
                        $sms = 'del_pmp' . pg_last_error();
                    } else {
                        $rst_sec = pg_fetch_array($Clase->lista_ped_sec());
                        $sec = ($rst_sec[ped_orden] + 1);
                        if ($sec >= 0 && $sec < 10) {
                            $tx_trs = "00000";
                        } elseif ($sec >= 10 && $sec < 100) {
                            $tx_trs = "0000";
                        } elseif ($sec >= 100 && $sec < 1000) {
                            $tx_trs = "000";
                        } elseif ($sec >= 1000 && $sec < 10000) {
                            $tx_trs = "00";
                        } elseif ($sec >= 10000 && $sec < 100000) {
                            $tx_trs = "0";
                        } elseif ($sec >= 100000 && $sec < 1000000) {
                            $tx_trs = "";
                        }
                        $code = $tx_trs . $sec;
                        $n = 0;
                        $j = 11;
                        $i = 21;
                        while ($n < 6) {
                            $n++;
                            $j++;
                            $i++;
                            $ord_mp = array(
                                $code,
                                $data[5],
                                '5',
                                $data[$j],
                                $data[$i],
                                $data[$i],
                                $data[0]
                            );
                            if ($data[$j] != 0) {
                                if ($Clase->insert_pmp($ord_mp) == false) {
                                    $sms = 'update pedido mp' . pg_last_error();
                                }
                            }
                        }
                    }
                }

                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'ORDEN PADDING';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            } else {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase->del_pmp_orden($data) == false) {
            $sms = 'del_pmp' . pg_last_error();
        } else {
            $rst_or = pg_fetch_array($Clase->lista_una_orden_numero($data));
            $cns_det = $Clase->lista_detalle_orden($rst_or[opp_id]);
            while ($rst_det = pg_fetch_array($cns_det)) {
                if ($Clase->update_estado_mov($rst_det[pro_id], $rst_det[pro_lote], '0', '') == false) {
                    $sms = 'update estado movtotal' . pg_last_error();
                }
            }
            if ($Clase->delete_det_orden($rst_or[opp_id]) == false) {
                $sms = 'del_det_orden' . pg_last_error();
            } else {
                if ($Clase->delete($id) == true) {
                    $sms = 0;

                    $n = 0;
                    $modulo = 'ORDEN PADDING';
                    $accion = 'ELIMINAR';
                    if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                } else {
                    $sms = pg_last_error();
                }
            }
        }
        echo $sms;
        break;
    case 2:

        $rst = pg_fetch_array($Clase->lista_secuencial(5));
        $rst1 = pg_fetch_array($Clase->lista_siglas($id));
        $sec = (substr($rst[opp_codigo], -5) + 1);
        if ($sec >= 0 && $sec < 10) {
            $txt = '0000';
        } else if ($sec >= 10 && $sec < 100) {
            $txt = '000';
        } else if ($sec >= 100 && $sec < 1000) {
            $txt = '00';
        } else if ($sec >= 1000 && $sec < 10000) {
            $txt = '0';
        } else if ($sec >= 10000 && $sec < 100000) {
            $txt = '';
        }
        $rst1 = pg_fetch_array($Clase->lista_siglas($id));
        $retorno = 'C-' . $txt . $sec;
        echo $retorno;
        break;
    case 3:

        $rst = pg_fetch_array($Clase->lista_mostrar($id));
        $ancho = $rst[pro_ancho];
        $largo = $rst[pro_largo];
        $peso = $rst[pro_medvul]; ///$rst[pro_peso]
        $gramaje = $rst[pro_gramaje];
        $espesor = $rst[pro_espesor];
        $mf1 = $rst[pro_mf1];
        $mf2 = $rst[pro_mf2];
        $mf3 = $rst[pro_mf3];
        $mf4 = $rst[pro_mf4];
        $mp1 = $rst[pro_mp1];
        $mp2 = $rst[pro_mp2];
        $mp3 = $rst[pro_mp3];
        $mp4 = $rst[pro_mp4];
        $rst1 = pg_fetch_array($Clase->lista_capturar($id));
        $velocidad = $rst1[opp_velocidad];
        $rodillosup = $rst1[opp_temp_rodillosup];
        $rodilloinf = $rst1[opp_temp_rodilloinf];
        $observaciones = $rst1[opp_observaciones];
        $p_core = $rst1[pro_capa];


        $cns = $Clase->lista_combomp($id);
        $combo = "<option value='0'>Seleccione</option>";
        while ($rst = pg_fetch_array($cns)) {
            $combo.="<option value='$rst[mp_id]'>$rst[mp_referencia]</option>";
        }
        echo $ancho . '&' .
        $largo . '&' .
        $peso . '&' .
        $gramaje . '&' .
        $mf1 . '&' .
        $mf2 . '&' .
        $mf3 . '&' .
        $mf4 . '&' .
        $mp1 . '&' .
        $mp2 . '&' .
        $mp3 . '&' .
        $mp4 . '&' .
        $velocidad . '&' .
        $rodillosup . '&' .
        $rodilloinf . '&' .
        $observaciones . '&' .
        $combo . '&' .
        $espesor . '&' .
        $p_core;
        break;
    case 4:
        if ($data == 0) {
            $txt = " and(p.pro_ancho)>=$id";
        } else {
            $txt = " and(p.pro_ancho%$id)=0";
        }
        $fila = '';
        $cns = $Clase->lista_productos_semielaborados($id, $ei, $ef, $txt);
        $n = 0;
        while ($rst = pg_fetch_array($cns)) {

            $rst_inv = pg_fetch_array($Clase->total_inventario($rst[pro_id], $rst[mov_pago]));
            $inv = $rst_inv[ingreso] - $rst_inv[egreso];
            $cnt = $rst_inv[cnt];
            if (round($inv, 2) > 0) {
                $n++;
                $fila.= "<tr onmousedown='mover(this)' id='fila$n'>
                    <td>$rst[pro_codigo] </td>
                    <td hidden id='pro$n'>$rst[pro_id] </td>
                    <td>$rst[pro_descripcion] </td>
                    <td id='lote$n'>$rst[mov_pago]</td>
                    <td align='right'>$rst[pro_ancho]</td>
                    <td align='right'>$rst[pro_espesor]</td>
                    <td class='inv' align='right' id='inven$n'>" . str_replace(",", "", number_format($inv, 2)) . "</td>
                    <td align='right' id='cnt_inven$n'>" . str_replace(",", "", number_format($cnt, 0)) . "</td>
                  </tr>
                    ";
            }
        }
        echo $fila;
        break;
    case 5:
        $rst = pg_fetch_array($Clase->lista_mp_id($id));
        echo $rst[mp_pro1] . '&' . $rst[mp_pro2]; ////peso, rollos x paquete
        break;
}
?>
