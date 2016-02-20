<?php

$tip = $_GET[tipo];
if ($tip == '1') {
    $nm = 'kardex_pt' . date('Ymd') . '.xls';
} else if ($tip == '2') {
    $nm = 'kardex_mp' . date('Ymd') . '.xls';
} else if ($tip == '3') {
    $nm = 'prec_pt' . date('Ymd') . '.xls';
} else if ($tip == '4') {
    $nm = 'inv_general' . date('Ymd') . '.xls';
} else if ($tip == '5') {
    $nm = 'costos_producto' . date('Ymd') . '.xls';
} else {
    $nm = 'ventas_producto' . date('Ymd') . '.xls';
}
header('Content-Type: application/force-download');
header('Content-disposition: attachment; filename=' . $nm);
// Fix for crappy IE bug in download.  
header("Pragma: ");
header("Cache-Control: ");
echo utf8_decode($_REQUEST['datatodisplay']);
?>