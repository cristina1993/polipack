<?php

set_time_limit(0);

class Objeto {

    function con() {
//        return pg_connect('host=localhost port=5432 dbname=polipack user=postgres password=1234');
        return pg_connect('host=localhost port=5432 dbname=polipack_enero user=postgres password=SuremandaS495');
    }

    function lista_terminado_corte() {
        if ($this->con() == true) {
            return pg_query("select o.opp_codigo,o.pro_id,d.det_id, ((select sum(rpa_peso) from erp_reg_op_padding r where r.opp_id=o.opp_id)*100/o.pro_mf3) 
                            from erp_det_ped_venta d,erp_i_orden_produccion_padding o 
                            where d.ped_id=o.ped_id and d.pro_id=o.pro_id and
                            ((select sum(rpa_peso) from erp_reg_op_padding r where r.opp_id=o.opp_id)*100/o.pro_mf3)>90 and 
                            ((select sum(rpa_peso) from erp_reg_op_padding r where r.opp_id=o.opp_id)*100/o.pro_mf3)!='NaN'
                            order by det_id");
        }
    }

    function lista_produccion_corte() {
        if ($this->con() == true) {
            return pg_query("select o.opp_codigo,o.pro_id,d.det_id, ((select sum(rpa_peso) from erp_reg_op_padding r where r.opp_id=o.opp_id)*100/o.pro_mf3) 
                            from erp_det_ped_venta d,erp_i_orden_produccion_padding o 
                            where d.ped_id=o.ped_id and d.pro_id=o.pro_id and
                            ((select sum(rpa_peso) from erp_reg_op_padding r where r.opp_id=o.opp_id)*100/o.pro_mf3)<90
                            order by det_id");
        }
    }

    function lista_espera_corte() {
        if ($this->con() == true) {
            return pg_query("select o.opp_codigo,o.pro_id,d.det_id
                            from erp_det_ped_venta d,erp_i_orden_produccion_padding o 
                            where d.ped_id=o.ped_id and d.pro_id=o.pro_id and
                            not exists((select * from erp_reg_op_padding r where r.opp_id=o.opp_id))
                            order by det_id");
        }
    }

    function lista_aprobados($id) {
        if ($this->con() == true) {
            return pg_query("select d.det_id
                                        from erp_det_ped_venta d where det_estado!=5 and det_estado!=4 and not exists(select * from erp_i_orden_produccion_padding o 
                                        where d.ped_id=o.ped_id and d.pro_id=o.pro_id) 
                                        and 
                                        not exists(select * from erp_i_orden_produccion o 
                                        where d.ped_id=o.ped_id and d.pro_id=o.pro_id) 
                                        order by det_id");
        }
    }

    function lista_anulados_enviados($id) {
        if ($this->con() == true) {
            return pg_query("select d.det_id
                                from erp_det_ped_venta d where det_estado=5 or det_estado=4 
                                order by det_id");
        }
    }

    function lista_pedidos() {
        if ($this->con() == true) {
            return pg_query("select * from erp_reg_pedido_venta");
        }
    }

    function lista_un_detalle($id) {
        if ($this->con() == true) {
            return pg_query("select * from erp_det_ped_venta d where ped_id=$id");
        }
    }

    function update_estado_det($id, $sts) {
        if ($this->con() == true) {
            return pg_query("UPDATE erp_det_ped_venta set det_estado='$sts' where det_id=$id");
        }
    }

    function updta_estado_pedido($id, $sts) {
        if ($this->con() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta set ped_estado='$sts' where ped_id=$id");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

$Set = new Objeto();
$sms = 0;
//Bodega MP
$cns1 = $Set->lista_terminado_corte();
$cns2 = $Set->lista_produccion_corte();
$cns3 = $Set->lista_espera_corte();
$cns4 = $Set->lista_aprobados();
$cns5 = $Set->lista_anulados_enviados();
$cns6 = $Set->lista_pedidos();
$n = 0;
echo 'terminado<br>';
while ($rst1 = pg_fetch_array($cns1)) {
    $n++;
    echo $n . ' -- ' . $rst1[opp_codigo] . ' --- ' . $rst1[det_id] . '<br>';
    if ($Set->update_estado_det($rst1[det_id], '11') == false) {
        echo pg_last_error();
    }
}
echo 'produccion<br>';
while ($rst2 = pg_fetch_array($cns2)) {
    $n++;
    echo $n . ' -- ' . $rst2[opp_codigo] . ' --- ' . $rst2[det_id] . '<br>';
    if ($Set->update_estado_det($rst2[det_id], '10') == false) {
        echo pg_last_error();
    }
}
echo 'espera<br>';
while ($rst3 = pg_fetch_array($cns3)) {
    $n++;
    echo $n . ' -- ' . $rst3[opp_codigo] . ' --- ' . $rst3[det_id] . '<br>';
    if ($Set->update_estado_det($rst3[det_id], '9') == false) {
        echo pg_last_error();
    }
}
echo 'aprobados<br>';
while ($rst4 = pg_fetch_array($cns4)) {
    $n++;
    echo $n . ' -- ' . $rst4[det_id] . '<br>';
//    if($Set->update_estado_det($rst1[det_id], '1')==false){
//      echo pg_last_error();  
//    }
}
echo 'enviados y anulados<br>';
while ($rst5 = pg_fetch_array($cns5)) {
    $n++;
    echo $n . ' -- ' . $rst5[det_id] . '<br>';
}

while ($rst6 = pg_fetch_array($cns6)) {

    $rst7 = pg_fetch_array($Set->lista_un_detalle($rst6[ped_id]));
    echo $rst6[ped_num_registro] . '---' . $rst7[det_estado] . '<br>';
    if(!empty($rst7[det_estado])) {
        if ($Set->updta_estado_pedido($rst6[ped_id], $rst7[det_estado]) == false) {
            echo pg_last_error();
        }
    }
}
?>
