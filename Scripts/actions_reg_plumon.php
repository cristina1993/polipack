<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_reg_plumon.php';
$Set = new Clase_reg_plumon();
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
            if ($Set->insert_registro_plumon($str) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'REG. PRODUCCION PLUMON';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[6]) == false) {
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
            if ($Set->update_registro_plumon($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'REG. PRODUCCION PLUMON';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[6]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($Set->delete_registro_plumon($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'REG. PRODUCCION PLUMON';
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
        $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst[orp_id]));
        $sms = $rst[orp_id] . '&' . $rst[orp_num_pedido] . '&' . $rst1[pro_descripcion] . '&' . $rst[orp_pro_peso] . '&' . $rst[orp_cantidad] . '&' . $rst_prod[peso] . '&' . $rst_prod[rollo];
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
