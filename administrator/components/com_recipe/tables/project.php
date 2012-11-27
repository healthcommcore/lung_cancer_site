<?php

defined ( '_JEXEC') or die ('Restricted access');

class TableProject extends JTable

{
	var $id = null;
	var $project = null;
	var $dbserver = null;
	var $dbuser = null;
	var $dbpwd = null;
	var $dbname = null;
	var $synch1 = 0;
	var $dbserver2 = null;
	var $dbuser2 = null;
	var $dbpwd2 = null;
	var $dbname2 = null;
	var $synch2 = 0;

	
	function __construct (&$db)
	{
		parent::__construct ( '#__recipe_project', 'id', $db);
	}
}
?>