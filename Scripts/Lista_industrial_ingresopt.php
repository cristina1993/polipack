<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_ingresopt.php'; //cambiar clsClase_productos
$Ing = new Clase_industrial_ingresopt();
if (isset($_GET[txt], $_GET[prod], $_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $prod = trim(strtoupper($_GET[prod]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($prod)) {
        $ncom = pg_num_rows($Ing->lista_buscar_comerciales($prod));
        if ($ncom > 0) {
            $cns = $Ing->lista_buscar_comerciales($prod);
            $tab = 1;
        }
        $nind = pg_num_rows($Ing->lista_buscar_industriales($prod));
        if ($nind > 0) {
            $cns = $Ing->lista_buscar_industriales($prod);
            $tab = 0;
        }
        $fec1 = '';
        $fec2 = '';
        $txt = '';
        $det = 1;
    } else if (!empty($txt)) {
        $txt = " and (m.mov_documento like '%$txt%')";
        $fec1 = '';
        $fec2 = '';
        $prod = '';
        $cns = $Ing->lista_buscador_industrial_ingresopt($emisor, $txt);
        $n_prod = pg_num_rows($Ing->lista_num_productos($emisor, $txt));
        $det = 0;
    } else {
        $txt = " and m.mov_fecha_trans between '$fec1' and '$fec2' ";
        $cns = $Ing->lista_buscador_industrial_ingresopt($emisor, $txt);
        $n_prod = pg_num_rows($Ing->lista_num_productos($emisor, $txt));
        $det = 0;
    }
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    $prod = trim(strtoupper($_GET[prod]));
    $nm = trim(strtoupper($_GET[txt]));
} else {
    $txt = '';
    $trs = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $txt = " and m.mov_fecha_trans between '$fec1' and '$fec2' ";
    $cns = $Ing->lista_buscador_industrial_ingresopt($emisor, $txt);
    $n_prod = pg_num_rows($Ing->lista_num_productos($emisor, $txt));
    $det = 0;
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
                        frm.src = '../Scripts/Form_industrial_ingresopt.php';//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        //look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_industrial_ingresopt.php?id=' + id + '&x=' + x;//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        break;
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script> 
        <style>
            #mn54,
            #mn59,
            #mn70,
            #mn75,
            #mn80,
            #mn85,
            #mn90,
            #mn95,
            #mn100,
            #mn105{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
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
                <center class="cont_title" ><?PHP echo 'INGRESO DE PRODUCTO TERMINADO ' . $bodega ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        PRODUCTO:<input type="text" name="prod" size="15" id="prod" value="<?php echo $prod ?>" />
                        MOVIMIENTO:<input type="text" name="txt" size="15" id="txt" value="<?php echo $nm ?>"/>
                        DESDE:<input type="text" size="15" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="15" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
                <tr>
                    <th></th>
                    <th colspan="3">Documento</th>
                    <th colspan="5">Producto Terminado</th>
                    <th colspan="4">Transaccción</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Usuario</th>
                    <th>Fecha de Transacción</th>
                    <th>Documento No</th>
                    <th>Proveedor</th>
                    <th>Familia</th>
                    <th>Código</th>
                    <th>Lote</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $n = 0;
                $grup = '';
                if ($det == 0) {
                    while ($rst = pg_fetch_array($cns)) {
                        $n++;
                        $t_cnt+=$rst['mov_cantidad'];
                        echo "<tr>
                            <td>$n</td>";

                        if ($grup != $rst['mov_documento']) {
                            echo "<td>$rst[mov_usuario]</td>
                            <td align='center'>$rst[mov_fecha_trans]</td>
                                <td>$rst[mov_documento]</td>
                                <td>$rst[cli_raz_social]</td>";
                        } else {
                            echo "<td></td>
                                <td></td>
                                <td></td>
                                <td></td>";
                        }
                        if ($rst[mov_tabla] == 1) {
                            $rst1 = pg_fetch_array($Ing->lista_prod_comerciales($rst['pro_id']));
                            $rst['pro_codigo'] = $rst1['pro_a'];
                            $rst['pro_descripcion'] = $rst1['pro_b'];
                            $fl = explode('&', $rst1['pro_tipo']);
                            $fml = $fl[9];
                            $lote = $rst1['pro_ac'];
                        } else {
                            $rst1 = pg_fetch_array($Ing->lista_prod_industriales($rst['pro_id']));
                            $rst['pro_codigo'] = $rst1['pro_codigo'];
                            $rst['pro_descripcion'] = $rst1['pro_descripcion'];
                            $fml = '';
                            $lote = '';
                        }
                        echo "<td>$fml</td>     
                            <td>$rst[pro_codigo]</td>                    
                            <td>$lote</td>                    
                            <td>$rst[pro_descripcion]</td>
                            <td>$rst[trs_descripcion]</td>
                            <td align='right'>$rst[mov_cantidad]</td>
                        </tr>";

                        $grup = $rst['mov_documento'];
                    }
                } else {
                    while ($rst1 = pg_fetch_array($cns)) {
                        if ($tab == 1) {
                            $rst1[pro_id] = $rst1[id];
                            $rst1['pro_codigo'] = $rst1['pro_a'];
                            $rst1['pro_descripcion'] = $rst1['pro_b'];
                            $fl = explode('&', $rst1['pro_tipo']);
                            $fml = $fl[9];
                            $lote = $rst1['pro_ac'];
                        } else {
                            $rst1['pro_codigo'] = $rst1['pro_codigo'];
                            $rst1['pro_descripcion'] = $rst1['pro_descripcion'];
                            $fml = '';
                            $lote = '';
                            $rst1[pro_id] = $rst1[pro_id];
                        }
                        $mov = pg_num_rows($Ing->buscar_un_movimiento($rst1[pro_id], $tab, $emisor));
                        if ($mov > 0) {
                            $cns1 = $Ing->buscar_un_movimiento($rst1[pro_id], $tab, $emisor);
                            while ($rst = pg_fetch_array($cns1)) {
                                $n++;
                                $t_cnt+=$rst['mov_cantidad'];
                                if ($g_id != $rst1[pro_id]) {
                                    $n_prod += 1;
                                }
                                echo "<tr>
                                    <td>$n</td>";

                                if ($grup != $rst['mov_documento']) {

                                    echo "<td>$rst[mov_usuario]</td>
                                    <td align='center'>$rst[mov_fecha_trans]</td>
                                        <td>$rst[mov_documento]</td>
                                        <td>$rst[cli_raz_social]</td>";
                                } else {

                                    echo "<td></td>
                                <td></td>
                                <td></td>
                                <td></td>";
                                }
                                echo "<td>$fml</td>     
                            <td>$rst1[pro_codigo]</td>                    
                            <td>$lote</td>                    
                            <td>$rst1[pro_descripcion]</td>
                            <td>$rst[trs_descripcion]</td>
                            <td align='right'>$rst[mov_cantidad]</td>
                        </tr> ";
                                $grup = $rst['mov_documento'];
                                $g_id = $rst1[pro_id];
                            }
                        }
                    }
                }
                ?>
            </tbody>
            <?php
            echo "<tr>
        <td class='totales' ></td>
        <td class='totales' ></td>
        <td class='totales' ></td>
        <td class='totales' ></td>
        <td class='totales' ></td>
        <td class='totales' ></td>
        <td class='totales' ></td>
        <td class='totales' >Total</td>                                
        <td class='totales' >Items " . number_format($n, 0) . "</td>
        <td class='totales' >PRODUCTOS " . number_format($n_prod, 0) . "</td>
        <td class='totales' align='right' >" . number_format($t_cnt, 2) . "</td>
    </tr>";
            ?>
        </table>            
    </body>    
</html>

