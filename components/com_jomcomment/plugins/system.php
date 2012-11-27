<?php

/**
* @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
* @license http://www.azrul.com Copyrighted Commercial Software
**/

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');


if(cmsVersion() == _CMS_JOOMLA10){
	$_MAMBOTS->registerFunction('onAfterStart', 'jomcommentsys_bot');
    $_MAMBOTS->registerFunction('onStart', 'jomcommentStart_bot');

}else if(cmsVersion() == _CMS_MAMBO){
    $_MAMBOTS->registerFunction('onHeaders', 'jomcommentsys_bot');

}else if(cmsVersion() == _CMS_JOOMLA15){
	$mainframe->registerEvent( 'onAfterRoute', 'jomcommentsys_bot' );
}



// We need to detect cmslib custom pagination for com_content. If The URL ends with
// cpage,[num], we extract the num part and create a global variables
// index.php?option=com_content&task=view&sectionid=1&id=2&Itemid=cpage,2
function jomcommentStart_bot(){
	$uri = $_SERVER['REQUEST_URI'];

	if(strpos($uri, 'content/view')){
		$pattern = "'cpage,(.*)'s";
		preg_match($pattern, $uri, $matches);

		if ($matches) {
			$cpage = @ $matches[1];
			$_GET['cpage'] = $cpage;

			// Rename sectionid to id and id to Itemid
			$uri = str_replace('cpage,'.$cpage, '', $uri);
			$_SERVER['REQUEST_URI'] = $uri;
		}

// 		if(isset($_GET['cpage'])){
// 			$_REQUEST['limitstart'] = -1 * $_GET['cpage'];
// 		}
	}

}

// Reposible for addding the javascript to the page header
function jomcommentsys_bot() {
	global $_JOMCOMMENT, $mainframe;

	$cms    =& cmsInstance('CMSCore');
	$cms->load('helper','url');
	
	$option = cmsGetVar('option','','GET');

	// Get the current action. If its PDF in Joomla 1.5 then dont proceed.
	if(cmsVersion() == _CMS_JOOMLA15){
		$format	= JRequest::getWord('format', 'html');

		if($format == 'pdf')
			return;
	}
	include_once ($cms->get_path('root') . '/components/com_jomcomment/defines.jomcomment.php');

	if ($option != "com_jomcomment" && !isset ($_POST['no_html'])) {

		include_once($cms->get_path('root')  . '/administrator/components/com_jomcomment/config.jomcomment.php');

		global $mainframe, $_JC_CONFIG;
		$_JC_CONFIG = new JCConfig();

		// Check if front page disabled jom comment so we dont load any css stuffs
		if($_JC_CONFIG->get('disableFrontPage') && $option=='com_frontpage'){
			return;
		}
			
		if(!$_JC_CONFIG->get('extComSupport')){
			if(!($option == 'com_content' OR $option == 'com_myblog' OR $option=='com_frontpage' OR $option == '')){
				return;
			}
		}

		$jsscript  = "";
		$jsscript .= '<link rel="stylesheet" type="text/css" href="'.$cms->get_path('live') . '/components/com_jomcomment' .'/style.css"/>' . "\n";

		$style = $_JC_CONFIG->get('template') . "/comment_style.css";
		if (@ strpos($style, ".html")) {
			$style = substr($_JC_CONFIG->get('template'), 0, -5) . "/comment_style.css";
		}

		if($_JC_CONFIG->get('overrideTemplate')){
			// Get the css
			$customTemplateStyle 		= $cms->get_path('root') . '/templates/' . $mainframe->getTemplate() . '/com_jomcomment/comment_style.css';

			if(file_exists($customTemplateStyle))
				$jsscript .= '<link rel="stylesheet" type="text/css" href="'.$cms->get_path('live') . '/templates/' . $mainframe->getTemplate() . '/com_jomcomment/comment_style.css"/>' . "\n";
			else			
				$jsscript .= '<link rel="stylesheet" type="text/css" href="'.$cms->get_path('live') . '/components/com_jomcomment' .'/templates/' . $style . '"/>' . "\n";
		} else {
			$jsscript .= '<link rel="stylesheet" type="text/css" href="'.$cms->get_path('live') . '/components/com_jomcomment' .'/templates/' . $style . '"/>' . "\n";
		}
		$jsscript .= jcAddCustomScript();
		$jsscript  = jcFixHTTPSSiteUrl($jsscript);

		$mainframe->addCustomHeadTag($jsscript);
	}
}

function jcAddCustomScript() {
	global $option, $_JC_CONFIG, $mainframe;

	$cms    =& cmsInstance('CMSCore');

	$name_field = $_JC_CONFIG->get('username');
	$busy_gif = $cms->get_path('live') . '/components/com_jomcomment/busy.gif';
	$jsscript = "";
	$jsscript .= '
<script type=\'text/javascript\'>
/*<![CDATA[*/
var jc_option           = "' . $option . '";
var jc_autoUpdate       = "' . $_JC_CONFIG->get('autoUpdate') . '";
var jc_update_period    = ' .  $_JC_CONFIG->get('updatePeriod') . '*1000;
var jc_orderBy          = "' . $_JC_CONFIG->get('sortBy') . '";
var jc_livesite_busyImg = "' . $busy_gif . '";
var jc_username         = "";
var jc_email            = "";
var jc_commentForm;
/*]]>*/
</script>' . "\n";
	
// 	$jsPath = cmsSefAmpReplace('index.php?option=com_jomcomment&amp;task=userinfo&amp;no_html=1');
// 	$jsscript .= '<script src="' . $cms->get_path('live') . '/components/com_jomcomment' . '/script.js?" type="text/javascript"></script>'. "\n";
// 	$jsscript .= '<script src="' . $jsPath . '" type="text/javascript"></script>'. "\n";
	return $jsscript;
}


/**
 * Basically, we need to add the HTTPS support if required.
 */
function jcFixHTTPSSiteUrl($content){

	//global
	$cms =& cmsInstance('CMSCore');
	$live_site = $cms->get_path('live');

	$reqURI   = $live_site;

	# if host have wwww, but mosConfig doesn't
	if((substr_count($_SERVER['HTTP_HOST'], "www.") != 0) && (substr_count($reqURI, "www.") == 0)) {
		$reqURI = str_replace("://", "://www.", $reqURI);

	} else if((substr_count($_SERVER['HTTP_HOST'], "www.") == 0) && (substr_count($reqURI, "www.") != 0)) {
		// host do not have 'www' but mosConfig does
		$reqURI = str_replace("www.", "", $reqURI);
	}

	/* Check for HTTPS */
	if(isset($_SERVER)){
		if(isset($_SERVER['HTTPS'])){
			if(strtolower($_SERVER['HTTPS']) == "on" ){
				$reqURI = str_replace("http://", "https://", $reqURI);
			}
		}
	} else if(isset($_SERVER['REQUEST_URI'])) {
		// use REQUEST_URI method
		if(strpos($_SERVER['REQUEST_URI'], 'https://') === FALSE){
			// not a https
		} else {
			$reqURI = str_replace("http://", "https://", $reqURI);
		}
	}

	return str_replace($live_site, $reqURI, $content);
}
