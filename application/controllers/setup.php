<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Controller
{

  function index()
  {
		log_message('debug', "Wizard Step 1 - select language");

		// On charge la vue qui contient le corps de la page
		$tpl['file'] = 'setup/index';

		$this->load->view('setup/template', $tpl);
  }

	// Choix de la langue
  function ajax_step2()
  {
		log_message('debug', "Wizard Step 1 - language selected");

		// Enregistrement de la langue de l'utilisateur
		$this->wizard->make_config($_POST['language']);

		// Chargement du fichier de config généré
		include APPPATH.'config/config.php';

		// Adresse de base de l'application
		$this->config->set_item('base_url', $config['base_url']);

		// Force la langue dans la configuration
		$this->config->set_item('language', $config['language']);

		// On n'a plus de besoin de cette variable
		unset($config);

		// Décharge le fichier de langue auto-chargé
		$this->lang->is_loaded = array();

		// On charge de nouveau le fichier de langue mais dans la bonne langue
		$this->lang->load('setup');

		log_message('debug', "Wizard Step 1 - jump to step 2");

		sleep(1);
		$json = array('title' => $this->lang->line('setup_welcome'),
									'step' => $this->load->view('setup/steps/_step2', '', TRUE)
								 );

		$json = json_encode($json);

		header('Content-type: application/json');
		echo $json;
		die();
  }

	// Traitement du fichier 'advancedsettings.xml'
  function ajax_step3()
  {
		log_message('debug', "Wizard Step 2 - managing 'advancedsettings.xml' file");

		// Si le fichier 'advancedsettings.xml' a bien été fourni et traité, on poursuit.
		if ($this->wizard->make_database())
		{
			log_message('debug', "Wizard Step 2 - 'advancedsettings.xml' file seems correct");
			echo '<script type="text/javascript">
			<!--
				window.top.window.UploadAdvancedSettingsEnd();
			//-->
			</script>';
		}
		else
		{
			log_message('debug', "Wizard Step 2 - 'advancedsettings.xml' file seems incorrect");
			echo '<script type="text/javascript">
			<!--
				window.top.window.UploadErrorEnd("'.$this->lang->line('error_must_redo').'");
			//-->
			</script>';
		}

		die();
  }

	// Traitement du fichier 'sources.xml'
  function ajax_step4()
  {
		log_message('debug', "Wizard Step 3 - managing 'sources.xml' file");

		// Si le fichier 'sources.xml' a bien été fourni et traité, on poursuit.
		if ($this->wizard->make_sources())
		{
			log_message('debug', "Wizard Step 3 - 'sources.xml' file seems correct");
			echo '<script type="text/javascript">
			<!--
				window.top.window.UploadSourcesEnd();
			//-->
			</script>';
		}
		else
		{
			log_message('debug', "Wizard Step 3 - 'sources.xml' file seems incorrect");
			echo '<script type="text/javascript">
			<!--
				window.top.window.UploadErrorEnd("'.$this->lang->line('error_must_redo').'");
			//-->
			</script>';
		}

		die();
  }

	// Lien symbolique pour les images et fin
  function ajax_step5()
  {
		log_message('debug', "Wizard Step 4 - managing symbolic link");

		// Enregistrement du chemin des images si présent
		if ($_POST['symbolic'] != '')
		{
			// Création du lien symbolique
			if ($this->wizard->make_symbolic($_POST['symbolic']))
			{
				// Mise à jour des routes de l'application et des objets auto-charegés
				$this->wizard->make_routes();
				$this->wizard->make_autoload();

				log_message('debug', "Wizard Step 4 - jump to step 5 and finish");
				$json = array('success' => '1',
											'step' => $this->load->view('setup/steps/_step5', '', TRUE)
										 );
			}
			else
			{
				log_message('debug', "Wizard Step 4 - symbolic link seems incorrect");
				$json = array('success' => '0',
											'message' => $this->lang->line('error_must_redo')
										 );
			}
		}
		else
		{
			log_message('debug', "Wizard Step 4 - symbolic link seems empty");
			$json = array('success' => '0',
										'message' => $this->lang->line('error_must_redo')
									 );
		}

		$json = json_encode($json);

		header('Content-type: application/json');
		echo $json;
		die();
  }

	function ajax_i_database()
	{
		log_message('debug', "Wizard Step 2 - managing new database 'MyXbmc'");

		// Chargement de la base de données 'video' car celle de 'xbmc' est inexistante
		$this->load->database('video');
		$this->load->dbforge();
		$this->load->dbutil();

		// Inclusion des informations de toutes les bases de données
		include APPPATH.'config/database.php';

		// Le nom de la base de données à créer est maintenant connu
		// Si la base de données existe déjà, ell est détruite
		if ($this->dbutil->database_exists($db['xbmc']['database']))
		{
			log_message('debug', "Wizard Step 2 - erase existing database 'MyXbmc'");
			$this->dbforge->drop_database($db['xbmc']['database']);
		}

		log_message('debug', "Wizard Step 2 - create new database 'MyXbmc'");
		// Création de la base de données
		$this->dbforge->create_database($db['xbmc']['database']);

		// On n'a plus de besoin de cette variable
		unset($db);

		sleep(1);
		$this->load->view('setup/steps/_database');
	}

	function ajax_i_users()
	{
		$this->load->database('xbmc');
		$this->load->dbforge();

		$this->dbforge->add_field("id int(11) NOT NULL AUTO_INCREMENT");
		$this->dbforge->add_field("username varchar(70) NOT NULL");
		$this->dbforge->add_field("password varchar(70) NOT NULL");
		$this->dbforge->add_field("can_change_images tinyint(1) NOT NULL");
		$this->dbforge->add_field("can_change_infos tinyint(1) NOT NULL");
		$this->dbforge->add_field("can_download_video tinyint(1) NOT NULL");
		$this->dbforge->add_field("can_download_music tinyint(1) NOT NULL");
		$this->dbforge->add_field("is_active tinyint(1) NOT NULL");
		$this->dbforge->add_field("is_admin tinyint(1) NOT NULL");
		$this->dbforge->add_key("id", TRUE);

		log_message('debug', "Wizard Step 2 - create new table 'users'");
		$this->dbforge->create_table('users');

		sleep(1);
		echo '<img src="'.base_url().'assets/gui/tick.png" />'.$this->lang->line('setup_create_users');
	}

	function ajax_i_xbmc()
	{
		$this->load->database('xbmc');

    // Chargement des modèles de la base de données 'xbmc'
    $this->load->model('xbmc/users_model');

		log_message('debug', "Wizard Step 2 - create new user witch username and password are 'xbmc'");
		// Ajout de l'utilisateur xbmc et préparation de mise à jour de ses champs
		$data = array('id' => $this->users_model->add('xbmc', 'xbmc'),
								 'is_admin' => '1',
								 'can_change_infos' => '1',
								 'can_change_images' => '1',
								 'can_download_video' => '1',
								 'can_download_music' => '1',
								 );

		// Mise à jour des champs de l'utilisateur xbmc
		$this->db->update('users', $data);

		sleep(1);
		echo '<img src="'.base_url().'assets/gui/tick.png" />'.$this->lang->line('setup_add_xbmc');
	}

	function ajax_i_sources()
	{
		$this->load->database('xbmc');
		$this->load->dbforge();

		$this->dbforge->add_field("id int(11) NOT NULL AUTO_INCREMENT");
		$this->dbforge->add_field("idPath int(11) NOT NULL");
		$this->dbforge->add_field("client_path varchar(512) DEFAULT NULL");
		$this->dbforge->add_field("server_path varchar(512) DEFAULT NULL");
		$this->dbforge->add_field("media_db text");
		$this->dbforge->add_field("content text");
		$this->dbforge->add_field("scraper text");
		$this->dbforge->add_field("settings text");
		$this->dbforge->add_key("id", TRUE);

		log_message('debug', "Wizard Step 2 - create new table 'sources'");
		$this->dbforge->create_table('sources');

		sleep(1);
		echo '<img src="'.base_url().'assets/gui/tick.png" />'.$this->lang->line('setup_create_sources');
	}

  function ajax_i_step3()
  {
		log_message('debug', "Wizard Step 2 - jump to step 3");
		sleep(1);
		$this->load->view('setup/steps/_step3');
  }

  function ajax_i_step4()
  {
		log_message('debug', "Wizard Step 3 - jump to step 4");
		sleep(1);
		$this->load->view('setup/steps/_step4');
  }

}

/* End of file setup.php */
/* Location: ./application/controllers/setup.php */
