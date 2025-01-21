insert into api_keys
values (0, sha1('184fa1a0-9e75-4b5a-b9f0-604f6d643daf'));

-- Roller och behörigheter
INSERT INTO roles (`role_id`, `role_name`)
VALUES (1, 'ADMIN');
INSERT INTO roles (`role_id`, `role_name`)
VALUES (2, 'USER');
INSERT INTO roles (`role_id`, `role_name`)
VALUES (3, 'SUPERUSER');
INSERT INTO roles (`role_id`, `role_name`)
VALUES (4, 'COMPETITOR');
INSERT INTO roles (`role_id`, `role_name`)
VALUES (5, 'DEVELOPER');
INSERT INTO roles (`role_id`, `role_name`)
VALUES (6, 'VOLONTEER');


INSERT INTO `permission_type`(`type_id`, `type_desc`, `type`)
VALUES (1, 'Read permission', 'READ');
INSERT INTO `permission_type`(`type_id`, `type_desc`, `type`)
VALUES (2, 'Write permission', 'WRITE');
INSERT INTO `permission_type`(`type_id`, `type_desc`, `type`)
VALUES (3, 'Update permission', 'UPDATE');

INSERT INTO permissions (perm_id, perm_desc, type_id)
VALUES (1, 'USER', 1),
       (2, 'USER', 2),
       (3, 'USER', 3),
       (4, 'Delete users', 2),
       (5, 'READCONTROLS', 1),
       (6, 'UPDATE', 2),
       (7, 'CREATE', 2),
       (8, 'DELETE', 2),
       (9, 'READ', 1),
       (10, 'SITE', 1),
       (11, 'SITE', 2),
       (12, 'SITE', 3),
       (13, 'TRACK', 1),
       (14, 'TRACK', 2),
       (15, 'TRACK', 3),
       (16, 'EVENT', 1),
       (17, 'EVENT', 2),
       (18, 'EVENT', 3),
       (19, 'CHECKPOINT', 1),
       (20, 'CHECKPOINT', 2),
       (21, 'CHECKPOINT', 3),
       (22, 'ACPREPORT', 1),
       (23, 'ACPREPORT', 2),
       (24, 'ORGANIZER', 1),
       (25, 'ORGANIZER', 2),
       (26, 'ORGANIZER', 2);


INSERT INTO roles_permissions (role_id, perm_mod, perm_id)
VALUES (1, 'ADMIN', 1),
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
       (2, 'SITE', 10),
       (2, 'TRACK', 13),
       (2, 'EVENT', 17),
       (2, 'CHECKPOINT', 1),
       (4, 'COMPETITORS', 19),
       (1, 'ADMIN', 22),
       (1, 'ADMIN', 23),
       (1, 'ADMIN', 24),
       (1, 'ADMIN', 25);



INSERT INTO `organizers` (`organizer_id`, `name`, `email`, `phone`, `created_at`, `updated_at`)
VALUES (1, 'Randonneurs Laponia', 'organizer1@example.com', '1234567890', NOW(), NOW());
INSERT INTO `organizers` (`organizer_id`, `name`, `email`, `phone`, `created_at`, `updated_at`)
VALUES (2, 'Cykelintresset', 'organizer2@example.com', '1234567890', NOW(), NOW());

-- Användare
INSERT INTO `users` (`user_uid`, `user_name`, `given_name`, `family_name`, `password`, `organizer_id`)
VALUES ('82fbb2ec-d998-4b8a-861f-46f2b0fdbc4e', 'admin@admin', 'Admin', 'Administratör', sha1('admin'), 1),
       ('ac6543a6-df1e-4c5b-95a1-565a00676603', 'volonteer@volonteer', 'Anders', 'Volontär', sha1('volonteer'), 1),
       ('e3b78c98-ffe5-4877-8491-258413c772e9', 'user@user', 'Jonas', 'Användare', sha1('user'), 1),
       ('e3b78c98-ffe5-4877-8491-258413c772a8', 'bethem92@admin', 'Bethem', 'Admin', sha1('admin'), 2);

INSERT INTO `user_info`(`uid`, `user_uid`, `email`, `phone`)
VALUES ('5e125776-2005-43a7-b58a-a1fc960ce9f4', '82fbb2ec-d998-4b8a-861f-46f2b0fdbc4e', 'admin@administrator.se',
        '0703105900');

-- Lägg till lite behörigheter
-- Admin + superuser
INSERT INTO user_role(role_id, user_uid)
VALUES (1, '82fbb2ec-d998-4b8a-861f-46f2b0fdbc4e');
INSERT INTO user_role(role_id, user_uid)
VALUES (3, '82fbb2ec-d998-4b8a-861f-46f2b0fdbc4e');
-- Volontär
INSERT INTO user_role(role_id, user_uid)
VALUES (6, 'ac6543a6-df1e-4c5b-95a1-565a00676603');
-- Vanlig användare
INSERT INTO user_role(role_id, user_uid)
VALUES (2, 'e3b78c98-ffe5-4877-8491-258413c772e9');
-- Cyklist
INSERT INTO user_role(role_id, user_uid)
VALUES (2, 'e3b78c98-ffe5-4877-8491-258413c772e9');

-- Cyklister
INSERT INTO competitors(competitor_uid, user_name, given_name, family_name, role_id, password, birthdate, gender)
VALUES ('2922a6e9-9e32-4832-9575-b3d2eb3011b9', '100', 'Pelle', 'Cyklist', 4, sha1('test'), DATE ('1973-06-15'), 2);
INSERT INTO competitors(competitor_uid, user_name, given_name, family_name, role_id, password, birthdate, gender)
VALUES ('68f06a8c-8f08-45cc-8d20-d5e37ce658ba', '200', 'Johan', 'Randonnéer', 4, sha1('test1'), DATE ('1980-08-15'), 2);
INSERT INTO competitors(competitor_uid, user_name, given_name, family_name, role_id, password, birthdate, gender)
VALUES ('593edcab-5dcb-4916-829d-08ac536770ad', '300', 'Kalle', 'Super Randonneur', 4, sha1('test2'),
        DATE ('1990-03-04'), 2);

INSERT INTO `competitor_info`(`uid`, `competitor_uid`, `email`, `phone`, `adress`, `postal_code`, `place`, `country`,
                              country_id)
VALUES ('31a852b0-23ec-4689-b4cf-0c970f9b90fd', '2922a6e9-9e32-4832-9575-b3d2eb3011b9', 'democyklist@test.se',
        '0703158465', 'cyklistgatan 15', '90100', 'cykelby', 'Sweden', 145);

-- Utkast bygga banor
-- Sites text Brännäset
INSERT INTO site(site_uid, place, adress, description, lat, lng, location)
VALUES ('8a13602-83dc-447d-a85f-13b943e23a42', 'Umeå', 'Broparken 1', 'Startplats', 20.3128832, 63.7042688, '');
INSERT INTO site(site_uid, place, adress, description, lat, lng, location)
VALUES ('47e2e397-872a-49f9-8f5b-069d09f5855c', 'Brännäset', 'Brännsäset 1', 'Kontroll vid havet', null, null, '');
INSERT INTO site(site_uid, place, adress, description, lat, lng, location)
VALUES ('e53d8d51-c5e1-4d25-a8d3-afe0646c1f13', 'Rödtjarn', '', '', null, null, '');
INSERT INTO site(site_uid, place, adress, description, lat, lng, location)
VALUES ('e8a2df8a-7c0c-48f3-be9e-432d97e418ef', 'Circle K', 'Normaling', 'Matkontroll med vc', null, null, '');
-- Olika event som pågår över tid
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`,
                    `organizer_id`)
VALUES ('f6bdbba8-960d-472b-8864-cda48a07eeac', 'Brm series 2022', STR_TO_DATE("01-05-22", "%d-%m-%y"),
        STR_TO_DATE("01-09-22", "%d-%m-%y"), false, false, true, 'Brm serien 2022', 1);
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`,
                    `organizer_id`)
VALUES ('ae5c2acd-8042-480b-b937-f3f416d7aeaa', 'Västerbottenbrevet 2022', STR_TO_DATE("07-08-22", "%d-%m-%y"),
        STR_TO_DATE("07-08-22", "%d-%m-%y"), false, false, true, 'Brevet 2022', 1);
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`,
                    `organizer_id`)
VALUES ('2d1767dc-f768-419b-8cdd-01fdfdbc44e4', 'Västerbottenbrevet 2023', STR_TO_DATE("07-08-23", "%d-%m-%y"),
        STR_TO_DATE("07-08-23", "%d-%m-%y"), false, false, false, 'Brevet 2023', 1);
INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`,
                    `organizer_id`)
VALUES ('62c332d2-72c8-407c-b71c-ca2541d72577', 'Månskensbrevet 2022', STR_TO_DATE("18-09-22", "%d-%m-%y"),
        STR_TO_DATE("18-09-22", "%d-%m-%y"), false, false, true, 'Kvällscykling i fullmåne', 1);

INSERT INTO `event`(`event_uid`, `title`, `start_date`, `end_date`, `active`, `canceled`, `completed`, `description`,
                    `organizer_id`)
VALUES ('dc03677a-51eb-4269-bce1-b7a6a15dd7b3', 'Testbana för skapa deltagare', STR_TO_DATE("07-08-23", "%d-%m-%y"),
        STR_TO_DATE("07-08-23", "%d-%m-%y"), false, false, false, 'TEST skapa deltagare', 2);

-- Banor som ingår i ett event
-- BRM
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`, start_date_time, active)
VALUES ('8a5a0649-6aee-4b64-803e-4f083f746d2d', 'BRM200K', 'http://www.banan.strava.com',
        'f6bdbba8-960d-472b-8864-cda48a07eeac', '200 k ....', 200.3, STR_TO_DATE("07-07-23", "%d-%m-%y"), true);
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`, start_date_time, active)
VALUES ('0c9648fd-1664-4526-aaa4-059a01fc079c', 'BRM300K', 'http://www.banan.strava.com',
        'f6bdbba8-960d-472b-8864-cda48a07eeac', '300 k ....', 300.3, STR_TO_DATE("07-05-23", "%d-%m-%y"), true);
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`, start_date_time, active)
VALUES ('8862bd72-f5af-45f5-a377-337f26cbd195', 'BRM400K', 'http://www.banan.strava.com',
        'f6bdbba8-960d-472b-8864-cda48a07eeac', '400 k ....', 400.3, STR_TO_DATE("07-05-24", "%d-%m-%y"), true);
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`, start_date_time, active)
VALUES ('06ba3113-d95b-48c9-b0f9-f25bda5dad31', 'BRM600K', 'http://www.banan.strava.com',
        'f6bdbba8-960d-472b-8864-cda48a07eeac', '600 k ....', 600.3, STR_TO_DATE("07-09-24", "%d-%m-%y"), true);


-- Månskensbrev
INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`, start_date_time, active)
VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221', 'Månskensbrevet', 'http://www.banan.strava.com',
        '62c332d2-72c8-407c-b71c-ca2541d72577', 'Månstensbrevet k ....', 86.3, DATE ('2024-09-04'), true);

INSERT INTO `track`(`track_uid`, `title`, `link`, `event_uid`, `description`, `distance`, start_date_time, active)
VALUES ('c9dd3c38-6860-4a0d-bfed-eebff6b8b8a1', 'BRM 200K Kramfors', 'http://www.banan.strava.com',
        'dc03677a-51eb-4269-bce1-b7a6a15dd7b3', 'BRM 200k ....', 86.3, DATE ('2024-09-04'), true);



-- Koppling mellan banor och event
-- Här BRM banor
INSERT INTO `event_tracks`(`track_uid`, `event_uid`)
VALUES ('8a5a0649-6aee-4b64-803e-4f083f746d2d', 'f6bdbba8-960d-472b-8864-cda48a07eeac');
INSERT INTO `event_tracks`(`track_uid`, `event_uid`)
VALUES ('0c9648fd-1664-4526-aaa4-059a01fc079c', 'f6bdbba8-960d-472b-8864-cda48a07eeac');
INSERT INTO `event_tracks`(`track_uid`, `event_uid`)
VALUES ('8862bd72-f5af-45f5-a377-337f26cbd195', 'f6bdbba8-960d-472b-8864-cda48a07eeac');
INSERT INTO `event_tracks`(`track_uid`, `event_uid`)
VALUES ('06ba3113-d95b-48c9-b0f9-f25bda5dad31', 'f6bdbba8-960d-472b-8864-cda48a07eeac');
-- TESTBANA skapa deltagare
INSERT INTO `event_tracks`(`track_uid`, `event_uid`)
VALUES ('c9dd3c38-6860-4a0d-bfed-eebff6b8b8a1', 'dc03677a-51eb-4269-bce1-b7a6a15dd7b3');


-- Månskensbrev kopplad till banor
INSERT INTO `event_tracks`(`track_uid`, `event_uid`)
VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221', '62c332d2-72c8-407c-b71c-ca2541d72577');

-- Kontroller kopplade till en plats
-- En kontroll brännäset
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`)
VALUES ('63e8f1de-22ad-416d-b181-4a1a004a2959', '47e2e397-872a-49f9-8f5b-069d09f5855c', 'Test 1', 'DEMO', 33, TIME ("2021-06-15 09:00:00"),
        ("2021-06-15 10:00:00"));
-- En kontroll rödtjärn
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`)
VALUES ('6b86551e-e9b6-46f1-b411-3196e0f0f4e3', 'e53d8d51-c5e1-4d25-a8d3-afe0646c1f13', 'Test 2', 'DEMO', 60, TIME ("2021-06-15 11:00:00"),
        TIME ("2021-06-15 12:30:00"));
-- Mål broparken
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`)
VALUES ('c0a8e4a4-a37a-4e9d-b59e-112519b4abc0', '8a13602-83dc-447d-a85f-13b943e23a42', 'Test 3', 'DEMO', 86, TIME ("2021-06-15 08:00:00"),
        TIME ("2021-06-15 09:00:00"));


-- Testbana skapa deltagare. OBS ÄNDRA site SEN
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`)
VALUES ('6af9f3c5-d9c4-4bb0-959b-8d3700a1bf85', '47e2e397-872a-49f9-8f5b-069d09f5855c', 'Test 1', 'DEMO', 33, TIME ("2021-06-15 09:00:00"),
        ("2021-06-15 10:00:00"));
-- En kontroll rödtjärn
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`)
VALUES ('614e66c9-9d83-4144-b7f8-4effd01386f9', 'e53d8d51-c5e1-4d25-a8d3-afe0646c1f13', 'Test 2', 'DEMO', 60, TIME ("2021-06-15 11:00:00"),
        TIME ("2021-06-15 12:30:00"));
-- Mål broparken
INSERT INTO `checkpoint`(`checkpoint_uid`, `site_uid`, `title`, `description`, `distance`, `opens`, `closing`)
VALUES ('b340120e-f031-4040-bc86-4145fb74cd29 ', '8a13602-83dc-447d-a85f-13b943e23a42', 'Test 3', 'DEMO', 86, TIME ("2021-06-15 08:00:00"),
        TIME ("2021-06-15 09:00:00"));


-- Koppling bana till  kontroll/er
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`)
VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221', '63e8f1de-22ad-416d-b181-4a1a004a2959');
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`)
VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221', '6b86551e-e9b6-46f1-b411-3196e0f0f4e3');
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`)
VALUES ('bf31d141-32c3-4cc9-b497-36d82b060221', 'c0a8e4a4-a37a-4e9d-b59e-112519b4abc0');

-- Koppling testbana
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`)
VALUES ('c9dd3c38-6860-4a0d-bfed-eebff6b8b8a1', '6af9f3c5-d9c4-4bb0-959b-8d3700a1bf85');
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`)
VALUES ('c9dd3c38-6860-4a0d-bfed-eebff6b8b8a1', '614e66c9-9d83-4144-b7f8-4effd01386f9');
INSERT INTO `track_checkpoint`(`track_uid`, `checkpoint_uid`)
VALUES ('c9dd3c38-6860-4a0d-bfed-eebff6b8b8a1', 'b340120e-f031-4040-bc86-4145fb74cd29 ');

-- Deltagare på en viss bana
-- BRM200
INSERT INTO `participant`(`participant_uid`, `track_uid`, `competitor_uid`, `startnumber`, `finished`, `acpkod`,
                          `club_uid`, `time`, `dns`, `dnf`, `register_date_time`)
VALUES ('b3f68992-c5c7-4c31-bad5-78a93b53f28f', '8a5a0649-6aee-4b64-803e-4f083f746d2d',
        '2922a6e9-9e32-4832-9575-b3d2eb3011b9', 1018, false, 113072, 'e990365b-00a8-4615-a648-c7b6797ce13a', null,
        false, false, CURRENT_TIMESTAMP());
INSERT INTO `participant`(`participant_uid`, `track_uid`, `competitor_uid`, `startnumber`, `finished`, `acpkod`,
                          `club_uid`, `time`, `dns`, `dnf`, `register_date_time`)
VALUES ('162b49ea-1e8c-4047-8fd6-e4d96920a054', '8a5a0649-6aee-4b64-803e-4f083f746d2d',
        '593edcab-5dcb-4916-829d-08ac536770ad', 2018, false, 113036, '31f10de0-33c4-49da-a8fe-4cc2354604bc', null,
        false, false, CURRENT_TIMESTAMP());
-- BRM300
INSERT INTO `participant`(`participant_uid`, `track_uid`, `competitor_uid`, `startnumber`, `finished`, `acpkod`,
                          `club_uid`, `time`, `dns`, `dnf`, `register_date_time`)
VALUES ('e6957ddc-f9fd-444f-861f-f0f22cc363b1', '0c9648fd-1664-4526-aaa4-059a01fc079c',
        '593edcab-5dcb-4916-829d-08ac536770ad', 2018, false, 113036, '427b5419-ebad-4a31-bd84-ed26718a32be', null,
        false, false, CURRENT_TIMESTAMP());
-- Månskensbrevet
INSERT INTO `participant`(`participant_uid`, `track_uid`, `competitor_uid`, `startnumber`, `finished`, `acpkod`,
                          `club_uid`, `time`, `dns`, `dnf`, `register_date_time`)
VALUES ('e8f9557e-4b96-41a0-b6c7-be6c45d81259', 'bf31d141-32c3-4cc9-b497-36d82b060221',
        '2922a6e9-9e32-4832-9575-b3d2eb3011b9', 1020, false, 113072, 'be80dc3b-3ff7-414f-ad7f-41db57e90221', null,
        false, false, CURRENT_TIMESTAMP());

-- Koppla till kontroll
-- BRM200
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time`,
                                     `volonteer_checkin`, lat, lng)
VALUES ('b3f68992-c5c7-4c31-bad5-78a93b53f28f', '63e8f1de-22ad-416d-b181-4a1a004a2959', false, null, false, null, null);
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time`,
                                     `volonteer_checkin`, lat, lng)
VALUES ('162b49ea-1e8c-4047-8fd6-e4d96920a054', '63e8f1de-22ad-416d-b181-4a1a004a2959', false, null, false, null, null);
-- BRM300
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time`,
                                     `volonteer_checkin`, lat, lng)
VALUES ('e6957ddc-f9fd-444f-861f-f0f22cc363b1', '6b86551e-e9b6-46f1-b411-3196e0f0f4e3', false, null, false, null, null);
-- Månskensbrev
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time`,
                                     `volonteer_checkin`, lat, lng)
VALUES ('e8f9557e-4b96-41a0-b6c7-be6c45d81259', '63e8f1de-22ad-416d-b181-4a1a004a2959', false, null, false, null, null);
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time`,
                                     `volonteer_checkin`, lat, lng)
VALUES ('e8f9557e-4b96-41a0-b6c7-be6c45d81259', '6b86551e-e9b6-46f1-b411-3196e0f0f4e3', false, null, false, null, null);
INSERT INTO `participant_checkpoint`(`participant_uid`, `checkpoint_uid`, `passed`, `passeded_date_time`,
                                     `volonteer_checkin`, lat, lng)
VALUES ('e8f9557e-4b96-41a0-b6c7-be6c45d81259', 'c0a8e4a4-a37a-4e9d-b59e-112519b4abc0', false, null, false, null, null);

-- Fixa inloggning
INSERT INTO `competitor_credential`(`credential_uid`, `competitor_uid`, `participant_uid`, `user_name`, `password`)
VALUES ('8cdee576-1de3-454e-90f9-33d7c2c070ce', '2922a6e9-9e32-4832-9575-b3d2eb3011b9',
        'b3f68992-c5c7-4c31-bad5-78a93b53f28f', '100', sha1('test'));


-- Klubbar
insert into club (club_uid, acp_kod, title)
VALUES ('e990365b-00a8-4615-a648-c7b6797ce13a', 113072, 'Cykelintresset');
insert into club (club_uid, acp_kod, title)
VALUES ('31f10de0-33c4-49da-a8fe-4cc2354604bc', 113036, 'Gimonäs CK');
insert into club (club_uid, acp_kod, title)
VALUES ('427b5419-ebad-4a31-bd84-ed26718a32be', 113110, 'Sävar CK');
insert into club (club_uid, acp_kod, title)
VALUES ('6343a43b-dc0f-4415-ac56-50f000aca4d6', 113015, 'Höga kusten Cyklisterna');
insert into club (club_uid, acp_kod, title)
VALUES ('e26e0a36-1996-4980-b597-164470b7bdea', 113107, 'CK Örnen');
insert into club (club_uid, acp_kod, title)
VALUES ('8f0dc40d-e91e-4cc3-ad23-86ca481e0b32', 113099, 'Individuell');
insert into club (club_uid, acp_kod, title)
VALUES ('9547515e-6cd9-42dc-9dfa-cfae262907ee', 113111, 'CK Guldkedjan');
insert into club (club_uid, acp_kod, title)
VALUES ('6907d03f-3257-42b2-8941-09816a979771', 110108, 'She Rides C');


INSERT INTO `countries` (`country_id`, `country_name_en`, `country_name_sv`, `country_code`, `flag_url_svg`,
                         `flag_url_png`, `created_at`, `updated_at`)
VALUES (1, 'French Polynesia', 'Franska Polynesien', 'PF', 'https://flagcdn.com/pf.svg',
        'https://flagcdn.com/w320/pf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (2, 'Saint Martin', 'Saint-Martin', 'MF', 'https://flagcdn.com/mf.svg', 'https://flagcdn.com/w320/mf.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (3, 'Venezuela', 'Venezuela', 'VE', 'https://flagcdn.com/ve.svg', 'https://flagcdn.com/w320/ve.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (4, 'Réunion', 'Réunion', 'RE', 'https://flagcdn.com/re.svg', 'https://flagcdn.com/w320/re.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (5, 'El Salvador', 'El Salvador', 'SV', 'https://flagcdn.com/sv.svg', 'https://flagcdn.com/w320/sv.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (6, 'Dominica', 'Dominica', 'DM', 'https://flagcdn.com/dm.svg', 'https://flagcdn.com/w320/dm.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (7, 'Gibraltar', 'Gibraltar', 'GI', 'https://flagcdn.com/gi.svg', 'https://flagcdn.com/w320/gi.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (8, 'Kenya', 'Kenya', 'KE', 'https://flagcdn.com/ke.svg', 'https://flagcdn.com/w320/ke.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (9, 'Brazil', 'Brasilien', 'BR', 'https://flagcdn.com/br.svg', 'https://flagcdn.com/w320/br.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (10, 'Maldives', 'Maldiverna', 'MV', 'https://flagcdn.com/mv.svg', 'https://flagcdn.com/w320/mv.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (11, 'United States', 'USA', 'US', 'https://flagcdn.com/us.svg', 'https://flagcdn.com/w320/us.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (12, 'Cook Islands', 'Cooköarna', 'CK', 'https://flagcdn.com/ck.svg', 'https://flagcdn.com/w320/ck.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (13, 'Niue', 'Niue', 'NU', 'https://flagcdn.com/nu.svg', 'https://flagcdn.com/w320/nu.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (14, 'Seychelles', 'Seychellerna', 'SC', 'https://flagcdn.com/sc.svg', 'https://flagcdn.com/w320/sc.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (15, 'Central African Republic', 'Centralafrikanska republiken', 'CF', 'https://flagcdn.com/cf.svg',
        'https://flagcdn.com/w320/cf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (16, 'Tokelau', 'Tokelauöarna', 'TK', 'https://flagcdn.com/tk.svg', 'https://flagcdn.com/w320/tk.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (17, 'Vanuatu', 'Vanuatu', 'VU', 'https://flagcdn.com/vu.svg', 'https://flagcdn.com/w320/vu.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (18, 'Gambia', 'Gambia', 'GM', 'https://flagcdn.com/gm.svg', 'https://flagcdn.com/w320/gm.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (19, 'Guyana', 'Guyana', 'GY', 'https://flagcdn.com/gy.svg', 'https://flagcdn.com/w320/gy.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (20, 'Falkland Islands', 'Falklandsöarna', 'FK', 'https://flagcdn.com/fk.svg', 'https://flagcdn.com/w320/fk.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (21, 'Belgium', 'Belgien', 'BE', 'https://flagcdn.com/be.svg', 'https://flagcdn.com/w320/be.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (22, 'Western Sahara', 'Västsahara', 'EH', 'https://flagcdn.com/eh.svg', 'https://flagcdn.com/w320/eh.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (23, 'Turkey', 'Turkiet', 'TR', 'https://flagcdn.com/tr.svg', 'https://flagcdn.com/w320/tr.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (24, 'Saint Vincent and the Grenadines', 'Saint Vincent och Grenadinerna', 'VC', 'https://flagcdn.com/vc.svg',
        'https://flagcdn.com/w320/vc.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (25, 'Pakistan', 'Pakistan', 'PK', 'https://flagcdn.com/pk.svg', 'https://flagcdn.com/w320/pk.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (26, 'Åland Islands', 'Åland', 'AX', 'https://flagcdn.com/ax.svg', 'https://flagcdn.com/w320/ax.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (27, 'Iran', 'Iran', 'IR', 'https://flagcdn.com/ir.svg', 'https://flagcdn.com/w320/ir.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (28, 'Indonesia', 'Indonesien', 'ID', 'https://flagcdn.com/id.svg', 'https://flagcdn.com/w320/id.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (29, 'New Zealand', 'Nya Zeeland', 'NZ', 'https://flagcdn.com/nz.svg', 'https://flagcdn.com/w320/nz.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (30, 'Afghanistan', 'Afghanistan', 'AF',
        'https://upload.wikimedia.org/wikipedia/commons/5/5c/Flag_of_the_Taliban.svg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Flag_of_the_Taliban.svg/320px-Flag_of_the_Taliban.svg.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (31, 'Guam', 'Guam', 'GU', 'https://flagcdn.com/gu.svg', 'https://flagcdn.com/w320/gu.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (32, 'Albania', 'Albanien', 'AL', 'https://flagcdn.com/al.svg', 'https://flagcdn.com/w320/al.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (33, 'DR Congo', 'Kongo-Kinshasa', 'CD', 'https://flagcdn.com/cd.svg', 'https://flagcdn.com/w320/cd.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (34, 'Ivory Coast', 'Elfenbenskusten', 'CI', 'https://flagcdn.com/ci.svg', 'https://flagcdn.com/w320/ci.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (35, 'Sudan', 'Sudan', 'SD', 'https://flagcdn.com/sd.svg', 'https://flagcdn.com/w320/sd.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (36, 'Timor-Leste', 'Östtimor', 'TL', 'https://flagcdn.com/tl.svg', 'https://flagcdn.com/w320/tl.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (37, 'Luxembourg', 'Luxemburg', 'LU', 'https://flagcdn.com/lu.svg', 'https://flagcdn.com/w320/lu.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (38, 'Saudi Arabia', 'Saudiarabien', 'Saudi', 'https://flagcdn.com/sa.svg', 'https://flagcdn.com/w320/sa.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (39, 'Cambodia', 'Kambodja', 'KH', 'https://flagcdn.com/kh.svg', 'https://flagcdn.com/w320/kh.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (40, 'Nepal', 'Nepal', 'NP', 'https://flagcdn.com/np.svg', 'https://flagcdn.com/w320/np.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (41, 'French Guiana', 'Franska Guyana', 'GF', 'https://flagcdn.com/gf.svg', 'https://flagcdn.com/w320/gf.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (42, 'Malaysia', 'Malaysia', 'MY', 'https://flagcdn.com/my.svg', 'https://flagcdn.com/w320/my.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (43, 'Rwanda', 'Rwanda', 'RW', 'https://flagcdn.com/rw.svg', 'https://flagcdn.com/w320/rw.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (44, 'Thailand', 'Thailand', 'TH', 'https://flagcdn.com/th.svg', 'https://flagcdn.com/w320/th.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (45, 'Antarctica', 'Antarktis', 'AQ', 'https://flagcdn.com/aq.svg', 'https://flagcdn.com/w320/aq.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (46, 'Jordan', 'Jordanien', 'JO', 'https://flagcdn.com/jo.svg', 'https://flagcdn.com/w320/jo.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (47, 'Switzerland', 'Schweiz', 'CH', 'https://flagcdn.com/ch.svg', 'https://flagcdn.com/w320/ch.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (48, 'Comoros', 'Komorerna', 'KM', 'https://flagcdn.com/km.svg', 'https://flagcdn.com/w320/km.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (49, 'Kosovo', 'Kosovo', 'XK', 'https://flagcdn.com/xk.svg', 'https://flagcdn.com/w320/xk.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (50, 'Isle of Man', 'Isle of Man', 'IM', 'https://flagcdn.com/im.svg', 'https://flagcdn.com/w320/im.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (51, 'Montenegro', 'Montenegro', 'ME', 'https://flagcdn.com/me.svg', 'https://flagcdn.com/w320/me.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (52, 'Hong Kong', 'Hongkong', 'HK', 'https://flagcdn.com/hk.svg', 'https://flagcdn.com/w320/hk.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (53, 'Jersey', 'Jersey', 'JE', 'https://flagcdn.com/je.svg', 'https://flagcdn.com/w320/je.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (54, 'Tajikistan', 'Tadzjikistan', 'TJ', 'https://flagcdn.com/tj.svg', 'https://flagcdn.com/w320/tj.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (55, 'Bulgaria', 'Bulgarien', 'BG', 'https://flagcdn.com/bg.svg', 'https://flagcdn.com/w320/bg.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (56, 'Egypt', 'Egypten', 'EG', 'https://flagcdn.com/eg.svg', 'https://flagcdn.com/w320/eg.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (57, 'Malawi', 'Malawi', 'MW', 'https://flagcdn.com/mw.svg', 'https://flagcdn.com/w320/mw.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (58, 'Cape Verde', 'Kap Verde', 'CV', 'https://flagcdn.com/cv.svg', 'https://flagcdn.com/w320/cv.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (59, 'Benin', 'Benin', 'BJ', 'https://flagcdn.com/bj.svg', 'https://flagcdn.com/w320/bj.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (60, 'Morocco', 'Marocko', 'MA', 'https://flagcdn.com/ma.svg', 'https://flagcdn.com/w320/ma.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (61, 'Ireland', 'Irland', 'IE', 'https://flagcdn.com/ie.svg', 'https://flagcdn.com/w320/ie.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (62, 'Moldova', 'Moldavien', 'MD', 'https://flagcdn.com/md.svg', 'https://flagcdn.com/w320/md.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (63, 'Denmark', 'Danmark', 'DK', 'https://flagcdn.com/dk.svg', 'https://flagcdn.com/w320/dk.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (64, 'Turkmenistan', 'Turkmenistan', 'TM', 'https://flagcdn.com/tm.svg', 'https://flagcdn.com/w320/tm.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (65, 'Micronesia', 'Mikronesiska federationen', 'FM', 'https://flagcdn.com/fm.svg',
        'https://flagcdn.com/w320/fm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (66, 'Monaco', 'Monaco', 'MC', 'https://flagcdn.com/mc.svg', 'https://flagcdn.com/w320/mc.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (67, 'Barbados', 'Barbados', 'BB', 'https://flagcdn.com/bb.svg', 'https://flagcdn.com/w320/bb.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (68, 'Algeria', 'Algeriet', 'DZ', 'https://flagcdn.com/dz.svg', 'https://flagcdn.com/w320/dz.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (69, 'French Southern and Antarctic Lands', 'Franska södra territorierna', 'TF', 'https://flagcdn.com/tf.svg',
        'https://flagcdn.com/w320/tf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (70, 'Eritrea', 'Eritrea', 'ER', 'https://flagcdn.com/er.svg', 'https://flagcdn.com/w320/er.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (71, 'Lesotho', 'Lesotho', 'LS', 'https://flagcdn.com/ls.svg', 'https://flagcdn.com/w320/ls.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (72, 'Tanzania', 'Tanzania', 'TZ', 'https://flagcdn.com/tz.svg', 'https://flagcdn.com/w320/tz.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (73, 'Mali', 'Mali', 'ML', 'https://flagcdn.com/ml.svg', 'https://flagcdn.com/w320/ml.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (74, 'Niger', 'Niger', 'NE', 'https://flagcdn.com/ne.svg', 'https://flagcdn.com/w320/ne.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (75, 'Andorra', 'Andorra', 'AD', 'https://flagcdn.com/ad.svg', 'https://flagcdn.com/w320/ad.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (76, 'United Kingdom', 'Storbritannien', 'GB', 'https://flagcdn.com/gb.svg', 'https://flagcdn.com/w320/gb.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (77, 'Germany', 'Tyskland', 'DE', 'https://flagcdn.com/de.svg', 'https://flagcdn.com/w320/de.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (78, 'United States Virgin Islands', 'Amerikanska Jungfruöarna', 'VI', 'https://flagcdn.com/vi.svg',
        'https://flagcdn.com/w320/vi.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (79, 'Somalia', 'Somalia', 'SO', 'https://flagcdn.com/so.svg', 'https://flagcdn.com/w320/so.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (80, 'Sint Maarten', 'Sint Maarten', 'SX', 'https://flagcdn.com/sx.svg', 'https://flagcdn.com/w320/sx.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (81, 'Cameroon', 'Kamerun', 'CM', 'https://flagcdn.com/cm.svg', 'https://flagcdn.com/w320/cm.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (82, 'Dominican Republic', 'Dominikanska republiken', 'DO', 'https://flagcdn.com/do.svg',
        'https://flagcdn.com/w320/do.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (83, 'Guinea', 'Guinea', 'GN', 'https://flagcdn.com/gn.svg', 'https://flagcdn.com/w320/gn.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (84, 'Namibia', 'Namibia', 'NA', 'https://flagcdn.com/na.svg', 'https://flagcdn.com/w320/na.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (85, 'Montserrat', 'Montserrat', 'MS', 'https://flagcdn.com/ms.svg', 'https://flagcdn.com/w320/ms.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (86, 'South Georgia', 'Sydgeorgien', 'GS', 'https://flagcdn.com/gs.svg', 'https://flagcdn.com/w320/gs.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (87, 'Senegal', 'Senegal', 'SN', 'https://flagcdn.com/sn.svg', 'https://flagcdn.com/w320/sn.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (88, 'Bouvet Island', 'Bouvetön', 'BV', 'https://flagcdn.com/bv.svg', 'https://flagcdn.com/w320/bv.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (89, 'Solomon Islands', 'Salomonöarna', 'SB', 'https://flagcdn.com/sb.svg', 'https://flagcdn.com/w320/sb.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (90, 'France', 'Frankrike', 'FR', 'https://flagcdn.com/fr.svg', 'https://flagcdn.com/w320/fr.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (91, 'Saint Helena, Ascension and Tristan da Cunha', 'Sankta Helena', 'Saint Helena',
        'https://flagcdn.com/sh.svg', 'https://flagcdn.com/w320/sh.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (92, 'Macau', 'Macao', 'MO', 'https://flagcdn.com/mo.svg', 'https://flagcdn.com/w320/mo.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (93, 'Argentina', 'Argentina', 'AR', 'https://flagcdn.com/ar.svg', 'https://flagcdn.com/w320/ar.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (94, 'Bosnia and Herzegovina', 'Bosnien och Hercegovina', 'BA', 'https://flagcdn.com/ba.svg',
        'https://flagcdn.com/w320/ba.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (95, 'Anguilla', 'Anguilla', 'AI', 'https://flagcdn.com/ai.svg', 'https://flagcdn.com/w320/ai.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (96, 'Guernsey', 'Guernsey', 'GG', 'https://flagcdn.com/gg.svg', 'https://flagcdn.com/w320/gg.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (97, 'Djibouti', 'Djibouti', 'DJ', 'https://flagcdn.com/dj.svg', 'https://flagcdn.com/w320/dj.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (98, 'Saint Kitts and Nevis', 'Saint Kitts och Nevis', 'KN', 'https://flagcdn.com/kn.svg',
        'https://flagcdn.com/w320/kn.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (99, 'Syria', 'Syrien', 'SY', 'https://flagcdn.com/sy.svg', 'https://flagcdn.com/w320/sy.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (100, 'Puerto Rico', 'Puerto Rico', 'PR', 'https://flagcdn.com/pr.svg', 'https://flagcdn.com/w320/pr.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (101, 'Peru', 'Peru', 'PE', 'https://flagcdn.com/pe.svg', 'https://flagcdn.com/w320/pe.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (102, 'San Marino', 'San Marino', 'SM', 'https://flagcdn.com/sm.svg', 'https://flagcdn.com/w320/sm.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (103, 'Australia', 'Australien', 'AU', 'https://flagcdn.com/au.svg', 'https://flagcdn.com/w320/au.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (104, 'New Caledonia', 'Nya Kaledonien', 'NC', 'https://flagcdn.com/nc.svg', 'https://flagcdn.com/w320/nc.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (105, 'Jamaica', 'Jamaica', 'JM', 'https://flagcdn.com/jm.svg', 'https://flagcdn.com/w320/jm.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (106, 'Kazakhstan', 'Kazakstan', 'KZ', 'https://flagcdn.com/kz.svg', 'https://flagcdn.com/w320/kz.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (107, 'Sierra Leone', 'Sierra Leone', 'SL', 'https://flagcdn.com/sl.svg', 'https://flagcdn.com/w320/sl.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (108, 'Palau', 'Palau', 'PW', 'https://flagcdn.com/pw.svg', 'https://flagcdn.com/w320/pw.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (109, 'South Korea', 'Sydkorea', 'KR', 'https://flagcdn.com/kr.svg', 'https://flagcdn.com/w320/kr.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (110, 'Saint Pierre and Miquelon', 'Saint-Pierre och Miquelon', 'PM', 'https://flagcdn.com/pm.svg',
        'https://flagcdn.com/w320/pm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (111, 'Belize', 'Belize', 'BZ', 'https://flagcdn.com/bz.svg', 'https://flagcdn.com/w320/bz.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (112, 'Papua New Guinea', 'Papua Nya Guinea', 'PG', 'https://flagcdn.com/pg.svg',
        'https://flagcdn.com/w320/pg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (113, 'Iceland', 'Island', 'IS', 'https://flagcdn.com/is.svg', 'https://flagcdn.com/w320/is.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (114, 'American Samoa', 'Amerikanska Samoa', 'AS', 'https://flagcdn.com/as.svg',
        'https://flagcdn.com/w320/as.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (115, 'Burkina Faso', 'Burkina Faso', 'BF', 'https://flagcdn.com/bf.svg', 'https://flagcdn.com/w320/bf.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (116, 'Portugal', 'Portugal', 'PT', 'https://flagcdn.com/pt.svg', 'https://flagcdn.com/w320/pt.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (117, 'Taiwan', 'Taiwan', 'TW', 'https://flagcdn.com/tw.svg', 'https://flagcdn.com/w320/tw.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (118, 'Japan', 'Japan', 'JP', 'https://flagcdn.com/jp.svg', 'https://flagcdn.com/w320/jp.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (119, 'China', 'Kina', 'CN', 'https://flagcdn.com/cn.svg', 'https://flagcdn.com/w320/cn.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (120, 'Lebanon', 'Libanon', 'LB', 'https://flagcdn.com/lb.svg', 'https://flagcdn.com/w320/lb.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (121, 'Sri Lanka', 'Sri Lanka', 'LK', 'https://flagcdn.com/lk.svg', 'https://flagcdn.com/w320/lk.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (122, 'Guatemala', 'Guatemala', 'GT', 'https://flagcdn.com/gt.svg', 'https://flagcdn.com/w320/gt.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (123, 'Serbia', 'Serbien', 'RS', 'https://flagcdn.com/rs.svg', 'https://flagcdn.com/w320/rs.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (124, 'Madagascar', 'Madagaskar', 'MG', 'https://flagcdn.com/mg.svg', 'https://flagcdn.com/w320/mg.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (125, 'Eswatini', 'Swaziland', 'SZ', 'https://flagcdn.com/sz.svg', 'https://flagcdn.com/w320/sz.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (126, 'Romania', 'Rumänien', 'RO', 'https://flagcdn.com/ro.svg', 'https://flagcdn.com/w320/ro.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (127, 'Antigua and Barbuda', 'Antigua och Barbuda', 'AG', 'https://flagcdn.com/ag.svg',
        'https://flagcdn.com/w320/ag.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (128, 'Curaçao', 'Curaçao', 'CW', 'https://flagcdn.com/cw.svg', 'https://flagcdn.com/w320/cw.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (129, 'Zambia', 'Zambia', 'ZM', 'https://flagcdn.com/zm.svg', 'https://flagcdn.com/w320/zm.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (130, 'Zimbabwe', 'Zimbabwe', 'ZW', 'https://flagcdn.com/zw.svg', 'https://flagcdn.com/w320/zw.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (131, 'Tunisia', 'Tunisien', 'TN', 'https://flagcdn.com/tn.svg', 'https://flagcdn.com/w320/tn.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (132, 'United Arab Emirates', 'Förenade Arabemiraten', 'AE', 'https://flagcdn.com/ae.svg',
        'https://flagcdn.com/w320/ae.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (133, 'Mongolia', 'Mongoliet', 'MN', 'https://flagcdn.com/mn.svg', 'https://flagcdn.com/w320/mn.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (134, 'Norway', 'Norge', 'NO', 'https://flagcdn.com/no.svg', 'https://flagcdn.com/w320/no.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (135, 'Greenland', 'Grönland', 'GL', 'https://flagcdn.com/gl.svg', 'https://flagcdn.com/w320/gl.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (136, 'Uruguay', 'Uruguay', 'UY', 'https://flagcdn.com/uy.svg', 'https://flagcdn.com/w320/uy.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (137, 'Bahamas', 'Bahamas', 'BS', 'https://flagcdn.com/bs.svg', 'https://flagcdn.com/w320/bs.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (138, 'Russia', 'Ryssland', 'RU', 'https://flagcdn.com/ru.svg', 'https://flagcdn.com/w320/ru.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (139, 'British Virgin Islands', 'Brittiska Jungfruöarna', 'VG', 'https://flagcdn.com/vg.svg',
        'https://flagcdn.com/w320/vg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (140, 'Wallis and Futuna', 'Wallis- och Futunaöarna', 'WF', 'https://flagcdn.com/wf.svg',
        'https://flagcdn.com/w320/wf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (141, 'Chad', 'Tchad', 'TD', 'https://flagcdn.com/td.svg', 'https://flagcdn.com/w320/td.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (142, 'Saint Lucia', 'Saint Lucia', 'LC', 'https://flagcdn.com/lc.svg', 'https://flagcdn.com/w320/lc.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (143, 'Yemen', 'Jemen', 'YE', 'https://flagcdn.com/ye.svg', 'https://flagcdn.com/w320/ye.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (144, 'United States Minor Outlying Islands', 'Förenta staternas mindre öar i Oceanien och Västindien', 'UM',
        'https://flagcdn.com/um.svg', 'https://flagcdn.com/w320/um.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (145, 'Sweden', 'Sverige', 'SE', 'https://flagcdn.com/se.svg', 'https://flagcdn.com/w320/se.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (146, 'Svalbard and Jan Mayen', 'Svalbard och Jan Mayen', 'SJ', 'https://flagcdn.com/sj.svg',
        'https://flagcdn.com/w320/sj.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (147, 'Laos', 'Laos', 'LA', 'https://flagcdn.com/la.svg', 'https://flagcdn.com/w320/la.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (148, 'Latvia', 'Lettland', 'LV', 'https://flagcdn.com/lv.svg', 'https://flagcdn.com/w320/lv.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (149, 'Colombia', 'Colombia', 'CO', 'https://flagcdn.com/co.svg', 'https://flagcdn.com/w320/co.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (150, 'Grenada', 'Grenada', 'GD', 'https://flagcdn.com/gd.svg', 'https://flagcdn.com/w320/gd.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (151, 'Saint Barthélemy', 'Saint-Barthélemy', 'BL', 'https://flagcdn.com/bl.svg',
        'https://flagcdn.com/w320/bl.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (152, 'Canada', 'Kanada', 'CA', 'https://flagcdn.com/ca.svg', 'https://flagcdn.com/w320/ca.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (153, 'Heard Island and McDonald Islands', 'Heard- och McDonaldöarna', 'HM', 'https://flagcdn.com/hm.svg',
        'https://flagcdn.com/w320/hm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (154, 'India', 'Indien', 'IN', 'https://flagcdn.com/in.svg', 'https://flagcdn.com/w320/in.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (155, 'Guinea-Bissau', 'Guinea-Bissau', 'GW', 'https://flagcdn.com/gw.svg', 'https://flagcdn.com/w320/gw.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (156, 'North Macedonia', 'Nordmakedonien', 'MK', 'https://flagcdn.com/mk.svg', 'https://flagcdn.com/w320/mk.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (157, 'Paraguay', 'Paraguay', 'PY', 'https://flagcdn.com/py.svg', 'https://flagcdn.com/w320/py.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (158, 'Croatia', 'Kroatien', 'HR', 'https://flagcdn.com/hr.svg', 'https://flagcdn.com/w320/hr.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (159, 'Costa Rica', 'Costa Rica', 'CR', 'https://flagcdn.com/cr.svg', 'https://flagcdn.com/w320/cr.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (160, 'Uganda', 'Uganda', 'UG', 'https://flagcdn.com/ug.svg', 'https://flagcdn.com/w320/ug.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (161, 'Caribbean Netherlands', 'Karibiska Nederländerna', 'BES islands', 'https://flagcdn.com/bq.svg',
        'https://flagcdn.com/w320/bq.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (162, 'Bolivia', 'Bolivia', 'BO', 'https://flagcdn.com/bo.svg', 'https://flagcdn.com/w320/bo.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (163, 'Togo', 'Togo', 'TG', 'https://flagcdn.com/tg.svg', 'https://flagcdn.com/w320/tg.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (164, 'Mayotte', 'Mayotte', 'YT', 'https://flagcdn.com/yt.svg', 'https://flagcdn.com/w320/yt.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (165, 'Marshall Islands', 'Marshallöarna', 'MH', 'https://flagcdn.com/mh.svg', 'https://flagcdn.com/w320/mh.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (166, 'North Korea', 'Nordkorea', 'KP', 'https://flagcdn.com/kp.svg', 'https://flagcdn.com/w320/kp.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (167, 'Netherlands', 'Nederländerna', 'NL', 'https://flagcdn.com/nl.svg', 'https://flagcdn.com/w320/nl.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (168, 'British Indian Ocean Territory', 'Brittiska territoriet i Indiska Oceanen', 'IO',
        'https://flagcdn.com/io.svg', 'https://flagcdn.com/w320/io.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (169, 'Malta', 'Malta', 'MT', 'https://flagcdn.com/mt.svg', 'https://flagcdn.com/w320/mt.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (170, 'Mauritius', 'Mauritius', 'MU', 'https://flagcdn.com/mu.svg', 'https://flagcdn.com/w320/mu.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (171, 'Norfolk Island', 'Norfolkön', 'NF', 'https://flagcdn.com/nf.svg', 'https://flagcdn.com/w320/nf.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (172, 'Honduras', 'Honduras', 'HN', 'https://flagcdn.com/hn.svg', 'https://flagcdn.com/w320/hn.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (173, 'Spain', 'Spanien', 'ES', 'https://flagcdn.com/es.svg', 'https://flagcdn.com/w320/es.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (174, 'Estonia', 'Estland', 'EE', 'https://flagcdn.com/ee.svg', 'https://flagcdn.com/w320/ee.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (175, 'Kyrgyzstan', 'Kirgizistan', 'KG', 'https://flagcdn.com/kg.svg', 'https://flagcdn.com/w320/kg.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (176, 'Chile', 'Chile', 'CL', 'https://flagcdn.com/cl.svg', 'https://flagcdn.com/w320/cl.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (177, 'Bermuda', 'Bermuda', 'BM', 'https://flagcdn.com/bm.svg', 'https://flagcdn.com/w320/bm.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (178, 'Equatorial Guinea', 'Ekvatorialguinea', 'GQ', 'https://flagcdn.com/gq.svg',
        'https://flagcdn.com/w320/gq.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (179, 'Liberia', 'Liberia', 'LR', 'https://flagcdn.com/lr.svg', 'https://flagcdn.com/w320/lr.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (180, 'Pitcairn Islands', 'Pitcairnöarna', 'PN', 'https://flagcdn.com/pn.svg', 'https://flagcdn.com/w320/pn.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (181, 'Libya', 'Libyen', 'LY', 'https://flagcdn.com/ly.svg', 'https://flagcdn.com/w320/ly.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (182, 'Liechtenstein', 'Liechtenstein', 'LI', 'https://flagcdn.com/li.svg', 'https://flagcdn.com/w320/li.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (183, 'Vatican City', 'Vatikanstaten', 'VA', 'https://flagcdn.com/va.svg', 'https://flagcdn.com/w320/va.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (184, 'Christmas Island', 'Julön', 'CX', 'https://flagcdn.com/cx.svg', 'https://flagcdn.com/w320/cx.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (185, 'Oman', 'Oman', 'OM', 'https://flagcdn.com/om.svg', 'https://flagcdn.com/w320/om.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (186, 'Philippines', 'Filippinerna', 'PH', 'https://flagcdn.com/ph.svg', 'https://flagcdn.com/w320/ph.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (187, 'Poland', 'Polen', 'PL', 'https://flagcdn.com/pl.svg', 'https://flagcdn.com/w320/pl.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (188, 'Faroe Islands', 'Färöarna', 'FO', 'https://flagcdn.com/fo.svg', 'https://flagcdn.com/w320/fo.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (189, 'Bahrain', 'Bahrain', 'BH', 'https://flagcdn.com/bh.svg', 'https://flagcdn.com/w320/bh.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (190, 'Belarus', 'Belarus', 'BY', 'https://flagcdn.com/by.svg', 'https://flagcdn.com/w320/by.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (191, 'Slovenia', 'Slovenien', 'SI', 'https://flagcdn.com/si.svg', 'https://flagcdn.com/w320/si.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (192, 'Guadeloupe', 'Guadeloupe', 'GP', 'https://flagcdn.com/gp.svg', 'https://flagcdn.com/w320/gp.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (193, 'Qatar', 'Qatar', 'QA', 'https://flagcdn.com/qa.svg', 'https://flagcdn.com/w320/qa.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (194, 'Vietnam', 'Vietnam', 'VN', 'https://flagcdn.com/vn.svg', 'https://flagcdn.com/w320/vn.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (195, 'Mauritania', 'Mauretanien', 'MR', 'https://flagcdn.com/mr.svg', 'https://flagcdn.com/w320/mr.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (196, 'Singapore', 'Singapore', 'SG', 'https://flagcdn.com/sg.svg', 'https://flagcdn.com/w320/sg.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (197, 'Georgia', 'Georgien', 'GE', 'https://flagcdn.com/ge.svg', 'https://flagcdn.com/w320/ge.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (198, 'Burundi', 'Burundi', 'BI', 'https://flagcdn.com/bi.svg', 'https://flagcdn.com/w320/bi.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (199, 'Nauru', 'Nauru', 'NR', 'https://flagcdn.com/nr.svg', 'https://flagcdn.com/w320/nr.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (200, 'South Sudan', 'Sydsudan', 'SS', 'https://flagcdn.com/ss.svg', 'https://flagcdn.com/w320/ss.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (201, 'Samoa', 'Samoa', 'WS', 'https://flagcdn.com/ws.svg', 'https://flagcdn.com/w320/ws.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (202, 'Cocos (Keeling) Islands', 'Kokosöarna', 'CC', 'https://flagcdn.com/cc.svg',
        'https://flagcdn.com/w320/cc.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (203, 'Republic of the Congo', 'Kongo-Brazzaville', 'CG', 'https://flagcdn.com/cg.svg',
        'https://flagcdn.com/w320/cg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (204, 'Cyprus', 'Cypern', 'CY', 'https://flagcdn.com/cy.svg', 'https://flagcdn.com/w320/cy.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (205, 'Kuwait', 'Kuwait', 'KW', 'https://flagcdn.com/kw.svg', 'https://flagcdn.com/w320/kw.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (206, 'Trinidad and Tobago', 'Trinidad och Tobago', 'TT', 'https://flagcdn.com/tt.svg',
        'https://flagcdn.com/w320/tt.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (207, 'Tuvalu', 'Tuvalu', 'TV', 'https://flagcdn.com/tv.svg', 'https://flagcdn.com/w320/tv.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (208, 'Angola', 'Angola', 'AO', 'https://flagcdn.com/ao.svg', 'https://flagcdn.com/w320/ao.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (209, 'Tonga', 'Tonga', 'TO', 'https://flagcdn.com/to.svg', 'https://flagcdn.com/w320/to.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (210, 'Greece', 'Grekland', 'GR', 'https://flagcdn.com/gr.svg', 'https://flagcdn.com/w320/gr.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (211, 'Mozambique', 'Moçambique', 'MZ', 'https://flagcdn.com/mz.svg', 'https://flagcdn.com/w320/mz.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (212, 'Myanmar', 'Myanmar', 'MM', 'https://flagcdn.com/mm.svg', 'https://flagcdn.com/w320/mm.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (213, 'Austria', 'Österrike', 'AT', 'https://flagcdn.com/at.svg', 'https://flagcdn.com/w320/at.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (214, 'Ethiopia', 'Etiopien', 'ET', 'https://flagcdn.com/et.svg', 'https://flagcdn.com/w320/et.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (215, 'Martinique', 'Martinique', 'MQ', 'https://flagcdn.com/mq.svg', 'https://flagcdn.com/w320/mq.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (216, 'Azerbaijan', 'Azerbajdzjan', 'AZ', 'https://flagcdn.com/az.svg', 'https://flagcdn.com/w320/az.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (217, 'Uzbekistan', 'Uzbekistan', 'UZ', 'https://flagcdn.com/uz.svg', 'https://flagcdn.com/w320/uz.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (218, 'Bangladesh', 'Bangladesh', 'BD', 'https://flagcdn.com/bd.svg', 'https://flagcdn.com/w320/bd.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (219, 'Armenia', 'Armenien', 'AM', 'https://flagcdn.com/am.svg', 'https://flagcdn.com/w320/am.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (220, 'Nigeria', 'Nigeria', 'NG', 'https://flagcdn.com/ng.svg', 'https://flagcdn.com/w320/ng.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (221, 'South Africa', 'Sydafrika', 'ZA', 'https://flagcdn.com/za.svg', 'https://flagcdn.com/w320/za.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (222, 'Brunei', 'Brunei', 'BN', 'https://flagcdn.com/bn.svg', 'https://flagcdn.com/w320/bn.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (223, 'Italy', 'Italien', 'IT', 'https://flagcdn.com/it.svg', 'https://flagcdn.com/w320/it.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (224, 'Finland', 'Finland', 'FI', 'https://flagcdn.com/fi.svg', 'https://flagcdn.com/w320/fi.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (225, 'Israel', 'Israel', 'IL', 'https://flagcdn.com/il.svg', 'https://flagcdn.com/w320/il.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (226, 'Aruba', 'Aruba', 'AW', 'https://flagcdn.com/aw.svg', 'https://flagcdn.com/w320/aw.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (227, 'Nicaragua', 'Nicaragua', 'NI', 'https://flagcdn.com/ni.svg', 'https://flagcdn.com/w320/ni.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (228, 'Haiti', 'Haiti', 'HT', 'https://flagcdn.com/ht.svg', 'https://flagcdn.com/w320/ht.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (229, 'Kiribati', 'Kiribati', 'KI', 'https://flagcdn.com/ki.svg', 'https://flagcdn.com/w320/ki.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (230, 'Turks and Caicos Islands', 'Turks- och Caicosöarna', 'TC', 'https://flagcdn.com/tc.svg',
        'https://flagcdn.com/w320/tc.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (231, 'Cayman Islands', 'Caymanöarna', 'KY', 'https://flagcdn.com/ky.svg', 'https://flagcdn.com/w320/ky.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (232, 'Ukraine', 'Ukraina', 'UA', 'https://flagcdn.com/ua.svg', 'https://flagcdn.com/w320/ua.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (233, 'Mexico', 'Mexiko', 'MX', 'https://flagcdn.com/mx.svg', 'https://flagcdn.com/w320/mx.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (234, 'Palestine', 'Palestina', 'PS', 'https://flagcdn.com/ps.svg', 'https://flagcdn.com/w320/ps.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (235, 'Fiji', 'Fiji', 'FJ', 'https://flagcdn.com/fj.svg', 'https://flagcdn.com/w320/fj.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (236, 'Slovakia', 'Slovakien', 'SK', 'https://flagcdn.com/sk.svg', 'https://flagcdn.com/w320/sk.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (237, 'Ghana', 'Ghana', 'GH', 'https://flagcdn.com/gh.svg', 'https://flagcdn.com/w320/gh.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (238, 'Suriname', 'Surinam', 'SR', 'https://flagcdn.com/sr.svg', 'https://flagcdn.com/w320/sr.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (239, 'Cuba', 'Kuba', 'CU', 'https://flagcdn.com/cu.svg', 'https://flagcdn.com/w320/cu.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (240, 'Bhutan', 'Bhutan', 'BT', 'https://flagcdn.com/bt.svg', 'https://flagcdn.com/w320/bt.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (241, 'Hungary', 'Ungern', 'HU', 'https://flagcdn.com/hu.svg', 'https://flagcdn.com/w320/hu.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (242, 'São Tomé and Príncipe', 'São Tomé och Príncipe', 'ST', 'https://flagcdn.com/st.svg',
        'https://flagcdn.com/w320/st.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (243, 'Iraq', 'Irak', 'IQ', 'https://flagcdn.com/iq.svg', 'https://flagcdn.com/w320/iq.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (244, 'Czechia', 'Tjeckien', 'CZ', 'https://flagcdn.com/cz.svg', 'https://flagcdn.com/w320/cz.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (245, 'Lithuania', 'Litauen', 'LT', 'https://flagcdn.com/lt.svg', 'https://flagcdn.com/w320/lt.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (246, 'Northern Mariana Islands', 'Nordmarianerna', 'MP', 'https://flagcdn.com/mp.svg',
        'https://flagcdn.com/w320/mp.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (247, 'Botswana', 'Botswana', 'BW', 'https://flagcdn.com/bw.svg', 'https://flagcdn.com/w320/bw.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (248, 'Panama', 'Panama', 'PA', 'https://flagcdn.com/pa.svg', 'https://flagcdn.com/w320/pa.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (249, 'Gabon', 'Gabon', 'GA', 'https://flagcdn.com/ga.svg', 'https://flagcdn.com/w320/ga.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
       (250, 'Ecuador', 'Ecuador', 'EC', 'https://flagcdn.com/ec.svg', 'https://flagcdn.com/w320/ec.png',
        '2023-09-30 08:22:34', '2024-01-06 19:23:45');