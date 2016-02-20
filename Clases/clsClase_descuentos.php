<?php

include_once 'Conn.php';

class Descuentos {

    var $con;

    function Descuentos() {
        $this->con = new Conn();
    }

    function lista_locales() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  emisor ORDER by cod_orden ");
        }
    }

    function lista_precio_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios where pre_id=$id ");
        }
    }

    function lista_desc_prid_ems($prid, $ems) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos where pre_id=$prid and ems_id=$ems ");
        }
    }

    function lista_precio_prod_tbl($proid, $tbl) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios where pro_id=$proid and pro_tabla=$tbl ");
        }
    }

    function lista_productos($tblp) {
        if ($this->con->Conectar() == true) {
            if ($tblp > 0) {
                $tbl = 1;
            } else {
                $tbl = 0;
            }
            switch ($tbl) {
                case 0:
                    $sql = "SELECT pro_id as id,'0' as tbl, pro_codigo as codigo, '' as lote, pro_descripcion as desc,(select pre_id from erp_pro_precios where erp_pro_precios.pro_id=erp_i_productos.pro_id and erp_pro_precios.pro_tabla=0 limit 1) as precio, (select pre_precio from erp_pro_precios where erp_pro_precios.pro_id=erp_i_productos.pro_id and erp_pro_precios.pro_tabla=0 limit 1) as precios FROM erp_i_productos where pro_estado=0";
                    break;
                case 1:
                    $sql = "SELECT id as id,'1' as tbl, pro_a as codigo, pro_ac as lote, pro_b as desc, (select pre_id from erp_pro_precios where erp_pro_precios.pro_id=erp_productos.id and erp_pro_precios.pro_tabla=1 limit 1) as precio, (select pre_precio from erp_pro_precios where erp_pro_precios.pro_id=erp_productos.id and erp_pro_precios.pro_tabla=1 limit 1) as precios FROM erp_productos WHERE ids=$tblp and pro_estado=0";
                    break;
            }
            return pg_query($sql);
        }
    }

    function insert_descuentos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_descuentos(pre_id, ems_id, dsc_descuento)VALUES ($data[0],$data[1],$data[2])");
        }
    }

    function upd_descuentos($dsc, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_descuentos  SET dsc_descuento=$dsc  WHERE dsc_id=$id");
        }
    }

    function insert_precios($pro_id, $tbl) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_pro_precios(pro_id, pro_tabla, pre_precio)VALUES ($pro_id,$tbl,0)");
        }
    }

    function lista_by_tipo_productos_comerciales() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT split_part(ps.pro_tipo, '&', 10) AS protipo ,ps.* FROM erp_productos_set ps order by protipo");
        }
    }

    function lista_buscador_i_productos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pro_id as id,'0' as tbl, pro_codigo as codigo, '' as lote, pro_descripcion as desc,(select pre_id from erp_pro_precios where erp_pro_precios.pro_id=erp_i_productos.pro_id and erp_pro_precios.pro_tabla=0 limit 1) as precio, (select pre_precio from erp_pro_precios where erp_pro_precios.pro_id=erp_i_productos.pro_id and erp_pro_precios.pro_tabla=0 limit 1) as precios FROM erp_i_productos where ($txt) and pro_estado=0");
        }
    }

    function lista_buscador_productos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT id as id,'1' as tbl, pro_a as codigo, pro_ac as lote, pro_b as desc, (select pre_id from erp_pro_precios where erp_pro_precios.pro_id=erp_productos.id and erp_pro_precios.pro_tabla=1 limit 1) as precio, (select pre_precio from erp_pro_precios where erp_pro_precios.pro_id=erp_productos.id and erp_pro_precios.pro_tabla=1 limit 1) as precios FROM erp_productos where ($txt) and pro_estado=0");
        }
    }

    ///////////////////////////////////////////////////////// Reporte Descuentos    

    function lista_reporte_descuentos_productos_buscador($txt, $fml) {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(prod,'&',1) as familia,
                            split_part(prod,'&',2) as id,
                            split_part(prod,'&',3) as tbl,
                            split_part(prod,'&',4) as codigo,
                            split_part(prod,'&',5) as lote,
                            split_part(prod,'&',6) as descr,
                            split_part(prod,'&',7) as precio,
                            split_part(prod,'&',8) as precios,
                            loc1,
                            loc2,
                            loc3,
                            loc4,
                            loc5,
                            loc6,
                            loc7,
                            loc8,
                            loc9,
                            loc10,
                            loc11,
                            loc12,
                            loc13,
                            loc14
                            from descuentos where prod is not null
                            $txt
                            $fml    
                             ");
        }
    }

    function lista_reporte_descuentos_productos_cantidad($txt, $fml, $pag, $ini) {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(prod,'&',1) as familia,
                            split_part(prod,'&',2) as id,
                            split_part(prod,'&',3) as tbl,
                            split_part(prod,'&',4) as codigo,
                            split_part(prod,'&',5) as lote,
                            split_part(prod,'&',6) as descr,
                            split_part(prod,'&',7) as precio,
                            split_part(prod,'&',8) as precios,
                            loc1,
                            loc2,
                            loc3,
                            loc4,
                            loc5,
                            loc6,
                            loc7,
                            loc8,
                            loc9,
                            loc10,
                            loc11,
                            loc12,
                            loc13,
                            loc14
                            from descuentos where prod is not null
                            $txt
                            $fml
                            order by  id  DESC LIMIT $pag offset $ini
                             ");
        }
    }
    
    function lista_un_local($pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  emisor WHERE cod_punto_emision=$pto");
        }
    }

}

?>
