-- =====================================================
-- MIGRATION: Sync Club UIDs with Loppservice Database
-- =====================================================
-- Purpose: Update club UIDs in app database to match loppservice database
-- Generated on: 2025-07-02 08:29:03
-- Total clubs to sync: 152
-- =====================================================

-- =====================================================
-- MIGRATION: Sync Club UIDs with Loppservice Database
-- =====================================================
-- Purpose: Update club UIDs in app database to match loppservice database
-- Generated on: 2025-07-02 08:29:03
-- Total clubs to sync: 152
-- =====================================================

-- Step 1: Create temporary mapping table to store old->new UID mappings
-- This table will hold the relationship between app database UIDs and loppservice UIDs
CREATE TEMPORARY TABLE club_uid_mapping (
    old_club_uid CHAR(36),      -- Current UID in app database
    new_club_uid CHAR(36),      -- Target UID from loppservice database
    club_name VARCHAR(200),     -- Club name (for verification)
    acp_kod VARCHAR(11),        -- ACP code from app database
    matched_by_name BOOLEAN DEFAULT FALSE  -- Flag to indicate successful name match
);

-- Step 2: Insert club mappings based on name matching
-- These are clubs that exist in both databases with matching names
-- Only clubs with exact name matches are included for safety
INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES
('82ca57b7-ec71-467e-b5f0-b012d50c2d43', '689ebeb9-954b-4fba-95d1-976a4f0dc28a', 'Alba Rosa', '', TRUE),
('a1b6b511-4c37-49b6-a30f-31de6ac755df', '0c851f4c-6a97-41e8-8cca-2d3463de40f0', 'Alnö Race Team', 'SE113046', TRUE),
('3f759adc-13e4-4c46-a061-02c0e040dc9e', '9716e876-3e31-43fc-9532-176a58f342da', 'Andover Wheelers', '0', TRUE),
('bc76e32d-e8ef-45fd-a571-825f6cccdc40', 'a702a90b-41cd-460d-bfe7-5c3c16b15a22', 'Antaris', '0', TRUE),
('1cbbc9d5-6d5a-4863-9f49-4e4fb27c2622', 'aee6355f-323f-4446-8819-ca2d71d88416', 'Antaris Team', '0', TRUE),
('86bbb2cb-d293-4204-8711-6c8025019f8c', 'ff0b90a3-b954-4289-b935-f1314f974cb6', 'ARA Berlin Brandenburg', '0', TRUE),
('43bc7e7c-1ac8-487a-8863-bb65e3835239', '39357002-fbee-428d-ba02-5f987bda21cf', 'ARA Hamburg', '0', TRUE),
('dcdf7063-5fbe-4cea-ae2c-c24b75a13692', 'aee7f50b-f010-44db-978a-c0b01943e121', 'ARA Niederrhein', '0', TRUE),
('a52ab5ca-be29-48c5-b855-886a1b0b2288', 'de23695a-ba4f-4f2d-b09f-0d51c48272d2', 'ARA Nordbayern', '0', TRUE),
('763f0abe-4c70-4d97-9177-e129e5d5d46c', 'fefc3fc8-ed6e-495d-9765-87d462ff493c', 'ARA Nordbayern Fränkische Alb', '0', TRUE),
('b347b186-bcfc-438f-8b63-1690e00b41fa', '3abb03b6-4293-491f-bc1f-ca4561b2a951', 'ARD Danmark', '0', TRUE),
('7457ee85-1d7e-4a4c-824d-061e8aba3db0', '8ae091f2-42a7-4b6f-b699-165491ea6e27', 'Audax Australia', '0', TRUE),
('29730fd0-9f26-42f7-b877-60a558c77579', 'bcc422b6-9456-4b22-86a0-11d53cbcd684', 'Audax Club Oslo', '0', TRUE),
('9e54b923-c604-443d-9d77-4d017bdaeb51', '99630602-6881-4f2d-9a7c-6160d2d0b9fa', 'Audax Danmark', '0', TRUE),
('142d4a88-41a5-427b-9ae5-9bf7301fb199', 'b561e9dc-44b6-4073-a302-dfea17bc2e83', 'Audax Nordbayern', '0', TRUE),
('7d74b62c-675f-4f44-bbfb-f9fd5a1f011d', '6ec966d5-b9cb-49bd-96b3-eb266ab45984', 'Audax Poland', '0', TRUE),
('fbee80a8-01d6-4615-9adf-47d9486c0383', 'b928b79e-1850-4ab1-ba89-5e04db440ac9', 'Audax Polska', '0', TRUE),
('3fc88e1f-23c0-4b20-a48f-e82845c57fde', '971d1384-2bfb-4305-aae5-17f7b997cc0c', 'Audax Randonneurs Allemagne', '0', TRUE),
('b6b15c78-38b6-4698-ac50-6840c9a921e1', 'f62257fa-982b-4945-b944-54e2cf5d34e8', 'Audax Randonneurs Danemark', '0', TRUE),
('e5dfd1c9-080e-4484-be09-64c1cfab2fbc', '4b65e419-6d2c-453c-9c8a-171f42b53fea', 'AUDAX RANDONNEURS GRECE', '0', TRUE);

INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES
('bfac18a2-fac4-4d58-b63f-e34929886f02', '3a6ab27a-b25d-4d98-adc1-c861f95ca206', 'Audax Randonneurs Kinki', '0', TRUE),
('48aa1afd-c1cb-4a60-b156-cd32137bfc11', '48aa1afd-c1cb-4a60-b156-cd32137bfc11', 'Audax UK', '0', TRUE),
('c0e64b28-e7f1-435a-944c-0c5f1f776425', '537b0188-1dc2-4831-bed8-5349a22c4277', 'Axa', '0', TRUE),
('3343fcb9-dac1-44a3-b7b8-6ce73aae5112', 'd602e2ac-ecaf-413d-910b-0facb9b62fa8', 'AXA Sports Club', '0', TRUE),
('e899b383-7ffb-40fa-8ed0-46079169a86d', 'cfd7e804-2a5b-4a21-bdc4-038d453d7fa6', 'BCC', '0', TRUE),
('5e21ed23-0bd9-452b-b160-935cc9a4d2b5', '6982636d-c90c-4eea-b570-691688dc49c2', 'Beckenham Dads CC', '0', TRUE),
('7b5862ad-3a47-43b1-a033-4577dbf493c8', '08f8421c-288f-4a82-b78c-c403995cb469', 'Berg CC', '0', TRUE),
('99404078-e7ec-4c8a-b991-575d31e3cadd', '0b7384ee-7f2d-4bc2-a8bd-0a1750dee19a', 'Bollnäs CK', 'SE113101', TRUE),
('ea8c894a-2c67-4afc-a246-3b11c2b7b7ce', 'db841c3a-041b-44e0-813e-652c1858b1ea', 'BtB', '0', TRUE),
('3b5cba09-3b0e-4bd8-bae0-32fb48e73f9d', 'fdf14b9f-3966-4904-be6d-37be4218a09d', 'C.C. RIPOLLET', '0', TRUE),
('10e30f0f-ce62-4b70-84ba-15132f130b6d', 'be77bb35-29d1-42fa-9c97-26fa5f5f3da2', 'Cardiff Ajax', '0', TRUE),
('3e058d47-fd99-4ead-bae7-99c5a46147cf', '533eb8de-4dc9-4667-b75c-4395fb68f85f', 'Ciclismo Cycling Club', 'SE113106', TRUE),
('da92f834-44e5-4d67-8095-bb47c27b1ec4', 'c788c05b-fd17-49fb-ace5-ed9283995467', 'CK DaimX', '0', TRUE),
('c1f9d7a0-6e21-4c41-83b0-d8c9cd3d7e', 'c1f9d7a0-6e21-4c41-83b0-d8c9cd3d7eb0', 'CK Distans', 'SE113043', TRUE),
('c978afc3-1b25-449d-bba9-aaee76ab532f', 'ee0708a4-186f-411e-8a4b-01094cab82b1', 'CK Guldkedjan', 'SE113111', TRUE),
('7465cc6e-3c35-4552-adc6-b107c3ddedf2', '7465cc6e-3c35-4552-adc6-b107c3ddedf2', 'CK Hymer', 'SE113080', TRUE),
('8c573741-5e2f-408a-9c4b-e866441a5775', 'f0e15d64-5195-467c-aad4-fa2d8d564f0a', 'CK Örnen ', 'SE113107', TRUE),
('6716774c-c92b-4379-b56a-139b6d45dab2', 'b3726cd9-42ce-4921-985b-39fec8e36f29', 'CK Sävast', '0', TRUE),
('f1e55854-56e3-43c1-96c2-a0c6b108ece9', 'fb27261d-5abb-43cd-856c-800a27524d54', 'CK Snäckan', 'SE113092', TRUE),
('b941d3d3-2aa3-4310-920a-395dfa05f1e8', '48e21477-3619-4134-b9b3-87af2cea63cc', 'CK Sundet', 'SE113022', TRUE);

INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES
('2a0ea5a0-81fb-4771-99b8-2d53d1697a0b', '9a1bce72-43b2-4107-a437-148ee7212f91', 'CK Uni', 'SE113034', TRUE),
('163bfa81-4015-4964-9ecf-8cabd0b418b2', 'b325ecf8-d85f-437b-a765-596cd410b0f1', 'Club Ciclista Riazor', '0', TRUE),
('576995a0-9a41-4838-a4e4-613666d3ef58', '09044084-0526-48ea-8795-b3af6e2f233e', 'Cycling Colonia', '0', TRUE),
('f1a2abc5-7a0a-4fa6-8d12-2cc20734c58f', 'f1a2abc5-7a0a-4fa6-8d12-2cc20734c58f', 'Cykelintresset', 'SE113072', TRUE),
('36436c4c-baaa-4625-8737-e924f44e81f2', 'be5feb82-517e-410f-800e-68edfc9cdf46', 'Cykelmagasinet', '0', TRUE),
('c043b96e-1903-44e3-84e4-3c4848d7cf1e', '2b734ec6-651a-46c4-a454-1e6a39e3421e', 'Cykelslang CC', '0', TRUE),
('630f4857-41f3-4c6d-8e38-5e536e87bb76', '10e93d35-9ae0-42d6-b2f7-24777dd01262', 'Derby Mercury', '0', TRUE),
('4820b122-e31a-4503-99c6-a29107d5d9a2', '5214390e-f9c9-4858-9959-f9d1d3532303', 'DNT fjellsport Bergen', '0', TRUE),
('f1277de0-8dd1-4fbf-8dcf-8c1694ff2a90', '51c51daf-974e-41e7-bdc6-5a97d14b37ff', 'dsgfdsfgd', '0', TRUE),
('18726416-5075-481c-adfb-5f3562bbed18', '73d11ee6-98b9-410b-bc4b-5537bac3b7d0', 'Ersmark', '0', TRUE),
('1f202530-4400-4d34-8501-7db208e3b2ef', '57d3e945-44c2-4963-a4b5-18158fd873ae', 'Fibrax Fenwick''s Wrexham Cycling Club', '0', TRUE),
('b5460449-68f4-424e-9677-8db06695089a', '9585151e-77cc-4e67-bd3a-c139d344925e', 'Fredrikshofs IF CK', 'SE113002', TRUE),
('d7048d37-4203-4b80-b131-48b8e5723f8e', 'e09ebe33-9b86-4f57-91af-1d3560b60444', 'Free Solo', '0', TRUE),
('805c4602-5a31-4593-b0de-a9c7b70a449a', '2e1c0b17-4add-46f4-8ebb-e4417c1b7bb7', 'Friends With Bikes', '0', TRUE),
('89022915-6a3a-49ac-bc8d-d41b2aa76bd6', '89022915-6a3a-49ac-bc8d-d41b2aa76bd6', 'Gimonäs CK', 'SE113036', TRUE),
('1cd9eb61-e397-407f-a854-1a1d97e8c7b5', 'e94eb470-d368-4670-8213-f8cf8a9835ba', 'Gironde', '0', TRUE),
('28ee4204-71b0-4b08-857f-bb9cad3148c9', '84e10d8f-c901-4f3f-ba55-f09b626897fd', 'Håkmarks hbgf', '0', TRUE),
('d4622951-facd-46af-8935-6dcd769f8bd9', '61841917-5066-4660-936b-5a88d3f9595d', 'Hisingens CK', 'SE113003', TRUE),
('1bb35ad1-5b37-4a70-b68b-a2e66ee6d147', 'cffc1801-3c9e-43ef-ba83-9cb77dfdc125', 'Höga kusten cyklisterna', 'SE113015', TRUE),
('99b8d1e5-6740-4096-ade6-4ec12c681ab4', '7eb933f3-1523-42c7-8a0b-1ff67d2fc90e', 'Horna Girls', '0', TRUE);

INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES
('db03a96d-a61f-4789-8835-13deda233599', 'd7aaf1f3-46b5-4245-aca8-733d6500606d', 'Icehouse', '0', TRUE),
('5731955b-e3f8-4f28-86ba-3dfb7111e2a3', '5662e6cb-cf45-46c5-b3cd-3bebc50df737', 'IF Åland', '0', TRUE),
('03602e3a-0f19-439c-b539-7b561bab27b7', 'baeeff6e-ac4d-4acf-bf20-2dffd7962ff3', 'IF Frøy', '0', TRUE),
('b7fe8020-e27e-44be-8f7c-3123ebd673d8', '0d45f88e-50f2-46a0-9c45-a753882c9425', 'IFK Arvidsjaur Cykel', '0', TRUE),
('88d01283-e18e-44d5-89f7-ced4b91d01f9', '88d01283-e18e-44d5-89f7-ced4b91d01f9', 'IFK Arvidsjaur Skidor', '0', TRUE),
('8e54829f-0acd-42d3-bd88-c371d74e74a9', '4ef3a0fd-6b35-40cf-9bc2-ad73a749b03e', 'Ilmenauer Radsport Club e.V.', '0', TRUE),
('080f455d-424e-4eab-b6bb-8c76ab843f05', '080f455d-424e-4eab-b6bb-8c76ab843f05', 'Independent Armenia', '0', TRUE),
('9cef8680-6b1e-4d4e-89f6-02b6a841830f', '9cef8680-6b1e-4d4e-89f6-02b6a841830f', 'Independent Belarus', '0', TRUE),
('94db6cb1-0c74-11ee-b56a-e4434bde7140', '94db6cb1-0c74-11ee-b56a-e4434bde7140', 'Independent France', '0', TRUE),
('642cdff2-d73f-4bf3-ba58-dca4f03aae27', '642cdff2-d73f-4bf3-ba58-dca4f03aae27', 'Independent Lithuania', '0', TRUE),
('dca792ee-4fa4-4a6e-b544-01088d5d52a2', 'dca792ee-4fa4-4a6e-b544-01088d5d52a2', 'Independent Netherlands', '0', TRUE),
('b85c576a-29fc-423f-94f5-17b140b90ced', 'b85c576a-29fc-423f-94f5-17b140b90ced', 'Independent Sweden', 'SE113099', TRUE),
('572980f8-e759-4369-bafd-4133f539728c', '572980f8-e759-4369-bafd-4133f539728c', 'Independent UK', '0', TRUE),
('224c6840-7fba-4a7d-acc3-5164017438ee', '224c6840-7fba-4a7d-acc3-5164017438ee', 'Independiente Espagne', '0', TRUE),
('dca792ee-4fa4-4a6e-b544-01088d5d52a3', 'dca792ee-4fa4-4a6e-b544-01088d5d52a3', 'Individuel Allemand', '0', TRUE),
('c7a55da2-715c-400f-b4f9-2b9435864490', 'c7a55da2-715c-400f-b4f9-2b9435864490', 'Individuel Belgique', '0', TRUE),
('76d0673e-24c1-4a1f-825c-4f79598c2cb7', '76d0673e-24c1-4a1f-825c-4f79598c2cb7', 'Individuel Bulgarie', '0', TRUE),
('94db6cb1-0c65-11ee-b56b-e4434bde7140', '94db6cb1-0c65-11ee-b56b-e4434bde7140', 'Individuel Italien', '0', TRUE),
('3b360d0f-d400-479d-be88-fd8f5d418715', '3b360d0f-d400-479d-be88-fd8f5d418715', 'Individuel Norvege', 'NO11499', TRUE),
('8469fd99-c22d-4b8b-a6ea-5f04b9943953', '8469fd99-c22d-4b8b-a6ea-5f04b9943953', 'Individuel Suisse', '0', TRUE);

INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES
('2a11f7f8-5b61-4f26-b938-712400b06d8b', '2a11f7f8-5b61-4f26-b938-712400b06d8b', 'IOGT/NTO Skövde', 'SE113016', TRUE),
('bdc2cb47-cb05-47e4-8913-b935711356e3', 'a2eee988-da01-421f-a94c-ba45564c562d', 'Kalajärven Näädät', '0', TRUE),
('1c5f7ac9-a5da-4c9c-8624-a88192b438ab', 'f2f25ecb-9e22-4c5d-9016-97c64fb151bf', 'Kaupin kanuunat ', '0', TRUE),
('10523dbf-b063-40a6-88b0-482f89d65737', 'd049d4fd-7708-4f26-b3a8-7f7707131102', 'KBCK', 'SE113081', TRUE),
('6e0090d8-e2c3-4c8d-bf9d-901cfe813fe1', '6e224419-c973-4bb0-9e84-da311a56d9c5', 'Kinross Cycling Club', '0', TRUE),
('ead265ad-b829-40c4-bbe6-7bc8f3a64633', '411d56f4-fa8f-49d5-86a1-6a819a5331e0', 'km10', '0', TRUE),
('ea277c0d-f638-44e6-bd0c-52f864a8377f', '390aa933-2124-4c60-ab59-3e5f34cdea5e', 'Koiviston Isku', '0', TRUE),
('a4f2ca41-e95f-44dd-9b5b-0dc53369de8b', 'c4b17edf-2180-4920-abe4-afc843fee25b', 'KTK86', '0', TRUE),
('c0a41ffe-e1c9-4847-a637-73087621a457', '949348d9-7a86-4f4a-9f78-04020b447ccc', 'Kungsbacka CK ', 'SE113075', TRUE),
('43cc768a-5f12-45c1-85cc-9908e9a8eeb4', 'd8e3d0b0-8c38-4b2c-ae2f-70358e1204b9', 'Lapua', '0', TRUE),
('c590829c-004b-49a0-994f-81d582402105', 'acd03a86-d67e-453d-8c7a-9d465f66c98c', 'Leton Leisku', '0', TRUE),
('3bf37f10-f23c-44ec-b7d4-141ff8b38e15', '15f6f0dc-6dda-4c56-a687-89260c31c322', 'Luleå CK', '0', TRUE),
('3aad57f1-9723-45a7-a2fc-a7fc3d9525e9', 'a78b7e6a-d638-4582-af52-e284cf2de6e0', 'Lycksele IF Cykel', '0', TRUE),
('98b2f325-648f-4387-942b-cddb3a7898eb', '224765c0-4ad1-42ae-91c0-5f7722d6de0a', 'Lygnens venner', '', TRUE),
('f0e10fe5-1a5a-465b-a52a-a9bc389a82e4', '95adcccb-c006-41e3-b71b-0175a07755ba', 'Membre individuel gironde', '0', TRUE),
('f66a218f-eba1-451e-934e-ff23b0d41092', 'b590cb66-b57d-4d6f-9083-708903701252', 'Mera Lera MTB', '0', TRUE),
('90b174a7-6f67-4373-8447-7a61dd4ef2d2', '90b174a7-6f67-4373-8447-7a61dd4ef2d2', 'Milslukaren', 'SE113024', TRUE),
('0816b855-fba9-4f31-a49e-05efda6650ac', '00141f17-65cd-40cb-8d90-0f59b8d93ca0', 'Moonglu CC', '0', TRUE),
('4ea3e6a6-82c7-4f16-b20c-85a9e80c04fd', 'b38339ae-45ce-48ab-85dd-b581fe93cb64', 'Mozac Cyclo Club', '0', TRUE),
('0ba7669b-e98a-43c0-b817-973a624621aa', 'd513effb-7584-4a1e-b59f-2a2d68648fbe', 'N/A', '', TRUE);

INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES
('1ef995bc-9b50-4089-a87d-530bfa232225', '2504b035-38e6-48bc-ba6d-b337e7287936', 'N8RIDDERS', '0', TRUE),
('26137332-c070-44ce-bcaf-e0fdf4d678af', '47d09a9a-fe07-462d-8dc9-626983a230a5', 'Ness Pedal Collective', '0', TRUE),
('79ff4dcd-10c8-4062-91cd-298af3bc145f', 'fd9ccafa-1869-4192-89a0-06bf9bd08d07', 'Northern Virginia Randonneurs', '0', TRUE),
('5a5554c4-4c5d-41fa-ac69-baf2b4e0c0dc', '6f7d9676-daa4-4e64-9d31-3921bbad6b84', 'Northern XC Sports Club', '0', TRUE),
('c6c36476-6068-4a43-abee-4b49adfc149b', 'ea0e432f-c72d-4213-930f-4a3cd1715ccb', 'Örebrocyklisterna ', 'SE113009', TRUE),
('e4dd1682-65f8-4230-9cb8-94842b902a2b', 'e54727ac-3e4a-4c11-9283-74f089eb6757', 'Orkla Cycleclub', '0', TRUE),
('b2115655-d09a-4a50-93e9-fed357412616', '555a52fa-a8b6-4b3e-93b1-cc8b54e70150', 'Östersunds CK', 'Se113014', TRUE),
('c963f5ef-596a-49da-b8d9-429aae301c6c', 'f5da9722-cfa5-45da-800f-06df57f071c5', 'Ostrobothnia Randounners', '0', TRUE),
('f58fca61-b8c7-458e-8c76-dc3682756d72', 'f12a6f7f-b688-41ac-99b7-51ca0d87d476', 'PandR', '0', TRUE),
('de0b95ec-58bc-4364-b479-8fd904d2bcfb', '02580a8d-91c7-45d1-8f30-3140779413a0', 'Partille CK', '0', TRUE),
('4540b232-fc6f-4663-869a-eeb3d04865dd', '792701ba-3050-4855-9cf2-22cfd9287ad2', 'Randonneur Stockholm', 'SE113004', TRUE),
('151945e2-bd7a-4379-aeaa-5a951972e28f', '151945e2-bd7a-4379-aeaa-5a951972e28f', 'Randonneur Väst', 'SE113013', TRUE),
('abaa35d8-b3e3-42a4-a619-b811b6d031d6', '6c741b1d-679a-41f5-a929-7a0031e856a3', 'Randonneurs Andalucia', '0', TRUE),
('3e51e2da-7c79-41c7-9153-ee56fa72e7df', '70480e6e-0685-489f-ab50-d24a896cc303', 'Randonneurs Armenia', '0', TRUE),
('ee31ca84-34c0-468d-8b0b-667d48d8aa1c', 'a8bbe7b8-1cfa-4295-8a0e-df111f838066', 'Randonneurs Austria', '', TRUE),
('7053d0d1-f72f-49dc-806c-9935d160bc6f', 'e961f299-023c-464e-865f-23bc1d3cf9f9', 'Randonneurs Autonomes Aquitains', '0', TRUE),
('dec27d3e-b4a1-468b-94b0-cd804d9aec65', 'dec27d3e-b4a1-468b-94b0-cd804d9aec65', 'Randonneurs Finland', 'FI31466', TRUE),
('f3e28bfd-783e-40b7-8593-5a99cb4d002e', 'ee3fc77d-95bf-4fec-9db6-7f8480af7550', 'Randonneurs Laponia', 'SE113117', TRUE),
('dda5dd14-cf67-4e49-a665-450076937127', 'a4072e31-59a1-4627-9e61-8f2fa645c04d', 'Randonneurs Nederland', '0', TRUE),
('51e8c323-6cf6-4375-9887-007d9d11bc90', '316dac3f-f4b5-4058-83fc-44ae95bcc4c1', 'Randonneurs Sverige', 'SE113000', TRUE);

INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES
('40f71cb5-57f7-4570-a13f-b7b80c9109ce', '382a4b39-ebc9-4970-98b1-191431e7e348', 'Randonneurs USA', '0', TRUE),
('1058160f-c301-45ef-94f0-f4e32d37e2f3', 'e675ce81-a2ad-4f2f-8902-b526e3f6d7bd', 'Rooster Club Varberg', '0', TRUE),
('0f594758-b51b-4da3-9efc-676a2e871f60', '33fd0274-4953-4a9d-8fe1-7de92a206c54', 'RSC Rot-Gold Bremen', '0', TRUE),
('a724096d-35c0-4d87-94a6-85eb771d5f60', '9ec0f3ec-ae63-4d26-9ffa-a2f0a74fb8fd', 'RSV Ellmendingen', '0', TRUE),
('fdc25bd8-95f3-4fb1-b2a1-caa6f2a7c93d', '8495189b-730a-4380-8f27-7cefee85c404', 'San Francisco Randonneurs', '0', TRUE),
('9d37e8fc-f053-4d43-8b43-f20207614add', '7ba3c17a-f6fc-4c63-b75d-7ee34d6f64bc', 'Sävar CK', '0', TRUE),
('bd22e0c1-fabe-4dde-8520-47fc210f13ae', 'b262a385-7c30-4a2c-8a8b-041b4beb8b68', 'Seattle Randonneurs ', '0', TRUE),
('0d6363d0-3508-46e2-8735-82e343a4c002', '7410f205-74a0-4d01-9029-68a95fcc1159', 'Spårvägen CF', '0', TRUE),
('6560772e-8d1d-4b84-b33a-76ad17d15820', 'b65b71ab-291c-4311-8efc-a23b680dde03', 'SPIF Triathlon', '0', TRUE),
('8a41df1c-0de0-4b8a-ab1e-07da89f3d8f3', 'e2c9791d-8767-4daf-b786-6b1d216b93b7', 'Staubwolke Refrath', '0', TRUE),
('33632987-571e-4ec9-922a-70b747706eac', 'f0bfa6b2-48b7-41ae-bcbb-4a736eda2963', 'Stöcke TS Järnet', 'SE113115', TRUE),
('987d5ed4-73d4-47c6-ad80-7ec12e13d640', 'fbe3fef1-73e3-44de-8bbf-394cacbacdaa', 'Sumo Cycling Club', 'SE113049', TRUE),
('33af3c8b-48b1-4495-a555-6df9f1763baf', 'f9a14279-680d-49d4-a933-4f5b4c7c8651', 'Super-Brevet Berlin-Munich-Berlin', '0', TRUE),
('5316aea4-24c1-4e72-9565-86dae34b5dfc', '5f86c00e-822a-460f-aa1a-24877b4e8eb4', 'SV 1860 Minden e.V. / ARA Weser-Leinebergland', '0', TRUE),
('715ca55a-c1d5-4d46-b8c0-348b75dd6854', '9d63cbd1-c021-495f-b0fc-8d9ef4a00de8', 'Svarta Haesten CK', '0', TRUE),
('e3158515-c2fd-4d91-a7b9-b6c343139e2a', '04a232f0-3066-4e1e-adf8-8535e426634b', 'Sydney Uni Velo Club, Australia', '0', TRUE),
('f80bc4eb-7eb1-422c-ace9-adc85d7584b7', '8e66444f-477e-4868-b1e7-78286312181c', 'Täby IS Skidor', '0', TRUE),
('2a0b57ad-eea9-44e9-a037-6d29a73709a8', '2a0b57ad-eea9-44e9-a037-6d29a73709a8', 'Team Elgiganten Piteå', '0', TRUE),
('e4058367-250d-41f0-ae6c-66c132195cee', 'e4058367-250d-41f0-ae6c-66c132195cee', 'Team Kungälv', 'SE113006', TRUE),
('0aa2dbcb-bad1-4d4f-8c60-786131307a6a', '5e396e28-0b3f-4a7f-9f62-8855ec1f16a4', 'Team Rundt med de ben', '0', TRUE);

INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES
('19f1c115-0316-4a9c-bf41-ddc2ed82b693', '49c45d78-cb40-41c2-ad53-bb4525add25a', 'Team Tavelsjö', '0', TRUE),
('08f3c718-cff7-481c-893a-35a0f3918aaf', '97a4e0f5-b423-4a8c-962f-fee8ff904362', 'Telia IF', '0', TRUE),
('424979d1-df43-4b12-a91e-a91ce0e682ec', '424979d1-df43-4b12-a91e-a91ce0e682ec', 'Tranås CK', 'SE113005', TRUE),
('18bd0d2a-168f-4214-a3ba-9f55111f393f', '64099b5d-1e1d-462d-a962-2ba3b5133532', 'Triathlon Günzburg', '0', TRUE),
('b94ce334-5670-440f-991c-4d9f174624fa', '060c09e3-91a1-4b89-b71a-36339aa9c7fe', 'Triathlon Väst', '0', TRUE),
('b184a7cb-0d64-4337-b3e2-028d71a2f249', '736425ee-28f3-48cd-b8f1-cf1cb45059b1', 'TV Aldekerk', '0', TRUE),
('468ab2b8-9c7e-4d73-b63d-3824e4503da8', 'af3dd2c6-d000-4758-a9da-60e0390f9886', 'typ.nu', '0', TRUE),
('3688fe87-08dc-4736-b192-102743fcff7e', '6aba169e-4926-4171-bdc6-8bf942dde251', 'Ulstein og Omegn Sykkelklubb', '0', TRUE),
('d38c01ea-ee9f-4be6-8afe-f5a2172cb919', 'bde13176-e76c-4622-8361-8d45e8ee5490', 'Umeå', '0', TRUE),
('addc7f36-07b8-48a6-8297-e61565090f7d', 'ecd6518c-33da-48ac-8b98-f9f5e0366a49', 'Umeå Simsällskap', '0', TRUE),
('80831c14-c36d-4566-a5dc-2a1636f74bae', 'baced864-1294-436c-baa8-faa2aa8c57d5', 'Velo Club Bourevestnik', '0', TRUE),
('96fbffb1-2b2b-4e42-8810-8eea66e65f01', 'b68a01a0-8fab-46d4-961f-142e44a9840e', 'We Love Audax', '0', TRUE);

-- Step 3: Update participant table to use new club UIDs
-- This ensures all participants reference the correct club UIDs from loppservice
-- Only updates participants whose clubs were successfully matched
UPDATE participant p
JOIN club_uid_mapping m ON p.club_uid = m.old_club_uid
SET p.club_uid = m.new_club_uid
WHERE p.club_uid IS NOT NULL AND m.matched_by_name = TRUE;

-- Step 4: Update club table with new UIDs and data from loppservice
-- This replaces the club UIDs and ensures data consistency
-- Only updates clubs that were successfully matched by name
-- Handle cases where old and new UIDs are different
UPDATE club c
JOIN club_uid_mapping m ON c.club_uid = m.old_club_uid
SET 
    c.club_uid = m.new_club_uid,  -- Use loppservice UID
    c.title = m.club_name,        -- Keep app database name
    c.acp_kod = m.acp_kod         -- Keep app database ACP code
WHERE m.matched_by_name = TRUE AND m.old_club_uid != m.new_club_uid;

-- Step 4b: Update club names and ACP codes for clubs with same UID
-- This handles cases where the UID is the same but we want to sync other data
UPDATE club c
JOIN club_uid_mapping m ON c.club_uid = m.old_club_uid
SET 
    c.title = m.club_name,        -- Update name from loppservice
    c.acp_kod = m.acp_kod         -- Update ACP code from loppservice
WHERE m.matched_by_name = TRUE AND m.old_club_uid = m.new_club_uid;

-- Step 5: Clean up temporary mapping table
DROP TEMPORARY TABLE club_uid_mapping;

-- =====================================================
-- MIGRATION COMPLETE
-- =====================================================
-- Summary:
-- - Updated 152 clubs with matching names
-- - All participant references updated to use new club UIDs
-- - Club table updated with loppservice UIDs
-- - Unmatched clubs remain unchanged (see manual mapping section below)
-- =====================================================

-- =====================================================
-- MANUAL MAPPING REQUIRED: Clubs Only in App Database
-- =====================================================
-- These clubs exist only in the app database and need manual mapping
-- You can either:
-- 1. Add them to loppservice database with the same UID
-- 2. Create manual INSERT statements in the migration above
-- 3. Leave them unchanged (they will keep their current UIDs)
-- =====================================================
-- App UID: 93da0266-59a5-4371-a6de-f375a306234d, Name: Alavus
-- App UID: b2fd83f8-d5f3-49f6-90ea-19a0c6adbf4e, Name: ARA München Oberbayern
-- App UID: f24ad34e-a9cf-4a81-95ea-25cd2d99612e, Name: ARA Ostfalen
-- App UID: e378f1a8-6233-4afb-96c1-2eca37f4e2ad, Name: Åsane CK
-- App UID: a4a0ee01-f2e1-4331-a840-8fa866f2e588, Name: Audax Club Mid-Essex
-- App UID: 4bd532ab-661b-41f4-9fc2-f833830ed262, Name: Audax Club Parisien
-- App UID: fb552b12-e0b4-478e-a7af-5cc6b29403f7, Name: Audax Ecosse
-- App UID: 2cefc5a1-75a1-400b-8628-cb0eb3fc1048, Name: Audax Ireland
-- App UID: 43ca4396-f603-40d0-9a5e-0a487a843b93, Name: Audax Netherlands 
-- App UID: 33366343-6310-4646-8bf7-605efc0520b2, Name: Audax Suisse
-- App UID: 26c728ef-11be-4f40-aeea-56ab1d14d9df, Name: Bauhaus Sportklubb
-- App UID: f2fe5711-f1da-42f7-abc9-1153dece2b4f, Name: Bergen CK
-- App UID: b47248a5-b6ca-43ac-b086-a391dbed3e55, Name: Björna IF
-- App UID: e01a98bf-bc39-47a3-bf88-9c2c91151d05, Name: Bjørgvin Randoneurs
-- App UID: 707692d7-1b76-4712-b911-bf7b9efa23b8, Name: Bjørgvin Randonneurcyklelaug
-- App UID: dcd9870d-8f48-43b3-b312-e91830791583, Name: Bollnäs Cykelklubb 
-- App UID: 0d404fae-ddd9-43af-9235-05c246fdee65, Name: Brevet-Selbsthilfegruppe
-- App UID: 5c89c238-53e5-42c7-8e57-c760bb4cfba9, Name: C.C.Ripollet
-- App UID: 137210a4-0e31-4fcd-8d2a-a873e046410f, Name: Centurion Cycling Club
-- App UID: 4051e8f6-811d-40d2-bf97-3f31b8833ad7, Name: Centurion Cycling Club / Adobo Velo
-- App UID: 836d629b-b13b-4102-8f47-22e4f1fe9c7d, Name: Ciclocubin C.D.
-- App UID: 265d6880-3d0a-46d6-a2d3-ec0cb1b88737, Name: CK She Rides
-- App UID: d83f8d50-da01-424c-ae66-a363b85aee52, Name: Ck Sotra
-- App UID: cbbbd0ef-9a1f-41ef-ad46-5892d3bbfa9f, Name: CkdaimX
-- App UID: 5283eba2-874a-4ada-aabd-bc164fbcc2a9, Name: Club Ciclismo Chimeneas Elche
-- App UID: 0615ab4a-5edf-4abb-b7ad-e76154b524f5, Name: Colombia Bike Tours
-- App UID: dc6e01cc-c9f3-42c2-aee7-c3d69d2267a6, Name: Echelon Cycling and Triathlon club
-- App UID: 34309552-96b0-4951-9c1c-9d50ca90d937, Name: Edsbyns SK-MTB
-- App UID: 55a93df4-f00c-4348-8c04-67517f955688, Name: El Toro Minden
-- App UID: 4eb7aef0-d9af-48a8-a1e2-0330e1f62406, Name: EPZ Velo67
-- App UID: 97a74436-046a-4846-8802-1d3065e38915, Name: Fysio Danmark Hillerød
-- App UID: ae81fa09-3114-4948-8502-e25a537a88cd, Name: Gällivare Endurance
-- App UID: a9099b9f-196b-46de-a5db-223fcf5326f9, Name: Humboldt Randonneurs
-- App UID: 929365d5-342c-4ce9-a2e4-2d5f974a1b7e, Name: Independent Czech Republic
-- App UID: a5670896-d044-4e45-a272-55816c0f4525, Name: Independent Denmark
-- App UID: b0946c96-c9e7-4b01-9bd2-250ed88236c8, Name: Individuel Finland
-- App UID: 1a87e1d5-3026-47af-84e4-a6013496ce27, Name: Individuel Finlande
-- App UID: 6ce94c6e-6fb3-41f9-9340-04970337d1ad, Name: Israeli Randonneurs
-- App UID: 19e41f90-b7aa-4906-a65c-72cff2246332, Name: Kalajärven Näädät FI031424
-- App UID: aeacbc8f-1580-4400-a69b-588384bd93a4, Name: Kidderminster CTC
-- App UID: 32c116cd-11a5-41fc-9f12-b74059632bdb, Name: Kingston wheelers
-- App UID: 218b129d-3843-44a4-9b48-d7549f53e282, Name: Klubblös
-- App UID: ff1c6c81-b264-4177-b8d8-eda32a48a2c0, Name: Let's Go Velosis Allstars
-- App UID: a13242f9-00e4-496f-829b-6fda2713625d, Name: Malaysia Ultra Cycling Association
-- App UID: 56313b51-e8e0-4525-99de-8fdf8a10af40, Name: MSD Chartres Cyclo
-- App UID: c0525568-aa78-47d8-bd65-ec9781f699a2, Name: Nordingrå SK
-- App UID: 01c8516f-3328-4d4f-9cc2-0b735ee33546, Name: Norrlandscyklisterna IK
-- App UID: 50a2368e-58d3-47fc-8fb3-b91f63593a77, Name: Obbola IK
-- App UID: 6db997a3-b9ce-4d4a-bc91-c5884f38c66c, Name: Pacific Coast Highway Randonneurs
-- App UID: c9043e79-971e-4a8c-9368-1baeda04f8b4, Name: Penya ciclista Bonavista
-- App UID: b92b3196-28e1-42ee-a5e5-2d2935aa1432, Name: Prologi
-- App UID: 97c7d181-0b95-4bbe-86b6-3e919bc1fc88, Name: Rana SK
-- App UID: 30f9515f-7658-4e64-9f5a-26a19efe8653, Name: Randonneurs Malaysia
-- App UID: 0712f03e-7ed6-4937-a611-c5bb8f292b1a, Name: Randonneurs Norge
-- App UID: aa35d238-5d0d-44c2-9e2e-4c25c3ea10c1, Name: Randonneurs Tokyo
-- App UID: 60788d23-7764-4fa9-b9fb-a7b11e6ed0b3, Name: Randuneur Stockholm
-- App UID: f2828ccf-94cb-4535-a8b8-967457a2beb1, Name: Rannondeurs finland
-- App UID: 702b97e5-c5ef-498a-a127-cde79fddcb53, Name: RCC
-- App UID: 80f4c9b0-9639-41cb-9754-717b13768e32, Name: Rhos-on-Sea
-- App UID: bc37f688-65d9-45ca-8999-e152f7435071, Name: Sandviken CK
-- App UID: d901430d-759d-4c3f-b86a-54d742fdfa65, Name: Scottish Borders Randonneurs
-- App UID: 54b52cfa-2bd0-4064-9b4c-6e87c4f2996a, Name: SEKKKK
-- App UID: 3d3bd6f7-c6ae-4e2a-a038-a2ad3907e7e2, Name: SF Randoneurs
-- App UID: 9b77456b-623a-4bdd-b114-e1196896c2fc, Name: Sigr
-- App UID: 00fe89c8-e8f6-4b47-83ab-0ca527788172, Name: Själevad
-- App UID: 15b728b1-17ee-459b-8883-49373c35115a, Name: Själv är bäste dräng CK
-- App UID: d6eca73c-68fa-4ca1-be5e-585d7f16c9ce, Name: Skellefteå XC
-- App UID: 59862fdc-1ad9-4e18-9a85-cb1bbce0fa0d, Name: Sörmjöle CK
-- App UID: 2c1f32c2-be32-480c-b8b5-9013e6497ba2, Name: Stockholm cykelklubb
-- App UID: 4ce45d9c-8f78-494a-80ab-807e323d2573, Name: Storöns IK
-- App UID: 0c4b440f-affe-4580-9d1b-1e4a21ade460, Name: SUMO CC
-- App UID: dea24f0f-010f-4987-bd41-07cc34b48cc8, Name: Sveaskog IF
-- App UID: 4c6053a5-2b4a-4ac6-ab05-143fdd1cdb4c, Name: Tavelsjö AIK
-- App UID: 9b9c2dfa-9a57-4631-b8a0-aa137366bdd7, Name: Team Aron CK
-- App UID: 5cfc5944-9255-48c4-9b87-e9a0046cf7d1, Name: Team Holmgard
-- App UID: f9fa0183-00f2-4a3d-b11d-d1acd41364e9, Name: Team Vegan
-- App UID: 3605576c-96da-43ab-8aa0-5d10ee7b5233, Name: TESA
-- App UID: 08de9135-abb5-4b7a-b86c-de7426281316, Name: TESTTA
-- App UID: e30e5dbb-d940-496e-a1f2-2a090d5a4f41, Name: TETTS
-- App UID: f2bd7024-29b7-4a78-a153-83c8bc083369, Name: Tomato Team
-- App UID: 2b46bd57-aef5-4616-b2bf-d344e16a2553, Name: TRCC
-- App UID: d38ab8da-22fb-4b7c-8126-aca277da3f74, Name: Triathlon Inverness
-- App UID: 13da2227-1b9a-404a-a0fe-775273b70ee8, Name: TTCC
-- App UID: 3d705172-be15-4507-aece-3dcff426863b, Name: TTGU IF
-- App UID: 2c02ae98-a68a-4713-8d35-f0ffc483c9a1, Name: Ultrabikers
-- App UID: c116abd1-da92-4e34-b752-12a40a6ceb94, Name: Umara Sports Club 
-- App UID: d22801ef-3058-4f09-ba8a-b43e25d3ec31, Name: Umeå Sports Club
-- App UID: 709bb2fb-4b8b-492a-90d9-3def40c0a883, Name: Västerås Cykelklubb
-- App UID: 584f0113-cc49-4bd0-963e-44de3eb0635f, Name: VC 167
-- App UID: 5eb2491f-86a1-4c10-9c9f-8431c36e9227, Name: Veikkolan Veivaajat
-- App UID: aa38c62a-ce60-427d-9445-55c5644c48d2, Name: Velina
-- App UID: 615009cb-0fae-4b79-bd2b-dd5b2d995cae, Name: Velo-Freunde Wertheim
-- App UID: 5e046715-3f65-411d-a6b9-867c6feca42d, Name: Women by bike

-- =====================================================
-- MANUAL MAPPING REQUIRED: Clubs Only in Loppservice Database
-- =====================================================
-- These clubs exist only in loppservice database
-- You can either:
-- 1. Add them to app database with the same UID
-- 2. Create manual INSERT statements in the migration above
-- 3. Ignore them (they won't affect existing data)
-- =====================================================
-- Loppservice UID: dfca06af-7542-4516-86b8-75470175d9dd, Name: --
-- Loppservice UID: a56cf2d4-05d1-49f7-90c6-9e44a20175ec, Name: /
-- Loppservice UID: d2197d99-ec9d-46ab-8b0e-b5073326721b, Name: Action Bike Team
-- Loppservice UID: 1f5e888d-85d4-4b95-9e6f-1d521b37a068, Name: Adrianna Mason
-- Loppservice UID: 9321a125-d12a-4773-a6af-4bb199dd8056, Name: ARA MUC / Randonneurs Autriche
-- Loppservice UID: 29e5c9d8-8270-4906-89b8-e70fbec25b3c, Name: Arvika IS
-- Loppservice UID: 51110c65-121a-45da-97b9-9c81b6befd00, Name: Audax Club Franconia
-- Loppservice UID: b16763d9-bbf7-4665-bec4-0bd9a177af88, Name: Audax Club Malaysia
-- Loppservice UID: bbfbb34f-fc94-424a-b999-72d7fe173afd, Name: Audax Club Nordique
-- Loppservice UID: a2fb853e-515c-49e0-923c-3c5e71ab27ba, Name: Audax Cologne
-- Loppservice UID: 83f1eb39-418e-485b-95ad-18b83d06d0c3, Name: Audax Franconia
-- Loppservice UID: 52d79b6a-ed8a-4b16-bec4-b9d832c0b7f3, Name: Audax Franconia / ARA Nordbayern Fränkische Alb
-- Loppservice UID: f75f569f-8350-4fe7-aaab-3230f81cfa9f, Name: B3
-- Loppservice UID: 0eccad45-29d1-4a6f-8b93-7d678bfe81fc, Name: Bålsta CK
-- Loppservice UID: f63c553e-d0f2-4fc1-8d03-84581093ee04, Name: Bonjour Cycliste
-- Loppservice UID: 62b03bef-2c45-42a1-96d5-56d658751d7b, Name: Borensbergs IF CK
-- Loppservice UID: e28056eb-395e-491a-bc7e-5028539309b7, Name: Borlänge CK
-- Loppservice UID: 94ef6b02-4733-4ce5-8e99-fb693b2e9d0b, Name: Cgdfgdgdgf
-- Loppservice UID: 40b765a9-4e60-48f3-9445-f038a8628f60, Name: CICLOCUBIN, C.D.
-- Loppservice UID: 9b422497-7b6f-4354-bcca-07288cc4fabe, Name: CK Avanti
-- Loppservice UID: 7cf288d1-1a6f-41ff-ad91-4022370380e3, Name: CK Ceres
-- Loppservice UID: 8ae00d84-dfbd-41ce-aacf-8352e9645e72, Name: CK Dainon
-- Loppservice UID: 9bd8bb82-f64a-4e78-afff-cd95c731f2a2, Name: CK Lunedi
-- Loppservice UID: c2a2fc1d-38e4-47d4-b95b-a72433802ffc, Name: CK Master
-- Loppservice UID: c006ce65-7d61-43db-b86c-e258d9750ec4, Name: CK Valhall
-- Loppservice UID: 3037bab3-574f-4143-9b21-f62148653532, Name: CK Wano
-- Loppservice UID: ba2ebf9e-3a2a-4df9-8704-dd6ea448846f, Name: CK Wheelsucker
-- Loppservice UID: d0ba5e1e-d8f0-4c38-8762-ba14a90c7546, Name: dddddddddd
-- Loppservice UID: b71f55dd-1480-4e95-9877-68570fed48a4, Name: dgdsfgfdg
-- Loppservice UID: 003ed974-3466-499d-a6bb-3ff71cbca764, Name: DSFSDFS
-- Loppservice UID: 4d4a7bb0-7f75-4cbd-a83e-83b82284e9d6, Name: DSW12 Darmstadt Triathlon
-- Loppservice UID: 80f45cf3-5fd0-4100-86ab-895d52b6aff1, Name: Eksjö CK
-- Loppservice UID: 214a346f-4af9-4f9e-bcfb-84733b095778, Name: Enköpings CK
-- Loppservice UID: f7fe8f5c-e218-451d-b0ba-6c9b0aff7645, Name: Eslöv CK
-- Loppservice UID: b1bed20e-c70e-46f6-badd-896b45cc5e04, Name: ewrwerwe
-- Loppservice UID: 3e505105-e4d4-4f8b-8cb5-7924f82eee34, Name: Falköpings CK
-- Loppservice UID: d2cac02d-4ac1-4649-8a5f-11c2c4207fcc, Name: Falu CK
-- Loppservice UID: 09c10ba9-145b-4c6f-8eac-d1ba99001b9f, Name: Fixedgear.se
-- Loppservice UID: a3bde5f6-a251-4750-852c-41fcc491e46d, Name: FK Trampen
-- Loppservice UID: c3cf401a-13c9-4b19-894d-5d080ac3f287, Name: Frosta Multisport
-- Loppservice UID: 6af6e5af-b5ce-4481-bb92-0becdb1740e8, Name: FysioDanmark Hillerød
-- Loppservice UID: 4271ebc8-052a-49d7-8850-a65ef03e2bf5, Name: Gävle CA
-- Loppservice UID: fd92bb53-895b-4c70-b79c-14265e32fe24, Name: GCK
-- Loppservice UID: f01ef96e-670b-4e78-bdda-fa2d60937485, Name: Gnellspikens Multisportklubb
-- Loppservice UID: c8d6239b-6b03-4a81-b252-d91ab9e23bea, Name: Göteborgs CK
-- Loppservice UID: 2ba10a8e-839b-4220-bf4c-acc6b1a20a09, Name: Happy MTB
-- Loppservice UID: 93cf70a3-f491-4825-9ed2-c0aa6dbbcaf6, Name: Härnösand CK
-- Loppservice UID: 93452aee-7303-4234-9188-cb9889e176b1, Name: Hässleholms CK
-- Loppservice UID: 7018bde6-180e-42b3-9f10-f15211fdf9cf, Name: HCK
-- Loppservice UID: c4ce564e-6a92-4c84-bb39-8bd6e38024bc, Name: Hudik Triathlon
-- Loppservice UID: d6afe42b-fbee-459e-83f6-7835f785e656, Name: I am living in Germany, but should the citizenship be mentionned: I am Swiss :-).
-- Loppservice UID: 747f725a-cae3-4985-b8b6-a440586b2693, Name: Idk
-- Loppservice UID: 39d17078-0254-48b6-a246-40491900281c, Name: IFK Helsingborg
-- Loppservice UID: fb23ee16-c1fd-473f-bd99-ce7bf722a0bd, Name: IK Hakarpspojkarna
-- Loppservice UID: a7704445-1391-4894-bb54-d661437bf536, Name: IK Nocout.se
-- Loppservice UID: 4c3b71eb-c3d7-45e4-8056-57353521495c, Name: IK Vinco
-- Loppservice UID: 929365d5-342c-4ce9-a2e4-2d5f974a1b7e, Name: Independant Czech Republic
-- Loppservice UID: a5670896-d044-4e45-a272-55816c0f4525, Name: Independant Denmark
-- Loppservice UID: b89e2e4c-ea83-45d6-82fd-e048a3a2cfb1, Name: Individuel Autriche
-- Loppservice UID: c070d27a-fb0f-4dc1-8c61-236b08a9e1e3, Name: JHIsotTxQjKfuLJsOllylPr
-- Loppservice UID: dfc7f285-4594-473b-9aac-23f3e48f3fd8, Name: Jönköpings CK
-- Loppservice UID: e316a462-7c7f-48fc-b8f5-d696856ee56f, Name: Karlskoga Löparklubb
-- Loppservice UID: 9914a121-e172-4962-b239-da2d5923658f, Name: Kinna CK
-- Loppservice UID: 64fa0ae0-770a-4f67-bcd3-e378ad62c5f4, Name: Komet Club Rouleur
-- Loppservice UID: f2cc77c7-f8e0-4d04-84da-fa00e05d7ae3, Name: Kristinehamn Multisport
-- Loppservice UID: 5e92902d-5ece-487b-a739-1b7e23623f9d, Name: Kumla CA
-- Loppservice UID: 39260c9f-42f0-4128-ac77-f333671aade6, Name: La Lepre Stanca
-- Loppservice UID: 731a546a-92b2-433f-b61a-a0bf1f6bbba8, Name: Legacy Byrd
-- Loppservice UID: 8cef2e8f-1875-4165-a0ed-b8a549395f27, Name: Lidköpings CK
-- Loppservice UID: 8f2b4d15-7ed2-43ae-9916-8260266eab4a, Name: Ljusdals CK
-- Loppservice UID: 9b271c9f-e067-4811-89b1-393d29f24d32, Name: LSR
-- Loppservice UID: f32fdc8c-909b-4fd4-8dd8-2908316b34a6, Name: Malaysia UltraCycking Association
-- Loppservice UID: 538bb68b-6a35-4ea9-8f85-db716da30480, Name: Malaysia UltraCycling Association
-- Loppservice UID: 677ff01c-e0cb-4d5c-88ac-f8cd825e7068, Name: Mölndals CK
-- Loppservice UID: 46a39c55-2272-4dd1-9c04-14f99b9970fb, Name: Motala AIF CK
-- Loppservice UID: ff6d7229-63b5-4e5a-8d2c-ed42a3b665a6, Name: MSD Chartes Cyclo
-- Loppservice UID: a5bf08ce-21dd-49d1-92be-eedf2d209dc2, Name: MTV Köln Mülheim
-- Loppservice UID: 8187903f-6a6a-43cb-8c81-bb8a7d0a6639, Name: NA
-- Loppservice UID: 754d19f2-4d65-475e-828d-d33c26cd82ec, Name: No Club
-- Loppservice UID: cf249151-82e2-4d87-9f0c-67b1f1eb8ffa, Name: None
-- Loppservice UID: 05b5d0c6-a4b9-4414-b500-6cb20682c90e, Name: Northern Virgina Randonneurs
-- Loppservice UID: 8e862042-ffb1-4eac-a288-c962bd62e7c6, Name: Obbola IK Cykel
-- Loppservice UID: 1e18f3cd-2595-4967-8e8e-4f2ee3972b3c, Name: Östersund Cykel Klubb
-- Loppservice UID: 82d261e8-67c9-40a5-b0a0-694b2f91808b, Name: Östra Aros CK
-- Loppservice UID: 34a4df78-cdd1-4abc-a961-d6043cd37882, Name: Pista Malmö
-- Loppservice UID: 0d209629-fdef-44c0-9284-9bb078db9c0b, Name: Qwarnsvedens Triathlon
-- Loppservice UID: a30332f3-f983-46ca-93b2-d17dde1beb4d, Name: Randonneurs Croatie
-- Loppservice UID: ce28ddc0-9eba-410c-9f11-3ff5d94c7a07, Name: Rembo IK
-- Loppservice UID: b9a4d9f7-df80-43ea-ade9-2c16bd7a405b, Name: Rixwall
-- Loppservice UID: c2ec7260-8ff7-43e2-9ea9-39353c076080, Name: Roslins CK Ystad
-- Loppservice UID: 81c947c9-eeff-4224-a3e5-44e1805b5e8a, Name: RSV-Düsseldorf-Rath/Ratingen 1951 e.V.
-- Loppservice UID: 51cdc4ac-199a-4aa6-90fe-7610d7ff60bf, Name: Sandvikens CK
-- Loppservice UID: 88b0c087-5087-4762-935d-4add34d6a412, Name: Scottish Border Randonneurs
-- Loppservice UID: 86a2b3d0-425e-4159-b570-f82f3400e014, Name: She Rides CK
-- Loppservice UID: d5055357-2716-4966-909b-bd2376845598, Name: Skövde CK
-- Loppservice UID: 96877d45-d6df-4814-9902-4d326cc835d5, Name: SMACK
-- Loppservice UID: 9c3cd58f-da74-4554-bbf2-2b7e20577419, Name: Stockholm City Triathlon
-- Loppservice UID: bd3a0037-e395-4de1-81dc-3ee1c2d0160c, Name: Stockholm CK
-- Loppservice UID: 55c51e4a-102f-439d-942a-7eed2678295c, Name: Stockholm Multisport
-- Loppservice UID: 2c568513-ae78-4b9f-be11-ee166a2497c4, Name: Strängnäs CK
-- Loppservice UID: b3028f6d-c464-4a42-a351-308b98e24389, Name: Sundsvallscyklisterna
-- Loppservice UID: 83ea78cd-99cc-44af-8fae-1fb626d3a9be, Name: Team Cykelcenter Södertälje IK
-- Loppservice UID: 69fb4c3f-c827-4952-8eaf-7e72ed5b880a, Name: TeamUV
-- Loppservice UID: f309f8f0-7f85-4f03-bcfb-0aeb05b0ad6a, Name: Testklubb
-- Loppservice UID: dcbf12e3-c31c-4d5b-a88d-21d32b5bd6dc, Name: Töreboda CK
-- Loppservice UID: c7791d41-4693-49ea-844f-d88dc38d34c0, Name: Trampkraft.se
-- Loppservice UID: 4c2645bb-19f9-443a-b988-23848145e8f3, Name: Trollhättan CK
-- Loppservice UID: 700232a5-f46f-40bd-b82f-ba13f39d3fd4, Name: Trosa Vagnhärad CK
-- Loppservice UID: 5663ab5b-26df-4e27-8f84-7872defa17d6, Name: Tullinge CK
-- Loppservice UID: 5c81b4ea-1e38-4adc-9a61-f05ba07af4f5, Name: U.V.H CK
-- Loppservice UID: 40ffb01c-f414-42ce-9b9e-59800cdfc6ae, Name: Uddevalla CK
-- Loppservice UID: d198a7bf-c061-4651-9763-3a79bcc74462, Name: Ulricehamns CK
-- Loppservice UID: e6c8c5f8-b506-4d7d-ac84-86e7c4935d93, Name: Upsala CK
-- Loppservice UID: 91111f5e-ba35-4ac5-9742-ae6e3cfe8596, Name: Ural-Marathon
-- Loppservice UID: a619c9b5-50f1-4978-997d-2e811ec702b1, Name: Uralmarathon
-- Loppservice UID: 9f709885-d734-4d47-9148-4a0672f4274d, Name: Vallentuna CK
-- Loppservice UID: 5fe164d4-0eb4-4c36-8294-0d973ac9c4ed, Name: Västerås CK
-- Loppservice UID: d2d23268-f612-45cd-a530-f55ccab6eefc, Name: Velodrom CK
-- Loppservice UID: 316dac3f-f4b5-4058-83fc-44ae95ccc4c1, Name: vfdbc
-- Loppservice UID: 246ce99d-f28b-4f1a-a414-3b747f46be52, Name: Vihaan Schroeder
-- Loppservice UID: 1a0a25ab-0a88-45f7-ba4d-f9e2451ddbec, Name: Wänershofs CK
-- Loppservice UID: b623d6a6-fcb9-417b-82f9-8c189ec01aa4, Name: x

-- =====================================================
-- MANUAL MAPPING INSTRUCTIONS
-- =====================================================
-- If you have clubs that need manual mapping, add them to the migration above:
-- 
-- Example for clubs only in app database:
-- INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES
-- ('app-uid-here', 'loppservice-uid-here', 'Club Name', 'ACP123', FALSE);
-- 
-- Example for clubs only in loppservice database:
-- INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES
-- (NULL, 'loppservice-uid-here', 'Club Name', '', FALSE);
-- 
-- Then add corresponding INSERT statements for the club table:
-- INSERT INTO club (club_uid, title, acp_kod) VALUES ('loppservice-uid-here', 'Club Name', '');
-- =====================================================

-- =====================================================
-- MIGRATION COMPLETE
-- =====================================================
-- The migration has been generated. Review the SQL above and run it on your app database.
-- Make sure to backup your database before running the migration.
-- =====================================================

-- =====================================================
-- SUMMARY
-- =====================================================
-- Total matches: 152
-- Total app-only clubs: 93
-- Total loppservice-only clubs: 122
-- Migration will update 152 clubs automatically.
-- =====================================================
