ALTER TABLE `media_albums` CHANGE COLUMN `albumAccess` `albumContributions` enum('private', 'public') default 'private' NOT NULL;
ALTER TABLE `media_albums` ADD `albumVisiblity` enum('private','unlisted','visible') default 'visible' NOT NULL;


