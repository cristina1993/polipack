<?php

include('../Includes/nusoap.php');

class SRI {

    function Conectar() {
        return pg_connect('host=localhost'
                . ' port=5432 '
                . ' dbname=noperti'
                . ' user=postgres'
                . ' password=SuremandaS495');
    }

    function recupera_datos($clave, $amb) {
        if ($amb == 2) { //Produccion
            $wsdl = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl', 'wsdl');
        } else {      //Pruebas
            $wsdl = new nusoap_client('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl', 'wsdl');
        }

        $req = $wsdl->call('autorizacionComprobante', array("claveAccesoComprobante" => $clave));
        $res = $req[RespuestaAutorizacionComprobante][autorizaciones][autorizacion];
        if (empty($res[0])) {
            return $respuesta = array($res[estado], $res[numeroAutorizacion], $res[fechaAutorizacion], $res[ambiente], $res[comprobante], $res[mensajes][mensaje][mensaje]);
        } else {
            return $respuesta = array($res[0][estado], $res[0][numeroAutorizacion], $res[0][fechaAutorizacion], $res[0][ambiente], $res[0][comprobante], $res[0][mensajes][mensaje][mensaje]);
        }
    }

    function documentos_noautorizados() {
        if ($this->Conectar() == true) {
            return pg_query("select * from comprobantes where position('AUTORIZADO' in upper(com_estado))=0 and upper(com_estado)<>'ANULADO' and clave_acceso is not null");
        }
    }

    function documentos_noenviados() {
        if ($this->Conectar() == true) {
            return pg_query("select * from comprobantes where (char_length(com_autorizacion)<>37 or  com_autorizacion is null ) and tipo_comprobante=1");
        }
    }

    function actualizar_datos_documentos($estado, $auto, $fecha, $id) {
        if ($this->Conectar() == true) {
            return pg_query("UPDATE comprobantes 
                SET com_estado='RECIBIDA $estado',
                    com_autorizacion='$auto',
                    fecha_hora_autorizacion='$fecha'    
                WHERE com_id=$id ");
        }
    }

    function registra_errores($data) {
        if ($this->Conectar() == true) {
            return pg_query("INSERT INTO 
                erp_auditoria(
                usu_id,
                adt_date,
                adt_hour,
                adt_modulo,
                adt_accion,
                adt_documento,
                adt_campo,
                usu_login
                )VALUES(
                '$data[0]',
                '$data[1]',
                '$data[2]',
                '$data[3]',
                '$data[4]',    
                '$data[5]',
                '$data[6]',
                '$data[7]' ) ");
        }
    }

    function productos_facturados() {
        if ($this->Conectar() == true) {
            return pg_query("select 
                                pr.id,
                                dt.num_camprobante,
                                dt.lote,
                                dt.cod_producto,
                                dt.cantidad
                                from detalle_fact_notdeb_notcre dt
                                join erp_productos pr 
                                on(pr.pro_a=dt.cod_producto 
                                and pr.pro_ac=dt.lote 
                                and dt.tipo_comprobante=1 )");
        }
    }

    function busca_prod_mov($id, $fac) {
        if ($this->Conectar() == true) {
            return pg_query("select * from erp_i_mov_inv_pt 
                                where mov_tabla=1
                                and trs_id=25
                                and pro_id=$id
                                and mov_num_factura='$fac' ");
        }
    }

    function upd_documentos($dat, $id) {
        if ($this->Conectar() == true) {
            return pg_query("update comprobantes 
                set clave_acceso='$dat[0]', 
                com_estado='$dat[1] $dat[2]', 
                com_observacion='$dat[3]', 
                com_autorizacion='$dat[4]',
                fecha_hora_autorizacion='$dat[5]',                    
                xml_doc='$dat[6]'                        
                where com_id=$id ");
        }
    }

    function prueba($dat) {
        if ($this->Conectar() == true) {
            return pg_query("insert into prueba (prb_nm) values ('$dat') ");
        }
    }

}
