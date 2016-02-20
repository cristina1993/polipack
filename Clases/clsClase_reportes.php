
<?php

include_once 'Conn.php';

class Reportes {

    var $con;

    function Reportes() {
        $this->con = new Conn();
    }

    function lista_asientos_epyg($desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("
(select con_concepto_debe from erp_asientos_contables where con_fecha_emision between '$desde' and '$hasta' and con_concepto_debe<>'' and substr(con_concepto_debe,1,1)>'3'  group by con_concepto_debe  )
union
(select con_concepto_haber from erp_asientos_contables where con_fecha_emision between '$desde' and '$hasta' and con_concepto_haber<>'' and substr(con_concepto_haber,1,1)>'3'  group by con_concepto_haber ) order by con_concepto_debe
");
        }
    }

    function lista_una_cuenta_codigo($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas WHERE pln_codigo='$cod'");
        }
    }

    function lista_cuentas_epyg() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas WHERE SUBSTR(pln_codigo,1,1)>'3' ORDER BY pln_codigo ");
        }
    }

    function lista_cuentas_existe($cod, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables WHERE (con_concepto_debe='$cod' or con_concepto_haber='$cod') and con_fecha_emision between '$desde' and '$hasta'");
        }
    }

    function lista_parcial_cuenta($cuenta, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("");
        }
    }

    ////////////////////////
    function listar_descripcion_asiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where pln_codigo='$id'");
        }
    }

    function lista_balance_general($cod, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,2)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe1,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,2)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber1,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,5)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe2,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,5)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber2,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,8)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe3,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,8)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber3,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,11)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe4,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,11)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber4,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,14)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe5,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,14)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber5");
        }
    }

    function suma_cuentas($cod, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT(SELECT sum(con_valor_debe) as debe FROM erp_asientos_contables where con_concepto_debe='$cod' and con_fecha_emision between '$fec1' and '$fec2') as debe,
                                   (SELECT sum(con_valor_haber) as debe FROM erp_asientos_contables where con_concepto_haber='$cod' and con_fecha_emision between '$fec1' and '$fec2') as haber");
        }
    }

///REOPORTES POR LOCALES
    function lista_emisores($val) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from emisor 
                $val
                order by cod_orden");
        }
    }

    function lista_devoluciones_vendedor($e, $d, $h, $v) {
        if ($this->con->Conectar() == true) {
            return pg_query("select count(*) as nfact,
                                    sum(c.nrc_total_valor) as tventa,
                                    sum(c.ncr_total_descuento) as desc,
                                    sum(c.ncr_subtotal12) as con_iva,
                                    (sum(c.ncr_subtotal0)+sum(c.ncr_subtotal_ex_iva)+sum(c.ncr_subtotal_no_iva)) as sin_iva,
                                    (sum(c.ncr_subtotal12)+(sum(c.ncr_subtotal0)+sum(c.ncr_subtotal_ex_iva)+sum(c.ncr_subtotal_no_iva))) as sbt_neto,
                                    sum(c.ncr_total_ice) as ice,
                                    sum(c.ncr_total_iva) as iva,
                                    sum(c.nrc_total_valor) as tventas
                            from erp_nota_credito c
                            where emi_id=$e
                            and ncr_fecha_emision between '$d' and '$h'
                            and vnd_id=$v
                            ");
        }
    }

    function lista_devoluciones_tot($e, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
count(*) as nfact,
sum(c.total_valor) as tventa,
sum(c.total_descuento) as desc,
sum(c.subtotal12) as con_iva,
(sum(c.subtotal0)+sum(c.subtotal_exento_iva)+sum(c.subtotal_no_objeto_iva)) as sin_iva,
(sum(c.subtotal12)+(sum(c.subtotal0)+sum(c.subtotal_exento_iva)+sum(c.subtotal_no_objeto_iva))) as sbt_neto,
sum(c.total_ice) as ice,
sum(c.total_iva) as iva,
sum(c.total_valor) as tventas
from comprobantes c
where c.cod_punto_emision=$e
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=4
and exists(
select * from comprobantes f 
where replace(f.num_documento,'-','')=c.num_factura_modifica
and f.fecha_emision >=$d
and f.fecha_emision <=$h
and f.tipo_comprobante=1
)");
        }
    }

    function lista_ventas_devoluciones_total($e, $d, $h, $t) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
count(*) as nfact,
sum(c.total_valor) as tventa,
sum(c.total_descuento) as desc,
sum(c.subtotal12) as con_iva,
(sum(c.subtotal0)+sum(c.subtotal_exento_iva)+sum(c.subtotal_no_objeto_iva)) as sin_iva,
(sum(c.subtotal12)+(sum(c.subtotal0)+sum(c.subtotal_exento_iva)+sum(c.subtotal_no_objeto_iva))) as sbt_neto,
sum(c.total_ice) as ice,
sum(c.total_iva) as iva,
sum(c.total_valor) as tventas
from comprobantes c
where c.cod_punto_emision=$e
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=$t
");
        }
    }

    function lista_ventas_devoluciones_vendedor1($e, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select vnd_id as vendedor,
                                    count(*) as nfact,
                                    sum(fac_total_valor) as tventa,
                                    sum(fac_total_descuento) as desc,
                                    sum(fac_subtotal12) as con_iva,
                                    (sum(fac_subtotal0)+sum(fac_subtotal_ex_iva)+sum(fac_subtotal_no_iva)) as sin_iva,
                                    (sum(fac_subtotal12)+(sum(fac_subtotal0)+sum(fac_subtotal_ex_iva)+sum(fac_subtotal_no_iva))) as sbt_neto,
                                    sum(fac_total_ice) as ice,
                                    sum(fac_total_iva) as iva,
                                    sum(fac_total_valor) as tventas
                            from erp_factura
                            where emi_id=$e
                            and fac_fecha_emision between '$d' and '$h'
                            group by vnd_id
                            ");
        }
    }

    function lista_ventas_devoluciones_vendedor($e, $d, $h, $v) {
        if ($this->con->Conectar() == true) {
            return pg_query("select vnd_id as vendedor,
                                    count(*) as nfact,
                                    sum(fac_total_valor) as tventa,
                                    sum(fac_total_descuento) as desc,
                                    sum(fac_subtotal12) as con_iva,
                                    (sum(fac_subtotal0)+sum(fac_subtotal_ex_iva)+sum(fac_subtotal_no_iva)) as sin_iva,
                                    (sum(fac_subtotal12)+(sum(fac_subtotal0)+sum(fac_subtotal_ex_iva)+sum(fac_subtotal_no_iva))) as sbt_neto,
                                    sum(fac_total_ice) as ice,
                                    sum(fac_total_iva) as iva,
                                    sum(fac_total_valor) as tventas
                            from erp_factura
                            where emi_id=$e
                            and fac_fecha_emision between '$d' and '$h'
                            and vnd_id=$v
                                group by vnd_id
                            ");
        }
    }

    function lista_vendedores_fac_not($e, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select vnd_id as vendedor from erp_factura where fac_fecha_emision between '$d' and '$h' and emi_id=$e                            
                             union 
                             select vnd_id as vendedor from erp_nota_credito c where ncr_fecha_emision between '$d' and '$h' and emi_id=$e
                             group by vnd_id order by vendedor
                            ");
        }
    }

    function lista_vendedores_factura($e, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select vnd_id as vendedor from erp_factura where fac_fecha_emision between '$d' and '$h' and emi_id=$e                            
                                                         group by vnd_id order by vendedor
                            ");
        }
    }

    function lista_reporte_productos($d, $h, $fml, $txt, $linea, $talla, $fml2, $val) {
        if ($this->con->Conectar() == true) {
            return pg_query("
(select 
pcs.ids,
split_part(pcs.pro_tipo, '&', 10) AS familia,
d.cod_producto,
d.lote,
pc.pro_t,
pc.pro_ab,
d.descripcion,
sum(d.cantidad) as cantidad,
sum(d.precio_total) as valor
from comprobantes c,
detalle_fact_notdeb_notcre d, 
erp_productos pc,
erp_productos_set pcs
where d.num_camprobante=replace(c.num_documento,'-','')
and d.cod_producto=pc.pro_a
and d.lote=pc.pro_ac
and pc.ids=pcs.ids
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=1
and char_length(d.lote)>3
$fml
$txt    
$linea
$talla
$val    
group by 
pcs.ids,
familia,
d.cod_producto,
d.lote,
pc.pro_t,
pc.pro_ab,
d.descripcion)
union
(select 
'0' AS ids,
'INDUSTRIALES' AS familia,
d.cod_producto,
'' AS lote, 
'' AS pro_t,
'' AS pro_ab,
d.descripcion,
sum(d.cantidad) as cantidad,
sum(d.precio_total) as valor
from comprobantes c,
detalle_fact_notdeb_notcre d, 
erp_i_productos pi
where d.num_camprobante=replace(c.num_documento,'-','')
and d.cod_producto=pi.pro_codigo
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=1
and char_length(d.lote)<3
$fml2
$txt    
$val
group by
d.cod_producto,
d.descripcion)
order by familia


 ");
        }
    }

    function lista_reporte_productos_totales($d, $h, $e, $cod, $lt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
sum(d.cantidad) as cantidad,
sum(d.precio_total) as valor
from comprobantes c,
detalle_fact_notdeb_notcre d, 
erp_productos pc,
erp_productos_set pcs
where d.num_camprobante=replace(c.num_documento,'-','')
and d.cod_producto=pc.pro_a
and d.lote=pc.pro_ac
and pc.ids=pcs.ids
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=1
and c.cod_punto_emision=$e
and d.cod_producto='$cod'
and d.lote='$lt'
");
        }
    }

    function lista_reporte_productos_totales_ind($d, $h, $e, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("
select 
sum(d.cantidad) as cantidad,
sum(d.precio_total) as valor
from comprobantes c,
detalle_fact_notdeb_notcre d, 
erp_i_productos pi
where d.num_camprobante=replace(c.num_documento,'-','')
and d.cod_producto=pi.pro_codigo
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=1
and c.cod_punto_emision=$e
and d.cod_producto='$cod'                
                ");
        }
    }

    function lista_reporte_productos_totales_general($d, $h, $e) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
sum(d.cantidad) as cantidad,
sum(d.precio_total) as valor
from comprobantes c,
detalle_fact_notdeb_notcre d, 
erp_productos pc,
erp_productos_set pcs
where d.num_camprobante=replace(c.num_documento,'-','')
and d.cod_producto=pc.pro_a
and d.lote=pc.pro_ac
and pc.ids=pcs.ids
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=1
and c.cod_punto_emision=$e
");
        }
    }

    function lista_familias() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT split_part(ps.pro_tipo, '&', 10) AS protipo ,ps.* FROM erp_productos_set ps order by protipo");
        }
    }

    function update_vendedores($vnd1, $vnd2) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes set vendedor='$vnd2' where vendedor = '$vnd1'   ");
        }
    }

    function lista_vnd_fact($vnd) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  comprobantes where vendedor='$vnd'  ");
        }
    }

    function lista_bancos_desgloce_tc($e, $d, $h, $v) {
        if ($this->con->Conectar() == true) {
            return pg_query("select pag_banco, 
CASE 
	WHEN pag_banco='1' THEN 'Banco Pichincha'
	WHEN pag_banco='2' THEN 'Banco del Pacífico'
	WHEN pag_banco='3' THEN 'Banco de Guayaquil'
	WHEN pag_banco='4' THEN 'Produbanco'
	WHEN pag_banco='5' THEN 'Banco Bolivariano'
	WHEN pag_banco='6' THEN 'Banco Internacional'
	WHEN pag_banco='7' THEN 'Banco del Austro'
	WHEN pag_banco='8' THEN 'Banco Promerica'
	WHEN pag_banco='9' THEN 'Banco de Machala'
	WHEN pag_banco='10' THEN 'BGR'
	WHEN pag_banco='11' THEN 'Citibank (Ecuador)'
	WHEN pag_banco='12' THEN 'Banco ProCredit (Ecuador)'
	WHEN pag_banco='13' THEN 'UniBanco'
	WHEN pag_banco='14' THEN 'Banco Solidario'
	WHEN pag_banco='15' THEN 'Banco de Loja'
	WHEN pag_banco='16' THEN 'Banco Territorial'
	WHEN pag_banco='17' THEN 'Banco Coopnacional'
	WHEN pag_banco='18' THEN 'Banco Amazonas'
	WHEN pag_banco='19' THEN 'Banco Capital'
	WHEN pag_banco='20' THEN 'Banco D-MIRO'
	WHEN pag_banco='21' THEN 'Banco Finca'
	WHEN pag_banco='22' THEN 'Banco Comercial de Manabí'
	WHEN pag_banco='23' THEN 'Banco COFIEC'
	WHEN pag_banco='24' THEN 'Banco del Litoral'
	WHEN pag_banco='25' THEN 'Banco Delbank'
	WHEN pag_banco='26' THEN 'Banco Sudamericano'
	ELSE 'Error'
END as banco
from erp_pagos_factura pf, comprobantes c
where pf.com_id=c.num_documento
and pag_forma='1'
and c.cod_punto_emision=$e
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and upper(c.vendedor) ='$v'
and pag_banco<>'0'
group by pag_banco

");
        }
    }

    ///////////////******INVENTARIOS***************************/////////////

    function lista_inventario_producto($h, $fml, $txt, $linea, $talla, $fml2, $val, $txt1) {
        if ($this->con->Conectar() == true) {
            return pg_query("
select 
pcs.ids,
split_part(pcs.pro_tipo, '&', 10) AS familia,
inv.pro_id,
inv.pro_tbl,
pc.pro_a,
pc.pro_b,
pc.pro_ac,
pc.pro_ab,
pc.pro_t,
pr.pre_precio
from 
erp_consulta_inv inv
join
erp_pro_precios pr on(
inv.pro_id=pr.pro_id
and inv.pro_tbl=pr.pro_tabla 
and inv.con_fecha='$h'
and inv.mvt_cant>0
and inv.pro_tbl=1
)
join erp_productos pc on(pc.id=inv.pro_id)
join erp_productos_set pcs on(
pc.ids=pcs.ids
$fml    
$txt
$linea
$talla
$val
)
group by 
pcs.ids,
pcs.pro_tipo,
inv.pro_id,
inv.pro_tbl,
pc.pro_a,
pc.pro_b,
pc.pro_ac,
pc.pro_ab,
pc.pro_t,
pr.pre_precio
union
select 
0 as ids,
'INDUSTRIAL' AS familia,
inv.pro_id,
inv.pro_tbl,
pc.pro_codigo,
pc.pro_descripcion,
'-' as pro_ac,
'' AS pro_ab,
'' AS pro_t,
pr.pre_precio
from 
erp_consulta_inv inv
join
erp_pro_precios pr on(
inv.pro_id=pr.pro_id
and inv.pro_tbl=pr.pro_tabla 
and inv.con_fecha='$h'
and inv.mvt_cant>0
and inv.pro_tbl=0
)
join erp_i_productos pc on(
pc.pro_id=inv.pro_id
$fml2
$txt1    
)
group by 
inv.pro_id,
inv.pro_tbl,
pc.pro_codigo,
pc.pro_descripcion,
pr.pre_precio
order by familia,pro_b

");
        }
    }

    function lista_inv_cant_prod($f, $p, $t, $pe) {
        if ($this->con->Conectar() == true) {
            return pg_query("
select 
inv.mvt_cant,
pr.pre_precio,
(inv.mvt_cant*pr.pre_precio) as valor 
from 
erp_consulta_inv inv
join erp_pro_precios pr
on(
pr.pro_id=inv.pro_id
and inv.con_fecha='2015-05-07'
and inv.pro_id=$p
and inv.pro_tbl=$t
and pr.pre_precio>0
and inv.cod_punto_emision=$pe)
limit 1

    ");
        }
    }

/////LISTAS INVENTARIOS 


    function lista_inv_costo_local($e, $tx1, $tx2, $ln, $tll) {
        if ($this->con->Conectar() == true) {
            return pg_query("
SELECT 
pcs.ids,
split_part(pcs.pro_tipo, '&', 10) AS familia,
pro.pro_a,
pro.pro_ac,
pro.pro_b,
pro.pro_ab,
pro.pro_t,
inv.mvt_cant,
(select pre_precio from erp_pro_precios pr where pr.pro_id=inv.pro_id and pr.pro_tabla=1 limit 1) as precio
FROM 
erp_i_movpt_total inv,
erp_productos pro,
erp_productos_set pcs
WHERE inv.pro_tbl=1
AND inv.pro_id=pro.id
AND pcs.ids=pro.ids 
AND inv.cod_punto_emision=$e
AND inv.mvt_cant >0
$tx1
$ln
$tll    
UNION
SELECT 
0,
'INDUSTRIAL',
pro.pro_codigo,
'0',
pro.pro_descripcion,
'0',
'0',
inv.mvt_cant,
(select pre_precio from erp_pro_precios pr where pr.pro_id=inv.pro_id and pr.pro_tabla=0 limit 1) as precio
FROM 
erp_i_movpt_total inv,
erp_i_productos pro
WHERE inv.pro_tbl=0
AND inv.pro_id=pro.pro_id
AND inv.cod_punto_emision=$e
AND inv.mvt_cant >0
$tx2
order by familia

    ");
        }
    }

    function lista_todos_productos() {
        if ($this->con->Conectar() == true) {
            return pg_query("select pro.pro_a,pro.pro_b,pro.pro_ac, 1 as tbl, id from erp_productos pro
                             union all
                             select pro.pro_codigo,pro.pro_descripcion,'0', 0 as tbl, pro_id from erp_i_productos pro");
        }
    }

    function lista_precios_producto($pro, $tbl) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_pro_precios where pro_id=$pro and pro_tabla=$tbl order by pre_id ");
        }
    }

    function elimina_duplicados_precios($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("
                delete from erp_descuentos where pre_id=$id;
                delete from erp_pro_precios where pre_id=$id;
                ");
        }
    }

///NUEVAS FUNCIONES DE REPORTES DE VENTAS POR PRODUCTOS


    function lista_reporte_ventas_productos() {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(prod,'&',1) as familia,
                            split_part(prod,'&',2) as cod,
                            split_part(prod,'&',3) as descr,
                            split_part(prod,'&',4) as lote,
                            to_char(cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as precio,
                            split_part(prod,'&',6) as ids,
                            split_part(prod,'&',7) as linea,
                            split_part(prod,'&',8) as talla,
                            loc1,
                            to_char(cast(loc1 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v1,
                            loc2,
                            to_char(cast(loc2 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v2,
                            loc3,
                            to_char(cast(loc3 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v3,
                            loc4,
                            to_char(cast(loc4 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v4,
                            loc5,
                            to_char(cast(loc5 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v5,
                            loc6,
                            to_char(cast(loc6 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v6,
                            loc7,
                            to_char(cast(loc7 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v7,
                            loc8,
                            to_char(cast(loc8 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v8,
                            loc9,
                            to_char(cast(loc9 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v9,
                            loc10,
                            to_char(cast(loc10 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v10,
                            loc11,
                            to_char(cast(loc11 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v11,
                            loc12,
                            to_char(cast(loc12 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v12,
                            loc13,
                            to_char(cast(loc13 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v13,
                            loc14,
                            to_char(cast(loc14 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v14
                            from ventas_producto where prod is not null ");
        }
    }

    ///////////////////////////////////////////////// ventas por productos
    //// nuevas tablas /////

    function lista_reporte_productos_locales($txt, $fml, $d, $h) {
        if ($this->con->Conectar() == TRUE) {
            return pg_query("select split_part(prod,'&',1) as familia,
                                    split_part(prod,'&',2) as cod,
                                    split_part(prod,'&',3) as descr,
                                    split_part(prod,'&',4) as lote,
                                    split_part(prod,'&',5) as ids,
                                    split_part(prod,'&',6) as fecha,
                                    to_char(cast(split_part(loc1,'&',1) as double precision),'99,999,990.00') as loc1,
                                    to_char(cast(split_part(loc1,'&',2) as double precision),'99,999,990.0000') as v1,
                                    to_char(cast(split_part(loc2,'&',1) as double precision),'99,999,990.00') as loc2,
                                    to_char(cast(split_part(loc2,'&',2) as double precision),'99,999,990.0000') as v2,
                                    to_char(cast(split_part(loc3,'&',1) as double precision),'99,999,990.00') as loc3,
                                    to_char(cast(split_part(loc3,'&',2) as double precision),'99,999,990.0000') as v3,
                                    to_char(cast(split_part(loc4,'&',1) as double precision),'99,999,990.00') as loc4,
                                    to_char(cast(split_part(loc4,'&',2) as double precision),'99,999,990.0000') as v4,
                                    to_char(cast(split_part(loc5,'&',1) as double precision),'99,999,990.00') as loc5,
                                    to_char(cast(split_part(loc5,'&',2) as double precision),'99,999,990.0000') as v5,
                                    to_char(cast(split_part(loc6,'&',1) as double precision),'99,999,990.00') as loc6,
                                    to_char(cast(split_part(loc6,'&',2) as double precision),'99,999,990.0000') as v6,
                                    to_char(cast(split_part(loc7,'&',1) as double precision),'99,999,990.00') as loc7,
                                    to_char(cast(split_part(loc7,'&',2) as double precision),'99,999,990.0000') as v7,
                                    to_char(cast(split_part(loc8,'&',1) as double precision),'99,999,990.00') as loc8,
                                    to_char(cast(split_part(loc8,'&',2) as double precision),'99,999,990.0000') as v8,
                                    to_char(cast(split_part(loc9,'&',1) as double precision),'99,999,990.00') as loc9,
                                    to_char(cast(split_part(loc9,'&',2) as double precision),'99,999,990.0000') as v9,
                                    to_char(cast(split_part(loc10,'&',1) as double precision),'99,999,990.00') as loc10,
                                    to_char(cast(split_part(loc10,'&',2) as double precision),'99,999,990.0000') as v10,
                                    to_char(cast(split_part(loc11,'&',1) as double precision),'99,999,990.00') as loc11,
                                    to_char(cast(split_part(loc11,'&',2) as double precision),'99,999,990.0000') as v11,
                                    to_char(cast(split_part(loc12,'&',1) as double precision),'99,999,990.00') as loc12,
                                    to_char(cast(split_part(loc12,'&',2) as double precision),'99,999,990.0000') as v12,
                                    to_char(cast(split_part(loc13,'&',1) as double precision),'99,999,990.00') as loc13,
                                    to_char(cast(split_part(loc13,'&',2) as double precision),'99,999,990.0000') as v13,
                                    to_char(cast(split_part(loc14,'&',1) as double precision),'99,999,990.00') as loc14,
                                    to_char(cast(split_part(loc14,'&',2) as double precision),'99,999,990.0000') as v14
                                from ventas_por_producto where prod is not null  
                                and split_part(prod,'&',6)>='$d' and split_part(prod,'&',6)<='$h'
                                $txt
                                $fml");
        }
    }

//    function lista_reporte_productos_locales(){
//        if($this->con->Conectar() == TRUE){
//            return pg_query(" SELECT crosstab.prod,
//    crosstab.loc1,
//    crosstab.loc2,
//    crosstab.loc3,
//    crosstab.loc4,
//    crosstab.loc5,
//    crosstab.loc6,
//    crosstab.loc7,
//    crosstab.loc8,
//    crosstab.loc9,
//    crosstab.loc10,
//    crosstab.loc11,
//    crosstab.loc12,
//    crosstab.loc13,
//    crosstab.loc14
//   FROM crosstab('
//select 
//pr.familia || ''&'' || pr.codigo || ''&'' || pr.desc || ''&'' || pr.lote || ''&'' || pr.ids || ''&'' || trim(pr.linea) || ''&'' || pr.talla as producto,
//com.cod_punto_emision as pt_e,
//coalesce(sum(det.cantidad),0) || ''&'' ||coalesce(sum(det.precio_total),0) as sumas
//from lista_productos pr
//join  detalle_fact_notdeb_notcre det on (det.cod_producto=pr.codigo 
//and det.lote=pr.lote 
//and pr.lote<>''-'' 
//and pr.estado=0)
//join  comprobantes com on (det.num_camprobante=replace(com.num_documento,''-'','''') 
//and com.tipo_comprobante=1
//and det.tipo_comprobante=1
//and com.fecha_emision>=20150601 
//and com.fecha_emision<=20150630 
//)
//group by pr.familia || ''&'' || pr.codigo || ''&'' || pr.desc || ''&'' || pr.lote || ''&'' || pr.ids || ''&'' || trim(pr.linea) || ''&'' || pr.talla,
//com.cod_punto_emision
//union
//select 
//pr.familia || ''&'' || pr.codigo || ''&'' || pr.desc || ''&'' || pr.lote || ''&'' || pr.ids || ''&'' || trim(pr.linea) || ''&'' || pr.talla as producto,
//com.cod_punto_emision as pt_e,
//coalesce(sum(det.cantidad),0) || ''&'' ||coalesce(sum(det.precio_total),0) as sumas
//from lista_productos pr
//join  detalle_fact_notdeb_notcre det on (det.cod_producto=pr.codigo 
//and pr.lote=''-'' 
//and  trim(det.lote)='''' 
//and pr.estado=0)
//join  comprobantes com on (det.num_camprobante=replace(com.num_documento,''-'','''') 
//and com.tipo_comprobante=1
//and det.tipo_comprobante=1
//and com.fecha_emision>=20150601 
//and com.fecha_emision<=20150630 
//)
//group by pr.familia || ''&'' || pr.codigo || ''&'' || pr.desc || ''&'' || pr.lote || ''&'' || pr.ids || ''&'' || trim(pr.linea) || ''&'' || pr.talla,
//com.cod_punto_emision
//order by pt_e,producto
//
//   '::text, 'select l from generate_series(1,14) l'::text) crosstab(prod text, loc1 text, loc2 text, loc3 text, loc4 text, loc5 text, loc6 text, loc7 text, loc8 text, loc9 text, loc10 text, loc11 text, loc12 text, loc13 text, loc14 text);
//
//
//");
//        }
//    }
////nuevas tablas ///

    function lista_bancos_desgloce_general_tc($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select pag_banco, 
CASE 
	WHEN pag_banco='1' THEN 'Banco Pichincha'
	WHEN pag_banco='2' THEN 'Banco del Pacífico'
	WHEN pag_banco='3' THEN 'Banco de Guayaquil'
	WHEN pag_banco='4' THEN 'Produbanco'
	WHEN pag_banco='5' THEN 'Banco Bolivariano'
	WHEN pag_banco='6' THEN 'Banco Internacional'
	WHEN pag_banco='7' THEN 'Banco del Austro'
	WHEN pag_banco='8' THEN 'Banco Promerica'
	WHEN pag_banco='9' THEN 'Banco de Machala'
	WHEN pag_banco='10' THEN 'BGR'
	WHEN pag_banco='11' THEN 'Citibank (Ecuador)'
	WHEN pag_banco='12' THEN 'Banco ProCredit (Ecuador)'
	WHEN pag_banco='13' THEN 'UniBanco'
	WHEN pag_banco='14' THEN 'Banco Solidario'
	WHEN pag_banco='15' THEN 'Banco de Loja'
	WHEN pag_banco='16' THEN 'Banco Territorial'
	WHEN pag_banco='17' THEN 'Banco Coopnacional'
	WHEN pag_banco='18' THEN 'Banco Amazonas'
	WHEN pag_banco='19' THEN 'Banco Capital'
	WHEN pag_banco='20' THEN 'Banco D-MIRO'
	WHEN pag_banco='21' THEN 'Banco Finca'
	WHEN pag_banco='22' THEN 'Banco Comercial de Manabí'
	WHEN pag_banco='23' THEN 'Banco COFIEC'
	WHEN pag_banco='24' THEN 'Banco del Litoral'
	WHEN pag_banco='25' THEN 'Banco Delbank'
	WHEN pag_banco='26' THEN 'Banco Sudamericano'
	ELSE 'Error'
END as banco
from erp_pagos_factura pf, erp_factura c
where pf.com_id=cast(c.fac_id as varchar)
and pag_forma='1'
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and pag_banco<>'0'
group by pag_banco
");
        }
    }

    function lista_tc_general($d, $h, $b) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
pf.pag_tarjeta,
CASE 
	WHEN pag_tarjeta='1' THEN 'VISA'
	WHEN pag_tarjeta='2' THEN 'MASTER CARD'
	WHEN pag_tarjeta='3' THEN 'AMERICAN EXPRESS'
	WHEN pag_tarjeta='4' THEN 'DINNERS'
	WHEN pag_tarjeta='5' THEN 'DISCOVER'
	WHEN pag_tarjeta='6' THEN 'CUOTAFACIL'
	ELSE 'Error'
END as targeta
from erp_pagos_factura pf, erp_factura c
where pf.com_id=cast(c.fac_id as varchar)
and pag_forma='1'
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and pf.pag_banco='$b'
and pf.pag_tarjeta<>'0'
group by pf.pag_tarjeta
ORDER BY targeta");
        }
    }

    function lista_pag_general($d, $h, $b, $tg) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
pf.pag_contado,
CASE 
	WHEN pf.pag_contado='1' THEN 'CONTADO'
	WHEN pf.pag_contado='2' THEN '3 MESES'
	WHEN pf.pag_contado='3' THEN '6 MESES'
	WHEN pf.pag_contado='4' THEN '9 MESES'
	WHEN pf.pag_contado='5' THEN '12 MESES'
	WHEN pf.pag_contado='6' THEN '18 MESES'
	WHEN pf.pag_contado='7' THEN '36 MESES'
	ELSE 'Error'
END as pago,
sum(pf.pag_cant)
from erp_pagos_factura pf, erp_factura c
where pf.com_id=cast(c.fac_id as varchar)
and pf.pag_forma='1'
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and pf.pag_banco='$b'
and pf.pag_tarjeta='$tg'
and pf.pag_contado is not null
group by pf.pag_contado");
        }
    }

    function lista_tot_tipo_pago($e, $d, $h, $v, $f) {
        if ($this->con->Conectar() == true) {
            return pg_query("select
sum(pf.pag_cant)
from erp_factura c,erp_pagos_factura pf
where cast(c.fac_id as varchar)=pf.com_id
and c.emi_id=$e
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and c.vnd_id ='$v'
and pf.pag_forma='$f'
");
        }
    }

    function lista_valor_targeta_contado($d, $h, $b, $t, $fp, $e, $v) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
sum(pf.pag_cant)
from erp_pagos_factura pf, erp_factura c
where pf.com_id=cast(c.fac_id as varchar)
and pf.pag_forma='1'
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and pf.pag_banco='$b'
and pf.pag_tarjeta='$t'
and pf.pag_contado='$fp'
and c.emi_id=$e
and c.vnd_id ='$v'
");
        }
    }

    function lista_vendedores($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_vendedores where vnd_id=$id ");
        }
    }

    function lista_reporte_ventas_productos_buscador($txt, $fml) {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(prod,'&',1) as familia,
                            split_part(prod,'&',2) as cod,
                            split_part(prod,'&',3) as descr,
                            split_part(prod,'&',4) as lote,
                            to_char(cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as precio,
                            split_part(prod,'&',6) as ids,
                            split_part(prod,'&',7) as linea,
                            split_part(prod,'&',8) as talla,
                            loc1,
                            to_char(cast(loc1 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v1,
                            loc2,
                            to_char(cast(loc2 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v2,
                            loc3,
                            to_char(cast(loc3 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v3,
                            loc4,
                            to_char(cast(loc4 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v4,
                            loc5,
                            to_char(cast(loc5 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v5,
                            loc6,
                            to_char(cast(loc6 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v6,
                            loc7,
                            to_char(cast(loc7 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v7,
                            loc8,
                            to_char(cast(loc8 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v8,
                            loc9,
                            to_char(cast(loc9 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v9,
                            loc10,
                            to_char(cast(loc10 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v10,
                            loc11,
                            to_char(cast(loc11 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v11,
                            loc12,
                            to_char(cast(loc12 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v12,
                            loc13,
                            to_char(cast(loc13 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v13,
                            loc14,
                            to_char(cast(loc14 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v14
                            from ventas_producto where prod is not null
                            $txt
                            $fml    
                             ");
        }
    }

    function lista_reporte_costos_productos_buscador($txt, $fml) {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(prod,'&',1) as familia,
                            split_part(prod,'&',2) as cod,
                            split_part(prod,'&',3) as descr,
                            split_part(prod,'&',4) as lote,
                            to_char(cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as precio,
                            split_part(prod,'&',6) as ids,
                            split_part(prod,'&',7) as linea,
                            split_part(prod,'&',8) as talla,
                            loc1,
                            to_char(cast(loc1 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v1,
                            loc2,
                            to_char(cast(loc2 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v2,
                            loc3,
                            to_char(cast(loc3 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v3,
                            loc4,
                            to_char(cast(loc4 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v4,
                            loc5,
                            to_char(cast(loc5 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v5,
                            loc6,
                            to_char(cast(loc6 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v6,
                            loc7,
                            to_char(cast(loc7 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v7,
                            loc8,
                            to_char(cast(loc8 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v8,
                            loc9,
                            to_char(cast(loc9 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v9,
                            loc10,
                            to_char(cast(loc10 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v10,
                            loc11,
                            to_char(cast(loc11 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v11,
                            loc12,
                            to_char(cast(loc12 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v12,
                            loc13,
                            to_char(cast(loc13 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v13,
                            loc14,
                            to_char(cast(loc14 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v14
                            from costos_producto where prod is not null
                            $txt
                            $fml    
                             ");
        }
    }

    function lista_reporte_ventas_diarias_buscador($desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(fecha, '&', 1) as fecha,
                                to_char(sum(cast(loc1 as double precision)),'99,999,990.00') as loc1,
                                to_char(sum(cast(loc2 as double precision)),'99,999,990.00') as loc2,
                                to_char(sum(cast(loc3 as double precision)),'99,999,990.00') as loc3,
                                to_char(sum(cast(loc4 as double precision)),'99,999,990.00') as loc4,
                                to_char(sum(cast(loc5 as double precision)),'99,999,990.00') as loc5,
                                to_char(sum(cast(loc6 as double precision)),'99,999,990.00') as loc6,
                                to_char(sum(cast(loc7 as double precision)),'99,999,990.00') as loc7,
                                to_char(sum(cast(loc8 as double precision)),'99,999,990.00') as loc8,
                                to_char(sum(cast(loc9 as double precision)),'99,999,990.00') as loc9,
                                to_char(sum(cast(loc10 as double precision)),'99,999,990.00') as loc10,
                                to_char(sum(cast(loc11 as double precision)),'99,999,990.00') as loc11,
                                to_char(sum(cast(loc12 as double precision)),'99,999,990.00') as loc12,
                                to_char(sum(cast(loc13 as double precision)),'99,999,990.00') as loc13,
                                to_char(sum(cast(loc14 as double precision)),'99,999,990.00') as loc14
                              from ventas_netas2 where split_part(fecha, '&', 1) between '$desde' and '$hasta' group by  split_part(fecha, '&', 1) order by split_part(fecha, '&', 1)
                                
                             ");
        }
    }

}

?>
