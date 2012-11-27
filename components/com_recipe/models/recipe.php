<?php

defined ( '_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.model');
class ModelRecipesRecipe extends JModel
{
	var $_recipe = null;
	var $_icons = null;
	var $_id = null;
	
	function __construct()
	{
		global $mainframe;
		parent::__construct();
		// First check if an ID has been set up as a parameter
		$params = $mainframe->getParams('com_recipe');
		$id = $params->get('id', 0);
		
		if (! $id) {
		
			$id = JRequest::getVar('id', 0);
		}
		$this->_id = $id;
	}
	
	function getRecipe()
	{
		if (!$this->_recipe)
		
		{
			$conf =& JFactory::getConfig();
			// Language not really an issue, because IDs already distinguish between recipes
			// $lang= substr( $conf->getValue('config.language'), 0,2);

			$query = "SELECT * FROM #__recipe WHERE id = " . $this->_id; // . "'  AND language = '$lang' ";
			
			//$query = "SELECT * FROM #__recipe WHERE id = '" . $this->_id . "'";
			$this->_db->setQuery( $query);
			
			$this->_recipe = $this->_db->loadObject();
			if (!$this->_recipe->published)
			
			{
			
				JError::raiseError( 404, "Invalid ID provided");
			}
			

		}
		return $this->_recipe;
	}

	function getDietIcons()
	{
		if (!$this->_icons)
		
		{
			// Retrieve array of icon images					

			// $query = "SELECT d.imagefile FROM #__recipe_recipediet r,#__recipe_dietcat d WHERE r.recipeid ='" . $this->_id . "' AND r.dietid = d.id ";
			$query = "SELECT d.tagname FROM #__recipe_recipediet r INNER JOIN #__recipe_dietcat d ON r.dietid = d.id  INNER JOIN #__recipe_dietproject p ON r.dietid = p.dietid WHERE r.recipeid ='" . $this->_id . "'";
			$this->_db->setQuery( $query);
			
			$this->_icons = $this->_db->loadRowList();
			
			// print_r($this->_icons);


		}
		return $this->_icons;
	}
}
?>