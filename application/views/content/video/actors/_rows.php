<ul class="list">
	<?php
	foreach($actors as $actor)
	{
		$data['actor'] = $actor;
		$this->load->view('content/video/actors/_row', $data);
	}
	?>
</ul>