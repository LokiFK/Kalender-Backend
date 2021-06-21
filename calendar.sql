-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 21. Jun 2021 um 09:15
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
(13, 'benjamin.ilchmann', 'benjamin.ilchmann@gmail.com', '$2y$10$VpILIoyMRbxXfjZrz6zYHeHeKvHz8n/MI0/sWWLS9gk7wpto33nYS', '2021-Jun-M'),
(14, 't', 'e', '$2y$10$4C0bqQ1a/wUnPtnsO7jlQ..saY.sBiniV2Imnx/3b0fMBEm/U473y', '2021-06-16'),
(15, 'js', 'js@js', '$2y$10$d7ajFCIz.fnS9VY4AcEfoeEK/lqm8HAzEu4LKWbP0ZsGuLgC44Wsq', '2021-06-18'),
(16, 'test', 'test@test.de', '$2y$10$gwe3GWl2v8iG8Z41zeRVw.DCClYAvbvfrmJUubN4.AdzHKATwY9Pa', '2021-Jun-S'),
(21, 'a', 'a', '$2y$10$/dTGntDwaXjmqLkWgL93EeXAf8KT3lc7RqysnrZ05dMpdRE0rkWYq', '2021-Jun-S');

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
(15, 0);

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
(1, 21, 1, NULL, '2021-06-20 19:51:14', '2021-06-20 19:51:14', 'in Work');

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
(7, 13, 'c88d3a38a227d4d30250820927c194ea12ba64ad3b56a84432', '2021-Jun-Mon'),
(8, 14, '24b581ce7a8e6f1079db1f27d9f9fb4b271faa7d21fc03e3ed', '2021-Jun-Wed'),
(9, 16, 'fb3ed549b5dbc158390ca190363c1443a9c14b0898a4dd2c25', '2021/06/19 06:17:08pm'),
(11, 21, '6c72168d86552367304a3b038f9fa989d6f9b9a6c13e3164c8', '2021/06/19 07:10:56pm'),
(12, 21, '581772d33e4cf82148464bd95e2a09ca8f583ff01282d8afee', '2021/06/19 07:11:19pm');

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
(1, 21, '82aed33a3c1f63d0b03a5b6631ced23d3009ad817d115180a9', '2021-06-19 08:34:58', 0);

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
(3, 13, '2743cd3eb90b4efcba049a46782d6143fb1d6100ee66310cd7', '2021-Jun-Mon 20:Jun:th', NULL),
(4, 1, 'ed5a748f90d4a5f7e3f9dc6a0724b0163ef56f0328239d459b', '2021-Jun-Tue 11:Jun:th', NULL),
(5, 1, '3b58d0ee36ea5e99d0f0385f10e504a254ab43607a016854af', '2021-Jun-Tue 12:Jun:th', NULL),
(6, 1, '18b691cea03261cd45cc9bcd72e39fccb99c4d774ed9d85f6c', '2021-Jun-Tue 12:Jun:th', NULL),
(7, 1, '307913330b9a3ee8d8ead9d4f60e9d5cbcd6db647a745b56eb', '2021-Jun-Tue 13:Jun:th', '2021-Jun-Tue 19:Jun:th'),
(8, 1, '53f6807a3d09cbcb1c368efc9b63ef72c32fd5ae003b615209', '2021-Jun-Tue 19:Jun:th', '2021-Jun-Tue 19:Jun:th'),
(9, 1, '76591907761269e083864b9618696d7d26ca488dce6b2996e8', '2021-Jun-Tue 19:Jun:th', '2021-Jun-Tue 19:Jun:th'),
(10, 1, '79cebcc63ca9b4bc07612ddf28ead71dec02201ba0c3854401', '2021-Jun-Tue 23:Jun:th', '2021-Jun-Wed 20:Jun:th'),
(11, 14, 'e1ca46e88edf2bf398dd839675e3e630685913c311a348237d', '2021-Jun-Wed 20:Jun:th', '2021-Jun-Wed 21:Jun:th'),
(12, 14, '233af710d788c30ac3f3b63a78e18a9b79a738bcb7fd143ba9', '2021-Jun-Wed 20:Jun:th', '2021-Jun-Wed 21:Jun:th'),
(13, 1, '626c918e3e5b00f352944878fbe02c129220700cdfa7acb07f', '2021/06/18 03:31:50pm', '2021/06/18 05:02:30pm'),
(14, 1, '322b1f3e2b386ffaa960680e5bc4b876ffdb09470a45f978de', '2021/06/18 04:32:39pm', '2021/06/18 05:02:39pm'),
(15, 1, '7d64b283b8776cb7aca10cad3b05f077fada597d2ab1d5c5f4', '2021/06/18 04:36:45pm', '2021/06/18 05:30:50pm'),
(16, 1, 'b86bf4ccdaeee242ba4bc3ad7a48051c247d4c6bb6e3df41c9', '2021/06/18 07:17:38pm', '2021/06/18 07:56:58pm'),
(17, 1, '29d85e9d2776036538339c0d8d876decb765f98655e112408b', '2021/06/18 07:26:59pm', '2021/06/18 07:57:16pm'),
(18, 1, 'bca13009a4aedb3f9cca6e56cbe5ca4def1a41014f0f6916b6', '2021/06/18 07:27:16pm', '2021/06/18 07:38:35pm'),
(19, 15, '68ba5077ffc07279d79b19cc75720a697641e492e661002e40', '2021/06/18 07:38:48pm', '2021/06/18 08:11:22pm'),
(20, 15, 'daf4d7c17be95b6f2585f326bca82dee737a0dbc26c86db988', '2021/06/18 08:14:13pm', '2021/06/18 08:15:59pm'),
(21, 15, '5a41516798972a91efae5a4090332dfae03abc65e0e7f614ec', '2021/06/18 08:16:09pm', '2021/06/18 08:46:09pm'),
(22, 16, '25807ceeabf17a10e16602c88046bedc11e46603c3340796bf', '2021/06/19 06:19:59pm', '2021/06/19 06:20:02pm'),
(23, 16, '3b286c9c43022006254862986dd2abbb1d6365db51036e5f7d', '2021/06/19 06:22:58pm', '2021/06/19 06:23:01pm'),
(24, 21, '044a23fb7a715bb9bce0cff7cb0b1cfeea884f8da9fa64f7da', '2021/06/19 07:10:56pm', '2021/06/19 07:42:15pm'),
(25, 21, '7e9d5748619de50704d9d6813242f3ce33f0a10e991ed05a8a', '2021/06/19 07:12:15pm', '2021/06/19 07:42:49pm'),
(26, 21, '85df3d325a3ad4976917cbc94f9ba83c9212b6ef47caa8fff4', '2021/06/19 07:53:29pm', '2021/06/19 08:23:29pm'),
(27, 21, '009d09b1320dcfb3deed92162a328b03320da5e5d0a91f1c45', '2021/06/19 07:53:35pm', '2021/06/19 08:23:35pm'),
(28, 21, '25c9916e5b27d351f93d36c062733ac4f3f283c0e163d69af2', '2021/06/19 07:54:29pm', '2021/06/19 08:24:29pm'),
(29, 21, '14e8b0f37965988726df5bbc7b826a210e0f152e599b3616d2', '2021/06/19 07:54:58pm', '2021/06/19 08:24:58pm'),
(30, 1, 'a093f211b4d8a489cfc4a30513e3d420ff6cd450aa354e2109', '2021/06/19 07:55:31pm', '2021/06/19 08:25:31pm'),
(31, 1, '43200e51c88e7942d02672e2dad6c00e018c03bd2fb64c7f1e', '2021/06/19 07:56:42pm', '2021/06/19 08:26:42pm'),
(32, 21, '3300b7ef56a0429f48266a0cb7e8bc7509a1b9731d78ac208e', '2021/06/19 07:57:33pm', '2021/06/19 07:59:48pm'),
(33, 21, '0093882dd1df72c2e6bc3dbf2034289a8af63b96a5ae09c2b1', '2021/06/19 08:35:15pm', '2021/06/19 08:35:19pm'),
(34, 21, '0dbc643c589939ef552f6b6c6a3e46601304c5ba023155a5de', '2021/06/19 08:35:24pm', '2021/06/19 09:05:34pm'),
(35, 21, '4f95481c561eb9adc6a0a69125b0d9516c1606b069d81821c4', '2021/06/21 08:55:49', '2021/06/21 09:44:35');

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
(1, 'b1', 15, 1, 1),
(2, 'b2', 10, 0, NULL);

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
(13, 'Benjamin', 'Ilchmann', 'Herr', 'Privat', '30.05.2005', ''),
(14, 'b', 'c', 'a', 'g', '5', ''),
(15, 'J', 'S', 'Herr', 'g.', '10.03.2004', NULL),
(16, 'V', 'N', 'Es', 'g', '10.03.2004', ''),
(21, 'a', 'a', 'a', 'a', 'a', '');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `notapproved`
--
ALTER TABLE `notapproved`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `passwordreset`
--
ALTER TABLE `passwordreset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT für Tabelle `treatment`
--
ALTER TABLE `treatment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
