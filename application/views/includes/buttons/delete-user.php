<?php if (!isset($tabindex)) $tabindex = 1; ?>
<button id="delete-user-button" title="<?php echo $this->lang->line('btn_delete'); ?>" class="button" tabindex=<?php echo $tabindex; ?> type="button">
  <img src="<?php echo base_url(); ?>assets/gui/delete.png" />
</button>