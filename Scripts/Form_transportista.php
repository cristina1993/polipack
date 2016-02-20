<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_transportista.php';
$Clase_transportista = new Clase_transportista();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase_transportista->lista_un_transportista($id));
} else {
    $id = 0;
    $rst['reg_fecha'] = date('Y-m-d');
    $rst['mov_cantidad1'] = 0;
    $fila = 0;
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
            });
            function save(id) {
                var data = Array(
                        identificacion.value,
                        razon_social.value,
                        email.value,
                        placa.value,
                        telefono.value,
                        direccion.value
                        );

                var fields = Array();
                $("#tbl_form").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (identificacion.value.length == 0) {
                            $("#identificacion").css({borderColor: "red"});
                            $("#identificacion").focus();
                            return false;
                        }
                        else if (razon_social.value.length == 0) {
                            $("#razon_social").css({borderColor: "red"});
                            $("#razon_social").focus();
                            return false;
                        }
                        else if (telefono.value.length == 0) {
                            $("#telefono").css({borderColor: "red"});
                            $("#telefono").focus();
                            return false;
                        }
                        else if (direccion.value.length == 0) {
                            $("#direccion").css({borderColor: "red"});
                            $("#direccion").focus();
                            return false;
                        }
                        else if (placa.value.length == 0) {
                            $("#placa").css({borderColor: "red"});
                            $("#placa").focus();
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_transportista.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_transportistas.php';
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
                <tr>
                    <td>CEDULA / RUC:</td>
                    <td><input type="text" size="40"  id="identificacion" value="<?php echo $rst[identificacion] ?>" /></td>
                </tr>
                <tr>
                    <td>RAZON SOCIAL:</td>
                    <td><input type="text" size="40"  id="razon_social" value="<?php echo $rst[razon_social] ?>" onblur="this.value = this.value.toUpperCase()"/>
                </tr>
                <tr>
                    <td>TELEFONO:</td>
                    <td><input type="text" size="40"  id="telefono" value="<?php echo $rst[telefono] ?>" />
                </tr>
                <tr>
                    <td>EMAIL:</td>
                    <td><input type="email" size="40"  id="email" value="<?php echo $rst[email] ?>"/>
                </tr>
                <tr>
                    <td>DIRECCION:</td>
                    <td><input type="text" size="40"  id="direccion" value="<?php echo $rst[direccion] ?>" onblur="this.value = this.value.toUpperCase()"/>
                </tr>
                <tr>
                    <td>PLACA:</td>
                    <td><input type="text" size="40"  id="placa" value="<?php echo $rst[placa] ?>" onblur="this.value = this.value.toUpperCase()"/>
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

