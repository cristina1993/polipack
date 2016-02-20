<?php

set_time_limit(0);
date_default_timezone_set('America/Guayaquil');
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
            return pg_query("select * from erp_retencion where (char_length(ret_autorizacion)<>37 or  ret_autorizacion is null) and (ret_estado_aut is null or ret_estado_aut<>'ANULADO')  order by 1");
        }
    }

    function actualizar_datos_documentos($estado, $auto, $fecha, $xml, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_retencion
                SET ret_estado_aut='RECIBIDA $estado',
                    ret_autorizacion='$auto',
                    ret_fec_hora_aut='$fecha',
                    ret_xml_doc=''    
                WHERE ret_id='$id' ");
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
            return pg_query("update erp_retencion 
                set ret_clave_acceso='$dat[0]', 
                ret_estado_aut='$dat[1] $dat[2]', 
                ret_observacion_aut='$dat[3]', 
                ret_autorizacion='$dat[4]',
                ret_fec_hora_aut='$dat[5]',
                ret_xml_doc=''    
                where ret_id='$id' ");
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
$rst_ambiente=  pg_fetch_array($Sri->lista_ambiente());
$ambiente = $rst_ambiente[con_ambiente]; //Pruebas 1    Produccion 2
$codigo = "12345678"; //Del ejemplo del SRI
$tp_emison = "1"; //Emision Normal
$parametros = "<parametros>" .
        "<keyStore>/usr/lib/jvm/jre/lib/security/cacerts</keyStore>" .
        "<keyStorePassword>changeit</keyStorePassword>" .
        "<ambiente>" . $ambiente . "</ambiente>" .
        "<pathFirma>/var/www/FacturacionElectronica/usr006.p12</pathFirma>" .
        "<passFirma>Noperti1952</passFirma>" .
        "</parametros>";
$Set = new Set();
$doc = $Sri->recupera_datos('0605201501179000787100120010010000011161234567813', $ambiente);
$rst_load = pg_fetch_array($Sri->lista_session_sri(date('Y-m-d')));
$fnow = date('Y-m-d');
$hnow = date('H:i:s');
$date1 = date_create($fnow . " " . $hnow);
$date2 = date_create($rst_load[adt_date] . " " . $rst_load[adt_time]);
$diff = date_diff($date1, $date2);
$mins = $diff->format("%i");

//if ($mins > 3) {
//    if (!$Sri->insert_load_sri(array($_SESSION[usuario], $fnow, $hnow))) {
//        echo pg_last_error();
//    } else {
$doc = $Sri->recupera_datos('0209201501179000787100120030010000008981234567816', $ambiente);
$cns = $Sri->documentos_noenviados();
while ($rst = pg_fetch_array($cns)) {
    if (strlen($rst[ret_clave_acceso]) == 49) { //Si tiene clave de acceso
        $doc1 = $Sri->recupera_datos($rst[ret_clave_acceso], $ambiente);
        if (strlen($doc1[1]) == 37) { //Si recupera los datos
            if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[ret_id])) {
                $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[ret_clave_acceso], '', 'SuperAdmin');
                if (!$Sri->registra_errores($data)) {
                    echo pg_last_error();
                }
            }
        } else {//Si no recupera los datos.
            $doc = envio_electronico($rst[ret_id], $ambiente, $codigo, $tp_emison, $parametros);
            $dc = explode('&', $doc);
            $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfico Conxecion;
            if (strlen($dc[0]) == 49 || $err1 == true) {
                $doc1 = $Sri->recupera_datos($dc[0]);
                if (strlen($doc1[1]) == 37) {
                    if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[ret_id])) {
                        $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[clave_acceso], '', 'SuperAdmin');
                        if (!$Sri->registra_errores($data)) {
                            echo pg_last_error();
                        }
                    }
                }
            }
        }
    } else { //Si no tiene clave de acceso
        $doc = envio_electronico($rst[ret_id], $ambiente, $codigo, $tp_emison, $parametros);
        $dc = explode('&', $doc);
        $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfifico Conxecion;
        if (strlen($dc[0]) == 49 || $err1 == true) {
            $doc1 = $Sri->recupera_datos($dc[0]);
            if (strlen($doc1[1]) == 37) {
                if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[ret_id])) {
                    $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[ret_clave_acceso], '', 'SuperAdmin');
                    if (!$Sri->registra_errores($data)) {
                        echo pg_last_error();
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

    $fecha = date_format(date_create($rst_enc[ret_fecha_emision]), 'd/m/Y');

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
        If ($reg_detalle[dtr_tipo_impuesto] == 'IV') {
            $tipo = '2';
        } else if ($reg_detalle[dtr_tipo_impuesto] == 'IR') {
            $tipo = '1';
        }
        $xml.="<impuesto>" . chr(13);
        $xml.="<codigo>" . $tipo . "</codigo>" . chr(13);
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

    $comando = 'java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "' . htmlentities($xml, ENT_QUOTES, "UTF-8") . '" "' . htmlentities($parametros, ENT_QUOTES, "UTF-8") . '"';
    $dat = $clave . '&' . shell_exec($comando);
    $data = explode('&', $dat);
    
    $sms = 0;
    $env = 'Envio SRI';
    $dt0 = $Adt->sanear_string($data[0]); //Clave de acceso
    $dt1 = $Adt->sanear_string($data[1]); // Recepcion
    $dt2 = $Adt->sanear_string($data[2]); // Autorizacion
    $dt3 = $Adt->sanear_string($data[3]); // Mensaje
    $dt4 = $Adt->sanear_string($data[4]); // Numero Autorizacion
    $dt5 = $data[5];                      // Hora y fecha Autorizacion
    $dt6 = '';
    $dat = array($dt0, $dt1, $dt2, $dt3, $dt4, $dt5, $dt6);
    if (!$Sri->upd_documentos($dat, $id)) {
        $sms = pg_last_error();
        $env = 'Envio Fallido';
    }
    return $sms . '&' . $clave;
}
