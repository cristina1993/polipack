<?php

header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: filename=" . date('Y-m-d'). ".xls");
header("Pragma: no-cache");
header("Expires: 0");

include_once '../Clases/clsClase_reportes.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Rep = new Reportes();
$niv = $_GET[nivel];
$anio = $_GET[anio];
$mes = $_GET[mes];
$dc=2;
//**************FUNCIONES**********************

function ultimoDia($mes, $anio) {
    $ultimo_dia = 28;
    while (checkdate($mes, $ultimo_dia + 1, $anio)) {
        $ultimo_dia++;
    }
    return $ultimo_dia;
}

function sms($val, $mensaje, $tnp) {
    $rdp = ($val * 100) / $tnp;
    echo"
        <td><td>
        <tr>
        <td></td>
        <td>$mensaje</td>
        <td></td>
        <td>" . $op . "" . number_format($val, $dc) . "</td>
        <td>" . $op . "" . number_format($rdp, $dc) . "</td>
        </tr>
        ";
}

function ventas_netas($Rep, $desde, $hasta) {
    $vb = pg_fetch_array($Rep->lista_balance_general('4.01.01.', $desde, $hasta));
    $tvb = $vb[debe3] - $vb[haber3];
    $ps = pg_fetch_array($Rep->lista_balance_general('4.01.02.', $desde, $hasta));
    $tps = $ps[debe3] - $ps[haber3];
    $in = pg_fetch_array($Rep->lista_balance_general('4.01.06.', $desde, $hasta));
    $tin = $in[debe3] - $in[haber3];
    $di = pg_fetch_array($Rep->lista_balance_general('4.01.07.', $desde, $hasta));
    $tdi = $di[debe3] - $di[haber3];
    $oi = pg_fetch_array($Rep->lista_balance_general('4.01.09.', $desde, $hasta));
    $toi = $oi[debe3] - $oi[haber3];
    $dsv = pg_fetch_array($Rep->lista_balance_general('4.01.10.', $desde, $hasta));
    $tdsv = $dsv[debe3] - $dsv[haber3];
    $dv = pg_fetch_array($Rep->lista_balance_general('4.01.11.', $desde, $hasta));
    $tdv = $dv[debe3] - $dv[haber3];
    $tvn = $tvb + $tps + $tin + $tdi + $toi - $tdsv - $tdv;
    return $tvn;
}

function utilidad_bruta($Rep, $desde, $hasta) {
    $tvn = ventas_netas($Rep, $desde, $hasta);
    $bp = pg_fetch_array($Rep->lista_balance_general('4.01.12.', $desde, $hasta));
    $tbp = $bp[debe3] - $bp[haber3];
    $rc = pg_fetch_array($Rep->lista_balance_general('4.01.13.', $desde, $hasta));
    $trc = $rc[debe3] - $rc[haber3];
    $uv = pg_fetch_array($Rep->lista_balance_general('4.01.18.', $desde, $hasta));
    $tuv = $uv[debe3] - $uv[haber3];
    $ir = pg_fetch_array($Rep->lista_balance_general('4.01.19.', $desde, $hasta));
    $tir = $ir[debe3] - $ir[haber3];
    $oi = pg_fetch_array($Rep->lista_balance_general('4.03.', $desde, $hasta));
    $toi = $oi[debe2] - $oi[haber2];
    $co = pg_fetch_array($Rep->lista_balance_general('5.', $desde, $hasta));
    $tco = $co[debe1] - $co[haber1];
    $tub = $tvn - $tbp - $trc + $tuv + $tir + $toi - $tco;
    return $tub;
}

function utilidad_neta_ventas($Rep, $desde, $hasta) {
    $tub = utilidad_bruta($Rep, $desde, $hasta);
    $ga = pg_fetch_array($Rep->lista_balance_general('6.01.', $desde, $hasta));
    $tga = $ga[debe2] - $ga[haber2];
    $tunv = $tub - $tga;
    return $tunv;
}

function utilidad_antes($Rep, $desde, $hasta) {
    $tub = utilidad_bruta($Rep, $desde, $hasta);
    $uai = pg_fetch_array($Rep->lista_balance_general('6.', $desde, $hasta));
    $tuai = $uai[debe1] - $uai[haber1];
    $tui = $tub - $tuai;
    return $tui;
}

function utilidad_ejercicio($Rep, $desde, $hasta) {
    $tui = utilidad_antes($Rep, $desde, $hasta);
    $ue = pg_fetch_array($Rep->lista_balance_general('7.', $desde, $hasta));
    $tue = $ue[debe1] - $ue[haber1];
    $tuej = $tui - $tue;
    return $tuej;
}

////************FIN DE FUNCIONES***************
$hasta = ultimoDia($mes, $anio);
if ($mes < 10) {
    $mes = '0' . $mes;
} else {
    $mes = $mes;
}
$desde = trim($anio) . '-' . $mes . '-01';
$hasta = trim($anio) . '-' . $mes . '-' . $hasta;

switch ($mes) {
    case '01':$mes = 'Enero';
        break;
    case '02':$mes = 'Febrero';
        break;
    case '03':$mes = 'Marzo';
        break;
    case '04':$mes = 'Abril';
        break;
    case '05':$mes = 'Mayo';
        break;
    case '06':$mes = 'Junio';
        break;
    case '07':$mes = 'Julio';
        break;
    case '08':$mes = 'Agosto';
        break;
    case '09':$mes = 'Septiembre';
        break;
    case '10':$mes = 'Octubre';
        break;
    case '11':$mes = 'Noviembre';
        break;
    case '12':$mes = 'Diciembre';
        break;
}
$array0 = Array();
$cns_cuentas = $Rep->lista_asientos_epyg($desde, $hasta);
while ($rst_cuentas = pg_fetch_array($cns_cuentas)) {
    if (!empty($rst_cuentas[con_concepto_debe])) {
        array_push($array0, $rst_cuentas[con_concepto_debe]);
    }
}
$array1 = array_unique($array0);
$periodo = $mes . ' del ' . $anio;
echo "<table><tr><td>";
//echo "<img src='../img/logo_noperti.jpg' />";
echo "</td></tr>";
echo "<tr><td>ESTADO DE RESULTADOS</td></tr>";
echo "<tr><td>NOPERTI CIA. LTDA." . count($array1) . "</td></tr>";
echo "<tr><td>RUC: 1790007871001</td></tr>";
echo "<tr><td>Periodo $periodo</td></tr>";

//Cuentas
echo"
    <tr>
    <td>Codigo</td>
    <td>Cuenta</td>
    <td>Parcial</td>
    <td>Total</td>
    <td>% Rendimiento</td>
    </tr>
    ";

$n = 0;
$j = 1;
$g = 0;
$det = 1;
$ut = 1;
$ua = 1;
$uej = 1;
$dv = 1;
while ($n < count($array1)) {
    $d = substr($array1[$n], 0, 1);
    $d1 = substr($array1[$n], 0, 2);

    if ($d > '4') {
        $sm = pg_fetch_array($Rep->lista_balance_general('4.', $desde, $hasta));
        $tnp = $sm[debe1] - $sm[haber1];
        $ing = pg_fetch_array($Rep->lista_balance_general($d1, $desde, $hasta));
        $tn1 = $ing[debe1] - $ing[haber1];
        $rd1 = ($tn1 * 100) / $tnp;
    } else {
        $tvn = ventas_netas($Rep, $desde, $hasta);
        $tn1 = $tvn;
        $rd1 = ($tn1 * 100) / $tn1;
    }



    if ($niv == 1 || $niv > 1) {
        if ($g != $d1) {

            if ($d1 == '5.' && $det == 1) {
                $tvn = ventas_netas($Rep, $desde, $hasta);
                sms($tvn, 'VENTAS NETAS', $tnp);
                $det = 0;
            }
            if ($d1 == '6.' && $ut == 1) {
                $tub = utilidad_bruta($Rep, $desde, $hasta, $tvn);
                sms($tub, 'UTILIDAD BRUTA EN VENTAS', $tnp);
                $ut = 0;
            }
            if ($d1 == '7.' && $ua == 1) {
                $tui = utilidad_antes($Rep, $desde, $hasta);
                sms($tui, 'UTILIDAD ANTES DE IMPUESTOS Y PARTICIPACIONES', $tnp);
                $ua = 0;
            }
            $rst1 = pg_fetch_array($Rep->listar_descripcion_asiento($d1));
            echo"
                <tr>
                <td>" . $rst1[pln_codigo] . "</td>
                <td>" . substr(strtoupper($rst1[pln_descripcion]), 0, 35) . "</td>
                <td></td>
                <td>" . $op . "" . number_format($tn1, $dc) . "</td>
                <td>" . $op . "" . number_format($rd1, $dc) . "</td>
                </tr>
                ";
        }
    }
    if ($niv == 2 || $niv > 2) {
        $d2 = substr($array1[$n], 0, 5);
        if ($g2 != $d2) {
            if (($d2 == '6.02.' || $d2 == '6.03.' || $d2 == '6.04.' || $d2 == '6.05.' || $d2 == '6.06.' || $d2 == '6.07.' || $d2 == '6.08.' || $d2 == '6.09.' || $d3 == '6.10.') && $dv == 1) {
                $tunv = utilidad_neta_ventas($Rep, $desde, $hasta);
                sms($tunv, 'UTILIDAD NETA EN VENTAS', $tnp);
                $dv = 0;
            }
            echo"
                <tr>
                <td></td>
                </tr>
                ";
            $rst2 = pg_fetch_array($Rep->listar_descripcion_asiento($d2));
            $sm2 = pg_fetch_array($Rep->lista_balance_general($d2, $desde, $hasta));
            if ($d2 == '4.01.') {
                $tvn = ventas_netas($Rep, $desde, $hasta);
                $tn2 = $tvn;
            } else {
                $tn2 = $sm2[debe2] - $sm2[haber2];
            }
            if ($d > '4') {
                $rd2 = ($tn2 * 100) / $tnp;
            } else {
                $rd2 = ($tn2 * 100) / $tn1;
            }
            echo"
                <tr>
                <td>" . $rst2[pln_codigo] . "</td>
                <td>" . substr(strtoupper($rst2[pln_descripcion]), 0, 35) . "</td>
                <td></td>
                <td>" . $op . "" . number_format($tn2, $dc) . "</td>
                <td>" . $op . "" . number_format($rd2, $dc) . "</td>
                </tr>
                ";
        }
    }
    if ($niv == 3 || $niv > 3) {
        $d3 = substr($array1[$n], 0, 8);
        if ($g3 != $d3) {
            if (($d3 == '4.01.12.' || $d3 == '4.01.13.' || $d3 == '4.01.18.') && $det == 1) {
                $tvn = ventas_netas($Rep, $desde, $hasta);
                sms($tvn, 'VENTAS NETAS', $tnp);
                $det = 0;
            }
            echo"
                <tr>
                <td></td>
                </tr>
                ";
            $rst3 = pg_fetch_array($Rep->listar_descripcion_asiento($d3));
            $sm3 = pg_fetch_array($Rep->lista_balance_general($d3, $desde, $hasta));
            $tn3 = $sm3[debe3] - $sm3[haber3];
            if ($d > '4') {
                $rd3 = ($tn3 * 100) / $tnp;
            } else {
                $rd3 = ($tn3 * 100) / $tn1;
            }
            echo"
                <tr>
                <td>" . $rst3[pln_codigo] . "</td>
                <td>" . substr(strtoupper($rst3[pln_descripcion]), 0, 35) . "</td>
                <td></td>
                <td>" . $op . "" . number_format($tn3, $dc) . "</td>
                <td>" . $op . "" . number_format($rd3, $dc) . "</td>
                </tr>
                ";
        }
    }
    if ($niv == 4 || $niv > 4) {
        $d4 = substr($array1[$n], 0, 11);
        if ($g4 != $d4) {
            echo"
                <tr>
                <td></td>
                </tr>
                ";
            $rst4 = pg_fetch_array($Rep->listar_descripcion_asiento($d4));
            $sm4 = pg_fetch_array($Rep->lista_balance_general($d4, $desde, $hasta));
            $tn4 = $sm4[debe4] - $sm4[haber4];
            if ($d > '4') {
                $rd4 = ($tn4 * 100) / $tnp;
            } else {
                $rd4 = ($tn4 * 100) / $tn1;
            }
            echo"
                <tr>
                <td>" . $rst4[pln_codigo] . "</td>
                <td>" . substr(strtoupper($rst4[pln_descripcion]), 0, 35) . "</td>
                <td></td>
                <td>" . $op . "" . number_format($tn4, $dc) . "</td>
                <td>" . $op . "" . number_format($rd4, $dc) . "</td>
                </tr>
                ";
        }
    }
    if ($niv == 5) {
        $rst_cuentas1 = pg_fetch_array($Rep->listar_descripcion_asiento($array1[$n]));
        $rst_v = pg_fetch_array($Rep->suma_cuentas($array1[$n], $desde, $hasta));
        $tot = $rst_v[debe] - $rst_v[haber];
        if ($d > '4') {
            $rd5 = ($tot * 100) / $tnp;
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
if ($det == 1) {
    $tvn = ventas_netas($Rep, $desde, $hasta);
    sms($tvn, 'VENTAS NETAS', $tnp);
    $det = 0;
}
if ($ut == 1) {
    $tub = utilidad_bruta($Rep, $desde, $hasta);
    sms($tub, 'UTILIDAD BRUTA EN VENTAS', $tnp);
    $ut = 0;
}

if ($dv == 1) {
    $tunv = utilidad_neta_ventas($Rep, $desde, $hasta);
    sms($tunv, 'UTILIDAD NETA EN VENTAS', $tnp);
    $dv = 0;
}
if ($ua == 1) {
    $tui = utilidad_antes($Rep, $desde, $hasta);
    sms($tui, 'UTILIDAD ANTES DE IMPUESTOS Y PARTICIPACIONES', $tnp);
    $ua = 0;
}
if ($uej == 1) {
    $tuej = utilidad_ejercicio($Rep, $desde, $hasta);
    sms($tuej, 'UTILIDAD NETA DEL EJERCICIO', $tnp);
    $ut = 0;
}
echo "</table>";
?>