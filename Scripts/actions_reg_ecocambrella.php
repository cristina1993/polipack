<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_reg_ecocambrella.php';
$Set = new Clase_reg_ecocambrella();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$s = $_REQUEST[s];
$fec = $_REQUEST[fec];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $cliente) {
                $str[$n] = strtoupper($cliente);
                $n++;
            }
            if ($Set->insert_registro_ecocambrella($str) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'REG. PRODUCCION ECOCAMBRELLA';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[8]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $cliente) {
                $str[$n] = strtoupper($cliente);
                $n++;
            }
            if ($Set->update_registro_ecocambrella($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'REG. PRODUCCION ECOCAMBRELLA';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[8]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($Set->delete_registro_ecocambrella($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'REG. PRODUCCION ECOCAMBRELLA';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                $sms = "Auditoria" . pg_last_error();
            }
        }
        echo $sms;
        break;
    case 2:
        $sms;
        $rst = pg_fetch_array($Set->lista_una_orden_cod(trim(strtoupper($id))));
        $rst1 = pg_fetch_array($Set->lista_un_producto($rst[pro_id]));
        $rst2 = pg_fetch_array($Set->lista_un_producto($rst[ord_pro_secundario]));
        $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst[ord_id]));
        $tot_peso_pri = ($rst1[pro_ancho] * $rst[ord_largo] * $rst[ord_gramaje] * $rst[ord_num_rollos])/1000;
        $tot_peso_sec = ($rst[ord_sec_ancho] * $rst[ord_largo] * $rst[ord_gramaje] * $rst[ord_num_rollos])/1000;
        
        $sms = $rst[ord_id] . '&' . $rst[ord_num_orden] . '&' . $rst1[pro_descripcion] . '&' . $rst2[pro_descripcion].'&'.$tot_peso_pri.'&'.$rst[ord_num_rollos].'&'.$rst_prod[peso].'&'.$rst_prod[peso2].'&'.$rst_prod[rollo].'&'.$rst_prod[rollo2].'&'.$tot_peso_sec;
        echo $sms;
        break;

    case 3:
        $sms = 0;
        $data = strtoupper($data);
        if ($s == 1) {
            $txt = "set chq_deposito='$data', chq_estado='$s', chq_fecha='$fec' ";
        } else {
            $txt = "set chq_observacion='$data', chq_estado='$s', chq_fecha='$fec'";
        }
        if ($Set->upd_estado($id, $txt) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
}
?>
