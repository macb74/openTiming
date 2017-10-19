#!/bin/sh

VERANSTALTUNG=12
cd Listen

wget -O "out.htm" -q --keep-session-cookies --save-cookies=cookie.txt "http://localhost/openTiming/index.php?func=veranstaltungen.select&ID=$VERANSTALTUNG"

wget -O "Ergebnis_Rennen 1 U15m - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=21"
wget -O "Ergebnis_Rennen 1 U15m - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=22"
wget -O "Ergebnis_Rennen 1 U15w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=24"
wget -O "Ergebnis_Rennen 1 U15w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=25"
wget -O "Ergebnis_Rennen 2 U13m - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=27"
wget -O "Ergebnis_Rennen 2 U13m - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=28"
wget -O "Ergebnis_Rennen 2 U13w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=30"
wget -O "Ergebnis_Rennen 2 U13w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=31"
wget -O "Ergebnis_Rennen 4 U17m - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=33"
wget -O "Ergebnis_Rennen 4 U17m - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=34"
wget -O "Ergebnis_Rennen 4 Masters 2m - Obb. Meiserschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=36"
wget -O "Ergebnis_Rennen 4 Masters 2m (M2 + M3 + M4) - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=37"
wget -O "Ergebnis_Rennen 4 Masters 3m - Obb. Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=39"
wget -O "Ergebnis_Rennen 4 Masters 4m - Obb. Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=41"
wget -O "Ergebnis_Rennen 4 Masters w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=43"
wget -O "Ergebnis_Rennen 4 Masters w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=44"
wget -O "Ergebnis_Rennen 4 Masters w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=45"
wget -O "Ergebnis_Rennen 4 U17w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=46"
wget -O "Ergebnis_Rennen 4 U17w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=47"
wget -O "Ergebnis_Rennen 4 Elite w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=49"
wget -O "Ergebnis_Rennen 4 Elite w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=50"
wget -O "Ergebnis_Rennen 4 U19w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=52"
wget -O "Ergebnis_Rennen 4 U19w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=53"
wget -O "Ergebnis_Rennen 6 U19m - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=55"
wget -O "Ergebnis_Rennen 6 U19m - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=56"
wget -O "Ergebnis_Rennen 6 U19m - Bayernliga + Gäste.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=57"
wget -O "Ergebnis_Rennen 6 Elite m - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=58"
wget -O "Ergebnis_Rennen 6 Elite m (U23 + Elite + M1) - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=59"
wget -O "Ergebnis_Rennen 6 Elite m - Bayernliga + Gäste.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=60"
wget -O "Ergebnis_Rennen 6 Masters 1m - Obb. Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=61"
wget -O "Ergebnis_Rennen 5 Hobby m.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=62"
wget -O "Ergebnis_Rennen 5 Hobby w.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=ergebnisKlasse&id=63"


rm cookie.txt
rm out.htm
cd ..
