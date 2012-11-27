<?php

defined ( '_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.view');
class RecipeViewRecipe extends JView
{
	function display( $tpl = null)
	{
		global $option, $mainframe;
		$model = &$this->getModel();
		
		$recipe = $model->getRecipe();
		$icons = $model->getDietIcons();
		$params 	   =& $mainframe->getParams('com_content');
		
		
		$pathway =& $mainframe->getPathWay();	// breadcrumbs
		
		// If want link to list of all recipes
		$backlink = JRoute::_('index.php?option='. $option . '&view=all');
		$print = JRequest::getBool('print');
		
		
		$this->assignRef('recipe', $recipe);
		$this->assignRef('icons', $icons);
		$this->assignRef('backlink', $backlink);
		$this->assignRef('option', $option);
		$this->assignRef('params' , $params);
		$this->assignRef('print', $print);
		parent::display($tpl);
	}

}