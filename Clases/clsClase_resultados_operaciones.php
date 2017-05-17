<?php

include_once 'Conn.php';

class Clase_resultados_operaciones {

    var $con;

    function Clase_resultados_operaciones() {
        $this->con = new Conn();
    }

    function lista_materia_prima($d, $h) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT sum(m.mov_cantidad) as cnt, sum(m.mov_peso_total) as val FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp
//                              where m.trs_id=t.trs_id and m.mp_id=mp.mp_id and t.trs_operacion=1 and mov_fecha_trans between '$d' and '$h' and mp.mpt_id between 1 and 25 and mov_num_orden like 'EC-%'");
            return pg_query("CREATE OR REPLACE VIEW costo_mp_resultados AS 
                                SELECT m.mp_id,sum(m.mov_cantidad) as cantidad, 
                                case when (select  c.cmp_valor from erp_costos_mp c where  m.mp_id=c.mp_id and c.cmp_fecha between '$d' and '$h' and cmp_tabla=0 order by cmp_fecha desc limit 1) is null then 0 else
                                (select  c.cmp_valor from erp_costos_mp c where  m.mp_id=c.mp_id and c.cmp_fecha between '$d' and '$h' and cmp_tabla=0 order by cmp_fecha desc limit 1) 
                                end
                                FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp 
                                where m.trs_id=t.trs_id and m.mp_id=mp.mp_id and t.trs_operacion=1 and mov_fecha_trans between '$d' and '$h' and mp.mpt_id between 1 and 25 and mov_num_orden like 'EC-%'
                                group by m.mp_id;
                                select sum(cantidad) as cnt,sum(cantidad*cmp_valor) as val, sum(cantidad*cmp_valor)/sum(cantidad) as val_unit from costo_mp_resultados
                                ");
        }
    }

    function lista_insumos($d, $h) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT sum(m.mov_cantidad) as cnt, sum(m.mov_peso_total) as val FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp
//                              where m.trs_id=t.trs_id and m.mp_id=mp.mp_id and t.trs_operacion=1 and mov_fecha_trans between '$d' and '$h' and mp.mpt_id between 26 and 99 and mov_num_orden like 'EC-%'");
            return pg_query("CREATE OR REPLACE VIEW costo_insumos_resultados AS 
                                SELECT m.mp_id,sum(m.mov_cantidad) as cantidad, 
                                case when (select  c.cmp_valor from erp_costos_mp c where  m.mp_id=c.mp_id and c.cmp_fecha between '$d' and '$h' and cmp_tabla=0 order by cmp_fecha desc limit 1) is null then 0 else
                                (select  c.cmp_valor from erp_costos_mp c where  m.mp_id=c.mp_id and c.cmp_fecha between '$d' and '$h' and cmp_tabla=0 order by cmp_fecha desc limit 1) 
                                end
                                FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp 
                                where m.trs_id=t.trs_id and m.mp_id=mp.mp_id and t.trs_operacion=1 and mov_fecha_trans between '$d' and '$h' and mp.mpt_id between 26 and 99 and mov_num_orden like 'EC-%'
                                group by m.mp_id;
                                select sum(cantidad) as cnt,sum(cantidad*cmp_valor) as val, sum(cantidad*cmp_valor)/sum(cantidad) as val_unit from costo_insumos_resultados
                                ");
        }
    }

    function lista_rubros($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_rubros");
        }
    }

    function lista_secuencial() {
        if ($this->con->Conectar() == true) {
            return pg_query("select rop_secuencial from erp_resultado_operaciones order by rop_secuencial desc");
        }
    }

    function lista_extruido($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query(" select(select sum(rec_peso_primario) from erp_reg_op_ecocambrella where rec_estado=0 and rec_fecha between '$d' and '$h') as conforme,
                                    (select sum(rec_peso_primario) from erp_reg_op_ecocambrella where rec_estado=3 and rec_fecha between '$d' and '$h') as noconforme");
        }
    }

    function lista_materia_prima_corte($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(m.mov_cantidad) as cnt FROM erp_mov_inv_semielaborado m, erp_transacciones t, erp_i_productos p
                              where m.trs_id=t.trs_id and m.pro_id=p.pro_id and t.trs_operacion=1 and mov_fecha_trans between '$d' and '$h' and mov_guia_transporte like 'C-%'");
        }
    }

    function lista_insumos_corte($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(m.mov_cantidad) as cnt, sum(m.mov_peso_total) as val FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp
                              where m.trs_id=t.trs_id and m.mp_id=mp.mp_id and t.trs_operacion=1 and mov_fecha_trans between '$d' and '$h' and mp.mpt_id between 26 and 99 and mov_num_orden like 'C-%'");
        }
    }

    function insert_resultados($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_resultado_operaciones(
                                                                    rop_cnt_mp, 
                                                                    rop_ext_unit_mp, 
                                                                    rop_ext_costo_mp, 
                                                                    rop_ext_cnt_ins, 
                                                                    rop_ext_unit_ins, 
                                                                    rop_ext_costo_ins, 
                                                                    tot_costo_extrusion, 
                                                                    tot_prod_conforme, 
                                                                    tot_prod_noconforme, 
                                                                    rop_tot_producido, 
                                                                    rop_unit_global, 
                                                                    rop_unit_conforme, 
                                                                    rop_unit_noconforme, 
                                                                    rop_crt_cnt_mp, 
                                                                    rop_crt_unit_mp, 
                                                                    rop_crt_costo_mp, 
                                                                    rop_crt_cnt_produccion, 
                                                                    rop_crt_unit_produccion, 
                                                                    rop_crt_costo_produccion, 
                                                                    rop_crt_cnt_consumo, 
                                                                    rop_crt_unit_consumo, 
                                                                    rop_crt_costo_consumo, 
                                                                    rop_cnt_insumos, 
                                                                    rop_crt_unit_insumos, 
                                                                    rop_crt_costo_insumo, 
                                                                    rop_costo_corte, 
                                                                    rop_total_operacion,
                                                                    rop_secuencial,
                                                                    rop_anio,
                                                                    rop_mes)
                                                                    values(
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
                                                                    '$data[28]',
                                                                    '$data[29]'
                                                                    )
                            ");
        }
    }

    function lista_unresultado_operacion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_resultado_operaciones where rop_secuencial='$id'");
        }
    }

    function insert_detalle($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_resultado_operaciones(
            rop_id,
            rub_id,
            dro_costo,
            dro_tipo
            )
            VALUES ($data[0],$data[1],$data[2],$data[3])");
        }
    }

    function lista_resultado_anterior($m, $a) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_resultado_operaciones where rop_mes='$m' and rop_anio='$a'");
        }
    }

    function lista_resultado_operaciones($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_resultado_operaciones $txt");
        }
    }

    function lista_resultado_operacion_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_resultado_operaciones where rop_id='$id'");
        }
    }

    function lista_detalle($id, $t) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_det_resultado_operaciones where rop_id=$id and dro_tipo=$t");
        }
    }

    function lista_suma_detalle($id, $t) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(dro_costo) from erp_det_resultado_operaciones where rop_id=$id and dro_tipo=$t");
        }
    }

    function update_resultados($id, $data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_resultado_operaciones SET
                                                                    rop_cnt_mp='$data[0]', 
                                                                    rop_ext_unit_mp='$data[1]', 
                                                                    rop_ext_costo_mp='$data[2]', 
                                                                    rop_ext_cnt_ins='$data[3]', 
                                                                    rop_ext_unit_ins='$data[4]', 
                                                                    rop_ext_costo_ins='$data[5]', 
                                                                    tot_costo_extrusion='$data[6]', 
                                                                    tot_prod_conforme='$data[7]', 
                                                                    tot_prod_noconforme='$data[8]', 
                                                                    rop_tot_producido='$data[9]', 
                                                                    rop_unit_global='$data[10]', 
                                                                    rop_unit_conforme='$data[11]', 
                                                                    rop_unit_noconforme='$data[12]', 
                                                                    rop_crt_cnt_mp='$data[13]', 
                                                                    rop_crt_unit_mp='$data[14]', 
                                                                    rop_crt_costo_mp='$data[15]', 
                                                                    rop_crt_cnt_produccion='$data[16]', 
                                                                    rop_crt_unit_produccion='$data[17]', 
                                                                    rop_crt_costo_produccion='$data[18]', 
                                                                    rop_crt_cnt_consumo='$data[19]', 
                                                                    rop_crt_unit_consumo='$data[20]', 
                                                                    rop_crt_costo_consumo='$data[21]', 
                                                                    rop_cnt_insumos='$data[22]', 
                                                                    rop_crt_unit_insumos='$data[23]', 
                                                                    rop_crt_costo_insumo='$data[24]', 
                                                                    rop_costo_corte='$data[25]', 
                                                                    rop_total_operacion='$data[26]',
                                                                    rop_secuencial='$data[27]',
                                                                    rop_anio='$data[28]',
                                                                    rop_mes='$data[29]',
                                                                    rop_estado='1'
                                                                    where rop_id=$id            
                                                                    ");
        }
    }

    function delete_detalle($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_det_resultado_operaciones where rop_id=$id");
        }
    }
    
     function delete_resultados($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_resultado_operaciones where rop_id=$id");
        }
    }

}

?>
