#!/bin/sh

VERANSTALTUNG=12
cd Listen

wget -O "out.htm" -q --keep-session-cookies --save-cookies=cookie.txt "http://localhost/openTiming/index.php?func=veranstaltungen.select&ID=$VERANSTALTUNG"

wget -O "Startliste_Rennen 1 U15m - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=21"
wget -O "Startliste_Rennen 1 U15m - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=22"
wget -O "Startliste_Rennen 1 U15w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=24"
wget -O "Startliste_Rennen 1 U15w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=25"
wget -O "Startliste_Rennen 2 U13m - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=27"
wget -O "Startliste_Rennen 2 U13m - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=28"
wget -O "Startliste_Rennen 2 U13w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=30"
wget -O "Startliste_Rennen 2 U13w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=31"
wget -O "Startliste_Rennen 4 U17m - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=33"
wget -O "Startliste_Rennen 4 U17m - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=34"
wget -O "Startliste_Rennen 4 Masters 2m - Obb. Meiserschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=36"
wget -O "Startliste_Rennen 4 Masters 2m (M2 + M3 + M4) - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=37"
wget -O "Startliste_Rennen 4 Masters 3m - Obb. Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=39"
wget -O "Startliste_Rennen 4 Masters 4m - Obb. Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=41"
wget -O "Startliste_Rennen 4 Masters w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=43"
wget -O "Startliste_Rennen 4 Masters w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=44"
wget -O "Startliste_Rennen 4 Masters w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=45"
wget -O "Startliste_Rennen 4 U17w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=46"
wget -O "Startliste_Rennen 4 U17w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=47"
wget -O "Startliste_Rennen 4 Elite w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=49"
wget -O "Startliste_Rennen 4 Elite w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=50"
wget -O "Startliste_Rennen 4 U19w - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=52"
wget -O "Startliste_Rennen 4 U19w - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=53"
wget -O "Startliste_Rennen 6 U19m - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=55"
wget -O "Startliste_Rennen 6 U19m - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=56"
wget -O "Startliste_Rennen 6 U19m - Bayernliga + Gäste.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=57"
wget -O "Startliste_Rennen 6 Elite m - Obb Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=58"
wget -O "Startliste_Rennen 6 Elite m (U23 + Elite + M1) - Bayernliga.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=59"
wget -O "Startliste_Rennen 6 Elite m - Bayernliga + Gäste.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=60"
wget -O "Startliste_Rennen 6 Masters 1m - Obb. Meisterschaft.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=61"
wget -O "Startliste_Rennen 5 Hobby m.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=62"
wget -O "Startliste_Rennen 5 Hobby w.pdf" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/exportPDF.php?action=startliste&sort=stnr&id=63"


rm cookie.txt
rm out.htm
cd ..
