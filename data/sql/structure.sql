/*
SQLyog Enterprise - MySQL GUI v8.12 
MySQL - 5.1.37-1ubuntu5 : Database - ridedb_prod
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `blog_links` */

DROP TABLE IF EXISTS `blog_links`;

CREATE TABLE `blog_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `submitter` int(11) NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` int(11) DEFAULT NULL,
  `status` enum('valid','invalid') DEFAULT NULL,
  `blogId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `blog_posts` */

DROP TABLE IF EXISTS `blog_posts`;

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `submitter` int(11) NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` int(11) DEFAULT NULL,
  `status` enum('valid','invalid') DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `blogId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=435 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `blogs` */

DROP TABLE IF EXISTS `blogs`;

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `submitter` int(11) NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` int(11) DEFAULT NULL,
  `status` enum('valid','invalid') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2110 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `checkins` */

DROP TABLE IF EXISTS `checkins`;

CREATE TABLE `checkins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `submitter` int(10) unsigned NOT NULL,
  `spot` int(10) unsigned NOT NULL,
  `checkinDate` datetime NOT NULL,
  `checkinDuration` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` int(10) unsigned DEFAULT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


/*Table structure for table `comments` */

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` int(10) unsigned DEFAULT NULL,
  `parentType` varchar(64) DEFAULT NULL,
  `date` datetime NOT NULL,
  `submitter` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` int(10) unsigned DEFAULT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  `tone` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3969 DEFAULT CHARSET=utf8;

/*Table structure for table `countries` */

DROP TABLE IF EXISTS `countries`;

CREATE TABLE  `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  `lang` varchar(10) NOT NULL,
  `simpleTitle` varchar(32) NOT NULL,
  `status` enum('valid','invalid') NOT NULL,
  `date` datetime NOT NULL,
  `submitter` int(11) NOT NULL,
  `bounds` varchar(64) DEFAULT NULL,
  `lastEditor` int(11) DEFAULT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

/*Table structure for table `dossiers` */

DROP TABLE IF EXISTS `dossiers`;

CREATE TABLE `dossiers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `submitter` int(11) NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` int(11) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `status` enum('valid','invalid') DEFAULT 'invalid',
  `category` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Table structure for table `dpt` */

DROP TABLE IF EXISTS `dpt`;

CREATE TABLE  `dpt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `prefix` varchar(10) NOT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  `location` int(10) unsigned DEFAULT NULL,
  `simpleTitle` varchar(128) NOT NULL,
  `code` varchar(8) NOT NULL,
  `country` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `submitter` int(11) NOT NULL DEFAULT '1',
  `bounds` varchar(128) DEFAULT NULL,
  `lastEditor` int(11) NOT NULL DEFAULT '1',
  `lastEditionDate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `forum_access` */

DROP TABLE IF EXISTS `forum_access`;

CREATE TABLE `forum_access` (
  `forumId` int(10) unsigned NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `access` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`forumId`,`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `forum_posts` */

DROP TABLE IF EXISTS `forum_posts`;

CREATE TABLE `forum_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topicId` int(10) unsigned NOT NULL,
  `content` text CHARACTER SET latin1 NOT NULL,
  `submitter` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `status` enum('valid','invalid') CHARACTER SET latin1 NOT NULL DEFAULT 'invalid',
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` int(10) unsigned DEFAULT NULL,
  `tone` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35437 DEFAULT CHARSET=utf8;

/*Table structure for table `forum_topics` */

DROP TABLE IF EXISTS `forum_topics`;

CREATE TABLE `forum_topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forumId` int(10) unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `submitter` varchar(45) CHARACTER SET latin1 NOT NULL,
  `date` datetime NOT NULL,
  `lastEditor` int(10) unsigned DEFAULT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `status` enum('valid','invalid') CHARACTER SET latin1 DEFAULT 'invalid',
  `sticky` tinyint(1) DEFAULT '0',
  `announcement` tinyint(1) DEFAULT '0',
  `lastPostDate` datetime NOT NULL,
  `lastPoster` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3276 DEFAULT CHARSET=utf8;

/*Table structure for table `forums` */

DROP TABLE IF EXISTS `forums`;

CREATE TABLE `forums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `category` int(10) unsigned NOT NULL,
  `privacy` enum('public','private') DEFAULT 'public',
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  `lastPoster` int(10) unsigned NOT NULL,
  `lastPostDate` datetime NOT NULL,
  `submitter` int(10) unsigned NOT NULL,
  `lastEditor` int(10) unsigned DEFAULT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `topics` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

/*Table structure for table `imports` */

DROP TABLE IF EXISTS `imports`;

CREATE TABLE `imports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemId` int(11) NOT NULL,
  `itemType` varchar(32) NOT NULL,
  `url` varchar(512) DEFAULT NULL,
  `oldId` int(11) NOT NULL,
  `oldItemType` varchar(64) NOT NULL,
  `oldUrl` varchar(512) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `NewItem` (`itemId`,`itemType`),
  UNIQUE KEY `OldItem` (`oldId`,`oldItemType`)
) ENGINE=MyISAM AUTO_INCREMENT=10858 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `item_views` */

DROP TABLE IF EXISTS `item_views`;

CREATE TABLE `item_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemId` int(11) NOT NULL,
  `itemType` varchar(40) NOT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `lastView` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item` (`itemId`,`itemType`),
  KEY `date` (`lastView`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*Table structure for table `items` */

DROP TABLE IF EXISTS `items`;

CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Index of the item in its table',
  `itemId` int(11) NOT NULL DEFAULT '0',
  `itemType` varchar(32) NOT NULL DEFAULT '' COMMENT 'Type of the item (equivalent to its table)',
  `date` datetime DEFAULT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  `parentItemId` int(10) unsigned DEFAULT NULL,
  `submitter` int(10) unsigned NOT NULL,
  `notification` enum('announce','silent') DEFAULT 'announce',
  PRIMARY KEY (`id`),
  UNIQUE KEY `item` (`date`,`itemId`,`itemType`) USING BTREE,
  KEY `parentItemId` (`parentItemId`)
) ENGINE=MyISAM AUTO_INCREMENT=82534 DEFAULT CHARSET=utf8;

/*Table structure for table `locations` */

DROP TABLE IF EXISTS `locations`;

CREATE TABLE  `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `longitude` decimal(11,8) NOT NULL,
  `latitude` decimal(11,8) NOT NULL,
  `zoom` int(10) unsigned NOT NULL DEFAULT '13',
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  `mapType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `yaw` decimal(11,8) NOT NULL,
  `pitch` decimal(11,8) NOT NULL,
  `itemId` int(11) DEFAULT NULL,
  `itemType` varchar(32) NOT NULL,
  `dpt` int(11) NOT NULL,
  `country` int(11) NOT NULL,
  `city` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lon` (`longitude`,`latitude`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `media_album_aggregations` */

DROP TABLE IF EXISTS `media_album_aggregations`;

CREATE TABLE `media_album_aggregations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'albumId',
  `keyName` varchar(45) CHARACTER SET latin1 NOT NULL,
  `keyValue` int(10) unsigned NOT NULL,
  `albumId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `Index_2` (`albumId`)
) ENGINE=MyISAM AUTO_INCREMENT=1820 DEFAULT CHARSET=utf8;

/*Table structure for table `media_albums` */

DROP TABLE IF EXISTS `media_albums`;

CREATE TABLE `media_albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `submitter` int(11) NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` int(11) DEFAULT NULL,
  `location` int(10) unsigned DEFAULT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  `albumType` enum('simple','aggregate') NOT NULL DEFAULT 'simple' COMMENT 'Simple or aggregated',
  `albumAccess` enum('public','private') NOT NULL DEFAULT 'public',
  `albumCreation` enum('static','automatic','user') NOT NULL DEFAULT 'automatic',
  `spot` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3376 DEFAULT CHARSET=utf8;

/*Table structure for table `media_albums_items` */

DROP TABLE IF EXISTS `media_albums_items`;

CREATE TABLE `media_albums_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemType` varchar(32) NOT NULL,
  `itemId` int(11) NOT NULL,
  `albumId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item` (`itemType`,`itemId`),
  UNIQUE KEY `album` (`albumId`)
) ENGINE=InnoDB AUTO_INCREMENT=1546 DEFAULT CHARSET=utf8;

/*Table structure for table `media_items` */

DROP TABLE IF EXISTS `media_items`;

CREATE TABLE `media_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` int(10) unsigned DEFAULT NULL,
  `location` int(10) unsigned DEFAULT NULL,
  `dpt` varchar(255) DEFAULT NULL,
  `spot` varchar(255) DEFAULT NULL,
  `trick` varchar(255) DEFAULT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  `albumId` int(11) unsigned NOT NULL,
  `mediaType` varchar(255) DEFAULT NULL,
  `uri` varchar(1024) DEFAULT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  `mediaSubType` varchar(255) NOT NULL,
  `submitter` int(10) unsigned NOT NULL,
  `thumbnailUri` varchar(1024) DEFAULT NULL,
  `thumbnailWidth` int(10) unsigned DEFAULT NULL,
  `thumbnailHeight` int(10) unsigned DEFAULT NULL,
  `thumbnailSubType` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `externalKey` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1456 DEFAULT CHARSET=utf8;

/*Table structure for table `media_items_riders` */

DROP TABLE IF EXISTS `media_items_riders`;

CREATE TABLE `media_items_riders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mediaId` int(10) unsigned NOT NULL,
  `riderId` int(10) unsigned NOT NULL,
  `riderName` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1293 DEFAULT CHARSET=utf8;

/*Table structure for table `news` */

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `submitter` int(11) NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` int(11) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `location` int(10) unsigned DEFAULT NULL,
  `dpt` varchar(255) DEFAULT NULL,
  `spot` varchar(255) DEFAULT NULL,
  `status` enum('valid','invalid') DEFAULT 'invalid',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `privatemessages` */

DROP TABLE IF EXISTS `privatemessages`;

CREATE TABLE `privatemessages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(45) CHARACTER SET latin1 NOT NULL,
  `content` text CHARACTER SET latin1 NOT NULL,
  `date` datetime NOT NULL,
  `submitter` int(10) unsigned NOT NULL,
  `lastEditor` int(10) unsigned DEFAULT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `toUser` int(10) unsigned NOT NULL DEFAULT '0',
  `read` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` enum('valid','invalid') CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `from` (`submitter`),
  KEY `to` (`toUser`,`date`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `spots` */

DROP TABLE IF EXISTS `spots`;

CREATE TABLE `spots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `submitter` int(11) NOT NULL,
  `lastEditor` int(11) DEFAULT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `dpt` int(11) NOT NULL,
  `location` int(11) DEFAULT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'valid',
  `difficulty` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `spotType` tinyint(3) unsigned,
  `groundType` tinyint(3) unsigned,
  PRIMARY KEY (`id`),
  KEY `fk_spots_submitter` (`submitter`),
  KEY `fk_spots_locations` (`location`),
  KEY `fk_spots_dpt` (`dpt`),
  KEY `fk_spots_editor` (`lastEditor`)
) ENGINE=MyISAM AUTO_INCREMENT=952 DEFAULT CHARSET=utf8;

/*Table structure for table `tags` */

DROP TABLE IF EXISTS `tags`;

CREATE TABLE `tags` (
  `itemId` int(10) unsigned NOT NULL DEFAULT '0',
  `itemType` varchar(32) NOT NULL DEFAULT '',
  `text` varchar(64) NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `item` (`itemId`,`itemType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `tests` */

DROP TABLE IF EXISTS `tests`;

CREATE TABLE `tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `submitter` int(11) NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` int(11) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `status` enum('valid','invalid') DEFAULT 'invalid',
  `category` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Table structure for table `translated_texts` */

DROP TABLE IF EXISTS `translated_texts`;

CREATE TABLE `translated_texts` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `itemType` varchar(64) CHARACTER SET latin1 NOT NULL,
  `lang` char(2) CHARACTER SET latin1 NOT NULL,
  `type` enum('title','description','content') CHARACTER SET latin1 NOT NULL,
  `text` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`,`itemType`,`lang`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `tricks` */

DROP TABLE IF EXISTS `tricks`;

CREATE TABLE `tricks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `submitter` int(11) NOT NULL,
  `lastEditor` int(11) DEFAULT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  `difficulty` int(10) unsigned NOT NULL DEFAULT '1',
  `trickTip` varchar(512) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_tricks_submitter` (`submitter`),
  KEY `fk_tricks_editor` (`lastEditor`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

/*Table structure for table `user_notifications` */

DROP TABLE IF EXISTS `user_notifications`;

CREATE TABLE `user_notifications` (
  `userId` int(10) unsigned NOT NULL,
  `itemType` varchar(30) NOT NULL,
  `medium` enum('none','homePage','email','twitter','facebook') NOT NULL DEFAULT 'none',
  `notify` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`userId`,`medium`,`itemType`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `status` enum('banned','guest','pending','member','writer','editor','admin') NOT NULL DEFAULT 'guest',
  `date` datetime DEFAULT NULL,

  `lastLogin` datetime DEFAULT NULL,
  `openidIdentity` varchar(256) DEFAULT NULL,
  `lang` varchar(3) DEFAULT NULL,
  `firstName` varchar(64) DEFAULT NULL,
  `lastName` varchar(64) DEFAULT NULL,
  `birthDate` date DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `zip` int(10) unsigned DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `level` tinyint(1) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `occupation` varchar(128) DEFAULT NULL,
  `gear` varchar(255) DEFAULT NULL,
  `otherSports` varchar(255) DEFAULT NULL,
  `rideType` varchar(3) DEFAULT NULL,
  `activationKey` varchar(32) DEFAULT NULL,
  `newPassword` varchar(32) DEFAULT NULL,
  `avatar` varchar(512) DEFAULT NULL,
  `dpt` int(11) DEFAULT NULL,  
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=2110 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
