<h3 class="drag-handle"><?php echo $title ?></h3>
<a class="close" href="#"><i class="material-icons">highlight_off</i></a>
<div class="clear"></div>
<hr/>
<form method="post" action="#<?php echo $path; ?>">
  <div class="inset">
    <?php $form->render(); ?>
  </div>
  <hr>
  <p class="full-width">
    <span class="buttons pull-left">
      <input type="reset" value="<?php echo __('Reset'); ?>" />
      <input type="button" name="cancel" class="close"
        value="<?php echo __('Cancel'); ?>" />
    </span>
    <span class="buttons pull-right">
      <input type="submit" value="<?php
        echo $verb ?: __('Update'); ?>" />
    </span>
  </p>
  <div class="clear"></div>
</form>
