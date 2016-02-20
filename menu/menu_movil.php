<?php
session_start();
include_once '../Clases/clsUsers.php';
$usuario = $_SESSION['usuid'];
$User = new User();
$cnsProc = $User->list_proceos_movil($usuario);
$cnsProc1 = $User->list_proceos_movil($usuario);
?>

<!DOCTYPE html>
<html class="ui-mobile"><head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"><!-- base href="http://taitems.github.io/iOS-Inspired-jQuery-Mobile-Theme/" --> 
        <meta charset="utf-8"> 
        <title>SCP</title> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"> 	 
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link href="files/jquery.css" rel="stylesheet">
        <script src="files/jquery-1.js"></script>
        <script>
            $(".ui-dialog button").live("click", function () {
                $("[data-role='dialog']").dialog("close");
            });
            $(document).on("mobileinit", function () {
                $.mobile.defaultPageTransition = "slide";
            });

            function submenu(mn)
            {
                window.location = mn;
            }

        </script>
        <script src="files/jquery.js"></script>
        <style>
            #footerTabs {
                background: #FFF -webkit-radial-gradient(circle, #FFF, #dee2e4);
            }
            .ui-listview sup {
                font-size: 0.6em;
                color: #cc0000;
            }
            .menu_principal{
                margin-top: 1.5px;
                padding:10px;
                border-bottom:solid 1px #ccc; 
                border-radius:5px; 
                width:93% !important; 
                background: rgba(255,255,255,1)!important;
                background: -moz-linear-gradient(left, rgba(255,255,255,1) 0%, rgba(246,246,246,1) 47%, rgba(237,237,237,1) 100%)!important;
                background: -webkit-gradient(left top, right top, color-stop(0%, rgba(255,255,255,1)), color-stop(47%, rgba(246,246,246,1)), color-stop(100%, rgba(237,237,237,1)))!important;
                background: -webkit-linear-gradient(left, rgba(255,255,255,1) 0%, rgba(246,246,246,1) 47%, rgba(237,237,237,1) 100%)!important;
                background: -o-linear-gradient(left, rgba(255,255,255,1) 0%, rgba(246,246,246,1) 47%, rgba(237,237,237,1) 100%)!important;
                background: -ms-linear-gradient(left, rgba(255,255,255,1) 0%, rgba(246,246,246,1) 47%, rgba(237,237,237,1) 100%)!important;
                background: linear-gradient(to right, rgba(255,255,255,1) 0%, rgba(246,246,246,1) 47%, rgba(237,237,237,1) 100%)!important;
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ededed', GradientType=1 )!important;
            }            
            .menu_principal a{
                color:black !important; 
                text-decoration:none; 
            }
            .menu_principal:hover{
                cursor:pointer !important; 
                color:#ccc !important; 
                text-decoration:none !important; 
                border:solid 1px #005580 !important; 
            }

        </style>

    </head> 
    <body class="ui-mobile-viewport ui-overlay-c"> 

        <div style="" class="ui-page ui-body-c ui-page-header-fixed ui-page-active" tabindex="0" data-url="/iOS-Inspired-jQuery-Mobile-Theme/" data-role="page"> 
            <div role="banner" class="ui-header ui-bar-a ui-header-fixed slidedown" data-role="header" data-position="fixed"> 
                <h1 aria-level="1" role="heading" class="ui-title">Menu Principal</h1>
            </div><!-- /header --> 
            <?php
            while ($rstProc = pg_fetch_array($cnsProc)) {
                ?>    
                <div class="menu_principal"><a  href="<?php echo '#' . $rstProc[proc_descripcion] ?>"><?php echo $rstProc[proc_descripcion] ?></a></div>                
                <?php
            }
            ?>            
        </div>


        <?php
        $n = 0;
        while ($rstProc = pg_fetch_array($cnsProc1)) {
            $n++;
            ?>    
            <div data-url="dialogs" data-role="page" id="<?php echo $rstProc[proc_descripcion] ?>">
                <div data-role="header" data-position="fixed">
                    <h1><?php echo $rstProc[proc_descripcion] ?></h1>
                    <a href="#" data-rel="back" data-theme="a">Back</a>
                </div><!-- /header --> 

                <div data-role="content"> 
                    <?php
                    $cnsMod = $User->list_modulos_movil($rstProc[proc_id], $usuario);
                    while ($rstMod = pg_fetch_array($cnsMod)) {
//                        $rstOl = pg_fetch_array($User->list_primer_opl($rstMod[mod_id], $usuario, ' limit 1'));
                        $dr = explode('/', $rstMod[opl_direccion]);
                        $dir = '../Scripts_Movil/' . $dr[1] . '.php?ol=' . $rstOl[opl_id];
                        ?>
                        <a href="#" onclick="submenu('<?php echo $dir?>')" class="ui-btn-plain" data-role="button" data-rel="dialog" data-transition="slideup"><?php echo $rstMod[opl_modulo] ?></a>
                        <?php
                    }
                    ?>
                </div>    
            </div>
            <?php
        }
        ?>        





        <div class="ui-loader ui-corner-all ui-body-a ui-loader-default"><span class="ui-icon ui-icon-loading"></span><h1>loading</h1></div>
    </body>
</html>