<?php

//$_SESSION[User]='PRUEBA';
set_time_limit(0);
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once("../Clases/clsClase_inv_fisico.php");
include_once '../Clases/clsClase_industrial_inventariopt.php';
$Clase_inv_fisico = new Clase_inv_fisico();
$Inv_general = new Clase_industrial_inventariopt();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$bod = $_REQUEST[bod];
$auditor = $_REQUEST[auditor];
$fields = $_REQUEST[fields]; //Datos para auditoria
//$emisor = $_GET[emisor];
$Adt = new Auditoria();
switch ($op) {
    case 0:
        $sms = 0;
        if ($Inv_general->limpiar_movpt_total() == false) {
            $sms = pg_last_error();
        } else {
            $n = 1;
            while ($n <= 14) {
                $cns = $Inv_general->lista_inv_productos($n);
                while ($rst = pg_fetch_array($cns)) {
                    $rst_inv = pg_fetch_array($Inv_general->total_ingreso_egreso($rst[pro_id], $n, $rst[mov_tabla]));
                    $inv = $rst_inv[ingreso] - $rst_inv[egreso];
//                    $rst_inv_tot = pg_fetch_array($Inv_general->lista_inv_prod_local($rst[pro_id], $rst[mov_tabla], $n));
//                    if ($rst_inv_tot[mvt_cant] != '') {
//                        $cant = $inv + $rst_inv_tot[mvt_cant];
//                        if ($Inv_general->update_cantidad($cant, $rst[pro_id], $n, $rst[mov_tabla]) == false) {
//                            $sms = pg_last_error();
//                        }
//                    } else {
                    $data = Array(
                        $rst[pro_id],
                        $rst[mov_tabla],
                        $inv,
                        date('Y-m-d'),
                        $n
                    );
                    if ($Inv_general->insert_movpt_total($data) == false) {
                        $sms = pg_last_error();
                    }
//                    }
                }
                $n++;
            }
        }
        echo $sms;
        break;
}
?>
