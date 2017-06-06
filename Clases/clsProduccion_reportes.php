<?php

include_once 'Conn.php';

class Produccion_reportes {

    var $con;

    function Produccion_reportes() {
        $this->con = new Conn();
    }

    function listaExtrusoras() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_maquinas where ids=1 order by maq_a");
        }
    }

    function listaCortadoras() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_maquinas where ids=2 order by maq_a");
        }
    }

    function lista_un_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_productos where pro_id='$id'");
        }
    }

    function rptExt1($date, $ext) {
        if ($this->con->Conectar() == true) {
            return pg_query("
                        SELECT op.ord_num_orden  FROM erp_reg_op_ecocambrella r_ext,erp_i_orden_produccion op, erp_maquinas m
                        WHERE r_ext.ord_id=op.ord_id
                        and r_ext.maq_id=m.id
                        AND r_ext.rec_fecha='$date' 
                        and r_ext.maq_id=$ext GROUP BY op.ord_num_orden");
        }
    }

    function rptExt01($date, $ext) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT op.ord_num_orden,op.ord_id,op.ord_pro_secundario,op.ord_pro3,op.ord_pro4 
                        FROM erp_reg_op_ecocambrella r_ext,erp_i_orden_produccion op, erp_maquinas m
                        WHERE r_ext.ord_id=op.ord_id
                        and r_ext.maq_id=m.id
                        AND r_ext.rec_fecha='$date'
                        and r_ext.maq_id=$ext
			group by op.ord_num_orden,op.ord_id,op.ord_pro_secundario,op.ord_pro3,op.ord_pro4 order by ord_num_orden");
        }
    }

    function rptExtrusion($date, $ext) {
        if ($this->con->Conectar() == true) {
            return pg_query(" SELECT op.ord_num_orden,
                           cl.cli_raz_social,
                           p.pro_id,     
                           p.pro_descripcion,
                           m.maq_a,
                           p.pro_espesor,
                           p.pro_ancho,
                           p.pro_gramaje
                        FROM erp_reg_op_ecocambrella r_ext,erp_i_orden_produccion op, erp_maquinas m,erp_i_productos p,erp_i_cliente cl
                        WHERE r_ext.ord_id=op.ord_id
                        and r_ext.maq_id=m.id
                        AND r_ext.rec_fecha='$date' 
                        and r_ext.maq_id=$ext
                        and r_ext.pro_id=p.pro_id
                        and op.cli_id=cl.cli_id
                        group by op.ord_num_orden,
                           cl.cli_raz_social,
                           p.pro_id,     
                           p.pro_descripcion,
                           m.maq_a,
                           p.pro_espesor,
                           p.pro_ancho,
                           p.pro_gramaje
                       ");
        }
    }

    function rptExtrusionFecha($date) {
        if ($this->con->Conectar() == true) {
            return pg_query(" SELECT op.ord_num_orden,
                           cl.cli_raz_social,
                           p.pro_id,     
                           p.pro_descripcion,
                           m.maq_a,
                           p.pro_espesor,
                           p.pro_ancho,
                           p.pro_gramaje,
                           sum(r_ext.rec_peso_primario)as peso
                        FROM erp_reg_op_ecocambrella r_ext,erp_i_orden_produccion op, erp_maquinas m,erp_i_productos p,erp_i_cliente cl
                        WHERE r_ext.ord_id=op.ord_id
                        and r_ext.maq_id=m.id
                        AND r_ext.rec_fecha='$date' 
                        and r_ext.pro_id=p.pro_id
                        and op.cli_id=cl.cli_id
                        group by op.ord_num_orden,
                           cl.cli_raz_social,
                           p.pro_id,     
                           p.pro_descripcion,
                           m.maq_a,
                           p.pro_espesor,
                           p.pro_ancho,
                           p.pro_gramaje
                      ");
        }
    }

    function rptExtrusionMaq($date) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT maq_id FROM erp_reg_op_ecocambrella r_ext WHERE r_ext.rec_fecha='$date' group by maq_id");
        }
    }
    
    function rptExtrusionEst($fec,$m,$op,$pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (SELECT sum(r_ext.rec_peso_primario)as peso FROM erp_reg_op_ecocambrella r_ext,erp_i_orden_produccion op 
                                    WHERE r_ext.ord_id=op.ord_id and r_ext.rec_fecha='$fec' and r_ext.maq_id=$m and rec_estado=0 and op.ord_num_orden='$op' and r_ext.pro_id=$pro) as conforme,
                                    (SELECT sum(r_ext.rec_peso_primario)as peso FROM erp_reg_op_ecocambrella r_ext,erp_i_orden_produccion op 
                                    WHERE r_ext.ord_id=op.ord_id and r_ext.rec_fecha='$fec' and r_ext.maq_id=$m and rec_estado=3 and op.ord_num_orden='$op' and r_ext.pro_id=$pro) as inconforme
                            ");
        }
    }

//    function rptExt2($date, $ext, $pedido, $turno, $pro_id) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT op.ord_num_orden,
//                           cl.cli_raz_social,
//                           p.pro_id,     
//                           p.pro_descripcion,
//                           m.maq_a,
//                           p.pro_espesor,
//                           p.pro_ancho,
//                           p.pro_gramaje,
//                           sum(r_ext.rec_peso_primario)as peso
//                    FROM erp_i_orden_produccion op,erp_i_cliente cl, erp_i_productos p, erp_reg_op_ecocambrella r_ext, erp_maquinas m
//                    WHERE op.cli_id=cl.cli_id
//                    and  r_ext.ord_id=op.ord_id
//                    and p.pro_id=op.pro_id
//                    and r_ext.pro_id=op.pro_id
//                    and r_ext.maq_id=m.id
//                    and  r_ext.rec_fecha='$date'	
//                    and  op.ord_num_orden='$pedido'
//                    and  r_ext.maq_id='$ext'
//                    group by op.ord_num_orden,
//                            p.pro_id,    
//                            cl.cli_raz_social,
//                            p.pro_descripcion,
//                            m.maq_a, 
//                            p.pro_espesor,
//                            p.pro_ancho,
//                            p.pro_gramaje");
//        }
//    }
//    function rptExt3($date, $ext, $pedido, $turno, $pro_id) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT op.ord_num_orden,
//                           cl.cli_raz_social,
//                           p.pro_id,     
//                           p.pro_descripcion,
//                            m.maq_a,
//                           p.pro_espesor,
//                           p.pro_ancho,
//                           p.pro_gramaje,
//                           sum(r_ext.rec_peso_primario)as peso
//                    FROM erp_i_orden_produccion op,erp_i_cliente cl, erp_i_productos p, erp_reg_op_ecocambrella r_ext, erp_maquinas m
//                    WHERE op.cli_id=cl.cli_id
//                    and  r_ext.ord_id=op.ord_id
//                    and p.pro_id=op.ord_pro_secundario
//                    and r_ext.pro_id=op.ord_pro_secundario
//                    and r_ext.maq_id=m.id
//                    and  r_ext.rec_fecha='$date'	
//                    and  op.ord_num_orden='$pedido'
//                    and  r_ext.maq_id='$ext'
//                    group by op.ord_num_orden,
//                            p.pro_id,    
//                            cl.cli_raz_social,
//                            p.pro_descripcion,
//                            m.maq_a, 
//                            p.pro_espesor,
//                            p.pro_ancho,
//                            p.pro_gramaje");
//        }
//    }
//
//    function rptExt4($date, $ext, $pedido, $turno, $pro_id) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT op.ord_num_orden,
//                           cl.cli_raz_social,
//                           p.pro_id,     
//                           p.pro_descripcion,
//                           m.maq_a,
//                           p.pro_espesor,
//                           p.pro_ancho,
//                           p.pro_gramaje,
//                           sum(r_ext.rec_peso_primario)as peso
//                    FROM erp_i_orden_produccion op,erp_i_cliente cl, erp_i_productos p, erp_reg_op_ecocambrella r_ext, erp_maquinas m
//                    WHERE op.cli_id=cl.cli_id
//                    and  r_ext.ord_id=op.ord_id
//                    and p.pro_id=op.ord_pro3
//                    and r_ext.pro_id=op.ord_pro3
//                    and r_ext.maq_id=m.id
//                    and  r_ext.rec_fecha='$date'	
//                    and  op.ord_num_orden='$pedido'
//                    and  r_ext.maq_id='$ext'
//                    group by op.ord_num_orden,
//                            p.pro_id,    
//                            cl.cli_raz_social,
//                            p.pro_descripcion,
//                            m.maq_a, 
//                            p.pro_espesor,
//                            p.pro_ancho,
//                            p.pro_gramaje");
//        }
//    }
//
//    function rptExt5($date, $ext, $pedido, $turno, $pro_id) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT op.ord_num_orden,
//                           cl.cli_raz_social,
//                           p.pro_id,     
//                           p.pro_descripcion,
//                           m.maq_a,
//                           p.pro_espesor,
//                           p.pro_ancho,
//                           p.pro_gramaje,
//                           sum(r_ext.rec_peso_primario)as peso
//                    FROM erp_i_orden_produccion op,erp_i_cliente cl, erp_i_productos p, erp_reg_op_ecocambrella r_ext, erp_maquinas m
//                    WHERE op.cli_id=cl.cli_id
//                    and  r_ext.ord_id=op.ord_id
//                    and p.pro_id=op.ord_pro4
//                    and r_ext.pro_id=op.ord_pro4
//                    and r_ext.maq_id=m.id
//                    and  r_ext.rec_fecha='$date'	
//                    and  op.ord_num_orden='$pedido'
//                   and  r_ext.maq_id='$ext'
//                    group by op.ord_num_orden,
//                            p.pro_id,    
//                            cl.cli_raz_social,
//                            p.pro_descripcion,
//                            m.maq_a, 
//                            p.pro_espesor,
//                            p.pro_ancho,
//                            p.pro_gramaje");
//        }
//    }

    function listaPedidosCodigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion op, erp_i_productos pr, erp_i_cliente cl
                                            WHERE op.cli_id=cl.cli_id
                                            AND   op.pro_id=pr.pro_id
                                            AND   op.ord_num_orden='$id' ORDER BY op.ord_fec_entrega,op.ord_num_orden ");
        }
    }

    //Reporte de Corte
    function rptSell1($date, $sell) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT op.opp_codigo  FROM erp_reg_op_padding r_sell,erp_i_orden_produccion_padding op, erp_maquinas m
                                            WHERE r_sell.opp_id=op.opp_id
                                            and r_sell.maq_id=m.id
                                            AND   r_sell.rpa_fecha='$date' and r_sell.maq_id=$sell GROUP BY op.opp_codigo");
        }
    }

    function rptSell2($date, $sell, $pedido, $turno) {
        if ($this->con->Conectar() == true) {
            return pg_query("
SELECT 
op.opp_codigo,
p.pro_uni,
p.pro_codigo,
cl.cli_raz_social,
p.pro_id,
m.maq_a,
p.pro_espesor,
p.pro_descripcion,
sum(r_sell.rpa_rollo) as rollos, 
sum(r_sell.rpa_peso) as peso
FROM erp_i_orden_produccion_padding op,erp_i_cliente cl,
erp_i_productos p, erp_maquinas m,
erp_reg_op_padding r_sell
	WHERE op.cli_id=cl.cli_id
	and  p.pro_id=op.pro_id
	and  r_sell.opp_id=op.opp_id
	and  r_sell.maq_id=m.id
	and  r_sell.rpa_fecha='$date'
	and  op.opp_codigo='$pedido'
        and  r_sell.maq_id=$sell
GROUP BY 
op.opp_codigo,
p.pro_uni,
p.pro_codigo,
cl.cli_raz_social,
p.pro_id,
m.maq_a,
p.pro_espesor,
p.pro_descripcion");
        }
    }

    function listaPedidosCodigoCorte($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_padding op, erp_i_productos pr, erp_i_cliente cl
                                            WHERE op.cli_id=cl.cli_id
                                            AND   op.pro_id=pr.pro_id
                                            AND   op.opp_codigo='$id' ORDER BY op.opp_fec_entrega,op.opp_codigo ");
        }
    }

    function listaExtrusionProduccionByDateSec($sec, $from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("select  sum(re.rec_peso_primario) from erp_reg_op_ecocambrella re, erp_maquinas m
                                        where re.maq_id=m.id and re.rec_fecha between '$from' and '$until'");
        }
    }

//Reporte Operativo de Extrusion

    function listarSumaEgresoMateriaPrimaFecha($sec, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(mov_cantidad) FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp
                                                    WHERE t.trs_id=m.trs_id and t.trs_operacion=1 and m.mp_id=mp.mp_id
                                                    and m.mov_fecha_trans between '$d' and '$h' and mp.mpt_id between 26 and 99");
        }
    }

    function listarSumaReciclado($sec, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(mov_cantidad) FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp
                                                    WHERE t.trs_id=m.trs_id and t.trs_operacion=0 and m.mp_id=mp.mp_id
                                                    and m.mov_fecha_trans between '$d' and '$h' and mp.mpt_id between 100 and 299");
        }
    }

    function listaExtrusionProduccionByDateMaq($id, $from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("select  sum(re.rec_peso_primario),sum(re.rec_peso_primario/p.pro_gramaje) as mts from erp_i_productos p, erp_reg_op_ecocambrella re, erp_maquinas m
                                        where p.pro_id=re.pro_id and re.maq_id=m.id and re.rec_fecha between '$from' and '$until' and re.maq_id=$id");
        }
    }

    function listDesperdicio($sec, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(mov_cantidad) FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp
                                                    WHERE t.trs_id=m.trs_id and t.trs_operacion=0 and m.mp_id=mp.mp_id
                                                    and m.mov_fecha_trans between '$d' and '$h' and mp.mpt_id between 300 and 399");
        }
    }

    ////reporte operativo cortadora

    function listaProduccionByDateSecTurno($from, $until, $sec) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(rs.rpa_rollo) as rollos,
                                        sum(rs.rpa_peso) as peso
                                        from erp_reg_op_padding rs, erp_maquinas m 
                                        where rs.maq_id=m.id
                                        and rs.rpa_fecha between '$from' and  '$until'
                                        ");
        }
    }

    function listaProduccionByDateMaq($from, $until, $maq) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(rpa_rollo) as rollos,
                                        sum(rpa_peso) as peso
                                        from erp_reg_op_padding 
                                        where rpa_fecha between '$from' and  '$until'
                                        and maq_id=$maq ");
        }
    }

    function listaSecciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones order by sec_gerencia desc,sec_area,sec_descricpion Asc ");
        }
    }

    function listaSeccionesDesc($desc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_nombre='$desc'");
        }
    }

    function listaSeccionesRecicladoras() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_id=12 or sec_id=1 or sec_id=7 order by sec_descricpion Asc ");
        }
    }

    function listaUnaSecciones($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_id=$id");
        }
    }

    function listaSeccionesPolietileno() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones
                                         where sec_gerencia='T' and sec_area='P' 
                                         and sec_id<>13 and sec_id<>15 and sec_id<>4 order by sec_descricpion  ");
        }
    }

    function listaSeccionGerenciasDivision($ger, $div) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_gerencia='$ger' and sec_area='$div' order by sec_descricpion Asc ");
        }
    }

    function lista_divisiones_gerencia($ger) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sec_area from par_secciones where sec_gerencia='$ger' group by sec_area ");
        }
    }

    function listaSecGerDivCaract($ger, $div, $crt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones 
                                         where sec_gerencia='$ger' 
                                         and sec_area='$div' 
                                         and sec_$crt='t'
                                         order by sec_codigo Asc ");
        }
    }

    function listaSeccionGerenciasDivisionExtrusoras() {
        if ($this->con->Conectar() == true) {
            return pg_query("select sc.sec_id,sc.sec_descricpion from par_secciones sc, par_extrusoras ex 
where sc.sec_id=ex.sec_id
and sc.sec_gerencia='T' and sc.sec_area='P' 
and sc.sec_id<>13 
group by sc.sec_descricpion,sc.sec_id order by sc.sec_descricpion ");
        }
    }

    function listaAllSeccion() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones order by sec_gerencia desc, sec_area desc  ");
        }
    }

    function listaSecByCaracteristica($sec, $crt, $crt1) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones 
                                        where sec_id<>$sec
                                        and (sec_$crt='t'
                                        or sec_$crt1='t') ");
        }
    }

    function listaSeccionGerencias($ger) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_gerencia='$ger' order by sec_codigo,sec_nombre  Asc ");
        }
    }

    function insertSec($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO par_secciones(sec_descricpion,
                                                                  sec_codigo,
                                                                  sec_area,
                                                                  sec_gerencia, 
                                                                  sec_nombre,
                                                                  sec_ext,
                                                                  sec_imp,
                                                                  sec_sel,
                                                                  sec_alm,
                                                                  sec_adm,
                                                                  sec_opr)
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
                                                                '$data[10]') ");
        }
    }

    function updateSec($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query(" update par_secciones set sec_descricpion='$data[0]',
                                                                  sec_codigo='$data[1]',
                                                                  sec_area='$data[2]',
                                                                  sec_gerencia='$data[3]', 
                                                                  sec_nombre='$data[4]',
                                                                  sec_ext='$data[5]',
                                                                  sec_imp='$data[6]',
                                                                  sec_sel='$data[7]',
                                                                  sec_alm='$data[8]',
                                                                  sec_adm='$data[9]',
                                                                  sec_opr='$data[10]' WHERE sec_id=$id ");
        }
    }

    function deleteSec() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones order by sec_gerencia desc, sec_area desc  ");
        }
    }

    function lista_divisiones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_division");
        }
    }

    function lista_una_division($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_division WHERE div_id=$id");
        }
    }

    function lista_una_gerencia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_gerencia WHERE ger_id=$id");
        }
    }

    function listaExtrusionProduccionByDateMaqEst($id, $from, $until, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("select  sum(re.rec_peso_primario) as pbruto,sum(re.rec_peso_primario-pro_propiedad5) as pneto,sum(re.rec_peso_primario/p.pro_gramaje) as mts_neto,sum(re.rec_peso_primario-pro_propiedad5/p.pro_gramaje) as mts_bruto from erp_i_productos p, erp_reg_op_ecocambrella re, erp_maquinas m
                                        where p.pro_id=re.pro_id and re.maq_id=m.id and re.rec_fecha between '$from' and '$until' and re.maq_id=$id and rec_estado='$std'");
        }
    }

    function listaProduccionByDateMaqEst($from, $until, $maq, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(rpa_rollo) as rollos,sum(r.rpa_peso-p.pro_capa) as pneto,sum(r.rpa_peso) as pbruto from erp_reg_op_padding r, erp_i_productos p, erp_i_orden_produccion_padding o 
                                        where o.pro_id=p.pro_id and o.opp_id=r.opp_id and r.rpa_fecha between '$from' and  '$until' and r.maq_id=$maq and r.rpa_estado='$std'");
        }
    }

    function listaReporteMP() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mp m, erp_i_tpmp t where m.mpt_id=t.mpt_id order by mpt_nombre");
        }
    }

    function listarSumaEgresoMateriaPrimaId($id, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(mov_cantidad) FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp
                                                    WHERE t.trs_id=m.trs_id and t.trs_operacion=1 and m.mp_id=mp.mp_id
                                                    and m.mov_fecha_trans between '$d' and '$h' 
                                                    and mp.mp_id=$id        
                                                    ");
        }
    }

    function listaSumaTotalIMPbyDateSec($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(mov_cantidad) FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp
                                                    WHERE t.trs_id=m.trs_id and t.trs_operacion=1 and m.mp_id=mp.mp_id
                                                    and m.mov_fecha_trans between '$d' and '$h'");
        }
    }

    function listaSumIngMPbydatetoSec($d, $h, $t) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(mov_cantidad) FROM erp_i_mov_inventario m, erp_transacciones t, erp_i_mp mp, erp_i_tpmp  tp
                                                    WHERE t.trs_id=m.trs_id and t.trs_operacion=1 and m.mp_id=mp.mp_id and tp.mpt_id=mp.mpt_id 
                                                    and tp.mpt_id=$t and m.mov_fecha_trans between '$d' and '$h'");
        }
    }

}

?>
