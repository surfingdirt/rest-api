/*
SQLyog Enterprise - MySQL GUI v8.12 
MySQL - 5.1.36-community : Database - ridedb_prod
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
use ridedb_prod;

TRUNCATE blog_links;
TRUNCATE blog_posts;
TRUNCATE blogs;
TRUNCATE comments;
TRUNCATE dossiers;
TRUNCATE dpt;
TRUNCATE forum_access;
TRUNCATE forum_posts;
TRUNCATE forum_topics;
TRUNCATE forums;
TRUNCATE imports;
TRUNCATE items;
TRUNCATE locations;
TRUNCATE media_album_aggregations;
TRUNCATE media_albums;
TRUNCATE media_albums_items;
TRUNCATE media_items;
TRUNCATE media_items_riders;
TRUNCATE news;
TRUNCATE privatemessages;
TRUNCATE spots;
TRUNCATE tags;
TRUNCATE tests;
TRUNCATE translated_texts;
TRUNCATE tricks;
TRUNCATE user_notifications;
TRUNCATE users;


/*Data for the table `blog_links` */

/*Data for the table `blog_posts` */

/*Data for the table `blogs` */

/*Data for the table `comments` */

/*Data for the table `dossiers` */

/*Data for the table `dpt` */

insert  into `dpt`(`id`,`title`,`prefix`,`status`,`location`,`simpleTitle`) values (1,'Ain','de l\'','valid',1,'ain'),(2,'Aisne','de l\'','valid',2,'aisne'),(3,'Allier','de l\'','valid',3,'allier'),(4,'Haute-Provence','de la','valid',4,'hauteprovence'),(5,'Hautes-Alpes','des','valid',5,'hautesalpes'),(6,'Alpes Maritimes','des','valid',6,'alpesmaritimes'),(7,'Ardennes','des','valid',7,'ardennes'),(8,'Ardennes','des','valid',8,'ardennes'),(9,'Ariège','de l\'','valid',9,'ariege'),(10,'Aube','de l\'','valid',10,'aube'),(11,'Aude','de l\'','valid',11,'aude'),(12,'Aveyron','de l\'','valid',12,'aveyron'),(13,'Bouches du Rhône','des','valid',13,'bouchesdurhone'),(14,'Calvados','du ','valid',14,'calvados'),(15,'Cantal','du ','valid',15,'cantal'),(16,'Charentes','des ','valid',16,'charentes'),(17,'Charentes Maritimes','des ','valid',17,'charentesmaritimes'),(18,'Cher','du ','valid',18,'cher'),(19,'Corrèze','de ','valid',19,'correze'),(20,'Corse','de ','valid',20,'corse'),(21,'Côtes d\'or','des ','valid',21,'cotesdor'),(22,'Côtes d\'Armor','des ','valid',22,'cotesdarmor'),(23,'Creuse','de la ','valid',23,'creuse'),(24,'Dordogne','de la ','valid',24,'dordogne'),(25,'Doubs','du ','valid',25,'doubs'),(26,'Drome','de la ','valid',26,'drome'),(27,'Eure','de l\'','valid',27,'eure'),(28,'Eure-et-Loir','de l\'','valid',28,'eureetloir'),(29,'Finistère','du ','valid',29,'finistere'),(30,'Gard','du ','valid',30,'gard'),(31,'Haute-Garonne','de la ','valid',31,'hautegaronne'),(32,'Gers','du ','valid',32,'gers'),(33,'Gironde','de la ','valid',33,'gironde'),(34,'Hérault','de l\'','valid',34,'herault'),(35,'Ille-et-Vilaine','de l\'','valid',35,'illeetvilaine'),(36,'Indres','de l\'','valid',36,'indres'),(37,'Indre-et-Loire','de l\'','valid',37,'indreetloire'),(38,'Isere','de l\'','valid',38,'isere'),(39,'Jura','du ','valid',39,'jura'),(40,'Landes','des ','valid',40,'landes'),(41,'Loir-et-Cher','du ','valid',41,'loiretcher'),(42,'Loire','de la ','valid',42,'loire'),(43,'Haute-Loire','de la ','valid',43,'hauteloire'),(44,'Loire-Atlantique','de ','valid',44,'loireatlantique'),(45,'Loiret','du ','valid',45,'loiret'),(46,'Lot','du ','valid',46,'lot'),(47,'Lot et Garonne','du ','valid',47,'lotetgaronne'),(48,'Lozère','de la ','valid',48,'lozere'),(49,'Maine et Loire','du ','valid',49,'maineetloire'),(50,'Manche','de la ','valid',50,'manche'),(51,'Marne','de la ','valid',51,'marne'),(52,'Haute-Marne','de la ','valid',52,'hautemarne'),(53,'Mayenne','de la ','valid',53,'mayenne'),(54,'Meurthe et Moselle','de ','valid',54,'meurtheetmoselle'),(55,'Meuse','de la ','valid',55,'meuse'),(56,'Morbihan','du ','valid',56,'morbihan'),(57,'Moselle','de la','valid',57,'moselle'),(58,'Nièvre','de la ','valid',58,'nievre'),(59,'Nord','du ','valid',59,'nord'),(60,'Oise','de l\'','valid',60,'oise'),(61,'Orne','de l\'','valid',61,'orne'),(62,'Pas-de-Calais','du ','valid',62,'pasdecalais'),(63,'Puy-de-Dôme','du ','valid',63,'puydedome'),(64,'Pyrénées-Atlantiques','des ','valid',64,'pyreneesatlantiques'),(65,'Hautes-Pyrénées','des ','valid',65,'hautespyrenees'),(66,'Pyrénées Orientales','des ','valid',66,'pyreneesorientales'),(67,'Bas Rhin','du ','valid',67,'basrhin'),(68,'Haut Rhin','du ','valid',68,'hautrhin'),(69,'Rhône','du ','valid',69,'rhone'),(70,'Haute Saône','de la ','valid',70,'hautesaone'),(71,'Saône et Loire','de la ','valid',71,'saoneetloire'),(72,'Sarthe','de la ','valid',72,'sarthe'),(73,'Savoie','de la ','valid',73,'savoie'),(74,'Haute-Savoie','de ','valid',74,'hautesavoie'),(75,'Paris','de ','valid',75,'paris'),(76,'Seine Maritime','de ','valid',76,'seinemaritime'),(77,'Seine et Marne','de la ','valid',77,'seineetmarne'),(78,'Yvelines','des ','valid',78,'yvelines'),(79,'Deux Sèvres','des ','valid',79,'deuxsevres'),(80,'Somme','de la ','valid',80,'somme'),(81,'Tarn','du ','valid',81,'tarn'),(82,'Tarn et Garonne','du ','valid',82,'tarnetgaronne'),(83,'Var','du ','valid',83,'var'),(84,'Vaucluse','du ','valid',84,'vaucluse'),(85,'Vendée','de ','valid',85,'vendee'),(86,'Vienne','de la ','valid',86,'vienne'),(87,'Haute-Vienne','de la ','valid',87,'hautevienne'),(88,'Vosges','des ','valid',88,'vosges'),(89,'Yonne','de l\'','valid',89,'yonne'),(90,'Territoire de Belfort','du ','valid',90,'territoiredebelfort'),(91,'Essone','de l\'','valid',91,'essone'),(92,'Hauts de Seine','des ','valid',92,'hautsdeseine'),(93,'Seine Saint-Denis','de la ','valid',93,'seinesaintdenis'),(94,'Val de Marne','du ','valid',94,'valdemarne'),(95,'Val d\'Oise','du ','valid',95,'valdoise');

/*Data for the table `forum_access` */

/*Data for the table `forum_posts` */

/*Data for the table `forum_topics` */

/*Data for the table `forums` */

insert  into `forums`(`id`,`title`,`description`,`category`,`privacy`,`status`,`lastPoster`,`lastPostDate`,`submitter`,`lastEditor`,`lastEditionDate`,`topics`) values (2,'Chat','Le mountainboard et le reste...',1,'public','valid',1,'2009-12-31 13:11:45',0,1,'2009-12-31 13:11:45',1),(3,'AMTB','Tout ce qui concerne l\\\'AMTB...',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(4,'Coulisses','Les dessous de mountainboard.fr',1,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(5,'International','Posts in English only, please !',1,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(6,'Projets','Racontez ici vos expériences...',1,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(23,'CleanSlide','Le forum de l\\\'asso...',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(13,'Ragnagna','Le Forum des Auvergnats',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(8,'HxcMTB33','Le forum des Bordelais',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(9,'Hot Locos','Le forum des 64-65 (Pyrénées)',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(10,'BreizhMTB','Le forum des Bretons',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(11,'Viking Team','Le forum des Vikings',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(12,'TMC31','Le forum des Toulousains',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(14,'Burnin\' Marmottes','Le Forum des Grenoblois.',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(15,'HENT MTB ARE','Asso du Finistère',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(16,'Go, go !','Le forum des parigots...',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(17,'DVD 2004','Forum privé',1,'private','valid',25,'2009-05-24 16:16:53',0,25,'2009-05-24 16:16:53',NULL),(18,'A.M.C','Le forum des compiegnois.',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(19,'Creuse ton Crew','Le forum des charentais...',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(20,'Le site','bugs, améliorations...',1,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(21,'Videos du Net','sans commentaire !',1,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(22,'VIP','Le forum des G.O. (Gentils organisateurs)',1,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(24,'Poubelle','Les topics qui ne méritent pas de vivre.',1,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(25,'DVD 2006','Sujets à propos du DVD 2006',1,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(26,'Chti','Le forum des gens de là haut.',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(27,'RastaRockets','Le forum des Alpes Maritimes...',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(28,'Mtb dans le 34','Montpellier et ses environs',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(29,'Gnon-gnon','Le forum des bourguignons',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(30,'Naranoriders','Le forum de l\'asso Naranoriders',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL),(31,'1-K','Le forum des Charentes',2,'public','valid',0,'0000-00-00 00:00:00',0,NULL,NULL,NULL);

/*Data for the table `imports` */

insert  into `imports`(`id`,`itemId`,`itemType`,`url`,`oldId`,`oldItemType`,`oldUrl`) values (1,2,'forum','/forum/chat_2/page-1/',2,'forums','/forum2.html'),(2,4,'forum','/forum/coulisses_4/page-1/',4,'forums','/forum4.html'),(3,5,'forum','/forum/international_5/page-1/',5,'forums','/forum5.html'),(4,6,'forum','/forum/projets_6/page-1/',6,'forums','/forum6.html'),(5,17,'forum','/forum/dvd-2004_17/page-1/',17,'forums','/forum17.html'),(6,20,'forum','/forum/le-site_20/page-1/',20,'forums','/forum20.html'),(7,21,'forum','/forum/videos-du-net_21/page-1/',21,'forums','/forum21.html'),(8,22,'forum','/forum/vip_22/page-1/',22,'forums','/forum22.html'),(9,24,'forum','/forum/poubelle_24/page-1/',24,'forums','/forum24.html'),(10,3,'forum','/forum/amtb_3/page-1/',3,'forums','/forum3.html'),(11,8,'forum','/forum/hxcmtb33_8/page-1/',8,'forums','/forum8.html'),(12,9,'forum','/forum/hot-locos_9/page-1/',9,'forums','/forum9.html'),(13,10,'forum','/forum/breizhmtb_10/page-1/',10,'forums','/forum10.html'),(14,11,'forum','/forum/viking-team_11/page-1/',11,'forums','/forum11.html'),(15,12,'forum','/forum/tmc31_12/page-1/',12,'forums','/forum12.html'),(16,13,'forum','/forum/ragnagna_13/page-1/',13,'forums','/forum13.html'),(17,14,'forum','/forum/burnin-marmottes_14/page-1/',14,'forums','/forum14.html'),(18,15,'forum','/forum/hent-mtb-are_15/page-1/',15,'forums','/forum15.html'),(19,16,'forum','/forum/go-go_16/page-1/',16,'forums','/forum16.html'),(20,18,'forum','/forum/a-m-c_18/page-1/',18,'forums','/forum18.html'),(21,19,'forum','/forum/creuse-ton-crew_19/page-1/',19,'forums','/forum19.html'),(22,23,'forum','/forum/cleanslide_23/page-1/',23,'forums','/forum23.html'),(23,26,'forum','/forum/chti_26/page-1/',26,'forums','/forum26.html'),(24,27,'forum','/forum/rastarockets_27/page-1/',27,'forums','/forum27.html'),(25,28,'forum','/forum/mtb-dans-le-34_28/page-1/',28,'forums','/forum28.html'),(26,29,'forum','/forum/gnon-gnon_29/page-1/',29,'forums','/forum29.html'),(27,30,'forum','/forum/naranoriders_30/page-1/',30,'forums','/forum30.html'),(28,31,'forum','/forum/1-k_31/page-1/',31,'forums','/forum31.html'),(29,25,'forum','/forum/dvd-2006_25/page-1/',25,'forums','/forum25.html'),(30,1,'user','/profil/mikael_1/',1,'users','/profil1.html'),(31,3,'user','/profil/paul_3/',3,'users','/profil3.html');

/*Data for the table `items` */

insert  into `items`(`id`,`itemId`,`itemType`,`date`,`status`,`parentItemId`,`submitter`,`notification`) values (1,1,'mediaalbum','2002-02-18 14:00:00','valid',NULL,1,'silent'),(2,2,'mediaalbum','2002-02-18 14:00:00','valid',NULL,1,'silent'),(3,3,'mediaalbum','2002-02-18 14:00:00','valid',NULL,1,'silent'),(4,4,'mediaalbum','2002-02-18 14:00:00','valid',NULL,1,'silent');

/*Data for the table `locations` */

/*Data for the table `media_album_aggregations` */

insert  into `media_album_aggregations`(`id`,`keyName`,`keyValue`,`albumId`) values (1,'user',1,4),(2,'user',3,5);

/*Data for the table `media_albums` */

insert  into `media_albums`(`id`,`date`,`submitter`,`lastEditionDate`,`lastEditor`,`location`,`status`,`albumType`,`albumAccess`,`albumCreation`,`spot`) values (1,'2002-02-18 14:00:00',1,NULL,NULL,NULL,'valid','simple','public','static',NULL),
(2,'2002-02-18 14:00:00',1,NULL,NULL,NULL,'valid','simple','public','static',NULL),
(3,'2002-02-18 14:00:00',1,NULL,NULL,NULL,'valid','simple','public','static',NULL),
(4,'2002-02-18 14:00:00',1,NULL,NULL,NULL,'valid','aggregate','public','automatic',NULL),
(5,'2002-02-18 14:00:00',3,NULL,NULL,NULL,'valid','aggregate','public','automatic',NULL);

/*Data for the table `media_albums_items` */

/*Data for the table `media_items` */

/*Data for the table `media_items_riders` */

/*Data for the table `news` */

/*Data for the table `privatemessages` */

/*Data for the table `spots` */

/*Data for the table `tags` */

/*Data for the table `tests` */

/*Data for the table `translated_texts` */

insert  into `translated_texts`(`id`,`itemType`,`lang`,`type`,`text`) values (1,'mediaalbum','fr','title','galerie photo'),(1,'mediaalbum','fr','description','les photos postées par les membres'),(1,'mediaalbum','en','title','photo gallery'),(1,'mediaalbum','en','description','user-submitted photos'),(2,'mediaalbum','fr','title','galerie vidéo'),(2,'mediaalbum','fr','description','les vidéos postées par les membres'),(2,'mediaalbum','en','title','video gallery'),(2,'mediaalbum','en','description','user-submitted videos'),(3,'mediaalbum','fr','title','portfolio'),(3,'mediaalbum','fr','description','les plus belles photos de mountainboard'),(3,'mediaalbum','en','title','portfolio'),(3,'mediaalbum','en','description','the most beautiful mountainboard pictures'),(4,'mediaalbum','fr','title','album de mikael'),(4,'mediaalbum','fr','description','album de mikael');

/*Data for the table `tricks` */

/*Data for the table `user_notifications` */

insert  into `user_notifications`(`userId`,`itemType`,`medium`,`notify`) values (1,'blogpost','homePage',1),(1,'comment','homePage',1),(1,'dossier','homePage',1),(1,'mediaalbum','homePage',0),(1,'news','homePage',1),(1,'photo','homePage',1),(1,'post','homePage',1),(1,'privatemessage','homePage',1),(1,'topic','homePage',1),(1,'test','homePage',1),(1,'trick','homePage',1),(1,'spot','homePage',0),(1,'user','homePage',0),(1,'video','homePage',1);

/*Data for the table `users` */

insert  into `users`(`userId`,`username`,`password`,`email`,`status`,`date`,`lastLogin`,`openidIdentity`,`lang`,`firstName`,`lastName`,`birthDate`,`country`,`city`,`zip`,`gender`,`level`,`site`,`occupation`,`gear`,`otherSports`,`rideType`,`activationKey`,`newPassword`,`avatar`) values (1,'mikael','fba814834202ddc5f74554a06ae72b34','mikael@mountainboard.fr','admin','2002-02-18 14:00:00','2010-01-11 09:04:37','http://openid-provider.appspot.com/mgramont','fr','mikaël','gramont','1979-12-08','france','toulouse',31000,1,2,'http://www.mountainboard.fr','cadre','pro12','snow','110',NULL,NULL,NULL),
(3,'Paul','aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','banned@example.org','banned','2002-02-18 14:00:00','2010-01-11 09:04:37','','fr','','','','','',0,1,2,'','','','','',NULL,NULL,NULL),
(0,'guest','empty','','guest',NULL,'2002-02-18 14:00:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
