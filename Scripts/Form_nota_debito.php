<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nota_debito.php';
$Clase_nota_debito = new Clase_nota_debito();
$emisor;
if ($emisor >= 10) {
    $ems = '0' . $emisor . '-';
} else {
    $ems = '00' . $emisor . '-';
}

$id = $_GET[id];
$x = $_GET[x];
if ($id != '') {
    $rst = pg_fetch_array($Clase_nota_debito->lista_una_nota_debito_id($id));
    $det = 1;
    $comprobante = $rst[ndb_numero];
    $cns = $Clase_nota_debito->lista_detalle_nota($id);
} else {
    $det = 0;
    $id = '0';
    $rst = pg_fetch_array($Clase_nota_debito->lista_una_factura_id($x));
    $rst_sec = pg_fetch_array($Clase_nota_debito->lista_secuencial_nota_debito($emisor));
    if (empty($rst_sec)) {
        $sec = 1;
    } else {
        $se = explode('-', $rst_sec[ndb_numero]);
        $sec = ($se[2] + 1);
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
    $comprobante = $ems . '001-' . $tx . $sec;
    $rst[ndb_num_comp_modifica] = $rst[fac_numero];
    $rst[ndb_fecha_emision] = date('Y-m-d');
    $rst[ndb_fecha_emi_comp] = $rst[fac_fecha_emision];
    $rst[ndb_nombre] = $rst[fac_nombre];
    $rst[ndb_identificacion] = $rst[fac_identificacion];
    $rst[ndb_email] = $rst[fac_email];
    $rst[ndb_direccion] = $rst[fac_direccion];
    $rst[ndb_telefono] = $rst[fac_telefono];
    $rst[fac_id] = $rst[fac_id];
    $rst[cli_id] = $rst[cli_id];
}
$rst_ven = pg_fetch_array($Clase_nota_debito->lista_vendedor(strtoupper($rst_user[usu_person])));
$ven_id = $rst_ven[vnd_id];
$vendedor = strtoupper($rst_user[usu_person]);
$descuento == '0';
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
            var usu =<?php echo $emisor ?>;
            var num = '<?php echo $num_not_credito ?>';
            var det = '<?php echo $det ?>';
            var vendedor = '<?php echo $vendedor ?>';
            var vnd = '<?php echo $ven_id ?>';
            var comp = '<?php echo $comprobante ?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#detalle').find("tbody tr:last");
                    var a = tr.find("input").attr("lang");
                    if ($('#descripcion' + a).val().length != 0) {
                        if (this.lang == 0) {
                            clona_fila($('#detalle'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                if (det != 0) {
                    calculo();
                }
            });
            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    if (parts[1] != 'item') {
                        this.value = '';
                    }

                    ;
                    this.lang = x;
                    return parts[1] + x;
                });
                tr.find("select").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    if (parts[1] != 'item') {
                        this.value = '';
                    }
                    ;
                    this.lang = x;
                    return parts[1] + x;
                });
                $('#detalle').find("tbody tr:last").after(tr);
                $('#descripcion' + x).focus();
                $('#cantidad' + x).val('0');
                $('#item' + x).attr('lang', x);
            }
//====================================================================================================================================================

            function save() {
                email_cliente = '<?php echo $rst[ndb_email] ?>';
                var data = Array(
                        cli_id.value,
                        vnd,
                        usu,
                        num_comprobante.value,
                        motivo.value,
                        fecha_emision.value,
                        nombre.value,
                        identificacion.value,
                        email_cliente,
                        direccion_cliente.value,
                        '1',
                        num_secuencial.value,
                        fecha_emision_comprobante.value,
                        subtotal12.value,
                        subtotal0.value,
                        subtotalex.value,
                        subtotalno.value,
                        '0',
                        total_iva.value,
                        fac_id.value,
                        total_valor.value,
                        telefono_cliente.value,
                        '0',
                        '0',
                        subtotal.value);
                var data1 = Array();
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;

                while (n < i) {
                    n++;
                    if ($('#cantidad' + n).val() != null) {
                        tipo_comprobante = 5;
                        cantidad = $("#cantidad" + n).val().replace(',', '');
                        descripcion = $("#descripcion" + n).val();
                        data1.push(
                                '0' + '&' +
                                descripcion + '&' +
                                cantidad + '&' +
                                '0&' +
                                '0&' +
                                '0&' +
                                '0&'
                                )
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
//                        Validaciones antes de enviar
                        if ($('#motivo').val() == 0) {
                            $('#motivo').css({borderColor: "red"});
                            $('#motivo').focus();
                            return false;
                        }
                        n = 0;
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#cantidad' + n).val() != null) {
                                    if ($('#descripcion' + n).val() == 0) {
                                        $('#descripcion' + n).css({borderColor: "red"});
                                        $('#descripcion' + n).focus();
                                        return false;
                                    }
                                    if ($('#cantidad' + n).val() == 0) {
                                        $('#cantidad' + n).css({borderColor: "red"});
                                        $('#cantidad' + n).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        if (det != 0) {
                            if (comp != num_comprobante.value) {
                                alert('No se puede modificar numero de Nota de Debito');
                                $('#num_comprobante').css({borderColor: "red"});
                                $('#num_comprobante').focus();
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
                    url: 'actions_nota_debito.php',
                    data: {op: 0, 'data[]': data, 'data1[]': data1, id: id, 'fields[]': fields, x: det}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            cancelar();
                        } else if (dt == 1) {
                            alert('Numero Secuencial de la Nota de Debito ya existe \n Debe hacer otra Nota de Debito con otro Secuencial');
                            cancelar();
                        } else if (dt == 2) {
                            alert('Una de las cuentas de la Nota de Debito esta inactiva');
                            loading('hidden');
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }
//====================================================================================================================================================

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_debito.php';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function elimina_fila(obj) {
                itm = $('.itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                    calculo();
                } else {
                    alert('No puede eliminar todas las filas');
                }
            }

            function calculo() {
//                i = $('.itm').length;
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                var t12 = 0;
                var t0 = 0;
                var tex = 0;
                var tno = 0;
                var tiva = 0;
                var irbpnr = 0;
//                var ice = 0;
                var gtot = 0;
                if (st1.checked == true) {
                    ob = '12';
                } else if (st2.checked == true) {
                    ob = '0';
                } else if (st3.checked == true) {
                    ob = 'NO';
                } else if (st4.checked == true) {
                    ob = 'EX';
                }

                while (n < i) {
                    n++;
                    if ($('#item' + n).val() == null) {
//                        ob = 0;
                        val = 0;
                    } else {
//                        ob = $('#iva' + n).val();
                        val = $('#cantidad' + n).val().replace(',', '');
                    }

                    if (ob == '12') {
                        t12 = (t12 * 1 + val * 1);
                        tiva = ((t12 * 1) * 12 / 100);
                    }
                    if (ob == '0') {
                        t0 = (t0 * 1 + val * 1);
                    }
                    if (ob == 'EX') {
                        tex = (tex * 1 + val * 1);
                    }
                    if (ob == 'NO') {
                        tno = (tno * 1 + val * 1);
                    }
                }
                st = (t12 * 1) + (t0 * 1) + (tex * 1) + (tno * 1);
                gtot = (t12 * 1 + t0 * 1 + tex * 1 + tno * 1 + tiva * 1);
                $('#subtotal12').val(t12.toFixed(2));
                $('#subtotal0').val(t0.toFixed(2));
                $('#subtotalex').val(tex.toFixed(2));
                $('#subtotalno').val(tno.toFixed(2));
                $('#subtotal').val(st.toFixed(2));
//                $('#total_ice').val(ice.toFixed(2));
                $('#irbpnr').val(irbpnr.toFixed(2));
                $('#total_iva').val(tiva.toFixed(2));
                $('#total_valor').val(gtot.toFixed(2));
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
            .head{
                text-align: center;
                height:22px;
            }
            select{
                width: 80px;
            }
            .totales td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
        </style>
    </head>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form" >

                <thead>
                    <tr><th colspan="12" >NOTA DE DEBITO<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>   
                <tr><td><table>
                            <tr>
                                <td width="108">NOTA DE DEBITO NO:</td>                    
                                <td><input type="text" size="30"  id="num_comprobante" readonly value="<?php echo $comprobante ?>"  /></td> 
                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="20"  id="fecha_emision"  value="<?php echo $rst[ndb_fecha_emision] ?>" /><img src="../img/calendar.png" id="im-campo1" readonly/></td>
                            </tr>         
                            <tr>
                                <td width="108">FACTURA NO:</td>                    
                                <td><input type="text" size="30"  id="num_secuencial" readonly value="<?php echo $rst[ndb_num_comp_modifica] ?>"  />
                                    <input type="hidden" size="10"  id="fac_id" readonly value="<?php echo $rst[fac_id] ?>"  /></td>
                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="20"  id="fecha_emision_comprobante" readonly value="<?php echo $rst[ndb_fecha_emi_comp] ?>" /></td>
                            </tr>         
                            <tr>
                                <td>CLIENTE :</td>
                                <td><input type="text" size="30"  id="nombre" readonly value="<?php echo $rst[ndb_nombre] ?>"  />
                                    <input type="hidden" size="10"  id="cli_id" readonly value="<?php echo $rst[cli_id] ?>"  /></td>
                                <td>CI/RUC :</td>
                                <td><input type="text" size="20"  id="identificacion" readonly value="<?php echo $rst[ndb_identificacion] ?>" /></td>
                            </tr>
                            <tr>
                                <td>DIRECCION :</td>
                                <td><input type="text" size="30"  id="direccion_cliente" readonly value="<?php echo $rst[ndb_direccion] ?>"  /></td>
                                <td>TELÉFONO:</td>
                                <td><input type="text" size="20"  id="telefono_cliente" readonly value="<?php echo $rst[ndb_telefono] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>MOTIVO :</td>
                                <td><input type="text" size="35"  id="motivo" value="<?php echo $rst[ndb_motivo] ?>"  /></td>
                            </tr>
                        </table>
                    </td> 
                </tr>
                <tr>
                    <td>
                        <table id="detalle">
                            <tr id="head">
                            <thead id="tabla">
                            <th>Item</th>
                            <th>Razon de la Modificacion</th>
                            <th>Val. Modificacion</th>   
<!--                            <th>IVA</th>-->
                            <th>Accion</th>
                            </thead>
                            <?php
                            if ($det == '0') {
                                ?>
                                <tr>
                                    <td><input type="text" size="8" id="item1" readonly class="itm" lang="1" value="1"   accept=""style="text-align:right" /></td>
                                    <td><input type="text" size="50" id="descripcion1" value="<?php echo $rst_det[descripcion] ?>" list="productos" /></td>
                                    <td><input type="text" size="17" id="cantidad1" value="<?php echo number_format($rst_det[precio_total], 2) ?>"style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), calculo()" lang="1"/></td>
    <!--                                    <td> <select id="iva1" onchange="calculo()">
                                            <option value="12">12%</option>
                                            <option value="0">0%</option>
                                            <option value="EX">EX</option>
                                            <option value="NO">NO</option>
                                        </select>
                                    </td>-->
                                    <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" /></td>

                                </tr>
                                <?php
                            } else {
                                $n = 0;
                                while ($rst_det = pg_fetch_array($cns)) {
                                    $n++;
                                    ?>

                                    <tr>
                                        <td><input type="text" size="8" id="item<?php echo $n ?>" readonly class="itm" lang="<?php echo $n ?>" value="<?php echo $n ?>"   accept=""style="text-align:right" /></td>
                                        <td><input type="text" size="50" id="descripcion<?php echo $n ?>" value="<?php echo $rst_det[dnd_descripcion] ?>" lang="<?php echo $n ?>"/></td>
                                        <td><input type="text" size="17" id="cantidad<?php echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst_det[dnd_precio_total], 2)) ?>"style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), calculo()" lang="<?php echo $n ?>"/></td>
        <!--                                        <td> <select id="iva<?php echo $n ?>" onchange="calculo()">
                                                <option value="12">12%</option>
                                                <option value="0">0%</option>
                                                <option value="EX">EX</option>
                                                <option value="NO">NO</option>
                                            </select>
                                        </td>-->
                                        <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" /></td>

                                    </tr>

                                    <?php
                                }
                            }
                            ?>
                            <tfoot>
                                <tr>
                                    <td><button id="add_row" onclick="frm_save.lang = 0" >+</button></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal:</td>
                                    <td class="sbtls" ><input type="text" size="15" id="subtotal" readonly  value="<?php echo number_format(0, 2) ?>" style="text-align:right"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal 12%:</td>
                                    <td class="sbtls" ><input type="text" size="15" id="subtotal12" readonly  value="<?php echo number_format(0, 2) ?>"style="text-align:right" /><input type="radio" id="st1" name="st" onclick="calculo()"/></td>
                                    <td> </td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal 0%:</td>
                                    <td class="sbtls" ><input type="text" size="15" readonly  id="subtotal0" value="<?php echo number_format(0, 2) ?>" style="text-align:right" /><input type="radio" id="st2" name="st" onclick="calculo()"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal No Objeto Iva:</td>
                                    <td class="sbtls" ><input type="text" size="15" readonly  id="subtotalno" value="<?php echo number_format(0, 2) ?>" style="text-align:right"/><input type="radio" id="st3" name="st" onclick="calculo()"/></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td colspan="2" align="right">Total Excento Iva:</td>
                                    <td class="sbtls" ><input type="text" size="15" readonly  id="subtotalex" value="<?php echo number_format(0, 2) ?>" style="text-align:right"/><input type="radio" id="st4" name="st" onclick="calculo()"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">IVA 12%:</td>
                                    <td class="sbtls" ><input type="text" size="15" id="total_iva" readonly  value="<?php echo number_format(0, 2) ?>" style="text-align:right"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">IRBPNR:</td>
                                    <td class="sbtls" ><input type="text" size="15" id="irbpnr" readonly  value="<?php echo number_format(0, 2) ?>" style="text-align:right"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Total:</td>
                                    <td class="sbtls"><input type="text" size="15" id="total_valor" readonly  value="<?php echo number_format(0, 2) ?>"  style="text-align:right"/></td>
                                    <td></td>
                                </tr>
                        </table>
                    </td>
                </tr>
                </tfoot>
                <!--</tfoot>-->
                <!------------------------------------->
            </table>
        </form>
        <?PHP
        if ($x != 1) {
            ?> 
            <button id="guardar" onclick="save()">Guardar</button>   
            <?PHP
        }
        ?>
        <button id="cancelar" >Cancelar</button> 
    </body>
</html>    
<script>
    var iva12 = '<?php echo $rst[ndb_subtotal12] ?>';
    var iva0 = '<?php echo $rst[ndb_subtotal0] ?>';
    var ivaex = '<?php echo $rst[ndb_subtotal_ex_iva] ?>';
    var ivano = '<?php echo $rst[ndb_subtotal_no_iva] ?>';
    if (parseFloat(iva12) > 0) {
        $('#st1').attr('checked', true);
    }
    if (parseFloat(iva0) > 0) {
        $('#st2').attr('checked', true);
    }
    if (parseFloat(ivano) > 0) {
        $('#st3').attr('checked', true);
    }
    if (parseFloat(ivaex) > 0) {
        $('#st4').attr('checked', true);
    }
</script>
<?php
echo "<datalist id='productos'>";
$cns_pro = $Clase_nota_debito->lista_productos_fletes();
while ($rst_pro = pg_fetch_array($cns_pro)) {
    echo "<option value='$rst_pro[codigo]'> $rst_pro[lote] $rst_pro[codigo] $rst_pro[descripcion] </option>";
}
echo "</datalist>";
?>