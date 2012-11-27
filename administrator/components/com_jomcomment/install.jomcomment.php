<?php
/**
* @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
* @license http://www.azrul.com Copyrighted Commercial Software
**/

ini_set('error_reporting', 'E_ALL');
ini_set('display_errors', 1);
ini_set('max_execution_time', '120');

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
define("SITE_ROOT_PATH", dirname(dirname(dirname(dirname(__FILE__)))) );
@ini_set('error_reporting', 'E_ALL');
/**
 * Extract zip files
 */
function jcExtractArchive($src, $destDir){
	if(class_exists('JFactory') && defined('_JEXEC')){
		global $mainframe;

		$destDir =  JPath::clean($destDir);
		$src 	=  JPath::clean($src);
		
		JArchive::extract($src, $destDir);
		
	} else {
		if(!class_exists('PclZip')){
		    #BC compatibilities.
	        include_once(SITE_ROOT_PATH. "/administrator/includes/pcl/pclzip.lib.php");
		}

		$archive = new PclZip($src);
		$list = $archive->extract(PCLZIP_OPT_PATH, $destDir);
	}

	return true;
}

function jcMkdir($destDir){
	if(class_exists('JFactory') && defined('_JEXEC')){
		JFolder::create($destDir);
	} else {
		mkdir($destDir);
	}

	return true;
}

function jcRmdir($destDir){
	if(class_exists('JFactory') && defined('_JEXEC')){
		global $mainframe;
		$destDir =  JPath::clean($destDir);
		
		if(file_exists($destDir))
		  JFolder::delete($destDir);
	} else {
        if(file_exists($destDir))
		  @deleteDir($destDir);
	}

	return true;
}

function jcInstallClearCache(){
	global $mainframe;

	$cms    =& cmsInstance('CMSCore');
	$cms->load('helper','directory');
	
	$cachePath = $cms->get_path('root') . '/components/libraries/cmslib/cache';
	$list   = cmsGetFiles($cachePath, '');
	
	if(!empty($list)){
		foreach($list as $file){
		    // Only remove files that contains the naming convention for cache_
		    if(strstr($file, 'cache_')){
		        @unlink($cachePath . '/' . $file);
			}
		}
	}
	
	// For Joomla 1.0 we would still need to clear the cached files
	// located in $mosConfig_cachepath
	if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
	    $list   = cmsGetFiles($mainframe->getCfg('cachepath'), '');
	    if(!empty($list)){
		    foreach($list as $file){
		        if(strstr($file, 'cache_'))
		            @unlink($mainframe->getCfg('cachepath') . '/' . $file);
			}
		}
	}
}

// Helper function to delete folder recurively
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
        if (!@unlink($dir))
            return false;
        return true;
    }
    return false;
} 

// Return the current system bot version
function sysBotGetVersion(){

  	$cms    =& cmsInstance('CMSCore');
  	
  	if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
	  	if(!defined('DOMIT_INCLUDE_PATH'))
	  		require_once( $cms->get_path('root') . '/includes/domit/xml_domit_lite_include.php' );
	} else if(cmsVersion() == _CMS_JOOMLA15){
	    if(!defined('DOMIT_INCLUDE_PATH'))
	        require_once($cms->get_path('root') . '/libraries/domit/xml_domit_lite_parser.php');
	}

	$version = 0;
	
	$filename = $cms->get_path('root') . '/mambots/system/azrul.system.xml';
	if(file_exists($filename)){
		// Read the file to see if it's a valid component XML file
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );
	
		if (!$xmlDoc->loadXML( $filename, false, true )) {
			//continue;
		}
	
		$root = &$xmlDoc->documentElement;

		if ($root->getTagName() != 'mosinstall') {
			//continue;
		}

		$element 		= &$root->getElementsByPath('version', 1);
		$version 		= $element ? $element->getText() : '';
	}

	return doubleval($version);
}

function sysBotUpgrade($src){
    $cms    =& cmsInstance('CMSCore');
    $installIt = false;

	$botTable   = (cmsVersion() == _CMS_JOOMLA15) ? 'plugins' : 'mambots';
	$botVersion = sysBotGetVersion();
    if($botVersion != 0 && $botVersion < 2.5){
    	require_once($cms->get_path('root') . "/administrator/includes/pcl/pclzip.lib.php");

    	@deleteDir($cms->get_path('plugin') . "/system/pc_includes");
		@unlink($cms->get_path('plugin') . "/system/azrul.system.php");
		@unlink($cms->get_path('plugin') . "/system/azrul.system.xml");
		$strSQL = "DELETE FROM #__{$botTable} WHERE element='azrul.system'";
	    $cms->db->query($strSQL);

	    $installIt = true;
	} else if($botVersion == 0){
		// No system bot detected, install it
		$installIt = true;
	}

	
	if($installIt){
		echo '<img src="images/tick.png"> Installing Azrul.com System mambots <br/>';		
		$list = jcExtractArchive($src,$cms->get_path('plugins') . "/system/");
	    
	    $strSQL = "DELETE FROM #__{$botTable} WHERE element='azrul.system' OR `name`='Azrul.com System Mambot'";
	    $cms->db->query($strSQL);
	    
	    $strSQL = "INSERT INTO `#__{$botTable}` SET `name`='Azrul.com System Mambot', "
	            . "`element`='azrul.system', "
	            . "`folder`='system', "
	            . "`access`='0', "
	            . "`ordering`='1', "
	            . "`published`='1'";
		$cms->db->query($strSQL);
	    unset($archive);
	}
}

function upgradeCmsLib(){
	
	// Remove older folder
	jcRmdir(SITE_ROOT_PATH . '/components/libraries');

	// Install it now (from the cmslib.zip file)
	$libpath = SITE_ROOT_PATH . '/components/libraries';

	if (!file_exists($libpath)) jcMkDir($libpath);
	if (!file_exists($libpath.'/cmslib')) jcMkDir($libpath.'/cmslib');

	# CMS Compatibilities
	#spframework not included yet, cant use cmsVersion()
	$list = jcExtractArchive(SITE_ROOT_PATH . '/components/com_jomcomment/cmslib.zip', $libpath . '/cmslib/');
	
	if($list){
		// Extract success, Include the library
		include_once (SITE_ROOT_PATH . '/components/libraries/cmslib/spframework.php');
	}
}

// Install the CMS Lib if required and load the library
function installCmsLib(){
	global $mainframe;
	
	// Test if version.php file exists in the cmslib folder. If it doesn't exists, we know that this might be an upgrade.
	if( file_exists( SITE_ROOT_PATH . '/components/libraries/cmslib/version.php') ){
		// This is an upgrade from 1.1 cmslib
		global $cmsVersion;
		include_once( SITE_ROOT_PATH . '/components/libraries/cmslib/version.php');
		
		if($cmsVersion >= 1.1){
			// Don't need to upgrade this since the cmslib version is higher.
			// Just include it.
			include_once( SITE_ROOT_PATH . '/components/libraries/cmslib/spframework.php');
		} else {
			upgradeCmsLib();
		}
	} else {
		// This is older versions before 1.1 cmslib exist
		upgradeCmsLib();
	}
}

function com_install() {
	global $database, $mainframe, $_VERSION;
	installCmsLib();
	
	if(cmsVersion() == _CMS_JOOMLA10 ){
		ob_start();
	} 
	
	if(cmsVersion() == _CMS_JOOMLA15){
		$inst =& JInstaller::getInstance();
		$inst->setOverwrite(true);
	}
	
	

	if(!function_exists('cmsInstance')){
		echo "<p>Installation FAILS!. You need to make sure the Joomla root directory is writeable.</p>";
		echo "<p>We need the root folder to be writeable to allow us to install our custom libraries.</p>";
		return;
	}
	
	// CMSLib loaded, need to get db object
	$db 	=& cmsInstance('CMSDb');
	$cms	=& cmsInstance('CMSCore');

	$botPath = "";
	if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
	    $botPath    = 'mambots';
	}else if(cmsVersion() == _CMS_JOOMLA15){
	    $botPath    = 'plugins';
	}
	
	// Load installation script
	include_once($cms->get_path('root') . '/administrator/components/com_jomcomment/install.sql.php');

	$strSQL = "UPDATE `#__components` SET `admin_menu_img`='../administrator/components/com_jomcomment/jomcomment-icon.png' "
	        . "WHERE `admin_menu_link`='option=com_jomcomment'";
	$db->query($strSQL);

	$strSQL = "ALTER TABLE `#__jomcomment` MODIFY COLUMN `name` VARCHAR(200)";
	$db->query($strSQL);


    echo '
        <p><img src="components/com_jomcomment/logo.gif" alt="logo" /></p>
        <p><strong>Jom Comment - A Joomla, Ajax-based User Comments Component</strong><br/>
        <code>
        <br/>';

    //add new field if it doesn't exist
    $fields = $cms->db->getFields('#__jomcomment');
    

    echo '<img src="images/tick.png"> Updating database <br/>';
    if(!empty($fields)){
        
        if(!array_key_exists("user_id", $fields)){
            $query = "ALTER TABLE `#__jomcomment` ADD COLUMN `user_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `star`";
            $cms->db->query($query);
        }
        
        if(!array_key_exists('preview', $fields)){
        	$query	= "ALTER TABLE `#__jomcomment` ADD `preview` TEXT NOT NULL AFTER `comment`";
        	$cms->db->query($query);
		}
		
        if(!array_key_exists("option", $fields)){
            $query = "ALTER TABLE `#__jomcomment` ADD COLUMN `option` varchar(50) NOT NULL default 'com_content' AFTER `user_id`";
            $cms->db->query($query);
        }
        
        if(!array_key_exists("voted", $fields)){
            $query = "ALTER TABLE `#__jomcomment` ADD `voted` SMALLINT NOT NULL DEFAULT '0'";
            $cms->db->query($query);
        }
        
        if(!array_key_exists("referer", $fields)){
            $query = "ALTER TABLE `#__jomcomment` ADD `referer` TEXT NOT NULL";
            $cms->db->query($query);
        }
    }

	// Checking #__jomcomment_config for language row.
	$strSQL	= "SELECT COUNT(*) FROM #__jomcomment_config WHERE `name`='language'";
	$cms->db->query($strSQL);

	// Insert default language config	
	if($cms->db->get_value() < 1){
		global $mainframe;
		$strSQL	= "INSERT INTO #__jomcomment_config (`name` ,`value`) "
				. "VALUES ('language', '\$languages = array(\"language\" => \"english.php\","
				. "\"\" => \"arabic\", \"\" => \"brazilian\", " 
				. "\"\" => \"bulgarian\", \"\" => \"czech\", "
				. "\"\" => \"danish\", \"\" => \"dutch\", "
				. "\"\" => \"english\", \"\" => \"estonian\", "
				. "\"\" => \"finnish\", \"\" => \"french\", "
				. "\"\" => \"german\", \"\" => \"germani\", "
				. "\"\" => \"greek\", \"\" => \"hebrew\", "
				. "\"\" => \"hindi\", \"\" => \"hrvatski\", "
				. "\"\" => \"hungarian\", \"\" => \"hungariani\", "
				. "\"\" => \"italian\", \"\" => \"japanese\", "
				. "\"\" => \"latvian\", \"\" => \"malay\", "
				. "\"\" => \"norwegian\", \"\" => \"polish\", "
				. "\"\" => \"russian\", \"\" => \"spanish\", "
				. "\"\" => \"srpski\", \"\" => \"srpski_cyr\", "
				. "\"\" => \"swedish\", \"autoLang\" => \"\" );')";
				
		$cms->db->query($strSQL);
	}
	
	
	// Check if id exists in #__jomcomment_mailq
	$fields = $cms->db->getFields('#__jomcomment_mailq');

	$comid = $cms->db->get_value("SELECT `id` FROM #__components WHERE `name`='Jom Comment'");
	$query = "UPDATE #__menu SET `componentid`='$comid' WHERE `link` LIKE 'index.php?option=com_jomcomment%'";
	$cms->db->query($query);

	if(!empty($fields)){
	    if(!array_key_exists('id',$fields)){
	        // ID Field not created, just re-create the entire table
	        $strSQL = "DROP TABLE `#__jomcomment_mailq`";
	        $cms->db->query($strSQL);

	        // Reinsert table
			$strSQL = "	CREATE TABLE IF NOT EXISTS `#__jomcomment_mailq` (
								`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
								`commentid` INT(10) NOT NULL,
								`email` VARCHAR( 200 ) NOT NULL ,
								`status` TINYINT NOT NULL DEFAULT '0',
								`title` VARCHAR( 200 ) NOT NULL ,
								`name` VARCHAR( 200 ) NOT NULL ,
								`content` TEXT NOT NULL ,
								`posted_on` DATETIME NOT NULL ,
								INDEX ( `status` )
							) TYPE = MYISAM ;";

			$cms->db->query($strSQL);
		}
		
		if(!array_key_exists("commentid", $fields))
		{
			$strSQL	= 'ALTER TABLE `#__jomcomment_mailq` ADD `commentid` INT( 10 ) NOT NULL';
			$cms->db->query($strSQL);
		}
	}
	
   	$src  = $cms->get_path('root') . "/components/com_jomcomment/azrul.zip";
   	sysBotUpgrade($src);
   	
   	/* Patch the trackback table if necessary */
   	// change trackbakc url to varchar to allow indexing   	 
	$db->query("ALTER TABLE `#__jomcomment_tb` CHANGE `url` `url` VARCHAR( 255 ) NOT NULL DEFAULT ' ' ");
	
   	$result = $db->query('select * FROM `#__jomcomment_tb` LIMIT 1');
	$i = 0;	
	$sql = '';
	$alter = array();
	if(is_resource($result)){
	while ($i < mysql_num_fields($result)) {
	   $meta = mysql_fetch_field($result, $i);
	   if ($meta) {
	       if($meta->name == 'contentid' && $meta->multiple_key == 0){
				$alter[] = ' ADD INDEX ( `contentid` ) ' ;
		   }
		   
		   if($meta->name == 'url' && $meta->multiple_key == 0){
				$alter[] = 'ADD INDEX ( `url` ) ';
		   }
		   
		   if($meta->name == 'published' && $meta->multiple_key == 0){
				$alter[] = ' ADD INDEX ( `published` ) ';
		   }
		   
		   if($meta->name == 'option' && $meta->multiple_key == 0){
				$alter[] = ' ADD INDEX ( `option` ) ' ;
		   }
		   
		   if($meta->name == 'ip' && $meta->multiple_key == 0){
				$alter[] = ' ADD INDEX ( `ip` ) ';
		   }
	   }
	   
	   $i++;
	}
	
	if(count($alter)){
		$sql = 'ALTER TABLE `#__jomcomment_tb` ' . implode( ',', $alter) ;
		$db->query($sql);
	}
	}

    /**
     * Install jom_comment_bot
     **/
     $jcBotZip  = $cms->get_path('root') . '/components/com_jomcomment/bot.zip';
     $jcBotDest = $cms->get_path('root') . '/' . $botPath . '/content/';
     
    echo '<img src="images/tick.png"> Installing Content mambots <br/>';

	$list   = jcExtractArchive($jcBotZip, $jcBotDest);
	
	$strSQL = "INSERT INTO #__$botPath SET `name`='Jom Comment', "
	        . "`element`='jom_comment_bot', "
	        . "`folder`='content', "
	        . "`access`='0', "
	        . "`ordering`='1', "
	        . "`published`='1'";
	$cms->db->query($strSQL);

    /**
     * End install jom_comment_bot
     */
    
    /**
     * Install jom_commentsys_bot
     **/
    $jcSysBotZip 	= $cms->get_path('root') . '/components/com_jomcomment/sys.zip';
    $jcSysBotDest  	= $cms->get_path('root') . '/' . $botPath . '/system/';
	echo '<img src="images/tick.png"> Installing Jom Comment System mambots <br/>';
	
	$list   = jcExtractArchive($jcSysBotZip, $jcSysBotDest);
	$strSQL    = "INSERT INTO #__$botPath SET `name`='Jom Comment Sys', "
	            . "`element`='jom_commentsys_bot', "
	            . "`folder`='system', "
	            . "`access`='0', "
	            . "`ordering`='1', "
	            . "`published`='1'";
	$cms->db->query($strSQL);
    /**
     * End install jom_commentsys_bot
     */

    /**
     * Clear Caches
     **/
	if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
	    mosCache::cleanCache();
	}else if(cmsVersion() == _CMS_JOOMLA15){
	    $cache = JFactory::getCache();
	    $cache->clean('com_content');
	}
    /**
     * End Clear Caches
     **/
     
    echo '
        <img src="images/tick.png"> Loading default configurations<br/>
        <br/>
        </code>
          <font class="small">&copy; Copyright 2006 by Azrul<br/>
          This component is copyrighted commercial software. Distribution is prohibited.</font></p>
        <p><a href="htt://www.azrul.com">www.azrul.com</a></p>';
 
    /**
     * Unzip full administrator files
     **/
    $jcAdminZip 	= $cms->get_path('root') . '/components/com_jomcomment/admin.zip';
    $jcAdminDest    = $cms->get_path('root') . '/administrator/components/com_jomcomment/';
	$list   = jcExtractArchive($jcAdminZip, $jcAdminDest);
	
	/**
     * Unzip full components files
     **/
	$jcComZip  = $cms->get_path('root') . '/administrator/components/com_jomcomment/com.zip';
	$jcComDest = $cms->get_path('root') . '/components/com_jomcomment/';
	$list       = @jcExtractArchive($jcComZip, $jcComDest);

     
	# Check for user menu entry. Create one if necessary
	$query = "SELECT COUNT(*) FROM #__menu 
				WHERE menutype='usermenu' 
				AND link='index.php?option=com_jomcomment&task=mycomments'";
	$cms->db->query($query);
	
	if(!$cms->db->get_value()){
	    if(cmsVersion() == _CMS_JOOMLA15){
	        $strSQL = "INSERT INTO #__menu SET
					menutype='usermenu', name='My Comments',
					alias='my-comment',
					link='index.php?option=com_jomcomment&task=mycomments' ,
					type='component', published='1', ordering='10', access=1, browserNav=0, params='menu_image=-1'";
		} else if (cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
			$strSQL	= "INSERT INTO #__menu SET
					menutype='usermenu', name='My Comments',
					link='index.php?option=com_jomcomment&task=mycomments' ,
					type='url', published='1', ordering='10', access=1, browserNav=0, params='menu_image=-1'";
		}
		$cms->db->query($strSQL);
	} else {
	    // Update existing menu.
		if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
		    $strSQL = "UPDATE #__menu SET `published`='1' , browserNav=0, type='url'
			WHERE menutype='usermenu'
			AND link='index.php?option=com_jomcomment&task=mycomments' ";
		} else if(cmsVersion() == _CMS_JOOMLA15){
 		    $strSQL = "UPDATE #__menu SET `published`='1' , browserNav=0, type='component', alias='my-comment'
			WHERE menutype='usermenu'
			AND link='index.php?option=com_jomcomment&task=mycomments' ";
		}
		$cms->db->query($strSQL);
	}
	
	
	// Clear cache
	jcInstallClearCache();

    /**
     * Remove zip files from site. Disallow any users from downloading these
     * zip files!
     **/
// 	@unlink($cms->get_path('root') . '/components/com_jomcomment/admin.zip');
// 	@unlink($cms->get_path('root') . '/components/com_jomcomment/sys.zip');
// 	@unlink($cms->get_path('root') . '/components/com_jomcomment/com.zip');
// 	@unlink($cms->get_path('root') . '/components/com_jomcomment/bot.zip');
// 	@unlink($cms->get_path('root') . '/components/com_jomcomment/azrul.zip');
// 	@unlink($cms->get_path('root') . '/components/com_jomcomment/cmslib.zip');
    /**
     * End removing zip files
     **/
	
	if(cmsVersion() == _CMS_JOOMLA10 ){
		$content    = ob_get_contents();
		ob_end_clean();
		return $content;
	} else {
		return true;
	}
}
