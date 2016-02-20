<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_descuentos.php';
$Desc = new Descuentos();
if (isset($_GET[search])) {
    $txt = strtoupper($_GET[txt]);
    $bod = $_GET[bodega];
    $locales = $_GET[locales];
    $pagina = 1;
    $coddet = strtoupper($_GET[txt]);
    $familia = $_GET[bodega];
    if ($txt != '') {
        $txt = "and (split_part(prod,'&',4) like '$txt%' or split_part(prod,'&',6) like '$txt%')";
        $bod = "";
        $cns = $Desc->lista_reporte_descuentos_productos_buscador($txt, $bod);
    } else {
        $txt = "";
        $bod = "and split_part(prod,'&',9)='$bod'";
        $cns = $Desc->lista_reporte_descuentos_productos_buscador($txt, $bod);
    }
    $f = 0;
} else {
    $pagina = $_GET[pagina];
    $familia = $_GET[familia];
    $locales = $_GET[locales];
    $coddet = $_GET[txt];
    if ($coddet != '') {
        $txt = "and (split_part(prod,'&',4) like '$coddet%' or split_part(prod,'&',6) like '$coddet%')";
        $bod = "";
        $cns = $Desc->lista_reporte_descuentos_productos_buscador($txt, $bod);
    } else {
        $txt = "";
        $bod = "and split_part(prod,'&',9)='$familia'";
        $cns = $Desc->lista_reporte_descuentos_productos_buscador($txt, $bod);
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Tipo de pago</title>
    <script type='text/javascript' src='../js/accounting.js'></script>
    <script type='text/javascript' src='../js/includes.js'></script>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            widthFixed: true
                        });
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                loading('hidden');
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id, fam, loc, cd) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,0%";
                switch (a) {
                    case 0://Nuevo
                        if (cd == 1) {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_descuentos.php?pagina=' + id + '&familia=' + fam + '&locales=' + loc;
                        } else {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_descuentos.php?pagina=' + id + '&txt=' + cd;
                        }
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_cupos.php?id=' + id;
                        look_menu();
                        break;
                }
            }

            function del(id) {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 48, id: id}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_descuentos.php';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }

            function seleccionar(p, obj) {
                if (p == 0) {//Horizontal
                    if ($(obj).attr('checked')) {
                        $('#tx' + obj.id).val();
                        $("input:checkbox[x=" + obj.id + "]").attr('checked', true);
                        $("input:text[x=t" + obj.id + "]").val($('#tx' + obj.id).val());
                    } else {
                        $("input:checkbox[x=" + obj.id + "]").attr('checked', false);
                        $("input:text[x=t" + obj.id + "]").val(0);
                    }
                } else { //Vertical
                    if ($(obj).attr('checked')) {
                        $("input:checkbox[y=" + obj.id + "]").attr('checked', true);
                        $("input:text[y=t" + obj.id + "]").val($('#tx' + obj.id).val());
                    } else {
                        $("input:checkbox[y=" + obj.id + "]").attr('checked', false);
                        $("input:text[y=t" + obj.id + "]").val(0);
                    }
                }
            }

            function guardar(pag, fami, loc, cd) {
                apli_boton.disabled = true;
                x = 0;
                j = 1;
                var data = Array();
                var fields = Array();
                $('.pre_id').each(function () {
                    x++;
                    var proid = this.id;
                    var tbl = $(this).attr('name');
                    var preid = this.value;
                    var data1 = Array();

                    $("input:text[x=tx" + x + "]").each(function () {
                        var vl = this.value;
                        var nm = $(this).attr('name');
                        var cod = $('#codigo' + x).html();
                        data1.push(preid + '&' + nm + '&' + vl + '&' + proid + '&' + tbl);
                        fields.push('codigo=' + cod + ',' + 'local=' + nm + ',' + 'descuento=' + vl + ',' + cod + '');
                    });
                    data.push(data1);
                });
                fields.push('');

                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_descuentos.php', //cambiar actions_productos
                    data: {op: 3, 'data[]': data, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('visible');
                            if (cd == 1) {
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_descuentos.php?pagina=' + pag + '&familia=' + fami + '&locales=' + loc;
                            } else {
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_descuentos.php?pagina=' + pag + '&familia=' + fami + '&txt=' + cd;
                            }
                        } else {
                            alert(dt);
                            loading('hidden');
                        }

                    }
                })
            }

            function valida() {
                if (txt.value.length < 6) {
                    alert('Debe poner almenos 6 caracteres');
                    window.location = '../Scripts/Lista_descuentos.php';
                    return false;
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function mensaje() {
                if (bodega.value == 'x' && txt.value == '') {
                    alert('Debe seleccionar una Familia');
                    loading('hidden');
                    return false;
                }
            }

        </script> 
        <style>
            #mn189{
                background:black;
                color:white;
                border: solid 1px white;
            }
            thead tr th{

            }
            *{
                font-size:10px !important; 
            }
            .aplicarh{
                background-color: #BDE5F8;
                border-bottom:solid 2px #ccc !important; 
                border-right:solid 1px #ccc !important;  
            }
            .aplicarv{
                background-color: #BDE5F8;
                border-right:solid 2px #ccc !important; 
                border-bottom:solid 1px #ccc !important;  
            }
            .ttl{
                color: #D8000C;
                background-color: #FFBABA;
                text-align:center;
                font-weight:bolder; 
            }
            input[type=text]{
                text-transform:uppercase; 
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>
        <img id="charging" src="../img/load_bar.gif" />
        <div id="cargando" align="center">Por Favor Espere...</div>

        <script>
            loading('visible');
        </script>        

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
                <center class="cont_title" >Descuentos</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return mensaje()" >
                        Producto:<input type="text" id="txt" name="txt" size="35" placeholder="Codigo/Descripcion"  onblur="valida()"/>
                        Familias:<select id="bodega" name="bodega">
                            <option value="x">SELECCIONE</option>
                            <?php
                            $cns_Tipo = $Desc->lista_by_tipo_productos_comerciales();
                            while ($rst = pg_fetch_array($cns_Tipo)) {
                                if ($_GET[bodega] == $rst[ids]) {
                                    $selected = 'selected';
                                } else {
                                    $selected = '';
                                }
                                echo "<option $selected value=$rst[ids]>$rst[protipo]</option>";
                            }
                            ?>
                            <option value="0">Industrial</option>
                        </select>
                        Bodegas:<select id="locales" name="locales">
                            <option value="0">TODOS</option>
                            <?php
                            $cns_locales = $Desc->lista_locales();
                            while ($rst_locales = pg_fetch_array($cns_locales)) {
                                echo "<option value='$rst_locales[cod_punto_emision]' />$rst_locales[nombre_comercial]</option>";
                            }
                            ?>
                        </select>
                        <button class="btn" title="Buscar" name="search" onclick="loading('visible')">Buscar</button>
                    </form>  
                </center>
            </caption>
            <tr>
                <?php
                if ($coddet == '') {
                    ?>
                    <td class="ttl" style="text-align:right " ><input type="button" value="Aplicar" id="apli_boton" onclick="guardar('<?php echo $pagina ?>', '<?php echo $familia ?>', '<?php echo $locales ?>', 1)" /></td>
                    <?php
                } else {
                    ?>
                    <td class="ttl" style="text-align:right " ><input type="button" value="Aplicar" id="apli_boton" onclick="guardar('<?php echo $pagina ?>', '<?php echo $familia ?>', 1, '<?php echo $coddet ?>')" /></td>
                    <?php
                }
                ?>
                <td class='aplicarh'></td>
                <td class='aplicarh'></td>
                <td class='aplicarh'></td>
                <?php
                if ($locales == 0) {
                    $cns_head = $Desc->lista_locales();
                    $row1 = pg_num_rows($cns_head);
                    $n = 0;
                    while ($n < $row1) {
                        $n++;
                        echo "<td align='center' class='aplicarh' >
                            <input type='text' id='txy$n' name='' size='3' maxlenght='3' value='0' />
                            <input type='checkbox' id='y$n' name='' onclick='seleccionar(1,this)'  />
                            </td>";
                    }
                } else if ($locales != 0) {
                    $cns_head = $Desc->lista_un_local($locales);
                    if ($locales == 1) {
                        $n = 1;
                    } else if ($locales == 10) {
                        $n = 2;
                    } else if ($locales == 14) {
                        $n = 3;
                    } else if ($locales == 13) {
                        $n = 4;
                    } else if ($locales == 2) {
                        $n = 5;
                    } else if ($locales == 9) {
                        $n = 6;
                    } else if ($locales == 3) {
                        $n = 7;
                    } else if ($locales == 12) {
                        $n = 8;
                    } else if ($locales == 11) {
                        $n = 9;
                    } else if ($locales == 8) {
                        $n = 10;
                    } else if ($locales == 4) {
                        $n = 11;
                    } else if ($locales == 6) {
                        $n = 12;
                    } else if ($locales == 7) {
                        $n = 13;
                    } else if ($locales == 5) {
                        $n = 14;
                    }

                    echo "<td align='center' class='aplicarh' >
                            <input type='text' id='txy$n' name='' size='3' maxlenght='3' value='0' />
                            <input type='checkbox' id='y$n' name='' onclick='seleccionar(1,this)'  />
                            </td>";
                }
                ?>
            </tr>
            <thead>                                                        
                <tr>
                    <th></th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Precio</th>
                    <?php
                    while ($rst_head = pg_fetch_array($cns_head)) {
                        echo "<th align='center'>$rst_head[nombre_comercial] 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>";
                    }
                    ?>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $num_total_registros = pg_num_rows($cns);
//Si hay registros
                if ($num_total_registros > 0) {
                    //Limito la busqueda
                    $TAMANO_PAGINA = 200;

                    if (!$pagina) {
                        $inicio = 0;
                        $pagina = 1;
                    } else {
                        $inicio = ($pagina - 1) * $TAMANO_PAGINA;
                    }
                    //calculo el total de paginas
                    $total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

                    $cns_cant = $Desc->lista_reporte_descuentos_productos_cantidad($txt, $bod, $TAMANO_PAGINA, $inicio);
                    $x = 0;
                    while ($rst = pg_fetch_array($cns_cant)) {
                        $x++;
                        $rst[precios] = number_format($rst[precios], 2);
                        if (empty($rst[precio])) {
                            $rst[precio] = 0;
                        }
                        if (!empty($rst[lote])) {
                            $lote = ' - ' . $rst[lote];
                        }

                        if ($rst[loc1] == '') {
                            $descu1 = 0;
                        } else {
                            $descu1 = $rst[loc1];
                        }

                        if ($rst[loc2] == '') {
                            $descu2 = 0;
                        } else {
                            $descu2 = $rst[loc2];
                        }

                        if ($rst[loc3] == '') {
                            $descu3 = 0;
                        } else {
                            $descu3 = $rst[loc3];
                        }

                        if ($rst[loc4] == '') {
                            $descu4 = 0;
                        } else {
                            $descu4 = $rst[loc4];
                        }

                        if ($rst[loc5] == '') {
                            $descu5 = 0;
                        } else {
                            $descu5 = $rst[loc5];
                        }

                        if ($rst[loc6] == '') {
                            $descu6 = 0;
                        } else {
                            $descu6 = $rst[loc6];
                        }

                        if ($rst[loc7] == '') {
                            $descu7 = 0;
                        } else {
                            $descu7 = $rst[loc7];
                        }

                        if ($rst[loc8] == '') {
                            $descu8 = 0;
                        } else {
                            $descu8 = $rst[loc8];
                        }

                        if ($rst[loc9] == '') {
                            $descu9 = 0;
                        } else {
                            $descu9 = $rst[loc9];
                        }

                        if ($rst[loc10] == '') {
                            $descu10 = 0;
                        } else {
                            $descu10 = $rst[loc10];
                        }

                        if ($rst[loc11] == '') {
                            $descu11 = 0;
                        } else {
                            $descu11 = $rst[loc11];
                        }

                        if ($rst[loc12] == '') {
                            $descu12 = 0;
                        } else {
                            $descu12 = $rst[loc12];
                        }

                        if ($rst[loc13] == '') {
                            $descu13 = 0;
                        } else {
                            $descu13 = $rst[loc13];
                        }

                        if ($rst[loc14] == '') {
                            $descu14 = 0;
                        } else {
                            $descu14 = $rst[loc14];
                        }

                        echo"<tr>
                        <td class='aplicarv' align='center'>$x
                            <input type='text' size='4' id='txx$x' value='0' />
                            <input type='checkbox' id='x$x' onclick='seleccionar(0, this)'/>
                            <input type='hidden' id='$rst[id]' name='$rst[tbl]' value='$rst[precio]' size='5' class='pre_id' >
                        </td>
                        <td id='codigo$x'>$rst[codigo] $lote</td>
                        <td>$rst[descr]</td>                     
                        <td align='right'>$rst[precios]</td>";
                        if ($locales == 0) {
                            echo"<td align='center'>
                            <input type='text' id='' name='1' size='3' x='tx$x' y='ty1'  value='$descu1' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y1' />                                    
                        </td>
                        <td align='center'>
                            <input type='text' id='' name='2' size='3' x='tx$x' y='ty2'  value='$descu2' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y2' />                                    
                        </td>
                        <td align='center'>
                            <input type='text' id='' name='3' size='3' x='tx$x' y='ty3'  value='$descu3' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y3' />                                    
                         </td>
                        <td align='center'>
                            <input type='text' id='' name='4' size='3' x='tx$x' y='ty4'  value='$descu4' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y4' />                                    
                        </td>
                        <td align='center'>
                            <input type='text' id='' name='5' size='3' x='tx$x' y='ty5'  value='$descu5' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y5' />                                    
                        </td>
                        <td align='center'>
                            <input type='text' id='' name='6' size='3' x='tx$x' y='ty6'  value='$descu6' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y6' />                                    
                        </td>
                        <td align='center'>
                            <input type='text' id='' name='7' size='3' x='tx$x' y='ty7'  value='$descu7' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y7' />                                    
                        </td>
                        <td align='center'>
                            <input type='text' id='' name='8' size='3' x='tx$x' y='ty8'  value='$descu8' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y8' />                                    
                        </td>
                            <td align='center'>
                            <input type='text' id='' name='9' size='3' x='tx$x' y='ty9'  value='$descu9' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y9' />                                    
                        </td>
                        <td align='center'>
                            <input type='text' id='' name='10' size='3' x='tx$x' y='ty10'  value='$descu10' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y10' />                                    
                        </td>
                        <td align='center'>
                            <input type='text' id='' name='11' size='3' x='tx$x' y='ty11'  value='$descu11' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y11' />                                    
                        </td>
                        <td align='center'>
                            <input type='text' id='' name='12' size='3' x='tx$x' y='ty12'  value='$descu12' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y12' />                                    
                        </td>
                        <td align='center'>
                            <input type='text' id='' name='13' size='3' x='tx$x' y='ty13'  value='$descu13' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y13' />                                    
                        </td>
                        <td align='center'>
                            <input type='text' id='' name='14' size='3' x='tx$x' y='ty14'  value='$descu14' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y14' />                                    
                        </td>
                    </tr>";
                        } else if ($locales == 1) {
                            echo"<td align='center'>
                            <input type='text' id='' name='1' size='3' x='tx$x' y='ty1'  value='$descu1' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y1' />                                    
                        </td>";
                        } else if ($locales == 10) {
                            echo"<td align='center'>
                            <input type='text' id='' name='2' size='3' x='tx$x' y='ty2'  value='$descu2' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y2' />                                    
                        </td>";
                        } else if ($locales == 14) {
                            echo"<td align='center'>
                            <input type='text' id='' name='3' size='3' x='tx$x' y='ty3'  value='$descu3' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y3' />                                    
                        </td>";
                        } else if ($locales == 13) {
                            echo"<td align='center'>
                            <input type='text' id='' name='4' size='3' x='tx$x' y='ty4'  value='$descu4' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y4' />                                    
                        </td>";
                        } else if ($locales == 2) {
                            echo"<td align='center'>
                            <input type='text' id='' name='5' size='3' x='tx$x' y='ty5'  value='$descu5' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y5' />                                    
                        </td>";
                        } else if ($locales == 9) {
                            echo"<td align='center'>
                            <input type='text' id='' name='6' size='3' x='tx$x' y='ty6'  value='$descu6' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y6' />                                    
                        </td>";
                        } else if ($locales == 3) {
                            echo"<td align='center'>
                            <input type='text' id='' name='7' size='3' x='tx$x' y='ty7'  value='$descu7' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y7' />                                    
                        </td>";
                        } else if ($locales == 12) {
                            echo"<td align='center'>
                            <input type='text' id='' name='8' size='3' x='tx$x' y='ty8'  value='$descu8' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y8' />                                    
                        </td>";
                        } else if ($locales == 11) {
                            echo"<td align='center'>
                            <input type='text' id='' name='9' size='3' x='tx$x' y='ty9'  value='$descu9' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y9' />                                    
                        </td>";
                        } else if ($locales == 8) {
                            echo"<td align='center'>
                            <input type='text' id='' name='10' size='3' x='tx$x' y='ty10'  value='$descu10' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y10' />                                    
                        </td>";
                        } else if ($locales == 4) {
                            echo"<td align='center'>
                            <input type='text' id='' name='11' size='3' x='tx$x' y='ty11'  value='$descu11' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y11' />                                    
                        </td>";
                        } else if ($locales == 6) {
                            echo"<td align='center'>
                            <input type='text' id='' name='12' size='3' x='tx$x' y='ty12'  value='$descu12' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y12' />                                    
                        </td>";
                        } else if ($locales == 7) {
                            echo"<td align='center'>
                            <input type='text' id='' name='13' size='3' x='tx$x' y='ty13'  value='$descu13' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y13' />                                    
                        </td>";
                        } else if ($locales == 5) {
                            echo"<td align='center'>
                            <input type='text' id='' name='14' size='3' x='tx$x' y='ty14'  value='$descu14' />
                            <input type='checkbox'  id='ab$x' name='$rst[precio]' x='x$x' y='y14' />                                    
                        </td>";
                        }
                    }

                    if ($coddet == '') {
                        echo"<tr>
                        <td colspan='18' align='center'>
                        <p>";
                        if ($total_paginas > 1) {
                            if ($pagina != 1) {
                                echo"<input type='button' value=' << ' onclick='auxWindow(0, $pagina - 1, $familia, $locales, 1)'>";
                            }
                            for ($i = 1; $i <= $total_paginas; $i++) {
                                if ($pagina == $i) {
                                    echo "<input type='button' value='$pagina' style='background:#1E90FF;' >";
                                } else {
                                    echo "<input type='button' onclick='auxWindow(0, $i, $familia, $locales, 1)' value='$i'>";
                                }
                            }
                            if ($pagina != $total_paginas) {
                                echo "<input type='button' value=' >> ' onclick='auxWindow(0, $pagina + 1, $familia, $locales, 1)'>";
                            }
                        }
                        echo "</p> 
                        </td>
                    </tr>";
                    } else {
                        echo"<tr>
                        <td colspan='18' align='center'>
                        <p>";
                        if ($total_paginas > 1) {
                            if ($pagina != 1) {
                                echo"<input type='button' value=' << ' onclick='auxWindow(0, $pagina - 1, 1, 1, $coddet)'>";
                            }
                            for ($i = 1; $i <= $total_paginas; $i++) {
                                if ($pagina == $i) {
                                    echo "<input type='button' value='$pagina' style='background:#1E90FF;' >";
                                } else {
                                    echo "<input type='button' onclick='auxWindow(0, $i, 1, 1, $coddet)' value='$i'>";
                                }
                            }
                            if ($pagina != $total_paginas) {
                                echo "<input type='button' value=' >> ' onclick='auxWindow(0, $pagina + 1, 1, 1, $coddet)'>";
                            }
                        }
                        echo "</p> 
                        </td>
                    </tr>";
                    }
                }
                ?>
            </tbody>
        </table>            
    </body>    
</html>

