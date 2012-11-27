<?php

defined ( '_JEXEC') or die ('Restricted access');
require_once( JApplicationHelper::getPath('toolbar_html'));
// echo 'task =' . $task;

switch($task)
{
	case 'edit':
	case 'add':
		TOOLBAR_recipe::_NEW();
		break;

	case 'diets':
	case 'saveDiet':
	case 'removeDiet':
		TOOLBAR_recipe_diet::_DEFAULT();
		break;

	case 'editDiet':
		TOOLBAR_recipe_diet::_NEW();
		break;

	case 'projects':
	case 'saveProject':
	case 'removeProject':
		TOOLBAR_recipe_project::_DEFAULT();
		break;

	case 'editProject':
		TOOLBAR_recipe_project::_NEW();
		break;

	case 'sync':	/* if in main toolbar */
	case 'syncProject':	/* if in project toolbar */
		//echo 'toolbar.recipe.php: syncProject';
		TOOLBAR_recipe_project::_SYNC();
		break;
		
	case 'list':	/* if in main toolbar */
	case 'listProject':	/* if in project toolbar */
		//echo 'toolbar.recipe.php: listProject';
		TOOLBAR_recipe_project::_LIST();
		break;

	default:
		TOOLBAR_recipe::_DEFAULT();
		break;

}

?>