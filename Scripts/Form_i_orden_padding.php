<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ordenes_padding.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$Clase = new Clase_Orden_Padding();
$cns_combo = $Clase->lista_combo(1);
$cns_combo1 = $Clase->lista_combopro(4);
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase->lista_uno($id));
    $cns_combomp1 = $Clase->lista_combomp($rts[emp_id]);
    $cns_combomp2 = $Clase->lista_combomp($rst[emp_id]);
    $cns_combomp3 = $Clase->lista_combomp($rst[emp_id]);
    $cns_combomp4 = $Clase->lista_combomp($rst[emp_id]);
    $rst_cli = pg_fetch_array($Set->lista_clientes_codigo($rst[cli_id]));
    $nombre = $rst_cli[cli_raz_social];
    $cli_id = $rst_cli[cli_id];
    $det = 0;
    $det_id = $rst[det_id];
} else {
    $id = 0;
    if (isset($_GET[prod])) {
        $rs_pv = pg_fetch_array($Set->lista_un_det_pedido($_GET[prod]));
        $rst[pro_id] = $rs_pv[pro_id];
        $rst['opp_cantidad'] = $rs_pv[det_cantidad];
        $rst['opp_fec_pedido'] = $rs_pv[ped_femision];
        $det = 1;
        $det_id = $_GET[prod];
    } else {
        $rst[pro_id] = '';
        $rst['opp_cantidad'] = '0';
        $det = 0;
        $rst['opp_fec_pedido'] = date('Y-m-d');
        $det_id = 0;
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
    $rst['opp_velocidad'] = 0;
    $rst['opp_temp_rodillosup'] = 0;
    $rst['opp_temp_rodilloinf'] = 0;
    $nombre = 'NOPERTI CIA LTDA';
    $cli_id = '71763';
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
                        pro_gramaje.value,
                        opp_refilado1.value,
                        opp_refilado2.value,
                        pro_mp1.value,
                        pro_mp2.value,
                        pro_mp3.value,
                        pro_mp4.value,
                        pro_mf1.value,
                        pro_mf2.value,
                        pro_mf3.value,
                        pro_mf4.value,
                        opp_kg1.value,
                        opp_kg2.value,
                        opp_kg3.value,
                        opp_kg4.value,
                        opp_velocidad.value,
                        opp_temp_rodillosup.value,
                        opp_temp_rodilloinf.value,
                        opp_observaciones.value,
                        det_id
                        );
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
                        else if (pro_gramaje.value.length == 0) {
                            $("#pro_gramaje").css({borderColor: "red"});
                            $("#pro_gramaje").focus();
                            return false;
                        } else if (opp_refilado1.value.length == 0 || parseFloat(opp_refilado1.value) == 0) {
                            $("#opp_refilado1").css({borderColor: "red"});
                            $("#opp_refilado1").focus();
                            return false;
                        }
                        else if (opp_refilado2.value.length == 0 || parseFloat(opp_refilado2.value) == 0) {
                            $("#opp_refilado2").css({borderColor: "red"});
                            $("#opp_refilado2").focus();
                            return false;
                        }
                        else if (pro_mp1.value == 0) {
                            $("#pro_mp1").css({borderColor: "red"});
                            $("#pro_mp1").focus();
                            return false;
                        }
                        else if ($("#suma").val() != 100) {
                            $("#suma").css({borderColor: "red"});
                            $("#suma").focus();
                            return false;
                        }
                        else if (opp_kg1.value.length == 0) {
                            $("#opp_kg1").css({borderColor: "red"});
                            $("#opp_kg1").focus();
                            return false;
                        }
                        else if (opp_kg2.value.length == 0) {
                            $("#opp_kg2").css({borderColor: "red"});
                            $("#opp_kg2").focus();
                            return false;
                        }
                        else if (opp_kg3.value.length == 0) {
                            $("#opp_kg3").css({borderColor: "red"});
                            $("#opp_kg3").focus();
                            return false;
                        }
                        else if (opp_kg4.value.length == 0) {
                            $("#opp_kg4").css({borderColor: "red"});
                            $("#opp_kg4").focus();
                            return false;
                        }
                        else if (opp_velocidad.value.length == 0) {
                            $("#opp_velocidad").css({borderColor: "red"});
                            $("#opp_velocidad").focus();
                            return false;
                        }
                        else if (opp_temp_rodillosup.value.length == 0) {
                            $("#opp_temp_rodillosup").css({borderColor: "red"});
                            $("#opp_temp_rodillosup").focus();
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_padding.php',
                    data: {op: 0, 'data[]': data, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
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
                if (det == 0) {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_orden_padding.php';
                } else {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_prod_pedido_venta.php';
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
                    $('#pro_gramaje').val(dat[3]);
                    $('#pro_mp1,#pro_mp2,#pro_mp3,#pro_mp4').html(dat[16]);
                    $('#pro_mf1').val(dat[4]);
                    $('#pro_mf2').val(dat[5]);
                    $('#pro_mf3').val(dat[6]);
                    $('#pro_mf4').val(dat[7]);
                    $('#pro_mp1').val(dat[8]);
                    $('#pro_mp2').val(dat[9]);
                    $('#pro_mp3').val(dat[10]);
                    $('#pro_mp4').val(dat[11]);
                    if (dat[12] == "") {
                        $('#opp_velocidad').val(0);
                    } else {
                        $('#opp_velocidad').val(dat[12]);
                    }
                    if (dat[13] == "") {
                        $('#opp_temp_rodillosup').val(0);
                    } else {
                        $('#opp_temp_rodillosup').val(dat[13]);
                    }
                    if (dat[14] == "") {
                        $('#opp_temp_rodilloinf').val(0);
                    } else {
                        $('#opp_temp_rodilloinf').val(dat[14]);
                    }
                    if (dat[15] == "") {
                        $('#opp_observaciones').val(0);
                    } else {
                        $('#opp_observaciones').val(dat[15]);
                    }

                    suma();
                })
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
            function calculo(a) {
//                peso = $("#pro_peso").val();
                peso = (parseFloat($('#pro_largo').val() * 1) * parseFloat($('#pro_ancho').val() * 1) * parseFloat($('#pro_gramaje').val() * 1) * ((parseFloat($('#opp_refilado1').val()) + parseFloat($('#opp_refilado2').val())) / 100)) / 1000;
                cant = $("#opp_cantidad").val();
                mf1 = $("#pro_mf1").val();
                mf2 = $("#pro_mf2").val();
                mf3 = $("#pro_mf3").val();
                mf4 = $("#pro_mf4").val();


                switch (a)
                {
                    case 0:
                        if (($("#pro_peso").val() != "") && ($("#opp_cantidad").val() != "")) {
                            opp_kg1.value = (((peso * cant) * mf1) / 100).toFixed(2);

                        }
                        break;
                    case 1:
                        if (($("#pro_peso").val() != "") && ($("#opp_cantidad").val() != "")) {
                            opp_kg2.value = (((peso * cant) * mf2) / 100).toFixed(2);

                        }
                        break;
                    case 2:
                        if (($("#pro_peso").val() != "") && ($("#opp_cantidad").val() != "")) {
                            opp_kg3.value = (((peso * cant) * mf3) / 100).toFixed(2);

                        }
                        break;
                    case 3:
                        if (($("#pro_peso").val() != "") && ($("#opp_cantidad").val() != "")) {
                            opp_kg4.value = (((peso * cant) * mf4) / 100).toFixed(2);

                        }
                        break;
                    case 4:

                        if (($("#pro_peso").val() != "") && ($("#opp_cantidad").val() != "")) {
                            opp_kg1.value = (((peso * cant) * mf1) / 100).toFixed(2);
                        }
                        break;
                    case 5:
                        if ($("#ord_kgtotal").val() != "" && peso != "") {
                            ord_num_rollos.value = (((ord_kgtotal.value * peso) / 100)).toFixed(2);
                            validacion();
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
        <form  autocomplete="off" id="frm_save">
            <style>
                tbody{
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
                    <tr><th colspan="7" >Orden de Produccion Padding 
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tbody>
                    <tr><td colspan="2" class="sbtitle" >DETALLE DE PRODUCTO </td>
                    </tr>           
                    <tr>
                        <td>PEDIDO:</td>
                        <td><input type="text" size="13"  id="opp_codigo" readonly value="<?php echo $rst['opp_codigo'] ?>"  /></td> 
                    </tr>
                    <tr>
                        <td>CLIENTE:</td>
                        <td>
                            <input type="hidden" id="cli_id" value="<?php echo $cli_id ?>"/>
                            <input type="text" id="nombre" list="clientes" size="50" onchange="load_cliente(this)" value="<?php echo $nombre ?>"/>
                        </td>  
                    </tr>
                    <tr>
                        <td>PRODUCTO:</td>
                        <td>
                            <select id="pro_id" onchange="load_datos(this)" >
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
                        <td><input type="text" size="15"  id="opp_cantidad"  value="<?php echo $rst['opp_cantidad'] ?>" onblur="calculo(4)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                    <tr>
                        <td>FECHA PEDIDO:</td>
                        <td><input type="text" size="15"  id="opp_fec_pedido"  value="<?php echo $rst['opp_fec_pedido'] ?>"/></td>
                    </tr>
                    <tr>
                        <td>FECHA ENTREGA:</td>
                        <td><input type="text" size="15"  id="opp_fec_entrega"  value="<?php echo $rst['opp_fec_entrega'] ?>"  /></td>
                    </tr>
                </tbody>
                <tbody>
                    <tr><td colspan="2" class="sbtitle" >DETALLE DE PRODUCTO </td>
                    </tr>
                    <tr>
                        <td>ANCHO:</td>
                        <td><input type="text" size="12"  id="pro_ancho"  value="<?php echo $rst['pro_ancho'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>m</td>
                    </tr>
                    <tr>
                        <td>LARGO:</td>
                        <td><input type="text" size="12"  id="pro_largo"  value="<?php echo $rst['pro_largo'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblmedida1">m</label></td>
                    </tr>
                    <tr>
                        <td>PESO:</td>
                        <td><input type="text" size="10"  id="pro_peso"  readonly value="<?php echo $rst['pro_peso'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg</td>
                    </tr>
                    <tr>
                        <td>GRAMAJE:</td>
                        <td><input type="text" size="12"  id="pro_gramaje"  value="<?php echo $rst['pro_gramaje'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />gr/m2</td>
                    </tr>
                    <tr>
                        <td>REFILADO 1:</td>
                        <td><input type="text" size="12"  id="opp_refilado1"  value="<?php echo $rst['opp_refilado1'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo(4)" />cm</td>
                    </tr>
                    <tr>
                        <td>REFILADO 2:</td>
                        <td><input type="text" size="12"  id="opp_refilado2"  value="<?php echo $rst['opp_refilado2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo(4)"/>cm</td>
                    </tr>
                </tbody> 
                <tbody> 
                <td colspan="3" class="sbtitle" >MIX DE FIBRA</td>
                <tr>
                    <td><select id="pro_mp1">
                            <option value="0">Seleccione</option>
                            <?php
                            while ($rst_combomp = pg_fetch_array($cns_combomp1)) {
                                echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                            }
                            ?>  
                        </select>
                    </td>    
                    <td><input type="text" size="12"  id="pro_mf1"  value="<?php echo $rst['pro_mf1'] ?>" onblur="calculo(0)"   onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />%</td>
                    <td><input type="text" size="12"  id="opp_kg1"  value="<?php echo $rst['opp_kg1'] ?>"   onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />Kg</td>
                </tr>
                <tr>
                    <td><select id="pro_mp2">
                            <option value="0">Seleccione</option>
                            <?php
                            while ($rst_combomp = pg_fetch_array($cns_combomp2)) {
                                echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                            }
                            ?>  
                        </select>
                    </td>
                    <td><input type="text" size="12"  id="pro_mf2"  value="<?php echo $rst['pro_mf2'] ?>" onblur="calculo(1)"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje2">%</label></td>
                    <td><input type="text" size="12"  id="opp_kg2"  value="<?php echo $rst['opp_kg2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblkg2">Kg</label></td>
                </tr>
                <tr>
                    <td><select id="pro_mp3">
                            <option value="0">Seleccione</option>
                            <?php
                            while ($rst_combomp = pg_fetch_array($cns_combomp3)) {
                                echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                            }
                            ?>  
                        </select>
                    </td>
                    <td><input type="text" size="12"  id="pro_mf3"  value="<?php echo $rst['pro_mf3'] ?>" onblur="calculo(2)"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje3">%</label></td>
                    <td><input type="text" size="12"  id="opp_kg3"  value="<?php echo $rst['opp_kg3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblkg3">Kg</label></td>
                </tr>
                <tr>
                    <td><select id="pro_mp4">
                            <option value="0">Seleccione</option>
                            <?php
                            while ($rst_combomp = pg_fetch_array($cns_combomp4)) {
                                echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                            }
                            ?>  
                        </select>
                    </td>
                    <td><input type="text" size="12"  id="pro_mf4"  value="<?php echo $rst['pro_mf4'] ?>" onblur="calculo(3)"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje4">%</label></td>
                    <td><input type="text" size="12"  id="opp_kg4"  value="<?php echo $rst['opp_kg4'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblkg4">Kg</label></td>
                <tr>
                    <td></td>
                    <td><input type="text" size="11"  id="suma" readonly value="<?php echo $rst['suma'] ?>"/>%</td>
                </tr>
                </tr>
                </tbody>
                <thead>
                    <tr>
                        <td colspan="7" class="sbtitle" > Set Maquinas </td>
                    </tr>
                </thead>
                <tr>
                    <td>VELOCIDAD:</td>
                    <td><input type="text" size="12"  id="opp_velocidad"  value="<?php echo $rst['opp_velocidad'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                </tr>
                <tr>
                    <td>TEMP.RODILLO SUP.:</td>
                    <td><input type="text" size="12"  id="opp_temp_rodillosup"  value="<?php echo $rst['opp_temp_rodillosup'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                </tr>
                <tr>
                    <td>TEMP.RODILLO INF.:</td>
                    <td><input type="text" size="12"  id="opp_temp_rodilloinf"  value="<?php echo $rst['opp_temp_rodilloinf'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                </tr>
                <tr>
                    <td>OBSERVACIONES:</td>
                    <td><input type="text" size="110"  id="opp_observaciones"  value="<?php echo $rst['opp_observaciones'] ?>"/></td>
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
<script>
//    var pro =<?php echo $rst[pro_id] ?>;
//    alert(pro);
    var cli =<?php echo $rst[cli_id] ?>;

    var mp1 = '<?php echo $rst[pro_mp1] ?>';
    var mp2 = '<?php echo $rst[pro_mp2] ?>';
    var mp3 = '<?php echo $rst[pro_mp3] ?>';
    var mp4 = '<?php echo $rst[pro_mp4] ?>';
    var mf1 = '<?php echo $rst[pro_mf1] ?>';
    var mf2 = '<?php echo $rst[pro_mf2] ?>';
    var mf3 = '<?php echo $rst[pro_mf3] ?>';
    var mf4 = '<?php echo $rst[pro_mf4] ?>';
    var kg1 = '<?php echo $rst[opp_kg1] ?>';
    var kg2 = '<?php echo $rst[opp_kg2] ?>';
    var kg3 = '<?php echo $rst[opp_kg3] ?>';
    var kg4 = '<?php echo $rst[opp_kg4] ?>';

    $('#cli_id').val(cli);
    $('#pro_mp1').val(mp1);
    $('#pro_mp2').val(mp2);
    $('#pro_mp3').val(mp3);
    $('#pro_mp4').val(mp4);
    var s = parseFloat(mf1) + parseFloat(mf2) + parseFloat(mf3) + parseFloat(mf4);
    $('#suma').val(s.toFixed(2));

    if (mf1 == 100.00) {
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
    }
    if (mf2 >= 0.00) {
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
    }
    if (mf3 > 0.00) {
        $('#pro_mp3').show();
        $('#pro_mf3').show();
        $('#opp_kg3').show();
        $('#lblporcentaje3').show();
        $('#lblkg3').show();
        $('#pro_mp4').hide();
        $('#pro_mf4').hide();
        $('#opp_kg4').hide();
        $('#lblporcentaje4').hide();
        $('#lblkg4').hide();
    }
    if (mf4 > 0.00) {
        $('#pro_mp4').show();
        $('#pro_mf4').show();
        $('#opp_kg4').show();
        $('#lblporcentaje4').show();
        $('#lblkg4').show();
    }
</script>