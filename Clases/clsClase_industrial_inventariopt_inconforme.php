<?php

include_once 'Conn.php';

class Clase_industrial_inventariopt_inconforme {

    var $con;

    function Clase_industrial_inventariopt_inconforme() {
        $this->con = new Conn();
    }

    function lista_buscar_inventariopt($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 7) as mov_pago,p.pro_tipo FROM erp_i_mov_inv_pt m, erp_i_productos p where m.pro_id=p.pro_id  and m.pro_id=p.pro_id and substring(m.mov_pago from  1 for 2)='EC' and mov_flete='3' $txt group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 7),p.pro_tipo 
                            union
                            select m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 6) as mov_pago,p.pro_tipo FROM erp_i_mov_inv_pt m, erp_i_productos p where m.pro_id=p.pro_id  and m.pro_id=p.pro_id and substring(m.mov_pago from  1 for 1)='C' and mov_flete='3' $txt group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 6),p.pro_tipo 
                            union
                            select m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 6) as mov_pago,p.pro_tipo FROM erp_i_mov_inv_pt m, erp_i_productos p where m.pro_id=p.pro_id  and m.pro_id=p.pro_id and substring(m.mov_pago from  1 for 1)!='C' and substring(m.mov_pago from  1 for 2)!='EC' and mov_flete='3' $txt group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 6),p.pro_tipo 
                            ORDER BY mov_pago, pro_id");
        }
    }

    function total_inventario($id, $lt, $txt, $c) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 0 $txt and m.mov_flete='3') as ingreso_inc,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 1 $txt and m.mov_flete='3') as egreso_inc,
                                   (SELECT SUM(m.mov_tabla) FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 0 $txt and m.mov_flete='3') as cnt_ingreso_inc,
                                   (SELECT SUM(m.mov_tabla) FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 1 $txt and m.mov_flete='3') as cnt_egreso_inc");
        }
    }

    function lista_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_padding where replace(opp_codigo,'-','')='$id'");
        }
    }

}

?>
