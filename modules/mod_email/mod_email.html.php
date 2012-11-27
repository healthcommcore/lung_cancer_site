<?php
/**
* @version 1.0 $
* @package Ad
* @copyright (C) 2008 Therese Lung DFCI/CCBR/HCC
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
 
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted Access.' );


class HTML_email {
	function displayEmail ($errmsg ) {
	global $option;
	global $view;
	global $itemid, $id;
	global $supportList;
		
		if ($errmsg != '') {
			echo '<span class="alert">'. $errmsg . '</span>';
		}

		?>
<div>
<p class="contentheading">Email my friend or family member</p>
<p>
<b>Making changes is much easier when you have help and support from the people around you.

Send friends and family members to <a href="http://Buddies.TrackMyChanges.org">Buddies.TrackMyChanges.org.</a></b>
</p>
<!--p>All fields are required</p-->
<?php
		echo '<form action="'. JRoute::_('index.php?option='. $option. '&view='. $view.'&task=send&id='.$id. '&Itemid='. $itemid) .'" method="post" name="email" onSubmit="return processEmail (this)">'; 
        ?>
<form>
<table>
<tbody>

		<tr>
<td class="key" width="150"><label for="mm_support">My friend or family member</label></td>
		<td>
		<select name="mm_support" id="mm_support" class="inputbox">
		
		<option value="" selected="selected">Select</option>
		<?php foreach ($supportList as $support) { ?>
			<option value="<?php echo $support?>"><?php echo $support?></option>
		<?php } ?>
		</select>
		</td></tr>

<tr>
<td class="key"><label for="mm_email">Their email address</label></td>
<td><input class="inputbox" name="mm_email" id="mm_email" value="" size="50" type="text" /></td></tr>

<tr><td class="key" valign="top">

Message to my buddy
</td>
<td>
Hello! I've just joined a project through my health care provider that's called Healthy Directions. Healthy Directions is going to help me make healthy changes, and I'm really excited about it. 
<br><br>
A big part of Healthy Directions is asking for help. That's because the support of friends and family can help make health changes easier! I think you'd be a great Healthy Directions Buddy. You don't need to do a lot - just be there for me and encourage me to meet my health goals! 
<br><br>
You can learn more at <a href="http://Buddies.TrackMyChanges.org">Buddies.TrackMyChanges.org</a>, the web site that's for buddies of Healthy Directions participants. You can also get in touch with me soon, and we can talk more about it. 
</td></tr>
<tr>
<td class="key" valign="top"><label for="mm_message">
My message (optional)</label></td>
<td id="mm_pane"><textarea rows="10" cols="50" name="mm_message" id="mm_message" class="inputbox"></textarea></td></tr></tbody></table><!--a href="" title="" class="readon" >Send email</a-->
<input class="hd_button" type="submit" value="Send email"></form>
</div>
	
		<?php	
	}
	
	function sendEmail ($sendemail_msg) {
	
	?>
	<p class="contentheading">Email my friend or family member</p>
  <h4><?php echo $sendemail_msg ?></h4>
  <p>
<a href="index.php?option=com_content&view=article&id=41&Itemid=50" class="hd_button">Invite another buddy</a>
  
  </p>
	
	<?php
	}	// end function
} // end class
?>
