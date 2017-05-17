<?php

include_once 'Conn.php';

class Clase_Productos {

    var $con;

    function Clase_Productos() {
        $this->con = new Conn();
    }

    function lista() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa e ,erp_i_productos p where e.emp_id=p.emp_id ORDER BY pro_id DESC");
        }
    }

    function lista_secuencial($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos p, erp_empresa e 
where e.emp_id=p.emp_id
and e.emp_id=$emp ORDER BY p.pro_codigo DESC LIMIT 1 ");
        }
    }

    function lista_siglas($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id=$emp");
        }
    }

    function lista_uno($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa e ,erp_i_productos p where e.emp_id=p.emp_id and p.pro_codigo like '%$codigo%' and pro_id=$id order by pro_codigo");
        }
    }

    function lista_buscador($txt) {
        if ($this->con->Conectar() == true) {
                return pg_query("SELECT * FROM erp_i_productos p,erp_empresa e where e.emp_id=p.emp_id $txt ");
        }
    }

    function lista_buscador_estado($std) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos WHERE pro_estado=$std");
        }
    }

    function insert($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_productos(
            emp_id, 
            pro_codigo,
            pro_familia, 
            pro_descripcion, 
            pro_uni,
            pro_largo,
            pro_ancho,
            pro_capa, 
            pro_espesor, 
            pro_gramaje, 
            pro_peso, 
            pro_medvul,
            pro_mp1,
            pro_mp2,
            pro_mp3,
            pro_mp4,
            pro_mp5,
            pro_mp6,
            pro_mf1,
            pro_mf2,
            pro_mf3,
            pro_mf4,
            pro_mf5,
            pro_mf6,
            pro_mp7,
            pro_mp8,
            pro_mp9,
            pro_mp10,
            pro_mp11,
            pro_mp12,
            pro_mf7,
            pro_mf8,
            pro_mf9,
            pro_mf10,
            pro_mf11,
            pro_mf12,
            pro_mp13,
            pro_mp14,
            pro_mp15,
            pro_mp16,
            pro_mp17,
            pro_mp18,
            pro_mf13,
            pro_mf14,
            pro_mf15,
            pro_mf16,
            pro_mf17,
            pro_mf18,
            pro_tipo,
            pro_por_tornillo1,
            pro_por_tornillo2,
            pro_por_tornillo3,
            pro_propiedad4,
            pro_propiedad5,
            pro_propiedad6,
            pro_propiedad7
            )
    VALUES ($data[0],'$data[1]',$data[2],'$data[3]','$data[4]','$data[5]','$data[6]',$data[7],'$data[8]','$data[9]','$data[10]','$data[11]',
                    '$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]',
                    '$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]',
                    '$data[24]','$data[25]','$data[26]','$data[27]','$data[28]','$data[29]',
                    '$data[30]','$data[31]','$data[32]','$data[33]','$data[34]','$data[35]',
                    '$data[36]','$data[37]','$data[38]','$data[39]','$data[40]','$data[41]',
                    '$data[42]','$data[43]','$data[44]','$data[45]','$data[46]','$data[47]',
                    '$data[48]','$data[49]','$data[50]','$data[51]',
                    '$data[52]','$data[53]','$data[54]','$data[55]'    
                    )");
        }
    }

    function upd($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_productos SET emp_id=$data[0],
                                                        pro_codigo='$data[1]',
                                                        pro_familia=$data[2],
                                                        pro_descripcion='$data[3]',
                                                        pro_uni='$data[4]',
                                                        pro_largo='$data[5]',
                                                        pro_ancho='$data[6]',
                                                        pro_capa=$data[7],
                                                        pro_espesor='$data[8]',
                                                        pro_gramaje='$data[9]',
                                                        pro_peso='$data[10]',
                                                        pro_medvul='$data[11]',
                                                        pro_mp1=$data[12],
                                                        pro_mp2=$data[13],
                                                        pro_mp3=$data[14],
                                                        pro_mp4=$data[15],
                                                        pro_mp5=$data[16],
                                                        pro_mp6=$data[17],
                                                        pro_mf1='$data[18]',
                                                        pro_mf2='$data[19]',
                                                        pro_mf3='$data[20]',
                                                        pro_mf4='$data[21]',
                                                        pro_mf5='$data[22]',
                                                        pro_mf6='$data[23]',
                                                        pro_mp7='$data[24]',
                                                        pro_mp8='$data[25]',
                                                        pro_mp9='$data[26]',
                                                        pro_mp10='$data[27]',
                                                        pro_mp11='$data[28]',
                                                        pro_mp12='$data[29]',
                                                        pro_mf7='$data[30]',
                                                        pro_mf8='$data[31]',
                                                        pro_mf9='$data[32]',
                                                        pro_mf10='$data[33]',
                                                        pro_mf11='$data[34]',
                                                        pro_mf12='$data[35]',
                                                        pro_mp13='$data[36]',
                                                        pro_mp14='$data[37]',
                                                        pro_mp15='$data[38]',
                                                        pro_mp16='$data[39]',
                                                        pro_mp17='$data[40]',
                                                        pro_mp18='$data[41]',
                                                        pro_mf13='$data[42]',
                                                        pro_mf14='$data[43]',
                                                        pro_mf15='$data[44]',
                                                        pro_mf16='$data[45]',
                                                        pro_mf17='$data[46]',
                                                        pro_mf18='$data[47]',
                                                        pro_tipo='$data[48]',
                                                        pro_por_tornillo1='$data[49]',
                                                        pro_por_tornillo2='$data[50]',
                                                        pro_por_tornillo3='$data[51]',    
                                                        pro_propiedad4='$data[52]',    
                                                        pro_propiedad5='$data[53]',    
                                                        pro_propiedad6='$data[54]',    
                                                        pro_propiedad7='$data[55]'    
                                                        WHERE pro_id=$id");
        }
    }

    function delete($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_productos WHERE pro_id=$id");
        }
    }

    function lst_emp() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id>2 ORDER BY emp_descripcion");
        }
    }

//    function lista_empresas() {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_empresa where emp_id>2 ORDER BY emp_descripcion");
//        }
//    }
    function lista_combomp($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mp m, erp_i_tpmp t where m.mpt_id=t.mpt_id and emp_id=$emp ORDER BY mpt_nombre");
        }
    }

    function update_estado_ind($est, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_productos set pro_estado=$est where pro_id=$id");
        }
    }

    function update_estado_com($est, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_productos set pro_estado=$est where id=$id");
        }
    }

    function lista_producto_plumon() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos where emp_id=3 ORDER BY pro_descripcion ");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
