<?php

class Clase_cierres_caja_masivo {

    function Conectar() {
        return pg_connect('host=localhost'
                . ' port=5432 '
                . ' dbname=noperti'
                . ' user=postgres'
                . ' password=SuremandaS495');
    }

    function lista_secuencial_cierre() {
        if ($this->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_cierres ORDER BY cie_secuencial DESC LIMIT 1");
        }
    }

    function lista_punto_emision() {
        if ($this->Conectar() == true) {
            return pg_query("SELECT * FROM emisor ORDER BY cod_punto_emision");
        }
    }

    function delete_cierre($f) {
        if ($this->Conectar() == true) {
            return pg_query("DELETE FROM erp_cierres WHERE cie_fecha='$f'");
        }
    }

    function delete_cierre_bodega($f, $pto) {
        if ($this->Conectar() == true) {
            return pg_query("DELETE FROM erp_cierres WHERE cie_fecha='$f' and cie_punto_emision=$pto");
        }
    }

    function delete_asiento($sec) {
        if ($this->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables WHERE con_documento='$sec' and con_concepto='CIERRE CAJA'");
        }
    }

    function insert_cierre($data) {
        if ($this->Conectar() == true) {
            return pg_query("INSERT INTO erp_cierres(
                cie_secuencial,
                cie_fecha,
                cie_hora,
                cie_usuario,
                cie_punto_emision,
                cie_fac_emitidas,
                cie_productos_facturados,
                cie_subtotal,
                cie_descuento,
                cie_iva,
                cie_total_facturas,
                cie_total_notas_credito,
                cie_total_tarjeta_credito,
                cie_total_tarjeta_debito,
                cie_total_cheque,
                cie_total_efectivo,
                cie_total_certificados,
                cie_total_bonos,
                cie_total_retencion,
                cie_total_not_credito
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
                                        '$data[9]',
                                            '$data[10]',
                                                '$data[11]',
                                                    '$data[12]',
                                                        '$data[13]',
                                                            '$data[14]',
                                                                '$data[15]',
                                                                    '$data[16]',
                                                                        '$data[17]',
                                                                            '$data[18]','$data[19]')");
        }
    }

    function upd_cierre_caja($data, $id) {
        if ($this->Conectar() == true) {
            return pg_query("UPDATE erp_cierres SET
            cie_secuencial='$data[0]',
            cie_fecha='$data[1]',
            cie_hora='$data[2]',
            cie_usuario='$data[3]',
            cie_punto_emision='$data[4]',
            cie_fac_emitidas='$data[5]',
            cie_productos_facturados='$data[6]',
            cie_subtotal='$data[7]',
            cie_descuento='$data[8]',
            cie_iva='$data[9]',
            cie_total_facturas='$data[10]',
            cie_total_notas_credito='$data[11]',
            cie_total_tarjeta_credito='$data[12]',
            cie_total_tarjeta_debito='$data[13]',
            cie_total_cheque='$data[14]',
            cie_total_efectivo='$data[15]',
            cie_total_certificados='$data[16]',
            cie_total_bonos='$data[17]',
            cie_total_retencion='$data[18]',
            cie_total_not_credito='$data[19]'
                
            WHERE cie_id='$id'");
        }
    }

    function lista_fechaemi_factura($f, $pto, $vend) {
        if ($this->Conectar() == true) {
            return pg_query("select count(*) as nfac from erp_factura  where fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'");
        }
    }

    function lista_fechaemi_factura_bodega($f, $pto, $vend) {
        if ($this->Conectar() == true) {
            return pg_query("select count(*) as nfac from erp_factura  where fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'");
        }
    }

    function lista_total_subtotal($f, $pto, $vend) {
        if ($this->Conectar() == true) {
            return pg_query("SELECT sum(fac_subtotal12) as suma_subtotal, sum(fac_total_descuento) as suma_descuento, sum(fac_total_iva) as suma_iva, sum(fac_total_valor) as suma_total_valor
from erp_factura
where fac_fecha_emision ='$f' 
and emi_id=$pto
and vnd_id='$vend'");
        }
    }

    function lista_total_subtotal_bodega($f, $pto, $vend) {
        if ($this->Conectar() == true) {
            return pg_query("SELECT sum(subtotal12) as suma_subtotal, sum(total_descuento) as suma_descuento, sum(total_iva) as suma_iva, sum(total_valor) as suma_total_valor
from comprobantes 
where fecha_emision =$f
and tipo_comprobante=1
and cod_punto_emision =$pto
and upper(vendedor)='$vend'");
        }
    }

    function lista_total_notacredito($f, $pto, $vend) {
        if ($this->Conectar() == true) {
            return pg_query("select sum(nc.nrc_total_valor) as suma_total_valor_nc from erp_nota_credito nc
where nc.ncr_fecha_emision='$f'
and nc.emi_id=$pto
and exists (select * from erp_factura f where nc.fac_id=f.fac_id and f.vnd_id='$vend')");
        }
    }

    function lista_cantidad_productos($f, $pto, $vend) {
        if ($this->Conectar() == true) {
            return pg_query("select sum(dfc_cantidad)as suma_cantidad, sum(dfc_precio_total) as suma_nota_credito from erp_factura c,erp_det_factura dc 
where dc.fac_id= c.fac_id
and fac_fecha_emision='$f' 
and emi_id=$pto
and vnd_id='$vend' ");
        }
    }

    function lista_cantidad_productos_bodega($f, $pto, $vend) {
        if ($this->Conectar() == true) {
            return pg_query("select sum(cantidad)as suma_cantidad, sum(precio_total) as suma_nota_credito from comprobantes c,detalle_fact_notdeb_notcre dc 
where substr(dc.num_camprobante,1,3) || '-' ||  substr(dc.num_camprobante,4,3) || '-' ||  substr(dc.num_camprobante,7,9) = c.num_documento
and c.tipo_comprobante=1
and fecha_emision=$f 
and cod_punto_emision = $pto 
and upper(vendedor)='$vend' ");
        }
    }

    function lista_formas_pago($f, $pto, $vend) {
        if ($this->Conectar() == true) {
            return pg_query("select (select sum(pg.pag_cant) as  tarjeta_credito from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='1'
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  tarjeta_debito from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='2'
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  cheque from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='3'
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  efectivo from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='4'
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  certificados from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='5'
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  bonos from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='6'
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  retencion from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='7'
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'), 
(select sum(pg.pag_cant) as  nota_credito from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='8'
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend')");
        }
    }

    function lista_formas_pago_bodega($f, $pto, $vend) {
        if ($this->Conectar() == true) {
            return pg_query("select (select sum(pg.pag_cant) as  tarjeta_credito from comprobantes c, erp_pagos_factura pg
where pg.com_id=c.num_documento
and c.tipo_comprobante=1
and pg.pag_forma='1'
and c.fecha_emision='$f' and cod_punto_emision = $pto  and upper(vendedor)='$vend'),
(select sum(pg.pag_cant) as  tarjeta_debito from comprobantes c, erp_pagos_factura pg
where pg.com_id=c.num_documento
and c.tipo_comprobante=1
and pg.pag_forma='2'
and c.fecha_emision='$f' and cod_punto_emision = $pto and upper(vendedor)='$vend'),
(select sum(pg.pag_cant) as  cheque from comprobantes c, erp_pagos_factura pg
where pg.com_id=c.num_documento
and c.tipo_comprobante=1
and pg.pag_forma='3'
and c.fecha_emision='$f' and cod_punto_emision = $pto and upper(vendedor)='$vend'),
(select sum(pg.pag_cant) as  efectivo from comprobantes c, erp_pagos_factura pg
where pg.com_id=c.num_documento
and c.tipo_comprobante=1
and pg.pag_forma='4'
and c.fecha_emision='$f' and cod_punto_emision = $pto and upper(vendedor)='$vend'),
(select sum(pg.pag_cant) as  certificados from comprobantes c, erp_pagos_factura pg
where pg.com_id=c.num_documento
and c.tipo_comprobante=1
and pg.pag_forma='5'
and c.fecha_emision='$f' and cod_punto_emision = $pto and upper(vendedor)='$vend'),
(select sum(pg.pag_cant) as  bonos from comprobantes c, erp_pagos_factura pg
where pg.com_id=c.num_documento
and c.tipo_comprobante=1
and pg.pag_forma='6'
and c.fecha_emision='$f' and cod_punto_emision = $pto and upper(vendedor)='$vend'),
(select sum(pg.pag_cant) as  retencion from comprobantes c, erp_pagos_factura pg
where pg.com_id=c.num_documento
and c.tipo_comprobante=1
and pg.pag_forma='7'
and c.fecha_emision='$f' and cod_punto_emision = $pto and upper(vendedor)='$vend'), 
(select sum(pg.pag_cant) as  nota_credito from comprobantes c, erp_pagos_factura pg
where pg.com_id=c.num_documento
and c.tipo_comprobante=1
and pg.pag_forma='8'
and c.fecha_emision='$f' and cod_punto_emision = '$pto' and upper(vendedor)='$vend')");
        }
    }

    function lista_bodega($cod) {
        if ($this->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cierres c, emisor e where e.cod_punto_emision = c.cie_punto_emision and e.cod_punto_emision='$cod'");
        }
    }

    function lista_un_cierre_punto_fecha($pto, $fecha) {
        if ($this->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cierres where cie_punto_emision=$pto and cie_fecha='$fecha'");
        }
    }

    function lista_cierres_caja($f, $pto) {
        if ($this->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_cierres where cie_fecha='$f' and cie_punto_emision=$pto");
        }
    }

    ///****************nuevo cierre************
    function lista_facturas_vendedor($f) {
        if ($this->Conectar() == true) {
            return pg_query("select vnd_id as vendedor, emi_id from erp_factura
where fac_fecha_emision='$f' 
and emi_id<>1 
and emi_id<>10 
and vnd_id!='0' group by vnd_id, emi_id
UNION
select f.vnd_id as vendedor, f.emi_id from erp_factura f
where f.emi_id<>1 
and f.emi_id<>10 
and exists (select * from erp_nota_credito nc  where nc.fac_id=f.fac_id and nc.ncr_fecha_emision='$f')");
        }
    }

    function lista_facturas_vendedor_bodega($f, $pto) {
        if ($this->Conectar() == true) {
            return pg_query("select upper(vendedor) as vendedor, cod_punto_emision from comprobantes 
where fecha_emision=$f 
and cod_punto_emision<>1 
and cod_punto_emision<>10 
and tipo_comprobante=1  
and cod_punto_emision=$pto 
and vendedor!='' group by upper(vendedor), cod_punto_emision
UNION
select upper(f.vendedor) as vendedor, f.cod_punto_emision from comprobantes f
where f.cod_punto_emision<>1 
and f.cod_punto_emision<>10 
and f.tipo_comprobante=1 
and exists (select * from comprobantes nc  where nc.num_factura_modifica=replace(f.num_documento,'-','') and nc.fecha_emision=$f and tipo_comprobante=4 and cod_punto_emision=$pto)");
        }
    }

    function lista_ultimo_secuencial($pto) {
        if ($this->Conectar() == true) {
            return pg_query("select * from erp_cierres where cie_punto_emision=$pto order by cie_secuencial desc limit 1");
        }
    }

    function lista_cierrres($fec, $vend, $pto) {
        if ($this->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cierres where cie_fecha='$fec' and cie_usuario='$vend' and cie_punto_emision='$pto'");
        }
    }

    function insert_asientos($data) {
        if ($this->Conectar() == true) {
            return pg_query("INSERT INTO erp_asientos_contables(
                con_asiento,
                con_concepto,
                con_documento,
                con_fecha_emision,
                con_concepto_debe,
                con_concepto_haber,
                con_valor_debe,
                con_valor_haber,
                con_estado
            )
    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]',0)");
        }
    }

    function ultimo_asiento() {
        if ($this->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    function lista_cierre_vendedor_emisor($fecha,$vnd,$emi) {
        if ($this->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cierres where cie_fecha='$fecha' and cie_punto_emision=$emi and cie_usuario='$vnd'");
        }
    }

    function siguiente_asiento() {
        if ($this->Conectar() == true) {
            $rst = pg_fetch_array($this->ultimo_asiento());
            if (!empty($rst)) {
                $sec = (substr($rst[con_asiento], -10) + 1);
                $n_sec = substr($rst[con_asiento], 0, (12 - strlen($sec))) . $sec;
            } else {
                $n_sec = 'AS0000000001';
            }
            return $n_sec;
        }
    }

}

set_time_limit(0);
date_default_timezone_set('America/Guayaquil');
$Clase_cierres_caja_masivo = new Clase_cierres_caja_masivo();
$op = 0;
$fecha = date('Y-m-d', strtotime('-1 day'));
$user = 'Sistema';
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$bodega = $_REQUEST[bod];

$sms = 0;
$f = $fecha;
$cns1 = $Clase_cierres_caja_masivo->lista_facturas_vendedor($f);

        while ($rst = pg_fetch_array($cns1)) {
            $rst_cierre_hecho = pg_fetch_array($Clase_cierres_caja_masivo->lista_cierre_vendedor_emisor($f, $rst[vendedor], $rst[emi_id]));
            if (empty($rst_cierre_hecho)) {
            $rst_sec = pg_fetch_array($Clase_cierres_caja_masivo->lista_ultimo_secuencial($rst[emi_id]));
                if ($rst[emi_id] >= 10) {
                    $ems = $rst[emi_id];
                } else {
                    $ems = '0' . $rst[emi_id];
                }
                $sec = (substr($rst_sec[cie_secuencial], -4) + 1);
                if ($sec >= 0 && $sec < 10) {
                    $txt = '000';
                } else if ($sec >= 10 && $sec < 100) {
                    $txt = '00';
                } else if ($sec >= 100 && $sec < 1000) {
                    $txt = '0';
                } else if ($sec >= 1000 && $sec < 10000) {
                    $txt = '';
                }
                $sec1 = $ems . $txt . $sec;

                if (!$Clase_cierres_caja_masivo->delete_asiento($sec1) == FALSE) {
                   echo $sms = pg_last_error();
                }

                $rst_nfct = pg_fetch_array($Clase_cierres_caja_masivo->lista_fechaemi_factura($f, $rst[emi_id], $rst[vendedor]));
                $rst_npf = pg_fetch_array($Clase_cierres_caja_masivo->lista_cantidad_productos($f, $rst[emi_id], $rst[vendedor]));
                $rst_sbt = pg_fetch_array($Clase_cierres_caja_masivo->lista_total_subtotal($f, $rst[emi_id], $rst[vendedor]));
                $rst_tnc = pg_fetch_array($Clase_cierres_caja_masivo->lista_total_notacredito($f, $rst[emi_id], $rst[vendedor]));
                $rst_fp = pg_fetch_array($Clase_cierres_caja_masivo->lista_formas_pago($f, $rst[emi_id], $rst[vendedor]));
                $suma_subt = $rst_sbt[suma_subtotal] + $rst_sbt[suma_descuento];

                $data = array($sec1,
                    $fecha,
                    date('H:i'),
                    $rst[vendedor],
                    $rst[emi_id],
                    $rst_nfct[nfac],
                    str_replace(',', '', $rst_npf[suma_cantidad]),
                    str_replace(',', '', number_format($suma_subt, 4)),
                    str_replace(',', '', number_format($rst_sbt[suma_descuento], 4)),
                    str_replace(',', '', number_format($rst_sbt[suma_iva], 4)),
                    str_replace(',', '', number_format($rst_sbt[suma_total_valor], 4)),
                    str_replace(',', '', number_format($rst_tnc[suma_total_valor_nc], 4)),
                    str_replace(',', '', number_format($rst_fp[tarjeta_credito], 4)),
                    str_replace(',', '', number_format($rst_fp[tarjeta_debito], 4)),
                    str_replace(',', '', number_format($rst_fp[cheque], 4)),
                    str_replace(',', '', number_format($rst_fp[efectivo], 4)),
                    str_replace(',', '', number_format($rst_fp[certificados], 4)),
                    str_replace(',', '', number_format($rst_fp[bonos], 4)),
                    str_replace(',', '', number_format($rst_fp[retencion], 4)),
                    str_replace(',', '', number_format($rst_fp[nota_credito], 4))
                );

                if ($Clase_cierres_caja_masivo->insert_cierre($data) == false) {
                    $sms = pg_last_error();
                } 
//                else {
//                    if ($rst[emi_id] == 2) {
//                        $debe1 = '1.01.02.05.101';
//                        $debe2 = '1.01.01.02.002';
//                        $haber = '1.01.02.05.002';
//                    } else if ($rst[emi_id] == 3) {
//                        $debe1 = '1.01.02.05.102';
//                        $debe2 = '1.01.01.02.003';
//                        $haber = '1.01.02.05.003';
//                    } else if ($rst[emi_id] == 4) {
//                        $debe1 = '1.01.02.05.103';
//                        $debe2 = '1.01.01.02.004';
//                        $haber = '1.01.02.05.004';
//                    } else if ($rst[emi_id] == 5) {
//                        $debe1 = '1.01.02.05.104';
//                        $debe2 = '1.01.01.02.005';
//                        $haber = '1.01.02.05.005';
//                    } else if ($rst[emi_id] == 6) {
//                        $debe1 = '1.01.02.05.105';
//                        $debe2 = '1.01.01.02.006';
//                        $haber = '1.01.02.05.006';
//                    } else if ($rst[emi_id] == 7) {
//                        $debe1 = '1.01.02.05.106';
//                        $debe2 = '1.01.01.02.002';
//                        $haber = '1.01.02.05.002';
//                    } else if ($rst[emi_id] == 8) {
//                        $debe1 = '1.01.02.05.107';
//                        $debe2 = '1.01.01.02.007';
//                        $haber = '1.01.02.05.007';
//                    } else if ($rst[emi_id] == 9) {
//                        $debe1 = '1.01.02.05.108';
//                        $debe2 = '1.01.01.02.009';
//                        $haber = '1.01.02.05.009';
//                    }
//
//                    $asiento = $Clase_cierres_caja_masivo->siguiente_asiento();
//                    $tarjeta = $rst_fp[tarjeta_credito] + $rst_fp[tarjeta_debito];
//
//                    if ($tarjeta != 0) {
//                        $dat0 = Array($asiento,
//                            'CIERRE CAJA',
//                            $sec1,
//                            date('Y-m-d'),
//                            $debe1,
//                            $haber,
//                            str_replace(',', '', $tarjeta),
//                            str_replace(',', '', $rst_sbt[suma_total_valor])
//                        );
//                        $d = 1;
//                    }
//                    if ($d == 0) {
//                        $haber2 = $haber;
//                        $total = $rst_sbt[suma_total_valor];
//                    } else {
//                        $haber2 = '';
//                        $total = 0;
//                    }
//
//                    if ($rst_fp[efectivo] != 0) {
//                        $dat1 = Array($asiento,
//                            'CIERRE CAJA',
//                            $sec1,
//                            date('Y-m-d'),
//                            $debe2,
//                            $haber2,
//                            str_replace(',', '', $rst_fp[efectivo]),
//                            str_replace(',', '', $total)
//                        );
//                        $d = 1;
//                    }
//                    if ($d == 0) {
//                        $haber3 = $haber;
//                        $total1 = $rst_sbt[suma_total_valor];
//                    } else {
//                        $haber3 = '';
//                        $total1 = 0;
//                    }
//
//                    if ($rst_fp[cheque] != 0) {
//                        $dat2 = Array($asiento,
//                            'CIERRE CAJA',
//                            $sec1,
//                            date('Y-m-d'),
//                            '1.01.01.02.021',
//                            $haber3,
//                            str_replace(',', '', $rst_fp[cheque]),
//                            str_replace(',', '', $total1)
//                        );
//                    }
//
//                    $array = array($dat0, $dat1, $dat2);
//                    $j = 0;
//                    while ($j <= count($array)) {
//                        if (!empty($array[$j])) {
//                            if ($Clase_cierres_caja_masivo->insert_asientos($array[$j]) == false) {
//                               echo $sms = pg_last_error();
//                            }
//                        }
//                        $j++;
//                    }
//                }
            }
        }



?>