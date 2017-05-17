<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cuentasxpagar.php';
$Cxp = new CuentasPagar();
$desde = date('Y-m-d');
$hasta = date('Y-m-d');
if (!empty($_GET[desde])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $txt = $_GET[txt];
    if (!empty($_GET[txt])) {
        $text = "and (rd.reg_ruc_cliente like '%$txt%' or op.obl_forma_pago like '%$txt%' or op.obl_doc like '%$txt%')";
        $text2 = "and (c.cli_ced_ruc like '%$txt%' or op.obl_forma_pago like '%$txt%' or op.obl_doc like '%$txt%')";
    } else {
        $text = "and op.obl_fecha_pago between '$desde' and '$hasta'";
        $text2 = "and op.obl_fecha_pago between '$desde' and '$hasta'";
    }
    $cns = $Cxp->lista_pagos_aprobados($text,$text2);
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
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
            });


        </script> 
        <style>
            #mn341{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                /*                background:#ccc;
                                color:black;
                                font-weight:bolder; */
            }
            .axb{
                padding:2px;
                width: 20px;
                background:#616975;
                border:solid 1px #00529B; 
                margin-left:5px;
                border-radius:5px; 
                cursor:pointer; 
            }
            .axb:hover{
                background:#7198ab; 
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
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
                <center class="cont_title" ><?php echo "REPORTE PAGOS" ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frmSearch" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off" >
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                        Buscar por:<input type="text" name="txt" size="35" id="txt" value="" list="lista_proveedores"/>
                        <datalist id="lista_proveedores">
                            <?php
                            $cns_prov = $Cxp->lista_proveedores();
                            while ($rst_prov = pg_fetch_array($cns_prov)) {
                                echo "<option value='$rst_prov[cli_ced_ruc]'>$rst_prov[cli_raz_social]</option>";
                            }
                            ?>
                        </datalist>
                        Desde  :<input type="text" size="12" id="desde" name="desde" value="<?php echo $desde ?>"/>
                        <img src="../img/calendar.png" id="im-desde"/>
                        Hasta  :<input type="text" size="12" id="hasta" name="hasta" value="<?php echo $hasta ?>" />
                        <img src="../img/calendar.png" id="im-hasta"/>
                        <button class="btn" title="Buscar" id="search" name="search" onclick="frmSearch.submit()">Buscar</button>
                    </form>


                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="6"></th>
                    <th colspan="6"></th>
                </tr>
                <tr>
                    <th width="10px">No</th>
                    <th width="80px">Orden Pago</th>
                    <th width="120px">Ruc</th>
                    <th width="250px">Proveedor</th>
                    <th width="100px">Valor A pagar</th>
                    <th width="100px">Formas de Pago</th>
                    <th width="250px">Concepto</th>
                    <th width="100px">#Documento</th>
                    <th width="250px">Cuenta</th>
                    <th width="100px">Estado</th>
                    <th width="100px">F.Pago</th>
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_cli = pg_fetch_array($Cxp->lista_cliente_ruc($rst[reg_ruc_cliente]));
                    $rst_cta = pg_fetch_array($Cxp->listar_un_asiento($rst[obl_cuenta]));
                    if ($rst[obl_tipo] == 1) {
                        $op = 2;
                    } else {
                        $op = 0;
                    }

                    if ($rst[obl_estado_obligacion] == 3) {
                        $sts = 'Pagado';
                        $fpago = $rst[obl_fecha_pago];
                        $concepto = $rst[obl_concepto];
                        $documento = $rst[obl_doc];
                        $cuenta_contable = $rst[obl_cuenta];
                        $cuenta_nombre = $rst_cta[pln_descripcion];
                        $forma_pago = $rst[obl_forma_pago];
                        $disabled = 'disabled';
                        $disabled1 = 'disabled';
                    } else if ($rst[obl_estado_obligacion] == 1 && $rst[obl_tipo] == 1) {
                        $sts = '';
                        $fpago = $rst[obl_fecha_pago];
                        $concepto = $rst[obl_concepto];
                        $documento = $rst[obl_doc];
                        $cuenta_contable = $rst[obl_cuenta];
                        $cuenta_nombre = $rst_cta[pln_descripcion];
                        $forma_pago = $rst[obl_forma_pago];
                        $disabled1 = 'disabled';
                        $disabled = '';
                    } else {
                        $sts = '';
                        $fpago = '';
                        $concepto = '';
                        $documento = '';
                        $cuenta_contable = '';
                        $cuenta_nombre = '';
                        $forma_pago = '';
                        $disabled = '';
                        $disabled1 = '';
                    }
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td ><?php echo $rst[obl_codigo] ?></td>
                        <td ><?php echo $rst[reg_ruc_cliente] ?></td>
                        <td ><?php echo $rst_cli[cli_raz_social] ?></td>
                        <td align="right" id="<?php echo 'cant_' . $rst[reg_ruc_cliente] ?>" ><?php echo number_format($rst[sum], 2) ?></td>
                        <td align="center"> <?php echo $forma_pago ?></td>
                        <td ><?php echo $concepto ?></td>
                        <td ><?php echo $documento ?></td>
                        <td align="left" ><?php echo $cuenta_contable . '  -  ' . $cuenta_nombre ?></td>
                        <td><?php echo $sts; ?></td>
                        <td><?php echo $fpago; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>            
    </body>   
</html>
