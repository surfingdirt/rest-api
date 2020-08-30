CREATE TABLE `surveys` (
    `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` enum('valid','invalid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invalid',
    `submitter` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `date` datetime(3) NOT NULL,
    `lastEditionDate` datetime(3) DEFAULT NULL,
    `lastEditor` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `survey_answers` (
    `surveyId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `userId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    `choice` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
    UNIQUE KEY `item` (`surveyId`,`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;