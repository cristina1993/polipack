<?php

include_once '../Clases/clsClase_etiqueta_grande.php';
include_once("../Clases/clsAuditoria.php");
$Set = new Clase_etiqueta_grande();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            if ($Set->insert_etiquetas($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'ETIQUETA GRANDE ';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($Set->upd_etiquetas($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'ETIQUETA GRANDE';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Set->delete_etiqueta($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $id;
            $modulo = 'ETIQUETA GRANDE';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 2:
        $t = substr($id, 0, 1);
        $ord = substr($id, 0, 8);
        $pro = substr($id, 8, 9);
        if ($t == 'E') {
            $rst = pg_fetch_array($Set->lista_orden_extrusion($ord, $pro));
            switch ($pro) {
                case 2:
                    $pro_id = $rst[ord_pro_secundario];
                    break;
                case 3:
                    $pro_id = $rst[ord_pro3];
                    break;
                case 4:
                    $pro_id = $rst[ord_pro4];
                    break;
                default:
                    $pro_id = $rst[pro_id];
                    break;
            }
            $ord_id=$rst[ord_id];
            $peso_bruto = $rst[pro_propiedad4];
            $peso_neto = $rst[pro_peso];
            $core = $rst[pro_propiedad5];
            $tipo=0;
        } else if ($t == 'C') {
            $rst = pg_fetch_array($Set->lista_orden_corte(trim($ord), $pro));
            $ord_id=$rst[opp_id];
            $pro_id = $rst[pro_id];
            $peso_bruto = $rst[pro_propiedad7];
            $peso_neto = $rst[pro_medvul];
            $core = $rst[pro_capa];
            $tipo=1;
        }

        echo $pro_id . '&&' .$ord_id. '&&' . $rst[cli_raz_social]. '&&' . $rst[cli_id] . '&&' . $peso_bruto . '&&' . $peso_neto . '&&' . $rst[pro_espesor] . '&&' . $rst[pro_ancho] . '&&' . $core. '&&' . $tipo;
        break;
   
}
?>
