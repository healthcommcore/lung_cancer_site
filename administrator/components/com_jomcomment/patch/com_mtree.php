<?php

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.'); 

global $jcPatchClass;

$jcPatchClass['mtree'] = "JCComHacker_mtree";

class JCComHacker_mtree extends JCComHacker{
	var $name;
	var $status;
	var $com;
	var $files;
	
	function JCComHacker_mtree(){
		$this->name = "Moset Tree";
		$this->com  = "mtree";
		$this->files = "/components/com_mtree/mtree.php";
	}
	
	function doBackup(){
		$cms =& cmsInstance('CMSCore');
		$src = $cms->get_path('root') . $this->files;
		$dst = $cms->get_path('root') . "/administrator/components/com_jomcomment/patch/backup/mtree.php";

		return $this->_doBackup($src, $dst);
	}

	function restoreBackup(){
		$cms =& cmsInstance('CMSCore');
		$dst = $cms->get_path('root') . $this->files;
		$src = $cms->get_path('root') . "/administrator/components/com_jomcomment/patch/backup/mtree.php";
		
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
		
		$objResponse->addAssign('mtree_action', 'innerHTML', getPatchLink('mtree', $this->getStatus()));
		$objResponse->addAssign('mtree_status', 'innerHTML', $this->getStatus());
		return $objResponse->sendResponse();
	}
	
	function apply(){
		if(!$this->doBackup())
			return;
		
		$cms =& cmsInstance('CMSCore');
		$filename = $cms->get_path('root') . $this->files;
		$handle = fopen($filename, "rb");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		
		# Check if hacks has been applied.
		if(strpos($contents, 'JOM COMMENT HACKED BEGIN') === false){
			if(strpos($content, '$cache->call( \'viewlink_cache\', $link, $limitstart, $fields, $params, $option );') === FALSE){
				// older < 2.0 moset
				$contents = str_replace(
					'$cache->call( \'viewlink_cache\', $link, $limitstart, $custom_fields, $params, $option );', 
					'$cache->call( \'viewlink_cache\', $link, $limitstart, $custom_fields, $params, $option );
						/* JOM COMMENT HACKED BEGIN */
						global $mosConfig_absolute_path;

						include_once($mosConfig_absolute_path . "/mambots/content/jom_comment_bot.php");
						echo jomcomment($link->link_id, "com_mtree");
						/* JOM COMMENT HACKED END */
						', 
					$contents);
			} else {
				// newer > 2.0 moset
				$contents = str_replace(
					'$cache->call( \'viewlink_cache\', $link, $limitstart, $fields, $params, $option );', 
					'$cache->call( \'viewlink_cache\', $link, $limitstart, $fields, $params, $option );
						/* JOM COMMENT HACKED BEGIN */
						global $mosConfig_absolute_path;
						include_once($mosConfig_absolute_path. "/mambots/content/jom_comment_bot.php");
						echo jomcomment($link->link_id, "com_mtree");
						/* JOM COMMENT HACKED END */
						', 
					$contents);
			}
			
				
			$handle = fopen($filename, 'w');
			fwrite($handle, $contents);
			fclose($handle);
		}
	}
	
	# Return the status of the hack
	function getStatus(){
	
		$cms      =& cmsInstance('CMSCore');
		$filename = $cms->get_path('root') . "/components/com_mtree/mtree.php";
		
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
