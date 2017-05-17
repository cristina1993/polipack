<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_etiquetas.php';
$Set = new Clase_etiquetas();
if (isset($_GET[search])) {
    $txt = trim(strtoupper($_GET[txt]));
    $text = "where eti_descripcion like '%$txt%'";
    $cns = $Set->lista_etiquetas($text);
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
                        frm.src = '../Scripts/Form_etiquetas.php?txt=' + txt.value;
                        look_menu();
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_etiquetas.php?id=' + id + '&x=' + x + '&txt=' + txt.value;
                        look_menu();
                        break;
                }

            }

            function del(id, nom)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_etiquetas.php", {op: 1, id: id, nom: nom}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_etiquetas.php';
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
                        Codigo:<input type="text"  id="txt"  name="txt" size="15" value="<?php echo $txt ?>"/>
                        <button class="btn" title="Buscar" name="search" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
            </caption>

            <thead>
            <th>No</th>
            <th>Descripcion</th>
            <th>Tamaño</th>
            <th># Elementos</th>
            <th>Acciones</th>
        </thead>
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $dt = explode('&', $rst[eti_elementos]);
                $j = 0;
                $elementos = 0;
                while ($j < count($dt)) {
                    $elementos+=$dt[$j];
                    $j++;
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst[eti_descripcion] ?></td>
                    <td><?php echo $rst[eti_tamano] ?></td>
                    <td><?php echo $elementos ?></td>

                    <td align="center">
                        <?php
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" onclick="auxWindow(1,<?php echo $rst[eti_id] ?>, 0)">
                            <?php
                        }
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png" onclick="del(<?php echo $rst[eti_id] ?>, '<?php echo $rst[eti_descripcion] ?>')">
                        <?php }
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

