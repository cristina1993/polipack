<?php
session_start();
$_SESSION['session'] = "";
$_SESSION['usuId'] = "";
session_destroy();
session_unset();
header("location:../index.php");
?>
