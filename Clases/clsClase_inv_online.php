<?php

include_once 'Conn.php';

class Inv_Online {

    var $con;

    function Inv_Online() {
        $this->con = new Conn();
    }

    function lista_general($e,$limit) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from 
                             erp_inventario_online 
                             where inv_estado=$e 
                             order by inv_subgrupo_familia,inv_descripcion $limit ");
        }
    }
    function lista_general_codigo($e,$limit,$cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from 
                             erp_inventario_online 
                             where inv_estado=$e 
                             and inv_codigo_barras like '%$cod%'    
                             order by inv_subgrupo_familia,inv_descripcion $limit ");
        }
    }
    function lista_general_talla($e,$limit,$talla) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from 
                             erp_inventario_online 
                             where inv_estado=$e 
                             and inv_marca like '%$talla%'    
                             order by inv_subgrupo_familia,inv_descripcion $limit ");
        }
    }
    function lista_general_familia($e,$limit,$fml) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from 
                             erp_inventario_online 
                             where inv_estado=$e 
                             and inv_subgrupo_familia='$fml'    
                             order by inv_subgrupo_familia,inv_descripcion $limit ");
        }
    }

    function update_inv_general($id, $campo, $val) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_inventario_online SET $campo='$val'   WHERE inv_id=$id");
        }
    }
    
    function lista_familias() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT split_part(ps.pro_tipo, '&', 10) AS protipo ,ps.* FROM erp_productos_set ps order by protipo");
        }
    }
    

}

?>
