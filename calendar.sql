-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 26. Jun 2021 um 00:37
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
(1, 'davidilchmann', 'david.ilchmann@gmail.com', '$2y$10$m4GteRzGuH.jTOyq2cqNYup3hiZF0etiP6sEuWFOKtcs6f8BcPbHW', '2021/06/25'),
(2, 'beatefalter', 'beate.falter@gmail.com', '$2y$10$rvyfzQfBNi8ODkzOxY3aKebtqQ19Kv5Dx4XnZ18l2f4kel79NJcra', '2021/06/25'),
(3, 'andreashäuser', 'andreas.haeuser@gmail.com', '$2y$10$zn9RuMZ44MsKTMT7N8inrOPJlfsmFYX2LjzKb2IWyjIMQUWPajv2O', '2021/06/25'),
(4, 'petersilie05', 'peter.silie@gmail.com', '$2y$10$mes7Bat0iROLxwS8eTHCmOZVqQik8jd.J2SCpf1spiusIGFfJvrLC', '2021/06/25');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin`
--

CREATE TABLE `admin` (
  `userID` int(11) NOT NULL,
  `role` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `admin`
--

INSERT INTO `admin` (`userID`, `role`) VALUES
(1, 'Arzt'),
(2, 'Arzt'),
(3, 'Arzthelfer');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `appointment`
--

CREATE TABLE `appointment` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `treatmentID` int(11) DEFAULT NULL,
  `roomID` int(11) DEFAULT NULL,
  `start` varchar(8) DEFAULT NULL,
  `end` varchar(8) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `day` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `appointment`
--

INSERT INTO `appointment` (`id`, `userID`, `treatmentID`, `roomID`, `start`, `end`, `status`, `day`) VALUES
(1, 4, 3, 1, '08:00:00', '08:15:00', 'bestätigt', '2021-06-28'),
(2, NULL, 3, 1, '10:00:00', '10:15:00', 'warten', '2021-06-28'),
(3, NULL, 3, 1, '08:00:00', '08:15:00', 'warten', '2021-07-06'),
(4, NULL, 3, 1, '08:15:00', '08:30:00', 'warten', '2021-07-06'),
(5, NULL, 3, 1, '08:30:00', '08:45:00', 'warten', '2021-07-06'),
(6, NULL, 3, 1, '08:45:00', '09:00:00', 'warten', '2021-07-06');

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
(2, 2),
(2, 3),
(3, 2),
(3, 3),
(4, 2),
(4, 3),
(5, 2),
(5, 3),
(6, 2),
(6, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `appointment_typical`
--

CREATE TABLE `appointment_typical` (
  `id` int(11) NOT NULL,
  `treatment` int(11) DEFAULT NULL,
  `day` varchar(32) NOT NULL,
  `endTime` time NOT NULL,
  `startTime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(1, 1, '54039f6f0a9cfd10c8bf0f25e35ae5a67673673cb0e612c052', '2021/06/25 23:16:58'),
(2, 2, '6e66d423c6e6d0ae7b3ae2568b603720285eab61ee92f7ff79', '2021/06/25 23:27:22'),
(3, 3, '4229b91f8e00383bfe8994f036678d9ebb2d377908fa20d26a', '2021/06/25 23:28:55'),
(4, 4, 'd6f81cf79f7f64960dbb8ada7d35bffe5c54b844285d980a84', '2021/06/25 23:33:20');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `opening_times`
--

CREATE TABLE `opening_times` (
  `id` int(11) NOT NULL,
  `day` varchar(32) DEFAULT NULL,
  `opening` time NOT NULL,
  `closing` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

--
-- Daten für Tabelle `room`
--

INSERT INTO `room` (`id`, `number`) VALUES
(1, '1'),
(2, '2'),
(3, '3'),
(4, '4');

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
(1, 1, '0178b1687f87dac3d63179f7f883fe11817bba7cadd104fb03', '2021/06/25 23:16:58', '2021/06/25 23:47:05', '::1'),
(2, 1, '2a6db89d89e84fe9372d1e93c2d6b0f3355fc5937d759504f6', '2021/06/25 23:17:05', '2021/06/25 23:26:25', '::1'),
(3, 2, 'c6dd031f3e3adde5a82fd7c167ecbf1e6186206723a0859646', '2021/06/25 23:27:22', '2021/06/25 23:57:27', '::1'),
(4, 2, '5e9ab93cc06792ef09da6d536b9b2c03153b6a831fe1bc99cd', '2021/06/25 23:27:27', '2021/06/25 23:28:03', '::1'),
(5, 3, 'ea59f928980cc06faae34c8b78db69956a311bd907c50055df', '2021/06/25 23:28:55', '2021/06/25 23:59:01', '::1'),
(6, 3, '0fd024a66ef3cf73a93e93440db0d3feff51d99702426fdabf', '2021/06/25 23:29:01', '2021/06/25 23:32:23', '::1'),
(7, 4, '5157821496fc0476e7a011cded40a1dcd41d566983f19be295', '2021/06/25 23:33:20', '2021/06/26 00:03:25', '::1'),
(8, 4, '48e393d07a4186bac0e86c965b2ebd895a1b016aa442d16be6', '2021/06/25 23:33:25', '2021/06/25 23:33:34', '::1'),
(9, 1, '81aeff37976cddff6fcf0d39b053869796910205cd8c363b0f', '2021/06/25 23:33:51', '2021/06/26 00:07:35', '::1'),
(10, 4, 'b1ce1e081e76c6eac76f9d34e57643e1b092dc37c344c291d0', '2021/06/26 00:08:07', '2021/06/26 00:08:16', '::1'),
(11, 1, '7ae5e574804be0174158c486a3eec546d0d1e5cba1126f2651', '2021/06/26 00:08:25', '2021/06/26 00:09:29', '::1'),
(12, 2, '3b844057639ea2a322d8bfb815fb2f7ad75e86cdabaed74a31', '2021/06/26 00:09:39', '2021/06/26 00:11:37', '::1'),
(13, 4, 'c70853c258cc2fc64173470947c8a5e30542ba18c787fb1a14', '2021/06/26 00:11:47', '2021/06/26 00:12:29', '::1'),
(14, 1, 'c29f23f8cc4253c045c4e4dfe12d1fafa22434478e5a0f8411', '2021/06/26 00:12:43', '2021/06/26 00:16:14', '::1'),
(15, 2, '7331084d512c037f14dd7e083751ae31b2774f9a3d93a9955f', '2021/06/26 00:16:27', NULL, '::1');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `treatment`
--

CREATE TABLE `treatment` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `nrDoctors` int(11) DEFAULT NULL,
  `nrNurses` int(11) DEFAULT NULL,
  `duration` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `treatment`
--

INSERT INTO `treatment` (`id`, `name`, `nrDoctors`, `nrNurses`, `duration`) VALUES
(1, 'Untersuchung', 1, 2, '00:00:30'),
(2, 'Belastungs EKG', 2, 4, '00:00:45'),
(3, 'Vorsorgung', 1, 1, '00:00:15');

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
(1, 'David', 'Ilchmann', 'Mann', 'Privat', '2003-10-18', ''),
(2, 'Beate', 'Falter', 'Frau', 'Privat', '1984-07-13', ''),
(3, 'Andreas', 'Häuser', 'Mann', 'Gesetzlich', '1967-04-12', ''),
(4, 'Peter', 'Silie', 'Mann', 'Gesetzlich', '2005-03-12', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `workhours`
--

CREATE TABLE `workhours` (
  `id` int(11) NOT NULL,
  `patientID` int(11) DEFAULT NULL,
  `day` varchar(20) DEFAULT NULL,
  `start` varchar(8) DEFAULT NULL,
  `end` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `workhours`
--

INSERT INTO `workhours` (`id`, `patientID`, `day`, `start`, `end`) VALUES
(1, 1, 'Montag', '08:00', '17:00'),
(2, 1, 'Dienstag', '08:00', '17:00'),
(3, 1, 'Mittwoch', '08:00', '13:00'),
(4, 1, 'Mittwoch', '15:00', '18:00'),
(5, 1, 'Donnerstag', '08:00', '17:00'),
(6, 1, 'Freitag', '08:00', '15:00'),
(7, 1, 'Samstag', '12:00', '15:00'),
(8, 2, 'Montag', '08:00', '17:00'),
(9, 3, 'Montag', '08:00', '17:00'),
(10, 2, 'Dienstag', '08:00', '17:00'),
(11, 3, 'Dienstag', '08:00', '17:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `workhoursblock`
--

CREATE TABLE `workhoursblock` (
  `id` int(11) NOT NULL,
  `patientID` int(11) DEFAULT NULL,
  `day` varchar(20) DEFAULT NULL,
  `start` varchar(8) DEFAULT NULL,
  `end` varchar(8) DEFAULT NULL,
  `isBlock` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Indizes für die Tabelle `appointment_typical`
--
ALTER TABLE `appointment_typical`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_typical_treatment_id_fk` (`treatment`);

--
-- Indizes für die Tabelle `notapproved`
--
ALTER TABLE `notapproved`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `userID_2` (`userID`);

--
-- Indizes für die Tabelle `opening_times`
--
ALTER TABLE `opening_times`
  ADD PRIMARY KEY (`id`);

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
-- Indizes für die Tabelle `workhours`
--
ALTER TABLE `workhours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patientID` (`patientID`);

--
-- Indizes für die Tabelle `workhoursblock`
--
ALTER TABLE `workhoursblock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patientID` (`patientID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `appointment_typical`
--
ALTER TABLE `appointment_typical`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `notapproved`
--
ALTER TABLE `notapproved`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `opening_times`
--
ALTER TABLE `opening_times`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `passwordreset`
--
ALTER TABLE `passwordreset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT für Tabelle `treatment`
--
ALTER TABLE `treatment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `workhours`
--
ALTER TABLE `workhours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT für Tabelle `workhoursblock`
--
ALTER TABLE `workhoursblock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Constraints der Tabelle `appointment_typical`
--
ALTER TABLE `appointment_typical`
  ADD CONSTRAINT `appointment_typical_treatment_id_fk` FOREIGN KEY (`treatment`) REFERENCES `treatment` (`id`);

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

--
-- Constraints der Tabelle `workhours`
--
ALTER TABLE `workhours`
  ADD CONSTRAINT `workhours_ibfk_1` FOREIGN KEY (`patientID`) REFERENCES `admin` (`userID`);

--
-- Constraints der Tabelle `workhoursblock`
--
ALTER TABLE `workhoursblock`
  ADD CONSTRAINT `workhoursblock_ibfk_1` FOREIGN KEY (`patientID`) REFERENCES `admin` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
