<?php

include_once 'Conn.php';

class Clase_reg_plumon {

    var $con;

    function Clase_reg_plumon() {
        $this->con = new Conn();
    }

    function lista_buscador_orden($txt) {  
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_op_plumon r, erp_i_orden_produccion_plumon o, erp_i_productos p where r.orp_id=o.orp_id and o.pro_id=p.pro_id $txt order by rpl_numero");
        }
    }

    function lista_secuencial_registro() {  
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_op_plumon order by rpl_numero desc limit 1");
        }
    }

    function lista_un_producto($id) {  
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos where pro_id=$id");
        }
    }

    function lista_una_orden_cod($id) { 
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_plumon WHERE orp_num_pedido='$id'");
        }
    }

    function insert_registro_plumon($data) { 
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_reg_op_plumon(
					rpl_fecha, 
					rpl_peso, 
					rpl_rollo,
					rpl_desperdicio, 
					rpl_operador, 
					orp_id,
					rpl_numero)
                              VALUES ( '$data[0]',
                                       '$data[1]',
                                       '$data[2]',
                                       '$data[3]',
                                       '$data[4]',
                                       '$data[5]',
                                       '$data[6]')");
        }
    }

    function update_registro_plumon($data, $id) {   
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_op_plumon
					SET rpl_fecha='$data[0]', 
					rpl_peso='$data[1]',
					rpl_rollo='$data[2]',
					rpl_desperdicio='$data[3]', 
					rpl_operador='$data[4]', 
					orp_id='$data[5]',
					rpl_numero='$data[6]' where rpl_id=$id");
        }
    }

    function delete_registro_plumon($id) { 
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE from erp_reg_op_plumon WHERE rpl_id=$id");
        }
    }

    function lista_produccion_pedido($id) { 
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(rpl_rollo) as rollo,sum(rpl_peso) as peso FROM erp_reg_op_plumon where orp_id=$id");
        }
    }
    
///////////////////////////////////////////////////////////////////////// ORDENES PLUMON
    
    function lista_una_orden_produccion_numero_orden_plumon($txt,$fec1,$fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_plumon p, erp_i_cliente c  WHERE p.cli_id=c.cli_id and orp_num_pedido like '%$txt%' and orp_fec_pedido between '$fec1' and '$fec2'");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
