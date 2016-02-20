<?php
include_once("../Clases/clsUsers.php");
include_once '../Includes/permisos.php';
$cnsUser = $User->listAllUser();

?>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Formulas</title>
        <script type="text/javascript">
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });
            function auxWindow(a, id, sts)
            {
                frm = parent.document.getElementById('bottomFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_usuario.php';
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_usuario.php?id=' + id;
                        break;
                    case 2://Cambiar Estado
                        if (confirm('Esta seguro de cambiar de Estado a este Usuario?') == true)
                        {
                            if (sts == 't')
                            {
                                sts = 'f';
                            } else {
                                sts = 't';
                            }
                            data = Array(sts);
                            $.post("actions.php", {act: 9, 'data[]': data, id: id},
                            function (dt) {
                                if (dt == 0)
                                {
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_usuarios.php';
                                    parent.document.getElementById('bottomFrame').src = '';
                                } else {
                                    alert(dt);
                                }
                            })

                        }

                        break;
                    case 3://Permisos
                        frm.src = '../Scripts/Form_permisos.php?id=' + id;
                        break;
                }

            }
            function delete_all()
            {
                if (prompt("Advertencia Este proceso Eliminara todos los registros de la DB; \n Si es Usuario Autorizado Ingrese el Codigo para Ejecutar") == 1234)
                {
                    $.post("actions.php", {act: 14},
                    function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_usuarios.php';
                        }
                    });
                }

            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

           
        </script>
        <style>
            #mn2{
                background:black;
                color:white;
                border: solid 1px white;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl(1, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >LISTA DE USUARIOS</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Usuario:<input type="text" name="txt" size="15" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
            </caption>
            <thead>
            <th align="left">No</th>
            <th align="left">Usuario</th>
            <th align="left">F/Registro</th>
            <th align="left">Persona</th>
            <th align="left" <?php echo $Prt->edition ?> >Estado</th>                                                
            <th align="left" <?php echo $Prt->edition ?> >Acciones</th>                                                
        </thead>
        <tbody>
            <?php
            $n = 0;
            while ($rst = pg_fetch_array($cnsUser)) {
                $n++;
                if ($rst[usu_status] == 't') {
                    $estado = 'Activo';
                } else {
                    $estado = 'Inactivo';
                }
                ?>
                <tr>
                    <td  align="left"><?PHP echo $n ?></td>
                    <td  align="left" style="text-transform:lowercase;font-size:14px"><?PHP echo $rst['usu_login'] ?></td>
                    <td  align="left"><?PHP echo $rst['usu_date']; ?></td>
                    <td  align="left"><?PHP echo $rst['usu_person']; ?></td>

                    <?php
                    if ($rst[usu_id] == 1) {
                        if ($_SESSION['usuid'] == 1) {
                            ?>
                            <td  align="left" onclick="auxWindow(2,<?php echo $rst[usu_id] ?>, '<?php echo $rst[usu_status] ?>')"><?PHP echo $estado; ?></td>
                            <td align="center">
                                <img src="../img/upd.png" width="20px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[usu_id] ?>, 2)" />
                                <img src="../img/Permisos.png" class="auxBtn" width="16px" onclick="auxWindow(3,<?php echo $rst[usu_id] ?>)" />
                            </td>
                            <?php
                        } else {
                            ?>
                            <td  align="left" onclick="alert('Ud no tiene permisos para administrar este usuario')"><?PHP echo $estado; ?></td>
                            <td align="center">
                                <img src="../img/no_edit.png" class="auxBtn"  width="20px" onclick="alert('Ud no tiene permisos para administrar este usuario')" />
                                <img src="../img/no_permit.png" class="auxBtn" width="16px" onclick="alert('Ud no tiene permisos para administrar este usuario')" />
                            </td>
                            <?php
                        }
                    } else {
                        ?>
                        <td  align="left" onclick="auxWindow(2,<?php echo $rst[usu_id] ?>, '<?php echo $rst[usu_status] ?>')"><?PHP echo $estado; ?></td>
                        <td align="center">
                            <img src="../img/upd.png" class="auxBtn"  width="20px" onclick="auxWindow(1,<?php echo $rst[usu_id] ?>, 2)" />
                            <img src="../img/Permisos.png" class="auxBtn" width="16px" onclick="auxWindow(3,<?php echo $rst[usu_id] ?>)" />
                        </td>
                        <?php
                    }
                    ?>                              
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</body>
<p id="back-top" style="display: block;">
    <a href="#" >&#9650;Inicio</a>
</p>
</html>
