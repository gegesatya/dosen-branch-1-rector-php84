<div class="page-content" style="padding-top: 0;">

  <div class="row">
    <div class="col-md-12 col-sm-12 col-lg-12">

      <div class="portlet box green">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift"></i>Form <?php echo lang('change_password_heading'); ?>
          </div>

        </div>
        <div class="portlet-body form">
          
          
          <?php echo form_open("auth/change_password", "role='form' class='form-horizontal'"); ?>
          <div class="form-body">

            <div class="form-group">
              <label class="col-md-3 control-label"><?php echo lang('change_password_old_password_label', 'old_password'); ?></label>
              <div class="col-md-4">
                <?php echo form_input($old_password, '', 'class="form-control"'); ?>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label"><?php echo sprintf(lang('change_password_new_password_label'), $min_password_length); ?></label>
              <div class="col-md-4">
                <?php echo form_input($new_password, '', 'class="form-control"'); ?>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label"><?php echo lang('change_password_new_password_confirm_label', 'new_password_confirm'); ?></label>
              <div class="col-md-4">
                <?php echo form_input($new_password_confirm, '', 'class="form-control"'); ?>
              </div>
            </div>
          </div>
          <div class="form-actions">
            <div class="row">
              <div class="col-md-offset-3 col-md-9">
                <?php echo form_input($user_id); ?>
                <button type="submit" class="btn btn-circle blue">Submit</button>
                <?php                 ?>
              </div>
            </div>
          </div>
          <?php echo form_close(); ?>
          
        </div>
      </div>













    </div>
  </div>
</div>