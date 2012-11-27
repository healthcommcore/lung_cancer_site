<?php

defined ( '_JEXEC') or die ('Restricted access');
$mainframe->registerEvent ( 'onSearch', 'botSearchRecipes');
$mainframe->registerEvent ( 'onSearchAreas', 'botSearchRecipesAreas');

function &botSearchRecipesAreas() {
	static $areas = array( 'recipes' => 'Recipes');
	return $areas;
}


// Q: Should we also search categories?
function botSearchRecipes( $text, $phrase='', $ordering='', $areas=null)
{
	// No results if no search text
	if (!$text) {
		return array();
	}
	
	// if search area specified, only if it matches ours ('bios')
	if (is_array($areas) ) {
		if (!array_intersect( $areas, array_keys( botSearchRecipesAreas() ) )) {
			// no match, no results
			return array();
		}
	}
	
	$db =& JFactory::getDBO();
	
	$plugin =& JPluginHelper::getPlugin('search', 'recipes');
	$pluginParams = new JParameter ($plugin->params);
	
	$limit = $pluginParams->get( 'search_limit', 50);
	
	// echo $phrase;
	if ($phrase == 'exact')
	{
		// $text		= $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
		$where = "(LOWER(title) LIKE '%$text%')
			OR (LOWER(ingredients) LIKE '%$text%')
			OR (LOWER(instructions) LIKE '%$text%') ";
	}
	else
	{
		$words = explode( ' ', $text);
		$wheres = array();
		foreach ($words as $word) {
			// $word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
			$wheres[] = "(LOWER(title) LIKE '%$word%')
			OR (LOWER(ingredients) LIKE '%$word%')
			OR (LOWER(instructions) LIKE '%$word%') ";
		}
		if ($phrase == 'all')
		{
			$separator = 'AND';
		}
		else
		{
			$separator = 'OR';
		}
		$where = '(' . implode( ") $separator (", $wheres ) . ')';
	}
	$where .= ' AND published = 1';
	
	switch ($ordering) {
		// We have no dates on bios, so ignore them
		case 'category':
			$order = 'webcat ASC';
			break;
		case 'alpha':
		default:
			$order = 'title DESC';
			break;
	}
	
	// echo $where;
	// $query = "SELECT title, ingredients AS text, '' AS created, ".
	
	$query = "SELECT title,  '' AS created, ".
		"\n CONCAT(ingredients, instructions) AS text, ".
		"\n 'Recipes' AS section," .
		"\n webcat AS category," .
		"\n CONCAT( 'index.php?option=com_recipe&view=recipe&id=', id) AS href," .
		"\n '2' AS browsernav" .
		"\n FROM #__recipe" .
		"\n WHERE $where" .
		"\n ORDER BY $order";
		$db->setQuery ( $query,  0, $limit);
		// $db->setQuery( $query );
		$rows = $db->loadObjectList();
		return $rows;
}
?>