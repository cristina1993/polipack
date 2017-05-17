<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_resultados_operaciones.php';
$Set = new Clase_resultados_operaciones();
if (isset($_GET[id])) {
    $id = $_GET[id];
    echo $x = $_GET[x];
    $rst = pg_fetch_array($Set->lista_resultado_operacion_id($id));
    $anio = $rst[rop_anio];
    $mes = $rst[rop_mes];
} else {
    $id = 0;
    $x = 0;
    $rst_sec = pg_fetch_array($Set->lista_secuencial());
    $sec = ($rst_sec[rop_secuencial] + 1);
    if ($sec >= 0 && $sec < 10) {
        $tx = '0000';
    } else if ($sec >= 10 && $sec < 100) {
        $tx = '000';
    } else if ($sec >= 100 && $sec < 1000) {
        $tx = '00';
    } else if ($sec >= 1000 && $sec < 10000) {
        $tx = '0';
    } else if ($sec >= 10000 && $sec < 100000) {
        $tx = '';
    }
    $rst[rop_secuencial] = $tx . $sec;
    $anio = $_GET[anio];
    $mes = $_GET[mes];
}
switch ($mes) {
    case 1:
        $mes_letras = 'ENERO';
        break;
    case 2:
        $mes_letras = 'FEBRERO';
        break;
    case 3:
        $mes_letras = 'MARZO';
        break;
    case 4:
        $mes_letras = 'ABRIL';
        break;
    case 5:
        $mes_letras = 'MAYO';
        break;
    case 6:
        $mes_letras = 'JUNIO';
        break;
    case 7:
        $mes_letras = 'JULIO';
        break;
    case 8:
        $mes_letras = 'AGOSTO';
        break;
    case 9:
        $mes_letras = 'SEPTIEMBRE';
        break;
    case 10:
        $mes_letras = 'OCTUBRE';
        break;
    case 11:
        $mes_letras = 'NOVIEMBRE';
        break;
    case 12:
        $mes_letras = 'DICIEMBRE';
        break;
}
if ($mes == 1) {
    $ant_anio = $anio - 1;
    $m_ant = '11';
} else {
    $ant_anio = $anio;
    $m_ant = $mes - 1;
}
$ultimo_dia = 28;
while (checkdate($mes, $ultimo_dia + 1, $anio)) {
    $ultimo_dia++;
}
if ($mes < 10) {
    $mes = '0' . $mes;
}
$desde = '01-' . $mes . '-' . $anio;
$hasta = $ultimo_dia . '-' . $mes . '-' . $anio;


//// extrusion materia prima
$rst_mp = pg_fetch_array($Set->lista_materia_prima($desde, $hasta));
//$cunit = $rst_mp[val] / $rst_mp[cnt];
$cunit = round($rst_mp[val_unit],2);
//// extrusion insumos
$rst_ins = pg_fetch_array($Set->lista_insumos($desde, $hasta));
//$ext_ins_cunit = $rst_ins[val] / $rst_ins[cnt];
$ext_ins_cunit=round($rst_ins[val_unit],2);
$rst_sum = pg_fetch_array($Set->lista_suma_detalle($id, '0'));
$tcost_ex = $rst_mp[val] + $rst_ins[val] + $rst_sum[sum];

$rst_ext = pg_fetch_array($Set->lista_extruido($desde, $hasta));
///total costo extrusion
$tot_ext = $rst_ext[conforme] + $rst_ext[noconforme]; ///total producido
$cgunit = $tcost_ex / $tot_ext; ///costo unitario global
$ccunit = $tcost_ex / $rst_ext[conforme]; ///costo unitario conforme
$cncunit = $tcost_ex / $rst_ext[noconforme]; ///costo unitario no conforme
//// corte inicial materia prima
$rst_ant = pg_fetch_array($Set->lista_resultado_anterior($m_ant, $ant_anio));
$mpunit = ($rst_ant[tot_costo_extrusion]) / ($rst_ant[rop_tot_producido]);
///costo produccion
$cproduccion = $tot_ext * round($cgunit, 4);
//// corte consumo materia prima
$rst_cmp = pg_fetch_array($Set->lista_materia_prima_corte($desde, $hasta));
$crt_cmp_cunit = ($tcost_ex + $rst_ant[tot_costo_extrusion]) / ($tot_ext + $rst_ant[rop_tot_producido]);
$rst_cmp[val] = $rst_cmp[cnt] * round($crt_cmp_cunit, 4);
//// corte insumos
$rst_cins = pg_fetch_array($Set->lista_insumos_corte($desde, $hasta));
$crt_cins_cunit = $rst_cins[val] / $rst_cins[cnt];
///costo de corte
$rst_sum = pg_fetch_array($Set->lista_suma_detalle($id, '1'));
$tot_cost_crt = $rst_cins[val] + $rst_cmp[val] + $rst_sum[sum];
///costo total de operacion
$inv_final = $rst_ant[rop_tot_producido] + $tot_ext - $rst_cmp[cnt];
//$unit_inv_final=$crt_cmp_cunit;
$cost_inv_final = round($crt_cmp_cunit, 4) * $inv_final;
$cost_tot_operacion = $cost_inv_final + $tot_cost_crt;
$rst_det[rub_id] = 0;
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
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
//                    clona_fila($('#extrusion'));
                });
            });

            function clona_fila(table, op) {
                var tr = $(table).find("tbody tr:last").clone();
                if (op == 0) {
                    cr = 'cost_r';
                    ad = 'add';
                    rub = '#rubro';
                } else {
                    cr = 'ccost_r';
                    ad = 'addc';
                    rub = '#crubro';
                }
                tr.find("input,select").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                     x = ++parts[2];
                    this.lang = x
                    if (parts[1] == cr) {
                        this.value = 0;
                    } else if (parts[1] == ad) {
                        this.value = '+';
                    } else {
                        this.value = '';
                    }
                    return parts[1] + x;
                    
                });
                $(table).find("tbody tr:last").after(tr);
                $(rub + x).focus();
                $(rub + x).val('0');
                total();
            }
            function elimina_fila(obj, op) {
                if (op == 0) {
                    tb = "#tbl_detalle";
                } else {
                    tb = "#tbl_detalle2";
                }
                itm = $(tb + ' .itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
                total();
            }

            function save(id) {
                mes = '<?php echo $mes ?>'
                var data = Array(
                        ext_mp_cnt.value,
                        ext_mp_cunit.value,
                        ext_mp_cost.value,
                        ext_ins_cnt.value,
                        ext_ins_cunit.value,
                        ext_ins_cost.value,
                        tcost_ext.value,
                        tconforme.value,
                        tnoconforme.value,
                        tproducido.value,
                        cunit_global.value,
                        cunit_conforme.value,
                        cunit_noconforme.value,
                        crt_mp_cnt.value,
                        crt_mp_cunit.value,
                        crt_mp_cost.value,
                        crt_prod_cnt.value,
                        crt_prod_cunit.value,
                        crt_prod_cost.value,
                        crt_cmp_cnt.value,
                        crt_cmp_cunit.value,
                        crt_prod_cost.value,
                        crt_cins_cnt.value,
                        crt_cins_cunit.value,
                        crt_cins_cost.value,
                        cost_corte.value,
                        cost_tot_operacion.value,
                        secuencial.value,
                        anio.value,
                        mes
                        );

                var tr = $('#tbl_detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;

                var data2 = Array();
                while (n < i) {
                    n++;
                    if ($('#rubro' + n).val() != null) {
                        data2.push(
                                $('#rubro' + n).val() + '&' +
                                $('#cost_r' + n).val() + '&' +
                                '0'
                                );
                    }
                }

                var tr = $('#tbl_detalle2').find("tbody tr:last");
                b = tr.find("input").attr("lang");
                j = parseInt(b);
                m = 0;
                while (m < j) {
                    m++;
                    if ($('#crubro' + m).val() != null) {
                        data2.push(
                                $('#crubro' + m).val() + '&' +
                                $('#ccost_r' + m).val() + '&' +
                                '1'
                                );
                    }
                }
                var fields = Array();
                $("#tbl_form").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        var tr = $('#tbl_detalle').find("tbody tr:last");
                        a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        n = 0;
                        while (n < i) {
                            n++;
                            if ($('#rubro' + n).val() == "0") {
                                $('#rubro' + n).css({borderColor: "red"});
                                $('#rubro' + n).focus();
                                return false;
                            }
                            if ($('#cost_r' + n).val().length == 0) {
                                $('#cost_r' + n).css({borderColor: "red"});
                                $('#cost_r' + n).focus();
                                return false;
                            }
                        }

                        var tr = $('#tbl_detalle2').find("tbody tr:last");
                        b = tr.find("input").attr("lang");
                        j = parseInt(b);
                        m = 0;
                        while (m < j) {
                            m++;
                            if ($('#crubro' + m).val() == "0") {
                                $('#crubro' + m).css({borderColor: "red"});
                                $('#crubro' + m).focus();
                                return false;
                            }
                            if ($('#ccost_r' + m).val().length == 0) {
                                $('#ccost_r' + m).css({borderColor: "red"});
                                $('#ccost_r' + m).focus();
                                return false;
                            }
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_resultados_operaciones.php',
                    data: {op: 0, 'data[]': data, 'data2[]': data2, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_resultados_operaciones.php';
                        } else {
                            loading('hidden');
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function total() {
                var tr = $('#tbl_detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                sum = 0;
                while (n < i) {
                    n++;
                    if ($('#cost_r' + n).val().length == 0) {
                        can = 0;
                    } else {
                        can = $('#cost_r' + n).val();
                    }
                    sum = sum + parseFloat(can);
                }
                tcost = sum + parseFloat($('#ext_mp_cost').val()) + parseFloat($('#ext_ins_cost').val());
                $('#tcost_ext').val(tcost.toFixed(2));
                if (parseFloat($('#tproducido').val()) == 0) {
                    cgunit = 0;
                } else {
                    cgunit = tcost / parseFloat($('#tproducido').val());
                }
                if (parseFloat($('#tconforme').val()) == 0) {
                    ccunit = 0;
                } else {
                    ccunit = tcost / parseFloat($('#tconforme').val());
                }
                if (parseFloat($('#tnoconforme').val()) == 0) {
                    cncunit = 0;
                } else {
                    cncunit = tcost / parseFloat($('#tnoconforme').val());
                }

                $('#cunit_global').val(cgunit.toFixed(2));
                $('#cunit_conforme').val(ccunit.toFixed(2));
//                $('#cunit_noconforme').val(cncunit.toFixed(2));
                $('#cunit_noconforme').val(0.00);
                $('#crt_prod_cunit').val(cgunit.toFixed(4));

                ccrtp = parseFloat($('#crt_prod_cnt').val()) * parseFloat($('#crt_prod_cunit').val());
                $('#crt_prod_cost').val(ccrtp.toFixed(2));
                ////calculo costo corte
                var tr = $('#tbl_detalle2').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                sumc = 0;
                while (n < i) {
                    n++;
                    if ($('#ccost_r' + n).val().length == 0) {
                        canc = 0;
                    } else {
                        canc = $('#ccost_r' + n).val();
                    }
                    sumc = sumc + parseFloat(canc);
                }


                cmp_cunit = (parseFloat($('#crt_mp_cost').val()) + parseFloat($('#crt_prod_cost').val())) / (parseFloat($('#crt_mp_cnt').val()) + parseFloat($('#crt_prod_cnt').val()));
                $('#crt_cmp_cunit').val(cmp_cunit.toFixed(4));
                cmp_cost = parseFloat($('#crt_cmp_cnt').val()) * parseFloat($('#crt_cmp_cunit').val());
                $('#crt_cmp_cost').val(cmp_cost.toFixed(2));
                tcostcrt = sumc + parseFloat($('#crt_cmp_cost').val()) + parseFloat($('#crt_cins_cost').val());
                $('#cost_corte').val(tcostcrt.toFixed(2));
                ///costo total de operacion
                $('#inv_cunit').val(cmp_cunit.toFixed(4));
                inv_final = parseFloat($('#crt_mp_cnt').val()) + parseFloat($('#crt_prod_cnt').val()) - parseFloat($('#crt_cmp_cnt').val());
                $('#inv_cnt').val(inv_final.toFixed(2));
                cost_inv_final = parseFloat($('#inv_cunit').val()) * parseFloat($('#inv_cnt').val());
                cost_operacion = cost_inv_final + parseFloat($('#cost_corte').val());


                $('#inv_cost').val(cost_inv_final.toFixed(2));
                $('#cost_tot_operacion').val(cost_operacion.toFixed(2));

            }

        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
        </style>
    </head>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO DE CONTROL  <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>

                <tbody id="extrusion">
                    <tr>
                        <td>CODIGO
                            <input type="text" size="10" id="secuencial" value="<?php echo $rst[rop_secuencial] ?>" readonly/></td>
                        <td colspan="2">Año
                            <input type="text" size="10" id="anio" value="<?php echo $anio ?>" readonly/></td>
                        <td colspan="2">Mes
                            <input type="text" size="10" id="mes" value="<?php echo $mes_letras ?>" readonly/></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="sbtitle" >COSTO DE EXTRUSION</td>
                    </tr>
                    <tr>
                        <td style="width: 200px"></td>
                        <td style="width: 100px">CANTIDAD</td>
                        <td style="width: 100px">C/UNITARIO</td>
                        <td style="width: 100px">COSTO</td>
                        <td style="width: 30px"></td>
                        <td style="width: 30px"></td>
                    </tr>
                    <tr>
                        <td style="width: 200px">MATERIA PRIMA</td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="ext_mp_cnt" value="<?php echo str_replace(',', '', number_format($rst_mp[cnt], 2)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="ext_mp_cunit" value="<?php echo str_replace(',', '', number_format($cunit, 4)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="ext_mp_cost" value="<?php echo str_replace(',', '', number_format($rst_mp[val], 2)) ?>" readonly/></td>
                        <td style="width: 30px"></td>
                        <td style="width: 30px"></td>
                    </tr>
                    <tr>
                        <td style="width: 200px">INSUMOS</td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="ext_ins_cnt" value="<?php echo str_replace(',', '', number_format($rst_ins[cnt], 2)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="ext_ins_cunit" value="<?php echo str_replace(',', '', number_format($ext_ins_cunit, 4)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="ext_ins_cost" value="<?php echo str_replace(',', '', number_format($rst_ins[val], 2)) ?>" readonly/></td>
                        <td style="width: 30px"></td>
                        <td style="width: 30px"></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <table id="tbl_detalle">
                                <?php
                                if (empty($id)) {
                                    ?>
                                    <tr>
                                        <td style="width: 190px">
                                            <select id="rubro1" name="rubro1" lang="1" class="itm">
                                                <option value='0'>SELECCIONE</option>
                                                <?php
                                                $cns_rub = $Set->lista_rubros();
                                                while ($rst_rub = pg_fetch_array($cns_rub)) {
                                                    echo "<option value='$rst_rub[rub_id]'>$rst_rub[rub_descripcion]</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td style="width: 100px"></td>
                                        <td style="width: 100px"></td>
                                        <td><input type="text" size="8" style="text-align: right" id="cost_r1" name="cost_r1" value="0" lang="1" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="total()"/></td>
                                        <td align="center" class="impresion">
                                            <input type="button" lang="1" id="add1" name="add1" value="+" onclick="clona_fila($('#extrusion'), 0)"/>
                                        </td>
                                        <td onclick="elimina_fila(this, 0)" align="center" class="impresion">
                                            <img class="auxBtn" width="14px" src="../Img/del_reg.png" />
                                        </td>
                                    </tr>
                                    <?php
                                } else {
                                    $cns_det_ext = $Set->lista_detalle($id, '0');
                                    $n = 0;
                                    while ($rst_det = pg_fetch_array($cns_det_ext)) {
                                        $n++;
                                        ?>
                                        <tr>
                                            <td style="width: 190px">
                                                <select id="rubro<?php echo $n ?>" name="rubro<?php echo $n ?>" lang="<?php echo $n ?>" class="itm">
                                                    <option value='0'>SELECCIONE</option>
                                                    <?php
                                                    $cns_rub = $Set->lista_rubros();
                                                    while ($rst_rub = pg_fetch_array($cns_rub)) {
                                                        echo "<option value='$rst_rub[rub_id]'>$rst_rub[rub_descripcion]</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td style="width: 100px"></td>
                                            <td style="width: 100px"></td>
                                            <td><input type="text" size="8" style="text-align: right" id="cost_r<?php echo $n ?>" name="cost_r<?php echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst_det[dro_costo], 2)) ?>" lang="<?php echo $n ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="total()"/></td>
                                            <td align="center" class="impresion">
                                                <input type="button" lang="<?php echo $n ?>" id="add<?php echo $n ?>" name="add<?php echo $n ?>" value="+" onclick="clona_fila($('#extrusion'), 0)"/>
                                            </td>
                                            <td onclick="elimina_fila(this, 0)" align="center" class="impresion">
                                                <img class="auxBtn" width="14px" src="../Img/del_reg.png" />
                                            </td>
                                        </tr>
                                        <script>
                                            var r = '<?php echo $rst_det[rub_id] ?>';
                                            var n = '<?php echo $n ?>';
                                            $('#rubro' + n).val(r);
                                        </script>
                                        <?php
                                    }
                                }
                                ?>
                            </table>
                        </td>
                    </tr>
                </tbody>
                <tr>
                    <td style="width: 200px">TOTAL COSTO EXTRUSION</td>
                    <td style="width: 100px"></td>
                    <td style="width: 100px" align="right">$</td>
                    <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="tcost_ext" value="<?php echo str_replace(',', '', number_format($tcost_ex, 2)) ?>" readonly/></td>
                </tr>
                <tr>
                    <td style="width: 200px">TOTAL PRODUCTO CONFORME</td>
                    <td style="width: 100px"></td>
                    <td style="width: 100px" align="right">KG</td>
                    <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="tconforme" value="<?php echo str_replace(',', '', number_format($rst_ext[conforme], 2)) ?>" readonly/></td>
                </tr>
                <tr>
                    <td style="width: 200px">TOTAL PRODUCTO NO CONFORME</td>
                    <td style="width: 100px"></td>
                    <td style="width: 100px" align="right">KG</td>
                    <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="tnoconforme" value="<?php echo str_replace(',', '', number_format($rst_ext[noconforme], 2)) ?>" readonly/></td>
                </tr>
                <tr>
                    <td style="width: 200px">TOTAL PRODUCIDO</td>
                    <td style="width: 100px"></td>
                    <td style="width: 100px" align="right">KG</td>
                    <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="tproducido" value="<?php echo str_replace(',', '', number_format($tot_ext, 2)) ?>" readonly/></td>
                </tr>
                <tr>
                    <td style="width: 200px">COSTO UNITARIO GLOBAL</td>
                    <td style="width: 100px"></td>
                    <td style="width: 100px" align="right">$</td>
                    <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="cunit_global" value="<?php echo str_replace(',', '', number_format($cgunit, 2)) ?>" readonly /></td>
                </tr>
                <tr>
                    <td style="width: 200px">COSTO UNITARIO CONFORME</td>
                    <td style="width: 100px"></td>
                    <td style="width: 100px" align="right">$</td>
                    <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="cunit_conforme" value="<?php echo str_replace(',', '', number_format($ccunit, 2)) ?>" readonly /></td>
                </tr>
                <tr>
                    <td style="width: 200px">COSTO UNITARIO NO CONFORME</td>
                    <td style="width: 100px"></td>
                    <td style="width: 100px" align="right">$</td>
                    <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="cunit_noconforme" value="<?php echo str_replace(',', '', number_format(0, 2)) ?>" readonly /></td>
                </tr>
                <tbody id="corte">
                    <tr>
                        <td  colspan="4" class="sbtitle">COSTO DE CORTE</td>
                    </tr>
                    <tr>
                        <td style="width: 200px"></td>
                        <td style="width: 100px">CANTIDAD</td>
                        <td style="width: 100px">C/UNITARIO</td>
                        <td style="width: 100px">COSTO</td>
                        <td style="width: 30px"></td>
                        <td style="width: 30px"></td>
                    </tr>
                    <tr>
                        <td style="width: 200px">INICIAL PERCHA</td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_mp_cnt" value="<?php echo str_replace(',', '', number_format($rst_ant[rop_tot_producido], 2)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_mp_cunit" value="<?php echo str_replace(',', '', number_format($mpunit, 4)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_mp_cost" value="<?php echo str_replace(',', '', number_format($rst_ant[tot_costo_extrusion], 2)) ?>" readonly/></td>
                        <td style="width: 30px"></td>
                        <td style="width: 30px"></td>
                    </tr>
                    <tr>
                        <td>INGRESO A PERCHA</td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_prod_cnt" value="<?php echo str_replace(',', '', number_format($tot_ext, 2)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_prod_cunit" value="<?php echo str_replace(',', '', number_format($cgunit, 4)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_prod_cost" value="<?php echo str_replace(',', '', number_format($cproduccion, 2)) ?>" readonly/></td>
                        <td style="width: 30px"></td>
                        <td style="width: 30px"></td>
                    </tr>
                    <tr>
                        <td>CONSUMO PERCHA</td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_cmp_cnt" value="<?php echo str_replace(',', '', number_format($rst_cmp[cnt], 2)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_cmp_cunit" value="<?php echo str_replace(',', '', number_format($crt_cmp_cunit, 4)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_cmp_cost" value="<?php echo str_replace(',', '', number_format($rst_cmp[val], 2)) ?>" readonly/></td>
                        <td style="width: 30px"></td>
                        <td style="width: 30px"></td>
                    </tr>
                    <tr>
                        <td>INV. FINAL</td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="inv_cnt" value="<?php echo str_replace(',', '', number_format($inv_final, 2)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="inv_cunit" value="<?php echo str_replace(',', '', number_format($crt_cmp_cunit, 4)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="inv_cost" value="<?php echo str_replace(',', '', number_format($cost_inv_final, 2)) ?>" readonly/></td>
                        <td style="width: 30px"></td>
                        <td style="width: 30px"></td>
                    </tr>
                    <tr>
                        <td>INSUMOS</td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_cins_cnt" value="<?php echo str_replace(',', '', number_format($rst_cins[cnt], 2)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_cins_cunit" value="<?php echo str_replace(',', '', number_format($crt_cins_cunit, 4)) ?>" readonly/></td>
                        <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="crt_cins_cost" value="<?php echo str_replace(',', '', number_format($rst_cins[val], 2)) ?>" readonly/></td>
                        <td style="width: 30px"></td>
                        <td style="width: 30px"></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <table id="tbl_detalle2">
                                <?php
                                if (empty($id)) {
                                    ?> 
                                    <tr>
                                        <td style="width: 190px">
                                            <select id="crubro1" name="crubro1" lang="1" class="itm">
                                                <option value='0'>SELECCIONE</option>
                                                <?php
                                                $cns_rub = $Set->lista_rubros();
                                                while ($rst_rub = pg_fetch_array($cns_rub)) {
                                                    echo "<option value='$rst_rub[rub_id]'>$rst_rub[rub_descripcion]</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td style="width: 100px"></td>
                                        <td style="width: 100px"></td>
                                        <td><input type="text" size="8" style="text-align: right" id="ccost_r1" name="ccost_r1" value="0" lang="1" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="total()"/></td>
                                        <td align="center" class="impresion">
                                            <input type="button" lang="1" id="addc1" name="addc1" value="+"  onclick="clona_fila($('#corte'), 1)" />
                                        </td>
                                        <td onclick="elimina_fila(this, 1)" align="center" class="impresion">
                                            <img class="auxBtn" width="14px" src="../Img/del_reg.png" />
                                        </td>
                                    </tr>
                                    <?php
                                } else {
                                    $cns_det_crt = $Set->lista_detalle($id, '1');
                                    $m = 0;
                                    while ($rst_det = pg_fetch_array($cns_det_crt)) {
                                        $m++;
                                        ?>
                                        <tr>
                                            <td style="width: 190px">
                                                <select id="crubro<?php echo $m ?>" name="crubro<?php echo $m ?>" lang="<?php echo $m ?>" class="itm">
                                                    <option value='0'>SELECCIONE</option>
                                                    <?php
                                                    $cns_rub = $Set->lista_rubros();
                                                    while ($rst_rub = pg_fetch_array($cns_rub)) {
                                                        echo "<option value='$rst_rub[rub_id]'>$rst_rub[rub_descripcion]</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td style="width: 100px"></td>
                                            <td style="width: 100px"></td>
                                            <td><input type="text" size="8" style="text-align: right" id="ccost_r<?php echo $m ?>" name="ccost_r<?php echo $m ?>" value="<?php echo str_replace(',', '', number_format($rst_det[dro_costo], 2)) ?>" lang="<?php echo $m ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="total()"/></td>
                                            <td align="center" class="impresion">
                                                <input type="button" lang="<?php echo $m ?>" id="addc<?php echo $m ?>" name="addc<?php echo $m ?>" value="+"  onclick="clona_fila($('#corte'), 1)" />
                                            </td>
                                            <td onclick="elimina_fila(this, 1)" align="center" class="impresion">
                                                <img class="auxBtn" width="14px" src="../Img/del_reg.png" />
                                            </td>
                                        </tr>
                                        <script>
                                            var r = '<?php echo $rst_det[rub_id] ?>';
                                            var m = '<?php echo $m ?>';
                                            $('#crubro' + m).val(r);
                                        </script>
                                        <?php
                                    }
                                }
                                ?>
                            </table>
                        </td>
                    </tr>
                </tbody>
                <tr>
                    <td style="width: 200px">COSTO DE CORTE</td>
                    <td style="width: 100px"></td>
                    <td style="width: 100px" align="right">$</td>
                    <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="cost_corte" value="<?php echo str_replace(',', '', number_format($tot_cost_crt, 2)) ?>" readonly/></td>
                </tr>
                <tr>
                    <td style="width: 200px">COSTO TOTAL DE OPERACION</td>
                    <td style="width: 100px"></td>
                    <td style="width: 100px" align="right">$</td>
                    <td style="width: 100px"><input type="text" size="6" style="text-align: right" id="cost_tot_operacion" value="<?php echo str_replace(',', '', number_format($cost_tot_operacion, 2)) ?>" readonly/></td>
                </tr>


                <tfoot>
                    <tr><td colspan="2">
                            <?PHP
                            if ($x != 1) {
                                ?>                 

                                <button id="guardar" onclick="save(<?php echo $id ?>, 0)">Guardar</button>    
                                <?PHP
                            }
                            ?>
                            <button id="cancelar" >Cancelar</button>
                        </td></tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>  

