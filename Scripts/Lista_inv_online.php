<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_inv_online.php';
$Inv = new Inv_Online();
$limit = '';
if (isset($_GET[search])) {
    $cod = strtoupper(trim($_GET[codigo]));
    $talla = strtoupper(trim($_GET[talla]));
    $fml = strtoupper(trim($_GET[fml]));
    if (!empty($cod)) {
        $cns = $Inv->lista_general_codigo(0, $limit, $cod);
    }else if(!empty($talla)){
        $cns = $Inv->lista_general_talla(0, $limit, $talla);
    }else{
        $cns = $Inv->lista_general_familia(0, $limit, $fml);
    }
} else {
    //$cns = $Inv->lista_general(0, $limit);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Inventario Online</title>
    <script>
        $(function () {
            $("#tbl").tablesorter(
                    {widgets: ['stickyHeaders'],
                        sortMultiSortKey: 'altKey',
                        widthFixed: true});
            parent.document.getElementById('bottomFrame').src = '';
            parent.document.getElementById('contenedor2').rows = "*,0%";
        });
        function acciones(obj) {
            cmp = obj.id.split('%');
            if (cmp[0] == 'inv_grupo_hogar' || cmp[0] == 'inv_grupo_hotel' || cmp[0] == 'inv_grupo_hospital' || cmp[0] == 'inv_grupo_industrial') {
                if (obj.checked == true) {
                    val = 1;
                } else {
                    val = 0;
                }
            } else {
                val = obj.value;
            }
            $.ajax({
                beforeSend: function () {
                    //  alert(val);
                    //  return false;
                },
                type: 'POST',
                url: 'actions_inv_online.php',
                data: {op: 0, campo: cmp[0], val: val, id: cmp[1]},
                success: function (dt) {
                    if (dt != 0) {
                        color = 'brown';
                    } else {
                        color = '#DFF2BF';
                    }
                    $('#row' + obj.lang).css('background', color);
                }
            })

        }
    </script>
    <head>
        <style>
            #mn270{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #tbl tbody tr td input{
                border:none !important;
                height:25px; 
                font-size:10px; 
            }
            tfoot tr td{
                background:#005580;
                color:white;
                font-size:12px;
                font-weight:bold; 
            }
        </style>
    </head>
    <body>
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
                <center class="cont_title" >INVENTARIO ONLINE</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo:<input type="text" name="codigo" size="15" />
                        Linea:<input type="text" name="talla" size="15" />
                        <select name="fml" id="fml" >
                            <option value="x">Familia</option>
                            <?php
                            $cns_fml = $Inv->lista_familias();
                            while ($rst_fml = pg_fetch_array($cns_fml)) {
                                if ($fml == $rst_fml[protipo]) {
                                    $sel = 'selected';
                                } else {
                                    $sel = '';
                                }
                                echo "<option $sel value=$rst_fml[protipo]>$rst_fml[protipo]</option>";
                            }
                            if ($faml == 'INDUSTRIAL') {
                                $sel = 'selected';
                            }
                            ?>
                            <option <?php echo $sel ?> value="INDUSTRIAL">Industriales</option>
                        </select>
                        <button class="btn" title="Buscar" name="search" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Codigo</th>
                    <th>---------------------------Descripcion---------------------------</th>
                    <th>Familia</th>            
                    <th>Linea</th>
                    <th>Talla</th>            
                    <th>Grupo</th>
                    <th>PVP</th>            
                    <th>Desc</th>
                    <th>G.Hogar</th>            
                    <th>G.Hotel</th>
                    <th>G.Hospital</th>            
                    <th>G.Industrial</th>
                    <th>Bod.Max</th>            
                    <th>Bod1</th>
                    <th>Bod2</th>
                    <th>Bod3</th>
                    <th>Bod4</th>
                    <th>Bod5</th>
                    <th>Bod6</th>
                    <th>Bod7</th>
                    <th>Bod8</th>
                    <th>Bod9</th>
                    <th>Bod10</th>
                    <th>Bod11</th>
                    <th>Bod12</th>
                    <th>Bod13</th>
                    <th>Bod14</th>
                    <th>Suma</th>
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $hg = '';
                    $ht = '';
                    $hp = '';
                    $in = '';
                    if ($rst[inv_grupo_hogar] == 1) {
                        $hg = 'checked';
                    }
                    if ($rst[inv_grupo_hotel] == 1) {
                        $ht = 'checked';
                    }
                    if ($rst[inv_grupo_hospital] == 1) {
                        $hp = 'checked';
                    }
                    if ($rst[inv_grupo_industrial] == 1) {
                        $in = 'checked';
                    }
                    $tb1+=$rst[inv_bodega1];
                    $tb2+=$rst[inv_bodega2];
                    $tb3+=$rst[inv_bodega3];
                    $tb4+=$rst[inv_bodega4];
                    $tb5+=$rst[inv_bodega5];
                    $tb6+=$rst[inv_bodega6];
                    $tb7+=$rst[inv_bodega7];
                    $tb8+=$rst[inv_bodega8];
                    $tb9+=$rst[inv_bodega9];
                    $tb10+=$rst[inv_bodega10];
                    $tb11+=$rst[inv_bodega11];
                    $tb12+=$rst[inv_bodega12];
                    $tb13+=$rst[inv_bodega13];
                    $tb14+=$rst[inv_bodega14];
                    ?>
                    <tr id="<?php echo 'row' . $rst[inv_id] ?>" >
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[inv_codigo_barras] ?></td>
                        <td><?php echo $rst[inv_descripcion] ?></td>                     
                        <td><?php echo $rst[inv_subgrupo_familia] ?></td>                    
                        <td><?php echo $rst[inv_marca] ?></td>
                        <td><?php echo $rst[inv_talla_medida] ?></td>
                        <td><input type="text" size="15" id="<?php echo 'inv_grupo_producto%' . $rst[inv_id] ?>" value="<?php echo $rst[inv_grupo_producto] ?>" style="text-align:center" onchange="acciones(this)" lang="<?php echo $rst[inv_id] ?>" /></td>
                        <td align="right"><?php echo number_format($rst[inv_precio_pvp], 2) ?></td>
                        <td><input type="text" size="5" id="<?php echo 'inv_descuento%' . $rst[inv_id] ?>" value="<?php echo number_format($rst[inv_descuento]) ?>" style="text-align:right" onchange="acciones(this)" lang="<?php echo $rst[inv_id] ?>" /></td>
                        <td align="center"><input type="checkbox" <?php echo $hg ?> id="<?php echo 'inv_grupo_hogar%' . $rst[inv_id] ?>" onclick="acciones(this)" lang="<?php echo $rst[inv_id] ?>" /></td>
                        <td align="center"><input type="checkbox" <?php echo $ht ?> id="<?php echo 'inv_grupo_hotel%' . $rst[inv_id] ?>" onclick="acciones(this)" lang="<?php echo $rst[inv_id] ?>" /></td>
                        <td align="center"><input type="checkbox" <?php echo $hp ?> id="<?php echo 'inv_grupo_hospital%' . $rst[inv_id] ?>" onclick="acciones(this)" lang="<?php echo $rst[inv_id] ?>" /></td>
                        <td align="center"><input type="checkbox" <?php echo $in ?> id="<?php echo 'inv_grupo_industrial%' . $rst[inv_id] ?>" onclick="acciones(this)" lang="<?php echo $rst[inv_id] ?>" /></td>
                        <td align="center"><input type="text" size="5" id="<?php echo 'inv_bod_virtual%' . $rst[inv_id] ?>" value="<?php echo number_format($rst[inv_bod_virtual]) ?>" style="text-align:right" onchange="acciones(this)" lang="<?php echo $rst[inv_id] ?>" /></td>
                        <td align="right"><?php echo number_format($rst[inv_bodega1], 0) ?></td>                        
                        <td align="right"><?php echo number_format($rst[inv_bodega2], 0) ?></td>
                        <td align="right"><?php echo number_format($rst[inv_bodega3], 0) ?></td>                        
                        <td align="right"><?php echo number_format($rst[inv_bodega4], 0) ?></td>
                        <td align="right"><?php echo number_format($rst[inv_bodega5], 0) ?></td>                        
                        <td align="right"><?php echo number_format($rst[inv_bodega6], 0) ?></td>
                        <td align="right"><?php echo number_format($rst[inv_bodega7], 0) ?></td>                        
                        <td align="right"><?php echo number_format($rst[inv_bodega8], 0) ?></td>
                        <td align="right"><?php echo number_format($rst[inv_bodega9], 0) ?></td>                        
                        <td align="right"><?php echo number_format($rst[inv_bodega10], 0) ?></td>
                        <td align="right"><?php echo number_format($rst[inv_bodega11], 0) ?></td>                        
                        <td align="right"><?php echo number_format($rst[inv_bodega12], 0) ?></td>
                        <td align="right"><?php echo number_format($rst[inv_bodega13], 0) ?></td>                        
                        <td align="right"><?php echo number_format($rst[inv_bodega14], 0) ?></td>
                        <td align="right"><?php echo number_format($rst[inv_bodega1]+$rst[inv_bodega2]+$rst[inv_bodega3]+$rst[inv_bodega4]+$rst[inv_bodega5]+$rst[inv_bodega6]+$rst[inv_bodega7]+$rst[inv_bodega8]+$rst[inv_bodega9]+$rst[inv_bodega10]+$rst[inv_bodega11]+$rst[inv_bodega12]+$rst[inv_bodega13]+$rst[inv_bodega14], 0) ?></td>
                    </tr>  
                    <?PHP
                }
                ?>
            </tbody>
            <?php
            $gt=number_format($tb1+$tb2+$tb3+$tb4+$tb5+$tb6+$tb7+$tb8+$tb9+$tb10+$tb11+$tb12+$tb13+$tb14);
            echo "<tfoot>
                <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                
                <td align=right >$tb1</td>
                <td align=right >$tb2</td>
                <td align=right >$tb3</td>
                <td align=right >$tb4</td>
                <td align=right >$tb5</td>
                <td align=right >$tb6</td>
                <td align=right >$tb7</td>
                <td align=right >$tb8</td>
                <td align=right >$tb9</td>
                <td align=right >$tb10</td>
                <td align=right >$tb11</td>
                <td align=right >$tb12</td>
                <td align=right >$tb13</td>
                <td align=right >$tb14</td>
                <td align=right >$gt</td>
                <tr>
                </tfoot>";
            
            ?>
        </table>            

    </body>
</html>