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

class JCTrackbackDB{
	var $db = null;
	
	function JCTrackbackDB(){
	    $this->db   =& cmsInstance('CMSDb');
	}
	
	function exists($type, $contentId , $url = ''){
		if($type == 'sent'){
		    $strSQL = "SELECT COUNT(*) FROM `#__jomcomment_tb_sent` WHERE `url`='{$url}'";
		}else{
			$strSQL = "SELECT COUNT(*) FROM `#__jomcomment_tb` WHERE `contentid`='{$contentId}' AND `url`='{$url}'";
		}
		$this->db->query($strSQL);
		return ($this->db->get_value() > 0);
	}
	
	function insert($type, $contentId, $url){
	    if($type == 'sent')
	    	$strSQL = "INSERT INTO `#__jomcomment_tb_sent` SET `url`='{$url}', `contentid`='{$contentId}'";
		$this->db->query($strSQL);
	}
	
	function lists($contentId, $option, $count = false){
	    $search = $option;
		$type   = '*';

		// Check if current component is myblog or content
		if($option == 'com_myblog' || $option == 'com_content')
			$search ="com_myblog' OR `option`='com_content";

		if($count){
		    $type   = 'COUNT(*)';
		}

		$strSQL = "SELECT $type FROM #__jomcomment_tb WHERE `contentid`='{$contentId}' "
				. "AND `published`='1' "
				. "AND (`option`='{$search}') "
				. "ORDER BY `id` DESC";

		$this->db->query($strSQL);

		if($count)
		    return $this->db->get_value();

		return $this->db->get_object_list();
	}
}

class JCTrackbackDBTable extends CMSDBTable{
	//var $id     	= null; 			// Trackback id
	var $contentid  = null; 			// Content id of the trackback
	var $ip         = null; 			// IP address of the trackback
	var $date       = null; 			// Trackback date

	var $title      = null;				// Trackback title
	var $excerpt    = null;				// Trackback excerpt
	var $url        = null; 			// Trackback url
	var $blog_name  = null; 			// Trackback blog name
	var $charset    = null; 			// Trackback charset
	var $published  = '1';  			// Trackback default publish status
	var $option     = 'com_content';    // Trackback default option

	function JCTrackbackDBTable(){
	    $this->CMSDBTable('#__jomcomment_tb', 'id');
	}
}

class JCTrackbackSentDBTable extends CMSDBTable{
	var $id     	= null; 			// Trackback id
	var $contentid  = null; 			// Content id of the trackback
	var $url        = null; 			// IP address of the trackback

	function JCTrackbackSentDBTable(){
	    $this->CMSDBTable('#__jomcomment_tb_sent', 'id');
	}
}
?>