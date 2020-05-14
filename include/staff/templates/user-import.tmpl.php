<div id="the-lookup-form">
<h3 class="drag-handle"><?php echo $info['title']; ?></h3>
<a class="close" href="#"><i class="material-icons">highlight_off</i></a>
<hr/>
<?php
if ($info['error']) {
    echo sprintf('<div id="msg_error"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path></svg></div><div id="alert-text">%s</div></div>', $info['error']);
} elseif ($info['warn']) {
    echo sprintf('<div id="msg_warning"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" /></svg></div><div id="alert-text">%s</div></div>', $info['warn']);
} elseif ($info['msg']) {
    echo sprintf('<div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text">%s</div></div>', $info['msg']);
} ?>
<ul class="tabs" id="user-import-tabs">
    <li class="active"><a href="#copy-paste"
        ><i class="icon-edit"></i>&nbsp;<?php echo __('Copy Paste'); ?></a></li>
    <li><a href="#upload"
        ><i class="icon-fixed-width icon-cloud-upload"></i>&nbsp;<?php echo __('Upload'); ?></a></li>
</ul>
<form action="<?php echo $info['action']; ?>" method="post" enctype="multipart/form-data"
    onsubmit="javascript:
    if ($(this).find('[name=import]').val()) {
        $(this).attr('action', '<?php echo $info['upload_url']; ?>');
        $(document).unbind('submit.dialog');
    }">
<?php echo csrf_token();
if ($org_id) { ?>
    <input type="hidden" name="id" value="<?php echo $org_id; ?>"/>
<?php } ?>
<div id="user-import-tabs_container">
<div class="tab_content" id="copy-paste" style="margin:5px;">
<h2 style="margin-bottom:10px"><?php echo __('Name and Email'); ?></h2>
<p><?php echo __(
'Enter one name and email address per line.'); ?><br/><em><?php echo __(
'To import more other fields, use the Upload tab.'); ?></em>
</p>
<textarea name="pasted" style="display:block;width:100%;height:8em"
    placeholder="<?php echo __('e.g. John Doe, john.doe@osticket.com'); ?>">
<?php echo $info['pasted']; ?>
</textarea>
</div>

<div class="hidden tab_content" id="upload" style="margin:5px;">
<h2 style="margin-bottom:10px"><?php echo __('Import a CSV File'); ?></h2>
<p>
<em><?php echo sprintf(__(
'Use the columns shown in the table below. To add more fields, visit the Admin Panel -&gt; Manage -&gt; Forms -&gt; %s page to edit the available fields.  Only fields with `variable` defined can be imported.'),
    UserForm::getUserForm()->get('title')
); ?>
</p>
<table class="list"><tr>
<?php
    $fields = array();
    $data = array(
        array('name' => __('John Doe'), 'email' => __('john.doe@osticket.com'))
    );
    foreach (UserForm::getUserForm()->getFields() as $f)
        if ($f->get('name'))
            $fields[] = $f->get('name');
    foreach ($fields as $f) { ?>
            <th><?php echo mb_convert_case($f, MB_CASE_TITLE); ?></th>
<?php } ?>
</tr>
<?php
    foreach ($data as $d) {
        foreach ($fields as $f) {
            ?><td><?php
            if (isset($d[$f])) echo $d[$f];
            ?></td><?php
        }
    } ?>
</tr></table>
<br/>
<input type="file" name="import"/>
</div>
</div>
    <hr>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="reset" value="<?php echo __('Reset'); ?>">
            <input type="button" name="cancel" class="close"  value="<?php
            echo __('Cancel'); ?>">
        </span>
        <span class="buttons pull-right">
            <input type="submit" value="<?php echo __('Import Users'); ?>">
        </span>
     </p>
</form>
