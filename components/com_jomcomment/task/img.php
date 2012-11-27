<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 * This file is to perform the execution of displaying captcha's image
 **/

include_once(JC_MODEL_PATH . '/captcha.db.php');
include_once(JC_LIB_PATH . '/captcha.class.php');

$cms    	=& cmsInstance('CMSCore');
$cms->load('libraries','input');

$captcha    = new JCCaptcha();
$captcha->show($cms->input->get('jc_sid', ''));
?>