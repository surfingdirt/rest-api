/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `images` */

DROP TABLE IF EXISTS `images`;

CREATE TABLE `images` (
  `id` varchar(36) NOT NULL,
  `storageType` int(11) NOT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'valid',
  `submitter` varchar(36) NOT NULL,
  `date` datetime NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` varchar(36) DEFAULT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `items` */

DROP TABLE IF EXISTS `items`;

CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Index of the item in its table',
  `itemId` varchar(36) NOT NULL DEFAULT '0',
  `itemType` varchar(32) NOT NULL DEFAULT '' COMMENT 'Type of the item (equivalent to its table)',
  `date` datetime DEFAULT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  `parentItemId` int(11) unsigned DEFAULT NULL,
  `submitter` varchar(36) NOT NULL,
  `notification` enum('announce','silent') DEFAULT 'announce',
  PRIMARY KEY (`id`),
  UNIQUE KEY `item` (`date`,`itemId`,`itemType`) USING BTREE,
  KEY `parentItemId` (`parentItemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `media_album_aggregations`;

CREATE TABLE `media_album_aggregations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'albumId',
  `keyName` varchar(45) CHARACTER SET utf8 NOT NULL,
  `keyValue` varchar(36) NOT NULL,
  `albumId` varchar(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `Index_2` (`albumId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `media_albums` */

DROP TABLE IF EXISTS `media_albums`;

CREATE TABLE `media_albums` (
  `id` varchar(36) NOT NULL,
  `date` datetime NOT NULL,
  `submitter` varchar(36) NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` varchar(36) DEFAULT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  `albumType` enum('simple','aggregate') NOT NULL DEFAULT 'simple' COMMENT 'Simple or aggregated',
  `albumAccess` enum('public','private') NOT NULL DEFAULT 'public',
  `albumCreation` enum('static','automatic','user') NOT NULL DEFAULT 'automatic',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `media_albums_items` */

DROP TABLE IF EXISTS `media_albums_items`;

CREATE TABLE `media_albums_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemType` varchar(32) NOT NULL,
  `itemId` varchar(36) NOT NULL,
  `albumId` varchar(36) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item` (`itemType`,`itemId`),
  UNIQUE KEY `album` (`albumId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `media_items` */

DROP TABLE IF EXISTS `media_items`;

CREATE TABLE `media_items` (
  `id` varchar(36) NOT NULL,
  `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
  `albumId` varchar(36) NOT NULL,
  `submitter` varchar(36) NOT NULL,
  `date` datetime NOT NULL,
  `lastEditionDate` datetime DEFAULT NULL,
  `lastEditor` varchar(36) DEFAULT NULL,
  `mediaType` varchar(255) DEFAULT NULL,
  `mediaSubType` varchar(255) NOT NULL,
  `storageType` varchar(255) NOT NULL,
  `key` varchar(1024) DEFAULT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  `thumbnailWidth` int(10) unsigned DEFAULT NULL,
  `thumbnailHeight` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `media_items_users` */

DROP TABLE IF EXISTS `media_items_users`;

CREATE TABLE `media_items_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mediaId` varchar(36) NOT NULL,
  `userId` varchar(36) NOT NULL,
  `userName` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `translated_texts` */

DROP TABLE IF EXISTS `translated_texts`;

CREATE TABLE `translated_texts` (
  `id` varchar(36) NOT NULL,
  `itemType` varchar(64) CHARACTER SET utf8 NOT NULL,
  `lang` char(2) CHARACTER SET utf8 NOT NULL,
  `type` enum('title','description','content') CHARACTER SET utf8 NOT NULL,
  `text` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`,`itemType`,`lang`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `user_notifications` */

DROP TABLE IF EXISTS `user_notifications`;

CREATE TABLE `user_notifications` (
  `userId` varchar(36) NOT NULL,
  `itemType` varchar(30) NOT NULL,
  `medium` enum('none','homePage','email','twitter','facebook') NOT NULL DEFAULT 'none',
  `notify` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`userId`,`medium`,`itemType`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `userId` varchar(36) NOT NULL,
  `username` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `status` enum('banned','guest','pending','member','writer','editor','admin') NOT NULL DEFAULT 'guest',
  `date` datetime DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL,
  `lang` varchar(3) DEFAULT NULL,
  `firstName` varchar(64) DEFAULT NULL,
  `lastName` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `activationKey` varchar(32) DEFAULT NULL,
  `newPassword` varchar(32) DEFAULT NULL,
  `avatar` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
