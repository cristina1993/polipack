<?php
set_time_limit(0);
class InvOnline {

    function Conectar() {
        return pg_connect('host=localhost'
                . ' port=5432 '
                . ' dbname=noperti'
                . ' user=postgres'
                . ' password=SuremandaS495');
    }

    function lista() {
        if ($this->Conectar() == true) {
            return pg_query("select pro_id from erp_i_movpt_total where pro_tbl=1 group by pro_id order by pro_id");
        }
    }
    function lista2($pro,$tbl) {
        if ($this->Conectar() == true) {
            return pg_query("select * from erp_i_movpt_total where pro_id=$pro and pro_tbl=$tbl and mvt_cant>0 ");
        }
    }
    function lista_pro_inv_online($pro,$tbl) {
        if ($this->Conectar() == true) {
            return pg_query("select * from erp_inventario_online where inv_pro_id=$pro and inv_tbl=$tbl ");
        }
    }

    function update($pe, $cnt, $pro) {
        if ($this->Conectar() == true) {
            return pg_query("update erp_inventario_online set inv_bodega$pe = $cnt  where inv_pro_id=$pro");
        }
    }

    function insert($pe, $cnt, $pro,$tbl) {
        if ($this->Conectar() == true) {
            return pg_query("INSERT INTO erp_inventario_online(
            inv_codigo_barras,
            inv_descripcion,
            inv_bodega$pe,
            inv_sumatoria_bodegas,
            inv_precio_pvp, 
            inv_subgrupo_familia,
            inv_marca,
            inv_talla_medida, 
            inv_lote,
            inv_estado,
            inv_pro_id, 
            inv_tbl)
            
select 
p.pro_a ||'.'|| p.pro_ac,
p.pro_b,
$cnt,
0,
pr.pre_precio,
split_part(ps.pro_tipo, '&', 10) AS familia,
p.pro_ab,
p.pro_t,
p.pro_ac,
0,
$pro,
$tbl
from erp_productos p
join erp_productos_set ps on(p.ids=ps.ids and p.id=$pro)
join erp_pro_precios pr on(pr.pro_id=p.id and pr.pro_tabla=$tbl) ");
        }
    }

}

$Obj = new InvOnline();
$cns = $Obj->lista();
$n = 0;
$tbl=1;
while ($rst = pg_fetch_array($cns)) {
    $n++;
    $cns_inv = $Obj->lista2($rst[pro_id],$tbl);
    while ($rst_inv = pg_fetch_array($cns_inv)) {
        $rst_dato=  pg_fetch_array($Obj->lista_pro_inv_online($rst[pro_id],$tbl));
        if(!empty($rst_dato)){
            $Obj->update($rst_inv[cod_punto_emision], $rst_inv[mvt_cant], $rst[pro_id]);
        }else{
            $Obj->insert($rst_inv[cod_punto_emision], $rst_inv[mvt_cant], $rst[pro_id],$tbl);
        }
        
        
    }
}