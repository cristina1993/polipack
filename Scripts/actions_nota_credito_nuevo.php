<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_notacredito_nuevo.php';
$Clase_nota_Credito_nuevo = new Clase_nota_Credito_nuevo();
$Adt = new Auditoria();
$act = $_REQUEST[act]; //Accion
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$id = $_REQUEST[id];
$tbl = $_REQUEST[tbl]; //tbl
$s = $_REQUEST[s]; //tbl
$x = $_REQUEST[x];
$fields = $_REQUEST[fields];
switch ($act) {
    case 0:
        $aud = 0;
        $sms = 0;
        if (empty($id)) {
            if (empty($data[0])) {
                if (strlen($data[6]) < 11) {
                    $tipo = 'CN';
                } else {
                    $tipo = 'CJ';
                }
                $rst_cod = pg_fetch_array($Clase_nota_Credito_nuevo->lista_secuencial_cliente($tipo));
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
                    strtoupper($data[6]),
                    strtoupper($data[8]),
                    $retorno,
                    strtoupper($data[20]),
                    $data[7]
                );
                if ($Clase_nota_Credito_nuevo->insert_cliente($da) == false) {
                    $sms = 'Insert_cli' . pg_last_error();
                }
                $rstcli = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_cliente_cedula($data[6]));
                $cli_id = $rstcli[cli_id];
            } else {
                $cli_id = $data[0];
            }

            if ($data[1] >= 10) {
                $ems = '0' . $data[1];
            } else {
                $ems = '00' . $data[1];
            }
            $rst_sec = pg_fetch_array($Clase_nota_Credito_nuevo->lista_secuencial_nota_credito($ems));
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
            if ($v == 0) {
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
                $rst_ulti_sec = pg_fetch_array($Clase_nota_Credito_nuevo->lista_secuencial_locales($data[1]));
                if ($data[2] == $rst_ulti_sec[secuencial]) {
                    $sms = 1;
                    $aud = 1;
                } else {
                    if ($Clase_nota_Credito_nuevo->insert_nota_credito($dat) == TRUE) {
                        $nc = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_notac_num($comprobante));
                        $nrc_id = $nc[ncr_id];
                        $n = 0;
                        while ($n < count($data1)) {
                            $dt = explode('&', $data1[$n]);
                            $num_nota = str_replace('-', '', $comprobante);
                            if ($Clase_nota_Credito_nuevo->insert_det_nota_credito($dt, $nrc_id) == TRUE) {
                                $bod = $data[1];
                                $tab = $dt[16];
                                if ($tab == 0) {
                                    $rst_pro = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_producto_industrial_id($dt[0]));
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
                                    if ($Clase_nota_Credito_nuevo->insert_movimiento($dat2) == FALSE) {
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
        echo $sms . '&' . $nrc_id . '&';
        break;
    case 1:
        if ($s == 0) {
            $cns = $Clase_nota_Credito_nuevo->lista_clientes_search(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[cli_raz_social];
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli . '&&';
        } else {
            $sms;
            $rst = pg_fetch_array($Clase_nota_Credito_nuevo->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_id];
            }
            echo $sms;
        }
        break;


    case 2:
        if (strlen($_REQUEST[lt]) >= 8) {
            $tabla = 1;
            $rst = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_producto_noperti_cod_lote($id, $_REQUEST[lt]));
            $rst_precio1 = pg_fetch_array($Clase_nota_Credito_nuevo->lista_precio_producto($rst[id], $tabla));
//            if ($rst_precio1[pre_vald_precio1] == 1) {
//                $rst_precio1[pre_precio] = $rst_precio1[pre_precio];
//            } else {
//                $rst_precio1[pre_precio] = $rst_precio1[pre_precio2];
//            }
//            $rst_desc = pg_fetch_array($Clase_nota_Credito_nuevo->lista_descuento_producto($rst_precio1[pre_id], $em));
            echo $rst[pro_a] . '&' . $rst[pro_b] . '&' . $rst[pro_uni] . '&' . $rst_precio1[pre_precio] . '&' . $rst_precio1[pre_iva] . '&' . $rst_desc[dsc_descuento] . '&' . $rst_precio1[pre_ice] . '&' . $rst[pro_ad] . '&' . $rst[pro_ac] . '&' . $rst[id] . '&1';
        } else {
            $tbl = substr($id, 0, 1);
            $id = substr($id, 1, (strlen($id) - 1));
            if ($tbl == 1) {
                $rst = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_producto_noperti_id($id));
                if ($rst[id] != '') {
                    $rst_precio1 = pg_fetch_array($Clase_nota_Credito_nuevo->lista_precio_producto($rst[id], $tbl));
//                    if ($rst_precio1[pre_vald_precio1] == 1) {
//                        $rst_precio1[pre_precio] = $rst_precio1[pre_precio];
//                    } else {
//                        $rst_precio1[pre_precio] = $rst_precio1[pre_precio2];
//                    }
//                    $rst_desc = pg_fetch_array($Clase_nota_Credito_nuevo->lista_descuento_producto($rst_precio1[pre_id], $em));
                    echo $rst[pro_a] . '&' . $rst[pro_b] . '&' . $rst[pro_uni] . '&' . $rst_precio1[pre_precio] . '&' . $rst_precio1[pre_iva] . '&' . $rst_desc[dsc_descuento] . '&' . $rst_precio1[pre_ice] . '&' . $rst[pro_ad] . '&' . $rst[pro_ac] . '&' . $rst[id] . '&1';
                }
            } else {
                $rst = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_producto_industrial_id($id));
                if ($rst[pro_id] != '') {
                    $rst_precio = pg_fetch_array($Clase_nota_Credito_nuevo->lista_precio_producto($rst[pro_id], $tbl));
//                    if ($rst_precio[pre_vald_precio1] == 1) {
//                        $rst_precio[pre_precio] = $rst_precio[pre_precio];
//                    } else {
//                        $rst_precio[pre_precio] = $rst_precio[pre_precio2];
//                    }
//                    $rst_desc = pg_fetch_array($Clase_nota_Credito_nuevo->lista_descuento_producto($rst_precio[pre_id], $em));
                    echo $rst[pro_codigo] . '&' . $rst[pro_descripcion] . '&' . $rst[pro_uni] . '&' . $rst_precio[pre_precio] . '&' . $rst_precio[pre_iva] . '&' . $rst_desc[dsc_descuento] . '&' . $rst_precio[pre_ice] . '& & &' . $rst[pro_id] . '&0';
                }
            }
        }
        break;
}
?>



















