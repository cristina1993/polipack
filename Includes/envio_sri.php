<?php
session_start();
set_time_limit(0);
include_once '../Includes/nusoap.php';
include_once '../Clases/Conn.php';
date_default_timezone_set('America/Guayaquil');

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
            return pg_query("select * from erp_factura where (char_length(fac_autorizacion)<>37 or  fac_autorizacion is null)");
        }
    }

    function actualizar_datos_documentos($estado, $auto, $fecha, $xml, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_factura 
                SET fac_estado_aut='RECIBIDA $estado',
                    fac_autorizacion='$auto',
                    fac_fec_hora_aut='$fecha',
                    fac_xml_doc=''    
                WHERE fac_id=$id ");
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
            return pg_query("update erp_factura 
                set fac_clave_acceso='$dat[0]', 
                fac_estado_aut='$dat[1] $dat[2]', 
                fac_observacion_aut='$dat[4]',
                fac_fec_hora_aut='$dat[5]',
                fac_xml_doc=''    
                where fac_id=$id ");
        }
    }

    function lista_session_sri($date) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_auditoria WHERE adt_date='$date' and usu_id=0 order by 1 desc limit 1");
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

    function lista_una_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura where fac_id=$id ");
        }
    }

    function lista_emisor($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * FROM  emisor where cod_punto_emision=$cod ");
        }
    }

    function lista_detalle_factura($fact) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_factura where fac_id='$fact' ");
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

///*****************SET XML**********************
$Sri = new SRI();
$rst_ambiente=  pg_fetch_array($Sri->lista_ambiente());
$ambiente = $rst_ambiente[con_ambiente]; //Pruebas 1    Produccion 2
$codigo = "12345678"; //Del ejemplo del SRI
$tp_emison = "1"; //Emision Normal
$parametros = "<parametros>" .
        "<keyStore>/usr/lib/jvm/jre/lib/security/cacerts</keyStore>" .
        "<keyStorePassword>changeit</keyStorePassword>" .
        "<ambiente>" . $ambiente . "</ambiente>" .
        "<pathFirma>/var/www/FacturacionElectronica/usr006.p12</pathFirma>" .
        "<passFirma>Nicco1952</passFirma>" .
        "</parametros>";

///////////**********************************
////////////////////////EJECUCION FUNCIONES Y CLASES///////////////////
$rst_load = pg_fetch_array($Sri->lista_session_sri(date('Y-m-d')));
$fnow = date('Y-m-d');
$hnow = date('H:i:s');
$date1 = date_create($fnow . " " . $hnow);
$date2 = date_create($rst_load[adt_date] . " " . $rst_load[adt_time]);
$diff = date_diff($date1, $date2);
$mins = $diff->format("%i");
$mins=0;
//if (1 == 1) {
//    if (!$Sri->insert_load_sri(array($_SESSION[usuario], $fnow, $hnow))) {
//        echo pg_last_error();
//    } else {
        //$doc = $Sri->recupera_datos('0209201501179000787100120030010000008981234567816', $ambiente);
        $cns = $Sri->documentos_noenviados();
        while ($rst = pg_fetch_array($cns)) {
            if (strlen($rst[fac_clave_acceso]) == 49) { //Si tiene clave de acceso
                $doc1 = $Sri->recupera_datos($rst[fac_clave_acceso], $ambiente);
                if (strlen($doc1[1]) == 37 && $doc1[0] != 'NO AUTORIZADO') { //Si recupera los datos
                    if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[fac_id])) {
                        $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[fac_clave_acceso], '', 'SuperAdmin');
                        if (!$Sri->registra_errores($data)) {
                            echo pg_last_error();
                        }
                    }
                } else {//Si no recupera los datos.
                    $doc = envio_electronico($rst[fac_id], $ambiente, $codigo, $tp_emison, $parametros);
                    $dc = explode('&', $doc);
                    $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfico Conxecion;
                    if (strlen($dc[0]) == 49 || $err1 == true) {
                        $doc1 = $Sri->recupera_datos($dc[0]);
                        if (strlen($doc1[1]) == 37) {
                            if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[fac_id])) {
                                $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[fac_clave_acceso], '', 'SuperAdmin');
                                if (!$Sri->registra_errores($data)) {
                                    echo pg_last_error();
                                }
                            }
                        }
                    }
                }
            } else { //Si no tiene clave de acceso
                $doc = envio_electronico($rst[fac_id], $ambiente, $codigo, $tp_emison, $parametros);
                $dc = explode('&', $doc);
                $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfifico Conxecion;
                if (strlen($dc[0]) == 49 || $err1 == true) {
                    $doc1 = $Sri->recupera_datos($dc[0]);
                    if (strlen($doc1[1]) == 37) {
                        if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[fac_id])) {
                            $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[fac_clave_acceso], '', 'SuperAdmin');
                            if (!$Sri->registra_errores($data)) {
                                echo 'Actualiza ' . pg_last_error();
                            }
                        }
                    }
                }
            }
//        }
//    }
}

function envio_electronico($id, $ambiente, $codigo, $tp_emison, $parametros) {
    $Set = new Set();
    $Adt = new Auditoria();
    $Sri = new SRI();
    $rst_enc = pg_fetch_array($Set->lista_una_factura_id($id));
    $ndoc = explode('-', $rst_enc[fac_numero]);
    $nfact = str_replace('-', '', $rst_enc[fac_numero]);
    $ems = $ndoc[0];
    $emisor = intval($ndoc[0]);
    $pt_ems = $ndoc[1];
    $secuencial = $ndoc[2];
    $emis = pg_fetch_array($Set->lista_emisor($emisor));
    $cns_det = $Set->lista_detalle_factura($rst_enc[fac_id]);
    $cns_det2 = $Set->lista_detalle_factura($rst_enc[fac_id]);
    $cod_doc = "01"; //01= factura, 02=nota de credito tabla 4
    $fecha = date_format(date_create($rst[fac_fecha_emision]), 'd/m/Y');
    $f2 = date_format(date_create($rst[fac_fecha_emision]), 'dmY');
    $dir_cliente = $Adt->sanear_string($rst_enc[fac_direccion]);
    $telf_cliente = $Adt->sanear_string($rst_enc[fac_telefono]);
    $email_cliente = $Adt->sanear_string($rst_enc[fac_email]);
    $direccion = $Adt->sanear_string($emis[dir_establecimiento_emisor]);
    $contabilidad = "SI";
    $razon_soc_comprador = $Adt->sanear_string($rst_enc[fac_nombre]);
    $id_comprador = $rst_enc[fac_identificacion];
    if (strlen($id_comprador) == 13 && $id_comprador != '9999999999999' && substr($id_comprador, -3) == '001') {
        $tipo_id_comprador = "04"; //RUC 04 
    } else if (strlen($id_comprador) == 10) {
        $tipo_id_comprador = "05"; //CEDULA 05 
    } else if ($id_comprador == '9999999999999') {
        $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
    } else {
        $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
    }
    $round = 2;
    $clave1 = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison);
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
    $clave = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison . $digito);

    $xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
    $xml.="<factura version='1.1.0' id='comprobante'>" . chr(13);
    $xml.="<infoTributaria>" . chr(13);
    $xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
    $xml.="<tipoEmision>" . $tp_emison . "</tipoEmision>" . chr(13);
    $xml.="<razonSocial>" . $Adt->sanear_string($emis[nombre]) . "</razonSocial>" . chr(13);
    $xml.="<nombreComercial>" . $Adt->sanear_string($emis[nombre_comercial]) . "</nombreComercial>" . chr(13);
    $xml.="<ruc>" . trim($emis[identificacion]) . "</ruc>" . chr(13);
    $xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
    $xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
    $xml.="<estab>" . $ems . "</estab>" . chr(13);
    $xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
    $xml.="<secuencial>" . $secuencial . "</secuencial>" . chr(13);
    $xml.="<dirMatriz>" . $Adt->sanear_string($emis[dir_establecimiento_matriz]) . "</dirMatriz>" . chr(13);
    $xml.="</infoTributaria>" . chr(13);
//ENCABEZADO
    $xml.="<infoFactura>" . chr(13);
    $xml.="<fechaEmision>" . $fecha . "</fechaEmision>" . chr(13);
    $xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
    $xml.="<contribuyenteEspecial>636</contribuyenteEspecial>" . chr(13);
    $xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
    $xml.="<tipoIdentificacionComprador>" . $tipo_id_comprador . "</tipoIdentificacionComprador>" . chr(13);
    $xml.="<razonSocialComprador>" . $razon_soc_comprador . "</razonSocialComprador>" . chr(13);
    $xml.="<identificacionComprador>" . $id_comprador . "</identificacionComprador>" . chr(13);
    $xml.="<totalSinImpuestos>" . round($rst_enc[fac_subtotal12] + $rst_enc[fac_subtotal0] + $rst_enc[fac_subtotal_ex_iva] + $rst_enc[fac_subtotal_no_iva], $round) . "</totalSinImpuestos>" . chr(13);
    $xml.="<totalDescuento>" . round($rst_enc["fac_total_descuento"], $round) . "</totalDescuento>" . chr(13);
    $xml.="<totalConImpuestos>" . chr(13);
    $base = 0;
    while ($reg_detalle = pg_fetch_array($cns_det)) {
        if ($reg_detalle[dfc_iva] == 12) {
            $codPorc = 2;
        } else {
            $codPorc = 0;
        }

        $base = $base + round($reg_detalle[dfc_precio_total], $round);
    }
    if ($codPorc == 2) {
        $valo_iva = round($base * 12 / 100, $round);
    } else {
        $valo_iva = 0.00;
    }

    $xml.="<totalImpuesto>" . chr(13);
    $xml.="<codigo>2</codigo>" . chr(13); //Tipo de Impuesto
    $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13); //Codigo del
    $xml.="<baseImponible>" . $base . "</baseImponible>" . chr(13);
    $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
    $xml.="</totalImpuesto>" . chr(13);
    $xml.="</totalConImpuestos>" . chr(13);
    $xml.="<propina>0.00</propina>" . chr(13);
    $xml.="<importeTotal>" . round($rst_enc[fac_total_valor], $round) . "</importeTotal>" . chr(13);
    $xml.="<moneda>DOLAR</moneda>" . chr(13);
    $xml.="</infoFactura>" . chr(13);

    $xml.="<detalles>" . chr(13);
    while ($reg_detalle = pg_fetch_array($cns_det2)) {
        $xml.="<detalle>" . chr(13);
        $xml.="<codigoPrincipal>" . trim($reg_detalle[dfc_codigo]) . "</codigoPrincipal>" . chr(13);
        if (strlen(trim($reg_detalle[dfc_cod_aux])) == 0) {
            $reg_detalle[dfc_cod_aux] = $reg_detalle[dfc_codigo];
        }
        $xml.="<codigoAuxiliar>" . trim($reg_detalle[dfc_cod_aux]) . "</codigoAuxiliar>" . chr(13);

        $xml.="<descripcion>" . trim($Adt->sanear_string($reg_detalle[dfc_descripcion])) . "</descripcion>" . chr(13);
        $xml.="<cantidad>" . round($reg_detalle[dfc_cantidad], $round) . "</cantidad>" . chr(13);
        $xml.="<precioUnitario>" . round($reg_detalle[dfc_precio_unit], 4) . "</precioUnitario>" . chr(13);
        $xml.="<descuento>" . round($reg_detalle[dfc_val_descuento], $round) . "</descuento>" . chr(13);
        $xml.="<precioTotalSinImpuesto>" . round($reg_detalle[dfc_precio_total], $round) . "</precioTotalSinImpuesto>" . chr(13);
        $xml.="<impuestos>" . chr(13);
        $xml.="<impuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13);
        if ($reg_detalle[dfc_iva] == 12) {
            $codPorc = 2;
            $valo_iva = round($reg_detalle[dfc_precio_total] * 12 / 100, $round);
            $tarifa = 12;
        } else {
            $tarifa = 0;
            $codPorc = 0;
            $valo_iva = 0.00;
        }
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13);
        $xml.="<tarifa>" . $tarifa . "</tarifa>" . chr(13);
        $xml.="<baseImponible>" . round($reg_detalle[dfc_precio_total], $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</impuesto>" . chr(13);
        $xml.="</impuestos>" . chr(13);
        $xml.="</detalle>" . chr(13);
    }
    $xml.="</detalles>" . chr(13);
    $xml.="<infoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Direccion'>" . $dir_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Email'>" . strtolower(utf8_decode($email_cliente)) . "</campoAdicional>" . chr(13);
    if ($rst_enc[observaciones] != '') {
        $xml.="<campoAdicional nombre='Observaciones'>" . strtoupper($rst_enc[fac_observaciones]) . "</campoAdicional>" . chr(13);
    }
    $xml.="</infoAdicional>" . chr(13);
    $xml.="</factura>" . chr(13);
    
    $fch = fopen("../xml_docs/" . $clave . ".xml", "w+o");
    fwrite($fch, $xml);
    fclose($fch);
    
        
    $comando = 'java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "' . htmlentities($xml, ENT_QUOTES, "UTF-8") . '" "' . htmlentities($parametros, ENT_QUOTES, "UTF-8") . '"';
    echo $dat = $clave . '&' . shell_exec($comando).'<br>';
    $data = explode('&', $dat);
    $sms = 0;
    $env = 'Envio SRI';
    $dt0 = $Adt->sanear_string($data[0]); //Clave de acceso
    $dt1 = $Adt->sanear_string($data[1]); // Recepcion
    $dt2 = $Adt->sanear_string($data[2]); // Autorizacion
    $dt3 = $Adt->sanear_string($data[3]); // Mensaje
    $dt4 = $Adt->sanear_string($data[4]); // Numero Autorizacion
    $dt5 = $data[5];                      // Hora y fecha Autorizacion
    $dt6 = '';                      // XML yano se recupera
    $dat = array($dt0, $dt1, $dt2, $dt3, $dt4, $dt5, $dt6);

    if (!$Sri->upd_documentos($dat, $id)) {
        $sms = "Err " . pg_last_error();
        $env = 'Envio Fallido';
    }

    return $sms . '&' . $clave;
}
?>

