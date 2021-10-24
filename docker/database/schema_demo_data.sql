insert into api_keys values(0, sha1('184fa1a0-9e75-4b5a-b9f0-604f6d643daf'));

-- Roller och behörigheter
INSERT INTO roles (`role_id`, `role_name`) VALUES(1,'ADMIN');
INSERT INTO roles (`role_id`, `role_name`) VALUES(2, 'USER');
INSERT INTO roles (`role_id`, `role_name`) VALUES(3, 'SUPERUSER');
INSERT INTO roles (`role_id`, `role_name`) VALUES(4,'COMPETITOR');
INSERT INTO roles (`role_id`, `role_name`) VALUES( 5,'DEVELOPER');
INSERT INTO roles (`role_id`, `role_name`) VALUES( 6,'VOLONTAR');

INSERT INTO permissions (perm_id, perm_desc) VALUES
( 1, 'Access users'),
( 2, 'Create new users'),
( 3, 'Update users'),
( 4, 'Delete users'),
(5, 'See controls');


INSERT INTO roles_permissions (role_id, perm_mod, perm_id) VALUES
(1, 'ADMIN', 1),
(1, 'ADMIN', 2),
(1, 'ADMIN', 3),
(1, 'ADMIN', 4),
(4, 'COMPETITORS', 5);


-- Användare
INSERT INTO `users` (`user_uid`, `user_name`, `given_name`, `family_name`, `role_id`, `password`) VALUES
('82fbb2ec-d998-4b8a-861f-46f2b0fdbc4e', 'admin@admin', 'Admin', 'Administratör', 1, sha1('admin')),
('ac6543a6-df1e-4c5b-95a1-565a00676603', 'volonta@volontar', 'Anders', 'Volontär', 6, sha1('volonteer')),
('e3b78c98-ffe5-4877-8491-258413c772e9', 'user@user', 'Jonas', 'Användare', 2, sha1('user'));

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
INSERT INTO competitors(competitor_uid, user_name, given_name, family_name, role_id,password) VALUES ('2922a6e9-9e32-4832-9575-b3d2eb3011b9','100','Pelle','Cyklist',4,sha1('test'));
INSERT INTO competitors(competitor_uid, user_name ,given_name, family_name,role_id,password) VALUES ('68f06a8c-8f08-45cc-8d20-d5e37ce658ba','200','Johan','Randonnéer',4,sha1('test1'));
INSERT INTO competitors(competitor_uid, user_name ,given_name, family_name,role_id,password) VALUES ('593edcab-5dcb-4916-829d-08ac536770ad','300','Kalle','Super Randonneur',4, sha1('test2'));

----- Utkast bygga banor
--Sites text Brännäset
INSERT INTO site(site_uid, place,  adress, description, location) VALUES ('8a13602-83dc-447d-a85f-13b943e23a42','Umeå','Broparken 1','Startplats','');
INSERT INTO site(site_uid, place,  adress, description, location) VALUES ('47e2e397-872a-49f9-8f5b-069d09f5855c','Brännäset','Brännsäset 1','Kontroll vid havet','');
INSERT INTO site(site_uid, place,  adress, description, location) VALUES ('e53d8d51-c5e1-4d25-a8d3-afe0646c1f13','Rödtjarn','','','');
INSERT INTO site(site_uid, place,  adress, description, location) VALUES ('e8a2df8a-7c0c-48f3-be9e-432d97e418ef','Circle K','Normaling','Matkontrll med vc','');
-- Olika event som pågår över tid
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`) VALUES ('f6bdbba8-960d-472b-8864-cda48a07eeac','Brm series 2022',STR_TO_DATE("01-05-22","%d-%m-%y"),STR_TO_DATE("01-09-22","%d-%m-%y"),false,false,false,'Ett event');
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`) VALUES ('ae5c2acd-8042-480b-b937-f3f416d7aeaa','Västerbottenbrevet 2022',STR_TO_DATE("07-08-22","%d-%m-%y"),STR_TO_DATE("07-08-22","%d-%m-%y"),false,false,false,'Brevet 2022');
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`) VALUES ('2d1767dc-f768-419b-8cdd-01fdfdbc44e4','Västerbottenbrevet 2023',STR_TO_DATE("07-08-23","%d-%m-%y"),STR_TO_DATE("07-08-23","%d-%m-%y"),false,false,false,'Brevet 2023');
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`) VALUES ('62c332d2-72c8-407c-b71c-ca2541d72577','Månskensbrevet',STR_TO_DATE("18-09-22","%d-%m-%y"),STR_TO_DATE("18-09-22","%d-%m-%y"),false,false,false,'Kvällscykling i fullmåne');

-- Banor som ingår i ett event
--BRM
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`) VALUES ('8a5a0649-6aee-4b64-803e-4f083f746d2d','BRM200K','http://www.banan.strava.com','f6bdbba8-960d-472b-8864-cda48a07eeac','200 k ....',200.3);
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`) VALUES ('0c9648fd-1664-4526-aaa4-059a01fc079c','BRM300K','http://www.banan.strava.com','f6bdbba8-960d-472b-8864-cda48a07eeac','300 k ....',300.3);
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`) VALUES ('8862bd72-f5af-45f5-a377-337f26cbd195','BRM400K','http://www.banan.strava.com','f6bdbba8-960d-472b-8864-cda48a07eeac','400 k ....',400.3);
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`) VALUES ('06ba3113-d95b-48c9-b0f9-f25bda5dad31','BRM600K','http://www.banan.strava.com','f6bdbba8-960d-472b-8864-cda48a07eeac','600 k ....',600.3);
-- Månskensbrev
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`) VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221','Månskensbrevet','http://www.banan.strava.com','62c332d2-72c8-407c-b71c-ca2541d72577','Månstensbrevet k ....',86.3);

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
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`) VALUES ('63e8f1de-22ad-416d-b181-4a1a004a2959','47e2e397-872a-49f9-8f5b-069d09f5855c','Test 1','[value-4]',33, TIME("08:00"),TIME("10:00"));
-- En kontroll rödtjärn
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`) VALUES ('6b86551e-e9b6-46f1-b411-3196e0f0f4e3','e53d8d51-c5e1-4d25-a8d3-afe0646c1f13','Test 2','[value-4]',60, TIME("11:00"),TIME("12:30"));
-- Mål broparken
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`) VALUES ('c0a8e4a4-a37a-4e9d-b59e-112519b4abc0','8a13602-83dc-447d-a85f-13b943e23a42','Test 3','[value-4]',86, TIME("13:00"),TIME("15:30"));


-- Koppling bana till  kontroll/er
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`) VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221','63e8f1de-22ad-416d-b181-4a1a004a2959');
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`) VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221','6b86551e-e9b6-46f1-b411-3196e0f0f4e3');
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`) VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221','c0a8e4a4-a37a-4e9d-b59e-112519b4abc0');


--incheckning på kontroll för en deltgare

