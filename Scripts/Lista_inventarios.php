<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php'; //cambiar clsClase_productos
$Set = new Set();
if (isset($_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec2 = $_GET[fecha2];
    $bod = $_GET[bod];
    if ($bod == '') {
        $bod = 'no';
    }
    if (!empty($txt)) {
        $txt = "AND (ins.ins_b like '%$txt%' or ins.ins_a like '%$txt%' ) and mov_ubicacion='$bod'";
    } else {
        $txt = "AND mov_fecha_trans BETWEEN '1900-01-01' AND '$fec2' and mov_ubicacion='$bod'";
    }
    $cns = $Set->lista_movimientos_insumos($txt);
    $nm = trim(strtoupper($_GET[txt]));
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
            var ids = '<?php echo $bod ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                if (ids == 'no') {
                    alert('Elija Ubicacion');
                }
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
                <center class="cont_title" ><?PHP echo 'INVENTARIO DE MATERIA PRIMA' ?></center>
                <center class="cont_finder">

                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        UBICACION:
                        <select id="bod" name="bod" >
                            <option value="">SELECCIONE</option>
                            <option value="1">Costura</option>
                            <option value="2">Bodega2</option>
                            <option value="3">Bodega3</option>
                        </select>
                        CODIGO:<input type="text" name="txt" size="15" id="txt" value="<?php echo $nm ?>"/>
                        AL:<input type="text" name="fecha2" size="15" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
                <tr>
                    <th colspan="3">Producto terminado</th>
                    <th colspan="2">Total</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Refencia</th>
                    <th>Descripcion</th>
                    <th>Cantidad</th>
                    <!--<th>Total</th>-->
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $i = 0;
                $grup = '';
                while ($rst = pg_fetch_array($cns)) {
                    if ($grup != $rst[id]) {
                        $i++;
                        $res = pg_fetch_array($Set->total_ingreso_egreso_insumos1($rst[id], $fec2, $bod));
                        $t_can = $res[ingreso] - $res[egreso];
                        $t_uni = $res[p1] - $res[p2];
//                        $t_tot = $t_can * $t_uni;
                        $g_total+=$t_can;
//                        $g_total1+=$t_tot;
                        echo "<tr style='height: 20px' id='fila'>
                            <td>$i</td>
                            <td>$rst[ins_a]  </td>
                            <td>$rst[ins_b]</td>
                            <td align='right'>" . number_format($t_can, 2) . "</td>";
//                            <td align='right'>" . number_format($t_tot, 2) . "</td>
                        echo "</tr>";
                        $t_can = 0;
//                        $t_tot = 0;
                        $t_uni = 0;
                    }
                    $grup = $rst[id];
                }
                echo "<tr style='font-weight:bolder'>
                <td colspan='3' style='font-size:14px;' align='right'>Total</td>
                <td align='right' style='font-size:14px;'>" . number_format($g_total, 2) . "</td>";
//                <td align='right' style='font-size:14px;'>" . number_format($g_total1, 2) . "</td>
                echo "</tr>";
                ?>
            </tbody>
        </table>            
    </body>    
</html>
<script>
    var e = '<?php echo $bod ?>';
    $('#bod').val(e);
</script>


