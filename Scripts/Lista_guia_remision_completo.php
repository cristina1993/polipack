<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_guia_remision.php'; //cambiar clsClase_productos
include_once '../Clases/clsSetting.php';
$Clase_guia = new Clase_guia_remision();
if ($emisor >= 10) {
    $ems = '0' . $emisor . '-';
} else {
    $ems = '00' . $emisor . '-';
}
if (isset($_GET[txt], $_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];

    if (!empty($txt)) {
        $text = "where (gui_numero like '%$txt%' or gui_nombre like '%$txt%' or gui_identificacion like'%$txt%' or gui_autorizacion like'%$txt%' or gui_estado_aut like'%$txt%')";
    } else {
        $text = "where gui_fecha_emision between '$fec1' and '$fec2' ";
    }
    $cns = $Clase_guia->lista_buscador_guias($text);
} else {
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $text = "where gui_fecha_emision between '$fec1' and '$fec2' ";
    $cns = $Clase_guia->lista_buscador_guias($text);
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
                $("#mensaje").load('../Includes/envio_sri_guia_remision.php');
                $("#mensaje").load('../Includes/envio_mail_guia.php');
                $('#email').click(function () {
                    $('#mensaje').html("<img src='../img/load_circle2.gif' width='30px' />");
                    $('#mensaje').load("../Includes/envio_mail_guia.php", function (r, s, xhr) {
                        $(this).html('email: ' + s);
                    });
                });
                $('#facturacion').click(function () {
                    $('#mensaje').html("<img src='../img/load_circle2.gif' width='30px' />");
                    $('#mensaje').load("../Includes/envio_sri_guia_remision.php", function (r, s, xhr) {
                        $(this).html('Sri: ' + s);
                    });
                });
            });


            function cargar_claves() {
                $.ajax({
                    beforeSend: function () {
                    },
                    type: 'POST',
                    url: 'actions_guia_remision.php',
                    data: {op: 6}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        window.location = 'Lista_guia_remision_completo.php';
                    }
                })
            }

            function insert_na() {
                ca = clave_acceso.value;
                na = num_auto.value;
                fh = fh_auto.value;
                $.ajax({
                    beforeSend: function () {
                        if (ca.length == 0) {
                            $("#clave_acceso").css({borderColor: "red"});
                            $("#clave_acceso").focus();
                            return false;
                        } else if (na.length != 37) {
                            $("#num_auto").css({borderColor: "red"});
                            $("#num_auto").focus();
                            return false;
                        } else if (fh.length == 0) {
                            $("#fh_auto").css({borderColor: "red"});
                            $("#fh_auto").focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_guia_remision.php',
                    data: {op: 7, na: na, fh: fh, id: ca}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        tbl_aux.style.display = 'none';
                        if (dt == 0) {
                            window.location = 'Lista_guia_remision_completo.php';
                        } else {
                            alert(dt);
                        }

                    }
                })
            }

            function cargar_datos(ca, fa, e) {
                tbl_aux.style.top = e.clientY;
                tbl_aux.style.left = (e.clientX - 600);
                tbl_aux.style.display = 'block';
                clave_acceso.value = ca;
                factura.value = fa;
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script> 
        <style>
            #tbl_aux{
                position:fixed; 
                display:none; 
                background:white; 
            }
            #tbl_aux tr{
                border-bottom:solid 1px #ccc  ;
            }
            #mensaje{
                position:fixed;
                top:50px;
                right:20px; 
                color:white;
            }
            .incorrecto{
                font-family:Arial, Helvetica, sans-serif; 
                border: 1px solid;
                margin: 10px 0px;
                padding:15px 10px 15px 50px;
                background-repeat: no-repeat;
                background-position: 10px center;
                color: #D8000C;
                background-color: #FFBABA !important;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="mensaje" ondblclick="this.hidden = true"></div>
        <table id="tbl_aux" style="border: solid 2px black">
            <tr>
                <td colspan="2"><img src="../img/b_delete.png" style="float:right;cursor: pointer" onclick="tbl_aux.style.display = 'none'"  /></td>
            </tr>
            <tr>
                <td>Guia Remision# </td>
                <td><input size="60" readonly id="factura"/></td>
            </tr>
            <tr>
                <td>Clave_Acceso</td>
                <td><input size="60" readonly id="clave_acceso"/></td>
            </tr>
            <tr>
                <td>Num_Autorizacion</td>
                <td><input size="50" id="num_auto"/></td>
            </tr>
            <tr>
                <td>Fecha_Hora_Auto</td>
                <td><input size="50" id="fh_auto"/></td>
            </tr>
            <tr>
                <td colspan="2"><img style="float:left" src="../img/save.png" class="auxBtn" onclick="insert_na()" /></td>
            </tr>
        </table>

        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_title" >
                    <font class="sbmnu" onclick="window.location = '../Scripts/Lista_guia_remision_completo.php'">Guia Remision</font>
                    <?php echo "GUIA REMISION COMPLETA " ?>
                    <!--<img src="../img/set.png" class="auxBtn" onclick="cargar_claves()" />-->
                    <img src="../img/sri_png.png" class="auxBtn" id="facturacion" width="15px" height="15px"/>
                    <img src="../img/mail.png" class="auxBtn" id="email" width="15px" height="15px"/>
                </center>             
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="25" id="txt" value="<?php echo $txt ?>" />
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
                <tr>
                    <th colspan="6">DOCUMENTO</th>
                    <th colspan="5">SERVICIO DE RENTAS INTERNAS</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>FECHA</th>
                    <th>GUIA REMISION</th>
                    <th>CLIENTE</th>
                    <th>RUC</th>
                    <th>FECHA/HORA/AUTO</th>
                    <th>ESTADO</th>
                    <th>CLAVE DE ACCESO</th>
                    <th>NUM AUTORIZACION SRI</th>   
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                $grup = '';
                $i;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    if (empty($rst[gui_autorizacion])) {
                        $tx_err1 = "No se puede conectar";
                        $tx_err2 = "NO AUTORIZADO CLAVE ACCESO REGISTRADA";
                        $tx_err3 = "NO AUTORIZADO ERROR SECUENCIAL REGISTRADO";
                        $tx_err4 = "javalangNullPointerException";
                        $estdado = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $rst[gui_estado_aut]);
                        $obs = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $rst[gui_observacion]);
                        $err1 = strpos($obs, $tx_err1);
                        $err2 = strpos($obs, $tx_err2);
                        $err3 = strpos($obs, $tx_err3);
                        $err4 = strpos($obs, $tx_err4);
                        if ($err1 == true) {
                            $estdado = 'SIN CONECCION';
                        }
                        if ($err2 == true || $err3 == true) {
                            $estdado = 'DEVUELTA';
                        }
                        if ($err4 == true && strlen($obs) < 50) {
                            $estdado = 'RECIBIDA AUTORIZADO';
                            $rst['com_autorizacion'] = 'NO SE RECUPERÓ NA';
                        }
                        if ($err4 == true && strlen($obs) > 50) {
                            $estdado = 'RECIBIDA';
                            $rst['com_autorizacion'] = 'ERROR DE ACCESO AL SRI';
                        }
                        $class = 'incorrecto';
                    } else {
                        $estdado = $rst[gui_estado_aut];
                        $class = '';
                    }
                    $cla1 = '"' . $rst[gui_clave_acceso] . '"';
                    $doc1 = '"' . $rst[gui_numero] . '"';
                    //                        <td align='right' style='font-size:14px;font-weight:bolder'>" . number_format($rst[ret_total_valor], 2) . "</td>
                    if ($rst[gui_sts] == 1) {
                        $estdado = 'Registrado No Enviado';
                    }
                    echo "<tr class='$class'>
                        <td>$n</td>
                        <td align='center'>$rst[gui_fecha_emision]</td>
                        <td align='center'>$rst[gui_numero]</td>
                        <td>$rst[gui_nombre]</td>
                        <td>$rst[gui_identificacion]</td>
                        <td>$rst[gui_fec_hora_aut]</td>
                        <td id='id$rst[gui_numero]' ondblclick='auxWindow(5, 0, 0, $rst[gui_id])'>$estdado</td>
                        <td>$rst[gui_clave_acceso]</td>";
                    if (strlen($rst[gui_autorizacion]) == 37 && !empty($rst[gui_fec_hora_aut])) {
                        echo "<td>$rst[gui_autorizacion]</td>";
                    } else {
                        if (empty($rst[gui_autorizacion]) && empty($rst[gui_fec_hora_aut])) {
                            $sms = 'NO SE ENCUENTRA ENVIADA';
                        } else {
                            $sms = '';
                        }

                        echo "<td style='color:darkred;font-weight:bolder' ondblclick='cargar_datos($cla1, $doc1, event)' >$sms $rst[gui_autorizacion]</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>         
        </table>            
    </body>    
</html>
