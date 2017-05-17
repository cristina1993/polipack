<?php

include_once 'Conn.php';

class Clase_resultados {

    var $con;

    function Clase_resultados() {
        $this->con = new Conn();
    }

    function Lista_Universal($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query(" 
                    SELECT op.ord_id,op.ord_num_orden,op.pro_id,pr.pro_codigo,pr.pro_descripcion,pr.pro_uni,op.ord_fec_pedido FROM erp_i_orden_produccion op, erp_i_productos pr WHERE op.pro_id=pr.pro_id $txt
                    union 
                    SELECT op.ord_id,op.ord_num_orden,op.ord_pro_secundario,pr.pro_codigo,pr.pro_descripcion,pr.pro_uni,op.ord_fec_pedido FROM erp_i_orden_produccion op, erp_i_productos pr WHERE op.ord_pro_secundario=pr.pro_id $txt
                    union 
                    SELECT op.ord_id,op.ord_num_orden,op.ord_pro3,pr.pro_codigo,pr.pro_descripcion,pr.pro_uni,op.ord_fec_pedido FROM erp_i_orden_produccion op, erp_i_productos pr WHERE op.ord_pro3=pr.pro_id $txt
                    union 
                    SELECT op.ord_id,op.ord_num_orden,op.ord_pro4,pr.pro_codigo,pr.pro_descripcion,pr.pro_uni,op.ord_fec_pedido FROM erp_i_orden_produccion op, erp_i_productos pr WHERE op.ord_pro4=pr.pro_id $txt
                    ORDER BY ord_fec_pedido desc,ord_num_orden  ");
        }
    }

    function lista_ultima_fecha($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_det_rubros where drb_fecha BETWEEN '$d' AND '$h' order by drb_fecha desc limit 1 ");
        }
    }

    function lista_suma_costos($f) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(d.drb_valor) from erp_det_rubros d, erp_rubros r where d.rub_id=r.rub_id and d.drb_fecha ='$f' and d.drb_estado=0 and r.rub_tipo=0) as mod,
                                    (select sum(d.drb_valor) from erp_det_rubros d, erp_rubros r where d.rub_id=r.rub_id and d.drb_fecha ='$f' and d.drb_estado=0 and r.rub_tipo=1) as moi,
                                    (select sum(d.drb_valor) from erp_det_rubros d, erp_rubros r where d.rub_id=r.rub_id and d.drb_fecha ='$f' and d.drb_estado=0 and r.rub_tipo=2) as cdf,
                                    (select sum(d.drb_valor) from erp_det_rubros d, erp_rubros r where d.rub_id=r.rub_id and d.drb_fecha ='$f' and d.drb_estado=0 and r.rub_tipo=3) as cif,
                                    (select sum(d.drb_valor) from erp_det_rubros d, erp_rubros r where d.rub_id=r.rub_id and d.drb_fecha ='$f' and d.drb_estado=0 and r.rub_tipo=4) as cv");
        }
    }

    function lista_totales_ingreso($ord) {
        if ($this->con->Conectar() == true) {
//            return pg_query("select i.ing_mp_estado,sum(i.ing_mp_cant) as consumo,sum(i.ing_mp_cant*i.ing_mp_cost_uni) as total, sum(i.ing_mp_cant*i.ing_mp_cost_uni)/sum(i.ing_mp_cant) as unitario  from par_ingreso_mp i where i.ord_pedido_id=$id group by i.ing_mp_estado,i.ord_pedido_id order by i.ing_mp_estado");
            return pg_query("SELECT sum(mov_cantidad) FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp
                                                    WHERE t.trs_id=m.trs_id and t.trs_operacion=1 and m.mp_id=mp.mp_id
                                                    and m.mov_num_orden='$ord' and mp.mpt_id between 26 and 99");
        }
    }

    function listaSellPedido($ped_codigo) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(reg_sell_cant_fundas) as cfundas, sum(reg_sell_peso_bulto* reg_sell_bultos) as pfundas, sum(reg_sell_cant_rollos) as rollos   FROM par_registro_sellado WHERE ord_pedido_id=$ped_codigo");
        }
    }

    function listaImpPedido($ped_codigo) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT count(reg_imp_num_rollo),sum(reg_imp_peso) FROM par_registro_impresion WHERE ord_pedido_id=" . $ped_codigo);
        }
    }

    function listaExtPedido($ped_codigo,$pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT count(ord_id),sum(rec_peso_primario) FROM erp_reg_op_ecocambrella 
                                         WHERE ord_id=$ped_codigo and pro_id=$pro ");
        }
    }

    function listaFechasExtrusion($ped_codigo,$pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT rec_fecha FROM erp_reg_op_ecocambrella WHERE ord_id=$ped_codigo and pro_id=$pro order by rec_fecha asc limit 1) as fec_ini,
                                   (SELECT rec_fecha FROM erp_reg_op_ecocambrella WHERE ord_id=$ped_codigo and pro_id=$pro order by rec_fecha desc limit 1) as fec_fin
                                  ");
        }
    }

    function listaFechasImpresion($ped_codigo) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT reg_imp_fecha FROM par_registro_impresion WHERE ord_pedido_id=$ped_codigo order by reg_imp_fecha asc limit 1) as fec_ini,
                                   (SELECT reg_imp_fecha FROM par_registro_impresion WHERE ord_pedido_id=$ped_codigo order by reg_imp_fecha desc limit 1) as fec_fin,
                                   (SELECT cast(reg_imp_fecha ||' '||reg_imp_hora as timestamp) FROM par_registro_extrusion WHERE ord_pedido_id=$ped_codigo order by reg_ext_fecha asc limit 1) as hor_ini,
                                   (SELECT cast(reg_imp_fecha ||' '||reg_imp_hora as timestamp) FROM par_registro_extrusion WHERE ord_pedido_id=$ped_codigo order by reg_ext_fecha desc limit 1) as hor_fin");
        }
    }

    function listaFechasSellado($ped_codigo) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT reg_sell_fecha FROM par_registro_sellado WHERE ord_pedido_id=$ped_codigo order by reg_sell_fecha asc limit 1) as fec_ini,
                                   (SELECT reg_sell_fecha FROM par_registro_sellado WHERE ord_pedido_id=$ped_codigo order by reg_sell_fecha desc limit 1) as fec_fin,
                                   (SELECT  cast(reg_sell_fecha ||' '||reg_sell_hora as timestamp) FROM par_registro_sellado WHERE ord_pedido_id=$ped_codigo order by reg_sell_fecha asc limit 1) as hor_ini,
                                   (SELECT  cast(reg_sell_fecha ||' '||reg_sell_hora as timestamp) FROM par_registro_sellado WHERE ord_pedido_id=$ped_codigo order by reg_sell_fecha desc limit 1) as hor_fin");
        }
    }

    function hora_anterior_extrusion($f) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT cast('$f' as timestamp)-cast(reg_ext_fecha ||' '||reg_ext_hora as timestamp) as reg_ext_hora FROM par_registro_extrusion where cast(reg_ext_fecha ||' '||reg_ext_hora as timestamp)<'$f' order by cast(reg_ext_fecha ||' '||reg_ext_hora as timestamp) desc limit 1");
            return pg_query("SELECT cast(reg_ext_fecha ||' '||reg_ext_hora as timestamp) as reg_ext_hora,reg_ext_fecha FROM par_registro_extrusion where cast(reg_ext_fecha ||' '||reg_ext_hora as timestamp)<'$f' order by cast(reg_ext_fecha ||' '||reg_ext_hora as timestamp) desc limit 1");
        }
    }
    
    function suma_horas($fi,$ff) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cast('$ff' as timestamp)-cast('$fi' as timestamp) as sum_hora");
        }
    }
    
    function hora_anterior_impresion($f) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cast(reg_imp_fecha ||' '||reg_imp_hora as timestamp) as reg_imp_hora,reg_imp_fecha FROM par_registro_impresion where cast(reg_imp_fecha ||' '||reg_imp_hora as timestamp)<'$f' order by cast(reg_imp_fecha ||' '||reg_imp_hora as timestamp) desc limit 1");
        }
    }

     function hora_anterior_sellado($f) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cast(reg_sell_fecha ||' '||reg_sell_hora as timestamp) as reg_sell_hora,reg_sell_fecha FROM par_registro_sellado where cast(reg_sell_fecha ||' '||reg_sell_hora as timestamp)<'$f' order by cast(reg_sell_fecha ||' '||reg_sell_hora as timestamp) desc limit 1");
        }
    }
}
