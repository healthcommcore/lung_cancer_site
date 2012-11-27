<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file is responsible for getting the avatars and displaying avatars
 **/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

// Include our custom cmslib if its not defined
if(!defined('CMSLIB_DEFINED'))
	include_once ((dirname((dirname(dirname(dirname(__FILE__)))))) . '/components/libraries/cmslib/spframework.php');

if(!class_exists('JCAvatarDB'))
	include_once(JC_MODEL_PATH . '/avatar.db.php');

if( class_exists('JCAvatar') || class_exists('JCAvatarCB') ||
	class_exists('JCAvatarFireboard') || class_exists('JCAvatar') ||
	class_exists('JCAvatarSMF') )
	return;

class JCAvatar{

	var $cms    	= '';
	var $default    = '';
	var $model      = null;
	var $userId     = null;
	var $email      = null;
	var $source     = null;     // Source of the avatar image.
	
	var $_width     = null;
	var $_height    = null;
	function JCAvatar($userId, $email = null){
		global $_JC_CONFIG;
		
	    $this->cms  =& cmsInstance('CMSCore');
		$this->cms->load('helper','url');

		// Instantiate the model object
		$this->model    = new JCAvatarDB();
		
	    // Default avatar if avatar could not be found
	    $this->default	= $this->cms->get_path('live') . '/components/com_jomcomment/smilies/guest.gif';
	    
	    $this->userId   = $userId;
	    
	    if($email)
			$this->email    = $email;
			
		$this->_width   = $_JC_CONFIG->get('gWidth');
		$this->_height  = $_JC_CONFIG->get('gHeight');
	}

	/**
	 *  _link()
	 *  Returns: The link to the avatar component. E.g: fireboard (string)
	 **/
	function link($userId, $userEmail = null, $ItemId = null){
	    global $_JC_CONFIG;
		
		if($userId == '0')
			return false;

		switch ($_JC_CONFIG->get('gravatar')) {
			case "cb" :
				$link	= $this->_link($userId, $ItemId);
				break;
			case "smf":
				$link	= $this->_link($userId, $userEmail);
			default:
			    $link   = false;
		}
		return $link;
	}

	/**
	 *  _img()
	 *  Returns: The image of the specific avatar (string)
	 **/
	function img(){
	    if($this->_init())
	        return "<img src=\"{$this->source}\" height=\"{$this->_height}\" width=\"{$this->_width}\" border=\"0\" alt=\"{$this->userId}\" />";
		else
		    return "<img src=\"{$this->default}\" height=\"{$this->_height}\" width=\"{$this->_width}\" border=\"0\" alt=\"{$this->userId}\" />";
	}
}

class JCAvatarFireboard extends JCAvatar{
	// Private properties
	var $_config 	= null;

	function JCAvatarFireboard($userId){
		parent::JCAvatar($userId);

		$this->_config  = $this->cms->get_path('root') . '/administrator/components/com_fireboard/fireboard_config.php';

		// Test if this is prior to 1.0.5
		if(!file_exists( $this->_config) ){
			// We assume it might be version 1.0.5 and above
			$this->_config	= $this->cms->get_path('root') . '/components/com_fireboard/sources/fb_config.class.php';
			
			if( file_exists($this->_config) )
			{
				require_once( $this->_config );
				global $fbConfig;
				
				$this->_config	= new fb_config();
				$this->_config->load();
			}
		}
	}
	
	function _loadVars()
	{
		if( !is_object( $this->_config ) )
		{
			require( $this->_config );
			global $fbConfig;
		
			$obj	= new stdclass();
				
			$obj->avatar_src	= $fbConfig['avatar_src'];
			$obj->version		= $fbConfig['version'];
			
			$this->_config	= $obj;
		}
		
	}
	
	function _init(){
		
	    if( !is_object($this->_config) && !file_exists($this->_config) ){
	        return false;
		}
		
		$this->_loadVars();

		echo '<pre>';
		print_r($this->_config);
		echo '</pre>';
		if($this->_config->avatar_src == 'fb'){
		    $path   = $this->model->getFireboard($this->userId);
		    
		    if($path){
		        $fireboardAvatar    = '';
		        
				if(!is_null($this->_config->version) && isset($this->_config->version) && $this->_config->version == '1.0.1')
				{
				    $fireboardAvatar    = '/components/com_fireboard/avatars/' . $path;
				} else {
				    // Newer versions
				    $fireboardAvatar    = '/images/fbfiles/avatars/' . $path;
				}
				
				if(file_exists($this->cms->get_path('root') . $fireboardAvatar)){
					$this->source   = $this->cms->get_path('live') . $fireboardAvatar;
				} else {
				    // Image file doesn't exist.
				    return false;
				}
			}else {
			    // Not set avatar yet.
			    return false;
			}
		} else if ($this->_config->avatar_src != 'cb') {
			// This is for handling cases when fireboard avatar source is Clexus PM
			// we just use the default avatar for this.
			return false;
        }
		return true;
	}
}

class JCAvatarSMF extends JCAvatar{
	var $_config    = null;
	var $_path      = null;
	
	function JCAvatarSMF($userId, $email){
	    global $_JC_CONFIG;
	    
	    parent::JCAvatar($userId, $email);
	    $this->_path  	= rtrim(trim($_JC_CONFIG->get('smfPath')), '/');
		$this->_config  = $this->_path . '/Settings.php';

		if(!$this->_path || $this->_path == null || !file_exists($this->_config)){
		    // If path specified in admin section is invalid or does not exists,
		    // we assume that the default would be in /forum
		    $this->_path    = $this->cms->get_path('root') . '/forum';
		}

		// If the folder /forum still does not exists, we need to check with the database.
		if(!$this->_path || $this->_path == null || !file_exists($this->_config)){
		    if($this->_path = $this->model->existsSMF()){
		        $this->_path    = rtrim( str_replace("\\", "/", $this->_path), "/");
			}
		}
		
	    
	}
	
	function _init(){
		// Check if configurations exists.
		if(file_exists($this->_config)){
			global $context, $txt, $smfSettings, $mainframe, $db_name, $db_prefix;

			include($this->_config);

			$smfSettings    = $this->_settings($db_name, $db_prefix);

			// Check if email even exists in SMF database
			if($this->model->getSMFEmail($db_name, $db_prefix, $this->email) == 0){
			    return false;
			}
			
			if($user = $this->model->getSMFEmail($db_name, $db_prefix, $this->email, true)){
			     $user->ID_ATTACH   = $this->model->getSMF($db_name, $db_prefix, $user->ID_MEMBER);

			     $context   = array();
			     
			     if($user->avatar == '' && $user->ID_ATTACH > 0){
			        $context['member']['avatar']    = array('choice'    => 'upload',
			                                                'server_pic'=> 'blank.gif',
			                                                'external'  => 'http://'
															);
				 }else if(stristr($user->avatar, 'http://')){
				    $context['member']['avatar']    = array('choice'	=> 'external',
				                                            'server_pic'=> 'blank.gif',
				                                            'external'  => $user->avatar
															);
				 }else if(file_exists($smfSettings['avatar_directory'] . '/' . $user->avatar)){
				    $context['member']['avatar']    = array('choice' => 'server_stored',
															'server_pic' => $user->avatar == '' ? 'blank.gif' : $user->avatar,
															'external' => 'http://'
															);
				 }else{
				    $context['member']['avatar']    = array('choice'	=> 'server_stored',
				                                            'server_pic'=> 'blank.gif',
				                                            'external'  => 'http://'
															);
				 }
				 
				 if(@$context['member']['avatar']['allow_server_stored']){
				    $context['avatar_list'] = array();
				    $context['avatars']     = is_dir($smfSettings['avatar_directory']) ? $this->_lists('', 0) : array();
				 } else {
				    $context['avatars']     = array();
				 }
				 
				 // Second level selected avatars
				 $context['avatar_selected']    = substr(strrchr($context['member']['avatar']['server_pic'], '/'), 1);

				 switch($context['member']['avatar']['choice']){
				    case 'external':
				        $this->source   = $user->avatar;
				        break;
					case 'upload':
					    // User could upload their own avatar.
					    $objData = $this->model->getSMF($db_name, $db_prefix, $user->ID_MEMBER, true);

					    if($objData->type == '1'){
					        // Admin specified another directory apart from the default avatars.
					        $this->source   = $smfSettings['custom_avatar_url'] . '/' . $objData->filename;
						}
						else
					    	$this->source   = $boardurl . '/index.php?action=dlattach;attach=' . $user->ID_ATTACH . ';type=avatar';
					    break;
					case 'server_stored':
					    if(!empty($smfSettings['custom_avatar_url'])){
					        $path	= $this->model->getSMF($db_name, $db_prefix, $user->ID_MEMBER, true);
							if(!empty($path)){
							    if(stristr($smfSettings['custom_avatar_url'], 'http://')){
									$this->source   = $smfSettings['custom_avatar_url'] . '/' . $path;
								}
								else
								    $this->source   = rtrim($boardurl, '/') . '/' . $smfSettings['custom_avatar_url'] . '/' . $path;
							} else {
							    $this->source   = $smfSettings['avatar_url'] . '/' . $user->avatar;
							}
						} else {
						    $this->source   = $smfSettings['avatar_url'] . '/' . $user->avatar;
						}
						break;
					default:
					    return false;
				 }
				 
			} else {
			    // Email not found.
			    return false;
			}
		}else
		    return false;

		return true;
	}

	function _link($userId, $userEmail){
	    global $_JC_CONFIG;
	    
		if(substr($this->_path, strlen($this->_path) - 1, 1) == "/")
				$this->_path = substr($this->_path, 0, strlen($this->_path) - 1);

		// Check if configurations exists.
		if(file_exists($this->_config)){
			include($this->_config);

			if($result = $this->model->getSMFId($db_name, $db_prefix, $userEmail)){

			    $wrap   = '';

				// Administrator could force wrap to smf profile page in Jom Comment settings.
				if($_JC_CONFIG->get('smfWrapped'))
				    $wrap   = true;

				if($wrap){
					$link   = cmsSefAmpReplace('index.php?option=com_smf&action=profile&u=' . $result[0]->ID_MEMBER . '&Itemid=' . $this->model->getSMFItemId());
				} else
				    $link   = cmsSefAmpReplace($boardurl . '/index.php?action=profile&u=' . $result[0]->ID_MEMBER);
			} else
			    return false;
		} else
		    return false;
		    
		return $link;
	}

	// Recursive function to retrieve avatar files
	function _lists($directory, $level){
		global $context, $txt, $smfSettings;

		$result = array();

		// Open the directory..
		$dir = dir($smfSettings['avatar_directory'] . (!empty($directory) ? '/' : '') . $directory);
		$dirs = array();
		$files = array();

		if (!$dir)
			return array();

		while ($line = $dir->read())
		{
			if (in_array($line, array('.', '..', 'blank.gif', 'index.php')))
				continue;

			if (is_dir($smfSettings['avatar_directory'] . '/' . $directory . (!empty($directory) ? '/' : '') . $line))
				$dirs[] = $line;
			else
				$files[] = $line;
		}
		$dir->close();

		// Sort the results...
		natcasesort($dirs);
		natcasesort($files);

		if ($level == 0)
		{
			$result[] = array(
				'filename' => 'blank.gif',
				'checked' => in_array($context['member']['avatar']['server_pic'], array('', 'blank.gif')),
				'name' => &$txt[422],
				'is_dir' => false
			);
		}

		foreach ($dirs as $line)
		{
			$tmp = getAvatars($directory . (!empty($directory) ? '/' : '') . $line, $level + 1);
			if (!empty($tmp))
				$result[] = array(
					'filename' => htmlspecialchars($line),
					'checked' => strpos($context['member']['avatar']['server_pic'], $line . '/') !== false,
					'name' => '[' . htmlspecialchars(str_replace('_', ' ', $line)) . ']',
					'is_dir' => true,
					'files' => $tmp
			);
			unset($tmp);
		}

		foreach ($files as $line)
		{
			$filename = substr($line, 0, (strlen($line) - strlen(strrchr($line, '.'))));
			$extension = substr(strrchr($line, '.'), 1);

			// Make sure it is an image.
			if (strcasecmp($extension, 'gif') != 0 && strcasecmp($extension, 'jpg') != 0 && strcasecmp($extension, 'jpeg') != 0 && strcasecmp($extension, 'png') != 0 && strcasecmp($extension, 'bmp') != 0)
				continue;

			$result[] = array(
				'filename' => htmlspecialchars($line),
				'checked' => $line == $context['member']['avatar']['server_pic'],
				'name' => htmlspecialchars(str_replace('_', ' ', $filename)),
				'is_dir' => false
			);
			if ($level == 1)
				$context['avatar_list'][] = $directory . '/' . $line;
		}

		return $result;
	}
	
	function _settings($dbName, $dbPrefix){
		static $smfSetting  = array();

		if(count($smfSetting) == 0){
			$settings   = $this->model->getSMFSettings($dbName, $dbPrefix);

			foreach($settings as $setting){
			    $smfSetting[$setting->variable] = $setting->value;
			}
		}
		return $smfSetting;
	}
}

class JCAvatarCB extends JCAvatar{

	function JCAvatarCB($userId){
	    parent::JCAvatar($userId);
	}
	
	function _init(){
		$path   = $this->model->getCB($this->userId);

		if($path){
		    // Community builder might store the images in either 2 folders
		    if(file_exists($this->cms->get_path('root') . '/components/com_comprofiler/images/' . $path))
		        $this->source   = $this->cms->get_path('live') . '/components/com_comprofiler/images/' . $path;
			else if (file_exists($this->cms->get_path('root') . '/images/comprofiler/' . $path))
			    $this->source   = $this->cms->get_path('live') . '/images/comprofiler/' . $path;
			else
			    return false;
		}else
		    return false;

		return true;
	}
	
	function _link($userId, $Itemid){
		return cmsSefAmpReplace("index.php?option=com_comprofiler&task=userProfile&user=$userId&Itemid=$Itemid");
	}
}

class JCAvatarGravatar extends JCAvatar{
	// Private properties.
	var $_width 	= '40';

	function JCAvatarGravatar($userId, $email){
	    parent::JCAvatar($userId, $email);
	}
	
	function _init(){
	
	    $this->source   = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($this->email)
	                    . '&amp;default=' . urlencode($this->default)
						. '&amp;size=' . $this->_width;
        return true;
	}
}

class JCAvatarDefault extends JCAvatar{
	function JCAvatarDefault($userId){
	    parent::JCAvatar($userId);
	}
	
	function _init(){
	    return false;
	}
	
	function img(){
			 return "";
	}
}
?>