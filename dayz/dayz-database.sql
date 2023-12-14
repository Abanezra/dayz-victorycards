-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server-Version:               10.4.28-MariaDB - mariadb.org binary distribution
-- Server-Betriebssystem:        Win64
-- HeidiSQL Version:             12.5.0.6677
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Exportiere Datenbank-Struktur für dayz
CREATE DATABASE IF NOT EXISTS `dayz` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `dayz`;

-- Exportiere Struktur von Tabelle dayz.players
CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(56) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle dayz.players: ~4 rows (ungefähr)
DELETE FROM `players`;
INSERT INTO `players` (`id`, `name`, `password`) VALUES
	(9, 'Benedikt', '$2y$10$qPQW4gC2n80HL2MuTfhgtuNOhBwEmnh2Yry8p4QCMfHW4BrQSKQUW'),
	(10, 'admin', '$2y$10$afx9lGHu8tet3DFe6g0y/OL9VVlMAons92qrhV1okXYtYX7Aks/UW'),
	(11, 'Mario', '$2y$10$lS4pcOg.AuRGwGjRpMtPBeVv0o8/EbvkYjKXuXo/H4I6GRlS4n.dK'),
	(14, 'Luigi', '$2y$10$HCFiwc.f2MN5xuso.71nNeOaiPoXAJwY2AFpDVQBbVmSxInmN9YW2');

-- Exportiere Struktur von Tabelle dayz.seasonmembers
CREATE TABLE IF NOT EXISTS `seasonmembers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `season_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `SeasonPlayerID` (`player_id`),
  KEY `SeasonSeasonID` (`season_id`),
  CONSTRAINT `SeasonPlayerID` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `SeasonSeasonID` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle dayz.seasonmembers: ~1 rows (ungefähr)
DELETE FROM `seasonmembers`;

-- Exportiere Struktur von Tabelle dayz.seasons
CREATE TABLE IF NOT EXISTS `seasons` (
  `id` int(11) NOT NULL,
  `name` varchar(56) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle dayz.seasons: ~4 rows (ungefähr)
DELETE FROM `seasons`;
INSERT INTO `seasons` (`id`, `name`) VALUES
	(1, 'Season 1'),
	(2, 'Season 2'),
	(3, 'Season 3'),
	(4, 'Season 4');

-- Exportiere Struktur von Tabelle dayz.states
CREATE TABLE IF NOT EXISTS `states` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle dayz.states: ~4 rows (ungefähr)
DELETE FROM `states`;
INSERT INTO `states` (`id`, `name`) VALUES
	(0, 'Failed'),
	(1, 'Open'),
	(2, 'Done'),
	(3, '???');

-- Exportiere Struktur von Tabelle dayz.targets
CREATE TABLE IF NOT EXISTS `targets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(56) DEFAULT NULL,
  `targettype_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `TargetTargettypeID` (`targettype_id`),
  CONSTRAINT `TargetTargettypeID` FOREIGN KEY (`targettype_id`) REFERENCES `targettypes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle dayz.targets: ~35 rows (ungefähr)
DELETE FROM `targets`;
INSERT INTO `targets` (`id`, `name`, `targettype_id`) VALUES
	(1, 'Benedikt', 1),
	(2, 'Michael', 1),
	(3, 'Fabian', 1),
	(4, 'deer', 2),
	(6, 'Luigi', 1),
	(7, 'cow', 2),
	(8, 'goat', 2),
	(9, 'chicken', 2),
	(10, 'pig', 2),
	(11, 'sheep', 2),
	(12, 'wild boar', 2),
	(13, 'mouflon', 2),
	(14, 'hare', 2),
	(15, 'bear', 2),
	(16, 'wolf', 2),
	(17, 'carp', 2),
	(18, 'sardines', 2),
	(19, 'bitterlings', 2),
	(20, 'mackerel', 2),
	(21, 'civilian', 3),
	(22, 'worker', 3),
	(23, 'police', 3),
	(24, 'firefighter', 3),
	(25, 'military', 3),
	(26, 'NBC / ABC', 3),
	(27, 'ada', 6),
	(28, 'olga', 6),
	(29, 'gunter', 6),
	(30, 'sarka', 6),
	(31, 'humvee', 6),
	(32, 'm3s', 6),
	(33, 'firestation', 4),
	(34, 'policestation', 4),
	(35, 'castle', 4),
	(36, 'fishing rod', 5),
	(37, 'lure', 5),
	(38, 'hunting vest', 5),
	(39, 'hunting backpack', 5);

-- Exportiere Struktur von Tabelle dayz.targettypes
CREATE TABLE IF NOT EXISTS `targettypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(56) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle dayz.targettypes: ~6 rows (ungefähr)
DELETE FROM `targettypes`;
INSERT INTO `targettypes` (`id`, `name`) VALUES
	(1, 'player'),
	(2, 'animal'),
	(3, 'infected'),
	(4, 'location'),
	(5, 'item'),
	(6, 'vehicle');

-- Exportiere Struktur von Tabelle dayz.victorycards
CREATE TABLE IF NOT EXISTS `victorycards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(56) DEFAULT NULL,
  `player_id` int(11) NOT NULL,
  `victorycondition_id` varchar(50) DEFAULT NULL,
  `state_id` int(11) DEFAULT 3,
  PRIMARY KEY (`id`),
  KEY `VictoryPlayerID` (`player_id`),
  KEY `VictoryVictoryconditionID` (`victorycondition_id`),
  KEY `VictoryStateID` (`state_id`),
  CONSTRAINT `VictoryPlayerID` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `VictoryStateID` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `VictoryVictoryconditionID` FOREIGN KEY (`victorycondition_id`) REFERENCES `victoryconditions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle dayz.victorycards: ~38 rows (ungefähr)
DELETE FROM `victorycards`;
INSERT INTO `victorycards` (`id`, `code`, `player_id`, `victorycondition_id`, `state_id`) VALUES
	(76, '6579cd9ca954c', 9, '6579cd9ca91d0', 1),
	(77, '6579cd9f88f9e', 9, '6579cd9f887b7', 1),
	(78, '6579cdfa3c812', 9, '6579cdfa3c16b', 1),
	(79, '6579ce0e793d9', 9, '6579ce0e78875', 2),
	(80, '6579ce13b0652', 9, '6579ce13b00ef', 1),
	(81, '6579ce1459f3e', 9, '6579ce1459b1c', 1),
	(82, '6579ce14e144b', 9, '6579ce14e101f', 0),
	(83, '6579ce155e4f9', 9, '6579ce155e050', 1),
	(84, '6579ce15ba3d9', 9, '6579ce15b9edf', 3),
	(85, '6579ce161989c', 9, '6579ce1619485', 1),
	(86, '6579ce16852c1', 9, '6579ce1684887', 1),
	(87, '6579ce1807c6c', 9, '6579ce1806f35', 1),
	(88, '6579ce189eb93', 9, '6579ce189e638', 1),
	(89, '6579ce1940aad', 9, '6579ce194066f', 0),
	(90, '6579ce19f259e', 9, '6579ce19f21cb', 2),
	(91, '6579ce1a9bb4b', 9, '6579ce1a9b7bd', 0),
	(92, '6579ce20e6776', 9, '6579ce20e63b0', 1),
	(93, '6579ce34ec45b', 9, '6579ce34eb82a', 1),
	(94, '6579cf503c41b', 9, '6579cf503b889', 1),
	(95, '6579f37e51311', 9, '6579f37e50fbf', 1),
	(96, '6579f3820c1b9', 9, '6579f3820b92d', 3),
	(97, '6579f382d6635', 9, '6579f382d615b', 3),
	(98, '6579f415f0f58', 9, '6579f415f0b2e', 1),
	(99, '6579f416d1430', 9, '6579f416d0fa6', 1),
	(100, '6579f4176294c', 9, '6579f4176211c', 1),
	(101, '6579f4198e8dd', 9, '6579f4198e50a', 1),
	(102, '6579f41b74f5c', 9, '6579f41b744a3', 1),
	(103, '6579f41c0f536', 9, '6579f41c0f174', 1),
	(104, '6579f41c6844f', 9, '6579f41c6801b', 1),
	(105, '6579f41cdebb7', 9, '6579f41cde7fe', 1),
	(106, '6579f41d4392c', 9, '6579f41d42c0e', 1),
	(107, '6579f41d9a141', 9, '6579f41d99c68', 1),
	(108, '6579f41e3af14', 9, '6579f41e3a96b', 1),
	(109, '6579f41eaf7ef', 9, '6579f41eaf45c', 1),
	(110, '657ab3c09d10a', 9, '657ab3c09ca33', 1),
	(111, '657ab3c4675f9', 9, '657ab3c46690d', 1),
	(112, '657ab3c9293fc', 9, '657ab3c92866e', 1),
	(113, '657ab47f012c1', 9, '657ab47f0061d', 1),
	(114, '657ac9a989625', 9, '657ac9a988b5f', 1);

-- Exportiere Struktur von Tabelle dayz.victoryconditions
CREATE TABLE IF NOT EXISTS `victoryconditions` (
  `id` varchar(50) NOT NULL,
  `name` varchar(56) NOT NULL,
  `condition` varchar(1024) NOT NULL,
  `targettype_id` int(11) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `VictoryTargettypeID` (`targettype_id`),
  KEY `VictoryTargetID` (`target_id`),
  CONSTRAINT `VictoryTargetID` FOREIGN KEY (`target_id`) REFERENCES `targets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `VictoryTargettypeID` FOREIGN KEY (`targettype_id`) REFERENCES `targettypes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle dayz.victoryconditions: ~38 rows (ungefähr)
DELETE FROM `victoryconditions`;
INSERT INTO `victoryconditions` (`id`, `name`, `condition`, `targettype_id`, `target_id`, `amount`) VALUES
	('6579cd9ca91d0', 'Lootbrain', 'Get', 5, 39, 3),
	('6579cd9f887b7', 'Fight the Virus', 'Dispatch', 3, 23, 3),
	('6579cdfa3c16b', 'Recon', 'Visit', 4, 33, NULL),
	('6579ce0e78875', 'Lootbrain', 'Get', 5, 36, 3),
	('6579ce13b00ef', 'Lootbrain', 'Get', 5, 36, 3),
	('6579ce1459b1c', 'Lootbrain', 'Get', 5, 36, 2),
	('6579ce14e101f', 'Hunting Trip', 'Hunt', 2, 16, 1),
	('6579ce155e050', 'Driving Lesson', 'Drive', 6, 28, NULL),
	('6579ce15b9edf', 'Fight the Virus', 'Dispatch', 3, 24, 2),
	('6579ce1619485', 'Fight the Virus', 'Dispatch', 3, 24, 1),
	('6579ce1684887', 'Driving Lesson', 'Drive', 6, 28, NULL),
	('6579ce1806f35', 'Lootbrain', 'Get', 5, 39, 1),
	('6579ce189e638', 'Assassination', 'Kill', 1, 3, NULL),
	('6579ce194066f', 'Hunting Trip', 'Hunt', 2, 13, 2),
	('6579ce19f21cb', 'Hunting Trip', 'Hunt', 2, 8, 3),
	('6579ce1a9b7bd', 'Fight the Virus', 'Dispatch', 3, 26, 3),
	('6579ce20e63b0', 'Recon', 'Visit', 4, 35, NULL),
	('6579ce34eb82a', 'Assassination', 'Kill', 1, 2, NULL),
	('6579cf503b889', 'Recon', 'Visit', 4, 34, NULL),
	('6579f37e50fbf', 'Driving Lesson', 'Drive', 6, 31, NULL),
	('6579f3820b92d', 'Driving Lesson', 'Drive', 6, 29, NULL),
	('6579f382d615b', 'Fight the Virus', 'Dispatch', 3, 21, 1),
	('6579f415f0b2e', 'Hunting Trip', 'Hunt', 2, 15, 2),
	('6579f416d0fa6', 'Hunting Trip', 'Hunt', 2, 12, 2),
	('6579f4176211c', 'Hunting Trip', 'Hunt', 2, 13, 1),
	('6579f4198e50a', 'Recon', 'Visit', 4, 33, NULL),
	('6579f41b744a3', 'Assassination', 'Kill', 1, 2, NULL),
	('6579f41c0f174', 'Driving Lesson', 'Drive', 6, 27, NULL),
	('6579f41c6801b', 'Recon', 'Visit', 4, 33, NULL),
	('6579f41cde7fe', 'Assassination', 'Kill', 1, 6, NULL),
	('6579f41d42c0e', 'Assassination', 'Kill', 1, 1, NULL),
	('6579f41d99c68', 'Recon', 'Visit', 4, 33, NULL),
	('6579f41e3a96b', 'Recon', 'Visit', 4, 35, NULL),
	('6579f41eaf45c', 'Assassination', 'Kill', 1, 1, NULL),
	('657ab3c09ca33', 'Hunting Trip', 'Hunt', 2, 15, 2),
	('657ab3c46690d', 'Assassination', 'Kill', 1, 3, NULL),
	('657ab3c92866e', 'Assassination', 'Kill', 1, 6, NULL),
	('657ab47f0061d', 'Fight the Virus', 'Dispatch', 3, 24, 3),
	('657ac9a988b5f', 'Driving Lesson', 'Drive', 6, 28, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
