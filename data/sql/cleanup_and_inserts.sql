/*
SQLyog Enterprise - MySQL GUI v8.12 
MySQL - 5.1.36-community : Database - ridedb_prod
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

TRUNCATE items;
TRUNCATE media_album_aggregations;
TRUNCATE media_albums;
TRUNCATE media_albums_items;
TRUNCATE media_items;
TRUNCATE media_items_riders;
TRUNCATE translated_texts;
TRUNCATE user_notifications;
TRUNCATE users;


/*Data for the table `items` */

insert  into `items`(`id`,`itemId`,`itemType`,`date`,`status`,`parentItemId`,`submitter`,`notification`) values
(1,1,'mediaalbum','2002-02-18 14:00:00','valid',NULL,1,'silent'),
(2,2,'mediaalbum','2002-02-18 14:00:00','valid',NULL,1,'silent'),
(3,3,'mediaalbum','2002-02-18 14:00:00','valid',NULL,1,'silent'),
(4,4,'mediaalbum','2002-02-18 14:00:00','valid',NULL,1,'silent');

/*Data for the table `media_album_aggregations` */

insert  into `media_album_aggregations`(`id`,`keyName`,`keyValue`,`albumId`) values
(1,'user',1,4),
(2,'user',3,5);

/*Data for the table `media_albums` */

insert  into `media_albums`(`id`,`date`,`submitter`,`lastEditionDate`,`lastEditor`,`status`,`albumType`,`albumAccess`,`albumCreation`,`spot`) values
(1,'2002-02-18 14:00:00',1,NULL,NULL,'valid','simple','public','static',NULL),
(2,'2002-02-18 14:00:00',1,NULL,NULL,'valid','simple','public','static',NULL),
(3,'2002-02-18 14:00:00',1,NULL,NULL,'valid','simple','public','static',NULL),
(4,'2002-02-18 14:00:00',1,NULL,NULL,'valid','aggregate','public','automatic',NULL),
(5,'2002-02-18 14:00:00',3,NULL,NULL,'valid','aggregate','public','automatic',NULL);

/*Data for the table `translated_texts` */

insert  into `translated_texts`(`id`,`itemType`,`lang`,`type`,`text`) values
(1,'mediaalbum','fr','title','galerie photo'),
(1,'mediaalbum','fr','description','les photos postées par les membres'),
(1,'mediaalbum','en','title','photo gallery'),
(1,'mediaalbum','en','description','user-submitted photos'),
(2,'mediaalbum','fr','title','galerie vidéo'),
(2,'mediaalbum','fr','description','les vidéos postées par les membres'),
(2,'mediaalbum','en','title','video gallery'),
(2,'mediaalbum','en','description','user-submitted videos'),
(3,'mediaalbum','fr','title','portfolio'),
(3,'mediaalbum','fr','description','les plus belles photos de mountainboard'),
(3,'mediaalbum','en','title','portfolio'),
(3,'mediaalbum','en','description','the most beautiful mountainboard pictures'),
(4,'mediaalbum','fr','title','album de mikael'),
(4,'mediaalbum','fr','description','album de mikael');

/*Data for the table `user_notifications` */

insert  into `user_notifications`(`userId`,`itemType`,`medium`,`notify`) values
(1,'mediaalbum','homePage',0),
(1,'photo','homePage',1),
(1,'user','homePage',0),
(1,'video','homePage',1);

/*Data for the table `users` */

insert  into `users`(`userId`,`username`,`password`,`email`,`status`,`date`,`lastLogin`,`lang`,`firstName`,`lastName`,`city`,`activationKey`,`newPassword`,`avatar`)
values (1,'mikael','fba814834202ddc5f74554a06ae72b34','mikael@mountainboard.fr','admin','2002-02-18 14:00:00','2010-01-11 09:04:37','fr','mikaël','gramont','toulouse',NULL,NULL,NULL),
(3,'banned','aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','banned@example.org','banned','2002-02-18 14:00:00','2010-01-11 09:04:37','fr','','','',NULL,NULL,NULL),
(0,'guest','empty','','guest',NULL,'2002-02-18 14:00:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
