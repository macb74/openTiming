-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 12. Nov 2017 um 14:31
-- Server-Version: 10.1.19-MariaDB
-- PHP-Version: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `myrun`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat`
--

CREATE TABLE `chat` (
  `ID` int(11) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `config`
--

CREATE TABLE `config` (
  `ID` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value_txt` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `klasse`
--

CREATE TABLE `klasse` (
  `ID` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `klasse_data`
--

CREATE TABLE `klasse_data` (
  `ID` int(11) NOT NULL,
  `kID` int(11) NOT NULL,
  `name` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `geschlecht` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `altervon` int(11) NOT NULL,
  `alterbis` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lauf`
--

CREATE TABLE `lauf` (
  `ID` int(11) NOT NULL,
  `vID` int(11) NOT NULL,
  `titel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `untertitel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start` datetime NOT NULL,
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
  `teamAtt` int(11) NOT NULL,
  `teamAttVal` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `roc` int(11) NOT NULL,
  `teamTogetherWith` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `teamDeaktivated` int(1) NOT NULL DEFAULT '0',
  `aktualisierung` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ort`
--

CREATE TABLE `ort` (
  `ID` int(11) NOT NULL,
  `ort` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plz` int(11) NOT NULL,
  `lon` decimal(11,8) NOT NULL,
  `lat` decimal(11,8) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `specialReporting`
--

CREATE TABLE `specialReporting` (
  `vid` int(11) NOT NULL,
  `uid` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `zeit` time NOT NULL,
  `TIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `teilnehmer`
--

CREATE TABLE `teilnehmer` (
  `ID` bigint(20) NOT NULL,
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
  `att` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `chip` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `zeit` time NOT NULL DEFAULT '00:00:00',
  `millisecond` int(3) NOT NULL DEFAULT '0',
  `manzeit` time NOT NULL DEFAULT '00:00:00',
  `useManTime` int(1) NOT NULL DEFAULT '0',
  `platz` int(11) NOT NULL,
  `akplatz` int(11) NOT NULL,
  `vplatz` int(11) NOT NULL COMMENT 'Platz in der Vereinswertung',
  `vnummer` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Eindeutige Mannschaftsnummer',
  `mplatz` int(11) NOT NULL COMMENT 'Platz innerhalb der Manschaft',
  `vtime` time NOT NULL COMMENT 'Zeit der Manschaftswertung',
  `vklasse` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `runden` int(11) NOT NULL DEFAULT '0',
  `aut_runden` int(11) NOT NULL DEFAULT '0' COMMENT 'automatisch ermittelte Runden aus der Zeit Tabelle',
  `man_runden` int(11) NOT NULL DEFAULT '0',
  `meisterschaft` int(11) NOT NULL,
  `ms_platz` int(11) NOT NULL COMMENT 'Platz in der Meisterschaftswertung',
  `ma_akplatz` int(11) NOT NULL COMMENT 'Klassenplatzierung in der Meisterschaftswertung'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `veranstaltung`
--

CREATE TABLE `veranstaltung` (
  `ID` int(11) NOT NULL,
  `titel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `untertitel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sonderwertung` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `datum` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `verein_ort`
--

CREATE TABLE `verein_ort` (
  `ID` int(11) NOT NULL,
  `verein` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ort` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `zeit`
--

CREATE TABLE `zeit` (
  `ID` int(11) NOT NULL,
  `vID` int(11) NOT NULL DEFAULT '0',
  `lID` int(11) NOT NULL DEFAULT '0',
  `nummer` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `zeit` datetime NOT NULL,
  `millisecond` int(3) NOT NULL DEFAULT '0',
  `TIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Reader` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `del` int(11) NOT NULL DEFAULT '0',
  `ant` int(4) UNSIGNED ZEROFILL NOT NULL DEFAULT '0000',
  `rssi` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `klasse`
--
ALTER TABLE `klasse`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `klasse_data`
--
ALTER TABLE `klasse_data`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `lauf`
--
ALTER TABLE `lauf`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `ort`
--
ALTER TABLE `ort`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ort` (`ort`);

--
-- Indizes für die Tabelle `teilnehmer`
--
ALTER TABLE `teilnehmer`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `teilnehmer` (`vID`,`lID`,`stnr`,`del`);

--
-- Indizes für die Tabelle `veranstaltung`
--
ALTER TABLE `veranstaltung`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `verein_ort`
--
ALTER TABLE `verein_ort`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `zeit`
--
ALTER TABLE `zeit`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `vID` (`vID`,`lID`,`nummer`,`zeit`,`millisecond`,`Reader`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `chat`
--
ALTER TABLE `chat`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT für Tabelle `config`
--
ALTER TABLE `config`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT für Tabelle `klasse`
--
ALTER TABLE `klasse`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT für Tabelle `klasse_data`
--
ALTER TABLE `klasse_data`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;
--
-- AUTO_INCREMENT für Tabelle `lauf`
--
ALTER TABLE `lauf`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;
--
-- AUTO_INCREMENT für Tabelle `ort`
--
ALTER TABLE `ort`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT für Tabelle `teilnehmer`
--
ALTER TABLE `teilnehmer`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7924;
--
-- AUTO_INCREMENT für Tabelle `veranstaltung`
--
ALTER TABLE `veranstaltung`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT für Tabelle `verein_ort`
--
ALTER TABLE `verein_ort`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=383;
--
-- AUTO_INCREMENT für Tabelle `zeit`
--
ALTER TABLE `zeit`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8537;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
