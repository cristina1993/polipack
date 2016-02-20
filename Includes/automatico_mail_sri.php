<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo 'NOPERTI / ' . $_SESSION[usuario] ?></title>
        <script type="text/javascript" src="../js/jquery-1.6.2.min.js"></script>                
        <script type="text/javascript" src="../js/jquery.min.js"></script>   
        <link rel="shortcut icon" type="image/x-icon" href="../img/icono_dalcroze.png" />
        <script>
            $(function () {
                $('#mensaje').load('envio_mail_factura.php');
                $('#mensaje').load('envio_mail_nota_credito.php');
//                $('#mensaje').load('../Includes/envio_mail_nota_debito.php');
//                $('#mensaje').load('../Includes/envio_mail_retencion.php');
//                $('#mensaje').load('../Includes/envio_mail_guia.php');
                setInterval('contador()', 35000)    ;
            });
            function contador() {
                window.location = 'automatico_mail_sri.php';
            }

        </script>        
        <style>
            *{
                font-family: Constantia, Palatino, "Palatino Linotype", "Palatino LT STD", Georgia, serif;
                color: #416c80;
                overflow-y: scroll;
                overflow-x: hidden;
                background:#ccc; 
            }
        </style>
    </head>
    <body>
<div id="mensaje" ></div>        
    </body>
</html>
