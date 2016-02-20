<?php

include_once '../Clases/clsClase_cierre_caja.php';
include_once("../Clases/clsAuditoria.php");
$Clase_cierre_caja = new Clase_cierre_caja();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$user = strtoupper($_REQUEST[user]);
$emisor = $_REQUEST[emi];
$fec = $_REQUEST[fec];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        $sms = 0;
        $usu = pg_fetch_array($Clase_cierre_caja->lista_vendedores($user));
        $user = $usu[vnd_id];
//        $fecha = str_replace('-', '', $fec);
        $cns = pg_fetch_array($Clase_cierre_caja->lista_num_facturas($fec, $user, $emisor));
        if (!$Clase_cierre_caja->delete_cierre_bodega($fec, $user, $emisor)) {
            $sms = pg_last_error();
        } else {
//            while ($rst = pg_fetch_array($cns)) {
//                echo $rst[vendedor];
//                $doc = str_replace('-', '', $rst[num_documento]);

            $rst_cierre = pg_fetch_array($Clase_cierre_caja->lista_un_cierre_punto_fecha($emisor, $fec, $user));
            $rst_nfct = pg_fetch_array($Clase_cierre_caja->lista_fechaemi_factura($fec, $emisor, $user));
            $rst_npf = pg_fetch_array($Clase_cierre_caja->lista_cantidad_productos($fec, $emisor, $user));
            $rst_sbt = pg_fetch_array($Clase_cierre_caja->lista_total_subtotal($fec, $emisor, $user));
            $rst_tnc = pg_fetch_array($Clase_cierre_caja->lista_total_notacredito($fec, $emisor, $user));
            $rst_fp = pg_fetch_array($Clase_cierre_caja->lista_formas_pago($fec, $emisor, $user));
            $suma_subt = $rst_sbt[suma_subtotal] + $rst_sbt[suma_descuento];

            $rst_sec = pg_fetch_array($Clase_cierre_caja->lista_ultimo_secuencial($emisor));
            if ($emisor >= 10) {
                $ems = $emisor;
            } else {
                $ems = '0' . $emisor;
            }
            $sec = (substr($rst_sec[cie_secuencial], -4) + 1);
            if ($sec >= 0 && $sec < 10) {
                $txt = '000';
            } else if ($sec >= 10 && $sec < 100) {
                $txt = '00';
            } else if ($sec >= 100 && $sec < 1000) {
                $txt = '0';
            } else if ($sec >= 1000 && $sec < 10000) {
                $txt = '';
            }
            $secuencial = $ems . $txt . $sec;

            if ($Clase_cierre_caja->delete_asiento($secuencial) == FALSE) {
                $sms = pg_last_error();
            }

            $fac_emitidas = $rst_nfct[nfac];
            $suma_productos = $rst_npf[suma_cantidad];
            $suma_total_notcre += $rst_tnc[suma_total_valor_nc];
            $subtotal = $suma_subt;
            $descuento = $rst_sbt[suma_descuento];
            $iva = $rst_sbt[suma_iva];
            $suma_total_valor = $rst_sbt[suma_total_valor];
            $suma_tarjeta_credito = $rst_fp[tarjeta_credito];
            $suma_tarjeta_debito = $rst_fp[tarjeta_debito];
            $suma_cheque = $rst_fp[cheque];
            $suma_efectivo = $rst_fp[efectivo];
            $suma_certificados = $rst_fp[certificados];
            $suma_bonos = $rst_fp[bonos];
            $suma_retencion = $rst_fp[retencion];
            $suma_not_cre = $rst_fp[nota_credito];


            $dat = array(
                $secuencial,
                $fec,
                date('H:i:s'),
                $user,
                $emisor,
                $fac_emitidas,
                $suma_productos,
                str_replace(',', '', number_format($subtotal, 4)),
                str_replace(',', '', number_format($descuento, 4)),
                str_replace(',', '', number_format($iva, 4)),
                str_replace(',', '', number_format($suma_total_valor, 4)),
                str_replace(',', '', number_format($suma_total_notcre, 4)),
                str_replace(',', '', number_format($suma_tarjeta_credito, 4)),
                str_replace(',', '', number_format($suma_tarjeta_debito, 4)),
                str_replace(',', '', number_format($suma_cheque, 4)),
                str_replace(',', '', number_format($suma_efectivo, 4)),
                str_replace(',', '', number_format($suma_certificados, 4)),
                str_replace(',', '', number_format($suma_bonos, 4)),
                str_replace(',', '', number_format($suma_retencion, 4)),
                str_replace(',', '', number_format($suma_not_cre, 4))
            );
//            }
            if (empty($cns)) {
                $sms = 1;
            } else {
//                if (empty($rst_cierre)) {
                if ($Clase_cierre_caja->insert_cierre($dat) == false) {
                    $sms = pg_last_error();
                    $d = 1;
                }
            }
//                else {
//                    if ($Clase_cierre_caja->upd_cierre_caja($dat, $rst_cierre[cie_id]) == false) {
//                        $sms = pg_last_error();
//                        $d = 1;
//                    }
//                }
//            }

            if ($d == 0) {
//                if ($emisor == 2) {
//                    $debe1 = '1.01.02.05.101';
//                    $debe2 = '1.01.01.02.002';
//                    $haber = '1.01.02.05.002';
//                } else if ($emisor == 3) {
//                    $debe1 = '1.01.02.05.102';
//                    $debe2 = '1.01.01.02.003';
//                    $haber = '1.01.02.05.003';
//                } else if ($emisor == 4) {
//                    $debe1 = '1.01.02.05.103';
//                    $debe2 = '1.01.01.02.004';
//                    $haber = '1.01.02.05.004';
//                } else if ($emisor == 5) {
//                    $debe1 = '1.01.02.05.104';
//                    $debe2 = '1.01.01.02.005';
//                    $haber = '1.01.02.05.005';
//                } else if ($emisor == 6) {
//                    $debe1 = '1.01.02.05.105';
//                    $debe2 = '1.01.01.02.006';
//                    $haber = '1.01.02.05.006';
//                } else if ($emisor == 7) {
//                    $debe1 = '1.01.02.05.106';
//                    $debe2 = '1.01.01.02.002';
//                    $haber = '1.01.02.05.002';
//                } else if ($emisor == 8) {
//                    $debe1 = '1.01.02.05.107';
//                    $debe2 = '1.01.01.02.007';
//                    $haber = '1.01.02.05.007';
//                } else if ($emisor == 9) {
//                    $debe1 = '1.01.02.05.108';
//                    $debe2 = '1.01.01.02.009';
//                    $haber = '1.01.02.05.009';
//                }
//
//                $asiento = $Clase_cierre_caja->siguiente_asiento();
//                $tarjeta = $rst_fp[tarjeta_credito] + $rst_fp[tarjeta_debito];
//
//                if ($tarjeta != 0) {
//                    $dat0 = Array($asiento,
//                        'CIERRE CAJA',
//                        $secuencial,
//                        date('Y-m-d'),
//                        $debe1,
//                        $haber,
//                        str_replace(',', '', $tarjeta),
//                        str_replace(',', '', $rst_sbt[suma_total_valor])
//                    );
//                    $d = 1;
//                }
//                if ($d == 0) {
//                    $haber2 = $haber;
//                    $total = $rst_sbt[suma_total_valor];
//                } else {
//                    $haber2 = '';
//                    $total = 0;
//                }
//
//                if ($rst_fp[efectivo] != 0) {
//                    $dat1 = Array($asiento,
//                        'CIERRE CAJA',
//                        $secuencial,
//                        date('Y-m-d'),
//                        $debe2,
//                        $haber2,
//                        str_replace(',', '', $rst_fp[efectivo]),
//                        str_replace(',', '', $total)
//                    );
//                    $d = 1;
//                }
//                if ($d == 0) {
//                    $haber3 = $haber;
//                    $total1 = $rst_sbt[suma_total_valor];
//                } else {
//                    $haber3 = '';
//                    $total1 = 0;
//                }
//
//                if ($rst_fp[cheque] != 0) {
//                    $dat2 = Array($asiento,
//                        'CIERRE CAJA',
//                        $secuencial,
//                        date('Y-m-d'),
//                        '1.01.01.02.021',
//                        $haber3,
//                        str_replace(',', '', $rst_fp[cheque]),
//                        str_replace(',', '', $total1)
//                    );
//                }
//
//                $array = array($dat0, $dat1, $dat2);
//                $j = 0;
//                while ($j <= count($array)) {
//                    if (!empty($array[$j])) {
//                        if ($Clase_cierre_caja->insert_asientos($array[$j]) == false) {
//                            $sms = pg_last_error();
//                        }
//                    }
//                    $j++;
//                }

                $modulo = 'CIERRE DE CAJA LOCALES';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, '', $secuencial) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        }
        echo $sms;
        break;

    case 1:
        $sms = 0;
        if ($Clase_cierre_caja->upd_totales_cierres($data, $id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 2:
        $sms = 0;
        if (empty($id)) {
            $n = 0;
            while ($n < count($data2)) {
                $ncr .=$data2[$n] . '&';
                $n++;
            }
            if ($Clase_cierre_caja->insert_arqueo_caja($data, $ncr) == false) {
                $sms = 'Insert_arq_caja' . pg_last_error();
            }
        }
        if ($sms == 0) {
            $n = 0;
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'ARQUEO DE CAJA';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 3:
        $sms = 0;
        $cns = pg_fetch_array($Clase_cierre_caja->lista_arqueo_caja($fec, $emisor));
        if (empty($cns)) {
            $sms = 0;
        } else {
            $sms = 1;
        }
        echo $sms;
        break;
}
?>
