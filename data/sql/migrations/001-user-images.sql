ALTER TABLE `users` DROP COLUMN `avatar`;
ALTER TABLE `users` ADD COLUMN `avatar` varchar(36) DEFAULT NULL after `newPassword`;
ALTER TABLE `users` ADD COLUMN `cover` varchar(36) DEFAULT NULL after `avatar`;