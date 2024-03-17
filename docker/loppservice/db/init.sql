SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `adress` (
  `adress_uid` char(36) NOT NULL,
  `adress` varchar(100) NOT NULL,
  `person_person_uid` char(36) NOT NULL,
  `postal_code` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `country_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `categoryID` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`categoryID`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Clothes', 'Kläder', '2023-09-30 08:22:32', '2023-09-30 08:22:32'),
(2, 'Food', 'Food', '2023-09-30 08:22:32', '2023-09-30 08:22:32'),
(3, 'Activity', 'Activity', '2023-09-30 08:22:32', '2023-09-30 08:22:32'),
(4, 'Other', 'Other', '2023-09-30 08:22:32', '2023-09-30 08:22:32'),
(5, 'Service', 'Event service for participants', '2023-09-30 08:22:32', '2023-09-30 08:22:32'),
(6, 'Registration', 'Event registration product category', '2023-10-17 05:31:52', '2023-10-17 05:31:52'),
(7, 'Reservation', 'Event reservation product category', '2023-10-17 05:31:52', '2023-10-17 05:31:52'),
(8, 'Medal', 'Event medal', '2024-01-06 19:23:11', '2024-01-06 19:23:11');

CREATE TABLE `clubs` (
  `club_uid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `official_club` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clubs` (`club_uid`, `name`, `description`, `official_club`, `created_at`, `updated_at`) VALUES
('2b973d70-1c5d-46ff-aa65-b0861fadc888', 'Kidderminster CTC', NULL, 0, '2023-09-30 18:01:30', '2023-09-30 18:01:30'),
('3f9201bd-6f00-41ce-bf57-592c2d2d148c', 'Audax UK', NULL, 0, '2023-09-30 18:02:02', '2023-09-30 18:02:02'),
('81a6e4d5-c4c4-4802-ab2d-86b2447e508b', 'ARA Ostfalen', NULL, 0, '2023-09-30 18:03:40', '2023-09-30 18:03:40'),
('72bd4572-8e78-4794-8f52-e795e902fcda', 'Scottish Birders Randonneurs', NULL, 0, '2023-09-30 18:04:28', '2023-09-30 18:04:28'),
('217970e6-8546-4f41-a962-9774c813436b', 'Audax Randonneure Allemagne', NULL, 0, '2023-09-30 18:05:10', '2023-09-30 18:05:10'),
('6aa4d645-4b27-4f78-8551-4343bacd0905', 'San Francisco Radonneurs', NULL, 0, '2023-09-30 18:05:32', '2023-09-30 18:05:32'),
('8dc3ca57-e513-447a-96fb-f1539c8db669', 'Triathlon Inverness', NULL, 0, '2023-09-30 18:06:27', '2023-09-30 18:06:27'),
('769c2c3d-4d32-49d7-a32f-540d24457b14', 'Scottish Borders Randonneurs', NULL, 0, '2023-09-30 18:10:24', '2023-09-30 18:10:24'),
('690b1862-d320-4c57-94e6-e0f634249a8d', 'SF Randoneurs', NULL, 0, '2023-09-30 18:16:53', '2023-09-30 18:16:53'),
('4f52601e-b8e2-4ea3-bfe4-d81ca8d5b02d', 'Centurion Cycling Club', NULL, 0, '2023-09-30 18:42:17', '2023-09-30 18:42:17'),
('72103394-684e-42d1-b9e9-4d2a5c2379d4', 'Centurion Cycling Club / Adobo Velo', NULL, 0, '2023-09-30 18:43:34', '2023-09-30 18:43:34'),
('ea1e7ab0-8ba2-406a-bd97-21411218e690', 'Randonneurs Malaysia', NULL, 0, '2023-09-30 22:21:00', '2023-09-30 22:21:00'),
('fc0bf2fa-6de3-4d6d-a60c-eb8bc85bf755', 'Randonneurs finland', NULL, 0, '2023-09-30 22:28:22', '2023-09-30 22:28:22'),
('1d86e498-ed98-47b7-b4e3-5f1346445e3d', 'none', NULL, 0, '2023-09-30 23:46:55', '2023-09-30 23:46:55'),
('d308ff3e-22b5-4992-9b85-6649a51858b2', 'Audax Ireland', NULL, 0, '2023-10-01 01:41:43', '2023-10-01 01:41:43'),
('8b717c31-2c50-4c73-b81a-36d33e73a351', '-', NULL, 0, '2023-10-01 02:09:03', '2023-10-01 02:09:03'),
('6d3e8a0c-9c01-4cec-9981-148c91e37a3f', 'Pacific Coast Highway Randonneurs', NULL, 0, '2023-10-01 03:11:53', '2023-10-01 03:11:53'),
('e3386735-213f-4e45-bd16-47038fc8aeb7', 'ARA München Oberbayern', NULL, 0, '2023-10-01 04:26:32', '2023-10-01 04:26:32'),
('e5ab284b-bfa7-4420-a433-886ca875b27a', 'Kingston wheelers', NULL, 0, '2023-10-01 08:41:04', '2023-10-01 08:41:04'),
('f390d9f9-98c2-49e6-9a79-3968e9a307c9', 'ARA Ruhrgebiet', NULL, 0, '2023-10-01 10:06:53', '2023-10-01 10:06:53'),
('5a033adc-3d04-4b73-a405-20d7b30e3603', 'Fredrikshov', NULL, 0, '2023-10-01 11:28:48', '2023-10-01 11:28:48'),
('f89a41ee-8906-405b-97da-7e050bcc1774', 'Randonneurs NL', NULL, 0, '2023-10-01 13:09:22', '2023-10-01 13:09:22'),
('09c23c33-d8aa-4029-9a69-6df52b4939a9', 'Audax Allemagne', NULL, 0, '2023-10-01 13:49:20', '2023-10-01 13:49:20'),
('c287a910-d7ce-4760-a67d-7fa84069bad4', 'Bodens cykelklubb \"CK Sävast\"', NULL, 0, '2023-10-01 14:16:18', '2023-10-01 14:16:18'),
('73035c1d-7803-497d-8088-c4b5f5db455a', 'Ck Sävast', NULL, 0, '2023-10-01 14:18:31', '2023-10-01 14:18:31'),
('70d64105-79c6-45d9-aca5-0052095b3f22', 'Not part of a club', NULL, 0, '2023-10-01 15:31:58', '2023-10-01 15:31:58'),
('2b973d70-1c5d-46ff-aa65-b0861fadc888', 'Kidderminster CTC', NULL, 0, '2023-09-30 18:01:30', '2023-09-30 18:01:30'),
('3f9201bd-6f00-41ce-bf57-592c2d2d148c', 'Audax UK', NULL, 0, '2023-09-30 18:02:02', '2023-09-30 18:02:02'),
('81a6e4d5-c4c4-4802-ab2d-86b2447e508b', 'ARA Ostfalen', NULL, 0, '2023-09-30 18:03:40', '2023-09-30 18:03:40'),
('72bd4572-8e78-4794-8f52-e795e902fcda', 'Scottish Birders Randonneurs', NULL, 0, '2023-09-30 18:04:28', '2023-09-30 18:04:28'),
('217970e6-8546-4f41-a962-9774c813436b', 'Audax Randonneure Allemagne', NULL, 0, '2023-09-30 18:05:10', '2023-09-30 18:05:10'),
('6aa4d645-4b27-4f78-8551-4343bacd0905', 'San Francisco Radonneurs', NULL, 0, '2023-09-30 18:05:32', '2023-09-30 18:05:32'),
('8dc3ca57-e513-447a-96fb-f1539c8db669', 'Triathlon Inverness', NULL, 0, '2023-09-30 18:06:27', '2023-09-30 18:06:27'),
('769c2c3d-4d32-49d7-a32f-540d24457b14', 'Scottish Borders Randonneurs', NULL, 0, '2023-09-30 18:10:24', '2023-09-30 18:10:24'),
('690b1862-d320-4c57-94e6-e0f634249a8d', 'SF Randoneurs', NULL, 0, '2023-09-30 18:16:53', '2023-09-30 18:16:53'),
('4f52601e-b8e2-4ea3-bfe4-d81ca8d5b02d', 'Centurion Cycling Club', NULL, 0, '2023-09-30 18:42:17', '2023-09-30 18:42:17'),
('72103394-684e-42d1-b9e9-4d2a5c2379d4', 'Centurion Cycling Club / Adobo Velo', NULL, 0, '2023-09-30 18:43:34', '2023-09-30 18:43:34'),
('ea1e7ab0-8ba2-406a-bd97-21411218e690', 'Randonneurs Malaysia', NULL, 0, '2023-09-30 22:21:00', '2023-09-30 22:21:00'),
('fc0bf2fa-6de3-4d6d-a60c-eb8bc85bf755', 'Randonneurs finland', NULL, 0, '2023-09-30 22:28:22', '2023-09-30 22:28:22'),
('1d86e498-ed98-47b7-b4e3-5f1346445e3d', 'none', NULL, 0, '2023-09-30 23:46:55', '2023-09-30 23:46:55'),
('d308ff3e-22b5-4992-9b85-6649a51858b2', 'Audax Ireland', NULL, 0, '2023-10-01 01:41:43', '2023-10-01 01:41:43'),
('8b717c31-2c50-4c73-b81a-36d33e73a351', '-', NULL, 0, '2023-10-01 02:09:03', '2023-10-01 02:09:03'),
('6d3e8a0c-9c01-4cec-9981-148c91e37a3f', 'Pacific Coast Highway Randonneurs', NULL, 0, '2023-10-01 03:11:53', '2023-10-01 03:11:53'),
('e3386735-213f-4e45-bd16-47038fc8aeb7', 'ARA München Oberbayern', NULL, 0, '2023-10-01 04:26:32', '2023-10-01 04:26:32'),
('e5ab284b-bfa7-4420-a433-886ca875b27a', 'Kingston wheelers', NULL, 0, '2023-10-01 08:41:04', '2023-10-01 08:41:04'),
('f390d9f9-98c2-49e6-9a79-3968e9a307c9', 'ARA Ruhrgebiet', NULL, 0, '2023-10-01 10:06:53', '2023-10-01 10:06:53'),
('5a033adc-3d04-4b73-a405-20d7b30e3603', 'Fredrikshov', NULL, 0, '2023-10-01 11:28:48', '2023-10-01 11:28:48'),
('f89a41ee-8906-405b-97da-7e050bcc1774', 'Randonneurs NL', NULL, 0, '2023-10-01 13:09:22', '2023-10-01 13:09:22'),
('09c23c33-d8aa-4029-9a69-6df52b4939a9', 'Audax Allemagne', NULL, 0, '2023-10-01 13:49:20', '2023-10-01 13:49:20'),
('c287a910-d7ce-4760-a67d-7fa84069bad4', 'Bodens cykelklubb \"CK Sävast\"', NULL, 0, '2023-10-01 14:16:18', '2023-10-01 14:16:18'),
('73035c1d-7803-497d-8088-c4b5f5db455a', 'Ck Sävast', NULL, 0, '2023-10-01 14:18:31', '2023-10-01 14:18:31'),
('70d64105-79c6-45d9-aca5-0052095b3f22', 'Not part of a club', NULL, 0, '2023-10-01 15:31:58', '2023-10-01 15:31:58'),
('b16763d9-bbf7-4665-bec4-0bd9a177af88', 'Audax Club Malaysia', NULL, 0, '2023-10-02 06:49:22', '2023-10-02 06:49:22'),
('642cdff2-d73f-4bf3-ba58-dca4f03aae27', 'Independent', '', 0, '2023-10-01 22:00:00', '0000-00-00 00:00:00'),
('a4072e31-59a1-4627-9e61-8f2fa645c04d', 'Randonneurs Nederland', '', 0, '2023-10-02 08:00:00', '2023-10-02 08:00:00'),
('f12a6f7f-b688-41ac-99b7-51ca0d87d476', 'PandR', NULL, 0, '2023-10-03 05:54:42', '2023-10-03 05:54:42'),
('78782717-db55-4f64-a536-09f22b7a2c85', 'Randonneurs NL  808056', NULL, 0, '2023-10-03 18:02:13', '2023-10-03 18:02:13'),
('91111f5e-ba35-4ac5-9742-ae6e3cfe8596', 'Ural-Marathon', NULL, 0, '2023-10-03 18:06:19', '2023-10-03 18:06:19'),
('4ef3a0fd-6b35-40cf-9bc2-ad73a749b03e', 'Ilmenauer Radsport Club e.V.', NULL, 0, '2023-10-04 16:38:33', '2023-10-04 16:38:33'),
('d513effb-7584-4a1e-b59f-2a2d68648fbe', 'N/A', NULL, 0, '2023-10-04 16:51:25', '2023-10-04 16:51:25'),
('a702a90b-41cd-460d-bfe7-5c3c16b15a22', 'Antaris', NULL, 0, '2023-10-05 04:42:43', '2023-10-05 04:42:43'),
('f1a2abc5-7a0a-4fa6-8d12-2cc20734c58f', 'Cykelintresset', NULL, 0, '2023-10-05 06:40:55', '2023-10-05 06:40:55'),
('8495189b-730a-4380-8f27-7cefee85c404', 'San Francisco Randonneurs', NULL, 0, '2023-10-05 14:48:33', '2023-10-05 14:48:33'),
('aee6355f-323f-4446-8819-ca2d71d88416', 'Antaris Team', NULL, 0, '2023-10-06 04:01:06', '2023-10-06 04:01:06'),
('8ae091f2-42a7-4b6f-b699-165491ea6e27', 'Audax Australia', NULL, 0, '2023-10-07 06:19:18', '2023-10-07 06:19:18'),
('e961f299-023c-464e-865f-23bc1d3cf9f9', 'Randonneurs Autonomes Aquitains', NULL, 0, '2023-10-07 16:24:10', '2023-10-07 16:24:10'),
('33fd0274-4953-4a9d-8fe1-7de92a206c54', 'RSC Rot-Gold Bremen', NULL, 0, '2023-10-08 06:21:09', '2023-10-08 06:21:09'),
('6aba169e-4926-4171-bdc6-8bf942dde251', 'Ulstein og omegn sykkelklubb', NULL, 0, '2023-10-08 14:26:11', '2023-10-08 14:26:11'),
('d7aaf1f3-46b5-4245-aca8-733d6500606d', 'Icehouse', NULL, 0, '2023-10-08 16:06:53', '2023-10-08 16:06:53'),
('b38339ae-45ce-48ab-85dd-b581fe93cb64', 'MOZAC CYCLO CLUB', NULL, 0, '2023-10-08 16:36:49', '2023-10-08 16:36:49'),
('51110c65-121a-45da-97b9-9c81b6befd00', 'Audax Club Franconia', NULL, 0, '2023-10-09 02:38:00', '2023-10-09 02:38:00'),
('61841917-5066-4660-936b-5a88d3f9595d', 'Hisingens CK', NULL, 0, '2023-10-09 03:32:11', '2023-10-09 03:32:11'),
('83f1eb39-418e-485b-95ad-18b83d06d0c3', 'Audax Franconia', NULL, 0, '2023-10-09 10:43:28', '2023-10-09 10:43:28'),
('52d79b6a-ed8a-4b16-bec4-b9d832c0b7f3', 'Audax Franconia / ARA Nordbayern Fränkische Alb', NULL, 0, '2023-10-09 10:44:49', '2023-10-09 10:44:49'),
('792701ba-3050-4855-9cf2-22cfd9287ad2', 'Randonneur Stockholm', NULL, 0, '2023-10-13 17:17:47', '2023-10-13 17:17:47'),
('e94eb470-d368-4670-8213-f8cf8a9835ba', 'Gironde', NULL, 0, '2023-10-15 06:10:57', '2023-10-15 06:10:57'),
('a619c9b5-50f1-4978-997d-2e811ec702b1', 'Uralmarathon', NULL, 0, '2023-10-15 09:57:12', '2023-10-15 09:57:12'),
('f9a14279-680d-49d4-a933-4f5b4c7c8651', 'Super-Brevet Berlin-Munich-Berlin', NULL, 0, '2023-10-16 13:40:31', '2023-10-16 13:40:31'),
('390aa933-2124-4c60-ab59-3e5f34cdea5e', 'Koiviston Isku', NULL, 0, '2023-10-17 07:16:22', '2023-10-17 07:16:22'),
('5662e6cb-cf45-46c5-b3cd-3bebc50df737', 'IF Åland', NULL, 0, '2023-10-17 07:29:43', '2023-10-17 07:29:43'),
('ff0b90a3-b954-4289-b935-f1314f974cb6', 'ARA Berlin Brandenburg', NULL, 0, '2023-10-20 02:15:48', '2023-10-20 02:15:48'),
('2b734ec6-651a-46c4-a454-1e6a39e3421e', 'Cykelslang CC', NULL, 0, '2023-10-23 07:09:11', '2023-10-23 07:09:11'),
('d049d4fd-7708-4f26-b3a8-7f7707131102', 'KBCK', NULL, 0, '2023-10-23 08:57:57', '2023-10-23 08:57:57'),
('de23695a-ba4f-4f2d-b09f-0d51c48272d2', 'ARA Nordbayern', NULL, 0, '2023-10-24 16:12:50', '2023-10-24 16:12:50'),
('a2fb853e-515c-49e0-923c-3c5e71ab27ba', 'Audax Cologne', NULL, 0, '2023-10-26 09:40:00', '2023-10-26 09:40:00'),
('b0946c96-c9e7-4b01-9bd2-250ed88236c8', 'Individuel Finland', NULL, 0, '2023-10-26 09:49:58', '2023-10-26 09:49:58'),
('971d1384-2bfb-4305-aae5-17f7b997cc0c', 'Audax Randonneurs Allemagne', NULL, 0, '2023-10-30 08:44:16', '2023-10-30 08:44:16'),
('fefc3fc8-ed6e-495d-9765-87d462ff493c', 'ARA Nordbayern Fränkische Alb', NULL, 0, '2023-10-30 13:53:25', '2023-10-30 13:53:25'),
('95adcccb-c006-41e3-b71b-0175a07755ba', 'Membre individuel gironde', NULL, 0, '2023-11-01 13:42:49', '2023-11-01 13:42:49'),
('64099b5d-1e1d-462d-a962-2ba3b5133532', 'Triathlon Günzburg', NULL, 0, '2023-11-03 07:42:56', '2023-11-03 07:42:56'),
('8469fd99-c22d-4b8b-a6ea-5f04b9943953', 'jCsIxbVKOmOFvwWeKams', NULL, 0, '2023-11-03 12:38:19', '2023-11-03 12:38:19'),
('6ec966d5-b9cb-49bd-96b3-eb266ab45984', 'Audax Poland', NULL, 0, '2023-11-10 11:56:08', '2023-11-10 11:56:08'),
('736425ee-28f3-48cd-b8f1-cf1cb45059b1', 'TV Aldekerk', NULL, 0, '2023-11-12 15:49:22', '2023-11-12 15:49:22'),
('a5670896-d044-4e45-a272-55816c0f4525', 'kNjzUcalWpnWkLNNSAPsQyEBxJz', NULL, 0, '2023-11-16 04:52:32', '2023-11-16 04:52:32'),
('b928b79e-1850-4ab1-ba89-5e04db440ac9', 'Audax Polska', NULL, 0, '2023-11-17 13:55:24', '2023-11-17 13:55:24'),
('b262a385-7c30-4a2c-8a8b-041b4beb8b68', 'Seattle Randonneurs', NULL, 0, '2023-11-18 15:38:07', '2023-11-18 15:38:07'),
('8e66444f-477e-4868-b1e7-78286312181c', 'Täby IS Skidor', NULL, 0, '2023-11-19 20:01:34', '2023-11-19 20:01:34'),
('572980f8-e759-4369-bafd-4133f539728c', 'IjtTywRfQxLbfILFQEHcMWIuaiXk', NULL, 0, '2023-11-22 00:50:40', '2023-11-22 00:50:40'),
('9b271c9f-e067-4811-89b1-393d29f24d32', 'LSR', NULL, 0, '2023-11-22 00:52:31', '2023-11-22 00:52:31'),
('b325ecf8-d85f-437b-a765-596cd410b0f1', 'Club Ciclista Riazor', NULL, 0, '2023-11-24 07:19:49', '2023-11-24 07:19:49'),
('acd03a86-d67e-453d-8c7a-9d465f66c98c', 'Leton Leisku', NULL, 0, '2023-11-25 07:26:21', '2023-11-25 07:26:21'),
('c1f9d7a0-6e21-4c41-83b0-d8c9cd3d7eb0', 'CK Distans', NULL, 0, '2023-11-28 08:45:26', '2023-11-28 08:45:26'),
('5e396e28-0b3f-4a7f-9f62-8855ec1f16a4', 'Team Rundt med de ben', NULL, 0, '2023-11-28 11:46:23', '2023-11-28 11:46:23'),
('6af6e5af-b5ce-4481-bb92-0becdb1740e8', 'FysioDanmark Hillerød', NULL, 0, '2023-11-28 13:17:16', '2023-11-28 13:17:16'),
('47d09a9a-fe07-462d-8dc9-626983a230a5', 'Ness Pedal Collective', NULL, 0, '2023-11-30 11:22:51', '2023-11-30 11:22:51'),
('3b360d0f-d400-479d-be88-fd8f5d418715', 'Individual', NULL, 0, '2023-12-01 14:51:07', '2023-12-01 14:51:07'),
('c4b17edf-2180-4920-abe4-afc843fee25b', 'KTK86', NULL, 0, '2023-12-02 08:47:20', '2023-12-02 08:47:20'),
('9716e876-3e31-43fc-9532-176a58f342da', 'Andover Wheelers', NULL, 0, '2023-12-02 09:46:44', '2023-12-02 09:46:44'),
('929365d5-342c-4ce9-a2e4-2d5f974a1b7e', 'czffDRtOWKOLtnJdKLJDY', NULL, 0, '2023-12-03 00:51:05', '2023-12-03 00:51:05'),
('b89e2e4c-ea83-45d6-82fd-e048a3a2cfb1', 'BjuMuwzTXLfseebtUzusWNtbq', NULL, 0, '2023-12-03 01:38:06', '2023-12-03 01:38:06'),
('99630602-6881-4f2d-9a7c-6160d2d0b9fa', 'Audax Danmark', NULL, 0, '2023-12-03 17:48:08', '2023-12-03 17:48:08'),
('731a546a-92b2-433f-b61a-a0bf1f6bbba8', 'Legacy Byrd', NULL, 0, '2023-12-04 11:43:26', '2023-12-04 11:43:26'),
('c070d27a-fb0f-4dc1-8c61-236b08a9e1e3', 'JHIsotTxQjKfuLJsOllylPr', NULL, 0, '2023-12-06 14:35:43', '2023-12-06 14:35:43'),
('39357002-fbee-428d-ba02-5f987bda21cf', 'ARA Hamburg', NULL, 0, '2023-12-08 17:15:31', '2023-12-08 17:15:31'),
('6982636d-c90c-4eea-b570-691688dc49c2', 'Beckenham Dads CC', NULL, 0, '2023-12-10 20:46:15', '2023-12-10 20:46:15'),
('1f5e888d-85d4-4b95-9e6f-1d521b37a068', 'Adrianna Mason', NULL, 0, '2023-12-11 08:59:19', '2023-12-11 08:59:19'),
('747f725a-cae3-4985-b8b6-a440586b2693', 'Idk', NULL, 0, '2023-12-12 06:48:22', '2023-12-12 06:48:22'),
('b561e9dc-44b6-4073-a302-dfea17bc2e83', 'Audax Nordbayern', NULL, 0, '2023-12-13 09:32:17', '2023-12-13 09:32:17'),
('382a4b39-ebc9-4970-98b1-191431e7e348', 'Randonneurs USA', NULL, 0, '2023-12-18 20:38:43', '2023-12-18 20:38:43'),
('9cef8680-6b1e-4d4e-89f6-02b6a841830f', 'dsfsdfds', NULL, 0, '2023-12-21 05:44:49', '2023-12-21 05:44:49'),
('246ce99d-f28b-4f1a-a414-3b747f46be52', 'Vihaan Schroeder', NULL, 0, '2023-12-22 12:31:28', '2023-12-22 12:31:28'),
('224c6840-7fba-4a7d-acc3-5164017438ee', 'Independiente', NULL, 0, '2023-12-25 08:49:48', '2023-12-25 08:49:48'),
('7da31624-aa92-45b5-abf8-52c51cbda618', 'Kaupin Kanuunat', NULL, 0, '2023-12-27 07:11:59', '2023-12-27 07:11:59'),
('f2f25ecb-9e22-4c5d-9016-97c64fb151bf', 'Kaupin Kanuunat', NULL, 0, '2023-12-27 07:11:59', '2023-12-27 07:11:59'),
('88b0c087-5087-4762-935d-4add34d6a412', 'Scottish Border Randonneurs', NULL, 0, '2023-12-27 13:04:26', '2023-12-27 13:04:26'),
('be77bb35-29d1-42fa-9c97-26fa5f5f3da2', 'Cardiff Ajax', NULL, 0, '2023-12-28 14:04:31', '2023-12-28 14:04:31'),
('fdf14b9f-3966-4904-be6d-37be4218a09d', 'C.C. RIPOLLET', NULL, 0, '2023-12-28 16:01:09', '2023-12-28 16:01:09'),
('080f455d-424e-4eab-b6bb-8c76ab843f05', 'INDEPENDENT ARMENIA AM585099', NULL, 0, '2023-12-29 04:39:20', '2023-12-29 04:39:20'),
('6c741b1d-679a-41f5-a929-7a0031e856a3', 'Randonneurs Andalucia', NULL, 0, '2023-12-29 21:25:45', '2023-12-29 21:25:45'),
('6f8a6305-cd4c-4c8a-a74a-aae823f9b793', 'MSD CHARTRES', NULL, 0, '2023-12-30 17:19:21', '2023-12-30 17:19:21'),
('ff6d7229-63b5-4e5a-8d2c-ed42a3b665a6', 'MSD CHARTRES CYCLO', NULL, 0, '2023-12-30 17:20:13', '2023-12-30 17:20:13'),
('b1bed20e-c70e-46f6-badd-896b45cc5e04', 'ewrwerwe', NULL, 0, '2024-01-06 20:30:52', '2024-01-06 20:30:52'),
('003ed974-3466-499d-a6bb-3ff71cbca764', 'DSFSDFS', NULL, 0, '2024-01-06 20:38:45', '2024-01-06 20:38:45'),
('baeeff6e-ac4d-4acf-bf20-2dffd7962ff3', 'IF Frøy', NULL, 0, '2024-01-21 09:08:19', '2024-01-21 09:08:19'),
('754d19f2-4d65-475e-828d-d33c26cd82ec', 'No Club', NULL, 0, '2024-02-13 16:50:29', '2024-02-13 16:50:29'),
('40b765a9-4e60-48f3-9445-f038a8628f60', 'CICLOCUBIN, C.D.', NULL, 0, '2024-02-15 20:11:12', '2024-02-15 20:11:12'),
('f75f569f-8350-4fe7-aaab-3230f81cfa9f', 'B3', NULL, 0, '2024-02-19 19:15:05', '2024-02-19 19:15:05');

CREATE TABLE `contactinformation` (
  `contactinformation_uid` char(36) NOT NULL,
  `tel` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `person_person_uid` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `countries` (
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `country_name_en` varchar(100) NOT NULL,
  `country_name_sv` varchar(100) NOT NULL,
  `country_code` varchar(15) NOT NULL,
  `flag_url_svg` varchar(200) NOT NULL,
  `flag_url_png` varchar(200) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `countries` (`country_id`, `country_name_en`, `country_name_sv`, `country_code`, `flag_url_svg`, `flag_url_png`, `created_at`, `updated_at`) VALUES
(1, 'French Polynesia', 'Franska Polynesien', 'PF', 'https://flagcdn.com/pf.svg', 'https://flagcdn.com/w320/pf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(2, 'Saint Martin', 'Saint-Martin', 'MF', 'https://flagcdn.com/mf.svg', 'https://flagcdn.com/w320/mf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(3, 'Venezuela', 'Venezuela', 'VE', 'https://flagcdn.com/ve.svg', 'https://flagcdn.com/w320/ve.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(4, 'Réunion', 'Réunion', 'RE', 'https://flagcdn.com/re.svg', 'https://flagcdn.com/w320/re.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(5, 'El Salvador', 'El Salvador', 'SV', 'https://flagcdn.com/sv.svg', 'https://flagcdn.com/w320/sv.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(6, 'Dominica', 'Dominica', 'DM', 'https://flagcdn.com/dm.svg', 'https://flagcdn.com/w320/dm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(7, 'Gibraltar', 'Gibraltar', 'GI', 'https://flagcdn.com/gi.svg', 'https://flagcdn.com/w320/gi.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(8, 'Kenya', 'Kenya', 'KE', 'https://flagcdn.com/ke.svg', 'https://flagcdn.com/w320/ke.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(9, 'Brazil', 'Brasilien', 'BR', 'https://flagcdn.com/br.svg', 'https://flagcdn.com/w320/br.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(10, 'Maldives', 'Maldiverna', 'MV', 'https://flagcdn.com/mv.svg', 'https://flagcdn.com/w320/mv.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(11, 'United States', 'USA', 'US', 'https://flagcdn.com/us.svg', 'https://flagcdn.com/w320/us.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(12, 'Cook Islands', 'Cooköarna', 'CK', 'https://flagcdn.com/ck.svg', 'https://flagcdn.com/w320/ck.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(13, 'Niue', 'Niue', 'NU', 'https://flagcdn.com/nu.svg', 'https://flagcdn.com/w320/nu.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(14, 'Seychelles', 'Seychellerna', 'SC', 'https://flagcdn.com/sc.svg', 'https://flagcdn.com/w320/sc.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(15, 'Central African Republic', 'Centralafrikanska republiken', 'CF', 'https://flagcdn.com/cf.svg', 'https://flagcdn.com/w320/cf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(16, 'Tokelau', 'Tokelauöarna', 'TK', 'https://flagcdn.com/tk.svg', 'https://flagcdn.com/w320/tk.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(17, 'Vanuatu', 'Vanuatu', 'VU', 'https://flagcdn.com/vu.svg', 'https://flagcdn.com/w320/vu.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(18, 'Gambia', 'Gambia', 'GM', 'https://flagcdn.com/gm.svg', 'https://flagcdn.com/w320/gm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(19, 'Guyana', 'Guyana', 'GY', 'https://flagcdn.com/gy.svg', 'https://flagcdn.com/w320/gy.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(20, 'Falkland Islands', 'Falklandsöarna', 'FK', 'https://flagcdn.com/fk.svg', 'https://flagcdn.com/w320/fk.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(21, 'Belgium', 'Belgien', 'BE', 'https://flagcdn.com/be.svg', 'https://flagcdn.com/w320/be.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(22, 'Western Sahara', 'Västsahara', 'EH', 'https://flagcdn.com/eh.svg', 'https://flagcdn.com/w320/eh.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(23, 'Turkey', 'Turkiet', 'TR', 'https://flagcdn.com/tr.svg', 'https://flagcdn.com/w320/tr.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(24, 'Saint Vincent and the Grenadines', 'Saint Vincent och Grenadinerna', 'VC', 'https://flagcdn.com/vc.svg', 'https://flagcdn.com/w320/vc.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(25, 'Pakistan', 'Pakistan', 'PK', 'https://flagcdn.com/pk.svg', 'https://flagcdn.com/w320/pk.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(26, 'Åland Islands', 'Åland', 'AX', 'https://flagcdn.com/ax.svg', 'https://flagcdn.com/w320/ax.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(27, 'Iran', 'Iran', 'IR', 'https://flagcdn.com/ir.svg', 'https://flagcdn.com/w320/ir.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(28, 'Indonesia', 'Indonesien', 'ID', 'https://flagcdn.com/id.svg', 'https://flagcdn.com/w320/id.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(29, 'New Zealand', 'Nya Zeeland', 'NZ', 'https://flagcdn.com/nz.svg', 'https://flagcdn.com/w320/nz.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(30, 'Afghanistan', 'Afghanistan', 'AF', 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Flag_of_the_Taliban.svg', 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Flag_of_the_Taliban.svg/320px-Flag_of_the_Taliban.svg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(31, 'Guam', 'Guam', 'GU', 'https://flagcdn.com/gu.svg', 'https://flagcdn.com/w320/gu.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(32, 'Albania', 'Albanien', 'AL', 'https://flagcdn.com/al.svg', 'https://flagcdn.com/w320/al.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(33, 'DR Congo', 'Kongo-Kinshasa', 'CD', 'https://flagcdn.com/cd.svg', 'https://flagcdn.com/w320/cd.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(34, 'Ivory Coast', 'Elfenbenskusten', 'CI', 'https://flagcdn.com/ci.svg', 'https://flagcdn.com/w320/ci.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(35, 'Sudan', 'Sudan', 'SD', 'https://flagcdn.com/sd.svg', 'https://flagcdn.com/w320/sd.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(36, 'Timor-Leste', 'Östtimor', 'TL', 'https://flagcdn.com/tl.svg', 'https://flagcdn.com/w320/tl.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(37, 'Luxembourg', 'Luxemburg', 'LU', 'https://flagcdn.com/lu.svg', 'https://flagcdn.com/w320/lu.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(38, 'Saudi Arabia', 'Saudiarabien', 'Saudi', 'https://flagcdn.com/sa.svg', 'https://flagcdn.com/w320/sa.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(39, 'Cambodia', 'Kambodja', 'KH', 'https://flagcdn.com/kh.svg', 'https://flagcdn.com/w320/kh.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(40, 'Nepal', 'Nepal', 'NP', 'https://flagcdn.com/np.svg', 'https://flagcdn.com/w320/np.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(41, 'French Guiana', 'Franska Guyana', 'GF', 'https://flagcdn.com/gf.svg', 'https://flagcdn.com/w320/gf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(42, 'Malaysia', 'Malaysia', 'MY', 'https://flagcdn.com/my.svg', 'https://flagcdn.com/w320/my.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(43, 'Rwanda', 'Rwanda', 'RW', 'https://flagcdn.com/rw.svg', 'https://flagcdn.com/w320/rw.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(44, 'Thailand', 'Thailand', 'TH', 'https://flagcdn.com/th.svg', 'https://flagcdn.com/w320/th.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(45, 'Antarctica', 'Antarktis', 'AQ', 'https://flagcdn.com/aq.svg', 'https://flagcdn.com/w320/aq.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(46, 'Jordan', 'Jordanien', 'JO', 'https://flagcdn.com/jo.svg', 'https://flagcdn.com/w320/jo.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(47, 'Switzerland', 'Schweiz', 'CH', 'https://flagcdn.com/ch.svg', 'https://flagcdn.com/w320/ch.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(48, 'Comoros', 'Komorerna', 'KM', 'https://flagcdn.com/km.svg', 'https://flagcdn.com/w320/km.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(49, 'Kosovo', 'Kosovo', 'XK', 'https://flagcdn.com/xk.svg', 'https://flagcdn.com/w320/xk.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(50, 'Isle of Man', 'Isle of Man', 'IM', 'https://flagcdn.com/im.svg', 'https://flagcdn.com/w320/im.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(51, 'Montenegro', 'Montenegro', 'ME', 'https://flagcdn.com/me.svg', 'https://flagcdn.com/w320/me.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(52, 'Hong Kong', 'Hongkong', 'HK', 'https://flagcdn.com/hk.svg', 'https://flagcdn.com/w320/hk.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(53, 'Jersey', 'Jersey', 'JE', 'https://flagcdn.com/je.svg', 'https://flagcdn.com/w320/je.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(54, 'Tajikistan', 'Tadzjikistan', 'TJ', 'https://flagcdn.com/tj.svg', 'https://flagcdn.com/w320/tj.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(55, 'Bulgaria', 'Bulgarien', 'BG', 'https://flagcdn.com/bg.svg', 'https://flagcdn.com/w320/bg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(56, 'Egypt', 'Egypten', 'EG', 'https://flagcdn.com/eg.svg', 'https://flagcdn.com/w320/eg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(57, 'Malawi', 'Malawi', 'MW', 'https://flagcdn.com/mw.svg', 'https://flagcdn.com/w320/mw.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(58, 'Cape Verde', 'Kap Verde', 'CV', 'https://flagcdn.com/cv.svg', 'https://flagcdn.com/w320/cv.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(59, 'Benin', 'Benin', 'BJ', 'https://flagcdn.com/bj.svg', 'https://flagcdn.com/w320/bj.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(60, 'Morocco', 'Marocko', 'MA', 'https://flagcdn.com/ma.svg', 'https://flagcdn.com/w320/ma.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(61, 'Ireland', 'Irland', 'IE', 'https://flagcdn.com/ie.svg', 'https://flagcdn.com/w320/ie.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(62, 'Moldova', 'Moldavien', 'MD', 'https://flagcdn.com/md.svg', 'https://flagcdn.com/w320/md.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(63, 'Denmark', 'Danmark', 'DK', 'https://flagcdn.com/dk.svg', 'https://flagcdn.com/w320/dk.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(64, 'Turkmenistan', 'Turkmenistan', 'TM', 'https://flagcdn.com/tm.svg', 'https://flagcdn.com/w320/tm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(65, 'Micronesia', 'Mikronesiska federationen', 'FM', 'https://flagcdn.com/fm.svg', 'https://flagcdn.com/w320/fm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(66, 'Monaco', 'Monaco', 'MC', 'https://flagcdn.com/mc.svg', 'https://flagcdn.com/w320/mc.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(67, 'Barbados', 'Barbados', 'BB', 'https://flagcdn.com/bb.svg', 'https://flagcdn.com/w320/bb.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(68, 'Algeria', 'Algeriet', 'DZ', 'https://flagcdn.com/dz.svg', 'https://flagcdn.com/w320/dz.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(69, 'French Southern and Antarctic Lands', 'Franska södra territorierna', 'TF', 'https://flagcdn.com/tf.svg', 'https://flagcdn.com/w320/tf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(70, 'Eritrea', 'Eritrea', 'ER', 'https://flagcdn.com/er.svg', 'https://flagcdn.com/w320/er.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(71, 'Lesotho', 'Lesotho', 'LS', 'https://flagcdn.com/ls.svg', 'https://flagcdn.com/w320/ls.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(72, 'Tanzania', 'Tanzania', 'TZ', 'https://flagcdn.com/tz.svg', 'https://flagcdn.com/w320/tz.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(73, 'Mali', 'Mali', 'ML', 'https://flagcdn.com/ml.svg', 'https://flagcdn.com/w320/ml.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(74, 'Niger', 'Niger', 'NE', 'https://flagcdn.com/ne.svg', 'https://flagcdn.com/w320/ne.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(75, 'Andorra', 'Andorra', 'AD', 'https://flagcdn.com/ad.svg', 'https://flagcdn.com/w320/ad.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(76, 'United Kingdom', 'Storbritannien', 'GB', 'https://flagcdn.com/gb.svg', 'https://flagcdn.com/w320/gb.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(77, 'Germany', 'Tyskland', 'DE', 'https://flagcdn.com/de.svg', 'https://flagcdn.com/w320/de.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(78, 'United States Virgin Islands', 'Amerikanska Jungfruöarna', 'VI', 'https://flagcdn.com/vi.svg', 'https://flagcdn.com/w320/vi.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(79, 'Somalia', 'Somalia', 'SO', 'https://flagcdn.com/so.svg', 'https://flagcdn.com/w320/so.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(80, 'Sint Maarten', 'Sint Maarten', 'SX', 'https://flagcdn.com/sx.svg', 'https://flagcdn.com/w320/sx.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(81, 'Cameroon', 'Kamerun', 'CM', 'https://flagcdn.com/cm.svg', 'https://flagcdn.com/w320/cm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(82, 'Dominican Republic', 'Dominikanska republiken', 'DO', 'https://flagcdn.com/do.svg', 'https://flagcdn.com/w320/do.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(83, 'Guinea', 'Guinea', 'GN', 'https://flagcdn.com/gn.svg', 'https://flagcdn.com/w320/gn.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(84, 'Namibia', 'Namibia', 'NA', 'https://flagcdn.com/na.svg', 'https://flagcdn.com/w320/na.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(85, 'Montserrat', 'Montserrat', 'MS', 'https://flagcdn.com/ms.svg', 'https://flagcdn.com/w320/ms.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(86, 'South Georgia', 'Sydgeorgien', 'GS', 'https://flagcdn.com/gs.svg', 'https://flagcdn.com/w320/gs.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(87, 'Senegal', 'Senegal', 'SN', 'https://flagcdn.com/sn.svg', 'https://flagcdn.com/w320/sn.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(88, 'Bouvet Island', 'Bouvetön', 'BV', 'https://flagcdn.com/bv.svg', 'https://flagcdn.com/w320/bv.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(89, 'Solomon Islands', 'Salomonöarna', 'SB', 'https://flagcdn.com/sb.svg', 'https://flagcdn.com/w320/sb.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(90, 'France', 'Frankrike', 'FR', 'https://flagcdn.com/fr.svg', 'https://flagcdn.com/w320/fr.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(91, 'Saint Helena, Ascension and Tristan da Cunha', 'Sankta Helena', 'Saint Helena', 'https://flagcdn.com/sh.svg', 'https://flagcdn.com/w320/sh.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(92, 'Macau', 'Macao', 'MO', 'https://flagcdn.com/mo.svg', 'https://flagcdn.com/w320/mo.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(93, 'Argentina', 'Argentina', 'AR', 'https://flagcdn.com/ar.svg', 'https://flagcdn.com/w320/ar.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(94, 'Bosnia and Herzegovina', 'Bosnien och Hercegovina', 'BA', 'https://flagcdn.com/ba.svg', 'https://flagcdn.com/w320/ba.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(95, 'Anguilla', 'Anguilla', 'AI', 'https://flagcdn.com/ai.svg', 'https://flagcdn.com/w320/ai.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(96, 'Guernsey', 'Guernsey', 'GG', 'https://flagcdn.com/gg.svg', 'https://flagcdn.com/w320/gg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(97, 'Djibouti', 'Djibouti', 'DJ', 'https://flagcdn.com/dj.svg', 'https://flagcdn.com/w320/dj.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(98, 'Saint Kitts and Nevis', 'Saint Kitts och Nevis', 'KN', 'https://flagcdn.com/kn.svg', 'https://flagcdn.com/w320/kn.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(99, 'Syria', 'Syrien', 'SY', 'https://flagcdn.com/sy.svg', 'https://flagcdn.com/w320/sy.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(100, 'Puerto Rico', 'Puerto Rico', 'PR', 'https://flagcdn.com/pr.svg', 'https://flagcdn.com/w320/pr.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(101, 'Peru', 'Peru', 'PE', 'https://flagcdn.com/pe.svg', 'https://flagcdn.com/w320/pe.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(102, 'San Marino', 'San Marino', 'SM', 'https://flagcdn.com/sm.svg', 'https://flagcdn.com/w320/sm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(103, 'Australia', 'Australien', 'AU', 'https://flagcdn.com/au.svg', 'https://flagcdn.com/w320/au.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(104, 'New Caledonia', 'Nya Kaledonien', 'NC', 'https://flagcdn.com/nc.svg', 'https://flagcdn.com/w320/nc.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(105, 'Jamaica', 'Jamaica', 'JM', 'https://flagcdn.com/jm.svg', 'https://flagcdn.com/w320/jm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(106, 'Kazakhstan', 'Kazakstan', 'KZ', 'https://flagcdn.com/kz.svg', 'https://flagcdn.com/w320/kz.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(107, 'Sierra Leone', 'Sierra Leone', 'SL', 'https://flagcdn.com/sl.svg', 'https://flagcdn.com/w320/sl.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(108, 'Palau', 'Palau', 'PW', 'https://flagcdn.com/pw.svg', 'https://flagcdn.com/w320/pw.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(109, 'South Korea', 'Sydkorea', 'KR', 'https://flagcdn.com/kr.svg', 'https://flagcdn.com/w320/kr.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(110, 'Saint Pierre and Miquelon', 'Saint-Pierre och Miquelon', 'PM', 'https://flagcdn.com/pm.svg', 'https://flagcdn.com/w320/pm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(111, 'Belize', 'Belize', 'BZ', 'https://flagcdn.com/bz.svg', 'https://flagcdn.com/w320/bz.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(112, 'Papua New Guinea', 'Papua Nya Guinea', 'PG', 'https://flagcdn.com/pg.svg', 'https://flagcdn.com/w320/pg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(113, 'Iceland', 'Island', 'IS', 'https://flagcdn.com/is.svg', 'https://flagcdn.com/w320/is.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(114, 'American Samoa', 'Amerikanska Samoa', 'AS', 'https://flagcdn.com/as.svg', 'https://flagcdn.com/w320/as.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(115, 'Burkina Faso', 'Burkina Faso', 'BF', 'https://flagcdn.com/bf.svg', 'https://flagcdn.com/w320/bf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(116, 'Portugal', 'Portugal', 'PT', 'https://flagcdn.com/pt.svg', 'https://flagcdn.com/w320/pt.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(117, 'Taiwan', 'Taiwan', 'TW', 'https://flagcdn.com/tw.svg', 'https://flagcdn.com/w320/tw.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(118, 'Japan', 'Japan', 'JP', 'https://flagcdn.com/jp.svg', 'https://flagcdn.com/w320/jp.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(119, 'China', 'Kina', 'CN', 'https://flagcdn.com/cn.svg', 'https://flagcdn.com/w320/cn.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(120, 'Lebanon', 'Libanon', 'LB', 'https://flagcdn.com/lb.svg', 'https://flagcdn.com/w320/lb.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(121, 'Sri Lanka', 'Sri Lanka', 'LK', 'https://flagcdn.com/lk.svg', 'https://flagcdn.com/w320/lk.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(122, 'Guatemala', 'Guatemala', 'GT', 'https://flagcdn.com/gt.svg', 'https://flagcdn.com/w320/gt.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(123, 'Serbia', 'Serbien', 'RS', 'https://flagcdn.com/rs.svg', 'https://flagcdn.com/w320/rs.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(124, 'Madagascar', 'Madagaskar', 'MG', 'https://flagcdn.com/mg.svg', 'https://flagcdn.com/w320/mg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(125, 'Eswatini', 'Swaziland', 'SZ', 'https://flagcdn.com/sz.svg', 'https://flagcdn.com/w320/sz.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(126, 'Romania', 'Rumänien', 'RO', 'https://flagcdn.com/ro.svg', 'https://flagcdn.com/w320/ro.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(127, 'Antigua and Barbuda', 'Antigua och Barbuda', 'AG', 'https://flagcdn.com/ag.svg', 'https://flagcdn.com/w320/ag.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(128, 'Curaçao', 'Curaçao', 'CW', 'https://flagcdn.com/cw.svg', 'https://flagcdn.com/w320/cw.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(129, 'Zambia', 'Zambia', 'ZM', 'https://flagcdn.com/zm.svg', 'https://flagcdn.com/w320/zm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(130, 'Zimbabwe', 'Zimbabwe', 'ZW', 'https://flagcdn.com/zw.svg', 'https://flagcdn.com/w320/zw.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(131, 'Tunisia', 'Tunisien', 'TN', 'https://flagcdn.com/tn.svg', 'https://flagcdn.com/w320/tn.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(132, 'United Arab Emirates', 'Förenade Arabemiraten', 'AE', 'https://flagcdn.com/ae.svg', 'https://flagcdn.com/w320/ae.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(133, 'Mongolia', 'Mongoliet', 'MN', 'https://flagcdn.com/mn.svg', 'https://flagcdn.com/w320/mn.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(134, 'Norway', 'Norge', 'NO', 'https://flagcdn.com/no.svg', 'https://flagcdn.com/w320/no.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(135, 'Greenland', 'Grönland', 'GL', 'https://flagcdn.com/gl.svg', 'https://flagcdn.com/w320/gl.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(136, 'Uruguay', 'Uruguay', 'UY', 'https://flagcdn.com/uy.svg', 'https://flagcdn.com/w320/uy.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(137, 'Bahamas', 'Bahamas', 'BS', 'https://flagcdn.com/bs.svg', 'https://flagcdn.com/w320/bs.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(138, 'Russia', 'Ryssland', 'RU', 'https://flagcdn.com/ru.svg', 'https://flagcdn.com/w320/ru.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(139, 'British Virgin Islands', 'Brittiska Jungfruöarna', 'VG', 'https://flagcdn.com/vg.svg', 'https://flagcdn.com/w320/vg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(140, 'Wallis and Futuna', 'Wallis- och Futunaöarna', 'WF', 'https://flagcdn.com/wf.svg', 'https://flagcdn.com/w320/wf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(141, 'Chad', 'Tchad', 'TD', 'https://flagcdn.com/td.svg', 'https://flagcdn.com/w320/td.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(142, 'Saint Lucia', 'Saint Lucia', 'LC', 'https://flagcdn.com/lc.svg', 'https://flagcdn.com/w320/lc.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(143, 'Yemen', 'Jemen', 'YE', 'https://flagcdn.com/ye.svg', 'https://flagcdn.com/w320/ye.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(144, 'United States Minor Outlying Islands', 'Förenta staternas mindre öar i Oceanien och Västindien', 'UM', 'https://flagcdn.com/um.svg', 'https://flagcdn.com/w320/um.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(145, 'Sweden', 'Sverige', 'SE', 'https://flagcdn.com/se.svg', 'https://flagcdn.com/w320/se.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(146, 'Svalbard and Jan Mayen', 'Svalbard och Jan Mayen', 'SJ', 'https://flagcdn.com/sj.svg', 'https://flagcdn.com/w320/sj.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(147, 'Laos', 'Laos', 'LA', 'https://flagcdn.com/la.svg', 'https://flagcdn.com/w320/la.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(148, 'Latvia', 'Lettland', 'LV', 'https://flagcdn.com/lv.svg', 'https://flagcdn.com/w320/lv.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(149, 'Colombia', 'Colombia', 'CO', 'https://flagcdn.com/co.svg', 'https://flagcdn.com/w320/co.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(150, 'Grenada', 'Grenada', 'GD', 'https://flagcdn.com/gd.svg', 'https://flagcdn.com/w320/gd.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(151, 'Saint Barthélemy', 'Saint-Barthélemy', 'BL', 'https://flagcdn.com/bl.svg', 'https://flagcdn.com/w320/bl.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(152, 'Canada', 'Kanada', 'CA', 'https://flagcdn.com/ca.svg', 'https://flagcdn.com/w320/ca.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(153, 'Heard Island and McDonald Islands', 'Heard- och McDonaldöarna', 'HM', 'https://flagcdn.com/hm.svg', 'https://flagcdn.com/w320/hm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(154, 'India', 'Indien', 'IN', 'https://flagcdn.com/in.svg', 'https://flagcdn.com/w320/in.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(155, 'Guinea-Bissau', 'Guinea-Bissau', 'GW', 'https://flagcdn.com/gw.svg', 'https://flagcdn.com/w320/gw.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(156, 'North Macedonia', 'Nordmakedonien', 'MK', 'https://flagcdn.com/mk.svg', 'https://flagcdn.com/w320/mk.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(157, 'Paraguay', 'Paraguay', 'PY', 'https://flagcdn.com/py.svg', 'https://flagcdn.com/w320/py.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(158, 'Croatia', 'Kroatien', 'HR', 'https://flagcdn.com/hr.svg', 'https://flagcdn.com/w320/hr.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(159, 'Costa Rica', 'Costa Rica', 'CR', 'https://flagcdn.com/cr.svg', 'https://flagcdn.com/w320/cr.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(160, 'Uganda', 'Uganda', 'UG', 'https://flagcdn.com/ug.svg', 'https://flagcdn.com/w320/ug.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(161, 'Caribbean Netherlands', 'Karibiska Nederländerna', 'BES islands', 'https://flagcdn.com/bq.svg', 'https://flagcdn.com/w320/bq.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(162, 'Bolivia', 'Bolivia', 'BO', 'https://flagcdn.com/bo.svg', 'https://flagcdn.com/w320/bo.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(163, 'Togo', 'Togo', 'TG', 'https://flagcdn.com/tg.svg', 'https://flagcdn.com/w320/tg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(164, 'Mayotte', 'Mayotte', 'YT', 'https://flagcdn.com/yt.svg', 'https://flagcdn.com/w320/yt.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(165, 'Marshall Islands', 'Marshallöarna', 'MH', 'https://flagcdn.com/mh.svg', 'https://flagcdn.com/w320/mh.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(166, 'North Korea', 'Nordkorea', 'KP', 'https://flagcdn.com/kp.svg', 'https://flagcdn.com/w320/kp.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(167, 'Netherlands', 'Nederländerna', 'NL', 'https://flagcdn.com/nl.svg', 'https://flagcdn.com/w320/nl.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(168, 'British Indian Ocean Territory', 'Brittiska territoriet i Indiska Oceanen', 'IO', 'https://flagcdn.com/io.svg', 'https://flagcdn.com/w320/io.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(169, 'Malta', 'Malta', 'MT', 'https://flagcdn.com/mt.svg', 'https://flagcdn.com/w320/mt.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(170, 'Mauritius', 'Mauritius', 'MU', 'https://flagcdn.com/mu.svg', 'https://flagcdn.com/w320/mu.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(171, 'Norfolk Island', 'Norfolkön', 'NF', 'https://flagcdn.com/nf.svg', 'https://flagcdn.com/w320/nf.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(172, 'Honduras', 'Honduras', 'HN', 'https://flagcdn.com/hn.svg', 'https://flagcdn.com/w320/hn.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(173, 'Spain', 'Spanien', 'ES', 'https://flagcdn.com/es.svg', 'https://flagcdn.com/w320/es.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(174, 'Estonia', 'Estland', 'EE', 'https://flagcdn.com/ee.svg', 'https://flagcdn.com/w320/ee.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(175, 'Kyrgyzstan', 'Kirgizistan', 'KG', 'https://flagcdn.com/kg.svg', 'https://flagcdn.com/w320/kg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(176, 'Chile', 'Chile', 'CL', 'https://flagcdn.com/cl.svg', 'https://flagcdn.com/w320/cl.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(177, 'Bermuda', 'Bermuda', 'BM', 'https://flagcdn.com/bm.svg', 'https://flagcdn.com/w320/bm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(178, 'Equatorial Guinea', 'Ekvatorialguinea', 'GQ', 'https://flagcdn.com/gq.svg', 'https://flagcdn.com/w320/gq.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(179, 'Liberia', 'Liberia', 'LR', 'https://flagcdn.com/lr.svg', 'https://flagcdn.com/w320/lr.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(180, 'Pitcairn Islands', 'Pitcairnöarna', 'PN', 'https://flagcdn.com/pn.svg', 'https://flagcdn.com/w320/pn.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(181, 'Libya', 'Libyen', 'LY', 'https://flagcdn.com/ly.svg', 'https://flagcdn.com/w320/ly.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(182, 'Liechtenstein', 'Liechtenstein', 'LI', 'https://flagcdn.com/li.svg', 'https://flagcdn.com/w320/li.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(183, 'Vatican City', 'Vatikanstaten', 'VA', 'https://flagcdn.com/va.svg', 'https://flagcdn.com/w320/va.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(184, 'Christmas Island', 'Julön', 'CX', 'https://flagcdn.com/cx.svg', 'https://flagcdn.com/w320/cx.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(185, 'Oman', 'Oman', 'OM', 'https://flagcdn.com/om.svg', 'https://flagcdn.com/w320/om.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(186, 'Philippines', 'Filippinerna', 'PH', 'https://flagcdn.com/ph.svg', 'https://flagcdn.com/w320/ph.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(187, 'Poland', 'Polen', 'PL', 'https://flagcdn.com/pl.svg', 'https://flagcdn.com/w320/pl.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(188, 'Faroe Islands', 'Färöarna', 'FO', 'https://flagcdn.com/fo.svg', 'https://flagcdn.com/w320/fo.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(189, 'Bahrain', 'Bahrain', 'BH', 'https://flagcdn.com/bh.svg', 'https://flagcdn.com/w320/bh.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(190, 'Belarus', 'Belarus', 'BY', 'https://flagcdn.com/by.svg', 'https://flagcdn.com/w320/by.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(191, 'Slovenia', 'Slovenien', 'SI', 'https://flagcdn.com/si.svg', 'https://flagcdn.com/w320/si.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(192, 'Guadeloupe', 'Guadeloupe', 'GP', 'https://flagcdn.com/gp.svg', 'https://flagcdn.com/w320/gp.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(193, 'Qatar', 'Qatar', 'QA', 'https://flagcdn.com/qa.svg', 'https://flagcdn.com/w320/qa.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(194, 'Vietnam', 'Vietnam', 'VN', 'https://flagcdn.com/vn.svg', 'https://flagcdn.com/w320/vn.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(195, 'Mauritania', 'Mauretanien', 'MR', 'https://flagcdn.com/mr.svg', 'https://flagcdn.com/w320/mr.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(196, 'Singapore', 'Singapore', 'SG', 'https://flagcdn.com/sg.svg', 'https://flagcdn.com/w320/sg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(197, 'Georgia', 'Georgien', 'GE', 'https://flagcdn.com/ge.svg', 'https://flagcdn.com/w320/ge.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(198, 'Burundi', 'Burundi', 'BI', 'https://flagcdn.com/bi.svg', 'https://flagcdn.com/w320/bi.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(199, 'Nauru', 'Nauru', 'NR', 'https://flagcdn.com/nr.svg', 'https://flagcdn.com/w320/nr.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(200, 'South Sudan', 'Sydsudan', 'SS', 'https://flagcdn.com/ss.svg', 'https://flagcdn.com/w320/ss.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(201, 'Samoa', 'Samoa', 'WS', 'https://flagcdn.com/ws.svg', 'https://flagcdn.com/w320/ws.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(202, 'Cocos (Keeling) Islands', 'Kokosöarna', 'CC', 'https://flagcdn.com/cc.svg', 'https://flagcdn.com/w320/cc.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(203, 'Republic of the Congo', 'Kongo-Brazzaville', 'CG', 'https://flagcdn.com/cg.svg', 'https://flagcdn.com/w320/cg.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(204, 'Cyprus', 'Cypern', 'CY', 'https://flagcdn.com/cy.svg', 'https://flagcdn.com/w320/cy.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(205, 'Kuwait', 'Kuwait', 'KW', 'https://flagcdn.com/kw.svg', 'https://flagcdn.com/w320/kw.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(206, 'Trinidad and Tobago', 'Trinidad och Tobago', 'TT', 'https://flagcdn.com/tt.svg', 'https://flagcdn.com/w320/tt.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(207, 'Tuvalu', 'Tuvalu', 'TV', 'https://flagcdn.com/tv.svg', 'https://flagcdn.com/w320/tv.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(208, 'Angola', 'Angola', 'AO', 'https://flagcdn.com/ao.svg', 'https://flagcdn.com/w320/ao.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(209, 'Tonga', 'Tonga', 'TO', 'https://flagcdn.com/to.svg', 'https://flagcdn.com/w320/to.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(210, 'Greece', 'Grekland', 'GR', 'https://flagcdn.com/gr.svg', 'https://flagcdn.com/w320/gr.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(211, 'Mozambique', 'Moçambique', 'MZ', 'https://flagcdn.com/mz.svg', 'https://flagcdn.com/w320/mz.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(212, 'Myanmar', 'Myanmar', 'MM', 'https://flagcdn.com/mm.svg', 'https://flagcdn.com/w320/mm.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(213, 'Austria', 'Österrike', 'AT', 'https://flagcdn.com/at.svg', 'https://flagcdn.com/w320/at.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(214, 'Ethiopia', 'Etiopien', 'ET', 'https://flagcdn.com/et.svg', 'https://flagcdn.com/w320/et.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(215, 'Martinique', 'Martinique', 'MQ', 'https://flagcdn.com/mq.svg', 'https://flagcdn.com/w320/mq.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(216, 'Azerbaijan', 'Azerbajdzjan', 'AZ', 'https://flagcdn.com/az.svg', 'https://flagcdn.com/w320/az.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(217, 'Uzbekistan', 'Uzbekistan', 'UZ', 'https://flagcdn.com/uz.svg', 'https://flagcdn.com/w320/uz.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(218, 'Bangladesh', 'Bangladesh', 'BD', 'https://flagcdn.com/bd.svg', 'https://flagcdn.com/w320/bd.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(219, 'Armenia', 'Armenien', 'AM', 'https://flagcdn.com/am.svg', 'https://flagcdn.com/w320/am.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(220, 'Nigeria', 'Nigeria', 'NG', 'https://flagcdn.com/ng.svg', 'https://flagcdn.com/w320/ng.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(221, 'South Africa', 'Sydafrika', 'ZA', 'https://flagcdn.com/za.svg', 'https://flagcdn.com/w320/za.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(222, 'Brunei', 'Brunei', 'BN', 'https://flagcdn.com/bn.svg', 'https://flagcdn.com/w320/bn.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(223, 'Italy', 'Italien', 'IT', 'https://flagcdn.com/it.svg', 'https://flagcdn.com/w320/it.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(224, 'Finland', 'Finland', 'FI', 'https://flagcdn.com/fi.svg', 'https://flagcdn.com/w320/fi.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(225, 'Israel', 'Israel', 'IL', 'https://flagcdn.com/il.svg', 'https://flagcdn.com/w320/il.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(226, 'Aruba', 'Aruba', 'AW', 'https://flagcdn.com/aw.svg', 'https://flagcdn.com/w320/aw.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(227, 'Nicaragua', 'Nicaragua', 'NI', 'https://flagcdn.com/ni.svg', 'https://flagcdn.com/w320/ni.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(228, 'Haiti', 'Haiti', 'HT', 'https://flagcdn.com/ht.svg', 'https://flagcdn.com/w320/ht.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(229, 'Kiribati', 'Kiribati', 'KI', 'https://flagcdn.com/ki.svg', 'https://flagcdn.com/w320/ki.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(230, 'Turks and Caicos Islands', 'Turks- och Caicosöarna', 'TC', 'https://flagcdn.com/tc.svg', 'https://flagcdn.com/w320/tc.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(231, 'Cayman Islands', 'Caymanöarna', 'KY', 'https://flagcdn.com/ky.svg', 'https://flagcdn.com/w320/ky.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(232, 'Ukraine', 'Ukraina', 'UA', 'https://flagcdn.com/ua.svg', 'https://flagcdn.com/w320/ua.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(233, 'Mexico', 'Mexiko', 'MX', 'https://flagcdn.com/mx.svg', 'https://flagcdn.com/w320/mx.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(234, 'Palestine', 'Palestina', 'PS', 'https://flagcdn.com/ps.svg', 'https://flagcdn.com/w320/ps.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(235, 'Fiji', 'Fiji', 'FJ', 'https://flagcdn.com/fj.svg', 'https://flagcdn.com/w320/fj.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(236, 'Slovakia', 'Slovakien', 'SK', 'https://flagcdn.com/sk.svg', 'https://flagcdn.com/w320/sk.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(237, 'Ghana', 'Ghana', 'GH', 'https://flagcdn.com/gh.svg', 'https://flagcdn.com/w320/gh.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(238, 'Suriname', 'Surinam', 'SR', 'https://flagcdn.com/sr.svg', 'https://flagcdn.com/w320/sr.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(239, 'Cuba', 'Kuba', 'CU', 'https://flagcdn.com/cu.svg', 'https://flagcdn.com/w320/cu.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(240, 'Bhutan', 'Bhutan', 'BT', 'https://flagcdn.com/bt.svg', 'https://flagcdn.com/w320/bt.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(241, 'Hungary', 'Ungern', 'HU', 'https://flagcdn.com/hu.svg', 'https://flagcdn.com/w320/hu.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(242, 'São Tomé and Príncipe', 'São Tomé och Príncipe', 'ST', 'https://flagcdn.com/st.svg', 'https://flagcdn.com/w320/st.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(243, 'Iraq', 'Irak', 'IQ', 'https://flagcdn.com/iq.svg', 'https://flagcdn.com/w320/iq.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(244, 'Czechia', 'Tjeckien', 'CZ', 'https://flagcdn.com/cz.svg', 'https://flagcdn.com/w320/cz.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(245, 'Lithuania', 'Litauen', 'LT', 'https://flagcdn.com/lt.svg', 'https://flagcdn.com/w320/lt.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(246, 'Northern Mariana Islands', 'Nordmarianerna', 'MP', 'https://flagcdn.com/mp.svg', 'https://flagcdn.com/w320/mp.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(247, 'Botswana', 'Botswana', 'BW', 'https://flagcdn.com/bw.svg', 'https://flagcdn.com/w320/bw.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(248, 'Panama', 'Panama', 'PA', 'https://flagcdn.com/pa.svg', 'https://flagcdn.com/w320/pa.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(249, 'Gabon', 'Gabon', 'GA', 'https://flagcdn.com/ga.svg', 'https://flagcdn.com/w320/ga.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45'),
(250, 'Ecuador', 'Ecuador', 'EC', 'https://flagcdn.com/ec.svg', 'https://flagcdn.com/w320/ec.png', '2023-09-30 08:22:34', '2024-01-06 19:23:45');

CREATE TABLE `discount_codes` (
  `discountcode_uid` char(36) NOT NULL,
  `code` varchar(20) NOT NULL,
  `discount_amount` decimal(13,4) NOT NULL,
  `expirered` tinyint(1) NOT NULL DEFAULT 0,
  `unlimited` tinyint(1) NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `usage_limit` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `eventconfigurations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `max_registrations` bigint(20) NOT NULL,
  `registration_opens` datetime NOT NULL,
  `registration_closes` datetime NOT NULL,
  `resarvation_on_event` tinyint(1) NOT NULL DEFAULT 0,
  `eventconfiguration_type` varchar(255) NOT NULL,
  `eventconfiguration_id` char(36) NOT NULL COMMENT '(DC2Type:guid)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `eventconfigurations` (`id`, `max_registrations`, `registration_opens`, `registration_closes`, `resarvation_on_event`, `eventconfiguration_type`, `eventconfiguration_id`, `created_at`, `updated_at`) VALUES
(1, 200, '2023-10-01 00:00:00', '2024-06-14 23:59:59', 1, 'App\\Models\\Event', 'd32650ff-15f8-4df1-9845-d3dc252a7a84', '2023-09-30 08:22:00', '2024-01-06 19:23:00');

CREATE TABLE `events` (
  `event_uid` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `completed` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `events` (`event_uid`, `title`, `description`, `startdate`, `enddate`, `completed`, `created_at`, `updated_at`) VALUES
('d32650ff-15f8-4df1-9845-d3dc252a7a84', 'Midnight Sun Randonnée 2024', 'Epic bike ride in the midnigtht sun', '2024-06-16', '2024-06-20', 0, '2023-09-29 22:00:00', '2023-09-29 22:00:00');

CREATE TABLE `event_groups` (
  `eventgroup_uid` char(36) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `active` tinyint(1) NOT NULL,
  `canceled` tinyint(1) NOT NULL,
  `completed` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_09_04_202104_create_countries_table', 1),
(6, '2023_09_05_172324_create_registrations_table', 1),
(7, '2023_09_06_173314_create_people_table', 1),
(8, '2023_09_06_174915_create_adresses_table', 1),
(9, '2023_09_06_175028_create_contactinformations_table', 1),
(10, '2023_09_15_080127_create_event_groups_table', 1),
(11, '2023_09_15_081013_create_events_table', 1),
(12, '2023_09_15_082017_create_event_configurations_table', 1),
(13, '2023_09_16_165059_add_msr_event_details', 1),
(14, '2023_09_16_205711_create_start_number_configs_table', 1),
(15, '2023_09_17_102527_create_orders_table', 1),
(16, '2023_09_17_125209_create_categories_table', 1),
(17, '2023_09_17_125210_add_categories_to_table', 1),
(18, '2023_09_17_134621_create_products_table', 1),
(19, '2023_09_17_193629_add_data_to_products', 1),
(20, '2023_09_18_153116_create_optionals_table', 1),
(21, '2023_09_19_153528_create_clubs_table', 1),
(22, '2023_09_20_161420_add_price_id_to_products', 1),
(23, '2023_09_21_072646_add_ref_nr_to_registrations', 1),
(24, '2023_09_24_180237_add_price_id_to_products', 1),
(25, '2023_09_28_154830_create_discount_codes_table', 1),
(26, '2023_10_01_190707_delete_unused_startnumbers', 2),
(27, '2023_10_01_192332_delete_unused_startnumbers_1252', 3),
(28, '2023_10_03_195512_remove_failed_reg_for_steven_lobregt', 4),
(29, '2023_10_05_090805_delete_registration_for_florian_kynman', 5),
(30, '2023_10_05_091345_delete_registration_for_florian_kynman_2', 6),
(34, '2023_10_05_091700_delete_optionals_for_florian_kynman', 7),
(36, '2023_10_05_094209_add_order_for_florian', 8),
(37, '2023_10_08_091614_add_registration_category_to_categories', 9),
(38, '2023_10_08_091828_add_reservation_category_to_categories', 9),
(39, '2023_10_17_074915_add_reservation__msr_2024_product_to_product', 10),
(40, '2023_10_17_075540_add_registration_msr_2024_product_to_product', 10),
(41, '2023_10_28_135459_add_order_for_martin_spreemann', 11),
(42, '2023_10_28_143018_add_order_for_klauck', 12),
(43, '2023_10_28_145659_give_klauck_startnumber_and_referensnumber', 13),
(44, '2023_10_20_173859_create_reservationconfigs_table', 14),
(45, '2023_10_20_175653_add_data_to_reservationconfig', 15),
(46, '2023_11_02_180642_remove_msrreg_for_1061_oleg_volkov', 16),
(47, '2023_11_02_181142_remove_double_msrreg_for_1004_michael_patzer', 16),
(48, '2023_11_02_183146_remove_more_failed_msrreg_for_oleg_volkov', 17),
(49, '2023_11_02_183526_remove_one_more_failed_msrreg_for_oleg_volkov', 18),
(50, '2023_11_02_184148_remove_registrations_without_startnumner_and_refnr', 19),
(51, '2023_11_02_185334_remove_failed_msrregistration_1004__graeme', 20),
(52, '2023_11_03_195052_add_morph_relation_to_product', 21),
(53, '2023_11_03_201230_change_relation_column_in_eventconfigurations', 21),
(54, '2023_11_03_201444_add_products_to_event', 22),
(55, '2023_11_03_213542_add_medal_category_to_categories', 22),
(56, '2023_11_05_203739_drop_foreign_key_on_person', 22),
(57, '2023_11_07_205940_add_person_uid_to_registrations', 22),
(58, '2023_11_07_210747_update_eventconfiguration_with_event_uid', 22),
(59, '2023_11_08_173950_update_registrations_with_person_uid', 22),
(60, '2023_11_09_172355_add_hash_column_to_person', 22),
(61, '2023_11_12_202938_update_hash_sum_for_person', 22),
(62, '2023_12_26_133026_add_price_to_productid_1006', 22),
(63, '2023_12_26_133707_add_buffedinner_to_eventconfig', 23);

CREATE TABLE `optionals` (
  `optional_uid` char(36) NOT NULL,
  `registration_uid` char(36) NOT NULL,
  `productID` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `order_id` char(36) NOT NULL,
  `registration_uid` char(36) NOT NULL,
  `payment_intent_id` varchar(100) NOT NULL,
  `payment_status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `person` (
  `person_uid` char(36) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `registration_registration_uid` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `checksum` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `productID` bigint(20) UNSIGNED NOT NULL,
  `productname` varchar(100) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `full_description` varchar(400) DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `categoryID` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `price_id` varchar(255) NOT NULL,
  `productable_type` varchar(255) NOT NULL,
  `productable_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`productID`, `productname`, `description`, `full_description`, `active`, `categoryID`, `price`, `created_at`, `updated_at`, `price_id`, `productable_type`, `productable_id`) VALUES
(1000, 'Pre-event coffee ride', 'Pre-event coffee ride - Umeå Plaza, Saturday 15 June, 10:00.', NULL, 1, 3, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1001, 'Lunch box', 'Lunch box - Baggböle Manor, Sunday 16 June, 15:00.', NULL, 1, 2, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1002, 'Baggage drop', 'Bag drop Umeå Plaza - Baggböle Manor, Sunday 16 June, 15:00.', NULL, 1, 5, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1003, 'Parking during event', 'Long-term parking - Baggböle Manor, Sunday 16 June - Thursday 20 June.', NULL, 1, 5, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1004, 'Buffet Dinner', 'Buffet Dinner- Brännland Inn, Sunday 16 June, 19:00.', NULL, 1, 2, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1005, 'Midsummer Celebration', 'Swedish Midsummer Celebration - Friday 20 June, 12:00.', NULL, 1, 3, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1006, 'Pre-event buffet', 'Pre-event buffet dinner - Saturday 15 June, 17:00, 320 SEK (-25%)', NULL, 1, 2, NULL, '2023-09-30 08:22:32', '2024-01-06 19:23:44', 'price_1OVgytLnAzN3QPcUTKVVKh5G', 'App\\Models\\EventConfiguration', 1),
(1007, 'MSR Jersey - Jersey F/M GRAND', 'GRAND Jersey F/M (87 EUR on webshop): 70 EUR', NULL, 1, 1, NULL, '2023-09-30 08:22:32', '2024-01-06 19:23:11', 'price_1NvLC4LnAzN3QPcU58Hq8vNQ', 'App\\Models\\EventConfiguration', 1),
(1008, 'MSR Jersey - Jersey F/M TOR', 'TOR 3.0 Jersey F/M (107 EUR on webshop): 86 EUR', NULL, 1, 1, NULL, '2023-09-30 08:22:32', '2024-01-06 19:23:11', 'price_1NvLC4LnAzN3QPcUpkYI3Iwu', 'App\\Models\\EventConfiguration', 1),
(1009, 'Driver looking for passengers', 'Driver looking for passengers)', NULL, 1, 4, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1010, 'Passenger looking for vehicle', 'Passenger looking for vehicle', NULL, 1, 4, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1011, 'MSR 2024 Reservation', 'Reservation for 2024 edition of Midnight Sun Randonnée', NULL, 1, 7, NULL, '2023-10-17 06:02:58', '2024-01-06 19:23:11', 'price_1NrHBYLnAzN3QPcUumT5kAA2', 'App\\Models\\EventConfiguration', 1),
(1012, 'MSR 2024 Registration', 'Registration for 2024 edition of Midnight Sun Randonnée', NULL, 1, 6, NULL, '2023-10-17 06:02:58', '2024-01-06 19:23:11', 'price_1NvK5dLnAzN3QPcUxffzaVi4', 'App\\Models\\EventConfiguration', 1);

CREATE TABLE `registrations` (
  `registration_uid` char(36) NOT NULL,
  `course_uid` char(36) NOT NULL,
  `additional_information` varchar(500) DEFAULT NULL,
  `reservation` tinyint(1) NOT NULL DEFAULT 0,
  `reservation_valid_until` date DEFAULT NULL,
  `startnumber` bigint(20) DEFAULT NULL,
  `club_uid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ref_nr` bigint(20) DEFAULT NULL,
  `person_uid` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reservationconfigs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `use_reservation_until` date NOT NULL,
  `use_reservation_on_event` tinyint(1) NOT NULL,
  `event_configuration_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reservationconfigs` (`id`, `use_reservation_until`, `use_reservation_on_event`, `event_configuration_id`, `created_at`, `updated_at`) VALUES
(1, '2023-12-31', 1, 1, '2023-10-29 15:50:18', '2023-10-29 15:50:18');

CREATE TABLE `startnumberconfigs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `begins_at` bigint(20) NOT NULL,
  `ends_at` bigint(20) NOT NULL,
  `increments` bigint(20) NOT NULL,
  `startnumberconfig_type` varchar(255) NOT NULL,
  `startnumberconfig_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `startnumberconfigs` (`id`, `begins_at`, `ends_at`, `increments`, `startnumberconfig_type`, `startnumberconfig_id`) VALUES
(1, 1001, 1201, 1, 'App\\Models\\EventConfiguration', 1);

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `adress`
  ADD PRIMARY KEY (`adress_uid`),
  ADD KEY `adress_person_person_uid_foreign` (`person_person_uid`),
  ADD KEY `adress_country_id_index` (`country_id`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`categoryID`);

ALTER TABLE `contactinformation`
  ADD PRIMARY KEY (`contactinformation_uid`),
  ADD KEY `contactinformation_person_person_uid_foreign` (`person_person_uid`);

ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`);

ALTER TABLE `eventconfigurations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evconf` (`eventconfiguration_type`,`eventconfiguration_id`);

ALTER TABLE `event_groups`
  ADD PRIMARY KEY (`eventgroup_uid`);

ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `optionals`
  ADD PRIMARY KEY (`optional_uid`),
  ADD KEY `optionals_productid_index` (`productID`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

ALTER TABLE `person`
  ADD PRIMARY KEY (`person_uid`),
  ADD KEY `person_registration_registration_uid_foreign` (`registration_registration_uid`);

ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`productID`),
  ADD KEY `products_categoryid_foreign` (`categoryID`),
  ADD KEY `i_product` (`productable_type`,`productable_id`);

ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_uid`);

ALTER TABLE `reservationconfigs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `startnumberconfigs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `s_num_conf` (`startnumberconfig_type`,`startnumberconfig_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);


ALTER TABLE `categories`
  MODIFY `categoryID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `countries`
  MODIFY `country_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

ALTER TABLE `eventconfigurations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `products`
  MODIFY `productID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1013;

ALTER TABLE `reservationconfigs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `startnumberconfigs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `adress`
  ADD CONSTRAINT `adress_person_person_uid_foreign` FOREIGN KEY (`person_person_uid`) REFERENCES `person` (`person_uid`) ON DELETE CASCADE;

ALTER TABLE `contactinformation`
  ADD CONSTRAINT `contactinformation_person_person_uid_foreign` FOREIGN KEY (`person_person_uid`) REFERENCES `person` (`person_uid`) ON DELETE CASCADE;

ALTER TABLE `optionals`
  ADD CONSTRAINT `optionals_productid_foreign` FOREIGN KEY (`productID`) REFERENCES `products` (`productID`) ON DELETE CASCADE;

ALTER TABLE `products`
  ADD CONSTRAINT `products_categoryid_foreign` FOREIGN KEY (`categoryID`) REFERENCES `categories` (`categoryID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `adress` (
  `adress_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adress` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `person_person_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `categoryID` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`categoryID`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Clothes', 'Kläder', '2023-09-30 08:22:32', '2023-09-30 08:22:32'),
(2, 'Food', 'Food', '2023-09-30 08:22:32', '2023-09-30 08:22:32'),
(3, 'Activity', 'Activity', '2023-09-30 08:22:32', '2023-09-30 08:22:32'),
(4, 'Other', 'Other', '2023-09-30 08:22:32', '2023-09-30 08:22:32'),
(5, 'Service', 'Event service for participants', '2023-09-30 08:22:32', '2023-09-30 08:22:32'),
(6, 'Registration', 'Event registration product category', '2024-02-23 20:15:26', '2024-02-23 20:15:26'),
(7, 'Reservation', 'Event reservation product category', '2024-02-23 20:15:26', '2024-02-23 20:15:26'),
(8, 'Medal', 'Event medal', '2024-02-23 20:15:26', '2024-02-23 20:15:26');

CREATE TABLE `clubs` (
  `club_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `official_club` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contactinformation` (
  `contactinformation_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tel` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `person_person_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `countries` (
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `country_name_en` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_name_sv` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag_url_svg` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag_url_png` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `countries` (`country_id`, `country_name_en`, `country_name_sv`, `country_code`, `flag_url_svg`, `flag_url_png`, `created_at`, `updated_at`) VALUES
(1, 'French Polynesia', 'Franska Polynesien', 'PF', 'https://flagcdn.com/pf.svg', 'https://flagcdn.com/w320/pf.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(2, 'Saint Martin', 'Saint-Martin', 'MF', 'https://flagcdn.com/mf.svg', 'https://flagcdn.com/w320/mf.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(3, 'Venezuela', 'Venezuela', 'VE', 'https://flagcdn.com/ve.svg', 'https://flagcdn.com/w320/ve.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(4, 'Réunion', 'Réunion', 'RE', 'https://flagcdn.com/re.svg', 'https://flagcdn.com/w320/re.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(5, 'El Salvador', 'El Salvador', 'SV', 'https://flagcdn.com/sv.svg', 'https://flagcdn.com/w320/sv.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(6, 'Dominica', 'Dominica', 'DM', 'https://flagcdn.com/dm.svg', 'https://flagcdn.com/w320/dm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(7, 'Gibraltar', 'Gibraltar', 'GI', 'https://flagcdn.com/gi.svg', 'https://flagcdn.com/w320/gi.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(8, 'Kenya', 'Kenya', 'KE', 'https://flagcdn.com/ke.svg', 'https://flagcdn.com/w320/ke.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(9, 'Brazil', 'Brasilien', 'BR', 'https://flagcdn.com/br.svg', 'https://flagcdn.com/w320/br.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(10, 'Maldives', 'Maldiverna', 'MV', 'https://flagcdn.com/mv.svg', 'https://flagcdn.com/w320/mv.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(11, 'United States', 'USA', 'US', 'https://flagcdn.com/us.svg', 'https://flagcdn.com/w320/us.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(12, 'Cook Islands', 'Cooköarna', 'CK', 'https://flagcdn.com/ck.svg', 'https://flagcdn.com/w320/ck.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(13, 'Niue', 'Niue', 'NU', 'https://flagcdn.com/nu.svg', 'https://flagcdn.com/w320/nu.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(14, 'Seychelles', 'Seychellerna', 'SC', 'https://flagcdn.com/sc.svg', 'https://flagcdn.com/w320/sc.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(15, 'Central African Republic', 'Centralafrikanska republiken', 'CF', 'https://flagcdn.com/cf.svg', 'https://flagcdn.com/w320/cf.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(16, 'Tokelau', 'Tokelauöarna', 'TK', 'https://flagcdn.com/tk.svg', 'https://flagcdn.com/w320/tk.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(17, 'Vanuatu', 'Vanuatu', 'VU', 'https://flagcdn.com/vu.svg', 'https://flagcdn.com/w320/vu.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(18, 'Gambia', 'Gambia', 'GM', 'https://flagcdn.com/gm.svg', 'https://flagcdn.com/w320/gm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(19, 'Guyana', 'Guyana', 'GY', 'https://flagcdn.com/gy.svg', 'https://flagcdn.com/w320/gy.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(20, 'Falkland Islands', 'Falklandsöarna', 'FK', 'https://flagcdn.com/fk.svg', 'https://flagcdn.com/w320/fk.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(21, 'Belgium', 'Belgien', 'BE', 'https://flagcdn.com/be.svg', 'https://flagcdn.com/w320/be.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(22, 'Western Sahara', 'Västsahara', 'EH', 'https://flagcdn.com/eh.svg', 'https://flagcdn.com/w320/eh.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(23, 'Turkey', 'Turkiet', 'TR', 'https://flagcdn.com/tr.svg', 'https://flagcdn.com/w320/tr.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(24, 'Saint Vincent and the Grenadines', 'Saint Vincent och Grenadinerna', 'VC', 'https://flagcdn.com/vc.svg', 'https://flagcdn.com/w320/vc.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(25, 'Pakistan', 'Pakistan', 'PK', 'https://flagcdn.com/pk.svg', 'https://flagcdn.com/w320/pk.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(26, 'Åland Islands', 'Åland', 'AX', 'https://flagcdn.com/ax.svg', 'https://flagcdn.com/w320/ax.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(27, 'Iran', 'Iran', 'IR', 'https://flagcdn.com/ir.svg', 'https://flagcdn.com/w320/ir.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(28, 'Indonesia', 'Indonesien', 'ID', 'https://flagcdn.com/id.svg', 'https://flagcdn.com/w320/id.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(29, 'New Zealand', 'Nya Zeeland', 'NZ', 'https://flagcdn.com/nz.svg', 'https://flagcdn.com/w320/nz.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(30, 'Afghanistan', 'Afghanistan', 'AF', 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Flag_of_the_Taliban.svg', 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Flag_of_the_Taliban.svg/320px-Flag_of_the_Taliban.svg.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(31, 'Guam', 'Guam', 'GU', 'https://flagcdn.com/gu.svg', 'https://flagcdn.com/w320/gu.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(32, 'Albania', 'Albanien', 'AL', 'https://flagcdn.com/al.svg', 'https://flagcdn.com/w320/al.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(33, 'DR Congo', 'Kongo-Kinshasa', 'CD', 'https://flagcdn.com/cd.svg', 'https://flagcdn.com/w320/cd.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(34, 'Ivory Coast', 'Elfenbenskusten', 'CI', 'https://flagcdn.com/ci.svg', 'https://flagcdn.com/w320/ci.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(35, 'Sudan', 'Sudan', 'SD', 'https://flagcdn.com/sd.svg', 'https://flagcdn.com/w320/sd.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(36, 'Timor-Leste', 'Östtimor', 'TL', 'https://flagcdn.com/tl.svg', 'https://flagcdn.com/w320/tl.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(37, 'Luxembourg', 'Luxemburg', 'LU', 'https://flagcdn.com/lu.svg', 'https://flagcdn.com/w320/lu.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(38, 'Saudi Arabia', 'Saudiarabien', 'Saudi', 'https://flagcdn.com/sa.svg', 'https://flagcdn.com/w320/sa.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(39, 'Cambodia', 'Kambodja', 'KH', 'https://flagcdn.com/kh.svg', 'https://flagcdn.com/w320/kh.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(40, 'Nepal', 'Nepal', 'NP', 'https://flagcdn.com/np.svg', 'https://flagcdn.com/w320/np.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(41, 'French Guiana', 'Franska Guyana', 'GF', 'https://flagcdn.com/gf.svg', 'https://flagcdn.com/w320/gf.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(42, 'Malaysia', 'Malaysia', 'MY', 'https://flagcdn.com/my.svg', 'https://flagcdn.com/w320/my.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(43, 'Rwanda', 'Rwanda', 'RW', 'https://flagcdn.com/rw.svg', 'https://flagcdn.com/w320/rw.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(44, 'Thailand', 'Thailand', 'TH', 'https://flagcdn.com/th.svg', 'https://flagcdn.com/w320/th.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(45, 'Antarctica', 'Antarktis', 'AQ', 'https://flagcdn.com/aq.svg', 'https://flagcdn.com/w320/aq.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(46, 'Jordan', 'Jordanien', 'JO', 'https://flagcdn.com/jo.svg', 'https://flagcdn.com/w320/jo.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(47, 'Switzerland', 'Schweiz', 'CH', 'https://flagcdn.com/ch.svg', 'https://flagcdn.com/w320/ch.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(48, 'Comoros', 'Komorerna', 'KM', 'https://flagcdn.com/km.svg', 'https://flagcdn.com/w320/km.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(49, 'Kosovo', 'Kosovo', 'XK', 'https://flagcdn.com/xk.svg', 'https://flagcdn.com/w320/xk.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(50, 'Isle of Man', 'Isle of Man', 'IM', 'https://flagcdn.com/im.svg', 'https://flagcdn.com/w320/im.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(51, 'Montenegro', 'Montenegro', 'ME', 'https://flagcdn.com/me.svg', 'https://flagcdn.com/w320/me.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(52, 'Hong Kong', 'Hongkong', 'HK', 'https://flagcdn.com/hk.svg', 'https://flagcdn.com/w320/hk.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(53, 'Jersey', 'Jersey', 'JE', 'https://flagcdn.com/je.svg', 'https://flagcdn.com/w320/je.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(54, 'Tajikistan', 'Tadzjikistan', 'TJ', 'https://flagcdn.com/tj.svg', 'https://flagcdn.com/w320/tj.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(55, 'Bulgaria', 'Bulgarien', 'BG', 'https://flagcdn.com/bg.svg', 'https://flagcdn.com/w320/bg.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(56, 'Egypt', 'Egypten', 'EG', 'https://flagcdn.com/eg.svg', 'https://flagcdn.com/w320/eg.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(57, 'Malawi', 'Malawi', 'MW', 'https://flagcdn.com/mw.svg', 'https://flagcdn.com/w320/mw.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(58, 'Cape Verde', 'Kap Verde', 'CV', 'https://flagcdn.com/cv.svg', 'https://flagcdn.com/w320/cv.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(59, 'Benin', 'Benin', 'BJ', 'https://flagcdn.com/bj.svg', 'https://flagcdn.com/w320/bj.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(60, 'Morocco', 'Marocko', 'MA', 'https://flagcdn.com/ma.svg', 'https://flagcdn.com/w320/ma.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(61, 'Ireland', 'Irland', 'IE', 'https://flagcdn.com/ie.svg', 'https://flagcdn.com/w320/ie.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(62, 'Moldova', 'Moldavien', 'MD', 'https://flagcdn.com/md.svg', 'https://flagcdn.com/w320/md.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(63, 'Denmark', 'Danmark', 'DK', 'https://flagcdn.com/dk.svg', 'https://flagcdn.com/w320/dk.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(64, 'Turkmenistan', 'Turkmenistan', 'TM', 'https://flagcdn.com/tm.svg', 'https://flagcdn.com/w320/tm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(65, 'Micronesia', 'Mikronesiska federationen', 'FM', 'https://flagcdn.com/fm.svg', 'https://flagcdn.com/w320/fm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(66, 'Monaco', 'Monaco', 'MC', 'https://flagcdn.com/mc.svg', 'https://flagcdn.com/w320/mc.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(67, 'Barbados', 'Barbados', 'BB', 'https://flagcdn.com/bb.svg', 'https://flagcdn.com/w320/bb.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(68, 'Algeria', 'Algeriet', 'DZ', 'https://flagcdn.com/dz.svg', 'https://flagcdn.com/w320/dz.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(69, 'French Southern and Antarctic Lands', 'Franska södra territorierna', 'TF', 'https://flagcdn.com/tf.svg', 'https://flagcdn.com/w320/tf.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(70, 'Eritrea', 'Eritrea', 'ER', 'https://flagcdn.com/er.svg', 'https://flagcdn.com/w320/er.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(71, 'Lesotho', 'Lesotho', 'LS', 'https://flagcdn.com/ls.svg', 'https://flagcdn.com/w320/ls.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(72, 'Tanzania', 'Tanzania', 'TZ', 'https://flagcdn.com/tz.svg', 'https://flagcdn.com/w320/tz.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(73, 'Mali', 'Mali', 'ML', 'https://flagcdn.com/ml.svg', 'https://flagcdn.com/w320/ml.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(74, 'Niger', 'Niger', 'NE', 'https://flagcdn.com/ne.svg', 'https://flagcdn.com/w320/ne.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(75, 'Andorra', 'Andorra', 'AD', 'https://flagcdn.com/ad.svg', 'https://flagcdn.com/w320/ad.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(76, 'United Kingdom', 'Storbritannien', 'GB', 'https://flagcdn.com/gb.svg', 'https://flagcdn.com/w320/gb.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(77, 'Germany', 'Tyskland', 'DE', 'https://flagcdn.com/de.svg', 'https://flagcdn.com/w320/de.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(78, 'United States Virgin Islands', 'Amerikanska Jungfruöarna', 'VI', 'https://flagcdn.com/vi.svg', 'https://flagcdn.com/w320/vi.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(79, 'Somalia', 'Somalia', 'SO', 'https://flagcdn.com/so.svg', 'https://flagcdn.com/w320/so.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(80, 'Sint Maarten', 'Sint Maarten', 'SX', 'https://flagcdn.com/sx.svg', 'https://flagcdn.com/w320/sx.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(81, 'Cameroon', 'Kamerun', 'CM', 'https://flagcdn.com/cm.svg', 'https://flagcdn.com/w320/cm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(82, 'Dominican Republic', 'Dominikanska republiken', 'DO', 'https://flagcdn.com/do.svg', 'https://flagcdn.com/w320/do.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(83, 'Guinea', 'Guinea', 'GN', 'https://flagcdn.com/gn.svg', 'https://flagcdn.com/w320/gn.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(84, 'Namibia', 'Namibia', 'NA', 'https://flagcdn.com/na.svg', 'https://flagcdn.com/w320/na.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(85, 'Montserrat', 'Montserrat', 'MS', 'https://flagcdn.com/ms.svg', 'https://flagcdn.com/w320/ms.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(86, 'South Georgia', 'Sydgeorgien', 'GS', 'https://flagcdn.com/gs.svg', 'https://flagcdn.com/w320/gs.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(87, 'Senegal', 'Senegal', 'SN', 'https://flagcdn.com/sn.svg', 'https://flagcdn.com/w320/sn.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(88, 'Bouvet Island', 'Bouvetön', 'BV', 'https://flagcdn.com/bv.svg', 'https://flagcdn.com/w320/bv.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(89, 'Solomon Islands', 'Salomonöarna', 'SB', 'https://flagcdn.com/sb.svg', 'https://flagcdn.com/w320/sb.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(90, 'France', 'Frankrike', 'FR', 'https://flagcdn.com/fr.svg', 'https://flagcdn.com/w320/fr.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(91, 'Saint Helena, Ascension and Tristan da Cunha', 'Sankta Helena', 'Saint Helena', 'https://flagcdn.com/sh.svg', 'https://flagcdn.com/w320/sh.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(92, 'Macau', 'Macao', 'MO', 'https://flagcdn.com/mo.svg', 'https://flagcdn.com/w320/mo.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(93, 'Argentina', 'Argentina', 'AR', 'https://flagcdn.com/ar.svg', 'https://flagcdn.com/w320/ar.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(94, 'Bosnia and Herzegovina', 'Bosnien och Hercegovina', 'BA', 'https://flagcdn.com/ba.svg', 'https://flagcdn.com/w320/ba.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(95, 'Anguilla', 'Anguilla', 'AI', 'https://flagcdn.com/ai.svg', 'https://flagcdn.com/w320/ai.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(96, 'Guernsey', 'Guernsey', 'GG', 'https://flagcdn.com/gg.svg', 'https://flagcdn.com/w320/gg.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(97, 'Djibouti', 'Djibouti', 'DJ', 'https://flagcdn.com/dj.svg', 'https://flagcdn.com/w320/dj.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(98, 'Saint Kitts and Nevis', 'Saint Kitts och Nevis', 'KN', 'https://flagcdn.com/kn.svg', 'https://flagcdn.com/w320/kn.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(99, 'Syria', 'Syrien', 'SY', 'https://flagcdn.com/sy.svg', 'https://flagcdn.com/w320/sy.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(100, 'Puerto Rico', 'Puerto Rico', 'PR', 'https://flagcdn.com/pr.svg', 'https://flagcdn.com/w320/pr.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(101, 'Peru', 'Peru', 'PE', 'https://flagcdn.com/pe.svg', 'https://flagcdn.com/w320/pe.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(102, 'San Marino', 'San Marino', 'SM', 'https://flagcdn.com/sm.svg', 'https://flagcdn.com/w320/sm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(103, 'Australia', 'Australien', 'AU', 'https://flagcdn.com/au.svg', 'https://flagcdn.com/w320/au.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(104, 'New Caledonia', 'Nya Kaledonien', 'NC', 'https://flagcdn.com/nc.svg', 'https://flagcdn.com/w320/nc.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(105, 'Jamaica', 'Jamaica', 'JM', 'https://flagcdn.com/jm.svg', 'https://flagcdn.com/w320/jm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(106, 'Kazakhstan', 'Kazakstan', 'KZ', 'https://flagcdn.com/kz.svg', 'https://flagcdn.com/w320/kz.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(107, 'Sierra Leone', 'Sierra Leone', 'SL', 'https://flagcdn.com/sl.svg', 'https://flagcdn.com/w320/sl.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(108, 'Palau', 'Palau', 'PW', 'https://flagcdn.com/pw.svg', 'https://flagcdn.com/w320/pw.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(109, 'South Korea', 'Sydkorea', 'KR', 'https://flagcdn.com/kr.svg', 'https://flagcdn.com/w320/kr.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(110, 'Saint Pierre and Miquelon', 'Saint-Pierre och Miquelon', 'PM', 'https://flagcdn.com/pm.svg', 'https://flagcdn.com/w320/pm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(111, 'Belize', 'Belize', 'BZ', 'https://flagcdn.com/bz.svg', 'https://flagcdn.com/w320/bz.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(112, 'Papua New Guinea', 'Papua Nya Guinea', 'PG', 'https://flagcdn.com/pg.svg', 'https://flagcdn.com/w320/pg.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(113, 'Iceland', 'Island', 'IS', 'https://flagcdn.com/is.svg', 'https://flagcdn.com/w320/is.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(114, 'American Samoa', 'Amerikanska Samoa', 'AS', 'https://flagcdn.com/as.svg', 'https://flagcdn.com/w320/as.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(115, 'Burkina Faso', 'Burkina Faso', 'BF', 'https://flagcdn.com/bf.svg', 'https://flagcdn.com/w320/bf.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(116, 'Portugal', 'Portugal', 'PT', 'https://flagcdn.com/pt.svg', 'https://flagcdn.com/w320/pt.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(117, 'Taiwan', 'Taiwan', 'TW', 'https://flagcdn.com/tw.svg', 'https://flagcdn.com/w320/tw.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(118, 'Japan', 'Japan', 'JP', 'https://flagcdn.com/jp.svg', 'https://flagcdn.com/w320/jp.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(119, 'China', 'Kina', 'CN', 'https://flagcdn.com/cn.svg', 'https://flagcdn.com/w320/cn.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(120, 'Lebanon', 'Libanon', 'LB', 'https://flagcdn.com/lb.svg', 'https://flagcdn.com/w320/lb.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(121, 'Sri Lanka', 'Sri Lanka', 'LK', 'https://flagcdn.com/lk.svg', 'https://flagcdn.com/w320/lk.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(122, 'Guatemala', 'Guatemala', 'GT', 'https://flagcdn.com/gt.svg', 'https://flagcdn.com/w320/gt.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(123, 'Serbia', 'Serbien', 'RS', 'https://flagcdn.com/rs.svg', 'https://flagcdn.com/w320/rs.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(124, 'Madagascar', 'Madagaskar', 'MG', 'https://flagcdn.com/mg.svg', 'https://flagcdn.com/w320/mg.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(125, 'Eswatini', 'Swaziland', 'SZ', 'https://flagcdn.com/sz.svg', 'https://flagcdn.com/w320/sz.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(126, 'Romania', 'Rumänien', 'RO', 'https://flagcdn.com/ro.svg', 'https://flagcdn.com/w320/ro.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(127, 'Antigua and Barbuda', 'Antigua och Barbuda', 'AG', 'https://flagcdn.com/ag.svg', 'https://flagcdn.com/w320/ag.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(128, 'Curaçao', 'Curaçao', 'CW', 'https://flagcdn.com/cw.svg', 'https://flagcdn.com/w320/cw.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(129, 'Zambia', 'Zambia', 'ZM', 'https://flagcdn.com/zm.svg', 'https://flagcdn.com/w320/zm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(130, 'Zimbabwe', 'Zimbabwe', 'ZW', 'https://flagcdn.com/zw.svg', 'https://flagcdn.com/w320/zw.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(131, 'Tunisia', 'Tunisien', 'TN', 'https://flagcdn.com/tn.svg', 'https://flagcdn.com/w320/tn.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(132, 'United Arab Emirates', 'Förenade Arabemiraten', 'AE', 'https://flagcdn.com/ae.svg', 'https://flagcdn.com/w320/ae.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(133, 'Mongolia', 'Mongoliet', 'MN', 'https://flagcdn.com/mn.svg', 'https://flagcdn.com/w320/mn.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(134, 'Norway', 'Norge', 'NO', 'https://flagcdn.com/no.svg', 'https://flagcdn.com/w320/no.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(135, 'Greenland', 'Grönland', 'GL', 'https://flagcdn.com/gl.svg', 'https://flagcdn.com/w320/gl.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(136, 'Uruguay', 'Uruguay', 'UY', 'https://flagcdn.com/uy.svg', 'https://flagcdn.com/w320/uy.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(137, 'Bahamas', 'Bahamas', 'BS', 'https://flagcdn.com/bs.svg', 'https://flagcdn.com/w320/bs.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(138, 'Russia', 'Ryssland', 'RU', 'https://flagcdn.com/ru.svg', 'https://flagcdn.com/w320/ru.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(139, 'British Virgin Islands', 'Brittiska Jungfruöarna', 'VG', 'https://flagcdn.com/vg.svg', 'https://flagcdn.com/w320/vg.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(140, 'Wallis and Futuna', 'Wallis- och Futunaöarna', 'WF', 'https://flagcdn.com/wf.svg', 'https://flagcdn.com/w320/wf.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(141, 'Chad', 'Tchad', 'TD', 'https://flagcdn.com/td.svg', 'https://flagcdn.com/w320/td.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(142, 'Saint Lucia', 'Saint Lucia', 'LC', 'https://flagcdn.com/lc.svg', 'https://flagcdn.com/w320/lc.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(143, 'Yemen', 'Jemen', 'YE', 'https://flagcdn.com/ye.svg', 'https://flagcdn.com/w320/ye.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(144, 'United States Minor Outlying Islands', 'Förenta staternas mindre öar i Oceanien och Västindien', 'UM', 'https://flagcdn.com/um.svg', 'https://flagcdn.com/w320/um.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(145, 'Sweden', 'Sverige', 'SE', 'https://flagcdn.com/se.svg', 'https://flagcdn.com/w320/se.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(146, 'Svalbard and Jan Mayen', 'Svalbard och Jan Mayen', 'SJ', 'https://flagcdn.com/sj.svg', 'https://flagcdn.com/w320/sj.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(147, 'Laos', 'Laos', 'LA', 'https://flagcdn.com/la.svg', 'https://flagcdn.com/w320/la.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(148, 'Latvia', 'Lettland', 'LV', 'https://flagcdn.com/lv.svg', 'https://flagcdn.com/w320/lv.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(149, 'Colombia', 'Colombia', 'CO', 'https://flagcdn.com/co.svg', 'https://flagcdn.com/w320/co.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(150, 'Grenada', 'Grenada', 'GD', 'https://flagcdn.com/gd.svg', 'https://flagcdn.com/w320/gd.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(151, 'Saint Barthélemy', 'Saint-Barthélemy', 'BL', 'https://flagcdn.com/bl.svg', 'https://flagcdn.com/w320/bl.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(152, 'Canada', 'Kanada', 'CA', 'https://flagcdn.com/ca.svg', 'https://flagcdn.com/w320/ca.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(153, 'Heard Island and McDonald Islands', 'Heard- och McDonaldöarna', 'HM', 'https://flagcdn.com/hm.svg', 'https://flagcdn.com/w320/hm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(154, 'India', 'Indien', 'IN', 'https://flagcdn.com/in.svg', 'https://flagcdn.com/w320/in.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(155, 'Guinea-Bissau', 'Guinea-Bissau', 'GW', 'https://flagcdn.com/gw.svg', 'https://flagcdn.com/w320/gw.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(156, 'North Macedonia', 'Nordmakedonien', 'MK', 'https://flagcdn.com/mk.svg', 'https://flagcdn.com/w320/mk.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(157, 'Paraguay', 'Paraguay', 'PY', 'https://flagcdn.com/py.svg', 'https://flagcdn.com/w320/py.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(158, 'Croatia', 'Kroatien', 'HR', 'https://flagcdn.com/hr.svg', 'https://flagcdn.com/w320/hr.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(159, 'Costa Rica', 'Costa Rica', 'CR', 'https://flagcdn.com/cr.svg', 'https://flagcdn.com/w320/cr.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(160, 'Uganda', 'Uganda', 'UG', 'https://flagcdn.com/ug.svg', 'https://flagcdn.com/w320/ug.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(161, 'Caribbean Netherlands', 'Karibiska Nederländerna', 'BES islands', 'https://flagcdn.com/bq.svg', 'https://flagcdn.com/w320/bq.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(162, 'Bolivia', 'Bolivia', 'BO', 'https://flagcdn.com/bo.svg', 'https://flagcdn.com/w320/bo.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(163, 'Togo', 'Togo', 'TG', 'https://flagcdn.com/tg.svg', 'https://flagcdn.com/w320/tg.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(164, 'Mayotte', 'Mayotte', 'YT', 'https://flagcdn.com/yt.svg', 'https://flagcdn.com/w320/yt.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(165, 'Marshall Islands', 'Marshallöarna', 'MH', 'https://flagcdn.com/mh.svg', 'https://flagcdn.com/w320/mh.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(166, 'North Korea', 'Nordkorea', 'KP', 'https://flagcdn.com/kp.svg', 'https://flagcdn.com/w320/kp.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(167, 'Netherlands', 'Nederländerna', 'NL', 'https://flagcdn.com/nl.svg', 'https://flagcdn.com/w320/nl.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(168, 'British Indian Ocean Territory', 'Brittiska territoriet i Indiska Oceanen', 'IO', 'https://flagcdn.com/io.svg', 'https://flagcdn.com/w320/io.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(169, 'Malta', 'Malta', 'MT', 'https://flagcdn.com/mt.svg', 'https://flagcdn.com/w320/mt.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(170, 'Mauritius', 'Mauritius', 'MU', 'https://flagcdn.com/mu.svg', 'https://flagcdn.com/w320/mu.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(171, 'Norfolk Island', 'Norfolkön', 'NF', 'https://flagcdn.com/nf.svg', 'https://flagcdn.com/w320/nf.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(172, 'Honduras', 'Honduras', 'HN', 'https://flagcdn.com/hn.svg', 'https://flagcdn.com/w320/hn.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(173, 'Spain', 'Spanien', 'ES', 'https://flagcdn.com/es.svg', 'https://flagcdn.com/w320/es.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(174, 'Estonia', 'Estland', 'EE', 'https://flagcdn.com/ee.svg', 'https://flagcdn.com/w320/ee.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(175, 'Kyrgyzstan', 'Kirgizistan', 'KG', 'https://flagcdn.com/kg.svg', 'https://flagcdn.com/w320/kg.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(176, 'Chile', 'Chile', 'CL', 'https://flagcdn.com/cl.svg', 'https://flagcdn.com/w320/cl.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(177, 'Bermuda', 'Bermuda', 'BM', 'https://flagcdn.com/bm.svg', 'https://flagcdn.com/w320/bm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(178, 'Equatorial Guinea', 'Ekvatorialguinea', 'GQ', 'https://flagcdn.com/gq.svg', 'https://flagcdn.com/w320/gq.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(179, 'Liberia', 'Liberia', 'LR', 'https://flagcdn.com/lr.svg', 'https://flagcdn.com/w320/lr.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(180, 'Pitcairn Islands', 'Pitcairnöarna', 'PN', 'https://flagcdn.com/pn.svg', 'https://flagcdn.com/w320/pn.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(181, 'Libya', 'Libyen', 'LY', 'https://flagcdn.com/ly.svg', 'https://flagcdn.com/w320/ly.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(182, 'Liechtenstein', 'Liechtenstein', 'LI', 'https://flagcdn.com/li.svg', 'https://flagcdn.com/w320/li.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(183, 'Vatican City', 'Vatikanstaten', 'VA', 'https://flagcdn.com/va.svg', 'https://flagcdn.com/w320/va.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(184, 'Christmas Island', 'Julön', 'CX', 'https://flagcdn.com/cx.svg', 'https://flagcdn.com/w320/cx.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(185, 'Oman', 'Oman', 'OM', 'https://flagcdn.com/om.svg', 'https://flagcdn.com/w320/om.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(186, 'Philippines', 'Filippinerna', 'PH', 'https://flagcdn.com/ph.svg', 'https://flagcdn.com/w320/ph.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(187, 'Poland', 'Polen', 'PL', 'https://flagcdn.com/pl.svg', 'https://flagcdn.com/w320/pl.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(188, 'Faroe Islands', 'Färöarna', 'FO', 'https://flagcdn.com/fo.svg', 'https://flagcdn.com/w320/fo.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(189, 'Bahrain', 'Bahrain', 'BH', 'https://flagcdn.com/bh.svg', 'https://flagcdn.com/w320/bh.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(190, 'Belarus', 'Belarus', 'BY', 'https://flagcdn.com/by.svg', 'https://flagcdn.com/w320/by.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(191, 'Slovenia', 'Slovenien', 'SI', 'https://flagcdn.com/si.svg', 'https://flagcdn.com/w320/si.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(192, 'Guadeloupe', 'Guadeloupe', 'GP', 'https://flagcdn.com/gp.svg', 'https://flagcdn.com/w320/gp.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(193, 'Qatar', 'Qatar', 'QA', 'https://flagcdn.com/qa.svg', 'https://flagcdn.com/w320/qa.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(194, 'Vietnam', 'Vietnam', 'VN', 'https://flagcdn.com/vn.svg', 'https://flagcdn.com/w320/vn.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(195, 'Mauritania', 'Mauretanien', 'MR', 'https://flagcdn.com/mr.svg', 'https://flagcdn.com/w320/mr.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(196, 'Singapore', 'Singapore', 'SG', 'https://flagcdn.com/sg.svg', 'https://flagcdn.com/w320/sg.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(197, 'Georgia', 'Georgien', 'GE', 'https://flagcdn.com/ge.svg', 'https://flagcdn.com/w320/ge.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(198, 'Burundi', 'Burundi', 'BI', 'https://flagcdn.com/bi.svg', 'https://flagcdn.com/w320/bi.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(199, 'Nauru', 'Nauru', 'NR', 'https://flagcdn.com/nr.svg', 'https://flagcdn.com/w320/nr.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(200, 'South Sudan', 'Sydsudan', 'SS', 'https://flagcdn.com/ss.svg', 'https://flagcdn.com/w320/ss.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(201, 'Samoa', 'Samoa', 'WS', 'https://flagcdn.com/ws.svg', 'https://flagcdn.com/w320/ws.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(202, 'Cocos (Keeling) Islands', 'Kokosöarna', 'CC', 'https://flagcdn.com/cc.svg', 'https://flagcdn.com/w320/cc.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(203, 'Republic of the Congo', 'Kongo-Brazzaville', 'CG', 'https://flagcdn.com/cg.svg', 'https://flagcdn.com/w320/cg.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(204, 'Cyprus', 'Cypern', 'CY', 'https://flagcdn.com/cy.svg', 'https://flagcdn.com/w320/cy.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(205, 'Kuwait', 'Kuwait', 'KW', 'https://flagcdn.com/kw.svg', 'https://flagcdn.com/w320/kw.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(206, 'Trinidad and Tobago', 'Trinidad och Tobago', 'TT', 'https://flagcdn.com/tt.svg', 'https://flagcdn.com/w320/tt.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(207, 'Tuvalu', 'Tuvalu', 'TV', 'https://flagcdn.com/tv.svg', 'https://flagcdn.com/w320/tv.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(208, 'Angola', 'Angola', 'AO', 'https://flagcdn.com/ao.svg', 'https://flagcdn.com/w320/ao.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(209, 'Tonga', 'Tonga', 'TO', 'https://flagcdn.com/to.svg', 'https://flagcdn.com/w320/to.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(210, 'Greece', 'Grekland', 'GR', 'https://flagcdn.com/gr.svg', 'https://flagcdn.com/w320/gr.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(211, 'Mozambique', 'Moçambique', 'MZ', 'https://flagcdn.com/mz.svg', 'https://flagcdn.com/w320/mz.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(212, 'Myanmar', 'Myanmar', 'MM', 'https://flagcdn.com/mm.svg', 'https://flagcdn.com/w320/mm.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(213, 'Austria', 'Österrike', 'AT', 'https://flagcdn.com/at.svg', 'https://flagcdn.com/w320/at.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(214, 'Ethiopia', 'Etiopien', 'ET', 'https://flagcdn.com/et.svg', 'https://flagcdn.com/w320/et.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(215, 'Martinique', 'Martinique', 'MQ', 'https://flagcdn.com/mq.svg', 'https://flagcdn.com/w320/mq.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(216, 'Azerbaijan', 'Azerbajdzjan', 'AZ', 'https://flagcdn.com/az.svg', 'https://flagcdn.com/w320/az.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(217, 'Uzbekistan', 'Uzbekistan', 'UZ', 'https://flagcdn.com/uz.svg', 'https://flagcdn.com/w320/uz.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(218, 'Bangladesh', 'Bangladesh', 'BD', 'https://flagcdn.com/bd.svg', 'https://flagcdn.com/w320/bd.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(219, 'Armenia', 'Armenien', 'AM', 'https://flagcdn.com/am.svg', 'https://flagcdn.com/w320/am.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(220, 'Nigeria', 'Nigeria', 'NG', 'https://flagcdn.com/ng.svg', 'https://flagcdn.com/w320/ng.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(221, 'South Africa', 'Sydafrika', 'ZA', 'https://flagcdn.com/za.svg', 'https://flagcdn.com/w320/za.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(222, 'Brunei', 'Brunei', 'BN', 'https://flagcdn.com/bn.svg', 'https://flagcdn.com/w320/bn.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(223, 'Italy', 'Italien', 'IT', 'https://flagcdn.com/it.svg', 'https://flagcdn.com/w320/it.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(224, 'Finland', 'Finland', 'FI', 'https://flagcdn.com/fi.svg', 'https://flagcdn.com/w320/fi.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(225, 'Israel', 'Israel', 'IL', 'https://flagcdn.com/il.svg', 'https://flagcdn.com/w320/il.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(226, 'Aruba', 'Aruba', 'AW', 'https://flagcdn.com/aw.svg', 'https://flagcdn.com/w320/aw.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(227, 'Nicaragua', 'Nicaragua', 'NI', 'https://flagcdn.com/ni.svg', 'https://flagcdn.com/w320/ni.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(228, 'Haiti', 'Haiti', 'HT', 'https://flagcdn.com/ht.svg', 'https://flagcdn.com/w320/ht.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(229, 'Kiribati', 'Kiribati', 'KI', 'https://flagcdn.com/ki.svg', 'https://flagcdn.com/w320/ki.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(230, 'Turks and Caicos Islands', 'Turks- och Caicosöarna', 'TC', 'https://flagcdn.com/tc.svg', 'https://flagcdn.com/w320/tc.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(231, 'Cayman Islands', 'Caymanöarna', 'KY', 'https://flagcdn.com/ky.svg', 'https://flagcdn.com/w320/ky.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(232, 'Ukraine', 'Ukraina', 'UA', 'https://flagcdn.com/ua.svg', 'https://flagcdn.com/w320/ua.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(233, 'Mexico', 'Mexiko', 'MX', 'https://flagcdn.com/mx.svg', 'https://flagcdn.com/w320/mx.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(234, 'Palestine', 'Palestina', 'PS', 'https://flagcdn.com/ps.svg', 'https://flagcdn.com/w320/ps.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(235, 'Fiji', 'Fiji', 'FJ', 'https://flagcdn.com/fj.svg', 'https://flagcdn.com/w320/fj.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(236, 'Slovakia', 'Slovakien', 'SK', 'https://flagcdn.com/sk.svg', 'https://flagcdn.com/w320/sk.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(237, 'Ghana', 'Ghana', 'GH', 'https://flagcdn.com/gh.svg', 'https://flagcdn.com/w320/gh.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(238, 'Suriname', 'Surinam', 'SR', 'https://flagcdn.com/sr.svg', 'https://flagcdn.com/w320/sr.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(239, 'Cuba', 'Kuba', 'CU', 'https://flagcdn.com/cu.svg', 'https://flagcdn.com/w320/cu.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(240, 'Bhutan', 'Bhutan', 'BT', 'https://flagcdn.com/bt.svg', 'https://flagcdn.com/w320/bt.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(241, 'Hungary', 'Ungern', 'HU', 'https://flagcdn.com/hu.svg', 'https://flagcdn.com/w320/hu.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(242, 'São Tomé and Príncipe', 'São Tomé och Príncipe', 'ST', 'https://flagcdn.com/st.svg', 'https://flagcdn.com/w320/st.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(243, 'Iraq', 'Irak', 'IQ', 'https://flagcdn.com/iq.svg', 'https://flagcdn.com/w320/iq.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(244, 'Czechia', 'Tjeckien', 'CZ', 'https://flagcdn.com/cz.svg', 'https://flagcdn.com/w320/cz.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(245, 'Lithuania', 'Litauen', 'LT', 'https://flagcdn.com/lt.svg', 'https://flagcdn.com/w320/lt.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(246, 'Northern Mariana Islands', 'Nordmarianerna', 'MP', 'https://flagcdn.com/mp.svg', 'https://flagcdn.com/w320/mp.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(247, 'Botswana', 'Botswana', 'BW', 'https://flagcdn.com/bw.svg', 'https://flagcdn.com/w320/bw.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(248, 'Panama', 'Panama', 'PA', 'https://flagcdn.com/pa.svg', 'https://flagcdn.com/w320/pa.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27'),
(249, 'Gabon', 'Gabon', 'GA', 'https://flagcdn.com/ga.svg', 'https://flagcdn.com/w320/ga.png', '2023-09-30 08:22:34', '2024-02-23 20:15:28'),
(250, 'Ecuador', 'Ecuador', 'EC', 'https://flagcdn.com/ec.svg', 'https://flagcdn.com/w320/ec.png', '2023-09-30 08:22:34', '2024-02-23 20:15:27');

CREATE TABLE `discount_codes` (
  `discountcode_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_amount` decimal(13,4) NOT NULL,
  `expirered` tinyint(1) NOT NULL DEFAULT 0,
  `unlimited` tinyint(1) NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `usage_limit` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `eventconfigurations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `max_registrations` bigint(20) NOT NULL,
  `registration_opens` datetime NOT NULL,
  `registration_closes` datetime NOT NULL,
  `resarvation_on_event` tinyint(1) NOT NULL DEFAULT 0,
  `eventconfiguration_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eventconfiguration_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `eventconfigurations` (`id`, `max_registrations`, `registration_opens`, `registration_closes`, `resarvation_on_event`, `eventconfiguration_type`, `eventconfiguration_id`, `created_at`, `updated_at`) VALUES
(1, 200, '2023-09-01 00:00:00', '2024-06-14 23:59:59', 1, 'App\\Models\\Event', 'd32650ff-15f8-4df1-9845-d3dc252a7a84', '2023-09-30 08:22:00', '2024-02-23 20:15:00');

CREATE TABLE `events` (
  `event_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `completed` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `events` (`event_uid`, `title`, `description`, `startdate`, `enddate`, `completed`, `created_at`, `updated_at`) VALUES
('d32650ff-15f8-4df1-9845-d3dc252a7a84', 'Midnight Sun Randonnée 2024', 'Epic bike ride in the midnigtht sun', '2024-06-16', '2024-06-20', 0, '2023-09-29 22:00:00', '2023-09-29 22:00:00');

CREATE TABLE `event_groups` (
  `eventgroup_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `active` tinyint(1) NOT NULL,
  `canceled` tinyint(1) NOT NULL,
  `completed` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_09_04_202104_create_countries_table', 1),
(6, '2023_09_05_172324_create_registrations_table', 1),
(7, '2023_09_06_173314_create_people_table', 1),
(8, '2023_09_06_174915_create_adresses_table', 1),
(9, '2023_09_06_175028_create_contactinformations_table', 1),
(10, '2023_09_15_080127_create_event_groups_table', 1),
(11, '2023_09_15_081013_create_events_table', 1),
(12, '2023_09_15_082017_create_event_configurations_table', 1),
(13, '2023_09_16_165059_add_msr_event_details', 1),
(14, '2023_09_16_205711_create_start_number_configs_table', 1),
(15, '2023_09_17_102527_create_orders_table', 1),
(16, '2023_09_17_125209_create_categories_table', 1),
(17, '2023_09_17_125210_add_categories_to_table', 1),
(18, '2023_09_17_134621_create_products_table', 1),
(19, '2023_09_17_193629_add_data_to_products', 1),
(20, '2023_09_18_153116_create_optionals_table', 1),
(21, '2023_09_19_153528_create_clubs_table', 1),
(22, '2023_09_20_161420_add_price_id_to_products', 1),
(23, '2023_09_21_072646_add_ref_nr_to_registrations', 1),
(24, '2023_09_24_180237_add_price_id_to_products', 1),
(25, '2023_09_28_154830_create_discount_codes_table', 1),
(26, '2023_10_01_190707_delete_unused_startnumbers', 2),
(27, '2023_10_01_192332_delete_unused_startnumbers_1252', 2),
(28, '2023_10_03_195512_remove_failed_reg_for_steven_lobregt', 2),
(29, '2023_10_05_090805_delete_registration_for_florian_kynman', 2),
(30, '2023_10_05_091345_delete_registration_for_florian_kynman_2', 2),
(31, '2023_10_05_091700_delete_optionals_for_florian_kynman', 2),
(32, '2023_10_05_094209_add_order_for_florian', 2),
(33, '2023_10_08_091614_add_registration_category_to_categories', 2),
(34, '2023_10_08_091828_add_reservation_category_to_categories', 2),
(35, '2023_10_17_074915_add_reservation__msr_2024_product_to_product', 2),
(36, '2023_10_17_075540_add_registration_msr_2024_product_to_product', 2),
(37, '2023_10_20_173859_create_reservationconfigs_table', 2),
(38, '2023_10_20_175653_add_data_to_reservationconfig', 2),
(39, '2023_10_28_135459_add_order_for_martin_spreemann', 2),
(40, '2023_10_28_143018_add_order_for_klauck', 2),
(41, '2023_10_28_145659_give_klauck_startnumber_and_referensnumber', 2),
(42, '2023_11_02_180642_remove_msrreg_for_1061_oleg_volkov', 2),
(43, '2023_11_02_181142_remove_double_msrreg_for_1004_michael_patzer', 2),
(44, '2023_11_02_183146_remove_more_failed_msrreg_for_oleg_volkov', 2),
(45, '2023_11_02_183526_remove_one_more_failed_msrreg_for_oleg_volkov', 2),
(46, '2023_11_02_184148_remove_registrations_without_startnumner_and_refnr', 2),
(47, '2023_11_02_185334_remove_failed_msrregistration_1004__graeme', 2),
(48, '2023_11_03_195052_add_morph_relation_to_product', 2),
(49, '2023_11_03_201230_change_relation_column_in_eventconfigurations', 2),
(50, '2023_11_03_201444_add_products_to_event', 2),
(51, '2023_11_03_213542_add_medal_category_to_categories', 2),
(52, '2023_11_05_203739_drop_foreign_key_on_person', 2),
(53, '2023_11_07_205940_add_person_uid_to_registrations', 2),
(54, '2023_11_07_210747_update_eventconfiguration_with_event_uid', 2),
(55, '2023_11_08_173950_update_registrations_with_person_uid', 2),
(56, '2023_11_09_172355_add_hash_column_to_person', 2),
(57, '2023_11_12_202938_update_hash_sum_for_person', 2);

CREATE TABLE `optionals` (
  `optional_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `productID` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `order_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_intent_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `person` (
  `person_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthdate` date DEFAULT NULL,
  `registration_registration_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `checksum` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `productID` bigint(20) UNSIGNED NOT NULL,
  `productname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_description` varchar(400) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `categoryID` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `price_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `productable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `productable_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`productID`, `productname`, `description`, `full_description`, `active`, `categoryID`, `price`, `created_at`, `updated_at`, `price_id`, `productable_type`, `productable_id`) VALUES
(1000, 'Pre-event coffee ride', 'Pre-event coffee ride - Umeå Plaza, Saturday 15 June, 10:00.', NULL, 1, 3, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1001, 'Lunch box', 'Lunch box - Baggböle Manor, Sunday 16 June, 15:00.', NULL, 1, 2, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1002, 'Baggage drop', 'Bag drop Umeå Plaza - Baggböle Manor, Sunday 16 June, 15:00.', NULL, 1, 5, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1003, 'Parking during event', 'Long-term parking - Baggböle Manor, Sunday 16 June - Thursday 20 June.', NULL, 1, 5, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1004, 'Buffet Dinner', 'Buffet Dinner- Brännland Inn, Sunday 16 June, 19:00.', NULL, 1, 2, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1005, 'Midsummer Celebration', 'Swedish Midsummer Celebration - Friday 20 June, 12:00.', NULL, 1, 3, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1006, 'Pre-event buffet', 'Pre-event buffet dinner - Saturday 15 June, 17:00, 320 SEK (-25%)', NULL, 1, 2, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1007, 'MSR Jersey - Jersey F/M GRAND', 'GRAND Jersey F/M (87 EUR on webshop): 70 EUR', NULL, 1, 1, NULL, '2023-09-30 08:22:32', '2024-02-23 20:15:26', 'price_1NvLC4LnAzN3QPcU58Hq8vNQ', 'App\\Models\\EventConfiguration', 1),
(1008, 'MSR Jersey - Jersey F/M TOR', 'TOR 3.0 Jersey F/M (107 EUR on webshop): 86 EUR', NULL, 1, 1, NULL, '2023-09-30 08:22:32', '2024-02-23 20:15:26', 'price_1NvLC4LnAzN3QPcUpkYI3Iwu', 'App\\Models\\EventConfiguration', 1),
(1009, 'Driver looking for passengers', 'Driver looking for passengers)', NULL, 1, 4, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1010, 'Passenger looking for vehicle', 'Passenger looking for vehicle', NULL, 1, 4, NULL, '2023-09-30 08:22:32', '2023-09-30 08:22:32', '', '', 0),
(1011, 'MSR 2024 Reservation', 'Reservation for 2024 edition of Midnight Sun Randonnée', NULL, 1, 7, NULL, '2024-02-23 20:15:26', '2024-02-23 20:15:26', 'price_1NvL3BLnAzN3QPcU8FcaSorF', 'App\\Models\\EventConfiguration', 1),
(1012, 'MSR 2024 Registration', 'Registration for 2024 edition of Midnight Sun Randonnée', NULL, 1, 6, NULL, '2024-02-23 20:15:26', '2024-02-23 20:15:26', 'price_1NvL2CLnAzN3QPcUka5kMIwR', 'App\\Models\\EventConfiguration', 1);

CREATE TABLE `registrations` (
  `registration_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_information` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reservation` tinyint(1) NOT NULL DEFAULT 0,
  `reservation_valid_until` date DEFAULT NULL,
  `startnumber` bigint(20) DEFAULT NULL,
  `club_uid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ref_nr` bigint(20) DEFAULT NULL,
  `person_uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reservationconfigs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `use_reservation_until` date NOT NULL,
  `use_reservation_on_event` tinyint(1) NOT NULL,
  `event_configuration_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reservationconfigs` (`id`, `use_reservation_until`, `use_reservation_on_event`, `event_configuration_id`, `created_at`, `updated_at`) VALUES
(1, '2023-12-31', 1, 1, '2024-02-23 20:15:26', '2024-02-23 20:15:26');

CREATE TABLE `startnumberconfigs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `begins_at` bigint(20) NOT NULL,
  `ends_at` bigint(20) NOT NULL,
  `increments` bigint(20) NOT NULL,
  `startnumberconfig_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `startnumberconfig_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `startnumberconfigs` (`id`, `begins_at`, `ends_at`, `increments`, `startnumberconfig_type`, `startnumberconfig_id`) VALUES
(1, 1000, 1200, 1, 'App\\Models\\EventConfiguration', 1);

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `adress`
  ADD PRIMARY KEY (`adress_uid`),
  ADD KEY `adress_person_person_uid_foreign` (`person_person_uid`),
  ADD KEY `adress_country_id_index` (`country_id`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`categoryID`);

ALTER TABLE `contactinformation`
  ADD PRIMARY KEY (`contactinformation_uid`),
  ADD KEY `contactinformation_person_person_uid_foreign` (`person_person_uid`);

ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`);

ALTER TABLE `eventconfigurations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evconf` (`eventconfiguration_type`,`eventconfiguration_id`);

ALTER TABLE `event_groups`
  ADD PRIMARY KEY (`eventgroup_uid`);

ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `optionals`
  ADD PRIMARY KEY (`optional_uid`),
  ADD KEY `optionals_productid_index` (`productID`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

ALTER TABLE `person`
  ADD PRIMARY KEY (`person_uid`),
  ADD KEY `person_registration_registration_uid_foreign` (`registration_registration_uid`);

ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`productID`),
  ADD KEY `products_categoryid_foreign` (`categoryID`);

ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_uid`);

ALTER TABLE `startnumberconfigs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `s_num_conf` (`startnumberconfig_type`,`startnumberconfig_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);


ALTER TABLE `categories`
  MODIFY `categoryID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `countries`
  MODIFY `country_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

ALTER TABLE `eventconfigurations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `products`
  MODIFY `productID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1011;

ALTER TABLE `startnumberconfigs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `adress`
  ADD CONSTRAINT `adress_person_person_uid_foreign` FOREIGN KEY (`person_person_uid`) REFERENCES `person` (`person_uid`) ON DELETE CASCADE;

ALTER TABLE `contactinformation`
  ADD CONSTRAINT `contactinformation_person_person_uid_foreign` FOREIGN KEY (`person_person_uid`) REFERENCES `person` (`person_uid`) ON DELETE CASCADE;

ALTER TABLE `optionals`
  ADD CONSTRAINT `optionals_productid_foreign` FOREIGN KEY (`productID`) REFERENCES `products` (`productID`) ON DELETE CASCADE;

ALTER TABLE `person`
  ADD CONSTRAINT `person_registration_registration_uid_foreign` FOREIGN KEY (`registration_registration_uid`) REFERENCES `registrations` (`registration_uid`) ON DELETE CASCADE;

ALTER TABLE `products`
  ADD CONSTRAINT `products_categoryid_foreign` FOREIGN KEY (`categoryID`) REFERENCES `categories` (`categoryID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;