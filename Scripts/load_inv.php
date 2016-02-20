<?php
set_time_limit(0);
include_once '../Clases/clsSetting.php';
$Set = new Set();
$tp = 4;
$cns = $Set->lista_detalle_fact($tp);
$n = 0;
$np0 = array();
$cli = array();
while ($rst = pg_fetch_array($cns)) {
    $n++;
    $nd1 = substr($rst[num_camprobante], 0, 3);
    $nd2 = substr($rst[num_camprobante], 3, 3);
    $nd3 = substr($rst[num_camprobante], -9);
    $nd = $nd1 . '-' . $nd2 . '-' . $nd3;
    $rst_fac = pg_fetch_array($Set->lista_factura_num($nd));
    $rst_pro = pg_fetch_array($Set->lista_un_producto_ind($rst[cod_producto]));
    $pro_id = $rst_pro[pro_id];
    $mov_tabla = 0;
    if (empty($rst_pro)) {
        $rst_pro = pg_fetch_array($Set->lista_un_producto_com1($rst[cod_producto], $rst[lote]));
        $pro_id = $rst_pro[id];
        $mov_tabla = 1;
    }
    if (empty($rst_pro)) {
        $rst_pro = pg_fetch_array($Set->lista_un_producto_com2($rst[cod_producto]));
        $pro_id = $rst_pro[id];
        $mov_tabla = 1;
    }
    if (empty($rst_pro)) {
        $pro_id = 0;
        array_push($np0, $rst[cod_producto]);
    }
    $rst_cli = pg_fetch_array($Set->lista_un_cliente_cedula($rst_fac[identificacion]));
    $cli_id = $rst_cli[cli_id];
    if (empty($rst_cli)) {
        $cli_id = 0;
        array_push($cli, $rst_fac[identificacion]);
    }

    $data = array(
        $pro_id,
        $trs_id = 12,
        $cli_id,
        $bod_id = $rst_fac[cod_punto_emision],
        $mov_documento = $rst[num_camprobante],
        $mov_guia_transporte = '0',
        $mov_num_trans = '0',
        $mov_fecha_trans = $rst_fac[fecha_emision],
        $mov_fecha_registro = $rst_fac[fecha_emision],
        $mov_hora_registro = '00:00',
        $mov_cantidad = $rst[cantidad],
        $mov_num_factura = $rst[num_camprobante],
        $mov_tabla
    );
    
    if ($pro_id != 0) {
        if (!$Set->lista_inser_new_inv($data)) {
            echo pg_last_error().'Linea '.$n;
            print_r($data);
            break;
        }
    }

}
//print_r($np0);