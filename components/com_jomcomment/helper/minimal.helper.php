<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file stores most of the minimal used functions instead of including
 * all the functions from functions.jomcomment.php
 **/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

// Include our custom cmslib
if(!defined('CMSLIB_DEFINED'))
	include_once ((dirname(dirname(dirname(__FILE__)))). '/components/libraries/cmslib/spframework.php');

/**
 * jcCountComment -- To calculate the comment count for specific item
 * use this function.
 * @param $articleId: unique id of the current requested item.
 * @param $option: components name e.g com_content
 * Returns: Comments count (int)
 **/
function jcCountComment($articleId, $option = 'com_content'){
	// Store result statically so that we would not have to
	// keep on querying the database to get the value.
	static $result  = array();
	
	$db =& cmsInstance('CMSDb');
	
	if(!isset($result['' . $articleId . $option])){
	    $searchBy   = '';
	    
		if($option == 'com_content' || $option == 'com_myblog')
			$option = "com_content' OR `option`='com_myblog";

		$strSQL = "SELECT COUNT(*) FROM #__jomcomment WHERE `contentid`='{$articleId}' AND (`option`='$option') AND `published`='1'";
		$result['' . $articleId . $option]	=	$db->get_value($strSQL);
	}
	
	return $result['' . $articleId . $option];
}

/**
 * jcCountContentHit -- To calculate the comment count for specific content id
 * use this function.
 * @param $contentId: unique id of the current requested item.
 * Returns: Hits count (int)
 **/
function jcCountContentHit($contentId){
	$db =& cmsInstance('CMSDb');
	
	$strSQL = "SELECT `hits` FROM #__content WHERE `id`='{$contentId}'";
	
	return $db->get_value($strSQL);
}
?>