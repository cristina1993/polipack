<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_factura.php';
$Fact= new Clase_factura();
if (isset($_POST)) {
    $x=$_POST[x];
    $y=$_POST[y];
    $w=$_POST[w];
    $h=$_POST[h];
    $p=$_POST[p];
    
    if(!$Fact->set_seteo_factura(array($x,$y,$w,$h,$p))){
        echo pg_last_error();
    }
    
    
}else{
    $x=0;
    $y=0;
    $w=50;
    $h=50;
    $p=100;
    
}
?>
<script>
    $(function () {

        $('#x,#y,#w,#h,#p').change(function () {
            $('#frm').submit();
        });

    })
</script>
<form method="POST" name="frm" id="frm">
    X:<input type="number" id="x" name="x" style="width:50px;" value="<?php echo $x?>"  />
    Y:<input type="number" id="y" name="y" style="width:50px;" value="<?php echo $y?>"  />
    W:<input type="number" id="w" name="w" style="width:50px;"  maxlength="3" value="<?php echo $w?>"/>
    H:<input type="number" id="h" name="h" style="width:50px;"  maxlength="3" value="<?php echo $h?>"/>
    %<input type="number"  id="p" name="p" style="width:50px;"  maxlength="2" value="<?php echo $p?>"/>
</form>
<iframe src="pdf_fact.php" width="90%" height="90%" >




