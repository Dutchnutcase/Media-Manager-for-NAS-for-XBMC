<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = "setup";

$route['setup/language'] = "setup/ajax_step2";
$route['setup/advancedsettings'] = "setup/ajax_step3";
$route['setup/sources'] = "setup/ajax_step4";
$route['setup/symbolic'] = "setup/ajax_step5";

$route['setup/i_database'] = "setup/ajax_i_database";
$route['setup/i_users'] = "setup/ajax_i_users";
$route['setup/i_xbmc'] = "setup/ajax_i_xbmc";
$route['setup/i_sources'] = "setup/ajax_i_sources";
$route['setup/i_step3'] = "setup/ajax_i_step3";
$route['setup/i_step4'] = "setup/ajax_i_step4";

/* End of file routes.php */
/* Location: ./application/config/routes.php */
