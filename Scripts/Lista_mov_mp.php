<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$Prt1 = new Permisos();
$Prt2 = new Permisos();
$Prt3 = new Permisos();
$Prt4 = new Permisos();
$Prt5 = new Permisos();
$Prt6 = new Permisos();
$Prt1->Permit($_SESSION[usuid], 17); //Mov
$Prt2->Permit($_SESSION[usuid], 18); //Inv
$Prt3->Permit($_SESSION[usuid], 19); //Kardex
//Prod Terminado
$Prt4->Permit($_SESSION[usuid], 20); //Mov
$Prt5->Permit($_SESSION[usuid], 21); //Inv
$Prt6->Permit($_SESSION[usuid], 22); //Kardex
if (isset($_GET[from])) {
    $txt = trim(strtoupper($_GET[txt]));
    $from = $_GET[from];
    $until = $_GET[until];
    $bod = $_GET[bod];
    if ($bod == '') {
        $bod = 'no';
    }
    if (!empty($txt)) {
        $txt = "AND (ins.ins_b like '%$txt%' or ins.ins_a like '%$txt%' ) and mov_ubicacion='$bod' AND mov_fecha_trans BETWEEN '$from' AND '$until'";
    } else {
        $txt = "AND mov_fecha_trans BETWEEN '$from' AND '$until' and mov_ubicacion='$bod'";
    }
    $cns = $Set->lista_mov_insumos($txt);
    $nm = trim(strtoupper($_GET[txt]));
} else {
    $from = date('Y-m-d');
    $until = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Inventarios</title>
    <head>
        <script>
            var ids = '<?php echo $bod ?>';
            $(function () {
                Calendar.setup({inputField: from, ifFormat: '%Y-%m-%d', button: im_from});
                Calendar.setup({inputField: until, ifFormat: '%Y-%m-%d', button: im_until});
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                if (ids == 'no') {
                    alert('Elija Ubicacion');
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
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0:
                        window.location = '../Scripts/Lista_egreso_bodega_mp.php';
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_mov_mp_view.php?cod=' + id + '&x=' + x;
                        break;
                    case 2:
                        frm.src = '../Scripts/Form_mov_mp.php';
                        break;
                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

        </script>    
        <style>
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="grid" onclick="alert('¡Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')" ></div>
        <table style="width:100%" id="tbl">

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
                </center>
                <center class="cont_title" >Movimiento de Materia Prima</center>
                <center class="cont_finder">

                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(2, 0)">Nuevo</a>

                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        UBICACION:
                        <select id="bod" name="bod" >
                            <option value="">SELECCIONE</option>
                            <option value="1">Costura</option>
                            <option value="2">Bodega2</option>
                            <option value="3">Bodega3</option>
                        </select>
                        CODIGO:<input type="text" name="txt" size="10" id="txt" value="<?php echo $nm ?>"/>
                        Desde:<input type="text" id="from" name="from" size="10" value="<?php echo $from ?>" />
                        <img src="../img/calendar.png" id="im_from" />
                        Hasta:<input type="text" id="until"  name="until"size="10" value="<?php echo $until ?>" />
                        <img src="../img/calendar.png" id="im_until" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>
                </center>
            </caption>
            <thead>
                <tr>
                    <th>No</th>
                    <!--<th>Bodega</th>-->
                    <th>Documento</th>
                    <th>Fecha</th>
                    <th>Procedencia/Destino</th>
                    <th>Transaccion</th>
                    <th>Referencia</th>
                    <th>Descripcion</th>
                    <th>Unidad</th>
                    <th>Cantidad</th>
                    <th>Costo/U</th>
                    <th>Costo/T</th>
                </tr>                
            </thead>
            <tbody id="tbody">
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    switch ($rst[mov_procedencia_destino]) {
                        case 1:$proc = "Costura";
                            break;
                        case 2:$proc = "Bodega2";
                            break;
                        case 3:$proc = "Bodega3";
                            break;
                        case 4:$proc = "Otro";
                            break;
                    }
                    switch ($rst[mov_unidad]) {
                        case 0:$und = "Unidad";
                            break;
                        case 1:$und = "Metros";
                            break;
                        case 2:$und = "Kilos";
                            break;
                        case 3:$und = "Rollos";
                            break;
                        case 4:$und = "Otro";
                            break;
                    }

                    $rst_trs = pg_fetch_array($Set->lista_una_transaccion($rst[trs_id]));

                    echo "<tr>
                        <td >$n</td>   
                        <td >$rst[mov_documento]</td>
                        <td >$rst[mov_fecha_trans]</td>
                        <td >$proc</td>
                        <td >$rst_trs[trs_descripcion]</td>
                        <td align='center'>$rst[ins_a]</td>
                        <td align='left' >$rst[ins_b]</td>
                        <td align='right' >$und</td>
                        <td align='right' >".number_format($rst[mov_cantidad], 1)."</td>
                        <td align='right' >".number_format($rst[mov_v_unit], 2)."</td>
                        <td align='right' >" . number_format($rst[mov_cantidad] * $rst[mov_v_unit], 2) . "</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>            
    </body>    
</html>
<script>
    var e = '<?php echo $bod ?>';
    $('#bod').val(e);
</script>

