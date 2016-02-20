<?php
//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ord_pedido_venta.php';
$Docs = new Clase_ord_pedido_venta();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst_enc = pg_fetch_array($Docs->lista_un_registro($id));
    $cns_det = $Docs->lista_detalle_registro_pedido($id);
    $cns_pag = $Docs->lista_pagos_registro_pedido($id);
    $num_doc = $rst_enc[ped_num_registro];
} else {
    $id = 0;

    $txt = '000000000';
    $rst = pg_fetch_array($Docs->lista_ultimo_registro());
    $num_doc = $rst[ped_num_registro];
    $num_doc = intval($num_doc + 1);
    $num_doc = substr($txt, 0, (10 - strlen($num_doc))) . $num_doc;


    $rst[det_cantidad] = 0;
    $rst[ped_sbt12] = 0;
    $rst[ped_sbt0] = 0;
    $rst[ped_sbt_noiva] = 0;
    $rst[ped_sbt_excento] = 0;
    $rst[ped_sbt] = 0;
    $rst[ped_tdescuento] = 0;
    $rst[ped_ice] = 0;
    $rst[ped_irbpnr] = 0;
    $rst[ped_iva12] = 0;
    $rst[ped_propina] = 0;
    $rst[ped_total] = 0;
    $rst_enc[ped_desc_asolicitar] = 0;
    $rst_enc[ped_vendedor] = $rst_user[usu_person];
    $rst_enc[ped_femision] = date('Y-m-d');
    $rst_pag[pag_fecha_v] = date('Y-m-d');
}
$cns = $Docs->lista_bodegas();
?>
<!doctype html>
<html class="ui-mobile">
    <head>
        <meta charset='utf-8'>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Menu</title>
        <link href="../menu/files/jquery.css" rel="stylesheet">
        <link href="../css/style_movil.css" rel="stylesheet">
        <script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
        <script>
            $(function () {
                $('input').attr('readOnly', 'readOnly');
                $('#usu_id').val(<?php echo $rst[usu_id] ?>);

                $('#frm_detalle').submit(function (e) {
                    e.preventDefault();
                    clona_fila($("#tbl_detalle"));
                });
                $('#frm_fpagos').submit(function (e) {
                    e.preventDefault();
                    clona_fila($("#tbl_fpagos"));
                });

                $('#cancel').click(function () {
                    cancelar();
                    return false;
                });
                $('#con_clientes').hide();
                parent.document.getElementById('contenedor2').rows = "*,90%";
                Calendar.setup({inputField: "ped_femision", ifFormat: "%Y-%m-%d", button: "im-reg_femision"});

                posicion_aux_window();
            });
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }

            function cerrar_ventana() {
                $('#con_clientes').hide();
            }

            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input,select").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    this.lang = x;
                    this.checked = false;
                    var parent = $(this).parents();
                    $(parent[1]).css('background-color', 'transparent');
                    if (parts[1] == 'item') {
                        this.value = x;
                    } else {
                        this.value = '';
                    }
                    return parts[1] + x;
                });
                $(table).find("tbody tr:last").after(tr);
            }
            function elimina_fila(obj, tbl) {
                if (tbl == 0) {
                    tb = "#tbl_fpagos";
                } else {
                    tb = "#tbl_detalle";
                }
                itm = $(tb + ' .itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
                calculo_total_pago();

            }
            function save(id) {
                ///**********encabezado*****************
                var data = Array(
                        ped_femision.value,
                        ped_num_registro.value,
                        ped_local.value,
                        ped_vendedor.value,
                        ped_ruc_cc_cliente.value,
                        ped_nom_cliente.value,
                        ped_dir_cliente.value,
                        ped_tel_cliente.value,
                        ped_email_cliente.value,
                        ped_parroquia_cliente.value,
                        ped_ciu_cliente.value,
                        ped_pais_cliente.value,
                        ped_sbt12.value,
                        ped_sbt0.value,
                        ped_sbt_noiva.value,
                        ped_sbt_excento.value,
                        ped_sbt.value,
                        ped_tdescuento.value,
                        ped_ice.value,
                        ped_irbpnr.value,
                        ped_iva12.value,
                        ped_propina.value,
                        ped_total.value,
                        ped_observacion.value.toUpperCase(),
                        cli_id.value,
                        tipo_cliente.value);
///**********detalle*****************                        
                det = $('#tbl_detalle .itm');
                var detalle = Array();
                ndet = 1;
                while (ndet <= det.length) {
                    detalle.push($('#det_cod_producto' + ndet).val() + '&' +
                            $('#det_lote' + ndet).val() + '&' +
                            $('#det_cod_auxiliar' + ndet).val() + '&' +
                            $('#det_descripcion' + ndet).val() + '&' +
                            $('#det_cantidad' + ndet).val() + '&' +
                            $('#det_vunit' + ndet).val() + '&' +
                            $('#det_descuento_porcentaje' + ndet).val() + '&' +
                            $('#det_descuento_moneda' + ndet).val() + '&' +
                            $('#det_total' + ndet).val() + '&' +
                            $('#det_impuesto' + ndet).val()
                            );
                    ndet++;
                }
//**********pagos*****************                                        
                pag = $('#tbl_fpagos .itm');
                var pagos = Array();
                npag = 1;
                while (npag <= pag.length) {
                    pagos.push($('#pag_porcentage' + npag).val() + '&' +
                            $('#pag_dias' + npag).val() + '&' +
                            $('#pag_valor' + npag).val() + '&' +
                            $('#pag_fecha_v' + npag).val()
                            );
                    npag++;
                }

                $.ajax({
                    beforeSend: function () {

                        var tr = $('#tbl_form').find("tbody tr:last");
                        a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        pag = document.getElementsByClassName('itme');
                        n = 0;
                        j = 0;

                        return_v = 0;
                        if (ped_local.value == '0') {
                            $('#ped_local').focus();
                            $('#ped_local').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (ped_ruc_cc_cliente.value.length == 0) {
                            $('#ped_ruc_cc_cliente').focus();
                            $('#ped_ruc_cc_cliente').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#det_cod_producto' + n).val() != null) {
                                    if ($('#det_cod_producto' + n).val() == 0) {
                                        $('#det_cod_producto' + n).css({borderColor: "red"});
                                        $('#det_cod_producto' + n).focus();
                                        return false;
                                    }
                                    else if ($('#det_cantidad' + n).val() == 0) {
                                        $('#det_cantidad' + n).css({borderColor: "red"});
                                        $('#det_cantidad' + n).focus();
                                        return false;
                                    }

                                }
                            }
                        }

                        if (return_v == 1) {
                            return false;
                        } else {
                            return true;
                        }
                    },
                    type: 'POST',
                    url: "../Scripts/actions_ord_pedido_venta.php",
                    data: {op: 0, 'data[]': data, 'detalle[]': detalle, 'pagos[]': pagos, id: id},
                    success: function (dt) {
                        if (dt == 0) {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }

                    }
                });
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
                        $('#ped_nom_cliente').focus();
                    }
                });
            }

            function load_cliente2(obj) {
                $.post("actions.php", {act: 63, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Cree uno Nuevo??');
                        $('#ped_nom_cliente').focus();
                    } else {
                        dat = dt.split('&');
                        $('#ped_ruc_cc_cliente').val(dat[0]);
                        $('#ped_nom_cliente').val(dat[1]);
                        $('#ped_dir_cliente').val(dat[2]);
                        $('#ped_tel_cliente').val(dat[3]);
                        $('#ped_email_cliente').val(dat[4]);
                        $('#ped_parroquia_cliente').val(dat[5]);
                        $('#ped_ciu_cliente').val(dat[6]);
                        $('#ped_pais_cliente').val(dat[7]);
                        $('#cli_id').val(dat[8]);
                        $('#tipo_cliente').val(dat[9]);
                    }
                    $('#con_clientes').hide();
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

            function calculo_fecha(obj) {
                n = obj.lang;
                var sumarDias = parseInt($('#pag_dias' + n).val());
                var fecha = $('#ped_femision').val();
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
                $('#pag_fecha_v' + n).val(anio + "-" + mes + "-" + dia);
            }
            function calculo_total_pago() {
                var t = 0;
                var tp = 0;
                obj = $("#tbl_fpagos .itm ");
                total = $("#ped_total").val();
                n = 1;
                while (n <= obj.length) {
                    por = $("#pag_porcentage" + n).val();
                    vpago = (por * total / 100);
                    $("#pag_valor" + n).val(vpago.toFixed(3));
                    t += ($("#pag_valor" + n).val() * 1);
                    tp += ($("#pag_porcentage" + n).val() * 1);
                    n++;
                }
                $("#pg_total").val(t.toFixed(2));
                $("#pg_por").val(tp.toFixed(2));
            }

            function calculo(obj) {
                n = obj.lang;
                c = $("#det_cantidad" + n).val();
                v = $("#det_vunit" + n).val();
                dp = $("#det_descuento_porcentaje" + n).val();
                tp = (c * v);
                d = (tp * dp / 100);
                t = (c * v) - d;
                $("#det_descuento_moneda" + n).val(d.toFixed(4));
                $("#det_total" + n).val(t.toFixed(4));
                calculo_totales();
            }
            function calculo_totales() {
                obj = $("#tbl_detalle .itm ");
                n = 1;
                sbt12 = 0;
                sbt0 = 0;
                sbtno = 0;
                sbtex = 0;
                desc = 0;
                while (n <= obj.length) {
                    desc += ($("#det_descuento_moneda" + n).val() * 1)

                    switch ($("#det_impuesto" + n).val()) {
                        case '12':
                            sbt12 += ($("#det_total" + n).val() * 1);
                            break;
                        case '0':
                            sbt0 += ($("#det_total" + n).val() * 1);
                            break;
                        case 'NO':
                            sbtno += ($("#det_total" + n).val() * 1);
                            break;
                        case 'EX':
                            sbtex += ($("#det_total" + n).val() * 1);
                            break;
                    }
                    n++;
                }
                sbt = (sbt12 + sbt0 + sbtno + sbtex);
                iva = (sbt12 * 0.12);
                gtot = (sbt + iva + ($("#ped_ice").val() * 1) + ($("#ped_irbpnr").val() * 1) + ($("#ped_propina").val() * 1));
                $("#ped_sbt12").val(sbt12.toFixed(4));
                $("#ped_sbt0").val(sbt0.toFixed(4));
                $("#ped_sbt_noiva").val(sbtno.toFixed(4));
                $("#ped_sbt_excento").val(sbtex.toFixed(4));
                $("#ped_sbt").val(sbt.toFixed(4));
                $("#ped_tdescuento").val(desc.toFixed(4));
                $("#ped_iva12").val(iva.toFixed(4));
                $("#ped_total").val((gtot * 1).toFixed(4));
                calculo_total_pago();
            }

            function asg_autocomplete(obj) {
                $(obj).autocomplete({source: productos});

            }

            function list_productos(obj) {
                id = obj.value;
                if (id == 1 || id == 10) {
                    rdn = false;
                } else {
                    rdn = true;
                }

                $('.pg_input').each(function () {
                    var pts = this.id.match(/(\D+)(\d+)$/);
                    if (pts[1] == 'pag_porcentage') {
                        this.readOnly = rdn;
                    }
                    if (pts[1] == 'pag_dias') {
                        this.readOnly = rdn;
                    }
                })

                $('.dt_input').each(function () {
                    var pts = this.id.match(/(\D+)(\d+)$/);
                    if (pts[1] == 'det_vunit') {
                        this.readOnly = rdn;
                    }
                })
                $.post('../Scripts/actions_ord_pedido_venta.php', {op: 2, id: id}, function (dt) {
                    $('#productos').html(dt);
                })

            }

            function caracter(e, obj, x) {
                j = obj.lang;
                var ch0 = e.keyCode;
                var ch1 = e.which;
                if (ch0 == 0 && ch1 == 46 && x == 0) { //Punto (Con lector de Codigo de Barras)

                    $('#det_descripcion' + j).focus();

                    $(obj).autocomplete({
                        minLength: 0,
                        source: ''
                    });


                } else if (ch0 == 9 && ch1 == 0 && x == 0) { //Tab (Sin lector de Codigo de Barras)
                    $('#det_descripcion' + j).focus();
                    v = 0;
                    load_producto(j, v);
                } else if (x == 1 && obj.value.length > 8) {//Desde lote
                    $('#det_cantidad' + j).focus();
                    v = 1;
                    load_producto(j, v);
                }
            }

            function load_producto(j, v) {
                if (v == 1) {
                    vl = $('#det_cod_producto' + j).val();
                    lt = $('#det_lote' + j).val();
                } else {
                    vl = $('#det_cod_producto' + j).val();
                    lt = 0;
                }
                $.post("actions.php", {act: 64, id: vl, lt: lt, s: ped_local.value},
                function (dt) {
                    dat = dt.split('&');
                    $('#det_cod_producto' + j).val(dat[0]);
                    $('#det_descripcion' + j).val(dat[1]);
                    $('#det_impuesto' + j).val(dat[4]);
                    $('#det_descuento_moneda' + j).val(0);
                    $('#det_lote' + j).val(dat[8]); ///comentar para codigo ean

                    if (dat[7] == '') {
                        $('#det_cod_auxiliar' + j).val('');
                    } else {
                        $('#det_cod_auxiliar' + j).val(dat[7]);
                    }

                    if (dat[3] == '') {
                        $('#det_vunit' + j).val(0);
                        $('#det_impuesto' + j).val('12');
                    } else {
                        $('#det_vunit' + j).val(dat[3]);
                    }

                    if (dat[5] == '') {
                        $('#det_descuento_porcentaje' + j).val(0);
                    } else {
                        $('#det_descuento_porcentaje' + j).val(dat[5]);
                    }
                    calculo('1');
                });
            }

            function aprobar(act, id, ped) {
                main = parent.document.getElementById('mainFrame');
                if (act == 1) {
                    sms = confirm("Se Aprobara el pedido " + ped + " \n Desea Continuar?");
                    sts = 1;
                } else if (act == 0) {
                    sms = confirm("No se aprobara el pedido " + ped + " \n Desea Continuar?");
                    sts = 2;
                }
                if (sms == true) {
                    $.post("../Scripts/actions_ord_pedido_venta.php", {op: 4, id: id, sts: sts}, function (dt) {
                        if (dt == 0) {
                            window.location = '../Scripts_Movil/Lista_aut_pedido_venta.php';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }

            function asientos(d1) {

                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: 0, id: d1, x: 1},
                    success: function (dt) {
                        if (dt[0] == 0) {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }

                    }
                });
            }
            function back() {
                window.location = 'Lista_aut_pedido_venta.php';
            }

        </script>
        <style>
            input[type=text]{
                text-transform:uppercase !important; 
                background:#f8f8f8;
                border:solid 1px #ccc; 
            }
            *{
                font-size:8px; 
            }
            img{
                width:8px;  
            }
            tr{
                height:auto !important; 
            }
            tr td{
                background:#f8f8f8;
            }
        </style>
    </head>
    <body class="ui-mobile-viewport ui-overlay-c">
        <div class="ui-page ui-body-c ui-page-header-fixed ui-page-active" data-role="page" data-url="/iOS-Inspired-jQuery-Mobile-Theme/" tabindex="0" style="min-height: 912px;"></div>
        <div id="Gerencial" class="ui-page ui-body-c ui-page-header-fixed ui-page-active" data-role="page" data-url="Gerencial" tabindex="0" style="padding-top: 44px; min-height: 912px;">
            <div class="ui-header ui-bar-a ui-header-fixed slidedown" data-position="fixed" data-role="header" role="banner">
                <h1 class="ui-title" role="heading" aria-level="1"></h1>
                <a class="ui-btn-left ui-btn ui-shadow ui-btn-corner-all ui-btn-up-a" data-theme="a" data-rel="back" href="#" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span">
                    <span class="ui-btn-inner ui-btn-corner-all">
                        <span class="ui-btn-text" onclick="back()">Back</span>
                    </span>
                </a>
            </div>
            <table id="" cellpadding="0"  border="0"   >
                <thead>
                    <tr>
                        <th colspan="5" >   
                            <?PHP echo "FORMULARIO ORDENES DE PEDIDO DE VENTA" ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2">
                            <form id="frm_fencabezado" autocomplete="off">
                                <table id="tbl_fencabezado"  border="0">
                                    <tbody>
                                        <tr>
                                            <td>Orden</td>
                                            <td><input type="text" size="10" id="ped_num_registro" value="<?php echo $num_doc ?>" readonly /></td>
                                            <td>Fecha</td>
                                            <td>
                                                <input type="text" size="10" id="ped_femision" value="<?php echo $rst_enc[ped_femision] ?>" />
                                            </td>
                                            <td>Local</td>
                                            <td>
                                                <select id="ped_local" onchange="list_productos(this)" style="width:70px "> 
                                                    <option value="0">Seleccione</option>
                                                    <?php
                                                    while ($rst_locales = pg_fetch_array($cns)) {
                                                        echo "<option value='$rst_locales[cod_punto_emision]'>$rst_locales[nombre_comercial]</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>Vendedor</td>
                                            <td><input type="text" size="30" id="ped_vendedor" value="<?php echo $rst_enc[ped_vendedor] ?>" /></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form> 
                        </td>
                    </tr>
                    <tr>
                        <td  rowspan="0" valign="top" align="left"  >
                            <form id="frm_fcliente" autocomplete="off">
                                <table id="tbl_fcliente"  border="0">
                                    <thead>
                                        <tr><th colspan="2">Cliente</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>RUC/CC:</td>
                                            <td><input type="text" id="ped_ruc_cc_cliente" size="25" onchange="load_cliente(this)" value="<?php echo $rst_enc[ped_ruc_cc_cliente] ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td>NOMBRE:</td>
                                            <td><input type="text" id="ped_nom_cliente" size="25" value="<?php echo $rst_enc[ped_nom_cliente] ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td>DIRECCION:</td>
                                            <td><input type="text" id="ped_dir_cliente" size="25" value="<?php echo $rst_enc[ped_dir_cliente] ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td>TELEFONO:</td>
                                            <td><input type="text" id="ped_tel_cliente" size="25" value="<?php echo $rst_enc[ped_tel_cliente] ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td>EMAIL:</td>
                                            <td><input type="text" id="ped_email_cliente" size="25" value="<?php echo $rst_enc[ped_email_cliente] ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td>PARROQUIA:</td>
                                            <td><input type="text" id="ped_parroquia_cliente" size="25" value="<?php echo $rst_enc[ped_parroquia_cliente] ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td>CIUDAD:</td>
                                            <td><input type="text" id="ped_ciu_cliente" size="25" value="<?php echo $rst_enc[ped_ciu_cliente] ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td>PAIS:</td>
                                            <td><input type="text" id="ped_pais_cliente" size="25" value="<?php echo $rst_enc[ped_pais_cliente] ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" id="cli_id" size="3" value="<?php echo $rst_enc[cli_id] ?>" hidden/></td>
                                            <td><input type="text" id="tipo_cliente" size="3" value="<?php echo $rst_enc[tipo_cliente] ?>" hidden/></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form> 
                        </td>
                        <td  rowspan="0" valign="top" align="left"  >
                            <form id="frm_fpagos" autocomplete="off">
                                <table id="tbl_fpagos"  border="0">
                                    <thead>
                                        <tr><th colspan="6">Formas de Pago</th></tr>
                                        <tr>
                                            <th>No</th>
                                            <th>%</th>
                                            <th>Días</th>
                                            <th>Valor</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (pg_num_rows($cns_pag) == 0) {
                                            ?>
                                            <tr>
                                                <td align="right">
                                                    <input type="text" size="2" class="itm" id="item1" name="item1"  value="1" lang="1" readonly style="text-align:right"/>
                                                </td>
                                                <td><input class="pg_input" type="text" size="3" id="pag_porcentage1" name="pag_porcentage1" lang="1" value="0" maxlength="4" onkeyup="calculo_total_pago()"/></td>
                                                <td><input class="pg_input" type="text" size="7" id="pag_dias1" name="pag_dias1" lang="1" value="0" onchange="calculo_fecha(this)" /></td>
                                                <td><input class="pg_input" type="text" size="7" id="pag_valor1" name="pag_valor1" lang="1" value="0" onkeyup="calculo_total_pago()" style="text-align:right" readonly /></td>
                                                <td>
                                                    <input class="pg_input" type="date" size="3" id="pag_fecha_v1" name="pag_fecha_v1" lang="1"  value="<?php echo $rst_pag[pag_fecha_v] ?>" readonly />
                                                </td>
                                            </tr>
                                            <?php
                                        } else {
                                            $npg = 0;
                                            while ($rst_pag = pg_fetch_array($cns_pag)) {
                                                $pag_total+=$rst_pag[pag_valor];
                                                $pag_por+=$rst_pag[pag_porcentage];
                                                $npg++;
                                                ?>
                                                <tr>
                                                    <td align="right"><input type="text" size="2" class="itm" id="<?php echo 'item' . $npg ?>" name="<?php echo 'item' . $npg ?>"   value="<?php echo $npg ?>" lang="<?php echo $npg ?>" readonly style="text-align:right"/></td>
                                                    <?php
                                                    if ($rst_pag[ped_local] == 1 || $rst_pag[ped_local] == 10) {
                                                        ?>
                                                        <td><input class="pg_input" type="text" size="3" id="<?php echo 'pag_porcentage' . $npg ?>" name="<?php echo 'pag_porcentage' . $npg ?>" value="<?php echo $rst_pag[pag_porcentage] ?>" lang="<?php echo $npg ?>" maxlength="4" onkeyup="calculo_total_pago()"/></td>
                                                        <td><input class="pg_input" type="text" size="7" id="<?php echo 'pag_dias' . $npg ?>" name="<?php echo 'pag_dias' . $npg ?>" value="<?php echo $rst_pag[pag_dias] ?>" lang="<?php echo $npg ?>" onchange="calculo_fecha(this)"/></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td><input class="pg_input" type="text" size="3" id="<?php echo 'pag_porcentage' . $npg ?>" name="<?php echo 'pag_porcentage' . $npg ?>" value="<?php echo $rst_pag[pag_porcentage] ?>" lang="<?php echo $npg ?>" maxlength="4" onkeyup="calculo_total_pago()" readonly/></td>
                                                        <td><input class="pg_input" type="text" size="7" id="<?php echo 'pag_dias' . $npg ?>" name="<?php echo 'pag_dias' . $npg ?>" value="<?php echo $rst_pag[pag_dias] ?>" lang="<?php echo $npg ?>" onchange="calculo_fecha(this)" readonly/></td>
                                                        <?php
                                                    }
                                                    ?>
                                                    <td><input class="pg_input" type="text" size="7" id="<?php echo 'pag_valor' . $npg ?>" name="<?php echo 'pag_valor' . $npg ?>" value="<?php echo $rst_pag[pag_valor] ?>" lang="<?php echo $npg ?>" onkeyup="calculo_total_pago()" style="text-align:right" readonly/></td>
                                                    <td>
                                                        <input class="pg_input" type="date" size="10" id="<?php echo 'pag_fecha_v' . $npg ?>" name="<?php echo 'pag_fecha_v' . $npg ?>" value="<?php echo $rst_pag[pag_fecha_v] ?>" lang="<?php echo $npg ?>" readonly />
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="7"></td>
                                        </tr>
                                        <tr>
                                            <td>Total:</td>
                                            <td><input type="text" size="7" value="<?php echo round($pag_por) ?>" id="pg_por" style="text-align:right" readonly/></td>
                                            <td></td>
                                            <td><input type="text" size="7" value="<?php echo round($pag_total, 2) ?>" id="pg_total" style="text-align:right" readonly/></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </form> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <form id="frm_detalle" autocomplete="off">
                                <table border="0" align="left" id="tbl_detalle" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Cod</th>
                                            <th>Lote</th>
                                            <th>Descripcion</th>
                                            <th>Cant</th>
                                            <th>V.U</th>
                                            <th>Desc%</th>
                                            <th>Total</th>
                                            <th>Imp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (pg_num_rows($cns_det) == 0) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <input class="dt_input" type="text" size="15" id="det_cod_producto1" name="det_cod_producto1"  value=" " lang="1"   maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2" />
                                                </td>
                                                <td><input class="dt_input" type="text" size="10" id="det_lote1" name="det_lote1" value="" lang="1"/></td>
                                                <td><input class="dt_input" type="text" size="30" id="det_descripcion1" name="det_descripcion1" value="" lang="1"  style="font-size:9px;height:20px; "/></td>
                                                <td><input class="dt_input" type="text" size="3" id="det_cantidad1" name="det_cantidad1" value="" lang="1"  onchange="calculo(this)"/></td>
                                                <td><input class="dt_input" type="text" size="3" id="det_vunit1" name="det_vunit1" value="" lang="1"  onchange="calculo(this)"/></td>
                                                <td><input class="dt_input" type="text" size="3" id="det_descuento_porcentaje1" name="det_descuento_porcentaje1" value="" lang="1"  onchange="calculo(this)"/></td>
                                                <td><input class="dt_input" type="text" size="5" id="det_total1" name="det_total1" value="" lang="1" readonly style="text-align:right" /></td>
                                                <td>
                                                    <select id="det_impuesto1" name="det_impuesto1" lang="1" onchange="calculo(this)" >
                                                        <option value="12">IVA 12</option>
                                                        <option value="0">IVA 0</option>
                                                        <option value="NO">NO OBJETO</option>
                                                        <option value="EX">EXCENTO</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php
                                        } else {
                                            $ndt = 0;
                                            while ($rst_det = pg_fetch_array($cns_det)) {
                                                $ndt++;
                                                ?>
                                                <tr>
                                                    <td><input class="dt_input" type="text" size="15" id="<?php echo 'det_cod_producto' . $ndt ?>" name="<?php echo 'det_cod_producto' . $ndt ?>" value="<?php echo $rst_det[det_cod_producto] ?>" lang="<?php echo $ndt ?>" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2"/></td>
                                                    <td><input class="dt_input" type="text" size="10" id="<?php echo 'det_lote' . $ndt ?>" name="<?php echo 'det_lote' . $ndt ?>" value="<?php echo $rst_det[det_lote] ?>" lang="<?php echo $ndt ?>"/></td>
                                                    <td><input class="dt_input" type="text" size="30" id="<?php echo 'det_descripcion' . $ndt ?>" name="<?php echo 'det_descripcion' . $ndt ?>" value="<?php echo $rst_det[det_descripcion] ?>" lang="<?php echo $ndt ?>"    style="font-size:9px"/></td>
                                                    <td><input class="dt_input" type="text" size="3" id="<?php echo 'det_cantidad' . $ndt ?>" name="<?php echo 'det_cantidad' . $ndt ?>" value="<?php echo $rst_det[det_cantidad] ?>" lang="<?php echo $ndt ?>"  onchange="calculo(this)"/></td>
                                                    <td><input class="dt_input" type="text" size="3" id="<?php echo 'det_vunit' . $ndt ?>" name="<?php echo 'det_vunit' . $ndt ?>" value="<?php echo $rst_det[det_vunit] ?>" lang="<?php echo $ndt ?>"  onchange="calculo(this)"/></td>
                                                    <td><input class="dt_input" type="text" size="3" id="<?php echo 'det_descuento_porcentaje' . $ndt ?>" name="<?php echo 'det_descuento_porcentaje' . $ndt ?>" value="<?php echo $rst_det[det_descuento_porcentaje] ?>"  lang="<?php echo $ndt ?>"  onchange="calculo(this)"/></td>
                                                    <td><input class="dt_input" type="text" size="5" id="<?php echo 'det_total' . $ndt ?>" name="<?php echo 'det_total' . $ndt ?>" value="<?php echo $rst_det[det_total] ?>"  lang="<?php echo $ndt ?>" readonly style="text-align:right" /></td>
                                                    <td>
                                                        <select id="<?php echo 'det_impuesto' . $ndt ?>" name="<?php echo 'det_impuesto' . $ndt ?>"   lang="<?php echo $ndt ?>" onchange="calculo(this)" >
                                                            <option value="12">12%</option>
                                                            <option value="0">0%</option>
                                                            <option value="NO">NO</option>
                                                            <option value="EX">EX</option>
                                                        </select>
                                                        <script>
                                                            idt1 = '<?php echo 'det_impuesto' . $ndt ?>';
                                                            $('#' + idt1).val('<?php echo $rst_det[det_impuesto] ?>');
                                                        </script>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td colspan="3">Subtotal 12%:</td>
                                            <td><input type="text" size="5" id="ped_sbt12" value="<?php echo round($rst_enc[ped_sbt12], 4) ?>" readonly style="text-align:right"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Observaciones:</td>
                                            <td colspan="3">Subtotal 0%:</td>
                                            <td><input type="text" size="5" id="ped_sbt0" value="<?php echo round($rst_enc[ped_sbt0], 4) ?>" readonly style="text-align:right"/></td>
                                        </tr>
                                        <tr>
                                            <td valign="top" rowspan="11" colspan="3">
                                                <textarea id="ped_observacion" rows="10" style="width:80%; text-transform: uppercase;" onkeydown="return enter(event)"><?php echo $rst_enc[ped_observacion] ?></textarea>
                                            </td>
                                            <td colspan="3">Subt No Iva:</td>
                                            <td><input type="text" size="5" id="ped_sbt_noiva" value="<?php echo round($rst_enc[ped_sbt_noiva], 4) ?>" readonly style="text-align:right"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Subt Exc Iva:</td>
                                            <td><input type="text" size="5" id="ped_sbt_excento" value="<?php echo round($rst_enc[ped_sbt_excento], 4) ?>" readonly style="text-align:right"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Subtotal:</td>
                                            <td><input type="text" size="5" id="ped_sbt" value="<?php echo round($rst_enc[ped_sbt], 4) ?>" readonly style="text-align:right"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Tot Desc:</td>
                                            <td><input type="text" size="5" id="ped_tdescuento" value="<?php echo round($rst_enc[ped_tdescuento], 4) ?>" readonly style="text-align:right"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">IVA 12%:</td>
                                            <td><input type="text" size="5" id="ped_iva12" value="<?php echo round($rst_enc[ped_iva12], 4) ?>" readonly style="text-align:right"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Valor ICE:</td>
                                            <td><input type="text" size="5" id="ped_ice" value="<?php echo round($rst_enc[ped_ice], 4) ?>"  style="text-align:right" onchange="calculo_totales()"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Valor IRBPRN:</td>
                                            <td><input type="text" size="5" id="ped_irbpnr" value="<?php echo round($rst_enc[ped_irbpnr], 4) ?>"  style="text-align:right" onchange="calculo_totales()"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Propina:</td>
                                            <td><input type="text" size="5" id="ped_propina" value="<?php echo round($rst_enc[ped_propina], 4) ?>"  style="text-align:right" onchange="calculo_totales()"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Valor Total:</td>
                                            <td><input type="text" size="7" id="ped_total" value="<?php echo round($rst_enc[ped_total], 4) ?>" readonly style="text-align:right;background:#ccc;color:brown;font-weight:bolder   "/></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </form>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <?php
                            if ($x != 3) {
                                if ($x != 1) {
                                    ?>
                                    <button id="save" lang="<?php echo $id ?>" onclick="save(<?php echo $id ?>)" >Guardar</button>
                                    <button id="cancel" >Cancelar</button>
                                    <?php
                                } else {
                                    ?>
                                    <button id="aprobar" lang="<?php echo $id ?>" onclick="aprobar(1,<?php echo $rst_enc[ped_id] ?>, '<?php echo $num_doc ?>')" >Aprobar</button>
                                    <button id="rechazar" lang="<?php echo $id ?>" onclick="aprobar(0,<?php echo $rst_enc[ped_id] ?>, '<?php echo $num_doc ?>')">Rechazar</button>
                                    <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </body>
</html>        
<script>
    $('#ped_local').val('<?php echo $rst_enc[ped_local] ?>');
    var productos = [];
</script>
<datalist id="productos">
</datalist>


