CREATE TABLE `reactions` (
    `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `itemType` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
    `itemId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` enum('valid','invalid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invalid',
    `submitter` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `date` datetime(3) NOT NULL,
    `lastEditionDate` datetime(3) DEFAULT NULL,
    `lastEditor` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `item` (`submitter`,`itemType`,`itemId`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;