<?php
defined ( '_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.view');
class RecipeViewCategory extends JView
{
	function display( $tpl = null)
	{
		global $option;
		global $mainframe;
		$model = &$this->getModel();
		$list = $model->getList();
		
		$params = $mainframe->getParams('com_recipe');
		$intro = $params->get('comp_description', '');
		$showimgintro = $params->get('show_imgintro', '');
		$pagetitle = $params->get('page_title', '');
		$showtitle = $params->get('show_page_title', '');
		$linktitle = $params->get('link_titles', '');
		$suffix = $params->get('pageclass_sfx', '');
		
		for ($i = 0; $i < count($list); $i++)
		{
			$row =& $list[$i];
			// Set link   here
			$row->link = JRoute::_('index.php?option=' . $option . '&id=' . $row->id . '&view=recipe');
		
		}
	
		$this->assignRef('list', $list);
		$this->assignRef('intro', $intro);
		$this->assignRef('showimgintro', $showimgintro);
		$this->assignRef('pagetitle', $pagetitle);
		$this->assignRef('showtitle', $showtitle);
		$this->assignRef('linktitle', $showtitle);
		$this->assignRef('suffix', $suffix);
		parent::display($tpl);
	}

}