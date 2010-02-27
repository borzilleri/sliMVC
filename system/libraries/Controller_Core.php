<?php defined('SYS_ROOT') OR die('Direct script access prohibited');

abstract class Controller_Core {
	
	public function __construct() {
		if( null == sliMVC::$controller ) {
			sliMVC::$controller = $this;
		}
	}
			
	public function __call($method, $args) {
		// TODO: generate a 404
		echo "__call method in the base controller: Controller method not found.";
	}
	
	public function _slimvc_load_view($_slimvc_file, $_slimvc_data) {
		if( '' == $_slimvc_file ) {
			return;
		}
		
		ob_start();
		extract($_slimvc_data, EXTR_SKIP);
		
		try {
			include($_slimvc_file);
		}
		catch( Exception $e ){
			ob_end_clean();
			throw $e;
		}
		
		return ob_get_clean();
	}
}
?>