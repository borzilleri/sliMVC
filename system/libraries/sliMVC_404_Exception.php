<?php defined('SYS_ROOT') OR die('Direct script access prohibited');

class sliMVC_404_Exception extends sliMVC_Exception {
	protected $code = E_SLIMVC_404;
	
	public function __construct($page = false, $view = false) {
		if( false === $page ) {
			$page = Dispatcher::getCurrentRoute();
		}		
		
		Exception::__construct(sliMVC::lang('core.page_not_found',$page));
		
		if( false !== $view ) {
			$this->view = $view;
		}
	}
	
	public function sendHeaders() {
		header('HTTP/1.1 404 File Not Found');
	}
}

?>