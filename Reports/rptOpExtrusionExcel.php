<?php
set_time_limit(0);
date_default_timezone_set('America/Guayaquil');
include_once '../Includes/permisos.php';
include_once("../Clases/clsProduccion_reportes.php");

//$sec = $_POST[sec_id];
//if ($_POST[extm] == 'on') {
//    rptExtrusionResumen($sec, $_POST['f_month_from'], $_POST['f_month_until']);
//}
$Set = new Produccion_reportes();

echo $_GET[search];
if (isset($_GET[fecha1],$_GET[fecha2])) {
    $from = $_GET[fecha1];
    $until = $_GET[fecha2];
} else {
    $from = date('Y-m-d');
    $until = date('Y-m-d');
}
$cnsMaq = $Set->listaExtrusoras($sec);
$cnsMaq2 = $Set->listaExtrusoras($sec);
$cnsMaq3 = $Set->listaExtrusoras($sec);
$cnsMaq4 = $Set->listaExtrusoras($sec);
function dias($from, $until) {
    $dias = (strtotime($from) - strtotime($until)) / 86400;
    $dias = abs($dias);
    $dias = floor($dias);
    return $dias;
}
?>
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

        </script> 
        <style>

            *{
                text-transform: uppercase;
            }
            input{
                background:#f8f8f8 !important; 
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >
                    REPORTE DE PRODUCCION - RESUMEN OPERATIVO<br><br>
                    DEPARTAMENTO DE EXTRUSION
                </center>
                <center class="cont_finder">
                    <!--                    <form id="exp_excel" style="float:right;margin-top:6px;padding:0px" method="post" action="../Includes/export.php?tipo=1" onsubmit="return exportar_excel()"  >
                                            <input type="submit" value="Excel" class="auxBtn" />
                                            <input type="hidden" id="datatodisplay" name="datatodisplay">
                                        </form>-->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        DESDE:<input type="text" name="fecha1" size="15" id="fecha1" value="<?php echo $from ?>"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" name="fecha2" size="15" id="fecha2" value="<?php echo $until ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" name="search" onclick="frmSearch.submit()">Buscar</button>
                    </form> 
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <?php
            $col = pg_num_rows($cnsMaq) * 4;
            if (pg_num_rows($cnsMaq) > 0) {
                //Head*************************************************************************** 
                ?>
                <thead >
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
//                        $d1 = explode('-', $from);
//                        $f1 = date_create($d1[0] . '-' . $d1[1] . '-' . $d1[2]);
                        $f1 = date_create($from);
                        date_add($f1, date_interval_create_from_date_string($n . ' days'));
                        $dt = date_format($f1, 'Y-m-d');
//                            $dt = $from;
                        //Consumo mp***************************************************************
                        ?>
                        <td rowspan="3"><?php echo $dt ?></td>
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
        <!--                        <tr>
                        //Produccion mt***************************************************************        
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
                                                                                                                        <td align="right"><?php echo number_format($cnt_t1[mt] * 1000) ?></td>
                                                                                                                        <td align="right"><?php echo number_format($cnt_t2[mt] * 1000) ?></td>
                                                                        <td align="right"><?php echo number_format($cnt_c[mts_neto], 2) ?></td>
                                                                        <td align="right"><?php echo number_format($cnt_c[mts_bruto], 2) ?></td>
                                                                        <td align="right"><?php echo number_format($cnt_i[mts_neto], 2) ?></td>
                                                                        <td align="right"><?php echo number_format($cnt_i[mts_bruto], 2) ?></td>
                                                                    <td align="right"><?php echo number_format($cant[mts], 2) ?></td>
                        <?php
                        $tot = $tot + ($cant[mts]);
                    }
                    ?>
                        <td align="right"><?php echo number_format($tot, 2) ?></td>
                    </tr>-->
                    <!--//Desp kg***************************************************************-->        
<!--                    <tr>
                        <td>Desperdicio (kg)</td>
                        <?php
                        $cnsMaq = $Set->listaExtrusoras($sec);
                        $tot = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $cant = pg_fetch_array($Set->listDesperdicio($rstMaq[id], $date, $date)); //Total Desperdicio de Extrusion
//                                $cnt_t1 = pg_fetch_array($Set->listDespExtrusionByMaqTur($rstMaq[ext_id], $date, $date, '1')); //Total Desperdicio de Extrusion
//                                $cnt_t2 = pg_fetch_array($Set->listDespExtrusionByMaqTur($rstMaq[ext_id], $date, $date, '2')); //Total Desperdicio de Extrusion
                            ?>
                                                                                                                            <td align="right"><?php echo number_format($cnt_t1[sum]) ?></td>
                                                                                                                <td align="right"><?php echo number_format($cnt_t2[sum]) ?></td>
                                                                                                                <td align="right"><?php echo number_format($cant[sum]) ?></td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <?php
                        }
                        ?>
                        <td align="right"><?php echo number_format($cant[sum], 2) ?></td>
                    </tr>-->
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
    <!--                    <tr>
                    //Total Produccion / maq mt***************************************************************        
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
                                                                                                            <td align="right"><?php echo number_format($cnt_t1[mt] * 1000) ?></td>
                                                                                                    <td align="right"><?php echo number_format($cnt_t2[mt] * 1000) ?></td>
                                                                    <td align="right"><?php echo number_format($cnt_c[mts_neto], 2) ?></td>
                                                                    <td align="right"><?php echo number_format($cnt_c[mts_bruto], 2) ?></td>
                                                                    <td align="right"><?php echo number_format($cnt_i[mts_neto], 2) ?></td>
                                                                    <td align="right"><?php echo number_format($cnt_i[mts_bruto], 2) ?></td>
                                                                    <td align="right"><?php echo number_format($cant[mts], 2) ?></td>
                    <?php
                    $tot = $tot + ($cant[mts]);
                }
                ?>
                    <td align="right"><?php echo number_format($tot, 2) ?></td>
                </tr>-->
<!--                <tr>
                    //Total desperdicio / maq kg***************************************************************        
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
                                                                                                            <td align="right"><?php echo number_format($cnt_t1[sum]) ?></td>
                                                                                                    <td align="right"><?php echo number_format($cnt_t2[sum]) ?></td>
                                                                                                    <td align="right"><?php echo number_format($cant[sum]) ?></td>
                        <td align="center">-</td>
                        <td align="center">-</td>
                        <td align="center">-</td>
                        <td align="center">-</td>
                        <?php
//                                $tot = $tot + ($cant[sum]);
                    }
                    ?>
                    <td align="right"><?php echo number_format($cant[sum], 2) ?></td>
                </tr>-->
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
<!--                <tr>
                    //EFICIENCIA POR CAPACIDAD**************************************************************        
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
                                                                                                            <td align="right"><?php echo number_format(($cnt_1[sum] / (($d) * 12)) / $rstMaq[maq_d], 2) ?></td>
                                                                                                    <td align="right"><?php echo number_format(($cnt_2[sum] / (($d) * 12)) / $rstMaq[maq_d], 2) ?></td>
                        <td align="center" colspan="4"><?php echo number_format(($cant[sum] / (($d) * 24)) / $rstMaq[maq_d], 2) ?></td>
                        <?php
                        $tot = $tot + round(($cant[sum] / (($d) * 24)) / $rstMaq[maq_d], 2);
                    }
                    ?> 
                    <td align="right"><?php echo number_format($tot / $div, 2) ?></td>
                </tr>-->
<!--                <tr>
                    //EFICIENCIA**************************************************************    
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
                                                                                                        <td align="right"><?php echo number_format(($cnt_1[sum] / (($d) * 12)) / $rstMaq[ext_productividad] * 100, 2) . '%' ?></td>
                                                                                                <td align="right"><?php echo number_format(($cnt_2[sum] / (($d) * 12)) / $rstMaq[ext_productividad] * 100, 2) . '%' ?></td>
                        <td align="center" colspan="4"><?php echo number_format(($cant[sum] / (($d) * 24)) / $rstMaq[maq_d] * 100, 2) . '%' ?></td>
                        <?php
                        $tot = $tot + round(($cant[sum] / (($d) * 24)) / $rstMaq[maq_d] * 100, 2);
                    }
                    ?> 
                    <td align="right" ><?php echo number_format($tot / $div, 2) . '%' ?></td>
                </tr>-->
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
    </body>
</html>
