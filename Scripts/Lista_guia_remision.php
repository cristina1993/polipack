<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_guia_remision.php'; //cambiar clsClase_productos
$Clase_guia_remision = new Clase_guia_remision();

if ($emisor >= 10) {
    $ems = '0' . $emisor . '-';
} else {
    $ems = '00' . $emisor . '-';
}
if (isset($_GET[txt], $_GET[fecha1], $_GET[fecha2], $_GET[fac])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fac = trim(strtoupper($_GET[fac]));
    $fec1 = trim($_GET[fecha1]);
    $fec2 = trim($_GET[fecha2]);

    if (!empty($txt)) {
        $text = "and g.emi_id=$emisor and (g.gui_identificacion like '%$txt%' or g.gui_nombre like '%$txt%' or g.gui_numero like '%$txt%')";
        $cns = $Clase_guia_remision->lista_buscador_guias_fac($text);
    } else if (!empty($fac)) {
        $text = "where emi_id=$emisor and (fac_identificacion like '%$fac%' or fac_nombre like '%$fac%' or fac_numero like '%$fac%')";
        $cns = $Clase_guia_remision->lista_buscador_facturas($text);
    } else {
        $text = "where emi_id=$emisor and fac_fecha_emision between '$fec1' and '$fec2' ";
        $cns = $Clase_guia_remision->lista_buscador_facturas($text);
    }
} else {
    $txt = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $text = " and g.emi_id=$emisor and g.gui_fecha_emision between '$fec1' and '$fec2'";
    $cns = $Clase_guia_remision->lista_buscador_guias_fac($text);
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
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }


            function auxWindow(a, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_guia_remision.php';//
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Lista_guias_factura.php?id=' + id + '&num=' + x //
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/Form_guia_remision.php?id=' + id + '&x=' + x;//
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

        </script> 
        <style>
            #mn65,
            #mn113,
            #mn118,
            #mn123,
            #mn128,
            #mn133,
            #mn138,
            #mn143,
            #mn148,
            #mn153{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input[type=text]{
                text-transform: uppercase;
            }

        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>   
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
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
                <center class="cont_title" ><?php echo "GUIAS DE REMISION BODEGA " . $bodega ?></center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>-->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="25" id="txt" value="<?php echo $txt ?>"/>
                        FACTURA NO.:<input type="text" name="fac" size="25" id="fac"  value="<?php echo $fac ?>"/>
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
            <th>Tipo</th>
            <th>Factura No.</th>
            <th>Guias Remision.</th>
            <th>Identificacion</th>
            <th>Cliente</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $grup = '';
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                if ($rst[fac_autorizacion] != '') {
                    $num_guias = pg_num_rows($Clase_guia_remision->lista_guias_factura($rst[fac_id]));
                    echo "<tr style='height: 30px'>
                        <td>$n</td>
                        <td align='center'>$rst[fac_fecha_emision]</td>
                        <td>FACTURA</td>
                        <td>$rst[fac_numero]</td>
                        <td align='center'>$num_guias</td>
                        <td>$rst[fac_identificacion]</td>
                        <td>$rst[fac_nombre]</td>
                        <td align='center'>";
                    if ($Prt->edition == 0 || $Prt->edition == 1) {
                        ?>
                    <img src="../img/upd.png" width="16px" class="auxBtn" onclick="auxWindow(1, '<?php echo $rst[fac_id] ?>', '<?php echo $rst[fac_numero] ?>', 0)">
                    <?php
                }

                echo "</td>
                </tr>";
            }
        }
        ?>
        </tbody>
    </table>            
</body>    
</html>

