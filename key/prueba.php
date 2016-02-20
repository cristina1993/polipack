<?php
$parametros="<parametros>".
"<keyStore>/usr/lib/jvm/jre/lib/security/cacerts</keyStore>".
"<keyStorePassword>changeit</keyStorePassword>".
"<ambiente>1</ambiente>".
"<pathFirma>/var/www/FacturacionElectronica/usr006.p12</pathFirma>".
"<passFirma>Noperti1952</passFirma>".
"</parametros>";
$comprobante = file_get_contents('archivo.xml');
//echo htmlentities($comprobante);
echo $comando=shell_exec('java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "'.htmlentities($comprobante,ENT_QUOTES, "UTF-8").'" "'.htmlentities($parametros,ENT_QUOTES, "UTF-8").'"');
?>