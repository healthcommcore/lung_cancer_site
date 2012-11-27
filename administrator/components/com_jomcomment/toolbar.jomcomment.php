<?php

/**
* @copyright (C) 2006 by Azrul Rahim - All rights reserved!
* @license http://www.azrul.com Copyrighted Commercial Software
**/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

require_once ($mainframe->getPath('toolbar_html'));
require_once ($mainframe->getPath('toolbar_default'));

switch ($task) {
	case "maintd":
		menujomcomment::MAINTD_MENU();
		break;
		
	case 'hacks':
		menujomcomment::HACKS_MENU();
		break;
		
	case "config" :
		menujomcomment :: CONFIG_MENU();
		break;
	case "editLanguage":
		menujomcomment :: SAVE_LANGUAGE_MENU();
		break;
		
	case "import" :
		menujomcomment :: IMPORT_MENU();
		break;
		
	case 'latestnews':
		menujomcomment :: LATEST_MENU();
		break;
	
	case "language" :
		menujomcomment :: LANGUAGE_MENU();
		break;

	case "edit" :
		menujomcomment :: FILE_MENU();
		break;

	case "comments" :
		menujomcomment :: MENU_Default();
		break;
		
	case "trackbacks" :
		menujomcomment :: TRACKBACK_MENU();
		break;

	case "reports" :
		menujomcomment :: REPORTS_MENU();
		break;
		
	case 'license':
		menujomcomment::LICENSE_MENU();
		break;
	
	case "editLanguage" :
		menujomcomment :: STATS_MENU();
		break;
		
	
	case "about" :
		menujomcomment :: ABOUT_MENU();
		break;

	case "stats" :
		menujomcomment :: STATS_MENU();
		break;
	
	case "support" :
		menujomcomment :: SUPPORT_MENU();
		break;
		
	case "reports":
		break;
	default :
		//MENU_Default::MENU_Default();
		menujomcomment :: MENU_Default();
		break;
}
?>
