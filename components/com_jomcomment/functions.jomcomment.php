<?php
/**
 * Jom Comment 
 * @package JomComment
 * @copyright (C) 2006 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/
 
# Don't allow direct linking
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

/** String utilities **/

/**
 * A more comprehansive nl2br replacement. Note that we need to put a space 
 * before the <br/> 
 */
function jcNl2BrStrict($text, $replac=" <br />") {
	return preg_replace("/\r\n|\n|\r/", $replac, $text);
}


// str_ireplace replacement for php4 compatibility
function jcStrIReplace($search,$replace,$subject) {
	if ( function_exists('str_ireplace') ) {
		return str_ireplace($search,$replace,$subject);		// php 5 only
	}
	$srchlen = strlen($search);    // lenght of searched string
	$result  = "";

	if(!empty($search)){
		while ($find = stristr($subject,$search)) {				// find $search text in $subject - case insensitiv
			$srchtxt = substr($find,0,$srchlen);    			// get new case-sensitively-correct search text
			$pos	 = strpos( $subject, $srchtxt );			// stripos is php5 only...
			$result	 .= substr( $subject, 0, $pos ) . $replace;	// replace found case insensitive search text with $replace
			$subject = substr( $subject, $pos + $srchlen );
		}
	}
	return $result . $subject;
}

function jcStrIPos( $search , $replace )
{
	// PHP5 has stripos, just use it
	if( function_exists('stripos') ){
		return stripos( $search , $replace );
	}

	// Convert them to lower case and do the search
	return strpos($search, stristr( $search, $replace ));
}

function jcValidEmail($email){
	return(preg_match( '/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email ));
}

/**
 * jcCommentGetNum()
 * - Compatibility issues
 **/ 
function jcCommentGetNum($articleId, $option = 'com_content'){
	global $mainframe;
	
	if(!function_exists('jcCountComment'))
		include_once($mainframe->getCfg('absolute_path') . '/components/com_jomcomment/helper/minimal.helper.php');
	
	return jcCountComment($articleId, $option);
}

// Transforme the utf-8 text into htmlentities compatible string
function jcTransformDbText ($source) {
	// if default ISO is already utf-8, just return the string
	if(defined('_ISO')){
		if(strpos(_ISO, 'UTF-8')){
			return $str;
		}
	}
	 
    // array used to figure what number to decrement from character order value
    // according to number of characters used to map unicode to ascii by utf-8
    $decrement[4] = 240;
    $decrement[3] = 224;
    $decrement[2] = 192;
    $decrement[1] = 0;
    
    // the number of bits to shift each charNum by
    $shift[1][0] = 0;
    $shift[2][0] = 6;
    $shift[2][1] = 0;
    $shift[3][0] = 12;
    $shift[3][1] = 6;
    $shift[3][2] = 0;
    $shift[4][0] = 18;
    $shift[4][1] = 12;
    $shift[4][2] = 6;
    $shift[4][3] = 0;
    
    $pos = 0;
    $len = strlen ($source);
    $encodedString = '';
    while ($pos < $len) {
        $asciiPos = ord (substr ($source, $pos, 1));
        
        // we must skip standard ascii cahracter from being unicode encoded!
        if($asciiPos > 31 && $asciiPos <= 127){
            $encodedString .= substr ($source, $pos, 1);
            $pos++;
        } 
        else 
        {
        
       
       if (($asciiPos >= 240) && ($asciiPos <= 255)) {
           // 4 chars representing one unicode character
           $thisLetter = substr ($source, $pos, 4);
           $pos += 4;
       }
       else if (($asciiPos >= 224) && ($asciiPos <= 239)) {
           // 3 chars representing one unicode character
           $thisLetter = substr ($source, $pos, 3);
           $pos += 3;
       }
       else if (($asciiPos >= 192) && ($asciiPos <= 223)) {
           // 2 chars representing one unicode character
           $thisLetter = substr ($source, $pos, 2);
           $pos += 2;
       }
       else {
           // 1 char (lower ascii)
           $thisLetter = substr ($source, $pos, 1);
           $pos += 1;
       }
    
       // process the string representing the letter to a unicode entity
       $thisLen = strlen ($thisLetter);
       $thisPos = 0;
       $decimalCode = 0;
       while ($thisPos < $thisLen) {
           $thisCharOrd = ord (substr ($thisLetter, $thisPos, 1));
           if ($thisPos == 0) {
               $charNum = intval ($thisCharOrd - $decrement[$thisLen]);
               $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
           }
           else {
               $charNum = intval ($thisCharOrd - 128);
               $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
           }
    
           $thisPos++;
       }
    
       if ($thisLen == 1)
           $encodedLetter = "&#". str_pad($decimalCode, 3, "0", STR_PAD_LEFT) . ';';
       else
           $encodedLetter = "&#". str_pad($decimalCode, 5, "0", STR_PAD_LEFT) . ';';
    
       $encodedString .= $encodedLetter;      
       
       }
    }
    return $encodedString;
}

/**
 * trim the given variable
 */
function jcTrim(& $val) {
	$val = trim($val);
	return $val;
}

// Wrap the given text
function jcTextwrap($text, $width = 75) {
	if ($text)
		return preg_replace("/([^\n\r ?&\.\/<>\"\\-]{" . $width . "})/i", " \\1\n", $text);
}


/** Comments **/

/**
 * Return valid Itemid for my blog links
 * If 'option' are currently on myblog, then use current ItemId 
 */  
function jcGetMyBlogItemid(){
	static $jcItemid = -1;
	
	if($jcItemid == -1){
		
		global $Itemid;
		$db = &cmsInstance('CMSDb');
		$jcItemid = $Itemid;
		$db->query("select id from #__menu where link LIKE '%option=com_myblog%' and published='1' AND `id`='$jcItemid'");
		if(!$db->get_value()){
			// The current itemId is not myblog related, ignore it, and find a valid one
			// Menu type is 'component' for Joomla 1.0 and 'components' for Jooma 1.5
			$db->query("select id from #__menu where (type='component' or type='components') and link='index.php?option=com_myblog' and published='1'");
			$jcItemid = $db->get_value();
		}
	}

	return $jcItemid;
}


# Post request to remote server
function jcHttpPost($host, $query, $others = '') {
	if(function_exists('curl_init')){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, "http://" .$host . "?". $query);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		ob_start();
		curl_exec ($ch);
		curl_close ($ch);
		$string = ob_get_contents();
		ob_end_clean();
		return $string;
	}

//				
// 	if(ini_get('allow_url_fopen') == 1){
// 		$dh = @fopen("http://". $host . "?". $query,'r');
// 		if($dh === FALSE){
// 			# fopen failed, Do nothing
// 		} else {
// 			$result = fread($dh,8192);                                                                                                                   
// 			return $result;
// 		}
// 	}
	
	$path = explode('/', $host);
	$host = $path[0];
	$r = "";
	unset ($path[0]);
	$path = '/' . (implode('/', $path));
	$post = "POST $path HTTP/1.0\r\nHost: $host\r\nContent-type: application/x-www-form-urlencoded\r\n${others}User-Agent: Mozilla 4.0\r\nContent-length: " . strlen($query) . "\r\nConnection: close\r\n\r\n$query";
	$h = @fsockopen($host, 80, $errno, $errstr, 7);
	if ($h) {
		fwrite($h, $post);
		for ($a = 0, $r = ''; !$a;) {
			$b = fread($h, 8192);
			$r .= $b;
			$a = (($b == '') ? 1 : 0);
		}
		fclose($h);
	}
	return $r;
}


/**
 * Basically, we need to add the HTTPS support if required.
 */ 
function jcFixLiveSiteUrl($content){

	//global
	$cms =& cmsInstance('CMSCore'); 
	$live_site = $cms->get_path('live');
	
	$reqURI   = $live_site;

	# if host have wwww, but mosConfig doesn't
	if((substr_count($_SERVER['HTTP_HOST'], "www.") != 0) && (substr_count($reqURI, "www.") == 0)) {
		$reqURI = str_replace("://", "://www.", $reqURI);
			
	} else if((substr_count($_SERVER['HTTP_HOST'], "www.") == 0) && (substr_count($reqURI, "www.") != 0)) {
		// host do not have 'www' but mosConfig does
		$reqURI = str_replace("www.", "", $reqURI);
	}

	/* Check for HTTPS */
	if(isset($_SERVER)){
		if(isset($_SERVER['HTTPS'])){
			if(strtolower($_SERVER['HTTPS']) == "on" ){
				$reqURI = str_replace("http://", "https://", $reqURI);
			}
		}
	} else if(isset($_SERVER['REQUEST_URI'])) {
		// use REQUEST_URI method
		if(strpos($_SERVER['REQUEST_URI'], 'https://') === FALSE){
			// not a https
		} else {
			$reqURI = str_replace("http://", "https://", $reqURI);
		}
	}

	return str_replace($live_site, $reqURI, $content);

}

function jcGetTemplatePath($file){
	global $_JC_CONFIG;
	
	$path		= JC_COM_PATH . '/templates/_default/' . $file;
	
	
	// Template overriding.
	if($_JC_CONFIG->get('overrideTemplate')){
		$customPath			= JC_CUSTOM_TPL . '/' . $file;

		if(file_exists($customPath))
			$path	= $customPath;
		
	} else {
		$template	= JC_COM_PATH . '/templates/' . $_JC_CONFIG->get('template');
		
		if(file_exists($template . '/' . $file))
			$path	= $template . '/' . $file;
	}
	return $path;
}

function jcCreatePagingLink($baseUrl, $total, $limit, $limitStart){
	$html = array();
	return $html;
}


function jcIsValidtf8($Str) {	
		/*
		if(function_exists('mb_detect_encoding')){
			$enc = mb_detect_encoding($Str, "auto");		
			$iso = explode( '=', _ISO );
	
			return ($enc == $iso);
		} else
		*/ {
		for ($i = 0; $i < strlen($Str) / 5; $i++) {
			if (ord($Str[$i]) < 0x80)
				continue; # 0bbbbbbb
			elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n = 1; # 110bbbbb
			elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n = 2; # 1110bbbb
			elseif ((ord($Str[$i]) & 0xF8) == 0xF0) $n = 3; # 11110bbb
			elseif ((ord($Str[$i]) & 0xFC) == 0xF8) $n = 4; # 111110bb
			elseif ((ord($Str[$i]) & 0xFE) == 0xFC) $n = 5; # 1111110b
			else
				return false; # Does not match any model
			for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++ $i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;
	}
}

/**
 * Decode the BBCode
 */
function jcDecodeSmilies($comment) {
	$cms =& cmsInstance('CMSCore'); 
	$live_site = $cms->get_path('live');
	
	$img_path = $live_site;
	
	# fix smilies
	$smilies = array (
		":)" => "smilies/smiley.gif",
		";)" => "smilies/wink.gif",
		":D" => "smilies/cheesy.gif",
		";D" => "smilies/grin.gif",
		">:(" => "smilies/angry.gif",
		":(" => "smilies/sad.gif",
		":o" => "smilies/shocked.gif",
		"8)" => "smilies/cool.gif",
		"::)" => "smilies/rolleyes.gif",
		":P" => "smilies/tongue.gif",
		":-[" => "smilies/embarassed.gif",
		":-X" => "smilies/lipsrsealed.gif",
		":-*" => "smilies/kiss.gif",
		":'(" => "smilies/cry.gif"
	);
	
	$smiliesAlt  = array (
			":)"     => 'smile',
			";)"     => 'wink',
			":D"     => 'laugh',
			";D"     => 'grin',
			">:("    => 'angry',
			":("     => 'sad',
			":o"     => 'shocked',
			"8)"     => 'cool',
			"::)" 	 => "rolleyes",
			":P"     => 'tongue',
			":-[" 	 => "embarassed",
			":-X" 	 => "lips sealed",
			":-*"    => 'kiss',
			":'("   => 'cry'
		);

	foreach ($smilies as $key => $value) {
		$comment = str_replace($key, "<img src='$img_path/"."components/com_jomcomment/" . $value . "' title='{$smiliesAlt[$key]}' border='0' alt='$value' />", $comment);
	}
	
	return $comment;
}

/**
 * Return the content author's id
 */
function jcGetContentAuthor($cid) {
	$db = &cmsInstance('CMSDb');
	$db->query("SELECT created_by from #__content WHERE id=$cid");
	return $db->get_value();
}

function jcGetAuthorName($uid) {
	$db = &cmsInstance('CMSDb');
	$db->query("SELECT name from #__users WHERE id={$uid}");
	return $db->get_value();
}

function jcContentTitle($cid) {
	$db = &cmsInstance('CMSDb');
	$db->query("SELECT title from #__content WHERE id=$cid");
	$title = $db->get_value();
	if(!$title)
		$title = "n/a";
		
	return $title;
}

function jcContentPublished($cid){
	$db = &cmsInstance('CMSDb');
	$db->query("SELECT state from #__content WHERE id=$cid");
	return $db->get_value();
}


/**
 * $ip : ip to test
 * $ips: array of test condition 
 */ 
function jcCheckBlockedIp($ip, $ips) {
	# read in the ip address file
	$lines = $ips; //file("ipaddresses.txt");
	
	# set a variable as false
	$found = false;
	
	# convert ip address into a number
	$split_it = split("\.",$ip);
	$ip = "1" . sprintf("%03d",$split_it[0]) .
	sprintf("%03d",$split_it[1]) . sprintf("%03d",$split_it[2]) .
	sprintf("%03d",$split_it[3]);
	
	# loop through the ip address file
	foreach ($lines as $line) {
		# remove line feeds from the line
		$line = chop($line);
		# replace x with a *
		$line = str_replace("x","*",$line);
		# remove comments
		$line = preg_replace("|[A-Za-z#/]|","",$line);
		# set a maximum and minimum value
		$max = $line;
		$min = $line;
		# replace * with a 3 digit number
		if ( strpos($line,"*",0) <> "" ) {
			$max = str_replace("*","999",$line);
			$min = str_replace("*","000",$line);
		}
		# replace ? with a single digit
		if ( strpos($line,"?",0) <> "" ) {
			$max = str_replace("?","9",$line);
			$min = str_replace("?","0",$line);
		}
		# if the line is invalid go to the next line
		if ( $max == "" ) { continue; };
		# check for a range
		if ( strpos($max," - ",0) <> "" ) {
			$split_it = split(" - ",$max);
			# if the second part does not match an ip address
			if ( !preg_match("|\d{1,3}\.|",$split_it[1]) ) {
				$max = $split_it[0];
			}
			else { 
				$max = $split_it[1];
			};
		}
		if ( strpos($min," - ",0) <> "" ) {
			$split_it = split(" - ",$min);
			$min = $split_it[0];
		}
		# make $max into a number
		$split_it = split("\.",$max);
		for ( $i=0;$i<4;$i++ ) {
			if ( $i == 0 ) { $max = 1; };
			if ( strpos($split_it[$i],"-",0) <> "" ) {
				$another_split = split("-",$split_it[$i]);
				$split_it[$i] = $another_split[1];
			} 
		$max .= sprintf("%03d",$split_it[$i]);
		}
		# make $min into a number
		$split_it = split("\.",$min);
		for ( $i=0;$i<4;$i++ ) {
			if ( $i == 0 ) { $min = 1; };
			if ( strpos($split_it[$i],"-",0) <> "" ) {
				$another_split = split("-",$split_it[$i]);
				$split_it[$i] = $another_split[0];
			} 
		$min .= sprintf("%03d",$split_it[$i]);
		}
		# check for a match
		if ( ($ip <= $max) && ($ip >= $min) ) {
			$found = true;
			break;
		};
	}
	return $found;
}; # end function


function jcClearCache(){
	global $mainframe;

	$cms    =& cmsInstance('CMSCore');
	$cms->load('helper','directory');
	$cms->load('libraries', 'cache');
	$cms->cache->clear();

	$list   = cmsGetFiles(JC_CACHE_PATH, '');

	foreach($list as $file){
	    // Only remove files that contains the naming convention for cache_
	    if(strstr($file, 'cache_')){
	        @unlink(JC_CACHE_PATH . '/' . $file);
		}
	}
	
	// For Joomla 1.0 we would still need to clear the cached files
	// located in $mosConfig_cachepath
	if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
	    $list   = cmsGetFiles($mainframe->getCfg('cachepath'), '');
	    foreach($list as $file){
	        if(strstr($file, 'cache_'))
	            @unlink($mainframe->getCfg('cachepath') . '/' . $file);
		}
	}
	
	// For Joomla 1.5 we would also need to clear the cached files for JCache.
	if(cmsVersion() == _CMS_JOOMLA15){
		if($mainframe->getCfg('caching')){
			$cache =& JFactory::getCache();
			
			// Clear com_content's cache
			$cache->clean('com_content');
		}
	}
}

function jcGetCommentHtml($id, $extoption, $row){
	global $_JOMCOMMENT;
	return $_JOMCOMMENT->getHTML($id, $extoption, $row);
}

/**
* Similar to mosCreateMail, except that we need to change the email
* encoding to utf-8
*/
function jomCreateMail($from = '', $fromname = '', $subject, $body) {
	$cms =& cmsInstance('CMSCore');

	if(cmsVersion() == _CMS_JOOMLA15){
		$cms->load('libraries','cfg');
		jimport('joomla.mail.mail');

		$mail		=& JFactory::getMailer();
		$fromname	= $fromname ? $fromname : $cms->cfg->get('fromname');
		$from		= $from ? $from : $cms->cfg->get('mailfrom');

		$mail->setSender(array($from,$fromname));
		$mail->setSubject($subject);
		$mail->setBody($body);

	}else if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
		$mail = new mosPHPMailer();
		global $mainframe;

		$mail->PluginDir = $cms->get_path('root') . '/includes/phpmailer/';
		$mail->SetLanguage('en', $cms->get_path('root') . '/includes/phpmailer/language/');
		$mail->CharSet = 'UTF-8'; //substr_replace(_ISO, '', 0, 8);
		$mail->IsMail();
		$mail->From = $from ? $from : $mainframe->getCfg('mailfrom');
		$mail->FromName = $fromname ? $fromname : $mainframe->getCfg('fromname');
		$mail->Mailer = $mainframe->getCfg('mailer');

		// Add smtp values if needed
		if ($mainframe->getCfg('mailer') == 'smtp') {
			$mail->IsSMTP();
			$mail->SMTPAuth = $mainframe->getCfg('smtpauth');
			$mail->Username = $mainframe->getCfg('smtpuser');
			$mail->Password = $mainframe->getCfg('smtppass');

			$smtphost = $mainframe->getCfg('smtphost');
			$hostIp = split(":", $smtphost);
			if(count($hostIp)> 1){
				$mail->Host = $hostIp[0];
				$mail->Port = $hostIp[1];
			} else {
				$mail->Host = $mainframe->getCfg('smtphost');
			}
			
		} else{
			// Set sendmail path
			if ($mainframe->getCfg('mailer') == 'sendmail') {
				$sendmail = $mainframe->getCfg('sendmail');
				if (isset ($sendmail))
					$mail->Sendmail = $sendmail;
				} // if
		}
		$mail->Subject = $subject;
		$mail->Body = $body;
	}

	return $mail;
}

/**
 * Sending out email. Very similar to mosMail, except that we uses jomCreateMail
 * instead of mosCreateMail to allow us to change the encoding. 
 */
function jomMail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = NULL, $bcc = NULL, $attachment = NULL, $replyto = NULL, $replytoname = NULL) {

	$body = stripslashes($body);
	$mail = jomCreateMail($from, $fromname, $subject, $body);

	// activate HTML formatted emails
	if ($mode) {
		$mail->IsHTML(true);
	}

	// Check if mail address is valid.
	$regexp	= "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";
	
	if (is_array($recipient)) {
		foreach ($recipient as $to) {
			if(!empty($to) && eregi($regexp, $to)){
				$mail->AddAddress($to);
			}
		}
	} else {
		if(!empty($recipient) && eregi($regexp, $recipient)){
			$mail->AddAddress($recipient);
		}
	}
	if (isset ($cc)) {
		if (is_array($cc)) {
			foreach ($cc as $to) {
				$mail->AddCC($to);
			}
		} else {
			$mail->AddCC($cc);
		}
	}
	if (isset ($bcc)) {
		if (is_array($bcc)) {
			foreach ($bcc as $to) {
				$mail->AddBCC($to);
			}
		} else {
			$mail->AddBCC($bcc);
		}
	}
	if ($attachment) {
		if (is_array($attachment)) {
			foreach ($attachment as $fname) {
				$mail->AddAttachment($fname);
			}
		} else {
			$mail->AddAttachment($attachment);
		}
	}
	
	if(cmsVersion() == _CMS_JOOMLA15){
		if($replyto){
			$mail->addReplyTo(array($replyto, $replytoname));
		}
	} else {
		//Important for being able to use mosMail without spoofing...
		if ($replyto) {
			if (is_array($replyto)) {
				reset($replytoname);
				foreach ($replyto as $to) {
					$toname = ((list ($key, $value) = each($replytoname)) ? $value : '');
					$mail->AddReplyTo($to, $toname);
				}
			} else {
				$mail->AddReplyTo($replyto, $replytoname);
			}
		}
	}

	$mailssend = $mail->Send();

	return $mailssend;
}


// return ItemID for jom comment link
function jcGetItemID(){
	$db = &cmsInstance('CMSDb');
	static $jcitemid   = '';
	
	if(empty($jcitemid)){
		# autodetect Itemid
		$query = "SELECT id FROM #__menu  WHERE type='components' "
		        ."AND link='index.php?option=com_jomcomment' "
		        ."AND published='1'";

		$db->query($query);

		$jcitemid = $db->get_value();
		if (!$jcitemid){
			if (isset ($_REQUEST['Itemid']) and $_REQUEST['Itemid'] != "" and $_REQUEST['Itemid'] != "0"){
				$jcitemid	= $_REQUEST['Itemid'];
			} else
            	$jcitemid = 1;
		}
	}
	return $jcitemid;
}

// Return the 'Powered by xxx' link
function jcGetPoweredByLink(){
	return '';
	return '<div style="text-align:center;font-size:95%"><a target="_blank" href="http://www.azrul.com">Powered by Azrul&#39;s Jom Comment for Joomla!</a></div>';
}
