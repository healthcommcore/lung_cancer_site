<?php
global $CMSDbLog;
/**
 * Wrapper for mysql_query
 **/
function cmsdb_query($db, $query, $resultmode = ''){
	global $CMSDbLog;
	
	
	$time_start = microtime();
	// Returns result
	$result = mysql_query($query, $db->db);
	
	$time_end = microtime();
	$time = $time_end - $time_start;
	$CMSDbLog[]=array($query, $time);;
	
	return $result;
}

/**
 * Wrapper for mysql_error
 **/
function cmsdb_error($db){
	return mysql_error($db->db);
}

/**
 * Wrapper for mysql_result
 **/
function cmsdb_result($db,$row){
	return mysql_result($db->result, 0);
}

/**
 * Wrapper for mysql_free_result
 **/
function cmsdb_free_result($db){
	return @mysql_free_result($db->db);
}

/**
 * Wrapper for mysql_num_rows
 **/
function cmsdb_num_rows($db){
	return mysql_num_rows($db->result);
}

/**
 * Wrapper for mysql_fetch_object
 **/
function cmsdb_fetch_object($db){
	return mysql_fetch_object($db->result);
}

/**
 * Wrapper for mysql_insert_id
 **/
function cmsdb_insert_id($db){
	// Returns auto generated id
	return mysql_insert_id($db->db);
}


/**
 * Wrapper for mysql_escape_string
 **/
function cmsdb_escape_string($db, $string){
	return mysql_escape_string($string, $db->db);
}

/**
 * Wrapper for mysql_real_escape_string
 **/
function cmsdb_real_escape_string($db, $string){
	if(function_exists('mysql_real_escape_string')){
		return mysql_real_escape_string($string, $db->db);
	} else {
		return mysql_escape_string($string, $db->db);
	}
}
?>
