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
if (isset($_GET[fecha1])) {
    $date = $_GET[fecha1];
} else {
    $date = date('Y-m-d');
}
$selladoras = $Set->listaCortadoras();

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
                    DEPARTAMENTO DE CORTE/BOBINADO
                </center>
                <center class="cont_finder">
                    <!--                    <form id="exp_excel" style="float:right;margin-top:6px;padding:0px" method="post" action="../Includes/export.php?tipo=1" onsubmit="return exportar_excel()"  >
                                            <input type="submit" value="Excel" class="auxBtn" />
                                            <input type="hidden" id="datatodisplay" name="datatodisplay">
                                        </form>-->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        FECHA:<input type="text" name="fecha1" size="15" id="fecha1" value="<?php echo $date ?>"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
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
                        <th rowspan="2">MAQ#</th> 
                        <!--<th >Product </th>-->  
                        <th rowspan="2">PEDIDO</th>  
                        <th rowspan="2">CLIENTE</th>  
                        <th colspan="4">PRODUCTO</th>  
                        <!--<th colspan="5">PRODUCCION 1ER TURNO</th>-->  
                        <!--<th colspan="5">PRODUCCION 2DO TURNO</th>-->  
                        <th colspan="4">PRODUCCION DIARIA</th>  
                    </tr>
                    <tr>
                        <!--<th>Estim 12hrs</th>-->  
                        <th>Codigo</th>  
                        <th>Referencia</th>  
                        <th style="width: 100px">Ancho</th>  
                        <th style="width: 100px">Und</th>  
    <!--                    <th>Cant</th>  
                        <th>Bult</th>  
                        <th>Pes.(kg)</th>  
                        <th>MstPrt</th>  
                        <th>Dsp(Kg)</th>  
                        <th>Cant</th>  
                        <th>Bult</th>  
                        <th>Pes.(kg)</th>  
                        <th>MstPrt</th>  
                        <th>Dsp(Kg)</th>  -->
                        <th style="width: 100px">Cant</th>  
                        <!--<th>Bult</th>-->  
                        <th style="width: 100px">Pes.(kg)</th>  
<!--                        <th>MstPrt</th>  
                        <th>Dsp(Kg)</th>  -->
                    </tr>
                </thead>
                <?php
                //*************************************************************            
                //Fundas
                $tfundas1 = 0;
                $tfundas2 = 0;
                $stfundas = 0;
                $gtfundas = 0;
                //Rollos
                $tr_rollos1 = 0;
                $tr_rollos2 = 0;

                //Kilos
                $tkilos1 = 0;
                $tkilos2 = 0;
                $stkilos = 0;
                $gtkilos = 0;
                //Bultos
                $tbultos1 = 0;
                $tbultos2 = 0;
                $stbultos = 0;
                $gtbultos = 0;
                //Peso
                $tpeso1 = 0;
                $tpeso2 = 0;
                $stpeso = 0;
                $gtpeso = 0;
                //mstPrint
                $tmst1 = 0;
                $tmst2 = 0;
                $stmst = 0;
                $gtmst = 0;
                //desp
                $tdesp1 = 0;
                $tdesp2 = 0;
                $stdesp = 0;
                $gtdesp = 0;

                while ($rstMaq = pg_fetch_array($selladoras)) {
                    $cnsPedidos = $Set->rptSell1($date, $rstMaq['id']);
                    $rstPedidos1 = pg_fetch_array($Set->rptSell1($date, $rstMaq['id']));
                    $Maquina = '';
                    $Productividad = '';
                    $cn = 0;
                    $cmb = pg_num_rows($cnsPedidos);
                    if (empty($rstPedidos1)) {
                        ?>
                        <tr>
                            <td><?php echo $rstMaq['maq_a'] ?></td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <!--<td>-</td>-->
<!--                            <td>-</td>
                            <td>-</td>-->
            <!--                        <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>-->
                        </tr>
                        <?php
                    } else {

                        //*****************Si Existen Pedidos*********************************************
                        while ($rstPedidos = pg_fetch_array($cnsPedidos)) {

                            $cn++;
//                        if ($cn == 1) {
                            $Maquina = $rstMaq['maq_a'];
//                            $Productividad = number_format(($rstMaq['sell_velocidad'] * $rstPedidos['ord_pedido_carriles'] * 0.6));
//                            $h = $cmb * 6;
//                            $l = 1;
//                        } else {
//                            $Maquina = '';
//                            $Productividad = '';
//                            $h = 6;
//                            $l = 0;
//                        }
                            $rstDatos1 = pg_fetch_array($Set->rptSell2($date, $rstMaq['id'], $rstPedidos['opp_codigo'], 1));
//                        $rstDatos2 = pg_fetch_array($Set->rptSell2($date, $rstMaq['sell_id'], $rstPedidos['ord_pedido_codigo'], 2));
//                        $Ped = new Pedido();
                            $rstPedido = pg_fetch_array($Set->listaPedidosCodigoCorte($rstPedidos['opp_codigo']));

//                        if ($rstPedido['pro_fuelle_lateral'] == 0) {
//                            $fuelle = "";
//                        } else {
//                            $fuelle = "+2F" . number_format($rstPedido['pro_fuelle_lateral'], 1);
//                        }
//                        if ($rstPedido['pro_largo'] == 0) {
//                            $fuellef = "";
//                        } else {
//                            $fuellef = "+Ff" . number_format($rstPedido['pro_fuelle_fondo'], 1);
//                        }
//                        if ($rstPedido['pro_solapa'] == 0) {
//                            $solapa = "";
//                        } else {
//                            $solapa = "+DSR" . number_format($rstPedido['pro_solapa'], 1);
//                        }
//
//                        $largo = "x" . number_format($rstPedido['pro_largo'], 1);
//                        $espesor = "x" . $rstPedido['pro_espesor'];
//                        $medida = number_format($rstPedido['pro_ancho'], 1) . $fuelle . $largo . $solapa . $espesor;
                            $medida = number_format($rstPedido['pro_ancho'], 2);
//                        //$Producto=$rstPedido['pro_ancho'];
//                        if ($rstDatos1 == '') {
                            ?>
                <!--                            <tr>
                                        <td><?php echo $Maquina ?></td>
                                        <td align="right"><?php echo $Productividad ?></td>
                            <?php
//                                if (substr($rstDatos2['ord_pedido_codigo'], 0, 1) == 'W') {
//                                    $rstDatos2['ord_pedido_codigo'] = substr($rstDatos2['ord_pedido_codigo'], 1, 10);
//                                }
                            ?>

                                        <td><?php echo $rstDatos2['ord_pedido_codigo'] ?></td>
                                        <td><?php echo substr($rstDatos2['cli_raz_social'], 0, 8) ?></td>
                                        <td align="right"><?php echo $rstDatos2['pro_id'] ?></td>
                                        <td><?php echo substr($rstDatos2['pro_referencia'], 0, 15) ?></td>
                                        <td align="right"><?php echo $medida ?></td>
                            <?php
                            if ($rstPedido['pro_unidad'] == 'FUNDAS') {
                                $unidad = 'MIL';
                            } else {
                                $unidad = substr($rstPedido['pro_unidad'], 0, 3);
                            }
                            ?>
                                        <td align="center"><?php echo $unidad ?></td>
                                        <td align="right">0</td>
                                        <td align="right">0</td>
                                        <td align="right">0</td>
                                        <td align="right">0</td>
                                        <td align="right">0</td>
                            <?php
                            //Sumo las unidades de Fundas-Kilos-Rollos
                            switch ($rstPedido['pro_uni']) {
                                case 'FUNDAS':
                                    ?>
                                                        <td align="right"><?php echo number_format($rstDatos2['fundas'], 2) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['bultos']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['mst']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['desp']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['fundas'], 2) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['bultos']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['mst']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['desp']) ?></td>
                                    <?php
                                    $tfundas2 = $tfundas2 + $rstDatos2['fundas'];
                                    $tbultos2 = $tbultos2 + $rstDatos2['bultos'];
                                    $tpeso2 = $tpeso2 + $rstDatos2['peso'];
                                    $tmst2 = $tmst2 + $rstDatos2['mst'];
                                    $tdesp2 = $tdesp2 + $rstDatos2['desp'];
                                    break;
                                case 'ROLLOS':
                                    ?>
                                                        <td align="right"><?php echo number_format($rstDatos2['rollos'], 2) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['bultos']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['mst']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['desp']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['rollos'], 2) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['bultos']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['mst']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['desp']) ?></td>
                                    <?php
                                    $tr_rollos2 = $tr_rollos2 + $rstDatos2['rollos'];
                                    $tr_bultos2 = $tr_bultos2 + $rstDatos2['bultos'];
                                    $tr_peso2 = $tr_peso2 + $rstDatos2['peso'];
                                    $tr_mst2 = $tr_mst2 + $rstDatos2['mst'];
                                    $tr_desp2 = $tr_desp2 + $rstDatos2['desp'];
                                    break;
                                case 'KILOS':
                                    ?>
                                                        <td align="right"><?php echo number_format($rstDatos2['bultos'] * $rstDatos2['peso'], 2) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['bultos']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['mst']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['desp']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['bultos'] * $rstDatos2['peso'], 2) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['bultos']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['mst']) ?></td>
                                                        <td align="right"><?php echo number_format($rstDatos2['desp']) ?></td>
                                    <?php
                                    $tk_kilos2 = $tk_kilos2 + ($rstDatos2['bultos'] * $rstDatos2['peso']);
                                    $tk_bultos2 = $tk_bultos2 + $rstDatos2['bultos'];
                                    $tk_peso2 = $tk_peso2 + $rstDatos2['peso'];
                                    $tk_mst2 = $tk_mst2 + $rstDatos2['mst'];
                                    $tk_desp2 = $tk_desp2 + $rstDatos2['desp'];
                                    break;
                            }
                            ?>
                                    </tr>-->
                            <?php
//                        } else {
                            ?>
                            </tr>
                            <td><?php echo $Maquina ?></td>
                            <!--<td align="right"><?php echo $Productividad ?></td>-->
                            <?php
//                        if (substr($rstDatos1['ord_pedido_codigo'], 0, 1) == 'W') {
//                            $rstDatos1['ord_pedido_codigo'] = substr($rstDatos1['ord_pedido_codigo'], 1, 10);
//                        }
                            ?>
                            <td><?php echo $rstDatos1['opp_codigo'] ?></td>
                            <td><?php echo substr($rstDatos1['cli_raz_social'], 0, 8) ?></td>
                            <td><?php echo $rstDatos1['pro_codigo'] ?></td>
                            <td><?php echo substr($rstDatos1['pro_descripcion'], 0, 15) ?></td>
                            <td align="right"><?php echo $medida ?></td>
                            <?php
                            if ($rstPedido['pro_uni'] == 'FUNDAS') {
                                $unidad = 'MIL';
                            } else {
                                $unidad = substr($rstPedido['pro_uni'], 0, 3);
                            }
                            ?>
                            <td align="center"><?php echo $unidad ?></td>
                            <?php
                            switch ($rstPedido['pro_uni']) {
                                case 'FUNDAS':
                                    ?>
                        <!--                                <td align="right"><?php echo number_format($rstDatos1['fundas'], 2) ?></td>
                                            <td align="right"><?php echo number_format($rstDatos1['bultos']) ?></td>
                                            <td align="right"><?php echo number_format($rstDatos1['peso']) ?></td>
                                            <td align="right"><?php echo number_format($rstDatos1['mst']) ?></td>
                                            <td align="right"><?php echo number_format($rstDatos1['desp']) ?></td>
                                            <td align="right"><?php echo number_format($rstDatos2['fundas'], 2) ?></td>
                                            <td align="right"><?php echo number_format($rstDatos2['bultos']) ?></td>
                                            <td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>
                                            <td align="right"><?php echo number_format($rstDatos2['mst']) ?></td>
                                            <td align="right"><?php echo number_format($rstDatos2['desp']) ?></td>-->
                                    <td align="right"><?php echo number_format($rstDatos1['fundas'] + $rstDatos2['fundas'], 2) ?></td>
                                    <!--<td align="right"><?php echo number_format($rstDatos1['bultos'] + $rstDatos2['bultos']) ?></td>-->
                                    <td align="right"><?php echo number_format($rstDatos1['peso'] + $rstDatos2['peso']) ?></td>
                                    <!--<td align="right"><?php echo number_format($rstDatos1['mst'] + $rstDatos2['mst']) ?></td>-->
                                    <!--<td align="right"><?php echo number_format($rstDatos1['desp'] + $rstDatos2['desp']) ?></td>-->
                                    <?php
                                    $tfundas1 = $tfundas1 + $rstDatos1['fundas'];
                                    $tbultos1 = $tbultos1 + $rstDatos1['bultos'];
                                    $tpeso1 = $tpeso1 + $rstDatos1['peso'];
                                    $tmst1 = $tmst1 + $rstDatos1['mst'];
                                    $tdesp1 = $tdesp1 + $rstDatos1['desp'];
                                    $tfundas2 = $tfundas2 + $rstDatos2['fundas'];
                                    $tbultos2 = $tbultos2 + $rstDatos2['bultos'];
                                    $tpeso2 = $tpeso2 + $rstDatos2['peso'];
                                    $tmst2 = $tmst2 + $rstDatos2['mst'];
                                    $tdesp2 = $tdesp2 + $rstDatos2['desp'];
                                    break;
                                case 'ROLLO':
                                    ?>
                        <!--                                <td align="right"><?php echo number_format($rstDatos1['rollos'], 2) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos1['bultos']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos1['peso']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos1['mst']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos1['desp']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['rollos'], 2) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['bultos']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['mst']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['desp']) ?></td>-->
                                    <td align="right"><?php echo number_format($rstDatos1['rollos'] + $rstDatos2['rollos'], 2) ?></td>
                                    <!--<td align="right"><?php echo number_format($rstDatos1['bultos'] + $rstDatos2['bultos']) ?></td>-->
                                    <td align="right"><?php echo number_format($rstDatos1['peso'] + $rstDatos2['peso'], 2) ?></td>
<!--                                    <td align="right"><?php echo number_format($rstDatos1['mst'] + $rstDatos2['mst']) ?></td>-->
                                    <!--<td align="right"><?php echo number_format($rstDatos1['desp'] + $rstDatos2['desp']) ?></td>-->
                                    <?php
                                    $tr_rollos1 = $tr_rollos1 + $rstDatos1['rollos'];
                                    $tr_bultos1 = $tr_bultos1 + $rstDatos1['bultos'];
                                    $tr_peso1 = $tr_peso1 + $rstDatos1['peso'];
                                    $tr_mst1 = $tr_mst1 + $rstDatos1['mst'];
                                    $tr_desp1 = $tr_desp1 + $rstDatos1['desp'];
                                    $tr_rollos2 = $tr_rollos2 + $rstDatos2['rollos'];
                                    $tr_bultos2 = $tr_bultos2 + $rstDatos2['bultos'];
                                    $tr_peso2 = $tr_peso2 + $rstDatos2['peso'];
                                    $tr_mst2 = $tr_mst2 + $rstDatos2['mst'];
                                    $tr_desp2 = $tr_desp2 + $rstDatos2['desp'];
                                    break;
                                case 'KILOS':
                                    ?>
                        <!--                                <td align="right"><?php echo number_format($rstDatos1['peso'], 2) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos1['bultos']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos1['peso']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos1['mst']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos1['desp']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['peso'], 2) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['bultos']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['mst']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['desp']) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos1['peso'] + $rstDatos2['peso'], 2) ?></td>-->
                                    <!--<td align="right"><?php echo number_format($rstDatos1['bultos'] + $rstDatos2['bultos']) ?></td>-->
                                    <td align="right"><?php echo number_format($rstDatos1['peso'] + $rstDatos2['peso']) ?></td>
                                    <!--<td align="right"><?php echo number_format($rstDatos1['mst'] + $rstDatos2['mst']) ?></td>-->
                                    <!--<td align="right"><?php echo number_format($rstDatos1['desp'] + $rstDatos2['desp']) ?></td>-->
                                    <?php
                                    $tk_kilos1 = $tk_kilos1 + $rstDatos1['peso'];
                                    $tk_bultos1 = $tk_bultos1 + $rstDatos1['bultos'];
                                    $tk_peso1 = $tk_peso1 + $rstDatos1['peso'];
                                    $tk_mst1 = $tk_mst1 + $rstDatos1['mst'];
                                    $tk_desp1 = $tk_desp1 + $rstDatos1['desp'];
                                    $tk_rollos2 = $tk_rollos2 + ($rstDatos2['bultos'] * $rstDatos2['peso']);
                                    $tk_bultos2 = $tk_bultos2 + $rstDatos2['bultos'];
                                    $tk_peso2 = $tk_peso2 + $rstDatos2['peso'];
                                    $tk_mst2 = $tk_mst2 + $rstDatos2['mst'];
                                    $tk_desp2 = $tk_desp2 + $rstDatos2['desp'];
                                    break;
                            }
                            ?>
                            </tr>
                            <?php
//                }
                        }
                    }
                }
                ?>
                <thead >
                    <tr><!--
-->                        <th colspan="7" align="right">Total:</th>
<!--                        <th colspan="2" align="right"><?php echo number_format($tfundas1 + $tfundas2, 1) ?></th>
                        <th colspan="2" align="right"><?php echo number_format($tpeso1 + $tpeso2, 1) . " (kg)" ?></th>
                        <th></th>
                        <th align="right"><?php echo number_format($tfundas1, 1) ?></th>
                        <th align="right"><?php echo number_format($tbultos1) ?></th>
                        <th align="right"><?php echo number_format($tpeso1) ?></th>
                        <th align="right"><?php echo number_format($tmst1) ?></th>
                        <th align="right"><?php echo number_format($tdesp1) ?></th>
                        <th align="right"><?php echo number_format($tfundas2, 1) ?></th>
                        <th align="right"><?php echo number_format($tbultos2) ?></th>
                        <th align="right"><?php echo number_format($tpeso2) ?></th>
                        <th align="right"><?php echo number_format($tmst2) ?></th>
                        <th align="right"><?php echo number_format($tdesp2) ?></th>-->
                        <th align="right"><?php echo number_format($tr_rollos1 + $tr_rollos2, 2) ?></th>
                        <!--<th align="right"><?php echo number_format($tbultos1 + $tbultos2) ?></th>-->
                        <th align="right"><?php echo number_format($tr_peso1 + $tr_peso2, 2) ?></th>
<!--                        <th align="right"><?php echo number_format($tmst1 + $tmst2) ?></th>
                        <th align="right"><?php echo number_format($tdesp1 + $tdesp2) ?></th>-->
<!--                    </tr>
                    //Rollos
                    <tr>
                        <th colspan="2" align="right">Rollos</th>
                        <th colspan="2" align="right"><?php echo number_format($tr_rollos1 + $tr_rollos2, 1) ?></th>
                        <th colspan="2" align="right"><?php echo number_format($tr_peso1 + $tr_peso2, 1) . " (kg)" ?></th>
                        <th></th>
                        <th align="right"><?php echo number_format($tr_rollos1, 1) ?></th>
                        <th align="right"><?php echo number_format($tr_bultos1) ?></th>
                        <th align="right"><?php echo number_format($tr_peso1) ?></th>
                        <th align="right"><?php echo number_format($tr_mst1) ?></th>
                        <th align="right"><?php echo number_format($tr_desp1) ?></th>
                        <th align="right"><?php echo number_format($tr_rollos2, 1) ?></th>
                        <th align="right"><?php echo number_format($tr_bultos2) ?></th>
                        <th align="right"><?php echo number_format($tr_peso2) ?></th>
                        <th align="right"><?php echo number_format($tr_mst2) ?></th>
                        <th align="right"><?php echo number_format($tr_desp2) ?></th>
                        <th align="right"><?php echo number_format($tr_rollos1 + $tr_rollos2, 1) ?></th>
                        <th align="right"><?php echo number_format($tr_bultos1 + $tr_bultos2) ?></th>
                        <th align="right"><?php echo number_format($tr_peso1 + $tr_peso2, 2) ?></th>
                        <th align="right"><?php echo number_format($tr_mst1 + $tr_mst2) ?></th>
                        <th align="right"><?php echo number_format($tr_desp1 + $tr_desp2) ?></th>
                    </tr>
                    //Kilos
                    <tr>
                        <th colspan="2" align="right">Kilos:</th>
                        <th colspan="2" align="right"><?php echo number_format($tk_kilos1 + $tk_kilos2, 1) ?></th>
                        <th colspan="2" align="right"><?php echo number_format($tk_kilos1 + $tk_kilos2, 1) . '(kg)' ?></th>
                        <th></th>
                        <th align="right"><?php echo number_format($tk_kilos1, 1) ?></th>
                        <th align="right"><?php echo number_format($tk_bultos1) ?></th>
                        <th align="right"><?php echo number_format($tk_peso1) ?></th>
                        <th align="right"><?php echo number_format($tk_mst1) ?></th>
                        <th align="right"><?php echo number_format($tk_desp1) ?></th>
                        <th align="right"><?php echo number_format($tk_fundas2, 1) ?></th>
                        <th align="right"><?php echo number_format($tk_bultos2) ?></th>
                        <th align="right"><?php echo number_format($tk_peso2) ?></th>
                        <th align="right"><?php echo number_format($tk_mst2) ?></th>
                        <th align="right"><?php echo number_format($tk_desp2) ?></th>
                        <th align="right"><?php echo number_format($tk_kilos1 + $tk_kilos2, 1) ?></th>
                        <th align="right"><?php echo number_format($tk_bultos1 + $tk_bultos2) ?></th>
                        <th align="right"><?php echo number_format($tk_peso1 + $tk_peso2) ?></th>
                        <th align="right"><?php echo number_format($tk_mst1 + $tk_mst2) ?></th>
                        <th align="right"><?php echo number_format($tk_desp1 + $tk_desp1) ?></th>
                    </tr>
                    //Gran Total
                    <tr>
                        <th colspan="2" align="right">Totales:</th>
                        <th colspan="2" ></th>
                        <th colspan="2" align="right"><?php echo number_format($tk_kilos1 + $tk_kilos2 + $tr_peso1 + $tr_peso2 + $tpeso1 + $tpeso2, 1) . '(kg)' ?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th align="right"><?php echo number_format($tr_peso1 + $tpeso1 + $tk_peso1) ?></th>
                        <th></th>
                        <th align="right"><?php echo number_format($tr_desp1 + $tdesp1 + $tk_desp1) ?></th>
                        <th></th>
                        <th></th>
                        <th align="right"><?php echo number_format($tr_peso2 + $tpeso2 + $tk_peso2) ?></th>
                        <th></th>
                        <th align="right"><?php echo number_format($tr_desp2 + $tdesp2 + $tk_desp2) ?></th>
                                <th></th>
                        <th></th>
                        <th></th>
                        <th align="right"><?php echo number_format($tr_peso1 + $tpeso1 + $tk_peso1 + $tr_peso2 + $tpeso2 + $tk_peso2, 2) ?></th>
                        <th></th>
                        <th align="right"><?php echo number_format($tr_desp1 + $tdesp1 + $tk_desp1 + $tr_desp2 + $tdesp2 + $tk_desp2) ?></th>
                    --></tr>
        </table>
    </body>
</html>
