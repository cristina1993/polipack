<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_reg_nota_debito.php';
$Reg_nota_debito = new Clase_reg_nota_debito();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$id = $_REQUEST[id];
$x = $_REQUEST[x];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        $aud = 0;
        if (empty($id)) {
            $rst_sec = pg_fetch_array($Reg_nota_debito->lista_secuencial($data[16]));
            if (!empty($rst_sec)) {
                $sms = 3;
            } else {
                $prov = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('213'));
                $ctaxpagar = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('214'));
                $cta1 = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('276'));
                $cta2 = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('277'));
                $cta3 = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('278'));
                if ($prov[pln_id] == '' || $ctaxpagar[pln_id] == '' || $cta1[pln_id] == '' || $cta2[pln_id] == '' || $cta3[pln_id] == '') {
                    $sms = 1;
                } else {

                    if ($Reg_nota_debito->insert_nota_debito($data) == TRUE) {
                        $nd = pg_fetch_array($Reg_nota_debito->lista_una_nota_debito($data[16]));
                        $ndb_id = $nd[rnd_id];
                        $n = 0;
                        while ($n < count($data1)) {
                            $dt = explode('&', $data1[$n]);
                            if ($Reg_nota_debito->insert_det_nota_debito($dt, $ndb_id) == false) {
                                $sms = 'Insert_det' . pg_last_error();
                                $aud = 1;
                            }
                            $n++;
                        }
                    } else {
                        $sms = 'Insert_nota' . pg_last_error();
                        $aud = 1;
                    }

                    if ($aud == 0) {
                        $rp = pg_fetch_array($Reg_nota_debito->buscar_un_pago_doc($nd[reg_id]));
                        if (empty($rp)) {
                            $rp2 = pg_fetch_array($Reg_nota_debito->buscar_un_pago_doc1($nd[reg_id]));
                            $pag_id = $rp2[pag_id];
                        } else {
                            $pag_id = $rp[pag_id];
                        }
                        $rst_cliente = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('213'));
                        $rst_cta = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('214'));
                        $cta = array(
                            $nd[reg_id], //com_id
                            $data[18], //cta_fec
                            $data[15], //cta_monto
                            'NOTA DE DEBITO', //forma de pago
                            $rst_cta[pln_codigo], //cta_banco
                            $rst_cliente[pln_id], /// pln_id
                            $data[18], //fec_pag
                            $pag_id, //pag_id
                            '0', //num_doc
                            'NOTA DE DEBITO', //cta_concepto
                            '2', //asiento
                            '0', //chq_id
                            $ndb_id //doc_id
                        );
                        if ($Reg_nota_debito->insert_ctasxpagar($cta) == false) {
                            $sms = 'ctasxpagar ' . pg_last_error();
                        } else {
                            $asi = $Reg_nota_debito->siguiente_asiento();
                            $rst_cliente = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('213'));
                            $rst_cta = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('214'));
                            $asiento = array(
                                $asi,
                                'CUENTAS X PAGAR',
                                $nd[rnd_numero], //doc
                                $data[18], //fec
                                $rst_cliente[pln_codigo], //con_debe
                                $rst_cta[pln_codigo], //con_haber
                                $data[15], //val_debe
                                $data[15], // val_haber
                                '0', //estado
                                $ndb_id//doc_id
                            );
                            if ($Reg_nota_debito->insert_asientos($asiento) == false) {
                                $sms = 'asientos ' . pg_last_error();
                            }
                            $asient = $Reg_nota_debito->siguiente_asiento();
                            $dat_asi = array($nd[rnd_subtotal],
                                $nd[rnd_numero],
                                $nd[rnd_fecha_emision],
                                '',
                                $nd[rnd_total_iva],
                                $nd[rnd_total_ice],
                                '0',
                                $prov[pln_codigo],
                                $cta1[pln_codigo], // cta subtotal
                                $cta2[pln_codigo], //cta total iva
                                $cta3[pln_codigo], // cta ice
                                $data[15]); // val total
                            if ($Reg_nota_debito->insert_asiento_mp($dat_asi, $asient) == false) {
                                $sms = 'asientos_1' . pg_last_error();
                            }
                        }
                    }

                    if ($aud == 0) {
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = 'REG. NOTA DE DEBITO';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[16]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            }
        } else {
            $prov = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('213'));
            $ctaxpagar = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('214'));

            if ($prov[pln_id] == '' || $ctaxpagar[pln_id] == '') {
                $sms = 1;
            } else {

                if ($Reg_nota_debito->upd_nota_debito($data, $id) == TRUE) {
                    if ($Reg_nota_debito->delete_det_nota($id) == false) {
                        $sms = 'delete' . pg_last_error();
                        $aud = 1;
                    } else {
                        $ndb_id = $id;
                        $n = 0;
                        while ($n < count($data1)) {
                            $dt = explode('&', $data1[$n]);
                            if ($Reg_nota_debito->insert_det_nota_debito($dt, $ndb_id) == false) {
                                $sms = 'Insert_det' . pg_last_error();
                                $aud = 1;
                            }
                            $n++;
                        }
                    }
                } else {
                    $sms = 'Insert_nota' . pg_last_error();
                    $aud = 1;
                }

                if ($aud == 0) {
                    if ($Reg_nota_debito->delete_asientos($id, $data[1], 'CUENTAS X PAGAR') == false) {
                        $sms = 'ctasxpagar ' . pg_last_error();
                    } else {
                        if ($Reg_nota_debito->delete_ctasxpagar($id, 'NOTA DE DEBITO') == false) {
                            $sms = 'ctasxpagar ' . pg_last_error();
                        } else {
                            $rp = pg_fetch_array($Reg_nota_debito->buscar_un_pago_doc($data[22]));
                            if (empty($rp)) {
                                $rp2 = pg_fetch_array($Reg_nota_debito->buscar_un_pago_doc1($data[22]));
                                $pag_id = $rp2[pag_id];
                            } else {
                                $pag_id = $rp[pag_id];
                            }
                            $rst_cliente = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('213'));
                            $rst_cta = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('214'));
                            $cta = array(
                                $data[22], //com_id
                                $data[18], //cta_fec
                                $data[15], //cta_monto
                                'NOTA DE DEBITO', //forma de pago
                                $rst_cta[pln_codigo], //cta_banco
                                $rst_cliente[pln_id], /// pln_id
                                $data[18], //fec_pag
                                $pag_id, //pag_id
                                '0', //num_doc
                                'NOTA DE DEBITO', //cta_concepto
                                '2', //asiento
                                '0', //chq_id
                                $id //doc_id
                            );
                            if ($Reg_nota_debito->insert_ctasxpagar($cta) == false) {
                                $sms = 'ctasxpagar ' . pg_last_error();
                            } else {
                                $asi = $Reg_nota_debito->siguiente_asiento();
                                $rst_cliente = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('213'));
                                $rst_cta = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('214'));
                                $asiento = array(
                                    $asi,
                                    'CUENTAS X PAGAR',
                                    $data[1], //doc
                                    $data[18], //fec
                                    $rst_cliente[pln_codigo], //con_debe
                                    $rst_cta[pln_codigo], //con_haber
                                    $data[15], //val_debe
                                    $data[15], // val_haber
                                    '0', //estado
                                    $id//doc_id
                                );
                                if ($Reg_nota_debito->insert_asientos($asiento) == false) {
                                    $sms = 'asientos ' . pg_last_error();
                                }
                            }
                        }
                    }
                }

                if ($aud == 0) {
                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'REG. NOTA DE DEBITO';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $data[16]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }
        }
        echo $sms;
        break;

    case 1:
        $sms = 0;
        if ($Reg_nota_debito->delete_asientos($id, $data1, 'CUENTAS X PAGAR') == false) {
            $sms = 'ctasxpagar ' . pg_last_error();
        } else {
            if ($Reg_nota_debito->delete_ctasxpagar($id, 'NOTA DE DEBITO') == false) {
                $sms = 'ctasxpagar ' . pg_last_error();
            } else {
                if ($Reg_nota_debito->delete_det_nota($id) == FALSE) {
                    $sms = pg_last_error() . 'delet_det1';
                } else {
                    if ($Reg_nota_debito->delete_nota_debito($id) == FALSE) {
                        $sms = pg_last_error() . 'delete1';
                    }
                }
            }
        }

        $modulo = 'REG. NOTA DE DEBITO';
        $accion = 'ELIMINAR';
        if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
            $sms = "Auditoria" . pg_last_error();
        }
        break;
    case 4:
        if ($s == 0) {
            $cns = $Reg_nota_debito->lista_buscar_clientes(strtoupper($id));
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
            $rst = pg_fetch_array($Reg_nota_debito->lista_clientes_cedula($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . trim($rst[cli_raz_social]) . '&' . $rst[cli_id];
            }
            echo $sms;
        }

        break;

    case 5:
        if ($x == 0) {
            $cns = $Reg_nota_debito->lista_una_factura_nfact($id);
            $fac = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[reg_id];
                $fac .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_factura2('$rst[reg_id]')" . " /></td><td>$n</td><td>$rst[reg_num_documento]</td><td>$rst[cli_raz_social]</td></tr>";
                $sms = 1;
            }
            echo $sms . '&&' . $fac;
        } else {
            $rst = pg_fetch_array($Reg_nota_debito->lista_un_regfactura_id($id));
            echo $rst[reg_id] . '&' .
            $rst[reg_femision] . '&' .
            $rst[reg_ruc_cliente] . '&' .
            $rst[cli_raz_social] . '&' .
            $rst[cli_id];
        }
        break;

    case 6:
        $rst = pg_fetch_array($Reg_nota_debito->lista_nota_deb_duplicada($id, $data));
        echo $rst[rnd_identificacion] . '&' . $rst[rnd_numero];
        break;

    case 7:
        $prov = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('215'));
        $ctaxpagar = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('216'));
        $cta1 = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('279'));
        $cta2 = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('280'));
        $cta3 = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('281'));
        if ($prov[pln_id] == '' || $ctaxpagar[pln_id] == '' || $cta1[pln_id] == '' || $cta2[pln_id] == '' || $cta3[pln_id] == '') {
            $sms = 1;
        } else {
            $sms = 0;
            if ($Reg_nota_debito->update_estado_reg_nd($_REQUEST[md_id], $_REQUEST[estado]) == true) {
                if ($Reg_nota_debito->update_estado_det_nd($_REQUEST[md_id], $_REQUEST[estado]) == false) {
                    $sms = 'Update_reg_det' . pg_last_error();
                } else {
                    if ($Reg_nota_debito->update_ctasxpagar($_REQUEST[md_id], '1') == false) {
                        $sms = 'ctasxpagar ' . pg_last_error();
                    } else {
                        $asi = $Reg_nota_debito->siguiente_asiento();
                        $rst_cliente = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('215'));
                        $rst_cta = pg_fetch_array($Reg_nota_debito->lista_asientos_ctas('216'));
                        $rst_reg = pg_fetch_array($Reg_nota_debito->lista_una_nota_debito_id($_REQUEST[md_id]));
                        $asiento = array(
                            $asi,
                            'ANULACION CUENTAS X PAGAR',
                            $rst_reg[rnd_numero], //doc
                            $rst_reg[rnd_fecha_emision], //fec
                            $rst_cta[pln_codigo], //con_haber
                            $rst_cliente[pln_codigo], //con_debe
                            $rst_reg[rnd_total_valor], // val_haber
                            $rst_reg[rnd_total_valor], //val_debe
                            '0', //estado
                            $_REQUEST[md_id]//doc_id
                        );
                        if ($Reg_nota_debito->insert_asientos($asiento) == false) {
                            $sms = 'asientos ' . pg_last_error();
                        }
                    }
                }
            } else {
                $sms = 'Update_reg_encab' . pg_last_error();
            }
        }
        echo $sms;
        break;
}
?>
