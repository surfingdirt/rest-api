ALTER TABLE `users` ADD COLUMN `salt` varchar(36) NOT NULL after `password`;
