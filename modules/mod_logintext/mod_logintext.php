<?php
/**
* @version 1.0 $
* @package Login Text module
*	Displays information for users who are not logged-in yet (but nothing when logged in)
* @copyright (C) 2008 HCC
*/
 
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted Access.' );
$my = JFactory::getUser();
if ($my->id != 0 ) return;
 
$id =  $params->get( 'article_id' );

if ($id != '') {
	
	$db =& JFactory::getDBO();

    $sql="SELECT title, introtext, `fulltext` FROM jos_content WHERE (id=".$id.") ";
	$db->setQuery($sql);	
	$resultID=$db->query();
	if ($resultID) {
	        if ($db->getNumRows($resultID) >0) {
					$row =$db->loadRow();
					echo '<h2>'.$row[0].'</h2>';
					echo $row[1];
					echo $row[2];
					// print_r($row);
			}
	}
}
?>
