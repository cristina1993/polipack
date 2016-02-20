<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_guia_remision.php';
include_once("../Clases/clsAuditoria.php");
$Clase_guia_remision = new Clase_guia_remision();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
$x = $_REQUEST[x];
$s = $_REQUEST[s];
switch ($op) {
    case 0:
        $sms = 0;
        $aud = 0;
        if (empty($id)) {
            $n = 0;
            if ($data[1] >= 10) {
                $ems = '0' . $data[1];
            } else {
                $ems = '00' . $data[1];
            }
            $rst = pg_fetch_array($Clase_guia_remision->lista_secuencial_documento($data[1]));
            if (empty($rst)) {
                $sec = 1;
            } else {
                $dat = explode('-', $rst[gui_numero]);
                $sec = $dat[2] + 1;
            }
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

            $num = $ems . '-001-' . $txt . $sec;
            if ($data[18] == 0) {
                $trans = Array(
                    strtoupper($data[12]),
                    strtoupper($data[22]),
                    strtoupper($data[23]));
                if ($Clase_guia_remision->insert_transportista($trans) == FALSE) {
                    $sms = pg_last_error();
                    $aud = 1;
                } else {
                    $rst_tra = pg_fetch_array($Clase_guia_remision->lista_un_transportista(strtoupper($data[12])));
                    $tra = $rst_tra[id];
                }
            } else {
                $tra = $data[18];
            }
            $rst_ulti_sec = pg_fetch_array($Clase_guia_remision->lista_secuencial_locales($data[1]));
            if ($data[3] == $rst_ulti_sec[secuencial]) {
                $sms = 1;
                $aud = 1;
            } else {
                if ($Clase_guia_remision->insert_guia_remision($data, $data[3], $tra) == TRUE) {
                    $gr = pg_fetch_array($Clase_guia_remision->lista_una_guia($num));
                    $gui_id = $gr[gui_id];
                    $n = 0;
                    while ($n < count($data2)) {
                        $dt = explode('&', $data2[$n]);
                        if ($Clase_guia_remision->insert_det_guia_remision($dt, $gui_id) == false) {
                            $sms = 'Insert_det' . pg_last_error();
                            $aud = 1;
                        }
                        $n++;
                    }
                } else {
                    $sms = 'Insert' . pg_last_error();
                    $aud = 1;
                }
            }

            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'GUIA REMISION';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $num) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                    $aud = 1;
                }
            }
        } else {
            if ($data[18] == '0') {
                $trans = Array(
                    strtoupper($data[12]),
                    strtoupper($data[22]),
                    strtoupper($data[23]));
                if ($Clase_guia_remision->insert_transportista($trans) == FALSE) {
                    $sms = pg_last_error();
                    $aud = 1;
                }
                $rst_tra = pg_fetch_array($$Clase_guia_remision->lista_un_transportista(strtoupper($data[12])));
                $tra = $rst_tra[id];
            } else {
                $tra = $data[18];
            }

            if ($Clase_guia_remision->update_guia_remision($data, $id, $tra == true)) {
                if ($Clase_guia_remision->delete_det_guia($id) == FALSE) {
                    $sms = pg_last_error();
                    $aud = 1;
                } else {
                    $n = 0;
                    while ($n < count($data2)) {
                        $dt = explode('&', $data2[$n]);
                        if ($Clase_guia_remision->insert_det_guia_remision($dt, $id) == false) {
                            $sms = 'Insert_det' . pg_last_error();
                            $aud = 1;
                        }
                        $n++;
                    }
                }
            } else {
                $sms = "Update" . pg_last_error() . 'ok2';
            }


            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'GUIA REMISION';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[3]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_guia_remision->delete_guia_remision($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'GUIA REMISION';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $id) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;


    case 5:

        if ($s == 0) {
            $cns = $Clase_guia_remision->lista_buscar_transportistas(strtoupper($id));
            $cli = '';
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = trim($rst[razon_social]);
                $cli .= "<tr><td><input type='button' value='&#8730;' onclick=" . "load_transportista2('$rst[identificacion]')" . " /></td><td>$n</td><td>$rst[identificacion]</td><td>$nm</td></tr>";
            }
            echo $cli . '&&';
        } else if ($s == 1) {
            $sms;
            $rst = pg_fetch_array($Clase_guia_remision->lista_un_transportista($id));
            if (!empty($rst)) {
                $sms = $rst[identificacion] . '&' . trim($rst[razon_social]) . '&' . $rst[placa] . '&' . $rst[id];
            }
            echo $sms;
        }
        break;

    case 6:
        $cns = $Clase_guia_remision->lista_guias();
        while ($rst = pg_fetch_array($cns)) {
            if (empty($rst[clave_acceso])) {
                $f = $rst['fecha_emision'];
                $f2 = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
                $cod_doc = "06"; //01= factura, 02=nota de credito tabla 4
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
                $Clase_guia_remision->upd_guia_clave_acceso($clave, $rst[num_comprobante]);
                echo $clave;
            }
        }
        break;
    case 7:
        $sms = 0;
        if ($Clase_guia_remision->upd_guia_na($_REQUEST[na], $_REQUEST[fh], $id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
}
?>
