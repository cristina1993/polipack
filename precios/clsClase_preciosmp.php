<?php

include_once 'Conn.php';

class Clase_preciosmp {

    var $con;

    function Clase_preciosmp() {
        $this->con = new Conn();
    }

    function lista_precios($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where ids=$id order by mp_b");
        }
    }

     function lista_buscador_precios($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where $txt order by mp_b asc");
        }
    }

    
    function upd_precios($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_mp SET mp_c=$data[0],mp_d='$data[1]',mp_e=$data[2], mp_f=$data[3] WHERE id=$id");
        }
    }

    function upd_precios_todos($desc,$id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_mp SET mp_e='$desc' where ids=$id");
        }
    }

    function upd_precios2($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_mp SET mp_c = mp_d where id=$id");
        }
    }

    function update_descuentos($data, $id, $des) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_descuentos SET pre_id=$id, des_fec_inicio='$data[0]',des_fec_fin='$data[1]',des_valor='$data[2]',cod_punto_emision='$data[3]' where des_id=$des");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
