<?php
if (!defined('IN_WPRO')) exit;
class wproFilePlugin_flvplayer {
	
	var $extensions = array('.flv','.mp3', '.mp4', '.h264', '.xspf');
	var $description = 'FLV & MP3 Media Player';
	var $local = true; // does this plugin handle inserting files from the image manager?
	var $remote = true; // does this plugin handle inserting files form a remote web location?
	var $jsFile = 'plugin_src.js';
	
	function wproFilePlugin_flvplayer () {
		if (isset($GLOBALS['DIALOG']))
			$this->description = $GLOBALS['DIALOG']->langEngine->get('wproCore_fileBrowser','flvPlayer');
	}
		
	/* returns an associative array of values to be displayed on the image details pane */
	function displayDetails($file, &$response) {
		global $DIALOG;
		$return = NULL;
		if ($arr = $DIALOG->plugins['wproCore_fileBrowser']->getMediaDimensions($file)) {
			$width = $arr['width']; $height=$arr['height'];
			if (!empty($width)&&!empty($height)) {
				$return = array($DIALOG->langEngine->get('wproCore_fileBrowser','dimensions') => $width.' x '.$height);
			}
		}
		return $return;
	}
	
	/* returns an associative array of values to help populate the local options form */
	function getDetails($file, &$response) {
		global $DIALOG;
		$return = NULL;
		
		// playlists
		if (strrchr($file, '.') == '.xspf') {
			// get default values
			include(WPRO_DIR.'conf/defaultValues/wproCore_fileBrowser.inc.php');
			$return = array('width' => $defaultValues['flvplayerWidth'], 'height' => $defaultValues['flvplayerHeight'], 'playlist' => 1);
		} else {
		
			if ($arr = $DIALOG->plugins['wproCore_fileBrowser']->getMediaDimensions($file)) {
				$width = $arr['width']; $height=$arr['height'];
				if (!empty($width)&&!empty($height)) {
					$return = array('width' => $width, 'height' => $height);
				}
			}
		
		}
		return $return;
	}
	
	/* returns HTML for displaying local options */
	function displayLocalOptions($prefix) {
		global $DIALOG;
		
		$tpl = new wproTemplate();
		$tpl->assign('prefix', $prefix);
		$DIALOG->assignCommonVarsToTemplate($tpl);
		
		return $tpl->fetch(dirname(__FILE__).'/form.tpl.php');
	}
	
	/* returns HTML for displaying remote options */
	function displayRemoteOptions($prefix) {
		global $DIALOG;
		
		$tpl = new wproTemplate();
		$tpl->assign('prefix', $prefix);
		$DIALOG->assignCommonVarsToTemplate($tpl);
		
		return $tpl->fetch(dirname(__FILE__).'/form.tpl.php');
	}
		
}

?>