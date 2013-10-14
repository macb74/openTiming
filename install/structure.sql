-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 14. Okt 2013 um 18:51
-- Server Version: 5.5.16
-- PHP-Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `myrun_test`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `klasse`
--

CREATE TABLE IF NOT EXISTS `klasse` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `klasse_data`
--

CREATE TABLE IF NOT EXISTS `klasse_data` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `kID` int(11) NOT NULL,
  `name` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `geschlecht` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `altervon` int(11) NOT NULL,
  `alterbis` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=167 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lauf`
--

CREATE TABLE IF NOT EXISTS `lauf` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `vID` int(11) NOT NULL,
  `titel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `untertitel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start` time NOT NULL,
  `ende` time NOT NULL,
  `klasse` int(11) NOT NULL,
  `vklasse` int(11) NOT NULL DEFAULT '0',
  `aktuell` int(1) NOT NULL,
  `team_anz` int(11) NOT NULL DEFAULT '0',
  `team_sex` int(11) NOT NULL,
  `uTemplate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uDefinition` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rundenrennen` int(11) NOT NULL DEFAULT '0',
  `use_lID` int(11) NOT NULL,
  `teamrennen` int(11) NOT NULL DEFAULT '0',
  `rdVorgabe` int(11) NOT NULL DEFAULT '0',
  `lockRace` int(11) NOT NULL DEFAULT '0',
  `showLogo` int(11) NOT NULL DEFAULT '1',
  `mainReaderIp` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `aktualisierung` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=71 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `teilnehmer`
--

CREATE TABLE IF NOT EXISTS `teilnehmer` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `vID` int(11) NOT NULL,
  `lID` int(11) NOT NULL,
  `disq` int(1) NOT NULL DEFAULT '0',
  `del` int(1) NOT NULL DEFAULT '0',
  `titel` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `vorname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nachname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `jahrgang` int(4) NOT NULL,
  `geschlecht` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `klasse` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `nation` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ort` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `stnr` int(11) NOT NULL,
  `verein` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `att` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `chip` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `zeit` time NOT NULL DEFAULT '00:00:00',
  `millisecond` int(3) NOT NULL DEFAULT '0',
  `manzeit` time NOT NULL DEFAULT '00:00:00',
  `useManTime` int(1) NOT NULL DEFAULT '0',
  `platz` int(11) NOT NULL,
  `akplatz` int(11) NOT NULL,
  `vplatz` int(11) NOT NULL COMMENT 'Platz in der Vereinswertung',
  `vnummer` varchar(11) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Eindeutige Mannschaftsnummer',
  `mplatz` int(11) NOT NULL COMMENT 'Platz innerhalb der Manschaft',
  `vtime` time NOT NULL COMMENT 'Zeit der Manschaftswertung',
  `vklasse` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `runden` int(11) NOT NULL DEFAULT '0',
  `aut_runden` int(11) NOT NULL DEFAULT '0' COMMENT 'automatisch ermittelte Runden aus der Zeit Tabelle',
  `man_runden` int(11) NOT NULL DEFAULT '0',
  `meisterschaft` int(11) NOT NULL,
  `ms_platz` int(11) NOT NULL COMMENT 'Platz in der Meisterschaftswertung',
  `ma_akplatz` int(11) NOT NULL COMMENT 'Klassenplatzierung in der Meisterschaftswertung',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `teilnehmer` (`vID`,`lID`,`stnr`,`del`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5899 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `veranstaltung`
--

CREATE TABLE IF NOT EXISTS `veranstaltung` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `untertitel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `datum` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `zeit`
--

CREATE TABLE IF NOT EXISTS `zeit` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `vID` int(11) NOT NULL DEFAULT '0',
  `lID` int(11) NOT NULL DEFAULT '0',
  `nummer` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `zeit` time NOT NULL,
  `millisecond` int(3) NOT NULL DEFAULT '0',
  `TIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Reader` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2709 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
