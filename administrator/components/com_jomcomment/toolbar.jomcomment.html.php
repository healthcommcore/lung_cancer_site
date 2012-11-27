<?php
/**
* @copyright (C) 2006 by Azrul Rahim - All rights reserved!
* @license http://www.azrul.com Copyrighted Commercial Software
**/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.'); 

$cms	=& cmsInstance('CMSCore');
/* CMS Compatibilities */
if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO)
	include_once($cms->get_path('root') . '/administrator/components/com_jomcomment/toolbar10.jomcomment.html.php');
else if(cmsVersion() == _CMS_JOOMLA15)
	include_once($cms->get_path('root') . '/administrator/components/com_jomcomment/toolbar15.jomcomment.html.php');
/* End CMS Compatibilities */

?>