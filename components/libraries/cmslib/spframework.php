<?php

if(!defined('CMSLIB_DEFINED')){

define('CMS_VERSION', 1);
define('CMSLIB_DEFINED', 1);

define('_CMS_JOOMLA15','_CMS_JOOMLA15');
define('_CMS_JOOMLA10','_CMS_JOOMLA10');
define('_CMS_MAMBO','_CMS_MAMBO');
global $CMSDbLog;

/**
 * (c) Azrul.com
 * Single Page MVC application framework
 * + database object access (MySql Only)
 * + build-in superfast template system
 * + library loadin
 * + SIMPLE, MINIMAL, YET COMPLETE 
 */ 
 
class CMSController {
	
	var $cms;
	function CMSController(){
		$this->cms = &cmsInstance('CMSCore');
	}
	
	// Simplistic execute that just call the display
	function execute(){
		$content = $this->display();
		echo $this->_header();
		echo $content;
		echo $this->_footer();
	}
	
	function _header(){
		return '';
	}
	
	function _footer(){
		return '';
	}
	
	// Output stuff
	function display(){
		return '';
	}
}


class CMSCore {

	var $db = null;			// Database object
	var $document = null;	// Document object that gets rendered
	var $paths = null;
    var $cfg = null;
    
	function CMSCore(){
		global $my;

		$this->load('libraries', 'cfg');	
	}
	
	# Similar to PHP mail, but loaded with predefineds vars and preferably uses
	# the CMS default mailing system	
	function mail($to, $subject, $message, $header="null"){
		mail($to, $subject, $message);
	}
	
	# Add custom string to the header section
	# @todo:	
	function add_header($str){
	
	}
	
	# @todo:
	function redirect($str){
	}
	
	// Load (include) the given library, instantiate the object and add them 
	// to cmscore var
	function load($loadtype, $name, $com =""){
		if(isset($this->$name) && empty($com))
			return;
		
		$filename = $this->get_path('root') . "/components/libraries/cmslib/$loadtype" . "/" . $name . ".php";
		
		// If the file is not global, look at the given component name path
		if(!file_exists($filename) || !empty($com)){
			$filename = $this->get_path('root') . "/components/$com/$loadtype" . "/" . $name . ".php";	
		}
		
		if(file_exists($filename)){
			include_once($filename);
		}
		switch ($loadtype){
			case 'libraries':
				$classname = "CMS" . ucfirst($name);
				if(class_exists($classname))
					$this->$name =& new $classname();
					
				break;
			case 'model':
				$classname = "CMS" . ucfirst($name);
				if(class_exists($classname))
					$this->$name =& new $classname();
					
				break;
				
			case 'helper':
				break;	
		}
	}
	
	// Remove the global instance of the object
	function unload($name){
		if(isset($this->$name)){
			unset($this->$name);
		}
	}
	
	
	
	// Return the absolute path for various system
	function get_path($path = 'root', $opt=""){	
		if(!isset($this->path)){
			global $mosConfig_absolte_path;
			global $mosConfig_live_site;
			
			$siteroot = '';
			$https = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS']: 'off';
			
			// Set site's absolute path
			if(!empty($mosConfig_absolte_path)){
				$siteroot = ltrim($mosConfig_absolte_path, '\\/');
				
			} else{
				$siteroot = dirname(dirname(dirname(dirname(__FILE__))));
			}
					
			$this->paths = array(
				'root' => $siteroot, 
				'com' => $siteroot . '/components',
				'modules' => $siteroot . '/modules',
				'plugins' => $siteroot
				);
			$this->paths['plugins'] .= cmsVersion() == _CMS_JOOMLA15 ? '/plugins' : '/mambots';
			
			// Set site live path
			if(!empty($mosConfig_live_site)){
				$this->paths['live']  = $mosConfig_live_site;
				if((strtolower($https) == 'on')){
					$this->paths['live']  = str_replace('http:', 'https:', $this->paths['live']);
				}
				
			} else{
				// Use JURI::base() to get site's url instead.
				$this->paths['live'] = rtrim(JURI::base(), '/');
				
				// Remove '/administrator' from live path if found.
				if(stristr($this->paths['live'], 'administrator'))
				{
					$this->paths['live']	= str_replace('/administrator', '', $this->paths['live']);
				}
				
// 				$this->paths['live']  = (strtolower($https) == 'on') ? 'https://' : 'http://';
// 				$this->paths['live'] .= $_SERVER['HTTP_HOST'];
// 				
// 				if(stristr(ltrim(dirname($_SERVER['SCRIPT_NAME']),'\\/'),'administrator') ){
// 					// for admin, we need to remember the /administrator/ in the path
// 					$this->paths['live'] .= '/'.ltrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '\\/');
// 				} else {
// 	                $this->paths['live'] .= '/'.ltrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
// 				}
// 				// trim for extra trailing slashes
// 				$this->paths['live'] = rtrim($this->paths['live'], '\\/');
				
			}
			
			
			if(cmsVersion() == _CMS_JOOMLA15)
				$this->paths['plugin-live'] = $this->paths['live'] .'/plugins'; 
			else
				$this->paths['plugin-live'] = $this->paths['live'] .'/mambots';
		}
		
		return $this->paths[$path];
	}
	
	// Convert the given url to SEF url
	function sef($url){
		if(class_exists('JRoute'))
			return JRoute::_($url);
			
		elseif(function_exists('sefRelToAbs'))
			return sefRelToAbs($url);
			
		else
			return $url;
	}
	
}

/**
 * CMSDB is a simple abstraction layer for CMSes database, it should provide
 * + simple way to perform typical query such as 
 * 	+ getting array of objects
 * 	+ get single value result
 * 	+ insert/update/delete object easily       
 */ 
class CMSDb {
	var $result; 		// Query result
//	var $result_obj;	// CMSDbResult object
	var $result_data;	// Actual db array result
	var $db;			// MySQL resource
	var $rec_pointer;	// record pointer
	var $prefix;		//
	var $prefix_mask;	//
	var $cms;
	var $log;
	var $lastQuery;
	
    # Contructor
	function CMSDb(){
		global $CMSDbLog; 
		$CMSDbLog = array();
		if (version_compare(PHP_VERSION, "5.0.0", ">=")) {
			// do nothing
		} else {
			//destructor
			register_shutdown_function(array(&$this, '__destruct'));

			//constructor
			$argcv = func_get_args();
			call_user_func_array(array(&$this, '__construct'), $argcv); 
		}
	}
	
	function __construct(){
	 	//code for constructor goes here
	 	# This should be the only area that we will find Joomla Code
		if(cmsVersion() == _CMS_JOOMLA15){
            #Use Joomla 1.5 variables
            $dbo = & JFactory::getDBO();
            $this->db   	=& $dbo->_resource;
            $config         = new JConfig();
            $this->prefix   = $config->dbprefix;
            
		} elseif(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
		    #Use Joomla 1.0 variables
		    global $database, $mosConfig_dbprefix;
		    
			$this->db			= $database->_resource;
			$this->prefix		= $mosConfig_dbprefix;
		}

		$this->prefix_mask	= "#__";
	}
	
	function __destruct(){
	 	if(is_resource($this->result))
			@cmsdb_free_result($this);
		//@mysql_free_result($this->result);
	}

	function init(&$cms){
	    $this->cms = $cms;

		// Check which database and load the appropriate function calls
		if(is_resource($this->db)){
			$resourceType   = get_resource_type($this->db);

			if($resourceType == 'mysql link'){
				require_once($this->cms->get_path('root') . '/components/libraries/cmslib/db/mysql.php');
			}
		}else{
			require_once($this->cms->get_path('root') . '/components/libraries/cmslib/db/mysqli.php');
		}
	}
	
	// --------------------------------------------------------------------
	
	# Execute the given query
	function query($sql){
		if(is_resource($this->result))
		    @cmsdb_free_result($this);
		//	mysql_free_result($this->result);
					
		if(isset($this->result_data))
			unset($this->result_data);
		
		$sql = str_replace($this->prefix_mask, $this->prefix, $sql);
		$this->lastQuery = $sql; 
		$this->result   = cmsdb_query($this, $sql);
//		$this->result = mysql_query($sql, $this->db) or die($sql . mysql_error());
			 
		return $this->result;
	}
	
	// Return the first row of the data
	function first_row(){
		$data = $this->get_object_list();
		if(count($data) > 0){
			return $data[0];
		} else 
			return null;
	}
	
	function getFields($tableName){
	    $result = array();
		$sql    = 'SHOW FIELDS FROM ' . $tableName;
		$this->query($sql);

		$fields = $this->get_object_list();

		foreach($fields as $field){
		    $result[$field->Field]  = preg_replace('/[(0-9)]/','',$field->Type);
		}
		return $result;
	}
	
	# Return number of rows of last query result
	function num_rows(){
	    return cmsdb_num_rows($this);
		//return mysql_num_rows($this->result);
	}
	
	# Return a single row of result Object from current #_result pointer
	# Assume $_result is not null	
	function row(){
	    cmsdb_fetch_object($this);
		//mysql_fetch_object($this->result);
	}
	
	# Result first row/first colum result
	function get_value($query =""){
//		return $this->first_row();
		if(!empty($query)){
			$this->query($query);
		}
		
		if($this->result){
			//if(mysql_num_rows($this->result) > 0)
			if(cmsdb_num_rows($this) > 0){
			    //var_dump(cmsdb_result($this,0));
			    return cmsdb_result($this, 0);
				//return mysql_result($this->result, 0);
			}
			else
				return 0;
		}else
			return 0;
	}
	
	# If a query is given
	function get_count($table, $cond){
		$sql = "SELECT COUNT(*) FROM `$table` WHERE ";
		$sql .= $this->implode_data($cond, "AND");
		
		return $this->get_value($sql);
	}
	
	# Return an array of objects
	function get_object_list(){
		//return $this->result_obj->get_object_list();
		
		if (isset($this->result_data) && count($this->result_data) > 0){
			return $this->result_data;
		}
		
		//if($this->result && (mysql_num_rows($this->result) > 0)){
		if($this->result && (cmsdb_num_rows($this) > 0 )){
			//$this->_data_seek(0);
			//while ($row = mysql_fetch_object($this->result)) {
			while($row = cmsdb_fetch_object($this)){
			   $this->result_data[] = $row;
			}
			
			return $this->result_data;
		} else {
			$this->result_data = array();
			return $this->result_data;
		}
	} 
	
	# @todo:
	function get_where($table, $cond){
	}
	
	# @todo:
	function set_where($table, $new, $cond){
	}
	
	
	# Insert the given array into the specified table
	# Data can either be an array of object
	function insert($table, $data){
		$sql = "INSERT INTO `$table` SET ";
		
		if(is_object($data)){
			$data = $this->object_to_array($data);
		}
		
		foreach( $data as $key => $val){
			if(is_numeric($data[$key]))
				$data[$key] = "`$key`=" . intval($this->_escape($val));
			else
				$data[$key] = "`$key`='" . $this->_escape($val) . "'";
		}

		$sql .= implode(",", $data);
		return $this->query($sql);
	}

	# Insert the given array into the specified table
	# Data can either be an array of object
	function update($table, $data, $cond){
		$sql = "UPDATE `$table` SET ";
		
		if(is_object($data)){
			$data = $this->object_to_array($data);
		}
		
		# Only build the sting if it is an array, otherwise assume it is a complete
		# query string		
		if(is_array($data)){
			$sql .= $this->implode_data($data);
		} else if(is_string($data)){
			$sql .= " $data "; 
		}
		
		$sql .= " WHERE ";
		
		if(is_array($cond)){
			$sql .= $this->implode_data($cond, "AND");
		} else if(is_string($cond)){
			$sql .= " $cond ";
		}
		
		return $this->query($sql);
	}
	
	
	# Return the last insert id
	function get_insert_id(){
	    return cmsdb_insert_id($this);
		//return mysql_insert_id($this->db);
	}
	
	# Escape the given string
	function _escape($str){
	    if(!is_array($str)){
			if (version_compare(phpversion(), '4.3.0', '<')) {
			    return cmsdb_escape_string($this, $str);
			} else 	{
			    return cmsdb_real_escape_string($this, $str);
			}
		}else {
            return $str;
		}
	}
	
	// --------------------------------------------------------------------
	/* Utilities */
	
	function object_to_array($obj) {
       $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
       $arr = array();
       foreach ($_arr as $key => $val) {
               $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
               $arr[$key] = $val;
       }
       return $arr;
	}
	
	# Implode the given data and return a string
	function implode_data($data, $tag=" , "){
		$sql = "";
		
		if(is_object($data)){
			$data = $this->object_to_array($data);
		}
		
		# where condition
		if(is_array($data)){		
			foreach( $data as $key => $val){
				if(is_numeric($data[$key]))
					$data[$key] = "`$key`=" . intval($this->_escape($val));
				else
					$data[$key] = "`$key`='" . $this->_escape($val) . "'";
			}
	
			$sql .= implode(" $tag ", $data);
		} else {
			$sql = $data;
		}
		return $sql;
	}
	
	function debug(){
		global $CMSDbLog;
		
		$sum = 0;
		if(!empty($CMSDbLog)){
			foreach ( $CMSDbLog as $v ) {
				$sum += $v[1];
			}
		}

		echo '<div style="position:absolute;top:0px;left0px;padding:10px;background:#FFFFFF"><pre>';
		echo "Total query: " . count($CMSDbLog) .'<br/>';
		echo "Total time: " . $sum .'&nbsp;ms<br/>';
		foreach($CMSDbLog as $row){
// 			echo $row[0] . '<br/>';
// 			echo $row[1] . '<br/>';
		}
		
		echo '</pre></div>';
	}
}

/**
 * MosDBTable equivalent. the only thing we will EVER implement is
 * bind
 * load
 * store   
 */ 
class CMSDbTable {
	var $_table;
	var $_key;
	var $_db;
	var $_fields = null; // array with field info object
	
	function CMSDbTable($tablename, $primarykey){
		$this->_table 	= $tablename;
		$this->_key		= $primarykey;
		$this->_db 		= & cmsInstance('CMSDb');
	}
	
	// Load the table row
	function load($key){
		$result = $this->_db->query("SELECT * FROM {$this->_table} WHERE `{$this->_key}`='{$key}' ");
		if($result){
			$row = $this->_db->first_row();
			//$row    = $result;
			$rowvars = get_object_vars($this);
			foreach($rowvars as $key => $val){
				if(!empty($row->$key))
					$this->$key = $row->$key;
			}
		}
	}
	
	// If primary key is set, update the table, if not, create a new one and
	// update primary key, ignore the column that is not valid 
	function store(){
		$tablekey = $this->_key;
		$data = array();
		
		// get table fields info, if we don't have them yet
		if($this->_fields == NULL){
			$this->_db->query("SHOW COLUMNS FROM `{$this->_table}`");
			$fields = $this->_db->get_object_list();
			$this->_fields = array();
			foreach($fields as $row){
				$this->_fields[$row->Field] = $row;
			}
		}
		//print_r($this->_fields); exit;
		
		// Only add the data array those that is actually have a valid field
		$rowvars = get_object_vars($this);
		foreach($rowvars as $key => $val){
			if(isset($this->_fields[$key])){
				$data[$key] = $val;
			}
		}
		
		if($this->$tablekey > 0){
			// Update
			$cond = array($this->_key => $this->$tablekey);
			$this->_db->update($this->_table, $data, $cond);
			
		} else {
			// Insert new & update the primary key id
			$this->_db->insert($this->_table, $data);
			$key = $this->_key;
			$this->$key      = cmsdb_insert_id($this->_db);
		}
	}
	
	// Bind the given array to current var, is available
	function bind($data){
		if(empty($data) || !is_array($data)) return;
		foreach($data as $key => $val){
			$this->$key = $val;
		}
	}
}
/**
 * Some HTML helper code
 */ 
class CMSHtml {
	function show_tabbed_nav($links, $captions, $active = 0){
		
		$cms	=& cmsInstance('CMSCore');
		
		$html = '
		<div id="azheadnav">';
		
		for($i =0; $i < count($links); $i++){
			if($i == $active)
				$html .= '<span><a id="current" href="'. $links[$i] .'">'. $captions[$i] .'</a></span>';
			else
				$html .='<span><a href="'. $links[$i] .'">'. $captions[$i] .'</a></span>';
				
			//$html .= '<span><a href="'. $links[$i] .'">'. $captions[$i] .'</a></span>';
		}
		
		$html .= '
		</div>';
		
		return $html;
	}
}

// Return the single instance of any of the above class
// @example: 	$cms=&cmsInstance('CMSCore');
// 				$cms=&cmsInstance('CMSDb');
function & cmsInstance($class) {
    static $instances;

    if (!is_array($instances)) {
        $instances = array();
    }

    if (!isset($instances[$class])) {

        // Load some default libraries for the CMSCore
        if($class == 'CMSCore' || $class == 'CMSDb' ){

            // CMSCore needs to have reference to the cmsdb
			$instances['CMSCore'] 	=& new CMSCore();
			$instances['CMSDb'] 	=& new CMSDb();

            $instances['CMSCore']->db =& $instances['CMSDb'];
        	$instances['CMSCore']->load('libraries', 'input');

        	// Now CMSCore is properly initialized, need to init the CMSDb
        	$instances['CMSDb']->init($instances['CMSCore']);
		} else {
		    $instances[$class] =& new $class;
		}
    }

    return $instances[$class];
}

function cmsVersion(){
	static $result;

	if(!isset($result)){
		$path	= dirname(dirname(dirname(dirname(__FILE__))));
		
		if(class_exists('JFactory') && defined('_JEXEC') && file_exists($path . '/libraries/joomla/factory.php')){
			$result	= _CMS_JOOMLA15;
		} else if(defined('_VALID_MOS') && class_exists('joomlaVersion')){
		    #Joomla 1.0.X?
		    $result = _CMS_JOOMLA10;
		}elseif(defined('_VALID_MOS') && class_exists('mamboCore')){
		    #Mambo X.X?
		    $result = _CMS_MAMBO;
		}
	}
	return $result;
}

}
