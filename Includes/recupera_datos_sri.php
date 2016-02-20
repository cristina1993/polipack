<?php
include_once '../Clases/clsClaseSri.php';
$Sri = new SRI();
//****verificar conexion************//
$doc = $Sri->recupera_datos('0605201501179000787100120010010000011161234567813');
$pos = strpos($doc, 'HTTP ERROR');
if ($pos == false) {
    $cns = $Sri->documentos_noautorizados();
    while ($rst = pg_fetch_array($cns)) {
        $doc1 = $Sri->recupera_datos($rst[clave_acceso]);
        if (strlen($doc1[1]) == 37) {
            if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $rst[com_id])) {
                $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[clave_acceso], '', 'SuperAdmin');
                if(!$Sri->registra_errores($data)){
                    echo pg_last_error();
                }
            }
        }
    }
}
?>