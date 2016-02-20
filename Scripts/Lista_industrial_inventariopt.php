<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_inventariopt.php'; //cambiar clsClase_productos
$Clase_industrial_inventariopt = new Clase_industrial_inventariopt();
if (isset($_GET[txt], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec2 = $_GET[fecha2];
    if (!empty($txt)) {
        $cns = $Clase_industrial_inventariopt->lista_productos($txt);
        $det = 1;
    } else {
        if ($fec2 == date('Y-m-d')) {
            $cns = $Clase_industrial_inventariopt->lista_inventariopt_tot($emisor, $fec2);
        } else {
            $txt = " erp_consulta_inv where cod_punto_emision= $emisor and con_fecha ='$fec2'";
            $cns = $Clase_industrial_inventariopt->lista_buscar_inventariopt($txt);
        }
        $det = 0;
    }
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


            function auxWindow(a)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0:
                        main.src = '../Scripts/Lista_industrial_ingresopt.php';
                        break;
                    case 1:
                        main.src = '../Scripts/Lista_industrial_egresopt.php';
                        break;
                    case 2:
                        main.src = '../Scripts/Lista_industrial_movimientopt.php';
                        break;
                    case 3:
                        main.src = '../Scripts/Lista_industrial_inventariopt.php';
                        break;
                    case 4:
                        main.src = '../Scripts/Lista_industrial_kardexpt.php';
                        break;
                }
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
                <center class="cont_title" ><?PHP echo 'INVENTARIO DE PRODUCTO TERMINADO ' . $bodega ?></center>
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
                    <th colspan="7">Producto terminado</th>
                    <th colspan="1">Totales</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Familia</th>
                    <th>Codigo</th>
                    <th>Lote</th>
                    <th>Descripción</th>
                    <th>Unidad</th>
                    <th>Peso</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $i = 0;
                $g_total = 0;
                if ($det == 0) {
                    while ($rst = pg_fetch_array($cns)) {
                        $i++;
                        if ($rst[pro_tbl] == 1) {
                            $rst_com = pg_fetch_array($Clase_industrial_inventariopt->lista_prod_comerciales($rst[pro_id]));
                            $fl = explode('&', $rst_com[pro_tipo]);
                            $fml = $fl[9];
                            $cod = $rst_com[pro_a];
                            $des = $rst_com[pro_b];
                            $lote = $rst_com[pro_ac];
                            $unid = $rst_com[pro_x];
                            $peso = '';
                        } else {
                            $rst_ind = pg_fetch_array($Clase_industrial_inventariopt->lista_prod_industriales($rst[pro_id]));
                            $fml = 'INDUSTRIAL';
                            $cod = $rst_ind[pro_codigo];
                            $des = $rst_ind[pro_descripcion];
                            $lote = '';
                            $unid = $rst_ind[pro_uni];
                            $peso = number_format($rst_ind[pro_peso],2);
                        }
                        echo "<tr style='height: 20px' id='fila'>
                            <td>$i </td>
                            <td>$fml </td>
                            <td>$cod </td>
                            <td>$lote </td>
                            <td>$des</td>
                            <td>$unid</td>
                            <td>$peso</td>
                            <td align='right'>" . number_format($rst[mvt_cant], 2) . "</td>
                        </tr> ";
                        $g_total+=$rst[mvt_cant];
                    }
                } else {
                    while ($rst_pro = pg_fetch_array($cns)) {
                        $i++;
                        $pro = $rst_pro[id];
                        $cod = $rst_pro[pro_a];
                        $des = $rst_pro[pro_b];
                        $lote = $rst_pro[pro_ac];
                        $unid = '';
                        $peso = '';
                        $fl = explode('&', $rst_pro[tipo]);
                        $fml = $fl[9];
                        $tab = $rst_pro[tab];
                        $mov = pg_fetch_array($Clase_industrial_inventariopt->buscar_un_movimiento($pro, $tab, $emisor));
                        if ($mov[pro_id] != '') {
                            echo "<tr style='height: 20px' id='fila'>
                                <td>$i</td>
                                <td>$fml</td>
                                <td>$cod</td>
                                <td>$lote</td>
                                <td>$des</td>
                                <td>$unid</td>
                                <td>$peso</td>
                                <td align='right'>" . number_format($mov[mvt_cant], 2) . "</td>
                            </tr> ";
                            $g_total+=$mov[mvt_cant];
                        }
                    }
                }
                echo "<tr style='font-weight:bolder'>
                <td colspan='7' align='right' style='font-size:14px;'>Total</td>
                <td align='right' style='font-size:14px;'>".number_format($g_total, 2)."</td>
                </tr>";
                ?>
            </tbody>
        </table>            
    </body>    
</html>



