<?php
session_start();
//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ord_pedido_venta.php';
$Docs = new Clase_ord_pedido_venta();
if (isset($_GET[search])) {
//    $ord = trim(strtoupper($_GET[ord]));
//    $cli = trim(strtoupper($_GET[cli]));
//    $ruc = trim(strtoupper($_GET[ruc]));
//    $estado = $_GET[ped_estado];
//    $ven = trim(strtoupper($_GET[ven]));
//    if (!empty($ord)) {
//        $ord = "where ped_num_registro='$ord'";
//        $cli = '';
//        $ruc = '';
//        $cns = $Docs->lista_buscador_orden($ord);
//    } else if (!empty($cli)) {
//        $cli = "where ped_nom_cliente='$cli'";
//        $ord = '';
//        $ruc = '';
//        $cns = $Docs->lista_buscador_orden($cli);
//    } else if (!empty($ruc)) {
//        $ruc = "where ped_ruc_cc_cliente='$ruc'";
//        $ord = '';
//        $cli = '';
//        $cns = $Docs->lista_buscador_orden($ruc);
//    } else if ($estado != 'x') {
//        $estado = "where ped_estado='$estado' and tipo_cliente=0";
//        $cns = $Docs->lista_buscador_orden($estado);
//    } else {
//        $ven = "where ped_vendedor='$ven' ";
//        $cns = $Docs->lista_buscador_orden($ven);
//    }
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $cns = $Docs->lista_buscador_orden_movil($desde, $hasta);
} else {
    $cns = $Docs->lista_registros_completo_pedidos_pendiente();
    $desde = date('d-m-Y');
    $hasta = date('d-m-Y');
}
?>
<!doctype html>
<html class="ui-mobile">
    <head>
        <meta charset='utf-8'>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Menu</title>
        <link href="../menu/files/jquery.css" rel="stylesheet">
        <link href="../css/style_movil.css" rel="stylesheet">
        <script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
        <script>
            emi = '<?php echo $emisor ?>';
            $(function () {
                $("#tbl_movil").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
            });

            function auxWindow(a, id, x) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 2://Editar
                        window.location = 'Form_ord_pedido_venta.php?id=' + id + '&x=' + x;
                        look_menu();
                        break;
                }
            }

            function cambiar_estado(act, id, ped, emi) {
                main = parent.document.getElementById('mainFrame');
                if (act == 1) {
                    sms = confirm("Se Aprobara el pedido " + ped + " \n Desea Continuar?");
                    sts = 1;
                } else if (act == 0) {
                    sms = confirm("El Pedido " + ped + " sera rechazado \n Desea Continuar?");
                    sts = 2;
                }
                if (sms == true) {
                    $.post("../Scripts/actions_ord_pedido_venta.php", {op: 4, id: id, sts: sts, emi: emi}, function (dt) {
                        if (dt == 0) {
                            window.location = '../Scripts_Movil/Lista_aut_pedido_venta.php';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }
            function back() {
                window.location = "../menu/menu_movil.php";
            }
        </script> 
    </head>
    <body class="ui-mobile-viewport ui-overlay-c">
        <div class="ui-page ui-body-c ui-page-header-fixed ui-page-active" data-role="page" data-url="/iOS-Inspired-jQuery-Mobile-Theme/" tabindex="0" style="min-height: 912px;"></div>
        <div id="Gerencial" class="ui-page ui-body-c ui-page-header-fixed ui-page-active" data-role="page" data-url="Gerencial" tabindex="0" style="padding-top: 44px; min-height: 912px;">
            <div class="ui-header ui-bar-a ui-header-fixed slidedown" data-position="fixed" data-role="header" role="banner">
                <h1 class="ui-title" role="heading" aria-level="1"></h1>
                <a class="ui-btn-left ui-btn ui-shadow ui-btn-corner-all ui-btn-up-a" data-theme="a" data-rel="back" href="#" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span">
                    <span class="ui-btn-inner ui-btn-corner-all">
                        <span class="ui-btn-text" onclick="back()">Back</span>
                    </span>
                </a>
            </div>
            <table style="width:100%" id="tbl_movil">
                <caption  class="tbl_head">
                    <center class="cont_title" >AUTORIZACIÓN DE PEDIDOS DE VENTA</center>
                    <center class="cont_finder">
                        <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                            Desde:<input type="date" value="<?php echo $desde ?>" name="desde" id="desde" style="width:60px;font-size:8px"/>
                            Hasta:<input type="date" value="<?php echo $hasta ?>" name="hasta" id="hasta" style="width:60px;font-size:8px"/>
                            <input type="submit" name="search" id="search" value="Buscar" />
                        </form>  
                    </center>
                </caption>
                <!--Nombres de la columna de la tabla-->
                <thead>
                <th>No</th>
                <th>Cliente</th>                                
                <th>Local</th>
                <th>Vendedor</th>
                <th>Tot.$</th>
                <th>Desc.%</th>
                <th>Estado</th>
                </thead>
                <!------------------------------------->

                <tbody id="tbody">
                    <?PHP
                    $n = 0;
                    while ($rst = pg_fetch_array($cns)) {
                        $n++;
                        switch ($rst[ped_local]) {
                            case '1':$local = 'Noperti';
                                break;
                            case '10':$local = 'Industrial';
                                break;
                            case '2':$local = 'Condado';
                                break;
                            case '3':$local = 'Quicentro Sur Shopping';
                                break;
                            case '4':$local = 'Mall del Sol';
                                break;
                            case '5':$local = 'Shopping Machala';
                                break;
                            case '6':$local = 'Riocentro Norte';
                                break;
                            case '7':$local = 'San Marino Shopping';
                                break;
                            case '8':$local = 'City Mall';
                                break;
                            case '9':$local = 'Quicentro Shopping';
                                break;
                            case '11':$local = 'Top Tenis';
                                break;
                            case '12':$local = 'Recreo';
                                break;
                            case '13':$local = 'CCNU';
                                break;
                            case '14':$local = 'Atahualpa';
                                break;
                        }
                        switch ($rst[ped_estado]) {
                            case '0':$estado = 'Pendiente';
                                break;
                            case '1':$estado = 'Aprobado';
                                break;
                            case '2':$estado = 'Rechazado';
                                break;
                        }
                        ?>
                        <tr>
                            <td><?php echo $n ?></td>
                            <td onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo $rst[ped_nom_cliente] ?></td>
                            <td onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo $local ?></td>
                            <td onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo $rst[ped_vendedor] ?></td>
                            <td align="right" onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo number_format($rst[ped_total], 2) ?></td>
                            <td align="right" onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo number_format($rst[ped_desc_asolicitar], 1) . '%' ?></td>
                            <td align="center" width="50px">
                                <?php
                                if ($estado == 'Pendiente') {
                                    ?>
                                    <font class="auxBtn green" onclick="cambiar_estado(1,<?php echo $rst[ped_id] ?>, '<?php echo $rst[ped_num_registro] ?>', '<?php echo $rst[ped_local] ?>')" >&#8730;</font>
                                    <font class="auxBtn red" onclick="cambiar_estado(0,<?php echo $rst[ped_id] ?>, '<?php echo $rst[ped_num_registro] ?>')" >&#1093;</font>                        
                                    <?php
                                }else{
                                    echo $estado;
                                }
                                ?>
                            </td>
                        </tr>  
                        <?PHP
                    }
                    ?>
                </tbody>
            </table>            

        </div>
    </body>
</html>        


