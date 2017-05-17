
<?php
session_start();
include_once '../Clases/clsClase_pagos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsUsers.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_preciospt.php';
$Clase_pagos = new Clase_pagos();
$Clases_preciospt = new Clase_preciospt();

$dias = date("d", mktime(0, 0, 0, (date('m') + 1), 0, date('Y')));
$from = date('Y-m-') . '01';
$until = date('Y-m-d', strtotime($from . ' + ' . ($dias - 1) . ' days'));

$prod = 0;
$act = $_REQUEST[act]; //Accion
$id = $_REQUEST[id]; //Id
$field = $_REQUEST[field]; //Field Name
$data = $_REQUEST[data]; //Data
$data2 = $_REQUEST[data2]; //Data
$data4 = $_REQUEST[data3]; //Data
$data5 = $_REQUEST[data4];
$old = $_REQUEST[old]; //Field Name old
$tbl = $_REQUEST[tbl]; //tbl
$s = $_REQUEST[s]; //tbl
$sts = $_REQUEST[sts];
$user = $_SESSION[usuid];
$fields = $_REQUEST[fields]; //Field Name
$nom = $_REQUEST[nom]; //Field Name
$usu = $_REQUEST[usu]; //Field Name

$Set = new Set();
$Adt = new Auditoria();
//0 add field
//1 upd  field
//2 del  field
//3 add upd  record
//4 del  record
//5 insert  data
switch ($tbl) {
    case 'erp_insumos_set':
        $prf = 'ins_';
        break;
    case 'erp_insumos':
        $prf = 'ins_';
        break;

    case 'erp_productos_set':
        $prf = 'pro_';
        $prod = 1;
        break;
    case 'erp_productos':
        $prf = 'pro_';
        break;
    case 'erp_maquinas_set':
        $prf = 'maq_';
        break;
    case 'erp_maquinas':
        $prf = 'maq_';
        break;
    case 'erp_registros_set':
        $prf = 'reg_';
        break;
    case 'erp_pedidos_set':
        $prf = 'ped_';
        break;
    case 'erp_pedidos':
        $prf = 'ped_';
        break;
    case 'erp_sn1_set':
        $prf = 'sn1_';
        break;
    case 'erp_clientes_set':
        $prf = 'cli_';
        break;
    case 'erp_sn3_set':
        $prf = 'sn3_';
        break;
    case 'erp_registros_produccion':
        $prf = 'reg';
        break;
}

switch ($act) {
    //STRUCTURES
    case 0:
        $sms = 0;
        if ($prod == 1) {
            if ($Set->addField_prod($prf . $field, $tbl) == FALSE) {

                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Insertar', $field));
            }
        } else {
            if ($Set->addField($prf . $field, $tbl) == FALSE) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Insertar', $field));
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($prod == 1) {
            if ($Set->updField_prod($old, $prf . $field, $tbl) == FALSE) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Modificar', substr($old, 4) . '==>' . $field));
            }
        } else {
            if ($Set->updField($old, $prf . $field, $tbl) == FALSE) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Modificar', substr($old, 4) . '==>' . $field));
            }
        }
        echo $sms;
        break;
    case 2:
        $sms = 0;
        if ($prod == 1) {
            if ($Set->delField_prod($field, $tbl) == FALSE) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Eliminar', substr($field, 4)));
            }
        } else {
            if ($Set->delField($field, $tbl) == FALSE) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Eliminar', substr($field, 4)));
            }
        }
        echo $sms;
        break;
    //RECORDS
    case 3:
        $sms = 0;
        if (empty($id)) {
            if ($Set->insert($data, $field, $tbl) == false) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Insertar', $data[0]));
            }
        } else {
            if ($Set->update($data, $field, $tbl, $id, $s) == false) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Modificar', $data[0]));
            }
        }

        echo $sms;
        break;
    case 4:
        $sms = 0;
        if ($Set->del($tbl, $id) == false) {
            $sms = pg_last_error();
        } else {
            $Adt->insert(array(substr($tbl, 4), 'Eliminar', $data[0]));
        }
        echo $sms;
        break;
    case 5:
        $sms = 0;
        if (empty($id)) {
            if ($data[0] == 2 || $data[0] == 3 || $data[0] == 5 || $data[0] == 7) {
                $rst_prod1 = pg_fetch_array($Set->lista_pro_comercial($data[4], $data[2]));
                $codigo1 = $rst_prod1[pro_a];
                $lote1 = $rst_prod1[pro_ac];
                if ($codigo1 == $data[4] || $lote1 == $data[2]) {
                    $sms = 1;
                } else {
                    if ($Set->insert($data, $field, $tbl) == false) {
                        $sms = pg_last_error();
                    } else {
//                        $Adt->insert(array(substr($tbl, 4), 'Insertar', $data[1]));
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = substr($tbl, 4);
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[1]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            }
            if ($data[0] == 1 || $data[0] == 8) {
                $rst_prod2 = pg_fetch_array($Set->lista_pro_comercial($data[1], $data[4]));
                $codigo2 = $rst_prod2[pro_a];
                $lote2 = $rst_prod2[pro_ac];
                if ($codigo2 == $data[1] || $lote2 == $data[4]) {
                    $sms = 1;
                } else {
                    if ($Set->insert($data, $field, $tbl) == false) {
                        $sms = pg_last_error();
                    } else {
//                        $Adt->insert(array(substr($tbl, 4), 'Insertar', $data[1]));
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = substr($tbl, 4);
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[1]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            }
            if ($data[0] == 4 || $data[0] == 6) {
                $rst_prod3 = pg_fetch_array($Set->lista_pro_comercial($data[1], $data[3]));
                $codigo3 = $rst_prod3[pro_a];
                $lote1 = $rst_prod3[pro_ac];
                if ($codigo3 == $data[1] || $lote3 == $data[3]) {
                    $sms = 1;
                } else {
                    if ($Set->insert($data, $field, $tbl) == false) {
                        $sms = pg_last_error();
                    } else {
//                        $Adt->insert(array(substr($tbl, 4), 'Insertar', $data[1]));
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = substr($tbl, 4);
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[1]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            }
            if ($data[0] >= 15 && $data[0] <> 46) {
                $rst_prod = pg_fetch_array($Set->lista_pro_comercial($data[1], $data[3]));
                $codigo = $rst_prod[pro_a];
                $lote = $rst_prod[pro_ac];
                if ($codigo == $data[1] || $lote == $data[3]) {
                    $sms = 1;
                } else {
                    if ($Set->insert($data, $field, $tbl) == false) {
                        $sms = pg_last_error();
                    } else {
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = substr($tbl, 4);
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[1]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            }
        } else {
            if ($Set->update($data, $field, $tbl, $id, $s) == false) {
                $sms = pg_last_error();
            } else {
//                $Adt->insert(array(substr($tbl, 4), 'Modificar', $data[1]));
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = substr($tbl, 4);
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[1]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }


        if ($prf == 'ped_') {
            $rstPed = pg_fetch_array($Set->list_one_data_by_id('erp_pedidos', $id));
            if ($rstPed[ped_f] == 6 || $rstPed[ped_f] == 1) {

                $Set->cambia_status($rstPed[0], 0);
            }
        }

        if ($prf == 'reg') {
            $rstPed = pg_fetch_array($Set->list_one_data_by_id('erp_pedidos', $data[0]));
            $sol = $rstPed[ped_e1] + $rstPed[ped_e2] + $rstPed[ped_e3] + $rstPed[ped_e4];
            $do0 = pg_fetch_array($Set->list_produccion_pedido_tipo($data[0], 0));
            $do1 = pg_fetch_array($Set->list_produccion_pedido_tipo($data[0], 1));
            $do2 = pg_fetch_array($Set->list_produccion_pedido_tipo($data[0], 2));
            $do = ($do0[sum] + $do1[sum] + $do2[sum]) / 3;
            if ($rstPed[ped_f] == 1) {
                $Set->cambia_status($rstPed[0], 2);
            }
            if ($do >= $sol) {
                $Set->cambia_status($rstPed[0], 3);
            }
        }

        echo $sms;
        break;
    case 6:
        $sms = 0;
        if ($Set->del_data($tbl, $id) == false) {
            $sms = pg_last_error();
        } else {
            $Adt->insert(array(substr($tbl, 4), 'Eliminar', $data[0]));
        }
        if ($Clases_preciospt->del_pre($id, '1') == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 7:
        $tbl.='_set';
        $cnsCampos = $Set->listar($tbl, 's');
        while ($rstCampos = pg_fetch_array($cnsCampos)) {
            $val = explode('&', $rstCampos[$prf . 'tipo']);
            echo "<option value='$rstCampos[ids]'>$val[9]</option>";
        }
        break;
    case 8:
        $sms = 0;
        $User = new User();
        if ($id == 0) {
            if ($User->insertaUsuarios($data) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    if ($fields[$n] == $fields[1] || $fields[$n] == $fields[2]) {
                        $fields[$n] = md5($fields[$n]);
                    }
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'USUARIO';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($User->modificaUsuario($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    if ($fields[$n] == $fields[1] || $fields[$n] == $fields[2]) {
                        $fields[$n] = md5($fields[$n]);
                    }
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'USUARIO';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 9:
        $sms = 0;
        $User = new User();
        if ($User->modificaEstado($data, $id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 10:
        $data = pg_fetch_array($Set->list_one_data_by_id('erp_productos', $id));
        $data2 = pg_fetch_array($Set->list_one_data_by_id('erp_pedidos', $_REQUEST[id2]));
        $files = pg_fetch_array($Set->lista_one_data('erp_productos_set', $data[ids]));
        ?>
        <tr>
            <td colspan="2" style="background:#f8f8f8;font-weight:bolder; " ></td>
            <?php
            $n = 2;
            while ($n <= count($files)) {
                $file = explode('&', $files[$n]);
                if ($file[0] == 'T' && !empty($file[9])) {
                    ?>
                    <td style="background:#f8f8f8;font-weight:bolder;font-size:12px;"><?php echo $file[9] ?></td>
                    <?php
                }
                $n++;
            }
            ?>            
        </tr>
        <tr>
            <td colspan="2" style="background:#f8f8f8;font-weight:bolder; " align="right" >Solicitado:</td>
            <td><input type="text" class='elemento' id="ped_e1" size="2" onchange="calculos(1, this.value)" value="<?php echo $data2[ped_e1] ?>" /></td>
            <td><input type="text" class='elemento' id="ped_e2" size="2" onchange="calculos(2, this.value)" value="<?php echo $data2[ped_e2] ?>" /></td>
            <td><input type="text" class='elemento' id="ped_e3" size="2" onchange="calculos(3, this.value)" value="<?php echo $data2[ped_e3] ?>" /></td>
            <td><input type="text" class='elemento' id="ped_e4" size="2" onchange="calculos(4, this.value)" value="<?php echo $data2[ped_e4] ?>" /></td>
        </tr>

        <?php
        $n = 2;
        while ($n <= count($files)) {
            $file = explode('&', $files[$n]);
            if ($file[0] == 'I' && !empty($file[9])) {
                if ($file[5] == 0) {
                    $req = '<font class="req" >&#8727</font>';
                } else {
                    $req = '';
                }

                if ($_REQUEST[op] == 0) {
                    $val = $data[$file[8]];
                } else {
                    $val = $data2[$file[8]];
                }

                switch ($file[2]) {
                    case 'I':
                        $input = "<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                            <img src='$val' width='128px' id='im$file[8]'/> ";
                        break;
                    case 'N':
                        $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )'  />";
                        break;
                    case 'C':
                        $input = "<input readonly class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                        break;
                    case 'F':
                        $input = "<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                        break;
                    case 'E':
                        $cnsEnlace = $Set->listOneById($file[7], $file[6]);

                        $input = "<select class='elemento' style='width:150px' name='mat0' title='$file[7]' lang='$file[5]' id='$file[8]' onchange='calc_telas()'  >";
                        $input.="<option value='0'>Ninguno</option>";
                        while ($rstEnlace = pg_fetch_array($cnsEnlace)) {
                            $selected = '';
                            if ($rstEnlace[id] == $val) {
                                $selected = 'selected';
                            }
                            $input.="<option  $selected value='$rstEnlace[id]'>$rstEnlace[ins_a]==>$rstEnlace[ins_b]</option>";
                        }
                        $input.="</select>";
                        break;
                }

                if ($n == 2) {
                    $val1 = $data[$file[8] . '1'];
                    $val2 = $data[$file[8] . '2'];
                    $val3 = $data[$file[8] . '3'];
                    $val4 = $data[$file[8] . '4'];
                } else {
                    $val1 = $data2[$file[8] . '1'];
                    $val2 = $data2[$file[8] . '2'];
                    $val3 = $data2[$file[8] . '3'];
                    $val4 = $data2[$file[8] . '4'];
                }
                ?>

                <tr>
                    <td><?php echo $file[9] ?></td>
                    <td><?php echo $input ?></td>
                    <td>
                        <input readonly type="hidden" lang="1"  name="aux_mat1" size="2" value="<?php echo $data[$file[8] . '1'] ?>" />
                        <input class='elemento' style="text-align:right" readonly type="text" lang="1"  id="<?php echo $file[8] . '1' ?>" name="mat1" size="2" value="<?php echo $val1 ?>" />
                    </td>
                    <td>
                        <input readonly type="hidden" lang="1" name="aux_mat2" size="2" value="<?php echo $data[$file[8] . '2'] ?>" />
                        <input class='elemento' style="text-align:right" readonly type="text" lang="1" id="<?php echo $file[8] . '2' ?>" name="mat2" size="2" value="<?php echo $val2 ?>" />
                    </td>
                    <td>
                        <input readonly type="hidden" lang="1" name="aux_mat3" size="2" value="<?php echo $data[$file[8] . '3'] ?>" />
                        <input class='elemento' style="text-align:right" readonly type="text" lang="1" id="<?php echo $file[8] . '3' ?>" name="mat3" size="2" value="<?php echo $val3 ?>" />
                    </td>
                    <td>
                        <input readonly type="hidden" lang="1" name="aux_mat4" size="2" value="<?php echo $data[$file[8] . '4'] ?>" />
                        <input class='elemento' style="text-align:right" readonly type="text" lang="1" id="<?php echo $file[8] . '4' ?>" name="mat4" size="2" value="<?php echo $val4 ?>" />
                    </td>
                </tr>                                            
                <?php
            }
            $n++;
        }
        ?>
        <?php
        break;
    case 11:
        $sms = 0;
        if ($Set->cambia_status($id, $sts) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'APROBACION PEDIDOS CONFECCIONES';
            $accion = $data;
            if ($Adt->insert_audit_general($modulo, $accion, '', $data2) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 12:
        $sms = 0;
        if ($id == 0) {
            if ($Set->insert_movimiento($data) == false) {
                $sms = pg_last_error();
            }
        } else {

            if ($Set->update_movimiento($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 13:
        $rst = pg_fetch_array($Set->list_one_data_by_id("erp_insumos", $id));
        echo $rst[ins_a] . '&' . $rst[ins_b] . '&' . $_REQUEST[sec];
        break;

    case 14:
        $sms = 0;
        if ($Set->delete_all("erp_registros") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_registros_produccion") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_mov_inventario") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_pedidos") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_clientes") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_insumos") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_maquinas") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_productos") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_auditoria") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_sn1") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_sn3") == false) {
            echo $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 15:
        $sms = 0;
//        if ($Set->del_data_by_pedido("erp_registros_produccion", $id) == true && $Set->del_data_by_pedido2("erp_mov_inventario", $id) == true && $Set->del_data("erp_pedidos", $id) == true) {
        if ($Set->del_data_by_pedido("erp_registros_produccion", $id) == true && $Set->del_data("erp_pedidos", $id) == true) {
            $Adt->insert(array(substr('erp_pedidos', 4), 'Eliminar', $data[0]));
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 16:
        $sms = 0;
        if ($data[12] == 0) {
            $rst_dupl = pg_fetch_array($Set->lista_movimiento_codigo($data[2]));

            if (!empty($rst_dupl)) {
                $sms = "Documento ya existe";
            } else {

                if ($Set->inser_inventarios($data) == false) {
                    $sms = pg_last_error();
                }
            }
        } else {
            if ($Set->inser_inventarios($data) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 17:
        $sms = 0;
        if ($id == 0) {//Insertar
            if ($Set->insert_tpmp($data) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'TIPO MP INDUSTRIAL';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {//Modificar
            if ($Set->upd_tpmp($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'TIPO MP INDUSTRIAL';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 18:
        $sms = 0;
        if ($Set->del_tpmp($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $nom;
            $modulo = 'TIPO MP INDUSTRIAL';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 19:
        $sms = 0;
        if ($id == 0) {//Insertar
            if ($Set->insert_mp($data) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'MATERIA PRIMA INDUSTRIAL';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[1]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {//Modificar
            if ($Set->upd_mp($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'MATERIA PRIMA INDUSTRIAL';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[1]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 20:
        $sms = 0;
        if ($Set->del_mp($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $nom;
            $modulo = 'MATERIA PRIMA INDUSTRIAL';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 21:
        $rst_code = pg_fetch_array($Set->lista_codigo_mp($_REQUEST[tp]));
        $rst_tp = pg_fetch_array($Set->lista_un_tpmp($_REQUEST[tp]));
        //$cod=explode(".",$rst_code[mp_codigo]);
        //$code=($cod[1]+1);
        $cod = substr($rst_code[mp_codigo], -4);
        $code = ($cod + 1);
        if ($code >= 0 && $code < 10) {
            $txt = '000';
        } elseif ($code >= 10 && $code < 100) {
            $txt = '00';
        } elseif ($code >= 100 && $code < 1000) {
            $txt = '0';
        } elseif ($code >= 1000 && $code < 10000) {
            $txt = '';
        }

        echo $rst_tp[mpt_siglas] . $txt . $code;
        break;
    case 22:
        $sms = 0;
        $obs = '';
        if ($Set->upd_factura_orden_compra($data[11], $data[2]) == true) {
            if ($Set->insert_inv_mp($data) == false) {
                $sms = pg_last_error();
            }
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'SEGUIMIENTO ORDENES DE COMPRA';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $data[2]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        } else {
            $sms = pg_last_error();
        }
        $rst_ord = pg_fetch_array($Set->lista_total_orden_compra_code($data[2]));
        $rst_mov = pg_fetch_array($Set->lista_inv_mp_doc_total($data[2], 0));
        if ($rst_ord[sum] > $rst_mov[peso]) {
            $sts = 4;
        } elseif ($rst_ord[sum] <= $rst_mov[peso]) {
            $sts = 5;
        }
        if ($Set->upd_orden_compra_estado($sts, $obs, $rst_ord[orc_id]) == false) {
            $sms = 'upd_sts' . pg_last_error();
        }
        echo $sms;
        break;
    case 222:
        $sms = 0;
        $obs = '';
        if ($Set->insert_inv_mp($data) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'EGRESO MP INDUSTRIAL';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $data[2]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 225:
        $sms = 0;
        if ($Set->insert_inv_mp($data) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 23:
        $sms = 0;
        if ($Set->del_mov($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 24:
        $sms = 0;
        if ($Set->del_mov_code($_REQUEST[nm_trs]) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

//Pedido MP
    case 25:
        $sms = 0;
        if ($id == 0) {//Insertar
            if ($Set->insert_pmp($data) == false) {
                $sms = pg_last_error();
            }
        } else {//Modificar
            if ($Set->upd_pmp($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
//caso la descripcion del producto
    case 26:
        $rst_mp = pg_fetch_array($Set->lista_des_mp($_REQUEST[mp]));
        $rst_inv_ing = pg_fetch_array($Set->lista_inv_mp($_REQUEST[mp], 0));
        $rst_inv_egr = pg_fetch_array($Set->lista_inv_mp($_REQUEST[mp], 1));
        $invp = str_replace(',', '', number_format($rst_inv_ing[peso] - $rst_inv_egr[peso], 1));
        $invu = str_replace(',', '', number_format($rst_inv_ing[unidad] - $rst_inv_egr[unidad], 1));
        $invpu = ($rst_inv_ing[peso] - $rst_inv_egr[peso]) / ($rst_inv_ing[unidad] - $rst_inv_egr[unidad]);
        $rst_c = pg_fetch_array($Set->lista_ultimo_costo($_REQUEST[mp]));
        $rst_c[mov_peso_unit] = ($rst_c[tot_ing] - $rst_c[tot_egr]) / ($rst_c[cnt_ing] - $rst_c[cnt_egr]);
        echo $rst_mp[mp_codigo] . "&" . $invp . "&" . $rst_mp[mp_unidad] . "&" . $rst_mp[mp_presentacion] . "&" . $invu . "&" . $invpu . "&" . $rst_c[mov_peso_unit] . "&" . $rst_mp[mp_referencia] . "&" . $rst_mp[mp_id];
        break;
    case 27:
        $sms = 0;
        if ($Set->del_pmp($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'PEDIDO MP INDUSTRIAL';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $s . ' ' . $sts) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 28:
        $rst_trs = pg_fetch_array($Set->lista_una_transaccion($id));
        $rst_sec = pg_fetch_array($Set->lista_secuencia_transaccion($rst_trs[trs_operacion]));
        $sec0 = explode('-', $rst_sec[mov_num_trans]);
        $sec = ($sec0[1] + 1);
        if ($sec >= 0 && $sec < 10) {
            $tx_trs = "000000000";
        } elseif ($sec >= 10 && $sec < 100) {
            $tx_trs = "00000000";
        } elseif ($sec >= 100 && $sec < 1000) {
            $tx_trs = "0000000";
        } elseif ($sec >= 1000 && $sec < 10000) {
            $tx_trs = "000000";
        } elseif ($sec >= 10000 && $sec < 100000) {
            $tx_trs = "00000";
        } elseif ($sec >= 100000 && $sec < 1000000) {
            $tx_trs = "0000";
        } elseif ($sec >= 1000000 && $sec < 10000000) {
            $tx_trs = "000";
        } elseif ($sec >= 10000000 && $sec < 100000000) {
            $tx_trs = "00";
        } elseif ($sec >= 100000000 && $sec < 1000000000) {
            $tx_trs = "0";
        } elseif ($sec >= 1000000000 && $sec < 10000000000) {
            $tx_trs = "";
        }

        if ($rst_trs[trs_operacion] == 0) {
            $txt0 = "000";
        } else {
            $txt0 = "100";
        }

        echo $no_trs = $txt0 . '-' . $tx_trs . $sec;
        break;
//Orden Compra Mp
    case 29:
        $sms = 0;
        $rst = $Set->lista_orden_compra_code($id);
        if (pg_num_rows($rst) == 0) {//Insertar
            if ($Set->insert_orden_compra($data) == false) {
                $sms = 'Insert' . pg_last_error();
            }
        } else {//Modificar
            if ($Set->upd_orden_compra($data, $id) == false) {
                $sms = 'Upd' . pg_last_error();
            }
        }
        echo $sms;
        break;
//Detalle Orden Compra Mp
    case 30:
        $sms = 0;
        if ($id == 0) {//Insertar
            if ($Set->insert_det_orden_compra($data) == false) {
                $sms = pg_last_error();
            }
        } else {//Modificar
            if ($Set->upd_det_orden_compra($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        //***Reviso cupos e historial > 0
        if ($s == 1) {
            $sts = 2;
            $obs = 'Sin Orden';

            $rst = pg_fetch_array($Set->lista_una_orden_compra($data[0]));
            $rst_mp = pg_fetch_array($Set->lista_una_orden_compra($data[1]));

            $data1 = array(3,
                $data[1],
                $rst[orc_codigo],
                $rst[orc_documento],
                $rst[orc_fecha],
                $data[2],
                $rst[mp_presentacion],
                $data[4], ///$data[2], total
                $rst[cli_id],
                $data[3], ///1, unit
                '',
                $data[5],);
            if ($Set->insert_inv_mp($data1) == false) {
                $sms = 'Sin orden' . pg_last_error();
            }
        } else {
            $rst_cupos = pg_fetch_array($Set->lista_un_cupo_usuid($user));
            $rst_total_orden = pg_fetch_array($Set->lista_una_orden_total($data[0]));
            $rst_total_mensual = pg_fetch_array($Set->lista_orden_total_mensual_user($user, $from, $until));
            $rst_historial_mp = pg_fetch_array($Set->lista_historial_orden_mp($data[1]));
            $dif = abs($rst_historial_mp[orc_det_vu] - $data[3]);
            $por = ($dif * 100 / $rst_historial_mp[orc_det_vu]);

            if (!empty($rst_cupos) && ($rst_cupos[cup_xorden] < $rst_total_orden[sum])) { //Si supera su cupo por orden
                $sts = 1;
                $obs = 'Supera Cupo Por Orden';
            } elseif (!empty($rst_cupos) && ($rst_cupos[cup_mensual] < $rst_total_mensual[sum])) {
                $sts = 1;
                $obs = 'Supera Cupo Mensual';
            } elseif (!empty($rst_historial_mp) && $rst_historial_mp[orc_det_vu] != $data[3] && $por > 5) {
                $sts = 1;
                $obs = 'Valor Unitario Supera Rango de Tolerancia';
            } else {
                $sts = 2;
                $obs = '';
            }
        }

        if ($Set->upd_orden_compra_estado($sts, $obs, $data[0]) == FALSE) {
            $sms = pg_last_error();
        }

        echo $sms;
        break;
    case 31:
        $sms = 0;
        $rst = pg_fetch_array($Set->lista_una_det_orden_compra($id));
        if ($Set->del_det_orden_compra($id) == true) {
            if ($Set->del_mov_doc_mp($rst[orc_codigo], $rst[mp_id]) == false) {
                $sms = pg_last_error();
            }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 32:
        $sms = 0;
        $rst_ord = pg_fetch_array($Set->lista_una_orden_compra($id));
        if ($Set->del_det_orden_compra_orc($id) == true && $Set->del_mov_doc($rst_ord[orc_codigo]) == true) {
            if ($Set->del_orden_compra($id) == false) {
                $sms = pg_last_error();
            } else {
                $modulo = 'ORDENES DE COMPRA';
                $accion = 'ELIMINAR';
                if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 33:
        $n = 0;
        $sms = 0;
        if (!empty($_REQUEST[doc])) {
            while ($n < count($data)) {
                $dat = explode('%', $data[$n]);
                $rst_mov = pg_fetch_array($Set->lista_un_movimiento_mp($dat[0]));
                $dat[4] = $rst_mov[mp_codigo] . 'OC00000';
                $dat[5] = 1;
                if ($Set->insert_etq_orden($dat) == false) {
                    //$sms=pg_last_error();
                    print_r($dat);
                    break;
                }
                $n++;
            }
        } else {
            while ($n < count($data)) {
                $dat = explode('%', $data[$n]);
                if ($Set->insert_etq_orden($dat) == false) {
                    $sms = pg_last_error();
                    break;
                }
                $n++;
            }
        }
        echo $sms;
        break;

////////////////////////// Cumplimiento /////////////////////////////////////
    case 34:
        $sms = 0;
        if ($id == 0) {// Insertar
            if ($Set->insertar_cumplimiento($data) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'CUMPLIMIENTO';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {// Modificar
            if ($Set->modificar_cumplimiento($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'CUMPLIMIENTO';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
// Eliminar Cumplimiento
    case 35:
        $sms = 0;
        if ($Set->delete_cumplimiento($id) == FALSE) {
            $sms = pg_last_error();
        } else {

            $n = 0;
            $f = $nom;
            $modulo = 'CUMPLIMIENTO';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
///////////////////////////////////////////////////////////////////////////////////////
////////////////////////// Tipo de Pago /////////////////////////////////////
    case 36:
        $sms = 0;
        if ($id == 0) {// Insertar
            if ($Set->insertar_tipo_de_pago($data) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'TIPO PAGO';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {// Modificar
            if ($Set->modificar_tipo_de_pago($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'TIPO PAGO';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
// Eliminar Tipo de Pago
    case 37:
        $sms = 0;
        if ($Set->delete_tipo_de_pago($id) == FALSE) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $nom;
            $modulo = 'TIPO PAGO';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
///////////////////////////////////////////////////////////////////////////////////////
////////////////////////// Capacidad de Compra /////////////////////////////////////
    case 38:
        $sms = 0;
        if ($id == 0) {// Insertar
            if ($Set->insertar_capacidad_de_compra($data) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'CAPACIDAD COMPRA';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {// Modificar
            if ($Set->modificar_capacidad_de_compra($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'CAPACIDAD COMPRA';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
// Eliminar Capacidad de Compra
    case 39:
        $sms = 0;
        if ($Set->delete_capacidad_de_compra($id) == FALSE) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $nom;
            $modulo = 'CAPACIDAD COMPRA';
            $accion = 'Eliminar';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }

        echo $sms;
        break;
///////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////  CLIENTES /////////////////////////////////////
    case 40:
        $sms = 0;
        if ($id == '0') {// Insertar
            if ($Set->insertar_clientes($data) == true) {
                if (count($data2) > 0) {
                    $rst_cli = pg_fetch_array($Set->lista_un_cliente_codigo($data[1]));
                    array_push($data2, $rst_cli[cli_id]);
                    if ($Set->insertar_direccion_entrega($data2) == false) {
                        $sms = pg_last_error();
                    }
                }
            } else {
                $sms = "Insert " . pg_last_error();
            }
        } else {// Modificar
            if ($Set->modificar_clientes($data, $id) == true) {
                if (!empty($_REQUEST[campo])) {
                    if ($Set->insert_cambio_clientes($id, $_REQUEST[cambio], $_REQUEST[campo]) == false) {
                        $sms = 'Cambio' . pg_last_error();
                    }
                }
                if (count($data2) > 0) {
                    $rst_cli = pg_fetch_array($Set->lista_un_cliente_codigo($data[1]));
                    array_push($data2, $rst_cli[cli_id]);
                    if ($Set->insertar_direccion_entrega($data2) == false) {
                        $sms = pg_last_error();
                    }
                }
            } else {
                $sms = "Editar " . pg_last_error();
            }
        }
        echo $sms;
        break;
// Eliminar clientes
    case 41:
        $sms = 0;
        $rst_cli = pg_fetch_array($Set->lista_un_cliente($id));
        if ($Set->delete_direccion_entrega($id) == true && $Set->delete_aprobaciones($rst_cli[cli_codigo]) == true) {
            if ($Set->delete_clientes($id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
// Eliminar Direccion entrega CLIENTES
    case 42:
        $sms = 0;
        if ($Set->delete_direccion_entrega($id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 43:
        $rst = pg_fetch_array($Set->lista_clientes_codigo($id));
        $cod = substr($rst[cli_codigo], -5);
        $code = ($cod + 1);
        if ($code >= 0 && $code < 10) {
            $txt = '0000';
        } elseif ($code >= 10 && $code < 100) {
            $txt = '000';
        } elseif ($code >= 100 && $code < 1000) {
            $txt = '00';
        } elseif ($code >= 1000 && $code < 10000) {
            $txt = '0';
        } elseif ($code >= 10000 && $code < 100000) {
            $txt = '';
        }
        echo $txt . $code;
        break;
    case 44:
        $rst = pg_fetch_array($Set->lista_ultima_orden_compra_producto($id));
        if (empty($rst)) {
            $rst = pg_fetch_array($Set->lista_un_mp_code($id));
        }
        $rst_sec = pg_fetch_array($Set->lista_secuencial_orden());
        $sec = ($rst_sec[orc_codigo] + 1);
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
        echo $rst[mp_codigo] . "&" . $rst[mp_referencia] . "&" . $rst[cli_codigo] . "&" . $rst[emp_descripcion] . "&" . $no_orden = $tx_trs . $sec;
        ;
        break;

    case 45:
        $sms = 0;
//                                emp_descripcion.value,
//                                cli_nombre.value,
//                                orc_codigo.value,
//                                mp_codigo.value,
//                                orc_fecha.value,
//                                orc_det_guia.value,
//                                etq_peso.value,
//                                etq_bar_code.value

        $rst_cli = pg_fetch_array($Set->lista_un_cliente_codigo($data[1]));
        $rst_emp = pg_fetch_array($Set->lista_una_fabrica_desc($data[0]));
//                                            cli_id,
//                                            orc_fecha,
//                                            orc_codigo,
//                                            emp_id,
//                                            orc_descuento,
//                                            orc_flete
        if ($Set->insert_orden_compra(array($rst_cli[cli_id], $data[4], $data[2], $rst_emp[emp_id], 0, 0)) == true) {
            $rst_oc = pg_fetch_array($Set->lista_orden_compra_code($data[2]));
            $rst_mp = pg_fetch_array($Set->lista_un_mp_code($data[3]));
//                                             orc_id,
//                                             mp_id,
//                                             orc_det_cant,
//                                             orc_det_vu,
//                                             orc_det_vt
            if ($Set->insert_det_orden_compra(array($rst_oc[orc_id], $rst_mp[mp_id], 0, 0, 0)) == true) {
                $rst_doc = pg_fetch_array($Set->lista_det_orden_compra_oc_mp($rst_oc[orc_id], $rst_mp[mp_id]));
//                                                orc_det_id,
//                                                etq_cant,
//                                                etq_peso,
//                                                etq_fecha,
//                                                etq_bar_code
                if ($Set->upd_guia_det_orden_compra($data[5], $rst_doc[orc_det_id]) == true) {

                    if ($Set->insert_etq_orden(array($rst_doc[orc_det_id], 1, $data[6], $data[4], $data[7])) == false) {
                        $sms = "Etq" . pg_last_error();
                    }
                } else {
                    $sms = "Upd_Guia" . pg_last_error();
                }
            } else {
                $sms = "Detalle" . pg_last_error();
            }
        } else {
            $sms = "Orden" . pg_last_error();
        }
        echo $sms . "&" . $rst_doc[orc_det_id];
        break;
    case 46:
        $sms = 0;
        if ($Set->upd_aprobaciones($id, $sts) == false) {
            $sms = pg_last_error();
        }

        if ($sts == 1) {
            $rst = pg_fetch_array($Set->lista_una_aprobaciones($id));
            if ($Set->upd_aprobaciones_clientes($rst[cli_id], $rst[abp_campo], $rst[apb_cambio]) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 47:

        $sms = 0;
        if ($id == 0) {
            if ($Set->insert_cupo($data) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'CUPOS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, '') == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($Set->upd_cupo($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'CUPOS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, '') == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }

        echo $sms;
        break;
    case 48:
        $sms = 0;
        if ($Set->del_cupo($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'CUPOS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $nom) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 49:
        $sms = 0;
        $rst = pg_fetch_array($Set->lista_orden_compra_code($id));
        if (!empty($rst)) {
            $rst_cli = pg_fetch_array($Set->lista_un_cliente($rst[cli_id]));
            $cns = $Set->lista_det_orden_compra($rst[orc_id]);
            $sms = $rst_cli[cli_nombre] . '&';
            while ($rst_mp = pg_fetch_array($cns)) {
                $sms.="<option value='$rst_mp[mp_id]' >$rst_mp[mp_referencia]</option>";
            }
        }
        echo $sms;
        break;
    case 50:
        $sms = 0;
        $obs = '';
        if ($Set->upd_orden_compra_estado($sts, $obs, $id) == false) {
            $sms = pg_last_error();
        } else {
            if ($sts == 2) {
                $accion = 'APROBAR';
            } else if ($sts == 3) {
                $accion = 'IMPRIMIR';
            } else if ($sts == 6) {
                $accion = 'RECHAZAR';
            }
            $modulo = 'ORDENES DE COMPRA';

            if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 51:
        $rst_pro = pg_fetch_array($Set->lista_un_producto($_REQUEST[id]));
        $rst_fbc = pg_fetch_array($Set->lista_una_fabrica($rst_pro[emp_id]));
        $rst_set = pg_fetch_array($Set->lista_utlimo_seteo_maquina($_REQUEST[id]));
        $cns_mp = $Set->lista_solo_mp();
        $combo = "<option  value='0'> - Seleccione - </option>";
        while ($rst_mp = pg_fetch_array($cns_mp)) {
            $combo.="<option value='$rst_mp[mp_id]'>$rst_mp[mp_referencia]</option>";
        }

        $retorno = $rst_pro[pro_ancho] . "&" .
                $rst_pro[pro_mp1] . "&" .
                $rst_pro[pro_mp2] . "&" .
                $rst_pro[pro_mp3] . "&" .
                $rst_pro[pro_mp4] . "&" .
                $rst_pro[pro_mp5] . "&" .
                $rst_pro[pro_mp6] . "&" .
                $rst_pro[pro_mf1] . "&" .
                $rst_pro[pro_mf2] . "&" .
                $rst_pro[pro_mf3] . "&" .
                $rst_pro[pro_mf4] . "&" .
                $rst_pro[pro_mf5] . "&" .
                $rst_pro[pro_mf6] . "&" .
                ($rst_pro[pro_mf1] + $rst_pro[pro_mf2] + $rst_pro[pro_mf3] + $rst_pro[pro_mf4] + $rst_pro[pro_mf5] + $rst_pro[pro_mf6]) . "&" .
                $rst_pro[pro_largo] . "&" .
                $rst_pro[pro_gramaje] . "&" .
                $rst_pro[pro_propiedad4] . "&" . ///$rst_pro[pro_peso]
                $rst_fbc[emp_sigla] . "&" .
                $rst_set[ord_zo1] . "&" .
                $rst_set[ord_zo2] . "&" .
                $rst_set[ord_zo3] . "&" .
                $rst_set[ord_zo4] . "&" .
                $rst_set[ord_zo5] . "&" .
                $rst_set[ord_zo6] . "&" .
                $rst_set[ord_spi_temp] . "&" .
                $rst_set[ord_upp_rol_tem_controller] . "&" .
                $rst_set[ord_dow_rol_tem_controller] . "&" .
                $rst_set[ord_spi_tem_controller] . "&" .
                $rst_set[ord_coo_air_temp] . "&" .
                $rst_set[ord_upp_rol_heating] . "&" .
                $rst_set[ord_upp_rol_oil_pump] . "&" .
                $rst_set[ord_dow_rol_heating] . "&" .
                $rst_set[ord_dow_rol_oil_pump] . "&" .
                $rst_set[ord_spi_rol_heating] . "&" .
                $rst_set[ord_spi_rol_oil_pump] . "&" .
                $rst_set[ord_mat_pump] . "&" .
                $rst_set[ord_spi_blower] . "&" .
                $rst_set[ord_sid_blower] . "&" .
                $rst_set[ord_dra_blower] . "&" .
                $rst_set[ord_gsm_setting] . "&" .
                $rst_set[ord_aut_spe_adjust] . "&" .
                $rst_set[ord_spe_mod_auto] . "&" .
                $rst_set[ord_lap_speed] . "&" .
                $rst_set[ord_man_spe_setting] . "&" .
                $rst_set[ord_rol_mill] . "&" .
                $rst_set[ord_win_tensility] . "&" .
                $rst_set[ord_mas_bra_autosetting] . "&" .
                $rst_set[ord_rol_mil_up_down] . "&" .
                $combo . "&" .
                $rst_pro[pro_mp7] . "&" .
                $rst_pro[pro_mp8] . "&" .
                $rst_pro[pro_mp9] . "&" .
                $rst_pro[pro_mp10] . "&" .
                $rst_pro[pro_mp11] . "&" .
                $rst_pro[pro_mp12] . "&" .
                $rst_pro[pro_mf7] . "&" .
                $rst_pro[pro_mf8] . "&" .
                $rst_pro[pro_mf9] . "&" .
                $rst_pro[pro_mf10] . "&" .
                $rst_pro[pro_mf11] . "&" .
                $rst_pro[pro_mf12] . "&" .
                ($rst_pro[pro_mf7] + $rst_pro[pro_mf8] + $rst_pro[pro_mf9] + $rst_pro[pro_mf10] + $rst_pro[pro_mf11] + $rst_pro[pro_mf12]) . "&" .
                $rst_pro[pro_mp13] . "&" .
                $rst_pro[pro_mp14] . "&" .
                $rst_pro[pro_mp15] . "&" .
                $rst_pro[pro_mp16] . "&" .
                $rst_pro[pro_mp17] . "&" .
                $rst_pro[pro_mp18] . "&" .
                $rst_pro[pro_mf13] . "&" .
                $rst_pro[pro_mf14] . "&" .
                $rst_pro[pro_mf15] . "&" .
                $rst_pro[pro_mf16] . "&" .
                $rst_pro[pro_mf17] . "&" .
                $rst_pro[pro_mf18] . "&" .
                ($rst_pro[pro_mf13] + $rst_pro[pro_mf14] + $rst_pro[pro_mf15] + $rst_pro[pro_mf16] + $rst_pro[pro_mf17] + $rst_pro[pro_mf18] + $rst_pro[pro_mf18]) . "&" .
                $rst_pro[pro_por_tornillo1] . "&" .
                $rst_pro[pro_por_tornillo2] . "&" .
                $rst_pro[pro_por_tornillo3];


//////////////////////////////////////////////////////////////////////////////////
        echo $retorno;
        break;
    case 52:
        $retorno;
        $cns_pro = $Set->lista_productos_faltantes($_REQUEST[faltante], $_REQUEST[gramaje]);
//        $num = pg_num_rows($cns_pro);
//        if (empty($num)) {
        $retorno.= "<option  value='0'>Ninguno</option>";
//        }
        while ($rst_pro = pg_fetch_array($cns_pro)) {
            $retorno.= "<option  value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
        }
        echo $retorno;
        break;
    case 53:
        $rst_pro = pg_fetch_array($Set->lista_un_producto($_REQUEST[id]));
        $retorno = $rst_pro[pro_ancho];
        echo $retorno;
        break;
////////////////////////// Reporte Produccion /////////////////////////////////////
    case 54:
        $sms = 0;
        if ($id == 0) {// Insertar
            if ($Set->insertar_reporte_produccion($data) == false) {
                $sms = pg_last_error();
            }
        } else {// Modificar
            if ($Set->modificar_reporte_produccion($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
// Eliminar Reporte Produccion
    case 55:
        $sms = 0;
        if ($Set->delete_reporte_produccion($id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
///////////////////////////////////////////////////////////////////////////////////////
    case 56:
        $rst_ord = pg_fetch_array($Set->lista_una_orden_produccion_numero_orden($_REQUEST[ord_num_orden]));
        $rst_pro = pg_fetch_array($Set->lista_un_producto($rst_ord[pro_id]));
        if ($rst_ord[ord_pro_secundario] == 0) {
            $rst_pro_secundario[pro_descripcion] = ' NINGUN PRODUCTO ';
        } else {
            $rst_pro_secundario = pg_fetch_array($Set->lista_un_producto($rst_ord[ord_pro_secundario]));
        }
        echo $rst_ord[ord_id] . "&" . $rst_pro[pro_descripcion] . "&" . $rst_pro_secundario[pro_descripcion] . "&" . $rst_ord[ord_rep_ancho];
        break;
//////////////////////////////////////////////////////////////////////////////////////////
////////////////////////// Orden Produccion - Plumon /////////////////////////////////////
    case 57:
        $sms = 0;
        if ($id == 0) {// Insertar
            if ($Set->insertar_orden_produccion_plumon($data) == false) {
                $sms = pg_last_error();
            } else {
                $rst_sec = pg_fetch_array($Set->lista_ped_sec());
                $sec = ($rst_sec[ped_orden] + 1);
                if ($sec >= 0 && $sec < 10) {
                    $tx_trs = "00000";
                } elseif ($sec >= 10 && $sec < 100) {
                    $tx_trs = "0000";
                } elseif ($sec >= 100 && $sec < 1000) {
                    $tx_trs = "000";
                } elseif ($sec >= 1000 && $sec < 10000) {
                    $tx_trs = "00";
                } elseif ($sec >= 10000 && $sec < 100000) {
                    $tx_trs = "0";
                } elseif ($sec >= 100000 && $sec < 1000000) {
                    $tx_trs = "";
                }
                $code = $tx_trs . $sec;
                $n = 0;
                $j = 7;
                $i = 16;
                while ($n < 4) {
                    $n++;
                    $j++;
                    $i++;
                    $ord_mp = array(
                        $code,
                        $data[23],
                        '3',
                        $data[$j],
                        $data[$i],
                        $data[$i],
                        $data[0]
                    );
                    if ($data[$j] != 0) {
                        if ($Set->insert_pmp($ord_mp) == false) {
                            $sms = 'insert pedido mp' . pg_last_error();
                        }
                    }
                }
                $accion = 'INSERTAR';
                if ($Set->lista_cambia_status_det($data[33], '9') == false) {
                    $sms = 'Cambio_estado_ped' . pg_last_error();
                }
            }
        } else {// Modificar
            if ($Set->modificar_orden_produccion_plumon($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                if ($Set->del_pmp_orden($data[0]) == false) {
                    $sms = 'del_pmp' . pg_last_error();
                } else {
                    $rst_sec = pg_fetch_array($Set->lista_ped_sec());
                    $sec = ($rst_sec[ped_orden] + 1);
                    if ($sec >= 0 && $sec < 10) {
                        $tx_trs = "00000";
                    } elseif ($sec >= 10 && $sec < 100) {
                        $tx_trs = "0000";
                    } elseif ($sec >= 100 && $sec < 1000) {
                        $tx_trs = "000";
                    } elseif ($sec >= 1000 && $sec < 10000) {
                        $tx_trs = "00";
                    } elseif ($sec >= 10000 && $sec < 100000) {
                        $tx_trs = "0";
                    } elseif ($sec >= 100000 && $sec < 1000000) {
                        $tx_trs = "";
                    }
                    $code = $tx_trs . $sec;
                    $n = 0;
                    $j = 7;
                    $i = 16;
                    while ($n < 4) {
                        $n++;
                        $j++;
                        $i++;
                        $ord_mp = array(
                            $code,
                            $data[23],
                            '3',
                            $data[$j],
                            $data[$i],
                            $data[$i],
                            $data[0]
                        );
                        if ($data[$j] != 0) {
                            if ($Set->insert_pmp($ord_mp) == false) {
                                $sms = 'insert pedido mp' . pg_last_error();
                            }
                        }
                    }
                }

                $accion = 'MODIFICAR';
            }
        }
        $n = 0;
        while ($n < count($fields)) {
            $f = $f . strtoupper($fields[$n] . '&');
            $n++;
        }
        $modulo = 'ORDEN PLUMON';
        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
            $sms = "Auditoria" . pg_last_error() . 'ok2';
        }
        echo $sms;
        break;
// Eliminar Orden Produccion - Plumon
    case 58:
        $sms = 0;
        if ($Set->del_pmp_orden($data) == false) {
            $sms = 'del_pmp' . pg_last_error();
        } else {
            if ($Set->delete_orden_produccion_plumon($id) == FALSE) {
                $sms = pg_last_error();
            }
        }
        $modulo = 'ORDEN PLUMON';
        $accion = 'ELIMINAR';
        if ($Adt->insert_audit_general($modulo, $accion, $f, $data) == false) {
            $sms = "Auditoria" . pg_last_error() . 'ok2';
        }
        echo $sms;
        break;

    case 59:
        $rst_pro = pg_fetch_array($Set->lista_un_producto($_REQUEST[id]));
        $rst_fbc = pg_fetch_array($Set->lista_una_fabrica($rst_pro[emp_id]));
        $rst_set = pg_fetch_array($Set->lista_utlimo_seteo_maquina_plumon($_REQUEST[id]));
        $cns_mp = $Set->lista_mp($rst_pro[emp_id]);
        $combo = "<option  value='0'> - Seleccione - </option>";
        while ($rst_mp = pg_fetch_array($cns_mp)) {
            $combo.="<option value='$rst_mp[mp_id]'>$rst_mp[mp_referencia]</option>";
        }
        $retorno = $rst_pro[pro_ancho] . "&" .
                $rst_pro[pro_mp1] . "&" .
                $rst_pro[pro_mp2] . "&" .
                $rst_pro[pro_mp3] . "&" .
                $rst_pro[pro_mp4] . "&" .
                $rst_pro[pro_mf1] . "&" .
                $rst_pro[pro_mf2] . "&" .
                $rst_pro[pro_mf3] . "&" .
                $rst_pro[pro_mf4] . "&" .
                ($rst_pro[pro_mf1] + $rst_pro[pro_mf2] + $rst_pro[pro_mf3] + $rst_pro[pro_mf4]) . "&" .
                $rst_pro[pro_largo] . "&" .
                $rst_pro[pro_peso] . "&" .
                $rst_pro[pro_gramaje] . "&" .
                $rst_fbc[emp_sigla] . "&" .
                $rst_set[orp_temperatura] . "&" .
                $rst_set[orp_agua] . "&" .
                $rst_set[orp_resina] . "&" .
                $combo;
        echo $retorno;
        break;
///////////////////////////////////////////////////////////////////////////////////////
    case 60:
        $sms = 0;

//        if ($data[25] == '[object Window]') { ///ord_pro_secundario
//            $data[25] = 0;
//        }
        if ($id == 0) {// Insertar
            if ($Set->insertar_orden_produccion($data) == false) {
                $sms = 'Insert' . pg_last_error();
            } else {
                if ($Set->insertar_formula_produccion($data) == false) {
                    $sms = 'Insert_formula' . pg_last_error();
                }
                $rst_sec = pg_fetch_array($Set->lista_ped_sec());
                $sec = ($rst_sec[ped_orden] + 1);
                if ($sec >= 0 && $sec < 10) {
                    $tx_trs = "00000";
                } elseif ($sec >= 10 && $sec < 100) {
                    $tx_trs = "0000";
                } elseif ($sec >= 100 && $sec < 1000) {
                    $tx_trs = "000";
                } elseif ($sec >= 1000 && $sec < 10000) {
                    $tx_trs = "00";
                } elseif ($sec >= 10000 && $sec < 100000) {
                    $tx_trs = "0";
                } elseif ($sec >= 100000 && $sec < 1000000) {
                    $tx_trs = "";
                }
                $code = $tx_trs . $sec;
                ///mp tornillo1
                $n = 0;
                $j = 6;
                $i = 43;
                while ($n < 18) {
                    $n++;
                    $j++;
                    $i++;
                    $ord_mp = array(
                        $code,
                        $data[68], ///ord_fec_entrega
                        '5',
                        $data[$j],
                        $data[$i],
                        $data[$i],
                        $data[0]///ord_num_orden
                    );
                    if ($data[$j] != 0) {
                        if ($Set->insert_pmp($ord_mp) == false) {
                            $sms = 'insert pedido mp tornillos' . pg_last_error();
                        }
                    }
                }
//                ////mp tornillo2
//                $n = 0;
//                $j = 73;
//                $i = 85;
//                while ($n < 6) {
//                    $n++;
//                    $j++;
//                    $i++;
//                    $ord_mp = array(
//                        $code,
//                        $data[19],
//                        '5',
//                        $data[$j],
//                        $data[$i],
//                        $data[$i],
//                        $data[0]
//                    );
//                    if ($data[$j] != 0) {
////                        if ($Set->insert_pmp($ord_mp) == false) {
////                            $sms = 'insert pedido mp t2' . pg_last_error();
////                        }
//                    }
//                }
//                ////mp tornillo3
//                $n = 0;
//                $j = 91;
//                $i = 103;
//                while ($n < 6) {
//                    $n++;
//                    $j++;
//                    $i++;
//                    $ord_mp = array(
//                        $code,
//                        $data[19],
//                        '5',
//                        $data[$j],
//                        $data[$i],
//                        $data[$i],
//                        $data[0]
//                    );
//                    if ($data[$j] != 0) {
////                        if ($Set->insert_pmp($ord_mp) == false) {
////                            $sms = 'insert pedido mp t3' . pg_last_error();
////                        }
//                    }
//                }
                ////mp empaque
                $n = 0;
                $j = 96;
                $i = 132;
                while ($n < 24) {
                    $n++;
                    $j++;
                    $i++;
                    $ord_mp = array(
                        $code,
                        $data[68], //fec_entrega
                        '5',
                        $data[$j], //mp_id
                        $data[$i], //kg
                        $data[$i], //kg
                        $data[0]//orden
                    );
                    if ($data[$j] != 0) {
                        if ($Set->insert_pmp($ord_mp) == false) {
                            $sms = 'insert pedido mp empq' . pg_last_error();
                        }
                    }
                }
                $accion = 'INSERTAR';
                if (!empty($data[186])) {
                    if ($Set->lista_cambia_status_det($data[186], '9') == true) {///
                        $rst_ped = pg_fetch_array($Set->lista_pedido($data[186]));
                        if ($Set->lista_cambia_status_pedido($rst_ped[ped_id], 9) == false) {///
                            $sms = 'Cambio_estado_ped' . pg_last_error();
                        }
                    } else {
                        $sms = 'Cambio_estado_ped' . pg_last_error();
                    }
                }
            }
        } else {// Modificar
            if ($Set->modificar_orden_produccion($data, $id) == false) {
                $sms = 'upd' . pg_last_error();
            } else {
                if ($Set->modificar_formula_produccion($data, $id) == false) {
                    $sms = 'upd' . pg_last_error();
                }
                if ($Set->del_pmp_orden($data[0]) == false) {
                    $sms = 'del_pmp' . pg_last_error();
                } else {
                    $rst_sec = pg_fetch_array($Set->lista_ped_sec());
                    $sec = ($rst_sec[ped_orden] + 1);
                    if ($sec >= 0 && $sec < 10) {
                        $tx_trs = "00000";
                    } elseif ($sec >= 10 && $sec < 100) {
                        $tx_trs = "0000";
                    } elseif ($sec >= 100 && $sec < 1000) {
                        $tx_trs = "000";
                    } elseif ($sec >= 1000 && $sec < 10000) {
                        $tx_trs = "00";
                    } elseif ($sec >= 10000 && $sec < 100000) {
                        $tx_trs = "0";
                    } elseif ($sec >= 100000 && $sec < 1000000) {
                        $tx_trs = "";
                    }
                    $code = $tx_trs . $sec;
                    ///mp tornillo1
                    $n = 0;
                    $j = 6;
                    $i = 43;
                    while ($n < 18) {
                        $n++;
                        $j++;
                        $i++;
                        $ord_mp = array(
                            $code,
                            $data[68], ///ord_fec_entrega
                            '5',
                            $data[$j],
                            $data[$i],
                            $data[$i],
                            $data[0]///ord_num_orden
                        );
                        if ($data[$j] != 0) {
                            if ($Set->insert_pmp($ord_mp) == false) {
                                $sms = 'insert pedido mp t0rnillos' . pg_last_error();
                            }
                        }
                    }
//                    ////mp tornillo2
//                    $n = 0;
//                    $j = 73;
//                    $i = 85;
//                    while ($n < 6) {
//                        $n++;
//                        $j++;
//                        $i++;
//                        $ord_mp = array(
//                            $code,
//                            $data[19],
//                            '5',
//                            $data[$j],
//                            $data[$i],
//                            $data[$i],
//                            $data[0]
//                        );
////                        if ($data[$j] != 0) {
////                            if ($Set->insert_pmp($ord_mp) == false) {
////                                $sms = 'insert pedido mp t2' . pg_last_error();
////                            }
////                        }
//                    }
//                    ////mp tornillo3
//                    $n = 0;
//                    $j = 91;
//                    $i = 103;
//                    while ($n < 6) {
//                        $n++;
//                        $j++;
//                        $i++;
//                        $ord_mp = array(
//                            $code,
//                            $data[19],
//                            '5',
//                            $data[$j],
//                            $data[$i],
//                            $data[$i],
//                            $data[0]
//                        );
//                        if ($data[$j] != 0) {
////                            if ($Set->insert_pmp($ord_mp) == false) {
////                                $sms = 'insert pedido mp t3' . pg_last_error();
////                            }
//                        }
//                    }
                    ////mp empaque
                    $n = 0;
                    $j = 96;
                    $i = 132;
                    while ($n < 24) {
                        $n++;
                        $j++;
                        $i++;
                        $ord_mp = array(
                            $code,
                            $data[68], //fec_entrega
                            '5',
                            $data[$j], //mp_id
                            $data[$i], //kg
                            $data[$i], //kg
                            $data[0]//orden
                        );
                        if ($data[$j] != 0) {
                            if ($Set->insert_pmp($ord_mp) == false) {
                                $sms = 'insert pedido mp empq' . pg_last_error();
                            }
                        }
                    }
                }
                $accion = 'MODIFICAR';
            }
        }

        $n = 0;
        while ($n < count($fields)) {
            $f = $f . strtoupper($fields[$n] . '&');
            $n++;
        }
        $modulo = 'ORDEN ECOCAMBRELLA';
        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
            $sms = "Auditoria" . pg_last_error() . 'ok2';
        }
        echo $sms;

        break;
    case 61:
        $sms = 0;

        if ($Set->del_pmp_orden($data) == false) {
            $sms = 'del_pmp' . pg_last_error();
        } else {
            if ($Set->delete_orden_produccion($id) == false) {
                $sms = pg_last_error();
            } else {
                $modulo = 'ORDEN ECOCAMBRELLA';
                $accion = 'ELIMINAR';
                $f = '';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 62:
//echo md5($id);
        $sms = 0;
        $User = new User();
        if (md5($data[1]) == $data[2]) {
            if ($User->cambia_clave(md5($data[0]), $id) == false) {
                $sms = pg_last_error();
            }
        } else {
            $sms = 'Clave Anterior Incorrecta';
        }

        echo $sms;
        break;

    case 63:
        $sms = '';
        if ($s == 0) {
            $cns = $Set->lista_clientes_search(strtoupper($id));
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[cli_raz_social];
                $sms .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_id]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
        } else {
            $rst = pg_fetch_array($Set->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . ' ' . $rst[cli_numeracion] . ' ' . $rst[cli_calle_sec] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_parroquia] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst[cli_id] . '&' . $rst[tipo_cliente] . '&' . $rst[cli_estado];
            }
        }
        echo $sms;
        break;
    ///CAMBIOS CRISTINA
    case 64:
//        $rst_em = pg_fetch_array($Set->lista_emisor($s));
//        $em = $rst_em[cod_orden];
//        if (strlen($_REQUEST[lt]) >= 8) {
//            $tabla = 1;
//            $rst = pg_fetch_array($Set->lista_un_producto_noperti_cod_lote($id, $_REQUEST[lt]));
//            $rst_precio1 = pg_fetch_array($Set->lista_precio_producto($rst[id], $tabla));
//            if ($rst_precio1[pre_vald_precio1] == 1) {
//                $rst_precio1[pre_precio] = $rst_precio1[pre_precio];
//            } else {
//                $rst_precio1[pre_precio] = $rst_precio1[pre_precio2];
//            }
//            $rst_desc = pg_fetch_array($Set->lista_descuento_producto($rst_precio1[pre_id], $em));
//            $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fac($rst[id], $s, '1'));
//            $inv = $rst1[ingreso] - $rst1[egreso];
//            echo $rst[pro_a] . '&' . $rst[pro_b] . '&' . $rst[pro_uni] . '&' . $rst_precio1[pre_precio] . '&' . $rst_precio1[pre_iva] . '&' . $rst_desc[dsc_descuento] . '&' . $rst_precio1[pre_ice] . '&' . $rst[pro_ad] . '&' . $rst[pro_ac] . '&' . $rst[id] . '&1&' . $inv;
//        } else {
//            $tbl = substr($id, 0, 1);
//            $id = substr($id, 1, (strlen($id) - 1));
//            if ($tbl == 1) {
//                $rst = pg_fetch_array($Set->lista_un_producto_noperti_id($id));
//                if ($rst[id] != '') {
//                    $rst_precio1 = pg_fetch_array($Set->lista_precio_producto($rst[id], $tbl));
//                    if ($rst_precio1[pre_vald_precio1] == 1) {
//                        $rst_precio1[pre_precio] = $rst_precio1[pre_precio];
//                    } else {
//                        $rst_precio1[pre_precio] = $rst_precio1[pre_precio2];
//                    }
//
//                    $rst_desc = pg_fetch_array($Set->lista_descuento_producto($rst_precio1[pre_id], $em));
//                    $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fac($rst[id], $s, '1'));
//                    $inv = $rst1[ingreso] - $rst1[egreso];
//                    echo $rst[pro_a] . '&' . $rst[pro_b] . '&' . $rst[pro_uni] . '&' . $rst_precio1[pre_precio] . '&' . $rst_precio1[pre_iva] . '&' . $rst_desc[dsc_descuento] . '&' . $rst_precio1[pre_ice] . '&' . $rst[pro_ad] . '&' . $rst[pro_ac] . '&' . $rst[id] . '&1&' . $inv;
//                }
//            } else {

        $rst = pg_fetch_array($Set->lista_un_producto_industrial_id($id));
//        if ($rst[pro_id] != '') {
        $rst_precio = pg_fetch_array($Set->lista_precio_producto($rst[pro_id], $tbl));
        if ($rst_precio[pre_vald_precio1] == 1) {
            $rst_precio[pre_precio] = $rst_precio[pre_precio];
        } else {
            $rst_precio[pre_precio] = $rst_precio[pre_precio2];
        }
        $rst_desc = pg_fetch_array($Set->lista_descuento_producto($rst_precio[pre_id], $em));

        if (!empty($_REQUEST[lt])) {
            $rst1 = pg_fetch_array($Set->total_ingreso_egreso_lote($_REQUEST[lt]));
            $rst = pg_fetch_array($Set->lista_movimiento_lote($_REQUEST[lt]));
        } else {
            $rst = pg_fetch_array($Set->lista_un_producto($id));
            if ($rst[pro_id] != '') {
                $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fac($rst[pro_id], $_REQUEST[lt]));
            }
        }

        $cns_lote = $Set->lista_lotes_movimiento($rst[pro_id]);
        $lotes = "";
        while ($rst_lt = pg_fetch_array($cns_lote)) {
            $lotes.="<option value='$rst_lt[mov_pago]'>$rst_lt[mov_pago]</option>";
        }

//            $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fac($rst[pro_id], $_REQUEST[lt]));
        $inv = $rst1[ingreso] - $rst1[egreso];
        echo $rst[pro_codigo] . '&' . $rst[pro_descripcion] . '&' . $rst[pro_uni] . '&' . $rst_precio[pre_precio] . '&' . $rst_precio[pre_iva] . '&' . $rst_desc[dsc_descuento] . '&' . $rst_precio[pre_ice] . '& & &' . $rst[pro_id] . '&0&' . $inv . '&' . $lotes;
//        }
//            }
//        }
        break;
    case 65:
        $sms = 0;
        $aud = 0;

        if (empty($id)) {// Insertar
            $rstcliente = pg_num_rows($Set->lista_un_cliente_cedula($data[2]));
            if ($rstcliente > 0) {
                $data3 = array(
                    strtoupper($data[17]),
                    strtoupper($data[18]),
                    strtoupper($data[19]),
                    strtoupper($data[20]),
                    strtoupper($data[21]),
                    strtoupper($data[24])
                );
                if ($Set->upd_email_cliente($data3, $data[2]) == false) {
                    $sms = 'Insert_email' . pg_last_error() . $data[17] . '&' . $data[18] . '&' . $data[19] . '&' . $data[20] . '&' . $data[21] . '&' . $data[24];
                    $v = 1;
                }
            } else {
                if (strlen($data[2]) < 11) {
                    $tipo = 'CN';
                } else {
                    $tipo = 'CJ';
                }
                $rst_cod = pg_fetch_array($Clases_preciospt->lista_secuencial_cliente($tipo));
                $sec = (substr($rst_cod[cli_codigo], -5) + 1);

                if ($sec >= 0 && $sec < 10) {
                    $txt = '0000';
                } else if ($sec >= 10 && $sec < 100) {
                    $txt = '000';
                } else if ($sec >= 100 && $sec < 1000) {
                    $txt = '00';
                } else if ($sec >= 1000 && $sec < 10000) {
                    $txt = '0';
                } else if ($sec >= 10000 && $sec < 100000) {
                    $txt = '';
                }

                $retorno = $tipo . $txt . $sec;

                $da = array(
                    strtoupper($data[1]),
                    strtoupper($data[2]),
                    strtoupper($data[17]),
                    strtoupper($data[19]),
                    strtoupper($data[18]),
                    strtoupper($data[20]),
                    strtoupper($data[21]),
                    $retorno,
                    strtoupper($data[24])
                );
                if ($Set->insert_cliente($da) == false) {
                    $sms = 'Insert_cli' . pg_last_error();
                    $v = 1;
                    $aud = 1;
                }
            }

            if ($data[23] >= 10) {
                $ems = '0' . $data[23];
            } else {
                $ems = '00' . $data[23];
            }
            $rst_sec = pg_fetch_array($Set->lista_secuencial_documento($ems));
            $sec = ($rst_sec[secuencial] + 1);
            if ($sec >= 0 && $sec < 10) {
                $tx = '00000000';
            } else if ($sec >= 10 && $sec < 100) {
                $tx = '0000000';
            } else if ($sec >= 100 && $sec < 1000) {
                $tx = '000000';
            } else if ($sec >= 1000 && $sec < 10000) {
                $tx = '00000';
            } else if ($sec >= 10000 && $sec < 100000) {
                $tx = '0000';
            } else if ($sec >= 100000 && $sec < 1000000) {
                $tx = '000';
            } else if ($sec >= 1000000 && $sec < 10000000) {
                $tx = '00';
            } else if ($sec >= 10000000 && $sec < 100000000) {
                $tx = '0';
            } else if ($sec >= 100000000 && $sec < 1000000000) {
                $tx = '';
            }
            $num_factura = $ems . '-001-' . $tx . $sec;
            $secuencial = $tx . $sec;
            $nfact = str_replace('-', '', $num_factura);


            if ($v == 0) {
                $dt1 = explode('&', $data4[0]);
//                $rst_compp = pg_fetch_array($Clase_pagos->lista_un_comprobante($data[25]));
//                $comprobantes = $rst_compp[com_id];
                if ($dt1[0] == 1 || $dt1[0] == 10) {
                    $m = 0;
                    $i = count($data4);
                    $pg = 1;
//                    $nfact = str_replace('-', '', $data[25]);
                    while ($m < $i) {
                        $dt1 = explode('&', $data4[$m]);
                        $data5 = array(
                            $num_factura,
                            $pg,
                            $dt1[1],
                            $dt1[2],
                            $dt1[3],
                            $dt1[4],
                            0,
                            0,
                            0,
                            0,
                            0
                        );
                        if ($Clase_pagos->insert_pagos($data5) == false) {
                            $sms = 'Insert_pagos1' . pg_last_error();
                            $aud = 1;
                        }
                        $m++;
                    }
                } else {
                    $dt1 = explode('&', $data4[0]);
                    $m = 0;
                    $i = count($data4);
                    while ($m < $i) {
                        $dt1 = explode('&', $data4[$m]);
                        $pg = 0;
//                        $nfact = str_replace('-', '', $data[25]);
                        $data5 = array(
                            $num_factura,
                            $pg,
                            0,
                            0,
                            0,
                            date('Y-m-d'),
                            $dt1[1],
                            $dt1[2],
                            $dt1[3],
                            $dt1[4],
                            $dt1[5]
                        );
                        if ($dt1[1] != 0) {
                            if ($Clase_pagos->insert_pagos($data5) == false) {
                                $sms = 'Insert_pagos2' . pg_last_error() . $data3;
                                $aud = 1;
                            }
                        }
                        $m++;
                    }
                }
                if ($data[28] == 0) {
                    $estp = '4';
                } else {
                    $estp = '3';
                }
                if ($Set->lista_cambia_status($data[29], $estp) == false) {
                    $sms = pg_last_error();
                }

//                print_r($data);


                if ($Set->insert_factura($data, $ems, $secuencial, $num_factura) == true) {
                    $n = 0;
                    $i = count($data2);
                    while ($n < $i) {
                        $dt = explode('&', $data2[$n]);
                        if ($Set->insert_detalle_factura($dt, $nfact) == true) {

                            $tab = substr($dt[14], 0, 1);
                            $rst_pro[pro_id] = substr($dt[14], 1, 50);
                            $bod = $data[23];
                            if ($tab == 0) {
                                if (($rst_pro[emp_id] == 3 || $rst_pro[emp_id] == 4) && $data[23] == 1) {
                                    $bod = '10';
                                } else {
                                    $bod = $data[23];
                                }
                            }

                            $rst_cli = pg_fetch_array($Set->lista_un_cliente_cedula($data[2]));
                            $dat = array(
                                $rst_pro[pro_id],
                                25,
                                $rst_cli[cli_id],
                                $bod, ///BODEGA
                                $dt[0],
                                '0',
                                '0',
                                date('Y-m-d'),
                                date('Y-m-d'),
                                date('H:i:s'),
                                $dt[3],
                                '',
                                date('Y-m-d'),
                                $dt[0],
                                '',
                                '',
                                0,
                                0,
                                0,
                                0,
                                $tab
                            );
                            if ($Set->insert_movimiento_pt($dat, $nfact) == false) {
                                $sms = 'Insert_mov' . pg_last_error();
                                $aud = 1;
                            }
                        } else {
                            $sms = 'Insert_det' . pg_last_error();
                            $aud = 1;
                        }
                        $n++;
                    }
                } else {
                    $sms = 'Insert' . pg_last_error();
                    $accion = 'Insertar';
                    $aud = 1;
                }
            }

            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    if ($n == 0) {
                        $f = $f . "NUM_SECUENCIAL=$num_factura&";
                    } else {
                        $f = $f . strtoupper($fields[$n] . '&');
                    }

                    $n++;
                }
                $modulo = 'FACTURA';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $num_factura) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {// Modificar
            $nfact = str_replace('-', '', $data[25]);
            if ($usu == 1) {
                if ($Set->elimina_movpt_documento($nfact, '25') == true) {
                    if ($Set->elimina_detalle_factura($nfact) == true) {
                        if ($Set->elimina_factura($data[25]) == false) {
                            $sms = 'del' . pg_last_error();
                            $aud = 1;
                        }
                    }
                } else {
                    $sms = 'del_det' . pg_last_error();
                    $aud = 1;
                }

                if ($Set->insert_factura($data, $data[23], $data[0], $data[25]) == true) {
                    $n = 0;
                    $i = count($data2);
                    while ($n < $i) {
                        $dt = explode('&', $data2[$n]);
                        if ($Set->insert_detalle_factura($dt) == true) {
                            $tab = substr($dt[14], 0, 1);
                            $rst_pro[pro_id] = substr($dt[14], 1, 50);
                            $bod = $data[23];
                            if ($tab == 0) {
                                if (($rst_pro[emp_id] == 3 || $rst_pro[emp_id] == 4) && $data[23] == 1) {
                                    $bod = '10';
                                } else {
                                    $bod = $data[23];
                                }
                            }
                            $rst_cli = pg_fetch_array($Set->lista_un_cliente_cedula($data[2]));
                            $dat = array(
                                $rst_pro[pro_id],
                                25,
                                $rst_cli[cli_id],
                                $bod,
                                $dt[0],
                                '0',
                                '0',
                                date('Y-m-d'),
                                date('Y-m-d'),
                                date('H:i:s'),
                                $dt[3],
                                '',
                                date('Y-m-d'),
                                $dt[0],
                                '',
                                '',
                                0,
                                0,
                                0,
                                0,
                                $tab);
                            if ($Set->insert_movimiento_pt($dat, $data[25]) == false) {
                                $sms = 'Insert_mov' . pg_last_error() . '----' . $data[2];
                                $aud = 1;
                            }
                        } else {
                            $sms = 'Insert_det' . pg_last_error();
                            $aud = 1;
                        }
                        $n++;
                    }

                    if ($Clase_pagos->delete_pagos($data[25]) == false) {
                        $sms = 'Delete_pagos1' . pg_last_error();
                        $aud = 1;
                    } else {

                        $dt1 = explode('&', $data4[0]);
                        if ($dt1[0] == 1 || $dt1[0] == 10) {
                            $m = 0;
                            $i = count($data4);
                            $pg = 1;
                            $nfact = str_replace('-', '', $data[25]);
                            while ($m < $i) {
                                $dt1 = explode('&', $data4[$m]);
                                $data5 = array(
                                    $data[25],
                                    $pg,
                                    $dt1[1],
                                    $dt1[2],
                                    $dt1[3],
                                    $dt1[4],
                                    0,
                                    0,
                                    0,
                                    0,
                                    0
                                );
                                if ($Clase_pagos->insert_pagos($data5) == false) {
                                    $sms = 'Insert_pagos1' . pg_last_error();
                                    $aud = 1;
                                }
                                $m++;
                            }
                        } else {
                            $dt1 = explode('&', $data4[0]);
                            $m = 0;
                            $i = count($data4);
                            while ($m < $i) {
                                $dt1 = explode('&', $data4[$m]);
                                $pg = 0;
                                $nfact = str_replace('-', '', $data[25]);
                                $data5 = array(
                                    $data[25],
                                    $pg,
                                    0,
                                    0,
                                    0,
                                    20150122,
                                    $dt1[1],
                                    $dt1[2],
                                    $dt1[3],
                                    $dt1[4],
                                    $dt1[5]
                                );
                                if ($dt1[1] != 0) {
                                    if ($Clase_pagos->insert_pagos($data5) == false) {
                                        $sms = 'Insert_pagos2' . pg_last_error() . $data3;
                                        $aud = 1;
                                    }
                                }
                                $m++;
                            }
                        }
                    }
                } else {
                    $sms = 'Insert' . pg_last_error();
                    $accion = 'Insertar';
                    $aud = 1;
                }
            } else {
                if ($Clase_pagos->delete_pagos($data[25]) == false) {
                    $sms = 'Delete_pagos1' . pg_last_error();
                    $aud = 1;
                } else {

                    $dt1 = explode('&', $data4[0]);
                    if ($dt1[0] == 1 || $dt1[0] == 10) {
                        $m = 0;
                        $i = count($data4);
                        $pg = 1;
                        $nfact = str_replace('-', '', $data[25]);
                        while ($m < $i) {
                            $dt1 = explode('&', $data4[$m]);
                            $data5 = array(
                                $data[25],
                                $pg,
                                $dt1[1],
                                $dt1[2],
                                $dt1[3],
                                $dt1[4],
                                0,
                                0,
                                0,
                                0,
                                0
                            );
                            if ($Clase_pagos->insert_pagos($data5) == false) {
                                $sms = 'Insert_pagos1' . pg_last_error();
                                $aud = 1;
                            }
                            $m++;
                        }
                    } else {
                        $dt1 = explode('&', $data4[0]);
                        $m = 0;
                        $i = count($data4);
                        while ($m < $i) {
                            $dt1 = explode('&', $data4[$m]);
                            $pg = 0;
                            $nfact = str_replace('-', '', $data[25]);
                            $data5 = array(
                                $data[25],
                                $pg,
                                0,
                                0,
                                0,
                                20150122,
                                $dt1[1],
                                $dt1[2],
                                $dt1[3],
                                $dt1[4],
                                $dt1[5]
                            );
                            if ($dt1[1] != 0) {
                                if ($Clase_pagos->insert_pagos($data5) == false) {
                                    $sms = 'Insert_pagos2' . pg_last_error() . $data3;
                                    $aud = 1;
                                }
                            }
                            $m++;
                        }
                    }
                }
            }
            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'FACTURA';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[25]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
            $num_factura = $data[25];
        }
        $rst_com = pg_fetch_array($Set->lista_una_factura_nfact($num_factura));
        echo $sms . '&' . $rst_com[com_id] . '&' . $mesaje . '&' . $num_factura;
        break;
///CAMBIOS CRISTINA
    case 66:
        $rst = pg_fetch_array($Set->lista_un_producto_noperti_id($id));
        $rst_precio1 = pg_fetch_array($Set->lista_precio_producto($rst[id]));
        echo $rst[pro_a] . '&' . $rst[pro_b] . '&' . $rst[pro_uni] . '&' . $rst_precio1[pre_precio] . '&' . $rst_precio1[pre_iva] . '&' . $rst_precio1[pre_descuento] . '&' . $rst_precio1[pre_ice] . '&' . $rst[pro_ad] . '&' . $rst[pro_ac];
        break;
    case 67:
        $sms = 0;
        $dt0 = $Adt->sanear_string($data[0]); //Clave de acceso
        $dt1 = $Adt->sanear_string($data[1]); // Recepcion
        $dt2 = $Adt->sanear_string($data[2]); // Autorizacion
        $dt3 = $Adt->sanear_string($data[3]); // Mensaje
        $dt4 = $Adt->sanear_string($data[4]); // Numero Autorizacion
        $dt5 = $data[5];                      // Hora y fecha Autorizacion
        $dt6 = $data[6];                      // XML
        $dat = array($dt0, $dt1, $dt2, $dt3, $dt4, $dt5, $dt6);
        if ($Set->upd_fac_nd_nc($dat, $id) == false) {
            $sms = 'upd_doc1' . pg_last_error();
        }
        $rst = pg_fetch_array($Set->lista_una_factura_id($id));
        echo $sms . "&" . $rst[num_documento] . '&' . $dt[4];
        break;
    case 68:
        $sms = 0;
        $dt0 = $Adt->sanear_string($data[0]); //Clave de acceso
        $dt1 = $Adt->sanear_string($data[1]); // Recepcion
        $dt2 = $Adt->sanear_string($data[2]); // Autorizacion
        $dt3 = $Adt->sanear_string($data[3]); // Mensaje
        $dt4 = $Adt->sanear_string($data[4]); // Numero Autorizacion
        $dt5 = $data[5];                      // Hora y fecha Autorizacion
        $dat = array($dt0, $dt1, $dt2, $dt3, $dt4, $dt5);
        $idt = str_replace('-', '', $id);
        if ($Set->upd_retencion($dat, $idt) == false) {
            $sms = 'upd_ret' . pg_last_error();
        }
        echo $sms;
        break;
    case 69:
        $sms = 0;
        $dt0 = $Adt->sanear_string($data[0]); //Clave de acceso
        $dt1 = $Adt->sanear_string($data[1]); // Recepcion
        $dt2 = $Adt->sanear_string($data[2]); // Autorizacion
        $dt3 = $Adt->sanear_string($data[3]); // Mensaje
        $dt4 = $Adt->sanear_string($data[4]); // Numero Autorizacion
        $dt5 = $data[5];                      // Hora y fecha Autorizacion
        $dat = array($dt0, $dt1, $dt2, $dt3, $dt4, $dt5);
        if ($Set->upd_gui_rem($dat, $id) == false) {
            $sms = 'upd' . pg_last_error();
        }
        echo $sms;
        break;
    case 70:
        $rst = pg_fetch_array($Set->lista_obs_documentos($id));
        echo $rst[com_observacion];
        break;
    case 71:
        $cns = $Set->lista_factura_completo();
        while ($rst = pg_fetch_array($cns)) {
            if (empty($rst[clave_acceso])) {
                $f = $rst['fecha_emision'];
                $f2 = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
                $cod_doc = "01"; //01= factura, 02=nota de credito tabla 4
                $emis[identificacion] = '1790007871001'; //Noperti
                $ambiente = 2;
                if ($rst[cod_punto_emision] >= 10) {
                    $ems = '0' . $rst[cod_punto_emision];
                } else {
                    $ems = '00' . $rst[cod_punto_emision];
                }

                $sec = $rst[num_secuencial];
                if ($sec >= 0 && $sec < 10) {
                    $tx = "00000000";
                } else if ($sec >= 10 && $sec < 100) {
                    $tx = "0000000";
                } else if ($sec >= 100 && $sec < 1000) {
                    $tx = "000000";
                } else if ($sec >= 1000 && $sec < 10000) {
                    $tx = "00000";
                } else if ($sec >= 10000 && $sec < 100000) {
                    $tx = "0000";
                } else if ($sec >= 100000 && $sec < 1000000) {
                    $tx = "000";
                } else if ($sec >= 1000000 && $sec < 10000000) {
                    $tx = "00";
                } else if ($sec >= 10000000 && $sec < 100000000) {
                    $tx = "0";
                } else if ($sec >= 100000000 && $sec < 1000000000) {
                    $tx = "";
                }
                $secuencial = $tx . $sec;

                $codigo = "12345678"; //Del ejemplo del SRI                    
                $tp_emison = "1"; //Emision Normal                    
                $clave1 = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . "001" . $secuencial . $codigo . $tp_emison);
                $cla = strrev($clave1);
                $n = 0;
                $p = 1;
                $i = strlen($clave1);
                $m = 0;
                $s = 0;
                $j = 2;
                while ($n < $i) {
                    $d = substr($cla, $n, 1);
                    $m = $d * $j;
                    $s = $s + $m;
                    $j++;
                    if ($j == 8) {
                        $j = 2;
                    }
                    $n++;
                }
                $div = $s % 11;
                $digito = 11 - $div;
                if ($digito < 10) {
                    $digito = $digito;
                } else if ($digito == 10) {
                    $digito = 1;
                } else if ($digito == 11) {
                    $digito = 0;
                }
                $clave = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . "001" . $secuencial . $codigo . $tp_emison . $digito);
                if (strlen($clave) != 49) {
                    $clave = '';
                }
                $Set->upd_fac_clave_acceso($clave, $rst[com_id]);
            }
        }
        break;
    case 72:
        $sms = 0;
        if ($Set->upd_fac_na($_REQUEST[na], $_REQUEST[fh], $id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 73:
        $sms = 0;
        $dat = 'ENVIADO';
        if ($Set->upd_env_ema($dat, $id) == false) {
            $sms = 'upd_doc2' . pg_last_error();
        }
        echo $sms;
        break;
    case 74:
        $sms = 0;
        $dat = 'ENVIADO';
        if ($Set->upd_env_ema_gui($dat, $id) == false) {
            $sms = 'upd_doc2' . pg_last_error();
        }
        echo $sms;
        break;
    case 75:
//        $sms = 0;
        $not = str_replace('-', '', $id);
        $dat = 'ENVIADO';
        if ($Set->upd_env_ema_no($dat, $not) == false) {
            $sms = 'upd_doc3' . pg_last_error();
        }
//        echo $sms;
        break;
    case 76:
//        $sms = 0;
        $deb = str_replace('-', '', $id);
        $dat = 'ENVIADO';
        if ($Set->upd_env_ema_ret($dat, $deb) == false) {
            $sms = 'upd_doc4' . pg_last_error();
        }
//        echo $sms;
        break;
    case 77:
//        $sms = 0;
        $not = str_replace('-', '', $id);
        $dat = 'ENVIADO';
        if ($Set->upd_env_ema_nodeb($dat, $not) == false) {
            $sms = 'upd_doc6' . pg_last_error();
        }
//        echo $sms;
        break;
    case 78:
        $sms = 0;
        if ($Set->upd_ambiente($id, $data) == false) {
            $sms = pg_last_error();
        } else {
            $dt2 = array($data2[1],
                $data2[2],
                $data2[3]);
            if ($Set->upd_configuraciones_sueldo($data2[0], $dt2) == false) {
                $sms = 'upd_sueldo' . pg_last_error();
            }
            $dt3 = array($data4[1],
                $data4[2],
                $data4[3]);
            if ($Set->upd_configuraciones_sueldo($data4[0], $dt3) == false) {
                $sms = 'upd_sueldo' . pg_last_error();
            }
            if ($Set->upd_sueldo_basico($data5) == false) {
                $sms = 'upd_sueldo_basico' . pg_last_error();
            }
            if ($Set->upd_sueldo_basico_empleado($data5) == false) {
                $sms = 'upd_sueldo_basico_emp' . pg_last_error();
            }
            //// decimales moneda /////
            $data6 = array(
                $nom,
                '0',
                '0'
            );
            if ($Set->upd_configuraciones_sueldo('6', $data6) == false) {
                $sms = 'upd_moneda' . pg_last_error();
            }

            /// decimales cantidad /////
            $data7 = array(
                $usu,
                '0',
                '0'
            );
            if ($Set->upd_configuraciones_sueldo('7', $data7) == false) {
                $sms = 'upd_cantidad' . pg_last_error();
            }



            $modulo = 'CONFIGURACIONES';
            $accion = 'CAMBIO AMBIENTE';
            if ($Adt->insert_audit_general($modulo, $accion, '', '') == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 79:
        $sms = 0;
        if ($Set->delete_mov_insumos($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 80:
        $rst = pg_fetch_array($Set->lista_utimo_insumo($id));
        $rst1 = pg_fetch_array($Set->lista_un_insumo($id));
        echo $rst[mov_v_unit] . '&' . $rst1[ins_b] . '&' . $id . '&' . $rst1[ins_a];
        break;

    case 81:
        $rst = pg_fetch_array($Set->lista_clientes_codigo($id));
        echo $rst[cli_raz_social] . '&' . $rst[cli_id];
        break;
    case 82:
        $sms = 0;
        $n = 0;
        if (empty($data)) {
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'ORDENES DE COMPRA';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $id) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        } else {
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'ORDENES DE COMPRA';
            $accion = 'MODIFICAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $id) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 83:
        $sms = 0;
        $n = 0;
        while ($n < count($fields)) {
            $f = $f . strtoupper($fields[$n] . '&');
            $n++;
        }
        if ($s == 1) {
            $modulo = 'INGRESO MP INDUSTRIAL';
        } else if ($s == 2) {
            $modulo = 'PEDIDO MP INDUSTRIAL';
        } else if ($s == 3) {
            $modulo = 'MOVIMIENTO MP INDUSTRIAL';
        } else {
            $modulo = 'MOVIMIENTO MP CONFECCIONES';
        }
        if ($sts == 1) {
            $accion = 'MODIFICAR';
        } else {
            $accion = 'INSERTAR';
        }
        if ($Adt->insert_audit_general($modulo, $accion, $f, $id) == false) {
            $sms = "Auditoria" . pg_last_error() . 'ok2';
        }

        echo $sms;
        break;

    case 84:
        $sms = 0;
        $val = $data[0] . '&' . $data[1] . '&' . $data[2] . '&' . $data[3] . '&' . $data[4] . '&' . $data[5] . '&' . $data[6] . '&' . $data[7];
        if ($Set->update_conf_email('2', $val) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 85:
        $tor = $_REQUEST[tornillo];
        $rst_pro = pg_fetch_array($Set->lista_un_producto($_REQUEST[id]));
        switch ($tor) {
            case 1:
                echo $rst_pro[pro_mp1] . "&" .
                $rst_pro[pro_mp2] . "&" .
                $rst_pro[pro_mp3] . "&" .
                $rst_pro[pro_mp4] . "&" .
                $rst_pro[pro_mp5] . "&" .
                $rst_pro[pro_mp6] . "&" .
                $rst_pro[pro_mf1] . "&" .
                $rst_pro[pro_mf2] . "&" .
                $rst_pro[pro_mf3] . "&" .
                $rst_pro[pro_mf4] . "&" .
                $rst_pro[pro_mf5] . "&" .
                $rst_pro[pro_mf6] . "&" .
                ($rst_pro[pro_mf1] + $rst_pro[pro_mf2] + $rst_pro[pro_mf3] + $rst_pro[pro_mf4] + $rst_pro[pro_mf5] + $rst_pro[pro_mf6]);
                break;
            case 2:
                echo $rst_pro[pro_mp7] . "&" .
                $rst_pro[pro_mp8] . "&" .
                $rst_pro[pro_mp9] . "&" .
                $rst_pro[pro_mp10] . "&" .
                $rst_pro[pro_mp11] . "&" .
                $rst_pro[pro_mp12] . "&" .
                $rst_pro[pro_mf7] . "&" .
                $rst_pro[pro_mf8] . "&" .
                $rst_pro[pro_mf9] . "&" .
                $rst_pro[pro_mf10] . "&" .
                $rst_pro[pro_mf11] . "&" .
                $rst_pro[pro_mf12] . "&" .
                ($rst_pro[pro_mf7] + $rst_pro[pro_mf8] + $rst_pro[pro_mf9] + $rst_pro[pro_mf10] + $rst_pro[pro_mf11] + $rst_pro[pro_mf12]);
                break;
            case 3:
                echo $rst_pro[pro_mp13] . "&" .
                $rst_pro[pro_mp14] . "&" .
                $rst_pro[pro_mp15] . "&" .
                $rst_pro[pro_mp16] . "&" .
                $rst_pro[pro_mp17] . "&" .
                $rst_pro[pro_mp18] . "&" .
                $rst_pro[pro_mf13] . "&" .
                $rst_pro[pro_mf14] . "&" .
                $rst_pro[pro_mf15] . "&" .
                $rst_pro[pro_mf16] . "&" .
                $rst_pro[pro_mf17] . "&" .
                $rst_pro[pro_mf18] . "&" .
                ($rst_pro[pro_mf13] + $rst_pro[pro_mf14] + $rst_pro[pro_mf15] + $rst_pro[pro_mf16] + $rst_pro[pro_mf17] + $rst_pro[pro_mf18]);
                break;
        }

        break;
    case 86:

        $rst_pro = pg_fetch_array($Set->lista_una_formula($id));
        $cns_mp = $Set->lista_mp0();
        $combo = "<option  value='0'> - Seleccione - </option>";
        while ($rst_mp = pg_fetch_array($cns_mp)) {
            $combo.="<option value='$rst_mp[mp_id]'>$rst_mp[mp_referencia]</option>";
        }

        echo $combo . "&" .
        $rst_pro[ord_mp1] . "&" .
        $rst_pro[ord_mp2] . "&" .
        $rst_pro[ord_mp3] . "&" .
        $rst_pro[ord_mp4] . "&" .
        $rst_pro[ord_mp5] . "&" .
        $rst_pro[ord_mp6] . "&" .
        $rst_pro[ord_mp7] . "&" .
        $rst_pro[ord_mp8] . "&" .
        $rst_pro[ord_mp9] . "&" .
        $rst_pro[ord_mp10] . "&" .
        $rst_pro[ord_mp11] . "&" .
        $rst_pro[ord_mp12] . "&" .
        $rst_pro[ord_mp13] . "&" .
        $rst_pro[ord_mp14] . "&" .
        $rst_pro[ord_mp15] . "&" .
        $rst_pro[ord_mp16] . "&" .
        $rst_pro[ord_mp17] . "&" .
        $rst_pro[ord_mp18] . "&" .
        $rst_pro[ord_mf1] . "&" .
        $rst_pro[ord_mf2] . "&" .
        $rst_pro[ord_mf3] . "&" .
        $rst_pro[ord_mf4] . "&" .
        $rst_pro[ord_mf5] . "&" .
        $rst_pro[ord_mf6] . "&" .
        $rst_pro[ord_mf7] . "&" .
        $rst_pro[ord_mf8] . "&" .
        $rst_pro[ord_mf9] . "&" .
        $rst_pro[ord_mf10] . "&" .
        $rst_pro[ord_mf11] . "&" .
        $rst_pro[ord_mf12] . "&" .
        $rst_pro[ord_mf13] . "&" .
        $rst_pro[ord_mf14] . "&" .
        $rst_pro[ord_mf15] . "&" .
        $rst_pro[ord_mf16] . "&" .
        $rst_pro[ord_mf17] . "&" .
        $rst_pro[ord_mf18] . "&" .
        $rst_pro[ord_por_tornillo1] . "&" .
        $rst_pro[ord_por_tornillo2] . "&" .
        $rst_pro[ord_por_tornillo3] . "&" .
        $rst_pro[ord_patch1] . "&" .
        $rst_pro[ord_patch2] . "&" .
        $rst_pro[ord_patch3];
        break;
}
?>
