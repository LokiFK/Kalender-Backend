-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 22. Jun 2021 um 15:31
-- Server-Version: 10.4.19-MariaDB
-- PHP-Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `calendar`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `account`
--

CREATE TABLE `account` (
  `userID` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `createdAt` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `account`
--

INSERT INTO `account` (`userID`, `username`, `email`, `password`, `createdAt`) VALUES
(1, 'davidilchmann', 'david.ilchmann@gmail.com', '$2y$10$d7ajFCIz.fnS9VY4AcEfoeEK/lqm8HAzEu4LKWbP0ZsGuLgC44Wsq', '0000-00-00'),
(2, 'ralphmueller', 'ralphmueller@yahoo.de', 'aergrHtn347ghn', '2010-12-14'),
(3, 'gustav56', 'g.stresemann@gmx.de', 'weimarrepublik123', '2012-05-05'),
(4, 'WGREENLAND', 'greenland-wolfgang@gmail.com', 'passwort123456', '2019-04-23'),
(5, 'malko', 'dara07.malkova@gmail.com', 'bnrovla492!)fn', '2020-08-04'),
(6, 'juliaf', 'juliafaust@gmx.net', 'hergo49ujoo', '2021-05-03'),
(13, 'benjamin.ilchmann', 'benjamin.ilchmann@gmail.com', '$2y$10$W5h8wd.vsB/WeGbYjuuyw./wzHNBVw8VX3mcgTlu9DDvCTCq/xKpa', '2021-06-11');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin`
--

CREATE TABLE `admin` (
  `userID` int(11) NOT NULL,
  `role` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `admin`
--

INSERT INTO `admin` (`userID`, `role`) VALUES
(7, 1),
(8, 2),
(9, 1),
(10, 2),
(11, 2),
(12, 0),
(13, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `appointment`
--

CREATE TABLE `appointment` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `treatmentID` int(11) DEFAULT NULL,
  `roomID` int(11) DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `appointment`
--

INSERT INTO `appointment` (`id`, `userID`, `treatmentID`, `roomID`, `start`, `end`, `status`) VALUES
(2, 1, 1, 1, '2020-12-28 16:00:00', '2020-12-28 16:30:00', 'completed'),
(3, 3, 2, 2, '2021-06-21 12:00:00', '2021-06-21 12:15:00', 'completed'),
(4, 5, 3, 3, '2021-06-21 12:00:00', '2021-06-21 13:00:00', 'completed'),
(5, NULL, 3, 1, '2021-06-22 12:00:00', '2021-06-22 12:00:15', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `appointment_admin`
--

CREATE TABLE `appointment_admin` (
  `appointmentID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `appointment_admin`
--

INSERT INTO `appointment_admin` (`appointmentID`, `adminID`) VALUES
(2, 9),
(2, 10),
(3, 9),
(4, 7),
(4, 8),
(4, 10),
(4, 11),
(4, 13);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notapproved`
--

CREATE TABLE `notapproved` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `datetime` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `notapproved`
--

INSERT INTO `notapproved` (`id`, `userID`, `code`, `datetime`) VALUES
(1, 1, 'a00ecfa70e895fda7a5404b18d07f495a29468bbb17763ad37', '0000-00-00 00:00:00'),
(2, 1, '49d9700726d2e06b4044d38e1680e923a79648324813c68632', '0000-00-00 00:00:00'),
(3, 1, '44cfa3c1bd1e6f4cc9d23f421e8cd9c55976765bebad344c95', '0000-00-00 00:00:00'),
(4, 1, 'eb8b7d5853e63b5dae57502ffaf52f012fbe4a97b5ed3b93a4', '0000-00-00 00:00:00'),
(5, 1, 'e460cb48332a234d7590238f79b5956fa0c13221a7a797f088', '0000-00-00 00:00:00'),
(6, 1, 'ec52a4eea248d2db70957b7830a748a176bf8cc082c72077d6', '0000-00-00 00:00:00'),
(7, 13, 'c88d3a38a227d4d30250820927c194ea12ba64ad3b56a84432', '2021-Jun-Mon');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `passwordreset`
--

CREATE TABLE `passwordreset` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `isUsed` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `passwordreset`
--

INSERT INTO `passwordreset` (`id`, `userID`, `code`, `datetime`, `isUsed`) VALUES
(1, 13, 'a5202c5184df220a5c68e74aa0ebddd454abd429215f4485e4', '2021-06-22 14:40:03', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `room`
--

INSERT INTO `room` (`id`, `number`) VALUES
(1, '1'),
(2, '1a'),
(3, '1b'),
(4, '1c'),
(5, '2'),
(6, '2a'),
(7, '2b'),
(8, '2c');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `session`
--

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `start` varchar(30) DEFAULT NULL,
  `end` varchar(30) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `session`
--

INSERT INTO `session` (`id`, `userID`, `token`, `start`, `end`, `ip`) VALUES
(1, 1, '13c45e794fbc49093bd8d01c9ffbb77c0e8d2285c45cc9f02b', '0000-00-00 00:00:00', NULL, NULL),
(2, 1, '338c0a3fe7533eca5b89c6cdf451a8ef3ebf16a9a63312b049', '0000-00-00 00:00:00', NULL, NULL),
(3, 13, '2743cd3eb90b4efcba049a46782d6143fb1d6100ee66310cd7', '2021-Jun-Mon 20:Jun:th', NULL, NULL),
(4, 13, '450cb58a88079efc0e1ccb5ef9e3b0aaf77f93953b7049f000', '2021/06/22 14:40:47', '2021/06/22 15:56:00', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `treatment`
--

CREATE TABLE `treatment` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `nrDoctors` int(11) DEFAULT NULL,
  `nrNurses` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `treatment`
--

INSERT INTO `treatment` (`id`, `name`, `duration`, `nrDoctors`, `nrNurses`) VALUES
(1, 'Vorsorgeuntersuchung', 30, 1, 1),
(2, 'Sprechstunde', 15, 1, 0),
(3, 'Operation', 60, 2, 3),
(4, 'Untersuchung', 30, 1, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `salutation` varchar(20) DEFAULT NULL,
  `insurance` varchar(20) DEFAULT NULL,
  `birthday` varchar(10) DEFAULT NULL,
  `patientID` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `salutation`, `insurance`, `birthday`, `patientID`) VALUES
(1, 'David', 'Ilchmann', 'Herr', 'gesetzlich', '0000-00-00', ''),
(2, 'Ralph', 'Müller', 'Herr', 'gesetzlich', '1989-05-20', ''),
(3, 'Gustav ', 'Stresemann', 'Herr', 'privat', '1956-8-12', ''),
(4, 'Wolfgang', 'Greenland', 'Herr', 'gesetzlich', '2000-04-09', ''),
(5, 'Dara', 'Malokova', 'Frau', 'gesetzlich', '1999-07-26', ''),
(6, 'Julia', 'Faust', 'Frau', 'gesetzlich', '1997-05-01', ''),
(7, 'Barbara', 'Lochmann', 'Frau', 'gesetzlich', '2001-09-11', ''),
(8, 'David', 'Ramonoff', 'Herr', 'privat', '1980-01-03', ''),
(9, 'Sergej', 'Mustermann', 'Herr', 'privat', '1998-05-03', ''),
(10, 'Nina', 'Peterson', 'Frau', 'gesetzlich', '2001-08-05', ''),
(11, 'Ben', 'Dietrich', 'Herr', 'gesetzlich', '1987-06-27', ''),
(12, 'Kevin', 'Schlauberg', 'Herr', 'gesetzlich', '2000-03-25', ''),
(13, 'Benjamin', 'Ackermann', 'Herr', 'privat', '2005-05-30', '');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`,`email`),
  ADD KEY `email` (`email`);

--
-- Indizes für die Tabelle `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`userID`);

--
-- Indizes für die Tabelle `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`),
  ADD KEY `treatmentID` (`treatmentID`),
  ADD KEY `roomID` (`roomID`);

--
-- Indizes für die Tabelle `appointment_admin`
--
ALTER TABLE `appointment_admin`
  ADD PRIMARY KEY (`appointmentID`,`adminID`),
  ADD KEY `adminID` (`adminID`);

--
-- Indizes für die Tabelle `notapproved`
--
ALTER TABLE `notapproved`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `userID_2` (`userID`);

--
-- Indizes für die Tabelle `passwordreset`
--
ALTER TABLE `passwordreset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `userID` (`userID`);

--
-- Indizes für die Tabelle `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `number` (`number`);

--
-- Indizes für die Tabelle `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `session_ibfk_1` (`userID`);

--
-- Indizes für die Tabelle `treatment`
--
ALTER TABLE `treatment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `notapproved`
--
ALTER TABLE `notapproved`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `passwordreset`
--
ALTER TABLE `passwordreset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `treatment`
--
ALTER TABLE `treatment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `account`
--
ALTER TABLE `account`
  ADD CONSTRAINT `account_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`treatmentID`) REFERENCES `treatment` (`id`),
  ADD CONSTRAINT `appointment_ibfk_3` FOREIGN KEY (`roomID`) REFERENCES `room` (`id`);

--
-- Constraints der Tabelle `appointment_admin`
--
ALTER TABLE `appointment_admin`
  ADD CONSTRAINT `appointment_admin_ibfk_1` FOREIGN KEY (`appointmentID`) REFERENCES `appointment` (`id`),
  ADD CONSTRAINT `appointment_admin_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `admin` (`userID`);

--
-- Constraints der Tabelle `notapproved`
--
ALTER TABLE `notapproved`
  ADD CONSTRAINT `notapproved_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `passwordreset`
--
ALTER TABLE `passwordreset`
  ADD CONSTRAINT `passwordreset_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `session_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
