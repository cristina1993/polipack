<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_plan_cuentas.php'; //cambiar clsClase_productos
//include_once '../Clases/clsSetting.php';
$Clase_plan_cuentas = new Clase_plan_cuentas();

if (isset($_GET[txt])) {
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($txt)) {
        $txt = "where pln_codigo like '%$txt%' or pln_descripcion like '%$txt%' or pln_obs like '%$txt%'";
    }
    $cns = $Clase_plan_cuentas->lista_buscador_cuentas($txt);
} else {
    $txt = '';
    $cns = $Clase_plan_cuentas->lista_cuentas();
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
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function del(op, id, nom)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_plan_cuentas.php", {op: op, id: id, nom: nom}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_plan_cuentas.php';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";

                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_plan_cuentas.php?'//Cambiar Form_productos
                        look_menu();
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_plan_cuentas.php?id=' + id;//Cambiar Form_productos
                        look_menu();
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        break;

                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }


        </script> 
        <style>
            #mn66,
            #mn114,
            #mn119,
            #mn124,
            #mn129,
            #mn134,
            #mn139,
            #mn144,
            #mn149,
            #mn154{
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
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
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
                <center class="cont_title" ><?php echo "PLAN DE CUENTAS" ?></center>

                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="25" id="txt" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button><img src="../img/finder.png"/>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Codigo</th>
            <th>Descripcion</th>
            <th>Observacion</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $grup = '';
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst['pln_codigo'] ?></td>
                    <td ><?php echo $rst['pln_descripcion'] ?></td>
                    <td ><?php echo $rst['pln_obs'] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png" width="16px"  class="auxBtn" onclick="del(1,<?php echo $rst[pln_id] ?>, '<?php echo $rst[pln_codigo] ?>')">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png"  width="16px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[pln_id] ?>)">
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

