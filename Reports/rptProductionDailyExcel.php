<?php
//session_start();
//include_once '../Librerias4.php';
include_once '../Includes/permisos.php';
include_once("../Clases/clsProduccion_reportes.php");
//include_once("../Clases/clsReportes.php");
//include_once("../Clases/clsPedido.php");
//include_once("../Clases/clsPoductos.php");
//include_once("../Clases/clsSecciones.php");
//include_once("../Clases/clsColores.php");
//include_once("../Clases/clsMail.php");
//include_once '../Clases/clsMateriaPrima.php';
//include_once("../Clases/clsRegistroExtrusion.php");
//include_once("../Clases/clsRegistroImpresion.php");
//include_once("../Clases/clsRegistroSellado.php");
//header("Content-type: application/vnd.ms-excel");
//header("Content-Disposition:attachment;filename=produccion.xls");

date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$secId = $_POST['sec_id'];
$ext = $_POST['ext'];
$imp = $_POST['imp'];
$sell = $_POST['sell'];
$rptGeneral = $_POST['rptGeneral'];
$res = $_POST['res'];
$from = $_POST['f_month_from'];
$until = $_POST['f_month_until'];
$sec = $_POST['sec_id'];
$rec = $_POST[rec];
$mp = $_POST[mp];
$mpm = $_POST[mpm];
$prod = $_POST[prod];
$prodm = $_POST[prodm];
$extm = $_POST[extm];
//$Maq = new Maquinas();
$Set = new Produccion_reportes();

if ($rptGeneral == 'on' || $res == 'on' || $mp == 'on' || $mpm == 'on' || $prod == 'on' || $prodm == 'on' || $extm == 'on') {
    if ($extm == 'on') {
        rptExtrusionResumen($sec, $_POST['f_month_from'], $_POST['f_month_until']);
    }
    if ($mpm == 'on') {
        rptMatPrimCons($_POST['f_month_from'], $_POST['f_month_until']);
    }
    if ($mp == 'on') {
        $from = $_POST['f_month_from'];
        $until = $_POST['f_month_until'];
        $d = days($from, $until);
        $n = 0;
        while ($n <= intval($d)) {
            $d1 = explode('/', $from);
            $f1 = date_create($d1[0] . '-' . $d1[1] . '-' . $d1[2]);
            date_add($f1, date_interval_create_from_date_string($n . ' days'));
            $dt = date_format($f1, 'd/m/Y');
            rptMatPrimDialy($dt, $dt);
            $n++;
        }
    }

    if ($rptGeneral == 'on') {
        rptGeneralFlowReport($_POST['f_month_from'], $_POST['f_month_until'], $_POST['sec_id']);
    }

    if ($res == 'on') {
        rptGenralResumen($_POST[f_month_from], $_POST[f_month_until], $_POST[sec_id]);
    }


    if ($prod == 'on') {
        rptSegProducto($from, $until, $sec);
    }
    if ($prodm == 'on') {
        rptMesProducto($sec, 2013);
    }
} else {
    $from = $_POST['f_month_from'];
    $until = $_POST['f_month_until'];
    $d = days($from, $until);
    $n = 0;
    while ($n <= intval($d)) {
//        $d1 = explode('/', $from);
//        $f1 = date_create($d1[0] . '-' . $d1[1] . '-' . $d1[2]);
//        date_add($f1, date_interval_create_from_date_string($n . ' days'));
//        $dt = date_format($from);

        $cnsExtrusoras = $Set->listaExtrusoras();
        $cnsCortadoras = $Set->listaCortadoras();
//        $cnsSelladoras = $Maq->listaSelladorasSeccion($secId);
        if ($ext == 'on') {
            rptExtrusion($cnsExtrusoras, $from, $secId);
        }

        if ($sell == 'on') {
            rptSellado($cnsCortadoras, $from, $secId);
        }
        if ($rec == 'on') {
            rptReciclado($dt);
        }
        $n++;
    }
}

function days($from, $until) {
    $f = explode('/', $from);
    $u = explode('/', $until);
    $fecha_i = $f[0] . '-' . $f[1] . '-' . $f[2];
    $fecha_f = $u[0] . '-' . $u[1] . '-' . $u[2];
    $dias = (strtotime($fecha_i) - strtotime($fecha_f)) / 86400;
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
        function rptExtrusion($extrusoras, $date, $secId) {
            $Set = new Produccion_reportes();
//        $rstSeccion = pg_fetch_array($Seccion->listaUnaSecciones($secId));
//        $Pedido = new Pedido();
//        $Set = new Reportes();
            ?>
            <table align="left" id="tbl" width="100%" border="1">
                <thead >
                    <tr>
                        <th style="font-weight: bold" colspan="8" align="center" >POLIPACK</th>  
                    </tr>

                    <tr>
                        <th style="font-weight: bold;text-align: left" colspan="3" align="center">DEPARTAMENTO DE EXTRUSION</th>  
                        <th style="font-weight: bold;text-align: left" colspan="3" align="center">REPORTE DIARIO DE PRODUCCION</th>  
                        <th style="font-weight: bold;text-align: left" colspan="2" align="center">FECHA <?php echo $date ?></th> 
                    </tr>
                    <tr>
                        <th rowspan="2">MAQ#</th> 
                        <!--<th >Product </th>-->  
                        <th rowspan="2">PEDIDO</th>  
                        <th rowspan="2">CLIENTE</th>  
                        <th rowspan="2">REFERENCIA</th>  
                        <th>ANCHO</th>  
                        <th>ESP</th>  
                        <!--<th rowspan="2">COLOR</th>-->  
        <!--                    <th colspan="3">PRODUCCION 1ER TURNO</th>  
                        <th colspan="3">PRODUCCION 2DO TURNO</th>  -->
                        <th colspan="3">PRODUCCION DIARIA</th>  
                    </tr>
                    <tr>
                        <!--<th>Estim 12hrs</th>-->  
                        <th>(cm)</th>  
                        <th>(&mu;)</th>  <!--
                        <th>peso(kg)</th>  
                        <th>mts</th>  
                        <th>desp(kg)</th>  
                        <th>peso(kg)</th> 
                        <th>mts</th>  
                        <th>desp(kg)</th> -->
                        <th style="width: 100px">peso(kg)</th> 
                        <th style="width: 100px">mts</th>  
                        <!--<th>desp(kg)</th>--> 
                        <!--<th>Costo U</th>-->  
                    </tr>
                </thead>
                <?php
                $tpeso = 0;
                $tpeso0 = 0;
                $tdesp = 0;
                $tdesp0 = 0;
                $tmts = 0;
                $tmts0 = 0;
                $tpeso1 = 0;
                $tpeso2 = 0;
                $tdesp1 = 0;
                $tdesp2 = 0;
                $tmts1 = 0;
                $tmts2 = 0;
                $tcostom = 0;
                $oper1 = '';
                $oper2 = '';
                $fill = true;

                while ($rstMaq = pg_fetch_array($extrusoras)) {

                    if ($fill == true) {
                        $fill = !$fill;
                    } else {
                        $fill = true;
                    }
//                $cnsPedidos = $Set->rptExt01($date, $rstMaq['ext_id']);
                    $cnsPedidos = $Set->rptExt01($date,$rstMaq[id]);

                    if (pg_num_rows($cnsPedidos) == 0) {
//                    $cnsPedidos = $Set->rptExt1($date, $rstMaq['ext_id']);
                        $cnsPedidos = $Set->rptExt1($date,$rstMaq[id]);
                    }

//                $rstPedidos1 = pg_fetch_array($Set->rptExt1($date, $rstMaq['ext_id']));
                    $rstPedidos1 = pg_fetch_array($Set->rptExt1($date,$rstMaq[id]));
                    $Maquina = '';
                    $cn = 0;
                    $cmb = pg_num_rows($cnsPedidos);
                    $Maquina = $rstMaq['maq_a'];
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
                <!--                        <td>-</td> 
                            <td>-</td> 
                            <td>-</td> 
                            <td>-</td> 
                            <td>-</td> 
                            <td>-</td> 
                            <td>-</td> 
                            <td>-</td> 
                            <td>-</td> 
                            <td>-</td> -->
                        </tr>
                        <?php
                    } else {
                        //*******Si tiene Pedidos*********************************************************
                        while ($rstPedidos = pg_fetch_array($cnsPedidos)) {
                            //*********Calculo Ancho de Extrusion
                            $rst = pg_fetch_array($Set->listaPedidosCodigo($rstPedidos['ord_num_orden']));
//                        $sello = $rst['ord_pedido_sello'];
//                        $tipoExt = $rst['ord_pedido_tipo_sello'];
//                        $carriles = $rst['ord_pedido_carriles'];
//                        if ($sello == 2) { //lateral (t) fondo (f)
//                            if ($tipoExt == 't') { //lamina (f) manga(t)
//                                $a = ($rst['pro_largo'] + $rst['pro_solapa'] + $rst['pro_fuelle_fondo']) + ($rst['pro_solapa_simple'] + $rst['pro_lengueta'] + $rst['pro_wicked']) * 0.5;
//                            } else {
//                                $a = ($rst['pro_largo'] + $rst['pro_solapa'] + $rst['pro_solapa_simple'] + $rst['pro_fuelle_fondo'] + $rst['pro_lengueta'] + $rst['pro_wicked']) * 2;
//                            }
//                        } else {
//                            if ($tipoExt == 't') { //lamina (f) manga(t)
//                                $a = $rst['pro_ancho'] + (2 * $rst['pro_fuelle_lateral']);
//                            } else {
//                                $a = ($rst['pro_ancho'] + (2 * $rst['pro_fuelle_lateral']) * 2);
//                            }
//                        }
//                        $ae = $a * $carriles - $rst['ord_perfilado']; //ancho a extruir
//                        //***********************************    
//                        $cn++;
//                        if ($cn == 1) {
//                            $Maquina = $rstMaq['ext_descripcion'];
//                            $Productividad = number_format($rstMaq['ext_productividad'] * 10);
//                            $h = $cmb * 7;
//                            $l = 1;
//                        } else {
//                            $Maquina = '';
//                            $Productividad = '';
//                            $h = 7;
//                            $l = 0;
//                        }
                            /////turnos
                            $rstDatos1 = pg_fetch_array($Set->rptExt2($date, $rstMaq['id'], $rstPedidos['ord_num_orden'], 1, $rstPedidos['ord_pro_id']));
//                        $rstDatos2 = pg_fetch_array($Set->rptExt2($date, $rstMaq['ext_id'], $rstPedidos['ord_pedido_codigo'], 2));
//                        if ($rstDatos1 == '') {
                            ?>    
                                                                <!--<tr>-->
                                                                    <!--<td><?php echo $Maquina ?></td>-->   
                                                                    <!--<td align="right"><?php echo $Productividad ?></td>-->   
                            <?php
//                                if (substr($rstDatos2['ord_pedido_codigo'], 0, 1) == 'W') {
//                                    $rstDatos2['ord_pedido_codigo'] = substr($rstDatos2['ord_pedido_codigo'], 1, 10);
//                                }
                            ?>
                                                                    <!--<td align="center"><?php echo $rstDatos2['ord_pedido_codigo'] ?></td>-->   
                                                                    <!--<td><?php echo substr($rstDatos2['cli_nombre'], 0, 8) ?></td>-->   
                                                                    <!--<td><?php echo substr($rstDatos2['pro_descripcion'], 0, 12) ?></td>-->   
                                                                    <!--<td align="right"><?php echo number_format($ae, 2) ?></td>-->   
                                                                    <!--<td align="right"><?php echo $rstDatos2['pro_espesor'] ?></td>-->   
                            <?php
//                                $Mp = new MateriaPrima();
//                                $n = 1;
//                                while ($n <= 12) {
//                                    $rstMP = pg_fetch_array($Mp->listaUnaMateriaPrima($rstDatos2['pro_t1_mp' . $n]));
//                                    $t = substr($rstMP['mat_prim_codigo'], 0, 2);
//                                    if (trim($rstMP['mat_prim_grupo']) == 5/* $t == 10 */) {
//                                        $colorManga = substr($rstMP['mat_prim_nombre'], 0, 9);
//                                        $n = 13;
//                                    } else {
//                                        $colorManga = 'Transparente';
//                                        $n++;
//                                    }
//                                }
//
//                                ////*******calculos para costo neto(kg)
//                                $cns_det = $Mp->lista_ingmp_det_cost($rstPedidos['ord_pedido_id']);
//                                $cost_mezcla = 0;
//                                $cost_tot = 0;
//                                $tot_consumo = 0;
//                                $tot_tot = 0;
//                                $tot_cost_mezcla = 0;
//                                $cost_primo = 0;
//                                $scrap = 0;
//                                $costo_neto = 0;
//                                $costo_netokg = 0;
//                                while ($rst = pg_fetch_array($cns_det)) {
//                                    $rst_sum = pg_fetch_array($Mp->suma_ingmp_ord($rstPedidos['ord_pedido_id']));
//                                    $porcentaje = $rst[ing_mp_cant] * 100 / $rst_sum[sum];
//                                    if ($rst[ing_mp_cost_uni] > 0) {
//                                        $cost_unit = $rst[ing_mp_cost_uni];
//                                    }
//                                    $cost_mezcla = ($porcentaje * $cost_unit) / 100;
//                                    $cost_tot = $rst[ing_mp_cant] * $cost_unit;
//                                    $tot_consumo = $tot_consumo + $rst[ing_mp_cant];
//                                    $tot_tot = $tot_tot + $cost_tot;
//                                    $tot_cost_mezcla = $tot_cost_mezcla + round($cost_mezcla, 2);
//                                    $cost_primo = round($tot_tot, 2);
//                                    $rst_dsp = pg_fetch_array($Mp->suma_peso_desperdicio($rstPedidos['ord_pedido_id']));
//                                    $scrap = round($rst_dsp[desperdicio], 2) * round($tot_cost_mezcla, 2);
//                                    $costo_neto = $scrap + $cost_primo;
//                                    $costo_netokg = $costo_neto / round($tot_consumo, 2);
//                                }
                            ?>
                                                                    <!--<td><?php echo $colorManga ?></td>--> 
                                                                    <!--<td align="right">0</td>--> 
                                                                    <!--<td align="right">0</td>--> 
                                                                    <!--<td align="right">0</td>--> 
                                                                    <!--<td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>--> 
                                                                    <!--<td align="right"><?php echo number_format(number_format($rstDatos2['mts'] * 1000)) ?></td>--> 
                                                                    <!--<td align="right"><?php echo number_format($rstDatos2['desp'], 1) ?></td>--> 
                                                                    <!--<td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>--> 
                                                                    <!--<td align="right"><?php echo number_format($rstDatos2['mts'] * 1000) ?></td>--> 
                                                                    <!--<td align="right"><?php echo number_format($rstDatos2['desp'], 1) ?></td>--> 
                                                                    <!--<td align="right"><?php echo number_format($costo_netokg, 2) ?></td>--> 
                            <!--</tr>-->
                            <?php
//                            $tpeso2 = $tpeso2 + $rstDatos2['peso'];
//                            $tdesp2 = $tdesp2 + $rstDatos2['desp'];
//                            $tpeso = $tpeso + $rstDatos2['peso'];
//                            $tdesp = $tdesp + $rstDatos2['desp'];
//                            $tdesp0 = $tdesp0 + $rstDatos2['desp'];
//                            $tpeso0 = $tpeso0 + $rstDatos2['peso'];
//                            $tmts2 = $tmts2 + $rstDatos2['mts'];
//                            $tmts0 = $tmts0 + $rstDatos2['mts'];
//                            $tcostom = $tcostom + ($costo_netokg);
//                        } else {
                            ?>
                            <tr>
                                <td><?php echo $Maquina ?></td>   
                                <!--<td align="right"><?php echo $Productividad ?></td>-->
                                <?php
//                                if (substr($rstDatos1['ord_pedido_codigo'], 0, 1) == 'W') {
//                                    $rstDatos1['ord_pedido_codigo'] = substr($rstDatos1['ord_pedido_codigo'], 1, 10);
//                                }
                                ?>
                                <td align="center"><?php echo $rstDatos1['ord_num_orden'] ?></td>
                                <td><?php echo substr($rstDatos1['cli_raz_social'], 0, 8) ?></td>
                                <td><?php echo $rstDatos1['pro_descripcion']?></td>
                                <td align="right"><?php echo number_format($rstDatos1['pro_ancho']*100, 2) ?></td>
                                <td align="right"><?php echo $rstDatos1['pro_espesor'] ?></td>
                                <?php
                                //Color de la manga        
//                                $Mp = new MateriaPrima();
//                                $n = 1;
//                                while ($n <= 12) {
//                                    $rstMP = pg_fetch_array($Mp->listaUnaMateriaPrima($rstDatos1['pro_t1_mp' . $n]));
//                                    if (trim($rstMP['mat_prim_grupo']) == 5) {
//                                        $colorManga = substr($rstMP['mat_prim_nombre'], 0, 9);
//                                        $n = 13;
//                                    } else {
//                                        $colorManga = 'Transparente';
//                                        $n++;
//                                    }
//                                }
//                                ////*******calculos para costo neto(kg)
//                                $cns_det = $Mp->lista_ingmp_det_cost($rstPedidos['ord_pedido_id']);
//                                $cost_mezcla = 0;
//                                $cost_tot = 0;
//                                $tot_consumo = 0;
//                                $tot_tot = 0;
//                                $tot_cost_mezcla = 0;
//                                $cost_primo = 0;
//                                $scrap = 0;
//                                $costo_neto = 0;
//                                $costo_netokg = 0;
//                                while ($rst = pg_fetch_array($cns_det)) {
//                                    $rst_sum = pg_fetch_array($Mp->suma_ingmp_ord($rstPedidos['ord_pedido_id']));
//                                    $porcentaje = $rst[ing_mp_cant] * 100 / $rst_sum[sum];
//                                    if ($rst[ing_mp_cost_uni] > 0) {
//                                        $cost_unit = $rst[ing_mp_cost_uni];
//                                    }
//                                    $cost_mezcla = ($porcentaje * $cost_unit) / 100;
//                                    $cost_tot = $rst[ing_mp_cant] * $cost_unit;
//                                    $tot_consumo = $tot_consumo + $rst[ing_mp_cant];
//                                    $tot_tot = $tot_tot + $cost_tot;
//                                    $tot_cost_mezcla = $tot_cost_mezcla + round($cost_mezcla, 2);
//                                    $cost_primo = round($tot_tot, 2);
//                                    $rst_dsp = pg_fetch_array($Mp->suma_peso_desperdicio($rstPedidos['ord_pedido_id']));
//                                    $scrap = round($rst_dsp[desperdicio], 2) * round($tot_cost_mezcla, 2);
//                                    $costo_neto = $scrap + $cost_primo;
//                                    $costo_netokg = $costo_neto / round($tot_consumo, 2);
//                                }
                                $rstDatos1['mts'] = ($rstDatos1[peso] /$rstDatos1[pro_gramaje]);
                                ?>
                                <!--<td><?php echo $colorManga ?></td>-->
                    <!--                                <td align="right"><?php echo number_format($rstDatos1['peso']) ?></td>
                                <td align="right"><?php echo number_format($rstDatos1['mts'] * 1000) ?></td>
                                <td align="right"><?php echo number_format($rstDatos1['desp'], 1) ?></td>
                                <td align="right"><?php echo number_format($rstDatos2['peso']) ?></td>
                                <td align="right"><?php echo number_format($rstDatos2['mts'] * 1000) ?></td>
                                <td align="right"><?php echo number_format($rstDatos2['desp'], 1) ?></td>-->
                                <td align="right"><?php echo number_format($rstDatos1['peso'], 2) ?></td>
                                <td align="right"><?php echo number_format($rstDatos1['mts'], 2) ?></td>
                                <!--<td align="right"><?php echo number_format($rstDatos1['desp'] + $rstDatos2['desp'], 1) ?></td>-->
                                <!--<td align="right"><?php echo number_format($costo_netokg, 2) ?></td>-->
                            </tr>
                            <!--//////producto2-->
                            <?php
                            if (!empty($rstPedidos[ord_pro_secundario])) {
                                $rst_pro2 = pg_fetch_array($Set->lista_un_producto($rstPedidos['ord_pro_secundario']));
                                $rstDatos2 = pg_fetch_array($Set->rptExt3($date, $rstMaq[id], $rstPedidos['ord_num_orden'], 1, $rstPedidos['ord_pro_id']));
                                ?>
                                <tr>
                                    <td><?php echo $Maquina ?></td>   
                                    <td align="center"><?php echo $rstDatos1['ord_num_orden'] ?></td>
                                    <td><?php echo substr($rstDatos1['cli_raz_social'], 0, 8) ?></td>
                                    <td><?php echo $rst_pro2['pro_descripcion'] ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['pro_ancho']*100, 2) ?></td>
                                    <td align="right"><?php echo $rstDatos2['pro_espesor'] ?></td>
                                    <?php
                                    $rstDatos2['mts'] = ($rstDatos2[peso]) / ($rstDatos2[pro_gramaje]);
                                    ?>
                                    <td align="right"><?php echo number_format($rstDatos2['peso'], 2) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos2['mts'], 2) ?></td>
                                </tr>
                                <?php
                            }
                            //////producto3-->
                            if (!empty($rstPedidos[ord_pro3])) {
                                $rst_pro3 = pg_fetch_array($Set->lista_un_producto($rstPedidos['ord_pro3']));
                                $rstDatos3 = pg_fetch_array($Set->rptExt4($date, $rstMaq[id], $rstPedidos['ord_num_orden'], 1, $rstPedidos['ord_pro_id']));
                                ?>
                                <tr>
                                    <td><?php echo $Maquina ?></td>   
                                    <td align="center"><?php echo $rstDatos1['ord_num_orden'] ?></td>
                                    <td><?php echo substr($rstDatos1['cli_raz_social'], 0, 8) ?></td>
                                    <td><?php echo $rst_pro3['pro_descripcion'] ?></td>
                                    <td align="right"><?php echo number_format($rst_pro3['pro_ancho']*100, 2) ?></td>
                                    <td align="right"><?php echo $rstDatos3['pro_espesor'] ?></td>
                                    <?php
                                    $rstDatos3['mts'] = ($rstDatos3[peso] ) / ($rstDatos3[pro_gramaje]);
                                    ?>
                                    <td align="right"><?php echo number_format($rstDatos3['peso'], 2) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos3['mts'], 2) ?></td>
                                </tr>
                                <?php
                            }
                            //////producto4-->
                            if (!empty($rstPedidos[ord_pro4])) {
                                $rst_pro4 = pg_fetch_array($Set->lista_un_producto($rstPedidos['ord_pro4']));
                                $rstDatos4 = pg_fetch_array($Set->rptExt5($date, $rstMaq[id], $rstPedidos['ord_num_orden'], 1, $rstPedidos['ord_pro_id']));
                                ?>
                                <tr>
                                    <td><?php echo $Maquina ?></td>   
                                    <td align="center"><?php echo $rstDatos1['ord_num_orden'] ?></td>
                                    <td><?php echo substr($rstDatos1['cli_raz_social'], 0, 8) ?></td>
                                    <td><?php echo $rst_pro4['pro_descripcion'] ?></td>
                                    <td align="right"><?php echo number_format($rst_pro4['pro_ancho']*100, 2) ?></td>
                                    <td align="right"><?php echo $rstDatos4['pro_espesor']?></td>
                                    <?php
                                    $rstDatos4['mts'] = ($rstDatos4[peso]) / ($rstDatos4[pro_gramaje]);
                                    ?>
                                    <td align="right"><?php echo number_format($rstDatos4['peso'], 2) ?></td>
                                    <td align="right"><?php echo number_format($rstDatos4['mts'], 2) ?></td>
                                </tr>
                                <?php
                            }
                            $tpeso1 = $tpeso1 + $rstDatos1['peso'];
                            $tpeso2 = $tpeso2 + $rstDatos2['peso'];
                            $tdesp1 = $tdesp1 + $rstDatos1['desp'];
                            $tdesp2 = $tdesp2 + $rstDatos2['desp'];
                            $tmts1 = $tmts1 + $rstDatos1['mts'];
                            $tmts2 = $tmts2 + $rstDatos2['mts'];
                            $tpeso = $tpeso + $rstDatos1['peso'] + $rstDatos2['peso'] + $rstDatos3['peso'] + $rstDatos4['peso'];
                            $tdesp = $tdesp + $rstDatos1['desp'] + $rstDatos2['desp'];
                            $tpeso0 = $tpeso0 + $rstDatos1['peso'] + $rstDatos2['peso'] + $rstDatos3['peso'] + $rstDatos4['peso'];
                            $tdesp0 = $tdesp0 + $rstDatos1['desp'] + $rstDatos2['desp'];
                            $tmts0 = $tmts0 + $rstDatos1['mts'] + $rstDatos2['mts'] + $rstDatos3['mts'] + $rstDatos4['mts'];
                            $tcostom = $tcostom + ($costo_netokg);
                            $rstDatos1['peso'] = 0;
                            $rstDatos2['peso'] = 0;
                            $rstDatos3['peso'] = 0;
                            $rstDatos4['peso'] = 0;
                            $rstDatos1['mts'] = 0;
                            $rstDatos2['mts'] = 0;
                            $rstDatos3['mts'] = 0;
                            $rstDatos4['mts'] = 0;
                        }
                    }
                }
//            }
                ?>
                <tr>
                    <td style="font-weight: bold" colspan="4">Tot.Prod.Diaria:</td>
                    <td style="font-weight: bold" colspan="2" align="right"><?php echo number_format($tpeso, 2) . '(kg)   ' . number_format($tmts0, 2) . ' mts' ?></td>
                    <!--<td style="font-weight: bold" colspan="3">Tot.Produccion Turnos:</td>-->
        <!--                <td style="font-weight: bold" align="right"><?php echo number_format($tpeso1) ?></td>
                    <td style="font-weight: bold" align="right"><?php echo number_format($tmts1, 2) ?></td>
                    <td style="font-weight: bold" align="right"><?php echo number_format($tdesp1, 1) ?></td>
                    <td style="font-weight: bold" align="right"><?php echo number_format($tpeso2) ?></td>
                    <td style="font-weight: bold" align="right"><?php echo number_format($tmts2 * 1000) ?></td>
                    <td style="font-weight: bold" align="right"><?php echo number_format($tdesp2, 1) ?></td>-->
                    <td style="font-weight: bold" align="right"><?php echo number_format($tpeso0, 2) ?></td>
                    <td style="font-weight: bold" align="right"><?php echo number_format($tmts0, 2) ?></td>
        <!--                <td style="font-weight: bold" align="right"><?php echo number_format($tdesp0, 1) ?></td>
                    <td style="font-weight: bold" align="right"><?php echo number_format($tcostom, 2) ?></td>-->
                </tr>
            </table>
            <?php
        }

        function rptExtrusionResumen($sec, $from, $until) {
//        $Seccion = new Secciones();
//        $Maq = new Maquinas();
//        $Extrusion = new Extrusion();
//        $Impresion = new Impresion();
//        $Sellado = new Sellado();
//        $Mp = new MateriaPrima();
            $Set = new Produccion_reportes();
//        $rstSec = pg_fetch_array($Seccion->listaUnaSecciones($sec));
            $cnsMaq = $Set->listaExtrusoras();
            if (pg_num_rows($cnsMaq) > 0) {
                ?>
                <table align="left" id="dataTable" width="100%" border="1">
                    <thead >
                        <tr>
                            <td style="font-weight: bold" colspan="18" align="center" >POLIPACK</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold" colspan="18" align="center" >REPORTE DE PRODUCCION - RESUMEN OPERATIVO</td>
                            <td style="font-weight: bold" colspan="14" align="center" >DEPARTAMENTO DE EXTRUSION</td>
                            <td style="font-weight: bold" colspan="14">Desde: <?php echo $from ?> Hasta:<?php echo $until ?></td>
                        </tr>
                    </thead >
                    <tr>
                        <th>FECHA</th>
                        <th>DATOS</th>
                        <?php
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            ?>
                            <th><?php $rstMaq[ext_descripcion] ?></th>
                            <?php
                        }
                        ?>
                        <th>Total</th>

                        <?php
                        $cnsSecImpSell = $Seccion->listaSecByCaracteristica($sec, 'imp', 'sel');
                        ?>
                        <th colspan="<?php pg_num_rows($cnsSecImpSell) ?>">Salida de rollos</th>
                    </tr>
                    <tr>
                        <?php
                        while ($rstSecImpSell = pg_fetch_array($cnsSecImpSell)) {
                            ?>
                            <th><?php echo $rstSecImpSell[sec_nombre] ?></th>
                            <?php
                        }
                        ?>
                    </tr>
                    //Body***************************************************************************  
                    <?php
                    $d = intval(days($from, $until));
                    $n = 0;
                    while ($n <= $d) {
                        $d1 = explode('/', $from);
                        $f1 = date_create($d1[0] . '-' . $d1[1] . '-' . $d1[2]);
                        date_add($f1, date_interval_create_from_date_string($n . ' days'));
                        $dt = date_format($f1, 'd/m/Y');
                        //Consumo mp***************************************************************
                        //        $this->Cell(15,$h*5,$dt,1,0,'C');//Celda de fecha
                        //        $this->Cell(40,$h,'Consumo mp (kg)',1,0,'L');
                        //        $cnsMaq=$Maq->listaExtrusorasSeccion($sec);
                        //        $tot=0;
                        //        $date=$dt;
                        //        $cant=pg_fetch_array($Mp->listarSumaIngresoMateriaPrimaFecha($sec,$date,$date));        
                        //        while($rstMaq=pg_fetch_array($cnsMaq))
                        //        {
                        //            $this->Cell($w,$h,'-',1,0,'C');                            
                        //        }            
                        //        $this->Cell($w,$h,number_format($cant[sum]),1,0,'R');                            
                        //        $cnsSecImpSell=$Seccion->listaSecByCaracteristica($sec,'imp','sel');      
                        //        while($rstSecImpSell=pg_fetch_array($cnsSecImpSell))
                        //        {
                        //            $this->Cell($w,$h,'-',1,0,'C');                            
                        //        } 
                        //        $this->Ln();  
                        //        //Consumo desp-rec***************************************************************        
                        //        $this->Cell($w*1.5,$h,'',0,0,'C');//Celda en blanco
                        //        $this->Cell($w*4,$h,'Consumo Desp-Rec (kg)',1,0,'L');
                        //        $cnsMaq=$Maq->listaExtrusorasSeccion($sec);
                        //        $tot=0;
                        //        $rstReciclado=pg_fetch_array($Mp->listarSumaReciclado($sec,$date,$date));//Ingreso de Reciclado a Extrusion de Otra Area
                        //        $rstReprocesadoOut=pg_fetch_array($Mp->listarSumaReprocesadoOut($sec,$date,$date));//Ingreso de Reciclado a Extrusion de Otra Area
                        //        $rstReprocesadoIn=pg_fetch_array($Mp->listarSumaReprocesadoIn($sec,$date,$date));//04 proc=sec dest=sec
                        //        $consExt=$rstReciclado[sum]+$rstReprocesadoOut[sum]+$rstReprocesadoIn[sum];//Consumo REC REP
                        //        while($rstMaq=pg_fetch_array($cnsMaq))
                        //        {
                        //            $this->Cell($w,$h,'-',1,0,'C');                            
                        //        }            
                        //        $this->Cell($w,$h,number_format($consExt),1,0,'R');                            
                        //        $cnsSecImpSell=$Seccion->listaSecByCaracteristica($sec,'imp','sel');      
                        //        while($rstSecImpSell=pg_fetch_array($cnsSecImpSell))
                        //        {
                        //            $this->Cell($w,$h,'-',1,0,'C');                            
                        //        } 
                        //        $this->Ln();
                        //        //Produccion kg***************************************************************        
                        //        $this->Cell($w*1.5,$h,'',0,0,'C');//Celda en blanco
                        //        $this->Cell($w*4,$h,'Produccion (kg)',1,0,'L');
                        //        $cnsMaq=$Maq->listaExtrusorasSeccion($sec);
                        //        $tot=0;
                        //        while($rstMaq=pg_fetch_array($cnsMaq))
                        //        {
                        //            $cant=pg_fetch_array($Extrusion->listaExtrusionByMaqDate($rstMaq[ext_id],$date,$date));
                        //            $this->Cell($w,$h,number_format($cant[sum]),1,0,'R');                            
                        //            $tot=$tot+$cant[sum];            
                        //        }            
                        //        $this->Cell($w,$h,number_format($tot),1,0,'R');                            
                        //        $cnsSecImpSell=$Seccion->listaSecByCaracteristica($sec,'imp','sel');      
                        //        while($rstSecImpSell=pg_fetch_array($cnsSecImpSell))
                        //        {
                        //            $sal_rollo=pg_fetch_array($Extrusion->listaExtrusionBySecDate($sec,$rstSecImpSell[sec_id],$date));
                        //            $this->Cell($w,$h,$sal_rollo[sum],1,0,'R');                            
                        //        } 
                        //        $this->Ln();
                        //        //Produccion mt***************************************************************        
                        //        $this->Cell($w*1.5,$h,'',0,0,'C');//Celda en blanco
                        //        $this->Cell($w*4,$h,'Produccion (m)',1,0,'L');
                        //        $cnsMaq=$Maq->listaExtrusorasSeccion($sec);
                        //        $tot=0;
                        //        while($rstMaq=pg_fetch_array($cnsMaq))
                        //        {
                        //            $cant=pg_fetch_array($Extrusion->listaExtrusionByMaqDate($rstMaq[ext_id],$date,$date));
                        //            $this->Cell($w,$h,number_format($cant[mt]*1000),1,0,'R');                            
                        //            $tot=$tot+($cant[mt]*1000);            
                        //        }            
                        //        $this->Cell($w,$h,number_format($tot),1,0,'R');                            
                        //        $cnsSecImpSell=$Seccion->listaSecByCaracteristica($sec,'imp','sel');      
                        //        while($rstSecImpSell=pg_fetch_array($cnsSecImpSell))
                        //        {
                        //            $this->Cell($w,$h,'-',1,0,'C');                            
                        //        } 
                        //        $this->Ln();
                        //        //Desp kg***************************************************************        
                        //        $this->Cell($w*1.5,$h,'',0,0,'L');//Celda en blanco
                        //        $this->Cell($w*4,$h,'Desperdicio (kg)',1,0,'L');
                        //        $cnsMaq=$Maq->listaExtrusorasSeccion($sec);
                        //        $tot=0;
                        //        while($rstMaq=pg_fetch_array($cnsMaq))
                        //        {
                        //            $cant=pg_fetch_array($Mp->listDespExtrusionByMaq($rstMaq[ext_id],$date,$date));//Total Desperdicio de Extrusion
                        //            $this->Cell($w,$h,number_format($cant[sum]),1,0,'R');                            
                        //            $tot=$tot+$cant[sum];            
                        //        }            
                        //        $this->Cell($w,$h,number_format($tot),1,0,'R');                            
                        //        $cnsSecImpSell=$Seccion->listaSecByCaracteristica($sec,'imp','sel');      
                        //        while($rstSecImpSell=pg_fetch_array($cnsSecImpSell))
                        //        {
                        //            $this->Cell($w,$h,'-',1,0,'C');                            
                        //        } 
                        //        $this->Ln();
                        //            
                        $n++;
                    }
                    //Totales kg***************************************************************  
                    ?>
                    <tr>
                        <th>CONSUMO MP x MAQUINA (kg)</th>
                    </tr>
                    <tr>
                        <th>CONSUMO REC x MAQUINA (kg)</th>
                    </tr>
                    <!--Total Produccion / maq kg***************************************************************-->        
                    <tr>
                        <th>PRODUCCION POR MAQ (kg)</th>

                        <?php
                        $cnsMaq = $Maq->listaExtrusorasSeccion($sec);
                        $tot = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $cant = pg_fetch_array($Extrusion->listaExtrusionByMaqDate($rstMaq[ext_id], $from, $until));
                            ?>
                            <td align="right"><?php echo number_format($cant[sum]) ?></td>
                            <?php
                            $tot = $tot + $cant[sum];
                        }
                        ?>
                        <td align="right"><?php echo number_format($tot) ?></td>
                        <?php
                        $cnsSecImpSell = $Seccion->listaSecByCaracteristica($sec, 'imp', 'sel');
                        while ($rstSecImpSell = pg_fetch_array($cnsSecImpSell)) {
                            ?>
                            <td align="center">-</td>
                            <?php
                        }
                        ?>
                    </tr>
                    <!--//Total Produccion / maq mt***************************************************************-->        
                    <tr>
                        <th>PRODUCCION POR MAQ (m)</th>
                        <?php
                        $cnsMaq = $Maq->listaExtrusorasSeccion($sec);
                        $tot = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $cant = pg_fetch_array($Extrusion->listaExtrusionByMaqDate($rstMaq[ext_id], $from, $until));
                            ?>
                            <td align="right"><?php echo number_format($cant[mt] * 1000) ?></td>
                            <?php
                            $tot = $tot + ($cant[mt] * 1000);
                        }
                        ?>
                        <td align="right"><?php echo number_format($tot) ?></td>
                        <?php
                        $cnsSecImpSell = $Seccion->listaSecByCaracteristica($sec, 'imp', 'sel');
                        while ($rstSecImpSell = pg_fetch_array($cnsSecImpSell)) {
                            ?> 
                            <td align="center">-</td>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <!--//Total desperdicio / maq kg***************************************************************-->        
                        <th> DESPERDICIO POR MAQ (kg)</th> 
                        <?php
                        $cnsMaq = $Maq->listaExtrusorasSeccion($sec);
                        $tot = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $cant = pg_fetch_array($Mp->listDespExtrusionByMaq($rstMaq[ext_id], $from, $until)); //Total Desperdicio de Extrusion
                            ?>
                            <td align="right"><?php echo number_format($cant[sum]) ?></td>
                            <?php
                            $tot = $tot + ($cant[sum]);
                        }
                        ?>
                        <td align="right"><?php echo number_format($tot) ?></td>
                        <?php
                        $cnsSecImpSell = $Seccion->listaSecByCaracteristica($sec, 'imp', 'sel');
                        while ($rstSecImpSell = pg_fetch_array($cnsSecImpSell)) {
                            ?> 
                            <td align="center">-</td>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <!--//Capacidad Teorica**************************************************************-->        
                        <th>CAPACIDAD TEORICA POR MAQ (kg/h)</th>
                        <?php
                        $cnsMaq = $Maq->listaExtrusorasSeccion($sec);
                        $tot = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            ?>
                            <td align="right"><?php echo $rstMaq[ext_productividad] ?></td>
                            <?php
                            $tot = $tot + $rstMaq[ext_productividad];
                        }
                        ?>
                        <td align="right"><?php echo number_format($tot) ?></td>
                        <?php
                        $cnsSecImpSell = $Seccion->listaSecByCaracteristica($sec, 'imp', 'sel');
                        while ($rstSecImpSell = pg_fetch_array($cnsSecImpSell)) {
                            ?>
                            <td align="center">-</td>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <!--//Capacidad Real**************************************************************-->        
                        <th>CAPACIDAD REAL POR MAQ (kg/h)</th>
                        <?php
                        $cnsMaq = $Maq->listaExtrusorasSeccion($sec);
                        $tot = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $cant = pg_fetch_array($Extrusion->listaExtrusionByMaqDate($rstMaq[ext_id], $from, $until));
                            ?>
                            <td align="right"><?php echo number_format($cant[sum] / (($d + 1) * 24)) ?></td>
                            <?php
                            $tot = $tot + ($cant[sum] / (($d + 1) * 24));
                        }
                        ?>
                        <td align="right"><?php echo number_format($tot) ?></td>
                        <?php
                        $cnsSecImpSell = $Seccion->listaSecByCaracteristica($sec, 'imp', 'sel');
                        while ($rstSecImpSell = pg_fetch_array($cnsSecImpSell)) {
                            ?>
                            <td align="center">-</td>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <!--//EFICIENCIA POR CAPACIDAD**************************************************************-->        
                        <th>EFICIENCIA POR CAPACIDAD</th>
                        <?php
                        $cnsMaq = $Maq->listaExtrusorasSeccion($sec);
                        $tot = 0;
                        $div = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $div++;
                            $cant = pg_fetch_array($Extrusion->listaExtrusionByMaqDate($rstMaq[ext_id], $from, $until));
                            ?>
                            <td align="right"><?php echo number_format(($cant[sum] / (($d + 1) * 24)) / $rstMaq[ext_productividad], 2) ?></td>
                            <?php
                            $tot = $tot + (($cant[sum] / (($d + 1) * 24)) / $rstMaq[ext_productividad]);
                        }
                        ?>
                        <td align="right"><?php echo number_format($tot / $div, 2) ?></td>
                        <?php
                        $cnsSecImpSell = $Seccion->listaSecByCaracteristica($sec, 'imp', 'sel');
                        while ($rstSecImpSell = pg_fetch_array($cnsSecImpSell)) {
                            ?>  
                            <td align="center">-</td>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <!--//EFICIENCIA**************************************************************-->        
                        <th>EFICIENCIA</th>
                        <?php
                        $cnsMaq = $Maq->listaExtrusorasSeccion($sec);
                        $tot = 0;
                        $div = 0;
                        while ($rstMaq = pg_fetch_array($cnsMaq)) {
                            $div++;
                            $cant = pg_fetch_array($Extrusion->listaExtrusionByMaqDate($rstMaq[ext_id], $from, $until));
                            ?>
                            <td align="right"><?php echo number_format(($cant[sum] / (($d + 1) * 24)) / $rstMaq[ext_productividad] * 100, 2) . '%' ?></td>
                            <?php
                            $tot = $tot + (($cant[sum] / (($d + 1) * 24)) / $rstMaq[ext_productividad] * 100);
                        }
                        ?>
                        <td align="right"><?php echo number_format($tot / $div, 2) . '%' ?></td>
                        <?php
                        $cnsSecImpSell = $Seccion->listaSecByCaracteristica($sec, 'imp', 'sel');
                        while ($rstSecImpSell = pg_fetch_array($cnsSecImpSell)) {
                            ?>
                            <td align="center">-</td>
                            <?php
                        }
                        ?>
                    </tr>
                    //********************************************************************        
                    <tr>
                        <td>Dias Laborados: <?php echo ($d + 1) ?> </td>
                    </tr>
                    <?php
                } else {
                    ?>
                    <tr>
                        <td>No existen EXTRUSORAS en la seccion <?php echo $rstSec['sec_descricpion'] ?></td>
                    </tr>
                </table>
                <?php
            }
        }

        function rptSellado($selladoras, $date, $secId) {
//        $Seccion = new Secciones();
//        $rstSeccion = pg_fetch_array($Seccion->listaUnaSecciones($secId));
            $Set = new Produccion_reportes();
//        $Pedido = new Pedido();
            ?>
            <table align="left" id="tbl" width="100%" border="1">
                <thead >
                    <tr>
                        <th style="font-weight: bold" colspan="11" align="center" >POLIPACK</th>
                    </tr>
                    <tr>
                        <th style="font-weight: bold" colspan="4" align="center" >DEPARTAMENTO DE SELLADO</th>
                        <th style="font-weight: bold" colspan="3" align="center" >REPORTE DIARIO DE PRODUCCION</th>
                        <th style="font-weight: bold" colspan="4" align="center" >FECHA: <?php echo $date ?></h>
                    </tr>
                    <tr>
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
                </thead>
            </table>
            <?php
        }

        function rptMatPrimDialy($from, $until) {
            $Sec = new Secciones();
            $Mp = new MateriaPrima();
            $cnsSec = $Sec->listaSeccionGerenciasDivisionExtrusoras();
            $cnsSec0 = $Sec->listaSeccionGerenciasDivisionExtrusoras();
            $gt = pg_fetch_array($Mp->listaSumaTotalIMPbyDate($from, $until));
            $numc = pg_num_rows($Sec->listaSeccionGerenciasDivisionExtrusoras());
            ?>
            <table align="left" id="dataTable" width="100%" border="1">
                <thead >
                    <tr>
                        <th colspan="<?php echo ($numc * 2) + 4 ?>" align="center" >REPORTE DIARIO DE CONSUMO DE MATERIA PRIMA POR SECCION </th>
                    </tr>
                    <tr>
                        <td colspan="<?php echo ($numc * 2) + 4 ?>">Fecha: <?php echo $from ?></td>
                    </tr>
                    <tr>
                        <th rowspan="2"> MATERIAL</th>
                        <?php
                        while ($rstSec = pg_fetch_array($cnsSec)) {
                            $cnt = $cnt + 2;
                            ?>
                            <th colspan="2"><?php echo$rstSec[sec_descricpion] ?></th>
                            <?php
                        }
                        ?>
                        <th colspan="2">TOTAL</th>
                    </tr>
                    <tr>
                        <?php
                        $cn = $cnt;
                        while (($cnt + 2) > 0) {
                            ?>  

                            <th>kg</th>
                            <th>%</th>
                            <?php
                            $cnt = $cnt - 2;
                        }
                        ?>  
                    </tr>
                    <?php
                    //*****Datos***********
                    $cnsMp = $Mp->listaReporteMP();
                    $mptp = '';
                    while ($rstMp = pg_fetch_array($cnsMp)) {
                        $rstIMP = pg_fetch_array($Mp->listaSumIngMPbydatePro($from, $until, $rstMp[mat_prim_id]));
                        if ($rstIMP[sum] > 0) {
                            if ($mptp != $rstMp[mat_prim_tipo] && !empty($mptp)) {
                                ?>  
                                <tr>
                                    <th>TOTAL <?php echo $mptp ?></th>
                                    <?php
                                    $cnsSec0 = $Sec->listaSeccionGerenciasDivisionExtrusoras();
                                    $t1 = 0;
                                    $t1p = 0;
                                    while ($rstSec = pg_fetch_array($cnsSec0)) {
                                        $tcmpsec = pg_fetch_array($Mp->listaSumaTotalIMPbyDateSec($from, $until, $rstSec[sec_id]));
                                        $tgroup = pg_fetch_array($Mp->listaSumIngMPbydatetoSec($from, $until, $mptp, $rstSec[sec_id]));
                                        $porc = number_format($tgroup[sum] * 100 / $tcmpsec[sum], 2);
                                        if ($tgroup[sum] == 0) {
                                            $cmp = '';
                                            $porc = '0';
                                        } else {
                                            $cmp = $tgroup[sum];
                                        }
                                        ?>
                                        <th align="right"><?php echo number_format($tgroup[sum]) ?></th>
                                        <th align="right"><?php echo $porc ?></th>
                                        <?php
                                        $t1 = $t1 + $tgroup[sum];
                                    }
                                    ?>
                                    <th align="right"><?php echo number_format($t1) ?></th>
                                    <th align="right"><?php echo number_format($t1 * 100 / $gt[sum], 2) ?></th>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td> <?php echo $rstMp[mat_prim_codigo] . '-' . $rstMp[mat_prim_nombre] ?></td>
                                <?php
                                $cnsSec0 = $Sec->listaSeccionGerenciasDivisionExtrusoras();
                                $t1 = 0;
                                $t1p = 0;
                                while ($rstSec = pg_fetch_array($cnsSec0)) {
                                    $tcmpsec = pg_fetch_array($Mp->listaSumaTotalIMPbyDateSec($from, $until, $rstSec[sec_id]));
                                    $rstCMP = pg_fetch_array($Mp->listaSumIngMPbydateSecPro($from, $until, $rstMp[mat_prim_id], $rstSec[sec_id]));
                                    $porc = number_format($rstCMP[sum] * 100 / $tcmpsec[sum], 2);
                                    if ($rstCMP[sum] == 0) {
                                        $cmp = '';
                                        $porc = '';
                                    } else {
                                        $cmp = $rstCMP[sum];
                                    }
                                    ?>
                                    <td align="right"><?php echo number_format($cmp) ?></td>
                                    <td align="right"><?php echo $porc ?></td>
                                    <?php
                                    $t1 = $t1 + $rstCMP[sum];
                                }
                                ?>
                                <td align="right"><?php echo number_format($t1) ?></td>
                                <td align="right"><?php echo number_format($t1 * 100 / $gt[sum], 2) ?></td>
                                <?php
                                $mptp = $rstMp[mat_prim_tipo];
                            }
                        }
                        ?>
                    </tr>
                    <tr>
                        <th><?php echo'TOTAL ' . $mptp ?></th>
                        <?php
                        $cnsSec0 = $Sec->listaSeccionGerenciasDivisionExtrusoras();
                        $t1 = 0;
                        $t1p = 0;
                        while ($rstSec = pg_fetch_array($cnsSec0)) {
                            $tcmpsec = pg_fetch_array($Mp->listaSumaTotalIMPbyDateSec($from, $until, $rstSec[sec_id]));
                            $tgroup = pg_fetch_array($Mp->listaSumIngMPbydatetoSec($from, $until, $mptp, $rstSec[sec_id]));
                            $porc = number_format($tgroup[sum] * 100 / $tcmpsec[sum], 2);
                            if ($tgroup[sum] == 0) {
                                $cmp = '';
                                $porc = '0';
                            } else {
                                $cmp = $tgroup[sum];
                            }
                            ?>
                            <th align="right"><?php echo number_format($tgroup[sum]) ?></th>
                            <th align="right"><?php echo $porc ?></th>
                            <?php
                            $t1 = $t1 + $tgroup[sum];
                        }
                        ?>
                        <th align="right"><?php echo number_format($t1) ?></th>
                        <th align="right"><?php echo number_format($t1 * 100 / $gt[sum], 2) ?></th>
                    </tr>
                    <tr>
                        <!--//*********************-->
                        <th>TOTAL A EXTRUSION:</th>
                        <?php
                        $cnsSec0 = $Sec->listaSeccionGerenciasDivisionExtrusoras();
                        $t1 = 0;
                        $t1p = 0;
                        while ($rstSec = pg_fetch_array($cnsSec0)) {
                            $tcmpsec = pg_fetch_array($Mp->listaSumaTotalIMPbyDateSec($from, $until, $rstSec[sec_id]));
                            ?>
                            <th align="right"><?php echo number_format($tcmpsec[sum]) ?></th>
                            <th align="right"><?php echo '100' ?></th>
                            <?php
                            $t1 = $t1 + $tcmpsec[sum];
                        }
                        ?>
                        <th align="right" colspan="2"><?php echo number_format($t1) ?></th>
                    </tr>
            </table>
            <?php
        }

        function rptMatPrimCons($from, $until) {
            $Sec = new Secciones();
            $Mp = new MateriaPrima();
            $cnsSec = $Sec->listaSeccionesPolietileno();
            $cnsSec0 = $Sec->listaSeccionesPolietileno();
            $gt = pg_fetch_array($Mp->listaSumaTotalIMPbyDate($from, $until));
            ?>
            <table align="left" id="dataTable" width="100%" border="1">
                <thead >
                    <tr>
                        <th colspan="5" align="center" >REPORTE ACUMULADO DE CONSUMO DE MATERIA PRIMA POR SECCION</th>
                    </tr
                    <tr>
                        <th colspan="2" align="left" >Desde: <?php echo $from ?></th>
                        <th colspan="2" align="left" >Hasta: <?php echo $until ?></th>
                        <th align="left" ></th>
                    </tr
                    <tr>
                        <?php
                        $h = 5;
                        $cnt = 0;
                        $cn = 0;
                        ?>
                        <th rowspan="2">MATERIAL</th>
                        <?php
                        while ($rstSec = pg_fetch_array($cnsSec)) {
                            $cnt = $cnt + 2;
                            ?>
                            <th colspan="2"><?php echo $rstSec[sec_nombre] ?></th>
                            <?php
                        }
                        ?>
                        <th colspan="2">TOTAL</th>
                    </tr
                    <tr>
                        <?php
                        $cn = $cnt;
                        while (($cnt + 2) > 0) {
                            ?>   
                            <th>kg</th>
                            <th>%</th>
                            <?php
                            $cnt = $cnt - 2;
                        }
                        ?>   
                    </tr
                    <tr>
                        <!--//*****Datos***********-->
                        <?php
                        $cnsMp = $Mp->listaReporteMP();
                        $mptp = '';
                        while ($rstMp = pg_fetch_array($cnsMp)) {
                            ?> 
                            <?php
                            $rstIMP = pg_fetch_array($Mp->listaSumIngMPbydatePro($from, $until, $rstMp[mat_prim_id]));
                            if ($rstIMP[sum] > 0) {
                                if ($mptp != $rstMp[mat_prim_tipo] && !empty($mptp)) {
                                    ?>     
                                    <th align="left">TOTAL <?php echo $mptp ?></th>
                                    <?php
                                    $cnsSec0 = $Sec->listaSeccionesPolietileno();
                                    $t1 = 0;
                                    $t1p = 0;
                                    while ($rstSec = pg_fetch_array($cnsSec0)) {
                                        $tcmpsec = pg_fetch_array($Mp->listaSumaTotalIMPbyDateSec($from, $until, $rstSec[sec_id]));
                                        $tgroup = pg_fetch_array($Mp->listaSumIngMPbydatetoSec($from, $until, $mptp, $rstSec[sec_id]));
                                        $porc = number_format($tgroup[sum] * 100 / $tcmpsec[sum], 2);
                                        if ($tgroup[sum] == 0) {
                                            $cmp = '';
                                            $porc = '0';
                                        } else {
                                            $cmp = $tgroup[sum];
                                        }
                                        ?>  
                                        <th align="right"><?php echo number_format($tgroup[sum]) ?></th>
                                        <th align="right"> <?php echo $porc ?></th>
                                        <?php
                                        $t1 = $t1 + $tgroup[sum];
                                    }
                                    ?>
                                    <th align="right"><?php echo number_format($t1) ?></th>
                                    <th align="right"><?php echo number_format($t1 * 100 / $gt[sum], 2) ?></th>
                                </tr>
                                <tr>
                                    <?php
                                }
                                ?>
                                <td> <?php echo $rstMp[mat_prim_codigo] . '-' . $rstMp[mat_prim_nombre] ?></td>
                                <?php
                                $cnsSec0 = $Sec->listaSeccionesPolietileno();
                                $t1 = 0;
                                $t1p = 0;
                                while ($rstSec = pg_fetch_array($cnsSec0)) {
                                    $tcmpsec = pg_fetch_array($Mp->listaSumaTotalIMPbyDateSec($from, $until, $rstSec[sec_id]));
                                    $rstCMP = pg_fetch_array($Mp->listaSumIngMPbydateSecPro($from, $until, $rstMp[mat_prim_id], $rstSec[sec_id]));
                                    $porc = number_format($rstCMP[sum] * 100 / $tcmpsec[sum], 2);
                                    if ($rstCMP[sum] == 0) {
                                        $cmp = '';
                                        $porc = '';
                                    } else {
                                        $cmp = $rstCMP[sum];
                                    }
                                    ?>
                                    <td align="right"><?php echo number_format($cmp) ?></td>
                                    <td align="right"> <?php echo $porc ?></td>
                                    <?php
                                    $t1 = $t1 + $rstCMP[sum];
                                    //$t1p=$t1p+$porc;
                                }
                                ?>
                                <td align="right"><?php echo number_format($t1) ?></td>
                                <td align="right"><?php echo number_format($t1 * 100 / $gt[sum], 2) ?></td>
                            <tr>
                                <?php
                                $mptp = $rstMp[mat_prim_tipo];
                            }
                        }
                        ?>
                        <th align="left">TOTAL <?php echo $mptp ?></th>
                        <?php
                        $cnsSec0 = $Sec->listaSeccionesPolietileno();
                        $t1 = 0;
                        $t1p = 0;
                        while ($rstSec = pg_fetch_array($cnsSec0)) {
                            $tcmpsec = pg_fetch_array($Mp->listaSumaTotalIMPbyDateSec($from, $until, $rstSec[sec_id]));
                            $tgroup = pg_fetch_array($Mp->listaSumIngMPbydatetoSec($from, $until, $mptp, $rstSec[sec_id]));
                            $porc = number_format($tgroup[sum] * 100 / $tcmpsec[sum], 2);
                            if ($tgroup[sum] == 0) {
                                $cmp = '';
                                $porc = '0';
                            } else {
                                $cmp = $tgroup[sum];
                            }
                            ?>
                            <th align="right"><?php echo number_format($tgroup[sum]) ?></th>
                            <th align="right"><?php echo $porc ?></th>
                            <?php
                            $t1 = $t1 + $tgroup[sum];
                            //$t1p=$t1p+$porc;
                        }
                        ?>
                        <th align="right"><?php echo number_format($t1) ?></th>
                        <th align="right"><?php echo number_format($t1 * 100 / $gt[sum], 2) ?></th>
                    </tr>
                    <tr>

                        <!--//*********************-->
                        <th>TOTAL A EXTRUSION:</th>
                        <?php
                        $cnsSec0 = $Sec->listaSeccionesPolietileno();
                        $t1 = 0;
                        $t1p = 0;
                        while ($rstSec = pg_fetch_array($cnsSec0)) {
                            $tcmpsec = pg_fetch_array($Mp->listaSumaTotalIMPbyDateSec($from, $until, $rstSec[sec_id]));
                            ?>
                            <th align="right"><?php echo number_format($tcmpsec[sum]) ?></th>
                            <th align="right"><?php echo'100' ?></th>
                            <?php
                            $t1 = $t1 + $tcmpsec[sum];
                        }
                        ?>
                        <th colspan="2"><?php echo number_format($t1) ?></th>
                    </tr>
            </table>
            <?php
        }

        function rptGenralResumen($from, $until, $sec) {
            $Pedido = new Pedido();
            $Extrusion = new Extrusion();
            $Impresion = new Impresion();
            $Sellado = new Sellado();
            $Seccion = new Secciones();
            $MatPrim = new MateriaPrima();
            $Maq = new Maquinas();
            $rstSecc = pg_fetch_array($Seccion->listaUnaSecciones($sec));
            ?>
            <table align="left" id="dataTable" width="100%" border="1">
                <thead >
                    <tr>
                        <th colspan="28" align="center" >RESUMEN GENERAL DE FLUJO</th>
                    </tr
                    <tr>
                        <th colspan="14" align="left" >Desde : <?php echo $from ?>  ---   Hasta : <?php echo $until ?></th>
                        <th colspan="14" align="right" ><?php echo 'Seccion ==> ' . $rstSecc['sec_descricpion'] ?></th>
                    </tr
                    <tr>
                        <th colspan="10"> EXTRUSION</th>
                        <th colspan="7"> IMPRESION</th>
                        <th colspan="6"> SELLADO</th>
                        <th colspan="5"> DESPERDICIO RECICLADO</th>
                    </tr
                    <tr>
                        <th>DIA</th>
                        <th>MP</th>
                        <th>REC</th>
                        <th>REP</th>
                        <th>CONSUMO</th>
                        <th>PROD</th>
                        <th>DESP</th>
                        <th>SAL.ROLL</th>
                        <th>ROL A IMP</th>
                        <th>ROL A SELL</th>
                        <th>TINTAS</th>
                        <th>ENT ROLL</th>
                        <th>CONSUMO</th>
                        <th>PROD</th>
                        <th>DESP</th>
                        <th>SAL.ROLL</th>
                        <th>ROL A SELL</th>
                        <th>ENT ROLL</th>
                        <th>CONSUMO</th>
                        <th>PROD KG</th>
                        <th>DESP </th>
                        <th>FUNDAS</th>
                        <th>ROLLOS</th>
                        <th>T-DESP</th>
                        <th>CONSUMO</th>
                        <th>PROD</th>
                        <th>SAL REC </th>
                        <th>REC A EXT</th>
                    </tr>
                    <?php
                    $d = days($from, $until);
                    $n = 0;
                    //*********************************************************************************************************
                    $t_mp = 0;
                    $t_rc = 0;
                    $t_rp = 0;
                    $t_cns_e = 0;
                    $t_prod_e = 0;
                    $t_desp_e = 0;
                    $t_sr_e = 0;
                    $t_si_e = 0;
                    $t_ss_e = 0;
                    $t_tnt = 0;
                    $t_er_i = 0;
                    $t_cns_i = 0;
                    $t_prod_i = 0;
                    $t_desp_i = 0;
                    $t_sr_i = 0;
                    $t_ss_i = 0;
                    $t_er_s = 0;
                    $t_cns_s = 0;
                    $t_prod_s = 0;
                    $t_desp_s = 0;
                    $t_fnd = 0;
                    $t_roll = 0;
                    $t_ed = 0;
                    $t_cns_r = 0;
                    $t_prod_r = 0;
                    $t_sr_r = 0;
                    $t_re_r = 0;
                    while ($n <= intval($d)) {
                        $d1 = explode('/', $from);
                        $f1 = date_create($d1[0] . '-' . $d1[1] . '-' . $d1[2]);
                        date_add($f1, date_interval_create_from_date_string($n . ' days'));
                        $dt = date_format($f1, 'd/m/Y');
                        //***************************************************************************************************
                        $rstMateriaPrima = pg_fetch_array($MatPrim->listarSumaIngresoMateriaPrimaFecha($sec, $dt, $dt)); //Ingreso de Materia prima a Extrusion
                        $rstReciclado = pg_fetch_array($MatPrim->listarSumaDespRecMP($sec, $dt, $dt, 'REC')); //Ingreso de Reciclado a Extrusion de Otra Seccion
                        $rstReprocesadoOut = pg_fetch_array($MatPrim->listarSumaDespRecMP($sec, $dt, $dt, 'REP')); //Ingreso de Reciclado a Extrusion de Otra Area            
                        $cosExt = ($rstMateriaPrima[sum] + $rstReciclado[sum] + $rstReprocesadoOut[sum]);
                        $sumRegExt = pg_fetch_array($Extrusion->listaExtAcumFechaSec($dt, $dt, $sec)); //Produccion Extrusion                    
                        $totExtRec = pg_fetch_array($MatPrim->listDespExtrusion($sec, $dt, $dt)); //Total Desperdicio de Extrusion                
                        $totSalExt = pg_fetch_array($Extrusion->listaExtSalRollos($dt, $dt, $sec));
                        $totSalImp = pg_fetch_array($Extrusion->listSumaExt_a_Imp($dt, $dt, $sec));
                        $totSalSell = pg_fetch_array($Extrusion->listSumaExt_a_Sell($dt, $dt, $sec));
                        $cnsInTintas = pg_fetch_array($MatPrim->listaIngTintasByDateSec($sec, $dt, $dt));
                        $entRoll = pg_fetch_array($Extrusion->listaEntRollImp($dt, $dt, $sec));
                        $consImp = $entRoll[sum] + $totSalImp[sum];
                        $sumRegImp = pg_fetch_array($Impresion->listaImpAcumFechaSec($dt, $dt, $sec));
                        $totImpRec = pg_fetch_array($MatPrim->listDespImpresion($sec, $dt, $dt));
                        $totImpOut = pg_fetch_array($Impresion->listaSalRoll($dt, $dt, $sec));
                        $rstImpSell = pg_fetch_array($Impresion->listaImp_Sell_DateSec($dt, $dt, $sec));
                        $totEntSell = pg_fetch_array($Extrusion->EntrRollos_a_Sellado($dt, $dt, $sec));
                        $consSell = $totSalSell[sum] + $rstImpSell[sum] + $totEntSell[sum];


                        //$sumRegSell=pg_fetch_array($Sellado->listaProduccionByDateSec($dt,$dt,$sec));
                        $sumRegSellf = pg_fetch_array($Sellado->listaProdFundasByDateSec($dt, $dt, $sec));
                        $sumRegSellr = pg_fetch_array($Sellado->listaProdRollosByDateSec($dt, $dt, $sec));


                        $totSellRec = pg_fetch_array($MatPrim->listDespSellado($sec, $dt, $dt));
                        $totDespIng = pg_fetch_array($MatPrim->listDespIng($sec, $dt, $dt));
                        $consDesp = $totExtRec[sum] + $totImpRec[sum] + $totSellRec[sum] + $totDespIng[sum];
                        $totDespProd = pg_fetch_array($MatPrim->listDesp($sec, $dt, $dt));
                        $totRecOtraSec = pg_fetch_array($MatPrim->listRecicladoOtraSeccion($sec, $dt, $dt));
                        $rstReprocesadoIn = pg_fetch_array($MatPrim->listarSumaReprocesadoIn($sec, $dt, $dt));

                        $t_mp = $t_mp + $rstMateriaPrima[sum];
                        $t_rc = $t_rc + $rstReciclado[sum];
                        $t_rp = $t_rp + $rstReprocesadoOut[sum];
                        $t_cns_e = $t_cns_e + $cosExt;
                        $t_prod_e = $t_prod_e + $sumRegExt[sum];
                        $t_desp_e = $t_desp_e + $totExtRec[sum];
                        $t_sr_e = $t_sr_e + $totSalExt[sum];
                        $t_si_e = $t_si_e + $totSalImp[sum];
                        $t_ss_e = $t_ss_e + $totSalSell[sum];
                        $t_tnt = $t_tnt + $cnsInTintas[sum];
                        $t_er_i = $t_er_i + $entRoll[sum];
                        $t_cns_i = $t_cns_i + $consImp;
                        $t_prod_i = $t_prod_i + $sumRegImp[sum];
                        $t_desp_i = $t_desp_i + $totImpRec[sum];
                        $t_sr_i = $t_sr_i + $totImpOut[sum];
                        $t_ss_i = $t_ss_i + $rstImpSell[sum];
                        $t_er_s = $t_er_s + $totEntSell[sum];
                        $t_cns_s = $t_cns_s + $consSell;
                        $t_prod_s = $t_prod_s + ($sumRegSellf[peso] + $sumRegSellr[peso]);
                        $t_desp_s = $t_desp_s + $totSellRec[sum];
                        $t_fnd = $t_fnd + $sumRegSellf[fundas];
                        $t_roll = $t_roll + $sumRegSellr[rollos];
                        $t_ed = $t_ed + ($totExtRec[sum] + $totImpRec[sum] + $totSellRec[sum] + $totDespIng[sum]);
                        $t_cns_r = $t_cns_r + $consDesp;
                        $t_prod_r = $t_prod_r + $totDespProd[sum];
                        $t_sr_r = $t_sr_r + $totRecOtraSec[sum];
                        $t_re_r = $t_re_r + $rstReprocesadoIn[sum];
                        ?>
                        <!--//***************************************************************************************************-->
                        <!--//Extrusion-->          
                        <!--//                $this->SetFont('Arial', 'U', 5);-->

                        <!--//                $this->Cell($w, $h, $dt, 1, 0, 'L', false, 'rptReporteGeneralFlujoResumen.php?dt=' . $dt . '&sec=' . $sec);-->
                        <tr>    
                            <td ><?php echo $dt ?></td>    
                            <td align="right"><?php echo number_format($rstMateriaPrima[sum]) ?></td> 
                            <td align="right"><?php echo number_format($rstReciclado[sum]) ?></td> 
                            <td align="right"><?php echo number_format($rstReprocesadoOut[sum]) ?></td> 
                            <td align="right"><?php echo number_format($cosExt) ?></td> 
                            <td align="right"><?php echo number_format($sumRegExt[sum]) ?></td> 
                            <td align="right"><?php echo number_format($totExtRec[sum]) ?></td> 
                            <td align="right"><?php echo number_format($totSalExt[sum]) ?></td> 
                            <td align="right"><?php echo number_format($totSalImp[sum]) ?></td> 
                            <td align="right"><?php echo number_format($totSalSell[sum]) ?></td> 
                            <!--//Impresion-->          
                            <td align="right"><?php echo number_format($cnsInTintas[sum]) ?></td> 
                            <td align="right"><?php echo number_format($entRoll[sum]) ?></td> 
                            <td align="right"><?php echo number_format($consImp) ?></td> 
                            <td align="right"><?php echo number_format($sumRegImp[sum]) ?></td> 
                            <td align="right"><?php echo number_format($totImpRec[sum]) ?></td> 
                            <td align="right"><?php echo number_format($totImpOut[sum]) ?></td> 
                            <td align="right"><?php echo number_format($rstImpSell[sum]) ?></td> 
                            <!--//Sellado-->                
                            <td align="right"><?php echo number_format($totEntSell[sum]) ?></td> 
                            <td align="right"><?php echo number_format($consSell) ?></td> 
                            <td align="right"><?php echo number_format($sumRegSellf[peso] + $sumRegSellr[peso]) ?></td> 
                            <td align="right"><?php echo number_format($totSellRec[sum]) ?></td> 
                            <td align="right"><?php echo number_format($sumRegSellf[fundas]) ?></td> 
                            <td align="right"><?php echo number_format($sumRegSellr[rollos]) ?></td> 
                            <!--//Desp/Rec-->                
                            <!--//              $this->Cell($w, $h,  number_format($totDespIng[sum]),1,0,'R');//Ingreso de desperdicio-->
                            <td align="right"><?php echo number_format($totExtRec[sum] + $totImpRec[sum] + $totSellRec[sum] + $totDespIng[sum]) ?></td> 
                            <td>-</td> 
                            <td align="right"><?php echo number_format($totDespProd[sum]) ?></td> 
                            <td align="right"><?php echo number_format($totRecOtraSec[sum]) ?></td> 
                            <td align="right"><?php echo number_format($rstReprocesadoIn[sum]) ?></td> 
                        </tr>
                        <?php
                        $n++;
                    }
                    ?>
                    <tr>
                        <td>-</td>
                        <td align="right"><?php echo number_format($t_mp) ?></td>
                        <td align="right"><?php echo number_format($t_rc) ?></td>
                        <td align="right"><?php echo number_format($t_rp) ?></td>
                        <td align="right"><?php echo number_format($t_cns_e) ?></td>
                        <td align="right"><?php echo number_format($t_prod_e) ?></td>
                        <td align="right"><?php echo number_format($t_desp_e) ?></td>
                        <td align="right"><?php echo number_format($t_sr_e) ?></td>
                        <td align="right"><?php echo number_format($t_si_e) ?></td>
                        <td align="right"><?php echo number_format($t_ss_e) ?></td>
                        <td align="right"><?php echo number_format($t_tnt) ?></td>
                        <td align="right"><?php echo number_format($t_er_i) ?></td>
                        <td align="right"><?php echo number_format($t_cns_i) ?></td>
                        <td align="right"><?php echo number_format($t_prod_i) ?></td>
                        <td align="right"><?php echo number_format($t_desp_i) ?></td>
                        <td align="right"><?php echo number_format($t_sr_i) ?></td>
                        <td align="right"><?php echo number_format($t_ss_i) ?></td>
                        <td align="right"><?php echo number_format($t_er_s) ?></td>
                        <td align="right"><?php echo number_format($t_cns_s) ?></td>
                        <td align="right"><?php echo number_format($t_prod_s) ?></td>
                        <td align="right"><?php echo number_format($t_desp_s) ?></td>
                        <td align="right"><?php echo number_format($t_fnd) ?></td>
                        <td align="right"><?php echo number_format($t_roll) ?></td>
                        <td align="right"><?php echo number_format($t_ed) ?></td>
                        <td>-</td>
                        <td align="right"><?php echo number_format($t_prod_r) ?></td>
                        <td align="right"><?php echo number_format($t_sr_r) ?></td>
                        <td align="right"><?php echo number_format($t_re_r) ?></td>
                    </tr>
            </table>
            <?php
        }
        ?>

</body>
