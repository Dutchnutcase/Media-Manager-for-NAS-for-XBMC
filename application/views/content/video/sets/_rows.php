<ul class="list">
	<?php
	foreach($sets as $set)
	{
		$data['set'] = $set;
		$this->load->view('content/video/sets/_row', $data);
	}
	?>
</ul>