<?php

include_once 'Conn.php';

class Clase_semielaborado_inventario {

    var $con;

    function Clase_semielaborado_inventario() {
        $this->con = new Conn();
    }

    function lista_buscar_inventariopt($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 7) as mov_pago FROM erp_mov_inv_semielaborado m, erp_i_productos p where m.pro_id=p.pro_id  and m.pro_id=p.pro_id and mov_flete='0' $txt group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 7) ORDER BY p.pro_codigo, substring(m.mov_pago from  1 for 7)");
        }
    }

    function total_inventario($id,$txt,$lt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and substring(m.mov_pago from  1 for 7)='$lt' and mov_flete='0' $txt) as ingreso_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and substring(m.mov_pago from  1 for 7)='$lt' and mov_flete='0' $txt) as egreso_con,
                                   (SELECT count(*) FROM rollos_semielaborados where pro_id=$id and substring(mov_pago from  1 for 7)='$lt' and (ingreso>egreso or egreso is null) and mov_flete='0') as cnt_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and substring(m.mov_pago from  1 for 7)='$lt' and mov_flete='3' $txt) as ingreso_inc,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and substring(m.mov_pago from  1 for 7)='$lt' and mov_flete='3' $txt) as egreso_inc,
                                   (SELECT count(*) FROM rollos_semielaborados where pro_id=$id and substring(mov_pago from  1 for 7)='$lt' and (ingreso>egreso or egreso is null) and mov_flete='3') as cnt_inc
                                ");
        }
    }
    
     function lista_buscar_kardexpt($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT  * FROM  erp_mov_inv_semielaborado m, erp_transacciones t, erp_i_cliente c, erp_i_productos p where m.trs_id=t.trs_id and c.cli_id=m.cli_id and p.pro_id=m.pro_id $txt order by m.pro_id,p.pro_codigo,m.mov_fecha_trans,m.mov_documento asc");
        }
    }
    
     function lista_combo_transacciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_transacciones order by trs_descripcion");
        }
    }
    
    function total_ingreso_egreso($id, $fec1,$lt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0  and mov_fecha_trans<'$fec1') as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and mov_fecha_trans<'$fec1') as egreso");
        }
    }
    
     function crear_vista($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query(

                    "CREATE OR REPLACE VIEW rollos_semielaborados AS 
 SELECT m.pro_id,
    m.mov_pago,
    m.mov_flete,
    case when(SELECT sum(m2.mov_cantidad) AS sum
           FROM erp_mov_inv_semielaborado m2,
            erp_transacciones t2
          WHERE m2.trs_id = t2.trs_id AND t2.trs_operacion = 0 AND m.pro_id = m2.pro_id AND m.mov_pago::text = m2.mov_pago::text AND m.mov_flete = m2.mov_flete) is null then 0 else (SELECT sum(m2.mov_cantidad) AS sum
           FROM erp_mov_inv_semielaborado m2,
            erp_transacciones t2
          WHERE m2.trs_id = t2.trs_id AND t2.trs_operacion = 0 AND m.pro_id = m2.pro_id AND m.mov_pago::text = m2.mov_pago::text AND m.mov_flete = m2.mov_flete) end  AS ingreso,
    case when (SELECT sum(m2.mov_cantidad) AS sum
           FROM erp_mov_inv_semielaborado m2,
            erp_transacciones t2
          WHERE m2.trs_id = t2.trs_id AND t2.trs_operacion = 1 AND m.pro_id = m2.pro_id AND m.mov_pago::text = m2.mov_pago::text AND m.mov_flete = m2.mov_flete) is null then 0 else (SELECT sum(m2.mov_cantidad) AS sum
           FROM erp_mov_inv_semielaborado m2,
            erp_transacciones t2
          WHERE m2.trs_id = t2.trs_id AND t2.trs_operacion = 1 AND m.pro_id = m2.pro_id AND m.mov_pago::text = m2.mov_pago::text AND m.mov_flete = m2.mov_flete) end AS egreso
   FROM erp_mov_inv_semielaborado m,
    erp_transacciones t,
     erp_i_productos p
  WHERE m.trs_id = t.trs_id and m.pro_id=p.pro_id $txt
  GROUP BY m.pro_id, m.mov_pago, m.mov_flete
  ORDER BY m.pro_id, m.mov_pago"

                    );
        }
    }
    /////inventario x rollos
    
    function lista_buscar_inventario_rollos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,m.mov_pago FROM erp_mov_inv_semielaborado m, erp_i_productos p where m.pro_id=p.pro_id  and m.pro_id=p.pro_id $txt group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,m.mov_pago ORDER BY p.pro_codigo, m.mov_pago");
        }
    }
    
//     function total_inventario_rollos($id,$txt,$lt) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='0' $txt) as ingreso_con,
//                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='0' $txt) as egreso_con,
//                                   (SELECT count(*) FROM rollos_semielaborados where pro_id=$id and mov_pago='$lt' and (ingreso>egreso or egreso is null) and mov_flete='0') as cnt_con,
//                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='3' $txt) as ingreso_inc,
//                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='3' $txt) as egreso_inc,
//                                   (SELECT count(*) FROM rollos_semielaborados where pro_id=$id and mov_pago='$lt' and (ingreso>egreso or egreso is null) and mov_flete='3') as cnt_inc
//                                ");
//        }
//    }
    
      function total_inventario_rollos($id,$txt,$lt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='0' $txt) as ingreso_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='0' $txt) as egreso_con,
                                   (case when((SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='0' $txt)-
                                    (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='0' $txt))<0 then 0 else 1 end) as cnt_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='3' $txt) as ingreso_inc,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='3' $txt) as egreso_inc,
                                   (case when((SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='3' )-
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='3' $txt))<0 then 0 else 1 end) as cnt_inc
    
                                ");
        }
    }
    
    function lista_inventario_actual($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_inv_semielaborado_actual m, erp_i_productos p where m.pro_id=p.pro_id  $txt ORDER BY p.pro_codigo, m.mva_rollo");
        }
    }
    
    function lista_inventario_historico($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_inv_semielaborado_historico m, erp_i_productos p where m.pro_id=p.pro_id  $txt ORDER BY p.pro_codigo, m.mvh_rollo");
        }
    }
    
    function lista_buscar_inventario_actual($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mva_rollo from  1 for 7) as mov_pago, sum(m.mva_cantidad) as cantidad,sum(mva_peso) as peso  
                             FROM erp_inv_semielaborado_actual m, erp_i_productos p
                             where m.pro_id=p.pro_id  and m.pro_id=p.pro_id and mva_estado='0' $txt
                             group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mva_rollo from  1 for 7)
                             ORDER BY p.pro_codigo, substring(m.mva_rollo from  1 for 7)");
        }
    }
    
    function lista_buscar_inventario_historico($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mvh_rollo from  1 for 7) as mov_pago, sum(m.mvh_cantidad) as cantidad,sum(mvh_peso) as peso 
                            FROM erp_inv_semielaborado_historico m, erp_i_productos p 
                            where m.pro_id=p.pro_id  and m.pro_id=p.pro_id and mvh_estado='0' $txt 
                            group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mvh_rollo from  1 for 7) 
                            ORDER BY p.pro_codigo, substring(m.mvh_rollo from  1 for 7)");
        }
    }
}

?>
