<?php

set_time_limit(0);

class Objeto {

    function con() {
//        return pg_connect('host=localhost port=5432 dbname=polipack user=postgres password=1234');
        return pg_connect('host=localhost port=5433 dbname=polipack user=postgres password=SuremandaS495');
    }

//    function lista_rollos() {
//        if ($this->con() == true) {
//            return pg_query("select * from inventario_rollos where bod=1 
//                            and substring(mov_pago from 1 for 7)!='EC00044'
//                            and substring(mov_pago from 1 for 7)!='EC00037'
//                            and substring(mov_pago from 1 for 7)!='EC00062'
//                            and substring(mov_pago from 1 for 7)!='EC00040'
//                            and substring(mov_pago from 1 for 7)!='EC00041'
//                            and substring(mov_pago from 1 for 7)!='EC00063'
//                            and substring(mov_pago from 1 for 7)!='EC00064'
//                            and substring(mov_pago from 1 for 7)<'EC00064'
//                            and inv>0");
//        }
//    }
    
    function lista_rollos() {
        if ($this->con() == true) {
            return pg_query("select * from inventario_rollos where mov_pago='EC000311020' and bod=1 and inv>0
                            union
                            select * from inventario_rollos where mov_pago='EC000441079' and bod=1 and inv>0
                            union
                            select * from inventario_rollos where mov_pago<'EC000371325' and mov_pago>='EC000371001' and bod=1 and inv>0
                            union
                            select * from inventario_rollos where substring(mov_pago from 1 for 7)='EC00040' and bod=1 and inv>0
                            union
                            select * from inventario_rollos where mov_pago<'EC000631093' and mov_pago>='EC000631028' and bod=1 and inv>0
                            order by mov_pago 
                            ");
        }
    }

    function lista_una_orden($id) {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion where replace(ord_num_orden,'-','')='$id'");
        }
    }

    function lista_un_movimiento($id) {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM erp_mov_inv_semielaborado where mov_pago='$id' order by mov_fecha_trans desc limit 1");
        }
    }

    function lista_secuencial_transferencia() {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM  erp_secuencial_semielaborado   ORDER BY sec_id DESC LIMIT 1");
        }
    }

    function insert_sec_transferencia($data) {
        if ($this->con() == true) {
            return pg_query("INSERT INTO erp_secuencial_semielaborado(sec_transferencias) VALUES ('$data')");
        }
    }

    function insert_transferencia($data) {
        if ($this->con() == true) {
            $f = date('Y-m-d');
            $h = date('H:i');
            $usu = 'SUPER ADMIN';
            return pg_query("INSERT INTO erp_mov_inv_semielaborado(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_cantidad,                
                mov_tabla,                
                mov_fecha_registro,
                mov_hora_registro,
                mov_usuario,
                mov_pago,
                mov_flete
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$data[4]',   
            '$data[5]',
            '$data[6]',   
            '$data[7]',
            '$data[8]',
            '$f',
            '$h',
            '$usu',
            '$data[9]',
            '$data[10]')");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

$Set = new Objeto();
$sms = 0;
//Bodega MP
$cns1 = $Set->lista_rollos();
$n = 0;
while ($rst1 = pg_fetch_array($cns1)) {
    $rst_ord = pg_fetch_array($Set->lista_una_orden(substr($rst1[mov_pago], 0, 7)));
    $rst_mov = pg_fetch_array($Set->lista_un_movimiento($rst1[mov_pago]));
    if ($rst_mov[mov_fecha_trans] < '2017-05-04') {
        $rst = pg_fetch_array($Set->lista_secuencial_transferencia());
        $sec = (substr($rst[sec_transferencias], -5) + 1);
        if ($sec >= 0 && $sec < 10) {
            $txt = '000000000';
        } else if ($sec >= 10 && $sec < 100) {
            $txt = '00000000';
        } else if ($sec >= 100 && $sec < 1000) {
            $txt = '0000000';
        } else if ($sec >= 1000 && $sec < 10000) {
            $txt = '000000';
        } else if ($sec >= 10000 && $sec < 100000) {
            $txt = '00000';
        } else if ($sec >= 100000 && $sec < 1000000) {
            $txt = '0000';
        } else if ($sec >= 1000000 && $sec < 10000000) {
            $txt = '000';
        } else if ($sec >= 10000000 && $sec < 100000000) {
            $txt = '00';
        } else if ($sec >= 100000000 && $sec < 1000000000) {
            $txt = '0';
        } else if ($sec >= 1000000000 && $sec < 10000000000) {
            $txt = '';
        }
        $retorno = '001-' . $txt . $sec;
        if ($Set->insert_sec_transferencia($retorno) == FALSE) {
            $sms = 'insert_sec_elaborado' . pg_last_error();
        }
        $dt_mov = Array(
            $rst1[pro_id], //pro_id,
            '21', ///trs_id,
            $rst_ord[cli_id], //cli_id,
            '1', //bod_id,
            $retorno, //mov_documento,
            $rst_ord[ord_num_orden], // mov_guia_transporte,
            date('Y-m-d'), //mov_fecha_trans,
            $rst1[inv], //mov_cantidad,
            '1', //mov_tabla,
            $rst1[mov_pago], //mov_pago
            $rst_mov[mov_flete]//mov_flete(estado)
        );
        if (!$Set->insert_transferencia($dt_mov)) {
            $sms = 'insert_mov_elaborado' . pg_last_error();
        }
    }
}
?>
