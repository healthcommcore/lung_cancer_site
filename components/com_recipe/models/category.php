<?php

defined ( '_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.model');
class ModelRecipesCategory extends JModel
{
	var $_recipes = null;
	function getList()
	{
		global $mainframe, $mosConfig_lang;;
		if (!$this->_recipes)
		
		{
		
			// Category list
		// Get the menu item object
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		
				// Get the page/component configuration
		$params = $mainframe->getParams('com_recipe');
		$webcat = $params->get('webcat', '');
		$conf =& JFactory::getConfig();
		
		$lang= substr( $conf->getValue('config.language'), 0,2);
		
		if ($webcat != '') $and = "AND webcat = '$webcat'";
		else $and = '';
		
			$query = "SELECT * FROM #__recipe WHERE published = '1' AND language = '$lang' $and ORDER BY title";
			$this->_recipes = $this->_getList( $query, 0, 0);	
		}
		return $this->_recipes;
	}
}
?>