<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_pedidospt.php';
$Clase_pedidospt = new Clase_pedidospt();
if (isset($_GET[txt], $_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($txt)) {
        $txt = " and (p.pro_codigo like '%$txt%' or m.ped_documento like '%$txt%' or c.cli_raz_social like '%$txt%' or p.pro_descripcion like '%$txt%' or p.pro_uni like '%$txt%' or p.pro_uni2 like '%$txt%')";
        $fec1 = '';
        $fec2 = '';
    } else {
        $txt = " and m.ped_fecha_registro between '$fec1' and '$fec2' ";
    }
    //$cns = $Clase_pedidospt->lista_buscador_pedido($txt);
} else {
    $txt = '';
    $cns = $Clase_pedidospt->lista_pedido_cliente($id_cli);
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
                $('#fecha1').val('<?php echo date('Y-m-d'); ?>');
                $('#fecha2').val('<?php echo date('Y-m-d'); ?>');
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
                        frm.src = '../Scripts/Form_pedidospt.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_pedidospt.php?id=' + id + '&x=' + x;
                        break;
                }

            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

        </script> 
        <style>
            #mn157,
            #mn159,
            #mn161,
            #mn163,
            #mn165,
            #mn167,
            #mn169,
            #mn171,
            #mn173,
            #mn175,
            #mn111{
                background:black;
                color:white;
                border: solid 1px white;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
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
                <center class="cont_title" ><?PHP echo 'LISTA DE PEDIDOS BODEGA ' . $bodega ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" />
                        DESDE:<input type="text" size="15" name="fecha1" id="fecha1" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="15" name="fecha2" id="fecha2" />
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>

                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th></th>
                    <th colspan="4">Documento</th>
                    <th colspan="4">Producto Terminado</th>
                    <th colspan="2"></th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Fecha de Transaccion</th>
                    <th>Orden de Venta</th>
                    <th>Documento</th>
                    <th>Bodega</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Presentacion</th>
                    <th>Unidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>                    
                </tr>

            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                $grup = '';
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    if ($rst['ped_estado'] == 3) {
                        $rst['ped_estado'] = PENDIENTE;
                    }
                    if ($rst['ped_estado'] == 2) {
                        $rst['ped_estado'] = COMPLETADO;
                    }
                    if ($rst['ped_estado'] == 1) {
                        $rst['ped_estado'] = ENPROCESO;
                    }
                    ?>
                    <tr  id="fila" onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 1)">
                        <td><?php echo $n ?></td>
                        <?PHP
                        if ($grup != $rst['ped_documento']) {

                            $rst_bod = pg_fetch_array($Clase_pedidospt->lista_una_bodega($rst['bod_id']));
                            ?>
                            <td><?php echo $rst['ped_fecha_registro'] ?></td>
                            <td><?php echo $rst['ped_documento'] ?></td>
                            <td><?php echo $rst['ped_documento'] ?></td>
                            <td><?php echo $rst_bod[nombre_comercial] ?></td>

                            <?php
                        } else {
                            ?>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <?php
                        }
                        ?>
                        <td><?php echo $rst['pro_codigo'] ?></td>
                        <td><?php echo $rst['pro_descripcion'] ?></td>
                        <td><?php echo $rst['pro_uni'] ?></td>
                        <td><?php echo $rst['pro_uni2'] ?></td>
                        <td><?php echo $rst['ped_estado'] ?></td>             
                        <td>
                            <img class="auxBtn" width="16px" src="../img/del.png" onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 0)"/>
                            <img class="auxBtn" width="16px" src="../img/upd.png" onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 0)"/>
                        </td>             
                    </tr>  
                    <?PHP
                    $grup = $rst['ped_documento'];
                }
                ?>
            </tbody>
        </table>            
    </body>    
</html>

