<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_factura.php';
$Set = new Clase_factura();
$emisor = $_GET[emisor];
if ($emisor >= 10) {
    $ems = '0' . $emisor;
} else {
    $ems = '00' . $emisor;
}
$t = $_GET[txt];
$d = $_GET[desde];
$h = $_GET[hasta];

if (isset($_GET[id])) {
    if ($_SESSION[usuid] == 1) {
        $read = '';
    } else {
        $read = 'readonly';
    }
    $id = $_GET[id];
    $user = $rst_user[usu_id];
    $cns_vend = $Set->lista_vendedores($user);
    $rst = pg_fetch_array($Set->lista_una_factura_id($id));
    $vnd = strtoupper($rst[vnd_nombre]);
    $rst[num_secuencial] = $rst[fac_numero];
    $cns_pagos = $Set->lista_detalle_pagos($rst[fac_id]);
    $num_pagos = pg_num_rows($Set->lista_detalle_pagos($rst[fac_id]));
    $rst['opg_codigo'] = $num_pagos;
    $det = 1;
    $id_vnd = $rst[vnd_id];
} else {
    $read = '';
    $rst_sec = pg_fetch_array($Set->lista_secuencial_documento($ems));
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
    $rst[fac_numero] = $ems . '-001-' . $tx . $sec;
    $rst[fac_fecha_emision] = date('Y-m-d');
    $id = 0;
    $rst[fac_id] = 0;
    $rst[cli_id] = 0;
    $user = $rst_user[usu_id];
    $cns_vend = $Set->lista_vendedores($user);
    $rst_vend = pg_fetch_array($Set->lista_vendedores($user));
    $vnd = $rst_vend[vnd_nombre];
    $id_vnd = $rst_vend[vnd_id];
    $num_pagos = 0;
    $det = 0;
    $rst['opg_codigo'] = 1;

    $rst[cli_pais] = 'ECUADOR';
    if ($emisor == 4 || $emisor == 6 || $emisor == 7 || $emisor == 8) {
        $ciudad = 'GUAYAQUIL';
    } else if ($emisor == 5) {
        $ciudad = 'MACHALA';
    } else {
        $ciudad = 'QUITO';
    }
    $rst[cli_canton] = $ciudad;
}
if ($emisor == 1 || $emisor == 10) {
    $readonly = '';
} else {
    $readonly = 'readonly';
}
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
            user = '<?php echo $user ?>';
            vnd = '<?php echo $vnd ?>';
            idvnd = '<?php echo $id_vnd ?>';
            $(function () {
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    if (this.lang == 1) {
                        save(id);
                    } else if (this.lang == 0) {
                        var tr = $('#tbl_form').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        if ($('#pro_descripcion' + a).val().length != 0 && $('#cantidad' + a).val().length != 0 && $('#pro_precio' + a).val() != 0) {
                            clona_fila('#tbl_form');
                        }
                    }
                });
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
                data = Array(
                        emi,
                        cli_id.value,
                        vendedor.value,
                        '0', //ped_id,
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
                        subtotal.value.replace(',', ''), //subtotal
                        vnd,
                        '0'
                        );

                var data2 = Array();
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                while (n < i) {
                    n++;
                    if ($('#pro_descripcion' + n).val() != null) {
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
                        tab = $('#tab' + n).val();

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
                                tab
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
                        if (id != 0) {
                            if (num_secuencial.value != '<?php echo $rst[num_secuencial] ?>') {
                                $("#num_secuencial").css({borderColor: "red"});
                                $("#num_secuencial").focus();
                                alert('No se puede modificar el numero secuencial');
                                $('#save').attr('disabled', false);
                                return false;
                            }
                        }
                        if (num_secuencial.value.length == 0) {
                            $("#num_secuencial").css({borderColor: "red"});
                            $("#num_secuencial").focus();
                            $('#save').attr('disabled', false);
                            return false;
                        } else if (cod_punto_emision.value.length == 0) {
                            $("#cod_punto_emision").css({borderColor: "red"});
                            $("#cod_punto_emision").focus();
                            $('#save').attr('disabled', false);
                            return false;
                        } else if (identificacion.value.length == 0) {
                            $("#identificacion").css({borderColor: "red"});
                            $("#identificacion").focus();
                            $('#save').attr('disabled', false);
                            return false;
                        } else if (nombre.value.length == 0) {
                            $("#nombre").css({borderColor: "red"});
                            $("#nombre").focus();
                            $('#save').attr('disabled', false);
                            return false;
                        } else if (direccion_cliente.value.length == 0) {
                            $("#direccion_cliente").css({borderColor: "red"});
                            $("#direccion_cliente").focus();
                            $('#save').attr('disabled', false);
                            return false;
                        } else if (telefono_cliente.value.length == 0) {
                            $("#telefono_cliente").css({borderColor: "red"});
                            $("#telefono_cliente").focus();
                            $('#save').attr('disabled', false);
                            return false;
                        } else if (email_cliente.value.length == 0) {
                            $("#email_cliente").css({borderColor: "red"});
                            $("#email_cliente").focus();
                            $('#save').attr('disabled', false);
                            return false;
                        }
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#pro_descripcion' + n).val() != null) {
                                    if ($('#pro_descripcion' + n).val() == 0) {
                                        $('#pro_descripcion' + n).css({borderColor: "red"});
                                        $('#pro_descripcion' + n).focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                    else if ($('#cantidad' + n).val() == 0) {
                                        $('#cantidad' + n).css({borderColor: "red"});
                                        $('#cantidad' + n).focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                    else if ($('#descuento' + n).val().length == 0) {
                                        $('#descuento' + n).css({borderColor: "red"});
                                        $('#descuento' + n).focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                    else if ($('#pro_precio' + n).val() == 0) {
                                        $('#pro_precio' + n).css({borderColor: "red"});
                                        $('#pro_precio' + n).focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }

                                }
                            }
                        }
                        if ($('#total_valor').val() > 200 && $('#nombre').val() == 'CONSUMIDOR FINAL') {
                            alert('PARA CONSUMIDOR FINAL EL VALOR TOTAL NO PUDE SER MAYOR $200');
                            $('#save').attr('disabled', false);
                            return false;
                        }


                        if ($('#vendedor').val() == '0') {
                            $('#vendedor').css({borderColor: "red"});
                            $('#vendedor').focus();
                            $('#save').attr('disabled', false);
                            return false;
                        }
                        if (emi == 1 || emi == 10) {
                            if ($('#opg_codigo').val().length == 0) {
                                $('#opg_codigo').css({borderColor: "red"});
                                $('#opg_codigo').focus();
                            }
                            if (pag.length != 0) {
                                while (j < pag.length) {
                                    j++;
                                    if ($('#pag' + j).val() == 0) {
                                        $('#pag' + j).css({borderColor: "red"});
                                        $('#opg_codigo').focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                    else if ($('#dias' + j).val() == 0) {
                                        $('#dias' + j).css({borderColor: "red"});
                                        $('#dias' + j).focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                    else if ($('#valor' + j).val().length == 0) {
                                        $('#valor' + j).css({borderColor: "red"});
                                        $('#valor' + j).focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                    else if ($('#fecha' + j).val().length == 0) {
                                        $('#fecha' + j).css({borderColor: "red"});
                                        $('#fecha' + j).focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                }
                            }
                        }
                        else if (emi != 1 && emi != 10) {
                            if (id == 0) {
                                if (idvnd == '') {
                                    alert('Este Usuario no existe en la Tabla Vendedor \n Debe crear un Vendedor con este Usuario');
                                    if ($('#vendedor').val().length == 0) {
                                        $('#vendedor').css({borderColor: "red"});
                                        $('#vendedor').focus();
                                        $('#save').attr('disabled', false);
                                        return false;
                                    }
                                }
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
                        loading('visible');

                    }
                    ,
                    type: 'POST',
                    url: 'actions_factura_nuevo.php',
                    data: {op: 2, 'data[]': data, 'data2[]': data2, 'data3[]': data3, id: id, 'fields[]': fields, usu: user},
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            asientos(dat[2], dat[1], dat[3]);
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

            function asientos(sms, fac_id) {
                $.ajax({
                    beforeSend: function () {
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: 0, id: fac_id, x: det},
                    success: function (dt) {

                        loading('hidden');
                        if (dt == 0) {
                            if (sms == '') {
                                if (emi == 1 || emi == 10) {
                                    auxWindow(0, fac_id);
                                } else {
                                    auxWindow(1, fac_id);
                                }
                            }
                        } else {
                            alert(dt);
                        }

                    }
                });
            }


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function load_cliente(obj) {
                if (obj.value.length <= 7) {
                    alert('Debe escribir al menos 8 caracteres ');
                    $('#identificacion').val('');
                    $('#identificacion').focus();
                } else {
                    $.post("actions_factura_nuevo.php", {op: 0, id: obj.value, s: 0},
                    function (dt) {
                        if (dt != 0) {
                            $('#con_clientes').css('visibility', 'visible');
                            $('#con_clientes').show();
                            $('#clientes').html(dt);
                        } else {
                            alert('Cliente no existe \n Cree uno Nuevo??');
                            $('#nombre').focus();
                            $('#nombre').val('');
                            $('#direccion_cliente').val('');
                            $('#telefono_cliente').val('');
                            $('#email_cliente').val('');
                            $('#cli_parroquia').val('');
                            $('#cli_ciudad').val('');
                            $('#cli_pais').val('');
                            $('#cli_id').val('0');
                        }
                    });
                }
            }

            function load_cliente2(obj) {
                $.post("actions_factura_nuevo.php", {op: 0, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Cree uno Nuevo??');
                        $('#nombre').focus();
                        $('#identificacion').val('');
                        $('#nombre').val('');
                        $('#direccion_cliente').val('');
                        $('#telefono_cliente').val('');
                        $('#email_cliente').val('');
                        $('#cli_parroquia').val('');
                        $('#cli_ciudad').val('');
                        $('#cli_pais').val('');
                        $('#cli_id').val('0');
                    } else {
                        dat = dt.split('&');
                        if (dat[10] == 0) {
                            $('#identificacion').val(dat[0]);
                            $('#nombre').val(dat[1]);
                            $('#direccion_cliente').val(dat[2]);
                            $('#telefono_cliente').val(dat[3]);
                            $('#email_cliente').val(dat[4]);
                            $('#cli_parroquia').val(dat[5]);
                            $('#cli_ciudad').val(dat[6]);
                            $('#cli_pais').val(dat[7]);
                            $('#cli_id').val(dat[8]);
                        } else {
                            alert('El Cliente esta Inactivo o Suspendido');
                            $('#identificacion').focus();
                            $('#identificacion').val('');
                            $('#nombre').val('');
                            $('#direccion_cliente').val('');
                            $('#telefono_cliente').val('');
                            $('#email_cliente').val('');
                            $('#cli_parroquia').val('');
                            $('#cli_ciudad').val('');
                            $('#cli_pais').val('');
                            $('#cli_id').val('0');
                        }
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
                var sb = 0;
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
                    } else {
                        cnt = $('#cantidad' + n).val().replace(',', '');
                        pr = $('#pro_precio' + n).val().replace(',', '');
                        d = $('#descuento' + n).val().replace(',', '');
                        if (d > 100) {
                            de = 0;
                            alert('El descuento no puede ser mayor a 100%');
                            $('#descuento' + n).css({borderColor: "red"});
                            $('#descuento' + n).val(0);
                            $("#descuent" + n).val(de.toFixed(4));
                            $('#descuento' + n).focus();
                            calculo();
                        } else {
                            vtp = cnt * pr; //Valor total parcial
                            vt = (vtp * 1) - (vtp * d / 100);
                            $('#descuent' + n).val((vtp * d / 100).toFixed(4));
                            $('#valor_total' + n).val(vt.toFixed(4));
                            ob = $('#iva' + n).val();
                            val = $('#valor_total' + n).val().replace(',', '');
                            d = $('#descuent' + n).val().replace(',', '');
                        }
                    }

                    tdsc = (tdsc * 1) + (d * 1);
                    if (ob == '14') {
                        t12 = (t12 * 1 + val * 1);
                    }
                    if (ob == '12') {

                            alert('IVA 12% favor contactar al departamento de sistemas' );
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
                sb = t12 + t0 + tex + tno;
                tiva = (t12 * 14 / 100);
                gtot = (t12 * 1 + t0 * 1 + tex * 1 + tno * 1 + tiva * 1);
                $('#subtotal12').val(t12.toFixed(4));
                $('#subtotal0').val(t0.toFixed(4));
                $('#subtotalex').val(tex.toFixed(4));
                $('#subtotalno').val(tno.toFixed(4));
                $('#subtotal').val(sb.toFixed(4));
                $('#total_descuento').val(tdsc.toFixed(4));
                $('#total_iva').val(tiva.toFixed(4));
                $('#total_valor').val(gtot.toFixed(4));
                if (emi == 1 || emi == 10) {
                    calculo_pago();
                } else {
                    $('#pago_cantidad1').val(gtot.toFixed(4));
                    calculo_pago_locales();
                }

            }
            function calculo_pago_locales() {
                n = 0;
                while (n < 4) {
                    n++;
                    val = $('#val_nt_cre' + n).val();
                    if ($('#pago_forma' + n).val() == 7 || $('#pago_forma' + n).val() == 8 || $('#pago_forma' + n).val() == 3) {
                        if (parseFloat($('#val_nt_cre' + n).val()) < parseFloat($('#pago_cantidad' + n).val())) {
                            alert('Cantidad es mayor a el Valor del docuemento de pago: ' + val);
                            $('#pago_cantidad' + n).val(val);
                        }
                    }
                }
                tp = parseFloat(pago_cantidad1.value) + parseFloat(pago_cantidad2.value) + parseFloat(pago_cantidad3.value) + parseFloat(pago_cantidad4.value);
                flt = parseFloat(total_valor.value.replace(',', '')) - tp.toFixed(4);
                if (flt.toFixed(4) < 0) {
                    alert('Valor ingresado incorrecto');
                } else {
                    t_pagos.value = flt.toFixed(4);
                }

            }
            function cancelar() {
                t = '<?php echo $t ?>';
                d = '<?php echo $d ?>';
                h = '<?php echo $h ?>';
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php?txt=' + t + '&desde=' + d + '&hasta=' + h;
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
                n = obj.lang;
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
//                    $('#pago_cantidad' + s).val('0');
                }
                calculo_pago_locales();
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
                $('.itm').each(function () {
                    pro = $('#pro_id' + this.value).val();
                    pro2 = $('#pro_descripcion' + j).val().substring(1);
                    $('#pro_descripcion' + j).css({borderColor: ""});
                    if (pro2 == pro) {
                        alert('Producto ya ingresado');
                        vl = '';
                        $('#pro_descripcion' + j).focus();
                        return false;
                    }
                });

                $.post("actions_factura_nuevo.php", {op: 1, id: vl, lt: lt, s: emi},
                function (dt) {
                    dat = dt.split('&');
                    $('#pro_descripcion' + j).val(dat[0]);
                    $('#pro_referencia' + j).val(dat[1]);
                    $('#iva' + j).val(dat[4]);
                    $('#descuent' + j).val(0);
                    $('#codaux' + j).val(dat[7]);
                    $('#lote' + j).val(dat[8]); ///comentar para codigo ean
                    $('#pro_id' + j).val(dat[9]);
                    $('#tab' + j).val(dat[10]);
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


            function inventario(obj) {
                n = obj.lang;
                if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
                    alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    calculo();
                }
            }

            function confirmar() {
                $('#save').attr('disabled', true);
                if (confirm('Desea guardar la factura') == true) {
                    save(id);
                } else {
                    $('#save').attr('disabled', false);
                    return false;

                }
            }

            function validar_email(valor)
            {
                var filter = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
                if (filter.test(valor))
                    return true;
                else
                    return false;
            }

            function mail_validado() {
                if ($("#email_cliente").val() == '')
                {
                    alert("Ingrese un email");
                } else if (validar_email($("#email_cliente").val()))
                {

                } else
                {
                    alert("El email no es valido");
                    $('#email_cliente').css({borderColor: "red"});
                    $('#email_cliente').val('');
                    $('#email_cliente').focus();
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

            function limpliar_ruc() {
                $('#identificacion').val('');
                $('#identificacion').focus();
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
                text-transform: uppercase;
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
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'; limpliar_ruc()">&#X00d7;</font><br>
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
                    <td colspan="2">
                        <table>
                            <tr>
                                <td>Factura N:</td>
                                <td>
                                    <input type="text" size="20" id="num_secuencial" readonly value="<?php echo $rst[fac_numero] ?>" />
                                    <input type="hidden" id="cod_punto_emision" value="<?php echo $emisor ?>" />
                                    <input type="hidden" id="fac_id" value="<?php echo $rst[fac_id] ?>" />
                                </td>
                                <td>Fecha:</td>
                                <td>
                                    <input type="text" size="10" id="fecha_emision" readonly value="<?php echo $rst[fac_fecha_emision] ?>" <?php echo $read ?> />
                                    <img src="../img/calendar.png" id="im-fecha_emision" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td><table id='tbl_colum2' border="0" ><tr class="trthead"><td  colspan="2" style="background:#00557F ;color:white " align='center' ><label class="tdtitulo">CLIENTE:</label></td></tr>
                            <tr>
                                <td style="width:80px ">RUC/CC:</td>
                                <td><input type="text" size="45"  id="identificacion" value="<?php echo $rst[fac_identificacion] ?>" onchange="load_cliente(this)" onkeyup="this.value = this.value.replace(/[^a-zA-Z0-9]/, '')"  maxlength="13" <?php echo $read ?>/>
                                    <input type="hidden" size="10"  id="cli_id" value="<?php echo $rst[cli_id] ?>" /></td>
                            </tr>
                            <tr>
                                <td>NOMBRE:</td>
                                <td><input type="text"  size="45" id="nombre"  value="<?php echo $rst[fac_nombre] ?>" placeholder="NOMBRES Y APELLIDOS"  <?php echo $read ?>/></td>
                            </tr>
                            <tr>
                                <td>DIRECCION:</td>
                                <td><input type="text"  size="45" id="direccion_cliente"  value="<?php echo $rst[fac_direccion] ?>" <?php echo $read ?> /></td>
                            </tr>
                            <tr>
                                <td>TELEFONO:</td>
                                <td><input type="text"   size="45"  id="telefono_cliente"  value="<?php echo $rst[fac_telefono] ?>"  <?php echo $read ?>/></td>
                            </tr>
                            <tr>
                                <td>EMAIL:</td>
                                <td><input type="text"   size="45"  id="email_cliente"  value="<?php echo $rst[fac_email] ?>"  onchange="mail_validado()" style="text-transform:lowercase " <?php echo $read ?>/></td>
                            </tr>
                            <tr>
                                <td>PARROQUIA:</td>
                                <td><input type="text"  size="45"  id="cli_parroquia"  value="<?php echo $rst[cli_parroquia] ?>"  <?php echo $read ?>/></td>
                            </tr>
                            <tr>
                                <td>CIUDAD:</td>
                                <td><input type="text"  size="45"  id="cli_ciudad"  value="<?php echo $rst[cli_canton] ?>"  <?php echo $read ?>/></td>
                            </tr>
                            <tr>
                                <td>PAIS:</td>
                                <td><input type="text"  size="45"  id="cli_pais"  value="<?php echo $rst[cli_pais] ?>"  <?php echo $read ?>/></td>
                            </tr></table></td>
                    <td valign="top">
                        <table width='100%' id='tbl_colum3' border="0">
                            <td class="trthead" colspan="6" align='center' style="background:#00557F ;color:white " >
                                <label  class="tdtitulo">FORMAS DE PAGO</label>
                            </td>
                            <?php
                            if ($emisor == 1 || $emisor == 10) {
                                ?>
                                <tr>
                                    <td>
                                        <table>
                                            <tr>
                                                <td style="width: 100px">Vendedor:</td>
                                                <td colspan="2">
                                                    <select id="vendedor">
                                                        <option value="0">SELECCIONE</option>;
                                                        <?php
                                                        while ($rst_vend = pg_fetch_array($cns_vend)) {
                                                            echo "<option value='$rst_vend[vnd_id]'>$rst_vend[vnd_nombre]</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>

                                            <?php
                                        } else {
                                            ?>
                                            <tr>
                                                <td>Vendedor:
                                                </td>
                                                <td>
                                                    <select id="vendedor" disabled>
                                                        <?php
                                                        echo "<option value='$id_vnd'>$vnd</option>";
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        if ($emisor == 1 || $emisor == 10) {
                                            ?>
                                            <tr>
                                                <td>#PAGOS</td>
                                                <td colspan="2"><input type="number" style="width:70px;text-align: center; " min="1" max="4" id="opg_codigo" value="<?php echo $rst['opg_codigo'] ?>" onchange="pago(this)" lang="1" /> <input type="text" size="12" readonly value="MIN.1 MAX.4"/></td>
                                            </tr>
                                            <tr>
                                                <td align="left" style="width: 128px">PORCENTAJE </td>
                                                <td align="left" style="width: 118px">DIAS</td>
                                                <td align="left" style="width: 134px">VALOR</td>
                                                <td align="left" style="width: 135px">FECHA</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <?php
                                if ($num_pagos == 0) {
                                    ?>
                                    <tr id="pagos"  >
                                        <td>
                                            <table id ="pag">
                                                <tr>
                                                    <td id="p1"><input type="text" id="pag1" class="itme" value="<?php echo $rst['pag'] ?>" lang="1" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_pago(this)"/>%</td>
                                                    <td id="d1"><input type="text" id="dias1" value="<?php echo $rst['dias'] ?>" lang="1"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_fecha(this)"/></td>
                                                    <td id="v1"><input type="text" id="valor1" value="<?php echo $rst['valor_pago'] ?>" lang="1" readonly/></td>
                                                    <td id="f1"><input type="text" id="fecha1" value="<?php echo $rst['fecha_pago'] ?>" lang="<?PHP echo $n ?>" readonly/></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <?php
                                } else {
                                    while ($rst_pag = pg_fetch_array($cns_pagos)) {
                                        $n++;
                                        ?>
                                        <tr id="pagos" >
                                            <td>
                                                <table id ="pag">
                                                    <tr>
                                                        <td id="p<?php echo $n ?>"><input type="text" id="pag<?php echo $n ?>" class="itme" value="<?php echo $rst_pag['pag_porcentage'] ?>" lang="1" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_pago(this)"/>%</td>
                                                        <td id="d<?php echo $n ?>"><input type="text" id="dias<?php echo $n ?>" value="<?php echo $rst_pag['pag_dias'] ?>" lang="1"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_fecha(this)"/></td>
                                                        <td id="v<?php echo $n ?>"><input type="text" id="valor<?php echo $n ?>" value="<?php echo $rst_pag['pag_valor'] ?>" lang="1" readonly/></td>
                                                        <td id="f<?php echo $n ?>"><input type="text" id="fecha<?php echo $n ?>" value="<?php echo $rst_pag['pag_fecha_v'] ?>" lang="<?PHP echo $n ?>" readonly/></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <?php
                                    }
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
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CANTIDAD</td>
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
                                            <option value="6">CUOTAFACIL</option>
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
                                    <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad1" value="0" onchange="calculo_pago_locales(this), pag_sig(this)" lang="1" disabled/></td>
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
                                            <option value="6">CUOTAFACIL</option>
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
                                            <option value="6">CUOTAFACIL</option>
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
                                            <option value="6">CUOTAFACIL</option>
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
                                <script>
                                    s = 0;
    <?php
    while ($rts_combos = pg_fetch_array($cns_pagos)) {
        ?>
                                        s++;
                                        forma = '<?php echo $rts_combos[pag_forma] ?>';
                                        tarjeta = '<?php echo $rts_combos[pag_tarjeta] ?>';
                                        banco = '<?php echo $rts_combos[pag_banco] ?>';
                                        cant = '<?php echo $rts_combos[pag_cant] ?>';
                                        cont = '<?php echo $rts_combos[pag_contado] ?>';
                                        $('#pago_forma' + s).val(forma);
                                        $('#pago_tarjeta' + s).val(tarjeta);
                                        $('#pago_banco' + s).val(banco);
                                        $('#pago_cantidad' + s).val(cant);
                                        $('#pago_contado' + s).val(cont);

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
                                            $('#pago_contado' + s).val('0');
                                            $('#pago_contado' + s).attr('disabled', true);
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
        <?php
    }
    ?>
                                    calculo_pago_locales();
                                </script>
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
                            <th>CANTIDAD</th>
                            <th>PRECIO</th>
                            <th>DESCUENTO%</th>
                            <th>DESCUENTO $</th>
                            <th>IVA</th>
                            <th>VALOR TOTAL</th>
                            <th>ACCIONES</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    $n = 0;

                                    if ($emisor == 1 || $emisor == 10) {
                                        $readOnly = "";
                                    } else {
                                        $readOnly = "readOnly";
                                    }

                                    $cns_det = $Set->lista_detalle_factura($rst[fac_id]);
                                    if (pg_num_rows($cns_det) == 0) {
                                        ?>
                                    <tr>
                                        <td><input type ="text" size="4" class="itm" id="item1"  readonly value="1" lang="1"/></td>
                                        <td>
                                            <input type="text" size="25" id="pro_descripcion1"  value="" lang="1"   maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2" />
                                        </td>
                                        <td>
                                            <input type ="text" size="40" class="refer"  id="pro_referencia1"   value="" lang="1" readonly style="width:300px;height:20px;font-size:11px;font-weight:100 "  />
                                            <input type ="hidden" size="15"  id="pro_id1"  value="" lang="1"/>
                                            <input type ="hidden" size="15"  id="tab1"  value="" lang="1"/>
                                        </td>
                                        <td><input type ="text" size="10"  id="lote1"  value="" lang="1" maxlength="10" onkeypress="caracter(event, this, 1)"/></td>
                                        <td><input type ="text" size="7"  id="inventario1"  value="" lang="1" readonly /></td>
                                        <td><input type ="text" size="15"  id="codaux1"  value="" lang="1" readonly/></td>
                                        <td><input type ="text" size="7"  id="cantidad1"  value="" lang="1" onchange="calculo(this), inventario(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" /></td>
                                        <td><input type ="text" size="7"  id="pro_precio1"  onchange="calculo(this)" value="" lang="1" <?php echo $readOnly ?> onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" /></td>
                                        <td><input type ="text" size="7"  id="descuento1"  value="" lang="1" onchange="calculo(this)"  <?php echo $readOnly ?> onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" /></td>
                                        <td><input type ="text" size="7"  id="descuent1"  value="" lang="1" readonly  /></td>
                                        <td><input type="text" id="iva1" size="5" value="" readonly /></td>
                                        <td>
                                            <input type ="text" size="9"  id="valor_total1"  value="" lang="1" readonly />
                                        </td>
                                        <td onclick="elimina_fila(this)" ><img class="auxBtn" width="16px" src="../img/b_delete.png" /></td>
                                    </tr>
                                    <?php
                                } else {
                                    while ($rst_det = pg_fetch_array($cns_det)) {
                                        $n++;
                                        $pro_id = $rst_det[pro_id];
                                        $tab = $rst_det[dfc_tab];
                                        $rst_inv = pg_fetch_array($Set->total_ingreso_egreso_fac($pro_id, $emisor, $tab));
                                        $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                                        ?>
                                        <tr>
                                            <td><input type ="text" size="4" class="itm" id="<?PHP echo 'item' . $n ?>"  lang="<?PHP echo $n ?>" readonly value="<?PHP echo $n ?>"/></td>
                                            <td><input type="text" size="25" id="<?php echo 'pro_descripcion' . $n ?>" value="<?php echo $rst_det[dfc_codigo] ?>" lang="<?PHP echo $n ?>"  maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2" <?PHP echo $read ?> />
                                            <td>
                                                <input type ="text" size="40"  id="<?php echo 'pro_referencia' . $n ?>"  value="<?php echo $rst_det[dfc_descripcion] ?>" lang="<?PHP echo $n ?>" readonly/>
                                                <input type ="hidden" size="15"  id="<?php echo 'pro_id' . $n ?>"  value="<?php echo $rst_det[pro_id] ?>" lang="1"/>
                                                <input type ="hidden" size="15"  id="<?php echo 'tab' . $n ?>"  value="<?php echo $rst_det[dfc_tab] ?>" lang="1"/>
                                            </td>
                                            <td><input type ="text" size="10"  id="<?php echo 'lote' . $n ?>"  value="<?php echo $rst_det[dfc_lote] ?>" lang="<?PHP echo $n ?>" <?PHP echo $read ?> /></td>
                                            <td><input type ="text" size="7"  id="<?php echo 'inventario' . $n ?>"  value="<?php echo $inv ?>" lang="1" readonly/></td>
                                            <td><input type ="text" size="15"  id="<?php echo 'codaux' . $n ?>"  value="<?php echo $rst_det[dfc_cod_aux] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                            <td><input type ="text" size="7"  id="<?php echo 'cantidad' . $n ?>"  value="<?php echo str_replace(',', '', $rst_det[dfc_cantidad]) ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)" <?PHP echo $read ?> onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                                            <td><input type ="text" size="7"  id="<?php echo 'pro_precio' . $n ?>"  value="<?php echo str_replace(',', '', $rst_det[dfc_precio_unit]) ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)"  <?PHP echo $read ?> onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" /></td>
                                            <td>
                                                <input type ="text" size="7"  id="<?php echo 'descuento' . $n ?>"  value="<?php echo $rst_det[dfc_porcentaje_descuento] ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)" <?PHP echo $read ?> onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                                            </td>
                                            <td>
                                                <input type ="text" size="7"  id="<?php echo 'descuent' . $n ?>"  value="<?php echo str_replace(',', '', $rst_det[dfc_val_descuento]) ?>" lang="<?PHP echo $n ?>"  readonly />
                                            </td>

                                            <td><input type="text" id="<?php echo 'iva' . $n ?>" size="5" value="<?php echo $rst_det[dfc_iva] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                            <td><input type ="text" size="9"  id="<?php echo 'valor_total' . $n ?>"  value="<?php echo number_format($rst_det[dfc_precio_total], 4) ?>" lang="1" readonly lang="<?PHP echo $n ?>"/></td>
                                            <?php
                                            if ($read == '') {
                                                ?>
                                                <td onclick="elimina_fila(this)" ><img class="auxBtn" width="16px" src="../img/b_delete.png" /></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        <?php
                                        if ($read == '') {
                                            ?>
                                            <button id="add_row" onclick="frm_save.lang = 0" >+</button>
                                            <?php
                                        }
                                        ?>
                                    </td>

                                </tr>
                                <tr>
                                    <td>Observaciones:</td>
                                </tr>
                                <tr>

                                    <td valign="top" rowspan="7" colspan="8"><textarea id="observacion" style="width:100%; text-transform: uppercase;" onkeydown="return enter(event)" <?PHP echo $read ?>><?php echo $rst[fac_observaciones] ?></textarea></td>    
                                    <td colspan="3" align="right">Sub Total 14%:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotal12" value="<?php echo str_replace(',', '', number_format($rst[fac_subtotal12], 4)) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right">Sub Total 0%:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotal0" value="<?php echo str_replace(',', '', number_format($rst[fac_subtotal0], 4)) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right">Sub Total Excento de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalex" value="<?php echo str_replace(',', '', number_format($rst[fac_subtotal_ex_iva], 4)) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right">Sub Total no objeto de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalno" value="<?php echo str_replace(',', '', number_format($rst[fac_subtotal_no_iva], 4)) ?>" readonly/></td>
                                    <td><input style="text-align:right" type="hidden" size="12" id="subtotal" value="<?php echo str_replace(',', '', number_format($rst[fac_subtotal], 4)) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right">Total Descuento:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_descuento" value="<?php echo str_replace(',', '', number_format($rst[fac_total_descuento], 4)) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right">Total IVA:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_iva" value="<?php echo str_replace(',', '', number_format($rst[fac_total_iva], 4)) ?>" readonly /></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right">Total Valor:</td>
                                    <td><input style="text-align:right;font-size:15px;color:red  " type="text" size="12" id="total_valor" value="<?php echo str_replace(',', '', number_format($rst[fac_total_valor], 4)) ?>" readonly /></td>
                                </tr>

                            </tfoot>
                        </table></td></tr>
                <tfoot>
                    <tr>
                        <td colspan="2"><button id="save" onclick="confirmar()" >FACTURAR</button></td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>    
<?php
echo "<datalist id='productos'>";
$cns_pro = $Set->lista_producto_total($emisor);
//$x = 0;
while ($rst_pro = pg_fetch_array($cns_pro)) {
//    $x++;
    echo "<option value='$rst_pro[tbl]$rst_pro[id]'> $rst_pro[lote] $rst_pro[codigo] $rst_pro[descripcion] </option>";
}
echo "</datalist>";
?>
<script>
    n = 0;
    vend = '<?php echo $id_vnd ?>';
    $('#vendedor').val(vend);
</script>


