<?php
session_start();
set_time_limit(0);
include_once '../Clases/clsClase_resultados.php';
include_once '../Includes/permisos.php';
//include_once '../Clases/clsRegistroExtrusion.php';
//include_once '../Clases/clsRegistroImpresion.php';
//include_once '../Clases/clsRegistroSellado.php';
//include_once '../Clases/clsMaquinas.php';
//include_once '../Clases/clsCalculos.php';
//include_once '../Includes/library2.php';
$Set = new Clase_resultados();
//$Ext = new Extrusion();
//$Imp = new Impresion();
//$Sell = new Sellado();
//$Calculo = new Calculos();
if (isset($_GET[search])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $txt = strtoupper($_GET[txt]);
    if (!empty($txt)) {
        $text = "AND(op.ord_num_orden like '%$txt%'
                OR  pr.pro_descripcion like '%$txt%')";
    } else {
        $text = "AND op.ord_fec_pedido BETWEEN '$desde' AND  '$hasta' ";
    }
    echo $text;
    $cnsPedido = $Set->Lista_Universal($text);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $txt = '';
}
//
//function horas($hora) {
//    $f = explode(' ', $hora);
//    if ($f[1] == 'days' || $f[1] == 'day') {
//        ///si existen dias
//        $hi = explode(':', $f[2]);
//        $d = $f[0] * 24;
//        $h = $hi[0] + $d;
//        $mn = $hi[1];
//        $s = $hi[2];
//    } else {
//        ///si no existen dias
//        $hi = explode(':', $f[0]);
//        $d = 0;
//        $h = $hi[0];
//        $mn = $hi[1];
//        $s = $hi[2];
//    }
//    return $h . ':' . $mn . ':' . $s;
//}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lista de Pedidos</title>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "desde", ifFormat: "%d/%m/%Y", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%d/%m/%Y", button: "im-hasta"});
            });

            function checkKey(key)
            {
                var unicode
                if (key.charCode) {
                    unicode = key.charCode;
                } else {
                    unicode = key.keyCode;
                }
                if (unicode == 13) {
                    reload()
                }
            }
            function valFecha(val, id)
            {
                v = val.split('-');
                if (val.length !== 10 || v[0].length !== 4 || v[1].length !== 2 || v[2].length !== 2)
                {
                    doc = document.getElementById(id);
                    doc.focus();
                    alert('Formato de fecha debe ser (yyyy-mm-dd)');
                    return false;
                }
            }

        </script>
        <style>

            body{
                margin-top:0px; 
            }
            .sms td{
                color: #3A87AD;
                background-color: #D9EDF7;
                border: solid 1px #BCE8F1;
                text-align:center;
                font-weight:bolder;
                text-transform:capitalize; 
            }
        </style>
    </head>
    <body>
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
                <center class="cont_title" >LISTA RESULTADOS</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        DESDE:<input type="text"  name="desde" value="<?php echo $desde ?>" id="desde" size="10" maxlength="10" onchange="valFecha(this.value, this.id)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')"/>
                        <img src="../img/calendar.png" id="im-desde"/>
                        HASTA:<input type="text"  name="hasta" value="<?php echo $hasta ?>" id="hasta" size="10" maxlength="10" onchange="valFecha(this.value, this.id)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')" />
                        <img src="../img/calendar.png" id="im-hasta"/>
                        ORDEN:
                        <input type="text" placeholder="Codigo" name="txt" value="<?php echo $txt ?>" style="text-transform:uppercase" id="txt" size="15" onkeypress="checkKey(event)" />
                        <input type="submit" name="search" value="Buscar" id="search"  />
                    </form>                            
                </center>
            </caption>
            <thead >
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Orden</th>
                    <th rowspan="2">Producto</th>
                    <th rowspan="2">Unid</th> 
                    <th rowspan="2">Fecha</th>
                    <th colspan="3">Extrusion</th>
                    <th colspan="3">Corte</th>
                    <th>Total</th>
                    <th rowspan="2">Prod. Und</th>
                    <th rowspan="2">Prod. Kg</th>
                    <th rowspan="2">Material</th>
                    <th rowspan="2">C/Unit</th>
                    <th rowspan="2">C/Tot</th>
                    <th rowspan="2">MOD</th>
                    <th rowspan="2">MOI</th>
                    <th rowspan="2">CDF</th>
                    <th rowspan="2">CIF</th>
                    <th rowspan="2">CV</th>
                    <th rowspan="2">TOTAL</th>
                    <th rowspan="2">C.UNITARIO</th>
                </tr>
                <tr>
                    <th>Dia Inicial</th>
                    <th>Dia Final</th>
                    <th>Tiempo</th>
                    <th>Dia Inicial</th>
                    <th>Dia Final</th>
                    <th>Tiempo</th>
                    <th>Tiempo</th>
                </tr>
                <?php
                if (pg_num_rows($cnsPedido) == 0) {
                ?>
                <tr class="sms" ><td colspan="26">NO EXISTEN DATOS EN ESTA CONSULTA</td></tr>
               <?php
                }
                ?>                
            </thead>

            <tbody id="tblbody">
                <?php
//                $n = 0;
                while ($rstPedido = pg_fetch_array($cnsPedido)) {
                    $n++;
//                    //*************Calculos*****************************

//                    include '../Reportes/includeCalculo.php';
////
//                    if ($rstPedido['ord_pedido_sello1'] != 3) {
//                        //******************Calculo Sellado*******************************           
//                        //Fundas       
//                        $rstSell = pg_fetch_array($Set->listaSellPedido($rstPedido['ord_pedido_id']));
//                        if ($rstSell[cfundas] == 0) {
//                            $cntSellf = 0;
//                            $pFundas = 0;
//                        } else {
//                            $cntSellf = $rstSell[cfundas] * $mltF;
//                            $pFundas = number_format($rstSell[pfundas], 2);
//                        }
//
//                        //Rollos              
//                        if ($rstSell[rollos] == 0) {
//                            $cntSell = 0;
//                            $rollos = 0;
//                        } else {
//                            $cntSell = $rstSell[rollos];
//                            $rollos = number_format($rstSell[pfundas], 2);
//                        }
//                        switch ($rstPedido['pro_uni']) {
//                            case 'FUNDAS':
//                                $rstPedido['pro_unidad'] = 'MILS';
//                                $cs = $cs / 1000;
//                                $sellA = number_format($cntSellf / 1000, 2); /// PRODUCCION CNT
//                                $sellB = $pFundas; /// PRODUCCION KG
//                                break;
//                            case 'ROLLOS':
//                                $rstPedido['pro_unidad'] = 'ROLL';
//                                $sellA = number_format($cntSell * $mltF, 2); /// PRODUCCION CNT
//                                $sellB = $rollos; /// PRODUCCION KG
//                                break;
//                            case 'KILOS':
//                                $rstPedido['pro_unidad'] = 'KIL';
//                                $sellA = number_format($cntSell * $mltF, 2); /// PRODUCCION CNT
//                                $sellB = $rollos; /// PRODUCCION KG
//                                break;
//                        }
//                        $cnt = $sellA;
//                        $kg = $sellB;
//                    } else {
////                         *********************Calculo Impresion****************************
//                        $rstImp = pg_fetch_array($Set->listaImpPedido($rstPedido['ord_pedido_id']));
//                        if ($rstImp[sum] == 0) {
//                            $kgImp = 0;
//                            $cntImp = 0;
//                        } else {
//                            $cntImp = $rstImp[count];
//                            $kgImp = $rstImp[sum];
//                        }
//
//                        if ($rstPedido['pro_colf_1'] != 0 || $rstPedido['pro_colr_1'] != 0) {
//                            $impA = number_format($cntImp, 2);
//                            $impB = number_format($tImp, 2);
//                            $cnt = 0;
//                            $kg = $impA;
//                        } else {
//                            //*****************Calculo Extrusion*********************************    
                            $rstExtrusion = pg_fetch_array($Set->listaExtPedido($rstPedido['ord_id'],$rstPedido['pro_id']));
                            if ($rstExtrusion[sum] == 0) {
                                $cntExtrusion = 0;
                                $kgExtrusion = 0;
                            } else {
                                $cntExtrusion = number_format($rstExtrusion[count], 2);
                                $kgExtrusion = number_format($rstExtrusion[sum], 2);
                            }

                            $cnt = $cntExtrusion;
                            $kg = $kgExtrusion;
//                        }
//                    }
//                    ////fecha inicial y final extrusion
                    $rst_ext = pg_fetch_array($Set->listaFechasExtrusion($rstPedido['ord_id'],$rstPedido['pro_id']));
                    if (!empty($rst_ext[fec_ini])) {
                        $fec_ext_ini = $rst_ext['fec_ini'];
                        $fec_ext_fin = $rst_ext['fec_fin'];
                    } else {
                        $fec_ext_ini = "";
                        $fec_ext_fin = "";
                    }
//                    $rst_imp = pg_fetch_array($Set->listaFechasImpresion($rstPedido['ord_pedido_id']));
//                    if (!empty($rst_imp[fec_ini])) {
//                        $fec_imp_ini = date('d/m/Y', strtotime($rst_imp['fec_ini']));
//                        $fec_imp_fin = date('d/m/Y', strtotime($rst_imp['fec_fin']));
//                    } else {
//                        $fec_imp_ini = "";
//                        $fec_imp_fin = "";
//                    }
//                    $rst_sell = pg_fetch_array($Set->listaFechasSellado($rstPedido['ord_pedido_id']));
//                    if (!empty($rst_sell[fec_ini])) {
//                        $fec_sell_ini = date('d/m/Y', strtotime($rst_sell['fec_ini']));
//                        $fec_sell_fin = date('d/m/Y', strtotime($rst_sell['fec_fin']));
//                    } else {
//                        $fec_sell_ini = "";
//                        $fec_sell_fin = "";
//                    }
//                    ////fecha hora anterior de extrusion
//                    $pri_hora = pg_fetch_array($Set->hora_anterior_extrusion($rst_ext['hor_ini']));
//                    $nfe = strtotime('-1 day', strtotime($rst_ext['fec_ini']));
//                    $nfe = date('Y-m-d', $nfe);
//                    if ($pri_hora[reg_ext_fecha] != $rst_ext['fec_ini']) {
//                        if ($nfe != $pri_hora[reg_ext_fecha]) {
//                            $pri_hora[reg_ext_hora] = $rst_ext['fec_ini'] . ' 00:00:00';
//                        }
//                    }
//
//                    ///suma de horas extruidas
//                    if (!empty($rst_ext[fec_ini])) {
//                        $hor_ext = pg_fetch_array($Set->suma_horas($pri_hora[reg_ext_hora], $rst_ext['hor_fin']));
//                        $tot_hor_ext = $hor_ext[sum_hora];
//                    } else {
//                        $tot_hor_ext = 0;
//                    }
//
//                    ////fecha hora anterior de impresion
//                    $pri_hora_imp = pg_fetch_array($Set->hora_anterior_impresion($rst_imp['hor_ini']));
//                    $nfi = strtotime('-1 day', strtotime($rst_imp['fec_ini']));
//                    $nfi = date('Y-m-d', $nfi);
//                    if ($pri_hora_imp[reg_imp_fecha] != $rst_imp['fec_ini']) {
//                        if ($nfi != $pri_hora_imp[reg_imp_fecha]) {
//                            $pri_hora_imp[reg_imp_fecha] = $rst_imp['fec_ini'] . ' 00:00:00';
//                        }
//                    }
//                    ///suma de horas impresion
//                    if (!empty($rst_imp[fec_ini])) {
//                        $hor_imp = pg_fetch_array($Set->suma_horas($pri_hora_imp[reg_imp_hora], $rst_imp['hor_fin']));
//                        $tot_hor_imp = $hor_imp[sum_hora];
//                    } else {
//                        $tot_hor_imp = 0;
//                    }
//
//                    ////fecha hora anterior de sellado
//                    $pri_hora_sell = pg_fetch_array($Set->hora_anterior_sellado($rst_sell['hor_ini']));
//                    $nfs = strtotime('-1 day', strtotime($rst_sell['fec_ini']));
//                    $nfs = date('Y-m-d', $nfs);
//                    if ($pri_hora_sell[reg_sell_fecha] != $rst_sell['fec_ini']) {
//                        if ($nfs != $pri_hora_sell[reg_sell_fecha]) {
//                            $pri_hora_sell[reg_sell_fecha] = $rst_sell['fec_ini'] . ' 00:00:00';
//                        }
//                    }
//                    ///suma de horas sellado
//                    if (!empty($rst_sell[fec_ini])) {
//                        $hor_sell = pg_fetch_array($Set->suma_horas($pri_hora_sell[reg_sell_hora], $rst_sell['hor_fin']));
//                        $tot_hor_sell = $hor_sell[sum_hora];
//                    } else {
//                        $tot_hor_sell = 0;
//                    }
//                    //////suma de tiempo 
//                    $h_ext = horas($tot_hor_ext);
//                    $h_imp = horas($tot_hor_imp);
//                    $h_sell = horas($tot_hor_sell);
//                    $hext = explode(':', $h_ext);
//                    $himp = explode(':', $h_imp);
//                    $hsel = explode(':', $h_sell);
//                    ///suma total de tiempo
//                    $ts = $hext[2] + $himp[2] + $hsel[2];
//                    $tm = $hext[1] + $himp[1] + $hsel[1];
//                    $th = $hext[0] + $himp[0] + $hsel[0];
//                    ///funcion de suma de tiempo
//                    $ds = $ts / 60;
//                    $ms = $ts % 60;
//                    $es = explode('.', $ds);
//                    $dm = ($tm + $es[0]) / 60;
//                    $mm = ($tm + $es[0]) % 60;
//                    $em = explode('.', $dm);
//                    $dh = ($th + $em[0]) / 24;
//                    $mh = ($th + $em[0]) % 24;
//                    $eh = explode('.', $dh);
//                    $dd = ($td + $eh[0]);
////                    if (strlen($mh) < 2) {
////                        $mh = '0' . $mh;
//                    $mh = $th;
////                    }
//                    if (strlen($mm) < 2) {
//                        $mm = '0' . $mm;
//                    }
//                    if (strlen($ms) < 2) {
//                        $ms = '0' . $ms;
//                    }
//                    ///formato impresion de tiempo
//                    $tiempo = $mh . '.' . $mm;
//
//
                    $ref = explode(' ', $rstPedido['pro_descripcion']);
                    $ref0 = $ref[0] . ' ' . $ref[1];
                    if (count_chars($ref0) <= 8) {
                        $ref0 = $ref[0] . ' ' . $ref[1];
                    } elseif (count_chars($ref[0]) > 8) {
                        $ref0 = substr($ref[0], 0, 8);
                    } else {
                        $ref0 = $ref[0];
                    }
                    $rst_tot = pg_fetch_array($Set->lista_totales_ingreso($rstPedido[ord_pedido_id]));
//                    $rst_uf = pg_fetch_array($Set->lista_ultima_fecha($desde, $hasta));
//                    $rst_cts = pg_fetch_array($Set->lista_suma_costos($rst_uf[drb_fecha]));
//                    $mod = $rst_cts[mod] * $tiempo;
//                    $moi = $rst_cts[moi] * $tiempo;
//                    $cdf = $rst_cts[cdf] * $tiempo;
//                    $cif = $rst_cts[cif] * $tiempo;
//                    $cv = $rst_cts[cv] * $tiempo;
//                    $total = round($mod, 2) + round($moi, 2) + round($cdf, 2) + round($cif, 2) + round($cv, 2);
//                    $t_unitario = $total / round(str_replace(',', '', $cnt), 2);
//                    if ($h_ext == '0::') {
//                        $h_ext = '';
//                        $hei = '';
//                        $hef = '';
//                    } else {
//                        $hei = date('d/m/Y H:m:i', strtotime($pri_hora[reg_ext_hora]));
//                        $hef = date('d/m/Y H:m:i', strtotime($rst_ext['hor_fin']));
//                    }
//                    if ($h_imp == '0::') {
//                        $h_imp = '';
//                        $hii = '';
//                        $hif = '';
//                    } else {
//                        $hii = date('d/m/Y H:m:i', strtotime($pri_hora_imp[reg_imp_hora]));
//                        $hif = date('d/m/Y H:m:i', strtotime($rst_imp['hor_fin']));
//                    }
//                    if ($h_sell == '0::') {
//                        $h_sell = '';
//                        $hsi = '';
//                        $hsf = '';
//                    } else {
//                        $hsi = date('d/m/Y H:m:i', strtotime($pri_hora_sell[reg_sell_hora]));
//                        $hsf = date('d/m/Y H:m:i', strtotime($rst_sell['hor_fin']));
//                    }
                ?>
                    <tr>
                        <td align="left"><?PHP echo $n; ?></td>
                        <td align="left"><?PHP echo $rstPedido['ord_id'].'-'.$rstPedido['ord_num_orden'] ?></td>   
                        <td align="left" ><?PHP echo$rstPedido['pro_id'].'-'. $rstPedido['pro_codigo'] . ' - ' . $ref0; ?></td>
                        <td align="left"><?PHP echo $rstPedido['pro_uni'] ?></td>
                        <td align="center"><?PHP echo $rstPedido['ord_fec_pedido'] ?></td>
                        <td align="center"><?PHP echo $fec_ext_ini ?></td>
                        <td align="center"><?PHP echo $fec_ext_fin ?></td>
                        <td align="right"><?PHP echo $h_ext ?></td>
                        <td align="center"><?PHP echo $hii ?></td>
                        <td align="center"><?PHP echo $hif ?></td>
                        <td align="right"><?PHP echo $h_imp ?></td>
                        <td align="right"><?PHP echo $tiempo ?></td>
                        <td align="right"><?PHP echo $cnt ?></td>
                        <td align="right"><?PHP echo $kg ?></td>
                        <td align="right"><?PHP echo number_format($rst_tot[consumo], 2) ?></td>
                        <td align="right"><?PHP echo number_format($rst_tot[unitario], 2) ?></td>
                        <td align="right"><?PHP echo number_format($rst_tot[total], 2) ?></td>
                        <td align="right"><?PHP echo number_format($mod, 2) ?></td>
                        <td align="right"><?PHP echo number_format($moi, 2) ?></td>
                        <td align="right"><?PHP echo number_format($cdf, 2) ?></td>
                        <td align="right"><?PHP echo number_format($cif, 2) ?></td>
                        <td align="right"><?PHP echo number_format($cv, 2) ?></td>
                        <td align="right"><?PHP echo number_format($total, 2) ?></td>
                        <td align="right"><?PHP echo number_format($t_unitario, 2) ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </body>
</html>
