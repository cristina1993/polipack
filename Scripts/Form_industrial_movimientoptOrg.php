<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_movimientopt.php'; // cambiar clsClase_productos
$Clase_industrial_movimientopt = new Clase_industrial_movimientopt();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Clase_industrial_movimientopt->lista_un_ingreso_industrial($id));
    $cns = $Clase_industrial_movimientopt->lista_ingreso_industrial_documento($rst['mov_documento']);
    $x = $_GET[x];
    $fila = pg_numrows($cns);
} else {
    $id = 0;
    $rst['mov_fecha_trans'] = date('Y-m-d');
    $fila = 0;
}
$cns_combo = $Clase_industrial_movimientopt->lista_combo_fabricas_industrial();
$cns_combo1 = $Clase_industrial_movimientopt->lista_combo_fabricas_noperti();
$cns_pro = $Clase_industrial_movimientopt->lista_proveedor(0);
$cns_trans = $Clase_industrial_movimientopt->lista_combo_transacciones();
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
                    var tr = $('#tbl_form').find("tbody tr:last");
                    var a = tr.find("input").attr("id");
                    var i = a.substring(4, 5);
                    if ($('#pro_descripcion' + i).val().length != 0) {
                        if (this.lang == 0) {
                            clona_fila($('#tbl_form'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });

                Calendar.setup({inputField: "mov_fecha_trans", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                if (id == 0) {
                    sec_automatico();
                }
            });

            function save(id) {
                var data = Array();
                doc = document.getElementsByClassName('itm');
                n = 0;
                var tr = $('#tbl_form').find("tbody tr:last");
                var a = tr.find("input").attr("id");
                var i = a.substring(4, 5);
                while (n < i) {
                    n++;
                    pro_id = $('#pro_id' + n).val();
                    can = $('#mov_cantidad' + n).val();
                    trs_id = $('#trs_id').val();
                    data.push(pro_id + '&' +
                            trs_id + '&' +
                            cli_id.value + '&' +
                            mov_documento.value + '&' +
                            mov_guia_transporte.value + '&' +
                            mov_fecha_trans.value + '&' +
                            can + '&' +
                            emisor.value
                            );
                }
                fields = $('#frm_save').serialize();
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        doc = document.getElementsByClassName('itm');

                        n = 0;
                        if (mov_fecha_trans.value.length == 0) {
                            $("#mov_fecha_trans").css({borderColor: "red"});
                            $("#mov_fecha_trans").focus();
                            return false;
                        }
                        else if (trs_id.value == 0) {
                            $("#trs_id").css({borderColor: "red"});
                            $("#trs_id").focus();
                            return false;
                        }
                        else if (emp_id.value == 0) {
                            $("#emp_id").css({borderColor: "red"});
                            $("#emp_id").focus();
                            return false;
                        }

                        else if (cli_nombre.value.length == 0) {
                            $("#cli_nombre").css({borderColor: "red"});
                            $("#cli_nombre").focus();
                            return false;
                        }
                        else if (doc.length != 0) {
                            while (n < doc.length) {
                                n++;
                                if ($('#pro_descripcion' + n).val() == 0) {
                                    $('#pro_descripcion' + n).css({borderColor: "red"});
                                    $('#pro_descripcion' + n).focus();
                                    return false;
                                }
                                if ($('#mov_cantidad' + n).val() == 0) {
                                    $('#mov_cantidad' + n).css({borderColor: "red"});
                                    $('#mov_cantidad' + n).focus();
                                    return false;
                                }
                            }
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_industrial_movimientopt.php',
                    data: {op: 0, 'data[]': data, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            window.history.go(0);
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
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    //this.lang = x;
                    if (parts[1] == 'cantidad') {
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
            }
            function datos(obj) {
                $('#frm_save').attr('lang', 1);
                id = obj.value;
                n = obj.lang;
                var p = obj.id.match(/(\D+)(\d+)$/);
                $('#mov_cantidad' + p[2]).focus();
                $.post('actions_industrial_movimientopt.php', {op: 2, id: id}, function (dt) {
                    dat = dt.split('&');
                    if (dat[1] == 0) {
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                    } else {
                        $('#pro_id' + n).val(dat[0]);
                        $('#pro_codigo' + n).val(dat[1]);
                        $('#pro_descripcion' + n).val(dat[2]);
                        $('#pro_uni' + n).val(dat[3]);
                    }

                })
            }
            function lista_prod(obj) {
                id = obj.value;
                $.post('actions_industrial_movimientopt.php', {op: 3, id: id}, function (dt) {
                    $('#lista_prod').html(dt);
                })
            }

            function sec_automatico() {
                $.post("actions_industrial_movimientopt.php", {op: 4}, function (dt) {
                    mov_documento.value = '001-' + dt;
                })
            }
            function cliente(obj) {
                $.post("actions_industrial_movimientopt.php", {op: 5, id: obj.value}, function (dt) {
                    dat = dt.split('&');
                    if (dat[0] == 0) {
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                    }
                    else {
                        cli_id.value = dat[0];
                        cli_nombre.value = dat[1];
                    }
                })
            }
            function desactivar() {
                if ($("#emp_id").val() != 0) {
                    $("#emp_id").attr('disabled', true)
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
            .head{
                text-align: center;
                height:22px;
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
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form" border="0" >
                <thead>
                    <tr><th colspan="7" >INGRESO DE PRODUCTO TERMINADO NOPERTI</th></tr>
                </thead>
                <tr>
                    <td>Documento No.:</td>
                    <td><input type="text" size="20" id="mov_documento" value="<?php echo $rst['mov_documento'] ?>" readonly/>
                        <input type="hidden"   id="emisor" readonly value="<?php echo $emisor ?>"  />
                    </td>
                    <td>Fecha de Ingreso:</td>
                    <td><input type="text" size="10" id="mov_fecha_trans" value="<?php echo $rst['mov_fecha_trans'] ?>" /><img src="../Img/calendar.png" id="im-campo1"/></td>
                    <td>Guia de Recepcion:</td>
                    <td><input type="text" size="20" id="mov_guia_transporte" value="<?php echo $rst['mov_guia_transporte'] ?>"  /></td>
                </tr>
                <tr>
                    <td>Transaccion:</td>
                    <td> <select id="trs_id">
                            <option value="0">Seleccione</option>
                            <?php
                            while ($rst_tran = pg_fetch_array($cns_trans)) {
                                echo "<option value=$rst_tran[trs_id]>$rst_tran[trs_descripcion]</option>";
                            }
                            ?>  
                        </select>
                    </td>
                    <td>Fabrica:</td>
                    <td> <select id="emp_id" onchange="lista_prod(this), ocultar()" onblur="desactivar()">
                            <option value="0">Seleccione</option>
                            <?php
                            if ($emisor == 1) {
                                while ($rst_combo1 = pg_fetch_array($cns_combo1)) {
                                    echo "<option value='$rst_combo1[emp_id]' >$rst_combo1[emp_descripcion]</option>";
                                }
                            } else {
                                while ($rst_combo = pg_fetch_array($cns_combo)) {
                                    echo "<option value='$rst_combo[emp_id]' >$rst_combo[emp_descripcion]</option>";
                                }
                            }
                            ?>  
                        </select>
                    </td>
                    <td  colspan="">Proveedor:</td>
                    <td  colspan="2"><input type="text" size="40" id="cli_nombre" onblur="cliente(this)" value="<?php echo $rst['cli_raz_social'] ?>" list="lista_cliente"/>
                        <input type ="text" size="20"  id="cli_id"  value="" hidden /></td>
                </tr>
                <thead id="tabla">
                <th>Item</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Acciones</th>
                </thead>
                <?php
                if ($fila == "0") {
                    ?>
                    <tr>
                        <td align="right"><input type ="text" size="4"  class="itm" id="item1"  readonly value="1"/>
                            <input type ="text" size="20"  id="pro_id1"  value="" lang="1" hidden/></td>
                        <td><input type ="text" size="30"  id="pro_codigo1"  value="" lang="1" onchange="datos(this)"  list="lista_prod"/></td>
                        <td><input type="text" size="30" id="pro_descripcion1" value="" lang="1"  readonly/></td>
                        <td><input type ="text" size="10"  id="pro_uni1"  value="" lang="1" readonly/></td>
                        <td style="display:none"><input type ="text" size="10"  id="pro_uni21"  value="" lang="1" readonly/></td>
                        <td><input type ="text" size="10"  id="mov_cantidad1"  value="" lang="1" class="cnt" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), total()"/></td>
                        <td onclick="elimina_fila(this)" ><img class="auxBtn" src="../img/b_delete.png" /></td>
                    </tr>
                    <?PHP
                } else {
                    $n = 0;
                    $suma = 0;
                    while ($rst1 = pg_fetch_array($cns)) {
                        $n++;
                        ?>
                        <tr>
                            <td align="right"><input type ="text" size="4"  id="item1"  readonly value="<?PHP echo $n ?>"/></td>
                            <td><input type ="text" size="10"  id="pro_codigo1"  value="<?php echo $rst1['pro_codigo'] ?>" lang="1" readonly/></td>
                            <td><input type="text" size="30" id="pro_descripcion1" value="<?php echo $rst1['pro_descripcion'] ?>" lang="1"/></td>
                            <td><input type ="text" size="10"  id="pro_uni1"  value="<?php echo $rst1['pro_uni'] ?>" lang="1" readonly/></td>
                            <td style="display:none"><input type ="text" size="10"  id="pro_uni2"  value="<?php echo $rst1['pro_uni2'] ?>" lang="1" readonly/></td>
                            <td><input type ="text" size="10"  id="mov_cantidad1"  value="<?php echo $rst1['mov_cantidad'] ?>" lang="1" onblur="total()"/></td>
                        </tr>
                        <?PHP
                        $suma = $suma + $rst1['mov_cantidad'];
                    }
                }
                ?>

                <tfoot>
                    <tr class="totales">
                        <td><button id="add_row">+</button></td>
                        <td colspan="4" align="right">Total:</td>
                        <?PHP
                        if ($fila != "0") {
                            ?>
                            <td align="right" style="font-size:15px; " id="total"><?php echo $suma ?></td>
                            <?PHP
                        } else {
                            ?>
                            <td align="right" style="font-size:15px; " id="total">0</td>
                            <?PHP
                        }
                        ?>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </form>
        <?PHP
        if ($x != 1) {
            ?> 
            <button id="guardar" onclick="save(<?php echo $id ?>, 0)">Guardar</button>   
            <?PHP
        }
        ?>
        <button id="cancelar" >Cancelar</button>    
    </body>
</html>    

<datalist id="lista_prod">
</datalist>
<datalist id="lista_cliente">
    <?php
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        echo "<option value='$rst_pro[cli_id]' >$rst_pro[nombres]</option>";
    }
    ?>
</datalist>
<script>
    var emp_id = '<?php echo $rst[emp_id] ?>';
    $('#emp_id').val(emp_id);
    var trs_id = '<?php echo $rst[trs_id] ?>';
    $('#trs_id').val(trs_id);
</script>