<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file is to perform the execution of displaying comments from a specific user.
 **/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

$cms    =& cmsInstance('CMSCore');

$cms->load('libraries', 'input');
$cms->load('helper','url');

$sid    = cmsGetVar('sid', '', 'GET');
$action = cmsGetVar('do', '', 'GET');

if($sid){
	$strSQL = "SELECT `commentid` FROM #__jomcomment_admin WHERE `sid`='{$sid}'";
	$cms->db->query($strSQL);
	
	if($commentid = $cms->db->get_value()){
	    include_once(JC_MODEL_PATH . '/jomadmin.db.php');
	    $jomAdmin   = new JCJomAdmin();
	    
	    switch($action){
	        case 'publish':
					// Publish some comment
					$jomAdmin->publish($commentid);
					echo "<p><b><img src=\"".$cms->get_path('live')."/administrator/images/tick.png\" /> The comment has been published</b></p>";
				break;
			case 'unpublish':
			    	// Unpublish some comment
			    	$jomAdmin->unpublish($commentid);
					echo "<p><b><img src=\"".$cms->get_path('live')."/administrator/images/tick.png\" /> The comment has been unpublished</b></p>";
			    break;
			case 'delete':
			    	// Remove some comment
			    	$jomAdmin->delete($commentid);
					echo "<p><b><img src=\"".$cms->get_path('live')."/administrator/images/tick.png\" /> The comment has been deleted</b></p>";
			    break;
		}
	}
	return;
}
echo "<p><b>The link is invalid. You can use the backend to publish the comment.</b></p>";
return;
?>