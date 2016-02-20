<?php

include_once 'Conn.php';

class Clase_rep_ventas {

    var $con;

    function Clase_rep_ventas() {
        $this->con = new Conn();
    }

    function lista_total_ventas($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(fac_total_valor) FROM erp_factura where emi_id=$emi");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where identificacion='$id'");
        }
    }

    function orden_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where cod_orden=$id");
        }
    }

    //// tablas nuevas////

    function lista_total_venta($emi, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(fac_subtotal12) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2')as sub12,
                                    (SELECT sum(fac_subtotal0) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2')as sub0,
                                    (SELECT sum(fac_subtotal_ex_iva) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2')as subex,
                                    (SELECT sum(fac_subtotal_no_iva) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2')as subno,
                                    (SELECT sum(fac_total_descuento) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2')as des,
                                    (SELECT sum(fac_total_ice) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2')as ice,
                                    (SELECT sum(fac_total_iva) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2')as iva,
                                    (SELECT sum(fac_total_valor) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2')as total");
        }
    }

    function lista_total_devoluciones($emi, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(ncr_subtotal12) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2')as nsub12,
                                    (SELECT sum(ncr_subtotal0) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2')as nsub0,
                                    (SELECT sum(ncr_subtotal_ex_iva) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2')as nsubex,
                                    (SELECT sum(ncr_subtotal_no_iva) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2')as nsubno,
                                    (SELECT sum(ncr_total_descuento) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2')as ndes,
                                    (SELECT sum(ncr_total_ice) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2')as nice,
                                    (SELECT sum(ncr_total_iva) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2')as niva,
                                    (SELECT sum(nrc_total_valor) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2')as ntotal");
        }
    }

    function lista_total_cierre($emi, $f1, $f2) {
        return pg_query("SELECT (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=cast(c.fac_id as varchar) and c.emi_id=$emi and p.pag_forma='1' and c.fac_fecha_emision between '$f1' and '$f2')as credito,
                                    (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=cast(c.fac_id as varchar) and c.emi_id=$emi and p.pag_forma='2' and c.fac_fecha_emision between '$f1' and '$f2')as debito,
                                    (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=cast(c.fac_id as varchar) and c.emi_id=$emi and p.pag_forma='3' and c.fac_fecha_emision between '$f1' and '$f2')as cheque,
                                    (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c where p.com_id=cast(c.fac_id as varchar) and c.emi_id=$emi and p.pag_forma='4' and c.fac_fecha_emision between '$f1' and '$f2')as efectivo,
                                    (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=cast(c.fac_id as varchar) and c.emi_id=$emi and p.pag_forma='5' and c.fac_fecha_emision between '$f1' and '$f2')as certificados,
                                    (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=cast(c.fac_id as varchar) and c.emi_id=$emi and p.pag_forma='6' and c.fac_fecha_emision between '$f1' and '$f2')as bonos,
                                    (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=cast(c.fac_id as varchar) and c.emi_id=$emi and p.pag_forma='7' and c.fac_fecha_emision between '$f1' and '$f2')as retencion,
                                    (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=cast(c.fac_id as varchar) and c.emi_id=$emi and p.pag_forma='8' and c.fac_fecha_emision between '$f1' and '$f2')as nota,
                                    (SELECT sum(fac_total_valor) from erp_factura where emi_id=$emi and fac_fecha_emision between '$f1' and '$f2') as valor");
    }

    function lista_ultima_fecha() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_consulta_inv order by con_fecha desc limit 1");
        }
    }

    function lista_configuraciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones where con_id=1");
        }
    }

}

?>
