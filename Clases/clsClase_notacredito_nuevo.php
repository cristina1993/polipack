<?php

include_once 'Conn.php';

class Clase_nota_Credito_nuevo {

    var $con;

    function Clase_nota_Credito_nuevo() {
        $this->con = new Conn();
    }

    function lista_un_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_ced_ruc='$id'");
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

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where cod_punto_emision='$id'");
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

    function lista_productos_noperti() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos ORDER BY pro_b");
        }
    }

    function lista_productos_industrial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos ORDER BY pro_descripcion ");
        }
    }

    function lista_un_producto_noperti($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code'");
        }
    }

    function lista_un_producto_noperti_id($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where id=$code and pro_estado=0");
        }
    }

    function lista_precio_producto($id, $tabla) {//////
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios where pro_id=$id and pro_tabla=$tabla");
        }
    }

    function lista_un_producto_industrial($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$code'");
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
  cli_codigo,
  cli_telefono,
  cli_email
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
'$data[5]')");
        }
    }

    function lista_un_cliente_cedula($cod) {// sirve para cuando selecciono un registro para modificar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente WHERE cli_ced_ruc='$cod'");
        }
    }

    function lista_producto_total($ems) {
        if ($this->con->Conectar() == true) {

            if ($ems == 1) { //Nopeti (todos los comerciales + paddin y plumos)
                $query = "(SELECT '1' as tbl,id as id,pro_ac as lote,pro_a as codigo,pro_b as descripcion FROM  erp_productos where pro_estado=0 
                           union
                           SELECT '0' as tbl, pro_id as id, '' as lote ,pro_codigo as codigo,pro_descripcion as descripcion FROM  erp_i_productos where pro_estado=0 and (emp_id=3 or emp_id=4)) order by descripcion";
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

    function lista_un_producto_noperti_cod_lote($code, $lote) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code' and pro_ac='$lote' and pro_estado=0");
        }
    }

/// Nota nuevas tablas
//
    function lista_secuencial_nota_credito($bod) {
        if ($this->con->Conectar() == true) {
            if ($this->con->Conectar() == true) {
                return pg_query("SELECT * FROM  erp_nota_credito where emi_id='$bod' order by ncr_numero desc limit 1");
            }
        }
    }

    function lista_vendedor($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM  erp_vendedores where vnd_nombre='$txt'");
        }
    }

    function insert_nota_credito($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_nota_credito(
                            cli_id,
                            emi_id,
                            vnd_id,
                            ncr_numero,
                            ncr_motivo,
                            ncr_fecha_emision,
                            ncr_nombre,
                            ncr_identificacion,
                            ncr_email,
                            ncr_direccion ,
                            ncr_denominacion_comprobante,
                            ncr_num_comp_modifica ,
                            ncr_fecha_emi_comp,
                            ncr_subtotal12,
                            ncr_subtotal0,
                            ncr_subtotal_ex_iva ,
                            ncr_subtotal_no_iva ,
                            ncr_total_descuento ,
                            ncr_total_ice ,
                            ncr_total_iva ,
                            ncr_irbpnr,
                            nrc_telefono ,
                            nrc_total_valor,        
                            ncr_total_propina,        
                            fac_id,
                            trs_id,
                            ncr_subtotal
                            )VALUES(
                            $data[0],
                            $data[1],
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
                            '$data[23]',
                            '$data[24]',
                            '$data[25]',
                            '$data[26]')");
        }
    }

    function lista_un_notac_num($fac) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito where ncr_numero='$fac'");
        }
    }

    function insert_det_nota_credito($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_nota_credito(
                                   pro_id, 
                                   ncr_id, 
                                   dnc_codigo, 
                                   dnc_cod_aux, 
                                   dnc_cantidad, 
                                   dnc_descripcion, 
                                   dnc_precio_unit, 
                                   dnc_porcentaje_descuento, 
                                   dnc_val_descuento,
                                   dnc_precio_total, 
                                   dnc_iva, 
                                   dnc_ice,
                                   dnc_irbpnr,
                                   dnc_p_ice,
                                   dnc_cod_ice,
                                   dnc_p_irbpnr,
                                   dnc_lote,
                                   dnc_tab
                                    )VALUES(
                                   '$data[0]',
                                   '$id',
                                   '$data[1]',
                                   '$data[2]',
                                   '$data[3]',
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
                                   '$data[16]'
                                     )");
        }
    }

    function lista_un_producto_industrial_id($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$code and pro_estado=0");
        }
    }

    function insert_movimiento($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_fecha_trans,
                mov_fecha_registro,
                mov_hora_registro,
                mov_cantidad,
                mov_tabla,
                mov_usuario
                            )
    VALUES (
                    $data[0],
                    $data[1],
                    $data[2],
                    $data[3],
                    '$data[4]',
                    '$data[5]',
                    '" . date('Y-m-d') . "',
                    '" . date("H:i:s") . "',
                    '$data[6]',
                    '$data[7]',
                    '$data[8]'
                    )");
        }
    }

     function lista_secuencial_locales($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT n.ncr_numero as secuencial FROM  erp_nota_credito n, emisor e where n.emi_id=e.cod_punto_emision and n.emi_id=$emi order by n.ncr_numero desc limit 1");
        }
    }
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
