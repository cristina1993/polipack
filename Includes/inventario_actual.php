<?php

set_time_limit(0);

class Objeto {

    function con() {
//        return pg_connect('host=localhost port=5432 dbname=polipack user=postgres password=1234');
        return pg_connect('host=localhost port=5433 dbname=polipack user=postgres password=SuremandaS495');
    }

    function lista_buscar_inventario_rollos() {
        if ($this->con() == true) {
            return pg_query("SELECT m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,m.mov_pago FROM erp_mov_inv_semielaborado m, erp_i_productos p where m.pro_id=p.pro_id  and m.pro_id=p.pro_id group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,m.mov_pago ORDER BY p.pro_codigo, m.mov_pago");
        }
    }

    function delete_inventario() {
        if ($this->con() == true) {
            return pg_query("delete from erp_inv_semielaborado_actual");
        }
    }

    function insert_transferencia($data) {
        if ($this->con() == true) {
            return pg_query("INSERT INTO erp_inv_semielaborado_actual(
                pro_id,
                mva_cantidad,
                mva_peso,
                mva_estado,
                mva_rollo
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$data[4]')");
        }
    }

     function total_inventario_rollos($id,$lt) {
         if ($this->con() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='0') as ingreso_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='0') as egreso_con,
                                   (case when((SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='0')-
                                    (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='0'))<0 then 0 else 1 end) as cnt_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='3') as ingreso_inc,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='3') as egreso_inc,
                                   (case when((SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='3' )-
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='3'))<0 then 0 else 1 end) as cnt_inc
    
                                ");
        }
    }
///////////////////////////////////////////////////////////////////////////////////////         
}

$Set = new Objeto();
$sms = 0;
if (!$Set->delete_inventario()) {
    $sms = 'delete' . pg_last_error();
}
//conformes
$txt = "and m.mov_flete='0'";
$cns = $Set->lista_buscar_inventario_rollos($txt);
$n = 0;
while ($rst = pg_fetch_array($cns)) {
    $rst_inv = pg_fetch_array($Set->total_inventario_rollos($rst[pro_id], $rst[mov_pago]));
    $inv_con = $rst_inv[ingreso_con] - $rst_inv[egreso_con];
    $cnt_con = $rst_inv[cnt_con];
    if ($cnt_con != 0 && $inv_con != 0) {
        $data = array(
            $rst[pro_id],
            $cnt_con,
            round($inv_con,2),
            '0',
            $rst[mov_pago]);
        if (!$Set->insert_transferencia($data)) {
            $sms = 'insert_conforme' . pg_last_error();
        }
    }
}
///inconformes
$txt1 = "and m.mov_flete='3'";
$cns1 = $Set->lista_buscar_inventario_rollos($txt1);
$n = 0;
while ($rst = pg_fetch_array($cns1)) {
    $rst_inv = pg_fetch_array($Set->total_inventario_rollos($rst[pro_id], $rst[mov_pago]));
    $inv_inc = $rst_inv[ingreso_inc] - $rst_inv[egreso_inc];
    $cnt_inc = $rst_inv[cnt_inc];
    if ($cnt_inc != 0 && $inv_inc != 0) {
        $data1 = array(
            $rst[pro_id],
            $cnt_inc,
            round($inv_inc,2),
            '3',
            $rst[mov_pago]);
        if (!$Set->insert_transferencia($data1)) {
            $sms = 'insert_inconforme' . pg_last_error();
        }
    }
}
?>
