<h2><?php echo $this->lang->line('sources_title'); ?></h2>
<div class="inner">
  <form id="edit_source_form" action="#" class="form">
    <div class="group wat-cf">
      <label class="label"><?php echo $this->lang->line('field_content_input'); ?></label>
      <input type="text" name="content" class="text_field" value="<?php echo $source->content; ?>" readonly />
      <span class="description"><?php echo $this->lang->line('field_info_by_xbmc_desc'); ?></span>
    </div>
    <div class="group wat-cf">
      <label class="label"><?php echo $this->lang->line('field_scraper_input'); ?></label>
      <input type="text" name="scraper" class="text_field" value="<?php echo $source->scraper; ?>" readonly />
      <span class="description"><?php echo $this->lang->line('field_info_by_xbmc_desc'); ?></span>
    </div>
    <div class="group wat-cf">
      <label class="label"><?php echo $this->lang->line('field_client_path_input'); ?></label>
      <input type="text" name="client_path" class="text_field" value="<?php echo $source->client_path; ?>" readonly />
      <span class="description"><?php echo $this->lang->line('field_client_path_desc'); ?></span>
    </div>
    <div class="group wat-cf">
      <label class="label"><?php echo $this->lang->line('field_server_path_input'); ?></label>
      <input type="text" name="server_path" class="text_field" value="<?php echo $source->server_path; ?>" />
      <span class="description"><?php echo $this->lang->line('field_server_path_desc'); ?></span>
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
