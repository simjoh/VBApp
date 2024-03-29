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