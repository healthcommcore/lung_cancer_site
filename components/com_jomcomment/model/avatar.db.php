<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 *
 **/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

// Include our custom cmslib if its not defined
if(!defined('CMSLIB_DEFINED'))
	include_once ((dirname((dirname(dirname(dirname(__FILE__)))))) . '/components/libraries/cmslib/spframework.php');

global $mainframe;

class JCAvatarDB{
	var $db = null;
	
	function JCAvatarDB(){
	    $this->db   =& cmsInstance('CMSDb');
	}
	
	function getFireboard($userId){
	    $strSQL = "SELECT `avatar` FROM #__fb_users WHERE `userid`='{$userId}'";
	    $this->db->query($strSQL);
		return $this->db->get_value();
	}
	
	function getCB($userId){
	    $strSQL = "SELECT `avatar` FROM #__comprofiler WHERE `user_id`='{$userId}' AND `avatarapproved`='1'";
	    
	    $this->db->query($strSQL);
		
		return $this->db->get_value();
	}
	
	function existsSMF(){
		$this->_originalDB();
	    $strSQL = "SELECT COUNT(*) FROM #__components WHERE `option`='com_smf'";
	    $this->db->query($strSQL);
	    
	    if($this->db->get_value()){
	        $strSQL = "SELECT `value1` FROM #__smf_config WHERE `variable`='smf_path'";
	        $this->db->query($strSQL);

			global $mainframe;
            mysql_select_db($mainframe->getCfg('db'), $this->db->db);
	        return $this->db->get_value();
		}
		return false;
	}
	
	function getSMFEmail($dbName , $dbPrefix, $email, $load = false){

		$this->_changeDB($dbName);

	    $selectType = 'COUNT(*)';
	    
	    if($load)
	        $selectType = '*';

	    $strSQL = "SELECT $selectType FROM `{$dbPrefix}members` WHERE `emailAddress`='{$email}'";

		$this->db->query($strSQL);

		$this->_originalDB();
		
		if($load){
		    // Return only 1 row of the specific user
		    $temp   = array();
		    $temp   = $this->db->get_object_list();
			return $temp[0];
		}
		return $this->db->get_value();
	}

	function getSMF($dbName, $dbPrefix, $id, $file = false){
	    $this->_changeDB($dbName);
	    $selectType = 'ID_ATTACH';
	    
	    if($file)
	        $selectType = 'filename';
	
	    $strSQL = "SELECT `{$selectType}`,`attachmentType` as `type` FROM `{$dbPrefix}attachments` WHERE "
				. "`ID_MEMBER`='{$id}' AND "
				. "`ID_MSG`='0'";

		$this->db->query($strSQL);

		$this->_originalDB();
		
		if($file){
		    // Need to return the object list
		    $temp   = array();
		    $temp   = $this->db->get_object_list();
			return $temp[0];
		}

		return $this->db->get_value();
	}
	
	function getSMFId($dbName, $dbPrefix, $email){
	
	    $this->_changeDB($dbName);

		$strSQL = "SELECT `ID_MEMBER` FROM `{$dbPrefix}members` WHERE `emailAddress`='{$email}'";

		$this->db->query($strSQL);
		$this->_originalDB();
		return $this->db->get_object_list();

	}

	function getSMFSettings($dbName, $dbPrefix){
	    $this->_changeDB($dbName);
		$strSQL = "SELECT * FROM {$dbPrefix}settings";
		$this->db->query($strSQL);
		$this->_originalDB();
		return $this->db->get_object_list();
	}
	
	function getSMFType(){

	    $this->_originalDB();
	    $strSQL = "SELECT `id` FROM #__components WHERE `option`='com_smf'";
	    $this->db->query($strSQL);
	    return $this->db->get_value();
	}

	function getSMFItemId(){

	    $this->_originalDB();
	    $strSQL 	= "SELECT `id` FROM #__menu WHERE `link`='index.php?option=com_smf' AND "
	            	. "`menutype`='mainmenu' AND "
	            	. "`published`='1'";
		$this->db->query($strSQL);
		
		$temp   = $this->db->get_value();
		
		if(!$temp){
		    // If we still cant find an itemid, search any itemid we can get
		    $strSQL = "SELECT `id` FROM #__menu WHERE `link` LIKE '%index.php?option=com_smf%'";
		    $this->db->query($strSQL);
			$temp   = $this->db->get_value();
		}
		
		return $temp;
	}


	function _originalDB(){
	    global $_JC_CONFIG, $mainframe;
	    mysql_select_db($mainframe->getCfg('db'), $this->db->db);
	}
	
	function _changeDB($dbName){
	    mysql_select_db($dbName, $this->db->db);
//	    var_dump($this->db->db);
//	   var_dump(mysql_select_db($dbName, $this->db->db));exit;
	}
}
?>
