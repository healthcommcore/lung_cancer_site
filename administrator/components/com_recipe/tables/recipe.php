<?php

defined ( '_JEXEC') or die ('Restricted access');

class TableRecipe extends JTable

{
	var $id = null;
	var $webcat = null;
	var $title = null;
	var $serves = null;
	var $intro = null;
	var $ingredients = null;
	var $instructions = null;
	var $imagefile = null;
	var $masterid = null;
	var $featured = null;
	var $language = null;
	var $published = null;
	var $modified = null;
	
	function __construct (&$db)
	{
		parent::__construct ( '#__recipe', 'id', $db);
	}
}
?>