<?php

include_once 'Conn.php';

class Clase_costo_semielaborado {

    var $con;

    function Clase_costo_semielaborado() {
        $this->con = new Conn();
    }

    function Lista_Universal($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from ordenes_costo_extrusion $txt");
        }
    }

    function lista_otros_costo($a, $m) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT o.*,sum(dro_costo)/o.tot_prod_conforme as dro_costo from erp_resultado_operaciones o, erp_det_resultado_operaciones d 
                            where o.rop_id=d.rop_id and o.rop_anio='$a' and o.rop_mes='$m' and d.dro_tipo=0 group by o.rop_id");
        }
    }

    function lista_costo_mp($o, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("CREATE OR REPLACE VIEW costo_mp_orden AS
                                select m.mp_id,sum(m.mov_cantidad) as cantidad, 
                                case when (select  c.cmp_valor from erp_costos_mp c where  m.mp_id=c.mp_id and c.cmp_fecha <= '$h' and cmp_tabla=0 order by cmp_fecha desc limit 1) is null then 0 else
                                (select  c.cmp_valor from erp_costos_mp c where  m.mp_id=c.mp_id and c.cmp_fecha <='$h' and cmp_tabla=0 order by cmp_fecha desc limit 1) 
                                end 
                                FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp
                                WHERE t.trs_id=m.trs_id and t.trs_operacion=1 and m.mp_id=mp.mp_id
                                and m.mov_num_orden='$o' and mp.mpt_id between 1 and 26
                                group by m.mp_id;
                                select sum(cantidad) as cnt,sum(cantidad*cmp_valor) as val, sum(cantidad*cmp_valor)/sum(cantidad) as val_unit from costo_mp_orden
                            ");
        }
    }

    function lista_costo_insumos($o, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("CREATE OR REPLACE VIEW costo_insumo_orden AS
                                select m.mp_id,sum(m.mov_cantidad) as cantidad, 
                                case when (select  c.cmp_valor from erp_costos_mp c where  m.mp_id=c.mp_id and c.cmp_fecha <= '$h' and cmp_tabla=0 order by cmp_fecha desc limit 1) is null then 0 else
                                (select  c.cmp_valor from erp_costos_mp c where  m.mp_id=c.mp_id and c.cmp_fecha <='$h' and cmp_tabla=0 order by cmp_fecha desc limit 1) 
                                end 
                                FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp
                                WHERE t.trs_id=m.trs_id and t.trs_operacion=1 and m.mp_id=mp.mp_id
                                and m.mov_num_orden='$o' and mp.mpt_id between 26 and 99
                                group by m.mp_id; 
                                select sum(cantidad) as cnt,sum(cantidad*cmp_valor) as val, sum(cantidad*cmp_valor)/sum(cantidad) as val_unit from costo_insumo_orden");
        }
    }

    function lista_central_costos($p, $f,$t) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_costos_mp where mp_id=$p and cmp_fecha='$f' and cmp_tabla=$t");
        }
    }

}
