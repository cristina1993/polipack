<?php
set_time_limit(0);
include_once '../Clases/clsClaseSri.php';
$Obj = new SRI();
$cns = $Obj->productos_facturados();
$n = 0;
while ($rst = pg_fetch_array($cns)) {
    $rst1 = pg_fetch_array($Obj->busca_prod_mov($rst[id], $rst[num_camprobante]));
    if (empty($rst1)) {
        $n++;
        echo $n . '-' . $rst[id].'-'.$rst[num_camprobante] .'-'.$rst[cod_producto] .'-'.$rst[lote].'-'.$rst[cantidad].'<br>';
    }
}

