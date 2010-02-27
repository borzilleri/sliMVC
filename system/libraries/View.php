<?php defined('SYS_ROOT') OR die('Direct script access prohibited');

class View {
	protected static $_global_data = array();
	protected $_local_data = array();
	protected $_view_file_name = null;
	
	
	public function __construct($name = null) {
		if( !is_null($name) ) {
			$this->setFile($name);
		}
	}
	
	public function setFile($name) {

		if( !is_null($name) && is_string($name) ) {
			if( file_exists(APP_ROOT.DS.'views'.DS.$name) ) {
				// Check to see if the base name exists in the App's views directory
				$this->_view_file_name = APP_ROOT.DS.'views'.DS.$name;
				return true;
			}
			elseif( file_exists(APP_ROOT.DS.'views'.DS.$name.'.php') ) {
				$this->_view_file_name = APP_ROOT.DS.'views'.DS.$name.'.php';
				return true;
			}
			elseif( file_exists(SYS_ROOT.DS.'views'.DS.$name) ) {
				$this->_view_file_name = SYS_ROOT.DS.'views'.DS.$name;
				return true;
			}
			elseif( file_exists(SYS_ROOT.DS.'views'.DS.$name.'.php') ) {
				$this->_view_file_name = SYS_ROOT.DS.'views'.DS.$name.'.php';
				return true;
			}
		}
		
		return false;
	}
	
	public function render($print = false) {
		if( empty($this->_view_file_name) ) {
			// Error
			throw new Exception('No View file set');
		}
		//TODO: Support different file types?
		
		
		$data = array_merge(View::$_global_data, $this->_local_data);
		
		$output = sliMVC::$controller->_slimvc_load_view(
			$this->_view_file_name, $data);
						
		if( true === $print ) {
			echo $output;
			return;
		}
		return $output;
	}
	
	public function __set($name, $value) {
		$this->_local_data[$name] = $value;
	}
	
	public function __get($name) {
		if( isset($this->_local_data[$name]) ) {
			return $this->_local_data[$name];
		}
		
		if( isset(View::$_global_data[$name]) ) {
			return View::$_global_data[$name];
		}
		
		if( isset($this->$name) ) {
			return $this->$name;
		}
		return $this->get($name);
	}
	
	public function __isset($name) {
		return isset($this->_local_data[$name]);
	}
	public function __unset($name) {
		unset($this->_local_data[$name]);
	}
}

?>