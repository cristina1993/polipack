<?php

include_once '../Clases/clsClase_registro_facturas.php';
include_once("../Clases/clsAuditoria.php");
$Reg = new Clase_registro_facturas();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$fields = $_REQUEST[fields];
$detalle = $_REQUEST[detalle];
$pagos = $_REQUEST[pagos];
$id = $_REQUEST[id];
$nom = $_REQUEST[nom];
$doc = $_REQUEST[doc];
$ruc = $_REQUEST[ruc];
$Adt = new Auditoria();
switch ($op) {
    case 100:
        if ($Reg->insert_asiento_mp($_REQUEST[sbt_mp]) == false) {
            $sms = 'Asiento ' . pg_last_error();
        }
        //echo $sms;       
        break;
    case 0:
        $sms = 0;
        $num = $data[4];
        if ($id == 0) { //Insertar
            $rst_sec = pg_fetch_array($Reg->lista_secuencial($data[22]));
            if (!empty($rst_sec)) {
                $sms = 3;
            } else {
                $iv = pg_fetch_array($Reg->lista_asientos_ctas('265'));
                $ctas = pg_fetch_array($Reg->lista_asientos_ctas('264'));
                $ic = pg_fetch_array($Reg->lista_asientos_ctas('266'));
                $irb = pg_fetch_array($Reg->lista_asientos_ctas('267'));
                $cdesc = pg_fetch_array($Reg->lista_asientos_ctas('268'));
                $prop = pg_fetch_array($Reg->lista_asientos_ctas('269'));
                if ($iv[pln_id] == '' || $ctas[pln_id] == '' || $ic[pln_id] == '' || $irb[pln_id] == '' || $cdesc[pln_id] == '' || $prop[pln_id] == '') {
                    $sms = 1;
                } else {
                    $rst_cli = pg_fetch_array($Reg->lista_cliente_ruc($data[21]));
                    if (empty($rst_cli)) {
                        if (strlen($data[21]) < 11) {
                            $tipo = 'PN';
                        } else {
                            $tipo = 'PJ';
                        }
                        $rst_cod = pg_fetch_array($Reg->lista_secuencial_cliente($tipo));
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
                            strtoupper($data[23]),
                            strtoupper($data[21]),
                            strtoupper($data[24]),
                            strtoupper($data[25]),
                            strtolower($data[26]),
                            strtoupper($retorno)
                        );
                        if ($Reg->insert_cliente($da) == false) {
                            $sms = 'Insert_cli' . pg_last_error();
                        }
                    } else {
                        $da3 = array(
                            strtoupper($data[24]),
                            strtolower($data[26]),
                            strtoupper($data[25])
                        );
                        if ($Reg->upd_email_cliente($da3, $data[21]) == false) {
                            $sms = 'Update_email' . pg_last_error();
                        }
                    }
                    if ($data[28] != 0) {
                        $cli = $data[28];
                    } else {
                        $rst_cli = pg_fetch_array($Reg->lista_cliente_ruc($data[21]));
                        $cli = $rst_cli[cli_id];
                    }
                    if ($Reg->insert_registro($data, $cli) == true) {
                        $rst_reg = pg_fetch_array($Reg->lista_registro_numero($data[22]));
                        $id = $rst_reg[reg_id];
                        $concepto_reg = $data[9];
                        foreach ($detalle as $row => $data) {
                            $det = explode('&', $data);

                            if ($det[10] == 1) {
                                $tp = $det[8];
                                $cod = $det[0];
                                $desc = $det[1];
                                if ($Reg->insert_producto_insumo_mp($cod, $desc, $tp) == false) {
                                    $sms = pg_last_error();
                                }
                                $rst_pro = pg_fetch_array($Reg->lista_producto_insumos_otros_cod($det[12], $cod));
                                $pro_id = $rst_pro[id];
                            } else {
                                $pro_id = $det[11];
                            }
                            array_push($det, $pro_id);
                            array_push($det, $rst_reg[reg_id]);

                            if ($Reg->insert_detalle_registro($det) == false) {
                                $sms = 'Detalle ' . pg_last_error();
                            }
                        }
                        foreach ($pagos as $row => $data) {
                            $pag = explode('&', $data);
                            array_push($pag, $rst_reg[reg_id]);
                            if ($Reg->insert_pagos_registro($pag) == false) {
                                $sms = 'Pagos ' . pg_last_error();
                            }
                        }
                        $asiento = $Reg->siguiente_asiento();
                        $cns_sum = $Reg->lista_sum_cuentas($rst_reg[reg_id]);
                        while ($rst1 = pg_fetch_array($cns_sum)) {
                            $dat_asi_det = array(
                                $asiento,
                                $concepto_reg,
                                $rst_reg[reg_num_documento],
                                $rst_reg[reg_fregistro],
                                $rst1[reg_codigo_cta],
                                $rst1[dtot] + $rst1[ddesc],
                            );
                            if ($Reg->insert_asiento_det($dat_asi_det) == false) {
                                $sms = 'asi_det' . pg_last_error();
                                $aud = 1;
                            }
                        }

                        $dat_asi = array(
                            $rst_reg[reg_sbt],
                            $rst_reg[reg_num_documento],
                            $rst_reg[reg_fregistro],
                            '',
                            $rst_reg[reg_iva12],
                            $rst_reg[reg_total],
                            $rst_reg[reg_ice],
                            $rst_reg[reg_irbpnr],
                            $rst_reg[reg_propina],
                            $rst_reg[reg_tdescuento],
                            '0',
                            $iv[pln_codigo],
                            $ctas[pln_codigo],
                            $ic[pln_codigo],
                            $irb[pln_codigo],
                            $cdesc[pln_codigo],
                            $prop[pln_codigo],
                            $concepto_reg,
                        );
                        $result = $Reg->insert_asiento_mp($dat_asi, $asiento);
                        $rst_asien = explode('&', $result);

                        if ($rst_asien[0] == false) {
                            $sms = 'Asiento ' . pg_last_error();
                        } else {
                            if ($Reg->upd_num_asiento($rst_asien[1], $rst_reg[reg_id]) == false) {
                                $sms = pg_last_error();
                            }
                        }

                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = 'REGISTRO DOCUMENTOS';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $num) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    } else {
                        $sms = "Reg " . pg_last_error();
                    }

                    $brt = pg_fetch_array($Reg->buscar_retencion($rst_reg[reg_num_documento], $rst_reg[reg_ruc_cliente]));
                    if (!empty($brt)) {
                        $sms = '2';
                    }
                }
            }
        } else { //Modificar
            $concepto_reg = $data[9];
            $iv = pg_fetch_array($Reg->lista_asientos_ctas('265'));
            $ctas = pg_fetch_array($Reg->lista_asientos_ctas('264'));
            $ic = pg_fetch_array($Reg->lista_asientos_ctas('266'));
            $irb = pg_fetch_array($Reg->lista_asientos_ctas('267'));
            $cdesc = pg_fetch_array($Reg->lista_asientos_ctas('268'));
            $prop = pg_fetch_array($Reg->lista_asientos_ctas('269'));
            if ($iv[pln_id] == '' || $ctas[pln_id] == '' || $ic[pln_id] == '' || $irb[pln_id] == '' || $cdesc[pln_id] == '' || $prop[pln_id] == '') {
                $sms = 1;
            } else {
                $rst_cli = pg_fetch_array($Reg->lista_cliente_ruc($data[21]));
                if (empty($rst_cli)) {
                    if (strlen($data[21]) < 11) {
                        $tipo = 'PN';
                    } else {
                        $tipo = 'PJ';
                    }
                    $rst_cod = pg_fetch_array($Reg->lista_secuencial_cliente($tipo));
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
                        strtoupper($data[23]),
                        strtoupper($data[21]),
                        strtoupper($data[24]),
                        strtoupper($data[25]),
                        strtolower($data[26]),
                        strtoupper($retorno)
                    );
                    if ($Reg->insert_cliente($da) == false) {
                        $sms = 'Insert_cli' . pg_last_error();
                    }
                } else {
                    $da3 = array(
                        strtoupper($data[24]),
                        strtolower($data[26]),
                        strtoupper($data[25])
                    );
                    if ($Reg->upd_email_cliente($da3, $data[21]) == false) {
                        $sms = 'Update_email' . pg_last_error();
                    }
                }
                if ($Reg->upd_registro($data, $id) == true) {


                    if ($Reg->elimina_detalle_pagos($id) == true) {
                        foreach ($detalle as $row => $data) {
                            $det = explode('&', $data);
//                        array_push($det, $id);
                            if ($det[10] == 1) {
                                $tp = $det[8];
                                $cod = $det[0];
                                $desc = $det[1];
                                if ($Reg->insert_producto_insumo_mp($cod, $desc, $tp) == false) {
                                    $sms = pg_last_error();
                                }
                                $rst_pro = pg_fetch_array($Reg->lista_producto_insumos_otros_cod($det[12], $cod));
                                $pro_id = $rst_pro[id];
                            } else {
                                $pro_id = $det[11];
                            }
                            array_push($det, $pro_id);
                            array_push($det, $id);
                            if ($Reg->insert_detalle_registro($det) == false) {
                                $sms = 'Detalle ' . pg_last_error();
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
                    $rst_as = pg_fetch_array($Reg->lista_un_registro($id));
                    if ($Reg->elimina_asientos_asiento($rst_as[con_asiento]) == true) {
                        $asiento = $Reg->siguiente_asiento();
                        $cns_sum = $Reg->lista_sum_cuentas($rst_as[reg_id]);
                        while ($rst1 = pg_fetch_array($cns_sum)) {
                            $dat_asi_det = array(
                                $asiento,
                                $concepto_reg,
                                $rst_as[reg_num_documento],
                                $rst_as[reg_fregistro],
                                $rst1[reg_codigo_cta],
                                $rst1[dtot] + $rst1[ddesc],
                            );
                            if ($Reg->insert_asiento_det($dat_asi_det) == false) {
                                $sms = 'asi_det_mod' . pg_last_error();
                            }
                        }
                        $dat_asi = array(
                            $rst_as[reg_sbt],
                            $rst_as[reg_num_documento],
                            $rst_as[reg_fregistro],
                            '',
                            $rst_as[reg_iva12],
                            $rst_as[reg_total],
                            $rst_as[reg_ice],
                            $rst_as[reg_irbpnr],
                            $rst_as[reg_propina],
                            $rst_as[reg_tdescuento],
                            '0',
                            $iv[pln_codigo],
                            $ctas[pln_codigo],
                            $ic[pln_codigo],
                            $irb[pln_codigo],
                            $cdesc[pln_codigo],
                            $prop[pln_codigo],
                            $concepto_reg
                        );
                        $result = $Reg->insert_asiento_mp($dat_asi, $asiento);
                        $rst_asien = explode('&', $result);
                        if ($rst_asien[0] == false) {
                            $sms = 'Asiento ' . pg_last_error();
                        } else {
                            if ($Reg->upd_num_asiento($rst_asien[1], $rst_as[reg_id]) == false) {
                                $sms = pg_last_error();
                            }
                        }
                    }


                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'REGISTRO DOCUMENTOS';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $num) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                } else {
                    $sms = pg_last_error();
                }
            }
        }
        echo $sms . '&' . $id . '&' . $rst_asien[1];
        break;
    case 1:
        $sms = 0;
        if ($Reg->elimina_registro_detalle_pagos_id($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $nom;
            $modulo = 'REGISTRO DOCUMENTOS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 2:
        $rst = pg_fetch_array($Reg->lista_producto_insumos_otros_id($_REQUEST[tbl], $id));
        $rst_ant = pg_fetch_array($Reg->lista_producto_ant($id,$_REQUEST[tbl]));
        if (empty($rst_ant)) {
            $rst_ant[pln_id] = '0';
        }
        echo $rst[tbl] . '&' . $rst[cod] . '&' . $rst[dsc] . '&' . $rst[lote] . '&' . $id. '&' . $rst_ant[pln_id] . '&' . $rst_ant[reg_codigo_cta];
        break;
    case 3:
        $rst = pg_fetch_array($Reg->lista_ultimo_codigo($id));

        if (empty($rst)) {
            switch ($id) {
                case '0':
                    $prf = 'IO';
                    break;
                case '1':
                    $prf = 'V';
                    break;
                case '2':
                    $prf = 'MP';
                    break;
                default :
                    $prf = '';
                    break;
            }
            $prf = $prf;
            $sec = '0001';
        } else {

            if ($id == 1) {
                $prf = substr($rst[cod], 0, 1);
                $sec = substr($rst[cod], -4);
            } else {
                $prf = substr($rst[cod], 0, 2);
                $sec = substr($rst[cod], -4);
            }

            if ($sec < 10) {
                $txt = '000';
            } else if ($sec >= 10 & $sec < 100) {
                $txt = '00';
            } else if ($sec >= 100 & $sec < 1000) {
                $txt = '0';
            } else {
                $txt = '';
            }
            $sec = $txt . ($sec + 1);
        }
        echo $prf . '&' . $sec;
        break;

    case 4:
        $rst = pg_fetch_array($Reg->lista_un_registro_factura($doc, $ruc, $data));
        echo $rst[reg_num_documento] . '&' . $rst[reg_ruc_cliente] . '&' . $rst[reg_estado];
        break;
    case 5:
        $sms = 0;
        $iv = pg_fetch_array($Reg->lista_asientos_ctas('271'));
        $ctas = pg_fetch_array($Reg->lista_asientos_ctas('270'));
        $ic = pg_fetch_array($Reg->lista_asientos_ctas('272'));
        $irb = pg_fetch_array($Reg->lista_asientos_ctas('273'));
        $desc = pg_fetch_array($Reg->lista_asientos_ctas('274'));
        $prop = pg_fetch_array($Reg->lista_asientos_ctas('275'));
        if ($iv[pln_id] == '' || $ctas[pln_id] == '' || $ic[pln_id] == '' || $irb[pln_id] == '' || $desc[pln_id] == '' || $prop[pln_id] == '') {
            $sms = 2;
        } else {
            $rst_fc = pg_fetch_array($Reg->lista_un_registro($_REQUEST[md_id]));
            $rst_nc = pg_fetch_array($Reg->lista_una_nota_cred($_REQUEST[md_id]));
            $rst_nd = pg_fetch_array($Reg->lista_una_nota_deb($_REQUEST[md_id]));
            $rst_rt = pg_fetch_array($Reg->lista_retencion($_REQUEST[md_id]));
            if (empty($rst_nc) && empty($rst_nd) && empty($rst_rt)) {
                if (empty($rst_fc[reg_femision])) {
                    $rst_fc[reg_femision] = date('Y-m-d');
                }
                if ($Reg->update_estado_reg_factura($_REQUEST[md_id], $_REQUEST[estado], $rst_fc[reg_femision]) == true) {
                    if ($Reg->update_estado_det_factura($_REQUEST[md_id], $_REQUEST[estado]) == false) {
                        $sms = 'Update_reg_encab_regfac' . pg_last_error();
                    } else {
                        $rst_retencion = pg_fetch_array($Reg->lista_ultima_retencion($rst_fc[reg_id]));
                        if (!empty($rst_retencion)) {
                            $rst_asiento = pg_fetch_array($Reg->lista_asiento_retencion($rst_retencion[ret_numero]));
                            $asiento = $rst_asiento[con_asiento];
                        } else {
                            $asiento = $Reg->siguiente_asiento();
                        }
                        $cns_sum = $Reg->lista_sum_cuentas($rst_fc[reg_id]);
                        while ($rst1 = pg_fetch_array($cns_sum)) {
                            $dat_asi_det = array(
                                $asiento,
                                $rst_fc[reg_num_documento],
                                $rst_fc[reg_fregistro],
                                $rst1[reg_codigo_cta],
                                $rst1[dtot] + $rst1[ddesc],
                                 $rst_fc[reg_id]
                            );
                            if ($Reg->insert_asiento_anulacion_det($dat_asi_det) == false) {
                                $sms = 'asi_det_aunlacion' . pg_last_error();
                                $aud = 1;
                            }
                        }

                        $dat_asi = array(
                            $rst_fc[reg_sbt],
                            $rst_fc[reg_num_documento],
                            $rst_fc[reg_fregistro],
                            '',
                            $rst_fc[reg_iva12],
                            $rst_fc[reg_total],
                            $rst_fc[reg_ice],
                            $rst_fc[reg_irbpnr],
                            $rst_fc[reg_propina],
                            $rst_fc[reg_tdescuento],
                            '0',
                            $iv[pln_codigo],
                            $ctas[pln_codigo],
                            $ic[pln_codigo],
                            $irb[pln_codigo],
                            $desc[pln_codigo],
                            $prop[pln_codigo],
                            $rst_fc[reg_id]
                        );
                        $result = $Reg->insert_asiento_anulacion($dat_asi, $asiento);
                        $rst_asien = explode('&', $result);

                        if ($rst_asien[0] == false) {
                            $sms = 'Asiento ' . pg_last_error();
                        } else {
                            if ($Reg->update_asiento_anulacion($asiento,$rst_fc[reg_id]) == false) {
                                $sms = 'Update_asi_anulacion' . pg_last_error();
                            }
                        }
                    }
                } else {
                    $sms = 'Update_reg_encab_regfac' . pg_last_error();
                }
            } else {
                $sms = '1';
            }
        }
        echo $sms;
        break;

    case 6:
        $cta = pg_fetch_array($Reg->lista_plan_cuentas_id($id));
        echo $cta[pln_id] . '&' . $cta[pln_codigo] . '&' . $cta[pln_descripcion];
        break;
    case 7:
        $rst = pg_fetch_array($Reg->lista_encabezdo_ant($id));
        if ($rst[reg_fcaducidad] == '1900-01-01') {
            $rst[reg_fcaducidad] = '';
        }
        if ($rst[reg_fautorizacion] == '1900-01-01') {
            $rst[reg_fautorizacion] = '';
        }

        echo $rst[reg_sustento] . '&' . $rst[reg_num_autorizacion] . '&' . $rst[reg_fautorizacion] . '&' . $rst[reg_fcaducidad]. '&' . $rst[reg_tpcliente]. '&' . $rst[reg_concepto];
        break;
}
?>
