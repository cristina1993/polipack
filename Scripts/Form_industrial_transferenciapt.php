<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_ingresopt.php'; // cambiar clsClase_productos
include_once '../Clases/clsClase_industrial_movimientopt.php'; // cambiar clsClase_productos
$Clase_industrial_movimientopt = new Clase_industrial_movimientopt();
$Clase_industrial_ingresopt = new Clase_industrial_ingresopt();
$fec1 = $_GET[fecha1];
$fec2 = $_GET[fecha2];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $bod = $_GET[bod];
    if ($bod == 10) {
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_ingreso_industrial($id));
        $rst_bod = pg_fetch_array($Clase_industrial_ingresopt->lista_una_transferencia($rst[mov_documento]));
        $cns = $Clase_industrial_ingresopt->lista_det_transferencia_industrial($rst[mov_documento]);
        $secuencial = $rst[mov_documento];
    } else {
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_ingreso_comercial($id));
        $rst_bod = pg_fetch_array($Clase_industrial_ingresopt->lista_una_transferencia($rst[mov_documento]));
        $cns = $Clase_industrial_ingresopt->lista_det_transferencia_comercial($rst[mov_documento]);
        $secuencial = $rst[mov_documento];
    }
} else {
    $id = 0;
    $rst['mov_fecha_trans'] = date('Y-m-d');
    $fila = 0;
    $secuencial = $_GET[sec];
    $cns_loc = $Clase_industrial_ingresopt->lista_locales();
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
            var emi =<?php echo $emisor ?>;
            $(function () {

                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar(id);
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#tbl_form').find("tbody tr:last");
                    var a = tr.find("input").attr("lang");
                    var i = parseInt(a);
                    if ($('#pro_descripcion' + i).val().length != 0 && (parseFloat($('#mov_cantidad' + i).val()) != 0 || $('#mov_cantidad' + i).val().length != 0) && (parseFloat($('#inventario' + i).val()) > 0 && $('#inventario' + i).val().length != 0)) {
                        if (this.lang == 0) {
                            clona_fila($('#tbl_form'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });
                Calendar.setup({inputField: "mov_fecha_trans", ifFormat: "%Y-%m-%d", button: "im-mov_fecha_trans"});
                if (id == 0) {
//                    seccion_auto();
                    $("#factura").hide();
                    $("#guardar").show();
                    $('#mov_cantidad1').val('0');
                } else {
                    $("#mov_fecha_trans").attr('disabled', true);
                    $("#mov_guia_transporte").attr('disabled', true);
                    $("#org").attr('disabled', true);
                    $("#des").attr('disabled', true);
                }
                parent.document.getElementById('contenedor2').rows = "*,80%";

                $('input[type=text][readonly]').change(function () {
                    accion_incorrecta(this);
                });

            });

            function accion_incorrecta(camp) {
                $.post("../Validate/bloquea_usuario.php", {ac: 0, mod: 'Transferencias', doc: mov_documento.value, camp: camp.id},
                function (dt) {
                    if (dt == 0) {
                        window.top.location.href = '../Validate/closeSession.php';
                        //window.location = '../Validate/closeSession.php';
                    } else {
                        alert(dt);
                    }

                });

            }


            function save(id, x) {
                var data1 = Array();
                var data2 = Array();
                var fields = Array();
                $('.itm').each(function () {
                    pro = $('#pro_id' + this.value).val();
                    trs_egr = 20;
                    trs_ing = 4;
                    bod_org = $('#org_bod').val();
                    cli_org = $('#org_cli').val();
                    bod_des = $('#des_bod').val();
                    cli_des = $('#des_cli').val();
                    doc = $('#mov_documento').val();
                    gui = $('#mov_guia_transporte').val();
                    fch = $('#mov_fecha_trans').val();
                    cnt = $('#mov_cantidad' + this.value).val();
                    tbl = $('#tbl' + this.value).val();
                    data1.push(pro + '&' + trs_egr + '&' + cli_des + '&' + bod_org + '&' + doc + '&' + gui + '&' + fch + '&' + cnt + '&' + tbl);
                    data2.push(pro + '&' + trs_ing + '&' + cli_org + '&' + bod_des + '&' + doc + '&' + gui + '&' + fch + '&' + cnt + '&' + tbl);
                });

                $('#frm_save').find(':input').each(function () {
                    var elemento = this;
                    f = elemento.id + "=" + elemento.value;
                    fields.push(f);
                });
                $.ajax({
                    beforeSend: function () {
                        var v = 0;

                        if (org.value.length == 0) {
                            $("#org").css({borderColor: "red"});
                            $("#org").focus();
                            return false;
                        } else if (des.value.length == 0) {
                            $("#des").css({borderColor: "red"});
                            $("#des").focus();
                            return false;
                        } else if (org_bod.value === des_bod.value) {
                            $("#des").css({borderColor: "red"});
                            $("#des").focus();
                            $("#org").css({borderColor: "red"});
                            return false;
                        } else {
                            $('.itm').each(function () {
                                cod = $('#pro_codigo' + this.value).val();
                                cnt = $('#mov_cantidad' + this.value).val();
                                inv = $('#inventario' + this.value).val();
                                if (cod.length == 0) {
                                    $('#pro_codigo' + this.value).css({borderColor: "red"});
                                    $('#pro_codigo' + this.value).focus();
                                    v = 1;
                                    return false;
                                }
                                if (cnt.length == 0 || parseFloat(cnt) == 0) {
                                    $('#mov_cantidad' + this.value).css({borderColor: "red"});
                                    $('#mov_cantidad' + this.value).focus();
                                    v = 1;
                                }
                                if (parseFloat(inv) < parseFloat(cnt)) {
                                    alert('NO SE PUEDE REGISTRAR LA CANTIDAD \n ES MAYOR QUE EL INVENTARIO');
                                    $('#mov_cantidad').val('');
                                    $('#mov_cantidad').focus();
                                    $('#mov_cantidad').css({borderColor: "red"});
                                    v = 1;
                                }
                            });
                            if (v == 1) {
                                return false;
                                $('#guardar').show();
                            } else {
                                return true;
                                $('#guardar').hide();
                                loading('visible');
                            }

                        }
                    },
                    type: 'POST',
                    url: 'actions_industrial_ingresopt.php',
                    data: {op: 11, 'data1[]': data1, 'data2[]': data2, 'fields[]': fields, id: id, x: x},
                    success: function (dt) {
                        loading('hidden');
                        if (dt == 0) {
                            imprimir();
                        } else {
                            alert(dt);
                        }
                    }
                })

            }
            function cancelar(a) {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                if (id != 0) {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_transferenciapt.php?fecha1=' + '<?php echo $fec1 ?>' + '&fecha2=' + '<?php echo $fec2 ?>';
                }
            }
            
            function imprimir() {
                $('#usr').show();
                window.print();
                $('#usr').hide();
                cancelar();
            }
            
            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] != 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] != 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    ;
                    return parts[1] + x;
                });
                $(table).find("tbody tr:last").after(tr);
                $('#pro_descripcion' + x).focus();
                $('#mov_cantidad' + x).val('0');

            }
            function elimina_fila(obj) {
                itm = $('.itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
                total();
            }
            function datos(obj) {
                $('.itm').each(function () {
                    pro = $('#pro_id' + this.value).val();
                    pro2 = obj.value.substring(1, 100);
                    $(obj).css({borderColor: ""});
                    if (pro2 == pro) {
                        alert('Producto ya ingresado');
                        $(obj).val('');
                        $(obj).focus();
                        return false;
                    }
                });
                $.post('actions_industrial_ingresopt.php', {op: 10, id: obj.value, s: org_bod.value}, function (dt) {
                    dat = dt.split('&');
                    if (dat[1] == 0) {
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                    } else {
                        $('#pro_id' + obj.lang).val(dat[0]);
                        $('#pro_codigo' + obj.lang).val(dat[1]);
                        $('#pro_lote' + obj.lang).val(dat[2]);
                        $('#pro_descripcion' + obj.lang).val(dat[3]);
                        $('#pro_uni' + obj.lang).val(dat[4]);
                        $('#tbl' + obj.lang).val(dat[5]);
                        $('#inventario' + obj.lang).val(dat[6]);
                        $('#codaux' + obj.lang).val(dat[7]);
                        $('#mov_cantidad' + obj.lang).focus();
                        $('#mov_cantidad' + obj.lang).select();
                    }
                })
            }
            function lista_producto(obj) {
                id = obj.value;
                $("#factura").hide();
                $("#guardar").show();
                if ($("#emp_id").val() == 1) {
                    $.post('actions_industrial_ingresopt.php', {op: 7}, function (dt) {
                        $('#lista_producto').html(dt);
                    })
                } else if ($("#emp_id").val() == 2) {
                    $.post('actions_industrial_ingresopt.php', {op: 8}, function (dt) {
                        $('#lista_producto').html(dt);
                    })
                } else {
                    $.post('actions_industrial_ingresopt.php', {op: 3, id: id, ems: emisor.value}, function (dt) {
                        $('#lista_producto').html(dt);
                    })
                }
            }

//            function seccion_auto() {
//                $.post("actions_industrial_ingresopt.php", {op: 4}, function (dt) {
//                    mov_documento.value = '001-' + dt;
//                })
//            }

            function cliente(obj) {

                $.post("actions_industrial_ingresopt.php", {op: 9, id: obj.value}, function (dt) {
                    dat = dt.split('&');
                    if (dat[0] == 0) {
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                    } else {
                        $('#' + obj.id + '_bod').val(dat[0]);
                        $('#' + obj.id + '_cli').val(dat[1]);
                        obj.value = dat[2];
                    }

                });

            }

            function bloquear() {
                if ($("#emp_id").val() != 0) {
                    $("#emp_id").attr('disabled', true)
                }
            }

            function lista_prod(obj) {
                $(obj).attr('disabled', true);
                $.post("actions_industrial_ingresopt.php", {op: 13, s: obj.value}, function (dt) {
                    $("#lista_producto").html(dt);
                })
            }

            function inventario(obj) {
                if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
                    alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    total();
                }
            }

            function total() {
                doc = document.getElementsByClassName('itm');
                n = 0;
                sum = 0;
                while (n < doc.length) {
                    n++;
                    if ($('#mov_cantidad' + n).val().length == 0) {
                        can = 0;
                    } else {
                        can = $('#mov_cantidad' + n).val()
                    }
                    sum = sum + parseFloat(can);
                }

                $('#total').html(sum);
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

            #descripcion{
                width: 150px;
            }
            #emp_id{
                width: 140px;
            }
            .add td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
            *{
                font-size: 10px;
            }

            #txt_salir{
                width:24px;
                font-size:18px;  
                font-weight:bolder; 
                padding:3px; 
                border-radius:2px; 
                background: linear-gradient(to bottom, #f0b7a1 0%,#8c3310 50%,#752201 51%,#bf6e4e 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0b7a1', endColorstr='#bf6e4e',GradientType=0 ); /* IE6-9 */
                position:fixed; 
                cursor:pointer; 
                color:white;
                font-weight:bolder; 
            }
            #txt_salir:hover{
                color:#D8000C; 

            }            
            .auxBtn{
                width:14px; 
                padding:2px; 
            }
            table{
                border-collapse:collapse; 
            }
            #usr{
                float:right; 
                margin-right:20px; 
                text-transform:uppercase;
                display:none; 
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form" border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th colspan="8" ><?PHP echo 'TRANSFERENCIA DE PRODUCTO TERMINADO ' ?>
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>  
                            <font id="usr"><?php echo $_SESSION[usuario] ?></font>                            
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="6" align="left" >Documento :&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" size="24"  id="mov_documento" readonly value="<?php echo $secuencial ?>"  />
                        <input type="hidden"   id="emisor" readonly value="<?php echo $emisor ?>"  />
                        &nbsp;&nbsp;Fecha :
                        &nbsp;<input type="text" size="20" name="fecha1" id="mov_fecha_trans"  value="<?php echo $rst['mov_fecha_trans'] ?>"/>
                        <img src="../img/calendar.png" id="im-mov_fecha_trans"/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Doc/Trans :
                        <input type="text" size="30"  id="mov_guia_transporte" value="<?php echo $rst['mov_guia_transporte'] ?>"  />
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="left">Transaccion :
                        <select id="trs_id" style="width:160px;" disabled>
                            <option value="20">EGRESO TRANSFERENCIA</option>
                        </select>
                        &nbsp;Origen :
                        <input type="text" size="30"  id="org" onchange="cliente(this)" onblur="lista_prod(this)" value="<?php echo $rst_bod['cli_raz_social'] ?>" list="lista_locales"/>
                        <input type ="hidden" size="20" id="org_cli"  value="" />
                        <input type ="hidden" size="20" id="org_bod"  value="" />
                        &nbsp;&nbsp;Destino :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" size="30" id="des" onchange="cliente(this)" value="<?php echo $rst['cli_raz_social'] ?>" list="lista_locales"/>
                        <input type ="hidden" size="20"  id="des_cli"  value="" />
                        <input type ="hidden" size="20"  id="des_bod"  value="" />
                    </td>
                </tr>
                <thead id="tabla">
                    <tr>
                        <th>Item</th>
                        <th>Codigo</th>
                        <th>Lote</th>
                        <th>Descripcion</th>
                        <th>Inventario</th>
                        <th>Cod.Auxiliar</th>
                        <th>Cantidad</th>
                        <?php
                        if ($id == 0){
                        ?>
                        <th>Acciones</th>
                        <?php
                        }
                        ?>
                    </tr>
                </thead>
                <?php
                $n = 0;
                if ($id == 0) {
                    ?>
                    <tr>
                        <td>
                            <input type="text" size="5" class="itm" id="item1" name="item1" lang="1" readonly value="1"/>
                            <input type ="hidden" size="20"  id="pro_id1"  value="" lang="1" />
                            <input type ="text" size="20"  id="tbl1"  value="" lang="1" hidden/>
                        </td>
                        <td><input type ="text" size="20"  id="pro_codigo1" onfocus="this.style.width = '600px'" onblur="this.style.width = '120px'" value="" lang="1" onchange="datos(this)" list="lista_producto"/></td>
                        <td><input type ="text" size="20"  id="pro_lote1"  value="" lang="1" readonly /></td>
                        <td><input type="text" size="60"  id="pro_descripcion1" value="" lang="1" readonly  style="font-weight: 100"/></td>
                        <td><input type ="text" size="8"  id="inventario1"  value="" lang="1" readonly /></td>
                        <td><input type ="text" size="15"  id="codaux1"  value="" lang="1" readonly/></td>
                        <td style="display:none "><input type="text" size="10" id="pro_uni1" value="" lang="1" readonly/></td>
                        <td><input type="text" size="10" id="mov_cantidad1" name="mov_cantidad1" value="" lang="1" class="cnt" onblur="total()" onblur="inventario(this)"/></td>
                        <td onclick="elimina_fila(this)" ><img class="auxBtn"  src="../img/del_reg.png" /></td>
                    </tr>
                    <?php
                } else {
                    while ($rst_trans = pg_fetch_array($cns)) {
                        $n++;
                        if ($bod == 10) {
                            $codigo = $rst_trans[pro_codigo];
                            $lote = '';
                            $descripcion = $rst_trans[pro_descripcion];
                            $codaux = '';
                            $cantidad = $rst_trans[mov_cantidad];
                        } else {
                            $codigo = $rst_trans[pro_a];
                            $lote = $rst_trans[pro_ac];
                            $descripcion = $rst_trans[pro_b];
                            $codaux = $rst_trans[pro_ad];
                            $cantidad = $rst_trans[mov_cantidad];
                        }
                        $rst_pro = pg_fetch_array($Clase_industrial_ingresopt->lista_un_producto_noperti_cod_lote($codigo, $lote));
                        if (empty($rst_pro)) {
                            $rst_pro = pg_fetch_array($Clase_industrial_ingresopt->lista_un_producto_industrial($codigo));
                            $pro_id = $rst_pro[pro_id];
                            $tab = 0;
                        } else {
                            $pro_id = $rst_pro[id];
                            $tab = 1;
                        }
                        $rst_inv = pg_fetch_array($Clase_industrial_ingresopt->total_ingreso_egreso_fac($pro_id, $bod, $tab));
                        $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                        ?>
                        <tr>
                            <td>
                                <input type="text" size="5" class="itm" id="<?php echo 'item' . $n ?>" name="item" lang="<?php echo $n ?>" readonly value="<?php echo $n ?>"/>
                                <input type ="hidden" size="20"  id="pro_id1"  value="" lang="1" />
                                <input type ="text" size="20"  id="tbl1"  value="" lang="1" hidden/>
                            </td>
                            <td><input type ="text" size="20"  id="<?php echo 'pro_codigo' . $n ?>" onfocus="this.style.width = '600px'" onblur="this.style.width = '120px'" value="<?php echo $codigo ?>" lang="<?php echo $n ?>" onchange="datos(this)" readonly list="lista_producto"/></td>
                            <td><input type ="text" size="20"  id="<?php echo 'pro_lote' . $n ?>"  value="<?php echo $lote ?>" lang="<?php echo $n ?>" readonly /></td>
                            <td><input type="text" size="60"  id="<?php echo 'pro_descripcion' . $n ?>" value="<?php echo $descripcion ?>" lang="<?php echo $n ?>" readonly  style="font-weight: 100"/></td>
                            <td><input type ="text" size="8"  id="<?php echo 'inventario' . $n ?>"  value="<?php echo $inv ?>" lang="<?php echo $n ?>" readonly /></td>
                            <td><input type ="text" size="15"  id="<?php echo 'codaux' . $n ?>"  value="<?php echo $codaux ?>" lang="<?php echo $n ?>" readonly/></td>
                            <td style="display:none "><input type="text" size="10" id="pro_uni1" value="" lang="<?php echo $n ?>" readonly/></td>
                            <td><input type="text" size="10" id="<?php echo 'mov_cantidad' . $n ?>" name="<?php echo 'mov_cantidad' . $n ?>" value="<?php echo $cantidad ?>" lang="<?php echo $n ?>" class="cnt" onblur="total()" onblur="inventario(this)" readonly /></td>
<!--                            <td onclick="elimina_fila(this)" ><img class="auxBtn"  src="../img/del_reg.png" /></td>-->
                        </tr>
                        <?php
                        $total = $total + $cantidad;
                    }
                }
                ?>
                <tfoot>
                    <tr class="add">
                        <td>
                            <?PHP
                            if ($x != 1) {
                                ?> 
                                <button id="add_row">+</button>
                                <?PHP
                            }
                            ?>
                        </td>
                        <td colspan="5" align="right">Total:</td>
                        <td align="right" style="font-size:15px; " id="total"><?php echo number_format($total, 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </form>
        <?php
        if ($x != 1) {
            ?>
            <button id="guardar" onclick="save(0, 0)">Guardar</button>   
            <button id="cancelar" >Cancelar</button>   
            <?php
        }
        if ($x == 1) {
            ?>
            <button id="imprimir" onclick="imprimir()">Imprimir</button> 
            <?php
        }
        ?>
    </body>
</html>
<datalist id="lista_producto" >
    //<?php
//    $cns_pro = $Clase_industrial_ingresopt->lista_productos_total();
//    while ($rst_pro = pg_fetch_array($cns_pro)) {
//        echo "<option value='$rst_pro[tbl]$rst_pro[id]' >$rst_pro[lote] $rst_pro[codigo]  $rst_pro[descripcion]</option>";
//    }
//    
    ?>
</datalist>
<datalist id="lista_locales">
    <?php
    while ($rst_loc = pg_fetch_array($cns_loc)) {
        echo "<option value='$rst_loc[cod_punto_emision]' >$rst_loc[nombre_comercial]</option>";
    }
    ?>
</datalist>
