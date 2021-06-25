-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 25. Jun 2021 um 15:17
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
(13, 'js', 'js@js', '$2y$10$N80C6B/3v1/pIwq4.PyOOOPwxrYetOcXWNQhzWKz/fIk4/gt0DhgS', '2021/06/22'),
(14, 'a', 'a@a', '$2y$10$GU3XmWTXWQxWAJcmujRIZecNnybHs6JaZxaXzLy5W/mS41zNY4oia', '2021/06/22'),
(15, 'Thing', 'Thing@t', '$2y$10$O67vANwa3YXU25i9kir6GOmN1gkVEm0SlTNcVrKuqeQPyx8rxQ7e.', NULL),
(16, 'Bliblablub', 'p@g', '$2y$10$f4vU4DhWGxtSrg/do65FJ.dFAFNCMDRpY5rYMzKvNymW3zr6yKcB6', NULL),
(17, 'Loki123', 'philip.f.koschnitzki@gmail.com', '$2y$10$PZ3KHZlM3aUIk.0ymcvCKeIjFU3uQcO6wlX89A1S6iwE59GHkiDa.', '2021/06/24');

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
(7, 'Arzt'),
(8, 'Arzthelfer'),
(9, 'Arzt'),
(10, 'Arzthelfer'),
(11, 'Arzthelfer'),
(12, 'Sekretär'),
(13, 'Arzt'),
(17, 'Arzt');

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
  `day` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `appointment`
--

INSERT INTO `appointment` (`id`, `userID`, `treatmentID`, `roomID`, `start`, `end`, `status`, `day`) VALUES
(2, 1, 1, 1, '12:45:00', '13:00:00', 'wahrgenommen', '2020-12-12'),
(3, 3, 2, 2, '12:45:00', '13:00:00', 'wahrgenommen', '2020-12-12'),
(4, 5, 3, 3, '12:45:00', '13:00:00', 'wahrgenommen', '2020-12-12'),
(5, NULL, 3, 1, '12:45:00', '13:00:00', 'warten', '2020-12-12');

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
-- Tabellenstruktur für Tabelle `appointment_typical`
--

CREATE TABLE `appointment_typical` (
  `id` int(11) NOT NULL,
  `treatment` int(11) DEFAULT NULL,
  `day` varchar(32) NOT NULL,
  `endTime` time NOT NULL,
  `startTime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `appointment_typical`
--

INSERT INTO `appointment_typical` (`id`, `treatment`, `day`, `endTime`, `startTime`) VALUES
(5, 2, 'Montag', '15:00:00', '12:00:00'),
(6, 1, 'Montag', '14:00:00', '12:00:00'),
(7, 4, 'Montag', '12:00:00', '11:00:00'),
(8, 3, 'Montag', '10:00:00', '08:00:00');

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
(7, 13, 'c88d3a38a227d4d30250820927c194ea12ba64ad3b56a84432', '2021-Jun-Mon'),
(8, 13, '5cc39f45c8a0ce81d65a61ebfb7faa802b880d90d3f5b7672b', '2021/06/22 16:36:21'),
(9, 14, '1af4ad953405f3d1c106b17a508de44d8f0709b02cd2a863af', '2021/06/22 16:39:08'),
(10, 15, '4f04c517856edd77c43d89504c9a31c4dd449f12c64595b3ac', '2021/06/23 19:22:32'),
(11, 15, '916dc5c233ab803b0f07c4e120e354570ee445cc29e53d7a51', '2021/06/23 19:23:30'),
(12, 16, '019b9386fb43d79e5ca008b72dab71fbbe967d319268bb7a7e', '2021/06/24 09:44:43'),
(13, 17, '86ac73eff58d384f55d5a3966c0404c236a45d1822d18dbc11', '2021/06/24 16:26:58'),
(14, 17, '6eefa33ab90bfde13b94adfa8a2e34d80385223f5a832ab8e9', '2021/06/24 16:27:17');

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

--
-- Daten für Tabelle `opening_times`
--

INSERT INTO `opening_times` (`id`, `day`, `opening`, `closing`) VALUES
(1, 'Montag', '09:00:00', '12:00:00'),
(2, 'Montag', '13:00:00', '16:00:00'),
(3, 'Dienstag', '08:00:00', '12:00:00'),
(4, 'Dienstag', '13:00:00', '14:00:00');

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
(4, 13, '450cb58a88079efc0e1ccb5ef9e3b0aaf77f93953b7049f000', '2021/06/22 14:40:47', '2021/06/22 15:33:29', NULL),
(5, 13, 'db0a3ac554f39f7c6c28bf7088b79d3d89181d1275cea04712', '2021/06/22 15:33:43', NULL, '::1'),
(6, 13, 'e2ca51ac373f53c01a8fbff37be5b7c72d234472813527b171', '2021/06/22 15:37:28', '2021/06/22 16:19:10', '::1'),
(7, 13, '562878df89fdfd1c787f32edf26189c66b992bce6b1520790e', '2021/06/22 15:49:10', '2021/06/22 16:42:35', '::1'),
(8, 13, '831710f6d8c4b652de8e7326a4d6036de53a6232af0f2c97c1', '2021/06/22 16:12:35', '2021/06/22 16:44:03', '::1'),
(9, 13, '1431d48af53f85baeb201d386805fa3007137c1c5737913bf3', '2021/06/22 16:14:03', '2021/06/22 16:46:42', '::1'),
(10, 13, '5a82b4f262fc5db96427f60f93f9c1b443788e0f57b8a5d837', '2021/06/22 16:16:42', '2021/06/22 16:47:07', '::1'),
(11, 13, 'f3f62fece83d125e6b65d0c477894bb6c7d09bad35daf3fa4e', '2021/06/22 16:17:07', '2021/06/22 16:47:20', '::1'),
(12, 13, 'ac21f2ed9b08ae7c4df4afe02856dd22e38490ee6c32026b50', '2021/06/22 16:17:20', '2021/06/22 16:48:48', '::1'),
(13, 13, '868f507f92d186db3c20e03d62581d821455089072c4c82a06', '2021/06/22 16:18:48', '2021/06/22 16:49:00', '::1'),
(14, 13, 'bbecdbbe0cc89a341bf75b264eea82e8af5cefc45e08b5b75d', '2021/06/22 16:19:00', '2021/06/22 16:49:38', '::1'),
(15, 13, '86101fa1d1b76c89398f6aff6ca3d9429961877cd51c38181a', '2021/06/22 16:19:38', '2021/06/22 17:04:07', '::1'),
(16, 13, 'dfe3cad85ca1a11537cff345a80ec561941ed5f17f3e2d39bc', '2021/06/22 16:34:07', '2021/06/22 17:06:41', '::1'),
(17, 13, '2241c6f23fe64916ac9ced43731deaf3946408261faf5c78aa', '2021/06/22 16:36:41', '2021/06/22 16:38:31', '::1'),
(18, 14, 'd40d9af513bd519735789c1576adf3bf30cdff610e8d56d389', '2021/06/22 16:39:08', '2021/06/22 17:12:36', '::1'),
(19, 14, 'c63c5f0c5de68c6cdc848977383135b621227f0996cb67a31b', '2021/06/22 16:42:36', '2021/06/22 16:44:19', '::1'),
(20, 1, 'ac67f5ce17be47668c9642a772b19b2fc3020a70b8dea3967a', '2021/06/23 19:10:23', '2021/06/23 19:20:32', '::1'),
(21, 14, '43a9449e682643e79526a6db034f0924f6fc64f72672b1b90d', '2021/06/23 19:20:41', '2021/06/23 19:21:33', '::1'),
(22, 1, 'db32182f2eee6a7a45d3326b6978e795f648a54a4d9d87e0fe', '2021/06/23 19:21:42', '2021/06/23 19:21:47', '::1'),
(23, 15, '9365fa31f764f55839158924c3fc714b1e7dfc87234541d113', '2021/06/23 19:22:32', '2021/06/23 19:52:42', '::1'),
(24, 15, '26d65d151e927c979c5552c29a0e3e3fc2e5e8047fa287ae6b', '2021/06/23 19:22:42', '2021/06/23 19:53:20', '::1'),
(25, 15, '0175b285dc91741d40218adb141eb9e1e9dfeff315e6da8277', '2021/06/23 19:23:20', '2021/06/23 19:38:20', '::1'),
(26, 15, 'eb6eed3f456c6fc0199f2782303975af2dd107a9e98872ba39', '2021/06/23 19:44:26', '2021/06/23 19:44:29', '::1'),
(27, 14, '0bb696a181eef6eebddbeb602f2dac52912e1d63ac818e5995', '2021/06/23 19:44:35', '2021/06/23 19:51:09', '::1'),
(28, 13, '768b7631e2f7783abfcff9578449a891ea270410bb8497a5b8', '2021/06/23 19:51:19', '2021/06/23 21:45:22', '::1'),
(29, 16, '22ab03fdca492de3902242ec8c2d91eafce6752b0fb997e9b1', '2021/06/24 09:44:43', '2021/06/24 10:14:51', '::1'),
(30, 16, '9ba5a8d05338871469e2fe2d882229d08f155553c03e450d21', '2021/06/24 09:44:51', '2021/06/24 10:14:51', '::1'),
(31, 17, '2face22798cd84fbaed325b8acf0629eea30e58b8c99923aec', '2021/06/24 16:26:58', '2021/06/24 16:57:25', '::1'),
(32, 17, '07d8f2f30c37c8eb01b5208a63f0acf1167ba31663790c3bec', '2021/06/24 16:27:25', '2021/06/24 16:57:39', '::1'),
(33, 17, '75a2c5f0da68211ad5377ff8e1287bb88f485bc7346cd0dfd9', '2021/06/24 16:27:39', '2021/06/24 17:29:33', '::1'),
(34, 17, '86561a33620179c5aefb06fbb944b8ad0b585993cb1d3554ad', '2021/06/24 19:22:09', NULL, '::1'),
(35, 13, '2702f3ef55dee945f6424548ef7ee0935b91db5494cb9e8f47', '2021/06/24 21:55:35', NULL, '::1');

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
(13, 'Julius', 'Schuchert', 'Thing', 'gesetzlich', '2004-03-10', NULL),
(14, 'a', 'a', 'Mann', 'a', '2021-06-22', ''),
(15, 'Thing', 'Thing', 'Thing', 'gesetzlich', '2021-06-18', ''),
(16, 'Philip', 'Koschn', 'keine Angabe', 'privat', '2000-05-16', ''),
(17, 'Philip', 'Koschnitzki', 'Mann', 'Gesetzlich', '2003-05-16', '');

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
(1, 13, 'Montag', '08:00', '13:00');

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
-- Daten für Tabelle `workhoursblock`
--

INSERT INTO `workhoursblock` (`id`, `patientID`, `day`, `start`, `end`, `isBlock`) VALUES
(1, 13, '2021-06-28', '08:00:00', '13:00:00', 1),
(2, 13, '2021-06-30', '08:00:00', '18:00:00', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `appointment_typical`
--
ALTER TABLE `appointment_typical`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `notapproved`
--
ALTER TABLE `notapproved`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `opening_times`
--
ALTER TABLE `opening_times`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT für Tabelle `treatment`
--
ALTER TABLE `treatment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT für Tabelle `workhours`
--
ALTER TABLE `workhours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `workhoursblock`
--
ALTER TABLE `workhoursblock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
