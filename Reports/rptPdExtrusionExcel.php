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
    $desde = $_GET[fecha1];
    $hasta = $_GET[fecha2];
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
}

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
                    REPORTE DIARIO DE PRODUCCION<br><br>
                    DEPARTAMENTO DE EXTRUSION
                </center>
                <center class="cont_finder">
                    <!--                    <form id="exp_excel" style="float:right;margin-top:6px;padding:0px" method="post" action="../Includes/export.php?tipo=1" onsubmit="return exportar_excel()"  >
                                            <input type="submit" value="Excel" class="auxBtn" />
                                            <input type="hidden" id="datatodisplay" name="datatodisplay">
                                        </form>-->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        DESDE:<input type="text" name="fecha1" size="15" id="fecha1" value="<?php echo $desde ?>"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" name="fecha2" size="15" id="fecha2" value="<?php echo $hasta ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" name="search" onclick="frmSearch.submit()">Buscar</button>
                    </form> 
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <?php
            //Head*************************************************************************** 
            ?>
            <thead >
                <tr>
                    <th rowspan="4">FECHA</th> 
                    <th rowspan="4">MAQ#</th> 
                    <th rowspan="4">PEDIDO</th>  
                    <th rowspan="4">CLIENTE</th>  
                    <th rowspan="4">REFERENCIA</th>  
                    <th rowspan="2">ANCHO</th>  
                    <th rowspan="2">ESP</th>  
                    <th colspan="4">PRODUCCION DIARIA</th>  
                </tr>
                <tr>
                    <th colspan="2">Conforme</th>
                    <th colspan="2">Inconforme</th>
                <tr>
                <tr>
                    <th>(cm)</th>  
                    <th>(&mu;)</th> 
                    <th style="width: 100px">peso(kg)</th> 
                    <th style="width: 100px">mts</th>  
                    <th style="width: 100px">peso(kg)</th> 
                    <th style="width: 100px">mts</th>
                </tr>
            </thead>
            <?php
            $d = (intval(dias($desde, $hasta)) + 1);
            $dnolab = 0;
            $n = 0;
            $tpeso = 0;
            $tpesoi = 0;
            $tmts = 0;
            $tmtsi = 0;
            while ($n < $d) {
                $tpeso0 = 0;
                $tpesoi0 = 0;
                $tdesp = 0;
                $tmts0 = 0;
                $tmtsi0 = 0;
                $fill = true;
                $r = 0;

                $f1 = date_create($desde);
                date_add($f1, date_interval_create_from_date_string($n . ' days'));
                $date = date_format($f1, 'Y-m-d');
                $extrusoras = $Set->listaExtrusoras();
                $cext = pg_num_rows($extrusoras) + 1;
                $cmb = pg_num_rows($Set->rptExtrusionFecha($date));
                $mq = pg_num_rows($Set->rptExtrusionMaq($date));
                if ($cmb > 0) {
                    $cmb = $cmb - $mq;
                }
                while ($rstMaq = pg_fetch_array($extrusoras)) {
                    $r++;
                    if ($fill == true) {
                        $fill = !$fill;
                    } else {
                        $fill = true;
                    }
                    $cnsPedidos = $Set->rptExtrusion($date, $rstMaq[id]);
                    $rstPedidos1 = pg_fetch_array($Set->rptExtrusion($date, $rstMaq[id]));
                    $Maquina = '';
                    $cn = 0;
                    $rw = $cext + $cmb;
                    $Maquina = $rstMaq['maq_a'];

                    if (empty($rstPedidos1)) {
                        ?>
                        <tr>
                            <?php
                            if ($r == 1) {
                                ?>
                                <td rowspan="<?php echo $rw ?>"><?php echo $date ?></td> 
                                <?php
                                $r = 2;
                            }
                            ?>
                            <td><?php echo $rstMaq['maq_a'] ?></td> 
                            <td align="center">-</td> 
                            <td align="center">-</td> 
                            <td align="center">-</td> 
                            <td align="center">-</td> 
                            <td align="center">-</td> 
                            <td align="center">-</td> 
                            <td align="center">-</td> 
                            <td align="center">-</td> 
                            <td align="center">-</td> 
                        </tr>
                        <?php
                    } else {
                        //*******Si tiene Pedidos*********************************************************
                        while ($rstPedidos = pg_fetch_array($cnsPedidos)) {
                            //*********Calculo Ancho de Extrusion
                            $rst_pe = pg_fetch_array($Set->rptExtrusionEst($date, $rstMaq[id], $rstPedidos['ord_num_orden'], $rstPedidos['pro_id']));
                            ?>
                            <tr>
                                <?php
                                if ($r == 1) {
                                    ?>
                                    <td rowspan="<?php echo $rw ?>"><?php echo $date ?></td> 
                                    <?php
                                    $r = 2;
                                }
                                ?>
                                <td><?php echo $Maquina ?></td>   
                                <td align="center"><?php echo $rstPedidos['ord_num_orden']?></td>
                                <td><?php echo substr($rstPedidos['cli_raz_social'], 0, 8) ?></td>
                                <td><?php echo $rstPedidos['pro_descripcion'] ?></td>
                                <td align="right"><?php echo number_format($rstPedidos['pro_ancho'] * 100, 2) ?></td>
                                <td align="right"><?php echo $rstPedidos['pro_espesor'] ?></td>
                                <?php
                                $rstDatos1['mts'] = ($rst_pe[conforme] / $rstPedidos[pro_gramaje]);
                                $rstDatos1['mtsi'] = ($rst_pe[inconforme] / $rstPedidos[pro_gramaje]);
                                ?>
                                <td align="right"><?php echo number_format($rst_pe['conforme'], 2) ?></td>
                                <td align="right"><?php echo number_format($rstDatos1['mts'], 2) ?></td>
                                <td align="right"><?php echo number_format($rst_pe['inconforme'], 2) ?></td>
                                <td align="right"><?php echo number_format($rstDatos1['mtsi'], 2) ?></td>
                            </tr>
                            <?php
                            $tpeso += $rst_pe['conforme'];
                            $tpesoi += $rst_pe['inconforme'];
                            $tmts+= $rstDatos1['mts'];
                            $tmtsi+= $rstDatos1['mtsi'];
                            $tpeso0 += $rst_pe['conforme'];
                            $tpesoi0 += $rst_pe['inconforme'];
                            $tmts0 += $rstDatos1['mts'];
                            $tmtsi0 += $rstDatos1['mtsi'];
                        }
                    }
                }
                ?>
                <tr>
                    <td style="font-weight: bold" colspan="6">Tot.Prod.Diaria:</td>
                    <td style="font-weight: bold" align="right"><?php echo number_format($tpeso0, 2) ?></td>
                    <td style="font-weight: bold" align="right"><?php echo number_format($tmts0, 2) ?></td>
                    <td style="font-weight: bold" align="right"><?php echo number_format($tpesoi0, 2) ?></td>
                    <td style="font-weight: bold" align="right"><?php echo number_format($tmtsi0, 2) ?></td>
                </tr>
                <?php
                $n++;
            }
            ?>
            <tr>
                <td style="font-weight: bold" colspan="7">Total Produccion</td>
                <td style="font-weight: bold" align="right"><?php echo number_format($tpeso, 2) ?></td>
                <td style="font-weight: bold" align="right"><?php echo number_format($tmts, 2) ?></td>
                <td style="font-weight: bold" align="right"><?php echo number_format($tpesoi, 2) ?></td>
                <td style="font-weight: bold" align="right"><?php echo number_format($tmtsi, 2) ?></td>
            </tr>
        </table>
    </body>
</html>
