<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_resultados_operaciones.php'; //cambiar clsClase_productos
$Set = new Clase_resultados_operaciones();
if (isset($_GET[buscar])) {
    $txt = trim(strtoupper($_GET[txt]));
    $anio = $_GET[anio];
    $mes = $_GET[mes];
    if (!empty($txt)) {
        $text = "where rop_secuencial like '%$txt%'";
    } else if (!empty($anio) && !empty($mes)) {
        $text = "where rop_anio='$anio' and rop_mes='$mes'";
    } else if (!empty($anio)) {
        $text = "where rop_anio='$anio'";
    } else if (!empty($mes)) {
        $text = "where rop_mes='$mes'";
    }
    $cns = $Set->lista_resultado_operaciones($text);
    $rst1 = pg_fetch_array($Set->lista_resultado_operaciones($text));
    if (empty($rst1)) {
        $sms = 'Registro no existe ¿desea crear uno?';
    }
} else {
    $txt = '';
    $anio = date('Y');
    $mes = date('m');
//    $cns = $Set->lista_resultado_operaciones();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            var sms = '<?php echo $sms ?>'
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                if (sms != '') {
                    var r = confirm(sms);
                    if (r == true) {
                        auxWindow(0);
                    }
                }
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
                parent.document.getElementById('contenedor2').rows = "*,80%";

                switch (a)
                {
                    case 0://Nuevo
                        if (anio.value == '0') {
                            alert('Seleccione Año');
                            return false;
                        }
                        if (mes.value == '0') {
                            alert('Seleccione Mes');
                            return false;
                        }
                        frm.src = '../Scripts/Form_resultados_operaciones.php?anio=' + anio.value + '&mes=' + mes.value;//Cambiar Form_productos
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_resultados_operaciones.php?id=' + id;//Cambiar Form_productos
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/Form_resultados_operaciones.php?id=' + id + '&x=1';//Cambiar Form_productos
                        break;
                }
            }
            function del(id, op)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_resultados_operaciones.php", {id: id, op: op}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_resultados_operaciones.php';
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
            #mn69{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input[type=text]{
                text-transform: uppercase;
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
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >RESULTADO DE OPERACIONES</center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>-->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
                        Perdiodo: 
                        Año:
                        <select id="anio" name="anio">
                            <option value="0">SELECCIONE</option>
                            <option value="2015">2015</option>
                            <option value="2016">2016</option>
                            <option value="2017">2017</option>
                            <option value="2018">2018</option>
                            <option value="2019">2019</option>
                            <option value="2020">2020</option>
                            <option value="2021">2021</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                            <option value="2029">2029</option>
                            <option value="2030">2030</option>
                        </select>
                        Mes:
                        <select id="mes" name="mes">
                            <option value="0">SELECCIONE</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                        <button class="btn" title="Buscar" id='buscar' name='buscar' onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th style="width: 30px">No</th>
            <th>Codigo</th>
            <th>Año</th>
            <th>Mes</th>
            <th style="width: 200px">Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $ev = "onclick='auxWindow(2,$rst[rop_id],1)'";
                $n++;
                switch ($rst[rop_mes]) {
                    case 1:
                        $mes_letras = 'ENERO';
                        break;
                    case 2:
                        $mes_letras = 'FEBRERO';
                        break;
                    case 3:
                        $mes_letras = 'MARZO';
                        break;
                    case 4:
                        $mes_letras = 'ABRIL';
                        break;
                    case 5:
                        $mes_letras = 'MAYO';
                        break;
                    case 6:
                        $mes_letras = 'JUNIO';
                        break;
                    case 7:
                        $mes_letras = 'JULIO';
                        break;
                    case 8:
                        $mes_letras = 'AGOSTO';
                        break;
                    case 9:
                        $mes_letras = 'SEPTIEMBRE';
                        break;
                    case 10:
                        $mes_letras = 'OCTUBRE';
                        break;
                    case 11:
                        $mes_letras = 'NOVIEMBRE';
                        break;
                    case 12:
                        $mes_letras = 'DICIEMBRE';
                        break;
                }
                ?>
                <tr>
                    <td <?php echo $ev ?>><?php echo $n ?></td>
                    <td <?php echo $ev ?>><?php echo $rst['rop_secuencial'] ?></td>
                    <td <?php echo $ev ?>><?php echo $rst['rop_anio'] ?></td>
                    <td <?php echo $ev ?>><?php echo $mes_letras ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png"  class="auxBtn" onclick="del(<?php echo $rst[rop_id] ?>, 1)">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png"  class="auxBtn" onclick="auxWindow(1,<?php echo $rst[rop_id] ?>, 0)">
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
<script>
    var a = '<?php echo $anio ?>';
    var m = '<?php echo $mes ?>';
    $('#anio').val(a);
    $('#mes').val(m);
</script>
</html>

