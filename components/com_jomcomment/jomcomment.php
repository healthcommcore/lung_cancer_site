<?php

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

global $_JOMCOMMENT, $_JC_CONFIG;

include_once (dirname(__FILE__).'/main.jomcomment.php');

$cms = &cmsInstance('CMSCore');
$cms->load('helper','url');

define('JC_DEFAULT_LIMIT',20);
define('JC_TEMPLATE_PATH',$cms->get_path('root') . "/components/com_jomcomment/templates");

$task   	= cmsGetVar('task','','GET');

$controller = new JCController();

if(method_exists($controller, $task))
		$controller->$task();

class JCController{
	var $cms = null;

	function JCController(){
		$this->cms  = & cmsInstance('CMSCore');
	}
	
	function trackback(){
	    include_once(JC_TASK_PATH . '/trackback.php');
	}
	
	function jomadmin(){
	    include_once(JC_TASK_PATH . '/jomadmin.php');
	}
	
	function rss(){
	    include_once(JC_TASK_PATH . '/rss.php');
	}
	
	function img(){
		include_once(JC_TASK_PATH . '/img.php');
	}
	
	function azrul_ajax(){
		// Do nothing, since it ajax system will handle it
	}
	
	// Load current user's name and email
	function userinfo(){
	    include_once(JC_TASK_PATH . '/userinfo.php');
	}
	
	/**
	 * Jom Comment's comments administrations for users
	 **/
	function mycomments(){
        include_once(JC_TASK_PATH . '/admin.mycomments.php');
	}
	
	function mysubscriptions(){
		include_once(JC_TASK_PATH . '/admin.mysubscriptions.php');
	}

	function myfavorites(){
		include_once(JC_TASK_PATH . '/admin.myfavorites.php');
	}
}

function jcxLoadCommentForm($id = ''){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxLoadCommentForm($id);
}


function jcxLoadUserInfo($name, $email, $website) {
	global $_JOMCOMMENT;
	if ($name == "null")
		$name = "";
	if ($email == "null")
		$email = "";
	if ($website == "null")
		$website = "";
	return $_JOMCOMMENT->ajaxLoadUserInfo($name, $email, $website);
}

function jcxPreviewComment($comment){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxPreviewComment($comment);
}

function jcxAddComment($xajaxArgs) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->ajaxAddComment($xajaxArgs);
}
function jcxUpdateComment($cid, $com, $num) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxUpdateComment($cid, $com, $num);
}
function jcxUnpublish($uid, $com) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxUnpublish($uid, $com);
}
function jcxEdit($uid) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxEdit($uid);
}
function jcxSave($xajaxArgs, $saveit) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxSave($xajaxArgs, $saveit);
}
function jcxReport($id, $com, $referrer) {
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxReport($id, $com, $referrer);
}

function jcxVote($status, $id, $com){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxVote($status, $id, $com);
}

function jcxShowBookmarkThis($id, $com){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxShowBookmarkThis($id, $com);
}

function jcxShowTerms(){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxShowTerms();
}

function jcxShowEmailThis($id, $com){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxShowEmailThis($id, $com);
}

function jcxShowFavorites($id, $com){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxShowFavorites($id, $com);
}

function jcxShowComment($id){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxShowComment($id);
}

function jcxDismissReport($id){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxDismissReport($id);
}

// a user mark the current content as favorite
function jcxMyFav($cid, $option){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxMyFav($cid, $option);
}

function jcxRemoveFav($id){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxRemoveFav($id);
}

function jcxUnsubscribe($id){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxUnsubscribe($id);
}

function jcxEnableSubscription($id){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxEnableSubscription($id);
}

function jcxDisableSubscription($id){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxDisableSubscription($id);
}

function jcxShowSendEmail(){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxShowSendEmail();
}

function jcxSendEmail($recEmail, $sendName, $sendEmail, $sendSubject, $articleLink){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->jcxSendEmail($recEmail, $sendName, $sendEmail, $sendSubject, $articleLink);
}
