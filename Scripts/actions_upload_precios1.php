<?php

include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_preciospt.php';
include_once '../Clases/clsAuditoria.php';
$Pre = new Clase_preciospt();
$file = $_FILES[archivo];
$file[tmp_name];
$name = date('Ymd') . '_prec.csv';
move_uploaded_file($file[tmp_name], '../formatos/' . $name);
$emisor = 2;

function load_file($file) {
    $Aud = new Auditoria();
    $arch = $file;
    $d = date('Y-m-d');
    $h = date('Y-m-d');
    $Pre = new Clase_preciospt();
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
            if (trim($row[0]) == 'INDUSTRIAL') {
                $rst_prod = pg_fetch_array($Pre->lista_un_producto_industrial($row[1]));
                $pro_id = $rst_prod[pro_id];
                $tbl = 0;
            } else {
                $rst_prod = pg_fetch_array($Pre->lista_un_producto_noperti_cod_lote(trim($row[1]), trim($row[2])));
                $pro_id = $rst_prod[id];
                $tbl = 1;
            }

            $rst_pre = pg_fetch_array($Pre->lista_precios_proid_tabla($pro_id, $tbl));
            $pre_id = $rst_pre[pre_id];
            $pre_precio = $row[3];
            $pre_iva = trim($row[5]);
            $pre_precio2 = $row[4];
            $data = array(
                $pre_precio,
                $pre_iva,
                $pre_precio2
            );


            if (strlen(trim($row[0])) > 0) {
                if (!$Pre->upd_precios($data, $pre_id)) {
                    $err;
                    echo "<script>
                                   alert('$err Linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_preciospt.php'
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
           parent.document.getElementById('mainFrame').src = '../Scripts/Lista_preciospt.php'
          </script> ";
}

function check_file($file) {
    $d = date('Y-m-d');
    $h = date('Y-m-d');
    $Pre = new Clase_preciospt();
    $sms = 0;
    $op = 0;
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
                    if ($pos0 == true || $pos1 == true) {
                        $vl = 1;
                    }
                }
                if ($vl == 1) {
                    $sms = "<script>
                                   alert('No se acepta caracteres especiales linea $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_preciospt.php'
                           </script>                        
                        
                        ";
                    break;
                } else {
                    if (trim($row[0]) == 'INDUSTRIAL') {
                        $rst_prod = pg_fetch_array($Pre->lista_un_producto_industrial($row[1]));
                        $producto = $row[1];
                        $lote = 0;
                    } else {
                        if (trim($row[2]) == '') {
                            $lote = 1;
                        } else {
                            $lote = 0;
                        }
                        $rst_prod = pg_fetch_array($Pre->lista_un_producto_noperti_cod_lote(trim($row[1]), trim($row[2])));
                        $producto = $row[1] . ' ' . $row[2];
                    }
                    if ($lote != 0) {
                        $sms = "<script>
                                   alert('Ingrese lote en linea $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_preciospt.php'
                           </script>                        
                        
                        ";
                        break;
                    }

                    if (empty($rst_prod)) {
                        $sms = "<script>
                                   alert('Producto ' . $producto . ' no existe favor registrarlo linea $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_preciospt.php'
                           </script>                        
                        
                        ";
                        break;
                    }

                    if (is_numeric($row[3])) {
                        
                    } else {
                        $sms = "<script>
                                   alert('El campo Precio no es un valor numerico linea  $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_preciospt.php'
                           </script>                        
                        
                        ";
                        break;
                    }
                    if (is_numeric($row[4])) {
                        
                    } else {
                        $sms = "<script>
                                   alert('El campo Descuento no es un valor numerico linea  $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_preciospt.php'
                           </script>                        
                        
                        ";
                        break;
                    }

                    if (trim($row[5]) != '12' || trim($row[5]) != '0' || trim($row[5]) != 'EX' || trim($row[5]) != 'NO') {
                        
                    } else {
                        $sms = "<script>
                                   alert('El campo Iva no corresponde a los indicados 12, 0, EX o NO  linea  $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_preciospt.php'
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
    load_file('../formatos/' . $name);
} else {
    echo $check;
}
?>