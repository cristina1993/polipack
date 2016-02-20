<?php

//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_factura.php';
include_once("../Clases/clsAuditoria.php");
$Adt = new Auditoria();
$Set = new Clase_factura();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$data4 = $_REQUEST[data3];
$id = $_REQUEST[id];
$tbl = $_REQUEST[tbl]; //tbl
$s = $_REQUEST[s]; //tbl
$sts = $_REQUEST[sts];
$user = $_SESSION[usuid];
$fields = $_REQUEST[fields]; //Field Name
$nom = $_REQUEST[nom]; //Field Name
$usu = $_REQUEST[usu]; //Fiel
switch ($op) {

    ///// FACTURA NUEVO/////

    case 0:
        if ($s == 0) {
            $cns = $Set->lista_clientes_search(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[cli_raz_social];
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
        } else {
            $sms;
            $rst = pg_fetch_array($Set->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . ' ' . $rst[cli_numeracion] . ' ' . $rst[cli_calle_sec] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_parroquia] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst[cli_id] . '&' . $rst[cli_tipo_cliente] . '&' . $rst[cli_estado];
            }
            echo $sms;
        }

        break;

    case 1:
        $rst_em = pg_fetch_array($Set->lista_un_emisor($s));
        $em = $rst_em[cod_orden];
        if (strlen($_REQUEST[lt]) >= 8) {
            $tabla = 1;
            $rst = pg_fetch_array($Set->lista_un_producto_noperti_cod_lote($id, $_REQUEST[lt]));
            $rst_precio1 = pg_fetch_array($Set->lista_precio_producto($rst[id], $tabla));
            if ($rst_precio1[pre_vald_precio1] == 1) {
                $rst_precio1[pre_precio] = $rst_precio1[pre_precio];
            } else {
                $rst_precio1[pre_precio] = $rst_precio1[pre_precio2];
            }
            $rst_desc = pg_fetch_array($Set->lista_descuento_producto($rst_precio1[pre_id], $em));
            $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fac($rst[id], $s, '1'));
            $inv = $rst1[ingreso] - $rst1[egreso];
            echo $rst[pro_a] . '&' . $rst[pro_b] . '&' . $rst[pro_uni] . '&' . $rst_precio1[pre_precio] . '&' . $rst_precio1[pre_iva] . '&' . $rst_desc[dsc_descuento] . '&' . $rst_precio1[pre_ice] . '&' . $rst[pro_ad] . '&' . $rst[pro_ac] . '&' . $rst[id] . '&1&' . $inv;
        } else {
            $tbl = substr($id, 0, 1);
            $id = substr($id, 1, (strlen($id) - 1));
            if ($tbl == 1) {
                $rst = pg_fetch_array($Set->lista_un_producto_noperti_id($id));
                if ($rst[id] != '') {
                    $rst_precio1 = pg_fetch_array($Set->lista_precio_producto($rst[id], $tbl));
                    if ($rst_precio1[pre_vald_precio1] == 1) {
                        $rst_precio1[pre_precio] = $rst_precio1[pre_precio];
                    } else {
                        $rst_precio1[pre_precio] = $rst_precio1[pre_precio2];
                    }

                    $rst_desc = pg_fetch_array($Set->lista_descuento_producto($rst_precio1[pre_id], $em));
                    $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fac($rst[id], $s, '1'));
                    $inv = $rst1[ingreso] - $rst1[egreso];
                    echo $rst[pro_a] . '&' . $rst[pro_b] . '&' . $rst[pro_uni] . '&' . $rst_precio1[pre_precio] . '&' . $rst_precio1[pre_iva] . '&' . $rst_desc[dsc_descuento] . '&' . $rst_precio1[pre_ice] . '&' . $rst[pro_ad] . '&' . $rst[pro_ac] . '&' . $rst[id] . '&1&' . $inv;
                }
            } else {
                $rst = pg_fetch_array($Set->lista_un_producto_industrial_id($id));
                if ($rst[pro_id] != '') {
//                    if (($rst[emp_id] == 3 || $rst[emp_id] == 4) && $s == 1) {
//                        $s = 10;
//                    }
                    $rst_precio = pg_fetch_array($Set->lista_precio_producto($rst[pro_id], $tbl));
                    if ($rst_precio[pre_vald_precio1] == 1) {
                        $rst_precio[pre_precio] = $rst_precio[pre_precio];
                    } else {
                        $rst_precio[pre_precio] = $rst_precio[pre_precio2];
                    }
                    $rst_desc = pg_fetch_array($Set->lista_descuento_producto($rst_precio[pre_id], $em));
                    $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fac($rst[pro_id], $s, '0'));
                    $inv = $rst1[ingreso] - $rst1[egreso];
                    echo $rst[pro_codigo] . '&' . $rst[pro_descripcion] . '&' . $rst[pro_uni] . '&' . $rst_precio[pre_precio] . '&' . $rst_precio[pre_iva] . '&' . $rst_desc[dsc_descuento] . '&' . $rst_precio[pre_ice] . '& & &' . $rst[pro_id] . '&0&' . $inv;
                    ;
                }
            }
        }
        break;
    case 2:
        $sms = 0;
        $aud = 0;
        if (empty($id)) {// Insertar
            if (!empty($data[1])) {
                $data3 = array(
                    strtoupper($data[9]),
                    strtoupper($data[8]),
                    strtoupper($data[20]),
                    strtoupper($data[22]),
                    strtoupper($data[23]),
                    strtoupper($data[24])
                );
                if ($Set->upd_email_cliente($data3, $data[7]) == false) {
                    $sms = 'Insert_email' . pg_last_error() . $data[17] . '&' . $data[18] . '&' . $data[19] . '&' . $data[20] . '&' . $data[21] . '&' . $data[24];
                }
                $cli = $data[1];
            } else {
                if (strlen($data[7]) < 11) {
                    $tipo = 'CN';
                } else {
                    $tipo = 'CJ';
                }
                $rst_cod = pg_fetch_array($Set->lista_secuencial_cliente($tipo));
                $sec = (substr($rst_cod[cli_codigo], 2, 6) + 1);

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

                $retorno = $tipo . $txt . $sec;

                $da = array(
                    strtoupper($data[6]),
                    strtoupper($data[7]),
                    strtoupper($data[9]),
                    strtoupper($data[20]),
                    strtoupper($data[8]),
                    strtoupper($data[22]),
                    strtoupper($data[23]),
                    $retorno,
                    strtoupper($data[24])
                );


                if ($Set->insert_cliente($da) == false) {
                    $sms = 'Insert_cli' . pg_last_error();
                    $aud = 1;
                }
                $rst_cl = pg_fetch_array($Set->lista_clientes_codigo(strtoupper($data[7])));
                $cli = $rst_cl[cli_id];
            }


//            if ($data[0] >= 10) {
//                $ems = '0' . $data[0];
//            } else {
//                $ems = '00' . $data[0];
//            }
//            $rst_sec = pg_fetch_array($Set->lista_secuencial_documento($ems));
//            $sec = ($rst_sec[secuencial] + 1);
//            if ($sec >= 0 && $sec < 10) {
//                $tx = '00000000';
//            } else if ($sec >= 10 && $sec < 100) {
//                $tx = '0000000';
//            } else if ($sec >= 100 && $sec < 1000) {
//                $tx = '000000';
//            } else if ($sec >= 1000 && $sec < 10000) {
//                $tx = '00000';
//            } else if ($sec >= 10000 && $sec < 100000) {
//                $tx = '0000';
//            } else if ($sec >= 100000 && $sec < 1000000) {
//                $tx = '000';
//            } else if ($sec >= 1000000 && $sec < 10000000) {
//                $tx = '00';
//            } else if ($sec >= 10000000 && $sec < 100000000) {
//                $tx = '0';
//            } else if ($sec >= 100000000 && $sec < 1000000000) {
//                $tx = '';
//            }
//            $secuencial = $ems . '-001-' . $tx . $sec;


            $rst_ulti_sec = pg_fetch_array($Set->lista_secuencial_locales($data[0]));
            if ($data[5] == $rst_ulti_sec[secuencial]) {
                $sms = 1;
            } else {
                $j = 0;
                $str = $data;
                foreach ($str as $row => $factura) {
                    $str[$j] = strtoupper($factura);
                    $j++;
                }

                if ($Set->insert_factura($str, $cli) == false) { ///// secuanecial 1 listo
                    $sms = 'Insert' . pg_last_error();
                    $accion = 'Insertar';
                    $aud = 1;
                } else {
                    $rst_fac = pg_fetch_array($Set->lista_una_factura_num($data[5])); ////// secuencial 2 listo 
                    $fac_id = $rst_fac[fac_id];
                    $dt1 = explode('&', $data4[0]);
                    if ($dt1[0] == 1 || $dt1[0] == 10) {
                        $m = 0;
                        $i = count($data4);
                        $pg = 1;
                        $pagos1 = '';
                        while ($m < $i) {
                            $dt1 = explode('&', $data4[$m]);
                            $pagos1.="INSERT INTO erp_pagos_factura(com_id,pag_tipo,pag_porcentage, pag_dias,pag_valor,pag_fecha_v,pag_forma,pag_banco,pag_tarjeta,pag_cant,pag_contado)
                                values ('$fac_id','$pg','$dt1[1]','$dt1[2]','$dt1[3]','$dt1[4]','0','0','0','0','0');";
                            $m++;
                        }
                    } else {
                        $dt1 = explode('&', $data4[0]);
                        $m = 0;
                        $pagos1 = '';
                        $i = count($data4);
                        while ($m < $i) {
                            $dt1 = explode('&', $data4[$m]);
                            $pg = 0;
                            $fec = $data[4];

                            if ($dt1[1] != 0) {
                                $pagos1.="INSERT INTO erp_pagos_factura(com_id,pag_tipo,pag_porcentage, pag_dias,pag_valor,pag_fecha_v,pag_forma,pag_banco,pag_tarjeta,pag_cant,pag_contado)
                                values ('$fac_id','$pg','0','0','0','$fec','$dt1[1]','$dt1[2]','$dt1[3]','$dt1[4]','$dt1[5]');";
                            }
                            $m++;
                        }
                    }
                    if ($Set->insert_pagos($pagos1) == false) {
                        $sms = 'Insert_pagos2' . pg_last_error();
                        $aud = 1;
                    }
                    $n = 0;
                    $i = count($data2);
                    while ($n < $i) {
                        $dt = explode('&', $data2[$n]);
                        $num_sec = str_replace('-', '', $data[5]); ///// secuencial 3 listo
                        $detalles.="INSERT INTO erp_det_factura(fac_id,pro_id,dfc_codigo,dfc_cod_aux,dfc_cantidad,dfc_descripcion,dfc_precio_unit,
                                                            dfc_porcentaje_descuento,dfc_val_descuento,dfc_precio_total,dfc_iva,dfc_ice,dfc_p_ice, 
                                                            dfc_cod_ice,dfc_irbpnr,dfc_p_irbpnr,dfc_lote,dfc_tab)
                                              VALUES ($fac_id,'$dt[0]','$dt[1]','$dt[2]','$dt[3]','$dt[4]','$dt[5]','$dt[6]','$dt[7]','$dt[8]','$dt[9]',
                                                   '$dt[10]','$dt[14]','$dt[15]','$dt[13]','$dt[16]','$dt[17]','$dt[18]');";

                        $n++;
                        if ($data[27] == 0) {
                            $estp = '4';
                        } else {
                            $estp = '3';
                        }
                        if ($Set->lista_cambia_status_det($data[3], $estp, $dt[0]) == false) {
                            $sms = pg_last_error();
                        }
                    }
                    if ($Set->insert_detalle_factura($detalles) == false) {
                        $sms = 'Insert_det' . pg_last_error();
                        $aud = 1;
                    } else {
                        $l = 0;
                        $h = count($data2);
                        while ($l < $h) {
                            $dtm = explode('&', $data2[$l]);
                            $bod = $data[0];
                            $tab = $dtm[18];
                            if ($tab == 0) {
                                $rst_pro = pg_fetch_array($Set->lista_un_producto_industrial_id($dtm[0]));
//                            if (($rst_pro[emp_id] == 3 || $rst_pro[emp_id] == 4) && $data[0] == 1) {
//                                $bod = '10';
//                            } else {
                                $bod = $data[0];
//                            }
                            }

                            $fec_mov = date('Y-m-d');
                            $hor_mov = date('H:i:s');
                            $movimientos.="INSERT INTO erp_i_mov_inv_pt(pro_id,trs_id,cli_id,bod_id,mov_documento,mov_guia_transporte, 
                                                                    mov_num_trans,mov_fecha_trans,mov_fecha_registro,mov_hora_registro, 
                                                                    mov_cantidad,mov_tranportista,mov_fecha_entrega,mov_num_factura, 
                                                                    mov_pago,mov_direccion,mov_val_unit,mov_descuento,mov_iva,mov_flete,
                                                                    mov_tabla,mov_usuario)
                                       values($dtm[0],25,$cli,$bod,'$num_sec','0','0','$fec_mov','$fec_mov','$hor_mov','$dtm[3]','','$fec_mov','$num_sec','','','$dtm[11]','0','0','0','$dtm[18]','$data[26]');";
                            $l++;
                        }


                        if ($Set->insert_movimiento_pt($movimientos) == false) {
                            $sms = 'Insert_mov' . pg_last_error();
                            $aud = 1;
                        }
                    }

                    ////pedido venta cambio de estado
                    if ($aud == 0) {
                        if ($data[27] == 0) {
                            $estp = '4';
                        } else {
                            $estp = '3';
                        }
                        if ($Set->lista_cambia_status($data[3], $estp) == false) {
                            $sms = pg_last_error();
                        }
                    }

                    if ($aud == 0) {
                        $n = 0;
                        while ($n < count($fields)) {
                            if ($n == 0) {
                                $f = $f . "NUM_SECUENCIAL=$data[5]&"; //// secuencial 4 listo
                            } else {
                                $f = $f . strtoupper($fields[$n] . '&');
                            }
                            $n++;
                        }
                        $modulo = 'FACTURA';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[5]) == false) { ///// secuencial 5 listo
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            }
        }
//        else {// Modificar
//            if (!empty($data[1])) {
//                $data3 = array(
//                    strtoupper($data[9]),
//                    strtoupper($data[8]),
//                    strtoupper($data[20]),
//                    strtoupper($data[22]),
//                    strtoupper($data[23]),
//                    strtoupper($data[24])
//                );
//                if ($Set->upd_email_cliente($data3, $data[7]) == false) {
//                    $sms = 'Insert_email' . pg_last_error() . $data[17] . '&' . $data[18] . '&' . $data[19] . '&' . $data[20] . '&' . $data[21] . '&' . $data[24];
//                }
//                $cli = $data[1];
//            } else {
//                if (strlen($data[7]) < 11) {
//                    $tipo = 'CN';
//                } else {
//                    $tipo = 'CJ';
//                }
//                $rst_cod = pg_fetch_array($Set->lista_secuencial_cliente($tipo));
//                $sec = (substr($rst_cod[cli_codigo], 2, 6) + 1);
//
//                if ($sec >= 0 && $sec < 10) {
//                    $txt = '0000';
//                } else if ($sec >= 10 && $sec < 100) {
//                    $txt = '000';
//                } else if ($sec >= 100 && $sec < 1000) {
//                    $txt = '00';
//                } else if ($sec >= 1000 && $sec < 10000) {
//                    $txt = '0';
//                } else if ($sec >= 10000 && $sec < 100000) {
//                    $txt = '';
//                }
//
//                $retorno = $tipo . $txt . $sec;
//
//                $da = array(
//                    strtoupper($data[6]),
//                    strtoupper($data[7]),
//                    strtoupper($data[9]),
//                    strtoupper($data[20]),
//                    strtoupper($data[8]),
//                    strtoupper($data[22]),
//                    strtoupper($data[23]),
//                    $retorno,
//                    strtoupper($data[24])
//                );
//                if ($Set->insert_cliente($da) == false) {
//                    $sms = 'Insert_cli' . pg_last_error();
//                    $aud = 1;
//                }
//                $rst_cl = pg_fetch_array($Set->lista_clientes_codigo($data[7]));
//                $cli = $rst_cl[cli_id];
//            }
//            $j = 0;
//            $str = $data;
//            foreach ($str as $row => $factura) {
//                $str[$j] = strtoupper($factura);
//                $j++;
//            }
//            if ($Set->update_factura($str, $cli, $id) == false) {
//                $sms = 'Update' . pg_last_error();
//                $accion = 'Update';
//                $aud = 1;
//            } else {
//                $num_factura = str_replace('-', '', $data[5]);
//                if ($Set->elimina_movpt_documento($num_factura) == false) {
//                    $sms = 'del' . pg_last_error();
//                    $aud = 1;
//                } else {
//                    if ($Set->elimina_detalle_factura($id) == true) {
//                        if ($Set->delete_pagos($id) == false) {
//                            $sms = 'Delete_pagos1' . pg_last_error();
//                            $aud = 1;
//                        }
//                    } else {
//                        $sms = 'del_det' . pg_last_error();
//                        $aud = 1;
//                    }
//                }
//
//                $fac_id = $id;
//                $dt1 = explode('&', $data4[0]);
//                if ($dt1[0] == 1 || $dt1[0] == 10) {
//                    $m = 0;
//                    $i = count($data4);
//                    $pg = 1;
//                    while ($m < $i) {
//                        $dt1 = explode('&', $data4[$m]);
//                        $data5 = array(
//                            $fac_id,
//                            $pg,
//                            $dt1[1],
//                            $dt1[2],
//                            $dt1[3],
//                            $dt1[4],
//                            0,
//                            0,
//                            0,
//                            0,
//                            0
//                        );
//                        if ($Set->insert_pagos($data5) == false) {
//                            $sms = 'Insert_pagos1' . pg_last_error();
//                            $aud = 1;
//                        }
//                        $m++;
//                    }
//                } else {
//                    $dt1 = explode('&', $data4[0]);
//                    $m = 0;
//                    $i = count($data4);
//                    while ($m < $i) {
//                        $dt1 = explode('&', $data4[$m]);
//                        $pg = 0;
//                        $fec = $data[4];
//                        $data5 = array(
//                            $fac_id, //com_id
//                            $pg,
//                            0,
//                            0,
//                            0,
//                            $fec,
//                            $dt1[1],
//                            $dt1[2],
//                            $dt1[3],
//                            $dt1[4],
//                            $dt1[5]
//                        );
//                        if ($dt1[1] != 0) {
//                            if ($Set->insert_pagos($data5) == false) {
//                                $sms = 'Insert_pagos2' . pg_last_error();
//                                $aud = 1;
//                            }
//                        }
//                        $m++;
//                    }
//                }
//
//                $n = 0;
//                $i = count($data2);
//                while ($n < $i) {
//                    $dt = explode('&', $data2[$n]);
//                    if ($Set->insert_detalle_factura($dt, $fac_id) == false) {
//                        $sms = 'Insert_det' . pg_last_error();
//                        $aud = 1;
//                    } else {
//                        $bod = $data[0];
//                        $tab = $dt[18];
//                        if ($tab == 0) {
//                            $rst_pro = pg_fetch_array($Set->lista_un_producto_industrial_id($dt[0]));
//                            if (($rst_pro[emp_id] == 3 || $rst_pro[emp_id] == 4) && $data[0] == 1) {
//                                $bod = '10';
//                            } else {
//                                $bod = $data[0];
//                            }
//                        }
//                        $dat = array(
//                            $dt[0],
//                            25,
//                            $cli,
//                            $bod, ///BODEGA
//                            $num_factura,
//                            '0',
//                            '0',
//                            date('Y-m-d'),
//                            date('Y-m-d'),
//                            date('H:i:s'),
//                            $dt[3], //cantidad
//                            '',
//                            date('Y-m-d'),
//                            $num_factura,
//                            '',
//                            '',
//                            $dt[11],
//                            0,
//                            0,
//                            0,
//                            $dt[18],
//                            $data[26]
//                        );
//
//                        if ($Set->insert_movimiento_pt($dat) == false) {
//                            $sms = 'Insert_mov' . pg_last_error();
//                            $aud = 1;
//                        }
//                    }
//                    $n++;
//                }
//            }
//            if ($aud == 0) {
//                $n = 0;
//                while ($n < count($fields)) {
//                    $f = $f . strtoupper($fields[$n] . '&');
//                    $n++;
//                }
//                $modulo = 'FACTURA';
//                $accion = 'MODIFICAR';
//                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[5]) == false) {
//                    $sms = "Auditoria" . pg_last_error() . 'ok2';
//                }
//            }
//        }
        $rst_com = pg_fetch_array($Set->lista_una_factura_id($fac_id));
        echo $sms . '&' . $rst_com[fac_id] . '&' . $mesaje;
        break;

    case 3:
        $sms = 0;
        if ($Set->upd_fac_na($_REQUEST[na], $_REQUEST[fh], $id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
}
?>
