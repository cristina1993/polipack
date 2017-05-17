<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
$txt = $_GET[txt];
$emp = $_GET[emp_id];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($Set->lista_un_mp($id));
} else {
    $id = 0;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            id =<?php echo $id ?>;
            function save(id)
            {
                var data = Array(
                        fbc_id.value,
                        mpt_id.value,
                        mp_codigo.value.toUpperCase(),
                        mp_referencia.value.toUpperCase(),
                        mp_pro1.value.toUpperCase(),
                        mp_pro2.value.toUpperCase(),
                        mp_pro3.value.toUpperCase(),
                        mp_pro4.value.toUpperCase(),
                        mp_obs.value.toUpperCase(),
                        mp_unidad.value.toLowerCase(),
                        mp_presentacion.value.toUpperCase());
                var fields = Array();
                $("#tbl_form").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });

                $.post("actions.php", {act: 19, 'data[]': data, 'fields[]': fields, id: id},
                function (dt) {
                    if (dt == 0)
                    {
                        loading('hidden');
                        cancelar();
                    } else {
                        alert(dt);
                    }
                });
            }
            function cancelar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_mp.php?search=1&txt=<?php echo $txt?>&emp_id=<?php echo $emp?>';

            }
            function crea_codigo(fbc, tp)
            {
                if (fbc != 0 && tp != 0) {
                    $.post("actions.php", {act: 21, fbc: fbc, tp: tp},
                    function (dt) {
                        mp_codigo.value = dt;
                    });
                }
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
                <tr><th colspan="3" >MATERIA PRIMA <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
            </thead>
            <tr>
                <td>Fabrica:</td>
                <td>
                    <select name="fbc_id" id="fbc_id" onchange="crea_codigo(fbc_id.value, mpt_id.value)">
                        <option value="0">Seleccione</option>
                        <?php
                        $cns_fbc = $Set->lista_fabricas();
                        while ($rst_fbc = pg_fetch_array($cns_fbc)) {
                            if ($rst_fbc[emp_id] == $rst[fbc_id]) {
                                $sel = "selected";
                            } else {
                                $sel = "";
                            }
                            echo "<option $sel value='$rst_fbc[emp_id]'>$rst_fbc[emp_descripcion]</option>";
                        }
                        ?>
                    </select>
                </td>
            <script>
                document.getElementById("fbc_id").value =<?php echo $rst[emp_id] ?>
            </script>
        </tr>
        <tr>
            <td>Tipo:</td>
            <td>
                <select name="mpt_id" id="mpt_id" style="width:200px "  onchange="crea_codigo(fbc_id.value, mpt_id.value)">
                    <option value="0">Seleccione</option>
                    <?php
                    $cns_tp = $Set->lista_tpmp();
                    while ($rst_tp = pg_fetch_array($cns_tp)) {
                        if ($rst_tp[mpt_id] == $rst[mpt_id]) {
                            $sel = "selected";
                        } else {
                            $sel = "";
                        }

                        echo "<option $sel value='$rst_tp[mpt_id]'>$rst_tp[mpt_nombre]</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Codigo:</td>
            <td><input type="text" readonly style="background:#ccc;" name="mp_codigo" id="mp_codigo" size="20" value="<?php echo $rst[mp_codigo] ?>" /></td>
        </tr>
        <tr>
            <td>Descripcion:</td>
            <td><input type="text" name="mp_referencia" id="mp_referencia" size="35" value="<?php echo $rst[mp_referencia] ?>" /></td>
        </tr>
        <tr>
            <td>Unidad:</td>
            <td>
                <select id="mp_unidad" style="text-transform:lowercase" >
                    <option value="kg">kg</option>
                    <option value="unidad">unidad</option>
                    <option value="lb">lb</option>
                    <option value="gr">gr</option>
                    <option value="litro">litro</option>
                    <option value="galon">galon</option>
                    <option value="m">m</option>
                    <option value="cm">cm</option>
                    <option value="ft">ft</option>
                    <option value="in">in</option>
                </select>
                <script>
                    document.getElementById("mp_unidad").value =<?php echo $rst[mp_unidad] ?>
                </script>
            </td>
        </tr>
        <tr>
            <td>Presentacion:</td>
            <td><input type="text" name="mp_presentacion" id="mp_presentacion" size="35" value="<?php echo $rst[mp_presentacion] ?>" /></td>
        </tr>
        <tr>
            <td>Peso (kg):</td>
            <td><input type="text" name="mp_pro1" id="mp_pro1" size="35" value="<?php echo $rst[mp_pro1] ?>" /></td>
        </tr>
        <tr>
            <td>Propiedad2:</td>
            <td><input type="text" name="mp_pro2" id="mp_pro2" size="35" value="<?php echo $rst[mp_pro2] ?>" /></td>
        </tr>
        <tr>
            <td>Propiedad3:</td>
            <td><input type="text" name="mp_pro3" id="mp_pro3" size="35" value="<?php echo $rst[mp_pro3] ?>" /></td>
        </tr>
        <tr>
            <td>Procedencia:</td>
            <td><input type="text" name="mp_pro4" id="mp_pro4" size="35" value="<?php echo $rst[mp_pro4] ?>" /></td>
        </tr>

        <tr>
            <td>Observaciones:</td>
            <td>
                <textarea name="mp_obs" id="mp_obs" style="width:100%"><?php echo $rst[mp_obs] ?></textarea>    
            </td>
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