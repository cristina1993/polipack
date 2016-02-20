<?php
include_once '../Includes/permisos.php';
$fec1 = date('Y-m-d');
$fec2 = date('Y-m-d');
?>
<!doctype html>
<html class="ui-mobile">
    <head>
        <meta charset='utf-8'>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Menu</title>
        <link href="../menu/files/jquery.css" rel="stylesheet">
        <script>
            $(function () {
                Calendar.setup({inputField: desde, ifFormat: '%Y-%m-%d', button: im_desde});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
            });
            function auxWindow() {
                window.location = '../Scripts_Movil/frm_pdf_reporte_ventas.php?desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val();
            }
            function fechas(op) {
                switch (op)
                {
                    case 1:
                        $('#desde').val('<?php echo $fec1 ?>');
                        $('#hasta').val('<?php echo $fec2 ?>');
                        break;
                    case 2:
                        var fecha = '<?php echo $fec1 ?>';
                        fecha = fecha.replace("-", "/").replace("-", "/");
                        fecha = new Date(fecha);
                        fecha.setDate(fecha.getDate() - 1);
                        var anio = fecha.getFullYear();
                        var mes = fecha.getMonth() + 1;
                        var dia = fecha.getDate();
                        if (mes.toString().length < 2) {
                            mes = "0".concat(mes);
                        }

                        if (dia.toString().length < 2) {
                            dia = "0".concat(dia);
                        }
                        $('#desde').val(anio + "-" + mes + "-" + dia);
                        $('#hasta').val(anio + "-" + mes + "-" + dia);
                        break;
                    case 3:
                        var fecha = '<?php echo $fec1 ?>';
                        fecha = new Date(fecha);
                        var anio = fecha.getFullYear();
                        var mes = fecha.getMonth() + 1;
                        var dia = fecha.getMonthDays();
                        if (mes.toString().length < 2) {
                            mes = "0".concat(mes);
                        }
                        $('#desde').val(anio + '-' + mes + '-01');
                        $('#hasta').val(anio + '-' + mes + '-' + dia);
                        break;
                }
                auxWindow();
            }

            function back() {
                window.location = "../menu/menu_movil.php";
            }
        </script>
        <style>
            input,select{
                border:solid 1px #ccc;
            }
            #mn185{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #desde,#hasta{
                background:#E0E0E0; 
            }
            input[type=submit]{
                width:100%;
                height:40px; 
            }
        </style>
    </head>
    <body class="ui-mobile-viewport ui-overlay-c">
        <div class="ui-page ui-body-c ui-page-header-fixed ui-page-active" data-role="page" data-url="/iOS-Inspired-jQuery-Mobile-Theme/" tabindex="0" style="min-height: 912px;"></div>
        <div id="Gerencial" class="ui-page ui-body-c ui-page-header-fixed ui-page-active" data-role="page" data-url="Gerencial" tabindex="0" style="padding-top: 44px; min-height: 912px;">
            <div class="ui-header ui-bar-a ui-header-fixed slidedown" data-position="fixed" data-role="header" role="banner">
                <h1 class="ui-title" role="heading" aria-level="1"></h1>
                <a class="ui-btn-left ui-btn ui-shadow ui-btn-corner-all ui-btn-up-a" data-theme="a" data-rel="back" href="#" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span">
                    <span class="ui-btn-inner ui-btn-corner-all">
                        <span class="ui-btn-text" onclick="back()">Back</span>
                    </span>
                </a>
            </div>
            Desde:<input type="text" id="desde" size="12" readonly style="text-align:right" value="<?php echo $fec1 ?>"/>
            <img src="../img/calendar.png" id="im_desde" />
            Hasta:<input type="text" id="hasta" size="12" readonly style="text-align:right" value="<?php echo $fec2 ?>"/>
            <img src="../img/calendar.png" id="im_hasta"/>
            <input type="submit" id="save" onclick="fechas(1)" value="Hoy"><br>
            <input type="submit" id="save" onclick="fechas(2)" value="Ayer"><br>
            <input type="submit" id="save" onclick="fechas(3)" value="Mes"><br>
            <input type="submit" id="save" onclick="auxWindow()" value="Generar">
        </div>
    </body>
</html>        


