<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php'; //cambiar clsClase_productos
$Set = new Set();
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    $bod = $_GET[bod];
    if ($bod == '') {
        $bod = 'no';
    }
    if (!empty($txt)) {
        $txt = "AND (ins.ins_b like '%$txt%' or ins.ins_a like '%$txt%' ) and mov_ubicacion='$bod' AND mov_fecha_trans BETWEEN '$fec1' AND '$fec2'";
    } else {
        $txt = "AND mov_fecha_trans BETWEEN '$fec1' AND '$fec2' and mov_ubicacion='$bod'";
    }
    $cns = $Set->lista_movimientos_insumos($txt);
    $nm = trim(strtoupper($_GET[txt]));
} else {
    $fec1 = date("Y-m-d");
    $fec2 = date("Y-m-d");
}
$a='"@"';
$sty = "mso-number-format:$a";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            var ids = '<?php echo $bod ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                if (ids == 'no') {
                    alert('Elija Ubicacion');
                }
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function exportar_excel() {
                $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                $("#tbl2").append($("#tbl tbody").clone()).html();
                $("#tbl2").append($("#tbl tfoot").clone()).html();
                $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                return true;
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script> 
        <style>
            #mn57,
            #mn62,
            #mn73,
            #mn78,
            #mn83,
            #mn88,
            #mn93,
            #mn98,
            #mn103,
            #mn108{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
            }
            input{
                background:#f8f8f8 !important; 
            }
        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <tr><td colspan="16"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></td></tr>
            <tr><td colspan="16" align="center"><?PHP echo 'KARDEX DE MATERIA PRIMA' ?></td></tr>
            <tr>
                <?php
                switch ($bod) {
                    case 1:$bodega = "COSTURA";
                        break;
                    case 2:$bodega = "BODEGA2";
                        break;
                    case 3:$bodega = "BODEGA3";
                        break;
                    case 4:$bodega = "OTRO";
                        break;
                }
                ?>
                <td colspan="16"><?php echo 'UBICACION: ' . $bodega ?></td>

            </tr>
            <tr>
                <td colspan="16"><?php echo 'Desde: ' . $fec1 . ' Hasta: ' . $fec2 ?></td>
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
                <center class="cont_title" ><?PHP echo 'KARDEX DE MATERIA PRIMA' ?></center>
                <center class="cont_finder">
                    <form id="exp_excel" style="float:right;margin-top:6px;padding:0px" method="post" action="../Includes/export.php?tipo=2" onsubmit="return exportar_excel()"  >
                        <input type="submit" value="Excel" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        UBICACION:
                        <select id="bod" name="bod" >
                            <option value="">SELECCIONE</option>
                            <option value="1">Costura</option>
                            <option value="2">Bodega2</option>
                            <option value="3">Bodega3</option>
                        </select>
                        CODIGO:<input type="text" name="txt" size="10" id="txt" value="<?php echo $nm ?>"/>
                        DESDE:<input type="text" name="fecha1" size="10" id="fecha1" value="<?php echo $fec1 ?>"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" name="fecha2" size="10" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
                <tr>
                    <th colspan="3">Producto terminado</th>
                    <th colspan="4">Transaccion</th>
                    <th colspan="3">Entrada</th>
                    <th colspan="3">Salida</th>
                    <th colspan="3">Saldo</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Refencia</th>
                    <th>Descripcion</th>
                    <th>Documento</th>
                    <th>Fecha</th>
                    <th>Procedencia/Destino</th>
                    <th>Transaccion</th>
                    <th>Cantidad</th>
                    <th>Costo/U</th>
                    <th>Costo/T</th>
                    <th>Cantidad</th>
                    <th>Costo/U</th>
                    <th>Costo/T</th>
                    <th>Cantidad</th>
                    <th>Costo/U</th>
                    <th>Costo/T</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?php
                $i = 0;
                $n = 0;
                $grup = '';
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    switch ($rst[mov_procedencia_destino]) {
                        case 1:$proc = "Costura";
                            break;
                        case 2:$proc = "Bodega2";
                            break;
                        case 3:$proc = "Bodega3";
                            break;
                        case 4:$proc = "Otro";
                            break;
                    }
                    if ($rst[trs_operacion] == 0) {
                        $op = null;
                        $ing = $rst[mov_cantidad];
                        $uin = $rst[mov_v_unit];
                        $tin = $rst[mov_cantidad] * $rst[mov_v_unit];
                        $egr = '';
                        $ueg = '';
                        $teg = '';
                    } else {
                        $ing = '';
                        $uin = '';
                        $tin = '';
                        $egr = $rst[mov_cantidad];
                        $ueg = $rst[mov_v_unit];
                        $teg = $rst[mov_cantidad] * $rst[mov_v_unit];
                    }

                    if ($t_cnt == 0) {
                        $t_cnt = '';
                    }
                    if ($t_pu1 == 0) {
                        $t_pu1 = '';
                    }
                    if ($t_pk1 == 0) {
                        $t_pk1 = '';
                    }
                    if ($t_egr == 0) {
                        $t_egr = '';
                    }
                    if ($t_pu2 == 0) {
                        $t_pu2 = '';
                    }
                    if ($t_pk2 == 0) {
                        $t_pk2 = '';
                    }

                    if ($mp != $rst[id] && $n != 1) {
                        $sal = 0;
                        $tsa = 0;
                        $usa = 0;

                        echo "<tr>
                            <td class='totales' ></td>
                            <td class='totales' ></td>
                            <td class='totales' ></td>
                            <td class='totales' ></td>
                            <td class='totales' ></td>
                            <td class='totales' >TOTAL</td>                                
                            <td class='totales' style='$sty'>$mp_code</td>
                            <td class='totales' align='right'>" . number_format($t_cnt, 1) . "</td>
                            <td class='totales' align='right'>" . number_format($t_pu1, 2) . "</td>
                            <td class='totales' align='right'>" . number_format($t_pk1, 2) . "</td>
                            <td class='totales' align='right'>" . number_format($t_egr, 1) . "</td>
                            <td class='totales' align='right'>" . number_format($t_pu2, 2) . "</td>
                            <td class='totales' align='right'>" . number_format($t_pk2, 2) . "</td>
                            <td class='totales' align='right'>" . number_format($t_sal, 1) . "</td>
                            <td class='totales' align='right'>" . number_format($t_spu, 2) . "</td>
                            <td class='totales' align='right'>" . number_format($t_spk, 2) . "</td>
                        </tr>";


                        $t_cnt = 0;
                        $t_egr = 0;
                        $t_sal = 0;
                        $t_pk1 = 0;
                        $t_pk2 = 0;
                        $t_spk = 0;
                        $t_pu1 = 0;
                        $t_pu2 = 0;
                        $pk1 = 0;
                        $pk2 = 0;
                        $cnt = 0;
                        $cne = 0;
                    }
                    if ($mp != $rst[id]) {
                        $rst_ant = pg_fetch_array($Set->total_ingreso_egreso_insumos($rst[id], $fec1, $bod));
                        $aing = $rst_ant[ingreso];
                        $aegr = $rst_ant[egreso];
                        $au1 = $rst_ant[p1];
                        $au2 = $rst_ant[p2];
                        if ($aegr != 0) {
                            $aegr = $rst_ant[egreso];
                            $ap2 = $rst_ant[p2];
                        }
                        $ap1 = $aing * $au1;
                        $ap2 = $aegr * $au2;
                        $ant = $aing - $aegr;
                        $atu = $au1 + $au2;
                        $atp = $ant * $atu;
                        echo "<tr style='font-weight:bolder'>
                            <td>" . $n++ . "</td>
                            <td>$rst[ins_a] </td>
                            <td style='$sty'>$rst[ins_b]</td>
                            <td></td>
                            <td align='center' style='text-transform:lowercase'>$rst[mp_unidad]</td>  
                            <td></td>                                
                            <td>SALDO ANTERIOR</td>
                            <td></td>                                
                            <td></td>                                
                            <td></td>                                
                            <td></td>              
                            <td></td>              
                            <td></td>              
                            <td align='right'>" . number_format($ant, 1) . "</td>
                            <td align='right'>" . number_format($atu, 2) . "</td>
                            <td align='right'>" . number_format($atp, 2) . "</td>
                        </tr>";
                    }
                    $sal = $ant + $sal + $ing - $egr;
                    $tsa = $atp + $tsa + $tin - $teg;
                    $usa = $tsa / $sal;

                    if ($t_cnt == 0) {
                        $t_cnt = '';
                    }
                    if ($t_pu1 == 0) {
                        $t_pu1 = '';
                    }
                    if ($t_pk1 == 0) {
                        $t_pk1 = '';
                    }
                    if ($t_egr == 0) {
                        $t_egr = '';
                    }
                    if ($t_pu2 == 0) {
                        $t_pu2 = '';
                    }
                    if ($t_pk2 == 0) {
                        $t_pk2 = '';
                    }
                    echo "<tr style='height: 20px' id='fila'>
                        <td>" . $n . "</td>
                        <td style='$sty'>$rst[ins_a]</td>
                        <td>$rst[ins_b]</td>
                        <td style='$sty'>$rst[mov_documento]</td>
                        <td>$rst[mov_fecha_trans]</td>
                        <td>$proc</td>
                        <td>$rst[trs_descripcion]</td>
                        <td align='right'>" . number_format($ing, 1) . "</td>
                        <td align='right'>" . number_format($uin, 2) . "</td>
                        <td align='right'>" . number_format($tin, 2) . "</td>
                        <td align='right'>" . number_format($egr, 1) . "</td>
                        <td align='right'>" . number_format($ueg, 2) . "</td>
                        <td align='right'>" . number_format($teg, 2) . "</td>
                        <td align='right'>" . number_format($sal, 1) . "</td>
                        <td align='right'>" . number_format($usa, 2) . "</td>
                        <td align='right'>" . number_format($tsa, 2) . "</td>
                    </tr>";

                    $cnt+=$ing + $aing;
                    $t_cnt+=$ing;
                    $pk1+=$tin + $ap1;
                    $t_pk1+=$tin;
                    $t_egr+=$egr;
                    $cne+=$egr + $aegr;
                    $pk2+=$teg + $ap2;
                    $t_pk2+=$teg;
                    $t_sal = $cnt - $cne;
                    $t_spk = $pk1 - $pk2;
                    $t_spu = $t_spk / $t_sal;
                    $t_pu1 = $t_pk1 / $t_cnt;
                    $t_pu2 = $t_pk2 / $t_egr;
                    if ($t_pu2 == null) {
                        $t_pu2 = 0;
                    }
                    $mp = $rst[id];
                    $mp_code = $rst[ins_a];
                    $ing = 0;
                    $egr = 0;
                    $ant = 0;
                    $uin = 0;
                    $ueg = 0;
                    $atp = 0;
                    $aing = 0;
                    $aegr = 0;
                    $ap1 = 0;
                    $ap2 = 0;
                }

                if ($t_cnt == 0) {
                    $t_cnt = '';
                }
                if ($t_pu1 == 0) {
                    $t_pu1 = '';
                }
                if ($t_pk1 == 0) {
                    $t_pk1 = '';
                }
                if ($t_egr == 0) {
                    $t_egr = '';
                }
                if ($t_pu2 == 0) {
                    $t_pu2 = '';
                }
                if ($t_pk2 == 0) {
                    $t_pk2 = '';
                }
                echo "<tr>
                    <td class='totales' ></td>
                    <td class='totales' ></td>
                    <td class='totales' ></td>
                    <td class='totales' ></td>
                    <td class='totales' ></td>
                    <td class='totales' >TOTAL</td>                                
                    <td class='totales' style='$sty'>$mp_code</td>
                    <td class='totales' align='right'>" . number_format($t_cnt, 1) . "</td>
                    <td class='totales' align='right'>" . number_format($t_pu1, 2) . "</td>
                    <td class='totales' align='right'>" . number_format($t_pk1, 2) . "</td>
                    <td class='totales' align='right'>" . number_format($t_egr, 1) . "</td>
                    <td class='totales' align='right'>" . number_format($t_pu2, 2) . "</td>
                    <td class='totales' align='right'>" . number_format($t_pk2, 2) . "</td>
                    <td class='totales' align='right'>" . number_format($t_sal, 1) . "</td>
                    <td class='totales' align='right'>" . number_format($t_spu, 2) . "</td>
                    <td class='totales' align='right'>" . number_format($t_spk, 2) . "</td>
                </tr>";
                ?>
            </tbody>
        </table>            
    </body>    
</html>
<script>
    var e = '<?php echo $bod ?>';
    $('#bod').val(e);
</script>



