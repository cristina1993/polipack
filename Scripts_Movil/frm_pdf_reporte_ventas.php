<?php
session_start();
//include_once '../Includes/permisos.php';
$desde = $_REQUEST[desde];
$hasta = $_REQUEST[hasta];
?>
<!DOCTYPE html>
<html class="ui-mobile">

    <head>
        <link href="../menu/files/jquery.css" rel="stylesheet">
        <script>
            function salir() {
                window.location = 'Lista_reporte_ventas.php';
            }

        </script>
        <style>
            /*            iframe{
                            height:95%!important;
                        }*/
        </style>

    </head>
    <body class="ui-mobile-viewport ui-overlay-c">
            <div class="ui-header ui-bar-a ui-header-fixed slidedown" data-position="fixed" data-role="header" role="banner">

                <h1 class="ui-title" role="heading" aria-level="1">REPORTE DE VENTAS</h1>
                <a class="ui-btn-left ui-btn ui-shadow ui-btn-corner-all ui-btn-up-a" data-theme="a" data-rel="back" href="#" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span">
                    <span class="ui-btn-inner ui-btn-corner-all">
                        <span class="ui-btn-text" onclick="salir()">Back</span>
                    </span>
                </a>
            </div>
            <iframe  src='../Reports/pdf_rep_ventas.php?desde=<?php echo $desde ?>&hasta=<?php echo $hasta ?>' width="100%" />           
    </body>
</html>




