<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cliente.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$cns_cp = $Set->lista_capacidad_de_compra();
$Clase_cliente = new Clase_cliente();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Clase_cliente->lista_un_cliente($id));
    $cns = $Clase_cliente->lista_direccion_cliid($id);
    $fila = pg_numrows($cns);
    $x = $_GET[x];
} else {
    $id = 0;
    $fila = "0";
    $rst['cli_cup_maximo'] = 0;
    $rst['cli_sueldo'] = 0;
    $rst['cli_ingresos'] = 0;
    $rst['cli_total_ingresos'] = 0;
    $rst['cli_total_gastos'] = 0;
    $rst['cli_con_sueldo'] = 0;
    $rst['cli_con_ingresos'] = 0;
    $rst['cli_con_total_ingresos'] = 0;
    $rst['cli_con_total_gastos'] = 0;
    $rst['cli_valor_arriendo'] = 0;
    $rst['cli_refc_credito1'] = 0;
    $rst['cli_refc_credito2'] = 0;
}
$rst_user = pg_fetch_array($User->listUnUsuario($_SESSION[usuid]));
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            var id =<?php echo $id ?>;
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    save(id);
                });

                Calendar.setup({inputField: "cli_fecha", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "cli_fecha_nac", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                if (id == 0) {
                    $('#cli_fecha').val('<?php echo date('Y-m-d'); ?>');
                    $('#cli_fecha_nac').val('<?php echo date('Y-m-d'); ?>');
                    cli_categoria1.checked = true;
                    cli_retencion1.checked = true;
                    cli_credito1.checked = true;
                    cli_estado_civil1.checked = true;
                    cli_tipo_vivienda1.checked = true;
                    cli_propia1.checked = true;
                    //$('#cli_tipo_actividad1').attr('checked', true);
                    natural();
                    arrienda_no();
                    $('#tbdirentrega').show();
                    est_civil1();
                }

                $('#add_row').click(function (e) {
                    e.preventDefault();
                    add();
                });

                usu_pc = '<?php echo $rst_user[usu_pc] ?>';

//if(usu_pc!=2){
//   proveedor();
//   cod();
//}

            });
            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    //this.lang = x;
                    if (parts[1] == 'cde_referencia') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] != 'cde_referencia') {
                        this.value = '';
                        this.lang = x;
                    }

                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    ;
                    return parts[1] + x;
                });
                $(table).find("tbody tr:last").after(tr);
                $('#cde_local' + x).focus();
            }
            ;
            function save(id) {
                if (cli_categoria1.checked == true) {
                    cat = 1;
                } else {
                    cat = 2;
                }
                if (cli_retencion1.checked == true) {
                    ret = 1;
                } else {
                    ret = 2;
                }
                if (cli_credito1.checked == true) {
                    cre = 1;
                } else {
                    cre = 2;
                }

                if (cli_estado_civil1.checked == true) {
                    est = 1;
                } else if (cli_estado_civil2.checked == true) {
                    est = 2;
                } else if (cli_estado_civil3.checked == true) {
                    est = 3;
                } else if (cli_estado_civil4.checked == true) {
                    est = 4;
                } else if (cli_estado_civil5.checked == true) {
                    est = 5;
                }

                if (cli_tipo_vivienda1.checked == true) {
                    viv = 1;
                } else if (cli_tipo_vivienda2.checked == true) {
                    viv = 2;
                } else if (cli_tipo_vivienda3.checked == true) {
                    viv = 3;
                } else if (cli_tipo_vivienda4.checked == true) {
                    viv = 4;
                } else if (cli_tipo_vivienda5.checked == true) {
                    viv = 5;
                } else if (cli_tipo_vivienda6.checked == true) {
                    viv = 6;
                }

                if (cli_tipo_actividad1.checked == true) {
                    a = 1;
                } else {
                    a = 0;
                }
                if (cli_tipo_actividad2.checked == true) {
                    b = 2;
                } else {
                    b = 0;
                }
                if (cli_tipo_actividad3.checked == true) {
                    c = 3;
                } else {
                    c = 0;
                }
                if (cli_tipo_actividad4.checked == true) {
                    d = 4;
                } else {
                    d = 0;
                }
                if (cli_tipo_actividad5.checked == true) {
                    e = 5;
                } else {
                    e = 0;
                }
                if (cli_tipo_actividad6.checked == true) {
                    f = 6;
                } else {
                    f = 0;
                }
                if (cli_tipo_actividad7.checked == true) {
                    g = 7;
                } else {
                    g = 0;
                }
                if (cli_tipo_actividad8.checked == true) {
                    h = 8;
                } else {
                    h = 0;
                }
                cli_tipo_actividad = a + ',' + b + ',' + c + ',' + d + ',' + e + ',' + f + ',' + g + ',' + h;
                if (cli_propia1.checked == true) {
                    pro = 1;
                } else {
                    pro = 2;
                }

                if (cli_cup_maximo.value.length == 0) {
                    cup = 0;
                } else {
                    cup = cli_cup_maximo.value;
                }
                if (cli_valor_arriendo.value.length == 0) {
                    arriendo = 0;
                } else {
                    arriendo = cli_valor_arriendo.value;
                }
                if (cli_con_sueldo.value.length == 0) {
                    consueldo = 0;
                } else {
                    consueldo = cli_con_sueldo.value;
                }
                if (cli_sueldo.value.length == 0) {
                    sueldo = 0;
                } else {
                    sueldo = cli_sueldo.value;
                }
                if (cli_ingresos.value.length == 0) {
                    ingresos = 0;
                } else {
                    ingresos = cli_ingresos.value;
                }
                if (cli_total_gastos.value.length == 0) {
                    gastos = 0;
                } else {
                    gastos = cli_total_gastos.value;
                }
                if (cli_con_ingresos.value.length == 0) {
                    coningresos = 0;
                } else {
                    coningresos = cli_con_ingresos.value;
                }
                if (cli_con_total_gastos.value.length == 0) {
                    congastos = 0;
                } else {
                    congastos = cli_con_total_gastos.value;
                }
                if (cli_refc_credito1.value.length == 0) {
                    credito1 = 0;
                } else {
                    credito1 = cli_refc_credito1.value;
                }
                if (cli_refc_credito2.value.length == 0) {
                    credito2 = 0;
                } else {
                    credito2 = cli_refc_credito2.value;
                }
                var data = Array(cli_fecha.value,
                        cli_tipo.value,
                        cat,
                        cli_codigo.value,
                        cli_estado.value,
                        cli_apellidos.value,
                        cli_nombres.value,
                        cli_ced_ruc.value,
                        cli_raz_social.value,
                        cli_nom_comercial.value,
                        ret,
                        cre,
                        cup,
                        cli_cat_cliente.value,
                        cli_nacionalidad.value,
                        cli_lugar_nac.value,
                        cli_fecha_nac.value,
                        est,
                        cli_con_cedula.value,
                        cli_con_apellido_paterno.value,
                        cli_con_apellido_materno.value,
                        cli_con_nombres.value,
                        viv,
                        arriendo,
                        cli_pais.value,
                        cli_provincia.value,
                        cli_canton.value,
                        cli_parroquia.value,
                        cli_calle_prin.value,
                        cli_numeracion.value,
                        cli_calle_sec.value,
                        cli_tiempo_residencia.value,
                        cli_telefono.value,
                        cli_email.value,
                        cli_referencia.value,
                        cli_tipo_actividad,
                        cli_empresa.value,
                        cli_actividad.value,
                        pro,
                        cli_cargo.value,
                        cli_tiempo_trab.value,
                        cli_actividad_telefono.value,
                        cli_actividad_celular.value,
                        cli_direccion_trabajo.value,
                        sueldo,
                        ingresos,
                        gastos,
                        consueldo,
                        coningresos,
                        congastos,
                        cli_ref_apellidos1.value,
                        cli_ref_nombres1.value,
                        cli_ref_parentesco1.value,
                        cli_ref_telefono1.value,
                        cli_ref_apellidos2.value,
                        cli_ref_nombres2.value,
                        cli_ref_parentesco2.value,
                        cli_ref_telefono2.value,
                        cli_rep_apellidos.value,
                        cli_rep_nombres.value,
                        cli_rep_telefono.value,
                        cli_rep_celular.value,
                        cli_rep_email.value,
                        cli_cont_apellidos.value,
                        cli_cont_nombres.value,
                        cli_cont_telefono.value,
                        cli_cont_celular.value,
                        cli_cont_email.value,
                        cli_refc_empresa1.value,
                        credito1,
                        cli_refc_telefono1.value,
                        cli_refc_empresa2.value,
                        credito2,
                        cli_refc_telefono2.value,
                        cli_refb_institucion1.value,
                        cli_refb_cuenta1.value,
                        cli_refb_tip_cuenta1.value,
                        cli_refb_institucion2.value,
                        cli_refb_cuenta2.value,
                        cli_refb_tip_cuenta2.value,
                        cli_tipo_cliente.value);
                var data1 = Array();
                doc = document.getElementsByClassName('itm');
                a = doc.length;
                n = 0;
                var tr = $('#tbl_form').find("tbody tr:last");
                var a = tr.find("input").attr("id");
                var i = a.substring(4, 5);
                while (n < i) {
                    n++;
                    loc = $('#cde_local' + n).val();
                    ape = $('#cde_apellido' + n).val();
                    nom = $('#cde_nombre' + n).val();
                    tel = $('#cde_telefono' + n).val();
                    pais = $('#cde_pais' + n).val();
                    pro = $('#cde_provincia' + n).val();
                    can = $('#cde_canton' + n).val();
                    par = $('#cde_parroquia' + n).val();
                    cp = $('#cde_calle_prin' + n).val();
                    num = $('#cde_numero' + n).val();
                    cs = $('#cde_calle_sec' + n).val();
                    ref = $('#cde_referencia' + n).val();
                    data1.push(loc + '&' +
                            ape + '&' +
                            nom + '&' +
                            tel + '&' +
                            pais + '&' +
                            pro + '&' +
                            can + '&' +
                            par + '&' +
                            cp + '&' +
                            num + '&' +
                            cs + '&' +
                            ref
                            );
                }
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        var n = 0;
                        var tr = $('#tbl_form').find("tbody tr:last");
                        var a = tr.find("input").attr("id");
                        var i = a.substring(4, 5);
                        if (cli_tipo.value == "") {
                            $("#cli_tipo").css({borderColor: "red"});
                            $("#cli_tipo").focus();
                            return false;
                        }
                        if (cli_categoria1.checked == true) {
                            if (cli_apellidos.value.length == 0) {
                                $("#cli_apellidos").css({borderColor: "red"});
                                $("#cli_apellidos").focus();
                                return false;
                            }
                            else if (cli_nombres.value.length == 0) {
                                $("#cli_nombres").css({borderColor: "red"});
                                $("#cli_nombres").focus();
                                return false;
                            }
                        }
                        if (cli_ced_ruc.value.length == 0) {
                            $("#cli_ced_ruc").css({borderColor: "red"});
                            $("#cli_ced_ruc").focus();
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_cliente.php',
                    data: {op: 0, 'data[]': data, 'data1[]': data1, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_cliente.php';
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
            }

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function eliminar_fila(obj) {

                var parent = $(obj).parents();
                var tr = $(parent[0]).find("input").attr("id");
                var i = tr.substring(4, 5);
                if (i == '1') {
                    $('#cde_local1').val("");
                    $('#cde_apellido1').val("");
                    $('#cde_nombre1').val("");
                    $('#cde_telefono1').val("");
                    $('#cde_pais1').val("");
                    $('#cde_provincia1').val("");
                    $('#cde_canton1').val("");
                    $('#cde_parroquia1').val("");
                    $('#cde_calle_prin1').val("");
                    $('#cde_numero1').val("");
                    $('#cde_calle_sec1').val("");
                    $('#cde_referencia1').val("");
                } else {
                    $(parent[0]).remove();
                }
            }
            function natural() {
                //cli_tipo_actividad1.checked = true;
                cli_categoria1.checked = true;
                cli_retencion1.checked = true;
                cli_credito1.checked = true;
                cli_estado_civil1.checked = true;
                cli_tipo_vivienda1.checked = true;
                cli_propia1.checked = true;
                $('#lbldatgenerales').show();
                $('#tdapellidos').show();
                $('#tdnombres').show();
                $('#cliapellidos').show();
                $('#clinombres').show();
                $('#lblapellidos').show();
                $('#cli_apellidos').show();
                $('#lblnombres').show();
                $('#cli_nombres').show();
                $('#comercial1').hide();
                $('#comercial2').hide();
                $('#thpersonales').show();
                $('.tbpersonales').show();
                $('#lblresidencia').show();
                $('#lblpais').show();
                $('#cli_pais').show();
                $('#lblProvincia').show();
                $('#cli_provincia').show();
                $('#lblcanton').show();
                $('#cli_canton').show();
                $('#lblparroquia').show();
                $('#cli_parroquia').show();
                $('#lblcalleprin').show();
                $('#cli_calle_prin').show();
                $('#lblnumeracion').show();
                $('#cli_numeracion').show();
                $('#lblcallesec').show();
                $('#cli_calle_sec').show();
                $('#lbltieresidencia').show();
                $('#cli_tiempo_residencia').show();
                $('#lbltelefono').show();
                $('#cli_telefono').show();
                $('#lblemail').show();
                $('#cli_email').show();
                $('#lblreferencia').show();
                $('#cli_referencia').show();
                $('#lbllocal').hide();
                $('#lbllocpropio').hide();
                $('#cli_tipo_vivienda4').hide();
                $('#lbllocarriendo').hide();
                $('#cli_tipo_vivienda5').hide();
                $('#cli_valor_arriendo').hide();
                $('#lblvalor').hide();
                $('#lbllocotros').hide();
                $('#cli_tipo_vivienda6').hide();
                $('#thdatos').show();
                $('.datos').show();
                $('#thdatos2').show();
                $('#tbdatos2').show();
                $('#thdatos3').show();
                $('#tbdatos3').show();
                $('#thactividad').show();
                $('.tbactividad').show();
                $('#threferencias').show();
                $('#tbreferencias').show();
                $('#lblcomercial').hide();
                $('#cli_nom_comercial').hide();
                $('#lblmatriz').hide();
                $('#threpresentante').hide();
                $('.tbrepresentante').hide();
                $('#thcontacto').hide();
                $('.tbcontacto').hide();
                $('#thcomerciales').hide();
                $('.tbcomerciales').hide();
                $('#thbancarias').hide();
                $('.tbbancarias').hide();
                limpiar_natural();
            }

            function juridica() {
                cli_tipo_actividad1.checked = false;
                cli_tipo_vivienda1.checked = false;
                cli_tipo_vivienda4.checked = true;
                $('#lbldatgenerales').show();
                $('#tdapellidos').hide();
                $('#tdnombres').hide();
                $('#cliapellidos').hide();
                $('#clinombres').hide();
                $('#lblapellidos').hide();
                $('#cli_apellidos').hide();
                $('#lblnombres').hide();
                $('#cli_nombres').hide();
                $('#thpersonales').hide();
                $('.tbpersonales').hide();
                $('#cli_valor_arriendo').show();
                $('#lblresidencia').hide();
                $('#lblpais').show();
                $('#cli_pais').show();
                $('#lblProvincia').show();
                $('#cli_provincia').show();
                $('#lblcanton').show();
                $('#cli_canton').show();
                $('#lblparroquia').show();
                $('#cli_parroquia').show();
                $('#lblcalleprin').show();
                $('#cli_calle_prin').show();
                $('#lblnumeracion').show();
                $('#cli_numeracion').show();
                $('#lblcallesec').show();
                $('#cli_calle_sec').show();
                $('#lbltieresidencia').hide();
                $('#cli_tiempo_residencia').hide();
                $('#lbltelefono').show();
                $('#cli_telefono').show();
                $('#lblemail').show();
                $('#cli_email').show();
                $('#lblreferencia').show();
                $('#cli_referencia').show();
                $('#lbllocal').show();
                $('#lbllocpropio').show();
                $('#cli_tipo_vivienda4').show();
                $('#lbllocarriendo').show();
                $('#cli_tipo_vivienda5').show();
                $('#lbllocotros').show();
                $('#cli_tipo_vivienda6').show();
                $('#thdatos').hide();
                $('.datos').hide();
                $('#tbdatos2').hide();
                $('#thdatos2').hide();
                $('#tbdatos2').hide();
                $('#thdatos3').hide();
                $('#tbdatos3').hide();
                $('#thactividad').hide();
                $('.tbactividad').hide();
                $('#threferencias').hide();
                $('#tbreferencias').hide();
                $('#lblcomercial').show();
                $('#comercial1').show();
                $('#comercial2').show();
                $('#cli_nom_comercial').show();
                $('#lblmatriz').show();
                $('#threpresentante').show();
                $('.tbrepresentante').show();
                $('#thcontacto').show();
                $('.tbcontacto').show();
                $('#thcomerciales').show();
                $('.tbcomerciales').show();
                $('#thbancarias').show();
                $('.tbbancarias').show();
                limpiar_juridica();
                arrienda_no();
            }

            function limpiar_natural() {
                $('#cli_nom_comercial').val('');
                $('#cli_rep_apellidos').val('');
                $('#cli_rep_nombres').val('');
                $('#cli_rep_telefono').val('');
                $('#cli_rep_celular').val('');
                $('#cli_rep_email').val('');
                $('#cli_cont_apellidos').val('');
                $('#cli_cont_nombres').val('');
                $('#cli_cont_telefono').val('');
                $('#cli_cont_celular').val('');
                $('#cli_cont_email').val('');
                $('#cli_refc_empresa1').val('');
                $('#cli_refc_credito1').val('0');
                $('#cli_refc_telefono1').val('');
                $('#cli_refc_empresa2').val('');
                $('#cli_refc_credito2').val('0');
                $('#cli_refc_telefono2').val('');
                $('#cli_refb_institucion1').val('');
                $('#cli_refb_cuenta1').val('');
                $('#cli_refb_tip_cuenta1').val('');
                $('#cli_refb_institucion2').val('');
                $('#cli_refb_cuenta2').val('');
                $('#cli_refb_tip_cuenta2').val('');
            }

            function limpiar_juridica() {
                $('#cli_apellidos').val('');
                $('#cli_nombres').val('');
                $('#cli_nacionalidad').val('');
                $('#cli_lugar_nac').val('');
                $('#cli_con_cedula').val('');
                $('#cli_con_apellido_paterno').val('');
                $('#cli_con_apellido_materno').val('');
                $('#cli_con_nombres').val('');
                $('#cli_tiempo_residencia').val('');
                $('#cli_valor_arriendo').val('0');
                $('#cli_refc_credito1').val('0');
                $('#cli_refc_credito2').val('0');
                $('#cli_tipo_actividad').val('');
                $('#cli_empresa').val('');
                $('#cli_actividad').val('');
                $('#cli_cargo').val('');
                $('#cli_tiempo_trab').val('');
                $('#cli_actividad_telefono').val('');
                $('#cli_actividad_celular').val('');
                $('#cli_direccion_trabajo').val('');
                $('#cli_sueldo').val('0');
                $('#cli_ingresos').val('0');
                $('#cli_total_ingresos').val('0');
                $('#cli_total_gastos').val('0');
                $('#cli_con_sueldo').val('0');
                $('#cli_con_ingresos').val('0');
                $('#cli_con_total_ingresos').val('0');
                $('#cli_con_total_gastos').val('0');
                $('#cli_ref_apellidos1').val('');
                $('#cli_ref_nombres1').val('');
                $('#cli_ref_parentesco1').val('');
                $('#cli_ref_telefono1').val('');
                $('#cli_ref_apellidos2').val('');
                $('#cli_ref_nombres2').val('');
                $('#cli_ref_parentesco2').val('');
                $('#cli_ref_telefono2').val('');
                cli_tipo_actividad1.checked = false;
                cli_tipo_actividad2.checked = false;
                cli_tipo_actividad3.checked = false;
                cli_tipo_actividad4.checked = false;
                cli_tipo_actividad5.checked = false;
                cli_tipo_actividad6.checked = false;
                cli_tipo_actividad7.checked = false;
                cli_tipo_actividad8.checked = false;
            }

            function cod() {
                if ($('#cli_tipo').val() == 0) {
                    a = 'C';
                } else if ($('#cli_tipo').val() == 1) {
                    a = 'P';
                } else if ($('#cli_tipo').val() == 2) {
                    a = 'CP';
                }
                if (cli_categoria1.checked == true) {
                    b = 'N';
                }
                else if (cli_categoria2.checked == true) {
                    b = 'J';
                }
                if ($('#cli_tipo').val() == "") {
                    $('#cli_codigo').val('');
                } else {
                    codigo(a, b);
                }
            }
            function codigo(a, b) {
//                alert(a+'--'+b)
                $.post('actions_cliente.php', {l1: a, l2: b, op: 2}, function (dt) {
                    $('#cli_codigo').val(dt);
                })
            }
            function cupo_si() {
                $('#lblcupo').show();
                $('#cli_cup_maximo').show();
                $('#lblcategoria').show();
                $('#cli_cat_cliente').show();
            }
            function cupo_no() {
                $('#lblcupo').hide();
                $('#cli_cup_maximo').hide();
                $('#cli_cup_maximo').val('0');
                $('#lblcategoria').hide();
                $('#cli_cat_cliente').hide();
                $('#cli_cat_cliente').val(0);
            }
            function arrienda_si() {
                $('#lblvalor').show();
                $('#cli_valor_arriendo').show();
            }
            function arrienda_no() {
                $('#lblvalor').hide();
                $('#cli_valor_arriendo').hide();
                $('#cli_valor_arriendo').val('0');
            }
            function suma() {
                var s = parseFloat($('#cli_sueldo').val() * 1) + parseFloat($('#cli_ingresos').val() * 1);
                s = s.toFixed(2);
                $('#cli_total_ingresos').val(s);
            }
            function sumacon() {
                var s = parseFloat($('#cli_con_sueldo').val() * 1) + parseFloat($('#cli_con_ingresos').val() * 1);
                s = s.toFixed(2);
                $('#cli_con_total_ingresos').val(s);
            }
            function proveedor() {
                if ($('#cli_tipo').val() == "1") {
                    var n = 0;
                    var tr = $('#tbl_form').find("tbody tr:last");
                    var a = tr.find("input").attr("id");
                    var i = a.substring(4, 5);
                    if (i != 0) {
                        while (n < i) {
                            n++;
                            $('#cde_local' + n).val('');
                            $('#cde_apellido' + n).val('');
                            $('#cde_nombre' + n).val('');
                            $('#cde_telefono' + n).val('');
                            $('#cde_pais' + n).val('');
                            $('#cde_provincia' + n).val('');
                            $('#cde_canton' + n).val('');
                            $('#cde_parroquia' + n).val('');
                            $('#cde_calle_prin' + n).val('');
                            $('#cde_numero' + n).val('');
                            $('#cde_calle_sec' + n).val('');
                            $('#cde_referencia' + n).val('');
                        }
                    }
                    $('#thdirentrega').hide();
                    $('#tbdirentrega').hide();
                    $('#add_row').hide();
                    $('#matriz').hide();
                    $('#head').hide();
                } else {
                    $('#thdirentrega').show();
                    $('#tbdirentrega').show();
                    $('#head').show();
                    $('#add_row').show();
                }
            }
            function add() {
                if (this.lang == 0) {
                    clona_fila($('#tbl_form'));
                } else {
                    this.lang = 0;
                }
            }
            function est_civil1() {
                $('#lblconyugue').hide();
                $('#lblconcedula').hide();
                $('#cli_con_cedula').hide();
                $('#lblconapellido1').hide();
                $('#cli_con_apellido_paterno').hide();
                $('#lblconapellido2').hide();
                $('#cli_con_apellido_materno').hide();
                $('#lblconnombres').hide();
                $('#cli_con_nombres').hide();
                $('#lblactconyugue').hide();
                $('#lblconsueldo').hide();
                $('#cli_con_sueldo').hide();
                $('#lblconingresos').hide();
                $('#cli_con_ingresos').hide();
                $('#lblcontotingresos').hide();
                $('#cli_con_total_ingresos').hide();
                $('#lblcontotgastos').hide();
                $('#cli_con_total_gastos').hide();
                $('#cli_con_cedula').val('');
                $('#cli_con_apellido_paterno').val('');
                $('#cli_con_apellido_materno').val('');
                $('#cli_con_nombres').val('');
                $('#cli_con_sueldo').val('0');
                $('#cli_con_ingresos').val('0');
                $('#cli_con_total_ingresos').val('0');
                $('#cli_con_total_gastos').val('0');
            }
            function est_civil2() {
                $('#lblconyugue').show();
                $('#lblconcedula').show();
                $('#cli_con_cedula').show();
                $('#lblconapellido1').show();
                $('#cli_con_apellido_paterno').show();
                $('#lblconapellido2').show();
                $('#cli_con_apellido_materno').show();
                $('#lblconnombres').show();
                $('#cli_con_nombres').show();
                $('#lblactconyugue').show();
                $('#lblconsueldo').show();
                $('#cli_con_sueldo').show();
                $('#lblconingresos').show();
                $('#cli_con_ingresos').show();
                $('#lblcontotingresos').show();
                $('#cli_con_total_ingresos').show();
                $('#lblcontotgastos').show();
                $('#cli_con_total_gastos').show();
            }
            function cedula() {

            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function verificar_ced_ruc(obj) {
                ruc_ced = obj.value;
                $.post('actions_cliente.php', {op: 3, id: ruc_ced},
                function (dt) {
                    if (dt == 1) {
                        alert('La Cedula/RUC ya existe');
                        $('#cli_ced_ruc').val('');
                        $('#cli_ced_ruc').focus();
                    }
                });
            }

        </script>
        <style>
            .eliminar{ cursor: pointer; color: #000; }
            thead tr td{
                font-size: 11px;
                border:solid 1px #ccc;
            }
            .totales td{
                color: #00529B;
                font-weight:bolder;
                font-size: 11px;
            }
            .head td {
                font-size: 11px;
                text-align: center;
            }
            input[type=text]{
                text-transform: uppercase;
            }
            .txt{
                margin-left:-20px;
            }
            .i{
                background:transparent;
                border:0px;
                font-weight: 400;
                font-size: 10px;
            }
            .hd{
                font-size: 10px;
                margin-left: -13px;
                text-align: center;
                background:transparent;
                border:0px;
            }
            .trhead{
                background-color:#0480be;
                height:22px;
            }
            .tdhead{
                font-weight: bolder;
                text-align: center;
                border-collapse: collapse;

            }
            .lblhead{
                font-size:12px;
                color:white;
            }
            .select{
                width: 200px;
            }

            *{
                font-size: 11px;
                /*font-weight:100;*/ 
            }

            #cli_tipo_cliente{
                width: 100px;
            }

            #cli_tipo{
                width: 170px;
            }

            #cli_cat_cliente{
                width: 170px;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  lang="0" autocomplete="off" id="frm_save">
            <table id="tbl_form" border='0'>
                <thead>
                    <tr>
                        <th colspan="10" >
                            FORMULARIO DE CONTROL
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td>Fecha Ingreso:</td>
                    <td>
                        <input type="text" size="25" id="cli_fecha" value="<?php echo $rst[cli_fecha] ?>"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                    </td>
                    <td>Tipo:</td>
                    <td>
                        <?php
                        switch ($rst_user[usu_pc]) {
                            case 0:
                                ?>
                                <select class="select" id="cli_tipo" onchange="cod(), proveedor()" onblur="proveedor()">
                                    <option value="0" >CLIENTE</option>
                                </select>
                                <?php
                                break;
                            case 1:
                                ?>
                                <select class="select" id="cli_tipo" onchange="cod(), proveedor()" onblur="proveedor()">
                                    <option value="1" >PROVEEDOR</option>
                                </select>
                                <?php
                                break;
                            case 2:
                                ?>
                                <select class="select" id="cli_tipo" onchange="cod(), proveedor()" onblur="proveedor()">
                                    <option value="" >SELECCIONE</option>
                                    <option value="0" >CLIENTE</option>
                                    <option value="1" >PROVEEDOR</option>
                                    <option value="2" >AMBOS</option>
                                </select>
                                <?php
                                break;
                            default:
                                ?>
                                <select class="select" id="cli_tipo" onchange="cod(), proveedor()" onblur="proveedor()">
                                    <option value="" >SELECCIONE</option>
                                    <option value="0" >CLIENTE</option>
                                    <option value="1" >PROVEEDOR</option>
                                    <option value="2" >AMBOS</option>
                                </select>
                                <?php
                                break;
                        }
                        ?>
                    </td>
                    <td>
                        <input type="radio"  id="cli_categoria1" name="cli_categoria" value="1" onclick="natural(), limpiar_natural(), cod()" />Natural
                    </td>
                    <td>
                        <input type="radio" id="cli_categoria2" name="cli_categoria" value="2" onclick="juridica(), limpiar_juridica(), cod()" />Juridica
                    </td>

                    <td>
                        Codigo:<input type="text" size="15" id="cli_codigo" readonly value="<?php echo $rst[cli_codigo] ?>"/>
                    </td>
                    <td>Estado:
                        <select id="cli_estado" >
                            <option value="0" >ACTIVO</option>
                            <option value="1" >INACTIVO</option>
                            <option value="2" >SUSPENDIDO</option>
                        </select>
                    </td>
                    <td>Cliente:</td>
                    <td><select class="select" id="cli_tipo_cliente">
                            <option value="0">NACIONAL</option>
                            <option value="1">EXTRANJERO</option>
                        </select></td>
                </tr>
                <tr class="trhead">
                    <td  class="tdhead" colspan="25"><label class="lblhead" for="male" id="lbldatgenerales" >DATOS GENERALES</label></td>
                </tr>
                <tr>
                    <td id="tdapellidos"><label for="male" id="lblapellidos">Apellidos:</label></td>
                    <td id="cliapellidos">
                        <input type="text" size="30" id="cli_apellidos" value="<?php echo $rst[cli_apellidos] ?>" />
                    </td>
                    <td id="tdnombres"><label for="male" id="lblnombres" >Nombres:</label></td>
                    <td id="clinombres">
                        <input type="text" size="30" id="cli_nombres" value="<?php echo $rst[cli_nombres] ?>"  />
                    </td>
                    <td><label for="male" id="lblrazon">Razon Social:</label></td>
                    <td>
                        <input type="text" size="30" id="cli_raz_social" value="<?php echo $rst[cli_raz_social] ?>" />
                    </td>
                    <td><label for="male" id="lblcedula">Cedula/Ruc:</label></td>
                    <td>
                        <input type="text" size="30" id="cli_ced_ruc" value="<?php echo $rst['cli_ced_ruc'] ?>" onchange="verificar_ced_ruc(this)"/>
                    </td>
                    <td id="comercial1"><label for="male" id="lblcomercial">Nombre Comercial:</label></td>
                    <td id="comercial2">
                        <input type="text" size="30" id="cli_nom_comercial" value="<?php echo $rst['cli_nom_comercial'] ?>" />
                    </td>

                </tr>
                <tr>
                    <td><label for="male" id="lblcredito">Credito:</label></td>
                    <td>
                        <input type="radio" id="cli_credito1" name="cli_credito" value="1" onclick="cupo_si()"/>Si
                        <input type="radio" id="cli_credito2" name="cli_credito" value="2" onclick="cupo_no()"/>No
                    </td>
                    <td><label for="male" id="lblcupo">Cupo Maximo:</label></td>
                    <td>
                        <input type="text" size="30" id="cli_cup_maximo" value="<?php echo $rst[cli_cup_maximo] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                    </td>
                    <td><label for="male" id="lblcategoria">Categoria Cliente:</label></td>
                    <td>
                        <select class="select" id="cli_cat_cliente">
                            <option value="0">Seleccione</option>
                            <?php
                            while ($rst_cp = pg_fetch_array($cns_cp)) {
                                $cns_tp = $Set->lista_tipo_de_pago();
                                while ($rst_tp = pg_fetch_array($cns_tp)) {
                                    $cns_cum = $Set->lista_cumplimiento();
                                    while ($rst_cum = pg_fetch_array($cns_cum)) {
                                        $tot = number_format(($rst_cp[cap_descuento] + $rst_tp[tip_descuento] + $rst_cum[cum_descuento]) / 3, 2);
                                        $n++;
                                        ?>
                                        <option value='<?php echo $rst_cp[cap_codigo] . $rst_tp[tip_codigo] . $rst_cum[cum_codigo] ?>'><?php echo $rst_cp[cap_codigo] . $rst_tp[tip_codigo] . $rst_cum[cum_codigo] . '   --> ' . $tot ?></option>
                                        <?PHP
                                    }
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td><label for="male" id="lblretencion">Emite Retencion:</label></td>
                    <td>
                        <input type="radio" id="cli_retencion1" name="cli_retencion" value="1" />Si
                        <input type="radio" id="cli_retencion2" name="cli_retencion" value="2" />No
                    </td>
                </tr>
                <tr class="trhead" id="thdatos">
                    <td  class="tdhead" colspan="14"><label class="lblhead" id="lbldpersonales">DATOS PERSONALES<label></td>
                                </tr>
                                <tr class="datos">
                                    <td><label for="male" id="lblnacionalidad">Nacionalidad:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_nacionalidad" value="<?php echo $rst[cli_nacionalidad] ?>"/>
                                    </td>
                                    <td><label for="male" id="lbllugar">Lugar de Nacimiento:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_lugar_nac" value="<?php echo $rst[cli_lugar_nac] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblfechanac">Fecha de Nacimiento:</label></td>
                                    <td>
                                        <input type="text" size="25" id="cli_fecha_nac" value="<?php echo $rst[cli_fecha_nac] ?>"/>
                                        <img src="../img/calendar.png" id="im-campo2"/>
                                    </td>
                                </tr>
                                <tr class="datos">
                                    <td colspan="4" id="estado"><label for="male" id="lblcivil">Estado Civil:</label>
                                        <input type="radio"  id="cli_estado_civil1" name="cli_estado_civil" value="1" onclick="est_civil1()"/>Soltero

                                        <input type="radio"  id="cli_estado_civil2" name="cli_estado_civil" value="2" onclick="est_civil2()"/>Casado

                                        <input type="radio"  id="cli_estado_civil3" name="cli_estado_civil" value="3" onclick="est_civil1()"/>Divorciado

                                        <input type="radio"  id="cli_estado_civil4" name="cli_estado_civil" value="4" onclick="est_civil2()"/>Union Libre

                                        <input type="radio"  id="cli_estado_civil5" name="cli_estado_civil" value="5" onclick="est_civil1()"/>Viudo
                                    </td>

                                    <td colspan="2"><label for="male" id="lblvivienda">Vivienda:</label>

                                        <input type="radio"  id="cli_tipo_vivienda1" name="cli_tipo_vivienda" value="1" onclick="arrienda_no()"/>Propia 

                                        <input type="radio"  id="cli_tipo_vivienda2" name="cli_tipo_vivienda" value="2" onclick="arrienda_no()"/>Familiar

                                        <input type="radio"  id="cli_tipo_vivienda3" name="cli_tipo_vivienda" value="3" onclick="arrienda_si()"/>Arrienda
                                    <td>
                                        <label for="male" id="lblvalor">Valor:</label>
                                    </td>
                                    <td>
                                        <input type="text" size="30" id="cli_valor_arriendo" value="<?php echo $rst[cli_valor_arriendo] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                </tr>
                                <tr class="datos">
                                    <td colspan="2"><label for="male" id="lblconyugue">Datos del Conyugue:</label></td>
                                </tr>
                                <tr class="datos">
                                    <td><label for="male" id="lblconcedula">Cedula:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_con_cedula" value="<?php echo $rst['cli_con_cedula'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                    <td><label for="male" id="lblconapellido1">Apellido Paterno:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_con_apellido_paterno" value="<?php echo $rst[cli_con_apellido_paterno] ?>" />
                                    </td>
                                    <td><label for="male" id="lblconapellido2">Apellido Materno:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_con_apellido_materno" value="<?php echo $rst[cli_con_apellido_materno] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblconnombres">Nombres:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_con_nombres" value="<?php echo $rst[cli_con_nombres] ?>"/>
                                    </td>
                                </tr>
                                <tr class="trhead">
                                    <td  class="tdhead" colspan="10"><label class="lblhead" id="lblresidencia">DIRECCION RESIDENCIA</label><label class="lblhead" for="male" id="lblmatriz">DIRECCION MATRIZ</label></td>
                                </tr>
                                <tr>
                                    <td><label for="male" id="lblpais">Pais:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_pais" value="<?php echo $rst[cli_pais] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblprovincia">Provincia:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_provincia" value="<?php echo $rst[cli_provincia] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblcanton">Canton:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_canton" value="<?php echo $rst[cli_canton] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblparroquia">Parroquia:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_parroquia" value="<?php echo $rst[cli_parroquia] ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="male" id="lblcallepri">Calle Principal:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_calle_prin" value="<?php echo $rst[cli_calle_prin] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblnumeracion">Numeracion:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_numeracion" value="<?php echo $rst[cli_numeracion] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblcallepri">Calle Secundaria:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_calle_sec" value="<?php echo $rst[cli_calle_sec] ?>"/>
                                    </td>    
                                    <td><label for="male" id="lblrestelefono">Telefono:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_telefono" value="<?php echo $rst[cli_telefono] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="male" id="lblemail">E-mail:</label></td>
                                    <td>
                                        <input type="email" size="30" id="cli_email" value="<?php echo $rst[cli_email] ?>" style="text-transform: lowercase"/>
                                    </td>
                                    <td><label for="male" id="lblreferncia">Referencia:</label></td>
                                    <td colspan="3">
                                        <input type="text" size="91" id="cli_referencia" value="<?php echo $rst[cli_referencia] ?>"/>
                                    </td>
                                    <td><label for="male" id="lbltieresidencia">Tiempo de residencia:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_tiempo_residencia" value="<?php echo $rst[cli_tiempo_residencia] ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="male" id="lbllocal">local arriendo es:</label></td>
                                    <td colspan="3">
                                        <input type="radio"  id="cli_tipo_vivienda4" name="cli_tipo_vivienda" value="4" /><label for="male" id="lbllocpropio">Propia</label>
                                        <input type="radio"  id="cli_tipo_vivienda5" name="cli_tipo_vivienda" value="5" /><label for="male" id="lbllocarriendo">Arriendo</label>
                                        <input type="radio"  id="cli_tipo_vivienda6" name="cli_tipo_vivienda" value="6" /><label for="male" id="lbllocotros">Otros</label>
                                    </td>
                                </tr>
                                <tr class="trhead" id="thactividad">
                                    <td  class="tdhead" colspan="10"><label class="lblhead" for="male" id="lblactividad">ACTIVIDAD ECONOMICA</label></td>
                                </tr>
                                <tr class="tbactividad">
                                    <td colspan="2">
                                        <input type="checkbox"  id="cli_tipo_actividad1" name="cli_tipo_actividad" value="1" />Empleado Publico
                                    </td>
                                    <td colspan="2">
                                        <input type="checkbox"  id="cli_tipo_actividad2" name="cli_tipo_actividad" value="2" />Rentista
                                    </td>
                                    <td colspan="2">
                                        <input type="checkbox"  id="cli_tipo_actividad3" name="cli_tipo_actividad" value="3" />Empleado Privado
                                    </td>
                                    <td colspan="2">
                                        <input type="checkbox"  id="cli_tipo_actividad4" name="cli_tipo_actividad" value="4" />Ama de casa
                                    </td>
                                </tr>
                                <tr class="tbactividad">
                                    <td colspan="2">
                                        <input type="checkbox"  id="cli_tipo_actividad5" name="cli_tipo_actividad" value="5" />Independiente
                                    </td>
                                    <td colspan="2">
                                        <input type="checkbox"  id="cli_tipo_actividad6" name="cli_tipo_actividad" value="6" />Remesas Exterior
                                    </td>
                                    <td colspan="2">
                                        <input type="checkbox"  id="cli_tipo_actividad7" name="cli_tipo_actividad" value="7" />Estudiante
                                    </td>
                                    <td colspan="2">
                                        <input type="checkbox"  id="cli_tipo_actividad8" name="cli_tipo_actividad" value="8" />Jubilado
                                    </td>
                                </tr>
                                <tr class="tbactividad">
                                    <td><label for="male" id="lblempresa">Nombre Empresa:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_empresa" value="<?php echo $rst[cli_empresa] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblactividad">Actividad:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_actividad" value="<?php echo $rst[cli_actividad] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblpropia">Propia:</label></td>
                                    <td>
                                        <input type="radio" id="cli_propia1" name="cli_propia" value="1"/>Si
                                        <input type="radio" id="cli_propia2" name="cli_propia" value="2"/>No
                                    </td>
                                    <td><label for="male" id="lblcargo">Cargo:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_cargo" value="<?php echo $rst[cli_cargo] ?>"/>
                                    </td>
                                </tr>
                                <tr class="tbactividad">
                                    <td><label for="male" id="lbltietrabajo">Tiempo de Trabajo:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_tiempo_trab" value="<?php echo $rst[cli_tiempo_trab] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblemptelefono">Telefono:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_actividad_telefono" value="<?php echo $rst['cli_actividad_telefono'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                    <td><label for="male" id="lblempcelular">Celular:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_actividad_celular" value="<?php echo $rst['cli_actividad_celular'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                    <td><label for="male" id="lbldirtrabajo">Direccion de Trabajo:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_direccion_trabajo" value="<?php echo $rst[cli_direccion_trabajo] ?>"/>
                                    </td>
                                </tr>
                                <tr class="tbactividad">
                                    <td><label for="male" id="lblsueldo">Sueldo:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_sueldo" value="<?php echo $rst[cli_sueldo] ?> " onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onblur="suma()"/>
                                    </td>
                                    <td><label for="male" id="lblingresos">Otros Ingresos:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_ingresos" value="<?php echo $rst[cli_ingresos] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onblur="suma()"/>
                                    </td>
                                    <td><label for="male" id="lbltotingresos">Total Ingresos:</label></td>
                                    <td>
                                        <input type="text" size="17" id="cli_total_ingresos" value="<?php echo $rst[cli_total_ingresos] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" readonly/>
                                    </td>
                                    <td><label for="male" id="lbltotgastos">Total Gastos:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_total_gastos" value="<?php echo $rst[cli_total_gastos] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                </tr>
                                <tr class="tbactividad">
                                    <td><label for="male" id="lblactconyugue">Conyugue:</label></td>
                                </tr>
                                <tr class="tbactividad">
                                    <td><label for="male" id="lblconsueldo">Sueldo:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_con_sueldo" value="<?php echo $rst[cli_con_sueldo] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onblur="sumacon()"/>
                                    </td>
                                    <td><label for="male" id="lblconingresos">Otros Ingresos:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_con_ingresos" value="<?php echo $rst[cli_con_ingresos] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"onblur="sumacon()"/>
                                    </td>
                                    <td><label for="male" id="lblcontotingresos">Total Ingresos:</label></td>
                                    <td>
                                        <input type="text" size="17" id="cli_con_total_ingresos" value="<?php echo $rst[cli_con_total_ingresos] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" readonly/>
                                    </td>
                                    <td><label for="male" id="lblcontotgastos">Total Gastos:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_con_total_gastos" value="<?php echo $rst[cli_con_total_gastos] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                </tr>
                                <tr class="trhead" id="thpersonales">
                                    <td  class="tdhead" colspan="14"><label class="lblhead" for="male" id="lblpersonales">REFERENCIAS PERSONALES</label></td>
                                </tr>
                                <tr class="tbpersonales">
                                    <td><label for="male" id="lblrefapellidos1">Apellidos:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_ref_apellidos1" value="<?php echo $rst[cli_ref_apellidos1] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblrefnombres1">Nombres:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_ref_nombres1" value="<?php echo $rst[cli_ref_nombres1] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblrefparentesco1">Parentesco:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_ref_parentesco1" value="<?php echo $rst[cli_ref_parentesco1] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblreftelefono1">Telefono:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_ref_telefono1" value="<?php echo $rst['cli_ref_telefono1'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                </tr>
                                <tr class="tbpersonales">
                                    <td><label for="male" id="lblrefapellidos2">Apellidos:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_ref_apellidos2" value="<?php echo $rst[cli_ref_apellidos2] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblrefnombres2">Nombres:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_ref_nombres2" value="<?php echo $rst[cli_ref_nombres2] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblrefparentesco2">Parentesco:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_ref_parentesco2" value="<?php echo $rst[cli_ref_parentesco2] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblreftelefono2">Telefono:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_ref_telefono2" value="<?php echo $rst['cli_ref_telefono2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                </tr>
                                <tr class="trhead" id="threpresentante">
                                    <td  class="tdhead" colspan="14"><label class="lblhead" for="male" id="lblrepresentante">REPRESENTANTE LEGAL</label></tr>
                                <tr class="tbrepresentante">
                                    <td><label for="male" id="lblrepapellidos">Apellidos:</label>
                                        <input type="text" size="25" id="cli_rep_apellidos" value="<?php echo $rst[cli_rep_apellidos] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblrepnombres">Nombres:</label>
                                        <input type="text" size="25" id="cli_rep_nombres" value="<?php echo $rst[cli_rep_nombres] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblreptelefono">Telefono:</label></td>
                                    <td>
                                        <input type="text" size="25" id="cli_rep_telefono" value="<?php echo $rst[cli_rep_telefono] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                    <td><label for="male" id="lblrepcelular">Celular:</label></td>
                                    <td>
                                        <input type="text" size="25" id="cli_rep_celular" value="<?php echo $rst['cli_rep_celular'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                    <td><label for="male" id="lblrepemail">E-mail:</label></td>
                                    <td>
                                        <input type="email" size="30" id="cli_rep_email" value="<?php echo $rst[cli_rep_email] ?>" style="text-transform: lowercase"/>
                                    </td>
                                </tr>
                                <tr class="trhead" id="thcontacto">
                                    <td  class="tdhead" colspan="14"><label class="lblhead" for="male" id="lblcontacto">CONTACTO CLIENTE / PROVEEDOR</label></td>
                                </tr>
                                <tr class="tbcontacto">
                                    <td><label for="male" id="lblcontapellidos">Apellidos:</label>
                                        <input type="text" size="25" id="cli_cont_apellidos" value="<?php echo $rst[cli_cont_apellidos] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblcontnombres">Nombres:</label>
                                        <input type="text" size="25" id="cli_cont_nombres" value="<?php echo $rst[cli_cont_nombres] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblconttelefono">Telefono:</label></td>
                                    <td>
                                        <input type="text" size="25" id="cli_cont_telefono" value="<?php echo $rst[cli_cont_telefono] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                    <td><label for="male" id="lblcontcelular">Celular:</label></td>
                                    <td>
                                        <input type="text" size="25" id="cli_cont_celular" value="<?php echo $rst['cli_cont_celular'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                    <td><label for="male" id="lblcontemail">E-mail:</label></td>
                                    <td>
                                        <input type="email" size="30" id="cli_cont_email" value="<?php echo $rst[cli_cont_email] ?>" style="text-transform: lowercase"/>
                                    </td>
                                </tr>
                                <tr class="trhead" id="thcomerciales">
                                    <td  class="tdhead" colspan="10"><label class="lblhead" for="male" id="lblcomerciales">REFERENCIAS COMERCIALES</label></td>
                                </tr>
                                <tr class="tbcomerciales">
                                    <td><label for="male" id="lblrefcempresa1">Nombre de Empresa:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refc_empresa1" value="<?php echo $rst[cli_refc_empresa1] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblrefccredito1">Monto de Credito:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refc_credito1" value="<?php echo $rst[cli_refc_credito1] ?> "onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                    <td><label for="male" id="lblrefctelefono1">Telefono:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refc_telefono1" value="<?php echo $rst[cli_refc_telefono1] ?>"onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                </tr>
                                <tr class="tbcomerciales">
                                    <td><label for="male" id="lblrefcempresa2">Nombre de Empresa:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refc_empresa2" value="<?php echo $rst[cli_refc_empresa2] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblrefccredito2">Monto de Credito:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refc_credito2" value="<?php echo $rst[cli_refc_credito2] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                    <td><label for="male" id="lblrefctelefono2">Telefono:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refc_telefono2" value="<?php echo $rst[cli_refc_telefono2] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                </tr>
                                <tr class="trhead" id="thbancarias">
                                    <td  class="tdhead" colspan="14"><label class="lblhead" for="male" id="lblreferencias">REFERENCIAS BANCARIAS </label></td>
                                </tr>
                                <tr class="tbbancarias">
                                    <td><label for="male" id="lblinstitucion1">Institucion:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refb_institucion1" value="<?php echo $rst[cli_refb_institucion1] ?>" />
                                    </td>
                                    <td><label for="male" id="lblcuenta1">No. Cuenta:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refb_cuenta1" value="<?php echo $rst[cli_refb_cuenta1] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                    <td><label for="male" id="lbltipcuenta1">Tipo de cuenta:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refb_tip_cuenta1" value="<?php echo $rst[cli_refb_tip_cuenta1] ?>" />
                                    </td>
                                </tr>
                                <tr class="tbbancarias">
                                    <td><label for="male" id="lblinstitucion2">Institucion:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refb_institucion2" value="<?php echo $rst[cli_refb_institucion2] ?>"/>
                                    </td>
                                    <td><label for="male" id="lblcuenta1">No. Cuenta:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refb_cuenta2" value="<?php echo $rst[cli_refb_cuenta2] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                    </td>
                                    <td><label for="male" id="lbltipcuenta2">Tipo de cuenta:</label></td>
                                    <td>
                                        <input type="text" size="30" id="cli_refb_tip_cuenta2" value="<?php echo $rst[cli_refb_tip_cuenta2] ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <table id="tbdirentrega" style="width:101%;margin-left:-10px;"> 
                                            <tr class="trhead" id="thdirentrega">
                                                <td  class="tdhead" colspan="14"><label class="lblhead"for="male" id="lbldirentrega">DIRECCION DE ENTREGA</label> </td></tr>
                                            <tr id="head">

                                                <td>        
                                                    ITEM
                                                </td>
                                                <td>
                                                    LOCAL
                                                </td>
                                                <td>
                                                    APELLIDO
                                                </td>
                                                <td>
                                                    NOMBRE
                                                </td>
                                                <td>
                                                    TELEFONO
                                                </td>
                                                <td>
                                                    PAIS
                                                </td>
                                                <td>
                                                    PROVINCIA
                                                </td>
                                                <td>
                                                    CANTON
                                                </td>
                                                <td>
                                                    PARROQUIA
                                                </td>
                                                <td>
                                                    CALLE PRINCIPAL
                                                </td>
                                                <td>
                                                    NO.
                                                </td>
                                                <td>
                                                    CALLE SECUNDARIA
                                                </td>
                                                <td>
                                                    REFERENCIA
                                                </td>
                                            </tr>
                                            <?PHP
                                            $n = 0;
                                            if ($fila == "0") {
                                                ?>
                                                <tr>
                                                    <td align="right">
                                                        <input type="text" size="1" class="itm" id="item1" name="item1"   value="1" lang="1" style="text-align:right" readonly/>
                                                    </td>
                                                    <td>
                                                        <input class="txt" type="text" size="30" id="cde_local1" name="cde_local1" value="" lang="1"/>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="txt" size="12" id="cde_apellido1" name="cde_apellido1" value="" lang="1" />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="txt" size="12" id="cde_nombre1" name="cde_nombre1" value="" lang="1"/>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="txt" size="12" id="cde_telefono1" name="cde_telefono1" value="" lang="1"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="txt" size="12" id="cde_pais1" name="cde_pais1" value="" lang="1" />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="txt" size="12" id="cde_provincia1" name="cde_provincia1" value="" lang="1" />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="txt" size="12" id="cde_canton1" name="cde_canton1" value="" lang="1"/>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="txt" size="12" id="cde_parroquia1" name="cde_parroquia1" value="" lang="1"/>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="txt" size="30" id="cde_calle_prin1" name="cde_calle_prin1" value="" lang="1"/>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="txt" size="10" id="cde_numero1" name="cde_numero1" value="" lang="1"/>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="txt" size="30" id="cde_calle_sec1" name="cde_calle_sec1" value="" lang="1"/>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="txt" size="30" id="cde_referencia1" name="cde_referencia1" value="" lang="1"/>
                                                    </td>
                                                    <td onclick="eliminar_fila(this)"> <img src="../img/b_delete.png" /></td>                                            </td>
                                                </tr>
                                                <?PHP
                                            } else {
                                                while ($rst1 = pg_fetch_array($cns)) {
                                                    $n++;
                                                    ?>
                                                    <tr>
                                                        <td align="right">
                                                            <input type="text" size="2" class="itm" id="item<?PHP echo $n ?>" name="item<?PHP echo $n ?>"   value="<?PHP echo $n ?>" lang="<?PHP echo $n ?>" style="text-align:right" readonly/>
                                                        </td>
                                                        <td>
                                                            <input type="text" size="30" class="txt" id="cde_local<?PHP echo $n ?>" name="cde_local<?PHP echo $n ?>" value="<?php echo $rst1['cde_local'] ?>" lang="<?PHP echo $n ?>" />
                                                        </td>
                                                        <td>
                                                            <input type="text" size="12" class="txt"  id="cde_apellido<?PHP echo $n ?>" name="cde_apellido<?PHP echo $n ?>" value="<?php echo $rst1['cde_apellido'] ?>" lang="<?PHP echo $n ?>" />
                                                        </td>
                                                        <td>
                                                            <input type="text" size="12" class="txt"  id="cde_nombre<?PHP echo $n ?>" name="cde_nombre<?PHP echo $n ?>" value="<?php echo $rst1['cde_nombre'] ?>" lang="<?PHP echo $n ?>"/>
                                                        </td>
                                                        <td>
                                                            <input type="text" size="12" class="txt"  id="cde_telefono<?PHP echo $n ?>" name="cde_telefono<?PHP echo $n ?>" value="<?php echo $rst1['cde_telefono'] ?>" lang="<?PHP echo $n ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>
                                                        </td>
                                                        <td>
                                                            <input type="text" size="12" class="txt" id="cde_pais<?PHP echo $n ?>" name="cde_pais<?PHP echo $n ?>" value="<?php echo $rst1['cde_pais'] ?>" lang="<?PHP echo $n ?>" />
                                                        </td>
                                                        <td>
                                                            <input type="text" size="12" class="txt" id="cde_provincia<?PHP echo $n ?>" name="cde_provincia<?PHP echo $n ?>" value="<?php echo $rst1['cde_provincia'] ?>" lang="<?PHP echo $n ?>" />
                                                        </td>
                                                        <td>
                                                            <input type="text" size="12" class="txt" id="cde_canton<?PHP echo $n ?>" name="cde_canton<?PHP echo $n ?>" value="<?php echo $rst1['cde_canton'] ?>" lang="<?PHP echo $n ?>" />
                                                        </td>
                                                        <td>
                                                            <input type="text" size="12" class="txt" id="cde_parroquia<?PHP echo $n ?>" name="cde_parroquia<?PHP echo $n ?>" value="<?php echo $rst1['cde_parroquia'] ?>" lang="<?PHP echo $n ?>" />
                                                        </td>
                                                        <td>
                                                            <input type="text" size="30" class="txt" id="cde_calle_prin<?PHP echo $n ?>" name="cde_calle_prin<?PHP echo $n ?>" value="<?php echo $rst1['cde_calle_prin'] ?>" lang="<?PHP echo $n ?>"/>
                                                        </td>
                                                        <td>
                                                            <input type="text" size="10" class="txt" id="cde_numero<?PHP echo $n ?>" name="cde_numero<?PHP echo $n ?>" value="<?php echo $rst1['cde_numero'] ?>" lang="<?PHP echo $n ?>" />
                                                        </td>
                                                        <td>
                                                            <input type="text" size="30" class="txt" id="cde_calle_sec<?PHP echo $n ?>" name="cde_calle_sec<?PHP echo $n ?>" value="<?php echo $rst1['cde_calle_sec'] ?>" lang="<?PHP echo $n ?>" />
                                                        </td>
                                                        <td>
                                                            <input type="text" size="30" class="txt" id="cde_referencia<?PHP echo $n ?>" name="cde_referencia<?PHP echo $n ?>" value="<?php echo $rst1['cde_referencia'] ?>" lang="<?PHP echo $n ?>" />
                                                        </td>
                                                        <td onclick="eliminar_fila(this)"> <img src="../img/b_delete.png" /></td>
                                                    </tr>
                                                    <?PHP
                                                }
                                            }
                                            ?>
                                        </table>                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="10"><button id="add_row">+</button></td>
                                </tr>
                                <tfoot>
                                    <tr> 
                                        <td colspan="8">
                                            <?PHP
                                            if ($x != 1) {
                                                ?>                 
                                                <button id="guardar">Guardar</button>   
                                                <?PHP
                                            }
                                            ?>
                                            <button id="cancelar" >Cancelar</button>    
                                        </td>
                                    </tr>
                                </tfoot>
                                </table>
                                </form>
                                </body>
                                </html>    
                                <script>
                                    var tip = '<?php echo $rst[cli_tipo] ?>';
                                    $('#cli_tipo').val(tip);
                                    var est = '<?php echo $rst[cli_estado] ?>';
                                    $('#cli_estado').val(est);
                                    var cat =<?php echo $rst[cli_categoria] ?>;
                                    if (cat == 1) {
                                        $('#cli_categoria1').attr('checked', true);
                                        natural();
                                        suma();
                                        sumacon();
                                    } else if (cat == 2) {
                                        $('#cli_categoria2').attr('checked', true);
                                        juridica();
                                    }

                                    var ret = '<?php echo $rst[cli_retencion] ?>';
                                    if (ret == 1) {
                                        $('#cli_retencion1').attr('checked', true);
                                    } else {
                                        $('#cli_retencion2').attr('checked', true);
                                    }
                                    var cre = '<?php echo $rst[cli_credito] ?>';
                                    if (cre == 1) {
                                        $('#cli_credito1').attr('checked', true);
                                    } else if (cre == 2) {
                                        $('#cli_credito2').attr('checked', true);
                                        cupo_no();
                                    }
                                    var civ = '<?php echo $rst[cli_estado_civil] ?>';
                                    if (civ == 1) {
                                        $('#cli_estado_civil1').attr('checked', true);
                                        est_civil1();
                                    } else if (civ == 2) {
                                        $('#cli_estado_civil2').attr('checked', true);
                                        est_civil2();
                                    } else if (civ == 3) {
                                        $('#cli_estado_civil3').attr('checke d', true);
                                        est_civil1();
                                    } else if (civ == 4) {
                                        $('#cli_estado_civil4').attr('checked', true);
                                        est_civil2();
                                    } else if (civ == 5) {
                                        $('#cli_estado_civil5').attr('checked', true);
                                        est_civil1();
                                    }

                                    var viv = '<?php echo $rst[cli_tipo_vivienda] ?>';
                                    if (viv == 1) {
                                        $('#cli_tipo_vivienda1').attr('checked', true);
                                    } else if (viv == 2) {
                                        $('#cli_tipo_vivienda2').attr('checked', true);
                                    } else if (viv == 3) {
                                        $('#cli_tipo_vivienda3').attr('checked', true);
                                        arrienda_si();
                                    } else if (viv == 4) {
                                        $('#cli_tipo_vivienda4').attr('checked', true);
                                    } else if (viv == 5) {
                                        $('#cli_tipo_vivienda5').attr('checked', true);
                                    } else if (viv == 6) {
                                        $('#cli_tipo_vivienda6').attr('checked', true);
                                    }

                                    var pro = '<?php echo $rst[cli_propia] ?>';
                                    if (pro == 1) {
                                        $('#cli_propia1').attr('checked', true);
                                    } else if (pro == 2) {
                                        $('#cli_propia2').attr('checked', true);
                                    }

                                    var act = '<?php echo $rst[cli_tipo_actividad] ?>';
                                    dat = act.split(',');
                                    if (dat[0].length == 0) {
                                        $('#cli_tipo_actividad1').attr('checked', false);
                                    } else {
                                        $('#cli_tipo_actividad1').attr('checked', true);
                                    }

                                    if (dat[1].length == 0) {
                                        $('#cli_tipo_actividad2').attr('checked', false);
                                    } else {
                                        $('#cli_tipo_actividad2').attr('checked', true);
                                    }
                                    if (dat[2].length == 0) {
                                        $('#cli_tipo_actividad3').attr('checked', false);
                                    } else {
                                        $('#cli_tipo_actividad3').attr('checked', true);
                                    }
                                    if (dat[3].length == 0) {
                                        $('#cli_tipo_actividad4').attr('checked', false);
                                    } else {
                                        $('#cli_tipo_actividad4').attr('checked', true);
                                    }
                                    if (dat[4].length == 0) {
                                        $('#cli_tipo_actividad5').attr('checked', false);
                                    } else {
                                        $('#cli_tipo_actividad5').attr('checked', true);
                                    }
                                    if (dat[5].length == 0) {
                                        $('#cli_tipo_actividad6').attr('checked', false);
                                    } else {
                                        $('#cli_tipo_actividad6').attr('checked', true);
                                    }
                                    if (dat[6].length == 0) {
                                        $('#cli_tipo_actividad7').attr('checked', false);
                                    } else {
                                        $('#cli_tipo_actividad7').attr('checked', true);
                                    }
                                    if (dat[7].length == 0) {
                                        $('#cli_tipo_actividad8').attr('checked', false);
                                    } else {
                                        $('#cli_tipo_actividad8').attr('checked', true);
                                    }
                                    if (id != 0) {
                                        if ($('#cli_tipo').val() == "1") {
                                            $('#head').hide();
                                            $('#thdirentrega').hide();
                                            $('#tbdirentrega').hide();
                                            $('#add_row').hide();
                                        }
                                    }
                                    var ctc =<?php echo $rst[cli_tipo_cliente] ?>;
                                    $('#cli_tipo_cliente').val(ctc);


                                </script>