<?php

include_once '../Clases/clsClase_industrial_movimientopt.php'; //cambiar clsClase_productos
include_once '../Includes/permisos.php';
include_once '../Clases/clsAuditoria.php';
$Mvpt = new Clase_industrial_movimientopt();
$file = $_FILES[archivo];
$file[tmp_name];
$name = date('Ymd') . '_' . $emisor . '.csv';
move_uploaded_file($file[tmp_name], '../formatos/' . $name);

function load_file($file, $emisor, $id_cli) {
    $Aud = new Auditoria();
    $arch = $file;
    $d = date('Y-m-d');
    $h = date('Y-m-d');
    $Mvpt = new Clase_industrial_movimientopt();
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
            if (trim($row[5]) == 'INDUSTRIAL') {
                $rst_prod = pg_fetch_array($Mvpt->lista_un_prod_industriales_cod($row[0]));
                $pro_id = $rst_prod[pro_id];
                $tbl = 0;
            } else {
                $rst_prod = pg_fetch_array($Mvpt->lista_un_prod_comercial_codlote($row[0], $row[1]));
                $pro_id = $rst_prod[id];
                $tbl = 1;
            }
            switch ($row[2]) {
                case 'INGRESO':
                    $trs_id = 2;
                    $dc0 = '001';
                    break;
                case 'EGRESO':
                    $trs_id = 21;
                    $dc0 = '000';
                    break;
            }
            $txdc = '0000000000';
            $rst_doc = pg_fetch_array($Mvpt->lista_ultimo_secuencial_tp($dc0));
            $dc = explode('-', $rst_doc[mov_documento]);
            $dc1 = ($dc[1] + 1);
            $dc2 = $dc0 . '-' . (substr($txdc, 0, (10 - strlen($dc1)))) . $dc1;

            $cli_id = $id_cli;
            $bod_id = $emisor;
            $mov_documento = $dc2;
            $mov_fecha_trans = date('Y-m-d');
            $mov_fecha_registro = date('Y-m-d');
            $mov_hora_registro = date('H:i');
            $mov_cantidad = $row[3];
            $mov_tranportista = $row[4];
            $mov_tabla = $tbl;

            $data = array(
                $pro_id,
                $trs_id,
                $cli_id,
                $bod_id,
                $mov_documento,
                $mov_fecha_trans,
                $mov_fecha_registro,
                $mov_hora_registro,
                $mov_cantidad,
                $mov_tranportista,
                $mov_tabla);
            if (strlen(trim($row[0])) > 0) {
                if (!$Mvpt->insert_movimiento_pt($data)) {
                    $err;
                    echo "<script>
                                   alert('$err Linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php?fecha1=$d &fecha2=$h '
                           </script>                        
                        ";
                    break;
                }
            }
        }
        $n++;
    }
    $campos = Array('Subir Archvo', 'Subir', $arch);
    if (!$Aud->insert($campos)) {
        $err = pg_last_error();
        echo "<script>alert($err)</script>";
    }
    fclose($file);
    echo "<script>
           parent.document.getElementById('bottomFrame').src = ''
           parent.document.getElementById('contenedor2').rows = '*,0%'
           parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php?fecha1=$d &fecha2=$h '
          </script> ";
}

function check_file($file) {
    $d = date('Y-m-d');
    $h = date('Y-m-d');
    $sms = 0;
    $Mvpt = new Clase_industrial_movimientopt();
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
                    $pos4 = strpos($row[4], $val);
                    if ($pos0 == true || $pos1 == true || $pos4 == true) {
                        $vl = 1;
                    }
                }
                if ($vl == 1) {
                    $sms = "<script>
                                   alert('No se acepta caracteres especiales linea $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php?fecha1=$d&fecha2=$h'
                           </script>                        
                        
                        ";
                    break;
                } else {
                    if (trim($row[5]) == 'COMERCIAL' && strlen(trim($row[1])) < 5) {
                        $sms = "<script>
                                   alert('Lote incorrecto para productos COMERCIALES linea $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php?fecha1=$d&fecha2=$h'
                                </script>                            
                            ";
                        break;
                    }
                    if ($row[2] == 'INGRESO' || $row[2] == 'EGRESO') {
                        
                    } else {
                        $sms = "<script>
                                   alert('Campo INGRESO/EGRESO es incorrecto  linea $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php?fecha1=$d&fecha2=$h'
                                </script>";
                        break;
                    }
                    if (is_numeric($row[3])) {
                        
                    } else {
                        $sms = "<script>
                                   alert('El campo cantidad no es un valor numerico linea $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php?fecha1=$d&fecha2=$h'
                                </script>";
                        break;
                    }
                    if (trim($row[5]) == 'COMERCIAL' || trim($row[5]) == 'INDUSTRIAL') {
                        if (trim($row[5]) == 'INDUSTRIAL') {
                            $rst_prod = pg_fetch_array($Mvpt->lista_un_prod_industriales_cod($row[0]));
                        } elseif (trim($row[5]) == 'COMERCIAL') {
                            $rst_prod = pg_fetch_array($Mvpt->lista_un_prod_comercial_codlote($row[0], $row[1]));
                        }
                        if (empty($rst_prod)) {
                            $sms = 'Producto ' . $row[5] . ' no existe favor registrarlo  linea  ' . $n;
                            break;
                        }
                    } else {
                        $sms = "<script>
                                   alert('Campo COMERCIAL/INDUSTRIAL es incorrecto  linea')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php?fecha1=$d&fecha2=$h'
                                </script>                            
                             ";
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
    load_file('../formatos/' . $name, $emisor, $id_cli);
} else {
    echo $check;
}

