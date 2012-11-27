<?php
ob_start();
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

define('JOMCOMMENT_DEFAULT_LIMIT', 30);

if(!defined('CMSLIB_DEFINED'))
	include_once (dirname(dirname(dirname(dirname(__FILE__)))). '/components/libraries/cmslib/spframework.php');

global $task, $_JC_CONFIG;
global $jcPatchClass;
$jcPatchClass = array ();

$cms =& cmsInstance('CMSCore');

// Load helper for directories
$cms->load('helper','directory');
$cms->load('helper','url');

require_once ($cms->get_path('root') . "/administrator/components/com_jomcomment/class.jomcomment.php");
require_once ($cms->get_path('root') . "/administrator/components/com_jomcomment/admin.jomcomment.html.php");
require_once ($cms->get_path('root') . "/components/com_jomcomment/jomcomment.php");
include_once ($cms->get_path('plugins') . "/system/pc_includes/ajax.php");
include_once ($cms->get_path('plugins') . "/system/pc_includes/template.php");
require_once ($cms->get_path('root') . "/administrator/components/com_jomcomment/patch.jomcomment.php");

/* CMS Compatibilities */
if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO)
	$patchFiles = mosReadDirectory($cms->get_path('root') . "/administrator/components/com_jomcomment/patch", "php");
else if(cmsVersion() == _CMS_JOOMLA15){
    $patchFiles = JFolder::folders($cms->get_path('root') . "/administrator/components/com_jomcomment/patch", "php");
    
    #Import Joomla 1.5 libraries
    jimport('joomla.cache.cache');
}
/* End CMS Compatibilities */

foreach ($patchFiles as $p) {
	#Some ISP include php.ini in every subfolders
	#Need to check if its a php.ini file
	if($p != 'php.ini')
		require_once ($cms->get_path('root') . "/administrator/components/com_jomcomment/patch/" . $p);
}

$cid    = cmsGetVar('cid', 0, 'POST');
$task   = cmsGetVar('task', '', 'POST');

if (empty ($task))
	$task   = cmsGetVar('task','comments', 'GET');


$option = "com_jomcomment";
switch ($task) {
	case 'xajax' :
		break;
	case 'exportEmail' :
		jcExportEmail();
		break;
	case 'hacks' :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showAvailableHacks();
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
	case "import" :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		importComments($option);
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
	case "latestnews" :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showLatestNews();
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
	case "save" :
		saveComment($option);
		break;
	case "maintd" :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showMaintd();
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
	case "remove" :
		removeComments($cid, $option);
		break;
	case "publish" :
		publishComments($cid, 1, $option, $task);
		break;
	case "unpublish" :
		publishComments($cid, 0, $option, $task);
		break;
	case "publish_tb" :
		publishTrackbacks($cid, 1, $option);
		break;
	case "unpublish_tb" :
		publishTrackbacks($cid, 0, $option);
		break;
	case "remove_tb" :
		removeTrackbacks($cid, $option);
		break;
	case 'publish_reports':
		publishComments($cid, 1, $option, 'reports');
		break;
	case 'unpublish_reports':
		publishComments($cid, 0, $option, 'reports');
		break;
	case 'dismiss_reports':
		dismissReports($cid, $option);
	case "config" :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showConfig($option);
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
	case "editLanguage" :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		editLanguage("xxx");
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
	case "savesettings" :
		saveConfig($option);
		break;
	case "savelanguagesettings":
		saveLanguageConfig($option);
		break;
	case "stats" :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showStatistics();
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
	case "about" :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showAbout();
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
	case "support" :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showSupport();
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
	case "license" :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showLicense();
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
	case "trackbacks" :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showTrackbacks($option);
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
		
	case "reports" :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showReports($option);
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
	
	case 'language':
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showLanguageConfig($option);
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
		
	case "comments" :
	default :
		ob_start();
		showAjaxedAdmin();
		$panel = ob_get_contents();
		ob_end_clean();
		ob_start();
		showComments($option);
		$content = ob_get_contents();
		ob_end_clean();
		$content = str_replace("{CONTENT}", $content, $panel);
		echo $content;
		break;
}

function dismissReports($cid, $option){
	$cms    =& cmsInstance('CMSCore');

	if (!is_array($cid) || count($cid) < 1) {
		echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
		exit;
	}
	$cids	= implode(',', $cid);

	// If data is not an array, format it to be an array so that it would be
	// much easier to be processed.
	if(!is_array($cids))
		$cids	= array($cids);
		
	foreach($cids as $id){
		$strSQL	= "SELECT COUNT(*) FROM #__jomcomment_reported WHERE `commentid`='{$id}'";
		$cms->db->query($strSQL);
		
		if(!$cms->db->get_value())
			$cms->db->query("INSERT INTO #__jomcomment_reported SET `commentid`='{$id}'");
			
		$cms->db->query("DELETE FROM #__jomcomment_reports WHERE `commentid`='{$id}'");
	}
	# Clear the cache, otherwise it won't show after refresh
	jcClearCache();
	
	cmsRedirect("index2.php?option=$option&task=reports");
}

function transformDbText ($source) {
	// if mbstring is available, use it instead
	
	 
    // array used to figure what number to decrement from character order value
    // according to number of characters used to map unicode to ascii by utf-8
    $decrement[4] = 240;
    $decrement[3] = 224;
    $decrement[2] = 192;
    $decrement[1] = 0;
    
    // the number of bits to shift each charNum by
    $shift[1][0] = 0;
    $shift[2][0] = 6;
    $shift[2][1] = 0;
    $shift[3][0] = 12;
    $shift[3][1] = 6;
    $shift[3][2] = 0;
    $shift[4][0] = 18;
    $shift[4][1] = 12;
    $shift[4][2] = 6;
    $shift[4][3] = 0;
    
    $pos = 0;
    $len = strlen ($source);
    $encodedString = '';
    while ($pos < $len) {
        $asciiPos = ord (substr ($source, $pos, 1));
        
        // we must skip standard ascii cahracter from being unicode encoded!
        if($asciiPos > 31 && $asciiPos <= 127){
            $encodedString .= substr ($source, $pos, 1);
            $pos++;
        } 
        else 
        {
        
       
       if (($asciiPos >= 240) && ($asciiPos <= 255)) {
           // 4 chars representing one unicode character
           $thisLetter = substr ($source, $pos, 4);
           $pos += 4;
       }
       else if (($asciiPos >= 224) && ($asciiPos <= 239)) {
           // 3 chars representing one unicode character
           $thisLetter = substr ($source, $pos, 3);
           $pos += 3;
       }
       else if (($asciiPos >= 192) && ($asciiPos <= 223)) {
           // 2 chars representing one unicode character
           $thisLetter = substr ($source, $pos, 2);
           $pos += 2;
       }
       else {
           // 1 char (lower ascii)
           $thisLetter = substr ($source, $pos, 1);
           $pos += 1;
       }
    
       // process the string representing the letter to a unicode entity
       $thisLen = strlen ($thisLetter);
       $thisPos = 0;
       $decimalCode = 0;
       while ($thisPos < $thisLen) {
           $thisCharOrd = ord (substr ($thisLetter, $thisPos, 1));
           if ($thisPos == 0) {
               $charNum = intval ($thisCharOrd - $decrement[$thisLen]);
               $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
           }
           else {
               $charNum = intval ($thisCharOrd - 128);
               $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
           }
    
           $thisPos++;
       }
    
       if ($thisLen == 1)
           $encodedLetter = "&#". str_pad($decimalCode, 3, "0", STR_PAD_LEFT) . ';';
       else
           $encodedLetter = "&#". str_pad($decimalCode, 5, "0", STR_PAD_LEFT) . ';';
    
       $encodedString .= $encodedLetter;      
       
       }
    }
    return $encodedString;
}


function editLanguage() {
	$cms =& cmsInstance('CMSCore');
	$cms->load('helper','directory');

	$languages  = cmsGetFiles($cms->get_path('root') . '/components/com_jomcomment/languages', '.php');

	$options    = '';
	
	foreach($languages as $language){
	    $options    .= '<option value="' . $language . '">' . $language . '</option>';
	}
	HTML_comment :: showLanguageEdit($options);
}

/**
 * Patch com_content/content.php for pagination support 
 */ 
function jcxPatchContent(){
	
	$objResponse	= new JAXResponse();
	$cms    =& cmsInstance('CMSCore');
	include_once(JC_COM_PATH . '/helper/system.hacks.php');
	$patchok = jcPatchContentPagination();
	
	if($patchok){
		$objResponse->addAlert('Patch applied successfully');
		$objResponse->addAssign('paginationPatchAction','innerHTML', '<input type="button" name="Submit"  class="CommonTextButtonSmall" value="Restore com_content file" onClick="javascript:void(0);jax.call(\'jomcomment\',\'jcxRestorePatchedContent\');"/>');
	} else {
		$objResponse->addAlert('File already patched');
	}
	return $objResponse->sendResponse();
}

/**
 * Restore old com_content hacks
 */ 
function jcxRestorePatchedContent(){
	
	$objResponse	= new JAXResponse();
	$cms    =& cmsInstance('CMSCore');
	include_once(JC_COM_PATH . '/helper/system.hacks.php');
	if(jcCheckContentPagination()){
		jcRestoreContentPagination();
		$objResponse->addAssign('paginationPatchAction', 'innerHTML','<input type="button" name="Submit"  class="CommonTextButtonSmall" value="Patch com_content" onClick="javascript:void(0);jax.call(\'jomcomment\',\'jcxPatchContent\');"/>');
	}

	$objResponse->addAlert('Backup loaded successfully');
	return $objResponse->sendResponse();
}

/*
 * Patch Artio's file so that we can include our custom pagination
 */
function jcxPatchArtio(){
	#Include our own libraries
	$cms    =& cmsInstance('CMSCore');

	while (@ ob_end_clean());
	ob_start();

	$sef_file		= $cms->get_path('root') . '/components/com_sef/sef_ext/com_content.php';
	$patch_file  	= JC_ADMIN_COM_PATH . '/patch/artio/com_content.php';

	$objResponse	= new JAXResponse();
	
	#Make backup copy of existing file before doing anything
	if(copy($sef_file,JC_ADMIN_COM_PATH . '/patch/artio/com_content.php.backup')){
	    #Now, remove the old file
		if(unlink($sef_file)){
		    #If sucessfull remove old file copy our patch to new path
			if(copy($patch_file,$sef_file)){
			    $objResponse->addAlert('JoomSEF for Jom Comment Patched! Please purge JoomSEF URLs!');
			    #Reload the page
				$objResponse->addScriptCall('window.location.reload();');
			}else{
			    $objResponse->addAlert('Error copying sef patch file - com_content.php');
			}
		}else{
		    $objResponse->addAlert('Error deleting sef old file - com_content.php');
		}
	}else{
		$objResponse->addAlert('Error backing up sef old file - com_content.php');
	}
	return $objResponse->sendResponse();
}

/*
 * Restore Artio's backup file
 */
function jcxRestoreArtio(){
	#Include our own libraries
	$cms    =& cmsInstance('CMSCore');

	while (@ ob_end_clean());
	ob_start();

	$sef_file		= $cms->get_path('root') . '/components/com_sef/sef_ext/com_content.php';
	$backup_file  	= JC_ADMIN_COM_PATH . '/patch/artio/com_content.php.backup';

	$objResponse	= new JAXResponse();

	#Remove our patch file from sef folder
	if(unlink($sef_file)){
	    if(rename($backup_file,$sef_file)){
	        $objResponse->addAlert('Original sef file restored! Please purge JoomSEF URLs!');
		    #Reload the page
			$objResponse->addScriptCall('window.location.reload();');
		}else{
		    $objResponse->addAlert('Error copying backup file - com_content.php');
		}
	}else{
	    $objResponse->addAlert('Error removing sef patch file - com_content.php');
	}

	return $objResponse->sendResponse();
}

function jcxDoPatch($com, $action) {
	global $jcPatchClass;
	$result = "";
	eval ('$patch = new ' . $jcPatchClass[$com] . '();');
	$result = $patch->action($action);
	return $result;
}

function modifyText(& $item, $key) {
	$item->comment = transformDbText($item->comment);
}

function jcxLoadLangFile($fileName) {
	$cms    =& cmsInstance('CMSCore');
	
	while (@ ob_end_clean());
	ob_start();
	$filename = $cms->get_path('root') . "/components/com_jomcomment/languages/" . $fileName;
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	$pattern = "'<?" . "php(.*)\?" . ">'s";
	preg_match($pattern, $contents, $matches);
	$contents = @ $matches[1];
	$contents = trim($contents);
	
	$objResponse = new JAXResponse();
	$objResponse->addAssign('editLangTextArea', 'value', $contents);
	$objResponse->addAssign('currentFile', 'value', $fileName);
	$objResponse->addAssign('ajaxInfo', 'innerHTML', $fileName . " loaded...");
	return $objResponse->sendResponse();
}

function jcxTogglePublish($id) {
	$db =& cmsInstance('CMSDb');
	while (@ ob_end_clean());
	ob_start();
	
	$db->query("SELECT published FROM #__jomcomment WHERE id=$id");
	$publish = $db->get_value();
	
	$publish = intval(!($publish));
	$db->query("UPDATE #__jomcomment SET published='$publish' WHERE id=$id");
	
	$objResponse = new JAXResponse();
	if ($publish) {
		$objResponse->addAssign('pubImg' . $id, 'src', 'images/publish_g.png');
		$d['id'] = $id;
	} else {
		$objResponse->addAssign('pubImg' . $id, 'src', 'images/publish_x.png');
		$d['id'] = $id;
	}
	return $objResponse->sendResponse();
}
function jcxToggleTrackbackPublish($id) {
	$db =& cmsInstance('CMSDb');
	while (@ ob_end_clean());
	ob_start();
	$db->query("SELECT published FROM #__jomcomment_tb WHERE id=$id");
	$publish = $db->get_value();
	
	$publish = intval(!($publish));
	$db->query("UPDATE #__jomcomment_tb SET published='$publish' WHERE id=$id");

	$objResponse = new JAXResponse();
	if ($publish) {
		$objResponse->addAssign('pubImg' . $id, 'src', 'images/publish_g.png');
	} else {
		$objResponse->addAssign('pubImg' . $id, 'src', 'images/publish_x.png');
	}
	return $objResponse->sendResponse();
}

function jcxEditComment($commentid) {
	global $_JC_CONFIG;
	while (@ ob_end_clean());
	ob_start();
	$obj	= null;
	$cms    =& cmsInstance('CMSCore');
	$cms->db->query('SELECT * FROM #__jomcomment WHERE `id`=' . $commentid . ' LIMIT 1');
	$comment    = $cms->db->object_to_array($cms->db->get_object_list());

	$comment[0]['comment'] = str_replace("<br />", '\n', stripslashes($comment[0]['comment']));
	$comment[0]['comment'] = str_replace("<br/>", '\n', stripslashes($comment[0]['comment']));
	$comment[0]['comment'] = str_replace("</br>", '\n', stripslashes($comment[0]['comment']));

	$tpl = & new AzrulJXTemplate();

	foreach ($comment[0] as $key => $val) {
		$tpl->set($key, $val);
	}
	
	$cms->db->query("SELECT title FROM #__content WHERE id='" . $comment[0]['contentid'] . "'");
	$tpl->set('content_title', $cms->db->get_value());

	$html = $tpl->fetch(JC_ADMIN_COM_PATH . '/templates/edit_comment.tpl.html');
	$objResponse = new JAXResponse();
	$objResponse->addScriptCall("showFloatingDialog", $html);
	return $objResponse->sendResponse();
}

function jcxSendMail($args){
	global $_JC_CONFIG, $mainframe;
	$cms	=& cmsInstance('CMSCore');
	$cms->load('libraries','user');
	
	$from	= $args['from'];
	$to		= $args['recipient'];
	$title	= $args['title'];
	$msg	= $args['message'];

	if(!function_exists('jomMail'))
		require_once($cms->get_path('root') . '/components/com_jomcomment/functions.jomcomment.php');

	$response	= new JAXResponse();
	
 	if(jomMail($cms->user->email, $from, $to, $title, $msg)){
		$response->addScriptCall("alert('Email sent!');");
		$response->addScriptCall("jQuery('#popupWindowContainer').css('visibility', 'hidden');");
 	} else {
 		$response->addScriptCall("alert('Error while sending mail. Please check mail configuration in Global Configuration.');");
 	}
 	return $response->sendResponse();
}

function jcxMoveComment($id, $contentid){
	$cms	=& cmsInstance('CMSCore');
	
	$strSQL	= "UPDATE #__jomcomment SET `contentid`='{$contentid}' WHERE `id`='{$id}'";
	$cms->db->query($strSQL);
	
	$response	= new JAXResponse();
	$response->addScriptCall("alert('Comment moved!');");
	$response->addScriptCall("jQuery('#popupWindowContainer').css('visibility', 'hidden');");

	// Update new content title
	$strSQL	= "SELECT `title` FROM #__content WHERE `id`='{$contentid}'";
	$cms->db->query($strSQL);
	$title	= $cms->db->get_value();
	
	$response->addAssign("content-" . $id, 'innerHTML', $title);
	return $response->sendResponse();
}

function jcxMoveCommentForm($id){
	global $_JC_CONFIG;
	$cms	=& cmsInstance('CMSCore');
	
	while (@ ob_end_clean());
	ob_start();

	$strSQL		= "SELECT b.title FROM #__jomcomment AS a, #__content AS b "
				. "WHERE a.id='{$id}' "
				. "AND b.id=a.contentid";
	$cms->db->query($strSQL);
	$oldTitle	= $cms->db->get_value();

	$categories	= trim($_JC_CONFIG->get('categories'));
	$strSQL		= "SELECT DISTINCT(b.id),(b.title) "
				. "FROM #__jomcomment AS a, #__content AS b "
				. "WHERE a.contentid != b.id "
				. "AND `sectionid` IN(" . $categories . ")";
	$cms->db->query($strSQL);
	$newTitles	= $cms->db->get_object_list();
	
	$template	=& new AzrulJXTemplate();
	$template->set('id', $id);
	$template->set('oldTitle', $oldTitle);
	$template->set('newTitles', $newTitles);
	$data		= $template->fetch(JC_ADMIN_COM_PATH . '/templates/move.tpl.html');
	
	$response	= new JAXResponse();
	$response->addScriptCall('showFloatingDialog', $data);
	
	return $response->sendResponse();
}

function jcxEmailForm($recipient) {
	global $_JC_CONFIG;
	$cms	=& cmsInstance('CMSCore');
	
	while (@ ob_end_clean());
	ob_start();

	$template	=& new AzrulJXTemplate();
	$template->set('recipient', $recipient);
	$data		= $template->fetch(JC_ADMIN_COM_PATH . '/templates/email.tpl.html');
	
	$response	= new JAXResponse();
	$response->addScriptCall('showFloatingDialog', $data);
	
	return $response->sendResponse();
}

function jcxSaveComment($xajaxArgs) {
	global $mainframe;
	
	require_once(JC_COM_PATH . '/views.jomcomment.php');
	$view	= new JCView();
	 
	while (@ ob_end_clean());
	ob_start();
	$row = new mosJomcomment();
	$commentid = $xajaxArgs['id'];
	$row->load($commentid);
	$row->bind($xajaxArgs);
	
	// Set the preview
	$row->preview	= $view->_formatComment($row->comment, '', $commentid);
	
	$row->store();
	//$row->updateOrder("contentid='$row->contentid'");
	$newrow = new mosJomcomment();
	$newrow->load($commentid);
	$nlSearch = array (
		"\n",
		"\r"
	);
	$nlReplace = array (
		" ",
		" "
	);
	$newrow->comment = str_replace($nlSearch, $nlReplace, $newrow->comment);
	$newrow->comment = transformDbText($row->comment);
	if (strlen($newrow->comment) > 300) {
		$newrow->comment = stripslashes(substr($newrow->comment, 0, 300 - 3));
		$newrow->comment .= "...";
	}
	$newrow->comment = strip_tags($newrow->comment);

	$objResponse = new JAXResponse();
	jcClearCache();
	$objResponse->addScriptCall("jQuery('#popupWindowContainer').css", 'visibility', 'hidden');
	$objResponse->addAssign("comment-" . $commentid, 'innerHTML', $newrow->comment);
	$objResponse->addAssign('comment-name-' . $commentid , 'innerHTML' , $newrow->name );
	$objResponse->addAssign('comment-email-' . $commentid , 'innerHTML' , $newrow->email );
	$objResponse->addAssign('comment-title-' . $commentid , 'innerHTML' , $newrow->title );
	
	$click	= 'jQuery("#comment-email-' . $commentid . '").click(function(){';
	$click	.= 'jax.call("jomcomment","jcxEmailForm", "' . $newrow->email . '");';
	$click	.= '});';
	
	$objResponse->addScriptCall( $click );
	//$objResponse->addAssign('comment-email-' . $commentid , 'innerHTML' , $newrow->email );
	$objResponse->addAssign('comment-website-' . $commentid , 'innerHTML' , $newrow->website );
	$objResponse->addAssign("date-" . $commentid, 'innerHTML', $newrow->date);
	
	
	return $objResponse->sendResponse();
}

function jcxBanUserName($name) {
	global $_JC_CONFIG;
	$_JC_CONFIG->addBlockedUser($name);
	$objResponse = new JAXResponse();
	$objResponse->addScriptCall("alert", "$name blocked");
	return $objResponse->sendResponse();
}

function jcxBanUserIP($ip) {
	global $_JC_CONFIG;
	$_JC_CONFIG->addBlockedIP($ip);
	$objResponse = new JAXResponse();
	$objResponse->addScriptCall("alert", "$ip IP blocked");
	return $objResponse->sendResponse();
}

/*
 * function : jcxSaveLanguage
 *          : Saves a specific language file
 * params   : $content (language files data)
 *          : $fileName (language file name english.php)
 */
function jcxSaveLanguage($content, $fileName){
	$cms    =& cmsInstance('CMSCore');
	
	while (@ ob_end_clean());
	$content = "<?php\n" . $content . "?" . ">";
	$content = stripslashes($content);
	$filename = $cms->get_path('root') . '/components/com_jomcomment/languages/' . $fileName;
	$handle = fopen($filename, "w");
	fwrite($handle, ($content));
	fclose($handle);
	$objResponse = new JAXResponse();
	$objResponse->addAssign(" ajaxInfo ", 'innerHTML', $fileName . " saved . . . ");

	return $objResponse->sendResponse();
}



function jcxTrainFilterTest($id){
	return jcxTrainTrackbackFilterTest($id, false);
}

function jcxTrainTrackbackFilterTest($id, $isTrackback=true){
	$cms    =& cmsInstance('CMSCore');

	if($isTrackback)
		$cms->db->query("SELECT excerpt, url FROM #__jomcomment_tb WHERE id='$id'");
	else
		$cms->db->query("SELECT comment FROM #__jomcomment WHERE id='$id'");
		
	#$comment = $database->loadRow();
	$comment    = $cms->db->row();
	$document = $comment[0] . " " . $comment[1] ;

	$data = "action=cat&type=comment&version=2&document=" . urlencode($document);
	$response = post("filter0.azrul.com/index.php", $data);
	$objResponse = new JAXResponse();
	$objResponse->addAlert($response);
	
	return $objResponse->sendResponse();
}

function jcxTrainTrackbackFilter($contentid, $cat, $quite = true) {
	return jcxTrainFilter($contentid, $cat, $quite, true);
}
/**
 * Send the filter setting to our centralize server
 */
function jcxTrainFilter($contentid, $cat, $quite = true, $trackback= false) {
	global $database, $mainframe;

	
	$document = "";
	$docid = $mainframe->getCfg('live_site') . "-$contentid";
	
	if($trackback){
		$database->setQuery("SELECT excerpt, url FROM #__jomcomment_tb WHERE id=$contentid");
		$docid .= "-tb";
		
		$comment = $database->loadRow();
		$document = $comment[0] . " " . $comment[1] ;
	
	} else {
		$database->setQuery("SELECT comment FROM #__jomcomment WHERE id=$contentid");
		$docid .= "-comment";
		
		$comment = $database->loadResult();
		$document = $comment;
	}
	
	

	if (true) {

		while (@ ob_end_clean());
		ob_start();
		$data = "action=train&server=$docid&cat=$cat&document=" . urlencode($document) . "&version=2&lang=" . $mainframe->getCfg('lang');
		$response = post("filter0.azrul.com/index.php", $data);
		$objResponse = new JAXResponse();
		$objResponse->addAlert($response);
		$objResponse->addAlert("Filter Trained");
		return $objResponse->sendResponse();
	} else {
		ob_start();
		$data = "action=train&docid=" . $mainframe->getCfg('live_site') . "-$contentid&cat=$cat&document=" . urlencode($document) . "&version=2&lang=" . $mainframe->getCfg('lang');
		$response = post("filter0.azrul.com/index.php", $data);
		ob_end_clean();

	}
}

/**
 * Remove all unpublished comments and trackbacks
 */ 
function jcxRemoveUnpublished(){
	$cms    =& cmsInstance('CMSCore');

	while (@ ob_end_clean());
	ob_start();
	
	$objResponse = new JAXResponse();

	return $objResponse->sendResponse();
}

/**
 * Rebuild table index
 */ 
function jcxRebuildIndex(){
	$cms    =& cmsInstance('CMSCore');
	while (@ ob_end_clean());
	ob_start();
	
	$objResponse = new JAXResponse();

	// Check jomcomment table index
	$doIndex        = array('option','published','contentid');
	$availableIndex = array();

	$cms->db->query('SHOW INDEX FROM #__jomcomment');
	$createdIndexes = $cms->db->get_object_list();

	foreach($createdIndexes as $row){
		$availableIndex[] = $row->Key_name;
	}
	
	foreach($doIndex as $key){
		if(!in_array($key,$availableIndex)){
			$strSQL = 'ALTER TABLE #__jomcomment ADD INDEX (`' . $key . '`)';
			$cms->db->query($strSQL);
		}
	}
	
	// Check jomcomment_tb table index
	$doIndex        = array('url','published','contentid', 'ip', 'option');
	$availableIndex = array();

	$cms->db->query('SHOW INDEX FROM #__jomcomment_tb');
	$createdIndexes = $cms->db->get_object_list();

	foreach($createdIndexes as $row){
		$availableIndex[] = $row->Key_name;
	}
	
	foreach($doIndex as $key){
		if(!in_array($key,$availableIndex)){
			$strSQL = 'ALTER TABLE #__jomcomment_tb ADD INDEX (`' . $key . '`)';
			$cms->db->query($strSQL);
		}
	}
	$objResponse->addAlert('Database tables for Jom Comment has been optimized!');

	return $objResponse->sendResponse();
}

/**
 * Clearing jom comment cache
 */  
function jcxClearCache(){
	while (@ ob_end_clean());
	ob_start();
	
	$objResponse = new JAXResponse();

	return $objResponse->sendResponse();
}

function post($host, $query, $others = '') {

	if(function_exists('curl_init')){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, "http://" .$host . "?". $query);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		ob_start();
		curl_exec ($ch);
		curl_close ($ch);
		$string = ob_get_contents();
		ob_end_clean();
		return $string;
	}
	
	if(ini_get('allow_url_fopen') == 1){
		
		$dh = fopen("http://". $host . "?". $query,'r');
		$result = fread($dh,8192);                                                                                                                   
		return $result;
	}

	/////////////////////////
	$path = explode('/', $host);
	$host = $path[0];
	$r = "";
	unset ($path[0]);
	$path = '/' . (implode('/', $path));
	$post = "POST $path HTTP/1.0\r\nHost: $host\r\nContent-type: application/x-www-form-urlencoded\r\n${others}User-Agent: Mozilla 4.0\r\nContent-length: " . strlen($query) . "\r\nConnection: close\r\n\r\n$query";
	$h = fsockopen($host, 80, $errno, $errstr, 7);
	if ($h) {
		fwrite($h, $post);
		for ($a = 0, $r = ''; !$a;) {
			$b = fread($h, 8192);
			$r .= $b;
			$a = (($b == '') ? 1 : 0);
		}
		fclose($h);
	}
	return $r;
}

function jcPagination($total, $limitstart, $limit){
	$pagination	= new stdClass();
	
	if(cmsVersion() == _CMS_JOOMLA15){
		jimport('joomla.html.pagination');
		$pageNav				= new JPagination($total, $limitstart, $limit);
		
		$pagination->limitstart	= $limitstart;
		$pagination->limit		= $limit;
		$pagination->total		= $total;
		$pagination->footer		= $pageNav->getListFooter();
	}
	else{
		$cms	=& cmsInstance('CMSCore');
		include_once($cms->get_path('root') . '/administrator/includes/pageNavigation.php');
		// We assume that this is a joomla 1.0 or mambo.
		$pageNav	= new mosPageNav($total, $limitstart, $limit);
		$pagination->limitstart	= $limitstart;
		$pagination->limit		= $limit;
		$pagination->total		= $total;
		$pagination->footer		= $pageNav->getListFooter();
	}
	return $pagination;
}

/**
 * Show the comment listing page
 */ 
function showComments($option) {
	global $database, $mainframe;

	$cms    =& cmsInstance('CMSCore');

	$cms->db->query("SELECT distinct `option` FROM #__jomcomment");
    $results = $cms->db->get_object_list();
	#Set the default option
	$default_com	= 'com_content';
	//$default_com	= $cms->db->get_value();

	if(count($results) == 1){
    	$default_com = $results[0]->option;
	}

	$limitOption    = cmsGetVar('limitOption', $default_com, 'REQUEST');
	
	$limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 10);
	$limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
	$search = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
	$search = $cms->db->_escape(trim(strtolower($search)));
	
	$searchContent = $mainframe->getUserStateFromRequest("searchContent{$option}", 'searchContent', '');
	$searchContent = $cms->db->_escape(trim(strtolower($searchContent)));
 	
	$searchUser		= $mainframe->getUserStateFromRequest("searchUser",'searchUser','');
	$searchUser		= $cms->db->_escape(trim(strtolower($searchUser)));

	$where = array ();
	
	// Seach for comment with the given string
	if ($search) {
		$where[] = "LOWER(comment) LIKE '%$search%'";
	}
	
	$where[] = "`option`='$limitOption' ";
	
	// search for comment with the given content. Only the first content that
	// matches is displayed	
	if($searchContent){
		$cms->db->query("SELECT id FROM #__content WHERE `title` LIKE '%$searchContent%'");
		$contentid = $cms->db->get_value();
		if($contentid != 0){
			$where[] = " `contentid`=$contentid ";
			$where[] = " `option`='$limitOption' ";
		}
	}

	// User wants to search comments from specific user
	if($searchUser){
		include_once(JC_COM_PATH . '/helper/comments.helper.php');
		
		$userId	= intval($searchUser);
		if($userId == 0){
			$userId	= jcGetUserId($searchUser, true);
			$where[] = "`user_id`='{$userId}'";
		} else {
			// User input user id
			$where[] = "`user_id`='{$searchUser}'";
    	}
    }
	
	$cms->db->query("SELECT count(*) FROM #__jomcomment AS a" . (count($where) ? "\nWHERE " . implode(' AND ', $where) : ""));
	
	$total = $cms->db->get_value();
	
	$pageNav = jcPagination($total, $limitstart, $limit);

	$cms->db->query("SELECT * FROM #__jomcomment" . (count($where) ? "\nWHERE " . implode(' AND ', $where) : "") . "\nORDER BY id DESC" . "\nLIMIT $pageNav->limitstart,$pageNav->limit");
	$rows = $cms->db->get_object_list();

	#Check if $rows is array first
	if(is_array($rows)){
	    array_walk($rows, 'modifyText');
	}

	HTML_comment :: showComments($option, $rows, $search, $pageNav, $searchContent, $searchUser);
}


/**
 * Show the trackback list page
 */ 
function showTrackbacks($option) {
	global $mainframe;
	$cms    =& cmsInstance('CMSCore');
	
	$limit		= $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 10);
	$limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
	$search		= $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
	$search		= $cms->db->_escape(trim(strtolower($search)));


 	$searchContent = $mainframe->getUserStateFromRequest("searchContent{$option}", 'searchContent', '');
 	$searchContent = $cms->db->_escape(trim(strtolower($searchContent)));
 	
	$where = array ();
	if ($search) {
		$where[] = "LOWER(excerpt) LIKE '%$search%'";
	}
	
	// search for comment with the given content. Only the first content that
	// matches is displayed	
	if($searchContent){
		$cms->db->query("SELECT id FROM #__content WHERE `title` LIKE '%$searchContent%'");
		$contentid = $cms->db->get_value();
		
		if($contentid != 0){
			$where[] = " `contentid`=$contentid ";
		}
	}

	$cms->db->query("SELECT count(*) FROM #__jomcomment_tb AS a" . (count($where) ? "\nWHERE " . implode(' AND ', $where) : ""));
	$total = $cms->db->get_value();
	include_once ("includes/pageNavigation.php");
	$pageNav = new mosPageNav($total, $limitstart, $limit);

	$cms->db->query("SELECT * FROM #__jomcomment_tb" . (count($where) ? "\nWHERE " . implode(' AND ', $where) : "") . "\nORDER BY id DESC" . "\nLIMIT $pageNav->limitstart,$pageNav->limit");
	$rows = $cms->db->get_object_list();

	HTML_trackbacks :: showTrackbacks($option, $rows, $search, $pageNav, $searchContent);
}


/**
 * 
 */ 
function publishComments($cid = null, $publish = 1, $option, $task = 'comments') {
	$cms    =& cmsInstance('CMSCore');

	if (!is_array($cid) || count($cid) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
		exit;
	}
	$cids	= implode(',', $cid);
	$cms->db->query("UPDATE #__jomcomment SET `published`='{$publish}' WHERE `id` IN ({$cids})");
	
	# Clear the cache, otherwise it won't show after refresh
	jcClearCache();
	
	cmsRedirect("index2.php?option=$option&task=$task");
}

function joomfishExists(){
	$cms	=& cmsInstance('CMSCore');
	
	if(file_exists($cms->get_path('root') . '/components/com_joomfish/joomfish.php'))
		return true;
	return false;
}

/**
 *
 */ 
function publishTrackbacks($cid = null, $publish = 1, $option, $task = 'comments') {
	global $mainframe;
	$cms    =& cmsInstance('CMSCore');

	if (!is_array($cid) || count($cid) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
		exit;
	}
	$cids = implode(',', $cid);
	$cms->db->query("UPDATE #__jomcomment_tb SET published='$publish' WHERE id IN ($cids)");

	cmsRedirect("index2.php?option=$option&task=$task");
}


function showMaintd(){
	global $mainframe;
	$cms    =& cmsInstance('CMSCore');
	$db		=& cmsInstance('CMSDb');

	if(isset($_POST['maintd']) && $_POST['maintd'] == 'syncUser'){
		$strSQL	= "SELECT `id`,`name` FROM #__users";
		$cms->db->query($strSQL);
		
		$users	= $cms->db->get_object_list();
		foreach($users as $user){
			$strSQL	= "UPDATE #__jomcomment SET `name`='{$user->name}' WHERE `user_id`='{$user->id}'";
			$cms->db->query($strSQL);
		}
		jcClearCache();
		echo "<h3>Name's synchronized...</h3>";
	}

	if(isset($_POST['maintd']) && $_POST['maintd'] == 'clearunpublished'){
		$db->query("DELETE FROM #__jomcomment WHERE published=0");
		$db->query("DELETE FROM #__jomcomment_tb WHERE published=0");
		echo "<h3>Unpublished items deleted...</h3>";
	}

	if(isset($_POST['maintd']) && $_POST['maintd'] == 'clearcache'){
		$cms->load('libraries', 'cache');
		$cms->cache->clear();
		
		jcClearCache();
		echo "<h3>Cache cleared...</h3>";
	}


?>
<table width="800" border="0" cellspacing="2" cellpadding="0">
    <tr>
        <td width="558"><h2>Clear all Jom Comment cache.</h2>
        Jom Comment cache the generated page to significantly increase the loading performance. This cache is independent of Joomla cache and are automatically cleared at a specific interval. If you want, you can force the cache to be cleared here 
		<p>
		<form id="form1" name="form1" method="post" action="">
			<input name="maintd" type="hidden" id="maintd" value="clearcache" />
            <input type="submit" name="Submit"  class="CommonTextButtonSmall" value="Clear Jom Comment Cache" />
        </form>
		</p>
		</td>
        <td  valign="bottom" width="274">        </td>
    </tr>
    <tr>
        <td colspan="2"><hr size="1" noshade="noshade" /></td>
    </tr>
    <tr>
        <td><h2>Synchronize User's Real Name</h2>
        This tool will synchronize the user's real name with the latest real name used by the user.<br /><br />
        Note: This operation may take a little while if you have a huge set of user's database.
        <p>
        <form id="form1" name="form1" method="post" action="">
            <input type="submit" name="Submit"  class="CommonTextButtonSmall" value="Synchronize Now" />
            <input name="maintd" type="hidden" id="maintd" value="syncUser" />
        </form>
		</p>
        </td>
        <td  valign="bottom">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2"><hr size="1" noshade="noshade" /></td>
    </tr>
    <tr>
        <td><h2>Removed unpublished comments and trackbacks</h2>
        You can delete all unpublished items from Jom Comment database to make the database smaller.
        <p>
        <form id="form1" name="form1" method="post" action="">
            <input type="submit" name="Submit"  class="CommonTextButtonSmall" value="Delete unpublished items" />
            <input name="maintd" type="hidden" id="maintd" value="clearunpublished" />
        </form>
		</p>
        </td>
        <td  valign="bottom"></td>
    </tr>
    <tr>
        <td colspan="2"><hr size="1" noshade="noshade" /></td>
    </tr>
    <tr>
        <td><h2 style="margin-top:0px;">Rebuild and optimize table index</h2>
        If you are upgrading from an older version of Jom Comment, 
		the table index might be missing. Click the button below to rebuild the 
		table index. Indexing the table properly will greatly improve Jom Comment
		performance on site with huge number of comments.
        <p><form id="form1" name="form1" method="post" action="">
            <input type="button" name="Submit"  class="CommonTextButtonSmall" value="Rebuild table index" onClick="javascript:void(0);jax.call('jomcomment','jcxRebuildIndex');"/>
            <input name="maintd" type="hidden" id="maintd" value="clearunpublished" />
			</form>
			</p>
        </td>
        <td  valign="bottom"></td>
    </tr>
    <tr>
        <td colspan="2"><hr size="1" noshade="noshade" /></td>
    </tr>
<?php
// Do not display the following for Joomla 1.5
if(cmsVersion() != _CMS_JOOMLA15){
?>
    <tr>
        <td width="558"><h2>Patch JoomSEF for Pagination</h2>
        To have Jom Comment's pagination to work correctly with ARTIO JoomSEF, please use this patch.<br /><br />
        <strong>* Please purge ARTIO's SEF URL after patching the file.</strong>
		<p>
		<form id="form1" name="form1" method="post" action="">
<?php
#Check if the file has been patched by checking if there is a file in
#JOOMLA/administrator/components/com_jomcomment/patch/artio/com_content.php.backup
$sef_backupfile   = JC_ADMIN_COM_PATH . '/patch/artio/com_content.php.backup';
if(!file_exists($sef_backupfile)){
#No backup files, allow patch
?>
<input type="button" name="Submit"  class="CommonTextButtonSmall" value="Patch Artio JoomSEF File" onClick="javascript:void(0);jax.call('jomcomment','jcxPatchArtio');"/>
<?php
}else{
#Already have backup files, allow undo?
?>
<input type="button" name="Submit"  class="CommonTextButtonSmall" value="Restore Artio JoomSEF File" onClick="javascript:void(0);jax.call('jomcomment','jcxRestoreArtio');"/>
<?php
}
?>

            
            <input name="maintd" type="hidden" id="maintd" value="clearcache" />
        </form>
		</p>
		</td>
        <td  valign="bottom" width="274">        </td>
    </tr>
    <tr>
        <td colspan="2"><hr size="1" noshade="noshade" /></td>
    </tr>
    <tr><!-- patch for pagination -->
        <td><h2 style="margin-top:0px;">Fix com_content pagination cache bug</h2>
        If you are using pagination feature, you will need to apply a small hack 
		to the com_content/content.php file to work-around Joomla's aggrasive caching.
		Joomla by default, does not recognize comment pagination. Don't worry, a backup
		file are created and stored as content.php.backup
        <p><form id="formPaginationHack" name="formPaginationHack" method="post" action="">
        <?php
        include_once(JC_COM_PATH . '/helper/system.hacks.php');
		if(!jcCheckContentPagination()) {
		?>
			<div id="paginationPatchAction">
            <input type="button" name="Submit"  class="CommonTextButtonSmall" value="Patch com_content" onClick="javascript:void(0);jax.call('jomcomment','jcxPatchContent');"/>
            </div>
		<?php
		} else {
		?>
			<div id="paginationPatchAction">
            <input type="button" name="Submit"  class="CommonTextButtonSmall" value="Restore com_content patched file" onClick="javascript:void(0);jax.call('jomcomment','jcxRestorePatchedContent');"/>
            </div>
		<?php
		} 
		?>
			        
			</form>
			</p>
        </td>
        <td  valign="bottom"></td>
    </tr>
    <tr>
        <td colspan="2"><hr size="1" noshade="noshade" /></td>
    </tr>
<?php
}
?>
</table>

	<?php 
	
	} 
	
	function showAbout() { 
		HTML_comment :: showAbout(); 
	} 
	
	function showSupport() {
		HTML_comment :: showSupport(); 
	} 
	
function showReports(){
	//Displays the administration section of View Reports
	global $mainframe;

	$jq     	= JC_ADMIN_LIVEPATH . '/js';
	$template	= JC_ADMIN_LIVEPATH	. '/templates';	
?>
<script src="<?php echo $jq;?>/jquery-1.2.6.pack.js" type="text/javascript"></script>
<script type='text/javascript'>
/*<![CDATA[*/
jQuery.noConflict();
/*]]>*/
</script>
<script src="<?php echo $jq;?>/ui.mouse.js" type="text/javascript"></script>
<script src="<?php echo $jq;?>/jquery.dimensions.js" type="text/javascript"></script>
<script src="<?php echo $jq;?>/ui.draggable.js" type="text/javascript"></script>
<script src="<?php echo $template;?>/edit_comment.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo $template;?>/edit_comment.css" type="text/css" />
<div id="popupWindowContainer" style="visibility:hidden; position:absolute" >
	<div class="dropshadowBox">
		<div class="innerbox">
		<div id="popupWindowHandle"></div>
			<div id="popupWindowEditable" ></div>
		</div>
	</div>
</div>
<?php
	$cms	= &cmsInstance('CMSCore');
	//Prepare for html output.
	$html   = '<form action="index2.php?option=com_jomcomment&task=reports" method="post" name="adminForm"  id="adminForm" >';
	//$html	= '<form action="index2.php?option=com_jomcomment&task=reports" method="post" name="adminForm"  id="adminForm" >';

	//Set header logo
	$html   .= '<table cellpadding="4" cellspacing="0" border="0" width="100%">'
	        . '	<tr>'
	        . '		<td><img src="components/com_jomcomment/logo.png"></td>'
	        . '	</tr>'
	        . '</table>';
	        
	// Load libraries
	$cms->load('libraries','trunchtml');
	$cms->load('libraries','table');
//	$cms->load('libraries','pagination');
	
	// Pagination values
	// Get limitstart value from query string.
	//$limitstart = cmsGetVar('limitstart', 0, 'GET');

	//$limit = $limitstart ? "LIMIT $limitstart, " . JOMCOMMENT_DEFAULT_LIMIT : 'LIMIT ' . JOMCOMMENT_DEFAULT_LIMIT;

	$limit 		= $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', JOMCOMMENT_DEFAULT_LIMIT );
	$limitstart = $mainframe->getUserStateFromRequest("viewcom_jomcommentlimitstart", 'limitstart', 0);
	
	
//	$pageNav = jcPagination($total, $limitstart, $limit);
	#Query database for the comments by only selecting the comments that
	#has been reported.
	$strSQL = "SELECT DISTINCT(b.commentid) as id,a.name,a.email,a.website,a.date,a.ip, "
        . "a.comment, a.title, a.published FROM #__jomcomment AS a "
        . "INNER JOIN #__jomcomment_reports AS b "
        . "ON b.commentid = a.id "
        . "ORDER BY a.id DESC LIMIT {$limitstart} , {$limit}";

	$cms->db->query($strSQL);
	$rows   = $cms->db->get_object_list();

	// set table properties
	$tmpl = array (
                'table_open'          => '<table border="0" width="100%" class="mytable" cellpadding="4" cellspacing="0">',

                'heading_row_start'   => '<tr>',
                'heading_row_end'     => '</tr>',
                'heading_cell_start'  => '',
                'heading_cell_end'    => '</th>',

                'row_start'           => '<tr class="row0">',
                'row_end'             => '</tr>',
                'cell_start'          => '',
                'cell_end'            => '</td>',

                'row_alt_start'       => '<tr class="row1">',
                'row_alt_end'         => '</tr>',
                'cell_alt_start'      => '',
                'cell_alt_end'        => '</td>',

                'table_close'         => '</table>'
          );
              
	$cms->table->set_template($tmpl);
	$cms->table->set_heading('<th width="5%"><input type="checkbox" name="toggle" value="" onclick="checkAll(' . count($rows) . ');" />','<th width="50%">Comment', '<th width="10%">Total Reports','<th width="15%" align=center>Date','<th width="15%">Publish Status');
	//<input type="checkbox" name="toggle" value="" onclick="checkAll(' . $totalRows . ');" />'

	$i	= 0;

	if($rows){
		foreach($rows as $row){
			# Get total number of reports for particular comment
			$strSQL = "SELECT COUNT(*) AS total FROM #__jomcomment_reports WHERE `commentid`='$row->id'";
			$cms->db->query($strSQL);
			$total	= $cms->db->get_value();
			$pubImg = $row->published ? 'publish_g.png' : 'publish_x.png';
	      
			#Format the comment output to a shorter comment
			$row->comment = transformDbText($row->comment);
			if(strlen($row->comment) > 300) {
				$row->comment  = stripslashes(substr($row->comment,0,300-3));
				$row->comment .= "...";
			}
			$pubImg =  "<a href=\"javascript: void(0);\" onclick=\"jax.call('jomcomment','jcxTogglePublish', $row->id);\">"
	    					.  "<img id=\"pubImg$row->id\" src=\"images/" . $pubImg . "\" width=\"12\" height=\"12\" border=\"0\">"
			  				.  "</a>";
					
			$col1 = "
					<div style=\"text-align:left\">
			      	<strong>INFO: </strong>
			      		<strong>Name: </strong>$row->name | 
						<strong>Email: </strong>$row->email | 
						<strong>URL: </strong>$row->website |
						<strong>Date:</strong> <span id=\"date-{$row->id}\">$row->date</span> |
						<strong>IP: </strong>$row->ip | 
			      	</div>";
			      	
			$data   = array('<td><input type="checkbox" id="cb' . $i . '" name="cid[]" value="' . $row->id . '" onclick="isChecked(this.checked);"" /></td>',
								'<td>' . "<strong>{$row->title}</strong>" .'<div class="comment" id="comment-' . $row->id . '">' .strip_tags($row->comment) . '</div>' . $col1,
								'<td align="center"><a href="#">' . $total . '</a>',
								'<td align="center">' . $row->date,
		    				'<td align="center">' . $pubImg.'&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="jax.call(\'jomcomment\',\'jcxDismissReport\', '.$row->id.');">dismiss</a>
								&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="jax.call(\'jomcomment\',\'jcxEditComment\', '.$row->id.');">edit</a>',
								);
				
			// Add hidden row
			$cms->table->add_row($data);
			$i++;
		}
	}
	
	#Append the data into output
	$html .= $cms->table->generate();

	#Load pagination array
	$config = array();
	
	
	
    #Get amount of comments that has been reported.
    $strSQL = "SELECT COUNT(DISTINCT `commentid`) FROM #__jomcomment_reports";
    $cms->db->query($strSQL);

	
// 	$config['total_rows']	= $cms->db->get_value();
// 	$config['base_url']		= $_SERVER['REQUEST_URI'];
// 	$config['per_page'] 	= JOMCOMMENT_DEFAULT_LIMIT;

	$pagination	= jcPagination( $cms->db->get_value() , $limitstart , $limit );
	//$cms->pagination->initialize($config);
	//$html .=  $cms->pagination->create_links();

	$html	.= $pagination->footer;
	#End the form
	$html .= '<input type="hidden" name="task" value="" />'
		    .'<input type="hidden" name="boxchecked" value="0" />'
			.'</form>';
    #Print data
    echo $html;
}
	
	
	// Show latest news from our rss feed
	function showLatestNews(){
		$cms = cmsInstance('CMSCore');
		$cms->load('libraries', 'lastrss');
		
		echo "<h2>Latest updates</h2>";
		echo "<div style=\"width:640px;\"><p>";
		
		$rss = $cms->lastrss;
		$rss->cache_dir = $cms->get_path('root') . '/cache';
		$rss->cache_time = 120;
		$rss->date_format = 'M d, Y g:i:s A';
		$rss->CDATA = 'content';
		
		$url = "http://support.azrul.com/rss/index.php?_m=news&_a=view&group=default";
		//$url = "http://localhost/joomla/dev/index.php?option=com_myblog&Itemid=27&task=rss";
		
		$i = 0;
		if ($rs = $rss->get($url)) {
			$rs = $rss->get($url);
			
			foreach ($rs['items'] as $item) {
				if($i < 10){
					echo "<div style=\"padding:8px;border-bottom:1px dotted #666666;\"><div style=\"font-weight:bold\"><a parent=\"_blank\"href=\"{$item['link']}\">{$item['title']}</a></div>";
					echo "<div>{$item['pubDate']}</div><div>{$item['description']}</div></div>";
				}
				$i++;
			}
		
			if ($rs['items_count'] <= 0) { echo "<li>Sorry, no items found in the RSS file :-(</li>"; }
		
		} else {
			echo "Sorry: It's not possible to reach RSS file $url\n<br />";
			// you will probably hide this message in a live version
		}
		
		// List of RSS URLs
		echo "</p></div>";

	}


	function showLicense() { 
		HTML_comment :: showLicense(); 
	} 
	
	function importMXComment(){
		$cms	=& cmsInstance('CMSCore');
		
		$strSQL = "INSERT INTO #__jomcomment(`contentid`, `option`, `ip`, `name`, `email`, `website`, `title`, `comment`, `date`, `published`)"
				. "SELECT contentid, component, ip, name, email, web, title, comment,date,published FROM  #__mxc_comments";
		$cms->db->query($strSQL);
		echo 'Import completed.';
	}
	
	function importJoomlaComment() { 
  global $database;
		$query = "INSERT INTO #__jomcomment
	                (contentid, `option`, ip, name, title, comment, date, published)
	              SELECT
	                contentid, 'com_content', ip, name, title, comment,date,published
	              FROM  #__comment";
				$database->setQuery($query);
	            $database->query();
				$numImported = $database->getAffectedRows();
				echo "Finished... "; echo $numImported . " comments imported"; 
	} 
	
	function importAkoData() { 
		global $database;
		$tname = array ( "#__akocomment" ); 
		$fields = @ $database->getTableFields($tname); 
		if (!empty ($fields)) { 
			$prefix = "#__akocomment"; $addwhere = ""; 
			if (isset ($_REQUEST['nounpublished']) && (@ $_REQUEST['nounpublished'])) { 
				$addwhere = " AND published=1 "; 
			} 
			$fieldURL = array_key_exists("url", $fields[$prefix]); 
			$fieldWeb = array_key_exists("web", $fields[$prefix]); 
			$fieldEmail = array_key_exists("email", $fields[$prefix]); 
			$insertMore = ""; 
			$selectMore = ""; 
			if ($fieldURL) { 
				$insertMore .= ", website"; 
				$selectMore .= ", url"; 
			} else if ($fieldWeb) { 
				$insertMore .= ", website"; $selectMore .= ", web"; } 
				
				if ($fieldEmail) { 
					$insertMore .= ", email"; 
					$selectMore .= ", email"; 
				} 
				$query = sprintf("
		        INSERT INTO #__jomcomment
		        (contentid, `option`, ip, name, title, comment, date, published %s)
		        SELECT contentid, 'com_content', ip, name, title, comment,date,published %s
		        from #__akocomment where 1 %s;", $insertMore, $selectMore, $addwhere); 
				$database->setQuery($query); 
				$database->query(); 
				$database->getQuery(); 
				$numImported = $database->getAffectedRows(); 
				echo "Finished... "; 
				echo $numImported . " comments imported"; 
				} else { echo "No AkoCOmment data detected"; } } 


	function importComments($option) {
		$cms	=& cmsInstance('CMSCore');
		$cms->load('helper','url');
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%">
	<tr>
    	<td width="100%" class="sectionname"><img src="components/com_jomcomment/logo.png"></td>
	</tr>
</table>
<?php

			if (!empty ($_REQUEST['confirm'])) {
				if ($_REQUEST['from'] == "joomlacomment") { 
					importJoomlaComment();
				} else if($_REQUEST['from'] == 'mxcomment'){
					importMXComment();
				} else if ($_REQUEST['from'] == "ako") { 
					importAkoData(); 
				} else if ($_REQUEST['from'] == "combomax") { 
					$tname = array ( "#__combomax" ); 
					$fields = $database->getTableFields($tname); 
					if (!empty ($fields)) { 
						$prefix = "#__combomax"; 
						if (array_key_exists("imported", $fields[$prefix])) { 
							echo "<p><div align=\"left\">Database records has been imported.</div></p>"; 
							return; 
						} 
						$addwhere = ""; 
						
						if (isset ($_REQUEST['nounpublished']) && (@ $_REQUEST['nounpublished'])) { 
							$addwhere = " AND approved=1 "; 
						} 
						
						$fieldURL = array_key_exists("url", $fields[$prefix]); 
						$fieldEmail = array_key_exists("email", $fields[$prefix]); 
						$insertMore = ""; $selectMore = ""; 
						if ($fieldURL) { 
							$insertMore .= ", website"; $selectMore .= ", url"; 
			} else if ($fieldWeb) { 
				$insertMore .= ", website"; 
				$selectMore .= ", web"; 
			} 
			if ($fieldEmail) { 
				$insertMore .= ", email"; $selectMore .= ", email"; 
			} 
			$query = "ALTER TABLE #__combomax ADD imported tinyint null;"; 
			$database->setQuery($query); 
			$database->query(); $query = sprintf("
						                INSERT INTO #__jomcomment
						                (contentid, `option`, ip, name, title, comment, date, published %s)
						                SELECT contentid, 'com_content', ip, name, '...', comment,date,approved %s
						                from #__combomax where imported IS null %s;", $insertMore, $selectMore, $addwhere); 
										$database->setQuery($query); $database->query(); 
										$numImported = $database->getAffectedRows(); 
										$query = "UPDATE #__akocomment SET imported=1;"; 
										$database->setQuery($query); $database->query(); 
										echo "Finished... "; echo $numImported . " comments imported"; 
										} else { 
											echo "No ComboMax data detected"; 
										} 
									} else { 
										$tname = array ( "#__content_comments" ); 
										$fields = $database->getTableFields($tname); 
										if (!empty ($fields)) { 
											$prefix = "#__content_comments"; 
												if (array_key_exists("imported", $fields[$prefix])) { 
													echo "<p><div align=\"left\">Database records has been imported.</div></p>"; 
													return; 
												} 
												$addwhere = ""; 
												if (isset ($_REQUEST['nounpublished']) && (@ $_REQUEST['nounpublished'])) { 
													$addwhere = " AND `published`=1 "; 
												} 
												
												$fieldURL = array_key_exists("homepage", $fields[$prefix]); 
												$fieldEmail = array_key_exists("email", $fields[$prefix]); 
												$insertMore = ""; $selectMore = ""; 
												if ($fieldURL) { 
													$insertMore .= ", website"; $selectMore .= ", homepage"; 
												} 
												
												if ($fieldEmail) { 
													$insertMore .= ", email"; $selectMore .= ", email"; 
												} 
												
												$query = "ALTER TABLE #__content_comments ADD imported tinyint null;"; 
												$database->setQuery($query); 
												$database->query(); 
												$query = sprintf("
						                INSERT INTO #__jomcomment
						                (contentid, `option`, name, title, comment, date, published %s)
						                SELECT articleid, 'com_content', name, '...', entry, NOW(),approved %s
						                from #__content_comments where imported IS null %s;", $insertMore, $selectMore, $addwhere); 
						                
										$database->setQuery($query); 
										$database->query(); 
										$numImported = $database->getAffectedRows(); 
										$query = "UPDATE #__content_comments SET imported=1;"; 
										$database->setQuery($query); 
										$database->query(); 
										echo "Finished... "; 
										echo $numImported . " comments imported"; 
										} else { echo "No MosCom data detected"; } } 
										} else {
												$jq     	= JC_ADMIN_LIVEPATH  . '/js/jquery-1.2.6.pack.js';
												$jq_tabs    = JC_ADMIN_LIVEPATH  . '/js/jquery.tabs.pack.js';
												$jq_css     = JC_ADMIN_LIVEPATH  . '/js/jquery.tabs.css';
?>
        <link rel="stylesheet" href="<?PHP echo $jq_css;?>" type="text/css" media="print, projection, screen">
        <script src="<?PHP echo $jq;?>" type="text/javascript"></script>
        <script src="<?PHP echo $jq_tabs;?>" type="text/javascript"></script>
        <script type="text/javascript">
            $(function() {
                $('#importTab').tabs();
            });
        </script>
<div id="importTab">
<ul>
	<li><a href="#ako"><span>AkoComment</span></a></li>
	<li><a href="#combomax"><span>Combomax</span></a></li>
	<li><a href="#moscom"><span>MosCom</span></a></li>
	<li><a href="#JoomlaComment"><span>!JoomlaComment</span></a></li>
	<li><a href="#MXComment"><span>MXComments</span></a></li>
</ul>

<div id="ako">
<a name="ako"></a>
    <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="mytable" >
<th>Import from AkoComment</th>
  <tr>
    <td><form name="formAko" method="post" action="">
      <p></p>
      <p>We will now attempt to import existing AkoComment data into Jom Comment. No data will be lost in this process. Click &quot;Import Now&quot;to proceed.           </p>
      <p>
        <input name="nounpublished" type="checkbox" id="nounpublished" value="true">
        Do not import unpublished comments </p>
      <p><input type="hidden" name="from" id="from" value="ako" />
        <input type="submit" name="Submit" value="Import Now" class="CommonTextButtonSmall"><input name="confirm" type="hidden" id="confirm" value="true">

        </p>
    </form>
    </td>
  </tr>
</table
</div>
<div id="combomax">
<a name="combomax"></a>
<table width="100%" border="0" cellspacing="0" cellpadding="0"  class="mytable" >
<th>Import from ComboMax</th>
  <tr>
    <td><form name="formCombomax" method="post" action="">
      <p></p>
      <p>We will now attempt to import existing ComboMax data into Jom Comment. No data will be lost in this process. Click &quot;Import Now&quot;to proceed.           </p>
      <p>
        <input name="nounpublished" type="checkbox" id="nounpublished" value="true">
        Do not import unpublished comments </p>
      <p><input type="hidden" name="from" id="from" value="combomax" />
        <input type="submit" name="Submit" value="Import Now" class="CommonTextButtonSmall"><input name="confirm" type="hidden" id="confirm" value="true">

        </p>
    </form>
    </td>
  </tr>
</table>
</div>
<div id="moscom">
<a name="moscom"></a>
<table width="100%" border="0" cellspacing="0" cellpadding="0"  class="mytable" >
<th>Import from MosCom</th>
  <tr>
    <td><form name="formMoscom" method="post" action="">
      <p></p>
      <p>We will now attempt to import existing MosCom data into Jom Comment. No data will be lost in this process. Click &quot;Import Now&quot;to proceed. Note that due to MosCom database structure, it is not possible to import MosCom entry date information </p>
      <p>
        <input name="nounpublished" type="checkbox" id="nounpublished" value="true">
        Do not import unpublished comments </p>
      <p><input type="hidden" name="from" id="from" value="moscom" />
        <input type="submit" name="Submit" value="Import Now" class="CommonTextButtonSmall"><input name="confirm" type="hidden" id="confirm" value="true">

        </p>
    </form>
    </td>
  </tr>
</table>
</div>
<div id="JoomlaComment">
<a name="!JoomlaComment"></a>
<table width="100%" border="0" cellspacing="0" cellpadding="0"  class="mytable" >
<th>Import from !JoomlaComment</th>
  <tr>
    <td><form name="formMosJoomla" method="post" action="">
      <p></p>
      <p>We will now attempt to import existing !JoomlaComment data into Jom Comment. No data will be lost in this process. Click &quot;Import Now&quot;to proceed.</p>
      <p><input type="hidden" name="from" id="from" value="joomlacomment" />
        <input type="submit" name="Submit" value="Import Now" class="CommonTextButtonSmall"><input name="confirm" type="hidden" id="confirm" value="true">

        </p>
    </form>
    </td>
  </tr>
</table>
</div>
<div id="MXComment">
<a name="MXComment"></a>
<table width="100%" border="0" cellspacing="0" cellpadding="0"  class="mytable" >
<th>Import from MXComments</th>
  <tr>
    <td>
		<form name="formMXComment" method="post" action="">
      <p></p>
      <p>We will now attempt to import existing !JoomlaComment data into Jom Comment. No data will be lost in this process. Click &quot;Import Now&quot;to proceed.</p>
      <p><input type="hidden" name="from" id="from" value="mxcomment" />
        <input type="submit" name="Submit" value="Import Now" class="CommonTextButtonSmall"><input name="confirm" type="hidden" id="confirm" value="true">

        </p>
    </form>
    </td>
  </tr>
</table>
</div>
</div>
<?php 
	} 
} 

function showStatistics() { 
global $database, $mainframe; 

?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" >
	<tr>
		<td width="100%" class="sectionname"><img src="components/com_jomcomment/logo.png"></td>
	</tr>
</table>
<?php 
$cms    	=& cmsInstance('CMSCore');
$pcTemplate = $cms->get_path('root') . "/administrator/components/com_jomcomment/templates/statistics.html.php";
$handle 	= fopen($pcTemplate, "r"); 
$fdata		= fread($handle, filesize($pcTemplate)); fclose($handle);

$cms->db->query('SELECT count(*) FROM #__jomcomment');
$contentSum = $cms->db->get_value();
$fdata		= str_replace("{totalComments}", $contentSum, $fdata);

$cms->db->query("SELECT count(*) from #__jomcomment WHERE DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= date");

$contentSum = $cms->db->get_value();
$fdata = str_replace("{commentThisMonth}", $contentSum, $fdata);
$query = (sprintf("
	            select count(*) as numComment, name, email from #__jomcomment 
	            where DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= date
	            AND name != '' 
	            GROUP BY name order by numComment desc LIMIT 0, 20
	          ")); 
			  $cms->db->query($query);
			  $result = $cms->db->get_object_list(); $topMember = "<ul>";
			  foreach ($result as $row) { 
			  	$topMember .= "<li><a href=\"mailto:$row->email\">$row->name</a> , ($row->numComment comments)</li>"; 
			} 
$topMember .= "</ul>";
$fdata = str_replace("{topMember}", $topMember, $fdata);
$query = (sprintf("
	            select count(*) as numComment, contentid from #__jomcomment 
	            where DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= date
	            GROUP BY contentid order by numComment desc LIMIT 0, 20
	          ")); 
$cms->db->query($query);
$result = $cms->db->get_object_list();
	$topContent = "<ul>";
	foreach ($result as $row) {
		$cms->db->query("SELECT title from #__content WHERE id=$row->contentid");
		$contentTitle = $cms->db->get_value();
		$topContent .= "<li>$contentTitle</li>";
	}
	$topContent .= "</ul>";
	$fdata = str_replace("{topContent}", $topContent, $fdata);
	echo $fdata;
}

function formatLanguage($language){

	if(is_array($language)){
		$languages	= array();
		
		// Format language file so that it doesn't appear with .php
		foreach($language as $file){
			$languages[$file]	= substr($file, 0, -4);
		}
	} else {
		return substr($language, 0, -4);
	}
	return $languages;
}

function showLanguageConfig($option){
	global $mainframe, $_JC_CONFIG;
	
	$cms	=& cmsInstance('CMSCore');

	// Load helper library for directory manipulations
	$cms->load('helper','directory');

	// Checking #__jomcomment_config for language row.
	$strSQL	= "SELECT COUNT(*) FROM #__jomcomment_config WHERE `name`='language'";
	$cms->db->query($strSQL);

	// Insert default language config	
	if($cms->db->get_value() < 1){		
		$defaultLanguage	= $mainframe->getCfg('lang') . '.php';
		$strSQL	= "INSERT INTO #__jomcomment_config (`name` ,`value`) "
				. "VALUES ('language', '\$languages = array(\"language\" => \"{$defaultLanguage}\","
				. "\"\" => \"arabic\", \"\" => \"brazilian\", " 
				. "\"\" => \"bulgarian\", \"\" => \"czech\", "
				. "\"\" => \"danish\", \"\" => \"dutch\", "
				. "\"\" => \"english\", \"\" => \"estonian\", "
				. "\"\" => \"finnish\", \"\" => \"french\", "
				. "\"\" => \"german\", \"\" => \"germani\", "
				. "\"\" => \"greek\", \"\" => \"hebrew\", "
				. "\"\" => \"hindi\", \"\" => \"hrvatski\", "
				. "\"\" => \"hungarian\", \"\" => \"hungariani\", "
				. "\"\" => \"italian\", \"\" => \"japanese\", "
				. "\"\" => \"latvian\", \"\" => \"malay\", "
				. "\"\" => \"norwegian\", \"\" => \"polish\", "
				. "\"\" => \"russian\", \"\" => \"spanish\", "
				. "\"\" => \"srpski\", \"\" => \"srpski_cyr\", "
				. "\"\" => \"swedish\", \"autoLang\" => \"\" );')";
				
		$cms->db->query($strSQL);
		cmsRedirect('index2.php?option=com_jomcomment&task=language');
		exit;
	}
		
	$files	= cmsGetFiles(JC_COM_PATH . '/languages', '.php');
	$lists	= formatLanguage($files);

	include(JC_ADMIN_COM_PATH . '/templates/config_language.php');
}

function showConfig($option) {
	global $mainframe, $_JC_CONFIG;
	$cms    =& cmsInstance('CMSCore');

	/******************************************
	 * Get templates lists
	 ******************************************/
	/* CMS Compatibilities */
	if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
		$file_list = mosReadDirectory($cms->get_path('root') . "/components/com_jomcomment/templates", "");
	}
	else if(cmsVersion() == _CMS_JOOMLA15){
		$file_list = JFolder::folders($cms->get_path('root') . "/components/com_jomcomment/templates", "");
	}
	/* End CMS Compatibilities */

	$t_list = array ();
	$filecount = 0;

	$temp_lists = array();
	foreach ($file_list as $val) {
		if (!strstr($val, "svn") && !strstr($val, "_") && !strstr($val, "admin")){
        	#$t_list[] = mosHTML :: makeOption($val, $val);
        	$temp_lists[$val] = $val;
		}
	}
	/******************************************
	 * End Get templates lists
	 ******************************************/

	#$templates = mosHTML :: selectList($t_list, 'template', 'class="inputbox" size="1"', 'value', 'text', $_JC_CONFIG->get('template'));

	/******************************************
	 * Get Sections
	 ******************************************/

	$query = "SELECT id,title FROM #__sections ORDER BY title ASC";
	
	$cms->db->query($query);
	$rows		= $cms->db->get_object_list();
	$sections	= "<select name=\"sections[]\" size=\"" . count($rows) . "\" multiple>";
	$pc_section_array = explode(",", $_JC_CONFIG->get('sections'));

	foreach ($rows as $row) {
		$sections .= "<option value='$row->id' ";
		if (in_array($row->id, $pc_section_array))
			$sections .= "selected";
		$sections .= ">$row->title</option>";
	}
	$sections .= "</select>";
	$lookup_array = explode(",", $_JC_CONFIG->get('categories'));

	$lookup = array ();
	$sel_cat    = array();
	for ($i = 0; $i < count($lookup_array); $i++) {
		$lookup[$i] = new stdClass();
		$lookup[$i]->value = $lookup_array[$i];
		$sel_cat[$i]    = $lookup_array[$i];
	}
	/******************************************
	 * End Get Sections
	 ******************************************/

	/******************************************
	 * Get Categories
	 ******************************************/
	$categories	= array ();
	$cat_list   = array();
	
	$query		= "SELECT c.id AS `value`, c.section AS `id`, CONCAT_WS( ' / ', s.title, c.title) AS `text`" . "\n FROM #__sections AS s" . "\n INNER JOIN #__categories AS c ON c.section = s.id" . "\n WHERE s.scope = 'content'" . "\n ORDER BY s.name,c.name";
	$cms->db->query($query);
	$categories = array_merge($categories, $cms->db->get_object_list());
	
	foreach($categories as $cat){
	    $cat_list[$cat->value] = $cat->text;
	}
	/******************************************
	 * End Get Categories
	 ******************************************/
	//$name_list = array ();
	include ($cms->get_path('root') . "/administrator/components/com_jomcomment/templates/config_template.php");
}

function saveLanguageConfig($option){
	global $_JC_CONFIG, $mainframe;
	
	$cms	=& cmsInstance('CMSCore');
	
	require_once(JC_ADMIN_COM_PATH . '/config.jomcomment.php');

	$_JC_CONFIG->saveLanguage();
	
	jcClearCache();
	cmsRedirect('index2.php?option=com_jomcomment&task=language','Settings saved');	
}

function saveConfig($option) {

	global $database, $option, $_JC_CONFIG,$mainframe;
	$cms    =& cmsInstance('CMSCore');
	require_once ($cms->get_path('root') . "/administrator/components/com_jomcomment/config.jomcomment.php");
	$_JC_CONFIG->save();
	
	// Clear cache
	jcClearCache();
	
	// Need to rebuild the config cache
	unset($_JC_CONFIG);
	$_JC_CONFIG = new JCConfig();
	//$_JC_CONFIG->rebuildCache();
	
	$cms->load('libraries', 'cache');
	$cms->cache->clear();
	
	cmsRedirect("index2.php?option=com_jomcomment&task=config", "Settings saved");

	return;
}



function removeComments($cid, $option) {
	$cms    =& cmsInstance('CMSCore');
	
	if (count($cid)) {
		$cids = implode(',', $cid);
		$cms->db->query("DELETE FROM #__jomcomment WHERE `id` IN ({$cids})");
	}
	cmsRedirect("index2.php?option=$option&task=comments");
}
function removeTrackbacks($cid, $option) {
	global $database;
	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__jomcomment_tb WHERE id IN ($cids)");
		if (!$database->query()) {
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}
	cmsRedirect("index2.php?option=$option&task=trackbacks");
}
function showAjaxedAdmin() {
	global $database, $mainframe, $option;

	$cms    =& cmsInstance('CMSCore');
	
	$xajax = new JAX($cms->get_path('plugin-live') . "/system/pc_includes");
	if (cmsVersion() == _CMS_JOOMLA15){
		$xajax->setReqURI("index.php?option=com_jomcomment&no_html=1&hidemainmenu=1");
	} else{
		$xajax->setReqURI("index2.php?option=com_jomcomment&no_html=1&hidemainmenu=1");
	}
	
	$xajax->process();
	
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $cms->get_path('live') . "/components/com_jomcomment/admin_style-3.0.css\" />";
?>

<script src="<?php echo $cms->get_path('live'); ?>/includes/js/tabs/tabpane_mini.js" type="text/javascript"></script>
<?php echo $xajax->getScript(); ?>
<script type="text/javascript">
jax.loadingFunction = function(){jax.$('loadingDiv').style.display='block';};
jax.doneLoadingFunction = function(){jax.$('loadingDiv').style.display='none';};
</script>
<style type="text/css">

</style>
<table width="100%"  border="0" cellspacing="4" cellpadding="6">
  <tr>
    <td width="10%" valign="top" align="center">
<?php showSidePanel() ?>
</td>
    <td width="90%" align="left" valign="top"><?php showInstallError(); ?><div id="mainAdminContent">{CONTENT}</td>
</tr>
</table>
<?php } function getAdminMenuIcon($com){ global $database, $mainframe; $database->setQuery("SELECT admin_menu_img from #__components WHERE
		menuid=0 AND 
		`parent`=0  AND 
		`option`='$com'"); 
		
		$icon = $database->loadResult();
if ($icon == "js/ThemeOffice/component.png")
	$icon = $mainframe->getCfg('live_site') . "/includes/js/ThemeOffice/component.png";
return $icon;
}
function getPatchLink($com, $status) {
	$button = "";
	if ($status == JCHACK_STATUS_READY)
		$button = "<a onclick=\"jax.call('jomcomment', 'jcxDoPatch', '$com', 'apply');\" class=\"CommonTextButtonSmall\">Apply hacks</a>&nbsp;";
	else
		$button = "<a onclick=\"jax.call('jomcomment', 'jcxDoPatch', '$com', 'unapply');\"  class=\"CommonTextButtonSmall\">Restore backup</a>";
	return $button;
}
function showAvailableHacks() {
	global $jcPatchClass;
	$tpl = & new AzrulJXTemplate();
	$status = array ();
	$patches = array ();
	foreach ($jcPatchClass as $patch) {
		eval ("\$p = new $patch();");
		$patches[] = array (
			"name" => $p->name,
			"com" => $p->com,
			"files" => $p->files,
			"icon" => getAdminMenuIcon('com_' . $p->com
		), "status" => $p->getStatus(), "action" => getPatchLink($p->com, $p->getStatus()));
	}
	sort($patches);
	$tpl->set('patches', $patches);
	$tpl->set('publish', true);
	$html = $tpl->fetch(JC_ADMIN_COM_PATH . '/templates/hacks.tpl.html');
	echo $html;
}
function showInstallError() {
	global $database;
	
	$cms    =& cmsInstance('CMSCore');
	
	$cms->db->query("SELECT * FROM #__templates_menu");
	$template = $cms->db->get_value();
	
	if ($template) {
		$filename = $cms->get_path('root') . "/templates/$template/index.php";
		if (file_exists($filename)) {
			$handle = fopen($filename, "r");
			$contents = fread($handle, filesize($filename));
			fclose($handle);
			if (!strstr($contents, "mosShowHead")) {
			    if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
					echo '<div align="center"><span style="color: #FF0000;font-weight: bold;font-size:16px;">Please add &quot; &lt;?php mosShowHead(); ?&gt; &quot; code within the &lt;head&gt; ... &lt;/head&gt; tag in your current joomla template!</span></div>';
				}
				else if(cmsVersion() == _CMS_JOOMLA15){
				    # Do something?
				}
			}
		}
	}
}

function jcConvertTitleToUtf8(& $str) {
	if (function_exists('mb_convert_encoding')) {
		$iso = explode('=', _ISO);
		$source = mb_convert_encoding($str->text, "UTF-8", "$iso[1], auto");
		$str->text = $source;
	} else
		$str->text = transformDbText($str->text);
}

function jcExportEmail(){
	$db =& cmsInstance('CMSDb');
	$cms =& cmsInstance('CMSCore');
	
	include_once($cms->get_path('root') . "/components/com_jomcomment/includes/csv/csv.php");
	$db->query("SELECT DISTINCT email, name FROM #__jomcomment");
	$rows = $db->get_object_list();
	
	//$csv =  new CSVHandler('parish_only.csv', ',', 'pnumber');
	//$result = $csv->ReadCSV();
	
	if($rows){
		@ob_clean();
		header('Content-type: text/csv'); 
		header('Content-Disposition: attachment; filename="visitors.csv"');

		foreach($rows as $row){
			echo "{$row->email},{$row->name}\n";
		}
		exit;
	}

	//$objResponse = new JAXResponse();
	//$objResponse->addAlert($response);
	
	//return $objResponse->sendResponse();
}



function showSidePanel() {
	global $mainframe;
	$cms    =& cmsInstance('CMSCore');
?>

<link rel="stylesheet" type="text/css" href="<?php echo $cms->get_path('live'); ?>/components/com_jomcomment/niftyCorners.css">
<script type="text/javascript" src="<?php echo $cms->get_path('live'); ?>/components/com_jomcomment/nifty.js"></script>
<script type="text/javascript">
window.onload=function(){
    if(!NiftyCheck())
        return;
    Rounded("div#sideNav","all","#FFF","#F8F8F8","border #cccccc");
}
</script>

<div class="loading" id="loadingDiv" style="display:none;position: fixed;top: 0;z-index: 100;">
<img src="<?php echo $mainframe->getCfg('live_site');?>/components/com_jomcomment/busy.gif" width="16" height="16" align="absmiddle">&nbsp;Please Wait...
</div>


<div style="background-color:#F8F8F8" id="sideNav">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td scope="col">
<div>
  <h3 align="center">Control Panel </h3>
</div>
<div class="sideNavTitle"><img src="components/com_jomcomment/images/Options_16x16.gif" hspace="2" style="vertical-align:middle" >&nbsp;Configuration</div>
<div class="sideNavContent">
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=config">General Settings </a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=language">Language Settings</a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=maintd">Maintenance </a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=editLanguage">Edit Language File</a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=hacks">3rd Party Components Integration</a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=stats">View Statistics/Export Emails</a></div>
</div>
<div class="sideNavTitle"><img src="components/com_jomcomment/images/Documents_16x16.gif" hspace="2" style="vertical-align:middle" >&nbsp;View</div>
<div class="sideNavContent">
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=comments" >View Comment </a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=trackbacks" >View Trackbacks </a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=reports" >View Reports </a></div>
</div>
<div class="sideNavTitle" style="vertical-align:middle"><img src="components/com_jomcomment/images/Import_16x16.gif" hspace="2" style="vertical-align:middle" >&nbsp;Import</div>
<div class="sideNavContent">
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=import#ako">Import from Ako Comment </a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=import#combomax">Import from ComboMax </a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=import#moscom">Import from MosCom </a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=import#joomla">Import from !JoomlaComment</a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=import#mxcomment">Import from MXComments</a></div>
  </div><div class="sideNavTitle"><img src="components/com_jomcomment/images/Information_16x16.gif" hspace="2" style="vertical-align:middle" >&nbsp;About / Support </div>
<div class="sideNavContent">
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=about">About Jom Comment </a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=latestnews">Check for latest news</a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=license">License Information </a></div>
  <div class="sideNavItem"><a href="index2.php?option=com_jomcomment&task=support">Support</a></div>
  </div></td>
    </tr>
  </table>
</div>
<?php } 
