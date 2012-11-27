<?php 
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
/**
 * Jom Comment
 * @version 2.0
 * @package JomComment
 * @copyright (C) 2008 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/

if(!defined('CMSLIB_DEFINED'))
	include_once (dirname(dirname(dirname(dirname(__FILE__)))). '/components/libraries/cmslib/spframework.php');

#Need to check if version is Joomla1.5 reason being if legacy mode is enabled $_MAMBOTS is set.
if(isset($_MAMBOTS) && cmsVersion() != _CMS_JOOMLA15 && ((cmsVersion() == _CMS_JOOMLA10) || (cmsVersion() == _CMS_MAMBO)) ){
	global $mainframe;

	if(cmsVersion() == _CMS_JOOMLA10){
		if(method_exists($mainframe, 'registerEvent')){
			$mainframe->registerEvent( 'onPrepareContent', 'jomcomment_bot_1_5' );
		}else{
            $_MAMBOTS->registerFunction('onPrepareContent', 'jomcomment_bot');
		}
	}else if(cmsVersion() == _CMS_MAMBO){
		if(method_exists($mainframe, 'registerEvent')){
			$mainframe->registerEvent( 'onPrepareContent', 'jomcomment_bot_1_5' );
		}else{
		    $_MAMBOTS->registerFunction('onPrepareContent', 'jomcomment_mambo_bot');
		}
	}
}else if(cmsVersion() == _CMS_JOOMLA15){
	global $mainframe;
	$mainframe->registerEvent('onPrepareContent','jomcomment_bot15');
}

$cms	=& cmsInstance('CMSCore');

function HTML_updateScript(){
	$jsscript = '
			<script type=\'text/javascript\'>
		    <!-- 
		    ';
	$jsscript .='
    function updateComment(){
        var comment_div = document.getElementById("jc_commentsDiv");
        var numComment = comment_div.child();
    }
		    '; $jsscript .= "//--></script> ";
}

/**
 * jomcomment -- To integrate commenting into other 3rd party components
 * use this function.
 * @param $uid: unique id of the current requested item.
 * @param $com_option: components name e.g com_content
 * @param $custom_params:
 * @param $custom_template:
 * Returns: Comments area data (HTML).
 **/
function jomcomment($uid, $com_option, $custom_params = "", $custom_template = ""){
	// Holder
	$item   = new stdClass;
	$params = new stdClass;

	// Set miscellenous properties for the 3rd party component.
	$item->id			= intval($uid);
	$item->text			= '';
	$item->sectionid    = 1024;
	$item->readmore     = false;

	if(cmsVersion() == _CMS_JOOMLA15){
		// Finally load jomcomment's bot to display the comments area.
		jomcomment_bot15($item, $params, 0, $com_option);
	} else {
		jomcomment_bot(true, $item, $params, 0, $com_option);
	}

	// Return comment area data.
	return $item->text;
}

/**
 * removeJomTags -- Remove unwanted jomcomment tags which are stored in the contents.
 * @param $text: text to be cleaned
 * Returns: text that has jomcomment tags stripped off.
 **/
function removeJomTags(& $text) { 
	$text = str_replace("{moscomment}", "", $text); 
	$text = str_replace("{!moscomment}", "", $text); 
	$text = str_replace("{jomcomment}", "", $text); 
	$text = str_replace("{!jomcomment}", "", $text); 
	$text = str_replace("{jomcomment_lock}", "", $text); 
	$text = str_replace("{jomcomment lock}", "", $text); 
	$text = preg_replace('/\{trackback\}(.*)\{\/trackback\}/', '', $text); 
	return $text;
}

function jcGetBodyScript(){
	$cms	=& cmsInstance('CMSCore');
	
	$cms->load('helper','url');

	$path	= $cms->get_path('live') . '/index2.php?option=com_jomcomment&amp;task=userinfo&amp;no_html=1';
	$script	= '<script src="' . $cms->get_path('live') . '/components/com_jomcomment' . '/script.js?' . JC_VERSION . '" type="text/javascript"></script>'. "\n";
	$script .= '<script src="' . $path . '" type="text/javascript"></script>'. "\n";
	
	return $script;
}

/**
 * jomcomment_bot15 -- jomcomment mambot for Joomla 1.5
 * @param
 * Returns:
 **/
function jomcomment_bot15(& $row,& $params, $page = 0 , $extoption = 'com_content' , $exttask = 'view'){
	global $_JC_CONFIG, $_JC_UTF8, $_JOMCOMMENT, $mainframe, $option;
	
	$cms    =& cmsInstance('CMSCore');

	#Check the view query string if its set then we get the value if not
	#then leave it as empty
	$layout = JRequest::getVar('layout', '', 'GET');
	$view 	= JRequest::getVar('view', '', 'GET');
	$option = JRequest::getVar('option', '', 'GET');

	// Check if Jom Comment is enabled. If it is not enabled, exit it.
	if (!isset($_JC_CONFIG) || !$_JC_CONFIG->get('enable')) { return; }

	// Compatibility issues with other 3rd party components and can be disabled from the back end.
	if(!isset($_JC_CONFIG) ||
		($_JC_CONFIG->get('disableFrontPage') && ($view == 'frontpage')))
		return;
		
	// Do not process the if option=com_content&view=$option
	if($option == 'com_content' && $view == 'category' && $layout != 'blog')
		return;
		
	// Define path for Azrul system mambot's
	$pcTemplatePath		= JC_COM_PATH . "/templates/";
	$pcTemplate			= $pcTemplatePath . $_JC_CONFIG->get('template');
	$pcLivePath_images	= JC_BOT_LIVEPATH . "/templates/images/";

	$paramsData        =& $params->_registry['_default']['data'];

	// Do not process mambots if its in a print view.
	$print	= JRequest::getVar('print', '', 'GET');
	
	if($print)
		return;
		
	// Check if the functions already exists.
	if(!function_exists('jcCountComment'))
	    include_once(JC_HELPER_PATH . '/minimal.helper.php');
	$cms->load('libraries','user');

	if($extoption == 'com_content'){
	    if(!isset($row->sectionid)){
	        removeJomTags($row->text);
		}

		// Do not display if current layout is blog
		$layout = JRequest::getVar('layout', '', 'GET');

		if(($view == 'frontpage') || ($view == 'category') || ($layout == 'blog') ){
			include_once ($cms->get_path('root') . "/components/com_jomcomment/main.jomcomment.php");
			include_once ($cms->get_path('root') . "/components/com_jomcomment/jomcomment.php");

			// Check if row contains catid and sectionid as category layout, the category itself is passed as 1 row
			//  and doesn't contain any catid or sectionid 
			// Test for uncategorized in joomla 1.5
			$catId = isset($row->catid) ? $row->catid : 0;
			$secId = isset($row->sectionid) ? $row->sectionid : 0; 
			if((isset($catId) && isset($secId)) || ($catId == '0' && $secId == '0' ))
			{
				showBlogView15($row, $params, false);
				removeJomTags($row->text);
				return;
			}
		}
	}

	include_once ($cms->get_path('root') . "/components/com_jomcomment/main.jomcomment.php");
	include_once ($cms->get_path('root') . "/components/com_jomcomment/jomcomment.php");
	if(($option == 'com_content') || ($extoption != 'com_content' && $exttask == 'view')){
	    $showCommentArea    = true;

		if(isset($row->sectionid) && isset($row->catid) && ($option == 'com_content')){
	        $showCommentArea    = $_JOMCOMMENT->validCategory($row->catid);
		}

		# Check if view=category in Joomla!1.5 so that Jom Comment doesnt
		# display itself during the page view of category.
		if($view == 'category'){
		    $showCommentArea    = false;
		}
		
		#Check if {!jomcomment} is present. If present dont display comment
		if(!isset ($row->sectionid) || strpos($row->text, "{!jomcomment}")) {
			$showCommentArea = false;
		}

		#Check if {jomcomment} is present. If present force display of comment area
		if (strpos($row->text, "{jomcomment}")) {
			$showCommentArea = true;
		}

		if (!$showCommentArea) {
			removeJomTags($row->text);
			return;
		}

// 		if ($_JC_CONFIG->get('autoUpdate')){
// 		    $mainframe->addCustomHeadTag(HTML_updateScript());
// 		}
		
		// Display script.js and userinfo stuffs.
		echo jcGetBodyScript();
		
		#If show comment area, then proceed the following
		$name_field    = $_JC_CONFIG->get('username');
		$user_name     = isset ($cms->user->$name_field) ? $cms->user->$name_field : "";
		$user_email    = isset ($cms->user->email) ? $cms->user->email : "";
		$busy_gif      = $cms->get_path('live') . '/' . 'components/com_jomcomment/busy.gif';
		$user_email    = str_replace('@', '+', $user_email);
		$jsscript      = "";
		$bbCodeToolbar = $_JOMCOMMENT->_viewMgr->getBBCodeToolbar(JC_BOT_LIVEPATH);
		$comments      = $jsscript . $_JOMCOMMENT->getHTML($row->id, $extoption, $row);
		$busyImgPath   = $cms->get_path('live') . "/components/com_jomcomment/busy.gif";
		$footerText    = '<br/>'.jcGetPoweredByLink();
		$footerText   .= '<script type=\'text/javascript\'> jc_loadUserInfo(); </script>';
		$footerText   .= '<img src    ="' . $busyImgPath . '" alt="busy" style="visibility:hidden;display:none;"/>';
		$footerText   .= '<!-- JOM COMMENT END -->';
		$footerText    = jcFixLiveSiteUrl($footerText);
		
		# Process mambots for trackbacks. We only process trackback about 
		# 10% of the time. No need to process it all the time
		if(rand(0, 10) < 1){
			include_once(JC_LIB_PATH . '/trackback.class.php');
			$trackback  = new JCTrackback('sent');
			$trackback->send($row);
		}
		
		$row->text .= '<!-- JOM COMMENT START -->';
		$row->text .= $comments;
		$row->text .= $footerText;
		$row->text = removeJomTags($row->text);
		
		jcSendSubscriptions();

		return;
	}
	return;
}

function jomcomment_mambo_bot($published, &$row, &$params, $page = 0) {
	global $option, $task, $mainframe, $my, $database;
	global $_JOMCOMMENT, $_JC_CONFIG, $_JC_UTF8;

	$cms    =& cmsInstance('CMSCore');

 	include_once ($cms->get_path('root') . "/components/com_jomcomment/main.jomcomment.php");
	include_once ($cms->get_path('root') . "/components/com_jomcomment/includes/mailq/mailq.php");

	#Jom comment not enabled so doesnt return any values
	if (!isset($_JC_CONFIG) && !$_JC_CONFIG->get('enable')) { return; }

	$pcTemplatePath		= JC_COM_PATH . "/templates/";
	$pcTemplate			= $pcTemplatePath . $_JC_CONFIG->get('template');
	$pcLivePath_images	= JC_BOT_LIVEPATH . "/templates/images/";
	$extoption          = 'com_content';
	$exttask            = 'view';
	
	if ($extoption == "com_content") {
		if (!isset ($row->sectionid)) {
			removeJomTags($row->text);
			return;
		}

		if (!$published) {
			removeJomTags($row->text);
			return;
		}

		if (!($option == 'com_content' AND $task == 'view')) {
			showBlogView($row, $params, false);
			removeJomTags($row->text);
			return;
		}
	}


	include_once ($cms->get_path('root') . "/components/com_jomcomment/jomcomment.php");
	if (($option == 'com_content' AND $task == 'view') OR ($extoption != 'com_content' AND $exttask == 'view')) {

		if (@ !isset ($params->_params->url) && ($extoption == "com_content")) {
			return;
		}
		$showCommentArea = true;

		if (isset ($row->sectionid) && isset ($row->catid) && ($option == 'com_content')) {
			$showCommentArea = $_JOMCOMMENT->validCategory($row->catid);
		}

		if (!isset ($row->sectionid) OR strpos($row->text, "{!jomcomment}")) {
			$showCommentArea = false;
		}
		if (strpos($row->text, "{jomcomment}")) {
			$showCommentArea = true;
		}
		if (isset ($params->_params->intro_only)) {
			if ($params->_params->intro_only) {
				$showCommentArea = false;
			}
		}
		if (!$showCommentArea) {
			removeJomTags($row->text);
			return;
		}

		if ($_JC_CONFIG->get('autoUpdate'))
			$mainframe->addCustomHeadTag(HTML_updateScript());

		$name_field = $_JC_CONFIG->get('username');
		$user_name = isset ($my-> $name_field) ? $my-> $name_field : "";
		$user_email = isset ($my->email) ? $my->email : "";
		$busy_gif = $cms->get_path('live') . '/components/com_jomcomment/busy.gif';
		$user_email = str_replace('@', '+', $user_email);
		$jsscript = "";
		$bbCodeToolbar = $_JOMCOMMENT->_viewMgr->getBBCodeToolbar(JC_BOT_LIVEPATH);
			
		$cms->load('libraries', 'cache');
		$numc = jcCountComment($row->id, $extoption);
		$language	= '';
		
		// Joom Fish integration.
		$language	= jcGetLanguage();		
		$cmt = $cms->cache->call('jcGetCommentHtml', $row->id, $extoption, $row, $numc, $language);
		$comments = $jsscript . $cmt;
			
		$busyImgPath = $cms->get_path('live') . "/components/com_jomcomment/busy.gif";
		$footerText = '<br/>'.jcGetPoweredByLink();
		$footerText .= '<script type=\'text/javascript\'> jc_loadUserInfo(); </script>';
		$footerText .= '<img src="' . $busyImgPath . '" alt="busy" style="visibility:hidden;display:none;"/>';
		$footerText .= '<!-- JOM COMMENT END -->';
		$footerText = jcFixLiveSiteUrl($footerText);

		$row->text .= '<!-- JOM COMMENT START -->';
		$row->text .= $comments;
		$row->text .= $footerText;
		$row->text = removeJomTags($row->text);
		$_JOMCOMMENT->trackbackSend($row);

		jcSendSubscriptions();
		return;
	}
	return true;
}

/**
 * Jom Comment mambot function for Joomla 1.0.x
 * Basically loads the required stuffs for Jom comment
 **/
function jomcomment_bot($published, & $row, & $params, $page = 0, $extoption = "com_content", $exttask = "view"){
	global $option, $task, $mainframe, $my, $database;
	global $_JOMCOMMENT, $_JC_CONFIG, $_JC_UTF8;
	
	$cms    =& cmsInstance('CMSCore');

	// Check if Jom Comment is enabled. If it is not enabled, exit it.
	if (!isset($_JC_CONFIG) || !$_JC_CONFIG->get('enable')) { return; }

	// Compatibility issues with other 3rd party components and can be disabled from the back end.
	if(!isset($_JC_CONFIG) ||
		($_JC_CONFIG->get('disableFrontPage') && ($option == 'com_frontpage' || $option == '')))
		return;

	// Define path for Azrul system mambot's
	$pcTemplatePath		= JC_COM_PATH . "/templates/";
	$pcTemplate			= $pcTemplatePath . $_JC_CONFIG->get('template');
	$pcLivePath_images	= JC_BOT_LIVEPATH . "/templates/images/";

	// Check if the functions already exists.
	if(!function_exists('jcCountComment'))
	    include_once(JC_HELPER_PATH . '/minimal.helper.php');

	// Check if current option is com_content.
	if ($extoption == "com_content"){
	
	    // Remove any jomcomment tags if the sectionid is not set.
		if(!isset($row->sectionid)){
			removeJomTags($row->text);
			return;
		}
		
		// Remove any jomcomment tags if its not published.
		if (!$published) {
			removeJomTags($row->text);
			return;
		}

		// Otherwise, show the comment tools
		if (!($option == 'com_content' AND $task == 'view')) {
			showBlogView($row, $params, false);
			removeJomTags($row->text);
			return;
		}
	}
	
	include_once ($cms->get_path('root') . "/components/com_jomcomment/main.jomcomment.php");
	include_once ($cms->get_path('root') . "/components/com_jomcomment/jomcomment.php");
	include_once(JC_HELPER_PATH . '/comments.helper.php');
	
	if (($option == 'com_content' AND $task == 'view') OR ($extoption != 'com_content' AND $exttask == 'view')) {
	
		// @rule: Check if user has configured to only enable members view.
		$cms->load('libraries','user');
		if($_JC_CONFIG->get('viewPermissions') == '2' && $cms->user->id == 0){
			// Get members area template to be displayed.
			$row->text	.= showMembersArea();
			return;
		}
		
		if (@ !isset ($params->_params->url) && ($extoption == "com_content")) {
			return;
		}
		
		$showCommentArea = true;
		
		if (isset ($row->sectionid) && isset ($row->catid) && ($option == 'com_content')) {
			$showCommentArea = $_JOMCOMMENT->validCategory($row->catid);
		}
		
		if (!isset ($row->sectionid) OR strpos($row->text, "{!jomcomment}")) {
			$showCommentArea = false;
		}
		
		if (strpos($row->text, "{jomcomment}")) {
			$showCommentArea = true;
		}
		if (isset ($params->_params->intro_only)) {
			if ($params->_params->intro_only) {
				$showCommentArea = false;
			}
		}
		if (!$showCommentArea) {
			removeJomTags($row->text);
			return;
		}

		if ($_JC_CONFIG->get('autoUpdate'))
			$mainframe->addCustomHeadTag(HTML_updateScript());
			
		$name_field = $_JC_CONFIG->get('username');
		$user_name = isset ($my-> $name_field) ? $my-> $name_field : "";
		$user_email = isset ($my->email) ? $my->email : "";
		$busy_gif = $cms->get_path('live') . '/' . 'components/com_jomcomment/busy.gif';
		$user_email = str_replace('@', '+', $user_email);
		$jsscript = "";
		$bbCodeToolbar = $_JOMCOMMENT->_viewMgr->getBBCodeToolbar(JC_BOT_LIVEPATH);
		
		$cms->load('libraries', 'cache');
		$cms->load('libraries', 'user');
		
		//$cmt = $_JOMCOMMENT->getHTML($row->id, $extoption, $row);
		$numc  = jcCountComment($row->id, $extoption);
		$isreg = !empty($cms->user->id) ? $cms->user->usertype : '';  
		
		$cpage = isset($_GET['cpage']) ? $_GET['cpage'] : 0;

		// Load userinfo and script.js
		echo jcGetBodyScript();
		$language	= jcGetLanguage();
		$cmt = $cms->cache->call('jcGetCommentHtml', $row->id, $extoption, $row, $numc, $isreg, $_JC_CONFIG, $cpage, $language);
		$comments = $jsscript . $cmt;  
		$busyImgPath = $cms->get_path('live') . "/components/com_jomcomment/busy.gif"; 
		$footerText = '<br/>'.jcGetPoweredByLink(); 
		$footerText .= '<script type=\'text/javascript\'> jc_loadUserInfo(); </script>'; 
		$footerText .= '<img src="' . $busyImgPath . '" alt="busy" style="visibility:hidden;display:none;"/>'; 		
		$footerText .= '<!-- JOM COMMENT END -->'; 
		$footerText = jcFixLiveSiteUrl($footerText); 

		# Process mambots for trackbacks. We only process trackback about 
		# 30% of the time. No need to process it all the time
		if(rand(0, 10) < 3){
			include_once(JC_LIB_PATH . '/trackback.class.php');
			$trackback  = new JCTrackback('sent');
			$trackback->send($row);
		}

		$row->text .= '<!-- JOM COMMENT START -->'; 
		$row->text .= $comments; 
		$row->text .= $footerText; 
		$row->text = removeJomTags($row->text);
		
		jcSendSubscriptions();
		return; 
	}
	return true; 
}

function jcSendSubscriptions()
{
	global $_JC_CONFIG;

	# Process the mailq only at 30% of the page load. Reduce unnecessary db query
	# and spread out email processing load		
	if($_JC_CONFIG->get('allowSubscription') && rand(0, 10) < 3){
		include_once (JC_COM_PATH. '/includes/mailq/mailq.php');
		$mailq = new JCMailQueue(); 
		$mailq->send(); 
	}
}

/* function : showBlogView15 (JOOMLA1.5 Usage)
 *          : adds Add Comment | Read More link in front page
 *            and blog view
 */
function showBlogView15(& $row, & $params, $showComment = true) {
	global $mainframe, $_JC_CONFIG, $Itemid , $option;

	$cms    =& cmsInstance('CMSCore');
	$cms->load('helper','url');
	$uncategorized	= false;
	
	// if the $row data do not have any id, we ignore this
	if(!isset($row->id))
		return;
	
	#Check if mambots/system/pc_includes/template.php was included.
	if(!defined('AzrulJXCachedTemplate')){
	    include_once($cms->get_path('plugins') . '/system/pc_includes/template.php');
	}

	// Need to pass by reference so that it would set the reference'd property.
	$paramsData    =& $params->_registry['_default']['data'];

	$showComment = $_JC_CONFIG->get('showCommentCount');
	$catId = isset($row->catid) ? $row->catid : 0;
	$secId = isset($row->sectionid) ? $row->sectionid : 0; 

	// Check if the categories / sections are selected to be managed by Jom Comment.
	// Otherwise, dont display add comments link.
	if ($showComment) {
		$categories		= explode(",", $_JC_CONFIG->get('categories'));
		$showComment	= !(!in_array($catId, $categories) AND !($_JC_CONFIG->get('staticContent') && ($secId == 0))) && !strpos($row->text, "{!jomcomment}");

		// Test for uncategorized in joomla 1.5
		if($_JC_CONFIG->get('staticContent') && ($secId == 0) && ($catId == 0))
		{
			$uncategorized	= true;
			$showComment	= true;
		}
	}

	# Do not add link if we are in some modules (newsflashes? perhaps)
	if (isset ($params->_params)) {
		if (array_key_exists("moduleclass_sfx", $params->_params)) {
			if (!@ $params->_params['readmore']) {
				return;
			}
		}
	}
	$iId	= $mainframe->getItemid($row->id);

	$show = array ();
	$show['readmore']	= $_JC_CONFIG->get('useReadMore');
	$show['comment']	= $showComment;
	$show['hit']		= $_JC_CONFIG->get('showHitCount');

	$link = array ();
	// Determine to use Itemid in link
	$tmpLink			= 'index.php?option=com_content&view=article&id=' . $row->slug;

	// Test if catid or section id is empty, we know its uncategorized in Joomla 1.5
	// don't need to display catid=blabla
	if(!$uncategorized)
	{
		 $tmpLink	.= '&catid=' . $row->catslug;
	}
	
	$tmpLink			.= (!empty($iId) && isset($iId)) ? '&Itemid='. $iId : '';

	$link['readmore']	= cmsSefAmpReplace($tmpLink);
	$link['comment']	= $link['readmore'] . "#comments";

	$count = array ();
	$count['comment'] 	= jcCountComment($row->id);
	$count['hit']		= jcCountContentHit($row->id);


	# If the system is set to show readmore, display it.
	if (isset ($paramsData->show_readmore)) {
		if ($paramsData->show_readmore && $_JC_CONFIG->get('useReadMore')) {
			$show['readmore'] = true;
		}
	}

	# If the system is not set to show readmore, check if Jom Comment is set
	# to show it by force
	if (isset ($paramsData->show_readmore)) {
		if (!$paramsData->show_readmore && !$_JC_CONFIG->get('useSelectiveReadMore') && $_JC_CONFIG->get('useReadMore')) {
			$show['readmore'] = true;
		}

		if ($row->fulltext == '' && $_JC_CONFIG->get('useSelectiveReadMore')) {
			$show['readmore'] = false;
		}
	}

	if ($show['readmore']) {
		$paramsData->show_readmore		= 0;
	}

	// Need to use unique id to determine which cache file to use
	// otherwise the cached content will be the same.
	$tpl = new AzrulJXCachedTemplate('frontpage_' . $_SERVER['QUERY_STRING'] . $row->id);

	$tpl->set('show', $show);
	$tpl->set('link', $link);
	$tpl->set('count', $count);
	$tpl->set('debugview', false);

	$fdata = '';

	// Cache file exists, dont need to continue the block.
	// Return here.
	if(jcBlogViewTemplate($tpl, $row, $link))
	    return;

	// Include the helper to translate template.
	include_once(JC_HELPER_PATH . '/comments.helper.php');

	// Template overrides
	if ($_JC_CONFIG->get('overrideTemplate')){
		$customTemplatePath	= JC_CUSTOM_TPL . '/readmore.tpl.html';
		
		if(file_exists($customTemplatePath))
			$fdata  = trim($tpl->fetch_cache($customTemplatePath,'jcTranslate'));
		else
			$fdata = trim($tpl->fetch_cache(JC_COM_PATH . "/templates/_default/readmore.tpl.html", 'jcTranslate'));
	} else {
		if(file_exists(JC_COM_PATH . '/templates/' . $_JC_CONFIG->get('template') . '/readmore.tpl.html')){
		    $fdata  = trim($tpl->fetch_cache(JC_COM_PATH . '/templates/' . $_JC_CONFIG->get('template') . '/readmore.tpl.html','jcTranslate'));
		}else {
			$fdata = trim($tpl->fetch_cache(JC_COM_PATH . "/templates/_default/readmore.tpl.html", 'jcTranslate'));
		}
	}

	$fdata  = str_replace('%HITS%',jcCountContentHit($row->id), $fdata);
	$fdata  = str_replace('%COMMENTS%', jcCountComment($row->id), $fdata);

	// Replace the readmore link and comment links with appropriate links.
	$fdata  = str_replace('%LINK_READMORE%', $link['readmore'], $fdata);
	$fdata  = str_replace('%LINK_COMMENT%', $link['comment'], $fdata);

	$row->text  .= $fdata;
	unset ($template);
}

function showMembersArea(){
	global $mainframe, $_JC_CONFIG;
	$cms	=& cmsInstance('CMSCore');
	
	// Include the helper to translate template.
	include_once(JC_HELPER_PATH . '/comments.helper.php');
	
	// Check if mambots/system/pc_includes/template.php was included.
	if(!defined('AzrulJXCachedTemplate'))
	    include_once($cms->get_path('plugins') . '/system/pc_includes/template.php');
	
	$template	= new AzrulJXCachedTemplate('members_' . $_SERVER['QUERY_STRING']);
	$data		= $template->fetch_cache(jcGetTemplatePath('members.tpl.html'), 'jcTranslate');
	
	return $data;
}

function showBlogView(&$row, &$params, $showComment = true){
	global $mainframe, $_JC_CONFIG, $Itemid, $option;
	$cms    =& cmsInstance('CMSCore');
	
	// Include the helper to translate template.
	include_once(JC_HELPER_PATH . '/comments.helper.php');
	
	if(@!isset($params->_params->menu_image) && ($option == 'com_content'))
	    return;

	//Check if mambots/system/pc_includes/template.php was included.
	if(!class_exists('AzrulJXCachedTemplate')){
	    include_once($cms->get_path('plugins') . '/system/pc_includes/template.php');
	}

	$language	= jcGetLanguage();
	$tpl = new AzrulJXCachedTemplate('frontpage_' . $row->id . $language);
	
	# we need to decide if we want to display the comment count or not.
	$showComment = $_JC_CONFIG->get('showCommentCount');

	if($showComment){
		$categories		= explode(",", $_JC_CONFIG->get('categories'));
		$showComment	= !(!in_array($row->catid, $categories) AND !($_JC_CONFIG->get('staticContent') && ($row->sectionid == 0))) && !strpos($row->text, "{!jomcomment}");
	}
	
	if($row->sectionid == 0){
		# no need to read-more for static content AT ALL
		return;
	}

	# Do not add link if we are in some modules (newsflashes? perhaps)
	if (isset ($params->_params)) {
		if (array_key_exists("moduleclass_sfx", $params->_params)) {
			if (!@ $params->_params['readmore']) {
				return;
			}
		}
	}
	$iId = 1;

	$iId = $mainframe->getItemid($row->id);

	if(empty($iId))
	    $iId    = $Itemid;

	// Initialize the configurations variables.
	$show = array ();
	$show['readmore']   = $_JC_CONFIG->get('useReadMore');
	$show['comment']    = $showComment;
	$show['hit']		= $_JC_CONFIG->get('showHitCount');

	$link				= array ();
	$link['readmore']	= sefRelToAbs('index.php?option=com_content&amp;task=view&amp;id=' . $row->id . '&amp;Itemid=' . $iId . '');
	$link['comment']	= $link['readmore'] . "#comments";

	$count				= array ();
	$count['comment']	= jcCountComment($row->id);
	$count['hit']		= jcCountContentHit($row->id);


	# If the system is set to show readmore, display it!
	if (isset ($row->readmore)) {
		if ($row->readmore && $_JC_CONFIG->get('useReadMore')) {
			$show['readmore'] = true;
		}
	}

	# If the system is not set to show readmore, check if Jom Comment is set
	# to show it by force
	if (isset ($row->readmore)) {
		if (!$row->readmore && !$_JC_CONFIG->get('useSelectiveReadMore') && $_JC_CONFIG->get('useReadMore')) {
			$show['readmore'] = true;
		}

		if (!$row->readmore && $_JC_CONFIG->get('useSelectiveReadMore')) {
			$show['readmore'] = false;
		}
	}

	if ($show['readmore']) {
		$row->readmore = 0;
		$params->_params->readmore = $row->readmore;
	}

	$tpl->set('show', $show);
	$tpl->set('link', $link);
	$tpl->set('count', $count);
	$tpl->set('debugview', false);
	
	$fdata = '';

	// Cache file exists, dont need to continue the block.
	// Return here.
	if(jcBlogViewTemplate($tpl, $row, $link))
	    return;


	
	// Template overrides
	if ($_JC_CONFIG->get('overrideTemplate')){
		$customTemplatePath	= JC_CUSTOM_TPL . '/readmore.tpl.html';
		
		if(file_exists($customTemplatePath))
			$fdata  = trim($tpl->fetch_cache($customTemplatePath,'jcTranslate'));
		else
			$fdata = trim($tpl->fetch_cache(JC_COM_PATH . "/templates/_default/readmore.tpl.html", 'jcTranslate'));
	} else {
		if(file_exists(JC_COM_PATH . '/templates/' . $_JC_CONFIG->get('template') . '/readmore.tpl.html')){
		    $fdata  = trim($tpl->fetch_cache(JC_COM_PATH . '/templates/' . $_JC_CONFIG->get('template') . '/readmore.tpl.html','jcTranslate'));
		}else {
			$fdata = trim($tpl->fetch_cache(JC_COM_PATH . "/templates/_default/readmore.tpl.html", 'jcTranslate'));
		}
	}
	
	$fdata  = str_replace('%HITS%',jcCountContentHit($row->id), $fdata);
	$fdata  = str_replace('%COMMENTS%', jcCountComment($row->id), $fdata);

	// Replace the readmore link and comment links with appropriate links.
	$fdata  = str_replace('%LINK_READMORE%', $link['readmore'], $fdata);
	$fdata  = str_replace('%LINK_COMMENT%', $link['comment'], $fdata);

	$row->text  .= $fdata;
	unset ($template);
}

function jcBlogViewTemplate($tpl, &$row, $link){
	global $mainframe;
	// Check if the cache file exists
	if (file_exists($tpl->cache_id)) {
		if (($mtime = filemtime($tpl->cache_id))) {
			if (($mtime + $mainframe->getCfg('cachetime')) > time()) {
				$fp = @ fopen($tpl->cache_id, 'r');
				if ($fp) {
					$filesize = filesize($tpl->cache_id);
					if ($filesize > 0) {
						$contents = fread($fp, $filesize);
						$contents  = str_replace('%HITS%',jcCountContentHit($row->id), $contents);
						$contents  = str_replace('%COMMENTS%', jcCountComment($row->id), $contents);

						// Replace the readmore link and comment links with appropriate links.
						$contents  = str_replace('%LINK_READMORE%', $link['readmore'], $contents);
						$contents  = str_replace('%LINK_COMMENT%', $link['comment'], $contents);

						$row->text .= $contents;
						return true;
					}
				}
			}
		}
	}
	
	return false;
}
