<?php

include_once 'Conn.php';

class Clase_factura {

    var $con;

    function Clase_factura() {
        $this->con = new Conn();
    }

    function lista_pdf_pago($id, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pago_factura where fac_id='$id'");
        }
    }

    function lista_ingreso_num_factura($nfac) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=1 and num_documento = '$nfac' ORDER BY nombre");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where identificacion='$id'");
        }
    }

    function lista_un_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where cod_punto_emision=$id");
        }
    }

///////////////////////////////////////////////////////DOCUMENTOS ELECTRONICOS////////////////////////////////         

    function lista_factura_clave($ci, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT fac_id as id, fac_numero as numero, fac_fecha_emision as fecha,fac_clave_acceso as clave, fac_estado_aut as estado, fac_autorizacion as autorizacion  FROM erp_factura where fac_identificacion='$ci' and fac_numero='$num' ");
        }
    }

    function lista_nota_credito_clave($ci, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ncr_id as id, ncr_numero as numero, ncr_fecha_emision as fecha,ncr_clave_acceso as clave, ncr_estado_aut as estado, ncr_autorizacion as autorizacion FROM erp_nota_credito where ncr_identificacion='$ci' and ncr_numero='$num' ");
        }
    }

    function lista_nota_debito_clave($ci, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ndb_id as id, ndb_numero as numero, ndb_fecha_emision as fecha,ndb_clave_acceso as clave, ndb_estado_aut as estado, ndb_autorizacion as autorizacion  FROM erp_nota_debito where ndb_identificacion='$ci' and ndb_numero='$num' ");
        }
    }

    function lista_guia_remision_clave($ci, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("select gui_id as id, gui_numero as numero, gui_fecha_emision as fecha,gui_clave_acceso as clave, gui_estado_aut as estado, gui_autorizacion as autorizacion  FROM erp_guia_remision where gui_identificacion='$ci' and gui_numero='$num'");
        }
    }

    function lista_retencion_clave($ci, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ret_id as id, ret_numero as numero, ret_fecha_emision as fecha, ret_clave_acceso as clave, ret_estado_aut as estado, ret_autorizacion as autorizacion FROM erp_retencion where ret_identificacion='$ci' and ret_numero='$num'");
        }
    }

    function lista_xml_clave($clave) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM comprobantes where clave_acceso='$clave' ");
        }
    }

    function lista_facturas_generadas($f, $tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM comprobantes where fecha_emision=$f and tipo_comprobante=$tp ");
        }
    }

    function lista_todos_noenviados() {
        if ($this->con->Conectar() == true) {
            return pg_query("select 'Fc:' as tipo,count(*) from erp_factura where (char_length(fac_autorizacion)<>37 or  fac_autorizacion is null)
union
select 'Nc:' as tipo,count(*) from erp_nota_credito where ((char_length(ncr_autorizacion)<>37 or  ncr_autorizacion is null) ) and ncr_sts<>1
union
select 'Nd:' as tipo,count(*) from erp_nota_debito where (char_length(ndb_autorizacion)<>37 or  ndb_autorizacion is null)
union
select 'Gr:' as tipo,count(*) from erp_guia_remision where (char_length(gui_autorizacion)<>37 or  gui_autorizacion is null) and gui_sts<>1
union
select 'Rt:' as tipo,count(*) from erp_retencion where ((char_length(ret_autorizacion)<>37 or  ret_autorizacion is null)  and ret_estado_aut<>'ANULADO'  )
order by count desc
 ");
        }
    }

////*********SETEO DE FACTURA *******************/////////////

    function set_seteo_factura($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_set_pdf 
                SET set_x=$data[0], 
                    set_y=$data[1], 
                    set_w=$data[2], 
                    set_h=$data[3], 
                    set_p=$data[4]
                                 where set_id=1   
                    ");
        }
    }

    function lista_seteo_pdf($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_set_pdf  where set_id=$doc   ");
        }
    }

    //FACTURA NUEVAS TABLAS////////////////////////////

    function lista_una_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura f, erp_i_cliente c, erp_vendedores v where f.cli_id=c.cli_id and f.vnd_id=v.vnd_id and f.fac_id=$id");
        }
    }

    function lista_una_factura_num($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura where fac_numero='$id'");
        }
    }

    function lista_buscador_factura($txt, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura c, emisor e, erp_vendedores v  WHERE c.emi_id=e.cod_punto_emision and v.vnd_id=c.vnd_id  $txt AND c.emi_id=$emi order by c.fac_numero");
        }
    }

    function lista_secuencial_documento($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT substr(f.fac_numero,9,9) as secuencial FROM  erp_factura f,emisor e where  f.emi_id=e.cod_punto_emision and f.emi_id=$emi order by f.fac_numero desc limit 1");
        }
    }

    function lista_clientes_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where 
                cli_codigo like '%$txt%' 
                    or cli_ced_ruc like '%$txt%'  
                        or cli_nombres like '%$txt%' 
                            or cli_apellidos like '%$txt%' 
                                or cli_raz_social like '%$txt%' 
                            
                            Order by cli_raz_social");
        }
    }

    function lista_clientes_codigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_ced_ruc='$id' ");
        }
    }

    function lista_detalle_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_factura where fac_id='$id'");
        }
    }

    function lista_un_cliente_cedula($cod) {// sirve para cuando selecciono un registro para modificar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente WHERE cli_ced_ruc='$cod'");
        }
    }

    function lista_secuencial_cliente($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where substr(cli_codigo,1,2)='$tp' order by cli_codigo desc limit 1");
        }
    }

    function insert_cliente($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cliente 
(
  cli_apellidos,
  cli_raz_social,
  cli_fecha,
  cli_estado,
  cli_tipo,
  cli_categoria,
  cli_ced_ruc,
  cli_calle_prin,
  cli_telefono,
  cli_email,
  cli_canton,
  cli_pais,
  cli_codigo,
cli_parroquia
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
'$data[5]',
'$data[6]',
'$data[7]', 
'$data[8]')");
        }
    }

    function lista_cambia_status($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta SET ped_estado='$sts' where ped_id=$id");
        }
    }

    function insert_factura($data, $cli) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_factura 
(emi_id,
cli_id, 
  vnd_id, 
 ped_id,
 fac_fecha_emision,
 fac_numero, 
 fac_nombre, 
 fac_identificacion, 
 fac_email, 
 fac_direccion, 
 fac_subtotal12, 
 fac_subtotal0, 
 fac_subtotal_ex_iva, 
 fac_subtotal_no_iva, 
 fac_total_descuento, 
 fac_total_ice, 
 fac_total_iva, 
 fac_total_irbpnr, 
 fac_total_propina,
 fac_telefono,
 fac_observaciones,
 fac_total_valor,
 fac_subtotal
) values ($data[0],
'$cli',
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
'$data[20]',
'$data[21]',               
'$data[19]',               
'$data[25]')               
                ");
        }
    }

    function update_factura($data, $cli, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_factura set
emi_id=$data[0],
cli_id='$cli', 
  vnd_id='$data[2]',  
 ped_id='$data[3]',
 fac_fecha_emision='$data[4]',
 fac_numero='$data[5]', 
 fac_nombre='$data[6]', 
 fac_identificacion='$data[7]', 
 fac_email='$data[8]', 
 fac_direccion='$data[9]', 
 fac_subtotal12='$data[10]', 
 fac_subtotal0='$data[11]', 
 fac_subtotal_ex_iva='$data[12]', 
 fac_subtotal_no_iva='$data[13]', 
 fac_total_descuento='$data[14]', 
 fac_total_ice='$data[15]', 
 fac_total_iva='$data[16]', 
 fac_total_irbpnr='$data[17]', 
 fac_total_propina='$data[18]',
 fac_telefono='$data[20]',
 fac_observaciones='$data[21]',
 fac_total_valor='$data[19]',
 fac_subtotal='$data[25]'
where fac_id=$id             
                ");
        }
    }

    function insert_detalle_factura($data) {
        if ($this->con->Conectar() == true) {
            return pg_query($data);
        }
    }

    function insert_movimiento_pt($data) {
        if ($this->con->Conectar() == true) {
            return pg_query($data);
        }
    }

    function elimina_movpt_documento($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_i_mov_inv_pt where mov_documento='$num' and trs_id='25'");
        }
    }

    function elimina_detalle_factura($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_det_factura where fac_id='$num'");
        }
    }

    function elimina_factura($nfact) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_factura where fac_id=$nfact");
        }
    }

    function total_ingreso_egreso_fact($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 $txt) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 $txt) as egreso");
        }
    }

    function lista_costos_mov($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_mov_inv_pt where pro_id=$id order by mov_id desc limit 1");
        }
    }

    function lista_vendedores($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_vendedores WHERE vnd_local=$id ORDER BY vnd_nombre");
        }
    }

    function lista_detalle_pagos($fact) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pagos_factura where com_id='$fact'");
        }
    }

    function lista_un_producto_noperti_cod_lote($code, $lote) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code' and pro_ac='$lote' and pro_estado=0");
        }
    }

    function lista_un_producto_industrial($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$code'");
        }
    }

    function total_ingreso_egreso_fac($id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egreso");
        }
    }

    function lista_producto_total($ems) {
        if ($this->con->Conectar() == true) {

            if ($ems == 1) { //Nopeti (todos los comerciales + paddin y plumos)
                $query = "(SELECT '1' as tbl,id as id,pro_ac as lote,pro_a as codigo,pro_b as descripcion FROM  erp_productos where pro_estado=0
                           union
                           SELECT '0' as tbl, pro_id as id, '' as lote ,pro_codigo as codigo,pro_descripcion as descripcion FROM erp_i_productos where pro_estado=0 and (  emp_id=3 or emp_id=4)) order by descripcion";
            } elseif ($ems == 10) { //Industrial solo los industriales
                $query = "(SELECT '0' as tbl, pro_id as id, '' as lote ,pro_codigo as codigo,pro_descripcion as descripcion FROM  erp_i_productos where pro_estado=0) order by descripcion";
            } else { //Locales todos
                $query = "(SELECT '1' as tbl,id as id,pro_ac as lote,pro_a as codigo,pro_b as descripcion FROM  erp_productos where pro_estado=0
                              union
                              SELECT '0' as tbl, pro_id as id, '' as lote ,pro_codigo as codigo,pro_descripcion as descripcion FROM  erp_i_productos where pro_estado=0) order by descripcion";
            }
            return pg_query($query);
        }
    }

    function lista_descuento_producto($id, $emi) {//////
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos where pre_id=$id and ems_id=$emi");
        }
    }

    function lista_un_producto_noperti_id($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where id= $code and pro_estado=0");
        }
    }

    function lista_precio_producto($id, $tabla) {//////
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios where pro_id=$id and pro_tabla=$tabla");
        }
    }

    function lista_un_producto_industrial_id($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$code and pro_estado=0");
        }
    }

    function upd_email_cliente($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cliente SET
            cli_calle_prin='$data[0]',
            cli_email='$data[1]',
            cli_telefono='$data[2]',
            cli_canton='$data[3]',
            cli_pais='$data[4]',
            cli_parroquia='$data[5]',
                cli_calle_sec='',
                cli_numeracion=''
            WHERE cli_ced_ruc='$id'");
        }
    }

    function insert_pagos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query($data);
        }
    }

    function delete_pagos($pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_pagos_factura where com_id='$pag'");
        }
    }

    function lista_factura_completo($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura $txt order by fac_numero");
        }
    }

    function upd_fac_na($na, $fh, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_factura
                set fac_estado_aut='RECIBIDA AUTORIZADO', fac_fec_hora_aut='$fh' , fac_autorizacion='$na'  where fac_clave_acceso='$id' ");
        }
    }

    function lista_secuencial_locales($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT f.fac_numero as secuencial FROM  erp_factura f,emisor e where  f.emi_id=e.cod_punto_emision and f.emi_id=$emi order by f.fac_numero desc limit 1");
        }
    }

    function lista_secuencial_num_factura($emi, $fac) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT f.fac_numero as secuencial 
                            FROM  erp_factura f,emisor e 
                            where  f.emi_id=e.cod_punto_emision 
                            and f.emi_id=$emi 
                            and f.fac_numero='$fac'
                            order by f.fac_numero desc limit 1 ");
        }
    }

function lista_f_vencimiento($fac_id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.pag_fecha_v from erp_factura f, erp_pagos_factura p where cast(p.com_id as integer)=($fac_id)");
        }
    }
    function lista_notcre_cli($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques WHERE cli_id=$id and chq_tipo_doc='3' AND chq_estado<>2");
        }
    }

    function lista_cheques_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques WHERE chq_id=$id");
        }
    }

    function lista_retencion_cli($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques WHERE cli_id=$id and (chq_tipo_doc='1' or chq_tipo_doc='2') AND chq_estado<>2");
        }
    }

    function upd_cantidad_cheques($cant, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cheques SET chq_cobro='$cant' where chq_id=$id");
        }
    }

//    function lista_detalle_pagos($fact) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_pagos_factura where com_id='$fact'");
//        }
//    }

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.cas_id='$id' and c.pln_estado=0");
        }
    }

    function insert_ctasxcobrar($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_ctasxcobrar(
                com_id, 
                cta_fecha, 
                cta_monto, 
                cta_forma_pago, 
                cta_banco,
                pln_id,
                cta_fecha_pago,
                pag_id,
                num_documento,
                cta_concepto,
                asiento,
                chq_id
                )
        VALUES ($data[0],
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
                '$data[11]'
               )");
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
                                                                con_estado)
                                                    VALUES (
                                                                '$data[0]',
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

    function insert_cheques($data) {
        if ($this->con->Conectar() == true) {
            return pg_query(" INSERT INTO erp_cheques(
                                                      cli_id,
                                                      chq_nombre,
                                                      chq_banco,
                                                      chq_numero,
                                                      chq_recepcion,
                                                      chq_fecha, 
                                                      chq_monto,
                                                      chq_estado,
                                                      chq_observacion,
                                                      chq_tipo_doc,
                                                      chq_deposito,
                                                      chq_cobro,
                                                      doc_id,
                                                      pag_id)
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
                                                      '$data[10]',
                                                      '$data[11]',
                                                      '$data[12]',
                                                      '$data[13]'
                                                        )");
        }
    }

    function buscar_cheques($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * from erp_cheques where pag_id=$id");
        }
    }

    function lista_cantidad_pagfac_id($doc, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_pagos_factura where com_id='$doc' and pag_id_chq=$id");
        }
    }
    
      //docuemntos secundarios
    
    function lista_notcre_factura($fac,$den) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nota_credito where ncr_num_comp_modifica='$fac' and ncr_denominacion_comprobante=$den  ");
        }
    }
    function lista_retencion_factura($fac,$den) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_registro_retencion where rgr_num_comp_retiene='$fac' and rgr_denominacion_comp=$den  ");
        }
    }
    
    function lista_det_ret($ret) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(drr_valor) from erp_det_reg_retencion where rgr_id=$ret group by drr_tipo_impuesto order by drr_tipo_impuesto desc");
        }
    }

      function lista_pagos_fecha_emisor($fecha, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query(" SELECT * FROM crosstab('select 
f.fac_fecha_emision,
cast(p.pag_forma as double precision),
sum(p.pag_cant) 
from erp_factura f,
erp_pagos_factura p 
where  cast(p.com_id as integer)=f.fac_id
and f.emi_id=$emi
and f.fac_fecha_emision=''$fecha''
group by f.fac_fecha_emision,p.pag_forma 
order by f.fac_fecha_emision,p.pag_forma
'::text, 'select l from generate_series(1,9) l'::text) crosstab(fecha text, tc text, td text, ch text, ef text, cer text, bon text, ret text, nc text, cre text);
");
        }
    }
    
     function lista_locales() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from emisor order by cod_orden");
        }
    }

    function lista_emisor_ptoemi($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where cod_punto_emision='$id'");
        }
    }
    
    function lista_pagos_factura($id){
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura WHERE com_id='$id'");
        }
    }

}

?>
