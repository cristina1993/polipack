<?php

include_once '../Clases/clsClase_industrial_genericos.php';
include_once("../Clases/clsAuditoria.php");
$Clase_industrial_genericos = new Clase_industrial_genericos();
$Adt = new Auditoria();
$cod = $_REQUEST[cod];
$ord = $_REQUEST[ord];
$data = $_REQUEST[data];
$ecuador = new DateTimeZone("America/Guayaquil");
$DateTime = new DateTime();
$DateTime->setTimeZone($ecuador);
$hora = $DateTime->format("H:i:s");
$fecha = $DateTime->format("Y-m-d");

switch ($cod) {
    case 0:
        if ($cod == 0) {
            $result = $Clase_industrial_genericos->lista_aprobaciones($data);
            if (pg_num_rows($result) != null) {
                echo "LA ORDEN YA SE INGRESO ANTERIORMENTE";
            } else {

                if ($Clase_industrial_genericos->insert_orden_aprobaciones($data, $fecha) == FALSE) {
                    echo $sms = pg_last_error();
                } else {
                    $fields = str_replace("&", ",", $fields[0]);
                    $modulo = 'GENERICOS';
                    $accion = 'INSERT';
                    if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                        $sms = "Auditoria" . pg_last_error();
                    }
                }
            }
        }
        break;
    case 1:

    case 2:

    case 3:
        if ($cod == 3) {
            $result = $Clase_industrial_genericos->lista_plumon($data);
            if (pg_num_rows($result) != null) {
                echo "LA ORDEN YA SE INGRESO ANTERIORMENTE";
            } else {

                if ($Clase_industrial_genericos->insert_orden_plumon($data, $fecha) == FALSE) {
                    echo $sms = pg_last_error();
                } else {
                    $fields = str_replace("&", ",", $fields[0]);
                    $modulo = 'GENERICOS';
                    $accion = 'INSERT';
                    if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                        $sms = "Auditoria" . pg_last_error();
                    }
                }
            }
        }
        break;
    case 4:
        if ($cod == 4) {
            $result = $Clase_industrial_genericos->lista_padding($data);
            if (pg_num_rows($result) != null) {
                echo "LA ORDEN YA SE INGRESO ANTERIORMENTE";
            } else {

                if ($Clase_industrial_genericos->insert_orden_padding($data, $fecha) == FALSE) {
                    $sms = pg_last_error();
                } else {
                    $fields = str_replace("&", ",", $fields[0]);
                    $modulo = 'GENERICOS';
                    $accion = 'INSERT';
                    if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                        $sms = "Auditoria" . pg_last_error();
                    }
                }
            }
        }
        break;
    case 5:
        if ($cod == 5) {
            $result = $Clase_industrial_genericos->lista_ecocambrella($data);
            if (pg_num_rows($result) != null) {
                echo "ORDEN YA ESTA INGRESADA";
            } else {

                if ($Clase_industrial_genericos->insert_orden_ecocambrella($data, $fecha) == FALSE) {
                    echo $sms = pg_last_error();
                } else {
                    $fields = str_replace("& ", ", ", $fields[0]);
                    $modulo = 'GENERICOS';
                    $accion = 'INSERT';
                    if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                        $sms = "Auditoria" . pg_last_error();
                    }
                }
            }
        }


        break;
    case 6:
        if ($cod == 6) {
            $result = $Clase_industrial_genericos->lista_geotextil($data);
            if (pg_num_rows($result) != null) {
                echo "ORDEN YA ESTA INGRESADA";
            } else {
                if ($Clase_industrial_genericos->insert_orden_geotexti($data, $fecha) == FALSE) {
                    echo $sms = pg_last_error();
                } else {
                    $fields = str_replace("& ", ", ", $fields[0]);
                    $modulo = 'GENERICOS';
                    $accion = 'INSERT';
                    if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                        $sms = "Auditoria" . pg_last_error();
                    }
                }
            }
        }
        break;
}
?>
