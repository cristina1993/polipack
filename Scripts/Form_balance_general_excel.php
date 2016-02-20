<?php

header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: filename=" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

include_once '../Clases/clsClase_balance_general.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_balance_general();
$niv = $_GET[nivel];
$anio = $_GET[anio];
$mes = $_GET[mes];
$ultimo_dia = 28;
while (checkdate($mes, $ultimo_dia + 1, $anio)) {
    $ultimo_dia++;
}
if ($mes < 10) {
    $mes = '0' . $mes;
}
$desde = '01-' . $mes . '-' . $anio;
$hasta = $ultimo_dia . '-' . $mes . '-' . $anio;
$dc = 2;
$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));
///*******Primer Enc********
echo "
    <table border='1'>
    <thead>
    <tr><th colspan='5'>BALANCE GENERAL</th></tr>
    </thead>
    <tr><td colspan='5'>NOPERTI CIA. LTDA.</td></tr>
    <tr><td colspan='5'>RUC: " . $emisor[identificacion] . "</td></tr>
    <tr><td colspan='5'>PERIODO: " . $desde . "  AL " . $hasta . "</td></tr>
    ";

echo "
    <tr>
    <td>COD. CUENTA</td>
    <td>NOMBRE CUENTA</td>
    <td>PARCIAL</td>
    <td>TOTAL</td>
    <td>% REND</td>
    </tr>
    ";

$i = 0;
while ($i < 3) {
    $i++;
    $d = $i . '.';
    $cns_cuentas = $Set->listar_asiento_agrupado($d, $desde, $hasta);
    $cuentas1 = Array();
    while ($rst_cuentas = pg_fetch_array($cns_cuentas)) {
        if (!empty($rst_cuentas[con_concepto_debe])) {
            array_push($cuentas1, $rst_cuentas[con_concepto_debe]);
        }
    }
    $cuentas = array_unique($cuentas1);
    $n = 0;
    $j = 1;
    $g = 0;
    while ($n < count($cuentas)) {
        $d1 = substr($cuentas[$n], 0, 2);
        if ($d1 == '2.') {

            $pp = pg_fetch_array($Set->suma_pasivo_patrimonio($desde, $hasta));
            $tpp = $pp[debe] - $pp[haber];
            $rdp = ($tpp * 100) / $tpp;
            echo"
                <tr>
                <td></td>
                <td>PASIVO Y PATRIMONIO</td>
                <td></td>
                <td>" . $op . "" . number_format($tpp, $dc) . "</td>
                <td>" . $op . "" . number_format($rdp, $dc) . "</td>
                </tr>
              ";
        }
        if ($d1 == '2.' || $d1 == '3.') {
            $op = '-';
        }

        if ($niv == 1 || $niv > 1) {
            if ($g != $d1) {
//                $this->Ln();
                $rst1 = pg_fetch_array($Set->listar_descripcion_asiento($d1));
                $sm = pg_fetch_array($Set->lista_balance_general($d1, $desde, $hasta));
                $tn1 = $sm[debe1] - $sm[haber1];
                if ($d1 == '2.' || $d1 == '3.') {
                    $rd1 = ($tn1 * 100) / $tpp;
                } else {
                    $rd1 = ($tn1 * 100) / $tn1;
                }
                echo"
                    <tr>
                    <td>" . $rst1[pln_codigo] . "</td>
                    <td>" . substr(strtoupper($rst1[pln_descripcion]), 0, 35) . "</td>
                    <td></td>
                    <td>" . $op . "" . number_format($tn1, $dc) . "</td>
                    <td>" . $op . "" . number_format($rd1, $dc) . "<td>
                    </tr>
                    ";
            }
        }
        if ($niv == 2 || $niv > 2) {
            $d2 = substr($cuentas[$n], 0, 5);
            if ($g2 != $d2) {
//                $this->Ln();
                $rst2 = pg_fetch_array($Set->listar_descripcion_asiento($d2));
                $sm2 = pg_fetch_array($Set->lista_balance_general($d2, $desde, $hasta));
                $tn2 = $sm2[debe2] - $sm2[haber2];
                if ($d1 == '2.' || $d1 == '3.') {
                    $rd2 = ($tn2 * 100) / $tpp;
                } else {
                    $rd2 = ($tn2 * 100) / $tn1;
                }
                echo"
                    <tr>
                    <td>" . $rst2[pln_codigo] . "</td>
                    <td>" . substr(strtoupper($rst2[pln_descripcion]), 0, 35) . "</td>
                    <td></td>
                    <td>" . $op . "" . number_format($tn2, $dc) . "</td>
                    <td>" . $op . "" . number_format($rd2, $dc) . "<td>
                    </tr>
                    ";
            }
        }
        if ($niv == 3 || $niv > 3) {
            $d3 = substr($cuentas[$n], 0, 8);
            if ($g3 != $d3) {
//                $this->Ln();
                $rst3 = pg_fetch_array($Set->listar_descripcion_asiento($d3));
                $sm3 = pg_fetch_array($Set->lista_balance_general($d3, $desde, $hasta));
                $tn3 = $sm3[debe3] - $sm3[haber3];
                if ($d1 == '2.' || $d1 == '3.') {
                    $rd3 = ($tn3 * 100) / $tpp;
                } else {
                    $rd3 = ($tn3 * 100) / $tn1;
                }
                echo"
                    <tr>
                    <td>" . $rst3[pln_codigo] . "</td>
                    <td>" . substr(strtoupper($rst3[pln_descripcion]), 0, 35) . "</td>
                    <td></td>
                    <td>" . $op . "" . number_format($tn3, $dc) . "</td>
                    <td>" . $op . "" . number_format($rd3, $dc) . "<td>
                    </tr>
                    ";
            }
        }
        if ($niv == 4 || $niv > 4) {
            $d4 = substr($cuentas[$n], 0, 11);
            if ($g4 != $d4) {
//                $this->Ln();
                $rst4 = pg_fetch_array($Set->listar_descripcion_asiento($d4));
                $sm4 = pg_fetch_array($Set->lista_balance_general($d4, $desde, $hasta));
                $tn4 = $sm4[debe4] - $sm4[haber4];
                if ($d1 == '2.' || $d1 == '3.') {
                    $rd4 = ($tn4 * 100) / $tpp;
                } else {
                    $rd4 = ($tn4 * 100) / $tn1;
                }
                echo"
                    <tr>
                    <td>" . $rst4[pln_codigo] . "</td>
                    <td>" . substr(strtoupper($rst4[pln_descripcion]), 0, 35) . "</td>
                    <td></td>
                    <td>" . $op . "" . number_format($tn4, $dc) . "</td>
                    <td>" . $op . "" . number_format($rd4, $dc) . "<td>
                    </tr>
                    ";
            }
        }
        if ($niv == 5) {
            $rst_cuentas1 = pg_fetch_array($Set->listar_descripcion_asiento($cuentas[$n]));
            $rst_v = pg_fetch_array($Set->suma_cuentas($cuentas[$n], $desde, $hasta));
            $tot = $rst_v[debe] - $rst_v[haber];
            if ($d1 == '2.' || $d1 == '3.') {
                $rd5 = ($tot * 100) / $tpp;
            } else {
                $rd5 = ($tot * 100) / $tn1;
            }
            echo"
                    <tr>
                    <td>" . $rst_cuentas1[pln_codigo] . "</td>
                    <td>" . substr(strtoupper($rst_cuentas1[pln_descripcion]), 0, 35) . "</td>
                    <td>" . $op . "" . number_format($tot, $dc) . "</td>
                    <td></td>
                    <td>" . $op . "" . number_format($rd5, $dc) . "</td>
                    </tr>
                    ";
        }
        $n++;
        $g = $d1;
        $g2 = $d2;
        $g3 = $d3;
        $g4 = $d4;
    }
}
echo"
    <tr>
    <td></td>
    <td>RESULTADO DEL PERIODO</td>
    <td></td>
    <td>" . number_format(0, $dc) . "</td>
    <td>" . number_format(0, $dc) . "</td>
    </tr>
    ";
echo "</table>";
?>