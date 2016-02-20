<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_notacredito_nuevo.php';
$Clase_nota_Credito_nuevo = new Clase_nota_Credito_nuevo();
$emisor;
if ($emisor >= 10) {
    $ems = '0' . $emisor . '-';
} else {
    $ems = '00' . $emisor . '-';
}

$det = 0;
$id = '0';
$rst_sec = pg_fetch_array($Clase_nota_Credito_nuevo->lista_secuencial_nota_credito($emisor));
if (empty($rst_sec)) {
    $sec = 1;
} else {
    $se = explode('-', $rst_sec[ncr_numero]);
    $sec = ($se[2] + 1);
}
if ($sec >= 0 && $sec < 10) {
    $tx = '00000000';
} else if ($sec >= 10 && $sec < 100) {
    $tx = '0000000';
} else if ($sec >= 100 && $sec < 1000) {
    $tx = '000000';
} else if ($sec >= 1000 && $sec < 10000) {
    $tx = '00000';
} else if ($sec >= 10000 && $sec < 100000) {
    $tx = '0000';
} else if ($sec >= 100000 && $sec < 1000000) {
    $tx = '000';
} else if ($sec >= 1000000 && $sec < 10000000) {
    $tx = '00';
} else if ($sec >= 10000000 && $sec < 100000000) {
    $tx = '0';
} else if ($sec >= 100000000 && $sec < 1000000000) {
    $tx = '';
}
$comprobante = $ems . '001-' . $tx . $sec;

$rst_det['fecha_emision'] = date('Y-m-d');
$rst_det['fecha_emision_comprobante'] = date('Y-m-d');
$descuento == '0';
$rst_ven = pg_fetch_array($Clase_nota_Credito_nuevo->lista_vendedor(strtoupper($rst_user[usu_person])));
$ven_id = $rst_ven[vnd_id];
$vendedor = strtoupper($rst_user[usu_person]);
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            var id = '<?php echo $id ?>';
            var usu =<?php echo $emisor ?>;
            var num = '<?php echo $num_not_credito ?>';
            var det = '<?php echo $det ?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#detalle').find("tbody tr:last");
                    var a = tr.find("input").attr("lang");
                    if ($('#descripcion' + a).val().length != 0) {
                        if (this.lang == 0) {
                            clona_fila($('#detalle'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha_emision_comprobante", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                if (det != 0) {
                    calculo();
                } else {
                    $('#motivo').val('12');
                    ocultar();
                    calculo();
                }
                posicion_aux_window();
            });


            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    if (parts[1] != 'item') {
                        this.value = '';
                    }

                    ;
                    this.lang = x;
                    return parts[1] + x;
                });

                tr.find("select").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    if (parts[1] != 'item') {
                        this.value = '';
                    }
                    ;
                    this.lang = x;
                    return parts[1] + x;
                });
                $('#detalle').find("tbody tr:last").after(tr);
                $('#cod_producto' + x).focus();
//                $('#cantidadf' + x).val('0');
//                $('#item' + x).attr('lang',x);
            }
//====================================================================================================================================================
            function save(id) {
                tipo_comprobante = 4;
                vendedor = '<?php echo $vendedor ?>';
                vnd_id = '<?php echo $ven_id ?>';
                var data = Array(
                        cli_id.value,
                        usu,
                        num_comprobante.value,
                        descripcion_motivo.value,
                        fecha_emision.value,
                        nombre.value,
                        identificacion.value,
                        email_cliente.value,
                        direccion_cliente.value,
                        '1', //denominacion
                        num_secuencial.value,
                        fecha_emision_comprobante.value,
                        subtotal12.value,
                        subtotal0.value,
                        subtotalex.value,
                        subtotalno.value,
                        total_descuento.value,
                        total_ice.value,
                        total_iva.value,
                        irbpnr.value,
                        telefono_cliente.value,
                        total_valor.value,
                        '0', //propina
                        motivo.value,
                        vnd_id,
                        '0',
                        '0', // subtotal,
                        vendedor
                        );
                var data1 = Array();
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                while (n < i) {
                    n++;
                    if ($('#cantidadf' + n).val() != null) {
                        if (motivo.value == '1') {
                            cod_producto = '';
                            pro_id = '0';
                            tab = '0';
                            lote = '';
                        } else {
                            cod_producto = $("#cod_producto" + n).val();
                            pro_id = $("#pro_id" + n).val();
                            lote = $("#lote" + n).val();
                            tab = $("#tab" + n).val();
                        }
                        cantidad = $("#cantidadf" + n).val().replace(',', '');
                        descripcion = $("#descripcion" + n).val().replace(',', '');
                        precio_unitario = $("#precio_unitario" + n).val().replace(',', '');
                        descuento = $("#descuento" + n).val().replace(',', '');
                        descuent = $("#descuent" + n).val().replace(',', '');
                        precio_total = $("#precio_total" + n).val().replace(',', '');
                        iva = $("#iva" + n).val().replace(',', '');
                        data1.push(
                                pro_id + '&' +
                                cod_producto + '&' +
                                '' + '&' +
                                cantidad + '&' +
                                descripcion + '&' +
                                precio_unitario + '&' +
                                descuento + '&' +
                                descuent + '&' +
                                precio_total + '&' +
                                iva + '&' +
                                '0&' + //ice
                                '0&' + //ibprn
                                '0&' + //ic_p 
                                '0&' + //ic_cod 
                                '0&' + //irbpnr_p
                                lote + '&' +
                                tab
                                );
                    }
                }
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
//                        Validaciones antes de enviar
                        doc = document.getElementsByClassName('itm');
                        var tr = $('#detalle').find("tbody tr:last");
                        a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        n = 0;
                        if (num_secuencial.value.length == 0) {
                            $("#num_secuencial").css({borderColor: "red"});
                            $("#num_secuencial").focus();
                            return false;
                        } else if (identificacion.value.length == 0) {
                            $("#identificacion").css({borderColor: "red"});
                            $("#identificacion").focus();
                            return false;
                        }
                        else if (nombre.value.length == 0) {
                            $("#nombre").css({borderColor: "red"});
                            $("#nombre").focus();
                            return false;
                        }
                        else if (direccion_cliente.value.length == 0) {
                            $("#direccion_cliente").css({borderColor: "red"});
                            $("#direccion_cliente").focus();
                            return false;
                        }
                        else if (telefono_cliente.value.length == 0) {
                            $("#telefono_cliente").css({borderColor: "red"});
                            $("#telefono_cliente").focus();
                            return false;
                        }
                        else if (email_cliente.value.length == 0) {
                            $("#email_cliente").css({borderColor: "red"});
                            $("#email_cliente").focus();
                            return false;
                        }
                        else if (descripcion_motivo.value.length == 0) {
                            $("#descripcion_motivo").css({borderColor: "red"});
                            $("#descripcion_motivo").focus();
                            return false;
                        }
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#cantidadf' + n).val() != null) {
                                    if (motivo.value != '1') {
                                        if ($('#cod_producto' + n).val().length == 0) {
                                            $('#cod_producto' + n).css({borderColor: "red"});
                                            $('#cod_producto' + n).focus();
                                            return false;
                                        }
                                    }
                                    if ($('#descripcion' + n).val().length == 0) {
                                        $('#descripcion' + n).css({borderColor: "red"});
                                        $('#descripcion' + n).focus();
                                        return false;
                                    }
                                    else if ($('#cantidadf' + n).val().length == 0) {
                                        $('#cantidadf' + n).css({borderColor: "red"});
                                        $('#cantidadf' + n).focus();
                                        return false;
                                    }
                                    if ($('#precio_unitario' + n).val().length == 0) {
                                        $('#precio_unitario' + n).css({borderColor: "red"});
                                        $('#precio_unitario' + n).focus();
                                        return false;
                                    }
                                    else if ($('#descuento' + n).val().length == 0) {
                                        $('#descuento' + n).css({borderColor: "red"});
                                        $('#descuento' + n).focus();
                                        return false;
                                    }
                                    else if ($('#iva' + n).val().length == 0) {
                                        $('#iva' + n).css({borderColor: "red"});
                                        $('#iva' + n).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        if (vnd_id == '') {
                            alert('Este Usuario no existe en la Tabla Vendedor \n Debe crear un Vendedor con este Usuario');
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_nota_credito_nuevo.php',
                    data: {op: 0, 'data[]': data, 'data1[]': data1, id: id, 'fields[]': fields, x: det}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            loading('hidden');
                            asientos(dat[1]);
                        } else if (dat[0] == 1) {
                            alert('Numero Secuencial de la Nota de Credito ya existe \n Debe hacer otra Nota de Credito con otro Secuencial');
                            cancelar();
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }
//====================================================================================================================================================

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_credito.php';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function elimina_fila(obj) {
                itm = $('.itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
            }

            function comparar(obj) {
                f = obj.lang;
                cf = $("#cantidadf1" + f).val();
                if ($("#cantidadf1" + f).val() * 1 < $("#cantidad" + f).val() * 1) {
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    $("#cantidadf" + f).val(cf);
                    $("#precio_total" + f).val('0.00');
                    calculo();
                    alert('La Cantidad es mayor a la factura');

                }
            }

            function restar_ing() {
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                w = parseInt(a);
                f = 0;
                t = 0;
                desc = 0;
                r = 0;
                inv = 0;
                while (f < w) {
                    f++;
                    canf = $("#cantidadf1" + f).val().replace(',', '');
                    can = $("#cantidadf" + f).val().replace(',', '');
                    uni = $("#precio_unitario" + f).val().replace(',', '');
                    d = $("#descuento" + f).val().replace(',', '');
                    inv = $("#inventario1" + f).val().replace(',', '');
                    r = parseFloat(canf) - parseFloat(can);
                    $("#cantidadf" + f).val(r.toFixed(4).replace(',', ''));
                    desc = (parseFloat(can) * parseFloat(uni)) * (parseFloat(d) / 100);
                    t = (parseFloat(can) * parseFloat(uni)) - desc;
                    $("#precio_total" + f).val(t.toFixed(4));
                    i = parseFloat(can) + parseFloat(inv);
                    $("#inventario" + f).val(i.toFixed(4));
                    if (can == 0 || can == '') {
                        cantidad = $("#cantidadf1" + f).val();
                        $("#cantidadf" + f).val(cantidad);
                        $("#precio_total" + f).val('0.00');
                        $("#inventario" + f).val('0.00');
                    }

                }
            }


            function restar(obj) {
                f = obj.lang;
                desc = (parseFloat($("#cantidadf" + f).val().replace(',', '')) * parseFloat($("#precio_unitario" + f).val().replace(',', ''))) * (parseFloat($("#descuento" + f).val().replace(',', '')) / 100);
                $("#descuent" + f).val(desc.toFixed(4).replace(',', ''));
                t = (parseFloat($("#cantidadf" + f).val().replace(',', '')) * parseFloat($("#precio_unitario" + f).val().replace(',', ''))) - desc;
                $("#precio_total" + f).val(t.toFixed(4)).replace(',', '');
                inv = $("#inventario1" + f).val().replace(',', '');
                i = parseFloat($("#cantidad" + f).val().replace(',', '')) + parseFloat(inv);
                $("#inventario" + f).val(i.toFixed(4)).replace(',', '');
                if ($("#cantidad" + f).val() == 0 || $("#cantidad" + f).val() == '') {
                    can = $("#cantidadf1" + f).val();
                    $("#cantidadf" + f).val(can);
                    $("#precio_total" + f).val('0.00');
                    $("#inventario" + f).val('0.00');
                }
            }

            function calculo() {
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                var t12 = 0;
                var t0 = 0;
                var tex = 0;
                var tno = 0;
                var tdsc = 0;
                var tiva = 0;
                var irbpnr = 0;
                var ice = 0;
                var gtot = 0;

                while (n < i) {
                    n++;

                    if ($('#item' + n).val() == null) {
                        ob = 0;
                        val = 0;
                        d = 0;
                        can = 0;
                        unit = 0;
                    } else {
                        ob = $('#iva' + n).val().toUpperCase();
                        val = $('#precio_total' + n).val().replace(',', '');
                        d = $('#descuento' + n).val();
                        can = $('#cantidadf' + n).val();
                        unit = $('#precio_unitario' + n).val();
                    }
                    tdsc = (tdsc * 1) + (can * unit * d / 100);
                    if (ob == '12') {
                        t12 = (t12 * 1 + val * 1);
                        tiva = ((t12 * 1) * 12 / 100);
                    }
                    if (ob == '0') {
                        t0 = (t0 * 1 + val * 1);
                    }
                    if (ob == 'EX') {
                        tex = (tex * 1 + val * 1);
                    }
                    if (ob == 'NO') {
                        tno = (tno * 1 + val * 1);
                    }

                }
                gtot = (t12 * 1 + t0 * 1 + tex * 1 + tno * 1 + tiva * 1);
                $('#subtotal12').val(t12.toFixed(4));
                $('#subtotal0').val(t0.toFixed(4));
                $('#subtotalex').val(tex.toFixed(4));
                $('#subtotalno').val(tno.toFixed(4));
                $('#total_ice').val(ice.toFixed(4));
                $('#irbpnr').val(irbpnr.toFixed(4));
                $('#total_descuento').val(tdsc.toFixed(4));
                $('#total_iva').val(tiva.toFixed(4));
                $('#total_valor').val(gtot.toFixed(4));
            }

            function ocultar() {
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                if ($('#motivo').val() == '1') {
                    while (n < i) {
                        n++;
                        it = $('#item' + n).val();
                        if (it != null) {
                            $('#cod_producto' + n).hide();
                            $('#codigo' + n).hide();
                            $('#lote' + n).hide();
                            $('#codigo' + n).val('');
                            $('#pro_id' + n).val('');
                            $('#lote' + n).val('');
                            $('#codigo').hide();
                            $('#lt').hide();
                            $('#lt' + n).hide();
                            $('.td1').hide();
                        }
                    }
                } else {
                    while (n < i) {
                        n++;
                        it = $('#item' + n).val();
                        if (it != null) {
                            $('#cod_producto' + n).show();
                            $('#codigo' + n).show();
                            $('#lote' + n).show();
                            $('#inventario' + n).show();
                            $('#codigo').show();
                            $('#lt').show();
                            $('#lt' + n).show();
                            $('.td1').show();
                            $('.td1').show();
                        }
                    }
                }
            }

            function load_cliente(obj) {
                $.post("actions_nota_credito_nuevo.php", {act: 1, id: obj.value, s: 0},
                function (dt) {
                    dat = dt.split('&&');
                    if (dat[0].length != 0) {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dat[0]);
                    } else {
                        alert('Cliente no existe \n Cree uno Nuevo??');
                        $('#nombre').focus();
                        $('#nombre').val('');
                        $('#direccion_cliente').val('');
                        $('#telefono_cliente').val('');
                        $('#email_cliente').val('');
                        $('#cli_id').val('0');
                    }
                });
            }

            function load_cliente2(obj) {
                $.post("actions_nota_credito_nuevo.php", {act: 1, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Cree uno Nuevo??');
                        $('#nombre').focus();
                        $('#nombre').val('');
                        $('#direccion_cliente').val('');
                        $('#telefono_cliente').val('');
                        $('#email_cliente').val('');
                        $('#cli_id').val('0');
                    } else {
                        dat = dt.split('&');
                        $('#identificacion').val(dat[0]);
                        $('#nombre').val(dat[1]);
                        $('#direccion_cliente').val(dat[2]);
                        $('#telefono_cliente').val(dat[3]);
                        $('#email_cliente').val(dat[4]);
                        $('#cli_id').val(dat[5]);
                    }
                    $('#con_clientes').hide();
                });

            }


            function caracter(e, obj, x) {

                j = obj.lang;
                var ch0 = e.keyCode;
                var ch1 = e.which;
                if (ch0 == 0 && ch1 == 46 && x == 0) { //Punto (Con lector de Codigo de Barras)

                    $('#lote' + j).focus();

                    $(obj).autocomplete({
                        minLength: 0,
                        source: ''
                    });


                } else if (ch0 == 9 && ch1 == 0 && x == 0) { //Tab (Sin lector de Codigo de Barras)
                    $('#lote' + j).focus();
                    v = 0;
                    load_producto(j, v);
                } else if (x == 1 && obj.value.length > 8) {//Desde lote
                    $('#cantidadf' + j).focus();
                    v = 1;
                    load_producto(j, v);
                }


            }

            function load_producto(j, v) {
                if (v == 1) {
                    vl = $('#cod_producto' + j).val();
                    lt = $('#lote' + j).val();
                } else {
                    vl = $('#cod_producto' + j).val();
                    lt = 0;
                }
                $.post("actions_nota_credito_nuevo.php", {act: 2, id: vl, lt: lt, s: usu},
                function (dt) {
                    dat = dt.split('&');
                    $('#cod_producto' + j).val(dat[0]);
                    $('#descripcion' + j).val(dat[1]);
                    $('#iva' + j).val(dat[4]);
                    $('#descuento' + j).val(0);
                    $('#codaux' + j).val(dat[7]);
                    $('#lote' + j).val(dat[8]); ///comentar para codigo ean
                    $('#pro_id' + j).val(dat[9]);
                    $('#tab' + j).val(dat[10]);

                    if (dat[3] == '') {
                        $('#precio_unitario' + j).val(0);
                        $('#iva' + j).val('12');
                    } else {
                        $('#precio_unitario' + j).val(dat[3]);
                    }

                    if (dat[5] == '') {
                        $('#descuento' + j).val(0);
                    } else {
                        $('#descuento' + j).val(dat[5]);
                    }
                    calculo('1');
                });

            }




            function posicion_aux_window() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#con_clientes");
                var objtx = $("#txt_salir");
                obj.css('top', (wndH - 400) / 2);
                obj.css('left', (wndW - 400) / 2);
                objtx.css('top', (wndH - 390) / 2);
                objtx.css('left', (wndW + 320) / 2);
            }

            function asientos(ncr_id) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: 1, id: ncr_id},
                    success: function (dt) {
                        if (dt == 0) {
                            cancelar();
                        } else {
                            alert(dt);
                        }
                    }
                });
            }

            function num_factura(obj) {
                nfac = obj.value;
                if (nfac.length != 17) {
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    alert('No cumple con la estructura ejem: 000-000-000000000');
                }
            }


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }


        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;                
            }
            .head{
                text-align: center;
                height:22px;
            }
            select{
                width: 230px;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">

                <thead>
                    <tr><th colspan="12" >NOTA DE CREDITO <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>   
                <tr><td><table>
                            <tr>
                                <td width="108">NOTA DE CREDITO NO:</td>                    
                                <td><input type="text" size="30"  id="num_comprobante" value="<?php echo $comprobante ?>" readonly/></td> 
                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="30"  id="fecha_emision"  readonly value="<?php echo $rst_det['fecha_emision'] ?>" /><img src="../img/calendar.png" id="im-campo1" /></td>
                            </tr>         
                            <tr>
                                <td width="108">FACTURA NO:</td>                    
                                <td><input type="text" size="30"  id="num_secuencial" maxlength="17" value="<?php echo $rst_det['num_secuencial'] ?>"  onblur="num_factura(this)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')"/></td> 
                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="30"  id="fecha_emision_comprobante" readonly value="<?php echo $rst_det['fecha_emision_comprobante'] ?>" /><img src="../img/calendar.png" id="im-campo2"></td>
                            </tr>         
                            <tr>
                                <td style="width:80px">CI/RUC :</td>
                                <td><input type="text" size="30"  id="identificacion" value="<?php echo $rst_det['identificacion'] ?>" onchange="load_cliente(this)" /></td>
                                <td>CLIENTE :</td>
                                <td><input type="text" size="30"  id="nombre" value="<?php echo $rst_det[nombre] ?>"  />
                                    <input type="hidden" size="10"  id="cli_id" value="<?php echo $rst_det[cli_id] ?>"  />
                                </td>
                            </tr>
                            <tr>
                                <td>DIRECCION :</td>
                                <td><input type="text" size="30"  id="direccion_cliente" value="<?php echo $rst_det['direccion_cliente'] ?>"  /></td>
                                <td>TELÉFONO:</td>
                                <td><input type="text" size="30"  id="telefono_cliente" value="<?php echo $rst_det['telefono_cliente'] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>CORREO:</td>
                                <td><input type="text" style="text-transform:lowercase "  size="30"  id="email_cliente"  value="<?php echo $rst_det['email_cliente'] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>TRANSACCION:</td>                
                                <td> <select id="motivo" onchange="ocultar()">
                                        <option value="12">DEVOLUCION DE VENTA</option>
                                        <option value="13">ANULACION DE VENTA</option>
                                        <option value="1">VARIOS</option>
                                    </select>
                                </td>
                                <td>MOTIVO:</td>
                                <td><input type="text" size="60"  id="descripcion_motivo" value="<?php echo $rst_det['descripcion_motivo'] ?>"  /></td>

                            </tr>
                        </table>
                    </td> 
                </tr>
                <tr>
                    <td>
                        <table id="detalle">
                            <tr id="head">
                            <thead id="tabla">
                            <th>Item</th>
                            <th id="codigo">Codigo</th>
                            <th>Descripcion</th>
                            <th id="lt">Lote</th>
                            <th>Cantidad</th>   
                            <th>Precio Unit</th>
                            <th>Descuento%</th>
                            <th>Descuento</th>
                            <th>Iva</th>
                            <th>Precio Total</th>
                            <th>Accion</th>
                            </thead>  
                            <!------------------------------------->

                            <tr>
                                <td><input type="text" size="8"  id="item1" class="itm"  lang="1" value="1" readonly  style="text-align:right" /></td>  
                                <td id="codigo1"><input type="text" size="15" id="cod_producto1"  value="" lang="1"  maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2"  /></td> 
                                <td><input type="text" size="40" id="descripcion1"  value="" lang="1"/>
                                    <input type="hidden" size="10" id="pro_id1"  value="" lang="1"/>
                                    <input type="hidden" size="10" id="tab1"  value="" lang="1"/>
                                </td>  
                                <td id="lt1"><input type="text" size="10" id="lote1"  value="" lang="1"/> </td>  
                                <td><input type="text" size="8"  id="cantidadf1"  value="" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), restar(this), calculo()" onblur="calculo()" style="text-align:right" lang="1"/>
                                <td><input type="text" size="8"  id="precio_unitario1"  value="" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), restar(this), calculo()" onblur="calculo()" lang="1"/></td>                  
                                <td><input type="text" size="8"  id="descuento1"  value=""  style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), calculo(), restar(this)" onblur="calculo()" lang="1"/></td>                  
                                <td><input type="text" size="8"  id="descuent1"  value="" lang="1" readonly  /></td>
                                <td><input type="text" size="8"  id="iva1"  value="" style="text-align:right" onkeyup="restar(this), calculo()" lang="1" onblur="calculo(), this.value = this.value.toUpperCase()"/></td>                  
                                <td><input type="text" size="12"  id="precio_total1"  value="" style="text-align:right" lang="1"/></td>                  
                                <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" /></td>
                            </tr>  
                            <tfoot>
                                <tr>
                                    <td><button id="add_row" onclick="frm_save.lang = 0" >+</button></td>
                                </tr>
                                <tr>
                                    <td style="width: 100px;" class="td1" colspan="2">
                                    <td colspan="7" align="right">Subtotal 12%:</td>
                                    <td class="sbtls" ><input style="text-align:right" type="text" size="12" id="subtotal12"  value="<?php echo number_format(0, 2) ?>" readonly/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="7" align="right">Subtotal 0%:</td>
                                    <td class="sbtls" ><input type="text" size="12"  id="subtotal0" value="<?php echo number_format(0, 2) ?>" style="text-align:right" readonly /></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="7" align="right">Subtotal No Objeto Iva:</td>
                                    <td class="sbtls" ><input type="text" size="12"   id="subtotalno" value="<?php echo number_format(0, 2) ?>" style="text-align:right" readonly/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="7" align="right">Subtotal Excento Iva:</td>
                                    <td class="sbtls" ><input type="text" size="12"  id="subtotalex" value="<?php echo number_format(0, 2) ?>" style="text-align:right" readonly/></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="7" align="right">Total Descuento:</td>
                                    <td class="sbtls" ><input type="text" size="12" id="total_descuento"   value="<?php echo number_format(0, 2) ?>" style="text-align:right" readonly/></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="7" align="right">ICE:</td>
                                    <td class="sbtls" ><input type="text" size="12" id="total_ice"  value="<?php echo number_format(0, 2) ?>" style="text-align:right" readonly/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="7" align="right">IVA 12%:</td>
                                    <td class="sbtls" ><input type="text" size="12" id="total_iva"  value="<?php echo number_format(0, 2) ?>" style="text-align:right" readonly/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="7" align="right">IRBPNR:</td>
                                    <td class="sbtls" ><input type="text" size="12" id="irbpnr"  value="<?php echo number_format(0, 2) ?>" style="text-align:right" readonly/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="7" align="right">Total:</td>
                                    <td class="sbtls"><input type="text" size="12" id="total_valor"  value="<?php echo number_format(0, 2) ?>"  style="text-align:right" readonly/></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <?PHP
                            if ($x != 1) {
                                ?> 
                                <button id="guardar" onclick="save('0'), frm_save.lang = 1">Guardar</button>   
                                <?PHP
                            }
                            ?>
                            <button id="cancelar" >Cancelar</button>   
                        </td>
                    </tr>
                </tfoot>
                <!------------------------------------->
            </table>
        </form>
    </body>
</html>    



<datalist id="productos">
    <?php
    $cns_pro = $Clase_nota_Credito_nuevo->lista_producto_total($emisor);
    $n = 0;
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        $n++;
        ?>
        <option value="<?php echo $rst_pro[tbl] . $rst_pro[id] ?>" label="<?php echo $rst_pro[lote] . ' ' . $rst_pro[codigo] . ' ' . $rst_pro[descripcion] ?>" />
        <?php
    }
    ?>
</datalist>
