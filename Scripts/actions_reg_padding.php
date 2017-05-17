<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_reg_padding.php';
$Set = new Clase_reg_padding();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data_semi = $_REQUEST[data_semi];
$id = $_REQUEST[id];
$s = $_REQUEST[s];
$fec = $_REQUEST[fec];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
//            $str = $data;
//            $n = 0;
//            foreach ($str as $row => $cliente) {
//                $str[$n] = strtoupper($cliente);
//                $n++;
//            }
            $n = 0;
            while ($n < count($data)) {
                $dt = explode('&&', $data[$n]);
                if ($Set->insert_registro_padding($dt) == FALSE) {
                    $sms = pg_last_error();
                } else {
                    $rst_ord = pg_fetch_array($Set->lista_una_orden($dt[0]));
                    ////bodega terminado
                    if ($dt[6] != 3) {
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
                        $dt_mov = Array(
                            $rst_ord[pro_id], //pro_id,
                            '26', ///trs_id,
                            $rst_ord[cli_id], //cli_id,
                            '1', //bod_id,
                            $retorno, //mov_documento,
                            $rst_ord[opp_codigo], // mov_guia_transporte,
                            $dt[1], //mov_fecha_trans,
                            $dt[4], //mov_cantidad peso,
                            $dt[5], //mov_tabla #rollos,
                            $dt[3], //mov_pago
                            $dt[6]//mov_flete(estado)
                        );

                        if (!$Set->insert_transferencia_terminado($dt_mov)) {
                            $sms = 'insert_mov_terminado' . pg_last_error();
                        }
                    } else {
                        ///bodega semielaborado ingreso
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

                        $dt_mov = Array(
                            $rst_ord[pro_id], //pro_id,
                            '26', ///trs_id,
                            $rst_ord[cli_id], //cli_id,
                            '1', //bod_id,
                            $retorno, //mov_documento,
                            $rst_ord[opp_codigo], // mov_guia_transporte,
                            $dt[1], //mov_fecha_trans,
                            $dt[4], //mov_cantidad,
                            $dt[5], //mov_tabla,
                            $dt[3], //mov_pago
                            $dt[6]//mov_flete(estado)
                        );

                        if (!$Set->insert_transferencia($dt_mov)) {
                            $sms = 'insert_mov_elaborado' . pg_last_error();
                        }
                    }
                    $n++;
                }

                ///bodega semielaborado egreso
                if ($dt[6] != 3) {
                    $m = 0;
                    while ($m < count($data_semi)) {
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
                            $sms = 'insert_semielaborado' . pg_last_error();
                        }
                        $dts = explode('&&', $data_semi[$m]);
                        $dts_mov = Array(
                            $dts[1], //pro_id,
                            '16', ///trs_id,
                            $rst_ord[cli_id], //cli_id,
                            '1', //bod_id,
                            $retorno, //mov_documento,
                            $rst_ord[opp_codigo], // mov_guia_transporte,
                            $dt[1], //mov_fecha_trans,
                            $dts[5], //mov_cantidad,
                            '1', //mov_tabla,
                            $dts[0], //mov_pago,
                            $dts[6] //mov_flete(estado),   
                        );
                        if (!$Set->insert_transferencia($dts_mov)) {
                            $sms = 'insert_mov_semi' . pg_last_error();
                        }
                        $m++;
                    }
                }
            }

            ///estados de pedido
            $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst_ord[opp_id], $rst_ord[pro_id]));
            $por = ($rst_prod[peso] * 100) / $rst_ord[pro_mf3];
            if ($por >= 90) {
                $est = '11';
            } else {
                $est = '10';
            }
            if ($Set->lista_cambia_status_det($rst_ord[ped_id], $est, $rst_ord[pro_id]) == true) {///
                if ($Set->lista_cambia_status_pedido($rst_ord[ped_id], $est) == false) {///
                    $sms = 'Cambio_estado_ped' . pg_last_error();
                }
            } else {
                $sms = 'Cambio_estado_det_ped' . pg_last_error();
            }

            $n = 0;
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'REG. PRODUCCION PADDING';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $data[6]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        } else {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $cliente) {
                $str[$n] = strtoupper($cliente);
                $n++;
            }
            if ($Set->update_registro_padding($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'REG. PRODUCCION PADDING';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[6]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        $rst_pro = pg_fetch_array($Set->lista_un_producto($rst_ord[pro_id]));
        echo $sms . '&' . $rst_pro[pro_ancho] * 1000 . '&' . $rst_pro[pro_largo] . '&' . $rst_pro[pro_espesor] . '&' . $rst_pro[pro_capa] . '&' . $rst_ord[opp_id] . '&' . $rst_pro[pro_id] . '&' . $rst_ord[cli_id]. '&' . $rst_pro[pro_gramaje];
        break;
    case 1:
        $sms = 0;
        if ($Set->delete_registro_padding($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'REG. PRODUCCION PADDING';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                $sms = "Auditoria" . pg_last_error();
            }
        }
        echo $sms;
        break;
    case 2:
        $sms;
        $rst = pg_fetch_array($Set->lista_una_orden_cod(trim(strtoupper($id))));
        $rst1 = pg_fetch_array($Set->lista_un_producto($rst[pro_id]));
        $cns = $Set->lista_detalle_orden($rst[opp_id]);
        $options = "";
//        $lote = "";
        $lt1 = array();
        while ($rst_se = pg_fetch_array($cns)) {
            $rst_i = pg_fetch_array($Set->total_inventario($rst_se[pro_id], $rst_se[mov_pago]));
            $rst_sm = pg_fetch_array($Set->lista_un_producto($rst_se[pro_id]));
            $inv = $rst_i[ingreso] - $rst_i[egreso] - $rst_sm[pro_propiedad5];
            if (round($inv, 2) > 0) {
                $options.="<option id='$rst_se[mov_pago] / $rst_se[pro_descripcion] /" . round($inv, 2) . " kg' value='$rst_se[mov_pago]'>$rst_se[mov_pago] / $rst_se[pro_descripcion] /" . round($inv, 2) . " kg</option>";
                array_push($lt1, $rst_se[pro_lote]);
            }
//            if ($rst_se[pro_lote] != $lote) {
//                $ord_semi.=$rst_se[pro_lote] . '    R:' . $lt1[$rst_se[pro_lote]] . ' ';
//            }
//            $lote = $rst_se[pro_lote];
        }
        $contagem = array_count_values($lt1);
        foreach ($contagem AS $lote => $veces) {
            $ord_semi.= "$lote   R: $veces  ";
        }
        $cns_reg = $Set->registros_productos($rst[opp_id]);
        $fila = '';
        $n = 0;
        $conf = 0;
        $inconf = 0;
        $cnf = 0;
        $sumc = 0;
        while ($rst_reg = pg_fetch_array($cns_reg)) {
            $peso_conf = '';
            $peso_inconf = '';
            if ($rst_reg[rpa_estado] == 0) {
                if (!empty($rst_reg[rpa_peso])) {
                    $peso_conf = str_replace(',', '', number_format($rst_reg[rpa_peso], 2));
                    $cnf = round($rst_reg[rpa_rollo]);
                } else {
                    $peso_conf = '';
                    $cnf = 0;
                }
            } else {
                if (!empty($rst_reg[rpa_peso])) {
                    $peso_inconf = str_replace(',', '', number_format($rst_reg[rpa_peso], 2));
                    $cnf = 0;
                } else {
                    $peso_inconf = '';
                    $cnf = 0;
                }
            }
//            if ($rst_reg[prod] == 1) {
//                array_push($prod1, $peso_conf . '&&' . $peso_inconf . '&&' . $rst_reg[pro_id] . '&&' . $rst_reg[rpa_lote] . '&&' . $rst_reg[rpa_lote]);
//            }
            $n++;
            $fila .= "<tr class='itm' style='height:20px'> 
                            <td style='width:100px' align='right' id='lote_semi1_$n'>$rst_reg[rpa_lote_semielaborado]</td> 
                            <td hidden id='semi_pro_id1_$n'>$rst_reg[rpa_semielaborado]</td> 
                            <td style='width:80px' align='right' id='cantidad_$n'>$rst_reg[rpa_rollo]</td> 
                            <td style='width:80px' align='right' id='valor1_$n'>$peso_conf</td> 
                            <td style='width:80px' align='right' id='valor2_$n'>$peso_inconf</td> 
                            <td hidden id='pro_id1_$n'>$rst_reg[pro_id]</td> 
                            <td hidden id='pro_lote1_$n'>$rst_reg[rpa_lote]</td> 
                            <td hidden id='pro_estado1_$n'> $rst_reg[rpa_estado]</td> 
                     </tr>";
            $cnt_rollo+=$rst_reg[rpa_rollo];
            $conf+=$peso_conf;
            $inconf+=$peso_inconf;
            $sumc+=$cnf;
        }
        $cj = round($sumc / $rst[opp_velocidad], 2);
        $dt = explode('.', $cj);
        if ($dt[1] > 0) {
            $cjr = $dt[0] + 1;
        } else {
            $cjr = $dt[0];
        }
        $cjf = $rst[mp_cnt1] - $cjr;
//        $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst[opp_id]));
        $sms = $rst[opp_id] . '&' .
                $rst[opp_codigo] . '&' .
                $rst[opp_fec_pedido] . '&' .
                $rst[opp_fec_entrega] . '&' .
                '2' . '&' .
                'TERMINADO' . '&' .
                $rst[cli_raz_social] . '&' .
                $rst1[pro_descripcion] . '&' .
                $rst[opp_cantidad] . '&' .
                $rst[pro_mf3] . '&' .
                $rst1[pro_ancho] . '&' .
                $rst1[pro_id] . '&' .
                $options . '&' .
                $fila . '&' .
                str_replace(',', '', number_format($conf, 2)) . '&' .
                str_replace(',', '', number_format($inconf, 2)) . '&' .
                $rst[opp_espesor_prod] . '&' .
                $rst1[pro_largo] . '&' .
                $rst[pro_mp1] . '&' .
                $rst[opp_velocidad] . '&' .
                $rst[pro_mf3] . '&' .
                $cjr . '&' .
                $cjf . '&' .
                $rst[mp_cnt1] . '&' .
                $rst1[pro_medvul] . '&' .
                $rst1[pro_propiedad7] . '&' .
                $cnt_rollo . '&' .
                $ord_semi . '&' .
                $rst[opp_observaciones] . '&' .
                $rst1[pro_capa] . '&' .
                $rst[opp_etiqueta] . '&' .
                $rst[opp_eti_numero];
        echo $sms;
        break;
    case 3:
        $sms = 0;
        $data = strtoupper($data);
        if ($s == 1) {
            $txt = "set chq_deposito='$data', chq_estado='$s', chq_fecha='$fec' ";
        } else {
            $txt = "set chq_observacion='$data', chq_estado='$s', chq_fecha='$fec'";
        }
        if ($Set->upd_estado($id, $txt) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 4:
        $rst = pg_fetch_array($Set->lista_un_rollo_semielaborado($id, $data));
        $rst_i = pg_fetch_array($Set->total_inventario($rst[pro_id], $rst[mov_pago]));
        $rst_sm = pg_fetch_array($Set->lista_un_producto($rst[pro_id]));
//        $inv = $rst_i[ingreso] - $rst_i[egreso] - $rst_sm[pro_propiedad5];
        $inv = $rst_i[ingreso] - $rst_i[egreso] - $rst_sm[pro_propiedad5];
        $inv_brt = $rst_i[ingreso] - $rst_i[egreso];
        ///0:id_lote_inv;1:lote/descripcion/inv;2:core;3:id;4:lote;5:inv;6:peso_bruto;7:ancho;8:estado
        echo $rst[pro_id] . '_' . $rst[mov_pago] . '_' . round($inv, 2) . '&&' . "$rst[mov_pago] / $rst[pro_descripcion] /" . round($inv, 2) . " Kg" . '&&' . $rst_sm[pro_propiedad5] . '&&' . $rst[pro_id] . '&&' . $rst[mov_pago] . '&&' . round($inv, 2) . '&&' . round($inv_brt, 2) . '&&' . $rst_sm[pro_ancho] . '&&' . $rst[mov_flete];
        break;

    case 5:
        ser_open("COM1", 9600, 8, "None", "1", "None");
        sleep(1);
        for ($i = 0; $i < 9; $i++) {
            $j = ser_readbyte();
            echo sprintf("%c", $j);
        }
        ser_close();
//        ////implementar funcion lectura de puerto serial
//        echo $sms; /// retorno peso   
        break;

    case 6:
        $sms = 0;
        if ($id == 1) {
            $dt_conf = array(
                $data[17], //ord_id
                $data[18], //pro_id
                $data[19], //cli_id
                '1', //etg_tipo
                $data[0], //etg_numero
                $data[8], //etg_copias
                $data[9], //etg_fecha
                $data[5], //lote
                $data[6], //etg_estado
                '1', //etg_procedencia
                $data[7], //etg_tamano
                $data[12], //etg_peso_neto
                $data[2],//etg_peso_bruto
                $data[20], //largo
                $data[21], //operador
                $data[22]); //observaciones
            if (!$Set->insert_etiqueta($dt_conf)) {
                $sms = 'insert_etiqueta' . pg_last_error();
            }
            if ($data[13] != '[object Window]' && $data[13] != '') {
                $dt_inconf = array(
                    $data[17], //ord_id
                    $data[18], //pro_id
                    $data[19], //cli_id
                    '1', //etg_tipo
                    $data[0], //etg_numero
                    $data[8], //etg_copias
                    $data[9], //etg_fecha
                    $data[15], //lote
                    $data[16], //etg_estado
                    '1', //etg_procedencia
                    $data[7], //etg_tamano
                    $data[14], //etg_peso_neto
                    $data[13],
                    '0',//etg_peso_bruto
                    $data[20],//largo
                    $data[21],//operador
                    $data[22]//observaciones
                    ); 
                if (!$Set->insert_etiqueta($dt_inconf)) {
                    $sms = 'insert_etiqueta2' . pg_last_error();
                }
            }
        }

        echo $sms;
        break;
    case 7:
        $sms = 0;
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
        $rst_ord = pg_fetch_array($Set->lista_regextrusion_rollo($data[0]));
        $dt_mov = Array(
            $data[1], //pro_id,
            '26', ///trs_id,
            $rst_ord[cli_id], //cli_id,
            '1', //bod_id,
            $retorno, //mov_documento,
            $rst_ord[ord_num_orden], // mov_guia_transporte,
            $data[2], //mov_fecha_trans,
            $data[3], //mov_cantidad,
            '0', //mov_tabla,
            $data[0], //mov_pago
            $rst_ord[rec_estado]//mov_flete(estado)
        );

        if (!$Set->insert_transferencia($dt_mov)) {
            $sms = 'insert_mov_elaborado' . pg_last_error();
        }
        $rst_pro = pg_fetch_array($Set->lista_un_producto($data[1]));
        echo $sms . '&' .
        $rst_pro[pro_ancho] * 1000 . '&' .
        $rst_pro[pro_largo] . '&' .
        $rst_pro[pro_espesor] . '&' .
        $rst_pro[pro_capa] . '&' .
        $rst_ord[ord_id] . '&' .
        $rst_pro[pro_id] . '&' .
        $rst_ord[cli_id] . '&' .
        $rst_ord[rec_estado] . '&' .
        $rst_ord[ord_etiqueta] . '&' .
        $rst_ord[ord_eti_numero] . '&' .
        $rst_ord[cli_raz_social] . '&' .
        $rst_ord[ord_num_orden];
        break;
}
?>
