<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_nota.php';
$Clase_nota_Credito = new Clase_nota_Credito();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$id = $_REQUEST[id];
$x = $_REQUEST[x];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $aud = 0;
        $sms = 0;
        if (empty($id)) {
            switch ($data[1]) {
                case 1:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('7'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('8'));
                    $cli_ext_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('9'));
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('10'));
                    break;
                case 2:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('24'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('25'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('26'));
                    break;
                case 3:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('40'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('41'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('42'));
                    break;
                case 4:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('56'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('57'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('58'));
                    break;
                case 5:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('72'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('73'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('74'));
                    break;
                case 6:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('88'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('89'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('90'));
                    break;
                case 7:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('104'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('105'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('106'));
                    break;
                case 8:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('120'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('121'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('122'));
                    break;
                case 9:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('136'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('137'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('138'));
                    break;
                case 10:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('145'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('146'));
                    $cli_ext_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('147'));
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('148'));
                    break;
                case 11:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('162'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('163'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('164'));
                    break;
                case 12:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('178'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('179'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('180'));
                    break;
                case 13:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('194'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('195'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('196'));
                    break;
                case 14:
                    $ven_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('210'));
                    $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('211'));
                    $cli_ext_ctas[pln_id] = '0';
                    $iva_ctas = pg_fetch_array($Clase_nota_Credito->lista_asientos_ctas('212'));
                    break;
            }
            if ($ven_ctas[pln_id] == '' || $cli_nac_ctas[pln_id] == '' || $cli_ext_ctas[pln_id] == '' || $iva_ctas[pln_id] == '') {
                $sms = 2;
            } else {
            if ($data[1] >= 10) {
                $ems = '0' . $data[1];
            } else {
                $ems = '00' . $data[1];
            }
            $rst_sec = pg_fetch_array($Clase_nota_Credito->lista_secuencial_nota_credito($ems));
            if (empty($rst_sec)) {
                $sec = 1;
            } else {
                $se = explode('-', $rst_sec[ncr_numero]);
                $sec = ($se[2] + 1);
            }
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

            $comprobante = $ems . '-001-' . $tx . $sec;
            $cli_id = $data[0];
            if ($v == 0) {
                $dat = Array(
                    $cli_id,
                    $data[1], //emisor
                    $data[24], //vendedor
                    $comprobante, //numero
                    strtoupper($data[3]), //motivo
                    $data[4], //fecha emision
                    strtoupper($data[5]), //nombre
                    strtoupper($data[6]), //identificacion
                    $data[7], //email
                    strtoupper($data[8]), //direccion
                    $data[9], //denominacion comp
                    $data[10], //numero_comp
                    $data[11], //fecha_comp
                    $data[12], //iva12
                    $data[13], //iva0
                    $data[14], //ivaex
                    $data[15], //ivano
                    $data[16], //desc
                    $data[17], //ice
                    $data[18], //total iva
                    $data[19], //total irbpnr
                    $data[20], //telefono
                    $data[21], //total valor
                    $data[22], //total propina
                    $data[25], //fac_id
                    $data[23], //trs_id
                    $data[26] //subtotal
                );

                if ($Clase_nota_Credito->insert_nota_credito($dat) == TRUE) {
                    $nc = pg_fetch_array($Clase_nota_Credito->lista_un_notac_num($comprobante));
                    $nrc_id = $nc[ncr_id];
                    $n = 0;
                    while ($n < count($data1)) {
                        $dt = explode('&', $data1[$n]);
                        $num_nota = str_replace('-', '', $comprobante);
                        if ($Clase_nota_Credito->insert_det_nota_credito($dt, $nrc_id) == TRUE) {
                            $bod = $data[1];
                            $tab = $dt[16];
                            if ($tab == 0) {
                                $rst_pro = pg_fetch_array($Clase_nota_Credito->lista_un_producto_industrial_id($dt[0]));
//                                if (($rst_pro[emp_id] == 3 || $rst_pro[emp_id] == 4) && $data[1] == 1) {
//                                    $bod = '10';
//                                } else {
                                $bod = $data[1];
//                                }
                            }
                            $dat2 = Array(
                                $dt[0],
                                $data[23],
                                $cli_id,
                                $bod,
                                $num_nota,
                                $data[4],
                                $dt[3],
                                $dt[16],
                                $data[27]
                            );
                            if ($data[23] != '1') {
                                if ($Clase_nota_Credito->insert_movimiento($dat2) == FALSE) {
                                    $sms = pg_last_error() . 'insert_mov,' . $pro;
                                    $aud = 1;
                                }
                            }
                        } else {
                            $sms = 'Insert_det' . pg_last_error();
                            $aud = 1;
                        }
                        $n++;
                    }
                } else {
                    $sms = 'Insert' . pg_last_error();
                    $accion = 'Insertar';
                    $aud = 1;
                }
            }
            if ($aud == 0) {
                $cheques = Array($data[0],
                    'NOTA DE CREDITO',
                    '',
                    $data[2],
                    $data[4],
                    $data[4],
                    $data[21],
                    '0',
                    '',
                    '3',
                    '',
                    '0',
                    $nrc_id,
                    '0');
                if ($Clase_nota_Credito->insert_cheques($cheques) == false) {
                    $sms = 'Insert_cheques' . pg_last_error();
                    $aud = 1;
                }
            }

            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    if ($n == 0) {
                        $f = $f . "NUM_COMPROBANTE=$comprobante&";
                    } else {
                        $f = $f . strtoupper($fields[$n] . '&');
                    }
                    $n++;
                }
                $modulo = 'NOTA DE CREDITO';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $comprobante) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
            }
        } else {

            $cli_id = $data[0];
            $nrc_id = $id;
            $num_nota = str_replace('-', '', $data[2]);
            $dat = Array(
                $cli_id,
                $data[1], //emisor
                $data[24], //vendedor
                $data[2], //numero
                strtoupper($data[3]), //motivo
                $data[4], //fecha emision
                strtoupper($data[5]), //nombre
                strtoupper($data[6]), //identificacion
                $data[7], //email
                strtoupper($data[8]), //direccion
                $data[9], //denominacion comp
                $data[10], //numero_comp
                $data[11], //fecha_comp
                $data[12], //iva12
                $data[13], //iva0
                $data[14], //ivaex
                $data[15], //ivano
                $data[16], //desc
                $data[17], //ice
                $data[18], //total iva
                $data[19], //total irbpnr
                $data[20], //telefono
                $data[21], //total valor
                $data[22], //total propina
                $data[25], //fac_id
                $data[23], //trs_id
                $data[26] //subtotal
            );


            if ($Clase_nota_Credito->upd_nota_credito($dat, $id) == FALSE) {
                $sms = pg_last_error() . 'oky1';
                $aud = 1;
            } else {
                if ($Clase_nota_Credito->delete_movimiento($num_nota) == FALSE) {
                    $sms = pg_last_error() . 'oky2';
                    $aud = 1;
                } else {
                    if ($Clase_nota_Credito->delete_det_nota_credito($id) == FALSE) {
                        $sms = pg_last_error() . 'oky3';
                        $aud = 1;
                    } else {
                        $n = 0;
                        while ($n < count($data1)) {
                            $dt = explode('&', $data1[$n]);
                            if ($Clase_nota_Credito->insert_det_nota_credito($dt, $id) == TRUE) {
                                $bod = $data[1];
                                $tab = $dt[16];
                                if ($tab == 0) {
                                    $rst_pro = pg_fetch_array($Clase_nota_Credito->lista_un_producto_industrial_id($dt[0]));
//                                    if (($rst_pro[emp_id] == 3 || $rst_pro[emp_id] == 4) && $data[1] == 1) {
//                                        $bod = '10';
//                                    } else {
                                    $bod = $data[1];
//                                    }
                                }
                                $dat2 = Array(
                                    $dt[0],
                                    $data[23],
                                    $cli_id,
                                    $bod,
                                    $num_nota,
                                    $data[4],
                                    $dt[3],
                                    $dt[16],
                                    $data[27]
                                );
                                if ($data[23] != '1') {
                                    if ($Clase_nota_Credito->insert_movimiento($dat2) == FALSE) {
                                        $sms = pg_last_error() . 'insert_mov,' . $pro;
                                        $aud = 1;
                                    }
                                }
                            } else {
                                $sms = 'Insert_det' . pg_last_error();
                                $aud = 1;
                            }
                            $n++;
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
                $modulo = 'NOTA DE CREDITO';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[2]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }

        echo $sms . '&' . $nrc_id;
        break;
    case 1:
        $cns = $Clase_nota_Credito->lista_nota_credito_completo();
        while ($rst = pg_fetch_array($cns)) {
            if (empty($rst[clave_acceso])) {
                $f = $rst['fecha_emision'];
                $f2 = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
                $cod_doc = "04"; //01= factura, 02=nota de credito tabla 4
                $emis[identificacion] = '1790007871001'; //Noperti
                $ambiente = 2;
                if ($rst[cod_punto_emision] >= 10) {
                    $ems = '0' . $rst[cod_punto_emision];
                } else {
                    $ems = '00' . $rst[cod_punto_emision];
                }

                $sec = $rst[num_secuencial];
                if ($sec >= 0 && $sec < 10) {
                    $tx = "00000000";
                } else if ($sec >= 10 && $sec < 100) {
                    $tx = "0000000";
                } else if ($sec >= 100 && $sec < 1000) {
                    $tx = "000000";
                } else if ($sec >= 1000 && $sec < 10000) {
                    $tx = "00000";
                } else if ($sec >= 10000 && $sec < 100000) {
                    $tx = "0000";
                } else if ($sec >= 100000 && $sec < 1000000) {
                    $tx = "000";
                } else if ($sec >= 1000000 && $sec < 10000000) {
                    $tx = "00";
                } else if ($sec >= 10000000 && $sec < 100000000) {
                    $tx = "0";
                } else if ($sec >= 100000000 && $sec < 1000000000) {
                    $tx = "";
                }
                $secuencial = $tx . $sec;

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
                $Clase_nota_Credito->upd_notcre_clave_acceso($clave, $rst[com_id]);
            }
        }
        break;
    case 2:
        $sms = 0;
        if ($Clase_nota_Credito->upd_notcre_na($_REQUEST[na], $_REQUEST[fh], $id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 3:
        $sms = 0;
        $sec = str_replace('-', '', trim($id));
        if ($Clase_nota_Credito->delete_comprobante_notacredito($id) == false) {
            $sms = pg_last_error() . 'delete1';
        }
        if ($Clase_nota_Credito->delete_det_notacredito($sec) == FALSE) {
            $sms = pg_last_error() . 'delete2';
            $aud = 1;
        }
        if ($Clase_nota_Credito->delete_asientos_notacredito($id) == FALSE) {
            $sms = pg_last_error() . 'delete3';
            $aud = 1;
        }
        if ($Clase_nota_Credito->delete_movimiento_notacredito($sec) == FALSE) {
            $sms = pg_last_error() . 'delete4';
            $aud = 1;
        }
        $modulo = 'NOTA DE CREDITO';
        $accion = 'ELIMINAR';
        if ($Adt->insert_audit_general($modulo, $accion, '', $id) == false) {
            $sms = "Auditoria" . pg_last_error();
        }
        echo $sms;
        break;
    case 4:
        $sms = 0;
        if ($Clase_nota_Credito->upd_estado_notcre($id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
}
?>



















