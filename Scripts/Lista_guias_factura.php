<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_guia_remision.php'; //cambiar clsClase_productos
$Clase_guia_remision = new Clase_guia_remision();
$id = $_GET[id];
$num = $_GET[num];
if (isset($_GET[id])) {
    $cns = $Clase_guia_remision->lista_guias_factura($id);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                revisa_sri();
                setInterval(revisa_sri, 5000);
                $("#mensaje").load('../Includes/envio_sri_guia_remision.php');
                $("#mensaje").load('../Includes/envio_mail_guia.php');
            });

            function revisa_sri() {
                $.ajax({
                    type: 'POST',
                    url: '../Includes/con_sri.php',
                    success: function (dt) {
                        if (dt == 1) {
                            $('#sri').css('background', 'red');
                            $('#sri').attr('title', 'No hay conexion con el servidor');
                        } else {
                            $('#sri').css('background', 'green');
                            $('#sri').attr('title', 'Conexion exitosa');
                        }
                    }
                });
            }

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";

            }

            function auxWindow(a, id, x, y)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_guia_remision.php?id=' + id;
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_guia_remision.php?id=' + id + '&x=' + x;
                        break;
                    case 2://Listar
                        frm.src = '../Scripts/Form_guia_remision.php?id=' + id + '&x=' + x + '&y=' + y;
                        break;
                    case 3://PDF
                        frm.src = '../Scripts/frm_pdf_guia_remision.php?id=' + id;
                        look_menu();
                        break;
                    case 4://XML
                        if (y == 0) {
                            id = x;
                            tp = 0;
                        } else {
                            id = id;
                            tp = 6;
                        }
                        loading('visible');
                        window.location = '../Reports/descargar_xml.php?id=' + id + '&tp=' + tp;
                        loading('hidden');
                        break;

                        break;
                    case 5://PDF
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {

                            },
                            type: 'GET',
                            url: '../Reports/pdf_guia_remision.php',
                            data: {id: id, x: 1},
                            success: function (dt) {
                                loading('hidden');
                                $.post("actions.php", {act: 74, dt: dt, id: id},
                                function (dt) {
                                    if (dt == 0) {
                                        alert('Guia Enviada Correctamente');
                                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_guias_factura.php';
                                    } else {
                                        alert(dt);
                                    }
                                });
                            }
                        });
                        break;

                }
            }
            function del(id)
            {
                var numero = '<?php echo $x ?>';
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_guia_remision.php", {act: 48, id: id, op: 1}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('bottomFrame').src = '../Scripts/Lista_guias_factura.php?id=' + numero;
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function envia_mail(id) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'GET',
                    url: '../Reports/pdf_guia_remision.php',
                    data: {id: id, x: 1},
                    success: function (dt) {
                        loading('hidden');
                        $.post("actions.php", {act: 74, dt: dt, id: id},
                        function (dt) {
                            if (dt == 0) {
                                alert('Guia Enviada Correctamente');
                                window.history.go(0);
                            } else {
                                alert(dt);
                            }
                        });
                    }
                });
            }

        </script> 
        <style>
            input[type=text]{
                text-transform: uppercase;
            }

        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="mensaje" hidden></div>
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_title" ><?php echo 'GUIAS DE REMISION DE LA FACTURA ' . $num ?><font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0, '<?php echo $id; ?>')" >Nuevo </a>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Fecha de Emision</th>
            <th>No. Guia Remision</th>
            <th>Documento No.</th>
            <th>Cliente</th>
            <th>Fecha Inicio Trans.</th>
            <th>Fecha Fin Trans.</th>
            <th>Motivo Traslado</th>
            <th>Punto Partida</th>
            <th>Destino</th>
            <th>Usuario</th>
            <th>ESTADO</th>
            <!--<th>AUTORIZACION</th>-->
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $grup = '';

            while ($rst = pg_fetch_array($cns)) {
                $n++;

                //CONTROL DE ERRRORES CORREO************************
                $estaemail = $rst[gui_estado_correo];
                $nomemi = $rst[gui_nombre];
                if ($nomemi == 'CONSUMIDOR FINAL') {
                    $nomemi = 'CONSUMIDOR FINAL';
                }
                if ($estaemail == 'ERROR AL ENVIAR') {
                    $estaemail = 'ERROR AL ENVIAR';
                }
                if ($estaemail == 'PENDIENTE DE ENVIAR') {
                    $estaemail = 'PENDIENTE DE ENVIAR';
                }
                if ($estaemail == 'ENVIADO') {
                    $estaemail = 'ENVIADO';
                } else {
                    $estaemail = $rst[gui_estado_correo];
                    $nomemi = $rst[gui_nombre];
                }
                if ($rst[gui_sts] == 1) {
                    $rst[gui_estado_aut] = 'REGISTRADO NO ENVIADO';
                }


                if (strlen(trim($rst[gui_autorizacion])) == 37) {
                    $estdado = 'RECIBIDA AUTORIZADO';
                    $tp = 0;
                } else {
                    $tp = 1;
                    if (strlen($rst[gui_estado_aut]) > 20) {
                        $sts = substr($rst[gui_estado_aut], 0, 20);
                    } else {
                        $sts = $rst[gui_estado_aut];
                    }
                }
                ?>
                <tr>
                    <td ><?php echo $n ?></td>
                    <td align="center" ><?php echo $rst[gui_fecha_emision] ?></td>
                    <td ><?php echo $rst[gui_numero] ?></td>
                    <td ><?php echo $rst[gui_num_comprobante] ?></td>
                    <td ><?php echo $rst[gui_nombre] ?></td>
                    <td ><?php echo $rst[gui_fecha_inicio] ?></td>
                    <td ><?php echo $rst[gui_fecha_fin] ?></td>
                    <td ><?php echo $rst[gui_motivo_traslado] ?></td>
                    <td ><?php echo $rst[gui_punto_partida] ?></td>
                    <td ><?php echo $rst[gui_destino] ?></td>
                    <td ><?php echo $rst[vnd_nombre] ?></td>
                    <td title="<?php echo $rst[gui_estado_aut] ?>"><?PHP echo $sts ?></td>
                    <td align="center">
                        <?php
                        if ($rst[gui_estado_aut] != 'RECIBIDA AUTORIZADO') {
                            if (empty($rst[gui_id])) {
                                ?>
                                <img class="auxBtn" width="12px" src="../img/upd.png" onclick="auxWindow(1, '<?php echo $rst[fac_id] ?>', '<?php echo $rst[gui_id] ?>')" />                    
                                <?PHP
                            }
                            ?>
                            <?PHP
                        }
                        if ($estaemail != 'ENVIADO' && $nomemi != 'CONSUMIDOR FINAL') {
                            if ($_SESSION[usuid] == 1) {
                                ?>
                                <img src="../img/mail.png" width="12px"  class="auxBtn" onclick="auxWindow(5, '<?php echo $rst[num_comprobante] ?>', 0)">
                                <?PHP
                            }
                        }
                        if ($_SESSION[usuid] == 1) {
                            ?>
                            <img class="auxBtn" width="12px" src="../img/xml.png" onclick="auxWindow(4, '<?php echo $rst[gui_id] ?>', '<?php echo $rst[gui_clave_acceso] ?>', '<?php echo $tp ?>')" />
                            <?php
                        }
                        ?>
                        <img src="../img/orden.png" width="12px"  class="auxBtn" onclick="auxWindow(3, '<?php echo $rst[gui_id] ?>', 0)">
                    </td>
                    <?php
                    if ($_SESSION[usuid] == 1) {
                        ?>
                        <td><?PHP echo $rst_ret[gui_observacion_aut] ?></td>
                        <?php
                    }
                    ?>
                </tr>  
                <?PHP
            }
            ?>
        </tbody>
    </table>            
</body>    
</html>

