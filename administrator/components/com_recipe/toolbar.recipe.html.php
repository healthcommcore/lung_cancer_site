<?php

defined ( '_JEXEC') or die ('Restricted access');
jimport('joomla.html.toolbar');

	function mycustom($task = '', $msg = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true, $x = false)
	{
		$bar = & JToolBar::getInstance('toolbar');

		//strip extension
		$icon	= preg_replace('#\.[^.]*$#', '', $icon);

		// Add a standard button
		// $bar->appendButton( 'Standard', $icon, $alt, $task, $listSelect, $x );
		if ($msg != '') $bar->appendButton( 'Confirm', $msg, $icon, $alt, $task, $listSelect, $x );
		else $bar->appendButton( 'Standard', $icon, $alt, $task, $listSelect, $x );

		// $bar->appendButton( 'Confirm', 'Are you sure you want to synch this project?', 'delete', $alt, $task, true, false );
	}


class TOOLBAR_recipe {
	function _NEW() {
		JToolBarHelper::title( JText::_( 'Recipe' ).': <small><small>[ Edit ]</small></small>', 'addedit.png' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
	}
	
	function _DEFAULT() {
		JToolBarHelper::title( JText::_('Recipes'), 'generic.png');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList('Are you sure you want to remove this Recipe?','remove');
		JToolBarHelper::addNew();
	
	}

}

class TOOLBAR_recipe_diet {
	function _NEW() {
		JToolBarHelper::title( JText::_( 'Diet' ).': <small><small>[ Edit ]</small></small>', 'addedit.png' );

		JToolBarHelper::save('saveDiet');
		// JToolBarHelper::apply('saveDiet');
		JToolBarHelper::cancel('diets');
	}

	function _DEFAULT() {
		JToolBarHelper::title( JText::_('Diets'), 'generic.png');
		JToolBarHelper::editList('editDiet');
		JToolBarHelper::deleteList('Are you sure you want to remove this Diet?','removeDiet');
		JToolBarHelper::addNew('editDiet');
	
	}

}

class TOOLBAR_recipe_project {
	function _NEW() {
		JToolBarHelper::title( JText::_( 'Project' ).': <small><small>[ Edit ]</small></small>', 'addedit.png' );

		JToolBarHelper::save('saveProject');
		JToolBarHelper::cancel('projects');
	}

	function _DEFAULT() {
		JToolBarHelper::title( JText::_('Projects'), 'generic.png');
		// mycustom('sync','Are you sure you want to synch this project?','synch.png','synch.png','Synch',true, false);
		mycustom('sync','Are you sure you want to synch this project?','copy.png','copy.png','Synch',true, false);
		// mycustom('list', '','list.png','list.png','List',true, false);
		mycustom('list', '','preview.png','preview.png','List',true, false);
		JToolBarHelper::editList('editProject');
		JToolBarHelper::deleteList('Are you sure you want to remove this Project?','removeProject');
		JToolBarHelper::addNew('editProject');
	
	}

	function _SYNC() {
		// JToolBarHelper::title( JText::_( 'Project' ).': <small><small>[ Sync ]</small></small>', 'dbrestore.png' );
		JToolBarHelper::title( JText::_( 'Project' ).': <small><small>[ Sync ]</small></small>', 'generic.png' );

	}
	function _LIST() {
		// JToolBarHelper::title( JText::_( 'Project' ).': <small><small>[ List ]</small></small>', 'list.png' );
		JToolBarHelper::title( JText::_( 'Project' ).': <small><small>[ List ]</small></small>', 'generic.png' );

	}

}


?>