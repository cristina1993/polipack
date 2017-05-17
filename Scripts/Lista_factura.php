<?php
include_once '../Includes/permisos.php';
//include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_factura.php';
$Set = new Clase_factura();
if ($emisor >= 10) {
    $ems = '0' . $emisor . '-';
} else {
    $ems = '00' . $emisor . '-';
}
if (isset($_GET[desde])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $txt = strtoupper($_GET[txt]);
    if (!empty($_GET[txt])) {
        $text = "and (c.fac_nombre like'%$txt%' or c.fac_identificacion like'%$txt%' or c.fac_numero like'%$txt%') and c.fac_fecha_emision between '$desde' and '$hasta' ";
        $cns = $Set->lista_buscador_factura($text, $emisor);
    } else {
        $text = "and c.fac_fecha_emision between '$desde' and '$hasta'";
        $cns = $Set->lista_buscador_factura($text, $emisor);
    }
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $text = "and c.fac_fecha_emision between '$desde' and '$hasta'";
    $cns = $Set->lista_buscador_factura($text, $emisor);
}
/////////*******RESPUESTAS************
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
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                revisa_sri();
                setInterval(revisa_sri, 5000);
                $("#mensaje").load('../Includes/envio_sri.php');
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }
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

            function auxWindow(a, id, x, comid) {
                t = $('#txt').val();
                d = $('#desde').val();
                h = $('#hasta').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_factura.php?emisor=' + emisor.value + '&desde=' + d + '&hasta=' + h + '&txt=' + t;//Cambiar Form_productos
                        //look_menu();
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                    case 1://Editar
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_factura.php?id=' + id + '&x=' + x + '&emisor=' + emisor.value + '&desde=' + d + '&hasta=' + h + '&txt=' + t;//Cambiar Form_productos
                        break;
                    case 2://Eliminar
                        alert('Proceso en construccion');
                        break;
                    case 3://Envio al SRI
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {

                            },
                            timeout: 10000,
                            type: 'POST',
                            url: '../xml/factura_xml.php',
                            data: {id: comid},
                            error: function (j, t, e) {
                                if (t == 'timeout') {
                                    loading('hidden');
                                    alert('Tiempo agotado sin respuesta del SRI \n Intente mas tarde');
                                    window.history.go(-1);
                                }
                            },
                            success: function (dt) {
//                                loading('hidden');
                                dat = dt.split('&');
                                $.post("actions.php", {act: 67, 'data[]': dat, dt: dt, id: comid},
                                function (dato) {
                                    dat1 = dato.split('&');
                                    if (dat1[0] == 0) {
//                                        
                                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php';
                                        if (dat[4].length == 38) {
                                            envia_mail(id);
                                        } else {
                                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php';
                                        }
                                    } else {
                                        alert(dato);
                                    }
                                });
                            }
                        });
                        break;
                    case 4://PDF
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_factura.php?id=' + id;
//                        look_menu();
                        break;
                    case 5:
                        $.post("actions.php", {act: 70, id: comid},
                        function (dt) {
                            if (dt.length == 0) {
                                dt = 'S/N';
                            }
                            obj = $('#aux' + comid);
                            obj.html(dt);

                        });
                        break;
                    case 6://PDF talonario
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_talonario_factura.php?id=' + id;
//                        look_menu();
                        break;
                    case 7://EMAIL
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {

                            },
                            type: 'GET',
                            url: '../Reports/pdf_factura_mail.php',
                            data: {id: id},
                            success: function (dt) {
                                loading('hidden');
                                $.post("actions.php", {act: 73, dt: dt, id: id},
                                function (dt) {
                                    if (dt == 0) {
                                        alert('Factura Enviada Correctamente');
                                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php';
                                    } else {
                                        alert(dt);
                                    }
                                });
                            }
                        });
                        break;

                    case 8://Cierre de Caja
                        $.ajax({
                            beforeSend: function () {

                            },
                            type: 'POST',
                            url: 'actions_cierre_caja.php',
                            data: {op: 0, id: id},
                            success: function (dt) {
                                d = dt.split('&');
                                if (d[1] == 1) {

                                    alert('No existe facturas realizadas en la fecha actual');

                                } else {
                                    if (d[1] == 0) {
                                        parent.document.getElementById('contenedor2').rows = "*,'95%";
                                        frm.src = '../Scripts/Form_cierre_caja.php?emisor=' + emisor;
                                    } else {
                                        alert(d[1]);
                                    }
                                }
                            }
                        });
                        break;
                    case 9://Genera XML
//                        loading('visible');
//                        $.ajax({
//                            beforeSend: function () {
//                            },
////                            timeout: 6000,
//                            type: 'POST',
//                            url: '../xml/factura_xml.php',
//                            data: {id: comid, gnr: 1},
//                            success: function (dt) {
//                                loading('hidden');
//                                window.location = '../Reports/descargar_xml.php?id=' + dt;
//                            }
//                        });
                        if (comid == 0) {
                            id = x;
                        } else {
                            id = id;
                        }
                        loading('visible');
                        window.location = '../Reports/descargar_xml.php?id=' + id + '&tp=' + comid;
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
                    url: '../Reports/pdf_factura_mail.php',
                    data: {id: id},
                    timeout: 10000,
                    error: function (j, t, e) {
                        if (t == 'timeout') {
                            loading('hidden');
                            alert('Tiempo agotado No se pudo enviar Via e-mal');
                            window.history.go(-1);
                        }
                    },
                    success: function (dt) {
                        loading('hidden');
                        $.post("actions.php", {act: 73, dt: dt, id: id},
                        function (dt) {
                            if (dt == 0) {
                                alert('Factura Enviada Correctamente');

                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php';
                            } else {
                                alert(dt);
                            }
                        });
                    }
                });
            }

        </script> 
        <style>
            #mn64,
            #mn112,
            #mn117,
            #mn122,
            #mn127,
            #mn132,
            #mn137,
            #mn142,
            #mn147,
            #mn152{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #sri{
                float:right;
                padding:2px; 
                border-radius:5px; 
                cursor:help; 
                border:double 1px #000;  
                box-shadow:0px 1px 1px #fff; 
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <div id="mensaje" style="display:none"></div>
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
                <center class="cont_title" >
                    <?php echo "FACTURACION BODEGA " . $bodega ?>
                    <font id="sri" onclick="revisa_sri()" title="" ><img src="../img/sri_png.png" width="30px" /></font>                    
                </center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCADOR:<input type="hidden" value="<?php echo $emisor ?>" id="emisor"  />
                        <input type="text" name="txt" size="40" id="txt" value="<?php echo $txt ?>" placeholder="CLIENTE/RUC/N-FACTURA" style="text-transform:uppercase "/>
                        DESDE:<input type="date" size="15" name="desde" id="desde" value="<?php echo $desde ?>" />
                        <img src="../img/calendar.png" id="im-desde"/>
                        HASTA:<input type="date" size="15" name="hasta" id="hasta" value="<?php echo $hasta ?>" />
                        <img src="../img/calendar.png" id="im-hasta"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="6">DOCUMENTO</th>
                    <th colspan="5">SERVICIO DE RENTAS INTERNAS</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>FECHA</th>
                    <th>FACTURA</th>
                    <th>CLIENTE</th>
                    <th>RUC</th>
                    <th>VALOR TOTAL $</th>
                    <th>ESTADO</th>
                    <th>VENDEDOR</th>
                    <th>FECHA Y HORA AUTORIZACION</th>
                    <th>ACCION</th>
                </tr>        
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $g_total+=$rst[fac_total_valor];
//CONTROL DE ERRORES ***********************
                    if (strlen(trim($rst[fac_autorizacion])) == 37) {
                        $estdado = 'RECIBIDA AUTORIZADO';
                        $tp = 0;
                    } else {
                        $tp = 1;
                        if (strlen($rst[fac_estado_aut]) > 20) {
                            $estdado = substr($rst[fac_estado_aut], 0, 20);
                        } else {
                            $estdado = $rst[fac_estado_aut];
                        }
                    }
//CONTROL DE ERRRORES CORREO************************
                    $estaemail = $rst[fac_estado_correo];
                    $nomemi = $rst[fac_nombre];
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
                        $estaemail = $rst[fac_estado_correo];
                        $nomemi = $rst[fac_nombre];
                    }

                    echo "<tr>
                        <td>$n</td>
                        <td>$rst[fac_fecha_emision]</td>
                        <td>$rst[fac_numero]</td>
                        <td>$rst[fac_nombre]</td>
                        <td>$rst[fac_identificacion]</td>
                        <td align='right' style='font-size:14px;font-weight:bolder'>" . number_format($rst[fac_total_valor], 2) . "</td>
                        <td id='id$rst[com_id]' ondblclick='auxWindow(5, 0, 0, '$rst[fac_id]')'  title='$rst[fac_estado_aut]'  >$estdado</td>
                        <td>$rst[vnd_nombre]</td>
                        <td>$rst[fac_fec_hora_aut]</td>
                        <td style='width:170px'>";

         //           if ($_SESSION[usuid] == 1) {
                        ?>
                    <img class="auxBtn" width="12px" src="../img/xml.png" onclick="auxWindow(9, '<?php echo $rst[fac_id] ?>', '<?php echo $rst[fac_clave_acceso] ?>', '<?php echo $tp ?>')" />
                    <?php
           //     }
                ?>
                <?php
                if ($estaemail != 'ENVIADO' && $nomemi != 'CONSUMIDOR FINAL') {
                    ?>
                    <img class="auxBtn" width="12px" src="../img/mail.png" onclick="auxWindow(7, '<?php echo $rst[fac_numero] ?>')">          
                    <?PHP
                }
                ?>
                <img class="auxBtn" width="12px" src="../img/orden.png" onclick="auxWindow(4, '<?php echo $rst[fac_id] ?>')">
                <img class="auxBtn" width="12px" src="../img/factura.png" onclick="auxWindow(6, '<?php echo $rst[fac_id] ?>')">                   
                </td>
                </tr>  
                <?PHP
            }
            ?>
            </tbody>
            <tr style="font-weight:bolder">
                <td colspan="5" align="right">Total</td>
                <td align="right" style="font-size:14px;"><?php echo number_format($g_total, 2) ?></td>
                <td colspan="6"></td>
            </tr>
        </table>            
    </body>    
</html>
