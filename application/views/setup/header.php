<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" />
  <title><?php echo $this->lang->line('setup_welcome'); ?></title>
  <?php echo link_tag('http://fonts.googleapis.com/css?family=Oswald'); ?>
  <?php echo link_tag('assets/styles/fancybox/jquery.fancybox-1.3.4.css'); ?>
  <?php echo link_tag('assets/styles/jquery.jgrowl.css'); ?>
  <?php echo link_tag('assets/styles/base.css'); ?>
  <?php echo link_tag('assets/styles/style.css'); ?>
  <?php echo link_tag('assets/styles/setup.css'); ?>
  <script src="<?php echo base_url(); ?>assets/scripts/jquery.min.js" language="javascript" type="text/javascript"></script>
  <script src="<?php echo base_url(); ?>assets/scripts/jquery.fancybox-1.3.4.js" language="javascript" type="text/javascript"></script>
  <script src="<?php echo base_url(); ?>assets/scripts/jquery.jgrowl.js" language="javascript" type="text/javascript"></script>
  <script src="<?php echo base_url(); ?>assets/scripts/setup.js" language="javascript" type="text/javascript"></script>
  <script language="javascript" type="text/javascript">
  site_url = '<?php echo base_url(); ?>';
  </script>
</head>
<body>
  <div id="container">
    <div id="header">
      <h1><?php echo $this->lang->line('site_name'); ?></h1>
		</div><!-- end header -->
    <div id="wrapper" class="wat-cf">
