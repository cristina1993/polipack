<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_asientos_automaticos.php';
$Clase_asientos_automaticos = new Clase_asientos_automaticos();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$x = $_REQUEST[x];
$fields = $_REQUEST[fields];
switch ($op) {

    case 0:
        $sms = 0;
        $fle12 = 0;
        $fle0 = 0;
        $tf0 = 0;
        if (isset($id)) {
            $cns = $Clase_asientos_automaticos->lista_facturas_documento($id);
            $rst_f = pg_fetch_array($Clase_asientos_automaticos->lista_facturas_documento($id));
            if ($x == 1) {
                if ($Clase_asientos_automaticos->delete_asientos($rst_f[fac_numero], 'FACTURACION VENTA') == FALSE) {
                    $sms = pg_last_error();
                }
            }
        } else {
            $cns = $Clase_asientos_automaticos->lista_facturas();
        }
        while ($rst = pg_fetch_array($cns)) {
            $rst_as = pg_fetch_array($Clase_asientos_automaticos->lista_asientos($rst[fac_numero], 'FACTURACION VENTA'));
            if (empty($rst_as)) {
                $asiento = $Clase_asientos_automaticos->siguiente_asiento();
                $fec = $rst[fac_fecha_emision];
                $num_doc = $rst[fac_numero];

                $cns_det = $Clase_asientos_automaticos->lista_det_fac($rst[fac_id]);

                while ($rst_det = pg_fetch_array($cns_det)) {
                    if (strtoupper($rst_det[dfc_codigo]) == 'FLETE 1') {
                        $fle0 = 1;
                        $tf0 = $rst_det[dfc_precio_total] + $rst_det[dfc_val_descuento] + $tf0;
                    }

                    if (strtoupper($rst_det[dfc_codigo]) == 'FLETE 2') {
                        $fle12 = 1;
                        $tf12 = $rst_det[dfc_precio_total] + $rst_det[dfc_val_descuento] + $tf12;
                    }
                }

                if ($rst[emi_id] == 1) {
                    $pid = 3;
                    $rst_tip = pg_fetch_array($Clase_asientos_automaticos->lista_un_cliente($rst[fac_identificacion]));
                    if ($rst_tip[cli_tipo_cliente] == 0) {
                        $cli = 1;
                    } else {
                        $cli = 2;
                    }
                    $iv = 4;
                    $dsc = 5;
                    $fl = 6;
                } else if ($rst[emi_id] == 2) {
                    $cli = 11;
                    $pid = 12;
                    $iv = 13;
                    $dsc = 14;
                    $fl = 15;
                } else if ($rst[emi_id] == 3) {
                    $cli = 27;
                    $pid = 28;
                    $iv = 29;
                    $dsc = 30;
                    $fl = 31;
                } else if ($rst[emi_id] == 4) {
                    $cli = 43;
                    $pid = 44;
                    $iv = 45;
                    $dsc = 46;
                    $fl = 47;
                } else if ($rst[emi_id] == 5) {
                    $cli = 59;
                    $pid = 60;
                    $iv = 61;
                    $dsc = 62;
                    $fl = 63;
                } else if ($rst[emi_id] == 6) {
                    $cli = 75;
                    $pid = 76;
                    $iv = 77;
                    $dsc = 78;
                    $fl = 79;
                } else if ($rst[emi_id] == 7) {
                    $cli = 91;
                    $pid = 92;
                    $iv = 93;
                    $dsc = 94;
                    $fl = 95;
                } else if ($rst[emi_id] == 8) {
                    $cli = 107;
                    $pid = 108;
                    $iv = 109;
                    $dsc = 110;
                    $fl = 111;
                } else if ($rst[emi_id] == 9) {
                    $cli = 123;
                    $pid = 124;
                    $iv = 125;
                    $dsc = 126;
                    $fl = 127;
                } else if ($rst[emi_id] == 10) {
                    $rst_tip = pg_fetch_array($Clase_asientos_automaticos->lista_un_cliente($rst[fac_identificacion]));
                    if ($rst_tip[cli_tipo_cliente] == 0) {
                        $cli = 139;
                    } else {
                        $cli = 140;
                    }
                    $pid = 141;
                    $iv = 142;
                    $dsc = 143;
                    $fl = 144;
                } else if ($rst[emi_id] == 11) {
                    $cli = 149;
                    $pid = 150;
                    $iv = 151;
                    $dsc = 152;
                    $fl = 153;
                } else if ($rst[emi_id] == 12) {
                    $cli = 165;
                    $pid = 166;
                    $iv = 167;
                    $dsc = 168;
                    $fl = 169;
                } else if ($rst[emi_id] == 13) {
                    $cli = 181;
                    $pid = 182;
                    $iv = 183;
                    $dsc = 184;
                    $fl = 185;
                } else if ($rst[emi_id] == 14) {
                    $cli = 197;
                    $pid = 198;
                    $iv = 199;
                    $dsc = 200;
                    $fl = 201;
                }




                $cta_fle = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($fl));
                if ($fle12 != 0) {
                    $dat4 = Array($asiento,
                        'FACTURACION VENTA',
                        $num_doc,
                        $fec,
                        '',
                        $cta_fle[pln_codigo],
                        '0.00',
                        $tf12
                    );
                }

                if ($fle0 != 0) {
                    $dat5 = Array($asiento,
                        'FACTURACION VENTA',
                        $num_doc,
                        $fec,
                        '',
                        $cta_fle[pln_codigo],
                        '0.00',
                        $tf0
                    );
                }


//                $rst_plan = pg_fetch_array($Clase_asientos_automaticos->lista_plan($pid));
                $rst_cliente = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($cli));
                $rst_iv = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($iv));
                $rst_vta = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($pid));
                $des = pg_fetch_array($Clase_asientos_automaticos->lista_suma_descuentos_factura($rst[fac_id]));

                if ($rst[fac_subtotal12] != 0) {
                    $subtotal = $rst[fac_subtotal12] + $des[desc12] - $tf12;
                    $cod_pln = $rst_plan[pln_codigo];
                } else {
                    $subtotal = '0.00';
                    $cod_pln = '';
                }

                $dat0 = Array($asiento,
                    'FACTURACION VENTA',
                    $num_doc,
                    $fec,
                    $rst_cliente[pln_codigo],
                    $rst_vta[pln_codigo],
                    $rst[fac_total_valor],
                    $subtotal
                );

                if ($rst[fac_subtotal12] != 0) {
                    $dat1 = Array($asiento,
                        'FACTURACION VENTA',
                        $num_doc,
                        $fec,
                        '',
                        $rst_iv[pln_codigo],
                        '0.00',
                        $rst[fac_total_iva]
                    );
                }

                $sub0 = $rst[fac_subtotal0] + $des[desc0] + $rst[fac_subtotal_ex_iva] + $des[descex] + $rst[fac_subtotal_no_iva] + $des[descno] - $tf0;
                if (($rst[fac_subtotal0] != 0 || $rst[fac_subtotal_ex_iva] != 0 || $rst[fac_subtotal_no_iva] != 0) && $sub0 != 0) {
                    $rst_plan = pg_fetch_array($Clase_asientos_automaticos->lista_plan($pid));
                    $dat2 = Array($asiento,
                        'FACTURACION VENTA',
                        $num_doc,
                        $fec,
                        '',
                        $rst_vta[pln_codigo],
                        '0.00',
                        $sub0
                    );
                }

                if ($rst[fac_total_descuento] != 0) {
                    $rst_dsc = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($dsc));
                    $dat3 = Array($asiento,
                        'FACTURACION VENTA',
                        $num_doc,
                        $fec,
                        $rst_dsc[pln_codigo],
                        '',
                        $rst[fac_total_descuento],
                        '0.00'
                    );
                }



                $array = array($dat0, $dat1, $dat2, $dat3, $dat4, $dat5);
                $j = 0;
                while ($j <= count($array)) {
                    if (!empty($array[$j])) {
                        if ($Clase_asientos_automaticos->insert_asientos($array[$j]) == false) {
                            $sms = pg_last_error();
                        }
                    }
                    $j++;
                }
                $dat0 = array();
                $dat1 = array();
                $dat2 = array();
                $dat3 = array();
                $dat4 = array();
                $dat5 = array();
                $fle12 = 0;
                $fle0 = 0;
            }
        }
        echo $sms;
        break;

    case 1:
        $sms = 0;
        if (isset($id)) {
            $cns = $Clase_asientos_automaticos->lista_notacre_documento($id);
            if ($x == 1) {
                $rst_nota = pg_fetch_array($Clase_asientos_automaticos->lista_notacre_documento($id));
                if ($Clase_asientos_automaticos->delete_asientos($rst_nota[ncr_numero], 'DEVOLUCION VENTA') == FALSE) {
                    $sms = pg_last_error();
                }
            }
        } else {
            $cns = $Clase_asientos_automaticos->lista_notas_credito();
        }

        while ($rst = pg_fetch_array($cns)) {
            $rst_as = pg_fetch_array($Clase_asientos_automaticos->lista_asientos($rst[ncr_numero], 'DEVOLUCION VENTA'));
            if (empty($rst_as)) {
                $asiento = $Clase_asientos_automaticos->siguiente_asiento();
                $fec = $rst[ncr_fecha_emision];
                $num_doc = $rst[ncr_numero];
                if ($rst[emi_id] == 1) {
                    $rst_tip = pg_fetch_array($Clase_asientos_automaticos->lista_un_cliente($rst[ncr_identificacion]));
                    if ($rst_tip[cli_tipo_cliente] == 0) {
                        $cli = 8;
                    } else {
                        $cli = 9;
                    }
                    $pid = 7;
                    $iv = 10;
                } else if ($rst[emi_id] == 2) {
                    $pid = 24;
                    $cli = 25;
                    $iv = 26;
                } else if ($rst[emi_id] == 3) {
                    $pid = 40;
                    $cli = 41;
                    $iv = 42;
                } else if ($rst[emi_id] == 4) {
                    $pid = 56;
                    $cli = 57;
                    $iv = 58;
                } else if ($rst[emi_id] == 5) {
                    $pid = 72;
                    $cli = 73;
                    $iv = 74;
                } else if ($rst[emi_id] == 6) {
                    $pid = 88;
                    $cli = 89;
                    $iv = 90;
                } else if ($rst[emi_id] == 7) {
                    $pid = 104;
                    $cli = 106;
                    $iv = 107;
                } else if ($rst[emi_id] == 8) {
                    $pid = 120;
                    $cli = 121;
                    $iv = 122;
                } else if ($rst[emi_id] == 9) {
                    $pid = 136;
                    $cli = 137;
                    $iv = 138;
                } else if ($rst[emi_id] == 10) {
                    $pid = 145;
                    $rst_tip = pg_fetch_array($Clase_asientos_automaticos->lista_un_cliente($rst[ncr_identificacion]));
                    if ($rst_tip[cli_tipo_cliente] == 0) {
                        $cli = 146;
                    } else {
                        $cli = 147;
                    }
                    $iv = 148;
                } else if ($rst[emi_id] == 11) {
                    $pid = 162;
                    $cli = 163;
                    $iv = 164;
                } else if ($rst[emi_id] == 12) {
                    $pid = 178;
                    $cli = 179;
                    $iv = 180;
                } else if ($rst[emi_id] == 13) {
                    $pid = 194;
                    $cli = 195;
                    $iv = 196;
                } else if ($rst[emi_id] == 14) {
                    $pid = 210;
                    $cli = 211;
                    $iv = 212;
                }
                $rst_dev = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($pid));
                $rst_cliente = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($cli));
                $des = pg_fetch_array($Clase_asientos_automaticos->lista_suma_descuentos_nota_cred($id));
                $subtotal = $rst[ncr_subtotal12] + $des[desc12] + $rst[ncr_subtotal0] + $des[desc0] + $rst[ncr_subtotal_ex_iva] + $des[descex] + $rst[ncr_subtotal_no_iva] + $des[descno];


                $val_tot = $rst[nrc_total_valor] + $rst[ncr_total_descuento];
                $dat0 = Array($asiento,
                    'DEVOLUCION VENTA',
                    $num_doc,
                    $fec,
                    $rst_dev[pln_codigo],
                    $rst_cliente[pln_codigo],
                    $subtotal,
                    $val_tot
                );

                if ($rst[ncr_total_iva] != 0) {
                    $rst_iv = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($iv));
                    $dat1 = Array($asiento,
                        'DEVOLUCION VENTA',
                        $num_doc,
                        $fec,
                        $rst_iv[pln_codigo],
                        '',
                        $rst[ncr_total_iva],
                        '0.00'
                    );
                }

//
//                if ($rst[total_descuento] != 0) {
//                    if ($rst[cod_punto_emision] == 1) {
//                        $desc = 1350;
//                    } else if ($rst[cod_punto_emision] == 2) {
//                        $desc = 1351;
//                    } else if ($rst[cod_punto_emision] == 3) {
//                        $desc = 1352;
//                    } else if ($rst[cod_punto_emision] == 4) {
//                        $desc = 1353;
//                    } else if ($rst[cod_punto_emision] == 5) {
//                        $desc = 1354;
//                    } else if ($rst[cod_punto_emision] == 6) {
//                        $desc = 1355;
//                    } else if ($rst[cod_punto_emision] == 7) {
//                        $desc = 1356;
//                    } else if ($rst[cod_punto_emision] == 8) {
//                        $desc = 1357;
//                    } else if ($rst[cod_punto_emision] == 9) {
//                        $desc = 1358;
//                    } else if ($rst[cod_punto_emision] == 10) {
//                        $desc = 1359;
//                    }
//                    $rst_plan = pg_fetch_array($Clase_asientos_automaticos->lista_plan($desc));
//                    $dat2 = Array($asiento,
//                        'DEVOLUCION VENTA',
//                        $num_doc,
//                        $fec,
//                        '',
//                        $rst_plan[pln_codigo],
//                        '0.00',
//                        $rst[total_descuento]
//                    );
//                }
//                $array = array($dat0, $dat1, $dat2);
                $array = array($dat0, $dat1);
                $j = 0;
                while ($j <= count($array)) {
                    if (!empty($array[$j])) {
                        if ($Clase_asientos_automaticos->insert_asientos($array[$j]) == false) {
                            $sms = pg_last_error();
                        }
                    }
                    $j++;
                }
                $dat0 = array();
                $dat1 = array();
                $dat2 = array();
                $dat3 = array();
            }
        }
        echo $sms = 0;
        break;
}
?>
