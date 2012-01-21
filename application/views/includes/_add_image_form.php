<div style="display:none;" >
	<div id="box_add_<?php echo $type; ?>">
		<div class="block">
			<h2><?php echo $this->lang->line('title_add_'.$type); ?></h2>
			<div class="content">
				<form id="add_<?php echo $type; ?>_form" enctype="multipart/form-data" action="<?php echo base_url() ?>images/upload" target="uploadFrame" method="post" class="form">
					<div class="group wat-cf">
						<label class="label"><?php echo $this->lang->line('field_add_'.$type.'_input'); ?></label>
						<input id="real_file_input_<?php echo $type; ?>" type="file" name="image" class="transparent_field" />
						<input type="hidden"  name="image_filename"  value="<?php echo $image_filename; ?>">
						<input type="hidden"  name="type"  value="<?php echo $type; ?>">
						<input id="fake_file_input_<?php echo $type; ?>" type="text" size="20" class="file_field" />
						<button id="browse_button_<?php echo $type; ?>" class="button" type="button">
							<img src="<?php echo base_url(); ?>assets/gui/browse.png" /> <?php echo $this->lang->line('btn_browse'); ?>
						</button>
					</div>
          <div class="group navform wat-cf">
            <div class="right">
              <button class="button" type="submit">
                <img src="<?php echo base_url(); ?>assets/gui/tick.png" /> <?php echo $this->lang->line('btn_save'); ?>
              </button>
            </div>
          </div>
				</form>
				<iframe id="uploadFrame" name="uploadFrame" src="#" style="display:none;"></iframe>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {

	// L'appui sur le bouton déclenche le choix d'un fichier
  $("#browse_button_<?php echo $type; ?>").click(function(){
		$('#real_file_input_<?php echo $type; ?>').click();
  });
  
  $('#real_file_input_<?php echo $type; ?>').change(function() {
    $('#fake_file_input_<?php echo $type; ?>').val($(this).val());
	});

});
</script>
