<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nota_debito.php'; //cambiar clsClase_productos
//include_once '../Clases/clsSetting.php';
$Clase_nota_debito = new Clase_nota_debito();
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
        $texto = "and n.emi_id=$emisor and(n.ndb_identificacion like '%$txt%' or n.ndb_nombre like '%$txt%' or n.ndb_numero like '%$txt%' or n.ndb_num_comp_modifica like '%$txt%')";
        $ident = 1;
        $cns = $Clase_nota_debito->lista_buscador_notas_debito($texto);
    } else if (!empty($fac)) {
        $texto = "and f.emi_id=$emisor and(f.fac_identificacion like '%$fac%' or f.fac_nombre like '%$fac%' or f.fac_numero like '%$fac%')";
        $ident = 0;
        $cns = $Clase_nota_debito->lista_buscador_facturas($texto);
    } else {
        $texto = "and n.emi_id=$emisor and n.ndb_fecha_emision between '$fec1' and '$fec2' ";
        $ident = 1;
        $cns = $Clase_nota_debito->lista_buscador_notas_debito($texto);
    }
} else {
    $txt = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $texto = "and n.emi_id=$emisor and n.ndb_fecha_emision between '$fec1' and '$fec2' ";
    $cns = $Clase_nota_debito->lista_buscador_notas_debito($texto);
    $ident = 1;
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


            function auxWindow(a, id, x, e)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 1://Editar
                        frm.src = '../Scripts/Form_nota_debito.php?id=' + id + '&x=' + x;//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 2://Reporte
                        frm.src = '../Scripts/Form_i_pdf_nota_debito.php?id=' + id + '&x=' + x;
                        parent.document.getElementById('contenedor2').rows = "*,80%";

                        break;
                    case 3://XML
                        if (e == 0) {
                            id = x;
                            tp = 0;
                        } else {
                            id = id;
                            tp = 5;
                        }
                        loading('visible');
                        window.location = '../Reports/descargar_xml.php?id=' + id + '&tp=' + tp;
                        loading('hidden');
                        break;
                    case 4://email
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {

                            },
                            type: 'GET',
                            url: '../Reports/pdf_nota_debito.php',
                            data: {id: id, x: e, val: 1},
                            success: function (dt) {
                                loading('hidden');
                                $.post("actions.php", {act: 77, dt: dt, id: id},
                                function (dt) {
                                    if (dt == 0) {
                                        alert('Nota de Debito Enviada Correctamente');
                                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_debito.php';
                                    } else {
                                        alert(dt);
                                    }
                                });
                            }
                        });

                        break;


                }
            }
            function envia_mail(id, e) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'GET',
                    url: '../Reports/pdf_nota_debito.php',
                    data: {id: id, x: e, val: 1},
                    success: function (dt) {
                        loading('hidden');
                        $.post("actions.php", {act: 75, dt: dt, id: id},
                        function (dt) {
                            if (dt == 0) {
                                alert('Nota de Debito Enviada Correctamente');
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_debito.php';
                            } else {
                                alert(dt);
                            }
                        });
                    }
                });
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function del(id) {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_nota_debito.php", {op: 3, id: id}, function (dt) {
                        if (dt == 0) {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_debito.php';
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
                        url: 'actions_nota_debito.php',
                        data: {op: 4, id: id}, //op sera de acuerdo a la acion que le toque
                        success: function (dt) {
                            tbl_aux.style.display = 'none';
                            if (dt == 0) {
                                window.location = 'Lista_nota_debito.php';
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
            #mn67,
            #mn115,
            #mn120,
            #mn125,
            #mn130,
            #mn135,
            #mn140,
            #mn145,
            #mn150,
            #mn155{
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
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="mensaje" ></div>

        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <table id="tbl_aux" style="border: solid 2px black">
            <tr>
                <td colspan="2" style="font-weight:bolder ">Anulación de Documento<img src="../img/b_delete.png" style="float:right;cursor: pointer" onclick="tbl_aux.style.display = 'none', cod_anular.value = ''"  /></td>
            </tr>
            <tr>
                <td>Nota Debito # </td>
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
                <center class="cont_title" ><?php echo "NOTAS DE DEBITO BODEGA " . $bodega ?></center>
                <center class="cont_finder">

                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="25" id="txt" value="<?php echo $txt ?>" />
                        FACTURA NO.:<input type="text" name="fac" size="25" id="fac" value="<?php echo $fac ?>"/>
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
            <th>Nota debito No.</th>            
            <th>Tipo</th>
            <th>Factura No.</th>
            <th>Identificacion</th>
            <th>Cliente</th>
            <th>Total Nota Deb. $</th>
            <th>Total Factura $</th>
            <th>Usuario</th>
            <th>ESTADO</th>
<!--            <th>NUM AUTORIZACION SRI</th>-->
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $grup = '';
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                if ($ident == 0) {
                    $rst_not = pg_fetch_array($Clase_nota_debito->lista_un_nota_fac($rst[fac_id]));
                    $fecha = $rst[fac_fecha_emision];
                    $numero = $rst_not[ndb_numero];
                    $vendedor = $rst_not[vnd_nombre];
                    $num_factura = $rst[fac_numero];
                    $fac_vend = $rst[vnd_nombre];
                    $identificacion = $rst[fac_identificacion];
                    $nombre = $rst[fac_nombre];
                    $tot_nc = $rst_not[ndb_total_valor];
                    $tot_fac = $rst[fac_total_valor];
                    $rst[ndb_id] = $rst_not[ndb_id];
                } elseif ($ident == 1) {
                    $rst_fact = pg_fetch_array($Clase_nota_debito->lista_una_factura_id($rst[fac_id]));
                    $fecha = $rst[ndb_fecha_emision];
                    $numero = $rst[ndb_numero];
                    $vendedor = $rst[vnd_nombre];
                    $num_factura = $rst[ndb_num_comp_modifica];
                    $fac_vend = $rst[vnd_nombre];
                    $identificacion = $rst[ndb_identificacion];
                    $nombre = $rst[ndb_nombre];
                    $tot_fac = $rst_fact[fac_total_valor];
                    $tot_nc = $rst[ndb_total_valor];
                }
//CONTROL DE ERRRORES CORREO***********************************
                $estaemail = $rst[ndb_estado_correo];
                $nomemi = $rst[nombre];

                if ($estaemail == 'ERROR AL ENVIAR') {
                    $estaemail = 'ERROR AL ENVIAR';
                }
                if ($estaemail == 'PENDIENTE DE ENVIAR') {
                    $estaemail = 'PENDIENTE DE ENVIAR';
                }
                if ($estaemail == 'ENVIADO') {
                    $estaemail = 'ENVIADO';
                } else {
                    $estaemail = $rst[ndb_estado_correo];
                }
                if (strlen(trim($rst[ndb_autorizacion])) == 37) {
                    $estdado = 'RECIBIDA AUTORIZADO';
                    $tp = 0;
                } else {
                    $tp = 1;
                    if (strlen($rst[ndb_estado_aut]) > 20) {
                        $estdado = substr($rst[ndb_estado_aut], 0, 20);
                    } else {
                        $estdado = $rst[ndb_estado_aut];
                    }
                }

                echo "<tr>
                    <td>$n</td>
                    <td align='center'>$fecha</td>
                    <td align='center'>$numero</td>
                    <td>FACTURA</td>
                    <td>$num_factura</td>
                    <td>$identificacion</td>
                    <td>$nombre</td>
                    <td align='right' style='font-size:14px;font-weight:bolder'>" . number_format($tot_nc, 2) . "</td>
                    <td align='right' style='font-size:14px;font-weight:bolder'>" . number_format($tot_fac, 2) . "</td>
                    <td>$vendedor</td>";

                if ($rst[ndb_estado_aut] == 'ANULADO' || empty($rst[ndb_estado_aut])) {
                    ?>
                <td style="color:darkred;font-weight:bolder "><?PHP echo $rst[ndb_estado_aut] ?></td>
                <?php
            } else {
                ?>
                <td style="color:darkred;font-weight:bolder " ondblclick="cargar_datos('<?php echo $rst['ndb_id'] ?>', '<?php echo $rst['ndb_numero'] ?>', event)"><?PHP echo $rst[ndb_estado_aut] ?></td>
                <?php
            }
            ?>
            <td align="center">
                <?php
                if ($ident != 0) {
                    ?>  
                    <img src="../img/orden.png"  class="auxBtn" width="12px" onclick="auxWindow(2, '<?php echo $rst[ndb_id] ?>', '<?php echo $emisor ?>')"> 
                    <?php
                    if ($_SESSION[usuid] == 1) {
                    ?>
                    <img class="auxBtn" width="12px" src="../img/xml.png" onclick="auxWindow(3, '<?php echo $rst[ndb_id] ?>', '<?php echo $rst[ndb_clave_acceso] ?>', '<?php echo $tp ?>')" />
                    <?php
                    }
                    if ($estaemail != 'ENVIADO') {
                        if ($_SESSION[usuid] == 1) {
                            ?>
                            <img src="../img/mail.png"  class="auxBtn" width="12px" onclick="auxWindow(4, '<?php echo $num_secuencial ?>', '<?php echo $emisor ?>')">
                            <?PHP
                        }
                    }
                    ?>
                    <?php
                }
                if (strlen($rst[ndb_autorizacion]) != 37) {
                    if (empty($rst[ndb_id])) {
                        ?>                             
                        <img src="../img/upd.png"  class="auxBtn" width="16px" onclick="auxWindow(1, '<?php echo $rst[ndb_id] ?>', '<?php echo $rst[fac_id] ?>', 0)">   
                        <?php
                    }
                }
                ?>  
            </td> 
            <?php
            if ($_SESSION[usuid] == 1) {
                ?>
                <td><?PHP echo $rst[ndb_observacion_aut] ?></td>
                <?php
            }
            ?>
        </tr>  
        <?PHP
        $f_total+=$tot_fac;
        $n_total+=$tot_nc;
    }
    ?>
</tbody>
<tr style="font-weight:bolder">
    <td colspan="7" align="right">Total</td>
    <td align="right" style="font-size:14px;"><?php echo number_format($n_total, 2) ?></td>
    <td align="right" style="font-size:14px;"><?php echo number_format($f_total, 2) ?></td>
    <td colspan="6"></td>
</tr>
</table>            
</body>   
</html>

