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
        $text = " and (pro_codigo like '%$prod%' or pro_descripcion like '%$prod%' or mov_pago like '%$prod%')";
    } else if (!empty($txt)) {
        $text = " and (m.mov_documento like '%$txt%')";
    } else {
        $text = " and m.mov_fecha_trans between '$fec1' and '$fec2' ";
    }
    $cns = $Ing->lista_buscador_industrial_ingresopt($text);
    $n_prod = pg_num_rows($Ing->lista_num_productos($text));
    $det = 0;

    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    $prod = trim(strtoupper($_GET[prod]));
    $nm = trim(strtoupper($_GET[txt]));
} else {
    $trs = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $text = " and m.mov_fecha_trans between '$fec1' and '$fec2' ";
//    $cns = $Ing->lista_buscador_industrial_ingresopt($text);
    $n_prod = 0;
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
                        $.post("actions_industrial_ingresopt.php", {op: 15}, function (dt) {
                            secuencial = '001-' + dt;
                            frm.src = '../Scripts/Form_industrial_ingresopt.php?sec=' + secuencial;//Cambiar Form_productos
                            if (secuencial != 0) {
                                $.post("actions_industrial_ingresopt.php", {op: 16, sec: secuencial}, function (dt) {
                                    if (dt != 0) {
                                        alert(dt);
                                    }
                                });
                            }
                        });
                        look_menu();
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
                <center class="cont_title" ><?PHP echo 'INGRESO DE PRODUCTO TERMINADO' ?></center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>-->
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
                    <th id="doc" colspan="3">Documento</th>
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
                    <th>#Rollo</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Cajas</th>
                    <th>Rollos</th>
                    <th>Peso</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $n = 0;
                $cja = 0;
                $grup = '';
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_ord = pg_fetch_array($Ing->lista_orden($rst[mov_guia_transporte]));
                    $t_peso+=$rst['mov_cantidad'];
                    
                    $t_cnt+=$rst['mov_tabla'];
                    $cja = round($rst['mov_tabla']) / $rst_ord[opp_velocidad];
                    $dt = explode('.', $cja);
                    if (empty($rst_ord[opp_velocidad])) {
                        $cnt_cj =0;
                    } else {
                        if (!empty($dt[1])) {
                            $cnt_cj = round($dt[0]);
                        } else {
                            $cnt_cj = $cja;
                        }
                    }
                    $t_caja+=$cnt_cj;
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
                            <td onmousemove='mover(this)'>$rst[pro_codigo]</td>                    
                            <td>$rst[mov_pago]</td>                    
                            <td>$rst[pro_descripcion]</td>
                            <td>$rst[trs_descripcion]</td>
                            <td align='right'>".number_format($cnt_cj)."</td>
                            <td align='right'>".number_format($rst[mov_tabla])."</td>
                            <td align='right'>".number_format($rst[mov_cantidad],2)."</td>
                        </tr>";

                    $grup = $rst['mov_documento'];
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
        <td class='totales' align='right' >" . number_format($t_caja, 2) . "</td>
        <td class='totales' align='right' >" . number_format($t_cnt, 2) . "</td>
        <td class='totales' align='right' >" . number_format($t_peso, 2) . "</td>
    </tr>";
            ?>
        </table>            
    </body>    
</html>

