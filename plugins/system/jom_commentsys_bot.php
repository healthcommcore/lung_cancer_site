<?php

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

global $mainframe;

if(!defined('CMSLIB_DEFINED'))
	include_once ((dirname(dirname(dirname(__FILE__)))). '/components/libraries/cmslib/spframework.php');

$cms =& cmsInstance('CMSCore');
include_once($cms->get_path('plugins') . "/system/pc_includes/template.php");

$sysPluginPath = $cms->get_path('root') . '/components/com_jomcomment/plugins/system.php';

if(file_exists($sysPluginPath))
	require($sysPluginPath);
