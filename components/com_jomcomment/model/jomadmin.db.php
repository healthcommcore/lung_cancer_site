<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file is the jomadmins' file and will only needed to be included if admin or authors
 *  wants to publish / unpublish / remove comments from the email links.
 **/

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class JCJomAdmin{
	var $db     = null;
	
	function JCJomAdmin(){
	    $this->db   =& cmsInstance('CMSDb');
	}
	
	function publish($id){
		$strSQL = "UPDATE #__jomcomment SET `published`='1' WHERE `id`='{$id}'";
		$this->db->query($strSQL);
	}
	
	function unpublish($id){
	    $strSQL = "UPDATE #__jomcomment SET `published`='0' WHERE `id`='{$id}'";
	    $this->db->query($strSQL);
	}
	
	function delete($id){
	    $strSQL = "DELETE FROM #__jomcomment WHERE `id`='{$id}'";
		$this->db->query($strSQL);
	}
}

?>