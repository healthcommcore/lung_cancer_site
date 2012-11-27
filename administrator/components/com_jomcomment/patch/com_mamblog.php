<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.'); 
global $jcPatchClass;

$jcPatchClass['mamblog'] = "JCComHacker_mamblog";

class JCComHacker_mamblog extends JCComHacker{
	var $name;
	var $status;
	var $com;
	var $files;
	var $backup;
	
	function JCComHacker_mamblog(){
		$this->name = "Mamblog";
		$this->com  = "mamblog";
		$this->files = "/components/com_mamblog/showblogs.php";
	}
	
	function doBackup(){
	    $cms =& cmsInstance('CMSCore');

		$src = $cms->get_path('root') . $this->files;
		$dst = $cms->get_path('root') . "/administrator/components/com_jomcomment/patch/backup/showblogs.php";
		
		return $this->_doBackup($src, $dst);
	}
	
	function restoreBackup(){
	    $cms =& cmsInstance('CMSCore');

		$dst = $cms->get_path('root') . "/components/com_mamblog/showblogs.php";
		$src = $cms->get_path('root') . "/administrator/components/com_jomcomment/patch/backup/showblogs.php";
		
		return copy($src, $dst);
	}
	
	function action($task){
		$objResponse = new JAXResponse();
		switch($task){
			case "apply":
				$this->apply();
				$objResponse->addAlert("Patch applied.");
				break;
				
			case "unapply":
				$this->restoreBackup();
				$objResponse->addAlert("backup restored");
				break;
				
			case "refresh":
				break;
		}
		
		# Refresh the data
		
		$objResponse->addAssign('mamblog_action', 'innerHTML', getPatchLink('mamblog', $this->getStatus()));
		$objResponse->addAssign('mamblog_status', 'innerHTML', $this->getStatus());
		return $objResponse->sendResponse();
	}
	
	function apply(){
		if(!$this->doBackup())
			return;

		$cms    =& cmsInstance('CMSCore');

		$filename = $cms->get_path('root') . "/components/com_mamblog/showblogs.php";
		$handle = fopen($filename, "rb");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		
		# Check if hacks has been applied.
		if(strpos($contents, 'JOM COMMENT HACKED BEGIN') === false){
			$contents = str_replace(
				'HTML_mamblog::show( $row, $mask, $gid, $option );', 
				'HTML_mamblog::show( $row, $mask, $gid, $option );
				/* JOM COMMENT HACKED BEGIN */
				global $mosConfig_absolute_path;
				
				include_once($mosConfig_absolute_path . "/mambots/content/jom_comment_bot.php");
				echo jomcomment($row->id, "com_mamblog");
				/* JOM COMMENT HACKED END */
					', 
				$contents);
				
			$handle = fopen($filename, 'w');
			fwrite($handle, $contents);
			fclose($handle);
		}
	}
	
	# Return the status of the hack
	function getStatus(){
	
		$cms      =& cmsInstance('CMSCore');
		$filename = $cms->get_path('root') . "/components/com_mamblog/showblogs.php";
		
		$this->status = JCHACK_STATUS_NO_COM;
		
		if(!file_exists($filename)){
			$this->status = JCHACK_STATUS_NO_COM;
		} else {
			$handle = fopen($filename, "rb");
			$contents = fread($handle, filesize($filename));
			fclose($handle);
			if(strpos($contents, 'JOM COMMENT HACKED BEGIN') === false){
				$this->status = JCHACK_STATUS_READY;
					
			} else {
			
				$this->status = JCHACK_STATUS_APPLIED;
			}
		}
		
		return $this->status;
	}
}
