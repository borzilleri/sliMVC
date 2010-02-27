<?php defined('SYS_ROOT') OR die('Direct script access prohibited');

final class sliMVC {
	public static $controller;
	private static $configuration;
	private static $messages;
		
	public static function config($key) {
		if( null === self::$configuration ) {
			// Load core config.
			self::$configuration['core'] = self::config_load('core');
		}
		
		// Generate a config group name from the key
		$group = explode('.',$key,2);
		$group = $group[0];
		
		if( !isset(self::$configuration[$group]) ) {
			self::$configuration[$group] = self::config_load($group);
		}
		
		$value = self::key_string(self::$configuration, $key);
		
		return $value;
	}
	
	public static function config_load($name) {
		$configuration = array();

		if( 'core' === $name ) {
			require(SYS_ROOT.DS.'config'.DS.'core.php');
			$configuration = array_merge($configuration, $config);
			
			if( file_exists(APP_ROOT.DS.'config'.DS.'core.php') ) {
				require(APP_ROOT.DS.'config'.DS.'core.php');
				if( isset($config) && is_array($config) ) {
					$configuration = array_merge($configuration, $config);
				}
			}
			return $configuration;
		}
		
		if( file_exists(SYS_ROOT.DS.'config'.DS.$name.'.php') ) {
			require(SYS_ROOT.DS.'config'.DS.$name.'.php');
			if( isset($config) && is_array($config) ) {
				$configuration = array_merge($configuration, $config);
			}
		}

		if( file_exists(APP_ROOT.DS.'config'.DS.$name.'.php') ) {
			require(APP_ROOT.DS.'config'.DS.$name.'.php');
			if( isset($config) && is_array($config) ) {
				$configuration = array_merge($configuration, $config);
			}
		}
		
		return $configuration;
	}
	
	public static function lang($key, $args = array()) {
		$group = explode('.', $key, 2);
		$group = $group[0];
		
		if( !isset(self::$messages[$group]) ) {
			self::$messages[$group] = self::language_load($group);
		}
		$line = self::key_string(self::$messages, $key);
		
		if( is_null($line) ) {
			// No Language Entry for this key!
			// Just default to the actual key string.
			return $key;
		}
		
		if( is_string($line) && func_num_args() > 1 ) {
			// Perform any string replacement necessary.
			$args = array_slice(func_get_args(), 1);			
			$line = vsprintf($line, is_array($args[0]) ? $args[0] : $args);
		}
		
		return $line;
	}
	
	public static function language_load($name) {
		$locale = self::config('core.language');
		$messages = array();
		
		if( file_exists(SYS_ROOT.DS.'language'.DS.$locale.DS.$name.'.php') ) {
			include(SYS_ROOT.DS.'language'.DS.$locale.DS.$name.'.php');
			if( !empty($lang) && is_array($lang) ) {
				$messages = $lang;
			}
		}
		
		if( file_exists(APP_ROOT.DS.'language'.DS.$locale.DS.$name.'.php') ) {
			include(APP_ROOT.DS.'language'.DS.$locale.DS.$name.'.php');
			if( !empty($lang) && is_array($lang) ) {
				foreach($lang as $k => $v) {
					$messages[$k] = $v;
				}
			}
		}
		
		return $messages;
	}
	
	public static function key_string($array, $keys) {
		if( empty($array) ) {
			return null;
		}
		
		$keys = explode('.',$keys);
		
		do {
			$key = array_shift($keys);
			if( isset($array[$key]) ) {				
				if( is_array($array[$key]) && !empty($keys) ) {
					$array = $array[$key];
				}
				else {
					return $array[$key];
				}
			}
			else {
				break;
			}
		} while( !empty($keys) );
		
		return null;
	}
	
	/**
	 *
	 */
	public static function exception_handler(
		$exception, $message = null, $file = null, $line = null) {
		
		try {
			// PHP Errors always have 5 parameters.
			$PHP_ERROR = (5===func_num_args());
		
			// CHeck to see if we should be displaying this error.
			if( $PHP_ERROR && 0===(error_reporting() & $exception) ) return;
		
			if( $PHP_ERROR ) {
				$code = $exception;
				$type = 'PHP Error';
			}
			else {
				$code = $exception->getCode();
				$type = get_class($exception);
				$message = $exception->getMessage();
				$file = $exception->getFile();
				$line = $exception->getLine();
			}
			
			if( is_numeric($code) ) {
				$codes = sliMVC::lang('errors');
				if( !empty($codes[$code]) ) {
					$error = $codes[$code];
				}
				else {
					$error = $PHP_ERROR ? 'Unknown Error' : get_class($exception);
				}
			}
			else {
				$error = $code;
			}
			
			// Strip the application root path from the file path.
			$file = str_replace('\\', '/', realpath($file));
			$file = preg_replace('|^'.preg_quote(APP_ROOT).'|', '', $file);
		
			// Send headers, if necessary
			if( $PHP_ERROR ) {
				if( !headers_sent() ) {
					header('HTTP/1.1 500 Internal Server Error');
				}
			}
			else {
				if( method_exists($exception, 'sendHeaders') && !headers_sent() ) {
					$exception->sendHeaders();
				}
			}
			
			if( true === self::config('core.display_errors') ) {
				if( !IN_PRODUCTION && false != $line ) {
					// generate trace
					$trace = $PHP_ERROR ? 
						array_slice(debug_backtrace(),1) : $exception->getTrace();
					$trace = self::formatBacktrace($trace);
				}
				
				include(SYS_ROOT.DS.'views'.DS.'slimvc_error_page.php');
			}
			else {
				include(SYS_ROOT.DS.'views'.DS.'slimvc_error_page_disabled.php');
			}
			
			error_reporting(0);
			exit;
		}
		catch( Exception $e ) {
			if( IN_PRODUCTION ) {
				die('Fatal Error');
			}
			else {
				die(sprintf('Fatal Error: %s \nFile: %s \nLine: %s', 
					$e->getMessage(), $e->getFile(), $e->getLine()));
			}
		}
	}
	
	public static function formatBacktrace($trace) {
		if( !is_array($trace) ) return;
		
		$out = array();
		
		$i = 0;
		foreach($trace as $entry) {
			$line = '<li class="row'.$i.'">';
			$i = ($i+1)%2;
			
			if( isset($entry['file']) ) {
				$line .= sprintf('<tt>%s<strong>[%s]:</strong></tt>', 
					preg_replace('!^'.preg_quote(APP_ROOT).'!', '', $entry['file']),
					$entry['line']);
			}
			
			$line .= '<code class="block">';
			
			if( isset($entry['class']) ) {
				$line .= $entry['class'].$entry['type'];
			}
			
			$line .= $entry['function'].'( ';
			
			if( isset($entry['args']) && is_array($entry['args']) ) {
				$sep = '';
				while( $arg = array_shift($entry['args']) ) {
					if( is_string($arg) && is_file($arg) ) {
						$arg = preg_replace('!^'.preg_quote(APP_ROOT).'!','',$arg);
					}
					
					$line .= $sep.htmlspecialchars(print_r($arg,true));
					$sep = ', ';
				}
			}
			
			$line .= ' )</code></li>';
			$out[] = $line;
		}
		
		return '<ul class="backtrace">'.implode("\n", $out).'</ul>';
	}
}

?>