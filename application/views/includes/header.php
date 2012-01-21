<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" />
  <title><?php echo $title; ?></title>
  <?php echo link_tag('http://fonts.googleapis.com/css?family=Oswald'); ?>
  <?php echo link_tag('assets/styles/fancybox/jquery.fancybox-1.3.4.css'); ?>
  <?php echo link_tag('assets/styles/jquery.jgrowl.css'); ?>
  <?php echo link_tag('assets/styles/base.css'); ?>
  <?php echo link_tag('assets/styles/style.css'); ?>
  <script src="<?php echo base_url(); ?>assets/scripts/jquery.min.js" language="javascript" type="text/javascript"></script>
  <script src="<?php echo base_url(); ?>assets/scripts/jquery.fancybox-1.3.4.js" language="javascript" type="text/javascript"></script>
  <script src="<?php echo base_url(); ?>assets/scripts/jquery.jeditable.js" language="javascript" type="text/javascript"></script>
  <script src="<?php echo base_url(); ?>assets/scripts/jquery.jgrowl.js" language="javascript" type="text/javascript"></script>
  <script src="<?php echo base_url(); ?>assets/scripts/xbmc.js" language="javascript" type="text/javascript"></script>
  <script src="<?php echo base_url(); ?>assets/scripts/jquery.dragsort-0.4.3.min.js" language="javascript" type="text/javascript"></script>
  <script language="javascript" type="text/javascript">
  site_url = '<?php echo base_url(); ?>';
  btn_save = '<?php echo $this->lang->line('btn_save'); ?>';
  btn_or = '<?php echo $this->lang->line('btn_or'); ?>';
  btn_cancel = '<?php echo $this->lang->line('btn_cancel'); ?>';
  btn_clic_edit = '<?php echo $this->lang->line('btn_clic_edit'); ?>';
  msg_bad_data = '<?php echo $this->lang->line('msg_bad_data'); ?>';
  msg_confirm_delete = '<?php echo $this->lang->line('msg_confirm_delete'); ?>';
  </script>
	<?php if ($this->session->flashdata('result') != ''): ?>
		<script type="text/javascript">
		<!--
		$(document).ready(function() {	
			$.jGrowl("<?php echo $this->session->flashdata('result') ?>");	
		});
		//-->				
		</script>
	<?php endif; ?>
</head>
<body>
  <div id="container">
    <div id="header">
      <h1><?php echo $this->lang->line('site_name'); ?></h1>
			<?php $this->load->view('includes/user'); ?>
			<?php $this->load->view('includes/menu'); ?>
		</div><!-- end header -->
    <div id="wrapper" class="wat-cf">
