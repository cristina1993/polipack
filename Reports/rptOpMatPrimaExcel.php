<?php
set_time_limit(0);
date_default_timezone_set('America/Guayaquil');
include_once '../Includes/permisos.php';
include_once("../Clases/clsProduccion_reportes.php");
$Set = new Produccion_reportes();
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $from = $_GET[fecha1];
    $until = $_GET[fecha2];
} else {
    $from = date('Y-m-d');
    $until = date('Y-m-d');
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
             .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
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
                    REPORTE ACUMULADO DE CONSUMO DE MATERIA
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
            //Head*************************************************************************** 
            ?>
            <thead >
                <tr>
                    <th rowspan="2">CODIGO</th>
                    <th rowspan="2">DESCRIPCION</th>
                    <th colspan="2">TOTAL</th>
                </tr>
                <tr>
                    <th>kg</th>
                    <th>%</th>
                </tr>
            </thead>
            <tr>
                <!--//*****Datos***********-->
                <?php
                $cnsMp = $Set->listaReporteMP();
                $mptp = '';
                while ($rstMp = pg_fetch_array($cnsMp)) {
                    ?> 
                    <?php
                    $rstIMP = pg_fetch_array($Set->listarSumaEgresoMateriaPrimaId($rstMp[mp_id], $from, $until));
                    if ($rstIMP[sum] > 0) {
                        if ($mptp != $rstMp[mpt_id] && !empty($mptp)) {
                            ?>     
                            <td class='totales' align="left">TOTAL <?php echo $tip ?></td>
                            <?php
                            $tcmpsec = pg_fetch_array($Set->listaSumaTotalIMPbyDateSec($from, $until));
                            $tgroup = pg_fetch_array($Set->listaSumIngMPbydatetoSec($from, $until, $mptp));
                            $porc = number_format($tgroup[sum] * 100 / $tcmpsec[sum], 2);
                            if ($tgroup[sum] == 0) {
                                $cmp = '';
                                $porc = '0';
                            } else {
                                $cmp = $tgroup[sum];
                            }
                            ?>  
                            <td colspan="2" class='totales' align="right"><?php echo number_format($tgroup[sum],2) ?></td>
                            <td class='totales' align="right"> <?php echo $porc ?></td>
                            <?php
                            $t1 = $t1 + $tgroup[sum];
                            ?>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td> <?php echo $rstMp[mp_codigo] ?></td>
                        <td> <?php echo $rstMp[mp_referencia] ?></td>
                        <?php
                        $tcmpsec = pg_fetch_array($Set->listaSumaTotalIMPbyDateSec($from, $until));
                        $rstCMP = pg_fetch_array($Set->listarSumaEgresoMateriaPrimaId($rstMp[mp_id], $from, $until));
                        $porc = number_format($rstCMP[sum] * 100 / $tcmpsec[sum], 2);
                        if ($rstCMP[sum] == 0) {
                            $cmp = '';
                            $porc = '';
                        } else {
                            $cmp = $rstCMP[sum];
                        }
                        ?>
                        <td align="right"><?php echo number_format($cmp,2) ?></td>
                        <td align="right"> <?php echo $porc ?></td>
                    <tr>
                        <?php
                        $mptp = $rstMp[mpt_id];
                        $tip = $rstMp[mpt_nombre];
                    }
                }
                ?>
                        <td colspan="2" class='totales' align="left">TOTAL <?php echo $tip ?></td>
                <?php
                $tcmpsec = pg_fetch_array($Set->listaSumaTotalIMPbyDateSec($from, $until));
                $tgroup = pg_fetch_array($Set->listaSumIngMPbydatetoSec($from, $until, $mptp));
                $porc = number_format($tgroup[sum] * 100 / $tcmpsec[sum], 2);
                if ($tgroup[sum] == 0) {
                    $cmp = '';
                    $porc = '0';
                } else {
                    $cmp = $tgroup[sum];
                }
                ?>
                <td class='totales' align="right"><?php echo number_format($tgroup[sum],2) ?></td>
                <td class='totales' align="right"><?php echo $porc ?></td>
                <?php
                ?>
            </tr>
            <tr>
                <!--//*********************-->
                <td class='totales'>TOTAL A EXTRUSION:</td>
                <?php
                $tcmpsec = pg_fetch_array($Set->listaSumaTotalIMPbyDateSec($from, $until));
                ?>
                <td colspan="2"  class='totales' align="right"><?php echo number_format($tcmpsec[sum],2) ?></td>
                <td class='totales' align="right"><?php echo'100.00' ?></td>
                <?php
                ?>
            </tr>     
        </table>
    </body>
</html>
