<?php

include_once 'Conn.php';

class Clase_Orden_Padding {

    var $con;

    function Clase_Orden_Padding() {
        $this->con = new Conn();
    }

    function lista() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_orden_produccion_padding e, erp_i_productos p, erp_empresa m where e.pro_id=p.pro_id and m.emp_id=e.emp_id ORDER BY opp_id DESC");
        }
    }

    function lista_mostrar($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$emp ");
        }
    }

    function lista_capturar($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos p, erp_i_orden_produccion_padding e where e.pro_id=p.pro_id and p.pro_id=$emp ORDER BY e.opp_id DESC LIMIT 1");
        }
    }

    function lista_secuencial($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa e, erp_i_orden_produccion_padding p
where e.emp_id=p.emp_id and e.emp_id=$emp ORDER BY opp_codigo DESC LIMIT 1 ");
        }
    }

    function lista_siglas($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id=$emp");
        }
    }

    function lista_uno($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT o.*, p.pro_capa FROM  erp_i_orden_produccion_padding o, erp_i_productos p where o.opp_id=$id and o.pro_id=p.pro_id");
        }
    }

    function lista_buscador($fec1, $fec2, $ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT op.*,p.pro_descripcion,e.*,cl.* FROM erp_i_orden_produccion_padding op, erp_i_productos p, erp_empresa e, erp_i_cliente cl where op.cli_id=cl.cli_id and p.pro_id=op.pro_id and e.emp_id=op.emp_id and op.opp_fec_pedido between '$fec1' and '$fec2' and op.opp_codigo like '%$ord%'");
        }
    }

    function insert($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_orden_produccion_padding(
              opp_codigo,
              cli_id,
              pro_id,
              emp_id,
              opp_cantidad,
              opp_fec_pedido,
              opp_fec_entrega,
              pro_ancho,
              pro_largo,
              pro_peso,
              pro_gramaje,
              opp_refilado1,
              opp_refilado2,
              pro_mp1,
              pro_mp2,
              pro_mp3,
              pro_mp4,
              pro_mp5,
              pro_mp6,
              pro_mf1,
              pro_mf2,
              pro_mf3,
              pro_mf4,
              opp_kg1,
              opp_kg2,
              opp_kg3,
              opp_kg4,
              opp_kg5,
              opp_kg6,
              opp_velocidad,
              opp_temp_rodillosup,
              opp_temp_rodilloinf,
              opp_observaciones,
              det_id,
              opp_espesor_prod,
              opp_por_espesor,
              opc_ancho,
              mp_cnt1,
              mp_cnt2,
              mp_cnt3,
              mp_cnt4,
              mp_cnt5,
              mp_cnt6,
              opp_status,
              ped_id,
              opp_etiqueta,
              opp_eti_numero
            )
    VALUES ('$data[0]',$data[1],$data[2],5,'$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]',$data[12],$data[13],$data[14],$data[15],'$data[16]','$data[17]','$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]','$data[24]','$data[25]','$data[26]','$data[27]','$data[28]','$data[29]','$data[30]','$data[31]','$data[32]','$data[33]','$data[34]','$data[35]','$data[36]','$data[37]','$data[38]','$data[39]','$data[40]','$data[41]','PROGRAMADO','$data[42]','$data[43]','$data[44]')");
        }
    }

    function upd($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_orden_produccion_padding SET 
                    opp_codigo='$data[0]',
                    cli_id=$data[1],
                    pro_id=$data[2],
                    opp_cantidad='$data[3]',
                    opp_fec_pedido='$data[4]',
                    opp_fec_entrega='$data[5]',
                    pro_ancho='$data[6]',
                    pro_largo='$data[7]',
                    pro_peso='$data[8]',
                    pro_gramaje='$data[9]',
                    opp_refilado1='$data[10]',
                    opp_refilado2='$data[11]',
                    pro_mp1=$data[12],
                    pro_mp2=$data[13],
                    pro_mp3=$data[14],
                    pro_mp4=$data[15],
                    pro_mp5='$data[16]',
                    pro_mp6='$data[17]',
                    pro_mf1='$data[18]',
                    pro_mf2='$data[19]',
                    pro_mf3='$data[20]',
                    pro_mf4='$data[21]',
                    opp_kg1='$data[22]',
                    opp_kg2='$data[23]',
                    opp_kg3='$data[24]',
                    opp_kg4='$data[25]',
                    opp_kg5='$data[26]',
                    opp_kg6='$data[27]',
                    opp_velocidad='$data[28]',
                    opp_temp_rodillosup='$data[29]',
                    opp_temp_rodilloinf='$data[30]',
                    opp_observaciones='$data[31]',
                    det_id='$data[32]',
                    opp_espesor_prod='$data[33]',
                    opp_por_espesor='$data[34]',
                    opc_ancho='$data[35]',
                    mp_cnt1='$data[36]',
                    mp_cnt2='$data[37]',
                    mp_cnt3='$data[38]',
                    mp_cnt4='$data[39]',
                    mp_cnt5='$data[40]',
                    mp_cnt6='$data[41]',
                    opp_etiqueta='$data[43]',
                    opp_eti_numero='$data[44]'
                    WHERE opp_id=$id");
        }
    }

    function delete($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_orden_produccion_padding WHERE opp_id=$id");
        }
    }

    function lista_combo($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_id, trim(cli_raz_social) as nombres  
                            from  erp_i_cliente 
                            where cli_tipo <>'$tp'
                            order by nombres");
        }
    }

    function lista_combomp($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mp m, erp_i_tpmp t where m.mpt_id=t.mpt_id and emp_id=4 ORDER BY mp_referencia");
        }
    }

////////////////////////////////////////////////////////////////////////// Ordenes Padding

    function lista_produccion_pedido($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(rpa_rollo) as rollo, sum(rpa_peso) as peso FROM erp_reg_op_padding WHERE opp_id=$id");
        }
    }

    function lista_una_orden_produccion_padding($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_padding WHERE opp_id=$id");
        }
    }

    function lista_un_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos WHERE pro_id=$id");
        }
    }

    function lista_un_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente WHERE cli_id=$id");
        }
    }

    function lista_cambia_status_det($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_det_ped_venta SET det_estado='$sts' where det_id=$id");
        }
    }

////// Pedido mp//////

    function lista_ped_sec() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_pedido_mp
                                                    ORDER BY ped_orden desc
                                                    limit 1 ");
        }
    }

    function insert_pmp($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_pedido_mp (
				  ped_orden,
				  ped_fecha,
				  emp_id,
				  mp_id,
				  ped_det_cant,
				  ped_det_peso,
                                  ped_num_orden)VALUES(
                                 '$data[0]',
                                 '$data[1]',
                                 '$data[2]',
                                 '$data[3]',
                                 '$data[4]',
                                 '$data[5]',
                                 '$data[6]')");
        }
    }

    function del_pmp_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_pedido_mp WHERE ped_num_orden='$id'");
        }
    }

////////////////////////////////////corte///////////////////////////////////////////////////       
//    function lista_productos_semielaborados($id,$ei,$ef, $div) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select p.pro_id, p.pro_codigo, p.pro_descripcion, p.pro_ancho,p.pro_espesor, m.mov_pago, 
//                                    (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.pro_id=p.pro_id and m.trs_id=t.trs_id and t.trs_operacion= '0') as ingreso,
//                                    (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.pro_id=p.pro_id and m.trs_id=t.trs_id and t.trs_operacion= '1') as egreso
//                                    from erp_i_productos p, erp_mov_inv_semielaborado m
//                                    where m.pro_id=p.pro_id $div and pro_espesor between '$ei' and '$ef'
//                                    group by p.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_ancho,p.pro_espesor, m.mov_pago  
//                                    order by p.pro_espesor, p.pro_codigo");
//        }
//    }

    function lista_productos_semielaborados($id, $ei, $ef, $div,$cons) {
        if ($this->con->Conectar() == true) {
//            return pg_query("select p.pro_id, p.pro_codigo, p.pro_descripcion, p.pro_ancho,p.pro_espesor, m.pro_lote, m.mvt_cant,m.estado
//                                    from erp_i_productos p, erp_movse_total m
//                                    where m.pro_id=p.pro_id $div and p.pro_espesor between '$ei' and '$ef' and m.estado=0  
//                                    order by p.pro_espesor, p.pro_codigo");
            return pg_query("SELECT m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_ancho,p.pro_espesor,p.pro_uni,substring(m.mov_pago from  1 for 7) as mov_pago FROM erp_mov_inv_semielaborado m, erp_i_productos p 
                                where m.pro_id=p.pro_id  and m.pro_id=p.pro_id $div and p.pro_espesor between '$ei' and '$ef' $cons 
                                group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_ancho,p.pro_espesor,p.pro_uni,substring(m.mov_pago from  1 for 7) 
                                ORDER BY p.pro_codigo, substring(m.mov_pago from  1 for 7)");
        }
    }

    function lista_combopro() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT *FROM erp_i_productos  where pro_tipo=1 ORDER BY pro_codigo");
        }
    }

    function lista_combo_empa_core($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mp m, erp_i_tpmp t where m.mpt_id=t.mpt_id and m.mpt_id='$id' ORDER BY mp_referencia");
        }
    }

    function lista_mp_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mp m, erp_i_tpmp t where m.mpt_id=t.mpt_id and m.mp_id='$id'");
        }
    }

    function lista_una_orden_numero($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_padding WHERE opp_codigo='$id'");
        }
    }

    function insert_detalle_orden($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_orden_padding (
				  opp_id,
				  pro_id,
				  pro_lote,
				  dtp_cant
				  )VALUES(
                                 '$data[0]',
                                 '$data[1]',
                                 '$data[2]',
                                 '$data[3]')");
        }
    }

    function delete_det_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_det_orden_padding WHERE opp_id=$id");
        }
    }

    function update_estado_mov($id, $lt, $std,$ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_movse_total SET estado='$std', mvt_orden='$ord' WHERE pro_id='$id' and pro_lote='$lt'");
        }
    }

    function lista_detalle_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * FROM erp_det_orden_padding d, erp_i_productos p WHERE p.pro_id=d.pro_id and d.opp_id=$id");
        }
    }

      function total_inventario($id,$lt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and substring(m.mov_pago from  1 for 7)='$lt') as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and substring(m.mov_pago from  1 for 7)='$lt') as egreso,
                                   (SELECT count(*) FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and substring(m.mov_pago from  1 for 7)='$lt') as cnt_ingreso,
                                   (SELECT count(*) FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and substring(m.mov_pago from  1 for 7)='$lt') as cnt_egreso");
        }
    }
    
    function lista_clientes_codigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_id='$id' ");
        }
    }
    
     function registros_productos($ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("   select * from erp_reg_op_padding where opp_id=$ord ");
        }
    }
    
     function lista_una_maquina($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_maquinas where id=$id");
        }
    }
    
     function lista_consumo_mp($ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT i.trs_id,i.mp_id,m.mp_codigo,m.mp_referencia, i.mov_num_orden,sum(mov_cantidad) as mov_cantidad 
                            FROM  erp_i_mp m, erp_i_mov_inventario i 
                            where m.mp_id=i.mp_id and i.trs_id=1 and i.mov_num_orden='$ord'
                            group by i.trs_id,i.mp_id,m.mp_codigo,m.mp_referencia, i.mov_num_orden order by mov_num_orden,mp_referencia");
        }
    }
    
    function lista_consumo_semi($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT rpa_semielaborado, substring(rpa_lote_semielaborado from  1 for 7) as orden,sum(rpa_peso) as peso FROM  erp_reg_op_padding where opp_id=$id group by rpa_semielaborado,substring(rpa_lote_semielaborado from  1 for 7)");
        }
    }
    
    function lista_cambia_status_pedido($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta SET ped_estado='$sts' where ped_id=$id");
        }
    }
    
    function lista_pedido($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_det_ped_venta where det_id=$id");
        }
    }
    
}

?>
