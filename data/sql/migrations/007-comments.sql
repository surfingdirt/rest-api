CREATE TABLE `comments` (
    `id` varchar(36) NOT NULL,
    `parentId` varchar(36) NOT NULL,
    `parentType` varchar(64) NOT NULL,
    `date` datetime NOT NULL,
    `submitter` varchar(36) NOT NULL,
    `lastEditionDate` datetime DEFAULT NULL,
    `lastEditor` varchar(36) DEFAULT NULL,
    `status` enum('valid','invalid') NOT NULL DEFAULT 'invalid',
    `tone` varchar(32) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;