<?php

require "../mailer/class.phpmailer.php";

class Mail extends PHPMailer {

    function envia_correo($mail, $name, $file, $ndoc, $tp) {
        include_once '../Clases/clsUsers.php';
        $User = new User();
        $rst = pg_fetch_array($User->lista_conf_email());
        $dat = explode('&', $rst[con_correo]);
        switch ($tp) {
            case 1:$doc = 'Factura';
                break;
            case 4:$doc = 'Nota de Credito';
                break;
            case 5:$doc = 'Nota de Debito';
                break;
            case 6:$doc = 'Guia de Remision';
                break;
            case 7:$doc = 'Retencion';
                break;
        }
        $sms = 0;
        $this->PluginDir = "../mailer/";
        $this->Mailer = "smtp";
        $this->SMTPAuth = true;
        $this->IsHTML(true);
        $this->IsSMTP();
//NOPERTI ******************************************************************************
        $this->SMTPSecure = "$dat[0]";
        $this->Port = $dat[1];
        $this->Host = "$dat[2]";
        $this->Username = "$dat[3]";
        $this->Password = "$dat[4]";
        $this->From = "$dat[3]";
//*************************************************************************************
        $mls = explode(';', $mail);
        $x = 0;
        while ($x < count($mls)) {
            $this->AddAddress($mls[$x], $name);
            $x++;
        }
        $this->FromName = "$dat[5]";
        $this->AddBCC('gabokatz@hotmail.com', 'gabokatz');
       
        $this->Subject = $doc . " Grupo Noperti // Cliente : " . $name . " No: " . $ndoc;
        $this->AddEmbeddedImage('../img/noperti_logo.jpg', 'logo_cli');
        $this->AddEmbeddedImage('../img/tikva_logo.jpg', 'logo_tivka');
        $mensaje = "<html>
            <body>
            <table>
            <tr><td><img src='cid:logo_cli' /></td></tr>
            <tr><td>Estimado cliente</td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td>Es un gusto recordarle que NOPERTI CIA.LITDA, por " . utf8_decode('disposición') . " del SRI, " . utf8_decode('está') . " emitiendo  todas sus facturas de manera " . utf8_decode('electrónica') . " adjuntas en este correo, <br>
            por lo que usted puede ingresar a nuestro portal web www.gruponoperti.com para descargar su factura.<br>
            Gracias por confiar en nosotros.<br><br><br>
            Este mail no debe ser respondido, para cualquier " . utf8_decode('información') . " llame al 022449696 con la " . utf8_decode('señora') . " Raquel " . utf8_decode('Cedeño') . " <br>
            " . utf8_decode('ó') . "  escribanos al email  rcedeno@gruponoperti.com
            </td></tr>
            <tr><td><br><br><br><br><br>
            <img src='cid:logo_tivka' />
            <br>Derechos Reservados TIKVASYSTEMS https://www.tivkasystem.com/</td></tr>
            </table>
            </body>
            </html>";

        $this->MsgHTML($mensaje);
        $this->AltBody = utf8_decode("Estimado cliente,
                          Es un gusto recordarle que NOPERTI CIA.LITDA, por disposición del SRI,  está emitiendo  todas sus facturas de manera electrónica  adjuntas en este correo, por lo que usted puede ingresar a nuestro portal web www.gruponoperti.com para descargar su factura.
                          Gracias por confiar en nosotros.

                          Este mail no debe ser respondido, para cualquier información llame al 022449696 con la señora Raquel Cedeño  ó escribanos al email  rcedeno@gruponoperti.com  con copia a  jsanchez@gruponoperti.com
                          
                          Favor Emitir la Retención en los proximos cinco dias de conformidad al ART 50 de la Ley de Regimen Tributario Interno ");
        $n = 0;
        while ($n < count($file)) {
            $this->AddAttachment($file[$n]);
            $n++;
        }

        if (!$this->Send()) {
            $sms = $this->ErrorInfo;
        }
        return $sms;
    }

}

?>
