<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file is to perform the execution of displaying comments from a specific user.
 **/
global $_JC_CONFIG, $mainframe;

$cms    =& cmsInstance('CMSCore');
$cms->load('libraries', 'user');


// Only valid user should have access
if(!$cms->user->id){
	echo  'Access to this location is not allowed.';
	return;
}

include_once($cms->get_path('plugins') . '/system/pc_includes/template.php');

$template   = new AzrulJXTemplate();

// Load the trunchtml library
$cms->load('libraries', 'trunchtml');

// Load helper functions
$cms->load('helper', 'url');


$limitstart = cmsGetVar('cpage', '', 'GET');
$limit      = $limitstart ? "LIMIT $limitstart, " . JC_DEFAULT_LIMIT : 'LIMIT ' . JC_DEFAULT_LIMIT;

// SQL Queries
$strSQL = "SELECT * FROM #__jomcomment_subs WHERE userid='{$cms->user->id}' "
        . "ORDER BY `id` "
        . "DESC {$limit}";

// Execute SQL Query & grab data
$cms->db->query($strSQL);
$subscriptions  = $cms->db->get_object_list();

for($i = 0; $i < count($subscriptions); $i++){
	$row    =& $subscriptions[$i];

	if($row->status == '1'){
	    // Enabled subscriptions we display a disable link
	    $row->subStatus = '<a href="javascript:void(0);" onclick="jax.call(\'jomcomment\',\'jcxDisableSubscription\',\'' . $row->id . '\');">'
						. 'Disable</a>';
	} else{
	    // Disabled subscriptions we display a enable link
	    $row->subStatus = '<a href="javascript:void(0);" onclick="jax.call(\'jomcomment\',\'jcxEnableSubscription\',\'' . $row->id . '\');">'
						. 'Enable</a>';
	}
	
	if($row->option == 'com_content' || $row->option == 'com_myblog'){
		$title	= jcContentTitle($row->contentid);
	}
	else if($row->option == 'com_eventlist'){
		$strSQL	= "SELECT `title` FROM #__eventlist_events WHERE `id`='{$row->contentid}'";
		$cms->db->query($strSQL);
		$title	= $cms->db->get_value();
	}
	$row->title	= $title;
	
	// Check if content exists and published.
	if(!empty($row->url)){
	    $row->parentLink    = $row->url;
	} else {
		if($row->option == 'com_content'){
		    // Content is from com_content we get itemid from mainframe
		    $row->parentLink    = jcGetContentLink($row->contentid, $mainframe->getItemid($row->contentid));
		}else if($row->option == 'com_myblog'){
			// Get My Blog's item id.
			$strSQL         = "SELECT `permalink` FROM #__myblog_permalinks WHERE `contentid`='{$row->contentid}'";
			$cms->db->query($strSQL);

			$row->parentLink    = 'index.php?option=com_myblog&show=' . $cms->db->get_value() . '&Itemid=' . jcGetMyBlogItemId();
		}else{
		    // Other components or 3rd party components that we dont know what
		    // the url is supposed to be.
		    $row->parentLink    = jcGetContentLink($row->contentid, 1);
		}
	}
	
	// Get total of comments per specific content item.
	$strSQL = "SELECT COUNT(*) FROM #__jomcomment WHERE `contentid`='{$row->contentid}'";
	$row->total		= $cms->db->get_value($strSQL);
	
	// Get last replier of the specific content.
	$strSQL = "SELECT `name`,`user_id` FROM #__jomcomment WHERE `contentid`='{$row->contentid}' "
	        . "GROUP BY `date` DESC LIMIT 0,1";
	$row->lastreply = $cms->db->get_value($strSQL);
}

// Prepare pagination
$cms->load('libraries', 'pagination');

// Initialize some configurations
$config = array();

$cms->db->query("SELECT COUNT(*) FROM #__jomcomment WHERE `user_id`='{$cms->user->id}'");

// Set some configurations so that we can initialize the paginations
$config['total_rows']   = $cms->db->get_value();
$config['base_url']     = str_replace($cms->get_path('live') , '', $_SERVER['REQUEST_URI']);
$config['per_page']     = JC_DEFAULT_LIMIT;

$template->set('jcitemid', jcGetItemId());
$template->set('pagination', $cms->pagination->create_links());
$template->set('subscriptions', $subscriptions);
echo $template->fetch(JC_TEMPLATE_PATH . '/admin/subscriptions.html');
return;
?>