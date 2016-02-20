<?php //
class Conn {
var $con = 0;
function Conectar(){
$this->con = pg_connect('host=localhost'
        . ' port=5432 '
        . ' dbname=noperti'
        . ' user=postgres'
        . ' password=SuremandaS495' );
return $this->con;
}
}
?>