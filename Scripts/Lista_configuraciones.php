<?php
include_once("../Clases/clsUsers.php");
include_once '../Includes/permisos.php';

$cns1 = $User->lista_configuraciones_recursos();
?>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Formulas</title>
        <script type="text/javascript">
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {

                    case 0://Permisos
                        frm.src = '../Scripts/Form_conf_email.php?cr=' + id;
                        break;

                }

            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function cambio_ambiente() {
                if ($('#con_ambiente1').attr('checked') == true) {
                    am = '1';
                } else {
                    am = '2';
                }
                txt = $('#correo1').val() + ';' + $('#correo2').val();

                if ($('#pago_sueldo1').attr('checked') == true) {
                    pag_sld = 0;
                } else {
                    pag_sld = 1;
                }
                if ($('#pago_he1').attr('checked') == true) {
                    pag_he = 0;
                } else {
                    pag_he = 1;
                }

                id_conf = 3;
                fec_ini = $('#inicio').val();
                fec_fnl = $('#final').val();
                id_conf1 = 4;
                fec_ini1 = '0';
                fec_fnl1 = '0';

                var data2 = Array(
                        id_conf,
                        pag_sld,
                        fec_ini,
                        fec_fnl
                        );
                var data3 = Array(
                        id_conf1,
                        pag_he,
                        fec_ini1,
                        fec_fnl1
                        );
                var data4 = Array(sueldo_basico.value);
                if (moneda.value.length == 0) {
                    $("#moneda").css({borderColor: "red"});
                    $("#moneda").focus();
                    return false;
                } else if (cantidad.value.length == 0) {
                    $("#cantidad").css({borderColor: "red"});
                    $("#cantidad").focus();
                    return false;
                }


                if (confirm('Esta seguro de ralizar los cambios?') == true) {
                    $.post("actions.php", {act: 78, id: am, data: txt, 'data2[]': data2, 'data3[]': data3, 'data4[]': data4, nom: moneda.value, usu: cantidad.value},
                    function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_configuraciones.php';
                        }
                    });
                } else {
                    return false;
                }
            }
        </script>
        <style>
            #mn286{
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
                <center class="cont_title" >LISTA DE CONFIGURACIONES GENERALES</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:right;margin-top:7px;padding:7px;" title="Guardar Cambios" onclick="cambio_ambiente()">Guardar</a></font>

<!--                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Usuario:<input type="text" name="txt" size="15" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  -->
                </center>
            </caption>
            <thead>
            <th align="left">No</th>
            <th align="left">Nombre</th>
            <th align="left">Accion</th>
            <th align="left"></th>
        </thead>
        <tbody>
            <?php
            $n = 0;
//            while ($rst = pg_fetch_array($cnsUser)) {
            $n++;
            if ($amb == 1) {
                $check1 = 'checked';
                $check2 = '';
            } else {
                $check2 = 'checked';
                $check1 = '';
            }
            $email = explode(';', $rst_am[con_correo]);
            $rst = pg_fetch_array($User->lista_conf_email());
            if (empty($rst[con_correo])) {
                $rst[con_correo] = 'No existen Credenciales Asignadas';
            } else {
                $cr = explode('&', $rst[con_correo]);
                $rst[con_correo] = $cr[0] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(' . $cr[2] . ')';
            }
            $text = "<font>$rst[con_correo]</font><img class='auxBtn' width='16px' src='../img/upd.png' onclick='auxWindow(0,8)' />";
            $rst_dec = pg_fetch_array($User->lista_configuraciones_gen('6'));
            $rst_cnt = pg_fetch_array($User->lista_configuraciones_gen('7'));
            ?>
            <tr>
                <td  align="left"><?PHP echo $n ?></td>
                <td>AMBIENTE</td>
                <td>
                    <input type="radio" name="ambiente" id="con_ambiente1"  <?php echo $check1 ?> />PRUEBAS
                    <input type="radio" name="ambiente" id="con_ambiente2" <?php echo $check2 ?> />PRODUCCION
                </td>
                <td></td>
            </tr> 
            <tr>
                <td  align="left">2</td>
                <td>DECIMALES MONEDA</td>
                <td><input type="email" size="10" id="moneda" value="<?php echo $rst_dec[con_ambiente] ?>" onkeyup="this.value = this.value.replace(/[^0-9]/, '')"></td>
                <td></td>
            </tr> 
            <tr>
                <td  align="left">2</td>
                <td>DECIMALES CANTIDAD</td>
                <td><input type="email" size="10" id="cantidad" value="<?php echo $rst_cnt[con_ambiente] ?>" onkeyup="this.value = this.value.replace(/[^0-9]/, '')"></td>
                <td></td>
            </tr> 
            <tr>
                <td  align="left">2</td>
                <td>Correos</td>
                <td><input type="email" size="40" id="correo1" value="<?php echo $email[0] ?>">&emsp;
                    <input type="email" size="40" id="correo2" value="<?php echo $email[1] ?>"></td>
                <td></td>
            </tr> 
            <tr>
                <td  align="left">3</td>
                <td>CREDENCIALES ENVIO MAIL</td>
                <td align="left" colspan="2" ><?PHP echo $text ?></td>
            </tr>
            <?php
//                }
            ?>
        </tbody>
        <thead>
        <th align="center" colspan="5">RRHH</th>
    </thead>
    <tbody>
        <?php
        $j = 0;
        while ($rst1 = pg_fetch_array($cns1)) {
            $j++;
            $op1 = $rst1[con_id];
            if ($op1 == 5) {
                $s_basico = $rst1[con_valor2];
            }
            if ($op1 >= 3 && $op1 <= 4) {
                switch ($op1) {
                    case '3':
                        if ($rst1[con_ambiente] == 0) {
                            $check1 = 'checked';
                            $check2 = '';
                        } else {
                            $check2 = 'checked';
                            $check1 = '';
                        }
                        $text = "<input type='radio' name='pago_sueldo' id='pago_sueldo1'  $check1 '/>MENSUAL
                                 <input type='radio' name='pago_sueldo' id='pago_sueldo2' $check2 '/>QUINCENAL";
                        $fecha = "INICIO<input type='text' name='inicio' id='inicio' value='$rst1[con_valor2]'>
                                  FINAL<input type='text' name='final' id='final' value='$rst1[con_valor3]'> MES";
                        break;
                    case '4':
                        if ($rst1[con_ambiente] == 0) {
                            $check3 = 'checked';
                            $check4 = '';
                        } else {
                            $check4 = 'checked';
                            $check3 = '';
                        }
                        $text = "<input type='radio' name='pago_he' id='pago_he1'  $check3 />IGUAL AL PERIODO
                                 <input type='radio' name='pago_he' id='pago_he2' $check4/>RETRAZO";
                        $fecha = "";
                        break;
                }
                echo"<tr>
                        <td>$j</td>
                        <td>$rst1[con_correo]</td>
                        <td>$text</td>
                        <td style='width: 1000px'>$fecha</td> 
                        <td></td> 
                    </tr>";
            }
        }
        ?>
        <tr>
            <td  align="left">5</td>
            <td>SUELDO BASICO</td>
            <td>
                <input type="text" name="sueldo_basico" id="sueldo_basico" value="<?php echo $s_basico ?>"/>
            </td>
            <td></td>
        </tr> 
    </tbody>
</table>
</body>
<p id="back-top" style="display: block;">
    <a href="#" >&#9650;Inicio</a>
</p>
</html>
