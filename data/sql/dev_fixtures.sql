TRUNCATE images;
TRUNCATE items;
TRUNCATE media_album_aggregations;
TRUNCATE media_albums;
TRUNCATE media_albums_items;
TRUNCATE media_items;
TRUNCATE media_items_users;
TRUNCATE translated_texts;
TRUNCATE user_notifications;
TRUNCATE users;


insert into `items`(`id`,`itemId`,`itemType`,`date`,`status`,`parentItemId`,`submitter`,`notification`) values
(1,'a3833b1c-1db0-4a93-9efc-b6659400ce9f','mediaalbum','2019-05-26 14:00:00','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent'),
(7,'f2d94b87-3c94-4bd1-9bdd-950393ac1aa5','mediaalbum','2019-05-26 14:00:00','valid',NULL,'60bfb8a7-5754-4186-acd2-44b20ef32399','silent');

insert into `media_album_aggregations`(`id`,`keyName`,`keyValue`,`albumId`) values
(4,'user','60bfb8a7-5754-4186-acd2-44b20ef32399','f2d94b87-3c94-4bd1-9bdd-950393ac1aa5');

insert into `media_albums`(`id`,`date`,`submitter`,`lastEditionDate`,`lastEditor`,`status`,`albumType`,`albumContributions`,`albumCreation`,`albumVisibility`) values
('a3833b1c-1db0-4a93-9efc-b6659400ce9f', '2019-05-26 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static','visible'),
('f2d94b87-3c94-4bd1-9bdd-950393ac1aa5', '2019-05-26 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','aggregate','public','automatic','visible');

insert into `translated_texts`(`id`,`itemType`,`lang`,`type`,`text`) values
('a3833b1c-1db0-4a93-9efc-b6659400ce9f','mediaalbum','en','title','Gallery'),
('a3833b1c-1db0-4a93-9efc-b6659400ce9f','mediaalbum','en','description','Rider photos and videos'),
('f2d94b87-3c94-4bd1-9bdd-950393ac1aa5','mediaalbum','en','title','Surfing Dirt\'s Gallery'),
('f2d94b87-3c94-4bd1-9bdd-950393ac1aa5','mediaalbum','en','description','Surfing Dirt\'s Gallery of wonders'),
('a3833b1c-1db0-4a93-9efc-b6659400ce9f','mediaalbum','fr','title','Galerie'),
('a3833b1c-1db0-4a93-9efc-b6659400ce9f','mediaalbum','fr','description','Les photos et vidéos des riders'),
('a5682d96-fc1b-4b76-b306-485631a5f26d','mediaalbum','en','title','Photos and videos of Surfing Dirt'),
('a5682d96-fc1b-4b76-b306-485631a5f26d','mediaalbum','en','description','Where Surfing Dirt was tagged');

insert into `users`(`userId`,`username`,`password`,`email`,`status`,`date`,`lastLogin`,`lang`,`firstName`,`lastName`,`city`,`activationKey`,`newPassword`,`avatar`) values
('0230ec1d-dc7b-42e6-89d3-3707ee5ade71','guest','empty','','guest',NULL,'2011-01-01 00:00:08',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
('60bfb8a7-5754-4186-acd2-44b20ef32399','Surfing Dirt','25f9e794323b453885f5181f1b624d0b','info@surfingdirt.com','admin','2019-05-26 14:00:00','2019-05-26 14:00:00','en','','','',NULL,NULL,NULL);

