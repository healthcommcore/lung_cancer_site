
CREATE TABLE IF NOT EXISTS `#__recipe` (
  `id` int(11) NOT NULL auto_increment,
  `webcat` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `serves` varchar(128) NOT NULL,
  `intro` text NOT NULL,
  `ingredients` text NOT NULL,
  `instructions` text NOT NULL,
  `imagefile` varchar(64) NOT NULL,
  `masterid` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL,
  `featured` tinyint(1) default '0',
  `language` varchar(2) NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__recipe_dietcat` (
  `id` int(11) NOT NULL auto_increment,
  `dietcat` varchar(128) NOT NULL,
  `tagname` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `#__recipe_dietproject` (
  `dietid` int(11) NOT NULL,
  `projectid` int(11) NOT NULL,
  PRIMARY KEY  (`dietid`,`projectid`)
);

CREATE TABLE IF NOT EXISTS `#__recipe_recipediet` (
  `dietid` int(11) NOT NULL,
  `recipeid` int(11) NOT NULL,
  PRIMARY KEY  (`dietid`,`recipeid`)
);

CREATE TABLE IF NOT EXISTS `#__recipe_translate` (
  `origid` int(11) NOT NULL COMMENT 'original (english) recipe id',
  `transid` int(11) NOT NULL COMMENT 'translated recipe id',
  PRIMARY KEY  (`origid`,`transid`)
);

CREATE TABLE IF NOT EXISTS `#__recipe_project` (
  `id` int(11) NOT NULL auto_increment,
  `project` varchar(64) NOT NULL,
  `dbserver` varchar(32) NOT NULL,
  `dbuser` varchar(64) NOT NULL,
  `dbpwd` varchar(64) NOT NULL,
  `dbname` varchar(64) NOT NULL,
  `synch1` tinyint(1) NOT NULL default '0',
  `dbserver2` varchar(32) NOT NULL,
  `dbuser2` varchar(64) NOT NULL,
  `dbpwd2` varchar(64) NOT NULL,
  `dbname2` varchar(64) NOT NULL,
  `synch2` tinyint(1) NOT NULL default '0',  PRIMARY KEY  (`id`)
)  ;

CREATE TABLE IF NOT EXISTS `#__recipe_recipeproject` (
  `projectid` int(11) NOT NULL,
  `recipeid` int(11) NOT NULL,
  PRIMARY KEY  (`projectid`,`recipeid`)
) ;
