<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[txt])) {
    $nm = trim(strtoupper($_GET[txt]));
    $emp_id = $_GET[emp_id];
    $hasta = $_GET[hasta];
    if (!empty($nm)) {
        $texto = "AND (mp.mp_codigo like '%$nm%' OR mp.mp_referencia like '%$nm%') and mi.mov_fecha_trans between '1990-01-01' and '$hasta'";
    } else if (!empty($_GET[emp_id])) {
        $texto = "and mp.emp_id=$emp_id and mi.mov_fecha_trans between '1990-01-01' and '$hasta'";
    } else {
        $texto = " and mi.mov_fecha_trans between '1990-01-01' and '$hasta'";
    }
    $cns = $Set->lista_inv_kardex($texto);
} else {
    $hasta = date("Y-m-d");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Movimiento de Materia Prima</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }

            );

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
                        frm.src = '../Scripts/Form_i_reg_movmp.php';
                        look_menu();
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
                            loading('hidden');
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_inventario.php';
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
            #mn29{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                color:black;
                font-weight:bolder; 
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="" id="tbl" width='100%'>
            <caption class="tbl_head" >
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl(18, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >INVENTARIO de Materia Prima</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo:<input type="text" name="txt" size="15" value="<?php echo $nm ?>"/>
                        Fabrica:
                        <select id="emp_id" name="emp_id" style="width:125px; font-size: 12px"  >
                            <option value="0">Seleccione</option>
                            <?php
                            $cns_emp = $Set->lista_fabricas();
                            while ($rst_emp = pg_fetch_array($cns_emp)) {
                                echo "<option $sel value='$rst_emp[emp_id]'>$rst_emp[emp_descripcion]</option>";
                            }
                            ?>
                        </select>
                        HASTA:<input type="text"   name="hasta" value="<?php echo $hasta ?>"  id="hasta" size="10" />
                        <img src="../img/calendar.png" width="16"   id="im-hasta" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>

                    </form>   
                </center>

            </caption>

            <thead>
                <tr>
                    <th colspan="3">Materia Prima</th>
                    <th colspan="2">Totales</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Referencia</th>
                    <th>Descripcion</th>
                    <th>Cantidad</th>
                    <th>Peso (kg)</th>
                </tr>  
            </thead>
            <tbody id="tbody">
                <?PHP
                $n = 0;
                $a = 0;
                $mp = null;
                $code = NULL;
                $ref = NULL;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    if ($rst[trs_operacion] == 0) {
                        $operador = null;
                    } else {
                        $operador = "-";
                    }

                    if ($mp != $rst[mp_id] && $n != 1) {
                        $a++;

                        echo "<tr>
                            <td>$a</td> 
                            <td>$mp_code</td>
                            <td>$mp_ref</td>
                            <td align='right'>" . number_format($t_cnt, 1) . "</td>
                            <td align='right'>" . number_format($t_cnt, 1) . "</td>
                        </tr>";
                        $cnt+=$t_cnt;

                        $t_cnt = 0;
                    }

                    $t_cnt+=$operador . $rst[mov_cantidad];
                    $mp = $rst[mp_id];
                    $mp_code = $rst[mp_codigo];
                    $mp_ref = $rst[mp_referencia];
                }
                $a++;
                echo "<tr>
                    <td>" . $a . "</td> 
                    <td>$mp_code</td>
                    <td>$mp_ref</td>
                    <td align='right'>" . number_format($t_cnt, 1) . "</td>
                    <td align='right'>" . number_format($t_cnt, 1) . "</td>
                </tr>";
                $cnt+=$t_cnt;
                echo "<tr>
                    <td></td> 
                    <td></td>
                    <td class='totales' style='font-size:14px;'>Total</td>
                    <td class='totales' style='font-size:14px;' align='right'>" . number_format($cnt, 1) . "</td>
                    <td class='totales' style='font-size:14px;' align='right'>" . number_format($cnt, 1) . "</td>
                </tr>";
                ?>
            </tbody>
        </table>            
    </body>    
</html>
<script>
    var e = '<?php echo $emp_id ?>';
    $('#emp_id').val(e);
</script>
