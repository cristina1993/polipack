<?php
set_time_limit(0);
date_default_timezone_set('America/Guayaquil');
include_once '../Includes/permisos.php';
include_once("../Clases/clsProduccion_reportes.php");
//session_start();
//include_once("../Clases/clsMaquinas.php");
//include_once("../Clases/clsSecciones.php");
//include_once '../Clases/clsMateriaPrima.php';
//include_once("../Clases/clsRegistroExtrusion.php");
//include_once("../Clases/clsRegistroImpresion.php");
//include_once("../Clases/clsRegistroSellado.php");
//header("Content-type: application/vnd.ms-excel");
//header("Content-Disposition:attachment;filename=produccion.xls");
$sec = $_POST[sec_id];
if ($_POST[extm] == 'on') {
    rptExtrusionResumen($sec, $_POST['f_month_from'], $_POST['f_month_until']);
}

//if ($_POST[impm] == 'on') {
//    rptImpresionResumen($sec, $_POST['f_month_from'], $_POST['f_month_until']);
//}

if ($_POST[sellm] == 'on') {
    rptsSelladoResumen($sec, $_POST['f_month_from'], $_POST['f_month_until']);
}

if ($_POST[repm] == 'on' || $_POST[drd] == 'on') {
    if ($_POST[drd] == 'on') {
        $from = $_POST['f_month_from'];
        $until = $_POST['f_month_until'];
        $d = dias($from, $until);
        $n = 0;
        while ($n <= intval($d)) {
            $d1 = explode('/', $from);
            $f1 = date_create($d1[0] . '-' . $d1[1] . '-' . $d1[2]);
            date_add($f1, date_interval_create_from_date_string($n . ' days'));
            $dt = date_format($f1, 'd/m/Y');
            ?>
            <table>
                <?php
                rptRepRecResumen($dt, $dt, 'REPORTE DIARIO');
                ?>
                <tr><td><br></td></tr>
                <?php
                rptDespRecDet($dt, $dt, 'REPORTE DIARIO');
                ?>
                <tr><td><br></td></tr>
                <?php
                rptConsumoDespDetalle($dt, $dt, 'REPORTE DIARIO');
                ?>
            </table>
            <?php
            $n++;
        }
    } else {
        rptRepRecResumen($_POST['f_month_from'], $_POST['f_month_until']);
        rptDespRecDet($_POST['f_month_from'], $_POST['f_month_until']);
        rptConsumoDespDetalle($_POST['f_month_from'], $_POST['f_month_until']);
    }
}

function dias($from, $until) {
//    $f = explode('/', $from);
//    $u = explode('/', $until);
//    $fecha_i = $f[0] . '-' . $f[1] . '-' . $f[2];
//    $fecha_f = $u[0] . '-' . $u[1] . '-' . $u[2];
    $dias = (strtotime($from) - strtotime($until)) / 86400;
    $dias = abs($dias);
    $dias = floor($dias);
    return $dias;
}
?>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title></title>
<script type="text/javascript">
    $(document).ready(function () {
        $('#dataTable').dataTable();
    });
    function imprimir()
    {
        window.print()
    }
</script>
<body>
    <?php

    function rptExtrusionResumen($sec, $from, $until) {
//        $Seccion = new Secciones();
//        $Maq = new Maquinas();
//        $Objeto = new Extrusion();
//        $Mp = new MateriaPrima();
        $Set = new Produccion_reportes();
//        $rstSec = pg_fetch_array($Seccion->listaUnaSecciones($sec));
        $cnsMaq = $Set->listaExtrusoras($sec);
        $cnsMaq2 = $Set->listaExtrusoras($sec);
        $cnsMaq3 = $Set->listaExtrusoras($sec);
        $cnsMaq4 = $Set->listaExtrusoras($sec);
        $col = pg_num_rows($cnsMaq) * 4;
        if (pg_num_rows($cnsMaq) > 0) {
            //Head*************************************************************************** 
            ?>
        <tr>
            <td>
                <table align="left" id="tbl" width="100%" border="1">
                    <thead >
                        <tr>
                            <th colspan="<?php echo 3 + $col ?>" align="center" >POLIPACK</th>  
                        </tr>
                        <tr>
                            <th colspan="<?php echo 3 + $col ?>" align="center">REPORTE DE PRODUCCION - RESUMEN OPERATIVO</th>  
                        </tr>
                        <tr>
                            <th colspan="<?php echo $col ?>" align="left">DEPARTAMENTO DE EXTRUSION</th>
                            <th colspan="3"> <?php echo 'Desde: ' . $from . '   Hasta: ' . $until ?></th>
                        </tr>
                        <tr>
                            <th rowspan="3">FECHA</th>
                            <th rowspan="3">DATOS</th>
                            <?php
                            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                                ?>
                                <th style="width: 100px" colspan="4"><?php echo $rstMaq[maq_a] ?></th>
                                <?php
                            }
                            ?>
                            <th style="width: 100px" rowspan="3">TOTAL</th>
                        </tr>
                        <tr>
                            <?php
                            while ($rstMaq3 = pg_fetch_array($cnsMaq3)) {
                                ?>
                                <th style="width: 100px" colspan="2">Conforme</th>
                                <th style="width: 100px" colspan="2">Inconforme</th>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <?php
                            while ($rstMaq4 = pg_fetch_array($cnsMaq4)) {
                                ?>
                                <th style="width: 100px" >Peso Neto</th>
                                <th style="width: 100px" >Peso Bruto</th>
                                <th style="width: 100px" >Peso Neto</th>
                                <th style="width: 100px" >Peso Bruto</th>
                                <?php
                            }
                            ?>
                        </tr>
        <!--                        <tr>
                        <?php
//                            while ($rstMaq = pg_fetch_array($cnsMaq2)) {
                        ?>
                                <th>T1</th> 
                                <th>T2</th> 
                                <th>SUMA</th> 
                        <?php
//                            }
                        ?>
                        </tr>-->
                    </thead>
                    <tr>
                        <?php
                        //Body***************************************************************************        
                        $d = (intval(dias($from, $until)) + 1);
                        $dnolab = 0;
                        $n = 0;
                        while ($n < $d) {
                            $d1 = explode('-', $from);
                            $f1 = date_create($d1[0] . '-' . $d1[1] . '-' . $d1[2]);
                            date_add($f1, date_interval_create_from_date_string($n . ' days'));
                            $dt = date_format($f1, 'd/m/Y');
//                            $dt = $from;
                            //Consumo mp***************************************************************
                            ?>
                            <td rowspan="5"><?php echo $dt ?></td>
                            <td>Consumo mp (kg)</td>
                            <?php
                            $cnsMaq = $Set->listaExtrusoras($sec);
                            $tot = 0;
                            $date = $dt;
                            $produccion = pg_fetch_array($Set->listaExtrusionProduccionByDateSec($sec, $date, $date));
                            $cant = pg_fetch_array($Set->listarSumaEgresoMateriaPrimaFecha($sec, $date, $date));
                            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                                ?>
                                <td align="center">-</td>
                                <td align="center">-</td>
                                <td align="center">-</td>
                                <td align="center">-</td>
                <!--                                    <td>-</td>
                                <td>-</td>-->
                                <?php
                            }
                            ?>
                            <td align="right"><?php echo number_format($cant[sum], 2) ?></td>
                        </tr>
                        <!--//Consumo desp-rec*********************************************************************-->        
                        <tr>    
                            <td>Consumo Rep y Rec (kg)</td>
                            <?php
                            $cnsMaq = $Set->listaExtrusoras($sec);
                            $tot = 0;
                            $rstReciclado = pg_fetch_array($Set->listarSumaReciclado($sec, $date, $date)); //Ingreso de Reciclado a Extrusion de Otra Area
//                                $rstReprocesadoOut = pg_fetch_array($Mp->listarSumaReprocesadoOut($sec, $date, $date)); //Ingreso de Reciclado a Extrusion de Otra Area
//                                $rstReprocesadoIn = pg_fetch_array($Mp->listarSumaReprocesadoIn($sec, $date, $date)); //04 proc=sec dest=sec
//                                $consExt = $rstReciclado[sum] + $rstReprocesadoOut[sum] + $rstReprocesadoIn[sum]; //Consumo REC REP
                            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                                ?>  
                                <td align="center">-</td>
                                <td align="center">-</td>
                                <td align="center">-</td>
                                <td align="center">-</td>
                                <!--
                                <td>-</td>
                                <td>-</td>-->
                                <?php
                            }
                            ?>  
                            <td align="right"><?php echo number_format($rstReciclado[sum], 2) ?></td>
                        </tr>
                        <tr>
                            <!--//Produccion kg***************************************************************-->        
                            <td>Produccion (kg)</td>
                            <?php
                            $cnsMaq = $Set->listaExtrusoras($sec);
                            $tot = 0;
                            $val = 0;
                            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                                $val++;
                                if ($produccion[sum] > 1) {
                                    $cant = pg_fetch_array($Set->listaExtrusionProduccionByDateMaq($rstMaq[id], $date, $date));
                                    $cnt_c = pg_fetch_array($Set->listaExtrusionProduccionByDateMaqEst($rstMaq[id], $date, $date, '0'));
                                    $cnt_i = pg_fetch_array($Set->listaExtrusionProduccionByDateMaqEst($rstMaq[id], $date, $date, '3'));
//                                        $cnt_t1 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $date, $date, '1'));
//                                        $cnt_t2 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $date, $date, '2'));
//                                        
                                    ?>  
                                                                                                            <!--                                        <td align="right"><?php echo number_format($cnt_t1[sum]) ?></td>
                                                                                                                                    <td align="right"><?php echo number_format($cnt_t2[sum]) ?></td>-->
                                    <td align="right"><?php echo number_format($cnt_c[pneto], 2) ?></td>
                                    <td align="right"><?php echo number_format($cnt_c[pbruto], 2) ?></td>
                                    <td align="right"><?php echo number_format($cnt_i[pneto], 2) ?></td>
                                    <td align="right"><?php echo number_format($cnt_i[pbruto], 2) ?></td>
                                    <!--<td align="right"><?php echo number_format($cant[sum], 2) ?></td>-->
                                    <?php
                                    $tot = $tot + $cant[sum];
                                } else {
                                    if ($val == 2) {
                                        $txt = 'NO SE TRABAJA';
                                        $dnolab++;
                                        $colspan = '4';
                                    } ELSE {
                                        $txt = '';
                                        $colspan = '4';
                                    }
                                    ?>
                                    <td align="center" colspan="<?php echo $colspan ?>"><?php echo $txt ?></td>
                                    <?php
                                }
                            }
                            ?>
                            <td align="right"><?php echo number_format($tot, 2) ?></td>
                        </tr>
                        <tr>
                            <!--//Produccion mt***************************************************************-->        
                            <td>Produccion (m)</td>
                            <?php
                            $cnsMaq = $Set->listaExtrusoras($sec);
                            $tot = 0;
                            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                                $cant = pg_fetch_array($Set->listaExtrusionProduccionByDateMaq($rstMaq[id], $date, $date));
                                $cnt_c = pg_fetch_array($Set->listaExtrusionProduccionByDateMaqEst($rstMaq[id], $date, $date, '0'));
                                $cnt_i = pg_fetch_array($Set->listaExtrusionProduccionByDateMaqEst($rstMaq[id], $date, $date, '3'));
//                                    $cnt_t1 = pg_fetch_array($Set->listaExtrusionByMaqDateTur($rstMaq[ext_id], $date, $date, '1'));
//                                    $cnt_t2 = pg_fetch_array($Set->listaExtrusionByMaqDateTur($rstMaq[ext_id], $date, $date, '2'));
//                                    
                                ?>
                                                                                <!--<td align="right"><?php echo number_format($cnt_t1[mt] * 1000) ?></td>-->
                                                                                <!--<td align="right"><?php echo number_format($cnt_t2[mt] * 1000) ?></td>-->
                                <td align="right"><?php echo number_format($cnt_c[mts_neto], 2) ?></td>
                                <td align="right"><?php echo number_format($cnt_c[mts_bruto], 2) ?></td>
                                <td align="right"><?php echo number_format($cnt_i[mts_neto], 2) ?></td>
                                <td align="right"><?php echo number_format($cnt_i[mts_bruto], 2) ?></td>
                            <!--<td align="right"><?php echo number_format($cant[mts], 2) ?></td>-->
                                <?php
                                $tot = $tot + ($cant[mts]);
                            }
                            ?>
                            <td align="right"><?php echo number_format($tot, 2) ?></td>
                        </tr>
                        <!--//Desp kg***************************************************************-->        
                        <tr>
                            <td>Desperdicio (kg)</td>
                            <?php
                            $cnsMaq = $Set->listaExtrusoras($sec);
                            $tot = 0;
                            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                                $cant = pg_fetch_array($Set->listDesperdicio($rstMaq[id], $date, $date)); //Total Desperdicio de Extrusion
//                                $cnt_t1 = pg_fetch_array($Set->listDespExtrusionByMaqTur($rstMaq[ext_id], $date, $date, '1')); //Total Desperdicio de Extrusion
//                                $cnt_t2 = pg_fetch_array($Set->listDespExtrusionByMaqTur($rstMaq[ext_id], $date, $date, '2')); //Total Desperdicio de Extrusion
                                ?>
                                                    <!--                                <td align="right"><?php echo number_format($cnt_t1[sum]) ?></td>
                                                                        <td align="right"><?php echo number_format($cnt_t2[sum]) ?></td>
                                                                        <td align="right"><?php echo number_format($cant[sum]) ?></td>-->
                                <td align="center">-</td>
                                <td align="center">-</td>
                                <td align="center">-</td>
                                <td align="center">-</td>
                                <?php
                            }
                            ?>
                            <td align="right"><?php echo number_format($cant[sum], 2) ?></td>
                        </tr>
                        <tr>
                            <?php
                            $n++;
                        }
                        $d = ($d - $dnolab);
                        //Totales kg***************************************************************        
                        ?>
                        <td colspan="2">CONSUMO MP x MAQUINA (kg)</td>
                        <?php
                        $cnsMaq = $Set->listaExtrusoras($sec);
                        $cant = pg_fetch_array($Set->listarSumaEgresoMateriaPrimaFecha($sec, $from, $until));
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            ?>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
            <!--                        <td>-</td>
                            <td>-</td>-->
                            <?php
                        }
                        ?>
                        <td align="right"><?php echo number_format($cant[sum], 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">CONSUMO REP Y REC x MAQUINA (kg)</td>
                        <?php
                        $cnsMaq = $Set->listaExtrusoras($sec);
                        $rstReciclado = pg_fetch_array($Set->listarSumaReciclado($sec, $from, $until)); //Ingreso de Reciclado a Extrusion de Otra Area
//                            $rstReprocesadoOut = pg_fetch_array($Mp->listarSumaReprocesadoOut($sec, $from, $until)); //Ingreso de Reciclado a Extrusion de Otra Area
//                            $rstReprocesadoIn = pg_fetch_array($Mp->listarSumaReprocesadoIn($sec, $from, $until)); //04 proc=sec dest=sec
//                            $consExt = $rstReciclado[sum] + $rstReprocesadoOut[sum] + $rstReprocesadoIn[sum]; //Consumo REC REP
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            ?>  
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
            <!--                        <td>-</td>
                            <td>-</td>-->
                            <?php
                        }
                        ?>
                        <td align="right"><?php echo number_format($rstReciclado[sum], 2) ?></td>
                    </tr>
                    <tr>
                        <!--//Total Produccion / maq kg***************************************************************-->        
                        <td colspan="2">PRODUCCION POR MAQ (kg)</td>
                        <?php
                        $cnsMaq = $Set->listaExtrusoras($sec);
                        $tot = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $cant = pg_fetch_array($Set->listaExtrusionProduccionByDateMaq($rstMaq[id], $from, $until));
//                                $cnt_t1 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $from, $until, '1'));
//                                $cnt_t2 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $from, $until, '2'));
                            $cnt_c = pg_fetch_array($Set->listaExtrusionProduccionByDateMaqEst($rstMaq[id], $from, $until, '0'));
                            $cnt_i = pg_fetch_array($Set->listaExtrusionProduccionByDateMaqEst($rstMaq[id], $from, $until, '3'));
                            ?>
                                            <!--                        <td align="right"><?php echo number_format($cnt_t1[sum]) ?></td>
                                                            <td align="right"><?php echo number_format($cnt_t2[sum]) ?></td>-->
                                    <!--<td align="right"><?php echo number_format($cant[sum], 2) ?></td>-->
                            <td align="right"><?php echo number_format($cnt_c[pneto], 2) ?></td>
                            <td align="right"><?php echo number_format($cnt_c[pbruto], 2) ?></td>
                            <td align="right"><?php echo number_format($cnt_i[pneto], 2) ?></td>
                            <td align="right"><?php echo number_format($cnt_i[pbruto], 2) ?></td>
                            <?php
                            $tot = $tot + $cant[sum];
                        }
                        ?>
                        <td align="right"><?php echo number_format($tot, 2) ?></td>
                    </tr>
                    <tr>
                        <!--//Total Produccion / maq mt***************************************************************-->        
                        <td colspan="2">PRODUCCION POR MAQ (m)</td>
                        <?php
                        $cnsMaq = $Set->listaExtrusoras($sec);
                        $tot = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $cant = pg_fetch_array($Set->listaExtrusionProduccionByDateMaq($rstMaq[id], $from, $until));
                            $cnt_c = pg_fetch_array($Set->listaExtrusionProduccionByDateMaqEst($rstMaq[id], $from, $until, '0'));
                            $cnt_i = pg_fetch_array($Set->listaExtrusionProduccionByDateMaqEst($rstMaq[id], $from, $until, '3'));
//                                $cnt_t1 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $from, $until, '1'));
//                                $cnt_t2 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $from, $until, '2'));
                            ?>
                                            <!--                        <td align="right"><?php echo number_format($cnt_t1[mt] * 1000) ?></td>
                                                            <td align="right"><?php echo number_format($cnt_t2[mt] * 1000) ?></td>-->
                            <td align="right"><?php echo number_format($cnt_c[mts_neto], 2) ?></td>
                            <td align="right"><?php echo number_format($cnt_c[mts_bruto], 2) ?></td>
                            <td align="right"><?php echo number_format($cnt_i[mts_neto], 2) ?></td>
                            <td align="right"><?php echo number_format($cnt_i[mts_bruto], 2) ?></td>
                            <!--<td align="right"><?php echo number_format($cant[mts], 2) ?></td>-->
                            <?php
                            $tot = $tot + ($cant[mts]);
                        }
                        ?>
                        <td align="right"><?php echo number_format($tot, 2) ?></td>
                    </tr>
                    <tr>
                        <!--//Total desperdicio / maq kg***************************************************************-->        
                        <td colspan="2"> DESPERDICIO POR MAQ (kg)</td>
                        <?php
                        $cnsMaq = $Set->listaExtrusoras($sec);
                        $tot = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $cant = pg_fetch_array($Set->listDesperdicio($rstMaq[ext_id], $from, $until)); //Total Desperdicio de Extrusion
//                                $cnt_t1 = pg_fetch_array($Mp->listDespExtrusionByMaqTur($rstMaq[ext_id], $from, $until, '1')); //Total Desperdicio de Extrusion
//                                $cnt_t2 = pg_fetch_array($Mp->listDespExtrusionByMaqTur($rstMaq[ext_id], $from, $until, '2')); //Total Desperdicio de Extrusion
//                                
                            ?>
                                            <!--                        <td align="right"><?php echo number_format($cnt_t1[sum]) ?></td>
                                                            <td align="right"><?php echo number_format($cnt_t2[sum]) ?></td>-->
                                                            <!--<td align="right"><?php echo number_format($cant[sum]) ?></td>-->
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <?php
//                                $tot = $tot + ($cant[sum]);
                        }
                        ?>
                        <td align="right"><?php echo number_format($cant[sum], 2) ?></td>
                    </tr>
                    <tr>
                        <!--//Capacidad Teorica**************************************************************-->        
                        <td colspan="2">CAPACIDAD TEORICA POR MAQ (kg/h)</td>
                        <?php
                        $cnsMaq = $Set->listaExtrusoras($sec);
                        $tot = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            ?>   
                                            <!--                        <td>-</td>
                                                            <td>-</td>-->
                            <td align="center" colspan="4"><?php echo $rstMaq[maq_d] ?></td>
                            <?php
                            $tot = $tot + $rstMaq[maq_d];
                        }
                        ?> 
                        <td align="right"><?php echo number_format($tot) ?></td>
                    </tr>
                    <tr>
                        <!--//Capacidad Real**************************************************************-->        
                        <td colspan="2">CAPACIDAD REAL POR MAQ (kg/h)</td>
                        <?php
                        $cnsMaq = $Set->listaExtrusoras($sec);
                        $tot = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $cant = pg_fetch_array($Set->listaExtrusionProduccionByDateMaq($rstMaq[id], $from, $until));
//                                $cnt_1 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $from, $until, '1'));
//                                $cnt_2 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $from, $until, '2'));
//                                
                            ?> 
                                                            <!--<td align="right"><?php echo number_format($cnt_1[sum] / (($d) * 12)) ?></td>-->
                                                            <!--<td align="right"><?php echo number_format($cnt_2[sum] / (($d) * 12)) ?></td>-->
                            <td align="center" colspan="4"><?php echo number_format($cant[sum] / (($d) * 24)) ?></td>
                            <?php
                            $tot = $tot + ($cant[sum] / (($d) * 24));
                        }
                        ?> 
                        <td align="right"><?php echo number_format($tot) ?></td>
                    </tr>
                    <tr>
                        <!--//EFICIENCIA POR CAPACIDAD**************************************************************-->        
                        <td colspan="2">EFICIENCIA POR CAPACIDAD</td>
                        <?php
                        $cnsMaq = $Set->listaExtrusoras($sec);
                        $tot = 0;
                        $div = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $div++;
                            $cant = pg_fetch_array($Set->listaExtrusionProduccionByDateMaq($rstMaq[id], $from, $until));
//                                $cnt_1 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $from, $until, '1'));
//                                $cnt_2 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $from, $until, '2'));
//                                
                            ?> 
                                            <!--                        <td align="right"><?php echo number_format(($cnt_1[sum] / (($d) * 12)) / $rstMaq[maq_d], 2) ?></td>
                                                            <td align="right"><?php echo number_format(($cnt_2[sum] / (($d) * 12)) / $rstMaq[maq_d], 2) ?></td>-->
                            <td align="center" colspan="4"><?php echo number_format(($cant[sum] / (($d) * 24)) / $rstMaq[maq_d], 2) ?></td>
                            <?php
                            $tot = $tot + round(($cant[sum] / (($d) * 24)) / $rstMaq[maq_d], 2);
                        }
                        ?> 
                        <td align="right"><?php echo number_format($tot / $div, 2) ?></td>
                    </tr>
                    <tr>
                        <!--//EFICIENCIA**************************************************************-->    
                        <td colspan="2">EFICIENCIA</td>
                        <?php
                        $cnsMaq = $Set->listaExtrusoras($sec);
                        $tot = 0;
                        $div = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $div++;
                            $cant = pg_fetch_array($Set->listaExtrusionProduccionByDateMaq($rstMaq[id], $from, $until));
//                                $cnt_1 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $from, $until, '1'));
//                                $cnt_2 = pg_fetch_array($Objeto->listaExtrusionByMaqDateTur($rstMaq[ext_id], $from, $until, '2'));
//                                
                            ?> 
                                        <!--                        <td align="right"><?php echo number_format(($cnt_1[sum] / (($d) * 12)) / $rstMaq[ext_productividad] * 100, 2) . '%' ?></td>-->
                                                        <!--<td align="right"><?php echo number_format(($cnt_2[sum] / (($d) * 12)) / $rstMaq[ext_productividad] * 100, 2) . '%' ?></td>-->
                            <td align="center" colspan="4"><?php echo number_format(($cant[sum] / (($d) * 24)) / $rstMaq[maq_d] * 100, 2) . '%' ?></td>
                            <?php
                            $tot = $tot + round(($cant[sum] / (($d) * 24)) / $rstMaq[maq_d] * 100, 2);
                        }
                        ?> 
                        <td align="right" ><?php echo number_format($tot / $div, 2) . '%' ?></td>
                    </tr>
                    <tr>
                        <!--//********************************************************************-->        
                        <td colspan="24">Dias Laborados: <?php echo $d ?></td>
                        <?php
                    } else {
//                            
                        ?>   
                        <td>No existen EXTRUSORAS en la seccion <?php echo $rstSec['sec_descricpion'] ?></td>
                        <?php
                    }
//                        
                    ?>
                </tr>
            </table>
        </td>
    </tr>
    <?php
}

function rptRepRecResumen($from, $until, $title = 'REPORTE ACUMULADO') {
    $Maq = new Maquinas();
    $Mp = new MateriaPrima();
    $d = dias($from, $until);
    ?>
    <tr>
        <td>
            <table align="left" id="dataTable" width="100%" border="1">
                <thead >
                    <tr>
                        <th colspan="11" align="center" > <?php echo$title . '  DE PRODUCCION DE RECICLADO Y REPROCESADO' ?></th>
                    </tr>
                    <tr>
                        <th colspan="2" align="left" >DESDE: <?php echo $from ?></th>
                        <th colspan="2" align="left" >HASTA: <?php echo $until ?></th>
                        <th colspan="7" align="left" ></th>
                    </tr>
                    <tr>
                        <th colspan="4"></th>
                        <th> CONSUMO</th>
                        <th colspan="4">PRODUCCION DE RECICLADO (KG)</th>
                        <th colspan="2">PERDIDA</th>
                    </tr>
                    <tr>
                        <th>DIAS</th>
                        <th>SECC</th>
                        <th>COD</th>
                        <th>DESCRIPCION</th>
                        <th>DESP</th>
                        <th>REP/REC</th>
                        <th>DUROS</th>
                        <th>TOTAL</th>
                        <th>PROM/MAQ</th>
                        <th>KG</th>
                        <th>%</th>
                    </tr>
                </thead>
                <?php
                $cnsRec = $Maq->listaRecicladoras0();
                $td = 0;
                $tr = 0;
                $tdr = 0;
                $tot0 = 0;
                $prom0 = 0;
                $lost0 = 0;
                while ($rstRec = pg_fetch_array($cnsRec)) {
                    $n = 0;
                    $cnd = 0;
                    while ($n <= intval($d)) {
                        $d1 = explode('/', $from);
                        $f1 = date_create($d1[0] . '-' . $d1[1] . '-' . $d1[2]);
                        date_add($f1, date_interval_create_from_date_string($n . ' days'));
                        $dt = date_format($f1, 'd/m/Y');
                        $rst = pg_fetch_array($Mp->listRecByDateMaq($rstRec[rec_id], 4, $dt, $dt));
                        if (!empty($rst[sum])) {
                            $cnd++;
                        }
                        $n++;
                    }
                    $cnsD = pg_fetch_array($Mp->listDespConsByDateMaq($rstRec[rec_id], 4, $from, $until));
                    $cnsR = pg_fetch_array($Mp->listRecByDateMaq($rstRec[rec_id], 4, $from, $until));
                    $cnsDr = pg_fetch_array($Mp->listDespDurosByDateMaq($rstRec[rec_id], 4, $from, $until)); //Duros
                    $tot = $cnsR[sum] + $cnsDr[sum];
                    if ($cnd == 0) {
                        $div = 1;
                    } else {
                        $div = $cnd;
                    }
                    $prom = $tot / $div;
                    $lost = $cnsD[sum] - $tot;
                    $lostp = ($lost * 100) / $cnsD[sum];
                    ?>
                    <tr>
                        <td><?php echo $cnd ?></td>
                        <td><?php echo $rstRec[sec_nombre] ?></td>
                        <td><?php echo $rstRec[rec_codigo] ?></td>
                        <td><?php echo $rstRec[rec_marca] ?></td>
                        <td align="right"><?php echo number_format($cnsD[sum], 1) ?></td>
                        <td align="right"><?php echo number_format($cnsR[sum], 1) ?></td>
                        <td align="right"><?php echo number_format($cnsDr[sum], 1) ?></td>
                        <td align="right"><?php echo number_format($tot, 1) ?></td>
                        <td align="right"><?php echo number_format($prom, 1) ?></td>
                        <td align="right"><?php echo number_format($lost, 1) ?></td>
                        <td align="right"><?php echo number_format($lostp, 1) ?></td>
                    </tr>
                    <?php
                    $td = $td + $cnsD[sum];
                    $tr = $tr + $cnsR[sum];
                    $tdr = $tdr + $cnsDr[sum];
                    $tot0 = $tot0 + $tot;
                    $prom0 = $prom0 + $cnsD[sum];
                    $lost0 = $lost0 + $cnsD[sum];
                }
                ?>
                <td colspan="4">TOTAL</td>
                <td align="right"><?php echo number_format($td) ?></td>
                <td align="right"><?php echo number_format($tr) ?></td>
                <td align="right"><?php echo number_format($tdr) ?></td>
                <td align="right"><?php echo number_format($tot0) ?></td>
                <td align="right"><?php echo number_format($prom0) ?></td>
                <td align="right"><?php echo number_format($lost0) ?></td>
                <td><?php echo '-' ?></td>
    </tr>
    </table>
    </td>
    </tr>
    <?php
}

function rptDespRecDet($from, $until, $title = 'REPORTE ACUMULADO') {
    $Maq = new Maquinas();
    $Mp = new MateriaPrima();
    ?>
    <tr>
        <td>
            <table align="left" id="dataTable" width="100%" border="1">
                <thead >
                    <tr>
                        <th colspan="9" align="center" > <?php echo $title . ' DE CONSUMO DE DESPERDICIO POR MAQUINA' ?></th>
                    </tr>
                    <tr>
                        <th colspan="2" align="left" >DESDE: <?php echo $from ?></th>
                        <th colspan="2" align="left" >HASTA: <?php echo $until ?></th>
                        <th colspan="5" align="left" ></th>
                    </tr>
                    <tr>
                        <th colspan="3">MAQUINA</th>
                        <th colspan="3">DESPERDICIO</th>
                        <th colspan="3">RECICLADO</th>
                    </tr>
                    <tr>
                        <th>SECC</th>
                        <th>COD</th>
                        <th>DESCRIPCION</th>
                        <th>COD</th>
                        <th>DESCRIPCION</th>
                        <th>CANT</th>
                        <th>COD</th>
                        <th>DESCRIPCION</th>
                        <th>CANT</th>
                    </tr>
                </thead >
                <tr>
                    <?php
                    $cnsRec = $Maq->listaRecicladoras0();
                    $t_d = 0;
                    $t_r = 0;
                    while ($rstRec = pg_fetch_array($cnsRec)) {
                        $cnsD = $Mp->listDespConsByProdDateMaq('and dr.cmp_destino_maq=' . $rstRec[rec_id], 4, $from, $until);
                        $cnsR = $Mp->listRecProdByDateMaq('and dr.cmp_procedencia_maq =' . $rstRec[rec_id], 4, $from, $until);
                        if (pg_num_rows($cnsD) > pg_num_rows($cnsR)) {
                            $mlt = pg_num_rows($cnsD);
                        } else {
                            $mlt = pg_num_rows($cnsR);
                        }
                        if ($mlt == 0) {
                            $mlt = 1;
                        }
                        $arrD = array();
                        while ($rstD = pg_fetch_array($cnsD)) {
                            array_push($arrD, array($rstD[mat_prim_codigo], $rstD[mat_prim_nombre], $rstD[sum]));
                        }
                        $arrR = array();
                        while ($rstR = pg_fetch_array($cnsR)) {
                            array_push($arrR, array($rstR[mat_prim_codigo], $rstR[mat_prim_nombre], $rstR[sum]));
                        }
                        ?>

                        <td><?php echo $rstRec[sec_nombre] ?></td>
                        <td><?php echo $rstRec[rec_codigo] ?></td>
                        <td><?php echo $rstRec[rec_marca] ?></td>
                        <?php
                        $n = 0;
                        $td = 0;
                        $tr = 0;
                        while ($n < $mlt) {
                            $td = $td + $arrD[$n][2];
                            $tr = $tr + $arrR[$n][2];
                            $t_d = $t_d + $arrD[$n][2];
                            $t_r = $t_r + $arrR[$n][2];
                            if ($n > 0) {
                                ?>
                                <td></td>
                                <?php
                            }
                            ?>
                            <td><?php echo $arrD[$n][0] ?></td>
                            <td><?php echo $arrD[$n][1] ?></td>
                            <td align="right"><?php echo number_format($arrD[$n][2]) ?></td>
                            <td><?php echo $arrR[$n][0] ?></td>
                            <td><?php echo $arrR[$n][1] ?></td>
                            <td align="right"><?php echo number_format($arrR[$n][2]) ?></td>
                            <?php
                            if (($n + 1) != $mlt) {
                                ?>
                            </tr>
                            <?php
                        }
                        $n++;
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td align="right"><?php echo number_format($td) ?></td>
                        <td></td>
                        <td></td>
                        <td align="right"><?php echo number_format($tr) ?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="3">TOTAL</td>
                    <td colspan="2"></td>
                    <td align="right"><?php echo number_format($t_d) ?></td>
                    <td colspan="2"></td>
                    <td align="right"><?php echo number_format($t_r) ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <?php
}

function rptConsumoDespDetalle($from, $until, $title = 'REPORTE ACUMULADO') {
    $Seccion = new Secciones();
    $Maq = new Maquinas();
    $Mp = new MateriaPrima();
    $numc = pg_num_rows($Seccion->listaSeccionesPolietileno());
    ?>
    <tr>
        <td>
            <table align="left" id="dataTable" width="100%" border="1">
                <thead >
                    <tr>
                        <th colspan="<?php echo $numc + 4 ?>" align="center" > <?php echo $title . ' DE CONSUMO DE DESPERDICIO POR SECCION' ?></th>
                    </tr>
                    <tr>
                        <th colspan="2" align="left" >DESDE: <?php echo $from ?></th>
                        <th colspan="2" align="left" >HASTA: <?php echo $until ?></th>
                        <th colspan="<?php echo $numc ?>" align="left" ></th>
                    </tr>
                    <tr>
                        <th colspan="3">MAQUINA</th>
                        <th colspan="<?php echo $numc + 1 ?>  ">SECCION</th>
                    </tr>
                    <tr>
                        <th>SEC</th>
                        <th>COD</th>
                        <th>DESCRIPCION</th>
                        <?php
                        $cnsSec = $Seccion->listaSeccionesPolietileno();
                        while ($rstSec = pg_fetch_array($cnsSec)) {
                            ?> 
                            <th> <?php echo $rstSec[sec_nombre] ?></th>
                            <?php
                        }
                        ?> 
                        <th>TOT</th>
                    </tr>
                    <?php
                    $cnsRec = $Maq->listaRecicladoras0();
                    $t = 0;
                    while ($rstRec = pg_fetch_array($cnsRec)) {
                        ?>
                        <tr>
                            <td><?php echo $rstRec[sec_nombre] ?></td>
                            <td><?php echo $rstRec[rec_codigo] ?></td>
                            <td><?php echo $rstRec[rec_marca] ?></td>
                            <?php
                            $cnsSec = $Seccion->listaSeccionesPolietileno();
                            $t0 = 0;
                            while ($rstSec = pg_fetch_array($cnsSec)) {
                                $rst = pg_fetch_array($Mp->listConsDespByRecSec($rstSec[sec_id], 'and    cmp_destino_maq=' . $rstRec[rec_id], $from, $until));
                                $t0 = $t0 + $rst[sum];
                                $t = $t + $rst[sum];
                                ?>
                                <td align="right"><?php echo number_format($rst[sum], 1) ?></td>
                                <?php
                            }
                            ?>
                            <td align="right"><?php echo number_format($t0) ?></td>
                        </tr>
                        <?php
                    }
                    ?>

                    <tr>
                        <td colspan="3"></td>
                        <?php
                        $cnsSec = $Seccion->listaSeccionesPolietileno();
                        while ($rstSec = pg_fetch_array($cnsSec)) {
                            $rst = pg_fetch_array($Mp->listConsDespByRecSec($rstSec[sec_id], '', $from, $until));
                            $t1 = $t1 + $rst[sum];
                            ?>
                            <td align="right"><?php echo number_format($rst[sum], 1) ?></td>
                            <?php
                        }
                        ?>
                        <td align="right"><?php echo number_format($t1) ?></td>
                    </tr>
            </table>
        </td>
    </tr>
    <?php
}

function rptImpresionResumen($sec, $from, $until) {
    $Seccion = new Secciones();
    $Maq = new Maquinas();
    $Objeto = new Impresion();
    $Mp = new MateriaPrima();
    $rstSec = pg_fetch_array($Seccion->listaUnaSecciones($sec));
    $cnsMaq = $Maq->listaImpresorasSeccion($sec);
    $cnsMaq2 = $Maq->listaImpresorasSeccion($sec);
    if (pg_num_rows($cnsMaq) > 0) {
//Head***************************************************************************    
        ?>
        <tr>
            <td>
                <table align="left" id="dataTable" width="100%" border="1">
                    <thead >
                        <tr>
                            <th colspan="12" align="center" > <?php echo 'SECCION ' . $rstSec['sec_descricpion'] ?></th>
                        </tr>
                        <tr>
                            <th colspan="12" align="center" >REPORTE DE PRODUCCION - RESUMEN OPERATIVO</th>
                        </tr>
                        </tr>
                        <tr>
                            <th colspan="12" align="center" >DEPARTAMENTO DE IMPRESION</th>
                        </tr>
                        <tr>
                            <th colspan="2" align="left" >Desde: <?php echo $from ?></th>
                            <th colspan="2" align="left" >Hasta:<?php echo $until ?></th>
                            <th colspan="8" align="left" ></th>
                        </tr>
                        <tr>
                            <th rowspan="2">FECHA</th>
                            <th rowspan="2" colspan="1">DATOS</th>
                            <?php
                            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                                ?>
                                <th colspan="3"><?php echo $rstMaq[imp_descripcion] ?></th>
                                <?php
                            }
                            ?>
                            <th rowspan="2">TOTAL</th>
                        </tr>
                        <tr>
                            <?php
                            while ($rstMaq = pg_fetch_array($cnsMaq2)) {
                                ?> 
                                <th>T1</th>
                                <th>T2</th>
                                <th>SUMA</th>
                                <?php
                            }
                            ?> 
                        </tr>

                        <?php
                        //Body***************************************************************************        
                        $d = (intval(dias($from, $until)) + 1);
                        $dnolab = 0;
                        $n = 0;
                        while ($n < $d) {
                            $d1 = explode('/', $from);
                            $f1 = date_create($d1[0] . '-' . $d1[1] . '-' . $d1[2]);
                            date_add($f1, date_interval_create_from_date_string($n . ' days'));
                            $dt = date_format($f1, 'd/m/Y');
                            //Consumo Rollos***************************************************************
                            ?>
                            <tr>
                                <td rowspan="5"><?php echo $dt ?></td>
                                <td>Consumo Rollos (kg)</td>
                                <?php
                                $cnsMaq = $Maq->listaImpresorasSeccion($sec);
                                $tot = 0;
                                $date = $dt;
                                $produccion = pg_fetch_array($Objeto->listaImpresionProduccionByDateSec($sec, $date, $date));
                                while ($rstMaq = pg_fetch_array($cnsMaq)) {
                                    $cant = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDate($rstMaq[imp_id], $date, $date));
                                    $cnt_1 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $date, $date, '1'));
                                    $cnt_2 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $date, $date, '2'));
                                    ?>
                                    <td align="right"><?php echo number_format($cnt_1[peso]) ?></td>
                                    <td align="right"><?php echo number_format($cnt_2[peso]) ?></td>
                                    <td align="right"><?php echo number_format($cant[peso]) ?></td>
                                    <?php
                                    $tot = $tot + $cant[peso];
                                }
                                ?>
                                <td align="right"><?php echo number_format($tot) ?></td>
                            </tr>
                            <tr>
                                <!--//Consumo Tintas*********************************************************************-->        

                                <td>Consumo Tintas (kg)</td>
                                <?php
                                $cnsMaq = $Maq->listaImpresorasSeccion($sec);
                                while ($rstMaq = pg_fetch_array($cnsMaq)) {
                                    ?>  
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <?php
                                }
                                ?> 
                                <td align="right"><?php echo number_format($consumiTintas) ?></td>        
                            </tr>
                            <tr>
                                <!--//Produccion kg***************************************************************-->        
                                <td>Produccion (kg)</td>
                                <?php
                                $cnsMaq = $Maq->listaImpresorasSeccion($sec);
                                $tot = 0;
                                $val = 0;
                                while ($rstMaq = pg_fetch_array($cnsMaq)) {
                                    $val++;
                                    if ($produccion[sum] > 1) {
                                        $cant = pg_fetch_array($Objeto->listaProduccionByMaqDate($rstMaq[imp_id], $date, $date));
                                        $cnt_1 = pg_fetch_array($Objeto->listaProduccionByMaqDateTurn($rstMaq[imp_id], $date, $date, '1'));
                                        $cnt_2 = pg_fetch_array($Objeto->listaProduccionByMaqDateTurn($rstMaq[imp_id], $date, $date, '2'));
                                        ?>
                                        <td align="right"><?php echo number_format($cnt_1[sum]) ?></td>
                                        <td align="right"><?php echo number_format($cnt_2[sum]) ?></td>
                                        <td align="right"><?php echo number_format($cant[sum]) ?></td>
                                        <?php
                                        $tot = $tot + $cant[sum];
                                    } else {
                                        if ($val == 2) {
                                            $txt = 'NO SE TRABAJA';
                                            $dnolab++;
                                            $colspan = '7';
                                        } ELSE {
                                            $txt = '';
                                            $colspan = '1';
                                        }
                                        ?>
                                        <td colspan="<?php echo $colspan ?>"><?php echo $txt ?></td> 

                                        <?php
                                    }
                                }
                                ?>
                                <td align="right"><?php echo number_format($tot) ?></td>
                            </tr>

                            <!--//Produccion mts***************************************************************-->

                        <td>Produccion (mt)</td>
                        <?php
                        $cnsMaq = $Maq->listaImpresorasSeccion($sec);
                        $tot = 0;
                        $date = $dt;
                        $produccion = pg_fetch_array($Objeto->listaImpresionProduccionByDateSec($sec, $date, $date));
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $cant = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDate($rstMaq[imp_id], $date, $date));
                            $cnt_1 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $date, $date, '1'));
                            $cnt_2 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $date, $date, '2'));
                            ?>
                            <td align="right"><?php echo number_format($cnt_1[metros] * 1000) ?> </td>
                            <td align="right"><?php echo number_format($cnt_2[metros] * 1000) ?> </td>
                            <td align="right"><?php echo number_format($cant[metros] * 1000) ?> </td>
                            <?php
                            $tot = $tot + ($cant[metros] * 1000);
                        }
                        ?>
                        <td align="right"><?php echo number_format($tot) ?></td>
            </tr>
            <!--//Desp kg***************************************************************************-->
            <tr>
                <td>Desperdicio (kg)</td>
                <?php
                $cnsMaq = $Maq->listaImpresorasSeccion($sec);
                $tot = 0;
                $date = $dt;
                $produccion = pg_fetch_array($Objeto->listaImpresionProduccionByDateSec($sec, $date, $date));
                while ($rstMaq = pg_fetch_array($cnsMaq)) {
                    $cant = pg_fetch_array($Mp->listDespImpByMaq($rstMaq[imp_id], $date, $date));
                    $cnt_1 = pg_fetch_array($Mp->listDespImpByMaqTurn($rstMaq[imp_id], $date, $date, '1'));
                    $cnt_2 = pg_fetch_array($Mp->listDespImpByMaqTurn($rstMaq[imp_id], $date, $date, '2'));
                    ?>
                    <td align="right"><?php echo number_format($cnt_1[sum]) ?> </td>
                    <td align="right"><?php echo number_format($cnt_2[sum]) ?> </td>
                    <td align="right"><?php echo number_format($cant[sum]) ?> </td>
                    <?php
                    $tot = $tot + ($cant[sum]);
                }
                ?>
                <td align="right"><?php echo number_format($tot) ?> </td>
            </tr>
            <?php
            $n++;
        }
        $d = ($d - $dnolab);
//Totales kg***************************************************************        
//Total Consumo de Rollos ***************************************************************
        ?>
        <tr>
            <td colspan="2">TOTAL COSUMO ROLLOS POR MAQ (kg)</td>
            <?php
            $cnsMaq = $Maq->listaImpresorasSeccion($sec);
            $tot = 0;
            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                $cant = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDate($rstMaq[imp_id], $from, $until));
                $cnt_1 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $from, $until, '1'));
                $cnt_2 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $from, $until, '2'));
                ?>
                <td align = "right"><?php echo number_format($cnt_1[peso]) ?></td>
                <td align = "right"><?php echo number_format($cnt_2[peso]) ?></td>
                <td align = "right"><?php echo number_format($cant[peso]) ?></td>
                <?php
                $tot = $tot + $cant[peso];
            }
            ?>
            <td align = "right"><?php echo number_format($tot) ?> </td>
        </tr>
        <tr>

            <!--//Total Consumo Tintas ***************************************************************-->  

            <td colspan="2">TOTAL COSUMO TINTAS POR MAQ (kg)</th>
            <?php
            $cnsMaq = $Maq->listaImpresorasSeccion($sec);
            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                ?>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <?php
        }
        ?>
        <td align = "right"><?php echo number_format($consumiTintas) ?></td>                        
        </tr>
        <!--//Total Produccion / maq kg***************************************************************-->        
        <tr>
            <td colspan="2">TOTAL PRODUCCION POR MAQ (kg) </td>
            <?php
            $cnsMaq = $Maq->listaImpresorasSeccion($sec);
            $tot = 0;
            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                $cant = pg_fetch_array($Objeto->listaProduccionByMaqDate($rstMaq[imp_id], $from, $until));
                $cnt_1 = pg_fetch_array($Objeto->listaProduccionByMaqDateTurn($rstMaq[imp_id], $from, $until, '1'));
                $cnt_2 = pg_fetch_array($Objeto->listaProduccionByMaqDateTurn($rstMaq[imp_id], $from, $until, '2'));
                ?>
                <td align = "right"><?php echo number_format($cnt_1[sum]) ?></td>
                <td align = "right"><?php echo number_format($cnt_2[sum]) ?></td>
                <td align = "right"><?php echo number_format($cant[sum]) ?></td>
                <?php
                $tot = $tot + $cant[sum];
            }
            ?>
            <td align = "right"><?php echo number_format($tot) ?> </td>
        </tr>
        <tr>
            <!--//Total Produccion / maq mts**************************************************************-->        
            <td colspan="2">TOTAL PRODUCCION POR MAQ (m) </td>
            <?php
            $cnsMaq = $Maq->listaImpresorasSeccion($sec);
            $tot = 0;
            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                $cant = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDate($rstMaq[imp_id], $from, $until));
                $cnt_1 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $from, $until, '1'));
                $cnt_2 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $from, $until, '2'));
                ?>
                <td align = "right"><?php echo number_format($cnt_1[metros] * 1000) ?> </td>
                <td align = "right"><?php echo number_format($cnt_2[metros] * 1000) ?> </td>
                <td align = "right"><?php echo number_format($cant[metros] * 1000) ?> </td>
                <?php
                $tot = $tot + ($cant[metros] * 1000);
            }
            ?>
            <td align = "right"><?php echo number_format($tot) ?> </td>
        </tr>
        <tr>
            <!--////Total desperdicio**************************************************************-->        
            <td colspan="2">TOTAL DESPERDICIO (kg) </td>
            <?php
            $cnsMaq = $Maq->listaImpresorasSeccion($sec);
            $tot = 0;
            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                $cant = pg_fetch_array($Mp->listDespImpByMaq($rstMaq[imp_id], $from, $until));
                $cnt_1 = pg_fetch_array($Mp->listDespImpByMaqTurn($rstMaq[imp_id], $from, $until, '1'));
                $cnt_2 = pg_fetch_array($Mp->listDespImpByMaqTurn($rstMaq[imp_id], $from, $until, '2'));
                ?>
                <td align = "right"><?php echo number_format($cnt_1[sum]) ?> </td>
                <td align = "right"><?php echo number_format($cnt_2[sum]) ?> </td>
                <td align = "right"><?php echo number_format($cant[sum]) ?> </td>
                <?php
                $tot = $tot + ($cant[sum]);
            }
            ?>
            <td align = "right"><?php echo number_format($tot) ?></td>
        </tr>
        <!--//Capacidad Teorica**************************************************************-->        
        <tr>
            <td colspan="2">CAPACIDAD TEORICA POR MAQ (m/min) </td>
            <?php
            $cnsMaq = $Maq->listaImpresorasSeccion($sec);
            $tot = 0;
            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                ?>    
                <td>-</td>
                <td>-</td>
                <td align = "right"><?php echo number_format($rstMaq[imp_velocidad]) ?> </td>
                <?php
                $tot = $tot + $rstMaq[imp_velocidad];
            }
            ?>
            <td align = "right"><?php echo number_format($tot) ?></td>
        </tr>
        <tr>
            <!--//Capacidad Real**************************************************************-->        
            <td colspan="2">CAPACIDAD REAL POR MAQ (m/min) </td>
            <?php
            $cnsMaq = $Maq->listaImpresorasSeccion($sec);
            $tot = 0;
            $cap = 0;
            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                $cant = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDate($rstMaq[imp_id], $from, $until));
                $cnt_1 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $from, $until, '1'));
                $cnt_2 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $from, $until, '2'));
                $cap = ($cant[metros] * 1000) / ($d * 24 * 60);
                $cap1 = ($cnt_1[metros] * 1000) / ($d * 12 * 60);
                $cap2 = ($cnt_2[metros] * 1000) / ($d * 12 * 60);
                ?>
                <td align = "right"><?php echo number_format($cap1) ?> </td>
                <td align = "right"><?php echo number_format($cap2) ?> </td>
                <td align = "right"><?php echo number_format($cap) ?> </td>
                <?php
                $tot = $tot + ($cap);
            }
            ?>
            <td align = "right"><?php echo number_format($tot) ?> </td>
        </tr>
        <tr>

            <!--//EFICIENCIA POR CAPACIDAD**************************************************************-->        
            <td colspan="2">EFICIENCIA POR CAPACIDAD </td>
            <?php
            $cnsMaq = $Maq->listaImpresorasSeccion($sec);
            $tot = 0;
            $cap = 0;
            $div = 0;
            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                $div++;
                $cant = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDate($rstMaq[imp_id], $from, $until));
                $cnt_1 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $from, $until, '1'));
                $cnt_2 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $from, $until, '2'));
                $cap = ($cant[metros] * 1000) / ($d * 24 * 60);
                $cap1 = ($cnt_1[metros] * 1000) / ($d * 12 * 60);
                $cap2 = ($cnt_2[metros] * 1000) / ($d * 12 * 60);
                ?>
                <td align = "right"><?php echo number_format($cap1 / $rstMaq[imp_velocidad], 2) ?> </td>
                <td align = "right"><?php echo number_format($cap2 / $rstMaq[imp_velocidad], 2) ?> </td>
                <td align = "right"><?php echo number_format($cap / $rstMaq[imp_velocidad], 2) ?> </td>
                <?php
                $tot = $tot + ($cap / $rstMaq[imp_velocidad]);
            }
            ?>
            <td align = "right"><?php echo number_format($tot / $div, 2) ?></td>
        </tr>
        <tr>
            <!--////EFICIENCIA**************************************************************-->        
            <td colspan="2">EFICIENCIA </td>
            <?php
            $cnsMaq = $Maq->listaImpresorasSeccion($sec);
            $tot = 0;
            $cap = 0;
            $div = 0;
            while ($rstMaq = pg_fetch_array($cnsMaq)) {
                $div++;
                $cant = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDate($rstMaq[imp_id], $from, $until));
                $cnt_1 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $from, $until, '1'));
                $cnt_2 = pg_fetch_array($Objeto->listaImpresionConsumoRollosByMaqDateTurn($rstMaq[imp_id], $from, $until, '2'));
                $cap = ($cant[metros] * 1000) / ($d * 24 * 60);
                $cap1 = ($cnt_1[metros] * 1000) / ($d * 12 * 60);
                $cap2 = ($cnt_2[metros] * 1000) / ($d * 12 * 60);
                ?>
                <td align = "right"><?php echo number_format($cap1 / $rstMaq[imp_velocidad] * 100, 2) . '%' ?> </td>
                <td align = "right"><?php echo number_format($cap2 / $rstMaq[imp_velocidad] * 100, 2) . '%' ?> </td>
                <td align = "right"><?php echo number_format($cap / $rstMaq[imp_velocidad] * 100, 2) . '%' ?> </td>
                <?php
                $tot = $tot + ($cap / $rstMaq[imp_velocidad] * 100);
            }
            ?>
            <td align = "right"><?php echo number_format($tot / $div, 2) . '%' ?></td>
        </tr>
        <tr>
            <!--////********************************************************************        -->
            <td colspan="2">Dias Laborados: <?php echo $d ?></td>
            <?php
        } else {
            ?>
            <td>No existen IMPRESORAS en la seccion <?php echo $rstSec['sec_descricpion'] ?></td>
            <?php
        }
        ?>
    </tr>
    </table>
    <?php
}

function rptsSelladoResumen($sec, $from, $until) {
    $Set = new Produccion_reportes();
//    $Seccion = new Secciones();
//    $Maq = new Maquinas();
//    $Objeto = new Sellado();
//    $Mp = new MateriaPrima();
//    $rstSec = pg_fetch_array($Seccion->listaUnaSecciones($sec));
    $cnsMaq = $Set->listaCortadoras($sec);
    $cnsMaq1 = $Set->listaCortadoras($sec);
    $cnsMaq2 = $Set->listaCortadoras($sec);
    if (pg_num_rows($cnsMaq) > 0) {
        //Head***************************************************************************    
        ?>
        <table align="left" id="tbl" width="100%" border="1">
            <thead >
                <tr>
                    <th colspan="<?php echo 3+pg_num_rows($cnsMaq) * 4 ?>" align="center" >POLIPACK</th>
                </tr>
                <tr>
                    <th colspan="<?php echo 3+pg_num_rows($cnsMaq) * 4 ?>" align="center" > REPORTE DE PRODUCCION - RESUMEN OPERATIVO</th>
                </tr>
                <tr>
                    <th colspan="<?php echo 1+pg_num_rows($cnsMaq) * 4 ?>" align="left" >DEPARTAMENTO DE BOBINADO/CORTE</th>
                    <th colspan="2"> <?php echo 'Desde: ' . $from . '   Hasta: ' . $until ?></th>
                    <!--<th colspan="<?php echo pg_num_rows($cnsMaq) - 3 ?>"></th>-->
                </tr>
                <tr>
                    <th rowspan="3">FECHA</th>
                    <th rowspan="3">DATOS</th>
                    <?php
                    while ($rstMaq = pg_fetch_array($cnsMaq)) {
                        ?>
                        <th style="width: 100px" colspan="4"><?php echo $rstMaq[maq_a] ?></th>
                        <?php
                    }
                    ?>
                    <th style="width: 100px" rowspan="3">TOTAL</th>
                </tr>
                <tr>
                    <?php
                    while ($rstMaq1 = pg_fetch_array($cnsMaq1)) {
                        ?>
                        <th style="width: 100px" colspan="2">CONFORME</th>
                        <th style="width: 100px" colspan="2">INCONFORME</th>
                        <?php
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    while ($rstMaq2 = pg_fetch_array($cnsMaq2)) {
                        ?>
                        <th style="width: 100px">PESO NETO</th>
                        <th style="width: 100px">PESO BRUTO</th>
                        <th style="width: 100px">PESO NETO</th>
                        <th style="width: 100px">PESO BRUTO</th>
                        <?php
                    }
                    ?>
                </tr>
            </thead>
            <?php
            //Body***************************************************************************        
            $d = (intval(dias($from, $until)) + 1);
            $dnolab = 0;
            $n = 0;
            while ($n < $d) {
                $d1 = explode('-', $from);
                $f1 = date_create($d1[0] . '-' . $d1[1] . '-' . $d1[2]);
                date_add($f1, date_interval_create_from_date_string($n . ' days'));
                $dt = date_format($f1, 'd/m/Y');
                $date = $dt;
                //Consumo Rollos***************************************************************
                ?>
                                    <!--                    <tr>
                                                    <td rowspan="6"><?php echo $dt ?></td>
                                                    <td>Produccion Turno </td>
                <?php
//                        $cnsMaq = $Set->listaCortadoras($sec);
//                        $tot = 0;
//                        $date = $dt;
//                        $produccion = pg_fetch_array($Objeto->listaProduccionByDateSecTurno($date, $date, $sec));
//                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
//                            $cant = pg_fetch_array($Objeto->listaProduccionByDateMaqTurno($date, $date, $rstMaq[sell_id], 1));
//                            
                ?>
                                                        <td align="right">//<?php echo number_format($cant[rollos]) ?></td>
                                                        //<?php
//                            $tot = $tot + ($cant[fundas] + $cant[rollos]);
//                        }
                ?>
                                                    <td align="right"><?php echo number_format($tot, 2) ?></td>
                                                </tr>
                                                <tr>
                                                    //Produccion T1***************************************************************
                                                    <td>Produccion Turno 2</td>
                <?php
//                        $cnsMaq = $Maq->listaSelladorasSeccion($sec);
//                        $tot = 0;
//                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
//                            $cant = pg_fetch_array($Objeto->listaProduccionByDateMaqTurno($date, $date, $rstMaq[sell_id], 2));
//                            
                ?>
                                                        <td align="right">//<?php echo number_format($cant[fundas] + $cant[rollos], 1) ?></td>
                                                        //<?php
//                            $tot = $tot + ($cant[fundas] + $cant[rollos]);
//                        }
//                        
                ?>
                                                    <td align="right"><?php echo number_format($tot, 2) ?></td>
                                                </tr>-->
                <tr>
                    <!--//Produccion Fundas/Rollos***************************************************************-->
                    <td rowspan="2"><?php echo $dt ?></td>
                    <td>Produccion Rollos </td>
                    <?php
                    $cnsMaq = $Set->listaCortadoras($sec);
                    $tot = 0;
                    while ($rstMaq = pg_fetch_array($cnsMaq)) {
                        $cant = pg_fetch_array($Set->listaProduccionByDateMaq($date, $date, $rstMaq[id]));
                        $cnt_c = pg_fetch_array($Set->listaProduccionByDateMaqEst($date, $date, $rstMaq[id],'0'));
                        $cnt_i = pg_fetch_array($Set->listaProduccionByDateMaqEst($date, $date, $rstMaq[id],'3'));
                        ?>
                    <!--<td align="right" colspan="2"><?php echo number_format($cant[rollos], 2) ?></td>-->
                    <td align="center" colspan="2"><?php echo number_format($cnt_c[rollos], 2) ?></td>
                    <td align="center" colspan="2"><?php echo number_format($cnt_i[rollos], 2) ?></td>
                        <?php
                        $tot = $tot + ($cant[rollos]);
                    }
                    ?>
                    <td align="right"><?php echo number_format($tot, 2) ?></td>
                </tr>
                <!--                    <tr>
                    //Produccion Mist print***************************************************************
                    <td>Produccion Fundas Mist Print </td>
                <?php
//                        $cnsMaq = $Maq->listaSelladorasSeccion($sec);
//                        $tot = 0;
//                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
//                            $cant = pg_fetch_array($Objeto->listaProduccionByDateMaq($date, $date, $rstMaq[sell_id]));
//                            
                ?>
                        <td align="right">//<?php echo number_format($cant[mstprint], 1) ?></td>
                        //<?php
//                            $tot = $tot + ($cant[mstprint]);
//                        }
                ?>  
                    <td align="right"><?php echo number_format($tot, 1) ?></td>
                </tr>-->
                <tr>
                    <!--//Produccion Peso***************************************************************-->
                    <td>Produccion Rollos peso(Kg)  </td>
                    <?php
                    $cnsMaq = $Set->listaCortadoras($sec);
                    $tot = 0;
                    while ($rstMaq = pg_fetch_array($cnsMaq)) {
                        $cant = pg_fetch_array($Set->listaProduccionByDateMaq($date, $date, $rstMaq[id]));
                        $cnt_c = pg_fetch_array($Set->listaProduccionByDateMaqEst($date, $date, $rstMaq[id],'0'));
                        $cnt_i = pg_fetch_array($Set->listaProduccionByDateMaqEst($date, $date, $rstMaq[id],'3'));
                        ?>
                        <!--<td align="right"><?php echo number_format($cant[peso], 2) ?></td>-->
                        <td align="right"><?php echo number_format($cnt_c[pneto], 2) ?></td>
                        <td align="right"><?php echo number_format($cnt_c[pbruto], 2) ?></td>
                        <td align="right"><?php echo number_format($cnt_i[pneto], 2) ?></td>
                        <td align="right"><?php echo number_format($cnt_i[pbruto], 2) ?></td>
                        <?php
                        $tot = $tot + ($cant[peso]);
                    }
                    ?>
                    <td align="right"><?php echo number_format($tot, 2) ?></td>
                </tr>
                <!--<tr>-->
                <!--//Produccion Mist Print***************************************************************-->
                <!--                        <td>Produccion Fundas Mist Print peso  </td>
                //<?php
//                        $cnsMaq = $Maq->listaSelladorasSeccion($sec);
//                        $tot = 0;
//                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
//                            $cant = pg_fetch_array($Objeto->listaProduccionByDateMaq($date, $date, $rstMaq[sell_id]));
//                            
                ?>
                    <td align="right">//<?php echo number_format($cant[mstprint], 1) ?></td>
                    //<?php
//                            $tot = $tot + ($cant[peso]);
//                        }
//                        
                ?>
                <td align="right"><?php echo number_format($tot, 1) ?></td>-->
                <!--</tr>-->
                
                    <?php
                    $n++;
                }
                $d = ($d - $dnolab);
            } else {
                ?>   
                <td>No existen CORTADORAS en la seccion <?php echo $rstSec['sec_descricpion'] ?></td> 
                <?php
            }
            ?> 
                <tr>
                    <!--//Total Produccion / maq kg***************************************************************-->        
                    <td colspan="2">PRODUCCION POR MAQ (Rollo)</td>
                    <?php
                    $cnsMaq = $Set->listaCortadoras($sec);
                    $tot = 0;
                    while ($rstMaq = pg_fetch_array($cnsMaq)) {
                        $cant = pg_fetch_array($Set->listaProduccionByDateMaq($from, $until,$rstMaq[id]));
                        $cnt_c = pg_fetch_array($Set->listaProduccionByDateMaqEst($from, $until,$rstMaq[id],'0'));
                        $cnt_i = pg_fetch_array($Set->listaProduccionByDateMaqEst($from, $until,$rstMaq[id], '3'));
                        ?>
                                <!--<td align="right"><?php echo number_format($cant[sum], 2) ?></td>-->
                    <td align="center" colspan="2"><?php echo number_format($cnt_c[rollos], 2) ?></td>
                        <td align="center" colspan="2"><?php echo number_format($cnt_i[rollos], 2) ?></td>
                        <?php
                        $tot = $tot + $cant[rollos];
                    }
                    ?>
                    <td align="right"><?php echo number_format($tot, 2) ?></td>
                </tr>
                <tr>
                    <!--//Total Produccion / maq mt***************************************************************-->        
                    <td colspan="2">PRODUCCION POR MAQ (kg)</td>
                    <?php
                    $cnsMaq = $Set->listaCortadoras($sec);
                    $tot = 0;
                    while ($rstMaq = pg_fetch_array($cnsMaq)) {
                        $cant = pg_fetch_array($Set->listaProduccionByDateMaq($from, $until,$rstMaq[id]));
                        $cnt_c = pg_fetch_array($Set->listaProduccionByDateMaqEst($from, $until,$rstMaq[id],'0'));
                        $cnt_i = pg_fetch_array($Set->listaProduccionByDateMaqEst($from, $until,$rstMaq[id], '3'));
                        ?>
                        <td align="right"><?php echo number_format($cnt_c[pneto], 2) ?></td>
                        <td align="right"><?php echo number_format($cnt_c[pbruto], 2) ?></td>
                        <td align="right"><?php echo number_format($cnt_i[pneto], 2) ?></td>
                        <td align="right"><?php echo number_format($cnt_i[pbruto], 2) ?></td>
                        <!--<td align="right"><?php echo number_format($cant[mts], 2) ?></td>-->
                        <?php
                        $tot = $tot + ($cant[peso]);
                    }
                    ?>
                    <td align="right"><?php echo number_format($tot, 2) ?></td>
                </tr><tr>
                    <!--//Capacidad Teorica**************************************************************-->        
                    <td colspan="2">CAPACIDAD TEORICA POR MAQ (kg/h)</td>
                    <?php
                    $cnsMaq = $Set->listaCortadoras($sec);
                    $tot = 0;
                    while ($rstMaq = pg_fetch_array($cnsMaq)) {
                        ?>   
                                        <!--                        <td>-</td>
                                                        <td>-</td>-->
                        <td align="center" colspan="4"><?php echo $rstMaq[maq_d] ?></td>
                        <?php
                        $tot = $tot + $rstMaq[maq_d];
                    }
                    ?> 
                    <td align="right"><?php echo number_format($tot) ?></td>
                </tr>
                <tr>
                    <!--//Capacidad Real**************************************************************-->        
                    <td colspan="2">CAPACIDAD REAL POR MAQ (kg/h)</td>
                    <?php
                    $cnsMaq = $Set->listaCortadoras($sec);
                    $tot = 0;
                    while ($rstMaq = pg_fetch_array($cnsMaq)) {
                        $cant = pg_fetch_array($Set->listaProduccionByDateMaq($from, $until,$rstMaq[id]));
                        ?> 
                        <td align="center" colspan="4"><?php echo number_format($cant[peso] / (($d) * 24)) ?></td>
                        <?php
                        $tot = $tot + ($cant[peso] / (($d) * 24));
                    }
                    ?> 
                    <td align="right"><?php echo number_format($tot) ?></td>
                </tr>
                <tr>
                    <!--//EFICIENCIA POR CAPACIDAD**************************************************************-->        
                    <td colspan="2">EFICIENCIA POR CAPACIDAD</td>
                    <?php
                    $cnsMaq = $Set->listaCortadoras($sec);
                    $tot = 0;
                    $div = 0;
                    while ($rstMaq = pg_fetch_array($cnsMaq)) {
                        $div++;
                        $cant = pg_fetch_array($Set->listaProduccionByDateMaq($from, $until,$rstMaq[id]));
                        ?> 
                        <td align="center" colspan="4"><?php echo number_format(($cant[peso] / (($d) * 24)) / $rstMaq[maq_d], 2) ?></td>
                        <?php
                        $tot = $tot + round(($cant[peso] / (($d) * 24)) / $rstMaq[maq_d], 2);
                    }
                    ?> 
                    <td align="right"><?php echo number_format($tot / $div, 2) ?></td>
                </tr>
                <tr>
                    <!--//EFICIENCIA**************************************************************-->    
                    <td colspan="2">EFICIENCIA</td>
                    <?php
                    $cnsMaq = $Set->listaCortadoras($sec);
                    $tot = 0;
                    $div = 0;
                    while ($rstMaq = pg_fetch_array($cnsMaq)) {
                        $div++;
                        $cant = pg_fetch_array($Set->listaProduccionByDateMaq($from, $until,$rstMaq[id]));
                        ?> 
                        <td align="center" colspan="4"><?php echo number_format(($cant[peso] / (($d) * 24)) / $rstMaq[maq_d] * 100, 2) . '%' ?></td>
                        <?php
                        $tot = $tot + round(($cant[peso] / (($d) * 24)) / $rstMaq[maq_d] * 100, 2);
                    }
                    ?> 
                    <td align="right" ><?php echo number_format($tot / $div, 2) . '%' ?></td>
                </tr>
                <tr>
                    <!--//********************************************************************-->        
                    <td colspan="24">Dias Laborados: <?php echo $d ?></td>

        </tr>
    </table>
    <?php
}
?>
</body>
