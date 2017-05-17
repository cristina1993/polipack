<?php

include_once 'Conn.php';

class Clase_reg_padding {

    var $con;

    function Clase_reg_padding() {
        $this->con = new Conn();
    }

    function lista_buscador_orden($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_op_padding r, erp_i_orden_produccion_padding o, erp_i_productos p where r.opp_id=o.opp_id and o.pro_id=p.pro_id $txt order by rpa_numero");
        }
    }

    function lista_secuencial_registro() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_op_padding order by rpa_numero desc limit 1");
        }
    }

    function lista_un_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos where pro_id=$id");
        }
    }

    function lista_una_orden_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_padding o, erp_i_cliente c WHERE o.cli_id=c.cli_id and o.opp_codigo='$id'");
        }
    }

    function insert_registro_padding($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_reg_op_padding(
                                        opp_id,
                                        rpa_fecha,
					rpa_numero,					
                                        rpa_lote,
					rpa_peso, 
					rpa_rollo,
                                        rpa_estado,
					rpa_semielaborado,
					rpa_lote_semielaborado,
                                        maq_id,
                                        rpa_observaciones
					)
                              VALUES ( '$data[0]',
                                       '$data[1]',
                                       '$data[2]',
                                       '$data[3]',
                                       '$data[4]',
                                       '$data[5]',
                                       '$data[6]',
                                       '$data[7]',
                                       '$data[8]',
                                       '$data[9]',
                                       '$data[10]')");
        }
    }

    function update_registro_padding($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_op_padding
					SET rpa_fecha='$data[0]', 
					rpa_peso='$data[1]',
					rpa_rollo='$data[2]',
					rpa_desperdicio='$data[3]', 
					rpa_operador='$data[4]', 
					opp_id='$data[5]',
					rpa_numero='$data[6]' where rpa_id=$id");
        }
    }

    function delete_registro_padding($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE from erp_reg_op_padding WHERE rpa_id=$id");
        }
    }

    function lista_produccion_pedido($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(rpa_rollo) as rollo,sum(rpa_peso) as peso FROM erp_reg_op_padding where opp_id=$id");
        }
    }

    function lista_secuencial_transferencia_terminado() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_secuencial  ORDER BY sec_id DESC LIMIT 1");
        }
    }

    function insert_sec_transferencia_terminado($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_secuencial(sec_transferencias) VALUES ('$data')");
        }
    }

    function insert_transferencia_terminado($data) {
        if ($this->con->Conectar() == true) {
            $f = date('Y-m-d');
            $h = date('H:i');
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_cantidad,                
                mov_tabla,                
                mov_fecha_registro,
                mov_hora_registro,
                mov_usuario,
                mov_pago,
                mov_flete
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$data[4]',   
            '$data[5]',
            '$data[6]',   
            '$data[7]',
            '$data[8]',
            '$f',
            '$h',
            '$usu',
            '$data[9]',
            '$data[10]')");
        }
    }

    function lista_una_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_padding o, erp_i_cliente c WHERE o.cli_id=c.cli_id and o.opp_id='$id'");
        }
    }

    function lista_detalle_orden($id) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM erp_det_orden_padding d, erp_i_productos p WHERE d.pro_id=p.pro_id and d.opp_id='$id'");
            return pg_query("SELECT i.mov_id,d.opp_id,i.pro_id,d.pro_lote,i.mov_cantidad,i.mov_pago,p.pro_codigo,p.pro_descripcion,p.pro_uni,i.trs_id  
                            FROM erp_det_orden_padding d, erp_i_productos p, erp_mov_inv_semielaborado i , erp_transacciones t
                            WHERE d.pro_id=p.pro_id and d.opp_id='$id' and i.pro_id=p.pro_id  and t.trs_id=i.trs_id  and substring(i.mov_pago from  1 for 7)=d.pro_lote and t.trs_operacion=0
                            order by pro_lote,mov_pago");
        }
    }

    function lista_secuencial_transferencia() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_secuencial_semielaborado   ORDER BY sec_id DESC LIMIT 1");
        }
    }

    function insert_sec_transferencia($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_secuencial_semielaborado(sec_transferencias) VALUES ('$data')");
        }
    }

    function insert_transferencia($data) {
        if ($this->con->Conectar() == true) {
            $f = date('Y-m-d');
            $h = date('H:i');
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_mov_inv_semielaborado(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_cantidad,                
                mov_tabla,                
                mov_fecha_registro,
                mov_hora_registro,
                mov_usuario,
                mov_pago,
                mov_flete
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$data[4]',   
            '$data[5]',
            '$data[6]',   
            '$data[7]',
            '$data[8]',
            '$f',
            '$h',
            '$usu',
            '$data[9]',
            '$data[10]')");
        }
    }

    function lista_maquinas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_maquinas where ids='2' ORDER BY maq_a");
        }
    }

    function lista_ordenes() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT opp_codigo,pro_descripcion FROM erp_i_orden_produccion_padding o, erp_i_productos p where o.pro_id=p.pro_id and (select sum(rpa_peso) from erp_reg_op_padding p where p.opp_id=o.opp_id)<(o.pro_mf3)
                            union 
                            select opp_codigo,pro_descripcion FROM erp_i_orden_produccion_padding o, erp_i_productos p where o.pro_id=p.pro_id and not exists(select * from erp_reg_op_padding p where p.opp_id=o.opp_id) order by opp_codigo desc
                            ");
        }
    }

    function total_inventario($id, $lt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt') as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt') as egreso");
        }
    }

    function registros_productos($ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("select r.rpa_id,r.opp_id,r.rpa_peso,p.pro_id,r.rpa_lote,r.rpa_estado,rpa_lote_semielaborado,rpa_semielaborado,rpa_rollo from erp_reg_op_padding r, erp_i_orden_produccion_padding p where r.opp_id=p.opp_id and r.opp_id=$ord
                               ");
        }
    }

    function lista_combo_empa_core($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mp m, erp_i_tpmp t where m.mpt_id=t.mpt_id and m.mpt_id='$id' ORDER BY mp_referencia");
        }
    }

    function lista_un_rollo_semielaborado($id, $lt) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM erp_det_orden_padding d, erp_i_productos p WHERE d.pro_id=p.pro_id and d.opp_id='$id'");
            return pg_query("SELECT i.mov_id,d.opp_id,i.pro_id,d.pro_lote,i.mov_cantidad,i.mov_pago,p.pro_codigo,p.pro_descripcion,p.pro_uni,i.trs_id ,i.mov_flete 
                            FROM erp_det_orden_padding d, erp_i_productos p, erp_mov_inv_semielaborado i , erp_transacciones t
                            WHERE d.pro_id=p.pro_id and d.opp_id='$id' and i.pro_id=p.pro_id  and t.trs_id=i.trs_id  and substring(i.mov_pago from  1 for 7)=d.pro_lote and t.trs_operacion=0 and i.mov_pago='$lt'
                            order by pro_lote,mov_pago");
        }
    }

    function lista_etiquetas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_etiquetas order by eti_descripcion");
        }
    }

    function insert_etiqueta($data) {
        if ($this->con->Conectar() == true) {
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_etiqueta_grande(
                ord_id, 
                pro_id, 
                cli_id, 
                etg_tipo, 
                etg_numero,
                etg_copias,
                etg_fecha,
                etg_pallet1,
                etg_estado,
                etg_procedencia,
                etg_tamano,
                etg_peso_neto,
                etg_peso_bruto,
                etg_espacio8,
                etg_operador,
                etg_observaciones
            )
    VALUES ('$data[0]',
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
            '$data[13]',      
            '$data[14]',      
            '$data[15]'      
            )");
        }
    }

    function lista_regextrusion_rollo($lt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_op_ecocambrella r, erp_i_orden_produccion o, erp_i_cliente c where r.ord_id=o.ord_id and c.cli_id=o.cli_id and rec_lote='$lt'");
        }
    }

    function lista_cambia_status_det($id, $sts, $pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_det_ped_venta SET det_estado='$sts' where ped_id=$id and pro_id=$pro");
        }
    }

    function lista_cambia_status_pedido($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta SET ped_estado='$sts' where ped_id=$id");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
