<div style="display:none;" >
	<div id="box_login">
		<div class="block">
			<h2><?php echo $this->lang->line('title_box_login'); ?></h2>
			<div class="content login">
				<form id="login-form" action="#" class="form">
					<div class="group wat-cf">
						<div class="left">
							<label class="label right"><?php echo $this->lang->line('field_username_input'); ?></label>
						</div>
						<div class="right">
							<input type="text" id="login-username" name="username" class="text_field" />
						</div>
					</div>
					<div class="group wat-cf">
						<div class="left">
							<label class="label right"><?php echo $this->lang->line('field_password_input'); ?></label>
						</div>
						<div class="right">
							<input type="password" id="login-password" name="password" class="text_field" />
						</div>
					</div>
          <div class="group navform wat-cf">
            <div class="right">
              <button class="button" type="submit">
                <img src="<?php echo base_url(); ?>assets/gui/key.png" /> <?php echo $this->lang->line('btn_login'); ?>
              </button>
            </div>
          </div>
				</form>
			</div>
		</div>
	</div>
</div>
