<h3 class="title"><?php echo $this->lang->line('setup_step3'); ?></h3>
<form id="upload_sources_form" enctype="multipart/form-data" action="<?php echo base_url() ?>setup/sources" method="post" target="uploadFrame" class="form">
	<div class="group">
		<label class="label"><?php echo $this->lang->line('field_upload_input'); ?></label>
		<input id="real_file_input_sources" type="file" name="sources" class="transparent_field" />
		<input id="fake_file_input_sources" type="text" size="20" class="file_field" />
		<button id="browse_button_sources" class="button" type="button">
			<img src="<?php echo base_url(); ?>assets/gui/browse.png" /> <?php echo $this->lang->line('btn_browse'); ?>
		</button>
		<hr class="clear" />
		<span class="description"><?php echo $this->lang->line('field_upload_sources_desc'); ?></span>
	</div>
	<div class="group navform wat-cf">
		<button class="button" type="submit">
			<img src="<?php echo base_url(); ?>assets/gui/tick.png" /> <?php echo $this->lang->line('btn_upload'); ?>
		</button>
	</div>
</form>
<hr class="clear" />