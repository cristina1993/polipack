<?php

include_once 'Conn.php';

class Clase_etiqueta_grande {

    var $con;

    function Clase_etiqueta_grande() {
        $this->con = new Conn();
    }

    function lista_empresa($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_empresa where emp_id=5");
        }
    }

    function lista_ordenes() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ord_num_orden, '0' as tip FROM  erp_i_orden_produccion 
                            union
                            SELECT ord_num_orden||'2', '0' as tip FROM  erp_i_orden_produccion where ord_pro_secundario!=0
                            union
                            SELECT ord_num_orden||'3', '0' as tip FROM  erp_i_orden_produccion where ord_pro3!=0
                            union
                            SELECT ord_num_orden||'4', '0' as tip FROM  erp_i_orden_produccion where ord_pro4!=0
                            union
                            SELECT opp_codigo as ord_num_orden,'1' as tip FROM  erp_i_orden_produccion_padding
                            order by tip, ord_num_orden");
        }
    }

    function lista_orden_extrusion($id, $pro) {
        if ($this->con->Conectar() == true) {
            switch ($pro) {
                case 2:
                    return pg_query("SELECT * FROM erp_i_orden_produccion o, erp_i_productos p, erp_i_cliente c where c.cli_id=o.cli_id and o.ord_pro_secundario=p.pro_id and o.ord_num_orden='$id'");
                    break;
                case 3:
                    return pg_query("SELECT * FROM erp_i_orden_produccion o, erp_i_productos p, erp_i_cliente c where c.cli_id=o.cli_id and o.ord_pro3=p.pro_id and o.ord_num_orden='$id'");
                    break;
                case 4:
                    return pg_query("SELECT * FROM erp_i_orden_produccion o, erp_i_productos p, erp_i_cliente c where c.cli_id=o.cli_id and o.ord_pro4=p.pro_id and o.ord_num_orden='$id'");
                    break;
                case '':
                    return pg_query("SELECT * FROM erp_i_orden_produccion o, erp_i_productos p, erp_i_cliente c where c.cli_id=o.cli_id and o.pro_id=p.pro_id and o.ord_num_orden='$id'");
                    break;
            }
        }
    }

    function lista_orden_corte($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_padding o, erp_i_productos p, erp_i_cliente c where c.cli_id=o.cli_id and o.pro_id=p.pro_id and o.opp_codigo='$id'");
        }
    }

        function insert_etiquetas($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_etiqueta_grande(
                                                            ord_id,
                                                            pro_id,
                                                            cli_id,
                                                            etg_tipo,
                                                            etg_numero,
                                                            etg_espacio1,
                                                            etg_espacio2, 
                                                            etg_espacio3,
                                                            etg_espacio4,
                                                            etg_espacio5,
                                                            etg_espacio6,
                                                            etg_espacio7, 
                                                            etg_espacio8,
                                                            etg_espacio9,
                                                            etg_espacio10,
                                                            etg_espacio11,
                                                            etg_espacio12, 
                                                            etg_espacio13,
                                                            etg_espacio14,
                                                            etg_copias,
                                                            etg_fecha,
                                                            etg_operador,
                                                            etg_pallet1,
                                                            etg_pallet2)
                                                    VALUES ('$data[0]',
                                                            '$data[1]',
                                                            '$data[2]',
                                                            '$data[3]',
                                                            '$data[4]',
                                                            '$data[5]',
                                                            '$data[6]',
                                                            '$data[7]',
                                                            '$data[8]',
                                                            '$data[9]',
                                                            '$data[10]',
                                                            '$data[11]',
                                                            '$data[12]',
                                                            '$data[13]',
                                                            '$data[14]',
                                                            '$data[15]',
                                                            '$data[16]',
                                                            '$data[17]',
                                                            '$data[18]',
                                                            '$data[19]',
                                                            '$data[20]',
                                                            '$data[21]',
                                                            '$data[22]',
                                                            '$data[23]'
                                                            )");
        }
    }
    
     function lista_una_etiqueta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_etiqueta_grande where etg_id='$id'");
        }
    }
    
    function lista_etiquetas($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_etiqueta_grande e, erp_i_cliente c where e.cli_id=c.cli_id and e.etg_procedencia=0 $txt order by etg_fecha");
        }
    }

   



    function upd_etiquetas($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_etiqueta_grande SET 
                                                            ord_id='$data[0]',
                                                            pro_id='$data[1]',
                                                            cli_id='$data[2]',
                                                            etg_tipo='$data[3]',
                                                            etg_numero='$data[4]',
                                                            etg_espacio1='$data[5]',
                                                            etg_espacio2='$data[6]', 
                                                            etg_espacio3='$data[7]',
                                                            etg_espacio4='$data[8]',
                                                            etg_espacio5='$data[9]',
                                                            etg_espacio6='$data[10]',
                                                            etg_espacio7='$data[11]', 
                                                            etg_espacio8='$data[12]',
                                                            etg_espacio9='$data[13]',
                                                            etg_espacio10='$data[14]',
                                                            etg_espacio11='$data[15]',
                                                            etg_espacio12='$data[16]', 
                                                            etg_espacio13='$data[17]',
                                                            etg_espacio14='$data[18]',
                                                            etg_copias='$data[19]',
                                                            etg_fecha='$data[20]',
                                                            etg_operador='$data[21]',    
                                                            etg_pallet1='$data[22]',    
                                                            etg_pallet2='$data[23]'    
                                    WHERE etg_id='$id'");
        }
    }

    function delete_etiqueta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_etiqueta_grande WHERE etg_id = '$id'"
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
