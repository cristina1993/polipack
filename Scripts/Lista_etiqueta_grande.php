<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_etiqueta_grande.php';
$Set = new Clase_etiqueta_grande();
if (isset($_GET[search])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($txt)) {
        $text = "and (cli_raz_social like '%$txt%' or etg_numero like '%$txt%' or etg_operador like '%$txt%')";
    } else {
        $text = "and etg_fecha between '$fec1' and '$fec2'";
    }
    $cns = $Set->lista_etiquetas($text);
} else {
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>etiquetas</title>
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
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/Form_etiqueta_grande.php?txt=' + txt.value+'&fec1='+ fecha1.value+'&fec2='+ fecha2.value;
                        look_menu();
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_etiqueta_grande.php?id=' + id + '&x=' + x + '&txt=' + txt.value+'&fec1='+ fecha1.value+'&fec2='+ fecha2.value;
                        look_menu();
                        break;
                    case 2://PDF
                        frm.src = '../Scripts/frm_pdf_etiqueta_grande.php?id=' + id;
                        look_menu();
                        break;
                }

            }

            function del(id, nom)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_etiqueta_grande.php", {op: 1, id: id, nom: nom}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_etiqueta_grande.php';
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
            #mn25{
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
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >ETIQUETAS</center>
                <center class="cont_finder">
                    <?php
                    if ($Prt->add == 0) {
                        ?>
                        <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;   " title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                        <?php
                    }
                    ?>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text"  id="txt"  name="txt" size="15" value="<?php echo $txt ?>"/>
                        DESDE:<input type="text" size="10" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="10" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>" />
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" name="search" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
            </caption>

            <thead>
            <th>No</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Orden Trabajo</th>
            <th>TARA</th>
            <th>P.Bruto</th>
            <th>P.Neto</th>
            <th>Espesor</th>
            <th>Ancho</th>
            <th>Operador</th>
            <th>Acciones</th>
        </thead>
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $pro = substr($rst[etg_numero], 8, 9);
                $ord = substr($rst[etg_numero], 0, 8);
                if ($rst[etg_tipo] == 0) {
                    $rst1 = pg_fetch_array($Set->lista_orden_extrusion($ord));
                    $pbruto = $rst1[pro_propiedad4];
                    $pneto = $rst1[pro_peso];
                    $core = $rst1[pro_propiedad5];
                } else if ($rst[etg_tipo] == 1) {
                    $rst1 = pg_fetch_array($Set->lista_orden_corte($ord, $pro));
                    $pbruto = $rst1[pro_propiedad7];
                    $pneto = $rst1[pro_medvul];
                    $core = $rst1[pro_capa];
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst[etg_fecha] ?></td>
                    <td><?php echo $rst[cli_raz_social] ?></td>
                    <td><?php echo $rst[etg_numero] ?></td>
                    <td><?php echo $core ?></td>
                    <td><?php echo $pbruto ?></td>
                    <td><?php echo $pneto ?></td>
                    <td><?php echo $rst1[pro_espesor] ?></td>
                    <td><?php echo $rst1[pro_ancho] ?></td>
                    <td><?php echo $rst[etg_operador] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" onclick="auxWindow(1,<?php echo $rst[etg_id] ?>, 0)">
                            <?php
                        }
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png" onclick="del(<?php echo $rst[etg_id] ?>, '<?php echo $rst[etg_numero] ?>')">
                            <?php
                        }
                        if ($Prt->pdf == 0) {
                            ?>
                            <img src='../img/orden.png' onclick='auxWindow(2, <?php echo $rst[etg_id] ?>, 0)'>
                            <?php
                        }
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

