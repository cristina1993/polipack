<?php
session_start();
include_once '../Includes/permisos.php';
$id = $_REQUEST['id'];
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
        iframe{
            height:87%!important;
        }
        html,body{
            height:100%; 
            overflow:hidden;
        }
        iframe{
            height:87%!important;
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
            <font class="cerrar" onclick="salir()" title="Salir del Formulario">&#X00d7;</font>
        </th>
    </thead>

</table>

<iframe  src='../Reports/pdf_talonario_notacredito.php?id=<?php echo $id ?>' width="100%" />           
</body>