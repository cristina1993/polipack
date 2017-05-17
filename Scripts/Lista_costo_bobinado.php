<?php
session_start();
set_time_limit(0);
include_once '../Clases/clsClase_costo_bobinado.php';
include_once '../Includes/permisos.php';
$Set = new Clase_costo_bobinado();
if (isset($_GET[search])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $txt = strtoupper($_GET[txt]);
    if (!empty($txt)) {
        $text = "where (opp_codigo like '%$txt%'
                OR pro_descripcion like '%$txt%')";
    } else {
        $text = "where fec_fin BETWEEN '$desde' AND  '$hasta' ";
    }
    $cnsPedido = $Set->Lista_Universal($text);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $txt = '';
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lista de Pedidos</title>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
            });

            function checkKey(key)
            {
                var unicode
                if (key.charCode) {
                    unicode = key.charCode;
                } else {
                    unicode = key.keyCode;
                }
                if (unicode == 13) {
                    reload()
                }
            }
            function valFecha(val, id)
            {
                v = val.split('-');
                if (val.length !== 10 || v[0].length !== 4 || v[1].length !== 2 || v[2].length !== 2)
                {
                    doc = document.getElementById(id);
                    doc.focus();
                    alert('Formato de fecha debe ser (yyyy-mm-dd)');
                    return false;
                }
            }

            function save(id, c, t, f, cod, n, rop) {
                var data = Array(
                        c,
                        f,
                        t,
                        rop
                        );

                var fields = Array(
                        'codigo=' + cod,
                        'costo=' + c,
                        'fecha=' + f,
                        'tabla=' + t,
                        cod +
                        ''
                        );
                $.ajax({
                    beforeSend: function () {
                        if (parseFloat($('#cmp' + n).html()) == 0) {
                            alert('No se puede revisar porque Costo MP es 0');
                            return false;
                        }
                        if (parseFloat($('#cins' + n).html()) == 0) {
                            alert('No se puede revisar porque Costo Insumo es 0');
                            return false;
                        }
                        if (parseFloat($('#otc' + n).html()) == 0) {
                            alert('No se puede revisar porque Otros Costos es 0');
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_preciosmp.php',
                    data: {op: 0, 'data[]': data, id: id, t: t, 'fields[]': fields, mod: 2}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_costo_bobinado.php?search=1&desde=<?php echo $desde ?>&hasta=<?php echo $hasta ?>&txt=<?php echo $txt ?>';
                        } else {
                            alert(dt);
                        }
                    }
                });
            }

        </script>
        <style>

            body{
                margin-top:0px; 
            }
            .sms td{
                color: #3A87AD;
                background-color: #D9EDF7;
                border: solid 1px #BCE8F1;
                text-align:center;
                font-weight:bolder;
                text-transform:capitalize; 
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
                <center class="cont_title" >LISTA COSTO BOBINADO</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        DESDE:<input type="text"  name="desde" value="<?php echo $desde ?>" id="desde" size="10" maxlength="10" onchange="valFecha(this.value, this.id)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')"/>
                        <img src="../img/calendar.png" id="im-desde"/>
                        HASTA:<input type="text"  name="hasta" value="<?php echo $hasta ?>" id="hasta" size="10" maxlength="10" onchange="valFecha(this.value, this.id)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')" />
                        <img src="../img/calendar.png" id="im-hasta"/>
                        ORDEN:
                        <input type="text" placeholder="Codigo" name="txt" value="<?php echo $txt ?>" style="text-transform:uppercase" id="txt" size="15" onkeypress="checkKey(event)" />
                        <input type="submit" name="search" value="Buscar" id="search"  />
                    </form>                            
                </center>
            </caption>
            <thead >
                <tr>
                    <th>No</th>
                    <th>Fecha</th>
                    <th>Orden</th>
                    <th>Producto</th>
                    <th>Unid</th> 
                    <th>Prod. Und</th>
                    <th>Prod. Kg</th>
                    <th>Costo MP</th>
                    <th>Costo Insumos</th>
                    <th>Otros Costos</th>
                    <th>Costo Unit</th>
                    <th>Costo Total</th>
                    <th>Estado</th>
                </tr>
                <?php
                if (pg_num_rows($cnsPedido) == 0) {
                    ?>
                    <tr class="sms" ><td colspan="26">NO EXISTEN DATOS EN ESTA CONSULTA</td></tr>
                    <?php
                }
                ?>                
            </thead>

            <tbody id="tblbody">
                <?php
                while ($rstPedido = pg_fetch_array($cnsPedido)) {
                    $n++;
                    $ref = explode(' ', $rstPedido['pro_descripcion']);
                    $ref0 = $ref[0] . ' ' . $ref[1];
                    if (count_chars($ref0) <= 8) {
                        $ref0 = $ref[0] . ' ' . $ref[1];
                    } elseif (count_chars($ref[0]) > 8) {
                        $ref0 = substr($ref[0], 0, 8);
                    } else {
                        $ref0 = $ref[0];
                    }
                    $df = explode('-', $rstPedido['fec_fin']);
                    $rst_cmp = pg_fetch_array($Set->lista_costo_mp($rstPedido['opp_codigo'], $rstPedido['fec_fin']));
                    $rst_cins = pg_fetch_array($Set->lista_costo_insumos($rstPedido['opp_codigo'], $rstPedido['fec_fin']));
                    $rst_oc = pg_fetch_array($Set->lista_otros_costo($df[0], $df[1]));
                    $cunit = round($rst_cmp[val_unit], 2) + round($rst_cins[val_unit], 2) + round($rst_oc[dro_costo], 2);
                    $ctotal = round($rstPedido['peso'], 2) * round($cunit, 2);
                    $rst_cc = pg_fetch_array($Set->lista_central_costos($rstPedido['pro_id'], $rstPedido['fec_fin'], '1'));
                    if (empty($rst_oc)) {
                        $estado = "MES NO CERRADO";
                        $style = "style='color: red; font-weight: bold'";
                    } else if (empty($rst_cc)) {
                        $estado = "SIN REVISAR";
                        $style = "style='color: orange; font-weight: bold'";
                    }  else if (!empty($rst_cc) && $rst_oc[rop_estado] == 1) {
                        $estado = "ACTUALIZAR";
                        $style = "style='color: blue; font-weight: bold'";
                    }else if (!empty($rst_cc)) {
                        $estado = "REVISADO";
                        $style = "style='color: green; font-weight: bold'";
                    }
                    ?>
                    <tr>
                        <td align="left"><?PHP echo $n; ?></td>
                        <td align="center"><?PHP echo $rstPedido['fec_fin'] ?></td>
                        <td align="left"><?PHP echo $rstPedido['opp_codigo'] ?></td>   
                        <td align="left" ><?PHP echo $rstPedido['pro_codigo'] . ' - ' . $ref0; ?></td>
                        <td align="left"><?PHP echo $rstPedido['pro_uni'] ?></td>
                        <td align="right"><?PHP echo number_format($rstPedido['cnt']) ?></td>
                        <td align="right"><?PHP echo number_format($rstPedido['peso'], 2) ?></td>
                        <td align="right" id="cmp<?PHP echo $n ?>"><?PHP echo number_format($rst_cmp[val_unit], 2) ?></td>
                        <td align="right" id="cins<?PHP echo $n ?>"><?PHP echo number_format($rst_cins[val_unit], 2) ?></td>
                        <td align="right" id="otc<?PHP echo $n ?>"><?PHP echo number_format($rst_oc[dro_costo], 2) ?></td>
                        <td align="right"><?PHP echo number_format($cunit, 2) ?></td>
                        <td align="right"><?PHP echo number_format($ctotal, 2) ?></td>
                        <?PHP
                        if ($estado == 'SIN REVISAR' || $estado == 'ACTUALIZAR') {
                            ?>
                            <td align="center" onclick="save('<?PHP echo $rstPedido[pro_id] ?>', '<?PHP echo $cunit ?>', '1', '<?PHP echo $rstPedido[fec_fin] ?>', '<?PHP echo $rstPedido[pro_codigo] ?>',<?PHP echo $n ?>,<?PHP echo $rst_oc[rop_id] ?>)" <?PHP echo $style ?>><?PHP echo $estado ?></td>
                            <?PHP
                        } else {
                            ?>
                            <td align="center" <?PHP echo $style ?>><?PHP echo $estado ?></td>
                            <?PHP
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </body>
</html>
