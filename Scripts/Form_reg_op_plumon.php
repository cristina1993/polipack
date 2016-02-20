<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_plumon.php';
$Set = new Clase_reg_plumon();
$txt = trim(strtoupper($_GET[txt]));
$est = $_GET[estado];
$fec1 = $_GET[fecha1];
$fec2 = $_GET[fecha2];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $texto = "and r.rpl_id=$id";
    $rst = pg_fetch_array($Set->lista_buscador_orden($texto));
    $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst[orp_id]));
    $totkg =  $rst[orp_pro_peso] * $rst[orp_cantidad];
    $f1 = $totkg - $rst_prod[peso];
    $fr1 = $rst[orp_cantidad] - $rst_prod[rollo];
} else {
    $id = 0;
    $rst[rpl_fecha] = date('Y-m-d');
    $rst[orp_pro_peso] = '0';
    $rst[orp_cantidad] = '0';
    $rst[rpl_peso] = '0';
    $rst_prod[peso] = '0';
    $rst[rpl_rollo] = '0';
    $rst_prod[rollo] = '0';
    $f1 = '0';
    $fr1 = '0';
    $rst_sec = pg_fetch_array($Set->lista_secuencial_registro());
    if (!empty($rst_sec)) {
        $sec = substr($rst_sec[rpl_numero], 4, 8) + 1;
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
    $rst[rpl_numero] = 'PPL' . $tx_trs . $sec;
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
                Calendar.setup({inputField: "rpl_fecha", ifFormat: "%Y-%m-%d", button: "im-rpl_fecha"});
            });
            function save(id) {
                var data = Array(
                        rpl_fecha.value,
                        rpl_peso_primario.value,
                        rpl_rollo_primario.value,
                        rpl_desperdicio.value,
                        rpl_operador.value,
                        opl_id.value,
                        rpl_numero.value
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
                        if (opl_numero.value.length == 0) {
                            $("#opl_numero").css({borderColor: "red"});
                            $("#opl_numero").focus();
                            return false;
                        }
                        else if (rpl_peso_primario.value == 0) {
                            $("#rpl_peso_primario").css({borderColor: "red"});
                            $("#rpl_peso_primario").focus();
                            return false;
                        }
                        else if (rpl_rollo_primario.value == 0) {
                            $("#rpl_rollo_primario").css({borderColor: "red"});
                            $("#rpl_rollo_primario").focus();
                            return false;
                        }
                        else if (rpl_desperdicio.value.length == 0) {
                            $("#rpl_desperdicio").css({borderColor: "red"});
                            $("#rpl_desperdicio").focus();
                            return false;
                        }
                        else if (rpl_operador.value.length == 0) {
                            $("#rpl_operador").css({borderColor: "red"});
                            $("#rpl_operador").focus();
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_reg_plumon.php',
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_reg_op_plumon.php?txt=' + '<?php echo $txt ?>' + '&estado=' + '<?php echo $est ?>' + '&fecha1=' + '<?php echo $fec1 ?>' + '&fecha2=' + '<?php echo $fec2 ?>';
            }

            function load_orden(obj) {
                $.post("actions_reg_plumon.php", {op: 2, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#opl_id').val(dat[0]);
                        $('#opl_numero').val(dat[1]);
                        $('#pro_primario').val(dat[2]);
                        $('#sol1').val(dat[3]*dat[4]);
                        $('#sol_r1').val(dat[4]);
                        $('#prod1').val(dat[5]);
                        $('#prod_r1').val(dat[6]);
                        f1 = dat[3] - dat[5];
                        fr1 = dat[4] - dat[6];
                        $('#fal1').val(f1);
                        $('#fal_r1').val(fr1);
                        if (dat[5] == '') {
                            $('#prod1').val('0');
                            $('#fal1').val('0');
                        }
                        if (dat[6] == '') {
                            $('#prod_r1').val('0');
                            $('#fal_r1').val('0');
                        }
                    } else {
                        alert('Orden no existe');
                        $('#opl_numero').focus();
                    }
                });
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function validar() {
                pp = $('#rpl_peso_primario').val();
                s1 = $('#sol1').val();
                rp = $('#rpl_rollo_primario').val();
                r1 = $('#sol_r1').val();
                pp1 = $('#prod1').val();
                pr1 = $('#prod_r1').val();
                mp =<?php echo $rst[rpl_peso] ?>;
                mr1 =<?php echo $rst[rpl_rollo] ?>;
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
                    $("#rpl_peso_primario").css({borderColor: "red"});
                    $("#rpl_peso_primario").focus();
                    $("#rpl_peso_primario").val('');
                    return false;
                }
                if (parseFloat(sr1) > parseFloat(r1)) {
                    alert('La suma de los rollos producidos y faltantes del \n primer producto es mayor al solicitado');
                    $("#rpl_rollo_primario").css({borderColor: "red"});
                    $("#rpl_rollo_primario").focus();
                    $("#rpl_rollo_primario").val('');
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
                            $('#rpl_peso_primario').focus();
                            break;
                        case 1:
                            $('#rpl_rollo_primario').focus();
                            break;
                    }
                }
            }
            
            function bloquear(){
                 $('#opl_numero').attr('disabled','disabled');
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
        <div id="proceso" >   
        </div>
        <div id="cargando"></div>

        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >REGISTRO DE PRODUCCION PLUMON<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>REGISTRO #:</td>
                    <td>
                        <input type="text" size="15" id="rpl_numero" readonly value="<?php echo $rst[rpl_numero] ?>" />
                    </td>
                </tr>
                <tr>
                    <td>ORDEN#:</td>
                    <td>
                        <input type="text" size="15" id="opl_numero" value="<?php echo $rst[orp_num_pedido] ?>" onchange="load_orden(this)" onblur="bloquear()"/>
                        <input type="hidden" size="10" id="opl_id" readonly value="<?php echo $rst[orp_id] ?>" />
                    </td>
                    <td>FECHA:</td>
                    <td>
                        <input type="text" size="15" id="rpl_fecha"  value="<?php echo $rst[rpl_fecha] ?>" />
                        <img src="../img/calendar.png" id="im-rpl_fecha" />
                    </td>
                </tr>
                <tr>
                    <td># OPERADOR:</td>
                    <td><input type="text" size="15"  id="rpl_operador" value="<?php echo $rst[rpl_operador] ?>" /></td>
                    <td>DESPERDICIO:</td>
                    <td><input type="text" size="15"  id="rpl_desperdicio" value="<?php echo $rst[rpl_desperdicio] ?>" onkeypress="tab(event, 0)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
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
                        <input type="text" size="8" id="sol1" value="<?php echo $totkg ?>" readonly />kg
                    </td>
                    <td>
                        <input type="text" size="8" id="prod1" value="<?php echo $rst_prod[peso] ?>" readonly/>kg
                    </td>
                    <td>
                        <input type="text" size="8" id="fal1" value="<?php echo $f1 ?>" readonly/>kg
                    </td>
                    <td>
                        <input type="text" size="10" id="rpl_peso_primario" value="<?php echo $rst[rpl_peso] ?>" onchange="validar()" onkeypress="tab(event, 1)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />kg
                    </td>
                </tr>
                <tr>
                    <td># CANTIDAD</td>
                    <td>
                        <input type="text" size="8" id="sol_r1" value="<?php echo $rst[orp_cantidad] ?>" readonly/>
                    </td>
                    <td>
                        <input type="text" size="8" id="prod_r1" value="<?php echo $rst_prod[rollo] ?>" readonly/>
                    </td>
                    <td>
                        <input type="text" size="8" id="fal_r1" value="<?php echo $fr1 ?>" readonly/>
                    </td>
                    <td>
                        <input type="text" size="10" id="rpl_rollo_primario" value="<?php echo $rst[rpl_rollo] ?>" onchange="validar()" onkeypress="tab(event, 2)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />
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
