<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$y = $_GET[y];
$x = $_GET[x];

if (isset($_GET[orc_codigo]) || isset($_GET[id])) {
    if (isset($_GET[id])) {
        $id = $_GET[id];
    } else {
        $id = 0;
    }
    $rst_h = pg_fetch_array($Set->lista_orden_compra_code($_GET[orc_codigo]));
    if (empty($rst_h)) {
        $rst_h = pg_fetch_array($Set->lista_una_orden_compra($_GET[id]));
        $cn0 = "hidden";
        $cn1 = "";
    } else {
        $cn0 = "";
        $cn1 = "hidden";
    }
    $no_orden = $rst_h[orc_codigo];
    $cns_mp = $Set->lista_mp($rst_h[emp_id]);
    $cns = $Set->lista_det_orden_compra($rst_h[orc_id]);
    $tbl_hidden = "";
} else {
    $id = 0;
    $tbl_hidden = "hidden";
    $cn0 = "";
    $cn1 = "hidden";
    $rst_sec = pg_fetch_array($Set->lista_secuencial_orden(0));
    $rst_h[orc_fecha] = date("Y-m-d");
    $rst_h[orc_fecha_entrega] = date("Y-m-d");
    $rst_h[orc_estado] = 0;
    $sec = ( substr($rst_sec[orc_codigo], 2) + 1);
    if ($sec >= 0 && $sec < 10) {
        $tx_trs = "0000";
    } elseif ($sec >= 10 && $sec < 100) {
        $tx_trs = "000";
    } elseif ($sec >= 100 && $sec < 1000) {
        $tx_trs = "00";
    } elseif ($sec >= 1000 && $sec < 10000) {
        $tx_trs = "0";
    } elseif ($sec >= 10000 && $sec < 100000) {
        $tx_trs = "";
    }
    $no_orden = 'OC' . $tx_trs . $sec;
}
if ($y == 0) {
    $fct = "readOnly";
} else {
    $fct = "";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            var sts =<?php echo $rst_h[orc_estado] ?>;
            var id1 =<?php echo $id ?>;
            $(function () {
                Calendar.setup({inputField: "orc_fecha", ifFormat: "%Y-%m-%d", button: "im-orc_fecha"});
                Calendar.setup({inputField: "orc_fecha_entrega", ifFormat: "%Y-%m-%d", button: "im-orc_fecha_entrega"});
                $('#emp_id').val(<?php echo $rst_h[emp_id] ?>);
                $('#cli_id').val(<?php echo $rst_h[cli_id] ?>);
                $('#orc_condicion_pago').val('<?php echo $rst_h[orc_condicion_pago] ?>');

                if (sts > 2) {
                    $('input,select').each(function () {
                        this.disabled = true;
                    });
                    $('img').each(function () {
                        this.hidden = true;
                    });

                }

            });
            function save(id) {
                if (orc_factura.value == 0 && orc_factura.readOnly == false) {
                    $('#orc_factura').css({'border': 'solid 1px red'});
                    $('#orc_factura').focus();
                } else if (mp_id.value == 0) {
                    $('#mp_id').css({'border': 'solid 1px red'});
                    $('#mp_id').focus();
                } else if (orc_det_cant.value.length == 0) {
                    $('#orc_det_cant').css({'border': 'solid 1px red'});
                    $('#orc_det_cant').focus();
                } else if (orc_det_vu.value.length == 0) {
                    $('#orc_det_vu').css({'border': 'solid 1px red'});
                    $('#orc_det_vu').focus();
                } else {
                    var data = Array(
                            orc_id.value,
                            mp_id.value,
                            orc_det_cant.value,
                            orc_det_vu.value,
                            orc_det_vt.value);

                    $.post("actions.php", {act: 30, 'data[]': data, id: id, s: aux_y.value},
                    function (dt) {
                        if (dt == 0) {
                            window.location = "Form_i_orden_compra.php?orc_codigo=" + orc_codigo.value + "&y=" + aux_y.value + "&id=" + id1;
                        } else {
                            alert(dt);
                        }
                    });
                }
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
            }
            function cerrar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function cancelar() {
                if (orc_id.value.length > 0 && id1 == 0) {
                    $.post("actions.php", {act: 32, id: orc_id.value},
                    function (dt) {
                        if (dt == 0)
                        {
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
                d = orc_codigo.value;
                var fields = Array();
                $('#encabezado').find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });

                $('#lista').find('td').each(function () {
                    var elemento = this;
                    if (elemento.id != 'elim') {
                        des = elemento.id + "=" + $(elemento).html();
                    }
                    fields.push(des);
                });
                $('#totales1').find('input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                fields.push('');
                $.post("actions.php", {act: 82, id: d, 'fields[]': fields, data: id1},
                function (dt) {
                    if (dt == 0) {
                        cerrar();
                        window.history.go(0);
                    } else {
                        alert(dt);
                    }
                });

            }
            function save_head() {
                if (emp_id.value != '0' && cli_id.value != '0' && orc_fecha_entrega.value != '0') {
                    if (iva_aplica.checked == true) {
                        iva = 0;
                    } else {
                        iva = 1;
                    }

                    var data = Array(
                            cli_id.value,
                            orc_fecha.value,
                            orc_codigo.value,
                            emp_id.value,
                            orc_descuento.value,
                            orc_flete.value,
                            orc_fecha_entrega.value,
                            orc_factura.value,
                            orc_condicion_pago.value,
                            orc_direccion_entrega.value, '', '', iva)
                    $.post("actions.php", {act: 29, 'data[]': data, id: orc_codigo.value},
                    function (dt) {
                        if (dt == 0) {
                            window.location = "Form_i_orden_compra.php?orc_codigo=" + orc_codigo.value + "&y=" + aux_y.value + "&id=" + id1;
                        } else {
                            alert(dt);
                        }
                    });
                }
            }
            function del(id) {
                if (confirm("Desea Eliminar Este Elemento?")) {

                    $.post("actions.php", {act: 31, id: id},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.location = "Form_i_orden_compra.php?orc_codigo=" + orc_codigo.value + "&y=" + aux_y.value + "&id=" + id1;
                        }

                    });
                }
            }
            function datos(id) {
                $.post("actions.php", {act: 26, mp: id},
                function (dt) {
                    det = dt.split('&');
                    descripcion.innerHTML = det[0];
                    unidad.innerHTML = det[2];
                });
            }
            function valor_total() {
                orc_det_vt.value = (orc_det_cant.value * orc_det_vu.value).toFixed(1);
            }

            function imprimir() {
                var r = confirm("Luego de Imprmir ya no se podra modificar la Orden \n Desea Continuar??");
                if (r == true) {
                    window.print();
                    $.post("actions.php", {act: 50, sts: 3, id: orc_id.value, data: orc_codigo.value}, function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script>
        <style>
            .sbtls{
                border-left:solid 1px #ccc;
                border-right:solid 1px #ccc;        
            }
            .sbtls input{
                text-align:right; 
            }
        </style> 
        <link rel="stylesheet" href="../css/style_print.css" type="text/css" media="print" />        
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table id="tbl_form" >
            <thead>
                <tr>
                    <th colspan="8" >
                        ORDEN DE COMPRA DE MATERIA PRIMA
                        <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                    </th>
                </tr>
            </thead>
            <tbody id="encabezado">
                <tr>
                    <td >Fabrica:</td>
                    <td>    
                        <select id="emp_id" onchange="save_head()" style="width:200px"  >
                            <option value="0">Elija Una Fabrica</option>
                            <?php
                            $cns_emp = $Set->lista_fabricas();
                            while ($rst_emp = pg_fetch_array($cns_emp)) {
                                echo "<option $sel value='$rst_emp[emp_id]'>$rst_emp[emp_descripcion]</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>Fecha orden:</td>
                    <td><input type="text"  id="orc_fecha" size="12" value="<?php echo $rst_h[orc_fecha] ?>" onchange="save_head()"/>
                        <img id='im-orc_fecha' src='../img/calendar.png'  />
                    </td>
                    <td>Orden #:</td>
                    <td colspan="3">
                        <input type="text" id="orc_codigo" size="10"  readonly value="<?php echo $no_orden ?>" />
                        <input type="hidden" id="orc_id" value="<?php echo $rst_h[orc_id] ?>" />
                    </td>
                </tr>
                <tr>
                    <td >Proveedor:</td>
                    <td>
                        <select id="cli_id" onchange="save_head()" style="width:200px" >
                            <option value="0">Elija Una Opcion</option>
                            <?php
                            $cns_cli = $Set->lista_clientes_tipo(0);
                            while ($rst_cli = pg_fetch_array($cns_cli)) {
                                $n++;
                                echo "<option $sel value='$rst_cli[cli_id]'>$rst_cli[nombres]</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>Fecha Entrega:</td>
                    <td><input type="text"  id="orc_fecha_entrega" size="12" value="<?php echo $rst_h[orc_fecha_entrega] ?>" onchange="save_head()"/>
                        <img id='im-orc_fecha_entrega' src='../img/calendar.png'  />
                    </td>
                    <td>Factura #:</td>
                    <td colspan="3">
                        <input type="text" id="orc_factura" <?php echo $fct ?>  size="20"  value="<?php echo $rst_h[orc_factura] ?>" onchange="save_head()" />
                        <?php
                        if ($rst_h[orc_estado] > 1 && $rst_h[orc_estado] < 6) {
                            ?>
                            <img class="auxBtn" src="../img/print_iconop.png" width="20px" onclick="imprimir()" >
                            <?php
                        } else {
                            ?>
                            <img class="auxBtn" src="../img/no_print.png" width="20px"  >
                            <?php
                        }
                        ?>
                        <input type="hidden"  id="aux_y" value="<?php echo $y ?>" />
                    </td>
                </tr>
                <tr>
                    <td>Condicion_Pago:</td>
                    <td>
                        <select id="orc_condicion_pago" style="width:200px" onchange="save_head()" >
                            <option value="CONTADO">CONTADO</option>
                            <option value="A 30 DIAS">A 30 DIAS</option>
                            <option value="A 60 DIAS">A 60 DIAS</option>
                            <option value="A 90 DIAS">A 90 DIAS</option>
                            <option value="A 120 DIAS">A 120 DIAS</option>
                            <option value="A 150 DIAS">A 150 DIAS</option>
                        </select>
                    </td>
                    <td>Direccion de Entrega:</td>
                    <td colspan="5">
                        <input type="text" id="orc_direccion_entrega" style="width:100%" value="<?php echo $rst_h[orc_direccion_entrega] ?>" onchange="save_head()" />
                    </td>
                </tr>
            </tbody>
            <tbody class="tbl_frm_aux">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Descripcion</th>
                    <th>Referencia</th>            
                    <th>Unidad</th>
                    <th>Cantidad</th>
                    <th>Valor_U</th>
                    <th>Valot_T</th>
                    <th>Acciones</th>
                </tr>
                <tr class="options" <?php echo $tbl_hidden ?>>
                    <td></td>
                    <td>
                        <select id="mp_id" style="width:200px" onchange="datos(mp_id.value)">
                            <option value="0">Elija un Opcion</option> 
                            <?php
                            while ($rst_mp = pg_fetch_array($cns_mp)) {
                                echo "<option value='$rst_mp[mp_id]'  >$rst_mp[mp_referencia]</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td align="left" id="descripcion"></td>
                    <td align="center" id="unidad"></td>
                    <td><input type="text" id="orc_det_cant" size="10" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="valor_total()" /></td>
                    <td><input type="text" id="orc_det_vu" size="10" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="valor_total()" /></td>
                    <td id="total"><input type="text" readonly id="orc_det_vt" size="10" /></td>
                    <td><button onclick="save(0)">+</button></td>
                </tr>
            </thead>
        </tbody>
        <tbody class="tbl_frm_aux" id="lista">                 
            <?php
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $stb+=$rst[orc_det_vt];
                ?>
                <tr>
                    <td align="right" id="item<?php echo $n ?>"><?php echo $n ?></td>
                    <td id="codigo<?php echo $n ?>"><?php echo $rst[mp_codigo] ?></td>
                    <td id="referncia<?php echo $n ?>"><?php echo $rst[mp_referencia] ?></td>          
                    <td id="unidad<?php echo $n ?>"><?php echo $rst[mp_unidad] ?></td>
                    <td id="cantidad<?php echo $n ?>" align="right"><?php echo number_format($rst[orc_det_cant], 1) ?></td>
                    <td id="v_un<?php echo $n ?>"  align="right"><?php echo number_format($rst[orc_det_vu], 1) ?></td>
                    <td id="v_tot<?php echo $n ?>" align="right" class="sbtls" ><?php echo number_format($rst[orc_det_vt], 1) ?></td>
                    <td id="elim" align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png" onclick="del(<?php echo $rst[orc_det_id] ?>)">
                        <?php }
                        ?>
                    </td>
                </tr>

                <?php
            }
            $t_desc = $stb * $rst_h[orc_descuento] / 100;
            if ($rst_h[orc_iva] == 0) {
                $iva12 = ($stb - $t_desc) * 0.12;
                $chk_iva = 'checked';
            } else {
                $iva12 = 0;
                $chk_iva = '';
            }
            $total = ($stb - $t_desc + $iva12 + $rst_h[orc_flete]);
            ?>
        </tbody>

        <tbody class="tbl_frm_aux" id="totales1">
            <tr>
                <td colspan="6" align="right">Subtotal:</td>
                <td class="sbtls" align="right"><input id="subt" type="text" size="10" readonly  value="<?php echo number_format($stb, 2) ?>" /></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="6" align="right">Descuento:</td>
                <td class="sbtls" align="right">
                    <input type="text" size="5" maxlength="4" id="orc_descuento" onchange="save_head()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" value="<?php echo number_format($rst_h[orc_descuento], 1) ?>" />
                    %<input type="text" size="10" readonly  value="<?php echo number_format($t_desc, 2) ?>" />                    
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="6" align="right">
                    <font style="float:right  ">IVA 12%:</font>
                    <input type="checkbox" id="iva_aplica" <?php echo $chk_iva ?> style="float:right" onclick="save_head()"  />                        
                </td>
                <td class="sbtls" align="right"><input type="text" size="10" readonly  id="iva1" value="<?php echo number_format($iva12, 2) ?>" /></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="6" align="right">Flete:</td>
                <td class="sbtls" align="right"><input type="text" size="10" id='orc_flete' onchange="save_head()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" value="<?php echo number_format($rst_h[orc_flete], 2) ?>" /></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="6" align="right">Total:</td>
                <td class="sbtls" align="right" style="border-bottom:solid 1px #ccc;font-size:18px; "><input type="text" size="10" readonly id="tot"  value="<?php echo number_format($total, 2) ?>" /></td>
                <td></td>
            </tr>
            <?php
            if ($y == 0) {
                ?>
                <tr>
                    <td colspan="8">
                        <button id="save" style="float:left" onclick="finalizar()">Guardar</button>  
                        <button id="cancel0" style="float:left" <?php echo $cn0 ?> onclick="cancelar()">Cancelar</button>
                        <button id="cancel1" style="float:left" <?php echo $cn1 ?> onclick="cerrar()">Cancelar</button>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</body>
</html>