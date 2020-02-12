ALTER TABLE `comments` MODIFY COLUMN `content` text COLLATE utf8mb4_unicode_ci default NULL;

ALTER TABLE `media_albums` MODIFY COLUMN `title` text COLLATE utf8mb4_unicode_ci default NULL;
ALTER TABLE `media_albums` MODIFY COLUMN `description` text COLLATE utf8mb4_unicode_ci default NULL;

ALTER TABLE `media_items` MODIFY COLUMN `title` text COLLATE utf8mb4_unicode_ci default NULL;
ALTER TABLE `media_items` MODIFY COLUMN `description` text COLLATE utf8mb4_unicode_ci default NULL;

ALTER TABLE `users` MODIFY COLUMN `bio` text COLLATE utf8mb4_unicode_ci default NULL;