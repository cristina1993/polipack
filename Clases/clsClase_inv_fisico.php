<?php

include_once 'Conn.php';

class Clase_inv_fisico {

    var $con;

    function Clase_inv_fisico() {
        $this->con = new Conn();
    }

    function lista_bodegas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor");
        }
    }

    function lista_productos() {
        if ($this->con->Conectar() == true) {
            return pg_query("(SELECT '1' as tbl,id as id,pro_ac as lote,pro_a as codigo,pro_b as descripcion FROM  erp_productos where pro_estado=0
                              union
                              SELECT '0' as tbl, pro_id as id, '' as lote ,pro_codigo as codigo,pro_descripcion as descripcion FROM  erp_i_productos where pro_estado=0) order by descripcion");
        }
    }

    function lista_secuencial_inventario($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_inv_fisico where inv_bodega=$doc ORDER BY inv_num_documento DESC LIMIT 1");
        }
    }

    function lista_productos_id($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("(SELECT '1' as tbl,id as id,pro_ac as lote,pro_a as codigo,pro_b as descripcion FROM  erp_productos where pro_a='$code' and pro_estado=0
                              union
                              SELECT '0' as tbl, pro_id as id, '' as lote ,pro_codigo as codigo,pro_descripcion as descripcion FROM  erp_i_productos where pro_codigo='$code' and pro_estado=0) order by descripcion");
        }
    }

    function insert_inv_fisico($data,$inv) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_inv_fisico(
                pro_id,
                inv_num_documento,
                inv_bodega,
                inv_fec_emison,
                inv_cantidad,
                inv_pro_codigo,
                inv_auditor,
                pro_tbl,
                inv_cant_inventario
            )
    VALUES ($data[0],'$data[1]',$data[2],'$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$inv')");
        }
    }

    function lista_buscador_inv_fisico($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_inv_fisico $txt ORDER BY inv_id DESC");
        }
    }

    function lista_un_producto_noperti_cod_lote($code, $lote) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code' and pro_ac='$lote' and pro_estado=0");
        }
    }

    function lista_un_producto_noperti_id($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where id= $code and pro_estado=0");
        }
    }

    function lista_un_producto_industrial_id($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$code and pro_estado=0");
        }
    }

    function lista_suma_cantidad($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(inv_cantidad) as suma FROM erp_inv_fisico WHERE inv_num_documento='$code'");
        }
    }

    function total_ingreso_egreso_fac($id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egreso");
        }
    }
    
     function lista_inv_fisico_doc($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_inv_fisico where inv_num_documento='$doc'");
        }
    }

////////////////////////////////////////////////////////////////////////////////////////    
}

?>
