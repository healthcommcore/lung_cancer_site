<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

define('CMSCACHE_TIMEOUT', 1800);

if(!defined('CMS_CACHE_PATH'))
	define('CMS_CACHE_PATH',(dirname(dirname(dirname(dirname(dirname(__FILE__)))))). '/components/libraries/cmslib/cache');

class CMSCache {
	var $cachetype;
	var $cms;
	var $cache;
	
	function CMSCache(){
		// If APC is available, use it
		if(function_exists('apc_store')){
			$this->cache = new CMSCache_APC();
		} else {
			$this->cache = new CMSCache_Filesystem();
		}
	}
	
	// do a function call
	function call(){
		$numargs = func_num_args();
		$fname = '';
		$arglist = array();
		
		$arglist = func_get_args();
		
		// Get function name
		$fname = array_shift($arglist);
		
		// Get language so we can properly name the cached file
		// We know the first arg should be language since the first parameter has been shifted out in the fname above
		//$language	= array_shift($arglist);
		
		// Get cache group
		//$fname = array_shift($arglist);

		// Fix for IIS webservers that doesn't have REQUEST_URI
		if(!isset($_SERVER['REQUEST_URI'])) {
			$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 ); 
			
			if (isset($_SERVER['QUERY_STRING'])) {
				$_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING'];
			}
		}
		$fkey = $fname. serialize($arglist) . serialize($_SERVER['REQUEST_URI']);
		 
		if(!$data = $this->cache->fetch($fkey)){
			$data = call_user_func_array($fname, $arglist);
			$this->cache->store($fkey, $data, CMSCACHE_TIMEOUT);
		}
   		
   		return $data;
	}
	
	function fetch($key){
		return $this->cache->fetch($key);
	}
	
	function store($key, $data, $ttl=CMSCACHE_TIMEOUT){
		$this->cache->store($key, $data, $ttl);
	}
	
	function delete($key){
		$this->cache->delete($key);
	}
	
	// clear all cache
	function clear(){
		$this->cache->clear();
	}
	
}

class CMSCacheBase {
	function fetch($key){
	}
	
	function store($key, $data, $ttl=CMSCACHE_TIMEOUT){
	}
	
	function delete($key){
	}
	
	// clear all cache
	function clear(){
	}
}


class CMSCache_Filesystem extends CMSCacheBase {

  // This is the function you store information with
  function store($key,$data,$ttl = CMSCACHE_TIMEOUT) {

    // Opening the file in read/write mode
    $h = @fopen($this->getFileName($key),'w');
    if (!$h) {
		return; 
		//throw new Exception('Could not write to cache');
    }

    // Serializing along with the TTL
    $data = serialize(array(time()+$ttl,$data));
    if (@fwrite($h,$data)===false) {
      return;
    }
    fclose($h);

  }

  // The function to fetch data returns false on failure
  function fetch($key) {

      $filename = $this->getFileName($key);
      if (!file_exists($filename)) return false;
      $h = fopen($filename,'r');

      if (!$h) return false;

      // Getting a shared lock 
      flock($h,LOCK_SH);

      $data = file_get_contents($filename);
      fclose($h);

      $data = @unserialize($data);
      if (!$data) {

         // If unserializing somehow didn't work out, we'll delete the file
         unlink($filename);
         return false;

      }

      if (time() > $data[0]) {

         // Unlinking when the file was expired
         unlink($filename);
         return false;

      }
      return $data[1];
   }

   function delete( $key ) {

      $filename = $this->getFileName($key);
      if (file_exists($filename)) {
          return unlink($filename);
      } else {
          return false;
      }

   }

	function getFileName($key) {
		// Use CMS_CACHE_PATH instead of mosConfig_cachepath
		return 	CMS_CACHE_PATH . '/cache_' . md5($key);
		//return ini_get('session.save_path') . '/cache' . md5($key);
		//return 	dirname(dirname(__FILE__)). '/cache/cache_' . md5($key);
	}
  
  	// clear all cache
	function clear(){
		 // Define the full path to your folder from root
		global $mosConfig_cachepath;
		
		$path = $mosConfig_cachepath;
		
		// Open the folder
		$dir_handle = @opendir($path);
		
		// Check if the directory is opened correctly.
		if($dir_handle){
			while (false !== ($file = readdir($dir_handle))) {
				if(is_file($path . $file)){
	       			if(substr($file, 0, 5) == 'cache')
						@unlink($path . $file);
		   		}
	   		}
			// Close
			closedir($dir_handle);
		}
	}

} 


class CMSCache_APC extends CMSCacheBase {

        function fetch($key) {
            return apc_fetch($key);
        }

        function store($key,$data,$ttl) {
            return apc_store($key,$data,$ttl);

        }

        function delete($key) {
            return apc_delete($key);

        }
        
        // clear all cache
		function clear(){
			apc_clear_cache("user");
		}

    }
