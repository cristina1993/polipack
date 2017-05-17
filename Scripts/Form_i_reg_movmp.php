<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$num_trs = $_GET[num_trs];
if (isset($_GET[num_trs])) {
    $rst_h = pg_fetch_array($Set->lista_mov_mp_codigo($num_trs));
    $no_trs = $_GET[num_trs];
    $trsid = $rst_h[trs_id];
    $cns = $Set->lista_mov_mp_codigo($num_trs);
    $rst_cli = pg_fetch_array($Set->lista_clientes_codigo($rst_h[mov_proveedor]));
} else {
    $rst_h[mov_fecha_trans] = date('Y-m-d');
    $no_trs = null;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                Calendar.setup({inputField: "mov_fecha_trans", ifFormat: "%Y-%m-%d", button: "im-mov_fecha_trans"});
            });
            function save(num_trs) {
                if (mov_documento.value.length == 0)
                {
                    alert('Guia de Recepcion es campo obligatorio');
                } else if (mp_id.value == 0) {
                    alert('Elija una materia prima');
                } else if (mov_cantidad.value.length == 0) {
                    alert('Cantidad es campo obligatorio');
                } else if (mov_peso_total.value.length == 0) {
                    alert('Peso Total es campo obligatorio');
                } else if (trs_id.value == 0) {
                    alert('Elija una Transaccion');
                } else if (mov_proveedor.value == 0) {
                    alert('Elija un Proveedor');
                }
                else {
                    var data = Array(
                            trs_id.value,
                            mp_id.value,
                            mov_documento.value,
                            mov_num_trans.value,
                            mov_fecha_trans.value,
                            mov_cantidad.value,
                            mp_presentacion.value,
                            mov_peso_total.value,
                            mov_proveedor.value,
                            mov_peso_unit.value,
                            '',
                            '',
                            mov_num_orden.value)

                    $.post("actions.php", {act: 225, 'data[]': data},
                    function (dt) {
                        if (dt == 0) {
                            window.location = "Form_i_reg_movmp.php?num_trs=" + num_trs;
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
            }
            function cancelar()
            {
                nm_trs = mov_num_trans.value;

                if (nm_trs.length != 0)
                {
                    $.post("actions.php", {act: 24, nm_trs: nm_trs},
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
                if ($('#referencia1').html() != null) {
                    id = mov_num_trans.value;
                    var fields = Array();
                    $('#encabezado').find(':input').each(function () {
                        var elemento = this;
                        des = elemento.id + "=" + elemento.value;
                        fields.push(des);
                    });

                    $('#lista').find('td').each(function () {
                        var elemento = this;
                        if (elemento.id != 'no') {
                            des = elemento.id + "=" + $(elemento).html();
                            fields.push(des);
                        }
                    });
                    $.post("actions.php", {act: 83, id: id, 'fields[]': fields, s: 3},
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
            function crea_codigo(fbc, tp)
            {
                $.post("actions.php", {act: 21, fbc: fbc, tp: tp},
                function (dt) {
                    mp_codigo.value = dt;
                });

            }
            function del(id, num_trs)
            {
                if (confirm("Desea Eliminar Este Elemento?")) {

                    $.post("actions.php", {act: 23, id: id},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.location = "Form_i_reg_movmp.php?num_trs=" + num_trs;
                        }

                    });
                }
            }
            function datos(id) {
                if (mov_num_trans.value.length == 0) {
                    alert('Elija transaccion');
                    mp_id.value = '';
                    codigo.value = '';
                    $('#inventario').val('0');
                } else {
                    $.post("actions.php", {act: 26, mp: id},
                    function (dt) {
                        det = dt.split('&');
                        mp_ref.innerHTML = det[0];
                        mov_unidad.innerHTML = det[2];
                        mp_presentacion.value = det[3];
                        codigo.value = det[7];
                        mp_id.value = det[8];
                        mov_cantidad.value=0;
                        if (det[4] != '') {
                            $('#inventario').val(det[4]);
                        } else {
                            $('#inventario').val('0');
                        }
                        if (mov_num_trans.value.substring(0, 1) == 1) {
                            if (det[6] != '') {
                                mov_peso_unit.value = parseFloat(det[6]).toFixed(4);
                            } else {
                                mov_peso_unit.value = 0;
                            }
                            $('#mov_peso_unit').attr('disabled', 'true');
                            $('#mov_peso_total').attr('disabled', 'true');
                        } else {
                            mov_peso_unit.value = 0;
                        }
                    });
                }
            }
            function num_trs(trs) {
                $.post("actions.php", {act: 28, id: trs},
                function (dt) {
                    mov_num_trans.value = dt;
                });
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function costo(x) {
                can = $('#mov_cantidad').val();
                uni = $('#mov_peso_unit').val() * 1;
                tot = $('#mov_peso_total').val();
                if (x == 1) {
                    t = parseFloat(can) * parseFloat(uni);
                    $('#mov_peso_total').val(t.toFixed(4));
                } else {
                    if (can != 0) {
                        t = parseFloat(tot) / parseFloat(can);
                    } else {
                        t = 0;
                    }
                    $('#mov_peso_unit').val(t.toFixed(2));
                }
                inventario();
            }
            function inventario() {
                inv = $('#inventario').val();
                can = $('#mov_cantidad').val();
                if (mov_num_trans.value.substring(0, 1) == 1) {
                    if (parseFloat(inv) < parseFloat(can)) {
                        alert('No se puede registrar la cantidad \nes mayor que el inventario');
                        $('#mov_cantidad').val('');
                    }
                }
            }

            function load_proveedor(id) {
                $.post("actions.php", {act: 81, id: id},
                function (dt) {
                    dat = dt.split('&');
                    proveedor.value = dat[0];
                    mov_proveedor.value = dat[1];
                });
            }

        </script>
        <style>
            .reg{
                background:#015b85;
                color:white;
                font-weight:bolder;
                text-align:center; 
            }
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
                <tr><th colspan="10" > 
                        REGISTRO DE MATERIA PRIMA
                        <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                    </th></tr>
            </thead>   
            <tbody id="encabezado">
                <tr>
                    <td>Documento No:</td>
                    <td><input type="text" size="25" id="mov_num_trans" readonly style="background:#ccc;" value="<?php echo $no_trs ?>" /></td>
                    <td>Fecha Ingreso:</td>
                    <td><input type="text"  id="mov_fecha_trans" size="10" value="<?php echo $rst_h[mov_fecha_trans] ?>"/>
                        <img id='im-mov_fecha_trans' src='../img/calendar.png'  />
                    </td>
                    <td>
                        Guia de Recepcion:
                    </td>
                    <td colspan="5">
                        <input type="text"  id="mov_documento" value="<?php echo $rst_h[mov_documento] ?>" />
                    </td>
                </tr>
                <tr>
                    <td>Transaccion</td>
                    <td>
                        <select id="trs_id" style="width:200px" onchange="num_trs(this.value)">
                            <option value="0">Elija Una Opcion</option>
                            <?php
                            $cns_trs = $Set->lista_transacciones();
                            while ($rst_trs = pg_fetch_array($cns_trs)) {
                                echo "<option value='$rst_trs[trs_id]'>$rst_trs[trs_descripcion]</option>";
                            }
                            ?>
                        </select>
                        <script>document.getElementById("trs_id").value =<?php echo $trsid ?></script>
                    </td>
                    <td>
                        Proveedor:
                    </td>
                    <td>
                        <input type="hidden" id="mov_proveedor" value ='<?php echo $rst_h[mov_proveedor] ?>'>
                        <input type="text" list="proveedores" id="proveedor" value="<?php echo $rst_cli[cli_raz_social] ?>" onchange="load_proveedor(this.value)" onfocus="this.style.width = '400px';" onblur="this.style.width = '200px';" >
                    </td>
                    <td>
                        Ord. Produccion No.:
                    </td>
                    <td>
                        <input type="text" id="mov_num_orden" list="ord_produccion" value="<?php echo $rst_h[mov_num_orden] ?>" >
                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Descripcion</th>          
                    <th>Codigo</th>
                    <th hidden>Presentacion</th>
                    <th>Unidad</th>
                    <th>Inventario</th>
                    <th>Cantidad</th>
                    <th>Costo U</th>
                    <th>Costo T</th>
                    <th>Accion</th>
                </tr>
                <tr>
                    <th></th>
                    <th>
                        <input type="text" list="productos" id="codigo" onchange="datos(this.value)" onfocus="this.style.width = '400px';" onblur="this.style.width = '170px';" >
                        <input type="hidden" id="mp_id" >
                    </th>
                    <th id="mp_ref" style="color:black;font-size:12px;  "></th>
                    <th hidden>
                        <input type="text" size="20" id="mp_presentacion"/>
                    </th>
                    <th id="mov_unidad" style="color:black;font-size:12px;text-transform:lowercase" ></th>
                    <th><input type="text" size="5" id="inventario" disabled /></th>
                    <th><input type="text" size="5" id="mov_cantidad"  onblur="costo(1)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></th>
                    <th><input type="text" size="5" id="mov_peso_unit"  onblur="costo(1)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></th>
                    <th><input type="text" size="5" id="mov_peso_total" onblur="costo(2)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></th>
                    <th>
                        <?php
                        if ($Prt->add == 0 || $Prt->edition == 0) {
                            ?>
                            <button id="" onclick="save(mov_num_trans.value)" >+</button>
                            <?php
                        }
                        ?>
                    </th>
                </tr>
            </thead>     
            <tbody class="tbl_frm_aux" id="lista">     
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td id="referencia<?php echo $n ?>"><?php echo $rst[mp_referencia] ?></td>
                        <td id="codigo<?php echo $n ?>"><?php echo $rst[mp_codigo] ?></td>          
                        <td hidden id="representacion<?php echo $n ?>"><?php echo $rst[mov_presentacion] ?></td>
                        <td id="unidad<?php echo $n ?>" style="text-transform:lowercase"><?php echo $rst[mp_unidad] ?></td>
                        <td> </td>
                        <td id="cantidad<?php echo $n ?>" align="right"><?php echo number_format($rst[mov_cantidad], 1) ?></td>
                        <td id="peso_unit<?php echo $n ?>" align="right"><?php echo number_format($rst[mov_peso_unit], 4) ?></td>
                        <td id="peso_tot<?php echo $n ?>" align="right"><?php echo number_format($rst[mov_peso_total], 4) ?></td>
                        <td id="no" align="center">
                            <?php
                            if ($Prt->delete == 0) {
                                ?>
                                <img src="../img/b_delete.png" onclick="del(<?php echo $rst[mov_id] ?>, '<?php echo $rst_h[mov_num_trans] ?>')">
                            <?php }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="10">
                        <button id="cancel" onclick="finalizar()">Guardar</button>  
                        <button id="cancel" onclick="cancelar()">Cancelar</button>
                    </td>
                </tr>
            </tbody>      
        </table>
    </body>
</html>
<datalist id="productos">
    <?php
    $cns_trn = $Set->lista_mp0();
    while ($rst_trn = pg_fetch_array($cns_trn)) {
        echo "<option value='$rst_trn[mp_id]'  >$rst_trn[mp_referencia]</option>";
    }
    ?>
</datalist>

<datalist id="proveedores">
    <?php
    $cns_cli = $Set->lista_clientes_tipo(0);
    while ($rst_cli = pg_fetch_array($cns_cli)) {
        echo "<option  value='$rst_cli[cli_id]'>$rst_cli[nombres]</option>";
    }
    ?>
</datalist>

<datalist id="ord_produccion">
    <?php
    $cns_ord_pro = $Set->lista_ordenes_produccion_mp(0);
    while ($rst_ord_pro = pg_fetch_array($cns_ord_pro)) {
        echo "<option  value='$rst_ord_pro[ped_num_orden]'>$rst_ord_pro[ped_num_orden]</option>";
    }
    ?>
</datalist>