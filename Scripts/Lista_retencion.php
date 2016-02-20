<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_retencion.php'; //cambiar clsClase_productos
$Clase_retencion = new Clase_retencion();
if ($emisor == '') {
    $emisor = 1;
}
if ($emisor >= 10) {
    $ems = '0' . $emisor;
} else {
    $ems = '00' . $emisor;
}
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];

    if (!empty($txt)) {
        $text = "r.emi_id='$emisor' and (r.ret_numero like '%$txt%' or r.ret_num_comp_retiene like '%$txt%' or r.ret_identificacion like '%$txt%' or r.ret_nombre like '%$txt%'   )";
    } else {
        $text = "r.emi_id='$emisor' and r.ret_fecha_emision between '$fec1' and '$fec2' ";
    }
    $cns = $Clase_retencion->lista_buscador_retencion($text);
} else {
    $txt = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $text = " r.emi_id='$emisor' and r.ret_fecha_emision between '$fec1' and '$fec2' ";
    $cns = $Clase_retencion->lista_buscador_retencion($text);
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
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                $("#mensaje").load('../Includes/envio_sri_retencion.php');
                $("#mensaje").load('../Includes/envio_mail_retencion.php');
                revisa_sri();
                setInterval(revisa_sri, 5000);
                usuid = '<?php echo $_SESSION[usuid] ?>';
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


            function auxWindow(a, id, x, t)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Editar
                        frm.src = '../Scripts/Form_retencion.php'//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1://Editar
//                         alert(id);
                        frm.src = '../Scripts/Form_retencion.php?id=' + id //Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 2://PDF
//                        alert(id);
                        frm.src = '../Scripts/frm_pdf_retencion.php?id=' + id;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
//                        look_menu();
                        break;
                    case 3://Editar
                        frm.src = '../Scripts/Form_retencion.php?id=' + id + '&x=' + x //Cambiar Form_productos
                        look_menu();
                        break;
                    case 4://XML
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {
                            },
//                            timeout: 6000,
                            type: 'POST',
                            url: '../xml/retencion_xml.php',
                            data: {id: id},
//                            error: function (j, t, e) {
//                                if (t == 'timeout') {
//                                    loading('hidden');
//                                    alert('Tiempo agotado sin respuesta del SRI \n Intente mas tarde');
//                                    window.history.go(-1);
//                                }
//                            },
                            success: function (dt) {
                                dat = dt.split('&');
                                alert
                                $.post("actions.php", {act: 68, 'data[]': dat, id: id},
                                function (dato) {
//                                    loading('hidden');
                                    if (dato == 0) {
                                        if (dat[4].length == 38) {
                                            envia_mail(id);
                                        } else {
                                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_retencion.php';
                                        }
                                    } else {
                                        alert(dato);
                                    }
                                });
                            }
                        });
                        break;
                    case 5://EMAIL-PDF
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {

                            },
                            type: 'GET',
                            url: '../Reports/pdf_retencion.php',
                            data: {id: id, val: 1},
                            success: function (dt) {
                                loading('hidden');
                                $.post("actions.php", {act: 76, dt: dt, id: id},
                                function (dt) {
                                    if (dt == 0) {
                                        alert('Retencion Enviada Correctamente');
                                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_retencion.php';
                                    } else {
                                        alert(dt);
                                    }
                                });
                            }
                        });
                        break;
                    case 6://Genera XML
                        if (t == 0) {
                            id = x;
                            tp = 0;
                        } else {
                            id = id;
                            tp = 7;
                        }
                        loading('visible');
                        window.location = '../Reports/descargar_xml.php?id=' + id + '&tp=' + tp;
                        loading('hidden');
                        break;

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
                    url: '../Reports/pdf_retencion.php',
                    data: {id: id, val: 1},
                    success: function (dt) {
                        loading('hidden');
                        $.post("actions.php", {act: 76, dt: dt, id: id},
                        function (dt) {
                            if (dt == 0) {
                                alert('Retencion Enviada Correctamente');
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_retencion.php';
                            } else {
                                alert(dt);
                            }
                        });
                    }
                });
            }

            function del(id, nom) {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_retencion.php", {op: 1, id: id, nom: nom}, function (dt) {
                        if (dt == 0) {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_retencion.php';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }
            function cargar_datos(id, fa, e) {
                tbl_aux.style.top = e.clientY;
                tbl_aux.style.left = (e.clientX - 600);
                tbl_aux.style.display = 'block';
                factura.value = fa;
                com_id.value = id;
            }

            function anular() {
                if (usuid == 1 || usuid == 109 || usuid == 57) {
                    id = com_id.value;
                    codigo_muzo = 'brt8thir';
                    codigo_mejia = '75ma7gBU';
                    codigo_supadm = 'tvk36146';
                    $.ajax({
                        beforeSend: function () {
                            if (cod_anular.value == codigo_muzo && usuid == 57) {
                                return true;
                            } else if (cod_anular.value == codigo_mejia && usuid == 109) {
                                return true;
                            } else if (cod_anular.value == codigo_supadm && usuid == 1) {
                                return true;
                            } else {
                                $('#cod_anular').css('border', 'solid 1px red');
                                $('#cod_anular').val('');
                                return false;
                            }
                        },
                        type: 'POST',
                        url: 'actions_retencion.php',
                        data: {op: 7, id: id}, //op sera de acuerdo a la acion que le toque
                        success: function (dt) {
                            tbl_aux.style.display = 'none';
                            if (dt == 0) {
                                window.location = 'Lista_retencion.php';
                            } else {
                                alert(dt);
                            }

                        }
                    });
                } else {
                    alert('Ud no esta autorizado para realizar este proceso');
                }
            }
        </script> 
        <style>
            #mn68,
            #mn116,
            #mn121,
            #mn126,
            #mn131,
            #mn136,
            #mn141,
            #mn146,
            #mn151,
            #mn156{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #tbl_aux{
                position:fixed; 
                display:none; 
                background:white; 
            }
            #tbl_aux tr{
                border-bottom:solid 1px #ccc  ;
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="mensaje" style="display:none"></div>
        <table id="tbl_aux" style="border: solid 2px black">
            <tr>
                <td colspan="2" style="font-weight:bolder ">Anulación de Documento<img src="../img/b_delete.png" style="float:right;cursor: pointer" onclick="tbl_aux.style.display = 'none', cod_anular.value = ''"  /></td>
            </tr>
            <tr>
                <td>Retencion # </td>
                <td>
                    <input size="30" readonly id="factura"/>
                    <input size="10" type="text" id="com_id"/></td>
            </tr>
            <tr>
                <td>Codigo de autorizacion</td>
                <td><input size="30" id="cod_anular"/></td>
            </tr>
            <tr>
                <td colspan="2"><img style="float:left" src="../img/save.png" class="auxBtn" onclick="anular()" /></td>
            </tr>
        </table>
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" ><?php echo "RETENCIONES BODEGA " . $bodega ?></center>
                <center class="cont_finder">
                    <!-- <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0, '<?php echo $id; ?>')" >Nuevo </a> -->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
                        DESDE:<input type="text" size="15" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="15" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button><img src="../img/finder.png"/>
                    </form>   
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Fecha de Emision</th>
            <th>Retencion No.</th>
            <th>Tipo</th>
            <th>Documento Retenido No.</th>
            <th>Identificacion</th>
            <th>Cliente</th>
            <th>Total Valor $</th>
            <th>Usuario</th>
            <th>ESTADO</th>
            <!--<th>NUM AUTORIZACION</th>-->
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $grup = '';
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $rst_usu = pg_fetch_array($Clase_retencion->lista_usuario_id($rst[vnd_id]));
                if ($rst[ret_denominacion_comp] == 1) {
                    $rst[ret_denominacion_comp] = 'FACTURA';
                } else if ($rst[ret_denominacion_comp] == 4) {
                    $rst[ret_denominacion_comp] = 'NOTA DEBITO';
                }
//CONTROL DE ERRRORES CORREO************************
                $estaemail = $rst[ret_estado_correo];
//                $nomemi = $rst[nombre_destinatario];
//                if ($nomemi == 'CONSUMIDOR FINAL') {
//                    $nomemi = 'CONSUMIDOR FINAL';
//                }
                if ($estaemail == 'ERROR AL ENVIAR') {
                    $estaemail = 'ERROR AL ENVIAR';
                }
                if ($estaemail == 'PENDIENTE DE ENVIAR') {
                    $estaemail = 'PENDIENTE DE ENVIAR';
                }
                if ($estaemail == 'ENVIADO') {
                    $estaemail = 'ENVIADO';
                } else {
                    $estaemail = $rst[ret_estado_correo];
//                    $nomemi = $rst[nombre_destinatario];
                }
                if (strlen(trim($rst[ret_autorizacion])) == 37) {
                    $tp = 0;
                } else {
                    $tp = 1;
                }

                if (strlen($rst[ret_estado_aut]) > 36) {
                    $style = 'color:darkred;font-weight:bolder';
                    $estado = substr($rst[ret_estado_aut], 0, 35);
                } else {
                    $style = '';
                    $estado = $rst[ret_estado_aut];
                }

                echo "<tr>

                    <td>$n</td>
                    <td align='center'>$rst[ret_fecha_emision]</td>
                    <td>$rst[ret_numero]</td>
                    <td>$rst[ret_denominacion_comp]</td>
                    <td>$rst[ret_num_comp_retiene]</td>
                    <td>$rst[ret_identificacion]</td>
                    <td>$rst[ret_nombre]</td>
                    <td align='right' style='font-size:14px;font-weight:bolder'>" . number_format($rst[ret_total_valor], 2) . "</td>
                    <td>" . $rst_usu[usu_person] . "</td>";
                if ($rst[ret_estado_aut] == 'ANULADO') {
                    ?>
                <td style="color:darkred;font-weight:bolder "><?PHP echo substr($estado, 0, 20) ?></td>
                <?php
            } else {
                ?>
                <td style="<?php echo $style ?>" title="<?php echo $rst[ret_estado_aut] ?>" ondblclick="cargar_datos('<?php echo $rst[ret_id] ?>', '<?php echo $rst[ret_numero] ?>', event)"><?PHP echo $estado ?></td>
                <?php
            }
            ?>
            <td align="center">
                <?PHP
                if ($estaemail != 'ENVIADO') {
                    if ($_SESSION[usuid] == 1) {
                        ?>
                        <img src="../img/mail.png" width="12px"  class="auxBtn" onclick="auxWindow(5, '<?php echo $rst[ret_numero] ?>', 0)">
                        <?PHP
                    }
                }
                ?>
                <img src="../img/orden.png" width="12px"  class="auxBtn" onclick="auxWindow(2, '<?php echo $rst[ret_id] ?>', 0)">
                <?php
                if ($_SESSION[usuid] == 1) {
                ?>
                <img class="auxBtn" width="12px" src="../img/xml.png" onclick="auxWindow(6, '<?php echo $rst[ret_id] ?>', '<?php echo $rst[ret_clave_acceso] ?>', '<?php echo $tp ?>')" />
                <?php
                }
                ?>

            </td>
            <?php
            if ($_SESSION[usuid] == 1) {
                ?>
                <td id="<?php echo 'aux' . $rst[ret_numero] ?>" ondblclick="this.innerHTML = ''" ><?PHP //echo $obs                 ?></td>
                <?php
            }
            ?>
        </tr>  
        <?PHP
        $r_total+=$rst[ret_total_valor];
    }
    ?>
</tbody>
<tr style="font-weight:bolder">
    <td colspan="7" align="right">Total</td>
    <td align="right" style="font-size:14px;"><?php echo number_format($r_total, 2) ?></td>
    <td colspan="6"></td>
</tr>
</table>       
</body>    
</html>


