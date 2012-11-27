<?php
/**
* Jom Comment 
* @version 1.0
* @package JomComment
* @copyright (C) 2006 by Azrul Rahim - All rights reserved!
* @license Copyrighted Commercial Software
**/

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');



function jcAdminUnicode_to_entities( $unicode ) {
        
        $entities = '';
        foreach( $unicode as $value ){
            if($value >= 128)
                $entities .= '&#' . $value . ';';
            else
                $entities .= chr($value);
        }
        return $entities;
        
    } // unicode_to_entities
    
function jcAdminUtf8_to_unicode( $str ) {
    $temp = jcUtf8_to_unicode($source);
	$result = jcUnicode_to_entities($temp);
	return $result;
} // utf8_to_unicode
    

class HTML_comment 
{

  function showAbout() {
  	$cms    =& cmsInstance('CMSCore');
  	
  	require_once( $cms->get_path('root') . '/includes/domit/xml_domit_lite_include.php' );
  	
	// Read the file to see if it's a valid component XML file
	$xmlDoc = new DOMIT_Lite_Document();
	$xmlDoc->resolveErrors( true );

	if (!$xmlDoc->loadXML( $cms->get_path('root') . "/administrator/components/com_jomcomment/jomcomment.xml", false, true )) {
		//continue;
	}

	$root = &$xmlDoc->documentElement;

	if ($root->getTagName() != 'mosinstall') {
		//continue;
	}
	if ($root->getAttribute( "type" ) != "component") {
		//continue;
	}

	$element 			= &$root->getElementsByPath('creationDate', 1);
	$row->creationdate 	= $element ? $element->getText() : 'Unknown';

	$element 			= &$root->getElementsByPath('author', 1);
	$row->author 		= $element ? $element->getText() : 'Unknown';

	$element 			= &$root->getElementsByPath('copyright', 1);
	$row->copyright 	= $element ? $element->getText() : '';

	$element 			= &$root->getElementsByPath('authorEmail', 1);
	$row->authorEmail 	= $element ? $element->getText() : '';

	$element 			= &$root->getElementsByPath('authorUrl', 1);
	$row->authorUrl 	= $element ? $element->getText() : '';

	$element 			= &$root->getElementsByPath('version', 1);
	$row->version 		= $element ? $element->getText() : '';

	$row->mosname 		= @strtolower( str_replace( " ", "_", $row->name ) );
			
  ?>
      <table cellpadding="4" cellspacing="0" border="0" width="100%">
      <tr>
        <td width="100%">
          <img src="components/com_jomcomment/logo.png">
        </td>
      </tr>
      <tr>
        <td>
        <blockquote>
          <p><br />
              Jom Comment is created by The Team at www.azrul.com comprising of Azrul (lead developer) and Meriza Anna (marketing and others).</p>
          <p>Please visit <a href="http://www.azrul.com">www.azrul.com </a>to find out more about us. </p>
          <p>&nbsp;</p>
        </blockquote>
        </td>
      </tr>
      <tr>
      	<td><b>Release Date:</b>&nbsp;&nbsp;<?php echo $row->creationdate;?></td>
      </tr>
      <tr>
      	<td><b>Version:</b>&nbsp;&nbsp;<?php echo $row->version;?></td>
      </tr>
      </table>
  <?php
    }
    
    function showSupport() {
  ?>
      <table cellpadding="4" cellspacing="0" border="0" width="860px">
      <tr>
        <td width="100%">
          <img src="components/com_jomcomment/logo.png">
        </td>
      </tr>
      <tr>
        <td>

<blockquote>
    <h2>SUPPORT</h2>
    <table>
      <tbody>
          <tr>
              <td colspan="2" valign="top"><div align="left">
                          <h3><a href="http://wiki.azrul.com/" target="_blank">Product Wiki</a> </h3>
              </div>
                  <p align="left">Here&rsquo;s where you can find all product  documentation's, frequently asked questions (FAQ&rsquo;s) and other product  details. This is the best place for quick information on anything  related to our products.</p>
                  <div align="left">
                      <h3>E-mail</h3>
                  </div>
                  <div align="left">
                          <p>This is the best way to contact us and obtain support. You can email us at
                              <!--   var prefix = '&#109;a' + 'i&#108;' + '&#116;o';   var path = 'hr' + 'ef' + '=';   var addy63818 = 's&#117;pp&#111;rt' + '&#64;';   addy63818 = addy63818 + '&#97;zr&#117;l' + '&#46;' + 'c&#111;m';   var addy_text63818 = 's&#117;pp&#111;rt' + '&#64;' + '&#97;zr&#117;l' + '&#46;' + 'c&#111;m';   document.write( '<a ' + path + '\'' + prefix + ':' + addy63818 + '\'>' );   document.write( addy_text63818 );   document.write( '<\/a>' );   //--><a href="mailto:support@azrul.com">support@azrul.com</a>
                                  <!--   document.write( '<span style=\'display: none;\'>' );   //-->
                              This email address is being protected from spam bots, you need Javascript enabled to view it
                              <!--   document.write( '</' );   document.write( 'span>' );   //-->
                              . We will try our best to reply within 1 business day. Please write</p>
                          <ul>
                              <li>descriptive issues in the subject area</li>
                          <li>include the address to your website and as much additional details as possible </li>
                      </ul>
                  </div>
                  <br>
                      <div align="left">
                          <h3><a href="http://forum.azrul.com" target="_blank">Forum</a> </h3>
                    </div>
                  <div align="left">
                          <p>If  you cannot find what you&rsquo;re looking for in the Wiki, you can post your  questions here. The community will do the best to help you. <em><strong>Keep  in mind this forum is community driven and is NOT the best way to  obtain fast response from us. Please write to us at the email above</strong></em>.</p>
                  </div></td>
          </tr>
      </tbody>
  </table>
  <p><strong>Common Issues </strong></p>
  <blockquote>
      <p> <strong>1. Have you enable the Jom Comment Sys Bot ? </strong><br>

            This solve 90% of all "it's not working" problem. Jom Comment uses 2 mambots, a content and a system mambot. Make sure BOTH mambots is enabled <br>
            <br>
            <strong>2. Are you using a custom templates? </strong><br>
            Jom Comment require the folloowing code in the &lt;head&gt; section of your template. </p>
      <p> &lt;?php&nbsp;mosShowHead ();&nbsp; ?&gt; </p>

      <p>        The default template and most commercial template do include this code. Make sure it is there, if it is missing, add it. </p>
      <p>You can read more about it <a href="http://forum.azrul.com/index.php/topic,3.0.html" target="_blank">here</a> </p>
  </blockquote>
  </blockquote>

        </td>
      </tr>
      </table>
  <?php
    }
    
    function showLicense() {
  ?>
      <table cellpadding="4" cellspacing="0" border="0" width="860px">
      <tr>
        <td width="100%">
          <img src="components/com_jomcomment/logo.png">
        </td>
      </tr>
      <tr>
        <td>
        <blockquote>
  <H3>SOFTWARE LICENSE AND LIMITED WARRANTY </H3>
  <p>This is a legally binding agreement between you and <em>Azrul</em>. By   installing and/or using this software, you are agreeing to become bound by the   terms of this agreement.</p>
  <p>If you do not agree to the terms of this agreement, do not use this software.   </p>
  <p><strong>GRANT OF LICENSE</strong>. <em>Azrul</em> grants to you a non-exclusive   right to use this software program (hereinafter the "Software") in accordance   with the terms contained in this Agreement. You may use the Software on a single   computer. If you have purchased a site license, you may use the Software on the   number of websites defined by and in accordance with the site license.</p>
  <p><strong>UPGRADES</strong>. If you acquired this software as an upgrade of a previous   version, this Agreement replaces and supercedes any prior Agreements. You may   not continue to use any prior versions of the Software, and nor may you   distribute prior versions to other parties.</p>
  <p><strong>OWNERSHIP OF SOFTWARE</strong>. <em>Azrul</em> retains the copyright, title,   and ownership of the Software and the written materials.</p>
  <p><strong>COPIES</strong>. You may make as many copies of the software as you wish, as   long as you guarantee that the software can only be used on one website (joomla installation) in any   one instance. You may not distribute copies of the Software or accompanying   written materials to others.</p>
  <p><strong>TRANSFERS</strong>. You may not transfer the Software to another person provided   that you have a written permission from <em>Azrul</em> . You may not   transfer the Software from one website to another.  In no event may you transfer, assign, rent, lease, sell, or   otherwise dispose of the Software on a temporary basis.</p>
  <p><strong>TERMINATION</strong>. This Agreement is effective until terminated. This   Agreement will terminate automatically without notice from <em>Azrul</em> if   you fail to comply with any provision of this Agreement. Upon termination you   shall destroy the written materials and all copies of the Software, including   modified copies, if any.</p>
  <p><strong>DISCLAIMER OF WARRANTY</strong>. <em>Azrul</em> disclaims all other   warranties, express or implied, including, but not limited to, any implied   warranties of merchantability, fitness for a particular purpose and   noninfringement.</p>
  <p><strong>OTHER WARRANTIES EXCLUDED</strong>. <em>Azrul</em> shall not be liable for   any direct, indirect, consequential, exemplary, punitive or incidental damages   arising from any cause even if <em>Azrul</em> has been advised of the   possibility of such damages. Certain jurisdictions do not permit the limitation   or exclusion of incidental damages, so this limitation may not apply to you.</p>
  <p>In no event will <em>Azrul</em> be liable for any amount greater than what   you actually paid for the Software. Should any other warranties be found to   exist, such warranties shall be limited in duration to 15 days following the   date you install the Software.</p>
  <p><strong>EXPORT LAWS</strong>. You agree that you will not export the Software or   documentation.</p>
  <p><strong>PROPERTY</strong>. This software, including its code, documentation,   appearance, structure, and organization is an exclusive product of the <em>Azrul</em>, which retains the property rights to the software, its   copies, modifications, or merged parts.</p>
  <p>&nbsp;</p>
</blockquote>
        </td>
      </tr>
      </table>
  <?php
    }
    
############################################################################
  function showComments( $option, &$rows, &$search, &$pageNav, $searchContent, $searchUser) {
    $commentlenght = "40";

    $db =& cmsInstance('CMSDb');
	$cms    =& cmsInstance('CMSCore');
	$cms->load('helper','url');

	$limitOption    = cmsGetVar('limitOption','com_content', 'REQUEST');

    $db->query("SELECT distinct `option` FROM #__jomcomment");
    $results = $db->get_object_list();
    $limitComOptions = "";
    foreach($results as $res){
    	$optionName	= '';
    	
    	if($res->option == 'com_myblog')
    		$optionName	= 'My Blog';
    	else if($res->option == 'com_content')
    		$optionName	= 'Joomla Content';
    	else if($res->option == 'com_groupjive')
    		$optionName	= 'GroupJive';
    	else if($res->option == 'com_rsgallery2')
    		$optionName	= 'RS Gallery 2';
    	else if($res->option == 'com_comprofiler')
    		$optionName = 'Community builder';
    	else
    		$optionName	= $res->option;
    		
    	if($res->option == $limitOption)
    		$limitComOptions .= "<option value=\"$res->option\" selected>$optionName</option>";
    	else
    		$limitComOptions .= "<option value=\"$res->option\">$optionName</option>";
	}
    # Table header
	$jq     	= JC_ADMIN_LIVEPATH  . '/js';
	$template	= JC_ADMIN_LIVEPATH . '/templates';
	
	$guestImage		= JC_ADMIN_LIVEPATH . '/images/guest.gif';
	$memberImage	= JC_ADMIN_LIVEPATH . '/images/member.gif';
?>
<script src="<?php echo $jq;?>/jquery-1.2.6.pack.js" type="text/javascript"></script>
<script type='text/javascript'>
/*<![CDATA[*/
jQuery.noConflict();
/*]]>*/
</script>
<script src="<?php echo $jq;?>/ui.mouse.js" type="text/javascript"></script>
<script src="<?php echo $jq;?>/jquery.dimensions.js" type="text/javascript"></script>
<script src="<?php echo $jq;?>/ui.draggable.js" type="text/javascript"></script>
<script src="<?php echo $template;?>/edit_comment.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo $template;?>/edit_comment.css" type="text/css" />
<div id="popupWindowContainer" style="visibility:hidden; position:absolute" >
	<div class="dropshadowBox">
		<div class="innerbox">
		<div id="popupWindowHandle"></div>
			<div id="popupWindowEditable" ></div>
		</div>
	</div>
</div>
<form action="index2.php?option=com_jomcomment&task=comments" method="post" name="adminForm"  id="adminForm" >
<table cellpadding="4" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="10%" rowspan="2"><img src="components/com_jomcomment/logo.png"></td>
		<td width="30%" align="right">
			Search User:<input type="text" name="searchUser" value="<?php echo $searchUser;?>" class="inputbox" onChange="document.adminForm.submit();" />
		</td>
		<td>Search Comment:</td>
		<td>
			<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
		</td>
		<td>Search Content:</td>
  		<td>
    		<input type="text" name="searchContent" value="<?php echo $searchContent;?>" class="inputbox" onChange="document.adminForm.submit();" />
		</td>
	</tr>
	<tr>
    	<td colspan="6" align="right" nowrap="nowrap">Display Type: 
<?php
	$showType	= cmsGetVar('cformat','0','REQUEST');
?>
    <select name="cformat" id="format" onchange="document.location='index2.php?option=com_jomcomment&task=comments&cformat=' + jax.$('format').value;">
    	<option value="0" <?php echo ($showType == '0') ? 'selected="true"' : ''; ?>>Unformatted Comments</option>
    	<option value="1" <?php echo ($showType == '1') ? 'selected="true"' : ''; ?>>Formatted Comments</option>
    </select>
    Select Component: 
		<select name="limitOption" id="limitOption" onchange="document.location = 'index2.php?option=com_jomcomment&task=comments&limitOption=' + jax.$('limitOption').value;">
    	<?php echo $limitComOptions; ?>
        </select>
		</td>
	</tr>
	<tr>
		<td colspan="6">
			<span>Legend:</span>&nbsp;
			<span><img src="<?php echo $guestImage;?>"> - Guests</span>&nbsp;|&nbsp;
			<span><img src="<?php echo $memberImage;?>"> - Members</span>
		</td>
	</tr>
</table>

    <table id="mainListingTable" cellpadding="4" cellspacing="0" border="0" width="100%" class="mytable">
    <tbody>
      <tr>
        <th width="2%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
        <!--
		<th class="title">Action</th>
        <th class="title"><div align="center">Author</div></th>
        <th class="title"><div align="center">Email</div></th>
        -->
        
        <th class="title" width="45%"><div align="left">Comment</div></th>
        <!-- <th class="title"><div align="left">Action</div></th>
        <th class="title"><div align="center">Date</div></th> 
        <th class="title"><div align="center">IP</div></th>-->
        <th class="title" width="15%"><div align="center">Content</div></th>
        <th class="title"><div align="center">Published</div></th>
      </tr>
      <?php
    $k = 0;
    $entrylenght = 64;
    $viewObj = new JCView();
    
    // Drawing the rows
    for ($i=0, $n=count( $rows ); $i < $n; $i++) {
        $row = &$rows[$i];

		$query	= '';
		if($option == 'com_eventlist'){
			$query = "SELECT title FROM #__eventlist_events WHERE `id`='" . $row->contentid . "'";
		} else if($option == 'com_comprofiler'){
			$query	= "SELECT name FROM #__users WHERE `id`='{$row->contentid}'";
		} else {
        	$query = "SELECT title FROM #__content WHERE id='$row->contentid' AND state='1' ;";
        }
		$db->query( $query );
		$contentTitle = $db->get_value();
			
// 		$query	= '';
// // 		if($row->option == 'com_eventlist')
// // 			$query = "SELECT title FROM #__eventlist_events WHERE `id`='" . $row->contentid . "'";
// // 		else
// 			$query = "SELECT title FROM #__content WHERE id='" . $row->contentid . " AND state='1 ;";  
// 		
// 		$db->query($query);
// 		$contentTitle = $db->get_value();
		
        echo "<tr class='row$k'>";
        echo "<td width='1%'><input type='checkbox' id='cb$i' name='cid[]' value='$row->id' onclick='isChecked(this.checked);' /></td>";

		$row->comment  = transformDbText($row->comment);
		$row->comment = $viewObj->shortenURL($row->comment);
		if(strlen($row->comment) > 300) {
			$row->comment  = stripslashes(substr($row->comment,0,300-3));
			$row->comment .= "...";
		}
		
		// Determine user's image.
		$userImage		= ($row->user_id != '0' && !empty($row->user_id)) ? $memberImage : $guestImage;
		
		# We must strip tags the comment. This fix the issue where user add redirect meta header
		# and stall the whole system, even the backend!      
		$row->comment 	= strip_tags($row->comment);
?>
      <td>
      	<div style="font-weight:bold;" id="comment-title-<?php echo $row->id;?>"><?php echo $row->title; ?></div>
      	<div class="comment" style="text-align:left;overflow:hidden" id="comment-<?php echo $row->id; ?>">
      		<?php echo ($showType == '0') ? $row->comment : $row->preview; ?>
      	</div>
      	<div style="text-align:left">
      	<strong>INFO: </strong>
      		<strong>Name: </strong><span id="comment-name-<?php echo $row->id; ?>"><?php echo $row->name; ?></span> | 
			<strong>Email: </strong><a href="javascript:void(0);" onclick="jax.call('jomcomment','jcxEmailForm', '<?php echo $row->email;?>');" id="comment-email-<?php echo $row->id; ?>"><?php echo $row->email;?></a></span> | 
			<strong>URL: </strong><span id="comment-website-<?php echo $row->id; ?>"><?php echo $row->website;?></span> |
			<strong>Date:</strong> <span id="date-<?php echo $row->id; ?>"><?php echo $row->date;?></span> |
			<strong>IP: </strong><?php echo $row->ip;?> | 
      	</div>
	    
      	<div class="comment-info">
	      	<!-- <img src="components/com_jomcomment/images/Gear_16x16.png" style="vertical-align: middle;" hspace="2"/> -->
			<span class="tinyaction" onclick="jax.call('jomcomment','jcxEditComment', <?php echo $row->id; ?>);">Edit</span> |
		    <span class="tinyaction" onClick="jax.call('jomcomment','jcxBanUserName','<?php echo $row->name; ?>');">Ban this user</span> |
		    <span class="tinyaction" onClick="jax.call('jomcomment','jcxBanUserIP','<?php echo $row->ip; ?>');">Ban user IP</span> | 
		    <span class="tinyaction" onClick="jax.call('jomcomment','jcxMoveCommentForm','<?php echo $row->id; ?>');">Move Comment</span> | 
		    <img src="<?php echo $userImage;?>">
	    </div>
	</td>
	<td>
	<div>
      		<strong><span id="content-<?php echo $row->id;?>"><?php echo $contentTitle;?></span></strong>
      	</div>
	</td>
      <?php
      //echo "<td align='left' id='website-$row->id'width='10%'>$row->website&nbsp;</td>";
      //echo "<td align='center'>$row->date</td>";
      //echo "<td align='center' id='website-$row->id'width='10%'>$row->ip&nbsp;</td>";
      
      
      //echo "<td align='center' id='content-$row->id'>$contentTitle</td>";
      if(strlen($row->comment) > 64) {
        $row->comment  = substr($row->comment,0,64);
        $row->comment .= "...";
      }

      $task = $row->published ? 'unpublish' : 'publish';
      $img = $row->published ? 'publish_g.png' : 'publish_x.png';
      ?>
        <td width="10%" align="center">
            <a href="javascript: void(0);" onclick="jax.call('jomcomment','jcxTogglePublish', <?php echo $row->id; ?>);">
                <img id="pubImg<?php echo $row->id; ?>" src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" />
            </a>
        </td>
    </tr>
    <?php    
        $k = 1 - $k; 
        // Now we need to add hidden row.
        ?>
        <tr style="display: none;" id="<?php echo $row->id; ?>">
        <td align="center" colspan="10" height='1px'><div stye="display:block;" id="c<?php echo $row->id;?>" ></div></td>
        </tr>
        <?php 
    } 
    ?>
    <tr>
      <th align="center" colspan="10">
      <input type="hidden" value="<?php echo $limitOption; ?>" name="limitOption" id="limitOption">
      <?php
      	echo $pageNav->footer;
	  ?>
		</th>
    </tr>
	</tbody>
  </table>
  <input type="hidden" name="option" value="com_jomcomment" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  </form>
  <?php
  }


  
  function showLanguageEdit($flist){
  ?>
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%" >
    <tbody><tr>
      <td width="10%"><img src="components/com_jomcomment/logo.png"></td>
      <td align="center" width="100%"><div id="ajaxInfo" class="message" align="center">
      </div>
      </td>

      </tr>
    </tbody></table>
  <table width="100%" border="0" cellpadding="16" cellspacing="0">
    <tr>
      <td valign="top">
        <table width="100%"  class="mytable" border="0" cellpadding="0">
            <tr>
      <th valign="top">Select language file to edit </th>
    </tr>
          <tr>
            <td valign="top">
            <p>
                <select name="languageFile" size="12" style="width:200px "id="languageFile">
                  <?php echo $flist; ?>
                </select>
            </p>
              <p>                
              <input type="submit" name="Submit" value="Edit" class="CommonTextButtonSmall" onClick="jax.call('jomcomment','jcxLoadLangFile', document.getElementById('languageFile').value);">
              </p>
              </td>
          </tr>
        </table>
        </td>
      <td width="100%" valign="top">
      <table width="100%"  border="0" class="mytable" cellpadding="0">
      <tr>
      <th valign="top">Editing </th>
    </tr>
          <tr>
            <td><p>
                <textarea name="editLangTextArea" rows="20" id="editLangTextArea" style="width:98%"></textarea>
            </p>
              <p>                <input type="submit" name="Submit" value="Save" class="CommonTextButtonSmall" onClick="jax.call('jomcomment','jcxSaveLanguage', document.getElementById('editLangTextArea').value,document.getElementById('currentFile').value);">
                <input name="currentFile" type="hidden" id="currentFile">
              </p></td>
          </tr>
        </table>
        </td>
    </tr>
  </table>
  <?php
  }
}


class HTML_trackbacks
{
	
	function showTrackbacks( $option, &$rows, &$search, &$pageNav, $searchContent ) {
    	$db =& cmsInstance('CMSDb');
    	$commentlenght = 40;
?>
<form action="index2.php?option=com_jomcomment&task=trackbacks" method="post"  id="adminForm" name="adminForm">
	    <table cellpadding="4" cellspacing="0" border="0" width="100%">
	    <tr>
	      <td width="10%"><img src="components/com_jomcomment/logo.png"></td>
	      <td width="100%"  align="center"><div id="ajaxInfo" class="message" align="center" >
	      </div>
	      </td>
	      <td nowrap="nowrap">Display #</td>
	      <td>
	        <?php echo $pageNav->writeLimitBox(); ?>
	      </td>
	      <td>Search:</td>
	      <td>
	        <input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
	      </td>
	      <td>Search Content:</td>
      <td>
        <input type="text" name="searchContent" value="<?php echo $searchContent;?>" class="inputbox" onChange="document.adminForm.submit();" />
	    </tr>
	    </table>
	
	    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="mytable">
	      <tr>
	        <th width="2%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
	        <th class="title"><div align="center">Title</div></th>
	        <th class="title" width="55%"><div align="left">Excerpt</div></th>
	        <th class="title"><div align="center">Content</div></th>
	        <th class="title"><div align="center">Published</div></th>
	      </tr>
		
<?php
	    $k = 0;
	    $entrylenght = 64;

if($rows){	    
	    // Drawing the rows
	    for ($i=0, $n=count( $rows ); $i < $n; $i++) {
	        $row = &$rows[$i];
	        echo "<tr class='row$k'>";
	        echo "<td width='1%'><input type='checkbox' id='cb$i' name='cid[]' value='$row->id' onclick='isChecked(this.checked);' /></td>"; 
			echo "<td align='center' id='title-$row->id'>&nbsp;$row->title</td>";
			
			if(strlen($row->excerpt) > $commentlenght) {
				$row->excerpt  = transformDbText(stripslashes(substr($row->excerpt,0,$entrylenght-3)));
				$row->excerpt .= "...";
			}
?>
			<td>
				<div class="comment">
					<?= $row->excerpt; ?>
				</div>
				<div>
		      		<!-- <img src="components/com_jomcomment/images/Information_16x16.png" style="vertical-align: middle;" hspace="2"/> -->
					<strong>URL: </strong><?php echo $row->url;?> |
					<strong>Date:</strong> <?php echo $row->date;?> |
					<strong>IP: </strong><?php echo $row->ip;?> | 
		      	</div>
		      	
				<div class="comment-info">
				    <span class="tinyaction" onClick="jax.call('jomcomment','jcxBanUserIP','<?php echo $row->ip; ?>');">Ban user IP</span>
			    </div>
	        </td>
			<?php
			if(strlen($row->url) > 32) {
				$row->url  = substr($row->url,0,32);
				$row->url .= "...";
			}

			$query = "SELECT title FROM #__content WHERE id='$row->contentid' AND state='1' ;";
			$db->query( $query );
			$contentTitle = $db->get_value();
			echo "<td align='center' id='content-$row->id'>$contentTitle</td>";
			
			if(strlen($row->excerpt) > $commentlenght) {
				$row->excerpt  = substr($row->excerpt,0,$commentlenght-3);
				$row->excerpt .= "...";
			}
	
	      $task = $row->published ? 'unpublish' : 'publish';
	      $img = $row->published ? 'publish_g.png' : 'publish_x.png';
	      ?>
	        <td width="10%" align="center">
	            <a href="javascript: void(0);" onclick="jax.call('jomcomment','jcxToggleTrackbackPublish', <?php echo $row->id; ?>);">
	                <img id="pubImg<?php echo $row->id; ?>" src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" />
	            </a>
	            
	        </td>
	    </tr>
<?php    
	        $k = 1 - $k; 
	    }
	} else {
?>
		<tr>
			<td colspan="5">No trackbacks yet.</td>
		</tr>
<?php
	} 
?>
	    <tr>
	      <th align="center" colspan="10">
	        <?php echo $pageNav->writePagesLinks(); ?></th>
	    </tr>
	    <tr>
	      <td align="center" colspan="10">
	        <?php echo $pageNav->writePagesCounter(); ?></td>
	    </tr>
	  </table>
	  <input type="hidden" name="option" value="<?php echo $option;?>" />
	  <input type="hidden" name="task" value="" />
	  <input type="hidden" name="boxchecked" value="0" />
	  </form>
	  
	  

	  <?php
    }
}
