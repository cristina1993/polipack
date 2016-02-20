<?php

include_once 'Conn.php';

class Clase_prod_pedido_venta {

    var $con;

    function Clase_prod_pedido_venta() {
        $this->con = new Conn();
    }

    function lista_buscador_orden($ord, $cli, $ruc, $estado, $ven, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta p, erp_det_ped_venta d  where p.ped_id=d.ped_id and tipo_cliente=0 and (ped_estado='1' or ped_estado='4') and (ped_local='1' or ped_local='10') $ord $cli $ruc $estado $ven  ORDER BY ped_num_registro, det_estado");
        }
    }

    function total_ingreso_egreso_fac($id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egreso");
        }
    }

    function lista_productos_industrial_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos p, erp_empresa e where p.emp_id=e.emp_id and p.pro_id=$id");
        }
    }

    function lista_productos_noperti_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos p where id=$id");
        }
    }

    function lista_cambia_status($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta SET ped_estado='$sts' where ped_id=$id");
        }
    }

    function lista_cambia_status_det($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_det_ped_venta SET det_estado='$sts' where det_id=$id");
        }
    }

    function lista_ordenes($tab, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM $tab where det_id=$id");
        }
    }

    function lista_un_detalle($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_ped_venta where det_id=$id");
        }
    }

    function lista_detalle($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_ped_venta where ped_id=$id and det_estado=1");
        }
    }


}

?>
