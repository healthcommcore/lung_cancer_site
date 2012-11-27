<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){

	// All Joomla 1.0 Functions
	if(!function_exists('cmsGetVar')){
		function cmsGetVar($var, $default='', $type = 'REQUEST'){
			switch($type){
				case 'REQUEST':
					$type = &$_REQUEST;
					break;
				case 'GET' :
					$type = &$_GET;
					break;
				case 'POST' :
					$type = &$_POST;
					break;
				case 'FILES' :
					$type = &$_FILES;
					break;
				case 'COOKIE' :
					$type = &$_COOKIE;
					break;
				case 'ENV'    :
					$type = &$_ENV;
					break;
				case 'SERVER'    :
					$type = &$_SERVER;
					break;
			}
			return mosGetParam($type, $var, $default);
		}
	}

	if(!function_exists('cmsSefAmpReplace')){
		function cmsSefAmpReplace($url, $xhtml = true){
		    if(!function_exists('sefRelToAbs')){
		        global $mainframe;
		        include_once($mainframe->getCfg('absolute_path') . '/includes/sef.php');
			}
			
			if(!$xhtml)
			    return sefRelToAbs($url);
			else
				return ampReplace(sefReltoAbs($url));
		}
	}
	
	if(!function_exists('cmsFormatDate')){
		function cmsFormatDate($date, $format = null, $offset = null){
			return mosFormatDate($date, $format, $offset);		
		}
	}
	
	if(!function_exists('cmsGetISO')){
		function cmsGetISO(){
			$iso = explode( '=', _ISO);
			return $iso[1];
		}
	}
	
	if (!function_exists('cmsRedirect')) {
		function cmsRedirect($url, $msg){
			mosRedirect($url, $msg);
		}
	}

}else{

	// All Joomla 1.5 Functions
	if(!function_exists('cmsGetVar')){
		function cmsGetVar($var, $default='', $type = 'REQUEST'){
			return JRequest::getVar($var, $default, $type);
		}
	}
	
	if(!function_exists('cmsSefAmpReplace')){
		function cmsSefAmpReplace($url, $xhtml = true){
		
			if(!$xhtml){
			    $url    = JRoute::_($url, false);
			}
			else{
		    	$url	= JFilterOutput::ampReplace(JRoute::_($url));
		    	//echo $url;exit;
		    }

			// JRoute does not return http://absolute.com/ we need to get it from
			// JURI::base()
			if(stristr($url, 'http://') === false || stristr($url, 'https://') === false){
				/**
				 * Subfolder fix
				 *				 
				 * If site is on a subfolder, JRoute returns /subfolder/some/sef/url
				 * and JURI::base returns http://site.com/subfolder/
				 * We need to remove the subfolder.				 				 				 
				 **/			 			
				// Get subfolder
				
				// Gets subfolder if exists and explicitly search for '/' since some subfolder
				// may contain index and it might remove the index from index.php
				$subfolder = JURI::base(true) . '/';

				$site = rtrim(JURI::base(), '/'); // Gets site absolute path
				
				if($subfolder && $subfolder != '/'){
					// Remove subfolder from $url.
					$url = str_replace($subfolder, '', $url);
				}
				/**
				 * End subfolder fix
				 **/
				
				// Remove any '/' at the most left of the URL to prevent duplicates.
				$url	= ltrim($url, '/');
				$url	= $site . '/' . $url;
			}
			return $url;
		}
	}
	
	if(!function_exists('cmsFormatDate')){
		function cmsFormatDate($date, $format = null, $offset = null){
			return JHTML::date($date, $format, $offset);		
		}
	}
	
	if(!function_exists('cmsGetISO')){
		function cmsGetISO(){
			$iso = 'UTF-8';
			return $iso;
		}
	}
	
	if (!function_exists('cmsRedirect')) {
		function cmsRedirect($url, $msg){
			global $mainframe; 
			$mainframe->redirect($url, $msg);
		}
	}
		
}
