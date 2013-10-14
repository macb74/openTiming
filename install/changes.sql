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
