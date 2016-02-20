<?php
echo "okkk";
    $wsdl='https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl';
    $clave='0306201501179000787100120010010000013741234567811';
    $request=array("claveAccesoComprobante"=>$clave);
    $ws = new SoapClient($wsdl,array("exceptions" => 1, 'trace'=>1, ));
    $req = $ws->__soapCall('autorizacionComprobante',array($request));
    $a = (array) $req->RespuestaAutorizacionComprobante->autorizaciones->autorizacion;
    print_r($a);
//    $a[estado];
//    $a[numeroAutorizacion];
//    $a[fechaAutorizacion];
//    $a[ambiente];
//    $a[comprobante];
//    $a[mensajes];    
            
            
