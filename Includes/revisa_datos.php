<?php
include_once '../Clases/clsClase_factura.php';
$Fact = new Clase_factura();
$f = date('Ymd');
$op = $_GET[op];
switch ($op) {
    case 0:
        $rows = pg_fetch_all($Fact->lista_todos_noenviados());
        echo "No Autor<br><br>".$rows[0][tipo].$rows[0][count].'<br>'.
             $rows[1][tipo].$rows[1][count].'<br>'.
             $rows[2][tipo].$rows[2][count].'<br>'.
             $rows[3][tipo].$rows[3][count].'<br>'.   
             $rows[4][tipo].$rows[4][count].'<br>';   
        break;
}