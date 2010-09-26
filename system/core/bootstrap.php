<?php defined('SYS_ROOT') OR die('Direct script access prohibited');

// Include & Register autoload Functions
require_once(SYS_ROOT.'/core/constants.php');
require_once(SYS_ROOT.'/core/autoload.php');
require_once(SYS_ROOT.'/core/sliMVC.php');

set_error_handler(array('sliMVC', 'exception_handler'));
set_exception_handler(array('sliMVC', 'exception_handler'));

sliMVC::bootstrap();
Dispatcher::dispatch();

?>