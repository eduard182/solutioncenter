<?php
if (!defined('OSTSCPINC') || !$thisstaff
        || !$thisstaff->hasPerm(TicketModel::PERM_CREATE, false))
        die('Access Denied');

$info=array();
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);

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

if ($_POST)
    $info['duedate'] = Format::date(strtotime($info['duedate']), false, false, 'UTC');
?>
<form action="tickets.php?a=open" method="post" id="save"  enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="create">
 <input type="hidden" name="a" value="open">
<div style="margin-bottom:20px; padding-top:5px;">
    <div class="pull-left flush-left">
        <h2><?php echo __('Open a New Ticket');?></h2>
    </div>
</div>
 <table id="ticket-open-table" class="form_table fixed" width="100%" border="0" cellspacing="0" cellpadding="0">
    <thead>
    <!-- This looks empty - but beware, with fixed table layout, the user
         agent will usually only consult the cells in the first row to
         construct the column widths of the entire toable. Therefore, the
         first row needs to have two cells -->
        <tr><td style="padding:0;"></td><td style="padding:0;"></td></tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('User Information'); ?></strong></em>
                <div class="error"><?php echo $errors['user']; ?></div>
            </th>
        </tr>
        <?php
        if ($user) { ?>
        <tr><td><?php echo __('User'); ?></td><td>
            <div id="user-info">
                <input type="hidden" name="uid" id="uid" value="<?php echo $user->getId(); ?>" />
            <a href="#" onclick="javascript:
                $.userLookup('ajax.php/users/<?php echo $user->getId(); ?>/edit',
                        function (user) {
                            $('#user-name').text(user.name);
                            $('#user-email').text(user.email);
                        });
                return false;
                "><i class="material-icons">account_box</i>
                <span id="user-name"><?php echo Format::htmlchars($user->getName()); ?></span>
                <!-- &lt;<span id="user-email"><?php echo $user->getEmail(); ?></span>&gt; -->
                </a>
                <a class="inline button" style="overflow:inherit" href="#"
                    onclick="javascript:
                        $.userLookup('ajax.php/users/select/'+$('input#uid').val(),
                            function(user) {
                                $('input#uid').val(user.id);
                                $('#user-name').text(user.name);
                                $('#user-email').text('<'+user.email+'>');
                        });
                        return false;
                    "><svg style="width:18px;height:18px;padding:0 2px 0 0;" viewBox="0 0 24 24"><path fill="#fff" d="M16,9C18.33,9 23,10.17 23,12.5V15H17V12.5C17,11 16.19,9.89 15.04,9.05L16,9M8,9C10.33,9 15,10.17 15,12.5V15H1V12.5C1,10.17 5.67,9 8,9M8,7A3,3 0 0,1 5,4A3,3 0 0,1 8,1A3,3 0 0,1 11,4A3,3 0 0,1 8,7M16,7A3,3 0 0,1 13,4A3,3 0 0,1 16,1A3,3 0 0,1 19,4A3,3 0 0,1 16,7M9,16.75V19H15V16.75L18.25,20L15,23.25V21H9V23.25L5.75,20L9,16.75Z" /></svg><?php echo __('Change'); ?></a>
            </div>
        </td></tr>
        <?php
        } else { //Fallback: Just ask for email and name
            ?>
        <tr>
            <td width="160" class="required"> <?php echo __('Email Address'); ?> </td>
            <td>
                <div class="attached input">
                    <input type="text" size=45 name="email" id="user-email" class="attached"
                        autocomplete="off" autocorrect="off" value="<?php echo $info['email']; ?>" /> </span>
                <a href="?a=open&amp;uid={id}" data-dialog="ajax.php/users/lookup/form"
                    class="attached button"><i class="icon-search"></i></a>
                </div>
                <font class="error-asterisk">*</font>
                <div class="error"><?php echo $errors['email']; ?></div>
            </td>
        </tr>
        <tr>
            <td width="160" class="required"> <?php echo __('Full Name'); ?> </td>
            <td>
                <span style="display:inline-block;">
                    <input type="text" size=45 name="name" id="user-name" value="<?php echo $info['name']; ?>" /> </span>
                <font class="error-asterisk">*</font>
                <div class="error"><?php echo $errors['name']; ?></div>
            </td>
        </tr>
        <?php
        } ?>

        <?php
        if($cfg->notifyONNewStaffTicket()) {  ?>
			<tr>
				<td width="160"><?php echo __('Ticket Notice'); ?></td>
				<td>
					<div id="sendemail">				
						<input type="checkbox" id="checkboxG4" class="css-checkbox" name="alertuser" <?php echo (!$errors || $info['alertuser'])? : ''; ?>>
							<label for="checkboxG4" class="css-label brown">
								<?php echo __('Send alert to user.'); ?>
							</label>
					</div>
				</td>
			</tr>
        <?php
        } ?>
    </tbody>
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Ticket Information and Options');?></strong></em>
            </th>
        </tr>
        <tr>
            <td width="160" class="required">
                <?php echo __('Ticket Source');?>
            </td>
            <td>
                <select name="source">
                    <option value="Phone" selected="selected"><?php echo __('Phone'); ?></option>
                    <option value="Email" <?php echo ($info['source']=='Email')?'selected="selected"':''; ?>><?php echo __('Email'); ?></option>
                    <option value="Other" <?php echo ($info['source']=='Other')?'selected="selected"':''; ?>><?php echo __('Other'); ?></option>
                </select>
                <font class="error-asterisk">*<?php echo $errors['source']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="160" class="required">
                <?php echo __('Help Topic'); ?>
            </td>
            <td>
                <select name="topicId" onchange="javascript:
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
                    <?php
                    if ($topics=Topic::getHelpTopics(false, false, true)) {
                        if (count($topics) == 1)
                            $selected = 'selected="selected"';
                        else { ?>
                        <option value="" selected >&mdash; <?php echo __('Select Help Topic'); ?> &mdash;</option>
<?php                   }
                        foreach($topics as $id =>$name) {
                            echo sprintf('<option value="%d" %s %s>%s</option>',
                                $id, ($info['topicId']==$id)?'selected="selected"':'',
                                $selected, $name);
                        }
                        if (count($topics) == 1 && !$forms) {
                            if (($T = Topic::lookup($id)))
                                $forms =  $T->getForms();
                        }
                    }
                    ?>
                </select>
                <font class="error-asterisk">*</font><div class="error"><?php echo $errors['topicId']; ?></div>
            </td>
        </tr>
        <tr>
            <td width="160">
                <?php echo __('Department'); ?>
            </td>
            <td>
                <select name="deptId">
                    <option value="" selected >&mdash; <?php echo __('Select Department'); ?>&mdash;</option>
                    <?php
                    if($depts=Dept::getDepartments(array('dept_id' => $thisstaff->getDepts()))) {
                        foreach($depts as $id =>$name) {
                            if (!($role = $thisstaff->getRole($id))
                                || !$role->hasPerm(Ticket::PERM_CREATE)
                            ) {
                                // No access to create tickets in this dept
                                continue;
                            }
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['deptId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<font class="error"><?php echo $errors['deptId']; ?></font>
            </td>
        </tr>

         <tr>
            <td width="160">
                <?php echo __('SLA Plan');?>
            </td>
            <td>
                <select name="slaId">
                    <option value="0" selected="selected" >&mdash; <?php echo __('System Default');?> &mdash;</option>
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
        <?php
        if($thisstaff->hasPerm(TicketModel::PERM_ASSIGN, false)) { ?>
        <tr>
            <td width="160"><?php echo __('Assign To');?></td>
            <td>
                <select id="assignId" name="assignId">
                    <option value="0" selected="selected">&mdash; <?php echo __('Select an Agent OR a Team');?> &mdash;</option>
                    <?php
                    if(($users=Staff::getAvailableStaffMembers())) {
                        echo '<OPTGROUP label="'.sprintf(__('Agents (%d)'), count($users)).'">';
                        foreach($users as $id => $name) {
                            $k="s$id";
                            echo sprintf('<option value="%s" %s>%s</option>',
                                        $k,(($info['assignId']==$k)?'selected="selected"':''),$name);
                        }
                        echo '</OPTGROUP>';
                    }

                    if(($teams=Team::getActiveTeams())) {
                        echo '<OPTGROUP label="'.sprintf(__('Teams (%d)'), count($teams)).'">';
                        foreach($teams as $id => $name) {
                            $k="t$id";
                            echo sprintf('<option value="%s" %s>%s</option>',
                                        $k,(($info['assignId']==$k)?'selected="selected"':''),$name);
                        }
                        echo '</OPTGROUP>';
                    }
                    ?>
                </select>&nbsp;<span class='error'>&nbsp;<?php echo $errors['assignId']; ?></span>
            </td>
        </tr>
        <?php } ?>
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
                echo Misc::timeDropdown($hr, $min, 'time'); ?>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['duedate']; ?> &nbsp; <?php echo $errors['time']; ?></font>
                <em><?php echo __('Time is based on your time zone');?> (GMT <?php echo Format::date(false, false, 'ZZZ'); ?>)</em>
            </td>
        </tr>


        </tbody>
        <tbody id="dynamic-form">
        <?php
            foreach ($forms as $form) {
                print $form->getForm()->getMedia();
                include(STAFFINC_DIR .  'templates/dynamic-form.tmpl.php');
            }
        ?>
        </tbody>
        <tbody>
        <?php
        //is the user allowed to post replies??
        if ($thisstaff->getRole()->hasPerm(TicketModel::PERM_REPLY)) { ?>
        <tr>
            <th colspan="2">
                <em><strong><?php echo __('Response');?></strong> <?php echo __('Optional response to the above issue.');?></em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
            <?php
            if(($cannedResponses=Canned::getCannedResponses())) {
                ?>
                <div style="margin-top:0.3em;margin-bottom:0.5em">
                    <?php echo __('Canned Response');?>&nbsp;
                    <select id="cannedResp" name="cannedResp">
                        <option value="0" selected="selected">&mdash; <?php echo __('Select a canned response');?> &mdash;</option>
                        <?php
                        foreach($cannedResponses as $id =>$title) {
                            echo sprintf('<option value="%d">%s</option>',$id,$title);
                        }
                        ?>
                    </select>
                    &nbsp;&nbsp;
                    <label class="checkbox inline"><input type='checkbox' value='1' name="append" id="append" checked="checked"><?php echo __('Append');?></label>
                </div>
            <?php
            }
                $signature = '';
                if ($thisstaff->getDefaultSignatureType() == 'mine')
                    $signature = $thisstaff->getSignature(); ?>
                <textarea
                    class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                        ?> draft draft-delete" data-signature="<?php
                        echo Format::htmlchars(Format::viewableImages($signature)); ?>"
                    data-signature-field="signature" data-dept-field="deptId"
                    placeholder="<?php echo __('Initial response for the ticket'); ?>"
                    name="response" id="response" cols="21" rows="8"
                    style="width:80%;" <?php
    list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.staff.response', false, $info['response']);
    echo $attrs; ?>><?php echo $_POST ? $info['response'] : $draft;
                ?></textarea>
                    <div class="attachments">
<?php
print $response_form->getField('attachments')->render();
?>
                    </div>

                <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
                <td width="100"><?php echo __('Ticket Status');?></td>
                <td>
                    <select name="statusId">
                    <?php
                    $statusId = $info['statusId'] ?: $cfg->getDefaultTicketStatusId();
                    $states = array('open');
                    if ($thisstaff->hasPerm(TicketModel::PERM_CLOSE, false))
                        $states = array_merge($states, array('closed'));
                    foreach (TicketStatusList::getStatuses(
                                array('states' => $states)) as $s) {
                        if (!$s->isEnabled()) continue;
                        $selected = ($statusId == $s->getId());
                        echo sprintf('<option value="%d" %s>%s</option>',
                                $s->getId(),
                                $selected
                                 ? 'selected="selected"' : '',
                                __($s->getName()));
                    }
                    ?>
                    </select>
                </td>
            </tr>
             <tr>
                <td width="100"><?php echo __('Signature');?></td>
                <td>
                    <?php
                    $info['signature']=$info['signature']?$info['signature']:$thisstaff->getDefaultSignatureType();
                    ?>
                    <label><input type="radio" name="signature" value="none" checked="checked"> <?php echo __('None');?></label>
                    <?php
                    if($thisstaff->getSignature()) { ?>
                        <label><input type="radio" name="signature" value="mine"
                            <?php echo ($info['signature']=='mine')?'checked="checked"':''; ?>> <?php echo __('My signature');?></label>
                    <?php
                    } ?>
                    <label><input type="radio" name="signature" value="dept"
                        <?php echo ($info['signature']=='dept')?'checked="checked"':''; ?>> <?php echo sprintf(__('Department Signature (%s)'), __('if set')); ?></label>
                </td>
             </tr>
            </table>
            </td>
        </tr>
        <?php
        } //end canPostReply
        ?>
        <tr>
            <th id="ticket-open-internal-note" colspan="2">
                <em><strong><?php echo __('Internal Note');?></strong>
                <font class="error">&nbsp;<?php echo $errors['note']; ?></font></em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea
                    class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                        ?> draft draft-delete"
                    placeholder="<?php echo __('Optional internal note (recommended on assignment)'); ?>"
                    name="note" cols="21" rows="6" style="width:80%;" <?php
    list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.staff.note', false, $info['note']);
    echo $attrs; ?>><?php echo $_POST ? $info['note'] : $draft;
                ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="text-align:center;margin-top:20px;">
    <input type="submit" name="submit" value="<?php echo _P('action-button', 'Open');?>">
    <input type="reset"  name="reset"  value="<?php echo __('Reset');?>">
    <input type="button" name="cancel" value="<?php echo __('Cancel');?>" onclick="javascript:
        $('.richtext').each(function() {
            var redactor = $(this).data('redactor');
            if (redactor && redactor.opts.draftDelete)
                redactor.deleteDraft();
        });
        window.location.href='tickets.php';
    ">
</p>
</form>
<script type="text/javascript">
$(function() {
    $('input#user-email').typeahead({
        source: function (typeahead, query) {
            $.ajax({
                url: "ajax.php/users?q="+query,
                dataType: 'json',
                success: function (data) {
                    typeahead.process(data);
                }
            });
        },
        onselect: function (obj) {
            $('#uid').val(obj.id);
            $('#user-name').val(obj.name);
            $('#user-email').val(obj.email);
        },
        property: "/bin/true"
    });

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

   <?php
    // Popup user lookup on the initial page load (not post) if we don't have a
    // user selected
    if (!$_POST && !$user) {?>
    setTimeout(function() {
      $.userLookup('ajax.php/users/lookup/form', function (user) {
        window.location.href = window.location.href+'&uid='+user.id;
      });
    }, 100);
    <?php
    } ?>
});
</script>

