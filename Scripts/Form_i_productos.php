<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_productos.php'; // cambiar clsClase_productos
$Productos = new Clase_Productos();
  $txt1= $_GET[txt1];
  $txt2= $_GET[txt2];
  $txt3= $_GET[txt3];
  $tipo= $_GET[tipo];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Productos->lista_uno($id));
    $cns_combomp1 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp2 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp3 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp4 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp5 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp6 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp7 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp8 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp9 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp10 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp11 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp12 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp13 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp14 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp15 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp16 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp17 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp18 = $Productos->lista_combomp($rst[emp_id]);
    $num1 = $rst[emp_id];
} else {
    $id = 0;
    $rst['pro_largo'] = 0;
    $rst['pro_ancho'] = 0;
    $rst['pro_capa'] = 0;
    $rst['pro_espesor'] = 0;
    $rst['pro_gramaje'] = 0.918;
    $rst['pro_peso'] = 0;
    $rst['pro_medvul'] = 0;
    $rst['pro_mf1'] = 0;
    $rst['pro_mf2'] = 0;
    $rst['pro_mf3'] = 0;
    $rst['pro_mf4'] = 0;
    $rst['pro_mf5'] = 0;
    $rst['pro_mf6'] = 0;
    $rst['pro_mf7'] = 0;
    $rst['pro_mf8'] = 0;
    $rst['pro_mf9'] = 0;
    $rst['pro_mf10'] = 0;
    $rst['pro_mf11'] = 0;
    $rst['pro_mf12'] = 0;
    $rst['pro_mf13'] = 0;
    $rst['pro_mf14'] = 0;
    $rst['pro_mf15'] = 0;
    $rst['pro_mf16'] = 0;
    $rst['pro_mf17'] = 0;
    $rst['pro_mf18'] = 0;
    $rst['suma'] = 0;
    $rst['suma2'] = 0;
    $rst['suma3'] = 0;
    $rst['pro_por_tornillo1'] = 0;
    $rst['pro_por_tornillo2'] = 0;
    $rst['pro_por_tornillo3'] = 0;
    $rst['pro_propiedad4'] = 0;
    $rst['pro_propiedad5'] = 0;
    $rst['pro_propiedad6'] = 1;
    $rst['pro_propiedad7'] = 0;
}
$cns_combo = $Productos->lst_emp();
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
                $('#pro_mf1,#pro_mf2,#pro_mf3,#pro_mf4,#pro_mf5,#pro_mf6').change(function () {
                    suma();
                });
                $('#pro_mf7,#pro_mf8,#pro_mf9,#pro_mf10,#pro_mf11,#pro_mf12').change(function () {
                    suma2();
                });
                $('#pro_mf13,#pro_mf14,#pro_mf15,#pro_mf16,#pro_mf17,#pro_mf18').change(function () {
                    suma3();
                });
                $('#pro_peso,#pro_gramaje,#pro_capa,#pro_ancho,#pro_propiedad4,#pro_propiedad5,#pro_propiedad6,#pro_propiedad7,#pro_espesor').change(function () {
                    calcular();
                });

//                $('#pro_largo').change(function () {
//                    calcular();
//                });

                if (id == 0) {
                    $('#lblfabrica').show();
                    $('#emp_id').show();
                    $('#lblmedida1').show();
                    $('#lblfamilia').hide();
                    $('#lblalmohada').hide();
                    $('#pro_familia1').hide();
                    $('#lblrollo').hide();
                    $('#pro_familia2').hide();
                    $('#lblmedida2').hide();
                    $('#lblmedidor').hide();

                    $('#lblcapa').hide();
           
                    mostrarr1();
                } else {

                    $('#lblfabrica').hide();
                    $('#emp_id').hide();
                    mostrarr();
                }

            });
            function save(id) {

                if (pro_familia1.checked == true) {
                    fml = 1;
                } else {
                    fml = 2;
                }

                var data = Array(emp_id.value,
                        pro_codigo.value,
                        fml,
                        pro_descripcion.value,
                        pro_uni.value,
                        pro_largo.value,
                        parseFloat(pro_ancho.value/1000).toFixed(3),
                        pro_capa.value,
                        pro_espesor.value,
                        pro_gramaje.value,
                        pro_peso.value,
                        pro_medvul.value,
                        pro_mp1.value,
                        pro_mp2.value,
                        pro_mp3.value,
                        pro_mp4.value,
                        pro_mp5.value,
                        pro_mp6.value,
                        pro_mf1.value,
                        pro_mf2.value,
                        pro_mf3.value,
                        pro_mf4.value,
                        pro_mf5.value,
                        pro_mf6.value,
                        pro_mp7.value,
                        pro_mp8.value,
                        pro_mp9.value,
                        pro_mp10.value,
                        pro_mp11.value,
                        pro_mp12.value,
                        pro_mf7.value,
                        pro_mf8.value,
                        pro_mf9.value,
                        pro_mf10.value,
                        pro_mf11.value,
                        pro_mf12.value,
                        pro_mp13.value,
                        pro_mp14.value,
                        pro_mp15.value,
                        pro_mp16.value,
                        pro_mp17.value,
                        pro_mp18.value,
                        pro_mf13.value,
                        pro_mf14.value,
                        pro_mf15.value,
                        pro_mf16.value,
                        pro_mf17.value,
                        pro_mf18.value,
                        pro_tipo.value,
                        pro_por_tornillo1.value,
                        pro_por_tornillo2.value,
                        pro_por_tornillo3.value,
                        pro_propiedad4.value,
                        pro_propiedad5.value,
                        pro_propiedad6.value,
                        pro_propiedad7.value
                        );
                var fields = Array();
                $("#tbl_form").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (pro_tipo.value == '') {
                            $("#pro_tipo").css({borderColor: "red"});
                            $("#pro_tipo").focus();
                            return false;
                        }
                        else if (emp_id.value == 0) {
                            $("#emp_id").css({borderColor: "red"});
                            $("#emp_id").focus();
                            return false;
                        }
                        else if (pro_codigo.value.length == 0) {
                            $("#pro_codigo").css({borderColor: "red"});
                            $("#pro_codigo").focus();
                            return false;
                        }
                        else if (pro_descripcion.value.length == 0) {
                            $("#pro_descripcion").css({borderColor: "red"});
                            $("#pro_descripcion").focus();
                            return false;
                        }
                        else if (pro_uni.value == 0) {
                            $("#pro_uni").css({borderColor: "red"});
                            $("#pro_uni").focus();
                            return false;
                        }
                        else if (pro_largo.value.length == 0) {
                            $("#pro_largo").css({borderColor: "red"});
                            $("#pro_largo").focus();
                            return false;
                        }
                        else if (pro_ancho.value.length == 0) {
                            $("#pro_ancho").css({borderColor: "red"});
                            $("#pro_ancho").focus();
                            return false;
                        }
                        else if (pro_capa.value.length == 0) {
                            $("#pro_capa").css({borderColor: "red"});
                            $("#pro_capa").focus();
                            return false;
                        }
                        else if (pro_espesor.value.length == 0) {
                            $("#pro_espesor").css({borderColor: "red"});
                            $("#pro_espesor").focus();
                            return false;
                        }
                        else if (pro_gramaje.value.length == 0) {
                            $("#pro_gramaje").css({borderColor: "red"});
                            $("#pro_gramaje").focus();
                            return false;
                        }
                        else if (pro_peso.value.length == 0) {
                            $("#pro_peso").css({borderColor: "red"});
                            $("#pro_peso").focus();
                            return false;
                        }
                        else if (pro_propiedad4.value.length == 0) {
                            $("#pro_propiedad4").css({borderColor: "red"});
                            $("#pro_propiedad4").focus();
                            return false;
                        }
                        else if (pro_propiedad5.value.length == 0) {
                            $("#pro_propiedad5").css({borderColor: "red"});
                            $("#pro_propiedad5").focus();
                            return false;
                        }
                        else if (pro_propiedad6.value.length == 0) {
                            $("#pro_propiedad6").css({borderColor: "red"});
                            $("#pro_propiedad6").focus();
                            return false;
                        }
                        else if (pro_propiedad7.value.length == 0) {
                            $("#pro_propiedad7").css({borderColor: "red"});
                            $("#pro_propiedad7").focus();
                            return false;
                        }
//                        sum_tor = parseFloat(pro_por_tornillo1.value) + parseFloat(pro_por_tornillo2.value) + parseFloat(pro_por_tornillo3.value);
//                        if (sum_tor != 100) {
//                            alert('La suma de los Tornillos debe ser 100%')
//                            return false;
//                        }


//                        else if ($("#emp_id").val() != '7') {
//                            if (pro_mp1.value == 0) {
//                                $("#pro_mp1").css({borderColor: "red"});
//                                $("#pro_mp1").focus();
//                                return false;
//                            }
//                            else if (parseFloat($("#suma").val()) != 100) {
//                                $("#suma").css({borderColor: "red"});
//                                $("#suma").focus();
//                                return false;
//                            }
//                        }
//                        if (parseFloat($("#suma").val()) == 100 && parseFloat(pro_por_tornillo1.value) == 0) {
//                            $("#pro_por_tornillo1").css({borderColor: "red"});
//                            $("#pro_por_tornillo1").focus();
//                            return false;
//                        }
//                        if (parseFloat($("#suma2").val()) == 100 && parseFloat(pro_por_tornillo2.value) == 0) {
//                            $("#pro_por_tornillo2").css({borderColor: "red"});
//                            $("#pro_por_tornillo2").focus();
//                            return false;
//                        }
//                        if (parseFloat($("#suma3").val()) == 100 && parseFloat(pro_por_tornillo3.value) == 0) {
//                            $("#pro_por_tornillo3").css({borderColor: "red"});
//                            $("#pro_por_tornillo3").focus();
//                            return false;
//                        }
                        if (pro_mf1.value.length == 0) {
                            $("#pro_mf1").css({borderColor: "red"});
                            $("#pro_mf1").focus();
                            return false;
                        }
                        else if (pro_mf2.value.length == 0) {
                            $("#pro_mf2").css({borderColor: "red"});
                            $("#pro_mf2").focus();
                            return false;
                        }
                        else if (pro_mf3.value.length == 0) {
                            $("#pro_mf3").css({borderColor: "red"});
                            $("#pro_mf3").focus();
                            return false;
                        }
                        else if (pro_mf4.value.length == 0) {
                            $("#pro_mf4").css({borderColor: "red"});
                            $("#pro_mf4").focus();
                            return false;
                        }
                        else if (pro_mf5.value.length == 0) {
                            $("#pro_mf5").css({borderColor: "red"});
                            $("#pro_mf5").focus();
                            return false;
                        }
                        else if (pro_mf6.value.length == 0) {
                            $("#pro_mf6").css({borderColor: "red"});
                            $("#pro_mf6").focus();
                            return false;
                        }
                        else if (pro_mf7.value.length == 0) {
                            $("#pro_mf7").css({borderColor: "red"});
                            $("#pro_mf7").focus();
                            return false;
                        }
                        else if (pro_mf8.value.length == 0) {
                            $("#pro_mf8").css({borderColor: "red"});
                            $("#pro_mf8").focus();
                            return false;
                        }
                        else if (pro_mf9.value.length == 0) {
                            $("#pro_mf9").css({borderColor: "red"});
                            $("#pro_mf9").focus();
                            return false;
                        }
                        else if (pro_mf10.value.length == 0) {
                            $("#pro_mf10").css({borderColor: "red"});
                            $("#pro_mf10").focus();
                            return false;
                        }
                        else if (pro_mf11.value.length == 0) {
                            $("#pro_mf11").css({borderColor: "red"});
                            $("#pro_mf11").focus();
                            return false;
                        }
                        else if (pro_mf12.value.length == 0) {
                            $("#pro_mf12").css({borderColor: "red"});
                            $("#pro_mf12").focus();
                            return false;
                        }
                        else if (pro_mf13.value.length == 0) {
                            $("#pro_mf13").css({borderColor: "red"});
                            $("#pro_mf13").focus();
                            return false;
                        }
                        else if (pro_mf14.value.length == 0) {
                            $("#pro_mf14").css({borderColor: "red"});
                            $("#pro_mf14").focus();
                            return false;
                        }
                        else if (pro_mf15.value.length == 0) {
                            $("#pro_mf15").css({borderColor: "red"});
                            $("#pro_mf15").focus();
                            return false;
                        }
                        else if (pro_mf16.value.length == 0) {
                            $("#pro_mf16").css({borderColor: "red"});
                            $("#pro_mf16").focus();
                            return false;
                        }
                        else if (pro_mf17.value.length == 0) {
                            $("#pro_mf17").css({borderColor: "red"});
                            $("#pro_mf17").focus();
                            return false;
                        }
                        else if (pro_mf18.value.length == 0) {
                            $("#pro_mf18").css({borderColor: "red"});
                            $("#pro_mf18").focus();
                            return false;
                        }
                        else if (pro_por_tornillo1.value.length == 0) {
                            $("#pro_por_tornillo1").css({borderColor: "red"});
                            $("#pro_por_tornillo1").focus();
                            return false;
                        }
                        else if (pro_por_tornillo2.value.length == 0) {
                            $("#pro_por_tornillo2").css({borderColor: "red"});
                            $("#pro_por_tornillo2").focus();
                            return false;
                        }
                        else if (pro_por_tornillo3.value.length == 0) {
                            $("#pro_por_tornillo3").css({borderColor: "red"});
                            $("#pro_por_tornillo3").focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_productos.php', //cambiar actions_productos
                    data: {op: 0, 'data[]': data, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            if (id == 0) {
                                insert_precios();
                            } else {
                                cancelar();
                            }
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

            function insert_precios() {
                $.post("actions_preciospt.php", {op: 3, tab: 0},
                function (dt) {
                    if (dt == 0)
                    {
                        cancelar();
                    } else {
                        alert(dt);
                    }
                });
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_productos.php?search=1&txt1=<?php echo $txt1 ?>&txt2=<?php echo $txt2 ?>&txt3=<?php echo $txt3?>&tipo=<?php echo $tipo ?>';
            }

            function suma() {
                var sm = parseFloat($('#pro_mf1').val() * 1) + 0;
                sm = sm.toFixed(2);
                $('#suma').val(sm);
                var su = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1);
                su = su.toFixed(2);
                $('#suma').val(su);
                var sum = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1) + parseFloat($('#pro_mf3').val() * 1);
                sum = sum.toFixed(2);
                $('#suma').val(sum);
                var s = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1) + parseFloat($('#pro_mf3').val() * 1) + parseFloat($('#pro_mf4').val() * 1);
                s = s.toFixed(2);
                $('#suma').val(s);
                var ss = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1) + parseFloat($('#pro_mf3').val() * 1) + parseFloat($('#pro_mf4').val() * 1) + parseFloat($('#pro_mf5').val() * 1);
                ss = ss.toFixed(2);
                $('#suma').val(ss);
                var ssm = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1) + parseFloat($('#pro_mf3').val() * 1) + parseFloat($('#pro_mf4').val() * 1) + parseFloat($('#pro_mf5').val() * 1) + parseFloat($('#pro_mf6').val() * 1);
                ssm = ssm.toFixed(2);
                $('#suma').val(ssm);
                if (($('#suma').val() * 1) > 100) {
                    $('#pro_mf1').val('0');
                    $('#pro_mf2').val('0');
                    $('#pro_mf3').val('0');
                    $('#pro_mf4').val('0');
                    $('#pro_mf5').val('0');
                    $('#pro_mf6').val('0');
                    $("#suma").val('0');
                    alert('La suma es mayor al 100%');
                    suma();
                }
                if (sm == 100) {
                    $('#pro_mp2').hide();
                    $('#pro_mf2').hide();
                    $('#lblporcentaje1').hide();
                    $('#pro_mp3').hide();
                    $('#pro_mf3').hide();
                    $('#lblporcentaje2').hide();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#lblporcentaje3').hide();
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#pro_mp2').val('0');
                    $('#pro_mf2').val('0');
                    $('#pro_mp3').val('0');
                    $('#pro_mf3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#pro_mp5').val('0');
                    $('#pro_mf5').val('0');
                    $('#pro_mp6').val('0');
                    $('#pro_mf6').val('0');
                    $("#suma").val(sm);
                }
                else if (su == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#lblporcentaje1').show();
                    $('#pro_mp3').hide();
                    $('#pro_mf3').hide();
                    $('#lblporcentaje2').hide();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#lblporcentaje3').hide();
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#pro_mp3').val('0');
                    $('#pro_mf3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#pro_mp5').val('0');
                    $('#pro_mf5').val('0');
                    $('#pro_mp6').val('0');
                    $('#pro_mf6').val('0');
                    $("#suma").val(su);
                }
                else if (sum == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#lblporcentaje2').show();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#lblporcentaje3').hide();
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#pro_mp5').val('0');
                    $('#pro_mf5').val('0');
                    $('#pro_mp6').val('0');
                    $('#pro_mf6').val('0');
                    $("#suma").val(sum);
                }
                else if (s == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#lblporcentaje2').show();
                    $('#pro_mp4').show();
                    $('#pro_mf4').show();
                    $('#lblporcentaje3').show();
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#pro_mp5').val('0');
                    $('#pro_mf5').val('0');
                    $('#pro_mp6').val('0');
                    $('#pro_mf6').val('0');
                    $("#suma").val(s);
                }
                else if (ss == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#lblporcentaje2').show();
                    $('#pro_mp4').show();
                    $('#pro_mf4').show();
                    $('#lblporcentaje3').show();
                    $('#pro_mp5').show();
                    $('#pro_mf5').show();
                    $('#lblporcentaje4').show();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#pro_mp6').val('0');
                    $('#pro_mf6').val('0');
                    $("#suma").val(ss);
                }
                else if (su < 100 && sum < 100 && s < 100 && ss < 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#lblporcentaje1').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#lblporcentaje2').show();
                    $('#pro_mp4').show();
                    $('#pro_mf4').show();
                    $('#lblporcentaje3').show();
                    $('#pro_mp5').show();
                    $('#pro_mf5').show();
                    $('#lblporcentaje4').show();
                    $('#pro_mp6').show();
                    $('#pro_mf6').show();
                    $('#lblporcentaje5').show();
                }
                var a = $("#emp_id").val();
                if (a == 3 || a == 4 || a == 6) {
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                }
            }

            function suma2() {
                var sm = parseFloat($('#pro_mf7').val() * 1) + 0;
                sm = sm.toFixed(2);
                $('#suma2').val(sm);
                var su = parseFloat($('#pro_mf7').val() * 1) + parseFloat($('#pro_mf8').val() * 1);
                su = su.toFixed(2);
                $('#suma2').val(su);
                var sum = parseFloat($('#pro_mf7').val() * 1) + parseFloat($('#pro_mf8').val() * 1) + parseFloat($('#pro_mf9').val() * 1);
                sum = sum.toFixed(2);
                $('#suma2').val(sum);
                var s = parseFloat($('#pro_mf7').val() * 1) + parseFloat($('#pro_mf8').val() * 1) + parseFloat($('#pro_mf9').val() * 1) + parseFloat($('#pro_mf10').val() * 1);
                s = s.toFixed(2);
                $('#suma2').val(s);
                var ss = parseFloat($('#pro_mf7').val() * 1) + parseFloat($('#pro_mf8').val() * 1) + parseFloat($('#pro_mf9').val() * 1) + parseFloat($('#pro_mf10').val() * 1) + parseFloat($('#pro_mf11').val() * 1);
                ss = ss.toFixed(2);
                $('#suma2').val(ss);
                var ssm = parseFloat($('#pro_mf7').val() * 1) + parseFloat($('#pro_mf8').val() * 1) + parseFloat($('#pro_mf9').val() * 1) + parseFloat($('#pro_mf10').val() * 1) + parseFloat($('#pro_mf11').val() * 1) + parseFloat($('#pro_mf12').val() * 1);
                ssm = ssm.toFixed(2);
                $('#suma2').val(ssm);
                if (($('#suma2').val() * 1) > 100) {
                    $('#pro_mf7').val('0');
                    $('#pro_mf8').val('0');
                    $('#pro_mf9').val('0');
                    $('#pro_mf10').val('0');
                    $('#pro_mf11').val('0');
                    $('#pro_mf12').val('0');
                    $("#suma2").val('0');
                    alert('La suma es mayor al 100%');
                    suma2();
                }
                if (sm == 100) {
                    $('#pro_mp8').hide();
                    $('#pro_mf8').hide();
                    $('#lblporcentaje6').hide();
                    $('#pro_mp9').hide();
                    $('#pro_mf9').hide();
                    $('#lblporcentaje7').hide();
                    $('#pro_mp10').hide();
                    $('#pro_mf10').hide();
                    $('#lblporcentaje8').hide();
                    $('#pro_mp11').hide();
                    $('#pro_mf11').hide();
                    $('#lblporcentaje9').hide();
                    $('#pro_mp12').hide();
                    $('#pro_mf12').hide();
                    $('#lblporcentaje10').hide();
                    $('#pro_mp8').val('0');
                    $('#pro_mf8').val('0');
                    $('#pro_mp9').val('0');
                    $('#pro_mf9').val('0');
                    $('#pro_mp10').val('0');
                    $('#pro_mf10').val('0');
                    $('#pro_mp11').val('0');
                    $('#pro_mf11').val('0');
                    $('#pro_mp12').val('0');
                    $('#pro_mf12').val('0');
                    $("#suma2").val(sm);
                }
                else if (su == 100) {
                    $('#pro_mp8').show();
                    $('#pro_mf8').show();
                    $('#lblporcentaje6').show();
                    $('#pro_mp9').hide();
                    $('#pro_mf9').hide();
                    $('#lblporcentaje7').hide();
                    $('#pro_mp10').hide();
                    $('#pro_mf10').hide();
                    $('#lblporcentaje8').hide();
                    $('#pro_mp11').hide();
                    $('#pro_mf11').hide();
                    $('#lblporcentaje9').hide();
                    $('#pro_mp12').hide();
                    $('#pro_mf12').hide();
                    $('#lblporcentaje10').hide();
                    $('#pro_mp9').val('0');
                    $('#pro_mf9').val('0');
                    $('#pro_mp10').val('0');
                    $('#pro_mf10').val('0');
                    $('#pro_mp11').val('0');
                    $('#pro_mf11').val('0');
                    $('#pro_mp12').val('0');
                    $('#pro_mf12').val('0');
                    $("#suma2").val(su);
                }
                else if (sum == 100) {
                    $('#pro_mp8').show();
                    $('#pro_mf8').show();
                    $('#pro_mp9').show();
                    $('#pro_mf9').show();
                    $('#lblporcentaje7').show();
                    $('#pro_mp10').hide();
                    $('#pro_mf10').hide();
                    $('#lblporcentaje8').hide();
                    $('#pro_mp11').hide();
                    $('#pro_mf11').hide();
                    $('#lblporcentaje9').hide();
                    $('#pro_mp12').hide();
                    $('#pro_mf12').hide();
                    $('#lblporcentaje10').hide();
                    $('#pro_mp10').val('0');
                    $('#pro_mf10').val('0');
                    $('#pro_mp11').val('0');
                    $('#pro_mf11').val('0');
                    $('#pro_mp12').val('0');
                    $('#pro_mf12').val('0');
                    $("#suma2").val(sum);
                }
                else if (s == 100) {
                    $('#pro_mp8').show();
                    $('#pro_mf8').show();
                    $('#pro_mp9').show();
                    $('#pro_mf9').show();
                    $('#lblporcentaje7').show();
                    $('#pro_mp10').show();
                    $('#pro_mf10').show();
                    $('#lblporcentaje8').show();
                    $('#pro_mp11').hide();
                    $('#pro_mf11').hide();
                    $('#lblporcentaje9').hide();
                    $('#pro_mp12').hide();
                    $('#pro_mf12').hide();
                    $('#lblporcentaje10').hide();
                    $('#pro_mp11').val('0');
                    $('#pro_mf11').val('0');
                    $('#pro_mp12').val('0');
                    $('#pro_mf12').val('0');
                    $("#suma2").val(s);
                }
                else if (ss == 100) {
                    $('#pro_mp8').show();
                    $('#pro_mf8').show();
                    $('#pro_mp9').show();
                    $('#pro_mf9').show();
                    $('#lblporcentaje7').show();
                    $('#pro_mp10').show();
                    $('#pro_mf10').show();
                    $('#lblporcentaje8').show();
                    $('#pro_mp11').show();
                    $('#pro_mf11').show();
                    $('#lblporcentaje9').show();
                    $('#pro_mp12').hide();
                    $('#pro_mf12').hide();
                    $('#lblporcentaje10').hide();
                    $('#pro_mp12').val('0');
                    $('#pro_mf12').val('0');
                    $("#suma2").val(ss);
                }
                else if (su < 100 && sum < 100 && s < 100 && ss < 100) {
                    $('#pro_mp8').show();
                    $('#pro_mf8').show();
                    $('#lblporcentaje6').show();
                    $('#pro_mp9').show();
                    $('#pro_mf9').show();
                    $('#lblporcentaje7').show();
                    $('#pro_mp10').show();
                    $('#pro_mf10').show();
                    $('#lblporcentaje8').show();
                    $('#pro_mp11').show();
                    $('#pro_mf11').show();
                    $('#lblporcentaje9').show();
                    $('#pro_mp12').show();
                    $('#pro_mf12').show();
                    $('#lblporcentaje10').show();
                }
                var a = $("#emp_id").val();
                if (a == 3 || a == 4 || a == 6) {
                    $('#pro_mp11').hide();
                    $('#pro_mf11').hide();
                    $('#lblporcentaje9').hide();
                    $('#pro_mp12').hide();
                    $('#pro_mf12').hide();
                    $('#lblporcentaje10').hide();
                }
            }


            function suma3() {
                var sm = parseFloat($('#pro_mf13').val() * 1) + 0;
                sm = sm.toFixed(2);
                $('#suma3').val(sm);
                var su = parseFloat($('#pro_mf13').val() * 1) + parseFloat($('#pro_mf14').val() * 1);
                su = su.toFixed(2);
                $('#suma3').val(su);
                var sum = parseFloat($('#pro_mf13').val() * 1) + parseFloat($('#pro_mf14').val() * 1) + parseFloat($('#pro_mf15').val() * 1);
                sum = sum.toFixed(2);
                $('#suma3').val(sum);
                var s = parseFloat($('#pro_mf13').val() * 1) + parseFloat($('#pro_mf14').val() * 1) + parseFloat($('#pro_mf15').val() * 1) + parseFloat($('#pro_mf16').val() * 1);
                s = s.toFixed(2);
                $('#suma3').val(s);
                var ss = parseFloat($('#pro_mf13').val() * 1) + parseFloat($('#pro_mf14').val() * 1) + parseFloat($('#pro_mf15').val() * 1) + parseFloat($('#pro_mf16').val() * 1) + parseFloat($('#pro_mf17').val() * 1);
                ss = ss.toFixed(2);
                $('#suma3').val(ss);
                var ssm = parseFloat($('#pro_mf13').val() * 1) + parseFloat($('#pro_mf14').val() * 1) + parseFloat($('#pro_mf15').val() * 1) + parseFloat($('#pro_mf16').val() * 1) + parseFloat($('#pro_mf17').val() * 1) + parseFloat($('#pro_mf18').val() * 1);
                ssm = ssm.toFixed(2);
                $('#suma3').val(ssm);
                if (($('#suma3').val() * 1) > 100) {
                    $('#pro_mf13').val('0');
                    $('#pro_mf14').val('0');
                    $('#pro_mf15').val('0');
                    $('#pro_mf16').val('0');
                    $('#pro_mf17').val('0');
                    $('#pro_mf18').val('0');
                    $("#suma3").val('0');
                    alert('La suma es mayor al 100%');
                    suma3();
                }
                if (sm == 100) {
                    $('#pro_mp14').hide();
                    $('#pro_mf14').hide();
                    $('#lblporcentaje11').hide();
                    $('#pro_mp15').hide();
                    $('#pro_mf15').hide();
                    $('#lblporcentaje12').hide();
                    $('#pro_mp16').hide();
                    $('#pro_mf16').hide();
                    $('#lblporcentaje13').hide();
                    $('#pro_mp17').hide();
                    $('#pro_mf17').hide();
                    $('#lblporcentaje14').hide();
                    $('#pro_mp18').hide();
                    $('#pro_mf18').hide();
                    $('#lblporcentaje15').hide();
                    $('#pro_mp14').val('0');
                    $('#pro_mf14').val('0');
                    $('#pro_mp15').val('0');
                    $('#pro_mf15').val('0');
                    $('#pro_mp16').val('0');
                    $('#pro_mf16').val('0');
                    $('#pro_mp17').val('0');
                    $('#pro_mf17').val('0');
                    $('#pro_mp18').val('0');
                    $('#pro_mf18').val('0');
                    $("#suma3").val(sm);
                }
                else if (su == 100) {
                    $('#pro_mp14').show();
                    $('#pro_mf14').show();
                    $('#lblporcentaje11').show();
                    $('#pro_mp15').hide();
                    $('#pro_mf15').hide();
                    $('#lblporcentaje12').hide();
                    $('#pro_mp16').hide();
                    $('#pro_mf16').hide();
                    $('#lblporcentaje13').hide();
                    $('#pro_mp17').hide();
                    $('#pro_mf17').hide();
                    $('#lblporcentaje14').hide();
                    $('#pro_mp18').hide();
                    $('#pro_mf18').hide();
                    $('#lblporcentaje15').hide();
                    $('#pro_mp15').val('0');
                    $('#pro_mf15').val('0');
                    $('#pro_mp16').val('0');
                    $('#pro_mf16').val('0');
                    $('#pro_mp17').val('0');
                    $('#pro_mf17').val('0');
                    $('#pro_mp18').val('0');
                    $('#pro_mf18').val('0');
                    $("#suma3").val(su);
                }
                else if (sum == 100) {
                    $('#pro_mp14').show();
                    $('#pro_mf14').show();
                    $('#pro_mp15').show();
                    $('#pro_mf15').show();
                    $('#lblporcentaje12').show();
                    $('#pro_mp16').hide();
                    $('#pro_mf16').hide();
                    $('#lblporcentaje13').hide();
                    $('#pro_mp17').hide();
                    $('#pro_mf17').hide();
                    $('#lblporcentaje14').hide();
                    $('#pro_mp18').hide();
                    $('#pro_mf18').hide();
                    $('#lblporcentaje15').hide();
                    $('#pro_mp16').val('0');
                    $('#pro_mf16').val('0');
                    $('#pro_mp17').val('0');
                    $('#pro_mf17').val('0');
                    $('#pro_mp18').val('0');
                    $('#pro_mf18').val('0');
                    $("#suma3").val(sum);
                }
                else if (s == 100) {
                    $('#pro_mp14').show();
                    $('#pro_mf14').show();
                    $('#pro_mp15').show();
                    $('#pro_mf15').show();
                    $('#lblporcentaje12').show();
                    $('#pro_mp16').show();
                    $('#pro_mf16').show();
                    $('#lblporcentaje13').show();
                    $('#pro_mp17').hide();
                    $('#pro_mf17').hide();
                    $('#lblporcentaje14').hide();
                    $('#pro_mp18').hide();
                    $('#pro_mf18').hide();
                    $('#lblporcentaje15').hide();
                    $('#pro_mp17').val('0');
                    $('#pro_mf17').val('0');
                    $('#pro_mp18').val('0');
                    $('#pro_mf18').val('0');
                    $("#suma3").val(s);
                }
                else if (ss == 100) {
                    $('#pro_mp14').show();
                    $('#pro_mf14').show();
                    $('#pro_mp15').show();
                    $('#pro_mf15').show();
                    $('#lblporcentaje12').show();
                    $('#pro_mp16').show();
                    $('#pro_mf16').show();
                    $('#lblporcentaje13').show();
                    $('#pro_mp17').show();
                    $('#pro_mf17').show();
                    $('#lblporcentaje14').show();
                    $('#pro_mp18').hide();
                    $('#pro_mf18').hide();
                    $('#lblporcentaje15').hide();
                    $('#pro_mp18').val('0');
                    $('#pro_mf18').val('0');
                    $("#suma3").val(ss);
                }
                else if (su < 100 && sum < 100 && s < 100 && ss < 100) {
                    $('#pro_mp14').show();
                    $('#pro_mf14').show();
                    $('#lblporcentaje11').show();
                    $('#pro_mp15').show();
                    $('#pro_mf15').show();
                    $('#lblporcentaje12').show();
                    $('#pro_mp16').show();
                    $('#pro_mf16').show();
                    $('#lblporcentaje13').show();
                    $('#pro_mp17').show();
                    $('#pro_mf17').show();
                    $('#lblporcentaje14').show();
                    $('#pro_mp18').show();
                    $('#pro_mf18').show();
                    $('#lblporcentaje15').show();
                }
                var a = $("#emp_id").val();
                if (a == 3 || a == 4 || a == 6) {
                    $('#pro_mp17').hide();
                    $('#pro_mf17').hide();
                    $('#lblporcentaje14').hide();
                    $('#pro_mp18').hide();
                    $('#pro_mf18').hide();
                    $('#lblporcentaje15').hide();
                }
            }



            function calcular(op) {
                



                        var p = ((parseFloat($('#pro_propiedad7').val() )-parseFloat($('#pro_capa').val()))* 1000000 / (parseFloat($('#pro_ancho').val()*1) * parseFloat($('#pro_gramaje').val() * 1)*parseFloat($('#pro_espesor').val() * 1)));
                        p = p.toFixed(2);
                        if (p == 'Infinity' || p == 'NaN') {
                            p = 0;
                        }
                        $('#pro_largo').val(p);
                        
                        var t = (parseFloat($('#pro_propiedad7').val() )-parseFloat($('#pro_capa').val()))*parseFloat($('#pro_propiedad6').val() );
                        t = t.toFixed(2);
                        if (t == 'Infinity' || t == 'NaN') {
                            t = 0;
                        }
                        $('#pro_propiedad4').val(t);

                         var s = parseFloat($('#pro_propiedad4').val() )+parseFloat($('#pro_propiedad5').val());
                        s = s.toFixed(2);
                        if (s == 'Infinity' || s == 'NaN') {
                            s = 0;
                        }
                        $('#pro_peso').val(s);
                        
                        var v = (parseFloat($('#pro_propiedad7').val() )-parseFloat($('#pro_capa').val()));
                        v = v.toFixed(2);
                        if (v == 'Infinity' || t == 'NaN') {
                            v = 0;
                        }
                        $('#pro_medvul').val(v);

    
    }
            
            function load_datos(obj) {
                $.post('actions_productos.php', {id: obj.value, op: 2}, function (dt) { // cambiar a actions_productos
                    dat = dt.split('&');
                    $('#pro_codigo').val(dat[0]);
                    $('#pro_mp1,#pro_mp2,#pro_mp3,#pro_mp4,#pro_mp5,#pro_mp6,#pro_mp7,#pro_mp8,#pro_mp9,#pro_mp10,#pro_mp11,#pro_mp12,#pro_mp13,#pro_mp14,#pro_mp15,#pro_mp16,#pro_mp17,#pro_mp18').html(dat[1]);
                })
            }
            function ocultar() {

                $('#lblmedida1').show();
                $('#lblmedida2').hide();
                $('#lblmedidor').hide();

            }


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function porcentaje_tornillos(obj) {
                if ($('#pro_por_tornillo1').val() != '') {
                    t1 = parseFloat($('#pro_por_tornillo1').val());
                } else {
                    t1 = 0;
                }
                if ($('#pro_por_tornillo2').val() != '') {
                    t2 = parseFloat($('#pro_por_tornillo2').val());
                } else {
                    t2 = 0;
                }
                if ($('#pro_por_tornillo3').val() != '') {
                    t3 = parseFloat($('#pro_por_tornillo3').val());
                } else {
                    t3 = 0;
                }
                total = t1 + t2 + t3;
                if (total > 100) {
                    alert('La suma de los tornillos es mayor al 100%');
                    $(obj).val('');
                    $(obj).focus();
                }
            }
        </script>
    </head>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  autocomplete="off" id="frm_save">
            <style>
                tbody{
                    float:left;
                }
                select{
                    margin:3px; 
                }
                input[type=text]{
                    text-transform: uppercase;
                }
                #emp_id{
                    width: 150px;
                }
                #pro_mp1{
                    width: 200px;
                }
                #pro_mp2{
                    width: 200px;
                }
                #pro_mp3{
                    width: 200px;
                }
                #pro_mp4{
                    width: 200px;
                }
                #pro_mp5{
                    width: 200px;
                }
                #pro_mp6{
                    width: 200px;
                }
            </style>
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="5" >PRODUCTOS <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><label for="male" id="lbltipo">Tipo:</label></td>
                        <td>
                            <select id="pro_tipo">
                                <option value="">Seleccione</option>
                                <option value="0">Semielaborado</option>
                                <option value="1">Terminado</option>

                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="male" id="lblfabrica">Fabrica:</label></td>
                        <td>
                            <select id="emp_id" onchange="load_datos(this);
                                    mostrarr(this)" >
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combo = pg_fetch_array($cns_combo)) {
                                    echo "<option value='$rst_combo[emp_id]' >$rst_combo[emp_descripcion]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>CODIGO:</td>
                        <td><input type="text" size="35"  id="pro_codigo" value="<?php echo $rst['pro_codigo'] ?>"  /></td>
                    </tr>
                    <tr hidden>
                        <td><label for="male" id="lblfamilia">Familia:</label></td>
                        <td>
                            <label fot="male" id="lblalmohada">ALMOHADA</label>
                            <input type="radio" size="12"  id="pro_familia1" name="pro_familia" value="1" onclick="ocultar()"/>
                            <label for="male" id="lblrollo">ROLLO</label>
                            <input type="radio" size="12"  id="pro_familia2" name="pro_familia" value="2" onclick="mostrar()" />
                        </td>
                    </tr>
                    <tr>
                        <td> Descripcion: </td>
                        <td> <input type ="text" size="35"  id="pro_descripcion"  value="<?php echo $rst['pro_descripcion'] ?>"/></td>
                    </tr>
                    <tr>
                        <td>Unidad:</td>
                        <td><select id="pro_uni">
                                <option value="0">Seleccione</option>
                                <option value="ROLLO">Rollo</option>
                                <option value="UNIDAD">Unidad</option>
                                <option value="KG">Kg</option>
                                <option value="M">M</option>
                            </select>
                        </td>
                    </tr> 
                    <tr>
                        <td>P.Bruto Rollo Terminado</td>
                        <td><input type="text" size="15"  id="pro_propiedad7"  value="<?php echo $rst['pro_propiedad7'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                    <tr>
                        <td>P.NETO Rollo Terminado</td>
                        <td><input type="text" size="15" readonly id="pro_medvul" value="<?php echo $rst['pro_medvul'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />Kg</td>
                    </tr>
                    <tr>
                        <td>Ancho:</td>
                        <td><input type="text" size="15"  id="pro_ancho"  value="<?php echo $rst['pro_ancho']*1000 ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />mm</td>
                    </tr>
                    <tr>
                        <td><label for="male" id="lblespesor">Espesor:</label></td>
                        <td><input type="text" size="15"  id="pro_espesor"  value="<?php echo $rst['pro_espesor'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />micras</td>
                    </tr>
                    <tr>
                        <td>Densidad:</td>
                        <td><input type="text" size="15"  id="pro_gramaje"  value="<?php echo $rst['pro_gramaje'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />gr/cm3</td>
                    </tr>
                    <tr>
                        <td>Peso Core Terminado:</td>
                        <td><input type="text" size="15"  id="pro_capa"  value="<?php echo $rst['pro_capa'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />KG</td>
                    </tr>

                    <tr>
                        <td>Peso Core Percha</td>
                        <td><input type="text" size="15"  id="pro_propiedad5"  value="<?php echo $rst['pro_propiedad5'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />Kg</td>
                    </tr>
                    <tr>
                        <td>PARADAS</td>
                        <td><input type="text" size="15"  id="pro_propiedad6"  value="<?php echo $rst['pro_propiedad6'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                    <tr>
                        <td>Largo:</td>
                        <td><input type="text" size="15" readonly id="pro_largo"  value="<?php echo $rst['pro_largo'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />m</td>
                    </tr>
                    <tr>
                        <td>Peso Bruto Rollo Madre:</td>
                        <td><input type="text" size="15" readonly id="pro_peso"  value="<?php echo $rst['pro_peso'] ?>"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>kg</td>
                    </tr>
                    <tr>
                        <td>Peso Neto Rollo Madre</td>
                        <td><input type="text" size="15" readonly id="pro_propiedad4"  value="<?php echo $rst['pro_propiedad4'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />Kg</td>
                    </tr>

                </tbody>
                <tbody id="tbmix">
                    <tr><td><br></td></tr>
                    <tr><td><br></td></tr>
                    <tr>
                        <td>&emsp;EXTRUSOR B </td>
                        <td><input type="text" size="12"  id="pro_por_tornillo1"  value="<?php echo $rst['pro_por_tornillo1'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="porcentaje_tornillos(this)"/><label>%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp1">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp1)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf1"  value="<?php echo $rst['pro_mf1'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />%</td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp2">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp2)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf2"  value="<?php echo $rst['pro_mf2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje1">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp3">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp3)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf3"  value="<?php echo $rst['pro_mf3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje2">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp4">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp4)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf4"  value="<?php echo $rst['pro_mf4'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje3">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp5">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp5)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf5"  value="<?php echo $rst['pro_mf5'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje4">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp6">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp6)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf6"  value="<?php echo $rst['pro_mf6'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje5">%</label></td>
                        <td></td>
                    <tr<td></td></tr>
                <td></td>
                <td><input type="text" size="10"  id="suma" readonly value="<?php echo $rst['suma'] ?>"/>%</td>
                </tr>
                </tbody>
                <tbody id="tbmix2">
                    <tr><td><br></td></tr>
                    <tr><td><br></td></tr>
                    <tr>
                        <td>&emsp;EXTRUSOR A</td>
                        <td><input type="text" size="12"  id="pro_por_tornillo2"  value="<?php echo $rst['pro_por_tornillo2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"  onchange="porcentaje_tornillos(this)"/><label>%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp7">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp7)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf7"  value="<?php echo $rst['pro_mf7'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />%</td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp8">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp8)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf8"  value="<?php echo $rst['pro_mf8'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje6">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp9">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp9)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf9"  value="<?php echo $rst['pro_mf9'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje7">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp10">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp10)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf10"  value="<?php echo $rst['pro_mf10'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje8">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp11">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp11)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf11"  value="<?php echo $rst['pro_mf11'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje9">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp12">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp12)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf12"  value="<?php echo $rst['pro_mf12'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje10">%</label></td>
                        <td></td>
                    <tr<td></td></tr>
                <td></td>
                <td><input type="text" size="10"  id="suma2" readonly value="<?php echo $rst['suma'] ?>"/>%</td>
                </tr>
                </tbody>
                <tbody id="tbmix3">
                    <tr><td><br></td></tr>
                    <tr><td><br></td></tr>
                    <tr>
                        <td>&emsp;EXTRUSOR C</td>
                        <td><input type="text" size="12"  id="pro_por_tornillo3"  value="<?php echo $rst['pro_por_tornillo3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="porcentaje_tornillos(this)"/><label>%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp13">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp13)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf13"  value="<?php echo $rst['pro_mf13'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />%</td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp14">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp14)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf14"  value="<?php echo $rst['pro_mf14'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje11">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp15">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp15)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf15"  value="<?php echo $rst['pro_mf15'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje12">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp16">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp16)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf16"  value="<?php echo $rst['pro_mf16'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje13">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp17">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp17)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf17"  value="<?php echo $rst['pro_mf17'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje14">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp18">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp18)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf18"  value="<?php echo $rst['pro_mf18'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje15">%</label></td>
                        <td></td>
                    <tr<td></td></tr>
                <td></td>
                <td><input type="text" size="10"  id="suma3" readonly value="<?php echo $rst['suma'] ?>"/>%</td>
                </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <?php
                            if ($x != 1) {
                                ?>
                                <button id="guardar" >Guardar</button>    
                                <?php
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

    var tipo =<?php echo $rst[pro_tipo] ?>;
    var emp =<?php echo $rst[emp_id] ?>;
    var mp1 =<?php echo $rst[pro_mp1] ?>;
    var mp2 =<?php echo $rst[pro_mp2] ?>;
    var mp3 =<?php echo $rst[pro_mp3] ?>;
    var mp4 =<?php echo $rst[pro_mp4] ?>;
    var mp5 =<?php echo $rst[pro_mp5] ?>;
    var mp6 =<?php echo $rst[pro_mp6] ?>;
    var mf1 =<?php echo $rst[pro_mf1] ?>;
    var mf2 =<?php echo $rst[pro_mf2] ?>;
    var mf3 =<?php echo $rst[pro_mf3] ?>;
    var mf4 =<?php echo $rst[pro_mf4] ?>;
    var mf5 =<?php echo $rst[pro_mf5] ?>;
    var mf6 =<?php echo $rst[pro_mf6] ?>;
    var mp7 =<?php echo $rst[pro_mp7] ?>;
    var mp8 =<?php echo $rst[pro_mp8] ?>;
    var mp9 =<?php echo $rst[pro_mp9] ?>;
    var mp10 =<?php echo $rst[pro_mp10] ?>;
    var mp11 =<?php echo $rst[pro_mp11] ?>;
    var mp12 =<?php echo $rst[pro_mp12] ?>;
    var mf7 =<?php echo $rst[pro_mf7] ?>;
    var mf8 =<?php echo $rst[pro_mf8] ?>;
    var mf9 =<?php echo $rst[pro_mf9] ?>;
    var mf10 =<?php echo $rst[pro_mf10] ?>;
    var mf11 =<?php echo $rst[pro_mf11] ?>;
    var mf12 =<?php echo $rst[pro_mf12] ?>;
    var mp13 =<?php echo $rst[pro_mp13] ?>;
    var mp14 =<?php echo $rst[pro_mp14] ?>;
    var mp15 =<?php echo $rst[pro_mp15] ?>;
    var mp16 =<?php echo $rst[pro_mp16] ?>;
    var mp17 =<?php echo $rst[pro_mp17] ?>;
    var mp18 =<?php echo $rst[pro_mp18] ?>;
    var mf13 =<?php echo $rst[pro_mf13] ?>;
    var mf14 =<?php echo $rst[pro_mf14] ?>;
    var mf15 =<?php echo $rst[pro_mf15] ?>;
    var mf16 =<?php echo $rst[pro_mf16] ?>;
    var mf17 =<?php echo $rst[pro_mf17] ?>;
    var mf18 =<?php echo $rst[pro_mf18] ?>;
    var fml =<?php echo $rst[pro_familia] ?>;


    $('#pro_tipo').val(tipo);
    $('#emp_id').val(emp);
    $('#pro_uni').val('<?php echo $rst[pro_uni] ?>');
    $('#pro_mp1').val(mp1);
    $('#pro_mp2').val(mp2);
    $('#pro_mp3').val(mp3);
    $('#pro_mp4').val(mp4);
    $('#pro_mp5').val(mp5);
    $('#pro_mp6').val(mp6)
    $('#pro_mp7').val(mp7);
    $('#pro_mp8').val(mp8);
    $('#pro_mp9').val(mp9);
    $('#pro_mp10').val(mp10);
    $('#pro_mp11').val(mp11);
    $('#pro_mp12').val(mp12)
    $('#pro_mp13').val(mp13);
    $('#pro_mp14').val(mp14);
    $('#pro_mp15').val(mp15);
    $('#pro_mp16').val(mp16);
    $('#pro_mp17').val(mp17);
    $('#pro_mp18').val(mp18)


    suma();
    suma2();
    suma3();
</script>