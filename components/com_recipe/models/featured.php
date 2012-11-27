<?php

defined ( '_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.model');
class ModelRecipesFeatured extends JModel
{
	var $_recipes = null;
	function getList()
	{
		global $mainframe, $mosConfig_lang;
		if (!$this->_recipes)
		
		{
			// Featured list only if checked
			// Category list
			// Get the menu item object
			$menus = &JSite::getMenu();
			$menu  = $menus->getActive();
			
					// Get the page/component configuration
			$params = $mainframe->getParams('com_recipe');
			$featured = $params->get('show_featured', '1');
			$conf =& JFactory::getConfig();

			$lang= substr( $conf->getValue('config.language'), 0,2);
		
			$query = "SELECT * FROM #__recipe WHERE published = '1' AND language = '$lang' AND featured = $featured ORDER BY title";
			// $query = "SELECT * FROM #__recipe WHERE published = '1' AND featured = $featured ORDER BY title";
			$this->_recipes = $this->_getList( $query, 0, 0);	
		}
		return $this->_recipes;
	}
	
}
?>