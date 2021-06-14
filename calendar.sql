-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 14. Jun 2021 um 21:02
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
(13, 'benjamin.ilchmann', 'benjamin.ilchmann@gmail.com', '$2y$10$VpILIoyMRbxXfjZrz6zYHeHeKvHz8n/MI0/sWWLS9gk7wpto33nYS', '2021-Jun-M');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin`
--

CREATE TABLE `admin` (
  `userID` int(11) NOT NULL,
  `role` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `appointment_admin`
--

CREATE TABLE `appointment_admin` (
  `appointmentID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `session`
--

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `start` varchar(30) DEFAULT NULL,
  `end` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `session`
--

INSERT INTO `session` (`id`, `userID`, `token`, `start`, `end`) VALUES
(1, 1, '13c45e794fbc49093bd8d01c9ffbb77c0e8d2285c45cc9f02b', '0000-00-00 00:00:00', NULL),
(2, 1, '338c0a3fe7533eca5b89c6cdf451a8ef3ebf16a9a63312b049', '0000-00-00 00:00:00', NULL),
(3, 13, '2743cd3eb90b4efcba049a46782d6143fb1d6100ee66310cd7', '2021-Jun-Mon 20:Jun:th', NULL);

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
(1, 'David', 'Ilchmann', 'Hr', 'a', '0000-00-00', ''),
(2, 'David', 'Ilchmann', 'Hr', 'a', '0000-00-00', ''),
(3, 'David', 'Ilchmann', 'Hr', 'a', '0000-00-00', ''),
(4, 'David', 'Ilchmann', 'Hr', 'a', '0000-00-00', ''),
(5, 'David', 'Ilchmann', 'Hr', 'a', '0000-00-00', ''),
(6, 'David', 'Ilchmann', 'Hr', 'a', '0000-00-00', ''),
(7, 'David', 'Ilchmann', 'a', 'a', '0000-00-00', ''),
(8, 'David', 'Ilchmann', 'a', 'a', '0000-00-00', ''),
(9, 'David', 'Ilchmann', 'Hr', 'a', '0000-00-00', ''),
(10, 'David', 'Ilchmann', 'Hr', 'a', '0000-00-00', ''),
(11, 'David', 'Ilchmann', 'Hr', 'a', '0000-00-00', ''),
(12, 'David', 'Ilchmann', 'Hr', 'a', '0000-00-00', ''),
(13, 'Benjamin', 'Ilchmann', 'Herr', 'Privat', '30.05.2005', '');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`);

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
  ADD KEY `userID` (`userID`);

--
-- Indizes für die Tabelle `passwordreset`
--
ALTER TABLE `passwordreset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`);

--
-- Indizes für die Tabelle `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_ibfk_1` (`userID`);

--
-- Indizes für die Tabelle `treatment`
--
ALTER TABLE `treatment`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `notapproved`
--
ALTER TABLE `notapproved`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `passwordreset`
--
ALTER TABLE `passwordreset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `treatment`
--
ALTER TABLE `treatment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
