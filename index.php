<?php
//die("Servidor en Mantenimiento");
if (isset($_REQUEST[er])) {
    $sms_er = 'Clave o Usuario Incorrectos o Usuario Bloqueado...';
} else {
    $sms_er = '';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>POLIPACK</title>
        <meta name="description" content="Index">
        <meta name="author" content="Tikva Systemas">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">   
        <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
        <script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>                
        <script type="text/javascript" src="js/jquery.min.js"></script>                
        <script>
            $(function () {
                $('#main-container').show();
                $('#index_mobil').hide();
                if ($.browser.device = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()))) {
                    $('#main-container').hide();
                    $('#index_mobil').show();
                }
                $('#pass').attr('checked', true);
                cambia_forma();
            });
            function documento_electronico(e) {
                e.preventDefault();
                if (pass.checked == true) {
                    op = 0;
                } else {
                    op = 1;
                }
                cli = identificacion.value;
                tpd = tp_doc.value;
                ndoc = n_doc.value;
                iframe_doc.src = 'Scripts/Lista_documentos_electronicos.php?op=' + op + '&cli=' + cli + '&tpd=' + tpd + '&ndoc=' + ndoc;
            }
            function cambia_forma() {
                if (pass.checked == true) {
                    cont_tipo_doc.hidden = true;
                    txt_clave.innerHTML = 'Clave:';
                } else {
                    cont_tipo_doc.hidden = false;
                    txt_clave.innerHTML = '#_Doc:';
                }
            }
            function tipo_documento(obj) {
                switch (obj.value)
                {
                    case '1':
                        n_doc.placeholder = 'Numero de Factura';
                        break;
                    case '4':
                        n_doc.placeholder = 'Numero de Nota de Credito';
                        break;
                    case '5':
                        n_doc.placeholder = 'Numero de Nota de Debito';
                        break;
                    case '6':
                        n_doc.placeholder = 'Numero de Guia de Remision';
                        break;
                    case '7':
                        n_doc.placeholder = 'Numero de Retencion';
                        break;

                }
            }
        </script>
        <style>
            select{
                margin: 1px 0 0 1px;
                padding-left: 10px;	
            }
            #sms_er {
                font-family:Arial, Helvetica, sans-serif; 
                margin: 10px 0px;
                padding:0px 5px 5px 5px;
                color: #D8000C;
            }
            body, html{
                margin-top:-8px !important; 
            }
        </style>
    </head>

    <body >

        <table align="center" style="" border="0">
            <tr><td>
                    <div id="left-container"> <!-- Left part -->
                        <div id="tags" class="container"> <!-- Tags -->
                            <div class="bar title-bar">
                                <h2>Documento Electrónico</h2>
                            </div>
                            <table border="0" style="padding-left:10px; ">
                                <tr>
                                    <td>Clave:</td>
                                    <td><input type="radio" name="opcion" id="pass" onclick="cambia_forma()" />&nbsp;&nbsp;Tipo Documento:<input type="radio" name="opcion" id="doc" onclick="cambia_forma()" /></td>
                                </tr>
                                <br/>
                                <tr>
                                    <td>Cliente:</td>
                                    <td><input type="text" name="identificacion" id="identificacion" placeholder="Cedula / Ruc" ></td>
                                </tr>
                                <tr id="cont_tipo_doc">
                                    <td>T_Doc:</td>
                                    <td>
                                        &nbsp;<select name="tp_doc" id="tp_doc" onchange="tipo_documento(this)">
                                            <option value="1">Factura</option>
                                            <option value="4">Nota Credito</option>
                                            <option value="5">Nota Debito</option>
                                            <option value="6">Guia Remision</option>
                                            <option value="7">Retencion</option>
                                        </select>                                            
                                    </td>
                                </tr>                                    
                                <tr>
                                    <td id="txt_clave">Clave:</td>
                                    <td><input type="text" name="n_doc" id="n_doc" placeholder="Numero de Factura" ></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><input type="submit" class="btn" onclick="documento_electronico(event)" value="Obtener" id="obtener" /></td>
                                    <!--<td><a href="#" class="btn" id="obtener" name="obtener" onclick="documento_electronico()">Obtener</a></td>-->
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <td>
                    <div id="left-container"> <!-- Left part -->
                        <div id="tags" class="container" > <!-- Tags -->
                            <img class="img_logo" width="300px" src="" />
                        </div>
                    </div>
                </td>
                <td>
                    <div id="left-container"> <!-- Left part -->
                        <div id="tags" class="container"> <!-- Tags -->
                            <div class="bar title-bar">
                                <h2>Ingreso al Sistema</h2>
                            </div>
                            <form method="post" id="frm_login" action="Validate/userValidate.php" autocomplete="off">
                                <table border="0" style="padding-left:10px;padding-top:20px  ">
                                    <tr><td>Usuario:</td><td><input type="text" name="user" id="user"></td></tr>
                                    <tr><td>Clave:</td><td><input type="password" name="pass" id="pass"></td></tr>
                                    <tr><td colspan="2" id="sms_er" >&nbsp;<?php echo $sms_er ?></td></tr>
                                    <tr><td colspan="2">
                                            <input type="submit" class="btn" value="Entrar" id="enter" />
                                            <input type="hidden" name="device" id="device" value="0" />
                                        </td></tr>
                                </table>
                            </form>                                
                        </div>
                    </div>
                </td></tr>
            <tr><td colspan="3" >
                    <div id="left-container"> <!-- Left part -->
                        <div class="bar title-bar" style="height:50px; ">
                            <h2 style="text-align:center ">DOCUMENTOS IMPORTANTES.</h2>
                        </div>
                    </div>
                </td></tr>
            <tr>
                <td colspan="3" >
                    <div id="left-container"> <!-- Left part -->
                        <div id="frm_set" >
                            <iframe id="iframe_doc"  width="100%" height="100%" frameborder="0" ></iframe>
                        </div>
                        
                    </div>
                </td>
            </tr>
            
            <tr>
                <td colspan="3" align="right" class="foot" >Derechos Reservados  Tivka Systems <a href="http://www.tivkasystem.com/">www.tivkasystems.com</a></td>
            </tr>


        </table>                        
        <table align="left"  border="0" id="index_mobil">
            <tr>
                <td align="center">
                    <img src="img/logo_noperti.jpg" width="80%" />
                </td>
            </tr>
            <tr>
                <td>
                    <div id="left-container"> <!-- Left part -->
                        <div id="tags" class="container"> <!-- Tags -->
                            <div class="bar title-bar">
                                <h2>Ingreso al Sistema</h2>
                            </div>
                            <form method="post" id="frm_login" action="Validate/userValidate.php" autocomplete="off">
                                <table border="0" style="padding-left:10px;padding-top:20px  ">
                                    <tr><td>Usuario:</td><td><input type="text" name="user" id="user"></td></tr>
                                    <tr><td>Clave:</td><td><input type="password" name="pass" id="pass"></td></tr>
                                    <tr><td colspan="2" id="sms_er" >&nbsp;<?php echo $sms_er ?></td></tr>
                                    <tr><td colspan="2">
                                            <input type="submit" class="btn" value="Entrar" id="enter" />
                                            <input type="hidden" name="device" id="device" value="1" />
                                        </td></tr>
                                </table>
                            </form>                                
                        </div>
                    </div>
                </td></tr>
        </table>                        

    </body>
</html>
