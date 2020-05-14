<?php
    if ($list) {
        $page = ($_GET['p'] && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
        $count = $list->getNumItems();
        $pageNav = new Pagenate($count, $page, PAGE_LIMIT);
        if ($list->getSortMode() == 'SortCol')
            $pageNav->setSlack(1);
        $pageNav->setURL('lists.php?id='.$list->getId().'&a=items');
    }
    ?>
    <div style="margin: 5px 0">
    <?php if ($list) { ?>
    <div class="pull-left">
        <input type="text" placeholder="<?php echo __('Search items'); ?>"
            data-url="ajax.php/list/<?php echo $list->getId(); ?>/items/search"
            size="25" id="items-search" value="<?php
            echo Format::htmlchars($_POST['search']); ?>"/>
    </div>
    <div class="pull-right">
<?php
if ($list->allowAdd()) { ?>
        <a class="green button action-button field-config"
            href="#list/<?php
            echo $list->getId(); ?>/item/add">
            <i class="icon-plus-sign"></i>
            <?php echo __('Add New Item'); ?>
        </a>
<?php
    if (method_exists($list, 'importCsv')) { ?>
        <a class="action-button field-config"
            href="#list/<?php
            echo $list->getId(); ?>/import">
            <i class="icon-upload"></i>
            <?php echo __('Import Items'); ?>
        </a>
<?php
    }
} ?>

		<a id="ticket-more" data-dropdown="#action-dropdown-more">
			<div class="action-button change-status gray">
				<div class="button-icon">
					<svg viewBox="0 0 24 24">
						<path  d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
					</svg>
				</div>
				<div class="button-text">
					<?php echo __('More');?>
				</div>
				<div id="button-more-caret">
					<div class="caret">
						<i class="material-icons more">expand_more</i>
					</div>
				</div>        
			</div>
		</a>				

        <div id="action-dropdown-more" class="action-dropdown anchor-right">
            <ul>
                <li><a class="items-action" href="#list/<?php echo $list->getId(); ?>/disable">
                    <i class="icon-ban-circle icon-fixed-width"></i>
                    <?php echo __('Disable'); ?></a></li>
                <li><a class="items-action" href="#list/<?php echo $list->getId(); ?>/enable">
                    <i class="icon-ok-sign icon-fixed-width"></i>
                    <?php echo __('Enable'); ?></a></li>
                <li class="danger"><a class="items-action" href="#list/<?php echo $list->getId(); ?>/delete">
                    <i class="icon-trash icon-fixed-width"></i>
                    <?php echo __('Delete'); ?></a></li>
            </ul>
        </div>
    </div>
    <?php } ?>

    <div class="clear"></div>
    </div>


<?php
$prop_fields = ($list) ? $list->getSummaryFields() : array();
?>

    <table class="form_table fixed" width="100%" border="0" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th width="28" nowrap></th>
            <th><?php echo __('Value'); ?></th>
<?php
if ($prop_fields) {
    foreach ($prop_fields as $F) { ?>
            <th><?php echo $F->getLocal('label'); ?></th>
<?php
    }
} ?>
        </tr>
    </thead>

    <tbody id="list-items" <?php if (!isset($_POST['search']) && $list && $list->get('sort_mode') == 'SortCol') { ?>
            class="sortable-rows" data-sort="sort-"<?php } ?>>
        <?php
        if ($list) {
            $icon = ($list->get('sort_mode') == 'SortCol')
                ? '<i class="icon-sort"></i>&nbsp;' : '';
            $items = $list->getAllItems();
            $items = $pageNav->paginate($items);
            // Emit a marker for the first sort offset ?>
            <input type="hidden" id="sort-offset" value="<?php echo
                max($items[0]->sort, $pageNav->getStart()); ?>"/>
<?php
            foreach ($items as $item) {
                include STAFFINC_DIR . 'templates/list-item-row.tmpl.php';
            }
        } ?>
    </tbody>
    </table>
<?php if ($pageNav && $pageNav->getNumPages()) { ?>
    <div><?php echo __('Page').':'.$pageNav->getPageLinks('items', $pjax_container); ?></div>
<?php } ?>
</div>
<script type="text/javascript">
$(function() {
  var last_req;
  $('input#items-search').typeahead({
    source: function (typeahead, query) {
      if (last_req)
        last_req.abort();
      var $el = this.$element;
      var url = $el.data('url')+'?q='+query;
      last_req = $.ajax({
        url: url,
        dataType: 'json',
        success: function (data) {
          typeahead.process(data);
        }
      });
    },
    onselect: function (obj) {
      var $el = this.$element,
          url = 'ajax.php/list/{0}/item/{1}/update'
            .replace('{0}', obj.list_id)
            .replace('{1}', obj.id);
      $.dialog(url, [201], function (xhr, resp) {
        var json = $.parseJSON(resp);
        if (json && json.success) {
          if (json.id && json.row) {
            $('#list-item-' + json.id).replaceWith(json.row);
          }
        }
      });
      this.$element.val('');
    },
    property: "display"
  });
});
</script>
