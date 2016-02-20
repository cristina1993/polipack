<?php

include_once 'Conn.php';

class Clase_vendedores {

    var $con;

    function Clase_vendedores() {
        $this->con = new Conn();
    }

    function lista_vendedores() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_vendedores order by vnd_id");
        }
    }

    function lista_buscardor_vendedores($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_vendedores $txt");
        }
    }

    function lista_un_vendedor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_vendedores where vnd_id='$id'");
        }
    }

    function lista_un_vend($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_users where usu_id=$num");
        }
    }

    function lista_locales() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor ORDER BY identificacion ");
        }
    }

    function insert_vendedores($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_vendedores(
                        vnd_codigo,
                        vnd_nombre,
                        vnd_local)
            VALUES ('$data[0]','$data[1]','$data[2]')");
        }
    }

    function upd_vendedores($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_vendedores SET vnd_codigo='$data[0]',vnd_nombre='$data[1]',vnd_local='$data[2]' WHERE vnd_id='$id'");
        }
    }

    function delete_vendedores($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_vendedores WHERE vnd_id = '$id'"
            );
        }
    }

    function lista_usuarios() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_users where usu_id<>1 ORDER BY usu_person");
        }
    }

      function lista_usuario($usu) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_users where usu_id=$usu");
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
