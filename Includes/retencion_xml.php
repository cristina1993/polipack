<?php

set_time_limit(0);
date_default_timezone_set('America/Guayaquil');
include_once '../Includes/nusoap.php';
include_once '../Clases/Conn.php';
$id = $_REQUEST[id];

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

        $res = $wsdl->call('autorizacionComprobante', array("claveAccesoComprobante" => $clave));
        $req = $res[RespuestaAutorizacionComprobante][autorizaciones][autorizacion];
        if ($wsdl->fault) {
            $respuesta = array_merge(array('err'), ($res));
        } else {
            $err = $wsdl->getError();
            if ($err) {
                $respuesta = $err;
            } else {
                $respuesta = array($req[estado], $req[numeroAutorizacion], $req[fechaAutorizacion], $req[ambiente], $req[comprobante], $req[mensajes][mensaje][mensaje]);
            }
        }
        return $respuesta;
    }

}

///////**********************CLASE SETTINGS***************/////

class Set {

    var $con;

    function Set() {
        $this->con = new Conn();
    }

    function lista_emisor($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * FROM  emisor where cod_punto_emision=$cod ");
        }
    }

    ///////////////////retencion
    function lista_una_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion where ret_id=$id");
        }
    }

    function lista_detalle_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_retencion where ret_id=$id");
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

////////////////////////EJECUCION FUNCIONES Y CLASES///////////////////
$Sri = new SRI();
$Set = new Set();
$ambiente = 2; //Pruebas 1    Produccion 2
$codigo = "12345678"; //Del ejemplo del SRI
$tp_emison = "1"; //Emision Normal
$pos = strpos($doc, 'HTTP ERROR'); //Verfifico Conxecion;

if ($pos == false) {
    $cns = $Set->lista_una_retencion($id);
    if (pg_num_rows($cns) > 0) {
        while ($rst = pg_fetch_array($cns)) {
            if (strlen($rst[ret_clave_acceso]) == 49) { //Si tiene clave de acceso
                $doc1 = $Sri->recupera_datos($rst[ret_clave_acceso], $ambiente);

                if (strlen($doc1[1]) == 37) {
                    ////Si recupera los datos
                    echo '1--'.$rst[ret_clave_acceso].'---'.$doc1[0] . '--' . $doc1[1] . '--' . $doc1[2] . '--' . $doc1[3] . '--' . $doc1[4] . '--' . $doc1[5];
//                    xml_sri($doc1[0], $doc1[1], $doc1[2], $doc1[3], $doc1[4], $doc1[5]);
                }
            } else { //Si no tiene clave de acceso
                $doc = envio_electronico($id, $ambiente, $codigo, $tp_emison);
            }
        }
    }
}

//function xml_sri($aut, $num_aut, $fec_aut, $ambiente, $comprobante, $sms) {
//    $xml_autorizado = "
//                    <autorizacion>
//                        <estado>$aut</estado>
//                        <numeroAutorizacion>" . $num_aut . "</numeroAutorizacion>
//                        <fechaAutorizacion>" . $fec_aut . "</fechaAutorizacion>
//                        <ambiente>$ambiente</ambiente>
//                        <comprobante><![CDATA[
//                        " . $comprobante . "
//                        ]]></comprobante>
//                        <mensajes>$sms<mensajes/>
//                    </autorizacion>
//        ";
//    echo $xml_autorizado;
//}

function envio_electronico($id, $ambiente, $codigo, $tp_emison) {
    $Set = new Set();
    $Adt = new Auditoria();
    $Sri = new SRI();
    $rst_enc = pg_fetch_array($Set->lista_una_retencion($id));
    $cns_det = $Set->lista_detalle_retencion($id);
    $ejer = pg_fetch_array($Set->lista_detalle_retencion($id));
    $emis = pg_fetch_array($Set->lista_emisor($rst_enc[emi_id]));
    if ($emis[cod_establecimiento_emisor] > 0 && $emis[cod_establecimiento_emisor] < 10) {
        $txem = '00';
    } elseif ($emis[cod_establecimiento_emisor] >= 10 && $emis[cod_establecimiento_emisor] < 100) {
        $txem = '0';
    } else {
        $txem = '';
    }
    if ($emis[cod_punto_emision] > 0 && $emis[cod_punto_emision] < 10) {
        $txpe = '00';
    } elseif ($emis[cod_punto_emision] >= 10 && $emis[cod_punto_emision] < 100) {
        $txpe = '0';
    } else {
        $txpe = '';
    }
    $ems = $txem . $emis[cod_establecimiento_emisor];
    $pt_ems = $txpe . $emis[cod_punto_emision];

    $fecha = date_format(date_create($rst_enc[gui_fecha_emision]), 'd/m/Y');

    $ndoc = explode('-', $rst_enc[ret_numero]);
    $secuencial = $ndoc[2];

    $cod_doc = "07"; //01= factura, 02=nota de credito tabla 4


    $f2 = date_format(date_create($rst_enc[ret_fecha_emision]), 'dmY');
    $dir_cliente = $Adt->sanear_string($rst_enc[ret_direccion]);
    $telf_cliente = $Adt->sanear_string($rst_enc[ret_telefono]);
    $email_cliente = $Adt->sanear_string($rst_enc[ret_email]);
    $direccion = $Adt->sanear_string($emis[dir_establecimiento_emisor]);
    $contabilidad = 'SI';
    $razon_soc_comprador = $Adt->sanear_string($rst_enc[ret_nombre]);
    $id_comprador = $rst_enc[ret_identificacion];
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
    $xml.="<comprobanteRetencion version='1.0.0' id='comprobante'>" . chr(13);
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
    $xml.="<infoCompRetencion>" . chr(13);
    $xml.="<fechaEmision>" . $fecha . "</fechaEmision>" . chr(13);
    $xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
    $xml.="<contribuyenteEspecial>" . $emis[contribuyente_especial] . "</contribuyenteEspecial>" . chr(13);
    $xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
    $xml.="<tipoIdentificacionSujetoRetenido>" . $tipo_id_comprador . "</tipoIdentificacionSujetoRetenido>" . chr(13);
    $xml.="<razonSocialSujetoRetenido>" . $razon_soc_comprador . "</razonSocialSujetoRetenido>" . chr(13);
    $xml.="<identificacionSujetoRetenido>" . $id_comprador . "</identificacionSujetoRetenido>" . chr(13);
    $xml.="<periodoFiscal>" . $ejer[dtr_ejercicio_fiscal] . "</periodoFiscal>" . chr(13);
    $xml.="</infoCompRetencion>" . chr(13);

    $xml.="<impuestos>" . chr(13);
    while ($reg_detalle = pg_fetch_array($cns_det)) {
        $xml.="<impuesto>" . chr(13);
        $xml.="<codigo>" . $reg_detalle[dtr_tipo_impuesto] . "</codigo>" . chr(13);
        $xml.="<codigoRetencion>" . $reg_detalle[dtr_codigo_impuesto] . "</codigoRetencion>" . chr(13);
        $xml.="<baseImponible>" . round($reg_detalle[dtr_base_imponible], $round) . "</baseImponible>" . chr(13);
        $xml.="<porcentajeRetener>" . round($reg_detalle[dtr_procentaje_retencion], $round) . "</porcentajeRetener>" . chr(13);
        $xml.="<valorRetenido>" . round($reg_detalle[dtr_valor], $round) . "</valorRetenido>" . chr(13);
        $xml.="<codDocSustento>0" . $rst_enc[ret_denominacion_comp] . "</codDocSustento>" . chr(13);
        $xml.="<numDocSustento>" . str_replace('-', '', $rst_enc[ret_num_comp_retiene]) . "</numDocSustento>" . chr(13);
        $xml.="<fechaEmisionDocSustento>" . $fecha . "</fechaEmisionDocSustento>" . chr(13);
        $xml.="</impuesto>" . chr(13);
    }
    $xml.="</impuestos>" . chr(13);
    $xml.="<infoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Direccion'>" . $dir_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Email'>" . strtolower(utf8_decode($email_cliente)) . "</campoAdicional>" . chr(13);
    $xml.="</infoAdicional>" . chr(13);
    $xml.="</comprobanteRetencion>" . chr(13);

    $data = '0--'.$clave . '--' . $xml;
    echo $data;
}
