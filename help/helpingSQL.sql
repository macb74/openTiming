-- Vereine mit dem meisten Läufern im Ziel 
SELECT Verein, COUNT(id) anz 
 FROM teilnehmer 
 WHERE lid IN (74, 76) 
  AND Platz <> 0 
  AND Verein <> "" 
 GROUP BY Verein 
 ORDER BY anz DESC 
 LIMIT 0,10;
 

-- Export für Import in openTiming
SELECT 18, '', Startnummer, name, Vorname, Geschlecht, Jahrgang, Verein, Att, `Event` 
 FROM `marktlauf` 
 WHERE veranstaltung = '2016' 
 order by `Event`, Verein;
 
-- Anmeldungen pro Lauf
select count(*), event from marktlauf where veranstaltung = '2017' group by event 

-- Anmeldungen pro Lauf vergeleichen
select count(*), event, veranstaltung from marktlauf where veranstaltung = '2017' group by event
union all
select count(*), event, veranstaltung from marktlauf where veranstaltung = '2016' group by event order by event, veranstaltung;

-- alle zeiten der U10
SELECT * FROM `zeit` WHERE nummer in (SELECT stnr from teilnehmer where (klasse = 'MU10' or klasse = 'WU10') and vid = 19) and vid = 19 order by ID;

-- alle zeiten der U10 um 1:20 Minuten reduzieren
UPDATE `zeit` set `zeit` = zeit-120 WHERE nummer in (SELECT stnr from teilnehmer where (klasse = 'MU10' or klasse = 'WU10') and vid = 19) and vid = 19;
