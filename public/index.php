<?php
/** 
 * This is the main front-end for your application. The Application, Web, and
 * System directories are configured here. While they should correctly set
 * themselves automaticaly, if you are having difficulties with these constants
 * you can set them here.
 */

/**
 * Set this to TRUE to set the site as being in 'Production' mode.
 * This turns of several parts of the framework, such as displaying a backtrace
 * on exceptions & php errors.
 */
define('IN_PRODUCTION', FALSE);

/**
 * Directory Separator
 * 
 * This is just a shortcut, so we don't have to put the full string everywhere
 * we want to use this. You CAN hard-code this constant, but I wouldn't
 * recommend it unless you have a particularly good reason.
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Application Directory Root
 *
 * This should be the root directory of your application. This is the directory
 * that contains your config, controllers, public, views, and possibly system
 * directory.
 */
define('APP_ROOT', dirname(dirname(__FILE__)) );

/**
 * Web Directory Root
 * 
 * This is the directory for web-accessible files. It should be the
 * 'public' directory provided.
 */
define('WEB_ROOT', APP_ROOT.DS.'public');

/**
 * sliMVC System Directory Root
 * 
 * This is the directory for sliMVC system files. By default, this is the
 * system directory provided.
 */
define('SYS_ROOT', APP_ROOT.DS.'system');

/**
 * Turning off PHP's display_errors setting will completely disable sliMVC's
 * error display. You can turn off sliMVC's errors using the
 * 'core.display_errors' config setting.
 */
ini_set('display_errors');

###                                                                          ###
###    DO NOT EDIT BELOW HERE UNLESS YOU FULLY UNDERSTAND THE IMPLICATIONS   ###
###                                                                          ###

require_once(SYS_ROOT.'/core/bootstrap.php');

?>