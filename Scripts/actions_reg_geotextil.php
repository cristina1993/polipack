<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_reg_geotextil.php';
$Set = new Clase_reg_geotextil();
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
            if ($Set->insert_registro_geotextil($str) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'REG. PRODUCCION GEOTEXTIL';
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
            if ($Set->update_registro_geotextil($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'REG. PRODUCCION GEOTEXTIL';
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
        if ($Set->delete_registro_geotextil($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'REG. PRODUCCION GEOTEXTIL';
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
        $rst_prod = pg_fetch_array($Set->lista_produccion_pedido($rst[opg_id]));
        $sms = $rst[opg_id] . '&' . $rst[opg_codigo] . '&' . $rst1[pro_descripcion] . '&' . $rst[opg_peso_producir] . '&' . $rst[opg_num_rollos] . '&' . $rst_prod[peso] . '&' . $rst_prod[rollo]; 
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
