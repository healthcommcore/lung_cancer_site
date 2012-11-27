<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

function deleteDir($dir){
    if (substr($dir, strlen($dir)-1, 1)!= '/')
        $dir .= '/';
    
    if ($handle = opendir($dir)){
        while ($obj = readdir($handle)){
            if ($obj!= '.' && $obj!= '..'){
                if (is_dir($dir.$obj)){
                    if (!deleteDir($dir.$obj))
                        return false;
                }
                elseif (is_file($dir.$obj)){
                    if (!unlink($dir.$obj))
                        return false;
                }
            }
        }
        closedir($handle);
        if (!@rmdir($dir))
            return false;
        return true;
    }
    return false;
} 

function jcDeleteDir($src){
	if(class_exists('JFactory') && defined('_JEXEC'))
		JFolder::delete($src);
	else
		deleteDir($src);
		
}
function jcUnlink($src){
	if(class_exists('JFactory') && defined('_JEXEC')){
		
		JFile::delete($src);
	} else
		@unlink($src);
}


function com_uninstall() {
	global $database, $mainframe;
    define("SITE_ROOT_PATH", dirname(dirname(dirname(dirname(__FILE__)))) );
    
    $spfpath = SITE_ROOT_PATH . '/components/libraries/cmslib/spframework.php';
    if(file_exists($spfpath)){
    	include_once (SITE_ROOT_PATH . '/components/libraries/cmslib/spframework.php');
    } else {
    	// cmslib doesn't exisr, just return true so that uninstallatin goes through
    	return true;
	}
	
	$botPath = "mambots";
	if(cmsVersion() == _CMS_JOOMLA15){
		$botPath = "plugins";
	} else {
		$botPath = "mambots";
	}
	
	$cms =& cmsInstance('CMSCore');
	$db  =& cmsInstance('CMSDb');
	
	# Uninstall jom_comment_bot
	@jcUnlink($cms->get_path('plugins')."/content/jom_comment_bot.php");
	@jcUnlink($cms->get_path('plugins')."/content/jom_comment_bot.xml");
	$query = "DELETE FROM #__$botPath WHERE name='Jom Comment'";
    $db->query($query);
    
    # No need to remove the system mambots
    
    # Uninstall jom_commentsys_bot
    @jcUnlink($cms->get_path('plugins')."/system/jom_commentsys_bot.php");
    @jcUnlink($cms->get_path('plugins')."/system/jom_commentsys_bot.xml");
    $query = "DELETE FROM #__$botPath WHERE name='Jom Comment Sys'";
    $db->query($query);    
    
    # Delete com_jomcomment
    jcDeleteDir($cms->get_path('root')."/components/com_jomcomment");

	return true;   
}
