<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_movimientopt.php'; //cambiar clsClase_productos
$Clase_industrial_movimientopt = new Clase_industrial_movimientopt();
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $prod = trim(strtoupper($_GET[prod]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($prod)) {
        $ncom = pg_num_rows($Clase_industrial_movimientopt->lista_buscar_comerciales($prod));
        if ($ncom > 0) {
            $pro = "and (p.pro_a like '%$prod%' or p.pro_b like '%$prod%')
                    and m.mov_fecha_trans between '$fec1' and '$fec2'";
            $cns = $Clase_industrial_movimientopt->lista_buscar_comerciales_fecha($emisor, $pro);
            $tab = 1;
        }
        $nind = pg_num_rows($Clase_industrial_movimientopt->lista_buscar_industriales($prod));
        if ($nind > 0) {
            $pro = "and (p.pro_codigo like '%$prod%' or p.pro_descripcion like '%$prod%')
                     and m.mov_fecha_trans between '$fec1' and '$fec2'";
            $cns = $Clase_industrial_movimientopt->lista_buscar_industriales_fecha($emisor, $pro);
            $tab = 0;
        }
        $txt = '';
        $det = 1;
    } else if (!empty($txt)) {
        $txt = " and (m.mov_documento like '%$txt%' or m.mov_guia_transporte like '%$txt%' or c.cli_raz_social like '%$txt%' or t.trs_descripcion like '%$txt%')
                 and m.mov_fecha_trans between '$fec1' and '$fec2'";
        $prod = '';
        $cns = $Clase_industrial_movimientopt->lista_buscador_industrial_ingresopt($emisor, $txt);
        $det = 0;
    } else {
        $txt = " and m.mov_fecha_trans between '$fec1' and '$fec2' ";
        $cns = $Clase_industrial_movimientopt->lista_buscador_industrial_ingresopt($emisor, $txt);
        $det = 0;
    }
} else {
    $txt = '';
    $trs = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    //$cns = $Clase_industrial_movimientopt->lista_movimiento_industrial($emisor, $trs);
    $det = 0;
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
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
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
                    case 0://Nuevo
                        $.post("actions_industrial_ingresopt.php", {op: 15}, function (dt) {
                            secuencial = '001-' + dt;
                            frm.src = '../Scripts/Form_industrial_movimientopt.php?emisor=' +<?php echo $emisor ?> + '&sec=' + secuencial;//Cambiar Form_productos
                            if (secuencial != 0) {
                                $.post("actions_industrial_ingresopt.php", {op: 16, sec: secuencial}, function (dt) {
                                    if (dt != 0) {
                                        alert(dt);
                                    }
                                });
                            }
                        });
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_industrial_movimientopt.php?id=' + id + '&x=' + x;//Cambiar Form_productos
                        break;
                }
            }
            function descargar_archivo() {
                window.location = '../formatos/descargar_archivo.php?archivo=inventario.csv';
            }
            function load_file() {
                $('#frm_file').submit();
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

        </script> 
        <style>
            #mn56,
            #mn61,
            #mn72,
            #mn77,
            #mn82,
            #mn87,
            #mn92,
            #mn97,
            #mn102,
            #mn107{
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
                <center class="cont_title" ><?PHP echo 'MOVIMIENTO DE PRODUCTO TERMINADO ' . $bodega ?> </center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <a href="#" onclick="descargar_archivo()" style="float:right;text-transform:capitalize;margin-left:15px;margin-top:10px;text-decoration:none;color:#ccc; ">Descargar Formato<img src="../img/xls.png" width="16px;" /></a>

                    <form id="frm_file" name="frm_file" style="float:right" action="actions_upload.php" method="POST" enctype="multipart/form-data">
                        <div class="upload">
                            ...<input type="file"  name="archivo" id="archivo" onchange="load_file()" >
                        </div>
                    </form>
                    <font style="float:right" id="txt_load">Cargar Datos:</font>

                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        PRODUCTO:<input type="text" name="prod" size="15" id="prod" style="font-size: 10px"/>
                        MOVIMIENTO:<input type="text" name="txt" size="25" id="txt" list="transacciones" style="font-size: 10px"/>
                        DESDE:<input type="text" size="10" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="10" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th></th>
                    <th colspan="4">Documento</th>
                    <th colspan="5">Producto Terminado</th>
                    <th colspan="4">Transaccción</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Usuario</th>
                    <th>Fecha de Transacción</th>
                    <th>Documento No</th>
                    <th>Guía de Recepción</th>
                    <th>Proveedor</th>
                    <!--<th>Id.Prod</th>-->
                    <th>Familia</th>
                    <th>Código</th>
                    <th>Lote</th>
                    <th>Descripción</th>
                    <th>Unidad</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                $grup = '';
                if ($det == 0) {
                    while ($rst = pg_fetch_array($cns)) {
                        $n++;

                        echo "<tr>
                            <td>$n</td>";

                        if ($grup != $rst['mov_documento']) {
                            echo "<td>$rst[mov_usuario]</td>
                                <td align='center'>$rst[mov_fecha_trans]</td>
                                <td>$rst[mov_documento]</td>
                                <td>$rst[mov_guia_transporte]</td>
                                <td>$rst[cli_raz_social]</td>";
                        } else {
                            echo "<td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>";
                        }
                        if ($rst[mov_tabla] == 1) {
                            $rst1 = pg_fetch_array($Clase_industrial_movimientopt->lista_prod_comerciales($rst['pro_id']));
                            $rst['pro_codigo'] = $rst1['pro_a'];
                            $rst['pro_descripcion'] = $rst1['pro_b'];
                            $fl = explode('&', $rst1['pro_tipo']);
                            $fml = $fl[9];
                            $lote = $rst1['pro_ac'];
                        } else {
                            $rst1 = pg_fetch_array($Clase_industrial_movimientopt->lista_prod_industriales($rst['pro_id']));
                            $rst['pro_codigo'] = $rst1['pro_codigo'];
                            $rst['pro_descripcion'] = $rst1['pro_descripcion'];
                            $fml = '';
                            $lote = '';
                        }
                        echo "<td>$fml</td>     
                            <td>$rst[pro_codigo]</td>                    
                            <td>$lote</td>                    
                            <td>$rst[pro_descripcion]</td>
                            <td>$rst[pro_uni]</td>
                            <td>$rst[trs_descripcion]</td>
                            <td>$rst[mov_cantidad]</td>
                        </tr>";
                        $grup = $rst['mov_documento'];
                    }
                } else {
                    while ($rst1 = pg_fetch_array($cns)) {
                        if ($tab == 1) {
                            $rst1[pro_id] = $rst1[id];
                            $rst1['pro_codigo'] = $rst1['pro_a'];
                            $rst1['pro_descripcion'] = $rst1['pro_b'];
                            $fl = explode('&', $rst1['pro_tipo']);
                            $fml = $fl[9];
                            $lote = $rst1['pro_ac'];
                        } else {
                            $rst1['pro_codigo'] = $rst1['pro_codigo'];
                            $rst1['pro_descripcion'] = $rst1['pro_descripcion'];
                            $fml = '';
                            $lote = '';
                            $rst1[pro_id] = $rst1[pro_id];
                        }
                        $mov = pg_num_rows($Clase_industrial_movimientopt->buscar_un_movimiento($rst1[pro_id], $tab, $emisor));
                        if ($mov > 0) {
                            $cns1 = $Clase_industrial_movimientopt->buscar_un_movimiento($rst1[pro_id], $tab, $emisor);
                            while ($rst = pg_fetch_array($cns1)) {
                                $n++;

                                echo "<tr>
                                    <td>$n</td>";

                                if ($grup != $rst['mov_documento']) {

                                    echo "<td><?php echo $rst[mov_usuario]</td>
                                       <td align='center'><?php echo $rst[mov_fecha_trans]</td>
                                        <td>$rst[mov_documento] </td>
                                        <td>$rst[mov_guia_transporte] </td>
                                        <td>$rst[cli_raz_social] </td>";
                                } else {

                                    echo "<td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>";
                                }
                                echo "<td>$fml</td>     
                                    <td>$rst1[pro_codigo]</td>                    
                                    <td>$lote</td>                    
                                    <td>$rst1[pro_descripcion]</td>
                                    <td>$rst1[pro_uni]</td>
                                    <td>$rst[trs_descripcion]</td>
                                    <td>$rst[mov_cantidad]</td>
                                </tr>";
                                $grup = $rst['mov_documento'];
                            }
                        }
                    }
                }
                ?>
            </tbody>
        </table>            
    </body>    
</html>
<datalist id="transacciones">
    <?php
    $cns_trans = $Clase_industrial_movimientopt->lista_combo_transacciones();
    while ($rst_tran = pg_fetch_array($cns_trans)) {
        ?> 
        <option value="<?php echo$rst_tran[trs_descripcion] ?>"><?php echo$rst_tran[trs_descripcion] ?></option>;
        <?php
    }
    ?>  
</datalist>
