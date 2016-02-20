<?php

include_once 'Conn.php';

class Clase_movimiento_nopertipt {

    var $con;

    function Clase_movimiento_nopertipt() {
        $this->con = new Conn();
    }

    function lista_movimiento_noperti() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id ORDER BY mov_documento desc");
        }
    }

    function lista_secuencial_movimiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt ORDER BY mov_id DESC LIMIT 1");
        }
    }

    function lista_un_movimiento_noperti($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_id=$id");
        }
    }

    function lista_movimiento_noperti_documento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_documento='$id'");
        }
    }

    function lista_buscador_movimiento_noperti($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_mov_inv_pt m, erp_i_productos p,erp_transacciones t, erp_i_cliente c where p.pro_id= m.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id $txt order by m.mov_documento desc");
        }
    }

    function insert_movimiento_noperti($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_fecha_registro,
                mov_hora_registro,
                mov_cantidad,
                mov_tranportista
            )
    VALUES ($data[0],$data[1],$data[2],'1','$data[3]','$data[4]','$data[5]','" . date('Y-m-d') . "','" . date("H:i:s") . "','$data[6]','')");
        }
    }

    function upd_movimiento_noperti($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_mov_inv_pt SET 
                mov_fecha_entrega='$data[1]', 
                mov_num_factura='$data[2]', 
                mov_pago='$data[3]', 
                mov_direccion='$data[4]', 
                mov_val_unit='$data[5]', 
                mov_descuento='$data[6]', 
                mov_iva=$data[7], 
                mov_flete='$data[8]' 
                WHERE mov_id=$data[0]");
        }
    }

    function delete_movimiento_noperti($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_mov_inv_pt WHERE mov_documento='$id'");
        }
    }

    function lista_movimiento_empresa() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa ORDER BY emp_descripcion");
        }
    }

    function lista_movimiento_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos p, erp_empresa e where p.emp_id=e.emp_id and e.emp_id=$id ORDER BY p.pro_descripcion");
        }
    }

    function lista_un_producto_movimiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_descripcion='$id'");
        }
    }

    function lista_cliente_movimiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_tipo <>'0' ORDER BY cli_raz_social");
        }
    }

    function lista_un_cliente_movimiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_codigo='$id'");
        }
    }

    function lista_ultimo_movimiento_noperti() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c, erp_empresa e where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and e.emp_id=p.emp_id ORDER BY mov_id desc LIMIT 1");
        }
    }

    function lista_transaccion_movimiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_transacciones");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
