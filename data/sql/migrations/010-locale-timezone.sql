ALTER TABLE `users` ADD COLUMN `timezone` varchar(128) NOT NULL after `password`;
ALTER TABLE `users` ADD COLUMN `locale` varchar(128) NOT NULL after `password`;
ALTER TABLE `users` DROP COLUMN `lang`;
