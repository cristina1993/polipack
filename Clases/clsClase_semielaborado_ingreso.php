<?php

include_once 'Conn.php';

class Clase_semielaborado_ingreso {

    var $con;

    function Clase_semielaborado_ingreso() {
        $this->con = new Conn();
    }

    function lista_buscador_industrial_ingresopt($txt) {

        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mov_inv_semielaborado m, erp_transacciones t, erp_i_cliente c, erp_i_productos p where m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=26 and m.pro_id=p.pro_id $txt order by m.mov_documento asc");
        }
    }

    function lista_num_productos($txt) {

        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.pro_id FROM  erp_mov_inv_semielaborado m, erp_transacciones t, erp_i_cliente c,erp_i_productos p where m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=26 and m.pro_id=p.pro_id  $txt group by  p.pro_id ");
        }
    }

    function lista_productos_total() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_estado=0 order by pro_codigo");
        }
    }

    function lista_un_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$id");
        }
    }

    function lista_ingresos_doc($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pro_id,mov_tabla,sum(mov_cantidad) FROM erp_i_mov_inv_pt  where mov_documento='$doc' group by pro_id");
        }
    }

    function lista_secuencial_transferencia() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_secuencial_semielaborado   ORDER BY sec_id DESC LIMIT 1");
        }
    }

    function insert_sec_transferencia($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_secuencial_semielaborado(sec_transferencias) VALUES ('$data')");
        }
    }

    function insert_transferencia($data) {
        if ($this->con->Conectar() == true) {
            $f = date('Y-m-d');
            $h = date('H:i');
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_mov_inv_semielaborado(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_cantidad,                
                mov_tabla,                
                mov_fecha_registro,
                mov_hora_registro,
                mov_usuario,
                mov_pago
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$data[4]',   
            '$data[5]',
            '$data[6]',   
            '$data[7]',
            '$data[8]',
            '$f',
            '$h',
            '$usu',
            '$data[9]')");
        }
    }

      function lista_transaccion($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_transacciones where trs_id=$emp");
        }
    }
   }

?>
