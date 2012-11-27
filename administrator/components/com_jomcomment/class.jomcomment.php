<?php

/**
* Jom Comment 
* @package JomComment
* @copyright (C) 2006 by Azrul Rahim - All rights reserved!
* @license Copyrighted Commercial Software
**/

# Don't allow direct linking
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

/*
if (defined('_JC_JOMCOMENT_DB')) 
{
    return;
} 
else 
{
    define('_JC_JOMCOMENT_DB', 1);
} 
*/

class mosJomcomment extends CMSDBTable 
{

	var $_table 	= '#__jomcomment';
	var $_key		= 'id';
		
	var $id        = null;
	var $parentid  = null;
	var $status    = null;
	var $contentid = null;
	var $ip        = null;
	var $name      = null;
	var $title     = null;
	var $comment   = null;
	var $preview   = null;
	
	var $date      = null;
	var $published = null;
	var $ordering  = null;

	var $email     = null;
	var $website   = null;
	var $updateme  = null;
	var $custom1   = null;
	var $custom2   = null;
	var $custom3   = null;
	var $custom4   = null;
	var $custom5   = null;
	var $star      = null;
	var $user_id   = null;
	var $option    = null;
	
	var $voted     = null; #For votes in template
	var $referer    = null; // Easy way to retrieve the current page.

	var $_sid		= "";
	var $_password 	= "";
	var $_username  = ""; // not implemented yet
	var $_subscribe = "";
	
	function mosJomcomment() 
	{
		$this->CMSDBTable('#__jomcomment', 'id');
	}

}

class mosJomcommentTb extends CMSDBTable 
{
	var $id        = null;
	var $contentid = null;
	
	var $ip        = null;
	var $date      = null;
	
	var $title     = null;
	var $excerpt   = null;
	var $url   	   = null;
	var $blog_name = null;
	var $charset   = null;
	
	var $published = '1';
	var $option    = 'com_content';


	function mosJomcommentTb() 
	{
		$this->CMSDBTable('#__jomcomment_tb', 'id');
	}
	
}

