-- 01.03.2011

ALTER TABLE `lauf` ADD `rundenrennen` INT NOT NULL AFTER `uDefinition`;

ALTER TABLE `teilnehmer` ADD `runden` INT NOT NULL;

ALTER TABLE `teilnehmer` CHANGE `jahrgang` `jahrgang` INT( 4 ) NOT NULL;

ALTER TABLE `teilnehmer` ADD `aut_runden` INT NOT NULL DEFAULT '0', ADD `man_runden` INT NOT NULL DEFAULT '0';

ALTER TABLE `lauf` CHANGE `rundenrennen` `rundenrennen` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `teilnehmer` CHANGE `runden` `runden` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `teilnehmer` CHANGE `aut_runden` `aut_runden` INT( 11 ) NOT NULL DEFAULT '0' COMMENT 'automatisch ermittelte Runden aus der Zeit Tabelle';

-- 17.04.2011
ALTER TABLE `lauf` ADD `use_lID` INT NOT NULL AFTER `rundenrennen`;

-- 18.07.2011
ALTER TABLE `lauf` ADD `teamrennen` INT NOT NULL DEFAULT '0' AFTER `use_lID`;

-- 10.08.2011
ALTER TABLE `lauf` ADD `rdVorgabe` INT NOT NULL DEFAULT '0' AFTER `teamrennen`;

-- 10.11.2011
ALTER TABLE `lauf` ADD `lockRace` INT NOT NULL DEFAULT '0' AFTER `rdVorgabe`;

-- 18.11.2011
ALTER TABLE `teilnehmer` ADD `vklasse` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `vtime`;
ALTER TABLE `lauf` ADD `vklasse` INT NOT NULL DEFAULT '0' AFTER `klasse`;

-- 21.11.2011
ALTER TABLE `lauf` ADD `showLogo` INT NOT NULL DEFAULT '1' AFTER `lockRace`;

-- 04.02.2012
ALTER TABLE `zeit` ADD `Reader` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `lauf` ADD `mainReaderIp` VARCHAR( 16 ) NOT NULL AFTER `showLogo`;

-- 17.05.2012
ALTER TABLE `teilnehmer` ADD `att` VARCHAR( 2 ) NOT NULL AFTER `verein` 

-- 14.10.2013
ALTER TABLE `zeit` ADD `millisecond` INT( 3 ) NOT NULL DEFAULT '0' AFTER `zeit` 
ALTER TABLE `teilnehmer` ADD `millisecond` INT( 3 ) NOT NULL DEFAULT '0' AFTER `zeit`

-- 24.10.2013
ALTER TABLE `zeit` ADD `zeit_tmp` DATETIME NOT NULL AFTER `nummer`;
update zeit set zeit_tmp = concat(DATE(timestamp), ' ',zeit);
ALTER TABLE `zeit` DROP `zeit`;
ALTER TABLE `zeit` CHANGE `zeit_tmp` `zeit` DATETIME NOT NULL;

-- 21.10.2015
ALTER TABLE `lauf` ADD `start_tmp` DATETIME NOT NULL AFTER `start`;
UPDATE lauf as l
	inner join veranstaltung as v on v.id = l.vid
SET l.start_tmp = concat(v.datum, ' ', l.start);
ALTER TABLE `lauf` DROP `start`;
ALTER TABLE `lauf` CHANGE `start_tmp` `start` DATETIME NOT NULL;

-- 20.02.2016
ALTER TABLE `zeit` ADD `del` INT NOT NULL DEFAULT '0';

-- 04.03.2016
ALTER TABLE `zeit` ADD `ant` VARCHAR( 4 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0';
ALTER TABLE `zeit` ADD `rssi` INT NOT NULL DEFAULT '0';

-- 18.09.2016
CREATE TABLE IF NOT EXISTS `config` (
`ID` int( 11 ) NOT NULL AUTO_INCREMENT ,
`key` varchar( 255 ) DEFAULT NULL ,
`value` varchar( 255 ) DEFAULT NULL ,
PRIMARY KEY ( `ID` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT =1

-- 19.09.2016

CREATE TABLE IF NOT EXISTS `zeit_tmp` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `vID` int(11) NOT NULL DEFAULT '0',
  `lID` int(11) NOT NULL DEFAULT '0',
  `nummer` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `zeit` datetime NOT NULL,
  `millisecond` int(3) NOT NULL DEFAULT '0',
  `TIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Reader` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `del` int(11) NOT NULL DEFAULT '0',
  `ant` int(4) unsigned zerofill NOT NULL DEFAULT '0000',
  `rssi` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `vID` (`vID`,`lID`,`nummer`,`zeit`,`millisecond`,`Reader`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT INTO zeit_tmp SELECT * FROM zeit ON DUPLICATE KEY UPDATE `zeit_tmp`.`ID`=`zeit_tmp`.`ID`;
DROP TABLE `zeit`;
ALTER TABLE zeit_tmp RENAME `zeit`;

-- 03.10.2016
ALTER TABLE `teilnehmer` CHANGE `att` `att` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;

-- 27.09.2017
ALTER TABLE `veranstaltung` ADD `sonderwertung` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `untertitel`;

CREATE TABLE `specialReporting` (
  `vid` int(11) NOT NULL,
  `uid` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `zeit` time NOT NULL,
  `TIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 09.10.2017
CREATE TABLE IF NOT EXISTS `chat` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 15.10.2017
ALTER TABLE `lauf` ADD `teamAtt` INT NOT NULL AFTER `mainReaderIp`, ADD `teamAttVal` VARCHAR(20) NOT NULL AFTER `teamAtt`;

-- 17.10.2017
ALTER TABLE `lauf` ADD `roc` INT NOT NULL AFTER `teamAttVal`;
UPDATE `lauf` SET `roc` = 0;

-- 23.10.2017
ALTER TABLE `lauf` ADD `teamTogetherWith` VARCHAR(20) NOT NULL AFTER `roc`;
ALTER TABLE `teilnehmer` CHANGE `vnummer` `vnummer` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Eindeutige Mannschaftsnummer';
ALTER TABLE `lauf` ADD `teamTogetherWithDeaktivated` INT(1) NOT NULL DEFAULT '0' AFTER `teamTogetherWith`;

-- 24.10.2017
ALTER TABLE `lauf` CHANGE `teamTogetherWithDeaktivated` `teamDeaktivated` INT(1) NOT NULL DEFAULT '0';

-- 09.11.2017
CREATE TABLE `verein_ort` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `verein` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ort` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE `ort` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ort` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `plz` int(11) NOT NULL,
  `lon` decimal(11,8) NOT NULL,
  `lat` decimal(11,8) NOT NULL,
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ort` (`ort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

ALTER TABLE `config` ADD `value_txt` TEXT NOT NULL AFTER `value`;
