<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[txt])) {
    $_GET[txt] = strtoupper(trim($_GET[txt]));
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if (empty($_GET[txt])) {
        $txt = null;
        $date = "  AND oc.orc_fecha BETWEEN '$desde' and '$hasta'  ";
    } else {
        $txt = "  AND oc.orc_codigo like '%$_GET[txt]%'  ";
        $date = null;
    }
} else {
    //$cns=$Set->lista_ordenes_compra();    
    $desde = date("Y-m-d");
    $hasta = date("Y-m-d");
    $desde = date("Y-m-d");
    $hasta = date("Y-m-d");
    $date = "  AND oc.orc_fecha BETWEEN '$desde' and '$hasta'  ";
    $txt = null;
}
$cns = $Set->lista_ordenes_compra_search($txt, $date);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Orden de Compra de Prima</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 1:
                        frm.src = '../Scripts/Form_i_aprob_orden.php?id=' + id;
                        look_menu();
                        break;
                }
            }
            function aprobacion(a, id, doc) {
                if (a == 2) {
                    apb = 'APROBAR';
                } else {
                    apb = 'RECHAZAR';
                }
                var r = confirm("Esta Seguro de " + apb + " esta peticion?");
                if (r == true) {
                    $.post("actions.php", {act: 50, id: id, sts: a, data: doc}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_aprob_orden.php';
                        } else {
                            alert(dt);
                        }
                    });
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script> 
        <style>
            #mn40{
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
        <table style="" id="tbl" width='50%'>
            <caption class="tbl_head" >
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
                <center class="cont_title" >ORDENES DE COMPRA POR APROBAR</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" style="margin-top:5px; " autocomplete="off" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo:<input type="text" name="txt" size="15" />
                        DESDE:<input type="text"   name="desde" value="<?php echo $desde ?>"  id="desde" size="10" />
                        <img src="../img/calendar.png" width="16"  id="im-desde" />
                        HASTA:<input type="text"   name="hasta" value="<?php echo $hasta ?>"  id="hasta" size="10" />
                        <img src="../img/calendar.png" width="16"  id="im-hasta" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>

            </caption>

            <thead>
                <tr>
                    <th>No</th>
                    <th>Orden #</th>
                    <th>Fecha de Orden</th>
                    <th>Fecha Entrega</th>
                    <th>Observacion</th>
                    <th>Acciones</th>
                </tr>  
            </thead>
            <tbody>
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[orc_codigo] ?></td>                        
                        <td><?php echo $rst[orc_fecha] ?></td>
                        <td><?php echo $rst[orc_fecha_entrega] ?></td>
                        <td><?php echo $rst[orc_obs] ?></td>
                        <td align="center" >
                            <?php
                            if ($Prt->edition == 0) {
                                ?>
                                <img class="auxBtn" width="22px" src="../img/upd.png" onclick="auxWindow(1,<?php echo $rst[orc_id] ?>)">     
                                <?php
                            }
                            if ($Prt->edition == 0 && $rst[orc_estado] == 1) {
                                ?>
                                <img class="auxBtn" width="22px" src="../img/error.png" title="Rechazar" onclick="aprobacion(6,<?php echo $rst[orc_id] ?>, '<?php echo $rst[orc_codigo] ?>')" />                                    
                                <img class="auxBtn" width="22px" src="../img/exito.png" title="Aprobar"  onclick="aprobacion(2,<?php echo $rst[orc_id] ?>, '<?php echo $rst[orc_codigo] ?>')" />
                            <?php }
                            ?>
                        </td>
                    </tr>  
                    <?PHP
                }
                ?>    
            </tbody>


        </table>            

    </body>    
</html>

