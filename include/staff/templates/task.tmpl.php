<?php

if (!$info['title'])
    $info['title'] = __('Nueva Tarea');

$namespace = 'task.add';
if ($ticket)
    $namespace = sprintf('ticket.%d.task', $ticket->getId());

///// MODIFICADO POR FCOLMENAREZ -----------------------------------------------------
$OC=array();
$valido=true;
$analisises=""; $realizaciones=""; $testinges=""; $totales=""; $dispo="";
if ($ticket){
    $OC = $ticket->getValidaHHTask($ticket->getId());
    
    if($OC["analisises"]==-1){ $valido=false; } else{ $analisises=$OC["analisises"]; }
    if($OC["realizaciones"]==-1){ $valido=false; } else{ $realizaciones=$OC["realizaciones"]; }
    if($OC["testinges"]==-1){ $valido=false; } else{ $testinges=$OC["testinges"]; }
    if($OC["totales"]==-1){ $valido=false; } else{ $totales=$OC["totales"]; }

    $dispo="An&aacute;lisis:".$analisises." Realizaci&oacute;n:".$realizaciones." Testing:".$testinges;
   
}

if (!$valido){
    ?>
    <div id="task-form">
        <h3 class="drag-handle"><?php echo $info['title']; ?> <BR> No Permitido, Las Horas de la OC estan Copadas</h3>
        <a class="close" href="#"><i class="material-icons">highlight_off</i></a>
        <hr/>
    </div>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="button" name="cancel" class="close" value="<?php echo __('Cancel'); ?>">
        </span>
     </p>

    <?php die;
}
///-------------------------------------------------------------------------------------

?>
<div id="task-form">
<h3 class="drag-handle"><?php echo $info['title']; ?></h3>
<a class="close" href="#"><i class="material-icons">highlight_off</i></a>
<hr/>
<?php

if ($info['error']) {
    echo sprintf('<div id="msg_error"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path></svg></div><div id="alert-text">%s</div></div>', $info['error']);
} elseif ($info['warning']) {
    echo sprintf('<div id="msg_warning"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" /></svg></div><div id="alert-text">%s</div></div>', $info['warning']);
} elseif ($info['msg']) {
    echo sprintf('<div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text">%s</div></div>', $info['msg']);
} 

if ($ticket){
   echo '<div class="responsive-div ticket_info ticket-view">
            <h4 class="drag-handle">Horas Disponibles</h4>
            <label>'.$dispo.'</label>
        </div>';
}

?>
<div id="new-task-form" style="display:block;">
<form method="post" class="org" action="<?php echo $info['action'] ?: '#tasks/add'; ?>">
    <?php
        $form = $form ?: TaskForm::getInstance();
        echo $form->getForm()->asTable(' ', array('draft-namespace' => $namespace));

        $iform = $iform ?: TaskForm::getInternalForm();
        echo $iform->asTable(__("Task Visibility & Assignment"));
?>
    <hr>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="reset" value="<?php echo __('Reset'); ?>">
            <input type="button" name="cancel" class="close"
                value="<?php echo __('Cancel'); ?>">
        </span>
        <span class="buttons pull-right">
            <input type="submit" value="<?php echo __('Crear Tarea'); ?>">
        </span>
     </p>
     <!-- CAMBIO EFECTUADO POR FCOLMENAREZ -->
     <input type="hidden" id="analisises" value="<?php echo $analisises;?>"/>
     <input type="hidden" id="realizaciones" value="<?php echo $realizaciones;?>"/>
     <input type="hidden" id="testinges" value="<?php echo $testinges;?>"/>
     <input type="hidden" id="totales" value="<?php echo $totales;?>"/>
      <!-- FIN DEL CAMBIO -->
     <br><br><br><br><br><br>
</form>
</div>
<div class="clear"></div>
</div>

<script type="text/javascript">
//// AGREGADO POR FRANCISCO COLMENAREZ ---------------------------
$(function() {
    /////-- DECLARACION DE VARIABLES  -----------------
    var total=0;
    var total_h=0;
    var anali=0;
    var reali=0;
    var test=0;
    var anali_h=0;
    var reali_h=0;
    var test_h=0;
    ////--------------------------------------------------------------
    $('#_t_estimacion').val(0);
    $('#_t_estimacion').attr('readonly', true);
   /* $('#_t_holtotal').val(0);
    $('#_t_holtotal').attr('readonly', true);*/

    $("#_t_analisis" ).val($("#analisises").val());
    $("#_t_desarrollo" ).val($("#realizaciones").val());
    $("#_t_testing" ).val($("#testinges").val());
    $("#_t_estimacion" ).val($("#totales").val());

    $("#_t_analisis" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });

    $("#_t_desarrollo" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });

    $("#_t_testing" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });

    /*$("#_t_holanalisis" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });

    $("#_t_holdesarrollo" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });

    $("#_t_holtesting" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });*/
    
    $("#_t_analisis").keyup(function () {

        if($("#_t_analisis").val() > parseInt($("#analisises").val()) ){ alert("No puede colocar Horas por Enciama de la OC para este Item!!!"); $("#_t_analisis" ).val($("#analisises").val()); }

        if($("#_t_analisis").val()>0){ anali=parseInt($("#_t_analisis").val()); $("#_t_analisis").val(parseInt(anali)); }
        if($("#_t_desarrollo").val()>0){ reali=parseInt($("#_t_desarrollo").val()); $("#_t_desarrollo").val(parseInt(reali)); }
        if($("#_t_testing").val()>0){ test=parseInt($("#_t_testing").val()); $("#_t_testing").val(parseInt(test)); }
        
        total=anali+reali+test;
        $('#_t_estimacion').val(total);  
        total=0;anali=0;reali=0;test=0;  
       
    });

    $("#_t_desarrollo").keyup(function () {

        if($("#_t_desarrollo").val() > parseInt($("#realizaciones").val()) ){ alert("No puede colocar Horas por Enciama de la OC para este Item!!!"); $("#_t_desarrollo" ).val($("#realizaciones").val()); }
        
        if($("#_t_analisis").val()>0){ anali=parseInt($("#_t_analisis").val()); $("#_t_analisis").val(parseInt(anali)); }
        if($("#_t_desarrollo").val()>0){ reali=parseInt($("#_t_desarrollo").val()); $("#_t_desarrollo").val(parseInt(reali)); }
        if($("#_t_testing").val()>0){ test=parseInt($("#_t_testing").val()); $("#_t_testing").val(parseInt(test)); }
        
        total=anali+reali+test;
        $('#_t_estimacion').val(total);  
        total=0;anali=0;reali=0;test=0;  
        
       
    });

     $("#_t_testing").keyup(function () {

        if($("#_t_testing").val() > parseInt($("#testinges").val()) ){ alert("No puede colocar Horas por Enciama de la OC para este Item!!!");  $("#_t_testing" ).val($("#testinges").val()); }
       
        if($("#_t_analisis").val()>0){ anali=parseInt($("#_t_analisis").val()); $("#_t_analisis").val(parseInt(anali)); }
        if($("#_t_desarrollo").val()>0){ reali=parseInt($("#_t_desarrollo").val()); $("#_t_desarrollo").val(parseInt(reali)); }
        if($("#_t_testing").val()>0){ test=parseInt($("#_t_testing").val()); $("#_t_testing").val(parseInt(test)); }
        
        total=anali+reali+test;
        $('#_t_estimacion').val(total);  
        total=0;anali=0;reali=0;test=0;  
        
       
    });

   /* $("#_t_holanalisis").keyup(function () {
        
        if($("#_t_holanalisis").val()>0){ anali_h=parseInt($("#_t_holanalisis").val()); }
        if($("#_t_holdesarrollo").val()>0){ reali_h=parseInt($("#_t_holdesarrollo").val()); }
        if($("#_t_holtesting").val()>0){ test_h=parseInt($("#_t_holtesting").val()); }
        
        total_h=anali_h+reali_h+test_h;
        $('#_t_holtotal').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0;  
       
    });

    $("#_t_holdesarrollo").keyup(function () {
        
        if($("#_t_holanalisis").val()>0){ anali_h=parseInt($("#_t_holanalisis").val()); }
        if($("#_t_holdesarrollo").val()>0){ reali_h=parseInt($("#_t_holdesarrollo").val()); }
        if($("#_t_holtesting").val()>0){ test_h=parseInt($("#_t_holtesting").val()); }
        
        total_h=anali_h+reali_h+test_h;
        $('#_t_holtotal').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0;  
        
       
    });

     $("#_t_holtesting").keyup(function () {
       
        if($("#_t_holanalisis").val()>0){ anali_h=parseInt($("#_t_holanalisis").val()); }
        if($("#_t_holdesarrollo").val()>0){ reali_h=parseInt($("#_t_holdesarrollo").val()); }
        if($("#_t_holtesting").val()>0){ test_h=parseInt($("#_t_holtesting").val()); }
        
        total_h=anali_h+reali_h+test_h;
        $('#_t_holtotal').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0; 
        
       
    });*/

});
  //// FIN DE AGREGADO FRANCISCO COLMENAREZ ------------------
</script>