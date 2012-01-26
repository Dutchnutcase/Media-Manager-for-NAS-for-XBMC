<div id="movies-index">
  <div class="block">
    <div class="content">
      <h2 class="title"><?php echo $this->lang->line('list_last_movies'); ?></h2>
      <div class="inner">
				<?php
				if (count($last_movies) > 0)
				{
					$data['movies'] = $last_movies;;
					$this->load->view('content/video/movies/_rows', $data);
				}
				else
				{
					echo '<h4>'.$this->lang->line('list_no_movie').'</h4>';
				}
				?>
        <hr class="clear" />
      </div><!-- inner -->
    </div><!-- content -->
  </div><!-- block -->
</div><!-- movies-index -->

<div id="tvshows-index">
  <div class="block">
    <div class="content">
			<h2 class="title"><?php echo $this->lang->line('list_last_tvshows'); ?></h2>
			<div class="inner">
				<?php
				if (count($last_tvshows) > 0)
				{
					$data['tvshows'] = $last_tvshows;
					$this->load->view('content/video/tvshows/_rows', $data);
				}
				else
				{
					echo '<h4>'.$this->lang->line('list_no_tvshow').'</h4>';
				}
				?>
				<hr class="clear" />
			</div><!-- inner -->
    </div><!-- content -->
  </div><!-- block -->
</div><!-- media-list -->

<div id="episodes-index">
  <div class="block">
    <div class="content">
			<?php
			if (count($last_episodes) > 0)
			{
				$data['title'] = $this->lang->line('list_last_episodes');

				// Cette variable non vide permet d'affiche le nom de la série pour chaque épisode
				$data['tvshow_name'] = ' ';

				$data['episodes'] = $last_episodes;
				$this->load->view('content/video/episodes/_rows', $data);
			}
			else
			{
				echo '<h2>'.$this->lang->line('list_last_episodes').'</h2>';
				echo '<div class="inner">';
				echo '  <ul class="list">';
				echo '		<h4>'.$this->lang->line('list_no_tvshow').'</h4>';
				echo '  </ul>';
				echo '  <hr class="clear" />';
				echo '</div><!-- inner -->';
			}
			?>
    </div><!-- content -->
  </div><!-- block -->
</div><!-- episodes-index -->