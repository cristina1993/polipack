<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_padding.php';
$Set = new Clase_reg_padding();
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $est = $_GET[estado];
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($txt)) {
        $texto = "and (r.rpa_numero like'%$ord%' or o.opp_codigo like'%$ord%') ";
    } else {
        $texto = "and r.rpa_fecha between '$fec1' and '$fec2'";
    }
    $cns = $Set->lista_buscador_orden($texto);
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
                posicion_accion();
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
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_reg_op_padding.php?txt=' + $('#txt').val() + '&estado=' + $('#estado').val() + '&fecha1=' + $('#fecha1').val() + '&fecha2=' + $('#fecha2').val();
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_reg_op_padding.php?id=' + id + '&txt=' + $('#txt').val() + '&estado=' + $('#estado').val() + '&fecha1=' + $('#fecha1').val() + '&fecha2=' + $('#fecha2').val();
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function del(id, data)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_reg_padding.php", {op: 1, id: id, data: data}, function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
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
                <center class="cont_title" ><?php echo "REGISTRO DE PRODUCCION PADDING" ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
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
                    <th colspan="4"></th>
                    <th colspan="3">PRODUCTO PRIMARIO</th>
                    <th colspan="3"></th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>FECHA</th>                                
                    <th>REGISTRO</th>
                    <th>ORDEN</th>
                    <th>DESCRIPCION</th>
                    <th>PESO TOTAL</th>
                    <th># CANTIDAD</th>
                    <th>DESPERDICIO</th>
                    <th># OPERADOR</th>
                    <th>ACCION</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    echo "<tr>
                        <td>$n</td>
                        <td>$rst[rpa_fecha] </td>
                        <td>$rst[rpa_numero] </td>
                        <td>$rst[opp_codigo] </td>
                        <td>$rst[pro_descripcion] </td>
                        <td align='right'>" . number_format($rst[rpa_peso], 2) . "</td>
                        <td align='right'>" . number_format($rst[rpa_rollo], 2) . "</td>
                        <td align='right'>" . number_format($rst[rpa_desperdicio], 2) . "</td>
                        <td>$rst[rpa_operador] </td>
                        <td>";

                    if ($Prt->delete == 0) {
                        ?>
                    <img src='../img/b_delete.png' width='20px' class='auxBtn' onclick="del('<?php echo $rst[rpa_id] ?>', '<?php echo $rst[rpa_numero] ?>')"/>
                    <?PHP
                }
                if ($Prt->edition == 0) {
                    echo "<img src='../img/upd.png'  class='auxBtn' width='20px' onclick='auxWindow(1, $rst[rpa_id])'>";
                }
                echo "</td>
                    </tr>";
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
