<li id="search">
	<?php
		$action = base_url();
		if (isset($this->uri->segments[1])) $action .= $this->uri->segments[1].'/';
		$action .= "pre_search";
	?>
	<form action="<?php echo $action; ?>" method="post">
		<input type="text" class="text_field" name="query" />
		<?php $this->load->view('includes/buttons/search'); ?>
	</form>
</li>
