<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_pagos.php';
include_once '../Clases/clsClase_ord_pedido_venta.php';
$Docs = new Clase_ord_pedido_venta();
$Clase_pagos = new Clase_pagos();
$Set = new Set();
if ($emisor >= 10) {
    $ems = '0' . $emisor;
} else {
    $ems = '00' . $emisor;
}
if (isset($_GET[det])) {
    $dtr = $_GET[det];
} else {
    $dtr = 0;
}
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst_sec = pg_fetch_array($Docs->lista_secuencial_documento($emisor));
    $sec = ($rst_sec[secuencial] + 1);
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
    $rst['num_secuencial'] = $ems . '-001-' . $tx . $sec;
    $rst['fecha_emision'] = date('Y-m-d');
    $rst['vendedor'] = $rst_user[usu_person];
    $rst_enc = pg_fetch_array($Docs->lista_un_registro($id));
    $cns_det = $Docs->lista_detalle_registro_pedido($id);
    $cns_pag = $Docs->lista_pagos_registro_pedido($id);
    $num_doc = $rst_enc[ped_num_registro];
	$observacion2 = $rst_enc[ped_observacion];
    $det = 1;
    $rst[opg_codigo] = pg_num_rows($Docs->lista_pagos_registro_pedido($id));
}
$rst_enc[ped_vendedor];
$rst_vend = pg_fetch_array($Docs->lista_vendedor($rst_enc[ped_vendedor]));
$id_vnd = $rst_vend[vnd_id];
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">
        <meta charset="utf-8">
        <title>Factura</title>
        <script>
            id = '<?php echo $id ?>';
            emi = '<?php echo $emisor ?>';
            det = '<?php echo $det ?>';
            dtr = '<?php echo $dtr ?>';
            vnd = '<?php echo $id_vnd ?>';
            $(function () {

                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    if (this.lang == 1) {
                        save(id);
                    } else if (this.lang == 0) {
                        var tr = $('#tbl_form').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        if ($('#pro_descripcion' + a).val().length != 0 && $('#cantidad' + a).val().length != 0) {
                            clona_fila('#tbl_form');
                        }
                    }
                });
                calculo();
                $('#con_clientes').hide();
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-fecha_emision"});
                posicion_aux_window();
            });


            function eliminaDuplicados(arr) {
                var i,
                        len = arr.length,
                        out = [],
                        obj = {};

                for (i = 0; i < len; i++) {
                    obj[arr[i]] = 0;
                }
                for (i in obj) {
                    out.push(i);
                }
                return out;
            }

            function auxWindow(a, id) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a) {
                    case 0://pdf
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_factura.php?id=' + id;
                        break;
                    case 1://talonario
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_talonario_factura.php?id=' + id + '&det=1';
                        break;
                }
            }
            function clona_fila(table, a) {
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
                if (a == 1) {
                    tr.find("td").attr("id", function () {
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
                }
                $(table).find("tbody tr:last").after(tr);
                if (a != 1) {
                    obj = $(table).find(".itm");
                    idt = obj[(obj.length - 1)].lang;
                    $('#pro_descripcion' + idt).focus();
                }
            }
            ;
            function save(id) {
                var data = Array();
                doc = document.getElementsByClassName('itm');
                n = 0;
                f = fecha_emision.value.split('-');
                fch = f[0] + f[1] + f[2];
                ns = num_secuencial.value.split('-');
                sec = num_secuencial.value;
                cli_id = '<?php echo $rst_enc[cli_id] ?>';
                if (dtr == 1) {
                    estado = 0;
                } else {
                    estado = cli_estado.value;
                }
                data = Array(
                        emi,
                        cli_id,
                        vnd,
                        id, //ped_id,
                        fecha_emision.value,
                        num_secuencial.value,
                        nombre.value,
                        identificacion.value,
                        email_cliente.value,
                        direccion_cliente.value,
                        subtotal12.value.replace(',', ''),
                        subtotal0.value.replace(',', ''),
                        subtotalex.value.replace(',', ''), //subtotal_exento_iva.value,
                        subtotalno.value.replace(',', ''),
                        total_descuento.value.replace(',', ''),
                        '0', //ice
                        total_iva.value.replace(',', ''), //total_iva.value,
                        '0', //irbpnr
                        '0', //total_propina.value,
                        total_valor.value.replace(',', ''),
                        telefono_cliente.value,
                        observacion.value,
                        cli_ciudad.value,
                        cli_pais.value,
                        cli_parroquia.value,
                        '0', //subtotal
                        vendedor.value,
                        estado
                        )
                var data2 = Array();
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                while (n < i) {
                    n++;
                    if ($('#pro_descripcion' + n).val() != null && parseFloat($('#cantidad' + n).val()) != 0) {
                        cod = $('#pro_descripcion' + n).val();
                        desc = $('#pro_referencia' + n).val();
                        cnt = $('#cantidad' + n).val();
                        und = $('#pro_unidad' + n).val();
                        pr = $('#pro_precio' + n).val();
                        dsc = $('#descuento' + n).val();
                        iva = $('#iva' + n).val().trim();
                        pt = $('#valor_total' + n).val().replace(',', '');
                        dsc0 = $('#descuent' + n).val();
                        aux = $('#codaux' + n).val();
                        lote = $('#lote' + n).val();
                        pro_id = $('#pro_id' + n).val();
                        tab = $('#tabla' + n).val();
                        uni_pro_reg = $('#uni_pro_reg' + n).val();
                        uni_pro_ped = $('#uni_pedido' + n).val();
                        data2.push(
                                pro_id + '&' +
                                cod + '&' + //cod_producto,
                                aux + '&' + //cod_aux,
                                cnt + '&' + //cantidad,
                                desc + '&' + //descripcion,
                                pr + '&' +
                                dsc + '&' +
                                dsc0 + '&' +
                                pt + '&' +
                                iva + '&' +
                                '0&' + //ice
                                '0&' + //cost unitario
                                '0&' + //cost total
                                '0&' + //irbrp  
                                '0&' + //ic_p   
                                '0&' + //ic_cod   
                                '0&' + //irbp_p   
                                lote + '&' + //irbp_p  
                                tab + '&' +
                                uni_pro_reg + '&' +
                                uni_pro_ped
                                );
                    }
                }
                if (emi != 1 && emi != 10) {
                    var data3 = Array();
                    n = 0;
                    while (n < 4) {
                        n++;
                        pag_forma = $('#pago_forma' + n).val();
                        pag_banco = $('#pago_banco' + n).val();
                        pag_tarjeta = $('#pago_tarjeta' + n).val();
                        pag_cantidad = $('#pago_cantidad' + n).val();
                        pag_contado = $('#pago_contado' + n).val();
                        nc_num = $('#num_nota_credito' + n).val();
                        id_ntc = $('#id_nota_credito' + n).val();
                        val_ntc = $('#val_nt_cre' + n).val();
                        data3.push(
                                emi + '&' +
                                pag_forma + '&' +
                                pag_banco + '&' +
                                pag_tarjeta + '&' +
                                pag_cantidad + '&' +
                                pag_contado + '&' +
                                nc_num + '&' +
                                id_ntc + '&' +
                                val_ntc
                                );
                    }
                } else {
                    var data3 = Array();
                    i = $('.itme').length;
                    n = 0;
                    while (n < i) {
                        n++;
                        pag = $('#pag' + n).val();
                        dias = $('#dias' + n).val();
                        valor = $('#valor' + n).val();
                        fecha = $('#fecha' + n).val();
                        data3.push(
                                emi + '&' +
                                pag + '&' +
                                dias + '&' +
                                valor + '&' + //cod_producto,
                                fecha
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

                        var tr = $('#tbl_form').find("tbody tr:last");
                        a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        pag = document.getElementsByClassName('itme');
                        n = 0;
                        j = 0;
                        if (num_secuencial.value.length == 0) {
                            $("#num_secuencial").css({borderColor: "red"});
                            $("#num_secuencial").focus();
                            return false;
                        } else if (cod_punto_emision.value.length == 0) {
                            $("#cod_punto_emision").css({borderColor: "red"});
                            $("#cod_punto_emision").focus();
                            return false;
                        } else if (identificacion.value.length == 0) {
                            $("#identificacion").css({borderColor: "red"});
                            $("#identificacion").focus();
                            return false;
                        } else if (nombre.value.length == 0) {
                            $("#nombre").css({borderColor: "red"});
                            $("#nombre").focus();
                            return false;
                        } else if (direccion_cliente.value.length == 0) {
                            $("#direccion_cliente").css({borderColor: "red"});
                            $("#direccion_cliente").focus();
                            return false;
                        } else if (telefono_cliente.value.length == 0) {
                            $("#telefono_cliente").css({borderColor: "red"});
                            $("#telefono_cliente").focus();
                            return false;
                        } else if (email_cliente.value.length == 0) {
                            $("#email_cliente").css({borderColor: "red"});
                            $("#email_cliente").focus();
                            return false;
                        } else if (cli_parroquia.value.length == 0) {
                            $("#cli_parroquia").css({borderColor: "red"});
                            $("#cli_parroquia").focus();
                            return false;
                        }
                        else if (cli_ciudad.value.length == 0) {
                            $("#cli_ciudad").css({borderColor: "red"});
                            $("#cli_ciudad").focus();
                            return false;
                        }
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#pro_descripcion' + n).val() != null) {
                                    if ($('#pro_descripcion' + n).val() == 0) {
                                        $('#pro_descripcion' + n).css({borderColor: "red"});
                                        $('#pro_descripcion' + n).focus();
                                        return false;
                                    }
                                    else if ($('#cantidad' + n).val().length == 0) {
                                        $('#cantidad' + n).css({borderColor: "red"});
                                        $('#cantidad' + n).focus();
                                        return false;
                                    }
                                    else if ($('#descuento' + n).val().length == 0) {
                                        $('#descuento' + n).css({borderColor: "red"});
                                        $('#descuento' + n).focus();
                                        return false;
                                    }

                                    if (parseFloat($('#cantidad' + n).val()) > 0) {
                                        if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cantidad' + n).val())) {
                                            alert('LA CANTIDAD ES MAYOR QUE EL INVENTARIO');
                                            $('#cantidad' + n).css({borderColor: "red"});
                                            $('#cantidad' + n).focus();
                                            return false;
                                        } else if ($('#uni_pro_reg' + n).val() == 'ROLLO' && $('#uni_pedido' + n).val() == 'M') {
                                            if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                                                alert('LA CANTIDAD ES MAYOR QUE EL INVENTARIO');
                                                $('#cantidad' + n).css({borderColor: "red"});
                                                $('#cantidad' + n).focus();
                                                return false;
                                            }
                                        } else if ($('#uni_pro_reg' + n).val() == 'ROLLO' && $('#uni_pedido' + n).val() == 'KG') {
                                            if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                                                alert('LA CANTIDAD ES MAYOR QUE EL INVENTARIO');
                                                $('#cantidad' + n).css({borderColor: "red"});
                                                $('#cantidad' + n).focus();
                                                return false;
                                            }
                                        } else if ($('#uni_pro_reg' + n).val() == 'KG' && $('#uni_pedido' + n).val() == 'ROLLO') {
                                            if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                                                alert('LA CANTIDAD ES MAYOR QUE EL INVENTARIO');
                                                $('#cantidad' + n).css({borderColor: "red"});
                                                $('#cantidad' + n).focus();
                                                return false;
                                            }
                                        } else if ($('#uni_pro_reg' + n).val() == 'KG' && $('#uni_pedido' + n).val() == 'M') {
                                            if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                                                alert('LA CANTIDAD ES MAYOR QUE EL INVENTARIO');
                                                $('#cantidad' + n).css({borderColor: "red"});
                                                $('#cantidad' + n).focus();
                                                return false;
                                            }
                                        } else if ($('#uni_pro_reg' + n).val() == 'M' && $('#uni_pedido' + n).val() == 'ROLLO') {
                                            if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                                                alert('LA CANTIDAD ES MAYOR QUE EL INVENTARIO');
                                                $('#cantidad' + n).css({borderColor: "red"});
                                                $('#cantidad' + n).focus();
                                                return false;
                                            }
                                        } else if ($('#uni_pro_reg' + n).val() == 'M' && $('#uni_pedido' + n).val() == 'KG') {
                                            if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                                                alert('LA CANTIDAD ES MAYOR QUE EL INVENTARIO');
                                                $('#cantidad' + n).css({borderColor: "red"});
                                                $('#cantidad' + n).focus();
                                                return false;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if ($('#total_valor').val() > 20 && $('#nombre').val() == 'CONSUMIDOR FINAL') {
                            alert('PARA CONSUMIDOR FINAL EL VALOR TOTAL NO PUDE SER MAYOR $20');
                            return false;
                        }


                        if (emi == 1 || emi == 10) {
                            if (pag.length != 0) {
                                while (j < pag.length) {
                                    j++;
                                    if ($('#pag' + j).val() == 0) {
                                        $('#pag' + j).css({borderColor: "red"});
                                        $('#opg_codigo').focus();
                                        return false;
                                    }
                                    else if ($('#dias' + j).val() == 0) {
                                        $('#dias' + j).css({borderColor: "red"});
                                        $('#dias' + j).focus();
                                        return false;
                                    }
                                    else if ($('#valor' + j).val().length == 0) {
                                        $('#valor' + j).css({borderColor: "red"});
                                        $('#valor' + j).focus();
                                        return false;
                                    }
                                    else if ($('#fecha' + j).val().length == 0) {
                                        $('#fecha' + j).css({borderColor: "red"});
                                        $('#fecha' + j).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        else if (emi != 1 && emi != 10) {
                            if ($('#vendedor').val().length == 0) {
                                $('#vendedor').css({borderColor: "red"});
                                $('#vendedor').focus();
                                return false;
                            }
                            sp = (parseFloat($('#pago_cantidad1').val()) * 1) + (parseFloat($('#pago_cantidad2').val()) * 1) + (parseFloat($('#pago_cantidad3').val()) * 1) + (parseFloat($('#pago_cantidad4').val()) * 1);
                            if (sp.toFixed(4) != $('#total_valor').val().replace(',', '')) {
                                alert('LA SUMA DE LOS PAGOS NO COINCIDEN CON EL TOTAL FACTURADO');
                                return false;
                            }

                            j = 0;
                            while (j < 5) {
                                j++;
                                if ($('#pago_cantidad' + j).val() != 0) {
                                    if ($('#pago_forma' + j).val() == 0) {
                                        $('#pago_forma' + j).css({borderColor: "red"});
                                        $('#pago_forma' + j).focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                    if ($('#pago_banco' + j).val() == 0 && $('#pago_banco' + j).attr('disabled') == false) {
                                        $('#pago_banco' + j).css({borderColor: "red"});
                                        $('#pago_banco' + j).focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                    if ($('#pago_tarjeta' + j).val() == 0 && $('#pago_tarjeta' + j).attr('disabled') == false) {
                                        $('#pago_tarjeta' + j).css({borderColor: "red"});
                                        $('#pago_tarjeta' + j).focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                    if ($('#pago_contado' + j).val() == 0 && $('#pago_contado' + j).attr('disabled') == false) {
                                        $('#pago_contado' + j).css({borderColor: "red"});
                                        $('#pago_contado' + j).focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                }
                            }

                            sp = (parseFloat($('#pago_cantidad1').val()) * 1) + (parseFloat($('#pago_cantidad2').val()) * 1) + (parseFloat($('#pago_cantidad3').val()) * 1) + (parseFloat($('#pago_cantidad4').val()) * 1);
                            if (sp.toFixed(4) != $('#total_valor').val().replace(',', '')) {
                                alert('LA SUMA DE LOS PAGOS NO COINCIDEN CON EL TOTAL FACTURADO');
                                $('#save').attr('disabled', false);
                                return false;
                            }
                        }
                        if (parseFloat($('#total_valor').val()) == 0) {
                            alert('NO SE PUEDE GUARDAR UNA FACTURA CON VALOR CERO');
                            return false;
                        }
                        loading('visible');
                        $('#proceso').show();
                        $('#sri_cont').show();
                        $('#mail_cont').hide();
                    },
                    type: 'POST',
                    url: 'actions_factura_nuevo.php',
                    data: {op: 2, 'data[]': data, 'data2[]': data2, 'data3[]': data3, id: 0, 'fields[]': fields},
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            asientos(dat[0], dat[1]);
                        } else if (dat[0] == 1) {
                            alert('Numero Secuencial de la Factura ya existe \n Debe hacer otra factura con otro Secuencial');
                            loading('hidden');
                        } else if (dat[0] == 2) {
                            alert('Una de las cuentas de la factura esta inactiva');
                            loading('hidden');
                        } else {
                            alert(dt);
                            loading('hidden');
                        }
                    }
                })
            }

//            function envia_sri(id, a) {
//                $.ajax({
//                    beforeSend: function () {
//
//                    },
//                    timeout: 10000,
//                    type: 'POST',
//                    url: '../xml/factura_xml.php',
//                    data: {id: id},
//                    error: function (j, t, e) {
//                        if (t == 'timeout') {
//                            confirm_sri(id, a);
//                        }
//                    },
//                    success: function (dt) {
//                        dat = dt.split('&');
//                        cambia_estado(dat, id);
//                    }
//                });
//            }

//            function confirm_sri(id, s)
//            {
//                if (a != 3) {
//                    var r = confirm("Error de conexion con el SRI \n Desea Enviar Nuevamente");
//                    if (r == true) {
//                        a = s + 1;
//                        envia_sri(id, a);
//                    } else {
//                        window.history.go(0);
//                    }
//                } else {
//                    alert('Error de conexion con el SRI \n Intente envio mas tarde');
//                    window.history.go(0);
//                }
//            }

//            function cambia_estado(dat, id) {
//                $.ajax({
//                    beforeSend: function () {
//
//                    },
//                    type: 'POST',
//                    url: 'actions.php',
//                    data: {act: 67, id: id, 'data[]': dat},
//                    success: function (dt) {
//                        r = dt.split('&');
//                        if (r[0] == 0) {
//                            if (dat[4].length == 38) {
//                                envi_mail(r[1], 1);
//                            } else {
//                                alert(dat[3]);
//                                loading('hidden');
//                            }
//
//                        } else {
//                            alert(dt);
//                        }
//                    }
//                });
//            }
//
//            function envi_mail(id, a) {
//                $('#proceso').show();
//                $('#sri_cont').hide();
//                $('#mail_cont').show();
//
//                $.ajax({
//                    beforeSend: function () {
//
//                    },
//                    type: 'GET',
//                    url: '../Reports/pdf_factura_mail.php',
//                    timeout: 10000,
//                    data: {id: id},
//                    error: function (j, t, e) {
//                        if (t == 'timeout') {
//                            confirm_email(id, a);
//                        }
//                    },
//                    success: function (dt) {
//                        rs = dt.split('&');
//                        if (rs[0] == 0) {
//                            cambia_status_mail(rs[1], id);
//                        } else {
//                            alert('No se pudo enviar ' + dt);
//                            window.history.go(-1);
//                        }
//                    }
//                });
//            }
//
//            function confirm_email(id, s)
//            {
//                if (a != 3) {
//                    var r = confirm("Servidor e-mail inaccesible \n Desea Enviar Via e-mail Nuevamente");
//                    if (r == true) {
//                        a = s + 1;
//                        envi_mail(id, a);
//                    } else {
//                        window.history.go(-1);
//                    }
//                } else {
//                    alert('Error de conexion con el Servidor e-mail \n Intente envio mas tarde');
//                    window.history.go(-1);
//                }
//            }
//
//            function cambia_status_mail(id, num) {
//                $.ajax({
//                    beforeSend: function () {
//
//                    },
//                    type: 'POST',
//                    url: 'actions.php',
//                    data: {act: 73, id: id},
//                    success: function (dt) {
//                        loading('hidden');
//                        if (dt == 0) {
//                            alert('Documento Autorizado y Enviado Correctamente');
////                            window.history.go(0);
//                            if (emi == 1 || emi == 10) {
//                                auxWindow(0, num);
//                            } else {
//                                auxWindow(1, num);
//                            }
//
//                        } else {
//                            alert(dt);
//                        }
//
//                    }
//                });
//            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#proceso').css('visibility', prop);
            }

            function load_cliente(obj) {
                $.post("actions.php", {act: 63, id: obj.value, s: 0},
                function (dt) {
                    if (dt != '') {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dt);
                    } else {
                        alert('Cliente no existe \n Cree uno Nuevo??');
                        $('#nombre').focus();
                    }
                });
            }

            function load_cliente2(obj) {
                $.post("actions.php", {act: 63, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Cree uno Nuevo??');
                        $('#nombre').focus();
                    } else {
                        dat = dt.split('&');
                        $('#identificacion').val(dat[0]);
                        $('#nombre').val(dat[1]);
                        $('#direccion_cliente').val(dat[2]);
                        $('#telefono_cliente').val(dat[3]);
                        $('#email_cliente').val(dat[4]);
                        $('#cli_parroquia').val(dat[5]);
                        $('#cli_ciudad').val(dat[6]);
                        $('#cli_pais').val(dat[7]);
                    }
                    $('#con_clientes').hide();
                });
            }



            function elimina_fila(obj, a) {
                if (a != 1) {
                    itm = $('.itm').length;
                } else {
                    itm = $('.itme').length;
                }
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                    if (a != 1) {
                        calculo('1');
                    }
                } else {
                    alert('No puede eliminar todas las filas');
                }
            }


            function calculo(obj) {
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                var t12 = 0;
                var t0 = 0;
                var tex = 0;
                var tno = 0;
                var tdsc = 0;
                var tiva = 0;
                var gtot = 0;
                var sal = 0;
                var ent = 0;
                var sol = 0;
                var tsal = 0;

                while (n < i) {
                    n++;
                    if ($('#item' + n).val() == null) {
                        ob = 0;
                        val = 0;
                        d = 0;
                        cnt = 0;
                        pr = 0;
                        d = 0;
                        vtp = 0;
                        vt = 0;
                        entr = 0;
                        soli = 0;
                    } else {
                        cnt = $('#cantidad' + n).val().replace(',', '');
                        entr = $('#entregado' + n).val().replace(',', '');
                        soli = $('#solicitado' + n).val().replace(',', '');
                        pr = $('#pro_precio' + n).val().replace(',', '');
                        d = $('#descuento' + n).val().replace(',', '');
                        vtp = cnt * pr; //Valor total parcial
                        vt = (vtp * 1) - (vtp * d / 100);
                        $('#descuent' + n).val((vtp * d / 100).toFixed(4));
                        $('#valor_total' + n).val(vt.toFixed(4));
                        ob = $('#iva' + n).val();
                        val = $('#valor_total' + n).val().replace(',', '');
                        d = $('#descuent' + n).val().replace(',', '');
                    }

                    tdsc = (tdsc * 1) + (d * 1);
                    if (ob == '14') {
                        t12 = (t12 * 1 + val * 1);
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
                    sol = sol + parseFloat(soli);
                    ent = ent + parseFloat(entr);
                    sal = sal + parseFloat(cnt);
                }
                tsal = sol - ((ent * 1) + (sal * 1));
                tiva = (t12 * 14 / 100);
                gtot = (t12 * 1 + t0 * 1 + tex * 1 + tno * 1 + tiva * 1);
                $('#subtotal12').val(t12.toFixed(4));
                $('#subtotal0').val(t0.toFixed(4));
                $('#subtotalex').val(tex.toFixed(4));
                $('#subtotalno').val(tno.toFixed(4));
                $('#total_descuento').val(tdsc.toFixed(4));
                $('#total_iva').val(tiva.toFixed(4));
                $('#total_valor').val(gtot.toFixed(4));
                $('#cli_estado').val(tsal);
                if (emi == 1 || emi == 10) {
                    calculo_pago();
                    calculo_fecha();
                } else {
                    pago_cantidad1.value = gtot.toFixed(4);
                    calculo_pago_locales();
                }

            }
            function calculo_pago_locales() {
                tp = parseFloat(pago_cantidad1.value) + parseFloat(pago_cantidad2.value) + parseFloat(pago_cantidad3.value) + parseFloat(pago_cantidad4.value);
                flt = parseFloat(total_valor.value.replace(',', '')) - tp.toFixed(4);
                if (flt.toFixed(4) < 0) {
                    alert('Valor ingresado incorrecto');
                } else {
                    t_pagos.value = flt.toFixed(4);
                }
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
//                parent.document.getElementById('mainFrame').src = '../';
            }
            function cerrar_ventana() {
                $('#con_clientes').hide();
            }

            function pago(obj) {
                n = 0;
                itm = $('.itme').length;
                if (obj.value <= 4) {
                    f = obj.value - itm;
                    if (f > 0) {
                        while (n < f) {
                            clona_fila('#tbl_colum3', 1);
                            n++;
                        }
                    } else {
                        f = Math.abs(f);
                        n = 0;
                        while (n < f) {
                            itm = $('.itme').length;
                            e = '#p' + itm;
                            elimina_fila(e, 1);
                            n++;
                        }
                    }
                }
            }

            function calculo_pago() {
                itm = $('.itme').length;
                n = 0;
                while (n < itm) {
                    n++;
                    pag = $('#pag' + n).val().replace(',', '');
                    vt = $('#total_valor').val().replace(',', '');
                    tot = (pag / 100) * vt;
                    $('#valor' + n).val(tot.toFixed(4));
                    if ($('#pag' + n).val() > 100) {
                        alert('La cantidad es mayor a 100%');
                        $('#pag' + n).val(0);
                        $('#valor' + n).val(0);
                    }
                }
            }

            function calculo_fecha(obj) {
                itm = $('.itme').length;
                n = 0;
                while (n < itm) {
                    n++;
                    var sumarDias = parseInt($('#dias' + n).val());
                    var fecha = $('#fecha_emision').val();
                    fecha = fecha.replace("-", "/").replace("-", "/");
                    fecha = new Date(fecha);
                    fecha.setDate(fecha.getDate() + sumarDias);
                    var anio = fecha.getFullYear();
                    var mes = fecha.getMonth() + 1;
                    var dia = fecha.getDate();
                    if (mes.toString().length < 2) {
                        mes = "0".concat(mes);
                    }
                    if (dia.toString().length < 2) {
                        dia = "0".concat(dia);
                    }
                    $('#fecha' + n).val(anio + "-" + mes + "-" + dia);
                }
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

            function habilitar(obj) {
                if (obj.lang != null) {
                    s = obj.lang;
                } else {
                    s = obj;
                }
                if ($('#pago_forma' + s).val() == '1') {
                    $('#pago_banco' + s).attr('disabled', false);
                    $('#pago_tarjeta' + s).attr('disabled', false);
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_contado' + s).attr('disabled', false);
                    $('#pago_banco' + s).focus();
                } else if ($('#pago_forma' + s).val() == '2') {
                    $('#pago_banco' + s).attr('disabled', false);
                    $('#pago_tarjeta' + s).attr('disabled', false);
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_contado' + s).val('0');
                    $('#pago_banco' + s).focus();
                } else if ($('#pago_forma' + s).val() == '3') {
                    $('#pago_banco' + s).attr('disabled', false);
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_contado' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_banco' + s).focus();
                } else if ($('#pago_forma' + s).val() > '3') {
                    $('#pago_banco' + s).attr('disabled', true);
                    $('#pago_banco' + s).val('0');
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_contado' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_cantidad' + s).focus();
                } else {
                    $('#pago_banco' + s).attr('disabled', true);
                    $('#pago_banco' + s).val('0');
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_contado' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', true);
                }
                calculo_pago_locales();
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
                    $('#cantidad' + j).focus();
                    v = 1;
                    load_producto(j, v);
                }


            }


            function load_producto(j, v) {
                if (v == 1) {
                    vl = $('#pro_descripcion' + j).val();
                    lt = $('#lote' + j).val();
                } else {
                    vl = $('#pro_descripcion' + j).val();
                    lt = 0;
                }
                $.post("actions.php", {act: 64, id: vl, lt: lt, s: emi},
                function (dt) {
                    dat = dt.split('&');
                    $('#pro_descripcion' + j).val(dat[0]);
                    $('#pro_referencia' + j).val(dat[1]);
                    $('#iva' + j).val(dat[4]);
                    $('#descuent' + j).val(0);
                    $('#codaux' + j).val(dat[7]);
                    $('#lote' + j).val(dat[8]); ///comentar para codigo ean
                    $('#pro_id' + j).val(dat[10] + dat[9]);
                    $('#cantidad' + j).val('');


                    if (dat[3] == '') {
                        $('#pro_precio' + j).val(0);
                        $('#iva' + j).val('12');
                    } else {
                        $('#pro_precio' + j).val(dat[3]);
                    }

                    if (dat[5] == '') {
                        $('#descuento' + j).val(0);
                    } else {
                        $('#descuento' + j).val(dat[5]);
                    }

                    if (dat[11] == '') {
                        $('#inventario' + j).val('0');
                    } else {
                        $('#inventario' + j).val(dat[11]);
                    }
                    calculo('1');
                });

            }


            function enter(e) {
                var char = e.which;
                if (char == 13) {
                    return false;
                }
            }

            function asientos(sms, id1) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: 0, id: id1, x: det},
                    success: function (dt) {
                        loading('hidden');
                        if (dt == 0) {
                            if (emi == 1 || emi == 10) {
                                auxWindow(0, id1);
                            } else {
                                auxWindow(1, id1);
                            }

                        } else {
                            alert(dt);
                        }

                    }
                });
            }

            function inventario(obj) {
                n = obj.lang;
                can = obj.value;
                rollo = 1;
                mts = 100; //Largo producto
                kg = 27; //Peso producto
                if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
                    alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                    $(obj).val('0');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    calculo();
                } else if ($('#uni_pro_reg' + n).val() == 'ROLLO' && $('#uni_pedido' + n).val() == 'M') {
                    cantid = can * rollo / mts;
                    $('#cant_conv_uni' + n).val(cantid);
                    if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                        alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                        $(obj).val('0');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                        calculo();
                    }
                } else if ($('#uni_pro_reg' + n).val() == 'ROLLO' && $('#uni_pedido' + n).val() == 'KG') {
                    cantid = can * rollo / kg;
                    $('#cant_conv_uni' + n).val(cantid);
                    if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                        alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                        $(obj).val('0');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                        calculo();
                    }
                } else if ($('#uni_pro_reg' + n).val() == 'KG' && $('#uni_pedido' + n).val() == 'ROLLO') {
                    cantid = can * kg / rollo;
                    $('#cant_conv_uni' + n).val(cantid);
                    if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                        alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                        $(obj).val('0');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                        calculo();
                    }
                } else if ($('#uni_pro_reg' + n).val() == 'KG' && $('#uni_pedido' + n).val() == 'M') {
                    cantid = can * kg / mts;
                    $('#cant_conv_uni' + n).val(cantid);
                    if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                        alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                        $(obj).val('0');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                        calculo();
                    }
                } else if ($('#uni_pro_reg' + n).val() == 'M' && $('#uni_pedido' + n).val() == 'ROLLO') {
                    cantid = can * mts / rollo;
                    $('#cant_conv_uni' + n).val(cantid);
                    if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                        alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                        $(obj).val('0');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                        calculo();
                    }
                } else if ($('#uni_pro_reg' + n).val() == 'M' && $('#uni_pedido' + n).val() == 'KG') {
                    cantid = can * mts / kg;
                    $('#cant_conv_uni' + n).val(cantid);
                    if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cant_conv_uni' + n).val())) {
                        alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                        $(obj).val('0');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                        calculo();
                    }
                }
            }

            function solicitado(obj) {
                n = obj.lang;
                if (parseFloat($('#solicitado' + n).val()) < parseFloat($(obj).val())) {
                    alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE LO SOLICTADO');
                    $(obj).val('0');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    calculo();
                }
            }

            function pag_sig(obj) {
                f = obj.lang;
                s = parseInt(f) + 1;
                tp = parseFloat(pago_cantidad1.value) + parseFloat(pago_cantidad2.value) + parseFloat(pago_cantidad3.value) + parseFloat(pago_cantidad4.value);
                flt = parseFloat(total_valor.value) - parseFloat(tp);
                if (obj.value != 0 && (flt.toFixed(4) > 0)) {
                    $('#pago_cantidad' + s).val(flt.toFixed(4));
                }
            }

            function busqueda_ntscre(obj) {
                if (obj.lang != null) {
                    s = obj.lang;
                } else {
                    s = obj;
                }
                nc = obj.value;
                ruc_cli = $('#identificacion').val();
                if (ruc_cli != '') {
                    if (nc == 8) {
                        $.post("actions_factura_nuevo.php", {op: 4, id: ruc_cli, s: 0, l: s, doc: nc},
                        function (dt) {
                            if (dt != '') {
                                $('#con_clientes').css('visibility', 'visible');
                                $('#con_clientes').show();
                                $('#clientes').html(dt);
                            } else {
                                alert('El Cliente no tiene Documentos \n En esta opcion');
                                $('#num_nota_credito' + s).val('');
                                $('#id_nota_credito' + s).val('0');
                                $('#val_nt_cre' + s).val('');
                                $('#pago_forma' + s).val(0);
                                $('#pago_forma' + s).focus();
                                $('#pago_cantidad' + s).val('0');
                                $('#pago_cantidad' + s).attr('disabled', true);
                            }
                        });
                    } else {
                        $('#num_nota_credito' + s).val('');
                        $('#id_nota_credito' + s).val('0');
                        $('#val_nt_cre' + s).val('');
                    }
                } else {
                    alert('Debe elejir un cliente');
                    $('#pago_forma' + s).val(0);
                    $('#pago_cantidad' + s).attr('disabled', true);
                    $('#pago_cantidad' + s).attr('disabled', true);
                    $('#identificacion').focus();
                    $('#num_nota_credito' + s).val('');
                    $('#id_nota_credito' + s).val('0');
                    $('#val_nt_cre' + s).val('');
                }
            }

            function load_notas_credito(n, obj) {
                id1 = $('#id_nota_credito1').val();
                id2 = $('#id_nota_credito2').val();
                id3 = $('#id_nota_credito3').val();
                id4 = $('#id_nota_credito4').val();
                id5 = obj;
                if (id1 == id5 || id2 == id5 || id3 == id5 || id4 == id5) {
                    $('#con_clientes').hide();
                    alert('Documento ya ingresado');
                    $('#pago_forma' + n).val(0);
                    $('#num_nota_credito' + n).val('');
                    $('#id_nota_credito' + n).val('0');
                    $('#val_nt_cre' + n).val('');
                    $('#pago_cantidad' + n).val('0');
                    $('#pago_cantidad' + n).attr('disabled', true);
                    obj = '';
                    return false;
                }
                $.post("actions_factura_nuevo.php", {op: 4, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('El Cliente no tiene Documentos \n En esta opcion');
                        $('#pago_forma' + n).val(0);
                        $('#pago_forma' + n).focus();
                    } else {
                        dat = dt.split('&');
                        $('#num_nota_credito' + n).val(dat[0]);
                        $('#pago_cantidad' + n).val(dat[1]);
                        $('#id_nota_credito' + n).val(dat[2]);
                        $('#val_nt_cre' + n).val(dat[1]);
                        $('#pago_cantidad' + n).focus();
                        calculo_pago_locales();
                    }
                    if (dt == 1) {
                        $('#num_nota_credito' + n).val('');
                        $('#id_nota_credito' + n).val('0');
                        $('#val_nt_cre' + n).val('');
                        $('#pago_cantidad' + n).val(0);
                        $('#pago_cantidad' + n).attr('disabled', true);
                        calculo_pago_locales();
                    }
                    $('#con_clientes').hide();
                }
                );
            }

            function verificar_cuenta(obj) {
                if (obj.lang != null) {
                    s = obj.lang;
                } else {
                    s = obj;
                }
                $.post("actions_factura_nuevo.php", {op: 5, id: obj.value, usu: emi},
                function (dt) {
                    if (dt == 1) {
                        alert('La Cuenta de esta forma de Pago \n Se encuentra inactiva en este momento');
                        $('#pago_forma' + s).val(0);
                        $('#pago_banco' + s).attr('disabled', true);
                        $('#pago_tarjeta' + s).attr('disabled', true);
                        $('#pago_cantidad' + s).attr('disabled', true);
                        $('#pago_contado' + s).attr('disabled', true);
                    }
                });
            }

        </script>
        <style>
            .fila-base{ display: none; } /* fila base oculta */
            .eliminar{ cursor: pointer; color: #000; }
            thead tr td{
                font-size: 11px;
                border:solid 1px #ccc;
            }
            .totales td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
            *{
                font-size: 11px;
                font-weight:100; 
            }
            select{
                width: 150px;
            }
        </style>
    </head>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="mensaje" hidden></div>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form id="frm_save" lang="0" autocomplete="off" >
            <table id="tbl_form" border="1">
                <thead>
                    <tr>
                        <th colspan="10" >
                            <?php echo "FORMULARIO DE FACTURA " . $bodega ?>
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="5">
                        <table>
                            <tr>
                                <td>Factura N:</td>
                                <td>
                                    <input type="text" size="20" id="num_secuencial" readonly value="<?php echo $rst['num_secuencial'] ?>"/>
                                    <input type="hidden" id="cod_punto_emision" value="<?php echo $emisor ?>" />
                                </td>
                                <td>Fecha Pedido:</td>
                                <td>
                                    <input type="text" size="10" id="fecha_pedido" readonly value="<?php echo $rst_enc['ped_femision'] ?>" />
                                </td>
                                <td>Fecha Emision:</td>
                                <td>
                                    <input type="text" size="10" id="fecha_emision" readonly value="<?php echo $rst['fecha_emision'] ?>" />
                                    <img src="../img/calendar.png" id="im-fecha_emision" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td><table id='tbl_colum2' border="0" ><tr class="trthead"><td  colspan="2" style="background:#00557F ;color:white " align='center' ><label class="tdtitulo">CLIENTE:</label></td></tr>
                            <tr>
                                <td style="width:80px ">RUC/CC:</td>
                                <td><input type="text" size="45"  id="identificacion" readonly value="<?php echo $rst_enc[ped_ruc_cc_cliente] ?>" onchange="load_cliente(this)"  /></td>
                            </tr>
                            <tr>
                                <td>NOMBRE:</td>
                                <td><input type="text"  size="45" id="nombre" readonly value="<?php echo $rst_enc[ped_nom_cliente] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>DIRECCION:</td>
                                <td><input type="text"  size="45" id="direccion_cliente" readonly value="<?php echo $rst_enc[ped_dir_cliente] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>TELEFONO:</td>
                                <td><input type="text"   size="45"  id="telefono_cliente" readonly value="<?php echo $rst_enc[ped_tel_cliente] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>EMAIL:</td>
                                <td><input type="text"   size="45"  id="email_cliente" readonly value="<?php echo $rst_enc[ped_email_cliente] ?>" style="text-transform:lowercase " /></td>
                            </tr>
                            <tr>
                                <td>PARROQUIA:</td>
                                <td><input type="text"  size="45"  id="cli_parroquia" readonly value="<?php echo $rst_enc[ped_parroquia_cliente] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>CIUDAD:</td>
                                <td><input type="text"  size="45"  id="cli_ciudad" readonly value="<?php echo $rst_enc[ped_ciu_cliente] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>PAIS:</td>
                                <td><input type="text"  size="45"  id="cli_pais" readonly value="<?php echo $rst_enc[ped_pais_cliente] ?>"  /></td>
                                <td><input type="hidden"  size="5"  id="cli_estado" readonly value="" /></td>
                            </tr></table></td>
                    <td valign="top">
                        <table width='100%' id='tbl_colum3' border="0">
                            <td class="trthead" colspan="6" align='center' style="background:#00557F ;color:white " >
                                <label  class="tdtitulo">FORMAS DE PAGO</label>
                            </td>
                            <tr>
                                <td class="vendedor">Vendedor:</td>
                                <td>  
                                    <input type="text" size="" id="vendedor" value="<?php echo $rst_enc[ped_vendedor] ?>" readonly/>
                                </td>
                            </tr>
                            <?php
                            if ($emisor == 1 || $emisor == 10) {
                                ?>
                                <tr>
                                    <td>#PAGOS</td>
                                    <td><input type="number" style="width:70px;text-align: center; " min="1" max="4" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" id="opg_codigo" value="<?php echo $rst['opg_codigo'] ?>" onchange="pago(this)" lang="1" /><input type="text" size="10" readonly value="MIN.1 MAX.4"/></td>
                                </tr>
                                <tr>
                                    <td align="center">PORCENTAJE</td>
                                    <td align="center">DIAS</td>
                                    <td align="center">VALOR</td>
                                    <td align="center">FECHA</td>
                                </tr>
                                <?php
                                $n = 0;
                                while ($rst_pag = pg_fetch_array($cns_pag)) {
                                    $n++;
                                    ?>

                                    <tr id="pagos" >
                                        <td id="p<?php echo $n ?>"><input type="text" id="pag<?php echo $n ?>" class="itme" value="<?php echo $rst_pag['pag_porcentage'] ?>" lang="<?PHP echo $n ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_pago(this)"/>%</td>
                                        <td id="d<?php echo $n ?>"><input type="text" id="dias<?php echo $n ?>" value="<?php echo $rst_pag['pag_dias'] ?>" lang="<?PHP echo $n ?>"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_fecha(this)"/></td>
                                        <td id="v<?php echo $n ?>"><input type="text" id="valor<?php echo $n ?>" value="<?php echo $rst_pag['pag_valor'] ?>" lang="<?PHP echo $n ?>" readonly/></td>
                                        <td id="f<?php echo $n ?>"><input type="text" id="fecha<?php echo $n ?>" value="<?php echo $rst_pag['pag_fecha_v'] ?>" lang="<?PHP echo $n ?>" readonly/></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <?php
                            } else {
                                ?>
                                <tr>
                                    <td>FORMA</td>
                                    <td>NUM DOC PAGO</td>
                                    <td>BANCO</td>
                                    <td>TARJETA</td>
                                    <td>PAGO</td>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CANTIDAD</td>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="pago_forma1" lang="1" onblur="habilitar(this), busqueda_ntscre(this)" onchange="verificar_cuenta(this)">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                            <option value="8">NOTA CREDITO</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" id="num_nota_credito1" lang="1" disabled >
                                        <input type="hidden" size="6" id="id_nota_credito1" lang="1">
                                        <input type="hidden" size="6" id="val_nt_cre1" lang="1">
                                    </td>
                                    <td>
                                        <select id="pago_banco1" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">Banco Pichincha</option>
                                            <option value="2">Banco del Pacífico</option>
                                            <option value="3">Banco de Guayaquil</option>
                                            <option value="4">Produbanco</option>
                                            <option value="5">Banco Bolivariano</option>
                                            <option value="6">Banco Internacional</option>
                                            <option value="7">Banco del Austro</option>
                                            <option value="8">Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                            <option value="9">Banco de Machala</option>
                                            <option value="10">BGR</option>
                                            <option value="11">Citibank (Ecuador)</option>
                                            <option value="12">Banco ProCredit (Ecuador)</option>
                                            <option value="13">UniBanco</option>
                                            <option value="14">Banco Solidario</option>
                                            <option value="15">Banco de Loja</option>
                                            <option value="16">Banco Territorial</option>
                                            <option value="17">Banco Coopnacional</option>
                                            <option value="18">Banco Amazonas</option>
                                            <option value="19">Banco Capital</option>
                                            <option value="20">Banco D-MIRO</option>
                                            <option value="21">Banco Finca</option>
                                            <option value="22">Banco Comercial de Manabí</option>
                                            <option value="23">Banco COFIEC</option>
                                            <option value="24">Banco del Litoral</option>
                                            <option value="25">Banco Delbank</option>
                                            <option value="26">Banco Sudamericano</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_tarjeta1" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">VISA</option>
                                            <option value="2">MASTER CARD</option>
                                            <option value="3">AMERICAN EXPRESS</option>
                                            <option value="4">DINNERS</option>
                                            <option value="5">DISCOVER</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_contado1" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">Contado</option>
                                            <option value="2">3 meses</option>
                                            <option value="3">6 meses</option>
                                            <option value="4">9 meses</option>
                                            <option value="5">12 meses</option>
                                            <option value="6">18 meses</option>
                                            <option value="7">36 meses</option>
                                        </select>
                                    </td>
                                    <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad1" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="1" disabled/></td>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="pago_forma2" lang="2" onblur="habilitar(this), busqueda_ntscre(this)" onchange="verificar_cuenta(this)">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                            <option value="8">NOTA CREDITO</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" id="num_nota_credito2" lang="2" disabled >
                                        <input type="hidden" size="6" id="id_nota_credito2" lang="2">
                                        <input type="hidden" size="6" id="val_nt_cre2" lang="2">
                                    </td>
                                    <td>
                                        <select id="pago_banco2" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">Banco Pichincha</option>
                                            <option value="2">Banco del Pacífico</option>
                                            <option value="3">Banco de Guayaquil</option>
                                            <option value="4">Produbanco</option>
                                            <option value="5">Banco Bolivariano</option>
                                            <option value="6">Banco Internacional</option>
                                            <option value="7">Banco del Austro</option>
                                            <option value="8">Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                            <option value="9">Banco de Machala</option>
                                            <option value="10">BGR</option>
                                            <option value="11">Citibank (Ecuador)</option>
                                            <option value="12">Banco ProCredit (Ecuador)</option>
                                            <option value="13">UniBanco</option>
                                            <option value="14">Banco Solidario</option>
                                            <option value="15">Banco de Loja</option>
                                            <option value="16">Banco Territorial</option>
                                            <option value="17">Banco Coopnacional</option>
                                            <option value="18">Banco Amazonas</option>
                                            <option value="19">Banco Capital</option>
                                            <option value="20">Banco D-MIRO</option>
                                            <option value="21">Banco Finca</option>
                                            <option value="22">Banco Comercial de Manabí</option>
                                            <option value="23">Banco COFIEC</option>
                                            <option value="24">Banco del Litoral</option>
                                            <option value="25">Banco Delbank</option>
                                            <option value="26">Banco Sudamericano</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_tarjeta2" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">VISA</option>
                                            <option value="2">MASTER CARD</option>
                                            <option value="3">AMERICAN EXPRESS</option>
                                            <option value="4">DINNERS</option>
                                            <option value="5">DISCOVER</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_contado2" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">Contado</option>
                                            <option value="2">3 meses</option>
                                            <option value="3">6 meses</option>
                                            <option value="4">9 meses</option>
                                            <option value="5">12 meses</option>
                                            <option value="6">18 meses</option>
                                            <option value="7">36 meses</option>
                                        </select>
                                    </td>
                                    <td align="right" ><input type="text" style="text-align:right" size="15" id="pago_cantidad2" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="2" disabled/></td>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="pago_forma3" lang="3" onblur="habilitar(this), busqueda_ntscre(this)" onchange="verificar_cuenta(this)">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                            <option value="8">NOTA CREDITO</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" id="num_nota_credito3" lang="3" disabled >
                                        <input type="hidden" size="6" id="id_nota_credito3" lang="3">
                                        <input type="hidden" size="6" id="val_nt_cre3" lang="3">
                                    </td>
                                    <td>
                                        <select id="pago_banco3" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">Banco Pichincha</option>
                                            <option value="2">Banco del Pacífico</option>
                                            <option value="3">Banco de Guayaquil</option>
                                            <option value="4">Produbanco</option>
                                            <option value="5">Banco Bolivariano</option>
                                            <option value="6">Banco Internacional</option>
                                            <option value="7">Banco del Austro</option>
                                            <option value="8">Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                            <option value="9">Banco de Machala</option>
                                            <option value="10">BGR</option>
                                            <option value="11">Citibank (Ecuador)</option>
                                            <option value="12">Banco ProCredit (Ecuador)</option>
                                            <option value="13">UniBanco</option>
                                            <option value="14">Banco Solidario</option>
                                            <option value="15">Banco de Loja</option>
                                            <option value="16">Banco Territorial</option>
                                            <option value="17">Banco Coopnacional</option>
                                            <option value="18">Banco Amazonas</option>
                                            <option value="19">Banco Capital</option>
                                            <option value="20">Banco D-MIRO</option>
                                            <option value="21">Banco Finca</option>
                                            <option value="22">Banco Comercial de Manabí</option>
                                            <option value="23">Banco COFIEC</option>
                                            <option value="24">Banco del Litoral</option>
                                            <option value="25">Banco Delbank</option>
                                            <option value="26">Banco Sudamericano</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_tarjeta3" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">VISA</option>
                                            <option value="2">MASTER CARD</option>
                                            <option value="3">AMERICAN EXPRESS</option>
                                            <option value="4">DINNERS</option>
                                            <option value="5">DISCOVER</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_contado3" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">Contado</option>
                                            <option value="2">3 meses</option>
                                            <option value="3">6 meses</option>
                                            <option value="4">9 meses</option>
                                            <option value="5">12 meses</option>
                                            <option value="6">18 meses</option>
                                            <option value="7">36 meses</option>
                                        </select>
                                    </td>
                                    <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad3" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="3" disabled/></td>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="pago_forma4" lang="4" onblur="habilitar(this), busqueda_ntscre(this)" onchange="verificar_cuenta(this)">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                            <option value="8">NOTA CREDITO</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" id="num_nota_credito4" lang="4" disabled >
                                        <input type="hidden" size="6" id="id_nota_credito4" lang="4">
                                        <input type="hidden" size="6" id="val_nt_cre4" lang="4">
                                    </td>
                                    <td>
                                        <select id="pago_banco4" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">Banco Pichincha</option>
                                            <option value="2">Banco del Pacífico</option>
                                            <option value="3">Banco de Guayaquil</option>
                                            <option value="4">Produbanco</option>
                                            <option value="5">Banco Bolivariano</option>
                                            <option value="6">Banco Internacional</option>
                                            <option value="7">Banco del Austro</option>
                                            <option value="8">Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                            <option value="9">Banco de Machala</option>
                                            <option value="10">BGR</option>
                                            <option value="11">Citibank (Ecuador)</option>
                                            <option value="12">Banco ProCredit (Ecuador)</option>
                                            <option value="13">UniBanco</option>
                                            <option value="14">Banco Solidario</option>
                                            <option value="15">Banco de Loja</option>
                                            <option value="16">Banco Territorial</option>
                                            <option value="17">Banco Coopnacional</option>
                                            <option value="18">Banco Amazonas</option>
                                            <option value="19">Banco Capital</option>
                                            <option value="20">Banco D-MIRO</option>
                                            <option value="21">Banco Finca</option>
                                            <option value="22">Banco Comercial de Manabí</option>
                                            <option value="23">Banco COFIEC</option>
                                            <option value="24">Banco del Litoral</option>
                                            <option value="25">Banco Delbank</option>
                                            <option value="26">Banco Sudamericano</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_tarjeta4" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">VISA</option>
                                            <option value="2">MASTER CARD</option>
                                            <option value="3">AMERICAN EXPRESS</option>
                                            <option value="4">DINNERS</option>
                                            <option value="5">DISCOVER</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_contado4" disabled>
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">Contado</option>
                                            <option value="2">3 meses</option>
                                            <option value="3">6 meses</option>
                                            <option value="4">9 meses</option>
                                            <option value="5">12 meses</option>
                                            <option value="6">18 meses</option>
                                            <option value="7">36 meses</option>
                                        </select>
                                    </td>
                                    <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad4" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="4" disabled/></td>
                                </tr>
                                <tr>
                                    <td colspan="4" ></td>
                                    <td align="right">Faltante</td>
                                    <td align="right"><input type="text" style="text-align:right" readonly id="t_pagos" name="t_pagos" size="13" value="0"  /></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                    </td>
                <tr><td colspan="2">
                        <table  id="tbl_dinamic" lang="0">
                            <thead>
                            <th>Item</th>
                            <th>CODIGO</th>
                            <th>DESCRIPCION</th>
                            <th>LOTE</th>
                            <th>INVENTARIO</th>
                            <th>COD.AUXILIAR</th>
                            <th>SOLICITADO</th>
                            <th>ENTREGADO</th>
                            <th>SALDO</th>
                            <th>PRECIO</th>
                            <th>DESCUENTO%</th>
                            <th>DESCUENTO $</th>
                            <th>IVA</th>
                            <th>VALOR TOTAL</th>
                            <!--<th>ACCIONES</th>-->
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    $n = 0;
                                    while ($rst_det = pg_fetch_array($cns_det)) {
                                        $n++;
                                        $rst_pro = pg_fetch_array($Set->lista_un_producto_noperti_cod_lote($rst_det['det_cod_producto'], $rst_det['det_lote']));
                                        switch ($rst_det['det_unidad']) {
                                            case 1:
                                                $unidad = 'ROLLO';
                                                break;
                                            case 2:
                                                $unidad = 'KG';
                                                break;
                                            case 3:
                                                $unidad = 'M';
                                                break;
                                            case 4:
                                                $unidad = 'UNIDAD';
                                                break;
                                        }
                                        if (empty($rst_pro)) {
                                            $rst_pro = pg_fetch_array($Set->lista_un_producto_industrial($rst_det['det_cod_producto']));
                                            $uni_pro_reg = pg_fetch_array($Set->lista_unidad_pro_indus($rst_det[pro_id]));
                                            $uni_pro = $uni_pro_reg[pro_uni];
                                            //////////////// CALCULOS DE UNIDADES PARA LAS CANTIDADES DEL INVENTARIO .... DE LA FACTURACION DE LAS ORDENES DE PEDIDO DE VENTA INDUSTRIAL 
                                            //Producto
                                            $rollo = 1;
                                            $mts = 100; //Largo producto
                                            $kg = 27; //Peso producto
                                            if ($uni_pro_reg[pro_uni] == $unidad) {
                                                $cantid = $rst_det['det_cantidad'];
                                            } else if ($uni_pro_reg[pro_uni] == 'ROLLO' && $unidad == 'M') {
                                                $cantid = $rst_det['det_cantidad'] * $rollo / $mts;
                                            } else if ($uni_pro_reg[pro_uni] == 'ROLLO' && $unidad == 'KG') {
                                                $cantid = $rst_det['det_cantidad'] * $rollo / $kg;
                                            } else if ($uni_pro_reg[pro_uni] == 'KG' && $unidad == 'ROLLO') {
                                                $cantid = $rst_det['det_cantidad'] * $kg / $rollo;
                                            } else if ($uni_pro_reg[pro_uni] == 'KG' && $unidad == 'M') {
                                                $cantid = $rst_det['det_cantidad'] * $kg / $mts;
                                            } else if ($uni_pro_reg[pro_uni] == 'M' && $unidad == 'ROLLO') {
                                                $cantid = $rst_det['det_cantidad'] * $mts / $rollo;
                                            } else if ($uni_pro_reg[pro_uni] == 'M' && $unidad == 'KG') {
                                                $cantid = $rst_det['det_cantidad'] * $mts / $kg;
                                            } else {
                                                $cantid = $rst_det['det_cantidad'];
                                            }
                                        } else {
                                            $uni_pro_comer = pg_fetch_array($Set->lista_un_producto_noperti_id($rst_det[pro_id]));
                                            $uni_pro = 'UNIDAD';
                                            $cantid = $rst_det['det_cantidad'];
                                        }
                                        $rst_inv = pg_fetch_array($Set->total_ingreso_egreso_fac($rst_det[pro_id], $emisor, $rst_det[det_tab]));
                                        $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                                        $rst_ent = pg_fetch_array($Docs->sum_entregado($id, $rst_det[pro_id], $rst_det[det_tab]));
                                        if ($rst_ent[suma] == '') {
                                            $rst_ent[suma] = 0;
                                        }
                                        $saldo = $rst_det['det_cantidad'] - $rst_ent[suma];
                                        ?>
                                    <tr>
                                        <td><input type ="text" size="4" class="itm" id="<?PHP echo 'item' . $n ?>"  lang="<?PHP echo $n ?>" readonly value="<?PHP echo $n ?>"/></td>
                                        <td><input type="text" size="20" id="<?php echo 'pro_descripcion' . $n ?>" value="<?php echo $rst_det['det_cod_producto'] ?>" lang="<?PHP echo $n ?>" maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" readonly /></td>
                                        <td>
                                            <input type ="text" size="35"  id="<?php echo 'pro_referencia' . $n ?>"  value="<?php echo $rst_det['det_descripcion'] ?>" lang="<?PHP echo $n ?>" readonly/>
                                            <input type ="text" size="15"  id="<?php echo 'pro_id' . $n ?>"  value="<?php echo $rst_det[pro_id] ?>" lang="1" hidden/>
                                            <input type ="text" size="15"  id="<?php echo 'tabla' . $n ?>"  value="<?php echo $rst_det[det_tab] ?>" lang="1" hidden/>
                                        </td>
                                        <td><input type ="text" size="10"  id="<?php echo 'lote' . $n ?>"  value="<?php echo $rst_det['det_lote'] ?>" lang="<?PHP echo $n ?>" readonly/></td>
                                        <td><input type ="text" size="7"  id="<?php echo 'inventario' . $n ?>"  value="<?php echo $inv ?>" lang="1" readonly/></td>
                                        <td>
                                            <input type ="text" size="15"  id="<?php echo 'codaux' . $n ?>"  value="<?php echo $rst_det['det_cod_auxiliar'] ?>" lang="<?PHP echo $n ?>" readonly/>
                                            <input type="hidden" size="4" id="<?php echo 'uni_pro_reg' . $n ?>" value="<?php echo $uni_pro ?>" />
                                        </td>
                                        <td>
                                            <input type ="text" size="7"  id="<?php echo 'solicitado' . $n ?>"  value="<?php echo $rst_det['det_cantidad'] ?>" lang="<?PHP echo $n ?>" readonly />
                                            <input type="hidden" size="4" id="<?php echo 'uni_pedido' . $n ?>" value="<?php echo $unidad ?>">
                                            <input type="hidden" size="8" id="<?php echo 'cant_conv_uni' . $n ?>" value="<?php echo $cantid ?>">
                                        </td>
                                        <td><input type ="text" size="7"  id="<?php echo 'entregado' . $n ?>"  value="<?php echo $rst_ent[suma] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                        <td><input type ="text" size="7"  id="<?php echo 'cantidad' . $n ?>"  value="<?php echo $saldo ?>" lang="<?PHP echo $n ?>" onblur=" inventario(this), solicitado(this), calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" /></td>
                                        <td><input type ="text" size="7"  id="<?php echo 'pro_precio' . $n ?>"  value="<?php echo $rst_det['det_vunit'] ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)" readonly /></td>
                                        <td>
                                            <input type ="text" size="7"  id="<?php echo 'descuento' . $n ?>"  value="<?php echo $rst_det['det_descuento_porcentaje'] ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)" readonly />
                                        </td>
                                        <td>
                                            <input type ="text" size="7"  id="<?php echo 'descuent' . $n ?>"  value="<?php echo $rst_det['det_descuento_moneda'] ?>" lang="<?PHP echo $n ?>"  readonly />
                                        </td>

                                        <td><input type="text" id="<?php echo 'iva' . $n ?>" size="5" value="<?php echo $rst_det['det_impuesto'] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                        <td><input type ="text" size="9"  id="<?php echo 'valor_total' . $n ?>"  value="<?php echo number_format($rst_det['det_total'], 4) ?>" lang="1" readonly lang="<?PHP echo $n ?>"/></td>
                                        <!--<td onclick="elimina_fila(this)" ><img class="auxBtn" width="16px" src="../img/b_delete.png" /></td>-->
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Observaciones:</td>
                                </tr>
                                <tr>
                                    <td valign="top" rowspan="7" colspan="8"><textarea id="observacion" style="width:100%; text-transform: uppercase;" onkeydown="return enter(event)"><?php echo $observacion2 ?></textarea></td>    
                                    <td colspan="5" align="right">Sub Total 14%:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotal12" value="<?php echo number_format($rst_enc['ped_sbt12'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="5" align="right">Sub Total 0%:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotal0" value="<?php echo number_format($rst_enc['ped_sbt0'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="5" align="right">Sub Total Excento de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalex" value="<?php echo number_format($rst_enc['ped_sbt_excento'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="5" align="right">Sub Total no objeto de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalno" value="<?php echo number_format($rst_enc['ped_sbt_noiva'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="5" align="right">Total Descuento:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_descuento" value="<?php echo number_format($rst_enc['ped_tdescuento'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="5" align="right">Total IVA:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_iva" value="<?php echo number_format($rst_enc['ped_iva12'], 4) ?>" readonly /></td>
                                </tr>
                                <tr>
                                    <td colspan="5" align="right">Total Valor:</td>
                                    <td colspan="2"><input style="text-align:right;font-size:15px;color:red  " type="text" size="12" id="total_valor" value="<?php echo number_format($rst_enc['ped_total'], 4) ?>" readonly /></td>
                                </tr>

                            </tfoot>
                        </table></td></tr>
                <tfoot>
                    <tr>
                        <td colspan="2"><button id="save" onclick="frm_save.lang = 1" >FACTURAR</button></td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>    
<script>
    n = 0;
<?php
while ($rts_combos = pg_fetch_array($cns_pagos)) {
    ?>
        tarjeta =<?php echo $rts_combos[pag_tarjeta] ?>;
        forma =<?php echo $rts_combos[pag_forma] ?>;
        banco =<?php echo $rts_combos[pag_banco] ?>;
        cant =<?php echo $rts_combos[pag_cant] ?>;
        n++;
        $('#pago_tarjeta' + n).val(tarjeta);
        $('#pago_forma' + n).val(forma);
        $('#pago_banco' + n).val(banco);
        $('#pago_cantidad' + n).val(cant);
        habilitar(n);
    <?php
}
?>
</script>
<datalist id="productos">
    <?php
    $cns_pro = $Set->lista_producto_total($emisor);
    $n = 0;
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        $n++;
        ?>
        <option value="<?php echo $rst_pro[tbl] . $rst_pro[id] ?>" label="<?php echo $rst_pro[lote] . ' ' . $rst_pro[codigo] . ' ' . $rst_pro[descripcion] ?>" />
        <?php
    }
    ?>
</datalist>
