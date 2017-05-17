<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[txt], $_GET[desde], $_GET[hasta])) {
    $nm = trim(strtoupper($_GET[txt]));
    $emp_id = $_GET[emp_id];
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if (!empty($_GET[txt])) {
        $texto = "and trs.trs_operacion=0 and (mp.mp_codigo like '%$nm%' OR mp.mp_referencia like '%$nm%')";
    } else if (!empty($_GET[emp_id])) {
        $texto = "and trs.trs_operacion=0 and mp.emp_id=$emp_id and mi.mov_fecha_trans between '$desde' and '$hasta'";
    } else {
        $texto = "and trs.trs_operacion=0 and mi.mov_fecha_trans between '$desde' and '$hasta'";
    }
    $cns = $Set->lista_mov_mp_search2($texto);
} else {
    $hasta = date("Y-m-d");
    $desde = date("Y-m-d");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Registro Materia Prima</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
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
                switch (a) {
                    case 0:
                        frm.src = '../Scripts/Form_i_reg_mp.php';
                        parent.document.getElementById('contenedor2').rows = "*,85%";
                        look_menu();
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_i_reg_mp.php?id=' + id + '&x=' + x;
                        parent.document.getElementById('contenedor2').rows = "*,85%";
                        if (x == 0) {
                            look_menu();
                        }
                        break;
                }

            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 20, id: id}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_reg_mp.php';
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
            #mn24{
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
        <table style="width: 100%" id="tbl">
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
                    <center class="cont_title" >Registro de Inventario de Materia Prima</center>
                    <center class="cont_finder">
                        <?php
                        if ($Prt->add == 0) {
                            ?>
                            <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                            <?php
                        }
                        ?>
                        <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                            Codigo:<input type="text" name="txt" size="15" value="<?php echo $nm ?>"/>
                            Fabrica:
                            <select id="emp_id" name="emp_id" style="width:125px; font-size: 12px"  >
                                <?php
                                $cns_emp = $Set->lista_fabricas();
                                while ($rst_emp = pg_fetch_array($cns_emp)) {
                                    echo "<option $sel value='$rst_emp[emp_id]'>$rst_emp[emp_descripcion]</option>";
                                }
                                ?>
                            </select>
                            DESDE:<input type="text" name="desde" id="desde" value="<?php echo $desde ?>" size="10"/>
                            <img src="../img/calendar.png" id="im-desde" width="16" />
                            HASTA:<input type="text"   name="hasta" value="<?php echo $hasta ?>"  id="hasta" size="10" />
                            <img src="../img/calendar.png" width="16"   id="im-hasta" />
                            <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>

                        </form>  
                    </center>

            </caption>

            <thead>
                <tr>
                    <th colspan="4">Materia Prima</th>
                    <th colspan="4">Documento</th>
                    <th colspan="4">Transaccion</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Unidad</th>
                    <th>Fecha Transaccion</th>
                    <th>Documento No</th>
                    <th>Guia de Recepcion</th>
                    <th>Proveedor</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Costo/U</th>
                    <th>Costo/T</th>
                </tr>  
            </thead>
            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $rst_cli = pg_fetch_array($Set->lista_un_cliente($rst[mov_proveedor]));
                    $n++;
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[mp_codigo] ?></td>
                        <td><?php echo $rst[mp_presentacion] ?></td>
                        <td align="center" style="text-transform:lowercase"><?php echo $rst[mp_unidad] ?></td>                        
                        <td><?php echo $rst[mov_fecha_trans] ?></td>
                        <td><?php echo $rst[mov_num_trans] ?></td>
                        <td><?php echo $rst[mov_guia_remision] ?></td>
                        <td><?php echo trim($rst_cli['cli_raz_social']) ?></td>
                        <td><?php echo $rst[trs_descripcion] ?></td>
                        <td align="right"><?php echo number_format($rst[mov_cantidad], 1) ?></td>
                        <td align="right"><?php echo number_format($rst[mov_peso_unit], 2) ?></td>
                        <td align="right"><?php echo number_format($rst[mov_peso_total], 2) ?></td>
                    </tr>  
                    <?PHP
                }
                ?>
            </tbody>


        </table>            

    </body>    
</html>
<script>
    var e = '<?php echo $emp_id ?>';
    $('#emp_id').val(e);
</script>

