<?php
function cmsdb_query($db, $strSQL){
	return mysqli_query($db->db, $strSQL);
}

function cmsdb_error($db){
	return mysqli_error($db->resource);
}

function cmsdb_num_rows($db){
    return mysqli_num_rows($db->result);
}

function cmsdb_result($db,$row = 0){
	// mysqli dont have mysqli_result need to manually perform it.
	$data = mysqli_fetch_array($db->result);
 	return $data[$row];
}

function cmsdb_free_result($db, $result){
	// Frees memory
	return @mysqli_free_result($db->result);
}

function cmsdb_fetch_object($db){
	return mysqli_fetch_object($db->result);
}

function cmsdb_insert_id($db){
	// Returns auto generated id
	return mysqli_insert_id($db->db);
}

function cmsdb_escape_string($db, $string){
	return mysqli_escape_string($db->db, $string);
}

function cmsdb_real_escape_string($db, $string){

	if(function_exists('mysqli_real_escape_string')){
		return mysqli_real_escape_string($db->db, $string);
	} else {
		return mysqli_escape_string($db->db, $string);
	}

}
?>
