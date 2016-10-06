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