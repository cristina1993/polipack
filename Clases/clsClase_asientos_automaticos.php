<?php

include_once 'Conn.php';

class Clase_asientos_automaticos {

    var $con;

    function Clase_asientos_automaticos() {
        $this->con = new Conn();
    }

    function lista_asientos($doc, $con) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_asientos_contables where con_documento='$doc' and con_concepto='$con'");
        }
    }

    function lista_plan($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where pln_id=$id");
        }
    }

    function ultimo_asiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    ////ASIENTOS NUEVOS///
    //
    function lista_facturas_documento($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura where fac_id='$doc'");
        }
    }

    function delete_asientos($doc, $con) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_asientos_contables where con_documento='$doc' and con_concepto='$con'");
        }
    }

    function lista_facturas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura order by fac_numero asc");
        }
    }

    function siguiente_asiento() {
        if ($this->con->Conectar() == true) {
            $rst = pg_fetch_array($this->ultimo_asiento());
            if (!empty($rst)) {
                $sec = (substr($rst[con_asiento], -10) + 1);
                $n_sec = 'AS' . substr($rst[con_asiento], 2, (10 - strlen($sec))) . $sec;
            } else {
                $n_sec = 'AS0000000001';
            }
            return $n_sec;
            print_r($n_sec);
        }
    }

    function lista_det_fac($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_factura where fac_id='$num'");
        }
    }

    function lista_un_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_ced_ruc='$id'");
        }
    }

    function lista_suma_descuentos_factura($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id='$doc' and dfc_iva='0') as desc0,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id='$doc' and dfc_iva='12') as desc12,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id='$doc' and dfc_iva='EX') as descex,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id='$doc' and dfc_iva='NO') as descno");
        }
    }

    function insert_asientos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_asientos_contables(
                con_asiento,
                con_concepto,
                con_documento,
                con_fecha_emision,
                con_concepto_debe,
                con_concepto_haber,
                con_valor_debe,
                con_valor_haber,
                con_estado
            )
    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]',1)");
        }
    }

    function lista_notacre_documento($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito where ncr_id='$doc'");
        }
    }

    function lista_notas_credito() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito order by ncr_numero asc");
        }
    }

    function lista_suma_descuentos_nota_cred($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='0') as desc0,
                                    (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='12') as desc12,
                                    (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='EX') as descex,
                                    (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='NO') as descno");
        }
    }

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.cas_id='$id'");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
