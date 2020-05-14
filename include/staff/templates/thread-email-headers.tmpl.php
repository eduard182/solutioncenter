<h3 class="drag-handle"><?php echo __('Raw Email Headers'); ?></h3>
<a class="close" href="#"><i class="material-icons">highlight_off</i></a>
<hr/>

<pre style="max-height: 300px; overflow-y: scroll">
<?php echo Format::htmlchars($headers); ?>
</pre>

<hr>
<p class="full-width">
    <span class="buttons pull-right">
        <input type="button" name="cancel" class="close"
            value="<?php echo __('Close'); ?>">
    </span>
</p>
