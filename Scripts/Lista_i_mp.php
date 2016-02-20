<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[txt])) {
    $txt = trim(strtoupper($_GET[txt]));
    $emp = $_GET[emp_id];
    if ($emp != 0) {
        $t_emp = "AND em.emp_id=" . $emp;
    } else {
        $t_emp = "";
    }

    $cns = $Set->lista_search_mp($txt, $t_emp);
} else {
    $cns = $Set->lista_mp0();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Materia Prima</title>
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
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/Form_i_mp.php';
                        look_menu();
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_i_mp.php?id=' + id + '&x=' + x;
                        look_menu();
                        break;
                }

            }

            function del(id, nom)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 20, id: id, nom: nom}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_mp.php';
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
            #mn23{
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
                    $cns_sbm = $User->list_primer_opl(16, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>

                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >MATERIA PRIMA</center>
                <center class="cont_finder">
                    <?php
                    if ($Prt->add == 0) {
                        ?>
                        <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                        <?php
                    }
                    ?>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo:<input type="text" name="txt" size="25" placeholder="Referencia o Descripcion" />
                        Fabrica:<select name="emp_id">
                            <option value="0" >Todos</option>
                            <?php
                            $cns_emp = $Set->lista_fabricas();
                            while ($rst_emp = pg_fetch_array($cns_emp)) {
                                echo "<option value='$rst_emp[emp_id]' >$rst_emp[emp_descripcion]</option>";
                            }
                            ?>
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>

            </caption>
            <thead>
            <th>No</th>
            <th>Fabrica</th>
            <th>Tipo</th>
            <th>Referencia</th>
            <th>Descripcion</th>                     
            <th>Presentacion</th>
            <th>Unidad</th>
            <th>Propiedad1</th>
            <th>Propiedad2</th>
            <th>Propiedad3</th>
            <th>Procedencia</th>
            <th>Observaciones</th>
            <th>Acciones</th>
        </thead>
        <tbody>
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst[emp_descripcion] ?></td>
                    <td><?php echo $rst[mpt_nombre] ?></td>
                    <td><?php echo $rst[mp_codigo] ?></td>
                    <td><?php echo $rst[mp_referencia] ?></td>
                    <td><?php echo $rst[mp_presentacion] ?></td>
                    <td style="text-transform:lowercase" ><?php echo $rst[mp_unidad] ?></td>
                    <td><?php echo $rst[mp_pro1] ?></td>
                    <td><?php echo $rst[mp_pro2] ?></td>
                    <td><?php echo $rst[mp_pro3] ?></td>
                    <td><?php echo $rst[mp_pro4] ?></td>
                    <td><?php echo $rst[mp_obs] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" onclick="auxWindow(1,<?php echo $rst[mp_id] ?>, 0)">
                            <?php
                        }
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png" onclick="del(<?php echo $rst[mp_id] ?>, '<?php echo $rst[mp_codigo] ?>')">
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

