<?php
/**
 * Jom Comment 
 * @package JomComment
 * @copyright (C) 2006 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/

# Don't allow direct linking
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class JCDataManager {
	var $_createError = "";
	var $_utf8 = null;
	var $db = null;
	
	function JCDataManager() {
		# set up utf8 object
		$this->_utf8 	= new Utf8Helper();
		$this->cms      =& cmsInstance('CMSCore');
		$this->db		=& cmsInstance('CMSDb');
	}

	/**
	 * Resturn the password given the secret id
	 */
	function getPassword($sid) {
		$db = &cmsInstance('CMSDb');
		
		$sid = $db->_escape($sid);
		$query = "SELECT `password` FROM #__captcha_session WHERE `sessionid`='$sid'";
		$db->query($query);
		$secCode = $db->get_value();
		
		$query = "DELETE FROM #__captcha_session where `sessionid`='$sid';";
		$db->query($query);
	
		return $secCode;
	}

	function deletePassword($sid) {
		$db = &cmsInstance('CMSDb');
		
		$sid = $db->_escape($sid);
		$query = "DELETE FROM #__captcha_session WHERE sessionid='$sid'";
		$db->query($query);
	}

	/**
	 * Return 1 comment object given the unique id. 
	 * Return null if not found
	 */
	function get($uid) {
	}

	function getFlood($username, $ip, $date, $postInterval) {
		 
		$db = &cmsInstance('CMSDb');
		
		$username 	= $db->_escape($username);
		$ip 		= $db->_escape($ip);
		$date 		= $db->_escape($date);
		$postInterval = intval($postInterval);
		
		$query = sprintf("SELECT count(*) FROM #__jomcomment" 
		. "\n WHERE name='%s'" 
		. "\n AND ip='%s'" 
		. "\n AND '%s' <= DATE_ADD(date, interval %d second);", 
		$username, $ip, $date, $postInterval);

		$db->query($query);
		
		return $db->get_value();
	}

	function getNumComment($cid, $option) {
		$db = &cmsInstance('CMSDb');
		$query = "";
		
		$option 	= $db->_escape($option);
		$cid 		= intval($cid);
		
		if ($option != "com_content" && $option != "com_myblog") {
			$query = "	SELECT count(*) FROM #__jomcomment" 
			. "\n WHERE `contentid`='$cid'" 
			. "\n AND `option`='$option'"
			. "\n AND `published`='1'";
		}else {
			$query = "	SELECT count(*) FROM #__jomcomment as a"
			. "\n WHERE `contentid`='$cid' "
			. "\n AND ( a.`option`='com_content' OR a.`option`='com_myblog')"
			. "\n AND `published`='1'";
		}

		$db->query($query);
		return $db->get_value();
	}

	/**
	 * Return the number of comment by the specific IP address
	 */	 	
	function getNumCommentByIP($ip, $date) {
		$db = &cmsInstance('CMSDb');
		$query = "SELECT count(*) FROM #__jomcomment" 
		. " WHERE `ip`='$ip'" 
		. " AND '$date' <= DATE_ADD(date, interval 1800 second);";

		$db->query($query);
		return $db->get_value();
	}

	/**
	 * return true if similar content already exist
	 */	 	
	function searchSimilarComments($contentid, $comment, $date) {
		$db = &cmsInstance('CMSDb');
		
		$contentid = intval($contentid);
		$query = "SELECT comment FROM #__jomcomment"
		. "\n WHERE"
		. "\n `contentid`=$contentid" 
		. "\n AND	`published`=1 "
		. "\n AND	'$date' <= DATE_ADD("
		. "\n 	date, " 
		. "\n 	INTERVAL 1800 second); ";
		
		$db->query($query);
		$rows = $db->get_object_list();

		if ($rows) {
			$len = strlen($comment);
			foreach ($rows as $row) {
				if (abs(strlen($row->comment) - $len) < ($len / 4)) {
					$percent = 0;
					similar_text($row->comment, $comment, $percent);
					if ($percent > 96) {
						return true;
					}

				}
			}
		}

		return false;
	}

	/**
	 * Return array of comments	for the given contentid and option
	 */
	function &getAll($cid, $option, $count, $page) {
		global $_JC_CONFIG;

		$db = &cmsInstance('CMSDb');
		$option 	= $db->_escape($option);
		$cid 		= intval($cid);

		# set the ordering
		$orderBy = " ORDER BY id DESC ";
		if ($_JC_CONFIG->get('sortBy') == 0) {
			$orderBy = " ORDER BY date DESC, id DESC ";
		} else {
			$orderBy = " ORDER BY date ASC, id ASC ";
		}

		# add limit
		$rule   = '';
		# Add pagination if necessary, We need to strip the data if pagination is active
		if($_JC_CONFIG->get('paging') && ($count > $_JC_CONFIG->get('paging'))){
            $next       = $page + (int) $_JC_CONFIG->get('paging');
			$rule    = ' LIMIT ' . $page . ',' . $next;
		}

		if ($option != "com_content" && $option != "com_myblog") {
		    $strSQL = "SELECT *, '-1' AS created_by FROM #__jomcomment"
		            . "\n WHERE `contentid`='$cid'"
		            . "\n AND `option`='$option'"
		            . "\n AND `published`='1' "
		            . $orderBy
		            . $rule;
			$db->query($strSQL);
		} else {
		    $strSQL = "SELECT a.*, b.created_by FROM #__jomcomment AS a, #__content AS b"
		            . "\n WHERE a.`contentid`='$cid'"
		            . "\n AND(a.`option`='com_content' OR a.`option`='com_myblog')"
		            . "\n AND a.`published`='1'"
		            . "\n AND b.`id`=a.`contentid` "
		            . $orderBy
		            . $rule;
			$db->query($strSQL);
		}
		$result = $db->get_object_list();
		$refArray = array();
		for($i =0; $i < count($result); $i++){
			$refArray[$result[$i]->id] = $i;
		}

		for($i =0; $i < count($result); $i++){
			$row =& $result[$i];
	
 			if($row->parentid != 0){
 				// search for parent array index
 				$parentIndex = $refArray[$row->parentid];
	 				
 				// unset the rows from the parent rows since this is a child
 				if(!isset($result[$parentIndex]))
 					$result[$parentIndex]->child = array();
	 				
 				$result[$parentIndex]->child[] = $row;
 			}
		}
		
		$finalResult = array();
		for($i =0; $i < count($result); $i++){
			if($result[$i]->parentid != 0){
				//unset($result[$i]);
			} else{
				$finalResult[] = $result[$i]; 
			}
		}
		

// 		echo '<pre>';
//  		print_r($finalResult);
//  		echo '</pre>';
//  		exit;
		return $finalResult;
	}

	/**
	 * Create a new jom comment object based on the given input
	 */
	function &create($xajaxArgs) {
		global $mainframe, $_JC_CONFIG;
		
		require (JC_ADMIN_COM_PATH . '/class.jomcomment.php');
		$this->cms->load('libraries','user');
		
		$this->_createError = "";
		$data = new mosJomcomment();

		# more data
		$option = isset ($xajaxArgs['jc_option']) ? $xajaxArgs['jc_option'] : "com_content";
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = strftime("%Y-%m-%d %H:%M:%S", time() + ($mainframe->getCfg('offset') * 60 * 60));
		$email = isset ($xajaxArgs['jc_email']) ? $xajaxArgs['jc_email'] : $this->cms->user->email;
		$username = isset ($xajaxArgs['jc_name']) ? $xajaxArgs['jc_name'] : $this->cms->user->name;
		
		# fix data with different name
		$arrayInput = array (
			"comment" => "jc_comment",
			"contentid" => "jc_contentid",
			"title" => "jc_title",
			"option" => "jc_option",
			"website" => "jc_website",
			"name" => "jc_username",
			"_password" => "jc_password",
			"_subscribe" => "subscribe",
			'parentid'	=> 'jc_parentid',
			"_sid" => "jc_sid",
			"_recaptchaResponse"	=> "recaptcha_response_field",
			"_recaptchaChallange"	=> "recaptcha_challenge_field"
		);

		foreach ($arrayInput as $key => $value) {
			if (!isset ($xajaxArgs[$value]))
				$xajaxArgs[$value] = "";

			$xajaxArgs[$key] = $xajaxArgs[$value];
		}

		# If more_info is set, we get username from args or the login name
		$username = "";
		$name_field = $_JC_CONFIG->get('username');
		
		$username	= $xajaxArgs['jc_name'];
//		We dont use this anymore because if an administrator disables the option
//		for real name, we will use a hidden input.
// 		if ($_JC_CONFIG->get('moreInfo')) {
// 			if ($this->cms->user->username) {
// 				$username = isset ($xajaxArgs['jc_name']) ? $xajaxArgs['jc_name'] : $this->cms->user->$name_field;
// 			} else {
// 				// no username supplied, just give a blank username
// 				$username = isset ($xajaxArgs['jc_name']) ? $xajaxArgs['jc_name'] : "";
// 			}
// 		} else {
// 			// if more_info is not set, set all unregistered user as guest
// 			if ($this->cms->user->name) {
// 				$username = $this->cms->user->$name_field;
// 			} else {
// 				$username = $this->_utf8->utf8ToHtmlEntities(_JC_GUEST_NAME);
// 			}
// 		}

		if (!$_JC_CONFIG->get('moreInfo') and !$email)
			$email = "#";
			
		if (!$_JC_CONFIG->get('moreInfo') and !$username)
			$username = $this->_utf8->utf8ToHtmlEntities(_JC_GUEST_NAME);

		if ($email == "#" AND $this->cms->user->email)
			$email = $this->cms->user->email;

		$xajaxArgs['id'] 		= 0;
		$xajaxArgs['date'] 		= $date;
		$xajaxArgs['user_id'] 	= $this->cms->user->id;
		$xajaxArgs['ip'] 		= $ip;
		$xajaxArgs['email'] 	= $email;
		$xajaxArgs['name'] 		= $username;
		$xajaxArgs['published'] = intval($_JC_CONFIG->get('autoPublish'));

		# bind the array, the private data has to be added manually
		$data->bind($xajaxArgs);
		$data->_sid = $xajaxArgs['jc_sid'];
		$data->_password = isset($xajaxArgs['jc_password']) ? $xajaxArgs['jc_password']: "";
		$data->comment = $xajaxArgs['jc_comment'];
	
		# must strip html tags from input
		$data->comment 	= strip_tags($data->comment, $_JC_CONFIG->get('allowedTags'));
		$data->website 	= strip_tags($data->website);
		$data->name 	= strip_tags($data->name);
		$data->title 	= strip_tags($data->title);

		# validate required fields
		return $data;
	}

	/**
	 * Return the las
	 */
	function getCreateError() {
		return $this->_createError;
	}

	/**
	 * Enough said!
	 */
	function publish($uid) {
		$uid = intval($uid);
		$db = &cmsInstance('CMSDb');
		$db->query("UPDATE #__jomcomment" 
		. "\n SET published='1'" 
		. "\n WHERE id='$uid'");
	}

	/**
	 * Enough said!
	 */
	function unpublish($uid) {
		$uid = intval($uid);
		$db = &cmsInstance('CMSDb');
		$db->query("UPDATE #__jomcomment" 
		. "\n SET published='0'" 
		. "\n WHERE id='$uid'");
	}
	
	/**
	 * Enough said!
	 */
	function delete($uid) {
		$uid = intval($uid);
		$db = &cmsInstance('CMSDb');
		$db->query("DELETE FROM #__jomcomment WHERE id='$uid'");
	}
}
