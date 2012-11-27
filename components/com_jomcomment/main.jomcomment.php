<?php
/**
 * Jom Comment 
 * @package JomComment
 * @copyright (C) 2006 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
if (defined('_JC_MAINFRAME_CLASS')) {
	return;
} else {
	define('_JC_MAINFRAME_CLASS', 1);
}

global $_JOMCOMMENT, $_JC_CONFIG, $_JC_UTF8;
 
// Include our custom cmslib
if(!defined('CMSLIB_DEFINED'))
	include_once ((dirname(dirname(dirname(__FILE__)))). '/components/libraries/cmslib/spframework.php');

$cms = & cmsInstance('CMSCore');

// Include all core files
include_once ($cms->get_path('root') . '/components/com_jomcomment/defines.jomcomment.php');

// Include helper files
include_once(JC_HELPER_PATH . '/comments.helper.php');
include_once(JC_HELPER_PATH . '/minimal.helper.php');
include_once (JC_CONFIG);
include_once (JC_COM_PATH . '/class.templates.php');
include_once (JC_COM_PATH . '/class.encoding.php');
include_once (JC_COM_PATH . '/functions.jomcomment.php');


/**
 * Main controller class
 */
class JCMainFrame 
{
	var $_language = null;
	var $_utf8 = null;
	var $_viewMgr = null;
	var $_dataMgr = null;
	var $db = null;
	var $cms = null;
	var $cfg = null;
	
	/**
	 * Constructor
	 */
	function JCMainFrame() {
		global $_JC_CONFIG;
		
		include_once (JC_COM_PATH . '/datamanager.jomcomment.php');
		include_once (JC_COM_PATH . '/views.jomcomment.php');
		include_once (JC_ADMIN_COM_PATH . '/config.jomcomment.php');

		# load the config
		$this->_config = new StdClass();

		# set up utf8 object
		$this->_utf8 = new Utf8Helper();

		# set up data manager
		$this->_dataMgr = new JCDataManager();

		# set up view manager
		$this->_viewMgr = new JCView();
		
		$this->db = &cmsInstance('CMSDb');
		$this->cms = &cmsInstance('CMSCore');
		$this->cfg = $_JC_CONFIG;
		
	}
	
	function getCommentContainer() {
	}

	# Return the formatted comments list
	function getHTML($cid, $option, & $contentObj) {
		$this->cms->load('libraries', 'input');
		$page = $this->cms->input->get('cpage', 0);
		$count  = jcCountComment($cid, $option);
		
		$data = $this->_dataMgr->getAll($cid, $option,$count,$page);

		$html = $this->_viewMgr->prepAll($data, $cid, $option, $contentObj);
		
		
		unset($data);
		
		return $html;
	}
	
	function tbGetHTML($cid, $option) {
		$data = $this->_dataMgr->tbGetAll($cid, $option);
		$html = $this->_viewMgr->tbPrepAll($data, $cid, $option);
		return $html;
	}

	/**
	 * Return the name of current template.
	 * Just a special version of config.
	 */
	function getTemplate() {
		global $_JC_CONFIG;
		return $_JC_CONFIG->get('template');
	}
	function getSecurityImg($sid) {
	}

	/**
	 * Return a unique 32 character unique id
	 */
	function getSid($len = 12) {
		$token = md5(uniqid('a'));
		$sid = md5(uniqid(rand(), true));
		$ret = "";
		
		for($i = 0; $i < $len; $i++){
			$ret .= substr($sid, rand(1, ($len-1)), 1 );
		}
		
		return $ret;
	}

	/**
	 * Need to notify admin and give link to publish/unpublish the comment
	 */
	function notifyAdmin($data) {
	    global $mainframe, $_JC_CONFIG;

		$this->cms->load('helper','url');
		
		# Must make sure that the emai is valid, otherwise, do not send the
		# email				
		$email	= $_JC_CONFIG->get('notifyEmail');
		//$regexp	= "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";

		//if(!empty($email) && eregi($regexp, $email)){
			$sid	= $this->getSid();
			$date	= strftime("%Y-%m-%d %H:%M:%S", time() + ($mainframe->getCfg('offset') * 60 * 60));

			$this->db->query("DELETE FROM #__jomcomment_admin WHERE sid='$sid'");
			$this->db->query("INSERT INTO #__jomcomment_admin "
							. "\n SET sid='$sid', action='moderate', commentid='{$data->id}', date='$date'");
							// ON DUPLICATE KEY UPDATE date='$date' : removed since it only works on mysql > 4.1

			$subject = "New comment added. ";
		
			if (!$data->published)
				$subject .= "Moderator approval required.";
			
			$publishLink 	= cmsSefAmpReplace("index.php?option=com_jomcomment&task=jomadmin&sid=$sid&do=publish", false);
			$unpublishLink 	= cmsSefAmpReplace("index.php?option=com_jomcomment&task=jomadmin&sid=$sid&do=unpublish", false);
			$deleteLink 	= cmsSefAmpReplace("index.php?option=com_jomcomment&task=jomadmin&sid=$sid&do=delete", false);

			if(isset($_SERVER['HTTP_REFERER'])){
				$link = $_SERVER['HTTP_REFERER'];
			}

			// Check if the option is com_comprofiler as we do not want to send the wrong titles.
			if( $data->option == 'com_comprofiler' ){
				$contentTitle = JCView::_translateTemplate('_JC_TPL_CBMAILTITLE') . ' ' . jcGetAuthorName($data->contentid);
			} else {
				$contentTitle = jcContentTitle($data->contentid);
			}
			
			$comment 	  = jcTextwrap($data->comment);

			#Check if mambots/system/pc_includes/template.php was included.
			if(!defined('AzrulJXTemplate')){
			    include_once($this->cms->get_path('plugins') . '/system/pc_includes/template.php');
			}
			$tpl = new AzrulJXTemplate();

			if(!$data->published){
			    // Comment is not published.
			    $emailMsg	= $tpl->fetch(JC_COM_PATH . '/templates/admin/mail.approve.html');
			} else {
			    // Comment is published.
			    $emailMsg	= $tpl->fetch(JC_COM_PATH . '/templates/admin/mail.notice.html');
			}

			$emailMsg	= str_replace('%PUBLISH%', $publishLink, $emailMsg);
			$emailMsg	= str_replace('%UNPUBLISH%', $unpublishLink, $emailMsg);
			$emailMsg   = str_replace('%DELETE%', $deleteLink, $emailMsg);
			$emailMsg   = str_replace('%CONTENTTITLE%', $contentTitle, $emailMsg);
			$emailMsg   = str_replace('%COMMENTTITLE%', $data->title, $emailMsg);
			$emailMsg   = str_replace('%NAME%', $data->name, $emailMsg);
			$emailMsg   = str_replace('%EMAIL%', $data->email, $emailMsg);
			$emailMsg   = str_replace('%LINK%', $link, $emailMsg);
			$emailMsg   = str_replace('%COMMENT%', $data->comment, $emailMsg);

			// Set mail properties.
			$mode			= 0;
			$cc				= NULL;
			$bcc			= NULL;
			$attachment 	= NULL;
			$replyto 		= NULL;
			$replytoname	= NULL;

			if($data->email)
			    $replyto    = $data->email;

			if($data->name)
			    $replytoname    = $data->name;

			// Check if there is a comma in the email so we can split it and send the mails.
			$emails	= explode(',', $email);
			
			jomMail($mainframe->getCfg('mailfrom'), $mainframe->getCfg('fromname'), $emails, $subject, $emailMsg, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
		
		//}
	}

	# Test if the given section id is part of our valid sections
	# This is now, more appropriately, should be called valid category	
	function validCategory($sectionid) {
		global $_JC_CONFIG;
		$valid = true;
		
		# 1. section 1024 is all valid
		if ($sectionid == 1024)
			return true;
		
		# 2. if we don't limit on section, it's valid
		#if(!$_JC_CONFIG->get('limitSection'))
		#	return true;
		
		# 3. Check if static content needs comment as well.
		if ($_JC_CONFIG->get('staticContent') && $sectionid == 0)
			return true;
			
		# 4. If we limit on section, make sure it is there.	
		$categories = explode(",", $_JC_CONFIG->get('categories'));
		return in_array($sectionid, $categories);
	}
	
	/**
	 * Process click from admin's email
	 */
	function processAdminTask() {

	}

	/**
	 * Given the name, email and website, we simply return all of it back, with
	 * updates name and email if user is currently logged in 
	 */
	function ajaxLoadUserInfo($name, $email, $website) {
		global $_JC_CONFIG, $mainframe;
		$this->cms->load('libraries','user');

		while (@ ob_end_clean());
		ob_start();
		if (!isset ($name))
			$name = "";
		if (!isset ($email))
			$email = "";
		if (!isset ($website))
			$website = "";
		if (is_array($name))
			$name = "";
		if (is_array($email))
			$email = "";
		if (is_array($website))
			$website = "";
		if ($this->cms->user->name) {
			$name = $this->cms->user->name;
			$email = $this->cms->user->email;
			
			# name is all but in utf-8 encoding. We need to convert this to utf8
			if (function_exists('mb_convert_encoding') && @ (_ISO)) {
				$iso = explode('=', _ISO);
				$name = mb_convert_encoding($name, "UTF-8", $iso[1]);
			}
		}
		$objResponse = new JAXResponse();
		$objResponse->addAssign('jc_name', 'value', strval($name));
		$objResponse->addAssign('jc_email', 'value', strval($email));
		$objResponse->addAssign('jc_website', 'value', strval($website));
		
		// Need to load new security code picture as well
		if ($_JC_CONFIG->get('useCaptcha') AND $mainframe->getCfg('caching')) {
			$sidNew = $this->getSid();
			// Use absolute path instead
			$resultCaptchaImg   = $this->cms->get_path('live') . "/index2.php?option=com_jomcomment&no_html=1&task=img&jc_sid=$sidNew";
			$resultCaptchaSid = $sidNew;
			$objResponse->addAssign('jc_captchaImg', 'src', $resultCaptchaImg);
			$objResponse->addAssign('jc_sid', 'value', $resultCaptchaSid);
		}
		return $objResponse->sendResponse();
	}
	
	/**
	 * Some code that need to be attached to the page <head> section.
	 * There is no need to add this code if the user opt to. 	 
	 */
	function addCustomHeader() {
		global $mainframe, $option, $_JC_CONFIG;
		if (!$this->requireHeaderScript())
			return;
		$jsscript = "";
		$style = $_JC_CONFIG->get('template') . "/comment_style.css";
		if (@ strpos($style, ".html")) {
			$style = substr($_JC_CONFIG->get('template'), 0, -5) . "/comment_style.css";
		}
		$jsscript .= '<link rel="stylesheet" type="text/css" href="'.JC_COM_LIVEPATH.'/style.css"/>' . "\n";
		$jsscript .= '<link rel="stylesheet" type="text/css" href="'.JC_COM_LIVEPATH.'/templates/' . $style . '"/>' . "\n";
		//$jsscript .= $this->addCustomScript();
		$jsscript = jcFixLiveSiteUrl($jsscript);
		$mainframe->addCustomHeadTag($jsscript);
		return;
	}
	
	/**
	 * Add a new comment
	 */
	function ajaxAddComment($xajaxArgs) {
		ini_set('display_errors',1);
		error_reporting(E_ALL);
		global $_JC_CONFIG, $mainframe;
		
		$this->cms->load('libraries','user');
		$this->cms->load('libraries', 'input');
		
		include_once (JC_COM_PATH.'/spamfilter.jomcomment.php');

		$language	= jcGetLanguage();
		
		include_once(JC_LANGUAGE_PATH . '/' . $language);
		//$ob_active = ob_get_length() !== FALSE;
		
		if (ob_get_length() !== FALSE || ob_get_length() > 0) {
			while (@ ob_end_clean());
			if (function_exists('ob_clean')) {
				@ ob_clean();
			}
		}
		$view	= new JCView();

		$this->cms->load('libraries', 'user');
		ob_start();
		$emailAdmin = $_JC_CONFIG->get('notifyAdmin');
		$objResponse = new JAXResponse();
		$responseMsg = "";
	
		$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_MESSAGE_ADDED);
		$status = JC_STATUS_OK;

		# create a new comment object
		$data = $this->_dataMgr->create($xajaxArgs);

		if ($data == null) {
			# If 'create' fail, there could be some missing info or data not validated
			$resultMsg = $this->_dataMgr->getCreateError();
			$status = JC_STATUS_WARNING;
		}

		

		# apply filters
		if ($status == JC_STATUS_OK) {
			$filter = new JCSpamFilter($data, $objResponse);
			if ($filter->isSpam()) {
				$resultMsg = $filter->getErrorMsg();
				$status = JC_STATUS_WARNING;
			}
		}
		
		if (!class_exists('HTML_BBCodeParser') AND !function_exists('BBCode')) {
			include_once (JC_COM_PATH . "/bbcode.php");
		}
		
		# @rule: For com_content, do not add the comment if the content are unpublished
		if($data->option == 'com_content'){
			if(jcContentPublished($data->contentid) != 1){
				$resultMsg = "Cannot add comment to unpublished content";
				$status = JC_STATUS_WARNING;
			}
		}
		
		# @rule: hard limit on the number of comment per 30 minutes by the same IP 
		# we block it's IP automatically. and inform admin
		$numcommentByIp = $this->_dataMgr->getNumCommentByIP($data->ip, $data->date);
		if (($status == JC_STATUS_OK) AND ($numcommentByIp > 20)) {
			$_JC_CONFIG->addBlockedIP($data->ip);
			$status = JC_STATUS_WARNING;
			$resultMsg = preg_replace("/{INTERVAL}/i",$_JC_CONFIG->get('postInterval'), $this->_utf8->utf8ToHtmlEntities(_JC_TPL_REPOST_WARNING));
		}

		# @rule: block SPAM flood
		if ($_JC_CONFIG->get('postInterval') AND ($status == JC_STATUS_OK)) {
			if ($this->_dataMgr->getFlood($data->name, $data->ip, $data->date, $_JC_CONFIG->get('postInterval'))) {
				$status = JC_STATUS_WARNING;
				$resultMsg = preg_replace("/{INTERVAL}/i",$_JC_CONFIG->get('postInterval'), $this->_utf8->utf8ToHtmlEntities(_JC_TPL_REPOST_WARNING));
			}
		}

		# @rule : minimum comment length
		if ($_JC_CONFIG->get('commentMinLen') AND ($status == JC_STATUS_OK)) {
			$tmp	= BBCode($data->comment);
			$tmp	= strip_tags($tmp);
			if ($this->_utf8->strlen($tmp) < intval(trim($_JC_CONFIG->get('commentMinLen')))) {
				$status = JC_STATUS_WARNING;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_TPL_TOO_SHORT);
			}
		}

		# @rule : maximum comment length
		if (($status == JC_STATUS_OK) AND $_JC_CONFIG->get('commentMaxLen')) {
			// Strip HTML tags before checking length.


			if ($this->_utf8->strlen($data->comment) > intval($_JC_CONFIG->get('commentMaxLen'))) {
				$status = JC_STATUS_WARNING;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_TPL_TOO_LONG);
			}
		}
		
		# Check if user agreed to the terms.
		if(($status == JC_STATUS_OK) AND  $_JC_CONFIG->get('showTerms')){
		    if(!isset($data->jc_agree) || empty($data->jc_agree)){
			    $resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_TPL_TERMS_WARNING);
			    $status = JC_STATUS_WARNING;
			    $objResponse->addAssign('err_jc_agree', 'innerHTML', '*');
			} else {
				$objResponse->addAssign('err_jc_agree', 'innerHTML', '');
			}
		}

		# @rule: duplicate entry
		if (($status == JC_STATUS_OK) AND $this->_dataMgr->searchSimilarComments($data->contentid, $data->comment, $data->date)) {
			$status = JC_STATUS_WARNING;
			$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_TPL_DUPLICATE);
		}

		# @rule: match re-captcha image
		if($_JC_CONFIG->get('useRecaptcha') && ($status == JC_STATUS_OK)){
			include_once($this->cms->get_path('root') . '/components/com_jomcomment/includes/recaptcha.php');
			
			$response	= recaptcha_check_answer($_JC_CONFIG->get('recaptchaPrivateKey'),
												 $_SERVER["REMOTE_ADDR"], $data->_recaptchaChallange, 
												 $data->_recaptchaResponse);
			
			if(!$response->is_valid){
				$status		= JC_STATUS_WARNING;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_RECAPTCHA_MISMATCH);
			}
		}
		
		# @rule: password must be correct, 
		if ($_JC_CONFIG->get('useCaptcha') AND ($status == JC_STATUS_OK)) {
			$isOk = false;
			if (!$_JC_CONFIG->get('useCaptchaRegistered') AND $this->cms->user->username) {
				$isOk = true;
			} else {
				$secCode = $this->_dataMgr->getPassword($data->_sid);
				$isOk = (isset ($secCode) AND (strval($secCode) == strval($data->_password)));
				$this->_dataMgr->deletePassword($data->_sid);
			}

			if (!$isOk) {
				$status = JC_STATUS_WARNING;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_CAPTCHA_MISMATCH);
			}
		}
		# xss filter
		$data->comment = $this->cms->input->xss_clean($data->comment);
		
        // Set current page. Since this is an ajax call, we can use
		// HTTP_REFERER as this would be called from the current page.
		$data->referer  = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

		# store the new comment into database
		if ($status == JC_STATUS_OK) {
			$data->store();
		}

		#Get the list of subscribers and insert a row in the mailq table.
		if($status == JC_STATUS_OK){

			# Get all the subscriber for this particular content
			$strSQL = "SELECT * FROM #__jomcomment_subs WHERE `contentid`='{$data->contentid}'";
		    $this->cms->db->query($strSQL);
		    $rows   = $this->cms->db->get_object_list();

			$articleName    = '';

			# For content/myblog we can get the article title
			if($data->option == 'com_content' || $data->option == 'com_myblog'){
			    $strSQL = "SELECT `title` FROM #__content WHERE `id`='{$data->contentid}'";
			    $this->cms->db->query($strSQL);
			    $articleName    = $this->cms->db->get_value();
			}
			else{
			    #Unknown component
			}
			
			#Get the referer url
			$urlreferer = isset ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
			$urlreferer .= '#comment-' . $data->id;
			

			#Check if mambots/system/pc_includes/template.php was included.
			if(!defined('AzrulJXTemplate')){
			    include_once($this->cms->get_path('plugins') . '/system/pc_includes/template.php');
			}
			$tpl = new AzrulJXTemplate();

		    // Comment is not published.
		    $emailMsg	= $tpl->fetch(JC_COM_PATH . '/templates/admin/subscribe.mail.html');
			$emailMsg	= str_replace('%LINK%', $urlreferer, $emailMsg);
			$emailMsg   = str_replace('%COMMENT%', $data->comment, $emailMsg);

			if($rows){
			    #There is already subscribers
				foreach($rows as $row){
				    $insertData = array(
				                        'email'     => $row->email,
				                        'commentid'	=> $data->id,
				                        'status'    => 0,       	#not yet send?
				                        'title'     => 'Comment replied for article: ' . $articleName,
				                        'name'      => $row->name,
				                        'content'   => $emailMsg,
				                        'posted_on' => $data->date
										);
					$this->cms->db->insert('#__jomcomment_mailq',$insertData);
				}
			}else{
			    #No subscribers
			}
		}
		
		$subscribed = false;
		
		# Save in subscription table?
		if ($status == JC_STATUS_OK && ($data->_subscribe) && $this->cms->user->id){
		    #Check if user has already subscribed previously with this contentid
		    
		    $strSQL     = "SELECT COUNT(*) FROM #__jomcomment_subs "
		                . "WHERE userid='". $this->cms->user->id . "' "
						. "AND contentid='$data->contentid'";
            $this->cms->db->query($strSQL);

			if($this->cms->db->get_value() <= 0){
				#User has not subscribed before for this content
				$insertData = array('userid' 	=> $this->cms->user->id,
									'contentid' => $data->contentid,
									'option' 	=> $data->option,
									'status'    => '1',
									'email'     => $data->email,
									'name'      => $data->name
									);
				#Insert into subscription table
				$this->cms->db->insert('#__jomcomment_subs', $insertData);
				$subscribed	= true;
            }
		}

		# check if we need to unpublish it
		if ($status == JC_STATUS_OK) {

			# @unpublish rule: unpublish if necesary
			if (!$_JC_CONFIG->get('autoPublish')) {
				$this->_dataMgr->unpublish($data->id);
				$data->published = 0;
				$status = JC_STATUS_BLOCKED;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_MESSAGE_NEED_MOD);
			}
			
			# @unpublish rule: moderate guest post
			if (($status == JC_STATUS_OK) AND $_JC_CONFIG->get('modGuest') AND !$this->cms->user->username) {
				$this->_dataMgr->unpublish($data->id);
				$data->published = 0;
				$status = JC_STATUS_BLOCKED;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_MESSAGE_NEED_MOD);
				$emailAdmin = true;
			}
			
			//@todo: Need to optimize this block the for loop seems to take some time to be processed.
			# @unpublish rule: contain blocked words
			if (($status == JC_STATUS_OK) AND $_JC_CONFIG->get('blockWords')) {
				$words = explode(",", $_JC_CONFIG->get('blockWords'));
				array_walk($words, "jcTrim");
				foreach ($words as $word)
				{
					if (!empty ($word))
					{
						if( jcStrIpos( $data->comment , $word) !== false )
						{
							$this->_dataMgr->unpublish($data->id);
							$data->published = 0;
							$status = JC_STATUS_BLOCKED;
							$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_MESSAGE_NEED_MOD);
							$emailAdmin = true;
						}
					}
				}
			}
			# @unpublish rule: maximum number of link.
			# unfortunately, we need to process the comment first to be able to reliably count
			# the number of links
			$comment = $data->comment;

			$comment = BBCode($comment);
			
			# @rule : maximum comment length
			$urlCount = preg_match_all("/(a href=)/ie", $comment, $matches);
			if (intval($urlCount) > $_JC_CONFIG->get('spamMaxLink')) {
				$this->_dataMgr->unpublish($data->id);
				$data->published = 0;
				$status = JC_STATUS_BLOCKED;
				$resultMsg = $this->_utf8->utf8ToHtmlEntities(_JC_MESSAGE_NEED_MOD);
			}
			
			# send notification to admins if required
			# $_JCPROFILER->mark('Sending Email');
			if ($emailAdmin) {
				$this->notifyAdmin($data);
			}
			#$_JCPROFILER->mark('Notify Admin');
			
			# send notification to content author
			if ($_JC_CONFIG->get('notifyAuthor') && (($data->option == "com_content") || ($data->option == "com_myblog"))) {
				$auid = jcGetContentAuthor($data->contentid);
				$contentTitle = jcContentTitle($data->contentid);
				$this->db->query("SELECT email FROM #__users WHERE id=$auid");
				$authorEmail = $this->db->get_value();
				
				if(isset($_SERVER['HTTP_REFERER'])){
					$link = $_SERVER['HTTP_REFERER'];
				}

				$emailSubject = "Author notification: New comment posted";
				
				#Check if mambots/system/pc_includes/template.php was included.
				if(!defined('AzrulJXTemplate')){
				    include_once($this->cms->get_path('plugins') . '/system/pc_includes/template.php');
				}
				$tpl = new AzrulJXTemplate();

				$emailMsg   = $tpl->fetch(JC_COM_PATH . '/templates/admin/mail.author.html');

				$emailMsg   = str_replace('%CONTENTTITLE%', $contentTitle, $emailMsg);
				$emailMsg   = str_replace('%COMMENTTITLE%', $data->title, $emailMsg);
				$emailMsg   = str_replace('%COMMENT%', $data->comment, $emailMsg);

 				$mode		= 0;
				$cc			= NULL;
				$bcc 		= NULL;
				$attachment = NULL;
				$replyto 	= NULL;
				$replytoname = NULL;

				if ($data->email)
					$replyto = $data->email;
				if ($data->name)
					$replytoname = $data->name;

				jomMail($mainframe->getCfg('mailfrom'), $mainframe->getCfg('fromname'), $authorEmail, $emailSubject, $emailMsg, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
			}
			#$_JCPROFILER->mark('Notify Author');
			# for joomla 1.0.9 and above, we need to clear content cache
			# also needs to clear up caching for modules
			jcClearCache();
		}
		$sidNew = $this->getSid();
		$resultCaptchaImg   = $this->cms->get_path('live') . "/index2.php?option=com_jomcomment&no_html=1&task=img&jc_sid=$sidNew";
		
		$resultCaptchaSid = $sidNew;
		$responseMsg = '<div class="infolevel1"></div><div class="infolevel2"></div>';
		$responseMsg .= '<div class="infolevel3" id="infolevel3" style="text-align: center;">' . $resultMsg . '</div>';
		$responseMsg .= '<div class="infolevel2"></div><div class="infolevel1"></div>';
		switch ($status) {
			case JC_STATUS_OK :				
				// Need to make sure that hidden value is set to false to make sure this comment gets shown
				// Otherwise, it will be marked as 'lowly rated comment'
				$data->hidden = false;
				
				$newComment = $this->_viewMgr->getCommentsHTML($data);
				$newComment = $this->_viewMgr->_cleanUpOutput($newComment);
				$objResponse->addAssign('jc_busyDiv', 'innerHTML', $responseMsg);
				$objResponse->addScriptCall("jc_insertNewEntry", $newComment, "pc_" . $data->id);
				$objResponse->addClear('jc_comment', 'value');
				$objResponse->addClear('jc_title', 'value');
				$objResponse->addAssign('jc_numComment', 'innerHTML', $this->_dataMgr->getNumComment($data->contentid, $data->option));
				break;
			case JC_STATUS_BLOCKED :
				$objResponse->addClear('jc_title', 'value');
				$objResponse->addClear('jc_comment', 'value');
				break;
			case JC_STATUS_WARNING :
				break;
		}
		# $objResponse->addAssign('mos_profiler', 'innerHTML', $_JCPROFILER->getHTML());
		$objResponse->addAssign('jc_captchaImg', 'src', $resultCaptchaImg);
		$objResponse->addAssign('jc_sid', 'value', $resultCaptchaSid);
		$objResponse->addAssign('jc_busyDiv', 'innerHTML', $responseMsg);
		$objResponse->addClear('jc_password', 'value');
		$objResponse->addScriptCall("jc_enableForm");
		$objResponse->addScriptCall("jcOpacity", "jc_busyDiv", 0, 100);
		$objResponse->addScriptCall("jc_fadeMessage");
		
		if($_JC_CONFIG->get('useRecaptcha')){
			$objResponse->addScriptCall('Recaptcha.reload();');
		}
		# technically, the output buffering should be empty. If it is not, send a bug report
		$ob_content = ob_get_contents();
		if (!empty ($ob_content)) {
		}
		
		# clear the caching
		$this->cms->load('libraries', 'cache');
		//$this->cms->cache->clear();

		return $objResponse->sendResponse();
	}
	
	function notifySubscribers($contentid, $option, $newcomment) {
	}
	
	function jcxUpdateComment($cid, $com, $num) {
		$contentid = intval($cid);
		$com = strval($com);
		$currentNum = $this->_dataMgr->getNumComment($cid, $com);
		while (@ ob_end_clean());
		ob_start();
		$objResponse = new JAXResponse();
		if ($num != $currentNum) {
			$comments = $this->getHTML($cid, $com, 0);
			$objResponse->addAssign('jc_commentsDiv', 'innerHTML', $comments);
			$objResponse->addAssign('jc_numComment', 'innerHTML', $currentNum);
		}
		$objResponse->addScriptCall("setTimeout", "jc_update()", strval($_JC_CONFIG->get('updatePeriod')));
		return $objResponse->sendResponse();
	}
	
	function jcxUnpublish($postid, $com) {
		$this->cms->load('libraries','user');
		
		$allowedUser = array (
			'Editor',
			'Publisher',
			'Manager',
			'Administrator',
			'Super Administrator'
		);
		$isAdmin = in_array($this->cms->user->usertype, $allowedUser);
		$objResponse = new JAXResponse();
		if ($isAdmin) {
			$id = substr($postid, 3);
			$this->db->query("UPDATE #__jomcomment SET published=0 WHERE id=$id AND `option`='$com'");
			$this->db->query("SELECT contentid FROM #__jomcomment WHERE id=$id");
			$contentid = $this->db->get_value();
			$objResponse->addAssign('jc_numComment', 'innerHTML', jcCountComment($contentid, $com));
		} else {
			$objResponse->addAlert("Permission Error. You might have been logged-out.");
		}
		return $objResponse->sendResponse();
	}
	
	function jcxReport($id, $com, $referrer){
		$objResponse = new JAXResponse();
		
		if(!$this->cfg->get('allowvote')){	
			$objResponse->addAlert("Permission Denied");
			return $objResponse->sendResponse();
		}
		
		$this->cms->load('libraries', 'user');
		global $mainframe, $_JC_CONFIG;
//		include_once (JC_LANGUAGE_PATH.'/'.$this->cfg->get('language'));
	    
	    // If similar report has been dismissed, we ignore it
	    $this->db->query("SELECT COUNT(*) FROM #__jomcomment_reported WHERE `commentid`='$id'");
	    if($this->db->get_value()){
			$html = $this->_getCustomAlertHtml('', JCView::_translateTemplate('_JC_NOTIFY_ADMIN'));
			$objResponse->addScriptCall('azrulShowWindow', 'pc_'.$id, $html);
			
//	    	$objResponse->addAlert(_JC_NOTIFY_ADMIN);
	    	return $objResponse->sendResponse();
		}
	    
	    // same IP cannot report on the same commentid/option combo
	    $numreports = $this->db->get_count("#__jomcomment_reports", array( 
			"ip" =>$_SERVER['REMOTE_ADDR'], 
			"commentid" => $id,
			"option"	=> $com
			));
		
		if($numreports > 0){
			//$objResponse->addAssign('voteReport_' . $id,'innerHTML',_JC_TPL_REPORTS_DUP);
			//$objResponse->addScriptCall("jax.$('voteReport_$id').style.display = 'block';");
			//$objResponse->addAlert(_JC_TPL_REPORTS_DUP);
			$html = $this->_getCustomAlertHtml('', JCView::_translateTemplate('_JC_TPL_REPORTS_DUP'));
			$objResponse->addScriptCall('azrulShowWindow', 'pc_'.$id, $html);
			return $objResponse->sendResponse();
		}
			
	    $data = array(
			"ip" =>$_SERVER['REMOTE_ADDR'], 
			"commentid" => $id,
			"option"	=> $com,
			'user_id'	=> $this->cms->user->id
			);
		$this->db->insert("#__jomcomment_reports", $data);
		
		// Count how many report, if too many, unpublish the comment
		$numreports = $this->db->get_count("#__jomcomment_reports", array( 
			"commentid" => $id,
			"option"	=> $com));
		
		// Need to check if auto unpublish reported is disabled.
		if($numreports > $this->cfg->get('unpublishReported') && intval($this->cfg->get('unpublishReported')) != 0){
			$this->db->update('#__jomcomment', "published='0'",  array('id' => $id));

			// Alert admin that a comment has been unpublished
			$result = $this->db->query("SELECT * FROM #__jomcomment WHERE id='$id'");
			$data = $this->db->first_row();
			
			$subject	  = "A comment has been automatically unpublished as maximum reports has been reached.";
			$contentTitle = jcContentTitle($data->contentid);
			$comment 	  = jcTextwrap($data->comment);			
		
			$email_msg = "";
			$email_msg = "The following comment has been reported by site visitors and has reached the limit of " . $this->cfg->get('unpublishReported') . " reports";
		$email_msg .= "\n
	
	===========================================================================
	Content Title: $contentTitle
	===========================================================================\n
	Comment Title: $data->title
	Author: $data->name
	Email: $data->email
	
	Comment: 
	$data->comment\n
	\n
	===========================================================================\n
	\n
	\n
	[Powered by Jom Comment]\n
	";
			$mode = 0;
			$cc = NULL;
			$bcc = NULL;
			$attachment = NULL;
			$replyto = NULL;
			$replytoname = NULL;
			if ($data->email)
				$replyto = $data->email;
			if ($data->name)
				$replytoname = $data->name;
			jomMail($mainframe->getCfg('mailfrom'), $mainframe->getCfg('fromname'), $_JC_CONFIG->get('notifyEmail'), $subject, $email_msg, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
			
		}
		
		// Set response
		//$objResponse->addAssign('voteReport_' . $id,'innerHTML',_JC_NOTIFY_ADMIN);
		//$objResponse->addScriptCall("jax.$('voteReport_$id').style.display = 'block';");
		//$objResponse->addAlert(_JC_NOTIFY_ADMIN);
		$html = $this->_getCustomAlertHtml('', JCView::_translateTemplate('_JC_NOTIFY_ADMIN'));
		$objResponse->addScriptCall('azrulShowWindow', 'pc_'.$id, $html);

		return $objResponse->sendResponse();
	    
	}
	
	function jcxShowEmailThis($cid, $option){
		$objResponse = new JAXResponse();
		$uri        = $_SERVER['HTTP_REFERER'];
	
		//Check if mambots/system/pc_includes/template.php was included.
		if(!class_exists('AzrulJXTemplate')){
		    include_once($this->cms->get_path('plugins') . '/system/pc_includes/template.php');
		}
		$this->cms->load('libraries','user');
		
		$tpl	= new AzrulJXTemplate();
		$path	= jcGetTemplatePath('share.tpl.html');
		
		$tpl->set('uri', $uri);
		$tpl->set('name', $this->cms->user->name);
		$tpl->set('email', $this->cms->user->email);

		$data	= $tpl->fetch($path);
		$data	= $this->_getCustomAlertHtml('<div class="show-linkmore jcemail" style="float:left">_JC_TITLE_SHARE</div>', $data);
	
		$data	= JCView::_translateTemplate($data);
		$objResponse->addScriptCall('azrulShowWindow', 'commentTools', $data);
		
	    return $objResponse->sendResponse();
	}
	
	function jcxShowBookmarkThis($id, $option){
	    global $mainframe;
	    
		$objResponse 	= new JAXResponse();

		// Use HTTP_REFERER since the current content is the content that the user
		// would like to bookmark.
	    $uri    = urlencode($_SERVER['HTTP_REFERER']);

		$title		= ('com_myblog' == $option || 'com_content' == $option ) ? jcContentTitle($id) : 'n/a';
		$title		= urlencode($title);

		$busyimg	=  $this->cms->get_path('live') .'/components/com_jomcomment/busy.gif';

		//Check if mambots/system/pc_includes/template.php was included.
		if(!class_exists('AzrulJXTemplate')){
		    include_once($this->cms->get_path('plugins') . '/system/pc_includes/template.php');
		}
		
		$tpl	= new AzrulJXTemplate();
		$path	= jcGetTemplatePath('bookmarks.tpl.html');
		
		$tpl->set('uri', $uri);
		$tpl->set('title', $title);

		$data	= $tpl->fetch($path);
		$data	= $this->_getCustomAlertHtml('<div class="show-linkmore jcshare" style="float:left">_JC_TITLE_BOOKMARKS</div>', $data);
	
		$data	= JCView::_translateTemplate($data);
		
		$objResponse->addScriptCall('azrulShowWindow', 'commentTools', $data);
		
	    return $objResponse->sendResponse();
	}
	
	/**
	 * Ajax call to show terms and conditions
	 **/
	function jcxShowTerms(){
	    global $_JC_CONFIG;
	    $objResponse    = new JAXResponse();

		$html = $this->_getCustomAlertHtml('<div class="show-linkmore" style="float:left">_JC_TPL_TERMS_TITLE</div>',
		                                    stripslashes($_JC_CONFIG->get('termsText'))
											. '<div class="clear" style="clear:both;z-order:100"></div>');
		$html	= JCView::_translateTemplate($html);
		$objResponse->addScriptCall('azrulShowWindow', 'err_jc_agree', $html);

	    return $objResponse->sendResponse();
	}

	function jcxShowFavorites($cid, $option){
		$objResponse = new JAXResponse();		
		$html = $this->_getCustomAlertHtml('<div class="show-linkmore jcfav" style="float:left">Set as favorite</div>',
			'<form name="emailForm" id="emailForm" action="#" onsubmit="return false;" method="post">
					<table width="100%" cellpadding="3">
						<tr>
						<td><span class="smalltext">Friends email:</span></td><td align="left"><input type="text" name="recipientEmail" id="recipientEmail" class="inputbox" size="30"><span id="emailRecipientError" class="jcerr"></span></td></tr><tr><td><span class="smalltext">Your Name:</span></td>
						<td align="left"><input type="text" name="senderName" id="senderName" class="inputbox" size="30"><span id="emailNameError" class="jcerr"></span></td>
						</tr>
						
						<tr>
						<td><span class="smalltext">Your E-Mail:</span></td>
						<td align="left"><input type="text" name="senderEmail" id="senderEmail" class="inputbox" size="30"><span id="emailSenderError" class="jcerr"></span></td>
						</tr>
						
						<tr>
						<td width="35%"><span class="smalltext">Message Subject:</span></td>
						<td width="65%" align="left"><input type="text" id="senderSubject" name="senderSubject" size="35" class="inputbox"><span id="emailSubjectError" class="jcerr"></span></td>
						</tr>
						
						<tr>
						<td colspan="2" align="center">
							<input type="reset" class="button" value="Reset">&nbsp;
							<input class="button" type="button" value="Send" onclick="javascript:jax.call(\'jomcomment\', \'jcxSendEmail\', jax.$(\'recipientEmail\').value, jax.$(\'senderName\').value, jax.$(\'senderEmail\').value, jax.$(\'senderSubject\').value, \'$uri\');"></td>
						</tr>
					</table>		
				<div class="clear" style="clear:both;z-order:100"></div>
			</form>');
			
		$objResponse->addScriptCall('azrulShowWindow', 'commentTools', $html);
		
	    return $objResponse->sendResponse();
	}

	function jcxVote($status, $id, $com){
		global $mainframe;

		if(!$this->cfg->get('allowvote'))
			return;
		
		// Make sure someone didn't inject some funny vote values
		if($status > 0) $status = 1;
		if($status < 0) $status = -1;
		
//		include_once (JC_LANGUAGE_PATH.'/'.$this->cfg->get('language'));
	    $objResponse = new JAXResponse();
		
		// same IP cannot report on the same commentid/option combo
	    $numreports = $this->db->get_count("#__jomcomment_votes", array( 
			"ip" =>$_SERVER['REMOTE_ADDR'], 
			"value"=>$status,
			"commentid" => $id,
			"option"	=> $com
			));
		
		if($numreports > 0){
			$html = $this->_getCustomAlertHtml('', JCView::_translateTemplate('_JC_TPL_VOTINGS_DUP'));
			$objResponse->addScriptCall('azrulShowWindow', 'pc_'.$id, $html);
			return $objResponse->sendResponse();
		}
		
	    $voted_on = strftime("%Y-%m-%d %H:%M:%S", time() + ($mainframe->getCfg('offset') * 60 * 60));
		
		$data = array(
			"ip" =>$_SERVER['REMOTE_ADDR'], 
			"value"=>$status,
			"commentid" => $id,
			"option"	=> $com,
			'voted_on'	=> $voted_on
			);
		
		// Make sure the same IP hasn't vote on the same comment before			
		$this->db->insert("#__jomcomment_votes", $data);
		
		// Now update the overall rating value for the comment
		$this->db->update("#__jomcomment", "`voted`=`voted` + ($status)", "`id`='$id'"); 
		
		// If The vote cause the total vote to be too small or too big, clear the cache
		$this->db->query("SELECT voted FROM #__jomcomment WHERE `id`='$id'");
		$voted = $this->db->get_value();
		
		// Check if auto minimize vote is disabled
		if($voted == (-1*$this->cfg->get('minVoteCount')) && (intval($this->cfg->get('minVoteCount')) != 0)){
			
			$this->cms->load('libraries', 'cache');
			$this->cms->cache->clear();
	
	        // Clear cache so that the value of votes will be updated immediately.
			jcClearCache();
		}
		// Set response
		$html = $this->_getCustomAlertHtml('', JCView::_translateTemplate('_JC_VOTE_VOTED'));
		$objResponse->addScriptCall('azrulShowWindow', 'pc_'.$id, $html);
		
		// Update the vote count instantly
		$this->db->query("SELECT `voted` FROM #__jomcomment WHERE `id`='{$id}'");
		$objResponse->addAssign('cvote-'.$id, 'innerHTML', $this->db->get_value());
		
		// Clear the cache so that the votes will be displayed correctly when the next page reload.
		jcClearCache();
		
	    return $objResponse->sendResponse();
	}
	
	// When a report is dismissed, we should ignore all future report on the
	// same comment
	function jcxDismissReport($id){
		$objResponse = new JAXResponse();
		$this->db->query("SELECT COUNT(*) FROM #__jomcomment_reported WHERE `commentid`='$id'");
		if(!$this->db->get_value()){
			$this->db->query("INSERT INTO #__jomcomment_reported SET `commentid`='$id'");
			
			// Now delete all related reports
			$this->db->query("DELETE FROM `#__jomcomment_reports` WHERE `commentid`='$id'");
		}
		#Reload the page
		$objResponse->addScriptCall('window.location.reload();');
		return $objResponse->sendResponse();
	}
	
	function jcxEdit($postid) {
		$this->cms->load('libraries','user');
		$allowedUser = array (
			'Editor',
			'Publisher',
			'Manager',
			'Administrator',
			'Super Administrator'
		);
		$isAdmin = in_array($this->cms->user->usertype, $allowedUser);
		$objResponse = new JAXResponse();
		if ($isAdmin) {
			$id = substr($postid, 3);
			$this->db->query("SELECT comment  FROM #__jomcomment WHERE id=$postid");
			$comment = $this->db->get_value();
			$text = '<div id="pc_{id}" name="pc_{id}">
		                <form id="form-edit-{id}" name="form-edit-{id}" method="post" action="">
		                                    <label>
		                                    <textarea name="comment" rows="8" id="comment" style="width:98%">{comment}</textarea>
		                                    </label>
		                    <input name="id" type="hidden" id="id" value="{id}" />
		                                    <label>
		                                    <input name="Save" type="button" value="Save" onclick="jax.call(\'jomcomment\', \'jcxSave\', jax.getFormValues(\'form-edit-{id}\'), true);" />
		                                    </label>
		                                    <label>
		                                    <input name="Discard" type="button" value="Discard"  onclick="jax.call(\'jomcomment\',\'jcxSave\', jax.getFormValues(\'form-edit-{id}\'), false);"/>
		                                    </label>
		                </form>
		                </div>';
			$text = str_replace('{id}', $postid, $text);
			$text = str_replace('{comment}', $comment, $text);
			$objResponse->addAssign('pc_edit_' . $postid, 'innerHTML', $text);
		} else {
			$objResponse->addAlert("Permission Error. You might have been logged-out.");
		}
		return $objResponse->sendResponse();
	}
	
	// Return true if we need to add custom header to this particular page
	function requireHeaderScript() {
		global $option, $_JC_CONFIG;
		
		if($_JC_CONFIG->get('extComSupport')){
			return true;
		}
		
		return ($option == 'com_content' OR $option == 'com_myblog' OR $option=='com_frontpage');
	}

	/**
	 * Saving the data from front-end editing
	 */
	function jcxSave($xajaxArgs, $saveit) {
		global $_JC_CONFIG, $_JOMCOMMENT;
		
		$this->cms->load('libraries','user');
		$allowedUser = array (
								'Editor',
								'Publisher',
								'Manager',
								'Administrator',
								'Super Administrator'
							);

		$isAdmin = in_array($this->cms->user->usertype, $allowedUser);
		$objResponse = new JAXResponse();

		if ($isAdmin) {
			require (JC_CONFIG);
			$comment = isset ($xajaxArgs['comment']) ? $xajaxArgs['comment'] : "";
			$id = isset ($xajaxArgs['id']) ? $xajaxArgs['id'] : 0;
			
			$comment	= strip_tags( $comment , $_JC_CONFIG->get('allowedTags') );

			if (isset($saveit) && ($saveit == 'true')) {
				$this->db->query("UPDATE #__jomcomment SET comment='$comment' WHERE id=$id");

				// Format comment
				include_once(JC_COM_PATH . '/views.jomcomment.php');
				$view		= new JCView();
				
				$formattedComment	= addslashes($view->_formatComment($comment, '', false));
				$formattedComment	= $this->cms->db->_escape( $formattedComment );
				$this->db->query("UPDATE #__jomcomment SET preview='$formattedComment' WHERE id=$id");
			}

			$query		= "SELECT * FROM #__jomcomment WHERE id=$id";
			$dbResult	= $this->db->query($query);
			$item 		= $this->db->first_row();

			$this->_viewMgr->prepData($item, 0, "none", false);
			$objResponse->addAssign('comment-text-container-' . $id, 'innerHTML', $item->comment);
			$objResponse->addAssign('pc_edit_' . $id, 'innerHTML', "");
		} else {
			$objResponse->addAlert("Permission Error. You might have been logged-out.");
			return $objResponse->sendResponse();
		}
		
		# Clear the cache, otherwise it won't show after refresh
        jcClearCache();
		return $objResponse->sendResponse();
	}
	
	function jcxMyFav($cid, $option){
		$this->cms->load('libraries','user');
		$objResponse = new JAXResponse();
		
		if($this->cms->user->id != 0){
			$this->cms->load('libraries', 'user');
			$this->cms->db->query("SELECT COUNT(*) FROM #__jomcomment_fav WHERE `contentid`='$cid' AND `option`='$option' AND `userid`='{$this->cms->user->id}'");
			$ref = $_SERVER['HTTP_REFERER'];

			if($this->cms->db->get_value()){
				$html	= $this->_getCustomAlertHtml(	'<div class="show-linkmore jcfav" style="float:left">' . JCView::_translateTemplate('_JC_TITLE_FAVORITES') . '</div>',
														JCView::_translateTemplate('_JC_TPL_WARNING_FAVORITE'));
                $objResponse->addScriptCall('azrulShowWindow','commentTools',$html);
			}else {
				$data = array(	'userid' 	=> $this->cms->user->id,
								'url' 		=> $ref,
								'contentid' =>$cid,
								'option' 	=> $option );
				$this->cms->db->insert('#__jomcomment_fav', $data);

				$busyimg =  $this->cms->get_path('live') .'/components/com_jomcomment/busy.gif';
				$html	= $this->_getCustomAlertHtml(
													'<div class="show-linkmore jcfav" style="float:left">' . JCView::_translateTemplate('_JC_TITLE_FAVORITES') . '</div>',
													'<span id="favnotice">' . JCView::_translateTemplate('_JC_TPL_ADDED_FAVORITE') . '</span>'
													);
				$objResponse->addScriptCall('azrulShowWindow','commentTools',$html);
			}
		}else{
			    // Show message to user that they will need to register first.
			$html	= $this->_getCustomAlertHtml(	'<div class="show-linkmore jcfav" style="float:left">' . JCView::_translateTemplate('_JC_TITLE_FAVORITES') . '</div>',
													JCView::_translateTemplate('_JC_TPL_MEMBERS_FAV'));
            $objResponse->addScriptCall('azrulShowWindow','commentTools',$html);
		}

		return $objResponse->sendResponse();
	}
	
	function jcxRemoveFav($id){
		$objResponse = new JAXResponse();
		$strSQL = "DELETE FROM #__jomcomment_fav WHERE `id`='{$id}'";
        $this->cms->db->query($strSQL);
        $objResponse->addScriptCall('window.location.reload();');
		return $objResponse->sendResponse();
	}

	function jcxShowSendEmail(){
	    $objResponse    = new JAXResponse();

		return $objResponse->sendResponse();
	}

	/**
	 * Ajax function call (jcxSendEmail)
	 * params:  $recipient	- Receiver's email address
	 *          $name       - Sender's Name
	 *          $email      - Sender's Email
	 *          $subject    - Subject of the Email
	 *          $articleLink- Link for the current article.
	 **/
	function jcxSendEmail($recipient, $name, $email, $subject, $articleLink){
	    global $_JC_CONFIG;
	    
	    $objResponse    = new JAXResponse();
		$processForm    = true;
		
	    # Rule: Check if recipient email is valid.
	    if(!jcValidEmail($recipient)){
	        $processForm    = false;
			$objResponse->addAssign('emailRecipientError','innerHTML','*');
		}else{
		    //Previously may contain error, so we just reset it.
		    $objResponse->addAssign('emailRecipientError','innerHTML','');
		}

		# Rule: Check if sender's email is valid.
		if(!jcValidEmail($email)){
		    $processForm    = false;
		    $objResponse->addAssign('emailSenderError','innerHTML','*');
		}else{
		    $objResponse->addAssign('emailSenderError','innerHTML','');
		}
		
		# Rule: Check if name is entered.
		if(strlen($name) <= 0){
		    $objResponse->addAssign('emailNameError','innerHTML','*');
		}else{
            $objResponse->addAssign('emailNameError','innerHTML','');
		}

		# Rule: Check if subject is entered.
		if(strlen($subject) <= 0){
		    $objResponse->addAssign('emailSubjectError','innerHTML','*');
		}else{
            $objResponse->addAssign('emailSubjectError','innerHTML','');
		}
		
		# Rule: Check for spams?
		
		
		# Check if to process the form
		if($processForm){
		    $html           = "<div id=\"emailFormResult\">" . JCView::_translateTemplate('_JC_TPL_SENT_MAIL') . " <span id=\"email\">{$recipient}</span>.</div><br />";
	        $articleLlink   = urldecode($articleLink);

			#Check if mambots/system/pc_includes/template.php was included.
			if(!class_exists('AzrulJXTemplate')){
			    include_once($this->cms->get_path('plugins') . '/system/pc_includes/template.php');
			}
			$tpl = new AzrulJXTemplate();

			$emailMsg   = $tpl->fetch(JC_COM_PATH . '/templates/admin/share.mail.html');

			$emailMsg	= str_replace('%NAME%', $name, $emailMsg);
			$emailMsg	= str_replace('%SITE%', $articleLink, $emailMsg);

			// Send email
			global $mainframe;

			if(jomMail($email, $name, $recipient, $subject, $emailMsg , 0 , NULL , NULL , NULL , $email)){
			    // Sendmail success
				// Hide email form,
			    $objResponse->addAssign('dialog_body','innerHTML',$html);
			} else {
			    // Sendmail failed.
			    $html	= "<div id=\"emailFormResult\">Error while sending an email to <span id=\"email\">{$email}</span>.</div><br />";
			    $objResponse->addAssign('dialog_body','innerHTML',$html);

			}
			
		}

        return $objResponse->sendResponse();
	}

	function jcxUnsubscribe($id){
	    $objResponse    = new JAXResponse();

	    $strSQL = "DELETE FROM #__jomcomment_subs WHERE `id`='{$id}'";
	    $this->cms->db->query($strSQL);
	    $objResponse->addScriptCall('window.location.reload();');
		return $objResponse->sendResponse();
	}

	function jcxEnableSubscription($id){
	    $objResponse    = new JAXResponse();
	    $strSQL = "UPDATE #__jomcomment_subs SET `status`='1' WHERE `id`='{$id}'";
	    $this->cms->db->query($strSQL);
	    $objResponse->addScriptCall('window.location.reload();');
		return $objResponse->sendResponse();
	}

	function jcxPreviewComment($comment){
		global $_JC_CONFIG;
		
		$response	= new JAXResponse();
		
		if($_JC_CONFIG->get('commentPreview')){
			$view		= new JCView();
			
			if($comment == ''){
				$response->addScriptCall('alert("' . JCView::_translateTemplate('_JC_TPL_ERROR_PREVIEW') . '");');
			} else {
				// Just format the comment to be displayed
				$comment	= '<br />' . $view->_formatComment($comment);
				$comment	.= '<br /><br />
								<div style="text-align: right;"><button id="addcommentbutton" class="button" onclick="javascript:addComments();azrulHideWindow();">' 
							. JCView::_translateTemplate('_JC_TPL_ADDCOMMENT') . '</button></div>';
				$html = $this->_getCustomAlertHtml('<div class="show-linkmore jcpreview" style="float:left">' . JCView::_translateTemplate('_JC_TPL_HEAD_PREVIEW') . '</div>',
					$comment);
				
				$response->addScriptCall('azrulShowWindow', 'jc_commentForm', $html);
			}
		} else {
			$response->addScriptCall('alert("Not available.");');
		}
	    return $response->sendResponse();
	}
	
	function jcxDisableSubscription($id){
	    $objResponse    = new JAXResponse();
	    $strSQL = "UPDATE #__jomcomment_subs SET `status`='0' WHERE `id`='{$id}'";

	    $this->cms->db->query($strSQL);
	    $objResponse->addScriptCall('window.location.reload();');
		return $objResponse->sendResponse();
	}
	
	function jcxShowComment($id){
		$objResponse = new JAXResponse();

  		$query = "SELECT * FROM #__jomcomment WHERE id=$id";
		$dbResult = $this->db->query($query);
		$item = $this->db->first_row(); 
		
		// Need to rif the voted count so that it gets displayed
		$item->voted = 0;
		
		$newComment = $this->_viewMgr->getCommentsHTML($item);
		$newComment = $this->_viewMgr->_cleanUpOutput($newComment);
		
		//$objResponse->addAssign('pc_'.$id, 'innerHTML', $newComment);
		//$objResponse->addAlert("Permission Error. You might have been logged-out.");
		$objResponse->addScriptCall("jc_showComment", $newComment, "pc_" . $id);
		return $objResponse->sendResponse();
	}
	
	function _getCustomAlertHtml($title, $content, $style ='', $actions=''){
		$html = '<div class="dialog_header">'. $title .'<div onclick="azrulHideWindow();" class="dialog_close">[ ' . JCView::_translateTemplate('_JC_TPL_CLOSEWIN')  . ' ]</div></div>
			 <div class="dialog_content">
				<div id="dialog_body" class="dialog_body">
					'. $content .'
				</div>';
		
		if($actions){
			$html .='		
				<div id="dialog_buttons" class="dialog_buttons">
					<hr size="1" noshade="noshade"/>
					'.$actions.'
				</div>';
		}
		
		$html .='</div>';
		return $html;
		
	}
}
// Initiate some global objects
$_JC_CONFIG = new JCConfig();
$_JOMCOMMENT = new JCMainFrame();
$_JC_UTF8 = new Utf8Helper();
