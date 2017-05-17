<?php

include_once 'Conn.php';

class Clase_reg_reproceso {

    var $con;

    function Clase_reg_reproceso() {
        $this->con = new Conn();
    }

    function lista_buscador_reproceso($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_reproceso r,erp_i_productos p where r.pro_id=p.pro_id $txt order by r.rrp_codigo desc");
        }
    }

    function lista_secuencial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_reg_reproceso  ORDER BY rrp_codigo DESC LIMIT 1");
        }
    }

    function lista_productos_bod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  inventario_rollos i, erp_i_productos p where i.bod=$id and p.pro_id=i.pro_id  ORDER BY i.mov_pago");
        }
    }

    function lista_un_producto($id, $lt, $bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  inventario_rollos i, erp_i_productos p where i.bod=$bod and i.pro_id=$id and i.mov_pago='$lt' and p.pro_id=i.pro_id");
        }
    }

    function lista_un_producto_lote($lt, $bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  inventario_rollos i, erp_i_productos p where i.bod=$bod and i.mov_pago='$lt' and p.pro_id=i.pro_id");
        }
    }

    function insert_reproceso($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_reg_reproceso(
                pro_id,
                rrp_bodega,
                rrp_codigo,
                rrp_orden,
                rrp_fecha,
                rrp_cantidad,                
                rpr_lote,
                rrp_tipo
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[4]',   
            '$data[5]',
            '$data[6]',   
            '$data[7]',
            '$data[9]',   
            '$data[10]'
                    )");
        }
    }

    function lista_un_registro($id, $lt, $bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  inventario_rollos i, erp_i_productos p where i.bod=$bod and i.pro_id=$id and i.mov_pago='$lt' and p.pro_id=i.pro_id");
        }
    }

    function lista_secuencial_transferencia() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_secuencial_semielaborado   ORDER BY sec_id DESC LIMIT 1");
        }
    }

    function insert_sec_transferencia($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_secuencial_semielaborado(sec_transferencias) VALUES ('$data')");
        }
    }

    function insert_transferencia($data) {
        if ($this->con->Conectar() == true) {
            $f = date('Y-m-d');
            $h = date('H:i');
            $usu = strtoupper($_SESSION[User]);
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
                mov_pago
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
            '$data[9]'
                    )");
        }
    }

    function lista_secuencial_transferencia_terminado() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_secuencial   ORDER BY sec_id DESC LIMIT 1");
        }
    }

    function insert_sec_transferencia_terminado($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_secuencial(sec_transferencias) VALUES ('$data')");
        }
    }

    function insert_transferencia_terminado($data) {
        if ($this->con->Conectar() == true) {
            $f = date('Y-m-d');
            $h = date('H:i');
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
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
                mov_pago
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
            '$data[9]'
                    )");
        }
    }

    function insert_inv_mp($data) {
        if ($this->con->Conectar() == true) {
            $fecha = date('Y-m-d');
            $hora = date('H:i');
            return pg_query("INSERT INTO erp_i_mov_inventario (
                                usu_id,
                                mov_fecha_registro,
                                mov_hora_registro,
                                mov_ubicacion,                                
                                mov_procedencia_destino,                                
                                trs_id,
                                mp_id,
                                mov_documento,
                                mov_num_trans,                                
                                mov_fecha_trans,
                                mov_cantidad,
                                mov_presentacion,
                                mov_peso_total,
                                mov_proveedor,
                                mov_peso_unit,
                                mov_tranportista,
                                mov_guia_remision,
                                mov_num_orden
                            )VALUES(
                            $_SESSION[usuid],
                            '$fecha',
                            '$hora',
                            '',
                            '',    
                            $data[0],
                            $data[1],
                           '$data[2]',    
                           '$data[3]',
                           '$data[4]',
                            $data[5],
                           '$data[6]',
                            $data[7],
                           '$data[8]',
                            $data[9],
                           '$data[10]',
                           '$data[11]',
                           '" . strtoupper($data[12]) . "'  )");
        }
    }

    function lista_secuencia_transaccion($trs) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_mov_inventario mi,
                                            erp_transacciones tr
                                            where mi.trs_id=tr.trs_id
                                            and tr.trs_operacion=$trs
                                            order by mi.mov_num_trans desc
                                            limit 1 ");
        }
    }

    function lista_combo_transacciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_transacciones order by trs_descripcion");
        }
    }

    function lista_ordenes() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ord_num_orden, '0' as tip FROM  erp_i_orden_produccion 
                            order by tip, ord_num_orden");
        }
    }

    function lista_mp() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mp where mpt_id=100 or mpt_id=101  order by mp_codigo, mp_referencia");
        }
    }
    
     function lista_ultimo_registro() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ord_num_orden as orden FROM  erp_reg_op_ecocambrella r, erp_i_orden_produccion p where r.ord_id=p.ord_id  order by rec_id desc limit 1");
        }
    }
}

?>
