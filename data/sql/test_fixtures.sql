TRUNCATE comments;
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


insert into `comments`(`id`,`parentId`,`parentType`, `date`, `submitter`, `tone`, `status`, `content`) values
('c74814e6-89c7-42f1-a9d1-a98567048c17','523de4aa-3c06-45c8-8c4f-339a37d2bf83', 'video', '2019-11-07 16:40:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone1', 'valid', '[{"locale": "en-US", "text": "This is comment 1"}]'),
('f3ae1451-b341-4364-80db-2751da4f3537','523de4aa-3c06-45c8-8c4f-339a37d2bf83', 'video', '2019-11-07 16:41:00', '85193083-ce22-43a5-993b-1c7aba53d13c', 'tone2', 'valid', '[{"locale": "en-US", "text": "This is comment 2"}]'),
('5f7f5e35-52e6-44ad-90c6-e5365eda3469','523de4aa-3c06-45c8-8c4f-339a37d2bf83', 'video', '2019-11-07 16:42:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone3', 'valid', '[{"locale": "en-US", "text": "This is comment 3"}]'),
('4c49ebde-6d4b-4dba-b3f5-f19fb7bfa23f','051cae3a-95f7-4a29-996d-796d4c263a1e', 'photo', '2019-11-07 16:45:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone4', 'valid', '[{"locale": "en-US", "text": "This is comment 4"}]'),
('3ca9b798-3ca8-4734-872d-13216df0d7f1','9119c477-1c48-4449-9b8e-9cdb745fc912', 'photo', '2019-11-07 16:46:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone5', 'valid', '[{"locale": "en-US", "text": "This is comment 5", "original": true}]'),
('adba891f-1768-4916-9e84-ff80ae2a978c','9119c477-1c48-4449-9b8e-9cdb745fc912', 'photo', '2019-11-07 16:47:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone6', 'valid', '[{"locale": "en-US", "text": "</script><script>alert(\'this is an XSS\')</script>"}]'),
('53cb7042-b6da-47d1-ba34-0b43fd4f6c79','a3833b1c-1db0-4a93-9efc-b6659400ce9f', 'photo', '2019-11-07 16:48:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone7', 'invalid', 'This is an invalid comment'),
('786b4faa-6f80-4b31-9bad-c436d7dd085e','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 16:49:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for update"}]'),
('211be7cc-6070-4674-b675-eb489b324dd1','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 16:50:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for update"}]'),
('282a984e-a33d-49de-853f-13b357808826','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 16:51:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for update"}]'),
('120613d6-eb81-4521-a61e-bab1b694dda5','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 16:52:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for update"}]'),
('d399ad28-c39a-4a68-9787-b4d1dad1c644','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 16:53:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for update"}]'),
('55074dbe-f5f8-4e2d-9d1d-065715a182d8','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 16:54:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for update"}]'),
('a7868b1e-7cc0-49d0-bc2e-c07f22c7bcd0','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 16:55:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for deletion"}]'),
('cae9e44f-b74b-47dc-abaf-30f61f467e00','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 16:56:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for deletion"}]'),
('76d4a5ce-cbfc-4117-8901-f3a41b23deb5','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 16:57:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for deletion"}]'),
('97b235b8-ab41-448c-a97b-e15867148c85','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 16:58:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for deletion"}]'),
('7b142ba7-7e00-4e86-bf53-8ef22d10d56c','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 16:59:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for deletion"}]'),
('0da69559-b156-4da1-b8c1-c85cb11f3cbb','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 17:00:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for deletion"}]'),
('eca65962-63a9-4928-aecc-3606645a8874','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 17:01:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for deletion"}]'),
('ee55ff22-7cc0-49d0-bc2e-c07f22c71234','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 17:01:00', '941b2ac5-2519-44de-84f9-ea9980e4631f', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for deletion"}]'),
('d36ac198-cb92-46e3-9d5d-8d46e825aa30','44aa386d-3171-432b-a648-f40929043758', 'photo', '2019-11-07 17:01:00', '941b2ac5-2519-44de-84f9-ea9980e4631f', 'tone8', 'valid', '[{"locale": "en-US", "text": "This is comment for reaction"}]');

insert into `images`(`id`,`imageType`,`storageType`, `status`, `submitter`, `date`, `lastEditionDate`, `lastEditor`, `width`, `height`) values
('44130f1c-2931-451a-b6cd-0f49d0b3ad85', 0, 0, 'valid', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-18 10:00:00', null, null, 720, 540),
('84846b0b-254e-472b-94a4-2ecd6bc4cd5e', 0, 0, 'valid', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:01', null, null, 720, 540),
('66059f80-59d4-4237-8602-fbbf17f26616', 0, 0, 'valid', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-20 10:00:03', null, null, 720, 540),
('a9c8081c-43ec-418a-acc2-1ad575672250', 0, 0, 'valid', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:04', null, null, 720, 540),
('b5c1429b-f492-4d71-892d-38dd33deffda', 0, 0, 'invalid', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:05', null, null, 720, 540),
('325edced-b174-4479-8d6f-fea7a4d5e84b', 0, 0, 'valid', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:07', null, null, 720, 540),
('00e08c00-42a1-474c-a0a1-ae0c2dee68f8', 0, 0, 'valid', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:07', null, null, 720, 540),
('bc560af4-8039-43f7-8f07-2c22c53e1954', 0, 0, 'valid', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:07', null, null, 720, 540),
('2c22c53e1954-8039-43f7-8f07-bc560af4', 0, 0, 'valid', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:07', null, null, 720, 540),
('bf22c53e1954-2339-12f7-5f07-55560af8', 0, 0, 'valid', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:07', null, null, 720, 540);

insert  into `media_album_aggregations`(`id`,`keyName`,`keyValue`,`albumId`) values
(1,'user','85193083-ce22-43a5-993b-1c7aba53d13c','ab2121cb-03d6-45de-a1ba-4581dd00d79f'),
(2,'user','b1786ac1-5cc8-4156-8471-8a80a87efe17','f2aa61fc-bbc5-45b2-bada-10bff101d957'),
(3,'user','85193083-ce22-43a5-993b-1c7aba53d13c','bc560af4-8039-43f7-8f07-2c22c53e1954'),
(4,'user','60bfb8a7-5754-4186-acd2-44b20ef32399','f2d94b87-3c94-4bd1-9bdd-950393ac1aa5'),
(5,'user','102e6ed9-cdac-4c9c-9483-a3309970db59','dee785d1-89a5-45df-ae86-876ec2b47024'),
(6,'user','6750ff62-7195-49f5-bf81-e3d395e6cdcf','e7f47c36-b9b1-41a1-8e53-7b6a9d0914ee'),
(7,'user','0230ec1d-dc7b-42e6-89d3-3707ee5ade71','87587188-3c92-4027-8b63-4997cf7f8ea2'),
(8,'user','941b2ac5-2519-44de-84f9-ea9980e4631f','210ae999-f7e3-4b39-915d-033dbc8d965c'),
(9,'user','cc834ce6-58df-4381-aed3-8fe4c2923434','a8a7b0fd-e0f2-49d9-8974-e6292d7e667b');

insert  into `media_albums`(`id`,`date`,`submitter`,`lastEditionDate`,`lastEditor`,`status`,`albumType`,`albumContributions`,`albumCreation`,`albumVisibility`) values
('a3833b1c-1db0-4a93-9efc-b6659400ce9f', '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static','visible'),
('40763a20-5aa0-49d2-85f7-292c95cb3643', '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static','visible'),
('40c38ab9-cb77-49a7-a296-0805237d2710', '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static','visible'),
('ab2121cb-03d6-45de-a1ba-4581dd00d79f', '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','simple','public','static','visible'),
('89602268-929a-4018-8341-9b96ccddf9c8', '2002-02-18 14:00:00','102e6ed9-cdac-4c9c-9483-a3309970db59',NULL,NULL,'valid','simple','private','static','visible'),
('99602268-929a-4018-8341-9b96ccddf9c9', '2002-02-18 14:00:00','102e6ed9-cdac-4c9c-9483-a3309970db59',NULL,NULL,'valid','simple','public','static','visible'),
('7c719bac-998d-4b15-8b52-2321078e887c', '2002-02-18 14:00:00','85193083-ce22-43a5-993b-1c7aba53d13c',NULL,NULL,'valid','simple','public','static','visible'),
('f2aa61fc-bbc5-45b2-bada-10bff101d957', '2002-02-18 14:00:00','85193083-ce22-43a5-993b-1c7aba53d13c',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('9fb7e4d7-fc38-43b1-a890-4d0872874b5b', '2002-02-18 14:00:00','b1786ac1-5cc8-4156-8471-8a80a87efe17',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('a5682d96-fc1b-4b76-b306-485631a5f26d', '2002-02-18 14:00:00','60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('6b34bbdd-780d-4c2a-adba-c4229a161136', '2002-02-18 14:00:00','102e6ed9-cdac-4c9c-9483-a3309970db59',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('0a8aca42-cd0e-4fe7-8c55-9495f2e95164', '2002-02-18 14:00:00','6750ff62-7195-49f5-bf81-e3d395e6cdcf',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('ac1a8df7-e15b-470e-a2bb-b7fb8187270f','2002-02-18 14:00:00','941b2ac5-2519-44de-84f9-ea9980e4631f',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('76d513e3-3879-48d7-8f5f-2e8afc36a647','2002-02-18 14:00:00','cc834ce6-58df-4381-aed3-8fe4c2923434',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('feba0696-8954-4596-a849-0087cbe8ea76','2002-02-18 14:00:00','85193083-ce22-43a5-993b-1c7aba53d13c',NULL,NULL,'valid','simple','public','static','visible'),
('bc560af4-8039-43f7-8f07-2c22c53e1954', '2002-02-18 14:00:00', '85193083-ce22-43a5-993b-1c7aba53d13c',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('f2d94b87-3c94-4bd1-9bdd-950393ac1aa5', '2002-02-18 14:00:00', '60bfb8a7-5754-4186-acd2-44b20ef32399',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('dee785d1-89a5-45df-ae86-876ec2b47024', '2002-02-18 14:00:00', '102e6ed9-cdac-4c9c-9483-a3309970db59',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('e7f47c36-b9b1-41a1-8e53-7b6a9d0914ee', '2002-02-18 14:00:00', '6750ff62-7195-49f5-bf81-e3d395e6cdcf',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('87587188-3c92-4027-8b63-4997cf7f8ea2', '2002-02-18 14:00:00', '0230ec1d-dc7b-42e6-89d3-3707ee5ade71',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('210ae999-f7e3-4b39-915d-033dbc8d965c', '2002-02-18 14:00:00', '941b2ac5-2519-44de-84f9-ea9980e4631f',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('a8a7b0fd-e0f2-49d9-8974-e6292d7e667b', '2002-02-18 14:00:00', 'cc834ce6-58df-4381-aed3-8fe4c2923434',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('7b3a3651-08d1-4cee-9082-ed522d8e447b', '2002-02-18 14:00:00', 'cc834ce6-58df-4381-aed3-8fe4c2923434',NULL,NULL,'valid','aggregate','public','automatic','visible'),
('c68acf0f-b740-4e62-ba0c-1bd60eb72efc', '2002-02-18 14:00:00', 'cc834ce6-58df-4381-aed3-8fe4c2923434',NULL,NULL,'valid','aggregate','public','automatic','visible');

insert into `media_items`(`id`,`submitter`,`date`,`lastEditionDate`,`lastEditor`, `status`, `albumId`, `mediaType`, `imageId`, `vendorKey`, `width`, `height`, `thumbWidth`, `thumbHeight`, `mediaSubType`, `storageType`) VALUES
('44aa386d-3171-432b-a648-f40929043758', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-18 10:00:00', null, null, 'valid', 'a3833b1c-1db0-4a93-9efc-b6659400ce9f',  'photo', '44130f1c-2931-451a-b6cd-0f49d0b3ad85', null, 720, 540, 720, 540, 'jpg', 0),
('9b523108-9b34-43ea-916d-874ff6013021', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:01', null, null, 'valid', 'a3833b1c-1db0-4a93-9efc-b6659400ce9f',  'photo', '84846b0b-254e-472b-94a4-2ecd6bc4cd5e', null, 720, 540, 720, 540, 'jpg', 0),
('e4e4cb62-4820-4cff-baff-74b2bdcb1993', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:03', null, null, 'valid', 'a3833b1c-1db0-4a93-9efc-b6659400ce9f',  'photo', 'a9c8081c-43ec-418a-acc2-1ad575672250', null, 720, 540, 720, 540, 'jpg', 0),
('9119c477-1c48-4449-9b8e-9cdb745fc912', '6750ff62-7195-49f5-bf81-e3d395e6cdcf', '2002-02-19 10:00:04', null, null, 'valid', 'a3833b1c-1db0-4a93-9efc-b6659400ce9f',  'photo', '66059f80-59d4-4237-8602-fbbf17f26616', null, 720, 540, 720, 540, 'jpg', 0),
('051cae3a-95f7-4a29-996d-796d4c263a1e', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-19 10:00:05', null, null, 'invalid', 'a3833b1c-1db0-4a93-9efc-b6659400ce9f',  'photo', 'b5c1429b-f492-4d71-892d-38dd33deffda', null, 720, 540, 720, 540, 'jpg', 0),
('6af68e15-276b-42d7-8683-909943b0ae27', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-20 10:00:06', null, null, 'valid', 'feba0696-8954-4596-a849-0087cbe8ea76', 'video', 'c51a53ef-c5af-4087-b766-cd83223a06b0', 'myvideo', 720, 540, 720, 540, 'youtube', 0),
('523de4aa-3c06-45c8-8c4f-339a37d2bf83', '85193083-ce22-43a5-993b-1c7aba53d13c', '2019-11-07 16:00:06', null, null, 'valid', 'feba0696-8954-4596-a849-0087cbe8ea76', 'video', 'ff9faa11-7970-49e8-a742-d44b8035b083', 'xxyyzz', 720, 540, 720, 540, 'youtube', 0),
('78980394-8774-428f-8989-59f76db7721b', '85193083-ce22-43a5-993b-1c7aba53d13c', '2002-02-20 10:00:06', null, null, 'valid', 'c68acf0f-b740-4e62-ba0c-1bd60eb72efc', 'photo', 'dc740f48-1604-431b-9d71-af121d3f3768', 'bzzzzt', 720, 540, 720, 540, 'youtube', 0),
('9e17540a-09da-4813-bbfc-7c93383bd1c4', '85193083-ce22-43a5-993b-1c7aba53d13c', '2019-11-07 16:00:06', null, null, 'valid', 'c68acf0f-b740-4e62-ba0c-1bd60eb72efc', 'video', '90aacaf7-1404-4922-b9fb-66892b3ed6d5', 'sxyyzz', 720, 540, 720, 540, 'youtube', 0);

insert into `reactions`(`id`,`itemType`,`itemId`,`type`,`status`,`submitter`,`date`,`lastEditionDate`,`lastEditor`) values
('73ff48a7-5754-4186-acd2-44b20ef32344','comment','c74814e6-89c7-42f1-a9d1-a98567048c17','laughing','valid','85193083-ce22-43a5-993b-1c7aba53d13c','2019-05-26 14:00:00',NULL,NULL),
('bdff48a7-5754-4186-acd2-22b20ef32355','comment','f3ae1451-b341-4364-80db-2751da4f3537','fire','valid','85193083-ce22-43a5-993b-1c7aba53d13c','2019-05-26 15:00:00',NULL,NULL),
('e8ff48a7-5754-4186-acd2-11b20ef32399','comment','5f7f5e35-52e6-44ad-90c6-e5365eda3469','angry','valid','85193083-ce22-43a5-993b-1c7aba53d13c','2019-05-26 16:00:00',NULL,NULL),
('abff48a7-5754-4186-acd2-11b20ef32311','comment','ee55ff22-7cc0-49d0-bc2e-c07f22c71234','angry','valid','941b2ac5-2519-44de-84f9-ea9980e4631f','2019-05-28 16:00:00',NULL,NULL),
('abcdef12-5754-4186-acd2-11b20ef32311','comment','0055ff22-7cc0-49d0-bc2e-c07f22c71234','fire','valid','6750ff62-7195-49f5-bf81-e3d395e6cdcf','2019-05-28 16:00:00',NULL,NULL);

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

insert  into `users`(`userId`,`username`,`password`,`salt`,`email`,`status`,`date`,`lastLogin`,`timezone`,`locale`,`firstName`,`lastName`,`city`,`activationKey`,`newPassword`,`avatar`) values
('85193083-ce22-43a5-993b-1c7aba53d13c','plainuser','$2y$12$yRIPb1O8JAM5B1NmGSGDG.GQKVMTryO9SXcj1A/t6nqeAGhqbsCre','b3833b1c-1db0-4a93-9efc-b6659400ce9f','user1@example.org','member','2011-01-01 00:00:01','2011-08-14 08:12:20','Europe/Paris','fr-FR','prenom','nom','toulouse',NULL,NULL,'/media/avatars/1.jpg'),
('b1786ac1-5cc8-4156-8471-8a80a87efe17','banneduser','$2y$12$yRIPb1O8JAM5B1NmGSGDG.GQKVMTryO9SXcj1A/t6nqeAGhqbsCre','b3833b1c-1db0-4a93-9efc-b6659400ce9f','banned@mountainboard.fr','banned','2011-01-01 00:00:02','2010-01-11 09:04:37','Europe/Paris','fr-FR','','','',NULL,NULL,NULL),
('60bfb8a7-5754-4186-acd2-44b20ef32399','adminuser','$2y$12$yRIPb1O8JAM5B1NmGSGDG.GQKVMTryO9SXcj1A/t6nqeAGhqbsCre','b3833b1c-1db0-4a93-9efc-b6659400ce9f','admin@mountainboard.fr','admin','2011-01-01 00:00:03','2010-01-11 09:04:37','Europe/Paris','en-US','','','oakland',NULL,NULL,NULL),
('102e6ed9-cdac-4c9c-9483-a3309970db59','editoruser','$2y$12$yRIPb1O8JAM5B1NmGSGDG.GQKVMTryO9SXcj1A/t6nqeAGhqbsCre','b3833b1c-1db0-4a93-9efc-b6659400ce9f','editor@mountainboard.fr','editor','2011-01-01 00:00:04','2010-01-11 09:04:37','Europe/Paris','en-US','','','madrid',NULL,NULL,NULL),
('6750ff62-7195-49f5-bf81-e3d395e6cdcf','writeruser','$2y$12$yRIPb1O8JAM5B1NmGSGDG.GQKVMTryO9SXcj1A/t6nqeAGhqbsCre','b3833b1c-1db0-4a93-9efc-b6659400ce9f','writer@mountainboard.fr','writer','2011-01-01 00:00:05','2010-01-11 09:04:37','Europe/Paris','fr-FR','','',NULL,NULL,NULL,NULL),
('941b2ac5-2519-44de-84f9-ea9980e4631f','otheruser','$2y$12$yRIPb1O8JAM5B1NmGSGDG.GQKVMTryO9SXcj1A/t6nqeAGhqbsCre','b3833b1c-1db0-4a93-9efc-b6659400ce9f','member@mountainboard.fr','member','2011-01-01 00:00:06','2010-01-11 09:04:37','Europe/Paris','en-US','','',NULL,NULL,NULL,NULL),
('cc834ce6-58df-4381-aed3-8fe4c2923434','pendinguser','$2y$12$yRIPb1O8JAM5B1NmGSGDG.GQKVMTryO9SXcj1A/t6nqeAGhqbsCre','b3833b1c-1db0-4a93-9efc-b6659400ce9f','pending@mountainboard.fr','pending','2011-01-01 00:00:07','2010-01-11 09:04:37','Europe/Paris','fr-FR','','','','art85dnh2obrozxtqo830shfcmsp4acl',NULL,NULL),
('0230ec1d-dc7b-42e6-89d3-3707ee5ade71','guest','empty','b3833b1c-1db0-4a93-9efc-b6659400ce9f','','guest',NULL,'2011-01-01 00:00:08',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

