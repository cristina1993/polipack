<?php
include_once '../Clases/clsClase_etiquetas.php';
include_once '../Includes/permisos.php';
$Set = new Clase_etiquetas();
$id = $_GET[id];
$txt = $_GET[txt];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($Set->lista_una_etiqueta($id));
    $dt = explode('&', $rst[eti_elementos]);
} else {
    $id = 0;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                if (id == 0) {
                    $('#etiqueta_grande').hide();
                }
            });
            function save(id)
            {
                if ($('#eti_tamano').val() != 3) {
                    if ($('#orden_trabajo').attr('checked') == true) {
                        ot = 1;
                    } else {
                        ot = 0;
                    }
                    if ($('#ancho').attr('checked') == true) {
                        a = 1;
                    } else {
                        a = 0;
                    }
                    if ($('#peso').attr('checked') == true) {
                        p = 1;
                    } else {
                        p = 0;
                    }
                    if ($('#espesor').attr('checked') == true) {
                        e = 1;
                    } else {
                        e = 0;
                    }
                    if ($('#largo').attr('checked') == true) {
                        l = 1;
                    } else {
                        l = 0;
                    }
                    if ($('#cod_barras').attr('checked') == true) {
                        c = 1;
                    } else {
                        c = 0;
                    }
                    if ($('#estado').attr('checked') == true) {
                        est = 1;
                    } else {
                        est = 0;
                    }
                    if ($('#empresa').attr('checked') == true) {
                        em = 1;
                    } else {
                        em = 0;
                    }
                    var data = Array(
                            eti_descripcion.value.toUpperCase(),
                            eti_tamano.value.toUpperCase(),
                            ot + '&' + a + '&' + p + '&' + e + '&' + l + '&' + c + '&' + est + '&' + em
                            )
                } else {
                    if ($('#empresa_gr').attr('checked') == true) {
                        emp = 1;
                    } else {
                        emp = 0;
                    }
                    if ($('#fecha_gr').attr('checked') == true) {
                        fec = 1;
                    } else {
                        $('#fecha_gr').attr('checked', true);
                        fec = 0;
                    }
                    if ($('#espacio1_gr').attr('checked') == true) {
                        esp1 = 1;
                    } else {
                        esp1 = 0;
                    }
                    if ($('#espacio1_gr').attr('checked') == true) {
                        esp2 = 1;
                    } else {
                        esp2 = 0;
                    }
                    if ($('#cliente_gr').attr('checked') == true) {
                        cli = 1;
                    } else {
                        cli = 0;
                    }
                    if ($('#orden_trabajo_gr').attr('checked') == true) {
                        ot = 1;
                    } else {
                        ot = 1;
                    }
                    if ($('#espacio3_gr').attr('checked') == true) {
                        esp3 = 1;
                    } else {
                        esp3 = 0;
                    }
                    if ($('#espacio4_gr').attr('checked') == true) {
                        esp4 = 1;
                    } else {
                        esp4 = 0;
                    }
                    if ($('#espacio5_gr').attr('checked') == true) {
                        esp5 = 1;
                    } else {
                        esp5 = 0;
                    }
                    if ($('#espacio6_gr').attr('checked') == true) {
                        esp6 = 1;
                    } else {
                        esp6 = 0;
                    }
                    if ($('#tara_gr').attr('checked') == true) {
                        tara = 1;
                    } else {
                        tara = 0;
                    }
                    if ($('#peso_bruto_gr').attr('checked') == true) {
                        pbr = 1;
                    } else {
                        pbr = 0;
                    }
                    if ($('#peso_neto_gr').attr('checked') == true) {
                        pnt = 1;
                    } else {
                        pnt = 0;
                    }
                    if ($('#espesor_gr').attr('checked') == true) {
                        esp = 1;
                    } else {
                        esp = 0;
                    }
                    if ($('#ancho_gr').attr('checked') == true) {
                        anc = 1;
                    } else {
                        anc = 0;
                    }
                    if ($('#espacio7_gr').attr('checked') == true) {
                        esp7 = 1;
                    } else {
                        esp7 = 0;
                    }
                    if ($('#espacio8_gr').attr('checked') == true) {
                        esp8 = 1;
                    } else {
                        esp8 = 0;
                    }
                    if ($('#espacio9_gr').attr('checked') == true) {
                        esp9 = 1;
                    } else {
                        esp9 = 0;
                    }
                    if ($('#espacio10_gr').attr('checked') == true) {
                        esp10 = 1;
                    } else {
                        esp10 = 0;
                    }
                    if ($('#operador_gr').attr('checked') == true) {
                        op = 1;
                    } else {
                        op = 0;
                    }
                    if ($('#espacio11_gr').attr('checked') == true) {
                        esp11 = 1;
                    } else {
                        esp11 = 0;
                    }
                    if ($('#espacio12_gr').attr('checked') == true) {
                        esp12 = 1;
                    } else {
                        esp12 = 0;
                    }
                    if ($('#espacio13_gr').attr('checked') == true) {
                        esp13 = 1;
                    } else {
                        esp13 = 0;
                    }
                    if ($('#espacio14_gr').attr('checked') == true) {
                        esp14 = 1;
                    } else {
                        esp14 = 0;
                    }
                    if ($('#cod_barra_gr').attr('checked') == true) {
                        cdb = 1;
                    } else {
                        cdb = 0;
                    }
                    if ($('#estado_gr').attr('checked') == true) {
                        estg = 1;
                    } else {
                        estg = 0;
                    }
                    if ($('#observacion_gr').attr('checked') == true) {
                        obg = 1;
                    } else {
                        obg = 0;
                    }
                    var data = Array(
                            eti_descripcion.value.toUpperCase(),
                            eti_tamano.value.toUpperCase(),
                            emp + '&' + fec + '&' + esp1 + '&' + esp2 + '&' + cli + '&' + ot + '&' + esp3 + '&' + esp4 + '&' +
                            esp5 + '&' + esp6 + '&' + tara + '&' + pbr + '&' + pnt + '&' + esp + '&' + anc + '&' + esp7 + '&' +
                            esp8 + '&' + esp9 + '&' + esp10 + '&' + op + '&' + esp11 + '&' + esp12 + '&' + esp13 + '&' + 
                            esp14 + '&' + cdb+ '&' + estg+'&'+obg
                            );
                }
                var fields = Array();
                $("#tbl_form").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.post("actions_etiquetas.php", {op: 0, 'data[]': data, id: id, 'fields[]': fields},
                function (dt) {
                    if (dt == 0)
                    {
                        cancelar();
                    } else {
                        alert(dt);
                    }
                });


            }

            function cancelar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_etiquetas.php?search=1&txt=<?php echo $txt ?>';
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
            function mostrar_etiqueta() {
                if ($('#eti_tamano').val() == '3') {
                    $('#etiqueta').hide();
                    $('#etiqueta_grande').show();
                } else {
                    $('#etiqueta').show();
                    $('#etiqueta_grande').hide();
                }
            }
        </script>
    </head>
    <style>
        *{
            text-transform: uppercase;
        }
    </style>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>

        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="3" >ETIQUETAS<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
            </thead>                    
            <tr>
                <td>Descripcion:</td>
                <td><input type="text" name="eti_descripcion" id="eti_descripcion" value="<?php echo $rst[eti_descripcion] ?>" /></td>
            </tr>
            <tr>
                <td>Tamaño:</td>
                <td>
                    <select name="eti_tamano" id="eti_tamano" onchange="mostrar_etiqueta()">
                        <option value="1">1X2"</option>
                        <option value="2">2X4"</option>
                        <option value="3">Grande</option>
                    </select>
                </td>
            </tr>
            <thead>
                <tr>
                    <th colspan="3">Elementos</th>
                </tr>
            </thead> 
            <tbody id="etiqueta">
                <tr>
                    <td>Nombre Empresa</td>
                    <td><input type="checkbox" id="empresa"/></td>
                </tr>
                <tr>
                    <td>#Orden Trabajo</td>
                    <td><input type="checkbox" id="orden_trabajo"/></td>
                </tr>
                <tr>
                    <td>Ancho</td>
                    <td><input type="checkbox" id="ancho"/></td>
                </tr>
                <tr>
                    <td>Peso</td>
                    <td><input type="checkbox" id="peso"/></td>
                </tr>
                <tr>
                    <td>Espesor</td>
                    <td><input type="checkbox" id="espesor"/></td>
                </tr>
                <tr>
                    <td>Largo</td>
                    <td><input type="checkbox" id="largo"/></td>
                </tr>
                <tr>
                    <td>Cod.Barras</td>
                    <td><input type="checkbox" id="cod_barras"/></td>
                </tr>
                <tr>
                    <td>Estado</td>
                    <td><input type="checkbox" id="estado"/></td>
                </tr>
            </tbody>
            <tbody id="etiqueta_grande">
                <tr>
                    <td>Nombre Empresa</td>
                    <td><input type="checkbox" id="empresa_gr"/></td>
                </tr>
                <tr>
                    <td>Fecha</td>
                    <td><input type="checkbox" id="fecha_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio1</td>
                    <td><input type="checkbox" id="espacio1_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio2</td>
                    <td><input type="checkbox" id="espacio2_gr"/></td>
                </tr>
                <tr>
                    <td>Cliente</td>
                    <td><input type="checkbox" id="cliente_gr"/></td>
                </tr>
                <tr>
                    <td>#Orden Trabajo</td>
                    <td><input type="checkbox" id="orden_trabajo_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio3</td>
                    <td><input type="checkbox" id="espacio3_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio4</td>
                    <td><input type="checkbox" id="espacio4_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio5</td>
                    <td><input type="checkbox" id="espacio5_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio6</td>
                    <td><input type="checkbox" id="espacio6_gr"/></td>
                </tr>
                <tr>
                    <td>Tara</td>
                    <td><input type="checkbox" id="tara_gr"/></td>
                </tr>
                <tr>
                    <td>Peso bruto</td>
                    <td><input type="checkbox" id="peso_bruto_gr"/></td>
                </tr>
                <tr>
                    <td>Peso neto</td>
                    <td><input type="checkbox" id="peso_neto_gr"/></td>
                </tr>
                <tr>
                    <td>Espesor</td>
                    <td><input type="checkbox" id="espesor_gr"/></td>
                </tr>
                <tr>
                    <td>Ancho</td>
                    <td><input type="checkbox" id="ancho_gr"/></td>
                </tr>
                <tr hidden="">
                    <td>Espacio7</td>
                    <td><input type="checkbox" id="espacio7_gr"/></td>
                </tr>
                <tr>
                    <td>Largo</td>
                    <td><input type="checkbox" id="espacio8_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio9</td>
                    <td><input type="checkbox" id="espacio9_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio10</td>
                    <td><input type="checkbox" id="espacio10_gr"/></td>
                </tr>

                <tr>
                    <td>Operador</td>
                    <td><input type="checkbox" id="operador_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio11</td>
                    <td><input type="checkbox" id="espacio11_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio12</td>
                    <td><input type="checkbox" id="espacio12_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio13</td>
                    <td><input type="checkbox" id="espacio13_gr"/></td>
                </tr>
                <tr>
                    <td>Espacio14</td>
                    <td><input type="checkbox" id="espacio14_gr"/></td>
                </tr>
                <tr>
                    <td>Codigo Barras</td>
                    <td><input type="checkbox" id="cod_barra_gr"/></td>
                </tr>
                <tr>
                    <td>Estado</td>
                    <td><input type="checkbox" id="estado_gr"/></td>
                </tr>
                <tr>
                    <td>Observaciones</td>
                    <td><input type="checkbox" id="observacion_gr"/></td>
                </tr>
            </tbody>
            <tr>
                <td colspan="3">
                    <?php
                    if ($Prt->add == 0 || $Prt->edition == 0) {
                        ?>
                        <button id="save" onclick="save(<?php echo $id ?>)">Guardar</button>
                    <?php }
                    ?>
                    <button id="cancel" onclick="cancelar()">Cancelar</button>
                </td>
            </tr>                    

        </table>
    </body>
</html>
<script>
    var tam = '<?php echo $rst[eti_tamano] ?>';
    $('#eti_tamano').val(tam);
    if (tam != '3') {
        var ot = '<?php echo $dt[0] ?>';
        var a = '<?php echo $dt[1] ?>';
        var p = '<?php echo $dt[2] ?>';
        var e = '<?php echo $dt[3] ?>';
        var l = '<?php echo $dt[4] ?>';
        var c = '<?php echo $dt[5] ?>';
        var est = '<?php echo $dt[6] ?>';
        var em = '<?php echo $dt[7] ?>';
        if (ot == 0) {
            $('#orden_trabajo').attr('checked', false);
        } else {
            $('#orden_trabajo').attr('checked', true);
        }
        if (a == 0) {
            $('#ancho').attr('checked', false);
        } else {
            $('#ancho').attr('checked', true);
        }
        if (p == 0) {
            $('#peso').attr('checked', false);
        } else {
            $('#peso').attr('checked', true);
        }
        if (e == 0) {
            $('#espesor').attr('checked', false);
        } else {
            $('#espesor').attr('checked', true);
        }
        if (l == 0) {
            $('#largo').attr('checked', false);
        } else {
            $('#largo').attr('checked', true);
        }
        if (c == 0) {
            $('#cod_barras').attr('checked', false);
        } else {
            $('#cod_barras').attr('checked', true);
        }
        if (est == 0) {
            $('#estado').attr('checked', false);
        } else {
            $('#estado').attr('checked', true);
        }
        if (em == 0) {
            $('#empresa').attr('checked', false);
        } else {
            $('#empresa').attr('checked', true);
        }
    } else {
        
        var emp = '<?php echo $dt[0] ?>';
        var fec = '<?php echo $dt[1] ?>';
        var esp1 = '<?php echo $dt[2] ?>';
        var esp2 = '<?php echo $dt[3] ?>';
        var cli = '<?php echo $dt[4] ?>';
        var ot = '<?php echo $dt[5] ?>';
        var esp3 = '<?php echo $dt[6] ?>';
        var esp4 = '<?php echo $dt[7] ?>';
        var esp5 = '<?php echo $dt[8] ?>';
        var esp6 = '<?php echo $dt[9] ?>';
        var tara = '<?php echo $dt[10] ?>';
        var pbr = '<?php echo $dt[11] ?>';
        var pnt = '<?php echo $dt[12] ?>';
        var esp = '<?php echo $dt[13] ?>';
        var anc = '<?php echo $dt[14] ?>';
        var esp7 = '<?php echo $dt[15] ?>';
        var esp8 = '<?php echo $dt[16] ?>';
        var esp9 = '<?php echo $dt[17] ?>';
        var esp10 = '<?php echo $dt[18] ?>';
        var op = '<?php echo $dt[19] ?>';
        var esp11 = '<?php echo $dt[20] ?>';
        var esp12 = '<?php echo $dt[21] ?>';
        var esp13 = '<?php echo $dt[22] ?>';
        var esp14 = '<?php echo $dt[23] ?>';
        var cdb = '<?php echo $dt[24] ?>';
        var estg = '<?php echo $dt[25] ?>';
        var obg = '<?php echo $dt[26] ?>';
        if (emp == 0) {
            $('#empresa_gr').attr('checked', false);
        } else {
            $('#empresa_gr').attr('checked', true);
        }
        if (fec == 0) {
            $('#fecha_gr').attr('checked', false);
        } else {
            $('#fecha_gr').attr('checked', true);
        }
        if (esp1 == 0) {
            $('#espacio1_gr').attr('checked', false);
        } else {
            $('#espacio1_gr').attr('checked', true);
        }
        if (esp2 == 0) {
            $('#espacio2_gr').attr('checked', false);
        } else {
            $('#espacio2_gr').attr('checked', true);
        }
        if (cli == 0) {
            $('#cliente_gr').attr('checked', false);
        } else {
            $('#cliente_gr').attr('checked', true);
        }
        if (ot == 0) {
            $('#orden_trabajo_gr').attr('checked', false);
        } else {
            $('#orden_trabajo_gr').attr('checked', true);
        }
        if (esp3 == 0) {
            $('#espacio3_gr').attr('checked', false);
        } else {
            $('#espacio3_gr').attr('checked', true);
        }
        if (esp4 == 0) {
            $('#espacio4_gr').attr('checked', false);
        } else {
            $('#espacio4_gr').attr('checked', true);
        }
        if (esp5 == 0) {
            $('#espacio5_gr').attr('checked', false);
        } else {
            $('#espacio5_gr').attr('checked', true);
        }
        if (esp6 == 0) {
            $('#espacio6_gr').attr('checked', false);
        } else {
            $('#espacio6_gr').attr('checked', true);
        }
        if (tara == 0) {
            $('#tara_gr').attr('checked', false);
        } else {
            $('#tara_gr').attr('checked', true);
        }
        if (pbr == 0) {
            $('#peso_bruto_gr').attr('checked', false);
        } else {
            $('#peso_bruto_gr').attr('checked', true);
        }
        if (pnt == 0) {
            $('#peso_neto_gr').attr('checked', false);
        } else {
            $('#peso_neto_gr').attr('checked', true);
        }
        if (esp == 0) {
            $('#espesor_gr').attr('checked', false);
        } else {
            $('#espesor_gr').attr('checked', true);
        }
        if (anc == 0) {
            $('#ancho_gr').attr('checked', false);
        } else {
            $('#ancho_gr').attr('checked', true);
        }
        if (esp7 == 0) {
            $('#espacio7_gr').attr('checked', false);
        } else {
            $('#espacio7_gr').attr('checked', true);
        }
        if (esp8 == 0) {
            $('#espacio8_gr').attr('checked', false);
        } else {
            $('#espacio8_gr').attr('checked', true);
        }
        if (esp9 == 0) {
            $('#espacio9_gr').attr('checked', false);
        } else {
            $('#espacio9_gr').attr('checked', true);
        }
        if (esp10 == 0) {
            $('#espacio10_gr').attr('checked', false);
        } else {
            $('#espacio10_gr').attr('checked', true);
        }
        if (op == 0) {
            $('#operador_gr').attr('checked', false);
        } else {
            $('#operador_gr').attr('checked', true);
        }
        if (esp11 == 0) {
            $('#espacio11_gr').attr('checked', false);
        } else {
            $('#espacio11_gr').attr('checked', true);
        }
        if (esp12 == 0) {
            $('#espacio12_gr').attr('checked', false);
        } else {
            $('#espacio12_gr').attr('checked', true);
        }
        if (esp13 == 0) {
            $('#espacio13_gr').attr('checked', false);
        } else {
            $('#espacio13_gr').attr('checked', true);
        }
        if (esp14 == 0) {
            $('#espacio14_gr').attr('checked', false);
        } else {
            $('#espacio14_gr').attr('checked', true);
        }
        if (cdb == 0) {
            $('#cod_barra_gr').attr('checked', false);
        } else {
            $('#cod_barra_gr').attr('checked', true);
        }
         if (estg == 0) {
            $('#estado_gr').attr('checked', false);
        } else {
            $('#estado_gr').attr('checked', true);
        }
         if (obg == 0) {
            $('#observacion_gr').attr('checked', false);
        } else {
            $('#observacion_gr').attr('checked', true);
        }
    }
    mostrar_etiqueta();
</script>