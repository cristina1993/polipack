<?php
include_once '../Clases/clsClase_factura.php';
$Doc = new Clase_factura();
$op = $_GET[op];
$cli = $_GET[cli];
$tpd = $_GET[tpd];
$ndoc = $_GET[ndoc];
if ($op == 0) {
    $tpd = substr($_GET[ndoc], 0, 2);
    $punto = '0' . substr($_GET[ndoc], 2, 2);
    $sec = substr($_GET[ndoc], 4, 9);
    switch (strlen($sec)) {
        case 1: $tx = '00000000';
            break;
        case 2: $tx = '0000000';
            break;
        case 3: $tx = '000000';
            break;
        case 4: $tx = '00000';
            break;
        case 5: $tx = '0000';
            break;
        case 6: $tx = '000';
            break;
        case 7: $tx = '00';
            break;
        case 8: $tx = '0';
            break;
        case 9: $tx = '';
            break;
    }
    $id = $punto . '-001-' . $tx . $sec;
    switch ($tpd) {
        case '01':$tp_documento = 'FACTURA';
            $rst = pg_fetch_array($Doc->lista_factura_clave($cli, $id));
            $script = "descarga_documento('$rst[id]',1)";
            if (empty($rst[clave])) {
                $xml = "descarga_xml('$rst[id]',1)";
            } else {
                $xml = "descarga_xml('$rst[clave]',0)";
            }
            break;
        case '04':$tp_documento = 'NOTA DE CREDITO';
            $rst = pg_fetch_array($Doc->lista_nota_credito_clave($cli, $id));
            $script = "descarga_documento('$rst[id]','4')";
            if (empty($rst[clave])) {
                $xml = "descarga_xml('$rst[id]',4)";
            } else {
                $xml = "descarga_xml('$rst[clave]',0)";
            }
            break;
    }
} else {
    $punto = substr($_GET[ndoc], 0, 3);
    switch ($tpd) {
        case '01':$tp_documento = 'FACTURA';
            $rst = pg_fetch_array($Doc->lista_factura_clave($cli, $ndoc));
            $script = "descarga_documento('$rst[id]',1)";
            if (empty($rst[clave])) {
                $xml = "descarga_xml('$rst[id]',1)";
            } else {
                $xml = "descarga_xml('$rst[clave]',0)";
            }
            break;
        case '04':$tp_documento = 'NOTA DE CREDITO';
            $rst = pg_fetch_array($Doc->lista_nota_credito_clave($cli, $ndoc));
            $script = "descarga_documento('$rst[id]',4)";
            if (empty($rst[clave])) {
                $xml = "descarga_xml('$rst[id]',4)";
            } else {
                $xml = "descarga_xml('$rst[clave]',0)";
            }
            break;
        case '05':$tp_documento = 'NOTA DE DEBITO';
            $rst = pg_fetch_array($Doc->lista_nota_debito_clave($cli, $ndoc));
            $script = "descarga_documento('$rst[id]',5)";
            if (empty($rst[clave])) {
                $xml = "descarga_xml('$rst[id]',5)";
            } else {
                $xml = "descarga_xml('$rst[clave]',0)";
            }
            break;
        case '06':$tp_documento = 'GUIA DE REMISION';
            $rst = pg_fetch_array($Doc->lista_guia_remision_clave($cli, $ndoc));
            $script = "descarga_documento('$rst[id]',6)";
            if (empty($rst[clave])) {
                $xml = "descarga_xml('$rst[id]',6)";
            } else {
                $xml = "descarga_xml('$rst[clave]',0)";
            }
            break;
        case '07':$tp_documento = 'RETENCION';
            $rst = pg_fetch_array($Doc->lista_retencion_clave($cli, $ndoc));
            $script = "descarga_documento('$rst[id]',7)";
            if (empty($rst[clave])) {
                $xml = "descarga_xml('$rst[id]',7)";
            } else {
                $xml = "descarga_xml('$rst[clave]',0)";
            }
            break;
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Documentos Eletronicos</title>
    <head>
        <style>
            thead tr th{
                font-family: sans-serif;
                font-weight: bold;
                background: #f97e76;
                color: #ffffff;
                text-align: center;
            }
            tbody tr td{
                text-align: center;
                background:#f8f8f8; 
                border-bottom:dashed 1px #ccc; 
            }
            img{
                cursor:pointer; 
                padding:2px;
                border: solid 1px #ccc;
            }
            img:hover{
                background:#f97e76;  
            }
        </style>
        <script>
            function descarga_documento(id, tipo, punto) {
                if (tipo == 1) {
                    window.location = '../Reports/pdf_factura.php?ide=' + id;
                } else if (tipo == 4) {
                    window.location = '../Reports/pdf_nota_credito.php?ide=' + id;
                }
                if (tipo == 5) {
                    window.location = '../Reports/pdf_nota_debito.php?ide=' + id;
                }
                if (tipo == 6) {
                    window.location = '../Reports/pdf_guia_remision.php?ide=' + id;
                }
                if (tipo == 7) {
                    window.location = '../Reports/pdf_retencion.php?ide=' + id;
                }
            }

            function descarga_xml(id, tp) {
                window.location = '../Reports/descargar_xml.php?id=' + id + '&tp=' + tp;
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script>
    </script>


</head>
<body>

    <table style="width:100%" id="tbl" border="0">
        <thead>
            <tr>
                <th>Tipo Documento</th>
                <th>Fecha de Emision</th>
                <th>Numero Documento</th>
                <th>Estado</th>
                <th>PDF</th>
                <th>XML</th>
            </tr>
        </thead>
        <tbody id="tbody">
            <?php
            if (!empty($rst)) {
                if (strlen(trim($rst[autorizacion])) == 37) {
                    $rst[com_estado] = 'AUTORIZADO';
                }
                ?>
            <td><?php echo $tp_documento ?></td>
            <td><?php echo $rst[fecha] ?></td>
            <td><?php echo $rst[numero] ?></td>
            <td><?php echo $rst[estado] ?></td>

            <td><img width="24px" src="../img/orden.png" onclick="<?php echo $script ?>" title="Descargar PDF Documento"/></td>
            <td><img width="24px" src="../img/xml.png" onclick="<?php echo $xml ?>" title="Descargar XML Documento"/></td>
            <?php
        } else {
            ?>
            <td colspan="6">No existen registros de esta consulta por favor verifique datos</td>
            <?php
        }
        ?>
    </tbody>
</table>       
</body>    
</html>


