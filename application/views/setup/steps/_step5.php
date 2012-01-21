<h3 class="title"><?php echo $this->lang->line('setup_step5'); ?></h3>
<form id="end_form" enctype="multipart/form-data" action="<?php echo base_url() ?>" class="form">
	<h4><?php echo $this->lang->line('setup_usefull_infos'); ?></h4>
	<div class="group wat-cf">
		<div class="left">
			<label class="label right"><?php echo $this->lang->line('field_username_input'); ?></label>
		</div>
		<div class="right">
			<input type="text" class="text_field" value="xbmc" readonly />
		</div>
	</div>
	<div class="group wat-cf">
		<div class="left">
			<label class="label right"><?php echo $this->lang->line('field_password_input'); ?></label>
		</div>
		<div class="right">
			<input type="text" class="text_field" value="xbmc" readonly />
		</div>
	</div>

	<div class="flash">
		<div class="message error">
			<p><?php echo $this->lang->line('setup_remember_sources'); ?></p>
		</div>
	</div>

	<div class="group navform wat-cf">
		<button class="button" type="submit">
			<img src="<?php echo base_url(); ?>assets/gui/tick.png" /> <?php echo $this->lang->line('btn_save'); ?>
		</button>
	</div>
</form>
<hr class="clear" />