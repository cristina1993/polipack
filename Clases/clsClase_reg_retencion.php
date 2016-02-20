<?php

include_once 'Conn.php';

class Clase_reg_retencion {

    var $con;

    function Clase_reg_retencion() {
        $this->con = new Conn();
    }

    function lista_buscador_retencion($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_retencion $txt ORDER BY rgr_num_registro");
        }
    }

    function lista_secuencial_retencion($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT rgr_num_registro as sec FROM  erp_registro_retencion order by rgr_num_registro desc limit 1");
        }
    }

    function lista_datos_porcentaje($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  porcentages_retencion where por_codigo='$id' or por_descripcion='$id'");
        }
    }

    function lista_buscar_clientes($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where 
                (cli_codigo like '%$txt%' 
                    or cli_ced_ruc like '%$txt%'  
                        or cli_nombres like '%$txt%' 
                            or cli_apellidos like '%$txt%' 
                                or cli_raz_social like '%$txt%') 
                                                        Order by cli_nombres");
        }
    }

    function lista_clientes_cedula($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_ced_ruc='$id'");
        }
    }

    function lista_porcentaje() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  porcentages_retencion  ORDER BY por_porcentage");
        }
    }

    function insert_retencion($data) {
//        print_r($data);
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_registro_retencion(
                                                                cli_id,
                                                                rgr_numero, 
                                                                rgr_nombre, 
                                                                rgr_identificacion,
                                                                rgr_num_comp_retiene, 
                                                                rgr_autorizacion,
                                                                rgr_denominacion_comp,
                                                                rgr_total_valor, 
                                                                rgr_fecha_emision, 
                                                                rgr_num_registro, 
                                                                rgr_fec_autorizacion, 
                                                                rgr_fec_registro, 
                                                                rgr_fec_caducidad,
                                                                fac_id)
                                                         VALUES ('$data[0]',
                                                                '$data[1]',
                                                                '" . strtoupper($data[2]) . "',
                                                                '" . strtoupper($data[3]) . "',  
                                                                '$data[4]', 
                                                                '$data[5]',
                                                                '$data[6]',
                                                                '$data[7]', 
                                                                '$data[8]',
                                                                '$data[9]',
                                                                '$data[10]',
                                                                '$data[11]',
                                                                '$data[12]',
                                                                '$data[13]')");
        }
    }

    function insert_det_retencion($data, $id) {
//        print_r($data);
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_reg_retencion(
                                                                rgr_id, 
                                                                por_id, 
                                                                drr_ejercicio_fiscal, 
                                                                drr_base_imponible, 
                                                                drr_codigo_impuesto, 
                                                                drr_procentaje_retencion, 
                                                                drr_valor, 
                                                                drr_tipo_impuesto)
                                                         VALUES (
                                                                $id,
                                                                '$data[0]',
                                                                '$data[1]',
                                                                '$data[2]',
                                                                '" . strtoupper($data[3]) . "',  
                                                                '$data[4]', 
                                                                '$data[5]',
                                                                '$data[6]'
                                                                )");
        }
    }

    function lista_retencion_numero($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_retencion where rgr_num_registro='$id'");
        }
    }

    function lista_una_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_retencion where rgr_id='$id'");
        }
    }

    function lista_det_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_det_reg_retencion d, porcentages_retencion p where d.por_id=p.por_id and d.rgr_id='$id'");
        }
    }

    function delete_det_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_det_reg_retencion where rgr_id='$id'");
        }
    }

    function update_retencion($data, $id) {
//        print_r($data);
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_registro_retencion SET
                                                                cli_id='$data[0]',
                                                                rgr_numero='$data[1]', 
                                                                rgr_nombre='" . strtoupper($data[2]) . "', 
                                                                rgr_identificacion='" . strtoupper($data[3]) . "',  
                                                                rgr_num_comp_retiene='$data[4]',  
                                                                rgr_autorizacion='$data[5]',
                                                                rgr_denominacion_comp='$data[6]',
                                                                rgr_total_valor='$data[7]', 
                                                                rgr_fecha_emision='$data[8]', 
                                                                rgr_num_registro='$data[9]', 
                                                                rgr_fec_autorizacion='$data[10]', 
                                                                rgr_fec_registro='$data[11]', 
                                                                rgr_fec_caducidad='$data[12]', 
                                                                fac_id='$data[13]' 
                                                               WHERE rgr_id=$id
                    ");
        }
    }

    function delete_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_registro_retencion WHERE rgr_id='$id'");
        }
    }

    function lista_una_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where com_id='$id'");
        }
    }

    function lista_retencion_duplicada($doc, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_retencion where rgr_numero=$doc and rgr_identificacion=$ruc and rgr_estado=1");
        }
    }

    function insert_cobros($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_cheques(
                                                                cli_id, 
                                                                chq_nombre, 
                                                                chq_banco, 
                                                                chq_numero, 
                                                                chq_recepcion, 
                                                                chq_fecha, 
                                                                chq_monto, 
                                                                chq_tipo_doc,
                                                                chq_cobro,
                                                                doc_id)
                                                         VALUES (
                                                                '$data[0]',
                                                                '$data[1]',
                                                                '$data[2]',
                                                                '$data[3]',  
                                                                '$data[4]', 
                                                                '$data[5]',
                                                                '$data[6]',
                                                                '$data[7]',
                                                                '$data[8]',
                                                                '$data[9]'
                                                                )");
        }
    }

    function update_cobros($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_cheques set
                                                                cli_id='$data[0]', 
                                                                chq_nombre='$data[1]', 
                                                                chq_banco='$data[2]',
                                                                chq_numero='$data[3]', 
                                                                chq_recepcion='$data[4]', 
                                                                chq_fecha='$data[5]', 
                                                                chq_monto='$data[6]', 
                                                                chq_tipo_doc='$data[7]',
                                                                chq_cobro='$data[8]'
                                                         where doc_id=$id and chq_nombre='RETENCION'
                                                        ");
        }
    }

    function delete_cobros($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_cheques where doc_id=$id and chq_nombre='RETENCION'");
        }
    }

    function lista_cobro_doc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_cheques where doc_id=$id and chq_nombre='RETENCION' and chq_cobro=0");
        }
    }

    function update_estado_reg_retencion($id, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_registro_retencion set rgr_estado='$std' WHERE rgr_id=$id");
        }
    }

    function update_estado_det_retencion($id, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_det_reg_retencion set det_estado='$std' WHERE rgr_id=$id");
        }
    }

    function update_estado_cobros($id, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cheques set chq_estado='$std' WHERE doc_id=$id and chq_tipo_doc='5'");
        }
    }

    function lista_una_factura_nfact($id) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  comprobantes f, erp_i_cliente c where c.cli_ced_ruc=f.identificacion and f.num_documento='$id' and tipo_comprobante=1 and com_val_estado=1");
            return pg_query("SELECT * FROM  erp_factura f, erp_i_cliente c where c.cli_id=f.cli_id and f.fac_numero='$id'");
        }
    }

    function ultimo_asiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
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
        }
    }

    function insert_asientos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into erp_asientos_contables(
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
                                                    VALUES (
                                                                '$data[0]',
                                                                '$data[1]',
                                                                '$data[2]',
                                                                '$data[3]',
                                                                '$data[4]',
                                                                '$data[5]',
                                                                '$data[6]',
                                                                '$data[7]',
                                                                '$data[8]'
                                                            )");
        }
    }

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.cas_id='$id' and c.pln_estado=0");
        }
    }

    function lista_suma_ir_iv($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT(SELECT sum(drr_valor) suma_ir FROM erp_det_reg_retencion WHERE rgr_id=$id and drr_tipo_impuesto='IR' GROUP BY drr_tipo_impuesto) AS ir,
                                   (SELECT sum(drr_valor) suma_iv FROM erp_det_reg_retencion WHERE rgr_id=$id and drr_tipo_impuesto='IV' GROUP BY drr_tipo_impuesto) AS iv");
        }
    }

    function lista_secuencial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_registro_retencion where rgr_num_registro='$id'");
        }
    }
    
      function lista_id_cuenta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM porcentages_retencion WHERE por_id=$id");
        }
    }
    
     function lista_cuenta_contable($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas WHERE pln_id=$id");
        }
    }

     function lista_cuentas_act_inac($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas where pln_id=$id and pln_estado='0'");
        }
    }
     function update_asiento_ret($id, $as) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_registro_retencion set con_asiento='$as' WHERE rgr_id=$id");
        }
    }
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
