<?php
if(empty($_SESSION['User']) or empty($_SESSION['usuid'])){
    $_SESSION['User']='';    
    $_SESSION['usuid']='';
    $_SESSION['usuario']='';
    session_destroy();
    header("location:../index.php");
}
?>
