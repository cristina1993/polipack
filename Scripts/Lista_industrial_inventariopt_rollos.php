<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_inventariopt.php'; //cambiar clsClase_productos
$Clase_industrial_inventariopt = new Clase_industrial_inventariopt();
if (isset($_GET[txt], $_GET[fecha2])) {
    $nm = trim(strtoupper($_GET[txt]));
    $fec2 = $_GET[fecha2];
    $std = $_GET[estado];
    if (!empty($nm)) {
        $txt = "and (pro_codigo like '%$nm%' or pro_descripcion like '%$nm%' or m.mov_pago like '%$nm%') and m.mov_fecha_trans between '1900-01-01' and '$fec2' and m.mov_flete='$std'";
    } else {
        $txt = "and m.mov_fecha_trans between '1900-01-01' and '$fec2' and m.mov_flete='$std'";
    }
    $fec = "and m.mov_fecha_trans between '1900-01-01' and '$fec2'";
    $cns = $Clase_industrial_inventariopt->lista_buscar_inventariopt_rollos($txt);
} else {
    $fec1 = date("Y-m-d");
    $fec2 = date("Y-m-d");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
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
                <center class="cont_title" ><?PHP echo 'INVENTARIO DE PRODUCTO TERMINADO ' ?></center>
                <center class="cont_finder">

                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        CODIGO:<input type="text" name="txt" size="15" id="txt" value="<?php echo $nm ?>"/>
                        AL:<input type="text" name="fecha2" size="15" id="fecha2" value="<?php echo $fec2 ?>"/>
                        ESTADO:
                        <select id="estado" name="estado">
                            <option value="0">CONFORME</option>
                            <option value="3">INCONFORME</option>
                        </select>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
                <tr>
                    <th colspan="6">Producto terminado</th>
                    <th colspan="2">Total Conforme</th>
                    <th colspan="2">Total Inconforme</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Tipo</th>
                    <th>Codigo</th>
                    <th>Orden</th>
                    <th>Descripción</th>
                    <th>Unidad</th>
                    <th>Rollos</th>
                    <th>Peso</th>
                    <th>Rollos</th>
                    <th>Peso</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $i = 0;
                $g_total = 0;
                $gr_id='';
                $gr_lt='';
                while ($rst = pg_fetch_array($cns)) {
                    
                    $rst_inv = pg_fetch_array($Clase_industrial_inventariopt->total_inventario_rollos($rst[pro_id], $rst[mov_pago], $fec));
                    $inv_con = $rst_inv[ingreso_con] - $rst_inv[egreso_con];
                    $cnt_con = $rst_inv[cnt_ingreso_con] - $rst_inv[cnt_egreso_con];
                    $inv_inc = $rst_inv[ingreso_inc] - $rst_inv[egreso_inc];
                    $cnt_inc = $rst_inv[cnt_ingreso_inc] - $rst_inv[cnt_egreso_inc];
                    $rst_ord = pg_fetch_array($Clase_industrial_inventariopt->lista_orden($rst[mov_pago]));
                    $cja=$cnt_con/$rst_ord[opp_velocidad];
                    $dt=  explode('.',$cja);
                    if(!empty($dt[1])){
                        $cnt_cj=$dt[0];
                    }
                    if ($rst[pro_tipo] == 0) {
                        $tipo = 'SEMIELABORADO';
                    } else {
                        $tipo = 'TERMINADO';
                    }
                    if(substr($rst[mov_pago], 0, 2)=='EC'){
                            $l2=7;
                        }else{
                            $l2=6;
                        }
                    if (($cnt_con != 0 && $inv_con != 0)||($cnt_inc != 0 && $inv_inc != 0)) {
                        $i++;
                        if ($gr_id != $rst[pro_id] && $gr_lt != substr($rst[mov_pago], 0, $l2) && $i != 1) {
                            ?>
                            <tr style="height: 20px;" class="totales" id="fila" >
                                <td style="font-size: 13px;" colspan="3"></td>
                                <td style="font-size: 13px;" colspan="3" align="right"><?php echo 'TOTAL ' . $gr_cod.' '. $gr_lt ?></td>
                                <td style="font-size: 13px;" align="right"><?php echo number_format($rcc_tot) ?></td>
                                <td style="font-size: 13px;" align="right"><?php echo number_format($rcp_tot, 2) ?></td>
                                <td style="font-size: 13px;" align="right"><?php echo number_format($ric_tot) ?></td>
                                <td style="font-size: 13px;" align="right"><?php echo number_format($rip_tot, 2) ?></td>
                            </tr>  
                            <?PHP
                            $rcc_tot = 0;
                            $rcp_tot = 0;
                            $ric_tot = 0;
                            $rip_tot = 0;
                        }
                        echo "<tr style='height: 20px' id='fila'>
                            <td>$i </td>
                            <td>$tipo</td>
                            <td>$rst[pro_codigo] </td>
                            <td>$rst[mov_pago] </td>
                            <td>$rst[pro_descripcion]</td>
                            <td>$rst[pro_uni]</td>
                            <td align='right'>" . number_format($cnt_con) . "</td>
                            <td align='right'>" . number_format($inv_con, 2) . "</td>
                                <td align='right'>" . number_format($cnt_inc) . "</td>
                            <td align='right'>" . number_format($inv_inc, 2) . "</td>
                        </tr> ";
                        $rcp_tot+=$inv_con;
                        $rcc_tot+=$cnt_con;
                        $rip_tot+=$inv_inc;
                        $ric_tot+=$cnt_inc;
                        $g_total+=$inv_con;
                        $cj_total+=$cnt_cj;
                        $cnt_total+=$cnt_con;
                        $ig_total+=$inv_inc;
                        $icnt_total+=$cnt_inc;
                        $gr_id = $rst[pro_id];
                        if(substr($rst[mov_pago], 0, 2)=='EC'){
                            $l=7;
                        }else{
                            $l=6;
                        }
                        $gr_lt = substr($rst[mov_pago], 0, $l);
                        $gr_cod = $rst[pro_codigo];
                    }
                }
                 if ($gr_id != $rst[pro_id] && $gr_lt != substr($rst[mov_pago], 0, $l)) {
                    ?>
                    <tr style="height: 20px;" class="totales" id="fila" >
                        <td style="font-size: 13px;" colspan="3"></td>
                        <td style="font-size: 13px;" colspan="3" align="right"><?php echo 'TOTAL ' .$gr_cod.' '. $gr_lt ?></td>
                        <td style="font-size: 13px;" align="right"><?php echo number_format($rcc_tot) ?></td>
                        <td style="font-size: 13px;" align="right"><?php echo number_format($rcp_tot, 2) ?></td>
                        <td style="font-size: 13px;" align="right"><?php echo number_format($ric_tot) ?></td>
                        <td style="font-size: 13px;" align="right"><?php echo number_format($rip_tot, 2) ?></td>
                    </tr>   
                    <?PHP
                   
                }
                echo "<tr style='font-weight:bolder' class='totales'>
                <td colspan='6' align='right' style='font-size:14px;'>Total</td>
                <td align='right' style='font-size:15px;'>" . number_format($cnt_total) . "</td>
                <td align='right' style='font-size:15px;'>" . number_format($g_total, 2) . "</td>
                    <td align='right' style='font-size:15px;'>" . number_format($icnt_total) . "</td>
                <td align='right' style='font-size:15px;'>" . number_format($ig_total, 2) . "</td>
                </tr>";
                ?>
            </tbody>
        </table>            
    </body>    
</html>



