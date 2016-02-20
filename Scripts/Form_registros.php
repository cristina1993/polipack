<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
$tp = $_GET[tp_reg];
if (isset($_GET[id])) {
    $ped = pg_fetch_array($Set->list_one_data_by_id("erp_pedidos", $id));
    $pro = pg_fetch_array($Set->list_one_data_by_id("erp_productos", $ped[ped_d]));
    $pro_set = pg_fetch_array($Set->lista_one_data("erp_productos_set", $pro[ids]));
    $pro_fam = explode("&", $pro_set[pro_tipo]);
}
switch ($tp) {
    case 0:
        $title = "REGISTRO DE CORTE          ";
        break;
    case 1:
        $title = "REGISTRO DE COSTURA        ";
        break;
    case 2:
        $title = "REGISTRO DE EMPAQUE        ";
        break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title><?php echo $tbl_name ?></title>
    <head>
        <script>
            $(function () {
                Calendar.setup({inputField: reg_fecha, ifFormat: '%Y-%m-%d', button: reg_fecha_im});
                if (reg_fecha.value.length == 0)
                {
                    //date=getDa ; 
                    reg_fecha.value = '<?php echo date('Y-m-d') ?>';
                }
            });
            var ids = '<?php echo $id ?>';
            var reg_tipo = '<?php echo $tp ?>';

            function actions(a, id0)
            {
                switch (a)
                {
                    case 0:
                        id = id_item.value;
                        act = 5;
                        break;
                    case 1:
                        id = id0;
                        act = 6;
                        break;
                }
                var fecha = new Date();
                reg_hora = fecha.getHours() + ":" + fecha.getMinutes() + ":" + fecha.getSeconds();
                var data = Array(
                        ids,
                        reg_tipo,
                        reg_maq.value,
                        reg_oper.value,
                        reg_fecha.value,
                        reg_hora,
                        reg_cnt1.value,
                        reg_cnt2.value,
                        reg_cnt3.value,
                        reg_cnt4.value);
                var file = Array(
                        'ids',
                        'reg_tipo',
                        'reg_maq',
                        'reg_oper',
                        'reg_fecha',
                        'reg_hora',
                        'reg_cnt1',
                        'reg_cnt2',
                        'reg_cnt3',
                        'reg_cnt4');

                var fields = Array();
                fields.push('<?php echo "ORDEN No=" . strtoupper($ped[ped_a]) ?>');
                $("#encabezado").find('td').each(function () {
                    var elemento = this;
                    if (elemento.id != 'no') {
                        des = elemento.id + "=" + $(elemento).html();
                        fields.push(des);
                    }
                });
                $("#lista").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });

                $.post("actions.php", {act: act, 'data[]': data, 'field[]': file, 'fields[]': fields, tbl: 'erp_registros_produccion', id: id, s: ''},
                function (dt) {
                    if (dt == 0)
                    {
                        window.location = "Form_registros.php?id=" + ids + "&tp_reg=" + reg_tipo;
                    } else {
                        alert(dt);
                    }
                });


            }
            function load_data(id)
            {
                reg_maq.value = document.getElementById("mq0" + id).value;
                reg_oper.value = document.getElementById("op" + id).innerHTML;
                reg_fecha.value = document.getElementById("fe" + id).innerHTML;
                reg_cnt1.value = document.getElementById("c1" + id).innerHTML;
                reg_cnt2.value = document.getElementById("c2" + id).innerHTML;
                reg_cnt3.value = document.getElementById("c3" + id).innerHTML;
                reg_cnt4.value = document.getElementById("c4" + id).innerHTML;
                id_item.value = id;
            }


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";

            }
        </script>
        <style>
            .importante td{
                background:#015b85;
                color:white;
                font-weight:bolder; 
                font-size:11px; 
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="12" ><?php echo "$title  ORDEN No: " . strtoupper($ped[ped_a]) ?>
                    <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>  </th></tr>
            </thead>
            <tbody id="encabezado">
                <tr>
                    <td id="no"  colspan="2">Fecha de orden:</td>
                    <td id="fecha_orden"><?php echo $ped[ped_b] ?></td>
                    <td id="no" colspan="2">Fecha de Entrega:</td>
                    <td id="no" id="fecha_entrega"><?php echo $ped[ped_b] ?></td>
                    <td id="no" colspan="5"></td>
                </tr>                    
                <tr>
                    <td id="no">Codigo:</td>
                    <td id="codigo"><?php echo $pro[pro_a] ?></td>
                    <td id="no">Familia:</td>
                    <td id="familia"><?php echo $pro_fam[9] ?></td>
                    <td id="no">Linea:</td>
                    <td id="linea"><?php echo $pro[pro_b] ?></td>
                    <td colspan="5"></td>
                </tr>   
            </tbody>
            <thead>       
                <tr>
                    <th>No</th>
                    <th>Fecha</th>
                    <?php
                    $n = 1;
                    while ($n <= 27) {
                        $head = explode("&", $pro_set[$n]);
                        if ($head[0] == "T" && !empty($head[0])) {
                            echo "<th>$head[9]</th>";
                        }
                        $n++;
                    }
                    ?>

                    <th>Total</th>
                    <th>Oper</th>
                    <th>Maquina</th>
                    <th>...</th>
                </tr>

                <tr class="importante" >
                    <th colspan="2" align="right">Cantidad Solicitada</th>
                    <th align="right" ><?php echo $ped[ped_e1] ?></th>
                    <th align="right" ><?php echo $ped[ped_e2] ?></th>
                    <th align="right" ><?php echo $ped[ped_e3] ?></th>
                    <th align="right" ><?php echo $ped[ped_e4] ?></th>
                    <th align="right" ><?php echo $ped[ped_e1] + $ped[ped_e2] + $ped[ped_e3] + $ped[ped_e4] ?></th>
                    <th colspan="3"></th>
                </tr>
            </thead>
            <tbody class="tbl_frm_aux" id="lista" >                 

                <tr>
                    <td><input type="hidden" id="id_item" value="0" /></td>
                    <td>
                        <input type="text" size="10" id="reg_fecha"/>
                        <img src="../img/calendar.png" id="reg_fecha_im" />
                    </td>
                    <td><input type="text" size="5" id="reg_cnt1"/></td>
                    <td><input type="text" size="5" id="reg_cnt2"/></td>
                    <td><input type="text" size="5" id="reg_cnt3"/></td>
                    <td><input type="text" size="5" id="reg_cnt4"/></td>
                    <td></td>
                    <td><input type="text" size="5" id="reg_oper"/></td>
                    <td>
         <!--               <input type="text" size="5" id="reg_maq"/>-->
                        <select id="reg_maq">
                            <?php
                            $cns_mq = $Set->lista_one_data("erp_maquinas", ($tp + 1));
                            while ($rst_maq = pg_fetch_array($cns_mq)) {
                                echo "<option value='$rst_maq[id]'>$rst_maq[maq_a]";
                            }
                            ?>
                        </select>
                    </td>
                    <td><input type="button" id="save" value="Guardar" onclick="actions(0)" /></td>
                </tr>
                <?php
                $n = 1;
                $cnsReg = $Set->list_produccion("erp_registros_produccion", $id, ($tp)); //Esta funcion buscar la produccion
                while ($rstReg = pg_fetch_array($cnsReg)) {
                    $t_c1+=$rstReg[reg_cnt1];
                    $t_c2+=$rstReg[reg_cnt2];
                    $t_c3+=$rstReg[reg_cnt3];
                    $t_c4+=$rstReg[reg_cnt4];
                    $maq = pg_fetch_array($Set->list_one_data_by_id("erp_maquinas", $rstReg[reg_maq]));
                    ?>
                    <tr>
                        <td ><?php echo $n ?></td>
                        <td onclick="load_data(<?php echo $rstReg[id] ?>)" id="<?php echo "fe" . $rstReg[id] ?>" align="right"><?php echo $rstReg[reg_fecha] ?></td>
                        <td onclick="load_data(<?php echo $rstReg[id] ?>)" id="<?php echo "c1" . $rstReg[id] ?>" align="right"><?php echo $rstReg[reg_cnt1] ?></td>
                        <td onclick="load_data(<?php echo $rstReg[id] ?>)" id="<?php echo "c2" . $rstReg[id] ?>" align="right"><?php echo $rstReg[reg_cnt2] ?></td>
                        <td onclick="load_data(<?php echo $rstReg[id] ?>)" id="<?php echo "c3" . $rstReg[id] ?>" align="right"><?php echo $rstReg[reg_cnt3] ?></td>
                        <td onclick="load_data(<?php echo $rstReg[id] ?>)" id="<?php echo "c4" . $rstReg[id] ?>" align="right"><?php echo $rstReg[reg_cnt4] ?></td>
                        <td align="right"><?php echo $rstReg[reg_cnt1] + $rstReg[reg_cnt2] + $rstReg[reg_cnt3] + $rstReg[reg_cnt4] ?></td>                    
                        <td onclick="load_data(<?php echo $rstReg[id] ?>)" id="<?php echo "op" . $rstReg[id] ?>" align="right"><?php echo $rstReg[reg_oper] ?></td>
                        <td onclick="load_data(<?php echo $rstReg[id] ?>)" id="<?php echo "mq" . $rstReg[id] ?>" align="right"><?php echo $maq[maq_a] ?><input type="hidden" id="<?php echo "mq0" . $rstReg[id] ?>" value="<?php echo $rstReg[reg_maq] ?>" /></td>
                        <td align="center">
                            <?php
                            if ($_GET[x] == 1) {
                                ?>
                                <img class="auxBtn" src="../img/del.png" width="16px" onclick="if (confirm('Esta Seguro de eliminar este Item?') == true) {
                                                    actions(1,<?php echo $rstReg[id] ?>)
                                                }" />
                                     <?php
                                 }
                                 ?>

                        </td>
                    </tr>
                    <?php
                    $n++;
                }
                ?>    
                <tr class="importante" >
                    <td colspan="2">Totales:</td>
                    <td align="right"><?php echo number_format($t_c1, 1) ?></td>
                    <td align="right"><?php echo number_format($t_c2, 1) ?></td>
                    <td align="right"><?php echo number_format($t_c3, 1) ?></td>
                    <td align="right"><?php echo number_format($t_c4, 1) ?></td>
                    <td align="right"><?php echo number_format($t_c1 + $t_c2 + $t_c3 + $t_c4, 1) ?></td>                    
                    <td colspan="3"></td>
                </tr>    
            </tbody>
        </table>    
    </body>
</html>