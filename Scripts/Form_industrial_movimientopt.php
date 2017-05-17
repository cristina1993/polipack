<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_ingresopt.php'; // cambiar clsClase_productos
include_once '../Clases/clsClase_industrial_movimientopt.php'; // cambiar clsClase_productos
$Clase_industrial_movimientopt = new Clase_industrial_movimientopt();
$Clase_industrial_ingresopt = new Clase_industrial_ingresopt();
$emisor = $_GET[emisor];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_ingreso_industrial($id));
    $cns = $Clase_industrial_ingresopt->lista_ingreso_industrial_documento($rst['mov_documento']);
    $x = $_GET[x];
    $fila = pg_numrows($cns);
} else {
    $id = 0;
    $rst['mov_fecha_trans'] = date('Y-m-d');
    $fila = 0;
    $secuencial = $_GET[sec];
    $x = 0;
}
$cns_combo = $Clase_industrial_ingresopt->lista_combo_fabricas_industrial();
$cns_combo1 = $Clase_industrial_ingresopt->lista_combo_fabricas_noperti();
$cns_pro = $Clase_industrial_ingresopt->lista_clientes_tipo(0);
$cns_trans = $Clase_industrial_movimientopt->lista_combo_transacciones();
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
                $('#pro_codigo1').attr('disabled', true);
                $('#lote1').attr('disabled', true);
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
                });
                Calendar.setup({inputField: "mov_fecha_trans", ifFormat: "%Y-%m-%d", button: "im-mov_fecha_trans"});
                if (id == 0) {
                    $("#factura").hide();
                    $("#guardar").show();
                    $('#mov_cantidad1').val('');
                    $('#mov_cajas1').val('');
                    $('#mov_peso1').val('');
                    load_transaccion(0);
                }
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
                        peso = $('#mov_peso' + n).val();
                        can = $('#mov_cantidad' + n).val();
                        estado = $('#mov_estado' + n).val();
                        lote = $('#lote' + n).val();
                        trs_id = $('#trs_id').val().substring(1, 100);
                        data.push(pro + '&' +
                                trs_id + '&' +
                                cli_id.value + '&' +
                                emisor.value + '&' +
                                mov_documento.value + '&' +
                                mov_guia_transporte.value + '&' +
                                mov_fecha_trans.value + '&' +
                                peso + '&' +
                                can + '&' +
                                lote+ '&' +
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
                        } else if (trs_id.value == 0) {
                            $("#trs_id").css({borderColor: "red"});
                            $("#trs_id").focus();
                            return false;
                        } else if (cli_nombre.value.length == 0) {
                            $("#cli_nombre").css({borderColor: "red"});
                            $("#cli_nombre").focus();
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
                                    if ($('#mov_peso' + n).val().length == 0) {
                                        $('#mov_peso' + n).css({borderColor: "red"});
                                        $('#mov_peso' + n).focus();
                                        return false;
                                    }
                                    if ($('#mov_cajas' + n).val().length == 0) {
                                        $('#mov_cajas' + n).css({borderColor: "red"});
                                        $('#mov_cajas' + n).focus();
                                        return false;
                                    }
                                    if ($('#trs_id').val().substring(0, 1) == 1) {
                                        if (parseFloat($('#inventario' + n).val()) < parseFloat($('#mov_cantidad' + n).val())) {
                                            alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                                            $('#mov_cantidad' + n).val('');
                                            $('#mov_cantidad' + n).focus();
                                            $('#mov_cantidad' + n).css({borderColor: "red"});
                                            total();
                                            return false;
                                        }
                                    }

                                }
                            }
                        }
                        loading('visible');

                    },
                    type: 'POST',
                    url: 'actions_industrial_ingresopt.php',
                    data: {op: 12, 'data[]': data, 'fields[]': fields, id: id, x: 1}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            main = parent.document.getElementById('mainFrame');
                            main.src = '../Scripts/Lista_industrial_movimientopt.php?fecha1=' + mov_fecha_trans.value + '&fecha2=' + mov_fecha_trans.value;//Cambiar Form_productos
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
                $.post('actions_industrial_ingresopt.php', {id: obj, op: 6}, function (dt) {
                    $('#trs_id').val(dt);
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
                $('#mov_cajas' + x).val('');
                $('#mov_cantidad' + x).val('');
                $('#mov_peso' + x).val('');

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
            }

            function cliente(obj) {
                $.post("actions_industrial_ingresopt.php", {op: 5, id: obj.value}, function (dt) {
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
                sum1 = 0;
                sum2 = 0;
                sum3 = 0;
                while (n < i) {
                    n++;
                    if ($('#mov_cajas' + n).val().length == 0) {
                        cja = 0;
                    } else {
                        cja = $('#mov_cajas' + n).val()
                    }
                    if ($('#mov_cantidad' + n).val().length == 0) {
                        can = 0;
                    } else {
                        can = $('#mov_cantidad' + n).val()
                    }
                    if ($('#mov_peso' + n).val().length == 0) {
                        pes = 0;
                    } else {
                        pes = $('#mov_peso' + n).val()
                    }
                    sum1 = sum1 + parseFloat(cja);
                    sum2 = sum2 + parseFloat(can);
                    sum3 = sum3 + parseFloat(pes);
                }

                $('#total1').html(sum1);
                $('#total2').html(sum2);
                $('#total3').html(sum3.toFixed(2));
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

//            function caracter(e, obj, x) {
//                j = obj.lang;
//                var ch0 = e.keyCode;
//                var ch1 = e.which;
////                if (ch0 == 0 && ch1 == 46 && x == 0) { //Punto (Con lector de Codigo de Barras)
////
////                    $('#lote' + j).focus();
////                    $(obj).autocomplete({
////                        minLength: 0,
////                        source: ''
////                    });
////                } else 
//                if (ch0 == 9 && ch1 == 0 && x == 0) { //Tab (Sin lector de Codigo de Barras)
//                    $('#lote' + j).focus();
//                    v = 0;
//                    load_producto(j, v);
//                } else if (x == 1 && obj.value.length > 8) {//Desde lote
//                    $('#mov_cantidad' + j).focus();
//                    v = 1;
//                    load_producto(j, v);
//                }
//            }

            function seleccion() {
                if ($('#trs_id').val() == 0) {
                    var tr = $('#dinamica').find("tbody tr:last");
                    a = tr.find("input").attr("lang");
                    i = parseInt(a);
                    n = 0;
                    sum = 0;
                    while (n < i) {
                        n++;
                        $('#pro_codigo' + n).attr('disabled', true);
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
                        $('#pro_codigo' + n).attr('disabled', false);
                        $('#lote' + n).attr('disabled', false);
                    }
                }
            }

            function verificar_duplicados(j, o) {
                var v = 0;
                $('.itm').each(function () {
                    pro = $('#pro_id' + this.value).val() + $('#lote' + this.value).val().trim();
                    pro2 = $('#pro_id' + j).val() + $('#lote' + j).val().trim();
                    $('#pro_codigo' + j).css({borderColor: ""});
//                    alert(pro + '=' + pro2);
                    if (this.value != j) {
                        if (pro2 == pro) {
                            v = 1;
                        }
                    }
                });
                return v;
            }

            function load_producto(obj, v) {
                j = obj.lang;

//                if (v == 1) {
                vl = $('#pro_codigo' + j).val();
                lt = $('#lote' + j).val().trim();
//                } else {
//                    vl = $('#pro_codigo' + j).val();
//                    lt = '';
//                }

                $.post("actions_industrial_movimientopt.php", {op: 7, id: vl, lt: lt},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#pro_codigo' + j).val(dat[0]);
                        $('#pro_descripcion' + j).val(dat[1]);
                        $('#codaux' + j).val('');
                        $('#pro_id' + j).val(dat[3]);
                        $('#pro_uni' + j).val(dat[2]);
                        $('#mov_cantidad' + j).val('');
                        $('#pro_tbl' + j).val('0');
                        $('#uni_caja' + j).val(dat[8]);
                        $('#mov_estado' + j).val(dat[9]);
                        if (dat[11] == '') {
                            $('#inv_caja' + j).val('');
                            $('#inventario' + j).val('');
                            $('#inv_peso' + j).val('');
                        } else {
                            $('#inv_caja' + j).val(dat[7]);
                            $('#inventario' + j).val(dat[6]);
                            $('#inv_peso' + j).val(dat[5]);
                        }
                        if (v == 0) {
                            $('#lote' + j).focus();
                            $('#lotes').html(dat[4]);
                        } else {
                            $('#mov_cajas' + j).focus();
                        }
                    } else {
                        alert('Producto no existe');
                        $('#pro_codigo' + j).val('');
                        $('#pro_descripcion' + j).val('');
                        $('#codaux' + j).val('');
                        $('#lote' + j).val(''); ///comentar para codigo ean
                        $('#pro_id' + j).val('0');
                        $('#pro_uni' + j).val('');
                        $('#mov_cantidad' + j).val(0);
                        $('#mov_cajas' + j).val(0);
                        $('#mov_peso' + j).val(0);
                        $('#pro_tbl' + j).val('');
                        $('#inv_caja' + j).val('0');
                        $('#inventario' + j).val('0');
                        $('#inv_peso' + j).val('0');
                        $('#uni_caja' + j).val('0');
                        $('#mov_estado' + j).val('0');
                        $('#lotes').html('');
                    }
                    rd = verificar_duplicados(j, v);

                    if (rd == 1) {
                        alert('Producto ya ingresado');
                        vl = '';
                        lt = '';
                        $('#pro_codigo' + j).val('');
                        $('#pro_descripcion' + j).val('');
                        $('#pro_uni' + j).val(''); ///comentar para codigo ean
                        $('#pro_id' + j).val('0');
                        $('#mov_cantidad' + j).val(0);
                        $('#mov_cajas' + j).val(0);
                        $('#mov_peso' + j).val(0);
                        $('#inv_caja' + j).val('0');
                        $('#inventario' + j).val('0');
                        $('#inv_peso' + j).val('0');
                        $('#uni_caja' + j).val('0');
                        $('#mov_estado' + j).val('0');
                        $('#lote' + j).val('');
                        $('#lotes').html('');
                    }
                });
                total();
            }

            function inventario(obj, op) {
                n = obj.lang;
                if ($('#trs_id').val().substring(0, 1) == 1) {
                    if (op == 0) {
                        if (parseFloat($('#inv_caja' + n).val()) < parseFloat($(obj).val())) {
                            alert('NO SE PUEDE REGISTRAR CAJAS\n ES MAYOR QUE EL INVENTARIO');
                            $(obj).focus();
                            $('#mov_cajas' + n).val('');
                            $('#mov_cantidad' + n).val('');
                            $('#mov_peso' + n).val('');
                            $(obj).css({borderColor: "red"});
                            total();
                            return false;
                        }
                    } else if (op == 1) {
                        if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
                            alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                            $(obj).focus();
                            $('#mov_cajas' + n).val('');
                            $('#mov_cantidad' + n).val('');
                            $('#mov_peso' + n).val('');
                            $(obj).css({borderColor: "red"});
                            total();
                            return false;
                        }
                    }
                    if (parseFloat($('#inv_peso' + n).val()) < parseFloat($('#mov_peso' + n).val())) {
                        alert('NO SE PUEDE REGISTRAR EL PESO\n ES MAYOR QUE EL INVENTARIO');
                        $('#mov_cajas' + n).focus();
                        $('#mov_cajas' + n).val('');
                        $('#mov_cantidad' + n).val('');
                        $('#mov_peso' + n).val('');
                        $(obj).css({borderColor: "red"});
                        total();
                        return false;
                    }
                }
                calculos(obj, op);
            }
            function calculos(obj, op) {
                n = obj.lang;
                if ($(obj).val().length != 0) {
                    if (op == 0) {
                        ///calculo cantidad
                        if ($('#uni_caja' + n).val().length == 0) {
                            cnt = 0;
                            p = 0;
                        } else {
                            cnt = parseFloat($('#mov_cajas' + n).val()) * parseFloat($('#uni_caja' + n).val());
                            p = parseFloat($('#inv_peso' + n).val()) / parseFloat($('#inventario' + n).val()) * cnt;
                        }
                        $('#mov_cantidad' + n).val(cnt);
                        $('#mov_peso' + n).val(p.toFixed(2));
                    } else {
                        ///calculo cajas
                        if ($('#uni_caja' + n).val().length == 0) {
                            cj = 0;
                            p = 0;
                        } else {
                            cj = parseFloat($('#mov_cantidad' + n).val()) / parseFloat($('#uni_caja' + n).val());
                            dt = cj.toString().split('.');
                            if (parseFloat(dt[1]) > 0) {
                                cjr = parseFloat(dt[0]);
                            } else {
                                cjr = parseFloat(dt[0]);
                            }
                            p = parseFloat($('#inv_peso' + n).val()) / parseFloat($('#inventario' + n).val()) * parseFloat($('#mov_cantidad' + n).val());
                            $('#mov_cajas' + n).val(cjr);
                            $('#mov_peso' + n).val(p.toFixed(2));
                        }
                    }
                } else {
                    $('#mov_cajas' + n).val('0');
                    $('#mov_cantidad' + n).val('0');
                    $('#mov_peso' + n).val('0');
                }
                total();
            }

        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;                
            }

            #descripcion{
                width: 150px;
            }
            #emp_id{
                width: 140px;
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

        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>

                    <tr>
                        <th colspan="7" ><?PHP echo 'MOVIMIENTO DE PRODUCTO TERMINADO' ?>
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
                                    <td>Fecha de Ingreso:</td>
                                    <td>
                                        <input type="text" size="20" name="fecha1" id="mov_fecha_trans" value="<?php echo $rst['mov_fecha_trans'] ?>"/>
                                        <img src="../img/calendar.png" id="im-mov_fecha_trans"/>
                                    </td>
                                    <td>Orden No:</td>
                                    <td><input type="text" size="20"  id="mov_guia_transporte" value="<?php echo $rst['mov_guia_transporte'] ?>" list="ordenes" /></td>
                                </tr>
                                <tr>
                                    <td>Transaccion:</td>

                                    <td> <select id="trs_id" style="width:200px; " onchange="seleccion()">
                                            <option value="0">Seleccione</option>
                                            <?php
                                            while ($rst_tran = pg_fetch_array($cns_trans)) {
                                                echo "<option value=$rst_tran[trs_operacion]$rst_tran[trs_id]>$rst_tran[trs_descripcion]</option>";
                                            }
                                            ?>  
                                        </select>
                                    </td>
                                    <td>Proveedor:</td>
                                    <td><input type="text" size="30" id="cli_nombre" onblur="cliente(this)" value="<?php echo $rst['cli_raz_social'] ?>" list="lista_proveedor"/>
                                        <input type ="text" size="20"  id="cli_id"  value="" hidden=""/></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>

                        <table id="dinamica">
                            <tr>
                                <td>

                                    <table id="dinamica">
                                        <tr id="head">
                                        <thead id="tabla">
                                        <th colspan="6"></th>
                                        <th colspan="3">Inventario</th>
                                        <th colspan="3">Egreso</th>
                                        <th></th>
                                        </thead>
                            </tr>
                            <tr id="head">
                            <thead id="tabla">
                            <th>Item</th>
                            <th>Codigo</th>
                            <th>#Rollo</th>
                            <th>Descripcion</th>
                            <th>Unidad</th>
                            <th>UndxCaja</th>
                            <th>Cajas</th>
                            <th>Cantidad</th>
                            <th>Peso</th>
                            <th>Cajas</th>
                            <th>Cantidad</th>
                            <th>Peso</th>
                            <th>Acciones</th>
                            </thead>
                </tr>
                <?php
                if ($fila == "0") {
                    ?>

                    <tr>
                        <td><input type="text" size="5" class="itm" id="item1" name="item1" readonly value="1" lang="1"/>
                            <input type ="hidden" size="20"  id="pro_id1"  value="0" lang="1" />
                            <input type ="hidden" size="20"  id="pro_tbl1"  value="" lang="1" />
                        </td>
                        <td><input type="text" size="20" id="pro_codigo1"  value="" lang="1"   maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" onchange="load_producto(this, 0)"/> </td>
                        <td><input type ="text" size="15"  id="lote1"  value="" lang="1" maxlength="11" onchange="load_producto(this, 1)" list="lotes"/></td>
                        <td><input type="text" size="40"  id="pro_descripcion1" value="" lang="1" readonly  style="font-weight: 100"/></td>
                        <td><input type="text" size="10"  id="pro_uni1" value="" lang="1" readonly  style="font-weight: 100"/></td>
                        <td><input type="text" size="10" style="text-align:right"  id="uni_caja1" value="" lang="1" readonly  style="font-weight: 100"/></td>
                        <td><input type ="text" size="8" style="text-align:right"  id="inv_caja1"  value="" lang="1" readonly /></td>
                        <td><input type ="text" size="8" style="text-align:right"  id="inventario1"  value="" lang="1" readonly /></td>
                        <td><input type ="text" size="8" style="text-align:right"  id="inv_peso1"  value="" lang="1" readonly /></td>
                        <!--<td><input type ="text" size="15"  id="codaux1"  value="" lang="1" readonly/></td>-->
                        <td><input type="text" size="10" style="text-align:right"  id="mov_cajas1" name="mov_cajas1" value="" lang="1" class="cnt" onblur="inventario(this, 0)" onkeyup="this.value = this.value.replace(/[^0-9]/, ''), total()"/></td>
                        <td><input type="text" size="10" style="text-align:right"  id="mov_cantidad1" name="mov_cantidad1" value="" lang="1" class="cnt" onblur="inventario(this, 1)" onkeyup="this.value = this.value.replace(/[^0-9]/, ''), total()"/></td>
                        <td><input type="text" size="10" style="text-align:right"  id="mov_peso1" name="mov_peso1" value="" lang="1" class="cnt"  onchange="inventario(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                         <td hidden><input type="text" size="10" style="text-align:right"  id="mov_estado1" name="mov_estado1" value="" lang="1" class="cnt"  onchange="inventario(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                        <td onclick="elimina_fila(this)" ><img class="auxBtn" src="../img/b_delete.png" /></td>
                    </tr>
                    <?PHP
                } else {
                    $n = 0;
                    while ($rst1 = pg_fetch_array($cns)) {
                        $n++;
                        ?>
                        <tr>
                            <td>
                                <input type ="text" size="5"  id="item1"  readonly value="<?PHP echo $n ?>"/>
                                <input type="hidden" size="20" id="pro_id1" value="<?php echo $rst1['pro_id'] ?>" lang="1"/>
                            </td>
                            <td><input type ="text" size="30"  id="pro_descripcion1"  value="<?php echo $rst1['pro_descripcion'] ?>" style="font-weight: 100" lang="1" readonly/></td>

                            <td><input type ="text" size="10"  id="mov_cantidad1" style="text-align:right "  value="<?php echo $rst1['mov_cantidad'] ?>" lang="1" onblur="total()"/></td>
                        </tr>
                        <?PHP
                        $total+=$rst1['mov_cantidad'];
                    }
                }
                ?>

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
                        <td colspan="8" align="right">Total:</td>
                        <td align="right" style="font-size:15px; " id="total1"><?php echo number_format($total) ?></td>
                        <td align="right" style="font-size:15px; " id="total2"><?php echo number_format($total) ?></td>
                        <td align="right" style="font-size:15px; " id="total3"><?php echo number_format($total, 2) ?></td>
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
    <button id="factura" onclick="save(<?php echo $id ?>, 1)">Factura</button>
    <?PHP
}
?>
<button id="cancelar" >Cancelar</button>   
</body>
</html>
<datalist id="lotes">

</datalist>
<datalist id="lista_proveedor">
    <?php
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        echo "<option value='$rst_pro[cli_id]' >$rst_pro[nombres]</option>";
    }
    ?>
</datalist>
<datalist id="productos">
    <?php
    $cns_pro = $Clase_industrial_ingresopt->lista_productos_total($emisor);
    $n = 0;
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        $n++;
        ?>
        <option value="<?php echo $rst_pro[pro_id] ?>" label="<?php echo $rst_pro[pro_codigo] . ' ' . $rst_pro[pro_descripcion] ?>" />
        <?php
    }
    ?>
</datalist>

<script>
    var emp_id = '<?php echo $rst[emp_id] ?>';
    $('#emp_id').val(emp_id);
</script>

<datalist id="ordenes">
    <?php
    $cns_ord = $Clase_industrial_movimientopt->lista_ordenes();
    $n = 0;
    while ($rst_ord = pg_fetch_array($cns_ord)) {
        $n++;
        ?>
        <option value="<?php echo $rst_ord[ord_num_orden] ?>" label="<?php echo $rst_ord[ord_num_orden] ?>" />
        <?php
    }
    ?>
</datalist>