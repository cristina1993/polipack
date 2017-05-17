<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$id = $_GET [id];
$x = $_GET[x];
$fec1 = $_GET[desde];
$fec2 = $_GET[hasta];
$txt = $_GET[txt];
if (isset($_GET [id])) {
    $rst = pg_fetch_array($Set->lista_una_orden_produccion($id));
    $rst_cli = pg_fetch_array($Set->lista_clientes_codigo($rst[cli_id]));
    $nombre = $rst_cli[cli_raz_social];
    $cli_id = $rst_cli[cli_id];
    $det = 0;
    $det_id = $rst[det_id];
} else {
    if (isset($_GET[prod])) {
        $rs_pv = pg_fetch_array($Set->lista_un_det_pedido($_GET[prod]));
        $rst[pro_id] = $rs_pv[pro_id];
        $rst[unidad] = $rs_pv[det_unidad];
        $rst[ord_num_rollos] = $rs_pv[det_cantidad];
        $det = 1;
        $rst[ord_fec_pedido] = $rs_pv[ped_femision];
        $det_id = $_GET[prod];
    } else {
        $rst[pro_id] = '';
        $rst[ord_num_rollos] = '';
        $det = 0;
        $rst[ord_fec_pedido] = date("Y-m-d");
        $det_id = 0;
    }
    $cns = $Set->lista_orden_produccion();
    $rst[ord_tornillo] = 1;
    $rst[ord_kg1] = 0;
    $rst[ord_kg2] = 0;
    $rst[ord_kg3] = 0;
    $rst[ord_kg4] = 0;
    $rst[ord_kg5] = 0;
    $rst[ord_kg6] = 0;
    $rst[ord_mf1] = 0;
    $rst[ord_mf2] = 0;
    $rst[ord_mf3] = 0;
    $rst[ord_mf4] = 0;
    $rst[ord_mf5] = 0;
    $rst[ord_mf6] = 0;
    $rst[ord_fec_entrega] = date("Y-m-d");
    $rst[ord_anc_total] = '1.8';
    $rst[ord_pri_ancho] = 0;
    $rst[ord_pri_carril] = 0;
    $rst[ord_pri_faltante] = 0;
    $rst[ord_sec_ancho] = 0;
    $rst[ord_sec_carril] = 0;
    $rst[ord_refilado] = '0.15';
    $rst[ord_rep_ancho] = 0;
    $rst[ord_rep_carril] = 0;
    $rst[ord_largo] = 0;
    $rst[ord_gramaje] = 0;
    $rst[ord_merma] = '0.3';
    $rst[ord_merma_peso] = '0';
    $rst[ord_tot_fin] = '0';
    $rst[ord_tot_fin_peso] = '0';
    $rst[ord_kgtotal] = '0.0';
    $rst[ord_mftotal] = '0.0';
    $rst_sec = pg_fetch_array($Set->lista_secuencial_orden_produccion());
    if (!empty($rst_sec)) {
        $cod = explode('-', $rst_sec[ord_num_orden]);
        $sec = ($cod[1] + 1);
        $cod = $cod[0];
    } else {
        $cod = 'EC';
        $sec = 1;
    }
    if ($sec >= 0 && $sec < 10) {
        $tx_trs = "0000";
    } elseif ($sec >= 10 && $sec < 100) {
        $tx_trs = "000";
    } elseif ($sec >= 100 && $sec < 1000) {
        $tx_trs = "00";
    } elseif ($sec >= 1000 && $sec < 10000) {
        $tx_trs = "0";
    } elseif ($sec >= 10000 && $sec < 100000) {
        $tx_trs = "";
    }
    $rst['ord_num_orden'] = $cod . '-' . $tx_trs . $sec;
    $nombre = 'POLIPACK';
    $cli_id = '1';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            var fec1 = '<?php echo $fec1 ?>';
            var fec2 = '<?php echo $fec2 ?>';
            var txt = '<?php echo $txt ?>';
            var det = '<?php echo $det ?>';
            var det_id = '<?php echo $det_id ?>';
            $(function () {
                Calendar.setup({inputField: "ord_fec_pedido", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "ord_fec_entrega", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                document.getElementById("ord_pro_principal").disabled = true;
                producto(<?php echo $rst[pro_id] ?>);
                posicion_aux_window();

            });
            function limpiar_datos_detalle()
            {
                ord_anc_total.value = "0";
                ord_kg1.value = 0;
                ord_kg2.value = 0;
                ord_kg3.value = 0;
                ord_kg4.value = 0;
                ord_kg5.value = 0;
                ord_kg6.value = 0;
                ord_mp1.value = '0';
                ord_mp2.value = '0';
                ord_mp3.value = '0';
                ord_mp4.value = '0';
                ord_mp5.value = '0';
                ord_mp6.value = '0';
                ord_mf1.value = '0';
                ord_mf2.value = '0';
                ord_mf3.value = '0';
                ord_mf4.value = '0';
                ord_mf5.value = '0';
                ord_mf6.value = '0';
                ord_mftotal.value = 0;
                ord_kgtotal.value = 0;
                ord_pri_ancho.value = 0;
                ord_pri_carril.value = 0;
                ord_pri_faltante.value = 0;
                ord_sec_ancho.value = 0;
                ord_sec_carril.value = 0;
                ord_rep_ancho.value = 0;
                ord_rep_carril.value = 0;
                ord_largo.value = 0;
                ord_gramaje.value = 0;
                ord_refilado.value = 0.15;
                ord_anc_total.value = 1.8;
                ord_pro_principal.value = pro_id.value;
                ord_zo1.value = 0;
                ord_zo2.value = 0;
                ord_zo3.value = 0;
                ord_zo4.value = 0;
                ord_zo5.value = 0;
                ord_zo6.value = 0;
                ord_rol_mil_up_down.value = 0;
                ord_mas_bra_autosetting.value = 0;
                ord_win_tensility.value = 0;
                ord_rol_mill.value = 0;
                ord_man_spe_setting.value = 0;
                ord_lap_speed.value = 0;
                ord_dra_blower.value = 0;
                ord_sid_blower.value = 0;
                ord_spi_blower.value = 0;
                ord_mat_pump.value = 0;
                ord_spi_rol_oil_pump.value = 0;
                ord_spi_rol_heating.value = 0;
                ord_dow_rol_oil_pump.value = 0;
                ord_dow_rol_heating.value = 0;
                ord_upp_rol_oil_pump.value = 0;
                ord_upp_rol_heating.value = 0;
                ord_spi_temp.value = 0;
                ord_upp_rol_tem_controller.value = 0;
                ord_dow_rol_tem_controller.value = 0;
                ord_coo_air_temp.value = 0;
                ord_spi_tem_controller.value = 0;
                ord_gsm_setting.value = 0;
                ord_aut_spe_adjust.value = 0;
                ord_spe_mod_auto.value = 0;
                ord_tornillo.value = 1;
                ord_bodega.value = '';
                ord_peso.value=0;
                ord_gran_tot.value=0;
                ord_kgtotal1.value=0;
                ord_gran_tot_peso.value=0;
                ord_num_rollos.value=0;
                calculo();
            }

            function limpiar()
            {
                pro_id.style.borderColor = "";
                cli_id.style.borderColor = "";
                ord_num_rollos.style.borderColor = "";
                ord_fec_pedido.style.borderColor = "";
                ord_fec_entrega.style.borderColor = "";
                ord_anc_total.style.borderColor = "";
            }
            function abrir() {
                cod = pro_id.value;
                v = 0;
                if (cod != 0 || cod != "") {
                    parent.document.getElementById('bottomFrame').src = '../Scripts/Form_i_productos.php?id=' + cod + '&x=' + v;
                    parent.document.getElementById('contenedor2').rows = "*,80%";
                    look_menu();
                }

            }
            function save(id)
            {

                if (cli_id.value == 0) {
                    alert('El Cliente es un campo obligatorio.');
                    cli_id.focus();
                    limpiar();
                    cli_id.style.borderColor = "red";
                } else if (pro_id.value == 0) {
                    alert('El Producto es un campo obligatorio.');
                    pro_id.focus();
                    limpiar();
                    pro_id.style.borderColor = "red";
                } else if (ord_num_rollos.value.length == 0) {
                    alert('El Número de Rollos es un campo obligatorio.');
                    ord_num_rollos.focus();
                    limpiar();
                    ord_num_rollos.style.borderColor = "red";
                } else if (ord_fec_pedido.value.length == 0) {
                    alert('La Fecha de Pedido es un campo obligatorio.');
                    ord_fec_pedido.focus();
                    limpiar();
                    ord_fec_pedido.style.borderColor = "red";
                } else if (ord_fec_entrega.value.length == 0) {
                    alert('La Fecha de Entrega es un campo obligatorio.');
                    ord_fec_entrega.focus();
                    limpiar();
                    ord_fec_entrega.style.borderColor = "red";
                } else if (ord_anc_total.value.length == 0) {
                    alert('El Ancho Total es un campo obligatorio.');
                    ord_anc_total.focus();
                    limpiar();
                    ord_anc_total.style.borderColor = "red";
                } else if (ord_fec_entrega.value < ord_fec_pedido.value) {
                    alert('El Fecha de Entrega no puede ser antes de la Fecha de Pedido.');
                    ord_fec_entrega.focus();
                    limpiar();
                    ord_fec_entrega.style.borderColor = "red";
                } else if (ord_mftotal.value != 100) {
                    alert('Valor total del porcentaje debe ser 100');
                    ord_mftotal.focus();
                    limpiar();
                    ord_mftotal.style.borderColor = "red";
                } else if (ord_merma.value.length == 0) {
                    alert('El porcentaje de merma es un campo obligatorio.');
                    ord_merma.focus();
                    limpiar();
                    ord_merma.style.borderColor = "red";
                } else {
                    var data = Array(
                            ord_num_orden.value,
                            cli_id.value,
                            pro_id.value,
                            ord_num_rollos.value,
                            ord_mp1.value,
                            ord_mp2.value,
                            ord_mp3.value,
                            ord_mp4.value,
                            ord_mf1.value,
                            ord_mf2.value,
                            ord_mf3.value,
                            ord_mf4.value,
                            ord_mftotal.value,
                            ord_kg1.value,
                            ord_kg2.value,
                            ord_kg3.value,
                            ord_kg4.value,
                            ord_kgtotal.value,
                            ord_fec_pedido.value,
                            ord_fec_entrega.value,
                            ord_anc_total.value,
                            ord_refilado.value,
                            ord_pri_ancho.value,
                            ord_pri_carril.value,
                            ord_pri_faltante.value,
                            ord_pro_secundario.value,
                            ord_sec_ancho.value,
                            ord_sec_carril.value,
                            ord_rep_ancho.value,
                            ord_rep_carril.value,
                            ord_largo.value,
                            ord_gramaje.value,
                            ord_zo1.value.toUpperCase(),
                            ord_zo2.value.toUpperCase(),
                            ord_zo3.value.toUpperCase(),
                            ord_zo4.value.toUpperCase(),
                            ord_zo5.value.toUpperCase(),
                            ord_zo6.value.toUpperCase(),
                            ord_spi_temp.value.toUpperCase(),
                            ord_upp_rol_tem_controller.value.toUpperCase(),
                            ord_dow_rol_tem_controller.value.toUpperCase(),
                            ord_spi_tem_controller.value.toUpperCase(),
                            ord_coo_air_temp.value.toUpperCase(),
                            ord_upp_rol_heating.value.toUpperCase(),
                            ord_upp_rol_oil_pump.value.toUpperCase(),
                            ord_dow_rol_heating.value.toUpperCase(),
                            ord_dow_rol_oil_pump.value.toUpperCase(),
                            ord_spi_rol_heating.value.toUpperCase(),
                            ord_spi_rol_oil_pump.value.toUpperCase(),
                            ord_mat_pump.value.toUpperCase(),
                            ord_spi_blower.value.toUpperCase(),
                            ord_sid_blower.value.toUpperCase(),
                            ord_dra_blower.value.toUpperCase(),
                            ord_gsm_setting.value.toUpperCase(),
                            ord_aut_spe_adjust.value.toUpperCase(),
                            ord_spe_mod_auto.value.toUpperCase(),
                            ord_lap_speed.value.toUpperCase(),
                            ord_man_spe_setting.value.toUpperCase(),
                            ord_rol_mill.value.toUpperCase(),
                            ord_win_tensility.value.toUpperCase(),
                            ord_mas_bra_autosetting.value.toUpperCase(),
                            ord_rol_mil_up_down.value.toUpperCase(),
                            ord_observaciones.value.toUpperCase(),
                            ord_mp5.value,
                            ord_mp6.value,
                            ord_mf5.value,
                            ord_mf6.value,
                            ord_kg5.value,
                            ord_kg6.value,
                            ord_merma.value,
                            ord_merma_peso.value,
                            ord_gran_tot.value,
                            ord_gran_tot_peso.value,
                            det_id,
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            '0',
                            ord_tornillo.value,
                            ord_bodega.value
                            );
                    var fields = Array();
                    $("#tbl_form").find(':input').each(function () {
                        var elemento = this;
                        des = elemento.id + "=" + elemento.value;
                        fields.push(des);
                    });
                    loading('visible');
                    $.post("actions.php", {act: 60, 'data[]': data, id: id, 'fields[]': fields},
                    function (dt) {
                        if (dt == 0) {
//                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_orden_ecocambrella.php';
                            loading('hidden');
                            cancelar();
                        } else {
                            alert(dt);
                            loading('hidden');
                        }
                    }
                    );
                }
            }
            function cancelar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                if (det == 0) {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_orden_ecocambrella.php?desde=' + fec1 + '&hasta=' + fec2 + '&txt=' + txt;
                } else {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_prod_pedido_venta.php';
                }
            }
            function calculo_porcentage() {
                ord_mftotal.value = (ord_mf1.value * 1 + ord_mf2.value * 1 + ord_mf3.value * 1 + ord_mf4.value * 1 + ord_mf5.value * 1 + ord_mf6.value * 1).toFixed(2);
                ord_gran_tot.value = (ord_mftotal.value * 1 + ord_merma.value * 1).toFixed(2);
                suma_kg();
            }
            function calculo_kg() {
                ord_kgtotal1.value = (ord_kg1.value * 1 + ord_kg2.value * 1 + ord_kg3.value * 1 + ord_kg4.value * 1 + ord_kg5.value * 1 + ord_kg6.value * 1).toFixed(2);
                if (ord_num_rollos.value == "") {
                    ord_kgtotal.value = (ord_kg1.value * 1 + ord_kg2.value * 1 + ord_kg3.value * 1 + ord_kg4.value * 1 + ord_kg5.value * 1 + ord_kg6.value * 1).toFixed(2);
                } else {
                    calculo_peso();
                }
            }
            function calculo_peso(a) {
                peso = $("#ord_peso").val();
                pro = $("#pro_id").val();

                switch (a)
                {
                    case 0:
                        if ($("#pro_id").val() == "" || ($("#pro_id").val() == 0)) {
                            ord_kgtotal.value = 0;
                            ord_num_rollos.value = 0;
                        }
                        break;
                    case 1:

                        if ($("#ord_peso").val() == "" || ($("#ord_peso").val() == 0)) {
                            ord_kgtotal.value = 0;
                        } else {
                            uni = $('#unidad').val();
                            if (uni == 1) {
                                num_rollos = ord_num_rollos.value;
                            } else if (uni == 2) {
                                gran_peso_kg_total = ord_num_rollos.value * 1 + ord_merma_peso.value * 1;
                                n_rollos = gran_peso_kg_total / peso;
                                num_rollos = Math.ceil(n_rollos.toFixed(2));
                                $('#ord_num_rollos').val(num_rollos);
                            } else if (uni == 3) {
                                totpeso_rollo = ((ord_anc_total.value * ord_num_rollos.value * ord_gramaje.value) / 1000).toFixed(2);
                                totpeso2 = ((ord_anc_total.value * ord_largo.value * ord_gramaje.value) / 1000).toFixed(2);
                                n_rollos1 = totpeso_rollo / totpeso2;
                                num_rollos = Math.ceil(n_rollos1.toFixed(2));
                                $('#ord_num_rollos').val(num_rollos);
                            }
                            num_rollos = ord_num_rollos.value;
                            totpeso = ((ord_anc_total.value * ord_largo.value * ord_gramaje.value) / 1000).toFixed(2);
                            rollos_madre_prod = (num_rollos / ord_pri_carril.value);
                            ord_kgtotal.value = (totpeso * rollos_madre_prod).toFixed(2);
                            ord_gran_tot.value = ((ord_mftotal.value * 1) + (ord_merma.value * 1)).toFixed(2);

                            validacion();
                        }
                        break;
                    case 2:
                        if ($("#ord_kgtotal").val() != "" && peso != "") {
                            ord_num_rollos.value = (ord_kgtotal.value / peso).toFixed(2);
                            validacion();
                        }
                        break;
                }
            }

            function validacion() {
                n = 0;
                totpeso = ((ord_pri_ancho.value * ord_pri_carril.value * ord_largo.value * ord_gramaje.value) / 1000).toFixed(2);
                rollos_madre_prod = (ord_num_rollos.value / ord_pri_carril.value);
                kgtotal = (totpeso * rollos_madre_prod).toFixed(2);
                while (n < 6) {
                    n++;
                    ord = $('#ord_mf' + n).val();
                    if (ord != "" && ord != 0) {
                        kg = ((kgtotal * ord) / 100);
                        $('#ord_kg' + n).val(kg.toFixed(2));
                    } else {
                        $('#ord_kg' + n).val('0.00');
                    }

                }
                calculo_porcentage();
            }
            function calculo(c) {
                if ((ord_anc_total.value * 1) < (ord_pri_ancho.value * 1)) {
                    alert(' - Tome en cuenta que no puede ingresar un ANCHO menor al ANCHO del producto principal');
                    ord_anc_total.value = "0";
                    ord_anc_total.focus();
                    ord_anc_total.style.borderColor = "red";
                    ord_pri_carril.value = 0;
                    ord_pri_faltante.value = 0;
                    ord_pro_secundario.value = 0;
                    ord_sec_ancho.value = 0;
                    ord_sec_carril.value = 0;
                    ord_rep_ancho.value = 0;
                    ord_rep_carril.value = 0;
                    document.getElementById("ord_pro_secundario").disabled = false;
                } else {
                    ord_anc_total.style.borderColor = "";
                    if (c == null) {
                        ord_pri_carril.value = (((ord_anc_total.value - (2 * ord_refilado.value)) / ord_pri_ancho.value) - (((ord_anc_total.value - (2 * ord_refilado.value)) / ord_pri_ancho.value) - parseInt((ord_anc_total.value - (2 * ord_refilado.value)) / ord_pri_ancho.value))).toFixed(2);
                    }
                    faltante = (ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_refilado.value) * 2).toFixed(2);

                    if (faltante == 'NaN') {
                        faltante = '0';
                    }
                    if (ord_pri_carril.value == 'NaN') {
                        ord_pri_carril.value = '0';
                    }
                    if (faltante < 0) {
                        alert('Sobrepasa el ancho total');
                        calculo();
                    } else {
                        ord_pri_faltante.value = faltante;
                        $.post("actions.php", {act: 52, faltante: ord_pri_faltante.value, gramaje: ord_gramaje.value},
                        function (dt) {
                            $('#ord_pro_secundario').html(dt);
                            document.getElementById("ord_pro_secundario").disabled = false;
                            z = $('#ord_pro_secundario').val();
                            if (z > 0) {
                                despliegue_ancho_producto_secundario(z);
                            } else if (ord_pri_faltante.value == 0.0 || ord_pri_faltante.value == 0) {
                                ord_sec_ancho.value = 0;
                                ord_sec_carril.value = 0;
                                ord_rep_ancho.value = 0;
                                ord_rep_carril.value = 0;
                            } else {
                                ord_sec_ancho.value = 0;
                                ord_sec_carril.value = 0;
                                ord_rep_ancho.value = ord_pri_faltante.value;
                                ord_rep_carril.value = 1;
                            }
                        });
                    }
                }
            }
            function despliegue_ancho_producto_secundario(id)
            {
                $.post("actions.php", {act: 53, id: id},
                function (dt) {
                    ord_sec_ancho.value = dt;
                    ord_sec_carril.value = ((ord_pri_faltante.value / ord_sec_ancho.value) - (ord_pri_faltante.value / ord_sec_ancho.value - parseInt(ord_pri_faltante.value / ord_sec_ancho.value))).toFixed(2);
                    if (ord_sec_carril.value == 'NaN') {
                        alert('El Producto Secundario \n Cuenta con un valor de Ancho 0.00 \n El cual no es permitido para el calculo');
                        $('#save').hide();
                    } else if (ord_sec_carril.value != 'NaN') {
                        $('#save').show();
                    }
                    if (ord_pro_secundario.value == 0) {
                        var numero = 0;
                        ord_sec_ancho.value = numero.toFixed(2);
                        ord_sec_carril.value = 0;
                        if (ord_pri_faltante.value > 0) {

                            document.getElementById("ord_pro_secundario").disabled = true;
                            ord_rep_ancho.value = ord_pri_faltante.value;
                            ord_rep_carril.value = 1;
                        } else {

                            if (ord_pri_faltante.value == 0 && ord_pro_secundario.value == 0) {

                                ord_sec_ancho.value = 0;
                                ord_sec_carril.value = 0;
                                ord_rep_ancho.value = 0;
                                ord_rep_carril.value = 0;
                                document.getElementById("ord_pro_secundario").disabled = false; //ojo
                            }
                        }
                    } else {

                        ord_rep_ancho.value = ((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_refilado.value * 2) - (ord_sec_ancho.value * ord_sec_carril.value))).toFixed(2);

                        if (ord_rep_ancho.value > 0) {
//                            ord_rep_carril.value = ((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value) - (((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value) - parseInt((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value));
                            ord_rep_carril.value = ((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_sec_ancho.value * ord_sec_carril.value) - (ord_refilado.value * 2)).toFixed(2) / ord_rep_ancho.value) - (((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_sec_ancho.value * ord_sec_carril.value) - (ord_refilado.value * 2)).toFixed(2) / ord_rep_ancho.value) - parseInt(((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_sec_ancho.value * ord_sec_carril.value) - (ord_refilado.value * 2)).toFixed(2) / ord_rep_ancho.value))).toFixed(2);
                            if (ord_rep_carril.value == 'NaN') {
                                ord_rep_carril.value = 0;
                            }

                        } else {
                            ord_rep_carril.value = 0;
                        }
                    }
                });
            }
            function producto(id) {
                if (pro_id.value == 0) {
                    limpiar_datos_detalle();
                } else {
                    pro_id.style.borderColor = "";
                    ord_pro_principal.value = pro_id.value;
                    $.post("actions.php", {act: 51, id: id},
                    function (dt) {
                        dat = dt.split('&');
                        var a = '<?php echo $id ?>';
                        if (a.length == 0) {
                            $('#ord_mp1,#ord_mp2,#ord_mp3,#ord_mp4,#ord_mp5,#ord_mp6').html(dat[48]);/// combos de materias primas formulacion
                            ord_pri_ancho.value = dat[0];/// ancho prod principal
                            ord_mp1.value = dat[1];// materia prima 1
                            ord_mp2.value = dat[2];// materia prima 2
                            ord_mp3.value = dat[3];// materia prima 3
                            ord_mp4.value = dat[4];// materia prima 4
                            ord_mp5.value = dat[5];// materia prima 5
                            ord_mp6.value = dat[6];// materia prima 6
                            ord_mf1.value = dat[7];// porcentaje mp 1
                            ord_mf2.value = dat[8];// porcentaje mp 2
                            ord_mf3.value = dat[9];// porcentaje mp 3
                            ord_mf4.value = dat[10];// porcentaje mp 4
                            ord_mf5.value = dat[11];// porcentaje mp 5
                            ord_mf6.value = dat[12];// porcentaje mp 6
                            ord_mftotal.value = dat[13];// porcentaje mp total
                            ord_largo.value = dat[14];// largo
                            ord_gramaje.value = dat[15];//gramaje
                            ord_peso.value = dat[16];// peso
                            ord_zo1.value = (dat[18]);
                            if (dat[18] == "") {///Roll Speed set
                                $('#ord_zo1').val(0);
                            } else {
                                $('#ord_zo1').val(dat[18]);
                            }
                            if (dat[19] == "") {///rolls speed act1.
                                $('#ord_zo2').val(0);
                            } else {
                                $('#ord_zo2').val(dat[19]);
                            }
                            if (dat[20] == "") {///rolls speed act2.
                                $('#ord_zo3').val(0);
                            } else {
                                $('#ord_zo3').val(dat[20]);
                            }
                            if (dat[21] == "") {///rolls speed act3.
                                $('#ord_zo4').val(0);
                            } else {
                                $('#ord_zo4').val(dat[21]);
                            }
                            if (dat[22] == "") {///rolls speed act4.
                                $('#ord_zo5').val(0);
                            } else {
                                $('#ord_zo5').val(dat[22]);
                            }
                            if (dat[23] == "") {///Temperature set1
                                $('#ord_zo6').val(0);
                            } else {
                                $('#ord_zo6').val(dat[23]);
                            }
                            //////////////////////////////
                            if (dat[24] == "") {  ///Screw speed act.Extr A.
                                $('#ord_spi_temp').val(0);
                            } else {
                                $('#ord_spi_temp').val(dat[24]);
                            }
                            if (dat[25] == "") {  /// Thoughput Extr A.
                                $('#ord_upp_rol_tem_controller').val(0);
                            } else {
                                $('#ord_upp_rol_tem_controller').val(dat[25]);
                            }
                            if (dat[26] == "") {/// meltpump Revolutions
                                $('#ord_dow_rol_tem_controller').val(0);
                            } else {
                                $('#ord_dow_rol_tem_controller').val(dat[26]);
                            }
                            if (dat[27] == "") {/// Screw speed act Extr. B 
                                $('#ord_spi_tem_controller').val(0);
                            } else {
                                $('#ord_spi_tem_controller').val(dat[27]);
                            }
                            if (dat[28] == "") {/// Total throughputh Extr. B 
                                $('#ord_coo_air_temp').val(0);
                            } else {
                                $('#ord_coo_air_temp').val(dat[28]);
                            }
                            if (dat[35] == "") {///
                                $('#ord_upp_rol_heating').val(0);
                            } else {
                                $('#ord_upp_rol_heating').val(dat[35]);
                            }
                            if (dat[36] == "") {///
                                $('#ord_upp_rol_oil_pump').val(0);
                            } else {
                                $('#ord_upp_rol_oil_pump').val(dat[36]);
                            }
                            if (dat[31] == "") {///
                                $('#ord_dow_rol_heating').val(0);
                            } else {
                                $('#ord_dow_rol_heating').val(dat[30]);
                            }
                            if (dat[32] == "") {///Temperature set2
                                $('#ord_dow_rol_oil_pump').val(0);
                            } else {
                                $('#ord_dow_rol_oil_pump').val(dat[32]);
                            }
                            if (dat[33] == "") {///Temperature set3
                                $('#ord_spi_rol_heating').val(0);
                            } else {
                                $('#ord_spi_rol_heating').val(dat[33]);
                            }
                            if (dat[34] == "") {///Terperature Act1.
                                $('#ord_spi_rol_oil_pump').val(0);
                            } else {
                                $('#ord_spi_rol_oil_pump').val(dat[34]);
                            }
                            if (dat[35] == "") {///Screw speed act Extr. C
                                $('#ord_mat_pump').val(0);
                            } else {
                                $('#ord_mat_pump').val(dat[35]);
                            }
                            if (dat[36] == "") {///Total throughputh Extr. C 
                                $('#ord_spi_blower').val(0);
                            } else {
                                $('#ord_spi_blower').val(dat[36]);
                            }
                            if (dat[37] == "") {///Total throughputh
                                $('#ord_sid_blower').val(0);
                            } else {
                                $('#ord_sid_blower').val(dat[37]);
                            }
                            if (dat[38] == "") {///Softbox 
                                $('#ord_dra_blower').val(0);
                            } else {
                                $('#ord_dra_blower').val(dat[38]);
                            }
                            if (dat[39] == "") {///Terperature Act2.
                                $('#ord_gsm_setting').val(0);
                            } else {
                                $('#ord_gsm_setting').val(dat[39]);
                            }
                            if (dat[40] == "") {///Terperature Act3.
                                $('#ord_aut_spe_adjust').val(0);
                            } else {
                                $('#ord_aut_spe_adjust').val(dat[40]);
                            }
                            if (dat[41] == "") {///
                                $('#ord_spe_mod_auto').val(0);
                            } else {
                                $('#ord_spe_mod_auto').val(dat[41]);
                            }
                            if (dat[42] == "") {///Vacuumbox 
                                $('#ord_lap_speed').val(0);
                            } else {
                                $('#ord_lap_speed').val(dat[42]);
                            }
                            if (dat[43] == "") {///Taper
                                $('#ord_man_spe_setting').val(0);
                            } else {
                                $('#ord_man_spe_setting').val(dat[43]);
                            }
                            if (dat[44] == "") {///Inline tension 
                                $('#ord_rol_mill').val(0);
                            } else {
                                $('#ord_rol_mill').val(dat[44]);
                            }
                            if (dat[45] == "") {///Taper Curve tension 
                                $('#ord_win_tensility').val(0);
                            } else {
                                $('#ord_win_tensility').val(dat[45]);
                            }
                            if (dat[46] == "") {///Start Tension
                                $('#ord_mas_bra_autosetting').val(0);
                            } else {
                                $('#ord_mas_bra_autosetting').val(dat[46]);
                            }
                            if (dat[47] == "") {///ACT tension 
                                $('#ord_rol_mil_up_down').val(0);
                            } else {
                                $('#ord_rol_mil_up_down').val(dat[47]);
                            }
                            calculo();
                            if (det != '') {
                                calculo_peso(1);
                            }
                        } else {
                            ord_pri_ancho.value = dat[0];
                            ord_peso.value = dat[16];
                            $('#ord_mp1,#ord_mp2,#ord_mp3,#ord_mp4,#ord_mp5,#ord_mp6').html(dat[48]);
                            $('#ord_mp1').val(<?php echo $rst[ord_mp1] ?>);
                            $('#ord_mp2').val(<?php echo $rst[ord_mp2] ?>);
                            $('#ord_mp3').val(<?php echo $rst[ord_mp3] ?>);
                            $('#ord_mp4').val(<?php echo $rst[ord_mp4] ?>);
                            $('#ord_mp5').val(<?php echo $rst[ord_mp5] ?>);
                            $('#ord_mp6').val(<?php echo $rst[ord_mp6] ?>);
                        }
                    }
                    );
                }
            }



            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
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

            function suma_kg() {
                ord_kgtotal1.value = (ord_kg1.value * 1 + ord_kg2.value * 1 + ord_kg3.value * 1 + ord_kg4.value * 1 + ord_kg5.value * 1 + ord_kg6.value * 1).toFixed(2);
                ord_merma_peso.value = (((ord_kgtotal1.value * 1) * (ord_merma.value * 1)) / 100).toFixed(2);
                ord_gran_tot_peso.value = ((ord_kgtotal1.value * 1) + (ord_merma_peso.value * 1)).toFixed(2);
                ord_kgtotal.value = ord_gran_tot_peso.value;

            }

            function load_tornillo(obj) {
                if (pro_id.value == 0) {
                    limpiar_datos_detalle();
                } else {
                    $.post("actions.php", {act: 85, id: pro_id.value, tornillo: obj.value},
                    function (dt) {
                        dat = dt.split('&');
                        ord_mp1.value = dat[0].trim();// materia prima 1
                        ord_mp2.value = dat[1];// materia prima 2
                        ord_mp3.value = dat[2];// materia prima 3
                        ord_mp4.value = dat[3];// materia prima 4
                        ord_mp5.value = dat[4];// materia prima 5
                        ord_mp6.value = dat[5];// materia prima 6
                        ord_mf1.value = dat[6];// porcentaje mp 1
                        ord_mf2.value = dat[7];// porcentaje mp 2
                        ord_mf3.value = dat[8];// porcentaje mp 3
                        ord_mf4.value = dat[9];// porcentaje mp 4
                        ord_mf5.value = dat[10];// porcentaje mp 5
                        ord_mf6.value = dat[11];// porcentaje mp 6
                        ord_mftotal.value = dat[12];// porcentaje mp total
                        validacion();
                    });
                }

            }
        </script>
    </head>
    <body>    
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <table id="tbl_form" border="1" >
            <thead>
                <tr>
                    <th colspan="3" >
                        Orden de Bobinado/Corte
                        <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                    </th>
                </tr>
            </thead>
            <tr>  
                <td colspan="2"  class="sbtitle" >DATOS GENERALES</td>
                <td class="sbtitle" >FORMULACION</td>
            </tr>
            <tr>
                <td>Orden # :</td>
                <td><input readonly type="text" name="ord_num_orden" id="ord_num_orden" size="20" value="<?php echo $rst['ord_num_orden'] ?>" /></td>                     
            </tr>
            <tr>
                <td>Bodega:</td>
                <td><select name="ord_bodega" id="ord_bodega"style="width:200px">
                        <option value="">SELECCIONE</option>
                        <option value="1">SEMIELABORADO</option>
                        <option value="2">TERMINADO</option>
                    </select>
                </td>

                <td rowspan="6">
                    Tornillo:<select name="ord_tornillo" id="ord_tornillo"style="width:50px" onchange="load_tornillo(this)">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select><br>
                    <select name="ord_mp1" id="ord_mp1"style="width:180px">
                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_mf1" id="ord_mf1" size="10" style="text-align:right" value="<?php echo $rst[ord_mf1] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_kg1" id="ord_kg1" size="10" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg1] ?>" readonly /> kg<br />
                    <select name="ord_mp2" id="ord_mp2" style="width:180px">

                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_mf2" id="ord_mf2" size="10" style="text-align:right"  value="<?php echo $rst[ord_mf2] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg2" id="ord_kg2" size="10" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg2] ?>" readonly /> kg<br />
                    <select name="ord_mp3" id="ord_mp3" style="width:180px">

                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_mf3" id="ord_mf3" size="10" style="text-align:right"  value="<?php echo $rst[ord_mf3] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg3" id="ord_kg3" size="10" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg3] ?>" readonly /> kg<br />
                    <select name="ord_mp4" id="ord_mp4" style="width:180px">

                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_mf4" id="ord_mf4" size="10" style="text-align:right"  value="<?php echo $rst[ord_mf4] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg4" id="ord_kg4" size="10" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg4] ?>" readonly />kg<br />               
                    <select name="ord_mp5" id="ord_mp5" style="width:180px">

                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_mf5" id="ord_mf5" size="10" style="text-align:right"  value="<?php echo $rst[ord_mf5] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg5" id="ord_kg5" size="10" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg5] ?>" readonly />kg<br />               
                    <select name="ord_mp6" id="ord_mp6" style="width:180px">

                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_mf6" id="ord_mf6" size="10" style="text-align:right" value="<?php echo $rst[ord_mf6] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg6" id="ord_kg6" size="10" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg6] ?>" readonly />kg<br />

            </tr>
            <script>
                document.getElementById("ord_bodega").value = '<?php echo $rst[ord_bodega] ?>';
                document.getElementById("ord_tornillo").value = '<?php echo $rst[ord_tornillo] ?>';
                document.getElementById("ord_mp1").value = '<?php echo $rst[ord_mp1] ?>';
                document.getElementById("ord_mp2").value = '<?php echo $rst[ord_mp2] ?>';
                document.getElementById("ord_mp3").value = '<?php echo $rst[ord_mp3] ?>';
                document.getElementById("ord_mp4").value = '<?php echo $rst[ord_mp4] ?>';
                document.getElementById("ord_mp5").value = '<?php echo $rst[ord_mp5] ?>';
                document.getElementById("ord_mp6").value = '<?php echo $rst[ord_mp6] ?>';
            </script>
            <tr>
                <td>Cliente :</td>
                <td>
                    <input type="hidden" id="cli_id" value="<?php echo $cli_id ?>"/>
                    <input type="text" id="nombre" list="clientes" size="30" onchange="load_cliente(this)" value="<?php echo $nombre ?>"/>
                </td>
            </tr>
            <tr>
                <td>Producto :</td>

                <td><select name="pro_id" id="pro_id" style="width:200px; " onchange="producto(pro_id.value)" onblur="calculo_peso(1)">
                        <option value=""> - Elija un Producto - </option>
                        <?php
                        $cns_pro = $Set->lista_productosss();
                        while ($rst_pro = pg_fetch_array($cns_pro)) {
                            echo "<option $sel value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
                        }
                        ?>
                    </select>
                    <button id="editar"  onclick="abrir()">EDITAR</button>
                </td>
            </tr>
            <script>
                document.getElementById("pro_id").value = '<?php echo $rst[pro_id] ?>';</script>
            <tr>
                <td># de Rollos:</td>
                <td>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_num_rollos" id="ord_num_rollos" onchange="calculo_peso(1)" style="text-align:right" size="10" value="<?php echo $rst[ord_num_rollos] ?>" />
                    <input type="hidden" size="1" name="unidad" id="unidad" value="<?php echo $rst[unidad] ?>">
                </td>
            </tr>
            <tr>
                <td>Peso Total a Producir:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_kgtotal" id="ord_kgtotal" size="10"   onchange="calculo_peso(2)" value="<?php echo $rst[ord_kgtotal] ?>" /> kg </td>

            </tr>
            <tr>
                <td>Fecha Pedido:</td>
                <td><input type="text" name="ord_fec_pedido" id="ord_fec_pedido" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_pedido] ?>"/>
                    <img src="../img/calendar.png" width="16"  id="im-desde" /></td>
            </tr>
            <tr>
                <td>Fecha Entrega:</td>
                <td><input type="text" name="ord_fec_entrega" id="ord_fec_entrega" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_entrega] ?>"/>
                    <img src="../img/calendar.png" width="16"  id="im-hasta" /></td>
                <?php
                $total_kg = $rst[ord_kg1] + $rst[ord_kg2] + $rst[ord_kg3] + $rst[ord_kg4] + $rst[ord_kg5] + $rst[ord_kg6];
                ?>
                <td>Total 100%: <input  readonly type="text" size="9" name="ord_mftotal" id="ord_mftotal" style="text-align:right" value="<?php echo $rst[ord_mftotal] ?>"/> % 
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Total: <input  readonly type="text" size="9" name="ord_kgtotal1" id="ord_kgtotal1" style="text-align:right" value="<?php echo $total_kg ?>"/>KG</td> 
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
                <td>Merma:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_merma" id="ord_merma" size="11" value="<?php echo $rst[ord_merma] ?>"onchange="calculo_peso(1)"/> % 
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input style="text-align:right" readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_merma_peso" id="ord_merma_peso" size="9" value="<?php echo $rst[ord_merma_peso] ?>"/> Kg </td>

            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
                <td>Gran Total:
                    <input style="text-align:right" type="text" readonly name="ord_gran_tot" id="ord_gran_tot" size="9" value="<?php echo $rst[ord_tot_fin] ?>"/> % 
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input style="text-align:right" readonly  type="text" name="ord_gran_tot_peso" id="ord_gran_tot_peso" size="9" value="<?php echo $rst[ord_tot_fin_peso] ?>" /> Kg </td>

            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" class="sbtitle" > Detalle de Producto </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td>Ancho Total :
                    <input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_anc_total" id="ord_anc_total" size="10" value="<?php echo $rst[ord_anc_total] ?>"onchange="calculo()"/> m </td>
                <td>Refilado :
                    <input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_refilado" id="ord_refilado" size="10" value="<?php echo $rst[ord_refilado] ?>"onchange="calculo()"/> m </td>

            </tr> 
            <td>Producto Principal :</td>
            <td><select name="ord_pro_principal" id="ord_pro_principal"  >
                    <option value="0"> - Elija un Producto - </option>
                    <?php
                    $cns_pro = $Set->lista_producto();
                    while ($rst_pro = pg_fetch_array($cns_pro)) {
                        echo "<option $sel value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
                    }
                    ?>
                </select></td>
            <td rowspan="4"> 
                Ancho :
                <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_pri_ancho" id="ord_pri_ancho" size="6" value="<?php echo $rst[ord_pri_ancho] ?>"/> m
                Carriles :
                <input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_pri_carril" id="ord_pri_carril" size="8" value="<?php echo $rst[ord_pri_carril] ?>" onchange="calculo(1)" />
                Faltante :
                <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_pri_faltante" id="ord_pri_faltante" size="6" value="<?php echo $rst[ord_pri_faltante] ?>"/> m<br />
                Ancho :
                <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_sec_ancho" id="ord_sec_ancho" size="6" value="<?php echo $rst[ord_sec_ancho] ?>" /> m
                Carriles :
                <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_sec_carril" id="ord_sec_carril" size="6" value="<?php echo $rst[ord_sec_carril] ?>" /><br />      
                Ancho :
                <input  readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_rep_ancho" id="ord_rep_ancho" size="6" value="<?php echo $rst[ord_rep_ancho] ?>" /> m 
                Carriles :
                <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_rep_carril" id="ord_rep_carril" size="6" value="<?php echo $rst[ord_rep_carril] ?>" /></td>
            <tr>
                <td>Producto Secundario :</td>
                <td>
                    <select name="ord_pro_secundario" id="ord_pro_secundario" style="width:182px" onchange="despliegue_ancho_producto_secundario(ord_pro_secundario.value)" ></select>
                </td>
            </tr>
            <script>
                if (<?php echo $rst[ord_pro_secundario] ?> == 0) {
                    ord_pro_secundario = "<option value='0'> - Ninguno - </option>";
                    ord_pro_secundario = "<option value='MERMA'> - MERMA - </option>";
                    $('#ord_pro_secundario').html(ord_pro_secundario);
                    document.getElementById("ord_pro_secundario").disabled = false;
                } else {
                    $.post("actions.php", {act: 52, faltante: <?php echo $rst[ord_pri_faltante] ?>, gramaje: <?php echo $rst[ord_gramaje] ?>},
                    function (dt) {
                        if (dt.length == 0) {
                            dt = "<option value='0'> - Ninguno - </option>";
                        }
                        $('#ord_pro_secundario').html(dt);
                        $('#ord_pro_secundario').val('<?php echo $rst[ord_pro_secundario] ?>');
                    });
                }
                document.getElementById("ord_pro_principal").value = '<?php echo $rst[pro_id] ?>';</script>
            <tr>
                <td>Reproceso :</td>
                <td></td> 
            </tr>
            <tr> </tr>
            <tr> </tr>
            <tr>
                <td>Largo:
                    <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_largo" id="ord_largo" size="10" value="<?php echo $rst[ord_largo] ?>" /> m </td>
                <td>Gramaje :
                    <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_gramaje" id="ord_gramaje" size="8" value="<?php echo $rst[ord_gramaje] ?>" /> gr/m2</td>   
                <td>Peso :
                    <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_peso" id="ord_peso" size="8" value="<?php echo $rst[ord_peso] ?>" /> kg</td>   
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" class="sbtitle" >Condiciones de Operacion</td>
            </tr>

            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>


            <tr>

                <td colspan="2" id="ttl_zone" >
                    <table style="width:100% ">
                        <tr>
                            <td align="left">Screw speed act.Extr A. </td>
                            <td><input type="text" name="ord_spi_temp" id="ord_spi_temp" size=20" value="<?php echo $rst[ord_spi_temp] ?>" /></td>
                            <td align="left">rpm</td>
                        </tr>
                        <tr>                        
                            <td align="left">Thoughput Extr A.</td>
                            <td><input type="text" name="ord_upp_rol_tem_controller" id="ord_upp_rol_tem_controller" size="20" value="<?php echo $rst[ord_upp_rol_tem_controller] ?>" /></td>
                            <td align="left">kg/hr</td>

                        </tr>        
                        <tr> 
                            <td align="left">meltpump Revolutions</td>
                            <td><input type="text"  name="ord_dow_rol_tem_controller" id="ord_dow_rol_tem_controller" size="20" value="<?php echo $rst[ord_dow_rol_tem_controller] ?>" /></td>
                            <td align="left">rpm</td>
                        </tr>   
                        <tr> 
                            <td align="left">Screw speed act Extr. B </td>
                            <td><input type="text" name="ord_spi_tem_controller" id="ord_spi_tem_controller" size="20" value="<?php echo $rst[ord_spi_tem_controller] ?>" /></td>
                            <td align="left">rpm</td>
                        </tr>     
                        <tr> 
                            <td align="left"> Total throughputh Extr. B </td>
                            <td><input type="text" name="ord_coo_air_temp" id="ord_coo_air_temp" size="20" value="<?php echo $rst[ord_coo_air_temp] ?>" /></td>
                            <td align="left">kg/hr</td>
                        </tr>  

                        <tr>
                            <td>Screw speed act Extr. C</td>
                            <td><input type="text" name="ord_mat_pump" id="ord_mat_pump" size=20" value="<?php echo $rst[ord_mat_pump] ?>" /></td>
                            <td align="left">rpm</td>
                        </tr>

                        <tr>
                            <td>Total throughputh Extr. C </td>
                            <td><input type="text" name="ord_spi_blower" id="ord_spi_blower" size="20" value="<?php echo $rst[ord_spi_blower] ?>" /></td>
                            <td align="left">rpm</td>
                        </tr>
                        <tr>
                            <td>Total throughputh</td>
                            <td><input type="text" name="ord_sid_blower" id="ord_sid_blower" size="20" value="<?php echo $rst[ord_sid_blower] ?>" /></td>
                            <td align="left">kg/hr</td>
                        </tr>
                        <tr>
                            <td>Softbox </td>
                            <td><input type="text" name="ord_dra_blower" id="ord_dra_blower" size="20" value="<?php echo $rst[ord_dra_blower] ?>" /></td>
                            <td hidden >text <input style="float:right " type="text" name="ord_spe_mod_auto" id="ord_spe_mod_auto" size="20" value="<?php echo $rst[ord_spe_mod_auto] ?>" /></td>
                            <td align="left">Hz</td>                        
                        </tr>
                        <tr>
                            <td>Vacuumbox </td>
                            <td><input type="text" name="ord_lap_speed" id="ord_lap_speed" size=20" value="<?php echo $rst[ord_lap_speed] ?>" /></td>
                            <td align="left">Hz</td> 
                        </tr>

                    </table>
                </td>     
                <td colspan="2" id="ttl_zone" >
                    <table style="width:100% ">
                        <tr>
                            <td align="center"></td>
                            <td align="center">Chill C1</td>
                            <td align="center">Chill C2</td>
                            <td align="center">T1</td>
                            <td align="center">B1</td>
                            <td align="center"></td>     
                        </tr>
                        <tr>
                            <td align="left">Roll Speed set</td>
                            <td><input type="text" name="ord_zo1" id="ord_zo1" size="10"  value="<?php echo $rst[ord_zo1] ?>" /></td>
                            <td align="center"> </td>
                            <td align="center"> </td>
                            <td align="center"> </td>
                            <td align="center">m/min</td>
                        </tr>
                        <tr>                        
                            <td align="left">rolls speed act.</td>
                            <td><input type="text" name="ord_zo2" id="ord_zo2"  size="10" value="<?php echo $rst[ord_zo2] ?>" /></td>
                            <td><input type="text" name="ord_zo3" id="ord_zo3"  size="10" value="<?php echo $rst[ord_zo3] ?>" /></td>
                            <td><input type="text" name="ord_zo4" id="ord_zo4"  size="10" value="<?php echo $rst[ord_zo4] ?>" /></td>
                            <td><input type="text" name="ord_zo5" id="ord_zo5"  size="10" value="<?php echo $rst[ord_zo5] ?>" /></td>
                            <td align="left">m/min</td>

                        </tr>        
                        <tr> 
                            <td align="left">Temperature set</td>
                            <td><input type="text" name="ord_zo6" id="ord_zo6"  size="10" value="<?php echo $rst[ord_zo6] ?>" /></td>
                            <td><input type="text" name=="ord_dow_rol_oil_pump" id="ord_dow_rol_oil_pump"  size="10" value="<?php echo $rst[ord_dow_rol_oil_pump] ?>" /></td>
                            <td><input type="text" name="ord_spi_rol_heating" id="ord_spi_rol_heating" size="10" value="<?php echo $rst[ord_spi_rol_heating] ?>" /></td>
                            <td align="center"></td>
                            <td align="left">C</td>
                        </tr>   
                        <tr> 
                            <td align="left">Terperature Act.</td>
                            <td><input type="text" name="ord_spi_rol_oil_pump" id="ord_spi_rol_oil_pump" size="10" value="<?php echo $rst[ord_spi_rol_oil_pump] ?>" /></td>
                            <td><input type="text" name="ord_gsm_setting" id="ord_gsm_setting" size="10" value="<?php echo $rst[ord_gsm_setting] ?>" /></td>
                            <td><input type="text" name="ord_aut_spe_adjust" id="ord_aut_spe_adjust" size="10" value="<?php echo $rst[ord_aut_spe_adjust] ?>" /></td>
                            <td align="center"></td>
                            <td align="left">C</td>
                        </tr>     

                        <tr>
                            <td>Taper</td>
                            <td><input type="text" name="ord_man_spe_setting" id="ord_man_spe_setting" size="10" value="<?php echo $rst[ord_man_spe_setting] ?>" /></td>
                            <td hidden>text <input style="float:right " type="text" name="ord_upp_rol_oil_pump" id="ord_upp_rol_oil_pump" size="20" value="<?php echo $rst[ord_upp_rol_oil_pump] ?>" /></td>
                        </tr>
                        <tr>
                            <td>Inline tension </td>
                            <td><input  type="text" name="ord_rol_mill" id="ord_rol_mill" size="10" value="<?php echo $rst[ord_rol_mill] ?>" /></td>
                            <td hidden>text <input style="float:right " type="text" name="ord_upp_rol_heating" id="ord_upp_rol_heating" size="20" value="<?php echo $rst[ord_upp_rol_heating] ?>" /></td> 
                        </tr>
                        <tr>
                            <td>Taper Curve tension </td>
                            <td><input type="text" name="ord_win_tensility" id="ord_win_tensility" size="10" value="<?php echo $rst[ord_win_tensility] ?>" /></td>
                        </tr>
                        <tr>
                            <td>Start Tension</td>
                            <td><input type="text" name="ord_mas_bra_autosetting" id="ord_mas_bra_autosetting" size="10" value="<?php echo $rst[ord_mas_bra_autosetting] ?>" /></td>
                        </tr>
                        <tr>
                            <td>ACT tension </td>
                            <td><input type="text" name="ord_rol_mil_up_down" id="ord_rol_mil_up_down" size="10" value="<?php echo $rst[ord_rol_mil_up_down] ?>" /></td>
                            <td hidden >text<input style="float:right " type="text" name="ord_dow_rol_heating" id="ord_dow_rol_heating" size="20" value="<?php echo $rst[ord_dow_rol_heating] ?>" /></td>
                        </tr>                         
                    </table>

                </td>       
            </tr>

            <tr>
                <td>Observaciones:</td>
                <td colspan="2" ><textarea name="ord_observaciones" id="ord_observaciones" style="width:100%"><?php echo $rst[ord_observaciones] ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><?php
                    if (($Prt->add == 0 || $Prt->edition == 0) && $x != 1) {
                        ?>
                        <button id="save" onclick="save(<?php echo $id ?>)">Guardar</button>
                    <?php }
                    ?>

                    <button id="cancel"  onclick="cancelar()">Cancelar</button></td>
            </tr>
            <td colspan="6">&nbsp;</td>
        </table>
</html>
