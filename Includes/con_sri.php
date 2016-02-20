<?php
include_once '../Clases/clsClaseSri.php';
$Sri = new SRI();
$doc = $Sri->recupera_datos('0605201501179000787100120010010000011161234567813',2);
if(empty($doc[0]))
{echo 1;}else{echo 0;}
//$pos=  strpos($doc,'HTTP ERROR');
//if($pos==true){echo 1;}else{echo 0;}

