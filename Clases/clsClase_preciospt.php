<?php

include_once 'Conn.php';

class Clase_preciospt {

    var $con;

    function Clase_preciospt() {
        $this->con = new Conn();
    }

    function lista_precios() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios order by pre_id asc");
        }
    }

    function lista_secuencial_cliente($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where substr(cli_codigo,1,2)='$tp' order by cli_codigo desc limit 1");
        }
    }

    function lista_precios_proid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios where pro_id=$id order by pre_id asc");
        }
    }

    function lista_precios_proid_tabla($id, $tabla) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios where pro_id=$id and pro_tabla=$tabla");
        }
    }

    function lista_i_productos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$id");
        }
    }

    function lista_productos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where id=$id");
        }
    }

    function lista_buscador_precios($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pro_precios where $bod");
        }
    }

    function lista_buscador_i_productos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where ($txt) and pro_estado=0");
        }
    }

    function lista_buscador_productos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where ($txt) and pro_estado=0");
        }
    }

    function insert_precios($id, $pre_id, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_pro_precios (pre_id,pro_id, pro_tabla, pre_precio) VALUES ($pre_id,$id,$tab,'0.00')");
        }
    }

    function upd_precios($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_precio=$data[0],pre_iva='$data[1]',pre_precio2=$data[2] WHERE pre_id=$id");
            //return pg_query("UPDATE erp_pro_precios SET pre_precio=$data[0],pre_iva='$data[1]' WHERE pre_id=$id");
        }
    }

    function ultimo_precios() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_pro_precios order by pre_id desc limit 1");
        }
    }

    function del_pre($id, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_pro_precios WHERE pro_id=$id and pro_tabla=$tab");
        }
    }

    function upd_precios_todos($desc) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_descuento='$desc'");
        }
    }

    function upd_precios2($id) { 
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_precio = pre_precio2, pre_precio2=0 where pre_id=$id");
        }
    }

    function upd_precios_precios2() { 
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_precio = pre_precio2, pre_precio2=0 where pre_id=$id");
        }
    }

    function upd_vald_pre1($id) { 
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_vald_precio1=0, pre_vald_precio2=1 where pro_id=$id and pro_tabla=1");
        }
    }

    function upd_vpre1_pre2($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_vald_precio1=1, pre_vald_precio2=0 where pro_tabla=$id");
        }
    }

    function upd_vald_pre2($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_vald_precio1=0, pre_vald_precio2=1 where pro_tabla=$id");
        }
    }

    function upd_vpre2_pre1($id) { 
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_vald_precio1=1, pre_vald_precio2=0 where pro_id=$id and pro_tabla=1");
        }
    }

    function ultimo_producto_comercial() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_productos order by id desc limit 1");
        }
    }

    function ultimo_producto_industrial() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_productos order by pro_id desc limit 1");
        }
    }

    function lista_precios_id($id, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios where pro_id ='$id' and pro_tabla=$tab");
        }
    }

    function lista_emisor() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  emisor");
        }
    }

    function lista_emisor_cod($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  emisor where cod_punto_emision=$cod");
        }
    }

    function insert_descuento($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_descuentos(pre_id, des_fec_inicio, des_fec_fin, des_valor,cod_punto_emision) VALUES ($id,'$data[0]','$data[1]','$data[2]','$data[3]')");
        }
    }

    function update_descuentos($data, $id, $des) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_descuentos SET pre_id=$id, des_fec_inicio='$data[0]',des_fec_fin='$data[1]',des_valor='$data[2]',cod_punto_emision='$data[3]' where des_id=$des");
        }
    }

    function delete_descuentos($des) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_descuentos where des_id=$des");
        }
    }

    function lista_un_descuento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos where pre_id=$id");
        }
    }

    function lista_descuentos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos");
        }
    }

    function lista_buscar_descuentos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos d, erp_pro_precios p where d.pre_id=p.pre_id and $txt");
        }
    }

    function lista_un_descuento_precio($id, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos d, erp_pro_precios p  where d.pre_id=p.pre_id and d.pre_id=$id and d.cod_punto_emision=$pto");
        }
    }

    function lista_un_descuento_fecha($id, $pto, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos d, erp_pro_precios p  where d.pre_id=p.pre_id and d.pre_id=$id and d.cod_punto_emision=$pto and '$fec' between d.des_fec_inicio  and d.des_fec_fin");
        }
    }

    function lista_un_producto_noperti_cod_lote($code, $lote) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code' and pro_ac='$lote'");
        }
    }

    function lista_un_producto_industrial($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$code'");
        }
    }
//////////////////////////// cambios productos comerciales Omar
    
    function lista_by_tipo_productos_comerciales() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT split_part(ps.pro_tipo, '&', 10) AS protipo ,ps.* FROM erp_productos_set ps order by protipo");
        }
    }
    
    function lista_table_by_tipo($table, $tipo) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM $table WHERE ids=$tipo and pro_estado=0 order by id");
        }
    }
    
    function lista_table_by_comercial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_productos p, erp_pro_precios r where p.id=r.pro_id and r.pro_id=$id and pro_tabla=1");
        }
    }
    
    function lista_table_pro_set($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_productos WHERE ids=$id");
        }
    }

    function upd_precios_costos($id) { 
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_costo1 = pre_costo2, pre_costo2='0' where pre_id=$id");
        }
    }
    
    function upd_costos($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_costo1=$data[0],pre_costo2='$data[1]' WHERE pre_id=$id");
        }
    }
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
