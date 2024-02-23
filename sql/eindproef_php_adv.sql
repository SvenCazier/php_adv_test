-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 09, 2024 at 01:21 PM
-- Server version: 8.2.0
-- PHP Version: 8.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eindproef_php_adv`
--
CREATE DATABASE IF NOT EXISTS `eindproef_php_adv` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `eindproef_php_adv`;

-- --------------------------------------------------------

--
-- Table structure for table `bestellijnen`
--

DROP TABLE IF EXISTS `bestellijnen`;
CREATE TABLE IF NOT EXISTS `bestellijnen` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `bestelId` int UNSIGNED NOT NULL,
  `pizzaId` int NOT NULL,
  `aantal` int NOT NULL,
  `bestelPrijs` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bestellijnen_extra`
--

DROP TABLE IF EXISTS `bestellijnen_extra`;
CREATE TABLE IF NOT EXISTS `bestellijnen_extra` (
  `bestelLijnId` int UNSIGNED NOT NULL,
  `extraId` int UNSIGNED NOT NULL,
  `bestelPrijs` float NOT NULL,
  UNIQUE KEY `bestelLijnId` (`bestelLijnId`,`extraId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bestellingen`
--

DROP TABLE IF EXISTS `bestellingen`;
CREATE TABLE IF NOT EXISTS `bestellingen` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `klantId` int UNSIGNED NOT NULL,
  `datum` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `koerierInfo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `klanten`
--

DROP TABLE IF EXISTS `klanten`;
CREATE TABLE IF NOT EXISTS `klanten` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `naam` varchar(50) NOT NULL,
  `voornaam` varchar(50) NOT NULL,
  `straat` varchar(50) NOT NULL,
  `huisnummer` varchar(10) NOT NULL,
  `plaatsId` int NOT NULL,
  `telefoon` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `wachtwoord` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `rechtOpPromotie` tinyint(1) NOT NULL DEFAULT '0',
  `virtual_email` varchar(255) GENERATED ALWAYS AS (if((`email` is null),_utf8mb4'null',`email`)) VIRTUAL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `klantgegevens` (`naam`,`voornaam`,`straat`,`huisnummer`,`plaatsId`,`telefoon`,`virtual_email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `klanten`
--

INSERT INTO `klanten` (`id`, `naam`, `voornaam`, `straat`, `huisnummer`, `plaatsId`, `telefoon`, `email`, `wachtwoord`, `rechtOpPromotie`) VALUES
(1, 'Janssens', 'Sofie', 'Diestsestraat', '13', 1, '0477123456', 'gebruiker1@email.com', '$2y$10$Hm28RD2/O9fAmWfnf/s1F.e56LUXh/pWyvLnm/ONIyOfHVatQf46W', 0),
(2, 'De Rudder', 'Tom', 'Groenstraat', '42', 2, '0488987654', 'gebruiker2@email.com', '$2y$10$/m2DWNcB6S6wWzpv5J1tbePJesNQDR9nx9IRJ6LiI9DUr6v6nACfS', 1),
(3, 'Vermeulen', 'Anouk', 'Gemeentestraat', '7', 3, '0475111222', 'gebruiker3@email.com', '$2y$10$46V8aGSeskyjtkAqIlc2vOEzxwYlnab31QjQjyc.4bqj9/OUFTK5K', 0),
(4, 'Van Dyck', 'Maarten', 'Aarschotsesteenweg', '28', 4, '0499333444', 'gebruiker4@email.com', '$2y$10$U.h7YixchxkGyEdT09q7Iujj6z2HLbLffoW10x/CZT1BDkK8ERs/q', 1),
(5, 'Wouters', 'Liesbeth', 'Casinolaan', '19', 5, '0488555666', 'gebruiker5@email.com', '$2y$10$ruBAAxB9dOe/5Z3RZNGBU.Oq2vPLAbUpUo/uItrNdTUtrrnzsjjye', 0),
(6, 'Claes', 'Kevin', 'Waversebaan', '5', 2, '0477777888', 'gebruiker6@email.com', '$2y$10$HNDLoEZxms/zdGnv9LP/euO7aDICm87J03PLlE/adiZi8COrQMeSa', 0),
(7, 'Peeters', 'Lisa', 'Martelarenlaan', '32', 3, '016999000', 'gebruiker7@email.com', '$2y$10$kpGtuXcmNJGvBDEW4sIzk.9DTKYjv0S03RVc0G1J7gDEB0RGXoKm.', 1),
(8, 'Jacobs', 'Pieter', 'Baron Descampslaan', '14', 5, '0488222333', 'gebruiker8@email.com', '$2y$10$LahXF0rb1Yaeol.TVDzzy.10F5qz7buFAjwPvIZ7ztlDw0sLE6nuq', 0),
(9, 'Vandenbergh', 'Emma', 'Weggevoerdenstraat', '23', 4, '0477444555', 'gebruiker9@email.com', '$2y$10$rKhDuFmB1cQrVN4u1K6o9.wag57Y.iZpCX9df7/fUQkREBmOdv9K2', 1),
(10, 'Vandenberghe', 'Bram', 'Naamsestraat', '110', 1, '016666777', 'gebruiker10@email.com', '$2y$10$FJOsw7QqjPUKp7fL4sCI.uAYhJE1vj4odiwy3MISXdxlMSfmFOdYy', 0);

-- --------------------------------------------------------

--
-- Table structure for table `plaatsen`
--

DROP TABLE IF EXISTS `plaatsen`;
CREATE TABLE IF NOT EXISTS `plaatsen` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `postcode` int NOT NULL,
  `woonplaats` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plaatsen`
--

INSERT INTO `plaatsen` (`id`, `postcode`, `woonplaats`) VALUES
(1, 3000, 'Leuven'),
(2, 3001, 'Heverlee'),
(3, 3010, 'Kessel-Lo'),
(4, 3012, 'Wilsele'),
(5, 3018, 'Wijgmaal');

-- --------------------------------------------------------

--
-- Table structure for table `producten`
--

DROP TABLE IF EXISTS `producten`;
CREATE TABLE IF NOT EXISTS `producten` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint UNSIGNED NOT NULL,
  `naam` varchar(20) NOT NULL,
  `omschrijving` varchar(255) NOT NULL,
  `prijs` float NOT NULL,
  `promotiePct` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `productnaam` (`naam`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `producten`
--

INSERT INTO `producten` (`id`, `type`, `naam`, `omschrijving`, `prijs`, `promotiePct`) VALUES
(1, 1, 'Margherita', 'Tomatensaus, mozzarella, basilicum', 8.5, 0.05),
(2, 1, 'Pepperoni', 'Tomatensaus, mozzarella, pepperoni', 9.5, 0),
(3, 1, 'Vegetariana', 'Tomatensaus, mozzarella, champignons, paprika, ui', 10, 0),
(4, 1, 'Quattro Formaggi', 'Tomatensaus, mozzarella, gorgonzola, parmezaanse kaas, emmentaler', 11.5, 0),
(5, 1, 'Hawaii', 'Tomatensaus, mozzarella, ham, ananas', 10.5, 0.2),
(6, 1, 'Capricciosa', 'Tomatensaus, mozzarella, ham, champignons, artisjok', 11, 0),
(7, 1, 'Calzone', 'Gesloten pizza met tomatensaus, mozzarella, ham, champignons', 12, 0),
(8, 1, 'Frutti di Mare', 'Tomatensaus, mozzarella, zeevruchten, knoflook', 13.5, 0.1),
(9, 1, 'Diavola', 'Tomatensaus, mozzarella, pikante salami, rode peper', 11.5, 0),
(10, 1, 'Quattro Stagioni', 'Tomatensaus, mozzarella, ham, champignons, artisjok, olijven', 12.5, 0),
(11, 2, 'Extra kaas', 'Extra mozzarella', 1.5, 0),
(12, 2, 'Peperoncini', 'Pittige rode pepers', 1, 0),
(13, 2, 'Olijven', 'Zwarte olijven', 1.2, 0.2),
(14, 2, 'Ui', 'Verse gesneden ui', 0.8, 0),
(15, 2, 'Champignons', 'Verse champignons', 1, 0),
(16, 2, 'Ananas', 'Verse ananasstukjes', 1.2, 0),
(17, 2, 'Salami', 'Italiaanse salami', 1.5, 0),
(18, 2, 'Artisjok', 'Verse artisjokharten', 1.5, 0.2),
(19, 2, 'Knoflook', 'Verse knoflook', 0.8, 0),
(20, 2, 'Ham', 'Verse hamreepjes', 1.2, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
