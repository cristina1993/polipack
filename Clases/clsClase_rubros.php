<?php

include_once 'Conn.php';

class Clase_rubros{

    var $con;

    function Clase_rubros() {
        $this->con = new Conn();
    }

    function lista_rubros() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_rubros order by rub_descripcion");
        }
    }

    function lista_buscardor_rubros($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_rubros $txt");
        }
    }

    function lista_un_rubro($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_rubros where rub_id='$id'");
        }
    }

    function insert_rubro($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_rubros(
                        rub_descripcion)
            VALUES ('$data[0]')");
        }
    }

    function upd_rubro($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_rubros SET rub_descripcion='$data[0]'WHERE rub_id='$id'");
        }
    }

    function delete_rubro($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_rubros WHERE rub_id = '$id'"
            );
        }
    }
}

?>
