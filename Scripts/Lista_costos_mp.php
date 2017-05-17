<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_preciosmp.php';
$Clase_preciosmp = new Clase_preciosmp();
$cns_comb = $Clase_preciosmp->lista_un_mp_mod1();
if (isset($_GET[txt], $_GET[ids])) {
    $txt = trim(strtoupper($_GET[txt]));
    $ids = $_GET[ids];
    $dt = explode('_', $ids);
    $tbl = $dt[0];
    $tip = $dt[1];
    $desde = '1900-01-01';
    $hasta = $_GET[hasta];

    if (!empty($txt)) {
        $texto1 = "where (p.mp_codigo like '%$txt%' or p.mp_referencia like '%$txt%')";
        $texto2 = "where (p.pro_codigo like '%$txt%' or p.pro_descripcion like '%$txt%')";
        $cns = $Clase_preciosmp->lista_buscador_todo($texto1,$texto2);
    } else if ($dt[1]!='' && $txt == '') {
        if ($dt[0] == '0') {
            $texto = "where p.mpt_id=$dt[1]";
            $cns = $Clase_preciosmp->lista_buscador_mp($texto);
        } else {
            $texto = "where p.pro_tipo=$dt[1]";
            $cns = $Clase_preciosmp->lista_buscador_productos($texto);
        }
    }
} else {
    $hasta = date("Y-m-d");
    $desde = '1900-01-01';
}
$dec = 2;
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
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
            });

            function save(id, c, t, cod) {

                prec = $('#pre_precio' + c).val();
                fec = $('#cmp_fecha' + c).val();

                var data = Array(
                        prec,
                        fec,
                        t
                        );

                var fields = Array(
                        'codigo=' + cod,
                        'costo=' + prec,
                        'fecha=' + fec,
                        cod +
                        ''
                        );
                $.ajax({
                    beforeSend: function () {
                        if ($("#pre_precio" + c).val().length == 0 ||parseFloat($("#pre_precio" + c).val())==0) {
                            $("#pre_precio" + c).css({borderColor: "red"});
                            $("#pre_precio" + c).focus();
                            return false;
                        }

                        if ($("#cmp_fecha" + c).val().length == 0) {
                            $("#cmp_fecha" + c).css({borderColor: "red"});
                            $("#cmp_fecha" + c).focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_preciosmp.php',
                    data: {op: 0, 'data[]': data, id: id, t: t, 'fields[]': fields,mod:0}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            $('#pre_precio' + c).val(parseFloat(dat[1]).toFixed(dec));
                            $('#cmp_fecha' + c).val(dat[2]);
                            $('#pre_precio' + c).attr('disabled', true);
                            $('#cmp_fecha' + c).attr('disabled', true);
                            $('#im-fecha' + c).attr('hidden', true);
                            $("#pre_precio" + c).css({borderColor:""});
                        } else {
                            alert(dt);
                        }
                    }
                });
            }
            function habilita(c) {
                $('#pre_precio' + c).attr('disabled', false);
                $('#cmp_fecha' + c).attr('disabled', false);
                $('#im-fecha' + c).attr('hidden', false);
            }

            
            function valida() {
                if (txt.value.length < 4) {
                    alert('Debe poner almenos 4 caracteres');
                    window.location = '../Scripts/Lista_costos_mp.php';
                    return false;
                }

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
            <tr><td colspan="15" align="center"><?PHP echo 'INGRESO DE COSTOS' ?></td></tr>
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
                <center class="cont_title" >INGRESO DE COSTOS</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>" onblur="valida()"/>
                        TIPO:<select id="ids" name="ids" class="sel">
                            <?php
                            while ($rst_c = pg_fetch_array($cns_comb)) {
                                ?>
                                <option value="0_<?php echo $rst_c[mpt_id] ?>"><?php echo $rst_c[mpt_nombre] ?></option>
                                <?php
                            }
                            ?>
                            <option value="1_0">SEMIELABORADO</option>
                            <option value="1_1">TERMINADO</option>
                        </select>
                        AL:<input type="text"   name="hasta" value="<?php echo $hasta ?>"  id="hasta" size="10" maxlength="10" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')"/>
                        <img src="../img/calendar.png" width="16"   id="im-hasta" />
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
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Costo</th>
                <th>Fecha</th>
                <th>Acciones</th>
                </thead>
                <!------------------------------------->
                <tbody id="tbody">
                    <?PHP
                    $n = 0;
                    while ($rst = pg_fetch_array($cns)) {
                        $n++;
                        $rst_cst = pg_fetch_array($Clase_preciosmp->lista_costos($rst['id'], $desde, $hasta));
                        if (empty($rst_cst[cmp_fecha])) {
                            $rst_cst[cmp_fecha] = date('Y-m-d');
                        }
                        ?>

                        <tr>
                            <td><?php echo $n ?></td>
                            <td id="codigo<?php echo $n ?>"><?php echo $rst['codigo'] ?></td>
                            <td><?php echo $rst['descripcion'] ?></td>
                            <td  align="center"><input type ="text" size="10"  class="precios2" id="<?php echo 'pre_precio' . $n ?>"  value="<?php echo number_format($rst_cst[cmp_valor], $dec) ?>" style="text-align:right" disabled lang="<?php echo $n ?>" /><input type="text" id="pro_id<?php echo $n ?>" value="<?php echo $rst[id] ?>" hidden></td>
                            <td  align="center"><input type ="text" size="10" maxlength="10" id="<?php echo 'cmp_fecha' . $n ?>"  value="<?php echo $rst_cst[cmp_fecha] ?>" style="text-align:right" disabled lang="<?php echo $n ?>" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')"/>
                                <img src="../img/calendar.png" hidden width="16"   id="im-fecha<?php echo $n ?>" /></td>
                            <td align="center">
                                <?php
                                if ($Prt->edition == 0) {
                                    ?>
                                    <img src="../img/save.png"  class="auxBtn" onclick="save('<?php echo $rst[id] ?>', '<?php echo $n ?>', '<?php echo $rst[tbl] ?>', '<?php echo $rst[codigo] ?>')">

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
                        Calendar.setup({inputField: "cmp_fecha<?php echo $n ?>", ifFormat: "%Y-%m-%d", button: "im-fecha<?php echo $n ?>"});
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

