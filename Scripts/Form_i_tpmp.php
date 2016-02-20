<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($Set->lista_un_tpmp($id));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            function save(id)
            {
                var data = Array(
                        mpt_siglas.value.toUpperCase(),
                        mpt_nombre.value.toUpperCase(),
                        mpt_obs.value.toUpperCase())
                var fields = Array();
                $("#tbl_form").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.post("actions.php", {act: 17, 'data[]': data, id: id, 'fields[]': fields},
                function (dt) {
                    if (dt == 0)
                    {
                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_tpmp.php';
                    } else {
                        alert(dt);
                    }
                });
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";

            }

            function cancelar()
            {
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
        </script>
    </head>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>

        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="3" >TIPOS DE MATERIA PRIMA</th></tr>
            </thead>                    
            <tr>
                <td>Siglas:</td>
                <td><input type="text" name="mpt_siglas" id="mpt_siglas" size="5" value="<?php echo $rst[mpt_siglas] ?>" /></td>
            </tr>
            <tr>
                <td>Nombre:</td>
                <td><input type="text" name="mpt_nombre" id="mpt_nombre" size="15" value="<?php echo $rst[mpt_nombre] ?>" /></td>
            </tr>
            <tr>
                <td>Observaciones:</td>
                <td><input type="text" name="mpt_obs" id="mpt_obs" size="25" value="<?php echo $rst[mpt_obs] ?>" /></td>
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