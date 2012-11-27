<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class CMSUser {
	var $name	  = null;
	var $username = null;
	var $id		= null;
	var $email	= null;
	var $usertype = null;

	function CMSUser(){
		
		if(cmsVersion() == _CMS_JOOMLA15){
			//$my = & JUser::getInstance();
			$my =& JFactory::getUser();
			$this->id = $my->id;
			$this->email = $my->email;
			$this->name = $my->name;
			$this->username = $my->username;
			$this->usertype = $my->usertype;
			
		} else {
			global $my;
			$this->id = $my->id;
			$this->name = $my->name;
			$this->email = $my->email;
			$this->username = $my->username;
			$this->usertype = $my->usertype;
		}
	}
}
