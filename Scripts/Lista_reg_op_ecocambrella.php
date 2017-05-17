<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_ecocambrella.php';
$Set = new Clase_reg_ecocambrella();
if (empty($_REQUEST[mod_id])) {
    $md = pg_fetch_array($User->list_primer_opl($mod_id, $_SESSION[usuid]));
    $mod = $md[opl_id];
} else {
    $mod = $_REQUEST[mod_id];
}
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $est = $_GET[estado];
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($txt)) {
        $texto = "and (r.rec_numero like'%$ord%' or o.ord_num_orden like'%$ord%') ";
    } else {
        $texto = "and r.rec_fecha between '$fec1' and '$fec2'";
    }
    $cns = $Set->lista_buscador_orden($texto);
} else {
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Ingreso Facturas</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                posicion_accion();
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id, mod, data) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                var boxH = $(window).height() * 0.50;
                var boxW = $(window).width() * 0.50;
                var boxHF = (boxH - 25);
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_reg_op_ecocambrella.php?txt=' + $('#txt').val() + '&estado=' + $('#estado').val() + '&fecha1=' + $('#fecha1').val() + '&fecha2=' + $('#fecha2').val() + '&mod_id=' + mod;
                        parent.document.getElementById('contenedor2').rows = "*,100%";
                        look_menu();
                        break;
                    case 1://Editar
                        wnd = "<iframe id='frmmodal' width='" + boxW + "' height='" + boxHF + "' src='../Reports/pdf_etiqueta_dinamica.php?mod=" + mod + "&data=" + data + "' frameborder='0' />";
                        break;
                }
                $.fallr.show({
                    content: '<center>ETIQUETA</center>'
                            + wnd,
                    width: boxW,
                    height: boxH,
                    duration: 5,
                    position: 'center',
                    buttons: {
                        button1: {
                            text: '&#X00d7;',
                            onclick: function () {
                                $.fallr.hide();
                            }
                        }
                    }
                });
            }


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }



            function del(id, data)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_reg_ecocambrella.php", {op: 1, id: id, data: data}, function (dt) {
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
            #mn180{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #tbl_aux{
                position:fixed; 
                display:none; 
                background:white; 
            }
            #tbl_aux tr{
                border-bottom:solid 1px #ccc  ;
            }
            .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
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
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php?mod_id=$rst_sbm[opl_id]" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" ><?php echo "REGISTRO DE PRODUCCION CAST" ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0, 0, '<?php echo $mod ?>')" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
                        <!--ESTADO:-->
<!--                        <select id="estado" name="estado">
                            <option value="">SELECCIONE</option>
                            <option value="0" >CAJA</option>
                            <option value="1" >DEPOSITADO</option>
                            <option value="2" >REBOTADO</option>
                        </select>-->
                        DESDE:<input type="text" size="10" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="10" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>" />
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>                                                               
                    </form>  
                </center>
            </caption>

            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="6"></th>
                    <th colspan="2">CONFORME</th>
                    <th colspan="2">INCONFORME</th>
                    <!--<th></th>-->
                </tr>
                <tr>
                    <th>No</th>
                    <th>FECHA</th>                                
                    <th>REGISTRO</th>
                    <th>ORDEN</th>
                    <th>DESCRIPCION</th>
                    <th>LOTE</th>
                    <th># ROLLOS</th>
                    <th>PESO</th>
                    <th># ROLLOS</th>
                    <th>PESO</th>
                    <!--<th>Etiqueta</th>-->
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
//                    $rst1 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro_secundario]));
                    $data = array(
                        $rst[ord_num_orden],
                        $rst[pro_ancho],
                        number_format($rst[rec_peso_primario], 2),
                        $rst[pro_espesor],
                        $rst[pro_largo],
                        $rst[rec_lote],
                        $rst[rec_estado]
                    );
                    if ($rst[rec_estado] == 0) {
                        $pcon = $rst[rec_peso_primario];
                        $rcon = $rst[rec_rollo_primario];
                        $pincon = '';
                        $rincon = '';
                    } else if ($rst[rec_estado] == 3) {
                        $pcon = '';
                        $rcon = '';
                        $pincon = $rst[rec_peso_primario];
                        $rincon = $rst[rec_rollo_primario];
                    }
                    echo "<tr>
                        <td>$n</td>
                        <td>$rst[rec_fecha] </td>
                        <td>$rst[rec_numero] </td>
                        <td>$rst[ord_num_orden] </td>
                        <td>$rst[pro_descripcion] </td>
                        <td>$rst[rec_lote] </td>
                        <td align='right'>" . number_format($rcon, 0) . "</td>
                        <td align='right'>" . number_format($pcon, 2) . "</td>
                        <td align='right'>" . number_format($rincon, 0) . "</td>
                        <td align='right'>" . number_format($pincon, 2) . "</td>
                    </tr>";
                    $tot_pcon+=$pcon;
                    $tot_rcon+=$rcon;
                    $tot_pincon+=$pincon;
                    $tot_rincon+=$rincon;
                }
                echo "<tr>
                        <td class='totales'></td>
                        <td class='totales'></td>
                        <td class='totales'></td>
                        <td class='totales'></td>
                        <td class='totales'></td>
                        <td class='totales'>Total</td>
                        <td class='totales' align='right'>" . number_format($tot_rcon, 0) . "</td>
                        <td class='totales' align='right'>" . number_format($tot_pcon, 2) . "</td>
                        <td class='totales' align='right'>" . number_format($tot_rincon, 0) . "</td>
                        <td class='totales' align='right'>" . number_format($tot_pincon, 2) . "</td>
                 </tr>";
                ?>
            </tbody>
        </table>            
    </body>    
</html>
<script>
    var e = '<?php echo $est ?>';
    $('#estado').val(e);
</script>
