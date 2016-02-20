<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ordenes_padding.php';
$Clase = new Clase_Orden_Padding();
if (isset($_GET[txt1], $_GET[txt2], $_GET[txt3])) {
    $fec1 = $_GET[txt1];
    $fec2 = $_GET[txt2];
    $ord = strtoupper(trim($_GET[txt3]));
    $cns = $Clase->lista_buscador($fec1, $fec2, $ord);
} else {
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
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
                Calendar.setup({inputField: "txt1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "txt2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
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
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_orden_padding.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_orden_padding.php?id=' + id;
                        look_menu();
                        break;
                    case 2://PDF
                        frm.src = '../Scripts/Form_i_pdf_orden_padding.php?id=' + id;
                        look_menu();
                        break;
                    case 3:
                        frm.src = '../Scripts/Form_i_orden_padding.php?id=' + id + '&x=' + x;
                        look_menu();
                        break;
                    case 4://etiqueta
                        frm.src = '../Scripts/frm_etiquetas_padding.php?id=' + id;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }

            }

            function del(id, op, doc)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_padding.php", {id: id, op: op, data: doc}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_orden_padding.php';
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
            #mn50{
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
                    $cns_sbm = $User->list_primer_opl(29, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>

                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >ORDEN DE PRODUCCION PADDING</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        DESDE:<input type="text" id="txt1" name="txt1" size="15" value="<?php echo $fec1 ?>"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" id="txt2" name="txt2" size="15" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        ORDEN:<input type="text" name="txt3" size="15" value="<?php echo $ord ?>"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th colspan="5"></th>
            <th colspan="2">Solicitado</th>
            <th colspan="2">Produccion</th>
            <th colspan="2">Faltante</th>
            <th colspan="2">Despacho</th>
            <th colspan="2"></th>
        </thead>
        <thead>
        <th>No.</th>
        <th>Pedido</th>
        <th>Fecha Pedido</th>
        <th>Cliente</th>
        <th>Producto</th>
        <th>Cant.</th>
        <th>Kg.</th>
        <th>Cant.</th>
        <th>Kg.</th>
        <th>Cant.</th>
        <th>Kg.</th>
        <th>Cant.</th>
        <th>Kg.</th>
        <th>Status</th>
        <th>Acciones</th>
    </thead>
    <!------------------------------------->
    <tbody id="tbody">
        <?PHP
        $n = 0;
        while ($rst = pg_fetch_array($cns)) {
            $n++;
            $rst_prod = pg_fetch_array($Clase->lista_produccion_pedido($rst[opp_id]));
            $faltante = $rst[opp_cantidad] - $rst_prod[rollo];
            $f_peso = $rst[pro_peso] - $rst_prod[peso];
            $ev = "onclick='auxWindow(3,$rst[opp_id], 1)'";
            echo"<tr>
                <td>$n</td>
                <td $ev >$rst[opp_codigo]</td>
                <td $ev >$rst[opp_fec_pedido]</td>
                <td $ev >$rst[cli_raz_social]</td>
                <td $ev >$rst[pro_descripcion]</td>
                <td align='right' $ev >$rst[opp_cantidad]</td>
                <td align='right' $ev >$rst[pro_peso]</td>
                <td align='right' $ev >$rst_prod[rollo]</td>
                <td align='right' $ev >$rst_prod[peso]</td>
                <td align='right' $ev >$faltante</td>
                <td align='right' $ev >$f_peso</td>
                <td align='right' $ev ></td>
                <td align='right' $ev ></td>";
            $por = ($rst_prod[peso] * 100) / $rst[pro_peso];
            if ($rst_prod[peso] == '') {
                $est = 'REGISTRADO';
            } else if ($por >= 90) {
                $est = 'TERMINADO';
            } else {
                $est = 'PRODUCCION';
            }
            echo"<td $ev >$est</td>
                <td align='center'>";
            if ($Prt->delete == 0) {
                $a = '"';
                echo"<img src='../img/b_delete.png'  class='auxBtn' onclick='del($rst[opp_id], 1,$a$rst[opp_codigo]$a)'>";
            }
            if ($Prt->pdf == 0) {
                echo"<img src='../img/orden.png'  class='auxBtn' onclick='auxWindow(2,$rst[opp_id], 0)'>";
            }
            echo "<img src='../img/etq2.jpg' title='etiqueta' width='24px' class='auxBtn' onclick='auxWindow(4,$rst[opp_id])'>";
            if ($Prt->edition == 0) {
                echo"<img src='../img/upd.png'  class='auxBtn' onclick='auxWindow(1,$rst[opp_id], 0)'>";
            }
            echo"</td>           
            </tr>";
        }
        ?>
    </tbody>
</table>            
</body>    
</html>

