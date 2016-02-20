<?php
include_once '../Clases/clsClase_inv_online.php';
include_once("../Clases/clsAuditoria.php");
$Inv = new Inv_Online();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$campo = $_REQUEST[campo];
$val = $_REQUEST[val];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        $sms=0;
        if(!$Inv->update_inv_general($id,$campo,$val)){
            $sms=  pg_last_error();
        }
        echo $sms;
        break;
}
?>
