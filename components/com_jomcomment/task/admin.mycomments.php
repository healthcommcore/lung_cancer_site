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
	echo 'Access to this location is not allowed.';
	return;
}
include_once($cms->get_path('plugins') . '/system/pc_includes/template.php');
include_once(JC_HELPER_PATH . '/comments.helper.php');

$template   = new AzrulJXTemplate();

// Load the trunchtml library
$cms->load('libraries', 'trunchtml');

// Load helper functions
$cms->load('helper', 'url');

$limitstart = cmsGetVar('cpage', '', 'GET');
$limit      = $limitstart ? "LIMIT $limitstart, " . JC_DEFAULT_LIMIT : 'LIMIT ' . JC_DEFAULT_LIMIT;

// SQL Queries
$strSQL = "SELECT a.id,a.published,a.date,a.contentid, a.comment AS comment, a.preview AS preview,a.option,a.name,a.email,a.ip, a.referer "
		. "FROM #__jomcomment AS a "
		. "WHERE a.user_id='{$cms->user->id}' "
		. "ORDER BY `date` DESC {$limit}";

// Execute SQL & Grab data
$cms->db->query($strSQL);
$comments   = $cms->db->get_object_list();

// Format the data so that it could be set to the template.
for($i = 0; $i < count($comments); $i++){
	$row    =& $comments[$i];
	
	// Test if the preview row is already populated
	if( !empty($row->preview) )
		$row->comment	= $row->preview;

	// For com_content or com_myblog we need to check the state of the content.
	if($row->option == 'com_content' || $row->option == 'com_myblog'){
		$strSQL	= "SELECT `state` FROM #__content WHERE `id`='{$row->contentid}'";
		
		$cms->db->query($strSQL);
		$state	= $cms->db->get_value();

		if($state != '1'){
			// Record is unpublished. Remove the current row.
			$row->show	= false;
			
			// Get out and proceed to the next row.
			continue;
		}
	}
	
	// Check if content exists and published.
	if(!empty($row->referer)){
	    $row->parentLink    = $row->referer;
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
	$row->show	= true;
}
//var_dump($comments);
// Prepare pagination
$cms->load('libraries', 'pagination');

// Initialize some configurations
$config = array();

// Only for com_content we need to check for unpublished contents we dont want to display 
// the comments.
$strSQL = "SELECT a.contentid, a.option FROM #__jomcomment AS a WHERE a.user_id = '{$cms->user->id}'";
$cms->db->query($strSQL);
$tmpComments	= $cms->db->get_object_list();

// Get total count for all comments for specific users
$totalCount		= count($tmpComments);
$unpublishCount = 0;

foreach($tmpComments as $tmp){
	if($tmp->option == 'com_content' || $tmp->option == 'com_myblog'){
		$strSQL	= "SELECT `state` FROM #__content WHERE `id`='{$tmp->contentid}'";
		$cms->db->query($strSQL);
		
		$tmpData = $cms->db->get_value();
		
		if($tmpData != '1')
			$unpublishCount += 1;	
	}
}

// Get unpublished
// Recalculate the exact content 
$config['total_rows']   = $totalCount - $unpublishCount;

$config['base_url']     = str_replace($cms->get_path('live') , '', $_SERVER['REQUEST_URI']);
$config['per_page']     = JC_DEFAULT_LIMIT;

$cms->pagination->initialize($config);

// Assign desired variables to the template file and display the template.
$template->set('jcitemid', jcGetItemId());
$template->set('pagination', $cms->pagination->create_links());
$template->set('comments', $comments);
echo $template->fetch(JC_TEMPLATE_PATH . '/admin/comments.html');
?>
