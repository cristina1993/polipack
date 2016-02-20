<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_prod_pedido_venta.php';
$Docs = new Clase_prod_pedido_venta();
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $ord = trim(strtoupper($_GET[ord]));
    $cli = trim(strtoupper($_GET[cli]));
    $ruc = trim(strtoupper($_GET[ruc]));
    $est = $_GET[ped_estado];
    $fecha1 = $_GET[fecha1];
    $fecha2 = $_GET[fecha2];
    if (!empty($ord)) {
        $ord1 = "and ped_num_registro='$ord' and det_tab=$tabla";
        $cli = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($ord1);
    } else if (!empty($cli)) {
        $cli1 = "and ped_nom_cliente='$cli' and det_tab=$tabla";
        $ord = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($cli1);
    } else if (!empty($ruc)) {
        $ruc1 = "and ped_ruc_cc_cliente='$ruc' and det_tab=$tabla";
        $ord = '';
        $cli = '';
        $cns = $Docs->lista_buscador_orden($ruc1);
    } else if ($est != 'x') {
        $estado1 = "and det_estado='$est' and det_tab=$tabla";
        $cns = $Docs->lista_buscador_orden($estado1);
    } else {
        $ord1 = "and ped_femision between '$fecha1' and '$fecha2' and det_tab=$tabla";
        $cns = $Docs->lista_buscador_orden($ord1);
    }
} else {
//    $cns = $Docs->lista_registros_completo_pedidos();
    $fecha1 = date('Y-m-d');
    $fecha2 = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Ingreso Facturas</title>
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
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id, fab) {
                ord = $('#ord').val();
                cli = $('#cli').val();
                ruc = $('#ruc').val();
                f1 = $('#fecha1').val();
                f2 = $('#fecha2').val();
                e = $('#ped_estado').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 1://Orden
                        switch (fab)
                        {
                             case 1:
//                                frm.src = '../Scripts/Form_i_ordenes_geotextil.php?prod=' + id + '&ord=' + ord + '&cli=' + cli + '&ruc=' + ruc + '&fecha1=' + f1 + '&fecha2=' + f2 + '&ped_estado=' + e;
//                                look_menu();
                                break;
                            case 3:
                                frm.src = '../Scripts/Form_i_orden_plumon.php?prod=' + id + '&ord=' + ord + '&cli=' + cli + '&ruc=' + ruc + '&fecha1=' + f1 + '&fecha2=' + f2 + '&ped_estado=' + e;
                                look_menu();
                                break;
                            case 4:
                                frm.src = '../Scripts/Form_i_orden_padding.php?prod=' + id + '&ord=' + ord + '&cli=' + cli + '&ruc=' + ruc + '&fecha1=' + f1 + '&fecha2=' + f2 + '&ped_estado=' + e;
                                look_menu();
                                break;
                            case 5:
                                frm.src = '../Scripts/Form_i_orden_ecocambrella.php?prod=' + id + '&ord=' + ord + '&cli=' + cli + '&ruc=' + ruc + '&fecha1=' + f1 + '&fecha2=' + f2 + '&ped_estado=' + e;
                                look_menu();
                                break;
                            case 6:
                                frm.src = '../Scripts/Form_i_ordenes_geotextil.php?prod=' + id + '&ord=' + ord + '&cli=' + cli + '&ruc=' + ruc + '&fecha1=' + f1 + '&fecha2=' + f2 + '&ped_estado=' + e;
                                look_menu();
                                break;
                        }
                        break;
                    case 2://Facturar
                        frm.src = '../Scripts/Form_factura_pedventa.php?id=' + id + '&det=1';
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }
            }

            function asientos(d1) {

                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: 0, id: d1, x: 1},
                    success: function (dt) {
                        if (dt[0] == 0) {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_ord_pedido_venta.php?ord=' + ord + '&cli=' + cli + '&ruc=' + ruc + '&fecha1=' + f1 + '&fecha2=' + f2 + '&ped_estado=' + e;
                        } else {
                            alert(dt);
                        }

                    }
                });
            }

            function del(id, det)
            {
                var r = confirm("Esta Seguro de Suspender este elemento?");
                if (r == true) {
                    $.post("actions_prod_pedido_venta.php", {op: 0, id: id, data: det, sts: 5}, function (dt) {
                        if (dt == 0) {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_prod_pedido_venta.php';
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
        </script> 
        <style>
            #mn308{
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
                <center class="cont_title" >ORDENES DE PEDIDO DE VENTA <?php echo $bodega ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        ORDEN:<input type="text" name="ord" size="15" id="ord" value="<?php echo $ord ?>"/>
                        CLIENTE:<input type="text" name="cli" size="15" id="cli" value="<?php echo $cli ?>"/>
                        RUC/Cedula:<input type="text" name="ruc" size="15" id="ruc" value="<?php echo $ruc ?>"/>
                        ESTADO:
                        <select id="ped_estado" name="ped_estado">
                            <option value="x" >SELECCIONE</option>
                            <option value="1" >Aprobado</option>
                            <option value="4" >Facturado</option>
                            <option value="5" >Suspendido</option>
                            <option value="9" >Programado</option>
                        </select>
                        DESDE:<input type="text" size="10" name="fecha1" id="fecha1" value="<?php echo $fecha1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="10" name="fecha2" id="fecha2" value="<?php echo $fecha2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>                                                               
                    </form>  
                </center>
            </caption>

            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Fecha</th>
            <th>Orden de Venta</th>
            <th>Codigo</th>
            <th>Lote</th>                                
            <th>Descripcion</th>
            <th>Fabrica</th>
            <th>Solicitado</th>
            <th>Unidad</th>
            <th>Inventario</th>
            <th>Estado</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $grup = '';
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                switch ($rst[det_estado]) {
                    case '1':$estado = 'Aprobado';
                        break;
                    case '4':$estado = 'Facturado';
                        break;
                    case '5':$estado = 'Suspendido';
                        break;
                    case '9':$estado = 'Programado';
                        break;
                }
                switch ($rst[det_unidad]) {
                    case '1':$unidad = 'ROLLO';
                        break;
                    case '2':$unidad = 'KG';
                        break;
                    case '3':$unidad = 'M';
                        break;
                    case '4':$unidad = 'UNIDAD';
                        break;
                }
                $rst_inv = pg_fetch_array($Docs->total_ingreso_egreso_fac($rst[pro_id], $emisor, $rst[det_tab]));
                $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                if ($rst[det_tab] == 0) {
                    $prod = pg_fetch_array($Docs->lista_productos_industrial_id($rst[pro_id]));
                } else {
                    $prod = pg_fetch_array($Docs->lista_productos_noperti_id($rst[pro_id]));
                    $prod[emp_id] = 1;
                    $prod[emp_descripcion] = 'COMERCIAL';
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst[ped_femision] ?></td>
                    <td><?php echo $rst[ped_num_registro] ?></td>
                    <td><?php echo $rst[det_cod_producto] ?></td>
                    <td><?php echo $rst[det_lote] ?></td>
                    <td><?php echo $rst[det_descripcion] ?></td>
                    <td><?php echo $prod[emp_descripcion] ?></td>
                    <td align="right"><?php echo $rst[det_cantidad] ?></td>
                    <td align="center"><?php echo $unidad ?></td>
                    <td align="right"><?php echo $inv ?></td>
                    <td align="center" ><?php echo $estado ?></td>
                    <td align="center">
                        <?php
                        switch ($prod[emp_id]) {
                            case '1':$emp = '';
                                break;
                            case '3':$emp = 'erp_i_orden_produccion_plumon';
                                break;
                            case '4':$emp = 'erp_i_orden_produccion_padding';
                                break;
                            case '5':$emp = 'erp_i_orden_produccion';
                                break;
                            case '6':$emp = 'erp_i_orden_produccion_geotexti';
                                break;
                        }

                        if ($estado == 'Aprobado') {
                            if ($Prt->delete == 0) {
                                ?>
                                <img src="../img/b_delete.png" width="20px" title="Suspender" class="auxBtn" onclick="del(<?php echo $rst[ped_id] ?>, '<?php echo $rst[det_id] ?>')">
                                <?php
                            }
                            if ($estado != 'Facturado' && $estado != 'Programado') {
                                if ($Prt->edition == 0) {
                                    if ($prod[emp_id] != 1) {
                                        ?>
                                        <img src="../img/orden.png" width="20px" title="Generar Orden de Produccion" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[det_id] ?>,<?php echo $prod[emp_id] ?>)">
                                        <?php
                                    } else {
                                        ?>
                                        <img src="../img/ord_comp.jpg" width="20px" title="Generar Orden de Compara" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[det_id] ?>,<?php echo $prod[emp_id] ?>)">
                                        <?php
                                    }
                                }
                            }
                        }
                        if ($grup != $rst[ped_num_registro]) {
                            if ($estado != 'Facturado' && $estado != 'Suspendido') {
                                if ($Prt->edition == 0) {
                                    ?>
                                    <img class="auxBtn" width="16px" src="../img/facturar.png" onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)" title="Facturar"/>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </td>
                </tr>  
                <?PHP
                $grup = $rst[ped_num_registro];
            }
            ?>
        </tbody>
    </table>            
</body>    
</html>
<script>
    var e = '<?php echo $est ?>';
    $('#ped_estado').val(e);
</script>

