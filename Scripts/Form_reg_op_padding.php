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
                n = 0;
                var pec;
                var pei;
                var lotec;
                var lotei;
                var estadoc;
                var estadoi;
                while (n < v) {
                    n++;
                    lote = $('#pro_lote1_' + f).html();
                    estado = $('#pro_estado1_' + f).html();
                    spro = $('#semi_pro_id1_' + f).html();
                    slote = $('#lote_semi1_' + f).html();
                    cnt = $('#cantidad_' + f).html();
                    if (estado == 0) {
                        pe = $('#valor1_' + f).html();
                        pec = $('#valor1_' + f).html();
                        lotec = $('#pro_lote1_' + f).html();
                        estadoc = estado;
                    } else {
                        pe = $('#valor2_' + f).html();
                        pei = $('#valor2_' + f).html();
                        lotei = $('#pro_lote1_' + f).html();
                        estadoi = estado;
                    }
                    if (cnt > 0) {
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
                                rec_observaciones.value.toUpperCase()
                                );
                    }
                    f++;
                }
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                j = $('.itms').length;
                i = 0;
                var data_semi = Array();
                while (i < j) {
                    i++;
                    data_semi.push($('#sm_lote' + i).html() + '&&' +
                            $('#sm_pro_id' + i).html() + '&&' +
                            $('#sm_rollo' + i).html() + '&&' +
                            $('#sm_peso' + i).html() + '&&' +
                            $('#sm_core' + i).html() + '&&' +
                            $('#sm_pesobrt' + i).html() + '&&' +
                            $('#sm_estado' + i).html()
                            );
                }
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (opa_numero.value.length == 0) {
                            $("#opa_numero").css({borderColor: "red"});
                            $("#opa_numero").focus();
                            return false;
                        }
                        if (rpa_fecha.value.length == 0) {
                            $("#rpa_fecha").css({borderColor: "red"});
                            $("#rpa_fecha").focus();
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
                    data: {op: 0, 'data[]': data, id: rec_id, 'fields[]': fields, 'data_semi[]': data_semi}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
//                            cancelar();
                            
                            if ($('#imprimir').attr('checked') == true) {
                                pec = pec;
                                lotec = lotec;
                                estadoc = estadoc;
                                pei = pei;
                                lotei = lotei;
                                estadoi = estadoi;
                            } else {
                                pec = pei;
                                lotec = lotei;
                                estadoc = estadoi;
                                pei = '';
                                lotei = '';
                                estadoi = '';
                            }
                            if ($('#imprimir_inc').attr('checked') == false) {
                                pec = pec;
                                lotec = lotec;
                                estadoc = estadoc;
                                pei = '';
                                lotei = '';
                                estadoi = '';
                            }
                            peso_nt = pec - dat[4];
                            largo = peso_nt / (dat[1] * dat[3] * dat[8]) * 1000000;
                            var datos = Array(
                                    opa_numero.value,
                                    dat[1],
                                    pec, ///p. conf
                                    dat[3],
                                    dat[2],
                                    lotec, //lote conf
                                    estadoc,
                                    etiqueta.value,
                                    eti_numero.value,
                                    rpa_fecha.value,
                                    nombre.value,
                                    dat[4], //core
                                    pec - dat[4], //p.neto conf
                                    pei, //p. inc bruto
                                    pei - dat[4], //p.neto inc
                                    lotei, //lote inc
                                    estadoi, //estado inc
                                    dat[5], //ord_id       
                                    dat[6], //pro_id       
                                    dat[7],//cli_id  
                                    largo,
                                    '<?php echo $_SESSION[usuario]?>',                        
                                    ord_observaciones.value.toUpperCase()
                                    );
                            $("#rec_observaciones").val('');
                            save_etiqueta(datos);
                            limpiar_form();
//                            save_semielaborado();
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }

            function save_etiqueta(datos) {
                imp = 0;
                if ($('#imprimir').attr('checked') == true || $('#imprimir_inc').attr('checked') == true) {
                    imp = 1;
                }
                $.ajax({
                    type: 'POST',
                    url: 'actions_reg_padding.php',
                    data: {op: 6, 'data[]': datos, id: imp}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            total(datos);
                        } else {
                            alert(dt);
                        }
                    }
                });
            }
            function limpiar_form() {
                $("#rec_observaciones").html('');
                $('#lista_semi').html('');
                $('#rollos_prod').val('0');
                $('#cajas_prod').val('0');
                $('#rollos_sueltos').val('0');
                $('#peso_conforme').val('0');
                $('#peso_inconforme').val('0');
                $('#ord_peso').val('0');
                $('#cores_pesados').val('0');
                $('#total_peso').val('0');
                $('#lote_semielaborado').focus();
                $("#scroll").scrollTop($("#scroll")[0].scrollHeight);
                $('#sobrante').val('0');
                $('#sobrante').attr('readonly', false);
                $('#imp_sobrante').attr('disabled', false);
                load_rollos();
            }
            function save_semielaborado() {
                if (parseFloat($('#total_psm').val()) == 0) {
                    alert('El sobrante no debe ser mayor al peso de semielaborado');
                    return false;
                }
                if (parseFloat($('#sobrante').val()) == 0) {
                    alert('El sobrante debe ser mayor a 0');
                    return false;
                }
                if ($('#sobrante').val().length == 0) {
                    alert('El sobrante debe ser mayor a 0');
                    $('#sobrante').val('0');
                    return false;
                }
                var fch = valFecha();
                if (fch == false) {
                    $("#rpa_fecha").css({borderColor: "red"});
                    $("#rpa_fecha").focus();
                    return false;
                }
                $('#sobrante').attr('readonly', true);
                $('#imp_sobrante').attr('disabled', true);

                j = $('.itms').length;
                var data = Array(
                        $('#sm_lote' + j).html(),
                        $('#sm_pro_id' + j).html(),
                        $('#rpa_fecha').val(),
                        $('#sobrante').val()
                        );

                $.ajax({
                    type: 'POST',
                    url: 'actions_reg_padding.php',
                    data: {op: 7, 'data[]': data}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            var datos = Array(
                                    dat[12],
                                    dat[1], //ancho
                                    parseFloat($('#sobrante').val()), //p.bruto
                                    dat[3], //espesor
                                    dat[2], //largo
                                    $('#sm_lote' + j).html(),
                                    dat[8],
                                    dat[9], //tamaño
                                    dat[10], //copias
                                    rpa_fecha.value, //fecha
                                    dat[11], //cliente
                                    $('#sm_core' + j).html(), //core
                                    parseFloat($('#sobrante').val()) - parseFloat($('#sm_core' + j).html()), //p.neto
                                    '', //inc
                                    '', //inc
                                    '', //inc
                                    '', //inc
                                    dat[5], //ord_id       
                                    dat[6], //pro_id       
                                    dat[7],//cli_id 
                                    largo        
                                    );
//                              alert(datos);      
                            save_etiqueta(datos);
//                            limpiar_form();
                        } else {
                            alert(dt);
                        }
                    }
                });
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
                $.post("actions_reg_padding.php", {op: 2, id: opa_numero.value.toUpperCase()},
                function (dt) {
                    dat = dt.split('&');
//                    $('#list_semielaborado').html(dat[12]);
                    $('#orden_semi').html(dat[27]);
                })
            }
            function load_orden(obj) {
                $('#lista_semi').html('');
                $('#rollos_prod').val('0');
                $('#cajas_prod').val('0');
                $('#rollos_sueltos').val('0');
                $('#peso_conforme').val('0');
                $('#peso_inconforme').val('0');
                $('#ord_peso').val('0');
                $('#cores_pesados').val('0');
                $('#total_peso').val('0');
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
                        $('#rollo1').val(dat[11]);
//                        $('#list_semielaborado').html(dat[12]);
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
                        $('#etiqueta').val(dat[30]);
                        $('#eti_numero').val(dat[31]);
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
//                        $('#list_semielaborado').html('');
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
                        $('#etiqueta').val('');
                        $('#eti_numero').val('');
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
                fecha = valFecha();
                if (fecha != false) {
                    if (parseFloat(ord_peso.value) > parseFloat(total_peso.value)) {
                        $("#ord_peso").css({borderColor: "red"});
                        $("#ord_peso").focus();
                        $("#ord_peso").val('0');
                        alert('Peso inconforme es mayor al peso de los Rollos');
                        return false;
                    } else {
                        $("#ord_peso").css({borderColor: ""});
                        $("#ord_peso").focus();
                    }
                    if ($("#rollos_prod").val() == 0) {
                        $("#rollos_prod").css({borderColor: "red"});
                        $("#rollos_prod").focus();
                        alert('Rollos debe ser mayor a 0');
                        return false;
                    } else {
                        $("#rollos_prod").css({borderColor: ""});
                        $("#rollos_prod").focus();
                    }

                    if ($("#rollos_prod").val().length == 0) {
                        $("#rollos_prod").css({borderColor: "red"});
                        $("#rollos_prod").focus();
                        return false;
                    } else {
                        $("#rollos_prod").css({borderColor: ""});
                        $("#rollos_prod").focus();
                    }


                    if ($("#peso_conforme").val().length == 0) {
                        $("#peso_conforme").css({borderColor: "red"});
                        $("#peso_conforme").focus();
                        return false;
                    } else {
                        $("#peso_conforme").css({borderColor: ""});
                        $("#peso_conforme").focus();
                    }

                    if ($("#ord_peso").val().length == 0) {
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
                    if ($('.itms').length == 0) {
                        alert('Ingrese por lo menos un Rollo semielaborado');
                        return false;
                    }

                    d = 0;
                    n = 0;
                    j = $('.itm').length;
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
                    val1 = parseFloat(peso_conforme.value).toFixed(2);
                    ls1 = $('#sm_lote1').html();
                    sid = $('#sm_pro_id1').html();
                    lote1 = lt1 + crs + i;
                    pro_id = $('#rollo1').val();
                    cnt1 = parseFloat($('#rollos_prod').val()).toFixed();
                    var fila = '<tr class="itm" style="height:20px">' +
                            '<td style="width:100px" align="right" id="lote_semi1_' + i + '">' + ls1 + '</td>' +
                            '<td hidden id="semi_pro_id1_' + i + '">' + sid + '</td>' +
                            '<td style="width:80px" align="right" id="cantidad_' + i + '">' + cnt1 + '</td>' +
                            '<td style="width:80px" align="right" id="valor1_' + i + '">' + val1 + '</td>' +
                            '<td style="width:80px" align="right" id="valor2_' + i + '"></td>' +
                            '<td hidden id="pro_id1_' + i + '">' + pro_id + '</td>' +
                            '<td hidden id="pro_lote1_' + i + '">' + lote1 + '</td>' +
                            '<td hidden id="pro_estado1_' + i + '">0</td>' +
                            '</tr>';
                    $('#lista').append(fila);
                    //////rollo inconforme
                    val2 = parseFloat(peso_inconforme.value).toFixed(2);
                    if (val2 > 0) {
                        v = v + 1;
                        h = i + 1;
                        if (h < 10) {
                            crs2 = '00'
                        } else if (h >= 10 && i < 100) {
                            crs2 = '0'
                        } else if (h >= 100 && i < 1000) {
                            crs2 = ''
                        }
                        lote2 = lt1 + crs2 + h;
                        var fila = '<tr class="itm" style="height:20px">' +
                                '<td style="width:100px" align="right" id="lote_semi1_' + h + '">' + ls1 + '</td>' +
                                '<td hidden id="semi_pro_id1_' + h + '">' + sid + '</td>' +
                                '<td style="width:80px" align="right" id="cantidad_' + h + '">1</td>' +
                                '<td style="width:80px" align="right" id="valor1_' + h + '"></td>' +
                                '<td style="width:80px" align="right" id="valor2_' + h + '">' + val2 + '</td>' +
                                '<td hidden id="pro_id1_' + h + '">' + pro_id + '</td>' +
                                '<td hidden id="pro_lote1_' + h + '">' + lote2 + '</td>' +
                                '<td hidden id="pro_estado1_' + h + '">3</td>' +
                                '</tr>';
                        $('#lista').append(fila);
                    }
                    save(f, v, pro_id);
                }
            }


            function bloquear() {
                $('#opa_numero').attr('disabled', 'disabled');
            }

            function total(datos) {
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
                if ($('#imprimir').attr('checked') == true || $('#imprimir_inc').attr('checked') == true) {
                    auxWindow(0, datos);

                }

            }

            var boxH = $(window).height() * 0.50;
            var boxW = $(window).width() * 0.50;
            var boxHF = (boxH - 25);
            function auxWindow(a, datos) {
                switch (a) {
                    case 0:
                        wnd = "<iframe id='frmmodal' width='" + boxW + "' height='" + boxHF + "' src='../Reports/pdf_etiqueta_dinamica.php?datos=" + datos + "' frameborder='0' />";
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


            function load_semielaborado(obj) {
                $.post("actions_reg_padding.php", {op: 4, data: obj.value.toUpperCase(), id: opa_id.value},
                function (dt) {
                    dat = dt.split('_');
                    dato = dt.split('&&');
                    if (dat[0] != '' && dato[5] > 0) {
                        //core,id,lote,inv,inv_brt,ancho,estado
                        clonar_semielaborados(dato[2], dato[3], dato[4], dato[5], dato[6], dato[7], dato[8]);
                    } else {
                        alert('No existe producto semielaborado');
                        $(obj).val('');
                        $('#lote_semielaborado').focus();
//                        $('#lista_semi').html('');
//                        $('#rollos_prod').val('0');
//                        $('#cajas_prod').val('0');
//                        $('#rollos_sueltos').val('0');
//                        $('#peso_conforme').val('0');
//                        $('#ord_peso').val('0');
//                        $('#cores_pesados').val('0');
//                        $('#total_peso').val('0');
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
//
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
                        cjr = parseFloat(dt[0]) + 1;
                    } else {
                        cjr = parseFloat(dt[0]);
                    }
                    rol = parseFloat(cal_rollo) - (cjr * parseFloat($('#opp_velocidad').val()));
                    $('#cal_cajas').val(cjr);
                    $('#cal_caja_rollos').val(rol);
                }

            }

            function clonar_semielaborados(core, idp, lt, inv, inv_brt, anc, sest) {
                l = 0;
                v = 0;
                j = $('.itms').length;
                while (l < j) {
                    l++;
                    if ($('#sm_lote' + l).html() == lt) {
                        v = 1;
                    }
                }
                if (v == 0) {
                    i = j + 1;
                    var sm_fila = '<tr class="itms" style="height:20px">' +
                            '<td style="width:150px" id="sm_lote' + i + '">' + lt + '</td>' +
                            '<td style="width:100px" id="sm_numero' + i + '">' + '1' + '</td>' +
                            '<td hidden id="sm_pro_id' + i + '">' + idp + '</td>' +
                            '<td hidden id="sm_rollo' + i + '">1</td>' +
                            '<td hidden style="width:80px" align="right" id="sm_peso' + i + '">' + inv + '</td>' +
                            '<td style="width:80px" align="right" id="sm_pesobrt' + i + '">' + inv_brt + '</td>' +
                            '<td hidden style="width:80px" align="right" id="sm_core' + i + '">' + core + '</td>' +
                            '<td hidden style="width:80px" align="right" id="sm_ancho' + i + '">' + anc * 1000 + '</td>' +
                            '<td hidden style="width:80px" align="right" id="sm_estado' + i + '">' + sest + '</td>' +
                            '</tr>';
                    $('#lista_semi').append(sm_fila);
                    $('#lote_semielaborado').val('');
                    $("#scroll_semi").scrollTop($("#scroll_semi")[0].scrollHeight);
                    $("#lote_semielaborado").focus();
//                    quitar_option(lt);
                    calculos();
                } else {
                    alert('Rollo ya ingresado');
                    $("#lote_semielaborado").val('');
                    $("#lote_semielaborado").focus();
                }
            }

            function quitar_option(lt) {
                var x = document.getElementById("list_semielaborado");
                var txt = "";
                var i;
                for (i = 0; i < x.options.length; i++) {
                    if (x.options[i].value != lt) {
                        txt = txt + "<option id='" + x.options[i].id + "' value='" + x.options[i].value + "'>" + x.options[i].id + "</option>";
                    }
                }
                $('#list_semielaborado').html(txt);
                calculos();
            }

            function calculos() {
                $('#ord_peso').val('0');
                $('#cores_pesados').val('0');
                j = $('.itms').length;
                i = 0;
                crp = 0;
                prr = 0;
                pcr = 0;
                tanc = 0;

                while (i < j) {
                    i++;
                    crp = crp + parseFloat($('#sm_rollo' + i).html());
                    prr = prr + parseFloat($('#sm_peso' + i).html());
                    pcr = pcr + parseFloat($('#sm_core' + i).html());
                    tanc = tanc + parseFloat($('#sm_ancho' + i).html());
                }
                ///PESO R MADRE BRUTO 
                pnrm = prr;

//                pnrm = prr - pcr;
                ///calculo refilado
                panc = tanc / i; ///ancho rollo madre
                crl = panc / parseFloat(ord_ancho1.value);///ancho rollo madre/
                crr = crl.toString().split('.');
                carril = crr[0];

                refil = panc - parseFloat(ord_ancho1.value) * carril;
                coef = parseFloat(total_peso.value) / panc;
                prefil = refil * coef;

                ////Cantidad Rollos PT 
                rpr = parseFloat(pnrm).toFixed(2) / parseFloat(pro_peso_nt.value).toFixed(2);
                dr = rpr.toString().split('.');
                rc = dr[0];
                ///Peso NETO 
                pn = parseFloat(rc) * parseFloat(pro_peso_nt.value);
                ///peso bruto conforme
                prconf = parseFloat(rc) * parseFloat(pro_peso_brt.value);
                ///Inconforme MATERIAL NETO 
                princ = parseFloat($('#ord_peso').val()) - (parseFloat(cores_pesados.value) * (pcr / crp).toFixed(2));
//                princ = pnrm - (rc * parseFloat(pro_peso_nt.value)) + parseFloat($('#ord_peso').val()) + prefil;
                /// cajas
                cjp = parseFloat(rc) / parseFloat($('#opp_velocidad').val());
                dtp = cjp.toString().split('.');
                cjrp = parseFloat(dtp[0]);
                rslt = (parseFloat(rc) / parseFloat($('#opp_velocidad').val()) - cjrp) * parseFloat($('#opp_velocidad').val());
//                psb = (pnrm - (pn + princ));
                $('#total_peso').val(prr);
                $('#rollos_prod').val(rc);
                $('#cajas_prod').val(cjrp);
                $('#rollos_sueltos').val(rslt.toFixed(2));
                $('#peso_conforme').val(prconf.toFixed(2));
                $('#peso_inconforme').val(princ.toFixed(2));
                $('#refil').val(refil.toFixed(2));
                $('#peso_refil').val(prefil.toFixed(2));
//                $('#sobrante').val(psb.toFixed(2));
                total_semielaborado();
            }

            function calculo_manual(v) {
                if ($('#rollos_prod').val().length == 0) {
                    $('#rollos_prod').val('0');
                    return false;
                }

                if ($('#cajas_prod').val().length == 0) {
                    $('#cajas_prod').val('0');
                    return false;
                }

                if ($('#ord_peso').val().length == 0) {
                    $('#ord_peso').val('0');
                    return false;
                }

                if ($('#sobrante').val().length == 0) {
                    $('#sobrante').val('0');
                    return false;
                }

                if ($('#cores_pesados').val().length == 0) {
                    $('#cores_pesados').val('0');
                    return false;
                }

                if (parseFloat(rollos_prod.value) < 1) {
                    return false;
                }
                if (v == 1) {
                    rc = parseFloat(rollos_prod.value);
//                    pnin = parseFloat($('#ord_peso').val()) - parseFloat(cores_pesados.value) * (pcr / crp).toFixed(2);
                } else if (v == 0) {
                    rc = parseFloat(cajas_prod.value) * parseFloat($('#opp_velocidad').val());
                } else if (v == 2) {
                    rc = parseFloat(rollos_prod.value);
                    if (parseFloat(ord_peso.value) <= parseFloat(cores_pesados.value)) {
                        $('#cores_pesados').val('0');
                        return false;
                    }
                }

                j = $('.itms').length;
                i = 0;
                crp = 0;
                prr = 0;
                pcr = 0;
                tanc = 0;
                while (i < j) {
                    i++;
                    crp = crp + parseFloat($('#sm_rollo' + i).html());
                    prr = prr + parseFloat($('#sm_peso' + i).html());
                    pcr = pcr + parseFloat($('#sm_core' + i).html());
                    tanc = tanc + parseFloat($('#sm_ancho' + i).html());
                }
                ///Peso R Madre Neto 
                pnrm = prr;
                ///calculo refilado
                panc = tanc / i;
                crl = panc / parseFloat(ord_ancho1.value);
                crr = crl.toString().split('.');
                carril = crr[0];
                refil = panc - parseFloat(ord_ancho1.value) * carril;
                coef = parseFloat(total_peso.value) / panc;
                prefil = refil * coef;
                ///restar peso balanza - cores pesados
                ////Inconforme MATERIAL NETO
                pnin = parseFloat($('#ord_peso').val()) - parseFloat(cores_pesados.value) * (pcr / crp).toFixed(2);
                //princ = parseFloat(ord_peso.value) + pnin;
                ///Inconforme
                if ($('#est_no').attr('checked') == true) {
                    princ = pnin + prefil;
                } else {
                    princ = pnin;
                }
                /// Peso NETO 
                pn = pnrm - pnin;
                //Peso Rollo neto PT
                prn = pn / rc;
                //peso bruto conforme
                prb = parseFloat($('#pro_coret').val()) + parseFloat(prn);
                ///Peso BRUTO CONFORME 

                prconf = prb * rc;
                //CANTIDAD CAJAS 
                cjp = parseFloat(rc) / parseFloat($('#opp_velocidad').val());
                dtp = cjp.toString().split('.');
                cjrp = parseFloat(dtp[0]);
                rslt = (parseFloat(rc) / parseFloat($('#opp_velocidad').val()) - cjrp) * parseFloat($('#opp_velocidad').val());
                ///peso conforme - sobrante
                if (parseFloat($('#sobrante').val()) >= parseFloat($('#total_psm').html())) {
                    $('#sobrante').val('0');
                }
                if (parseFloat($('#sobrante').val()) > 0) {
                    prconf = prconf - (parseFloat($('#sobrante').val()) - (pcr / crp).toFixed(2));
                }

                $('#total_peso').val(prr);
                $('#rollos_prod').val(rc);
                $('#cajas_prod').val(cjrp);
                $('#rollos_sueltos').val(rslt.toFixed(2));
                $('#peso_conforme').val(prconf.toFixed(2));
                $('#peso_inconforme').val(princ.toFixed(2));
                $('#refil').val(refil.toFixed(2));
                $('#peso_refil').val(prefil.toFixed(2));
                $('#sobrante').val(psb.toFixed(2));

            }

            function total_semielaborado() {
                doc = document.getElementsByClassName('itms');
                n = 0;
                sum_rs = 0;
                sum_ps = 0;
                while (n < doc.length) {
                    n++;
                    if ($('#sm_numero' + n).html().length == 0) {
                        can1 = 0;
                    } else {
                        can1 = $('#sm_numero' + n).html()
                    }
                    if ($('#sm_pesobrt' + n).html().length == 0) {
                        can2 = 0;
                    } else {
                        can2 = $('#sm_pesobrt' + n).html();
                    }

                    sum_rs = sum_rs + parseFloat(can1);
                    sum_ps = sum_ps + parseFloat(can2);
                }

                $('#total_rsm').html(sum_rs.toFixed(0));
                $('#total_psm').html(sum_ps.toFixed(2));
            }

            function valFecha()
            {

                var val = $('#rpa_fecha').val();
                v = val.split('-');
                if (val.length != 10 || v[0].length != 4 || v[1].length != 2 || v[2].length != 2)
                {
                    $('#rpa_fecha').val('');
                    $('#rpa_fecha').focus();
                    alert('Formato de fecha debe ser (yyyy-mm-dd)');
                    return false;
                }
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
                    <tr><th colspan="9" >REGISTRO DE BOBINADO/CORTE<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td style="height: 200px">
                        <table  >

                            <tr>
                                <td>ORDEN#:</td>
                                <td>
                                    <input type="text" size="50" id="opa_numero" value="<?php echo $rst[opp_codigo] ?>" onchange="load_orden(this)" list="ordenes"/>
                                    <input type="hidden" size="10" id="opa_id" readonly value="<?php echo $rst[opp_id] ?>" />
                                </td>

                                <td>REGISTRO #:</td>
                                <td>
                                    <input type="text" size="12" id="rpa_numero" readonly value="<?php echo $rst[rpa_numero] ?>" />
                                </td>
                                <td>FECHA:</td>
                                <td>
                                    <input type="text" size="10" id="rpa_fecha"  value="<?php echo $rst[rpa_fecha] ?>" onchange="valFecha()" onkeyup="this.value = this.value.replace(/[^0-9-]/, '');" maxlength="10"/>
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
                                <td colspan="3">
                                    <input type="text" size="35" id="lote_semielaborado" value="<?php echo $rst[opp_codigo] ?>" onchange="load_semielaborado(this)" list="list_semielaborado"/>
<!--                                    <input type="" size="10" id="semielaborado" readonly value="<?php echo $rst[opp_id] ?>" />
                                    <input type="hidden" size="5" id="pro_cores" name="pro_cores" value="0"/>-->
                                </td>
                                <td>Etiqueta:</td>
                                <td> 
                                    <select id="etiqueta" disabled>
                                        <option value="">seleccione</option>
                                        <?php
                                        $cns_eti = $Set->lista_etiquetas();
                                        while ($rst_eti = pg_fetch_array($cns_eti)) {
                                            echo "<option value='$rst_eti[eti_id]'>$rst_eti[eti_descripcion]</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>#Etiquetas:</td>
                                <td><input type="text" readonly size="7" id="eti_numero" /> </td>
                            </tr>
                            <!------------------------------------------------------------------------- ROLLOS SEMIELABORADOS ----------------------------------------------------------------------------------->
                            <tr>
                                <td colspan="2" rowspan="1">
                                    <table>
                                        <tr>
                                            <td>
                                                <div>
                                                    <table style="width:200px">
                                                        <thead>
                                                            <tr><th colspan="3" style="width: 100px">Rollos Semielaborado</th></tr>
                                                            <tr>
                                                                <th style="width: 150px">Producto</th>
                                                                <th style="width: 100px">#Rollos</th>
                                                                <th style="width: 80px">Peso</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div id="scroll_semi" style="overflow:scroll;height:150px;overflow-x:hidden;">
                                                    <table style="width:200px" border="1">
                                                        <tbody id="lista_semi" class="tbl_frm_aux"></tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table style="width:200px" >
                                                    <tr class="add" style="height: 20px">
                                                        <td style="width:100px"></td>
                                                        <td align="right" style="width:80px;font-size: 14px" ></td>
                                                        <td align="right" style="width:80px;font-size: 14px" id="total_rsm">0</td>
                                                        <td align="right" style="width:80px;font-size: 14px" id="total_psm">0</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td rowspan="1" colspan="6">
                                    <table>
                                        <tr>
                                            <td style="font-size: 15px;">#ROLLOS PRODUCIDOS</td>
                                            <td><input type="text" size="12" id="rollos_prod" onchange="calculo_manual(1)" value="0" onkeyup="this.value = this.value.replace(/[^0-9]/, '');"/></td>
                                            <td></td>
                                            <td style="font-size: 15px;">Refil</td>
                                            <td><input type="text" size="10" id="refil" readonly value="0" onkeyup="this.value = this.value.replace(/[^0-9]/, '');"/></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 15px;">#CAJAS PRODUCIDAS</td>
                                            <td><input type="text" size="12" id="cajas_prod" onchange="calculo_manual(0)" value="0" onkeyup="this.value = this.value.replace(/[^0-9]/, '');"/></td>
                                            <td></td>
                                            <td style="font-size: 15px;">Peso Refil</td>
                                            <td><input type="text" size="10" id="peso_refil" readonly value="0" onkeyup="this.value = this.value.replace(/[^0-9]/, '');"/></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 15px;">#ROLLOS SUELTOS</td>
                                            <td><input type="text" size="10" id="rollos_sueltos" readonly value="0"/></td>
                                            <td></td>
                                            <td style="font-size: 15px;">Estirado</td>
                                            <td><input type="radio" id="est_si" name="estirado" onchange="calculo_manual(0)"/>Si
                                                <input type="radio" id="est_si" name="estirado" onchange="calculo_manual(0)" checked/>No</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 15px;">PESO CONFORME</td>
                                            <td><input type="text" size="10" id="peso_conforme" readonly value="0"/></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 15px;">PESO INCONFORME</td>
                                            <td><input type="text" size="12" id="ord_peso" value="0" onchange="calculo_manual(1)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                                <input type="hidden" size="12" id="peso_inconforme" value="0"/></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 15px;">SOBRANTE</td>
                                            <td>
                                                <input type="text" size="12" id="sobrante" value="0" onchange="calculo_manual(1)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                                <!--<input type="hidden" size="12" id="total_peso" value="0"/></td>-->
                                            </td>
                                            <td><button id="imp_sobrante" onclick="save_semielaborado()"  style="font-size: 13px;">Imprimir</button></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 15px;">#CORES PESADOS</td>
                                            <td>
                                                <input type="text" size="12" id="cores_pesados" value="0" onchange="calculo_manual(2)" onkeyup="this.value = this.value.replace(/[^0-9]/, '');"/>
                                                <input type="hidden" size="12" id="total_peso" value="0"/></td>
                                            <td><button id="pesar" onclick="clonar()"  style="font-size: 13px;">Registrar</button></td>

                                        </tr>

                                    </table>
                                </td>
                            </tr>
                            <!------------------------------------------------------------------------- DATOS GENERALES ----------------------------------------------------------------------------------->                          
                            <tr>
                                <td colspan="8" class="sbtitle" >DATOS GENERALES</td>
                            </tr>
                            <tr>
                                <td>Fecha Pedido:</td>
                                <td><input readonly type="text" name="ord_fec_pedido" id="ord_fec_pedido" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_pedido] ?>"/>
                                <td>Bodega:</td>
                                <td colspan="2">
                                    <input type="hidden" id="tip_bodega" value="<?php echo $tbodega ?>"/>
                                    <input type="text" readonly id="bodega" size="30" value="<?php echo $tbodega ?>"/>
                                </td>
                                <td>Orden Rollo Semi.:</td>
                                <td colspan="2" rowspan="2"><textarea disabled id="orden_semi" style="width: 120px"> </textarea></td>
                            </tr>
                            <tr>
                                <td>Fecha Entrega:</td>
                                <td><input type="text" readonly name="ord_fec_entrega" id="ord_fec_entrega" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_entrega] ?>"/>
                                <td>Cliente:</td>
                                <td colspan="2"><input type="text" readonly id="nombre" size="30" value="<?php echo $nombre ?>"/></td>
                            </tr>

                            <tr><td colspan="8" class="sbtitle" >PRODUCTOS</td></tr> 
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
                                            <td hidden><input  readonly type="text" size="5" style="text-align:right; font-size: 17px;" name="pro_peso_nt" id="pro_peso_nt" value="<?php echo $rst[pro_propiedad7] ?>"/></td>
                                            <td><input  readonly type="text" size="5" style="text-align:right; font-size: 17px;" name="pro_peso_brt" id="pro_peso_brt" value="<?php echo $rst[pro_propiedad7] ?>"/></td>
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
                                            <td colspan="3" style="font-size: 17px;">CAJAS FALTANTES:
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
                                    <div id="scroll" style="overflow:scroll;height:450px;overflow-x:hidden;">
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
                                                    <td style="width:600px">
                                                        IMPRIMIR CONFORME:<input type="checkbox" id="imprimir" checked>
                                                    </td>    
                                                    <td style="width:600px">
                                                        IMPRIMIR INCONFORME:<input type="checkbox" id="imprimir_inc" checked>
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
        <option value="<?php echo $rst_ord[opp_codigo] ?>" label="<?php echo $rst_ord[opp_codigo] . ' ' . substr($rst_ord[pro_descripcion], 0, 40) ?>" />
        <?php
    }
    ?>
</datalist>
<!--<datalist id="list_semielaborado">
</datalist>-->