<?php
session_start();
include_once '../Includes/permisos.php';
$txt=$_REQUEST['txt'];
$d=$_REQUEST['d'];
$h=$_REQUEST['h'];
$e=$_REQUEST['e'];
$cli=$_REQUEST['cli'];
?>
<head>
    <script>
        function salir() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
    
    </script>
<style>
   html,body{
        height:100%; 
        overflow:hidden;
    }
    iframe{
        height:87%!important;
    }
    form{
        margin-top:-10px;  
    }
    .cerrar{
        color:white; 
        cursor:pointer; 
    }
</style>
</head>
<body>
    <table style="width:100% ">
    <thead>
    <th>
        <font class="cerrar"  onclick="salir()" title="Salir del Formulario">&#X00d7;</font>  
    </th>
    </thead>
    
</table>   
    <iframe  src='../Reports/pdf_estado_pagar.php?txt=<?php echo $txt?>&d=<?php echo $d?>&h=<?php echo $h?>&e=<?php echo $e?>&cli=<?php echo $cli?>' width="100%" />           
</body>