<?php

defined ( '_JEXEC') or die ('Restricted access');
jimport('joomla.application.component.controller');
class RecipeController extends JController
{
	function __construct ( $default = array())
	{
		parent::__construct ( $default );
		// To override default task -> function naming convention
		$this->registerTask ( 'add', 'edit');
		$this->registerTask ( 'apply', 'save');
		$this->registerTask ( 'sync', 'syncProject');
		$this->registerTask ( 'list', 'listProject');
	}

	
	function publish()
	{
	global $option, $mainframe;
	$row =& JTable::getInstance( 'Recipe', 'Table');	
	$cid =JRequest::getVar('cid',array(), '', 'array');
	$db =& JFactory::getDBO();	
	$limit =JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
	$limitstart =JRequest::getVar('limitstart', 0);

	$row->publish($cid, 1);


	// $mainframe->redirect('index.php?option='.  $option);
	$this->setRedirect('index.php?option='.  $option .'&limit='. $limit .'&limitstart='. $limitstart );

	}
	
	function unpublish()
	{
		global $option, $mainframe;
		$row =& JTable::getInstance( 'Recipe', 'Table');	
		$cid =JRequest::getVar('cid',array(), '', 'array');
		$db =& JFactory::getDBO();	
		$limit =JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
		$limitstart =JRequest::getVar('limitstart', 0);
	
		$row->publish($cid, 0);
		
	
		// $mainframe->redirect('index.php?option='.  $option);
		$this->setRedirect('index.php?option='.  $option .'&limit='. $limit .'&limitstart='. $limitstart );
	}
	

	function feature()
	{
		global $option, $mainframe;
		$row =& JTable::getInstance( 'Recipe', 'Table');	
		$cid =JRequest::getVar('cid',array(), '', 'array');
		$db =& JFactory::getDBO();	
		$limit =JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
		$limitstart =JRequest::getVar('limitstart', 0);
		
	
		if (count ($cid))
		{
			$cids = implode( ',', $cid);
			
			$query = "UPDATE #__recipe SET featured=1 WHERE id IN ( $cids)";
			$db->setQuery( $query);
			if ( !$db->query())
			{
				echo "<script>alert('".$db->getErrorMsg(). "');
					
						window.history.go(-1); </script>\n";
			}
		}
	
		// $mainframe->redirect('index.php?option='.  $option);
		$this->setRedirect('index.php?option='.  $option .'&limit='. $limit .'&limitstart='. $limitstart );
	
	}
	
	function unfeature ()
	{
		global $option, $mainframe;
		$row =& JTable::getInstance( 'Recipe', 'Table');	
		$cid =JRequest::getVar('cid',array(), '', 'array');
		$db =& JFactory::getDBO();	
		$limit =JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
		$limitstart =JRequest::getVar('limitstart', 0);
		
	
		if (count ($cid))
		{
			$cids = implode( ',', $cid);
			
			$query = "UPDATE #__recipe SET featured=0 WHERE id IN ( $cids)";
			$db->setQuery( $query);
			if ( !$db->query())
			{
				echo "<script>alert('".$db->getErrorMsg(). "');
					
						window.history.go(-1); </script>\n";
			}
		}
	
		// $mainframe->redirect('index.php?option='.  $option);
		$this->setRedirect('index.php?option='.  $option .'&limit='. $limit .'&limitstart='. $limitstart );
	
	}

	function edit()
	{
		global $option, $mainframe;
		$row =& JTable::getInstance( 'Recipe', 'Table');
		$limit =JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
		$limitstart =JRequest::getVar('limitstart', 0);

		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$id = $cid[0];
		$row->load($id);
		// print_r($row);
					
		$lists = array();
		
		$categories = array( 
			'0'=> array('value' => '', 'text' => 'Select category'),
			'1'=> array('value' => 'Appetizers', 'text' => 'Appetizers'),
			'2'=> array('value' => 'Beverages', 'text' => 'Beverages'),
			'3'=> array('value' => 'Breakfasts', 'text' => 'Breakfasts'),
			'4'=> array('value' => 'Desserts', 'text' => 'Desserts'),
			'5'=> array('value' => 'Main Courses', 'text' => 'Main Courses'),
			'6'=> array('value' => 'Soups and Salads', 'text' => 'Soups and Salads'),
			'7'=> array('value' => 'Side Dishes', 'text' => 'Side Dishes'),
			'8'=> array('value' => 'Snacks', 'text' => 'Snacks'),
		);
	
		$languages = array( 
			'1'=> array('value' => 'en', 'text' => 'english'),
			'2'=> array('value' => 'es', 'text' => 'spanish'),
			'3'=> array('value' => 'pt', 'text' => 'portuguese'),
			'4'=> array('value' => 'ru', 'text' => 'russian'),
			'5'=> array('value' => 'ht', 'text' => 'creole'),
		);
		$imagefolders = array();
		$imagefolders[] = array('value' => "", 'text' => " - Select image folder - ");
	
		$imagefiles = array();
		
		$recipepath = JPATH_SITE .'/images/recipes/images/';
		
		// Retrieve list of image folders
		if (is_dir($recipepath)) {
			$dirlist = scandir($recipepath);
			// print_r($dirlist);
			if ($dirlist) {
				foreach ($dirlist as $dir) {
					if (! eregi('\.', $dir)) {
						// echo "<br>folder $dir";
						$imagefolders[] = array('value' => "$dir", 'text' => "$dir");
					}
				}
				foreach ($dirlist as $dir) {
					if (! eregi('\.', $dir)) {
						$filelist = scandir($recipepath .'/'. $dir);
						foreach ($filelist as $file) {
							if (eregi("gif|jpg|png", $file)) {
								$imagefiles[$dir][] = "$file";
								// $imagefiles[$dir][] = array('value' => "$file", 'text' => "$file");
							}
						}
					}
				}
				// Retrieve list of all image files
				reset($dirlist);
			}
		}
		
		
		$lists['imagefiles'] = $imagefiles;
	
		if ($row->imagefile || ($row->imagefile != '')) {
				$file = substr( strrchr( $row->imagefile,'/'),1);
				$folder = substr( $row->imagefile, 0, stripos( $row->imagefile,'/'));
				//echo "folder = $folder, ends at position ". stripos( $row->imagefile,'/');
		}	
		else {
			$file = null;
			$folder = null;
		}
	
		// Retrieve list of diet categories
		$db =& JFactory::getDBO();	
			$query = "SELECT id, dietcat FROM #__recipe_dietcat ORDER BY dietcat ASC";
			$db->setQuery( $query);
			$rows = $db->loadObjectList();
			if ( $db->getErrorNum())
			{
				echo $db->stderr();
				return false;
			}
			
		// Retrieve list of diets selected	
			$query = "SELECT dietid FROM #__recipe_recipediet WHERE recipeid = $id";
			// echo $query;
			$db->setQuery( $query);
			$diets = $db->loadResultArray();
			if ( $db->getErrorNum())
			{
				echo $db->stderr();
				return false;
			}
			//echo '<br>Diet list:';
			//print_r($diets);
			
			//echo '<br>Diets assigned list:';
	
			//print_r ($rows);
		// Generate HTML for checkboxes
		
		$dietlist = '';
		foreach ($rows as $diet )
		{
			$dietlist .=  '<input type="checkbox" name="dietcat[]" ';
			$dietlist .= (in_array($diet->id, $diets) == TRUE) ? 'checked="Yes"': '';
			$dietlist .=  ' value="'. $diet->id .'"> ';
			$dietlist .= $diet->dietcat .'<br>';
		}
		$lists['dietcat'] = $dietlist;
		
		// Do same for projects assigned 
		// Retrieve list of projects
		$db =& JFactory::getDBO();	
			$query = "SELECT id, project FROM #__recipe_project ORDER BY project ASC";
			$db->setQuery( $query);
			$rows = $db->loadObjectList();
			if ( $db->getErrorNum())
			{
				echo $db->stderr();
				return false;
			}
			
		// Retrieve list of projects selected	
			$query = "SELECT projectid FROM #__recipe_recipeproject WHERE recipeid = $id";
			$db->setQuery( $query);
			$projects = $db->loadResultArray();
			if ( $db->getErrorNum())
			{
				echo $db->stderr();
				return false;
			}
			// print_r($projects);
			
	
			// print_r ($rows);
		// Generate HTML for checkboxes
		
		$projectlist = '';
		foreach ($rows as $project )
		{
			$projectlist .=  '<input type="checkbox" name="projects[]" ';
			$projectlist .= (in_array($project->id, $projects) == TRUE) ? 'checked="Yes"': '';
			$projectlist .=  ' value="'. $project->id .'"> ';
			$projectlist .= $project->project .'<br>';
		}
		$lists['projects'] = $projectlist;
	
		
		$lists['webcat'] = JHTML::_('select.genericList', $categories, 'webcat', 'class="inputbox" ' , 'value', 'text', $row->webcat);
	
		$lists['language'] = JHTML::_('select.genericList', $languages, 'language', 'class="inputbox" ' , 'value', 'text', $row->language);		
	
		//  Generate javascript to change file list
				
		$javascript	= 'onchange="changeDynaList( \'imagefile\', folderimages, document.adminForm.imagefolder.options[document.adminForm.imagefolder.selectedIndex].value, 0, 0);clearDisplayImage();"';
		
			
		$directory			= '/images/recipes/images/';
	
		$lists['imagefolder'] = JHTML::_('select.genericList', $imagefolders, 'imagefolder', 'class="inputbox" '. $javascript , 'value', 'text', $folder);
		//  Generate javascript to change image file
	
		$javascript			= 'onchange="changeDisplayImage();"';
		if ($folder != null) {	
			$directory			= '/images/recipes/images/'.$folder;
			$lists['imagefile']	= JHTML::_('list.images',  'imagefile', $file, $javascript, $directory );
		} else  {	
		
			$lists['imagefile'] = '<select name="imagefile" id="imagefile" class="inputbox" size="1" onchange="changeDisplayImage();"><option value="" selected="selected">- Select Image File -</option></select>';
		}
		
		
		$lists['featured'] = JHTML::_('select.booleanlist', 'featured', 'class="inputbox"', $row->featured);
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
		$lists['limit'] = $limit;
		$lists['limitstart'] = $limitstart;
		
		HTML_recipe::editRecipe($row, $lists, $option);
	
	}

	function save ()
	{
		global $option, $mainframe;
		$row =& JTable::getInstance( 'Recipe', 'Table');	
	
		$limit =JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
		$limitstart =JRequest::getVar('limitstart', 0);
	
		if (!$row->bind(JRequest::get('post')))
		{
			echo "<script>alert('".$row->getError(). "');
				
					window.history.go(-1); </script>\n<br />\n";
			exit();
		}
		
		
		
		$row->title  = JRequest::getVar('title','','post', 'string', JREQUEST_ALLOWRAW);
		$row->serves  = JRequest::getVar('serves','','post', 'string', JREQUEST_ALLOWRAW);
		$row->intro  = JRequest::getVar('intro','','post', 'string', JREQUEST_ALLOWRAW);
		$row->ingredients  = JRequest::getVar('ingredients','','post', 'string', JREQUEST_ALLOWRAW);
		$row->instructions  = JRequest::getVar('instructions','','post', 'string', JREQUEST_ALLOWRAW);
		$row->webcat = JRequest::getVar('webcat','','post', 'string', JREQUEST_ALLOWRAW);
	
		
		/* Check for valid inputs */
		if ( ( empty( $row->title))  || ( empty( $row->serves)) || ( empty( $row->instructions)) 
			|| ($row->webcat == '') )
			{
			echo "<script>alert('Please fill in all the required fields in the form');
					window.history.go(-1); </script>\n<br />\n";
			exit();
		
		}
	
		
		// $row->webcat = JRequest::getVar('webcat','','post', 'string', JREQUEST_ALLOWRAW);
		// $row->language = JRequest::getVar('language','','post', 'string', JREQUEST_ALLOWRAW);
	
	
		// Image file handling >> Test if selected
		
		
		if ( (JRequest::getVar('imagefolder','','post', 'string', JREQUEST_ALLOWRAW) != '') && 
			(JRequest::getVar('imagefile','','post', 'string', JREQUEST_ALLOWRAW) != '') ) {
				$row->imagefile = JRequest::getVar('imagefolder','','post', 'string', JREQUEST_ALLOWRAW) . '/' . 
					JRequest::getVar('imagefile','','post', 'string', JREQUEST_ALLOWRAW);
		}
		else $row->imagefile = '';
	
		// Handle diets relationships
		$dietcat = JRequest::getVar('dietcat','','post', 'string', JREQUEST_ALLOWRAW);
		
		// Handle existing recipes differently than new ones
		// Update relationship table recipe - diet categories
		// Make sure relationship doesn't already exist 
		// Remove relationships that are not checked
		$db =& JFactory::getDBO();	
		
		if ($row->id != 0 ) {
			$query = "DELETE FROM #__recipe_recipediet WHERE recipeid = $row->id";
				// echo $query;
				$db->setQuery( $query);
				if (!$db->query())
			{
				echo $db->stderr();
					// return false;
			}
		}
	
		
		// Handle project relationships
		$projects = JRequest::getVar('projects','','post', 'string', JREQUEST_ALLOWRAW);
		
		// Delete all previous relationships for this recipe
		//	from  table recipe - diet categories
		//	to ensure relationship  old relationships that are not checked are removed 
		if ($row->id != 0 ) {
			$query = "DELETE FROM #__recipe_recipeproject WHERE recipeid = $row->id";
				// echo $query;
				$db->setQuery( $query);
				if (!$db->query())
			{
				echo $db->stderr();
					// return false;
			}
		}
	
		


		$row->featured = JRequest::getVar('featured','','post', 'string', JREQUEST_ALLOWRAW);
		$row->published =JRequest::getVar('published',0, 'post');
		$row->modified = date('Y:m:d H:i:s');
		$row->masterid =JRequest::getVar('masterid',0, 'post');
		// $row->masterid = 0;		// default for recipes manually entered
		
		if (! $row->store())
		{
			echo $row->getError();
			echo "<script>alert('".$row->getError(). "');
				
					window.history.go(-1); </script>\n<br />\n";
			exit();
		
		}
		// Insert new recipe - diet relationships
		
		foreach ($dietcat as $dietid) 
		{
			$query = "INSERT INTO #__recipe_recipediet (dietid, recipeid) VALUES ( $dietid, $row->id ) ";
			// echo $query;
			$db->setQuery( $query);
			if (!$db->query())
			{
				echo $db->stderr();
				// return false;
			}
		}
		
		foreach ($projects as $projectid) 
		{
			$query = "INSERT INTO #__recipe_recipeproject (projectid, recipeid) VALUES ( $projectid, $row->id ) ";
			// echo $query;
			$db->setQuery( $query);
			if (!$db->query())
			{
				echo $db->stderr();
				// return false;
			}
		}
		
		switch ($this->_task)
		{
			case 'apply':
				$msg = 'Changes to Recipe saved';
				$link = 'index.php?option=' . $option .'&limit='. $limit .'&limitstart='. $limitstart . '&task=edit&cid[]=' . $row->id;
				break;
			
			case 'save':		
			default:
				$msg = 'Recipe saved';
				$link = 'index.php?option=' . $option .'&limit='. $limit .'&limitstart='. $limitstart;
			break;
		
		}
		// $mainframe->redirect($link, $msg);
		$this->setRedirect($link, $msg);
	}

	// Allow for pagination
	function showRecipes ()
	{
		global $option, $mainframe;
		$limit =JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
		$limitstart =JRequest::getVar('limitstart', 0);
		$db =& JFactory::getDBO();	
	
		// Retrieve total number of items first
		$query = "SELECT count(*) FROM #__recipe";
		$db->setQuery( $query);
		$total = $db->loadResult();
	
	
		// $query = "SELECT * FROM #__recipe ORDER BY title ASC";
		$query = "SELECT id, title, webcat, imagefile, featured, published FROM #__recipe ORDER BY title ASC";
		$db->setQuery( $query, $limitstart, $limit);
		$rows = $db->loadObjectList();
		if ( $db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit);
	
	
		HTML_recipe::showRecipes( $rows, $option, $pageNav);
	
	}

	function remove()
	{
		global $option, $mainframe;
		$cid =JRequest::getVar('cid',array(), '', 'array');
		$db =& JFactory::getDBO();	
		$limit =JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
		$limitstart =JRequest::getVar('limitstart', 0);
	
		if (count ($cid))
		{
			$cids = implode( ',', $cid);
			
			// Remove recipe relationships first
			//	Recipe <-> diet
			// Delete all diet relationships with these recipes first
			$query = "DELETE FROM #__recipe_recipediet WHERE recipeid in (  $cids) ";
			$db->setQuery( $query);
			if (!$db->query())
			{
				echo $db->getErrorMsg();
					// return false;
			}
			
			// Project <-> recipe
			// Delete all previous relationships for this recipe
			//	from  table recipe - diet categories
			//	to ensure relationship  old relationships that are not checked are removed 
			$query = "DELETE FROM #__recipe_recipeproject WHERE recipeid IN ( $cids )";
			// echo $query;
			$db->setQuery( $query);
			if (!$db->query())
			{
				echo "<script>alert('".$db->getErrorMsg(). "');
						window.history.go(-1); </script>\n";
			}
			
			$query = "DELETE FROM #__recipe WHERE id IN ( $cids)";
			$db->setQuery( $query);
			if ( !$db->query())
			{
				echo "<script>alert('".$db->getErrorMsg(). "');
						window.history.go(-1); </script>\n";
			}
		}
		// $mainframe->redirect('index.php?option='.  $option);
		$this->setRedirect('index.php?option='.  $option .'&limit='. $limit .'&limitstart='. $limitstart );
	}
	
	/* DIET */
	
	/* SHow all diets */
	function diets() {
		global $option, $mainframe;
		$limit =JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
		$limitstart =JRequest::getVar('limitstart', 0);
		$db =& JFactory::getDBO();	
	
		// Retrieve total number of items first
		$query = "SELECT count(*) FROM #__recipe_dietcat";
		$db->setQuery( $query);
		$total = $db->loadResult();
	
		$query = "SELECT id, dietcat FROM #__recipe_dietcat ORDER BY dietcat ASC";
		$db->setQuery( $query, $limitstart, $limit);
		$rows = $db->loadObjectList();
		if ( $db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit);
	
	
		HTML_recipe::showDiets ( $rows, $option, $pageNav);
	
	}

	function editDiet()
	{
		global $option;
		$row =& JTable::getInstance( 'Dietcat', 'Table');
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$id = $cid[0];
		$row->load($id);
			
		// $lists = array();
		// $imagefiles = array();
		// $dietpath = '/images/recipes/diets/';
		// $lists['imagefiles']	= JHTML::_('list.images',  'imagefile', $file, $javascript, $dietpath );
		
		
	
		// HTML_recipe::editDiet($row, $lists, $option);
		HTML_recipe::editDiet($row, $option, $lists);
	}

	function saveDiet ()
	{
		global $option, $mainframe;
		$row =& JTable::getInstance( 'Dietcat', 'Table');	
	
	
		if (!$row->bind(JRequest::get('post')))
		{
			echo "<script>alert('".$row->getError(). "');
				
					window.history.go(-1); </script>\n<br />\n";
			exit();
		}
		
		
		
		$row->dietcat  = JRequest::getVar('dietcat','','post', 'string', JREQUEST_ALLOWRAW);
		
		
		/* Check for valid inputs */
		if  ( empty( $row->dietcat) || empty( $row->tagname))
			{
			echo "<script>alert('Please fill in all the required fields in the form');
					window.history.go(-1); </script>\n<br />\n";
			exit();
		
		}
		if (! $row->store())
		{
			echo $row->getError();
			echo "<script>alert('".$row->getError(). "');
				
					window.history.go(-1); </script>\n<br />\n";
			exit();
		
		}
		
		switch ($this->_task)
		{
			/* case 'apply':
				$msg = 'Changes to Diet saved';
				$link = 'index.php?option=' . $option . '&task=editDiet&cid[]=' . $row->id;
				break;
				*/
			
			case 'save':		
			default:
				$msg = 'Diet saved';
				$link = 'index.php?option=' . $option . '&task=diets';
				break;
		
		}
		// $mainframe->redirect($link, $msg);
		$this->setRedirect($link, $msg);
	}

	function removeDiet()
	{
		global $option, $mainframe;
		$cid =JRequest::getVar('cid',array(), '', 'array');
		$db =& JFactory::getDBO();	
	
		if (count ($cid))
		{
			$cids = implode( ',', $cid);

			// Delete all project relationships with this diet first
			$query = "DELETE FROM #__recipe_dietproject WHERE dietid in (  $cids)";
			$db->setQuery( $query);
			if (!$db->query())
			{
				echo $db->getErrorMsg();
					// return false;
			}
			
			// Delete all recipe relationships with this diet first
			$query = "DELETE FROM #__recipe_recipediet WHERE dietid in (  $cids) ";
			$db->setQuery( $query);
			if (!$db->query())
			{
				echo $db->getErrorMsg();
					// return false;
			}
			// Delete diet from diet table
			
			$query = "DELETE FROM #__recipe_dietcat WHERE id IN ( $cids)";
			$db->setQuery( $query);
			if ( !$db->query())
			{
				echo "<script>alert('".$db->getErrorMsg(). "');
						window.history.go(-1); </script>\n";
			}
		}
		// $mainframe->redirect('index.php?option='.  $option);
		$this->setRedirect('index.php?option='.  $option. '&task=diets') ;
	}


	/* PROJECT */
	
	/* SHow all projects */
	function projects() {
		global $option, $mainframe;
		$limit =JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
		$limitstart =JRequest::getVar('limitstart', 0);
		$db =& JFactory::getDBO();	
	
		// Retrieve total number of items first
		$query = "SELECT count(*) FROM #__recipe_project";
		$db->setQuery( $query);
		$total = $db->loadResult();
	
		$query = "SELECT id, project FROM #__recipe_project ORDER BY project ASC";
		$db->setQuery( $query, $limitstart, $limit);
		$rows = $db->loadObjectList();
		if ( $db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit);
	
	
		HTML_recipe::showProjects ( $rows, $option, $pageNav);
	
	}

	function editProject()
	{
		global $option;
		$row =& JTable::getInstance( 'Project', 'Table');
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$id = $cid[0];
		$row->load($id);

		// Retrieve list of diet categories
		$db =& JFactory::getDBO();	
			$query = "SELECT id, dietcat FROM #__recipe_dietcat ORDER BY dietcat ASC";
			$db->setQuery( $query);
			$rows = $db->loadObjectList();
			if ( $db->getErrorNum())
			{
				echo $db->stderr();
				return false;
			}
			
		// Retrieve list of diets selected	
			$query = "SELECT dietid FROM #__recipe_dietproject WHERE projectid = $id";
			//echo $query;
			$db->setQuery( $query);
			$diets = $db->loadResultArray();
			if ( $db->getErrorNum())
			{
				echo $db->stderr();
				return false;
			}
			//echo '<br>Diet list:';
			//print_r($diets);
			
			//echo '<br>Diets assigned list:';
	
			//print_r ($rows);
		// Generate HTML for checkboxes
		
		$dietlist = '';
		foreach ($rows as $diet )
		{
			$dietlist .=  '<input type="checkbox" name="dietcat[]" ';
			$dietlist .= (in_array($diet->id, $diets) == TRUE) ? 'checked="Yes"': '';
			$dietlist .=  ' value="'. $diet->id .'"> ';
			$dietlist .= $diet->dietcat .'<br>';
		}
		$lists['dietcat'] = $dietlist;
		$lists['synch1'] = JHTML::_('select.booleanlist', 'synch1', 'class="inputbox"', $row->synch1);
		$lists['synch2'] = JHTML::_('select.booleanlist', 'synch2', 'class="inputbox"', $row->synch2);
					
		HTML_recipe::editProject($row, $option, $lists);
	}

	function saveProject ()
	{
		global $option, $mainframe;
		$row =& JTable::getInstance( 'Project', 'Table');	
	
	
		if (!$row->bind(JRequest::get('post')))
		{
			echo "<script>alert('".$row->getError(). "');
				
					window.history.go(-1); </script>\n<br />\n";
			exit();
		}
		
		
		
		$row->project  = JRequest::getVar('project','','post', 'string', JREQUEST_ALLOWRAW);
		
		
		/* Check for valid inputs */
		if  ( empty( $row->project))
			{
			echo "<script>alert('Please specify the project name');
					window.history.go(-1); </script>\n<br />\n";
			exit();
		
		}
		if (! $row->store())
		{
			echo $row->getError();
			echo "<script>alert('".$row->getError(). "');
				
					window.history.go(-1); </script>\n<br />\n";
			exit();
		
		}
		$db =& JFactory::getDBO();	
		
		// remove previous project - diet relationships for this project

		if ($row->id != 0 ) {
			$query = "DELETE FROM #__recipe_dietproject WHERE projectid = $row->id";
				$db->setQuery( $query);
				if (!$db->query())
			{
				echo $db->stderr();
					// return false;
			}
		}
		
		// Insert new project - diet relationships
		// Handle diets relationships
		$dietcat = JRequest::getVar('dietcat','','post', 'string', JREQUEST_ALLOWRAW);
		
		foreach ($dietcat as $dietid) 
		{
			$query = "INSERT INTO #__recipe_dietproject (dietid, projectid) VALUES ( $dietid, $row->id ) ";
			// echo $query;
			$db->setQuery( $query);
			if (!$db->query())
			{
				echo $db->stderr();
				// return false;
			}
		}
		
		
		switch ($this->_task)
		{
			/* case 'apply':
				$msg = 'Changes to Diet saved';
				$link = 'index.php?option=' . $option . '&task=editDiet&cid[]=' . $row->id;
				break;
				*/
			
			case 'save':		
			default:
				$msg = 'Project saved';
				$link = 'index.php?option=' . $option . '&task=projects';
				break;
		
		}
		// $mainframe->redirect($link, $msg);
		$this->setRedirect($link, $msg);
	}

	function removeProject()
	{
		global $option, $mainframe;
		$cid =JRequest::getVar('cid',array(), '', 'array');
		$db =& JFactory::getDBO();	
	
		if (count ($cid))
		{
			$cids = implode( ',', $cid);

			// Delete all diet relationships with this project first
			$query = "DELETE FROM #__recipe_dietproject WHERE projectid in (  $cids)";
			$db->setQuery( $query);
			if (!$db->query())
			{
				echo $db->stderr();
					// return false;
			}
		
			// Delete all recipe relationships with this project first
			$query = "DELETE FROM #__recipe_recipeproject WHERE projectid in (  $cids) ";
			$db->setQuery( $query);
			if (!$db->query())
			{
				echo $db->getErrorMsg();
					// return false;
			}
			// Delete diet from diet table
			
			$query = "DELETE FROM #__recipe_project WHERE id IN ( $cids)";
			$db->setQuery( $query);
			if ( !$db->query())
			{
				echo "<script>alert('".$db->getErrorMsg(). "');
						window.history.go(-1); </script>\n";
			}
		}
		$this->setRedirect('index.php?option='.  $option. '&task=projects') ;
	}

	function listProject()
	{
		global $option, $mainframe;
		$project =& JTable::getInstance( 'Project', 'Table');
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$db =& JFactory::getDBO();	


		if (count($cid) > 1) {
			echo '<br>More than one project selected...';
		}
		
		// Retrieve this project's parameters
		foreach ($cid as $id) {
		
			$project->load($id);
			echo "<h3>Listing project  $project->project</h3>";
		
			// Retrieve all published recipes for this project
			$query = "SELECT r.* FROM #__recipe_recipeproject p LEFT JOIN #__recipe r ON p.recipeid = r.id WHERE projectid =  $id AND r.published = 1 ORDER BY r.title";
			$db->setQuery( $query);
			if (!$db->query())
			{
				echo $db->getErrorMsg();
					// return false;
			}
			$recipes = $db->loadObjectList();
			if ( $db->getErrorNum())
			{
				echo $db->stderr();
				return false;
			}
			echo '<h3>There are ', count($recipes), ' project recipes :</h3>';
	
			foreach ($recipes as $recipe) {
				echo '<br>', $recipe->title;
			}
		}
		
/*
		// Retrieve this project's parameters
		$query = "SELECT p.* FROM #__recipe_project p WHERE id =  $id";
		$db->setQuery( $query);
		if (!$db->query())
		{
			echo $db->getErrorMsg();
				// return false;
		}
		$project = $db->loadObject();
	*/	


	}
	
	function synchOneProject( $id, $project, $dbserver, $dbname, $dbuser, $dbpwd) 
	{
		// Verify that target isn't the same as us 
		//	>> need to handle other possibilities, 127.0.0.1, % ?
		//	File paths?
		$db =& JFactory::getDBO();	
		
		// list all recipes for project
		
	
		// Retrieve all published recipes for this project
		$query = "SELECT r.* FROM #__recipe_recipeproject p LEFT JOIN #__recipe r ON p.recipeid = r.id WHERE projectid =  $id AND r.published = 1";
		$db->setQuery( $query);
		if (!$db->query())
		{
			echo $db->getErrorMsg();
				// return false;
		}
		$recipes = $db->loadObjectList();
		if ( $db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
		foreach ($recipes as $recipe) {
			// print_r($row->title);
		}
		echo '<br>There are ', count($recipes), ' published project recipes to transfer';
		
		if (count($recipes) == 0 ) {
			// echo 'no recipes to transfer';
			return;
		}
			
		

		// Try to connect to target server
				
		
		echo '<br>Connecting to target server... ';
		
		$targetdb=mysql_connect($dbserver,$dbuser ,$dbpwd, true);
		if ($targetdb) 
			mysql_select_db($dbname ,$targetdb);
		else {
			// Can't get any error msg if failed
			echo '<br>Unable to connect to target server: ';
			echo mysql_error();
			return;
		}

		echo '<br>Connected to target server... ';
		// Backup target tables, just in case
		// Target tables may not exist ???
			$sql = "DROP TABLE IF EXISTS back_recipe_recipediet";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "CREATE TABLE back_recipe_recipediet SELECT * FROM jos_recipe_recipediet";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "DROP TABLE IF EXISTS back_recipe";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "CREATE TABLE back_recipe SELECT * FROM jos_recipe";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "DROP TABLE IF EXISTS back_recipe_dietcat";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "CREATE TABLE back_recipe_dietcat SELECT * FROM jos_recipe_dietcat";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "DROP TABLE IF EXISTS back_recipe_recipediet";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "CREATE TABLE back_recipe_recipediet SELECT * FROM jos_recipe_recipediet";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
		
			$sql = "DROP TABLE IF EXISTS back_recipe_project";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "CREATE TABLE back_recipe_project SELECT * FROM jos_recipe_project";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "DROP TABLE IF EXISTS back_recipe_recipeproject";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "CREATE TABLE back_recipe_recipeproject SELECT * FROM jos_recipe_recipeproject";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "DROP TABLE IF EXISTS back_recipe_dietproject";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			$sql = "CREATE TABLE back_recipe_dietproject SELECT * FROM jos_recipe_dietproject";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}


		// DELETE existing recipes and recipe-diet relationships first.  
		//	REquirement: recipe component and tables must have been set up
		// Note that tables may not exist ??
		//	if new
				// Delete all relationships with this diet first
			$sql = "DELETE FROM jos_recipe_recipediet";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
				// Stop processing
			}
			// Delete diet from diet table
			
			$sql = "DELETE FROM jos_recipe_dietcat";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
			}
			
			$sql = "DELETE FROM jos_recipe";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
			}

			$sql = "DELETE FROM jos_recipe_project";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
			}
			$sql = "DELETE FROM jos_recipe_recipeproject";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo 'Error deleting  jos_recipe_recipeproject from target db';
				echo mysql_error($targetdb);
			}
			
			$sql = "DELETE FROM jos_recipe_dietproject";
	 		$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo 'Error deleting  jos_recipe_dietproject from target db';
				echo mysql_error($targetdb);
			}
			// echo '<br>Deleted target database entries... ';
			$query = "SET NAMES 'utf8';";
			mysql_query($query, $targetdb) or die(mysql_error()); 			
			
			//  Transfer recipes, including ids
			// 	Separate insert statements to avoid buffer overflow? 
			$sql = "INSERT INTO jos_recipe (id, title, webcat, serves, intro, ingredients,
				instructions, imagefile, masterid, published, featured, language, modified) VALUES ";
			$values = '';

			foreach ($recipes as $recipe) {
				// Set masterid = id on target db
				if ($values != '')  $values .= ',';
				$values .= " ( $recipe->id, '" .addslashes($recipe->title) . "','" .
				// $values = " ( $recipe->id, '" .addslashes($recipe->title) . "','" .
					addslashes($recipe->webcat) . "','" .
					addslashes($recipe->serves) . "','" .
					addslashes($recipe->intro) . "','" .
					addslashes($recipe->ingredients) . "','" .
					addslashes($recipe->instructions) . "','" .
					addslashes($recipe->imagefile) . "'," .
					$recipe->id . "," .
					($recipe->published) . "," .
					($recipe->featured) . ",'" .
					addslashes($recipe->language) . "','" .
					addslashes($recipe->modified) .
					"') ";
			
			}
			$sql = $sql . $values;
			// echo $sql;
			$result = mysql_query($sql, $targetdb ) ;
			if (!$result)
			{
				echo mysql_error($targetdb);
				return;
			}
		echo '<br>Project recipes transferred...';

		// Transfer all diet categories (or only those used by this project?)
		//	>> all for now
		$query = "SELECT d.* FROM #__recipe_dietcat d";	// WHERE projectid =  $id";
		$db->setQuery( $query);
		if (!$db->query())
		{
			echo $db->getErrorMsg();
				// return false;
		}
		$rows = $db->loadObjectList();

		//  Transfer these entries
		$sql = "INSERT INTO jos_recipe_dietcat (id, dietcat, tagname) VALUES ";
		$values = '';
		foreach ($rows as $row) {
		if ($values != '')  $values .= ',';
			// print_r($row->title);
			$values .= " ( $row->id,'". $row->dietcat ."', '". $row->tagname ."') ";
				
		}
		$sql = $sql . $values;
		// echo $sql;
		$result = mysql_query($sql, $targetdb ) ;
		if (!$result)
		{
			echo mysql_error($targetdb);
			return;
		}

		echo '<br>Diet categories transferred...';

		
		$rids = '';
		
		foreach ($recipes as $recipe) {
			if ($rids != '') $rids .= ',';
			$rids .= $recipe->id;
		}
		
		// Transfer recipe - diet entries
		$query = "SELECT d.* FROM #__recipe_recipediet d WHERE recipeid in  ($rids)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			echo $db->getErrorMsg();
				// return false;
		}
		$rows = $db->loadObjectList();
		// echo 'recipe -> diets :';
		// print_r($rows);

		//  Transfer these entries
		$sql = "INSERT INTO jos_recipe_recipediet (dietid, recipeid) VALUES ";
		$values = '';
		foreach ($rows as $row) {
		if ($values != '')  $values .= ',';
			// print_r($row->title);
			$values .= " ( $row->dietid, $row->recipeid) ";
				
		}
		$sql = $sql . $values;
		// echo $sql;
		$result = mysql_query($sql, $targetdb ) ;
		if (!$result)
		{
			echo mysql_error($targetdb);
			return;
		}
		echo '<br>Recipe diet list transferred...';

			//	only for recipes transferred ?
		// Retrieve all recipes - project entries
			
			
		// Transfer limited project entry
		//	only current project id (for consistency)
		//	no server information

		//  Transfer single project
		$sql = "INSERT INTO jos_recipe_project (id, project) VALUES ($project->id, '". addslashes($project->project) . "')";
		// echo $sql;
		$result = mysql_query($sql, $targetdb ) ;
		if (!$result)
		{
			echo mysql_error($targetdb);
			return;
		}
		echo '<br>Project transferred...';
		
		// Retrieve all recipes - project entries
		$query = "SELECT p.recipeid FROM #__recipe_recipeproject p WHERE projectid =  $id";
		$db->setQuery( $query);
		if (!$db->query())
		{
			echo $db->getErrorMsg();
				// return false;
		}
		$rows = $db->loadObjectList();

		//  Transfer project
		$sql = "INSERT INTO jos_recipe_recipeproject (recipeid, projectid) VALUES ";
		$values = '';
		foreach ($rows as $row) {
		if ($values != '')  $values .= ',';
			// print_r($row->title);
			$values .= " ( $row->recipeid, $id) ";
			
				
		}


		$sql = $sql . $values;
		// echo $sql;
		$result = mysql_query($sql, $targetdb ) ;
		if (!$result)
		{
			echo mysql_error($targetdb);
			return;
		}
		
		echo '<br>Project recipe list transferred...';
		// Transfer project -diet relationships
		// 	jos_recipe_dietproject

		// Retrieve all  entries
		$query = "SELECT p.dietid FROM #__recipe_dietproject p WHERE projectid =  $id";
		$db->setQuery( $query);
		if (!$db->query())
		{
			echo $db->getErrorMsg();
				// return false;
		}
		$rows = $db->loadObjectList();

		//  Transfer project
		$sql = "INSERT INTO jos_recipe_dietproject (dietid, projectid) VALUES ";
		$values = '';
		foreach ($rows as $row) {
		if ($values != '')  $values .= ',';
			// print_r($row->title);
			$values .= " ( $row->dietid, $id) ";
			
				
		}
		$sql = $sql . $values;
		// echo $sql;
		$result = mysql_query($sql, $targetdb ) ;
		if (!$result)
		{
			echo mysql_error($targetdb);
			return;
		}
		echo '<br>Project diet list transferred...';
		/*
		$host = 'hccdev3.dfci.harvard.edu';
		$user = 'therese';
		$pwd = 'macremote';
		
		// File transfers (transfer all instead of trying to identify specific files or remove existing ones
		// Remove any eixsting file
		$ftpFilename = '/tmp/mdbFtp' . time(). '.txt';
		unlink( '/tmp/mdbFtp*');
		unlink( $ftpFilename);
		
		$netrcFilename = '/tmp/netrc' . time(). '.txt';
		unlink( '/tmp/netrc*');
		unlink( $netrcFilename);
		
		// $ftpFilename = '/Library/WebServer/Documents/live_sites/masterrecipe/images/recipes/mdbFtp' . date('Ymd'). '.txt';
		$fp = fopen( $ftpFilename, "w");
		if (! $fp) {
				// echo 'Error fopen';
				// STOP - error log
					error_log( 'Error fopen file'. $ftpFilename);
		}
		chown(  $ftpFilename, 'therese');
		// chmod( $ftpFilename, 0600);
		$fp2 = fopen( $netrcFilename, "w");
		if (! $fp2) {
				// echo 'Error fopen';
				// STOP - error log
					error_log( 'Error fopen file' . $netrcFilename);
		}
		chown(  $netrcFilename, 'therese');
		// chmod( $netrcFilename, 0600);
		
		// if ( fwrite( $fp, "\n". 'ftp -i' . 'hccdev3.dfci.harvard.edu' 
			//				== false)
		if ( fwrite( $fp2, "machine $host") == false) echo 'Error fwrite'; 
		if ( fwrite( $fp2, "\nlogin $user") == false) echo 'Error fwrite'; 
		if ( fwrite( $fp2, "\npassword $pwd") == false) echo 'Error fwrite'; 
		if ( fwrite( $fp, "\n". 'cd ' .'hd2/images') == false) echo 'Error fwrite'; 
		if ( fwrite( $fp, "\n". 'mkdir recipes') == false) echo 'Error fwrite'; 
		if ( fwrite( $fp, "\n". 'cd recipes') == false) echo 'Error fwrite'; 
		if ( fwrite( $fp, "\n". 'lcd ' . JPATH_SITE. '/images/recipes') == false) echo 'Error fwrite'; 
		if ( fwrite( $fp, "\n". 'mkdir icons') == false) echo 'Error fwrite'; 
		if ( fwrite( $fp, "\n". 'cd ' .'icons') == false) echo 'Error fwrite'; 
		if ( fwrite( $fp, "\n". 'lcd ' .'icons') == false) echo 'Error fwrite'; 
		if ( fwrite( $fp, "\n". 'mput *.jpg') == false) echo 'Error fwrite'; 
		fclose($fp);
		fclose($fp2);
		$cmd ='ftp ' .  ' -N' . $netrcFilename . ' '. $host . ' < '. $ftpFilename ;
		echo $cmd;

		$status = system('ftp ' .  ' -N:' . $netrcFilename . ' '. $host . ':therese < '. $ftpFilename );
		// Change ownership of files to 'www'
		// $status = system('chown -R www ' );
		*/
		
		echo '<br>Project synchronization complete';
	
	
	}
	/* Only ONE project can be synched at a time */
	/* >> Select which target server */
	/* >> View recipes for project and Confirm */
	/* >> View processing */
	function syncProject()
	{
		global $option, $mainframe;
		$project =& JTable::getInstance( 'Project', 'Table');
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		// $cid =JRequest::getVar('cid',array(), '', 'array'); ??
		// >> WHAT IS MORE THAN 1 PROJECT SELECTED ?

		// $id = $cid[0];
		// $row->load($id);
		// print_r($row);
		
		if (count($cid) > 1) {
			echo '<br>More than one project selected...';
		}
		
		// Retrieve this project's parameters
		foreach ($cid as $id) {
		
			$project->load($id);
			echo "<h3>Synchronizing project  $project->project</h3>";
			$conf =& JFactory::getConfig();
			// print_r($conf);
			
			// Each project may have several target servers
			//	Check each to see if the synch flag is set
			//	Verify that connection information is set
			//	Then make sure that the target is not == source
			//	(>> localhost vs other IP)
			

		
			// Target 1:
			if ($project->synch1) {
				if ( ( trim($project->dbserver) == '') || ( trim($project->dbname) == '') || ( trim($project->dbuser) == '')
					|| ( trim($project->dbpwd) == '') ) {
					echo '<br>Target 1: Unable to synchronize, missing database connection information';
					continue; 
					
				}	
		
				if ( (strcasecmp ($conf->getValue('config.host'), $project->dbserver) == 0) && 
					( strcasecmp( $conf->getValue('config.db'), $project->dbname) == 0)) {
					// 
					echo '<br>Target 1: The target configuration is the same as this source. No synchronization will be performed';
					break; 
					
					
				}
				else {
					echo "<h3>Synchronizing target 1 ($project->dbserver, $project->dbname )</h3>";
					$this->synchOneProject( $id, $project, $project->dbserver, $project->dbname, $project->dbuser, $project->dbpwd);
				}
			}

			if ($project->synch2) {
				if ( ( trim($project->dbserver2) == '') || ( trim($project->dbname2) == '') || ( trim($project->dbuser2) == '')
					|| ( trim($project->dbpwd2) == '') ) {
					echo '<br>Target 2: Unable to synchronize, missing database connection information';
					continue; 
					
				}	
		
				if ( (strcasecmp ($conf->getValue('config.host'), $project->dbserver2) == 0) && 
					( strcasecmp( $conf->getValue('config.db'), $project->dbname2) == 0)) {
					// 
					echo '<br>Target 2: The target configuration is the same as this source. No synchronization will be performed';
					break; 
					
					
				}
				else {
					echo "<h3>Synchronizing target 2 ($project->dbserver2, $project->dbname2 )</h3>";
					$this->synchOneProject( $id, $project, $project->dbserver2, $project->dbname2, $project->dbuser2, $project->dbpwd2);
				}
			}

		
		}	
		
	}

}

?>
