<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_plan_cuentas.php';
$Clase_plan_cuentas = new Clase_plan_cuentas();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase_plan_cuentas->lista_una_cuenta($id));
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
                    save(id);
                });
            });
            function save(id) {
                var data = Array(
                        pln_codigo.value,
                        pln_descripcion.value,
                        pln_obs.value
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
                        if (pln_codigo.value.length == 0) {
                            $("#pln_codigo").css({borderColor: "red"});
                            $("#pln_codigo").focus();
                            return false;
                        }
                        else if (pln_descripcion.value.length == 0) {
                            $("#pln_descripcion").css({borderColor: "red"});
                            $("#pln_descripcion").focus();
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_plan_cuentas.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_plan_cuentas.php';
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
                    <tr><th colspan="9" >FORMULARIO PLAN DE CUENTAS<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>CODIGO:</td>
                    <td><input type="text" size="40"  id="pln_codigo" value="<?php echo $rst[pln_codigo] ?>" onblur="this.value = this.value.toUpperCase()"/></td>
                </tr>
                <tr>
                    <td>DESCRIPCION:</td>
                    <td><input type="text" size="40"  id="pln_descripcion" value="<?php echo $rst[pln_descripcion] ?>" onblur="this.value = this.value.toUpperCase()"/>
                </tr>
                <tr>
                    <td>OBSERVACION:</td>
                    <td><input type="text" size="40"  id="pln_obs" value="<?php echo $rst[pln_obs] ?>" onblur="this.value = this.value.toUpperCase()"/>
                </tr>

                <tfoot>
                    <tr><td colspan="2">
                            <?PHP
                            if ($x != 1) {
                                ?>                 

                                <button id="guardar">Guardar</button>    
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

