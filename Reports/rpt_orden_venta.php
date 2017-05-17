<?php
//include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$id = $_GET [id];
$x = $_GET[x];
$fec1 = $_GET[desde];
$fec2 = $_GET[hasta];
$txt = $_GET[txt];

if (isset($_GET [id])) {
    $rst = pg_fetch_array($Set->lista_una_orden_produccion($id));
    $ped_id = $rst[ped_id];
    $rst_pro1 = pg_fetch_array($Set->lista_un_producto($rst[pro_id]));
    $rst_pro2 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro_secundario]));
    $rst_pro3 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro3]));
    $rst_pro4 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro4]));
    $rst_cli = pg_fetch_array($Set->lista_clientes_codigo($rst[cli_id]));
    $nombre = $rst_cli[cli_raz_social];
    $cli_id = $rst_cli[cli_id];
    $rst_emp1 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp1])); ///EMPA1
    $rst_emp2 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp2]));  ///core
    $rst_emp3 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp3]));  ///des 1
    $rst_emp4 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp4])); ///des 2
    $rst_emp5 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp5]));  ///des 3
    $rst_emp6 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp6]));  ///des 4
    $rst_emp7 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp7]));  ///EMPA2
    $rst_emp8 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp8]));  ///core
    $rst_emp9 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp9]));  ///des 1
    $rst_emp10 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp10]));  ///des 2
    $rst_emp11 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp11]));  ///des 3
    $rst_emp12 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp12]));  ///des 4
    $rst_emp13 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp13]));  ///EMPA3
    $rst_emp14 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp14]));  ///core
    $rst_emp15 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp15]));  ///des 1
    $rst_emp16 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp16]));  ///des 2
    $rst_emp17 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp17]));  ///des 3
    $rst_emp18 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp18]));  ///des 4
    $rst_emp19 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp19]));  ///EMPA4
    $rst_emp20 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp20]));  ///core
    $rst_emp21 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp21]));  ///des 1
    $rst_emp22 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp22]));  ///des 2
    $rst_emp23 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp23]));  ///des 3
    $rst_emp24 = pg_fetch_array($Set->lista_des_mp($rst[pro_mp24]));  ///des 4
    $rst_mp1 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp1]));
    $rst_mp2 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp2]));
    $rst_mp3 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp3]));
    $rst_mp4 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp4]));
    $rst_mp5 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp5]));
    $rst_mp6 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp6]));
    $rst_mp7 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp7]));
    $rst_mp8 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp8]));
    $rst_mp9 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp9]));
    $rst_mp10 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp10]));
    $rst_mp11 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp11]));
    $rst_mp12 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp12]));
    $rst_mp13 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp13]));
    $rst_mp14 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp14]));
    $rst_mp15 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp15]));
    $rst_mp16 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp16]));
    $rst_mp17 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp17]));
    $rst_mp18 = pg_fetch_array($Set->lista_des_mp($rst[ord_mp18]));
    $det = 0;
    $det_id = $rst[det_id];
    $rst[ord_ancho_util] = $rst[ord_anc_total] - ($rst[ord_refilado] * 2);
    $rst[sumakg] = $rst[ord_kgtotal] + $rst[ord_kgtotal2] + $rst[ord_kgtotal3] + $rst[ord_kgtotal4] + $rst[ord_kgtotal_rep];
    $rst[sumam] = $rst[ord_pri_ancho] * $rst[ord_pri_carril] + $rst[ord_sec_ancho] * $rst[ord_sec_carril] + $rst[ord_ancho3] * $rst[ord_carril3] + $rst[ord_ancho4] * $rst[ord_carril4] + $rst[ord_rep_ancho] * $rst[ord_rep_carril];
    $rst[tot_por_tornillo1] = $rst[ord_mf1] + $rst[ord_mf2] + $rst[ord_mf3] + $rst[ord_mf4] + $rst[ord_mf5] + $rst[ord_mf6];
    $rst[tot_por_tornillo2] = $rst[ord_mf7] + $rst[ord_mf8] + $rst[ord_mf9] + $rst[ord_mf10] + $rst[ord_mf11] + $rst[ord_mf12];
    $rst[tot_por_tornillo3] = $rst[ord_mf13] + $rst[ord_mf14] + $rst[ord_mf15] + $rst[ord_mf16] + $rst[ord_mf17] + $rst[ord_mf18];
    $rst[tot_kg_tornillo1] = $rst[ord_kg1] + $rst[ord_kg2] + $rst[ord_kg3] + $rst[ord_kg4] + $rst[ord_kg5] + $rst[ord_kg6];
    $rst[tot_kg_tornillo2] = $rst[ord_kg7] + $rst[ord_kg8] + $rst[ord_kg9] + $rst[ord_kg10] + $rst[ord_kg11] + $rst[ord_kg12];
    $rst[tot_kg_tornillo3] = $rst[ord_kg13] + $rst[ord_kg14] + $rst[ord_kg15] + $rst[ord_kg16] + $rst[ord_kg17] + $rst[ord_kg18];
    $rst[unidad] = 1;
    if ($rst[ord_bodega] == 1) {
        $bodega = 'SEMIELABORADO';
    } else if ($rst[ord_bodega] == 2) {
        $bodega = 'TERMINADO';
    }
    $por = ($rst_prod[peso] * 100) / $rst[ord_kgtotal];
    if ($rst_prod[peso] == '') {
        $est = 'REGISTRADO';
    } else if ($por >= 90) {
        $est = 'TERMINADO';
    } else {
        $est = 'PRODUCCION';
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            var fec1 = '<?php echo $fec1 ?>';
            var fec2 = '<?php echo $fec2 ?>';
            var txt = '<?php echo $txt ?>';
            var det = '<?php echo $det ?>';
            var det_id = '<?php echo $det_id ?>';
            var ped_id = '<?php echo $ped_id ?>';

            function cancelar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_orden_ecocambrella.php?desde=' + fec1 + '&hasta=' + fec2 + '&txt=' + txt;
            }

            function imprimir()
            {
                botonimprimir.hidden = true
                window.print()
                botonimprimir.hidden = false
            }


        </script>
        <style>
            tbody{
                float:left;
            }
            *{
                text-transform: uppercase; 
            }
            .cerrar{
                float:right;
                width:24px;
                font-weight:bolder; 
                padding:3px; 
                border-radius:2px; 
                background: linear-gradient(to bottom, #f0b7a1 0%,#8c3310 50%,#752201 51%,#bf6e4e 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0b7a1', endColorstr='#bf6e4e',GradientType=0 ); /* IE6-9 */

            }
            .cerrar:hover{
                /*    background: brown; */
                background: linear-gradient(to bottom, #f0b7a1 10%,#8c3310 45%,#752201 51%,#bf6e4e 90%); /* W3C */
            }
            #tbl thead th{ //Para que se carge sin mostrar sin estrilo el encabezado
                           color:white; 
                           padding:3px; 
                           font-size:12px; 
                           background-color: #616975;
                           filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,StartColorStr='#727a86', EndColorStr='#505864');
                           border-bottom: 0.001em solid #33373d;
                           border-right:0.001em solid #33373d;
                           height: 1.5em;
                           line-height: 1.5em;
                           font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                           color: #fff;
                           text-shadow: 0px 1px 0px rgba(0,0,0,.5);
                           cursor:pointer;




            }
            table{
                border:solid 1px #000;
                font-size:10px;
                font-family:'Arial'; 
                background: white;
                width: 100%;
            }
            .font{
                font-size:10px;
                font-weight:bolder; 
            }
            .sbtitle{
                font-size:12px; 
                font-weight:bolder; 
                text-align: center;
            }
        </style>
    </head>
    <body>    

        <table id="tbl_form" style="width: 70%" >
            <thead>
                <tr><th class="sbtitle" colspan="20">ORDEN DE PRODUCCION<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
            </thead>
            <tr><td colspan="10" class="sbtitle" >DATOS GENERALES</td></tr>
            <tr>
                <td class="font">Estatus:</td>
                <td class="font"><?php echo $est ?></td>
            </tr>

            <!------------------------------------------------------------------------- DATOS GENERALES ----------------------------------------------------------------------------------->
            <tr>
                <td colspan="10">
                    <table colspan="10">
                        <tr>
                            <td class="font">Orden # :</td>
                            <td><?php echo $rst['ord_num_orden'] ?></td>   
                            <td class="font">Fecha Pedido:</td>
                            <td><?php echo $rst[ord_fec_pedido] ?></td>
                            <td class="font">Bodega:</td>
                            <td><?php echo $bodega ?></td>
                            <td class="font">Fecha Entrega:</td>
                            <td><?php echo $rst[ord_fec_entrega] ?></td>
                            <td class="font">Cliente:</td>
                            <td><?php echo $nombre ?></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr ><td colspan="10" class="sbtitle" >PRODUCTOS</td></tr> 
            <tr>
                <td colspan="10">
                    <table>
                        <!------------------------------------------------------------------------- PRimera linea  ----------------------------------------------------------------------------------->                            
                        <!------------------------------------------------------------------------- PRimera linea  ----------------------------------------------------------------------------------->                            
                        <!------------------------------------------------------------------------- PRimera linea  ----------------------------------------------------------------------------------->                            
                        <tr>
                            <td class="font">PRODUCTO PRIMARIO:</td>
                            <td><?php echo $rst_pro1[pro_descripcion] ?></td>
                            <td style="width: 30px" class="font" align="right" >#Rollos:</td>
                            <td style="width: 60px" align="right"><?php echo $rst[ord_num_rollos] ?></td>
                            <td style="width: 60px" class="font" align="right">Peso</td>
                            <td style="width: 60px" align="right"><?php echo str_replace(',', '', number_format($rst[ord_kgtotal], 2)) . ' kg' ?></td>
                            <td style="width: 60px" class="font" align="right">Ancho</td>
                            <td style="width: 60px" align="right"><?php echo str_replace(',', '', number_format($rst[ord_pri_ancho], 2)) . ' m' ?></td>
                            <td style="width: 60px" class="font" align="right">Carriles:</td>
                            <td style="width: 30px" align="right"><?php echo $rst[ord_pri_carril] ?></td>
                        </tr>
                        <!------------------------------------------------------------------------- SECUNDARIO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- SECUNDARIO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- SECUNDARIO   ----------------------------------------------------------------------------------->                             
                        <tr>
                            <td class="font">Producto Secundario :</td>
                            <td><?php echo $rst_pro2[pro_descripcion] ?></td>
                            <td class="font" align="right">#Rollos:</td>
                            <td align="right" ><?php echo $rst[ord_num_rollos2] ?></td>
                            <td class="font" align="right">Peso</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kgtotal2], 2)) . ' kg' ?></td>
                            <td class="font" align="right">Ancho:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_sec_ancho], 2)) . '  m' ?></td>
                            <td class="font" align="right">Carriles:</td>
                            <td align="right"><?php echo $rst[ord_sec_carril] ?></td>
                        </tr>

                        <!------------------------------------------------------------------------- PRODUCTO TERCERO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- PRODUCTO TERCERO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- PRODUCTO TERCERO   ----------------------------------------------------------------------------------->                             

                        <tr>
                            <td class="font">PRODUCTO TERCERO:</td>
                            <td><?php echo $rst_pro3[pro_descripcion] ?></td>
                            <td class="font" align="right" >#Rollos:</td>
                            <td align="right"><?php echo $rst[ord_num_rollos3] ?></td>
                            <td class="font" align="right">Peso</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kgtotal3], 2)) . ' kg' ?></td>
                            <td class="font" align="right">Ancho:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_ancho3], 2)) . '  m' ?></td>
                            <td class="font" align="right">Carriles:</td>
                            <td align="right"><?php echo $rst[ord_carril3] ?></td>

                        </tr>
                        <!------------------------------------------------------------------------- CUARTO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- CUARTO   ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- CUARTO   ----------------------------------------------------------------------------------->                             

                        <tr>
                            <td class="font">PRODUCTO CUARTO:</td>
                            <td><?php echo $rst_pro4[pro_descripcion] ?></td>
                            <td class="font" align="right" >#Rollos:</td>
                            <td align="right"><?php echo $rst[ord_num_rollos4] ?></td>
                            <td class="font" align="right">Peso</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kgtotal4], 2)) . ' kg ' ?></td>
                            <td class="font" align="right">Ancho:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_ancho4], 2)) . ' m' ?></td>
                            <td class="font" align="right">Carriles:</td>
                            <td align="right"><?php echo $rst[ord_carril4] ?></td>

                        </tr>
                        <!------------------------------------------------------------------------- REPROCESO    ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- REPROCESO    ----------------------------------------------------------------------------------->                             
                        <!------------------------------------------------------------------------- REPROCESO    ----------------------------------------------------------------------------------->                             

                        <tr>
                            <td class="font" colspan="4">Reproceso :</td>
                            <td class="font" align="right">Peso</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kgtotal_rep], 2)) . ' kg' ?></td> 
                            <td class="font" align="right">Ancho:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_rep_ancho], 2)) . '  m' ?></td>
                            <td class="font" align="right">Carriles:</td>
                            <td align="right"><?php echo $rst[ord_rep_carril] ?></td>

                        </tr>

                        <tr>
                            <td class="font">Largo: <?php echo str_replace(',', '', number_format($rst[ord_largo], 2)) . '  m' ?></td>
                            <td class="font">Gramaje: <?php echo str_replace(',', '', number_format($rst[ord_gramaje], 2)) . 'gr/m2' ?></td>   
                            <!------------------------------------------------------------------------- SUMA   ----------------------------------------------------------------------------------->                             
                            <!------------------------------------------------------------------------- SUMA   ----------------------------------------------------------------------------------->                             
                            <!------------------------------------------------------------------------- SUMA   ----------------------------------------------------------------------------------->                             
                            <td></td>
                            <td></td>
                            <td class="font">Total:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[sumakg], 2)) . ' kg' ?>  </td>   
                            <td></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[sumam], 2)) . '  m' ?></td>   
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>                
                        <tr>
                            <td class="font">Ancho Total: <?php echo str_replace(',', '', number_format($rst[ord_anc_total], 2)) . '  m' ?> </td>
                            <td class="font">Refilado: <?php echo str_replace(',', '', number_format($rst[ord_refilado], 2)) . '  m' ?> </td>
                            <td class="font">Ancho Util: <?php echo str_replace(',', '', number_format($rst[ord_ancho_util], 2)) . '  m' ?></td>
                        </tr>

                    </table>    
                </td>
            </tr>
            <!------------------------------------------------------------------------- EMPAQUES ----------------------------------------------------------------------------------->
            <!------------------------------------------------------------------------- EMPAQUES ----------------------------------------------------------------------------------->
            <!------------------------------------------------------------------------- EMPAQUES ----------------------------------------------------------------------------------->
            <tr>
                <td colspan="3" class="sbtitle" > EMPAQUE PROD. PRIMARIO</td>
                <td colspan="3" class="sbtitle" > EMPAQUE PROD. SECUNDARIO</td>
                <td colspan="3" class="sbtitle" > EMPAQUE PROD. TERCERO</td>
                <td colspan="3" class="sbtitle" > EMPAQUE PROD. CUARTO</td>
            </tr>          
            <tr>
                <td colspan="3">             
                    <table>    
                        <tr>
                            <td class="font">EMPAQUE:</td>
                            <td><?php echo $rst_emp1[mp_referencia] ?></td>
                        </tr> 
                        <tr>
                            <td class="font">ROLLOS POR EMPQ.:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['opp_velocidad'], 2)) ?></td>
                        </tr>
                        <tr>
                            <td class="font">CORE:</td>
                            <td><?php echo $rst_emp2[mp_referencia] ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp3[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt3'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp4[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt4'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp5[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt5'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp6[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt6'], 2)) ?></td>                           
                        </tr> 
                        <tr>
                            <td class="font">PESO NETO:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf1'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td class="font">PESO CORE:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf2'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td class="font">PESO BRUTO:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf3'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td></br></td>
                        </tr>
                    </table>
                </td>
                <td colspan="3">    
                    <!------------------------------------------------------------------------- EMPAQUES2 ----------------------------------------------------------------------------------->
                    <table>    
                        <tr>
                            <td class="font">EMPAQUE:</td>
                            <td><?php echo $rst_emp7[mp_referencia] ?></td>
                        </tr> 
                        <tr>
                            <td class="font">ROLLOS POR EMPQ.:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['opp_velocidad2'], 2)) ?></td>
                        </tr>
                        <tr>
                            <td class="font">CORE:</td>
                            <td><?php echo $rst_emp8[mp_referencia] ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp9[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt9'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp10[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt10'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp11[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt11'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp12[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt12'], 2)) ?></td>                            
                        </tr> 
                        <tr>
                            <td class="font">PESO NETO:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf4'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td class="font">PESO CORE:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf5'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td class="font">PESO BRUTO:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf6'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td></br></td>
                        </tr>
                    </table>
                </td>

                <td colspan="3"> 
                    <!------------------------------------------------------------------------- EMPAQUES3 ----------------------------------------------------------------------------------->
                    <table>    
                        <tr>
                            <td class="font">EMPAQUE:</td>
                            <td><?php echo $rst_emp13[mp_referencia] ?></td>
                        </tr> 
                        <tr>
                            <td class="font">ROLLOS POR EMPQ.:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['opp_velocidad3'], 2)) ?></td>
                        </tr>
                        <tr>
                            <td class="font">CORE:</td>
                            <td><?php echo $rst_emp14[mp_referencia] ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp15[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt15'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp16[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt16'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp17[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt17'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp18[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt18'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td class="font">PESO NETO:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf7'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td class="font">PESO CORE:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf8'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td class="font">PESO BRUTO:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf9'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td></br></td>
                        </tr>
                    </table>
                </td>
                <td colspan="3">  
                    <!------------------------------------------------------------------------- EMPAQUES4 ----------------------------------------------------------------------------------->
                    <table>    
                        <tr>
                            <td class="font">EMPAQUE:</td>
                            <td><?php echo $rst_emp19[mp_referencia] ?></td>
                        </tr> 
                        <tr>
                            <td class="font">ROLLOS POR EMPQ.:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['opp_velocidad4'], 2)) ?></td>
                        </tr>
                        <tr>
                            <td class="font">CORE:</td>
                            <td><?php echo $rst_emp20[mp_referencia] ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp21[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt21'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp22[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt22'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp23[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt23'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td><?php echo $rst_emp24[mp_referencia] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['mp_cnt24'], 2)) ?></td>
                        </tr> 
                        <tr>
                            <td class="font">PESO NETO:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf10'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td class="font">PESO CORE:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf11'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td class="font">PESO BRUTO:</td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst['pro_mf12'], 2)) . ' kg' ?></td>
                        </tr>
                        <tr>
                            <td></br></td>
                        </tr>
                    </table>
                </td>
                <!------------------------------------------------------------------------- MATERIA PRIMA ----------------------------------------------------------------------------------->

            <tr>
                <td colspan="9"class="sbtitle" >FORMULACION</td>
                <td colspan="10"class="sbtitle" >CONSUMO</td>
            </tr>
            <td colspan="3">
                <table>
                    <!------------------------------------------------------------------------- TORNILLO 1 ----------------------------------------------------------------------------------->
                    <tr>
                        <td class="font">Extrusora B: </td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_por_tornillo1], 2)) . '%' ?> </td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp1[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf1], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg1], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp2[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf2], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg2], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp3[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf3], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg3], 2)) ?> kg</td>
                    </tr>
                    <tr>        
                        <td><?php echo $rst_mp4[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf4], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg4], 2)) ?> kg</td>
                    </tr>
                    <tr>        
                        <td><?php echo $rst_mp5[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf5], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg5], 2)) ?> kg</td>
                    </tr>
                    <tr>        
                        <td><?php echo $rst_mp6[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf6], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg6], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td class="font">Total:</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[tot_por_tornillo1], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[tot_kg_tornillo1], 2)) ?> kg</td>
                    </tr>
                </table>
            </td>
            <!------------------------------------------------------------------------- TORNILLO 2 ----------------------------------------------------------------------------------->
            <td colspan="3">
                <table>
                    <tr>
                        <td class="font">Extrusora A: </td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_por_tornillo2], 2)) ?>%</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp7[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf7], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg7], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp8[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf8], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg8], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp9[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf9], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg9], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp10[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf10], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg10], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp11[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf11], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg11], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp12[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf12], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg12], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td class="font">Total:</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[tot_por_tornillo2], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[tot_kg_tornillo2], 2)) ?> kg</td>
                    </tr>
                </table>
            </td>
            <!------------------------------------------------------------------------- TORNILLO 3 ----------------------------------------------------------------------------------->

            <td colspan="3">
                <table>
                    <tr>
                        <td class="font">Extrusora C: </td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_por_tornillo3], 2)) ?>%</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp13[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf13], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg13], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp14[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf14], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg14], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp15[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf15], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg15], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp16[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf16], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg16], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp17[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf17], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg17], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td><?php echo $rst_mp18[mp_referencia] ?></td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_mf18], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_kg18], 2)) ?> kg</td>
                    </tr>
                    <tr>
                        <td class="font">Total:</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[tot_por_tornillo3], 2)) ?>%</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($rst[tot_kg_tornillo3], 2)) ?> kg</td>
                    </tr>
                </table>
            </td>
            <!-------------------------------------------------------------Consumo--------------------------------------------------------------->
            <td colspan="3">
                <table>
                    <tr>
                        <td class="font">Codigo</td>
                        <td class="font">Desc</td>
                        <td class="font">Peso(Kg)</td>
                        <td class="font">C.Unit</td>
                    </tr>
                    <?php
                    $cns_cons = $Set->lista_consumo_mp($rst['ord_num_orden']);
                    $n = 0;
                    while ($rst_cns = pg_fetch_array($cns_cons)) {
                        $n++;
                        $df=explode('-',$rst[ord_fec_pedido]);
                        $ultimo_dia = 28;
                        while (checkdate($df[1], $ultimo_dia + 1, $df[0])) {
                            $ultimo_dia++;
                        }
                        $fec=$df[0].'-'.$df[1].'-'.$ultimo_dia;
                        $rst_c = pg_fetch_array($Set->lista_costo_mp($rst_cns[mp_id],$fec));
                        ?>
                        <tr>
                            <td style="width: 60px"><?php echo $rst_cns[mp_codigo] ?></td>
                            <td style="width: 60px"><?php echo $rst_cns[mp_referencia] ?></td>
                            <td align="right" style="width: 30px"><?php echo str_replace(',', '', number_format($rst_cns[mov_cantidad], 2)) ?></td>
                            <td align="right" style="width: 30px"><?php echo str_replace(',', '', number_format($rst_c[cmp_valor], 2)) ?></td>
                        </tr>
                        <?php
                        $tot_cons+=$rst_cns[mov_cantidad];
                    }
                    while ($n < 6) {
                        $n++;
                        ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td class="font" colspan="2">Total</td>
                        <td align="right"><?php echo str_replace(',', '', number_format($tot_cons, 2)) ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <?php
            $total_kg = $rst[tot_kg_tornillo1] + $rst[tot_kg_tornillo2] + $rst[tot_kg_tornillo3];
            ?>
            <td class="font">Total 100%: <?php echo str_replace(',', '', number_format($rst[ord_mftotal], 2)) ?>%</td> 
            <td align="right">Total: <?php echo str_replace(',', '', number_format($total_kg, 2)) ?> KG</td> 
        </tr>
        <tr>
            <td class="font">Merma:<?php echo str_replace(',', '', number_format($rst[ord_merma], 2)) ?>%</td>  
            <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_merma_peso], 2)) ?> Kg </td>

        </tr>
        <tr>
            <td class="font">Gran Total:<?php echo str_replace(',', '', number_format($rst[ord_tot_fin], 2)) ?>% 
            <td align="right"><?php echo str_replace(',', '', number_format($rst[ord_tot_fin_peso], 2)) ?> Kg</td>
        </tr>
        <tr>
            <td class="font" colspan="8">Observaciones:<?php echo $rst[ord_observaciones] ?></td>
        </tr>
        <tr>
            <td colspan="20"class="sbtitle" >DETALLE ROLLOS</td>
        </tr>
        <tr>
            <td colspan="20">
                <table>
                    <tr>
                        <td class="font">No</td>
                        <td class="font" style="width: 100px">Producto</td>
                        <td class="font" style="width: 100px">Num.rollo</td>
                        <td class="font" style="width: 100px">Fecha</td>
                        <td class="font" style="width: 100px">Maquina</td>
                        <td class="font" style="width: 100px">Peso Conforme(kg)</td>
                        <td class="font" style="width: 100px">Peso Inconforme(kg)</td>
                    </tr>
                    <?php
                    $cns_reg = $Set->registros_productos($rst[ord_id], $rst[pro_id], $rst[ord_pro_secundario], $rst[ord_pro3], $rst[ord_pro4]);
                    $prod1 = array();
                    while ($rst_reg = pg_fetch_array($cns_reg)) {
                        $peso_conf = '';
                        $peso_inconf = '';
                        if ($rst_reg[rec_estado] == 0) {
                            if (!empty($rst_reg[rec_peso_primario])) {
                                $peso_conf = str_replace(',', '', number_format($rst_reg[rec_peso_primario], 2));
                            } else {
                                $peso_conf = '';
                            }
                        } else {
                            if (!empty($rst_reg[rec_peso_primario])) {
                                $peso_inconf = str_replace(',', '', number_format($rst_reg[rec_peso_primario], 2));
                            } else {
                                $peso_inconf = '';
                            }
                        }
                        array_push($prod1, $rst_reg[rec_lote] . '&&' . $rst_reg[rec_fecha] . '&&' . $peso_conf . '&&' . $peso_inconf . '&&' . $rst_reg[pro_id] . '&&' . $rst_reg[rec_estado] . '&&' . $rst_reg[maq_id]);
                    }
                    $j = 0;
                    $conf1 = 0;
                    $inconf1 = 0;
                    while ($j < count($prod1)) {
                        $p1 = explode('&&', $prod1[$j]);
                        $j++;
                        $rst_r1 = pg_fetch_array($Set->lista_un_producto($p1[4]));
                        $rst_m = pg_fetch_array($Set->lista_una_maquina($p1[6]));
                        ?>
                        <tr>
                            <td><?php echo $j ?></td>
                            <td><?php echo $rst_r1[pro_descripcion] ?></td>
                            <td><?php echo $p1[0] ?></td>
                            <td><?php echo $p1[1] ?></td>
                            <td><?php echo $rst_m[maq_a] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($p1[2], 2)) ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($p1[3], 2)) ?></td>
                        </tr>
                        <?php
                        $conf1+=$p1[2];
                        $inconf1+=$p1[3];
                    }
                    ?>
                    <tr>
                        <td class="font">Total</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="font" align="right"><?php echo str_replace(',', '', number_format($conf1, 2)) ?></td>
                        <td class="font" align="right"><?php echo str_replace(',', '', number_format($inconf1, 2)) ?></td>
                    </tr>

                </table>
            </td>
        </tr>
        <tr>
            <td>
                <button onClick="imprimir()" id="botonimprimir"><img src="../img/print_iconop.png" width="18px"></img>Imprimir</button><br/>
            </td>
        </tr>
    </table>
</html>
