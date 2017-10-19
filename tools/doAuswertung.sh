#!/bin/sh

VERANSTALTUNG=12
cd Listen

wget -O "out.htm" -q --keep-session-cookies --save-cookies=cookie.txt "http://localhost/openTiming/index.php?func=veranstaltungen.select&ID=$VERANSTALTUNG"

wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=21"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=22"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=24"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=25"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=27"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=28"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=30"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=31"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=33"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=34"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=36"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=37"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=39"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=41"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=43"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=44"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=45"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=46"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=47"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=49"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=50"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=52"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=53"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=55"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=56"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=57"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=58"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=59"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=60"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=61"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=62"
wget -O "out.htm" -q --keep-session-cookies --load-cookies=cookie.txt "http://localhost/openTiming/index.php?func=auswertung&ID=63"


rm cookie.txt
rm out.htm
cd ..
