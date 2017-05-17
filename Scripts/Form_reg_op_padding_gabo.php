<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_padding.php';
$Set = new Clase_reg_padding();
$txt = trim(strtoupper($_GET[txt]));
$est = $_GET[estado];
$fec1 = $_GET[fecha1];
$fec2 = $_GET[fecha2];
$cns_maq = $Set->lista_maquinas();
$cns_combomp1 = $Set->lista_combo_empa_core('26'); ///EMPA
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $texto = "and r.rpa_id=$id";
    $rst = pg_fetch_array($Set->lista_buscador_orden($texto));
    $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst[opp_id]));
    $f1 = $rst[pro_peso] - $rst_prod[peso];
    $fr1 = $rst[opp_cantidad] - $rst_prod[rollo];
} else {
    $id = 0;
    $rst[rpa_fecha] = date('Y-m-d');
    $rst[pro_peso] = '0';
    $rst[opp_cantidad] = '0';
    $rst[rpa_peso] = '0';
    $rst_prod[peso] = '0';
    $rst[rpa_rollo] = '0';
    $rst_prod[rollo] = '0';
    $f1 = '0';
    $fr1 = '0';
    $rst_sec = pg_fetch_array($Set->lista_secuencial_registro());
    if (!empty($rst_sec)) {
        $sec = substr($rst_sec[rpa_numero], 4, 8) + 1;
    } else {
        $sec = 1;
    }
    if ($sec >= 0 && $sec < 10) {
        $tx_trs = "0000000";
    } elseif ($sec >= 10 && $sec < 100) {
        $tx_trs = "000000";
    } elseif ($sec >= 100 && $sec < 1000) {
        $tx_trs = "00000";
    } elseif ($sec >= 1000 && $sec < 10000) {
        $tx_trs = "0000";
    } elseif ($sec >= 10000 && $sec < 100000) {
        $tx_trs = "000";
    } elseif ($sec >= 100000 && $sec < 1000000) {
        $tx_trs = "00";
    } elseif ($sec >= 1000000 && $sec < 10000000) {
        $tx_trs = "0";
    } elseif ($sec >= 10000000 && $sec < 100000000) {
        $tx_trs = "";
    }
    $rst[rpa_numero] = 'PPA' . $tx_trs . $sec;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            var rec_id =<?php echo $id ?>;
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
                Calendar.setup({inputField: "rpa_fecha", ifFormat: "%Y-%m-%d", button: "im-rpa_fecha"});
            });
            function save(f, v, pro_id) {
                var data = Array();
//                n = 0;
//                while (n < v) {
//                    n++;
                lote = $('#pro_lote1_' + f).html();
                estado = $('#pro_estado1_' + f).html();
                spro = $('#semi_pro_id1_' + f).html();
                slote = $('#lote_semi1_' + f).html();
                cnt = $('#cantidad_' + f).html();
                if (estado == 0) {
                    pe = $('#valor1_' + f).html();
                } else {
                    pe = $('#valor2_' + f).html();
                }
                peso_egr=($('#cal_rollo').val()*1)*($('#pro_peso_nt').val()*1)+($('#pro_cores').val()*1);
                data.push(
                        opa_id.value + '&&' +
                        rpa_fecha.value + '&&' +
                        rpa_numero.value + '&&' +
                        lote + '&&' +
                        pe + '&&' +
                        cnt + '&&' + //cnt rollo
                        estado + '&&' +
                        spro + '&&' +
                        slote + '&&' +
                        maq_id.value + '&&' +
                        rec_observaciones.value.toUpperCase()+ '&&' +
                        peso_egr
                        );
//                    f++;
//                }
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (opa_numero.value.length == 0) {
                            $("#opa_numero").css({borderColor: "red"});
                            $("#opa_numero").focus();
                            return false;
                        }
                        if (maq_id.value == 0) {
                            $("#maq_id").css({borderColor: "red"});
                            $("#maq_id").focus();
                            return false;
                        }
//                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_reg_padding.php',
                    data: {op: 0, 'data[]': data, id: rec_id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
//                            cancelar();
                            $("#rec_observaciones").html('');
                            $("#scroll").scrollTop($("#scroll")[0].scrollHeight);
                            load_rollos();
                            total(pro_id, pe, lote, opa_numero.value, estado);
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_reg_op_padding.php?txt=' + '<?php echo $txt ?>' + '&estado=' + '<?php echo $est ?>' + '&fecha1=' + '<?php echo $fec1 ?>' + '&fecha2=' + '<?php echo $fec2 ?>';
            }

            function load_rollos() {
                $.post("actions_reg_padding.php", {op: 2, id: opa_numero.value},
                function (dt) {
                    dat = dt.split('&');
                    $('#list_semielaborado').html(dat[12]);
                })
            }
            function load_orden(obj) {
                $.post("actions_reg_padding.php", {op: 2, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#opa_id').val(dat[0]);
                        $('#opa_numero').val(dat[1]);
                        $('#ord_fec_pedido').val(dat[2]);
                        $('#ord_fec_entrega').val(dat[3]);
                        $('#tip_bodega').val(dat[4]);
                        $('#bodega').val(dat[5]);
                        $('#nombre').val(dat[6]);
                        $('#producto1').val(dat[7]);
                        $('#ord_num_rollos1').val(dat[8]);
                        $('#ord_kgtotal1').val(dat[9]);
                        $('#ord_ancho1').val(dat[10] * 1000);
                        $('#ord_carril1').val('');
                        $('#tle_pro1').html(dat[7]);
                        $('#ord_peso').val('0');
                        if (dat[7].trim().length != 0) {
                            $('#guardar1').attr('disabled', false);
                            $('#inconforme1').attr('disabled', false);
                        } else {
                            $('#guardar1').attr('disabled', true);
                            $('#inconforme1').attr('disabled', true);
                        }
                        $('#rollo1').attr("class", '1_' + dat[11]);
                        $('#list_semielaborado').html(dat[12]);
                        $('#lista').html(dat[13]);
                        $('#total2').html(dat[14]);
                        $('#total3').html(dat[15]);
                        $('#ord_espesor').val(dat[16]);
                        $('#ord_largo').val(dat[17]);
                        $('#pro_mp1').val(dat[18]);
                        $('#opp_velocidad').val(dat[19]);
                        $('#pro_mf3').val(dat[20]);
                        $('#cajas').val(dat[21]);
                        $('#cajas_faltantes').val(dat[22]);
                        $('#mp_cnt1').val(dat[23]);
                        $('#pro_peso_nt').val(dat[24]);
                        $('#pro_peso_brt').val(dat[25]);
                        $('#total1').html(dat[26]);
                        $('#orden_semi').html(dat[27]);
                        $('#ord_observaciones').html(dat[28]);
                        $('#pro_coret').val(dat[29]);
                        $('#lote_semielaborado').val('');
                        $("#scroll").scrollTop($("#scroll")[0].scrollHeight);
                    } else {
                        alert('Orden no existe');
                        $('#opa_numero').focus();
                        $('#opa_id').val("");
                        $('#opa_numero').val("");
                        $('#ord_fec_pedido').val("");
                        $('#ord_fec_entrega').val("");
                        $('#tip_bodega').val("");
                        $('#bodega').val("");
                        $('#nombre').val("");
                        $('#producto1').val("");
                        $('#ord_num_rollos1').val("0");
                        $('#ord_kgtotal1').val("0");
                        $('#ord_ancho1').val("0");
                        $('#ord_carril1').val("0");
                        $('#tle_pro1').html("");
                        $('#rollo1').val("");
                        $('#guardar1').attr('disabled', true);
                        $('#inconforme1').attr('disabled', true);
                        $('#rollo1').attr("class", '');
                        $('#list_semielaborado').html('');
                        $('#lista').html('');
                        $('#total1').html('0');
                        $('#total2').html('0');
                        $('#ord_espesor').val('0');
                        $('#ord_largo').val('0');
                        $('#pro_mp1').val('0');
                        $('#opp_velocidad').val('0');
                        $('#pro_mf3').val('0');
                        $('#cajas').val('0');
                        $('#cajas_faltantes').val('0');
                        $('#mp_cnt1').val('0');
                        $('#pro_peso_nt').val('0');
                        $('#pro_peso_brt').val('0');
                        $('#pro_coret').val('0');
                        $('#total1').html('0');
                        $('#orden_semi').html('');
                        $('#ord_observaciones').html('');
                    }
                });
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function validar() {
                pp = $('#rpa_peso_primario').val();
                s1 = $('#sol1').val();
                rp = $('#rpa_rollo_primario').val();
                r1 = $('#sol_r1').val();
                pp1 = $('#prod1').val();
                pr1 = $('#prod_r1').val();
                mp =<?php echo $rst[rpa_peso] ?>;
                mr1 =<?php echo $rst[rpa_rollo] ?>;
                por1 = (parseFloat(s1) * 50) / 100;
                por3 = (parseFloat(r1) * 50) / 100;
                s1 = parseFloat(s1) + por1;
                r1 = parseFloat(r1) + por3;
                if (id == 0) {
                    sp1 = parseFloat(pp1) + parseFloat(pp);
                    sr1 = parseFloat(pr1) + parseFloat(rp);
                } else {
                    sp1 = parseFloat(pp1) + parseFloat(pp) - parseFloat(mp);
                    sr1 = parseFloat(pr1) + parseFloat(rp) - parseFloat(mr1);
                }
                if (sp1 > parseFloat(s1)) {
                    alert('La suma del peso producido y faltante del \n primer producto es mayor al solicitado');
                    $("#rpa_peso_primario").css({borderColor: "red"});
                    $("#rpa_peso_primario").focus();
                    $("#rpa_peso_primario").val('');
                    return false;
                }
                if (parseFloat(sr1) > parseFloat(r1)) {
                    alert('La suma de los rollos producidos y faltantes del \n primer producto es mayor al solicitado');
                    $("#rpa_rollo_primario").css({borderColor: "red"});
                    $("#rpa_rollo_primario").focus();
                    $("#rpa_rollo_primario").val('');
                    return false;
                }
            }


            function clonar() {
                var ps = peso();
                if (ps == 1) {
                    return false;
                }

//                if (ord_peso.value.length == 0) {
//                    $("#ord_peso").css({borderColor: "red"});
//                    $("#ord_peso").focus();
//                    return false;
//                } 

                if ($("#cal_rollo").val().length == 0) {
                    $("#cal_rollo").css({borderColor: "red"});
                    $("#cal_rollo").focus();
                    return false;
                } else {
                    $("#cal_rollo").css({borderColor: ""});
                    $("#cal_rollo").focus();
                }


                if ($("#pes_conforme").val().length == 0) {
                    $("#pes_conforme").css({borderColor: "red"});
                    $("#pes_conforme").focus();
                    return false;
                } else {
                    $("#pes_conforme").css({borderColor: ""});
                    $("#pes_conforme").focus();
                }


                if (maq_id.value == 0) {
                    $("#maq_id").css({borderColor: "red"});
                    $("#maq_id").focus();
                    return false;
                }
                if (semielaborado.value == null || semielaborado.value == 0) {
                    $("#semielaborado").css({borderColor: "red"});
                    $("#semielaborado").focus();
                    return false;
                }
//                if ($('#ord_peso').val().length != 0) {

                if ($('#cal_rollo').val().length != 0 || $('#pes_conforme').val().length != 0) {
                    d = 0;
                    n = 0;
                    j = $('.itm').length;
                    id1 = $('#rollo1').attr('class');
                    lt1 = $('#opa_numero').val().replace('-', '') + '1';
                    var v = 1;
                    var f = 0;
                    //rollo conforme
                    i = j + 1;
                    f = i;
                    if (i < 10) {
                        crs = '00'
                    } else if (i >= 10 && i < 100) {
                        crs = '0'
                    } else if (i >= 100 && i < 1000) {
                        crs = ''
                    }
//                    val1 = parseFloat(semi_peso1.value).toFixed(2);
                    val1 = parseFloat(pes_conforme.value).toFixed(2);
                    ls1 = rollo_semi1.value;
                    sid = semi_id1.value;
                    lote1 = lt1 + crs + i;
//                    cnt1 = parseFloat(semi_cnt1.value).toFixed();
                    cnt1 = parseFloat($('#cal_rollo').val()).toFixed();
                    var fila = '<tr class="itm" style="height:20px">' +
                            '<td style="width:100px" align="right" id="lote_semi1_' + i + '">' + ls1 + '</td>' +
                            '<td hidden id="semi_pro_id1_' + i + '">' + sid + '</td>' +
                            '<td style="width:80px" align="right" id="cantidad_' + i + '">' + cnt1 + '</td>' +
                            '<td style="width:80px" align="right" id="valor1_' + i + '">' + val1 + '</td>' +
                            '<td style="width:80px" align="right" id="valor2_' + i + '"></td>' +
                            '<td hidden id="pro_id1_' + i + '">' + id1 + '</td>' +
                            '<td hidden id="pro_lote1_' + i + '">' + lote1 + '</td>' +
                            '<td hidden id="pro_estado1_' + i + '">0</td>' +
                            '</tr>';
                    $('#lista').append(fila);
                    //////rollo inconforme
//                    val2 = parseFloat(semi_pesoinc1.value).toFixed(2);
//                    if (val2 > 0) {
//                        v = v + 1;
//                        h = i + 1;
//                        if (h < 10) {
//                            crs2 = '00'
//                        } else if (h >= 10 && i < 100) {
//                            crs2 = '0'
//                        } else if (h >= 100 && i < 1000) {
//                            crs2 = ''
//                        }
//                        lote2 = lt1 + crs2 + h;
//                        var fila = '<tr class="itm" style="height:20px">' +
//                                '<td style="width:100px" align="right" id="lote_semi1_' + h + '">' + ls1 + '</td>' +
//                                '<td hidden id="semi_pro_id1_' + h + '">' + sid + '</td>' +
//                                '<td style="width:80px" align="right" id="cantidad_' + h + '">1</td>' +
//                                '<td style="width:80px" align="right" id="valor1_' + h + '"></td>' +
//                                '<td style="width:80px" align="right" id="valor2_' + h + '">' + val2 + '</td>' +
//                                '<td hidden id="pro_id1_' + h + '">' + id1 + '</td>' +
//                                '<td hidden id="pro_lote1_' + h + '">' + lote2 + '</td>' +
//                                '<td hidden id="pro_estado1_' + h + '">3</td>' +
//                                '</tr>';
//                        $('#lista').append(fila);
//                    }
                    dt = id1.split('_');
                    pro_id = dt[1];
//                    save(f, v, pro_id);
                    save(f, '0', pro_id);

                }
                rollo1.value = '';
                rollo1.class = '';
                ord_peso.value = '0';
                $('#cal_rollo').val('0');
                pes_conforme.value = '0';
                lote_semielaborado.value = '';
                $('#cal_cajas').val('0');
                cal_caja_rollos.value = '0';
            }

            function calcular() {
                var ps = peso();
                if (ps == 1) {
                    return false;
                }
                if (ord_peso.value.length == 0) {
                    $("#ord_peso").css({borderColor: "red"});
                    $("#ord_peso").focus();
                    return false;
                } else {
                    $("#ord_peso").css({borderColor: ""});
                    $("#ord_peso").focus();
                }
                if (maq_id.value == 0) {
                    $("#maq_id").css({borderColor: "red"});
                    $("#maq_id").focus();
                    return false;
                }
                if (semielaborado.value == null || semielaborado.value == 0) {
                    $("#semielaborado").css({borderColor: "red"});
                    $("#semielaborado").focus();
                    return false;
                }
                if ($('#ord_peso').val().length != 0) {
                    d = 0;
                    n = 0;
                    j = $('.itm').length;
                    id1 = $('#rollo1').attr('class');
                    lt1 = $('#opa_numero').val().replace('-', '') + '1';
                    var v = 1;
                    var f = 0;
                    //rollo conforme
                    i = j + 1;
                    f = i;
                    if (i < 10) {
                        crs = '00'
                    } else if (i >= 10 && i < 100) {
                        crs = '0'
                    } else if (i >= 100 && i < 1000) {
                        crs = ''
                    }
                    val1 = parseFloat(semi_peso1.value).toFixed(2);
                    ls1 = rollo_semi1.value;
                    sid = semi_id1.value;
                    lote1 = lt1 + crs + i;
                    cnt1 = parseFloat(semi_cnt1.value).toFixed();
                    $('#cal_rollo').val(cnt1);
                    $('#pes_conforme').val(val1);
                    prol = val1 / cnt1;
                    $('#pes_rol').val(prol.toFixed(2));

                }
//                $('#ord_peso').val('0');
                calculo_cajas();
            }

            function bloquear() {
                $('#opa_numero').attr('disabled', 'disabled');
            }

            function total(pro, peso, lt, ord, std) {
                loading('hidden');
                doc = document.getElementsByClassName('itm');
                n = 0;
                cnf = 0;
                sum1 = 0;
                sum2 = 0;
                sum3 = 0;
                sumc = 0;
                while (n < doc.length) {
                    n++;
                    if ($('#cantidad_' + n).html().length == 0) {
                        can1 = 0;
                    } else {
                        can1 = $('#cantidad_' + n).html()
                    }
                    if ($('#valor1_' + n).html().length == 0) {
                        can2 = 0;
                        cnf = 0;
                    } else {
                        can2 = $('#valor1_' + n).html();
                        cnf = $('#cantidad_' + n).html();
                    }

                    if ($('#valor2_' + n).html().length == 0) {
                        can3 = 0;
                    } else {
                        can3 = $('#valor2_' + n).html();
                    }
                    sum1 = sum1 + parseFloat(can1);
                    sum2 = sum2 + parseFloat(can2);
                    sum3 = sum3 + parseFloat(can3);
                    sumc = sumc + parseFloat(cnf);
                }
                cj = sumc / parseFloat($('#opp_velocidad').val());
                dt = cj.toString().split('.');
                if (parseFloat(dt[1]) > 0) {
                    cjr = parseFloat(dt[0]) + 1;
                } else {
                    cjr = parseFloat(dt[0]);
                }
                cjf = parseFloat($('#mp_cnt1').val()) - cjr;
                $('#cajas').val(cjr);
                $('#cajas_faltantes').val(cjf);
                $('#total1').html(sum1.toFixed(2));
                $('#total2').html(sum2.toFixed(2));
                $('#total3').html(sum3.toFixed(2));
                if (parseFloat(ord_kgtotal1.value) < parseFloat($('#total1').html())) {
                    alert('Peso del Producto sobrepasa al requerido');
                }

                if ($('#imprimir').attr('checked') == true && std == '3') {
                    auxWindow(0, pro, peso, lt, ord, std);
                }
            }

            var boxH = $(window).height() * 0.50;
            var boxW = $(window).width() * 0.50;
            var boxHF = (boxH - 25);
            function auxWindow(a, pro, peso, lt, ord, std) {
                switch (a) {
                    case 0:
                        wnd = "<iframe id='frmmodal' width='" + boxW + "' height='" + boxHF + "' src='../Reports/pdf_etiq_reg_extrusion.php?pro_id=" + pro + "&peso=" + peso + "&lote=" + lt + "&orden=" + ord + "&std=" + std + "' frameborder='0' />";
                        break;
                }

                $.fallr.show({
                    content: '<center>ETIQUETA</center>'
                            + wnd,
                    width: boxW,
                    height: boxH,
                    duration: 5,
                    position: 'center',
                    buttons: {
                        button1: {
                            text: '&#X00d7;',
                            onclick: function () {
                                $.fallr.hide();
                            }
                        }
                    }
                });
            }

            function peso() {
                p = ord_peso.value;
                dt = $('#semielaborado').val().split('_');
                if (parseFloat(p) > parseFloat(dt[2])) {
                    c_p = 1;
                } else {
                    c_p = 0;
                }
                p1 = $('#producto1').val().trim();
                if (c_p == 0) {
                    if (p1.length != 0) {
                        $('#rollo1').val(p);
                    } else {
                        $('#rollo1').val("");
                    }
                    if ($('#semielaborado').val() != '0' || $('#semielaborado').val() != null) {
                        pt = parseFloat(dt[2]) - parseFloat(p);
                        rollos = parseFloat(pt).toFixed(2) / parseFloat(pro_peso_nt.value).toFixed(2);
                        dr = rollos.toString().split('.');
                        rc = dr[0];
                        pi = rollos - parseFloat(rc);
                        prconf = parseFloat(rc) * parseFloat(pro_peso_brt.value);
                        princ = pi * parseFloat(pro_peso_brt.value) + parseFloat(p);
                        $('#semi_id1').val(dt[0]);
                        $('#rollo_semi1').val(dt[1]);
                        $('#semi_cnt1').val(rc);
                        $('#semi_peso1').val(parseFloat(prconf).toFixed(2));
                        $('#semi_pesoinc1').val(parseFloat(princ).toFixed(2));
                    } else {
                        $('#semi_id1').val('');
                        $('#rollo_semi1').val('');
                        $('#semi_peso1').val('');
                    }
                    return 0;
                } else {
                    alert('El peso es mayor que el peso del rollo semielaborado');
                    $('#ord_peso').val('');
                    $('#rollo1').val("");
                    return 1;
                }
            }


            function load_semielaborado(obj) {
                $.post("actions_reg_padding.php", {op: 4, data: obj.value, id: opa_id.value},
                function (dt) {
                    dat = dt.split('_');
                    dato = dt.split('&&');
                    if (dat[0] != '') {
                        $('#semielaborado').val(dt);
                        $('#lote_semielaborado').val(dato[1]);
                        $('#pro_cores').val(dato[2]);
                        calcular();
                    } else {
                        alert('No existe producto semielaborado');
                        $(obj).val('');
                        $('#pro_cores').val('0');
                    }
                })
            }

            function traer_peso() {
                $.post("actions_reg_padding.php", {op: 5},
                function (dt) {
                    $('#ord_peso').val(dt);
                    clonar();
                })
            }

            function calculo_cajas() {
                if ($('#cal_rollo').val().length == 0) {
                    cal_rollo = 0;
                    $('#cal_cajas').val('0');
                    $('#cal_caja_rollos').val('0');
                } else {
                    cal_rollo = $('#cal_rollo').val();
                    cj = parseFloat(cal_rollo) / parseFloat($('#opp_velocidad').val());
                    dt = cj.toString().split('.');
                    if (parseFloat(dt[1]) > 0) {
                        cjr = parseFloat(dt[0]);
                    } else {
                        cjr = parseFloat(dt[0]);
                    }
                    rol = parseFloat(cal_rollo) - (cjr * parseFloat($('#opp_velocidad').val()));
                    $('#cal_cajas').val(cjr);
                    $('#cal_caja_rollos').val(rol);
                }

            }

            function calculo_peso() {
                if ($('#cal_rollo').val().length == 0) {
                    cal_rollo = 0;
                } else {
                    cal_rollo = $('#cal_rollo').val();
                }
                prl = (cal_rollo * parseFloat($('#pes_rol').val())).toFixed(2);
                if(prl=='NaN'){
                    prl=0;
                }
                $('#pes_conforme').val(prl);
                calculo_cajas();
            }

        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
            #semielaborado{
                width: 140px;
            }
            button{
                font-size:8px; 
                height: 30px;
            }
            .add td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="proceso" >   
        </div>
        <div id="cargando"></div>

        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form" border="1">
                <thead>
                    <tr><th colspan="9" >REGISTRO DE BOBINADO<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td style="height: 200px">
                        <table >

                            <tr>
                                <td colspan="1">ORDEN#:</td>
                                <td  colspan="1">
                                    <input type="text" size="15" id="opa_numero" value="<?php echo $rst[opp_codigo] ?>" onchange="load_orden(this)" list="ordenes"/>
                                    <input type="hidden" size="10" id="opa_id" readonly value="<?php echo $rst[opp_id] ?>" />
                                </td>
                                <td  colspan="1" hidden>REGISTRO #:</td>
                                <td  colspan="1" hidden>
                                    <input type="text" size="12" id="rpa_numero" readonly value="<?php echo $rst[rpa_numero] ?>" />
                                </td>
                                <td colspan="1" >FECHA:</td>
                                <td>
                                    <input type="text" size="15" id="rpa_fecha"  value="<?php echo $rst[rpa_fecha] ?>" />
                                    <img src="../img/calendar.png" id="im-rpa_fecha" />
                                </td>
                                <td>MAQUINA:</td>
                                <td>
                                    <select id="maq_id" style="width: 120px">
                                        <?php
                                        while ($rst_maq = pg_fetch_array($cns_maq)) {
                                            echo "<option value='$rst_maq[id]'>$rst_maq[maq_a]</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>

                                <td>ROLLO SEMI.</td>
                                <td>
                                    <input type="text" size="35" id="lote_semielaborado" value="<?php echo $rst[opp_codigo] ?>" onchange="load_semielaborado(this)" list="list_semielaborado"/>
                                    <input type="hidden" size="10" id="semielaborado" readonly value="<?php echo $rst[opp_id] ?>" />
                                    <input type="hidden" size="5" id="pro_cores" name="pro_cores" value="0"/></td>
                            <tr>
                                <td rowspan="1">
                                    <table>
                                    <tr>
                                        <td>
                                            <div>
                                               <table style="width:200px">
                                                    <thead>

                                                        <tr><th id="tle_pro1" colspan="1"></th></tr>
                                                        <tr><th style="width: 100px">P.Semielaborado</th></tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div id="scroll" style="overflow:scroll;height:150px;overflow-x:hidden;">
                                                <table style="width:200px" border="1"><tbody class="tbl_frm_aux"></tbody></table>
                                            </div>
                                        </td>
                                    </tr>
                                        <tr>
                                            <td>
                                                <div>
                                                    <table style="width:200px">
                                                        <tr class="add" style="height: 20px">
                                                            <td style="width:100px"></td>
                                                            <td align="right" style="width:80px;font-size: 14px" id="total1">0</td>
                                                            <td align="right" style="width:80px;font-size: 14px" id="total2">0</td>
                                                            <td align="right" style="width:80px;font-size: 14px" id="total3">0</td>
                                                        </tr>

                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                    </tr>
                    
                                <td>PESO INCONFORME:</td>
                                <td>
                                    <input type="text" size="15" id="ord_peso" value="0" onchange="calcular()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>

                                    <!--<button id="pesar" onclick="calcular()">Calcular</button>--> 
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="sbtitle" >CALCULOS</td>
                            </tr>
                            <tr>
                                <td>#Rollos</td> 
                                <td>
                                    <input type="text" size="12" id="cal_rollo" value="0" onchange="calculo_peso()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />
                                </td>  
                                <td>Peso Conforme</td> 
                                <td>
                                    <input type="text" size="12" id="pes_conforme" value="0" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    <input type="text" size="12" id="pes_rol" hidden/>
                                    <button id="pesar" onclick="clonar()">Registrar</button> 
                                </td> 
                            </tr>
                            <tr>
                                <td>Cajas</td> 
                                <td>
                                    <input type="text" readonly size="10" id="cal_cajas" value="0"  />
                                </td>  
                                <td>Rollos</td> 
                                <td>
                                    <input type="text" readonly size="10" id="cal_caja_rollos" value="0" />
                                </td> 
                            </tr>
                            <tr>
                                <td colspan="6" class="sbtitle" >DATOS GENERALES</td>
                            </tr>

                            <!------------------------------------------------------------------------- DATOS GENERALES ----------------------------------------------------------------------------------->
                        
                            
                            
                            
                            <tr>
                                <td>Fecha Pedido:</td>
                                <td><input readonly type="text" name="ord_fec_pedido" id="ord_fec_pedido" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_pedido] ?>"/>
                                <td>Bodega:</td>
                                <td>
                                    <input type="hidden" id="tip_bodega" value="<?php echo $tbodega ?>"/>
                                    <input type="text" readonly id="bodega" size="30" value="<?php echo $tbodega ?>"/>
                                </td>
                                <td>Orden Rollo Semi.:</td>
                                <td rowspan="2"><textarea disabled id="orden_semi"> </textarea></td>
                            </tr>
                            <tr>
                                <td>Fecha Entrega:</td>
                                <td><input type="text" readonly name="ord_fec_entrega" id="ord_fec_entrega" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_entrega] ?>"/>
                                <td>Cliente:</td>
                                <td><input type="text" readonly id="nombre" size="30" value="<?php echo $nombre ?>"/></td>
                            </tr>

                            <tr><td colspan="6" class="sbtitle" >PRODUCTOS</td></tr> 
                            <tr>
                                <td colspan="10">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Producto</th>
                                                <th>Rollos</th>
                                                <th>Peso(kg)</th>
                                                <th>Ancho(mm)</th>
                                                <th>Espesor</th>
                                                <th>Largo(m)</th>
                                                <th hidden>Peso Neto(kg)</th>
                                                <th>Peso Bruto(kg)</th>
                                            </tr>
                                        </thead>
                                        <!------------------------------------------------------------------------- PRimera linea  ----------------------------------------------------------------------------------->                            
                                        <tr >
                                            <td><input readonly type="text" size="3" style="text-align:right; font-size: 17px;" name="item1" id="item1" value="1"></td>
                                            <td><input readonly type="text" size="20" style="font-size: 17px;" name="producto1" id="producto1" value="<?php echo $rst1[pro_descripcion] ?>"></td>
                                            <td><input readonly type="text" size="6" style="text-align:right; font-size: 17px;" name="ord_num_rollos1" id="ord_num_rollos1" value="<?php echo $rst[ord_num_rollos] ?>" /></td>
                                            <td><input readonly type="text" size="8" style="text-align:right; font-size: 17px;" name="ord_kgtotal1" id="ord_kgtotal1" value="<?php echo $rst[ord_kgtotal] ?>"/></td>
                                            <td><input readonly type="text" size="5" style="text-align:right; font-size: 17px;" name="ord_ancho1" id="ord_ancho1" value="<?php echo $rst[ord_pri_ancho] ?>"/></td>
                                            <td><input readonly type="text" size="5" style="text-align:right; font-size: 17px;" name="ord_espesor" id="ord_espesor" value="<?php echo $rst[ord_pri_ancho] ?>"/></td>
                                            <td><input readonly type="text" size="5" style="text-align:right; font-size: 17px;" name="ord_largo" id="ord_largo" value="<?php echo $rst[ord_pri_ancho] ?>"/></td>
                                            <td hidden><input readonly type="text" size="5" style="text-align:right; font-size: 17px;" name="pro_peso_nt" id="pro_peso_nt" value="<?php echo $rst[pro_propiedad7] ?>"/></td>
                                            <td><input readonly type="text" size="5" style="text-align:right; font-size: 17px;" name="pro_peso_brt" id="pro_peso_brt" value="<?php echo $rst[pro_propiedad7] ?>"/></td>
                                            <td><input hidden type="text" size="5" style="text-align:right; font-size: 17px;" name="pro_coret" id="pro_coret" value="0"/></td>
                                            <!--<td><input readonly type="text" size="3" style="text-align:right" name="ord_carril1" id="ord_carril1" value="<?php echo $rst[ord_pri_carril] ?>" /></td>-->
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>EMPAQUE:</td>
                                            <td>
                                                <select id="pro_mp1" style="width: 160px" onchange="load_datos_mp(this)" lang="1" disabled >
                                                    <option value="0">Seleccione</option>
                                                    <?php
                                                    while ($rst_combo = pg_fetch_array($cns_combomp1)) {
                                                        echo "<option value='$rst_combo[mp_id]' >$rst_combo[mp_referencia]</option>";
                                                    }
                                                    ?>  
                                                </select>
                                                <input type="text" size="10"  hidden id="mp_cnt1" />
                                            </td>
                                            <td colspan="3" style="font-size: 17px;">CAJAS REALIZADAS:
                                                <input type="text" size="8"  id="cajas" style="text-align: right; font-size: 17px;" readonly value="0" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                                            <td colspan="2" style="font-size: 17px;">CAJAS FALTANTES:
                                                <input type="text" size="8"  id="cajas_faltantes" style="text-align: right; font-size: 17px;" readonly value="0" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>

                                        </tr> 
                                        <tr>
                                            <td>ROLLOS X EMPQ.:</td>
                                            <td><input type="text" readonly size="10" id="opp_velocidad" style="text-align: right" value="<?php echo $rst['opp_velocidad'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo()"/></td>
                                            <td colspan="3" rowspan="2">Observaciones:
                                                <textarea name="rec_observaciones" id="ord_observaciones" style="width:100%" disabled></textarea></td>

                                            <td colspan="3" rowspan="2">Novedades:
                                                <textarea name="rec_observaciones" id="rec_observaciones" style="width:100%"></textarea></td>
                                        </tr>
                                        <tr>
                                            <td>PESO BRUTO:</td>
                                            <td><input type="text" size="10"  id="pro_mf3" style="text-align: right" readonly value="<?php echo $rst['pro_mf3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                                        </tr>

                                    </table>    
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td rowspan="5">
                        <table>
                            <tr>
                                <td>
                                    <div>
                                        <table style="width:400px">
                                            <thead>
                                                <tr>
                                                    <td class="sbtitle" colspan="4">PRODUCTO</td>
                                                </tr>
                                                <tr>
                                                    <th id="tle_pro1" colspan="4"></th>
                                                </tr>
                                                <tr hidden>
                                                    <td  align="center"><input readonly type="text" size="6" style="text-align:right" name="rollo_semi1" id="rollo_semi1">
                                                        <input readonly type="text" size="6" name="semi_id1" id="semi_id1">
                                                        <input readonly type="text" size="6" name="semi_cnt1" id="semi_cnt1">
                                                        <input readonly type="text" size="6" name="semi_peso1" id="semi_peso1">
                                                        <input readonly type="text" size="6" name="semi_pesoinc1" id="semi_pesoinc1">
                                                    </td>
                                                    <td colspan="2" align="center"><input readonly type="text" size="6" style="text-align:right" name="rollo1" id="rollo1"></td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 100px">P.Semielaborado</th>
                                                    <th style="width: 80px"># Rollos</th>
                                                    <th style="width:80px">Peso Conf.</th>
                                                    <th style="width:80px">Peso Inconf.</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div id="scroll" style="overflow:scroll;height:150px;overflow-x:hidden;">
                                        <table style="width:400px" border="1">
                                            <tbody class="tbl_frm_aux" id="lista" >   
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <table style="width:400px">
                                            <tr class="add" style="height: 20px">
                                                <td style="width:100px"></td>
                                                <td align="right" style="width:80px;font-size: 14px" id="total1">0</td>
                                                <td align="right" style="width:80px;font-size: 14px" id="total2">0</td>
                                                <td align="right" style="width:80px;font-size: 14px" id="total3">0</td>
                                            </tr>
                                            <tfoot>
                                                <tr>
                                                    <td style="width:100px">
                                                        IMPRIMIR:<input type="checkbox" id="imprimir" checked>
                                                    </td>
                                                </tr>

                                            </tfoot>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>  
<datalist id="ordenes">
    <?php
    $cns_ord = $Set->lista_ordenes();
    $n = 0;
    while ($rst_ord = pg_fetch_array($cns_ord)) {
        $n++;
        ?>
        <option value="<?php echo $rst_ord[opp_codigo] ?>" label="<?php echo $rst_ord[opp_codigo] ?>" />
        <?php
    }
    ?>
</datalist>
<datalist id="list_semielaborado">
</datalist>