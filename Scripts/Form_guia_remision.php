<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_guia_remision.php';

if ($emisor >= 10) {
    $ems = '0' . $emisor;
} else {
    $ems = '00' . $emisor;
}
$Clase_guia_remision = new Clase_guia_remision();
if (isset($_GET[x])) {
    $id = $_GET[x];
    $x = $_GET[id];
    $rst_fac = pg_fetch_array($Clase_guia_remision->lista_una_factura($x));
    $rst = pg_fetch_array($Clase_guia_remision->lista_una_guia_id($id));
    $cns = $Clase_guia_remision->lista_detalle_factura($x);
    $vnd_id = $rst[vnd_id];
    $rst[gui_tipo] = 'FACTURA';
    $rst[fac_fecha_emision] = $rst_fac[fac_fecha_emision];
} else {
    $id = 0;
    $x = $_GET[id];
    $rst = pg_fetch_array($Clase_guia_remision->lista_una_factura($x));
    $rst_sec = pg_fetch_array($Clase_guia_remision->lista_secuencial_documento($emisor));
    if (empty($rst_sec)) {
        $sec = 1;
    } else {
        $dat = explode('-', $rst_sec[gui_numero]);
        $sec = $dat[2] + 1;
    }
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
    $rst[gui_numero] = $ems . '-001-' . $tx . $sec;
    $cns = $Clase_guia_remision->lista_detalle_factura($x);
    $rst[gui_num_comprobante] = $rst[fac_numero];
    $rst[gui_identificacion] = $rst[fac_identificacion];
    $rst[gui_nombre] = $rst[fac_nombre];
    $rst[gui_tipo] = 'FACTURA';
    $rst[gui_fecha_emision] = date('Y-m-d');
    $rst[gui_fecha_inicio] = date('Y-m-d');
    $rst[gui_fecha_fin] = date('Y-m-d');
    $rst[id] = '0';
    $rst_ven = pg_fetch_array($Clase_guia_remision->lista_vendedor(strtoupper($rst_user[usu_person])));
    $vnd_id = $rst_ven[vnd_id];
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
            var id = '<?php echo $id ?>';
            var comp = '<?php echo $rst[gui_numero] ?>';
            var x = '<?php echo $x ?>';
            var usu = '<?php echo $emisor ?>';
            var vnd = '<?php echo $vnd_id ?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    save(id);
                });
                $('#con_transportistas').hide();
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha_inicio_transporte", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                Calendar.setup({inputField: "fecha_fin_transporte", ifFormat: "%Y-%m-%d", button: "im-campo3"});
                posicion_aux_window();
            });

            function save(id) {
                fecha_comp = '<?php echo $rst[fac_fecha_emision] ?>';
                autorizacion = '<?php echo $rst[fac_autorizacion] ?>';
                var data = Array(
                        vnd,
                        usu,
                        cli_id.value,
                        num_comprobante.value,
                        fecha_emision.value,
                        fecha_inicio_transporte.value,
                        fecha_fin_transporte.value,
                        motivo_traslado.value,
                        punto_partida.value,
                        destino.value,
                        identificacion_destinario.value,
                        nombre_destinatario.value,
                        identificacion_trasportista.value,
                        documento_aduanero.value,
                        cod_establecimiento_destino.value,
                        num_comprobante_venta.value,
                        observacion.value,
                        fac_id.value,
                        tra_id.value, //tra_id,
                        '1',
                        autorizacion,
                        fecha_comp,
                        nombre_trasportista.value,
                        placa.value
                        );

                n = 0;
                doc = document.getElementsByClassName('itm');
                var data2 = Array();
                while (n < doc.length) {
                    n++;
                    if ($('#item' + n).val() != null) {
                        cantidad = $('#cantidad' + n).val();
                        descripcion = $('#descripcion' + n).val();
                        cod_producto = $('#cod_producto' + n).val();
                        pro_id = $('#pro_id' + n).val();
                        lote = $('#lote' + n).val();
                        tab = $('#tabla' + n).val();
                        data2.push(
                                cantidad + '&' +
                                cod_producto + '&' +
                                '' + '&' +
                                descripcion + '&' +
                                pro_id + '&' +
                                lote + '&' +
                                tab
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
                        //Validaciones antes de enviar
                        doc = document.getElementsByClassName('itm');
                        n = 0;
//                     
                        if (motivo_traslado.value.length == 0) {
                            $("#motivo_traslado").css({borderColor: "red"});
                            $("#motivo_traslado").focus();
                            return false;
                        }
                        else if (punto_partida.value.length == 0) {
                            $("#punto_partida").css({borderColor: "red"});
                            $("#punto_partida").focus();
                            return false;
                        }
                        else if (destino.value.length == 0) {
                            $("#destino").css({borderColor: "red"});
                            $("#destino").focus();
                            return false;
                        }

                        else if (nombre_destinatario.value.length == 0) {
                            $("#nombre_destinatario").css({borderColor: "red"});
                            $("#nombre_destinatario").focus();
                            return false;
                        }
                        else if (identificacion_destinario.value.length == 0) {
                            $("#identificacion_destinario").css({borderColor: "red"});
                            $("#identificacion_destinario").focus();
                            return false;
                        }
                        else if (cod_establecimiento_destino.value.length == 0) {
                            $("#cod_establecimiento_destino").css({borderColor: "red"});
                            $("#cod_establecimiento_destino").focus();
                            return false;
                        }
                        else if (identificacion_trasportista.value.length == 0) {
                            $("#identificacion_trasportista").css({borderColor: "red"});
                            $("#identificacion_trasportista").focus();
                            return false;
                        }
                        else if (nombre_trasportista.value.length == 0) {
                            $("#nombre_trasportista").css({borderColor: "red"});
                            $("#nombre_trasportista").focus();
                            return false;
                        }
                        else if (placa.value.length == 0) {
                            $("#placa").css({borderColor: "red"});
                            $("#placa").focus();
                            return false;
                        }
                        else if (doc.length != 0) {
                            while (n < doc.length) {
                                n++;
                                if ($('#cantidad' + n).val().length == 0) {
                                    $('#cantidad' + n).css({borderColor: "red"});
                                    $('#cantidad' + n).focus();
                                    return false;
                                }
                            }
                        }
                        if (id != 0) {
                            if (comp != $("#num_comprobante").val()) {
                                alert('No se puede modificar numero de Guia Remision');
                                $("#num_comprobante").css({borderColor: "red"});
                                $("#num_comprobante").focus();
                                return false;
                            }
                        }
                        if (vnd == '') {
                            alert('El usuario no es vendedor');
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_guia_remision.php',
                    data: {op: 0, 'data[]': data, 'data2[]': data2, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            cancelar();
                        } else if (dt == 1) {
                            alert('Numero Secuencial de la Guia Remision ya existe \n Debe hacer otra Guia Remision con otro Secuencial');
                            cancelar();
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }
            function cancelar() {
                numero = '<?php echo $rst[gui_num_comprobante] ?>';
                parent.document.getElementById('bottomFrame').src = '../Scripts/Lista_guias_factura.php?id=' + x + '&num=' + numero;
            }

            function saldo(obj) {
                n = obj.lang;
                var saldo = parseFloat($('#cantidadf' + n).val()) - parseFloat($('#entregado' + n).val());
                if (saldo < $(obj).val()) {
                    $(obj).css({borderColor: "red"});
                    $(obj).focus();
                    $(obj).val('');
                    alert('cantidad sobrepasa el saldo');
                    return false;
                } else {
                    if ($(obj).val().length == 0 || $(obj).val() == 0) {
                        var saldo = parseFloat($('#cantidadf' + n).val()) - parseFloat($('#entregado' + n).val());
                        $('#saldo' + n).val(saldo);
                    } else {
                        var saldo = parseFloat($('#cantidadf' + n).val()) - parseFloat($(obj).val()) - parseFloat($('#entregado' + n).val());
                        $('#saldo' + n).val(saldo);
                    }
                }
            }


            function load_transportista(obj) {
                $.post("actions_guia_remision.php", {op: 5, id: obj.value, s: 0},
                function (dt) {
                    dat = dt.split('&&');
                    if (dat[0].length != 0) {
                        $('#clientes').html(dat[0]);
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                    } else {
                        alert('Transportista no existe \n Cree uno Nuevo??');
                        $('#identificacion_trasportista').focus();
                        $('#tra_id').val('0');
                        $('#nombre_trasportista').val('');
                        $('#placa').val('');
                    }
                });
            }

            function load_transportista2(obj) {
                $.post("actions_guia_remision.php", {op: 5, id: obj, s: 1},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] == '') {
                        alert('Transportista no existe \n Cree uno Nuevo??');
                        $('#nombre_trasportista').focus();
                        $('#tra_id').val('0');
                        $('#nombre_trasportista').val('');
                        $('#placa').val('');
                    } else {
                        $('#identificacion_trasportista').val(dat[0]);
                        $('#nombre_trasportista').val(dat[1]);
                        $('#placa').val(dat[2]);
                        $('#tra_id').val(dat[3]);
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
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
            #observacion{
                text-transform: uppercase;
            }
            select{
                width: 150px;
            }
            .totales td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
            .obs{
                font-weight:bolder;
                font-size: 11px;
                text-transform: uppercase;
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
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO DE CONTROL <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>

                </thead>
                <tr><td>
                        <table>
                            <tr>

                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="20"  id="fecha_emision"  value="<?php echo $rst[gui_fecha_emision] ?>" /><img src="../img/calendar.png" id="im-campo1" readonly/></td>
                                <td>FECHA INCIO DE TRASLADO:</td>
                                <td><input type="text" size="20"  id="fecha_inicio_transporte"  value="<?php echo $rst[gui_fecha_inicio] ?>" /><img src="../img/calendar.png" id="im-campo2"/></td>
                                <td>FECHA TERMINACION DE TRASLADO:</td>
                                <td><input type="text" size="20"  id="fecha_fin_transporte"  value="<?php echo $rst[gui_fecha_fin] ?>" /><img src="../img/calendar.png" id="im-campo3"/></td>
                            </tr>
                            <tr>
                                <td>GUIA DE REMISION NO.:</td>
                                <td><input type="text" size="20"  id="num_comprobante"  value="<?php echo $rst[gui_numero] ?>" readonly/></td>
                                <td>TIPO DOCUMENTO:</td>
                                <td><input type="text" size="20"  id="tipo_comprobante" value="<?php echo $rst[gui_tipo] ?>"readonly/>
                                <td>NO. DOCUMENTO:</td>
                                <td><input type="text" size="20"  id="num_comprobante_venta"  value="<?php echo $rst[gui_num_comprobante] ?>" readonly/>
                                    <input type="hidden" size="10"  id="fac_id"  value="<?php echo $rst[fac_id] ?>" readonly/></td>
                            </tr>

                            <tr>
                                <td>NRO. DECLARACION ADUANERA:</td>
                                <td><input type="text" size="23"  id="documento_aduanero" value="<?php echo $rst[gui_doc_aduanero] ?>"/>
                            </tr>
                            <tr>
                                <td>MOTIVO DEL TRASLADO:</td>
                                <td><input type="text" size="23"  id="motivo_traslado" value="<?php echo $rst[gui_motivo_traslado] ?>"/>
                                <td>PUNTO DE PARTIDA:</td>
                                <td><input type="text" size="23"  id="punto_partida"  value="<?php echo $rst[gui_punto_partida] ?>" /></td>
                                <td>DESTINO:</td>
                                <td><input type="text" size="23"  id="destino"  value="<?php echo $rst[gui_destino] ?>"/></td>
                            </tr>
                            <tr>
                                <td>CEDULA / RUC:</td>
                                <td><input type="text" size="18"  readonly id="identificacion_destinario"  value="<?php echo $rst[gui_identificacion] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                                <td>CLIENTE:</td>
                                <td><input type="text" size="18" readonly id="nombre_destinatario"  value="<?php echo $rst[gui_nombre] ?>"/>
                                    <input type="hidden" size="10"  id="cli_id"  value="<?php echo $rst[cli_id] ?>"/></td>
                                <td>COD. ESTABLECIMIENTO DESTINO:</td>
                                <td><input type="text" size="23"  id="cod_establecimiento_destino"  value="<?php echo $rst[gui_cod_establecimiento] ?>"</td>
                            </tr>
                            <tr>
                                <td>CEDULA / RUC TRANSPORTISTA:</td>
                                <td><input type="text" size="23"  id="identificacion_trasportista"  value="<?php echo $rst[identificacion] ?>" onchange="load_transportista(this)" /></td>
                                <td>NOMBRE TRANSPORTISTA:</td>
                                <td><input type="text" size="23"  id="nombre_trasportista"  value="<?php echo $rst[razon_social] ?>"/>
                                    <input type="hidden" size="10"  id="tra_id"  value="<?php echo $rst[id] ?>"/></td>
                                <td>PLACA:</td>
                                <td><input type="text" size="23"  id="placa"  value="<?php echo $rst[placa] ?>"  /></td>
                            </tr>

                        </table>
                <tr><td>
                        <table id="guia">
                            <thead id="tabla">
                                <tr>
                                    <th>Item</th>
                                    <th>Codigo</th>
                                    <th>Descripcion</th>
                                    <th>Lote</th>
                                    <th>Solicitado</th>
                                    <th>Entregado</th>
                                    <th>Saldo</th>
                                    <th>Cantidad</th>
                                <tr>
                            </thead>
                            <?php
                            $n = 0;
                            $suma = 0;
                            while ($rst1 = pg_fetch_array($cns)) {
                                $n++;
                                ?>
                                <tr id="matriz">
                                    <td align="right"><input type ="text" size="8"  class="itm" id="item<?php echo $n ?>"  readonly value="<?php echo $n ?>"/></td>
                                    <td><input type ="text" size="20"  id="cod_producto<?php echo $n ?>"  value="<?php echo $rst1[dfc_codigo] ?>" lang="1" readonly/>
                                        <input type ="hidden" size="10"  id="pro_id<?php echo $n ?>"  value="<?php echo $rst1[pro_id] ?>" lang="1" readonly/>
                                        <input type ="hidden" size="10"  id="tabla<?php echo $n ?>"  value="<?php echo $rst1[dfc_tab] ?>" lang="1" readonly/></td>
                                    <td><input type ="text" size="50"  id="descripcion<?php echo $n ?>"  value="<?php echo $rst1[dfc_descripcion] ?>" lang="1" readonly/></td>
                                    <td><input type ="text" size="15"  id="lote<?php echo $n ?>"  value="<?php echo $rst1[dfc_lote] ?>" lang="1" readonly/></td>
                                    <td><input type ="text" size="10"  id="cantidadf<?php echo $n ?>"  value="<?php echo $rst1[dfc_cantidad] ?>" lang="1" readonly/></td>
                                    <?php
                                    $rst_sum = pg_fetch_array($Clase_guia_remision->suma_cantidad_entregado($rst1[pro_id], $rst[fac_id]));
                                    if ($rst_sum[suma] == '') {
                                        $rst_sum[suma] = 0;
                                    }
                                    $rst_can = pg_fetch_array($Clase_guia_remision->lista_cantidad($rst1[pro_id], $id));
                                    $saldo = $rst1[dfc_cantidad] - $rst_sum[suma];
                                    ?>
                                    <td><input type ="text" size="10"  id="entregado<?php echo $n ?>"  value="<?php echo $rst_sum[suma] ?>" lang="1" readonly/></td>
                                    <td><input type ="text" size="10"  id="saldo<?php echo $n ?>"  value="<?php echo $saldo ?>" lang="1" readonly/>
                                        <input type ="text" size="10"  id="saldo1<?php echo $n ?>"  value="<?php echo $saldo ?>" lang="1" readonly hidden/></td>
                                    <td><input type ="text" size="10"  id="cantidad<?php echo $n ?>"  value="<?php echo $rst_can[dfc_cantidad] ?>" lang="<?php echo $n ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), saldo(this)"/></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                    </td> </tr>
                <tr><td> 
                        <table>
                            <tr style="height: 20px"></tr>
                            <tr>
                                <td style="width: 700px">Observaciones: </td>
                            </tr>
                            <tr>
                                <td><textarea id="observacion" style="width:100%"><?php echo $rst[gui_observacion] ?></textarea></td>    
                            </tr>
                        </table>     
                    </td> </tr>
                <tfoot>
                    <tr><td> 
                            <button id="guardar">Guardar</button>    
                            <button id="cancelar" >Cancelar</button>
                        </td> </tr>
                </tfoot>
            </table>

        </form>
    </body>
</html>
<datalist id="lista_transportista">
    <?php
    $cns_trans = $Clase_guia_remision->lista_transportista();
    while ($rst_trans = pg_fetch_array($cns_trans)) {
        echo "<option value='$rst_trans[identificacion]'>$rst_trans[identificacion]</option>";
    }
    ?>
</datalist>


