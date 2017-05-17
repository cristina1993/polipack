<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[txt])) {
    $nm = trim(strtoupper($_GET[txt]));
    $emp_id = $_GET[emp_id];
    $hasta = $_GET[hasta];
    $desde = $_GET[desde];
    if (!empty($nm)) {
        $texto = "AND (mp.mp_codigo like '%$nm%' OR mp.mp_referencia like '%$nm%') and mi.mov_fecha_trans between '$desde' and '$hasta'";
    } else if (!empty($_GET[emp_id])) {
        $texto = "and mp.emp_id=$emp_id and mi.mov_fecha_trans between '$desde' and '$hasta'";
    } else {
        $texto = " and mi.mov_fecha_trans between '$desde' and '$hasta'";
    }
    $cns = $Set->lista_inv_kardex($texto);
} else {
    $hasta = date("Y-m-d");
    $desde = date("Y-m-d");
}
$a = '"@"';
$sty = "mso-number-format:$a";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Movimiento de Materia Prima</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }

            );

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/Form_i_reg_movmp.php';
                        look_menu();
                        break;
                }

            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 20, id: id}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_kardex.php';
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

            function exportar_excel() {
                $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                $("#tbl2").append($("#tbl tbody").clone()).html();
                $("#tbl2").append($("#tbl tfoot").clone()).html();
                $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                return true;
            }
        </script> 
        <style>
            #mn30{
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
            <tr><td colspan="15"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></td></tr>
            <tr><td colspan="15" align="center"><?PHP echo 'KARDEX DE DE MATERIA PRIMA' ?></td></tr>
            <tr>
                <td colspan="15"><?php echo 'Desde: ' . $desde . ' Hasta: ' . $hasta ?></td>
            </tr>
        </table>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="" id="tbl" width='100%'>
            <caption class="tbl_head" >
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl(18, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >KARDEX DE INVENTARIO DE MATERIA PRIMA</center>
                <center class="cont_finder">
                    <form id="exp_excel" style="float:right;margin-top:6px;padding:0px" method="post" action="../Includes/export.php?tipo=2" onsubmit="return exportar_excel()"  >
                        <input type="submit" value="Excel" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                    <form method="GET" id="frmSearch" name="frm1" style="margin-top:5px; " action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo:<input type="text" name="txt" size="15" value="<?php echo $nm ?>"/>
                        Fabrica:
                        <select id="emp_id" name="emp_id" style="width:125px; font-size: 12px"  >
                            <?php
                            $cns_emp = $Set->lista_fabricas();
                            while ($rst_emp = pg_fetch_array($cns_emp)) {
                                echo "<option $sel value='$rst_emp[emp_id]'>$rst_emp[emp_descripcion]</option>";
                            }
                            ?>
                        </select>
                        DESDE:<input type="text" name="desde" id="desde" value="<?php echo $desde ?>" size="10"/>
                        <img src="../img/calendar.png" id="im-desde" width="16" />
                        HASTA:<input type="text"   name="hasta" value="<?php echo $hasta ?>"  id="hasta" size="10" />
                        <img src="../img/calendar.png" width="16"   id="im-hasta" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>                                
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
            </caption>

            <thead>
                <tr>
                    <th colspan="5">Materia Prima</th>
                    <th colspan="4">Documento</th>
                    <th>Transaccion</th>
                    <th colspan="3">Entrada</th>
                    <th colspan="3">Salida</th>
                    <th colspan="3">Saldo</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Presentacion</th>
                    <th>Unidad</th>
                    <th>Fecha Transaccion</th>
                    <th>Documento No</th>
                    <th>Guia de Recepcion</th>
                    <th>Proveedor</th>
                    <th>Tipo</th>
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
            <tbody id="tbody">
                <?PHP
                $n = 0;
                $mp = null;
                $mp_code = null;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    if ($rst[trs_operacion] == 0) {
                        $operador = null;
                        $ing = $rst[mov_cantidad];
                        $pu1 = $rst[mov_peso_unit];
                        $pk1 = $rst[mov_peso_total];
                        $egr = '';
                        $pu2 = '';
                        $pk2 = '';
                    } else {
                        $egr = $rst[mov_cantidad];
                        $ing = '';
                        $pu1 = '';
                        $pk1 = '';
                        $pu2 = $rst[mov_peso_unit];
                        $pk2 = $rst[mov_peso_total];
                    }
                    $rst_prov = pg_fetch_array($Set->lista_un_cliente($rst[mov_proveedor]));

                    if ($mp != $rst[mp_id] && $n != 1) {
                        $sal = 0;
                        $slp = 0;
                        if ($t_sal <= 0.009 && $t_sal >= -0.009) {
                            $t_spu = 0;
                            $t_spk = 0;
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
                            <td class='totales' style='$sty'> $mp_code </td>
                            <td class='totales' align='right'>" . number_format($t_cnt, 1) . "</td>
                            <td class='totales' align='right'>" . number_format($t_pu1, 4) . "</td>
                            <td class='totales' align='right'>" . number_format($t_pk1, 4) . "</td>
                            <td class='totales' align='right'>" . number_format($t_egr, 1) . "</td>
                            <td class='totales' align='right'>" . number_format($t_pu2, 4) . "</td>
                            <td class='totales' align='right'>" . number_format($t_pk2, 4) . "</td>
                            <td class='totales' align='right'>" . number_format($t_sal, 1) . "</td>
                            <td class='totales' align='right'>" . number_format($t_spu, 4) . "</td>
                            <td class='totales' align='right'>" . number_format($t_spk, 4) . "</td>
                        </tr>";
                        $t_cnt = 0;
                        $t_egr = 0;
                        $t_sal = 0;
                        $t_pk1 = 0;
                        $t_pk2 = 0;
                        $t_spk = 0;
                        $cne = 0;
                        $cnt = 0;
                        $tpk1 = 0;
                        $tpk2 = 0;
                    }
                    if ($mp != $rst[mp_id]) {
                        $rst_ant = pg_fetch_array($Set->total_ingreso_egreso_mp_ant($rst[mp_id], $desde));
                        $aing = $rst_ant[ingreso];
                        $aegr = $rst_ant[egreso];
                        $ap1 = $rst_ant[p1];
                        $ap2 = $rst_ant[p2];
                        if ($aegr != 0) {
                            $aegr = $rst_ant[egreso];
                            $ap2 = $rst_ant[p2];
                        }
                        $ant = $aing - $aegr;
                        $atp = $ap1 - $ap2;
                        $atu = $atp / $ant;
                        $au1 = $ap1 / $aing;
                        $au2 = $ap2 / $aegr;

                        echo "<tr style='font-weight:bolder'>
                            <td>" . $n++ . "</td>
                            <td style='$sty'> $rst[mp_codigo] </td>
                            <td> $rst[mp_referencia] </td>
                            <td> $rst[mp_presentacion] </td>
                            <td align='center' style='text-transform:lowercase'> $rst[mp_unidad] </td>  
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>                                
                            <td>SALDO ANTERIOR</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>   
                            <td></td>   
                            <td></td>   
                            <td align='right'>" . number_format($ant, 1) . "</td>
                            <td align='right'>" . number_format($atu, 4) . "</td>
                            <td align='right'>" . number_format($atp, 4) . "</td>
                        </tr>";
                    }
                    $sal = $ant + $sal + $ing - $egr;
                    $slp = $atp + $slp + $pk1 - $pk2;
                    $slu = $slp / $sal;
                    if ($sal <= 0.009 && $sal>= -0.009) {
                        $slp = 0;
                        $slu = 0;
                    }

                    echo "<tr>
                        <td> $n </td>
                        <td style='$sty'> $rst[mp_codigo] </td>
                        <td> $rst[mp_referencia] </td>
                        <td> $rst[mp_presentacion] </td>
                        <td align='center' style='text-transform:lowercase'> $rst[mp_unidad] </td>                        
                        <td> $rst[mov_fecha_trans] </td>
                        <td> $rst[mov_num_trans] </td>
                        <td style='$sty'> $rst[mov_documento] </td>
                        <td>" . trim($rst_prov['cli_apellidos'] . ' ' . $rst_prov['cli_nombres'] . ' ' . $rst_prov['cli_raz_social']) . "</td>
                        <td> $rst[trs_descripcion] </td>
                        <td align='right'>" . number_format($ing, 1) . "</td>
                        <td align='right'>" . number_format($pu1, 4) . "</td>
                        <td align='right'>" . number_format($pk1, 4) . "</td>
                        <td align='right'>" . number_format($egr, 1) . "</td>
                        <td align='right'>" . number_format($pu2, 4) . "</td>
                        <td align='right'>" . number_format($pk2, 4) . "</td>
                        <td align='right'>" . number_format($sal, 1) . "</td>
                        <td align='right'>" . number_format($slu, 4) . "</td>
                        <td align='right'>" . number_format($slp, 4) . "</td>
                    </tr>";
                    $t_cnt+=$ing;
                    $cnt+=$ing + $aing;
                    $tpk1+=$pk1 + $ap1;
                    $t_pk1+=$pk1;
                    $cne+=$egr + $aegr;
                    $t_egr+=$egr;
                    $t_pk2+=$pk2;
                    $tpk2+=$pk2 + $ap2;
                    $t_sal = $cnt - $cne;
                    $t_spk = $tpk1 - $tpk2;
                    $t_spu = $t_spk / $t_sal;
                    $t_pu1 = $t_pk1 / $t_cnt;
                    $t_pu2 = $t_pk2 / $t_egr;
                    if ($t_pu2 == null) {
                        $t_pu2 = 0;
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
                    if ($t_pu2 == 0) {
                        $t_pu2 = '';
                    }
                    if ($t_pk2 == 0) {
                        $t_pk2 = '';
                    }
                    if ($t_egr == 0) {
                        $t_egr = '';
                    }

                    $mp = $rst[mp_id];
                    $mp_code = $rst[mp_codigo];
                    $ing = 0;
                    $egr = 0;
                    $ant = 0;
                    $pk1 = 0;
                    $pk2 = 0;
                    $atp = 0;
                    $aing = 0;
                    $aegr = 0;
                    $ap1 = 0;
                    $ap2 = 0;
                    if ($t_sal <= 0.009 && $t_sal>= -0.009) {
                        $t_spu = 0;
                        $t_spk = 0;
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
                    <td class='totales' style='$sty'> $mp_code </td>
                    <td class='totales' align='right'>" . number_format($t_cnt, 1) . "</td>
                    <td class='totales' align='right'>" . number_format($t_pu1, 4) . "</td>
                    <td class='totales' align='right'>" . number_format($t_pk1, 4) . "</td>
                    <td class='totales' align='right'>" . number_format($t_egr, 1) . "</td>
                    <td class='totales' align='right'>" . number_format($t_pu2, 4) . "</td>
                    <td class='totales' align='right'>" . number_format($t_pk2, 4) . "</td>
                    <td class='totales' align='right'>" . number_format($t_sal, 1) . "</td>
                    <td class='totales' align='right'>" . number_format($t_spu, 4) . "</td>
                    <td class='totales' align='right'>" . number_format($t_spk, 4) . "</td>
                </tr>";
                ?>
            </tbody>
        </table>            
    </body>    
</html>
<script>
    var e = '<?php echo $emp_id ?>';
    $('#emp_id').val(e);
</script>
