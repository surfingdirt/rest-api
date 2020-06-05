CREATE TABLE `reactions` (
    `userId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `itemType` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
    `itemId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `date` datetime(3) NOT NULL,
    `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`userId`),
    UNIQUE KEY `item` (`userId`,`itemType`,`itemId`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;