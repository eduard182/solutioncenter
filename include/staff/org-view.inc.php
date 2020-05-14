<?php
if(!defined('OSTSCPINC') || !$thisstaff || !is_object($org)) die('Invalid path');

?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td width="50%" class="has_bottom_border">
             <h2><a href="orgs.php?id=<?php echo $org->getId(); ?>"
             title="Reload"><?php echo $org->getName(); ?> 
				<svg viewBox="0 0 24 24">
					<path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z" />
				</svg>			 
			 </a></h2>
        </td>
        <td width="50%" class="right_align has_bottom_border">

<?php if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
			<a id="user-more" class="action-button user-more gray" data-dropdown="#action-dropdown-more">
				<div class="">
					<div class="button-icon">
						<svg viewBox="0 0 24 24">
							<path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
						</svg>
					</div>
					<div class="button-text">
						<?php echo __('More'); ?>
					</div>
					<div id="button-more-caret">
						<div class="caret">
							<i class="material-icons more">expand_more</i>
						</div>
					</div>	
				</div>	
			</a>		
<?php } ?>
            <div id="action-dropdown-more" class="action-dropdown anchor-right">
              <ul>
<?php if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
                <li><a href="#ajax.php/orgs/<?php echo $org->getId();
                    ?>/forms/manage" onclick="javascript:
                    $.dialog($(this).attr('href').substr(1), 201);
                    return false"
                    ><i class="icon-paste"></i>
                    <?php echo __('Manage Forms'); ?></a></li>
<?php } ?>


              </ul>
            </div>
			
<?php if ($thisstaff->hasPerm(Organization::PERM_DELETE)) { ?>

			<a id="org-delete" class="user-action org-action org-delete" href="#orgs/<?php echo $org->getId(); ?>/delete">
				<div class="action-button user-delete gray hover-red">
					<div class="button-icon">
						<svg viewBox="0 0 24 24">
							<path d="M12,4A4,4 0 0,1 16,8C16,9.95 14.6,11.58 12.75,11.93L8.07,7.25C8.42,5.4 10.05,4 12,4M12.28,14L18.28,20L20,21.72L18.73,23L15.73,20H4V18C4,16.16 6.5,14.61 9.87,14.14L2.78,7.05L4.05,5.78L12.28,14M20,18V19.18L15.14,14.32C18,14.93 20,16.35 20,18Z" />
						</svg>
					</div>
					<div class="button-text user-lookup">
						<?php echo __('Delete Organization'); ?></a>
					</div>
					<div class="button-spacing">
						&nbsp;
					</div>
				</div>
			</a>
			
<?php } ?>			
			
        </td>
    </tr>
</table>
<table class="ticket_info" cellspacing="0" cellpadding="0" width="100%" border="0">
    <tr>
        <td width="50%">
            <table border="0" cellspacing="" cellpadding="4" width="100%">
                <tr>
                    <th width="150"><?php echo __('Name'); ?></th>
                    <td>
<?php if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
                    <b><a href="#orgs/<?php echo $org->getId();
                    ?>/edit" class="org-action"><i
                        class="icon-edit"></i>
<?php }
                    echo $org->getName();
    if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
                    </a></b>
<?php } ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo __('Account Manager'); ?></th>
                    <td><?php echo $org->getAccountManager(); ?>&nbsp;</td>
                </tr>
            </table>
        </td>
        <td width="50%" style="vertical-align:top">
            <table border="0" cellspacing="" cellpadding="4" width="100%">
                <tr>
                    <th width="150"><?php echo __('Created'); ?></th>
                    <td><?php echo Format::datetime($org->getCreateDate()); ?></td>
                </tr>
                <tr>
                    <th><?php echo __('Last Updated'); ?></th>
                    <td><?php echo Format::datetime($org->getUpdateDate()); ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<div class="clear"></div>
<ul class="clean tabs" id="orgtabs">
	
	
    <li class="active"><a href="#users"><i class="icon-user"></i>&nbsp;<?php echo __('Users'); ?></a></li>
    
    
    
    <!-- <li><a href="#tickets"><i class="icon-list-alt"></i>&nbsp;<?php echo __('Tickets'); ?></a></li> cometado por hdandrea 20-04-18 -->
    
    <!-- ##########     agregago por hdandrea 20-04-18 ##################################  -->
    <li><a href="#open"><i class="icon-list-alt"></i>&nbsp;Abiertas</a></li>
    <li><a href="#approved"><i class="icon-list-alt"></i>&nbsp;Aprobadas</a></li>
     <li><a href="#closed"><i class="icon-list-alt"></i>&nbsp;Cerradas</a></li>
    
    <!-- ################################################################################# -->
    
    
    <li><a href="#notes"><i class="icon-pushpin"></i>&nbsp;<?php echo __('Notes'); ?></a></li>
    
    
</ul>
<div id="orgtabs_container">
<div class="tab_content" id="users">
<?php
include STAFFINC_DIR . 'templates/users.tmpl.php';
?>
</div>

<!-- comentado por hdandrea 20-04-2018
<div class="hidden tab_content" id="tickets">
<?php
include STAFFINC_DIR . 'templates/tickets.tmpl.php';
?>
</div>  -->


<!-- #########  agregado por hdandrea 20-04-18 ########### -->

<div class="hidden tab_content" id="open">
<?php
include STAFFINC_DIR . 'templates/tickets_open.tmpl.php';
?>
</div>

<div class="hidden tab_content" id="approved">
<?php
include STAFFINC_DIR . 'templates/tickets_approved.tmpl.php';
?>
</div>

<div class="hidden tab_content" id="closed">
<?php
include STAFFINC_DIR . 'templates/tickets_closed.tmpl.php';
?>
</div>

<!-- ###################################################### -->

<div class="hidden tab_content" id="notes">
<?php
$notes = QuickNote::forOrganization($org);
$create_note_url = 'orgs/'.$org->getId().'/note';
include STAFFINC_DIR . 'templates/notes.tmpl.php';
?>
</div>




</div>

<script type="text/javascript">
$(function() {
    $(document).on('click', 'a.org-action', function(e) {
        e.preventDefault();
        var url = 'ajax.php/'+$(this).attr('href').substr(1);
        $.dialog(url, [201, 204], function (xhr) {
            if (xhr.status == 204)
                window.location.href = 'orgs.php';
            else
                window.location.href = window.location.href;
         }, {
            onshow: function() { $('#org-search').focus(); }
         });
        return false;
    });
});
</script>
