<?php

include_once 'Conn.php';

class Clase_guia_remision {

    var $con;

    function Clase_guia_remision() {
        $this->con = new Conn();
    }

    function lista_facturas($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM comprobantes where tipo_comprobante=1 and cod_punto_emision=$emi ORDER BY num_secuencial");
        }
    }

    function lista_facturas_fec($desde, $hasta, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM comprobantes where fecha_emision >='$desde' and fecha_emision <='$hasta' and tipo_comprobante=1 and cod_punto_emision=$emi ORDER BY num_secuencial");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where cod_punto_emision='$id'");
        }
    }

    function lista_un_transportista($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM transportista where identificacion='$id'");
        }
    }

    function lista_transportista() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM transportista");
        }
    }

    function delete_guia_remision($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM guia_remision WHERE num_comprobante= '$id'"
            );
        }
    }

    function lista_cantidad($cod, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_guia where pro_id='$cod' and gui_id='$id' ");
        }
    }

    function lista_buscar_transportistas($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  transportista where 
                (CAST(identificacion AS VARCHAR) like '%$txt%' 
                    or razon_social like '%$txt%'  
                        or placa like '%$txt%' 
                          ) 
                                                        Order by razon_social");
        }
    }

    function insert_transportista($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO transportista(
                        identificacion,
                        razon_social,
                        placa
                        )
            VALUES ('$data[0]','$data[1]','$data[2]')");
        }
    }

    function lista_guias() {///////////
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT num_comprobante, num_comprobante_venta,fecha_emision,fecha_inicio_transporte,fecha_fin_transporte,motivo_traslado,destino,identificacion_destinario,nombre_destinatario, punto_partida, destino,com_observacion,com_estado,com_autorizacion,fecha_hora_autorizacion,clave_acceso FROM guia_remision group by num_comprobante, num_comprobante_venta,fecha_emision,fecha_inicio_transporte,fecha_fin_transporte,motivo_traslado,destino,identificacion_destinario,nombre_destinatario, punto_partida, destino,com_observacion,com_estado,com_autorizacion,fecha_hora_autorizacion,clave_acceso");
        }
    }

    function lista_guias_no_autorizadas() {///////////
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT num_comprobante, num_comprobante_venta,fecha_emision,fecha_inicio_transporte,fecha_fin_transporte,motivo_traslado,destino,identificacion_destinario,nombre_destinatario, punto_partida, destino,com_observacion,com_estado,com_autorizacion,fecha_hora_autorizacion,clave_acceso,vendedor 
FROM guia_remision 
where com_autorizacion is null or com_autorizacion='' or com_autorizacion='nullnull'
group by num_comprobante, num_comprobante_venta,fecha_emision,fecha_inicio_transporte,fecha_fin_transporte,motivo_traslado,destino,identificacion_destinario,nombre_destinatario, punto_partida, destino,com_observacion,com_estado,com_autorizacion,fecha_hora_autorizacion,clave_acceso,vendedor
");
        }
    }

    function upd_guia_clave_acceso($clave, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update guia_remision 
                set clave_acceso='$clave'  where num_comprobante=$id ");
        }
    }

    function upd_guia_na($na, $fh, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_guia_remision 
                set gui_estado_aut='RECIBIDA AUTORIZADO', gui_fec_hora_aut='$fh' , gui_autorizacion='$na'  where gui_clave_acceso='$id' ");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
////////////////////////////////////////////////////////////////////////////  
    ///nuevas tablas///
    function lista_buscador_facturas($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_factura $txt ORDER BY fac_numero");
        }
    }

    function lista_buscador_guias($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_guia_remision $txt ORDER BY gui_numero");
        }
    }
    
     function lista_buscador_guias_fac($txt) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM erp_guia_remision g, erp_factura f where g.fac_id=f.fac_id $txt ORDER BY g.gui_numero");
            return pg_query("SELECT f.fac_id,f.fac_numero,f.fac_fecha_emision,f.fac_identificacion,f.fac_nombre,f.fac_autorizacion FROM erp_guia_remision g, erp_factura f where g.fac_id=f.fac_id $txt group by f.fac_id,f.fac_numero,f.fac_fecha_emision,f.fac_identificacion,f.fac_nombre,f.fac_autorizacion");
        }
    }

    function lista_guias_fec($desde, $hasta, $emi) {///////////
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_guia_remision where gui_fecha_emision between '$desde' and '$hasta' and emi_id=$emi");
        }
    }

    function lista_una_factura_numdoc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura where fac_numero='$id'");
        }
    }

    function lista_guias_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_guia_remision g,transportista t,erp_vendedores v where g.tra_id=t.id and g.vnd_id=v.vnd_id and g.fac_id='$id' ");
        }
    }

    function lista_una_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_factura where fac_id='$id'");
        }
    }

    function lista_detalle_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_factura where fac_id='$id'");
        }
    }

    function suma_cantidad_entregado($cod, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(dtg_cantidad) as suma FROM erp_det_guia d, erp_guia_remision g where g.gui_id=d.gui_id and d.pro_id='$cod' and g.fac_id='$id' ");
        }
    }

    function lista_vendedor($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM  erp_vendedores where vnd_nombre='$txt'");
        }
    }

    function insert_guia_remision($data, $num, $tra) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_guia_remision(
            vnd_id, 
            emi_id, 
            cli_id, 
            gui_numero, 
            gui_fecha_emision, 
            gui_fecha_inicio, 
            gui_fecha_fin,
            gui_motivo_traslado, 
            gui_punto_partida, 
            gui_destino, 
            gui_identificacion, 
            gui_nombre, 
            gui_identificacion_transp, 
            gui_doc_aduanero, 
            gui_cod_establecimiento, 
            gui_num_comprobante,
            gui_observacion,
            fac_id,
            tra_id,
            gui_denominacion_comp,
            gui_aut_comp,
            gui_fecha_comp
                           )
           VALUES (
            '$data[0]',
            '$data[1]',
            '$data[2]',
            '$num',
            '$data[4]',
            '$data[5]',
            '$data[6]',
            '" . strtoupper($data[7]) . "',
            '" . strtoupper($data[8]) . "',
            '" . strtoupper($data[9]) . "',
            '" . strtoupper($data[10]) . "',
            '$data[11]',
            '$data[12]',
            '$data[13]',
            '$data[14]',
            '$data[15]',
            '" . strtoupper($data[16]) . "',
            '$data[17]',
            '$tra',
            '$data[19]',
            '$data[20]',
            '$data[21]')");
        }
    }

    function insert_det_guia_remision($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_guia(
            gui_id, 
            dtg_cantidad, 
            dtg_codigo, 
            dtg_cod_aux, 
            dtg_descripcion,
            pro_id,
            dtg_lote,
            dtg_tab)
            VALUES ($id,'$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]')");
        }
    }

    function update_guia_remision($data, $id, $tra) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_guia_remision SET
         vnd_id='$data[0]', 
            emi_id='$data[1]', 
            cli_id='$data[2]', 
            gui_numero='$data[3]', 
            gui_fecha_emision='$data[4]', 
            gui_fecha_inicio='$data[5]', 
            gui_fecha_fin='$data[6]',
            gui_motivo_traslado='" . strtoupper($data[7]) . "', 
            gui_punto_partida='" . strtoupper($data[8]) . "', 
            gui_destino='" . strtoupper($data[9]) . "', 
            gui_identificacion='" . strtoupper($data[10]) . "', 
            gui_nombre='$data[11]', 
            gui_identificacion_transp='" . strtoupper($data[12]) . "', 
            gui_doc_aduanero='$data[13]', 
            gui_cod_establecimiento='$data[14]', 
            gui_num_comprobante='$data[15]',
            gui_observacion='" . strtoupper($data[16]) . "',
            fac_id='$data[17]',
            tra_id='$tra',
            gui_denominacion_comp='$data[19]',
            gui_aut_comp='$data[20]',
            gui_fecha_comp='$data[21]' where gui_id=$id");
        }
    }

    function lista_secuencial_documento($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_guia_remision where emi_id='$bod' order by gui_numero desc limit 1");
        }
    }

    function lista_una_guia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_guia_remision where gui_numero='$id'");
        }
    }

    function lista_una_guia_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_guia_remision g,transportista t,erp_vendedores v, erp_i_cliente c where g.cli_id=c.cli_id and g.tra_id=t.id and g.vnd_id=v.vnd_id and g.gui_id='$id'");
        }
    }

    function delete_det_guia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_det_guia WHERE gui_id= '$id'"
            );
        }
    }

    function lista_detalle_guia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_guia where gui_id='$id'");
        }
    }
    
    function lista_secuencial_locales($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT g.gui_numero as secuencial FROM  erp_guia_remision g, emisor e where g.emi_id=e.cod_punto_emision and g.emi_id=$emi order by g.gui_numero desc limit 1");
        }
    }

}

?>