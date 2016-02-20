<?php

include_once 'Conn.php';
include_once '../Clases/clsSetting.php';

class Clase_industrial_genericos {

    var $con;

    function Clase_industrial_genericos() {
        $this->con = new Conn();
    }

    function lista_secuencial_orden_padding() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_orden_produccion_padding Order by opp_codigo desc limit 1");
        }
    }

    function lista_ecocambrella($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_orden_produccion where pro_id=$data[2] and ord_num_rollos=$data[1]");
        }
    }

    function lista_padding($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_orden_produccion_padding where pro_id=$data[2] and opp_cantidad=$data[1]");
        }
    }

    function lista_plumon($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_orden_produccion_plumon where pro_id=$data[2] and orp_cantidad=$data[1]");
        }
    }

    function lista_geotextil($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_orden_produccion_geotexti where pro_id=$data[2] and opg_num_rollos=$data[1]");
        }
    }

    function lista_aprobaciones($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_aprobaciones where pro_id=$data[2] and apr_cant=$data[1]");
        }
    }

    function lista_secuencial_orden_geotextil() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_orden_produccion_geotexti Order by opg_codigo desc limit 1");
        }
    }

    function lista_secuencial_aprobaciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_aprobaciones Order by apr_pedido desc limit 1");
        }
    }

    function lista_totalpedidos() {
        if ($this->con->Conectar() == true) {
            return pg_query("select  
1 as tbl,
pc.id,
pv.det_lote,
pv.det_cod_producto,
pv.det_descripcion,
'Empresa' as emp_descripcion,
0 as emp_id,
sum(pv.det_cantidad) as pedidos,
sum(it.mvt_cant) as inventarios,
(sum(it.mvt_cant))-(sum(pv.det_cantidad)) as total
from erp_det_ped_venta pv
join erp_productos pc on(pc.pro_a=pv.det_cod_producto and pc.pro_ac=pv.det_lote)
join erp_i_movpt_total it on (pc.id=it.pro_id and it.pro_tbl=1)
group by 
pc.id,
pv.det_lote,
pv.det_cod_producto,
pv.det_descripcion

union

select  
0 as tbl,
pc.pro_id,
'lote' as lote,
pv.det_cod_producto,
pv.det_descripcion,
em.emp_descripcion,
pc.emp_id,
sum(pv.det_cantidad) as pedidos,
sum(it.mvt_cant)/count(pc.pro_id) as inventarios,
(sum(it.mvt_cant))-(sum(pv.det_cantidad)) as total
from erp_det_ped_venta pv 
join erp_i_productos pc on(pc.pro_codigo=pv.det_cod_producto )
join erp_i_movpt_total it on (pc.pro_id=it.pro_id and it.pro_tbl=0)
join erp_empresa em on (em.emp_id=pc.emp_id)
group by 
pc.pro_id,
pv.det_cod_producto,
pv.det_descripcion,
it.mvt_cant,
em.emp_descripcion");
        }
    }

    function lista_por_nombre($txt1, $txt2) {
        if ($this->con->Conectar() == true) {
            return pg_query("select  
1 as tbl,
pc.id,
pv.det_lote,
pv.det_cod_producto,
pv.det_descripcion,
'Empresa' as Empresa,
sum(pv.det_cantidad),
sum(it.mvt_cant) as inventarios
from erp_det_ped_venta pv
join erp_productos pc on(pc.pro_a=pv.det_cod_producto and pc.pro_ac=pv.det_lote)
join erp_i_movpt_total it on (pc.id=it.pro_id and it.pro_tbl=1 $txt1)
union
select  
0 as tbl,
pc.pro_id,
'lote' as lote,
pv.det_cod_producto,
pv.det_descripcion,
em.emp_descripcion,
sum(pv.det_cantidad) as pedidos,
sum(it.mvt_cant) as inventarios
from erp_det_ped_venta pv 
join erp_i_productos pc on(pc.pro_codigo=pv.det_cod_producto )
join erp_i_movpt_total it on (pc.pro_id=it.pro_id and it.pro_tbl=0)
join erp_empresa em on (em.emp_id=pc.emp_id)$txt2");
        }
    }

    function insert_orden_ecocambrella($data, $fecha) {
        $Set = new Set();
        $rst_sec = pg_fetch_array($Set->lista_secuencial_orden_produccion());
        $cod = substr($rst_sec[ord_num_orden], -5);
        $sec = ($cod + 1);
        if ($sec >= 0 && $sec < 10) {
            $tx_trs = "0000";
        } elseif ($sec >= 10 && $sec < 100) {
            $tx_trs = "000";
        } elseif ($sec >= 100 && $sec < 1000) {
            $tx_trs = "00";
        } elseif ($sec >= 1000 && $sec < 10000) {
            $tx_trs = "0";
        } elseif ($sec >= 10000 && $sec < 100000) {
            $tx_trs = "";
        }
        $no_orden = 'EC-' . $tx_trs . $sec;
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_orden_produccion(
                        ord_num_rollos,
                        ord_fec_pedido,
                        pro_id,
                        ord_num_orden)
                      
            VALUES ($data[1],'$fecha',$data[2], '$no_orden')");
        }
    }

    function insert_orden_aprobaciones($data, $fecha) {
        $Clase_industrial_genericos = new Clase_industrial_genericos();

        $rst_sec = pg_fetch_array($Clase_industrial_genericos->lista_secuencial_aprobaciones());
        $cod = substr($rst_sec[apr_pedido], -5);
        $sec = ($cod + 1);
        if ($sec >= 0 && $sec < 10) {
            $tx_trs = "0000";
        } elseif ($sec >= 10 && $sec < 100) {
            $tx_trs = "000";
        } elseif ($sec >= 100 && $sec < 1000) {
            $tx_trs = "00";
        } elseif ($sec >= 1000 && $sec < 10000) {
            $tx_trs = "0";
        } elseif ($sec >= 10000 && $sec < 100000) {
            $tx_trs = "";
        }
        $no_orden = 'PC-' . $tx_trs . $sec;
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_aprobaciones(
                        apr_pedido,
                        apr_cant,
                        apr_fecha,
                        apr_estado,
                        pro_id
                        )
                      
            VALUES ('$no_orden',$data[1],'$fecha','REGISTRADO',$data[2])");
        }
    }

    function insert_orden_plumon($data, $fecha) {
        $Set = new Set();
        $rst_sec = pg_fetch_array($Set->lista_secuencial_orden_produccion_plumon());
        $cod = substr($rst_sec[orp_num_pedido], -5);
        $sec = ($cod + 1);
        if ($sec >= 0 && $sec < 10) {
            $tx_trs = "0000";
        } elseif ($sec >= 10 && $sec < 100) {
            $tx_trs = "000";
        } elseif ($sec >= 100 && $sec < 1000) {
            $tx_trs = "00";
        } elseif ($sec >= 1000 && $sec < 10000) {
            $tx_trs = "0";
        } elseif ($sec >= 10000 && $sec < 100000) {
            $tx_trs = "";
        }
        $no_orden = 'PL-' . $tx_trs . $sec;

        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_orden_produccion_plumon(
                        
                        orp_cantidad,
                        orp_fec_pedido,
                        pro_id,
                        orp_num_pedido)
            VALUES ('$data[1]','$fecha' ,$data[2],'$no_orden')");
        }
    }

    function insert_orden_padding($data, $fecha) {
        $Clase_industrial_genericos = new Clase_industrial_genericos();

        $rst_sec = pg_fetch_array($Clase_industrial_genericos->lista_secuencial_orden_padding());
        $cod = substr($rst_sec[opp_codigo], -5);
        $sec = ($cod + 1);
        if ($sec >= 0 && $sec < 10) {
            $tx_trs = "0000";
        } elseif ($sec >= 10 && $sec < 100) {
            $tx_trs = "000";
        } elseif ($sec >= 100 && $sec < 1000) {
            $tx_trs = "00";
        } elseif ($sec >= 1000 && $sec < 10000) {
            $tx_trs = "0";
        } elseif ($sec >= 10000 && $sec < 100000) {
            $tx_trs = "";
        }
        $no_orden = 'PAD' . $tx_trs . $sec;

        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_orden_produccion_padding(
                        opp_cantidad,
                        opp_fec_pedido,
                        pro_id,
                        emp_id,
                        opp_codigo)
            VALUES ($data[1],'$fecha',$data[2],$data[0],'$no_orden')");
        }
    }

    function insert_orden_geotexti($data, $fecha) {
        $Clase_industrial_genericos = new Clase_industrial_genericos();

        $rst_sec = pg_fetch_array($Clase_industrial_genericos->lista_secuencial_orden_geotextil());
        $cod = substr($rst_sec[opg_codigo], -5);
        $sec = ($cod + 1);
        if ($sec >= 0 && $sec < 10) {
            $tx_trs = "0000";
        } elseif ($sec >= 10 && $sec < 100) {
            $tx_trs = "000";
        } elseif ($sec >= 100 && $sec < 1000) {
            $tx_trs = "00";
        } elseif ($sec >= 1000 && $sec < 10000) {
            $tx_trs = "0";
        } elseif ($sec >= 10000 && $sec < 100000) {
            $tx_trs = "";
        }
        $no_orden = 'PPL' . $tx_trs . $sec;
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_orden_produccion_geotexti(
                        opg_num_rollos,
                        opg_fec_pedido,
                        pro_id,
                        emp_id,
                        opg_codigo)
            VALUES ($data[1],'$fecha',$data[2],$data[0],'$no_orden')");
        }
    }

}

?>
