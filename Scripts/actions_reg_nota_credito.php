<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_reg_nota_credito.php';
$Reg_nota_credito = new Clase_reg_nota_credito();
$Adt = new Auditoria();
$act = $_REQUEST[act]; //Accion
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$id = $_REQUEST[id];
$tbl = $_REQUEST[tbl]; //tbl
$s = $_REQUEST[s]; //tbl
$x = $_REQUEST[x];
$c = $_REQUEST[c];
$ctr_inv = $_REQUEST[ctr_inv];
$fields = $_REQUEST[fields];
$emi = $_REQUEST[emi];
switch ($act) {
    case 0:
        $sms = 0;
        $aud = 0;
        if (empty($id)) {
            $rst_sec = pg_fetch_array($Reg_nota_credito->lista_secuencial($data[22]));
            if (!empty($rst_sec)) {
                $sms = 3;
            } else {
                $ctsxpag = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('282'));
                $iva = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('283'));
                $ice = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('284'));
                $des = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('285'));
                $irb = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('286'));
                $pro = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('287'));
                $ctpag = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('288'));
                $pvee = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('289'));

                if ($ctsxpag[pln_id] == '' || $iva[pln_id] == '' || $ice[pln_id] == '' || $des[pln_id] == '' || $irb[pln_id] == '' || $pro[pln_id] == '' || $ctpag[pln_id] == '' || $pvee[pln_id] == '') {
                    $sms = 1;
                } else {

                    if ($Reg_nota_credito->insert_nota_credito($data) == TRUE) {
                        $nc = pg_fetch_array($Reg_nota_credito->lista_un_notac_num($data[22]));
                        $nrc_id = $nc[rnc_id];
                        $n = 0;
                        while ($n < count($data1)) {
                            $dt = explode('&', $data1[$n]);
                            if ($Reg_nota_credito->insert_det_nota_credito($dt, $nrc_id) == FALSE) {
                                $sms = 'Insert_det' . pg_last_error();
                                $aud = 1;
                            }
                            $n++;
                        }
                    } else {
                        $sms = 'Insert' . pg_last_error();
                        $accion = 'Insertar';
                        $aud = 1;
                    }

                    if ($aud == 0) {
                        $rp = pg_fetch_array($Reg_nota_credito->buscar_un_pago_doc($nc[reg_id]));
                        if (empty($rp)) {
                            $rp2 = pg_fetch_array($Reg_nota_credito->buscar_un_pago_doc1($nc[reg_id]));
                            $pag_id = $rp2[pag_id];
                        } else {
                            $pag_id = $rp[pag_id];
                        }
                        $banco = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('288'));
                        $prov = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('289'));
                        $cta = array(
                            $nc[reg_id], //com_id
                            $data[23], //cta_fec
                            $data[19], //cta_monto
                            'NOTA DE CREDITO', //forma de pago
                            $banco[pln_codigo], //cta_banco
                            $prov[pln_id], /// pln_id
                            $data[23], //fec_pag
                            $pag_id, //pag_id
                            '0', //num_doc
                            'PAGO FACTURACION', //cta_concepto
                            '2', //asiento
                            '0', //chq_id
                            $nrc_id //doc_id
                        );
                        if ($Reg_nota_credito->insert_ctasxpagar($cta) == false) {
                            $sms = 'ctasxpagar ' . pg_last_error();
                        } else {
                            $asi = $Reg_nota_credito->siguiente_asiento();
                            $asiento = array(
                                $asi,
                                'CUENTAS X PAGAR',
                                $nc[rnc_numero], //doc
                                $data[23], //fec
                                $banco[pln_codigo], //con_debe
                                $prov[pln_codigo], //con_haber
                                $data[19], //val_debe
                                $data[19], // val_haber
                                '0', //estado
                                $nrc_id//doc_id
                            );
                            if ($Reg_nota_credito->insert_asientos($asiento) == false) {
                                $sms = 'asientos ' . pg_last_error();
                            }
                        }
                    }


                    if ($aud == 0) {
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = 'REG. NOTA DE CREDITO';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[22]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            }
        } else {
            $ctsxpag = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('282'));
            $iva = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('283'));
            $ice = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('284'));
            $des = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('285'));
            $irb = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('286'));
            $pro = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('287'));
            $ctpag = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('288'));
            $pvee = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('289'));

            if ($ctsxpag[pln_id] == '' || $iva[pln_id] == '' || $ice[pln_id] == '' || $des[pln_id] == '' || $irb[pln_id] == '' || $pro[pln_id] == '' || $ctpag[pln_id] == '' || $pvee[pln_id] == '') {
                $sms = 1;
            } else {

                $nrc_id = $id;

                if ($Reg_nota_credito->update_nota_credito($data, $id) == FALSE) {
                    $sms = pg_last_error() . 'updnota_credito';
                    $aud = 1;
                } else {
//                   

                    if ($Reg_nota_credito->delete_det_nota_credito($id) == FALSE) {
                        $sms = pg_last_error() . 'delet_detalle';
                        $aud = 1;
                    } else {
                        $n = 0;
                        while ($n < count($data1)) {
                            $dt = explode('&', $data1[$n]);
                            if ($Reg_nota_credito->insert_det_nota_credito($dt, $id) == TRUE) {
                                $dat2 = Array(
                                    $dt[0],
                                    $data[20],
                                    $data[0],
                                    '1',
                                    $data[22],
                                    $data[23],
                                    $dt[3],
                                    '0',
                                    $dt[10],
                                    $dt[11]
                                );
                                if ($Reg_nota_credito->delete_movimiento($data[22]) == FALSE) {
                                    $sms = pg_last_error() . 'del_mov';
                                    $aud = 1;
                                } else {
                                    if ($data[20] != '1') {
                                        if ($Reg_nota_credito->insert_movimiento($dat2) == FALSE) {
                                            $sms = pg_last_error() . 'insert_mov,' . $pro;
                                            $aud = 1;
                                        }
                                    }
                                }
                                $n++;
                            }
                        }
                    }
                }

                if ($aud == 0) {
                    if ($Reg_nota_credito->delete_asientos($id, $data[1], 'CUENTAS X PAGAR') == false) {
                        $sms = 'ctasxpagar ' . pg_last_error();
                    } else {
                        if ($Reg_nota_credito->delete_ctasxpagar($id, 'NOTA DE CREDITO') == false) {
                            $sms = 'ctasxpagar ' . pg_last_error();
                        } else {
                            $rp = pg_fetch_array($Reg_nota_credito->buscar_un_pago_doc($data[26]));
                            if (empty($rp)) {
                                $rp2 = pg_fetch_array($Reg_nota_credito->buscar_un_pago_doc1($data[26]));
                                $pag_id = $rp2[pag_id];
                            } else {
                                $pag_id = $rp[pag_id];
                            }
                            $banco = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('288'));
                            $prov = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('289'));
                            $cta = array(
                                $data[26], //com_id
                                $data[23], //cta_fec
                                $data[19], //cta_monto
                                'NOTA DE CREDITO', //forma de pago
                                $banco[pln_codigo], //cta_banco
                                $prov[pln_id], /// pln_id
                                $data[23], //fec_pag
                                $pag_id, //pag_id
                                '0', //num_doc
                                'PAGO FACTURACION', //cta_concepto
                                '2', //asiento
                                '0', //chq_id
                                $nrc_id //doc_id
                            );
                            if ($Reg_nota_credito->insert_ctasxpagar($cta) == false) {
                                $sms = 'ctasxpagar ' . pg_last_error();
                            } else {
                                $asi = $Reg_nota_credito->siguiente_asiento();
                                $asiento = array(
                                    $asi,
                                    'CUENTAS X PAGAR',
                                    $data[1], //doc
                                    $data[23], //fec
                                    $banco[pln_codigo], //con_debe
                                    $prov[pln_codigo], //con_haber
                                    $data[19], //val_debe
                                    $data[19], // val_haber
                                    '0', //estado
                                    $nrc_id//doc_id
                                );
                                if ($Reg_nota_credito->insert_asientos($asiento) == false) {
                                    $sms = 'asientos ' . pg_last_error();
                                }
                            }
                        }
                    }
                }



                if ($aud == 0) {
                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'REG. NOTA DE CREDITO';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $data[22]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }
        }
        echo $sms . '&' . $nrc_id;
        break;
    case 1:
        if ($s == 0) {
            $cns = $Reg_nota_credito->lista_clientes_search(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[cli_raz_social];
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
            echo $sms = 1;
        } else {
            $sms;
            $rst = pg_fetch_array($Reg_nota_credito->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . trim($rst[cli_id]);
            }
            echo $sms;
        }
        break;

    case 2:
        $rst = pg_fetch_array($Reg_nota_credito->lista_nota_cred_duplicada($id, $data));
        echo $rst[rnc_identificacion] . '&' . $rst[rnc_numero];
        break;

    case 4:
        if ($x == 0) {
            $cns = $Reg_nota_credito->lista_una_factura_nfact($id);
            $fac = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[reg_id];
                $fac .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_factura2('$rst[reg_id]')" . " /></td><td>$n</td><td>$rst[reg_num_documento]</td><td>$rst[cli_raz_social]</td></tr>";
                $sms = 1;
            }
            echo $sms . '&&' . $fac;
        } else {
            $rst = pg_fetch_array($Reg_nota_credito->lista_un_regfactura_id($id));
            if ($rst[reg_id] != '') {
                $cns = $Reg_nota_credito->lista_det_factura($rst[reg_id]);
                while ($rst2 = pg_fetch_array($cns)) {
                    $n++;
                    $rst_s = pg_fetch_array($Reg_nota_credito->suma_prod_nota_credito($rst2[reg_id], $rst2[det_codigo_empresa]));
                    if (empty($rst_s)) {
                        $entr = $rst2[det_cantidad];
                    } else {
                        $entr = $rst2[det_cantidad] - $rst_s[sum];
                    }
                    switch ($rst2[det_tipo]) {
                        case 0:
                            $nom_tipo = 'Insumos-Otros';
                            break;
                        case 1:
                            $nom_tipo = 'Producto';
                            break;
                        case 2:
                            $nom_tipo = 'Materia Prima';
                            break;
                    }
                    $a = '"';
                    $lista.="<tr>
                                        <td><input type='text' size='5'  id='item$n' class='itm'  lang='$n' value='$n' readonly  style='text-align:right' /></td>   
                                        <td>
                                            <input type='text' size='12' id='nom_tipo$n'  readonly value='$nom_tipo' lang='$n'/>
                                            <input type='hidden' size='10' id='tipo$n'  readonly value='$rst2[det_tipo]' lang='$n'/>
                                        </td>  
                                        <td class='td1'><input type='text' size='12' id='cod_producto$n' readonly value='$rst2[det_codigo_empresa]' lang='$n' list='productos' onblur='this.style.width = '100px', load_producto(this)' onfocus='this.style.width = '500px''/>
                                            <input hidden type='text' size='10' id='pro_id$n' lang='$n' value='$rst2[pro_id]'/>
                                                </td>
                                        <td><input type='text' size='12' id='cod_externo$n'  readonly value='$rst2[det_codigo_externo]' lang='$n'/></td> 
                                        <td><input type='text' size='15' id='descripcion$n'  readonly value='$rst2[det_descripcion]' lang='$n'/></td>  
                                        <td class='td1'><input id='cantidad$n' type='text' lang='$n' readonly value='" . str_replace(',', '', number_format($entr, $s)) . "' size='8'/></td>
                                        <td><input type='text' size='7'  id='cantidadf$n' onchange='calculo(), comparar(this)'  value='" . str_replace(',', '', number_format($entr, $s)) . "' onkeyup='this.value = this.value.replace(/[^0-9.]/, $a$a)' style='text-align:right' lang='$n' /></td>
                                        <td><input type='text' size='7' readonly id='precio_unitario$n'  value='" . str_replace(',', '', number_format($rst2[det_vunit], $s)) . "' style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/, $a$a)' lang='$n' onchange='calculo()'/></td>                  
                                        <td><input type='text' size='7'  readonly id='descuento$n'  value='" . str_replace(',', '', number_format($rst2[det_descuento_porcentaje], $s)) . "'  style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/, $a$a)' lang='$n' onchange='calculo()'/></td>                  
                                        <td>
                                            <input type='text' size='7'  id='descuent$n'  value='" . str_replace(',', '', number_format($rst2[det_descuento_moneda], $s)) . "' lang='$n' readonly  />
                                            <label hidden id='lbldescuent$n' lang='$n'></label>
                                        </td>
                                        <td><input type='text' size='7'  readonly id='iva$n'  value='$rst2[det_impuesto]' style='text-align:right' lang='$n' onblur='calculo(), this.value = this.value.toUpperCase()' /></td>                  
                                        <td><input type='text' size='10'  id='precio_total$n'  value='" . str_replace(',', '', number_format($rst2[det_total], $s)) . "' style='text-align:right' lang='$n' readonly />                  
                                            <label  hidden id='lblprecio_total$n' lang='$n'></label></td>               
                                        <td onclick = 'elimina_fila(this)' ><img class = 'auxBtn' width='12px' src = '../img/del_reg.png'/></td>
                                  </tr>";
                }
            }
            echo $rst[reg_id] . '&' .
            $rst[reg_femision] . '&' .
            str_replace('&','',$rst[reg_ruc_cliente]) . '&' .
            str_replace('&','',$rst[cli_raz_social]) . '&' .
            $lista . '&' .
            $rst[cli_id] . '&' .
            str_replace(',', '', number_format($rst[reg_propina], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_sbt12], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_subtotal0], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_sbt_noiva], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_sbt_excento], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_tdescuento], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_ice], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_iva12], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_irbpnr], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_total], $s));
        }
        break;

    case 5:
        $sms = 0;
        if ($Reg_nota_credito->delete_asientos($id, $data1, 'DEVOLUCION COMPRA') == false) {
            $sms = pg_last_error();
        } else {
            if ($Reg_nota_credito->delete_asientos($id, $data1, 'CUENTAS X PAGAR') == false) {
                $sms = 'ctasxpagar ' . pg_last_error();
            } else {
                if ($Reg_nota_credito->delete_ctasxpagar($id, 'NOTA DE CREDITO') == false) {
                    $sms = 'ctasxpagar ' . pg_last_error();
                } else {
                    if ($Reg_nota_credito->delete_movimiento($data) == false) {

                        $sms = pg_last_error();
                    } else {
                        if ($Reg_nota_credito->delete_det_nota_credito($id) == false) {
                            $sms = pg_last_error();
                        } else {
                            if ($Reg_nota_credito->delete_nota_credito($id) == false) {
                                $sms = pg_last_error();
                            } else {
                                $modulo = 'REG. NOTA DE CREDITO';
                                $accion = 'ELIMINAR';
                                if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                                    $sms = "Auditoria" . pg_last_error();
                                }
                            }
                        }
                    }
                }
            }
        }

        echo $sms;
        break;

    case 6:
        $cdxp = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('290'));
        $iva = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('291'));
        $ice = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('292'));
        $des = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('293'));
        $irbp = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('294'));
        $pro = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('295'));
        $cxp = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('296'));
        $prv = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('297'));
        if ($cdxp[pln_id] == '' || $iva[pln_id] == '' || $ice[pln_id] == '' || $des[pln_id] == '' || $irbp[pln_id] == '' || $pro[pln_id] == '' || $cxp[pln_id] == '' || $prv[pln_id] == '') {
            $sms = 1;
        } else {
            $sms = 0;
            if ($Reg_nota_credito->update_estado_reg_nc($_REQUEST[md_id], $_REQUEST[estado]) == true) {
                if ($Reg_nota_credito->update_estado_det_nc($_REQUEST[md_id], $_REQUEST[estado]) == false) {
                    $sms = 'Update_reg_det' . pg_last_error();
                } else {
                    $cns = $Reg_nota_credito->lista_todo_registro($_REQUEST[md_id]);
                    while ($rst_reg = pg_fetch_array($cns)) {
                        $sub = $rst_reg[rnc_subtotal];
                        $doc = $rst_reg[rnc_numero];
                        $fec = $rst_reg[rnc_fecha_emision];
                        $reg_id = $rst_reg[rnc_id];
                        $total = $rst_reg[rnc_total_valor];
                    }
                    $ctas = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('290'));
                    $iv = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('291'));
                    $ic = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('292'));
                    $desc = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('293'));
                    $irb = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('294'));
                    $prop = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('295'));
                    $rst_nc = pg_fetch_array($Reg_nota_credito->lista_todo_registro($_REQUEST[md_id]));
                    $dat_asi = array(
                        $rst_nc[rnc_subtotal],
                        $rst_nc[rnc_numero],
                        $rst_nc[rnc_fecha_emision],
                        '',
                        $rst_nc[rnc_total_iva],
                        $rst_nc[rnc_total_valor],
                        $rst_nc[rnc_total_ice],
                        $rst_nc[rnc_irbpnr],
                        $rst_nc[rnc_total_propina],
                        $rst_nc[rnc_total_descuento],
                        $rst_nc[reg_codigo_cta],
                        $iv[pln_codigo],
                        $ctas[pln_codigo],
                        $ic[pln_codigo],
                        $irb[pln_codigo],
                        $desc[pln_codigo],
                        $prop[pln_codigo],
                        $rst_nc[rnc_id]
                    );
                    if ($Reg_nota_credito->insert_asiento_anulacionmp($dat_asi) == false) {
                        $sms = 'Asiento ' . pg_last_error();
                    }


                    if ($Reg_nota_credito->update_ctasxpagar($_REQUEST[md_id], '1') == false) {
                        $sms = 'ctasxpagar ' . pg_last_error();
                    } else {
                        $prov = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('297'));
                        $banc = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('296'));
                        $asi = $Reg_nota_credito->siguiente_asiento();
                        $asiento = array(
                            $asi,
                            'ANULACION CUENTAS X PAGAR',
                            $doc, //doc
                            $fec, //fec
                            $banc[pln_codigo], //con_haber
                            $prov[pln_codigo], //con_debe
                            $total, // val_haber
                            $total, //val_debe
                            '0', //estado
                            $_REQUEST[md_id]//doc_id
                        );
                        if ($Reg_nota_credito->insert_asientos($asiento) == false) {
                            $sms = 'asientos ' . pg_last_error();
                        }
                    }
                }
            } else {
                $sms = 'Update_reg_encab' . pg_last_error();
            }
        }
        echo $sms;
        break;
    case 7:
        $cta = pg_fetch_array($Reg_nota_credito->lista_plan_cuentas_id($id));
        echo $cta[pln_id] . '&' . $cta[pln_codigo] . '&' . $cta[pln_descripcion];
        break;
}
?>



















