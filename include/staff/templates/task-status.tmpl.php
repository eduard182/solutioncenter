<?php
global $cfg;

require_once(INCLUDE_DIR.'class.task.php');

if (!$info[':title'])
    $info[':title'] = __('Cambiar el estado de las tareas');

?>
<h3 class="drag-handle"><?php echo $info[':title']; ?></h3>
<a class="close" href="#"><i class="material-icons">highlight_off</i></a>
<div class="clear"></div>
<hr/>
<?php
if ($info['error']) {
    echo sprintf('<div id="msg_error"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path></svg></div><div id="alert-text">%s</div></div>', $info['error']);
} elseif ($info['warn']) {
    echo sprintf('<div id="msg_warning"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" /></svg></div><div id="alert-text">%s</div></div>', $info['warn']);
} elseif ($info['msg']) {
    echo sprintf('<div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text">%s</div></div>', $info['msg']);
} elseif ($info['notice']) {
   echo sprintf('<div id="msg_info"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text">%s</div></div>',
           $info['notice']);
}

$action = $info[':action'] ?: ('#tasks/mass/'. $action);
?>
<div style="display:block; margin:5px;">
    <form method="post" name="status" id="status"
        action="<?php echo $action; ?>"
        class="mass-action">
        <table width="100%">
            <?php
            if ($info[':extra']) {
                ?>
            <tbody>
                <tr><td colspan="2"><strong><?php echo $info[':extra'];
                ?></strong></td> </tr>
            </tbody>
            <?php
            }
            ?>
            <tbody>
                <tr>
                    <td colspan=2>
                        <span>
                            <strong><?php echo __('Status') ?>&nbsp;</strong>
                            <select name="status">
                            <?php
                           /* $statuses = array(
                                    'open' => __('Open'),
                                    'closed' => __('Closed'));

                            if (!$info['status'])
                                echo '<option value=""> '. __('Select One')
                                .' </option>';
                            foreach ($statuses as $k => $status) {
                                echo sprintf('<option value="%s" %s>%s</option>',
                                        $k,
                                        ($info['status'] == $k)
                                         ? 'selected="selected"' : '',
                                        $status
                                        );
                            }*/

                            foreach (Task::getTaskStatus() as $key => $value) {
                            
                                    $selected="";
                                    if(substr($action,-1)==$value["id"]) $selected='selected="selected"';

                                    echo '<option value="'.$value["id"].'" '.$selected.'>'.$value["name"].'</option>';
                            }



                            ?>
                            </select>
                            <font class="error">*&nbsp;<?php echo
                            $errors['status']; ?></font>
                        </span>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="2">
                        <?php
                        $placeholder = $info[':placeholder'] ?: __('Optional reason for status change (internal note)');
                        ?>
                        <textarea name="comments" id="comments"
                            cols="50" rows="3" wrap="soft" style="width:100%"
                            class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                            ?> no-bar"
                            placeholder="<?php echo $placeholder; ?>"><?php
                            echo $info['comments']; ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <p class="full-width">
            <span class="buttons pull-left">
                <input type="reset" value="<?php echo __('Reset'); ?>">
                <input type="button" name="cancel" class="close"
                value="<?php echo __('Cancel'); ?>">
            </span>
            <span class="buttons pull-right">
                <input type="submit" value="<?php
                echo $verb ?: __('Submit'); ?>">
            </span>
         </p>
    </form>
</div>
<div class="clear"></div>
