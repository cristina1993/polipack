<?php

include_once 'Conn.php';

class CuentasPagar {

    var $con;

    function CuentasPagar() {
        $this->con = new Conn();
    }

    function lista_configuraciones_gen($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones where con_id=$id");
        }
    } 
    
    function lista_documentos_buscador($nm) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_documentos c  $nm order by reg_ruc_cliente,reg_num_documento");
        }
    }

    function lista_cliente_ruc($ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where cli_ced_ruc='$ruc' ");
        }
    }

    function listar_una_ctapagar_ctpid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxpagar where ctp_id=$id");
        }
    }

    function listar_una_ctapagar_pagid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxpagar where pag_id=$id");
        }
    }

    function lista_documentos_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_documentos WHERE reg_id=$id");
        }
    }

    function lista_cliente_ced($id) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM erp_i_cliente WHERE (cli_ced_ruc='$id' or cli_codigo='$id') and (cli_tipo = '1' or cli_tipo = '2')");
            return pg_query("SELECT * FROM erp_i_cliente WHERE (cli_ced_ruc='$id' or cli_codigo='$id')");
        }
    }

    function lista_asientos_contables() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas where character_length (pln_codigo) = 14 and pln_id between 1054 and 1060
                    union SELECT * FROM erp_plan_cuentas where character_length (pln_codigo) = 14 and pln_id between 1172 and 1193 order by pln_codigo");
        }
    }

    function lista_cuentas_bancos() {
        if ($this->con->Conectar() == true) {
            return pg_query(" SELECT * FROM erp_plan_cuentas where character_length (pln_codigo) = 14");
        }
    }

    function listar_un_asiento($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where pln_codigo='$cod'");
        }
    }

    function listar_una_cuenta_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas where pln_id=$id");
        }
    }

    function insert_ctasxpagar($data, $pln) {
        if ($this->con->Conectar() == true) {
            return pg_query("
                INSERT INTO erp_ctasxpagar(
                reg_id, 
                ctp_fecha, 
                ctp_monto, 
                ctp_forma_pago, 
                ctp_banco,
                pln_id,
                ctp_fecha_pago,
                pag_id,
                num_documento,
                ctp_concepto,
                asiento)
        VALUES ( $data[0],
                '$data[1]',
                '$data[2]',
                '$data[3]',
                '$data[4]',
                 $pln,
                '$data[6]',
                 $data[7],
                '$data[8]',
                '$data[11]',
                '$data[12]')");
        }
    }

    function insert_ctasxcobrar($data, $pln, $pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_ctasxcobrar(com_id, cta_fecha, cta_monto, cta_forma_pago, cta_banco,pln_id,cta_fecha_pago,pag_id,num_documento,cta_concepto,asiento)
        VALUES ($data[10],'$data[1]','$data[2]','$data[3]','$data[4]',$pln,'$data[6]',$pag,'$data[9]','$data[11]','$data[12]')");
        }
    }

    function suma_pagos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(ctp_monto) from erp_ctasxpagar where reg_id=$id) as monto,
                                    (select sum(pag_valor) from erp_pagos_documentos where reg_id=$id) as pago");
        }
    }

    function suma_pagid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(ctp_monto) as cred FROM  erp_ctasxpagar where pag_id=$id");
        }
    }

    function buscar_un_documento($id, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_factura WHERE fac_numero='$id' and fac_identificacion='$ruc'");
        }
    }

    function buscar_documentos_vencidos($act, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.com_id,c.nombre,c.identificacion,c.fecha_emision, c.total_valor,c.num_documento FROM  comprobantes c, erp_pagos_factura p where c.num_documento=p.com_id  and c.tipo_comprobante=1 and(cod_punto_emision=1 or cod_punto_emision=10) and p.pag_fecha_v < '$act' and c.fecha_emision between $fec1 and $fec2 and (c.total_valor>(select sum(ct.cta_monto) from erp_ctasxcobrar ct where c.com_id=ct.com_id ) or not exists(select * from erp_ctasxcobrar ct where c.com_id=ct.com_id )) group by c.com_id,c.nombre,c.identificacion,c.fecha_emision, c.total_valor,c.num_documento order by c.num_documento");
        }
    }

    function buscar_documentos_vencer($act, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.com_id,c.nombre,c.identificacion,c.fecha_emision, c.total_valor,c.num_documento FROM  comprobantes c, erp_pagos_factura p where c.num_documento=p.com_id  and c.tipo_comprobante=1 and(cod_punto_emision=1 or cod_punto_emision=10) and p.pag_fecha_v > '$act' and c.fecha_emision between $fec1 and $fec2 and (c.total_valor>(select sum(ct.cta_monto) from erp_ctasxcobrar ct where c.com_id=ct.com_id ) or not exists(select * from erp_ctasxcobrar ct where c.com_id=ct.com_id ))group by c.com_id,c.nombre,c.identificacion,c.fecha_emision, c.total_valor,c.num_documento order by c.num_documento ");
        }
    }

    function lista_pago_vencer($id, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_DOCUMENTOS WHERE pag_id=$id and reg_id=$num");
        }
    }

    function buscar_un_pago($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura p WHERE p.com_id='$id' and not exists(SELECT * FROM erp_ctasxcobrar c where c.pag_id=p.pag_id)");
        }
    }

    function insert_asientosp($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into 
                erp_asientos_contables
                (con_asiento, 
                con_concepto, 
                con_documento, 
                con_fecha_emision,
                con_concepto_debe, 
                con_concepto_haber, 
                con_valor_debe, 
                con_valor_haber,
                con_estado)
        VALUES ('$data[0]',
                '$data[1]',
                '$data[2]',
                '$data[3]',
                '$data[4]',
                '$data[5]',
                '$data[6]',
                '$data[7]',
                '$data[8]')");
        }
    }

    function ultimo_asientop() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    function siguiente_asientop() {
        if ($this->con->Conectar() == true) {
            $rst = pg_fetch_array($this->ultimo_asientop());
            if (!empty($rst)) {
                $sec = (substr($rst[con_asiento], -10) + 1);
                $n_sec = 'AS' . substr($rst[con_asiento], 2, (10 - strlen($sec))) . $sec;
            } else {
                $n_sec = 'AS0000000001';
            }
            return $n_sec;
        }
    }

    function buscar_un_pago_doc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_documentos p WHERE reg_id='$id' and not exists(SELECT * FROM erp_ctasxpagar c where c.pag_id=p.pag_id) order by p.pag_id");
        }
    }

    function lista_estado_cuenta_cliente($txt, $f1, $f2) {
        if ($this->con->Conectar() == true) {
            return pg_query("select c.reg_id,c.reg_femision, c.reg_num_documento,('FACTURA COMPRA') as concepto,('') as forma,cast('0' as double precision) as debe ,c.reg_total from erp_reg_documentos c where c.reg_ruc_cliente='$txt' and exists(select * from erp_pagos_documentos p where p.reg_id= c.reg_id) and reg_femision between '$f1' and '$f2' 
                           union
select c.reg_id,ctp.ctp_fecha_pago , c.reg_num_documento,ctp.ctp_concepto,ctp_forma_pago, ctp.ctp_monto,('0') as reg_total  from erp_ctasxpagar ctp,erp_reg_documentos c where c.reg_id=ctp.reg_id and c.reg_ruc_cliente='$txt' and ctp.ctp_fecha_pago between '$f1' and '$f2' and ctp.ctp_forma_pago<>'NOTA DE DEBITO'
			   union
select c.reg_id,ctp.ctp_fecha_pago , c.reg_num_documento,ctp.ctp_concepto,ctp_forma_pago,('0') as debe,ctp_monto as reg_total  from erp_ctasxpagar ctp,erp_reg_documentos c where c.reg_id=ctp.reg_id and c.reg_ruc_cliente='$txt' and ctp.ctp_fecha_pago between '$f1' and '$f2' and ctp.ctp_forma_pago='NOTA DE DEBITO'
order by reg_femision, concepto");
                
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where identificacion='$id'");
        }
    }

    function saldo_anterior($txt, $fec1) {
        if ($this->con->Conectar() == true) {
            $f1 = str_replace('-', '', $fec1);
            return pg_query("select(select sum(c.reg_total) from erp_reg_documentos c where c.reg_ruc_cliente='$txt' and exists(select * from erp_pagos_documentos p where p.reg_id= c.reg_id) and c.reg_femision <'$fec1') as credito, 
                                    (select sum (ctp.ctp_monto) from erp_ctasxpagar ctp, erp_reg_documentos c where ctp.reg_id=c.reg_id and c.reg_ruc_cliente='$txt'  and ctp.ctp_fecha_pago <'$fec1') as debito");
        }
    }

    function listar_una_ctapagar_comid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxpagar where reg_id=$id");
        }
    }

    function lista_pagos_regfac($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_documentos WHERE reg_id='$id' order by pag_id $txt");
        }
    }

//////////********PAGO PROVEEDORES***********/////////

    function lista_pagos_por_vencer($today, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_documentos rd,
                             erp_pagos_documentos pd
                             where pd.reg_id=rd.reg_id
                             and pd.pag_fecha_v >='$today' 
                             and rd.reg_ruc_cliente='$ruc'
                             and rd.reg_estado<3");
        }
    }

   /// cambios para notas de debito
    function lista_pagos_documento($reg_id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(ctp_monto) from erp_ctasxpagar where reg_id=$reg_id and ctp_forma_pago<>'NOTA DE DEBITO' ");
        }
    }
    
    function lista_pagos_ndebito($reg_id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(ctp_monto) as debito from erp_ctasxpagar where reg_id=$reg_id and ctp_forma_pago='NOTA DE DEBITO' ");
        }
    }

    function lista_pagos_vencidos($today, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_documentos rd,
                             erp_pagos_documentos pd
                             where pd.reg_id=rd.reg_id
                             and pd.pag_fecha_v <'$today'
                             and rd.reg_ruc_cliente='$ruc' 
                             and rd.reg_estado<3");
        }
    }

    function lista_proveedores() {
        if ($this->con->Conectar() == true) {
            return pg_query("select cl.cli_ced_ruc,cl.cli_raz_social from 
erp_reg_documentos doc,
erp_i_cliente cl
where cl.cli_ced_ruc=doc.reg_ruc_cliente
group by cl.cli_ced_ruc,cl.cli_raz_social order by cl.cli_raz_social ");
        }
    }

    function lista_secuencial_obligaciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_obligacion_pago order by obl_codigo desc limit 1");
        }
    }

    function lista_obligaciones_reg_id($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select op.obl_codigo,
rd.reg_ruc_cliente,
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
sum(obl_cantidad) ,
op.obl_num_egreso
from erp_obligacion_pago op,
erp_pagos_documentos pd,
erp_reg_documentos rd   
where op.pag_id=pd.pag_id
and pd.reg_id=rd.reg_id
and (op.obl_estado_obligacion=1 or op.obl_estado_obligacion=3)
and op.obl_codigo='$cod'
group by 
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_codigo,
rd.reg_ruc_cliente,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
op.obl_num_egreso
order by op.obl_codigo desc ");
        }
    }

    function lista_obligaciones_reg_egr($cod, $egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("select op.obl_codigo,
rd.reg_ruc_cliente,
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
sum(obl_cantidad) ,
op.obl_num_egreso
from erp_obligacion_pago op,
erp_pagos_documentos pd,
erp_reg_documentos rd   
where op.pag_id=pd.pag_id
and pd.reg_id=rd.reg_id
and (op.obl_estado_obligacion=1 or op.obl_estado_obligacion=3)
and op.obl_codigo='$cod' and op.obl_num_egreso='$egr'
group by 
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_codigo,
rd.reg_ruc_cliente,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
op.obl_num_egreso
order by op.obl_codigo desc ");
        }
    }

    function inser_pago_obligaciones($dat) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_obligacion_pago(
            pag_id,
            obl_codigo,
            obl_cantidad,
            obl_estado
            )
    VALUES ($dat[0],
           '$dat[1]',
            $dat[2],
            $dat[3] ) ");
        }
    }

    function cambia_estado_obligaciones($sts, $id, $cnt, $egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_obligacion_pago SET obl_estado_obligacion=$sts ,obl_cantidad=$cnt,obl_num_egreso='$egr'  where obl_id=$id");
        }
    }

    function lista_estado_obligacion_pago($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_obligacion_pago WHERE pag_id=$id and obl_estado_obligacion<>2"); ///Si esta registrado y no esta rechazado
        }
    }

    function lista_obl_cod($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT 
op.obl_codigo,
op.obl_estado,
op.obl_estado_obligacion,
pg.reg_id,
pg.pag_id,
pg.pag_fecha_v,
op.obl_cantidad,
rd.reg_num_documento 
FROM  
erp_obligacion_pago op,
erp_pagos_documentos pg,
erp_reg_documentos rd
where op.pag_id=pg.pag_id 
and rd.reg_id=pg.reg_id
and op.obl_codigo='$cod'
and op.obl_estado_obligacion='1'
"); ///Si esta registrado y no esta rechazado
        }
    }

    function cambia_estado_obligacion_pago($sts, $cod, $fecha_pag, $forma_pago, $conepto, $documento, $cuenta, $n_egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_obligacion_pago 
                SET obl_estado_obligacion=$sts,
                    obl_fecha_pago='$fecha_pag',
                    obl_forma_pago='$forma_pago',
                    obl_concepto='$conepto',
                    obl_doc='$documento',
                    obl_cuenta='$cuenta',
                    obl_num_egreso='$n_egr'    
                    where obl_codigo='$cod' and obl_estado_obligacion=1 ");
        }
    }

    function lista_obligacion_pago($cod, $fecha) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_obligacion_pago op,
                            erp_pagos_documentos pd
                            where pd.pag_id=op.pag_id
                            and op.obl_codigo='$cod'
                            and op.obl_fecha_pago='$fecha' ");
        }
    }

    function lista_asiento_codigo($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select *
from erp_asientos_contables ac,
erp_plan_cuentas pc
where ac.con_concepto_haber=pc.pln_codigo
and ac.con_asiento='$cod'
and ac.con_concepto_haber<>''  ");
        }
    }

    function lista_obligacion_pago_datos($cod, $fecha) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(obl_cantidad),obl_codigo,obl_fecha_pago,obl_forma_pago,obl_concepto,obl_doc from erp_obligacion_pago
                            where pag_id=pag_id
                            and obl_codigo=trim('$cod')
                            and obl_fecha_pago='$fecha'
                            and obl_estado_obligacion=3
                            group by obl_codigo,obl_fecha_pago,obl_forma_pago,obl_concepto,obl_doc");
        }
    }

    function lista_obligacion_pago_datos_egr($cod, $fecha, $egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(obl_cantidad),obl_codigo,obl_fecha_pago,obl_forma_pago,obl_concepto,obl_doc from erp_obligacion_pago
                            where pag_id=pag_id
                            and obl_codigo=trim('$cod')
                            and obl_fecha_pago='$fecha'
                            and obl_num_egreso='$egr'
                            and obl_estado_obligacion=3
                            group by obl_codigo,obl_fecha_pago,obl_forma_pago,obl_concepto,obl_doc");
        }
    }

    function lista_debe_factura($reg_id, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(op.obl_cantidad) from erp_obligacion_pago op,
erp_pagos_documentos pd
where op.pag_id=pd.pag_id
and pd.reg_id=$reg_id
and op.obl_codigo='$cod'    
                    ");
        }
    }

    function lista_debe_factura_egr($reg_id, $cod, $egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(op.obl_cantidad) from erp_obligacion_pago op,
erp_pagos_documentos pd
where op.pag_id=pd.pag_id
and pd.reg_id=$reg_id
and op.obl_codigo='$cod'
and op.obl_num_egreso='$egr'
                    ");
        }
    }

//    function lista_asientos_obligacion_pago($cod) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select ac.con_concepto,
//ac.con_documento,
//ac.con_fecha_emision,
//ac.con_asiento,
//op.obl_codigo,
//rd.reg_ruc_cliente,
//ac.con_concepto_debe,
//ac.con_concepto_haber,
//ac.con_valor_debe,
//ac.con_valor_haber,
//pc.pln_descripcion,
//rd.con_asiento
//from 
//erp_obligacion_pago op,
//erp_asientos_contables ac,
//erp_plan_cuentas pc,
//erp_pagos_documentos pg,
//erp_reg_documentos rd
//where op.pag_id=ac.doc_id
//and trim(ac.con_concepto_debe)=trim(pc.pln_codigo)
//and op.obl_codigo='$cod'
//and op.obl_estado_obligacion=3
//and pg.pag_id=op.pag_id
//and rd.reg_id=pg.reg_id
//order by ac.con_fecha_emision,
//ac.con_documento
//");
//        }
//    }

    function lista_asientos_obligacion_pago($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pd.reg_id,
rd.reg_num_documento,
rd.con_asiento,
rd.reg_ruc_cliente
FROM erp_obligacion_pago op,
erp_pagos_documentos pd,
erp_reg_documentos rd,
erp_asientos_contables ac
where pd.pag_id=op.pag_id
and rd.reg_id=pd.reg_id
and op.obl_codigo='$cod'
and op.obl_estado_obligacion=3
group by pd.reg_id,
rd.reg_num_documento,
rd.con_asiento,
rd.reg_ruc_cliente
");
        }
    }

    function lista_asientos_obligacion_pago_egr($cod, $egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pd.reg_id,
rd.reg_num_documento,
rd.con_asiento,
rd.reg_ruc_cliente
FROM erp_obligacion_pago op,
erp_pagos_documentos pd,
erp_reg_documentos rd,
erp_asientos_contables ac
where pd.pag_id=op.pag_id
and rd.reg_id=pd.reg_id
and op.obl_codigo='$cod' and op.obl_num_egreso='$egr' 
and op.obl_estado_obligacion=3
group by pd.reg_id,
rd.reg_num_documento,
rd.con_asiento,
rd.reg_ruc_cliente
");
        }
    }

//    function lista_asientos_obligacion_pago($cod) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT pd.reg_id,
//rd.reg_num_documento,
//rd.con_asiento,
//rd.reg_ruc_cliente
//FROM erp_obligacion_pago op,
//erp_pagos_documentos pd,
//erp_reg_documentos rd,
//erp_asientos_contables ac
//where pd.pag_id=op.pag_id
//and rd.reg_id=pd.reg_id
//and op.obl_codigo='$cod'
//and op.obl_estado_obligacion=3
//group by pd.reg_id,
//rd.reg_num_documento,
//rd.con_asiento,
//rd.reg_ruc_cliente
//union
//SELECT '0' as reg_id,
//op.con_asiento,
//op.con_asiento,
//c.cli_ced_ruc
//FROM erp_obligacion_pago op,
//erp_i_cliente c
//where op.cli_id=c.cli_id
//and op.obl_codigo='$cod'
//and op.obl_estado_obligacion=3
//group by op.con_asiento,
//c.cli_ced_ruc
//");
//        }
//    }

    function insert_asientosp2($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into 
                erp_asientos_contables
                (con_asiento, 
                con_concepto, 
                con_documento, 
                con_fecha_emision,
                con_concepto_debe, 
                con_concepto_haber, 
                con_valor_debe, 
                con_valor_haber,
                con_estado,
                doc_id
                )
        VALUES ('$data[0]',
                '$data[1]',
                '$data[2]',
                '$data[3]',
                '$data[4]',
                '$data[5]',
                '$data[6]',
                '$data[7]',
                '$data[8]',$data[9])");
        }
    }

    //////////ENERO 2016

    function lista_ultimo_num_egreso() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_obligacion_pago where obl_num_egreso is not null order by obl_num_egreso desc limit 1");
        }
    }

    function lista_una_cuenta_bancos($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_bancos_y_cajas cb,
                            erp_plan_cuentas pc
                            where pc.pln_codigo=cb.byc_cuenta_contable
                            and cb.byc_cuenta_contable='$cod'");
        }
    }

    /////////cambios  bancos y cajas /////
//    function lista_obligaciones_pago($ruc) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM 
//                                erp_obligacion_pago op,
//                                erp_pagos_documentos pd,
//                                erp_reg_documentos rg
//                                where pd.pag_id=op.pag_id
//                                and rg.reg_id=pd.reg_id
//                                and op.obl_estado_obligacion<>3
//                                and rg.reg_ruc_cliente like '%$ruc%'
//                                    order by op.obl_codigo
//                                ");
//        }
//    }
//    

    function lista_obligaciones_pago($ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT op.*,rg.reg_ruc_cliente,rg.reg_num_documento,rg.reg_concepto,rg.reg_femision,rg.reg_id,pd.pag_valor,pd.pag_fecha_v FROM 
                                erp_obligacion_pago op,
                                erp_pagos_documentos pd,
                                erp_reg_documentos rg
                                where pd.pag_id=op.pag_id
                                and rg.reg_id=pd.reg_id
                                and op.obl_estado_obligacion<>3
                                and rg.reg_ruc_cliente like '%$ruc%'
                                union 
                            SELECT op.*, c.cli_ced_ruc as reg_ruc_cliente, op.con_asiento as reg_num_documento, op.obl_concepto as reg_concepto, op.obl_fecha_pago as reg_femision,'0' as reg_id,obl_cantidad as pag_valor,op.obl_fecha_pago as pag_fecha_v FROM 
                                erp_obligacion_pago op,erp_i_cliente c
                                where op.cli_id=c.cli_id
                                and op.obl_estado_obligacion<>3
                                and c.cli_ced_ruc like '%$ruc%'
                                    order by obl_codigo
                                ");
        }
    }

//    function lista_pagos_aprobados() {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select op.obl_codigo,
//rd.reg_ruc_cliente,
//op.obl_estado_obligacion,
//op.obl_fecha_pago,
//op.obl_forma_pago,
//op.obl_concepto,
//op.obl_doc,
//op.obl_cuenta,
//sum(obl_cantidad) 
//from erp_obligacion_pago op,
//erp_pagos_documentos pd,
//erp_reg_documentos rd   
//where op.pag_id=pd.pag_id
//and pd.reg_id=rd.reg_id
//and (op.obl_estado_obligacion=1 or op.obl_estado_obligacion=3)
//group by 
//op.obl_estado_obligacion,
//op.obl_fecha_pago,
//op.obl_codigo,
//rd.reg_ruc_cliente,
//op.obl_forma_pago,
//op.obl_concepto,
//op.obl_doc,
//op.obl_cuenta order by op.obl_codigo desc");
//        }
//    }

    function lista_pagos_aprobados() {
        if ($this->con->Conectar() == true) {
            return pg_query("select op.obl_codigo,
rd.reg_ruc_cliente,
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
sum(obl_cantidad),
op.obl_tipo,
op.obl_num_egreso
from erp_obligacion_pago op,
erp_pagos_documentos pd,
erp_reg_documentos rd   
where op.pag_id=pd.pag_id
and pd.reg_id=rd.reg_id
and (op.obl_estado_obligacion=1 or op.obl_estado_obligacion=3)
group by 
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_codigo,
rd.reg_ruc_cliente,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
op.obl_tipo,
op.obl_num_egreso
UNION 
select op.obl_codigo,
c.cli_ced_ruc as reg_ruc_cliente,
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
sum(obl_cantidad),
op.obl_tipo,
op.obl_num_egreso
from erp_obligacion_pago op,
erp_i_cliente c
where op.cli_id = c.cli_id
and (op.obl_estado_obligacion=1 or op.obl_estado_obligacion=3)
group by 
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_codigo,
c.cli_ced_ruc,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
op.obl_tipo,
op.obl_num_egreso
order by obl_codigo desc ");
        }
    }

    function lista_obligacion_pago_datos_banca($cod, $fecha) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(obl_cantidad),obl_codigo,obl_fecha_pago,obl_forma_pago,obl_concepto,obl_doc from erp_obligacion_pago
                            where pag_id=pag_id
                            and obl_codigo=trim('$cod')
                            and obl_fecha_pago='$fecha'
                            and obl_estado_obligacion=3
                            group by obl_codigo,obl_fecha_pago,obl_forma_pago,obl_concepto,obl_doc");
        }
    }

    function lista_un_cliente_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where cli_id='$id' ");
        }
    }

    function lista_una_obligacion_cod($id, $doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_obligacion_pago where obl_codigo='$id' and obl_doc='$doc' ");
        }
    }

    function lista_obligacion_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_obligacion_pago where obl_codigo='$id'");
        }
    }

    function lista_un_asiento($as) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT con_id, con_asiento,con_documento,con_concepto,con_fecha_emision,con_concepto_debe as concepto, con_valor_debe as valor,con_estado,con_tipo,doc_id , '0'  as tipo FROM  erp_asientos_contables where  char_length(trim(con_concepto_debe))<>0 and con_asiento='$as'
                             union 
                             SELECT con_id, con_asiento,con_documento,con_concepto,con_fecha_emision,con_concepto_haber as concepto, con_valor_haber as valor,con_estado,con_tipo,doc_id , '1'  as tipo FROM  erp_asientos_contables where  char_length(trim(con_concepto_haber))<>0 and con_asiento='$as'
                             order by con_id");
        }
    }

    function lista_un_plan_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_plan_cuentas where pln_codigo='$id' ");
        }
    }

    function delete_asientos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables WHERE con_asiento='$id'");
        }
    }

    function delete_asientos_pagid($id, $pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables WHERE con_asiento='$id' and doc_id='$pag'");
        }
    }

    function delete_obligacion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_obligacion_pago WHERE obl_codigo='$id'");
        }
    }

    function delete_obligacion_pagid($id, $pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_obligacion_pago WHERE obl_codigo='$id' and pag_id=$pag");
        }
    }

    function lista_un_asiento_pag_id($id, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables WHERE doc_id='$id' and con_concepto= 'CUENTAS X PAGAR' and con_fecha_emision='$fec'");
        }
    }

    function delete_ctasxpagar($id, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_ctasxpagar WHERE pag_id='$id' and ctp_fecha_pago='$fec'");
        }
    }

     function suma_pagos1($id) {
        if ($this->con->Conectar() == true) {
              return pg_query("select (select sum(ctp_monto) from erp_ctasxpagar where reg_id=$id and ctp_forma_pago<>'NOTA DE DEBITO') as monto,
                                    (select reg_total from erp_reg_documentos where reg_id='$id') as pago,
                                    (select sum(ctp_monto) from erp_ctasxpagar where reg_id=$id and ctp_forma_pago='NOTA DE DEBITO') as debito");
        }
    }
}

?>
