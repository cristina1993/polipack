<?php

include_once '../Clases/clsClase_ord_pedido_venta.php';
include_once("../Clases/clsAuditoria.php");
$Adt = new Auditoria();
$Reg = new Clase_ord_pedido_venta();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$detalle = $_REQUEST[detalle];
$pagos = $_REQUEST[pagos];
$id = $_REQUEST[id];
$s = $_REQUEST[s];
$sts = $_REQUEST[sts];
$emisor = $_REQUEST[emi];
$fields = $_REQUEST[fields];
switch ($op) {
    case 100:
        if ($Reg->insert_asiento_mp($_REQUEST[sbt_mp]) == false) {
            $sms = 'Asiento ' . pg_last_error();
        }
        //echo $sms;       
        break;
    case 0:
        $sms = 0;
        if ($id == 0) { //Insertar
            $rst_ulti_sec = pg_fetch_array($Reg->lista_ultimo_registro_sec());
            if ($data[1] == $rst_ulti_sec[ped_num_registro]) {
                $sms = 1;
            } else {


                $num = $data[1];
                $rstcliente = pg_num_rows($Reg->lista_un_cliente_cedula($data[4]));
                if ($rstcliente == '') {
                    if (strlen($data[4]) < 11) {
                        $tipo = 'CN';
                    } else {
                        $tipo = 'CJ';
                    }
                    $rst_cod = pg_fetch_array($Reg->lista_secuencial_cliente($tipo));
                    $sec = (substr($rst_cod[cli_codigo], -5) + 1);

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
                        strtoupper($data[5]),
                        strtoupper($data[4]),
                        strtoupper($data[6]),
                        strtoupper($data[7]),
                        strtoupper($data[8]),
                        strtoupper($data[10]),
                        strtoupper($data[11]),
                        $retorno,
                        strtoupper($data[9])
                    );
                    if ($Reg->insert_cliente($da) == false) {
                        $sms = 'Insert_cli' . pg_last_error();
                        $v = 1;
                        $aud = 1;
                    }
                }

                $rst_ruc_cli = pg_fetch_array($Reg->lista_un_ruc_cliente($data[4]));

                if ($Reg->insert_registro_pedido($data, $rst_ruc_cli[cli_id], $rst_ruc_cli[cli_tipo]) == true) {
                    $rst_reg = pg_fetch_array($Reg->lista_registro_numero($data[1]));
                    foreach ($detalle as $row => $data) {
                        $det = explode('&', $data);
                        array_push($det, $rst_reg[ped_id]);
                        if ($Reg->insert_detalle_registro_pedido($det) == false) {
                            $sms = 'Detalle1 ' . pg_last_error();
                        }
                    }
                    foreach ($pagos as $row => $data) {
                        $pag = explode('&', $data);
                        array_push($pag, $rst_reg[ped_id]);
                        if ($Reg->insert_pagos_registro($pag) == false) {
                            $sms = 'Pagos ' . pg_last_error();
                        }
                    }

//                    $rst_ped_id = pg_fetch_array($Reg->lista_una_ordped_id($num));
//                    $estd = 1;
//                    $id = $rst_ped_id[sec_id];
//                    if ($Reg->upd_sec_ord_ped_venta($id, $estd) == FALSE) {
//                        $sms = 'udp_estado_save id=' . $id . pg_last_error();
//                    }

//                    $cns_det_ped = $Reg->lista_detalle_registro_pedido($id);
//                    while ($rst_dp = pg_fetch_array($cns_det_ped)) {
//                        
//                    }

                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'PEDIDO DE VENTA';
                    $accion = 'INSERTAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $num) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                } else {
                    $sms = "Reg " . pg_last_error();
                }
            }
        } else { //Modificar
            $num = $data[1];
            if ($Reg->upd_registro_pedido($data, $id) == true) {
                if ($Reg->elimina_detalle_pagos_pedido($id) == true) {
                    foreach ($detalle as $row => $data) {
                        $det = explode('&', $data);
                        array_push($det, $id);
                        if ($Reg->insert_detalle_registro_pedido($det) == false) {
                            $sms = 'Detalle2 ' . pg_last_error();
                        }
                    }
                    foreach ($pagos as $row => $data) {
                        $pag = explode('&', $data);
                        array_push($pag, $id);
                        if ($Reg->insert_pagos_registro($pag) == false) {
                            $sms = 'Pagos ' . pg_last_error();
                        }
                    }
                }
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'PEDIDO DE VENTA';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $num) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            } else {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($Reg->elimina_registro_detalle_pedido_id($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $modulo = 'PEDIDO DE VENTA';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 2:
//        if ($id == 1) {
//            $cns = $Reg->lista_productos_noperti();
//        } else if ($id == 10) {
//            $cns = $Reg->lista_productos_industrial();
//        } else {
            $cns = $Reg->lista_productos_todos();
//        }
//        $rst_emi = pg_fetch_array($Reg->lista_emisor($id));
        while ($rst = pg_fetch_array($cns)) {
//            if ($id == 1) {
//                $rst[tbl];
//                $rst[id];
//                $rst[codigo];
//                $rst[lote];
//                $rst[descripcion];
//            } elseif ($id == 10) {
//                $rst[tbl];
//                $rst[id];
//                $rst[codigo];
//                $rst[lote];
//                $rst[descripcion];
//            } else {
//                $rst[tbl];
//                $rst[id];
//                $rst[codigo];
//                $rst[lote];
//                $rst[descripcion];
//            }
            $producto.= "<option value='$rst[pro_id]'>$rst[pro_codigo] $rst[pro_descripcion]</option>";
//            $producto.= "<option value='$rst[tbl]$rst[id]'>$rst[lote] $rst[codigo] $rst[descripcion]</option>";
        }
//        echo $producto . '&&' . $rst_emi[cod_cli];
        echo $producto . '&&1';
        break;
    case 3:
        $sms = 0;
        $rst_reg = pg_fetch_array($Reg->lista_un_pedido_venta($id));
        $nombre = $rst_reg[ped_nom_cliente];
        $identificacion = $rst_reg[ped_ruc_cc_cliente];
        $femision = str_replace('-', '', $rst_reg[ped_femision]);
        $subtotal12 = str_replace(',', '', number_format($rst_reg[ped_sbt12], 4));
        $subtotal0 = str_replace(',', '', number_format($rst_reg[ped_sbt0], 4));
        $subtotal_exento_iva = str_replace(',', '', number_format($rst_reg[ped_sbt_excento], 4));
        $subtotal_no_objeto_iva = str_replace(',', '', number_format($rst_reg[ped_sbt_noiva], 4));
        $total_descuento = str_replace(',', '', number_format($rst_reg[ped_tdescuento], 4));
        $total_iva = str_replace(',', '', number_format($rst_reg[ped_iva12], 4));
        $total_valor = str_replace(',', '', number_format($rst_reg[ped_total], 4));
        $direccion_cliete = $rst_reg[ped_dir_cliente];
        $email_cliente = $rst_reg[ped_email_cliente];
        $telefono_cliente = $rst_reg[ped_tel_cliente];
        $cli_ciudad = $rst_reg[ped_ciu_cliente];
        $cli_pais = $rst_reg[ped_pais_cliente];
        $cod_punto_emision = $rst_reg[ped_local];
        $cli_parroquia = $rst_reg[ped_parroquia_cliente];
        $vendedor = $rst_reg[ped_vendedor];
        $observacion = $rst_reg[ped_observacion];

        if ($emisor >= 10) {
            $ems = '0' . $emisor;
        } else {
            $ems = '00' . $emisor;
        }

        $rst_sec = pg_fetch_array($Reg->lista_secuencial_documento($ems));
        $sec = ($rst_sec[secuencial] + 1);


        if ($sec >= 0 && $sec < 10) {
            $tx = '00000000';
        } else if ($sec >= 10 && $sec < 100) {
            $tx = '0000000';
        } else if ($sec >= 100 && $sec < 1000) {
            $tx = '000000';
        } else if ($sec >= 1000 && $sec < 10000) {
            $tx = '00000';
        } else if ($sec >= 10000 && $sec < 100000) {
            $tx = '0000';
        } else if ($sec >= 100000 && $sec < 1000000) {
            $tx = '000';
        } else if ($sec >= 1000000 && $sec < 10000000) {
            $tx = '00';
        } else if ($sec >= 10000000 && $sec < 100000000) {
            $tx = '0';
        } else if ($sec >= 100000000 && $sec < 1000000000) {
            $tx = '';
        }
        $secuencial = $ems . '-001-' . $tx . $sec;

        $dat = array(
            $sec,
            $nombre,
            $identificacion,
            $femision,
            '0',
            '0',
            '01',
            $subtotal12,
            $subtotal0,
            $subtotal_exento_iva,
            $subtotal_no_objeto_iva,
            $total_descuento,
            '0',
            $total_iva,
            '0',
            '0',
            $total_valor,
            $direccion_cliete,
            $email_cliente,
            $telefono_cliente,
            $cli_ciudad,
            $cli_pais,
            '1',
            $cod_punto_emision,
            $cli_parroquia,
            $secuencial,
            $vendedor,
            $observacion
        );
        if ($sts == 1) {
            if ($Reg->insert_factura_pedido($dat) == true) {
                $cns_det = $Reg->lista_un_detalle_pedido_venta($id);
                while ($rst_det = pg_fetch_array($cns_det)) {
                    $datadet = array(str_replace('-', '', $secuencial) . '&' .
                        $rst_det[det_cod_producto] . '&' .
                        $rst_det[det_cod_auxiliar] . '&' .
                        $rst_det[det_cantidad] . '&' .
                        $rst_det[det_descripcion] . '&' .
                        '' . '&' .
                        '' . '&' .
                        $rst_det[det_vunit] . '&' .
                        $rst_det[det_descuento_porcentaje] . '&' .
                        $rst_det[det_total] . '&' .
                        $rst_det[det_impuesto] . '& NO &' .
                        $rst_det[det_descuento_moneda] . '&' .
                        $rst_det[det_lote]);


                    $n = 0;
                    $i = count($datadet);
                    while ($n < $i) {
                        $dt = explode('&', $datadet[$n]);
                        $data = array(
                            $dt[0],
                            $dt[1],
                            $dt[2],
                            $dt[3],
                            $dt[4],
                            $dt[5],
                            $dt[6],
                            $dt[7],
                            $dt[8],
                            $dt[9],
                            $dt[10],
                            $dt[11],
                            $dt[12],
                            $dt[13]
                        );
                        if ($Reg->insert_detalle_facturaped($data) == true) {
                            $rst_pro = pg_fetch_array($Reg->lista_un_producto_industrial($dt[1]));
                            if ($rst_pro[pro_id] != '') {
                                $rst_pro[pro_id] = $rst_pro[pro_id];
                                $tab = 0;
                                if (($rst_pro[emp_id] == 3 || $rst_pro[emp_id] == 4) && $dat[23] == 1) {
                                    $bod = '10';
                                } else {
                                    $bod = $dat[23];
                                }
                            }

                            $rst_procomer = pg_fetch_array($Reg->lista_un_producto_noperti_lote($dt[1], $dt[13]));
                            if ($rst_procomer[id] == '') {
                                $rst_procomer = pg_fetch_array($Reg->lista_un_producto_noperti($dt[1]));
                                $mesaje = $rst_procomer[pro_b];
                            }
                            if ($rst_procomer[id] != '') {
                                $rst_pro[pro_id] = $rst_procomer[id];
                                $tab = 1;
                                $bod = $dat[23];
                            }
                            $rst_cli = pg_fetch_array($Reg->lista_un_cliente_cedula($dat[2]));
                            $data1 = array(
                                $rst_pro[pro_id],
                                25,
                                $rst_cli[cli_id],
                                $bod, ///BODEGA
                                $dt[0],
                                '0',
                                '0',
                                date('Y-m-d'),
                                date('Y-m-d'),
                                date('H:i:s'),
                                $dt[3],
                                '',
                                date('Y-m-d'),
                                $dt[0],
                                '',
                                '',
                                0,
                                0,
                                0,
                                0,
                                $tab
                            );
                            if ($Reg->insert_movimiento_pt_ped($data1) == false) {
                                $sms = 'Insert_mov' . pg_last_error();
                            }
                        }
                        $n++;
                    }
                }
                $cns_pag = $Reg->lista_un_pago_pedido_venta($id);
                while ($rst_pag = pg_fetch_array($cns_pag)) {
                    $datapag = array($secuencial . '&' .
                        $rst_pag[pag_porcentage] . '&' .
                        $rst_pag[pag_dias] . '&' .
                        $rst_pag[pag_valor] . '&' .
                        $rst_pag[pag_fecha_v]);

                    $m = 0;
                    $i = count($datapag);
                    while ($m < $i) {
                        $dat = explode('&', $datapag[$m]);
                        $data4 = array(
                            $dat[0],
                            $dat[1],
                            $dat[2],
                            $dat[3],
                            $dat[4]);
                        if ($Reg->insert_pagos_pedventa($data4) == false) {
                            $sms = 'Insert_pg' . pg_last_error();
                        }
                        $m++;
                    }
                }
            }
        }
        echo $sms . '& ' . $secuencial;
//        if ($Reg->lista_cambia_status($id, $sts) == false) {
//            $sms = pg_last_error();
//        }
        break;
    case 4;
        $sms = 0;
        if ($sts == 'habilitar') {
            $rst = pg_fetch_array($Reg->lista_facturas($id));
            if (empty($rst)) {
                $sts = 1;
            } else {
                $sts = 3;
            }
        } else {
            $sts = $sts;
        }
        if ($Reg->lista_cambia_status($id, $sts) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 5:
        $cns = $Reg->lista_facturas($id);
        $aud = "";
        $n = 0;
        while ($rst = pg_fetch_array($cns)) {
            $n++;
            $aud .= "<tr ><td>$n</td><td>$rst[num_documento]</td></tr>";
        }
        echo $aud;
        break;
    case 6;
        $sms = 0;
        $ped1 = $_REQUEST[ped1];
        $ped2 = $_REQUEST[ped2];
        $rst1 = pg_fetch_array($Reg->lista_un_pedido_codigo($ped1));
        $rst2 = pg_fetch_array($Reg->lista_un_pedido_codigo($ped2));
        $rst1[ped_nom_cliente];
        $rst2[ped_nom_cliente];
        if ($rst1[ped_nom_cliente] != $rst2[ped_nom_cliente]) {
            $sms = 1;
        }
        echo $sms;
        break;
    case 7:
        $sms = 0;
        if ($Reg->lista_cambia_status($id, $sts) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 8:
        $sms = '';
        if ($s == 0) {
            $cns = $Reg->lista_clientes_search(strtoupper($id));
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[cli_raz_social];
                $sms .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_id]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
        } else {
            $rst = pg_fetch_array($Reg->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . ' ' . $rst[cli_numeracion] . ' ' . $rst[cli_calle_sec] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_parroquia] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst[cli_id] . '&' . $rst[tipo_cliente] . '&' . $rst[cli_estado];
            }
        }
        echo $sms;
        break;
    case 9:
        $rst = pg_fetch_array($Reg->lista_ultimo_registro_ordped_venta());
        $sec = (substr($rst[sec_ord_ped_venta], -5) + 1);
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

    case 10:
        $sms = 0;
        $sec = $_REQUEST[sec];
        if ($Reg->insert_sec_ord_ped_venta($sec) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 11:
        $sms = 0;
        $sec = $_REQUEST[sec];
        $rst = pg_fetch_array($Reg->lista_una_ordped_id($sec));
        $estd = 0;
        $id = $rst[sec_id];
        if ($Reg->upd_sec_ord_ped_venta($id, $estd) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
}
?>
