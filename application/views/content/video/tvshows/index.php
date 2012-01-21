<div id="tvshows-index">
  <div class="block">
    <div class="content">
			<?php
				$data['title'] = $title;
				$data['tvshows'] = $tvshows;
				$this->load->view('content/video/tvshows/_rows', $data);
			?>
    </div><!-- content -->
  </div><!-- block -->
</div><!-- media-list -->
