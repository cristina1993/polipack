<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_retencion.php';
$Clase_retencion = new Clase_retencion();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$nom = $_REQUEST[nom];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$id = $_REQUEST[id];
$x = $_REQUEST[x];
$fields = $_REQUEST[fields];
$s = $_REQUEST[s];
$doc = $_REQUEST[doc];
$tp_doc = $_REQUEST[tdoc];
switch ($op) {
    case 0:
        $aud = 0;
        $sms = 0;
        if (empty($id)) {
            $ctaxpag = pg_fetch_array($Clase_retencion->lista_asientos_ctas('332')); ///falta ctas x pagar
//            $iv = pg_fetch_array($Clase_retencion->lista_asientos_ctas('263'));
//            $rta = pg_fetch_array($Clase_retencion->lista_asientos_ctas('262'));
            $prov = pg_fetch_array($Clase_retencion->lista_asientos_ctas('261')); //// proveedores
            if ($ctaxpag[pln_id] == '' || $prov[pln_id] == '') {
                $sms = 2;
            } else {
                $n = 0;
                $rst = pg_fetch_array($Clase_retencion->lista_secuencial_retencion($data[1]));
                if ($rst[ret_numero] != '') {
                    $sec = (substr($rst[ret_numero], -9) + 1);
                    if ($sec >= 0 && $sec < 10) {
                        $txt = '00000000';
                    } else if ($sec >= 10 && $sec < 100) {
                        $txt = '0000000';
                    } else if ($sec >= 100 && $sec < 1000) {
                        $txt = '000000';
                    } else if ($sec >= 1000 && $sec < 10000) {
                        $txt = '00000';
                    } else if ($sec >= 10000 && $sec < 100000) {
                        $txt = '0000';
                    } else if ($sec >= 100000 && $sec < 1000000) {
                        $txt = '000';
                    } else if ($sec >= 1000000 && $sec < 10000000) {
                        $txt = '00';
                    } else if ($sec >= 10000000 && $sec < 100000000) {
                        $txt = '0';
                    } else if ($sec >= 100000000 && $sec < 1000000000) {
                        $txt = '';
                    }
                } else {
                    $txt = '000000001';
                }

                if ($data[1] >= 10) {
                    $ems = '0' . $data[1];
                } else {
                    $ems = '00' . $data[1];
                }
                $comprobante = $ems . '-001-' . $txt . $sec;

                $n = 0;
                if (empty($data[0])) {
                    if (strlen($data[5]) < 11) {
                        $tipo = 'PN';
                        $categoria = 1;
                    } else {
                        $tipo = 'PJ';
                        $categoria = 2;
                    }

                    $rst_cod = pg_fetch_array($Clase_retencion->lista_secuencial_cliente($tipo));
                    $sec = (substr($rst_cod[cli_codigo], 2, 5) + 1);

                    if ($sec >= 0 && $sec < 10) {
                        $txt = '0000';
                    } else if ($sec >= 10 && $sec < 100) {
                        $txt = '000';
                    } else if ($sec >= 100 && $sec < 1000) {
                        $txt = '00';
                    } else if ($sec >= 1000 && $sec < 10000) {
                        $txt = '0';
                    } else {
                        $txt = '';
                    }

                    $retorno = $tipo . $txt . $sec;
                    $da = array(
                        strtoupper($data[4]),
                        strtoupper($data[5]),
                        strtoupper($data[6]),
                        strtoupper($data[10]),
                        strtoupper($data[7]),
                        $retorno,
                        $categoria
                    );
                    if ($Clase_retencion->insert_cliente($da) == false) {
                        $sms = 'Insert_cli' . pg_last_error();
                        $aud = 1;
                    }
                    $rst_cl = pg_fetch_array($Clase_retencion->lista_clientes_cedula($data[5]));
                    $cli_id = $rst_cl[cli_id];
                } else {
                    $cli_id = $data[0];
                }

                $rst_ulti_sec = pg_fetch_array($Clase_retencion->lista_secuencial_locales($data[1]));
                if ($data[3] == $rst_ulti_sec[secuencial]) {
                    $sms = 1;
                } else {
                    if ($Clase_retencion->insert_retencion($data, $cli_id, $comprobante) == false) {
                        $sms = 'Insert_retencion' . pg_last_error();
                        $aud = 1;
                    } else {
                        /////////retencion anterior////
                        $rst_rete = pg_fetch_array($Clase_retencion->lista_retencion_numero($comprobante));
                        $rst_reg = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_rete[reg_id]));
                        $rst_rant = pg_fetch_array($Clase_retencion->lista_ultima_retencion_anulada($rst_rete[reg_id]));
                        $cns_rant = $Clase_retencion->lista_detalle_retencion($rst_rant[ret_id]);
                        $asiento_ant=$Clase_retencion->siguiente_asiento();
                        if (!empty($rst_rant)) {
                            $dat_asi = array(
                                $rst_rant[ret_total_valor],
                                $rst_rant[ret_numero],
                                $rst_rant[ret_fecha_emision],
                                $prov[pln_codigo]
                            );
//                      
                            if ($Clase_retencion->insert_asiento($dat_asi, $asiento_ant) == false) {
                                $sms = 'insert_asiento' . pg_last_error();
                                $aud = 1;
                            }
                            $a=0;
                            while ($rst_dan=  pg_fetch_array($cns_rant)) {
                                $rst_idcta = pg_fetch_array($Clase_retencion->lista_id_cuenta($rst_dan[por_id]));
                                $rst_cta = pg_fetch_array($Clase_retencion->lista_cuenta_contable($rst_idcta[cta_id]));
                                $concepto = $rst_idcta[por_descripcion] . ' ' . $rst_idcta[por_codigo];
                                $dt_asi = array(
                                    $rst_dan[dtr_valor],
                                    $rst_rant[ret_numero],
                                    $rst_rant[ret_fecha_emision],
                                    $rst_cta[pln_codigo],
                                    $concepto
                                );
                                if ($Clase_retencion->insert_asientos_ret($dt_asi, $asiento_ant) == false) {
                                    $sms = 'insert_asiento_ret' . pg_last_error();
                                    $aud = 1;
                                }
                                $a++;
                            }
                        }

                        //////// retencion actual///////
                        $rst_rete = pg_fetch_array($Clase_retencion->lista_retencion_numero($comprobante));
                        $rst_reg = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_rete[reg_id]));
                        $dat_asi = array(
                            $rst_rete[ret_total_valor],
                            $rst_rete[ret_numero],
                            $rst_rete[ret_fecha_emision],
                            $prov[pln_codigo]
                        );
//                      
                        if ($Clase_retencion->insert_asiento($dat_asi, $rst_reg[con_asiento]) == false) {
                            $sms = 'insert_asiento' . pg_last_error();
                            $aud = 1;
                        }
                        while ($n < count($data2)) {
                            $dt = explode('&', $data2[$n]);
                            $rst_ret = pg_fetch_array($Clase_retencion->lista_retencion_numero($comprobante));
                            $ret_id = $rst_ret[ret_id];
                            if ($Clase_retencion->insert_det_retencion($dt, $ret_id) == false) {
                                $sms = pg_last_error() . 'insert_det_retencion';
                                $aud = 1;
                            }
                            $rst_reg = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_ret[reg_id]));
                            $rst_idcta = pg_fetch_array($Clase_retencion->lista_id_cuenta($dt[0]));
                            $rst_cta = pg_fetch_array($Clase_retencion->lista_cuenta_contable($rst_idcta[cta_id]));
                            $rst_regi = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_ret[reg_id]));
                            $concepto = $rst_idcta[por_descripcion] . ' ' . $rst_idcta[por_codigo];
                            $dt_asi = array(
                                $dt[6],
                                $rst_ret[ret_numero],
                                $rst_ret[ret_fecha_emision],
                                $rst_cta[pln_codigo],
                                $concepto
                            );
                            if ($Clase_retencion->insert_asientos_ret($dt_asi, $rst_regi[con_asiento]) == false) {
                                $sms = 'insert_asiento_ret' . pg_last_error();
                                $aud = 1;
                            }
                            $n++;
                        }

                        $rp = pg_fetch_array($Clase_retencion->buscar_un_pago_doc($rst_ret[reg_id]));
                        if (empty($rp)) {
                            $rp2 = pg_fetch_array($Clase_retencion->buscar_un_pago_doc1($rst_ret[reg_id]));
                            $pag_id = $rp2[pag_id];
                        } else {
                            $pag_id = $rp[pag_id];
                        }
                        $cta = array(
                            $rst_ret[reg_id], //com_id
                            $rst_ret[ret_fecha_emision], //cta_fec
                            $rst_ret[ret_total_valor], //cta_monto
                            'RETENCION', //forma de pago
                            $ctaxpag[pln_codigo], //cta_banco
                            $prov[pln_id], /// pln_id
                            $rst_ret[ret_fecha_emision], //fec_pag
                            $pag_id, //pag_id
                            $rst_ret[ret_num_comp_retiene], //num_doc
                            'PAGO FACTURACION', //cta_concepto
                            '2', //asiento
                            '0', //chq_id
                            $rst_ret[ret_id] //doc_id
                        );
                        if ($Clase_retencion->insert_ctasxpagar($cta) == false) {
                            $sms = 'ctasxpagar ' . pg_last_error();
                        } else {
                            $asi = $Clase_retencion->siguiente_asiento();
                            $asiento = array(
                                $asi,
                                'CUENTAS X PAGAR',
                                $rst_ret[ret_numero], //numero de documento retencion
                                $rst_ret[ret_fecha_emision], //fec
                                $prov[pln_codigo], //con_debe
                                $ctaxpag[pln_codigo], //con_haber
                                $rst_ret[ret_total_valor], //val_debe
                                $rst_ret[ret_total_valor], // val_haber
                                '0', //estado
                                $rst_ret[reg_id] // id documento registro factura
                            );
                            if ($Clase_retencion->insert_asientos($asiento) == false) {
                                $sms = 'asientos ' . pg_last_error();
                            }
                        }
                    }

                    if ($aud == 0) {
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = 'RETENCION';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[3]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            }
        } else {
            if (empty($data[0])) {
                if (strlen($data[0]) < 11) {
                    $tipo = 'PN';
                    $categoria = 1;
                } else {
                    $tipo = 'PJ';
                    $categoria = 2;
                }

                $rst_cod = pg_fetch_array($Clase_retencion->lista_secuencial_cliente($tipo));
                $sec = (substr($rst_cod[cli_codigo], 2, 5) + 1);

                if ($sec >= 0 && $sec < 10) {
                    $txt = '0000';
                } else if ($sec >= 10 && $sec < 100) {
                    $txt = '000';
                } else if ($sec >= 100 && $sec < 1000) {
                    $txt = '00';
                } else if ($sec >= 1000 && $sec < 10000) {
                    $txt = '0';
                } else {
                    $txt = '';
                }

                $retorno = $tipo . $txt . $sec;
                $da = array(
                    strtoupper($data[4]),
                    strtoupper($data[5]),
                    strtoupper($data[6]),
                    strtoupper($data[10]),
                    strtoupper($data[7]),
                    $retorno,
                    $categoria
                );
                if ($Clase_retencion->insert_cliente($da) == false) {
                    $sms = 'Insert_cli' . pg_last_error();
                    $aud = 1;
                }
                $rst_cl = pg_fetch_array($Clase_retencion->lista_clientes_cedula($data[5]));
                $cli_id = $$rst_cl[cli_id];
            } else {
                $cli_id = $data[0];
            }

            if ($Clase_retencion->update_retencion($data, $id, $cli_id) == false) {
                $sms = 'Update_retencion' . pg_last_error();
                $aud = 1;
            } else {
                if ($Clase_retencion->delete_det_retencion($id) == false) {
                    $sms = 'Delete_retencion' . pg_last_error();
                    $aud = 1;
                } else {
                    $n = 0;
                    while ($n < count($data2)) {
                        $dt = explode('&', $data2[$n]);
                        if ($Clase_retencion->insert_det_retencion($dt, $id) == false) {
                            $sms = pg_last_error() . 'insert_det_retencion';
                            $aud = 1;
                        }
                        $n++;
                    }
                }
            }

            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'RETENCION';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[3]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }

        echo $sms . '&' . $rst_reg[con_asiento];
        break;
    case 1:
        $sec = str_replace('-', '', trim($id));
        if ($Clase_retencion->delete_retencion($sec) == true) {
            $sms = 0;
            $n = 0;
            $f = $nom;
            $modulo = 'RETENCION';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 2:
        $rst = pg_fetch_array($Clase_retencion->lista_datos_porcentaje($id));
        $rst_cuenta = pg_fetch_array($Clase_retencion->lista_cuentas_act_inac($rst[cta_id]));
        $descripcion = $rst[por_descripcion];
        $porcentaje = $rst[por_porcentage];
        $cod = $rst[por_codigo];
        $por_id = $rst[por_id] . '_' . $rst[por_siglas];
        echo $descripcion . '&' . $porcentaje . '&' . $cod . '&' . $por_id . '&' . $rst[por_siglas] . '&' . $rst[cta_id] . '&' . $rst_cuenta[pln_estado];
        break;

    case 3:
        $rst = pg_fetch_array($Clase_retencion->lista_secuencial_retencion($x));
        $rst1 = pg_num_rows($Clase_retencion->lista_secuencial_retencion($x));
        if ($rst1 != 0) {
            $sec = (substr($rst[sec], -5) + 1);
            if ($sec >= 0 && $sec < 10) {
                $txt = '00000000';
            } else if ($sec >= 10 && $sec < 100) {
                $txt = '0000000';
            } else if ($sec >= 100 && $sec < 1000) {
                $txt = '000000';
            } else if ($sec >= 1000 && $sec < 10000) {
                $txt = '00000';
            } else if ($sec >= 10000 && $sec < 100000) {
                $txt = '0000';
            } else if ($sec >= 100000 && $sec < 1000000) {
                $txt = '000';
            } else if ($sec >= 1000000 && $sec < 10000000) {
                $txt = '00';
            } else if ($sec >= 10000000 && $sec < 100000000) {
                $txt = '0';
            } else if ($sec >= 100000000 && $sec < 1000000000) {
                $txt = '';
            }
        } else {
            $txt = '000000001';
        }
        $retorno = $txt . $sec;
        echo $retorno;
        break;

    case 4:
        if ($s == 0) {
            $cns = $Clase_retencion->lista_buscar_clientes(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = trim($rst[cli_apellidos] . ' ' . $rst[cli_nombres] . ' ' . $rst[cli_raz_social]);
                //$ruc=string($rst[cli_ced_ruc]);
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
        } else if ($s == 1) {
            $sms;
            $rst = pg_fetch_array($Clase_retencion->lista_clientes_cedula($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . trim($rst[cli_raz_social]) . '&' . $rst[cli_calle_prin] . ' ' . $rst[cli_numeracion] . ' ' . $rst[cli_calle_sec] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst[cli_id];
            }
            echo $sms;
        }

        break;
    case 5:
        $cns = $Clase_retencion->lista_retencion_completo();
        while ($rst = pg_fetch_array($cns)) {
            if (empty($rst[clave_acceso])) {
                $f = $rst['fecha_emision'];
                $f2 = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
                $cod_doc = "07"; //01= factura, 02=nota de credito tabla 4
                $emis[identificacion] = '1790007871001'; //Noperti
                $ambiente = 2;
                $ems = substr($rst[num_comprobante], 0, 3);
                $secuencial = substr($rst[num_comprobante], 6, 9);
                $codigo = "12345678"; //Del ejemplo del SRI                    
                $tp_emison = "1"; //Emision Normal                    
                $clave1 = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . "001" . $secuencial . $codigo . $tp_emison);
                $cla = strrev($clave1);
                $n = 0;
                $p = 1;
                $i = strlen($clave1);
                $m = 0;
                $s = 0;
                $j = 2;
                while ($n < $i) {
                    $d = substr($cla, $n, 1);
                    $m = $d * $j;
                    $s = $s + $m;
                    $j++;
                    if ($j == 8) {
                        $j = 2;
                    }
                    $n++;
                }
                $div = $s % 11;
                $digito = 11 - $div;
                if ($digito < 10) {
                    $digito = $digito;
                } else if ($digito == 10) {
                    $digito = 1;
                } else if ($digito == 11) {
                    $digito = 0;
                }
                $clave = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . "001" . $secuencial . $codigo . $tp_emison . $digito);
                if (strlen($clave) != 49) {
                    $clave = '';
                }
                echo $rst[num_comprobante] . '%%%';
                $Clase_retencion->upd_retencion_clave_acceso($clave, $rst[num_comprobante]);
            }
        }

        break;
    case 6:
        $sms = 0;
        if ($Clase_retencion->upd_retencion_na($_REQUEST[na], $_REQUEST[fh], $id) == FALSE) {

            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 7:
        $sms = 0;
        $ctaxpag = pg_fetch_array($Clase_retencion->lista_asientos_ctas('336')); ///falta ctas x pagar
        $prov = pg_fetch_array($Clase_retencion->lista_asientos_ctas('333'));
        if ($ctaxpag[pln_id] == '' || $prov[pln_id] == '') {
            $sms = 2;
        } else {
            if ($Clase_retencion->upd_estado_retencion($id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $asiento = $Clase_retencion->siguiente_asiento();
                $rst_ret = pg_fetch_array($Clase_retencion->lista_retencion_id($id));
                $rst_reg = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_ret[reg_id]));
                $cns_det = $Clase_retencion->lista_detalle_retencion($id);

                $dt_asi = array(
                    $rst_ret[ret_total_valor],
                    $rst_ret[ret_numero],
                    $rst_ret[ret_fecha_emision],
                    $prov[pln_codigo],
                    'ANULACION RETENCION'
                );
                if ($Clase_retencion->insert_asientos_ret($dt_asi, $asiento) == false) {
                    $sms = 'insert_asiento_ret1' . pg_last_error();
                    $aud = 1;
                }

                while ($rst_det = pg_fetch_array($cns_det)) {
                    $dt = explode('&', $data2[$n]);
                    $rst_ret = pg_fetch_array($Clase_retencion->lista_retencion_id($id));
                    $rst_reg = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_ret[reg_id]));
                    $rst_idcta = pg_fetch_array($Clase_retencion->lista_id_cuenta($rst_det[por_id]));
                    $rst_cta = pg_fetch_array($Clase_retencion->lista_cuenta_contable($rst_idcta[cta_id]));
                    $rst_regi = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_ret[reg_id]));
                    $concepto = $rst_idcta[por_descripcion] . ' ' . $rst_idcta[por_codigo];
                    $dt_asi1 = array(
                        $rst_det[dtr_valor],
                        $rst_ret[ret_numero],
                        $rst_ret[ret_fecha_emision],
                        $rst_cta[pln_codigo],
                        'ANULACION ' . $concepto
                    );
                    if ($Clase_retencion->insert_asientos_ret_anulacion($dt_asi1, $asiento) == false) {
                        $sms = 'insert_asiento_ret2' . pg_last_error();
                        $aud = 1;
                    }
                }


                if ($aud != 1) {
                    $ctp = pg_fetch_array($Clase_retencion->lista_ctasxpagar1($id));
                    if ($Clase_retencion->update_ctasxpagar($ctp[ctp_id]) == false) {
                        $sms = pg_last_error();
                    } else {
                        $asi = $Clase_retencion->siguiente_asiento();
                        $asiento = array(
                            $asi,
                            'ANULACION CUENTAS X PAGAR',
                            $rst_ret[ret_numero], //numero de documento retencion
                            $rst_ret[ret_fecha_emision], //fec
                            $ctaxpag[pln_codigo], //con_debe
                            $prov[pln_codigo], //con_haber
                            $rst_ret[ret_total_valor], //val_debe
                            $rst_ret[ret_total_valor], // val_haber
                            '0', //estado
                            $rst_ret[reg_id] // id documento registro factura
                        );
                        if ($Clase_retencion->insert_asientos($asiento) == false) {
                            $sms = 'asientos ' . pg_last_error();
                        }
                    }
                }
            }
        }
        echo $sms;
        break;

    case 8:
        if ($s == 0) {
            $cns = $Clase_retencion->lista_reg_facturas($tp_doc, $doc);
            $docu = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $rst_cli = pg_fetch_array($Clase_retencion->lista_proveedores($rst[reg_ruc_cliente]));
                $nm = $rst_cli[cli_raz_social];
                $docu .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_documento2('$rst[reg_ruc_cliente]','$doc')" . " /></td><td>$n</td><td>$rst[reg_num_documento]</td><td>$nm</td></tr>";
            }
            echo $docu;
        } else if ($s == 1) {
            $sms;
            $rst = pg_fetch_array($Clase_retencion->lista_clientes_cedula($id));
            if (!empty($rst)) {
                $rst_id_doc = pg_fetch_array($Clase_retencion->lista_id_reg_factura($doc, $id));
                $sms = $rst[cli_ced_ruc] . '&' . trim($rst[cli_raz_social]) . '&' . $rst[cli_calle_prin] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst_id_doc[reg_id] . '&' . $rst_id_doc[reg_sbt] . '&' . $rst_id_doc[reg_iva12];
            }
            echo $sms;
        }
        break;
}
?>
