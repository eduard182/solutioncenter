<?php
if (!defined('OSTSCPINC')
        || !$ticket
        || !($ticket->checkStaffPerm($thisstaff, TicketModel::PERM_EDIT)))
    die('Access Denied');

$info=Format::htmlchars(($errors && $_POST)?$_POST:$ticket->getUpdateInfo());
if ($_POST)
    // Reformat duedate to the display standard (but don't convert to local
    // timezone)
    $info['duedate'] = Format::date(strtotime($info['duedate']), false, false, 'UTC');
?>
<form action="tickets.php?id=<?php echo $ticket->getId(); ?>&a=edit" method="post" id="save"  enctype="multipart/form-data">
    <?php csrf_token(); ?>
    <input type="hidden" name="do" value="update">
    <input type="hidden" name="a" value="edit">
    <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
    <div style="margin-bottom:20px; padding-top:5px;">
        <div class="pull-left flush-left">
            <h2><?php echo sprintf(__('Update Ticket #%s'),$ticket->getNumber());?></h2>
        </div>
    </div>
    <table class="form_table" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <th colspan="2">
                    <em><strong><?php echo __('User Information'); ?></strong> <?php //echo __(''); ?></em>
                </th>
            </tr>
        <?php
    if(!$info['user_id'] || !($user = User::lookup($info['user_id'])))
        $user = $ticket->getUser();
    ?>
    <tr><td><?php echo __('User'); ?></td><td>
        <div id="client-info">
            <a href="#" onclick="javascript:
                $.userLookup('ajax.php/users/<?php echo $ticket->getOwnerId(); ?>/edit',
                        function (user) {
                            $('#client-name').text(user.name);
                            $('#client-email').text(user.email);
                        });
                return false;
                "><i class="material-icons">account_box</i>
            <span id="client-name"><?php echo Format::htmlchars($user->getName()); ?></span>
            <!-- &lt;<span id="client-email"><?php echo $user->getEmail(); ?></span>&gt; -->
            </a>
            <a class="inline action-button" style="overflow:inherit" href="#"
                onclick="javascript:
                    $.userLookup('ajax.php/tickets/<?php echo $ticket->getId(); ?>/change-user',
                            function(user) {
                                $('input#user_id').val(user.id);
                                $('#client-name').text(user.name);
                                $('#client-email').text('<'+user.email+'>');
                    });
                    return false;
                "><svg style="width:18px;height:18px;padding:0 2px 0 0;" viewBox="0 0 24 24"><path fill="#fff" d="M16,9C18.33,9 23,10.17 23,12.5V15H17V12.5C17,11 16.19,9.89 15.04,9.05L16,9M8,9C10.33,9 15,10.17 15,12.5V15H1V12.5C1,10.17 5.67,9 8,9M8,7A3,3 0 0,1 5,4A3,3 0 0,1 8,1A3,3 0 0,1 11,4A3,3 0 0,1 8,7M16,7A3,3 0 0,1 13,4A3,3 0 0,1 16,1A3,3 0 0,1 19,4A3,3 0 0,1 16,7M9,16.75V19H15V16.75L18.25,20L15,23.25V21H9V23.25L5.75,20L9,16.75Z"></path></svg><?php echo __('Change'); ?></a>
            <input type="hidden" name="user_id" id="user_id"
                value="<?php echo $info['user_id']; ?>" />
        </div>
        </td></tr>
    <tbody>
        <tr>
            <th colspan="2">
            <em><strong><?php echo __('Ticket Information'); ?>&nbsp;&nbsp;&nbsp;</strong> <?php echo __("Due date overrides SLA's grace period."); ?></em>
            </th>
        </tr>
        <tr>
            <td width="160" class="required">
                <?php echo __('Ticket Source');?>
            </td>
            <td>
                <select name="source">
                    <option value="" selected >&mdash; <?php echo __('Select Source');?> &mdash;</option>
                    <option value="Phone" <?php echo ($info['source']=='Phone')?'selected="selected"':''; ?>><?php echo __('Phone');?></option>
                    <option value="Email" <?php echo ($info['source']=='Email')?'selected="selected"':''; ?>><?php echo __('Email');?></option>
                    <option value="Web"   <?php echo ($info['source']=='Web')?'selected="selected"':''; ?>><?php echo __('Web');?></option>
                    <option value="API"   <?php echo ($info['source']=='API')?'selected="selected"':''; ?>><?php echo __('API');?></option>
                    <option value="Other" <?php echo ($info['source']=='Other')?'selected="selected"':''; ?>><?php echo __('Other');?></option>
                </select>
                <font class="error-asterisk">*<?php echo $errors['source']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="160" class="required">
                <?php echo __('Help Topic');?>
            </td>
            <td>
                <select name="topicId">
                    <option value="" selected >&mdash; <?php echo __('Select Help Topic');?> &mdash;</option>
                    <?php
                    if($topics=Topic::getHelpTopics()) {
                        foreach($topics as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['topicId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                <font class="error-asterisk">*</font><div class="error"><?php echo $errors['topicId']; ?></div>
            </td>
        </tr>
        <tr>
            <td width="160">
                <?php echo __('SLA Plan');?>
            </td>
            <td>
                <select name="slaId">
                    <option value="0" selected="selected" >&mdash; <?php echo __('None');?> &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['slaId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['slaId']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="160">
                <?php echo __('Due Date');?>
            </td>
            <td>
                <i class="material-icons datepicker-icon">date_range</i>
                <input class="dp" id="duedate" name="duedate" value="<?php echo Format::htmlchars($info['duedate']); ?>" size="12" autocomplete=OFF>
                <?php
                $min=$hr=null;
                if($info['time'])
                    list($hr, $min)=explode(':', $info['time']);

                echo Misc::timeDropdown($hr, $min, 'time');
                ?>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['duedate']; ?>&nbsp;<?php echo $errors['time']; ?></font><br />
                <em><?php echo __('Time is based on your time zone');?> (GMT <?php echo Format::date(false, false, 'ZZZ'); ?>)</em>
            </td>
        </tr>
    </tbody>
</table>
<table class="form_table dynamic-forms" width="100%" border="0" cellspacing="0" cellpadding="0">
        <?php if ($forms)
            foreach ($forms as $form) {
                $form->render(true, false, array('mode'=>'edit','width'=>160,'entry'=>$form));
        } ?>
</table>
<table class="form_table" width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Internal Note');?></strong> <?php echo __('Reason for editing the ticket (required)');?> <font class="error">&nbsp;<?php echo $errors['note'];?></font></em>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <textarea class="richtext no-bar" name="note" cols="21"
                    rows="6" style="width:80%;"><?php echo $info['note'];
                    ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="text-align:center;margin-top:20px;">
    <input type="submit" name="submit" value="<?php echo __('Save');?>">
    <input type="reset"  name="reset"  value="<?php echo __('Reset');?>">
    <input type="button" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="tickets.php?id=<?php echo $ticket->getId(); ?>"'>
</p>
</form>
<div style="display:none;" class="dialog draggable" id="user-lookup">
    <div class="body"></div>
</div>
<script type="text/javascript">
+(function() {
  var I = setInterval(function() {
    if (!$.fn.sortable)
      return;
    clearInterval(I);
    $('table.dynamic-forms').sortable({
      items: 'tbody',
      handle: 'th',
      helper: function(e, ui) {
        ui.children().each(function() {
          $(this).children().each(function() {
            $(this).width($(this).width());
          });
        });
        ui=ui.clone().css({'background-color':'white', 'opacity':0.8});
        return ui;
      }
    });
  }, 20);
})();
</script>
<script>
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
    //$('#_totales').val(0);
    $('#_totales').attr('readonly', true);
    //$('#_totalholgura').val(0);
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
        
        if($("#_analisises").val()>0){ anali=parseInt($("#_analisises").val()); $("#_analisises").val(parseInt(anali));}
        if($("#_realizaciones").val()>0){ reali=parseInt($("#_realizaciones").val()); $("#_realizaciones").val(parseInt(reali));}
        if($("#_testinges").val()>0){ test=parseInt($("#_testinges").val()); $("#_testinges").val(parseInt(test)); }
        
        total=anali+reali+test;
        $('#_totales').val(total);  
        total=0;anali=0;reali=0;test=0;  
       
    });

    $("#_realizaciones").keyup(function () {
        
        if($("#_analisises").val()>0){ anali=parseInt($("#_analisises").val()); $("#_analisises").val(parseInt(anali));}
        if($("#_realizaciones").val()>0){ reali=parseInt($("#_realizaciones").val()); $("#_realizaciones").val(parseInt(reali));}
        if($("#_testinges").val()>0){ test=parseInt($("#_testinges").val()); $("#_testinges").val(parseInt(test)); }
        
        total=anali+reali+test;
        $('#_totales').val(total);  
        total=0;anali=0;reali=0;test=0;  
        
       
    });

     $("#_testinges").keyup(function () {
       
        if($("#_analisises").val()>0){ anali=parseInt($("#_analisises").val()); $("#_analisises").val(parseInt(anali));}
        if($("#_realizaciones").val()>0){ reali=parseInt($("#_realizaciones").val()); $("#_realizaciones").val(parseInt(reali));}
        if($("#_testinges").val()>0){ test=parseInt($("#_testinges").val()); $("#_testinges").val(parseInt(test)); }
        
        total=anali+reali+test;
        $('#_totales').val(total);  
        total=0;anali=0;reali=0;test=0;  
        
       
    });

    $("#_holanalisises").keyup(function () {
        
        if($("#_holanalisises").val()>0){ anali_h=parseInt($("#_holanalisises").val()); $("#_holanalisises").val(parseInt(anali_h));}
        if($("#_holrealizacion").val()>0){ reali_h=parseInt($("#_holrealizacion").val()); $("#_holrealizacion").val(parseInt(reali_h));}
        if($("#_holtesting").val()>0){ test_h=parseInt($("#_holtesting").val()); $("#_holtesting").val(parseInt(test_h));}
        
        total_h=anali_h+reali_h+test_h;
        $('#_totalholgura').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0;  
       
    });

    $("#_holrealizacion").keyup(function () {
        
        if($("#_holanalisises").val()>0){ anali_h=parseInt($("#_holanalisises").val()); $("#_holanalisises").val(parseInt(anali_h));}
        if($("#_holrealizacion").val()>0){ reali_h=parseInt($("#_holrealizacion").val()); $("#_holrealizacion").val(parseInt(reali_h));}
        if($("#_holtesting").val()>0){ test_h=parseInt($("#_holtesting").val()); $("#_holtesting").val(parseInt(test_h));}
        
        total_h=anali_h+reali_h+test_h;
        $('#_totalholgura').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0;  
        
       
    });

     $("#_holtesting").keyup(function () {
       
        if($("#_holanalisises").val()>0){ anali_h=parseInt($("#_holanalisises").val()); $("#_holanalisises").val(parseInt(anali_h));}
        if($("#_holrealizacion").val()>0){ reali_h=parseInt($("#_holrealizacion").val()); $("#_holrealizacion").val(parseInt(reali_h));}
        if($("#_holtesting").val()>0){ test_h=parseInt($("#_holtesting").val()); $("#_holtesting").val(parseInt(test_h));}
        
        total_h=anali_h+reali_h+test_h;
        $('#_totalholgura').val(total_h);  
        total_h=0;anali_h=0;reali_h=0;test_h=0; 
        
       
    });
    //// FIN DE AGREGADO FRANCISCO COLMENAREZ ------------------

});
</script>
