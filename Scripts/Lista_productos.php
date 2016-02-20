<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_preciospt.php';
$tbl_set = 'erp_productos_set';
$tbl = substr($tbl_set, 0, -4);
$tbl_name = 'productos';
$tp = 'pro_tipo';
$tp0 = 'pro_';
$user = $_SESSION[usuid];
$Set = new Set();
$Clase_preciospt = new Clase_preciospt();
if (isset($_GET[search])) {
    if (!empty($_GET[txt])) {
        $prod = strtoupper(trim($_GET[txt]));
        $rst0 = pg_fetch_array($Set->lista_one_table_code_finder($prod));
        $cns = $Set->lista_one_table_code_finder($prod, $tipo);
        $head = pg_fetch_array($Set->lista_one_data($tbl_set, $rst0[ids]));
        $tp_prod = explode('&', $head[1]);
        $tipo = $_GET[tipo] = $rst0[ids];
    } else {
        $tipo = $_GET[tipo];
        $cns = $Set->lista_table_by_tipo($tbl, $tipo);
    }

    $head = pg_fetch_array($Set->lista_one_data($tbl_set, $tipo));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title><?php echo $tbl_name ?></title>
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
            var tbl_name = '<?php echo $tbl_name ?>';
            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, tipo, id, x, e, obj)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/Form_' + tbl_name + '.php';
                        look_menu();
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_' + tbl_name + '.php?id=' + id + '&tipo=' + tipo + '&x=' + x;
                        if (x == 0)
                        {
                            look_menu();
                        }
                        break;
                    case 2:
                        main.src = '../Scripts/Set_' + tbl_name + '.php?ol=<?php echo $_SESSION[ol] ?>';
                        break;

                    case 3:
                        $.post("actions_preciospt.php", {op: 1, id: id, tab: x}, function (dt) {
                            if (dt == 0)
                            {
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_productos.php';
                            } else {
                                alert(dt);
                            }
                        });
                        break;
                    case 4:
                        var imgs;
                        switch (e) {
                            case 0:
                                imgs = "<img src = '../img/show.png' width='16px' class='auxBtn' onclick = 'auxWindow(4,0," + id + ",0,2,this)'>\n\
                                         <img src = '../img/activo.png' width='16px' class='auxBtn' onclick = 'auxWindow(4,0," + id + ",0,1,this)'>";
                                break;
                            case 1:
                                imgs = "<img src = '../img/show.png' width='16px' class='auxBtn' onclick = 'auxWindow(4,0," + id + ",0,2,this)'>\n\
                                         <img src = '../img/inactivo.png' width='16px' class='auxBtn' onclick = 'auxWindow(4,0," + id + ",0,0,this)'>";
                                break;
                            case 2:
                                imgs = "<img src = '../img/noshow.png' width='16px' class='auxBtn' onclick = 'auxWindow(4,0," + id + ",0,0,this)'>\n\
                                         <img src = '../img/inactivo.png' width='16px' class='auxBtn' onclick = 'auxWindow(4,0," + id + ",0,0,this)'>";
                                break;
                        }

                        $.post("actions_productos.php", {op: 3, id: id, tab: x, est: e}, function (dt) {
                            if (dt != 0) {
                                alert(dt);
                            } else {
                                var row = $(obj).parents();
                                $(row[0]).html(imgs);
                            }
                        });
                        break;

                }

            }

            function del(id, tbl, dat, nom)
            {
                data = Array(dat);
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 6, tbl: tbl, id: id, 'data[]': data, nom: nom}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_productos.php';
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
            *{
                font-size:10px;
                text-transform: uppercase;
                letter-spacing:-0.35px;
                font-family:Arial, Helvetica, sans-serif; 
            }
            .sbmnu{
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
                    <font class="sbmnu" onclick="window.location = 'Lista_productos.php'" >Productos</font>                                            
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                    <?php
                    if ($Prt->special == 0) {
                        ?>
                        <img class="auxBtn" src="../img/set.png" onclick="auxWindow(2)" width="16px" />
                    <?php }
                    ?>
                </center>
                <center class="cont_title" ><?php echo $tbl_name ?></center>
                <center class="cont_finder">
                    <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="frmSearch" name="frm1" style="margin:0px 5px 10px 0px;">
                        <?php
                        if ($Prt->add == 0) {
                            ?>
                            <a href="#" class="btn" title="Nuevo Registro" onclick="auxWindow(0, 0)" >Nuevo</a>
                            <?php
                        }
                        ?>
                        Codigo:<input type="text" size="45" id="txt" name="txt" />
                        <select style="width:200px; " name="tipo" id="tipo" onchange="loadData(this.value, '<?php echo $tbl_set ?>')">
                            <option value="0">Seleccione Tipo</option>
                            <?php
                            $cnsTipos = $Set->lista_by_tipo_productos_comerciales();
                            while ($rst = pg_fetch_array($cnsTipos)) {
                                if ($_GET[tipo] == $rst[ids]) {
                                    $selected = 'selected';
                                } else {
                                    $selected = '';
                                }
                                echo "<option $selected value=$rst[ids]>$rst[protipo]</option>";
                            }
                            ?>
                        </select>  
                        <input type="submit" value="Buscar" class="btn" id="search" name="search" >
                    </form>  
                </center>
            </caption>
            <thead>
            <th width="20px">No</th>
            <th width="80px">Codigo</th>
            <th width="60px">Lote</th>
            <th width="80px">Cod Aux</th>
            <th width="80px">Tipo</th>            
            <th>Descripcion</th>
            <?php
            $n = 2;
            while ($n <= count($head)) {
                $file = explode('&', $head[$n]);
                if (!empty($file[9]) && $file[3] == '0' && $file[8] != 'pro_a' && $file[8] != 'pro_ac' && $file[8] != 'pro_ad' && $file[8] != 'pro_b') {
                    ?>
                    <th><?php echo $file[9] ?></th>
                    <?php
                }
                $n++;
            }
            ?>
            <th width="70px">Estado</th>
            <th width="60px">Acciones</th>
        </thead>
        <tbody id="tbody">
            <?php
            $cn = 0;
            while ($rst = pg_fetch_array($cns)) {
                $cn++;
                $enc = pg_fetch_array($Set->lista_one_data($tbl_set, $rst[ids]));
                $tp_prod = explode('&', $enc[1]);
                ?>
                <tr>
                    <td><?php echo $cn ?></td>   
                    <td><?php echo $rst[pro_a] ?></td>
                    <td><?php echo $rst[pro_ac] ?></td>
                    <td><?php echo $rst[pro_ad] ?></td>
                    <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" ><?php echo $tp_prod[9] ?></td>                       
                    <td ><?php echo $rst[pro_b] ?></td>
                    <?php
                    $n = 2;
                    while ($n <= count($head)) {
                        $file = explode('&', $head[$n]);
                        if (!empty($file[9]) && $file[3] == '0' && $file[8] != 'pro_a' && $file[8] != 'pro_ac' && $file[8] != 'pro_ad' && $file[8] != 'pro_b') {
                            if ($file[2] == 'I') {
                                $value = $rst[$file[8]];
                                $rst[$file[8]] = "<img src='$value' width=64px />";
                            }
                            if ($file[2] == 'E') {
                                $rstEnlace = pg_fetch_array($Set->list_one_data_by_id($file[6], $rst[$file[8]]));
                                $rst[$file[8]] = $rstEnlace[ins_a];
                            }
                            ?>
                            <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" ><?php echo $rst[$file[8]] ?></td>
                            <?php
                        }
                        $n++;
                    }
                    ?>


                    <td align="center" >
                        <?php
                        if ($user == 1) {
                            if ($rst['pro_estado'] == 2) {
                                ?> 
                                <img src = "../img/noshow.png" width="16px" class="auxBtn" onclick = "auxWindow(4, 0,<?php echo $rst[id] ?>, 0, 0, this)">
                                <?php
                            } else {
                                ?> 
                                <img src = "../img/show.png" width="16px" class="auxBtn" onclick = "auxWindow(4, 0,<?php echo $rst[id] ?>, 0, 2, this)">
                                <?php
                            }
                        }
                        if ($rst['pro_estado'] != 0) {
                            ?>  
                            <img src = "../img/inactivo.png" width="16px" class="auxBtn" onclick = "auxWindow(4, 0,<?php echo $rst[id] ?>, 0, 0, this)">
                            <?php
                        } else {
                            ?>
                            <img src="../img/activo.png" width="16px" class="auxBtn" onclick = "auxWindow(4, 0,<?php echo $rst[id] ?>, 0, 1, this)">
                            <?php
                        }
                        ?>
                    </td>

                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/del_reg.png" width="16px" class="auxBtn" onclick="del(<?php echo $rst[id] ?>, '<?php echo $tbl ?>', '<?php echo $rst[$tp0 . 'a'] ?>',<?php echo $rst[pro_a] ?>)">                        
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" width="16px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 0)">                        
                        <?php }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>


    </table>            

</body>    
</html>

