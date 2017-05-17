<?php

set_time_limit(0);

class Objeto {

    function con() {
        return pg_connect('host=localhost port=5432 dbname=polipack user=postgres password=1234');
//        return pg_connect('host=localhost port=5433 dbname=polipack user=postgres password=SuremandaS495');
    }

    function lista_inventario_fecha() {
        if ($this->con() == true) {
            return pg_query("SELECT m.mov_fecha_trans FROM erp_mov_inv_semielaborado m group by m.mov_fecha_trans ORDER BY m.mov_fecha_trans");
        }
    }

    function lista_buscar_inventario_rollos($txt) {
        if ($this->con() == true) {
            return pg_query("SELECT m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,m.mov_pago FROM erp_mov_inv_semielaborado m, erp_i_productos p where m.pro_id=p.pro_id  and m.pro_id=p.pro_id $txt group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,m.mov_pago ORDER BY p.pro_codigo, m.mov_pago");
        }
    }

    function delete_inventario() {
        if ($this->con() == true) {
            return pg_query("delete from erp_inv_semielaborado_historico");
        }
    }

    function insert_transferencia($data) {
        if ($this->con() == true) {
            return pg_query("INSERT INTO erp_inv_semielaborado_historico(
                pro_id,
                mvh_fecha,
                mvh_cantidad,
                mvh_peso,
                mvh_estado,
                mvh_rollo
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$data[4]',
            '$data[5]')");
        }
    }

    function total_inventario_rollos($id, $lt,$txt) {
        if ($this->con() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='0' $txt) as ingreso_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='0' $txt) as egreso_con,
                                   (case when((SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='0' $txt)-
                                    (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='0' $txt))<0 then 0 else 1 end) as cnt_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='3' $txt) as ingreso_inc,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='3' $txt) as egreso_inc,
                                   (case when((SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt' and mov_flete='3' $txt)-
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 1 and m.mov_pago='$lt' and mov_flete='3' $txt))<0 then 0 else 1 end) as cnt_inc
    
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
$cnsf = $Set->lista_inventario_fecha();
while ($rstf = pg_fetch_array($cnsf)) {
//conformes
    $fec=" and m.mov_fecha_trans between '1900-01-01' and '$rstf[mov_fecha_trans]'";
    $txt = "and m.mov_flete='0' $fec";
    $cns = $Set->lista_buscar_inventario_rollos($txt);
    $n = 0;
    while ($rst = pg_fetch_array($cns)) {
        $rst_inv = pg_fetch_array($Set->total_inventario_rollos($rst[pro_id], $rst[mov_pago],$fec));
        $inv_con = $rst_inv[ingreso_con] - $rst_inv[egreso_con];
        $cnt_con = $rst_inv[cnt_con];
        if ($cnt_con != 0 && $inv_con != 0) {
            $data = array(
                $rst[pro_id],
                $rstf[mov_fecha_trans],
                $cnt_con,
                round($inv_con, 2),
                '0',
                $rst[mov_pago]);
            if (!$Set->insert_transferencia($data)) {
                echo 'insert_conforme' . pg_last_error();
            }
        }
    }
///inconformes
    $txt1 = "and m.mov_flete='3' $fec";
    $cns1 = $Set->lista_buscar_inventario_rollos($txt1);
    $n = 0;
    while ($rst = pg_fetch_array($cns1)) {
        $rst_inv = pg_fetch_array($Set->total_inventario_rollos($rst[pro_id], $rst[mov_pago],$fec));
        $inv_inc = $rst_inv[ingreso_inc] - $rst_inv[egreso_inc];
        $cnt_inc = $rst_inv[cnt_inc];
        if ($cnt_inc != 0 && $inv_inc != 0) {
            $data1 = array(
                $rst[pro_id],
                $rstf[mov_fecha_trans],
                $cnt_inc,
                round($inv_inc, 2),
                '3',
                $rst[mov_pago]);
            if (!$Set->insert_transferencia($data1)) {
                 echo 'insert_inconforme' . pg_last_error();
            }
        }
    }
}
?>
