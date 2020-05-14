<div id="the-lookup-form">

	<div id="the-lookup-form-left">
		<h3 class="drag-handle"><?php echo $info['title']; ?></h3>
	</div>
	<div id="the-lookup-form-right">
		<b><a class="close lookup" href="#">
			<i class="material-icons">highlight_off</i>
		</a></b>
	</div>

    <?php
        if (!isset($info['lookup']) || $info['lookup'] !== false) { ?>
    <div id="change-user-search-form">
        <input type="search" class="search-input" style="width:100%;"
            placeholder="<?php echo __('Search by email, phone or name'); ?>" id="user-search"
            autofocus autocorrect="off" autocomplete="off"/>
    </div>
    <?php
        }
        
        if ($info['error']) {
            echo sprintf('<div id="msg_error"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path></svg></div><div id="alert-text">%s</div></div>',$error);
        } elseif ($info['warn']) {
            echo sprintf('<div id="msg_warning"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" /></svg></div><div id="alert-text">%s</div>', $info['warn']);
        } elseif ($info['msg']) {
            echo sprintf('<div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text">%s</div></div>', $info['msg']);
        } ?>
    <div id="selected-user-info" style="display:<?php echo $user ? 'block' :'none'; ?>;margin:5px;">
        <form method="post" class="user" action="<?php echo $info['action'] ?  $info['action'] : '#users/lookup'; ?>">
            <input type="hidden" id="user-id" name="id" value="<?php echo $user ? $user->getId() : 0; ?>"/>
            <?php
                if ($user) { ?>
            <div class="avatar pull-left" style="margin: 0 10px;">
                <?php echo $user->getAvatar(); ?>
            </div>
            <?php
                }
                else { ?>
            <i class="icon-user icon-4x pull-left icon-border"></i>
            <?php
                }
                if ($thisstaff->hasPerm(User::PERM_CREATE)) { ?>
            <a class="action-button pull-right" style="overflow:inherit"
                id="unselect-user"  href="#">
                <svg id="change-user" style="width:24px;height:24px" viewBox="0 0 24 24">
                    <path d="M16,9C18.33,9 23,10.17 23,12.5V15H17V12.5C17,11 16.19,9.89 15.04,9.05L16,9M8,9C10.33,9 15,10.17 15,12.5V15H1V12.5C1,10.17 5.67,9 8,9M8,7A3,3 0 0,1 5,4A3,3 0 0,1 8,1A3,3 0 0,1 11,4A3,3 0 0,1 8,7M16,7A3,3 0 0,1 13,4A3,3 0 0,1 16,1A3,3 0 0,1 19,4A3,3 0 0,1 16,7M9,16.75V19H15V16.75L18.25,20L15,23.25V21H9V23.25L5.75,20L9,16.75Z"></path>
                </svg>
                <?php echo __('Add New User'); ?>
            </a>
            <?php }
                if ($user) { ?>
            <div><strong id="user-name"><?php echo Format::htmlchars($user->getName()->getOriginal()); ?></strong></div>
            <div>
                <!-- &lt;<span id="user-email"><?php echo $user->getEmail(); ?></span>&gt; -->
            </div>
            <?php
                if ($org=$user->getOrganization()) { ?>
            <div><span id="user-org"><?php echo $org->getName(); ?></span></div>
            <?php
                } ?>
            <table style="margin-top: 1em;">
                <?php foreach ($user->getDynamicData() as $entry) { ?>
                <tr>
                    <td colspan="2" style=""><strong><?php
                        echo $entry->getTitle(); ?></strong></td>
                </tr>
                <?php foreach ($entry->getAnswers() as $a) { ?>
                <tr style="vertical-align:top">
                    <td style="width:30%;"><?php echo Format::htmlchars($a->getField()->get('label'));
                        ?></td>
                    <td style=""><?php echo $a->display(); ?></td>
                </tr>
                <?php }
                    }
                    ?>
            </table>
            <?php } ?>
            <div class="clear"></div>
            <p class="full-width">
                <span class="buttons pull-left">
                <input type="button" name="cancel" class="close"  value="<?php
                    echo __('Cancel'); ?>">
                </span>
                <span class="buttons pull-right">
                <input type="submit" value="<?php echo __('Continue'); ?>">
                </span>
            </p>
        </form>
    </div>
    <div id="new-user-form" style="display:<?php echo $user ? 'none' :'block'; ?>;">
        <?php if ($thisstaff->hasPerm(User::PERM_CREATE)) { ?>
        <form method="post" class="user" action="<?php echo $info['action'] ?: '#users/lookup/form'; ?>">
            <table width="100%" class="fixed">
                <?php
                    if(!$form) $form = UserForm::getInstance();
                    $form->render(true, __('Create New User')); ?>
            </table>
            <p class="full-width">
                <span class="buttons pull-left">
                <input type="reset" tabindex="0" value="<?php echo __('Reset'); ?>">
                <input type="button" name="cancel" tabindex="0" class="<?php echo $user ?  'cancel' : 'close' ?>"  value="<?php echo __('Cancel'); ?>">
                </span>
                <span class="buttons pull-right">
                <input type="submit" tabindex="0" value="<?php echo __('Add User'); ?>">
                </span>
            </p>
        </form>
        <?php }
            else { ?>
        <p class="full-width">
            <span class="buttons pull-left">
            <input type="button" name="cancel" tabindex="0" class="<?php echo $user ?  'cancel' : 'close' ?>"  value="<?php echo __('Cancel'); ?>">
            </span>
        </p>
        <?php } ?>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    $(function() {
        var last_req;
        $('#user-search').typeahead({
            source: function (typeahead, query) {
                if (last_req) last_req.abort();
                last_req = $.ajax({
                    url: "ajax.php/users<?php
        echo $info['lookup'] ? "/{$info['lookup']}" : '' ?>?q="+query,
                    dataType: 'json',
                    success: function (data) {
                        typeahead.process(data);
                    }
                });
            },
            onselect: function (obj) {
                $('#the-lookup-form').load(
                    '<?php echo $info['onselect']? $info['onselect']: "ajax.php/users/select/"; ?>'+encodeURIComponent(obj.id)
                );
            },
            property: "/bin/true"
        });
    
        $('a#unselect-user').click( function(e) {
            e.preventDefault();
            $("#msg_error, #msg_notice, #msg_warning").fadeOut();
            $('div#selected-user-info').hide();
            $('div#new-user-form').fadeIn({start: function(){ $('#user-search').focus(); }});
            return false;
         });
    
        $(document).on('click', 'form.user input.cancel', function (e) {
            e.preventDefault();
            $('div#new-user-form').hide();
            $('div#selected-user-info').fadeIn({start: function(){ $('#user-search').focus(); }});
            return false;
         });
    });
</script>