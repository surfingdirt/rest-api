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
(1,1,'mediaalbum','2002-02-18 14:00:00','valid',NULL,4,'silent'),
(2,2,'mediaalbum','2002-02-18 14:00:00','valid',NULL,4,'silent'),
(3,3,'mediaalbum','2002-02-18 14:00:00','valid',NULL,4,'silent'),
(4,4,'mediaalbum','2002-02-18 14:00:00','valid',NULL,4,'silent'),
(5,5,'mediaalbum','2002-02-18 14:00:00','valid',NULL,1,'silent'),
(6,6,'mediaalbum','2002-02-18 14:00:00','valid',NULL,3,'silent'),
(7,7,'mediaalbum','2002-02-18 14:00:00','valid',NULL,4,'silent'),
(8,8,'mediaalbum','2002-02-18 14:00:00','valid',NULL,5,'silent'),
(9,9,'mediaalbum','2002-02-18 14:00:00','valid',NULL,6,'silent'),
(10,10,'mediaalbum','2002-02-18 14:00:00','valid',NULL,7,'silent'),
(11,11,'mediaalbum','2002-02-18 14:00:00','valid',NULL,8,'silent'),
(12,12,'mediaalbum','2002-02-18 14:00:00','valid',NULL,1,'silent'),

(13,1,'photo','2002-02-18 10:00:00','valid',NULL,6,'announce'),
(14,2,'photo','2002-02-19 10:00:00','valid',NULL,6,'announce'),
(15,3,'video','2002-02-20 10:00:00','valid',NULL,1,'announce'),
(16,4,'photo','2002-02-19 10:00:00','valid',NULL,1,'announce'),
(17,5,'photo','2002-02-19 10:00:00','valid',NULL,6,'announce'),
(18,6,'photo','2002-02-19 10:00:00','invalid',NULL,1,'announce');

insert  into `media_album_aggregations`(`id`,`keyName`,`keyValue`,`albumId`) values (1,'user',1,4),(2,'user',3,5);

insert  into `media_albums`(`id`,`date`,`submitter`,`lastEditionDate`,`lastEditor`,`status`,`albumType`,`albumAccess`,`albumCreation`) values
(1, '2002-02-18 14:00:00',4,NULL,NULL,'valid','simple','public','static'),
(2, '2002-02-18 14:00:00',4,NULL,NULL,'valid','simple','public','static'),
(3, '2002-02-18 14:00:00',4,NULL,NULL,'valid','simple','public','static'),
(4, '2002-02-18 14:00:00',4,NULL,NULL,'valid','simple','public','static'),
(5, '2002-02-18 14:00:00',1,NULL,NULL,'valid','aggregate','public','automatic'),
(6, '2002-02-18 14:00:00',3,NULL,NULL,'valid','aggregate','public','automatic'),
(7, '2002-02-18 14:00:00',4,NULL,NULL,'valid','aggregate','public','automatic'),
(8, '2002-02-18 14:00:00',5,NULL,NULL,'valid','aggregate','public','automatic'),
(9, '2002-02-18 14:00:00',6,NULL,NULL,'valid','aggregate','public','automatic'),
(10,'2002-02-18 14:00:00',7,NULL,NULL,'valid','aggregate','public','automatic'),
(11,'2002-02-18 14:00:00',8,NULL,NULL,'valid','aggregate','public','automatic'),
(12,'2002-02-18 14:00:00',1,NULL,NULL,'valid','simple','public','static');

insert into `media_items`(`id`,`submitter`,`date`,`lastEditionDate`,`lastEditor`, `status`, `albumId`, `mediaType`, `key`, `width`, `height`, `size`, `mediaSubType`, `thumbnailWidth`, `thumbnailHeight`, `storageType`) VALUES
(1, 6, '2002-02-18 10:00:00', null, null, 'valid', 1,  'photo', 'tata.jpg', 720, 540, 56789, 'jpg', 160, 120, 'local'),
(2, 6, '2002-02-19 10:00:00', null, null, 'valid', 1,  'photo', 'tata2.jpg', 720, 540, 5678900, 'jpg', 160, 120, 'local'),
(3, 1, '2002-02-20 10:00:00', null, null, 'valid', 12, 'video', 'myvideo', 720, 540, 5678900, 'youtube', 160, 120, 'local'),
(4, 6, '2002-02-19 10:00:00', null, null, 'valid', 1,  'photo', 'tata3.jpg', 720, 540, 5678900, 'jpg', 160, 120, 'local'),
(5, 6, '2002-02-19 10:00:00', null, null, 'valid', 1,  'photo', 'tata4.jpg', 720, 540, 5678900, 'jpg', 160, 120, 'local'),
(6, 1, '2002-02-19 10:00:00', null, null, 'invalid', 1,  'photo', 'invalid.jpg', 720, 540, 5678900, 'jpg', 160, 120, 'local');

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

(1,'mediaalbum','homePage',0),
(1,'photo','homePage',1),
(1,'user','homePage',1),
(1,'video','homePage',1),

(4,'mediaalbum','homePage',1),
(5,'mediaalbum','homePage',0),

(7,'mediaalbum','homePage',0),
(7,'photo','homePage',1),
(7,'user','homePage',1),
(7,'video','homePage',1);

insert  into `users`(`userId`,`username`,`password`,`email`,`status`,`date`,`lastLogin`,`lang`,`firstName`,`lastName`,`city`,`activationKey`,`newPassword`,`avatar`) values
(1,'plainuser','25f9e794323b453885f5181f1b624d0b','user1@example.org','member','2011-01-01 21:23:00','2011-08-14 08:12:20','fr','prenom','nom','toulouse',NULL,NULL,'/media/avatars/1.jpg'),
(3,'banneduser','25f9e794323b453885f5181f1b624d0b','banned@mountainboard.fr','banned','2002-02-18 14:00:00','2010-01-11 09:04:37','fr','','','',NULL,NULL,NULL),
(4,'adminuser','25f9e794323b453885f5181f1b624d0b','admin@mountainboard.fr','admin','2002-02-01 09:00:00','2010-01-11 09:04:37','en','','','oakland',NULL,NULL,NULL),
(5,'editoruser','25f9e794323b453885f5181f1b624d0b','editor@mountainboard.fr','editor','2011-03-05 07:55:36','2010-01-11 09:04:37','en','','','madrid',NULL,NULL,NULL),
(6,'writeruser','25f9e794323b453885f5181f1b624d0b','writer@mountainboard.fr','writer','2011-06-15 05:55:23','2010-01-11 09:04:37','fr','','',NULL,NULL,NULL,NULL),
(7,'otheruser','25f9e794323b453885f5181f1b624d0b','member@mountainboard.fr','member','2011-02-08 15:56:44','2010-01-11 09:04:37','en','','',NULL,NULL,NULL,NULL),
(8,'pendinguser','25f9e794323b453885f5181f1b624d0b','pending@mountainboard.fr','pending','2002-02-18 14:00:00','2010-01-11 09:04:37','fr','','','','art85dnh2obrozxtqo830shfcmsp4acl',NULL,NULL),
(9,'guest','empty','','guest',NULL,'2002-02-18 14:00:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/* Can't insert a user with and id of 0, so we have to resort to this: */
update `users` SET userId = 0 WHERE userId = 9;
