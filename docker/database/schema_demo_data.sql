insert into api_keys values(0, sha1('184fa1a0-9e75-4b5a-b9f0-604f6d643daf'));

-- Roller och behörigheter
INSERT INTO roles (`role_id`, `role_name`) VALUES(1,'ADMIN');
INSERT INTO roles (`role_id`, `role_name`) VALUES(2, 'USER');
INSERT INTO roles (`role_id`, `role_name`) VALUES(3, 'SUPERUSER');
INSERT INTO roles (`role_id`, `role_name`) VALUES(4,'COMPETITOR');
INSERT INTO roles (`role_id`, `role_name`) VALUES( 5,'DEVELOPER');
INSERT INTO roles (`role_id`, `role_name`) VALUES( 6,'VOLONTAR');

INSERT INTO permissions (perm_mod, perm_id, perm_desc) VALUES
('ADMIN', 1, 'Access users'),
('ADMIN', 2, 'Create new users'),
('ADMIN', 3, 'Update users'),
('ADMIN', 4, 'Delete users'),
('COMPETITOR', 5, 'See controls');


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

-- -- Cyklister
INSERT INTO competitors(competitor_uid, user_name, given_name, family_name, role_id,password) VALUES ('2922a6e9-9e32-4832-9575-b3d2eb3011b9','100','Pelle','Cyklist',4,sha1('test'));
INSERT INTO competitors(competitor_uid, user_name ,given_name, family_name,role_id,password) VALUES ('68f06a8c-8f08-45cc-8d20-d5e37ce658ba','200','Johan','Randonnéer',4,sha1('test1'));
INSERT INTO competitors(competitor_uid, user_name ,given_name, family_name,role_id,password) VALUES ('593edcab-5dcb-4916-829d-08ac536770ad','300','Kalle','Super Randonneur',4, sha1('test2'));

