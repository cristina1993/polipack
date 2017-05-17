<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nota.php';
$Clase_nota_Credito = new Clase_nota_Credito();
$emisor;
if ($emisor >= 10) {
    $ems = '0' . $emisor . '-';
} else {
    $ems = '00' . $emisor . '-';
}
$id = $_GET[id];
$x = $_GET[x];
if ($id != '') {
    $rst = pg_fetch_array($Clase_nota_Credito->lista_una_nota_credito_id($id));
    $det = 1;
    $comprobante = $rst[ncr_numero];
} else {
    $det = 0;
    $id = '0';
    $rst = pg_fetch_array($Clase_nota_Credito->lista_una_factura_id($x));
    $rst_sec = pg_fetch_array($Clase_nota_Credito->lista_secuencial_nota_credito($emisor));
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
    $rst[ncr_num_comp_modifica] = $rst[fac_numero];
    $cns = $Clase_nota_Credito->lista_detalle_factura($x);
    $rst[ncr_fecha_emision] = date('Y-m-d');
    $rst[ncr_fecha_emi_comp] = $rst[fac_fecha_emision];
    $rst[ncr_nombre] = $rst[fac_nombre];
    $rst[ncr_identificacion] = $rst[fac_identificacion];
    $rst[ncr_email] = $rst[fac_email];
    $rst[ncr_direccion] = $rst[fac_direccion];
    $rst[nrc_telefono] = $rst[fac_telefono];
    $rst[fac_id] = $rst[fac_id];
    $rst[cli_id] = $rst[cli_id];
}
$rst_ven = pg_fetch_array($Clase_nota_Credito->lista_vendedor(strtoupper($rst_user[usu_person])));
$ven_id = $rst_ven[vnd_id];
$vendedor = strtoupper($rst_user[usu_person]);
$descuento == '0';
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
            var comp = '<?php echo $comprobante ?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                if (det != 0) {
                    calculo();
                    ocultar();
                } else {
                    $('#motivo').val('12');
                    ocultar();
                    restar_ing();
                    calculo();
                }
            });
//====================================================================================================================================================
            function save() {
                tipo_comprobante = 4;
                cod_numerico = '<?php echo $rst[cod_numerico] ?>';
                vnd_id = '<?php echo $ven_id ?>';
                vendedor = '<?php echo $vendedor ?>';
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
                        fac_id.value,
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
                    if ($('#cantidad' + n).val() != null) {
                        if (motivo.value == '1') {
                            cod_producto = '';
                            pro_id = '0';
                            tab = '0';
                            lote = '';
                        } else {
                            cod_producto = $("#cod_producto" + n).val();
                            pro_id = $("#pro_id" + n).val();
                            tab = $("#tab" + n).val();
                            lote = $("#lote" + n).val();
                        }
                        cantidad = $("#cantidad" + n).val().replace(',', '');
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
                        var tr = $('#detalle').find("tbody tr:last");
                        a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        n = 0;
                        if ($('#descripcion_motivo').val().length == 0) {
                            $('#descripcion_motivo').css({borderColor: "red"});
                            $('#descripcion_motivo').focus();
                            return false;
                        }
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#cantidad' + n).val() != null) {
                                    if ($('#cantidad' + n).val() == 0) {
                                        $('#cantidad' + n).css({borderColor: "red"});
                                        $('#cantidad' + n).focus();
                                        return false;
                                    }
                                    if ($('#descuento' + n).val().length == 0) {
                                        $('#descuento' + n).css({borderColor: "red"});
                                        $('#descuento' + n).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        if (det != 0) {
                            if (num_comprobante.value != comp) {
                                alert('No se puede modificar Numero de Nota de Credito');
                                $('#num_comprobante').css({borderColor: "red"});
                                $('#num_comprobante').focus();
                                return false;
                            }
                        }

                        if (vnd_id == '') {
                            alert('Este Usuario no existe en la Tabla Vendedor \n Debe crear un Vendedor con este Usuario');
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_nota_credito.php',
                    data: {op: 0, 'data[]': data, 'data1[]': data1, id: id, 'fields[]': fields, x: det}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            asientos(dat[1]);
                        } else if (dat[0] == 1) {
                            alert('Numero Secuencial de la Nota de Credito ya existe \n Debe hacer otra Nota de Credito con otro Secuencial');
                            cancelar();
                        } else if (dat[0] == 2) {
                            alert('Una de las cuentas de la Nota de Credito esta inactiva');
                            loading('hidden');
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
                    calculo();
                } else {
                    alert('No puede eliminar todas las filas');
                }
            }
            function cliente(obj) {
                $.post("actions_pedidospt.php", {op: 3, id: obj.value}, function (dt) {
                    if (dt == 0) {
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                    }
                    else {
                        cli_id.value = dt;
                    }
                })
            }

            function comparar(obj) {
                if ($('#motivo').val() != '1') {
                    if (id == 0) {
                        f = obj.lang;
                        cf = $("#cantidadfa" + f).val();
                        if ($("#cantidadfa" + f).val() * 1 < $("#cantidad" + f).val() * 1) {
                            $(obj).val('');
                            $(obj).focus();
                            $(obj).css({borderColor: "red"});
                            $("#cantidadf" + f).val(cf);
                            $("#precio_total" + f).val('0.00');
                            calculo();
                            alert('La Cantidad es mayor a la factura');

                        }
                    }
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
                    can = $("#cantidadf" + f).val().replace(',', '');
                    uni = $("#precio_unitario" + f).val().replace(',', '');
                    d = $("#descuento" + f).val().replace(',', '');
                    desc = (parseFloat(can) * parseFloat(uni)) * (parseFloat(d) / 100);
                    t = (parseFloat(can) * parseFloat(uni)) - desc;
                    $("#precio_total" + f).val(t.toFixed(4));
                    if (can == 0 || can == '') {
                        cantidad = $("#cantidadfa" + f).val();
                        $("#cantidadf" + f).val(cantidad);
                        $("#precio_total" + f).val('0.00');
                        $("#inventario" + f).val('0.00');
                    }

                }
            }


            function restar(obj) {
                f = obj.lang;
                if ($("#cantidad" + f).val() != 0 || $("#cantidad" + f).val() != '') {
                    r = parseFloat($("#cantidadfa" + f).val().replace(',', '')) - parseFloat($("#cantidad" + f).val().replace(',', ''));
                    $("#cantidadf" + f).val(r.toFixed(4).replace(',', ''));
                    desc = (parseFloat($("#cantidad" + f).val().replace(',', '')) * parseFloat($("#precio_unitario" + f).val().replace(',', ''))) * (parseFloat($("#descuento" + f).val().replace(',', '')) / 100);
                    $("#descuent" + f).val(desc.toFixed(4).replace(',', ''));
                    t = (parseFloat($("#cantidad" + f).val().replace(',', '')) * parseFloat($("#precio_unitario" + f).val().replace(',', ''))) - desc;
                    $("#precio_total" + f).val(t.toFixed(4)).replace(',', '');
                    inv = $("#inventarioa" + f).val().replace(',', '');
                    i = parseFloat($("#cantidad" + f).val().replace(',', '')) + parseFloat(inv);
                    $("#inventario" + f).val(i.toFixed(4)).replace(',', '');
                } else if ($("#cantidad" + f).val() == 0 || $("#cantidad" + f).val() == '') {
                    can = $("#cantidadfa" + f).val();
                    $("#cantidadf" + f).val(can);
                    $("#precio_total" + f).val('0.00');
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
                        ob = $('#iva' + n).val();
                        val = parseFloat($('#precio_total' + n).val().replace(',', ''));
                        d = parseFloat($('#descuento' + n).val());
                        can = parseFloat($('#cantidad' + n).val());
                        unit = parseFloat($('#precio_unitario' + n).val());
                    }

                    tdsc = (tdsc * 1) + (can * unit * d / 100);

                    if (ob == '12') {
                        t12 = (t12 * 1 + val * 1);
                        tiva = ((t12 * 1) * 12 / 100);
                    }
                    if (ob == '14') {
                        t12 = (t12 * 1 + val * 1);
                        tiva = ((t12 * 1) * 14 / 100);
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
                        n++
                        it = $('#item' + n).val();
                        if (it != null) {
                            $('#cod_producto' + n).hide();
                            $('#codigo' + n).hide();
                            $('#codigo' + n).val('');
                            $('#inventario' + n).hide();
                            $('#tdinventario' + n).hide();
                            $('#inventario').hide();
                            $('#codigo').hide();
                            $('#precio_unitario' + n).attr('readonly', false);
                            $('#descripcion' + n).attr('readonly', false);
                            $('#iva' + n).attr('readonly', false);
                            $('.td1').hide();
                            $('#cantidadf').hide();
                            $('#cantidadf' + n).hide();
                            $('#tdcantidadf' + n).hide();
                            $('#tdlotef' + n).hide();
                            $('#lotef').hide();
                        }
                    }
                } else {
                    while (n < i) {
                        n++
                        it = $('#item' + n).val();
                        if (it != null) {
                            $('#cod_producto' + n).show();
                            $('#codigo' + n).show();
                            $('#inventario' + n).show();
                            $('#tdinventario' + n).show();
                            $('#inventario').show();
                            $('#codigo').show();
                            $('#precio_unitario' + n).attr('readonly', true);
                            $('#descripcion' + n).attr('readonly', true);
                            $('#iva' + n).attr('readonly', true);
                            $('.td1').show();
                            $('#cantidadf').show();
                            $('#cantidadf' + n).show();
                            $('#tdcantidadf' + n).show();
                            $('#tdlotef' + n).show();
                            $('#lotef').show();
                            $('.td1').show();
                        }
                    }
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function asientos(ncr_id) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: 1, id: ncr_id, x: det},
                    success: function (dt) {
                        if (dt == 0) {
                            cancelar();
                        } else {
                            alert(dt);
                        }
                    }
                });
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
                width: 225px;
            }
            *{
                font-size: 11px;
            }

        </style>
    </head>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">

                <thead>
                    <tr><th colspan="12" >NOTA DE CREDITO <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>   
                <tr><td><table>
                            <tr>
                                <td width="108">NOTA DE CREDITO NO:</td>                    
                                <td><input type="text" size="30"  id="num_comprobante" readonly value="<?php echo $comprobante ?>"  /></td> 
                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="20"  id="fecha_emision"  readonly value="<?php echo $rst[ncr_fecha_emision] ?>" /><img src="../img/calendar.png" id="im-campo1" readonly/></td>
                            </tr>         
                            <tr>
                                <td width="108">FACTURA NO:</td>                    
                                <td><input type="text" size="30"  id="num_secuencial" readonly value="<?php echo $rst[ncr_num_comp_modifica] ?>"  />
                                    <input type="hidden" size="10"  id="fac_id" readonly value="<?php echo $rst[fac_id] ?>"/>
                                </td> 
                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="20"  id="fecha_emision_comprobante" readonly value="<?php echo $rst[ncr_fecha_emi_comp] ?>" /></td>
                            </tr>         
                            <tr>
                                <td>CLIENTE :</td>
                                <td><input type="text" size="30"  id="nombre" readonly value="<?php echo $rst[ncr_nombre] ?>"/>
                                    <input type="hidden" size="10"  id="cli_id" readonly value="<?php echo $rst[cli_id] ?>"/>
                                    <input type="hidden" size="10"  id="email_cliente" readonly value="<?php echo $rst[ncr_email] ?>"/>
                                </td>
                                <td>CI/RUC :</td>
                                <td><input type="text" size="20"  id="identificacion" readonly value="<?php echo $rst[ncr_identificacion] ?>" /></td>
                            </tr>
                            <tr>
                                <td>DIRECCION :</td>
                                <td><input type="text" size="30"  id="direccion_cliente" readonly value="<?php echo $rst[ncr_direccion] ?>"  /></td>
                                <td>TELÉFONO:</td>
                                <td><input type="text" size="20"  id="telefono_cliente" readonly value="<?php echo $rst[nrc_telefono] ?>"  /></td>
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
                                <td><input type="text" size="60"  id="descripcion_motivo" value="<?php echo $rst[ncr_motivo] ?>"  /></td>

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
                            <th colspan="2">Descripcion</th>
                            <th id="lotef">Lote</th>
                            <th id="cantidadf">Cantidad</th>
                            <th id="inventario">Inventario</th> 
                            <th>Cantidad</th>                 
                            <th>Precio Unit</th>
                            <th>Descuento %</th>
                            <th>Descuento</th>
                            <th>Iva</th>
                            <th>Precio Total</th>
                            <th>Accion</th>
                            </thead>  
                            <!------------------------------------->
                            <?PHP
                            if ($det == '0') {
                                $n = 0;
                                while ($rst_det_pro = pg_fetch_array($cns)) {
                                    $n++;
                                    $inv = 0;
                                    $pro_id = $rst_det_pro[pro_id];
                                    $tab = $rst_det_pro[dfc_tab];
                                    ;
                                    $rst_inv = pg_fetch_array($Clase_nota_Credito->total_ingreso_egreso_fac($pro_id, $emisor, $tab));
                                    $inv = $rst_inv[ingreso] - $rst_inv[egreso];
//                                    $rst_c = pg_fetch_array($Clase_nota_Credito->sum_total_nc($id, $rst_det_pro['cod_producto'], $rst_det_pro['lote']));
                                    ?>
                                    <tr>
                                        <td><input type="text" size="7"  id="item<?php echo $n ?>" class="itm"  value="<?php echo $n ?>" readonly  style="text-align:right" lang="<?php echo $n ?>"/></td>                  
                                        <td id="codigo<?php echo $n ?>"><input  type="text" size="10" id="cod_producto<?php echo $n ?>"  value="<?php echo $rst_det_pro[dfc_codigo] ?>" readonly/></td>                  
                                        <td colspan="2"><input type="text" size="40"  id="descripcion<?php echo $n ?>"  value="<?php echo $rst_det_pro[dfc_descripcion] ?>" readonly/>
                                            <input  type="hidden" size="10" id="pro_id<?php echo $n ?>"  value="<?php echo $pro_id ?>" /> 
                                            <input  type="hidden" size="10" id="tab<?php echo $n ?>"  value="<?php echo $tab ?>" /></td> 
                                        <td id="tdlotef<?php echo $n ?>"><input type="text" size="10" id="lote<?php echo $n ?>"  value="<?php echo $rst_det_pro[dfc_lote] ?>" lang="1" readonly/> </td>  
                                        <td id="tdcantidadf<?php echo $n ?>"><input type="text" size="8"  id="cantidadf<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det_pro[dfc_cantidad], 4)) ?>" lang="<?php echo $n ?>" readonly  style="text-align:right"/> 
                                            <input type="text" hidden size="8"  id="cantidadfa<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det_pro[dfc_cantidad], 4)) ?>" lang="<?php echo $n ?>"/></td> 
                                        <td id="tdinventario<?php echo $n ?>"><input type="text" size="8" id="inventario<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($inv, 1)) ?>" readonly  style="text-align:right"/> 
                                            <input type="text" hidden size="8"  id="inventarioa<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($inv, 1)) ?>" /></td>  
                                        <td><input type="text" size="8"  id="cantidad<?php echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst_det_pro[dfc_cantidad], 2)) ?>" lang="<?php echo $n ?>" onkeyup="this.value = this.value.replace(/[^0-9]/, ''), restar(this), calculo()" onblur="calculo(), comparar(this)" style="text-align:right"/> </td>                  
                                        <td><input type="text" size="8"  id="precio_unitario<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det_pro[dfc_precio_unit], 4)) ?>" lang="<?php echo $n ?>" readonly  style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), restar(this), calculo()" onblur="calculo()"/></td>                  
                                        <td><input type="text" size="8"  id="descuento<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det_pro[dfc_porcentaje_descuento], 4)) ?>"  style="text-align:right" lang="<?php echo $n ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), calculo(), restar(this)" onblur="calculo()" readonly/></td>                  
                                        <td>
                                            <input type="text" size="8" id="descuent<?php echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst_det_pro[dfc_val_descuento], 4)) ?>" style="text-aling:right" lang="<?php echo $n ?>" readonly />           
                                        </td>
                                        <td><input type="text" size="8"  id="iva<?php echo $n ?>"  value="<?php echo $rst_det_pro[dfc_iva] ?>" lang="<?php echo $n ?>" onkeyup="restar(this), calculo()" readonly  style="text-align:right"/></td>                  
                                        <td><input type="text" size="8"  id="precio_total<?php echo $n ?>"  value="0.00 " readonly  style="text-align:right"/></td>                  
                                        <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" /></td>
                                    </tr>  
                                    <?PHP
                                }
                            } else if ($det == '1') {
                                $n = 0;
                                $cns2 = $Clase_nota_Credito->lista_detalle_nota_credito($id);
                                while ($rst2 = pg_fetch_array($cns2)) {
                                    $n++;
                                    $inv = 0;
                                    $pro_id = $rst2[pro_id];
                                    $tab = $rst2[dnc_tab];
                                    $rst_inv = pg_fetch_array($Clase_nota_Credito->total_ingreso_egreso_fac($pro_id, $emisor, $tab));
                                    $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                                    $rst_c = pg_fetch_array($Clase_nota_Credito->sum_total_nc($rst[fac_id], $pro_id, $tab));
                                    ?>
                                    <tr>
                                        <td><input type="text" size="7"  id="item<?php echo $n ?>" class="itm"  value="<?php echo $n ?>" readonly  style="text-align:right" lang="<?php echo $n ?>"/></td>                  
                                        <td id="codigo<?php echo $n ?>"><input type="text" size="10"  id="cod_producto<?php echo $n ?>"  value="<?php echo $rst2[dnc_codigo] ?>" readonly/></td>                  
                                        <td colspan="2"><input type="text" size="40"  id="descripcion<?php echo $n ?>"  value="<?php echo $rst2[dnc_descripcion] ?>" readonly/>
                                            <input  type="hidden" size="10" id="pro_id<?php echo $n ?>"  value="<?php echo $pro_id ?>" readonly />
                                            <input  type="hidden" size="10" id="tab<?php echo $n ?>"  value="<?php echo $tab ?>" readonly /></td>
                                        <td id="tdlotef<?php echo $n ?>"><input type="text" size="10" id="lote<?php echo $n ?>"  value="<?php echo $rst2[dnc_lote] ?>" lang="1" readonly/> </td> 
                                        <td id="tdcantidadf<?php echo $n ?>"><input type="text" size="8"  id="cantidadf<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_c[fac], 2)) ?>" lang="<?php echo $n ?>" readonly  style="text-align:right"/> 
                                            <input type="text" hidden="true"  size="8"  id="cantidadfa<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_c[fac], 2)) ?>" lang="<?php echo $n ?>" /></td> 
                                        <td id="tdinventario<?php echo $n ?>"><input type="text" size="8"  id="inventario<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($inv, 1)) ?>" readonly  style="text-align:right"/> 
                                            <input type="text" hidden size="8"  id="inventarioa<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($inv, 1)) ?>" /></td>  
                                        <td><input type="text" size="8"  id="cantidad<?php echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst2[dnc_cantidad], 4)) ?>" lang="<?php echo $n ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), restar(this), calculo()"  style="text-align:right" onblur="comparar(this), calculo()"/> </td>                  
                                        <td><input type="text" size="8"  id="precio_unitario<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst2[dnc_precio_unit], 4)) ?>" readonly lang="<?php echo $n ?>"  onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), restar(this), calculo()" onblur="calculo()" style="text-align:right"/></td>                  
                                        <td><input type="text" size="8"  id="descuento<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst2[dnc_porcentaje_descuento], 2)) ?>"  style="text-align:right" lang="<?php echo $n ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), calculo(), restar(this)" onblur="calculo()" readonly/></td>                  
                                        <td>
                                            <input type="text" size="8" id="descuent<?php echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst2[dnc_val_descuento], 4)) ?>" style="text-align:right" lang="<?php echo $n ?>" readonly />
                                        </td>
                                        <td><input type="text" size="8"  id="iva<?php echo $n ?>"  value="<?php echo $rst2[dnc_iva] ?>"  lang="<?php echo $n ?>" onkeyup="restar(this), calculo()" readonly  style="text-align:right"/></td>                  
                                        <td><input type="text" size="8"  id="precio_total<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst2[dnc_precio_total], 4)) ?>" readonly  style="text-align:right"/></td>                  
                                        <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" /></td>
                                    </tr> 
                                    <?php
                                }
                            }
                            ?>
                        </table>
                        <table>
                            <tr>
                                <td style="width: 400px;" class="td1" colspan="4">
                                <td style="width: 693px;" colspan="7" align="right">Subtotal :</td>
                                <td class="sbtls" ><input type="text" size="8" id="subtotal12" readonly  value="<?php echo number_format(0, 2) ?>"style="text-align:right" /></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="td1" colspan="4">
                                <td colspan="7" align="right">Subtotal 0%:</td>
                                <td class="sbtls" ><input type="text" size="8" readonly  id="subtotal0" value="<?php echo number_format(0, 2) ?>" style="text-align:right" /></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="td1" colspan="4">
                                <td colspan="7" align="right">Subtotal No Objeto Iva:</td>
                                <td class="sbtls" ><input type="text" size="8" readonly  id="subtotalno" value="<?php echo number_format(0, 2) ?>" style="text-align:right"/></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="td1" colspan="4">
                                <td colspan="7" align="right">Subtotal Excento Iva:</td>
                                <td class="sbtls" ><input type="text" size="8" readonly  id="subtotalex" value="<?php echo number_format(0, 2) ?>" style="text-align:right"/></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="td1" colspan="4">
                                <td colspan="7" align="right">Total Descuento:</td>
                                <td class="sbtls" ><input type="text" size="8" id="total_descuento" readonly  value="<?php echo number_format(0, 2) ?>" style="text-align:right" /></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="td1" colspan="4">
                                <td colspan="7" align="right">ICE:</td>
                                <td class="sbtls" ><input type="text" size="8" id="total_ice" readonly  value="<?php echo number_format(0, 2) ?>" style="text-align:right"/></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="td1" colspan="4">
                                <td colspan="7" align="right">TOTAL IVA:</td>
                                <td class="sbtls" ><input type="text" size="8" id="total_iva" readonly  value="<?php echo number_format(0, 2) ?>" style="text-align:right"/></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="td1" colspan="4">
                                <td colspan="7" align="right">IRBPNR:</td>
                                <td class="sbtls" ><input type="text" size="8" id="irbpnr" readonly  value="<?php echo number_format(0, 2) ?>" style="text-align:right"/></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="td1" colspan="4">
                                <td colspan="7" align="right">Total:</td>
                                <td class="sbtls"><input type="text" size="8" id="total_valor" readonly  value="<?php echo number_format(0, 2) ?>"  style="text-align:right"/></td>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <button id="guardar" onclick="save()">Guardar</button>   
                            <button id="cancelar" >Cancelar</button>   
                        </td>
                    </tr>
                </tfoot>
                <!------------------------------------->
            </table>
        </form>
    </body>
</html>    
<script>
    var mot = '<?php echo $rst[trs_id] ?>';
    if (mot != '') {
        $('#motivo').val(mot);
    } else {
        $('#motivo').val(1);
        ocultar();
    }
</script>
