<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_semielaborado_ingreso.php'; // cambiar clsClase_productos
$Ing = new Clase_semielaborado_ingreso();

if (isset($_REQUEST[mov_documento])) {
    $cod = $_REQUEST[mov_documento];
} else {
    $cod = $_GET[sec];
}
$cns_pro = $Ing->lista_productos_total($emisor);
$cns = $Ing->lista_ingresos_doc($cod);
$id_cli = 1;
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            cdg = '<?php echo $cod ?>';
            $(function () {
                if (cdg == 0) {
                    $('#validador').val('1');
                }
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    if (pro_descripcion.value != '' && pro_codigo.value != '' && pro_id.value != '' && (mov_cantidad.value.length != 0 && parseFloat(mov_cantidad.value) != 0)) {
                        clonar();
                    }
                });
            });
            function save(e, c) {
                var data = Array();
                n = 0;
                j = $('.itm').length;
                while (n < j) {
                    n++;
                    pro_id = $('#pro_id' + n).html();
                    mov_cantidad = $('#mov_cantidad' + n).html();
                    lote = $('#pro_lote' + n).html();
                    data.push(
                            pro_id + '&' +
                            '26' + '&' +
                            c + '&' +
                            e + '&' +
                            mov_documento.value + '&' +
                            '' + '&' +
                            mov_fecha_trans.value + '&' +
                            mov_cantidad + '&' +
                            '0'+ '&' +//tbl
                            lote///mov_pago
                            );
                }
                var fields = Array();
                $('#encabezado').find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });

                $('#lista').find('td').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + $(elemento).html();
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        if ($('#pro_id1').html() == null) {
                            alert('INGRESE POR LO MENOS UN REGISTRO');
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_semielaborado_ingreso.php',
                    data: {op: 12, 'data[]': data, 'fields[]': fields, x: 0}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            imprimir();
                            cancelar();
                        } else {
                            alert(dt);
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
                parent.document.getElementById('bottomFrame').src = '';                 parent.document.getElementById('contenedor2').rows = "*,0%";

            }
            function caracter(e, obj, x) {
                var ch0 = e.keyCode;
                var ch1 = e.which;
                if (ch0 == 0 && ch1 == 46 && x == 0) { //Punto (Con lector de Codigo de Barras)
                    $('#mov_cantidad').focus();
                }else if (ch0 == 9 && ch1 == 0 && x == 0) { //Tab (Sin lector de Codigo de Barras)
                    load_producto(0)
                } else if (x == 1 && obj.value.length > 8) {//Desde lote
                    $('#mov_cantidad').focus();
                    load_producto(1)
                }
            }


            function load_producto(v) {
                vl = $('#pro_codigo').val();
                $('#pro_descripcion').focus();
                $.post("actions_semielaborado_ingreso.php", {op: 64, id: vl},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#pro_id').val(dat[0]);
                        $('#pro_codigo').val(dat[1]);
                        $('#pro_descripcion').val(dat[2]);
                        $('#pro_uni').val(dat[3]);
                        $('#pro_lote').focus();
                        if ($('#validador').val() == '1') {
                            $('#mov_cantidad').val('1');
                            if (pro_descripcion.value != '' && pro_codigo.value != '' && pro_id.value != '' && pro_uni.value != '' && (mov_cantidad.value != '' || parseFloat(mov_cantidad.value) != 0)) {
                                clonar();
                            }
                        }
                    } else {
                        $('#pro_codigo').val('');
                        $('#pro_descripcion').val('');
                        $('#pro_id').val('');
                        $('#pro_uni').val('');
                    }
                });
            }


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function clonar() {
                d = 0;
                n = 0;
                j = $('.itm').length;
                if (j > 0) {
                    while (n < j) {
                        n++;
                        if ($('#pro_id' + n).html() == pro_id.value&& $('#pro_lote' + n).html() == pro_lote.value) {
                            d = 1;
                            cant = parseFloat($('#mov_cantidad' + n).html()) + parseFloat(mov_cantidad.value);
                            $('#mov_cantidad' + n).html(cant.toFixed(2))
                        }
                    }
                }
                if (d == 0) {
                    i = j + 1;
                    var fila = '<tr class="itm"><td>' + i + '</td><td id="pro_codigo' + i + '">' + pro_codigo.value + '</td><td hidden id="pro_id' + i + '">' + pro_id.value + '</td></td><td id="pro_descripcion' + i + '">' + pro_descripcion.value + '</td><td id="pro_uni' + i + '">' + pro_uni.value + '</td><td id="pro_lote' + i + '">' + pro_lote.value + '</td><td id="mov_cantidad' + i + '" align="right">' + parseFloat(mov_cantidad.value).toFixed(2) + '</td><td></td></tr>';
                    $('#lista').append(fila);
                }
                pro_codigo.value = '';
                pro_id.value = '';
                pro_uni.value = '';
                pro_descripcion.value = '';
                mov_cantidad.value = '';
                pro_lote.value = '';
                $('#pro_codigo').focus();
                total();

            }
            function total() {
                doc = document.getElementsByClassName('itm');
                n = 0;
                sum = 0;
                while (n < doc.length) {
                    n++;
                    if ($('#mov_cantidad' + n).html().length == 0) {
                        can = 0;
                    } else {
                        can = $('#mov_cantidad' + n).html()
                    }
                    sum = sum + parseFloat(can);
                }

                $('#total').html(sum.toFixed(2));
            }
            function imprimir() {
                $('#head_frm').hide();
                $('#botones').hide();
                $('#add').hide();
                $('.cerrar').hide();
                window.print();
                $('#head_frm').show();
                $('#botones').show();
                $('#add').show();
                $('.cerrar').show();
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
            #head td{
                background:#00529B;
                color:white !important;
                font-weight:bolder; 
                text-align:center; 
            }

            .add td{
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
        <form id="frm_save" lang="0" autocomplete="off" >
            <table id="tbl_form">
                <thead>
                    <tr>
                        <th colspan="7" ><?PHP echo 'INGRESO DE PRODUCTO SEMIELABORADO' ?>
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>  
                        </th>
                    </tr>
                </thead>
                <tbody id="encabezado">
                    <tr>
                        <td colspan="7">Documento No:
                            <input type="text" size="20"  id="mov_documento" readonly value="<?php echo $cod ?>" />
                            Fecha de Ingreso:
                            <input type="text" size="12" name="fecha1" id="mov_fecha_trans" value="<?php echo date('Y-m-d') ?>" readonly/>
                            Transaccion:<input type="text" size="25"  id="trs_id" readonly value="<?php echo 'Ingreso de Produccion' ?>" />                        
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            Proveedor:<input type="text" value="<?php echo POLIPACK ?>"  readonly/>
                            Destino:<input type="text"  value="<?php echo 'BODEGA SEMIELABORADO' ?>" readonly />
                            Ingreso:<select id="validador" >
                                <option value="0">Manual</option>
                                <option value="1">Unidad</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Codigo</th>
                        <th>Descripcion</th>
                        <th>Unidad</th>
                        <th>Lote</th>
                        <th>Cantidad</th>
                        <th></th>
                    </tr>
                </thead>
               <tbody class="tbl_frm_aux" id="head_frm" >  
                    <tr>
                        <td></td>
                        <td>
                            <input type="text" size="10" id="pro_codigo" list="productos" onkeypress="caracter(event, this, 0)" maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" />
                            <input type="hidden" size="10" id="pro_id" />
                        </td>
                        <td><input type="text" size="35" readonly id="pro_descripcion"  /></td>
                        <td><input type="text" size="10" readonly id="pro_uni" /></td>
                        <td><input type="text" size="10" id="pro_lote" maxlength="11" /></td>
                        <td><input type="text" size="10" id="mov_cantidad" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                        <td><button id='add'/>+</button></td>
                    </tr>

                </tbody>
                <tbody class="tbl_frm_aux" id="lista" >   
                </tbody>
                <tr class="add">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right" >TOTAL</td>
                    <td align="right" style="font-size:14px; " id="total">0</td>
                    <td></td>
                </tr>
                <tr>
            </table>
        </form>

        <table id="botones">
            <td colspan="3">
                <?php
                if ($Prt->add == 0 || $Prt->edition == 0) {
                    ?>
                    <button id="save" onclick="save('1', '<?php echo $id_cli ?>')">Guardar</button>
                <?php }
                ?>
                <button id="cancel" onclick="cancelar()">Cancelar</button>
            </td>
        </tr> 
    </table>
    <datalist id="productos">
        <?php
        while ($rst_pro = pg_fetch_array($cns_pro)) {
            echo "<option value='$rst_pro[pro_id]' >$rst_pro[pro_codigo] - $rst_pro[pro_descripcion]</option>";
        }
        ?>
    </datalist>
