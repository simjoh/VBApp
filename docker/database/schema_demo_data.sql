insert into api_keys values(0, sha1('184fa1a0-9e75-4b5a-b9f0-604f6d643daf'));

-- Roller och behörigheter
INSERT INTO roles (`role_id`, `role_name`) VALUES(1,'ADMIN');
INSERT INTO roles (`role_id`, `role_name`) VALUES(2, 'USER');
INSERT INTO roles (`role_id`, `role_name`) VALUES(3, 'SUPERUSER');
INSERT INTO roles (`role_id`, `role_name`) VALUES(4,'COMPETITOR');
INSERT INTO roles (`role_id`, `role_name`) VALUES( 5,'DEVELOPER');
INSERT INTO roles (`role_id`, `role_name`) VALUES( 6,'VOLONTEER');


INSERT INTO `permission_type`(`type_id`, `type_desc`, `type`) VALUES (1,'Read permission','READ');
INSERT INTO `permission_type`(`type_id`, `type_desc`, `type`) VALUES (2,'Write permission','WRITE');
INSERT INTO `permission_type`(`type_id`, `type_desc`, `type`) VALUES (3,'Update permission','UPDATE');

INSERT INTO permissions (perm_id, perm_desc,type_id) VALUES
( 1, 'USER',1),
( 2, 'USER',2),
( 3, 'USER',3),
( 4, 'Delete users',2),
(5, 'READCONTROLS',1),
(6, 'UPDATE',2),
(7, 'CREATE',2),
(8, 'DELETE',2),
(9, 'READ',1),
( 10, 'SITE',1),
( 11, 'SITE',2),
( 12, 'SITE',3),
( 13, 'TRACK',1),
( 14, 'TRACK',2),
( 15, 'TRACK',3),
( 16, 'EVENT',1),
( 17, 'EVENT',2),
( 18, 'EVENT',3),
( 19, 'CHECKPOINT',1),
( 20, 'CHECKPOINT',2),
( 21, 'CHECKPOINT',3);


INSERT INTO roles_permissions (role_id, perm_mod, perm_id) VALUES
(1, 'ADMIN', 1),
(1, 'ADMIN', 2),
(1, 'ADMIN', 3),
(1, 'ADMIN', 4),
(1, 'ADMIN', 10),
(1, 'ADMIN', 11),
(1, 'ADMIN', 12),
(1, 'ADMIN', 13),
(1, 'ADMIN', 14),
(1, 'ADMIN', 15),
(1, 'ADMIN', 16),
(1, 'ADMIN', 17),
(1, 'ADMIN', 18),
(2, 'USER', 1),
( 2, 'SITE',10),
( 2, 'TRACK',13),
( 2, 'EVENT',17),
( 2, 'CHECKPOINT',1),
(4, 'COMPETITORS', 19);



-- Användare
INSERT INTO `users` (`user_uid`, `user_name`, `given_name`, `family_name`, `password`) VALUES
('82fbb2ec-d998-4b8a-861f-46f2b0fdbc4e', 'admin@admin', 'Admin', 'Administratör',  sha1('admin')),
('ac6543a6-df1e-4c5b-95a1-565a00676603', 'volonteer@volonteer', 'Anders', 'Volontär',  sha1('volonteer')),
('e3b78c98-ffe5-4877-8491-258413c772e9', 'user@user', 'Jonas', 'Användare',  sha1('user'));

INSERT INTO `user_info`(`uid`, `user_uid`, `email`, `phone`) VALUES ('5e125776-2005-43a7-b58a-a1fc960ce9f4','82fbb2ec-d998-4b8a-861f-46f2b0fdbc4e','admin@administrator.se','0703105900');

-- Lägg till lite behörigheter
--Admin + superuser
INSERT INTO user_role(role_id, user_uid) VALUES (1,'82fbb2ec-d998-4b8a-861f-46f2b0fdbc4e');
INSERT INTO user_role(role_id, user_uid) VALUES (3,'82fbb2ec-d998-4b8a-861f-46f2b0fdbc4e');
-- Volontär
INSERT INTO user_role(role_id, user_uid) VALUES (6,'ac6543a6-df1e-4c5b-95a1-565a00676603');
-- Vanlig användare
INSERT INTO user_role(role_id, user_uid) VALUES (2,'e3b78c98-ffe5-4877-8491-258413c772e9');
-- Cyklist
INSERT INTO user_role(role_id, user_uid) VALUES (2,'e3b78c98-ffe5-4877-8491-258413c772e9');

-- -- Cyklister
INSERT INTO competitors(competitor_uid, user_name, given_name, family_name, role_id,password, birthdate) VALUES ('2922a6e9-9e32-4832-9575-b3d2eb3011b9','100','Pelle','Cyklist',4,sha1('test'), DATE('1973-06-15'));
INSERT INTO competitors(competitor_uid, user_name ,given_name, family_name,role_id,password,birthdate) VALUES ('68f06a8c-8f08-45cc-8d20-d5e37ce658ba','200','Johan','Randonnéer',4,sha1('test1'),  DATE('1980-08-15'));
INSERT INTO competitors(competitor_uid, user_name ,given_name, family_name,role_id,password, birthdate) VALUES ('593edcab-5dcb-4916-829d-08ac536770ad','300','Kalle','Super Randonneur',4, sha1('test2'),DATE('1990-03-04'));

INSERT INTO `competitor_info`(`uid`, `competitor_uid`, `email`, `phone`, `adress`, `postal_code`, `place`, `country`) VALUES ('31a852b0-23ec-4689-b4cf-0c970f9b90fd','2922a6e9-9e32-4832-9575-b3d2eb3011b9','democyklist@test.se','0703158465','cyklistgatan 15', '90100' ,'cykelby', 'sweden');

----- Utkast bygga banor
--Sites text Brännäset
INSERT INTO site(site_uid, place,  adress, description, lat, lng , location) VALUES ('8a13602-83dc-447d-a85f-13b943e23a42','Umeå','Broparken 1','Startplats',20.3128832, 63.7042688,'');
INSERT INTO site(site_uid, place,  adress, description, lat, lng , location) VALUES ('47e2e397-872a-49f9-8f5b-069d09f5855c','Brännäset','Brännsäset 1','Kontroll vid havet',null, null,'');
INSERT INTO site(site_uid, place,  adress, description, lat, lng , location) VALUES ('e53d8d51-c5e1-4d25-a8d3-afe0646c1f13','Rödtjarn','','',null, null,'');
INSERT INTO site(site_uid, place,  adress, description, lat, lng , location) VALUES ('e8a2df8a-7c0c-48f3-be9e-432d97e418ef','Circle K','Normaling','Matkontroll med vc',null, null,'');
-- Olika event som pågår över tid
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`) VALUES ('f6bdbba8-960d-472b-8864-cda48a07eeac','Brm series 2022',STR_TO_DATE("01-05-22","%d-%m-%y"),STR_TO_DATE("01-09-22","%d-%m-%y"),false,false,true,'Brm serien 2022');
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`) VALUES ('ae5c2acd-8042-480b-b937-f3f416d7aeaa','Västerbottenbrevet 2022',STR_TO_DATE("07-08-22","%d-%m-%y"),STR_TO_DATE("07-08-22","%d-%m-%y"),false,false,true,'Brevet 2022');
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`) VALUES ('2d1767dc-f768-419b-8cdd-01fdfdbc44e4','Västerbottenbrevet 2023',STR_TO_DATE("07-08-23","%d-%m-%y"),STR_TO_DATE("07-08-23","%d-%m-%y"),false,false,false,'Brevet 2023');
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`) VALUES ('62c332d2-72c8-407c-b71c-ca2541d72577','Månskensbrevet 2022',STR_TO_DATE("18-09-22","%d-%m-%y"),STR_TO_DATE("18-09-22","%d-%m-%y"),false,false,true,'Kvällscykling i fullmåne');

-- Banor som ingår i ett event
--BRM
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`,start_date_time, active) VALUES ('8a5a0649-6aee-4b64-803e-4f083f746d2d','BRM200K','http://www.banan.strava.com','f6bdbba8-960d-472b-8864-cda48a07eeac','200 k ....',200.3,null, true);
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`, start_date_time, active) VALUES ('0c9648fd-1664-4526-aaa4-059a01fc079c','BRM300K','http://www.banan.strava.com','f6bdbba8-960d-472b-8864-cda48a07eeac','300 k ....',300.3, null, true);
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`,start_date_time, active ) VALUES ('8862bd72-f5af-45f5-a377-337f26cbd195','BRM400K','http://www.banan.strava.com','f6bdbba8-960d-472b-8864-cda48a07eeac','400 k ....',400.3, null, true);
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`,start_date_time, active) VALUES ('06ba3113-d95b-48c9-b0f9-f25bda5dad31','BRM600K','http://www.banan.strava.com','f6bdbba8-960d-472b-8864-cda48a07eeac','600 k ....',600.3, null, true);
-- Månskensbrev
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`,start_date_time) VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221','Månskensbrevet','http://www.banan.strava.com','62c332d2-72c8-407c-b71c-ca2541d72577','Månstensbrevet k ....',86.3, null);

--  Koppling mellan banor och event
-- Här BRM banor
INSERT INTO `event_tracks`(`track_uid`, `event_uid`) VALUES ('8a5a0649-6aee-4b64-803e-4f083f746d2d','f6bdbba8-960d-472b-8864-cda48a07eeac');
INSERT INTO `event_tracks`(`track_uid`, `event_uid`) VALUES ('0c9648fd-1664-4526-aaa4-059a01fc079c','f6bdbba8-960d-472b-8864-cda48a07eeac');
INSERT INTO `event_tracks`(`track_uid`, `event_uid`) VALUES ('8862bd72-f5af-45f5-a377-337f26cbd195','f6bdbba8-960d-472b-8864-cda48a07eeac');
INSERT INTO `event_tracks`(`track_uid`, `event_uid`) VALUES ('06ba3113-d95b-48c9-b0f9-f25bda5dad31','f6bdbba8-960d-472b-8864-cda48a07eeac');

-- Månskensbrev kopplad till banor
INSERT INTO `event_tracks`(`track_uid`, `event_uid`) VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221','62c332d2-72c8-407c-b71c-ca2541d72577');

--Kontroller kopplade till en plats
-- En kontroll brännäset
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`) VALUES ('63e8f1de-22ad-416d-b181-4a1a004a2959','47e2e397-872a-49f9-8f5b-069d09f5855c','Test 1','DEMO',33, TIME("2021-06-15 09:00:00"),("2021-06-15 10:00:00"));
-- En kontroll rödtjärn
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`) VALUES ('6b86551e-e9b6-46f1-b411-3196e0f0f4e3','e53d8d51-c5e1-4d25-a8d3-afe0646c1f13','Test 2','DEMO',60, TIME("2021-06-15 11:00:00"),TIME("2021-06-15 12:30:00"));
-- Mål broparken
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`) VALUES ('c0a8e4a4-a37a-4e9d-b59e-112519b4abc0','8a13602-83dc-447d-a85f-13b943e23a42','Test 3','DEMO',86, TIME("2021-06-15 08:00:00"),TIME("2021-06-15 09:00:00"));


-- Koppling bana till  kontroll/er
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`) VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221','63e8f1de-22ad-416d-b181-4a1a004a2959');
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`) VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221','6b86551e-e9b6-46f1-b411-3196e0f0f4e3');
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`) VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221','c0a8e4a4-a37a-4e9d-b59e-112519b4abc0');

-- Deltagare på en viss bana
-- BRM200
INSERT INTO `participant`(`participant_uid`, `track_uid`, `competitor_uid`, `startnumber`, `finished`, `acpkod`, `club_uid`, `time`, `dns`, `dnf`, `register_date_time`) VALUES ('b3f68992-c5c7-4c31-bad5-78a93b53f28f','8a5a0649-6aee-4b64-803e-4f083f746d2d','2922a6e9-9e32-4832-9575-b3d2eb3011b9',1018,false,113072,'e990365b-00a8-4615-a648-c7b6797ce13a',null,false,false, CURRENT_TIMESTAMP());
INSERT INTO `participant`(`participant_uid`, `track_uid`, `competitor_uid`, `startnumber`, `finished`, `acpkod`, `club_uid`, `time`, `dns`, `dnf`, `register_date_time`) VALUES ('162b49ea-1e8c-4047-8fd6-e4d96920a054','8a5a0649-6aee-4b64-803e-4f083f746d2d','593edcab-5dcb-4916-829d-08ac536770ad',2018,false,113036,'31f10de0-33c4-49da-a8fe-4cc2354604bc',null,false,false, CURRENT_TIMESTAMP());
-- BRM300
INSERT INTO `participant`(`participant_uid`, `track_uid`, `competitor_uid`, `startnumber`, `finished`, `acpkod`, `club_uid`, `time`, `dns`, `dnf`, `register_date_time`) VALUES ('e6957ddc-f9fd-444f-861f-f0f22cc363b1','0c9648fd-1664-4526-aaa4-059a01fc079c','593edcab-5dcb-4916-829d-08ac536770ad',2018,false,113036,'427b5419-ebad-4a31-bd84-ed26718a32be',null,false,false,CURRENT_TIMESTAMP());
-- Månskensbrevet
INSERT INTO `participant`(`participant_uid`, `track_uid`, `competitor_uid`, `startnumber`, `finished`, `acpkod`, `club_uid`, `time`, `dns`, `dnf`,`register_date_time`) VALUES ('e8f9557e-4b96-41a0-b6c7-be6c45d81259','bf31d141-32c3-4cc9-b497-36d82b060221','2922a6e9-9e32-4832-9575-b3d2eb3011b9',1020,false,113072,'be80dc3b-3ff7-414f-ad7f-41db57e90221',null,false,false, CURRENT_TIMESTAMP());

-- Koppla till kontroll
-- BRM200
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time` , `volonteer_checkin`,lat, lng ) VALUES ('b3f68992-c5c7-4c31-bad5-78a93b53f28f','63e8f1de-22ad-416d-b181-4a1a004a2959',false,null,false,null, null);
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time` ,`volonteer_checkin`,lat, lng) VALUES ('162b49ea-1e8c-4047-8fd6-e4d96920a054','63e8f1de-22ad-416d-b181-4a1a004a2959',false,null, false,null, null);
--BRM300
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time`,`volonteer_checkin`,lat, lng) VALUES ('e6957ddc-f9fd-444f-861f-f0f22cc363b1','6b86551e-e9b6-46f1-b411-3196e0f0f4e3',false,null,false,null, null);
--- Månskensbrev
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time`,`volonteer_checkin`,lat, lng) VALUES ('e8f9557e-4b96-41a0-b6c7-be6c45d81259','63e8f1de-22ad-416d-b181-4a1a004a2959',false,null,false,null, null);
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time`,`volonteer_checkin`,lat, lng) VALUES ('e8f9557e-4b96-41a0-b6c7-be6c45d81259','6b86551e-e9b6-46f1-b411-3196e0f0f4e3',false,null,false,null, null);
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time`,`volonteer_checkin`,lat, lng) VALUES ('e8f9557e-4b96-41a0-b6c7-be6c45d81259','c0a8e4a4-a37a-4e9d-b59e-112519b4abc0',false,null,false,null, null);

-- Klubbar
insert into club (club_uid, acp_kod, title) VALUES('e990365b-00a8-4615-a648-c7b6797ce13a',113072, 'Cykelintresset');
insert into club (club_uid, acp_kod, title) VALUES('31f10de0-33c4-49da-a8fe-4cc2354604bc',113036, 'Gimonäs CK');
insert into club (club_uid, acp_kod, title) VALUES('427b5419-ebad-4a31-bd84-ed26718a32be',113110, 'Sävar CK');
insert into club (club_uid, acp_kod, title) VALUES('6343a43b-dc0f-4415-ac56-50f000aca4d6',113015, 'Höga kusten Cyklisterna');
insert into club (club_uid, acp_kod, title) VALUES('e26e0a36-1996-4980-b597-164470b7bdea',113107, 'CK Örnen');
insert into club (club_uid, acp_kod, title) VALUES('8f0dc40d-e91e-4cc3-ad23-86ca481e0b32',113099, 'Individuell');
insert into club (club_uid, acp_kod, title) VALUES('9547515e-6cd9-42dc-9dfa-cfae262907ee',113111, 'CK Guldkedjan');
insert into club (club_uid, acp_kod, title) VALUES('6907d03f-3257-42b2-8941-09816a979771',110108, 'She Rides C');

