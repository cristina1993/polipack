<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_reg_retencion.php';
$Reg_retencion = new Clase_reg_retencion();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$id = $_REQUEST[id];
$x = $_REQUEST[x];
$fields = $_REQUEST[fields];
$s = $_REQUEST[s];
switch ($op) {
    case 0:
        $aud = 0;
        $sms = 0;
        if (empty($id)) {
            $rst_sec = pg_fetch_array($Reg_retencion->lista_secuencial($data[9]));
            if (!empty($rst_sec)) {
                $sms = 3;
            } else {
                $cli_ctas = pg_fetch_array($Reg_retencion->lista_asientos_ctas('299'));
                $ir_ctas = pg_fetch_array($Reg_retencion->lista_asientos_ctas('298'));
                $iv_ctas = pg_fetch_array($Reg_retencion->lista_asientos_ctas('300'));

                if ($cli_ctas[pln_id] == '') {
                    $sms = 1;
                } else {
                    $asi = $Reg_retencion->siguiente_asiento();

                    if ($Reg_retencion->insert_retencion($data) == false) {
                        $sms = pg_last_error() . 'ins_ret';
                        $aud = 1;
                    } else {
                        $n = 0;
                        $rst = pg_fetch_array($Reg_retencion->lista_retencion_numero($data[9]));
                        $id = $rst[rgr_id];
                        while ($n < count($data2)) {
                            $dt = explode('&', $data2[$n]);
                            if ($Reg_retencion->insert_det_retencion($dt, $id) == false) {
                                $sms = pg_last_error() . 'ins_det_ret';
                                $aud = 1;
                            }
                            $rst_idcta = pg_fetch_array($Reg_retencion->lista_id_cuenta($dt[0]));
                            $rst_cta = pg_fetch_array($Reg_retencion->lista_cuenta_contable($rst_idcta[cta_id]));

                            $dat0 = Array($asi,
                                'REGISTRO DE RETENCION',
                                $data[1],
                                $data[8],
                                $rst_cta[pln_codigo],
                                '',
                                round($dt[5], 2),
                                '0',
                                '0'
                            );


                            if ($Reg_retencion->insert_asientos($dat0) == false) {
                                $sms = 'Insert_asientos' . pg_last_error();
                                $aud = 1;
                            }
                            $n++;
                        }

                        $rst_cliente = pg_fetch_array($Reg_retencion->lista_asientos_ctas('299'));

                        $data1 = Array($asi,
                            'REGISTRO DE RETENCION',
                            $data[1],
                            $data[8],
                            '',
                            $rst_cliente[pln_codigo],
                            '0',
                            round($data[7], 2),
                            '0'
                        );


                        if ($Reg_retencion->insert_asientos($data1) == false) {
                            $sms = 'Insert_asientos Ins' . pg_last_error();
                            $aud = 1;
                        }

                        if ($Reg_retencion->update_asiento_ret($id, $asi) == false) {
                            $sms = 'Insert_asientos Upd' . pg_last_error();
                            $aud = 1;
                        }

                        $cobros = Array(
                            $data[0],
                            'RETENCION',
                            '0',
                            $data[1],
                            date('Y-m-d'),
                            $data[8],
                            $data[7],
                            '5',
                            '0',
                            $id
                        );
                        if ($Reg_retencion->insert_cobros($cobros) == false) {
                            $sms = pg_last_error() . 'ins_cobros';
                            $aud = 1;
                        }
                    }
                    if ($aud == 0) {
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = 'REG. RETENCION';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[9]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            }
        } else {
            if ($Reg_retencion->update_retencion($data, $id) == false) {
                $sms = pg_last_error() . 'upd_ret';
                $aud = 1;
            } else {
                if ($Reg_retencion->delete_det_retencion($id) == false) {
                    $sms = pg_last_error() . 'delete_ret';
                    $aud = 1;
                } else {
                    $n = 0;
                    while ($n < count($data2)) {
                        $dt = explode('&', $data2[$n]);
                        if ($Reg_retencion->insert_det_retencion($dt, $id) == false) {
                            $sms = pg_last_error() . 'ins_det_ret';
                            $aud = 1;
                        }
                        $n++;
                    }
                }
                $cobros = Array(
                    $data[0],
                    'RETENCION',
                    '0',
                    $data[1],
                    date('Y-m-d'),
                    $data[8],
                    '0',
                    '5',
                    $id
                );

                if ($Reg_retencion->update_cobros($cobros, $id) == false) {
                    $sms = pg_last_error() . 'ins_cobro';
                    $aud = 1;
                }
            }

            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'REG. RETENCION';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[9]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($Reg_retencion->delete_cobros($id) == false) {
            $sms = pg_last_error() . 'del_cobro';
        } else {
            if ($Reg_retencion->delete_det_retencion($id) == false) {
                $sms = pg_last_error();
            } else {
                if ($Reg_retencion->delete_retencion($id) == false) {
                    $sms = pg_last_error();
                } else {
                    $n = 0;
                    $f = $data;
                    $modulo = 'REG. RETENCION';
                    $accion = 'ELIMINAR';
                    if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }
        }

        echo $sms;
        break;

    case 2:
        $rst = pg_fetch_array($Reg_retencion->lista_datos_porcentaje($id));
        $rst_cuenta = pg_fetch_array($Reg_retencion->lista_cuentas_act_inac($rst[cta_id]));
        $descripcion = $rst[por_descripcion];
        $porcentaje = $rst[por_porcentage];
        $cod = $rst[por_codigo];
        $por_id = $rst[por_id] . '_' . $rst[por_siglas];
        echo $descripcion . '&' . $porcentaje . '&' . $cod . '&' . $por_id . '&' . $rst[por_siglas] . '&' . $rst[cta_id] . '&' . $rst_cuenta[pln_estado];
        break;



    case 4:
        if ($s == 0) {
            $cns = $Reg_retencion->lista_buscar_clientes(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = trim($rst[cli_apellidos] . ' ' . $rst[cli_nombres] . ' ' . $rst[cli_raz_social]);
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
        } else if ($s == 1) {
            $sms;
            $rst = pg_fetch_array($Reg_retencion->lista_clientes_cedula($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . trim($rst[cli_raz_social]) . '&' . $rst[cli_id];
            }
            echo $sms;
        }

        break;

    case 5:
        $rst = pg_fetch_array($Reg_retencion->lista_una_factura_nfact($id));
//        $base=$rst[subtotal] + $rst[subtotal0] + $rst[subtotal_exento_iva] + $rst[subtotal_no_objeto_iva];
        $base = $rst[fac_subtotal];
        echo $rst[fac_id] . '&' .
        $rst[fac_fecha_emision] . '&' .
        $rst[fac_identificacion] . '&' .
        $rst[fac_nombre] . '&' .
        $rst[cli_id] . '&' .
        $rst[fac_numero] . '&' .
        $rst[fac_total_iva] . '&' .
        $base;
        break;

    case 6:
        $rst = pg_fetch_array($Reg_retencion->lista_retencion_duplicada($id, $data));
        echo $rst[rnd_identificacion] . '&' . $rst[rnd_numero];
        break;

    case 7:
        $sms = 0;
        $rst_cob = pg_fetch_array($Reg_retencion->lista_cobro_doc($_REQUEST[md_id]));
        if (!empty($rst_cob)) {
            if ($Reg_retencion->update_estado_reg_retencion($_REQUEST[md_id], $_REQUEST[estado]) == true) {
                if ($Reg_retencion->update_estado_det_retencion($_REQUEST[md_id], $_REQUEST[estado]) == false) {
                    $sms = 'Update_reg_det' . pg_last_error();
                } else {
                    if ($Reg_retencion->update_estado_cobros($_REQUEST[md_id], '3') == false) {
                        $sms = 'ctasxpagar ' . pg_last_error();
                    } else {
                        $asi = $Reg_retencion->siguiente_asiento();
                        $rst_ndoc = pg_fetch_array($Reg_retencion->lista_una_retencion($_REQUEST[md_id]));
                        $rst_cliente = pg_fetch_array($Reg_retencion->lista_asientos_ctas('299'));
                        $cns_det = $Reg_retencion->lista_det_retencion($_REQUEST[md_id]);
                        while ($rst_rd = pg_fetch_array($cns_det)) {
                            $rst_idcta = pg_fetch_array($Reg_retencion->lista_id_cuenta($rst_rd[por_id]));
                            $rst_cta = pg_fetch_array($Reg_retencion->lista_cuenta_contable($rst_idcta[cta_id]));

                            $data1 = Array($asi,
                                'ANULACION REGISTRO DE RETENCION',
                                $rst_ndoc[rgr_numero],
                                $rst_ndoc[rgr_fecha_emision],
                                '',
                                $rst_cta[pln_codigo],
                                '0',
                                round($rst_rd[drr_valor],2),
                                '0'
                            );
                            if ($Reg_retencion->insert_asientos($data1) == false) {
                                $sms = 'Insert_asientos' . pg_last_error();
                                $aud = 1;
                            }
                        }

                        $dat0 = Array($asi,
                            'ANULACION REGISTRO DE RETENCION',
                            $rst_ndoc[rgr_numero],
                            $rst_ndoc[rgr_fecha_emision],
                            $rst_cliente[pln_codigo],
                            '',
                            round($rst_ndoc[rgr_total_valor],2),
                            '0',
                            '0'
                        );
                        if ($Reg_retencion->insert_asientos($dat0) == false) {
                            $sms = 'Insert_asientos' . pg_last_error();
                            $aud = 1;
                        }
//                  
                    }
                }
            } else {
                $sms = 'Update_reg_encab' . pg_last_error();
            }
        } else {
            $sms = 1;
        }

        echo $sms;
        break;
}
?>
