<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file is to perform the execution of displaying captcha's image
 **/
global $_JC_CONFIG, $mainframe;

// @rule: Exit this page if Trackback is disabled.
if (!$_JC_CONFIG->get('enableTrackback')){
	exit();
}

// Include required libraries to process trackbacks
include_once(JC_MODEL_PATH . '/trackback.db.php');
include_once(JC_LIB_PATH . '/trackback.class.php');

$cms	=& cmsInstance('CMSCore');
$cms->load('libraries','input');
$cms->load('helper','url');

#Check if mambots/system/pc_includes/template.php was included.
if(!defined('AzrulJXTemplate')){
	include_once($cms->get_path('plugins') . '/system/pc_includes/template.php');
}

$trackbackType  = 'POST';

// Load the model so that it could perform any db operations
// The trackback.db.php file should be included in the task file
// We need to check if it is included or not.
if(!defined('TrackbackDB')){
	include_once(JC_COM_PATH . '/model/trackback.db.php');
}

$trackback  = new JCTrackback();

# If the referrer is the same as the main site, that means user are clicking the
# trackback. Output an error notice
$referer = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

if(!empty($referer) && !(strpos($referer, $cms->get_path('live')) === false)){
	$id 		= cmsGetVar('id','','GET');
	$option     = cmsGetVar('opt','', 'GET');
	
	echo $trackback->info($id, $option);
	return;
}

// Get some values from the query or server properties.
$id     	= cmsGetVar('id','','REQUEST');
$ip			= $_SERVER['REMOTE_ADDR'];

// Initialize the data so we can further process.
$postData	= $_GET;

// Remove 'id' from $postData
if(array_key_exists('id', $postData))
	$postData['id']	= '';

$trackback->init($id, $ip, $postData);
global $mainframe;

$trackback->set('date', strftime('%Y-%m-%d %H:%M:%S', time() + ($mainframe->getCfg('offset') * 60 * 60)));
$trackback->set('title', cmsGetVar('title', '...', $trackbackType));
$trackback->set('excerpt',cmsGetVar('excerpt', '...', $trackbackType));
$trackback->set('url',cmsGetVar('url','', $trackbackType));
$trackback->set('blog_name', cmsGetVar('blog_name', '...', $trackbackType));
$trackback->set('charset',cmsGetVar('charset', 'iso-8859-1',$trackbackType));
$trackback->set('option',cmsGetVar('opt', 'com_content',$trackbackType));

// @rule: Check for valid values
if(!intval($id)){
	$trackback->error('I really need an ID for this to work.');
}

// @rule: Do not add the comment if the contents are unpublished in com_content
if($trackback->get('option') == 'com_content'){
	if(jcContentPublished($trackback->get('contentid')) != 1){
	    $trackback->error('You cannot add trackbacks to unpublished content.');
	}
}

// @rule: Required fields.
if(!$trackback->get('url')){
	$trackback->error('Please provide complete trackback data for this to work.');
}

if(!$trackback->get('title')){
	$trackback->error('Please provide complete trackback data for this to work.');
}

// @rule: Use Jom Comments keyword blocking for title and excerpt
if($_JC_CONFIG->get('blockWords')){
	$blockedWords    = explode(',', $_JC_CONFIG->get('blockWords'));
	array_walk($blockedWords, 'jcTrim');

	foreach($blockedWords as $word){
	    if(!empty($word) && !($trackback->get('title'))){
	        if(@strpos($trackback->get('title'), $word) !== false){
	            $trackback->error('Spam Detected.');
			}
		} else if(!empty($word) && !($trackback->get('excerpt'))){
	        if(@strpos($trackback->get('excerpt'), $word) !== false){
	            $trackback->error('Spam Detected.');
			}
		}
	}
}

// @rule: Check if link back is required and we need to verify that the sender
// has the urls somewhere in the page given.
if($_JC_CONFIG->get('useLinkBack')){
	$content    = jcHttpPost($trackback->get('url'), '');
	if($content){
	    // Check if site that wants to trackback contains current site's url.
	    if(!strpos($content, $cms->get_path('live'))){
	        $trackback->error('A link to this website is required');
		}
	}else{
	    $trackback->error('A link to this website is required');
	}
}

// @rule: Only insert the trackback once. If duplicated entry dont insert
if($trackback->exists('receive',$id, $trackback->get('url'))){
	$trackback->error('We already have a ping from that URI for this specific post.');
}

// @rule: Process akismet anti spam if enabled.
if($_JC_CONFIG->get('remoteSpam') && $_JC_CONFIG->get('akismetKey')){
	include_once(JC_COM_PATH . '/includes/akismet/Akismet.class.php');

	$akismet    = new Akismet($cms->get_path('live'), trim($_JC_CONFIG->get('akismetKey')));
	$akismet->setAuthor('');
	$akismet->setAuthorEmail('');
	$akismet->setContent($document);

	if($akismet->isSpam())
	    $trackback->error('Your trackback content has been marked as spam in our anti spam database.');
}

// Clear necessary cache so that the trackbacks
// does get updated when viewing the trackbacks.
jcClearCache();

// Process trackback
$trackback->receive();
?>