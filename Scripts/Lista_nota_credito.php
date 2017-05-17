<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nota.php'; //cambiar clsClase_productos
include_once '../Clases/clsSetting.php';
$Clase_nota_Credito = new Clase_nota_Credito();
if ($emisor >= 10) {
    $ems = '0' . $emisor . '-';
} else {
    $ems = '00' . $emisor . '-';
}
if (isset($_GET[txt], $_GET[fecha1], $_GET[fecha2], $_GET[fac])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fac = trim(strtoupper($_GET[fac]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];

    if (!empty($txt)) {
        $texto = "and n.emi_id=$emisor and(n.ncr_identificacion like '%$txt%' or n.ncr_nombre like '%$txt%' or n.ncr_numero like '%$txt%' or n.ncr_num_comp_modifica like '%$txt%')";
        $ident = 1;
        $cns = $Clase_nota_Credito->lista_buscador_notas_credito($texto);
    } else if (!empty($fac)) {
        $texto = "and f.emi_id=$emisor and(f.fac_identificacion like '%$fac%' or f.fac_nombre like '%$fac%' or f.fac_numero like '%$fac%')";
        $ident = 0;
        $cns = $Clase_nota_Credito->lista_buscador_facturas($texto);
    } else {
        $texto = "and n.emi_id=$emisor and n.ncr_fecha_emision between '$fec1' and '$fec2' ";
        $ident = 1;
        $cns = $Clase_nota_Credito->lista_buscador_notas_credito($texto);
    }
} else {
    $txt = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $texto = "and n.emi_id=$emisor and n.ncr_fecha_emision between '$fec1' and '$fec2' ";
    $cns = $Clase_nota_Credito->lista_buscador_notas_credito($texto);
    $ident = 1;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            user = '<?php echo $_SESSION[usuid] ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                revisa_sri();
                setInterval(revisa_sri, 5000);
                
                $("#mensaje").load('../Includes/envio_sri_nota_credito.php');
                $("#mensaje").load('../Includes/envio_mail_nota_credito.php');                
                
                
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


            function auxWindow(a, id, x, e)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_nota_credito_nuevo.php?id=' + id;//Cambiar Form_productos
                        //look_menu();
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                    case 1://Editar
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_nota_credito.php?id=' + id + '&x=' + x;//Cambiar Form_productos
                        look_menu();
                        break;
                    case 2://Reporte
//                        alert(x);
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_i_pdf_nota_credito.php?id=' + id + '&x=' + x;
//                        look_menu();
                        break;
                    case 3://Envia SRI
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {
                            },
                            type: 'POST',
                            url: '../xml/nota_credito_xml.php',
//                            data: {id: id},
                            data: {id: x},
                            success: function (dt) {
                                //alert(dt)
                                loading('hidden');
                                dat = dt.split('&');
                                $.post("actions.php", {act: 67, 'data[]': dat, id: x},
                                function (dato) {
                                    loading('hidden');
                                    dto = dato.split('&');
                                    if (dto[0] == 0) {
                                        if (dat[4].length == 38) {
                                            envia_mail(id, e);
                                        } else {
                                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_credito.php';
                                        }
                                    } else {
                                        alert(dato);
                                    }
                                });
                            }
                        });
                        break;
                    case 4://PDF talonario
//                        alert(id);
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_talonario_notacredito.php?id=' + id;
//                        look_menu();
                        break;
                    case 5://EMAIL-PDF
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {

                            },
                            type: 'GET',
                            url: '../Reports/pdf_nota_credito.php',
                            data: {id: id, x: x, val: 1},
                            success: function (dt) {
                                loading('hidden');
                                $.post("actions.php", {act: 75, dt: dt, id: id},
                                function (dt) {
                                    if (dt == 0) {
                                        alert('Nota de Credito Enviada Correctamente');
                                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_credito.php';
                                    } else {
                                        alert(dt);
                                    }
                                });
                            }
                        });

                        break;
                    case 6://Genera XML
                        if (x != '') {
                            id = x;
                            tp = 0;
                        } else {
                            id = id;
                            tp = 4;
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

            function envia_mail(id, e) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'GET',
                    url: '../Reports/pdf_nota_credito.php',
                    data: {id: id, x: e, val: 1},
                    success: function (dt) {
                        loading('hidden');
                        $.post("actions.php", {act: 75, dt: dt, id: id},
                        function (dt) {
                            if (dt == 0) {
                                alert('Nota de Credito Enviada Correctamente');
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_credito.php';
                            } else {
                                alert(dt);
                            }
                        });
                    }
                });
            }

            function del(id, op)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_nota_credito.php", {op: 3, id: id}, function (dt) {//cambiar actions_productos
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_credito.php';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }

            function conf_anular(id, fa, e)
            {
                var r = confirm("Esta Seguro de anular este elemento?");
                if (r == true) {
                    cargar_datos(id, fa, e);
                } else {
                    return false;
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
            function cargar_datos(id, fa, e) {
                tbl_aux.style.top = e.clientY;
                tbl_aux.style.left = (e.clientX - 600);
                tbl_aux.style.display = 'block';
                factura.value = fa;
                com_id.value = id;
            }

            function anular() {
                id = com_id.value;
                codigo = 'brt8thir';
                $.ajax({
                    beforeSend: function () {
                        if (cod_anular.length == 0) {
                            $("#cod_anular").css({borderColor: "red"});
                            $("#cod_anular").focus();
                            return false;
                        }
                        if (cod_anular.value != codigo) {
                            $("#cod_anular").css({borderColor: "red"});
                            $("#cod_anular").focus();
                            $("#cod_anular").val('');
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_nota_credito.php',
                    data: {op: 4, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        tbl_aux.style.display = 'none';
                        if (dt == 0) {
                            window.location = 'Lista_nota_credito.php';
                        } else {
                            alert(dt);
                        }

                    }
                })
            }
        </script> 
        <style>
            #mn66,
            #mn114,
            #mn119,
            #mn124,
            #mn129,
            #mn134,
            #mn139,
            #mn144,
            #mn149,
            #mn154{
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
        <div id="mensaje" style="display:none"></div>
        <div id="cargando">Por Favor Espere...</div>
        <table id="tbl_aux" style="border: solid 2px black">
            <tr>
                <td colspan="2" style="font-weight:bolder ">Anulación de Documento<img src="../img/b_delete.png" style="float:right;cursor: pointer" onclick="tbl_aux.style.display = 'none', cod_anular.value = ''"  /></td>
            </tr>
            <tr>
                <td>Nota Credito # </td>
                <td><input size="30" readonly id="factura"/>
                    <input size="10" hidden id="com_id"/></td>
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
                <center class="cont_title" ><?php echo "NOTAS DE CREDITO BODEGA " . $bodega ?></center>

                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="25" id="txt" value="<?php echo $txt ?>" />
                        FACTURA NO.:<input type="text" name="fac" size="25" id="fac" value="<?php echo $fac ?>" />
                        DESDE:<input type="text" size="15" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>"/>
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
            <th>Nota Credito No.</th>
            <th>Usuario</th>
            <th>Tipo</th>
            <th>Factura No.</th>
            <th>Vendedor</th>
            <th>Identificacion</th>
            <th>Cliente</th>
            <th>Total Nota Cred. $</th>
            <th>Total Factura $</th>
            <th>ESTADO</th>
            <!--<th>NUM AUTORIZACION</th>-->
            <th width='200px'>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $grup = '';
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                if ($ident == 0) {
                    $rst_not = pg_fetch_array($Clase_nota_Credito->lista_un_nota_fac($rst[fac_id]));
                    $fecha = $rst[fac_fecha_emision];
                    $numero = $rst_not[ncr_numero];
                    $vendedor = $rst_not[vnd_nombre];
                    $num_factura = $rst[fac_numero];
                    $fac_vend = $rst[vnd_nombre];
                    $identificacion = $rst[fac_identificacion];
                    $nombre = $rst[fac_nombre];
                    $tot_nc = $rst_not[nrc_total_valor];
                    $tot_fac = $rst[fac_total_valor];
                    $rst[ncr_id] = $rst_not[ncr_id];
                } else if ($ident == 1) {
                    $rst_fact = pg_fetch_array($Clase_nota_Credito->lista_una_factura_id($rst[fac_id]));
                    $fecha = $rst[ncr_fecha_emision];
                    $numero = $rst[ncr_numero];
                    $vendedor = $rst[vnd_nombre];
                    $num_factura = $rst[ncr_num_comp_modifica];
                    $fac_vend = $rst[vnd_nombre];
                    $identificacion = $rst[ncr_identificacion];
                    $nombre = $rst[ncr_nombre];
                    $tot_fac = $rst[fac_total_valor];
                    $tot_nc = $rst[nrc_total_valor];
                    $tot_fac = $rst_fact[fac_total_valor];
                }

//CONTROL DE ERRRORES CORREO***********************************
                $estaemail = $rst[ncr_estado_correo];
                if ($estaemail == 'ERROR AL ENVIAR') {
                    $estaemail = 'ERROR AL ENVIAR';
                }
                if ($estaemail == 'PENDIENTE DE ENVIAR') {
                    $estaemail = 'PENDIENTE DE ENVIAR';
                }
                if ($estaemail == 'ENVIADO') {
                    $estaemail = 'ENVIADO';
                } else {
                    $estaemail = $rst[ncr_estado_correo];
                }
                if ($rst[ncr_estado_aut] == 'ANULADO') {
                    $tot_nc = 0;
                }
                if (strlen($rst[ncr_estado_aut]) > 20) {
                    $estado = substr($rst[ncr_estado_aut], 0, 20);
                } else {
                    $estado = $rst[ncr_estado_aut];
                }
                if ($rst[ncr_estado_aut] == 'ANULADO') {
                   $estado='ANULADO';
                }
                
              //  if($rst[ncr_sts]==1){
              //      $estado='REGISTRADO NO ENVIADO';
           //     }
                
                
                echo "<tr>
                    <td>$n</td>
                    <td align='center'>$fecha</td>
                    <td align='center'>$numero</td>
                    <td>$vendedor</td>
                    <td>FACTURA</td>
                    <td>$num_factura</td>
                    <td>$fac_vend</td>
                    <td>$identificacion</td>
                    <td>$nombre</td>
                    <td align='right' style='font-size:14px;font-weight:bolder'>" . number_format($tot_nc, 2) . "</td>
                    <td align='right' style='font-size:14px;font-weight:bolder'>" . number_format($tot_fac, 2) . "</td>";

                if ($rst[ncr_estado_aut] == 'ANULADO' || strlen($rst[ncr_autorizacion])!=37) {
                    ?>
                <td style="color:darkred;font-weight:bolder " ><?PHP echo $estado ?></td>
                <?php
            } else {
                ?>
                <td  title="<?php echo $rst[ncr_estado_aut] ?>" ondblclick="cargar_datos('<?php echo $rst[ncr_id] ?>', '<?php echo $rst['ncr_numero'] ?>', event)"><?PHP echo $estado ?></td>
                <?php
            }
            ?>
            <td align="center">
                <?php
                if ($_SESSION[usuid] == 1) {
                    ?>
                    <img class="auxBtn" width="12px" src="../img/xml.png" onclick="auxWindow(6, '<?php echo $rst[ncr_id] ?>', '<?php echo $rst[ncr_clave_acceso] ?>')" />
                    <?php
                }
                if (strlen($rst['ncr_autorizacion']) != 37) {
                    if (empty($rst[ncr_id])) {
                        ?>
                        <img src="../img/upd.png"  class="auxBtn" width="12px" onclick="auxWindow(1, '<?php echo $rst[ncr_id] ?>', '<?php echo $rst[fac_id] ?>')">
                        <?php
                    }
                }
                if ($ident != 0) {
                    ?>
                    <img src="../img/orden.png" width="12px" class="auxBtn" onclick="auxWindow(2, '<?php echo $rst[ncr_id] ?>', '<?php echo $emisor ?>')">
                    <?php
                    if ($estaemail != 'ENVIADO') {
                        if ($_SESSION[usuid] == 1) {
                            ?>
                            <img src="../img/mail.png" width="12px" class="auxBtn" onclick="auxWindow(5, '<?php echo $num_secuencial ?>', '<?php echo $emisor ?>')">
                            <?PHP
                        }
                    }
                    ?>
                    <img src="../img/factura.png" width="12px" class="auxBtn" onclick="auxWindow(4, '<?php echo $rst[ncr_id] ?>', '<?php echo $emisor ?>')">
                    <?php
                }
                ?>                             
            </td>
            <?php
            if ($_SESSION[usuid] == 1) {
                ?>
                <td><?PHP echo $rst[ncr_observacion_aut] ?></td>
                <?php
            }
            ?>
        </tr>  
        <?PHP
//                }
        $f_total+=$tot_fac;
        $n_total+=$tot_nc;
    }
    ?>
</tbody>
<tr style="font-weight:bolder">
    <td colspan="9" align="right">Total</td>
    <td align="right" style="font-size:14px;"><?php echo number_format($n_total, 2) ?></td>
    <td align="right" style="font-size:14px;"><?php echo number_format($f_total, 2) ?></td>
    <td colspan="6"></td>
</tr>
</table>            
</body>   
</html>

