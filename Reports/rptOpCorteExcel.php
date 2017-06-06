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
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $from = $_GET[fecha1];
    $until = $_GET[fecha2];
} else {
    $from = date('Y-m-d');
    $until = date('Y-m-d');
}
$cnsMaq = $Set->listaCortadoras($sec);
$cnsMaq1 = $Set->listaCortadoras($sec);
$cnsMaq2 = $Set->listaCortadoras($sec);

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
                    DEPARTAMENTO DE BOBINADO/CORTE
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
//                    $d1 = explode('-', $from);
//                    $f1 = date_create($d1[0] . '-' . $d1[1] . '-' . $d1[2]);
                    $f1 = date_create($from);
                    date_add($f1, date_interval_create_from_date_string($n . ' days'));
                    $dt = date_format($f1, 'Y-m-d');
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
                            $cnt_c = pg_fetch_array($Set->listaProduccionByDateMaqEst($date, $date, $rstMaq[id], '0'));
                            $cnt_i = pg_fetch_array($Set->listaProduccionByDateMaqEst($date, $date, $rstMaq[id], '3'));
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
                            $cnt_c = pg_fetch_array($Set->listaProduccionByDateMaqEst($date, $date, $rstMaq[id], '0'));
                            $cnt_i = pg_fetch_array($Set->listaProduccionByDateMaqEst($date, $date, $rstMaq[id], '3'));
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
                    $cant = pg_fetch_array($Set->listaProduccionByDateMaq($from, $until, $rstMaq[id]));
                    $cnt_c = pg_fetch_array($Set->listaProduccionByDateMaqEst($from, $until, $rstMaq[id], '0'));
                    $cnt_i = pg_fetch_array($Set->listaProduccionByDateMaqEst($from, $until, $rstMaq[id], '3'));
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
                    $cant = pg_fetch_array($Set->listaProduccionByDateMaq($from, $until, $rstMaq[id]));
                    $cnt_c = pg_fetch_array($Set->listaProduccionByDateMaqEst($from, $until, $rstMaq[id], '0'));
                    $cnt_i = pg_fetch_array($Set->listaProduccionByDateMaqEst($from, $until, $rstMaq[id], '3'));
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
                    $cant = pg_fetch_array($Set->listaProduccionByDateMaq($from, $until, $rstMaq[id]));
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
                    $cant = pg_fetch_array($Set->listaProduccionByDateMaq($from, $until, $rstMaq[id]));
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
                    $cant = pg_fetch_array($Set->listaProduccionByDateMaq($from, $until, $rstMaq[id]));
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
    </body>
</html>
