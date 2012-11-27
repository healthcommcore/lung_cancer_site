<?php
class menujomcomment {
    function CONFIG_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save( 'savesettings', 'Save' );
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

    function SAVE_LANGUAGE_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save( 'savelanguagesettings', 'Save' );
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  
  function LATEST_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  
   function FILE_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save();
    mosMenuBar::cancel();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

  function ABOUT_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  
  function SUPPORT_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  
  function LICENSE_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  
  function HACKS_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  
  function MAINTD_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }
  
  function LANGUAGE_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::save( 'savelanguagesettings', 'Save' );
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

  function STATS_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

  function IMPORT_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::back();
    mosMenuBar::spacer();
    mosMenuBar::endTable();
  }

  function TRACKBACK_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::publish('publish_tb', 'Publish');
    mosMenuBar::unpublish('unpublish_tb', 'Unpublish');
    mosMenuBar::deleteList('', 'remove_tb', 'Remove');
    mosMenuBar::endTable();
  }

  function REPORTS_MENU() {
    mosMenuBar::startTable();
    mosMenuBar::publish('publish_reports', 'Publish');
    mosMenuBar::unpublish('unpublish_reports', 'Unpublish');
    mosMenuBar::deleteList('', 'dismiss_reports', 'Dismiss');
    mosMenuBar::endTable();
  }
  
  function MENU_Default() {
    mosMenuBar::startTable();
    mosMenuBar::publish();
    mosMenuBar::unpublish();
    mosMenuBar::deleteList();
    mosMenuBar::endTable();
  }
}
?>
