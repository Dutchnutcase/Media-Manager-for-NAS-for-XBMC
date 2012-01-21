<h3 class="title"><?php echo $this->lang->line('setup_step2'); ?></h3>
<form id="upload_advancedsettings_form" enctype="multipart/form-data" action="<?php echo base_url() ?>setup/advancedsettings" method="post" target="uploadFrame" class="form">
	<div class="group">
		<label class="label"><?php echo $this->lang->line('field_upload_input'); ?></label>
		<input id="real_file_input_advancedsettings" type="file" name="advancedsettings" class="transparent_field" />
		<input id="fake_file_input_advancedsettings" type="text" size="20" class="file_field" />
		<button id="browse_button_advancedsettings" class="button" type="button">
			<img src="<?php echo base_url(); ?>assets/gui/browse.png" /> <?php echo $this->lang->line('btn_browse'); ?>
		</button>
		<hr class="clear" />
		<span class="description"><?php echo $this->lang->line('field_upload_advancedsettings_desc'); ?></span>
	</div>
	<div class="group navform wat-cf">
		<button class="button" type="submit">
			<img src="<?php echo base_url(); ?>assets/gui/tick.png" /> <?php echo $this->lang->line('btn_upload'); ?>
		</button>
	</div>
</form>
<hr class="clear" />