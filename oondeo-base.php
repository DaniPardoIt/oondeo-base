<?php

/**
 * Plugin Name: Oondeo Base
 * Plugin URI: https://oondeo.com
 * Description: Plugin con la configuración básica de Oondeo
 * Version: 1.0.0
 * Author: Oondeo
 * Author URI: https://oondeo.com
 */


define('ROOTPATH', str_replace('/web/', '/', ABSPATH));


$oo_conf = array(
	'log_level' => 2,
	'log_folder' => ROOTPATH . "/private/web_log/" . date('Y-m') . "/",
	'error_log' => true,
);


//* IMPORT
$plugin_path = plugin_dir_path(__FILE__);
// error_log("\nplugin_path: $plugin_path\n");

include_once $plugin_path . "functions/get-posts.php";
include_once $plugin_path . "functions/users.php";
include_once $plugin_path . "functions/custom-posts-grid.php";
include_once $plugin_path . "functions/woocommerce-functions.php";
//! IMPORT


//* ENQUEUE SCRIPTS
function oondeo_base_enqueue_scripts()
{
	wp_enqueue_style('bootstrap css', "https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css", array(), 20141119);
	wp_enqueue_script('bootstrap js', "https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js", array('jquery'), '20120206', true);

	//oondeo
	wp_enqueue_script('oondeo-base-js', plugin_dir_url(__FILE__) . 'assets/js/oondeo-base.js');
	wp_enqueue_script('modal-js', plugin_dir_url(__FILE__) . 'assets/js/modal.js');
	wp_enqueue_script('notifications-js', plugin_dir_url(__FILE__) . 'assets/js/notifications.js');
	wp_enqueue_script('posts-js', plugin_dir_url(__FILE__) . 'assets/js/posts.js');
}
add_action('wp_enqueue_scripts', 'oondeo_base_enqueue_scripts');
//! ENQUEUE SCRIPTS


//* URL FUNCTIONS
function get_full_url()
{
	$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	return $actual_link;
}

//! URL FUNCTIONS


//* FUNCIÓN DOCUMENTACIÓN
if (!function_exists('document_info')) {
	/**
	 * Document the information in a file.
	 *
	 * @param string $file The file to document.
	 * @param string $description The description of the information.
	 * @param mixed $info The information to document.
	 * @param bool $append Whether to append to the file or overwrite it.
	 * @return void
	 */
	function document_info($file, $description, $info, $append = false)
	{
		global $oo_conf;
		$log_folder = $oo_conf['log_folder'];
		// error_log(
		// 	"\nLog Folder: $log_folder\n"
		// );
		if (!file_exists($log_folder)) {
			mkdir($log_folder, 0755, true);
		}
		$file = $log_folder . $file;

		$append ? $FILE_APPEND = FILE_APPEND : $FILE_APPEND = 0;

		$now = DateTime::createFromFormat('U.u', microtime(TRUE));

		while (!$now) {
			$now = DateTime::createFromFormat('U.u', microtime(TRUE));
		}

		$now = $now->format('Y/m/d H:i:s.u');

		$text = <<<TEXT
		
		!-- $now --!
		$description
		
		TEXT;

		if (is_array($info) || is_object($info)) {
			file_put_contents($file, $text, $FILE_APPEND);
			file_put_contents($file, print_r($info, true), FILE_APPEND);
		} else {
			$text .= <<<TEXT
			$info
	
			TEXT;

			file_put_contents($file, $text, $FILE_APPEND);
		}
	}
}
//! FIN FUNCIÓN DOCUMENTACIÓN

//* RANDOM PASSWORD
function randomPassword($passLength = 10, $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-_.:,;?!|@#$%&()=')
{
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < $passLength; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
}
//! FIN RANDOM PASSWORD


//* FUNCIONES USUARIOS, ROLES Y FUNCIONES
function user_have_role($role, $user = null)
{
	if (!$user) {
		$user = wp_get_current_user();
	}
	$info_path = basename(__FILE__, '.php') . '.php->' . __FUNCTION__ . '.txt';
	document_info($info_path, "Role", $role);
	document_info($info_path, "User", $user);

	if (in_array($role, (array)$user->roles)) {
		return true;
	} else {
		return false;
	}
}

function user_is_admin()
{
	return user_have_role('administrator');
}
//! FUNCIONES USUARIOS, ROLES Y FUNCIONES
