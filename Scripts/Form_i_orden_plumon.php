<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$id = $_GET [id];
if (isset($_GET [id])) {
    $rst = pg_fetch_array($Set->lista_una_orden_produccion_plumon($id));
    $rst_cli = pg_fetch_array($Set->lista_clientes_codigo($rst[cli_id]));
    $nombre = $rst_cli[cli_raz_social];
    $cli_id = $rst_cli[cli_id];
    $det_id = $rst[det_id];
} else {
    $cns = $Set->lista_orden_produccion_plumon();
    $emp_id = $_GET [emp_id];
    $rst_fbc = pg_fetch_array($Set->lista_una_fabrica($emp_id));
    if (isset($_GET[prod])) {
        $rs_pv = pg_fetch_array($Set->lista_un_det_pedido($_GET[prod]));
        $rst[pro_id] = $rs_pv[pro_id];
        $rst['orp_cantidad'] = $rs_pv[det_cantidad];
        $rst[orp_fec_pedido] = $rs_pv[ped_femision];
        $det = 1;
        $det_id = $_GET[prod];
    } else {
        $rst[pro_id] = '';
        $rst['orp_cantidad'] = '0';
        $det = 0;
        $rst[orp_fec_pedido] = date("Y-m-d");
        $det_id = 0;
        $rst['orp_cantidad'] = 0;
    }

//    $primero = $rst_fbc[emp_sigla];
    $rst[orp_num_pedido] = $primero;
    $rst[orp_pro_peso] = 0;
    $rst[orp_capa] = 0;
    $rst[orp_espesor] = 0;
    $rst[orp_med_vueltas] = 0;
    $rst[orp_paquetes] = 0;
    $rst[orp_kg1] = 0;
    $rst[orp_kg2] = 0;
    $rst[orp_kg3] = 0;
    $rst[orp_kg4] = 0;
    $rst[orp_mf1] = 0;
    $rst[orp_mf2] = 0;
    $rst[orp_mf3] = 0;
    $rst[orp_mf4] = 0;
    $rst[orp_refilado] = 0;

    $rst[orp_fec_entrega] = date("Y-m-d");
    $rst[orp_mftotal] = '0';
    $rst_sec = pg_fetch_array($Set->lista_secuencial_orden_produccion_plumon());
    if (!empty($rst_sec)) {
        $cod = explode('-', $rst_sec[orp_num_pedido]);
        $sec = ($cod[1] + 1);
        $cod = $cod[0];
    } else {
        $cod = 'PL';
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
    $rst[orp_num_pedido] = $cod . '-' . $tx_trs . $sec;

    $nombre = 'NOPERTI CIA LTDA';
    $cli_id = '71763';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            var det_id =<?php echo $det_id ?>;
            $(function () {
                Calendar.setup({inputField: "orp_fec_pedido", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "orp_fec_entrega", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                producto(<?php echo $rst[pro_id] ?>);
                posicion_aux_window();
            });
            function limpiar_datos_detalle()
            {
//                orp_num_pedido.value = '<?php echo $primero ?>';
                orp_pro_ancho.value = 0;
                orp_kg1.value = 0;
                orp_kg2.value = 0;
                orp_kg3.value = 0;
                orp_kg4.value = 0;
                orp_mf1.value = 0;
                orp_mf2.value = 0;
                orp_mf3.value = 0;
                orp_mf4.value = 0;
                orp_mftotal.value = 0;
                orp_kgtotal.value = 0;
                orp_pro_largo.value = 0;
                orp_pro_peso.value = 0;
                orp_pro_gramaje.value = 0;
                orp_capa.value = 0;
                orp_espesor.value = 0;
                orp_med_vueltas.value = 0;
                orp_paquetes.value = 0;
                orp_refilado.value = 0;
            }


            function limpiar()
            {
                pro_id.style.borderColor = "";
                cli_id.style.borderColor = "";
                orp_cantidad.style.borderColor = "";
                orp_fec_pedido.style.borderColor = "";
                orp_fec_entrega.style.borderColor = "";
                orp_mftotal.style.borderColor = "";
            }

            function validacion() {


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
                } else if (orp_cantidad.value.length == 0) {
                    alert('La Cantidad es un campo obligatorio.');
                    orp_cantidad.focus();
                    limpiar();
                    orp_cantidad.style.borderColor = "red";
                } else if (orp_fec_pedido.value.length == 0) {
                    alert('La Fecha de Pedido es un campo obligatorio.');
                    orp_fec_pedido.focus();
                    limpiar();
                    orp_fec_pedido.style.borderColor = "red";
                } else if (orp_fec_entrega.value.length == 0) {
                    alert('La Fecha de Entrega es un campo obligatorio.');
                    orp_fec_entrega.focus();
                    limpiar();
                    orp_fec_entrega.style.borderColor = "red";
                } else if (orp_fec_entrega.value < orp_fec_pedido.value) {
                    alert('El Fecha de Entrega no puede ser antes de la Fecha de Pedido.');
                    orp_fec_entrega.focus();
                    limpiar();
                    orp_fec_entrega.style.borderColor = "red";
                } else if (orp_mftotal.value == 0.0) {
                    alert('El valor total del Mix de Fibra no puede ser [ 0.0 ]');
                    limpiar();
                    orp_mftotal.focus();
                } else if (orp_mftotal.value > 100) {
                    alert('El valor total del Mix de Fibra no puede ser mayor a [ 100 ]%');
                    limpiar();
                    orp_mftotal.focus();
                    orp_mftotal.style.borderColor = "red";
                } else if (orp_mftotal.value < 100) {
                    alert('El valor total del Mix de Fibra no puede ser menor a [ 100 ]%');
                    limpiar();
                    orp_mftotal.focus();
                    orp_mftotal.style.borderColor = "red";
                } else if (orp_refilado.value.length == 0 || parseFloat(orp_refilado.value) == 0) {
                    alert('El valor de refilado es un campo obligatorio');
                    limpiar();
                    orp_refilado.focus();
                } else if (orp_pro_peso.value.length == 0 || parseFloat(orp_pro_peso.value) == 0) {
                    alert('El valor del Peso no puede ser [ 0 ]');
                    limpiar();
                    orp_pro_peso.focus();
                } else {
                    var data = Array(
                            orp_num_pedido.value.toUpperCase(),
                            cli_id.value,
                            pro_id.value,
                            orp_pro_ancho.value,
                            orp_pro_largo.value,
                            orp_pro_peso.value,
                            orp_pro_gramaje.value,
                            orp_cantidad.value,
                            orp_mp1.value,
                            orp_mp2.value,
                            orp_mp3.value,
                            orp_mp4.value,
                            orp_mf1.value,
                            orp_mf2.value,
                            orp_mf3.value,
                            orp_mf4.value,
                            orp_mftotal.value,
                            orp_kg1.value,
                            orp_kg2.value,
                            orp_kg3.value,
                            orp_kg4.value,
                            orp_kgtotal.value,
                            orp_fec_pedido.value,
                            orp_fec_entrega.value,
                            orp_capa.value,
                            orp_espesor.value.toUpperCase(),
                            orp_med_vueltas.value,
                            orp_paquetes.value.toUpperCase(),
                            orp_temperatura.value,
                            orp_agua.value,
                            orp_resina.value,
                            orp_observaciones.value.toUpperCase(),
                            orp_refilado.value,
                            det_id);
                    var fields = Array();
                    $("#tbl_form").find(':input').each(function () {
                        var elemento = this;
                        des = elemento.id + "=" + elemento.value;
                        fields.push(des);
                    });
                    $.post("actions.php", {act: 57, 'data[]': data, id: id, 'fields[]': fields},
                    function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            cancelar();
                        } else {
                            loading('hidden');
                            alert(dt);
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
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_orden_plumon.php';
                } else {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_prod_pedido_venta.php';
                }
            }
            function calculo_porcentage() {
                orp_mftotal.value = (orp_mf1.value * 1 + orp_mf2.value * 1 + orp_mf3.value * 1 + orp_mf4.value * 1);
            }
            function calculo_kg() {
                orp_kgtotal.value = (orp_kg1.value * 1 + orp_kg2.value * 1 + orp_kg3.value * 1 + orp_kg4.value * 1);
            }

//            function calculo_peso() {
//                orp_pro_peso.value = ((orp_pro_ancho.value * 1 * orp_pro_largo.value * 1 * orp_pro_gramaje.value * 1 * ((orp_refilado.value * 2) / 100)) / 1000).toFixed(2);
//                calculo(4);
//            }
            function producto(id) {
                if (pro_id.value == 0) {
                    limpiar_datos_detalle();
                } else {
                    pro_id.style.borderColor = "";
                    $.post("actions.php", {act: 59, id: id},
                    function (dt) {
                        dat = dt.split('&');
                        var a = '<?php echo $id ?>';
                        if (a.length == 0) {
                            document.getElementById("pro_id").disabled = false;
                            document.getElementById("cli_id").disabled = false;
                            document.getElementById("orp_fec_pedido").disabled = false;
                            $('#orp_mp1,#orp_mp2,#orp_mp3,#orp_mp4').html(dat[17]);
                            if (dat[0] == "") {
                                orp_pro_ancho.value = 0;
                            } else {
                                orp_pro_ancho.value = dat[0];
                            }
                            orp_mp1.value = dat[1];
                            orp_mp2.value = dat[2];
                            orp_mp3.value = dat[3];
                            orp_mp4.value = dat[4];
                            if (dat[5] == "") {
                                orp_mf1.value = 0;
                            } else {
                                orp_mf1.value = dat[5];
                            }
                            if (dat[6] == "") {
                                orp_mf2.value = 0;
                            } else {
                                orp_mf2.value = dat[2];
                            }
                            if (dat[7] == "") {
                                orp_mf3.value = 0;
                            } else {
                                orp_mf3.value = dat[7];
                            }
                            if (dat[8] == "") {
                                orp_mf4.value = 0;
                            } else {
                                orp_mf4.value = dat[8];
                            }
                            if (dat[9] == "") {
                                orp_mftotal.value = 0;
                            } else {
                                orp_mftotal.value = dat[9];
                            }
                            if (dat[10] == "") {
                                orp_pro_largo.value = 0;
                            } else {
                                orp_pro_largo.value = dat[10];
                            }
                            if (dat[11] == "") {
                                orp_pro_peso.value = 0;
                            } else {
                                orp_pro_peso.value = (dat[11]);
                            }
                            if (dat[12] == "") {
                                orp_pro_gramaje.value = 0;
                            } else {
                                orp_pro_gramaje.value = dat[12];
                            }

//                            orp_num_pedido.value = '<?php echo $primero ?>' + dat[13] + '-' + '<?php echo $no_orden ?>';

                            if (dat[14] == "") {
                                $('#orp_temperatura').val(0);
                            } else {
                                $('#orp_temperatura').val(dat[14]);
                            }
                            if (dat[15] == "") {
                                $('#orp_agua').val(0);
                            } else {
                                $('#orp_agua').val(dat[15]);
                            }
                            if (dat[16] == "") {
                                $('#orp_resina').val(0);
                            } else {
                                $('#orp_resina').val(dat[16]);
                            }
                            calculo(4);
                        } else {
                            document.getElementById("pro_id").disabled = true;
                            document.getElementById("cli_id").disabled = true;
                            document.getElementById("orp_fec_pedido").disabled = true;
                            orp_pro_ancho.value = dat[0];
                            $('#orp_mp1,#orp_mp2,#orp_mp3,#orp_mp4').html(dat[17]);
                            $('#orp_mp1').val(<?php echo $rst[orp_mp1] ?>);
                            $('#orp_mp2').val(<?php echo $rst[orp_mp2] ?>);
                            $('#orp_mp3').val(<?php echo $rst[orp_mp3] ?>);
                            $('#orp_mp4').val(<?php echo $rst[orp_mp4] ?>);
                        }
                    }
                    );
                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
            function calculo(a) {
                peso = ((orp_pro_ancho.value * 1 * orp_pro_largo.value * 1 * orp_pro_gramaje.value * 1 * ((orp_refilado.value * 2) / 100)) / 1000).toFixed(2);
                cant = $("#orp_cantidad").val();
                total = $("#orp_kgtotal").val();
                mf1 = $("#orp_mf1").val();
                mf2 = $("#orp_mf2").val();
                mf3 = $("#orp_mf3").val();
                mf4 = $("#orp_mf4").val();

                switch (a)
                {
                    case 0:
                        if (($("#orp_pro_peso").val() != "") && ($("#orp_cantidad").val() != "")) {
                            orp_kg1.value = (((peso * cant) * mf1) / 100).toFixed(2);
                            orp_kg2.value = (((peso * cant) * mf2) / 100).toFixed(2);
                            orp_kg3.value = (((peso * cant) * mf3) / 100).toFixed(2);
                            orp_kg4.value = (((peso * cant) * mf4) / 100).toFixed(2);
                        }
                        break;
                    case 1:
                        if (($("#orp_pro_peso").val() != "") && ($("#orp_cantidad").val() != "")) {
                            orp_kg2.value = (((peso * cant) * mf2) / 100).toFixed(2);
                        }
                        break;
                    case 2:
                        if (($("#orp_pro_peso").val() != "") && ($("#orp_cantidad").val() != "")) {
                            orp_kg3.value = (((peso * cant) * mf3) / 100).toFixed(2);
                        }
                        break;
                    case 3:
                        if ($("#orp_kgtotal").val() != "") {
                            orp_kg4.value = (((peso * cant) * mf4) / 100).toFixed(2);
                        }
                        break;
                    case 4:
                        if (($("#orp_pro_peso").val() != "") && ($("#orp_cantidad").val() != "")) {
                            orp_kgtotal.value = (peso * cant).toFixed(2);
                            orp_kg1.value = (((peso * cant) * mf1) / 100).toFixed(2);
                            orp_kg2.value = (((peso * cant) * mf2) / 100).toFixed(2);
                            orp_kg3.value = (((peso * cant) * mf3) / 100).toFixed(2);
                            orp_kg4.value = (((peso * cant) * mf4) / 100).toFixed(2);
                        }
                        break;
                    case 5:
                        if (($("#orp_kgtotal").val() != "" && parseFloat($("#orp_kgtotal").val()) != 0) && ($("#orp_pro_peso").val() != "" && parseFloat($("#orp_pro_peso").val()) != 0)) {
                            orp_cantidad.value = (total / peso).toFixed(2);
                            orp_kg1.value = ((total * mf1) / 100).toFixed(2);
                            orp_kg2.value = ((total * mf2) / 100).toFixed(2);
                            orp_kg3.value = ((total * mf3) / 100).toFixed(2);
                            orp_kg4.value = ((total * mf4) / 100).toFixed(2);
                        }
                        break;
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
        <table id="tbl_form" border='1' >
            <thead>
                <tr>
                    <th colspan="8" >Orden de Producción
                        <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th>
                </tr>
            </thead>
            <tr> 
                <td class="sbtitle" >DATOS GENERALES</td>
                <td class="sbtitle" ></td>
                <td class="sbtitle" >DETALLE PRODUCTOS </td>
                <td class="sbtitle" ></td>
                <td class="sbtitle" >MATERIAS PRIMAS </td>          
            </tr>
            <tr>
                <td>Pedido :</td>
                <td><input readonly type="text" name="orp_num_pedido" id="orp_num_pedido" size="25" value="<?php echo $rst[orp_num_pedido] ?>" /></td>
                <td>Ancho :</td>
                <td><input readonly style="text-align:right" name="orp_pro_ancho" id="orp_pro_ancho" size="10" value="<?php echo $rst[orp_pro_ancho] ?>" />  m </td>
                <td rowspan="4">
                    <select name="orp_mp1" id="orp_mp1"style="width:180px">
                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="orp_mf1" id="orp_mf1" onblur="calculo(0)" size="10" style="text-align:right" value="<?php echo $rst[orp_mf1] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="orp_kg1" id="orp_kg1" size="10" style="text-align:right" value="<?php echo $rst[orp_kg1] ?>" /> kg<br />
                    <select name="orp_mp2" id="orp_mp2" style="width:180px">
                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="orp_mf2" id="orp_mf2" onblur="calculo(1)" size="10" style="text-align:right" value="<?php echo $rst[orp_mf2] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="orp_kg2" id="orp_kg2" size="10" style="text-align:right" value="<?php echo $rst[orp_kg2] ?>" /> kg<br />
                    <select name="orp_mp3" id="orp_mp3" style="width:180px">
                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="orp_mf3" id="orp_mf3" onblur="calculo(2)" size="10" style="text-align:right" value="<?php echo $rst[orp_mf3] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="orp_kg3" id="orp_kg3" size="10" style="text-align:right" value="<?php echo $rst[orp_kg3] ?>" /> kg<br />
                    <select name="orp_mp4" id="orp_mp4" style="width:180px">
                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="orp_mf4" id="orp_mf4" onblur="calculo(3)" size="10" style="text-align:right" value="<?php echo $rst[orp_mf4] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="orp_kg4" id="orp_kg4" size="10" style="text-align:right" value="<?php echo $rst[orp_kg4] ?>" />kg<br />               
            </tr>
            <script>
                document.getElementById("orp_mp1").value = '<?php echo $rst[orp_mp1] ?>';
                document.getElementById("orp_mp2").value = '<?php echo $rst[orp_mp2] ?>';
                document.getElementById("orp_mp3").value = '<?php echo $rst[orp_mp3] ?>';
                document.getElementById("orp_mp4").value = '<?php echo $rst[orp_mp4] ?>';</script>
            <tr>
                <td>Cliente :</td>
                <td>
                    <input type="hidden" id="cli_id" value="<?php echo $cli_id ?>"/>
                    <input type="text" id="nombre" list="clientes" size="50" onchange="load_cliente(this)" value="<?php echo $nombre ?>"/>
                </td>  
                <td>Largo:</td>
                <td><input readonly style="text-align:right" type="text" name="orp_pro_largo" id="orp_pro_largo" size="10" value="<?php echo $rst[orp_pro_largo] ?>" /> m </td>
            </tr>
            <tr>
                <td>Producto :</td>
                <td><select name="pro_id" id="pro_id" onchange="producto(pro_id.value)" >
                        <option value="0"> - Elija un Producto - </option>
                        <?php
                        $cns_pro = $Set->lista_producto_plumon();
                        while ($rst_pro = pg_fetch_array($cns_pro)) {
                            echo "<option $sel value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
                        }
                        ?>
                    </select></td>
                <td>Peso:</td>
                <td> <input readonly style="text-align:right" type="text" name="orp_pro_peso" id="orp_pro_peso"  size="10" value="<?php echo $rst[orp_pro_peso] ?>" /> kg </td>
            </tr>
            <script>
                document.getElementById("pro_id").value = '<?php echo $rst[pro_id] ?>';</script>
            <tr>
                <td>Cantidad:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onblur="calculo(4)" onchange="calculo_peso()" type="text" name="orp_cantidad" id="orp_cantidad" style="text-align:right" size="10" value="<?php echo $rst[orp_cantidad] ?>" /></td>
                <td>Gramaje :</td>
                <td><input readonly style="text-align:right" type="text" name="orp_pro_gramaje" id="orp_pro_gramaje" size="10" value="<?php echo $rst[orp_pro_gramaje] ?>" /> gr / m² </td>
            </tr>
            <tr>
                <td>Fecha Pedido:</td>
                <td><input type="text" name="orp_fec_pedido" id="orp_fec_pedido" size="9" style="text-align:right" value="<?php echo $rst[orp_fec_pedido] ?>"/>
                    <img src="../img/calendar.png" width="16"  id="im-desde" /></td>
                <td>Refilado:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"  style="text-align:right" type="text" name="orp_refilado" id="orp_refilado" size="12" value="<?php echo $rst[orp_refilado] ?>" onchange="calculo(4)"/>cm</td>

                <td>Total 100%:  <input  readonly type="text" size="5" name="orp_mftotal" id="orp_mftotal" style="text-align:right" value="<?php echo $rst[orp_mftotal] ?>"/> %   Total : <input  type="text" size="5" name="orp_kgtotal" id="orp_kgtotal" onblur="calculo(5)" style="text-align:right" value="<?php echo $rst[orp_kgtotal] ?>"/> kg</td>               
            </tr>
            <tr>
                <td>Fecha Entrega:</td>
                <td><input type="text" name="orp_fec_entrega" id="orp_fec_entrega" size="9" style="text-align:right" value="<?php echo $rst[orp_fec_entrega] ?>"/>
                    <img src="../img/calendar.png" width="16"  id="im-hasta" /></td>
                <td>Capa:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"  style="text-align:right" type="text" name="orp_capa" id="orp_capa" size="12" value="<?php echo $rst[orp_capa] ?>" /></td>

            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Espesor:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" style="text-align:right" type="text" name="orp_espesor" id="orp_espesor" size="12" value="<?php echo $rst[orp_espesor] ?>" /> m /cm </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Medidor de Vueltas:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" style="text-align:right" type="text" name="orp_med_vueltas" id="orp_med_vueltas" size="12" value="<?php echo $rst[orp_med_vueltas] ?>" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Paquetes:</td>
                <td><input style="text-align:right" type="text" name="orp_paquetes" id="orp_paquetes" size="12" value="<?php echo $rst[orp_paquetes] ?>" /></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" class="sbtitle" > Condición de Maquina </td>
            </tr>
            <tr>
                <td>Temperatura: </td><td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"  type="text" name="orp_temperatura" id="orp_temperatura" size="10" value="<?php echo $rst[orp_temperatura] ?>" /> °C </td>
                <td>Agua: <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="orp_agua" id="orp_agua" size="10" value="<?php echo $rst[orp_agua] ?>" />  Lt</td>
                <td>Resina: <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="orp_resina" id="orp_resina" size="10" value="<?php echo $rst[orp_resina] ?>" />  Lt</td>
            </tr>
            <tr>
                <td>Observaciones:</td>
                <td colspan="4" ><textarea name="orp_observaciones" id="orp_observaciones" style="width:100%"><?php echo $rst[orp_observaciones] ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="6"><?php
                    if ($Prt->add == 0 || $Prt->edition == 0) {
                        ?>
                        <button id="save" onclick="save(<?php echo $id ?>)">Guardar</button>
                    <?php }
                    ?>
                    <button id="cancel"  onclick="cancelar()">Cancelar</button>
                </td>
            </tr>
        </table>
</html>