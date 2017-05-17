<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[desde])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $ord = $_GET[txt];
    $status = $_GET[status];
    if (!empty($_GET[status])) {
        if ($status == 1) {
            $cns = $Set->lista_buscar_registrados_orden_eco();
        } else if ($status == 2) {
            $cns = $Set->lista_buscar_produccion_orden_eco();
        } else {
            $cns = $Set->lista_buscar_terminados_orden_eco();
        }
    } else {
        $cns = $Set->lista_buscar_orden_produccion($ord, $desde, $hasta);
    }
} else {
    $desde = date("Y-m-d");
    $hasta = date("Y-m-d");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Orden de Producción</title>
    <head>
        <script>

            $(function () {
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hata", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
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
                fec1 = $('#desde').val();
                fec2 = $('#hasta').val();
                txt = $('#txt').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";

                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_orden_ecocambrella.php?desde=' + fec1 + '&hasta=' + fec2 + '&txt=' + txt;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_orden_ecocambrella.php?id=' + id + '&desde=' + fec1 + '&hasta=' + fec2 + '&txt=' + txt;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 2://Reporte
                        frm.src = '../Reports/rpt_orden_venta.php?id=' + id + '&desde=' + fec1 + '&hasta=' + fec2 + '&txt=' + txt;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 3://Editar
                        frm.src = '../Scripts/Form_i_orden_ecocambrella.php?id=' + id + '&x=1' + '&desde=' + fec1 + '&hasta=' + fec2 + '&txt=' + txt;
                        ;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                    case 4://etiqueta
                        frm.src = '../Scripts/frm_etiquetas.php?id=' + id;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                    case 5://maq 3t
                        frm.src = '../Scripts/Form_i_orden_ecocambrella3T.php?desde=' + fec1 + '&hasta=' + fec2 + '&txt=' + txt;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                    case 6://edit maq 3t
                        frm.src = '../Scripts/Form_i_orden_ecocambrella3T.php?id=' + id + '&desde=' + fec1 + '&hasta=' + fec2 + '&txt=' + txt;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }

            }

            function del(id, doc)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 61, id: id, data: doc}, function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_orden_ecocambrella.php';
                        } else {
                            loading('hidden');
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
            #mn49{
                background:black;
                color:white;
                border: solid 1px white;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" >

                <!-- CODIGO PARA EL SUBMENU-->
                <center class="cont_menu" > 
                    <?php
                    $cns_sbm = $User->list_primer_opl(29, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <!---------------------------->

                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >ORDEN DE PRODUCCION</center>
                <center class="cont_finder">                    
                    <form method="GET" id="frmSearch" name="frm1" style="margin-top:5px; " action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <?php
                        if ($Prt->add == 0) {
                            ?>                                                 
                            <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;   " title="Nuevo Registro Maquina 1T" onclick="auxWindow(0)" >Maquina 1T</a>-->
                            <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;   " title="Nueva Orden" onclick="auxWindow(5)" >Nueva Orden</a>
                            <?php
                        }
                        ?>
                        Orden:<input type="text" name="txt" id="txt" size="20" />
                        Status:<select id='status' name='status'>
                            <option value="0">SELECCIONE</option>
                            <option value="1">REGISTRADO</option>
                            <option value="2">PRODUCCION</option>
                            <option value="3">TERMINADO</option>
                        </select>
                        DESDE:<input type="text"   name="desde" value="<?php echo $desde ?>"  id="desde" size="10" />
                        <img src="../img/calendar.png" width="16"  id="im-desde" />
                        HASTA:<input type="text"   name="hasta" value="<?php echo $hasta ?>"  id="hasta" size="10" />
                        <img src="../img/calendar.png" width="16"  id="im-hasta" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>

                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead >

                <tr >
                    <th  colspan="14">ORDENES DE PRODUCCION</th>  

                </tr>
                <tr>
                    <th colspan="5"></th>   
                    <th colspan="2">Solicitado</th>                   
                    <th colspan="2">Producción</th>  
                    <th colspan="2">Faltante</th>
                    <th colspan="3"></th>
                    <!--<th colspan="2" hidden >Despacho</th>-->
                    <!--<th ></th>-->               
<!--                    <th hidden colspan="2">Solicitado</th>                   
                    <th hidden colspan="2">Producción</th>  
                    <th hidden colspan="2">Faltante</th>
                    <th hidden colspan="2">Despacho</th>-->
                    <!--<th colspan="2"></th>-->
                </tr>
                <tr>
                    <th style="width:5px">No.</th>
                    <th style="width:7px">Pedido</th>
                    <th style="width:7px">Fecha Pedido</th>
                    <th style="width:100px">Cliente</th>
                    <th style="width:100px">Descripcion</th>
                    <th style="width:20px">Cnt</th>
                    <th style="width:20px">Kg</th>     
                    <th style="width:20px">Cnt</th>
                    <th style="width:20px">Kg</th>   
                    <th style="width:20px">Cnt</th>
                    <th style="width:20px">Kg</th>   
<!--                    <th hidden style="width:20px">Cnt</th>
                    <th hidden style="width:20px">Kg</th>  
                    <th hidden style="width:100px">Descripcion</th>
                    <th hidden style="width:20px">Cnt</th>
                    <th hidden style="width:20px">Kg</th>     
                    <th hidden style="width:20px">Cnt</th>
                    <th hidden style="width:20px">Kg</th>   
                    <th hidden style="width:20px">Cnt</th>
                    <th hidden style="width:20px">Kg</th>   
                    <th hidden style="width:20px">Cnt</th>
                    <th hidden style="width:20px">Kg</th>-->
                    <!--<th style="width:20px">Desperdicio</th>-->
                    <th style="width:20px">Status</th>
                    <th style="width:100px">Acciones</th>
                </tr>

            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_pro2 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro_secundario]));
                    $rst_pro3 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro3]));
                    $rst_pro4 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro4]));
                    $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst[ord_id], $rst[pro_id]));
                    $rst_prod2 = pg_fetch_array($Set->lista_produccion_pedido($rst[ord_id], $rst_pro2[pro_id]));
                    $rst_prod3 = pg_fetch_array($Set->lista_produccion_pedido($rst[ord_id], $rst_pro3[pro_id]));
                    $rst_prod4 = pg_fetch_array($Set->lista_produccion_pedido($rst[ord_id], $rst_pro4[pro_id]));
                    $rst_peso = pg_fetch_array($Set->lista_un_producto($rst[pro_id]));

                    $faltante = $rst[ord_num_rollos] - $rst_prod[rollo];
                    $f_peso = round($rst[ord_kgtotal], 2) - $rst_prod[peso];
                    $ev = "onclick='auxWindow(6,$rst[ord_id])'";
                    if ($rst[ord_tornillo] == 0) {
                        $opc = 6;
                    } else {
                        $opc = 1;
                    }
                    echo "<tr>
                        <td>$n </td>                  
                        <td $ev >$rst[ord_num_orden] </td>
                        <td $ev >$rst[ord_fec_pedido] </td>
                        <td $ev >$rst[cli_raz_social] </td>
                        <td $ev >$rst[pro_descripcion] </td>       
                        <td align='right' $ev >" . number_format($rst[ord_num_rollos], 2) . "</td>      
                        <td align='right' $ev >" . number_format($rst[ord_kgtotal], 2) . "</td>      
                        <td align='right' $ev >" . number_format($rst_prod[rollo], 2) . "</td>
                        <td align='right' $ev >" . number_format($rst_prod[peso], 2) . "</td>
                        <td align='right' $ev >" . number_format($faltante, 2) . "</td>
                        <td align='right' $ev >" . number_format($f_peso, 2) . "</td>
                       ";
                    $por = ($rst_prod[peso] * 100) / $rst[ord_kgtotal];
                    if ($rst_prod[peso] == '') {
                        $est = 'REGISTRADO';
                    } else if ($por >= 90) {
                        $est = 'TERMINADO';
                    } else {
                        $est = 'PRODUCCION';
                    }
                    echo "
                          <td $ev > $est </td>
                        <td align='center'>";
                    if ($rst_prod[peso] == '' && $rst_prod2[peso] == '' && $rst_prod3[peso] == '' && $rst_prod4[peso] == '') {
                        if ($Prt->delete == 0) {
                            $a = '"';
                            echo "<img src='../img/b_delete.png'  class='auxBtn' onclick='del($rst[ord_id],$a$rst[ord_num_orden]$a)'>";
                        }
                        if ($Prt->edition == 0) {
                            echo "<img src='../img/upd.png'  class='auxBtn' onclick='auxWindow($opc,$rst[ord_id])'>";
                        }
                    }
                    //                      <img src='../img/etq2.jpg' title='etiqueta' width='24px' class='auxBtn' onclick='auxWindow(4,$rst[ord_id])'>                                       

                    echo "<img src='../img/orden.png' class='auxBtn' onclick='auxWindow(2,$rst[ord_id])'>                                         
                </td>
                </tr>";
                    if ($rst_pro2 != 0) {
                        $faltante2 = $rst[ord_num_rollos2] - $rst_prod2[rollo];
                        $f_peso2 = round($rst[ord_kgtotal2], 2) - $rst_prod2[peso];
                        $por2 = ($rst_prod2[peso] * 100) / $rst[ord_kgtotal2];
                        if ($rst_prod2[peso] == '') {
                            $est2 = 'REGISTRADO';
                        } else if ($por2 >= 90) {
                            $est2 = 'TERMINADO';
                        } else {
                            $est2 = 'PRODUCCION';
                        }
                        echo "<tr style='height:30px'>
                        <td ></td>
                        <td ></td>
                        <td ></td>
                        <td ></td>
                        <td $ev >" . $rst_pro2[pro_descripcion] . "</td>       
                        <td align='right' $ev >" . number_format($rst[ord_num_rollos2], 2) . "</td>      
                        <td align='right' $ev >" . number_format($rst[ord_kgtotal2], 2) . "</td>      
                        <td align='right' $ev >" . number_format($rst_prod2[rollo], 2) . "</td>
                        <td align='right' $ev >" . number_format($rst_prod2[peso], 2) . "</td>
                        <td align='right' $ev >" . number_format($faltante2, 2) . "</td>
                        <td align='right' $ev >" . number_format($f_peso2, 2) . "</td>
                        <td $ev>$est2</td>
                        <td ></td></tr>";
                    }
                    //////////////////////////////////////////////////CAMBIAR VARIABLES ///////////////////////////////////////////////////////
                    if ($rst_pro3 != 0) {
                        $faltante3 = $rst[ord_num_rollos3] - $rst_prod3[rollo];
                        $f_peso3 = round($rst[ord_kgtotal3], 2) - $rst_prod3[peso];
                        $por3 = ($rst_prod3[peso] * 100) / $rst[ord_kgtotal3];
                        if ($rst_prod3[peso] == '') {
                            $est3 = 'REGISTRADO';
                        } else if ($por2 >= 90) {
                            $est3 = 'TERMINADO';
                        } else {
                            $est3 = 'PRODUCCION';
                        }
                        echo "<tr style='height:30px'>
                        <td ></td>
                        <td ></td>
                        <td ></td>
                        <td ></td>
                        <td $ev >" . $rst_pro3[pro_descripcion] . "</td>       
                        <td align='right' $ev >" . number_format($rst[ord_num_rollos3], 2) . "</td>      
                        <td align='right' $ev >" . number_format($rst[ord_kgtotal3], 2) . "</td>         
                        <td align='right' $ev >" . number_format($rst_pro3[rollo], 2) . "</td>
                        <td align='right' $ev >" . number_format($rst_pro3[peso], 2) . "</td>
                        <td align='right' $ev >" . number_format($faltante3, 2) . "</td>
                        <td align='right' $ev >" . number_format($f_peso3, 2) . "</td>
                        <td $ev>$est3</td>
                        <td ></td></tr>";
                    }
                    //////////////////////////////////////////////////CAMBIAR VARIABLES ///////////////////////////////////////////////////////
                    if ($rst_pro4 != 0) {
                        $faltante4 = $rst[ord_num_rollos4] - $rst_prod4[rollo];
                        $f_peso4 = round($rst[ord_kgtotal4], 2) - $rst_prod4[peso];
                        $por4 = ($rst_prod4[peso] * 100) / $rst[ord_kgtotal4];
                        if ($rst_prod4[peso] == '') {
                            $est4 = 'REGISTRADO';
                        } else if ($por4 >= 90) {
                            $est4 = 'TERMINADO';
                        } else {
                            $est4 = 'PRODUCCION';
                        }
                        echo "<tr style='height:30px'>
                        <td ></td>
                        <td ></td>
                        <td ></td>
                        <td ></td>
                        <td $ev >" . $rst_pro4[pro_descripcion] . "</td>       
                        <td align='right' $ev >" . number_format($rst[ord_num_rollos4], 2) . "</td>      
                        <td align='right' $ev >" . number_format($rst[ord_kgtotal4], 2) . "</td>     
                        <td align='right' $ev >" . number_format($rst_prod4[rollo], 2) . "</td>
                        <td align='right' $ev >" . number_format($rst_prod4[peso], 2) . "</td>
                        <td align='right' $ev >" . number_format($faltante4, 2) . "</td>
                        <td align='right' $ev >" . number_format($f_peso4, 2) . "</td>
                        <td $ev>$est4</td>
                        <td ></td></tr>";
                    }
                }
                ?>
            </tbody>
        </table>            
    </body>    
</html>
<script>
    var e = '<?php echo $status ?>';
    $('#status').val(e);
</script>

