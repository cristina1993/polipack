<?php

include_once 'Conn.php';

class Clase_reg_padding {

    var $con;

    function Clase_reg_padding() {
        $this->con = new Conn();
    }

    function lista_buscador_orden($txt) {  
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_op_padding r, erp_i_orden_produccion_padding o, erp_i_productos p where r.opp_id=o.opp_id and o.pro_id=p.pro_id $txt order by rpa_numero");
        }
    }

    function lista_secuencial_registro() {  
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_op_padding order by rpa_numero desc limit 1");
        }
    }

    function lista_un_producto($id) {  
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos where pro_id=$id");
        }
    }

    function lista_una_orden_cod($id) { 
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_padding WHERE opp_codigo='$id'");
        }
    }

    function insert_registro_padding($data) { 
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_reg_op_padding(
					rpa_fecha, 
					rpa_peso, 
					rpa_rollo,
					rpa_desperdicio, 
					rpa_operador, 
					opp_id,
					rpa_numero)
                              VALUES ( '$data[0]',
                                       '$data[1]',
                                       '$data[2]',
                                       '$data[3]',
                                       '$data[4]',
                                       '$data[5]',
                                       '$data[6]')");
        }
    }

    function update_registro_padding($data, $id) {   
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_op_padding
					SET rpa_fecha='$data[0]', 
					rpa_peso='$data[1]',
					rpa_rollo='$data[2]',
					rpa_desperdicio='$data[3]', 
					rpa_operador='$data[4]', 
					opp_id='$data[5]',
					rpa_numero='$data[6]' where rpa_id=$id");
        }
    }

    function delete_registro_padding($id) { 
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE from erp_reg_op_padding WHERE rpa_id=$id");
        }
    }

    function lista_produccion_pedido($id) { 
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(rpa_rollo) as rollo,sum(rpa_peso) as peso FROM erp_reg_op_padding where opp_id=$id");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
