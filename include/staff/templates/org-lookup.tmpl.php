<?php

if (!$info['title'])
    $info['title'] = __('Organization Lookup');

$msg_info = __('Search existing organizations or add a new one.');
if ($info['search'] === false)
    $msg_info = __('Complete the form below to add a new organization.');

?>
<div id="the-lookup-form">
<h3 class="drag-handle"><?php echo $info['title']; ?></h3>
<a class="close" href="#"><i class="material-icons">highlight_off</i></a>
<hr/>
<div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text"><?php echo $msg_info; ?></div></div>
<?php
if ($info['search'] !== false) { ?>
<div style="margin-bottom:10px;">
    <input type="text" class="search-input" style="width:100%;"
    placeholder="Search by name" id="org-search"
    autofocus autocorrect="off" autocomplete="off"/>
</div>
<?php
}

if ($info['error']) {
    echo sprintf('<div id="msg_error"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path></svg></div><div id="alert-text">%s</div></div>', $info['error']);
} elseif ($info['warning']) {
    echo sprintf('<div id="msg_warning"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" /></svg></div><div id="alert-text">%s</div></div>', $info['warning']);
} elseif ($info['msg']) {
    echo sprintf('<div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text">%s</div></div>', $info['msg']);
} ?>
<div id="selected-org-info" style="display:<?php echo $org ? 'block' :'none'; ?>;margin:5px;">
<form method="post" class="org" action="<?php echo $info['action'] ?: '#orgs/lookup'; ?>">
    <input type="hidden" id="org-id" name="orgid" value="<?php echo $org ? $org->getId() : 0; ?>"/>
    <i class="icon-group icon-4x pull-left icon-border"></i>
    <a class="action-button pull-right" style="overflow:inherit"
        id="unselect-org"  href="#"><i class="icon-remove"></i>
        <?php echo __('Add New Organization'); ?></a>
    <div><strong id="org-name"><?php echo $org ?  Format::htmlchars($org->getName()) : ''; ?></strong></div>
<?php if ($org) { ?>
    <table style="margin-top: 1em;">
<?php foreach ($org->getDynamicData() as $entry) { ?>
    <tr><td colspan="2" style=""><strong><?php
         echo $entry->getForm()->getTitle(); ?></strong></td></tr>
<?php foreach ($entry->getAnswers() as $a) { ?>
    <tr style="vertical-align:top"><td style="width:30%;"><?php echo Format::htmlchars($a->getField()->get('label'));
         ?></td>
    <td style=""><?php echo $a->display(); ?></td>
    </tr>
<?php }
    } ?>
   </table>
 <?php
  } ?>
<div class="clear"></div>
<hr>
<p class="full-width">
    <span class="buttons pull-left">
        <input type="button" name="cancel" class="close"  value="<?php echo __('Cancel'); ?>">
    </span>
    <span class="buttons pull-right">
        <input type="submit" value="<?php echo __('Continue'); ?>">
    </span>
 </p>
</form>
</div>
<div id="new-org-form" style="display:<?php echo $org ? 'none' :'block'; ?>;">
<form method="post" class="org" action="<?php echo $info['action'] ?: '#orgs/add'; ?>">
    <table width="100%" class="fixed">
    <?php
        if (!$form) $form = OrganizationForm::getInstance();
        $form->render(true, __('Create New Organization')); ?>
    </table>
    <hr>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="reset" value="<?php echo __('Reset'); ?>">
            <input type="button" name="cancel" class="<?php echo $org ? 'cancel' : 'close' ?>"
                value="<?php echo __('Cancel'); ?>">
        </span>
        <span class="buttons pull-right">
            <input type="submit" value="<?php echo __('Add Organization'); ?>">
        </span>
     </p>
</form>
</div>
<div class="clear"></div>
</div>
<script type="text/javascript">
$(function() {
    var last_req;
    $('#org-search').typeahead({
        source: function (typeahead, query) {
            if (last_req) last_req.abort();
            last_req = $.ajax({
                url: "ajax.php/orgs/search?q="+query,
                dataType: 'json',
                success: function (data) {
                    typeahead.process(data);
                }
            });
        },
        onselect: function (obj) {
            $('#the-lookup-form').load(
                '<?php echo $info['onselect'] ?: 'ajax.php/orgs/select'; ?>/'+encodeURIComponent(obj.id)
            );
        },
        property: "/bin/true"
    });

    $('a#unselect-org').click( function(e) {
        e.preventDefault();
        $('div#selected-org-info').hide();
        $('div#new-org-form').fadeIn({start: function(){ $('#org-search').focus(); }});
        return false;
     });

    $(document).on('click', 'form.org input.cancel', function (e) {
        e.preventDefault();
        $('div#new-org-form').hide();
        $('div#selected-org-info').fadeIn({start: function(){ $('#org-search').focus(); }});
        return false;
     });
});
</script>
