<div id="advanced-search">
<h3 class="drag-handle"><?php echo __('Advanced Ticket Search');?></h3>
<a class="close" href=""><i class="material-icons">highlight_off</i></a>
<hr/>
<form action="#tickets/search" method="post" name="search">
<div class="row">
<div class="span6">
    <input type="hidden" name="a" value="search">
<?php
foreach ($form->errors(true) ?: array() as $message) {
    ?><div class="error-banner"><?php echo $message;?></div><?php
}

$info = $search->getSearchFields($form);
foreach (array_keys($info) as $F) {
    ?><input type="hidden" name="fields[]" value="<?php echo $F; ?>"/><?php
}
$errors = !!$form->errors();
$inbody = false;
$first_field = true;
foreach ($form->getFields() as $name=>$field) {
    @list($name, $sub) = explode('+', $field->get('name'), 2);
    if ($sub === 'search') {
        if (!$first_field) {
            echo '</div></div>';
        }
        echo '<div class="adv-search-field-container">';
        $inbody = false;
        $first_field = false;
    }
    elseif (!$first_field && !$inbody) {
        echo sprintf('<div class="adv-search-field-body %s">',
            !$errors && isset($info[$name]) && $info[$name]['active'] ? 'hidden' : '');
        $inbody = true;
    }
?>
    <fieldset id="field<?php echo $field->getWidget()->id; ?>" <?php
        $class = array();
        if (!$field->isVisible())
            $class[] = "hidden";
        if ($sub === 'method')
            $class[] = "adv-search-method";
        elseif ($sub === 'search')
            $class[] = "adv-search-field";
        elseif ($field->get('__searchval__'))
            $class[] = "adv-search-val";
        if ($class)
            echo 'class="'.implode(' ', $class).'"';
        ?>>
        <?php echo $field->render(); ?>
        <?php if (!$errors && $sub === 'search' && isset($info[$name]) && $info[$name]['active']) { ?>
            <span style="padding-left: 5px">
            <a href="#"  data-name="<?php echo Format::htmlchars($name); ?>" onclick="javascript:
    var $this = $(this),
        name = $this.data('name'),
        expanded = $this.data('expanded') || false;
    $this.closest('.adv-search-field-container').find('.adv-search-field-body').slideDown('fast');
    $this.find('span.faded').hide();
    $this.find('i').removeClass('icon-caret-right').addClass('icon-caret-down');
    return false;
"><i class="icon-caret-right"></i>
            <span class="faded"><?php echo $search->describeField($info[$name]); ?></span>
            </a>
            </span>
        <?php } ?>
        <?php foreach ($field->errors() as $E) {
            ?><div class="error"><?php echo $E; ?></div><?php
        } ?>
    </fieldset>
    <?php if ($name[0] == ':' && substr($name, -7) == '+search') {
        list($N,) = explode('+', $name, 2);
?>
    <input type="hidden" name="fields[]" value="<?php echo $N; ?>"/>
    <?php }
}
if (!$first_field)
    echo '</div></div>';
?>
<div id="extra-fields"></div>
<hr/>
<select id="search-add-new-field" name="new-field" style="max-width: 300px;">
    <option value="">— <?php echo __('Add Other Field'); ?> —</option>
<?php
foreach ($matches as $name => $fields) { ?>
    <optgroup label="<?php echo $name; ?>">
<?php
    foreach ($fields as $id => $desc) { ?>
        <option value="<?php echo $id; ?>" <?php
            if (isset($state[$id])) echo 'disabled="disabled"';
        ?>><?php echo ($desc instanceof FormField ? $desc->getLocal('label') : $desc); ?></option>
<?php } ?>
    </optgroup>
<?php } ?>
</select>

</div>
<div class="span6" style="border-left:1px solid #888;position:relative;padding-bottom:26px;">
<div style="margin-bottom: 0.5em;"><b style="font-size: 110%;"><?php echo __('Saved Searches'); ?></b></div>
<hr>
<div id="saved-searches" class="accordian" style="max-height:200px;overflow-y:auto;">
<?php foreach (SavedSearch::forStaff($thisstaff) as $S) { ?>
    <dt class="saved-search">
        <a href="#" class="load-search"><?php echo $S->title; ?>
        <i class="icon-chevron-down pull-right"></i>
        </a>
    </dt>
    <dd>
        <span>
            <button type="button" onclick="javascript:$(this).closest('form').attr({
'method': 'get', 'action': '#tickets/search/<?php echo $S->id; ?>'}).trigger('submit');"><i class="icon-chevron-left"></i> <?php echo __('Load'); ?></button>
            <button type="button" onclick="javascript:
var that = this;
$.ajax({
    url: 'ajax.php/tickets/search/<?php echo $S->id; ?>',
    type: 'POST',
    data: {'form': $(this).closest('.dialog').find('form[name=search]').serializeArray()},
    dataType: 'json',
    success: function(json) {
      if (!json.id)
        return;
      $(that).closest('dd').effect('highlight');
    }
});
return false;
"><i class="icon-save"></i> <?php echo __('Update'); ?></button>
        </span>
        <span class="pull-right">
            <button type="button" title="<?php echo __('Delete'); ?>" onclick="javascript:
    if (!confirm(__('You sure?'))) return false;
    var that = this;
    $.ajax({
        'url': 'ajax.php/tickets/search/<?php echo $S->id; ?>',
        'type': 'delete',
        'dataType': 'json',
        'success': function(json) {
            if (json.success) {
                $(that).closest('dd').prev('dt').slideUp().next('dd').slideUp();
            }
        }
    });
    return false;
"><i class="icon-trash"></i></button>
        </span>
    </dd>
<?php } ?>
</div>
<div style="position:absolute;bottom:0">
<hr>
    <form method="post">
    <div class="attached input">
    <input name="title" type="text" size="27" placeholder="<?php
        echo __('Enter a title for the search'); ?>"/>
        <a class="attached button" href="#tickets/search/create" onclick="javascript:
$.ajax({
    url: 'ajax.php/' + $(this).attr('href').substr(1),
    type: 'POST',
    data: {'name': $(this).closest('form').find('[name=title]').val(),
           'form': $(this).closest('.dialog').find('form[name=search]').serializeArray()},
    dataType: 'json',
    success: function(json) {
      if (!json.id)
        return;
      $('<dt>')
        .append($('<a>').text(' ' + json.title)
          .prepend($('<i>').addClass('icon-chevron-left'))
        ).appendTo($('#saved-searches'));
    }
});
return false;
"><svg viewBox="0 0 24 24">
    <path d="M15,9H5V5H15M12,19A3,3 0 0,1 9,16A3,3 0 0,1 12,13A3,3 0 0,1 15,16A3,3 0 0,1 12,19M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3Z" />
</svg></a>
    </div>
</div>
</div>
</div>

<hr class="show">
<div>
    <div id="search-hint" class="pull-left">
    </div>
    <div class="buttons pull-right">
        <button class="button" type="submit" id="do_search">
			<svg viewBox="0 0 24 24">
				<path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"></path>
			</svg>		
            <?php echo __('Search'); ?></button>
    </div>
</div>

</form>

<style type="text/css">
#advanced-search .span6 .select2 {
  max-width: 300px !important;
}
</style>

<script type="text/javascript">
$(function() {
  $('#advanced-search [data-dropdown]').dropdown();

  var I = setInterval(function() {
    var A = $('#saved-searches.accordian');
    if (!A.length) return;
    clearInterval(I);

    var allPanels = $('dd', A).hide();
    $('dt > a', A).click(function() {
      $('dt', A).removeClass('active');
      allPanels.slideUp();
      $(this).parent().addClass('active').next().slideDown();
      return false;
    });
  }, 200);

  $('#search-add-new-field').on('change', function() {
    var that=this;
    $.ajax({
      url: 'ajax.php/tickets/search/field/'+$(this).val(),
      type: 'get',
      dataType: 'json',
      success: function(json) {
        if (!json.success)
          return false;
        ff_uid = json.ff_uid;
        $(that).find(':selected').prop('disabled', true);
        $('#extra-fields').append($(json.html));
      }
    });
  });
});
</script>
