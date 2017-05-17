<?php

include_once 'Conn.php';

class Clase_reg_ecocambrella {

    var $con;

    function Clase_reg_ecocambrella() {
        $this->con = new Conn();
    }

    function lista_buscador_orden($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_op_ecocambrella r, erp_i_orden_produccion o, erp_i_productos p where r.ord_id=o.ord_id and r.pro_id=p.pro_id $txt order by rec_numero");
        }
    }

    function lista_secuencial_registro() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_op_ecocambrella order by rec_numero desc limit 1");
        }
    }

    function lista_un_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos where pro_id=$id");
        }
    }

    function lista_una_orden_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion o, erp_i_cliente c where o.cli_id=c.cli_id and o.ord_num_orden='$id'");
        }
    }

    function insert_registro_ecocambrella($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_reg_op_ecocambrella(
                                        ord_id,					
                                        rec_fecha, 
                                        rec_numero,
                                        rec_lote,
					rec_peso_primario, 
					rec_rollo_primario,
                                        rec_estado,
                                        pro_id,
                                        maq_id,
                                        rec_observaciones
                                       )
                              VALUES ( '$data[0]',
                                       '$data[1]',
                                       '$data[2]',
                                       '$data[3]',
                                       '$data[4]',
                                       '$data[5]',
                                       '$data[6]',
                                       '$data[7]',
                                       '$data[8]',
                                       '$data[9]')");
        }
    }

    function update_registro_ecocambrella($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_op_ecocambrella
					SET rec_fecha='$data[0]', 
					rec_peso_primario='$data[1]', 
					rec_peso_secundario='$data[2]', 
					rec_rollo_primario='$data[3]',
					rec_rollo_secundario='$data[4]', 
					rec_desperdicio='$data[5]', 
					rec_operador='$data[6]', 
					ord_id='$data[7]',
					rec_numero='$data[8]',
					rec_lote='$data[9]',
					rec_lote2='$data[10]',
					where rec_id=$id");
        }
    }

    function delete_registro_ecocambrella($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE from erp_reg_op_ecocambrella WHERE rec_id=$id");
        }
    }

    function lista_produccion_pedido($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(rec_rollo_primario) as rollo,sum(rec_rollo_secundario) as rollo2,sum(rec_peso_primario) as peso,sum(rec_peso_secundario) as peso2 FROM erp_reg_op_ecocambrella where ord_id=$id");
        }
    }

    function lista() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_orden_produccion_geotexti e, erp_i_productos p, erp_empresa m, erp_i_clientes c where e.pro_id=p.pro_id and m.emp_id=e.emp_id and c.cli_id=e.cli_id ORDER BY opg_id DESC");
        }
    }

    function lista_mostrar($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$emp"); //no esta cojiendo de la tabala productos esta cojiendo de la misma tabla geotexti
        }
    }

    function lista_recupera($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos p, erp_i_orden_produccion_geotexti e where p.pro_id=e.pro_id and p.pro_id=$emp ORDER BY e.opg_id DESC LIMIT 1"); //no esta cojiendo de la tabala productos esta cojiendo de la misma tabla geotexti
        }
    }

    function lista_secuencial($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_orden_produccion_geotexti p, erp_empresa e 
where e.emp_id=p.emp_id 
and e.emp_id=$emp ORDER BY p.opg_codigo DESC LIMIT 1");
        }
    }

    function lista_siglas($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id=$emp");
        }
    }

    function lista_uno($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_orden_produccion_geotexti where opg_id=$id");
        }
    }

    function lista_buscador($fec1, $fec2, $ord, $st) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_geotexti op, erp_i_productos p, erp_empresa e, erp_i_cliente c where p.pro_id=op.pro_id and e.emp_id=op.emp_id and c.cli_id=op.cli_id and op.opg_fec_pedido between '$fec1' and '$fec2' and op.opg_codigo like '%$ord%' and op.opg_status like '%$st%'");
        }
    }

    function insert($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_orden_produccion_geotexti(         
              opg_codigo,
              cli_id,
              pro_id,
              emp_id,
              opg_num_rollos,
              opg_peso_producir,
              opg_fec_pedido,
              opg_fec_entrega,
              pro_mp1,
              pro_mp2,
              pro_mp3,
              pro_mp4,
              pro_mf1,
              pro_mf2,
              pro_mf3,
              pro_mf4,
              opg_kg1,
              opg_kg2,
              opg_kg3,
              opg_kg4,
              opg_ancho_total,
              opg_prod_principal,
              pro_largo,
              pro_ancho,
              pro_gramaje,
              opg_refilado,
              opg_caja1,
              opg_caja2,
              opg_caja3,
              opg_vel_transporte,
              opg_frecuencia,
              opg_capas,
              opg_doffer,
              opg_front,
              opg_random,
              opg_conveyor,
              opg_compensacion,
              opg_sensor1,
              opg_sensor2,
              opg_sensor3,
              opg_sensor4,
              opg_sensor5,
              opg_sensor6,
              opg_dosi_alimentacion,
              opg_mot_alimentacion,
              opg_mot_carda2,
              opg_mot_cilindro,
              opg_mot_gramaje,
              opg_hz,
              opg_vel_trans_madera,
              opg_vel_trans_caucho,
              opg_num_punzonadora,
              opg_vel_rod_salida,
              opg_vel_rod_compensadores,
              opg_vel_rod_entradawinder,
              opg_numpunzo_winder,
              opg_velrod_salidawinder,
              opg_numgolpes_punzo,
              opg_vel_enrolladora,
              opg_rev_min_calan,
              opg_observaciones,
              opg_status
              )
    VALUES ('$data[0]',$data[1],$data[2],6,$data[3],'$data[4]','$data[5]','$data[6]',$data[7],$data[8],$data[9],$data[10],'$data[11]','$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]','$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]','$data[24]','$data[25]','$data[26]','$data[27]','$data[28]','$data[29]',$data[30],'$data[31]','$data[32]','$data[33]','$data[34]','$data[35]','$data[36]','$data[37]','$data[38]','$data[39]','$data[40]','$data[41]','$data[42]','$data[43]','$data[44]','$data[45]','$data[46]','$data[47]','$data[48]','$data[49]','$data[50]','$data[51]','$data[52]','$data[53]','$data[54]','$data[55]','$data[56]','$data[57]','$data[58]','$data[59]','PROGRAMADO')");
        }
    }

    function upd($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_orden_produccion_geotexti SET opg_codigo='$data[0]',
                    cli_id=$data[1],
                    pro_id=$data[2],
                    opg_num_rollos=$data[3],
                    opg_peso_producir='$data[4]',
                    opg_fec_pedido='$data[5]',
                    opg_fec_entrega='$data[6]',
                    pro_mp1=$data[7],
                    pro_mp2=$data[8],
                    pro_mp3=$data[9],
                    pro_mp4=$data[10],
                    pro_mf1='$data[11]',
                    pro_mf2='$data[12]',
                    pro_mf3='$data[13]',
                    pro_mf4='$data[14]',
                    opg_kg1='$data[15]',
                    opg_kg2='$data[16]',
                    opg_kg3='$data[17]',
                    opg_kg4='$data[18]',
                    opg_ancho_total='$data[19]',
                    opg_prod_principal='$data[20]',
                    pro_largo='$data[21]',
                    pro_ancho='$data[22]',
                    pro_gramaje='$data[23]',
                    opg_refilado='$data[24]',
                    opg_caja1='$data[25]',
                    opg_caja2='$data[26]',
                    opg_caja3='$data[27]',
                    opg_vel_transporte='$data[28]',
                    opg_frecuencia='$data[29]',
                    opg_capas='$data[30]',
                    opg_doffer='$data[31]',
                    opg_front='$data[32]',
                    opg_random='$data[33]',
                    opg_conveyor='$data[34]',
                    opg_compensacion='$data[35]',
                    opg_sensor1='$data[36]',
                    opg_sensor2='$data[37]',
                    opg_sensor3='$data[38]',
                    opg_sensor4='$data[39]',
                    opg_sensor5='$data[40]',
                    opg_sensor6='$data[41]',
                    opg_dosi_alimentacion='$data[42]',
                    opg_mot_alimentacion='$data[43]',
                    opg_mot_carda2='$data[44]',
                    opg_mot_cilindro='$data[45]',
                    opg_mot_gramaje='$data[46]',
                    opg_hz='$data[47]',
                    opg_vel_trans_madera='$data[48]',
                    opg_vel_trans_caucho='$data[49]',
                    opg_num_punzonadora='$data[50]',
                    opg_vel_rod_salida='$data[51]',
                    opg_vel_rod_compensadores='$data[52]',
                    opg_vel_rod_entradawinder='$data[53]',
                    opg_numpunzo_winder='$data[54]',
                    opg_velrod_salidawinder='$data[55]',
                    opg_numgolpes_punzo='$data[56]',
                    opg_vel_enrolladora='$data[57]',
                    opg_rev_min_calan='$data[58]',
                    opg_observaciones='$data[59]' WHERE opg_id=$id");
        }
    }

    function delete($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_orden_produccion_geotexti WHERE opg_id=$id");
        }
    }

    function lista_combo() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_empresa ORDER BY emp_descripcion");
        }
    }

    function lista_combocli($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres  
from  erp_i_cliente 
where cli_tipo <>'$tp'
order by nombres");
        }
    }

    function lista_combopro($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT *FROM erp_i_productos where emp_id=$emp ORDER BY pro_descripcion ");
        }
    }

    function lista_combomp($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mp m, erp_i_tpmp t where m.mpt_id=t.mpt_id and m.emp_id=6 ORDER BY m.mp_referencia");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////  
    function lista_secuencial_transferencia() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_secuencial_semielaborado   ORDER BY sec_id DESC LIMIT 1");
        }
    }

    function insert_sec_transferencia($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_secuencial_semielaborado(sec_transferencias) VALUES ('$data')");
        }
    }

    function insert_transferencia($data) {
        if ($this->con->Conectar() == true) {
            $f = date('Y-m-d');
            $h = date('H:i');
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_mov_inv_semielaborado(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_cantidad,                
                mov_tabla,                
                mov_fecha_registro,
                mov_hora_registro,
                mov_usuario,
                mov_pago,
                mov_flete
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$data[4]',   
            '$data[5]',
            '$data[6]',   
            '$data[7]',
            '$data[8]',
            '$f',
            '$h',
            '$usu',
            '$data[9]',
            '$data[10]')");
        }
    }

    function lista_una_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion where ord_id=$id");
        }
    }

    function update_estado_mov($id, $lt, $std, $ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_movse_total SET estado='$std' WHERE pro_id='$id' and pro_lote='$lt' and mvt_orden='$ord'");
        }
    }

    function lista_secuencial_transferencia_terminado() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_secuencial  ORDER BY sec_id DESC LIMIT 1");
        }
    }

    function insert_sec_transferencia_terminado($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_secuencial(sec_transferencias) VALUES ('$data')");
        }
    }

    function insert_transferencia_terminado($data) {
        if ($this->con->Conectar() == true) {
            $f = date('Y-m-d');
            $h = date('H:i');
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_cantidad,                
                mov_tabla,                
                mov_fecha_registro,
                mov_hora_registro,
                mov_usuario,
                mov_pago,
                mov_flete
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$data[4]',   
            '$data[5]',
            '$data[6]',   
            '$data[7]',
            '$data[8]',
            '$f',
            '$h',
            '$usu',
            '$data[9]',
            '$data[10]')");
        }
    }

    function lista_maquinas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_maquinas where ids='1' ORDER BY maq_a");
        }
    }

    function lista_registros_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_rec_ecoacambrella where ord_id='$id'");
        }
    }

    function lista_ordenes() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ord_num_orden,pro_descripcion FROM erp_i_orden_produccion o, erp_i_productos p where o.pro_id=p.pro_id and (select sum(rec_peso_primario) from erp_reg_op_ecocambrella p where p.ord_id=o.ord_id)<(o.ord_kgtotal+o.ord_kgtotal2+o.ord_kgtotal3+o.ord_kgtotal4)
                            union 
                            select ord_num_orden,pro_descripcion FROM erp_i_orden_produccion o, erp_i_productos p where o.pro_id=p.pro_id and not exists(select * from erp_reg_op_ecocambrella p where p.ord_id=o.ord_id) order by ord_num_orden desc
                            ");
        }
    }

    function lista_mp($fbc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mp mp,
erp_i_tpmp tmp,
erp_empresa em 
WHERE mp.emp_id=em.emp_id
AND   mp.mpt_id=tmp.mpt_id
AND   mp.emp_id=$fbc
ORDER BY mp.mp_id ");
        }
    }

    function registros_productos($ord, $p1, $p2, $p3, $p4) {
        if ($this->con->Conectar() == true) {
            return pg_query("   select 1 as prod,rec_id,ord_id,rec_peso_primario,pro_id,rec_lote,rec_estado from erp_reg_op_ecocambrella where pro_id=$p1 and ord_id=$ord
                                union  
                                select 2 as prod,rec_id,ord_id,rec_peso_primario,pro_id,rec_lote,rec_estado from erp_reg_op_ecocambrella where pro_id=$p2 and ord_id=$ord 
                                union  
                                select 3 as prod,rec_id,ord_id,rec_peso_primario,pro_id,rec_lote,rec_estado from erp_reg_op_ecocambrella where pro_id=$p3 and ord_id=$ord 
                                union 
                                select 4 as prod,rec_id,ord_id,rec_peso_primario,pro_id,rec_lote,rec_estado from erp_reg_op_ecocambrella where pro_id=$p4 and ord_id=$ord"
                    . "         order by rec_id");
        }
    }

    function lista_etiquetas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_etiquetas order by eti_descripcion");
        }
    }

    function insert_etiqueta_pallets($data) {
        if ($this->con->Conectar() == true) {
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_etiqueta_pallets(
                ord_id, 
                pro_id, 
                pal_numero, 
                pal_rollos, 
                pal_peso,
                pal_usuario,
                pal_estado
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$data[4]',   
            '$usu',
             '$data[5]'      
            )");
        }
    }

    function update_etiqueta_pallets($data, $id) {
        if ($this->con->Conectar() == true) {
            $usu = strtoupper($_SESSION[User]);
            return pg_query("UPDATE erp_etiqueta_pallets set
                ord_id='$data[0]', 
                pro_id='$data[1]', 
                pal_numero='$data[2]', 
                pal_rollos='$data[3]', 
                pal_peso='$data[4]',
                pal_usuario='$usu',
                pal_estado='$data[5]'
                    where pal_id=$id
            ");
        }
    }

    function lista_pallets($id, $ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_etiqueta_pallets where ord_id=$ord and pro_id=$id order by pal_id desc");
        }
    }

    function lista_pallets_suma($id, $ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(pal_numero) as pal_numero  FROM erp_etiqueta_pallets where ord_id=$ord and pro_id=$id group by ord_id,pro_id");
        }
    }

    function insert_etiqueta($data) {
        if ($this->con->Conectar() == true) {
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_etiqueta_grande(
                ord_id, 
                pro_id, 
                cli_id, 
                etg_tipo, 
                etg_numero,
                etg_copias,
                etg_fecha,
                etg_pallet1,
                etg_estado,
                etg_procedencia,
                etg_tamano,
                etg_peso_neto,
                etg_peso_bruto,
                etg_espacio8,
                etg_operador,
                etg_observaciones
            )
    VALUES ('$data[17]',
            '$data[18]',
            '$data[19]',   
            '0',
            '$data[0]',   
            '$data[8]',   
            '$data[9]',      
            '$data[5]',      
            '$data[6]',      
            '1',      
            '$data[7]',         
            '$data[12]',         
            '$data[2]',
            '$data[20]',    
            '$data[21]',    
            '$data[22]'    
            )");
        }
    }

    function lista_cambia_status_det($id, $sts, $pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_det_ped_venta SET det_estado='$sts' where ped_id=$id and pro_id=$pro");
        }
    }

    function lista_cambia_status_pedido($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta SET ped_estado='$sts' where ped_id=$id");
        }
    }

}

?>
