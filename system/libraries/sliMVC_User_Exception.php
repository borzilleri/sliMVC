<?php defined('SYS_ROOT') OR die('Direct script access prohibited');

class sliMVC_User_Exception extends sliMVC_Exception {
	
	public function __construct($title, $message, $view = false) {
		Exception::__construct($message);
		$this->code = $title;
		
		if( false !== $view ) {
			$this->view = $view;
		}
	}
}

?>