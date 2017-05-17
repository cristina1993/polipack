<?php

include_once 'Conn.php';

class Clase_etiquetas {

    var $con;

    function Clase_etiquetas() {
        $this->con = new Conn();
    }

    function lista_etiquetas($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_etiquetas $txt order by eti_descripcion");
        }
    }

    
    function lista_una_etiqueta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_etiquetas where eti_id='$id'");
        }
    }

    function insert_etiquetas($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_etiquetas(
                        eti_descripcion,
                        eti_tamano,
                        eti_elementos)
            VALUES ('$data[0]','$data[1]','$data[2]')");
        }
    }

    function upd_etiquetas($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_etiquetas SET 
                                    eti_descripcion='$data[0]', 
                                    eti_tamano='$data[1]',
                                    eti_elementos='$data[2]'
                                    WHERE eti_id='$id'");
        }
    }

    function delete_etiqueta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_etiquetas WHERE eti_id = '$id'"
            );
        }
    }
    
     function lista_opciones($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM
                            erp_procesos pr,
                            erp_modulos md,
                            erp_option_list ol
                            WHERE pr.proc_id=md.proc_id
                            and   ol.mod_id=md.mod_id
                            and pr.proc_id!=4
                            and   exists(select * from erp_asg_option_list aol where aol.opl_id=ol.opl_id) 
                            $txt
                            order by pr.proc_orden,md.mod_descripcion,ol.opl_modulo");
        }
    }

     function lista_una_asignacion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asig_etiquetas a, erp_etiquetas e WHERE a.eti_id=e.eti_id and a.opl_id = '$id'"
            );
        }
    }
    
    function insert_asignacion($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_asig_etiquetas(
                        opl_id,
                        eti_id)
            VALUES ('$data[0]','$data[1]')");
        }
    }
    
     function upd_asignacion($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_asig_etiquetas SET 
                                    opl_id='$data[0]', 
                                    eti_id='$data[1]'
                                    WHERE ase_id='$id'");
        }
    }
    
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
