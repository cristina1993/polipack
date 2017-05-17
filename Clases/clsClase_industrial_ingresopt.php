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

    function lista_ingreso_bodega_producto($id, $x) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.*,p.*,t.*,c.*,ps.pro_tipo FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t, erp_i_cliente c  ,erp_productos_set ps  where  m.pro_id=p.id and ps.ids=p.ids and m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=3 and p.id=$id and m.mov_tabla=1 and m.mov_id=$x ORDER BY mov_documento desc");
        }
    }

    function lista_ingreso_bodega_iproducto($id, $x) {
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
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_id=$id and m.trs_id=20");
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

    function lista_buscador_industrial_ingresopt($txt) {

        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c, erp_i_productos p where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=26 $txt order by m.mov_documento asc");
        }
    }

    function lista_num_productos($txt) {

        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.pro_id FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c, erp_i_productos p  where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=26 $txt group by  m.pro_id");
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
            return pg_query("select cli_id, trim(cli_raz_social) as nombres  
from  erp_i_cliente 
order by nombres");
        }
    }

    function lista_un_proveedor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cli_id, trim(cli_raz_social) as nombres FROM  erp_i_cliente where cli_id=$id");
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
//////*******************TRANFERENCIAS*****************////////////
//    function lista_productos_total() {
//        if ($this->con->Conectar() == true) {
//            $query = "(SELECT '1' as tbl,id as id,pro_ac as lote,pro_a as codigo,pro_b as descripcion FROM  erp_productos 
//                       union
//                       SELECT '0' as tbl, pro_id as id, '' as lote ,pro_codigo as codigo,pro_descripcion as descripcion FROM  erp_i_productos) order by descripcion";
//            return pg_query($query);
//        }
//    }


    function lista_productos_total($ems) {
        if ($this->con->Conectar() == true) {

            return pg_query("SELECT * FROM  erp_i_productos where pro_estado=0 ORDER BY pro_codigo");
        }
    }

    function lista_locales() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor order by nombre_comercial asc");
        }
    }

    function lista_un_local($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where cod_punto_emision=$id");
        }
    }

    function lista_transferencias() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c 
                            where m.trs_id=t.trs_id 
                            and m.cli_id=c.cli_id 
                            and (t.trs_id=20 or t.trs_id=4)
                            ORDER BY m.mov_fecha_trans desc, m.mov_documento desc");
        }
    }

    function lista_transferencias_fecha($desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c 
                            where m.trs_id=t.trs_id 
                            and m.cli_id=c.cli_id 
                            and (t.trs_id=20)
                            and m.mov_fecha_trans between '$desde' and '$hasta'
                            ORDER BY m.mov_fecha_trans desc, m.mov_documento desc");
        }
    }

    function insert_transferencia($data) {
        if ($this->con->Conectar() == true) {
            $f = date('Y-m-d');
            $h = date('H:i');
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_cantidad,                
                mov_tabla,                
                mov_fecha_registro,
                mov_hora_registro,
                mov_usuario,
                mov_pago,
                mov_flete
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$data[4]',   
            '$data[5]',
            '$data[6]',   
            '$data[7]',
            '$data[8]',
            '$f',
            '$h',
            '$usu',
            '$data[9]',
            '$data[10]')");
        }
    }

    //DOCUMENTOS MOVIMIENTOS
    function lista_ingresos_doc($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pro_id,mov_tabla,sum(mov_cantidad) FROM erp_i_mov_inv_pt  where mov_documento='$doc' group by pro_id,mov_tabla ");
        }
    }

///cambios 13-04-2015
    function lista_buscar_comerciales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.*,ps.pro_tipo FROM  erp_productos p,erp_productos_set ps where ps.ids=p.ids and (p.pro_a='$id' or p.pro_b='$id')");
        }
    }

    function lista_buscar_industriales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$id' or pro_descripcion='$id'");
        }
    }

    function lista_prod_comerciales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.*,ps.pro_tipo FROM  erp_productos p,erp_productos_set ps  where ps.ids=p.ids and id=$id ");
        }
    }

    function lista_prod_industriales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$id");
        }
    }

    function buscar_un_movimiento($id, $tab, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and c.cli_id=m.cli_id and t.trs_id=26 and m.pro_id='$id' and m.mov_tabla='$tab' and m.bod_id=$emi ORDER BY m.pro_id,m.mov_tabla");
        }
    }

    function total_ingreso_egreso_fac($id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egreso");
        }
    }

    ///////////////////////////////////// TRANSFERENCIA PEDIDOS LOCALES 

    function lista_pedido_venta($id, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta p, erp_det_ped_venta d WHERE p.ped_id=d.ped_id and p.ped_id=$id and p.ped_num_registro='$cod'");
        }
    }

    function lista_bodega($pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor WHERE cod_punto_emision=$pto");
        }
    }

    function lista_local($cli) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor WHERE cod_cli=$cli");
        }
    }

    function lista_un_producto_noperti_cod_lote($code, $lote) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code' and pro_ac='$lote' and pro_estado=0");
        }
    }

    function lista_un_producto_industrial($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$code'");
        }
    }

    function lista_inventario($code, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(inv.mov_cantidad) as suma FROM erp_i_mov_inv_pt inv,erp_transacciones tr 
WHERE inv.trs_id=tr.trs_id
and inv.mov_guia_transporte='$code' 
and inv.pro_id=$id 
and tr.trs_id=20");
        }
    }

    function lista_pedidos_venta($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta p, erp_det_ped_venta d WHERE p.ped_id=d.ped_id and p.ped_num_registro='$cod'");
        }
    }

    function lista_encab_pedidos_venta($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ped_local, ped_femision, ped_nom_cliente, cli_id 
FROM erp_reg_pedido_venta p, erp_det_ped_venta d 
WHERE p.ped_id=d.ped_id and p.ped_num_registro='$cod' 
GROUP BY ped_local, ped_femision, ped_nom_cliente, cli_id");
        }
    }

    function lista_id_pedido($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ped_id FROM erp_reg_pedido_venta WHERE ped_num_registro='$cod' GROUP BY ped_id");
        }
    }

    function lista_det_pedido($cod, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (SELECT sum(inv.mov_cantidad) as suma FROM erp_i_mov_inv_pt inv,erp_transacciones tr 
        WHERE inv.trs_id=tr.trs_id
        and inv.mov_guia_transporte='$cod' 
        and tr.trs_id=20) as transferencia,
       (SELECT sum(det_cantidad) as suma FROM erp_det_ped_venta WHERE ped_id=$id) as pedido");
        }
    }

    function upd_pedido($id, $ped) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta SET 
                ped_estado='$ped' 
                WHERE ped_id=$id");
        }
    }

    function total_ingreso_egreso_fac_destino($id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egreso");
        }
    }

    function lista_secuencial_transferencia() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_secuencial   ORDER BY sec_id DESC LIMIT 1");
        }
    }

    function insert_sec_transferencia($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_secuencial(sec_transferencias) VALUES ('$data')");
        }
    }

    function lista_usuario_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT select * form erp_users usu_id=$id");
        }
    }
  ////////////////////////////////////////////// TRANSFERENCIA
    
    function lista_un_ingreso_comercial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_id=$id and m.trs_id=20");
        }
    }
    
    function lista_una_transferencia($pto) {
        if($this->con->Conectar() == true){
            return pg_query("SELECT c.cli_raz_social FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_documento='$pto' and m.trs_id=4 GROUP BY c.cli_raz_social");
        }
    }
    
    function lista_det_transferencia_comercial($doc) {
        if($this->con->Conectar() == true){
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t where m.pro_id=p.id and m.trs_id=t.trs_id and m.mov_documento='$doc' and m.trs_id=4");
        }
    }
    
    function lista_det_transferencia_industrial($doc) {
        if($this->con->Conectar() == true){
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.mov_documento='$doc' and m.trs_id=4");
        }
    }
    
    function lista_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_padding where opp_codigo='$id'");
        }
    }
}
?>
