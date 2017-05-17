<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$code = $_GET[code];
$rst_h = pg_fetch_array($Set->lista_un_pedidmp($code));
$guia = $rst_h[ped_num_orden];
$cns = $Set->lista_un_pedidmp_group($code);
$rst_sec = pg_fetch_array($Set->lista_sec_pedido($code, 1)); //Todos os egresos con ese pedido
$trasnportista = $rst_sec[mov_tranportista];
$fecha=date('Y-m-d');
//if (!empty($trasnportista)) {
    $rd_trs = "";
//} else {
//    $rd_trs = "";
//}
if (empty($rst_sec)) {
    $rst_sc = pg_fetch_array($Set->lista_sec_transaccion(1));
    $sec0 = explode('-', $rst_sc[mov_num_trans]);
    $sec = ($sec0[1] + 1);
    if ($sec >= 0 && $sec < 10) {
        $tx_trs = "000000000";
    } elseif ($sec >= 10 && $sec < 100) {
        $tx_trs = "00000000";
    } elseif ($sec >= 100 && $sec < 1000) {
        $tx_trs = "0000000";
    } elseif ($sec >= 1000 && $sec < 10000) {
        $tx_trs = "000000";
    } elseif ($sec >= 10000 && $sec < 100000) {
        $tx_trs = "00000";
    } elseif ($sec >= 100000 && $sec < 1000000) {
        $tx_trs = "0000";
    } elseif ($sec >= 1000000 && $sec < 10000000) {
        $tx_trs = "000";
    } elseif ($sec >= 10000000 && $sec < 100000000) {
        $tx_trs = "00";
    } elseif ($sec >= 100000000 && $sec < 1000000000) {
        $tx_trs = "0";
    } elseif ($sec >= 1000000000 && $sec < 10000000000) {
        $tx_trs = "";
    }
    $no_trs = '100-' . $tx_trs . $sec;
} else {
    $no_trs = $rst_sec[mov_num_trans];
}
$cns_mov = $Set->lista_movmp_numtrans($no_trs);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                $('#impresion').css('visibility', 'hidden');
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-fecha_emision"});
            })
            function save(mpid, ord, cnt, pres, pes, su, inv, n) {

                var su0 = document.getElementById(su);
                var cant = document.getElementById(cnt);
                su1 = (su0.value.replace(',', '') * 1.5); //rango de 50% mas en lo que se ingresa
                ct = (cant.value.replace(',', '') * 1);
//                alert(su1 +'-'+ ct);
                if (mov_transportista.value.length == 0) {
                    $("#mov_transportista").css({borderColor: "red"});
                    $("#mov_transportista").focus();
                } else if (parseFloat(su1) < parseFloat(ct)) {
                    alert('La cantidad no puede superar el rango del 50% mas de lo solicitado');
                    cant.focus();
                } else if (parseFloat(inv) < parseFloat(ct)) {
                    alert('No hay en existencia Favor revisar el Inventario' + inv + '-' + ct);
                    cant.focus();
                } else {
                    var peso = document.getElementById(pes);
                    var f = new Date();
//                    var fecha = f.getFullYear() + "-" + (f.getMonth() + 1) + "-" + f.getDate();

                    var pu1 = (peso.value).replace(',', '');
                    var cn1 = (cant.value).replace(',', '');

//                    var pu = (pu1) / (cn1);
                    var pu = pu1 * cn1;

                    var data = Array(1,
                            mpid,
                            ord,
                            nm_transc.value,
                            fecha_emision.value,
                            cn1,
                            pres,
                            pu,
                            '1',
                            pu1,
                            mov_transportista.value.toUpperCase(),
                            '',
                            mov_documento.value);
                    var fields = Array();
                    $('#encabezado').find(':input').each(function () {
                        var elemento = this;
                        des = elemento.id + "=" + elemento.value;
                        fields.push(des);
                    });
                    fields.push('codigo=' + $('#codigo' + n).html(),
                            'referencia=' + $('#referencia' + n).html(),
                            'presentacion=' + $('#presentacion' + n).html(),
                            'unidad=' + $('#unidad' + n).html(),
                            'cantidad=' + cn1,
                            '');

                    $.post("actions.php", {act: 222, 'data[]': data, 'fields[]': fields},
                    function (dt) {
                        if (dt == 0) {
                            window.location = "Form_i_egmp.php?code=" + ord;
                        } else {
                            alert(dt);
                        }
                    });
                }
            }
            function cerrar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_egrmp.php';
            }
            function cancelar()
            {
                doc = mov_documento.value;

                if (doc.length != 0)
                {
                    $.post("actions.php", {act: 24, doc: doc},
                    function (dt) {
                        if (dt == 0) {
                            cerrar();
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    cerrar();
                }


            }

            function finalizar()
            {
                cerrar();
                
            }

            function crea_codigo(fbc, tp)
            {
                $.post("actions.php", {act: 21, fbc: fbc, tp: tp},
                function (dt) {
                    mp_codigo.value = dt;
                });

            }
            function del(id, code)
            {
                if (confirm("Desea Eliminar Este Elemento?")) {

                    $.post("actions.php", {act: 23, id: id},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.location = "Form_i_reg_mp.php?code=" + code;
                        }

                    });
                }

            }
            function calculo(v, pu, peso) {
                ps = document.getElementById(peso);
//                ps.value = (v * pu);
            }


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function imprimir() {
                $('#tbl_form').hide();
                $('#impresion').css('visibility', 'visible');
                $('#guardar').hide();
                $('cancelar').hide();
                window.print();
                $('#impresion').hide();
                $('#tbl_form').show();
                $('cancelar').show();
                finalizar();
            }
        </script>
        <style>
            *{
                text-transform: uppercase;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr>
                    <th colspan="16" >
                        EGRESO DE MATERIA PRIMA
                        <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>                        
                        <input type="hidden" id="nm_transc" value='<?php echo $no_trs ?>'/>
                    </th>
                </tr>
            </thead>                    
            <tr>
            </tr>
            <tbody id="encabezado">
                <tr>
                    <td colspan="2">Documento:</td>
                    <td colspan="2"><input type="text" readonly value="<?php echo $rst_h[ped_orden] ?>" style="text-align:left" id="orden"/></td>
                </tr>
                <tr>
                    <td colspan="2">Fecha:</td>
                    <td colspan="2"><input type="text"  id="fecha_emision"  value="<?php echo $fecha ?>" style="text-align:left"/>
                        <img src="../img/calendar.png" id="im-fecha_emision" /></td>
                    <td colspan="2">Fecha De Pedido:</td>
                    <td colspan="2"><input type="text"  id="fecha_pedido" readonly value="<?php echo $rst_h[ped_fecha] ?>" style="text-align:left"/></td>
                    <td >Cliente:</td>
                    <td colspan="2"><input  type="text" size="17px" id="cliente" readonly value="<?php echo $rst_h[emp_descripcion] ?>" style="text-align:left"/></td>
                </tr>
                <tr>
                    <td colspan="2">Transaccion:</td>
                    <td colspan="2"><input   type="text" id="transaccion" readonly value="EGRESO A CONSUMO" style="text-align:left" /></td>
                    <td colspan="2">No. Orden:</td>
                    <td colspan="2"><input  type="text" readonly  id="mov_documento" value="<?php echo $guia ?>"  style="text-align:left"/></td>
                    <td >Solicitado por:</td>
                    <td colspan="2"><input type="text" <?php echo $rd_trs ?>  id="mov_transportista" value="<?php echo $trasnportista ?>" style="text-align:left"/></td>
                </tr>
            </tbody>

            <thead>
                <tr>
                    <!--<th></th>-->
                    <!--<th colspan="4">Materia Prima</th>-->
                    <th>Item</th>
                    <th>Codigo</th>                        
                    <th>Descripción</th>                        
                    <th>Presentacion</th>
                    <th>Unidad</th> 
                    <th >Solicitado</th>
                    <th >Inventario</th>
                    <th >Entregado</th>
                    <th >Saldo</th>
                    <th >Egreso</th>                        
                    <th >Acciones</th>
                </tr>
                <!--<tr>-->
<!--                    <th>Item</th>
                    <th>Codigo</th>                        
                    <th>Descripción</th>                        
                    <th>Presentacion</th>
                    <th>Unidad</th>                                                                        -->
                    <!--<th width="80px">Cant</th>-->                        
                    <!--<th width="80px">Peso</th>-->                        
                    <!--<th width="80px">Cant</th>-->                        
                    <!--<th width="80px">Peso</th>-->                        
                    <!--<th width="80px">Cant</th>-->                        
                    <!--<th width="80px">Peso</th>-->
                    <!--<th width="80px">Cant</th>-->                        
                    <!--<th width="80px">Peso</th>-->
                    <!--<th width="80px">Cant</th>-->                        
                    <!--<th width="80px">Peso</th>-->
                    <!--<th></th>-->
                <!--</tr>-->
            </thead>      
            <tbody class="tbl_frm_aux" >     
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;

                    $rst_inv = pg_fetch_array($Set->lista_inv_mp($rst[mp_id], 0));
                    $ingp = $rst_inv[peso];
                    $ingu = $rst_inv[unidad];
                    $rst_inv = pg_fetch_array($Set->lista_inv_mp($rst[mp_id], 1));
                    $egrp = $rst_inv[peso];
                    $egru = $rst_inv[unidad];

                    $inv_p = $ingp - $egrp;
                    $inv_u = $ingu - $egru;

                    $rst_egr = pg_fetch_array($Set->lista_inv_mp_doc($rst[mp_id], $rst_h[ped_orden], 1));


                    $salu = ($rst[ped_det_cant] - $rst_egr[unidad]);
//                    $salp = ($rst[ped_det_peso] - $rst_egr[peso]);
                    $salp = $inv_p / $inv_u;
                    $und = $rst[ped_det_cant] * 0.9;
                    if ($rst_egr[unidad] >= $und) {
                        $rdOnl = "readOnly";
                    } else {
                        $rdOnl = "";
                    }
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td id="codigo<?php echo $n ?>"><?php echo $rst[mp_codigo] ?></td>
                        <td id="referencia<?php echo $n ?>"><?php echo $rst[mp_referencia] ?></td>
                        <td id="presentacion<?php echo $n ?>"><?php echo $rst[mp_presentacion] ?></td>
                        <td id="unidad<?php echo $n ?>"><?php echo $rst[mp_unidad] ?></td>
                        <td align="right"><input type="text" size="5" readonly value="<?php echo str_replace(',', '', number_format($rst[ped_det_cant], 1)) ?>" /></td>
                        <!--<td align="right"><input type="text" size="5" readonly value="<?php echo str_replace(',', '', number_format($rst[ped_det_peso], 1)) ?>" /></td>-->
                        <td align="right"><input type="text" size="5" readonly value="<?php echo str_replace(',', '', number_format($inv_u, 1)) ?>" /></td>
                        <!--<td align="right"><input type="text" size="5" readonly value="<?php echo str_replace(',', '', number_format($inv_p, 1)) ?>" /></td>-->
                        <td align="right"><input type="text" size="5" readonly value="<?php echo str_replace(',', '', number_format($rst_egr[unidad], 1)) ?>" /></td>
                        <!--<td align="right"><input type="text" size="5" readonly value="<?php echo str_replace(',', '', number_format($rst_egr[peso], 1)) ?>" /></td>-->
                        <td align="right"><input type="text" size="5" readonly value="<?php echo str_replace(',', '', number_format($salu, 1)) ?>" id="<?php echo "su" . $n . $rst_h[ped_orden] ?>"   />
                            <input type="text" size="8" hidden readonly value="<?php echo str_replace(',', '', number_format($salp, 2)) ?>" id="<?php echo "ps" . $n . $rst_h[ped_orden] ?>"     /></td>
                            <!--<td align="right"><input type="text" size="5" readonly value="<?php echo str_replace(',', '', number_format($salp, 1)) ?>" /></td>-->
                        <td align="center"><input type="text" size="8" <?php echo $rdOnl ?> size="5" id="<?php echo "cn" . $n . $rst_h[ped_orden] ?>" value="<?php echo str_replace(',', '', number_format($salu, 1)) ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo(this.value,<?php echo $rst[ped_det_peso] / $rst[ped_det_cant] ?>, '<?php echo "ps" . $n . $rst_h[ped_orden] ?>')" /></td>
                        <!--<td align="center"></td>-->
                        <td>
                            <img src="../img/save.png" class="auxBtn" onclick="save(<?php echo $rst[mp_id] ?>, '<?php echo $rst_h[ped_orden] ?>', '<?php echo "cn" . $n . $rst_h[ped_orden] ?>', '<?php echo $rst[mp_presentacion] ?>', '<?php echo "ps" . $n . $rst_h[ped_orden] ?>', '<?php echo "su" . $n . $rst_h[ped_orden] ?>', '<?php echo $inv_u ?>',<?php echo $n ?>)">
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>      
            <tr>
                <td colspan="14">
                    <button id="cancel" onclick="imprimir()">Guardar</button>  
                    <button id="cancel" onclick="cerrar()">Cancelar</button>
                </td>
            </tr>
        </table>
        <table id="impresion" cellpadding="0" style="">
            <thead></thead>
            <tr>
                <td colspan="2">Documento:</td>
                <td colspan="2"><input type="text" readonly value="<?php echo $rst_h[ped_orden] ?>" style="text-align:left" id="orden"/></td>
                <td colspan="2">Fecha De Pedido:</td>
                <td colspan="4"><input type="text"  id="fecha_pedido" readonly value="<?php echo $rst_h[ped_fecha] ?>" style="text-align:left"/></td>
            </tr>
            <tr>
                <td colspan="2">Transaccion:</td>
                <td colspan="2"><input   type="text" id="transaccion" size="25" readonly value="EGRESO A CONSUMO" style="text-align:left" /></td>
                <td colspan="2">No. Orden:</td>
                <td colspan="4"><input  type="text" readonly  id="mov_documento" value="<?php echo $guia ?>"  style="text-align:left"/></td>
            </tr>
            <tr>
                <td colspan="2">Cliente:</td>
                <td colspan="2"><input  type="text" id="cliente" readonly value="<?php echo $rst_h[emp_descripcion] ?>" style="text-align:left"/></td>
                <td colspan="2">Transportista:</td>
                <td colspan="4"><input type="text" <?php echo $rd_trs ?>  readonly id="mov_transportista" value="<?php echo $trasnportista ?>" style="text-align:left"/></td>

            </tr>
            <thead>
                <tr>
                    <th></th>
                    <th colspan="4">Materia Prima</th>
                    <th colspan="2">Egreso</th>                        
                </tr>
                <tr>
                    <th>Item</th>
                    <th>Referencia</th>                        
                    <th >Descripción</th>                        
                    <th>Presentacion</th>
                    <th>Unidad</th>                                                                        
                    <th>Cant</th>                        
                    <th>Peso</th>                        

                </tr>
            </thead> 
            <tbody class="tbl_frm_aux" > 
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns_mov)) {
                    $n++;
                    ?>
                    <tr>
                        <td readonly><?php echo $n ?></td>
                        <td readonly id="codigo<?php echo $n ?>"><?php echo $rst[mp_codigo] ?></td>
                        <td readonly id="referencia<?php echo $n ?>"><?php echo $rst[mp_referencia] ?></td>
                        <td readonly id="presentacion<?php echo $n ?>"><?php echo $rst[mp_presentacion] ?></td>
                        <td readonly id="unidad<?php echo $n ?>"><?php echo $rst[mp_unidad] ?></td>
                        <td align="right"><input type="text" size="5" readonly value="<?php echo str_replace(',', '', number_format($rst[mov_cantidad], 1)) ?>" /></td>
                        <td align="right"><input type="text" size="5" readonly value="<?php echo str_replace(',', '', number_format($rst[mov_peso_total], 1)) ?>" /></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
    </body>
</html>