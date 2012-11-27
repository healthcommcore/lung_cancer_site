<?php
#CMS Version is Joomla1.5
class menujomcomment {
    function CONFIG_MENU() {
    JToolBarHelper::title( JText::_( 'JomComment Configuration' ), 'config.png' );
    JToolBarHelper::save( 'savesettings', 'Save' );
    JToolBarHelper::back();
    JToolBarHelper::spacer();
  }

function SAVE_LANGUAGE_MENU() {
    JToolBarHelper::title( JText::_( 'Edit Language' ), 'langmanager.png' );
	JToolBarHelper::save( 'savelanguagesettings', 'Save' );
    JToolBarHelper::back();
    JToolBarHelper::spacer();
  }
  
   function FILE_MENU() {
    JToolBarHelper::save();
    JToolBarHelper::cancel();
    JToolBarHelper::spacer();
  }

  function ABOUT_MENU() {
  	JToolBarHelper::title( JText::_( 'About JomComment' ), 'systeminfo.png' );
    JToolBarHelper::back();
    JToolBarHelper::spacer();
  }
  
  function LICENSE_MENU() {
		JToolBarHelper::title( JText::_( 'License Information' ), 'systeminfo.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}
	
	function HACKS_MENU() {
		JToolBarHelper::title( JText::_( '3rd Party Integration' ), 'plugin.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}
	
	function MAINTD_MENU() {
		JToolBarHelper::title( JText::_( 'Maintenance' ), 'plugin.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}
	
	function LANGUAGE_MENU() {
		JToolBarHelper::title( JText::_( 'Language Configuration' ), 'langmanager.png' );
	JToolBarHelper::save( 'savelanguagesettings', 'Save' );
    JToolBarHelper::back();
    JToolBarHelper::spacer();
	}
	
  
  function SUPPORT_MENU() {
		JToolBarHelper::title( JText::_( 'Support' ), 'systeminfo.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}

  function STATS_MENU() {
    JToolBarHelper::back();
    JToolBarHelper::spacer();
  }
  
  function LATEST_MENU(){
		JToolBarHelper::title( JText::_( 'Latest updates' ), 'systeminfo.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}

  function IMPORT_MENU() {
  	JToolBarHelper::title( JText::_( 'Imports' ), 'install.png' );
    JToolBarHelper::back();
    JToolBarHelper::spacer();
  }

  function TRACKBACK_MENU() {
  	JToolBarHelper::title( JText::_( 'Trackbacks' ), 'addedit.png' );
    JToolBarHelper::publish('publish_tb', 'Publish');
    JToolBarHelper::unpublish('unpublish_tb', 'Unpublish');
    JToolBarHelper::deleteList('', 'remove_tb', 'Remove');
  }

  function REPORTS_MENU() {
  	JToolBarHelper::title( JText::_( 'Reports' ), 'addedit.png' );
    JToolBarHelper::publish('publish_reports', 'Publish');
    JToolBarHelper::unpublish('unpublish_reports', 'Unpublish');
    JToolBarHelper::deleteList('', 'dismiss_reports', 'Dismiss');
  }

  function MENU_Default() {
  	JToolBarHelper::title( JText::_( 'Comments' ), 'addedit.png' );
    JToolBarHelper::publish();
    JToolBarHelper::unpublish();
    JToolBarHelper::deleteList();
  }
}
?>
