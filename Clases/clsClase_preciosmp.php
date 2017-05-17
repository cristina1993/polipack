<?php

include_once 'Conn.php';

class Clase_preciosmp {

    var $con;

    function Clase_preciosmp() {
        $this->con = new Conn();
    }

    function lista_un_mp_mod1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_tpmp order by mpt_nombre");
        }
    }

    function buscar_un_costo($p, $f) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_costos_mp where cmp_fecha='$f' and mp_id=$p");
        }
    }

     function lista_buscador_todo($txt1,$txt2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT 0 as tbl,p.mp_id as id,p.mpt_id as tp,p.mp_codigo as codigo,p.mp_referencia as descripcion,p.mp_unidad as unidad FROM  erp_i_mp p $txt1
                                union
                                SELECT 1 as tbl,p.pro_id as id,p.pro_tipo as tp,p.pro_codigo as codigo,p.pro_descripcion as descripcion,p.pro_uni as unidad FROM  erp_i_productos p $txt2
                                order by tbl,codigo");
                                        }
    }
    
    function lista_buscador_mp($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT 0 as tbl,p.mp_id as id,p.mpt_id as tp,p.mp_codigo as codigo,p.mp_referencia as descripcion,p.mp_unidad as unidad FROM  erp_i_mp p $txt order by mp_codigo");
        }
    }

    function lista_buscador_productos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT 1 as tbl,p.pro_id as id,p.pro_tipo as tp,p.pro_codigo as codigo,p.pro_descripcion as descripcion,p.pro_uni as unidad FROM  erp_i_productos p  $txt order by pro_codigo");
        }
    }

    function lista_costos($id, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_costos_mp where mp_id=$id and cmp_fecha between '$d' and '$h' order by cmp_fecha desc limit 1");
        }
    }

    function upd_costos($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_costos_mp SET cmp_valor='$data[0]' WHERE mp_id=$id and cmp_fecha='$data[1]'and cmp_tabla='$data[2]'");
        }
    }

    function insert_costos($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_costos_mp (cmp_valor,cmp_fecha,mp_id,cmp_tabla)values('$data[0]','$data[1]',$id,'$data[2]')");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
