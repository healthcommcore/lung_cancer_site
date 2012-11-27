<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file is the captcha's image file and will only needed to be included if captcha image
 * is enabled or be required to be displayed
 **/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

if(!defined('Trackback'))
	include_once(JC_COM_PATH . '/includes/trackback/trackback_cls.php');

// Include our custom cmslib if its not defined
if(!defined('CMSLIB_DEFINED'))
	include_once ((dirname((dirname(dirname(dirname(__FILE__)))))) . '/components/libraries/cmslib/spframework.php');

class JCTrackback{
	var $templateFile   = '';
	var $infoFile       = '';
	var $cms            = null;

	var $model          = null;
	var $modelTable     = null;

	function JCTrackback($type = 'receive'){
	    $this->cms  =& cmsInstance('CMSCore');
	    $this->cms->load('libraries', 'input');
	    
		// Load the model so that it could perform any db operations
		// The captcha.db.php file should be included in the task file
		// We need to check if it is included or not.
		if(!defined('JCCaptchaDB')){
			include_once(JC_MODEL_PATH . '/trackback.db.php');
		}
		$this->model    	= new JCTrackbackDB();
		
		if($type == 'sent'){
		    $this->modelTable   = new JCTrackbackSentDBTable();
		}else {
		    $this->modelTable   = new JCTrackbackDBTable();
		}
		
		
		global $_JC_CONFIG;

		// Template overrides
		if ($_JC_CONFIG->get('overrideTemplate')){
			$customTemplatePath	= JC_CUSTOM_TPL . '/trackback.tpl.html';
			if(file_exists($customTemplatePath))
				$this->templateFile = $customTemplatePath;
			else
				$this->templateFile = JC_COM_PATH . '/templates/_default/trackback.tpl.html';
		} else {
	    	// Define the template file location which will be used
	    	$this->templateFile = JC_COM_PATH . '/templates/_default/trackback.tpl.html';
	    }
	}

	function exists($type, $contentId, $url){
	    return $this->model->exists($type,$contentId, $url);
	}

	function get($property){
	    if($property)
	        return $this->modelTable->$property;
		else
	    	return null;
	}

	function set($property, $value){
	    $this->modelTable->$property    = $value;
	}

	function init($id = '', $ip = '', $postData){
	    $this->modelTable->ip   		= $ip;
	    $this->modelTable->contentid	= $id;

		// Initialize receive scripts
		$this->_initReceive($postData);

	}

	function _initReceive($postData){
	    $this->modelTable->bind($postData);
	}

	function receive(){
	    $this->modelTable->store();

	    $xmlData    = '<?xml version="1.0" encoding="UTF-8"?>'
	    			. '<response>'
	    			. '<error>0</error>'
	    			. '</response>';
	    $this->_print($xmlData);
	}
	
	/**
	 * send()
	 * @params $row: Default joomla $row data
	 *
	 * Returns: true upon success. (boolean)
	 **/
	function send(& $row){
		global $mainframe;

		// Dont keep sending trackbacks.
		
		include_once(JC_COM_PATH . '/includes/trackback/trackback_cls.php');
		$pattern    = "'{trackback}(.*){/trackback}'s";
		preg_match($pattern, $row->text, $matches);
		
		if($matches){
		    $trackbacks = $matches[1];
		    $trackback_arr = explode(",", $trackbacks);

		    foreach($trackback_arr as $url){
		        if(!$this->model->exists('sent', $url)){
		            $site_author    = 'azrul';
		        	$trackback  	= new Trackback($mainframe->getCfg('sitename'), $site_author, 'UTF-8');
					// We need to decode the html entities &amp; to & for url otherwise it would be &amp;
		        	$url        	= html_entity_decode(trim(strip_tags(jcNl2BrStrict($url))));
		        	$contentUrl     = $this->cms->get_path('live');
		        	$contentTitle   = $trackback->cut_short($row->title);
		        	$contentExcerpt = $trackback->cut_short($row->text);
		        	$contentId      = $row->id;
                    
					if($trackback->ping($url, $contentUrl, $contentTitle, $contentExcerpt)){
					    $this->model->insert('sent',$contentId, $url);
					}else{
					    return false;
					}
		        }
			}
		} else {
		    return;
		}
	}
	
	/**
	 * info()
	 * @params
	 *
	 * Returns: HTML code that describes the trackback (string)
	 **/
	function info($id, $option){
		$data   = "To use trackback, copy the url below and use it in your trackback-enabled blogging tools. <br/><br/>"
		        . "You can find out more about trackback <a href=\"http://en.wikipedia.org/wiki/Trackback\">here</a><br/><br/>"
		        . '<fieldset style="overflow:hidden">'
				. "		<legend>trackback url</legend>"
				. cmsSefAmpReplace('index.php?option=com_jomcomment&task=trackback&id='. $id . '&opt=' . $option)
				. "</fieldset>";

		return $data;
	}
	
	function error($errorMessage = ''){
	    $xmlData    = '<?xml version="1.0" encoding="UTF-8"?>'
	    			. '<response>'
	    			. '<error>1</error>'
	    			. '<message>' . $errorMessage . '</message>'
	    			. '</response>';
	    $this->_print($xmlData);
	}

	function _print($data){
	    header('Content-Type: text/xml; charset=ISO-8859-1');
		echo $data;
		exit();
	}
	
	/**
	 * list()
	 * @params $contentId: the current content's id
	 * @params $option: the current option
	 *
	 * Returns: HTML code that displays the trackbacks list for specifict content.
	 **/
	function lists($contentId, $option, $count = false){
	    global $_JC_CONFIG;
	    
	    // If only to display count just return the count.
	    if($count)
	        return $this->model->lists($contentId, $option, $count);

	    $this->cms->load('helper','url');
	    
		$link   = cmsSefAmpReplace('index.php?option=com_jomcomment&task=trackback&id=' . $contentId . '&opt=' . $option);
		$html	= '';

		$template 	= new AzrulJXTemplate();

		$trackbacks	= $this->model->lists($contentId, $option);
		$size		= count($trackbacks);

		$dateFormat = $_JC_CONFIG->get('dateFormat');

		for($i = 0; $i < count($trackbacks); $i++){
		    // Set a reference so that we can add additional properties
		    $row    =& $trackbacks[$i];
		    $row->num = $i + 1;
		    
		    // xss filter
		    $row->excerpt = $this->cms->input->xss_clean($row->excerpt);
		    $row->url 	  = $this->cms->input->xss_clean($row->url);
		    
			// Reformat the trackback date to be the same as configured from the backend.
			$row->date    = strftime($dateFormat, strtotime($row->date));
			
			// Ensure that the link has a http:// prefix
			if(!empty($row->url)){
			    if(substr($row->url, 0, 7) != 'http://' && substr($row->url, 0, 8) != 'https://'){
			        $row->url = 'http://' . $row->url;
				}
			}
		}
		$template->set('trackbacks', $trackbacks);
		$template->set('trackback_link', $link);
		$template->set('debugview', false);

		$html	= $template->fetch($this->templateFile);

		return $html;
	}
}
?>
