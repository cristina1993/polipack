<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_ecocambrella.php';
$Set = new Clase_reg_ecocambrella();
$txt = trim(strtoupper($_GET[txt]));
$est = $_GET[estado];
$fec1 = $_GET[fecha1];
$fec2 = $_GET[fecha2];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $texto = "and rec_id=$id";
    $rst = pg_fetch_array($Set->lista_buscador_orden($texto));
    $rst1 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro_secundario]));
    $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst[ord_id]));
    $tot_peso_pri = ($rst[pro_ancho] * $rst[ord_largo] * $rst[ord_gramaje] * $rst[ord_num_rollos])/1000;
    $f1 = $tot_peso_pri - $rst_prod[peso];
    $fr1 = $rst[ord_num_rollos] - $rst_prod[rollo];
    if ($rst[ord_pro_secundario] == 0) {
        $readonly = 'readonly';
        $sol_p = '0';
        $sol_r2 = '0';
        $f2 = '0';
        $fr2 = '0';
    } else {
        $tot_peso_sec = ($rst1[pro_ancho] * $rst[ord_largo] * $rst[ord_gramaje] * $rst[ord_num_rollos])/1000;
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
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
                Calendar.setup({inputField: "rec_fecha", ifFormat: "%Y-%m-%d", button: "im-rec_fecha"});
            });
            function save(id) {
                var data = Array(
                        rec_fecha.value,
                        rec_peso_primario.value,
                        rec_peso_secundario.value,
                        rec_rollo_primario.value,
                        rec_rollo_secundario.value,
                        rec_desperdicio.value,
                        rec_operador.value,
                        ord_id.value,
                        rec_numero.value
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
                        else if (rec_peso_primario.value.length == 0) {
                            $("#rec_peso_primario").css({borderColor: "red"});
                            $("#rec_peso_primario").focus();
                            return false;
                        }
                        else if (rec_rollo_primario.value.length == 0) {
                            $("#rec_rollo_primario").css({borderColor: "red"});
                            $("#rec_rollo_primario").focus();
                            return false;
                        }
                        else if (rec_peso_secundario.value.length == 0) {
                            $("#rec_peso_secundario").css({borderColor: "red"});
                            $("#rec_peso_secundario").focus();
                            return false;
                        }
                        else if (rec_rollo_secundario.value.length == 0) {
                            $("#rec_rollo_secundario").css({borderColor: "red"});
                            $("#rec_rollo_secundario").focus();
                            return false;
                        }
                        else if (rec_desperdicio.value.length == 0) {
                            $("#rec_desperdicio").css({borderColor: "red"});
                            $("#rec_desperdicio").focus();
                            return false;
                        }
                        else if (rec_operador.value.length == 0) {
                            $("#rec_operador").css({borderColor: "red"});
                            $("#rec_operador").focus();
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_reg_ecocambrella.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            cancelar();
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_reg_op_ecocambrella.php?txt=' + '<?php echo $txt ?>' + '&estado=' + '<?php echo $est ?>' + '&fecha1=' + '<?php echo $fec1 ?>' + '&fecha2=' + '<?php echo $fec2 ?>';
            }

            function load_orden(obj) {
                $.post("actions_reg_ecocambrella.php", {op: 2, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#ord_id').val(dat[0]);
                        $('#ord_numero').val(dat[1]);
                        $('#pro_primario').val(dat[2]);
                        $('#pro_secundario').val(dat[3]);
                        $('#sol1').val(dat[4]);
                        $('#sol2').val(dat[10]);
                        $('#sol_r1').val(dat[5]);
                        $('#sol_r2').val(dat[5]);
                        $('#prod1').val(dat[6]);
                        $('#prod2').val(dat[7]);
                        $('#prod_r1').val(dat[8]);
                        $('#prod_r2').val(dat[9]);
                        f1 = dat[4] - dat[6];
                        f2 = dat[4] - dat[7];
                        fr1 = dat[5] - dat[8];
                        fr2 = dat[5] - dat[9];
                        $('#fal1').val(f1);
                        $('#fal2').val(f2);
                        $('#fal_r1').val(fr1);
                        $('#fal_r2').val(fr2);
                        if (dat[3] == '0' || dat[3] == '') {
                            $('#rec_peso_secundario').attr('readonly', true);
                            $('#rec_rollo_secundario').attr('readonly', true);
                            $('#rec_rollo_secundario').val('0');
                            $('#rec_peso_secundario').val('0');
                            $('#sol2').val('0');
                            $('#sol_r2').val('0');
                        }
                        if (dat[6] == '') {
                            $('#prod1').val('0');
                            $('#fal1').val('0');
                        }
                        if (dat[7] == '') {
                            $('#prod2').val('0');
                            $('#fal2').val('0');
                        }
                        if (dat[8] == '') {
                            $('#prod_r1').val('0');
                            $('#fal_r1').val('0');
                        }
                        if (dat[9] == '') {
                            $('#prod_r2').val('0');
                            $('#fal_r2').val('0');
                        }
                    } else {
                        alert('Orden no existe');
                        $('#ord_numero').focus();
                    }
                });
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
                    }
                }
            }

//            function tab() {
//                $("#frm_save").find(':input').each(function () {
//                    if ($(this).attr('readonly') == true) {
//                        $(this).blur();
//                    }
//                });
//            }
        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
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
                    <tr><th colspan="9" >REGISTRO DE PRODUCCION ECOCAMBRELLA  <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>REGISTRO #:</td>
                    <td>
                        <input type="text" size="15" id="rec_numero" readonly value="<?php echo $rst[rec_numero] ?>" />
                    </td>
                </tr>
                <tr>
                    <td>ORDEN#:</td>
                    <td>
                        <input type="text" size="15" id="ord_numero" value="<?php echo $rst[ord_num_orden] ?>" onchange="load_orden(this)"/>
                        <input type="hidden" size="10" id="ord_id" readonly value="<?php echo $rst[ord_id] ?>" />
                    </td>
                    <td>FECHA:</td>
                    <td>
                        <input type="text" size="15" id="rec_fecha"  value="<?php echo $rst[rec_fecha] ?>" />
                        <img src="../img/calendar.png" id="im-rec_fecha" />
                    </td>
                </tr>
                <tr>
                    <td># OPERADOR:</td>
                    <td><input type="text" size="15"  id="rec_operador" value="<?php echo $rst[rec_operador] ?>" /></td>
                    <td>DESPERDICIO:</td>
                    <td><input type="text" size="15"  id="rec_desperdicio" value="<?php echo $rst[rec_desperdicio] ?>" onkeypress="tab(event, 0)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                </tr>

                <thead>
                    <tr>
                        <th colspan="5">PRODUCTO PRIMARIO</th>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <input type="text" size="60" id="pro_primario" readonly value="<?php echo $rst[pro_descripcion] ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Solicitado</th>
                        <th>Producido</th>
                        <th>Faltante</th>
                        <th>Registro</th>
                    </tr>
                </thead>

                <tr>
                    <td>PESO TOTAL</td>
                    <td>
                        <input type="text" size="8" id="sol1" value="<?php echo $tot_peso_pri ?>" readonly />kg
                    </td>
                    <td>
                        <input type="text" size="8" id="prod1" value="<?php echo $rst_prod[peso] ?>" readonly/>kg
                    </td>
                    <td>
                        <input type="text" size="8" id="fal1" value="<?php echo $f1 ?>" readonly/>kg
                    </td>
                    <td>
                        <input type="text" size="10" id="rec_peso_primario" value="<?php echo $rst[rec_peso_primario] ?>" onchange="validar()" onkeypress="tab(event, 1)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg
                    </td>
                </tr>
                <tr>
                    <td># ROLLO</td>
                    <td>
                        <input type="text" size="8" id="sol_r1" value="<?php echo $rst[ord_num_rollos] ?>" readonly/>
                    </td>
                    <td>
                        <input type="text" size="8" id="prod_r1" value="<?php echo $rst_prod[rollo] ?>" readonly/>
                    </td>
                    <td>
                        <input type="text" size="8" id="fal_r1" value="<?php echo $fr1 ?>" readonly/>
                    </td>
                    <td>
                        <input type="text" size="10" id="rec_rollo_primario" value="<?php echo $rst[rec_rollo_primario] ?>" onchange="validar()" onkeypress="tab(event, 2)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />
                    </td>
                </tr>

                <thead>
                    <tr>
                        <th colspan="5">PRODUCTO SECUNDARIO</th>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <input type="text" size="60" id="pro_secundario" readonly value="<?php echo $rst1[pro_descripcion] ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Solicitado</th>
                        <th>Producido</th>
                        <th>Faltante</th>
                        <th>Registro</th>
                    </tr>
                </thead>

                <tr>
                    <td>PESO TOTAL</td>
                    <td>
                        <input type="text" size="8" id="sol2" value="<?php echo $tot_peso_sec ?>"  readonly/>kg
                    </td>
                    <td>
                        <input type="text" size="8" id="prod2" value="<?php echo $rst_prod[peso2] ?>"  readonly/>kg
                    </td>
                    <td>
                        <input type="text" size="8" id="fal2" value="<?php echo $f2 ?>"  readonly/>kg
                    </td>
                    <td>
                        <input type="text" size="10" id="rec_peso_secundario" value="<?php echo $rst[rec_peso_secundario] ?>"  <?php echo $readonly ?> onchange="validar()" onkeypress="tab(event, 3)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg
                    </td>

                </tr>
                <tr>
                    <td># ROLLO</td>
                    <td>
                        <input type="text" size="8" id="sol_r2" value="<?php echo $sol_r2 ?>" readonly/>
                    </td>
                    <td>
                        <input type="text" size="8" id="prod_r2" value="<?php echo $rst_prod[rollo2] ?>" readonly/>
                    </td>
                    <td>
                        <input type="text" size="8" id="fal_r2" value="<?php echo $fr2 ?>" readonly/>
                    </td>
                    <td>
                        <input type="text" size="10" id="rec_rollo_secundario" value="<?php echo $rst[rec_rollo_secundario] ?>" <?php echo $readonly ?> onchange="validar()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />
                    </td>
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
