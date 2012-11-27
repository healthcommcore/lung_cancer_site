<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 **/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

// Include our custom cmslib if its not defined
if(!defined('CMSLIB_DEFINED'))
	include_once ((dirname((dirname(dirname(dirname(__FILE__)))))) . '/components/libraries/cmslib/spframework.php');

$cms    =& cmsInstance('CMSCore');

// Include the joomla rss feeds creator class.
if(!defined('RSSCreator20'))
	include_once($cms->get_path('root') . '/includes/feedcreator.class.php');

include_once(JC_HELPER_PATH . '/comments.helper.php');

// Check if mambots/system/pc_includes/template.php was included.
if(!defined('AzrulJXTemplate')){
    include_once($cms->get_path('plugins') . '/system/pc_includes/template.php');
}

class JCRss{
	var $cms		= null;
	var $template   = null;
	var $rss        = null;
	var $model          = null;
	
	var $title      	= '';
	var $option     	= '';
	var $description    = '';
	var $encoding       = 'UTF-8';
	var $link           = '';
	var $css            = '';
	var $contentId      = '';

	var $caching        = true;
	var $cacheFile		= '';
	var $cacheTimeOut   = '1800';

	function JCRss($option = '', $queryString = '', $contentId = '', $caching = true){
	    $this->cms  =& cmsInstance('CMSCore');
	    $this->cms->load('libraries','input');

		$this->_initTemplate($queryString);

		// Load the model so that it could perform any db operations
		// The rss.db.php file should be included in the task file
		// We need to check if it is included or not.
		if(!defined('JCRssDB')){
			include_once(JC_COM_PATH . '/model/rss.db.php');
		}
    	$this->model    	= new JCRssDB();
		$this->contentId    = $contentId;
		$this->option   	= $option;
		$this->cacheFile	= JC_COM_PATH . '/rsscache.php';
		$this->caching      = $caching;
		$this->rss          = new RSSCreator20();
		$this->rss->cssStyleSheet = NULL;
		$this->rss->link    = $this->cms->get_path('live');
	}

	function _initTemplate($queryString = ''){
		if($this->caching)
		    $this->template = new AzrulJXCachedTemplate('rsscache' . $queryString, $this->cacheTimeOut);
		else
		    $this->template = new AzrulJXTemplate();
	}


	function _cleanCache(){
	    if(file_exists($this->template->cache_id))
			@unlink($this->template->cache_id);
	}

	function set($varName, $varProperty){
	    if($varName && $varProperty)
	        $this->$varName = $varProperty;
	}

	/**
	 * add()
	 * Adds a child item in the corresponding rss feed.
	 * @param $title: title of the item
	 * @param $description: description of the item
	 * @param $timestamp: timestamp of the item
	 **/
	function add($title, $link, $description, $timestamp){
	    // Create new feed item and assign the required properties
	    $item 				= new FeedItem();

	    $item->title    	= !empty($title) ? $title : '...';
	    $item->link     	= $link;
	    $item->description  = $description;
	    $item->date         = $timestamp;
	    $this->rss->addItem($item);
	}
	
	function show(){
        header('Content-type: application/xml');
        
        if($this->caching && $this->template->is_cached()){
			// If cache is valid, no need to recreate the cache
		} else {
		    $this->_cleanCache();

			// Set and initialize any RSS property required.
	    	$this->rss->title   		= $this->title;
	    	$this->rss->description 	= $this->description;
	    	$this->rss->encoding    	= $this->encoding;
			$this->rss->cssStyleSheet   = null;

	    	// Make the cache files.
	    	$this->template->set('rss', $this->rss->createFeed());
		}
		
		$contents = $this->template->fetch_cache($this->cacheFile);
		echo $contents;
		
	    exit();
	}
}
?>