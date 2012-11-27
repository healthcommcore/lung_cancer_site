<?php
// Site Config

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class CMSCfg {

/* Site Settings */
var $offline = '0';
var $offline_message = '';
var $sitename = '';
var $editor = '';
var $list_limit = '20';
var $legacy = '0';

/* Debug Settings */
var $debug = '0';
var $debug_db = '0';
var $debug_lang = '0';

/* Database Settings */
var $dbtype = '';
var $host = '';
var $user = '';
var $password = '';
var $db = '';
var $dbprefix = '';

	/* Server Settings */
	var $secret = '';
	var $gzip = '';
	var $error_reporting = '';
	var $helpurl = '';
	var $xmlrpc_server = '';
	var $ftp_host = '';
	var $ftp_port = '';
	var $ftp_user = '';
	var $ftp_pass = '';
	var $ftp_root = '';
	var $ftp_enable = '';
	
	/* Locale Settings */
	var $offset = '';
	var $offset_user = '';
	
	/* Mail Settings */
	var $mailer = '';
	var $mailfrom = '';
	var $fromname = '';
	var $sendmail = '';
	var $smtpauth = '';
	var $smtpuser = '';
	var $smtppass = '';
	var $smtphost = '';
	
	/* Cache Settings */
	var $caching = '';
	var $cachetime = '';
	var $cache_handler = '';
	
	/* Meta Settings */
	var $MetaDesc = '';
	var $MetaKeys = '';
	var $MetaTitle = '';
	var $MetaAuthor = '';
	
	/* SEO Settings */
	var $sef           = '';
	var $sef_rewrite   = '';
	
	/* Feed Settings */
	var $feed_limit   = 10;
	var $feed_summary = 0;
	var $log_path = '';
	var $tmp_path = '';
	
	/* Session Setting */
	var $lifetime = '';
	var $session_handler = '';

	
	function CMSCfg(){
		// Read and load the configuration file
		$siteroot = dirname($_SERVER['SCRIPT_FILENAME']);
		if(stristr($siteroot, 'administrator')){
			$siteroot = dirname($siteroot);
		}
		
		if(class_exists('JApplication')){
			// J1.5
			$cfg = new JConfig();
			$class_vars = get_class_vars(get_class($cfg));
			foreach ($class_vars as $name => $value) {
			   $this->$name = $value;
			}

		} else {
			// J1.0
			$class_vars = get_class_vars('CMSCfg');
			foreach ($class_vars as $name => $value) {
				$varname = 'mosConfig_'.$name;
				if(isset($GLOBALS[$varname]))
			   $this->$name = $GLOBALS[$varname];
			}
		} 
	}
	
	function init(){
	}
	
	// Return the site configuration
	function get($arg){
		if(isset($this->$arg))
			return $this->$arg;
		else
			return  '';
	}
}


?>
