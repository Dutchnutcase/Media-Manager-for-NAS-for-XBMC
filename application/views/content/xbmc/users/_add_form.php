<form id="add_user_form" action="#" class="form">
  <div class="group wat-cf">
    <div class="left">
      <label class="label right"><?php echo $this->lang->line('field_username_input'); ?></label>
    </div>
    <div class="right">
      <input type="text" id="add-user-username" name="username" class="text_field" />
    </div>
  </div>
  <div class="group wat-cf">
    <div class="left">
      <label class="label right"><?php echo $this->lang->line('field_password_input'); ?></label>
    </div>
    <div class="right">
      <input type="password" id="add-user-password" name="password" class="text_field" />
    </div>
  </div>
  <div class="group navform wat-cf">
    <button class="button" type="submit">
      <img src="<?php echo base_url(); ?>assets/gui/tick.png" /> <?php echo $this->lang->line('btn_save'); ?>
    </button>
    <span class="text_button_padding"><?php echo $this->lang->line('btn_or'); ?></span>
    <button id="cancel-button" class="button" type="submit">
      <img src="<?php echo base_url(); ?>assets/gui/cross.png" /> <?php echo $this->lang->line('btn_cancel'); ?>
    </button>
  </div>
</form>
