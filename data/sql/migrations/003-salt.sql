ALTER TABLE `users` ADD COLUMN `salt` varchar(128) NOT NULL after `password`;
