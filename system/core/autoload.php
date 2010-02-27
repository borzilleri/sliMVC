<?php defined('SYS_ROOT') OR die('Direct script access prohibited');

/**
 * Attempt to include a library class.
 *
 * @param $lib The library class name to include.
 * @return bool
 */
function libraryAutoLoad($lib) {
	$result = true;

	// Check for the class in the app's library dir
	$app_path = APP_ROOT."/libraries/{$lib}.php";
	if( file_exists($app_path) ) {
		$result = include_once($app_path);
	}
	
	if( true === $result ) {
		// Check for the class in the system library dir.
		$system_path = SYS_ROOT."/libraries/{$lib}.php";
		if( file_exists($system_path) ) {
			include_once($system_path);
		}
	}	
}
spl_autoload_register('libraryAutoLoad');

function controllerAutoLoad($controller) {
	$result = true;

	$app_path = APP_ROOT."/controllers/{$controller}.php";
	if( file_exists($app_path) ) {
		$result = include_once($app_path);
	}
	
	if( true === $result ) {
		$system_path = SYS_ROOT."/controllers/{$controller}.php";
		if( file_exists($system_path) ) {
			include_once($system_path);
		}
	}
}
spl_autoload_register('controllerAutoLoad');

?>