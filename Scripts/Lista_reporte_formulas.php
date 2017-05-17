<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reporte_formulas.php';
$Set = new Clase_reporte_formulas();
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($txt)) {
        $texto = "and (ord_numero like'%$txt%' or cli_raz_social like'%$txt%') ";
    } else {
        $texto = "and ord_fec_pedido between '$fec1' and '$fec2'";
    }
    $cns = $Set->lista_formula($texto);
} else {
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
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

           
        </script> 
        <style>
            #mn180{
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
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php?mod_id=$rst_sbm[opl_id]" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" ><?php echo "REPORTE DE FORMULAS" ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>" placeholder="ORDEN/CLIENTE"/>
                        DESDE:<input type="text" size="10" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="10" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>" />
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>                                                               
                    </form>  
                </center>
            </caption>

            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="3"></th>
                    <th style="width: 300px" colspan="2">TONILLO B</th>
                    <th style="width: 300px" colspan="2">TONILLO A</th>
                    <th style="width: 300px" colspan="2">TONILLO C</th>
                </tr>
                <tr>
                    <th style="width: 100px">FECHA</th>                                
                    <th style="width: 100px">ORDEN</th>
                    <th style="width: 200px">CLIENTE</th>
                    <th style="width: 200px">MATERIAL</th>
                    <th style="width: 50px">%</th>
                    <th style="width: 200px">MATERIAL</th>
                    <th style="width: 50px">%</th>
                    <th style="width: 200px">MATERIAL</th>
                    <th style="width: 50px">%</th>
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                $gr = "";
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_fr=  pg_fetch_array($Set->lista_formulacion($rst[ord_numero]));
                    if ($gr != $rst[ord_numero]) {
                        echo "<tr class='totales'>
                        <th style='width: 100px'>$rst[ord_fec_pedido] </th>
                        <th style='width: 100px'>$rst[ord_numero] </th>
                        <th style='width: 200px'>$rst[cli_raz_social] </th>
                        <th colspan='2' align='right' style='width: 300px'>" . number_format($rst_fr[ord_por_tornillo1], 2) . "</th>
                        <th colspan='2' align='right' style='width: 300px'>" . number_format($rst_fr[ord_por_tornillo2], 2) . "</th>
                        <th colspan='2' align='right' style='width: 300px'>" . number_format($rst_fr[ord_por_tornillo3], 2) . "</th>
                        </tr>";
                    }
                    echo "<tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$rst[mp1]</td>
                        <td align='right'>" . number_format($rst[ord_mf1], 2) . "</td>
                        <td>$rst[mp2]</td>
                        <td align='right'>" . number_format($rst[ord_mf7], 2) . "</td>
                        <td>$rst[mp3]</td>
                        <td align='right'>" . number_format($rst[ord_mf13], 2) . "</td>
                        </tr>";
                    $gr = $rst[ord_numero];
                }
                
                ?>
            </tbody>
        </table>            
    </body>    
</html>
<script>
    var e = '<?php echo $est ?>';
    $('#estado').val(e);
</script>
