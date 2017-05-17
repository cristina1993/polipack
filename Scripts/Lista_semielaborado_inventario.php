<?php
set_time_limit(0);
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_semielaborado_inventario.php'; //cambiar clsClase_productos
$Set = new Clase_semielaborado_inventario();
if (isset($_GET[txt], $_GET[fecha2])) {
    $nm = trim(strtoupper($_GET[txt]));
    $fec2 = $_GET[fecha2];
    if (!empty($nm)) {
        $txt = "and (pro_codigo like '%$nm%' or pro_descripcion like '%$nm%' or substring(m.mov_pago from  1 for 7) like '%$nm%') and m.mov_fecha_trans between '1900-01-01' and '$fec2'";
    } else {
        $txt = "and m.mov_fecha_trans between '1900-01-01' and '$fec2'";
    }

//    $fec = "and m.mov_fecha_trans between '1900-01-01' and '$fec2'";
    $cns = $Set->lista_buscar_inventariopt($txt);
    $Set->crear_vista($txt);
} else {
    $fec1 = date("Y-m-d");
    $fec2 = date("Y-m-d");
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
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script> 
        <style>
            #mn57,
            #mn62,
            #mn73,
            #mn78,
            #mn83,
            #mn88,
            #mn93,
            #mn98,
            #mn103,
            #mn108{
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
                <center class="cont_title" ><?PHP echo 'INVENTARIO DE PRODUCTO PERCHA' ?></center>
                <center class="cont_finder">

                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        CODIGO:<input type="text" name="txt" size="15" id="txt" value="<?php echo $nm ?>"/>
                        AL:<input type="text" name="fecha2" size="15" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
                <tr>
                    <th colspan="5">Producto</th>
                    <th colspan="2">Total Conforme</th>
                    <!--<th colspan="2">Total Inconforme</th>-->
                </tr>
                <tr>
                    <th style="width: 50px">No</th>
                    <th>Codigo</th>
                    <th>Orden</th>
                    <th>Descripción</th>
                    <th>Unidad</th>
                    <th>Rollos</th>
<!--                    <th>Peso</th>
                    <th>Rollos</th>-->
                    <th>Peso</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $i = 0;
                $g_total = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $i++;
                    $rst_inv = pg_fetch_array($Set->total_inventario($rst[pro_id], $fec, $rst[mov_pago]));
                    $inv_con = $rst_inv[ingreso_con] - $rst_inv[egreso_con];
                    $cnt_con = $rst_inv[cnt_con];
//                    $inv_inc = $rst_inv[ingreso_inc] - $rst_inv[egreso_inc];
//                    $cnt_inc = $rst_inv[cnt_inc];
//                      <td align='right'>" . number_format($cnt_inc, 2) . "</td>
//                            <td align='right'>" . number_format($inv_inc, 2) . "</td>
//                    <td align='right' style='font-size:14px;'>" . number_format($icnt_total, 2) . "</td>
//                <td align='right' style='font-size:14px;'>" . number_format($ig_total, 2) . "</td>
                    if ($cnt_con!=0 && $inv_con != 0) {
                        echo "<tr style='height: 20px' id='fila'>
                            <td>$i </td>
                            <td>$rst[pro_codigo]</td>
                            <td>$rst[mov_pago]</td>
                            <td>$rst[pro_descripcion]</td>
                            <td>$rst[pro_uni]</td>
                            <td align='right'>" . number_format($cnt_con, 2) . "</td>
                            <td align='right'>" . number_format($inv_con, 2) . "</td>
                        </tr> ";
                        $g_total+=$inv_con;
                        $cnt_total+=$cnt_con;
//                        $ig_total+=$inv_inc;
//                        $icnt_total+=$cnt_inc;
                    }
                }
                echo "<tr style='font-weight:bolder'>
                <td colspan='5' align='right' style='font-size:14px;'>Total</td>
                <td align='right' style='font-size:14px;'>" . number_format($cnt_total, 2) . "</td>
                <td align='right' style='font-size:14px;'>" . number_format($g_total, 2) . "</td>
                </tr>";
                ?>
            </tbody>
        </table>            
    </body>    
</html>



