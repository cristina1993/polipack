<?php

include_once 'Conn.php';

class Clase_nota_Credito {

    var $con;

    function Clase_nota_Credito() {
        $this->con = new Conn();
    }

    function lista_un_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_ced_ruc='$id'");
        }
    }

    function lista_producto_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$id'");
        }
    }

    function lista_i_producto_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$id'");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where cod_punto_emision='$id'");
        }
    }

    

    function lista_comprobante($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=1 and cod_punto_emision=$emi ORDER BY num_secuencial desc");
        }
    }

    function lista_comprobante_fecha($desde, $hasta, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where fecha_emision >='$desde' and fecha_emision <='$hasta' and tipo_comprobante=4 and cod_punto_emision=$emi ORDER BY num_secuencial desc");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////          
    function lista_un_comprobante($id, $emi) {
//        print_r($id.'  '. $emi);
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where num_secuencial='$id' and tipo_comprobante=1 and cod_punto_emision=$emi");
        }
    }

    function lista_un_notac_factura($fac) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=4 and num_factura_modifica='$fac' ");
        }
    }

    function lista_inventario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT  * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and c.cli_id=m.cli_id and p.pro_codigo='$id' ");
        }
    }

    function lista_una_factura_numdoc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where num_documento='$id' and tipo_comprobante=1");
        }
    }

    function lista_nota_credito_completo() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=4 order by num_secuencial");
        }
    }

    function upd_notcre_clave_acceso($clave, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes 
                set clave_acceso='$clave'  where com_id=$id ");
        }
    }

    function upd_notcre_na($na, $fh, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_nota_credito 
                set ncr_estado_aut='RECIBIDA AUTORIZADO', ncr_fec_hora_aut='$fh' , ncr_autorizacion='$na'  where ncr_clave_acceso='$id' ");
        }
    }

    function lista_nota_numdoc($id, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=4 and num_documento='$id' and cod_punto_emision=$emi");
        }
    }

    function delete_comprobante_notacredito($num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM comprobantes WHERE num_documento='$num_secuencial' and tipo_comprobante=4");
        }
    }

    function delete_det_notacredito($num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  detalle_fact_notdeb_notcre WHERE num_camprobante='$num_secuencial' and tipo_comprobante=4");
        }
    }

    function delete_asientos_notacredito($num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables WHERE con_documento='$num_secuencial'");
        }
    }

    function delete_movimiento_notacredito($num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_i_mov_inv_pt WHERE mov_documento='$num_secuencial' and (trs_id=12 or trs_id=13)");
        }
    }

//    function lista_lote($num) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM comprobantes c, detalle_fact_notdeb_notcre d WHERE c.num_factura_modifica = d.num_camprobante and c.num_documento='$num'");
//        }
//    }
    function lista_lote($num, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM detalle_fact_notdeb_notcre WHERE num_camprobante='$num'   and cod_producto ='$cod'  ");
        }
    }

    function lista_un_producto_noperti_cod_lote($code, $lote) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code' and pro_ac='$lote'");
        }
    }

    function lista_un_producto_industrial_id($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$code");
        }
    }

    function lista_un_producto_industrial_cod($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$code'");
        }
    }

    function sum_total_nc($id, $cod, $tab) {
        if ($this->con->Conectar() == true) {
            $esp = str_replace('-', '', trim($id));
            return pg_query("select d.dnc_cantidad as can from erp_nota_credito c, erp_det_nota_credito d where c.ncr_id=d.ncr_id and c.fac_id='$id' and d.pro_id='$cod' and d.dnc_tab=$tab and c.fac_id<>0");
        }
    }

    function upd_estado_notcre($id, $est) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_nota_credito set ncr_estado_aut='ANULADO'where ncr_id='$id' ");
        }
    }

//// notas nuevas tablas 

    function lista_buscador_facturas($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura f, erp_i_cliente c, erp_vendedores v where f.cli_id=c.cli_id and f.vnd_id=v.vnd_id $txt ORDER BY f.fac_numero");
        }
    }

    function lista_buscador_notas_credito($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito n, erp_i_cliente c, erp_vendedores v where n.cli_id=c.cli_id and n.vnd_id=v.vnd_id $txt ORDER BY ncr_numero");
        }
    }

    function lista_una_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura f, erp_i_cliente c, erp_vendedores v where f.cli_id=c.cli_id and f.vnd_id=v.vnd_id and f.fac_id=$id");
        }
    }

    function lista_secuencial_nota_credito($bod) {
        if ($this->con->Conectar() == true) {
            if ($this->con->Conectar() == true) {
                return pg_query("SELECT * FROM  erp_nota_credito where emi_id='$bod' order by ncr_numero desc limit 1");
            }
        }
    }

    function lista_detalle_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_factura WHERE fac_id='$id'");
        }
    }

    function lista_una_nota_credito_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito n, erp_i_cliente c, erp_vendedores v where n.cli_id=c.cli_id and n.vnd_id=v.vnd_id and n.ncr_id=$id");
        }
    }

    function lista_detalle_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_nota_credito WHERE ncr_id=$id");
        }
    }

    function total_ingreso_egreso_fac($id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egreso");
        }
    }

    function lista_vendedor($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM  erp_vendedores where vnd_nombre='$txt'");
        }
    }

    function insert_nota_credito($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_nota_credito(
                            cli_id,
                            emi_id,
                            vnd_id,
                            ncr_numero,
                            ncr_motivo,
                            ncr_fecha_emision,
                            ncr_nombre,
                            ncr_identificacion,
                            ncr_email,
                            ncr_direccion ,
                            ncr_denominacion_comprobante,
                            ncr_num_comp_modifica ,
                            ncr_fecha_emi_comp,
                            ncr_subtotal12,
                            ncr_subtotal0,
                            ncr_subtotal_ex_iva ,
                            ncr_subtotal_no_iva ,
                            ncr_total_descuento ,
                            ncr_total_ice ,
                            ncr_total_iva ,
                            ncr_irbpnr,
                            nrc_telefono ,
                            nrc_total_valor,        
                            ncr_total_propina,        
                            fac_id,
                            trs_id,
                            ncr_subtotal
                            )VALUES(
                            $data[0],
                            $data[1],
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
                            '$data[26]')");
        }
    }

    function lista_un_notac_num($fac) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito where ncr_numero='$fac'");
        }
    }

    function insert_det_nota_credito($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_nota_credito(
                                   pro_id, 
                                   ncr_id, 
                                   dnc_codigo, 
                                   dnc_cod_aux, 
                                   dnc_cantidad, 
                                   dnc_descripcion, 
                                   dnc_precio_unit, 
                                   dnc_porcentaje_descuento, 
                                   dnc_val_descuento,
                                   dnc_precio_total, 
                                   dnc_iva, 
                                   dnc_ice,
                                   dnc_irbpnr,
                                   dnc_p_ice,
                                   dnc_cod_ice,
                                   dnc_p_irbpnr,
                                   dnc_lote,
                                   dnc_tab
                                    )VALUES(
                                   '$data[0]',
                                   '$id',
                                   '$data[1]',
                                   '$data[2]',
                                   '$data[3]',
                                   '" . strtoupper($data[4]) . "',
                                   '$data[5]',
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
                                   '$data[16]'
                                     )");
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
                mov_usuario
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
                    '$data[8]'
                    )");
        }
    }

    function upd_nota_credito($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_nota_credito SET
                            cli_id=$data[0],
                            emi_id=$data[1],
                            vnd_id=$data[2],
                            ncr_numero='$data[3]',
                            ncr_motivo='$data[4]',
                            ncr_fecha_emision='$data[5]',
                            ncr_nombre='$data[6]',
                            ncr_identificacion='$data[7]',
                            ncr_email='$data[8]',
                            ncr_direccion ='$data[9]',
                            ncr_denominacion_comprobante='$data[10]',
                            ncr_num_comp_modifica ='$data[11]',
                            ncr_fecha_emi_comp='$data[12]',
                            ncr_subtotal12='$data[13]',
                            ncr_subtotal0='$data[14]',
                            ncr_subtotal_ex_iva ='$data[15]',
                            ncr_subtotal_no_iva ='$data[16]',
                            ncr_total_descuento ='$data[17]',
                            ncr_total_ice ='$data[18]',
                            ncr_total_iva ='$data[19]',
                            ncr_irbpnr='$data[20]',
                            nrc_telefono ='$data[21]',
                            nrc_total_valor='$data[22]',        
                            ncr_total_propina='$data[23]',        
                            fac_id='$data[24]',
                            trs_id='$data[25]',
                            ncr_subtotal='$data[26]'	            	          	               
                            WHERE ncr_id=$id");
        }
    }

    function delete_movimiento($num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_i_mov_inv_pt WHERE mov_documento='$num_secuencial' and (trs_id=12 or trs_id=13)");
        }
    }

    function delete_det_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_det_nota_credito WHERE ncr_id='$id'");
        }
    }

    function lista_un_nota_fac($fac) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito n, erp_i_cliente c, erp_vendedores v where n.cli_id=c.cli_id and n.vnd_id=v.vnd_id and n.fac_id='$fac' and n.fac_id<>0");
        }
    }
    
    function lista_un_prod_id($id,$tbl) {
        if ($this->con->Conectar() == true) {
            if($tbl==0){ //Comerciales
                $query="SELECT 
                    pro_codigo as cod,
                    pro_descripcion as desc,
                    '' as cod_aux
                    FROM erp_i_productos where pro_id=$id";
            }else{ //Industrial
                $query="SELECT 
                    pro_a as cod,
                    pro_b as desc,
                    pro_ad as cod_aux
                    FROM erp_productos where id=$id ";
            }
            return pg_query($query);
        }
    }
    
    function lista_secuencial_locales($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT n.ncr_numero as secuencial FROM  erp_nota_credito n, emisor e where n.emi_id=e.cod_punto_emision and n.emi_id=$emi order by n.ncr_numero desc limit 1");
        }
    }
    
    function insert_cheques($data) {
        if ($this->con->Conectar() == true) {
            return pg_query(" INSERT INTO erp_cheques(
                                                      cli_id,
                                                      chq_nombre,
                                                      chq_banco,
                                                      chq_numero,
                                                      chq_recepcion,
                                                      chq_fecha, 
                                                      chq_monto,
                                                      chq_estado,
                                                      chq_observacion,
                                                      chq_tipo_doc,
                                                      chq_deposito,
                                                      chq_cobro,
                                                      doc_id,
                                                      pag_id)
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
                                                      '$data[12]',
                                                      '$data[13]'
                                                        )");
        }
    }
    
   function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.cas_id='$id' and c.pln_estado=0");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
