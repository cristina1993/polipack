<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_inconforme_inventario.php'; //cambiar clsClase_productos
$Set = new Clase_inconforme_inventario();
if (isset($_GET[txt], $_GET[prod], $_GET[fecha1], $_GET[fecha2])) {
    $nm = trim(strtoupper($_GET[txt]));
    $prod = trim(strtoupper($_GET[prod]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($nm)) {
        $txt = " and (m.mov_documento like '%$nm%' or m.mov_guia_transporte like '%$nm%' or c.cli_raz_social like '%$nm%' or t.trs_descripcion like '%$nm%') and m.mov_fecha_trans between '$fec1' and '$fec2'";
    } else if (!empty($prod)) {
        $txt = "and (p.pro_codigo like '%$prod%' or p.pro_descripcion like '%$prod%') and m.mov_fecha_trans between '$fec1' and '$fec2'";
    } else {
        $txt = " and m.mov_fecha_trans between '$fec1' and '$fec2' ";
    }
    $cns = $Set->lista_buscar_kardexpt($txt);
} else {
    $txt = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
}
$a = '"@"';
$sty = "mso-number-format:$a";
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
            function exportar_excel() {
                $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                $("#tbl2").append($("#tbl tbody").clone()).html();
                $("#tbl2").append($("#tbl tfoot").clone()).html();
                $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                return true;
            }
        </script> 
        <style>
            #mn58,
            #mn63,
            #mn74,
            #mn79,
            #mn84,
            #mn89,
            #mn94,
            #mn99,
            #mn104,
            #mn109{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
            }
            *{
                text-transform: uppercase;
            }
            input{
                background:#f8f8f8 !important; 
            }
        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <tr><td colspan="13"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></td></tr>
            <tr><td colspan="13" align="center"><?PHP echo 'KARDEX DE PRODUCTO SEMIELABORADO' ?></td></tr>
            <tr>
                <td colspan="13"><?php echo 'Desde: ' . $fec1 . ' Hasta: ' . $fec2 ?></td>
            </tr>
        </table>
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
                <center class="cont_title" ><?PHP echo 'KARDEX DE PRODUCTO SEMIELABORADO INCONFORME' ?></center>
                <center class="cont_finder">
                    <form id="exp_excel" style="float:right;margin-top:6px;padding:0px" method="post" action="../Includes/export.php?tipo=1" onsubmit="return exportar_excel()"  >
                        <input type="submit" value="Excel" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        PRODUCTO:<input style="mso-number-format:'@'" type="text" name="prod" size="15" id="prod" value="<?php echo $prod ?>" />
                        MOVIMIENTO:<input type="text" name="txt" size="35" id="txt" value="<?php echo $nm ?>" list="transacciones" />
                        DESDE:<input type="text" name="fecha1" size="15" id="fecha1" value="<?php echo $fec1 ?>"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" name="fecha2" size="15" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form> 

                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="6">Producto</th>
                    <th colspan="4">Documento</th>
                    <th>Transaccion</th>
                    <th colspan="3">Cantidad</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Usuario</th>
                    <th>Codigo</th>
                    <!--<th>Lote</th>-->
                    <th>Descripcion</th>
                    <th>Unidad</th>
                    <th>Fecha de transaccion</th>
                    <th>Documento No</th>
                    <th>Guia de remision</th>
                    <th>Proveedor</th>
                    <th>Tipo</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Saldo</th>
                </tr>        
            </thead>

            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                $mp = null;
                $mp_code = null;
                $tabla = null;

                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $j++;

                    if ($rst[trs_operacion] == 0) {
                        $ing = $rst['mov_cantidad'];
                        $egr = '';
                    } else {
                        $ing = '';
                        $egr = $rst['mov_cantidad'];
                    }
                    if ($mp != $rst[pro_id] && $n != 1) {
                        $sal = 0;
                        echo "<tr>
                                <td class='totales' ></td>
                                <td class='totales' ></td>
                                <td class='totales' ></td>
                                <td class='totales' ></td>
                                <td class='totales' ></td>
                                <td class='totales' ></td>
                                <td class='totales' ></td>
                                <td class='totales' ></td>
                                <td class='totales' >Total</td>                                
                                <td class='totales' >$mp_code</td>                                
                                <td class='totales' align='right' >" . number_format($t_cnt, 2) . "</td>
                                <td class='totales' align='right'>" . number_format($t_egr, 2) . "</td>
                                <td class='totales' align='right'>" . number_format($t_sal, 2) . "</td>
                            </tr>";
                        $t_cnt = 0;
                        $t_egr = 0;
                        $t_sal = 0;
                        $cnt = 0;
                        $cnt2 = 0;
                    }
                    if ($mp != $rst[pro_id]) {
                        $rst_ant = pg_fetch_array($Set->total_ingreso_egreso($rst['pro_id'], $fec1));
                        $aing = $rst_ant[ingreso];
                        $aegr = $rst_ant[egreso];
                        $ant = $aing - $aegr;
                        echo "<tr style='font-weight:bolder'>
                                <td>" . $j++ . "</td>
                                <td></td>
                                <td style='$sty'>$rst[pro_codigo]</td>
                                <td>$rst[pro_descripcion] </td>
                                <td>$rst[pro_uni] </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>                                
                                <td>SALDO ANTERIOR</td>
                                <td align='right'></td>
                                <td align='right'></td>
                                <td align='right'>" . number_format($ant, 2) . "</td>
                            </tr>";
                    }
                    $sal = $ant + $sal + $ing - $egr;
                    $c = "'";
                    echo "<tr>
                            <td>$j</td>
                            <td>$rst[mov_usuario]</td>
                            <td style='$sty'>$rst[pro_codigo]</td>
                            <td>$rst[pro_descripcion]</td>
                            <td>$rst[pro_uni]</td>
                            <td>$rst[mov_fecha_trans]</td>
                            <td style='$sty'>$rst[mov_documento]</td>
                            <td>$rst[mov_guia_transporte]</td>
                            <td>$rst[cli_raz_social]</td>
                            <td>$rst[trs_descripcion]</td>
                            <td align='right'>" . number_format($ing, 2) . "</td>
                            <td align='right'>" . number_format($egr, 2) . "</td>
                            <td align='right'>" . number_format($sal, 2) . "</td>
                        </tr>";
                    $t_cnt+=$ing;
                    $cnt+=$ing + $aing;
                    $t_egr+=$egr;
                    $cnt2+=$egr + $aegr;
                    $t_sal = $cnt - $cnt2;
                    $mp = $rst[pro_id];
                    $mp_code = $rst[pro_codigo];
                    $tabla = $rst[mov_pago];
                    $ant = 0;
                    $aing = 0;
                    $aegr = 0;
                    $ing = 0;
                    $egr = 0;
                    if ($t_cnt == 0) {
                        $t_cnt = '';
                    }
                    if ($t_egr == 0) {
                        $t_egr = '';
                    }
                }
                echo "<tr>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' >TOTAL</td>                                
                        <td class='totales' >$mp_code</td>
                        <td class='totales' align='right'>" . number_format($t_cnt, 2) . "</td>
                        <td class='totales' align='right'>" . number_format($t_egr, 2) . "</td>
                        <td class='totales' align='right' >" . number_format($t_sal, 2) . "</td>
                    </tr>";
                ?>
            </tbody>
        </table>            
    </body>    
</html>
<datalist id="transacciones">
    <?php
    $cns_trans = $Set->lista_combo_transacciones();
    while ($rst_tran = pg_fetch_array($cns_trans)) {
        ?> 
        <option value="<?php echo$rst_tran[trs_descripcion] ?>"><?php echo$rst_tran[trs_descripcion] ?></option>;
        <?php
    }
    ?>  
</datalist>

