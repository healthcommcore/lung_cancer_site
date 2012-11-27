<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file is to perform the execution of displaying user's info in the javascript variables.
 **/
global $_JC_CONFIG;

$cms        =& cmsInstance('CMSCore');
$cms->load('libraries','user');

$nameField  = $_JC_CONFIG->get('username');

$userName   = isset($cms->user->$nameField) ? $cms->user->$nameField : '';
$userEmail  = isset($cms->user->email) ? $cms->user->email : '';
?>
function jc_loadUserInfo(){

	if(jax.$("jc_name")){
		if(jc_username && !(jc_username.match(/^s+$/) || jc_username == "")){
			
			if(jax.$("jc_name")) {jax.$("jc_name").value = jc_username;} 
		}else {
			jax.$("jc_name").value = jc_readCookie('jc_name');}
	}
	
	if(jax.$("jc_email")){
		if(jc_email && !(jc_email.match(/^s+$/) || jc_email == "")){
			jc_email = jc_email.replace(/\+/, "@");
			if(jax.$("jc_email")) {jax.$("jc_email").value = jc_email;} 
		}else {jax.$("jc_email").value = jc_readCookie('jc_email');}
	}
	
	var sid = jcRandomString();
	if(jax.$("jc_website")) {jax.$("jc_website").value = jc_readCookie('jc_website');}
	if(jax.$("jc_sid")){jax.$("jc_sid").value = sid;
		 if(jax.$("jc_captchaImg")){
		 	jax.$("jc_captchaImg").src = jax_live_site + "?option=com_jomcomment&no_html=1&task=img&jc_sid=" +  sid.toString();	 
		}
	}
}
<?php
echo '
	jc_username     = "' . $userName . '";
	jc_email        = "' . $userEmail . '";
';

exit();
?>