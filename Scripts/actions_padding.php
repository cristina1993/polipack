<?php

//$_SESSION[User]='PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_ordenes_padding.php';
$Clase = new Clase_Orden_Padding();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$nom = $_REQUEST[nom];
$fields = $_REQUEST[fields]; //Datos para auditoria
$Adt = new Auditoria();
switch ($op) {
    case 0:
        if (empty($id)) {
            if ($Clase->insert($data) == true) {
                $sms = 0;

                $rst_sec = pg_fetch_array($Clase->lista_ped_sec());
                $sec = ($rst_sec[ped_orden] + 1);
                if ($sec >= 0 && $sec < 10) {
                    $tx_trs = "00000";
                } elseif ($sec >= 10 && $sec < 100) {
                    $tx_trs = "0000";
                } elseif ($sec >= 100 && $sec < 1000) {
                    $tx_trs = "000";
                } elseif ($sec >= 1000 && $sec < 10000) {
                    $tx_trs = "00";
                } elseif ($sec >= 10000 && $sec < 100000) {
                    $tx_trs = "0";
                } elseif ($sec >= 100000 && $sec < 1000000) {
                    $tx_trs = "";
                }
                $code = $tx_trs . $sec;
                $n = 0;
                $j = 11;
                $i = 19;
                while ($n < 4) {
                    $n++;
                    $j++;
                    $i++;
                    $ord_mp = array(
                        $code,
                        $data[5],
                        '4',
                        $data[$j],
                        $data[$i],
                        $data[$i],
                        $data[0]
                    );
                    if ($data[$j] != 0) {
                        if ($Clase->insert_pmp($ord_mp) == false) {
                            $sms = 'insert pedido mp' . pg_last_error();
                        }
                    }
                }

                if ($Clase->lista_cambia_status_det($data[28], '9') == false) {
                    $sms = 'Cambio_estado_ped' . pg_last_error();
                }

                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'ORDEN PADDING';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            } else {
                $sms = pg_last_error();
            }
        } else {
            if ($Clase->upd($data, $id) == true) {
                $sms = 0;
                $n = 0;
                if ($Clase->del_pmp_orden($data[0]) == false) {
                    $sms = 'del_pmp' . pg_last_error();
                } else {
                    $rst_sec = pg_fetch_array($Clase->lista_ped_sec());
                    $sec = ($rst_sec[ped_orden] + 1);
                    if ($sec >= 0 && $sec < 10) {
                        $tx_trs = "00000";
                    } elseif ($sec >= 10 && $sec < 100) {
                        $tx_trs = "0000";
                    } elseif ($sec >= 100 && $sec < 1000) {
                        $tx_trs = "000";
                    } elseif ($sec >= 1000 && $sec < 10000) {
                        $tx_trs = "00";
                    } elseif ($sec >= 10000 && $sec < 100000) {
                        $tx_trs = "0";
                    } elseif ($sec >= 100000 && $sec < 1000000) {
                        $tx_trs = "";
                    }
                    $code = $tx_trs . $sec;
                    $n = 0;
                    $j = 11;
                    $i = 19;
                    while ($n < 4) {
                        $n++;
                        $j++;
                        $i++;
                        $ord_mp = array(
                            $code,
                            $data[5],
                            '4',
                            $data[$j],
                            $data[$i],
                            $data[$i],
                            $data[0]
                        );
                        if ($data[$j] != 0) {
                            if ($Clase->insert_pmp($ord_mp) == false) {
                                $sms = 'insert pedido mp' . pg_last_error();
                            }
                        }
                    }
                }

                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'ORDEN PADDING';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            } else {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase->del_pmp_orden($data) == false) {
            $sms = 'del_pmp' . pg_last_error();
        } else {
            if ($Clase->delete($id) == true) {
                $sms = 0;

                $n = 0;
                $modulo = 'ORDEN PADDING';
                $accion = 'ELIMINAR';
                if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            } else {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 2:

        $rst = pg_fetch_array($Clase->lista_secuencial(4));
        $rst1 = pg_fetch_array($Clase->lista_siglas($id));
        $sec = (substr($rst[opp_codigo], -5) + 1);
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
        $rst1 = pg_fetch_array($Clase->lista_siglas($id));
        $retorno = $rst1[emp_sigla] . "-" . $txt . $sec;
        echo $retorno;
        break;
    case 3:

        $rst = pg_fetch_array($Clase->lista_mostrar($id));
        $ancho = $rst[pro_ancho];
        $largo = $rst[pro_largo];
        $peso = $rst[pro_peso];
        $gramaje = $rst[pro_gramaje];
        $mf1 = $rst[pro_mf1];
        $mf2 = $rst[pro_mf2];
        $mf3 = $rst[pro_mf3];
        $mf4 = $rst[pro_mf4];
        $mp1 = $rst[pro_mp1];
        $mp2 = $rst[pro_mp2];
        $mp3 = $rst[pro_mp3];
        $mp4 = $rst[pro_mp4];
        $rst1 = pg_fetch_array($Clase->lista_capturar($id));
        $velocidad = $rst1[opp_velocidad];
        $rodillosup = $rst1[opp_temp_rodillosup];
        $rodilloinf = $rst1[opp_temp_rodilloinf];
        $observaciones = $rst1[opp_observaciones];


        $cns = $Clase->lista_combomp($id);
        $combo = "<option value='0'>Seleccione</option>";
        while ($rst = pg_fetch_array($cns)) {
            $combo.="<option value='$rst[mp_id]'>$rst[mp_referencia]</option>";
        }
        echo $ancho . '&' . $largo . '&' . $peso . '&' . $gramaje . '&' . $mf1 . '&' . $mf2 . '&' . $mf3 . '&' . $mf4 . '&' . $mp1 . '&' . $mp2 . '&' . $mp3 . '&' . $mp4 . '&' . $velocidad . '&' . $rodillosup . '&' . $rodilloinf . '&' . $observaciones . '&' . $combo;
        break;
}
?>
