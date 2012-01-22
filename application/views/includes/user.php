<?php
//echo '<pre>'.print_r($this->lang, TRUE).'</pre>'; die();
?>
<div id="user-navigation">
  <ul class="wat-cf">
    <?php
    // Si un utilisateur est connecté
    if ($this->session->userdata('user_id'))
    {
      echo '<li id="user_welcome">'.sprintf($this->lang->line('user_welcome'), $this->session->userdata('username')).'</li>';
      echo '<li id="login_out"><a class="logout" href="'.site_url('users/logout').'">'.$this->lang->line('user_logout').'</a></li>';

			// Si l'utilisateur est sur l'administration, afficher lien vers site
			if ($this->session->userdata('in_admin'))
			{
				echo '<li id="extra_link"><a href="'.base_url().'">'.$this->lang->line('user_site').'</a></li>';
			}

			// Si l'utilisateur n'est PAS sur l'administration (donc sur le site), afficher lien vers l'administration
			if (!$this->session->userdata('in_admin'))
			{
				echo '<li id="extra_link"><a href="'.site_url('admin').'">'.$this->lang->line('user_admin').'</a></li>';
			}

    }
    else
    {
      echo '<li id="user_welcome"></li>';
      echo '<li id="login_out"><a id="login" href="#box_login">'.$this->lang->line('user_login').'</a></li>';
      echo '<li id="extra_link"></li>';
    }

    ?>
  </ul>
</div>
