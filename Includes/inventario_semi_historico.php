<?php

set_time_limit(0);

class Objeto {

    function con() {
//        return pg_connect('host=localhost port=5432 dbname=polipack user=postgres password=1234');
        return pg_connect('host=localhost port=5433 dbname=polipack user=postgres password=SuremandaS495');
    }

    function lista_buscar_inventario_rollos() {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM erp_inv_semielaborado_actual");
        }
    }

    function insert_transferencia($data) {
        if ($this->con() == true) {
            return pg_query("INSERT INTO erp_inv_semielaborado_historico(
                pro_id,
                mvh_cantidad,
                mvh_peso,
                mvh_estado,
                mvh_rollo,
                mvh_fecha
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$data[4]',
            '$data[5]')");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

$Set = new Objeto();
$sms = 0;
$fec = date('Y-m-d');
$fecha = date('Y-m-d', strtotime($fec . "-1 days"));
//conformes
$cns = $Set->lista_buscar_inventario_rollos();
$n = 0;
while ($rst = pg_fetch_array($cns)) {
    $data = array(
        $rst[pro_id],
        $rst[mva_cantidad],
        round($rst[mva_peso], 2),
        $rst[mva_estado],
        $rst[mva_rollo],
        $fecha);
    if (!$Set->insert_transferencia($data)) {
        $sms = 'insert_conforme' . pg_last_error();
    }
}
?>
