<h3 class="title"><?php echo $this->lang->line('setup_step4'); ?></h3>
<form id="symbolic_form" action="#" class="form">
	<div class="group">
		<label class="label"><?php echo $this->lang->line('field_symbolic'); ?></label>
		<input type="text" id="symbolic" name="symbolic" class="text_field" value="" />
		<hr class="clear" />
		<span class="description"><?php echo $this->lang->line('field_symbolic_desc'); ?></span>
	</div>
	<div class="group navform wat-cf">
		<button class="button_language" type="submit">
			<img src="<?php echo base_url(); ?>assets/gui/tick.png" /> <?php echo $this->lang->line('btn_save'); ?>
		</button>
	</div>
</form>
<hr class="clear" />