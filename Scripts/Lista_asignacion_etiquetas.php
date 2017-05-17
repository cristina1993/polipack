<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_etiquetas.php';
$set = new Clase_etiquetas();
if (isset($_GET[txt])) {
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($txt)) {
        $text = "and (upper(pr.proc_descripcion) like '%$txt%' or upper(ol.opl_modulo) like '%$txt%' or upper(md.mod_descripcion) like '%$txt%')";
        $cns = $set->lista_opciones($text);
    }else{
        $cns = $set->lista_opciones();
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            var dec = '<?php echo $dec ?>';
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

            function save(id, c, op) {
                mod = $('#modulo' + c).html();
                et = $('#etiqueta' + c).val();

                var data = Array(
                        op,
                        et
                        );

                var fields = Array(
                        'modulo=' + mod,
                        'etiqueta=' + et,
                        mod +
                        ''
                        );
                $.ajax({
                    beforeSend: function () {
                        if ($("#etiqueta" + c).val() == "") {
                            $("#etiqueta" + c).css({borderColor: "red"});
                            $("#etiqueta" + c).focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_etiquetas.php',
                    data: {op: 3, 'data[]': data, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            $('#etiqueta' + c).val(dat[1]);
                            $('#etiqueta' + c).attr('disabled', true);
                            $("#etiqueta" + c).css({borderColor: ""});
                        } else {
                            alert(dt);
                        }
                    }
                });
            }
            function habilita(c) {
                $('#etiqueta' + c).attr('disabled', false);
            }

            function load_etiqueta(obj) {
                n = obj.lang;
                $.post("actions_etiquetas.php", {op: 2, id: obj.value},
                function (dt) {
                    dat = dt.split('&&');
                    $('#tamaño' + n).html(dat[0]);
                    $('#elementos' + n).html(dat[1]);
                })

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

            input[readonly]{
                background:#f8f8f8; 
            }
            input{
                background:#f8f8f8 !important; 
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
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >ASIGNACION DE ETIQUETAS</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>" onblur="valida()"/>
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
                <th>Direccion</th>
                <th>Nombre</th>
                <th>Etiqueta</th>
                <th>Tamaño</th>
                <th>Elementos</th>
                <th>Acciones</th>
                </thead>
                <!------------------------------------->
                <tbody id="tbody">
                    <?PHP
                    $n = 0;
                    while ($rst = pg_fetch_array($cns)) {
                        $n++;
                        $rst_asig = pg_fetch_array($set->lista_una_asignacion($rst[opl_id]));
                        $dt = explode('&', $rst_asig[eti_elementos]);
                        $elementos = $dt[0] + $dt[1] + $dt[2] + $dt[3] + $dt[4] + $dt[5] + $dt[6];
                        ?>
                        <tr>
                            <td><?php echo $n ?></td>
                            <td id="codigo<?php echo $n ?>" ><?php echo $rst['proc_descripcion'] . '   /  ' . $rst['mod_descripcion'] ?></td>
                            <td id="modulo<?php echo $n ?>"><?php echo $rst['opl_modulo'] ?></td>
                            <td  align="center">
                                <select id="etiqueta<?php echo $n ?>" disabled onchange="load_etiqueta(this)" lang="<?php echo $n ?>">
                                    <option value="">seleccione</option>
                                    <?php
                                    $cns_eti = $set->lista_etiquetas();
                                    while ($rst_eti = pg_fetch_array($cns_eti)) {
                                        echo "<option value='$rst_eti[eti_id]'>$rst_eti[eti_descripcion]</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td  id="tamaño<?php echo $n ?>" align="center"><?php echo $rst_asig[eti_tamano] ?></td>
                            <td  id="elementos<?php echo $n ?>" align="center"><?php echo $elementos ?></td>
                            <td align="center">
                                <?php
                                if ($Prt->edition == 0) {
                                    ?>
                                    <img src="../img/save.png"  class="auxBtn" onclick="save('<?php echo $rst_asig[ase_id] ?>', '<?php echo $n ?>', '<?php echo $rst[opl_id] ?>')">

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
                    <script>
                        var et = '<?php echo $rst_asig[eti_id] ?>';
                        var n = '<?php echo $n ?>';
                        $('#etiqueta' + n).val(et);
                    </script>
                    <?PHP
                }
                ?>
                </tbody>
            </table>   
        </form>
    </body>    
</html>
<script>
    var t = '<?php echo $ids ?>';
    $('#ids').val(t);
</script>

