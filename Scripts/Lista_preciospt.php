<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_preciospt.php';
$tbl_set = 'erp_productos_set';
$tbl = substr($tbl_set, 0, -4);
$Clase_preciospt = new Clase_preciospt();
if (isset($_GET[txt], $_GET[tipo], $_GET[ivab])) {
    $txt = trim(strtoupper($_GET[txt]));
    $bod = $_GET[tipo];
    $iva = $_GET[ivab];
    if (!empty($txt)) {
        $bod = '';
        $iva = '';
        if ($txt != '') {
            $com = "pro_b like '%$txt%' or pro_a like '%$txt%'";
            $ind = "pro_descripcion like '%$txt%' or pro_codigo like '%$txt%'";
            $rst_i = pg_num_rows($Clase_preciospt->lista_buscador_i_productos($ind));
            $rst_c = pg_num_rows($Clase_preciospt->lista_buscador_productos($com));
            if ($rst_i != '0') {
                $tabla = 0;
                $cns = $Clase_preciospt->lista_buscador_i_productos($ind);
            }
            if ($rst_c != '0') {
                $tabla = 1;
                $cns = $Clase_preciospt->lista_buscador_productos($com);
            }
            $f = 0;
        }
    } else if ($iva != '') {
        $txt = '';
        $bod = '';
        $r = "pre_iva= '$iva' ORDER BY pro_id";
        $cns = $Clase_preciospt->lista_buscador_precios($r);
        $f = 1;
    } else if ($bod == 0) {
        $txt = '';
        $iva = '';
        $resp = "pro_tabla = $bod ORDER BY pro_id";
        $cns = $Clase_preciospt->lista_buscador_precios($resp);
        $f = 1;
    } else {
        $cns = $Clase_preciospt->lista_table_by_tipo($tbl, $bod);
        $f = 1;
    }
} else {
    $f = 0;
}
$cns_pre = $Clase_preciospt->lista_precios();
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

                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    save(id);
                });
            });

            function save(id, c, t, cod) {
                prec = $('#pre_precio' + c).val();
                iva = $('#pre_iva' + c).val();
                prec2 = $('#pre_preciox' + c).val();
                var data = Array(
                        prec,
                        iva,
                        prec2
                        );
                var fields = Array(
                        'codigo=' + cod,
                        'precio=' + prec,
                        'iva=' + iva,
                        'precio2=' + prec2,
                        cod
                        );
                $.ajax({
                    beforeSend: function () {
                        if ($("#pre_precio" + c).val().length == 0) {
                            $("#pre_precio" + c).css({borderColor: "red"});
                            $("#pre_precio" + c).focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_preciospt.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields, t: t}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            $('#pre_precio' + c).val(parseFloat(dat[1]).toFixed(4));
                            $('#pre_iva' + c).val(dat[2]);
                            $('#pre_preciox' + c).val(parseFloat(dat[3]).toFixed(4));
                            $('#pre_precio' + c).attr('disabled', true);
                            $('#pre_iva' + c).attr('disabled', true);
                            $('#pre_preciox' + c).attr('disabled', true);
                        } else {
                            alert(dt);
                        }
                    }
                });
            }
            function habilita(c) {
                $('#pre_precio' + c).attr('disabled', false);
                $('#pre_preciox' + c).attr('disabled', false);
                $('#pre_iva' + c).attr('disabled', false);
            }

            function actualizar_todo() {
                desc = $('#desc').val();
                $.post("actions_preciospt.php", {op: 2, id: desc}, function (dt) {
                    if (dt == 0) {
                        window.location = '../Scripts/Lista_preciospt.php';
                    } else {
                        alert(dt);
                    }
                });
            }

            function cambiar_precios() {
                tipo = $('#tipo').val();
                if (tipo == '') {
                    tipo = 0;
                } else {
                    tipo = $('#tipo').val();
                }
                if (pre_precios1.checked == true) {
                    if (tipo == 0) {
                        msm = confirm("¿ Esta seguro de seleccionar PRECIO 1 ?");
                        if (msm == true) {
                            pre_precios1 = $('#pre_precios1').val();
                            $.post("actions_preciospt.php", {op: 5, id: pre_precios1}, function (dt) {
                                if (dt == 0) {
                                    window.location = '../Scripts/Lista_preciospt.php'
                                } else {
                                    alert(dt);
                                }
                            });
                        }
                    } else {
                        msm = confirm("¿ Esta seguro de seleccionar PRECIO 1 ?");
                        if (msm == true) {
                            $.post("actions_preciospt.php", {op: 6, id: tipo}, function (dt) {
                                if (dt == 0) {
                                    window.location = '../Scripts/Lista_preciospt.php'
                                } else {
                                    alert(dt);
                                }
                            });
                        }
                    }
                }
                if (pre_precios2.checked == true) {
                    if (tipo == 0) {
                        msm = confirm("¿ Esta seguro de seleccionar PRECIO 2 ?");
                        if (msm == true) {
                            pre_precios2 = $('#pre_precios2').val();
                            $.post("actions_preciospt.php", {op: 7, id: pre_precios2}, function (dt) {
                                if (dt == 0) {
                                    window.location = '../Scripts/Lista_preciospt.php'
                                } else {
                                    alert(dt);
                                }
                            });
                        }
                    } else {
                        msm = confirm("¿ Esta seguro de seleccionar PRECIO 2 ?");
                        if (msm == true) {
                            $.post("actions_preciospt.php", {op: 8, id: tipo}, function (dt) {
                                if (dt == 0) {
                                    window.location = '../Scripts/Lista_preciospt.php'
                                } else {
                                    alert(dt);
                                }
                            });
                        }
                    }
                }

                if (camb.checked == true) {
                    if (tipo == 0) {
                        msm = confirm("¿ Esta seguro de CAMBIAR DE PRECIOS ?");
                        if (msm == true) {

                            $('.precios2').each(function () {
                                var i = this.lang;
                                var id = this.id;
                                if ($('#pre_preciox' + i).val() != 0) {
                                    $.post("actions_preciospt.php", {op: 4, id: id}, function (dt) {
                                        if (dt == 0) {
                                            window.location = '../Scripts/Lista_preciospt.php';
                                        } else {
                                            alert(dt);
                                        }
                                    });
                                }
                            })
                        }
                    } else {
                        msm = confirm("¿ Esta seguro de CAMBIAR DE PRECIOS ?");
                        if (msm == true) {

                            $('.precios2').each(function () {
                                var i = this.lang;
                                var id = this.id;
                                if ($('#pre_preciox' + i).val() != 0) {
                                    var fields = Array(
                                            'codigo=' + $('#codigo' + i).html(),
                                            'precio1=' + $('#pre_preciox' + i).val(),
                                            $('#codigo' + i).html()
                                            );
                                    $.post("actions_preciospt.php", {op: 4, id: id, 'fields[]': fields}, function (dt) {
                                        if (dt == 0) {
                                            window.location = '../Scripts/Lista_preciospt.php';
                                        } else {
                                            alert(dt);
                                        }
                                    });
                                }
                            })
                        }
                    }
                }
            }

            function seleccionar_todo_prec1() {
                n = 0;
                $('.precios1').each(function () {
                    $(this).attr('checked', true);
                    n++;
                })
            }

            function seleccionar_todo_prec2() {
                n = 0;
                $('.precios2').each(function () {
                    $(this).attr('checked', true);
                    n++;
                })
            }

            function auxWindow(a)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/Lista_descuentos.php';//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
//                        look_menu();
                        break;
                    case 1:
//                        alert($('#txt').val() + '&' + $('#bodega').val() + '&' + $('#ivab').val());
                        frm.src = '../Scripts/Lista_precios_excel.php?txt=' + $('#txt').val() + '&bodega=' + $('#bodega').val() + '&ivab=' + $('#ivab').val();//Cambiar Form_productos
//                        parent.document.getElementById('contenedor2').rows = "*,80%";
//                        look_menu();
                        break;
                }
            }
            function descargar_archivo() {
                window.location = '../formatos/descargar_archivo.php?archivo=precios.csv';
            }
            function load_file() {
                $('#frm_file').submit();
            }

            function valida() {
                if (txt.value.length < 6) {
                    alert('Debe poner almenos 6 caracteres');
                    window.location = '../Scripts/Lista_preciospt.php';
                    return false;
                }

            }

            function exportar_excel() {
                $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                $("#tbl2").append($("#tbl tbody").clone()).html();
                $("#tbl2").append($("#tbl tfoot").clone()).html();
                $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                return true;
            }

        </script> 
        <style>
            #mn110{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #head{
                padding: 3px 10px;  
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #63b8ff), color-stop(1, #00529B) );
                background:-moz-linear-gradient( center top, #63b8ff 5%, #00529B 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#63b8ff', endColorstr='#00529B');
                color:#FFFFFF; 
                font-size: 12px; 
                font-weight: bold; 
                border-left: 1px solid #f8f8f8;
                border-collapse: collapse;
                cursor:pointer;
            }
            input[type=text]{
                text-transform: uppercase;                
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
            input[readonly]{
                background:#f8f8f8; 
            }
            input{
                background:#f8f8f8 !important; 
            }
        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <tr><td colspan="15"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></td></tr>
            <tr><td colspan="15" align="center"><?PHP echo 'INGRESO DE PRECIOS' ?></td></tr>
            <tr>
                <td colspan="15"><?php echo 'Fecha: ' . date('Y-m-d') ?></td>
            </tr>
        </table>
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
                <center class="cont_title" >INGRESO DE PRECIOS</center>
                <center class="cont_finder">
<!--                    <form id="exp_excel" style="float:right;margin-top:6px;padding:0px" method="post" action="../Includes/export.php?tipo=3" onsubmit="return exportar_excel()"  >
                        <input type="submit" value="Excel" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>-->
                    <div style="float:right;margin-top:0px;padding:7px;">
                        <!--PRECIO 1-->
                        <input type="radio" id="pre_precios1" name="pre_precio" value="0" onclick="seleccionar_todo_prec1()" hidden/>
                        <!--PRECIO 2-->
                        <input type="radio" id="pre_precios2" name="pre_precio" value="0" onclick="seleccionar_todo_prec2()" hidden/>
                        CAMBIAR PRECIOS:<input type="radio" id="camb" name="pre_precio" value="0" />
                        <button class="btn" title="Guardar" onclick="cambiar_precios()">Guardar</button>
                    </div>
                    <!--<img src="../img/xls.png" width="16px;" style="float:right" class="auxBtn" onclick="auxWindow(1)"  title="Exporta Lista"  />-->
                    <!--<font style="float:right" id="txt_load">  Exportar:</font><img src="../img/xls.png" width="16px;"-->

                    <a href="#" onclick="descargar_archivo()" style="float:right;text-transform:capitalize;margin-left:15px;margin-top:10px;text-decoration:none;color:#ccc; ">Descargar Formato<img src="../img/xls.png" width="16px;" /></a>

                    <form id="frm_file" name="frm_file" style="float:right" action="actions_upload_precios.php" method="POST" enctype="multipart/form-data">
                        <div class="upload">
                            ...<input type="file"  name="archivo" id="archivo" onchange="load_file()" >
                        </div>
                    </form>
                    <font style="float:right" id="txt_load">Cargar Datos:</font>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>" onblur="valida()"/>
                        FAMILIAS:
                        <select style="width:150px" name="tipo" id="tipo">
                            <option value="">Seleccion Tipo</option>
                            <?php
                            $cns_Tipo = $Clase_preciospt->lista_by_tipo_productos_comerciales();
                            while ($rst = pg_fetch_array($cns_Tipo)) {
                                if ($_GET[tipo] == $rst[ids]) {
                                    $selected = 'selected';
                                } else {
                                    $selected = '';
                                }
                                echo "<option $selected value=$rst[ids]>$rst[protipo]</option>";
                            }
                            ?>
                            <option value="0">Industriales</option>
                        </select>
                        IVA:<select id="ivab" name="ivab">
                            <option value="">SELECCIONE</option>
                            <option value="12">12%</option>
                            <option value="0">0%</option>                             
                            <option value="EX">EX</option>                             
                            <option value="NO">NO</option>                             
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form> 
                </center>
            </caption>
        </table>  
        <form  autocomplete="off" id="frm_save" name="frm_save">
            <table id="tbl" style="width:100%">  
                <!--Nombres de la columna de la tabla-->
                <thead id="head">
                <th>No</th>
                <th>Bodega</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Precio 1</th>
                <th>Precio 2</th>
                <th>Iva</th>
                <th>Acciones</th>
                </thead>
                <!------------------------------------->
                <tbody id="tbody">
                    <?PHP
                    $n = 0;
                    while ($rst = pg_fetch_array($cns)) {
                        $n++;
                        if ($rst[id] != '') {
                            $rst = pg_fetch_array($Clase_preciospt->lista_table_by_comercial($rst[id]));
                        }
                        if ($f == 0) {
                            if ($tabla == 0) {
                                $id = $rst[pro_id];
                                $rst['pro_tabla'] = 'INDUSTRIAL';
                            } else {
                                $id = $rst[id];
                                $rst['pro_codigo'] = $rst[pro_a] . ' ' . $rst['pro_ac'];
                                $rst['pro_descripcion'] = $rst[pro_b];
                                $rst['pro_tabla'] = 'COMERCIAL';
                            }
                            $rst1 = pg_fetch_array($Clase_preciospt->lista_precios_proid_tabla($id, $tabla));
                            $rst[pre_id] = $rst1[pre_id];
                            $rst['pre_precio'] = $rst1['pre_precio'];
                            $rst['pre_precio2'] = $rst1['pre_precio2'];
                            $rst[pre_iva] = $rst1[pre_iva];
                            $rst['pre_vald_precio1'] = $rst1[pre_vald_precio1];
                        } else {
                            if ($rst['pro_tabla'] == '0') {
                                $rst['pro_tabla'] = 'INDUSTRIAL';
                                $rst2 = pg_fetch_array($Clase_preciospt->lista_i_productos($rst[pro_id]));
                                $rst['pro_codigo'] = $rst2['pro_codigo'];
                                $rst['pro_descripcion'] = $rst2['pro_descripcion'];
                                $tabla = 0;
                            } else if ($rst['pro_tabla'] == '1') {
                                $rst['pro_tabla'] = 'COMERCIAL';
                                $rst[pre_id] = $rst[pre_id];
                                $rst['pre_vald_precio1'] = $rst[pre_vald_precio1];
                                $rst2 = pg_fetch_array($Clase_preciospt->lista_table_by_comercial($rst[pro_id]));
                                $rst['pro_codigo'] = $rst2['pro_a'] . ' ' . $rst2['pro_ac'];
                                $rst['pro_descripcion'] = $rst2['pro_b'];
                                $rst['pre_precio'] = $rst2['pre_precio'];
                                $rst['pre_precio2'] = $rst2['pre_precio2'];
                                $rst['pre_iva'] = $rst2['pre_iva'];
                                $tabla = 1;
                            }
                        }
                        if ($rst[pre_id] != '') {
                            ?>
                            <tr>
                                <td><?php echo $n ?></td>
                                <td><?php echo $rst['pro_tabla'] ?></td>
                                <td id="codigo<?php echo $n ?>"><?php echo $rst['pro_codigo'] ?></td>
                                <td><?php echo $rst['pro_descripcion'] ?></td>
                                <td  align="center"><input type ="text" size="10"  id="<?php echo 'pre_precio' . $n ?>"  value="<?php echo number_format($rst['pre_precio'], 4) ?>" style="text-align:right" disabled /><input type="radio" class="precios1" id="<?php echo 'pre_vald_precio1' . $n ?>" name="<?php echo 'pre_precios' . $n ?>" value="" hidden></td>
                                <td  align="center"><input type ="text" size="10"  id="<?php echo 'pre_preciox' . $n ?>"  value="<?php echo number_format($rst['pre_precio2'], 4) ?>" style="text-align:right" disabled /><input type="radio" class="precios2" id="<?php echo $rst[pre_id] ?>" name="<?php echo 'pre_precios' . $n ?>" value="" lang="<?php echo $n ?>" hidden></td>
                                <td align="center"><select id="<?php echo 'pre_iva' . $n ?>" value="<?php echo $rst['pre_iva'] ?>" disabled >
                                        <option value="12">12%</option>
                                        <option value="0">0%</option>                            
                                        <option value="EX">EX</option>                            
                                        <option value="NO">NO</option>                            
                                    </select>
                                    <script>
                                        idt = '<?php echo 'pre_iva' . $n ?>';
                                        pr1_id = '<?php echo 'pre_vald_precio1' . $n ?>';
                                        pr2_id = '<?php echo $rst[pre_id] ?>';
                                        pr1 = '<?php echo $rst['pre_vald_precio1'] ?>';
                                        $('#' + idt).val('<?php echo $rst[pre_iva] ?>');

                                        if (pr1 == 1) {
                                            $('#' + pr1_id).attr('checked', true);
                                        } else {
                                            $('#' + pr2_id).attr('checked', true);
                                        }

                                    </script>
                                </td>
                                <td align="center">
                                    <?php
                                    if ($Prt->edition == 0) {
                                        ?>
                                        <img src="../img/save.png"  class="auxBtn" onclick="save(<?php echo $rst[pre_id] ?>,<?php echo $n ?>,<?php echo $tabla ?>, '<?php echo $rst[pro_codigo] ?>')">

                                        <?php
                                    }
                                    if ($Prt->edition == 0) {
                                        ?>
                                        <img src="../img/upd.png" width="16px"  class="auxBtn" onclick="habilita(<?php echo $n ?>)"
                                             <?php
                                         }
                                         ?>
                                </td>         
                            </tr>  

                            <?PHP
                        }
                    }
                    ?>
                </tbody>
            </table>   
        </form>
    </body>    
</html>
<script>
    var bod = '<?php echo $bod ?>';
    $('#bodega').val(bod)
    var iva = '<?php echo $iva ?>';
    $('#ivab').val(iva)
</script>

