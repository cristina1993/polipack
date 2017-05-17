<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$id = $_GET [id];
$x = $_GET[x];
$fec1 = $_GET[desde];
$fec2 = $_GET[hasta];
$txt = $_GET[txt];
$cns_combomp1 = $Set->lista_combo_empa_core('26'); ///EMPA
$cns_combomp2 = $Set->lista_combo_empa_core('27'); ///core
$cns_combomp3 = $Set->lista_combo_empa_core('27'); ///des 1
$cns_combomp4 = $Set->lista_combo_empa_core('27'); ///des 2
$cns_combomp5 = $Set->lista_combo_empa_core('27'); ///des 3
$cns_combomp6 = $Set->lista_combo_empa_core('27'); ///des 4
$cns_combomp7 = $Set->lista_combo_empa_core('26'); ///EMPA
$cns_combomp8 = $Set->lista_combo_empa_core('27'); ///core
$cns_combomp9 = $Set->lista_combo_empa_core('27'); ///des 1
$cns_combomp10 = $Set->lista_combo_empa_core('27'); ///des 2
$cns_combomp11 = $Set->lista_combo_empa_core('27'); ///des 3
$cns_combomp12 = $Set->lista_combo_empa_core('27'); ///des 4
$cns_combomp13 = $Set->lista_combo_empa_core('26'); ///EMPA
$cns_combomp14 = $Set->lista_combo_empa_core('27'); ///core
$cns_combomp15 = $Set->lista_combo_empa_core('27'); ///des 1
$cns_combomp16 = $Set->lista_combo_empa_core('27'); ///des 2
$cns_combomp17 = $Set->lista_combo_empa_core('27'); ///des 3
$cns_combomp18 = $Set->lista_combo_empa_core('27'); ///des 4
$cns_combomp19 = $Set->lista_combo_empa_core('26'); ///EMPA
$cns_combomp20 = $Set->lista_combo_empa_core('27'); ///core
$cns_combomp21 = $Set->lista_combo_empa_core('27'); ///des 1
$cns_combomp22 = $Set->lista_combo_empa_core('27'); ///des 2
$cns_combomp23 = $Set->lista_combo_empa_core('27'); ///des 3
$cns_combomp24 = $Set->lista_combo_empa_core('27'); ///des 4
if (isset($_GET [id])) {
    $rst = pg_fetch_array($Set->lista_una_orden_produccion($id));
    $ped_id = $rst[ped_id];
    $rst_pro1 = pg_fetch_array($Set->lista_un_producto($rst[pro_id]));
    $rst_pro2 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro_secundario]));
    $rst_pro3 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro3]));
    $rst_pro4 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro4]));
    $rst_cli = pg_fetch_array($Set->lista_clientes_codigo($rst[cli_id]));
    $nombre = $rst_cli[cli_raz_social];
    $cli_id = $rst_cli[cli_id];
    $det = 0;
    $det_id = $rst[det_id];
    $ped_id = $rst[ped_id];
    $rst[ord_ancho_util] = $rst[ord_anc_total] - ($rst[ord_refilado] * 2);
    $rst[sumakg] = $rst[ord_kgtotal] + $rst[ord_kgtotal2] + $rst[ord_kgtotal3] + $rst[ord_kgtotal4] + $rst[ord_kgtotal_rep];
    $rst[sumam] = $rst[ord_pri_ancho] * $rst[ord_pri_carril] + $rst[ord_sec_ancho] * $rst[ord_sec_carril] + $rst[ord_ancho3] * $rst[ord_carril3] + $rst[ord_ancho4] * $rst[ord_carril4] + $rst[ord_rep_ancho] * $rst[ord_rep_carril];
    $rst[tot_por_tornillo1] = $rst[ord_mf1] + $rst[ord_mf2] + $rst[ord_mf3] + $rst[ord_mf4] + $rst[ord_mf5] + $rst[ord_mf6];
    $rst[tot_por_tornillo2] = $rst[ord_mf7] + $rst[ord_mf8] + $rst[ord_mf9] + $rst[ord_mf10] + $rst[ord_mf11] + $rst[ord_mf12];
    $rst[tot_por_tornillo3] = $rst[ord_mf13] + $rst[ord_mf14] + $rst[ord_mf15] + $rst[ord_mf16] + $rst[ord_mf17] + $rst[ord_mf18];
    $rst[tot_kg_tornillo1] = $rst[ord_kg1] + $rst[ord_kg2] + $rst[ord_kg3] + $rst[ord_kg4] + $rst[ord_kg5] + $rst[ord_kg6];
    $rst[tot_kg_tornillo2] = $rst[ord_kg7] + $rst[ord_kg8] + $rst[ord_kg9] + $rst[ord_kg10] + $rst[ord_kg11] + $rst[ord_kg12];
    $rst[tot_kg_tornillo3] = $rst[ord_kg13] + $rst[ord_kg14] + $rst[ord_kg15] + $rst[ord_kg16] + $rst[ord_kg17] + $rst[ord_kg18];
    $rst[unidad] = 1;
} else {
    $rst[ord_num_rollos] = 0;
    $rst[ord_kgtotal] = '0.0';
    if (isset($_GET[prod])) {
        $rs_pv = pg_fetch_array($Set->lista_un_det_pedido($_GET[prod]));
        $rs_p = pg_fetch_array($Set->lista_un_producto($rs_pv[pro_id]));
        $rst[pro_id] = $rs_pv[pro_id];
        $rst[unidad] = $rs_pv[det_unidad];
        if ($rst[unidad] == 1) {
            $rst[ord_num_rollos] = $rs_pv[det_cantidad];
        } else if ($rst[unidad] == 1) {
            $rst[ord_kgtotal] = $rs_pv[det_cantidad];
            $rst[ord_num_rollos] = round($rs_pv[det_cantidad] / $rs_p[pro_peso]);
        } else if ($rst[unidad] == 5) {
            $rst[ord_num_rollos] = $rs_pv[det_cantidad] * $rs_pv[det_lote];
        }
        $det = 1;
        $rst[ord_fec_pedido] = $rs_pv[ped_femision];
        $det_id = $_GET[prod];
        $ped_id = $rs_pv[ped_id];
    } else {
        $rst[pro_id] = '';
        $rst[ord_num_rollos] = 0;
        $det = 0;
        $rst[ord_fec_pedido] = date("Y-m-d");
        $det_id = 0;
        $ped_id = 0;
        $rst[unidad] = 1;
    }
    $cns = $Set->lista_orden_produccion();
    $rst[ord_pro_secundario] = 0;
    $rst[ord_pro3] = 0;
    $rst[ord_pro4] = 0;
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
    $rst[ord_kg7] = 0;
    $rst[ord_kg8] = 0;
    $rst[ord_kg9] = 0;
    $rst[ord_kg10] = 0;
    $rst[ord_kg11] = 0;
    $rst[ord_kg12] = 0;
    $rst[ord_mf6] = 0;
    $rst[ord_mf7] = 0;
    $rst[ord_mf8] = 0;
    $rst[ord_mf9] = 0;
    $rst[ord_mf10] = 0;
    $rst[ord_mf11] = 0;
    $rst[ord_mf12] = 0;
    $rst[ord_kg13] = 0;
    $rst[ord_kg14] = 0;
    $rst[ord_kg15] = 0;
    $rst[ord_kg16] = 0;
    $rst[ord_kg17] = 0;
    $rst[ord_kg18] = 0;
    $rst[ord_mf13] = 0;
    $rst[ord_mf14] = 0;
    $rst[ord_mf15] = 0;
    $rst[ord_mf16] = 0;
    $rst[ord_mf17] = 0;
    $rst[ord_mf18] = 0;
    $rst[ord_fec_entrega] = date("Y-m-d");
    $rst[ord_anc_total] = '1.8';
    $rst[ord_num_rollos2] = 0;
    $rst[ord_num_rollos3] = 0;
    $rst[ord_num_rollos4] = 0;
    $rst[ord_pri_ancho] = 0;
    $rst[ord_pri_carril] = 0;
    $rst[ord_pri_faltante] = 0;
    $rst[ord_sec_ancho] = 0;
    $rst[ord_sec_carril] = 0;
    $rst[ord_carril3] = 0;
    $rst[ord_carril4] = 0;
    $rst[ord_refilado] = '0.15';
    $rst[ord_rep_ancho] = 0;
    $rst[ord_ancho3] = 0;
    $rst[ord_ancho4] = 0;
    $rst[ord_rep_carril] = 0;
    $rst[ord_largo] = 0;
    $rst[ord_gramaje] = 0;
    $rst[ord_merma] = '0.3';
    $rst[ord_merma_peso] = '0';
    $rst[ord_tot_fin] = '0';
    $rst[ord_tot_fin_peso] = '0';

    $rst[ord_kgtotal2] = '0.0';
    $rst[ord_kgtotal3] = '0.0';
    $rst[ord_kgtotal4] = '0.0';
    $rst[ord_kgtotal_rep] = '0.0';
    $rst[ord_mftotal] = '0.0';
    $rst[ord_por_tornillo1] = 0;
    $rst[ord_por_tornillo2] = 0;
    $rst[ord_por_tornillo3] = 0;
    $rst[tot_por_tornillo1] = 0;
    $rst[tot_por_tornillo2] = 0;
    $rst[tot_por_tornillo3] = 0;
    $rst[tot_kg_tornillo1] = 0;
    $rst[tot_kg_tornillo2] = 0;
    $rst[tot_kg_tornillo3] = 0;
    $rst[pro_mp1] = 0;
    $rst[pro_mp2] = 0;
    $rst[pro_mp3] = 0;
    $rst[pro_mp4] = 0;
    $rst[pro_mp5] = 0;
    $rst[pro_mp6] = 0;
    $rst[pro_mp7] = 0;
    $rst[pro_mp8] = 0;
    $rst[pro_mp9] = 0;
    $rst[pro_mp10] = 0;
    $rst[pro_mp11] = 0;
    $rst[pro_mp12] = 0;
    $rst[pro_mp13] = 0;
    $rst[pro_mp14] = 0;
    $rst[pro_mp15] = 0;
    $rst[pro_mp16] = 0;
    $rst[pro_mp17] = 0;
    $rst[pro_mp18] = 0;
    $rst[pro_mp19] = 0;
    $rst[pro_mp20] = 0;
    $rst[pro_mp21] = 0;
    $rst[pro_mp22] = 0;
    $rst[pro_mp23] = 0;
    $rst[pro_mp24] = 0;
    $rst['pro_mf1'] = 0;
    $rst['pro_mf2'] = 0;
    $rst['pro_mf3'] = 0;
    $rst['pro_mf4'] = 0;
    $rst['pro_mf5'] = 0;
    $rst['pro_mf6'] = 0;
    $rst['pro_mf7'] = 0;
    $rst['pro_mf8'] = 0;
    $rst['pro_mf9'] = 0;
    $rst['pro_mf10'] = 0;
    $rst['pro_mf11'] = 0;
    $rst['pro_mf12'] = 0;
    $rst['opp_kg1'] = 0;
    $rst['opp_kg2'] = 0;
    $rst['opp_kg3'] = 0;
    $rst['opp_kg4'] = 0;
    $rst['opp_kg5'] = 0;
    $rst['opp_kg6'] = 0;
    $rst['opp_kg7'] = 0;
    $rst['opp_kg8'] = 0;
    $rst['opp_kg9'] = 0;
    $rst['opp_kg10'] = 0;
    $rst['opp_kg11'] = 0;
    $rst['opp_kg12'] = 0;
    $rst['opp_kg13'] = 0;
    $rst['opp_kg14'] = 0;
    $rst['opp_kg15'] = 0;
    $rst['opp_kg16'] = 0;
    $rst['opp_kg17'] = 0;
    $rst['opp_kg18'] = 0;
    $rst['opp_kg19'] = 0;
    $rst['opp_kg20'] = 0;
    $rst['opp_kg21'] = 0;
    $rst['opp_kg22'] = 0;
    $rst['opp_kg23'] = 0;
    $rst['opp_kg24'] = 0;
    $rst['mp_cnt1'] = 0;
    $rst['mp_cnt2'] = 0;
    $rst['mp_cnt3'] = 0;
    $rst['mp_cnt4'] = 0;
    $rst['mp_cnt5'] = 0;
    $rst['mp_cnt6'] = 0;
    $rst['mp_cnt7'] = 0;
    $rst['mp_cnt8'] = 0;
    $rst['mp_cnt9'] = 0;
    $rst['mp_cnt10'] = 0;
    $rst['mp_cnt11'] = 0;
    $rst['mp_cnt12'] = 0;
    $rst['mp_cnt13'] = 0;
    $rst['mp_cnt14'] = 0;
    $rst['mp_cnt15'] = 0;
    $rst['mp_cnt16'] = 0;
    $rst['mp_cnt17'] = 0;
    $rst['mp_cnt18'] = 0;
    $rst['mp_cnt19'] = 0;
    $rst['mp_cnt20'] = 0;
    $rst['mp_cnt21'] = 0;
    $rst['mp_cnt22'] = 0;
    $rst['mp_cnt23'] = 0;
    $rst['mp_cnt24'] = 0;
    $rst['opp_velocidad'] = 0;
    $rst['opp_velocidad2'] = 0;
    $rst['opp_velocidad3'] = 0;
    $rst['opp_velocidad4'] = 0;
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
    $rst[tot_batch_tornillo1] = 0;
    $rst[tot_batch_tornillo2] = 0;
    $rst[tot_batch_tornillo3] = 0;
    $rst[ord_ancho_util] = $rst[ord_anc_total] - ($rst[ord_refilado] * 2);
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
    $rst[ord_patch1] = 250;
    $rst[ord_patch2] = 250;
    $rst[ord_patch3] = 250;
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
            var ped_id = '<?php echo $ped_id ?>';
            $(function () {
                Calendar.setup({inputField: "ord_fec_pedido", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "ord_fec_entrega", ifFormat: "%Y-%m-%d", button: "im-hasta"});
//                document.getElementById("ord_pro_principal").disabled = true;
                producto('<?php echo $rst[pro_id] ?>');
                posicion_aux_window();
            });
            function limpiar_datos_detalle()
            {
                ord_anc_total.value = "1.8";
                ord_mp1.value = 0;
                ord_mp2.value = 0;
                ord_mp3.value = 0;
                ord_mp4.value = 0;
                ord_mp5.value = 0;
                ord_mp6.value = 0;
                ord_mp7.value = 0;
                ord_mp8.value = 0;
                ord_mp9.value = 0;
                ord_mp10.value = 0;
                ord_mp11.value = 0;
                ord_mp12.value = 0;
                ord_mp13.value = 0;
                ord_mp14.value = 0;
                ord_mp15.value = 0;
                ord_mp16.value = 0;
                ord_mp17.value = 0;
                ord_mp18.value = 0;
                ord_mf1.value = 0;
                ord_mf2.value = 0;
                ord_mf3.value = 0;
                ord_mf4.value = 0;
                ord_mf5.value = 0;
                ord_mf6.value = 0;
                ord_kg1.value = 0;
                ord_kg2.value = 0;
                ord_kg3.value = 0;
                ord_kg4.value = 0;
                ord_kg5.value = 0;
                ord_kg6.value = 0;
                ord_mf7.value = 0;
                ord_mf8.value = 0;
                ord_mf9.value = 0;
                ord_mf10.value = 0;
                ord_mf11.value = 0;
                ord_mf12.value = 0;
                ord_kg7.value = 0;
                ord_kg8.value = 0;
                ord_kg9.value = 0;
                ord_kg10.value = 0;
                ord_kg11.value = 0;
                ord_kg12.value = 0;
                ord_mf13.value = 0;
                ord_mf14.value = 0;
                ord_mf15.value = 0;
                ord_mf16.value = 0;
                ord_mf17.value = 0;
                ord_mf18.value = 0;
                ord_kg13.value = 0;
                ord_kg14.value = 0;
                ord_kg15.value = 0;
                ord_kg16.value = 0;
                ord_kg17.value = 0;
                ord_kg18.value = 0;
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
                ord_bodega.value = '';
                ord_peso.value = 0;
                ord_gran_tot.value = 0;
                ord_kgtotal1.value = 0;
                ord_gran_tot_peso.value = 0;
                ord_num_rollos.value = 0;
                ord_por_tornillo1.value = 0;
                ord_por_tornillo2.value = 0;
                ord_por_tornillo3.value = 0;
                tot_por_tornillo1.value = 0;
                tot_por_tornillo2.value = 0;
                tot_por_tornillo3.value = 0;
                tot_kg_tornillo1.value = 0;
                tot_kg_tornillo2.value = 0;
                tot_kg_tornillo3.value = 0;
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
                } else if (ord_bodega.value == "") {
                    alert('La bodega es un campo obligatorio.');
                    ord_bodega.focus();
                    limpiar();
                    ord_bodega.style.borderColor = "red";
                } else if (ord_etiqueta.value == "") {
                    alert('La etiqueta es un campo obligatorio.');
                    ord_etiqueta.focus();
                    limpiar();
                    ord_etiqueta.style.borderColor = "red";
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
                } else if (ord_pri_carril.value.length == 0) {
                    alert('El carril es un campo obligatorio.');
                    ord_pri_carril.focus();
                    limpiar();
                    ord_pri_carril.style.borderColor = "red";
                } else if (ord_carril2.value.length == 0) {
                    alert('El carril es un campo obligatorio.');
                    ord_carril2.focus();
                    limpiar();
                    ord_carril2.style.borderColor = "red";
                } else if (ord_carril3.value.length == 0) {
                    alert('El carril es un campo obligatorio.');
                    ord_carril3.focus();
                    limpiar();
                    ord_carril3.style.borderColor = "red";
                } else if (opp_velocidad.value.length == 0) {
                    alert('Rollos por empaque es un campo obligatorio.');
                    opp_velocidad.focus();
                    limpiar();
                    opp_velocidad.style.borderColor = "red";
                } else if (opp_velocidad2.value.length == 0) {
                    alert('Rollos por empaque es un campo obligatorio.');
                    opp_velocidad2.focus();
                    limpiar();
                    opp_velocidad2.style.borderColor = "red";
                } else if (opp_velocidad3.value.length == 0) {
                    alert('Rollos por empaque es un campo obligatorio.');
                    opp_velocidad3.focus();
                    limpiar();
                    opp_velocidad3.style.borderColor = "red";
                } else if (opp_velocidad4.value.length == 0) {
                    alert('Rollos por empaque es un campo obligatorio.');
                    opp_velocidad4.focus();
                    limpiar();
                    opp_velocidad4.style.borderColor = "red";
                } else if (mp_cnt3.value.length == 0) {
                    mp_cnt3.focus();
                    mp_cnt3.style.borderColor = "red";
                } else if (mp_cnt4.value.length == 0) {
                    mp_cnt4.focus();
                    mp_cnt4.style.borderColor = "red";
                } else if (mp_cnt5.value.length == 0) {
                    mp_cnt5.focus();
                    mp_cnt5.style.borderColor = "red";
                } else if (mp_cnt6.value.length == 0) {
                    mp_cnt6.focus();
                    mp_cnt6.style.borderColor = "red";
                } else if (mp_cnt9.value.length == 0) {
                    mp_cnt9.focus();
                    mp_cnt9.style.borderColor = "red";
                } else if (mp_cnt10.value.length == 0) {
                    mp_cnt10.focus();
                    mp_cnt10.style.borderColor = "red";
                } else if (mp_cnt11.value.length == 0) {
                    mp_cnt11.focus();
                    mp_cnt11.style.borderColor = "red";
                } else if (mp_cnt12.value.length == 0) {
                    mp_cnt12.focus();
                    mp_cnt12.style.borderColor = "red";
                } else if (mp_cnt15.value.length == 0) {
                    mp_cnt15.focus();
                    mp_cnt15.style.borderColor = "red";
                } else if (mp_cnt16.value.length == 0) {
                    mp_cnt16.focus();
                    mp_cnt16.style.borderColor = "red";
                } else if (mp_cnt17.value.length == 0) {
                    mp_cnt17.focus();
                    mp_cnt17.style.borderColor = "red";
                } else if (mp_cnt18.value.length == 0) {
                    mp_cnt18.focus();
                    mp_cnt18.style.borderColor = "red";
                } else if (mp_cnt21.value.length == 0) {
                    mp_cnt21.focus();
                    mp_cnt21.style.borderColor = "red";
                } else if (mp_cnt22.value.length == 0) {
                    mp_cnt22.focus();
                    mp_cnt22.style.borderColor = "red";
                } else if (mp_cnt23.value.length == 0) {
                    mp_cnt23.focus();
                    mp_cnt23.style.borderColor = "red";
                } else if (mp_cnt24.value.length == 0) {
                    mp_cnt24.focus();
                    mp_cnt24.style.borderColor = "red";
                } else if (ord_mf1.value.length == 0) {
                    ord_mf1.focus();
                    ord_mf1.style.borderColor = "red";
                } else if (ord_mf2.value.length == 0) {
                    ord_mf2.focus();
                    ord_mf2.style.borderColor = "red";
                } else if (ord_mf3.value.length == 0) {
                    ord_mf3.focus();
                    ord_mf3.style.borderColor = "red";
                } else if (ord_mf4.value.length == 0) {
                    ord_mf4.focus();
                    ord_mf4.style.borderColor = "red";
                } else if (ord_mf5.value.length == 0) {
                    ord_mf5.focus();
                    ord_mf5.style.borderColor = "red";
                } else if (ord_mf6.value.length == 0) {
                    ord_mf6.focus();
                    ord_mf6.style.borderColor = "red";
                } else if (ord_mf7.value.length == 0) {
                    ord_mf7.focus();
                    ord_mf7.style.borderColor = "red";
                } else if (ord_mf8.value.length == 0) {
                    ord_mf8.focus();
                    ord_mf8.style.borderColor = "red";
                } else if (ord_mf9.value.length == 0) {
                    ord_mf9.focus();
                    ord_mf9.style.borderColor = "red";
                } else if (ord_mf10.value.length == 0) {
                    ord_mf10.focus();
                    ord_mf10.style.borderColor = "red";
                } else if (ord_mf11.value.length == 0) {
                    ord_mf11.focus();
                    ord_mf11.style.borderColor = "red";
                } else if (ord_mf12.value.length == 0) {
                    ord_mf12.focus();
                    ord_mf12.style.borderColor = "red";
                } else if (ord_mf13.value.length == 0) {
                    ord_mf13.focus();
                    ord_mf13.style.borderColor = "red";
                } else if (ord_mf14.value.length == 0) {
                    ord_mf14.focus();
                    ord_mf14.style.borderColor = "red";
                } else if (ord_mf15.value.length == 0) {
                    ord_mf15.focus();
                    ord_mf15.style.borderColor = "red";
                } else if (ord_mf16.value.length == 0) {
                    ord_mf16.focus();
                    ord_mf16.style.borderColor = "red";
                } else if (ord_mf17.value.length == 0) {
                    ord_mf17.focus();
                    ord_mf17.style.borderColor = "red";
                } else if (ord_mf18.value.length == 0) {
                    ord_mf18.focus();
                    ord_mf18.style.borderColor = "red";
                } else if (ord_patch1.value.length == 0) {
                    ord_patch1.focus();
                    ord_patch1.style.borderColor = "red";
                } else if (ord_patch2.value.length == 0) {
                    ord_patch2.focus();
                    ord_patch2.style.borderColor = "red";
                } else if (ord_patch3.value.length == 0) {
                    ord_patch3.focus();
                    ord_patch3.style.borderColor = "red";
                } else {
                    var data = Array(
                            ord_num_orden.value,
                            cli_id.value,
                            pro_id.value,
                            ord_num_rollos.value,
                            ord_num_rollos2.value,
                            ord_num_rollos3.value,
                            ord_num_rollos4.value,
                            ord_mp1.value,
                            ord_mp2.value,
                            ord_mp3.value,
                            ord_mp4.value,
                            ord_mp5.value,
                            ord_mp6.value,
                            ord_mp7.value,
                            ord_mp8.value,
                            ord_mp9.value,
                            ord_mp10.value,
                            ord_mp11.value,
                            ord_mp12.value,
                            ord_mp13.value,
                            ord_mp14.value,
                            ord_mp15.value,
                            ord_mp16.value,
                            ord_mp17.value,
                            ord_mp18.value,
                            ord_mf1.value,
                            ord_mf2.value,
                            ord_mf3.value,
                            ord_mf4.value,
                            ord_mf5.value,
                            ord_mf6.value,
                            ord_mf7.value,
                            ord_mf8.value,
                            ord_mf9.value,
                            ord_mf10.value,
                            ord_mf11.value,
                            ord_mf12.value,
                            ord_mf13.value,
                            ord_mf14.value,
                            ord_mf15.value,
                            ord_mf16.value,
                            ord_mf17.value,
                            ord_mf18.value,
                            ord_mftotal.value,
                            ord_kg1.value,
                            ord_kg2.value,
                            ord_kg3.value,
                            ord_kg4.value,
                            ord_kg5.value,
                            ord_kg6.value,
                            ord_kg7.value,
                            ord_kg8.value,
                            ord_kg9.value,
                            ord_kg10.value,
                            ord_kg11.value,
                            ord_kg12.value,
                            ord_kg13.value,
                            ord_kg14.value,
                            ord_kg15.value,
                            ord_kg16.value,
                            ord_kg17.value,
                            ord_kg18.value,
                            ord_kgtotal.value,
                            ord_kgtotal2.value,
                            ord_kgtotal3.value,
                            ord_kgtotal4.value,
                            ord_kgtotal_rep.value,
                            ord_fec_pedido.value,
                            ord_fec_entrega.value,
                            ord_anc_total.value,
                            ord_refilado.value,
                            ord_pri_ancho.value,
                            ord_pri_carril.value,
                            ord_pri_faltante.value,
                            ord_pro2.value,
                            ord_pro3.value,
                            ord_pro4.value,
                            ord_ancho2.value,
                            ord_ancho3.value,
                            ord_ancho4.value,
                            ord_carril2.value,
                            ord_carril3.value,
                            ord_carril4.value,
                            ord_rep_ancho.value,
                            ord_rep_carril.value,
                            ord_largo.value,
                            ord_gramaje.value,
                            ord_observaciones.value.toUpperCase(),
                            ord_merma.value,
                            ord_merma_peso.value,
                            ord_gran_tot.value,
                            ord_gran_tot_peso.value,
                            ord_por_tornillo1.value,
                            ord_por_tornillo2.value,
                            ord_por_tornillo3.value,
                            '0', //ord_tornillo
                            ord_bodega.value,
                            pro_mp1.value,
                            pro_mp2.value,
                            pro_mp3.value,
                            pro_mp4.value,
                            pro_mp5.value,
                            pro_mp6.value,
                            pro_mp7.value,
                            pro_mp8.value,
                            pro_mp9.value,
                            pro_mp10.value,
                            pro_mp11.value,
                            pro_mp12.value,
                            pro_mp13.value,
                            pro_mp14.value,
                            pro_mp15.value,
                            pro_mp16.value,
                            pro_mp17.value,
                            pro_mp18.value,
                            pro_mp19.value,
                            pro_mp20.value,
                            pro_mp21.value,
                            pro_mp22.value,
                            pro_mp23.value,
                            pro_mp24.value,
                            pro_mf1.value,
                            pro_mf2.value,
                            pro_mf3.value,
                            pro_mf4.value,
                            pro_mf5.value,
                            pro_mf6.value,
                            pro_mf7.value,
                            pro_mf8.value,
                            pro_mf9.value,
                            pro_mf10.value,
                            pro_mf11.value,
                            pro_mf12.value,
                            opp_kg1.value,
                            opp_kg2.value,
                            opp_kg3.value,
                            opp_kg4.value,
                            opp_kg5.value,
                            opp_kg6.value,
                            opp_kg7.value,
                            opp_kg8.value,
                            opp_kg9.value,
                            opp_kg10.value,
                            opp_kg11.value,
                            opp_kg12.value,
                            opp_kg13.value,
                            opp_kg14.value,
                            opp_kg15.value,
                            opp_kg16.value,
                            opp_kg17.value,
                            opp_kg18.value,
                            opp_kg19.value,
                            opp_kg20.value,
                            opp_kg21.value,
                            opp_kg22.value,
                            opp_kg23.value,
                            opp_kg24.value,
                            opp_velocidad.value,
                            opp_velocidad2.value,
                            opp_velocidad3.value,
                            opp_velocidad4.value,
                            mp_cnt1.value,
                            mp_cnt2.value,
                            mp_cnt3.value,
                            mp_cnt4.value,
                            mp_cnt5.value,
                            mp_cnt6.value,
                            mp_cnt7.value,
                            mp_cnt8.value,
                            mp_cnt9.value,
                            mp_cnt10.value,
                            mp_cnt11.value,
                            mp_cnt12.value,
                            mp_cnt13.value,
                            mp_cnt14.value,
                            mp_cnt15.value,
                            mp_cnt16.value,
                            mp_cnt17.value,
                            mp_cnt18.value,
                            mp_cnt19.value,
                            mp_cnt20.value,
                            mp_cnt21.value,
                            mp_cnt22.value,
                            mp_cnt23.value,
                            mp_cnt24.value,
                            ped_id,
                            det_id,
                            ord_patch1.value,
                            ord_patch2.value,
                            ord_patch3.value,
                            ord_formula.value,
                            ord_etiqueta.value
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
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_prod_pedido_venta.php?ord=<?php echo $_GET[ord] ?>&cli=<?php echo $_GET[cli] ?>&ruc=<?php echo $_GET[ruc] ?>&fecha1=<?php echo $_GET[fecha1] ?>&fecha2=<?php echo $_GET[fecha2] ?>&ped_estado=<?php echo $_GET[ped_estado] ?>';
                }
            }
            function calculo_porcentage(ob) {
                ///cambia para sumar el porcentaje de tornillos
//                ord_mftotal.value = (ord_mf1.value * 1 + ord_mf2.value * 1 + ord_mf3.value * 1 + ord_mf4.value * 1 + ord_mf5.value * 1 + ord_mf6.value * 1).toFixed(2);
                tpt1 = (ord_mf1.value * 1 + ord_mf2.value * 1 + ord_mf3.value * 1 + ord_mf4.value * 1 + ord_mf5.value * 1 + ord_mf6.value * 1).toFixed(2);
                tpt2 = (ord_mf7.value * 1 + ord_mf8.value * 1 + ord_mf9.value * 1 + ord_mf10.value * 1 + ord_mf11.value * 1 + ord_mf12.value * 1).toFixed(2);
                tpt3 = (ord_mf13.value * 1 + ord_mf14.value * 1 + ord_mf15.value * 1 + ord_mf16.value * 1 + ord_mf17.value * 1 + ord_mf18.value * 1).toFixed(2);
                n = 0;
                if (tpt1 > 100) {
                    alert('La suma de porcentajes de la Extrusora B no debe pasar de 100%')
//                    while (n < 6) {
//                        n++;
//                        $('#ord_mf' + n).val('0.00');
//                    }
//                    tpt1 = '0.00
                    $(ob).val('0.00');
                    calculo_porcentage(ob);
                }
                if (tpt2 > 100) {
                    alert('La suma de porcentajes de la Extrusora A no debe pasar de 100%')
//                    while (n < 12) {
//                        n++;
//                        $('#ord_mf' + n).val('0.00');
//                    }
//                    tpt2 = '0.00';
                    $(ob).val('0.00');
                    calculo_porcentage(ob);
                }
                if (tpt3 > 100) {
                    alert('La suma de porcentajes de la Extrusora C no debe pasar de 100%')
//                    while (n < 18) {
//                        n++;
//                        $('#ord_mf' + n).val('0.00');
//                    }
//                    tpt3 = '0.00';
                    $(ob).val('0.00');
                    calculo_porcentage(ob);
                }
                tot_por_tornillo1.value = tpt1;
                tot_por_tornillo2.value = tpt2;
                tot_por_tornillo3.value = tpt3;
                ord_mftotal.value = (ord_por_tornillo1.value * 1 + ord_por_tornillo2.value * 1 + ord_por_tornillo3.value * 1).toFixed(2);
                ord_gran_tot.value = (ord_mftotal.value * 1 + ord_merma.value * 1).toFixed(2);
                suma_kg();
            }
            function calculo_kg() {
                ord_kgtotal1.value = (ord_kg1.value * 1 + ord_kg2.value * 1 + ord_kg3.value * 1 + ord_kg4.value * 1 + ord_kg5.value * 1 + ord_kg6.value * 1 +
                        ord_kg7.value * 1 + ord_kg8.value * 1 + ord_kg9.value * 1 + ord_kg10.value * 1 + ord_kg11.value * 1 + ord_kg12.value * 1 +
                        ord_kg13.value * 1 + ord_kg14.value * 1 + ord_kg15.value * 1 + ord_kg16.value * 1 + ord_kg17.value * 1 + ord_kg18.value * 1).toFixed(2);
                if (ord_num_rollos.value == "") {
                    ord_kgtotal.value = (ord_kg1.value * 1 + ord_kg2.value * 1 + ord_kg3.value * 1 + ord_kg4.value * 1 + ord_kg5.value * 1 + ord_kg6.value * 1 +
                            ord_kg7.value * 1 + ord_kg8.value * 1 + ord_kg9.value * 1 + ord_kg10.value * 1 + ord_kg11.value * 1 + ord_kg12.value * 1 +
                            ord_kg13.value * 1 + ord_kg14.value * 1 + ord_kg15.value * 1 + ord_kg16.value * 1 + ord_kg17.value * 1 + ord_kg18.value * 1).toFixed(2);
                } else {
//                    calculo_peso();
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
//                        calculo_rollos();
                        break;
                    case 1:

                        if ($("#ord_peso").val() == "" || ($("#ord_peso").val() == 0)) {
                            ord_kgtotal.value = 0;
                        } else {
                            uni = $('#unidad').val();
                            /*                   if (uni == 1) {
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
                             }*/
                            //                   num_rollos = ord_num_rollos.value;
                            //                    totpeso = ((ord_anc_total.value * ord_largo.value * ord_gramaje.value) / 1000).toFixed(2);
                            //                      rollos_madre_prod = (num_rollos / ord_pri_carril.value);
                            //                        ord_kgtotal.value = (totpeso * rollos_madre_prod).toFixed(2);
                            //                          ord_gran_tot.value = ((ord_mftotal.value * 1) + (ord_merma.value * 1)).toFixed(2);
                            ord_kgtotal.value = (ord_num_rollos.value * peso).toFixed(2);

                        }
//                        calculo_rollos();
                        break;
                    case 2:
                        if ($("#ord_kgtotal").val() != "" && peso != "") {
                            ord_num_rollos.value = (ord_kgtotal.value / peso).toFixed(0);
                        }
//                        calculo_rollos();
                        break;
                }

                calculo_rollos();

            }

            function validacion(obj) {
                n = 0;
                totpeso = ((ord_pri_ancho.value * ord_pri_carril.value * ord_largo.value * ord_gramaje.value) / 1000).toFixed(2);
                rollos_madre_prod = (ord_num_rollos.value / ord_pri_carril.value);
                ////cambia total de rollos madre a suma total de peso de todos los productos 
//                kgtotal = (totpeso * rollos_madre_prod).toFixed(2);
                kgtotal = $('#sumakg').val();
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
                $('#tot_kg_tornillo1').val(tkgt1.toFixed(2))
                $('#tot_kg_tornillo2').val(tkgt2.toFixed(2))
                $('#tot_kg_tornillo3').val(tkgt3.toFixed(2))
                ptch1 = $('#ord_patch1').val();
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
                $('#tot_batch_tornillo1').val(tpch1.toFixed(2));
                $('#tot_batch_tornillo2').val(tpch2.toFixed(2));
                $('#tot_batch_tornillo3').val(tpch3.toFixed(2));
                calculo_porcentage(obj);
            }
            function calculo(c) {
                ord_ancho_util.value = (parseFloat(ord_anc_total.value) - (parseFloat(ord_refilado.value) * 2)).toFixed(2)
                if ((ord_anc_total.value * 1) < (ord_pri_ancho.value * 1)) {
                    alert(' - Tome en cuenta que no puede ingresar un ANCHO menor al ANCHO del producto principal');
                    ord_anc_total.value = "0";
                    ord_anc_total.focus();
                    ord_anc_total.style.borderColor = "red";
                    ord_pri_carril.value = 0;
                    ord_pri_faltante.value = 0;
                    ord_pro_secundario.value = 0;
                    ////CAMBIOS///
                    ord_pro_tercero.value = 0;
                    ord_pro_cuarto.value = 0;
                    ord_ancho2.value = 0;
                    ord_ancho3.value = 0;
                    ord_ancho4.value = 0;
                    ord_carril2.value = 0;
                    ord_carril3.value = 0;
                    ord_carril4.value = 0;
                    ///
                    ord_rep_ancho.value = 0;
                    ord_rep_carril.value = 0;
                    document.getElementById("ord_pro_secundario").disabled = false;
                } else {
                    ord_carril2.value = 0;
                    ord_carril3.value = 0;
                    ord_carril4.value = 0;
                    ord_anc_total.style.borderColor = "";
                    if (c == null) {
                        ord_pri_carril.value = (((ord_anc_total.value - (2 * ord_refilado.value)) / ord_pri_ancho.value) - (((ord_anc_total.value - (2 * ord_refilado.value)) / ord_pri_ancho.value) - parseInt((ord_anc_total.value - (2 * ord_refilado.value)) / ord_pri_ancho.value))).toFixed(0);
                    }
                    faltante = (ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_refilado.value) * 2).toFixed(2);
                    ///Cambio faltante a 2 producto;        
                    rep_ancho = ((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_refilado.value * 2) - (ord_ancho2.value * ord_carril2.value) - (ord_ancho3.value * ord_carril3.value) - (ord_ancho4.value * ord_carril4.value))).toFixed(2);
                    ord_rep_ancho.value = rep_ancho;
                    if (ord_rep_ancho.value > 0) {
//                            ord_rep_carril.value = ((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value) - (((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value) - parseInt((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value));
                        ord_rep_carril.value = (rep_ancho / ord_rep_ancho.value) - ((rep_ancho / ord_rep_ancho.value) - parseInt((rep_ancho / ord_rep_ancho.value))).toFixed(0);
                        if (ord_rep_carril.value == 'NaN') {
                            ord_rep_carril.value = 0;
                        }

                    } else {
                        ord_rep_carril.value = 0;
                    }
                    ///
                    if (faltante == 'NaN') {
                        faltante = '0';
                    }
                    if (ord_pri_carril.value == 'NaN') {
                        ord_pri_carril.value = '0';
                    }
                    if (faltante < 0) {
                        alert('Sobrepasa el ancho total');
                        ord_carril2.value = 0;
                        ord_carril3.value = 0;
                        ord_carril4.value = 0;
                        calculo();
                    } else {
                        ord_pri_faltante.value = faltante;
//                        $.post("actions.php", {act: 52, faltante: ord_pri_faltante.value, gramaje: ord_gramaje.value},
//                        function (dt) {
//                            $('#ord_pro_secundario').html(dt);
//                            document.getElementById("ord_pro_secundario").disabled = false;
//                            z = $('#ord_pro_secundario').val();
//                            if (z > 0) {
//                                despliegue_ancho_producto_secundario(z);
//                            } else if (ord_pri_faltante.value == 0.0 || ord_pri_faltante.value == 0) {
//                                ord_sec_ancho.value = 0;
//                                ord_sec_carril.value = 0;
//                                ord_rep_ancho.value = 0;
//                                ord_rep_carril.value = 0;
//                            } else {
//                                ord_sec_ancho.value = 0;
//                                ord_sec_carril.value = 0;
//                                ord_rep_ancho.value = ord_pri_faltante.value;
//                                ord_rep_carril.value = 1;
//                            }
//                        });
                    }
                }
                calculo_rollos();
            }
//            function despliegue_ancho_producto_secundario(id)
//            {
//                $.post("actions.php", {act: 53, id: id},
//                function (dt) {
//                    ord_sec_ancho.value = dt;
//                    ord_sec_carril.value = ((ord_pri_faltante.value / ord_sec_ancho.value) - (ord_pri_faltante.value / ord_sec_ancho.value - parseInt(ord_pri_faltante.value / ord_sec_ancho.value))).toFixed(2);
//                    if (ord_sec_carril.value == 'NaN') {
//                        alert('El Producto Secundario \n Cuenta con un valor de Ancho 0.00 \n El cual no es permitido para el calculo');
//                        $('#save').hide();
//                    } else if (ord_sec_carril.value != 'NaN') {
//                        $('#save').show();
//                    }
//                    if (ord_pro_secundario.value == 0) {
//                        var numero = 0;
//                        ord_sec_ancho.value = numero.toFixed(2);
//                        ord_sec_carril.value = 0;
//                        if (ord_pri_faltante.value > 0) {
//
//                            document.getElementById("ord_pro_secundario").disabled = true;
//                            ord_rep_ancho.value = ord_pri_faltante.value;
//                            ord_rep_carril.value = 1;
//                        } else {
//
//                            if (ord_pri_faltante.value == 0 && ord_pro_secundario.value == 0) {
//                                ord_sec_ancho.value = 0;
//                                ord_sec_carril.value = 0;
//                                ord_rep_ancho.value = 0;
//                                ord_rep_carril.value = 0;
//                                document.getElementById("ord_pro_secundario").disabled = false; //ojo
//                            }
//                        }
//                    } else {
//
//                        ord_rep_ancho.value = ((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_refilado.value * 2) - (ord_sec_ancho.value * ord_sec_carril.value))).toFixed(2);
//                        if (ord_rep_ancho.value > 0) {
////                            ord_rep_carril.value = ((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value) - (((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value) - parseInt((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value));
//                            ord_rep_carril.value = ((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_sec_ancho.value * ord_sec_carril.value) - (ord_refilado.value * 2)).toFixed(2) / ord_rep_ancho.value) - (((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_sec_ancho.value * ord_sec_carril.value) - (ord_refilado.value * 2)).toFixed(2) / ord_rep_ancho.value) - parseInt(((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_sec_ancho.value * ord_sec_carril.value) - (ord_refilado.value * 2)).toFixed(2) / ord_rep_ancho.value))).toFixed(2);
//                            if (ord_rep_carril.value == 'NaN') {
//                                ord_rep_carril.value = 0;
//                            }
//
//                        } else {
//                            ord_rep_carril.value = 0;
//                        }
//                    }
//                });
//            }
            function producto(id) {
                if (pro_id.value == 0) {
                    limpiar_datos_detalle();
                } else {
                    pro_id.style.borderColor = "";
//                    ord_pro_principal.value = pro_id.value;
                    $.post("actions.php", {act: 51, id: id},
                    function (dt) {
                        dat = dt.split('&');
                        var a = '<?php echo $id ?>';
                        if (a.length == 0) {
                            $('#ord_mp1,#ord_mp2,#ord_mp3,#ord_mp4,#ord_mp5,#ord_mp6,#ord_mp7,#ord_mp8,#ord_mp9,#ord_mp10,#ord_mp11,#ord_mp12,#ord_mp13,#ord_mp14,#ord_mp15,#ord_mp16,#ord_mp17,#ord_mp18').html(dat[48]); /// combos de materias primas formulacion
                            ord_pri_ancho.value = dat[0]; /// ancho prod principal
                            ord_mp1.value = dat[1]; // materia prima 1
                            ord_mp2.value = dat[2]; // materia prima 2
                            ord_mp3.value = dat[3]; // materia prima 3
                            ord_mp4.value = dat[4]; // materia prima 4
                            ord_mp5.value = dat[5]; // materia prima 5
                            ord_mp6.value = dat[6]; // materia prima 6
                            ord_mf1.value = dat[7]; // porcentaje mp 1
                            ord_mf2.value = dat[8]; // porcentaje mp 2
                            ord_mf3.value = dat[9]; // porcentaje mp 3
                            ord_mf4.value = dat[10]; // porcentaje mp 4
                            ord_mf5.value = dat[11]; // porcentaje mp 5
                            ord_mf6.value = dat[12]; // porcentaje mp 6
                            ord_mftotal.value = dat[13]; // porcentaje mp total
                            ord_largo.value = dat[14]; // largo
                            ord_gramaje.value = dat[15]; //gramaje
                            ord_peso.value = dat[16]; // peso
//                            ord_zo1.value = (dat[18]);
//                            if (dat[18] == "") {///Roll Speed set
//                                $('#ord_zo1').val(0);
//                            } else {
//                                $('#ord_zo1').val(dat[18]);
//                            }
//                            if (dat[19] == "") {///rolls speed act1.
//                                $('#ord_zo2').val(0);
//                            } else {
//                                $('#ord_zo2').val(dat[19]);
//                            }
//                            if (dat[20] == "") {///rolls speed act2.
//                                $('#ord_zo3').val(0);
//                            } else {
//                                $('#ord_zo3').val(dat[20]);
//                            }
//                            if (dat[21] == "") {///rolls speed act3.
//                                $('#ord_zo4').val(0);
//                            } else {
//                                $('#ord_zo4').val(dat[21]);
//                            }
//                            if (dat[22] == "") {///rolls speed act4.
//                                $('#ord_zo5').val(0);
//                            } else {
//                                $('#ord_zo5').val(dat[22]);
//                            }
//                            if (dat[23] == "") {///Temperature set1
//                                $('#ord_zo6').val(0);
//                            } else {
//                                $('#ord_zo6').val(dat[23]);
//                            }
//                            //////////////////////////////
//                            if (dat[24] == "") {  ///Screw speed act.Extr A.
//                                $('#ord_spi_temp').val(0);
//                            } else {
//                                $('#ord_spi_temp').val(dat[24]);
//                            }
//                            if (dat[25] == "") {  /// Thoughput Extr A.
//                                $('#ord_upp_rol_tem_controller').val(0);
//                            } else {
//                                $('#ord_upp_rol_tem_controller').val(dat[25]);
//                            }
//                            if (dat[26] == "") {/// meltpump Revolutions
//                                $('#ord_dow_rol_tem_controller').val(0);
//                            } else {
//                                $('#ord_dow_rol_tem_controller').val(dat[26]);
//                            }
//                            if (dat[27] == "") {/// Screw speed act Extr. B 
//                                $('#ord_spi_tem_controller').val(0);
//                            } else {
//                                $('#ord_spi_tem_controller').val(dat[27]);
//                            }
//                            if (dat[28] == "") {/// Total throughputh Extr. B 
//                                $('#ord_coo_air_temp').val(0);
//                            } else {
//                                $('#ord_coo_air_temp').val(dat[28]);
//                            }
//                            if (dat[35] == "") {///
//                                $('#ord_upp_rol_heating').val(0);
//                            } else {
//                                $('#ord_upp_rol_heating').val(dat[35]);
//                            }
//                            if (dat[36] == "") {///
//                                $('#ord_upp_rol_oil_pump').val(0);
//                            } else {
//                                $('#ord_upp_rol_oil_pump').val(dat[36]);
//                            }
//                            if (dat[31] == "") {///
//                                $('#ord_dow_rol_heating').val(0);
//                            } else {
//                                $('#ord_dow_rol_heating').val(dat[30]);
//                            }
//                            if (dat[32] == "") {///Temperature set2
//                                $('#ord_dow_rol_oil_pump').val(0);
//                            } else {
//                                $('#ord_dow_rol_oil_pump').val(dat[32]);
//                            }
//                            if (dat[33] == "") {///Temperature set3
//                                $('#ord_spi_rol_heating').val(0);
//                            } else {
//                                $('#ord_spi_rol_heating').val(dat[33]);
//                            }
//                            if (dat[34] == "") {///Terperature Act1.
//                                $('#ord_spi_rol_oil_pump').val(0);
//                            } else {
//                                $('#ord_spi_rol_oil_pump').val(dat[34]);
//                            }
//                            if (dat[35] == "") {///Screw speed act Extr. C
//                                $('#ord_mat_pump').val(0);
//                            } else {
//                                $('#ord_mat_pump').val(dat[35]);
//                            }
//                            if (dat[36] == "") {///Total throughputh Extr. C 
//                                $('#ord_spi_blower').val(0);
//                            } else {
//                                $('#ord_spi_blower').val(dat[36]);
//                            }
//                            if (dat[37] == "") {///Total throughputh
//                                $('#ord_sid_blower').val(0);
//                            } else {
//                                $('#ord_sid_blower').val(dat[37]);
//                            }
//                            if (dat[38] == "") {///Softbox 
//                                $('#ord_dra_blower').val(0);
//                            } else {
//                                $('#ord_dra_blower').val(dat[38]);
//                            }
//                            if (dat[39] == "") {///Terperature Act2.
//                                $('#ord_gsm_setting').val(0);
//                            } else {
//                                $('#ord_gsm_setting').val(dat[39]);
//                            }
//                            if (dat[40] == "") {///Terperature Act3.
//                                $('#ord_aut_spe_adjust').val(0);
//                            } else {
//                                $('#ord_aut_spe_adjust').val(dat[40]);
//                            }
//                            if (dat[41] == "") {///
//                                $('#ord_spe_mod_auto').val(0);
//                            } else {
//                                $('#ord_spe_mod_auto').val(dat[41]);
//                            }
//                            if (dat[42] == "") {///Vacuumbox 
//                                $('#ord_lap_speed').val(0);
//                            } else {
//                                $('#ord_lap_speed').val(dat[42]);
//                            }
//                            if (dat[43] == "") {///Taper
//                                $('#ord_man_spe_setting').val(0);
//                            } else {
//                                $('#ord_man_spe_setting').val(dat[43]);
//                            }
//                            if (dat[44] == "") {///Inline tension 
//                                $('#ord_rol_mill').val(0);
//                            } else {
//                                $('#ord_rol_mill').val(dat[44]);
//                            }
//                            if (dat[45] == "") {///Taper Curve tension 
//                                $('#ord_win_tensility').val(0);
//                            } else {
//                                $('#ord_win_tensility').val(dat[45]);
//                            }
//                            if (dat[46] == "") {///Start Tension
//                                $('#ord_mas_bra_autosetting').val(0);
//                            } else {
//                                $('#ord_mas_bra_autosetting').val(dat[46]);
//                            }
//                            if (dat[47] == "") {///ACT tension 
//                                $('#ord_rol_mil_up_down').val(0);
//                            } else {
//                                $('#ord_rol_mil_up_down').val(dat[47]);
//                            }
                            ord_mp7.value = dat[49]; // materia prima 7
                            ord_mp8.value = dat[50]; // materia prima 8
                            ord_mp9.value = dat[51]; // materia prima 9
                            ord_mp10.value = dat[52]; // materia prima 10
                            ord_mp11.value = dat[53]; // materia prima 11
                            ord_mp12.value = dat[54]; // materia prima 12
                            ord_mf7.value = dat[55]; // porcentaje mp 7
                            ord_mf8.value = dat[56]; // porcentaje mp 8
                            ord_mf9.value = dat[57]; // porcentaje mp 9
                            ord_mf10.value = dat[58]; // porcentaje mp 10
                            ord_mf11.value = dat[59]; // porcentaje mp 11
                            ord_mf12.value = dat[60]; // porcentaje mp 12
                            ord_mp13.value = dat[62]; // materia prima 13
                            ord_mp14.value = dat[63]; // materia prima 14
                            ord_mp15.value = dat[64]; // materia prima 15
                            ord_mp16.value = dat[65]; // materia prima 16
                            ord_mp17.value = dat[66]; // materia prima 17
                            ord_mp18.value = dat[67]; // materia prima 18
                            ord_mf13.value = dat[68]; // porcentaje mp 13
                            ord_mf14.value = dat[69]; // porcentaje mp 14
                            ord_mf15.value = dat[70]; // porcentaje mp 15
                            ord_mf16.value = dat[71]; // porcentaje mp 16
                            ord_mf17.value = dat[72]; // porcentaje mp 17
                            ord_mf18.value = dat[73]; // porcentaje mp 18
                            ord_por_tornillo1.value = dat[75]; // porcentaje mp 18
                            ord_por_tornillo2.value = dat[76]; // porcentaje mp 18
                            ord_por_tornillo3.value = dat[77]; // porcentaje mp 18
                            calculo();
//                            alert(<?php echo $rst[unidad] ?>);
                            if (det != '') {
                                if (<?php echo $rst[unidad] ?> == 1 || <?php echo $rst[unidad] ?> == 5)
                                    calculo_peso(1);
                            } else {
                                calculo_peso(2);
                            }
                        } else {
//                            pro_id.value = dat[0];
                            ord_peso.value = dat[16];
                            $('#ord_mp1,#ord_mp2,#ord_mp3,#ord_mp4,#ord_mp5,#ord_mp6,#ord_mp7,#ord_mp8,#ord_mp9,#ord_mp10,#ord_mp11,#ord_mp12,#ord_mp13,#ord_mp14,#ord_mp15,#ord_mp16,#ord_mp17,#ord_mp18').html(dat[48]);
                            $('#ord_mp1').val(<?php echo $rst[ord_mp1] ?>);
                            $('#ord_mp2').val(<?php echo $rst[ord_mp2] ?>);
                            $('#ord_mp3').val(<?php echo $rst[ord_mp3] ?>);
                            $('#ord_mp4').val(<?php echo $rst[ord_mp4] ?>);
                            $('#ord_mp5').val(<?php echo $rst[ord_mp5] ?>);
                            $('#ord_mp6').val(<?php echo $rst[ord_mp6] ?>);
                            $('#ord_mp7').val(<?php echo $rst[ord_mp7] ?>);
                            $('#ord_mp8').val(<?php echo $rst[ord_mp8] ?>);
                            $('#ord_mp9').val(<?php echo $rst[ord_mp9] ?>);
                            $('#ord_mp10').val(<?php echo $rst[ord_mp10] ?>);
                            $('#ord_mp11').val(<?php echo $rst[ord_mp11] ?>);
                            $('#ord_mp12').val(<?php echo $rst[ord_mp12] ?>);
                            $('#ord_mp13').val(<?php echo $rst[ord_mp13] ?>);
                            $('#ord_mp14').val(<?php echo $rst[ord_mp14] ?>);
                            $('#ord_mp15').val(<?php echo $rst[ord_mp15] ?>);
                            $('#ord_mp16').val(<?php echo $rst[ord_mp16] ?>);
                            $('#ord_mp17').val(<?php echo $rst[ord_mp17] ?>);
                            $('#ord_mp18').val(<?php echo $rst[ord_mp18] ?>);
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
                ord_kgtotal1.value = (ord_kg1.value * 1 + ord_kg2.value * 1 + ord_kg3.value * 1 + ord_kg4.value * 1 + ord_kg5.value * 1 + ord_kg6.value * 1 +
                        ord_kg7.value * 1 + ord_kg8.value * 1 + ord_kg9.value * 1 + ord_kg10.value * 1 + ord_kg11.value * 1 + ord_kg12.value * 1 +
                        ord_kg13.value * 1 + ord_kg14.value * 1 + ord_kg15.value * 1 + ord_kg16.value * 1 + ord_kg17.value * 1 + ord_kg18.value * 1).toFixed(2);
                ord_merma_peso.value = (((ord_kgtotal1.value * 1) * (ord_merma.value * 1)) / 100).toFixed(2);
                ord_gran_tot_peso.value = ((ord_kgtotal1.value * 1) + (ord_merma_peso.value * 1)).toFixed(2);
//                ord_kgtotal.value = ord_gran_tot_peso.value;
                calculo_empaque();
            }

            function load_datos_mp(obj) {
                n = obj.lang;
                $.post("actions_padding.php", {op: 5, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dt.length != 0) {
                        $('#mp_kg' + n).val(dat[0]);
                        calculo_empaque();
                    }
                });
            }

            function calculo_empaque() {

                if ($("#ord_num_rollos").val().length == 0) {
                    cnt = 0;
                } else {
                    cnt = parseFloat($("#ord_num_rollos").val());
                }

                if ($("#ord_num_rollos2").val().length == 0) {
                    cnt2 = 0;
                } else {
                    cnt2 = parseFloat($("#ord_num_rollos2").val());
                }

                if ($("#ord_num_rollos3").val().length == 0) {
                    cnt3 = 0;
                } else {
                    cnt3 = parseFloat($("#ord_num_rollos3").val());
                }

                if ($("#ord_num_rollos4").val().length == 0) {
                    cnt4 = 0;
                } else {
                    cnt4 = parseFloat($("#ord_num_rollos4").val());
                }

                if ($("#opp_velocidad").val().length == 0) {
                    r = 0;
                } else {
                    r = parseFloat($("#opp_velocidad").val());
                }

                if ($("#opp_velocidad2").val().length == 0) {
                    r2 = 0;
                } else {
                    r2 = parseFloat($("#opp_velocidad2").val());
                }

                if ($("#opp_velocidad3").val().length == 0) {
                    r3 = 0;
                } else {
                    r3 = parseFloat($("#opp_velocidad3").val());
                }

                if ($("#opp_velocidad4").val().length == 0) {
                    r4 = 0;
                } else {
                    r4 = parseFloat($("#opp_velocidad4").val());
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

                if ($("#mp_cnt7").val().length == 0) {
                    pemp7 = 0;
                } else {
                    pemp7 = parseFloat($("#mp_cnt7").val());
                }

                if ($("#mp_cnt8").val().length == 0) {
                    pemp8 = 0;
                } else {
                    pemp8 = parseFloat($("#mp_cnt8").val());
                }

                if ($("#mp_cnt9").val().length == 0) {
                    pemp9 = 0;
                } else {
                    pemp9 = parseFloat($("#mp_cnt9").val());
                }

                if ($("#mp_cnt10").val().length == 0) {
                    pemp10 = 0;
                } else {
                    pemp10 = parseFloat($("#mp_cnt10").val());
                }

                if ($("#mp_cnt11").val().length == 0) {
                    pemp11 = 0;
                } else {
                    pemp11 = parseFloat($("#mp_cnt11").val());
                }

                if ($("#mp_cnt12").val().length == 0) {
                    pemp12 = 0;
                } else {
                    pemp12 = parseFloat($("#mp_cnt12").val());
                }

                if ($("#mp_cnt13").val().length == 0) {
                    pemp13 = 0;
                } else {
                    pemp13 = parseFloat($("#mp_cnt13").val());
                }

                if ($("#mp_cnt14").val().length == 0) {
                    pemp14 = 0;
                } else {
                    pemp14 = parseFloat($("#mp_cnt14").val());
                }

                if ($("#mp_cnt15").val().length == 0) {
                    pemp15 = 0;
                } else {
                    pemp15 = parseFloat($("#mp_cnt15").val());
                }

                if ($("#mp_cnt16").val().length == 0) {
                    pemp16 = 0;
                } else {
                    pemp16 = parseFloat($("#mp_cnt16").val());
                }

                if ($("#mp_cnt17").val().length == 0) {
                    pemp17 = 0;
                } else {
                    pemp17 = parseFloat($("#mp_cnt17").val());
                }

                if ($("#mp_cnt18").val().length == 0) {
                    pemp18 = 0;
                } else {
                    pemp18 = parseFloat($("#mp_cnt18").val());
                }

                if ($("#mp_cnt19").val().length == 0) {
                    pemp19 = 0;
                } else {
                    pemp19 = parseFloat($("#mp_cnt19").val());
                }

                if ($("#mp_cnt20").val().length == 0) {
                    pemp20 = 0;
                } else {
                    pemp20 = parseFloat($("#mp_cnt20").val());
                }

                if ($("#mp_cnt21").val().length == 0) {
                    pemp21 = 0;
                } else {
                    pemp21 = parseFloat($("#mp_cnt21").val());
                }

                if ($("#mp_cnt22").val().length == 0) {
                    pemp22 = 0;
                } else {
                    pemp22 = parseFloat($("#mp_cnt22").val());
                }

                if ($("#mp_cnt23").val().length == 0) {
                    pemp23 = 0;
                } else {
                    pemp23 = parseFloat($("#mp_cnt23").val());
                }

                if ($("#mp_cnt24").val().length == 0) {
                    pemp24 = 0;
                } else {
                    pemp24 = parseFloat($("#mp_cnt24").val());
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
                if ($("#mp_kg7").val().length == 0) {
                    okg7 = 0;
                } else {
                    okg7 = parseFloat($("#mp_kg7").val());
                }
                if ($("#mp_kg8").val().length == 0) {
                    okg8 = 0;
                } else {
                    okg8 = parseFloat($("#mp_kg8").val());
                }
                if ($("#mp_kg9").val().length == 0) {
                    okg9 = 0;
                } else {
                    okg9 = parseFloat($("#mp_kg9").val());
                }
                if ($("#mp_kg10").val().length == 0) {
                    okg10 = 0;
                } else {
                    okg10 = parseFloat($("#mp_kg10").val());
                }
                if ($("#mp_kg11").val().length == 0) {
                    okg11 = 0;
                } else {
                    okg11 = parseFloat($("#mp_kg11").val());
                }
                if ($("#mp_kg12").val().length == 0) {
                    okg12 = 0;
                } else {
                    okg12 = parseFloat($("#mp_kg12").val());
                }
                if ($("#mp_kg13").val().length == 0) {
                    okg13 = 0;
                } else {
                    okg13 = parseFloat($("#mp_kg13").val());
                }
                if ($("#mp_kg14").val().length == 0) {
                    okg14 = 0;
                } else {
                    okg14 = parseFloat($("#mp_kg14").val());
                }
                if ($("#mp_kg15").val().length == 0) {
                    okg15 = 0;
                } else {
                    okg15 = parseFloat($("#mp_kg15").val());
                }
                if ($("#mp_kg16").val().length == 0) {
                    okg16 = 0;
                } else {
                    okg16 = parseFloat($("#mp_kg16").val());
                }
                if ($("#mp_kg17").val().length == 0) {
                    okg17 = 0;
                } else {
                    okg17 = parseFloat($("#mp_kg17").val());
                }
                if ($("#mp_kg18").val().length == 0) {
                    okg18 = 0;
                } else {
                    okg18 = parseFloat($("#mp_kg18").val());
                }
                if ($("#mp_kg19").val().length == 0) {
                    okg19 = 0;
                } else {
                    okg19 = parseFloat($("#mp_kg19").val());
                }
                if ($("#mp_kg20").val().length == 0) {
                    okg20 = 0;
                } else {
                    okg20 = parseFloat($("#mp_kg20").val());
                }
                if ($("#mp_kg21").val().length == 0) {
                    okg21 = 0;
                } else {
                    okg21 = parseFloat($("#mp_kg21").val());
                }
                if ($("#mp_kg22").val().length == 0) {
                    okg22 = 0;
                } else {
                    okg22 = parseFloat($("#mp_kg22").val());
                }
                if ($("#mp_kg23").val().length == 0) {
                    okg23 = 0;
                } else {
                    okg23 = parseFloat($("#mp_kg23").val());
                }
                if ($("#mp_kg24").val().length == 0) {
                    okg24 = 0;
                } else {
                    okg24 = parseFloat($("#mp_kg24").val());
                }
                ///cantidad de empaque
                if (cnt != '0' && r != '0') {
                    ct1 = cnt / r;
                } else {
                    ct1 = 0;
                }
                if (cnt2 != '0' && r2 != '0') {
                    ct2 = cnt2 / r2;
                } else {
                    ct2 = 0;
                }
                if (cnt3 != '0' && r3 != '0') {
                    ct3 = cnt3 / r3;
                } else {
                    ct3 = 0;
                }
                if (cnt4 != '0' && r4 != '0') {
                    ct4 = cnt4 / r4;
                } else {
                    ct4 = 0;
                }
                d = ct1.toString().split('.');
                if (parseFloat(d[1]) > 0) {
                    ce1 = ct1 + 1;
                } else {
                    ce1 = ct1;
                }
                d2 = ct2.toString().split('.');
                if (parseFloat(d2[1]) > 0) {
                    ce2 = ct2 + 1;
                } else {
                    ce2 = ct2;
                }
                d3 = ct3.toString().split('.');
                if (parseFloat(d3[1]) > 0) {
                    ce3 = ct3 + 1;
                } else {
                    ce3 = ct3;
                }
                d4 = ct4.toString().split('.');
                if (parseFloat(d4[1]) > 0) {
                    ce4 = ct4 + 1;
                } else {
                    ce4 = ct4;
                }
                $("#mp_cnt1").val(ce1.toFixed());
                $("#mp_cnt2").val(cnt);
                $("#mp_cnt7").val(ce2.toFixed());
                $("#mp_cnt8").val(cnt2);
                $("#mp_cnt13").val(ce3.toFixed());
                $("#mp_cnt14").val(cnt3);
                $("#mp_cnt19").val(ce4.toFixed());
                $("#mp_cnt20").val(cnt4);
                pt1 = ce1 * okg1;
                pt2 = okg2 * cnt;
                pt3 = pemp3 * okg3;
                pt4 = pemp4 * okg4;
                pt5 = pemp5 * okg5;
                pt6 = pemp6 * okg6;
                pt7 = ce2 * okg7;
                pt8 = okg8 * cnt2;
                pt9 = pemp9 * okg9;
                pt10 = pemp10 * okg10;
                pt11 = pemp11 * okg11;
                pt12 = pemp12 * okg12;
                pt13 = ce3 * okg13;
                pt14 = okg14 * cnt3;
                pt15 = pemp15 * okg15;
                pt16 = pemp16 * okg16;
                pt17 = pemp17 * okg17;
                pt18 = pemp18 * okg18;
                pt19 = ce4 * okg19;
                pt20 = okg20 * cnt4;
                pt21 = pemp21 * okg21;
                pt22 = pemp22 * okg22;
                pt23 = pemp23 * okg23;
                pt24 = pemp24 * okg24;
                $("#opp_kg1").val(pt1.toFixed(2));
                $("#opp_kg2").val(pt2.toFixed(2));
                $("#opp_kg3").val(pt3.toFixed(2));
                $("#opp_kg4").val(pt4.toFixed(2));
                $("#opp_kg5").val(pt5.toFixed(2));
                $("#opp_kg6").val(pt6.toFixed(2));
                $("#opp_kg7").val(pt7.toFixed(2));
                $("#opp_kg8").val(pt8.toFixed(2));
                $("#opp_kg9").val(pt9.toFixed(2));
                $("#opp_kg10").val(pt10.toFixed(2));
                $("#opp_kg11").val(pt11.toFixed(2));
                $("#opp_kg12").val(pt12.toFixed(2));
                $("#opp_kg13").val(pt13.toFixed(2));
                $("#opp_kg14").val(pt14.toFixed(2));
                $("#opp_kg15").val(pt15.toFixed(2));
                $("#opp_kg16").val(pt16.toFixed(2));
                $("#opp_kg17").val(pt17.toFixed(2));
                $("#opp_kg18").val(pt18.toFixed(2));
                $("#opp_kg19").val(pt19.toFixed(2));
                $("#opp_kg20").val(pt20.toFixed(2));
                $("#opp_kg21").val(pt21.toFixed(2));
                $("#opp_kg22").val(pt22.toFixed(2));
                $("#opp_kg23").val(pt23.toFixed(2));
                $("#opp_kg24").val(pt24.toFixed(2));
                ///peso Material
                pmat = parseFloat($("#ord_kgtotal").val());
                pmat2 = parseFloat($("#ord_kgtotal2").val());
                pmat3 = parseFloat($("#ord_kgtotal3").val());
                pmat4 = parseFloat($("#ord_kgtotal4").val());
                $("#pro_mf1").val(pmat.toFixed(2));
                $("#pro_mf4").val(pmat2.toFixed(2));
                $("#pro_mf7").val(pmat3.toFixed(2));
                $("#pro_mf10").val(pmat4.toFixed(2));
                ///peso Insumos

//                pins = pt1 + pt2 + pt3 + pt4 + pt5 + pt6;
                pins = pt2;
//                pins2 = pt7 + pt8 + pt9 + pt10 + pt11 + pt12;
                pins2 = pt8;
//                pins3 = pt13 + pt14 + pt15 + pt16 + pt17 + pt18;
                pins3 = pt14;
//                pins4 = pt19 + pt20 + pt21 + pt22 + pt23 + pt24;
                pins4 = pt20;
                $("#pro_mf2").val(pins.toFixed(2));
                $("#pro_mf5").val(pins2.toFixed(2));
                $("#pro_mf8").val(pins3.toFixed(2));
                $("#pro_mf11").val(pins4.toFixed(2));
                ///peso Total
                ptotal = pmat + pins;
                ptotal2 = pmat2 + pins2;
                ptotal3 = pmat3 + pins3;
                ptotal4 = pmat4 + pins4;
                $("#pro_mf3").val(ptotal.toFixed(2));
                $("#pro_mf6").val(ptotal2.toFixed(2));
                $("#pro_mf9").val(ptotal3.toFixed(2));
                $("#pro_mf12").val(ptotal4.toFixed(2));
            }

            function buscar_producto(id) {
                n = id.lang;
                if (id.value == 0) {
                    $("#ord_ancho" + n).val('0');
                    $("#peso_pr" + n).val('0');
                    $("#ord_kgtotal" + n).val('0');
                    $("#ord_num_rollos" + n).val('0');
                    $("#ord_carril" + n).val('0');
                    calculo_peso(4, id);
                } else {
                    $.post("actions.php", {act: 51, id: id.value},
                    function (dt) {
                        dat = dt.split('&');
                        $("#ord_ancho" + n).val(dat[0]);
                        $("#peso_pr" + n).val(dat[16]);
//                        calculo_rollos(id);
                        calculo_peso(4, id);
                    });
                }

            }

            function calculo_rollos(ob) {
//                n = ob.lang;
                rep_ancho = ((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_refilado.value * 2) - (ord_ancho2.value * ord_carril2.value) - (ord_ancho3.value * ord_carril3.value) - (ord_ancho4.value * ord_carril4.value))).toFixed(2);
                ord_rep_ancho.value = rep_ancho;
                if (rep_ancho > 0) {
//                            ord_rep_carril.value = ((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value) - (((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value) - parseInt((ord_rep_ancho.value - (2 * ord_refilado.value)) / ord_rep_ancho.value));
                    ord_rep_carril.value = (rep_ancho / ord_rep_ancho.value) - ((rep_ancho / ord_rep_ancho.value) - parseInt((rep_ancho / ord_rep_ancho.value))).toFixed(0);
                    if (ord_rep_carril.value == 'NaN') {
                        ord_rep_carril.value = 0;
                    }
                } else {
                    ord_rep_carril.value = 0;
                }

                n = 1;
                while (n < 4) {
                    n++;
                    num_rollo = parseFloat(ord_num_rollos.value * 1 / ord_pri_carril.value * 1).toFixed(2) * $('#ord_carril' + n).val() * 1;

                    if ($('#ord_pro' + n).val() != '0') {
                        if (n == 1) {
                            peso_tot = $('#peso_pr' + n).val() * 1 * num_rollo;
                        } else {
                            pes_metro = parseFloat($('#ord_kgtotal').val() * 1 / ((ord_pri_carril.value * 1) * (ord_pri_ancho.value * 1)));
                            peso_tot = pes_metro * ($('#ord_ancho' + n).val() * 1) * ($('#ord_carril' + n).val() * 1);
                        }
                        if (peso_tot == 'NaN') {
                            peso_tot = 0;
                        }
                        $('#ord_num_rollos' + n).val(num_rollo.toFixed(0));
                        $('#ord_kgtotal' + n).val(peso_tot.toFixed(2));
                    }
                }
                sum_4kg = $('#ord_kgtotal').val() * 1 + $('#ord_kgtotal2').val() * 1 + $('#ord_kgtotal3').val() * 1 + $('#ord_kgtotal4').val() * 1;
                sum_m4 = parseFloat(($('#ord_pri_ancho').val() * $('#ord_pri_carril').val()) + ($('#ord_ancho2').val() * $('#ord_carril2').val()) + ($('#ord_ancho3').val() * $('#ord_carril3').val()) + ($('#ord_ancho4').val() * $('#ord_carril4').val()));
                if (parseFloat($('#ord_rep_ancho').val()) > 0) {
                    peso_rep = (sum_4kg / sum_m4).toFixed(2) * ($('#ord_rep_ancho').val() * $('#ord_rep_carril').val());
                } else {
                    peso_rep = 0;
                }
                $('#ord_kgtotal_rep').val(peso_rep.toFixed(2));
                sum_kg = sum_4kg + peso_rep;
                sum_m = parseFloat(sum_m4 + ($('#ord_rep_ancho').val() * $('#ord_rep_carril').val()));
                $('#sumakg').val(sum_kg.toFixed(2));
                $('#sumam').val(sum_m.toFixed(2));
                if (sum_m.toFixed(2) > parseFloat($('#ord_ancho_util').val())) {
                    alert('No puede sobrepasar el ancho util');
                    $(ob).val('0');
                    calculo_rollos(ob);
                }
                validacion();
            }

            function load_formula(id) {
                $.post("actions.php", {act: 86, id: id.value},
                function (dt) {
                    dat = dt.split('&');
                    $('#ord_mp1,#ord_mp2,#ord_mp3,#ord_mp4,#ord_mp5,#ord_mp6,#ord_mp7,#ord_mp8,#ord_mp9,#ord_mp10,#ord_mp11,#ord_mp12,#ord_mp13,#ord_mp14,#ord_mp15,#ord_mp16,#ord_mp17,#ord_mp18').html(dat[0]); /// combos de materias primas formulacion
                    $("#ord_mp1").val(dat[1]);
                    $("#ord_mp2").val(dat[2]);
                    $("#ord_mp3").val(dat[3]);
                    $("#ord_mp4").val(dat[4]);
                    $("#ord_mp5").val(dat[5]);
                    $("#ord_mp6").val(dat[6]);
                    $("#ord_mp7").val(dat[7]);
                    $("#ord_mp8").val(dat[8]);
                    $("#ord_mp9").val(dat[9]);
                    $("#ord_mp10").val(dat[10]);
                    $("#ord_mp11").val(dat[11]);
                    $("#ord_mp12").val(dat[12]);
                    $("#ord_mp13").val(dat[13]);
                    $("#ord_mp14").val(dat[14]);
                    $("#ord_mp15").val(dat[15]);
                    $("#ord_mp16").val(dat[16]);
                    $("#ord_mp17").val(dat[17]);
                    $("#ord_mp18").val(dat[18]);
                    $("#ord_mf1").val(dat[19]);
                    $("#ord_mf2").val(dat[20]);
                    $("#ord_mf3").val(dat[21]);
                    $("#ord_mf4").val(dat[22]);
                    $("#ord_mf5").val(dat[23]);
                    $("#ord_mf6").val(dat[24]);
                    $("#ord_mf7").val(dat[25]);
                    $("#ord_mf8").val(dat[26]);
                    $("#ord_mf9").val(dat[27]);
                    $("#ord_mf10").val(dat[28]);
                    $("#ord_mf11").val(dat[29]);
                    $("#ord_mf12").val(dat[30]);
                    $("#ord_mf13").val(dat[31]);
                    $("#ord_mf14").val(dat[32]);
                    $("#ord_mf15").val(dat[33]);
                    $("#ord_mf16").val(dat[34]);
                    $("#ord_mf17").val(dat[35]);
                    $("#ord_mf18").val(dat[36]);
                    $("#ord_por_tornillo1").val(dat[37]);
                    $("#ord_por_tornillo2").val(dat[38]);
                    $("#ord_por_tornillo3").val(dat[39]);
                    $("#ord_patch1").val(dat[40]);
                    $("#ord_patch2").val(dat[41]);
                    $("#ord_patch3").val(dat[42]);
                    validacion();
                });
            }
        </script>
        <style>
            tbody{
                float:left;
            }
            .sel{
                width:120px; 
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
        <table id="tbl_form" border="1" >
            <thead>
                <tr><th>ORDEN DE PRODUCCION<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
            </thead>
            <tr><td colspan="10" class="sbtitle" >DATOS GENERALES</td></tr>

            <!------------------------------------------------------------------------- DATOS GENERALES ----------------------------------------------------------------------------------->
            <tr>
                <td colspan="10">
                    <table colspan="10">
                        <tr>
                            <td >Orden # :</td>
                            <td ><input readonly type="text" name="ord_num_orden" id="ord_num_orden" size="20" value="<?php echo $rst['ord_num_orden'] ?>" /></td>   

                            <td>Fecha Pedido:</td>
                            <td><input readonly type="text" name="ord_fec_pedido" id="ord_fec_pedido" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_pedido] ?>"/>

                            <td>Bodega:</td>
                            <td ><select name="ord_bodega" id="ord_bodega"style="width:200px">
                                    <option value="">SELECCIONE</option>
                                    <option value="1">SEMIELABORADO</option>
                                    <option value="2">TERMINADO</option></select></td>

                            <td>Fecha Entrega:</td>
                            <td><input type="text" name="ord_fec_entrega" id="ord_fec_entrega" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_entrega] ?>"/>

                            <td>Cliente:</td>
                            <td> <input type="hidden" id="cli_id" value="<?php echo $cli_id ?>"/><input type="text" id="nombre" list="clientes" size="30" onchange="load_cliente(this)" value="<?php echo $nombre ?>"/></td>
                            <td>Etiqueta:</td>
                            <td ><select name="ord_etiqueta" id="ord_etiqueta"style="width:200px">
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
                            <td>&nbsp;</td>
                        </tr>

                    </table>
                </td>
            </tr>
            <tr ><td colspan="10" class="sbtitle" >PRODUCTOS</td></tr> 
            <tr>
                <td colspan="10">
                    <table>
                        <!------------------------------------------------------------------------- PRimera linea  ----------------------------------------------------------------------------------->                            
                        <!------------------------------------------------------------------------- PRimera linea  ----------------------------------------------------------------------------------->                            
                        <!------------------------------------------------------------------------- PRimera linea  ----------------------------------------------------------------------------------->                            
                        <tr>
                            <td >PRODUCTO PRIMARIO:</td>
                            <td ><select name="pro_id" id="pro_id" style="width:200px; " onchange="producto(pro_id.value)">
                                    <option value=""> - Elija un Producto - </option>
                                    <?php
                                    $cns_pro = $Set->lista_productosss();
                                    while ($rst_pro = pg_fetch_array($cns_pro)) {
                                        echo "<option $sel value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
                                    }
                                    ?>
                                </select>
                                <!--    <button id="editar"  onclick="abrir()">EDITAR</button>--> 
                            </td>
                            <td align=right >#Rollos:</td>
                            <td >
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_num_rollos" id="ord_num_rollos" onchange="calculo_peso(1)" style="text-align:right" size="12" value="<?php echo $rst[ord_num_rollos] ?>" />
                                <input type="hidden" size="1" name="unidad" id="unidad" value="<?php echo $rst[unidad] ?>">
                            </td>
                            <td >Peso</td>
                            <td >
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_kgtotal" id="ord_kgtotal" size="12"   onchange="calculo_peso(2)" value="<?php echo $rst[ord_kgtotal] ?>" style="text-align:right"/> kg 
                            </td>

<!--                        <td hidden >Producto Principal:</td>
                        <td hidden><select name="ord_pro_principal" id="ord_pro_principal"  >
                                <option value="0"> - Elija un Producto - </option> <?php
                            $cns_pro = $Set->lista_producto();
                            while ($rst_pro = pg_fetch_array($cns_pro)) {
                                echo "<option $sel value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
                            }
                            ?>
                            </select></td>-->
                            <td>Ancho:<input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_pri_ancho" id="ord_pri_ancho" size="6" value="<?php echo $rst[ord_pri_ancho] ?>"/> m</td>
                            <td>Carriles:<input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9]/, '');" type="text" name="ord_pri_carril" id="ord_pri_carril" size="6" value="<?php echo $rst[ord_pri_carril] ?>" onchange="calculo(1)" /></td>
                        </tr>
                        <!------------------------------------------------------------------------- SECUNDARIO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- SECUNDARIO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- SECUNDARIO   ----------------------------------------------------------------------------------->                             
                        <tr>
                            <td>Producto Secundario :</td>
                            <td ><select name="ord_pro2" id="ord_pro2" style="width:200px;" onchange="buscar_producto(this)" lang="2">
                                    <option value="0"> - Elija un Producto - </option>
                                    <?php
                                    $cns_pro = $Set->lista_productosss();
                                    while ($rst_pro = pg_fetch_array($cns_pro)) {
                                        echo "<option $sel value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
                                    }
                                    ?>
                                </select>
                                <!--    <button id="editar"  onclick="abrir()">EDITAR</button>--> 
                            </td>

<!--<td><select name="ord_pro_secundario" id="ord_pro_secundario" style="width:182px" onchange="despliegue_ancho_producto_secundario(ord_pro_secundario.value)" ></select></td>-->
                            <td align=right >#Rollos:</td>
                            <td >
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_num_rollos2" id="ord_num_rollos2" readonly onchange="calculo_peso(1)" style="text-align:right" size="10" value="<?php echo $rst[ord_num_rollos2] ?>" />
                                <input type="hidden" size="1" name="unidad" id="unidad" value="<?php echo $rst[unidad] ?>">
                            </td>
                            <td >Peso</td>
                            <td ><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_kgtotal2" id="ord_kgtotal2" size="10"   onchange="calculo_peso(2)" value="<?php echo $rst[ord_kgtotal2] ?>" style="text-align:right" readonly /> kg 
                                <input type="" hidden size="1" name="peso_pr2" id="peso_pr2" value="<?php echo $rst_pro2[pro_peso] ?>"></td>

                            <td>Ancho:<input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_ancho2" id="ord_ancho2" size="6" value="<?php echo $rst[ord_sec_ancho] ?>" /> m</td>
                            <td>Carriles:<input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9]/, '');" type="text" name="ord_carril2" id="ord_carril2" size="6" value="<?php echo $rst[ord_sec_carril] ?>" lang="2" onchange="calculo_rollos(this)"/><br />      </td>

                        </tr>

                        <!------------------------------------------------------------------------- PRODUCTO TERCERO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- PRODUCTO TERCERO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- PRODUCTO TERCERO   ----------------------------------------------------------------------------------->                             

                        <tr>
                            <td>PRODUCTO TERCERO:</td>
                            <!--<td><select name="ord_pro_tercero" id="ord_pro_tercero" style="width:182px" onchange="despliegue_ancho_producto_secundario(ord_pro_secundario.value)" ></select></td>-->
                            <td ><select name="ord_pro3" id="ord_pro3" style="width:200px; " onchange="buscar_producto(this)" lang="3">
                                    <option value="0"> - Elija un Producto - </option>
                                    <?php
                                    $cns_pro = $Set->lista_productosss();
                                    while ($rst_pro = pg_fetch_array($cns_pro)) {
                                        echo "<option $sel value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td align=right >#Rollos:</td>
                            <td >
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" readonly name="ord_num_rollos3" id="ord_num_rollos3" onchange="calculo_peso(1)" style="text-align:right" size="10" value="<?php echo $rst[ord_num_rollos3] ?>" />
                                <input type="hidden" size="1" name="unidad3" id="unidad3" value="<?php echo $rst[unidad] ?>">
                            </td>
                            <td >Peso</td>
                            <td ><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_kgtotal3" id="ord_kgtotal3" size="10"   onchange="calculo_peso(2)" value="<?php echo $rst[ord_kgtotal3] ?>" style="text-align:right" readonly /> kg 
                                <input type="" size="1" hidden name="peso_pr3" id="peso_pr3" value="<?php echo $rst_pro3[pro_peso] ?>"></td>
                            <td>Ancho:<input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_ancho3" id="ord_ancho3" size="6" value="<?php echo $rst[ord_ancho3] ?>" /> m</td>
                            <td>Carriles:<input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9]/, '');" type="text" name="ord_carril3" id="ord_carril3" size="6" value="<?php echo $rst[ord_carril3] ?>" lang="3" onchange="calculo_rollos(this)"/><br />      </td>

                        </tr>
                        <!------------------------------------------------------------------------- CUARTO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- CUARTO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- CUARTO   ----------------------------------------------------------------------------------->                             

                        <tr>
                            <td>PRODUCTO CUARTO:</td>
                            <!--<td><select name="ord_pro_cuarto" id="ord_pro_secundario" style="width:182px" onchange="despliegue_ancho_producto_secundario(ord_pro_secundario.value)" ></select></td>-->
                            <td ><select name="ord_pro4" id="ord_pro4" style="width:200px; " onchange="buscar_producto(this)" lang="4">
                                    <option value="0"> - Elija un Producto - </option>
                                    <?php
                                    $cns_pro = $Set->lista_productosss();
                                    while ($rst_pro = pg_fetch_array($cns_pro)) {
                                        echo "<option $sel value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td align=right >#Rollos:</td>
                            <td >
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" readonly name="ord_num_rollos4" id="ord_num_rollos4" onchange="calculo_peso(1)" style="text-align:right" size="10" value="<?php echo $rst[ord_num_rollos4] ?>" />
                                <input type="hidden" size="1" name="unidad4" id="unidad4" value="<?php echo $rst[unidad] ?>">
                            </td>
                            <td >Peso</td>
                            <td ><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_kgtotal4" id="ord_kgtotal4" size="10"   onchange="calculo_peso(2)" value="<?php echo $rst[ord_kgtotal4] ?>" style="text-align:right" readonly /> kg 
                                <input type="" size="1" hidden name="peso_pr4" id="peso_pr4" value="<?php echo $rst_pro4[pro_peso] ?>"></td>
                            <td>Ancho:<input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_ancho4" id="ord_ancho4" size="6" value="<?php echo $rst[ord_ancho4] ?>" /> m</td>
                            <td>Carriles:<input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9]/, '');" type="text" name="ord_carril4" id="ord_carril4" size="6" value="<?php echo $rst[ord_carril4] ?>" lang="4" onchange="calculo_rollos(this)"/><br /></td>

                        </tr>
                        <!------------------------------------------------------------------------- REPROCESO    ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- REPROCESO    ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- REPROCESO    ----------------------------------------------------------------------------------->                             

                        <tr>
                            <td colspan="4">Reproceso :</td>
                            <td >Peso</td>
                            <td ><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_kgtotal_rep" id="ord_kgtotal_rep" size="10"   onchange="calculo_peso(2)" value="<?php echo $rst[ord_kgtotal_rep] ?>" style="text-align:right" readonly /> kg </td> 
                            <td >Ancho:<input  readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_rep_ancho" id="ord_rep_ancho" size="6" value="<?php echo $rst[ord_rep_ancho] ?>" /> m </td>
                            <td >Carriles:<input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_rep_carril" id="ord_rep_carril" size="6" value="<?php echo $rst[ord_rep_carril] ?>" /></td>

                        </tr>

                        <tr> </tr>
                        <tr> </tr>
                        <tr>
                            <td>Largo:<input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_largo" id="ord_largo" size="10" value="<?php echo $rst[ord_largo] ?>" /> m </td>
                            <td>Gramaje:<input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_gramaje" id="ord_gramaje" size="8" value="<?php echo $rst[ord_gramaje] ?>" /> gr/m2</td>   
                            <td hidden>Peso:<input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_peso" id="ord_peso" size="8" value="<?php echo $rst_pro1[pro_peso] ?>" /> kg</td>   
                            <!------------------------------------------------------------------------- SUMA   ----------------------------------------------------------------------------------->                             
                            <!------------------------------------------------------------------------- SUMA   ----------------------------------------------------------------------------------->                             
                            <!------------------------------------------------------------------------- SUMA   ----------------------------------------------------------------------------------->                             
                            <td></td>
                            <td></td>
                            <td>Total:</td>
                            <td><input readonly style="text-align:right" size="10" id="sumakg" value="<?php echo $rst[sumakg] ?>">  kg</td>   
                            <td align="right"><input readonly style="text-align:right" size="10" id="sumam" value="<?php echo $rst[sumam] ?>">  M</td>   
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>                
                        <tr>
                            <td hidden>Faltante:<input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_pri_faltante" id="ord_pri_faltante" size="6" value="<?php echo $rst[ord_pri_faltante] ?>"/> m<br /></td>
                            <td>Ancho Total:<input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_anc_total" id="ord_anc_total" size="10" value="<?php echo $rst[ord_anc_total] ?>"onchange="calculo()"/> m </td>
                            <td>Refilado:<input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_refilado" id="ord_refilado" size="10" value="<?php echo $rst[ord_refilado] ?>"onchange="calculo()"/> m </td>
                            <td>Ancho Util:<input style="text-align:right" readonly type="text" name="ord_ancho_util" id="ord_ancho_util" size="8" value="<?php echo $rst[ord_ancho_util] ?>"/> m </td>
                        </tr>

                    </table>    
                </td>
            </tr>
            <script>
                document.getElementById("pro_id").value = '<?php echo $rst[pro_id] ?>';
                document.getElementById("ord_pro2").value = '<?php echo $rst[ord_pro_secundario] ?>';
                document.getElementById("ord_pro3").value = '<?php echo $rst[ord_pro3] ?>';
                document.getElementById("ord_pro4").value = '<?php echo $rst[ord_pro4] ?>';
            </script>

            <!------------------------------------------------------------------------- MATERIA PRIMA ----------------------------------------------------------------------------------->

            <tr>
                <td colspan="10"class="sbtitle" >FORMULACION</td>
            </tr>
            <tr>
                <td colspan="2">
                    <table>
                        <!------------------------------------------------------------------------- TORNILLO 1 ----------------------------------------------------------------------------------->
                        <tr>
                            <td>Formulacion:</td>
                            <td colspan="2"><input onchange="load_formula(this)" type="text" name="ord_formula" id="ord_formula" size="8" value="<?php echo $rst[ord_formula] ?>" list="ordenes" /> 
                            </td>
                        <tr>
                            <td>Extrusora B: </td>
                            <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_por_tornillo1" id="ord_por_tornillo1" size="4" style="text-align:right"  value="<?php echo $rst[ord_por_tornillo1] ?>" /> </td><td>%</td>
                            <td colspan="2">Batch: 
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_patch1" id="ord_patch1" size="4" style="text-align:right"  value="<?php echo $rst[ord_patch1] ?>" /> </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp1" id="ord_mp1"style="width:180px">
                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf1" id="ord_mf1" size="4" style="text-align:right" value="<?php echo $rst[ord_mf1] ?>" /></td><td>%</td>

                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch1" id="cnt_patch1" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch1 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_kg1" id="ord_kg1" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg1] ?>" readonly /></td><td>kg
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp2" id="ord_mp2" style="width:180px">
                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf2" id="ord_mf2" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf2] ?>" /></td><td> %
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch2" id="cnt_patch2" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch2 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg2" id="ord_kg2" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg2] ?>" readonly /></td><td> kg
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp3" id="ord_mp3" style="width:180px">

                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf3" id="ord_mf3" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf3] ?>" /></td><td> %
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch3" id="cnt_patch3" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch3 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg3" id="ord_kg3" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg3] ?>" readonly /></td><td> kg
                            </td>
                        </tr>
                        <tr>        
                            <td>
                                <select name="ord_mp4" id="ord_mp4" style="width:180px">
                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf4" id="ord_mf4" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf4] ?>" /> </td><td>%
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch4" id="cnt_patch4" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch4 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg4" id="ord_kg4" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg4] ?>" readonly /></td><td>kg               
                            </td>
                        </tr>
                        <tr>        
                            <td>
                                <select name="ord_mp5" id="ord_mp5" style="width:180px">
                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf5" id="ord_mf5" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf5] ?>" /></td><td> %
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch5" id="cnt_patch5" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch5 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg5" id="ord_kg5" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg5] ?>" readonly /></td><td>kg               
                            </td>
                        </tr>
                        <tr>        
                            <td>
                                <select name="ord_mp6" id="ord_mp6" style="width:180px">

                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf6" id="ord_mf6" size="4" style="text-align:right" value="<?php echo $rst[ord_mf6] ?>" /></td><td> %
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch6" id="cnt_patch6" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch6 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg6" id="ord_kg6" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg6] ?>" readonly /></td><td>kg
                            </td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td>
                                <input type="text" name="tot_por_tornillo1" id="tot_por_tornillo1" size="3" style="text-align:right" value="<?php echo $rst[tot_por_tornillo1] ?>" readonly/> </td><td>%
                            </td>
                            <td>
                                <input type="text" name="tot_batch_tornillo1" id="tot_batch_tornillo1" size="4" style="text-align:right" value="<?php echo $rst[tot_batch_tornillo1] ?>" readonly/> 
                            </td>
                            <td>
                                <input type="text" name="tot_kg_tornillo1" id="tot_kg_tornillo1" size="7" style="text-align:right" value="<?php echo $rst[tot_kg_tornillo1] ?>" readonly/></td><td>kg
                            </td>
                        </tr>
                    </table>
                </td>
                <!------------------------------------------------------------------------- TORNILLO 2 ----------------------------------------------------------------------------------->
                <td colspan="2">
                    <table>
                        <tr>
                            <td>Extrusora A: </td>
                            <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_por_tornillo1" id="ord_por_tornillo2" size="4" style="text-align:right"  value="<?php echo $rst[ord_por_tornillo2] ?>" /></td><td>%</td>
                            <td colspan="2">Batch: 
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_patch2" id="ord_patch2" size="4" style="text-align:right"  value="<?php echo $rst[ord_patch2] ?>" /> </td>

                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp7" id="ord_mp7"style="width:180px">
                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf7" id="ord_mf7" size="4" style="text-align:right" value="<?php echo $rst[ord_mf7] ?>" /> </td><td>%
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch7" id="cnt_patch7" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch7 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_kg7" id="ord_kg7" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg7] ?>" readonly /></td><td> kg
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp8" id="ord_mp8" style="width:180px">

                                </select>

                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf8" id="ord_mf8" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf8] ?>" /></td><td> %
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch8" id="cnt_patch8" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch8 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg8" id="ord_kg8" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg8] ?>" readonly /></td><td> kg
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp9" id="ord_mp9" style="width:180px">

                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf9" id="ord_mf9" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf9] ?>" /></td><td> %
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch9" id="cnt_patch9" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch9 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg9" id="ord_kg9" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg9] ?>" readonly /></td><td> kg
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp10" id="ord_mp10" style="width:180px">

                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf10" id="ord_mf10" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf10] ?>" /></td><td> %
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch10" id="cnt_patch10" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch10 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg10" id="ord_kg10" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg10] ?>" readonly /></td><td>kg
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp11" id="ord_mp11" style="width:180px">

                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf11" id="ord_mf11" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf11] ?>" /></td><td> %
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch11" id="cnt_patch11" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch11 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg11" id="ord_kg11" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg11] ?>" readonly /></td><td>kg               
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp12" id="ord_mp12" style="width:180px">

                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf12" id="ord_mf12" size="4" style="text-align:right" value="<?php echo $rst[ord_mf12] ?>" /> </td><td>%
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch12" id="cnt_patch12" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch12 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg12" id="ord_kg12" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg12] ?>" readonly /></td><td>kg
                            </td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td>
                                <input type="text" name="tot_por_tornillo2" id="tot_por_tornillo2" size="3" style="text-align:right" value="<?php echo $rst[tot_por_tornillo2] ?>" readonly/></td><td> %
                            </td>
                            <td>
                                <input type="text" name="tot_batch_tornillo2" id="tot_batch_tornillo2" size="4" style="text-align:right" value="<?php echo $rst[tot_batch_tornillo2] ?>" readonly/> 
                            </td>
                            <td>
                                <input type="text" name="tot_kg_tornillo2" id="tot_kg_tornillo2" size="7" style="text-align:right" value="<?php echo $rst[tot_kg_tornillo2] ?>" readonly/></td><td>kg
                            </td>
                        </tr>
                    </table>
                </td>
                <!------------------------------------------------------------------------- TORNILLO 3 ----------------------------------------------------------------------------------->

                <td colspan="2">
                    <table>
                        <tr>
                            <td>Extrusora C: </td>
                            <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_por_tornillo3" id="ord_por_tornillo3" size="4" style="text-align:right"  value="<?php echo $rst[ord_por_tornillo3] ?>" />  </td><td>%</td>
                            <td colspan="2">Batch: 
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_patch3" id="ord_patch3" size="4" style="text-align:right"  value="<?php echo $rst[ord_patch3] ?>" /> </td>

                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp13" id="ord_mp13"style="width:180px">
                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf13" id="ord_mf13" size="4" style="text-align:right" value="<?php echo $rst[ord_mf13] ?>" /> </td><td>%
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch13" id="cnt_patch13" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch13 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion()" type="text" name="ord_kg13" id="ord_kg13" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg13] ?>" readonly /> </td><td>kg
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp14" id="ord_mp14" style="width:180px">

                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf14" id="ord_mf14" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf14] ?>" /> </td><td>%
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch14" id="cnt_patch14" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch14 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg14" id="ord_kg14" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg14] ?>" readonly /></td><td> kg
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp15" id="ord_mp15" style="width:180px">

                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf15" id="ord_mf15" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf15] ?>" /></td><td> %
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch15" id="cnt_patch15" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch15 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg15" id="ord_kg15" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg15] ?>" readonly /></td><td> kg
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp16" id="ord_mp16" style="width:180px">

                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf16" id="ord_mf16" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf16] ?>" /></td><td> %
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch16" id="cnt_patch16" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch16 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg16" id="ord_kg16" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg16] ?>" readonly /></td><td>kg</td>
                        </tr>
                        <tr>
                            <td>              
                                <select name="ord_mp17" id="ord_mp17" style="width:180px">

                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf17" id="ord_mf17" size="4" style="text-align:right"  value="<?php echo $rst[ord_mf17] ?>" /></td><td> %
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch17" id="cnt_patch17" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch17 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg17" id="ord_kg17" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg17] ?>" readonly /></td><td>kg
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="ord_mp18" id="ord_mp18" style="width:180px">

                                </select>
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="validacion(this)" type="text" name="ord_mf18" id="ord_mf18" size="4" style="text-align:right" value="<?php echo $rst[ord_mf18] ?>" /> </td><td>%
                            </td>
                            <td>
                                <input readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="cnt_patch18" id="cnt_patch18" size="4" onchange="suma_kg()" style="text-align:right" value="<?php echo $cnt_patch18 ?>"  /> 
                            </td>
                            <td>
                                <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg18" id="ord_kg18" size="7" onchange="suma_kg()" style="text-align:right" value="<?php echo $rst[ord_kg18] ?>" readonly /></td><td>kg
                            </td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td>
                                <input type="text" name="tot_por_tornillo3" id="tot_por_tornillo3" size="3" style="text-align:right" value="<?php echo $rst[tot_por_tornillo3] ?>" readonly/> </td><td>%
                            </td>
                            <td>
                                <input type="text" name="tot_batch_tornillo3" id="tot_batch_tornillo3" size="4" style="text-align:right" value="<?php echo $rst[tot_batch_tornillo3] ?>" readonly/> 
                            </td>
                            <td>
                                <input type="text" name="tot_kg_tornillo3" id="tot_kg_tornillo3" size="7" style="text-align:right" value="<?php echo $rst[tot_kg_tornillo3] ?>" readonly/></td><td>kg
                            </td>
                        </tr>

                    </table>

                </td>

                <!--</tbody>-->

            </tr>
            <script>
                document.getElementById("ord_bodega").value = '<?php echo $rst[ord_bodega] ?>';
                document.getElementById("ord_mp1").value = '<?php echo $rst[ord_mp1] ?>';
                document.getElementById("ord_mp2").value = '<?php echo $rst[ord_mp2] ?>';
                document.getElementById("ord_mp3").value = '<?php echo $rst[ord_mp3] ?>';
                document.getElementById("ord_mp4").value = '<?php echo $rst[ord_mp4] ?>';
                document.getElementById("ord_mp5").value = '<?php echo $rst[ord_mp5] ?>';
                document.getElementById("ord_mp6").value = '<?php echo $rst[ord_mp6] ?>';
                document.getElementById("ord_mp7").value = '<?php echo $rst[ord_mp7] ?>';
                document.getElementById("ord_mp8").value = '<?php echo $rst[ord_mp8] ?>';
                document.getElementById("ord_mp9").value = '<?php echo $rst[ord_mp9] ?>';
                document.getElementById("ord_mp10").value = '<?php echo $rst[ord_mp10] ?>';
                document.getElementById("ord_mp11").value = '<?php echo $rst[ord_mp11] ?>';
                document.getElementById("ord_mp12").value = '<?php echo $rst[ord_mp12] ?>';
                document.getElementById("ord_mp13").value = '<?php echo $rst[ord_mp13] ?>';
                document.getElementById("ord_mp14").value = '<?php echo $rst[ord_mp14] ?>';
                document.getElementById("ord_mp15").value = '<?php echo $rst[ord_mp15] ?>';
                document.getElementById("ord_mp16").value = '<?php echo $rst[ord_mp16] ?>';
                document.getElementById("ord_mp17").value = '<?php echo $rst[ord_mp17] ?>';
                document.getElementById("ord_mp18").value = '<?php echo $rst[ord_mp18] ?>';</script>

            <tr>
                <td>
                    <table>
                        <tr>
                            <?php
                            $total_kg = $rst[tot_kg_tornillo1] + $rst[tot_kg_tornillo2] + $rst[tot_kg_tornillo3];
                            ?>
                            <td>Total 100%: </td>
                            <td><input  readonly type="text" size="9" name="ord_mftotal" id="ord_mftotal" style="text-align:right" value="<?php echo $rst[ord_mftotal] ?>"/></td><td> %</td> 
                            <td>Total:</td>
                            <td><input  readonly type="text" size="9" name="ord_kgtotal1" id="ord_kgtotal1" style="text-align:right" value="<?php echo str_replace(',', '', number_format($total_kg, 2)) ?>"/></td><td>KG</td> 
                        </tr>
                        <tr>
                            <td>Merma:</td>
                            <td><input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_merma" id="ord_merma" size="11" value="<?php echo $rst[ord_merma] ?>"onchange="calculo_peso(1)"/></td><td> % </td>
                            <td></td>
                            <td><input style="text-align:right" readonly onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_merma_peso" id="ord_merma_peso" size="9" value="<?php echo $rst[ord_merma_peso] ?>"/></td><td> Kg </td>
                        </tr>
                        <tr>
                            <td>Gran Total:</td>
                            <td>  <input style="text-align:right" type="text" readonly name="ord_gran_tot" id="ord_gran_tot" size="9" value="<?php echo $rst[ord_tot_fin] ?>"/> </td><td>% 
                            <td></td> 
                            <td> <input style="text-align:right" readonly  type="text" name="ord_gran_tot_peso" id="ord_gran_tot_peso" size="9" value="<?php echo $rst[ord_tot_fin_peso] ?>" /> </td><td>Kg </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!------------------------------------------------------------------------- EMPAQUES ----------------------------------------------------------------------------------->
            <!------------------------------------------------------------------------- EMPAQUES ----------------------------------------------------------------------------------->
            <!------------------------------------------------------------------------- EMPAQUES ----------------------------------------------------------------------------------->
            <tr>
                <td class="sbtitle" colspan="2"> EMPAQUE PRODUCTO PRIMARIO</td>
                <td class="sbtitle" colspan="2"> EMPAQUE PRODUCTO SECUNDARIO</td>
                <td class="sbtitle" colspan="2"> EMPAQUE PRODUCTO TERCERO</td>
                <td class="sbtitle" colspan="2"> EMPAQUE PRODUCTO CUARTO</td>
            </tr>          
            <tr>
                <td colspan="2">             
                    <table>    
                        <tr>
                            <td>EMPAQUE:</td>
                            <td>
                                <select class="sel" id="pro_mp1" onchange="load_datos_mp(this)" lang="1" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo = pg_fetch_array($cns_combomp1)) {
                                        echo "<option value='$rst_combo[mp_id]' >$rst_combo[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                                <input type="text" size="10"  hidden id="mp_cnt1"  value="<?php echo $rst['mp_cnt1'] ?>"/>
                                <input type="text" size="10"  hidden id="mp_kg1"  value="<?php echo round($rst['opp_kg1'] / $rst['mp_cnt1'], 2) ?>"/>
                                <input type="text" size="10"  hidden id="opp_kg1"  value="<?php echo $rst['opp_kg1'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td>ROLLOS POR EMPQ.:</td>
                            <td><input type="text" size="10"  id="opp_velocidad"  value="<?php echo $rst['opp_velocidad'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_empaque()"/></td>
                        </tr>
                        <tr>
                            <td>CORE:</td>
                            <td>
                                <select id="pro_mp2" class="sel" onchange="load_datos_mp(this)" lang="2" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo2 = pg_fetch_array($cns_combomp2)) {
                                        echo "<option value='$rst_combo2[mp_id]' >$rst_combo2[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                                <input type="text" size="10" hidden id="mp_cnt2"  value="<?php echo $rst['mp_cnt2'] ?>"/>
                                <input type="text" size="10" hidden id="mp_kg2"  value="<?php echo round($rst['opp_kg2'] / $rst['mp_cnt2'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg2"  value="<?php echo $rst['opp_kg2'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp3" class="sel" onchange="load_datos_mp(this)" lang="3" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo3 = pg_fetch_array($cns_combomp3)) {
                                        echo "<option value='$rst_combo3[mp_id]' >$rst_combo3[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt3"   value="<?php echo $rst['mp_cnt3'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg3"  value="<?php echo round($rst['opp_kg3'] / $rst['mp_cnt3'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg3"  value="<?php echo $rst['opp_kg3'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp4" class="sel" onchange="load_datos_mp(this)" lang="4" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo4 = pg_fetch_array($cns_combomp4)) {
                                        echo "<option value='$rst_combo4[mp_id]' >$rst_combo4[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt4"  value="<?php echo $rst['mp_cnt4'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg4"  value="<?php echo round($rst['opp_kg4'] / $rst['mp_cnt4'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg4"  value="<?php echo $rst['opp_kg4'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp5" class="sel" onchange="load_datos_mp(this)" lang="5" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo5 = pg_fetch_array($cns_combomp5)) {
                                        echo "<option value='$rst_combo5[mp_id]' >$rst_combo5[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt5"  value="<?php echo $rst['mp_cnt5'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg5"  value="<?php echo round($rst['opp_kg5'] / $rst['mp_cnt5'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg5"  value="<?php echo $rst['opp_kg5'] ?>"/>
                            </td></tr> 

                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp6" class="sel" onchange="load_datos_mp(this)" lang="6" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo6 = pg_fetch_array($cns_combomp6)) {
                                        echo "<option value='$rst_combo6[mp_id]' >$rst_combo6[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt6" value="<?php echo $rst['mp_cnt6'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg6"  value="<?php echo round($rst['opp_kg6'] / $rst['mp_cnt6'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg6" value="<?php echo $rst['opp_kg6'] ?>"/>
                            </td>                            </tr> 
                        <tr>
                            <td>PESO NETO:</td>
                            <td><input type="text" size="10"  style="text-align: right" id="pro_mf1"  readonly value="<?php echo $rst['pro_mf1'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td>PESO CORE:</td>
                            <td><input type="text" size="10"  style="text-align: right" id="pro_mf2"  readonly value="<?php echo $rst['pro_mf2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td>PESO BRUTO:</td>
                            <td><input type="text" size="10" style="text-align: right" id="pro_mf3"  readonly value="<?php echo $rst['pro_mf3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td></br></td>
                        </tr>

                        <script>
                            document.getElementById("pro_mp1").value = '<?php echo $rst[pro_mp1] ?>';
                            document.getElementById("pro_mp2").value = '<?php echo $rst[pro_mp2] ?>';
                            document.getElementById("pro_mp3").value = '<?php echo $rst[pro_mp3] ?>';
                            document.getElementById("pro_mp4").value = '<?php echo $rst[pro_mp4] ?>';
                            document.getElementById("pro_mp5").value = '<?php echo $rst[pro_mp5] ?>';
                            document.getElementById("pro_mp6").value = '<?php echo $rst[pro_mp6] ?>';</script>
                    </table>
                </td>
                <td colspan="2">    
                    <!------------------------------------------------------------------------- EMPAQUES2 ----------------------------------------------------------------------------------->
                    <table>    
                        <tr>
                            <td>EMPAQUE:</td>
                            <td>
                                <select id="pro_mp7" class="sel" onchange="load_datos_mp(this)" lang="7" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo = pg_fetch_array($cns_combomp7)) {
                                        echo "<option value='$rst_combo[mp_id]' >$rst_combo[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                                <input type="text" size="10"  hidden id="mp_cnt7"  value="<?php echo $rst['mp_cnt7'] ?>"/>
                                <input type="text" size="10"  hidden id="mp_kg7"  value="<?php echo round($rst['mp_kg7'] / $rst['mp_cnt7'], 2) ?>"/>
                                <input type="text" size="10"  hidden id="opp_kg7"  value="<?php echo $rst['opp_kg7'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td>ROLLOS POR EMPQ.:</td>
                            <td><input type="text" size="10"  id="opp_velocidad2"  value="<?php echo $rst['opp_velocidad2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_empaque()"/></td>
                        </tr>
                        <tr>
                            <td>CORE:</td>
                            <td>
                                <select id="pro_mp8" class="sel" onchange="load_datos_mp(this)" lang="8" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo8 = pg_fetch_array($cns_combomp8)) {
                                        echo "<option value='$rst_combo8[mp_id]' >$rst_combo8[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                                <input type="text" size="10" hidden id="mp_cnt8"  value="<?php echo $rst['mp_cnt8'] ?>"/>
                                <input type="text" size="10" hidden id="mp_kg8"  value="<?php echo round($rst['opp_kg8'] / $rst['mp_cnt8'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg8"  value="<?php echo $rst['opp_kg8'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp9" class="sel" onchange="load_datos_mp(this)" lang="9" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo9 = pg_fetch_array($cns_combomp9)) {
                                        echo "<option value='$rst_combo9[mp_id]' >$rst_combo9[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt9"   value="<?php echo $rst['mp_cnt9'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg9"  value="<?php echo round($rst['opp_kg9'] / $rst['mp_cnt9'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg9"  value="<?php echo $rst['opp_kg9'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp10" class="sel"  onchange="load_datos_mp(this)" lang="10" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo10 = pg_fetch_array($cns_combomp10)) {
                                        echo "<option value='$rst_combo10[mp_id]' >$rst_combo10[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt10"  value="<?php echo $rst['mp_cnt10'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg10"  value="<?php echo round($rst['opp_kg10'] / $rst['mp_cnt10'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg10"  value="<?php echo $rst['opp_kg10'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp11"class="sel"  onchange="load_datos_mp(this)" lang="11" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo11 = pg_fetch_array($cns_combomp11)) {
                                        echo "<option value='$rst_combo11[mp_id]' >$rst_combo11[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt11"  value="<?php echo $rst['mp_cnt11'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg11"  value="<?php echo round($rst['opp_kg11'] / $rst['mp_cnt11'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg11"  value="<?php echo $rst['opp_kg11'] ?>"/>
                            </td></tr> 

                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp12" class="sel" onchange="load_datos_mp(this)" lang="12" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo12 = pg_fetch_array($cns_combomp12)) {
                                        echo "<option value='$rst_combo12[mp_id]' >$rst_combo12[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt12" value="<?php echo $rst['mp_cnt12'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg12"  value="<?php echo round($rst['opp_kg12'] / $rst['mp_cnt12'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg12" value="<?php echo $rst['opp_kg12'] ?>"/>
                            </td>                            </tr> 
                        <tr>
                            <td>PESO NETO:</td>
                            <td><input type="text" size="10" style="text-align: right" id="pro_mf4"  readonly value="<?php echo $rst['pro_mf4'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td>PESO CORE:</td>
                            <td><input type="text" size="10" style="text-align: right" id="pro_mf5"  readonly value="<?php echo $rst['pro_mf5'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td>PESO BRUTO:</td>
                            <td><input type="text" size="10" style="text-align: right" id="pro_mf6"  readonly value="<?php echo $rst['pro_mf6'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td></br></td>
                        </tr>

                        <script>
                            document.getElementById("pro_mp7").value = '<?php echo $rst[pro_mp7] ?>';
                            document.getElementById("pro_mp8").value = '<?php echo $rst[pro_mp8] ?>';
                            document.getElementById("pro_mp9").value = '<?php echo $rst[pro_mp9] ?>';
                            document.getElementById("pro_mp10").value = '<?php echo $rst[pro_mp10] ?>';
                            document.getElementById("pro_mp11").value = '<?php echo $rst[pro_mp11] ?>';
                            document.getElementById("pro_mp12").value = '<?php echo $rst[pro_mp12] ?>';</script>
                    </table>
                </td>

                <td colspan="2"> 
                    <!------------------------------------------------------------------------- EMPAQUES3 ----------------------------------------------------------------------------------->
                    <table>    
                        <tr>
                            <td>EMPAQUE:</td>
                            <td>
                                <select id="pro_mp13" class="sel" onchange="load_datos_mp(this)" lang="13" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo = pg_fetch_array($cns_combomp13)) {
                                        echo "<option value='$rst_combo[mp_id]' >$rst_combo[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                                <input type="text" size="10"  hidden id="mp_cnt13"  value="<?php echo $rst['mp_cnt13'] ?>"/>
                                <input type="text" size="10"  hidden id="mp_kg13"  value="<?php echo round($rst['opp_kg13'] / $rst['mp_cnt13'], 2) ?>"/>
                                <input type="text" size="10"  hidden id="opp_kg13"  value="<?php echo $rst['opp_kg13'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td>ROLLOS POR EMPQ.:</td>
                            <td><input type="text" size="10"  id="opp_velocidad3"  value="<?php echo $rst['opp_velocidad3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_empaque()"/></td>
                        </tr>
                        <tr>
                            <td>CORE:</td>
                            <td>
                                <select id="pro_mp14" class="sel" onchange="load_datos_mp(this)" lang="14" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo14 = pg_fetch_array($cns_combomp14)) {
                                        echo "<option value='$rst_combo14[mp_id]' >$rst_combo14[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                                <input type="text" size="10" hidden id="mp_cnt14"  value="<?php echo $rst['mp_cnt14'] ?>"/>
                                <input type="text" size="10" hidden id="mp_kg14"  value="<?php echo round($rst['opp_kg14'] / $rst['mp_cnt14'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg14"  value="<?php echo $rst['opp_kg14'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp15" class="sel" onchange="load_datos_mp(this)" lang="15" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo15 = pg_fetch_array($cns_combomp15)) {
                                        echo "<option value='$rst_combo15[mp_id]' >$rst_combo15[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt15"   value="<?php echo $rst['mp_cnt15'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg15"  value="<?php echo round($rst['opp_kg15'] / $rst['mp_cnt15'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg15"  value="<?php echo $rst['opp_kg15'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp16" class="sel"  onchange="load_datos_mp(this)" lang="16" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo16 = pg_fetch_array($cns_combomp16)) {
                                        echo "<option value='$rst_combo16[mp_id]' >$rst_combo16[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt16"  value="<?php echo $rst['mp_cnt16'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg16"  value="<?php echo round($rst['opp_kg16'] / $rst['mp_cnt16'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg16"  value="<?php echo $rst['opp_kg16'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp17"class="sel"  onchange="load_datos_mp(this)" lang="17" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo17 = pg_fetch_array($cns_combomp17)) {
                                        echo "<option value='$rst_combo17[mp_id]' >$rst_combo17[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt17"  value="<?php echo $rst['mp_cnt17'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg17"  value="<?php echo round($rst['opp_kg17'] / $rst['mp_cnt17'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg17"  value="<?php echo $rst['opp_kg17'] ?>"/>
                            </td></tr> 

                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp18" class="sel" onchange="load_datos_mp(this)" lang="18" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo18 = pg_fetch_array($cns_combomp18)) {
                                        echo "<option value='$rst_combo18[mp_id]' >$rst_combo18[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt18" value="<?php echo $rst['mp_cnt18'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg18"  value="<?php echo round($rst['opp_kg18'] / $rst['mp_cnt18'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg18" value="<?php echo $rst['opp_kg18'] ?>"/>
                            </td>                            </tr> 
                        <tr>
                            <td>PESO NETO:</td>
                            <td><input type="text" size="10" style="text-align: right" id="pro_mf7"  readonly value="<?php echo $rst['pro_mf7'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td>PESO CORE:</td>
                            <td><input type="text" size="10" style="text-align: right" id="pro_mf8"  readonly value="<?php echo $rst['pro_mf8'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td>PESO BRUTO:</td>
                            <td><input type="text" size="10" style="text-align: right" id="pro_mf9"  readonly value="<?php echo $rst['pro_mf9'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td></br></td>
                        </tr>

                        <script>
                            document.getElementById("pro_mp13").value = '<?php echo $rst[pro_mp13] ?>';
                            document.getElementById("pro_mp14").value = '<?php echo $rst[pro_mp14] ?>';
                            document.getElementById("pro_mp15").value = '<?php echo $rst[pro_mp15] ?>';
                            document.getElementById("pro_mp16").value = '<?php echo $rst[pro_mp16] ?>';
                            document.getElementById("pro_mp17").value = '<?php echo $rst[pro_mp17] ?>';
                            document.getElementById("pro_mp18").value = '<?php echo $rst[pro_mp18] ?>';</script>
                    </table>
                </td>
                <td colspan="2">  
                    <!------------------------------------------------------------------------- EMPAQUES4 ----------------------------------------------------------------------------------->
                    <table>    
                        <tr>
                            <td>EMPAQUE:</td>
                            <td>
                                <select id="pro_mp19" class="sel" onchange="load_datos_mp(this)" lang="19" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo = pg_fetch_array($cns_combomp19)) {
                                        echo "<option value='$rst_combo[mp_id]' >$rst_combo[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                                <input type="text" size="10"  hidden id="mp_cnt19"  value="<?php echo $rst['mp_cnt19'] ?>"/>
                                <input type="text" size="10"  hidden id="mp_kg19"  value="<?php echo round($rst['opp_kg19'] / $rst['mp_cnt19'], 2) ?>"/>
                                <input type="text" size="10"  hidden id="opp_kg19"  value="<?php echo $rst['opp_kg19'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td>ROLLOS POR EMPQ.:</td>
                            <td><input type="text" size="10"  id="opp_velocidad4"  value="<?php echo $rst['opp_velocidad4'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_empaque()"/></td>
                        </tr>
                        <tr>
                            <td>CORE:</td>
                            <td>
                                <select id="pro_mp20" class="sel" onchange="load_datos_mp(this)" lang="20" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo20 = pg_fetch_array($cns_combomp20)) {
                                        echo "<option value='$rst_combo20[mp_id]' >$rst_combo20[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                                <input type="text" size="10" hidden id="mp_cnt20"  value="<?php echo $rst['mp_cnt20'] ?>"/>
                                <input type="text" size="10" hidden id="mp_kg20"  value="<?php echo round($rst['opp_kg20'] / $rst['mp_cnt20'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg20"  value="<?php echo $rst['opp_kg20'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp21" class="sel" onchange="load_datos_mp(this)" lang="21" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo21 = pg_fetch_array($cns_combomp21)) {
                                        echo "<option value='$rst_combo21[mp_id]' >$rst_combo21[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt21"   value="<?php echo $rst['mp_cnt21'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg21"  value="<?php echo round($rst['opp_kg21'] / $rst['mp_cnt21'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg21"  value="<?php echo $rst['opp_kg21'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp22" class="sel"  onchange="load_datos_mp(this)" lang="22" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo22 = pg_fetch_array($cns_combomp22)) {
                                        echo "<option value='$rst_combo22[mp_id]' >$rst_combo22[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt22"  value="<?php echo $rst['mp_cnt22'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg22"  value="<?php echo round($rst['opp_kg22'] / $rst['mp_cnt22'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg22"  value="<?php echo $rst['opp_kg22'] ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp23"class="sel"  onchange="load_datos_mp(this)" lang="23" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo23 = pg_fetch_array($cns_combomp23)) {
                                        echo "<option value='$rst_combo23[mp_id]' >$rst_combo23[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt23"  value="<?php echo $rst['mp_cnt23'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg23"  value="<?php echo round($rst['opp_kg23'] / $rst['mp_cnt23'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg23"  value="<?php echo $rst['opp_kg23'] ?>"/>
                            </td></tr> 

                        <tr>
                            <td></td>
                            <td>
                                <select id="pro_mp24" class="sel" onchange="load_datos_mp(this)" lang="24" >
                                    <option value="0">Seleccione</option>
                                    <?php
                                    while ($rst_combo24 = pg_fetch_array($cns_combomp24)) {
                                        echo "<option value='$rst_combo24[mp_id]' >$rst_combo24[mp_referencia]</option>";
                                    }
                                    ?>  
                                </select>
                            </td>
                            <td><input type="text" size="10"  id="mp_cnt24" value="<?php echo $rst['mp_cnt24'] ?>" onchange="load_datos_mp(this)"/>
                                <input type="text" size="10" hidden id="mp_kg24"  value="<?php echo round($rst['opp_kg24'] / $rst['mp_cnt24'], 2) ?>"/>
                                <input type="text" size="10" hidden id="opp_kg24" value="<?php echo $rst['opp_kg24'] ?>"/>
                            </td>                            </tr> 
                        <tr>
                            <td>PESO NETO:</td>
                            <td><input type="text" size="10" style="text-align: right" id="pro_mf10"  readonly value="<?php echo $rst['pro_mf10'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td>PESO CORE:</td>
                            <td><input type="text" size="10" style="text-align: right" id="pro_mf11"  readonly value="<?php echo $rst['pro_mf11'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td>PESO BRUTO:</td>
                            <td><input type="text" size="10" style="text-align: right" id="pro_mf12"  readonly value="<?php echo $rst['pro_mf12'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                        </tr>
                        <tr>
                            <td></br></td>
                        </tr>

                        <script>
                            document.getElementById("pro_mp19").value = '<?php echo $rst[pro_mp19] ?>';
                            document.getElementById("pro_mp20").value = '<?php echo $rst[pro_mp20] ?>';
                            document.getElementById("pro_mp21").value = '<?php echo $rst[pro_mp21] ?>';
                            document.getElementById("pro_mp22").value = '<?php echo $rst[pro_mp22] ?>';
                            document.getElementById("pro_mp23").value = '<?php echo $rst[pro_mp23] ?>';
                            document.getElementById("pro_mp24").value = '<?php echo $rst[pro_mp24] ?>';</script>
                    </table>
                </td>


            <tr>
                <td colspan="8">Observaciones:
                    <textarea name="ord_observaciones" id="ord_observaciones" style="width:100%"><?php echo $rst[ord_observaciones] ?></textarea>
                </td>
            </tr>

            <tr>
                <td colspan="2"><?php
                    if (($Prt->add == 0 || $Prt->edition == 0) && $x != 1) {
                        ?>
                        <button id="save" onclick="save(<?php echo $id ?>)">Guardar</button>
                    <?php }
                    ?>

                    <button id="cancel"  onclick="cancelar()">Cancelar</button></td>
            </tr>
        </table>
</html>
<datalist id="ordenes">
    <?php
    $cns_ord = $Set->lista_ordenes();
    $n = 0;
    while ($rst_ord = pg_fetch_array($cns_ord)) {
        $n++;
        ?>
        <option value="<?php echo $rst_ord[ord_num_orden] ?>" label="<?php echo $rst_ord[ord_num_orden] ?>" />
        <?php
    }
    ?>
</datalist>
<script>
    var et = '<?php echo $rst[ord_etiqueta] ?>';
    $('#ord_etiqueta').val(et);
</script>