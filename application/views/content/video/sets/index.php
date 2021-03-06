<?php
//echo '<pre>'.print_r($sets, true).'</pre>';
?>
<?php if ($this->session->userdata('can_change_infos')): ?>
<script src="<?php echo base_url(); ?>assets/scripts/sets.js" language="javascript" type="text/javascript"></script>
<?php endif; ?>
<div id="sets-index">
  <div class="block">
    <div class="content">
      <h2><?php echo $title; ?></h2>
      <div class="inner">
				<?php
				if (count($sets) > 0)
				{
					$data['sets'] = $sets;
					$this->load->view('content/video/sets/_rows', $data);
				}
				else
				{
					echo '<h4>'.$this->lang->line('list_no_set').'</h4>';
				}
				?>
        <hr class="clear" />
				<div id="actions-bar" class="actions-bar wat-cf">
					<div class="actions">
						<?php
						if ($this->session->userdata('can_change_infos'))
								$this->load->view('includes/buttons/add-set');
						?>
					</div>
					<?php echo $this->my_pagination->create_links(); ?>
				</div><!-- actions-bar -->
      </div><!-- inner -->
      <div id="add-set-form" class="inner">
        <?php $this->load->view('content/video/sets/_add-form'); ?>
      </div><!-- inner -->
    </div><!-- content -->
  </div><!-- block -->
</div><!-- sets-list -->