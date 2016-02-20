<?php

session_start();
include_once '../Clases/Conn.php';

class Cerrar_session {

    var $con;

    function Cerrar_session() {
        $this->con = new Conn();
    }

    function desactiva_usuario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_users set usu_status='f'  where usu_id=$id ");
        }
    }

    function insertComplette($dat) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_auditoria
                  ( usu_login,
                    adt_date,
                    adt_hour,
                    adt_ip,
                    
                    adt_modulo,
                    adt_accion,
                    adt_documento,
                    adt_campo)
                     VALUES( '$_SESSION[User]',
                            '" . date("Y-m-d") . "',
                            '" . date("H:i:s") . "',
                            '$_SERVER[REMOTE_ADDR]',    
                            '$dat[0]',
                            '$dat[1]',
                            '$dat[2]',
                            '$dat[3]'   ) ");
        }
    }

}

$sms = 0;
$Obj = new Cerrar_session();
if ($Obj->desactiva_usuario($_SESSION[usuid])) {
    $dat = array(
        $_REQUEST[mod],
        'Acceso Incorrecto',
        $_REQUEST[doc],
        $_REQUEST[camp]
    );

    $Obj->insertComplette($dat);
} else {
    $sms = pg_last_error();
}
echo $sms;
