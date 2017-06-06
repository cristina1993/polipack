<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ordenes_padding.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$Clase = new Clase_Orden_Padding();
$cns_combo = $Clase->lista_combo();
$cns_combo1 = $Clase->lista_combopro();
$cns_combomp1 = $Clase->lista_combo_empa_core('26'); ///EMPA
$cns_combomp2 = $Clase->lista_combo_empa_core('27'); ///core
$cns_combomp3 = $Set->lista_combo_empa_core2('27','26'); ///des 1
$cns_combomp4 = $Set->lista_combo_empa_core2('27','26'); ///des 2
$cns_combomp5 = $Set->lista_combo_empa_core2('27','26'); ///des 3
$cns_combomp6 = $Set->lista_combo_empa_core2('27','26'); ///des 4

$txt1 = $_GET[txt1];
$txt2 = $_GET[txt2];
$txt3 = $_GET[txt3];
$nombre = 'POLIPACK';
$cli_id = '1';
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase->lista_uno($id));
    $rst_cli = pg_fetch_array($Set->lista_clientes_codigo($rst[cli_id]));
    $nombre = $rst_cli[cli_raz_social];
    $cli_id = $rst_cli[cli_id];
    $det = 0;
    $ped_id = 0;
    $det_id = $rst[det_id];
    $read = 'readonly';
    $disabled = 'disabled';
} else {
    $id = 0;
    if (isset($_GET[prod])) {
        $rs_pv = pg_fetch_array($Set->lista_un_det_pedido($_GET[prod]));
        $rst[pro_id] = $rs_pv[pro_id];
        $rs_p = pg_fetch_array($Set->lista_un_producto($rs_pv[pro_id]));
        $rst['opp_fec_pedido'] = $rs_pv[ped_femision];
        $det = 1;
        $det_id = $_GET[prod];
        $ped_id = $rs_pv[ped_id];
        $rst[opp_observaciones] = $rs_pv[det_observacion];
        $cli_id = $rs_pv[cli_id];
        $nombre = $rs_pv[ped_nom_cliente];
        if ($rs_pv[det_unidad] == 1) {
            $rst['opp_cantidad'] = $rs_pv[det_cantidad];
        } else if ($rs_pv[det_unidad] == 2) {
            $rst['opp_cantidad'] = round($rs_pv[det_cantidad] / $rs_p[pro_peso]);
        } else if ($rs_pv[det_unidad] == 5) {
            $rst['opp_cantidad'] = $rs_pv[det_cantidad] * $rs_pv[det_lote];
        }
    } else {
        $rst[pro_id] = '';
        $rst['opp_cantidad'] = '0';
        $det = 0;
        $rst['opp_fec_pedido'] = date('Y-m-d');
        $det_id = 0;
        $ped_id = 0;
    }

    $rst['opp_fec_entrega'] = date('Y-m-d');
    $rst['pro_ancho'] = 0;
    $rst['pro_largo'] = 0;
    $rst['pro_peso'] = 0;
    $rst['pro_gramaje'] = 0;
    $rst['opp_refilado1'] = 0;
    $rst['opp_refilado2'] = 0;
    $rst['pro_mf1'] = 0;
    $rst['pro_mf2'] = 0;
    $rst['pro_mf3'] = 0;
    $rst['pro_mf4'] = 0;
    $rst['suma'] = 0;
    $rst['opp_kg1'] = 0;
    $rst['opp_kg2'] = 0;
    $rst['opp_kg3'] = 0;
    $rst['opp_kg4'] = 0;
    $rst['opp_kg5'] = 0;
    $rst['opp_kg6'] = 0;
    $rst['mp_cnt1'] = 0;
    $rst['mp_cnt2'] = 0;
    $rst['mp_cnt3'] = 0;
    $rst['mp_cnt4'] = 0;
    $rst['mp_cnt5'] = 0;
    $rst['mp_cnt6'] = 0;
    $rst['opp_velocidad'] = 0;
    $rst['opp_temp_rodillosup'] = 0;
    $rst['opp_temp_rodilloinf'] = 0;
    $rst['opp_por_espesor'] = '10';
    $rst['opp_espesor_prod'] = 0;
    $rst['pro_capa'] = 0;
    $rst['opp_eti_numero'] = 1;
    $read = '';
    $disabled = '';
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
            var id =<?php echo $id ?>;
            var det =<?php echo $det ?>;
            var det_id =<?php echo $det_id ?>;
            var ped_id =<?php echo $ped_id ?>;
            $(function () {

                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    save(id);
                });
                $('#pro_mf1,#pro_mf2,#pro_mf3,#pro_mf4').change(function () {
                    suma();
                });
                $('#pro_largo,#pro_ancho,#pro_gramaje').change(function () {
                    calcular();
                });
                if (det == 1) {
                    load_datos(<?php echo $rst[pro_id] ?>);
                }
                posicion_aux_window();
            });
            if (id == 0) {
                load_codigo(4);
            }
            function save(id) {
                var data = Array(opp_codigo.value,
                        cli_id.value,
                        pro_id.value,
                        opp_cantidad.value,
                        opp_fec_pedido.value,
                        opp_fec_entrega.value,
                        pro_ancho.value,
                        pro_largo.value,
                        pro_peso.value,
                        '0', ///pro_gramaje.value,
                        '0', ///opp_refilado1.value,
                        '0', ///opp_refilado2.value,
                        pro_mp1.value,
                        pro_mp2.value,
                        pro_mp3.value,
                        pro_mp4.value,
                        pro_mp5.value,
                        pro_mp6.value,
                        pro_mf1.value,
                        pro_mf2.value,
                        pro_mf3.value,
                        $('#pro_mf4').html(),
                        mp_cnt1.value,//opp_kg1
                        mp_cnt2.value,//opp_kg2
                        mp_cnt3.value,//opp_kg3
                        mp_cnt4.value,//opp_kg4
                        mp_cnt5.value,//opp_kg5
                        mp_cnt6.value,//opp_kg6
                        opp_velocidad.value,
                        '0', ///opp_temp_rodillosup.value,
                        '0', ///opp_temp_rodilloinf.value,
                        opp_observaciones.value.toUpperCase(),
                        det_id, ///det_id
                        opp_espesor_prod.value,
                        opp_por_espesor.value,
                        $('#opc_ancho').val(),
                        mp_cnt1.value,
                        mp_cnt2.value,
                        mp_cnt3.value,
                        mp_cnt4.value,
                        mp_cnt5.value,
                        mp_cnt6.value,
                        ped_id,
                        opp_etiqueta.value,
                        opp_eti_numero.value
                        );
                var data2 = Array();
                n = 0;
                $(".inv").each(function () {
                    n++;
                    var p = $('#pro' + n).html();
                    var l = $('#lote' + n).html();
                    var i = $('#inven' + n).html();
                    var ct = $('#cnt_inven' + n).html();
                    var f = '#fila' + n;
                    if ($(f).css('background') != '') {
                        data2.push(p + '&' + l + '&' + i+ '&' + ct);
                    }
                    
                });
                var fields = Array();
                $("#tbl_form").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (opp_codigo.value.length == 0) {
                            $("#opp_codigo").css({borderColor: "red"});
                            $("#opp_codigo").focus();
                            return false;
                        }
                        else if (cli_id.value == 0) {
                            $("#cli_id").css({borderColor: "red"});
                            $("#cli_id").focus();
                            return false;
                        }
                        else if (pro_id.value == 0) {
                            $("#pro_id").css({borderColor: "red"});
                            $("#pro_id").focus();
                            return false;
                        }
                        else if (opp_etiqueta.value == "") {
                            $("#opp_etiqueta").css({borderColor: "red"});
                            $("#opp_etiqueta").focus();
                            return false;
                        }
                        else if (opp_eti_numero.value == "") {
                            $("#opp_eti_numero").css({borderColor: "red"});
                            $("#opp_eti_numero").focus();
                            return false;
                        }
                        else if (opp_cantidad.value.length == 0 || opp_cantidad.value == 0) {
                            $("#opp_cantidad").css({borderColor: "red"});
                            $("#opp_cantidad").focus();
                            return false;
                        }
                        else if (opp_fec_pedido.value.length == 0) {
                            $("#opp_fec_pedido").css({borderColor: "red"});
                            $("#opp_fec_pedido").focus();
                            return false;
                        }
                        else if (opp_fec_entrega.value.length == 0) {
                            $("#opp_fec_entrega").css({borderColor: "red"});
                            $("#opp_fec_entrega").focus();
                            return false;
                        }
                        else if (pro_ancho.value.length == 0) {
                            $("#pro_ancho").css({borderColor: "red"});
                            $("#pro_ancho").focus();
                            return false;
                        }
                        else if (pro_largo.value.length == 0) {
                            $("#pro_largo").css({borderColor: "red"});
                            $("#pro_largo").focus();
                            return false;
                        }
                        else if (pro_peso.value.length == 0 || parseFloat(pro_peso.value) == 0) {
                            $("#pro_peso").css({borderColor: "red"});
                            $("#pro_peso").focus();
                            return false;
                        }
//                        else if (pro_gramaje.value.length == 0) {
//                            $("#pro_gramaje").css({borderColor: "red"});
//                            $("#pro_gramaje").focus();
//                            return false;
//                        } 
//                        else if (opp_refilado1.value.length == 0 || parseFloat(opp_refilado1.value) == 0) {
//                            $("#opp_refilado1").css({borderColor: "red"});
//                            $("#opp_refilado1").focus();
//                            return false;
//                        }
//                        else if (opp_refilado2.value.length == 0 || parseFloat(opp_refilado2.value) == 0) {
//                            $("#opp_refilado2").css({borderColor: "red"});
//                            $("#opp_refilado2").focus();
//                            return false;
//                        }
                        else if (pro_mp1.value.length == 0 || pro_mp1.value == 0) {
                            $("#pro_mp1").css({borderColor: "red"});
                            $("#pro_mp1").focus();
                            return false;
                        }
                        else if (pro_mp2.value.length == 0 || pro_mp2.value == 0) {
                            $("#pro_mp2").css({borderColor: "red"});
                            $("#pro_mp2").focus();
                            return false;
                        }
//                        else if (pro_mp3.value.length == 0 || pro_mp3.value == 0) {
//                            $("#pro_mp3").css({borderColor: "red"});
//                            $("#pro_mp3").focus();
//                            return false;
//                        }
//                        else if (pro_mp4.value.length == 0 || pro_mp4.value == 0) {
//                            $("#pro_mp4").css({borderColor: "red"});
//                            $("#pro_mp4").focus();
//                            return false;
//                        }
//                        else if (pro_mp5.value.length == 0 || pro_mp5.value == 0) {
//                            $("#pro_mp5").css({borderColor: "red"});
//                            $("#pro_mp5").focus();
//                            return false;
//                        }
//                        else if (pro_mp6.value.length == 0 || pro_mp6.value == 0) {
//                            $("#pro_mp6").css({borderColor: "red"});
//                            $("#pro_mp6").focus();
//                            return false;
//                        }
//                        else if ($("#suma").val() != 100) {
//                            $("#suma").css({borderColor: "red"});
//                            $("#suma").focus();
//                            return false;
//                        }
//                        else if (opp_kg1.value.length == 0) {
//                            $("#opp_kg1").css({borderColor: "red"});
//                            $("#opp_kg1").focus();
//                            return false;
//                        }
//                        else if (opp_kg2.value.length == 0) {
//                            $("#opp_kg2").css({borderColor: "red"});
//                            $("#opp_kg2").focus();
//                            return false;
//                        }
                        else if (mp_cnt3.value.length == 0) {
                            $("#mp_cnt3").css({borderColor: "red"});
                            $("#mp_cnt3").focus();
                            return false;
                        }
                        else if (mp_cnt4.value.length == 0) {
                            $("#mp_cnt4").css({borderColor: "red"});
                            $("#mp_cnt4").focus();
                            return false;
                        }
                        else if (mp_cnt5.value.length == 0) {
                            $("#mp_cnt5").css({borderColor: "red"});
                            $("#mp_cnt5").focus();
                            return false;
                        }
                        else if (mp_cnt6.value.length == 0) {
                            $("#mp_cnt6").css({borderColor: "red"});
                            $("#mp_cnt6").focus();
                            return false;
                        }
                        else if (opp_velocidad.value.length == 0) {
                            $("#opp_velocidad").css({borderColor: "red"});
                            $("#opp_velocidad").focus();
                            return false;
                        }
                        else if (pro_largo.value.length == 0) {
                            $("#pro_largo").css({borderColor: "red"});
                            $("#pro_largo").focus();
                            return false;
                        }
                        else if (opp_espesor_prod.value.length == 0) {
                            $("#opp_espesor_prod").css({borderColor: "red"});
                            $("#opp_espesor_prod").focus();
                            return false;
                        }
                        else if (opp_por_espesor.value.length == 0) {
                            $("#opp_por_espesor").css({borderColor: "red"});
                            $("#opp_por_espesor").focus();
                            return false;
                        }
//                        else if (opp_temp_rodillosup.value.length == 0) {
//                            $("#opp_temp_rodillosup").css({borderColor: "red"});
//                            $("#opp_temp_rodillosup").focus();
//                            return false;
//                        }
                        else if (parseFloat($("#pro_mf4").html()) == 0) {
                            var r = confirm('Peso Total es cero esta seguro de continuar?');
//                            $("#pro_mf4").css({borderColor: "red"});
//                            $("#pro_mf4").focus();
                            if (r == false) {
                                return false;
                            }
                        }
                        else if (parseFloat(pro_mf1.value) < parseFloat($("#pro_mf4").html())) {
                            alert('Peso Total es mayor al rango a producir');
                        }
                        else if (parseFloat(pro_mf1.value) > parseFloat($("#pro_mf4").html())) {
                            alert('Peso Total es menor al rango a producir');
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_padding.php',
                    data: {op: 0, 'data[]': data, 'data2[]': data2, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            cancelar();
                        } else {
                            loading('hidden');
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
                parent.document.getElementById('contenedor2').rows = "*,0%";
                txt1 = '<?php echo $txt1 ?>';
                txt2 = '<?php echo $txt2 ?>';
                txt3 = '<?php echo $txt3 ?>';
                if (det == 0) {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_orden_padding.php?txt1=' + txt1 + '&txt2=' + txt2 + '&txt3=' + txt3;
                } else {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_prod_pedido_venta.php?ord=<?php echo $_GET[ord] ?>&cli=<?php echo $_GET[cli] ?>&ruc=<?php echo $_GET[ruc] ?>&fecha1=<?php echo $_GET[fecha1] ?>&fecha2=<?php echo $_GET[fecha2] ?>&ped_estado=<?php echo $_GET[ped_estado] ?>';
                }
            }
            function suma() {
                var sm = parseFloat($('#pro_mf1').val()) + 0;
                sm = sm.toFixed(2);
                $('#suma').val(sm);
                var su = parseFloat($('#pro_mf1').val()) + parseFloat($('#pro_mf2').val());
                su = su.toFixed(2);
                $('#suma').val(su);
                var sum = parseFloat($('#pro_mf1').val()) + parseFloat($('#pro_mf2').val()) + parseFloat($('#pro_mf3').val());
                sum = sum.toFixed(2);
                $('#suma').val(sum);
                var s = parseFloat($('#pro_mf1').val()) + parseFloat($('#pro_mf2').val()) + parseFloat($('#pro_mf3').val()) + parseFloat($('#pro_mf4').val());
                s = s.toFixed(2);
                $('#suma').val(s);
                if (sm == 100) {
                    $('#pro_mp2').hide();
                    $('#pro_mf2').hide();
                    $('#opp_kg2').hide();
                    $('#lblporcentaje2').hide();
                    $('#lblkg2').hide();
                    $('#pro_mp3').hide();
                    $('#pro_mf3').hide();
                    $('#opp_kg3').hide();
                    $('#lblporcentaje3').hide();
                    $('#lblkg3').hide();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#opp_kg4').hide();
                    $('#lblporcentaje4').hide();
                    $('#lblkg4').hide();
                    $('#pro_mp2').val('0');
                    $('#pro_mf2').val('0');
                    $('#opp_kg2').val('0');
                    $('#pro_mp3').val('0');
                    $('#pro_mf3').val('0');
                    $('#opp_kg3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#opp_kg4').val('0');
                    $("#suma").val(sm);
                }
                else if (su == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#opp_kg2').show();
                    $('#lblporcentaje2').show();
                    $('#lblkg2').show();
                    $('#pro_mp3').hide();
                    $('#pro_mf3').hide();
                    $('#opp_kg3').hide();
                    $('#lblporcentaje3').hide();
                    $('#lblkg3').hide();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#opp_kg4').hide();
                    $('#lblporcentaje4').hide();
                    $('#lblkg4').hide();
                    $('#pro_mp3').val('0');
                    $('#pro_mf3').val('0');
                    $('#opp_kg3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#opp_kg4').val('0');
                    $("#suma").val(su);
                }
                else if (sum == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#porcentaje2').show();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#opp_kg4').hide();
                    $('#lblporcentaje4').hide();
                    $('#lblkg4').hide();
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#opp_kg4').val('0');
                    $("#suma").val(sum);
                }
                else if (su < 100 && sum < 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#opp_kg2').show();
                    $('#lblporcentaje2').show();
                    $('#lblkg2').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#opp_kg3').show();
                    $('#lblporcentaje3').show();
                    $('#lblkg3').show();
                    $('#pro_mp4').show();
                    $('#pro_mf4').show();
                    $('#opp_kg4').show();
                    $('#lblporcentaje4').show();
                    $('#lblkg4').show();
                }
            }
            function calcular() {
                var c = (parseFloat($('#pro_largo').val() * 1) * parseFloat($('#pro_ancho').val() * 1) * parseFloat($('#pro_gramaje').val() * 1) / 1000).toFixed(2);
                c = c.toFixed(2);
                $('#pro_peso').val(c);
            }
            function load_codigo(num) {
                $.post('actions_padding.php', {id: num, op: 2}, function (dt) {
                    $('#opp_codigo').val(dt);
                })
            }
            function load_datos(obj) {
                if (det == 1) {
                    pro = obj;
                } else {
                    pro = obj.value;
                }
                $.post('actions_padding.php', {id: pro, op: 3}, function (dt) {
                    dat = dt.split('&');
                    $('#pro_ancho').val(dat[0]);
                    $('#pro_largo').val(dat[1]);
                    $('#pro_peso').val(dat[2]);
                    $('#opp_espesor_prod').val(dat[17]);
                    $('#pro_peso_core').val(dat[18]);
//                    $('#pro_gramaje').val(dat[3]);
//                    $('#pro_mp1,#pro_mp2,#pro_mp3,#pro_mp4').html(dat[16]);
//                    $('#pro_mf1').val(dat[4]);
//                    $('#pro_mf2').val(dat[5]);
//                    $('#pro_mf3').val(dat[6]);
//                    $('#pro_mf4').val(dat[7]);
//                    $('#pro_mp1').val(dat[8]);
//                    $('#pro_mp2').val(dat[9]);
//                    $('#pro_mp3').val(dat[10]);
//                    $('#pro_mp4').val(dat[11]);
//                    if (dat[12] == "") {
//                        $('#opp_velocidad').val(0);
//                    } else {
//                        $('#opp_velocidad').val(dat[12]);
//                    }
//                    if (dat[13] == "") {
//                        $('#opp_temp_rodillosup').val(0);
//                    } else {
//                        $('#opp_temp_rodillosup').val(dat[13]);
//                    }
//                    if (dat[14] == "") {
//                        $('#opp_temp_rodilloinf').val(0);
//                    } else {
//                        $('#opp_temp_rodilloinf').val(dat[14]);
//                    }
//                    if (dat[15] == "") {
//                        $('#opp_observaciones').val(0);
//                    } else {
//                        $('#opp_observaciones').val(dat[15]);
//                    }

                    lista_productos();
                })
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
            function calculo() {

                if ($("#opp_cantidad").val().length == 0) {
                    cnt = 0;
                } else {
                    cnt = parseFloat($("#opp_cantidad").val());
                }

                if ($("#opp_velocidad").val().length == 0) {
                    r = 0;
                } else {
                    r = parseFloat($("#opp_velocidad").val());
                }


                if ($("#mp_cnt1").val().length == 0) {
                    pemp = 0;
                } else {
                    pemp = parseFloat($("#mp_cnt1").val());
                }
                if ($("#mp_cnt2").val().length == 0) {
                    pemp2 = 0;
                } else {
                    pemp2 = parseFloat($("#mp_cnt2").val());
                }

                if ($("#mp_cnt3").val().length == 0) {
                    pemp3 = 0;
                } else {
                    pemp3 = parseFloat($("#mp_cnt3").val());
                }
                if ($("#mp_cnt4").val().length == 0) {
                    pemp4 = 0;
                } else {
                    pemp4 = parseFloat($("#mp_cnt4").val());
                }
                if ($("#mp_cnt5").val().length == 0) {
                    pemp5 = 0;
                } else {
                    pemp5 = parseFloat($("#mp_cnt5").val());
                }
                if ($("#mp_cnt6").val().length == 0) {
                    pemp6 = 0;
                } else {
                    pemp6 = parseFloat($("#mp_cnt6").val());
                }
                if ($("#mp_kg1").val().length == 0) {
                    okg1 = 0;
                } else {
                    okg1 = parseFloat($("#mp_kg1").val());
                }
                if ($("#mp_kg2").val().length == 0) {
                    okg2 = 0;
                } else {
                    okg2 = parseFloat($("#mp_kg2").val());
                }
                if ($("#mp_kg3").val().length == 0) {
                    okg3 = 0;
                } else {
                    okg3 = parseFloat($("#mp_kg3").val());
                }
                if ($("#mp_kg4").val().length == 0) {
                    okg4 = 0;
                } else {
                    okg4 = parseFloat($("#mp_kg4").val());
                }
                if ($("#mp_kg5").val().length == 0) {
                    okg5 = 0;
                } else {
                    okg5 = parseFloat($("#mp_kg5").val());
                }
                if ($("#mp_kg6").val().length == 0) {
                    okg6 = 0;
                } else {
                    okg6 = parseFloat($("#mp_kg6").val());
                }

                ///cantidad de empaque
                if (cnt != '0' && r != '0') {
                    ct1 = cnt / r;
                } else {
                    ct1 = 0;
                }
                d = ct1.toString().split('.');
                if (parseFloat(d[1]) > 0) {
                    cnt1 = ct1 + 1;
                } else {
                    cnt1 = ct1;
                }
                $("#mp_cnt1").val(cnt1.toFixed());
                $("#mp_cnt2").val(cnt);

                pt1 = cnt1 * okg1;
                pt2 = okg2 * cnt;
                pt3 = pemp3 * okg3;
                pt4 = pemp4 * okg4;
                pt5 = pemp5 * okg5;
                pt6 = pemp6 * okg6;
                $("#opp_kg1").val(pt1.toFixed(2));
                $("#opp_kg2").val(pt2.toFixed(2));
                $("#opp_kg3").val(pt3.toFixed(2));
                $("#opp_kg4").val(pt4.toFixed(2));
                $("#opp_kg5").val(pt5.toFixed(2));
                $("#opp_kg6").val(pt6.toFixed(2));
                ///peso Material
                pmat = parseFloat($("#pro_peso").val()) * cnt;
                $("#pro_mf1").val(pmat.toFixed(2));

                ///peso Insumos

//                pins = pt1 + pt2 + pt3 + pt4 + pt5 + pt6;
                pins = pt2;
                $("#pro_mf2").val(pins.toFixed(2));

                ///peso Total
                ptotal = pmat + pins;
                $("#pro_mf3").val(ptotal.toFixed(2));

//                peso = $("#pro_peso").val();
//                peso = (parseFloat($('#pro_largo').val() * 1) * parseFloat($('#pro_ancho').val() * 1) * parseFloat($('#pro_gramaje').val() * 1) * ((parseFloat($('#opp_refilado1').val()) + parseFloat($('#opp_refilado2').val())) / 100)) / 1000;
//                cant = $("#opp_cantidad").val();
//                mf1 = $("#pro_mf1").val();
//                mf2 = $("#pro_mf2").val();
//                mf3 = $("#pro_mf3").val();
//                mf4 = $("#pro_mf4").val();
//                switch (a)
//                {
//                    case 0:
//                        if (($("#pro_peso").val() != "") && ($("#opp_cantidad").val() != "")) {
//                            opp_kg1.value = (((peso * cant) * mf1) / 100).toFixed(2);
//                        }
//                        break;
//                    case 1:
//                        if (($("#pro_peso").val() != "") && ($("#opp_cantidad").val() != "")) {
//                            opp_kg2.value = (((peso * cant) * mf2) / 100).toFixed(2);
//                        }
//                        break;
//                    case 2:
//                        if (($("#pro_peso").val() != "") && ($("#opp_cantidad").val() != "")) {
//                            opp_kg3.value = (((peso * cant) * mf3) / 100).toFixed(2);
//                        }
//                        break;
//                    case 3:
//                        if (($("#pro_peso").val() != "") && ($("#opp_cantidad").val() != "")) {
//                            opp_kg4.value = (((peso * cant) * mf4) / 100).toFixed(2);
//                        }
//                        break;
//                    case 4:
//
//                        if (($("#pro_peso").val() != "") && ($("#opp_cantidad").val() != "")) {
//                            opp_kg1.value = (((peso * cant) * mf1) / 100).toFixed(2);
//                        }
//                        break;
//                    case 5:
//                        if ($("#ord_kgtotal").val() != "" && peso != "") {
//                            ord_num_rollos.value = (((ord_kgtotal.value * peso) / 100)).toFixed(2);
//                            validacion();
//                        }
//                        break;
//                }
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
            function load_cliente(obj) {
                $.post("actions.php", {act: 63, id: obj.value, s: 0},
                function (dt) {
                    if (dt != '') {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dt);
                    } else {
                        alert('Cliente no existe \n Debe crearlo');
                        $('#nombre').focus();
                    }
                });
            }

            function load_cliente2(obj) {
                $.post("actions.php", {act: 63, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Debe crearlo');
                        $('#nombre').focus();
                    } else {
                        dat = dt.split('&');
                        $('#nombre').val(dat[1]);
                        $('#cli_id').val(dat[8]);
                    }
                    $('#con_clientes').hide();
                });
            }

            function lista_productos() {
                a = $('#pro_ancho').val() * 1;
                e = $('#opp_espesor_prod').val() * 1;
                p = $('#opp_por_espesor').val() * 1;
                opc_a = $('#opc_ancho').val();
                if (a == '') {
                    a = 0;
                }
                if (e == '') {
                    e = 0;
                }
                if (p == '') {
                    e = 0;
                }
                if (e > 0) {
                    ei = e * (1 - p / 100);
                    ef = e * (1 + p / 100);
                } else {
                    ei = e;
                    ef = e;
                }

                if (a > 0 && e > 0 && p > 0) {
                    $.post("actions_padding.php", {op: 4, id: a, ei: ei.toFixed(2), ef: ef.toFixed(2), data: opc_a},
                    function (dt) {
                        if (dt == 0) {
                            alert('no existen productos similares');
                            $('#lista').html(dt);
                        } else {
                            $('#lista').html(dt);
                        }
                        $('#pro_mf4').html('0');
                        $('#pro_mf5').html('0');
                    });
                }
                calculo();
            }

            function mover(obj) {
                if ($(obj).css('background') == '') {
                    $(obj).css('background', 'red');
                } else {
                    $(obj).css('background', '');
                }
                var tot = 0;
                var tot_r = 0;
                n = 0;
                $(".inv").each(function () {
                    n++;
                    var o = '#inven' + n;
                    var r = '#cnt_inven' + n;
                    var f = '#fila' + n;
                    if ($(f).css('background') != '') {
                        cntp = parseFloat($(o).html());
                    } else {
                        cntp = 0;
                    }
                    if ($(f).css('background') != '') {
                        cntr = parseFloat($(r).html());
                    } else {
                        cntr = 0;
                    }

                    tot = tot + cntp;
                    tot_r = tot_r + cntr;
                });
                $('#pro_mf4').html(tot.toFixed(2));
                $('#pro_mf5').html(tot_r.toFixed(0));
            }

            function load_datos_mp(obj) {
                n = obj.lang;
                $.post("actions_padding.php", {op: 5, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dt.length != 0) {
                        $('#mp_kg' + n).val(dat[0]);
                        calculo();
                    }
                });
            }
        </script>
    </head>
    <style>
        select{
            width: 125px;
        }
    </style>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form  autocomplete="off" id="frm_save">
            <style>
                .float{
                    float:left;
                }
                select{
                    margin:3px; 
                }
                input[type=text]{
                    text-transform: uppercase;
                }
            </style>

            <table id="tbl_form" border='1'>
                <thead>
                    <tr><th colspan="7">Orden de Bobinado/Corte 
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td  colspan="2" class="sbtitle" >DETALLE DE PRODUCTO </td>
                    <td class="sbtitle" >PRODUCTOS</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table style="width: 100%" border="1">
                            <tr>
                                <td>PEDIDO:</td>
                                <td><input type="text" size="13"  id="opp_codigo" readonly value="<?php echo $rst['opp_codigo'] ?>"  /></td> 
                            </tr>
                            <tr>
                                <td>FECHA PEDIDO:</td>
                                <td><input type="text" size="15" <?php echo $read ?> id="opp_fec_pedido"  value="<?php echo $rst['opp_fec_pedido'] ?>"/></td>
                            </tr>
                            <tr>
                                <td>FECHA ENTREGA:</td>
                                <td><input type="text" size="15" <?php echo $read ?> id="opp_fec_entrega"  value="<?php echo $rst['opp_fec_entrega'] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>CLIENTE:</td>
                                <td>
                                    <input type="hidden" id="cli_id" value="<?php echo $cli_id?>"/>
                                    <input type="text" id="nombre" <?php echo $read ?> list="clientes" size="20" onchange="load_cliente(this)" value="<?php echo $nombre?>"/>
                                </td>  
                            </tr>
                            <tr>
                                <td>Etiqueta:</td>
                                <td><select name="opp_etiqueta" id="opp_etiqueta">
                                        <option value="">SELECCIONE</option>
                                        <?php
                                        $cns_eti = $Set->lista_etiquetas();
                                        while ($rst_eti = pg_fetch_array($cns_eti)) {
                                            echo "<option value='$rst_eti[eti_id]'>$rst_eti[eti_descripcion]</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>#Etiqueta:</td>
                                <td><input type="number" min="1" style="width: 50px" id="opp_eti_numero" value="<?php echo $rst['opp_eti_numero'] ?>"/></td>
                            </tr>
                            <tr>
                                <td>PRODUCTO:</td>
                                <td>
                                    <select id="pro_id" onchange="load_datos(this)" <?php echo $disabled ?>>
                                        <option value="0">Seleccione</option>
                                        <?php
                                        while ($rst_combo = pg_fetch_array($cns_combo1)) {
                                            echo "<option value='$rst_combo[pro_id]' >$rst_combo[pro_descripcion]</option>";
                                        }
                                        ?>  
                                    </select>
                                    <script>
                                        var pro =<?php echo $rst[pro_id] ?>;
                                        $('#pro_id').val(pro);
                                    </script>
                                </td>
                            </tr> 
                            <tr>
                                <td>CANTIDAD:</td>
                                <td><input type="text" size="9"  id="opp_cantidad" style="text-align: right"  value="<?php echo $rst['opp_cantidad'] ?>" onchange="calculo()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                            </tr>
                            <tr>
                                <td>ANCHO:</td>
                                <td><input type="text" size="7" readonly id="pro_ancho" style="text-align: right" value="<?php echo $rst['pro_ancho'] ?>" onchange="lista_productos()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />
                                    <select id="opc_ancho" onchange="lista_productos()">
                                        <option value="0">TODOS</option>
                                        <option value="1">EXACTO</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>ESPESOR:</td>
                                <td><input type="text" size="7"  readonly id="opp_espesor_prod"  style="text-align: right" value="<?php echo $rst['opp_espesor_prod'] ?>" onchange="lista_productos()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /> 
                                    &numsp;+/-&numsp;<input type="text" size="7"  id="opp_por_espesor"  value="<?php echo $rst['opp_por_espesor'] ?>" onchange="lista_productos()"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />%
                                </td>
                            </tr>
                            <tr>
                                <td>LARGO:</td>
                                <td><input type="text" size="7" readonly id="pro_largo"  style="text-align: right" value="<?php echo $rst['pro_largo'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblmedida1">m</label></td>
                            </tr>
                            <tr>
                                <td>PESO:</td>
                                <td><input type="text" size="7"  id="pro_peso"  readonly style="text-align: right" value="<?php echo $rst['pro_peso'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                            </tr>
                            <tr>
                                <td>PESO CORE:</td>
                                <td><input type="text" size="7"  id="pro_peso_core"  readonly style="text-align: right" value="<?php echo $rst['pro_capa'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                            </tr>
                        </table>
                    </td>
                    <td colspan="7" rowspan="15">
                        <div style="overflow:scroll;height:650px;overflow-x:hidden;">
                            <table id="tbl" style="width:100%" boder="1">
                                <thead>
                                    <tr style="height:10px">
                                        <th style="width: 70px">Codigo</th>
                                        <th style="width: 230px">Descripcion</th>
                                        <th style="width: 70px">Orden</th>
                                        <th style="width: 70px">Ancho</th>
                                        <th style="width: 70px">Espesor</th>
                                        <th style="width: 70px" >Peso</th>
                                        <th style="width: 70px" >Rollos</th>
                                    </tr>
                                </thead>
                                <?php
                                $cns_det = $Clase->lista_detalle_orden($id);
                                $n = 0;
                                $totPeso = 0;
                                $cons = "";
                                while ($rst_det = pg_fetch_array($cns_det)) {
                                    $n++;

                                    echo "<tr onmousedown='mover(this)' style='background:red;' id='fila$n'>
                                                <td>$rst_det[pro_codigo] </td>
                                                <td hidden id='pro$n'>$rst_det[pro_id] </td>
                                                <td>$rst_det[pro_descripcion] </td>
                                                <td id='lote$n'>$rst_det[pro_lote]</td>
                                                <td align='right'>$rst_det[pro_ancho]</td>
                                                <td align='right'>$rst_det[pro_espesor]</td>
                                                <td class='inv' align='right' id='inven$n'>" . str_replace(",", "", number_format($rst_det[dtp_cant], 2)) . "</td>
                                                <td align='right' id='cnt_inven$n'>" . str_replace(",", "", number_format($rst_det[dtp_rollo])) . "</td>
                                              </tr>";
                                    $totPeso+=$rst_det[dtp_cant];
                                    $totCnt+=$rst_det[dtp_rollo];
                                    $cons.=" and substring(m.mov_pago from  1 for 7)!= '$rst_det[pro_lote]'";
                                }
                                ?>
                                <tbody id="lista">
                                    <?php
                                    if (!empty($id)) {
                                        if ($rst[opp_espesor_prod] > 0) {
                                            $ei = $rst[opp_espesor_prod] * (1 - $rst[opp_por_espesor] / 100);
                                            $ef = $rst[opp_espesor_prod] * (1 + $rst[opp_por_espesor] / 100);
                                        } else {
                                            $ei = $rst[opp_espesor_prod];
                                            $ef = $rst[opp_espesor_prod];
                                        }
                                        if ($rst[opc_ancho] == 0) {
                                            $txt = " and(p.pro_ancho)>=$rst[pro_ancho]";
                                        } else {
                                            $txt = " and(p.pro_ancho%$rst[opc_ancho])=0";
                                        }
                                        $cns_pro = $Clase->lista_productos_semielaborados($rst[pro_ancho], $ei, $ef, $txt, $cons);
                                        while ($rst_pro = pg_fetch_array($cns_pro)) {
                                            $rst_inv = pg_fetch_array($Clase->total_inventario($rst_pro[pro_id], $rst_pro[mov_pago]));
                                            $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                                            $cnt = $rst_inv[cnt];
                                            if (round($inv, 2) > 0) {
                                                $n++;
                                                echo "<tr onmousedown='mover(this)' id='fila$n'>
                                                <td>$rst_pro[pro_codigo] </td>
                                                <td hidden id='pro$n'>$rst_pro[pro_id] </td>
                                                <td>$rst_pro[pro_descripcion] </td>
                                                <td id='lote$n'>$rst_pro[mov_pago]</td>
                                                <td align='right'>$rst_pro[pro_ancho]</td>
                                                <td align='right'>$rst_pro[pro_espesor]</td>
                                                <td class='inv' align='right' id='inven$n'>" . str_replace(",", "", number_format($inv, 2)) . "</td>
                                                <td align='right' id='cnt_inven$n'>" . str_replace(",", "", number_format($cnt)) . "</td>
                                              </tr>";
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">Total Peso</td>
                                        <td id="pro_mf4" align="right"><?php echo str_replace(',', '', number_format($totPeso, 2)) ?></td>
                                        <td id="pro_mf5" align="right"><?php echo str_replace(',', '', number_format($totCnt)) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"  class="sbtitle" > EMPAQUE</td>
                </tr>
                <tr>
                    <td colspan="2" >
                        <table style="width:100%" boder="1">
                            <tr>
                                <td>EMPAQUE:</td>
                                <td>
                                    <select id="pro_mp1" onchange="load_datos_mp(this)" lang="1" >
                                        <option value="0">Seleccione</option>
                                        <?php
                                        while ($rst_combo = pg_fetch_array($cns_combomp1)) {
                                            echo "<option value='$rst_combo[mp_id]' >$rst_combo[mp_referencia]</option>";
                                        }
                                        ?>  
                                    </select>
                                    <input type="text" size="10" hidden id="mp_cnt1"  value="<?php echo $rst['mp_cnt1'] ?>"/>
                                    <input type="text" size="10" hidden id="mp_kg1"  value="<?php echo round($rst['opp_kg1'] / $rst['mp_cnt1'], 2) ?>"/>
                                    <input type="text" size="10" hidden id="opp_kg1"  value="<?php echo $rst['opp_kg1'] ?>"/>
                                </td>
                            </tr> 
                            <tr>
                                <td>ROLLOS POR EMPAQUE:</td>
                                <td><input type="text" size="10"  id="opp_velocidad" style="text-align: right" value="<?php echo $rst['opp_velocidad'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo()"/></td>
                            </tr>
                            <tr>
                                <td>CORE:</td>
                                <td>
                                    <select id="pro_mp2" onchange="load_datos_mp(this)" lang="2" >
                                        <option value="0">Seleccione</option>
                                        <?php
                                        while ($rst_combo2 = pg_fetch_array($cns_combomp2)) {
                                            echo "<option value='$rst_combo2[mp_id]' >$rst_combo2[mp_referencia]</option>";
                                        }
                                        ?>  
                                    </select>
                                    <input type="text" size="10" hidden id="mp_cnt2"  value="<?php echo $rst['mp_cnt2'] ?>"/>
                                    <!--<input type="text" size="10" hidden id="mp_kg2"  value="<?php echo round($rst['opp_kg2'] / $rst['mp_cnt2'], 2) ?>"/>-->
                                    <input type="text" size="10" hidden id="opp_kg2"  value="<?php echo $rst['opp_kg2'] ?>"/>
                                </td>
                            </tr> 
                            <tr>
                                <td>PESO CORE:</td>
                                <td> <input type="text" size="10"  id="mp_kg2" style="text-align: right" value="<?php echo round($rst['opp_kg2'] / $rst['mp_cnt2'], 2)?>" readonly/>Kg</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <select id="pro_mp3" onchange="load_datos_mp(this)" lang="3" >
                                        <option value="0">Seleccione</option>
                                        <?php
                                        while ($rst_combo3 = pg_fetch_array($cns_combomp3)) {
                                            echo "<option value='$rst_combo3[mp_id]' >$rst_combo3[mp_referencia]</option>";
                                        }
                                        ?>  
                                    </select>
                                </td>
                                <td><input type="text" size="10"  id="mp_cnt3"  style="text-align: right" value="<?php echo $rst['mp_cnt3'] ?>" onchange="load_datos_mp(this)"/>
                                    <input type="text" size="10" hidden id="mp_kg3"  value="<?php echo round($rst['opp_kg3'] / $rst['mp_cnt3'], 2) ?>"/>
                                    <input type="text" size="10" hidden id="opp_kg3"  value="<?php echo $rst['opp_kg3'] ?>"/>
                                </td>
                            </tr> 
                            <tr>
                                <td></td>
                                <td>
                                    <select id="pro_mp4" onchange="load_datos_mp(this)" lang="4" >
                                        <option value="0">Seleccione</option>
                                        <?php
                                        while ($rst_combo4 = pg_fetch_array($cns_combomp4)) {
                                            echo "<option value='$rst_combo4[mp_id]' >$rst_combo4[mp_referencia]</option>";
                                        }
                                        ?>  
                                    </select>
                                </td>
                                <td><input type="text" size="10"  id="mp_cnt4"  style="text-align: right" value="<?php echo $rst['mp_cnt4'] ?>" onchange="load_datos_mp(this)"/>
                                    <input type="text" size="10" hidden id="mp_kg4"  value="<?php echo round($rst['opp_kg4'] / $rst['mp_cnt4'], 2) ?>"/>
                                    <input type="text" size="10" hidden id="opp_kg4"  value="<?php echo $rst['opp_kg4'] ?>"/>
                                </td>
                            </tr> 
                            <tr>
                                <td></td>
                                <td>
                                    <select id="pro_mp5" onchange="load_datos_mp(this)" lang="5" >
                                        <option value="0">Seleccione</option>
                                        <?php
                                        while ($rst_combo5 = pg_fetch_array($cns_combomp5)) {
                                            echo "<option value='$rst_combo5[mp_id]' >$rst_combo5[mp_referencia]</option>";
                                        }
                                        ?>  
                                    </select>
                                </td>
                                <td><input type="text" size="10"  id="mp_cnt5" style="text-align: right" value="<?php echo $rst['mp_cnt5'] ?>" onchange="load_datos_mp(this)"/>
                                    <input type="text" size="10" hidden id="mp_kg5"  value="<?php echo round($rst['opp_kg5'] / $rst['mp_cnt5'], 2) ?>"/>
                                    <input type="text" size="10" hidden id="opp_kg5"  value="<?php echo $rst['opp_kg5'] ?>"/>
                                </td></tr> 
                            <tr>
                                <td></td>
                                <td>
                                    <select id="pro_mp6" onchange="load_datos_mp(this)" lang="6" >
                                        <option value="0">Seleccione</option>
                                        <?php
                                        while ($rst_combo6 = pg_fetch_array($cns_combomp6)) {
                                            echo "<option value='$rst_combo6[mp_id]' >$rst_combo6[mp_referencia]</option>";
                                        }
                                        ?>  
                                    </select>
                                </td>
                                <td><input type="text" size="10"  id="mp_cnt6" style="text-align: right" value="<?php echo $rst['mp_cnt6'] ?>" onchange="load_datos_mp(this)"/>
                                    <input type="text" size="10" hidden id="mp_kg6"  value="<?php echo round($rst['opp_kg6'] / $rst['mp_cnt6'], 2) ?>"/>
                                    <input type="text" size="10" hidden id="opp_kg6" value="<?php echo $rst['opp_kg6'] ?>"/>
                                </td>                            </tr> 
                            <tr>
                                <td>PESO NETO:</td>
                                <td><input type="text" size="10"  id="pro_mf1" style="text-align: right"  readonly value="<?php echo $rst['pro_mf1'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                            </tr>
                            <tr>
                                <td>PESO CORE:</td>
                                <td><input type="text" size="10"  id="pro_mf2" style="text-align: right" readonly value="<?php echo $rst['pro_mf2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                            </tr>
                            <tr>
                                <td>PESO BRUTO:</td>
                                <td><input type="text" size="10"  id="pro_mf3" style="text-align: right" readonly value="<?php echo $rst['pro_mf3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                            </tr>
                            <tr>
                                <td colspan="8">Observaciones:
                                    <textarea name="opp_observaciones" id="opp_observaciones" style="width:100%"><?php echo $rst[opp_observaciones] ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <?php
                            if ($x != 1) {
                                ?>
                                <button id="guardar" >Guardar</button>    
                                <?php
                            }
                            ?>
                            <button id="cancelar" >Cancelar</button>    
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>  
<datalist id="clientes">
    <?PHP
    while ($rst_cli = pg_fetch_array($cns_combo)) {
        echo "<option value='$rst_cli[cli_id]'>$rst_cli[nombres]</option>";
    }
    ?>
</datalist>
<script>
    var cli =<?php echo $rst[cli_id] ?>;
    var mp1 = '<?php echo $rst[pro_mp1] ?>';
    var mp2 = '<?php echo $rst[pro_mp2] ?>';
    var mp3 = '<?php echo $rst[pro_mp3] ?>';
    var mp4 = '<?php echo $rst[pro_mp4] ?>';
    var mp5 = '<?php echo $rst[pro_mp5] ?>';
    var mp6 = '<?php echo $rst[pro_mp6] ?>';
    var mf1 = '<?php echo $rst[pro_mf1] ?>';
    var mf2 = '<?php echo $rst[pro_mf2] ?>';
    var mf3 = '<?php echo $rst[pro_mf3] ?>';
    var mf4 = '<?php echo $rst[pro_mf4] ?>';
    var opca = '<?php echo $rst[opc_ancho] ?>';
    var eti = '<?php echo $rst[opp_etiqueta] ?>';
    $('#cli_id').val(cli);
    $('#pro_mp1').val(mp1);
    $('#pro_mp2').val(mp2);
    $('#pro_mp3').val(mp3);
    $('#pro_mp4').val(mp4);
    $('#pro_mp5').val(mp5);
    $('#pro_mp6').val(mp6);
    $('#opc_ancho').val(opca);
    $('#opp_etiqueta').val(eti);
//    var s = parseFloat(mf1) + parseFloat(mf2) + parseFloat(mf3) + parseFloat(mf4);
//    $('#suma').val(s.toFixed(2));
//    if (mf1 == 100.00) {
//        $('#pro_mp2').hide();
//        $('#pro_mf2').hide();
//        $('#opp_kg2').hide();
//        $('#lblporcentaje2').hide();
//        $('#lblkg2').hide();
//        $('#pro_mp3').hide();
//        $('#pro_mf3').hide();
//        $('#opp_kg3').hide();
//        $('#lblporcentaje3').hide();
//        $('#lblkg3').hide();
//        $('#pro_mp4').hide();
//        $('#pro_mf4').hide();
//        $('#opp_kg4').hide();
//        $('#lblporcentaje4').hide();
//        $('#lblkg4').hide();
//    }
//    if (mf2 >= 0.00) {
//        $('#pro_mp3').hide();
//        $('#pro_mf3').hide();
//        $('#opp_kg3').hide();
//        $('#lblporcentaje3').hide();
//        $('#lblkg3').hide();
//        $('#pro_mp4').hide();
//        $('#pro_mf4').hide();
//        $('#opp_kg4').hide();
//        $('#lblporcentaje4').hide();
//        $('#lblkg4').hide();
//    }
//    if (mf3 > 0.00) {
//        $('#pro_mp3').show();
//        $('#pro_mf3').show();
//        $('#opp_kg3').show();
//        $('#lblporcentaje3').show();
//        $('#lblkg3').show();
//        $('#pro_mp4').hide();
//        $('#pro_mf4').hide();
//        $('#opp_kg4').hide();
//        $('#lblporcentaje4').hide();
//        $('#lblkg4').hide();
//    }
//    if (mf4 > 0.00) {
//        $('#pro_mp4').show();
//        $('#pro_mf4').show();
//        $('#opp_kg4').show();
//        $('#lblporcentaje4').show();
//        $('#lblkg4').show();
//    }
</script>