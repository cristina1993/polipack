<?php

header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: filename=" . date('Y-m-d'). ".xls");
header("Pragma: no-cache");
header("Expires: 0");

include_once '../Clases/clsClase_asientos.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_asientos();
$desde = $_GET[desde];
$hasta = $_GET[hasta];
$niv = $_GET[nivel];
$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));
$dc=2;
///*******Primer Enc********
echo "
    <table border='1'>
    <thead>
    <tr><th colspan='6'>BALANCE DE COMPROBACION</th></tr>
    </thead>
    <tr><td colspan='6'>NOPERTI CIA. LTDA.</td></tr>
    <tr><td colspan='6'>RUC: " . $emisor[identificacion] . "</td></tr>
    <tr><td colspan='6'>PERIODO: " . $desde . "  AL " . $hasta . "</td></tr>
    ";

echo "
    <tr>
    <td colspan='4'></td>
    <td colspan='2'>SALDO</td>
    </tr>
    <tr>
    <td>CODIGO</td>
    <td>CUENTA</td>
    <td>DEBE</td>
    <td>HABER</td>
    <td>DEUDOR</td>
    <td>ACREEDOR</td>
    </tr>
    ";

$cns = $Set->lista_cuentas_fecha($desde, $hasta);
$cuentas0 = pg_fetch_all_columns($cns);
$cuentas = array_unique($cuentas0);

$n = 0;
$g = 0;
while ($n < count($cuentas)) {
    $d1 = substr($cuentas[$n], 0, 2);
    if ($niv == 1) {
        if ($g != $d1) {
            $rst1 = pg_fetch_array($Set->listar_descripcion_asiento($d1));
            $sm = pg_fetch_array($Set->lista_balance_general($d1, $desde, $hasta));
            echo "
                <tr>
                <td>" . $rst1[pln_codigo] . "</td>
                <td>" . $rst1[pln_descripcion] . "</td>
                <td>" . number_format($sm[debe1],$dc) . "</td>
                <td>" . number_format($sm[haber1],$dc) . "</td>
                ";
            $debe = $sm[debe1];
            $haber = $sm[haber1];
            $total = $debe - $haber;
            if ($total > 0) {
                $deudor = $total;
            }
            if ($total < 0) {
                $acreedor = $total;
            }
            echo"
                <td>" . number_format($deudor,$dc) . "</td>
                <td>" . number_format($acreedor,$dc) . "</td>
                </tr>
                ";
            $total_debe = $total_debe + $debe;
            $total_haber = $total_haber + $haber;
            $total_deudor = $total_deudor + $deudor;
            $total_acreedor = $total_acreedor + $acreedor;
            $deudor = '';
            $acreedor = '';
        }
    }
    if ($niv == 2) {
        $d2 = substr($cuentas[$n], 0, 5);
        if ($g2 != $d2) {
            $rst2 = pg_fetch_array($Set->listar_descripcion_asiento($d2));
            $sm2 = pg_fetch_array($Set->lista_balance_general($d2, $desde, $hasta));
            echo"
                <tr>
                <td>" . $rst2[pln_codigo] . "</td>
                <td>" . $rst2[pln_descripcion] . "</td>
                <td>" . number_format($sm2[debe2],$dc) . "</td>
                <td>" . number_format($sm2[haber2],$dc) . "</td>
                ";
            $debe = $sm2[debe2];
            $haber = $sm2[haber2];
            $total = $debe - $haber;
            if ($total > 0) {
                $deudor = $total;
            }
            if ($total < 0) {
                $acreedor = $total;
            }
            echo"
                <td>" . number_format($deudor,$dc) . "</td>
                <td>" . number_format($acreedor,$dc) . "</td>
                </tr>
               ";
            $total_debe = $total_debe + $debe;
            $total_haber = $total_haber + $haber;
            $total_deudor = $total_deudor + $deudor;
            $total_acreedor = $total_acreedor + $acreedor;
            $deudor = '';
            $acreedor = '';
        }
    }
    if ($niv == 3) {
        $d3 = substr($cuentas[$n], 0, 8);
        if ($g3 != $d3) {
            $rst3 = pg_fetch_array($Set->listar_descripcion_asiento($d3));
            $sm3 = pg_fetch_array($Set->lista_balance_general($d3, $desde, $hasta));
            echo"
                <tr>
                <td>" . $rst3[pln_codigo] . "</td>
                <td>" . $rst3[pln_descripcion] . "</td>
                <td>" . number_format($sm3[debe3],$dc) . "</td>
                <td>" . number_format($sm3[haber3],$dc) . "</td>
                ";
            $debe = $sm3[debe3];
            $haber = $sm3[haber3];
            $total = $debe - $haber;
            if ($total > 0) {
                $deudor = $total;
            }
            if ($total < 0) {
                $acreedor = $total;
            }
            echo"
                <td>" . number_format($deudor,$dc) . "</td>
                <td>" . number_format($acreedor,$dc) . "</td>
                </tr>
                ";
            $total_debe = $total_debe + $debe;
            $total_haber = $total_haber + $haber;
            $total_deudor = $total_deudor + $deudor;
            $total_acreedor = $total_acreedor + $acreedor;
            $deudor = '';
            $acreedor = '';
        }
    }
    if ($niv == 4) {
        $d4 = substr($cuentas[$n], 0, 11);
        if ($g4 != $d4) {
            $rst4 = pg_fetch_array($Set->listar_descripcion_asiento($d4));
            $sm4 = pg_fetch_array($Set->lista_balance_general($d4, $desde, $hasta));
            echo"
                <tr>
                <td>" . $rst4[pln_codigo] . "</td>
                <td>" . $rst4[pln_descripcion] . "</td>
                <td>" . number_format($sm4[debe4],$dc) . "</td>
                <td>" . number_format($sm4[haber4],$dc) . "</td>
                ";
            $debe = $sm4[debe4];
            $haber = $sm4[haber4];
            $total = $debe - $haber;
            if ($total > 0) {
                $deudor = $total;
            }
            if ($total < 0) {
                $acreedor = $total;
            }
            echo"
                <td>" . number_format($deudor,$dc) . "</td>
                <td>" . number_format($acreedor,$dc) . "</td>
                </tr>
                ";
            $total_debe = $total_debe + $debe;
            $total_haber = $total_haber + $haber;
            $total_deudor = $total_deudor + $deudor;
            $total_acreedor = $total_acreedor + $acreedor;
            $deudor = '';
            $acreedor = '';
        }
    }
    if ($niv == 5) {
        $rst_suma = pg_fetch_array($Set->lista_suma_cuentas($cuentas[$n], $desde, $hasta));
        $rst_cue = pg_fetch_array($Set->listar_descripcion_asiento($cuentas[$n]));
        echo"
            <tr>
            <td>" . $cuentas[$n] . "</td>
            <td>" . strtoupper($rst_cue[pln_descripcion]) . "</td>
            <td>" . number_format($rst_suma[debe],$dc) . "</td>
            <td>" . number_format($rst_suma[haber],$dc) . "</td>
            ";
        $debe = $rst_suma[debe];
        $haber = $rst_suma[haber];
        $total = $debe - $haber;
        if ($total > 0) {
            $deudor = $total;
        }
        if ($total < 0) {
            $acreedor = $total;
        }
        echo"
            <td>" . number_format($deudor,$dc) . "</td>
            <td>" . number_format($acreedor,$dc) . "</td>
            ";
        $total_debe = $total_debe + $debe;
        $total_haber = $total_haber + $haber;
        $total_deudor = $total_deudor + $deudor;
        $total_acreedor = $total_acreedor + $acreedor;
    }
    $n++;
    $g = $d1;
    $g2 = $d2;
    $g3 = $d3;
    $g4 = $d4;
    $total = 0;
    $deudor = '';
    $acreedor = '';
}

echo"
    <tr>
    <td></td>
    <td>SUMA TOTAL</td>
    ";
if ($niv == 1 || $niv == 2 || $niv == 3 || $niv == 4 || $niv == 5) {
    echo"
        <td>" . number_format($total_debe, $dc) . "</td>
        <td>" . number_format($total_haber, $dc) . "</td>
        <td>" . number_format($total_deudor, $dc) . "</td>
        <td>" . number_format($total_acreedor, $dc) . "</td>
        ";
}
echo "</table>";
?>