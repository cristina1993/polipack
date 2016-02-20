<?php

//$_SESSION[User]='PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once("../Clases/clsClase_preciospt.php");
include_once '../Clases/clsClase_productos.php'; // cambiar clsClase_productos

$Prod = new Clase_Productos();
$Clases_preciospt = new Clase_preciospt();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$tab = $_REQUEST[tab];
$est = $_REQUEST[est];
$fields = $_REQUEST[fields]; //Datos para auditoria
$nom = $_REQUEST[nom];
$Adt = new Auditoria();
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $producto) {
                $str[$n] = strtoupper($producto);
                $n++;
            }

            if ($Prod->insert($str) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'PRODUCTOS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[1]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $producto) {
                $str[$n] = strtoupper($producto);
                $n++;
            }
            if ($Prod->upd($str, $id) == true) {
                $sms = 0;
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'PRODUCTOS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[1]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            } else {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Prod->delete($id) == true) {
            $sms = 0;
            if ($Clases_preciospt->del_pre($id, '0') == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                $f = $nom;
                $modulo = 'PRODUCTOS';
                $accion = 'ELIMINAR';
                if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        $rst = pg_fetch_array($Prod->lista_secuencial($id));
//        $sec=  (substr($rst[pro_codigo],-5)+1);
//        if($sec>=0 && $sec<10){
//            $txt='0000';
//        }else if($sec>=10 && $sec<100){
//            $txt='000';
//        }else if($sec>=100 && $sec<1000){
//            $txt='00';
//        }else if($sec>=1000 && $sec<10000){
//            $txt='0';
//        }else if($sec>=10000 && $sec<100000){
//            $txt='';
//        }
//        $rst1=  pg_fetch_array($Prod->lista_siglas($id));
//        $retorno=$rst1[emp_sigla].$txt.$sec;

        $cns = $Prod->lista_combomp($id);
        $combo = "<option value='0'>Seleccione</option>";
        while ($rst = pg_fetch_array($cns)) {
            $combo.="<option value='$rst[mp_id]'>$rst[mp_referencia]</option>";
        }
        echo $retorno . '&' . $combo;
        break;
    case 3:
        $sms = 0;
        if ($tab == 0) {
            if ($Prod->update_estado_com($est, $id) == false) {
                $sms = pg_last_error();
            }
        } else {
            if ($Prod->update_estado_ind($est, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
}
?>
