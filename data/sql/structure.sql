# Dump of table comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
    `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `parentId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `parentType` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `content` text COLLATE utf8mb4_unicode_ci,
    `date` datetime NOT NULL,
    `submitter` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `lastEditionDate` datetime DEFAULT NULL,
    `lastEditor` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `status` enum('valid','invalid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invalid',
    `tone` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `images`;

CREATE TABLE `images` (
    `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `imageType` int(11) NOT NULL,
    `storageType` int(11) NOT NULL,
    `status` enum('valid','invalid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'valid',
    `submitter` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `date` datetime(3) NOT NULL,
    `lastEditionDate` datetime(3) DEFAULT NULL,
    `lastEditor` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `width` int(11) NOT NULL,
    `height` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table item_views
# ------------------------------------------------------------

DROP TABLE IF EXISTS `item_views`;

CREATE TABLE `item_views` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `itemId` int(11) NOT NULL,
    `itemType` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
    `views` int(11) NOT NULL DEFAULT '0',
    `lastView` datetime(3) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `item` (`itemId`,`itemType`),
    KEY `date` (`lastView`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `items`;

CREATE TABLE `items` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Index of the item in its table',
    `itemId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
    `itemType` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Type of the item (equivalent to its table)',
    `date` datetime(3) DEFAULT NULL,
    `status` enum('valid','invalid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invalid',
    `parentItemId` varchar(36) DEFAULT NULL,
    `parentItemType` varchar(32) DEFAULT NULL,
    `parentItemDate` datetime(3) DEFAULT NULL,
    `submitter` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `notification` enum('announce','silent') COLLATE utf8mb4_unicode_ci DEFAULT 'announce',
    PRIMARY KEY (`id`),
    UNIQUE KEY `item` (`date`,`itemId`,`itemType`) USING BTREE,
    KEY `parentItemId` (`parentItemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table media_album_aggregations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_album_aggregations`;

CREATE TABLE `media_album_aggregations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'albumId',
    `keyName` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `keyValue` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `albumId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `Index_2` (`albumId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table media_albums
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_albums`;

CREATE TABLE `media_albums` (
      `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
      `date` datetime(3) NOT NULL,
      `submitter` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
      `lastEditionDate` datetime(3) DEFAULT NULL,
      `lastEditor` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `title` text COLLATE utf8mb4_unicode_ci,
      `description` text COLLATE utf8mb4_unicode_ci,
      `status` enum('valid','invalid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invalid',
      `albumType` enum('simple','aggregate') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'simple' COMMENT 'Simple or aggregated',
      `albumContributions` enum('private','public') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'private',
      `albumCreation` enum('static','automatic','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'automatic',
      `albumVisibility` enum('private','unlisted','visible') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visible',
      PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table media_albums_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_albums_items`;

CREATE TABLE `media_albums_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `itemType` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
    `itemId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `albumId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `item` (`itemType`,`itemId`),
    UNIQUE KEY `album` (`albumId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table media_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_items`;

CREATE TABLE `media_items` (
     `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
     `mediaType` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     `mediaSubType` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
     `vendorKey` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     `albumId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
     `title` text COLLATE utf8mb4_unicode_ci,
     `description` text COLLATE utf8mb4_unicode_ci,
     `storageType` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
     `imageId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
     `width` int(10) unsigned NOT NULL,
     `height` int(10) unsigned NOT NULL,
     `thumbWidth` int(10) unsigned NOT NULL,
     `thumbHeight` int(10) unsigned NOT NULL,
     `status` enum('valid','invalid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invalid',
     `submitter` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
     `date` datetime(3) NOT NULL,
     `lastEditionDate` datetime(3) DEFAULT NULL,
     `lastEditor` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `imageId` (`imageId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table media_items_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media_items_users`;

CREATE TABLE `media_items_users` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `mediaId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `userId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `userName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
    `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `date` datetime(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table user_notifications
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_notifications`;

CREATE TABLE `user_notifications` (
    `userId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `itemType` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
    `medium` enum('none','homePage','email','twitter','facebook') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
    `notify` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`userId`,`medium`,`itemType`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `userId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `username` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `password` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `salt` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `email` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` enum('banned','guest','pending','member','writer','editor','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'guest',
    `date` datetime(3) DEFAULT NULL,
    `lastLogin` datetime(3) DEFAULT NULL,
    `locale` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `timezone` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `firstName` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `lastName` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `city` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `site` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `activationKey` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `newPassword` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `avatar` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `cover` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `bio` text COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
