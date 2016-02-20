<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_factura.php';
$Set = new Set();
$Fac = new Clase_factura();
if (isset($_GET[txt])) {
//    $des = str_replace('-', '', $_GET[desde]);
//    $has = str_replace('-', '', $_GET[hasta]);
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($_GET[txt])) {
        $texto = "where (fac_numero like '%$txt%' or fac_nombre like '%$txt%' or fac_identificacion like '%$txt%')";
    } else {
        $texto = "where fac_fecha_emision between '$desde' and '$hasta'";
    }
    $cns = $Fac->lista_factura_completo($texto);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $texto = "where fac_fecha_emision between '$desde' and '$hasta'";
    $cns = $Fac->lista_factura_completo($texto);
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

                $("#mensaje").load('../Includes/envio_sri.php');


                // $('#email').click(function () {
                //     $('#mensaje').html("<img src='../img/load_circle2.gif' width='30px' />");
                //     $('#mensaje').load("../Includes/envio_mail_factura.php", function (r, s, xhr) {
                //         $(this).html('email: ' + s);
                //     });
                // });
                // $('#facturacion').click(function () {
                //     $('#mensaje').html("<img src='../img/load_circle2.gif' width='30px' />");
                //     $('#mensaje').load("../Includes/envio_sri.php", function (r, s, xhr) {
                //         $(this).html('Sri: ' + s);
                //     });
                // });



            });
            function cargar_claves() {
                $.ajax({
                    beforeSend: function () {
                    },
                    type: 'POST',
                    url: 'actions.php',
                    data: {act: 71}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        window.location = 'Lista_factura_completo.php';
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
                    url: 'actions_factura_nuevo.php',
                    data: {op: 3, na: na, fh: fh, id: ca}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        tbl_aux.style.display = 'none';
                        if (dt == 0) {
                            window.location = 'Lista_factura_completo.php';
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
                position: fixed;                                     
                display:none; 
                background:white; 
            }
            #tbl_aux tr{
                display:none; 
                border-bottom:solid 1px #ccc  ;
            }
            #mensaje{
                position:fixed;
                top:50px;
                right:20px; 
                
/*                color:white;
                background:#00415e;
                width:300px;
                height:300px;
                overflow:scroll; */
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
        <div id="cargando"></div>
        <div id="mensaje" ondblclick="this.hidden = true"></div>
        <table id="tbl_aux" style="border: solid 2px black">
            <tr>
                <td colspan="2"><img src="../img/b_delete.png" style="float:right;cursor: pointer" onclick="tbl_aux.style.display = 'none'"  /></td>
            </tr>
            <tr>
                <td>Factura # </td>
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
                    <font class="sbmnu" onclick="window.location = '../Scripts/Lista_factura_completo.php'">Factura</font>
                    <?php echo "FACTURACION COMPLETA " ?>
                    <img src="../img/sri_png.png" class="auxBtn" id="facturacion" width="15px" height="15px"/>
                    <img src="../img/mail.png" class="auxBtn" id="email" width="15px" height="15px"/>
                </center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="hidden" value="<?php echo $emisor ?>" id="emisor" />
                        FACTURA:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>" />
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
                    <th colspan="6">SERVICIO DE RENTAS INTERNAS</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>FECHA</th>
                    <th>FACTURA</th>
                    <th>CLIENTE</th>
                    <th>RUC</th>
                    <th>VALOR TOTAL_$</th>
                    <th>FECHA/HORA/AUTO</th>
                    <th>ESTADO</th>
                    <th>CLAVE DE ACCESO</th>
                    <th>NUM AUTORIZACION SRI</th>
                    <th>e-mail</th>
                    <th>XML</th>
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
                        $estdado = 'RECIBIDA AUTORIZADA';
                        $class = '';
                    } else {
                        $class = 'incorrecto';
                        $err1 = strpos($rst[fac_estado_aut], 'SIN CONEXION');
                        if ($err1 == true) {
                            $estdado = 'SIN CONEXION';
                        } else {
                            $estdado = substr($rst[fac_estado_aut], 0, 30);
                        }
                    }

                    if (strlen($rst[fac_xml_doc]) < 100) {
                        $xml = '';
                    } else {
                        $xml = 'Si';
                    }

                    echo "<tr class='$class'>
                        <td> $n </td>
                        <td>$rst[fac_fecha_emision]</td>
                        <td>$rst[fac_numero]</td>
                        <td>$rst[fac_nombre]</td>
                        <td>$rst[fac_identificacion]</td>
                        <td align='right' style='font-size:14px;font-weight:bolder'>" . number_format($rst[fac_total_valor], 2) . "</td>
                        <td>$rst[fac_fec_hora_aut]</td>
                        <td id='id$rst[fac_id]' ondblclick='auxWindow(5, 0, 0, '$rst[fac_id]')'  title='$rst[fac_estado_aut]'  >$estdado</td>
                        <td>$rst[fac_clave_acceso]</td>";
                    if (strlen($rst[fac_autorizacion]) == 37 && !empty($rst[fac_fec_hora_aut])) {
                        echo "<td>$rst[fac_autorizacion]</td>";
                    } else {
                        if (empty($rst[fac_autorizacion]) && empty($rst[fac_fec_hora_aut])) {
                            $sms = 'NO SE ENCUENTRA ENVIADA';
                        } else {
                            $sms = '';
                        }
                        $cla1 = '"' . $rst[fac_clave_acceso] . '"';
                        $doc1 = '"' . $rst[fac_numero] . '"';
                        echo "<td style='color:darkred;font-weight:bolder'  ondblclick='cargar_datos($cla1, $doc1, event)' >$sms $rst[fac_autorizacion]</td>";
                    }
                    echo "<td>$rst[fac_estado_correo]</td>
                          <td>$xml</td>
                            ";
                    echo "</tr>";
                }
                echo"</tbody>
            <tr style='font-weight:bolder'>
                <td colspan='5' align='right'>Total</td>
                <td align='right' style='font-size:14px;'>" . number_format($g_total, 2) . "</td>
                <td colspan='6'></td>
            </tr>";
                ?>
        </table>            
    </body>    
</html>

