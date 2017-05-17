<?php
include_once '../Clases/clsClase_etiqueta_grande.php';
include_once '../Includes/permisos.php';
$Set = new Clase_etiqueta_grande();
$id = $_GET[id];
$txt = $_GET[txt];
$fec1 = $_GET[fec1];
$fec2 = $_GET[fec2];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($Set->lista_una_etiqueta($id));
    $pro = substr($rst[etg_numero], 8, 9);
    $ord = substr($rst[etg_numero], 0, 8);
    if ($rst[etg_tipo] == 0) {
        $rst1 = pg_fetch_array($Set->lista_orden_extrusion($ord));
        switch ($pro) {
            case 2:
                $pro_id = $rst1[ord_pro_secundario];
                break;
            case 3:
                $pro_id = $rst1[ord_pro3];
                break;
            case 4:
                $pro_id = $rst1[ord_pro4];
                break;
            case '':
                $pro_id = $rst1[pro_id];
                break;
        }
        $ord_id = $rst1[ord_id];
        $pbruto = $rst1[pro_propiedad4];
        $pneto = $rst1[pro_peso];
        $core = $rst1[pro_propiedad5];
    } else if ($rst[etg_tipo] == 1) {
        $rst1 = pg_fetch_array($Set->lista_orden_corte($ord, $pro));
        $ord_id = $rst1[opp_id];
        $pro_id = $rst1[pro_id];
        $pbruto = $rst1[pro_propiedad7];
        $pneto = $rst1[pro_medvul];
        $core = $rst1[pro_capa];
    }
} else {
    $id = 0;
    $rst[etg_fecha] = date('Y-m-d');
    $rst[etg_copias] = '1';
    $ord_id = '';
    $pro_id = '';
    $peso_bruto = '';
    $peso_neto = '';
    $core = '';
}
$empresa = pg_fetch_array($Set->lista_empresa());
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                Calendar.setup({inputField: "etg_fecha", ifFormat: "%Y-%m-%d", button: "im-etg_fecha"});
            });
            function save(id)
            {
                var data = Array(
                        $('#ord_id').val(),
                        $('#pro_id').val(),
                        $('#cli_id').val(),
                        $('#etg_tipo').val(),
                        $('#etg_numero').val().toUpperCase(),
                        $('#etg_espacio1').val().toUpperCase(),
                        $('#etg_espacio2').val().toUpperCase(),
                        $('#etg_espacio3').val().toUpperCase(),
                        $('#etg_espacio4').val().toUpperCase(),
                        $('#etg_espacio5').val().toUpperCase(),
                        $('#etg_espacio6').val().toUpperCase(),
                        $('#etg_espacio7').val().toUpperCase(),
                        $('#etg_espacio8').val().toUpperCase(),
                        $('#etg_espacio9').val().toUpperCase(),
                        $('#etg_espacio10').val().toUpperCase(),
                        $('#etg_espacio11').val().toUpperCase(),
                        $('#etg_espacio12').val().toUpperCase(),
                        $('#etg_espacio13').val().toUpperCase(),
                        $('#etg_espacio14').val().toUpperCase(),
                        $('#etg_copias').val(),
                        $('#etg_fecha').val(),
                        $('#etg_operador').val().toUpperCase(),
                        $('#etg_pallet1').val().toUpperCase(),
                        $('#etg_pallet2').val().toUpperCase()
                        )
                var fields = Array();
                $("#tbl_form").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                if ($('#etg_copias').val().length == 0) {
                    $("#etg_copias").css({borderColor: "red"});
                    $("#etg_copias").focus();
                    return false;
                }
                else if ($('#etg_numero').val().length == 0) {
                    $("#etg_numero").css({borderColor: "red"});
                    $("#etg_numero").focus();
                    return false;
                }
                else if ($('#etg_cliente').val().length == 0) {
                    $("#etg_cliente").css({borderColor: "red"});
                    $("#etg_cliente").focus();
                    return false;
                }
                $.post("actions_etiqueta_grande.php", {op: 0, 'data[]': data, id: id, 'fields[]': fields},
                function (dt) {
                    if (dt == 0)
                    {
                        cancelar();
                    } else {
                        alert(dt);
                    }
                });


            }
            function load_orden(obj) {
                $.post("actions_etiqueta_grande.php", {op: 2, id: obj.value.toUpperCase()},
                function (dt) {
                    dat = dt.split('&&');
                    if (dat[0] != '') {
                        $('#pro_id').val(dat[0]);
                        $('#ord_id').val(dat[1]);
                        $('#etg_cliente').val(dat[2]);
                        $('#cli_id').val(dat[3]);
                        $('#etg_bruto').val(dat[4]);
                        $('#etg_neto').val(dat[5]);
                        $('#etg_espesor').val(dat[6]);
                        $('#etg_ancho').val(dat[7]);
                        $('#etg_tara').val(dat[8]);
                        $('#etg_tipo').val(dat[9]);
                    } else {
                        $('#pro_id').val('');
                        $('#ord_id').val('');
                        $('#etg_cliente').val('');
                        $('#cli_id').val('');
                        $('#etg_bruto').val('');
                        $('#etg_neto').val('');
                        $('#etg_espesor').val('');
                        $('#etg_ancho').val('');
                        $('#etg_tara').val('');
                        $('#etg_tipo').val('');
                    }
                })
            }

            function cancelar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_etiqueta_grande.php?search=1&txt=<?php echo $txt ?>&fecha1=<?php echo $fec1 ?>&fecha2=<?php echo $fec2 ?>';
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script>
    </head>
    <style>
        *{
            text-transform: uppercase;
        }
    </style>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>

        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="5" >ETIQUETAS<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
            </thead>                    
            <tr>
                <td colspan="2" align="center"><input type="text" size="30" readonly style="text-align: center" name="etg_empresa" id="etg_empresa" value="<?php echo $empresa[emp_descripcion] ?>" /></td>
                <td>#Etiquetas:</td>
                <td><input type="text" size="15" name="etg_copias" id="etg_copias" value="<?php echo $rst[etg_copias] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
            </tr>
            <td>Fecha:</td>
            <td>
                <input type="text" size="12" id="etg_fecha" readonly value="<?php echo $rst[etg_fecha] ?>" <?php echo $read ?> />
                <img src="../img/calendar.png" id="im-etg_fecha" />
            </td>
            <td><input type="text" size="15" name="etg_espacio1" placeholder="Espacio1" id="etg_espacio1" value="<?php echo $rst[etg_espacio1] ?>" /></td>
            <td><input type="text" size="15" name="etg_espacio2" placeholder="Espacio2" id="etg_espacio2" value="<?php echo $rst[etg_espacio2] ?>" /></td>
            <tr>
                <td>Ciente:</td>
                <td colspan="4"><input type="text" readonly size="50" name="etg_cliente" id="etg_cliente" value="<?php echo $rst1[cli_raz_social] ?>" />
                    <input type="hidden" size="10" name="cli_id" id="cli_id" value="<?php echo $rst[cli_id] ?>" /></td>
            </tr>
            <tr>
                <td>#Orden Trabajo:</td>
                <td><input type="text" size="15" name="etg_numero" id="etg_numero" list='ordenes' maxlength="9" value="<?php echo $rst[etg_numero] ?>" onchange="load_orden(this)"/>
                    <input type="hidden" size="10" name="ord_id" id="ord_id" value="<?php echo $ord_id ?>" />
                    <input type="hidden" size="10" name="etg_tipo" id="etg_tipo" value="<?php echo $rst[etg_tipo] ?>" />
                    <input type="hidden" size="10" name="pro_id" id="pro_id" value="<?php echo $pro_id ?>" /></td>
                <td><input type="text" size="15" name="etg_espacio3" placeholder="Espacio3" id="etg_espacio3" value="<?php echo $rst[etg_espacio3] ?>" /></td>
                <td><input type="text" size="15" name="etg_espacio4" placeholder="Espacio4" id="etg_espacio4" value="<?php echo $rst[etg_espacio4] ?>" /></td>
            </tr>
            <tr>
                <td><input type="text" size="15" name="etg_espacio5" placeholder="Espacio5" id="etg_espacio5" value="<?php echo $rst[etg_espacio5] ?>" /></td>
                <td><input type="text" size="15" name="etg_espacio6" placeholder="Espacio6" id="etg_espacio6" value="<?php echo $rst[etg_espacio6] ?>" /></td>
                <td>TARA:</td>
                <td><input type="text" size="12" readonly name="etg_tara" id="etg_tara" value="<?php echo $core ?>" /></td>
            </tr>
            <tr>
                <td>P.Bruto:</td>
                <td><input type="text" size="12" readonly name="etg_bruto" id="etg_bruto" value="<?php echo $pbruto ?>" /></td>
                <td>P.Neto:</td>
                <td><input type="text" size="12" readonly name="etg_neto" id="etg_neto" value="<?php echo $pneto ?>" /></td>
            </tr>
            <tr>
                <td>Espesor:</td>
                <td><input type="text" size="12" readonly name="etg_espesor" id="etg_espesor" value="<?php echo $rst1[pro_espesor] ?>" /></td>
                <td>Ancho:</td>
                <td><input type="text" size="12" readonly name="etg_ancho" id="etg_ancho" value="<?php echo $rst1[pro_ancho] ?>" /></td>
            </tr>
            <tr>
                <td><input type="text" size="15" name="etg_espacio7" placeholder="Espacio7" id="etg_espacio7" value="<?php echo $rst[etg_espacio7] ?>" /></td>
                <td><input type="text" size="15" name="etg_espacio8" placeholder="Espacio8" id="etg_espacio8" value="<?php echo $rst[etg_espacio8] ?>" /></td>
                <td><input type="text" size="15" name="etg_espacio9" placeholder="Espacio9" id="etg_espacio9" value="<?php echo $rst[etg_espacio9] ?>" /></td>
                <td><input type="text" size="15" name="etg_espacio10" placeholder="Espacio10" id="etg_espacio10" value="<?php echo $rst[etg_espacio10] ?>" /></td>
            </tr>
            <tr>
                <td>Operador:</td>
                <td><input type="text" size="15" name="etg_operador" id="etg_operador" value="<?php echo $rst[etg_operador] ?>" /></td>
                <td><input type="text" size="15" name="etg_espacio11" placeholder="Espacio11" id="etg_espacio11" value="<?php echo $rst[etg_espacio11] ?>" /></td>
                <td><input type="text" size="15" name="etg_espacio12" placeholder="Espacio12" id="etg_espacio12" value="<?php echo $rst[etg_espacio12] ?>" /></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><input type="text" size="35" name="etg_espacio13" placeholder="Espacio13" id="etg_espacio13" value="<?php echo $rst[etg_espacio13] ?>" /></td>
                <td colspan="2" align="center"><input type="text" size="35" name="etg_espacio14" placeholder="Espacio14" id="etg_espacio14" value="<?php echo $rst[etg_espacio14] ?>" /></td>
            </tr>
            <tr>
                <td colspan="4" align="center"><input type="text" size="25" name="etg_pallet1" placeholder="Cod. Barras" id="etg_pallet1" value="<?php echo $rst[etg_pallet1] ?>" />
                                               <input type="text" size="25" name="etg_pallet2" placeholder="Cod. Barras" id="etg_pallet2" value="<?php echo $rst[etg_pallet2] ?>" /></td>

            </tr>
            <tr>
                <td colspan="3">
                    <?php
                    if ($Prt->add == 0 || $Prt->edition == 0) {
                        ?>
                        <button id="save" onclick="save(<?php echo $id ?>)">Guardar</button>
                    <?php }
                    ?>
                    <button id="cancel" onclick="cancelar()">Cancelar</button>
                </td>
            </tr>                    

        </table>
    </body>
</html>
<datalist id="ordenes">
    <?php
    $cns_ord = $Set->lista_ordenes();
    $n = 0;
    while ($rst_ord = pg_fetch_array($cns_ord)) {
        $n++;
        ?>
        <option value="<?php echo $rst_ord[ord_num_orden] ?>" label="<?php echo $rst_ord[ord_num_orden] ?>" />
        <?php
    }
    ?>
</datalist>