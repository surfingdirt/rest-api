TRUNCATE images;
TRUNCATE items;
TRUNCATE media_album_aggregations;
TRUNCATE media_albums;
TRUNCATE media_albums_items;
TRUNCATE media_items;
TRUNCATE media_items_riders;
TRUNCATE translated_texts;
TRUNCATE user_notifications;
TRUNCATE users;


insert into `images`(`id`,`imageType`,`storageType`, `status`, `submitter`, `date`, `lastEditionDate`, `lastEditor`, `width`, `height`) values
('44130f1c-2931-451a-b6cd-0f49d0b3ad85', 0, 0, 'valid', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-18 10:00:00', null, null, 720, 540),
('84846b0b-254e-472b-94a4-2ecd6bc4cd5e', 0, 0, 'valid', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:01', null, null, 720, 540),
('66059f80-59d4-4237-8602-fbbf17f26616', 0, 0, 'valid', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-20 10:00:03', null, null, 720, 540),
('a9c8081c-43ec-418a-acc2-1ad575672250', 0, 0, 'valid', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:04', null, null, 720, 540),
('b5c1429b-f492-4d71-892d-38dd33deffda', 0, 0, 'invalid', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:05', null, null, 720, 540),
('325edced-b174-4479-8d6f-fea7a4d5e84b', 0, 0, 'valid', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:07', null, null, 720, 540),
('00e08c00-42a1-474c-a0a1-ae0c2dee68f8', 0, 0, 'valid', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:07', null, null, 720, 540),
('bc560af4-8039-43f7-8f07-2c22c53e1954', 0, 0, 'valid', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:07', null, null, 720, 540);

insert  into `items`(`id`,`itemId`,`itemType`,`date`,`status`,`parentItemId`,`submitter`,`notification`) values
(1,'a3833b1c-1db0-4a93-9efc-b6659400ce9f','mediaalbum','2002-02-18 14:00:00','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent'),
(2,'40763a20-5aa0-49d2-85f7-292c95cb3643','mediaalbum','2002-02-18 14:00:01','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent'),
(3,'40c38ab9-cb77-49a7-a296-0805237d2710','mediaalbum','2002-02-18 14:00:02','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent'),
(4,'ab2121cb-03d6-45de-a1ba-4581dd00d79f','mediaalbum','2002-02-18 14:00:03','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent'),
(5,'f2aa61fc-bbc5-45b2-bada-10bff101d957','mediaalbum','2002-02-18 14:00:04','valid',NULL,'85193083-ce22-43a5-993b-1c7aba53d13c','silent'),
(6,'9fb7e4d7-fc38-43b1-a890-4d0872874b5b','mediaalbum','2002-02-18 14:00:05','valid',NULL,'b1786ac1-5cc8-4156-8471-8a80a87efe17','silent'),
(7,'a5682d96-fc1b-4b76-b306-485631a5f26d','mediaalbum','2002-02-18 14:00:06','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent'),
(8,'6b34bbdd-780d-4c2a-adba-c4229a161136','mediaalbum','2002-02-18 14:00:07','valid',NULL,'102e6ed9-cdac-4c9c-9483-a3309970db59','silent'),
(9,'0a8aca42-cd0e-4fe7-8c55-9495f2e95164','mediaalbum','2002-02-18 14:00:08','valid',NULL,'6750ff62-7195-49f5-bf81-e3d395e6cdcf','silent'),
(10,'ac1a8df7-e15b-470e-a2bb-b7fb8187270f','mediaalbum','2002-02-18 14:00:09','valid',NULL,'941b2ac5-2519-44de-84f9-ea9980e4631f','silent'),
(11,'76d513e3-3879-48d7-8f5f-2e8afc36a647','mediaalbum','2002-02-18 14:00:10','valid',NULL,'cc834ce6-58df-4381-aed3-8fe4c2923434','silent'),
(12,'feba0696-8954-4596-a849-0087cbe8ea76','mediaalbum','2002-02-18 14:00:11','valid',NULL,'85193083-ce22-43a5-993b-1c7aba53d13c','silent'),

(13,'44aa386d-3171-432b-a648-f40929043758','photo','2002-02-18 10:00:00','valid',NULL,'6750ff62-7195-49f5-bf81-e3d395e6cdcf','announce'),
(14,'9b523108-9b34-43ea-916d-874ff6013021','photo','2002-02-19 10:00:00','valid',NULL,'6750ff62-7195-49f5-bf81-e3d395e6cdcf','announce'),
(15,'6af68e15-276b-42d7-8683-909943b0ae27','video','2002-02-20 10:00:00','valid',NULL,'85193083-ce22-43a5-993b-1c7aba53d13c','announce'),
(16,'e4e4cb62-4820-4cff-baff-74b2bdcb1993','photo','2002-02-19 10:00:00','valid',NULL,'85193083-ce22-43a5-993b-1c7aba53d13c','announce'),
(17,'9119c477-1c48-4449-9b8e-9cdb745fc912','photo','2002-02-19 10:00:00','valid',NULL,'6750ff62-7195-49f5-bf81-e3d395e6cdcf','announce'),
(18,'051cae3a-95f7-4a29-996d-796d4c263a1e','photo','2002-02-19 10:00:00','invalid',NULL,'85193083-ce22-43a5-993b-1c7aba53d13c','announce');

insert  into `media_album_aggregations`(`id`,`keyName`,`keyValue`,`albumId`) values
(1,'user','85193083-ce22-43a5-993b-1c7aba53d13c','ab2121cb-03d6-45de-a1ba-4581dd00d79f'),
(2,'user','b1786ac1-5cc8-4156-8471-8a80a87efe17','f2aa61fc-bbc5-45b2-bada-10bff101d957');

insert  into `media_albums`(`id`,`date`,`submitter`,`lastEditionDate`,`lastEditor`,`status`,`albumType`,`albumAccess`,`albumCreation`) values
('a3833b1c-1db0-4a93-9efc-b6659400ce9f', '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static'),
('40763a20-5aa0-49d2-85f7-292c95cb3643', '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static'),
('40c38ab9-cb77-49a7-a296-0805237d2710', '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static'),
('ab2121cb-03d6-45de-a1ba-4581dd00d79f', '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static'),
('89602268-929a-4018-8341-9b96ccddf9c8', '2002-02-18 14:00:00','102e6ed9-cdac-4c9c-9483-a3309970db59',NULL,NULL,'valid','simple','public','static'),
('7c719bac-998d-4b15-8b52-2321078e887c', '2002-02-18 14:00:00','85193083-ce22-43a5-993b-1c7aba53d13c',NULL,NULL,'valid','simple','public','static'),
('f2aa61fc-bbc5-45b2-bada-10bff101d957', '2002-02-18 14:00:00','85193083-ce22-43a5-993b-1c7aba53d13c',NULL,NULL,'valid','aggregate','public','automatic'),
('9fb7e4d7-fc38-43b1-a890-4d0872874b5b', '2002-02-18 14:00:00','b1786ac1-5cc8-4156-8471-8a80a87efe17',NULL,NULL,'valid','aggregate','public','automatic'),
('a5682d96-fc1b-4b76-b306-485631a5f26d', '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','aggregate','public','automatic'),
('6b34bbdd-780d-4c2a-adba-c4229a161136', '2002-02-18 14:00:00','102e6ed9-cdac-4c9c-9483-a3309970db59',NULL,NULL,'valid','aggregate','public','automatic'),
('0a8aca42-cd0e-4fe7-8c55-9495f2e95164', '2002-02-18 14:00:00','6750ff62-7195-49f5-bf81-e3d395e6cdcf',NULL,NULL,'valid','aggregate','public','automatic'),
('ac1a8df7-e15b-470e-a2bb-b7fb8187270f','2002-02-18 14:00:00','941b2ac5-2519-44de-84f9-ea9980e4631f',NULL,NULL,'valid','aggregate','public','automatic'),
('76d513e3-3879-48d7-8f5f-2e8afc36a647','2002-02-18 14:00:00','cc834ce6-58df-4381-aed3-8fe4c2923434',NULL,NULL,'valid','aggregate','public','automatic'),
('feba0696-8954-4596-a849-0087cbe8ea76','2002-02-18 14:00:00','85193083-ce22-43a5-993b-1c7aba53d13c',NULL,NULL,'valid','simple','public','static');

insert into `media_items`(`id`,`submitter`,`date`,`lastEditionDate`,`lastEditor`, `status`, `albumId`, `mediaType`, `imageId`, `vendorKey`, `width`, `height`, `mediaSubType`, `storageType`) VALUES
('44aa386d-3171-432b-a648-f40929043758', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-18 10:00:00', null, null, 'valid', 'a3833b1c-1db0-4a93-9efc-b6659400ce9f',  'photo', '44130f1c-2931-451a-b6cd-0f49d0b3ad85', null, 720, 540, 'jpg', 0),
('9b523108-9b34-43ea-916d-874ff6013021', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:01', null, null, 'valid', 'a3833b1c-1db0-4a93-9efc-b6659400ce9f',  'photo', '84846b0b-254e-472b-94a4-2ecd6bc4cd5e', null, 720, 540, 'jpg', 0),
('e4e4cb62-4820-4cff-baff-74b2bdcb1993', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:03', null, null, 'valid', 'a3833b1c-1db0-4a93-9efc-b6659400ce9f',  'photo', 'a9c8081c-43ec-418a-acc2-1ad575672250', null, 720, 540, 'jpg', 0),
('9119c477-1c48-4449-9b8e-9cdb745fc912', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:04', null, null, 'valid', 'a3833b1c-1db0-4a93-9efc-b6659400ce9f',  'photo', '66059f80-59d4-4237-8602-fbbf17f26616', null, 720, 540, 'jpg', 0),
('051cae3a-95f7-4a29-996d-796d4c263a1e', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:05', null, null, 'invalid', 'a3833b1c-1db0-4a93-9efc-b6659400ce9f',  'photo', 'b5c1429b-f492-4d71-892d-38dd33deffda', null, 720, 540, 'jpg', 0),
('6af68e15-276b-42d7-8683-909943b0ae27', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-20 10:00:06', null, null, 'valid', 'feba0696-8954-4596-a849-0087cbe8ea76', 'video', '', 'myvideo', 720, 540, 'youtube', 0);

insert  into `translated_texts`(`id`,`itemType`,`lang`,`type`,`text`) values
('a3833b1c-1db0-4a93-9efc-b6659400ce9f','mediaalbum','fr','title','photoAlbumTitleFr'),
('a3833b1c-1db0-4a93-9efc-b6659400ce9f','mediaalbum','fr','description','photoAlbumDescFr'),
('a3833b1c-1db0-4a93-9efc-b6659400ce9f','mediaalbum','en','title','photoAlbumTitleEn'),
('a3833b1c-1db0-4a93-9efc-b6659400ce9f','mediaalbum','en','description','photoAlbumDescEn'),

('40763a20-5aa0-49d2-85f7-292c95cb3643','mediaalbum','fr','title','videoAlbumTitleFr'),
('40763a20-5aa0-49d2-85f7-292c95cb3643','mediaalbum','fr','description','videoAlbumDescFr'),
('40763a20-5aa0-49d2-85f7-292c95cb3643','mediaalbum','en','title','videoAlbumTitleEn'),
('40763a20-5aa0-49d2-85f7-292c95cb3643','mediaalbum','en','description','videoAlbumDescEn'),

('40c38ab9-cb77-49a7-a296-0805237d2710','mediaalbum','fr','title','portfolioTitleFr'),
('40c38ab9-cb77-49a7-a296-0805237d2710','mediaalbum','fr','description','portfolioDescFr'),
('40c38ab9-cb77-49a7-a296-0805237d2710','mediaalbum','en','title','portfolioTitleEn'),
('40c38ab9-cb77-49a7-a296-0805237d2710','mediaalbum','en','description','portfolioDescEn'),

('ab2121cb-03d6-45de-a1ba-4581dd00d79f','mediaalbum','en','title','dummyAlbumTitle'),
('ab2121cb-03d6-45de-a1ba-4581dd00d79f','mediaalbum','en','description','dummyAlbumDescription'),

('f2aa61fc-bbc5-45b2-bada-10bff101d957','mediaalbum','en','title','album de plainUserAlbumTitle'),
('f2aa61fc-bbc5-45b2-bada-10bff101d957','mediaalbum','en','description','plainUserAlbumDescription'),

('9fb7e4d7-fc38-43b1-a890-4d0872874b5b','mediaalbum','en','title','album de bannedUserAlbumTitle'),
('9fb7e4d7-fc38-43b1-a890-4d0872874b5b','mediaalbum','en','description','bannedUserAlbumDescription'),

('a5682d96-fc1b-4b76-b306-485631a5f26d','mediaalbum','en','title','album de adminUserAlbumTitle'),
('a5682d96-fc1b-4b76-b306-485631a5f26d','mediaalbum','en','description','adminUserAlbumDescription'),

('6b34bbdd-780d-4c2a-adba-c4229a161136','mediaalbum','en','title','album de editorUserAlbumTitle'),
('6b34bbdd-780d-4c2a-adba-c4229a161136','mediaalbum','en','description','editorUserAlbumDescription'),

('0a8aca42-cd0e-4fe7-8c55-9495f2e95164','mediaalbum','en','title','album de writerUserAlbumTitle'),
('0a8aca42-cd0e-4fe7-8c55-9495f2e95164','mediaalbum','en','description','writerUserAlbumDescription'),

('ac1a8df7-e15b-470e-a2bb-b7fb8187270f','mediaalbum','en','title','album de otherUserAlbumTitle'),
('ac1a8df7-e15b-470e-a2bb-b7fb8187270f','mediaalbum','en','description','otherUserAlbumDescription'),

('76d513e3-3879-48d7-8f5f-2e8afc36a647','mediaalbum','en','title','pendinguserAlbumTitle'),
('76d513e3-3879-48d7-8f5f-2e8afc36a647','mediaalbum','en','description','pendinguserAlbumDescription'),

('feba0696-8954-4596-a849-0087cbe8ea76','mediaalbum','en','title','nonEmptyPlainUserAlbumTitle'),
('feba0696-8954-4596-a849-0087cbe8ea76','mediaalbum','en','description','nonEmptyPlainUserAlbumDescription'),

('44aa386d-3171-432b-a648-f40929043758','photo','en','title','firstPhotoTitle'),
('44aa386d-3171-432b-a648-f40929043758','photo','en','description','firstPhotoDescription'),

('9b523108-9b34-43ea-916d-874ff6013021','photo','en','title','secondPhotoTitle'),
('9b523108-9b34-43ea-916d-874ff6013021','photo','en','description','secondPhotoDescription'),

('6af68e15-276b-42d7-8683-909943b0ae27','video','en','title','firstVideoTitle'),
('6af68e15-276b-42d7-8683-909943b0ae27','video','en','description','firstVideoDescription'),

('e4e4cb62-4820-4cff-baff-74b2bdcb1993','photo','en','title','thirdPhotoTitle'),
('e4e4cb62-4820-4cff-baff-74b2bdcb1993','photo','en','description','thirdPhotoDescription'),

('9119c477-1c48-4449-9b8e-9cdb745fc912','photo','en','title','fourthPhotoTitle'),
('9119c477-1c48-4449-9b8e-9cdb745fc912','photo','en','description','fourthPhotoDescription'),

('051cae3a-95f7-4a29-996d-796d4c263a1e','photo','en','title','plainUserPhotoTitle'),
('051cae3a-95f7-4a29-996d-796d4c263a1e','photo','en','description','plainUserPhotoDescription');

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

