<?php
include_once '../Includes/permisos.php';
//include_once("../Clases/clsSecciones.php");
//include_once '../Includes/library1.php';
//$Sec = new Secciones();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            $(function () {
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "f_month_from", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "f_month_until", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
            });
//            function config() {
//
//                var extt = 'off'
//                var impp = 'off'
//                var sel = 'off'
//                var gen = 'off'
//                var from = f_month_from.value;
//                var until = f_month_until.value;
//                var sec = 0;
////                var sec = sec_id.value
//                if ($('#ext').attr('checked') == true) {
//                    var extt = 'on';
//                }
//                if ($('#imp').attr('checked') == true) {
//                    var impp = 'on';
//                }
//                if ($('#sell').attr('checked') == true) {
//                    var sel = 'on';
//                }
//                if ($('#rptGeneral').attr('checked') == true) {
//                    var gen = 'on';
//                }
//                emailwindow = dhtmlmodal.open("EmailBox", "iframe", "configMail.php?ext=" + extt + "&imp=" + impp + "&sell=" + sel + "&rptGen=" + gen + "&from=" + from + "&until=" + until + "&sec=" + sec, "Configurar Cuentas de Correo", "width=400%,height=400%,center=1,resize=0,scrolling=1", "recal")
//            }
            function hblprodDiaria() {
                if ($('#prodDiaria').attr('checked') == true) {
                    $('#ext').attr('checked', true);
                    $('#imp').attr('checked', true);
                    $('#sell').attr('checked', true);
                    $('#drd').attr('checked', true);
                    $('#mp').attr('checked', true);
                    $('#prod').attr('checked', true);
                    $('#resOperativo').attr('checked', false);
                    $('#repGen').attr('checked', false);
                    pedido.value = ''
                    $('#pedidos').attr('checked', false);
                    hblresOperativo()
                    hblGen()
                } else {
                    $('#ext').attr('checked', false);
                    $('#imp').attr('checked', false);
                    $('#sell').attr('checked', false);
                    $('#drd').attr('checked', false);
                    $('#mp').attr('checked', false);
                    $('#prod').attr('checked', false);
                }
            }
            function hblresOperativo()
            {
                if ($('#resOperativo').attr('checked') == true)
                {
                    $('#extm').attr('checked', true);
                    $('#impm').attr('checked', true);
                    $('#sellm').attr('checked', true);
                    $('#mpm').attr('checked', true);
                    $('#prodm').attr('checked', true);
                    $('#maqm').attr('checked', true);
                    $('#repm').attr('checked', true);
                    $('#resec').attr('checked', true);
                    $('#prodDiaria').attr('checked', false);
                    $('#repGen').attr('checked', false);
                    pedido.value = '';
                    $('#pedidos').attr('checked', false);
                    hblprodDiaria();
                    hblGen();
                } else {
                    $('#extm').attr('checked', false);
                    $('#impm').attr('checked', false);
                    $('#sellm').attr('checked', false);
                    $('#mpm').attr('checked', false);
                    $('#repm').attr('checked', false);
                    $('#prodm').attr('checked', false);
                    $('#maqm').attr('checked', false);
                    $('#resec').attr('checked', false);
                }
            }
            function hblGen()
            {
                if ($('#repGen').attr('checked') == true)
                {
                    $('#res').attr('checked', true);
                    $('#rptGeneral').attr('checked', true);
                    $('#prodDiaria').attr('checked', false);
                    $('#resOperativo').attr('checked', false);
                    pedido.value = '';
                    $('#pedidos').attr('checked', false);
                    hblprodDiaria();
                    hblresOperativo();

                } else {
                    $('#res').attr('checked', false);
                    $('#rptGeneral').attr('checked', false);
                }
            }
            function hbl0prodDiaria(o)
            {
                alert('ok');
                if ($('#ext').attr('checked') == true ||
                        $('#imp').attr('checked') == true ||
                        $('#sell').attr('checked') == true ||
                        $('#drd').attr('checked') == true ||
                        $('#mp').attr('checked') == true ||
                        $('#prod').attr('checked') == true)
                {
                    if (o == 0) {
                        $('#sell').attr('checked', false);
                    } else if (o == 1) {
                        $('#ext').attr('checked', false);
                    }
                    $('#prodDiaria').attr('checked', true);
                    $('#resOperativo').attr('checked', false);
                    $('#repGen').attr('checked', false);
                    pedido.value = ''
                    $('#pedidos').attr('checked', false);
                    hblresOperativo()
                    hblGen()
                } else {
                    $('#prodDiaria').attr('checked', false);
                }
            }

            function hbl0resOperativo(o)
            {
                if ($('#extm').attr('checked') == true ||
                        $('#impm').attr('checked') == true ||
                        $('#sellm').attr('checked') == true ||
                        $('#mpm').attr('checked') == true ||
                        $('#prodm').attr('checked') == true ||
                        $('#repm').attr('checked') == true ||
                        $('#maqm').attr('checked') == true ||
                        $('#resec').attr('checked') == true)
                {
                    if (o == 0) {
                        $('#sellm').attr('checked', false);
                    } else if (o == 1) {
                        $('#extm').attr('checked', false);
                    }
                    $('#resOperativo').attr('checked', true);
                    $('#prodDiaria').attr('checked', false);
                    $('#repGen').attr('checked', false);
                    pedido.value = '';
                    $('#pedidos').attr('checked', false);
                    hblprodDiaria();
                    hblGen();
                } else {
                    $('#resOperativo').attr('checked', false);
                }
            }
            function hbl0Gen()
            {
                if ($('#res').attr('checked') == true ||
                        $('#rptGeneral').attr('checked') == true)
                {
                    $('#repGen').attr('checked', true);
                    $('#prodDiaria').attr('checked', false);
                    $('#resOperativo').attr('checked', false);
                    pedido.value = ''
                    $('#pedidos').attr('checked', false);
                    hblprodDiaria()
                    hblresOperativo()
                } else {
                    $('#repGen').attr('checked', false);
                }

            }

            function hblPedidos()
            {
                if ($('#pedidos').attr('checked') == true)
                {
                    $('#repGen').attr('checked', false);
                    $('#prodDiaria').attr('checked', false);
                    $('#resOperativo').attr('checked', false);
                    hblprodDiaria()
                    hblresOperativo()
                    hblGen()
                } else {
                    $('#detProd').attr('checked', false);
                    $('#prodMaq').attr('checked', false);
                }

            }

//

            function redirection()
            {

                if ($('#resec').attr('checked') == true)
                {
                    frmMenuReport.action = 'rptresumen.php';
                } else {

                    if ($('#prodDiaria').attr('checked') == true || $('#repGen').attr('checked') == true || $('#mpm').attr('checked') == true || $('#prodm').attr('checked') == true || $('#maqm').attr('checked') == true)
                    {
                        if ($('#drd').attr('checked') == true) {
                            frmMenuReport.action = 'rptResumenOperativo.php';
                        } else {
                            frmMenuReport.action = 'rptProductionDailyReport.php';
                        }
                    } else if ($('#extm').attr('checked') == true || $('#impm').attr('checked') == true || $('#sellm').attr('checked') == true || $('#repm').attr('checked') == true) {
                        frmMenuReport.action = 'rptResumenOperativo.php';
                    } else if ($('#pedidos').attr('checked') == true)
                    {
                        if ($('#detProd').attr('checked') == true)
                        {
                            frmMenuReport.action = 'prodSobrantes.php'
                        } else if ($('#prodMaq').attr('checked') == true) {
                            frmMenuReport.action = 'rptProdMaq.php'
                        } else {
                            if (pedido.value.length == 0) {
                                alert('Debe Elejir un Criteio de Pedidos')
                                pedido.focus()
                                return false
                            } else {
                                frmMenuReport.action = 'pedListarpt.php'
                            }
                        }
                    } else {
                        alert('Debe Elejir un Criteio')
                        return false
                    }
                }
                $('#frmMenuReport').submit();
            }
//            function openclose()
//            {
//
//                if (boton1.value === '<<')
//                {
//                    boton1.value = '>>';
//                    doc = document.getElementById('framePrint');
//                    doc.width = '98%';
//                    doc = document.getElementById('frmMenuReport');
//                    doc.style.transition = 'all 0.5s ease-in';
//                    doc.style.background = '#015b85';
//                    doc.style.opacity = 0.2;
//                } else {
//                    boton1.value = '<<';
//                    doc = document.getElementById('framePrint');
//                    doc.width = '85%';
//                    doc = document.getElementById('frmMenuReport');
//                    doc.style.transition = 'all 0.5s ease-out';
//                    doc.style.background = 'none';
//                    doc.style.opacity = 1;
//                }
//            }

//            function valFecha(val, id)
//            {
//                v = val.split('-');
//                if (val.length !== 10 || v[0].length !== 4 || v[1].length !== 2 || v[2].length !== 2)
//                {
//                    doc = document.getElementById(id);
//                    doc.focus();
//                    alert('Formato de fecha debe ser (yyyy-mm-dd)');
//                    return false;
//                }
//            }
//            function smsclose()
//            {
//                sms = document.getElementById('sms');
//                sms.style.visibility = 'hidden';
//            }
            function vista_excel() {
                if ($('#resec').attr('checked') == true) {
                    frmMenuReport.action = 'rptresumen.php';
                    load_file();
                } else {
                    if ($('#prodDiaria').attr('checked') == true || $('#repGen').attr('checked') == true || $('#mpm').attr('checked') == true || $('#prodm').attr('checked') == true || $('#maqm').attr('checked') == true)
                    {
                        if ($('#drd').attr('checked') == true) {
                            frmMenuReport.action = 'rptResumenOperativoExcel.php';
                        } else {
                            frmMenuReport.action = 'rptProductionDailyExcel.php';
                        }
                    } else if ($('#extm').attr('checked') == true || $('#impm').attr('checked') == true || $('#sellm').attr('checked') == true || $('#repm').attr('checked') == true) {
                        frmMenuReport.action = 'rptResumenOperativoExcel.php';
                    } else if ($('#pedidos').attr('checked') == true) {
                        if ($('#detProd').attr('checked') == true) {
                            frmMenuReport.action = 'prodSobrantes.php?exc=1'
                        } else if ($('#prodMaq').attr('checked') == true) {
                            frmMenuReport.action = 'rptProdMaq.php'
                        } else {
                            if (pedido.value.length == 0) {
                                alert('Debe Elejir un Criterio de Pedidos')
                                pedido.focus();
                                return false;
                            } else {
                                frmMenuReport.action = 'pedListarpt.php'
                            }
                        }
                    } else {
                        alert('Debe Elejir un Criteio')
                        return false
                    }
                }
                $('#frmMenuReport').submit();
            }

        </script>
    </head>
    <body>

        <table style="width:14%" id="tbl" align="left">
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
<!--<img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />-->                            
                </center>
                <form action="" method="post"  target="framePrint" id="frmMenuReport" autocomplete="off" >  
                    <thead>
                        <tr>
                            <td colspan="2" class="finder">
                                <input class=""  type="button"  name="excel" id="excel" value="Generar" onclick="vista_excel()" style="float:right "/>
                                <!--<input class=""  type="button"   name="generate" id="generate" value="Generar" onclick="redirection()()" style="float:right "  />-->
                            </td>                
                        </tr>
                    </thead>
                    <tr hidden>
                        <td colspan="2" align="right">Validar Fecha:<input type="checkbox" name="valDate" id="valDate" /></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Desde:<input  type="text" size="12" name="f_month_from" id="f_month_from" value="<?php echo date('Y-m-d') ?>" onchange="valFecha(this.value, this.id)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')" />
                            <img src="../img/calendar.png" id="im-campo1"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Hasta: <input  type="text" size="12" name="f_month_until" id="f_month_until" value="<?php echo date('Y-m-d') ?>" onchange="valFecha(this.value, this.id)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')" />
                            <img src="../img/calendar.png" id="im-campo2"/>
                        </td>
                    </tr>

                    <thead>
                        <tr>
                            <th colspan="2" align="left">
                                <input hidden type="checkbox"  checked name="prodDiaria" id="prodDiaria" onclick="hblprodDiaria()" />
                                <label for="prodDiaria">PRODUCCION DIARIA</label>
                            </th>
                        </tr>
                    </thead>   
                    <tr>
                        <td>Extrusion</td>
                        <td>
                            <input type="checkbox" name="ext" checked id="ext" onclick="hbl0prodDiaria(0)" />
                        </td>
                    </tr>
                    <tr hidden>
                        <td >Impresion</td>
                        <td >
                            <input type="checkbox" name="imp"  id="imp" onclick="hbl0prodDiaria()" />
                        </td>
                    </tr>
                    <tr>
                        <td >Corte</td>
                        <td >
                            <input type="checkbox" name="sell"  id="sell" onclick="hbl0prodDiaria(1)" />
                        </td>
                    </tr>
                    <tr hidden>
                        <td >Desperdicio Reciclado</td>
                        <td >
                            <input type="checkbox" name="drd" id="drd" onclick="hbl0prodDiaria()" />
                        </td>
                    </tr>
                    <tr hidden>
                        <td >Consumo MP</td>
                        <td >
                            <input type="checkbox" name="mp" id="mp" onclick="hbl0prodDiaria();" />
                        </td>
                    </tr>
                    <tr style="display:none">
                        <td >Por producto</td>
                        <td >
                            <input type="checkbox" name="prod" id="prod" onclick="hbl0prodDiaria();" />
                        </td>
                    </tr>

                    <thead>
                        <tr>
                            <th colspan="2" align="left">
                                <input hidden type="checkbox" name="resOperativo" id="resOperativo" onclick="hblresOperativo();" />
                                <label for="resOperativo">RESUMEN OPERATIVO</label>
                            </th>
                        </tr>
                    </thead>
                    <tr>
                        <td >Extrusion</td>
                        <td >
                            <input type="checkbox" name="extm" id="extm" onclick="hbl0resOperativo(0)" />
                        </td>
                    </tr>
                    <tr hidden>
                        <td >Impresion</td>
                        <td >
                            <input type="checkbox" name="impm" id="impm" onclick="hbl0resOperativo()" />
                        </td>
                    </tr>
                    <tr>
                        <td >Corte</td>
                        <td >
                            <input type="checkbox" name="sellm" id="sellm" onclick="hbl0resOperativo(1)" />
                        </td>
                    </tr>
                    <tr style="display:none">
                        <td >Consumo de MP</td>
                        <td >
                            <input type="checkbox" name="mpm" id="mpm" onclick="hbl0resOperativo()" />
                        </td>
                    </tr>

                    <tr style="display:none">
                        <td >Desperdicio Reciclado</td>
                        <td >
                            <input type="checkbox" name="repm" id="repm" onclick="hbl0resOperativo()" />
                        </td>
                    </tr>
                    <tr style="display:none">
                        <td >Por Producto</td>
                        <td >
                            <input type="checkbox" name="prodm" id="prodm" onclick="hbl0resOperativo()" />
                        </td>
                    </tr>
                    <tr style="display:none">
                        <td >Por Maquina</td>
                        <td >
                            <input type="checkbox" name="maqm" id="maqm" onclick="hbl0resOperativo()" />
                        </td>
                    </tr>
                    <tr style="display:none">
                        <td >Resumen por Seccion:</td>
                        <td >
                            <input type="checkbox" name="resec" id="resec" onclick="hbl0resOperativo()" />
                        </td>
                    </tr>
                    <thead hidden>
                        <tr>
                            <th colspan="2" align="left">
                                <input type="checkbox" name="repGen" id="repGen" onclick="hblGen()" />
                                <label for="repGen">GENERAL DE FLUJO</label>
                            </th>
                        </tr>
                    </thead>
                    <tr hidden>
                        <td >Resumen</td>
                        <td >
                            <input type="checkbox" name="res" id="res" onclick="hbl0Gen()" />
                        </td>
                    </tr>
                    <tr hidden>
                        <td >Grafico</td>
                        <td >
                            <input type="checkbox" name="rptGeneral" id="rptGeneral" onclick="hbl0Gen()" />
                        </td>
                    </tr>
                    <thead hidden>
                        <tr>
                            <th colspan="2" align="left">
                                <input type="checkbox" name="pedidos"   id="pedidos" onclick="hblPedidos()"   />
                                <label for="pedidos">PEDIDOS</label>
                            </th>
                        </tr>
                    </thead>            
                    <tr hidden>
                        <td colspan="2" >
                            <input type="text" name="pedido" id="pedido" style="text-transform:uppercase "  size="25" placeholder="Pedido/Cliente/Producto" onchange="('#pedidos').attr('checked', true);
                                    hblPedidos()" />
                        </td>
                    </tr>
                    <tr hidden> 
                        <td >Detalle Productos </td>       
                        <td >
                            <input type="checkbox" id="detProd" name="detProd" onclick="$('#pedidos').attr('checked', true);
                                    hblPedidos()" />
                        </td>
                    </tr>
                    <tr hidden> 
                        <td >Produccion Maquinas </td>       
                        <td >
                            <input type="checkbox" id="prodMaq" name="prodMaq" onclick="$('#pedidos').attr('checked', true);
                                    hblPedidos()" />
                        </td>
                    </tr>
                </form>   
        </table>

        <iframe style="position:absolute;top:0px;right:0px;height:100%" frameborder="0" id="framePrint" width="86%" name="framePrint" ></iframe>
    </body>
</html>