<form action="" method="POST" name="adminForm">
  <table cellpadding="4" cellspacing="0" border="0" width="100%">
  <tr>
    <td width="100%" class="sectionname">
      <img src="components/com_jomcomment/logo.png">
    </td>
  </tr>
  </table>
<?PHP
$jq     	= JC_ADMIN_LIVEPATH .'/js/jquery-1.2.6.pack.js';
$jq_tabs    = JC_ADMIN_LIVEPATH .'/js/jquery.tabs.pack.js';
$jq_css     = JC_ADMIN_LIVEPATH .'/js/jquery.tabs.css';

$cms        =& cmsInstance('CMSCore');

$images		= $cms->get_path('live') . '/administrator/components/com_jomcomment/images';

# Load the setupoptions
$cms->load('libraries','optionsetup')
?>
<link rel="stylesheet" href="<?PHP echo $jq_css;?>" type="text/css" media="print, projection, screen">
<div style="width:100%">
        <script src="<?PHP echo $jq;?>" type="text/javascript"></script>
        <script src="<?PHP echo $jq_tabs;?>" type="text/javascript"></script>
        <script type="text/javascript">
            jQuery.noConflict();
            jQuery(document).ready(function() {
                jQuery('#jcTab').tabs();
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
	<li>
		<a href="#general">
			
			<span><img src="<?php echo $images;?>/general.gif" border="0" />General</span>
		</a>
	</li>
	<li>
		<a href="#spam">
            
			<span><img src="<?php echo $images;?>/spam.gif" border="0" />Anti Spam</span>
		</a>
	</li>
	<li>
		<a href="#extra">
            
			<span><img src="<?php echo $images;?>/extra.gif" border="0" />Extra</span>
		</a>
	</li>
	<li>
		<a href="#layout">
            
			<span><img src="<?php echo $images;?>/layout.gif" border="0" />Layout</span>
		</a>
	</li>
	<li>
		<a href="#permissions">
            
			<span><img src="<?php echo $images;?>/permissions.gif" border="0" />Permissions</span>
		</a>
	</li>
</ul>
<?PHP


$opt = new CMSOptionSetup();

$opt->add_section('Basic Settings');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'enable',
					'value' => $_JC_CONFIG->get('enable'),
					'title' => 'Enable Jom Comment',
					'desc'  => 'Check to Enable Jom Comment on your Joomla site.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'disableFrontPage',
					'value' => $_JC_CONFIG->get('disableFrontPage'),
					'title' => 'Disable Jom Comment on the front page.',
					'desc'  => 'If selected, Jom Comment will not appear on the front page of your site. If your site does not appear correctly, kindly enable this.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'autoPublish',
					'value' => $_JC_CONFIG->get('autoPublish'),
					'title' => 'Auto publish comments',
					'desc'  => 'Check to allow new comments to appear automatically. Uncheck if you want comments to be moderated.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'anonComment',
					'value' => $_JC_CONFIG->get('anonComment'),
					'title' => 'Enable guest commenting',
					'desc'  => 'Allows guest to comment without registering to your site'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'modGuest',
					'value' => $_JC_CONFIG->get('modGuest'),
					'title' => 'Moderate guest\'s comment',
					'desc'  => 'Comments by guest (unregistered visitors) will not be published. Admin will be warned through email to approve the comment.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useRSSFeed',
					'value' => $_JC_CONFIG->get('useRSSFeed'),
					'title' => 'Enable comment feed (RSS)',
					'desc'  => 'A unique RSS feed link will be created and maintained.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'allowSubscription',
					'value' => $_JC_CONFIG->get('allowSubscription'),
					'title' => 'Allow comment subscription via email',
					'desc'  => 'Allow registered user to opt to be notified of a new comment via email'
				)
		);
		
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'defaultSubscription',
					'value' => $_JC_CONFIG->get('defaultSubscription'),
					'title' => 'Enable subscription by default?',
					'desc'  => 'Select the checkbox to set subscription to "Yes" by default. '
				)
		);
		
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'extComSupport',
					'value' => $_JC_CONFIG->get('extComSupport'),
					'title' => 'Enable support for 3rd party component',
					'desc'  => 'If you do not use Jom Comment on any 3rd party component other than My Blog, you can set this to \'No\'. This will stop related JavaSript from being added to the component page. Set this to \'Yes\' if you use any 3rd party component integration feature'
				)
		);

$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'commentPreview',
					'value' => $_JC_CONFIG->get('commentPreview'),
					'title' => 'Enable support for Comment Previews',
					'desc'  => 'Enable support for previewing comments on the front page.'
				)
		);	
		
$opt->add_section('Content tools');

$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'showShareToolbar',
					'value' => $_JC_CONFIG->get('showShareToolbar'),
					'title' => 'Show Content tools',
					'desc'  => 'Show content tools; sharing, hits counter and favorite article system.
								<img src="'.JC_ADMIN_LIVEPATH.'/images/share_img.gif" />'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'allowFav',
					'value' => $_JC_CONFIG->get('allowFav'),
					'title' => 'Enable Favorite content',
					'desc'  => ''
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'showEmailButton',
					'value' => $_JC_CONFIG->get('showEmailButton'),
					'title' => 'Enable "Email this" button',
					'desc'  => ''
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'showShareButton',
					'value' => $_JC_CONFIG->get('showShareButton'),
					'title' => 'Enable Social Bookmarkings',
					'desc'  => ''
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'showHitsStats',
					'value' => $_JC_CONFIG->get('showHitsStats'),
					'title' => 'Enable hit counter',
					'desc'  => ''
				)
		);

$opt->add_section('Smilies And BBCode');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useBBCode',
					'value' => $_JC_CONFIG->get('useBBCode'),
					'title' => 'Enable BB Code',
					'desc'  => 'Enables the display of BB code icons such as smilies, bold, italic, etc..'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useSmilies',
					'value' => $_JC_CONFIG->get('useSmilies'),
					'title' => 'Enable Smilies',
					'desc'  => 'Convert text smilies such as :), :P etc.. to image based smilies'
				)
		);
$opt->add(
			array(
					'type' 	=> 'textarea',
					'name' 	=> 'allowedTags',
					'value' => $_JC_CONFIG->get('allowedTags'),
					'cols'  => 45,
					'rows'  => 4,
					'title' => 'Allowed HTML Tags',
					'desc'  => 'List of allowed HTML tags that users can use. For example, \'&lt;p&gt;&lt;b&gt;&lt;em&gt;\'. There is no need to add the closing tag.'
				)
		);
$opt->add_section('Fields to show');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'moreInfo',
					'value' => $_JC_CONFIG->get('moreInfo'),
					'title' => 'Require Name',
					'desc'  => 'Display field to enter name.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'fieldEmail',
					'value' => $_JC_CONFIG->get('fieldEmail'),
					'title' => 'Require E-Mail',
					'desc'  => 'Display field to enter e-mail. The e-mail entered will need to be a valid e-mail address.'
				)
		);
$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'username',
					'value' 	=> array(
					                        'username'  => 'Username',
					                        'name'  => 'Real Name'
										),
					'selected'  => $_JC_CONFIG->get('username'),
					'size'      => 1,
					'title' 	=> 'Name field to use',
					'desc'  	=> 'Select to use either user\'s real name or their login name. This doesn\'t affect unregistered user.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'fieldWebsite',
					'value' => $_JC_CONFIG->get('fieldWebsite'),
					'title' => 'Enable website field',
					'desc'  => 'Display website field (optional).'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'fieldTitle',
					'value' => $_JC_CONFIG->get('fieldTitle'),
					'title' => 'Enable title field',
					'desc'  => 'Display title field (optional)'
				)
		);

$opt->add_section('Sections');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'staticContent',
					'value' => $_JC_CONFIG->get('staticContent'),
					'title' => 'Comment static content',
					'desc'  => 'Allow visitors to add comment on all static content'
				)
		);
$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'categories[]',
					'value' 	=> $cat_list,
					'selected'  => $sel_cat,
					'size'      => 10,
					'title' 	=> 'Sections',
					'desc'  	=> 'select content that you want comments to appear on'
				)
		);

$opt->add_section('Notifications');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'notifyAdmin',
					'value' => $_JC_CONFIG->get('notifyAdmin'),
					'title' => 'Notify admin on new post',
					'desc'  => 'Send email to the specified address whenever a new comment is posted. If you disable the auto-publish feature, you might want to enable this.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'notifyEmail',
					'value' => $_JC_CONFIG->get('notifyEmail'),
					'size'  => 48,
					'maxlength' => 128,
					'title' => 'Notification E-Mail',
					'desc'  => 'Specify where the notification email should be send. To specify multiple e-mail address, use \'comma\'. E.g: user@email.com,user2@email.com'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'notifyAuthor',
					'value' => $_JC_CONFIG->get('notifyAuthor'),
					'title' => 'Notify author on new post',
					'desc'  => 'Send e-mail to the article\'s author whenever a new comment is posted.'
				)
		);
?>
<div id="general"><?PHP echo $opt->get_html();?></div>
<?PHP
$opt    = null;
////////////////////////////////////////////////////////////////////////////
// Spam tab
////////////////////////////////////////////////////////////////////////////

#$tabs->endTab();
#$tabs->startTab("Spam", "Spam Control -page");

$opt    = new CMSOptionSetup();
$opt->add_section('Akismet Spam Detection');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'remoteSpam',
					'value' => $_JC_CONFIG->get('remoteSpam'),
					'title' => 'Use Akismet spam detection service',
					'desc'  => 'Enable 3rd party spam detection service. Each comment and trackback post will be verified against Akismet spam database. Although spam detection accuracy will be improved significantly, it does take a few seconds to complete. You also need to obtain access key from www.wordpress.com .'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'akismetKey',
					'value' => $_JC_CONFIG->get('akismetKey'),
					'size'  => 25,
					'maxlength' => 128,
					'title' => 'Akismet access key',
					'desc'  => 'Akismet access key. You need an account with www.wordpress.com to obtain an access key.'
				)
		);

$opt->add_section('Captcha Settings');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useCaptcha',
					'value' => $_JC_CONFIG->get('useCaptcha'),
					'title' => 'Enable captcha image security',
					'desc'  => 'Enable captcha-image challenge. Poster needs to type in the displayed character in order to post a new comment'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useCaptchaRegistered',
					'value' => $_JC_CONFIG->get('useCaptchaRegistered'),
					'title' => 'Enable captcha for registered user',
					'desc'  => 'Choosing \'No\' will disable captcha for registered member'
				)
		);
$opt->add_section('Re-Captcha Settings');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useRecaptcha',
					'value' => $_JC_CONFIG->get('useRecaptcha'),
					'title' => 'Enable Re-Captcha image security',
					'desc'  => 'Enable captcha-image challenge. Poster needs to type in the displayed character in order to post a new comment'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'recaptchaPublicKey',
					'value' => $_JC_CONFIG->get('recaptchaPublicKey'),
					'size'  => 60,
					'maxlength' => 128,
					'title' => 'Re-captcha Public key',
					'desc'  => 'Re-Captcha Public access key. You need an account with http://recaptcha.net/ to obtain an access key.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'recaptchaPrivateKey',
					'value' => $_JC_CONFIG->get('recaptchaPrivateKey'),
					'size'  => 60,
					'maxlength' => 128,
					'title' => 'Re-captcha Private key',
					'desc'  => 'Re-Captcha Private access key. You need an account with http://recaptcha.net/ to obtain an access key.'
				)
		);
$opt->add_section('Terms & Conditions');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'showTerms',
					'value' => $_JC_CONFIG->get('showTerms'),
					'title' => 'Enable terms and conditions',
					'desc'  => 'Enable the terms and conditions at comment page.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'textarea',
					'name' 	=> 'termsText',
					'value' => $_JC_CONFIG->get('termsText'),
					'rows'  => 5,
					'cols'  => 50,
					'title' => 'Terms of usage',
					'desc'  => 'Enter your own terms and conditions'
				)
		);
$opt->add_section('Other Spam Settings');

$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'checkMultiPaste',
					'value' => $_JC_CONFIG->get('checkMultiPaste'),
					'title' => 'Block excessive copy-paste comment.',
					'desc'  => 'Detect those CTRL+V happy spammer. It will mark those "This is a test, this is a test, this is a test" style of comment as spam'
				)
		);


$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'checkUserAgent',
					'value' => $_JC_CONFIG->get('checkUserAgent'),
					'title' => 'Make sure all comment has a valid "User-Agent"',
					'desc'  => 'All modern browser will identify itself with the server using a valid 
					"User-Agent". Many spam bot do not have this information and
					we can safely mark comment without a valid "User-Agent" information as spambot'
				)
		);


$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'commentMinLen',
					'value' => $_JC_CONFIG->get('commentMinLen'),
					'size'  => 25,
					'maxlength' => 128,
					'title' => 'Minimum comment length',
					'desc'  => 'The minimum number of character(s) a user can post.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'commentMaxLen',
					'value' => $_JC_CONFIG->get('commentMaxLen'),
					'size'  => 25,
					'maxlength' => 128,
					'title' => 'Maximum comment length',
					'desc'  => 'The maximum number of character(s) a user can post.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'textarea',
					'name' 	=> 'blockUsers',
					'value' => $_JC_CONFIG->get('blockUsers'),
					'cols'  => 45,
					'rows'  => 4,
					'title' => 'Block the username',
					'desc'  => 'If you wish to block a certain user, you can enter his/her username here. Seperated by comma ,'
				)
		);
$opt->add(
			array(
					'type' 	=> 'textarea',
					'name' 	=> 'blockWords',
					'value' => $_JC_CONFIG->get('blockWords'),
					'cols'  => 45,
					'rows'  => 4,
					'title' => 'Blocked words',
					'desc'  => 'If a posting contains any of the specified words, it will automatically be unpublished and e-mail notification will be sent to admin. Seperated by comma ,'
				)
		);
$opt->add(
			array(
					'type' 	=> 'textarea',
					'name' 	=> 'censoredWords',
					'value' => $_JC_CONFIG->get('censoredWords'),
					'cols'  => 45,
					'rows'  => 4,
					'title' => 'Censored words',
					'desc'  => 'All censored words will appear as **** . For example, \'censored\' will appear as c******d.. Seperated by comma ,'
				)
		);
$opt->add(
			array(
					'type' 	=> 'textarea',
					'name' 	=> 'blockDomain',
					'value' => $_JC_CONFIG->get('blockDomain'),
					'cols'  => 45,
					'rows'  => 4,
					'title' => 'IP Blocking',
					'desc'  => 'All comment originated from the given IP will be blocked. <br/>
								IP/Range Format accepted:<br/>
								192.168.1.105 (Single IP)<br/>
								192.168.1.* (IP Range with *)<br/>
								192.168.1.100-110 (IP range with -)<br/>
								192.168.?.* (IP range with ?)<br/>
								Seperated each rule by comma ,'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'linkNofollow',
					'value' => $_JC_CONFIG->get('linkNofollow'),
					'title' => 'Add \'rel=nofollow\' on outgoing links',
					'desc'  => 'By adding Add \'rel=nofollow\' to outgoing links, search engine will ignore the link and will not crawl the link'
				)
		);

$opt->add_section('Flood Control');
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'postInterval',
					'value' => $_JC_CONFIG->get('postInterval'),
					'size'  => 25,
					'maxlength' => 25,
					'title' => 'Interval between post',
					'desc'  => 'Allow a time interval (in seconds) in between postings to deter comment flooding.'
				)
		);

$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'spamMaxLink',
					'value' => $_JC_CONFIG->get('spamMaxLink'),
					'size'  => 25,
					'maxlength' => 25,
					'title' => 'Maximum number of links',
					'desc'  => 'Maximum number of link(s) that can appear in a comment before it is marked as Spam'
				)
		);


?>
<div id="spam"><?PHP echo $opt->get_html();?>
</div>
<?PHP
$opt    = null;

////////////////////////////////////////////////////////////////////////////
// Extra tab
////////////////////////////////////////////////////////////////////////////

$opt    = new CMSOptionSetup();
$opt->add_section('Trackback Settings');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'enableTrackback',
					'value' => $_JC_CONFIG->get('enableTrackback'),
					'title' => 'Enable Trackbacks',
					'desc'  => 'Trackback feature allow other blog or site to notify you that they have either quoted, discuss, refer to or have similar content as	your article. Normally, the more sites ping and link back to you, the more	credible your article will be.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useLinkBack',
					'value' => $_JC_CONFIG->get('useLinkBack'),
					'title' => 'Require a link to your site',
					'desc'  => 'Accept trackback that actually contain a link to your website. This will significantly reduce the amount of trackback Spam.'
				)
		);

$opt->add_section('Comment Locking');
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'lockAfter',
					'value' => $_JC_CONFIG->get('lockAfter'),
					'size'  => 10,
					'maxlength' => 10,
					'title' => 'Disable comment posting after',
					'desc'  => 'If you want, you can set Jom Comment to disable comment input after a specified number of days. This feature would be useful in a situation where a discussion would be irrelevant after a number of days.
                    You can also control comment locking manually by inserting the following tag into the content text itself.<br />
					{jomcomment   lock} : completely lock the comment area
					<br/><img src="'.JC_ADMIN_LIVEPATH.'/images/comment_locked.gif" />'
				)
		);

$opt->add_section('Character Encodings');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'optimiseEncoding',
					'value' => $_JC_CONFIG->get('optimiseEncoding'),
					'title' => 'Optimize output encoding',
					'desc'  => 'Optimize the output encoding. Jom Comment will try to use the most efficient encoding. Uncheck if you are having problem with the output displayed.'
				)
		);
		
$opt->add_section('Voting And Reporting Settings');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'allowvote',
					'value' => $_JC_CONFIG->get('allowvote'),
					'title' => 'Enable voting feature',
					'desc'  => 'Allow user to vote on comment and report a comment to admin'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'unpublishReported',
					'size'  => '3',
					'maxlength'  => '3',
					'value' => $_JC_CONFIG->get('unpublishReported'),
					'title' => 'Auto unpublish comment',
					'desc'  => 'Set value to 0 to disable this feature. Unpublish comment if number of user report received is reached.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'minVoteCount',
					'size'  => '3',
					'maxlength'  => '3',
					'value' => $_JC_CONFIG->get('minVoteCount'),
					'title' => 'Auto minimize low votes comment',
					'desc'  => 'Set value to 0 to disable this feature. Minimizes comment when number of negative votes received.'
				)
		);
		
?>
<div id="extra"><?PHP echo $opt->get_html();?></div>
<?PHP
$opt    = null;

////////////////////////////////////////////////////////////////////////////
// Layout tab
////////////////////////////////////////////////////////////////////////////
$opt    = new CMSOptionSetup();
// $opt->add_section('Threaded');
// $opt->add(
// 			array(
// 					'type' 	=> 'checkbox',
// 					'name' 	=> 'enableThreaded',
// 					'value' => $_JC_CONFIG->get('enableThreaded'),
// 					'title' => 'Enable threaded comments',
// 					'desc'  => 'Enable Threaded comments.'
// 				)
// 		);
$opt->add_section('Templates');

$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'template',
					'value' 	=> $temp_lists,
					'selected'  => $_JC_CONFIG->get('template'),
					'size'      => 1,
					'title' 	=> 'Templates',
					'desc'  	=> 'Choose a template you wish to use for Jom Comment.Only 1 default template is provided in the standard version. The professional version contain 8 different, prefessionally designed templates.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'overrideTemplate',
					'value' => $_JC_CONFIG->get('overrideTemplate'),
					'title' => 'Override Template',
					'desc'  => 'Enable template overriding. Your custom template must resided within com_jomcomment folder of your currectly selected Joomla! template.'
				)
		);
$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'sortBy',
					'value' 	=> array(
					                        '1' => 'Older first',
					                        '0' => 'Newer first'
										),
					'selected'  => $_JC_CONFIG->get('sortBy'),
					'size'      => 2,
					'title' 	=> 'Comment ordering',
					'desc'  	=> 'Choose the ordering of your comment posts.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'cycleStyle',
					'value' => $_JC_CONFIG->get('cycleStyle'),
					'size'  => 50,
					'maxlength' => 50,
					'title' => 'Cycle comments css',
					'desc'  => 'The comments will be alternately styled using the following CSS selector. You can specify as many as you want.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'authorStyle',
					'value' => $_JC_CONFIG->get('authorStyle'),
					'size'  => 50,
					'maxlength' => 50,
					'title' => 'Author comments css',
					'desc'  => 'The comments will be alternately styled using the following CSS selector. You can specify as many as you want.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'nameWrap',
					'value' => $_JC_CONFIG->get('nameWrap'),
					'size'  => 16,
					'maxlength' => 16,
					'title' => 'Author\'s name wrap',
					'desc'  => 'If the name is too long, wrap it. Leave it empty to turn off wrapping.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'textWrap',
					'value' => $_JC_CONFIG->get('textWrap'),
					'size'  => 16,
					'maxlength' => 16,
					'title' => 'Wrap long word',
					'desc'  => 'If a word is too long, wrap it. Leave it empty to turn off text wrapping.'
				)
		);
$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'paging',
					'value' 	=> array(
					                        '0' 	=> 'No Paging',
					                        '10'    => '10',
					                        '20'    => '20',
											'30'    => '30',
											'50'    => '50',
											'100'   => '100'
										),
					'selected'  => $_JC_CONFIG->get('paging'),
					'size'      => 1,
					'title' 	=> 'Paging (Pagination)',
					'desc'  	=> '&nbsp;'
				)
		);
$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'gravatar',
					'value' 	=> array(
					                        'none' 			=> 'No Avatar',
					                        'gravatar'		=> 'Gravatar',
					                        'cb'    		=> 'Community Builder',
											'smf'    		=> 'SMF Forum Profile',
											'fireboard'		=> 'Fireboard'
										),
					'selected'  => $_JC_CONFIG->get('gravatar'),
					'size'      => 5,
					'title' 	=> 'Use Avatar',
					'desc'  	=> 'Select which avatar to display. If you do not want to use avatar, select &quot;none&quot;, otherwise you can select <a href="http://www.gravatar.com" target="_blank">Gravatar</a>.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'smfPath',
					'value' => $_JC_CONFIG->get('smfPath'),
					'size'  => 50,
					'maxlength' => 50,
					'title' => 'Path to SMF forum (if required)',
					'desc'  => 'Full path to your SMF forum folder.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'smfWrapped',
					'value' => $_JC_CONFIG->get('smfWrapped'),
					'title' => 'Wrap SMF Profile page',
					'desc'  => 'Wrap smf profile page.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'gWidth',
					'value' => $_JC_CONFIG->get('gWidth'),
					'title' => 'Avatar Width',
					'desc'  => 'Width of avatar to be displayed (in pixel).'
				)
		);
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'gHeight',
					'value' => $_JC_CONFIG->get('gHeight'),
					'title' => 'Avatar Height',
					'desc'  => 'Height of avatar to be displayed (in pixel).'
				)
		);
$opt->add_section('Startup Options');

$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'slideComment',
					'value' => $_JC_CONFIG->get('slideComment'),
					'title' => 'Enable comment area to be hidden/shown',
					'desc'  => 'This feature will only work properly if your template did not use "quirk mode". View your page source file and make sure that &lt;!DOCTYPE appear on the first line. If not, do not enable this feature as it will fail in Internet Explorer although it works well with FireFox.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'startAreaHidden',
					'value' => $_JC_CONFIG->get('startAreaHidden'),
					'title' => 'Start with comments area hidden',
					'desc'  => 'Start with comments area hidden.'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'slideForm',
					'value' => $_JC_CONFIG->get('slideForm'),
					'title' => 'Enable input area to be hidden/shown',
					'desc'  => 'This feature will only work properly if your template did not use "quirk mode". View your page source file and make sure that &lt;!DOCTYPE appear on the first line. If not, do not enable this feature as it will fails in Internet Explorer although it works well with FireFox'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'startFormHidden',
					'value' => $_JC_CONFIG->get('startFormHidden'),
					'title' => 'Start page with comment form hidden',
					'desc'  => '&nbsp;'
				)
		);

$opt->add_section('Display Format');
$opt->add(
			array(
					'type' 	=> 'text',
					'name' 	=> 'dateFormat',
					'value' => $_JC_CONFIG->get('dateFormat'),
					'size'  => 48,
					'maxlength' => 48,
					'title' => 'Date format',
					'desc'  => 'You can use PHP formatting strings. Please refer to PHP documentation <a target="_blank" href="http://de.php.net/strftime">here</a>. Leave it empty to use default formatting.'
				)
		);
$opt->add_section('Read More Integrations');
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useReadMore',
					'value' => $_JC_CONFIG->get('useReadMore'),
					'title' => 'Use Jom Comment\'s "Read more"',
					'desc'  => 'Do you want to use Jom Comment\'s "Read more" tag instead of Joomla\'s default "read more" link.'
				)
		);
		
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'showCommentCount',
					'value' => $_JC_CONFIG->get('showCommentCount'),
					'title' => 'Show comment count in front page',
					'desc'  => 'Do you want to show the number of comments, along with links to the comment area in front page and blog view.
								<br/><img src="'.JC_ADMIN_LIVEPATH.'/images/readmore_count.gif" />'
				)
		);
$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'showHitCount',
					'value' => $_JC_CONFIG->get('showHitCount'),
					'title' => 'Show hit count',
					'desc'  => 'Do you want to show the number of hits for a particular content
								<br/><img src="'.JC_ADMIN_LIVEPATH.'/images/readmore_hits.gif" />'
				)
		);

$opt->add(
			array(
					'type' 	=> 'checkbox',
					'name' 	=> 'useSelectiveReadMore',
					'value' => $_JC_CONFIG->get('useSelectiveReadMore'),
					'title' => 'Only show &quot;read more&quot; if necessary',
					'desc'  => 'If selected, "Read more" will only appear in articles that needs it. Select no to show "Read more" in all articles.'
				)
		);
?>
<div id="layout"><?php echo $opt->get_html();?></div>
<?php
////////////////////////////////////////////////////////////////////////////
// Permissions tab
////////////////////////////////////////////////////////////////////////////

$opt    = new CMSOptionSetup();
$opt->add_section('Permissions');
$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'viewPermissions',
					'value' 	=> array(
					                        '1' => 'All',
					                        '2' => 'Members Only'
										),
					'selected'  => $_JC_CONFIG->get('viewPermissions'),
					'size'      => 1,
					'title' 	=> 'Permissions to view comments',
					'desc'  	=> 'Select the permissions that you would want to allow for viewing of comments'
				)
		);
?>
<div id="permissions"><?php echo $opt->get_html();?></div>
  <input type="hidden" name="option" value="com_jomcomment">
  <input type="hidden" name="task" value="savesettings">
  <input type="hidden" name="boxchecked" value="0">
</form>
</div>
</div>
