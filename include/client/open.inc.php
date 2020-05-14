<?php
if(!defined('OSTCLIENTINC')) die('Access Denied!');
$info=array();
if($thisclient && $thisclient->isValid()) {
    $info=array('name'=>$thisclient->getName(),
                'email'=>$thisclient->getEmail(),
                'phone'=>$thisclient->getPhoneNumber());
}

$info=($_POST && $errors)?Format::htmlchars($_POST):$info;

$form = null;
if (!$info['topicId'])
    $info['topicId'] = $cfg->getDefaultTopicId();

$forms = array();
if ($info['topicId'] && ($topic=Topic::lookup($info['topicId']))) {
    foreach ($topic->getForms() as $F) {
        if (!$F->hasAnyVisibleFields())
            continue;
        if ($_POST) {
            $F = $F->instanciate();
            $F->isValidForClient();
        }
        $forms[] = $F;
    }
}

?>
 <!-- <h1><?php echo __('Open a New Ticket');?></h1> -->
<p><?php echo __('Please fill in the form below to open a new ticket.');?></p>
<form id="ticketForm" method="post" action="open.php" enctype="multipart/form-data">
  <?php csrf_token(); ?>
  <input type="hidden" name="a" value="open">
  <table width="800" cellpadding="1" cellspacing="0" border="0">
    <tbody>
<?php
        if (!$thisclient) {
            $uform = UserForm::getUserForm()->getForm($_POST);
            if ($_POST) $uform->isValid();
            $uform->render(false);
        }
        else { ?>
            <tr><td colspan="2"><hr /></td></tr>
        <tr><td><?php echo __('Email'); ?></td><td><?php echo $thisclient->getEmail(); ?></td></tr>
        <tr><td><?php echo __('Client'); ?></td><td><?php echo $thisclient->getName(); ?></td></tr>
        <?php } ?>
    </tbody>
    <tbody>
    <tr><td colspan="2"><hr />
        <div class="form-header" style="margin-bottom:0em">
        <b><?php echo __('Help Topic'); ?></b>
        </div>
    </td></tr>
    <tr>
        <td colspan="2">
            <select id="topicId" name="topicId" onchange="javascript:
                    var data = $(':input[name]', '#dynamic-form').serialize();
                    $.ajax(
                      'ajax.php/form/help-topic/' + this.value,
                      {
                        data: data,
                        dataType: 'json',
                        success: function(json) {
                          $('#dynamic-form').empty().append(json.html);
                          $(document.head).append(json.media);
                        }
                      });">
                <option value="" selected="selected">&mdash; <?php echo __('Select a Help Topic');?> &mdash;</option>
                <?php
                if($topics=Topic::getPublicHelpTopics()) {
                    foreach($topics as $id =>$name) {
                        echo sprintf('<option value="%d" %s>%s</option>',
                                $id, ($info['topicId']==$id)?'selected="selected"':'', $name);
                    }
                } else { ?>
                    <option value="0" ><?php echo __('General Inquiry');?></option>
                <?php
                } ?>
            </select>
            <font class="error">*&nbsp;<?php echo $errors['topicId']; ?></font>
        </td>
    </tr>
    </tbody>
    <tbody id="dynamic-form">
        <?php foreach ($forms as $form) {
            include(CLIENTINC_DIR . 'templates/dynamic-form.tmpl.php');
        } ?>
    </tbody>
    <tbody>
    <?php
    if($cfg && $cfg->isCaptchaEnabled() && (!$thisclient || !$thisclient->isValid())) {
        if($_POST && $errors && !$errors['captcha'])
            $errors['captcha']=__('Please re-enter the text again');
        ?>
    <tr class="captchaRow">
        <td class="required"><?php echo __('CAPTCHA Text');?></td>
	</tr>
	<tr>
        <td colspan="2">
            <span class="captcha"><img src="captcha.php" border="0" align="left"></span>
            &nbsp;&nbsp;
            <input id="captcha" type="text" name="captcha" size="6" autocomplete="off">
            <em><?php echo __('Enter the text shown on the image.');?></em>
            <font class="error">&nbsp;<?php echo $errors['captcha']; ?></font>
        </td>
    </tr>
    <?php
    } ?>
    <tr><td colspan=2>&nbsp;</td></tr>
    </tbody>
  </table>
<hr/>
  <p class="buttons" style="text-align:center;">
        <input type="submit" value="<?php echo __('Create Ticket');?>">
        <input type="reset" name="reset" value="<?php echo __('Reset');?>">
        <input type="button" name="cancel" value="<?php echo __('Cancel'); ?>" onclick="javascript:
            $('.richtext').each(function() {
                var redactor = $(this).data('redactor');
                if (redactor && redactor.opts.draftDelete)
                    redactor.deleteDraft();
            });
            window.location.href='index.php';">
  </p>
</form>
<script type="text/javascript">
$(function() {
    //// AGREGADO POR FRANCISCO COLMENAREZ ---------------------------
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
    $('#_totales').val(0);
    $('#_totales').attr('readonly', true);
    $('#_totalholgura').val(0);
    $('#_totalholgura').attr('readonly', true);


    $("#_analisises" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });

    $("#_realizaciones" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });

    $("#_testinges" ).keypress(function( e ) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla <=9) return true;
            patron = /\d/; // Solo acepta n�meros
            te = String.fromCharCode(tecla);
            return patron.test(te);
    });

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
    
    $("#_analisises").keyup(function () {
        
        if($("#_analisises").val()>0){ anali=parseInt($("#_analisises").val()); }
        if($("#_realizaciones").val()>0){ reali=parseInt($("#_realizaciones").val()); }
        if($("#_testinges").val()>0){ test=parseInt($("#_testinges").val()); }
        
        total=anali+reali+test;
        $('#_totales').val(total);  
        total=0;anali=0;reali=0;test=0;  
       
    });

    $("#_realizaciones").keyup(function () {
        
        if($("#_analisises").val()>0){ anali=parseInt($("#_analisises").val()); }
        if($("#_realizaciones").val()>0){ reali=parseInt($("#_realizaciones").val()); }
        if($("#_testinges").val()>0){ test=parseInt($("#_testinges").val()); }
        
        total=anali+reali+test;
        $('#_totales').val(total);  
        total=0;anali=0;reali=0;test=0;  
        
       
    });

     $("#_testinges").keyup(function () {
       
        if($("#_analisises").val()>0){ anali=parseInt($("#_analisises").val()); }
        if($("#_realizaciones").val()>0){ reali=parseInt($("#_realizaciones").val()); }
        if($("#_testinges").val()>0){ test=parseInt($("#_testinges").val()); }
        
        total=anali+reali+test;
        $('#_totales').val(total);  
        total=0;anali=0;reali=0;test=0;  
        
       
    });

    $("#_holanalisises").keyup(function () {
        
        if($("#_holanalisises").val()>0){ anali_h=parseInt($("#_holanalisises").val()); }
        if($("#_holrealizacion").val()>0){ reali_h=parseInt($("#_holrealizacion").val()); }
        if($("#_holtesting").val()>0){ test_h=parseInt($("#_holtesting").val()); }
        
        total_h=anali_h+reali_h+test_h;
        $('#_totalholgura').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0;  
       
    });

    $("#_holrealizacion").keyup(function () {
        
        if($("#_holanalisises").val()>0){ anali_h=parseInt($("#_holanalisises").val()); }
        if($("#_holrealizacion").val()>0){ reali_h=parseInt($("#_holrealizacion").val()); }
        if($("#_holtesting").val()>0){ test_h=parseInt($("#_holtesting").val()); }
        
        total_h=anali_h+reali_h+test_h;
        $('#_totalholgura').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0;  
        
       
    });

     $("#_holtesting").keyup(function () {
       
        if($("#_holanalisises").val()>0){ anali_h=parseInt($("#_holanalisises").val()); }
        if($("#_holrealizacion").val()>0){ reali_h=parseInt($("#_holrealizacion").val()); }
        if($("#_holtesting").val()>0){ test_h=parseInt($("#_holtesting").val()); }
        
        total_h=anali_h+reali_h+test_h;
        $('#_totalholgura').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0; 
        
       
    });
    //// FIN DE AGREGADO FRANCISCO COLMENAREZ ------------------
});
</script>