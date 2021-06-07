-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 04. Jun 2021 um 01:24
-- Server-Version: 10.1.21-MariaDB
-- PHP-Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `terminplanung`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `account`
--

CREATE TABLE `account` (
  `userid` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `erstellungsdatum` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin`
--

CREATE TABLE `admin` (
  `userid` int(11) NOT NULL,
  `role` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `behandlung`
--

CREATE TABLE `behandlung` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `anzahlaerzte` int(11) DEFAULT NULL,
  `anzahlarzthelfer` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notapproved`
--

CREATE TABLE `notapproved` (
  `id` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `passwordreset`
--

CREATE TABLE `passwordreset` (
  `id` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `used` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `raum`
--

CREATE TABLE `raum` (
  `id` int(11) NOT NULL,
  `nummer` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `session`
--

CREATE TABLE `session` (
  `userid` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `termin`
--

CREATE TABLE `termin` (
  `id` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `behandlungsid` int(11) DEFAULT NULL,
  `raumid` int(11) DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `terminmitarbeiter`
--

CREATE TABLE `terminmitarbeiter` (
  `terminid` int(11) NOT NULL,
  `adminid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `vorname` varchar(50) DEFAULT NULL,
  `nachname` varchar(50) DEFAULT NULL,
  `anrede` varchar(20) DEFAULT NULL,
  `geburtstag` date DEFAULT NULL,
  `patientenid` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indizes für die Tabelle `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`userid`);

--
-- Indizes für die Tabelle `behandlung`
--
ALTER TABLE `behandlung`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `notapproved`
--
ALTER TABLE `notapproved`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`);

--
-- Indizes für die Tabelle `passwordreset`
--
ALTER TABLE `passwordreset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`);

--
-- Indizes für die Tabelle `raum`
--
ALTER TABLE `raum`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`token`);

--
-- Indizes für die Tabelle `termin`
--
ALTER TABLE `termin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`),
  ADD KEY `behandlungsid` (`behandlungsid`),
  ADD KEY `raumid` (`raumid`);

--
-- Indizes für die Tabelle `terminmitarbeiter`
--
ALTER TABLE `terminmitarbeiter`
  ADD PRIMARY KEY (`terminid`,`adminid`),
  ADD KEY `adminid` (`adminid`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `behandlung`
--
ALTER TABLE `behandlung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `notapproved`
--
ALTER TABLE `notapproved`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `passwordreset`
--
ALTER TABLE `passwordreset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `raum`
--
ALTER TABLE `raum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `termin`
--
ALTER TABLE `termin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `account`
--
ALTER TABLE `account`
  ADD CONSTRAINT `account_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`);

--
-- Constraints der Tabelle `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`);

--
-- Constraints der Tabelle `notapproved`
--
ALTER TABLE `notapproved`
  ADD CONSTRAINT `notapproved_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`);

--
-- Constraints der Tabelle `passwordreset`
--
ALTER TABLE `passwordreset`
  ADD CONSTRAINT `passwordreset_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`);

--
-- Constraints der Tabelle `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `session_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`);

--
-- Constraints der Tabelle `termin`
--
ALTER TABLE `termin`
  ADD CONSTRAINT `termin_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `termin_ibfk_2` FOREIGN KEY (`behandlungsid`) REFERENCES `behandlung` (`id`),
  ADD CONSTRAINT `termin_ibfk_3` FOREIGN KEY (`raumid`) REFERENCES `raum` (`id`);

--
-- Constraints der Tabelle `terminmitarbeiter`
--
ALTER TABLE `terminmitarbeiter`
  ADD CONSTRAINT `terminmitarbeiter_ibfk_1` FOREIGN KEY (`terminid`) REFERENCES `termin` (`id`),
  ADD CONSTRAINT `terminmitarbeiter_ibfk_2` FOREIGN KEY (`adminid`) REFERENCES `admin` (`userid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
