<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_productos.php'; //cambiar clsClase_productos
$Productos = new Clase_Productos();
$cns_emp = $Productos->lst_emp();

if (isset($_GET[search])) {
    $codigo = trim(strtoupper($_GET[txt1]));
    if (!empty($_GET[txt1])) {
        $text = "and (pro_descripcion like '%$codigo%' or pro_codigo like '%$codigo%') and pro_estado= '0' order by pro_codigo";
    } else if ($_GET[tipo]!='') {
        $text = "and  pro_tipo=$_GET[tipo] and e.emp_id=$_GET[txt2] and pro_estado= '0' order by pro_codigo";
    } else if (!empty($_GET[txt3])) {
        $text = "and e.emp_id=$_GET[txt2] and pro_estado= '$_GET[txt3]' order by pro_codigo";
    } else if (!empty($_GET[txt2])) {
        $text = "and e.emp_id=$_GET[txt2] and pro_estado= '0' order by pro_codigo ";
    }
    $cns = $Productos->lista_buscador($text);
} else if (isset($_GET[cod])) {
    $cns = $Productos->lista_uno(trim(strtoupper($_GET[cod])));
}
$user = $_SESSION[usuid];
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
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }


            function auxWindow(a, id, x, e, obj, c)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_productos.php?txt1=' + txt1.value + '&txt2=' + emp_id.value + '&txt3=' + estado.value + '&tipo=' + tipo.value;//Cambiar Form_productos
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_productos.php?id=' + id + '&txt1=' + txt1.value + '&txt2=' + emp_id.value + '&txt3=' + estado.value + '&tipo=' + tipo.value;//Cambiar Form_productos
                        look_menu();
                        break;
                    case 2://Al dar dobleclik en la lista muestra formulario 
                        frm.src = '../Scripts/Form_i_productos.php?id=' + id + '&x=' + x + '&txt1=' + txt1.value + '&txt2=' + emp_id.value + '&txt3=' + estado.value + '&tipo=' + tipo.value;//Cambiar Form_productos
                        look_menu();
                        break;
                    case 4:
                        var imgs;
                        switch (e) {
                            case 0:
                                imgs = "<img src = '../img/show.png' width='16px' class='auxBtn' onclick = 'auxWindow(4," + id + ",1,2,this)'>\n\
                                         <img src = '../img/activo.png' width='16px' class='auxBtn' onclick = 'auxWindow(4," + id + ",1,1,this)'>";
                                break;
                            case 1:
                                imgs = "<img src = '../img/show.png' width='16px' class='auxBtn' onclick = 'auxWindow(4," + id + ",1,2,this)'>\n\
                                         <img src = '../img/inactivo.png' width='16px' class='auxBtn' onclick = 'auxWindow(4," + id + ",1,0,this)'>";
                                break;
                            case 2:
                                imgs = "<img src = '../img/noshow.png' width='16px' class='auxBtn' onclick = 'auxWindow(4," + id + ",1,0,this)'>\n\
                                         <img src = '../img/inactivo.png' width='16px' class='auxBtn' onclick = 'auxWindow(4," + id + ",1,0,this)'>";
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

            function del(id, op)
            {

                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_productos.php", {act: 48, id: id, op: op}, function (dt) {//cambiar actions_productos
                        if (dt == 0)
                        {
                            loading('hidden');
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_productos.php';
                        } else {

                            loading('hidden');
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
            #mn48{
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
                    $cns_sbm = $User->list_primer_opl(28, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >LISTA DE PRODUCTOS </center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        CODIGO/DESCRIPCION:<input type="text" id="txt1" name="txt1" size="15" />
                        FABRICA:
                        <select id="emp_id" name="txt2">
                            <?php
                            while ($rst_emp = pg_fetch_array($cns_emp)) {
                                echo "<option value='$rst_emp[emp_id]' >$rst_emp[emp_descripcion]</option>";
                            }
                            ?>  
                        </select>
                        TIPO:
                        <select id="tipo" name="tipo">
                            <option value="">Seleccione</option>
                            <option value="0">SEMIELABORADO</option>
                            <option value="1">TERMINADO</option>

                        </select>
                        ESTADO:
                        <select id="estado" name="txt3">
                            <option value="">Seleccione</option>
                            <option value="0">ACTIVO</option>
                            <option value="1">INACTIVO</option>
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()" name="search">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Tipo</th>
            <th>Codigo</th>
            <th>Descripcion</th>
            <th>Unidad</th>
            <th>Ancho</th>
            <th>Largo</th>
            <th>Densidad</th>
            <th>Espesor</th>
            <th>Peso</th>
            <th>Estado</th>
            <th>Acciones</th>

        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                switch ($rst['pro_tipo']) {
                    case 0:
                        $rst['pro_tipo'] = 'SEMIELABORADO';
                        break;
                    case 1:
                        $rst['pro_tipo'] = 'TERMINADO';
                        break;
                }
                ?>
                <tr id="fila" ondblclick="auxWindow(2,<?php echo $rst[pro_id] ?>, 1)">
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst['pro_tipo'] ?></td>
                    <td><?php echo $rst['pro_codigo'] ?></td>
                    <td><?php echo $rst['pro_descripcion'] ?></td>
                    <td><?php echo $rst['pro_uni'] ?></td>
                    <td align="right"><?php echo $rst['pro_ancho'] *1000?></td>
                    <td align="right"><?php echo $rst['pro_largo'] ?></td>
                    <td align="right"><?php echo $rst['pro_gramaje'] ?></td>
                    <td align="right"><?php echo $rst['pro_espesor'] ?></td>
                    <td align="right"><?php echo $rst['pro_peso'] ?></td>
                    <td align="center" >
                        <?php
                        if ($user == 1) {
                            if ($rst['pro_estado'] == 2) {
                                ?> 
                                <img src = "../img/noshow.png"  title="Mostrar producto" width="16px" class="auxBtn" onclick = "auxWindow(4,<?php echo $rst[pro_id] ?>, 1, 0, this)">
                                <?php
                            } else {
                                ?> 
                                <img src = "../img/show.png"  title="Ocultar producto" width="16px" class="auxBtn" onclick = "auxWindow(4,<?php echo $rst[pro_id] ?>, 1, 2, this)">
                                <?php
                            }
                        }
                        if ($rst['pro_estado'] != 0) {
                            ?>  
                            <img src = "../img/inactivo.png"  title="Activar producto" width="16px" class="auxBtn" onclick = "auxWindow(4,<?php echo $rst[pro_id] ?>, 1, 0, this)">
                            <?php
                        } else {
                            ?>
                            <img src="../img/activo.png"  title="Inactivar producto" width="16px" class="auxBtn" onclick = "auxWindow(4,<?php echo $rst[pro_id] ?>, 1, 1, this)">
                            <?php
                        }
                        ?>
                    </td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png"   title="eliminar producto" class="auxBtn" onclick="del(<?php echo $rst[pro_id] ?>, 1)">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png"   title="Editar producto" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[pro_id] ?>, 0)">
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
<script>
    var emp = '<?php echo $_GET[txt2] ?>';
    var est = '<?php echo $_GET[txt3] ?>';
    var tip = '<?php echo $_GET[tipo] ?>';
    $('#emp_id').val(emp);
    $('#tipo').val(tip);
    $('#estado').val(est);
</script>

