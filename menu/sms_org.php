<?php
include_once '../Clases/clsClase_multimedia.php';
$Mlt = new Multimedia();
$rst_sms = pg_fetch_array($Mlt->lista_sms_mult());
?>
<style>
    #cont_sms{
        position:absolute;
        width:auto;
        /*        height:70px;
                overflow:auto; */
    }
    #sms_sms{
        font-family:Arial, Helvetica, sans-serif; 
        padding-right:10px;
        padding-left:10px;
        margin-right:10px; 
    }
    #close{
        margin-left:7px; 
        margin-right: -8px; 
        cursor:pointer; 
        float:right; 
        font-size: 20px;
        border:solid 1px brown; 
        text-align:center;
        padding:0px 7px;
        border-radius:5px;
        background:wheat;
        color:brown; 
        margin-left:2px; 
        z-index:9999; 
    }
    #close:hover{
        color:brown; 
        background:#8c3310;
        color:white; 
    }
    textarea {
        color: #D8000C;
        background-color: #f8f8f8;
        font-size:18px; 
        border:none;	
        border-radius:15px;
        resize: none;
        box-shadow:5px 5px 5px #8c3310; 
    }
</style>
<script>
    function close_sms() {
        cont_sms.style.visibility = 'hidden';
    }
</script>
<!DOCTYPE html>
<html  >
    <head><meta charset="UTF-8"></head>
    <?php
    if (strlen(trim($rst_sms[sms_sms])) > 0) {
        ?>
        <div id="cont_sms">
            <div id="sms_sms" title="<?php echo $rst_sms[sms_user] . ' ' . $rst_sms[sms_fecha] ?>" >
                <font id="close" onclick="close_sms()" title="Cerrar Mensaje" >&#X00d7;</font>
                <textarea  rows="10" cols="80" readonly >            
                    <?php echo $rst_sms[sms_sms] ?>
                </textarea>
            </div>
        </div>
        <?php
    }
    ?>
</html>
