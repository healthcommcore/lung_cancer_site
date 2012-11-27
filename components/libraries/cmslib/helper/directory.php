<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){

	if (!function_exists('cmsGetFiles')) {
		function cmsGetFiles($path, $extension = ''){
			return mosReadDirectory($path, $extension);
		}
	}

}else{
	if (!function_exists('cmsGetFiles')) {
		function cmsGetFiles($path, $extension = ''){
			return JFolder::files($path, $extension);
		}
	}
}
