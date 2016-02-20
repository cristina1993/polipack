<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cliente.php';
$Clase_cliente = new Clase_cliente();
if (isset($_GET[search])) {
    $txt = strtoupper(trim($_GET[txt]));
    $tipo = $_GET[cli_tipo];
    $categoria = $_GET[cli_categoria];
    $estado = $_GET[cli_estado];
    if (!empty($txt)) {
        $txt = " where(cli_codigo like '%$txt%' 
or cli_ced_ruc like '%$txt%' 
or cli_apellidos like '%$txt%' 
or cli_nombres like '%$txt%' 
or cli_raz_social like '%$txt%' 
or cli_nom_comercial like '%$txt%'   )";
        $tipo = '';
        $categoria = '';
        $estado = '';
    } else {

        if ($tipo != 'x') {
            $tipo = "where cli_tipo ='$tipo'  ";
        } else {
            $tipo = '';
        }

        if ($categoria != 'x') {
            if ($tipo == '') {
                $prfix = 'where';
            } else {
                $prfix = 'and';
            }
            $categoria = $prfix . " cli_categoria ='$categoria'  ";
        } else {
            $categoria = '';
        }

        if ($estado != 'x') {
            if ($tipo == '' && $categoria == '') {
                $prfix = 'where';
            } else {
                $prfix = 'and';
            }
            $estado = $prfix . " cli_estado ='$estado'  ";
        } else {
            $estado = '';
        }
    }
    $cns = $Clase_cliente->lista_buscador_cliente($txt, $tipo, $categoria, $estado);
} else {
    $txt = '';
    $tipo = 'x';
    $categoria = 'x';
    $estado = 'x';
//    $cns = $Clase_cliente->lista_cliente();
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

            function auxWindow(a, id, x) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_cliente.php';
                        parent.document.getElementById('contenedor2').rows = "*,85%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_cliente.php?id=' + id;
                        parent.document.getElementById('contenedor2').rows = "*,85%";
                        look_menu();
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/Form_i_cliente.php?id=' + id + '&x=' + x;
                        //look_menu();
                        break;
                }

            }

            function del(id, op, nom)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_cliente.php", {act: 48, id: id, op: op, nom: nom}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_cliente.php';
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
            #mn33{
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
                    $cns_sbm = $User->list_primer_opl(19, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>

                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >CLIENTES Y PROVEEDORES</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" autocomplete="off" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR:<input type="text" name="txt" id="txt" size="15" />
                        TIPO:
                        <select id="cli_tipo" name="cli_tipo">
                            <option value="x" >Todos</option>
                            <option value="0" >CLIENTE</option>
                            <option value="1" >PROVEEDOR</option>
                            <option value="2" >AMBOS</option>                            
                        </select>
                        CATEGORIA:
                        <select id="cli_categoria" name="cli_categoria">
                            <option value="x" >Todos</option>
                            <option value="1" >NATURAL</option>
                            <option value="2" >JURIDICO</option>
                        </select>
                        ESTADO:
                        <select id="cli_estado" name="cli_estado">
                            <option value="x" >Todos</option>
                            <option value="0" >ACTIVO</option>
                            <option value="1" >INACTIVO</option>
                            <option value="2" >SUSPENDIDO</option>
                        </select>
                        <script>
                            $('#cli_tipo').val('<?php echo $_GET[cli_tipo] ?>');
                            $('#cli_categoria').val('<?php echo $_GET[cli_categoria] ?>');
                            $('#cli_estado').val('<?php echo $_GET[cli_estado] ?>');
                        </script>
                        <button class="btn" title="Buscar" id="search" name="search" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Codigo</th>
            <th>Tipo</th>
            <th>Categoria</th>
            <th>Cedula/ruc</th>
            <th>Cliente</th>
            <th>F_registro</th>
            <th>Pais</th>
            <th>Canton</th>
            <th>Parroquia</th>
            <th>Direccion</th>
            <th>Telefono</th>
            <th>Email</th>
            <th>Estado</th>
            <th>Accciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                if ($rst['cli_categoria'] == '1') {
                    $rst['cli_categoria'] = 'NATURAL';
                } else {
                    $rst['cli_categoria'] = 'JURIDICA';
                }

                if ($rst['cli_tipo'] == '0') {
                    $rst['cli_tipo'] = 'CLIENTE';
                } else if ($rst['cli_tipo'] == '1') {
                    $rst['cli_tipo'] = 'PROVEEDOR';
                } else {
                    $rst['cli_tipo'] = 'AMBOS';
                }
                if ($rst['cli_estado'] == '0') {
                    $rst['cli_estado'] = 'ACTIVO';
                } else if ($rst['cli_estado'] == '1') {
                    $rst['cli_estado'] = 'INACTIVO';
                } else {
                    $rst['cli_estado'] = 'SUSPENDIDO';
                }
                $n++;
                $ev = "onclick='auxWindow(2,$rst[cli_id],1)'";
                ?>
                <tr class="fila">
                    <td ><?php echo $n ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_codigo'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_tipo'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_categoria'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_ced_ruc'] ?></td>
                    <td <?php echo $ev ?> ><?php echo trim($rst['cli_apellidos'] . ' ' . $rst['cli_nombres'] . ' ' . $rst['cli_raz_social']) ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_fecha'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_pais'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_canton'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_parroquia'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_calle_prin'] . ' ' . $rst['cli_numero'] . ' ' . $rst['cli_calle_sec'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_telefono'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_email'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_estado'] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png" width="16px"  class="auxBtn" onclick="del(<?php echo $rst[cli_id] ?>, 1, '<?php echo $rst[cli_ced_ruc] ?>')">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png"  width="16px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[cli_id] ?>, 0)">
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

