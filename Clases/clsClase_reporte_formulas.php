<?php

include_once 'Conn.php';

class Clase_reporte_formulas{

    var $con;

    function Clase_reporte_formulas() {
        $this->con = new Conn();
    }

    function lista_formula($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select c.cli_raz_social, o.ord_fec_pedido, f.* ,
                            (select mp_referencia from erp_i_mp m where m.mp_id=f.ord_mp1) as mp1,
                            (select mp_referencia from erp_i_mp m where m.mp_id=f.ord_mp7) as mp2, 
                            (select mp_referencia from erp_i_mp m where m.mp_id=f.ord_mp13) as mp3
                            from formulacion f, erp_i_orden_produccion o, erp_i_cliente c 
                            where o.cli_id =c.cli_id and f.ord_numero=o.ord_num_orden 
                            and (f.ord_mp1+f.ord_mf1+f.ord_mp7+f.ord_mf7+f.ord_mp13+f.ord_mf13)>0 $txt 
                            order by ord_numero, fila
");
        }
    }

    function lista_formulacion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_formulacion where ord_numero='$id'");
        }
    }

}

?>
