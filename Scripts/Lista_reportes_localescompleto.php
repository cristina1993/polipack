<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reportes.php';
$Rep = new Reportes();
if (isset($_GET[txt])) {
    //$cns = $Rep->lista_un_cupo_user(trim(strtoupper($_GET[txt])));
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
} else {
    $cns = $Rep->lista_emisores();
    $d = 20150401;
    $h = 20150420;
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Tipo de pago</title>
    <head>
        <script>
            $(function () {
                Calendar.setup({inputField: desde, ifFormat: '%Y-%m-%d', button: im_desde});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";

            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_cupos.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_cupos.php?id=' + id;
                        look_menu();
                        break;
                }

            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 48, id: id}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_reportes_localescompleto.php';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script> 
        <style>
            #mn261{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input{
                background:#f8f8f8 !important; 
            }
            table tbody tr td{
                background:#f8f8f8;
                color:black;
                height:25px; 
            }
            .totales td{
                background-color: #E0EBFF;
                font-weight:bolder; 
            }
            .enc{
                color: #D8000C;
                background-color: #FFBABA;
                font-weight:bolder;                 
            }
            .enc_t td{
                color: #D8000C;
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
                <center class="cont_title" >REPORTES POR PUNTO DE FACTURACION</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="checkbox" />Por Vendedor
                        <input type="checkbox" />Resumen
                        <input type="checkbox" />Ventas
                        <input type="checkbox" />Devolucion
                        <input type="checkbox" />Caja
                        <input type="text" id="desde" name="desde" size="12" readonly value="<?php echo $desde ?>" />
                        <img src="../img/calendar.png" id="im_desde" />
                        <input type="text" id="hasta" name="desde" size="12" readonly value="<?php echo $hasta ?>" />
                        <img src="../img/calendar.png" id="im_hasta" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" />Desglose TC                                                
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="25"></th>
                    <?php
                    $cns_bh = $Rep->lista_bancos_desgloce_general_tc($d, $h);
                    while ($rst_bh = pg_fetch_array($cns_bh)) {
                        $cns_tc = $Rep->lista_tc_general($d, $h, $rst_bh[pag_banco]);
                        $n1 = 0;
                        while ($rst_tc = pg_fetch_array($cns_tc)) {
                            $cns_pag = $Rep->lista_pag_general($d, $h, $rst_bh[pag_banco], $rst_tc[pag_tarjeta]);
                            $n1 = $n1 + pg_num_rows($cns_pag);
                        }
                        ?>
                        <th colspan="<?php echo $n1 ?>"><?php echo $rst_bh[banco] ?></th>
                        <?php
                    }
                    ?>

                </tr>
                <tr>
                    <th colspan="10">Ventas</th>
                    <th colspan="6">Devoluciones</th>
                    <th>TOTAL</th>
                    <th colspan="8">Cierre de Caja</th>
                    <?php
                    $cns_bh = $Rep->lista_bancos_desgloce_general_tc($d, $h);
                    $ch = 0;
                    while ($rst_bh = pg_fetch_array($cns_bh)) {
                        $ch++;
                        $cns_tc = $Rep->lista_tc_general($d, $h, $rst_bh[pag_banco]);
                        while ($rst_tc = pg_fetch_array($cns_tc)) {
                            $cns_pag = $Rep->lista_pag_general($d, $h, $rst_bh[pag_banco], $rst_tc[pag_tarjeta]);
                            ?>
                            <th colspan="<?php echo pg_num_rows($cns_pag) ?>"><?php echo $rst_tc[targeta] ?></th>
                            <?php
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <th>Locales</th>
                    <th>#Facturas</th>
                    <th>Total Ventas</th>                                
                    <th>Descuento</th>
                    <th>Sbt con IVA</th>
                    <th>Sbt sin IVA</th>
                    <th>Sbt Neto</th>
                    <th>ICE</th>
                    <th>IVA</th>
                    <th>Total Ventas</th>

                    <th>#NC</th>
                    <th>Sbt con IVA</th>
                    <th>Sbt sin IVA</th>
                    <th>Sbt Neto</th>
                    <th>IVA</th>
                    <th>Total</th>

                    <th>Ventas Netas</th>
                    <th>Cheque</th>
                    <th>Efectivo</th>
                    <th>Retencion</th>
                    <th>NC</th>
                    <th>Certificados</th>
                    <th>Bonos</th>
                    <th>TD</th>
                    <th>TC</th>  
                    <?php
                    $cns_bh = $Rep->lista_bancos_desgloce_general_tc($d, $h);
                    while ($rst_bh = pg_fetch_array($cns_bh)) {
                        $cns_tc = $Rep->lista_tc_general($d, $h, $rst_bh[pag_banco]);
                        while ($rst_tc = pg_fetch_array($cns_tc)) {
                            $cns_pag = $Rep->lista_pag_general($d, $h, $rst_bh[pag_banco], $rst_tc[pag_tarjeta]);
                            while ($rst_pg = pg_fetch_array($cns_pag)) {
                                ?>
                                <th><?php echo $rst_pg[pago] ?></th>
                                <?php
                            }
                        }
                    }
                    ?>

<!--            <script>
    $('#col_h').attr('colspan','<?php //echo $ch             ?>');
</script>-->
<!--                    <th>Total</th>-->
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                while ($rst_locales = pg_fetch_array($cns)) {
                    $cns_vendedor = $Rep->lista_ventas_devoluciones_vendedor($rst_locales[cod_punto_emision], $d, $h, 1);
                    ?>
                    <tr class="enc_t">
                        <td >Total</td>
                    </tr>
                    <tr>
                        <td class="enc"><?php echo $rst_locales[nombre_comercial] ?></td>
                    </tr>
                    <?php
                    while ($rst_ventas = pg_fetch_array($cns_vendedor)) {
                        $rst_dev = pg_fetch_array($Rep->lista_devoluciones_vendedor($rst_locales[cod_punto_emision], $d, $h, strtoupper($rst_ventas[vendedor])));
                        if ($rst_locales[cod_punto_emision] != 1 && $rst_locales[cod_punto_emision] != 10) {
                            $tc = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[cod_punto_emision], $d, $h, strtoupper($rst_ventas[vendedor]), '1'));
                            $td = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[cod_punto_emision], $d, $h, strtoupper($rst_ventas[vendedor]), '2'));
                            $ch = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[cod_punto_emision], $d, $h, strtoupper($rst_ventas[vendedor]), '3'));
                            $ef = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[cod_punto_emision], $d, $h, strtoupper($rst_ventas[vendedor]), '4'));
                            $cr = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[cod_punto_emision], $d, $h, strtoupper($rst_ventas[vendedor]), '5'));
                            $bs = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[cod_punto_emision], $d, $h, strtoupper($rst_ventas[vendedor]), '6'));
                            $rt = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[cod_punto_emision], $d, $h, strtoupper($rst_ventas[vendedor]), '7'));
                            $nc = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[cod_punto_emision], $d, $h, strtoupper($rst_ventas[vendedor]), '8'));
                            $ch[sum] = number_format($ch[sum], 2);
                            $ef[sum] = number_format($ef[sum], 2);
                            $rt[sum] = number_format($rt[sum], 2);
                            $nc[sum] = number_format($nc[sum], 2);
                            $cr[sum] = number_format($cr[sum], 2);
                            $bs[sum] = number_format($bs[sum], 2);
                            $td[sum] = number_format($td[sum], 2);
                            $tc[sum] = number_format($tc[sum], 2);

                            //*******desgloce de targeta de credito*************///

                            $cns_banc = $Rep->lista_bancos_desgloce_tc($rst_locales[cod_punto_emision], $d, $h, strtoupper($rst_ventas[vendedor]));
                        } else {
                            $ch[sum] = '';
                            $ef[sum] = '';
                            $rt[sum] = '';
                            $nc[sum] = '';
                            $cr[sum] = '';
                            $bs[sum] = '';
                            $td[sum] = '';
                            $tc[sum] = '';
                        }
                        ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $rst_ventas[vendedor] ?></td>
                            <td align="right"><?php echo $rst_ventas[nfact] ?></td>
                            <td align="right"><?php echo number_format($rst_ventas[tventa], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_ventas[desc], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_ventas[con_iva], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_ventas[sin_iva], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_ventas[sbt_neto], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_ventas[ice], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_ventas[iva], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_ventas[tventas], 2) ?></td>
                            <!--Devoluciones-->
                            <td align="right"><?php echo $rst_dev[nfact] ?></td>
                            <td align="right"><?php echo number_format($rst_dev[con_iva], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_dev[sin_iva], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_dev[sbt_neto], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_dev[iva], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_dev[tventas], 2) ?></td>
                            <td align="right"><?php echo number_format($rst_ventas[tventas] - $rst_dev[tventas], 2) ?></td>

                            <td align="right"><?php echo $ch[sum] ?></td>
                            <td align="right"><?php echo $ef[sum] ?></td>
                            <td align="right"><?php echo $rt[sum] ?></td>
                            <td align="right"><?php echo $nc[sum] ?></td>
                            <td align="right"><?php echo $cr[sum] ?></td>
                            <td align="right"><?php echo $bs[sum] ?></td>
                            <td align="right"><?php echo $td[sum] ?></td>
                            <td align="right"><?php echo $tc[sum] ?></td>

                            <?php
                            $cns_bh = $Rep->lista_bancos_desgloce_general_tc($d, $h);
                            while ($rst_bh = pg_fetch_array($cns_bh)) {
                                $cns_tc = $Rep->lista_tc_general($d, $h, $rst_bh[pag_banco]);
                                while ($rst_tc = pg_fetch_array($cns_tc)) {
                                    $cns_pag = $Rep->lista_pag_general($d, $h, $rst_bh[pag_banco], $rst_tc[pag_tarjeta]);
                                    while ($rst_pg = pg_fetch_array($cns_pag)) {
                                        $rst_vlr = pg_fetch_array($Rep->lista_valor_targeta_contado($d, $h, $rst_bh[pag_banco], $rst_tc[pag_tarjeta], $rst_pg[pag_contado], $rst_locales[cod_punto_emision], $rst_ventas[vendedor]));
                                        ?>
                                        <td><?php echo $rst_vlr[sum] ?></td>
                                        <?php
                                    }
                                }
                            }
                            ?>


                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>


        </table>            

    </body>    
</html>


