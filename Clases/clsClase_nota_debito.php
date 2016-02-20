<?php

include_once 'Conn.php';

class Clase_nota_debito {

    var $con;

    function Clase_nota_debito() {
        $this->con = new Conn();
    }

    function lista_un_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_ced_ruc='$id'");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where cod_punto_emision='$id'");
        }
    }

    function lista_producto_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$id'");
        }
    }

    function lista_i_producto_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$id'");
        }
    }

    function lista_comprobante($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=1 and cod_punto_emision=$emi ORDER BY num_secuencial desc");
        }
    }

    function lista_comprobante_fecha($desde, $hasta, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where fecha_emision >='$desde' and fecha_emision <='$hasta' and tipo_comprobante=5 and cod_punto_emision=$emi ORDER BY num_secuencial desc");
        }
    }

    function lista_una_factura($id, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where num_secuencial='$id' and cod_punto_emision=$cod and tipo_comprobante=1");
        }
    }

    function lista_una_factura_numdoc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where num_documento='$id'and tipo_comprobante=1");
        }
    }

    function lista_nota_debito_completo() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=5 order by num_secuencial");
        }
    }

    function upd_notdeb_clave_acceso($clave, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes 
                set clave_acceso='$clave'  where com_id=$id ");
        }
    }

    function upd_notdeb_na($na, $fh, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_nota_debito 
                set ndb_estado_aut='RECIBIDA AUTORIZADO', ndb_fec_hora_aut='$fh' , ndb_autorizacion='$na'  where ndb_clave_acceso='$id'");
        }
    }

    function lista_nota_debito_numdoc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=5 and num_documento='$id'");
        }
    }

    function delete_nota_debito($num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM comprobantes WHERE num_documento='$num_secuencial' and tipo_comprobante=5");
        }
    }

    function upd_estado_notdeb($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes set com_estado='ANULADO' where com_id='$id'");
        }
    }

    ////tablas nuevas///
    //
    
     function lista_buscador_facturas($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura f, erp_i_cliente c, erp_vendedores v where f.cli_id=c.cli_id and f.vnd_id=v.vnd_id $txt ORDER BY f.fac_numero");
        }
    }

    function lista_buscador_notas_debito($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_debito n, erp_i_cliente c, erp_vendedores v where n.cli_id=c.cli_id and n.vnd_id=v.vnd_id $txt ORDER BY ndb_numero");
        }
    }

    function lista_un_nota_fac($fac) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_debito n, erp_i_cliente c, erp_vendedores v where n.cli_id=c.cli_id and n.vnd_id=v.vnd_id and n.fac_id='$fac'");
        }
    }

    function lista_una_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura f, erp_i_cliente c, erp_vendedores v where f.cli_id=c.cli_id and f.vnd_id=v.vnd_id and f.fac_id=$id");
        }
    }

    function lista_una_nota_debito_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_debito n, erp_i_cliente c, erp_vendedores v where n.cli_id=c.cli_id and n.vnd_id=v.vnd_id and ndb_id='$id'");
        }
    }

    function lista_una_nota_debito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_debito where ndb_numero='$id'");
        }
    }

    function lista_detalle_nota($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_nota_debito WHERE ndb_id='$id'");
        }
    }

    function lista_secuencial_nota_debito($bod) {
        if ($this->con->Conectar() == true) {
            if ($this->con->Conectar() == true) {
                return pg_query("SELECT * FROM  erp_nota_debito where emi_id='$bod' order by ndb_numero desc limit 1");
            }
        }
    }

    function lista_vendedor($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM  erp_vendedores where vnd_nombre='$txt'");
        }
    }

    function insert_nota_debito($data, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_nota_debito(
            cli_id, 
            vnd_id, 
            emi_id, 
            ndb_numero, 
            ndb_motivo, 
            ndb_fecha_emision, 
            ndb_nombre, 
            ndb_identificacion, 
            ndb_email, 
            ndb_direccion, 
            ndb_denominacion_comprobante, 
            ndb_num_comp_modifica, 
            ndb_fecha_emi_comp, 
            ndb_subtotal12, 
            ndb_subtotal0, 
            ndb_subtotal_ex_iva, 
            ndb_subtotal_no_iva, 
            ndb_total_ice, 
            ndb_total_iva, 
            fac_id, 
            ndb_total_valor, 
            ndb_telefono,
            ndb_cod_ice,
            imp_id
            )VALUES(
            '$data[0]',
            '$data[1]',
            '$data[2]',
            '$num',
            '" . strtoupper($data[4]) . "',
            '$data[5]',
            '$data[6]',
            '$data[7]',
            '$data[8]',
            '$data[9]',
            '$data[10]',
            '$data[11]',
            '$data[12]',
            '$data[13]',
            '$data[14]',
            '$data[15]',
            '$data[16]',
            '$data[17]',
            '$data[18]',
            '$data[19]',
            '$data[20]',
            '$data[21]',
            '$data[22]',
            '$data[23]'
                        )");
        }
    }

    function insert_det_nota_debito($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_nota_debito(
                                    ndb_id,
                                    pro_id,
                                    dnd_descripcion,
                                    dnd_precio_total,
                                    dnd_precio_unit,
                                    dnd_porcentaje_descuento,
                                    dnd_val_descuento,
                                    dnd_ice
                                    )VALUES(
                                    $id,
                                   '$data[0]',
                                   '" . strtoupper($data[1]) . "',
                                   '$data[2]',
                                   '$data[3]',
                                   '$data[4]',
                                   '$data[5]',
                                   '$data[6]'
                                    )");
        }
    }

    function upd_nota_debito($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_nota_debito SET
            cli_id='$data[0]', 
            vnd_id='$data[1]', 
            emi_id='$data[2]', 
            ndb_numero='$data[3]', 
            ndb_motivo='" . strtoupper($data[4]) . "', 
            ndb_fecha_emision='$data[5]', 
            ndb_nombre='$data[6]', 
            ndb_identificacion='$data[7]', 
            ndb_email='$data[8]', 
            ndb_direccion='$data[9]', 
            ndb_denominacion_comprobante='$data[10]', 
            ndb_num_comp_modifica='$data[11]', 
            ndb_fecha_emi_comp='$data[12]', 
            ndb_subtotal12='$data[13]', 
            ndb_subtotal0='$data[14]', 
            ndb_subtotal_ex_iva='$data[15]', 
            ndb_subtotal_no_iva='$data[16]', 
            ndb_total_ice='$data[17]', 
            ndb_total_iva='$data[18]', 
            fac_id='$data[19]', 
            ndb_total_valor='$data[20]', 
            ndb_telefono='$data[21]',
            ndb_cod_ice='$data[22]',
            imp_id='$data[23]'
  	    WHERE   ndb_id='$id'");
        }
    }

    function delete_det_nota($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_det_nota_debito where ndb_id='$id'");
        }
    }

    function lista_secuencial_locales($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT n.ndb_numero as secuencial FROM  erp_nota_debito n, emisor e where n.emi_id=e.cod_punto_emision and n.emi_id=$emi order by n.ndb_numero desc limit 1");
        }
    }

    function insert_cheque($data) {
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
                                                    chq_cobro)
                                            VALUES (
                                                    $data[0],
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

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.cas_id='$id' and c.pln_estado=0");
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

    function insert_asiento_nd($data, $sec) {
        if ($this->con->Conectar() == true) {
            if (round($data[0], 2) != 0) {
                $subt = "('$sec', 'NOTA DE DEBITO', '$data[1]', '$data[2]', '', '$data[8]', '0', '$data[0]', '0'),";
            }
            if (round($data[4], 2) != 0) {
                $iva = "('$sec', 'NOTA DE DEBITO', '$data[1]','$data[2]','','$data[9]','0','$data[4]','0'),";
            }
            if (round($data[11], 2) != 0) {
                $fle1 = "('$sec', 'NOTA DE DEBITO', '$data[1]','$data[2]','','$data[10]','0','$data[11]','0'),";
            }
            if (round($data[12], 2) != 0) {
                $fle2 = "('$sec', 'NOTA DE DEBITO', '$data[1]','$data[2]','','$data[10]','0','$data[12]','0'),";
            }
            return pg_query("INSERT INTO erp_asientos_contables(
            con_asiento,
            con_concepto,
            con_documento,
            con_fecha_emision, 
            con_concepto_debe, 
            con_concepto_haber,
            con_valor_debe, 
            con_valor_haber,
            con_estado)
            VALUES
            $subt
            $iva
            $fle1
            $fle2
            ('$sec', 'NOTA DE DEBITO', '$data[1]', '$data[2]', '$data[7]', '', '$data[5]', '0', '0')") . '&' . $sec;
        }
    }

    function lista_id_nota_debito($emi, $ndoc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nota_debito WHERE emi_id=$emi and ndb_num_comp_modifica='$ndoc'");
        }
    }

    function lista_productos_fletes() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT '1' as tbl,id as id,pro_ac as lote,pro_a as codigo,pro_b as descripcion FROM  erp_productos where pro_estado=0 and pro_a='FLETE 1' or pro_a='FLETE 2'");
        }
    }

    function lista_det_ntd($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_nota_debito where ndb_id=$id");
        }
    }

    function lista_cheques_numero($num, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques where chq_numero='$num' and cli_id=$id and chq_tipo_doc='4'");
        }
    }
    
     function insert_ctaxcobrar($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_ctasxcobrar (
                                    com_id,
                                    cta_fecha,
                                    cta_monto,
                                    cta_forma_pago,
                                    cta_banco,
                                    pln_id,
                                    pag_id,
                                    cta_fecha_pago,
                                    num_documento,
                                    cta_concepto,
                                    asiento,
                                    chq_id)
                                    values(
                                    '$data[0]',
                                    '$data[1]',
                                    '$data[2]',
                                    '$data[3]',
                                    '$data[4]',
                                    '$data[5]',
                                    '$data[6]',
                                    '$data[7]',
                                    '$data[8]',
                                    '$data[9]',
                                    '$data[10]',
                                    '$data[11]')
                                   ");
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
    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]',0)");
        }
    }
    
     function listar_una_cta_comid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxcobrar where com_id=$id");
        }
    }
    
     function lista_pagos($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura WHERE com_id='$id' order by pag_id $txt");
        }
    }
    
    function buscar_un_pago($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura p WHERE p.com_id='$id' and not exists(SELECT * FROM erp_ctasxcobrar c where c.pag_id=p.pag_id)");
        }
    }


///////////////////////////////////////////////////////////////////////////////////////         
}

?>
