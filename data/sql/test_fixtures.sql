TRUNCATE items;
TRUNCATE media_album_aggregations;
TRUNCATE media_albums;
TRUNCATE media_albums_items;
TRUNCATE media_items;
TRUNCATE media_items_riders;
TRUNCATE translated_texts;
TRUNCATE user_notifications;
TRUNCATE users;

insert  into `items`(`id`,`itemId`,`itemType`,`date`,`status`,`parentItemId`,`submitter`,`notification`) values
(1,1,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent'),
(2,2,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent'),
(3,3,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent'),
(4,4,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent'),
(5,5,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'85193083-ce22-43a5-993b-1c7aba53d13c','silent'),
(6,6,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'b1786ac1-5cc8-4156-8471-8a80a87efe17','silent'),
(7,7,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent'),
(8,8,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'102e6ed9-cdac-4c9c-9483-a3309970db59','silent'),
(9,9,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'6750ff62-7195-49f5-bf81-e3d395e6cdcf','silent'),
(10,10,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'941b2ac5-2519-44de-84f9-ea9980e4631f','silent'),
(11,11,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'cc834ce6-58df-4381-aed3-8fe4c2923434','silent'),
(12,12,'mediaalbum','2002-02-18 14:00:00','valid',NULL,'85193083-ce22-43a5-993b-1c7aba53d13c','silent'),

(13,1,'photo','2002-02-18 10:00:00','valid',NULL,'6750ff62-7195-49f5-bf81-e3d395e6cdcf','announce'),
(14,2,'photo','2002-02-19 10:00:00','valid',NULL,'6750ff62-7195-49f5-bf81-e3d395e6cdcf','announce'),
(15,3,'video','2002-02-20 10:00:00','valid',NULL,'85193083-ce22-43a5-993b-1c7aba53d13c','announce'),
(16,4,'photo','2002-02-19 10:00:00','valid',NULL,'85193083-ce22-43a5-993b-1c7aba53d13c','announce'),
(17,5,'photo','2002-02-19 10:00:00','valid',NULL,'6750ff62-7195-49f5-bf81-e3d395e6cdcf','announce'),
(18,6,'photo','2002-02-19 10:00:00','invalid',NULL,'85193083-ce22-43a5-993b-1c7aba53d13c','announce');

insert  into `media_album_aggregations`(`id`,`keyName`,`keyValue`,`albumId`) values
(1,'user','85193083-ce22-43a5-993b-1c7aba53d13c',4),
(2,'user','b1786ac1-5cc8-4156-8471-8a80a87efe17',5);

insert  into `media_albums`(`id`,`date`,`submitter`,`lastEditionDate`,`lastEditor`,`status`,`albumType`,`albumAccess`,`albumCreation`) values
(1, '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static'),
(2, '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static'),
(3, '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static'),
(4, '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static'),
(5, '2002-02-18 14:00:00','85193083-ce22-43a5-993b-1c7aba53d13c',NULL,NULL,'valid','aggregate','public','automatic'),
(6, '2002-02-18 14:00:00','b1786ac1-5cc8-4156-8471-8a80a87efe17',NULL,NULL,'valid','aggregate','public','automatic'),
(7, '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','aggregate','public','automatic'),
(8, '2002-02-18 14:00:00','102e6ed9-cdac-4c9c-9483-a3309970db59',NULL,NULL,'valid','aggregate','public','automatic'),
(9, '2002-02-18 14:00:00','6750ff62-7195-49f5-bf81-e3d395e6cdcf',NULL,NULL,'valid','aggregate','public','automatic'),
(10,'2002-02-18 14:00:00','941b2ac5-2519-44de-84f9-ea9980e4631f',NULL,NULL,'valid','aggregate','public','automatic'),
(11,'2002-02-18 14:00:00',8,NULL,NULL,'valid','aggregate','public','automatic'),
(12,'2002-02-18 14:00:00','85193083-ce22-43a5-993b-1c7aba53d13c',NULL,NULL,'valid','simple','public','static');

insert into `media_items`(`id`,`submitter`,`date`,`lastEditionDate`,`lastEditor`, `status`, `albumId`, `mediaType`, `key`, `width`, `height`, `size`, `mediaSubType`, `thumbnailWidth`, `thumbnailHeight`, `storageType`) VALUES
(1, '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-18 10:00:00', null, null, 'valid', 1,  'photo', 'tata.jpg', 720, 540, 56789, 'jpg', 160, 120, 'local'),
(2, '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:00', null, null, 'valid', 1,  'photo', 'tata2.jpg', 720, 540, 5678900, 'jpg', 160, 120, 'local'),
(3, '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-20 10:00:00', null, null, 'valid', 12, 'video', 'myvideo', 720, 540, 0, 'youtube', 160, 120, null),
(4, '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:00', null, null, 'valid', 1,  'photo', 'tata3.jpg', 720, 540, 5678900, 'jpg', 160, 120, 'local'),
(5, '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:00', null, null, 'valid', 1,  'photo', 'tata4.jpg', 720, 540, 5678900, 'jpg', 160, 120, 'local'),
(6, '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:00', null, null, 'invalid', 1,  'photo', 'invalid.jpg', 720, 540, 5678900, 'jpg', 160, 120, 'local');

insert  into `translated_texts`(`id`,`itemType`,`lang`,`type`,`text`) values
(1,'mediaalbum','fr','title','photoAlbumTitleFr'),
(1,'mediaalbum','fr','description','photoAlbumDescFr'),
(1,'mediaalbum','en','title','photoAlbumTitleEn'),
(1,'mediaalbum','en','description','photoAlbumDescEn'),

(2,'mediaalbum','fr','title','videoAlbumTitleFr'),
(2,'mediaalbum','fr','description','videoAlbumDescFr'),
(2,'mediaalbum','en','title','videoAlbumTitleEn'),
(2,'mediaalbum','en','description','videoAlbumDescEn'),

(3,'mediaalbum','fr','title','portfolioTitleFr'),
(3,'mediaalbum','fr','description','portfolioDescFr'),
(3,'mediaalbum','en','title','portfolioTitleEn'),
(3,'mediaalbum','en','description','portfolioDescEn'),

(4,'mediaalbum','en','title','dummyAlbumTitle'),
(4,'mediaalbum','en','description','dummyAlbumDescription'),

(5,'mediaalbum','en','title','album de plainUserAlbumTitle'),
(5,'mediaalbum','en','description','plainUserAlbumDescription'),

(6,'mediaalbum','en','title','album de bannedUserAlbumTitle'),
(6,'mediaalbum','en','description','bannedUserAlbumDescription'),

(7,'mediaalbum','en','title','album de adminUserAlbumTitle'),
(7,'mediaalbum','en','description','adminUserAlbumDescription'),

(8,'mediaalbum','en','title','album de editorUserAlbumTitle'),
(8,'mediaalbum','en','description','editorUserAlbumDescription'),

(9,'mediaalbum','en','title','album de writerUserAlbumTitle'),
(9,'mediaalbum','en','description','writerUserAlbumDescription'),

(10,'mediaalbum','en','title','album de otherUserAlbumTitle'),
(10,'mediaalbum','en','description','otherUserAlbumDescription'),

(11,'mediaalbum','en','title','pendinguserAlbumTitle'),
(11,'mediaalbum','en','description','pendinguserAlbumDescription'),

(12,'mediaalbum','en','title','nonEmptyPlainUserAlbumTitle'),
(12,'mediaalbum','en','description','nonEmptyPlainUserAlbumDescription'),

(1,'photo','en','title','firstPhotoTitle'),
(1,'photo','en','description','firstPhotoDescription'),

(2,'photo','en','title','secondPhotoTitle'),
(2,'photo','en','description','secondPhotoDescription'),

(3,'video','en','title','firstVideoTitle'),
(3,'video','en','description','firstVideoDescription'),

(4,'photo','en','title','thirdPhotoTitle'),
(4,'photo','en','description','thirdPhotoDescription'),

(5,'photo','en','title','fourthPhotoTitle'),
(5,'photo','en','description','fourthPhotoDescription'),

(6,'photo','en','title','plainUserPhotoTitle'),
(6,'photo','en','description','plainUserPhotoDescription');

insert  into `user_notifications`(`userId`,`itemType`,`medium`,`notify`) values
(0,'mediaalbum','homePage',0),
(0,'photo','homePage',1),
(0,'user','homePage',1),
(0,'video','homePage',1),

('85193083-ce22-43a5-993b-1c7aba53d13c','mediaalbum','homePage',0),
('85193083-ce22-43a5-993b-1c7aba53d13c','photo','homePage',1),
('85193083-ce22-43a5-993b-1c7aba53d13c','user','homePage',1),
('85193083-ce22-43a5-993b-1c7aba53d13c','video','homePage',1),

('60bfb8a7-5754-4186-acd2-44b20ef32399','mediaalbum','homePage',1),
('102e6ed9-cdac-4c9c-9483-a3309970db59','mediaalbum','homePage',0),

('941b2ac5-2519-44de-84f9-ea9980e4631f','mediaalbum','homePage',0),
('941b2ac5-2519-44de-84f9-ea9980e4631f','photo','homePage',1),
('941b2ac5-2519-44de-84f9-ea9980e4631f','user','homePage',1),
('941b2ac5-2519-44de-84f9-ea9980e4631f','video','homePage',1);

insert  into `users`(`userId`,`username`,`password`,`email`,`status`,`date`,`lastLogin`,`lang`,`firstName`,`lastName`,`city`,`activationKey`,`newPassword`,`avatar`) values
('85193083-ce22-43a5-993b-1c7aba53d13c','plainuser','25f9e794323b453885f5181f1b624d0b','user1@example.org','member','2011-01-01 00:00:01','2011-08-14 08:12:20','fr','prenom','nom','toulouse',NULL,NULL,'/media/avatars/1.jpg'),
('b1786ac1-5cc8-4156-8471-8a80a87efe17','banneduser','25f9e794323b453885f5181f1b624d0b','banned@mountainboard.fr','banned','2011-01-01 00:00:02','2010-01-11 09:04:37','fr','','','',NULL,NULL,NULL),
('60bfb8a7-5754-4186-acd2-44b20ef32399','adminuser','25f9e794323b453885f5181f1b624d0b','admin@mountainboard.fr','admin','2011-01-01 00:00:03','2010-01-11 09:04:37','en','','','oakland',NULL,NULL,NULL),
('102e6ed9-cdac-4c9c-9483-a3309970db59','editoruser','25f9e794323b453885f5181f1b624d0b','editor@mountainboard.fr','editor','2011-01-01 00:00:04','2010-01-11 09:04:37','en','','','madrid',NULL,NULL,NULL),
('6750ff62-7195-49f5-bf81-e3d395e6cdcf','writeruser','25f9e794323b453885f5181f1b624d0b','writer@mountainboard.fr','writer','2011-01-01 00:00:05','2010-01-11 09:04:37','fr','','',NULL,NULL,NULL,NULL),
('941b2ac5-2519-44de-84f9-ea9980e4631f','otheruser','25f9e794323b453885f5181f1b624d0b','member@mountainboard.fr','member','2011-01-01 00:00:06','2010-01-11 09:04:37','en','','',NULL,NULL,NULL,NULL),
('cc834ce6-58df-4381-aed3-8fe4c2923434','pendinguser','25f9e794323b453885f5181f1b624d0b','pending@mountainboard.fr','pending','2011-01-01 00:00:07','2010-01-11 09:04:37','fr','','','','art85dnh2obrozxtqo830shfcmsp4acl',NULL,NULL),
('0230ec1d-dc7b-42e6-89d3-3707ee5ade71','guest','empty','','guest',NULL,'2011-01-01 00:00:08',NULL,NULL,NULL,NULL,NULL,NULL,NULL);

