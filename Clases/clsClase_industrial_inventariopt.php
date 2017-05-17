<?php

include_once 'Conn.php';

class Clase_industrial_inventariopt {

    var $con;

    function Clase_industrial_inventariopt() {
        $this->con = new Conn();
    }

    function lista_ingreso_inventariopt($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.bod_id=$bod order by p.pro_codigo, p.pro_descripcion");
        }
    }

    function lista_inventariopt_noperti($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("
                            SELECT m.*,p.*,t.*,ps.pro_tipo FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t, erp_productos_set ps  where  m.pro_id=p.id and ps.ids=p.ids and m.trs_id=t.trs_id and m.bod_id=$bod ORDER BY mov_documento desc               
                    ");
        }
    }

    function lista_inventariopt($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.pro_id, m.mov_tabla FROM  erp_i_mov_inv_pt m  where  m.bod_id=$bod group by m.pro_id, m.mov_tabla ORDER BY m.pro_id,mov_tabla");
        }
    }

    function lista_suma_ingreso($cod, $bod, $tab, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id='$cod' and t.trs_operacion= 0 and m.bod_id=$bod and m.mov_tabla=$tab and m.mov_fecha_trans between '$fec1' and '$fec2'");
        }
    }

    function lista_suma_egreso($cod, $bod, $tab, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id='$cod' and t.trs_operacion= 1 and m.bod_id=$bod and m.mov_tabla=$tab and m.mov_fecha_trans between '$fec1' and '$fec2'");
        }
    }

    function lista_buscar_inventariopt1($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.pro_id, m.mov_tabla FROM  erp_i_mov_inv_pt m $txt group by m.pro_id, m.mov_tabla ORDER BY m.pro_id,mov_tabla");
        }
    }
    function lista_inventariopt_tot($bod,$fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.pro_id, m.pro_tbl, m.mvt_cant, m.cod_punto_emision,cast('$fec' as date) FROM erp_i_movpt_total m, erp_i_productos p where p.pro_id=m.pro_id and mvt_cant<>0 and pro_tbl=0 and p.pro_estado=0 and cod_punto_emision=$bod union
                             SELECT m.pro_id, m.pro_tbl, m.mvt_cant, m.cod_punto_emision,cast('$fec' as date) FROM erp_i_movpt_total m, erp_productos p where p.id=m.pro_id and mvt_cant<>0 and pro_tbl=1 and pro_estado=0 and cod_punto_emision=$bod");
        }
    }
    
     function lista_buscar_inventariopt($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 7) as mov_pago,p.pro_tipo FROM erp_i_mov_inv_pt m, erp_i_productos p where m.pro_id=p.pro_id  and m.pro_id=p.pro_id and substring(m.mov_pago from  1 for 2)='EC' and mov_flete='0' $txt group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 7),p.pro_tipo 
                            union
                            select m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 6) as mov_pago,p.pro_tipo FROM erp_i_mov_inv_pt m, erp_i_productos p where m.pro_id=p.pro_id  and m.pro_id=p.pro_id and substring(m.mov_pago from  1 for 1)='C' and mov_flete='0' $txt group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 6),p.pro_tipo 
                            union
                            select m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 6) as mov_pago,p.pro_tipo FROM erp_i_mov_inv_pt m, erp_i_productos p where m.pro_id=p.pro_id  and m.pro_id=p.pro_id and substring(m.mov_pago from  1 for 1)!='C' and substring(m.mov_pago from  1 for 2)!='EC' and mov_flete='0' $txt group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,substring(m.mov_pago from  1 for 6),p.pro_tipo 
                            ORDER BY mov_pago, pro_id");
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
    
    function lista_productos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("(SELECT '1' as tab, p.id,p.pro_a,p.pro_b,p.pro_ac,ps.pro_tipo as tipo FROM  erp_productos p,erp_productos_set ps where ps.ids=p.ids and (p.pro_a like'%$txt%' or p.pro_b like'%$txt%') union 
                              SELECT '0' as tab,pro_id, pro_codigo,pro_descripcion,'','' as tipo from erp_i_productos  where pro_codigo like'%$txt%' or pro_descripcion like'%$txt%') ORDER BY pro_b");
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

    function buscar_un_movimiento($id, $tab, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_movpt_total where pro_id='$id' and pro_tbl='$tab' and cod_punto_emision=$emi");
        }
    }
///////////////////////////////////////////////////////////////////////////////////////         
///////////INVENTARIO GENERAL//////////////////////

    function lista_prod_inventario_general() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pro_id,pro_tbl FROM erp_i_movpt_total group by pro_id,pro_tbl");
        }
    }

    function lista_locales() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor order by cod_orden ");
        }
    }
    
    function lista_emisores($val) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from emisor 
                $val
                order by cod_orden");
        }
    }

    function lista_inv_prod_local($pro, $tbl, $loc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_movpt_total where pro_id=$pro and pro_tbl=$tbl and cod_punto_emision=$loc ");
        }
    }

    function lista_inv_local($loc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(mvt_cant) FROM erp_i_movpt_total where cod_punto_emision=$loc ");
        }
    }

    function lista_inv_productos($bod) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT pro_id,mov_tabla FROM erp_i_mov_inv_pt where bod_id=$bod group by pro_id,mov_tabla");
            return pg_query("SELECT m.pro_id,m.mov_tabla FROM erp_i_mov_inv_pt m, erp_i_productos p where m.pro_id=p.pro_id and m.bod_id=$bod and m.mov_tabla=0 and p.pro_estado=0 and m.mov_cantidad <>0 group by m.pro_id,m.mov_tabla union 
                             SELECT m.pro_id,m.mov_tabla FROM erp_i_mov_inv_pt m, erp_productos p where m.pro_id=p.id and m.bod_id=$bod and m.mov_tabla=1 and p.pro_estado=0 and m.mov_cantidad <>0 group by m.pro_id,m.mov_tabla order by pro_id");
        }
    }

    function total_ingreso_egreso($id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egreso");
        }
    }

    function update_cantidad($cant, $id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_movpt_total SET mvt_cant=$cant WHERE pro_id=$id and cod_punto_emision=$emi and pro_tbl=$tab");
        }
    }

    function insert_movpt_total($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_movpt_total (
                pro_id,
                pro_tbl,
                mvt_cant, 
                mvt_fecha, 
                cod_punto_emision)VALUES(
                $data[0],
                $data[1],
                '$data[2]',
                '$data[3]',
                $data[4])");
        }
    }

    function limpiar_movpt_total() {
        if ($this->con->Conectar() == true) {
            return pg_query("TRUNCATE TABLE erp_i_movpt_total");
        }
    }

    function buscar_total_productos($txt1, $txt2, $linea, $talla, $fml, $fml2, $val) {
        if ($this->con->Conectar() == true) {
            return pg_query("(SELECT '1' as tbl,p.id as id,p.pro_ac as lote,p.pro_a as codigo,p.pro_b as descripcion FROM  erp_productos p, erp_i_movpt_total m where p.id=m.pro_id $txt1 $linea $talla $fml $val
                              union
                              SELECT '0' as tbl, p.pro_id as id, '' as lote ,p.pro_codigo as codigo,p.pro_descripcion as descripcion FROM  erp_i_productos p, erp_i_movpt_total m  where p.pro_id=m.pro_id $fml2 $txt2 ) order by descripcion");
        }
    }

    function suma_inv_loc($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(mvt_cant) FROM erp_i_movpt_total where $txt");
        }
    }
    
    /////////////////////////////////////////////////////// lista buscador inventario
    
    function lista_familias() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT split_part(ps.pro_tipo, '&', 10) AS protipo ,ps.* FROM erp_productos_set ps order by protipo");
        }
    }
    
    ////////////////////////////////////////////////// lista reporte inventario general productos
    
    function  lista_repote_inventario_productos_buscador($txt, $fml){
        if($this->con->Conectar() == true){
            return pg_query("select split_part(prod,'&',1) as familia,
                                    split_part(prod,'&',2) as codigo,
                                    split_part(prod,'&',3) as descripcion,
                                    split_part(prod,'&',4) as lote,
                                    split_part(prod,'&',5) as ids,
                                    split_part(prod,'&',6) as linea,
                                    split_part(prod,'&',7) as talla,
                                    to_char(cast(loc1 as double precision),'99,999,990') as loc1,
                                    to_char(cast(loc2 as double precision),'99,999,990') as loc2,
                                    to_char(cast(loc3 as double precision),'99,999,990') as loc3,
                                    to_char(cast(loc4 as double precision),'99,999,990') as loc4,
                                    to_char(cast(loc5 as double precision),'99,999,990') as loc5,
                                    to_char(cast(loc6 as double precision),'99,999,990') as loc6,
                                    to_char(cast(loc7 as double precision),'99,999,990') as loc7,
                                    to_char(cast(loc8 as double precision),'99,999,990') as loc8,
                                    to_char(cast(loc9 as double precision),'99,999,990') as loc9,
                                    to_char(cast(loc10 as double precision),'99,999,990') as loc10,
                                    to_char(cast(loc11 as double precision),'99,999,990') as loc11,
                                    to_char(cast(loc12 as double precision),'99,999,990') as loc12,
                                    to_char(cast(loc13 as double precision),'99,999,990') as loc13,
                                    to_char(cast(loc14 as double precision),'99,999,990') as loc14
                                from inventario_general where prod is not null   
                                $txt
                                $fml ");
        }
    }
    
    function  lista_inventario_negativo($txt, $fml){
        if($this->con->Conectar() == true){
            return pg_query("select split_part(prod,'&',1) as familia,
                                    split_part(prod,'&',2) as codigo,
                                    split_part(prod,'&',3) as descripcion,
                                    split_part(prod,'&',4) as lote,
                                    split_part(prod,'&',5) as ids,
                                    split_part(prod,'&',6) as linea,
                                    split_part(prod,'&',7) as talla,
                                    to_char(cast(loc1 as double precision),'99,999,990') as loc1,
                                    to_char(cast(loc2 as double precision),'99,999,990') as loc2,
                                    to_char(cast(loc3 as double precision),'99,999,990') as loc3,
                                    to_char(cast(loc4 as double precision),'99,999,990') as loc4,
                                    to_char(cast(loc5 as double precision),'99,999,990') as loc5,
                                    to_char(cast(loc6 as double precision),'99,999,990') as loc6,
                                    to_char(cast(loc7 as double precision),'99,999,990') as loc7,
                                    to_char(cast(loc8 as double precision),'99,999,990') as loc8,
                                    to_char(cast(loc9 as double precision),'99,999,990') as loc9,
                                    to_char(cast(loc10 as double precision),'99,999,990') as loc10,
                                    to_char(cast(loc11 as double precision),'99,999,990') as loc11,
                                    to_char(cast(loc12 as double precision),'99,999,990') as loc12,
                                    to_char(cast(loc13 as double precision),'99,999,990') as loc13,
                                    to_char(cast(loc14 as double precision),'99,999,990') as loc14
                                from inventario_negativo where prod is not null   
                                $txt
                                $fml ");
        }
    }
    
    function total_inventario($id,$lt,$txt,$c) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 0 $txt and m.mov_flete='0') as ingreso_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 1 $txt and m.mov_flete='0') as egreso_con,
                                   (SELECT SUM(m.mov_tabla) FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 0 $txt and m.mov_flete='0') as cnt_ingreso_con,
                                   (SELECT SUM(m.mov_tabla) FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 1 $txt and m.mov_flete='0') as cnt_egreso_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 0 $txt and m.mov_flete='3') as ingreso_inc,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 1 $txt and m.mov_flete='3') as egreso_inc,
                                   (SELECT SUM(m.mov_tabla) FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 0 $txt and m.mov_flete='3') as cnt_ingreso_inc,
                                   (SELECT SUM(m.mov_tabla) FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and substring(m.mov_pago from  1 for $c)='$lt' and t.trs_operacion= 1 $txt and m.mov_flete='3') as cnt_egreso_inc");
        }
    }
    
    function lista_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_padding where replace(opp_codigo,'-','')='$id'");
        }
    }
    
    ////inventario x rollos
      function lista_buscar_inventariopt_rollos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,m.mov_pago FROM erp_i_mov_inv_pt m, erp_i_productos p where m.pro_id=p.pro_id  and m.pro_id=p.pro_id $txt group by m.pro_id, p.pro_codigo, p.pro_descripcion,p.pro_uni,m.mov_pago ORDER BY  m.mov_pago,m.pro_id");
        }
    }
    
        function total_inventario_rollos($id,$lt,$txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and m.mov_pago='$lt' and t.trs_operacion= 0 $txt and m.mov_flete='0') as ingreso_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and m.mov_pago='$lt' and t.trs_operacion= 1 $txt and m.mov_flete='0') as egreso_con,
                                   (SELECT SUM(m.mov_tabla) FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and m.mov_pago='$lt' and t.trs_operacion= 0 $txt and m.mov_flete='0') as cnt_ingreso_con,
                                   (SELECT SUM(m.mov_tabla) FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and m.mov_pago='$lt' and t.trs_operacion= 1 $txt and m.mov_flete='0') as cnt_egreso_con,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and m.mov_pago='$lt' and t.trs_operacion= 0 $txt and m.mov_flete='3') as ingreso_inc,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and m.mov_pago='$lt' and t.trs_operacion= 1 $txt and m.mov_flete='3') as egreso_inc,
                                   (SELECT SUM(m.mov_tabla) FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and m.mov_pago='$lt' and t.trs_operacion= 0 $txt and m.mov_flete='3') as cnt_ingreso_inc,
                                   (SELECT SUM(m.mov_tabla) FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and m.mov_pago='$lt' and t.trs_operacion= 1 $txt and m.mov_flete='3') as cnt_egreso_inc");
        }
    }
}

?>
