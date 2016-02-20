<?php
session_start();
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
            return pg_query("select * from erp_guia_remision where (char_length(gui_autorizacion)<>37 or  gui_autorizacion is null) and gui_sts<>1 ");
        }
    }

    function actualizar_datos_documentos($estado, $auto, $fecha, $xml, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_guia_remision
                SET gui_estado_aut='RECIBIDA $estado',
                    gui_autorizacion='$auto',
                    gui_fec_hora_aut='$fecha',
                    gui_xml_doc=''    
                WHERE gui_numero='$id'");
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
            return pg_query("update erp_guia_remision
                set gui_clave_acceso='$dat[0]', 
                gui_estado_aut='$dat[1] $dat[2]', 
                gui_observacion_aut='$dat[3]', 
                gui_autorizacion='$dat[4]',
                gui_fec_hora_aut='$dat[5]',
                gui_xml_doc=''    
                where gui_id='$id'");
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

//    function lista_emisor_ruc($ruc) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select * FROM  erp_emisor where emi_identificacion='$ruc' ");
//        }
//    }
    ///////////////////guia
    function lista_una_guia($id, $t) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_guia_remision g, transportista t, erp_i_cliente c  where g.tra_id=t.id and g.gui_id='$id' and g.cli_id=c.cli_id");
        }
    }

    function lista_detalle_guia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_guia where gui_id='$id'");
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


////////////////////////EJECUCION FUNCIONES Y CLASES///////////////////
$Set = new Set();
$rst_load = pg_fetch_array($Sri->lista_session_sri(date('Y-m-d')));
$fnow = date('Y-m-d');
$hnow = date('H:i:s');
$date1 = date_create($fnow . " " . $hnow);
$date2 = date_create($rst_load[adt_date] . " " . $rst_load[adt_time]);
$diff = date_diff($date1, $date2);
$mins = $diff->format("%i");
//if ($mins > 1) {
//    if (!$Sri->insert_load_sri(array($_SESSION[usuario], $fnow, $hnow))) {
//        echo pg_last_error();
//    } else {
        $cns = $Sri->documentos_noenviados();
        while ($rst = pg_fetch_array($cns)) {
            if (strlen($rst[gui_clave_acceso]) == 49) { //Si tiene clave de acceso
                $doc1 = $Sri->recupera_datos($rst[clave_acceso], $ambiente);
                if (strlen($doc1[1]) == 37) { //Si recupera los datos
                    if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[gui_id])) {
                        $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[gui_clave_acceso], '', 'SuperAdmin');
                        if (!$Sri->registra_errores($data)) {
                            echo pg_last_error();
                        }
                    }
                } else {//Si no recupera los datos.
                    $doc = envio_electronico($rst[gui_id], $ambiente, $codigo, $tp_emison, $parametros);
                    $dc = explode('&', $doc);
                    $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfico Conxecion;
                    if (strlen($dc[0]) == 49 || $err1 == true) {
                        $doc1 = $Sri->recupera_datos($dc[0]);
                        if (strlen($doc1[1]) == 37) {
                            if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[gui_id])) {
                                $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[gui_clave_acceso], '', 'SuperAdmin');
                                if (!$Sri->registra_errores($data)) {
                                    echo pg_last_error();
                                }
                            }
                        }
                    }
                }
            } else { //Si no tiene clave de acceso
                $doc = envio_electronico($rst[gui_id], $ambiente, $codigo, $tp_emison, $parametros);
                $dc = explode('&', $doc);
                $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfifico Conxecion;
                if (strlen($dc[0]) == 49 || $err1 == true) {
                    $doc1 = $Sri->recupera_datos($dc[0]);
                    if (strlen($doc1[1]) == 37) {
                        if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[gui_id])) {
                            $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[gui_clave_acceso], '', 'SuperAdmin');
                            if (!$Sri->registra_errores($data)) {
                                echo pg_last_error();
                            }
                        }
                    }
                }
            }
        }
  //  }
//}

function envio_electronico($id, $ambiente, $codigo, $tp_emison, $parametros) {
    $Set = new Set();
    $Adt = new Auditoria();
    $Sri = new SRI();
    $rst_enc = pg_fetch_array($Set->lista_una_guia($id));
    $cns_det = $Set->lista_detalle_guia($id);
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

    $ndoc = explode('-', $rst_enc[gui_numero]);
    $secuencial = $ndoc[2];
    $cod_doc = "06"; //01= factura, 02=nota de credito tabla 4
    $f2 = date_format(date_create($rst_enc[gui_fecha_emision]), 'dmY');

    $dir_cliente = $Adt->sanear_string($rst_enc[cli_calle_prin]);
    $telf_cliente = $Adt->sanear_string($rst_enc[cli_telefono]);
    $email_cliente = $Adt->sanear_string($rst_enc[cli_email]);
    $direccion = $Adt->sanear_string($emis[dir_establecimiento_emisor]);
    $contabilidad = "SI";
    $razon_soc_comprador = $Adt->sanear_string($rst_enc[gui_nombre]);
    $id_comprador = $rst_enc[gui_identificacion];
    if (strlen($id_comprador) == 13 && $id_comprador != '9999999999999') {
        $tipo_id_comprador = "04"; //RUC 04 
    } else if (strlen($id_comprador) == 10) {
        $tipo_id_comprador = "05"; //CEDULA 05 
    } else if ($id_comprador == '9999999999999') {
        $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
    } else {
        $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
    }
    $id_trans = $rst_enc[gui_identificacion_transp];
    if (strlen($id_trans) == 13 && $id_trans != '9999999999999') {
        $tipo_id_trans = "04"; //RUC 04 
    } else if (strlen($id_trans) == 10) {
        $tipo_id_trans = "05"; //CEDULA 05 
    } else if ($id_trans == '9999999999999') {
        $tipo_id_trans = "07"; //VENTA A CONSUMIDOR FINAL
    } else {
        $tipo_id_trans = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
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
    $xml.="<guiaRemision version='1.1.0' id='comprobante'>" . chr(13);
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
    $xml.="<infoGuiaRemision>" . chr(13);
    $xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
    $xml.="<dirPartida>" . $Adt->sanear_string($rst_enc[gui_punto_partida]) . "</dirPartida>" . chr(13);
    $xml.="<razonSocialTransportista>" . $Adt->sanear_string($rst_enc[razon_social]) . "</razonSocialTransportista>" . chr(13);
    $xml.="<tipoIdentificacionTransportista>" . $tipo_id_trans . "</tipoIdentificacionTransportista>" . chr(13);
    $xml.="<rucTransportista>" . $rst_enc[identificacion] . "</rucTransportista>" . chr(13);
    $xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
    $xml.="<contribuyenteEspecial>636</contribuyenteEspecial>" . chr(13);
    $f_ini = date_format(date_create($rst_enc[gui_fecha_inicio]), 'd/m/Y');
    $f_fin = date_format(date_create($rst_enc[gui_fecha_fin]), 'd/m/Y');
    $xml.="<fechaIniTransporte>$f_ini</fechaIniTransporte>" . chr(13);
    $xml.="<fechaFinTransporte>$f_fin</fechaFinTransporte>" . chr(13);
    $xml.="<placa>$rst_enc[placa]</placa>" . chr(13);
    $xml.="</infoGuiaRemision>" . chr(13);

    $xml.="<destinatarios>" . chr(13);
    $xml.="<destinatario>" . chr(13);
    $xml.="<identificacionDestinatario>" . $id_comprador . "</identificacionDestinatario>" . chr(13);
    $xml.="<razonSocialDestinatario>" . $razon_soc_comprador . "</razonSocialDestinatario>" . chr(13);
    $xml.="<dirDestinatario>" . $dir_cliente . "</dirDestinatario>" . chr(13);
    $xml.="<motivoTraslado>" . $rst_enc[gui_motivo_traslado] . "</motivoTraslado>" . chr(13);
    if ($rst_enc[gui_doc_aduanero] != '') {
        $xml.="<docAduaneroUnico>" . $rst_enc[gui_doc_aduanero] . "</docAduaneroUnico>" . chr(13);
    }
    $xml.="<codEstabDestino>" . $rst_enc[gui_cod_establecimiento] . "</codEstabDestino>" . chr(13);
    $xml.="<codDocSustento>0" . $rst_enc[gui_denominacion_comp] . "</codDocSustento>" . chr(13);
    $xml.="<numDocSustento>" . $rst_enc[gui_num_comprobante] . "</numDocSustento>" . chr(13);
    if ($rst_enc[gui_aut_comp] != '') {
        $xml.="<numAutDocSustento>" . $rst_enc[gui_aut_comp] . "</numAutDocSustento>" . chr(13);
    }
    $fec_comp = date_format(date_create($rst_enc[gui_fecha_comp]), 'd/m/Y');
    $xml.="<fechaEmisionDocSustento>" . $fec_comp . "</fechaEmisionDocSustento>" . chr(13);
    $xml.="<detalles>" . chr(13);
    while ($reg_detalle = pg_fetch_array($cns_det)) {
        $xml.="<detalle>" . chr(13);
        $xml.="<codigoInterno>" . $reg_detalle[dtg_codigo] . "</codigoInterno>" . chr(13);
        if ($reg_detalle[dtg_cod_aux] != 0 && $reg_detalle[dtg_cod_aux] != '') {
            $xml.="<codigoAdicional>" . $reg_detalle[dtg_cod_aux] . "</codigoAdicional>" . chr(13);
        }
        $xml.="<descripcion>" . $reg_detalle[dtg_descripcion] . "</descripcion>" . chr(13);
        $xml.="<cantidad>" . round($reg_detalle[dtg_cantidad], $round) . "</cantidad>" . chr(13);
        $xml.="</detalle>" . chr(13);
    }
    $xml.="</detalles>" . chr(13);
    $xml.="</destinatario>" . chr(13);
    $xml.="</destinatarios>" . chr(13);
    $xml.="<infoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Direccion'>" . $dir_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Email'>" . strtolower(utf8_decode($email_cliente)) . "</campoAdicional>" . chr(13);
    $xml.="</infoAdicional>" . chr(13);
    $xml.="</guiaRemision>" . chr(13);

    $comando = 'java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "' . htmlentities($xml, ENT_QUOTES, "UTF-8") . '" "' . htmlentities($parametros, ENT_QUOTES, "UTF-8") . '"';
    $dat = $clave . '&' . shell_exec($comando);
    $data = explode('&', $dat);
    $sms = 0;
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
    }
    return $sms . '&' . $clave;
}
