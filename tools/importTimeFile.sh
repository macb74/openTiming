#!/bin/sh

if [ -z $1 ]; then
  echo "usage: importTimeFile [vID] [lID] [logfile]"
  exit
fi

cut -d ";" -f 2,5 $3 | sed -e "s/^/$1;$2;/" | sed -e 's/\....//g' > /tmp/timefile.tmp

/opt/lampp/bin/mysql -h localhost -u root -e "use myrun; load data local infile '/tmp/timefile.tmp' into table myrun.zeit fields terminated by ';' lines terminated by '\n' ignore 0 lines (vID,lID,zeit,nummer)"

rm /tmp/timefile.tmp

exit
