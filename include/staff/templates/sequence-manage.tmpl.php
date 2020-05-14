<h3 class="drag-handle"><i class="icon-wrench"></i> <?php echo __('Manage Sequences'); ?></i></h3>
<a class="close" href="#"><i class="material-icons">highlight_off</i></a>
<hr/><?php echo __(
'Sequences are used to generate sequential numbers. Various sequences can be
used to generate sequences for different purposes.'); ?>
<br/>
<br/>
<form method="post" action="<?php echo $info['action']; ?>">
<div id="sequences">
<?php
$current_list = array();
foreach ($sequences as $e) {
    $field = function($field, $name=false) use ($e) { ?>
    <input class="f<?php echo $field; ?>" type="hidden" name="seq[<?php echo $e->id;
        ?>][<?php echo $name ?: $field; ?>]" value="<?php echo $e->{$field}; ?>"/>
<?php }; ?>
    <div class="row-item">
        <?php echo $field('name'); echo $field('current', 'next'); echo $field('increment'); echo $field('padding'); ?>
        <input type="hidden" class="fdeleted" name="seq[<?php echo $e->get('id'); ?>][deleted]" value="0"/>
        <i class="icon-sort-by-order"></i>
        <div style="display:inline-block" class="name"> <?php echo $e->getName(); ?> </div>
        <div class="manage-buttons pull-right">
            <span class="faded"><?php echo __('next'); ?></span>
            <span class="current"><?php echo $e->current(); ?></span>
        </div>
        <div class="button-group">
            <div class="manage"><a href="#">
				<svg viewBox="0 0 24 24">
					<path  d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
				</svg>					
			</a></div>
            <div class="delete"><?php if (!$e->hasFlag(Sequence::FLAG_INTERNAL)) { ?>
                <a href="#"><i class="icon-trash"></i></a><?php } ?></div>
        </div>
        <div class="management hidden" data-id="<?php echo $e->id; ?>">
            <table width="100%"><tbody>
                <tr><td><label style="padding:0"><?php echo __('Increment'); ?>
                    <input class="-increment" type="text" size="4" value="<?php echo Format::htmlchars($e->increment); ?>"/>
                    </label></td>
                    <td><label style="padding:0"><?php echo __('Padding Character'); ?>
                    <input class="-padding" maxlength="1" type="text" size="4" value="<?php echo Format::htmlchars($e->padding); ?>"/>
                    </label></td></tr>
            </tbody></table>
        </div>
    </div>
<?php } ?>
</div>

<div class="row-item hidden" id="template">
    <i class="icon-sort-by-order"></i>
    <div style="display:inline-block" class="name"> <?php echo __('New Sequence'); ?> </div>
    <div class="manage-buttons pull-right">
        <span class="faded">next</span>
        <span class="next">1</span>
    </div>
    <div class="button-group">
        <div class="manage"><a href="#">
			<svg viewBox="0 0 24 24">
				<path  d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
			</svg>		
		</a></div>
        <div class="delete new"><a href="#"><i class="icon-trash"></i></a></div>
    </div>
    <div class="management hidden" data-id="<?php echo $e->id; ?>">
        <table width="100%"><tbody>
            <tr><td><label style="padding:0"><?php echo __('Increment'); ?>
                <input class="-increment" type="text" size="4" value="1"/>
                </label></td>
                <td><label style="padding:0"><?php echo __('Padding Character'); ?>
                <input class="-padding" maxlength="1" type="text" size="4" value="0"/>
                </label></td></tr>
        </tbody></table>
    </div>
</div>

<hr/>
<button onclick="javascript:
  var id = ++$.uid, base = 'seq[new-'+id+']';
  var clone = $('.row-item#template').clone()
    .appendTo($('#sequences'))
    .removeClass('hidden')
    .append($('<input>').attr({type:'hidden',class:'fname',name:base+'[name]',value:'<?php echo __('New Sequence'); ?>'}))
    .append($('<input>').attr({type:'hidden',class:'fcurrent',name:base+'[current]',value:'1'}))
    .append($('<input>').attr({type:'hidden',class:'fincrement',name:base+'[increment]',value:'1'}))
    .append($('<input>').attr({type:'hidden',class:'fpadding',name:base+'[padding]',value:'0'})) ;
  clone.find('.manage a').trigger('click');
  return false;
  "><i class="icon-plus"></i> <?php echo __('Add New Sequence'); ?></button>
<div id="delete-warning" style="display:none">
<hr>
    <div id="msg_warning"><?php echo __(
    'Clicking <strong>Save Changes</strong> will permanently remove the
    deleted sequences.'); ?>
    </div>
</div>
<hr>
<div>
    <span class="buttons pull-right">
        <input type="submit" value="<?php echo __('Save Changes'); ?>" onclick="javascript:
$('#sequences .save a').each(function() { $(this).trigger('click'); });
">
    </span>
</div>

<script type="text/javascript">
$(function() {
  var remove = function() {
    if (!$(this).parent().hasClass('new')) {
      $('#delete-warning').show();
      $(this).closest('.row-item').hide()
        .find('input.fdeleted').val('1');
      }
    else
      $(this).closest('.row-item').remove();
    return false;
  }, manage = function() {
    var top = $(this).closest('.row-item');
    top.find('.management').show(200);
    top.find('.name').empty().append($('<input class="-name" type="text" size="40">')
      .val(top.find('input.fname').val())
    );
    top.find('.current').empty().append($('<input class="-current" type="text" size="10">')
      .val(top.find('input.fcurrent').val())
    );
    $(this).find('i').attr('class','icon-save');
    $(this).parent().attr('class','save');
    return false;
  }, save = function() {
    var top = $(this).closest('.row-item');
    top.find('.management').hide(200);
     $.each(['name', 'current'], function(i, t) {
      var val = top.find('input.-'+t).val();
      top.find('.'+t).empty().text(val);
      top.find('input.f'+t).val(val);
    });
    $.each(['increment', 'padding'], function(i, t) {
      top.find('input.f'+t).val(top.find('input.-'+t).val());
    });
    $(this).find('i').attr('class','icon-cog');
    $(this).parent().attr('class','manage');
    return false;
  };
  $(document).on('click.seq', '#sequences .manage a', manage);
  $(document).on('click.seq', '#sequences .save a', save);
  $(document).on('click.seq', '#sequences .delete a', remove);
  $('.close, input:submit').click(function() {
      $(document).off('click.seq');
  });
});
</script>
