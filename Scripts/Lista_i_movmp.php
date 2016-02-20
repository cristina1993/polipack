<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[desde], $_GET[hasta])) {
    $nm = trim(strtoupper($_GET[txt]));
    $emp_id = $_GET[emp_id];
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if (!empty($_GET[txt])) {
        $texto = "and (mp.mp_codigo like '%$nm%' OR mp.mp_referencia like '%$nm%')";
    } else if (!empty($_GET[emp_id])) {
        $texto = "and mp.emp_id=$emp_id";
    } else {
        $texto = "and mi.mov_fecha_trans between '$desde' and '$hasta'";
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
    <title>Movimiento de Materia Prima</title>
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
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/Form_i_reg_movmp.php';
                        parent.document.getElementById('contenedor2').rows = "*,85%";
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
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_movmp.php';
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

            function descargar_archivo() {
                window.location = '../formatos/descargar_archivo.php?archivo=inv_mp.csv';
            }

            function load_file() {
                $('#frm_file').submit();
            }

        </script> 
        <style>
            #mn28{
                background:black;
                color:white;
                border: solid 1px white;
            }
            div.upload {
                padding:5px; 
                width: 14px;
                height: 20px;
                background-color: #568da7;        
                background-image:-moz-linear-gradient(
                    top,
                    rgba(255,255,255,0.4) 0%,
                    rgba(255,255,255,0.2) 60%);
                color:#FFFFFF; 
                overflow: hidden;
                border-radius: 4px 4px 4px 4px; 
                cursor:pointer; 
                border:solid 1px #ccc; 
            }
            div.upload:hover{
                background-color:#7198ab;        
            }
            div.upload input {
                margin-top:-20; 
                margin-left:-5; 
                display: block !important;
                width: 40px !important;
                height: 40px !important;
                opacity: 0 !important;
                overflow: hidden !important;
                cursor:pointer; 
            }    
            #txt_load{
                margin-right:5px; 
                margin-top:13px; 
            }
            *{
                text-transform: uppercase;
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
                    $cns_sbm = $User->list_primer_opl(18, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >Movimiento de Materia Prima</center>
                <center class="cont_finder">
                    <?php
                    if ($Prt->add == 0) {
                        ?>
                        <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                        <?php
                    }
                    ?>
                    <a href="#" onclick="descargar_archivo()" style="float:right;text-transform:capitalize;margin-left:15px;margin-top:10px;text-decoration:none;color:#ccc; ">Descargar Formato<img src="../img/xls.png" width="16px;" /></a>

                    <form id="frm_file" name="frm_file" style="float:right" action="actions_upload_invmp.php" method="POST" enctype="multipart/form-data">
                        <div class="upload">
                            ...<input type="file"  name="archivo" id="archivo" onchange="load_file()" >
                        </div>
                    </form>
                    <font style="float:right" id="txt_load">Cargar Datos:</font>
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
                    <th colspan="5">Documento</th>
                    <th colspan="5">Materia Prima</th>
                    <th colspan="4">Transaccion</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Fecha Transaccion</th>
                    <th>Documento No</th>
                    <th>Orden de Produccion</th>
                    <th>Guia de Recepcion</th>
                    <th>Proveedor</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Presentacion</th>
                    <th>Unidad</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Costo/U</th>
                    <th>Costo/T</th>
                </tr>  
            </thead>
            <tbody id="tbody">
                <?PHP
                $n = 0;
                $grup = '';
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_prov = pg_fetch_array($Set->lista_un_cliente($rst[mov_proveedor]));

                    echo "<tr>
                        <td> $n</td>";

                    if ($grup != $rst['mov_num_trans']) {
                        echo"<td>$rst[mov_fecha_trans]</td>
                            <td>$rst[mov_num_trans]</td>
                            <td>$rst[mov_num_orden]</td>
                            <td>$rst[mov_documento]</td>
                            <td>" . trim($rst_prov[cli_apellidos] . $rst_prov[cli_nombres] . $rst_prov[cli_raz_social]) . "</td>";
                    } else {
                        echo "<td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>";
                    }
                    echo "<td>$rst[mp_codigo]</td>
                        <td>$rst[mp_referencia]</td>
                        <td>$rst[mp_presentacion]</td>
                        <td align='center' style='text-transform:lowercase'>$rst[mp_unidad]</td>                        
                        <td>$rst[trs_descripcion]</td>
                        <td align='right'>" . number_format($rst[mov_cantidad], 1) . "</td>
                        <td align='right'>" . number_format($rst[mov_peso_unit], 4) . "</td>
                        <td align='right'>" . number_format($rst[mov_peso_total], 4) . "</td>
                    </tr> ";

                    $grup = $rst['mov_num_trans'];
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


