<?php
session_start();
set_time_limit(0);
//date_default_timezone_set('America/Guayaquil');
include_once '../Includes/nusoap.php';
include_once '../Clases/Conn.php';

/////////*****************CLASE SRI********************
class SRI {

    var $con;

    function SRI() {
        $this->con = new Conn();
    }

    function recupera_datos($clave, $amb) {
        if ($amb == 2) { //Produccion
            $wsdl = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl', 'wsdl');
        } else {      //Pruebas
            $wsdl = new nusoap_client('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl', 'wsdl');
        }
        $req = $wsdl->call('autorizacionComprobante', array("claveAccesoComprobante" => $clave));
        $res = $req[RespuestaAutorizacionComprobante][autorizaciones][autorizacion];
        if (!empty($res[0][estado])) {
            return $respuesta = array($res[0][estado], $res[0][numeroAutorizacion], $res[0][fechaAutorizacion], $res[0][ambiente], $res[0][comprobante], $res[0][mensajes][mensaje][mensaje]);
        } else {
            return $respuesta = array($res[estado], $res[numeroAutorizacion], $res[fechaAutorizacion], $res[ambiente], $res[comprobante], $res[mensajes][mensaje][mensaje]);
        }
    }

    function documentos_noenviados() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_nota_credito where (char_length(ncr_autorizacion)<>37 or  ncr_autorizacion is null ) and ncr_sts<>1 order by 1 DESC limit 1");
        }
    }

    function actualizar_datos_documentos($estado, $auto, $fecha, $xml, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_nota_credito 
                SET ncr_estado_aut='RECIBIDA $estado',
                    ncr_autorizacion='$auto',
                    ncr_fec_hora_aut='$fecha',
                    ncr_xml_doc=''    
                WHERE ncr_id=$id ");
        }
    }

    function registra_errores($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO 
                erp_auditoria(
                usu_id,
                adt_date,
                adt_hour,
                adt_modulo,
                adt_accion,
                adt_documento,
                adt_campo,
                usu_login
                )VALUES(
                '$data[0]',
                '$data[1]',
                '$data[2]',
                '$data[3]',
                '$data[4]',    
                '$data[5]',
                '$data[6]',
                '$data[7]' ) ");
        }
    }

    function upd_documentos($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_nota_credito 
                set ncr_clave_acceso='$dat[0]', 
                ncr_estado_aut='$dat[1] $dat[2]', 
                ncr_observacion_aut='$dat[3]', 
                ncr_autorizacion='$dat[4]',
                ncr_fec_hora_aut='$dat[5]',
                ncr_xml_doc=''    
                where ncr_id=$id ");
        }
    }

    function lista_session_sri($date) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_auditoria WHERE adt_date='$date' and usu_id=0 order by 1 asc limit 1");
        }
    }

    function insert_load_sri($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_auditoria 
                    (usu_id,
                     usu_login,                    
                     adt_date,
                     adt_time)
                       VALUES('0',
                    '$data[0]',
                    '$data[1]',
                    '$data[2]'  ) ");
        }
    }
    function lista_ambiente() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones");
        }
    }
    

}

///////**********************CLASE SETTINGS***************/////

class Set {

    var $con;

    function Set() {
        $this->con = new Conn();
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * FROM  emisor where cod_punto_emision=$id ");
        }
    }

    ///////////////////nota credito
    function lista_una_nota_credito_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito where ncr_id=$id");
        }
    }

    function lista_det_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_nota_credito where ncr_id=$id order by dnc_cod_ice");
        }
    }

    function suma_ice($id, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(dnc_precio_total) FROM  erp_det_nota_credito where ncr_id=$id and dnc_cod_ice='$cod'");
        }
    }

    function lista_un_impuesto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_impuestos where imp_id=$id");
        }
    }

}

/////////*************CLASE AUDITORIA*****************************

class Auditoria {

    function sanear_string($string) {

        $string = trim($string);

        $string = str_replace(
                array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
        );

        $string = str_replace(
                array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
        );

        $string = str_replace(
                array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
        );

        $string = str_replace(
                array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
        );

        $string = str_replace(
                array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
        );

        $string = str_replace(
                array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
        );

        $string = str_replace(
                array("\\", "¨", "º", "-", "~",
            "#", "@", "|", "!", "\"",
            "·", "$", "%", "&", "/",
            "(", ")", "?", "'", "¡",
            "¿", "[", "^", "`", "]",
            "+", "}", "{", "¨", "´",
            ">", "< ", ";", ",", ":",
            "."), '', $string
        );


        return $string;
    }

}
$Sri = new SRI();
$rst_ambiente=  pg_fetch_array($Sri->lista_ambiente());
$ambiente = $rst_ambiente['con_ambiente']; //Pruebas 1    Produccion 2
$codigo = "12345678"; //Del ejemplo del SRI
$tp_emison = "1"; //Emision Normal
$parametros = "<parametros>" .
        "<keyStore>/usr/lib/jvm/jre/lib/security/cacerts</keyStore>" .
        "<keyStorePassword>changeit</keyStorePassword>" .
        "<ambiente>" . $ambiente . "</ambiente>" .
        "<pathFirma>/var/www/FacturacionElectronica/usr006.p12</pathFirma>" .
        "<passFirma>Noperti1952</passFirma>" .
        "</parametros>";
////////////////////////EJECUCION FUNCIONES Y CLASES///////////////////
$Set = new Set();
$rst_load = pg_fetch_array($Sri->lista_session_sri(date('Y-m-d')));
$fnow = date('Y-m-d');
$hnow = date('H:i:s');
$date1 = date_create($fnow . " " . $hnow);
$date2 = date_create($rst_load['adt_date'] . " " . $rst_load['adt_time']);
$diff = date_diff($date1, $date2);
$mins = $diff->format("%i");
        $doc = $Sri->recupera_datos('0605201501179000787100120010010000011161234567813', $ambiente);
        $cns = $Sri->documentos_noenviados();
        while ($rst = pg_fetch_array($cns)) {
            if (strlen($rst['ncr_clave_acceso']) == 49) { //Si tiene clave de acceso
                $doc1 = $Sri->recupera_datos($rst['ncr_clave_acceso'], $ambiente);
                if (strlen($doc1[1]) == 37) { //Si recupera los datos
                    if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst['ncr_id'])) {
                        $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst['ncr_clave_acceso'], '', 'SuperAdmin');
                        if (!$Sri->registra_errores($data)) {
                            echo pg_last_error();
                        }
                    }
                } else {//Si no recupera los datos.
                    $doc = envio_electronico($rst['ncr_id'], $ambiente, $codigo, $tp_emison, $parametros);
                    $dc = explode('&', $doc);
                    $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfico Conxecion;
                    if (strlen($dc[0]) == 49 || $err1 == true) {
                        $doc1 = $Sri->recupera_datos($dc[0]);
                        if (strlen($doc1[1]) == 37) {
                            if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst['ncr_id'])) {
                                $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst['ncr_clave_acceso'], '', 'SuperAdmin');
                                if (!$Sri->registra_errores($data)) {
                                    echo pg_last_error();
                                }
                            }
                        }
                    }
                }
            } else { //Si no tiene clave de acceso
                $doc = envio_electronico($rst['ncr_id'], $ambiente, $codigo, $tp_emison, $parametros);
                $dc = explode('&', $doc);
                $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfifico Conxecion;
                if (strlen($dc[0]) == 49 || $err1 == true) {
                    $doc1 = $Sri->recupera_datos($dc[0]);
                    if (strlen($doc1[1]) == 37) {
                        if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst['ncr_id'])) {
                            $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst['ncr_clave_acceso'], '', 'SuperAdmin');
                            if (!$Sri->registra_errores($data)) {
                                echo pg_last_error();
                            }
                        }
                    }
                }
            }
}

function envio_electronico($id, $ambiente, $codigo, $tp_emison, $parametros) {
    $Set = new Set();
    $Adt = new Auditoria();
    $Sri = new SRI();
    $rst = pg_fetch_array($Set->lista_una_nota_credito_id($id));
    $cns_det = $Set->lista_det_nota_credito($id);
    $rst_emi = pg_fetch_array($Set->lista_emisor($rst['emi_id']));
    $cod_doc = '04'; //Factura
    if ($rst_emi['cod_establecimiento_emisor'] > 0 && $rst_emi['cod_establecimiento_emisor'] < 10) {
        $txem = '00';
    } elseif ($rst_emi['cod_establecimiento_emisor'] >= 10 && $rst_emi['cod_establecimiento_emisor'] < 100) {
        $txem = '0';
    } else {
        $txem = '';
    }
    if ($rst_emi['cod_punto_emision'] > 0 && $rst_emi['cod_punto_emision'] < 10) {
        $txpe = '00';
    } elseif ($rst_emi['cod_punto_emision'] >= 10 && $rst_emi['cod_punto_emision'] < 100) {
        $txpe = '0';
    } else {
        $txpe = '';
    }
    $ems = $txem . $rst_emi['cod_establecimiento_emisor'];
    $pt_ems = $txpe . $rst_emi['cod_punto_emision'];

    $fecha = date_format(date_create($rst['nrc_fecha_emision']), 'd/m/Y');

    $ndoc = explode('-', $rst['ncr_numero']);
    $secuencial = $ndoc[2];
    $dir_cliente = $Adt->sanear_string($rst['ncr_direccion']);
    $telf_cliente = $Adt->sanear_string($rst['nrc_telefono']);
    $email_cliente = $Adt->sanear_string($rst['ncr_email']);
    $direccion = $Adt->sanear_string($rst_emi['dir_establecimiento_emisor']);
    $contabilidad = "SI";
    $razon_soc_comprador = $Adt->sanear_string($rst['ncr_nombre']);
    $id_comprador = $rst['nrc_identificacion'];
    if (strlen($id_comprador) == 13 && $id_comprador != '9999999999999') {
        $tipo_id_comprador = "04"; //RUC 04 
    } else if (strlen($id_comprador) == 10) {
        $tipo_id_comprador = "05"; //CEDULA 05 
    } else if ($id_comprador == '9999999999999') {
        $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
    } else {
        $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
    }

    $round = 2;
    $clave1 = trim(str_replace('/', '', $fecha) . $cod_doc . $rst_emi['identificacion'] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison);
    $cla = strrev($clave1);
    $n = 0;
    $p = 1;
    $i = strlen($clave1);
    $m = 0;
    $s = 0;
    $j = 2;
    while ($n < $i) {
        $d = substr($cla, $n, 1);
        $m = $d * $j;
        $s = $s + $m;
        $j++;
        if ($j == 8) {
            $j = 2;
        }
        $n++;
    }
    $div = $s % 11;
    $digito = 11 - $div;
    if ($digito < 10) {
        $digito = $digito;
    } else if ($digito == 10) {
        $digito = 1;
    } else if ($digito == 11) {
        $digito = 0;
    }
    $clave = trim(str_replace('/', '', $fecha) . $cod_doc . $rst_emi['identificacion'] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison . $digito);
    $xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
    $xml.="<notaCredito version='1.1.0' id='comprobante'>" . chr(13);
    $xml.="<infoTributaria>" . chr(13);
    $xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
    $xml.="<tipoEmision>" . $tp_emison . "</tipoEmision>" . chr(13);
    $xml.="<razonSocial>" . $Adt->sanear_string($rst_emi['nombre']) . "</razonSocial>" . chr(13);
    $xml.="<nombreComercial>" . $Adt->sanear_string($rst_emi['nombre_comercial']) . "</nombreComercial>" . chr(13);
    $xml.="<ruc>" . trim($rst_emi['identificacion']) . "</ruc>" . chr(13);
    $xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
    $xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
    $xml.="<estab>" . $ems . "</estab>" . chr(13);
    $xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
    $xml.="<secuencial>" . substr($rst['ncr_numero'], -9) . "</secuencial>" . chr(13);
    $xml.="<dirMatriz>" . $Adt->sanear_string($rst_emi['dir_establecimiento_matriz']) . "</dirMatriz>" . chr(13);
    $xml.="</infoTributaria>" . chr(13);

//ENCABEZADO
    $xml.="<infoNotaCredito>" . chr(13);
    $xml.="<fechaEmision>" . $fecha . "</fechaEmision>" . chr(13);
    $xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
    $xml.="<tipoIdentificacionComprador>" . $tipo_id_comprador . "</tipoIdentificacionComprador>" . chr(13);
    $xml.="<razonSocialComprador>" . $razon_soc_comprador . "</razonSocialComprador>" . chr(13);
    $xml.="<identificacionComprador>" . $rst['ncr_identificacion'] . "</identificacionComprador>" . chr(13);
    $xml.="<contribuyenteEspecial>" . '636' . "</contribuyenteEspecial>" . chr(13);
    $xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
    $xml.="<codDocModificado>0" . $rst['ncr_denominacion_comprobante'] . "</codDocModificado>" . chr(13);
    $xml.="<numDocModificado>" . $rst['ncr_num_comp_modifica'] . "</numDocModificado>" . chr(13);
    //$xml.="<fechaEmisionDocSustento>" . date_format(date_create($rst['ncr_fecha_emi_comp']), 'd/m/Y') . "</fechaEmisionDocSustento>" . chr(13);

    $xml.="<fechaEmisionDocSustento>" . $rst['ncr_fecha_emi_comp'] . "</fechaEmisionDocSustento>" . chr(13);
    
    $xml.="<totalSinImpuestos>" . round($rst['ncr_subtotal12'] + $rst['ncr_subtotal0'] + $rst['ncr_subtotal_no_iva'] + $rst['ncr_subtotal_ex_iva'], $round) . "</totalSinImpuestos>" . chr(13);
    $xml.="<valorModificacion>" . round($rst['nrc_total_valor'], $round) . "</valorModificacion>" . chr(13);
    $xml.="<moneda>DOLAR</moneda>" . chr(13);
    $xml.="<totalConImpuestos>" . chr(13);

    $base = 0;

    if ($rst['ncr_subtotal12'] != 0) {
        $codPorc = 2;
        $base = $rst['ncr_subtotal12'];
        $valo_iva = round(($base * 12) / 100, $round);
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13); //Tipo de Impuesto
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13); //Codigo del
        $xml.="<baseImponible>" . round($base, $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }
    if ($rst['ncr_subtotal0'] != 0) {
        $codPorc = 0;
        $base = $rst['ncr_subtotal0'];
        $valo_iva = 0;
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13); //Tipo de Impuesto
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13); //Codigo del
        $xml.="<baseImponible>" . round($base, $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }
    if ($rst['ncr_subtotal_no_iva'] != 0) {
        $codPorc = 6;
        $base = $rst['ncr_subtotal_no_iva'];
        $valo_iva = 0;
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13); //Tipo de Impuesto
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13); //Codigo del
        $xml.="<baseImponible>" . round($base, $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }
    if ($rst['ncr_subtotal_ex_iva'] != 0) {
        $codPorc = 7;
        $base = $rst['ncr_subtotal_ex_iva'];
        $valo_iva = 0;
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13); //Tipo de Impuesto
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13); //Codigo del
        $xml.="<baseImponible>" . round($base, $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }

    $xml.="</totalConImpuestos>" . chr(13);
    $xml.="<motivo>" . $rst['ncr_motivo'] . "</motivo>" . chr(13);
    $xml.="</infoNotaCredito>" . chr(13);
    $xml.="<detalles>" . chr(13);
    while ($reg_detalle1 = pg_fetch_array($cns_det)) {
        $xml.="<detalle>" . chr(13);
        $xml.="<codigoInterno>" . trim($reg_detalle1['dnc_codigo']) . "</codigoInterno >" . chr(13);
        if ($reg_detalle1["dnc_cod_aux"] != '') {
            $xml.="<codigoAdicional>" . trim($reg_detalle1["dnc_cod_aux"]) . "</codigoAdicional>" . chr(13);
        }
        $xml.="<descripcion>" . trim($reg_detalle1["dnc_descripcion"]) . "</descripcion>" . chr(13);
        $xml.="<cantidad>" . round($reg_detalle1["dnc_cantidad"], $round) . "</cantidad>" . chr(13);
        $xml.="<precioUnitario>" . round($reg_detalle1["dnc_precio_unit"], $round) . "</precioUnitario>" . chr(13);
        $xml.="<descuento>" . round($reg_detalle1["dnc_val_descuento"], $round) . "</descuento>" . chr(13);
        $xml.="<precioTotalSinImpuesto>" . round($reg_detalle1["dnc_precio_total"], $round) . "</precioTotalSinImpuesto>" . chr(13);
        $xml.="<impuestos>" . chr(13);
        $xml.="<impuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13);
        $valo_iva = 0;
        if ($reg_detalle1['dnc_iva'] == '12') {
            $codPorc = 2;
            $valo_iva = round($reg_detalle1['dnc_precio_total'] * 12 / 100, $round);
            $tarifa = 12;
        } else if ($reg_detalle1['dnc_iva'] == '0') {
            $codPorc = 0;
            $valo_iva = 0.00;
            $tarifa = 0;
        } else if ($reg_detalle1['dnc_iva'] == 'NO') {
            $codPorc = 6;
            $valo_iva = 0.00;
            $tarifa = 0;
        } else if ($reg_detalle1['dnc_iva'] == 'EX') {
            $codPorc = 7;
            $valo_iva = 0.00;
            $tarifa = 0;
        }
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13);
        $xml.="<tarifa>" . $tarifa . "</tarifa>" . chr(13);
        $xml.="<baseImponible>" . round($reg_detalle1["dnc_precio_total"], $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</impuesto>" . chr(13);
        $xml.="</impuestos>" . chr(13);
        $xml.="</detalle>" . chr(13);
    }
    $xml.="</detalles>" . chr(13);
    $xml.="<infoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Direccion'>" . $rst['ncr_direccion'] . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Telefono'>" . $rst['nrc_telefono'] . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Email'>" . strtolower(utf8_decode($rst['ncr_email'])) . "</campoAdicional>" . chr(13);
    $xml.="</infoAdicional>" . chr(13);
    $xml.="</notaCredito>" . chr(13);

    //echo htmlentities($xml);
    //echo htmlentities($parametros);

    $fch = fopen("../xml_docs/" . $clave . ".xml", "w+o");
    fwrite($fch, $xml);
    fclose($fch);

/*     $comando = 'java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "' . htmlentities($xml, ENT_QUOTES, "UTF-8") . '" "' . htmlentities($parametros, ENT_QUOTES, "UTF-8") . '"';
     echo shell_exec($comando);
    //$dat = $clave . '&' . shell_exec($comando);

    $data = explode('&', $dat);
    $sms = 0;
    $env = 'Envio SRI';
    $dt0 = $Adt->sanear_string($data[0]); //Clave de acceso
    $dt1 = $Adt->sanear_string($data[1]); // Recepcion
    $dt2 = $Adt->sanear_string($data[2]); // Autorizacion
    $dt3 = $Adt->sanear_string($data[3]); // Mensaje
    $dt4 = $Adt->sanear_string($data[4]); // Numero Autorizacion
    $dt5 = $data[5];                      // Hora y fecha Autorizacion
    $dt6 = '';                      // XML
    $dat = array($dt0, $dt1, $dt2, $dt3, $dt4, $dt5, $dt6);
    if (!$Sri->upd_documentos($dat, $id)) {
        $sms = pg_last_error();
        $env = 'Envio Fallido';
    }
    return $sms . '&' . $clave;*/
}
