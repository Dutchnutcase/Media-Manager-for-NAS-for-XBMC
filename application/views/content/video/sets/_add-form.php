<form id="add-set-the_form" action="#" class="form">
  <div class="group">
    <label class="label"><?php echo $this->lang->line('field_new_set_input'); ?></label>
    <input type="text" id="add-set-name" name="name" class="text_field" />
    <span class="description"><?php echo $this->lang->line('field_new_set_desc'); ?></span>
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
