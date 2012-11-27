<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file is to perform the execution of displaying captcha's image
 **/
global $_JC_CONFIG, $mainframe;

// Exit if RSS Feed is disabled.
if (!$_JC_CONFIG->get('useRSSFeed')) {
	echo "RSS feed not enabled.";
	exit();
}

include_once(JC_MODEL_PATH . '/rss.db.php');
include_once(JC_LIB_PATH . '/rss.class.php');

// Total limits of records to display in this feed
// Increase it to any number of preferred choice.
$limits     = 20;

$cms    	=& cmsInstance('CMSCore');
$cms->load('libraries','input');

#Check if mambots/system/pc_includes/template.php was included.
if(!defined('AzrulJXTemplate')){
	include_once($cms->get_path('plugins') . '/system/pc_includes/template.php');
}

// Load the model so that it could perform any db operations
// The rss.db.php file should be included in the task file
// We need to check if it is included or not.
if(!defined('JCRssDB')){
	include_once(JC_COM_PATH . '/model/rss.db.php');
}

$option 		= $cms->input->get('opt', '');
$queryString	= $_SERVER['QUERY_STRING'];
$contentId      = $cms->input->get('contentid','');

$currentItemId  = $mainframe->getItemid($contentId);
$currentItemId	= !empty ($currentItemId) ? $currentItemId : 1;
$isMyBlog		= false;

// Check if current option is myblog as we would need to change to the correct Itemid
if(isset($option) && !empty($option)){
	if($option == 'com_myblog'){
		$isMyBlog		= true;
		$currentItemId	= jcGetMyBlogItemid();
	}
}

$rss    = new JCRss($option, $queryString, $contentId, true);
$rssDB  = new JCRssDB();


$title  		= $rssDB->getTitle($contentId, '#__content');
$description    = '';

// Check if mb_convert_encoding function exists so we can utilize it.
if(defined('_ISO') && function_exists('mb_convert_encoding')){
	$iso   	= explode('=', _ISO);
	$title	= mb_convert_encoding($title,'UTF-8', "$iso[1], auto");
}

if(isset($contentId) && isset($option)){
    $data   		= $rssDB->getComments($option, $limits, $contentId);
	$description	= 'Comments for ' . $title . ' at ' . $cms->get_path('live');
}else {
	$data           = $rssDB->getComments($option, $limits);
	$description    = 'Latest comments for ' . $cms->get_path('live');
}

$total  = 0;
if(count($data) > 0){
	// Get the total comments count for the entire component.
	// If getting feed for the entire site.
	if(isset($contentId) && !empty($contentId) && isset($option) && !empty($option)){
	    $total  = $rssDB->getCommentsCount($option, $contentId);
	}
	else{
	    $total  = count($data);
	}

	$description = $description . " , comment 1 to " . $total . " out of " . count($data) . " comments";
}
$rss->set('title', $title);
$rss->set('description', $description);


foreach($data as $row){
	// Check if option currently is myblog
	if($isMyBlog)
		$link   	= jcGetMyBlogLink($row->contentid, $currentItemId, false) . '#comment-' . $row->id;
	else
		$link   	= jcGetContentLink($row->contentid, $currentItemId, false) . '#comment-' . $row->id;

	$timestamp  = date('r', $row->created_ts);
	$row->comment = $cms->input->xss_clean($row->comment);
	$rss->add($row->title, $link , $row->comment . ' - ' . $row->name, $timestamp);
}

$rss->show();
exit();
?>
