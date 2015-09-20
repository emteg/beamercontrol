-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 19. Sep 2015 um 18:57
-- Server Version: 5.6.20
-- PHP-Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `infobeamer`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bildseite`
--

CREATE TABLE IF NOT EXISTS `bildseite` (
`Id` int(11) NOT NULL,
  `Extension` varchar(10) NOT NULL,
  `Beschriftung` text NOT NULL,
  `Layout` enum('Zweispaltig','Mittig') NOT NULL,
  `ZeigenAb` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ZeigenBis` datetime DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `einstellungen`
--

CREATE TABLE IF NOT EXISTS `einstellungen` (
  `Name` varchar(100) NOT NULL,
  `Wert` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `event`
--

CREATE TABLE IF NOT EXISTS `event` (
`Id` int(11) NOT NULL,
  `Titel` varchar(100) NOT NULL,
  `Beginn` datetime NOT NULL,
  `Ende` datetime DEFAULT NULL,
  `Kategorie` set('Allgemein','SdS') NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modul`
--

CREATE TABLE IF NOT EXISTS `modul` (
`Id` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `IstAktiv` tinyint(1) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `playlist`
--

CREATE TABLE IF NOT EXISTS `playlist` (
`Id` int(11) NOT NULL,
  `ModulId` int(11) NOT NULL,
  `Nummer` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `spiel`
--

CREATE TABLE IF NOT EXISTS `spiel` (
`Id` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Suche` set('ingame','ip','anders','') DEFAULT NULL,
  `Server` varchar(100) NOT NULL,
  `Zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Ip` varchar(50) NOT NULL,
  `Spieler` varchar(20) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `textseite`
--

CREATE TABLE IF NOT EXISTS `textseite` (
`Id` int(11) NOT NULL,
  `Inhalt` text NOT NULL,
  `IstAktiv` tinyint(1) NOT NULL DEFAULT '1',
  `ZeigenAb` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ZeigenBis` datetime DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `passwort` varchar(100) NOT NULL,
  `istAktiviert` tinyint(1) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bildseite`
--
ALTER TABLE `bildseite`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `einstellungen`
--
ALTER TABLE `einstellungen`
 ADD PRIMARY KEY (`Name`), ADD UNIQUE KEY `Key` (`Name`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `modul`
--
ALTER TABLE `modul`
 ADD PRIMARY KEY (`Id`), ADD UNIQUE KEY `Name` (`Name`);

--
-- Indexes for table `playlist`
--
ALTER TABLE `playlist`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `spiel`
--
ALTER TABLE `spiel`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `textseite`
--
ALTER TABLE `textseite`
 ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bildseite`
--
ALTER TABLE `bildseite`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=99;
--
-- AUTO_INCREMENT for table `modul`
--
ALTER TABLE `modul`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `playlist`
--
ALTER TABLE `playlist`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=83;
--
-- AUTO_INCREMENT for table `spiel`
--
ALTER TABLE `spiel`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `textseite`
--
ALTER TABLE `textseite`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=94;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
