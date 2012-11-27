<?php

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

// Include our custom cmslib
if(!defined('CMSLIB_DEFINED'))
	include_once ((dirname(dirname(dirname(__FILE__)))). '/components/libraries/cmslib/spframework.php');


if(defined('JC_COM_PATH'))
	return;

$cms = & cmsInstance('CMSCore');
// Define essentials path 
define('JC_COM_PATH', 		dirname(__FILE__));
define('JC_TASK_PATH',		JC_COM_PATH . '/task');
define('JC_MODEL_PATH',		JC_COM_PATH . '/model');
define('JC_LIB_PATH',		JC_COM_PATH . '/libraries');
define('JC_HELPER_PATH',	JC_COM_PATH . '/helper');
define('JC_CACHE_PATH',		$cms->get_path('root') . '/components/libraries/cmslib/cache');
define('JC_BOT_LIVEPATH', 	$cms->get_path('live') . '/components/com_jomcomment');
define('JC_COM_LIVEPATH', 	$cms->get_path('live') . '/components/com_jomcomment');
define('JC_ADMIN_LIVEPATH', $cms->get_path('live') . '/administrator/components/com_jomcomment');
define('JC_LANGUAGE_PATH', 	JC_COM_PATH . '/languages');
define('JC_ADMIN_COM_PATH', dirname(dirname(JC_COM_PATH)) . '/administrator/components/com_jomcomment');
define('JC_CONFIG', 		JC_ADMIN_COM_PATH . '/config.jomcomment.php');

global $mainframe;
define('JC_CUSTOM_TPL', $cms->get_path('root') . '/templates/' . $mainframe->getTemplate() . '/com_jomcomment');
// Some debugging/development consigurations
define('JC_STATUS_BLOCKED', '2');
define('JC_STATUS_OK', '1');
define('JC_STATUS_WARNING', '0');
define('JC_DEBUG', '0');
define('JC_VERSION', '1.8.9');


?>
