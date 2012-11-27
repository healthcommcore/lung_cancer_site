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

class JCCaptchaDB{

	var $table  = '#__captcha_session';
	var $key    = 'id';
	var $db     = null;
	
	function JCCaptchaDB(){
	    $this->db   =& cmsInstance('CMSDb');
	}
	
	function clean(){
	    $strSQL = "DELETE FROM `$this->table` WHERE DATE_SUB(CURDATE(), INTERVAL 1 DAY) <= date";
		$this->db->query($strSQL);
	}
	
	function add($securityId = '', $password = ''){
		$strSQL = "INSERT HIGH_PRIORITY INTO `$this->table` SET `sessionid`='{$securityId}', `password`='{$password}'";
		$this->db->query($strSQL);
	}
	
}
?>