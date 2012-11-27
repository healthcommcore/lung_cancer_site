<?php

defined ( '_JEXEC') or die ('Restricted access');
class HTML_recipe

{
	function editRecipe( $row, $lists, $option)
	
	{
		$editor =& JFactory::getEditor();
		
		?>
						<script language="javascript" type="text/javascript">
	<!--
		var folderimages = new Array;
        
		<?php
			$k = 0;
			echo "folderimages[".$k++. "] = new Array( '','',' - Select Image File - ' );";
			foreach ( $lists['imagefiles'] as $foldername => $folder ) {
				echo "folderimages[".$k++. "] = new Array( '$foldername','',' - Select Image File - ' );";
				foreach ( $folder as $file ) {

					echo "\nfolderimages[".$k++. "] = new Array ( '$foldername','$file','$file' );";
				}
			
			}
		?>
 		function changeDisplayImage() {
			if (document.adminForm.imagefile.value !='') {
				document.adminForm.imagelib.src='../images/recipes/images/' + document.adminForm.imagefolder.value + '/' + document.adminForm.imagefile.value;
			} else {
				document.adminForm.imagelib.src='images/blank.png';
			}
		}	
 		function clearDisplayImage() {
				document.adminForm.imagelib.src='images/blank.png';
		}	
		//-->
		</script>
       <form action="index.php" method="post" name="adminForm" id="adminForm">
        <fieldset class="adminForm">
        
        <legend>Details</legend>
        
        <table class="admintable">
        <tr>
        <td width="100" align="right" class="key">
        	ID :
        </td>
        
        <td>
        	<?php if ($row->id > 0) echo $row->id; ?>
 		</td>
               
        </tr>
        <tr>
        <td width="100" align="right" class="key">
        	Title * :
        </td>
        
        <td>
        	<input class="text_area" type="text"
 name="title" id="title" size="50" maxlength="128" value="<?php echo $row->title; ?>" >
 		</td>
               
        </tr>

        <tr>
        <td width="100" align="right" class="key">
        	Serves *:
        </td>
        
        <td>
        	<input class="text_area" type="text"
 name="serves" id="serves" size="50" maxlength="128" value="<?php echo $row->serves; ?>" >
 		</td>

        <tr>
        <td width="100" align="right" class="key">
        	Category *:
        </td>
        
        <td>
        	<?php
			echo $lists['webcat'];
			?>
 		</td>
        </tr>

        <tr>
        <td width="100" align="right" class="key">
        	Language:
        </td>
        
        <td>
        	<?php
			echo $lists['language'];
			?>
 		</td>
        </tr>
               
        </tr>
  
        <tr>
        <td width="100" align="right" class="key">
        	Intro:
        </td>
        
        <td>
        	<?php
			echo $editor->display( 'intro', $row->intro, '100%', '250', '40', '10');
			?>
 		</td>
               
        </tr>

        <tr>
        <td width="100" align="right" class="key">
        	Ingredients (list format):
        </td>
        
        <td>
        	<?php
			echo $editor->display( 'ingredients', $row->ingredients, '100%', '250', '40', '10');
			?>
 		</td>
               
        </tr>


        <tr>
        <td width="100" align="right" class="key">
        	Instructions *  (list format):
        </td>
        
        <td>
        	<?php
			echo $editor->display( 'instructions', $row->instructions, '100%', '250', '40', '10');
			?>
 		</td>
        </tr>


		
        <tr>
        <td width="100" align="right" class="key">
        	Recipe Image Folder:
        </td>
        
        <td>
        	<?php
			echo $lists['imagefolder'];

			?>
 		</td>
        </tr>
		

					<tr>
						<td valign="top" class="key">
							<label for="imagefile">
								<?php echo JText::_( 'Recipe Image File' ); ?>:
							</label>
						</td>
						<td >
							<?php echo $lists['imagefile']; ?>
						</td>
					</tr>
                    
					<tr>
						<td valign="top" class="key">
							<?php echo JText::_( 'Recipe Image' ); ?>:
						</td>
						<td valign="top">
							<?php
							if (eregi("gif|jpg|png", $row->imagefile)) {
								?>
								<img src="../images/recipes/images/<?php echo $row->imagefile; ?>" name="imagelib" />
								<?php
							} else {
								?>
								<img src="images/blank.png" name="imagelib" />
								<?php
							}
							?>
						</td>
					</tr>

		
        <tr>
        <td width="100" align="right" class="key">
        	Diet Categories :
        </td>
        
        <td>
        	<?php
			echo $lists['dietcat'];
			?>
 		</td>
               
        </tr>

        <tr>
        <td width="100" align="right" class="key">
        	Projects :
        </td>
        
        <td>
        	<?php
			echo $lists['projects'];
			?>
 		</td>
               
        </tr>

        <tr>
        <td width="100" align="right" class="key">
        	Published:
        </td>
        
        <td>
        	<?php
			echo $lists['published'];
			?>
 		</td>
               
        </tr>
        

        <tr>
        <td width="100" align="right" class="key">
        	Featured:
        </td>
        
        <td>
        	<?php
			echo $lists['featured'];
			?>
 		</td>
               
        </tr>
        
        <tr>
        <td width="100" align="right" class="key">
        	Last modified:
        </td>
        
        <td>
 			<?php echo $row->modified; ?>
 		</td>
               
        </tr>
        
        </table>
       
       
       	</fieldset>
		<!-- hidden fields -->
		<!-- for pagination -->
		<input type="hidden" name="limit" value="<?php echo $lists['limit']; ?>" />
		<input type="hidden" name="limitstart" value="<?php echo $lists['limitstart']; ?>" />

		
        <input type="hidden" name="masterid"  value="<?php echo $row->masterid; ?>" />
        <input type="hidden" name="id"  value="<?php echo $row->id; ?>" />
        <input type="hidden" name="option"  value="<?php echo $option; ?>" />
        <input type="hidden" name="task"  value="" />
        
        </form>
        
        
        <?php
	
	}
	// List of Recipes - default view
	function showRecipes( $rows, $option, $pageNav)
	{
		?>
        
        <form action="index.php" method="post"
 name="adminForm">
 		<table class="adminlist">
        <thead>
        <tr>
        	<th width="20">
            	<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows);?> );" />
            
            </th>
            <th class="title">Title</th>
            <th width="15%">Category</th>
            <th width="5%">Image</th>
            <th width="5%">Featured</th>
            <th width="5%">ID</th>
            <th width="5%" nowrap="nowrap">Published</th>
        </tr>
        </thead>
        
        <?php
		jimport('joomla.filter.filteroutput');
		$k = 0;
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		
		{
		$row = &$rows[$i];
		$checked = JHTML::_('grid.id', $i, $row->id);
		$published = JHTML::_('grid.published', $row, $i);
		
		$link = JFilterOutput::ampReplace( 'index.php?option=' . $option . '&task=edit&cid[]=' . $row->id . '&limitstart=' . $pageNav->limitstart);
		?>
        <tr class="<?php echo "row$k"; ?> ">
        <td>
        	<?php echo $checked; ?>
        </td>
        <td>
        	<a href="<?php echo $link; ?>">
        	<?php echo $row->title; ?></a>
        </td>

        <td>
        	<?php echo $row->webcat; ?>
        </td>

        <td>
        	<?php echo ($row->imagefile == '')? 'No': 'Yes' ; ?>
        </td>
		
		        <td align="center">
        	
		<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i?>','<?php echo ($row->featured == 1) ?  'unfeature':  'feature'; ?>')" title="<?php echo ($row->featured == 1) ?  'Unfeature':  'Feature'; ?> Item">
		<img src="images/<?php echo ($row->featured == 1) ?  'tick.png':   'publish_x.png'; ?>" alt="<?php echo ($row->featured == 1) ?  'Featured':  'Not Featured'; ?>" border="0"></a>        </td>

        <td>
        	<?php echo $row->id; ?>
        </td>

        <td align="center">
        	<?php echo $published; ?>
        </td>
        
        </tr>
		
		<?php
        $k = 1 - $k;
		
		}
		?>
		
		<tfoot>
		<td colspan="4"><?php echo $pageNav->getListFooter(); ?></td>
		</tfoot>
        
        </table>
        <input type="hidden" name="option"  value="<?php echo $option; ?>" />
        <input type="hidden" name="task"  value="" />
        <input type="hidden" name="boxchecked"  value="0" />
		
 		</form>
        <?php 
		
	}

	/* DIETS */
	// List of Diets - default view
	function showDiets( $rows, $option, $pageNav)
	{
		?>
        
        <form action="index.php" method="post"
 name="adminForm">
 		<table class="adminlist">
        <thead>
        <tr>
        	<th width="20">
            	<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows);?> );" />
            
            </th>
            <th class="title">Diet Type</th>
            <!--th class="title">Published</th-->
        </tr>
        </thead>
        
        <?php
		jimport('joomla.filter.filteroutput');
		$k = 0;
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		
		{
			$row = &$rows[$i];
			$checked = JHTML::_('grid.id', $i, $row->id);
			// $published = JHTML::_('grid.published', $row, $i);
			
			$link = JFilterOutput::ampReplace( 'index.php?option=' . $option . '&task=editDiet&cid[]=' . $row->id);
			?>
			<tr class="<?php echo "row$k"; ?> ">
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<a href="<?php echo $link; ?>">
				<?php echo $row->dietcat; ?></a>
			</td>
	
			
			</tr>
			
		<?php
			$k = 1 - $k;
		
		}
		?>
		
		<tfoot>
		<td colspan="4"><?php echo $pageNav->getListFooter(); ?></td>
		</tfoot>
        
        </table>
        <input type="hidden" name="option"  value="<?php echo $option; ?>" />
        <input type="hidden" name="task"  value="" />
        <input type="hidden" name="boxchecked"  value="0" />
		
 		</form>
        <?php 
		
	}


	function editDiet ( $row, $option, $lists)
	
	{
		?>
       <form action="index.php" method="post" name="adminForm" id="adminForm">
        <fieldset class="adminForm">
        
        <legend>Details</legend>
        
        <table class="admintable">
        <tr>
        <td width="100" align="right" class="key">
        	Diet Category * :
        </td>
        
        <td>
        	<input class="text_area" type="text"
 name="dietcat" id="dietcat" size="50" maxlength="128" value="<?php echo $row->dietcat; ?>" >
 		</td>
               
        </tr>

        <tr>
        <td width="100" align="right" class="key">
        	CSS image tag name * :
        </td>
        
        <td>
        	<input class="text_area" type="text"
 name="tagname" id="tagname" size="50" maxlength="64" value="<?php echo $row->tagname; ?>" >
 		</td>
               
        </tr>
		

        </table>
       
       
       	</fieldset>
        <input type="hidden" name="id"  value="<?php echo $row->id; ?>" />
        <input type="hidden" name="option"  value="<?php echo $option; ?>" />
        <input type="hidden" name="task"  value="" />
        
        </form>
        <?php 

	}

	/* Projects */
	// List of Projects - default view
	function showProjects( $rows, $option, $pageNav)
	{
		?>
        
        <form action="index.php" method="post"
 name="adminForm">
 		<table class="adminlist">
        <thead>
        <tr>
        	<th width="20">
            	<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows);?> );" />
            
            </th>
            <th class="title">Project</th>
            <th width="5%">ID</th>
        </tr>
        </thead>
        
        <?php
		jimport('joomla.filter.filteroutput');
		$k = 0;
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		
		{
			$row = &$rows[$i];
			$checked = JHTML::_('grid.id', $i, $row->id);
			
			$link = JFilterOutput::ampReplace( 'index.php?option=' . $option . '&task=editProject&cid[]=' . $row->id);
			?>
			<tr class="<?php echo "row$k"; ?> ">
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<a href="<?php echo $link; ?>">
				<?php echo $row->project; ?></a>
			</td>
			<td>
				<?php echo $row->id; ?></a>
			</td>
	
			
			</tr>
			
		<?php
			$k = 1 - $k;
		
		}
		?>
		
		<tfoot>
		<td colspan="4"><?php echo $pageNav->getListFooter(); ?></td>
		</tfoot>
        
        </table>
        <input type="hidden" name="option"  value="<?php echo $option; ?>" />
        <input type="hidden" name="task"  value="" />
        <input type="hidden" name="boxchecked"  value="0" />
		
 		</form>
        <?php 
		
	}


	function editProject ( $row, $option, $lists)
	
	{
		?>
       <form action="index.php" method="post" name="adminForm" id="adminForm">
        <fieldset class="adminForm">
        
        <legend>Details</legend>
        
        <table class="admintable">
        <tr>
        <td width="100" align="right" class="key">
        	Project * :
        </td>
        
        <td>
        	<input class="text_area" type="text"
 name="project" id="project" size="50" maxlength="128" value="<?php echo $row->project; ?>" >
 		</td>
               
        </tr>

        <tr>
        <td width="100" align="right" class="key">
        	Diet Categories :
        </td>
        
        <td>
        	<?php
			echo $lists['dietcat'];
			?>
 		</td>
               
        </tr>

        <tr>
        <td width="100" align="right" class="key">
        	DB Server1 (eg. dev) :
        </td>
        
        <td>
        	<input class="text_area" type="text"
 name="dbserver" id="dbserver" size="50" maxlength="128" value="<?php echo $row->dbserver; ?>" >
 		</td>
               
        </tr>

        <tr>
        <td width="100" align="right" class="key">
        	DB Name1:
        </td>
        
        <td>
        	<input class="text_area" type="text"
 name="dbname" id="dbname" size="50" maxlength="128" value="<?php echo $row->dbname; ?>" >
 		</td>
               
        </tr>
        <tr>
        <td width="100" align="right" class="key">
        	DB User1:
        </td>
        
        <td>
        	<input class="text_area" type="text"
 name="dbuser" id="dbuser" size="50" maxlength="128" value="<?php echo $row->dbuser; ?>" >
 		</td>
               
        </tr>
        <tr>
        <td width="100" align="right" class="key">
        	DB password1:
        </td>
        
        <td>
        	<input  class="text_area" type="password"
 name="dbpwd" id="dbpwd" size="50" maxlength="128" value="<?php echo $row->dbpwd; ?>" >
 		</td>
               
        </tr>

        </tr>


        <tr>
        <td width="100" align="right" class="key">
        	Synchronize 1:
        </td>
        
        <td>
        	<?php
			echo $lists['synch1'];
			?>
 		</td>
               
        </tr>


		
        <tr>
        <td width="100" align="right" class="key">
        	DB Server2 (eg. live) :
        </td>
        
        <td>
        	<input class="text_area" type="text"
 name="dbserver2" id="dbserver2" size="50" maxlength="128" value="<?php echo $row->dbserver2; ?>" >
 		</td>
               
        </tr>

        <tr>
        <td width="100" align="right" class="key">
        	DB Name2:
        </td>
        
        <td>
        	<input class="text_area" type="text"
 name="dbname2" id="dbname2" size="50" maxlength="128" value="<?php echo $row->dbname2; ?>" >
 		</td>
               
        </tr>
        <tr>
        <td width="100" align="right" class="key">
        	DB User2:
        </td>
        
        <td>
        	<input class="text_area" type="text"
 name="dbuser2" id="dbuser2" size="50" maxlength="128" value="<?php echo $row->dbuser2; ?>" >
 		</td>
               
        </tr>
        <tr>
        <td width="100" align="right" class="key">
        	DB password2:
        </td>
        
        <td>
        	<input  class="text_area" type="password"
 name="dbpwd2" id="dbpwd2" size="50" maxlength="128" value="<?php echo $row->dbpwd2; ?>" >
 		</td>
               
        </tr>

         <tr>
        <td width="100" align="right" class="key">
        	Synchronize 2:
        </td>
        
        <td>
        	<?php
			echo $lists['synch2'];
			?>
 		</td>
               
        </tr>
       </table>
       
       
       	</fieldset>
        <input type="hidden" name="id"  value="<?php echo $row->id; ?>" />
        <input type="hidden" name="option"  value="<?php echo $option; ?>" />
        <input type="hidden" name="task"  value="" />
        
        </form>
        <?php 

	}

}

?>