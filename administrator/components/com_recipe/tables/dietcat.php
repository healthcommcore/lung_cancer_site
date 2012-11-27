<?php

defined ( '_JEXEC') or die ('Restricted access');

class TableDietcat extends JTable

{
	var $id = null;
	var $dietcat = null;
	var $tagname = null;
	
	function __construct (&$db)
	{
		parent::__construct ( '#__recipe_dietcat', 'id', $db);
	}
}
?>