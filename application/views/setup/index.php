<div id="setup-index">
  <div class="block">
    <div class="content">
      <h2 class="title"><?php echo $this->lang->line('setup_welcome'); ?></h2>

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
							print_r($languages);
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
      </div><!-- inner -->

      <div class="inner" id="database">
				<h3 class="title"><?php echo $this->lang->line('setup_database'); ?></h3>
				<p id="info_database"></p>
				<p id="info_users"></p>
				<p id="info_xbmc"></p>
				<p id="info_sources"></p>
				<hr class="clear" />
      </div><!-- inner -->

      <div class="inner" id="step3">
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
      </div><!-- inner -->

      <div class="inner" id="step4">
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
      </div><!-- inner -->

      <div class="inner" id="step5">
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
      </div><!-- inner -->

    </div><!-- content -->
  </div><!-- block -->
</div><!-- setup-index -->
<iframe id="uploadFrame" name="uploadFrame" src="#" style="display:none;"></iframe>
