<?php

header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: filename=" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

include_once '../Clases/clsClase_asientos.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_asientos();
$desde = $_GET[desde];
$hasta = $_GET[hasta];
$cta = $_GET[cuenta];
$dc=2;
if (!empty($cta)) {
    $txt1 = " and con_concepto_debe='$cta'";
    $txt2 = " and con_concepto_haber='$cta'";
} else {
    $txt1 = "";
    $txt2 = "";
}
$cns = $Set->lista_cuentas_fecha($desde, $hasta, $txt1, $txt2);
$cuentas0 = pg_fetch_all_columns($cns);
$cuentas = array_unique($cuentas0);
$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));
///*******Primer Enc********
echo"
    <table border='1'>
    <thead>
    <tr><th colspan='7'>LIBRO MAYOR - GENERAL</th></tr>
    <tr><th colspan='7'>NOPERTI CIA. LTDA.</th></tr>
    <tr><th colspan='7'>RUC: 1790007871001</th></tr>
    <tr><th colspan='7'>PERIODO " . $desde . " AL " . $hasta . "</th></tr>
    </thead>
    ";

$n = 0;
while ($n < count($cuentas)) {
    $rst_cuenta = pg_fetch_array($Set->lista_un_plan_cuenta($cuentas[$n]));
    echo"
        <tr>
        <td colspan='7'>CODIGO: " . $cuentas[$n] . "</td>
        </tr>
        <tr>
        <td colspan='7'>CUENTA: " . strtoupper($rst_cuenta[pln_descripcion]) . "</td>
        </tr>
        <tr>
        <td>F. EMISION</td>
        <td>ASIENTO No</td>
        <td>TIPO</td>
        <td>CONCEPTO</td>
        <td>DEBE</td>
        <td>HABER</td>
        <td>SALDO</td>
        </tr>
        ";

    $cns_as = $Set->lista_asientos_cuenta_fecha($cuentas[$n], $desde, $hasta);
    while ($rst_as = pg_fetch_array($cns_as)) {
        $rst_cuenta = pg_fetch_array($Set->lista_un_plan_cuenta($cuenta));

        if ($rst_as[tipo] == 'Debe') {
            $debe = $rst_as[con_valor_debe];
            $haber = '';
        } else {
            $debe = '';
            $haber = $rst_as[con_valor_haber];
        }
        echo"
            <tr>
            <td>" . $rst_as[con_fecha_emision] . "</td>
            <td>" . $rst_as[con_asiento] . "</td>
            <td>" . $rst_as[con_tipo] . "</td>
            <td>" . $rst_as[con_concepto] . "</td>
            <td>" . number_format($debe, $dc) . "</td>
            ";
        $valor_d = $debe;
        $total_d = $total_d + $valor_d;
        echo"
            <td>" . number_format($haber, $dc) . "</td>
            ";
        $valor_h = $haber;
        $total_h = $total_h + $valor_h;
        $total_v = $total_d - $valor_h;

        //----------------------------
        if ($rst_as[tipo] == 'Debe') {
            $total_vv = $total_d - $valor_h;
        }
        if ($rst_as[tipo] == 'Haber') {
            $total_vv = $valor_d - $valor_h;
        }
        echo"
            <td>" . number_format($total_vv, $dc) . "</td>
            ";
    }
    $tot = pg_fetch_array($Set->suma_totales_dh($cuentas[$n], $desde, $hasta));
    echo"
        <tr>
        <td colspan='3'></td>
        <td>TOTAL</td>
        <td>" . number_format($tot[debe], $dc) . "</td>
        <td>" . number_format($tot[haber], $dc) . "</td>
        ";
    $total_may = $tot[debe] - $tot[haber];
    echo"
        <td>" . number_format($total_may, $dc) . "</td>
        </tr>
        ";
    $n++;
}
echo"
    </table>
    ";
?>