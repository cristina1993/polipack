<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_plumon.php';
$Set = new Clase_reg_plumon();
if (isset($_GET[txt])) {
    $fec1 = $_GET[desde];
    $fec2 = $_GET[hasta];
    $ord = strtoupper(trim($_GET[txt1]));
    $cns = $Set->lista_una_orden_produccion_numero_orden_plumon($ord, $fec1, $fec2);
} else {
    $fec1 = date("Y-m-d");
    $fec2 = date("Y-m-d");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Orden de Producción Plumo</title>
    <head>
        <script>
            $(function () {
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hata", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
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
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_orden_plumon.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_orden_plumon.php?id=' + id;
                        look_menu();
                        break;
                    case 2://Reporte
                        frm.src = '../Scripts/Form_i_pdf_orden_plumon.php?id=' + id;
                        look_menu();
                        break;
                    case 3://etiqueta
                        frm.src = '../Scripts/frm_etiquetas_plumon.php?id=' + id;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }
            }
            function del(id,doc)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 58, id: id, data:doc}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_orden_plumon.php';
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
            #mn51{
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
            <caption class="tbl_head" >
                <center class="cont_menu" > 
                    <?php
                    $cns_sbm = $User->list_primer_opl(29, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <!---------------------------->

                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >ORDEN DE PRODUCCION PLUMON</center>
                <center class="cont_finder">    
                    <form method="GET" id="frmSearch" name="frm1" style="margin-top:5px; " action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <?php
                        if ($Prt->add == 0) {
                            ?>                                                 
                            <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;   " title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                            <?php
                        }
                        ?>
                        DESDE: <input type="text"   name="desde" id="desde" size="10" value="<?php echo $fec1 ?>"/>
                        <img src="../img/calendar.png" width="16"  id="im-desde" />
                        HASTA: <input type="text"   name="hasta" id="hasta" size="10" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" width="16"  id="im-hasta" />
                        Pedido: <input type="text" name="txt" size="25" value="<?php echo $ord ?>" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead >
                <tr>
                    <th colspan="5"></th>               
                    <th colspan="2">Solicitado</th>                   
                    <th colspan="2">Producción</th>
                    <th colspan="2">Faltante</th>                   
                    <th colspan="2">Despacho</th>
                    <th colspan="2"></th>
                </tr>
            <thead >
            <th>No.</th>
            <th>Pedido</th>
            <th>Fecha Pedido</th>
            <th>Cliente</th>
            <th>Producto</th>
            <th>Cant.</th>
            <th>Kg</th>     
            <th>Cant.</th>
            <th>Kg</th>   
            <th>Cant.</th>
            <th>Kg</th>   
            <th>Cant.</th>
            <th>Kg</th>  
            <th>Status</th>
            <th>Acciones</th>
        </thead>
    </thead>
    <!------------------------------------->
    <tbody id="tbody">
        <?PHP
        $n = 0;
        while ($rst = pg_fetch_array($cns)) {
            $n++;
            $rst_pro = pg_fetch_array($Set->lista_un_producto($rst[pro_id]));
            $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst[orp_id]));
            $kgtot = $rst[orp_pro_peso] * $rst[orp_cantidad];
            $faltante = $rst[orp_cantidad] - $rst_prod[rollo];
            $f_peso = $kgtot - $rst_prod[peso];
            echo"<tr>
                <td>$n</td>                  
                <td>$rst[orp_num_pedido]</td>
                <td>$rst[orp_fec_pedido]</td>
                <td>$rst[cli_raz_social]</td>
                <td>$rst_pro[pro_descripcion]</td>      
                <td>$rst[orp_cantidad]</td>      
                <td>$kgtot</td>      
                <td>$rst_prod[rollo]</td>
                <td>$rst_prod[peso]</td>
                <td>$faltante</td>
                <td>$f_peso</td>
                <td></td>
                <td></td>";
            $por = ($rst_prod[peso] * 100) / $kgtot;
            if ($rst_prod[peso] == '') {
                $est = 'REGISTRADO';
            } else if ($por >= 90) {
                $est = 'TERMINADO';
            } else {
                $est = 'PRODUCCION';
            }
            echo "<td>$est</td>
                <td align='center'>";
            if ($Prt->edition == 0) {
                echo "<img src='../img/upd.png'  class='auxBtn' onclick='auxWindow(1,$rst[orp_id], 0)'>";
            }
            if ($Prt->delete == 0) {
                $a='"';
                echo "<img src='../img/b_delete.png'  class='auxBtn' onclick='del($rst[orp_id],$a$rst[orp_num_pedido]$a)'>";
            }
            echo"  <img src='../img/orden.png' class='auxBtn' onclick='auxWindow(2,$rst[orp_id])'>
                    <img src='../img/etq2.jpg' title='etiqueta' width='24px' class='auxBtn' onclick='auxWindow(3,$rst[orp_id])'>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>            
</body>    
</html>

