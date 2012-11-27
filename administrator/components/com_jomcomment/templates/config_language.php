<form action="" method="POST" name="adminForm">
  <table cellpadding="4" cellspacing="0" border="0" width="100%">
  <tr>
    <td width="100%" class="sectionname">
      <img src="components/com_jomcomment/logo.png">
    </td>
  </tr>
  </table>
<?php
$jq     	= JC_ADMIN_LIVEPATH .'/js/jquery-1.2.6.pack.js';
$jq_tabs    = JC_ADMIN_LIVEPATH .'/js/jquery.tabs.pack.js';
$jq_css     = JC_ADMIN_LIVEPATH .'/js/jquery.tabs.css';

$cms        =& cmsInstance('CMSCore');
# Load the setupoptions
$cms->load('libraries','optionsetup')
?>
<link rel="stylesheet" href="<?PHP echo $jq_css;?>" type="text/css" media="print, projection, screen">
<div style="width:100%">
        <script src="<?php echo $jq;?>" type="text/javascript"></script>
        <script src="<?php echo $jq_tabs;?>" type="text/javascript"></script>
        <script type="text/javascript">
            jQuery.noConflict();
            jQuery(document).ready(function() {
                jQuery('#jcTab').tabs();
				<?php if(!joomfishExists()){ ?>
                jQuery('#jcTab').disableTab(2);
                <?php } ?>
            });
			
			
        </script>
<style type="text/css">

div.cfgdesc{
color:#666666;
padding-top:4px;
}

label.cfgdesc{
color:#000000;
font-weight:bold;
padding-bottom:4px;
}

td.leftalign{
text-align:right; 
vertical-align:top;
}

input.cfgdesc{
margin-top:5px;
vertical-align:top;

}

table.mytable td div input{
	margin-top:10px;
}
</style>
<div id="jcTab">
<ul>
	<li><a href="#general"><span>General</span></a></li>
	<li><a href="#jfish"><span>Joomfish Integrations</span></a></li>
</ul>
<?php

$opt 	= new CMSOptionSetup();
$lang	= $_JC_CONFIG->get('languages');

$opt->add_section('General');
$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'language',
					'value' 	=> $lists,
					'selected'  => $lang['language'],
					'size'      => 1,
					'title' 	=> 'Languages',
					'desc'  	=> 'Select a language for Jom Comment.'
				)
		);
?>
<div id="general"><?php echo $opt->get_html();?></div>
<?php
$opt    = null;
////////////////////////////////////////////////////////////////////////////
// Permissions tab
////////////////////////////////////////////////////////////////////////////

$opt    = new CMSOptionSetup();
$opt->add_section('JoomFish');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'autoLang',
					'value' => $lang['autoLang'],
					'title' => 'Auto Select Language',
					'desc'  => 'Select to force Jom Comment to use preferred system/user language.'
				)
		);
$opt->add_section('JoomFish Shortcode Mapping');
$custom	= '';

foreach($files as $file){
	$key	= array_search(formatLanguage($file), $lang);
	$value	= formatLanguage($file);
	$custom	.= '<br /><input type="text" size="5" class="inputbox" name="file_' . $value . '" value="' . $key . '">';
	$custom .= '&nbsp;&gt;&nbsp;' . $file . '<br />';
}

$opt->add(
			array(
					'type' 	=> 'custom',
					'value' => $custom,
					'name'	=> 'postingFrom',
					'title' => 'Map JoomFish shortcode to Jom Comment\'s language files',
					'desc'  => 'Define the shortcodes mapping.'
				)
		);
?>
<div id="jfish"><?php echo $opt->get_html();?></div>
  <input type="hidden" name="option" value="com_jomcomment">
  <input type="hidden" name="task" value="savelanguagesettings">
  <input type="hidden" name="boxchecked" value="0">
</form>
</div>
</div>
