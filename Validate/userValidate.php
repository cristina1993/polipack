<?PHP

try {
    session_start();
    include_once("../Clases/clsUsers.php");
    include_once("../Clases/clsAuditoria.php");
    include_once("../Clases/clsSetting.php");
    if (isset($_POST)) {
        $user = $_POST['user'];
        $pass = md5($_POST['pass']);
        $objUser = new User();
        $Audit = new Auditoria();
        $Set= new Set();
        $consulta = $objUser->listUser($pass, $user);
        $registro = pg_fetch_array($consulta);
        if ($registro['usu_login'] == $user and $registro['usu_pass'] == $pass and $registro['usu_status'] == 't') {
            $_SESSION['User'] = $registro['usu_login'];
            $_SESSION['usuid'] = $registro['usu_id'];
            $_SESSION['usuario'] = $registro['usu_person'];
            if ($Audit->insert(array('Ingreso al Sistema', 'Login', '')) == false) {
                echo pg_last_error();
            } else {
                header("location:../menu/main.php");
                //// insertar inventario
//                $rst = pg_fetch_array($Set->lista_ultima_fecha());
//                $fec = date('Y-m-d', strtotime('-1 day'));
//                if ($rst[con_fecha] != $fec) {
//                    if ($Set->insert_consulta_inventario($fec) == false) {
//                        echo pg_last_error();
//                    }
//                }
            }
        } else {
            session_start();
            $_SESSION['session'] = "";
            $_SESSION['usuId'] = "";
            session_destroy();
            session_unset();
            header("location:../index.php?er=");
        }
    }
} catch (Exception $e) {
    echo 'Error:', $e->getMessage();
}
?>
