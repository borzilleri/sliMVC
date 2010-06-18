<?php defined('SYS_ROOT') OR die('Direct script access prohibited');

class Dispatcher {
	const DEFAULT_ROUTE_KEY = '_default';
	
	public static $controller = null;
	public static $method = null;
	public static $parameters = array();
	
	public static function dispatch() {
		if( is_null(self::$controller) ) {
			self::parseURI();
		}
		
		$controller_path = self::generateControllerPath();
		
		if( false === $controller_path ) {
			throw new sliMVC_404_Exception();
		}
		
		$controllerName = self::$controller;
		$controllerObj = new $controllerName();
		call_user_func_array(
			array($controllerObj, self::$method), // Object, Method
			self::$parameters // Array of parameters
		);
	}
	
	protected static function generateControllerPath() {
		$app_path = APP_ROOT.DS.'controllers'.DS.self::$controller.'.php';
		if( file_exists($app_path) ) {
			return $app_path;
		}
		
		$sys_path = SYS_ROOT.DS.'controllers'.DS.self::$controller.'.php';
		if( file_exists($sys_path) ) {
			return $sys_path;
		}
		
		return false;
	}
	
	protected static function parseURI() {
		// Load any custom routing.
		$customRoutes = sliMVC::config('routes');
		
		// Parse our URI into an array array that we can use.
		// for the requestURI we dont want the query string,
		// This is just a fast way of grabbing everything before the query string.
		$requestURI = explode('?', $_SERVER['REQUEST_URI']);
		$requestURI = explode('/', $requestURI[0]);
		$scriptName = explode('/', $_SERVER['SCRIPT_NAME']);
		
		$commandArray = array_diff_assoc($requestURI, $scriptName);
		$commandArray = array_values($commandArray);
		
		/**
		 * Build our array back into the route, using our default route if the
		 * user did not supply one (the "index" page, usually)
		 * 
		 * Then check for any custom routing rules that may be specified.
		 * This also handles our default routing scenario.
		 * 
		 * If we found one, replace our command array with the new route.
		 *
		 * TODO: Put this in a separate method, and make it more robust.
		 * (ie - regex-style replacement)
		 */
		$thisRoute = implode('/', $commandArray);
		$thisRoute = empty($thisRoute) ? self::DEFAULT_ROUTE_KEY : $thisRoute;
		if( !empty($customRoutes[$thisRoute]) ) {
			$commandArray = explode('/', $customRoutes[$thisRoute]);
		}
		foreach($customRoutes as $sourceRoute => $targetRoute) {
			if( preg_match('/'.$sourceRoute.'/', $thisRoute) ) {
				$thisRoute = preg_replace('/'.$sourceRoute.'/', $targetRoute, $thisRoute);
				break;
			}
		}
				
		// Finally, set our variables appropriately
		if( empty($commandArray[0]) ) {
			// Because of the default route handling above, this should NEVER be empty
			// If it is, toss up an exception.
			throw new sliMVC_Exception('core.no_controller');
		}
		
		self::$controller = $commandArray[0];
		self::$method = empty($commandArray[1]) ? 
			sliMVC::config('core.index_method') : $commandArray[1];
		self::$parameters = array_slice($commandArray,2);
	}
		
	public static function getCurrentRoute() {
		// TODO: Maybe add the domain name & protocol?
		
		$route = self::$controller.'/'.self::$method;
		if( !empty(self::$parameters) ) {
			$route .= '/'.implode('/', self::$parameters);
		}
		
		return $route;
	}
}

?>