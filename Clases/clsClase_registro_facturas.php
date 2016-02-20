<?php

include_once 'Conn.php';

class Clase_registro_facturas {

    var $con;

    function Clase_registro_facturas() {
        $this->con = new Conn();
    }

    function lista_proveedores() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where CAST(cli_tipo AS Integer)>0 order by CAST(cli_tipo AS Integer)");
        }
    }

    function lista_cliente_ruc($ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where cli_ced_ruc='$ruc' ");
        }
    }

    function lista_productos_insumos_otros() {
        if ($this->con->Conectar() == true) {
            return pg_query("(select 0 as tbl, pro_id as id, trim(pro_codigo) as cod, trim(pro_descripcion) as dsc, '' as lote  from erp_i_productos where pro_estado=0
union
select 1 as tbl, id as id, trim(pro_a) as cod, trim(pro_b) as dcs, trim(pro_ac) as lote from erp_productos where pro_estado=0 
union 
select 2 as tbl, ins_id as id, trim(ins_codigo) as cod,trim(ins_descripcion) as dsc, '' as lote from erp_insumos_otros
union 
select 3 as tbl, mp_id as id, trim(mp_codigo) as cod,trim(mp_descripcion) as dsc, '' as lote from erp_materia_prima )
order by cod ");
        }
    }

    function lista_producto_insumos_otros_id($tbl, $id) {
        if ($this->con->Conectar() == true) {
            switch ($tbl) {
                case 0:
                    $sql = "select 0 as tbl, pro_codigo as cod, pro_descripcion as dsc, '' as lote  from erp_i_productos where pro_id=$id "; //Producto
                    break;
                case 1:
                    $sql = "select 1 as tbl, pro_a as cod, pro_b as dsc, pro_ac as lote from erp_productos where id=$id "; //Producto
                    break;
                case 2:
                    $sql = "select 2 as tbl, ins_codigo as cod, ins_descripcion as dsc, '' as lote  from erp_insumos_otros where ins_id=$id "; //Insumos
                    break;
                case 3:
                    $sql = "select 3 as tbl, mp_codigo as cod, mp_descripcion as dsc, '' as lote from erp_materia_prima where mp_id=$id "; //Materia Primo
                    break;
            }
            return pg_query($sql);
        }
    }

    function lista_producto_insumos_otros_cod($tbl, $id) {
        if ($this->con->Conectar() == true) {
            switch ($tbl) {
                case 1:
                    $sql = "select 0 as tbl, pro_codigo as cod, pro_descripcion as dsc, '' as lote, pro_id as id  from erp_i_productos where pro_codigo='$id' "; //Producto
                    break;
                case 0:
                    $sql = "select 2 as tbl, ins_codigo as cod, ins_descripcion as dsc, '' as lote,ins_id as id  from erp_insumos_otros where ins_codigo='$id'"; //Insumos
                    break;
                case 2:
                    $sql = "select 3 as tbl, mp_codigo as cod, mp_descripcion as dsc, '' as lote,mp_id as id from erp_materia_prima where mp_codigo='$id' "; //Materia Primo
                    break;
                case 3:
                    $sql = "select 1 as tbl, pro_a as cod, pro_b as dsc, pro_ac as lote,id as id from erp_productos where pro_a='$id'"; //Producto
                    break;
            }
            return pg_query($sql);
        }
    }

    function lista_detalle_registro($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_det_documentos where reg_id=$id ");
        }
    }

    function lista_ultimo_codigo($tp) {
        if ($this->con->Conectar() == true) {
            switch ($tp) {
                case '0'://Insumos
                    $query = pg_query("select ins_codigo as cod from erp_insumos_otros order by ins_codigo desc limit 1");
                    break;
                case '1'://Productos
//                    $query = pg_query("select pro_codigo as cod from erp_i_productos where pro_codigo<>'flete 1' and pro_codigo<>'flete 2' order by pro_codigo desc limit 1");
                    $query = pg_query("select pro_codigo as cod from erp_i_productos where pro_codigo like 'V%' and char_length(pro_codigo)=5  order by pro_codigo desc limit 1");
                    break;
                case '2'://Materia Prima
                    $query = pg_query("select mp_codigo as cod from erp_materia_prima order by mp_codigo desc limit 1");
                    break;
            }

            return $query;
        }
    }

    function lista_pagos_registro($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_pagos_documentos where reg_id=$id ");
        }
    }

    function lista_un_registro($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos where reg_id=$id ");
        }
    }

    function lista_registro_numero($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_documentos where reg_num_registro='$num' ");
        }
    }

    function lista_registros_completo() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_documentos order by reg_femision,reg_num_registro ");
        }
    }

    function lista_ultimo_registro() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_documentos order by reg_num_registro desc ");
        }
    }

    function elimina_detalle_pagos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_pagos_documentos where reg_id=$id; 
                             delete from erp_reg_det_documentos where reg_id=$id ");
        }
    }

    function elimina_asientos_asiento($as) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_asientos_contables where con_asiento='$as' ");
        }
    }

    function elimina_registro_detalle_pagos_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_pagos_documentos where reg_id=$id; 
                             delete from erp_reg_det_documentos where reg_id=$id;
                             delete from erp_reg_documentos where reg_id=$id    
                                 ");
        }
    }

    function elimina_registro_detalle_pagos($num) {
        if ($this->con->Conectar() == true) {
            $sms = 0;
            $rst = pg_fetch_array($this->lista_registro_numero($num));
            if (!empty($rst)) {
                if (pg_query("delete from erp_pagos_documentos where reg_id=$rst[reg_id] ") == true) {
                    if (pg_query("delete from erp_reg_det_documentos where reg_id=$rst[reg_id] ") == true) {
                        if (pg_query("delete from erp_reg_documentos where reg_id=$rst[reg_id] ") == false) {
                            $sms = 'Del Reg ' . pg_last_error();
                        }
                    } else {
                        $sms = 'Del Det ' . pg_last_error();
                    }
                } else {
                    $sms = 'Del Pag ' . pg_last_error();
                }
            } else {
                $sms = 'No Existe Documento ' . $num;
            }
            return $sms;
        }
    }

    function insert_producto_insumo_mp($cod, $desc, $tp) {
        $cod = strtoupper($cod);
        $desc = strtoupper($desc);
        if ($this->con->Conectar() == true) {
            switch ($tp) {
                case '0':
                    $sql = "insert into erp_insumos_otros (ins_codigo,ins_descripcion)values('$cod','$desc')";
                    break;    //Insumos
                case '1':
                    $sql = "insert into erp_i_productos (pro_codigo,pro_descripcion)values('$cod','$desc')";
                    break;    //Productos Industriales
                case '2':
                    $sql = "insert into erp_materia_prima (mp_codigo,mp_descripcion)values('$cod','$desc')";
                    break;    //Materia prima
            }
            return pg_query($sql);
        }
    }

    function ultimo_asiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    function upd_num_asiento($as, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_documentos set con_asiento='$as' where reg_id=$id ");
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

//    function insert_asiento_mp($val, $doc, $femision, $as = '') {
//        if ($this->con->Conectar() == true) {
//            if ($as == '') {
//                $sec = $this->siguiente_asiento();
//            } else {
//                $sec = $as;
//            }
//
//            $iva = ($val * 0.12);
//            $sbt = ($val - $iva);
//
//            return pg_query("INSERT INTO erp_asientos_contables(
//            con_asiento,
//            con_concepto,
//            con_documento,
//            con_fecha_emision, 
//            con_concepto_debe, 
//            con_concepto_haber,
//            con_valor_debe, 
//            con_valor_haber)
//    VALUES ('$sec','FACTURA COMPRA DE MATERIA PRIMA','$doc','$femision','1.01.03.01.001','',$sbt,'0'),
//           ('$sec','FACTURA COMPRA DE MATERIA PRIMA','$doc','$femision','1.01.05.01.010','',$iva,'0'),
//           ('$sec','FACTURA COMPRA DE MATERIA PRIMA','$doc','$femision','','2.02.02.01.001','0','$val') ") . '&' . $sec;
//        }
//    }

    function insert_asiento_mp($data, $sec) {
        if ($this->con->Conectar() == true) {
            if (round($data[9], 2) != 0) {
                $desc = "('$sec', '$data[17]', '$data[1]', '$data[2]', '', '$data[15]', '0', '$data[9]', '0'),";
            }
            if (round($data[4], 2) != 0) {
                $iva = "('$sec','$data[17]','$data[1]','$data[2]','$data[11]','','$data[4]','0','0'),";
            }
            if (round($data[6], 2) != 0) {
                $ice = "('$sec','$data[17]','$data[1]','$data[2]','$data[13]','','$data[6]','0','0'),";
            }
            if (round($data[7], 2) != 0) {
                $ibr = "('$sec','$data[17]','$data[1]','$data[2]','$data[14]','','$data[7]','0','0'),";
            }
            if (round($data[8], 2) != 0) {
                $prop = "('$sec','$data[17]','$data[1]','$data[2]','$data[16]','','$data[8]','0','0'),";
            }
            $val = $data[0] + $data[9];
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
            $desc
            $iva
            $ice
            $ibr
            $prop
            ('$sec', '$data[17]', '$data[1]', '$data[2]', '', '$data[12]', '0', '$data[5]', '0')") . '&' . $sec;
        }
    }

    function insert_asiento_det($data) {
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
            con_estado)
            VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','','$data[5]','0','0')");
        }
    }

    function insert_asiento_anulacion($data, $sec) {
        if ($this->con->Conectar() == true) {
            if (round($data[9], 2) != 0) {
                $desc = "('$sec', 'ANULACION FACTURA COMPRA', '$data[1]', '$data[2]','$data[15]','', '$data[9]','0','0','$data[17]'),";
            }
            if (round($data[4], 2) != 0) {
                $iva = "('$sec','ANULACION FACTURA COMPRA','$data[1]','$data[2]','','$data[11]','0','$data[4]','0','$data[17]'),";
            }
            if (round($data[6], 2) != 0) {
                $ice = "('$sec','ANULACION FACTURA COMPRA','$data[1]','$data[2]','','$data[13]','0','$data[6]','0','$data[17]'),";
            }
            if (round($data[7], 2) != 0) {
                $ibr = "('$sec','ANULACION FACTURA COMPRA','$data[1]','$data[2]','','$data[14]','0','$data[7]','0','$data[17]'),";
            }
            if (round($data[8], 2) != 0) {
                $prop = "('$sec','ANULACION FACTURA COMPRA','$data[1]','$data[2]','','$data[16]','0','$data[8]','0','$data[17]'),";
            }
            $val = $data[0] + $data[9];
            return pg_query("INSERT INTO erp_asientos_contables(
            con_asiento,
            con_concepto,
            con_documento,
            con_fecha_emision, 
            con_concepto_debe, 
            con_concepto_haber,
            con_valor_debe, 
            con_valor_haber,
            con_estado,
            doc_id)
            VALUES
            $desc
            $iva
            $ice
            $ibr
            $prop
            ('$sec', 'ANULACION FACTURA COMPRA', '$data[1]', '$data[2]',  '$data[12]','','$data[5]','0', '0','$data[17]')") . '&' . $sec;
        }
    }

    function insert_asiento_anulacion_det($data) {
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
            con_estado,
            doc_id)
            VALUES ('$data[0]','ANULACION FACTURA COMPRA','$data[1]','$data[2]','','$data[3]','0','$data[4]','0','$data[5]')");
        }
    }

    function insert_registro($data, $cli_id) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into erp_reg_documentos (
            reg_fregistro,
            reg_femision,
            reg_fvencimiento,
            reg_tipo_documento, 
            reg_num_documento,
            reg_num_autorizacion,
            reg_fautorizacion,
            reg_fcaducidad, 
            reg_tpcliente,
            reg_concepto,
            reg_sbt12,
            reg_sbt0,
            reg_sbt_noiva, 
            reg_sbt_excento,
            reg_sbt,
            reg_tdescuento,
            reg_ice,
            reg_irbpnr, 
            reg_iva12,
            reg_propina,
            reg_total,
            reg_ruc_cliente,
            reg_num_registro,
            reg_estado,
            reg_sustento,
            cli_id,
            reg_importe,
            imp_id
            )values(
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
                '1',
            '$data[27]',
                $cli_id,
            '$data[29]',
            '$data[30]'
                )
            
            ");
        }
    }

    function insert_detalle_registro($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_reg_det_documentos(
            det_codigo_empresa, 
            det_descripcion, 
            det_cantidad, 
            det_vunit, 
            det_descuento_porcentaje, 
            det_descuento_moneda, 
            det_total, 
            det_impuesto, 
            det_tipo, 
            det_codigo_externo,
            det_tab,
            pln_id,
            reg_codigo_cta,
            pro_id,
            reg_id)
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
    '$data[9]',
    '$data[12]',
    '$data[13]',
    '$data[14]',
    '$data[15]',
    '$data[16]')");
        }
    }

    function insert_pagos_registro($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_pagos_documentos(
            pag_tipo,
            pag_porcentage,
            pag_dias,
            pag_valor, 
            pag_fecha_v,
            reg_id )
    VALUES (
    0,
    $data[0],    
    $data[1],
    $data[2],    
   '$data[3]',
    $data[4]  )
");
        }
    }

    function upd_registro($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_documentos
       SET reg_fregistro='$data[0]',
       reg_femision='$data[1]',
       reg_fvencimiento='$data[2]', 
       reg_tipo_documento='$data[3]',
       reg_num_documento='$data[4]',
       reg_num_autorizacion='$data[5]', 
       reg_fautorizacion='$data[6]',
       reg_fcaducidad='$data[7]',
       reg_tpcliente='$data[8]',
       reg_concepto='$data[9]', 
       reg_sbt12='$data[10]',
       reg_sbt0='$data[11]',
       reg_sbt_noiva='$data[12]',
       reg_sbt_excento='$data[13]', 
       reg_sbt='$data[14]',
       reg_tdescuento='$data[15]',
       reg_ice='$data[16]',
       reg_irbpnr='$data[17]',
       reg_iva12='$data[18]', 
       reg_propina='$data[19]',
       reg_total='$data[20]',
       reg_ruc_cliente='$data[21]',
       reg_num_registro='$data[22]',
       reg_importe='$data[29]',
       imp_id='$data[30]'
 WHERE reg_id=$id
     ");
        }
    }

    function lista_buscador_reg_fac($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_reg_documentos d, erp_i_cliente c where d.cli_id=c.cli_id $txt order by reg_num_registro");
        }
    }

    function lista_plan_cuentas_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_id = $id");
        }
    }

    function lista_plan_cuentas() {
        if (
                $this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_estado='0' ORDER BY pln_codigo");
        }
    }

    function lista_secuencial_cliente($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where substr(cli_codigo, 1, 2) = '$tp' order by cli_codigo desc limit 1");
        }
    }

    function insert_cliente($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cliente
            (
            cli_nom_comercial,
            cli_raz_social,
            cli_fecha,
            cli_estado,
            cli_tipo,
            cli_categoria,
            cli_ced_ruc,
            cli_calle_prin,
            cli_telefono,
            cli_email,
            cli_codigo
            ) values ('$data[0]',
            '$data[0]',
            '" . date('Y-m-d') . "',
            0,
            0,
            1,
            '$data[1]',
            '$data[2]',
            '$data[3]',
            '$data[4]',
            '$data[5]'
            )");
        }
    }

    function upd_email_cliente($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cliente SET
            cli_calle_prin = '$data[0]',
            cli_email = '$data[1]',
            cli_telefono = '$data[2]',
            cli_calle_sec = '',
            cli_numeracion = ''
            WHERE cli_ced_ruc = '$id'");
        }
    }

    function lista_un_registro_factura($doc, $ruc, $tip) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos where reg_num_documento = '$doc' and reg_ruc_cliente = '$ruc' and reg_tipo_documento = '$tip'");
        }
    }

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id = c.pln_id and a.cas_id = '$id' and c.pln_estado=0");
        }
    }

    function lista_sum_cuentas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select pln_id,reg_codigo_cta, sum(det_total) as dtot, sum(det_descuento_moneda) as ddesc  from erp_reg_det_documentos where reg_id=$id group by pln_id,reg_codigo_cta");
        }
    }

    function buscar_retencion($num, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion where ret_num_comp_retiene='$num' and ret_identificacion='$ruc' and ret_estado_aut<>'ANULADO'");
        }
    }

    function lista_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion where reg_id='$id' and ret_estado_aut<>'ANULADO'");
        }
    }

    function lista_una_nota_cred($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_nota_credito where reg_id = '$id' and rnc_estado<>'3'");
        }
    }

    function lista_una_nota_deb($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_nota_debito where reg_id = '$id' and rnd_estado<>'3'");
        }
    }

    function update_estado_reg_factura($id, $std, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_documentos set reg_estado = '$std', reg_femision = '$fec' WHERE reg_id = $id");
        }
    }

    function update_estado_det_factura($id, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_det_documentos set det_estado = '$std' WHERE reg_id = $id");
        }
    }

    function lista_secuencial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_reg_documentos WHERE reg_num_registro='$id'");
        }
    }

    function lista_ultima_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_retencion WHERE reg_id=$id order by ret_id desc limit 1");
        }
    }

    function lista_asiento_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_asientos_contables where con_documento='$id' and con_concepto='ANULACION RETENCION'");
        }
    }

    function lista_asiento_anulado($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_asientos_contables where con_documento='$id' and con_concepto='ANULACION FACTURA COMPRA'");
        }
    }

    function update_asiento_anulacion($asi, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_documentos set reg_asi_anulacion = '$asi' WHERE reg_id = $id");
        }
    }

    function lista_encabezdo_ant($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos where cli_id=$id and (reg_estado=1 or reg_estado=4) order by reg_num_registro desc limit 1");
        }
    }
    
      function lista_producto_ant($id,$tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos r, erp_reg_det_documentos d where d.reg_id=r.reg_id and d.pro_id=$id and d.det_tab=$tab and (r.reg_estado=1 or r.reg_estado=4) order by r.reg_num_registro desc limit 1 ");
        }
    }

}

?>
