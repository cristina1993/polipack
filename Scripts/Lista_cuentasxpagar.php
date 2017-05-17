<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cuentasxpagar.php';
$Cxp = new CuentasPagar();
if (isset($_GET[txt], $_GET[desde], $_GET[hasta], $_GET[estado])) {
    $nm = trim(strtoupper($_GET[txt]));
    $est1 = strtoupper($_GET[estado]);
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];

    if (!empty($_GET[txt])) {
        $txt = "and cli_raz_social like '%$nm%' or reg_ruc_cliente like '%$nm%' or reg_num_documento LIKE '%$nm%' or reg_concepto like '%$nm%' and reg_estado<3";
    } else {
        if ($est1 == 0) {
            $txt = " and c.reg_femision between '$desde' and '$hasta' and c.reg_estado<3";
        } else if ($est1 == 1) {
            $txt = " and c.reg_femision between '$desde' and '$hasta' and not exists(Select from erp_ctasxpagar ct where c.reg_id=ct.reg_id) and c.reg_estado<3";
        } else if ($est1 == 2) {
            $txt = " and c.reg_femision between '$desde' and '$hasta' and reg_total>(Select sum(ctp_monto)from erp_ctasxpagar ct where c.reg_id=ct.reg_id) and c.reg_estado<3";
        } else if ($est1 == 3) {//pagados
            $txt = " and c.reg_femision between '$desde' and '$hasta' and reg_total=(Select sum(ctp_monto)from erp_ctasxpagar ct where c.reg_id=ct.reg_id) and c.reg_estado<3";
        }
    }
    $cns = $Cxp->lista_documentos_buscador($txt);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
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

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id) {
                fec1 = $('#desde').val();
                fec2 = $('#hasta').val();
                txt = $('#txt').val();
                est = $('#estado').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Editar
                        frm.src = '../Scripts/Form_ctasxpagar.php?id=' + id + '&fec1=' + fec1 + '&fec2=' + fec2 + '&estado=' + est + '&nm=' + txt;//Cambiar Form_productos
                        look_menu();
                        parent.document.getElementById('contenedor2').rows = "*,70%";
                        break;
                    case 1://Reporte estado cuenta
                        frm.src = '../Scripts/frm_pdf_estado_pagar.php?txt=' + $('#txt').val() + '&d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&e=' + $('#estado').val() + '&cli=' + id;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;

                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }


        </script> 
        <style>
            #mn182{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
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
                <center class="cont_title" ><?php echo "CUENTAS POR PAGAR" ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frmSearch" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                        Factura/Cliente  :<input type="text" name="txt" size="25" id="txt" value="<?php echo $nm ?>"/>
                        Estado  :<select id="estado" name="estado">
                            <option value="0">Todos</option>
                            <option value="1">Por Pagar</option>
                            <option value="2">Parcialmente Pagado</option>
                            <option value="3">Pagado</option>
                        </select>
                        Fecha Emision: 
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
            <th>No</th>
            <th>Ruc</th>
            <th>Proveedor</th>
            <th>Documento</th>
            <th>Concepto</th>
            <th>Fecha Emision</th>
            <th>Fecha Vencimiento</th>
            <th>Total</th>
            <th>Pagado</th>
            <th>Saldo</th>
            <th>Estado</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $d = 0;
            $f = 0;
            $grup = '';
            while ($rst = pg_fetch_array($cns)) {
                $ast = '';
                $res = pg_fetch_array($Cxp->suma_pagos1($rst['reg_id']));
                $pagado = $res[monto];
                $total_valor = $rst[reg_total] + $res[debito];
                $saldo = $total_valor - $pagado;
                if ($res[debito] != 0) {
                    $ast = '*';
                }

                $cns_pag = $Cxp->lista_pagos_regfac($rst[reg_id]);
                while ($rst_pag = pg_fetch_array($cns_pag)) {
                    $cns_cta = $Cxp->listar_una_ctapagar_pagid($rst_pag[pag_id]);
                    $rst_ct = pg_fetch_array($cns_cta);
                    $f++;
                    $fp = pg_num_rows($cns_pag);
                    if ($rst_pag[pag_fecha_v] != $rst_ct[ctp_fecha] && $d == 0) {
                        $fec = $rst_pag[pag_fecha_v];
                        $d = 1;
                    }

                    if ($fp == $f) {
                        $fec = $rst_pag[pag_fecha_v];
                    }
                }
                if (round ($saldo,$dcm) == 0) {
                    $estado = 'PAGADO';
                } else if (round ($pagado,$dcm) == 0) {
                    $estado = 'POR PAGAR';
                } else if (round ($pagado,$dcm) != round ($saldo,$dcm)) {
                    $estado = 'PARCIALMENTE PAGADO';
                }
                $fecha = $fec;
                $rst_cli = pg_fetch_array($Cxp->lista_cliente_ruc($rst[reg_ruc_cliente]));
                $n++;
                if ($grup != $rst['reg_ruc_cliente'] && $n != 1) {
                    ?>
                    <tr>
                        <td class="totales" ></td>
                        <td class="totales" ></td>
                        <td class="totales" ></td>
                        <td class="totales" ></td>
                        <td class="totales" ></td>
                        <td class="totales" ></td>
                        <td class="totales" >Total</td>  
                        <td class="totales" align="right" ><?php echo number_format($tv, $dcm) ?></td>
                        <td class="totales" align="right"><?php echo number_format($tp, $dcm) ?></td>
                        <td class="totales" align="right"><?php echo number_format($ts, $dcm) ?></td>
                        <td class="totales" ></td>
                    </tr>
                    <?PHP
                    $tv = 0;
                    $tp = 0;
                    $ts = 0;
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <?php
                    if ($grup != $rst['reg_ruc_cliente']) {
                        ?>
                        <td><?php echo $rst['reg_ruc_cliente'] ?></td>
                        <td onclick="auxWindow(1, '<?php echo $rst['reg_ruc_cliente'] ?>')"><?php echo $rst_cli['cli_raz_social'] ?></td>
                        <?php
                    } else {
                        ?>
                        <td></td>
                        <td onclick="auxWindow(1, '<?php echo $rst['reg_ruc_cliente'] ?>')"></td>
                        <?php
                    }
                    ?>
                    <td onclick="auxWindow(0,<?php echo $rst[reg_id] ?>)"><?php echo $rst['reg_num_documento'] ?></td>
                    <td ><?php echo $rst['reg_concepto'] ?></td>
                    <td ><?php echo $rst['reg_femision'] ?></td>
                    <td ><?php echo $fecha ?></td>
                    <td align="right" ><?php echo $ast . number_format($total_valor, $dcm) ?></td>
                    <td align="right" ><?php echo number_format($pagado, $dcm) ?></td>
                    <td align="right" ><?php echo number_format($saldo, $dcm) ?></td>
                    <td ><?php echo $estado ?></td>
                </tr>
                <?PHP
                $d = 0;
                $grup = $rst['reg_ruc_cliente'];
                $tv+=round($total_valor, $dcm);
                $tp+=round($pagado, $dcm);
                $ts+=round($saldo, $dcm);
            }
            ?>
            <tr>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" >Total</td>  
                <td class="totales" align="right" ><?php echo number_format($tv, $dcm) ?></td>
                <td class="totales" align="right"><?php echo number_format($tp, $dcm) ?></td>
                <td class="totales" align="right"><?php echo number_format($ts, $dcm) ?></td>
                <td class="totales" ></td>
            </tr>
        </tbody>
    </table>            
</body>   
</html>
<script>
    var p = '<?php echo $est1 ?>';
    $('#estado').val(p);
</script>

