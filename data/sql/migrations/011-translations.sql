ALTER TABLE `comments` ADD COLUMN `content` text default NULL;

ALTER TABLE `media_albums` ADD COLUMN `title` text default NULL;
ALTER TABLE `media_albums` ADD COLUMN `description` text default NULL;

ALTER TABLE `media_items` ADD COLUMN `title` text default NULL;
ALTER TABLE `media_items` ADD COLUMN `description` text default NULL;

ALTER TABLE `users` MODIFY COLUMN `bio` text default  NULL;