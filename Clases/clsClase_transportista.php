<?php

include_once 'Conn.php';

class Clase_transportista {

    var $con;

    function Clase_transportista() {
        $this->con = new Conn();
    }

    function lista_transportista() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM transportista order by razon_social");
        }
    }

    function lista_buscardor_transportista($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM transportista $txt");
        }
    }

    function lista_un_transportista($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM transportista where id='$id'");
        }
    }

    function insert_transportista($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO transportista(
                        identificacion,
                        razon_social,
                        email,
                        placa,
                        telefono,
                        direccion)
            VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]')");
        }
    }

    function upd_transportista($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE transportista SET identificacion='$data[0]', razon_social='$data[1]',email='$data[2]',placa='$data[3]',telefono='$data[4]',direccion='$data[5]' WHERE id='$id'");
        }
    }

    function delete_transportista($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM transportista WHERE id = '$id'"
            );
        }
    }

    function delete_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_registro_produccion WHERE reg_id = $id");
        }
    }

    function delete_registro_movimientos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_mov_inv_pt WHERE mov_num_trans='$id'");
        }
    }

    function lista_una_orden_codigo(
    $ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos p, $ord");
        }
    }

    function lista_secuencial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_registro_produccion ORDER BY reg_id DESC LIMIT 1");
        }
    }

    function lista_movimientos_documento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inv_pt WHERE mov_documento='$id'");
        }
    }

//    function lista_movimientos_numtrans($id) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM erp_i_mov_inv_pt WHERE mov_num_trans='$id'");
//        }
//    }
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
