<?php

include_once 'Conn.php';

class Clase_industrial_ingresopt {

    var $con;

    function Clase_industrial_ingresopt() {
        $this->con = new Conn();
    }

    function lista_ingreso_industrial($bod) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=3 and m.bod_id=$bod ORDER BY mov_documento desc");
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=3 and m.bod_id=$bod ORDER BY mov_documento desc");
           
        }
    }
    
     function lista_ingreso_bodega($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.*,p.*,t.*,c.*,ps.pro_tipo FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t, erp_i_cliente c  ,erp_productos_set ps  where  m.pro_id=p.id and ps.ids=p.ids and m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=3 and m.bod_id=$bod ORDER BY mov_documento desc");
        }
    }
    
     function lista_ingreso_bodega_producto($id,$x) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.*,p.*,t.*,c.*,ps.pro_tipo FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t, erp_i_cliente c  ,erp_productos_set ps  where  m.pro_id=p.id and ps.ids=p.ids and m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=3 and p.id=$id and m.mov_tabla=1 and m.mov_id=$x ORDER BY mov_documento desc");
        }
    }
    
    function lista_ingreso_bodega_iproducto($id,$x) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.*,p.*,t.*,c.* FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c  where  m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=3 and p.pro_id=$id and m.mov_tabla=0 and m.mov_id=$x ORDER BY mov_documento desc");
        }
    }
    

    function lista_industrial_ingresopt() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t where m.pro_id=p.pro_id and m.trs_id=t.trs_id  ORDER BY mov_documento");
        }
    }

    function lista_secuencial($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt WHERE mov_documento like '001-%' ORDER BY mov_id DESC LIMIT 1");
        }
    }

    function lista_siglas($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt");
        }
    }

    function lista_un_ingreso_industrial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_id=$id");
        }
    }

    function lista_ingreso_industrial_documento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_documento='$id'");
        }
    }
    
    function lista_ingreso_comercial_documento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_documento='$id'");
        }
    }

    function lista_transaccion($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_transacciones where trs_id=$emp");
        }
    }

    function lista_buscador_industrial_ingresopt($bod,$txt) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM erp_i_mov_inv_pt m, erp_i_productos p,erp_transacciones t, erp_i_cliente c where p.pro_id= m.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.bod_id=$bod $txt order by m.mov_documento desc");
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=3 and m.bod_id=$bod  $txt order by m.mov_documento desc");
        }
    }

    function insert_industrial_ingresopt($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_num_trans,
                mov_fecha_trans,
                mov_fecha_registro,
                mov_hora_registro,
                mov_cantidad,
                mov_tranportista,
                mov_tabla
            )
    VALUES ($data[0],
        $data[8],
        $data[1],
       '$data[6]',
       '$data[2]',
       '$data[3]',
        '0',
       '$data[4]',
       '" . date('Y-m-d') . "',
       '" . date("H:i:s") . "',
       '$data[5]',
       '','$data[7]')");
        }
    }

    function upd_industrial_ingreso($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_mov_inv_pt SET 
                mov_fecha_entrega='$data[1]', 
                mov_num_factura='$data[2]', 
                mov_pago='$data[3]', 
                mov_direccion='$data[4]', 
                mov_val_unit='$data[5]', 
                mov_descuento='$data[6]', 
                mov_iva=$data[7], 
                mov_flete='$data[8]' 
                WHERE mov_id=$data[0]");
        }
    }

    function delete_industrial_ingreso($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_mov_inv_pt WHERE mov_documento='$id'");
        }
    }

    function lista_combo_fabricas_industrial() {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_empresa where emp_id>2 ORDER BY emp_descripcion");
            return pg_query("SELECT * FROM  erp_empresa ORDER BY emp_descripcion");
        }
    }
    function lista_combo_fabricas_noperti() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id<=2 ORDER BY emp_descripcion");
        }
    }

    function lista_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos p, erp_empresa e where p.emp_id=e.emp_id and e.emp_id=$id ORDER BY p.pro_descripcion");
        }
    }
    
    function lista_productos_comercial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.id,p.pro_a,p.pro_b,ps.pro_tipo FROM  erp_productos p, erp_productos_set ps 
where p.ids=ps.ids
ORDER BY p.pro_a");
        }
    }
    function lista_un_producto_noperti_cod($cod) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_productos where pro_a='$cod'");
            return pg_query("SELECT * FROM  erp_productos where id=$cod or pro_a='$cod'");
        }
    }
    function lista_un_producto_cod($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$cod or pro_codigo='$cod'");
        }
    }

    function lista_clientes_tipo($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres  
from  erp_i_cliente 
where cli_tipo <>'$tp'
order by nombres");
        }
    }

    function lista_un_proveedor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres FROM  erp_i_cliente where cli_id=$id");
        }
    }

    function lista_ultimo_ingreso_industrial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c, erp_empresa e where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and e.emp_id=p.emp_id ORDER BY mov_id desc LIMIT 1");
        }
    }
     function lista_ultimo_ingreso_comercial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c, erp_empresa e where m.trs_id=t.trs_id and m.cli_id=c.cli_id ORDER BY mov_id desc LIMIT 1");
        }
    }

    function lista_prod_comercial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.id, p.pro_a,p.pro_b, p.ids, p.pro_ac, ps.pro_tipo FROM  erp_productos p, erp_productos_set ps 
where p.ids=ps.ids and p.ids!=39
ORDER BY pro_a");
        }
    }
    
    function lista_prod_noperti() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.id,p.pro_a,p.pro_b, p.pro_ac, p.ids,ps.pro_tipo FROM  erp_productos p, erp_productos_set ps 
where p.ids=ps.ids and p.ids=39
ORDER BY p.pro_a");
        }
    }
    
    
     function lista_prod_comercial_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where id='$id'");
        }
    }
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
