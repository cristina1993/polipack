<?php 

// $con=odbc_connect("Driver=sql_conexion; Server=localhost; Database=sunchodesa",'sa','1234');

// if($con){
// echo "ok";
// }else{
// echo "no";
// }

 // $server = 'DESKTOP-TGQ6BVR\SQLEXPRESS';
 // $link = mssql_connect($server, 'sa', '1234');

	// if (!$link) {
	//     die('Algo fue mal mientras se conectaba a MSSQL');
	// }else{
	// 	echo "ok";
	// }

class Conn {
var $con = 0;
function Conectar(){
$this->con = pg_connect('host=181.39.126.19'
        . ' port=5432 '
        . ' dbname=noperti'
        . ' user=noperti_usr'
        . ' password=hu8j/*mn3' );
return $this->con;
}
}

$Con= new Conn();

if($Con->Conectar()==true){

    echo "ok";
   
}else{
    echo "no";
}

?>
