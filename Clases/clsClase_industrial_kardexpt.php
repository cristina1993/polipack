<?php

include_once 'Conn.php';

class Clase_industrial_kardexpt {

    var $con;

    function Clase_industrial_kardexpt() {
        $this->con = new Conn();
    }

    function lista_kardex($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT  * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and c.cli_id=m.cli_id and m.bod_id=$bod order by m.pro_id,m.mov_tabla,m.mov_fecha_trans desc");
        }
    }

    function lista_kardex_noperti($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("
                SELECT m.*,p.*,t.*,c.*,ps.pro_tipo FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t,erp_i_cliente c, erp_productos_set ps  where  m.pro_id=p.id and c.cli_id=m.cli_id and ps.ids=p.ids and m.trs_id=t.trs_id and m.bod_id=$bod ORDER BY mov_documento desc                
                
                    
                    ");
        }
    }

    function lista_suma_ingreso($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t WHERE m.pro_id=p.pro_id and m.trs_id=t.trs_id and p.pro_codigo='$cod' and t.trs_operacion= 0");
        }
    }

    function lista_suma_egreso($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t WHERE m.pro_id=p.pro_id and m.trs_id=t.trs_id and p.pro_codigo='$cod' and t.trs_operacion= 1");
        }
    }

    function lista_buscar_kardexpt($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT  * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c, erp_i_productos p where p.pro_id=m.pro_id and m.trs_id=t.trs_id and c.cli_id=m.cli_id $txt order by p.pro_codigo,m.mov_fecha_trans,m.mov_pago,m.mov_tabla,m.mov_documento asc");
        }
    }

    function lista_prod_comerciales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.*,ps.pro_tipo FROM  erp_productos p,erp_productos_set ps  where ps.ids=p.ids and id=$id");
        }
    }

    function lista_prod_industriales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$id");
        }
    }
    
    function lista_buscar_industriales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$id' or pro_descripcion='$id'");
        }
    }

    function lista_buscar_comerciales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.*,ps.pro_tipo FROM  erp_productos p,erp_productos_set ps where ps.ids=p.ids and (p.pro_a='$id' or p.pro_b='$id')");
        }
    }

    function buscar_un_movimiento($id, $tab, $emi,$fec1,$fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and c.cli_id=m.cli_id and m.pro_id='$id' and m.mov_tabla='$tab' and m.bod_id=$emi and m.mov_fecha_trans between '$fec1' and '$fec2' ORDER BY m.pro_id,m.mov_tabla,m.mov_fecha_trans asc");
        }
    }
    
    function lista_buscador_industrial_kardex($bod, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inv_pt m,erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.bod_id=$bod $txt ORDER BY m.pro_id,m.mov_tabla,m.mov_fecha_trans,m.mov_documento asc");
        }
    }
    
      function total_ingreso_egreso($id,$fec1,$lt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0  and mov_fecha_trans<'$fec1') as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and mov_fecha_trans<'$fec1') as egreso");
        }
    }
    
     function lista_combo_transacciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_transacciones order by trs_descripcion");
        }
    }
    
     function lista_buscar_productos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT '1' as tbl,id as id,pro_ac as lote,pro_a as codigo,pro_b as descripcion FROM  erp_productos where pro_estado=0 and (pro_a like '%$id%' or pro_b like '%$id%')
                              union
SELECT '0' as tbl, pro_id as id, '' as lote ,pro_codigo as codigo,pro_descripcion as descripcion FROM  erp_i_productos where pro_estado=0 and (pro_codigo like '%$id%' or pro_descripcion like '%$id%')");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
