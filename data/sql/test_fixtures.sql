TRUNCATE blog_links;
TRUNCATE blog_posts;
TRUNCATE blogs;
TRUNCATE checkins;
TRUNCATE comments;
TRUNCATE countries;
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

insert into `checkins`(`id`,`submitter`,`spot`,`checkinDate`,`checkinDuration`,`date`,`lastEditionDate`,`lastEditor`,`status`) values
(1,1,1,'2011-08-02 09:00:00',14400,'2011-07-01 09:00:00',null,null,'valid'), /* valid checkin in valid spot country 1, region 0 */
(2,1,3,'2011-08-02 09:00:00',14400,'2011-07-01 09:00:00',null,null,'invalid'), /* invalid checkin on valid spot country 1, region 0 */
(3,5,3,'2011-08-03 11:00:00',14400,'2011-07-01 09:00:00',null,null,'valid'), /* valid checkin in valid spot country 1, region 0 */
(4,4,5,'2011-08-03 11:00:00',14400,'2011-07-01 09:00:00',null,null,'valid'), /* valid checkin in valid spot, country 1, region 1  */
(5,4,5,'2012-01-01 10:00:00',14400,'2012-01-01 09:00:00',null,null,'valid') /* valid checkin in valid spot, country 1, region 1  */
;

insert into `comments`(`id`,`parentId`,`parentType`,`date`,`submitter`,`content`,`lastEditionDate`,`lastEditor`,`status`,`tone`) values
(1,1,'photo','2011-01-01 21:23:00',1,'myFirstComment',null,null,'valid',2),
(2,1,'photo','2011-01-01 21:23:01',1,'mySecondComment',null,null,'invalid',3),
(3,1,'photo','2011-01-01 21:23:02',7,'comment3Title',null,null,'valid',2),
(4,1,'photo','2011-01-01 21:23:03',1,'comment4Title',null,null,'valid',2);

insert into `countries`(`id`,`title`,`lang`,`simpleTitle`,`date`,`submitter`,`lastEditionDate`,`lastEditor`,`status`,`bounds`) values
(1,'France','fr','france','2000-01-01 00:00:00',4,null,null,'valid','42.1331639,-5.982052,50.0380022,10.40955'),
(2,'Spain','en','spain','2000-01-01 00:00:00',4,null,null,'valid','36.073,-10.024,43.644,3.898'),
(3,'New Zealand','en','new-zealand','2000-01-01 00:00:00',4,null,null,'invalid','-45.0769319,166.6901726,-36.4428471,-176.9182306'),
(4,'Japan','en','japan','2000-01-01 00:0:00',4,null,null,'valid','30.4661367,128.4613221,45.1679299,147.0445259');

insert  into `dpt`(`id`,`title`,`prefix`,`status`,`location`,`simpleTitle`, `code`,`country`,`bounds`,`submitter`) values
(1,'Ain','de l\'','valid',1,'ain',        'a',1,'1.2,2.3,3.4,4.5',4),
(2,'Aisne','de l\'','valid',2,'aisne',    'b',2,'2.3,3.4,4.5,5.6',4),
(3,'Allier','de l\'','invalid',3,'allier','c',1,'3.4,4.5,5.6,6.7',4);

insert  into `forums`(`id`,`title`,`description`,`category`,`privacy`,`status`,`lastPoster`,`lastPostDate`,`submitter`,`lastEditor`,`lastEditionDate`,`topics`) values (2,'Chat','Le mountainboard et le reste...',1,'public','valid',1,'2009-12-31 13:11:45',0,1,'2009-12-31 13:11:45',1),(3,'AMTB','Tout ce qui concerne l\\\'AMTB...',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(4,'Coulisses','Les dessous de mountainboard.fr',1,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(5,'International','Posts in English only, please !',1,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(6,'Projets','Racontez ici vos expériences...',1,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(23,'CleanSlide','Le forum de l\\\'asso...',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(13,'Ragnagna','Le Forum des Auvergnats',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(8,'HxcMTB33','Le forum des Bordelais',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(9,'Hot Locos','Le forum des 64-65 (Pyrénées)',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(10,'BreizhMTB','Le forum des Bretons',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(11,'Viking Team','Le forum des Vikings',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(12,'TMC31','Le forum des Toulousains',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(14,'Burnin\' Marmottes','Le Forum des Grenoblois.',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(15,'HENT MTB ARE','Asso du Finistère',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(16,'Go, go !','Le forum des parigots...',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(17,'DVD 2004','Forum privé',1,'private','valid',25,'2009-05-24 16:16:53',0,25,'2009-05-24 16:16:53',NULL),(18,'A.M.C','Le forum des compiegnois.',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(19,'Creuse ton Crew','Le forum des charentais...',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(20,'Le site','bugs, améliorations...',1,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(21,'Videos du Net','sans commentaire !',1,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(22,'VIP','Le forum des G.O. (Gentils organisateurs)',1,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(24,'Poubelle','Les topics qui ne méritent pas de vivre.',1,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(25,'DVD 2006','Sujets à propos du DVD 2006',1,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(26,'Chti','Le forum des gens de là haut.',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(27,'RastaRockets','Le forum des Alpes Maritimes...',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(28,'Mtb dans le 34','Montpellier et ses environs',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(29,'Gnon-gnon','Le forum des bourguignons',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(30,'Naranoriders','Le forum de l\'asso Naranoriders',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL),(31,'1-K','Le forum des Charentes',2,'public','valid',0,'2000-01-01 00:00:00',0,NULL,NULL,NULL);

insert  into `imports`(`id`,`itemId`,`itemType`,`url`,`oldId`,`oldItemType`,`oldUrl`) values (1,2,'forum','/forum/chat_2/page-1/',2,'forums','/forum2.html'),(2,4,'forum','/forum/coulisses_4/page-1/',4,'forums','/forum4.html'),(3,5,'forum','/forum/international_5/page-1/',5,'forums','/forum5.html'),(4,6,'forum','/forum/projets_6/page-1/',6,'forums','/forum6.html'),(5,17,'forum','/forum/dvd-2004_17/page-1/',17,'forums','/forum17.html'),(6,20,'forum','/forum/le-site_20/page-1/',20,'forums','/forum20.html'),(7,21,'forum','/forum/videos-du-net_21/page-1/',21,'forums','/forum21.html'),(8,22,'forum','/forum/vip_22/page-1/',22,'forums','/forum22.html'),(9,24,'forum','/forum/poubelle_24/page-1/',24,'forums','/forum24.html'),(10,3,'forum','/forum/amtb_3/page-1/',3,'forums','/forum3.html'),(11,8,'forum','/forum/hxcmtb33_8/page-1/',8,'forums','/forum8.html'),(12,9,'forum','/forum/hot-locos_9/page-1/',9,'forums','/forum9.html'),(13,10,'forum','/forum/breizhmtb_10/page-1/',10,'forums','/forum10.html'),(14,11,'forum','/forum/viking-team_11/page-1/',11,'forums','/forum11.html'),(15,12,'forum','/forum/tmc31_12/page-1/',12,'forums','/forum12.html'),(16,13,'forum','/forum/ragnagna_13/page-1/',13,'forums','/forum13.html'),(17,14,'forum','/forum/burnin-marmottes_14/page-1/',14,'forums','/forum14.html'),(18,15,'forum','/forum/hent-mtb-are_15/page-1/',15,'forums','/forum15.html'),(19,16,'forum','/forum/go-go_16/page-1/',16,'forums','/forum16.html'),(20,18,'forum','/forum/a-m-c_18/page-1/',18,'forums','/forum18.html'),(21,19,'forum','/forum/creuse-ton-crew_19/page-1/',19,'forums','/forum19.html'),(22,23,'forum','/forum/cleanslide_23/page-1/',23,'forums','/forum23.html'),(23,26,'forum','/forum/chti_26/page-1/',26,'forums','/forum26.html'),(24,27,'forum','/forum/rastarockets_27/page-1/',27,'forums','/forum27.html'),(25,28,'forum','/forum/mtb-dans-le-34_28/page-1/',28,'forums','/forum28.html'),(26,29,'forum','/forum/gnon-gnon_29/page-1/',29,'forums','/forum29.html'),(27,30,'forum','/forum/naranoriders_30/page-1/',30,'forums','/forum30.html'),(28,31,'forum','/forum/1-k_31/page-1/',31,'forums','/forum31.html'),(29,25,'forum','/forum/dvd-2006_25/page-1/',25,'forums','/forum25.html'),(30,1,'user','/profil/mikael_1/',1,'users','/profil1.html'),(31,3,'user','/profil/paul_3/',3,'users','/profil3.html');

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
(18,6,'photo','2002-02-19 10:00:00','invalid',NULL,1,'announce'),

(19,1,'privatemessage','2002-02-18 10:00:00','valid',NULL,6,'announce'),
(20,2,'privatemessage','2002-02-18 11:00:00','valid',NULL,7,'announce'),
(21,3,'privatemessage','2002-02-18 12:00:00','invalid',NULL,7,'announce'),
(22,4,'privatemessage','2002-02-18 13:00:00','valid',NULL,5,'announce'),
(23,5,'privatemessage','2002-02-18 14:00:00','valid',NULL,1,'announce'),

(24,1,'spot','2003-01-01 09:00:00','valid',NULL,1,'announce'),
(25,2,'spot','2003-01-01 09:00:00','invalid',NULL,1,'announce'),
(26,3,'spot','2003-01-01 09:00:00','valid',NULL,3,'announce'),
(27,4,'spot','2003-01-01 09:00:00','valid',NULL,4,'announce'),
(28,5,'spot','2003-01-01 09:00:00','valid',NULL,5,'announce'),

(29,1,'trick','2011-01-01 21:23:00','valid',NULL,1,'announce'),
(30,2,'trick','2011-01-01 22:23:00','invalid',NULL,1,'announce'),
(31,3,'trick','2011-01-01 23:23:00','valid',NULL,3,'announce'),
(32,4,'trick','2011-01-01 23:23:01','valid',NULL,4,'announce'),

(33,1,'comment','2011-01-01 23:23:00','valid',13,1,'announce'),
(34,2,'comment','2011-01-01 23:23:01','invalid',13,1,'announce'),
(35,3,'comment','2011-01-01 23:23:02','valid',13,7,'announce'),
(36,4,'comment','2011-01-01 23:23:03','valid',13,1,'announce');

insert into `locations`(`id`, `longitude`, `latitude`, `zoom`, `status`, `mapType`, `yaw`, `pitch`, `itemId`, `itemType`, `dpt`, `country`, `city`) values
(1, 2.30787549, 48.77591276, 1, 'valid', 0, 0, 0, 1, 'user', 0, 1, 'bourg-la-reine'),
(2, 2.30887549, 48.77691276, 1, 'valid', 0, 0, 0, 1, 'spot', 0, 1, 'bourg-la-reine'),
(3, 2.30787549, 48.77591276, 1, 'valid', 0, 0, 0, 2, 'spot', 0, 1, 'bourg-la-reine'),
(4, 2.30687549, 48.77491276, 1, 'valid', 0, 0, 0, 3, 'spot', 0, 1, 'bourg-la-reine'),
(5, 2.30587549, 48.77391276, 1, 'valid', 0, 0, 0, 4, 'spot', 0, 1, 'bourg-la-reine'),
(6, 2.30487549, 48.77291276, 1, 'valid', 0, 0, 0, 5, 'spot', 1, 1, 'bourg-la-reine');

insert  into `media_album_aggregations`(`id`,`keyName`,`keyValue`,`albumId`) values (1,'user',1,4),(2,'user',3,5);

insert  into `media_albums`(`id`,`date`,`submitter`,`lastEditionDate`,`lastEditor`,`location`,`status`,`albumType`,`albumAccess`,`albumCreation`,`spot`) values
(1, '2002-02-18 14:00:00',4,NULL,NULL,NULL,'valid','simple','public','static',NULL),
(2, '2002-02-18 14:00:00',4,NULL,NULL,NULL,'valid','simple','public','static',NULL),
(3, '2002-02-18 14:00:00',4,NULL,NULL,NULL,'valid','simple','public','static',NULL),
(4, '2002-02-18 14:00:00',4,NULL,NULL,NULL,'valid','simple','public','static',NULL),
(5, '2002-02-18 14:00:00',1,NULL,NULL,NULL,'valid','aggregate','public','automatic',NULL),
(6, '2002-02-18 14:00:00',3,NULL,NULL,NULL,'valid','aggregate','public','automatic',NULL),
(7, '2002-02-18 14:00:00',4,NULL,NULL,NULL,'valid','aggregate','public','automatic',NULL),
(8, '2002-02-18 14:00:00',5,NULL,NULL,NULL,'valid','aggregate','public','automatic',NULL),
(9, '2002-02-18 14:00:00',6,NULL,NULL,NULL,'valid','aggregate','public','automatic',NULL),
(10,'2002-02-18 14:00:00',7,NULL,NULL,NULL,'valid','aggregate','public','automatic',NULL),
(11,'2002-02-18 14:00:00',8,NULL,NULL,NULL,'valid','aggregate','public','automatic',NULL),
(12,'2002-02-18 14:00:00',1,NULL,NULL,NULL,'valid','simple','public','static',NULL);

insert into `media_items`(`id`,`submitter`,`date`,`lastEditionDate`,`lastEditor`, `location`, `dpt`, `spot`, `trick`, `status`, `albumId`, `mediaType`, `uri`, `width`, `height`, `size`, `mediaSubType`, `thumbnailUri`, `thumbnailWidth`, `thumbnailHeight`, `thumbnailSubType`, `author`, `externalKey`) VALUES
(1, 6, '2002-02-18 10:00:00', null, null, null, null, null, null, 'valid', 1,  'photo', 'tata.jpg', 720, 540, 56789, 'jpg', 'tata.jpg', 160, 120, 'jpg', 1, null),
(2, 6, '2002-02-19 10:00:00', null, null, null, null, null, null, 'valid', 1,  'photo', 'tata2.jpg', 720, 540, 5678900, 'jpg', 'tata2.jpg', 160, 120, 'jpg', 1, null),
(3, 1, '2002-02-20 10:00:00', null, null, null, null, null, null, 'valid', 12, 'video', 'myvideo', 720, 540, 5678900, 'youtube', 'myvideo.jpg', 160, 120, 'jpg', 1, null),
(4, 6, '2002-02-19 10:00:00', null, null, null, null, null, null, 'valid', 1,  'photo', 'tata3.jpg', 720, 540, 5678900, 'jpg', 'tata3.jpg', 160, 120, 'jpg', 1, null),
(5, 6, '2002-02-19 10:00:00', null, null, null, null, null, null, 'valid', 1,  'photo', 'tata4.jpg', 720, 540, 5678900, 'jpg', 'tata4.jpg', 160, 120, 'jpg', 1, null),
(6, 1, '2002-02-19 10:00:00', null, null, null, null, null, null, 'invalid', 1,  'photo', 'invalid.jpg', 720, 540, 5678900, 'jpg', 'invalid.jpg', 160, 120, 'jpg', 1, null);

insert  into `privatemessages`(`id`,`title`,`content`,`date`,`submitter`,`lastEditor`,`lastEditionDate`,`toUser`,`read`,`status`) values
(1,'message6To1Title', 'message6To1Content', '2002-02-18 10:00:00', 6, NULL, NULL, 1, 1, 'valid'),
(2,'message7To1Title', 'message7To1Content', '2002-02-18 11:00:00', 7, NULL, NULL, 1, 0, 'valid'),
(3,'message7To1TitleInvalid', 'message7To1ContentInvalid', '2002-02-18 12:00:00', 7, NULL, NULL, 1, 0, 'invalid'),
(4,'message5To6Title', 'message5To6Content', '2002-02-18 13:00:00', 5, NULL, NULL, 6, 0, 'valid'),
(5,'message1To6Title', 'message1To6Content', '2002-02-18 14:00:00', 1, NULL, NULL, 6, 0, 'valid');

insert into `spots`(`id`,`date`,`submitter`,`lastEditionDate`,`lastEditor`,`dpt`,`location`,`status`,`difficulty`,`spotType`,`groundType`) values
(1,'2003-01-01 09:00:00',1,null,null,0,2,'valid',2,2,2),
(2,'2003-01-01 09:00:00',1,null,null,0,3,'invalid',2,2,2),
(3,'2003-01-01 09:00:00',3,null,null,0,3,'valid',2,2,2),
(4,'2003-01-01 09:00:00',4,null,null,0,4,'valid',3,3,3),
(5,'2003-01-01 09:00:00',5,null,null,0,5,'valid',2,2,2);

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
(6,'photo','en','description','plainUserPhotoDescription'),

(1,'spot','en','title','firstValidSpotTitle'),
(1,'spot','en','description','firstValidSpotDescription'),

(2,'spot','en','title','firstInvalidSpotTitle'),
(2,'spot','en','description','firstInvalidSpotDescription'),

(3,'spot','en','title','validSpotTitle3'),
(3,'spot','en','description','validSpotDescription3'),

(4,'spot','en','title','validSpotTitle4'),
(4,'spot','en','description','validSpotDescription4'),

(5,'spot','en','title','validSpotTitle5'),
(5,'spot','en','description','validSpotDescription5'),

(1,'trick','en','title','firstValidTrickTitle'),
(1,'trick','en','description','firstValidTrickDescription'),

(2,'trick','en','title','firstInvalidTrickTitle'),
(2,'trick','en','description','firstInvalidTrickDescription'),

(3,'trick','en','title','trick3Title'),
(3,'trick','en','description','trick3Description'),

(4,'trick','en','title','trick4Title'),
(4,'trick','en','description','trick4Description');


insert into `tricks`(`id`,`date`,`submitter`,`lastEditionDate`,`lastEditor`,`status`,`difficulty`,`trickTip`) values
(1,'2011-01-01 21:23:00',1,null,null,'valid',2,'bonus1'),
(2,'2011-01-01 22:23:00',1,null,null,'invalid',2,'bonus2'),
(3,'2011-01-01 23:23:00',7,null,null,'valid',2,'bonus3'),
(4,'2011-01-01 23:23:01',1,null,null,'valid',2,'bonus4');

insert  into `user_notifications`(`userId`,`itemType`,`medium`,`notify`) values
(0,'blogpost','homePage',1),
(0,'comment','homePage',1),
(0,'dossier','homePage',1),
(0,'mediaalbum','homePage',0),
(0,'news','homePage',1),
(0,'photo','homePage',1),
(0,'post','homePage',1),
(0,'privatemessage','homePage',1),
(0,'topic','homePage',1),
(0,'test','homePage',1),
(0,'trick','homePage',1),
(0,'spot','homePage',1),
(0,'user','homePage',1),
(0,'video','homePage',1),

(1,'blogpost','homePage',1),
(1,'comment','homePage',1),
(1,'dossier','homePage',1),
(1,'mediaalbum','homePage',0),
(1,'news','homePage',1),
(1,'photo','homePage',1),
(1,'post','homePage',1),
(1,'privatemessage','homePage',1),
(1,'topic','homePage',1),
(1,'test','homePage',1),
(1,'trick','homePage',1),
(1,'spot','homePage',1),
(1,'user','homePage',1),
(1,'video','homePage',1),

(4,'mediaalbum','homePage',1),
(5,'mediaalbum','homePage',0),

(7,'blogpost','homePage',1),
(7,'comment','homePage',1),
(7,'dossier','homePage',1),
(7,'mediaalbum','homePage',0),
(7,'news','homePage',1),
(7,'photo','homePage',1),
(7,'post','homePage',1),
(7,'privatemessage','homePage',1),
(7,'topic','homePage',1),
(7,'test','homePage',1),
(7,'trick','homePage',0),
(7,'spot','homePage',1),
(7,'user','homePage',1),
(7,'video','homePage',1);

insert  into `users`(`userId`,`username`,`password`,`email`,`status`,`date`,`lastLogin`,`openidIdentity`,`lang`,`firstName`,`lastName`,`birthDate`,`country`,`city`,`zip`,`gender`,`level`,`site`,`occupation`,`gear`,`otherSports`,`rideType`,`activationKey`,`newPassword`,`avatar`) values
(1,'plainuser','25f9e794323b453885f5181f1b624d0b','user1@example.org','member','2011-01-01 21:23:00','2011-08-14 08:12:20','http://openid-provider.appspot.com/mgramont','fr','prenom','nom','1980-07-01',1,'toulouse',31000,1,2,'http://www.mountainboard.fr','occupation','pro95','snowboard','110',NULL,NULL,'/media/avatars/1.jpg'),
(3,'banneduser','25f9e794323b453885f5181f1b624d0b','banned@mountainboard.fr','banned','2002-02-18 14:00:00','2010-01-11 09:04:37','','fr','','','','','',0,1,2,'','','','','',NULL,NULL,NULL),
(4,'adminuser','25f9e794323b453885f5181f1b624d0b','admin@mountainboard.fr','admin','2002-02-01 09:00:00','2010-01-11 09:04:37','','en','','','',1,'oakland',94610,2,3,'','','grasshopper',NULL,NULL,NULL,NULL,NULL),
(5,'editoruser','25f9e794323b453885f5181f1b624d0b','editor@mountainboard.fr','editor','2011-03-05 07:55:36','2010-01-11 09:04:37','','en','','','',2,'madrid',NULL,1,3,'','',NULL,NULL,NULL,NULL,NULL,NULL),
(6,'writeruser','25f9e794323b453885f5181f1b624d0b','writer@mountainboard.fr','writer','2011-06-15 05:55:23','2010-01-11 09:04:37','','fr','','','',1,NULL,NULL,NULL,NULL,'','',NULL,NULL,NULL,NULL,NULL,NULL),
(7,'otheruser','25f9e794323b453885f5181f1b624d0b','member@mountainboard.fr','member','2011-02-08 15:56:44','2010-01-11 09:04:37','','en','','','',NULL,NULL,NULL,NULL,NULL,'','',NULL,NULL,NULL,NULL,NULL,NULL),
(8,'pendinguser','25f9e794323b453885f5181f1b624d0b','pending@mountainboard.fr','pending','2002-02-18 14:00:00','2010-01-11 09:04:37','','fr','','','','','',0,1,2,'','','','','','art85dnh2obrozxtqo830shfcmsp4acl',NULL,NULL),
(9,'guest','empty','','guest',NULL,'2002-02-18 14:00:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

# Can't insert a user with and id of 0, so we have to resort to this:
update `users` SET userId = 0 WHERE userId = 9;
