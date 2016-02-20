<?php

include_once 'Conn.php';

class Clase_reg_nota_credito {

    var $con;

    function Clase_reg_nota_credito() {
        $this->con = new Conn();
    }

    function lista_buscador_nota_credito($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_registro_nota_credito $txt ORDER BY rnc_num_registro");
        }
    }

    function lista_secuencial_nota_credito() {
        if ($this->con->Conectar() == true) {
            if ($this->con->Conectar() == true) {
                return pg_query("SELECT rnc_num_registro as sec FROM  erp_registro_nota_credito order by rnc_num_registro desc limit 1");
            }
        }
    }

    function lista_una_factura_nfact($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_reg_documentos d, erp_i_cliente c where c.cli_ced_ruc=d.reg_ruc_cliente and d.reg_num_documento='$id' and (reg_estado='1' or reg_estado='4')");
        }
    }

    function lista_un_regfactura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_reg_documentos d, erp_i_cliente c where c.cli_ced_ruc=d.reg_ruc_cliente and d.reg_id='$id'");
        }
    }

    function lista_det_factura($id) { // nueva 
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_reg_det_documentos where reg_id='$id'");
        }
    }

//    function lista_det_factura($id) { antigua
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_reg_det_documentos d, erp_mp p where d.det_codigo_empresa=p.mp_c and d.reg_id='$id'");
//        }
//    }

    function suma_prod_nota_credito($id, $pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(dc.drc_cantidad) FROM  erp_reg_det_nota_credito dc, erp_registro_nota_credito n where dc.rnc_id=n.rnc_id and n.reg_id='$id' and dc.drc_codigo='$pro'");
        }
    }

    function lista_tipos($id) { // ya no se va a utilizar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_tipos where id=$id");
        }
    }

    function lista_productos($id) { // ya no se va a utilizar
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(mp_tipo,'&',10) as producto from erp_mp_set where ids=$id");
        }
    }

    function total_ingreso_egreso_fact($id) { // ya no se va a utilizar
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1) as egreso");
        }
    }

    function lista_un_notac_num($fac) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_registro_nota_credito where rnc_num_registro='$fac'");
        }
    }

    function insert_nota_credito($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_registro_nota_credito(
                            cli_id, 
                            rnc_numero, 
                            rnc_motivo, 
                            rnc_fecha_emision, 
                            rnc_nombre, 
                            rnc_identificacion, 
                            rnc_denominacion_comprobante, 
                            rnc_num_comp_modifica, 
                            rnc_fecha_emi_comp, 
                            rnc_subtotal12, 
                            rnc_subtotal0, 
                            rnc_subtotal_ex_iva, 
                            rnc_subtotal_no_iva, 
                            rnc_total_descuento, 
                            rnc_total_ice, 
                            rnc_total_iva, 
                            rnc_irbpnr, 
                            rnc_total_propina, 
                            rnc_autorizacion, 
                            rnc_total_valor, 
                            trs_id, 
                            rnc_subtotal, 
                            rnc_num_registro, 
                            rnc_fec_registro, 
                            rnc_fec_autorizacion, 
                            rnc_fec_caducidad, 
                            reg_id,
                            reg_codigo_cta,
                            pln_id
                            )VALUES(
                            '$data[0]',
                            '$data[1]',
                            '" . strtoupper($data[2]) . "',
                            '$data[3]',
                            '" . strtoupper($data[4]) . "',
                            '" . strtoupper($data[5]) . "',
                            '$data[6]',   
                            '$data[7]',
                            '$data[8]',
                            '$data[9]',
                            '$data[10]',
                            '$data[11]',
                            '$data[12]',
                            '$data[13]',
                            '$data[14]',
                            '$data[15]',
                            '$data[16]',
                            '$data[17]',
                            '$data[18]',
                            '$data[19]',
                            '$data[20]',
                            '$data[21]',
                            '$data[22]',
                            '$data[23]',
                            '$data[24]',
                            '$data[25]',
                            '$data[26]',
                            '$data[27]',
                            '$data[28]')");
        }
    }

    function insert_det_nota_credito($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_reg_det_nota_credito(
                                   pro_id, 
                                   rnc_id, 
                                   drc_codigo, 
                                   drc_cod_aux, 
                                   drc_cantidad, 
                                   drc_descripcion, 
                                   drc_precio_unit, 
                                   drc_porcentaje_descuento, 
                                   drc_val_descuento, 
                                   drc_precio_total, 
                                   drc_iva                                   
                                    )VALUES(
                                   '$data[0]',
                                   '$id',
                                   '" . strtoupper($data[1]) . "',
                                   '" . strtoupper($data[2]) . "',
                                   '$data[3]',
                                   '" . strtoupper($data[4]) . "',
                                   '$data[5]',
                                   '$data[6]',
                                   '$data[7]',
                                   '$data[8]',
                                   '$data[9]'
                                     )");
        }
    }

    function lista_una_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_registro_nota_credito where rnc_id='$id'");
        }
    }

    function lista_detalle_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_det_nota_credito WHERE rnc_id='$id'");
        }
    }

    function update_nota_credito($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_registro_nota_credito SET
                            cli_id='$data[0]', 
                            rnc_numero='$data[1]', 
                            rnc_motivo='" . strtoupper($data[2]) . "', 
                            rnc_fecha_emision='$data[3]', 
                            rnc_nombre='" . strtoupper($data[4]) . "', 
                            rnc_identificacion='" . strtoupper($data[5]) . "',
                            rnc_denominacion_comprobante='$data[6]', 
                            rnc_num_comp_modifica='$data[7]', 
                            rnc_fecha_emi_comp='$data[8]', 
                            rnc_subtotal12='$data[9]', 
                            rnc_subtotal0='$data[10]', 
                            rnc_subtotal_ex_iva='$data[11]', 
                            rnc_subtotal_no_iva='$data[12]', 
                            rnc_total_descuento='$data[13]', 
                            rnc_total_ice='$data[14]', 
                            rnc_total_iva='$data[15]',
                            rnc_irbpnr='$data[16]', 
                            rnc_total_propina='$data[17]', 
                            rnc_autorizacion='$data[18]', 
                            rnc_total_valor='$data[19]', 
                            trs_id='$data[20]', 
                            rnc_subtotal='$data[21]', 
                            rnc_num_registro='$data[22]', 
                            rnc_fec_registro='$data[23]', 
                            rnc_fec_autorizacion='$data[24]', 
                            rnc_fec_caducidad='$data[25]', 
                            reg_id='$data[26]',
                            reg_codigo_cta='$data[27]',
                            pln_id='$data[28]'
                    WHERE rnc_id=$id            
                        ");
        }
    }

    function delete_det_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_reg_det_nota_credito WHERE rnc_id='$id'");
        }
    }

    function delete_movimiento($num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_i_mov_inv_pt WHERE mov_documento='$num_secuencial' and (trs_id=6 or trs_id=7)");
        }
    }

    function lista_un_producto($id) { // ya no se esta utilizando 
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where id=$id");
        }
    }

    function delete_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_registro_nota_credito WHERE rnc_id='$id'");
        }
    }

    function delete_asientos($id, $doc, $con) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_asientos_contables where doc_id='$id' and con_documento='$doc' and con_concepto='$con'");
        }
    }

    function buscar_un_pago_doc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_documentos p WHERE p.reg_id='$id' and not exists(SELECT * FROM erp_ctasxpagar c where c.pag_id=p.pag_id) order by p.pag_id");
        }
    }

    function buscar_un_pago_doc1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_documentos p WHERE p.reg_id='$id' order by p.pag_id desc");
        }
    }

    function insert_ctasxpagar($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_ctasxpagar(
                                                        reg_id, 
                                                        ctp_fecha, 
                                                        ctp_monto, 
                                                        ctp_forma_pago, 
                                                        ctp_banco,
                                                        pln_id,
                                                        ctp_fecha_pago,
                                                        pag_id,
                                                        num_documento,
                                                        ctp_concepto,
                                                        asiento,
                                                        chq_id,
                                                        doc_id)
                                                VALUES (
                                                        '$data[0]',
                                                        '$data[1]',
                                                        '$data[2]',
                                                        '$data[3]',
                                                        '$data[4]',
                                                        '$data[5]',
                                                        '$data[6]',
                                                        '$data[7]',
                                                        '$data[8]',
                                                        '$data[9]',
                                                        '$data[10]',
                                                        '$data[11]',
                                                        '$data[12]'
                                                        )");
        }
    }

    function ultimo_asiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    function siguiente_asiento() {
        if ($this->con->Conectar() == true) {
            $rst = pg_fetch_array($this->ultimo_asiento());
            if (!empty($rst)) {
                $sec = (substr($rst[con_asiento], -10) + 1);
                $n_sec = 'AS' . substr($rst[con_asiento], 2, (10 - strlen($sec))) . $sec;
            } else {
                $n_sec = 'AS0000000001';
            }
            return $n_sec;
        }
    }

    function insert_asientos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into erp_asientos_contables(
                                                                con_asiento, 
                                                                con_concepto, 
                                                                con_documento, 
                                                                con_fecha_emision,
                                                                con_concepto_debe, 
                                                                con_concepto_haber, 
                                                                con_valor_debe,
                                                                con_valor_haber,
                                                                con_estado,
                                                                doc_id)
                                                                VALUES ('$data[0]',
                                                                        '$data[1]',
                                                                        '$data[2]',
                                                                        '$data[3]',
                                                                        '$data[4]',
                                                                        '$data[5]',
                                                                        '$data[6]',
                                                                        '$data[7]',
                                                                        '$data[8]',
                                                                        '$data[9]'
                                                                        )");
        }
    }

    function delete_ctasxpagar($doc, $con) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_ctasxpagar where doc_id='$doc' and ctp_forma_pago='$con'");
        }
    }

    function ultimo_costo($id) { // ya no se va a utilizar 
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT mov_val_unit as cost FROM  erp_i_mov_inv_pt where pro_id='$id' order by mov_id desc limit 1");
        }
    }

    function lista_una_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura f, erp_vendedor v where f.vnd_id=v.vnd_id and f.fac_id='$id'");
        }
    }

    function lista_prod_det_factura($id, $pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_factura where fac_id='$id' and pro_id=$pro");
        }
    }

    function lista_costos_mov($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_mov_inv_pt where pro_id=$id order by mov_id desc limit 1");
        }
    }

    function lista_producto_total() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp p where p.mp_a='0'");
        }
    }

    function lista_un_producto_mp($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where id=$id and mp_a='0' and mp_i='0'");
        }
    }

    function lista_vendedor($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM  erp_vendedor where vnd_nombre='$txt'");
        }
    }

    function insert_movimiento($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_fecha_trans,
                mov_fecha_registro,
                mov_hora_registro,
                mov_cantidad,
                mov_tabla,
                mov_val_unit,
                mov_val_tot
                            )
    VALUES (
                    $data[0],
                    $data[1],
                    $data[2],
                    $data[3],
                    '$data[4]',
                    '$data[5]',
                    '" . date('Y-m-d') . "',
                    '" . date("H:i:s") . "',
                    '$data[6]',
                    '$data[7]',
                    '$data[8]',
                    '$data[9]')");
        }
    }

    function insert_cliente($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cliente 
(
  cli_apellidos,
  cli_raz_social,
  cli_fecha,
  cli_estado,
  cli_tipo,
  cli_categoria,
  cli_ced_ruc,
  cli_calle_prin,
  cli_codigo,
  cli_telefono,
  cli_email
) values ('$data[0]',
    '$data[0]',
'" . date('Y-m-d') . "',
    0,
    0,
    1,
'$data[1]',
'$data[2]',    
'$data[3]',
'$data[4]',
'$data[5]')");
        }
    }

    function lista_un_cliente_cedula($cod) {// sirve para cuando selecciono un registro para modificar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente WHERE cli_ced_ruc='$cod'");
        }
    }

    function upd_email_cliente($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cliente SET
            cli_calle_prin='$data[0]',
            cli_email='$data[1]',
            cli_telefono='$data[2]',
            cli_canton='$data[3]',
            cli_pais='$data[4]',
            cli_parroquia='$data[5]',
                cli_calle_sec='',
                cli_numeracion=''
            WHERE cli_ced_ruc='$id'");
        }
    }

    function lista_secuencial_cliente($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where substr(cli_codigo,1,2)='$tp' order by cli_codigo desc limit 1");
        }
    }

    function lista_un_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_ced_ruc='$id'");
        }
    }

    function lista_configuraciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_configuraciones where con_id='5'");
        }
    }

    function lista_configuraciones_dec() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_configuraciones where con_id='2'");
        }
    }

    function lista_producto_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$id'");
        }
    }

    function lista_i_producto_cod($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where mp_c='$cod'");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_id='$id'");
        }
    }

    function lista_comprobante($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=1 and cod_punto_emision=$emi ORDER BY num_secuencial desc");
        }
    }

    function lista_buscador_facturas($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM comprobantes $txt ORDER BY num_secuencial desc");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////          
    function lista_un_comprobante($id, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where num_secuencial='$id' and tipo_comprobante=1 and cod_punto_emision=$emi");
        }
    }

    function lista_un_notac_factura($fac) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=4 and num_factura_modifica='$fac' ");
        }
    }

    function lista_detalle_factura($num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM detalle_fact_notdeb_notcre WHERE num_camprobante='$num_secuencial' and tipo_comprobante='1'");
        }
    }

    function lista_inventario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT  * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and c.cli_id=m.cli_id and p.pro_codigo='$id' ");
        }
    }

    function lista_motivo($num_secuencial, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and mov_documento='$num_secuencial' and bod_id=$emi");
        }
    }

    function lista_una_factura_numdoc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where num_documento='$id' and tipo_comprobante=1");
        }
    }

    function lista_clientes_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where 
                cli_codigo like '%$txt%' 
                    or cli_ced_ruc like '%$txt%'  
                        or cli_nombres like '%$txt%' 
                            or cli_apellidos like '%$txt%' 
                                or cli_raz_social like '%$txt%' 
                            
                            Order by cli_raz_social");
        }
    }

    function lista_clientes_codigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_ced_ruc='$id' ");
        }
    }

    function lista_nota_cred_duplicada($id, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_registro_nota_credito where rnc_numero='$id' and rnc_identificacion='$ruc' and rnc_estado=1");
        }
    }

    function update_estado_reg_nc($id, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_registro_nota_credito set rnc_estado='$std' WHERE rnc_id=$id");
        }
    }

    function update_estado_det_nc($id, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_det_nota_credito set det_estado='$std' WHERE rnc_id=$id");
        }
    }

    function lista_todo_registro($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_nota_credito r,erp_reg_det_nota_credito d where r.rnc_id=d.rnc_id and r.rnc_id=$id ");
        }
    }

    function insert_asiento_anulacionmp($data) {
        if ($this->con->Conectar() == true) {
            if ($data[3] == '') {
                $sec = $this->siguiente_asiento();
            } else {
                $sec = $as;
            }

            if (round($data[9], 2) != 0) {
                $desc = "('$sec', 'ANULACION DE DEVOLUCION DE COMPRA', '$data[1]', '$data[2]', '', '$data[15]', '0', '$data[9]','$data[17]','0'),";
            }
            if (round($data[4], 2) != 0) {
                $iva = "('$sec','ANULACION DE DEVOLUCION DE COMPRA','$data[1]','$data[2]','$data[11]','','$data[4]','0','$data[17]','0'),";
            }
            if (round($data[6], 2) != 0) {
                $ice = "('$sec','ANULACION DE DEVOLUCION DE COMPRA','$data[1]','$data[2]','$data[13]','','$data[6]','0','$data[17]','0'),";
            }
            if (round($data[7], 2) != 0) {
                $ibr = "('$sec','ANULACION DE DEVOLUCION DE COMPRA','$data[1]','$data[2]','$data[14]','','$data[7]','0','$data[17]','0'),";
            }
            if (round($data[8], 2) != 0) {
                $prop = "('$sec','ANULACION DE DEVOLUCION DE COMPRA','$data[1]','$data[2]','$data[16]','','$data[8]','0','$data[17]','0'),";
            }
            $val = $data[0] + $data[9];
            return pg_query("INSERT INTO erp_asientos_contables(
            con_asiento,
            con_concepto,
            con_documento,
            con_fecha_emision, 
            con_concepto_debe, 
            con_concepto_haber,
            con_valor_debe, 
            con_valor_haber,
            doc_id,
            con_estado)
    VALUES ('$sec','ANULACION DE DEVOLUCION DE COMPRA','$data[1]','$data[2]','$data[10]','','$val','0','$data[17]','0'),
            $desc
            $iva
            $ice
            $ibr
            $prop
           ('$sec','ANULACION DE DEVOLUCION DE COMPRA','$data[1]','$data[2]', '', '$data[12]', '0','$data[5]','$data[17]','0')");
        }
    }

    function update_ctasxpagar($id, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_ctasxpagar set ctp_estado='$std' WHERE doc_id=$id and ctp_forma_pago='NOTA DE CREDITO'");
        }
    }

    function lista_plan_cuentas() {
        if (
                $this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas ORDER BY pln_codigo");
        }
    }

    function lista_plan_cuentas_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_id = $id");
        }
    }

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id = c.pln_id and a.cas_id = '$id' and c.pln_estado=0");
        }
    }

    function lista_detall_factura($id, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_reg_det_documentos where reg_id='$id' and det_codigo_empresa='$cod'");
        }
    }

    function lista_secuencial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_nota_credito where rnc_num_registro='$id'");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
