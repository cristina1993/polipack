<?php //
class Conn {
var $con = 0;

function Conectar(){
     
$this->con = pg_connect('host=localhost'
        . ' port=5432'
//        . ' dbname=polipackf'
        . ' dbname=polipack'
        . ' user=postgres'
        . ' password=1234' );
return $this->con;

}

}
?>