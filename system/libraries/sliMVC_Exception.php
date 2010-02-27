<?php defined('SYS_ROOT') OR die('Direct script access prohibited');

class sliMVC_Exception extends Exception {
	protected $view = 'slimvc_error_page';
	protected $code = E_SLIMVC;
	
	public function __construct($error) {
		$args = array_slice(func_get_args(), 1);
		
		$message = sliMVC::lang($error, $args);
		
		if( empty($message) || $error === $message ) {
			$message = 'Unknown Exception: '.$error;
		}
		
		parent::__construct($message);
	}
	
	public function sendHeaders() {
		header('HTTP/1.1 500 Internal Server Error');
	}
	
	public function getView() {
		return $this->view;
	}

}

?>