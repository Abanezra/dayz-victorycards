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

-- Exportiere Struktur von Tabelle dayz.targets
CREATE TABLE IF NOT EXISTS `targets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(56) DEFAULT NULL,
  `targettype_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `TargetTargettypeID` (`targettype_id`),
  CONSTRAINT `TargetTargettypeID` FOREIGN KEY (`targettype_id`) REFERENCES `targettypes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle dayz.targets: ~38 rows (ungefähr)
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
  `done` int(11) DEFAULT 3,
  PRIMARY KEY (`id`),
  KEY `VictoryPlayerID` (`player_id`),
  KEY `VictoryVictoryconditionID` (`victorycondition_id`),
  CONSTRAINT `VictoryPlayerID` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `VictoryVictoryconditionID` FOREIGN KEY (`victorycondition_id`) REFERENCES `victoryconditions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle dayz.victorycards: ~0 rows (ungefähr)
DELETE FROM `victorycards`;

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

-- Exportiere Daten aus Tabelle dayz.victoryconditions: ~0 rows (ungefähr)
DELETE FROM `victoryconditions`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
