<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_reproceso.php';
$Set = new Clase_reg_reproceso();
$id = 0;
$rst['rrp_fecha'] = date('Y-m-d');
$fila = 0;
$emisor = 1;
$secuencial = $_GET[sec];
$x = 0;
//$cns_cli = $Set->lista_clientes();
$cns_trans = $Set->lista_combo_transacciones();
$rst_sec = pg_fetch_array($Set->lista_secuencial());
$sec = (substr($rst_sec[rrp_codigo], -7) + 1);
if ($sec >= 0 && $sec < 10) {
    $txt = '000000000';
} else if ($sec >= 10 && $sec < 100) {
    $txt = '00000000';
} else if ($sec >= 100 && $sec < 1000) {
    $txt = '0000000';
} else if ($sec >= 1000 && $sec < 10000) {
    $txt = '000000';
} else if ($sec >= 10000 && $sec < 100000) {
    $txt = '00000';
} else if ($sec >= 100000 && $sec < 1000000) {
    $txt = '0000';
} else if ($sec >= 1000000 && $sec < 10000000) {
    $txt = '000';
} else if ($sec >= 10000000 && $sec < 100000000) {
    $txt = '00';
} else if ($sec >= 100000000 && $sec < 1000000000) {
    $txt = '0';
} else if ($sec >= 1000000000 && $sec < 10000000000) {
    $txt = '';
}
$secuencial = $txt . $sec;
$rst_reg = pg_fetch_array($Set->lista_ultimo_registro());
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            var id =<?php echo $id ?>;
            var emi =<?php echo $emisor ?>;
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#dinamica').find("tbody tr:last");
                    a = tr.find("input").attr("lang");
                    i = parseInt(a);
                    if ($('#pro_descripcion' + i).val().length != 0 && ($('#mov_cantidad' + i).val() != '' && parseFloat($('#mov_cantidad' + i).val()) != 0)) {
                        clona_fila($('#dinamica'));
                    }
                    seleccion();
                });
                Calendar.setup({inputField: "mov_fecha_trans", ifFormat: "%Y-%m-%d", button: "im-mov_fecha_trans"});
                if (id == 0) {
                    $("#guardar").show();
                    $('#mov_cantidad1').val('0');
                }
                if (id == 0) {
                    load_transaccion(0);
                }
                seleccion();
                parent.document.getElementById('contenedor2').rows = "*,80%";
            });

            function save(id, x) {
                var data = Array();
                n = 0;
                i = $('.itm');
                while (n < i.length) {
                    n++;
                    if ($('#pro_id' + n).val() != null) {
                        pro = $('#pro_id' + n).val();
                        can = $('#mov_cantidad' + n).val();
                        tab = '0';
                        lote = $('#lote' + n).val();
                        bod_id = $('#bod_id').val();
                        estado = $('#pro_tbl' + n).val();
                        data.push(pro + '&' +
                                bod_id + '&' +
                                '1' + '&' + ///proveedor
                                '1&' + ///emisor
                                mov_documento.value + '&' +
                                mov_guia_transporte.value + '&' +
                                mov_fecha_trans.value + '&' +
                                can + '&' +
                                tab + '&' +
                                lote + '&' +
                                destino.value + '&' +
                                $('#mp_id').val() + '&' +
                                estado
                                );

                    }
                }
                var fields = Array();
                $('#frm_save').find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });

                $.ajax({
                    beforeSend: function () {
                        if (mov_documento.value.length == 0) {
                            $("#mov_documento").css({borderColor: "red"});
                            $("#mov_documento").focus();
                            return false;
                        } else if (mov_fecha_trans.value.length == 0) {
                            $("#mov_fecha_trans").css({borderColor: "red"});
                            $("#mov_fecha_trans").focus();
                            return false;
                        } else if (mp_id.value == '0') {
                            $("#mp_id").css({borderColor: "red"});
                            $("#mp_id").focus();
                            return false;
                        } else if (bod_id.value == 0) {
                            $("#bod_id").css({borderColor: "red"});
                            $("#bod_id").focus();
                            return false;
                        } else if (destino.value == 0) {
                            $("#destino").css({borderColor: "red"});
                            $("#destino").focus();
                            return false;
                        }
                        if (destino.value == '2' && mov_guia_transporte.value.length == 0) {
                            $("#mov_guia_transporte").css({borderColor: "red"});
                            $("#mov_guia_transporte").focus();
                            return false;
                        }
                        var tr = $('#dinamica').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        n = 0;
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#pro_descripcion' + n).val() != null) {
                                    if ($('#lote' + n).val() == 0) {
                                        $('#lote' + n).css({borderColor: "red"});
                                        $('#lote' + n).focus();
                                        return false;
                                    }
                                    if ($('#pro_codigo' + n).val() == 0) {
                                        $('#pro_codigo' + n).css({borderColor: "red"});
                                        $('#pro_codigo' + n).focus();
                                        return false;
                                    }
                                    if ($('#pro_descripcion' + n).val() == 0) {
                                        $('#pro_descripcion' + n).css({borderColor: "red"});
                                        $('#pro_descripcion' + n).focus();
                                        return false;
                                    }
                                    if ($('#mov_cantidad' + n).val() == 0) {
                                        $('#mov_cantidad' + n).css({borderColor: "red"});
                                        $('#mov_cantidad' + n).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        loading('visible');

                    },
                    type: 'POST',
                    url: 'actions_reg_reproceso.php',
                    data: {op: 12, 'data[]': data, 'fields[]': fields, id: id, x: 1}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            main = parent.document.getElementById('mainFrame');
                            main.src = '../Scripts/Lista_reg_reproceso.php?fecha1=' + mov_fecha_trans.value + '&fecha2=' + mov_fecha_trans.value;//Cambiar Form_productos
                            cancelar();
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function load_transaccion(obj) {
                $.post('actions_reg_reproceso.php', {id: obj, op: 6}, function (dt) {
                    $('#bod_id').val(dt);
                })
            }

            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] != 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] != 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    ;
                    return parts[1] + x;
                });
                $(table).find("tbody tr:last").after(tr);
                $('#pro_descripcion' + x).focus();
                $('#mov_cantidad' + x).val('0');
                total();

            }
            function elimina_fila(obj) {
                var tr = $('#dinamica').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                if (i > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
                total();
            }

            function cliente(obj) {
                $.post("actions_reg_reproceso.php", {op: 5, id: obj.value}, function (dt) {
                    dat = dt.split('&');
                    if (dat[0] == 0) {
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                    } else {
                        cli_id.value = dat[0];
                        cli_nombre.value = dat[1];
                    }
                })
            }
            function total() {
                var tr = $('#dinamica').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                sum = 0;
                while (n < i) {
                    n++;
                    if ($('#mov_cantidad' + n).val().length == 0) {
                        can = 0;
                    } else {
                        can = $('#mov_cantidad' + n).val()
                    }
                    sum = sum + parseFloat(can);
                }

                $('#total').html(sum.toFixed(2));
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function caracter(e, obj, x) {
                j = obj.lang;
                var ch0 = e.keyCode;
                var ch1 = e.which;
                if (ch0 == 9 && ch1 == 0 && x == 0) { //Tab (Sin lector de Codigo de Barras)
                    v = 0;
                    load_producto(obj);
                } else if (x == 1 && obj.value.length > 8) {//Desde lote
                    $('#mov_cantidad' + j).focus();
                    v = 1;
                    load_producto(obj);
                }
            }

            function seleccion() {
                if ($('#bod_id').val() == 0) {
                    var tr = $('#dinamica').find("tbody tr:last");
                    a = tr.find("input").attr("lang");
                    i = parseInt(a);
                    n = 0;
                    sum = 0;
                    while (n < i) {
                        n++;
                        $('#lote' + n).attr('disabled', true);
                    }
                } else {
                    var tr = $('#dinamica').find("tbody tr:last");
                    a = tr.find("input").attr("lang");
                    i = parseInt(a);
                    n = 0;
                    sum = 0;
                    while (n < i) {
                        n++;
                        $('#lote' + n).attr('disabled', false);
                    }
                }
//                $.post("actions_reg_reproceso.php", {op: 1, id: $('#bod_id').val()},
//                function (dt) {
//                    $('#productos').html(dt);
//                })
            }

//            function load_producto(j, v) {
//                if (v == 1) {
//                    vl = $('#pro_codigo' + j).val();
//                    lt = $('#lote' + j).val().trim();
//                } else {
//                    vl = $('#pro_codigo' + j).val();
//                    lt = '';
//                }
//                $('.itm').each(function () {
//                    pro = $('#pro_id' + this.value).val() + $('#lote' + this.value).val().trim();
//                    pro2 = $('#pro_codigo' + j).val() + $('#lote' + j).val().trim();
//                    $('#pro_codigo' + j).css({borderColor: ""});
//                    if (pro2 == pro) {
//                        alert('Producto ya ingresado');
//                        vl = '';
//                        $('#pro_codigo' + j).focus();
//                        return false;
//                    }
//                });
//
//                $.post("actions_reg_reproceso.php", {op: 64, id: vl, lt: lt},
//                function (dt) {
//                    dat = dt.split('&');
//                    if (dt.trim().length != 0) {
//                        $('#pro_id' + j).val(dat[0]);
//                        $('#pro_codigo' + j).val(dat[1]);
//                        $('#pro_descripcion' + j).val(dat[2]);
//                        $('#pro_uni' + j).val(dat[3]);
//                        $('#mov_cantidad' + j).val('');
//                        if (dat[4] == '') {
//                            $('#inventario' + j).val('0');
//                        } else {
//                            $('#inventario' + j).val(dat[4]);
//                        }
//                        $('#pro_lote' + j).focus();
//                    } else {
//                        $('#pro_codigo' + j).val('');
//                        $('#pro_descripcion' + j).val('');
//                        $('#pro_uni' + j).val(''); ///comentar para codigo ean
//                        $('#pro_id' + j).val('');
//                        $('#mov_cantidad' + j).val(0);
//                        $('#inventario' + j).val('0');
//                    }
//                });
//                total();
//            }

            function load_producto(obj) {
                j = obj.lang;
                vl = '';
//                v = obj.value.split('_');
//                if (v[1] != null) {
//                    vl = v[0];
//                    lt = v[1];
//                } else {
//                    vl = obj.value;
//                    lt = '';
//                }
                lt = obj.value.trim();
                $('.itm').each(function () {
//                    pro = $('#pro_id' + this.value).val() + '_' + $('#lote' + this.value).val().trim();
                    if (j != this.value) {
                        pro = $('#lote' + this.value).val().trim();
                        pro2 = $('#lote' + j).val();
                        $('#lote' + j).css({borderColor: ""});
                        if (pro2 == pro) {
                            alert('Producto ya ingresado');
                            vl = '';
                            lt = '';
                            $('#pro_lote' + j).focus();
                            return false;
                        }
                    }
                });

                $.post("actions_reg_reproceso.php", {op: 64, id: vl, lt: lt, bod: $('#bod_id').val()},
                function (dt) {
                    dat = dt.split('&');
                    if (dt.trim().length != 0) {
                        $('#pro_id' + j).val(dat[0]);
                        $('#pro_codigo' + j).val(dat[1]);
                        $('#pro_descripcion' + j).val(dat[2]);
                        $('#pro_uni' + j).val(dat[3]);
                        $('#mov_cantidad' + j).val('');
                        $('#pro_tbl' + j).val(dat[6]);
                        if (dat[4] == '') {
                            $('#inventario' + j).val('0');
                        } else {
                            $('#inventario' + j).val(dat[4]);
                        }
                        $('#lote' + j).val(dat[5]);
                        if (dat[4] == '') {
                            $('#mov_cantidad' + j).val('0');
                        } else {
                            $('#mov_cantidad' + j).val(dat[4]);
                        }
                        total();
                    } else {
                        alert('Producto no existe');
                        $('#pro_codigo' + j).val('');
                        $('#pro_descripcion' + j).val('');
                        $('#codaux' + j).val('');
                        $('#lote' + j).val(''); ///comentar para codigo ean
                        $('#pro_id' + j).val('');
                        $('#pro_uni' + j).val('');
                        $('#mov_cantidad' + j).val(0);
                        $('#pro_tbl' + j).val('');
                        $('#inventario' + j).val('0');
                    }
                });
                total();
            }

            function inventario(obj) {
                n = obj.lang;
                if ($('#bod_id').val().substring(0, 1) == 1) {
                    if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
                        alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                        total();
                    }
                }
            }

        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;                
            }

            #descripcion{
                width: 150px;
            }

            .add td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
            *{
                font-size: 10px;
            }

            select{
                width:150px;
            }

        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>

                    <tr>
                        <th colspan="7" ><?PHP echo 'REGISTRO REPROCESO' ?>
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>  
                        </th>
                    </tr>
                </thead>

                <tr>
                    <td>
                        <table>
                            <tbody id="encabezado">
                                <tr>
                                    <td>Documento No:</td>
                                    <td>
                                        <input type="text" size="20"  id="mov_documento" readonly value="<?php echo $secuencial ?>"  />
                                        <input type="hidden"   id="emisor" readonly value="<?php echo $emisor ?>"  />
                                    </td>
                                    <td>Fecha:</td>
                                    <td>
                                        <input type="text" size="20" name="fecha1" id="mov_fecha_trans" value="<?php echo $rst['rrp_fecha'] ?>"/>
                                        <img src="../img/calendar.png" id="im-mov_fecha_trans"/>
                                    </td>
                                    <td>Materia Prima:</td>
                                    <td>
                                        <select id="mp_id">
                                            <!--<option value='0'>seleccione</option>-->
                                            <?php
                                            $cns_mp = $Set->lista_mp();
                                            while ($rst_mp = pg_fetch_array($cns_mp)) {
                                                echo "<option value='$rst_mp[mp_id]'>$rst_mp[mp_codigo] / $rst_mp[mp_referencia]</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Bodega:</td>
                                    <td> <select id="bod_id" onchange="seleccion()">
                                            <!--<option value="0">Seleccione</option>-->
                                            <option value="1">Semielaborado</option>
                                            <option value="2">Terminado</option>
                                        </select>
                                    </td>
                                    <td>Destino:</td>
                                    <td> <select id="destino">
                                            <!--<option value="0">Seleccione</option>-->
                                            <option value="2">Extrusion</option>
                                            <option value="1">BODEGA MP</option>

                                        </select>
                                    </td>
                                    <td>Orden No:</td>
                                    <td><input type="text" size="25"  id="mov_guia_transporte" list='ordenes' value="<?php echo $rst_reg[orden] ?>"  /></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>

                        <table id="dinamica">
                            <tr id="head">
                            <thead id="tabla">
                            <th>Item</th>
                            <th>Lote</th>
                            <th>Codigo</th>
                            <th>Descripcion</th>
                            <th>Unidad</th>
                            <th hidden>Inventario</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                            </thead>

                </tr>
                <tr>
                    <td><input type="text" size="5" class="itm" id="item1" name="item1" readonly value="1" lang="1"/>
                        <input type ="hidden" size="20"  id="pro_id1"  value="" lang="1" />
                        <input type ="hidden" size="20"  id="pro_tbl1"  value="" lang="1" />
                    </td>
                    <td><input type ="text" size="15" id="lote1"  value="" lang="1" maxlength="11" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2"/></td>
                    <td><input type="text" size="20" readonly id="pro_codigo1"  value="" lang="1"   maxlength="13" /> </td>
                    <td><input type="text" size="60"  id="pro_descripcion1" value="" lang="1" readonly  style="font-weight: 100"/></td>
                    <td><input type ="text" size="10"  id="pro_uni1"  value="" lang="1" readonly/></td>
                    <td hidden><input type ="text" size="8"  id="inventario1"  value="" lang="1" readonly /></td>
                    <td><input type="text" size="10" readonly id="mov_cantidad1" name="mov_cantidad1" value="" lang="1" class="cnt" onblur="inventario(this), total()" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), total()"/></td>
                    <td onclick="elimina_fila(this)" ><img class="auxBtn" src="../img/b_delete.png" /></td>
                </tr>
                <tfoot>
                    <tr class="add">
                        <td>
                            <?PHP
                            if ($x != 1) {
                                ?> 
                                <button id="add_row">+</button>
                                <?PHP
                            }
                            ?>
                        </td>
                        <td colspan="4" align="right">Total:</td>
                        <td align="right" style="font-size:15px; " id="total"><?php echo number_format($total, 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </td>
    </tr>
</table>
</form>
<?PHP
if ($x != 1) {
    ?> 
    <button id="guardar" onclick="save(<?php echo $id ?>, 0)">Guardar</button>   
    <?PHP
}
?>
<button id="cancelar" >Cancelar</button>   
</body>
</html>

<datalist id="lista_proveedor">
    <?php
    while ($rst_cli = pg_fetch_array($cns_cli)) {
        echo "<option value='$rst_cli[cli_id]' >$rst_cli[nombres]</option>";
    }
    ?>
</datalist>
<!--<datalist id="productos">

</datalist>-->
<datalist id="ordenes">
    <?php
    $cns_ord = $Set->lista_ordenes();
    $n = 0;
    while ($rst_ord = pg_fetch_array($cns_ord)) {
        $n++;
        ?>
        <option value="<?php echo $rst_ord[ord_num_orden] ?>" label="<?php echo $rst_ord[ord_num_orden] ?>" />
        <?php
    }
    ?>
</datalist>
<datalist id="materia_prima">
    <?php
    $cns_mp = $Set->lista_mp();
    while ($rst_mp = pg_fetch_array($cns_mp)) {
        echo "<option value='$rst_mp[id]'>$rst_mp[mp_codigo] / $rst_mp[mp_referencia]</option>";
    }
    ?>
</datalist>
