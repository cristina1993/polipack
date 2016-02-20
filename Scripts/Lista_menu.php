<?php
include_once '../Clases/clsUsers.php';
include_once '../Includes/library.php';
$User = new User();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Menu</title>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });

            var gap = 80;
            var boxH = $(window).height() - gap;
            var boxW = $(window).width() - gap * 10;
            var boxWF = (boxH - 20);
            function auxwindow(a, id, tbl)
            {
                datos = Array();
                campos = Array();
                switch (tbl)
                {
                    case 'erp_procesos':
                        txtProc = 'Proceso';
                        campos[0] = 'emp_id';
                        campos[1] = 'proc_descripcion';
                        break;
                }
                //tbl='';         

                switch (a)
                {
                    case 0:
                        proc = prompt(txtProc);
                        datos[0] = 2;
                        datos[1] = proc;
                        if (proc != null)
                        {
                            $.post("actions.php", {mod: 1, 'datos[]': datos, 'campos[]': campos, id: id, act: 0, tbl: tbl},
                            function (dt) {
                                if (dt == 0)
                                {

                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_menu.php';
                                } else {
                                    alert(dt);
                                }
                            })
                        }
                        break;
                    case 1:
                        conf = confirm('En realidad desea eliminar este ITEM?');
                        if (conf == true)
                        {
                            $.post("actions.php", {mod: 1, id: id, act: 1, tbl: tbl},
                            function (dt) {
                                if (dt == 0)
                                {
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_menu.php';
                                } else {
                                    alert(dt);
                                }
                            })

                        }

                        break;
                }
                $.fallr.show({
                    content: '<center>...</center>'
                            + wnd,
                    width: boxW,
                    height: boxH,
                    duration: 5,
                    position: 'center',
                    buttons: {
                        button1: {
                            text: '&#X00d7;',
                            onclick: function () {
                                $.fallr.hide();
                            }
                        }
                    }
                });

            }



            function drag(parrafo, evento) {
                evento.dataTransfer.setData('id', parrafo.id);
            }

            function drop(ev, mod, mn) {
                var e = ev.dataTransfer.getData('id');
                window.location = "Lista_menu.php?e=" + e + "&mod=" + mod + "&mn=" + mn

            }

            function move(idt, ord, tp, mn, op)
            {
                if (tp == 0)
                {
                    ord = ord - 1
                } else {
                    ord = ord + 1
                }
                window.location = "Lista_menu.php?idt=" + idt + "&ord=" + ord + "&mn=" + mn + "&op=" + op
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

        </script>
        <style>
            p{
                background:#0480be;  
                color:white;
                padding: 5 5 5 5;
            }
            li{
                list-style:none;
                background:white; 
                padding: 5 5 5 5;
                border: solid 1px #0480be;
            }
            li font{
                float:right;
                padding: 0 5 0 5;
                margin-left:1px; 
                border: solid 1px #0480be;
            }
            #mn1{
                background:black;
                color:white;
                border: solid 1px white;
            }

        </style>


    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl(1, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >
                    ADMINISTRACION DE MENU
                    <img src="../img/new.png" class="auxBtn" style="float:left " onclick="auxwindow(0, 0, 'erp_procesos')" title="Nuevo Proceso" />                
                </center>
            </caption>
            <thead>
                <tr>
                    <?php
                    $cnsProc = $User->lista_todos_procesos();
                    while ($rstProc = pg_fetch_array($cnsProc)) {
                        ?>
                        <th>
                            <?php echo $rstProc[proc_descripcion]; ?>
                            <img src="../img/new.png" onclick="auxwindow(0,<?php echo $rstProc[proc_id] ?>, 'erp_modulos')" style="float:left" title="Nuevo Modulo" />
                            <img src="../img/upd.png" onclick="auxwindow(0,<?php echo $rstProc[proc_id] ?>, 'erp_procesos')"/>
                            <img src="../img/del.png" onclick="auxwindow(1,<?php echo $rstProc[proc_id] ?>, 'erp_procesos')"/>
                        </th>
                        <?php
                    }
                    ?>
                </tr>
            </thead>                
            <tbody>                
                <tr>
                    <?php
                    $cnsProc = $User->lista_todos_procesos();
                    while ($rstProc = pg_fetch_array($cnsProc)) {
                        ?>
                        <td valign="top" align="left">
                            <?php
                            $cnsMod = $User->lista_modulosby_proceso($rstProc[proc_id]);
                            while ($rstMod = pg_fetch_array($cnsMod)) {
                                ?>
                                <p><?php echo $rstMod[mod_descripcion] ?></p>
                                <?php
                                $cnsOpl = $User->lista_oplby_modulo($rstMod[mod_id]);
                                while ($rstOpl = pg_fetch_array($cnsOpl)) {
                                    ?>
                        <li>
                            <?php echo $rstOpl[opl_modulo] ?>
                            <font>&#9650;</font>
                            <font>&#9660;</font>
                        </li>
                        <?php
                    }
                    ?> 
                    <?php
                }
                ?>
            </td>
            <?php
        }
        ?>

    </tr>
</tbody>
</table>
</body>
<p id="back-top" style="display: block;">
    <a href="#" >&#9650;Inicio</a>
</p>
</html>
