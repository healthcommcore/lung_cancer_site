<?php

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

define('JCHACK_STATUS_READY', 	"Ready");
define('JCHACK_STATUS_NO_COM', 	"Not available");
define('JCHACK_STATUS_APPLIED', "Patch applied");

class JCHacks {
	
}

class JCComHacker{
	var $name;
	var $status;
	
	function _doBackup($src, $dst){
		# Check if the src file is clean. If it is, create a new backup
				
		return copy($src, $dst);
	}
	
	function doBackup(){
		
	}
	
	function restoreBackup($filename, $dest){
	}
	
	function apply(){
	}
	
	function getStatus(){
	}
}
