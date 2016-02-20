<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reportes.php';

$Rep = new Reportes();
if (isset($_GET[search])) {
    $txt = strtoupper($_GET[txt]);
    $txt.=strtoupper($_GET[linea]);
    $txt.=strtoupper($_GET[talla]);
    $fml = $_GET[fml];
    $hasta = date('Y-m-d');
    if ($fml == 'x') {
        $txt = " and prod like '%$txt%'";
        $fml = "";
        $cns_data = $Rep->lista_reporte_ventas_productos_buscador($txt, $fml);
    } else {
        $txt = "";
        $fml = "and split_part(prod,'&',6)='$fml'";
        $cns_data = $Rep->lista_reporte_ventas_productos_buscador($txt, $fml);
    }
} else {
    $hasta = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Reporte por Productos</title>
    <script type='text/javascript' src='../js/accounting.js'></script>
    <script type='text/javascript' src='../js/includes.js'></script>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter({
                    headers: {
                        0: {sorter: false},
                        1: {sorter: false},
                        2: {sorter: false},
                        3: {sorter: false},
                        4: {sorter: false},
                        5: {sorter: false},
                        6: {sorter: false},
                        7: {sorter: false},
                        8: {sorter: false},
                        9: {sorter: false},
                        10: {sorter: false},
                        11: {sorter: false},
                        12: {sorter: false},
                        14: {sorter: false},
                        15: {sorter: false},
                        16: {sorter: false},
                        17: {sorter: false},
                        18: {sorter: false},
                        19: {sorter: false},
                        20: {sorter: false},
                        21: {sorter: false},
                        22: {sorter: false},
                        23: {sorter: false},
                        24: {sorter: false},
                        25: {sorter: false},
                        26: {sorter: false},
                        27: {sorter: false},
                        28: {sorter: false},
                        29: {sorter: false},
                        30: {sorter: false},
                        31: {sorter: false},
                        32: {sorter: false},
                        33: {sorter: false},
                        34: {sorter: false},
                        35: {sorter: false},
                        36: {sorter: false},
                        37: {sorter: false},
                        38: {sorter: false},
                        39: {sorter: false},
                        40: {sorter: false},
                        41: {sorter: false},
                        42: {sorter: false},
                        43: {sorter: false},
                        44: {sorter: false}


                    },
                    widgets: ['stickyHeaders'],
                    highlightClass: 'highlight',
                    widthFixed: false
                });
                $('#exp_excel').submit(function () {
                    $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                    $("#tbl2").append($("#tbl tbody").clone()).html();
                    $("#tbl2").append($("#tbl tfoot").clone()).html();
                    $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                });
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

            function del(id) {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 48, id: id}, function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }
        </script> 
        <style>
            #mn269{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input{
                background:#f8f8f8 !important; 
            }
            body{
                background:#f8f8f8;
            }

            .desc{
                font-size:9px !important; 
                letter-spacing:-0.35px !important;
            }
            .totales{
                color: #9F6000;
                font-size:12px; 
                background-color: #FEEFB3;
                font-weight:bolder; 

            }
            .familias,.familias_t{
                color: #D8000C;
                font-weight:bolder; 
                background-color: #FFBABA;
            }
            thead tr th{
                font-size:11px !important; 
            }
        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <tr><td colspan="33"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></td></tr>
            <tr><td colspan="33" align="center">REPORTE DE VENTAS POR PRODUCTO</td></tr>
            <tr>
                <td colspan="33"><?php echo 'Desde: ' . $desde . '    Hasta: ' . $hasta ?></td>
            </tr>
        </table>        

        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table  id="tbl" style="width:100%">
            <caption  class="tbl_head" id="cont_head" >
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn"  style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >INVENTARIO AL PRECIO</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" style="float:left " action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="text" name="txt" id="txt" size="35" placeholder="Codigo/Referencia" style="text-transform:uppercase" />                        
                        <input type="text" name="linea" id="linea" size="15" placeholder="Linea" style="text-transform:uppercase" />                                                
                        <input type="text" name="talla" id="talla" size="15" placeholder="Talla" style="text-transform:uppercase" />  
                        <select name="fml" id="fml" >
                            <option value="x">Familia</option>
                            <?php
                            $cns_fml = $Rep->lista_familias();
                            while ($rst_fml = pg_fetch_array($cns_fml)) {
                                if ($faml == $rst_fml[ids]) {
                                    $sel = 'selected';
                                } else {
                                    $sel = '';
                                }
                                echo "<option $sel value=$rst_fml[ids]>$rst_fml[protipo]</option>";
                            }
                            if ($faml == '0') {
                                $sel = 'selected';
                            }
                            ?>
                            <option <?php echo $sel ?> value="0">Industriales</option>
                        </select>
                        &nbsp;&nbsp;&nbsp;&nbsp;AL:&nbsp;<input type="text" id="hasta" name="hasta" size="10" readonly value="<?php echo $hasta ?>" />
                        <input type="submit" value="Buscar" name="search" id="search"  />
                    </form>  
                    <form id="exp_excel" style="float:right " method="post" action="../Includes/export.php"  >
                        <input type="submit" value="Excel" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th width='150px'>Familia</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php
                    $cns = $Rep->lista_emisores($val);
                    while ($rst_locales = pg_fetch_array($cns)) {
                        echo "<th colspan='2' class='locales' lang='$rst_locales[cod_punto_emision]'>$rst_locales[cod]</th>";
                        ?>

                        <?php
                    }
                    ?>
                    <th colspan="2" class="locales" lang="20">TOTAL</th>     
                </tr>
                <tr>
                    <th width='150px'>Codigo</th>
                    <th>Lote</th>
                    <th>Descripcion</th>
                    <th>Linea</th>
                    <th>Talla</th>
                    <th>Precio</th>
                    <?php
                    $cns = $Rep->lista_emisores($val);
                    while ($rst_locales = pg_fetch_array($cns)) {
                        echo "
                        <th>Cant</th>
                        <th>Valor</th>
                        ";
                    }
                    ?>
                    <th>Cant</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody>
                <?PHP
                $n = 0;
                $fml = '';
                $fml2 = '';
                while ($rst = pg_fetch_array($cns_data)) {
                    $n++;
                    if ($fml != $rst[familia]) {
                        if ($n > 1) {
                            echo "<tr class='familias_t' id='$fml2'   >
                                <td >Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>";
                        }
                        echo "<tr>
                                <td class='familias' lang='$rst[ids]' colspan='36' >$rst[familia]</td>
                        </tr>";
                    }
                    echo "<tr class='row' lang='$rst[ids]' >
                            <td>$rst[cod]</td>
                            <td>$rst[lote]</td>
                            <td class='desc' >$rst[descr]</td>
                            <td>$rst[linea]</td>
                            <td>$rst[talla]</td>
                            <td class='desc precio' align='right'>$rst[precio]</td>
                            <td align='right'  name='cn$rst[ids]1' class='cnt1'  >$rst[loc1]</td>    
                            <td align='right'  name='vl$rst[ids]1' class='vlr1'  >$rst[v1]</td>
                            <td align='right'  name='cn$rst[ids]10' class='cnt10'  >$rst[loc10]</td>    
                            <td align='right'  name='vl$rst[ids]10' class='vlr10'  >$rst[v10]</td>
                            <td align='right'  name='cn$rst[ids]14' class='cnt14'  >$rst[loc14]</td>
                            <td align='right'  name='vl$rst[ids]14' class='vlr14'  >$rst[v14]</td>
                            <td align='right'  name='cn$rst[ids]13' class='cnt13'  >$rst[loc13]</td>
                            <td align='right'  name='vl$rst[ids]13' class='vlr13'  >$rst[v13]</td>
                            <td align='right'  name='cn$rst[ids]2' class='cnt2'  >$rst[loc2]</td>
                            <td align='right'  name='vl$rst[ids]2' class='vlr2'  >$rst[v2]</td>
                            <td align='right'  name='cn$rst[ids]9' class='cnt9'  >$rst[loc9]</td>
                            <td align='right'  name='vl$rst[ids]9' class='vlr9'  >$rst[v9]</td>
                            <td align='right'  name='cn$rst[ids]3' class='cnt3'  >$rst[loc3]</td>
                            <td align='right'  name='vl$rst[ids]3' class='vlr3'  >$rst[v3]</td>
                            <td align='right'  name='cn$rst[ids]12' class='cnt12'  >$rst[loc12]</td>
                            <td align='right'  name='vl$rst[ids]12' class='vlr12'  >$rst[v12]</td>
                            <td align='right'  name='cn$rst[ids]11' class='cnt11'  >$rst[loc11]</td>
                            <td align='right'  name='vl$rst[ids]11' class='vlr11'  >$rst[v11]</td>
                            <td align='right'  name='cn$rst[ids]8' class='cnt8'  >$rst[loc8]</td>
                            <td align='right'  name='vl$rst[ids]8' class='vlr8'  >$rst[v8]</td>
                            <td align='right'  name='cn$rst[ids]4' class='cnt4'  >$rst[loc4]</td>
                            <td align='right'  name='vl$rst[ids]4' class='vlr4'  >$rst[v4]</td>
                            <td align='right'  name='cn$rst[ids]6' class='cnt6'  >$rst[loc6]</td>
                            <td align='right'  name='vl$rst[ids]6' class='vlr6'  >$rst[v6]</td>
                            <td align='right'  name='cn$rst[ids]7' class='cnt7'  >$rst[loc7]</td>
                            <td align='right'  name='vl$rst[ids]7' class='vlr7'  >$rst[v7]</td>
                            <td align='right'  name='cn$rst[ids]5' class='cnt5'  >$rst[loc5]</td>
                            <td align='right'  name='vl$rst[ids]5' class='vlr5'  >$rst[v5]</td>
                         </tr>";
                    $fml2 = $rst[ids];
                    $fml = $rst[familia];
                }

                echo"<tr class='familias_t' id='$fml2'   >
                    <td >Total:</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
            </tbody>
            <tfoot>
                <tr class='totales'>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Totales:</td>
                    <td align='right' id='t_cnt1' >c</td>
                    <td align='right' id='t_vlr1' >v</td>
                    <td align='right' id='t_cnt10' >c</td>
                    <td align='right' id='t_vlr10' >v</td>
                    <td align='right' id='t_cnt14' >c</td>
                    <td align='right' id='t_vlr14' >v</td>
                    <td align='right' id='t_cnt13' >c</td>
                    <td align='right' id='t_vlr13' >v</td>
                    <td align='right' id='t_cnt2' >c</td>
                    <td align='right' id='t_vlr2' >v</td>
                    <td align='right' id='t_cnt9' >c</td>
                    <td align='right' id='t_vlr9' >v</td>
                    <td align='right' id='t_cnt3' >c</td>
                    <td align='right' id='t_vlr3' >v</td>
                    <td align='right' id='t_cnt12' >c</td>
                    <td align='right' id='t_vlr12' >v</td>
                    <td align='right' id='t_cnt11' >c</td>
                    <td align='right' id='t_vlr11' >v</td>
                    <td align='right' id='t_cnt8' >c</td>
                    <td align='right' id='t_vlr8' >v</td>
                    <td align='right' id='t_cnt4' >c</td>
                    <td align='right' id='t_vlr4' >v</td>
                    <td align='right' id='t_cnt6' >c</td>
                    <td align='right' id='t_vlr6' >v</td>
                    <td align='right' id='t_cnt7' >c</td>
                    <td align='right' id='t_vlr7' >v</td>
                    <td align='right' id='t_cnt5' >c</td>
                    <td align='right' id='t_vlr5' >v</td>
                    <td align='right' id='t_cnt20' >c</td>
                    <td align='right' id='t_vlr20' >v</td>
                </tr>
            </tfoot>
        </table>";
                ?>
            <script>
                $('.row').each(function () {

                    pr = $(this).find('.precio').html().replace(/,/g, '');

                    t1 = $(this).find('.cnt1').html().replace(/,/g, '');
                    t2 = $(this).find('.cnt2').html().replace(/,/g, '');
                    t3 = $(this).find('.cnt3').html().replace(/,/g, '');
                    t4 = $(this).find('.cnt4').html().replace(/,/g, '');
                    t5 = $(this).find('.cnt5').html().replace(/,/g, '');
                    t6 = $(this).find('.cnt6').html().replace(/,/g, '');
                    t7 = $(this).find('.cnt7').html().replace(/,/g, '');
                    t8 = $(this).find('.cnt8').html().replace(/,/g, '');
                    t9 = $(this).find('.cnt9').html().replace(/,/g, '');
                    t10 = $(this).find('.cnt10').html().replace(/,/g, '');
                    t11 = $(this).find('.cnt11').html().replace(/,/g, '');
                    t12 = $(this).find('.cnt12').html().replace(/,/g, '');
                    t13 = $(this).find('.cnt13').html().replace(/,/g, '');
                    t14 = $(this).find('.cnt14').html().replace(/,/g, '');
                    t_v = (t1 * 1 + t2 * 1 + t3 * 1 + t4 * 1 + t5 * 1 + t6 * 1 + t7 * 1 + t8 * 1 + t9 * 1 + t10 * 1 + t11 * 1 + t12 * 1 + t13 * 1 + t14 * 1);

                    //$(this).append("<td align='right'>"+accounting.formatMoney(t_v, "", 0, ",", ".")+"</td><td align='right'>"+accounting.formatMoney(pr*t_v, "", 2, ",", ".")+"</td>");
                    $(this).append("<td align='right' name='cn" + this.lang + "20' class='cnt20' >" + accounting.formatMoney(t_v, "", 0, ",", ".") + "</td><td align='right' name='vl" + this.lang + "20' class='vlr20' >" + accounting.formatMoney(pr * t_v, "", 2, ",", ".") + "</td>");
                    //$( "li.item-ii" ).find( "li" ).css( "background-color", "red" );
                });
            </script>

            <script>
                $('.familias').each(function () {
                    fml = this.lang;
                    $('.locales').each(function () {
                        tcn = 0;
                        cn_nm = 'cn' + fml + this.lang;
                        vl_nm = 'vl' + fml + this.lang;
                        $("td[name=" + cn_nm + "]").each(function () {
                            tcn = (tcn * 1) + ($(this).html().replace(/,/g, '') * 1);
                        });
                        tvl = 0;
                        $("td[name=" + vl_nm + "]").each(function () {
                            tvl = (tvl * 1) + ($(this).html().replace(/,/g, '') * 1);
                        });
                        $("#" + fml).append("<td align='right'>" + accounting.formatMoney(tcn, "", 0, ",", ".") + "</td><td align='right' >" + accounting.formatMoney(tvl, "", 2, ",", ".") + "</td>").html();
                    });
                });

            </script>
            <script>
                tcnt = 0;
                $('.cnt_tot').each(function () {
                    tcnt = (tcnt * 1) + ($(this).html().replace(/,/g, '') * 1);
                });
                $('#tcnt_tot').html(accounting.formatMoney(tcnt, "", 0, ",", "."));
                tcnt = 0;
                $('.vlr_tot').each(function () {
                    tcnt = (tcnt * 1) + ($(this).html().replace(/,/g, '') * 1);
                });
                $('#tvlr_tot').html(accounting.formatMoney(tcnt, "", 2, ",", "."));

            </script>
            <script>
                $('.locales').each(function () {

                    cnt = 'cnt' + this.lang;
                    tcnt = 0;
                    $('.' + cnt).each(function () {
                        tcnt = (tcnt * 1) + ($(this).html().replace(/,/g, '') * 1);
                    });
                    $('#t_' + cnt).html(accounting.formatMoney(tcnt, "", 0, ",", "."));

                    vlr = 'vlr' + this.lang;
                    tvlr = 0;
                    $('.' + vlr).each(function () {
                        tvlr = (tvlr * 1) + ($(this).html().replace(/,/g, '') * 1);
                    });
                    $('#t_' + vlr).html(accounting.formatMoney(tvlr, "", 2, ",", "."));
                });
            </script>
