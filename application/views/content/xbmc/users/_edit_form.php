<h2><?php echo sprintf($this->lang->line('users_settings_account'), $user->username); ?></h2>
<div class="inner">
  <form id="edit_user_form" action="#" class="form">
    <div class="group wat-cf">
      <label class="label"><?php echo $this->lang->line('field_username_input'); ?></label>
      <input type="text" id="username" name="username" class="text_field" value="<?php echo $user->username; ?>" />
    </div>
    <div class="group wat-cf">
      <label class="label"><?php echo $this->lang->line('field_password_input'); ?></label>
      <input type="password" id="password" name="password" class="text_field" />
      <span class="description"><?php echo $this->lang->line('users_password_desc'); ?></span>
    </div>
    <div class="group wat-cf">
      <input type="checkbox" name="can_change_images" id="can_change_images" class="checkbox" <?php echo ($user->can_change_images) ? 'checked' : ''; ?>  />
      <label for="can_change_images" class="checkbox"><?php echo stripslashes($this->lang->line('users_can_change_images')); ?></label>
    </div>
    <div class="group wat-cf">
      <input type="checkbox" name="can_change_infos" id="can_change_infos" class="checkbox" <?php echo ($user->can_change_infos) ? 'checked' : ''; ?>  />
      <label for="can_change_infos" class="checkbox"><?php echo stripslashes($this->lang->line('users_can_change_infos')); ?></label>
    </div>
    <div class="group wat-cf">
      <input type="checkbox" name="can_download_video" id="can_download_video" class="checkbox" <?php echo ($user->can_download_video) ? 'checked' : ''; ?>  />
      <label for="can_download_video" class="checkbox"><?php echo stripslashes($this->lang->line('users_can_download_video')); ?></label>
    </div>
    <div class="group wat-cf">
      <input type="checkbox" name="can_download_music" id="can_download_music" class="checkbox" <?php echo ($user->can_download_music) ? 'checked' : ''; ?>  />
      <label for="can_download_music" class="checkbox"><?php echo stripslashes($this->lang->line('users_can_download_music')); ?></label>
    </div>
    <div class="group wat-cf">
      <input type="checkbox" name="is_active" id="is_active" class="checkbox" <?php echo ($user->is_active) ? 'checked' : ''; ?>  />
      <label for="is_active" class="checkbox"><?php echo stripslashes($this->lang->line('users_is_active')); ?></label>
    </div>
    <div class="group wat-cf">
      <input type="checkbox" name="is_admin" id="is_admin" class="checkbox" <?php echo ($user->is_admin) ? 'checked' : ''; ?>  />
      <label for="is_admin" class="checkbox"><?php echo stripslashes($this->lang->line('users_is_admin')); ?></label>
    </div>
    <div class="group navform wat-cf">
      <button id="save-button" class="button" type="submit">
        <img src="<?php echo base_url(); ?>assets/gui/tick.png" /> <?php echo $this->lang->line('btn_save'); ?>
      </button>
      <span class="text_button_padding"><?php echo $this->lang->line('btn_or'); ?></span>
      <button id="cancel-button" class="button" >
        <img src="<?php echo base_url(); ?>assets/gui/cross.png" /> <?php echo $this->lang->line('btn_cancel'); ?>
      </button>
    </div>
    </form>
  </div><!-- inner -->
