<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

if(!defined('CMSLIB_DEFINED'))
	include_once (dirname(dirname(dirname(__FILE__))). '/components/libraries/cmslib/spframework.php');

$cms  =& cmsInstance('CMSCore');
require_once($cms->get_path('root') . '/components/com_jomcomment/mambots.php');
	
