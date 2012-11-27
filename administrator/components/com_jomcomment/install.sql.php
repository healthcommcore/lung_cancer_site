<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 * 
 * execute all the installation sql here  
 **/

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

$db =& cmsInstance('CMSDb');

if(cmsVersion() == _CMS_JOOMLA10 || cmsVersion() == _CMS_MAMBO){
	$type   = "TYPE=MyISAM;";
}
elseif(cmsVersion() == _CMS_JOOMLA15){
	$type   = "TYPE=MyISAM CHARACTER SET `utf8`;";
}

$query = "CREATE TABLE IF NOT EXISTS `#__jomcomment` (
	`id` int(10) NOT NULL auto_increment,
	`parentid` int(10) NOT NULL default '0',
	`status` int(10) NOT NULL default '0',
	`contentid` int(10) NOT NULL default '0',
	`ip` varchar(15) NOT NULL default '',
	`name` varchar(255) NOT NULL default '',
	`title` varchar(200) NOT NULL default '',
	`comment` text NOT NULL,
	`preview` text NOT NULL,
	`date` datetime NOT NULL default '0000-00-00 00:00:00',
	`published` tinyint(1) NOT NULL default '0',
	`ordering` int(11) NOT NULL default '0',
	`email` varchar(100) NOT NULL default '',
	`website` varchar(100) NOT NULL default '',
	`updateme` smallint(5) unsigned NOT NULL default '0',
	`custom1` varchar(200) NOT NULL default '',
	`custom2` varchar(200) NOT NULL default '',
	`custom3` varchar(200) NOT NULL default '',
	`custom4` varchar(200) NOT NULL default '',
	`custom5` varchar(200) NOT NULL default '',
	`star` tinyint(3) unsigned NOT NULL default '0',
	`user_id` int(10) unsigned NOT NULL default '0',
	`option` varchar(50) NOT NULL default 'com_content',
	`voted` SMALLINT NOT NULL DEFAULT '0',
	`referer` varchar(255) NOT NULL default '',
	PRIMARY KEY  (`id`),
	KEY `option` (`option`),
	KEY `contentid` (`contentid`),
	KEY `published` (`published`)
	) {$type}";

$db->query($query);


$query = "CREATE TABLE IF NOT EXISTS `#__captcha_session` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`sessionid` VARCHAR(64) NOT NULL DEFAULT '',
	`password` VARCHAR(8) NOT NULL DEFAULT '',
	`date` datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY(`id`)
	) {$type}";
$db->query($query);	

$query = "CREATE TABLE  IF NOT EXISTS `#__jomcomment_config` (
	`name` VARCHAR(64) NOT NULL DEFAULT '',
	`value` TEXT NOT NULL DEFAULT '',
	PRIMARY KEY(`name`)
	) {$type}";
$db->query($query);	
	
$query = "CREATE TABLE  IF NOT EXISTS `#__jomcomment_admin` (
	`sid` VARCHAR(128) NOT NULL DEFAULT '',
	`commentid` int(10) NOT NULL default '0',
	`action` VARCHAR(128) NOT NULL DEFAULT '',
	`date` datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY(`sid`)
	) {$type}";
$db->query($query);

// Store all trackbacks	
$query = "CREATE TABLE IF NOT EXISTS `#__jomcomment_tb` (
	`id` INTEGER UNSIGNED NOT NULL  AUTO_INCREMENT,
	`contentid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
	`ip` VARCHAR(18) NOT NULL DEFAULT '',
	`date` datetime NOT NULL default '0000-00-00 00:00:00',
	`title` TEXT NOT NULL DEFAULT '',
	`excerpt` TEXT NOT NULL DEFAULT '',
	`url` varchar(255) NOT NULL default '',
	`blog_name` TEXT NOT NULL DEFAULT '',
	`charset` VARCHAR(45) NOT NULL DEFAULT '',
	`published` TINYINT UNSIGNED NOT NULL DEFAULT 0,
	`option` VARCHAR(64) NOT NULL DEFAULT '',
	PRIMARY KEY(`id`),
	KEY `contentid` (`contentid`),
	KEY `url` (`url`),
	KEY `published` (`published`),
	KEY `option` (`option`),
	KEY `ip` (`ip`)
	) {$type}";
$db->query($query);	


// Store all trackback that has been send out	
$query = "CREATE TABLE  IF NOT EXISTS  `#__jomcomment_tb_sent` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`url` VARCHAR( 200 ) NOT NULL ,
	`contentid` INT NOT NULL,
	UNIQUE (
	`url`
	)
	) {$type}";
$db->query($query);

// Temporary email queue	
$query = "CREATE TABLE IF NOT EXISTS `#__jomcomment_mailq` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`email` VARCHAR( 200 ) NOT NULL ,
	`status` TINYINT NOT NULL DEFAULT '0',
	`title` VARCHAR( 200 ) NOT NULL ,
	`name` VARCHAR( 200 ) NOT NULL ,
	`content` TEXT NOT NULL ,
	`posted_on` DATETIME NOT NULL ,
	INDEX ( `status` )
	) {$type}";
$db->query($query);	
	
//	All user reported instances
$query = "CREATE TABLE IF NOT EXISTS `#__jomcomment_reports` (
	`id` int(11) NOT NULL auto_increment,
	`ip` varchar(24) NOT NULL,
	`user_id` int(11) NOT NULL,
	`commentid` int(11) NOT NULL,
	`option` varchar(128)  NOT NULL,
	`reason` tinytext  NOT NULL,
	`status` tinyint(4) NOT NULL COMMENT '0 = unread',
	PRIMARY KEY  (`id`)
	) {$type}";
$db->query($query);

// All user votes	
$query = "CREATE TABLE  IF NOT EXISTS `#__jomcomment_votes` (
	`id` int(11) NOT NULL auto_increment,
	`ip` varchar(32) NOT NULL,
	`voted_on` datetime NOT NULL,
	`commentid` int(11) NOT NULL,
	`option` varchar(128) NOT NULL,
	`value` tinyint(4) NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `contentid` (`commentid`,`option`)
	) {$type}";
$db->query($query);

// Allow us to dismiss/ignore future report on any comment
$query = "CREATE TABLE  IF NOT EXISTS `#__jomcomment_reported` (
	`commentid` int(11) NOT NULL,
	PRIMARY KEY  (`commentid`)
	) {$type}";
$db->query($query);

// All user favorites	
$query = "CREATE TABLE IF NOT EXISTS  `#__jomcomment_fav` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`userid` INT NOT NULL ,
	`url` VARCHAR( 255 ) NOT NULL ,
	`contentid` INT NOT NULL ,
	`option` VARCHAR( 255 ) NOT NULL ,
	INDEX ( `contentid` , `option` )
	) {$type}";
$db->query($query);	


// Store user subscription	
$query = "CREATE TABLE  IF NOT EXISTS  `#__jomcomment_subs` (
	`id` int(11) NOT NULL auto_increment,
	`userid` int(11) NOT NULL,
	`url` varchar(255)  NOT NULL,
	`contentid` int(11) NOT NULL,
	`option` varchar(255) NOT NULL,
	`status` tinyint(4) NOT NULL,
	`email` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `contentid` (`contentid`,`option`)
	) {$type}";

$db->query($query);
	
