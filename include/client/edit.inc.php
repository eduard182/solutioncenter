<?php

if(!defined('OSTCLIENTINC') || !$thisclient || !$ticket || !$ticket->checkUserAccess($thisclient)) die('Acceso Denegado!');

?>

<h1>
    <?php echo sprintf(__('Editing Ticket #%s'), $ticket->getNumber()); ?>
</h1>

<form action="tickets.php?r=yes" method="post" id="form_edit">
    <?php echo csrf_token(); ?>
    <input type="hidden" name="a" value="edit"/>
    <input type="hidden" name="id" value="<?php echo Format::htmlchars($_REQUEST['id']); ?>"/>
<table width="800">
    <tbody id="dynamic-form">
    <?php if ($forms)
        foreach ($forms as $form) {
            $form->render(false);
    } ?>
    </tbody>
</table>
<hr>
<p style="text-align: center;">
   <input type="submit" value="Update"/>
    <!-- <input type="button" value="Update" id="btn_update"/> -->
    <input type="reset" value="Reset"/>
    <input type="button" value="Cancel" onclick="javascript:
        window.location.href='index.php';"/>
</p>
<?php
//// AGREGADO POR FRANCISCO COLMENAREZ ---------------------------
    $analisises=0; $realizaciones=0; $testinges=0; $totales=0;
    $OC=$ticket->gethhOC($ticket->getId()); 
    if (count($OC)>0){
        $analisises=$OC["analisises"];
        $realizaciones=$OC["realizaciones"];
        $testinges=$OC["testinges"];
        $totales=$OC["totales"];
    }
/// FIN DEL AGREGADO ----------------------------------------------
?>
    <!-- CAMBIO EFECTUADO POR FCOLMENAREZ -->
    <input type="hidden" id="act_analisises" value="" />
    <input type="hidden" id="act_realizaciones" value=""/>
    <input type="hidden" id="act_testinges" value=""/>
    <input type="hidden" id="act_totales" value=""/>

    <input type="hidden" id="analisises" value="<?php echo $analisises;?>"/>
    <input type="hidden" id="realizaciones" value="<?php echo $realizaciones;?>"/>
    <input type="hidden" id="testinges" value="<?php echo $testinges;?>"/>
    <input type="hidden" id="totales" value="<?php echo $totales;?>"/>
    <!-- FIN DEL CAMBIO -->
</form>
<script type="text/javascript">
//// AGREGADO POR FRANCISCO COLMENAREZ ---------------------------
$(function() {
    //// AGREGADO POR FRANCISCO COLMENAREZ ---------------------------
    /////-- DECLARACION DE VARIABLES  -----------------
    var total_h=0;
    var anali_h=0;
    var reali_h=0;
    var test_h=0;
    ////--------------------------------------------------------------
    $("#act_analisises").val($("#_holanalisises" ).val());
    $("#act_realizaciones").val($("#_holrealizacion" ).val());
    $("#act_testinges").val($("#_holtesting" ).val());

    $('#_totalholgura').attr('readonly', true);


    $("#_holanalisises" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });

    $("#_holrealizacion" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });

    $("#_holtesting" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });
    
    $("#_holanalisises").keyup(function () {

        if($("#_holanalisises").val() < parseInt($("#analisises").val()) ){ alert("No puede colocar Horas Meror a la OC para este Item!!!");  }
        
        if($("#_holanalisises").val()>0){ anali_h=parseInt($("#_holanalisises").val()); }
        if($("#_holrealizacion").val()>0){ reali_h=parseInt($("#_holrealizacion").val()); }
        if($("#_holtesting").val()>0){ test_h=parseInt($("#_holtesting").val()); }
        
        total_h=anali_h+reali_h+test_h;
        $('#_totalholgura').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0;  
       
    });

    $("#_holrealizacion").keyup(function () {
        
        if($("#_holrealizacion").val() < parseInt($("#realizaciones").val()) ){ alert("No puede colocar Horas Meror a la OC para este Item!!!"); }

        if($("#_holanalisises").val()>0){ anali_h=parseInt($("#_holanalisises").val()); }
        if($("#_holrealizacion").val()>0){ reali_h=parseInt($("#_holrealizacion").val()); }
        if($("#_holtesting").val()>0){ test_h=parseInt($("#_holtesting").val()); }
        
        total_h=anali_h+reali_h+test_h;
        $('#_totalholgura').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0;  
        
       
    });

     $("#_holtesting").keyup(function () {
       
        if($("#_holtesting").val() < parseInt($("#testinges").val()) ){ alert("No puede colocar Horas Meror a la OC para este Item!!!");  }

        if($("#_holanalisises").val()>0){ anali_h=parseInt($("#_holanalisises").val()); }
        if($("#_holrealizacion").val()>0){ reali_h=parseInt($("#_holrealizacion").val()); }
        if($("#_holtesting").val()>0){ test_h=parseInt($("#_holtesting").val()); }
        
        total_h=anali_h+reali_h+test_h;
        $('#_totalholgura').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0; 
        
       
    });

    $("#btn_update").click( function(e){ 
       e.preventDefault();
        $( "#form_edit" ).submit();

        //window.location.href='tickets.php?id=<?php //echo $ticket->getId();?>';
        window.location.reload();

    });

    //// FIN DE AGREGADO FRANCISCO COLMENAREZ ------------------

});
</script>
