<?php
set_time_limit(0);
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_ecocambrella.php';
$Set = new Clase_reg_ecocambrella();
$txt = trim(strtoupper($_GET[txt]));
$est = $_GET[estado];
$fec1 = $_GET[fecha1];
$fec2 = $_GET[fecha2];
$cns_maq = $Set->lista_maquinas();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $texto = "and rec_id=$id";
    $rst = pg_fetch_array($Set->lista_buscador_orden($texto));
    $rst1 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro_secundario]));
    $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst[ord_id]));
    $tot_peso_pri = ($rst[pro_ancho] * $rst[ord_largo] * $rst[ord_gramaje] * $rst[ord_num_rollos]) / 1000;
    $f1 = $tot_peso_pri - $rst_prod[peso];
    $fr1 = $rst[ord_num_rollos] - $rst_prod[rollo];
    if ($rst[ord_pro_secundario] == 0) {
        $readonly = 'readonly';
        $sol_p = '0';
        $sol_r2 = '0';
        $f2 = '0';
        $fr2 = '0';
    } else {
        $tot_peso_sec = ($rst1[pro_ancho] * $rst[ord_largo] * $rst[ord_gramaje] * $rst[ord_num_rollos]) / 1000;
        $sol_r2 = $rst[ord_num_rollos];
        $f2 = $tot_peso_sec - $rst_prod[peso2];
        $fr2 = $rst[ord_num_rollos] - $rst_prod[rollo2];
    }
} else {
    $id = 0;
    $rst[rec_fecha] = date('Y-m-d');
    $rst[ord_kgtotal] = '0';
    $rst[ord_num_rollos] = '0';
    $rst[rec_peso_primario] = '0';
    $rst_prod[peso] = '0';
    $rst[rec_peso_secundario] = '0';
    $rst_prod[peso2] = '0';
    $rst[rec_rollo_primario] = '0';
    $rst_prod[rollo] = '0';
    $rst_prod[rollo2] = '0';
    $rst[rec_rollo_secundario] = '0';
    $f1 = '0';
    $f2 = '0';
    $fr1 = '0';
    $fr2 = '0';
    $sol_p = '0';
    $sol_r2 = '0';
    $rst_sec = pg_fetch_array($Set->lista_secuencial_registro());
    if (!empty($rst_sec)) {
        $sec = substr($rst_sec[rec_numero], 4, 8) + 1;
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
    $rst[rec_numero] = 'PEC' . $tx_trs . $sec;
    $rst[ord_patch1] = 250;
    $rst[ord_patch2] = 250;
    $rst[ord_patch3] = 250;
    $rst[ord_por_tornillo1] = 0;
    $rst[ord_por_tornillo2] = 0;
    $rst[ord_por_tornillo3] = 0;
    $rst[ord_mp1] = 0;
    $rst[ord_mp2] = 0;
    $rst[ord_mp3] = 0;
    $rst[ord_mp4] = 0;
    $rst[ord_mp5] = 0;
    $rst[ord_mp6] = 0;
    $rst[ord_mp7] = 0;
    $rst[ord_mp8] = 0;
    $rst[ord_mp9] = 0;
    $rst[ord_mp10] = 0;
    $rst[ord_mp11] = 0;
    $rst[ord_mp12] = 0;
    $rst[ord_mp13] = 0;
    $rst[ord_mp14] = 0;
    $rst[ord_mp15] = 0;
    $rst[ord_mp16] = 0;
    $rst[ord_mp17] = 0;
    $rst[ord_mp18] = 0;
    $rst[ord_mf1] = 0;
    $rst[ord_mf2] = 0;
    $rst[ord_mf3] = 0;
    $rst[ord_mf4] = 0;
    $rst[ord_mf5] = 0;
    $rst[ord_mf6] = 0;
    $rst[ord_mf7] = 0;
    $rst[ord_mf8] = 0;
    $rst[ord_mf9] = 0;
    $rst[ord_mf10] = 0;
    $rst[ord_mf11] = 0;
    $rst[ord_mf12] = 0;
    $rst[ord_mf13] = 0;
    $rst[ord_mf14] = 0;
    $rst[ord_mf15] = 0;
    $rst[ord_mf16] = 0;
    $rst[ord_mf17] = 0;
    $rst[ord_mf18] = 0;
    $rst[ord_kg1] = 0;
    $rst[ord_kg2] = 0;
    $rst[ord_kg3] = 0;
    $rst[ord_kg4] = 0;
    $rst[ord_kg5] = 0;
    $rst[ord_kg6] = 0;
    $rst[ord_kg7] = 0;
    $rst[ord_kg8] = 0;
    $rst[ord_kg9] = 0;
    $rst[ord_kg10] = 0;
    $rst[ord_kg11] = 0;
    $rst[ord_kg12] = 0;
    $rst[ord_kg13] = 0;
    $rst[ord_kg14] = 0;
    $rst[ord_kg15] = 0;
    $rst[ord_kg16] = 0;
    $rst[ord_kg17] = 0;
    $rst[ord_kg18] = 0;
    $cnt_patch1 = 0;
    $cnt_patch2 = 0;
    $cnt_patch3 = 0;
    $cnt_patch4 = 0;
    $cnt_patch5 = 0;
    $cnt_patch6 = 0;
    $cnt_patch7 = 0;
    $cnt_patch8 = 0;
    $cnt_patch9 = 0;
    $cnt_patch10 = 0;
    $cnt_patch11 = 0;
    $cnt_patch12 = 0;
    $cnt_patch13 = 0;
    $cnt_patch14 = 0;
    $cnt_patch15 = 0;
    $cnt_patch16 = 0;
    $cnt_patch17 = 0;
    $cnt_patch18 = 0;
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

//                $("#scroll").scrollTop(1000);
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
                Calendar.setup({inputField: "rec_fecha", ifFormat: "%Y-%m-%d", button: "im-rec_fecha"});

            });
            function save(c, f, pr_id) {

                lote = $('#pro_lote' + c + '_' + f).html();
                estado = $('#pro_estado' + c + '_' + f).html();
                if (estado == 0) {
                    pe = $('#valor' + c + '_' + f).html();
                } else {
                    pe = $('#valorinc' + c + '_' + f).html();
                }
                var data = Array(
                        ord_id.value,
                        rec_fecha.value,
                        rec_numero.value,
                        lote,
                        pe,
                        '1', //cnt rollo
                        estado,
                        pr_id,
                        maq_id.value,
                        rec_observaciones.value
                        );
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (ord_numero.value.length == 0) {
                            $("#ord_numero").css({borderColor: "red"});
                            $("#ord_numero").focus();
                            return false;
                        }
                        if (rec_fecha.value.length == 0) {
                            $("#rec_fecha").css({borderColor: "red"});
                            $("#rec_fecha").focus();
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
                    url: 'actions_reg_ecocambrella.php',
                    data: {op: 0, 'data[]': data, id: rec_id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            peso_nt=pe - dat[4];
                            largo=peso_nt/(dat[1]*dat[3]*dat[8])*1000000;
                            var datos = Array(
                                    ord_numero.value,
                                    dat[1], //ancho
                                    pe, //p.bruto
                                    dat[3], //espesor
                                    dat[2], //largo
                                    lote,
                                    estado,
                                    etiqueta.value, //tamaño
                                    eti_numero.value, //copias
                                    rec_fecha.value, //fecha
                                    nombre.value, //cliente
                                    dat[4], //core
                                    pe - dat[4], //p.neto
                                    '', //inc
                                    '', //inc
                                    '', //inc
                                    '', //inc
                                    dat[5], //ord_id       
                                    dat[6], //pro_id       
                                    dat[7],//cli_id,
                                    largo,
                                    '<?php echo $_SESSION[usuario]?>',                        
                                    rec_observaciones.value.toUpperCase()
                                    );
                            $("#rec_observaciones").val('');
                            save_etiqueta(datos);

//                            loading('hidden');

//                            load_orden(1, pr_id, pe, lote, rec_numero.value, estado);
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }
            function save_etiqueta(datos) {
                $.ajax({
                    type: 'POST',
                    url: 'actions_reg_ecocambrella.php',
                    data: {op: 7, 'data[]': datos}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            load_orden(1, datos);
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_reg_op_ecocambrella.php?txt=' + '<?php echo $txt ?>' + '&estado=' + '<?php echo $est ?>' + '&fecha1=' + '<?php echo $fec1 ?>' + '&fecha2=' + '<?php echo $fec2 ?>';
            }


            function load_orden(op, datos) {
                $.post("actions_reg_ecocambrella.php", {op: 2, id: ord_numero.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#ord_id').val(dat[0]);
                        $('#ord_numero').val(dat[1]);
                        $('#ord_fec_pedido').val(dat[2]);
                        $('#ord_fec_entrega').val(dat[3]);
                        $('#tip_bodega').val(dat[4]);
                        $('#bodega').val(dat[5]);
                        $('#nombre').val(dat[6]);
                        $('#producto1').val(dat[7]);
                        $('#ord_num_rollos1').val(dat[8]);
                        $('#ord_kgtotal1').val(dat[9]);
                        $('#ord_ancho1').val(dat[10] * 1000);
                        $('#ord_carril1').val(dat[11]);
                        $('#producto2').val(dat[12]);
                        $('#ord_num_rollos2').val(dat[13]);
                        $('#ord_kgtotal2').val(dat[14]);
                        $('#ord_ancho2').val(dat[15] * 1000);
                        $('#ord_carril2').val(dat[16]);
                        $('#producto3').val(dat[17]);
                        $('#ord_num_rollos3').val(dat[18]);
                        $('#ord_kgtotal3').val(dat[19]);
                        $('#ord_ancho3').val(dat[20] * 1000);
                        $('#ord_carril3').val(dat[21]);
                        $('#producto4').val(dat[22]);
                        $('#ord_num_rollos4').val(dat[23]);
                        $('#ord_kgtotal4').val(dat[24]);
                        $('#ord_ancho4').val(dat[25] * 1000);
                        $('#ord_carril4').val(dat[26]);
                        $('#ord_kgtotal_rep').val(dat[27]);
                        $('#ord_rep_ancho').val(dat[28]);
                        $('#ord_rep_carril').val(dat[29]);
                        $('#sumakg').val(dat[30]);
                        $('#sumam').val(dat[31]);
                        $('#tle_pro1').html(dat[7]);
                        $('#tle_pro2').html(dat[12]);
                        $('#tle_pro3').html(dat[17]);
                        $('#tle_pro4').html(dat[22]);
                        $('#ord_peso').val('');
                        if (dat[7].trim().length != 0) {
                            $('#guardar1').attr('disabled', false);
                            $('#inconforme1').attr('disabled', false);
                            $('#tab1').attr('width', '200px');
                            $('#tab2').attr('width', '200px');
                            $('#tab3').attr('width', '200px');
                            $('#pro_pallet1').attr('disabled', false);
                        } else {
                            $('#guardar1').attr('disabled', true);
                            $('#inconforme1').attr('disabled', true);
                            $('#pro_pallet1').attr('disabled', true);
                        }
                        if (dat[12].trim().length != 0) {
                            $('#guardar2').attr('disabled', false);
                            $('#inconforme2').attr('disabled', false);
                            $('#tab1').attr('width', '400px');
                            $('#tab2').attr('width', '400px');
                            $('#tab3').attr('width', '400px');
                            $('.producto2').attr('hidden', false);
                            $('#pro_pallet2').attr('disabled', false);
                        } else {
                            $('#guardar2').attr('disabled', true);
                            $('#inconforme2').attr('disabled', true);
                            $('.producto2').attr('hidden', true);
                            $('#pro_pallet2').attr('disabled', true);
                        }
                        if (dat[17].trim().length != 0) {
                            $('#guardar3').attr('disabled', false);
                            $('#inconforme3').attr('disabled', false);
                            $('#tab1').attr('width', '600px');
                            $('#tab2').attr('width', '600px');
                            $('#tab3').attr('width', '600px');
                            $('.producto3').attr('hidden', false);
                            $('#pro_pallet3').attr('disabled', false);
                        } else {
                            $('#guardar3').attr('disabled', true);
                            $('#inconforme3').attr('disabled', true);
                            $('.producto3').attr('hidden', true);
                            $('#pro_pallet3').attr('disabled', true);
                        }
                        if (dat[22].trim().length != 0) {
                            $('#guardar4').attr('disabled', false);
                            $('#inconforme4').attr('disabled', false);
                            $('#tab1').attr('width', '800px');
                            $('#tab2').attr('width', '800px');
                            $('#tab3').attr('width', '800px');
                            $('.producto4').attr('hidden', false);
                            $('#pro_pallet4').attr('disabled', false);
                        } else {
                            $('#guardar4').attr('disabled', true);
                            $('#inconforme4').attr('disabled', true);
                            $('.producto4').attr('hidden', true);
                            $('#pro_pallet4').attr('disabled', true);
                        }
                        $('#pro_pallet1').val(dat[32]);
                        $('#pro_pallet2').val(dat[33]);
                        $('#pro_pallet3').val(dat[34]);
                        $('#pro_pallet4').val(dat[35]);
                        $('#rollo1').attr("class", '1_' + dat[32]);
                        $('#rollo2').attr("class", '2_' + dat[33]);
                        $('#rollo3').attr("class", '3_' + dat[34]);
                        $('#rollo4').attr("class", '4_' + dat[35]);
                        $('#ord_por_tornillo1').val(dat[36]);
                        $('#ord_por_tornillo2').val(dat[37]);
                        $('#ord_por_tornillo3').val(dat[38]);
                        $('#ord_mp1,#ord_mp2,#ord_mp3,#ord_mp4,#ord_mp5,#ord_mp6,#ord_mp7,#ord_mp8,#ord_mp9,#ord_mp10,#ord_mp11,#ord_mp12,#ord_mp13,#ord_mp14,#ord_mp15,#ord_mp16,#ord_mp17,#ord_mp18').html(dat[39]);
                        $('#ord_mp1').val(dat[40]);
                        $('#ord_mp2').val(dat[41]);
                        $('#ord_mp3').val(dat[42]);
                        $('#ord_mp4').val(dat[43]);
                        $('#ord_mp5').val(dat[44]);
                        $('#ord_mp6').val(dat[45]);
                        $('#ord_mp7').val(dat[46]);
                        $('#ord_mp8').val(dat[47]);
                        $('#ord_mp9').val(dat[48]);
                        $('#ord_mp10').val(dat[49]);
                        $('#ord_mp11').val(dat[50]);
                        $('#ord_mp12').val(dat[51]);
                        $('#ord_mp13').val(dat[52]);
                        $('#ord_mp14').val(dat[53]);
                        $('#ord_mp15').val(dat[54]);
                        $('#ord_mp16').val(dat[55]);
                        $('#ord_mp17').val(dat[56]);
                        $('#ord_mp18').val(dat[57]);
                        $('#ord_mf1').val(dat[58]);
                        $('#ord_mf2').val(dat[59]);
                        $('#ord_mf3').val(dat[60]);
                        $('#ord_mf4').val(dat[61]);
                        $('#ord_mf5').val(dat[62]);
                        $('#ord_mf6').val(dat[63]);
                        $('#ord_mf7').val(dat[64]);
                        $('#ord_mf8').val(dat[65]);
                        $('#ord_mf9').val(dat[66]);
                        $('#ord_mf10').val(dat[67]);
                        $('#ord_mf11').val(dat[68]);
                        $('#ord_mf12').val(dat[69]);
                        $('#ord_mf13').val(dat[70]);
                        $('#ord_mf14').val(dat[71]);
                        $('#ord_mf15').val(dat[72]);
                        $('#ord_mf16').val(dat[73]);
                        $('#ord_mf17').val(dat[74]);
                        $('#ord_mf18').val(dat[75]);
                        $('#ord_kg1').val(dat[76]);
                        $('#ord_kg2').val(dat[77]);
                        $('#ord_kg3').val(dat[78]);
                        $('#ord_kg4').val(dat[79]);
                        $('#ord_kg5').val(dat[80]);
                        $('#ord_kg6').val(dat[81]);
                        $('#ord_kg7').val(dat[82]);
                        $('#ord_kg8').val(dat[83]);
                        $('#ord_kg9').val(dat[84]);
                        $('#ord_kg10').val(dat[85]);
                        $('#ord_kg11').val(dat[86]);
                        $('#ord_kg12').val(dat[87]);
                        $('#ord_kg13').val(dat[88]);
                        $('#ord_kg14').val(dat[89]);
                        $('#ord_kg15').val(dat[90]);
                        $('#ord_kg16').val(dat[91]);
                        $('#ord_kg17').val(dat[92]);
                        $('#ord_kg18').val(dat[93]);
                        $('#lista').html(dat[94]);
                        $('#total1').html(parseFloat(dat[95]).toFixed(2));
                        $('#total2').html(parseFloat(dat[96]).toFixed(2));
                        $('#total3').html(parseFloat(dat[97]).toFixed(2));
                        $('#total4').html(parseFloat(dat[98]).toFixed(2));
                        $('#totalinc1').html(parseFloat(dat[99]).toFixed(2));
                        $('#totalinc2').html(parseFloat(dat[100]).toFixed(2));
                        $('#totalinc3').html(parseFloat(dat[101]).toFixed(2));
                        $('#totalinc4').html(parseFloat(dat[102]).toFixed(2));
                        if (dat[103] == '') {
                            $('#pro_peso1').val('0');
                        } else {
                            $('#pro_peso1').val(parseFloat(dat[103]).toFixed(2));
                        }
                        if (dat[104] == '') {
                            $('#pro_peso2').val('0');
                        } else {
                            $('#pro_peso2').val(parseFloat(dat[104]).toFixed(2));
                        }
                        if (dat[105] == '') {
                            $('#pro_peso3').val('0');
                        } else {
                            $('#pro_peso3').val(parseFloat(dat[105]).toFixed(2));
                        }
                        if (dat[106] == '') {
                            $('#pro_peso4').val('0');
                        } else {
                            $('#pro_peso4').val(parseFloat(dat[106]).toFixed(2));
                        }
                        if (dat[107] == '') {
                            $('#pro_metraje1').val('0');
                        } else {
                            $('#pro_metraje1').val(parseFloat(dat[107]).toFixed(2));
                        }
                        if (dat[108] == '') {
                            $('#pro_metraje2').val('0');
                        } else {
                            $('#pro_metraje2').val(parseFloat(dat[108]).toFixed(2));
                        }
                        if (dat[109] == '') {
                            $('#pro_metraje3').val('0');
                        } else {
                            $('#pro_metraje3').val(parseFloat(dat[109]).toFixed(2));
                        }
                        if (dat[110] == '') {
                            $('#pro_metraje4').val('0');
                        } else {
                            $('#pro_metraje4').val(parseFloat(dat[110]).toFixed(2));
                        }
                        $('#ord_patch').val(dat[111]);
                        $('#ord_patch2').val(dat[112]);
                        $('#ord_patch3').val(dat[113]);
                        $('#etiqueta').val(dat[114]);
                        $('#ord_observaciones').html(dat[115]);
                        $('#eti_numero').val(dat[116]);
                        $("#scroll").scrollTop($("#scroll")[0].scrollHeight);
                        validacion();
                        if (op != 1) {
                            cambio_producto(1);
                        }
                    } else {
                        alert('Orden no existe');
                        $('#ord_numero').focus();
                        $('#ord_id').val("");
                        $('#ord_numero').val("");
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
                        $('#producto2').val("");
                        $('#ord_num_rollos2').val("0");
                        $('#ord_kgtotal2').val("0");
                        $('#ord_ancho2').val("0");
                        $('#ord_carril2').val("0");
                        $('#producto3').val("");
                        $('#ord_num_rollos3').val("0");
                        $('#ord_kgtotal3').val("0");
                        $('#ord_ancho3').val("0");
                        $('#ord_carril3').val("0");
                        $('#producto4').val("");
                        $('#ord_num_rollos4').val("0");
                        $('#ord_kgtotal4').val("0");
                        $('#ord_ancho4').val("0");
                        $('#ord_carril4').val("0");
                        $('#ord_kgtotal_rep').val("0");
                        $('#ord_rep_ancho').val("0");
                        $('#ord_rep_carril').val("0");
                        $('#sumakg').val("0");
                        $('#sumam').val("0");
                        $('#tle_pro1').html("");
                        $('#tle_pro2').html("");
                        $('#tle_pro3').html("");
                        $('#tle_pro4').html("");
                        $('#rollo1').val("");
                        $('#rollo2').val("");
                        $('#rollo3').val("");
                        $('#rollo4').val("");
                        $('#guardar1').attr('disabled', true);
                        $('#inconforme1').attr('disabled', true);
                        $('#guardar2').attr('disabled', true);
                        $('#inconforme2').attr('disabled', true);
                        $('#guardar3').attr('disabled', true);
                        $('#inconforme3').attr('disabled', true);
                        $('#guardar4').attr('disabled', true);
                        $('#inconforme4').attr('disabled', true);
                        $('#rollo1').attr("class", '');
                        $('#rollo2').attr("class", '');
                        $('#rollo3').attr("class", '');
                        $('#rollo4').attr("class", '');
                        $('#ord_por_tornillo1').val("0");
                        $('#ord_por_tornillo2').val("0");
                        $('#ord_por_tornillo3').val("0");
                        $('#ord_mp1,#ord_mp2,#ord_mp3,#ord_mp4,#ord_mp5,#ord_mp6,#ord_mp7,#ord_mp8,#ord_mp9,#ord_mp10,#ord_mp11,#ord_mp12,#ord_mp13,#ord_mp14,#ord_mp15,#ord_mp16,#ord_mp17,#ord_mp18').html('');
                        $('#ord_mp1').val("0");
                        $('#ord_mp2').val("0");
                        $('#ord_mp3').val("0");
                        $('#ord_mp4').val("0");
                        $('#ord_mp5').val("0");
                        $('#ord_mp6').val("0");
                        $('#ord_mp7').val("0");
                        $('#ord_mp8').val("0");
                        $('#ord_mp9').val("0");
                        $('#ord_mp10').val("0");
                        $('#ord_mp11').val("0");
                        $('#ord_mp12').val("0");
                        $('#ord_mp13').val("0");
                        $('#ord_mp14').val("0");
                        $('#ord_mp15').val("0");
                        $('#ord_mp16').val("0");
                        $('#ord_mp17').val("0");
                        $('#ord_mp18').val("0");
                        $('#ord_mf1').val("0");
                        $('#ord_mf2').val("0");
                        $('#ord_mf3').val("0");
                        $('#ord_mf4').val("0");
                        $('#ord_mf5').val("0");
                        $('#ord_mf6').val("0");
                        $('#ord_mf7').val("0");
                        $('#ord_mf8').val("0");
                        $('#ord_mf9').val("0");
                        $('#ord_mf10').val("0");
                        $('#ord_mf11').val("0");
                        $('#ord_mf12').val("0");
                        $('#ord_mf13').val("0");
                        $('#ord_mf14').val("0");
                        $('#ord_mf15').val("0");
                        $('#ord_mf16').val("0");
                        $('#ord_mf17').val("0");
                        $('#ord_mf18').val("0");
                        $('#ord_kg1').val("0");
                        $('#ord_kg2').val("0");
                        $('#ord_kg3').val("0");
                        $('#ord_kg4').val("0");
                        $('#ord_kg5').val("0");
                        $('#ord_kg6').val("0");
                        $('#ord_kg7').val("0");
                        $('#ord_kg8').val("0");
                        $('#ord_kg9').val("0");
                        $('#ord_kg10').val("0");
                        $('#ord_kg11').val("0");
                        $('#ord_kg12').val("0");
                        $('#ord_kg13').val("0");
                        $('#ord_kg14').val("0");
                        $('#ord_kg15').val("0");
                        $('#ord_kg16').val("0");
                        $('#ord_kg17').val("0");
                        $('#ord_kg18').val("0");
                        $('#cnt_patch1').val("0");
                        $('#cnt_patch2').val("0");
                        $('#cnt_patch3').val("0");
                        $('#cnt_patch4').val("0");
                        $('#cnt_patch5').val("0");
                        $('#cnt_patch6').val("0");
                        $('#cnt_patch7').val("0");
                        $('#cnt_patch8').val("0");
                        $('#cnt_patch9').val("0");
                        $('#cnt_patch10').val("0");
                        $('#cnt_patch11').val("0");
                        $('#cnt_patch12').val("0");
                        $('#cnt_patch13').val("0");
                        $('#cnt_patch14').val("0");
                        $('#cnt_patch15').val("0");
                        $('#cnt_patch16').val("0");
                        $('#cnt_patch17').val("0");
                        $('#cnt_patch18').val("0");
                        $('#tot_por_tornillo1').val("0");
                        $('#tot_por_tornillo2').val("0");
                        $('#tot_por_tornillo3').val("0");
                        $('#tot_kg_tornillo1').val("0");
                        $('#tot_kg_tornillo2').val("0");
                        $('#tot_kg_tornillo3').val("0");
                        $('#tot_pch_tornillo1').val("0");
                        $('#tot_pch_tornillo2').val("0");
                        $('#tot_pch_tornillo3').val("0");
                        $('#pro_peso1').val('0');
                        $('#pro_peso2').val('0');
                        $('#pro_peso3').val('0');
                        $('#pro_peso4').val('0');
                        $('#pro_metraje1').val('0');
                        $('#pro_metraje2').val('0');
                        $('#pro_metraje3').val('0');
                        $('#pro_metraje4').val('0');
                        $('#etiqueta').val('');
                        $('#ord_observaciones').html('');
                        $('#eti_numero').val('0');
                    }
                });
                if (op == 1) {
                    auxWindow(0, datos);
                }

            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function validar() {
                pp = $('#rec_peso_primario').val();
                s1 = $('#sol1').val();
                ps = $('#rec_peso_secundario').val();
                s2 = $('#sol2').val();
                rp = $('#rec_rollo_primario').val();
                r1 = $('#sol_r1').val();
                rs = $('#rec_rollo_secundario').val();
                r2 = $('#sol_r2').val();
                pp1 = $('#prod1').val();
                pp2 = $('#prod2').val();
                pr1 = $('#prod_r1').val();
                pr2 = $('#prod_r2').val();
                mp =<?php echo $rst[rec_peso_primario] ?>;
                ms =<?php echo $rst[rec_peso_secundario] ?>;
                mr1 =<?php echo $rst[rec_rollo_primario] ?>;
                mr2 =<?php echo $rst[rec_rollo_secundario] ?>;
                por1 = (parseFloat(s1) * 50) / 100;
                por2 = (parseFloat(s2) * 50) / 100;
                por3 = (parseFloat(r1) * 50) / 100;
                por4 = (parseFloat(r2) * 50) / 100;
                s1 = parseFloat(s1) + por1;
                s2 = parseFloat(s2) + por2;
                r1 = parseFloat(r1) + por3;
                r2 = parseFloat(r2) + por4;
                if (id == 0) {
                    sp1 = parseFloat(pp1) + parseFloat(pp);
                    sp2 = parseFloat(pp2) + parseFloat(ps);
                    sr1 = parseFloat(pr1) + parseFloat(rp);
                    sr2 = parseFloat(pr2) + parseFloat(rs);
                } else {
                    sp1 = parseFloat(pp1) + parseFloat(pp) - parseFloat(mp);
                    sp2 = parseFloat(pp2) + parseFloat(ps) - parseFloat(ms);
                    sr1 = parseFloat(pr1) + parseFloat(rp) - parseFloat(mr1);
                    sr2 = parseFloat(pr2) + parseFloat(rs) - parseFloat(mr2);
                }
                if (sp1 > parseFloat(s1)) {
                    alert('La suma del peso producido y faltante del \n primer producto es mayor al solicitado');
                    $("#rec_peso_primario").css({borderColor: "red"});
                    $("#rec_peso_primario").focus();
                    $("#rec_peso_primario").val('');
                    return false;
                }
                if (sp2 > parseFloat(s2)) {
                    alert('La suma del peso producido y faltante del \n segundo producto es mayor al solicitado');
                    $("#rec_peso_secundario").css({borderColor: "red"});
                    $("#rec_peso_secundario").focus();
                    $("#rec_peso_secundario").val('');
                    return false;
                }
                if (parseFloat(sr1) > parseFloat(r1)) {
                    alert('La suma de los rollos producidos y faltantes del \n primer producto es mayor al solicitado');
                    $("#rec_rollo_primario").css({borderColor: "red"});
                    $("#rec_rollo_primario").focus();
                    $("#rec_rollo_primario").val('');
                    return false;
                }
                if (parseFloat(sr2) > parseFloat(r2)) {
                    alert('La suma de los rollos producidos y faltantes del \n segundo producto es mayor al solicitado');
                    $("#rec_rollo_secundario").css({borderColor: "red"});
                    $("#rec_rollo_secundario").focus();
                    $("#rec_rollo_secundario").val('');
                    return false;
                }
            }

            function tab(e, op) {
                var ch0 = e.keyCode;
                if (ch0 == 9) {
                    e.preventDefault();
                    switch (op)
                    {

                        case 0:
                            $('#rec_peso_primario').focus();
                            break;
                        case 1:
                            $('#rec_rollo_primario').focus();
                            break;
                        case 2:
                            $('#rec_peso_secundario').focus();
                            break;
                        case 3:
                            $('#rec_rollo_secundario').focus();
                            break;
                        case 4:
                            $('#rec_lote').focus();
                            break;
                        case 5:
                            $('#rec_lote2').focus();
                            break;
                    }
                }
            }
            function peso() {
                p1 = $('#producto1').val().trim();
                p2 = $('#producto2').val().trim();
                p3 = $('#producto3').val().trim();
                p4 = $('#producto4').val().trim();
                v = $('#ord_peso').val();
                if (p1.length != 0) {
                    $('#rollo1').val(v);
                } else {
                    $('#rollo1').val("");
                }
                if (p2.length != 0) {
                    $('#rollo2').val(v);
                } else {
                    $('#rollo2').val("");
                }
                if (p3.length != 0) {
                    $('#rollo3').val(v);
                } else {
                    $('#rollo3').val("");
                }
                if (p4.length != 0) {
                    $('#rollo4').val(v);
                } else {
                    $('#rollo4').val("");
                }
            }

            function clonar(c, op) {
                fecha = valFecha();
                if (fecha != false) {
                    if (maq_id.value == 0) {
                        $("#maq_id").css({borderColor: "red"});
                        $("#maq_id").focus();
                        return false;
                    }
                    if (op == 0 && $('#pro_pallet1').attr('checked') == false && $('#pro_pallet2').attr('checked') == false && $('#pro_pallet3').attr('checked') == false && $('#pro_pallet4').attr('checked') == false) {
                        alert('Seleccione pallet de producto');
                        return false;
                    }
                    if(isNaN($('#ord_peso').val())==true){
                        alert('Peso incorrecto');
                        $('#ord_peso').val('');
                        $('#ord_peso').focus();
                        return false;
                    }
                    if ($('#ord_peso').val().length != 0) {
//                    calculo de pallets y rollos 
                        if (op == 0) {
                            if (parseFloat($('#pal_rollos').html()) == 0) {
                                pal_num = parseFloat($('#pal_numero').html()) + 1;
                                $('#pal_numero').html(pal_num);
                            }
                            pal_rol = parseFloat($('#pal_rollos').html()) + 1;
                            $('#pal_rollos').html(pal_rol);
                            pal_pes = parseFloat($('#pal_peso').html()) + parseFloat($('#ord_peso').val());
                            $('#pal_peso').html(pal_pes);
                        }

                        d = 0;
                        n = 0;
                        j = $('.itm').length;
                        id1 = $('#rollo1').attr('class');
                        id2 = $('#rollo2').attr('class');
                        id3 = $('#rollo3').attr('class');
                        id4 = $('#rollo4').attr('class');
                        lt1 = $('#ord_numero').val().replace('-', '') + '1';
                        lt2 = $('#ord_numero').val().replace('-', '') + '2';
                        lt3 = $('#ord_numero').val().replace('-', '') + '3';
                        lt4 = $('#ord_numero').val().replace('-', '') + '4';
                        var v = 0;
                        var f = 0;
                        var val = '';
                        var vali = '';
                        var pid = '';
                        var lot = '';
                        var est = '';
                        var sec = '';
                        i = 0;
                        while (i < j) {
                            i++;
                            if (($('#valor' + c + '_' + i).html() == '' && v == 0) && ($('#valorinc' + c + '_' + i).html() == '' && v == 0)) {
                                if (i < 10) {
                                    crs = '00'
                                } else if (i >= 10 && i < 100) {
                                    crs = '0'
                                } else if (i >= 100 && i < 1000) {
                                    crs = ''
                                }
                                sec = crs + i;
                                val = '#valor' + c + '_' + i;
                                vali = '#valorinc' + c + '_' + i;
                                pid = '#pro_id' + c + '_' + i;
                                lot = '#pro_lote' + c + '_' + i;
                                est = '#pro_estado' + c + '_' + i;
                                v = 1;
                                f = i;
                            }
                        }
                        if (v == 0) {
                            i = 0;
                            i = j + 1;
                            f = i;
                        }
                        if (i < 10) {
                            crs = '00'
                        } else if (i >= 10 && i < 100) {
                            crs = '0'
                        } else if (i >= 100 && i < 1000) {
                            crs = ''
                        }
                        if (c == 1) {
                            if (op == 0) {
                                val1 = parseFloat(rollo1.value).toFixed(2);
                                valinc1 = '';
                            } else {
                                valinc1 = parseFloat(rollo1.value).toFixed(2);
                                val1 = '';
                            }
                            lote1 = lt1 + crs + i;
                            id = id1;
                            lote = lote1;
                        } else {
                            val1 = '';
                            valinc1 = '';
                            lote1 = '';
                        }
                        if (c == 2) {
                            if (op == 0) {
                                val2 = parseFloat(rollo2.value).toFixed(2);
                                valinc2 = '';
                            } else {
                                valinc2 = parseFloat(rollo2.value).toFixed(2);
                                val2 = '';
                            }
                            lote2 = lt2 + crs + i;
                            id = id2;
                            lote = lote2;
                        } else {
                            val2 = '';
                            valinc2 = '';
                            lote2 = '';
                        }
                        if (c == 3) {
                            if (op == 0) {
                                val3 = parseFloat(rollo3.value).toFixed(2);
                                valinc3 = '';
                            } else {
                                valinc3 = parseFloat(rollo3.value).toFixed(2);
                                val3 = '';
                            }
                            lote3 = lt3 + crs + i;
                            id = id3;
                            lote = lote3;
                        } else {
                            val3 = '';
                            valinc3 = '';
                            lote3 = '';
                        }
                        if (c == 4) {
                            if (op == 0) {
                                val4 = parseFloat(rollo4.value).toFixed(2);
                                valinc4 = '';
                            } else {
                                valinc4 = parseFloat(rollo4.value).toFixed(2);
                                val4 = '';
                            }
                            lote4 = lt4 + crs + i;
                            id = id4;
                            lote = lote4;
                        } else {
                            val4 = '';
                            valinc4 = '';
                            lote4 = '';
                        }

                        if ($('#tle_pro2').attr('hidden') == true) {
                            hid2 = 'hidden';
                        } else {
                            hid2 = '';
                        }
                        if ($('#tle_pro3').attr('hidden') == true) {
                            hid3 = 'hidden';
                        } else {
                            hid3 = '';
                        }
                        if ($('#tle_pro4').attr('hidden') == true) {
                            hid4 = 'hidden';
                        } else {
                            hid4 = '';
                        }
                        if (v == 0) {
                            var fila = '<tr class="itm" style="height:20px">' +
                                    '<td style="width: 10px" align="right">' + i + '</td>' +
                                    '<td style="width: 80px" align="right" id="valor1_' + i + '">' + val1 + '</td>' +
                                    '<td style="width: 80px" align="right" id="valorinc1_' + i + '">' + valinc1 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_id1_' + i + '">' + id1 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_lote1_' + i + '">' + lote1 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_estado1_' + i + '">' + op + '</td>' +
                                    '<td' + hid2 + ' style="width: 80px" align="right" id="valor2_' + i + '">' + val2 + '</td>' +
                                    '<td' + hid2 + ' style="width: 80px" align="right" id="valorinc2_' + i + '">' + valinc2 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_id2_' + i + '">' + id2 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_lote2_' + i + '">' + lote2 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_estado2_' + i + '">' + op + '</td>' +
                                    '<td ' + hid3 + ' style="width: 80px" align="right" id="valor3_' + i + '">' + val3 + '</td>' +
                                    '<td ' + hid3 + ' style="width: 80px" align="right" id="valorinc3_' + i + '">' + valinc3 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_id3_' + i + '">' + id3 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_lote3_' + i + '">' + lote3 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_estado3_' + i + '">' + op + '</td>' +
                                    '<td ' + hid4 + ' style="width: 80px" align="right" id="valor4_' + i + '">' + val4 + '</td>' +
                                    '<td ' + hid4 + ' style="width: 80px" align="right" id="valorinc4_' + i + '">' + valinc4 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_id4_' + i + '">' + id4 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_lote4_' + i + '">' + lote4 + '</td>' +
                                    '<td style="width: 80px" hidden id="pro_estado4_' + i + '">' + op + '</td>' +
                                    '</tr>';
                            $('#lista').append(fila);
                        } else {
                            if (c == 1) {
                                if (op == 0) {
                                    valor = val1;
                                    valorinc = '';
                                } else {
                                    valorinc = valinc1;
                                    valor = '';
                                }
                                id = id1;
                                lote = lt1 + sec;
                            } else if (c == 2) {
                                if (op == 0) {
                                    valor = val2;
                                    valorinc = '';
                                } else {
                                    valorinc = valinc2;
                                    valor = '';
                                }
                                id = id2;
                                lote = lt2 + sec;
                            } else if (c == 3) {
                                if (op == 0) {
                                    valor = val3;
                                    valorinc = '';
                                } else {
                                    valorinc = valinc3;
                                    valor = '';
                                }
                                id = id3;
                                lote = lt3 + sec;
                            } else if (c == 4) {
                                if (op == 0) {
                                    valor = val4;
                                    valorinc = '';
                                } else {
                                    valorinc = valinc4;
                                    valor = '';
                                }
                                id = id4;
                                lote = lt4 + sec;
                            }
                            $(val).html(valor);
                            $(vali).html(valorinc);
                            $(pid).html(id);
                            $(lot).html(lote);
                            $(est).html(op);
                        }
                        dt = id.split('_');
                        pro_id = dt[1];
                        save(dt[0], f, pro_id);
                        //                    auxWindow(0, pro_id, $('#ord_peso').val(), lote)
                    }
                    rollo1.value = '';
                    rollo1.class = '';
                    rollo2.value = '';
                    rollo2.class = '';
                    rollo3.value = '';
                    rollo4.class = '';
                    rollo4.value = '';
                    rollo4.class = '';
                    ord_peso.value = '';
                }
            }
            function total(pro, peso, lt, ord, std) {

//                doc = document.getElementsByClassName('itm');
//                n = 0;
//                sum1 = 0;
//                sum2 = 0;
//                sum3 = 0;
//                sum4 = 0;
//                suminc1 = 0;
//                suminc2 = 0;
//                suminc3 = 0;
//                suminc4 = 0;
//               alert(doc.length);
//                while (n <= doc.length) {
//                    n++;
//                    if ($('#valor1_' + n).html().length == 0) {
//                        can1 = 0;
//                    } else {
//                        can1 = $('#valor1_' + n).html()
//                    }
//                    sum1 = sum1 + parseFloat(can1);
//                    $('#total1').html(sum1.toFixed(2));
//                    if (parseFloat(ord_kgtotal1.value) < parseFloat($('#total1').html())) {
//                        alert('Peso del Producto1 sobrepasa al requerido');
//                    }
//                    if ($('#valor2_' + n).html().length == 0) {
//                        can2 = 0;
//                    } else {
//                        can2 = $('#valor2_' + n).html()
//                    }
//                    sum2 = sum2 + parseFloat(can2);
//                    $('#total2').html(sum2.toFixed(2));
//                    if (parseFloat(ord_kgtotal2.value) < parseFloat($('#total2').html())) {
//                        alert('Peso del Producto2 sobrepasa al requerido');
//                    }
//                    if ($('#valor3_' + n).html().length == 0) {
//                        can3 = 0;
//                    } else {
//                        can3 = $('#valor3_' + n).html()
//                    }
//                    sum3 = sum3 + parseFloat(can3);
//                    $('#total3').html(sum3.toFixed(2));
//                    if (parseFloat(ord_kgtotal3.value) < parseFloat($('#total3').html())) {
//                        alert('Peso del Producto2 sobrepasa al requerido');
//                    }
//                    if ($('#valor4_' + n).html().length == 0) {
//                        can4 = 0;
//                    } else {
//                        can4 = $('#valor4_' + n).html();
//                    }
//                    sum4 = sum4 + parseFloat(can4);
//                    $('#total4').html(sum4.toFixed(2));
//                    if (parseFloat(ord_kgtotal4.value) < parseFloat($('#total4').html())) {
//                        alert('Peso del Producto2 sobrepasa al requerido');
//                    }
//                     
//                    ///inconformes
//                    if ($('#valorinc1_' + n).html().length == 0) {
//                        caninc1 = 0;
//                    } else {
//                        caninc1 = $('#valorinc1_' + n).html();
//                    }
//                    
//                    suminc1 = suminc1 + parseFloat(caninc1);
//                    $('#totalinc1').html(suminc1.toFixed(2));
//                    if ($('#valorinc2_' + n).html().length == 0) {
//                        caninc2 = 0;
//                    } else {
//                        caninc2 = $('#valorinc2_' + n).html();
//                    }
//                    suminc2 = suminc2 + parseFloat(caninc2);
//                    $('#totalinc2').html(suminc2.toFixed(2));
//
//                    if ($('#valorinc3_' + n).html().length == 0) {
//                        caninc3 = 0;
//                    } else {
//                        caninc3 = $('#valorinc3_' + n).html();
//                    }
//                    suminc3 = suminc3 + parseFloat(caninc3);
//                    $('#totalinc3').html(suminc3.toFixed(2));
//
//                    if ($('#valorinc4_' + n).html().length == 0) {
//                        caninc4 = 0;
//                    } else {
//                        caninc4 = $('#valorinc4_' + n).html();
//                    }
//                    suminc4 = suminc4 + parseFloat(caninc4);
//                    $('#totalinc4').html(suminc4.toFixed(2));
//                }
//                auxWindow(0, pro, peso, lt, ord, std);
            }
            var boxH = $(window).height() * 0.50;
            var boxW = $(window).width() * 0.50;
            var boxHF = (boxH - 25);

            function auxWindow(a, datos) {
                switch (a) {
                    case 0:
                        wnd = "<iframe id='frmmodal' width='" + boxW + "' height='" + boxHF + "' src='../Reports/pdf_etiqueta_dinamica.php?datos=" + datos + "' frameborder='0' />";
                        break;
                    case 1:
                        wnd = "<iframe id='frmmodal' width='" + boxW + "' height='" + boxHF + "' src='../Reports/pdf_etiqueta_pallets.php?datos=" + datos + "' frameborder='0' />";
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
            function traer_peso() {
                $.post("actions_reg_ecocambrella.php", {op: 4},
                function (dt) {
                    var specialChars = "!@#$^&%*()+=-[]\/{}|:<>?G";
                    for (var i = 0; i < specialChars.length; i++) {
                        dt = dt.replace(new RegExp("\\" + specialChars[i], 'gi'), '');
                    }
                    $('#ord_peso').val(dt.trim());
                    peso();
                })
            }

            function validacion(obj) {
                n = 0;
                if (ord_por_tornillo1.value != '') {
                    tornillo1 = ord_por_tornillo1.value * 1;
                } else {
                    tornillo1 = 0;
                }
                if (ord_por_tornillo2.value != '') {
                    tornillo2 = ord_por_tornillo2.value * 1;
                } else {
                    tornillo2 = 0;
                }
                if (ord_por_tornillo3.value != '') {
                    tornillo3 = ord_por_tornillo3.value * 1;
                } else {
                    tornillo3 = 0;
                }
                suma_por_tornillo = tornillo1 + tornillo2 + tornillo3;
                if (suma_por_tornillo > 100) {
                    alert('La suma de porcentajes de las Extrusoras es mayor al 100%');
                    $(obj).val('0');
                    $(obj).focus();
                }
                kgtotal = $('#sumakg').val();
                kgtotal1 = ((kgtotal * tornillo1) / 100).toFixed(2);
                kgtotal2 = ((kgtotal * tornillo2) / 100).toFixed(2);
                kgtotal3 = ((kgtotal * tornillo3) / 100).toFixed(2);
                var tkgt1 = 0;
                var tkgt2 = 0;
                var tkgt3 = 0;
                while (n < 6) {
                    n++;
                    ord = $('#ord_mf' + n).val();
                    if (ord != "" && ord != 0) {
                        kg = ((kgtotal1 * ord) / 100);
                        $('#ord_kg' + n).val(kg.toFixed(2));
                    } else {
                        $('#ord_kg' + n).val('0.00');
                    }
                    tkgt1 = tkgt1 + ($('#ord_kg' + n).val() * 1);
                }
                while (n < 12) {
                    n++;
                    ord = $('#ord_mf' + n).val();
                    if (ord != "" && ord != 0) {
                        kg = ((kgtotal2 * ord) / 100);
                        $('#ord_kg' + n).val(kg.toFixed(2));
                    } else {
                        $('#ord_kg' + n).val('0.00');
                    }
                    tkgt2 = tkgt2 + ($('#ord_kg' + n).val() * 1);
                }
                while (n < 18) {
                    n++;
                    ord = $('#ord_mf' + n).val();
                    if (ord != "" && ord != 0) {
                        kg = ((kgtotal3 * ord) / 100);
                        $('#ord_kg' + n).val(kg.toFixed(2));
                    } else {
                        $('#ord_kg' + n).val('0.00');
                    }
                    tkgt3 = tkgt3 + ($('#ord_kg' + n).val() * 1);
                }
                $('#tot_kg_tornillo1').val(tkgt1.toFixed(2));
                $('#tot_kg_tornillo2').val(tkgt2.toFixed(2));
                $('#tot_kg_tornillo3').val(tkgt3.toFixed(2));
                ptch1 = $('#ord_patch').val();
                ptch2 = $('#ord_patch2').val();
                ptch3 = $('#ord_patch3').val();
                patch1 = (ptch1 * 1).toFixed(2);
                patch2 = (ptch2 * 1).toFixed(2);
                patch3 = (ptch3 * 1).toFixed(2);
                var tpch1 = 0;
                var tpch2 = 0;
                var tpch3 = 0;
                n = 0;
                while (n < 6) {
                    n++;
                    ord = $('#ord_mf' + n).val();
                    if (ord != "" && ord != 0) {
                        kg = ((patch1 * ord) / 100);
                        $('#cnt_patch' + n).val(kg.toFixed(2));
                    } else {
                        $('#cnt_patch' + n).val('0.00');
                    }
                    tpch1 = tpch1 + ($('#cnt_patch' + n).val() * 1);
                }
                while (n < 12) {
                    n++;
                    ord = $('#ord_mf' + n).val();
                    if (ord != "" && ord != 0) {
                        kg = ((patch2 * ord) / 100);
                        $('#cnt_patch' + n).val(kg.toFixed(2));
                    } else {
                        $('#cnt_patch' + n).val('0.00');
                    }
                    tpch2 = tpch2 + ($('#cnt_patch' + n).val() * 1);
                }
                while (n < 18) {
                    n++;
                    ord = $('#ord_mf' + n).val();
                    if (ord != "" && ord != 0) {
                        kg = ((patch3 * ord) / 100);
                        $('#cnt_patch' + n).val(kg.toFixed(2));
                    } else {
                        $('#cnt_patch' + n).val('0.00');
                    }
                    tpch3 = tpch3 + ($('#cnt_patch' + n).val() * 1);
                }
                $('#tot_pch_tornillo1').val(tpch1.toFixed(2));
                $('#tot_pch_tornillo2').val(tpch2.toFixed(2));
                $('#tot_pch_tornillo3').val(tpch3.toFixed(2));
                calculo_porcentage(obj);
            }

            function calculo_porcentage(ob) {
                tpt1 = (ord_mf1.value * 1 + ord_mf2.value * 1 + ord_mf3.value * 1 + ord_mf4.value * 1 + ord_mf5.value * 1 + ord_mf6.value * 1).toFixed(2);
                tpt2 = (ord_mf7.value * 1 + ord_mf8.value * 1 + ord_mf9.value * 1 + ord_mf10.value * 1 + ord_mf11.value * 1 + ord_mf12.value * 1).toFixed(2);
                tpt3 = (ord_mf13.value * 1 + ord_mf14.value * 1 + ord_mf15.value * 1 + ord_mf16.value * 1 + ord_mf17.value * 1 + ord_mf18.value * 1).toFixed(2);
                n = 0;
                if (tpt1 > 100) {
                    alert('La suma de porcentajes del Extrusora B no debe pasar de 100%')
                    $(ob).val('0.00');
                    calculo_porcentage(ob);
                }
                if (tpt2 > 100) {
                    alert('La suma de porcentajes del Extrusora A no debe pasar de 100%')
                    $(ob).val('0.00');
                    calculo_porcentage(ob);
                }
                if (tpt3 > 100) {
                    alert('La suma de porcentajes del Extrusora C no debe pasar de 100%')
                    $(ob).val('0.00');
                    calculo_porcentage(ob);
                }
                tot_por_tornillo1.value = tpt1;
                tot_por_tornillo2.value = tpt2;
                tot_por_tornillo3.value = tpt3;

            }

            function quitar(op) {
                if (op == 0) {
                    $('#formula').attr('hidden', true);
                    $('#mostrar').attr('hidden', true);
                    $('#ocultar').attr('hidden', false);
                } else {
                    $('#formula').attr('hidden', false);
                    $('#mostrar').attr('hidden', false);
                    $('#ocultar').attr('hidden', true);
                }
            }
            function cambio_producto(op) {
                switch (op) {
                    case 1:
                        nom = $('#producto1').val();
                        id_pro = $('#pro_pallet1').val();
                        break;
                    case 2:
                        nom = $('#producto2').val();
                        id_pro = $('#pro_pallet2').val();
                        break;
                    case 3:
                        nom = $('#producto3').val();
                        id_pro = $('#pro_pallet3').val();
                        break;
                    case 4:
                        nom = $('#producto4').val();
                        id_pro = $('#pro_pallet4').val();
                        break;
                }
                $('#pal_producto').html(nom);

                $.post("actions_reg_ecocambrella.php", {op: 6, id: id_pro, data: ord_id.value},
                function (dt) {
                    dat = dt.split('&');
                    $('#pal_numero').html(dat[0]);
                    $('#pal_rollos').html(dat[1]);
                    $('#pal_peso').html(dat[2]);
                    $('#pal_estado').html(dat[3]);
                    $('#pal_id').html(dat[4]);
                })
            }

            function save_pallet(op) {
                if (op == 1 && parseFloat($('#pal_rollos').html()) == 0) {
                    cancelar();
                }
                pal = $('#pal_id').html();
                if ($('#pro_pallet1').attr('checked') == false && $('#pro_pallet2').attr('checked') == false && $('#pro_pallet3').attr('checked') == false && $('#pro_pallet4').attr('checked') == false) {
                    if (op == 0) {
                        alert('Seleccione un producto');
                    }
                    return false;
                }
                if ($('#pro_pallet1').attr('checked') == true) {
                    producto = $('#pro_pallet1').val();
                } else if ($('#pro_pallet2').attr('checked') == true) {
                    producto = $('#pro_pallet2').val();
                } else if ($('#pro_pallet3').attr('checked') == true) {
                    producto = $('#pro_pallet3').val();
                } else if ($('#pro_pallet4').attr('checked') == true) {
                    producto = $('#pro_pallet4').val();
                }
                var data = Array(
                        $('#ord_id').val(),
                        producto,
                        $('#pal_numero').html(),
                        $('#pal_rollos').html(),
                        $('#pal_peso').html(),
                        op
                        );
                var etiqueta = Array(
                        $('#ord_numero').val(),
                        $('#pal_producto').html(),
                        $('#pal_numero').html(),
                        $('#pal_rollos').html(),
                        $('#pal_peso').html()
                        );
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if ($('#pal_numero').html() == 0) {
                            if (op == 0) {
                                alert('No existen pallets');
                            }
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_reg_ecocambrella.php',
                    data: {op: 5, 'data[]': data, id: pal}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            if (op == 0) {
                                $('#pro_pallet1').attr('checked', false);
                                $('#pro_pallet2').attr('checked', false);
                                $('#pro_pallet3').attr('checked', false);
                                $('#pro_pallet4').attr('checked', false);
                                $('#pal_producto').html('');
                                $('#pal_numero').html(0);
                                $('#pal_rollos').html(0);
                                $('#pal_peso').html(0);
                                $('#pal_estado').html(0);
                                $('#pal_id').html(0);
                                auxWindow(1, etiqueta);
                            } else {
                                cancelar();
                            }
                        } else {
                            alert(dt);
                        }
                    }
                })
            }

            function valFecha()
            {

                var val = $('#rec_fecha').val();
                v = val.split('-');
                if (val.length != 10 || v[0].length != 4 || v[1].length != 2 || v[2].length != 2)
                {
                    $('#rec_fecha').val('');
                    $('#rec_fecha').focus();
                    alert('Formato de fecha debe ser (yyyy-mm-dd)');
                    return false;
                }
            }
        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
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
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >REGISTRO DE PRODUCCION CAST  <font class="cerrar"  onclick="save_pallet(1)" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>
                        <table>
                            <button hidden id="ocultar" style="float:right" onclick="quitar(1)">>></button>
                            <button id="mostrar" style="float:right" onclick="quitar(0)"><<</button>
                            <tr>
                                <td>REGISTRO #:</td>
                                <td>
                                    <input type="text" size="12" id="rec_numero" readonly value="<?php echo $rst[rec_numero] ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>ORDEN#:</td>
                                <td>
                                    <input type="text" size="50" id="ord_numero" value="<?php echo $rst[ord_num_orden] ?>" onchange="load_orden(this)" list="ordenes"/>
                                    <input type="hidden" size="10" id="ord_id" readonly value="<?php echo $rst[ord_id] ?>" />
                                </td>
                                <td>FECHA:</td>
                                <td>
                                    <input type="text" size="15" id="rec_fecha"  value="<?php echo $rst[rec_fecha] ?>" onchange="valFecha(this)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '');" maxlength="10"/>
                                    <img src="../img/calendar.png" id="im-rec_fecha" />
                                </td>
                            </tr>
                            <tr>
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
                                <td>PESO:</td>
                                <td>
                                    <input type="text" size="15" id="ord_peso" value="<?php echo $rst[ord_num_orden] ?>" onchange="peso()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />
                                    <button id="pesar" onclick="traer_peso()">Pesar</button>    </td>

                            </tr>
                            <tr>
                                <td colspan="6" class="sbtitle" >DATOS GENERALES</td>
                            </tr>

                            <!------------------------------------------------------------------------- DATOS GENERALES ----------------------------------------------------------------------------------->

                            <tr>
                                <td>Fecha Pedido:</td>
                                <td><input readonly type="text" name="ord_fec_pedido" id="ord_fec_pedido" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_pedido] ?>" />
                                <td>Bodega:</td>
                                <td>
                                    <input type="hidden" id="tip_bodega" value="<?php echo $tbodega ?>"/>
                                    <input type="text" readonly id="bodega" size="25" value="<?php echo $tbodega ?>"/>
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

                            </tr>
                            <tr>
                                <td>Fecha Entrega:</td>
                                <td><input type="text" readonly name="ord_fec_entrega" id="ord_fec_entrega" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_entrega] ?>"/>
                                <td>Cliente:</td>
                                <td><input type="text" readonly id="nombre" size="25" value="<?php echo $nombre ?>"/></td>
                                <td>#Etiquetas:</td>
                                <td><input type="text" readonly size="7" id="eti_numero" /> </td>
                            </tr>

                            <tr ><td colspan="6" class="sbtitle" >PRODUCTOS</td></tr> 
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
                                                <th>BLK</h>
                                                <th>Peso Bruto(kg)</th>
                                                <th>Metraje(m)</th>
                                                <th>Pallet</th>
                                            </tr>
                                        </thead>
                                        <!------------------------------------------------------------------------- PRimera linea  ----------------------------------------------------------------------------------->                            
                                        <tr>
                                            <td><input readonly type="text" size="3" style="text-align:right" name="item1" id="item1" value="1"></td>
                                            <td><input readonly type="text" size="20" name="producto1" id="producto1" value="<?php echo $rst1[pro_descripcion] ?>"></td>
                                            <td><input readonly type="text" size="6" style="text-align:right" name="ord_num_rollos1" id="ord_num_rollos1" value="<?php echo $rst[ord_num_rollos] ?>" /></td>
                                            <td><input readonly type="text" size="8" style="text-align:right" name="ord_kgtotal1" id="ord_kgtotal1" value="<?php echo $rst[ord_kgtotal] ?>"/></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="ord_ancho1" id="ord_ancho1" value="<?php echo $rst[ord_pri_ancho] ?>"/></td>
                                            <td><input readonly type="text" size="3" style="text-align:right" name="ord_carril1" id="ord_carril1" value="<?php echo $rst[ord_pri_carril] ?>" /></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="pro_peso1" id="pro_peso1" value="<?php echo $rst[pro_peso] ?>" /></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="pro_metraje1" id="pro_metraje1" value="<?php echo $rst[pro_metraje] ?>" /></td>
                                            <td><input disabled type="radio" name="pro_pallet" id="pro_pallet1" onchange="cambio_producto(1)" checked/></td>
                                        </tr>
                                        <!------------------------------------------------------------------------- SECUNDARIO   ----------------------------------------------------------------------------------->                             
                                        <tr>
                                            <td><input readonly type="text" size="3" style="text-align:right" name="item1" id="item1" value="2"></td>
                                            <td><input readonly type="text" size="20" name="producto2" id="producto2" value="<?php echo $rst1[pro_descripcion] ?>"></td>
                                            <td><input readonly type="text" size="6" style="text-align:right" name="ord_num_rollos2" id="ord_num_rollos2" value="<?php echo $rst[ord_num_rollos] ?>" /></td>
                                            <td><input readonly type="text" size="8" style="text-align:right" name="ord_kgtotal2" id="ord_kgtotal2" value="<?php echo $rst[ord_kgtotal] ?>"/></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="ord_ancho2" id="ord_ancho2" value="<?php echo $rst[ord_pri_ancho] ?>"/></td>
                                            <td><input readonly type="text" size="3" style="text-align:right" name="ord_carril2" id="ord_carril2" value="<?php echo $rst[ord_pri_carril] ?>" /></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="pro_peso2" id="pro_peso2" value="<?php echo $rst[pro_peso] ?>" /></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="pro_metraje2" id="pro_metraje2" value="<?php echo $rst[pro_metraje] ?>" /></td>
                                            <td><input disabled type="radio" name="pro_pallet" id="pro_pallet2" onchange="cambio_producto(2)"/></td>
                                        </tr>
                                        <!------------------------------------------------------------------------- PRODUCTO TERCERO   ----------------------------------------------------------------------------------->                             
                                        <tr>
                                            <td><input readonly type="text" size="3" style="text-align:right" name="item1" id="item1" value="3"></td>
                                            <td><input readonly type="text" size="20" name="producto3" id="producto3" value="<?php echo $rst1[pro_descripcion] ?>"></td>
                                            <td><input readonly type="text" size="6" style="text-align:right" name="ord_num_rollos3" id="ord_num_rollos3" value="<?php echo $rst[ord_num_rollos] ?>" /></td>
                                            <td><input readonly type="text" size="8" style="text-align:right" name="ord_kgtotal3" id="ord_kgtotal3" value="<?php echo $rst[ord_kgtotal] ?>"/></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="ord_ancho3" id="ord_ancho3" value="<?php echo $rst[ord_pri_ancho] ?>"/></td>
                                            <td><input readonly type="text" size="3" style="text-align:right" name="ord_carril3" id="ord_carril3" value="<?php echo $rst[ord_pri_carril] ?>" /></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="pro_peso3" id="pro_peso3" value="<?php echo $rst[pro_peso] ?>" /></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="pro_metraje3" id="pro_metraje3" value="<?php echo $rst[pro_metraje] ?>" /></td>
                                            <td><input disabled type="radio" name="pro_pallet" id="pro_pallet3" onchange="cambio_producto(3)"/></td>
                                        </tr>
                                        <!------------------------------------------------------------------------- CUARTO   ----------------------------------------------------------------------------------->                             
                                        <tr>
                                            <td><input readonly type="text" size="3" style="text-align:right" name="item1" id="item1" value="4"></td>
                                            <td><input readonly type="text" size="20" name="producto4" id="producto4" value="<?php echo $rst1[pro_descripcion] ?>"></td>
                                            <td><input readonly type="text" size="6" style="text-align:right" name="ord_num_rollos4" id="ord_num_rollos4" value="<?php echo $rst[ord_num_rollos] ?>" /></td>
                                            <td><input readonly type="text" size="8" style="text-align:right" name="ord_kgtotal4" id="ord_kgtotal4" value="<?php echo $rst[ord_kgtotal] ?>"/></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="ord_ancho4" id="ord_ancho4" value="<?php echo $rst[ord_pri_ancho] ?>"/></td>
                                            <td><input readonly type="text" size="3" style="text-align:right" name="ord_carril4" id="ord_carril4" value="<?php echo $rst[ord_pri_carril] ?>" /></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="pro_peso4" id="pro_peso4" value="<?php echo $rst[pro_peso] ?>" /></td>
                                            <td><input readonly type="text" size="5" style="text-align:right" name="pro_metraje4" id="pro_metraje4" value="<?php echo $rst[pro_metraje] ?>" /></td>
                                            <td><input disabled type="radio" name="pro_pallet" id="pro_pallet4" onchange="cambio_producto(4)"/></td>
                                        </tr>
                                        <!------------------------------------------------------------------------- REPROCESO    ----------------------------------------------------------------------------------->                             
                                        <tr>
                                            <td colspan="3">Reproceso :</td>
                                            <td ><input readonly size="8" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_kgtotal_rep" id="ord_kgtotal_rep" onchange="calculo_peso(2)" value="<?php echo $rst[ord_kgtotal_rep] ?>" style="text-align:right"/></td> 
                                            <td ><input readonly size="5" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_rep_ancho" id="ord_rep_ancho"  value="<?php echo $rst[ord_rep_ancho] ?>" /></td>
                                            <td ><input readonly size="3" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_rep_carril" id="ord_rep_carril" value="<?php echo $rst[ord_rep_carril] ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="right">Total:</td>
                                            <td><input readonly style="text-align:right" size="8" id="sumakg" value="<?php echo $rst[sumakg] ?>"></td>   
                                            <td align="right"><input readonly  size="5" style="text-align:right" id="sumam" value="<?php echo $rst[sumam] ?>"></td>   
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table>    
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td id="formula" rowspan="20">
                        <table>
                            <tr><td colspan="6" class="sbtitle" >FORMULACION</td></tr> 

                            <tr hidden>
                                <td>Batch: </td>
                                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_patch" id="ord_patch" size="7" style="text-align:right"  value="<?php echo $rst[ord_patch1] ?>" readonly/> </td>
                            </tr>


                            <!------------------------------------------------------------------------- TORNILLO 1 ----------------------------------------------------------------------------------->
                            <tr>
                                <td>Extrusora B: </td>
                                <td><input readonly  onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_por_tornillo1" id="ord_por_tornillo1" size="5" style="text-align:right"  value="<?php echo $rst[ord_por_tornillo1] ?>" /> %</td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp1" id="ord_mp1"style="width:300px">
                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly  onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf1" id="ord_mf1" size="7" style="text-align:right" value="<?php echo $rst[ord_mf1] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch1" id="cnt_patch1" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch1 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_kg1" id="ord_kg1" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg1] ?>"  /> kg
                                </td>

                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp2" id="ord_mp2" style="width:300px">
                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly  onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf2" id="ord_mf2" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf2] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch2" id="cnt_patch2" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch2 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg2" id="ord_kg2" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg2] ?>"  /> kg
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp3" id="ord_mp3" style="width:300px">

                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly   onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf3" id="ord_mf3" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf3] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch3" id="cnt_patch3" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch3 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg3" id="ord_kg3" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg3] ?>"  /> kg
                                </td>
                            </tr>
                            <tr>        
                                <td>
                                    <select disabled name="ord_mp4" id="ord_mp4" style="width:300px">
                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf4" id="ord_mf4" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf4] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch4" id="cnt_patch4" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch4 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg4" id="ord_kg4" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg4] ?>"  />kg               
                                </td>
                            </tr>
                            <tr>        
                                <td>
                                    <select disabled name="ord_mp5" id="ord_mp5" style="width:300px">
                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf5" id="ord_mf5" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf5] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch5" id="cnt_patch5" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch5 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg5" id="ord_kg5" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg5] ?>"  />kg               
                                </td>
                            </tr>
                            <tr>        
                                <td>
                                    <select disabled name="ord_mp6" id="ord_mp6" style="width:300px">

                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf6" id="ord_mf6" size="7" style="text-align:right" value="<?php echo $rst[ord_mf6] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch6" id="cnt_patch6" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch6 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg6" id="ord_kg6" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg6] ?>"  />kg
                                </td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td hidden>
                                    <input readonly type="text" name="tot_por_tornillo1" id="tot_por_tornillo1" size="5" style="text-align:right" value="<?php echo $rst[tot_por_tornillo1] ?>" /> %
                                </td>
                                <td >
                                    <input readonly type="text" name="tot_pch_tornillo1" id="tot_pch_tornillo1" size="7" style="text-align:right" value="<?php echo $rst[tot_pch_tornillo1] ?>" />
                                </td>
                                <td hidden>
                                    <input readonly type="text" name="tot_kg_tornillo1" id="tot_kg_tornillo1" size="7" style="text-align:right" value="<?php echo $rst[tot_kg_tornillo1] ?>" />kg
                                </td>
                            </tr>
                            <!------------------------------------------------------------------------- TORNILLO 2 ----------------------------------------------------------------------------------->
                            <tr hidden>
                                <td>Batch: </td>
                                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_patch2" id="ord_patch2" size="7" style="text-align:right"  value="<?php echo $rst[ord_patch2] ?>" readonly/> </td>
                            </tr>
                            <tr>
                                <td>Extrusora A: </td>
                                <td><input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_por_tornillo1" id="ord_por_tornillo2" size="7" style="text-align:right"  value="<?php echo $rst[ord_por_tornillo2] ?>" /> %</td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp7" id="ord_mp7"style="width:300px">
                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf7" id="ord_mf7" size="7" style="text-align:right" value="<?php echo $rst[ord_mf7] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch7" id="cnt_patch7" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch7 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_kg7" id="ord_kg7" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg7] ?>"  /> kg
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp8" id="ord_mp8" style="width:300px">

                                    </select>

                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf8" id="ord_mf8" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf8] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch8" id="cnt_patch8" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch8 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg8" id="ord_kg8" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg8] ?>"  /> kg
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp9" id="ord_mp9" style="width:300px">

                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf9" id="ord_mf9" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf9] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch9" id="cnt_patch9" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch9 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg9" id="ord_kg9" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg9] ?>"  /> kg
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp10" id="ord_mp10" style="width:300px">

                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf10" id="ord_mf10" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf10] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch10" id="cnt_patch10" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch10 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg10" id="ord_kg10" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg10] ?>"  />kg
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp11" id="ord_mp11" style="width:300px">

                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf11" id="ord_mf11" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf11] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch11" id="cnt_patch11" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch11 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg11" id="ord_kg11" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg11] ?>"  />kg               
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp12" id="ord_mp12" style="width:300px">

                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf12" id="ord_mf12" size="7" style="text-align:right" value="<?php echo $rst[ord_mf12] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch12" id="cnt_patch12" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch12 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg12" id="ord_kg12" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg12] ?>"  />kg
                                </td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td hidden>
                                    <input readonly type="text" name="tot_por_tornillo2" id="tot_por_tornillo2" size="5" style="text-align:right" value="<?php echo $rst[tot_por_tornillo2] ?>" /> %
                                </td>
                                <td >
                                    <input readonly type="text" name="tot_pch_tornillo2" id="tot_pch_tornillo2" size="7" style="text-align:right" value="<?php echo $rst[tot_pch_tornillo2] ?>" />
                                </td>
                                <td hidden>
                                    <input readonly type="text" name="tot_kg_tornillo2" id="tot_kg_tornillo2" size="7" style="text-align:right" value="<?php echo $rst[tot_kg_tornillo2] ?>" />kg
                                </td>
                            </tr>
                            <!------------------------------------------------------------------------- TORNILLO 3 ----------------------------------------------------------------------------------->
                            <tr hidden>
                                <td>Batch: </td>
                                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_patch3" id="ord_patch3" size="7" style="text-align:right"  value="<?php echo $rst[ord_patch3] ?>" readonly/> </td>
                            </tr>
                            <tr>
                                <td>Extrusora C: </td>
                                <td><input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_por_tornillo3" id="ord_por_tornillo3" size="7" style="text-align:right"  value="<?php echo $rst[ord_por_tornillo3] ?>" /> %</td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp13" id="ord_mp13"style="width:300px">
                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf13" id="ord_mf13" size="7" style="text-align:right" value="<?php echo $rst[ord_mf13] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch13" id="cnt_patch13" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch13 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_kg13" id="ord_kg13" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg13] ?>"  /> kg
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp14" id="ord_mp14" style="width:300px">

                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf14" id="ord_mf14" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf14] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch14" id="cnt_patch14" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch14 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg14" id="ord_kg14" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg14] ?>"  /> kg
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp15" id="ord_mp15" style="width:300px">

                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf15" id="ord_mf15" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf15] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch15" id="cnt_patch15" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch15 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg15" id="ord_kg15" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg15] ?>"  /> kg
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp16" id="ord_mp16" style="width:300px">

                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf16" id="ord_mf16" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf16] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch16" id="cnt_patch16" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch16 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg16" id="ord_kg16" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg16] ?>"  />kg
                                </td>
                            </tr>
                            <tr>
                                <td>              
                                    <select disabled name="ord_mp17" id="ord_mp17" style="width:300px">

                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf17" id="ord_mf17" size="7" style="text-align:right"  value="<?php echo $rst[ord_mf17] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch17" id="cnt_patch17" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch17 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg17" id="ord_kg17" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg17] ?>"  />kg
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select disabled name="ord_mp18" id="ord_mp18" style="width:300px">

                                    </select>
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf18" id="ord_mf18" size="7" style="text-align:right" value="<?php echo $rst[ord_mf18] ?>" /> %
                                </td>
                                <td >
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch18" id="cnt_patch18" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch18 ?>"  /> 
                                </td>
                                <td hidden>
                                    <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg18" id="ord_kg18" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg18] ?>"  />kg<br />
                                </td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td hidden>
                                    <input readonly type="text" name="tot_por_tornillo3" id="tot_por_tornillo3" size="5" style="text-align:right" value="<?php echo $rst[tot_por_tornillo3] ?>" /> %
                                </td>
                                <td >
                                    <input readonly type="text" name="tot_pch_tornillo3" id="tot_pch_tornillo3" size="7" style="text-align:right" value="<?php echo $rst[tot_pch_tornillo3] ?>" />
                                </td>
                                <td hidden>
                                    <input readonly type="text" name="tot_kg_tornillo3" id="tot_kg_tornillo3" size="7" style="text-align:right" value="<?php echo $rst[tot_kg_tornillo3] ?>" />kg
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">Observaciones:
                                    <textarea name="rec_observaciones"  id="ord_observaciones" style="width:100%;" disabled></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="3">Novedades:
                                    <textarea name="rec_observaciones" id="rec_observaciones" style="width:100%"></textarea></td>
                            </tr>
                            <tr><td colspan="6" class="sbtitle" >PALLETS</td></tr> 
                            <tr> 
                                <td colspan="2">
                                    Producto:&nbsp<label id="pal_producto"></label>&nbsp&nbsp&nbsp
                                    Pallets #: &nbsp<label id="pal_numero">0</label>&nbsp&nbsp&nbsp
                                    Rollos #: &nbsp<label id="pal_rollos">0</label>&nbsp&nbsp&nbsp
                                    <label id="pal_peso" hidden>0</label>&nbsp&nbsp&nbsp
                                    <label id="pal_estado" hidden>0</label>&nbsp&nbsp&nbsp
                                    <label id="pal_id" hidden>0</label>&nbsp&nbsp&nbsp
                                    <button id="pallet" onclick="save_pallet(0)">Nuevo Pallet</button>    
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <table id="tab1" border="1" width="800px">
                                <thead>
                                    <tr>
                                        <td class="sbtitle" style="width: 10px"></td>
                                        <td class="sbtitle" colspan="2">PRODUCTO1</td>
                                        <td class="sbtitle producto2" colspan="2">PRODUCTO2</td>
                                        <td class="sbtitle producto3" colspan="2">PRODUCTO3</td>
                                        <td class="sbtitle producto4" colspan="2">PRODUCTO4</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 10px"></th>
                                        <th id="tle_pro1" colspan="2"></th>
                                        <th class="producto2" id="tle_pro2" colspan="2"></th>
                                        <th class="producto3" id="tle_pro3" colspan="2"></th>
                                        <th class="producto4" id="tle_pro4" colspan="2"></th>

                                    </tr>
                                    <!--                            </thead>-->
                                    <tr hidden>
                                        <td colspan="2" align="center"><input readonly type="text" size="6" style="text-align:right" name="rollo1" id="rollo1"></td>
                                        <td colspan="2" align="center"><input readonly type="text" size="6" style="text-align:right" name="rollo2" id="rollo2"></td>
                                        <td colspan="2" align="center"><input readonly type="text" size="6" style="text-align:right" name="rollo3" id="rollo3"></td>
                                        <td colspan="2" align="center"><input readonly type="text" size="6" style="text-align:right" name="rollo4" id="rollo4"></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 10px">Nº</th>
                                        <th style="width: 80px">Peso Conf.</th>
                                        <th style="width: 80px">Peso Incon.</th>
                                        <th class="producto2" style="width: 80px">Peso Conf.</th>
                                        <th class="producto2" style="width: 80px">Peso Incon.</th>
                                        <th class="producto3" style="width: 80px">Peso Conf.</th>
                                        <th class="producto3" style="width: 80px">Peso Incon.</th>
                                        <th class="producto4" style="width: 80px">Peso Conf.</th>
                                        <th class="producto4" style="width: 80px">Peso Incon.</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="scroll" style="overflow:scroll;height:300px;overflow-x:hidden;">
                            <table id="tab2" border="1" width="800px" >
                                <tbody class="tbl_frm_aux" id="lista" >   
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <table id="tab3" width="800px">
                                <tr class="add" style="height: 20px">
                                    <td style="width: 10px" align="right" style="font-size: 14px">&nbsp;</td>
                                    <td style="width: 80px" align="right" style="font-size: 14px" id="total1">0</td>
                                    <td style="width: 80px" align="right" style="font-size: 14px" id="totalinc1">0</td>
                                    <td class="producto2" style="width: 80px" align="right" style="font-size: 14px" id="total2">0</td>
                                    <td class="producto2" style="width: 80px" align="right" style="font-size: 14px" id="totalinc2">0</td>
                                    <td class="producto3" style="width: 80px" align="right" style="font-size: 14px" id="total3">0</td>
                                    <td class="producto3" style="width: 80px" align="right" style="font-size: 14px" id="totalinc3">0</td>
                                    <td class="producto4" style="width: 80px" align="right" style="font-size: 14px" id="total4">0</td>
                                    <td class="producto4" style="width: 80px" align="right" style="font-size: 14px" id="totalinc4">0</td>
                                </tr>
                                <tr>
                                    <td style="width: 10px"></td>
                                    <td style="width: 80px">
                                        <button disabled id="guardar1" onclick="clonar('1', '0')">Conforme</button>    
                                    </td>
                                    <td style="width: 80px">
                                        <button disabled id="inconforme1" onclick="clonar('1', '3')">Inconforme</button>    
                                    </td>
                                    <td class="producto2" style="width: 80px">
                                        <button disabled id="guardar2" onclick="clonar('2', '0')">Conforme</button>    
                                    </td>
                                    <td class="producto2" style="width: 80px">
                                        <button disabled id="inconforme2" onclick="clonar('2', '3')">Inconforme</button>    
                                    </td>
                                    <td class="producto3" style="width: 80px">
                                        <button disabled id="guardar3" onclick="clonar('3', '0')">Conforme</button>    
                                    </td>
                                    <td class="producto3" style="width: 80px">
                                        <button disabled id="inconforme3" onclick="clonar('3', '3')">Inconforme</button>    
                                    </td>
                                    <td class="producto4" style="width: 80px">
                                        <button disabled id="guardar4" onclick="clonar('4', '0')">Conforme</button>    
                                    </td>
                                    <td class="producto4" style="width: 80px">
                                        <button disabled id="inconforme4" onclick="clonar('4', '3')">Inconforme</button>    
                                    </td>
                                </tr>
                            </table>
                        </div>
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
//        $n++;
        ?>
        <option value="<?php echo $rst_ord[ord_num_orden] ?>"><?php echo $rst_ord[ord_num_orden] . ' ' . substr($rst_ord[pro_descripcion], 0, 40) ?></option>
        <?php
    }
    ?>
</datalist>