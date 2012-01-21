<div id="setup-index">
  <div class="block">
    <div class="content">
      <h2 class="title" id="title"><?php echo $this->lang->line('setup_welcome'); ?></h2>

      <div class="inner" id="step1">
				<h3 class="title"><?php echo $this->lang->line('setup_step1'); ?></h3>
				<form id="language_form" action="#" class="form">
					<div class="group">
						<label class="label"><?php echo $this->lang->line('field_languages'); ?></label>
						<select id="language" name="language">
							<?php
							$languages = glob(FCPATH.APPPATH.'language/*', GLOB_ONLYDIR);
							foreach($languages as $language)
							{
								echo '<option value="'.str_replace(FCPATH.APPPATH.'language/', '', $language).'">'.ucfirst(str_replace(FCPATH.APPPATH.'language/', '', $language))."</option>\n";
							}
							?>
						</select>
					</div>
					<div class="group navform wat-cf">
						<button class="button_language" type="submit">
							<img src="<?php echo base_url(); ?>assets/gui/tick.png" /> <?php echo $this->lang->line('btn_save'); ?>
						</button>
					</div>
				</form>
				<hr class="clear" />
      </div><!-- inner -->

      <div class="inner" id="step2">
      </div><!-- inner -->

      <div class="inner" id="database">
      </div><!-- inner -->

      <div class="inner" id="step3">
      </div><!-- inner -->

      <div class="inner" id="step4">
      </div><!-- inner -->

      <div class="inner" id="step5">
      </div><!-- inner -->

    </div><!-- content -->
  </div><!-- block -->
</div><!-- setup-index -->
<iframe id="uploadFrame" name="uploadFrame" src="#" style="display:none;"></iframe>
