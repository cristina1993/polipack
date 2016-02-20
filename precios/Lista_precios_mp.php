<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_preciosmp.php';
$Clase_preciosmp = new Clase_preciosmp();
$Set = new Set();
$mod = $_GET['mod'];
$rst_ti = pg_fetch_array($Set->lista_titulo($mod));
$des = strtoupper($rst_ti[mod_descripcion]);
$r_tip = pg_fetch_array($Set->lista_un_mp_mod($des));
if (isset($_GET[txt], $_GET[ivab])) {
    $txt = trim(strtoupper($_GET[txt]));
    $iva = $_GET[ivab];
    if (!empty($txt)) {
        $texto = "(mp_a like '%$txt%' or mp_b like '%$txt%') and ids=$r_tip[ids]";
    } else if ($iva != '') {
        $texto = "mp_f= '$iva'";
    }
    $cns = $Clase_preciosmp->lista_buscador_precios($texto);
} else {
    $cns = $Clase_preciosmp->lista_precios($r_tip[ids]);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            var ids = '<?php echo $r_tip[ids] ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
            });
            function save(id, c, t) {
                prec = $('#mp_c' + c).val();
                prec2 = $('#mp_d' + c).val();
                desc = $('#mp_e' + c).val();
                iva = $('#mp_f' + c).val();
                var data = Array(
                        prec,
                        prec2,
                        desc,
                        iva
                        );
                fields = $('#frm_save').serialize();
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if ($("#mp_c" + c).val().length == 0) {
                            $("#mp_c" + c).css({borderColor: "red"});
                            $("#mp_c" + c).focus();
                            return false;
                        }
                        else if ($("#mp_d" + c).val().length == 0) {
                            $("#mp_d" + c).css({borderColor: "red"});
                            $("#mp_d" + c).focus();
                            return false;
                        }
                        else if ($("#mp_e" + c).val().length == 0) {
                            $("#mp_e" + c).css({borderColor: "red"});
                            $("#mp_e" + c).focus();
                            return false;
                        }
                        else if ($("#mp_f" + c).val().length == 0) {
                            $("#mp_f" + c).css({borderColor: "red"});
                            $("#mp_f" + c).focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_preciosmp.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields, t: t}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            $('#mp_c' + c).val(dat[1]);
                            $('#mp_d' + c).val(dat[2]);
                            $('#mp_e' + c).val(dat[3]);
                            $('#mp_f' + c).val(dat[4]);
                            $('#mp_c' + c).attr('disabled', true);
                            $('#mp_d' + c).attr('disabled', true);
                            $('#mp_e' + c).attr('disabled', true);
                            $('#mp_f' + c).attr('disabled', true);
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                });
            }
            function habilita(c) {
                $('#mp_c' + c).attr('disabled', false);
                $('#mp_d' + c).attr('disabled', false);
                $('#mp_e' + c).attr('disabled', false);
                $('#mp_f' + c).attr('disabled', false);
            }


            function cambiar_precios() {

                if (confirm('Esta seguro de Aplicar los cambios?') == true) {
                    $('.precios2').each(function () {
                        var i = this.lang;
                        var id = this.id;
                        if ($(this).attr('checked') == true) {
                            if ($('#mp_d' + i).val() != 0) {
                                $.post("actions_preciosmp.php", {op: 1, id: id}, function (dt) {

                                });
                            }
                        }
                    })

                }
//                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_precios_mp.php?mod=' + <?php echo $mod_id ?>;

            }

            function seleccionar_todo_prec2(obj) {
                n = 0;
                if ($(obj).attr('checked') == true) {
                    $('.precios2').each(function () {
                        $(this).attr('checked', true);
                        n++;
                    })
                } else {
                    $('.precios2').each(function () {
                        $(this).attr('checked', false);
                        n++;
                    })
                }
            }

            function actualizar_todo() {
                desc = $('#desc').val();
                $.post("actions_preciosmp.php", {op: 2, tab: desc, id: ids}, function (dt) {
                    if (dt == 0) {
                        window.location = '../Scripts/Lista_precios_mp.php?mod=' + <?php echo $mod_id ?>;
                        ;
                    } else {
                        alert(dt);
                    }
                });
            }

            function auxWindow(a)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/Lista_descuentos.php'; //Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
//                        look_menu();
                        break;
                }
            }

            function descargar_archivo() {
                window.location = '../formatos/descargar_archivo.php?archivo=precios.csv';
            }
            function load_file() {
                var formData = new FormData($('#frm_file')[0]);
                $.ajax({
                    type: "POST",
                    url: "actions_upload_precios.php",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (dt) {
                        alert(dt);
                    }
                });
            }
        </script> 
        <style>
            #mn195{
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
            #frmSearch{
                font-size: 10px;
            }
            #frm_file{
                font-size: 10px;
            }
            .sel{
                font-size: 11px;
                width: 60px;
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
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php?mod=" . $mod_id . "&ids=" . $rst_sbm[opl_id] ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>    
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >CONTROL DE PRECIOS <?php echo $des ?></center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Asignar Descuentos" onclick="auxWindow(0)" >Asignar Descuentos</a>-->


                    <a href="#" onclick="descargar_archivo()" style="float:right;text-transform:capitalize;margin-left:15px;margin-top:10px;text-decoration:none;color:#ccc; " title="Descargar Formato">Descargar<img src="../img/xls.png" width="16px;" /></a>
                    <form id="frm_file" name="frm_file" style="float:right">
                        <div class="upload" style="font-size: 10px;">
                            ...<input type="file"  name="file" id="file" onchange="load_file()" size="5" >
                        </div>
                    </form>
                    <font style="float:right; font-size: 10px; " id="txt_load">Cargar Datos:</font>

                    <div style="float:right;margin-top:0px;padding:7px;font-size: 10px;">
                        Descuento:<input type="text"  name="desc" size="10" id="desc" style="font-size: 11px"/>
                        <button class="btn" title="Descuento Todos" onclick="actualizar_todo()" style="font-size: 10px;">Aplicar</button>
                    </div>
                    <div style="float:right;margin-top:0px;padding:7px; ">
                        <button class="btn" title="Aplicar Cambios" onclick="cambiar_precios()" style="font-size: 10px;">prec.2 a prec.1</button>
                    </div>

                    <!--<font style="float:right" id="txt_load">Cargar Datos:</font>-->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="hidden" name="mod" size="15" id="mod" value="<?php echo $mod ?>" />
                        BUSCAR POR:<input type="text" name="txt" size="12" id="txt" style="font-size: 11px" />
                        IVA:<select id="ivab" name="ivab" class="sel">
                            <option value="">SELECCIONE</option>
                            <option value="12">12%</option>
                            <option value="0">0%</option>                             
                            <option value="EX">EX</option>                             
                            <option value="NO">NO</option>                             
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()" style="font-size: 10px;" >Buscar</button>

                    </form> 
                </center>
            </caption>
            <form  autocomplete="off" id="frm_save" name="frm_save">
                <table id="tbl" style="width:100%">  
                    <!--Nombres de la columna de la tabla-->
                    <thead id="head">
                    <th>No</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Precio 1</th>
                    <th>Precio 2 <input type="checkbox" id="todos"  onclick="seleccionar_todo_prec2(this)"></th>
                    <th>Descuento %</th>
                    <th>Iva</th>
                    <th>Acciones</th>
                    </thead>
                    <!------------------------------------->
                    <tbody id="tbody">
                        <?PHP
                        $n = 0;
                        while ($rst = pg_fetch_array($cns)) {
                            $n++;
                            ?>
                            <tr>
                                <td><?php echo $n ?></td>
                                <td><?php echo $rst['mp_a'] ?></td>
                                <td><?php echo $rst['mp_b'] ?></td>
                                <td  align="center"><input type ="text" size="10"  id="<?php echo 'mp_c' . $n ?>"  value="<?php echo number_format($rst['mp_c'], 2) ?>" style="text-align:right" disabled /></td>
                                <td  align="center"><input type ="text" size="10"  id="<?php echo 'mp_d' . $n ?>"  value="<?php echo number_format($rst['mp_d'], 2) ?>" style="text-align:right" disabled /><input type="checkbox" id="<?php echo $rst[id] ?>" lang="<?php echo $n ?>" class="precios2"></td>
                                <td  align="center"><input type ="text" size="10"  id="<?php echo 'mp_e' . $n ?>"  value="<?php echo number_format($rst['mp_e'], 2) ?>" style="text-align:right" disabled /></td>
                                <td align="center"><select id="<?php echo 'mp_f' . $n ?>" value="<?php echo $rst['pre_iva'] ?>" disabled >
                                        <option value="12">12%</option>
                                        <option value="0">0%</option>                            
                                        <option value="EX">EX</option>                            
                                        <option value="NO">NO</option>                            
                                    </select>
                                    <script>
                                        idt = '<?php echo 'mp_f' . $n ?>';
                                        $('#' + idt).val('<?php echo $rst[mp_f] ?>');</script>
                                </td>
                                <td align="center">
                                    <?php
                                    if ($Prt->edition == 0) {
                                        ?>
                                        <img src="../img/save.png"  class="auxBtn" onclick="save(<?php echo $rst[id] ?>,<?php echo $n ?>, 0)">

                                        <?php
                                    }
                                    if ($Prt->edition == 0) {
                                        ?>
                                        <img src="../img/upd.png" width="16px"  class="auxBtn" onclick="habilita(<?php echo $n ?>)">
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
            </form>
    </body>    
</html>


