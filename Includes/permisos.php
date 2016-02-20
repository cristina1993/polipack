<?php
session_start();
set_time_limit(0);
date_default_timezone_set('America/Guayaquil');
require '../Validate/sessionValidate.php';
include_once("../Clases/clsPermisos.php");
include_once '../Includes/library.php';
include_once '../Clases/clsUsers.php';
$User= new User();
$Prt=new Permisos();
if(!empty($_REQUEST[ol]))
{
    $modulo=$_REQUEST[ol];
    $_SESSION[ol]=$_REQUEST[ol];
}else{
    $modulo=$_SESSION[ol];
}    
$rst_mod=  pg_fetch_array($User->lista_un_opl($modulo));
$mod_id=$rst_mod[mod_id];
switch ($mod_id){
    case 31:$emisor=1;$bodega='NOPERTI';$cod_cli='CPJ00001';$id_cli=3;break;    
    case 32:$emisor=1;$bodega='NOPERTI';$cod_cli='CPJ00001';$id_cli=3;break;    
    case 33:$emisor=10;$bodega='INDUSTRIAL';$cod_cli='CPJ00002';$id_cli=2;break;    
    case 34:$emisor=10;$bodega='INDUSTRIAL';$cod_cli='CPJ00002';$id_cli=2;break;        
    case 35:$emisor=2;$bodega='CONDADO';$cod_cli='CPJ00010';$id_cli=49;break;    
    case 36:$emisor=2;$bodega='CONDADO';$cod_cli='CPJ00010';$id_cli=49;break;        
    case 37:$emisor=3;$bodega='QUICENTRO SUR';$cod_cli='CPJ00003';$id_cli=41;break;    
    case 38:$emisor=3;$bodega='QUICENTRO SUR';$cod_cli='CPJ00003';$id_cli=41;break;        
    case 39:$emisor=4;$bodega='MALL DEL SOL';$cod_cli='CPJ00004';$id_cli=43;break;    
    case 40:$emisor=4;$bodega='MALL DEL SOL';$cod_cli='CPJ00004';$id_cli=43;break;        
    case 43:$emisor=5;$bodega='SHOPPING MACHALA';$cod_cli='CPJ00005';$id_cli=44;break;    
    case 44:$emisor=5;$bodega='SHOPPING MACHALA';$cod_cli='CPJ00005';$id_cli=44;break;        
    case 45:$emisor=6;$bodega='RIOCENTRO NORTE';$cod_cli='CPJ00006';$id_cli=45;break;    
    case 46:$emisor=6;$bodega='RIOCENTRO NORTE';$cod_cli='CPJ00006';$id_cli=45;break;        
    case 47:$emisor=7;$bodega='SAN MARINO SHOPPING';$cod_cli='CPJ00007';$id_cli=46;break;    
    case 48:$emisor=7;$bodega='SAN MARINO SHOPPING';$cod_cli='CPJ00007';$id_cli=46;break;        
    case 49:$emisor=8;$bodega='CITY MALL';$cod_cli='CPJ00008';$id_cli=47;break;    
    case 50:$emisor=8;$bodega='CITY MALL';$cod_cli='CPJ00008';$id_cli=47;break;    
    case 51:$emisor=9;$bodega='QUICENTRO SHOPPING';$cod_cli='CPJ00009';$id_cli=48;break;    
    case 52:$emisor=9;$bodega='QUICENTRO SHOPPING';$cod_cli='CPJ00009';$id_cli=48;break;
    case 66:$emisor=11;$bodega='TOP TENIS';$cod_cli='CPJ00011';$id_cli=3217;break;
    case 67:$emisor=11;$bodega='TOP TENIS';$cod_cli='CPJ00011';$id_cli=3217;break;
    case 68:$emisor=12;$bodega='RECREO';$cod_cli='CPJ00012';$id_cli=3218;break;
    case 69:$emisor=12;$bodega='RECREO';$cod_cli='CPJ00012';$id_cli=3218;break;
    case 70:$emisor=13;$bodega='CCNU';$cod_cli='CPJ00013';$id_cli=3219;break;
    case 71:$emisor=13;$bodega='CCNU';$cod_cli='CPJ00013';$id_cli=3219;break;
    case 72:$emisor=14;$bodega='ATAHUALPA';$cod_cli='CPJ00014';$id_cli=3220;break;
    case 73:$emisor=14;$bodega='ATAHUALPA';$cod_cli='CPJ00014';$id_cli=3220;break;
    case 29:$emisor=10;$tabla=0;$bodega='INDUSTRIAL';break;
    case 87:$emisor=1;$tabla=1;$bodega='NOPERTI';break;
}

$Prt->Permit($_SESSION[usuid],$modulo);
$rst_user = pg_fetch_array($User->listUnUsuario($_SESSION[usuid]));
$rst_am = pg_fetch_array($User->lista_configuraciones());
$amb=$rst_am[con_ambiente];
$rst_dec = pg_fetch_array($User->lista_configuraciones_gen('6'));
$rst_cnt = pg_fetch_array($User->lista_configuraciones_gen('7'));
$dcm=$rst_dec[con_ambiente];
$dcc=$rst_cnt[con_ambiente];

?>
