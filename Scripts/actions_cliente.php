<?php

//$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cliente.php';
include_once("../Clases/clsAuditoria.php");
$Clase_cliente = new Clase_cliente();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$id = $_REQUEST[id];
$l1 = $_REQUEST[l1];
$l2 = $_REQUEST[l2];
$nom = $_REQUEST[nom];
$fields = $_REQUEST[fields];
$Adt = new Auditoria();
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            $n = 0;

            $str = $data;
            foreach ($str as $row => $cliente) {
                $str[$n] = strtoupper($cliente);
                $n++;
            }

//            print_r($str);
            if ($Clase_cliente->insert_cliente($str) == false) {
                $sms = pg_last_error();
            } else {

                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'CLIENTE';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[8]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }

                $n = 0;
                $rst_reg = pg_fetch_array($Clase_cliente->lista_ultimo_cliente());
                while ($n < count($data1)) {
                    $dt = explode('&', $data1[$n]);
                    $dat = Array($rst_reg[cli_id],
                        $dt[0],
                        $dt[1],
                        $dt[2],
                        $dt[3],
                        $dt[4],
                        $dt[5],
                        $dt[6],
                        $dt[7],
                        $dt[8],
                        $dt[9],
                        $dt[10],
                        $dt[11]);
                    if ($dt[0] != 'undefined') {
                        if ($dt[0] != 'undefined') {
                            if ($Clase_cliente->insert_dir_entrega($dat) == FALSE) {
                                $sms = pg_last_error();
                            }
                        }
                    }
                    $n++;
                }
            }
        } else {
            $rst_cup = pg_fetch_array($Clase_cliente->lista_un_cliente($id));
            $aux_cupo = $rst_cup[cli_cup_maximo];
            $aux_categoria = $rst_cup[cli_cat_cliente];
            $rst_std = pg_fetch_array($Clase_cliente->lista_estado_cliente($id));

            if ($data[4] == 1) {
                $std = 6;
                if ($Clase_cliente->upd_estado_pedido($std, $id) == false) {
                    $sms = "upd_std_pedido" . pg_last_error();
                } else {
                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'CLIENTES';
                    $accion = 'ELIMINAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $data[8]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }


            if ($Clase_cliente->upd_cliente($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'CLIENTES';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[8]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }

                if ($aux_cupo != $data[12] || $aux_categoria != $data[13]) {
                    $cod = $data[3];
                    if ($aux_cupo != $data[12]) {
                        $txt = 'cli_cup_maximo';
                        $cambio = $data[12];
                    } else {
                        $txt = 'cli_cat_cliente';
                        $cambio = $data[13];
                    }

                    if ($Clase_cliente->insert_aprobacion($cod, 0, $cambio, $txt) == false) {
                        $sms = pg_last_error();
                    }
                }

                if ($Clase_cliente->delete_direccion_entrega($id) == false) {
                    $sms = pg_last_error();
                } else {
                    $n = 0;
                    while ($n < count($data1)) {
                        $dt = explode('&', $data1[$n]);
                        $dat = Array($id,
                            $dt[0],
                            $dt[1],
                            $dt[2],
                            $dt[3],
                            $dt[4],
                            $dt[5],
                            $dt[6],
                            $dt[7],
                            $dt[8],
                            $dt[9],
                            $dt[10],
                            $dt[11]);

                        if ($dt[0] != 'undefined') {
                            if ($dt[0] != '') {
                                if ($Clase_cliente->insert_dir_entrega($dat) == FALSE) {
                                    $sms = pg_last_error();
                                }
                            }
                        }
                        $n++;
                    }
                }
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($Clase_cliente->delete_direccion_entrega($id) == false) {
            $sms = pg_last_error();
        } else {

            $rst_cli = pg_fetch_array($Clase_cliente->lista_un_cliente($id));
            if ($Clase_cliente->delete_aprobacion($rst_cli[cli_codigo]) == false) {
                $sms = pg_last_error();
            } else {

                if ($Clase_cliente->delete_cliente($id) == false) {
                    $sms = pg_last_error();
                } else {
                    $n = 0;
                    $f = $nom;
                    $modulo = 'CLIENTES';
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
        $rst = pg_fetch_array($Clase_cliente->lista_secuencial_cliente($l1 . $l2));
        if ($l1 == 'CP') {
            $sec = (substr($rst[cli_codigo], 3, 8) + 1);
        } else {
            $sec = (substr($rst[cli_codigo], 2, 8) + 1);
        }
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
        if ($l1 == '0') {
            $retorno = '';
        } else {
            $retorno = $l1 . $l2 . $txt . $sec;
        }

        echo $retorno;
//        echo $sec;
        break;
    case 3:
        $sms = 0;
        $rst = pg_fetch_array($Clase_cliente->lista_una_ced_ruc($id));
        $ced_ruc = $rst[cli_ced_ruc];
        if (!empty($ced_ruc)) {
            $sms = 1;
        }
        echo $sms;
        break;
}
?>
