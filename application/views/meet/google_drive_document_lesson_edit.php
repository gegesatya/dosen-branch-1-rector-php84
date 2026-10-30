<input type="hidden" name="lesson_type" value="other-gdrive_document">

<div class="form-group">
    <label><?php echo get_phrase('gdrive_source'); ?>( <?php echo get_phrase('provide_the_source_only'); ?> )</label>
    <input type="text" id="gdrive_source" name="gdrive_source" class="form-control" placeholder="<?php echo get_phrase('provide_the_source_only'); ?>" value="<?php echo $lesson_details['attachment']; ?>">
</div>