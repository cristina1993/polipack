<?php

include_once '../Clases/clsSetting.php'; //cambiar clsClase_productos
include_once '../Includes/permisos.php';
include_once '../Clases/clsAuditoria.php';
$Mvpt = new Set();
$file = $_FILES[archivo];
$file[tmp_name];
$name = date('Ymd') . '_invmp.csv';
move_uploaded_file($file[tmp_name], '../formatos/' . $name);

function load_file($file) {
    $Aud = new Auditoria();
    $arch = $file;
    $d = date('Y-m-d');
    $h = date('Y-m-d');
    $Mvpt = new Set();
    $file = fopen($file, "r") or die("Error de Archivo");
    $n = 0;
    while (!feof($file)) {
        $aux = fgets($file);
        if ($n > 0) {
            $pos = strpos($aux, ';');
            if ($pos == true) {
                $row = explode(";", $aux);
            } else {
                $row = explode(",", $aux);
            }
            $rst_cl = pg_fetch_array($Mvpt->lista_un_cliente_cedula($row[0]));
            $cli_id = $rst_cl[cli_id];
            $rst_mp = pg_fetch_array($Mvpt->lista_un_mp_code($row[1]));
            $mp_id = $rst_mp[mp_id];
            $mov_presentacion = $rst_mp[mp_presentacion];

            switch ($row[2]) {
                case 'INGRESO':
                    $trs_id = 2;
                    $dc0 = '000';
                    $tr = 0;
                    break;
                case 'EGRESO':
                    $trs_id = 21;
                    $dc0 = '100';
                    $tr = 1;
                    break;
            }
            $rst_doc = pg_fetch_array($Mvpt->lista_secuencia_transaccion($tr));
            $dc = explode('-', $rst_doc[mov_num_trans]);
            $dc1 = ($dc[1] + 1);

            if ($dc1 >= 0 && $sec < 10) {
                $tx_trs = "000000000";
            } elseif ($dc1 >= 10 && $sec < 100) {
                $tx_trs = "00000000";
            } elseif ($dc1 >= 100 && $sec < 1000) {
                $tx_trs = "0000000";
            } elseif ($dc1 >= 1000 && $sec < 10000) {
                $tx_trs = "000000";
            } elseif ($dc1 >= 10000 && $sec < 100000) {
                $tx_trs = "00000";
            } elseif ($dc1 >= 100000 && $sec < 1000000) {
                $tx_trs = "0000";
            } elseif ($dc1 >= 1000000 && $sec < 10000000) {
                $tx_trs = "000";
            } elseif ($dc1 >= 10000000 && $sec < 100000000) {
                $tx_trs = "00";
            } elseif ($dc1 >= 100000000 && $sec < 1000000000) {
                $tx_trs = "0";
            } elseif ($dc1 >= 1000000000 && $sec < 10000000000) {
                $tx_trs = "";
            }
            $dc2 = $dc0 . '-' . (substr($tx_trs, 0, (10 - strlen($dc1)))) . $dc1;

            $mov_documento = $dc2;
            $mov_fecha_trans = date('Y-m-d');
            $mov_fecha_registro = date('Y-m-d');
            $mov_hora_registro = date('H:i');
            $mov_cantidad = $row[3];
            $mov_peso_unit = $row[4];
            $mov_peso_total = $row[5];
            $data = array(
                $trs_id,
                $mp_id,
                '',
                $mov_documento,
                $mov_fecha_trans,
                $mov_cantidad,
                $mov_presentacion,
                $mov_peso_total,
                $cli_id,
                $mov_peso_unit,
                '',
                ''
            );
            if (strlen(trim($row[1])) > 0) {
                if (!$Mvpt->insert_inv_mp($data)) {
                    $err;
                    echo "$err Linea $n";
                    break;
                }
            }
        }
        $n++;
    }
    $campos = Array('Subir Archvo', 'Subir', $arch);
    if (!$Aud->insert($campos)) {
        $err = pg_last_error();
        echo "<script>
                                   alert('$err Linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_movmp.php?desde=$d&hasta=$h'
                           </script>                        
                        ";
    }
    fclose($file);
    echo "<script>
           parent.document.getElementById('bottomFrame').src = ''
           parent.document.getElementById('contenedor2').rows = '*,0%'
           parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_movmp.php?desde=$d&hasta=$h'
          </script> ";
}

function check_file($file) {
    $d = date('Y-m-d');
    $h = date('Y-m-d');
    $sms = 0;
    $Mvpt = new Set();
    $file = fopen($file, "r") or die("Error de Archivo");
    $n = 0;
    while (!feof($file)) {
        $n++;
        $aux = fgets($file);
        if ($n > 1) {
            $pos = strpos($aux, ';');
            if ($pos == true) {
                $row = explode(";", $aux);
            } else {
                $row = explode(",", $aux);
            }
            $chr = array('*', '=', '/', "'", '"');
            if (strlen(trim($row[0]))) {
                foreach ($chr as $val) {
                    $pos0 = strpos($row[0], $val);
                    $pos1 = strpos($row[1], $val);
                    $pos4 = strpos($row[2], $val);
                    if ($pos0 == true || $pos1 == true || $pos4 == true) {
                        $vl = 1;
                    }
                }
                if ($vl == 1) {
                    $sms = "<script>
                                   alert('No se acepta caracteres especiales linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_movmp.php?desde=$d&hasta=$h'
                           </script>";
                    break;
                } else {
                    $rst_cl = pg_fetch_array($Mvpt->lista_un_cliente_cedula($row[0]));
                    $rst_mp = pg_fetch_array($Mvpt->lista_un_mp_code($row[1]));
                    if (!empty($rst_cl)) {
                        
                    } else {
                        $sms = "<script>
                                   alert('PROVEEDOR $row[0] no existe linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_movmp.php?desde=$d&hasta=$h'
                           </script>";
                        break;
                    }

                    if (!empty($rst_mp)) {
                        
                    } else {
                        $sms = "<script>
                                   alert('MATERIA PRIMA $row[1] no existe linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_movmp.php?desde=$d&hasta=$h'
                           </script>";
                        break;
                    }
                    if ($row[2] == 'INGRESO' || $row[2] == 'EGRESO') {
                        
                    } else {
                        $sms = "<script>
                                   alert('Campo INGRESO/EGRESO es incorrecto  linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_movmp.php?desde=$d&hasta=$h'
                           </script>";
                        break;
                    }
                    if (is_numeric(trim($row[3]))) {
                        
                    } else {
                        $sms = "<script>
                                   alert('El campo CANTIDAD no es un valor numerico linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_movmp.php?desde=$d&hasta=$h'
                           </script>";
                        break;
                    }

                    if (is_numeric(trim($row[4]))) {
                        
                    } else {
                        $sms = "<script>
                                   alert('El campo COSTO/U no es un valor numerico linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_movmp.php?desde=$d&hasta=$h'
                           </script>";
                        break;
                    }

                    if (is_numeric(trim($row[5]))) {
                        
                    } else {
                        $sms = "<script>
                                   alert('El campo COSTO/T no es un valor numerico linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_movmp.php?desde=$d&hasta=$h'
                           </script>";
                        break;
                    }
                }
            }
        }
    }
    fclose($file);
    return $sms;
}

$check = check_file('../formatos/' . $name);

if (strlen($check) == 1) {
    load_file('../formatos/' . $name);
} else {
    echo $check;
}

