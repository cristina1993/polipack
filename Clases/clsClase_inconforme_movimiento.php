<?php

include_once 'Conn.php';

class Clase_inconforme_movimiento {

    var $con;

    function Clase_inconforme_movimiento() {
        $this->con = new Conn();
    }

    function lista_buscador_industrial_ingresopt($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_mov_inv_semielaborado m,erp_transacciones t, erp_i_cliente c, erp_i_productos p where m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.pro_id=p.pro_id$txt order by m.mov_documento desc");
        }
    }

    function lista_combo_transacciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_transacciones order by trs_descripcion");
        }
    }

    function lista_clientes() {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres  
                        from  erp_i_cliente order by nombres");
        }
    }

    function lista_productos_total() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos  order by pro_codigo");
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

    function lista_un_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$id");
        }
    }

    function lista_un_proveedor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cli_id, trim(cli_raz_social) as nombres FROM  erp_i_cliente where cli_id=$id");
        }
    }

    function total_ingreso_egreso_fac($id, $lt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.mov_pago='$lt') as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_mov_inv_semielaborado m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.mov_pago='$lt') as egreso");
        }
    }
    
    function lista_ordenes() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ord_num_orden, '0' as tip FROM  erp_i_orden_produccion 
                            union
                            SELECT opp_codigo as ord_num_orden,'1' as tip FROM  erp_i_orden_produccion_padding
                            order by tip, ord_num_orden");
        }
    }

}

?>
