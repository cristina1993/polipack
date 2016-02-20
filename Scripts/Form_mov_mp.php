<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$cod = $_GET[cod];
if (isset($_GET[cod])) {
    $cns = $Set->lista_movimientos_inv_codigo($cod);
    $rst = pg_fetch_array($Set->lista_movimientos_inv_codigo($cod));

    $rst[mov_documento];
    if ($rst[mov_tp_sec] == 0) {
        $chk = "checked";
    } else {
        $chk = "";
    }
    $valida = 1;
    $dis = "disabled";
    if ($rst[trs_operacion] == 0) {
        $read = "";
    } else {
        $read = "readonly";
    }
} else {
    $valida = 0;
    $rst[mov_fecha_trans] = date("Y-m-d");
    $secc = pg_fetch_array($Set->lista_secuencia_movimiento());
    $sc = ($secc[mov_documento] + 1);
    if ($sc >= 0 && $sc < 10) {
        $tx = "000000";
    } elseif ($sc >= 10 && $sc <= 100) {
        $tx = "00000";
    } elseif ($sc >= 100 && $sc <= 1000) {
        $tx = "0000";
    } elseif ($sc >= 1000 && $sc <= 10000) {
        $tx = "000";
    } elseif ($sc >= 10000 && $sc <= 100000) {
        $tx = "00";
    } elseif ($sc >= 100000 && $sc <= 1000000) {
        $tx = "0";
    } elseif ($sc >= 1000000 && $sc <= 10000000) {
        $tx = "";
    }
    $dis = "";
    $read = "readonly";
    $rst[mov_documento] = $tx . $sc;
    $rdly = "readonly";
    $chk = "";
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Movimientos de Inventarios</title>
    <head>
        <script>
            var valida = '<?php echo $valida ?>';
            $(function () {
                Calendar.setup({inputField: mov_fecha_trans, ifFormat: '%Y-%m-%d', button: im_mov_fecha_trans});
//                load_productos();
            });
            function save(id)
            {
//                if (mov_tp_sec.checked == true)
//                {
//                    sec = 0;
//                } else {
                sec = 1;
//                }

                if (mov_procedencia_destino.value == 0)
                {

                    if (mov_procedencia_destino0.value.length != 0)
                    {
                        proc_dest = mov_procedencia_destino0.value;
                    } else {
                        proc_dest = "INVENTARIO";
                    }

                } else {
                    proc_dest = mov_procedencia_destino.value;
                }
                t = trs_id.value.split('-');

                var data = Array(t[1],
                        mov_ubicacion.value,
                        mov_documento.value,
                        mov_prod_id.value,
                        0,
                        '000001',
                        mov_fecha_trans.value,
                        mov_cantidad.value,
                        proc_dest,
                        mov_unidad.value,
                        mov_v_unit.value,
                        sec,
                        valida);



                $.post("actions.php", {act: 16, 'data[]': data, id: id},
                function (dt) {
                    if (dt == 0)
                    {
                        window.location = "Form_mov_mp.php?cod=" + mov_documento.value;
                    } else {
                        alert(dt);
                    }
                });

            }
            var secuencia = '<?php echo $rst[mov_documento] ?>';
            function habilta()
            {
                if (mov_tp_sec.checked == true) {
                    mov_documento.readOnly = false;
                    mov_documento.focus();
                    mov_documento.select();
                } else {
                    mov_documento.readOnly = true;
                    mov_documento.value = secuencia;
                }
            }

            function calculo(op) {
                cn = mov_cantidad.value;
                vu = mov_v_unit.value;
                vt = v_tot.value;
                if (op == 1) {
                    v2 = cn * vu;
                    v_tot.value = v2;
                } else {
                    if (cn == 0) {
                        mov_v_unit.value = 0;
                    } else {
                        v1 = vt / cn;
                        mov_v_unit.value = v1;
                    }
                }

            }

            function hbl_destino(vl) {
                if (vl == 0)
                {
                    mov_procedencia_destino0.readOnly = false;
                } else {
                    mov_procedencia_destino0.readOnly = true;
                    mov_procedencia_destino0.value = null;
                }
            }
            function validar(id)
            {
                if (valida == 1)
                {
                    alert("Al realizar un registro ya ud ya no puede modificar el encabezado");
                    document.getElementById(id).value = document.getElementById(id).lang;

                } else {
                    document.getElementById(id).lang = document.getElementById(id).value;
                    t = trs_id.value.split('-');
                    if (t[0] == 1) {
                        $('#mov_v_unit').attr('readonly', true);
                        $('#v_tot').attr('readonly', true);
                    } else {
                        $('#mov_v_unit').attr('readonly', false);
                        $('#v_tot').attr('readonly', false);
                        $('#v_tot').val('0');
                        $('#mov_v_unit').val('0');
                    }
                }

            }

            function load_productos() {
                $.post("actions.php", {act: 80, id: codigo.value},
                function (dt) {
                    dat = dt.split('&');
                    $('#descripcion').html(dat[1]);
                    $('#mov_prod_id').val(dat[2]);
                    $('#codigo').val(dat[3]);
                    t = trs_id.value.split('-');
                    if (t[0] == 1) {
                        $('#mov_v_unit').val(dat[0]);
                    } else {
                        $('#mov_v_unit').val('0');

                    }
                });
            }

            function cancelar()
            {
                if (valida == 0)
                {
                    if (confirm("No ha realizado ningun registro \n Esta seguro de finalizar? \n Ningun Dato se Guardara") == true) {

                        mnu = window.parent.frames[0].document.getElementById('lock_menu');
                        mnu.style.visibility = "hidden";
                        grid = window.parent.frames[1].document.getElementById('grid');
                        grid.style.visibility = "hidden";
                        parent.document.getElementById('bottomFrame').src = '';
                        parent.document.getElementById('contenedor2').rows = "*,0%";
                    }
                } else {
                    mnu = window.parent.frames[0].document.getElementById('lock_menu');
                    mnu.style.visibility = "hidden";
                    grid = window.parent.frames[1].document.getElementById('grid');
                    grid.style.visibility = "hidden";
                    parent.document.getElementById('bottomFrame').src = '';
                    var f = new Date();
                    desde = f.getFullYear() + "-" + (f.getMonth() + 1) + "-" + f.getDate();
                    hasta = desde;
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_mov_mp.php';
                }
            }

            function eliminar() {
                id = mov_documento.value;
                $.post("actions.php", {act: 79, id: id},
                function (dt) {
                    if (dt == 0) {
                        mnu = window.parent.frames[0].document.getElementById('lock_menu');
                        mnu.style.visibility = "hidden";
                        grid = window.parent.frames[1].document.getElementById('grid');
                        grid.style.visibility = "hidden";
                        parent.document.getElementById('bottomFrame').src = '';
                        var f = new Date();
                        desde = f.getFullYear() + "-" + (f.getMonth() + 1) + "-" + f.getDate();
                        hasta = desde;
                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_mov_mp.php';
                    }
                });
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function auditoria() {
                if (valida == 0)
                {
                    cancelar();
                } else {
                    id = mov_documento.value;
                    var fields = Array();
                    $('#encabezado').find(':input').each(function () {
                        var elemento = this;
                        des = elemento.id + "=" + elemento.value;
                        fields.push(des);
                    });

                    $('#lista').find('td').each(function () {
                        var elemento = this;
                        des = elemento.id + "=" + $(elemento).html();
                        fields.push(des);
                    });
                    $.post("actions.php", {act: 83, id: id, 'fields[]': fields},
                    function (dt) {
                        if (dt == 0) {
                            cancelar();
                        } else {
                            alert(dt);
                        }
                    });
                }
            }

        </script>
    </head>

    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="8" >Movimientos de Inventarios</th></tr>
            </thead>
            <tbody id="encabezado">
                <tr>
                    <td>
                        <table>
                            <tr style="">
                                <!--<td colspan="8">-->
                                <td> Documento:<input type="text" size="12" id="mov_documento" readonly lang="<?php echo $rst[mov_documento] ?>"  value="<?php echo $rst[mov_documento] ?>" onchange="validar(this.id)"  /></td>

                                <td>Fecha:
                                    <input type="text" size="15" id="mov_fecha_trans" lang="<?php echo $rst[mov_fecha_trans] ?>" value="<?php echo $rst[mov_fecha_trans] ?>" onchange="validar(this.id)"  />
                                    <img src="../img/calendar.png" id="im_mov_fecha_trans" /></td>
                                <td>Destino/Procedencia:</td>
                                <td><select id="mov_procedencia_destino"  style="width:150px"lang="<?php echo $rst[mov_procedencia_destino] ?>" onchange="validar(this.id);
                                        hbl_destino(this.value)" >
                                        <!--<option value="0">Seleccione</option>-->                        
                                        <option value="1">Costura</option>
                                        <option value="2">Bodega 2</option>
                                        <option value="3">Bodega 3</option>
                                    </select></td>

<!--<td>Manual:</td>-->
<!--<td><input type="checkbox" <?php echo $chk ?> id="mov_tp_sec"  lang="<?php echo $rst[mov_tp_sec] ?>"  onclick="habilta()" onchange="validar(this.id)" />  </td>-->                  
                            </tr>
                            <tr>
                                <td>Tipo de Transaccion:</td>
                                <td><select id="trs_id" lang="<?php echo $rst[trs_id] ?>" style="width:200px; " onchange="validar(this.id)" <?php echo $dis ?>>
                                        <?PHP
                                        $cns_trs = $Set->lista_transacciones();
                                        while ($rst_trs = pg_fetch_array($cns_trs)) {
                                            echo "<option value='$rst_trs[trs_operacion]-$rst_trs[trs_id]' >$rst_trs[trs_codigo] - $rst_trs[trs_descripcion]</option>";
                                        }
                                        ?>
                                    </select></td>
                                <td> Ubicacion:</td>
                                <td><select id="mov_ubicacion" lang="<?php echo $rst[mov_ubicacion] ?>"  style="width:150px"onchange="validar(this.id)" >
                                        <option value="1">Costura</option>
                                        <option value="2">Bodega2</option>
                                        <option value="3">Bodega3</option>
                                    </select></td>
                            <script>
                                var pd = '<?php echo $rst[mov_procedencia_destino] ?>';
                                var tid = '<?php echo$rst[trs_operacion] . '-' . $rst[trs_id] ?>';
                                document.getElementById('mov_ubicacion').value =<?php echo $rst[mov_ubicacion] ?>;
                                document.getElementById('trs_id').value = tid;
                                document.getElementById('mov_procedencia_destino').value = pd;
                            </script>    

                </tr>
        </table>
    </td>
</tr>
</tbody>

<tr>
    <td>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Referencia</th>
                    <th width="300px" >Descripcion</th>
                    <th>Unidad</th>
                    <th>Cantidad</th>
                    <th>Costo/U</th>
                    <th>Costo/T</th>
                    <th>Acciones</th>
                </tr>
            </thead>            
            <tbody class="tbl_frm_aux" >                 
                <tr>
                    <td></td>
                    <td> <input type="text" list="productos" id="codigo" onchange="load_productos()" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" >
                        <input type="hidden" id="mov_prod_id">
                    </td>
                    <td id="descripcion"></td>
                    <td><select id="mov_unidad">
                            <option value="0">Unidad</option>
                            <option value="1">Metros</option>
                            <option value="2">Kilos</option>
                            <option value="3">Rollos</option>
                            <option value="4">Otro</option>
                        </select></td>
                    <td><input type="text" size="10" maxlength="5" id="mov_cantidad" style="text-align:right" onblur="calculo(1)"  /></td>
                    <td><input type="text" size="10" maxlength="5" id="mov_v_unit" style="text-align:right" onblur="calculo(1)" value="0" <?php echo $read ?>/></td>
                    <td><input type="text" size="10" maxlength="5" id="v_tot" style="text-align:right" onblur="calculo(2)" value="0" <?php echo $read ?>/></td>
                    <td>
                        <button id="save" style="float:right" onclick="save(0)">+</button>            
                    </td>
                </tr>
            </tbody>
            <?php
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                switch ($rst[mov_unidad]) {
                    case 0:$und = "Unidad";
                        break;
                    case 1:$und = "Metros";
                        break;
                    case 2:$und = "Kilos";
                        break;
                    case 3:$und = "Rollos";
                        break;
                    case 4:$und = "Otro";
                        break;
                }
                $rst_prod = pg_fetch_array($Set->list_one_data_by_id('erp_insumos', $rst[mov_prod_id]));
                ?>
                <tbody class="tbl_frm_aux" id="lista" >                 

                    <tr>
                        <td align='center' ><?php echo $n ?></td>
                        <td id="referencia<?php echo $n ?>"><?php echo $rst_prod[ins_a] ?></td>
                        <td id="descripcion<?php echo $n ?>"><?php echo $rst_prod[ins_b] ?></td>
                        <td id="cantidad<?php echo $n ?>" align='center' ><?php echo $rst[mov_cantidad] ?></td>
                        <td id="unidad<?php echo $n ?>" align='center' ><?php echo $und ?></td>
                        <td id="v_unitario<?php echo $n ?>" align='center' ><?php echo number_format($rst[mov_v_unit], 1) ?></td>
                        <td id="v_total<?php echo $n ?>" align='center' ><?php echo number_format($rst[mov_cantidad] * $rst[mov_v_unit], 1) ?></td>
                        <td></td>
                    </tr>
                </tbody>
                <?php
            }
            ?>
            <tr>
        </table>
    </td>
</tr>
<td colspan="2">
    <button id="cancel" style="float:left" onclick="auditoria()">Guardar</button>            
    <button id="cancel" style="float:left" onclick="eliminar()">Cancelar</button>            
</td>
<td colspan="7"></td>                            
</tr>
</table>
</body>
</html>
<datalist id="productos">
    <?php
    $cns_ins = $Set->lista_insumos();
    while ($rst_ins = pg_fetch_array($cns_ins)) {
        echo "<option value='$rst_ins[id]' > $rst_ins[ins_a] - $rst_ins[ins_b]</option>";
    }
    ?>
</datalist>