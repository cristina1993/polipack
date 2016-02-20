<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_empleados.php';
$Clase_empleados = new Clase_empleados();
if (isset($_GET[search])) {
    $txt = trim(strtoupper($_GET[codigo]));
    $sec = $_GET[seccion];
    $est = $_GET[estado];
    $hor = $_GET[horario];
    $dis = $_GET[discapacidad];

    if ($dis == 'on') {
        $text = " where emp_disc='t'";
        $sec = 0;
        $hor = 0;
        $est = '';
    } else if ($sec == 0 && $est == '' && $hor == 0) {
        $text = "where emp_documento like '%$txt%' or emp_apellido_paterno like '%$txt%' or emp_apellido_materno like '%$txt%' or emp_nombres like '%$txt%' or emp_codigo like '%$txt%'";
        $sec = 0;
        $hor = 0;
        $est = '';
        $dis = 'off';
    } else if ($sec != 0 && $est == '' && $hor == 0) {
        $text = "where sec_id=$sec and (emp_documento like '%$txt%' or emp_apellido_paterno like '%$txt%' or emp_apellido_materno like '%$txt%' or emp_nombres like '%$txt%' or emp_codigo like '%$txt%')";
        $hor = 0;
        $est = '';
        $dis = 'off';
    } else if ($est != '' && $sec == 0 && $hor == 0) {
        $text = "where emp_estado=$est and (emp_documento like '%$txt%' or emp_apellido_paterno like '%$txt%' or emp_apellido_materno like '%$txt%' or emp_nombres like '%$txt%' or emp_codigo like '%$txt%')";
        $sec = 0;
        $hor = 0;
        $dis = 'off';
    } else if ($hor != 0 && $sec == 0 && $est == '') {
        $text = "where grp_id=$hor and (emp_documento like '%$txt%' or emp_apellido_paterno like '%$txt%' or emp_apellido_materno like '%$txt%' or emp_nombres like '%$txt%' or emp_codigo like '%$txt%')";
        $sec = 0;
        $est = '';
        $dis = 'off';
    } else if ($hor != 0 && $sec != 0 && $est != '') {
        $text = "where (emp_documento like '%$txt%' or emp_apellido_paterno like '%$txt%' or emp_apellido_materno like '%$txt%' or emp_nombres like '%$txt%' or emp_codigo like '%$txt%') sec_id=$sec and emp_estado=$est and grp_id=$hor";
    }
    
    $cns = $Clase_empleados->lista_buscador_empleados($text);
} else {
    $cns = $Clase_empleados->lista_buscador_empleados();
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

            function auxWindow(a, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,90%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_empleados.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_empleados.php?id=' + id;
                        look_menu();
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/Form_empleados.php?id=' + id + '&x=' + x;
                        look_menu();
                        break;
                }

            }

            function del(id, op)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_cliente.php", {act: 48, id: id, op: op}, function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }
        </script> 
        <style>
            #mn39{
                background:black;
                color:white;
                border: solid 1px white;
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
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
                </center>               
                <center class="cont_title" >LISTA DE EMPLEADOS</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR:<input type="text" name="codigo" id="codigo" size="15" value="<?PHP echo $txt ?>" />
                        SECCION:
                        <select id="seccion" name="seccion" style="width: 150px">
                            <option value="0">Seleccione</option>
                            <?php
                            $cns_sec = $Clase_empleados->lista_secciones();
                            while ($rst_sec = pg_fetch_array($cns_sec)) {
                                echo "<option value='$rst_sec[sec_id]'>$rst_sec[sec_descricpion]</option>";
                            }
                            ?>

                        </select>

                        ESTADO:
                        <select id="estado" name="estado" style="width: 150px">
                            <option value="" >SELECCIONE</option>
                            <option value="0" >ACTIVO</option>
                            <option value="1" >RETIRADO</option>
                        </select>
                        HORARIO:
                        <select id="horario" name="horario" style="width: 150px">
                            <option value="0">SELECCIONE</option>
                            <?php
                            $cns_hor = $Clase_empleados->lista_grupo_horarios();
                            while ($rst_hor = pg_fetch_array($cns_hor)) {
                                echo "<option $sel value='$rst_hor[gru_id]'>$rst_hor[gru_horarios]</option>";
                            }
                            ?>
                        </select>
                        DISCAPACIDAD            
                        <input  type="checkbox" name="discapacidad" id="discapacidad"/>
                        <button class="btn" title="Buscar" id="search" name="search" onclick="frmSearch.submit()">Buscar</button><img src="../img/finder.png"/>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th align="Center">No</th>
                    <th align="Center" colspan="2">Codigo</th>
                    <th align="Center">Cedula</th>
                    <th align="Center">F/Ingreso</th>
                    <th align="Center">F/Nacimiento</th>
                    <th align="Center">Apellidos/Nombres</th>
                    <th align="Center">Discapacidad</th>
                    <th align="Center">Sexo</th>                        
                    <th align="Center">Edad</th>                                                
                    <th align="Center">Division</th>  
                    <th align="Center">Seccion</th>
                    <th align="Center">Area</th>
                    <th align="Center">Cargo</th>
                    <th align="Center">Horario</th>
                    <th align="Center">Status</th>                          
                    <th align="Center">Observaciones</th>                          
                    <th align="Center">Acciones</th>                          

                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_sec = pg_fetch_array($Clase_empleados->lista_una_seccion_id($rst[sec_id]));
                    $rst_div = pg_fetch_array($Clase_empleados->lista_una_division($rst_sec[sec_area]));
                    $rst_sbs = pg_fetch_array($Clase_empleados->lista_una_subseccion($rst[emp_sub_sec]));
                    $rst_hor = pg_fetch_array($Clase_empleados->lista_un_horario($rst[grp_id]));
                    if ($rst['emp_sexo'] == 't') {
                        $gn = 'M';
                    } else {
                        $gn = 'F';
                    }
                    switch ($rst['emp_estado']) {
                        case 0:$status = "Act";
                            break;
                        case 1:$status = "Inact";
                            break;
                    }
                    $fnac = date("d-m-Y", strtotime($rst['emp_fnacimiento']));
                    $aFecha = explode('-', $fnac);
                    $edad = floor(( (date("Y") - $aFecha[2] ) * 372 + ( date("m") - $aFecha[1] ) * 31 + Date("d") - $aFecha[0] ) / 372);

                    if ($rst[emp_disc] == 't') {
                        $discapacidad = 'SI';
                    } else {
                        $discapacidad = 'NO';
                    }
                    if(empty($rst['emp_foto'])){
                       $rst['emp_foto']='../Empimg/no_foto.png';
                    }                    
                    ?>
                    <tr>
                        <td><?PHP echo $n; ?></td>  
                        <td align="left"><img src="<?PHP echo $rst['emp_foto']; ?>" width="25" height="20" /></td>
                        <td align="center" ><?PHP echo $rst['emp_codigo']; ?></td>
                        <td align="left" class="ficha" onclick="auxWindow(2,<?php echo $rst['emp_id'] ?>)" ><?PHP echo $rst['emp_documento']; ?></td>
                        <td align="left"><?PHP echo $rst['emp_fregistro']; ?></td>
                        <td align="left"><?PHP echo $rst['emp_fnacimiento']; ?></td>
                        <td align="left"><?PHP echo $rst['emp_apellido_paterno'] . " " . $rst['emp_apellido_materno'] . " " . $rst['emp_nombres']; ?></td>
                        <td align="center"><?PHP echo $discapacidad; ?></td>
                        <td align="left"><?PHP echo $gn; ?></td>
                        <td align="left"><?PHP echo $edad . " a&ntilde;os"; ?></td>          
                        <td align="left"><?PHP echo $rst_div[div_descripcion]?></td>          
                        <td align="left"><?PHP echo $rst_sec['sec_descricpion']; ?></td>          
                        <td align="left"><?PHP echo $rst_sbs[sbs_descripcion]; ?></td>          
                        <td align="left"><?PHP echo $rst['emp_cargo']; ?></td>
                        <td align="left"><?PHP echo $rst_hor['gru_horarios']; ?></td>
                        <td align="left"><?PHP echo $status; ?></td>
                        <td align="center"><?PHP echo $rst['emp_obs'] ?></td>          
                        <td align="center" onclick="auxWindow(1,<?php echo $rst['emp_id'] ?>, 0)" align="left"><img src="../img/upd.png" ></img></td>                                
                    </tr>
                    <?PHP
                }
                ?>
            </tbody>


        </table>            

    </body>    
</html>

<script>

    var sec = '<?PHP echo $sec ?>';
    var hor = '<?PHP echo $hor ?>';
    var est = '<?PHP echo $est ?>';
    var dis = '<?PHP echo $dis ?>';
    $('#seccion').val(sec);
    $('#horario').val(hor);
    $('#estado').val(est);
    if (dis == 'on') {
        $('#discapacidad').attr('checked', true);
    } else {
        $('#discapacidad').attr('checked', false);
    }
</script>
