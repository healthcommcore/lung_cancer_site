<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file is the captcha's image file and will only needed to be included if captcha image
 * is enabled or be required to be displayed
 **/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

// Include our custom cmslib if its not defined
if(!defined('CMSLIB_DEFINED'))
	include_once ((dirname((dirname(dirname(dirname(__FILE__)))))) . '/components/libraries/cmslib/spframework.php');

class JCRssDB{
	var $db     = null;
	
	function JCRssDB(){
	    $this->db   =& cmsInstance('CMSDb');
	}
	
	function getTitle($contentId = '', $tableName = ''){
	    if(!$contentId){
	        return false;
		}
		
		if(!$tableName){
		    $tableName  = '#__content';
		}
		
	    $strSQL = "SELECT `title` FROM `{$tableName}` WHERE `id`='{$contentId}' AND `state`='1'";
	    $this->db->query($strSQL);
	    return $this->db->get_value();
	}
	
	function getComments($option = '', $limit = 20, $id = ''){
	    $where   = '';
	    
		if($option)
		    $where .= " AND `option`='$option' ";

		if($id)
		    $where .= " AND `contentid`='{$id}' ";

	    $strSQL = "SELECT *, UNIX_TIMESTAMP(date) AS `created_ts` FROM `#__jomcomment` WHERE `published`='1' $where"
	            . "ORDER BY `date` DESC LIMIT 0,$limit";

		$this->db->query($strSQL);
		return $this->db->get_object_list();
	}
	
	function getCommentsCount($option, $contentId = ''){
	    $content = '';

	    if($contentId){
	        $content = "AND `contentid`='{$contentId}' ";
		}
		
	    $strSQL = "SELECT COUNT(*) FROM #__jomcomment WHERE `published`='1' AND `option`='$option' $content";
	    $this->db->query($strSQL);
	    return $this->db->get_value();
	}
}
?>