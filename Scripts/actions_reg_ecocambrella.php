<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_reg_ecocambrella.php';
$Set = new Clase_reg_ecocambrella();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$s = $_REQUEST[s];
$std = $_REQUEST[std];
$fec = $_REQUEST[fec];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $cliente) {
                $str[$n] = strtoupper($cliente);
                $n++;
            }
            if ($Set->insert_registro_ecocambrella($str) == FALSE) {
                $sms = 'insert_reg' . pg_last_error();
            } else {
                $rst_ord = pg_fetch_array($Set->lista_una_orden($data[0]));
//                ///insertar producto en inventario semielaborado
                if ($data[6] == 3) {
                    $rst_ord[ord_bodega] = 1;
                }
                if ($rst_ord[ord_bodega] == 1) {
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
                        $data[7], //pro_id,
                        '26', ///trs_id,
                        $rst_ord[cli_id], //cli_id,
                        '1', //bod_id,
                        $retorno, //mov_documento,
                        $rst_ord[ord_num_orden], // mov_guia_transporte,
                        $data[1], //mov_fecha_trans,
                        $data[4], //mov_cantidad,
                        '1', //mov_tabla,
                        $data[3], //mov_pago
                        $data[6]//mov_flete(estado)
                    );

                    if (!$Set->insert_transferencia($dt_mov)) {
                        $sms = 'insert_mov_elaborado' . pg_last_error();
                    }
                    if ($data[6] == 3) ///cuando estado es inconforme
                        if ($Set->update_estado_mov($rst_ord[pro_id], $data[6], $data[5], $rst_ord[ord_num_orden]) == false) {
                            $sms = 'update estado movtotal' . pg_last_error();
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

                    $dt_mov = Array(
                        $data[7], //pro_id,
                        '26', ///trs_id,
                        $rst_ord[cli_id], //cli_id,
                        '1', //bod_id,
                        $retorno, //mov_documento,
                        $rst_ord[ord_num_orden], // mov_guia_transporte,
                        $data[1], //mov_fecha_trans,
                        $data[4], //mov_cantidad,
                        '1', //mov_tabla,(cantidad rollos)
                        $data[3], //mov_pago
                        $data[6]//mov_flete(estado)
                    );

                    if (!$Set->insert_transferencia_terminado($dt_mov)) {
                        $sms = 'insert_mov_terminado' . pg_last_error();
                    }
                }
                ///estados de pedido
                $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst_ord[ord_id], $rst_ord[pro_id]));
                $por = ($rst_prod[peso] * 100) / $rst_ord[ord_kgtotal];
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
                $modulo = 'REG. PRODUCCION ECOCAMBRELLA';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[8]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
//        else {
//            $str = $data;
//            $n = 0;
//            foreach ($str as $row => $cliente) {
//                $str[$n] = strtoupper($cliente);
//                $n++;
//            }
//            if ($Set->update_registro_ecocambrella($data, $id) == FALSE) {
//                $sms = pg_last_error();
//            } else {
//                $n = 0;
//                while ($n < count($fields)) {
//                    $f = $f . strtoupper($fields[$n] . '&');
//                    $n++;
//                }
//                $modulo = 'REG. PRODUCCION ECOCAMBRELLA';
//                $accion = 'MODIFICAR';
//                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[8]) == false) {
//                    $sms = "Auditoria" . pg_last_error() . 'ok2';
//                }
//            }
//        }
        $rst_pro = pg_fetch_array($Set->lista_un_producto($data[7]));
        echo $sms . '&' . $rst_pro[pro_ancho] * 1000 . '&' . $rst_pro[pro_largo] . '&' . $rst_pro[pro_espesor] . '&' . $rst_pro[pro_propiedad5] . '&' . $rst_ord[ord_id] . '&' . $rst_pro[pro_id] . '&' . $rst_ord[cli_id]. '&' . $rst_pro[pro_gramaje];
        break;
    case 1:
        $sms = 0;
        if ($Set->delete_registro_ecocambrella($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'REG. PRODUCCION ECOCAMBRELLA';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                $sms = "Auditoria" . pg_last_error();
            }
        }
        echo $sms;
        break;
    case 2:

        $rst = pg_fetch_array($Set->lista_una_orden_cod(trim(strtoupper($id))));
        $rst1 = pg_fetch_array($Set->lista_un_producto($rst[pro_id]));
        $rst2 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro_secundario]));
        $rst3 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro3]));
        $rst4 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro4]));
        $sumakg = $rst[ord_kgtotal] + $rst[ord_kgtotal2] + $rst[ord_kgtotal3] + $rst[ord_kgtotal4] + $rst[ord_kgtotal_rep];
        $sumam = $rst[ord_pri_ancho] * $rst[ord_pri_carril] + $rst[ord_sec_ancho] * $rst[ord_sec_carril] + $rst[ord_ancho3] * $rst[ord_carril3] + $rst[ord_ancho4] * $rst[ord_carril4] + $rst[ord_rep_ancho] * $rst[ord_rep_carril];
        if ($rst[ord_bodega] == 1) {
            $bodega = 'SEMIELABORADO';
        } else if ($rst[ord_bodega] == 2) {
            $bodega = 'TERMINADO';
        }
        $cns_mp = $Set->lista_mp($rst1[emp_id]);
        $combo = "<option  value='0'> - Seleccione - </option>";
        while ($rst_mp = pg_fetch_array($cns_mp)) {
            $combo.="<option value='$rst_mp[mp_id]'>$rst_mp[mp_referencia]</option>";
        }
        $cns_reg = $Set->registros_productos($rst[ord_id], $rst[pro_id], $rst[ord_pro_secundario], $rst[ord_pro3], $rst[ord_pro4]);
        $prod1 = array();
        $prod2 = array();
        $prod3 = array();
        $prod4 = array();
        while ($rst_reg = pg_fetch_array($cns_reg)) {
            $peso_conf = '';
            $peso_inconf = '';
            if ($rst_reg[rec_estado] == 0) {
                if (!empty($rst_reg[rec_peso_primario])) {
                    $peso_conf = str_replace(',', '', number_format($rst_reg[rec_peso_primario], 2));
                } else {
                    $peso_conf = '';
                }
            } else {
                if (!empty($rst_reg[rec_peso_primario])) {
                    $peso_inconf = str_replace(',', '', number_format($rst_reg[rec_peso_primario], 2));
                } else {
                    $peso_inconf = '';
                }
            }
            if ($rst_reg[prod] == 1) {
                array_push($prod1, $peso_conf . '&&' . $peso_inconf . '&&' . $rst_reg[pro_id] . '&&' . $rst_reg[rec_lote] . '&&' . $rst_reg[rec_estado]);
            }
            if ($rst_reg[prod] == 2) {
                array_push($prod2, $peso_conf . '&&' . $peso_inconf . '&&' . $rst_reg[pro_id] . '&&' . $rst_reg[rec_lote] . '&&' . $rst_reg[rec_estado]);
            }
            if ($rst_reg[prod] == 3) {
                array_push($prod3, $peso_conf . '&&' . $peso_inconf . '&&' . $rst_reg[pro_id] . '&&' . $rst_reg[rec_lote] . '&&' . $rst_reg[rec_estado]);
            }
            if ($rst_reg[prod] == 4) {
                array_push($prod4, $peso_conf . '&&' . $peso_inconf . '&&' . $rst_reg[pro_id] . '&&' . $rst_reg[rec_lote] . '&&' . $rst_reg[rec_estado]);
            }
            if (!empty($rst[ord_pro_secundario])) {
                $hid2 = '';
            } else {
                $hid2 = 'hidden';
            }

            if (!empty($rst[ord_pro3])) {
                $hid3 = '';
            } else {
                $hid3 = 'hidden';
            }

            if (!empty($rst[ord_pro4])) {
                $hid4 = '';
            } else {
                $hid4 = 'hidden';
            }
        }

        $count = array(
            count($prod1),
            count($prod2),
            count($prod3),
            count($prod4)
        );

        $i = 0;
        $j = 0;
        $conf1 = 0;
        $conf2 = 0;
        $conf3 = 0;
        $conf4 = 0;
        $inconf1 = 0;
        $inconf2 = 0;
        $inconf3 = 0;
        $inconf4 = 0;
        while ($j < max($count)) {
            $p1 = explode('&&', $prod1[$j]);
            $p2 = explode('&&', $prod2[$j]);
            $p3 = explode('&&', $prod3[$j]);
            $p4 = explode('&&', $prod4[$j]);
            $i++;
            $lista.= "<tr class='itm' style='height:20px'>
                        <td style='width: 10px' align='right'>$i</td>
                        <td style='width: 80px' align='right' id='valor1_$i'>$p1[0]</td>
                        <td style='width: 80px' align='right' id='valorinc1_$i'>$p1[1]</td>
                        <td style='width: 80px' hidden id='pro_id1_$i'>$p1[2]</td> 
                        <td style='width: 80px' hidden id='pro_lote1_$i'>$p1[3]</td> 
                        <td style='width: 80px' hidden id='pro_estado1_$i'>$p1[4]</td> 
                        <td $hid2 style='width: 80px' align='right' id='valor2_$i'>$p2[0]</td> 
                        <td $hid2 style='width: 80px' align='right' id='valorinc2_$i'>$p2[1]</td> 
                        <td style='width: 80px' hidden id='pro_id2_$i'>$p2[2]</td> 
                        <td style='width: 80px' hidden id='pro_lote2_$i'>$p2[3]</td> 
                        <td style='width: 80px' hidden id='pro_estado2_$i'>$p2[4]</td> 
                        <td $hid3 style='width: 80px' align='right' id='valor3_$i'>$p3[0]</td> 
                        <td $hid3 style='width: 80px' align='right' id='valorinc3_$i'>$p3[1]</td> 
                        <td style='width: 80px' hidden id='pro_id3_$i'>$p3[2]</td> 
                        <td style='width: 80px' hidden id='pro_lote3_$i'>$p3[3]</td> 
                        <td style='width: 80px' hidden id='pro_estado3_$i'>$p3[4]</td> 
                        <td $hid4 style='width: 80px' align='right' id='valor4_$i'>$p4[0]</td> 
                        <td $hid4 style='width: 80px' align='right' id='valorinc4_$i'>$p4[1]</td> 
                        <td style='width: 80px' hidden id='pro_id4_$i'>$p4[2]</td> 
                        <td style='width: 80px' hidden id='pro_lote4_$i'>$p4[3]</td> 
                        <td style='width: 80px' hidden id='pro_estado4_$i'>$p4[4]</td>
                    </tr>";
            $j++;
            $conf1+=$p1[0];
            $conf2+=$p2[0];
            $conf3+=$p3[0];
            $conf4+=$p4[0];
            $inconf1+=$p1[1];
            $inconf2+=$p2[1];
            $inconf3+=$p3[1];
            $inconf4+=$p4[1];
        }
//        $sms = $rst[ord_id] . '&' . $rst[ord_num_orden] . '&' . $rst1[pro_descripcion] . '&' . $rst2[pro_descripcion] . '&' . $tot_peso_pri . '&' . $rst[ord_num_rollos] . '&' . $rst_prod[peso] . '&' . $rst_prod[peso2] . '&' . $rst_prod[rollo] . '&' . $rst_prod[rollo2] . '&' . $tot_peso_sec;
        echo $rst[ord_id] . '&' .
        $rst[ord_num_orden] . '&' .
        $rst[ord_fec_pedido] . '&' .
        $rst[ord_fec_entrega] . '&' .
        $rst[ord_bodega] . '&' .
        $bodega . '&' .
        $rst[cli_raz_social] . '&' .
        $rst1[pro_descripcion] . '&' .
        $rst[ord_num_rollos] . '&' .
        str_replace(',', '', number_format($rst[ord_kgtotal], 2)) . '&' .
        str_replace(',', '', number_format($rst[ord_pri_ancho], 2)) . '&' .
        $rst[ord_pri_carril] . '&' .
        $rst2[pro_descripcion] . '&' .
        $rst[ord_num_rollos2] . '&' .
        str_replace(',', '', number_format($rst[ord_kgtotal2], 2)) . '&' .
        str_replace(',', '', number_format($rst[ord_sec_ancho], 2)) . '&' .
        $rst[ord_sec_carril] . '&' .
        $rst3[pro_descripcion] . '&' .
        $rst[ord_num_rollos3] . '&' .
        str_replace(',', '', number_format($rst[ord_kgtotal3], 2)) . '&' .
        str_replace(',', '', number_format($rst[ord_ancho3], 2)) . '&' .
        $rst[ord_carril3] . '&' .
        $rst4[pro_descripcion] . '&' .
        $rst[ord_num_rollos4] . '&' .
        str_replace(',', '', number_format($rst[ord_kgtotal4], 2)) . '&' .
        str_replace(',', '', number_format($rst[ord_ancho4], 2)) . '&' .
        $rst[ord_carril4] . '&' .
        str_replace(',', '', number_format($rst[ord_kgtotal_rep], 2)) . '&' .
        str_replace(',', '', number_format($rst[ord_rep_ancho], 2)) . '&' .
        $rst[ord_rep_carril] . '&' .
        str_replace(',', '', number_format($sumakg, 2)) . '&' .
        str_replace(',', '', number_format($sumam, 2)) . '&' .
        $rst[pro_id] . '&' .
        $rst[ord_pro_secundario] . '&' .
        $rst[ord_pro3] . '&' .
        $rst[ord_pro4] . '&' .
        $rst[ord_por_tornillo1] . '&' .
        $rst[ord_por_tornillo2] . '&' .
        $rst[ord_por_tornillo3] . '&' .
        $combo . '&' .
        $rst[ord_mp1] . '&' .
        $rst[ord_mp2] . '&' .
        $rst[ord_mp3] . '&' .
        $rst[ord_mp4] . '&' .
        $rst[ord_mp5] . '&' .
        $rst[ord_mp6] . '&' .
        $rst[ord_mp7] . '&' .
        $rst[ord_mp8] . '&' .
        $rst[ord_mp9] . '&' .
        $rst[ord_mp10] . '&' .
        $rst[ord_mp11] . '&' .
        $rst[ord_mp12] . '&' .
        $rst[ord_mp13] . '&' .
        $rst[ord_mp14] . '&' .
        $rst[ord_mp15] . '&' .
        $rst[ord_mp16] . '&' .
        $rst[ord_mp17] . '&' .
        $rst[ord_mp18] . '&' .
        $rst[ord_mf1] . '&' .
        $rst[ord_mf2] . '&' .
        $rst[ord_mf3] . '&' .
        $rst[ord_mf4] . '&' .
        $rst[ord_mf5] . '&' .
        $rst[ord_mf6] . '&' .
        $rst[ord_mf7] . '&' .
        $rst[ord_mf8] . '&' .
        $rst[ord_mf9] . '&' .
        $rst[ord_mf10] . '&' .
        $rst[ord_mf11] . '&' .
        $rst[ord_mf12] . '&' .
        $rst[ord_mf13] . '&' .
        $rst[ord_mf14] . '&' .
        $rst[ord_mf15] . '&' .
        $rst[ord_mf16] . '&' .
        $rst[ord_mf17] . '&' .
        $rst[ord_mf18] . '&' .
        $rst[ord_kg1] . '&' .
        $rst[ord_kg2] . '&' .
        $rst[ord_kg3] . '&' .
        $rst[ord_kg4] . '&' .
        $rst[ord_kg5] . '&' .
        $rst[ord_kg6] . '&' .
        $rst[ord_kg7] . '&' .
        $rst[ord_kg8] . '&' .
        $rst[ord_kg9] . '&' .
        $rst[ord_kg10] . '&' .
        $rst[ord_kg11] . '&' .
        $rst[ord_kg12] . '&' .
        $rst[ord_kg13] . '&' .
        $rst[ord_kg14] . '&' .
        $rst[ord_kg15] . '&' .
        $rst[ord_kg16] . '&' .
        $rst[ord_kg17] . '&' .
        $rst[ord_kg18] . '&' .
        $lista . '&' .
        $conf1 . '&' .
        $conf2 . '&' .
        $conf3 . '&' .
        $conf4 . '&' .
        $inconf1 . '&' .
        $inconf2 . '&' .
        $inconf3 . '&' .
        $inconf4 . '&' .
        $rst1[pro_peso] . '&' .
        $rst2[pro_peso] . '&' .
        $rst3[pro_peso] . '&' .
        $rst4[pro_peso] . '&' .
        $rst1[pro_largo] . '&' .
        $rst2[pro_largo] . '&' .
        $rst3[pro_largo] . '&' .
        $rst4[pro_largo] . '&' .
        $rst[ord_patch1] . '&' .
        $rst[ord_patch2] . '&' .
        $rst[ord_patch3] . '&' .
        $rst[ord_etiqueta] . '&' .
        $rst[ord_observaciones] . '&' .
        $rst[ord_eti_numero];
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
        ser_flush(true,true);
        ser_open("COM1", 9600, 8, "None", "1", "None");
        sleep(1);
        $num="";
        for ($i = 0; $i < 9; $i++) {
            $j = ser_readbyte();
            $num.= sprintf("%c", $j);
        }
        ser_close();
        $resultado = preg_replace('/[^0-9.]+/', '', $num); 
        echo $resultado;
//        
//        ////implementar funcion lectura de puerto serial
//        echo $sms; /// retorno peso   
        break;

    case 5:
        $sms = 0;
        if (empty($id)) {
            if ($Set->insert_etiqueta_pallets($data) == FALSE) {
                $sms = 'insert_reg' . pg_last_error();
            }
        } else {
            if ($Set->update_etiqueta_pallets($data, $id) == FALSE) {
                $sms = 'update_reg' . pg_last_error();
            }
        }
        echo $sms;
        break;

    case 6:
        $sms = 0;
        if (!empty($id) && !empty($data)) {
            $rst = pg_fetch_array($Set->lista_pallets($id, $data));
            if ($rst[pal_estado] == 0) {
//                $rst_sum = pg_fetch_array($Set->lista_pallets_suma($id, $data));
                if (empty($rst[pal_numero])) {
                    $rst[pal_numero] = 0;
                } else {
                    $rst[pal_numero] = $rst[pal_numero];
                }
                echo $rst[pal_numero] . '&0&0&0&0';
            } else {
                echo $rst[pal_numero] . '&' . $rst[pal_rollos] . '&' . $rst[pal_peso] . '&' . $rst[pal_estado] . '&' . $rst[pal_id];
            }
        }

        break;

    case 7:
        $sms = 0;
        if (!$Set->insert_etiqueta($data)) {
            $sms = 'insert_etiqueta' . pg_last_error();
        }
        echo $sms;
        break;
}
?>
