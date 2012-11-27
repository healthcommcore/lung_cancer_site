<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file stores most of the minimal used functions instead of including
 * all the functions from functions.jomcomment.php
 **/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

// Include our custom cmslib
if(!defined('CMSLIB_DEFINED'))
	include_once ((dirname(dirname(dirname(__FILE__)))). '/components/libraries/cmslib/spframework.php');
	

/**
 * jcPatchContentPagination -- Patch the com_content to fix the 'pagination not
 * loaded issues' when system-wide cache is enabled
 * @param none
 * Returns: true if patch successful
 **/	
function jcPatchContentPagination(){
	$cms =& cmsInstance('CMSCore');
	$patchSuccessful = false;
	if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
		$filename = 	$cms->get_path('root') . '/components/com_content/content.php';
		if(copy($filename, $filename.'.backup')){

			$handle = fopen($filename, "rb");
			$contents = fread($handle, filesize($filename));
			fclose($handle);
	
			# Check if hacks has been applied.
			if(strpos($contents, 'JOM COMMENT HACKED BEGIN') === false){
				$contents = str_replace(
					'$cache->call( \'HTML_content::show\', $row, $params, $access, $page );', '
		/* JOM COMMENT HACKED BEGIN */
		$cpage = isset($_GET[\'cpage\']) ? $_GET[\'cpage\'] : 0;
		$cache->call( \'HTML_content::show\', $row, $params, $access, $page , $cpage );
		/* JOM COMMENT HACKED END */
						', 
					$contents);
					
				
				$contents = str_replace(
					'$cache->call( \'showItem\', $id, $gid, $access, $pop, $option, 0, $limit, $limitstart );', '
			/* JOM COMMENT HACKED BEGIN */
			$cpage = isset($_GET[\'cpage\']) ? $_GET[\'cpage\'] : 0;
			$cache->call( \'showItem\', $id, $gid, $access, $pop, $option, 0, $limit, $limitstart, $cpage );
			/* JOM COMMENT HACKED END */
						', 
					$contents);
					
				$handle = fopen($filename, 'w');
				fwrite($handle, $contents);
				fclose($handle);
				$patchSuccessful = true;
			}
		}
		
		return $patchSuccessful;
	}
}

/**
 * jcRestoreContentPagination -- restore the 
 * @param none
 * Returns: true if patch successful
 **/
function jcRestoreContentPagination(){
	// just copy back the content.php.backup to content.php
	$cms =& cmsInstance('CMSCore');
	$filename = 	$cms->get_path('root') . '/components/com_content/content.php';
	if(file_exists($filename . '.backup')){
		unlink($filename);
		if(copy($filename . '.backup', $filename)){
			unlink($filename. '.backup');	
		}
	}
	
}

/**
 * jcCheckContentPagination -- check if the pagination hack has been applied 
 * @param none
 * Returns: true if patch has been applied
 **/
function jcCheckContentPagination(){
	$cms =& cmsInstance('CMSCore');
	$filename = 	$cms->get_path('root') . '/components/com_content/content.php';
	$handle = fopen($filename, "rb");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	
	return strstr($contents, 'JOM COMMENT HACKED BEGIN');
}
