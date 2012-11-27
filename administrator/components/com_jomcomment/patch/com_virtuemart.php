<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.'); 
global $jcPatchClass;

$jcPatchClass['virtuemart'] = "JCComHacker_virtuemart";

class JCComHacker_virtuemart extends JCComHacker{
	var $name;
	var $status;
	var $com;
	var $files;
	var $backup;
	
	function JCComHacker_virtuemart(){
		$this->name = "Virtuemart";
		$this->com  = "virtuemart";
		$this->files = "/administrator/components/com_virtuemart/html/shop.product_details.php";
		$this->backup = "/administrator/components/com_jomcomment/patch/backup/shop.product_details.php";
	}
	
	function doBackup(){
		$cms =& cmsInstance('CMSCore');
		$src = $cms->get_path('root') . $this->files;
		$dst = $cms->get_path('root') . $this->backup;
		
		return $this->_doBackup($src, $dst);
	}
	
	function restoreBackup(){
		$cms =& cmsInstance('CMSCore');
		$dst = $cms->get_path('root') . $this->files;
		$src = $cms->get_path('root') . $this->backup;

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

		$objResponse->addAssign($this->com . '_action', 'innerHTML', getPatchLink($this->com, $this->getStatus()));
		$objResponse->addAssign($this->com . '_status', 'innerHTML', $this->getStatus());
		return $objResponse->sendResponse();
	}

	function apply(){
		if(!$this->doBackup())
			return;

		$cms      =& cmsInstance('CMSCore');
		$filename = $cms->get_path('root') . $this->files;
		$handle = fopen($filename, "rb");
		$contents = fread($handle, filesize($filename));
		fclose($handle);

		# Check if hacks has been applied.
		if(strpos($contents, 'JOM COMMENT HACKED BEGIN') === false){
			$contents = str_replace(
				'/* LINK TO VENDOR-INFO POP-UP **/', '
					/* JOM COMMENT HACKED BEGIN */
					global $mosConfig_absolute_path;
					include_once($mosConfig_absolute_path. "/mambots/content/jom_comment_bot.php");
					$product_reviews .= jomcomment($product_id, "com_virtuemart");
					/* JOM COMMENT HACKED END */
					/* LINK TO VENDOR-INFO POP-UP **/
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
		$filename = $cms->get_path('root') . $this->files;
		
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
