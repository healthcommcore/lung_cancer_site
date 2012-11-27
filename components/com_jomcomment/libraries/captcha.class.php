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

class JCCaptcha{
	var $cms    			= null;
	var $length				= 5;
	var $characters 		= 'abcdefghijklmnopqrstuvwxyz';
	var $password   		= null;
	var $imageColor 		= null;
	var $imageBackground    = null;
	var $imageFont          = null;
	var $model              = null;
	
	function JCCaptcha($stringLength = null){
	    $this->cms  =& cmsInstance('CMSCore');
	    
	    if($stringLength)
	        $this->length   = $stringLength;

		// Load the model so that it could perform any db operations
		// The captcha.db.php file should be included in the task file
		// We need to check if it is included or not.
		if(!defined('JCCaptchaDB')){
			include_once(JC_COM_PATH . '/model/captcha.db.php');
		}
		
		// Instantiate the model object
		$this->model    = new JCCaptchaDB();
		
		// Set the image background.
		$this->imageBackground  = imagecreatefrompng(JC_COM_PATH . '/includes/fonts/bg.png');
		
		// Set the image text color.
		$this->imageColor       = imagecolorallocate($this->imageBackground, 100, 100, 100);
		
		// Set the image font type.
		$this->imageFont        = imageloadfont(JC_COM_PATH . '/includes/fonts/dreamofme.gdf');
	}

	/**
	 *  _randomId()
	 *  Returns: Password has been set or not (boolean)
	 **/
	function _randomId($securityId){
	    $max    		= strlen($this->characters) - 1;
	    $password   	= NULL;
	    $realPassword	= NULL;
	    $tempNum     	= array();
	    
	    for($i = 0; $i < $this->length; $i ++){
	        $tempNum[$i]    = $this->characters{mt_rand(0, $max)};
	        
	        $password       .= "" . $tempNum[$i];
	        $realPassword   .= "" . $tempNum[$i];
		}
		
		if(rand(1,10) < 3){
		    // Execute delete query
			$this->model->clean();
		}
		// Insert captcha query
		$this->model->add($securityId, $realPassword);
		
		$this->password = $realPassword;
		
		return true;
	}

	/**
	 *  _randomId()
	 *  Prints the captcha image data.
	 *  This function will exit the execution as it is required to display the image and should not return any data.
	 *  Returns: null
	 **/
	function show($securityId = ''){
		if(!isset($securityId) || empty($securityId)){
		    echo 'ERROR: SID Incorrect.';
		} else {
		    if($this->_randomId($securityId)){
		        // Print image to user.
		        imagestring($this->imageBackground, $this->imageFont, 15, 10, $this->password, $this->imageColor);
		        header('Content-type: image/png');
		        imagepng($this->imageBackground);
			}
		}
		exit();
	}
}
?>