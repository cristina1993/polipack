<?php
//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase.php';
$Clase_factura = new Clase();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        if (empty($id)) {
            if ($Clase_factura->insert($data) == true) {
                $sms = 0;
            } else {
                $sms = pg_last_error();
            }
        } else {
            if ($Clase_factura->upd($data, $id) == true) {
                $sms = 0;
            } else {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_factura->delete($id) == true) {
            $sms = 0;
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        $rst=  pg_fetch_array($Clase_factura->lista_secuencial($id));
        $sec=  (substr($rst[pro_codigo],-5)+1);
        if($sec>=0 && $sec<10){
            $txt='0000';
        }else if($sec>=10 && $sec<100){
            $txt='000';
        }else if($sec>=100 && $sec<1000){
            $txt='00';
        }else if($sec>=1000 && $sec<10000){
            $txt='0';
        }else if($sec>=10000 && $sec<100000){
            $txt='';
        }
        
        $rst1=  pg_fetch_array($Clase_factura->lista_siglas($id));
        $retorno=$rst1[emp_sigla].$txt.$sec;
        
        $cns=$Clase_factura->lista_combomp($id);
        $combo="<option value='0'>Seleccione</option>";
        while($rst=  pg_fetch_array($cns)){
            $combo.="<option value='$rst[mpt_id]'>$rst[mpt_nombre]</option>";
        }
                
        echo $retorno.'&'.$combo;
        break;
}
?>
